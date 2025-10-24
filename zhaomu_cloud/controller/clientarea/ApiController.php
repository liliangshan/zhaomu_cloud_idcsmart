<?php

namespace addons\zhaomu_cloud\controller\clientarea;


use addons\zhaomu_cloud\services\LogService;
use addons\zhaomu_cloud\services\ZhaoMuCloudService;
use addons\zhaomu_cloud\model\HlwidcCache;
use addons\zhaomu_cloud\model\Order;
use addons\zhaomu_cloud\model\ProductGroup;
use think\facade\Db;

/**
 * 微信控制器
 * Class WechatController
 * @package addons\wechat_common_notify\controller\clientarea
 */
class ApiController extends Base
{
    private $wechatService;
    private $messageHandler;

    protected $zhaomuCloudService;
    protected $logService;
    public function initialize()
    {
        parent::initialize();

        // 检查缓存中是否有加密的 API Key
        $encryptedApiKey = HlwidcCache::value('zhaomu_key', null);


        $this->zhaomuCloudService = new ZhaoMuCloudService($encryptedApiKey);
        $this->logService = $this->zhaomuCloudService->getLogService();
    }
    /**
     * 检查缓存键是否存在
     * @return \think\Response
     */
    public function checkCacheKey()
    {
        try {
            // 检查 zhaomu_key 缓存键是否存在
            $cacheValue = HlwidcCache::value('zhaomu_key', null);

            // 如果返回 null 或空字符串，说明缓存不存在
            $exists = ($cacheValue !== null && $cacheValue !== '' && $cacheValue !== []);

            // 如果存在，尝试解密验证（可选）
            $isValid = false;
            if ($exists) {
                try {
                    // 尝试解密验证 API Key 是否有效
                    $decryptedKey = $this->zhaomuCloudService->decrypt($cacheValue);
                    $isValid = !empty($decryptedKey) && strlen($decryptedKey) >= 10;
                } catch (\Exception $e) {
                    // 解密失败，说明数据可能损坏
                    $isValid = false;
                }
            }

            return json_encode([
                'code' => 1,
                'msg' => '检查缓存键成功',
                'data' => [
                    'exists' => $exists,
                    'valid' => $isValid,
                    'key' => 'zhaomu_key',
                    'encrypted' => $exists,
                    'has_data' => $exists && !empty($cacheValue),

                ]
            ]);
        } catch (\Exception $e) {
            return json_encode(['code' => 0, 'msg' => $e->getMessage()], 200);
        }
    }


