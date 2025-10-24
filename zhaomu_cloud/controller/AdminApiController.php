<?php
namespace addons\zhaomu_cloud\controller;
use app\admin\controller\PluginAdminBaseController;
use Think\Db;
use Think\Exception;
use addons\zhaomu_cloud\services\ZhaoMuCloudService;
use addons\zhaomu_cloud\model\HlwidcCache;
use addons\zhaomu_cloud\model\Clients;
use addons\zhaomu_cloud\model\ProductGroup;
use addons\zhaomu_cloud\model\Product;
use addons\zhaomu_cloud\model\InvoiceItem;
use addons\zhaomu_cloud\model\Invoice;
use addons\zhaomu_cloud\model\Order;
use addons\zhaomu_cloud\model\Host;
class AdminApiController extends PluginAdminBaseController
{
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
    public function getProductsByRegion()
    {
        try {
        if(!$this->zhaomuCloudService){
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
            
            return json($products);
        } catch (\Exception $e) {
            return json(['code' => 0, 'msg' => $e->getMessage()], 200);
        }
    }
    public function getRegions()
    {
        try {
        if(!$this->zhaomuCloudService){
                throw new \Exception('朝暮云服务未初始化');
        }
        $regions = $this->zhaomuCloudService->getRegions();
        return json($regions);
        } catch (\Exception $e) {
            return json(['code' => 0, 'msg' => $e->getMessage()], 200);
        }
    }
    
    /**
     * 获取某个可用区的功能参数
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

    /**
     * 设置功能参数配置
     * @return \think\Response
     */
    public function setComparisonSettings()
    {
        try {
            // 获取请求参数中的比较配置
            $comparison = input('comparison', []);
            
            if (!is_array($comparison)) {
                throw new \Exception('功能参数配置必须是数组格式');
            }
            
            // 验证数据结构
            foreach ($comparison as $item) {
                if (!isset($item['name']) || !isset($item['use'])) {
                    throw new \Exception('功能参数配置格式不正确，必须包含 name 和 use 字段');
                }
            }
            
            // 保存到缓存
            $result = HlwidcCache::setValue('zhaomu_comparison', $comparison, 0);
            
            if ($result) {
                return json([
                    'code' => 1,
                    'msg' => '功能参数设置保存成功',
                    'data' => [
                        'comparison' => $comparison,
                        'count' => count($comparison),
                        'saved' => true
                    ]
                ]);
            } else {
                throw new \Exception('功能参数设置保存失败');
            }
            
        } catch (\Exception $e) {
            return json(['code' => 0, 'msg' => $e->getMessage()], 200);
        }
    }

    /**
     * 获取所有可用区列表（不受缓存影响）
     * @return \think\Response
     */
    public function getAllRegions()
    {
        try {
            if(!$this->zhaomuCloudService){
                throw new \Exception('朝暮云服务未初始化');
            }
            $regions = $this->zhaomuCloudService->getAllRegions();
            return json($regions);
        } catch (\Exception $e) {
            return json(['code' => 0, 'msg' => $e->getMessage()], 200);
        }
    }
    
    /**
     * 缓存国家数组数据
     * @return \think\Response
     */
    public function cacheCountries()
    {
        try {
            // 获取请求参数中的国家数组
            $countries = input('countries', []);
            
            if (empty($countries) || !is_array($countries)) {
                throw new \Exception('请传入有效的国家数组数据');
            }
            
            // 使用静态方法设置缓存
            $result = HlwidcCache::setValue('zhaomu_country', $countries, 0);
            
            if ($result) {
                return json(['code' => 1, 'msg' => '国家数据缓存成功', 'data' => $countries]);
            } else {
                throw new \Exception('国家数据缓存失败');
            }
        } catch (\Exception $e) {
            return json(['code' => 0, 'msg' => $e->getMessage()], 200);
        }
    }
    
    /**
     * 获取已选中的国家列表
     * @return \think\Response
     */
    public function getSelectedCountries()
    {
        try {
            // 使用静态方法获取缓存
            $selectedCountries = HlwidcCache::value('zhaomu_country', []);
            
            // 确保返回的是数组格式
            if (!is_array($selectedCountries)) {
                $selectedCountries = [];
            }
            
            return json([
                'code' => 1, 
                'msg' => '获取选中国家成功', 
                'data' => $selectedCountries,
                'count' => count($selectedCountries)
            ]);
        } catch (\Exception $e) {
            return json(['code' => 0, 'msg' => $e->getMessage()], 200);
        }
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
            
            return json([
                'code' => 1,
                'msg' => '检查缓存键成功',
                'data' => [
                    'exists' => $exists,
                    'valid' => $isValid,
                    'key' => 'zhaomu_key',
                    'encrypted' => $exists,
                    'has_data' => $exists && !empty($cacheValue)
                ]
            ]);
        } catch (\Exception $e) {
            return json(['code' => 0, 'msg' => $e->getMessage()], 200);
        }
    }
    
