<?php
/**
 * 朝暮数据API模块
 * 用于对接朝暮数据VPS服务的API接口
 * 
 * @author GZHX Technology
 * @version 1.0.15
 * @description 解决文件冲突
 */

use think\Db;
use addons\zhaomu_cloud\model\Host;
use addons\zhaomu_cloud\model\HlwidcCache;
use addons\zhaomu_cloud\ZhaoMuTemplete;
use addons\zhaomu_cloud\services\ZhaoMuCloudService;

/**
 * 获取模块元数据信息
 * @return array 模块基本信息
 */
function zhaomucloudapi_MetaData(){
    return [
        'DisplayName'=>'朝暮数据API',
        'author'      => 'GZHX Technology',
        'APIVersion'=>'1.0.15',
        'update_description'=>'解决文件冲突',
        'HelpDoc'=>'https://html5code.org/'
    ];
}




/**
 * 模块配置选项
 * 定义朝暮数据API模块的配置参数
 * @return array 配置选项数组
 */
function zhaomucloudapi_ConfigOptions(){
    return [
        [
            "type" => "text",
            "name" => "VPS产品ID",
            "description" => "VPS产品ID，具体请咨询对接站",
            "key" => "productid"
        ]
    ];
}

/**
 * API认证
 * @param array $params 参数数组
 * @return string 返回"OK"表示认证通过
 */
function zhaomucloudapi_ApiAuth($params){
    return "OK";
}

/**
 * 测试连接
 * 测试与朝暮数据API的连接状态
 * @param array $params 参数数组
 * @return string 返回'success'表示连接成功
 */
function zhaomucloudapi_TestLink(array $params)
{
    try {
        $success = true;
        $errorMsg = '';
    } catch (Exception $e) {
        $success = false;
        $errorMsg = $e->getMessage();
    }
    return 'success';
}




/**
 * 创建VPS账户
 * 在朝暮数据平台创建新的VPS实例
 * @param array $params 包含主机信息的参数数组
 * @return string 返回'success'表示创建成功，否则返回错误信息
 */
function zhaomucloudapi_CreateAccount($params){
    try {
        // 从params中提取所需参数
        $productId = $params['config_option1'] ?? $params['configoptions']['model'] ?? null;
        if (!$productId) {
            return '无法获取产品ID';
        }

        // 获取系统盘大小
        $systemDisk = $params['customfields']['系统盘'] ?? '25';
        
        // 获取数据盘大小
        $dataDisk = $params['customfields']['数据盘'] ?? '';
        
        // 获取带宽
        $bandwidth = $params['customfields']['带宽'] ?? '';
        
        // 获取操作系统镜像ID
        $imageId = $params['customfields']['操作系统'] ?? null;
        if (!$imageId) {
            return '无法获取操作系统镜像ID';
        }

        // 转换billingcycle为对应的数字
        $billingCycle = $params['billingcycle'] ?? 'monthly';
        $paymentCycle = 1; // 默认月付
        
        switch ($billingCycle) {
            case 'monthly':
                $paymentCycle = 1; // 月付
                break;
            case 'quarterly':
                $paymentCycle = 2; // 季付
                break;
            case 'semiannually':
                $paymentCycle = 3; // 半年付
                break;
            case 'annually':
                $paymentCycle = 4; // 年付
                break;
            default:
                $paymentCycle = 1; // 默认月付
                break;
        }

        // 构建订购数据
        $orderData = [
            'productId' => $productId,
            'disk' => $systemDisk,
            'diskData' => $dataDisk,
            'bandwidth' => $bandwidth,
            'imageId' => $imageId,
            'paymentCycle' => $paymentCycle
        ];

        // 使用ZhaoMuCloudService进行订购
        $zhaomuService = new ZhaoMuCloudService();
        $result = $zhaomuService->orderCloudServer($orderData);

        // 检查订购结果
        if (!isset($result['success']) || !$result['success']) {
            $errorMessage = isset($result['message']) ? $result['message'] : '订购失败';
            return $errorMessage;
        }

        // 获取业务ID并保存到CustomFieldValue
        if (isset($result['info']['id'])) {
            $businessId = $result['info']['id'];
            
            // 查找业务ID自定义字段
            $businessCustomField = \addons\zhaomu_cloud\model\CustomField::where([
                'relid' => $params['productid'],
                'fieldname' => '业务id'
            ])->find();
            
            if ($businessCustomField) {
                // 添加业务ID到CustomFieldValue
                (new \addons\zhaomu_cloud\model\CustomFieldValue())->addOrExit([
                    'fieldid' => $businessCustomField->id,
                    'relid' => $params['hostid'],
                    'value' => $businessId
                ]);
            }

            // 等待2秒后更新机器信息
            sleep(2);
            
            try {
                // 获取host对象
                $host = \addons\zhaomu_cloud\model\Host::find($params['hostid']);
                if ($host) {
                    // 更新云服务器信息
                    $zhaomuService->getAndUpdateCloudServerInfo($businessId, $host);
                }
            } catch (\Exception $e) {
                // 记录错误但不影响创建结果
                error_log("开通后更新机器信息失败: " . $e->getMessage());
            }
        }

        return 'success';
    } catch (\Exception $e) {
        return '创建失败: ' . $e->getMessage();
    }
}