    /**
     * 获取所有配置（销售折扣 + 汇率设置）
     * @return \think\Response
     */
    public function getAllConfig()
    {
        try {
            // 获取销售折扣
            $discount = HlwidcCache::value('zhaomu_sales_discount', null);
            if ($discount === null) {
                $discount = 90;
                HlwidcCache::setValue('zhaomu_sales_discount', $discount, 0);
            }

            // 获取汇率设置
            $exchangeRate = HlwidcCache::value('zhaomu_exchange_rate', null);
            $currencyUnit = HlwidcCache::value('zhaomu_currency_unit', null);
            $menu = HlwidcCache::value('zhaomu_navigation_menu', null);

            // 获取实名认证设置
            $realNameAuth = HlwidcCache::value('zhaomu_real_name_auth', null);

            if ($exchangeRate === null) {
                $exchangeRate = 1;
                HlwidcCache::setValue('zhaomu_exchange_rate', $exchangeRate, 0);
            }

            if ($currencyUnit === null) {
                $currencyUnit = 'CNY';
                HlwidcCache::setValue('zhaomu_currency_unit', $currencyUnit, 0);
            }

            // 设置实名认证默认值
            if ($realNameAuth === null) {
                $realNameAuth = true; // 默认需要实名认证
                HlwidcCache::setValue('zhaomu_real_name_auth', $realNameAuth, 0);
            }

            $productGroup = (new ProductGroup())->addOrExit();

            $MenuList = (new \app\common\logic\Menu())->getOneNavs("client", null);

            $p_list = array_filter($MenuList, function ($v) {
                return $v["nav_type"] == 2;
            });
            $ptype = array_values($p_list);
            //先查有没有设置过导航菜单
            if (!$menu) {
                //从$ptype中查找 云服务器
                $menuSearch = array_filter($ptype, function ($v) {
                    return $v["name"] == "云服务器";
                });
                //如果没找到找虚拟主机
                if (!$menuSearch) {
                    $menuSearch = array_filter($ptype, function ($v) {
                        return $v["name"] == "虚拟主机";
                    });
                }
                //如果没找到找独立服务器
                if (!$menuSearch) {
                    $menuSearch = $ptype[0];
                }

                $menu = array_values($menuSearch);
                $menu = $menu[0]["id"];
                HlwidcCache::setValue('zhaomu_navigation_menu', $menu, 0);
            }
            return json_encode([
                'code' => 1,
                'msg' => '获取配置成功',
                'data' => [
                    'salesDiscount' => [
                        'discount' => (float)$discount,
                        'is_default' => $discount === 90
                    ],
                    'exchangeSettings' => [
                        'exchangeRate' => (float)$exchangeRate,
                        'currencyUnit' => $currencyUnit,
                        'is_default' => $exchangeRate === 1 && $currencyUnit === 'CNY'
                    ],
                    'realNameAuth' => [
                        'required' => (bool)$realNameAuth,
                        'is_default' => $realNameAuth === true
                    ],
                    'productGroup' => $productGroup,
                    'ptype' => $ptype,
                    'menu' => $menu
                ]
            ]);
        } catch (\Exception $e) {
            return json_encode(['code' => 0, 'msg' => $e->getMessage()], 200);
        }
    }
    public function getRegions()
    {
        try {
            if (!$this->zhaomuCloudService) {
                throw new \Exception('朝暮云服务未初始化');
            }
            $regions = $this->zhaomuCloudService->getRegions();
            return json_encode($regions);
        } catch (\Exception $e) {
            return json_encode(['code' => 0, 'msg' => $e->getMessage()], 200);
        }
    }
    public function getProductsByRegion()
    {
        try {
            if (!$this->zhaomuCloudService) {
                throw new \Exception('朝暮云服务未初始化');
            }

            // 获取请求参数中的区域ID
            $regionId = input('regionId', '');

            if (empty($regionId)) {
                throw new \Exception('请传入有效的区域ID');
            }

            $products = $this->zhaomuCloudService->getProductsByRegion($regionId);

            // 获取销售折扣和汇率设置
            $salesDiscount = HlwidcCache::value('zhaomu_sales_discount', 90);
            $exchangeRate = HlwidcCache::value('zhaomu_exchange_rate', 1);
            // 处理价格：应用汇率和折扣
            foreach ($products as &$product) {
                if (isset($product['price']) && $product['price'] > 0) {
                    $convertedPrice = $product['price'] * $exchangeRate;
                    $discountedPrice = $convertedPrice * ($salesDiscount / 100);
                    $product['price'] = round($discountedPrice, 2);
                }
                if (isset($product['priceQuarter']) && $product['priceQuarter'] > 0) {
                    $convertedPrice = $product['priceQuarter'] * $exchangeRate;
                    $discountedPrice = $convertedPrice * ($salesDiscount / 100);
                    $product['priceQuarter'] = round($discountedPrice, 2);
                }
                if (isset($product['priceHalfYear']) && $product['priceHalfYear'] > 0) {
                    $convertedPrice = $product['priceHalfYear'] * $exchangeRate;
                    $discountedPrice = $convertedPrice * ($salesDiscount / 100);
                    $product['priceHalfYear'] = round($discountedPrice, 2);
                }
                if (isset($product['priceYear']) && $product['priceYear'] > 0) {
                    $convertedPrice = $product['priceYear'] * $exchangeRate;
                    $discountedPrice = $convertedPrice * ($salesDiscount / 100);
                    $product['priceYear'] = round($discountedPrice, 2);
                }
            }
            return json_encode($products);
        } catch (\Exception $e) {
            return json_encode(['code' => 0, 'msg' => $e->getMessage()], 200);
        }
    }