    /**
     * 保存 API Key
     * @return \think\Response
     */
    public function saveApiKey()
    {
        try {
            // 获取请求参数中的 API Key
            $apiKey = input('apiKey', '');
            
            if (empty($apiKey)) {
                return json(['code' => 0, 'msg' => '请传入有效的 API Key'], 200);
            }
            
            // 验证 API Key 格式（简单验证，可以根据实际需求调整）
            if (strlen($apiKey) < 5) {
                return json(['code' => 0, 'msg' => 'API Key 格式不正确，长度至少为 5 个字符'], 200);
            }
            
            // 先加密 API Key（让异常抛出到外层）
            $encryptedApiKey = ZhaoMuCloudService::staticEncrypt($apiKey);
            
            // 使用加密后的 API Key 测试是否有效（让异常抛出到外层）
            $tempService = new ZhaoMuCloudService($encryptedApiKey);
            $regions = $tempService->getRegions();
            $testResult = [
                'success' => true,
                'regions_count' => is_array($regions) ? count($regions) : 0,
                'message' => 'API Key 验证成功，地区数据获取正常'
            ];
            
            // 使用静态方法设置缓存，保存加密后的 API Key（让异常抛出到外层）
            $result = HlwidcCache::setValue('zhaomu_key', $encryptedApiKey, 0);
            
            if ($result) {
                // 重新初始化服务，使用加密的 API Key
                $this->zhaomuCloudService = new ZhaoMuCloudService($encryptedApiKey);
                
                return json([
                    'code' => 1, 
                    'msg' => 'API Key 验证通过并加密保存成功', 
                    'data' => [
                        'key' => 'zhaomu_key',
                        'encrypted' => true,
                        'saved' => true,
                        'test_result' => $testResult
                    ]
                ]);
            } else {
                return json([
                    'code' => 0, 
                    'msg' => 'API Key 保存到缓存失败',
                    'data' => [
                        'error' => '缓存写入失败',
                        'message' => '无法将加密的 API Key 保存到缓存，请重试'
                    ]
                ], 200);
            }
            
        } catch (\Exception $e) {
            return json(['code' => 0, 'msg' => '保存 API Key 异常：' . $e->getMessage()], 200);
        }
    }
    
    /**
     * 获取解密后的 API Key（供内部使用）
     * @return \think\Response
     */
    public function getDecryptedApiKey()
    {
        try {
            // 从缓存获取加密的 API Key
            $encryptedApiKey = HlwidcCache::value('zhaomu_key', null);
            
            if (empty($encryptedApiKey)) {
                throw new \Exception('API Key 不存在');
            }
            
            // 解密 API Key
            $decryptedApiKey = $this->zhaomuCloudService->decrypt($encryptedApiKey);
            
            if (empty($decryptedApiKey)) {
                throw new \Exception('API Key 解密失败');
            }
            
            return json([
                'code' => 1,
                'msg' => '获取 API Key 成功',
                'data' => [
                    'apiKey' => $decryptedApiKey,
                    'length' => strlen($decryptedApiKey)
                ]
            ]);
        } catch (\Exception $e) {
            return json(['code' => 0, 'msg' => $e->getMessage()], 200);
        }
    }
    
    /**
     * 测试加密解密功能
     * @return \think\Response
     */
    public function testEncryption()
    {
        try {
            $testResult = $this->zhaomuCloudService->testEncryption('Test API Key');
            
            return json([
                'code' => 1,
                'msg' => '加密解密测试完成',
                'data' => $testResult
            ]);
        } catch (\Exception $e) {
            return json(['code' => 0, 'msg' => $e->getMessage()], 200);
        }
    }
    