/**
 * 续费VPS
 * 为VPS实例续费指定年限
 * @param array $params 包含续费信息的参数数组
 * @return string 返回'success'表示续费成功，否则返回错误信息
 */
function zhaomucloudapi_Renew($params){
    try {
        // 获取业务ID
        $businessId = $params['customfields']['业务id'] ?? null;
        if (!$businessId) {
            return '无法获取业务ID';
        }

        // 转换billingcycle为对应的数字
        $billingCycle = $params['billingcycle'] ?? 'monthly';
        $paymentCycle = 1; // 默认月付
        
        switch ($billingCycle) {
            case 'monthly':
                $paymentCycle = 1; // 月付
                break;
            case 'quarterly':
                $paymentCycle = 2; // 季付
                break;
            case 'semiannually':
                $paymentCycle = 3; // 半年付
                break;
            case 'annually':
                $paymentCycle = 4; // 年付
                break;
            default:
                $paymentCycle = 1; // 默认月付
                break;
        }

        // 使用ZhaoMuCloudService进行续费操作
        $zhaomuService = new ZhaoMuCloudService();
        $result = $zhaomuService->renewCloudServer($businessId, $paymentCycle);
        
        return 'success';
    } catch (\Exception $e) {
        return '续费失败: ' . $e->getMessage();
    }
}

/**
 * 同步VPS信息
 * 从朝暮数据API同步VPS的最新状态信息
 * @param array $params 包含VPS信息的参数数组
 * @return string 返回'success'表示同步成功，否则返回错误信息
 */
function zhaomucloudapi_Sync($params){
    try {
        // 获取业务ID
        $businessId = $params['customfields']['业务id'] ?? null;
        if (!$businessId) {
            return '无法获取业务ID';
        }

        // 通过Host模型获取host对象
        $host = \addons\zhaomu_cloud\model\Host::find($params['hostid']);
        if (!$host) {
            return '找不到对应的主机记录';
        }

        // 使用ZhaoMuCloudService进行同步操作
        $zhaomuService = new ZhaoMuCloudService();
        $result = $zhaomuService->getAndUpdateCloudServerInfo($businessId, $host);
        
        return 'success';
    } catch (\Exception $e) {
        return '同步失败: ' . $e->getMessage();
    }
}
//结束

/**
 * 强制关机VPS
 * 强制关闭VPS实例
 * @param array $params 包含VPS信息的参数数组
 * @return array 返回操作结果状态和消息
 */
function zhaomucloudapi_HardOff($params){
    try {
        // 获取业务ID
        $businessId = $params['customfields']['业务id'] ?? null;
        if (!$businessId) {
            return '无法获取业务ID';
        }

        // 使用ZhaoMuCloudService进行关机操作
        $zhaomuService = new ZhaoMuCloudService();
        $result = $zhaomuService->shutdownCloudServer($businessId);
        
        return ['status'=>'success', 'msg'=>'关机指令已下达:)'];
    } catch (\Exception $e) {
        return '强制关机失败: ' . $e->getMessage();
    }
}
/**
 * 正常关机VPS
 * 正常关闭VPS实例
 * @param array $params 包含VPS信息的参数数组
 * @return array 返回操作结果状态和消息
 */
function zhaomucloudapi_Off($params){
    try {
        // 获取业务ID
        $businessId = $params['customfields']['业务id'] ?? null;
        if (!$businessId) {
            return '无法获取业务ID';
        }

        // 使用ZhaoMuCloudService进行关机操作
        $zhaomuService = new ZhaoMuCloudService();
        $result = $zhaomuService->shutdownCloudServer($businessId);
        
        return ['status'=>'success', 'msg'=>'关机指令已下达:)'];
    } catch (\Exception $e) {
        return '正常关机失败: ' . $e->getMessage();
    }
}

/**
 * 开机VPS
 * 启动VPS实例
 * @param array $params 包含VPS信息的参数数组
 * @return array 返回操作结果状态和消息
 */
function zhaomucloudapi_On($params){
    try {
        // 获取业务ID
        $businessId = $params['customfields']['业务id'] ?? null;
        if (!$businessId) {
            return '无法获取业务ID';
        }

        // 使用ZhaoMuCloudService进行开机操作
        $zhaomuService = new ZhaoMuCloudService();
        $result = $zhaomuService->rebootCloudServer($businessId);
        
        return ['status'=>'success', 'msg'=>'开机指令已下达:)'];
    } catch (\Exception $e) {
        return '开机失败: ' . $e->getMessage();
    }
}
//结束

//结束