    /**
     * 获取云服务器产品价格
     * @return \think\Response
     */
    public function getProductPrice()
    {
        try {
            if (!$this->zhaomuCloudService) {
                throw new \Exception('朝暮云服务未初始化');
            }

            // 获取请求参数
            $productId = input('productId', 0);
            $disk = input('disk', '');
            $diskData = input('diskData', '');
            $bandwidth = input('bandwidth', '');

            // 验证产品ID
            if (empty($productId) || !is_numeric($productId)) {
                throw new \Exception('请传入有效的产品ID');
            }

            // 构建参数数组
            $params = [];
            if (!empty($disk)) {
                $params['disk'] = $disk;
            }
            if (!empty($diskData)) {
                $params['diskData'] = $diskData;
            }
            if (!empty($bandwidth)) {
                $params['bandwidth'] = $bandwidth;
            }

            // 调用服务获取原始价格
            $originalPrices = $this->zhaomuCloudService->getProductPrice($productId, $params);

            // 获取销售折扣和汇率设置
            $salesDiscount = HlwidcCache::value('zhaomu_sales_discount', 90);
            $exchangeRate = HlwidcCache::value('zhaomu_exchange_rate', 1);
            $currencyUnit = HlwidcCache::value('zhaomu_currency_unit', 'CNY');

            // 处理价格：应用汇率和折扣
            $processedPrices = [];
            foreach ($originalPrices as $cycle => $price) {
                // 应用汇率转换
                $convertedPrice = $price * $exchangeRate;

                // 应用销售折扣
                $discountedPrice = $convertedPrice * ($salesDiscount / 100);

                // 保留2位小数
                $processedPrices[$cycle] = round($discountedPrice, 2);
            }

            return json_encode([
                'code' => 1,
                'msg' => '获取产品价格成功',
                'data' => $processedPrices,
                'meta' => [
                    'original_prices' => $originalPrices,
                    'sales_discount' => $salesDiscount,
                    'exchange_rate' => $exchangeRate,
                    'currency_unit' => $currencyUnit,
                    'applied_discount' => true,
                    'applied_exchange' => $exchangeRate != 1
                ]
            ]);
        } catch (\Exception $e) {
            return json_encode(['code' => 0, 'msg' => $e->getMessage()], 200);
        }
    }
    /**
     * 获取产品镜像列表
     * @return \think\Response
     */
    public function getProductImages()
    {
        try {
            if (!$this->zhaomuCloudService) {
                throw new \Exception('朝暮云服务未初始化');
            }

            // 获取请求参数中的产品ID
            $productId = input('productId', 0);

            if (empty($productId) || !is_numeric($productId)) {
                throw new \Exception('请传入有效的产品ID');
            }

            // 调用服务获取镜像列表
            $images = $this->zhaomuCloudService->getProductImages($productId);

            return json_encode([
                'code' => 1,
                'msg' => '获取产品镜像列表成功',
                'data' => $images
            ]);
        } catch (\Exception $e) {
            return json_encode(['code' => 0, 'msg' => $e->getMessage()], 200);
        }
    }

    /**
     * 获取机器信息
     * @return \think\Response
     */
    public function getMachineInfo()
    {
        try {
            if (!$this->zhaomuCloudService) {
                throw new \Exception('朝暮云服务未初始化');
            }

            // 获取请求参数中的 token
            $token = input('token', '');

            if (empty($token)) {
                throw new \Exception('请传入有效的 token 参数');
            }

            // 解密 token 获取 domain
            $domain = $this->decryptTokenToDomain($token);

            if (empty($domain)) {
                throw new \Exception('token 解密失败或无效');
            }

            // 根据 domain 查询单个 Host 记录，预加载自定义字段值和产品信息
            $host = \addons\zhaomu_cloud\model\Host::with(['customFieldValues', 'product'])
                ->where('domain', $domain)
                ->find();

            if (!$host) {
                throw new \Exception('未找到对应的机器信息');
            }

            // 提取业务ID
            $businessId = '';
            if (!empty($host->customFieldValues)) {
                foreach ($host->customFieldValues as $fieldValue) {
                    // 根据fieldid查找业务ID字段（fieldid=1308是业务ID）
                    if ($fieldValue->fieldid == 1308) {
                        $businessId = $fieldValue->value;
                        break;
                    }
                }
            }

            // 检查业务ID是否为空
            if (empty($businessId)) {
                throw new \Exception('业务ID为空，无法获取官方数据');
            }

            // 添加业务ID到host对象
            $host->business_id = $businessId;

            // 获取并更新官方云服务器信息
            try {
                $cloudServerInfo = $this->zhaomuCloudService->getAndUpdateCloudServerInfo($businessId, $host);
            } catch (\Exception $e) {
                // 如果获取官方数据失败，抛出错误
                throw new \Exception('获取官方云服务器信息失败: ' . $e->getMessage());
            }

            return json_encode([
                'code' => 1,
                'msg' => '获取机器信息成功',
                'data' =>  $host
            ]);
        } catch (\Exception $e) {
            return json_encode(['code' => 0, 'msg' => $e->getMessage()], 200);
        }
    }