    /**
     * 获取 API Key 状态信息（不暴露实际密钥）
     * @return \think\Response
     */
    public function getApiKeyStatus()
    {
        try {
            // 检查缓存中是否有加密的 API Key
            $encryptedApiKey = HlwidcCache::value('zhaomu_key', null);
            $hasKey = !empty($encryptedApiKey);
            
            // 检查当前服务是否正常
            $serviceStatus = false;
            $apiKeyLength = 0;
            
            if ($hasKey) {
                try {
                    // 尝试解密验证
                    $decryptedKey = $this->zhaomuCloudService->decrypt($encryptedApiKey);
                    $serviceStatus = !empty($decryptedKey);
                    $apiKeyLength = strlen($decryptedKey);
                } catch (\Exception $e) {
                    $serviceStatus = false;
                }
            }
            
            return json([
                'code' => 1,
                'msg' => '获取 API Key 状态成功',
                'data' => [
                    'has_key' => $hasKey,
                    'service_status' => $serviceStatus,
                    'key_length' => $apiKeyLength,
                    'is_encrypted' => $hasKey,
                    'auto_generated' => !$hasKey // 如果没有密钥，说明是自动生成的
                ]
            ]);
        } catch (\Exception $e) {
            return json(['code' => 0, 'msg' => $e->getMessage()], 200);
        }
    }
    
    /**
     * 重新生成 API Key（仅内存使用，不保存到数据库）
     * @return \think\Response
     */
    public function regenerateApiKey()
    {
        try {
            // 生成新的随机 API Key
            $newApiKey = bin2hex(random_bytes(16)); // 32个字符的十六进制字符串
            
            // 重新初始化服务（仅内存使用）
            $this->zhaomuCloudService = new ZhaoMuCloudService($newApiKey);
            
            return json([
                'code' => 1,
                'msg' => 'API Key 重新生成成功（仅内存使用）',
                'data' => [
                    'key_length' => strlen($newApiKey),
                    'memory_only' => true,
                    'regenerated' => true
                ]
            ]);
        } catch (\Exception $e) {
            return json(['code' => 0, 'msg' => $e->getMessage()], 200);
        }
    }
    