/**
 * 强制重启VPS
 * 强制重启VPS实例
 * @param array $params 包含VPS信息的参数数组
 * @return array 返回操作结果状态和消息
 */
function zhaomucloudapi_HardReboot($params){
    try {
        // 获取业务ID
        $businessId = $params['customfields']['业务id'] ?? null;
        if (!$businessId) {
            return '无法获取业务ID';
        }

        // 使用ZhaoMuCloudService进行重启操作
        $zhaomuService = new ZhaoMuCloudService();
        $result = $zhaomuService->rebootCloudServer($businessId);
        
        return ['status'=>'success', 'msg'=>'重启指令已下达:)'];
    } catch (\Exception $e) {
        return '强制重启失败: ' . $e->getMessage();
    }
}

/**
 * 正常重启VPS
 * 正常重启VPS实例
 * @param array $params 包含VPS信息的参数数组
 * @return array 返回操作结果状态和消息
 */
function zhaomucloudapi_Reboot($params){
    try {
        // 获取业务ID
        $businessId = $params['customfields']['业务id'] ?? null;
        if (!$businessId) {
            return '无法获取业务ID';
        }

        // 使用ZhaoMuCloudService进行重启操作
        $zhaomuService = new ZhaoMuCloudService();
        $result = $zhaomuService->rebootCloudServer($businessId);
        
        return ['status'=>'success', 'msg'=>'重启指令已下达:)'];
    } catch (\Exception $e) {
        return '正常重启失败: ' . $e->getMessage();
    }
}
//结束

//结束

/**
 * 暂停账户
 * 暂停VPS账户（关机）
 * @param array $params 参数数组
 * @return string 返回'success'表示操作成功
 */
function zhaomucloudapi_SuspendAccount($params){
    try {
        // 获取业务ID
        $businessId = $params['customfields']['业务id'] ?? null;
        if (!$businessId) {
            return '无法获取业务ID';
        }

        // 使用ZhaoMuCloudService进行关机操作（暂停就是关机）
        $zhaomuService = new ZhaoMuCloudService();
        $result = $zhaomuService->shutdownCloudServer($businessId);
        
        return 'success';
    } catch (\Exception $e) {
        return '暂停失败: ' . $e->getMessage();
    }
}

/**
 * 解除暂停账户
 * 解除VPS账户的暂停状态
 * @param array $params 参数数组
 * @return string 返回'success'表示操作成功
 */
function zhaomucloudapi_UnsuspendAccount($params){
    try {
        // 获取业务ID
        $businessId = $params['customfields']['业务id'] ?? null;
        if (!$businessId) {
            return '无法获取业务ID';
        }

        // 使用ZhaoMuCloudService进行开机操作（解除暂停就是开机）
        $zhaomuService = new ZhaoMuCloudService();
        $result = $zhaomuService->rebootCloudServer($businessId);
        
        return 'success';
    } catch (\Exception $e) {
        return '解除暂停失败: ' . $e->getMessage();
    }
}

/**
 * 删除账户
 * 删除VPS账户
 * @param array $params 参数数组
 * @return string 返回'success'表示操作成功
 */
function zhaomucloudapi_TerminateAccount($params){
    return 'success';
}






/**
 * 前台区域配置
 * 配置前台显示的区域信息
 * @param array $params 参数数组
 * @return array 前台区域配置
 */
function zhaomucloudapi_ClientArea($params){
    return [
        'information'=>[
            'name'=>'产品详情',
        ],
        
    ];
}

/**
 * 生成随机字符串
 * 生成指定长度的随机字符串
 * @param int $length 字符串长度
 * @return string 随机字符串
 */
function zhaomucloudapi_createNonceStr($length = 6) {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $str = "";
    for ($i = 0; $i < $length; $i++) {
        $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
    }
    return $str;
}

/**
 * 获取VPS信息
 * 检查VPS是否已存在
 * @param array $params 参数数组
 * @return bool 返回true表示VPS已存在
 */
function zhaomucloudapi_GetInfo($params) {
    // 这里应该实现检查VPS是否已存在的逻辑
    // 暂时返回false，表示VPS不存在
    return false;
}

/**
 * 前台区域输出
 * 处理前台区域的输出内容
 * @param array $params 参数数组
 * @param string $key 区域键名
 * @return array|null 返回模板配置或null
 */
function zhaomucloudapi_ClientAreaOutput($params,$key){
    // 获取服务器密钥并加密域名
    $token=HlwidcCache::value('zhaomu_server_key', null);
    $data=bin2hex(openssl_encrypt( $params['domain'],'aes-128-cbc',  mb_substr($token,0,16),1, mb_substr($token,8,16)));

    if($key == 'information'){
        return [
            'template'=>'templates/information.html',
            'vars'=>[
                'params'=>$data,
                'domain'=>$_SERVER['HTTP_HOST'] ?? 'localhost',
                'zhaomu_cloud_template'=>(new ZhaoMuTemplete())->fetch()
            ]
        ];
    }
    
 
}