    /**
     * 解密 token 获取 domain
     * @param string $token 加密的 token
     * @return string 解密后的 domain
     * @throws \Exception
     */
    private function decryptTokenToDomain($token)
    {
        try {
            // 获取服务器密钥
            $serverKey = HlwidcCache::value('zhaomu_server_key', null);

            if (empty($serverKey)) {
                throw new \Exception('服务器密钥未配置');
            }

            // 使用与加密相同的方式解密
            $decryptedData = openssl_decrypt(
                hex2bin($token),
                'aes-128-cbc',
                mb_substr($serverKey, 0, 16),
                1,
                mb_substr($serverKey, 8, 16)
            );

            if ($decryptedData === false) {
                throw new \Exception('token 解密失败');
            }

            // 直接返回解密后的数据作为 domain
            return $decryptedData;
        } catch (\Exception $e) {
            throw new \Exception('token 解密失败: ' . $e->getMessage());
        }
    }

    /**
     * 重启/开机云服务器
     * @return \think\Response
     */
    public function rebootServer()
    {
        try {
            if (!$this->zhaomuCloudService) {
                throw new \Exception('朝暮云服务未初始化');
            }

            // 获取请求参数中的 token
            $token = input('token', '');

            if (empty($token)) {
                throw new \Exception('请传入有效的 token 参数');
            }

            // 解密 token 获取 domain
            $domain = $this->decryptTokenToDomain($token);

            if (empty($domain)) {
                throw new \Exception('token 解密失败或无效');
            }

            // 根据 domain 查询 Host 记录获取业务ID
            $host = \addons\zhaomu_cloud\model\Host::with(['customFieldValues'])
                ->where('domain', $domain)
                ->find();

            if (!$host) {
                throw new \Exception('未找到对应的机器信息');
            }

            // 提取业务ID
            $businessId = '';
            if (!empty($host->customFieldValues)) {
                foreach ($host->customFieldValues as $fieldValue) {
                    if ($fieldValue->fieldid == 1308) {
                        $businessId = $fieldValue->value;
                        break;
                    }
                }
            }

            if (empty($businessId)) {
                throw new \Exception('业务ID为空，无法执行操作');
            }

            // 调用朝暮云官方API重启服务器
            $result = $this->zhaomuCloudService->rebootCloudServer($businessId);

            return json_encode([
                'code' => 1,
                'msg' => '重启命令发送成功',
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return json_encode(['code' => 0, 'msg' => $e->getMessage()], 200);
        }
    }

    /**
     * 关机云服务器
     * @return \think\Response
     */
    public function shutdownServer()
    {
        try {
            if (!$this->zhaomuCloudService) {
                throw new \Exception('朝暮云服务未初始化');
            }

            // 获取请求参数中的 token
            $token = input('token', '');

            if (empty($token)) {
                throw new \Exception('请传入有效的 token 参数');
            }

            // 解密 token 获取 domain
            $domain = $this->decryptTokenToDomain($token);

            if (empty($domain)) {
                throw new \Exception('token 解密失败或无效');
            }

            // 根据 domain 查询 Host 记录获取业务ID
            $host = \addons\zhaomu_cloud\model\Host::with(['customFieldValues'])
                ->where('domain', $domain)
                ->find();

            if (!$host) {
                throw new \Exception('未找到对应的机器信息');
            }

            // 提取业务ID
            $businessId = '';
            if (!empty($host->customFieldValues)) {
                foreach ($host->customFieldValues as $fieldValue) {
                    if ($fieldValue->fieldid == 1308) {
                        $businessId = $fieldValue->value;
                        break;
                    }
                }
            }

            if (empty($businessId)) {
                throw new \Exception('业务ID为空，无法执行操作');
            }

            // 调用朝暮云官方API关机服务器
            $result = $this->zhaomuCloudService->shutdownCloudServer($businessId);

            return json_encode([
                'code' => 1,
                'msg' => '关机命令发送成功',
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return json_encode(['code' => 0, 'msg' => $e->getMessage()], 200);
        }
    }

    /**
     * 重置云服务器密码
     * @return \think\Response
     */
    public function resetPassword()
    {
        try {
            if (!$this->zhaomuCloudService) {
                throw new \Exception('朝暮云服务未初始化');
            }

            // 获取请求参数
            $token = input('token', '');
            $password = input('password', '');

            if (empty($token)) {
                throw new \Exception('请传入有效的 token 参数');
            }

            if (empty($password)) {
                throw new \Exception('请传入新密码');
            }

            // 解密 token 获取 domain
            $domain = $this->decryptTokenToDomain($token);

            if (empty($domain)) {
                throw new \Exception('token 解密失败或无效');
            }

            // 根据 domain 查询 Host 记录获取业务ID
            $host = \addons\zhaomu_cloud\model\Host::with(['customFieldValues'])
                ->where('domain', $domain)
                ->find();

            if (!$host) {
                throw new \Exception('未找到对应的机器信息');
            }

            // 提取业务ID
            $businessId = '';
            if (!empty($host->customFieldValues)) {
                foreach ($host->customFieldValues as $fieldValue) {
                    if ($fieldValue->fieldid == 1308) {
                        $businessId = $fieldValue->value;
                        break;
                    }
                }
            }

            if (empty($businessId)) {
                throw new \Exception('业务ID为空，无法执行操作');
            }

            // 调用朝暮云官方API重置密码
            $result = $this->zhaomuCloudService->resetCloudServerPassword($businessId, $password);

            return json_encode([
                'code' => 1,
                'msg' => '重置密码命令发送成功',
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return json_encode(['code' => 0, 'msg' => $e->getMessage()], 200);
        }
    }

    /**
     * 重装云服务器
     * @return \think\Response
     */
    public function rebuildServer()
    {
        try {
            if (!$this->zhaomuCloudService) {
                throw new \Exception('朝暮云服务未初始化');
            }

            // 获取请求参数
            $token = input('token', '');
            $imageId = input('imageId', 0);

            if (empty($token)) {
                throw new \Exception('请传入有效的 token 参数');
            }

            if (empty($imageId) || !is_numeric($imageId)) {
                throw new \Exception('请传入有效的镜像ID');
            }

            // 解密 token 获取 domain
            $domain = $this->decryptTokenToDomain($token);

            if (empty($domain)) {
                throw new \Exception('token 解密失败或无效');
            }

            // 根据 domain 查询 Host 记录获取业务ID
            $host = \addons\zhaomu_cloud\model\Host::with(['customFieldValues'])
                ->where('domain', $domain)
                ->find();

            if (!$host) {
                throw new \Exception('未找到对应的机器信息');
            }

            // 提取业务ID
            $businessId = '';
            if (!empty($host->customFieldValues)) {
                foreach ($host->customFieldValues as $fieldValue) {
                    if ($fieldValue->fieldid == 1308) {
                        $businessId = $fieldValue->value;
                        break;
                    }
                }
            }

            if (empty($businessId)) {
                throw new \Exception('业务ID为空，无法执行操作');
            }

            // 调用朝暮云官方API重装服务器
            $result = $this->zhaomuCloudService->rebuildCloudServer($businessId, $imageId);

            return json_encode([
                'code' => 1,
                'msg' => '重装系统命令发送成功',
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return json_encode(['code' => 0, 'msg' => $e->getMessage()], 200);
        }
    }

    /**
     * 获取重装云服务器的镜像列表
     * @return \think\Response
     */
    public function getCloudServerImages()
    {
        try {
            if (!$this->zhaomuCloudService) {
                throw new \Exception('朝暮云服务未初始化');
            }

            // 获取请求参数中的 token
            $token = input('token', '');

            if (empty($token)) {
                throw new \Exception('请传入有效的 token 参数');
            }

            // 解密 token 获取 domain
            $domain = $this->decryptTokenToDomain($token);

            if (empty($domain)) {
                throw new \Exception('token 解密失败或无效');
            }

            // 根据 domain 查询 Host 记录获取业务ID
            $host = \addons\zhaomu_cloud\model\Host::with(['customFieldValues'])
                ->where('domain', $domain)
                ->find();

            if (!$host) {
                throw new \Exception('未找到对应的机器信息');
            }

            // 提取业务ID
            $businessId = '';
            if (!empty($host->customFieldValues)) {
                foreach ($host->customFieldValues as $fieldValue) {
                    if ($fieldValue->fieldid == 1308) {
                        $businessId = $fieldValue->value;
                        break;
                    }
                }
            }

            if (empty($businessId)) {
                throw new \Exception('业务ID为空，无法获取镜像列表');
            }

            // 调用朝暮云官方API获取镜像列表
            $images = $this->zhaomuCloudService->getCloudServerImages($businessId);

            return json_encode([
                'code' => 1,
                'msg' => '获取镜像列表成功',
                'data' => $images
            ]);
        } catch (\Exception $e) {
            return json_encode(['code' => 0, 'msg' => $e->getMessage()], 200);
        }
    }

    /**
     * 切换支付周期
     * @return \think\Response
     */
    public function switchPaymentCycle()
    {
        try {
            // 获取token参数
            $token = input('token', '');
            if (empty($token)) {
                throw new \Exception('缺少token参数');
            }

            // 获取新的支付周期
            $newCycle = input('cycle', '');
            if (empty($newCycle)) {
                throw new \Exception('缺少支付周期参数');
            }

            // 验证支付周期是否有效
            $validCycles = ['monthly', 'quarterly', 'semiannually', 'annually'];
            if (!in_array($newCycle, $validCycles)) {
                throw new \Exception('无效的支付周期');
            }

            // 解密token获取domain
            $domain = $this->decryptTokenToDomain($token);
            if (empty($domain)) {
                throw new \Exception('token解密失败');
            }

            // 获取主机信息
            $host = \addons\zhaomu_cloud\model\Host::with(['customFieldValues', 'product'])
                ->where('domain', $domain)
                ->find();

            if (!$host) {
                throw new \Exception('主机不存在');
            }

            // 提取业务ID
            $businessId = '';
            if (!empty($host->customFieldValues)) {
                foreach ($host->customFieldValues as $fieldValue) {
                    if ($fieldValue->fieldid == 1308) {
                        $businessId = $fieldValue->value;
                        break;
                    }
                }
            }

            if (empty($businessId)) {
                throw new \Exception('业务ID为空，无法获取官方数据');
            }

            // 获取官方机器信息里的价格
            $cloudServerInfo = $this->zhaomuCloudService->getCloudServerInfo($businessId);

            // 获取销售折扣和汇率设置
            $salesDiscount = HlwidcCache::value('zhaomu_sales_discount', 90);
            $exchangeRate = HlwidcCache::value('zhaomu_exchange_rate', 1);
            $currencyUnit = HlwidcCache::value('zhaomu_currency_unit', 'CNY');

            // 原始价格映射
            $originalPriceMap = [
                'monthly' => $cloudServerInfo['price'] ?? 0,
                'quarterly' => $cloudServerInfo['priceQuarter'] ?? 0,
                'semiannually' => $cloudServerInfo['priceHalfYear'] ?? 0,
                'annually' => $cloudServerInfo['priceYear'] ?? 0
            ];

            // 应用汇率和折扣处理价格
            $processedPriceMap = [];
            foreach ($originalPriceMap as $cycle => $originalPrice) {
                if ($originalPrice > 0) {
                    // 应用汇率转换
                    $convertedPrice = $originalPrice * $exchangeRate;

                    // 应用销售折扣
                    $discountedPrice = $convertedPrice * ($salesDiscount / 100);

                    // 保留2位小数
                    $processedPriceMap[$cycle] = round($discountedPrice, 2);
                } else {
                    $processedPriceMap[$cycle] = 0;
                }
            }

            $newPrice = $processedPriceMap[$newCycle] ?? 0;

            if ($newPrice <= 0) {
                throw new \Exception('无法获取新支付周期的价格信息');
            }

            // 保存原始数据用于返回
            $oldCycle = $host->payment;
            $oldPrice = $processedPriceMap[$host->payment] ?? 0;

            // 更新host数据的支付周期和金额
            $host->payment = $newCycle;
            $host->amount = $newPrice;
            $host->billingcycle = $newCycle;
            $host->save();

            return json_encode([
                'code' => 1,
                'msg' => '支付周期切换成功',
                'data' => [
                    'old_cycle' => $oldCycle,
                    'new_cycle' => $newCycle,
                    'old_price' => $oldPrice,
                    'new_price' => $newPrice,
                    'price_info' => $processedPriceMap,
                    'original_prices' => $originalPriceMap,
                    'meta' => [
                        'sales_discount' => $salesDiscount,
                        'exchange_rate' => $exchangeRate,
                        'currency_unit' => $currencyUnit,
                        'applied_discount' => true,
                        'applied_exchange' => $exchangeRate != 1
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return json_encode(['code' => 0, 'msg' => $e->getMessage()], 200);
        }
    }
    /**
     * 提交订单
     * @return \think\Response
     */
    public function submitOrder()
    {
        try {
            $userId = request()->uid;
            if (!$userId) {
                throw new \Exception('请先登录');
            }
            // 获取所有请求参数
            $orderData = input('', []);

            // 检查朝暮云服务是否初始化
            if (!$this->zhaomuCloudService) {
                throw new \Exception('朝暮云服务未初始化');
            }
            $orderData['customer']['id'] = $userId;
            // 调用服务类提交订单
            $result = $this->zhaomuCloudService->submitOrder($orderData);

            if ($result['success']) {
                return json_encode([
                    'code' => 1,
                    'msg' => '订单数据接收成功',
                    'data' => array_merge($result['data'], [
                        'raw_input' => $_REQUEST,
                        'method' => $_SERVER['REQUEST_METHOD'] ?? 'UNKNOWN',
                        'content_type' => $_SERVER['CONTENT_TYPE'] ?? 'UNKNOWN'
                    ])
                ]);
            } else {
                // 记录详细的失败信息
                $this->logService->error('订单提交失败', [
                    'order_data' => $orderData,
                    'result' => $result
                ], 'order_submit');
                return json_encode(['code' => 0, 'msg' => '订单提交失败: ' . ($result['msg'] ?? '未知错误')], 200);
            }
        } catch (\Exception $e) {
            // 记录详细的异常信息
            $this->logService->error('订单提交异常', [
                'order_data' => $orderData,
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 'order_submit');
            return json_encode(['code' => 0, 'msg' => '[ERROR]订单提交异常：' . $e->getMessage()], 200);
        }
    }

    /**
     * 获取某个可用区的功能参数比较
     * @return \think\Response
     */
    public function getRegionFeatureComparison()
    {
        try {
            // 检查缓存中是否有加密的 API Key
            $encryptedApiKey = \addons\zhaomu_cloud\model\HlwidcCache::value('zhaomu_key', null);

            if (empty($encryptedApiKey)) {
                throw new \Exception('API Key 未配置，请先配置朝暮云 API Key');
            }

            $zhaomuCloudService = new \addons\zhaomu_cloud\services\ZhaoMuCloudService($encryptedApiKey);

            // 获取请求参数中的可用区ID
            $regionId = input('regionId', '');

            if (empty($regionId)) {
                throw new \Exception('请传入有效的可用区ID');
            }

            // 调用服务获取功能参数
            $features = $zhaomuCloudService->getRegionFeatureComparison($regionId);

            // 获取缓存中的比较配置
            $comparison = \addons\zhaomu_cloud\model\HlwidcCache::value('zhaomu_comparison', []);



            // 提取features的name数组
            $featureNames = [];
            if (is_array($features)) {
                foreach ($features as $feature) {
                    if (isset($feature['name'])) {
                        $featureNames[] = $feature['name'];
                    }
                }
            }

            // 遍历featureNames，如果不在comparison中，则添加
            $comparisonUpdated = false;
            foreach ($featureNames as $name) {
                $found = false;
                foreach ($comparison as $item) {
                    if (isset($item['name']) && $item['name'] === $name) {
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
                    $comparison[] = [
                        'name' => $name,
                        'use' => true
                    ];
                    $comparisonUpdated = true;
                }
            }

            // 如果comparison有更新，保存到缓存
            if ($comparisonUpdated) {
                \addons\zhaomu_cloud\model\HlwidcCache::setValue('zhaomu_comparison', $comparison, 0);
            }

            // 过滤出use为true的features
            $filteredFeatures = [];
            if (is_array($features)) {
                foreach ($features as $feature) {
                    if (isset($feature['name'])) {
                        // 查找comparison中对应的配置
                        $useFeature = true; // 默认使用
                        foreach ($comparison as $item) {
                            if (isset($item['name']) && $item['name'] === $feature['name']) {
                                $useFeature = isset($item['use']) ? $item['use'] : true;
                                break;
                            }
                        }

                        if ($useFeature) {
                            $filteredFeatures[] = $feature;
                        }
                    }
                }
            }

            return json_encode([
                'code' => 1,
                'msg' => '获取功能参数成功',
                'data' => $filteredFeatures
            ]);
        } catch (\Exception $e) {
            return json_encode(['code' => 0, 'msg' => $e->getMessage()], 200);
        }
    }
}