    /**
     * 获取销售折扣
     * @return \think\Response
     */
    public function getSalesDiscount()
    {
        try {
            // 从缓存获取销售折扣
            $discount = HlwidcCache::value('zhaomu_sales_discount', null);
            
            // 如果没有设置过折扣，自动添加默认值90
            if ($discount === null) {
                $discount = 90;
                HlwidcCache::setValue('zhaomu_sales_discount', $discount, 0);
            }
            
            return json([
                'code' => 1,
                'msg' => '获取销售折扣成功',
                'data' => [
                    'discount' => (float)$discount,
                    'is_default' => $discount === 90
                ]
            ]);
        } catch (\Exception $e) {
            return json(['code' => 0, 'msg' => $e->getMessage()], 200);
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
            
            // 获取服务器密钥
            $serverKey = HlwidcCache::value('zhaomu_server_key', null);
            
            // 获取功能参数配置
            $comparison = HlwidcCache::value('zhaomu_comparison', null);
            
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
            
            // 生成服务器密钥（如果不存在）
            if ($serverKey === null) {
                $serverKey = $this->generateServerKey();
                HlwidcCache::setValue('zhaomu_server_key', $serverKey, 0);
            }
            
            // 设置功能参数默认值
            if ($comparison === null) {
                $comparison = [];
                HlwidcCache::setValue('zhaomu_comparison', $comparison, 0);
            }
            
            $productGroup = (new ProductGroup())->addOrExit();

            $MenuList = (new \app\common\logic\Menu())->getOneNavs("client", null);
          
            $p_list = array_filter($MenuList, function ($v) {
                return $v["nav_type"] == 2;
            });
            $ptype = array_values($p_list);
            //先查有没有设置过导航菜单
            if(!$menu){
                //从$ptype中查找 云服务器
                $menuSearch = array_filter($ptype, function ($v) {
                    return $v["name"] == "云服务器";
                });
                //如果没找到找虚拟主机
                if(!$menuSearch){
                    $menuSearch = array_filter($ptype, function ($v) {
                        return $v["name"] == "虚拟主机";
                    });
                }
                //如果没找到找独立服务器
                if(!$menuSearch){
                    $menuSearch = $ptype[0];
                }
              
                $menu = array_values($menuSearch);
                $menu = $menu[0]["id"];
                HlwidcCache::setValue('zhaomu_navigation_menu', $menu, 0);
            }
            return json([
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
                    'comparison' => [
                        'data' => $comparison,
                        'is_default' => $comparison === json_encode([])
                    ],
                    'productGroup' => $productGroup,
                    'ptype'=>$ptype,
                    'menu'=>$menu
                ]
            ]);
        } catch (\Exception $e) {
            return json(['code' => 0, 'msg' => $e->getMessage()], 200);
        }
    }
    
    /**
     * 设置销售折扣
     * @return \think\Response
     */
    public function setSalesDiscount()
    {
        try {
            // 获取请求参数中的折扣值
            $discount = input('discount', 0);
            
            // 验证折扣值
            if (!is_numeric($discount)) {
                throw new \Exception('折扣值必须是数字');
            }
            
            $discount = (float)$discount;
            
            // 检查折扣值是否不低于80
            if ($discount < 80) {
                throw new \Exception('销售折扣不能低于80%');
            }
            
           
            
            // 保存折扣到缓存
            $result = HlwidcCache::setValue('zhaomu_sales_discount', $discount, 0);
            
            if ($result) {
                return json([
                    'code' => 1,
                    'msg' => '销售折扣设置成功',
                    'data' => [
                        'discount' => $discount,
                        'saved' => true
                    ]
                ]);
            } else {
                throw new \Exception('销售折扣保存失败');
            }
            
        } catch (\Exception $e) {
            return json(['code' => 0, 'msg' => $e->getMessage()], 200);
        }
    }

    /**
     * 获取汇率设置
     * @return \think\Response
     */
    public function getExchangeSettings()
    {
        try {
            // 从缓存获取汇率设置
            $exchangeRate = HlwidcCache::value('zhaomu_exchange_rate', null);
            $currencyUnit = HlwidcCache::value('zhaomu_currency_unit', null);
            
            // 如果没有设置过汇率，自动添加默认值
            if ($exchangeRate === null) {
                $exchangeRate = 1;
                HlwidcCache::setValue('zhaomu_exchange_rate', $exchangeRate, 0);
            }
            
            if ($currencyUnit === null) {
                $currencyUnit = 'CNY';
                HlwidcCache::setValue('zhaomu_currency_unit', $currencyUnit, 0);
            }
            
            return json([
                'code' => 1,
                'msg' => '获取汇率设置成功',
                'data' => [
                    'exchangeRate' => (float)$exchangeRate,
                    'currencyUnit' => $currencyUnit,
                    'is_default' => $exchangeRate === 1 && $currencyUnit === 'CNY'
                ]
            ]);
        } catch (\Exception $e) {
            return json(['code' => 0, 'msg' => $e->getMessage()], 200);
        }
    }

    /**
     * 设置汇率设置
     * @return \think\Response
     */
    public function setExchangeSettings()
    {
        try {
            // 获取请求参数中的汇率设置
            $exchangeRate = input('exchangeRate', 0);
            $currencyUnit = input('currencyUnit', 'CNY');
            
            // 验证汇率值
            if (!is_numeric($exchangeRate)) {
                throw new \Exception('汇率值必须是数字');
            }
            
            $exchangeRate = (float)$exchangeRate;
            
            // 检查汇率值是否大于0
            if ($exchangeRate <= 0) {
                throw new \Exception('汇率必须大于0');
            }
            
            // 检查汇率值是否超过1000（支持不同货币的汇率范围）
            if ($exchangeRate > 1000) {
                throw new \Exception('汇率不能超过1000');
            }
            
            // 检查小数位数（最多6位小数）
            $decimalPlaces = strlen(substr(strrchr($exchangeRate, "."), 1));
            if ($decimalPlaces > 6) {
                throw new \Exception('汇率最多支持6位小数');
            }
            
            // 验证货币单位 - 支持常用货币
            $supportedCurrencies = [
                'CNY', 'USD', 'EUR', 'GBP', 'JPY', 'KRW', 'HKD', 'TWD', 'SGD', 'AUD', 'CAD', 'CHF', 'RUB', 'INR', 'THB', 'VND', 'MYR', 'IDR', 'PHP', 'BRL', 'MXN', 'ARS', 'CLP', 'COP', 'PEN', 'UYU', 'ZAR', 'EGP', 'NGN', 'KES', 'GHS', 'MAD', 'TND', 'DZD', 'TRY', 'ILS', 'AED', 'SAR', 'QAR', 'KWD', 'BHD', 'OMR', 'JOD', 'LBP', 'IQD', 'IRR', 'AFN', 'PKR', 'BDT', 'LKR', 'NPR', 'BTN', 'MMK', 'LAK', 'KHR', 'BND', 'FJD', 'NZD', 'XOF', 'XAF', 'XPF'
            ];
            
            if (!in_array($currencyUnit, $supportedCurrencies)) {
                throw new \Exception('不支持的货币单位，请选择有效的货币');
            }
            
            // 保存汇率到缓存
            $rateResult = HlwidcCache::setValue('zhaomu_exchange_rate', $exchangeRate, 0);
            $unitResult = HlwidcCache::setValue('zhaomu_currency_unit', $currencyUnit, 0);
            
            if ($rateResult && $unitResult) {
                return json([
                    'code' => 1,
                    'msg' => '汇率设置成功',
                    'data' => [
                        'exchangeRate' => $exchangeRate,
                        'currencyUnit' => $currencyUnit,
                        'saved' => true
                    ]
                ]);
            } else {
                throw new \Exception('汇率设置保存失败');
            }
            
        } catch (\Exception $e) {
            return json(['code' => 0, 'msg' => $e->getMessage()], 200);
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
            
            return json([
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
            return json(['code' => 0, 'msg' => $e->getMessage()], 200);
        }
    }

    /**
     * 搜索用户
     * @return \think\Response
     */
    public function searchUsers()
    {
        try {
            // 获取请求参数
            $keyword = input('keyword', '');
            $page = input('page', 1);
            $limit = input('limit', 10);
            
            // 验证参数
            $page = max(1, intval($page));
            $limit = min(100, max(1, intval($limit))); // 限制最大100条
            
            // 构建查询
            $query = Clients::field('id,username,email,phonenumber,status,create_time,lastlogin')
                ->order('id', 'desc');
            
            // 如果有搜索关键词
            if (!empty($keyword)) {
                $query->where(function($q) use ($keyword) {
                    $q->whereOr('username', 'like', '%' . $keyword . '%')
                      ->whereOr('email', 'like', '%' . $keyword . '%')
                      ->whereOr('phonenumber', 'like', '%' . $keyword . '%');
                });
            }
            
            // 分页查询
            $result = $query->paginate([
                'list_rows' => $limit,
                'page' => $page,
            ]);
            
            // 处理数据
            $users = [];
            foreach ($result->getCollection() as $user) {
                $users[] = [
                    'id' => $user->id,
                    'username' => $user->username,
                    'email' => $user->email,
                    'phonenumber' => $user->phonenumber,
                    'status' => $user->status,
                    'status_text' => $user->status_text,
                    'create_time' => $user->create_time,
                    'lastlogin' => $user->lastlogin,
                ];
            }
            
            return json([
                'code' => 1,
                'msg' => '搜索用户成功',
                'data' => [
                    'users' => $users,
                    'total' => $result->total(),
                    'page' => $page,
                    'limit' => $limit,
                    'pages' => $result->lastPage(),
                    'has_more' => $result->hasPages()
                ]
            ]);
            
        } catch (\Exception $e) {
            return json(['code' => 0, 'msg' => $e->getMessage()], 200);
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
            
            return json([
                'code' => 1,
                'msg' => '获取产品镜像列表成功',
                'data' => $images
            ]);
            
        } catch (\Exception $e) {
            return json(['code' => 0, 'msg' => $e->getMessage()], 200);
        }
    }

    /**
     * 提交订单
     * @return \think\Response
     */
    public function submitOrder()
    {
        try {
            // 获取所有请求参数
            $orderData = input('', []);
            
            // 检查朝暮云服务是否初始化
            if (!$this->zhaomuCloudService) {
                return json(['code' => 0, 'msg' => '朝暮云服务未初始化'], 200);
            }
            
            // 调用服务类提交订单
            $result = $this->zhaomuCloudService->submitOrder($orderData);
            
            if ($result['success']) {
                return json([
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
                return json(['code' => 0, 'msg' => '订单提交失败: ' . ($result['msg'] ?? '未知错误')], 200);
            }
            
        } catch (\Exception $e) {
            // 记录详细的异常信息
            $this->logService->error('订单提交异常', [
                'order_data' => $orderData,
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 'order_submit');
             return json(['code' => 0, 'msg' => '[ERROR]订单提交异常：' . $e->getMessage()], 200);
        }
    }
    
    /**
     * 设置导航菜单
     * @return \think\Response
     */
    public function setNavigation()
    {
        try {
            // 获取请求参数中的导航菜单ID
            $navigationId = input('navigationId', '');
            
            if (empty($navigationId)) {
                return json(['code' => 0, 'msg' => '导航菜单ID不能为空'], 200);
            }
            
            // 保存到缓存
            HlwidcCache::setValue('zhaomu_navigation_menu', $navigationId, 0);
            
            return json([
                'code' => 1,
                'msg' => '导航菜单设置成功',
                'data' => [
                    'navigationId' => $navigationId
                ]
            ]);
            
        } catch (\Exception $e) {
            return json(['code' => 0, 'msg' => $e->getMessage()], 200);
        }
    }

    /**
     * 设置实名认证
     * @return \think\Response
     */
    public function setRealNameAuth()
    {
        try {
            // 获取请求参数中的实名认证设置
            $required = input('required', true);
            
            // 转换为布尔值
            $required = filter_var($required, FILTER_VALIDATE_BOOLEAN);
            
            // 保存到缓存
            $result = HlwidcCache::setValue('zhaomu_real_name_auth', $required, 0);
            
            if ($result) {
                return json([
                    'code' => 1,
                    'msg' => '实名认证设置成功',
                    'data' => [
                        'required' => $required,
                        'saved' => true
                    ]
                ]);
            } else {
                throw new \Exception('实名认证设置保存失败');
            }
            
        } catch (\Exception $e) {
            return json(['code' => 0, 'msg' => $e->getMessage()], 200);
        }
    }

    /**
     * 获取订单列表（分页）
     * @return \think\Response
     */
    public function getOrderList()
    {
        try {
            // 获取请求参数
            $page = input('page', 1);
            $limit = input('limit', 15);
            $status = input('status', '');
            $keyword = input('keyword', '');
            $startDate = input('startDate', '');
            $endDate = input('endDate', '');
            
            // 验证参数
            $page = max(1, intval($page));
            $limit = min(100, max(1, intval($limit))); // 限制最大100条
            
            // 构建查询
            $query = Order::with(['client', 'invoice'])
                ->order('id', 'desc');
            
            // 状态筛选
            if (!empty($status)) {
                $query->where('status', $status);
            }
            
            // 关键词搜索
            if (!empty($keyword)) {
                $query->where(function($q) use ($keyword) {
                    $q->whereOr('ordernum', 'like', '%' . $keyword . '%')
                      ->whereOr('client.username', 'like', '%' . $keyword . '%')
                      ->whereOr('client.email', 'like', '%' . $keyword . '%')
                      ->whereOr('host.domain', 'like', '%' . $keyword . '%')
                      ->whereOr('product.name', 'like', '%' . $keyword . '%');
                });
            }
            $query->where('ordernum','like', 'GLC-%');
            // 日期范围筛选
            if (!empty($startDate)) {
                $query->where('create_time', '>=', strtotime($startDate));
            }
            if (!empty($endDate)) {
                $query->where('create_time', '<=', strtotime($endDate . ' 23:59:59'));
            }
            
            // 分页查询
            $result = $query->paginate([
                'list_rows' => $limit,
                'page' => $page,
            ]);
            
          
            
            return json([
                'code' => 1,
                'msg' => '获取订单列表成功',
                'data' => [
                    'orders' => $result->getCollection(),
                    'total' => $result->total(),
                    'page' => $page,
                    'limit' => $limit,
                    'pages' => $result->lastPage(),
                    'has_more' => $result->hasPages(),
                    'current_page' => $result->currentPage(),
                    'per_page' => $result->listRows()
                ]
            ]);
            
        } catch (\Exception $e) {
            return json(['code' => 0, 'msg' => $e->getMessage()], 200);
        }
    }


 
    /**
     * 继续处理订单
     * @return \think\Response
     */
    public function continueProcessing()
    {
        $orderId = input('orderId');
            
        if (empty($orderId)) {
            return json(['code' => 0, 'msg' => '订单ID不能为空'], 200);
        }
        
        $order = Order::with(['client', 'invoice'])->find($orderId);
        if (!$order) {
            return json(['code' => 0, 'msg' => '订单不存在'], 200);
        }
        try {
          
            
            if ($order->status !== 'Pending') {
            
                throw new \Exception('只有待处理状态的订单才能继续处理');
            }
            //获取host状态从item中
            $hostStatus = $order->invoice->item[0]->host;
            if ($hostStatus->domainstatus !== 'Pending') {
                $order->status = $hostStatus->domainstatus;
                $order->save();
                return json(['code' => 1, 'msg' => '主机状态不是待处理状态，订单状态已更新为' . $hostStatus], 200);
            }
            $businessCustomField = (new \addons\zhaomu_cloud\model\CustomField())->addOrExit([
                'relid' => $hostStatus->productid,
                'fieldname' => '业务id',
                'fieldtype' => 'text',
                'description' => '业务id',
            ]);
            // 调用朝暮云服务继续处理订单
            if (!$this->zhaomuCloudService) {
                throw new \Exception('朝暮云服务未初始化');
            }
            //查询字段参数
            $fieldParams = (new \addons\zhaomu_cloud\model\CustomFieldValue())->with('customField')->where('relid', $hostStatus->id)->select()->toArray();
            $newFieldParams = [];
            foreach ($fieldParams as $fieldParam) {
                $newFieldParams[$fieldParam['customField']['fieldname']] = $fieldParam['value'];
            }
           
            // 构建订单数据
            $orderData = [
                'productId' => $hostStatus->product->config_option1,
                'disk' => $newFieldParams['系统盘'], // 默认值，实际应该从订单配置中获取
                'diskData' => $newFieldParams['数据盘'],
                'bandwidth' => $newFieldParams['带宽'],
                'imageId' => $newFieldParams['操作系统'], // 默认值
                'paymentCycle' => $order->payment === 'monthly' ? 1 : ($order->payment === 'quarterly' ? 2 : 4)
            ];
            
            // 调用朝暮云API
            $result = $this->zhaomuCloudService->orderCloudServer($orderData);
            
            if ($result['success']) {
                // 更新订单状态为活跃
                $order->status = 'Active';
                $order->notes = '继续处理成功: ' . $result['message'];
                $order->save();
                $hostStatus->domainstatus = 'Active';
                $hostStatus->save();
                //添加业务ID到CustomFieldValue
                (new \addons\zhaomu_cloud\model\CustomFieldValue())->addOrExit([
                    'fieldid' => $businessCustomField->id,
                    'relid' => $hostStatus->id,
                    'value' => $result['info']['id']
                ]);
                
                // 延时一秒后获取并更新云服务器信息
                sleep(1);
                try {
                    $this->zhaomuCloudService->getAndUpdateCloudServerInfo($result['info']['id'], $hostStatus);
                } catch (\Exception $e) {
                    // 记录错误但不影响订单处理结果
                    error_log("继续处理订单时延时获取云服务器信息失败: " . $e->getMessage());
                }
                
                return json([
                    'code' => 1, 
                    'msg' => '订单继续处理成功', 
                    'data' => $result
                ], 200);
            } else {
                // 更新订单备注
                $order->notes = '继续处理失败: ' . $result['message'];
                $order->save();
                
                return json([
                    'code' => 0, 
                    'msg' => '继续处理失败: ' . $result['message']
                ], 200);
            }
            
        } catch (\Exception $e) {
            return json(['code' => 0, 'msg' => $e->getMessage()], 200);
        }
    }
    
    /**
     * 生成服务器密钥
     * @return string
     */
    private function generateServerKey()
    {
        // 生成32字节的随机密钥（AES-256需要32字节密钥）
        $key = bin2hex(random_bytes(32));
        return $key;
    }

}