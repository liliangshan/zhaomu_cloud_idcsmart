<?php

namespace addons\zhaomu_cloud\services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\GuzzleException;
use addons\zhaomu_cloud\services\LogService;
use addons\zhaomu_cloud\model\HlwidcCache;
use think\Db;

/**
 * 朝暮云服务类
 * Class ZhaoMuCloudService
 * @package addons\zhaomu_cloud\services
 */
class ZhaoMuCloudService
{
    /**
     * API基础URL (.NET API)
     */
    const BASE_URL = 'https://api.zhaomu.net';

    /**
     * API版本
     */
    const API_VERSION = 'v1';

    /**
     * 请求超时时间（秒）
     */
    const TIMEOUT = 30;

    /**
     * Guzzle HTTP客户端
     * @var Client
     */
    private $client;

    /**
     * API密钥
     * @var string
     */
    private $apiKey;

    /**
     * 日志服务
     * @var LogService
     */
    private $logService;

    /**
     * 构造函数
     * @param string $apiKey API密钥（可能是加密的）
     * @param array $config 配置参数
     */
    public function __construct($apiKey = '', $config = [])
    {
        // 如果传入的是加密的 API Key，尝试解密
        if (!empty($apiKey)) {
            try {
                // 尝试解密 API Key
                $decryptedKey = $this->decrypt($apiKey);
                if (!empty($decryptedKey)) {
                    $this->apiKey = $decryptedKey;
                } else {
                    // 解密失败，使用原始值
                    $this->apiKey = $apiKey;
                }
            } catch (\Exception $e) {
                // 解密失败，使用原始值
                $this->apiKey = $apiKey;
            }
        } else {
            $this->apiKey = $apiKey;
        }

        // 加载默认配置
        $defaultConfig = $this->loadDefaultConfig();
        $config = array_merge($defaultConfig, $config);

        $this->logService = new LogService($config);

        // 初始化Guzzle客户端 (.NET API兼容)
        $this->client = new Client([
            'base_uri' => self::BASE_URL,
            'timeout' => $config['api']['timeout'] ?? self::TIMEOUT,
            'verify' => $config['api']['verify_ssl'] ?? true,
            'headers' => [
                'User-Agent' => $config['dotnet']['user_agent'] ?? 'ZhaoMuCloud-PHP-SDK/1.0',
                'Accept' => $config['dotnet']['accept'] ?? 'application/json',
                'Content-Type' => $config['dotnet']['content_type'] ?? 'application/json',
                'X-Requested-With' => $config['dotnet']['x_requested_with'] ?? 'XMLHttpRequest',
            ]
        ]);
    }

    /**
     * 加载默认配置
     * @return array
     */
    private function loadDefaultConfig()
    {
        $configFile = __DIR__ . '/../config/zhaomu_cloud.php';
        if (file_exists($configFile)) {
            return include $configFile;
        }

        return [
            'api' => [
                'base_url' => self::BASE_URL,
                'timeout' => self::TIMEOUT,
                'verify_ssl' => true,
            ],
            'dotnet' => [
                'user_agent' => 'ZhaoMuCloud-PHP-SDK/1.0',
                'accept' => 'application/json',
                'content_type' => 'application/json',
                'x_requested_with' => 'XMLHttpRequest',
            ],
            'log' => [
                'enabled' => true,
                'level' => 'info',
            ]
        ];
    }

    /**
     * 设置API密钥
     * @param string $apiKey
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * 获取API密钥
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * 发送HTTP请求
     * @param string $method HTTP方法
     * @param string $uri 请求URI
     * @param array $options 请求选项
     * @return array
     * @throws \Exception
     */
    private function sendRequest($method, $uri, $options = [])
    {
        $startTime = microtime(true);

        try {
            // 添加认证头
            if (!empty($this->apiKey)) {
                $options['headers']['Authorization'] = 'Bearer ' . $this->apiKey;
            }

            // 记录请求日志
            $this->logService->info("发送API请求", [
                'method' => $method,
                'uri' => $uri,
                'options' => $options
            ], 'api');

            // 发送请求
            $response = $this->client->request($method, $uri, $options);

            // 计算请求耗时
            $duration = microtime(true) - $startTime;

            // 获取响应内容
            $body = $response->getBody()->getContents();
            $statusCode = $response->getStatusCode();

            // 解析JSON响应
            $data = json_decode($body, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('响应数据格式错误: ' . json_last_error_msg());
            }

            // 记录成功日志
            $this->logService->api(
                $method . ' ' . $uri,
                $options,
                $data,
                $statusCode,
                $duration
            );

            return [
                'success' => true,
                'data' => $data,
                'status_code' => $statusCode,
                'duration' => $duration
            ];
        } catch (RequestException $e) {
            $duration = microtime(true) - $startTime;

            // 处理HTTP错误
            $statusCode = $e->hasResponse() ? $e->getResponse()->getStatusCode() : 0;
            $errorBody = $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : $e->getMessage();

            // 尝试解析错误响应中的JSON数据
            $errorMessage = $e->getMessage();
            if ($e->hasResponse()) {
                try {
                    $errorData = json_decode($errorBody, true);
                    if (json_last_error() === JSON_ERROR_NONE && isset($errorData['message'])) {
                        $errorMessage = $errorData['message'];
                    }
                } catch (\Exception $jsonException) {
                    // JSON解析失败，使用原始错误信息
                }
            }

            // 记录错误日志
            $this->logService->error("API请求失败", [
                'method' => $method,
                'uri' => $uri,
                'error' => $errorMessage,
                'status_code' => $statusCode,
                'response' => $errorBody,
                'duration' => $duration
            ], 'api');

            return [
                'success' => false,
                'error' => $errorMessage,
                'status_code' => $statusCode,
                'response' => $errorBody,
                'duration' => $duration
            ];
        } catch (GuzzleException $e) {
            $duration = microtime(true) - $startTime;

            // 记录错误日志
            $this->logService->error("API请求异常", [
                'method' => $method,
                'uri' => $uri,
                'error' => $e->getMessage(),
                'duration' => $duration
            ], 'api');

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'status_code' => 0,
                'duration' => $duration
            ];
        }
    }

    /**
     * 获取可用区列表
     * @param bool $grouped 是否按层级分组
     * @return array
     * @throws \Exception
     */
    public function getRegions($grouped = true)
    {
        $result = $this->sendRequest('GET', '/region');

        if (!$result['success']) {
            // 检查是否是认证错误
            if (isset($result['status_code']) && $result['status_code'] == 401) {
                throw new \Exception('API Key 无效或已过期，请检查您的 API Key 是否正确');
            }
            throw new \Exception('获取可用区列表失败: ' . $result['error']);
        }

        $data = $result['data'];

        if ($grouped) {
            return $this->groupRegionsByHierarchy($data);
        }

        return $data;
    }

    /**
     * 获取所有可用区列表（不受缓存影响）
     * @param bool $grouped 是否按层级分组
     * @return array
     * @throws \Exception
     */
    public function getAllRegions($grouped = true)
    {
        $result = $this->sendRequest('GET', '/region');

        if (!$result['success']) {
            // 检查是否是认证错误
            if (isset($result['status_code']) && $result['status_code'] == 401) {
                throw new \Exception('API Key 无效或已过期，请检查您的 API Key 是否正确');
            }
            throw new \Exception('获取可用区列表失败: ' . $result['error']);
        }

        $data = $result['data'];

        if ($grouped) {
            return $this->groupAllRegionsByHierarchy($data);
        }

        return $data;
    }

    /**
     * 按层级分组可用区数据
     * @param array $regions 可用区数据
     * @return array
     */
    private function groupRegionsByHierarchy($regions)
    {
        // 先从缓存中获取国家列表
        $cachedCountries = [];
        //HlwidcCache::setValue('zhaomu_country', [], 0);
        $cachedCountries = HlwidcCache::value('zhaomu_country', []);

        // 如果缓存中有国家列表，则过滤掉这些国家
        if (!empty($cachedCountries) && is_array($cachedCountries) && count($cachedCountries) > 0) {
            $regions = array_filter($regions, function ($region) use ($cachedCountries) {
                return in_array($region['country'], $cachedCountries);
            });
        }

        $grouped = [];

        foreach ($regions as $region) {
            $continent = $region['continent'] ?? '未知大洲';
            $country = $region['country'] ?? '未知国家';
            $province = $region['province'] ?: '默认';
            $zone = $region['zone'] ?? '未知可用区';

            // 初始化大洲
            if (!isset($grouped[$continent])) {
                $grouped[$continent] = [
                    'name' => $continent,
                    'countries' => []
                ];
            }

            // 初始化国家
            if (!isset($grouped[$continent]['countries'][$country])) {
                $grouped[$continent]['countries'][$country] = [
                    'name' => $country,
                    'provinces' => []
                ];
            }

            // 初始化省份
            if (!isset($grouped[$continent]['countries'][$country]['provinces'][$province])) {
                $grouped[$continent]['countries'][$country]['provinces'][$province] = [
                    'name' => $province,
                    'zones' => []
                ];
            }

            // 添加可用区
            $grouped[$continent]['countries'][$country]['provinces'][$province]['zones'][] = [
                'name' => $zone,
                'id' => $region['id'],
                'city' => $region['city'] ?? '',
                'area' => $region['area'] ?? '',

            ];
        }

        // 转换为索引数组格式
        $result = [];
        foreach ($grouped as $continent) {
            $continentData = [
                'name' => $continent['name'],
                'countries' => []
            ];

            foreach ($continent['countries'] as $country) {
                $countryData = [
                    'name' => $country['name'],
                    'provinces' => []
                ];

                foreach ($country['provinces'] as $province) {
                    $provinceData = [
                        'name' => $province['name'],
                        'zones' => $province['zones']
                    ];

                    $countryData['provinces'][] = $provinceData;
                }

                $continentData['countries'][] = $countryData;
            }

            $result[] = $continentData;
        }

        return $result;
    }

    /**
     * 按层级分组所有可用区数据（不受缓存影响）
     * @param array $regions 可用区数据
     * @return array
     */
    private function groupAllRegionsByHierarchy($regions)
    {
        $grouped = [];

        foreach ($regions as $region) {
            $continent = $region['continent'] ?? '未知大洲';
            $country = $region['country'] ?? '未知国家';
            $province = $region['province'] ?: '默认';
            $zone = $region['zone'] ?? '未知可用区';

            // 初始化大洲
            if (!isset($grouped[$continent])) {
                $grouped[$continent] = [
                    'name' => $continent,
                    'countries' => []
                ];
            }

            // 初始化国家
            if (!isset($grouped[$continent]['countries'][$country])) {
                $grouped[$continent]['countries'][$country] = [
                    'name' => $country,
                    'provinces' => []
                ];
            }

            // 初始化省份
            if (!isset($grouped[$continent]['countries'][$country]['provinces'][$province])) {
                $grouped[$continent]['countries'][$country]['provinces'][$province] = [
                    'name' => $province,
                    'zones' => []
                ];
            }

            // 添加可用区
            $grouped[$continent]['countries'][$country]['provinces'][$province]['zones'][] = [
                'name' => $zone,
                'id' => $region['id'],
                'city' => $region['city'] ?? '',
                'area' => $region['area'] ?? '',

            ];
        }

        // 转换为索引数组格式
        $result = [];
        foreach ($grouped as $continent) {
            $continentData = [
                'name' => $continent['name'],
                'countries' => []
            ];

            foreach ($continent['countries'] as $country) {
                $countryData = [
                    'name' => $country['name'],
                    'provinces' => []
                ];

                foreach ($country['provinces'] as $province) {
                    $provinceData = [
                        'name' => $province['name'],
                        'zones' => $province['zones']
                    ];

                    $countryData['provinces'][] = $provinceData;
                }

                $continentData['countries'][] = $countryData;
            }

            $result[] = $continentData;
        }

        return $result;
    }

    /**
     * 获取云服务器列表
     * @param array $params 查询参数
     * @return array
     * @throws \Exception
     */
    public function getServers($params = [])
    {
        $query = http_build_query($params);
        $uri = '/servers' . ($query ? '?' . $query : '');

        $result = $this->sendRequest('GET', $uri);

        if (!$result['success']) {
            throw new \Exception('获取云服务器列表失败: ' . $result['error']);
        }

        return $result['data'];
    }

    /**
     * 获取云服务器产品信息
     * @param array $params 查询参数
     * @return array
     * @throws \Exception
     */
    public function getProducts($params = [])
    {
        $query = http_build_query($params);
        $uri = '/products' . ($query ? '?' . $query : '');

        $result = $this->sendRequest('GET', $uri);

        if (!$result['success']) {
            throw new \Exception('获取云服务器产品信息失败: ' . $result['error']);
        }

        return $result['data'];
    }

    /**
     * 获取某个可用区下的云服务器产品列表
     * @param string $regionId 可用区ID
     * @return array
     * @throws \Exception
     */
    public function getProductsByRegion($regionId)
    {
        $result = $this->sendRequest('GET', '/product/region/' . $regionId);

        if (!$result['success']) {
            throw new \Exception('获取可用区产品列表失败: ' . $result['error']);
        }

        return $result['data'];
    }

    /**
     * 订购云服务器
     * @param array $data 订购数据
     * @return array
     * @throws \Exception
     */
    public function createServer($data)
    {
        $result = $this->sendRequest('POST', '/servers', [
            'json' => $data
        ]);

        if (!$result['success']) {
            throw new \Exception($result['error']);
        }

        return $result['data'];
    }

    /**
     * 订购云服务器（朝暮数据API）
     * @param array $orderData 订购数据
     * @return array
     * @throws \Exception
     */
    public function orderCloudServer($orderData)
    {
        // 转换支付周期格式
        $paymentCycle = $orderData['paymentCycle'];
        if (is_string($paymentCycle)) {
            $cycleMap = [
                'monthly' => 1,
                'quarterly' => 2,
                'annually' => 4,
                '1' => 1,
                '2' => 2,
                '4' => 4
            ];
            $paymentCycle = $cycleMap[$paymentCycle] ?? 1;
        }
        
        // 构建请求参数
        $params = [
            'productId' => $orderData['productId'],
            'disk' => $orderData['disk'],
            'diskData' => $orderData['diskData'],
            'bandwidth' => $orderData['bandwidth'],
            'imageId' => $orderData['imageId'],
            'paymentCycle' => $paymentCycle
        ];

        $result = $this->sendRequest('POST', '/cloud/order', [
            'json' => $params
        ]);

        if (!$result['success']) {
            throw new \Exception($result['error']);
        }

        // 检查API响应中的success字段
        if (!isset($result['data']['success']) || !$result['data']['success']) {
            $errorMessage = isset($result['data']['message']) ? $result['data']['message'] : '订购失败';
            throw new \Exception($errorMessage);
        }

        // 返回完整的API响应数据，包含success、message、info等字段
        return $result['data'];
    }

    /**
     * 获取云服务器详情
     * @param string $serverId 服务器ID
     * @return array
     * @throws \Exception
     */
    public function getServer($serverId)
    {
        $result = $this->sendRequest('GET', '/servers/' . $serverId);

        if (!$result['success']) {
            throw new \Exception('获取云服务器详情失败: ' . $result['error']);
        }

        return $result['data'];
    }

    /**
     * 重启云服务器
     * @param string $serverId 服务器ID
     * @return array
     * @throws \Exception
     */
    public function rebootServer($serverId)
    {
        $result = $this->sendRequest('POST', '/servers/' . $serverId . '/reboot');

        if (!$result['success']) {
            throw new \Exception('重启云服务器失败: ' . $result['error']);
        }

        return $result['data'];
    }

    /**
     * 开机云服务器
     * @param string $serverId 服务器ID
     * @return array
     * @throws \Exception
     */
    public function startServer($serverId)
    {
        $result = $this->sendRequest('POST', '/servers/' . $serverId . '/start');

        if (!$result['success']) {
            throw new \Exception('开机云服务器失败: ' . $result['error']);
        }

        return $result['data'];
    }

    /**
     * 关机云服务器
     * @param string $serverId 服务器ID
     * @return array
     * @throws \Exception
     */
    public function stopServer($serverId)
    {
        $result = $this->sendRequest('POST', '/servers/' . $serverId . '/stop');

        if (!$result['success']) {
            throw new \Exception('关机云服务器失败: ' . $result['error']);
        }

        return $result['data'];
    }

    /**
     * 重装云服务器
     * @param string $serverId 服务器ID
     * @param array $data 重装数据
     * @return array
     * @throws \Exception
     */
    public function reinstallServer($serverId, $data)
    {
        $result = $this->sendRequest('POST', '/servers/' . $serverId . '/reinstall', [
            'json' => $data
        ]);

        if (!$result['success']) {
            throw new \Exception('重装云服务器失败: ' . $result['error']);
        }

        return $result['data'];
    }

    /**
     * 重置云服务器密码
     * @param string $serverId 服务器ID
     * @param array $data 密码数据
     * @return array
     * @throws \Exception
     */
    public function resetServerPassword($serverId, $data)
    {
        $result = $this->sendRequest('POST', '/servers/' . $serverId . '/reset-password', [
            'json' => $data
        ]);

        if (!$result['success']) {
            throw new \Exception('重置云服务器密码失败: ' . $result['error']);
        }

        return $result['data'];
    }

    /**
     * 续费云服务器
     * @param string $serverId 服务器ID
     * @param array $data 续费数据
     * @return array
     * @throws \Exception
     */
    public function renewServer($serverId, $data)
    {
        $result = $this->sendRequest('POST', '/servers/' . $serverId . '/renew', [
            'json' => $data
        ]);

        if (!$result['success']) {
            throw new \Exception('续费云服务器失败: ' . $result['error']);
        }

        return $result['data'];
    }

    /**
     * 续费云服务器（朝暮数据API）
     * @param int $serverId 云服务器编号
     * @param int $paymentCycle 付款周期
     * @return array 续费结果
     * @throws \Exception
     */
    public function renewCloudServer($serverId, $paymentCycle)
    {
        $result = $this->sendRequest('POST', '/cloud/renew/' . $serverId, [
            'json' => [
                'paymentCycle' => $paymentCycle
            ]
        ]);

        if (!$result['success']) {
            // 检查是否是认证错误
            if (isset($result['status_code']) && $result['status_code'] == 401) {
                throw new \Exception('API Key 无效或已过期，请检查您的 API Key 是否正确');
            }
            throw new \Exception('续费云服务器失败: ' . $result['error']);
        }

        // 检查API响应中的success字段
        if (!isset($result['data']['success']) || !$result['data']['success']) {
            $errorMessage = isset($result['data']['message']) ? $result['data']['message'] : '续费失败';
            throw new \Exception($errorMessage);
        }

        return $result['data'];
    }

    /**
     * 销毁云服务器
     * @param string $serverId 服务器ID
     * @return array
     * @throws \Exception
     */
    public function destroyServer($serverId)
    {
        $result = $this->sendRequest('DELETE', '/servers/' . $serverId);

        if (!$result['success']) {
            throw new \Exception('销毁云服务器失败: ' . $result['error']);
        }

        return $result['data'];
    }

    /**
     * 获取可用区下的镜像
     * @param string $regionId 可用区ID
     * @return array
     * @throws \Exception
     */
    public function getImages($regionId)
    {
        $result = $this->sendRequest('GET', '/regions/' . $regionId . '/images');

        if (!$result['success']) {
            throw new \Exception('获取镜像列表失败: ' . $result['error']);
        }

        return $result['data'];
    }

    /**
     * 获取订购云服务器的镜像列表
     * @param int $productId 云服务器产品编号
     * @return array 按type分组的镜像列表
     * @throws \Exception
     */
    public function getProductImages($productId)
    {
        $result = $this->sendRequest('GET', '/image/product/' . $productId);

        if (!$result['success']) {
            throw new \Exception('获取产品镜像列表失败: ' . $result['error']);
        }

        $images = $result['data'];

        // 按type字段进行分组
        $groupedImages = [];
        foreach ($images as $image) {
            $type = $image['type'] ?? '其他';

            if (!isset($groupedImages[$type])) {
                $groupedImages[$type] = [];
            }

            $groupedImages[$type][] = $image;
        }

        // 转换为二维数组格式
        $result = [];
        foreach ($groupedImages as $type => $typeImages) {
            $result[] = [
                'type' => $type,
                'images' => $typeImages
            ];
        }

        return $result;
    }

    /**
     * 变更云服务器
     * @param string $serverId 服务器ID
     * @param array $data 变更数据
     * @return array
     * @throws \Exception
     */
    public function resizeServer($serverId, $data)
    {
        $result = $this->sendRequest('POST', '/servers/' . $serverId . '/resize', [
            'json' => $data
        ]);

        if (!$result['success']) {
            throw new \Exception('变更云服务器失败: ' . $result['error']);
        }

        return $result['data'];
    }

    /**
     * 修改云服务器用户备注
     * @param string $serverId 服务器ID
     * @param array $data 备注数据
     * @return array
     * @throws \Exception
     */
    public function updateServerRemark($serverId, $data)
    {
        $result = $this->sendRequest('PUT', '/servers/' . $serverId . '/remark', [
            'json' => $data
        ]);

        if (!$result['success']) {
            throw new \Exception('修改云服务器备注失败: ' . $result['error']);
        }

        return $result['data'];
    }

    /**
     * 获取功能参数比较
     * @return array
     * @throws \Exception
     */
    public function getFeatureComparison()
    {
        $result = $this->sendRequest('GET', '/features/comparison');

        if (!$result['success']) {
            throw new \Exception('获取功能参数比较失败: ' . $result['error']);
        }

        return $result['data'];
    }

    /**
     * 获取某个可用区的功能参数比较
     * @param string $regionId 可用区编号
     * @return array 功能参数比较列表
     * @throws \Exception
     */
    public function getRegionFeatureComparison($regionId)
    {
        $result = $this->sendRequest('GET', '/compare/region/' . $regionId);

        if (!$result['success']) {
            // 检查是否是认证错误
            if (isset($result['status_code']) && $result['status_code'] == 401) {
                throw new \Exception('API Key 无效或已过期，请检查您的 API Key 是否正确');
            }
            throw new \Exception('获取可用区功能参数比较失败: ' . $result['error']);
        }

        return $result['data'];
    }

    /**
     * 获取云服务器控制台
     * @param string $serverId 服务器ID
     * @return array
     * @throws \Exception
     */
    public function getServerConsole($serverId)
    {
        $result = $this->sendRequest('GET', '/servers/' . $serverId . '/console');

        if (!$result['success']) {
            throw new \Exception('获取云服务器控制台失败: ' . $result['error']);
        }

        return $result['data'];
    }

    /**
     * 获取变更云服务器价格
     * @param string $serverId 服务器ID
     * @param array $data 变更数据
     * @return array
     * @throws \Exception
     */
    public function getResizePrice($serverId, $data)
    {
        $result = $this->sendRequest('POST', '/servers/' . $serverId . '/resize/price', [
            'json' => $data
        ]);

        if (!$result['success']) {
            throw new \Exception('获取变更云服务器价格失败: ' . $result['error']);
        }

        return $result['data'];
    }

    /**
     * 获取云服务器产品价格
     * @param int $productId 产品ID
     * @param array $params 可选参数：disk(系统盘), diskData(数据盘), bandwidth(带宽)
     * @return array 价格数据，包含月付、季付、年付、小时价格
     * @throws \Exception
     */
    public function getProductPrice($productId, $params = [])
    {
        $query = http_build_query($params);
        $uri = '/product/price/' . $productId . ($query ? '?' . $query : '');

        $result = $this->sendRequest('GET', $uri);

        if (!$result['success']) {
            throw new \Exception('[ERROR]获取云服务器产品价格失败: ' . $result['error']);
        }

        return $result['data'];
    }

    /**
     * 获取云服务器信息
     * @param int $serverId 云服务器产品编号
     * @return array 云服务器详细信息
     * @throws \Exception
     */
    public function getCloudServerInfo($serverId)
    {
        $result = $this->sendRequest('GET', '/cloud/' . $serverId);

        if (!$result['success']) {
            // 检查是否是认证错误
            if (isset($result['status_code']) && $result['status_code'] == 401) {
                throw new \Exception('API Key 无效或已过期，请检查您的 API Key 是否正确');
            }
            throw new \Exception('获取云服务器信息失败: ' . $result['error']);
        }

        return $result['data'];
    }

    /**
     * 重启/开机云服务器
     * @param int $serverId 云服务器编号
     * @return array 操作结果
     * @throws \Exception
     */
    public function rebootCloudServer($serverId)
    {
        $result = $this->sendRequest('POST', '/cloud/reboot/' . $serverId);

        if (!$result['success']) {
            // 检查是否是认证错误
            if (isset($result['status_code']) && $result['status_code'] == 401) {
                throw new \Exception('API Key 无效或已过期，请检查您的 API Key 是否正确');
            }
            throw new \Exception('重启云服务器失败: ' . $result['error']);
        }

        return $result['data'];
    }

    /**
     * 关机云服务器
     * @param int $serverId 云服务器编号
     * @return array 操作结果
     * @throws \Exception
     */
    public function shutdownCloudServer($serverId)
    {
        $result = $this->sendRequest('POST', '/cloud/shutdown/' . $serverId);

        if (!$result['success']) {
            // 检查是否是认证错误
            if (isset($result['status_code']) && $result['status_code'] == 401) {
                throw new \Exception('API Key 无效或已过期，请检查您的 API Key 是否正确');
            }
            throw new \Exception('关机云服务器失败: ' . $result['error']);
        }

        return $result['data'];
    }

    /**
     * 重置云服务器密码
     * @param int $serverId 云服务器编号
     * @param string $password 新密码
     * @return array 操作结果
     * @throws \Exception
     */
    public function resetCloudServerPassword($serverId, $password)
    {
        $result = $this->sendRequest('POST', '/cloud/password/' . $serverId, [
            'json' => [
                'password' => $password
            ]
        ]);

        if (!$result['success']) {
            // 检查是否是认证错误
            if (isset($result['status_code']) && $result['status_code'] == 401) {
                throw new \Exception('API Key 无效或已过期，请检查您的 API Key 是否正确');
            }
            throw new \Exception('重置云服务器密码失败: ' . $result['error']);
        }

        // 检查API响应中的success字段
        if (!isset($result['data']['success']) || !$result['data']['success']) {
            $errorMessage = isset($result['data']['message']) ? $result['data']['message'] : '重置密码失败';
            throw new \Exception($errorMessage);
        }

        return $result['data'];
    }

    /**
     * 重装云服务器
     * @param int $serverId 云服务器编号
     * @param int $imageId 镜像编号
     * @return array 操作结果
     * @throws \Exception
     */
    public function rebuildCloudServer($serverId, $imageId)
    {
        $result = $this->sendRequest('POST', '/cloud/rebuild/' . $serverId, [
            'json' => [
                'imageId' => $imageId
            ]
        ]);

        if (!$result['success']) {
            // 检查是否是认证错误
            if (isset($result['status_code']) && $result['status_code'] == 401) {
                throw new \Exception('API Key 无效或已过期，请检查您的 API Key 是否正确');
            }
            throw new \Exception('重装云服务器失败: ' . $result['error']);
        }

        return $result['data'];
    }

    /**
     * 获取重装云服务器的镜像列表
     * @param int $serverId 云服务器编号
     * @return array 按type分组的镜像列表
     * @throws \Exception
     */
    public function getCloudServerImages($serverId)
    {
        $result = $this->sendRequest('GET', '/image/cloud/' . $serverId);

        if (!$result['success']) {
            // 检查是否是认证错误
            if (isset($result['status_code']) && $result['status_code'] == 401) {
                throw new \Exception('API Key 无效或已过期，请检查您的 API Key 是否正确');
            }
            throw new \Exception('获取重装云服务器镜像列表失败: ' . $result['error']);
        }

        $images = $result['data'];

        // 按type字段进行分组
        $groupedImages = [];
        foreach ($images as $image) {
            $type = $image['type'] ?? '其他';

            if (!isset($groupedImages[$type])) {
                $groupedImages[$type] = [];
            }

            $groupedImages[$type][] = $image;
        }

        // 转换为二维数组格式
        $result = [];
        foreach ($groupedImages as $type => $typeImages) {
            $result[] = [
                'type' => $type,
                'images' => $typeImages
            ];
        }

        return $result;
    }

    /**
     * 测试API连接
     * @return bool
     */
    public function testConnection()
    {
        try {
            $this->getRegions();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 获取日志服务实例
     * @return LogService
     */
    public function getLogService()
    {
        return $this->logService;
    }

    /**
     * 获取或生成加密密钥
     * @return string
     */
    private function getEncryptionKey()
    {
        $key = HlwidcCache::value('zhaomu_encryption_key', null);

        if (empty($key)) {
            // 生成32字节的随机密钥
            $key = bin2hex(random_bytes(16)); // 32个字符的十六进制字符串
            HlwidcCache::setValue('zhaomu_encryption_key', $key, 0);
        }

        return $key;
    }

    /**
     * 获取或生成加密IV
     * @return string
     */
    private function getEncryptionIv()
    {
        $iv = HlwidcCache::value('zhaomu_encryption_iv', null);

        if (empty($iv)) {
            // 生成16字节的随机IV
            $iv = bin2hex(random_bytes(16)); // 32个字符的十六进制字符串
            HlwidcCache::setValue('zhaomu_encryption_iv', $iv, 0);
        }

        return $iv;
    }

    /**
     * AES-128-CBC 加密
     * @param string $data 要加密的数据
     * @return string 加密后的数据（base64编码）
     * @throws \Exception
     */
    public function encrypt($data)
    {
        try {
            $key = $this->getEncryptionKey();
            $iv = $this->getEncryptionIv();

            // 将十六进制字符串转换为二进制
            $keyBinary = hex2bin($key);
            $ivBinary = hex2bin($iv);

            // 使用AES-128-CBC加密
            $encrypted = openssl_encrypt($data, 'AES-128-CBC', $keyBinary, OPENSSL_RAW_DATA, $ivBinary);

            if ($encrypted === false) {
                throw new \Exception('加密失败: ' . openssl_error_string());
            }

            // 返回base64编码的结果
            return base64_encode($encrypted);
        } catch (\Exception $e) {
            // 检查 logService 是否已初始化
            if ($this->logService !== null) {
                $this->logService->error("AES加密失败", [
                    'error' => $e->getMessage(),
                    'data_length' => strlen($data)
                ], 'encryption');
            }
            throw $e;
        }
    }

    /**
     * AES-128-CBC 解密
     * @param string $encryptedData 加密的数据（base64编码）
     * @return string 解密后的数据
     * @throws \Exception
     */
    public function decrypt($encryptedData)
    {
        try {
            $key = $this->getEncryptionKey();
            $iv = $this->getEncryptionIv();

            // 将十六进制字符串转换为二进制
            $keyBinary = hex2bin($key);
            $ivBinary = hex2bin($iv);

            // 解码base64
            $encrypted = base64_decode($encryptedData);

            if ($encrypted === false) {
                throw new \Exception('Base64解码失败');
            }

            // 使用AES-128-CBC解密
            $decrypted = openssl_decrypt($encrypted, 'AES-128-CBC', $keyBinary, OPENSSL_RAW_DATA, $ivBinary);

            if ($decrypted === false) {
                throw new \Exception('解密失败: ' . openssl_error_string());
            }

            return $decrypted;
        } catch (\Exception $e) {
            // 检查 logService 是否已初始化
            if ($this->logService !== null) {
                $this->logService->error("AES解密失败", [
                    'error' => $e->getMessage(),
                    'data_length' => strlen($encryptedData)
                ], 'encryption');
            }
            throw $e;
        }
    }

    /**
     * SHA-128 哈希
     * @param string $data 要哈希的数据
     * @return string SHA-128哈希值
     */
    public function sha128($data)
    {
        // SHA-128实际上是SHA-1的变种，这里使用SHA-1
        return hash('sha1', $data);
    }

    /**
     * 生成随机密钥和IV（用于重置）
     * @return array 包含新密钥和IV的数组
     */
    public function generateNewKeys()
    {
        try {
            // 生成新的密钥和IV
            $newKey = bin2hex(random_bytes(16));
            $newIv = bin2hex(random_bytes(16));

            // 保存到缓存
            HlwidcCache::setValue('zhaomu_encryption_key', $newKey, 0);
            HlwidcCache::setValue('zhaomu_encryption_iv', $newIv, 0);

            $this->logService->info("生成新的加密密钥和IV", [
                'key_length' => strlen($newKey),
                'iv_length' => strlen($newIv)
            ], 'encryption');

            return [
                'key' => $newKey,
                'iv' => $newIv,
                'success' => true
            ];
        } catch (\Exception $e) {
            $this->logService->error("生成新密钥失败", [
                'error' => $e->getMessage()
            ], 'encryption');

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * 测试加密解密功能
     * @param string $testData 测试数据
     * @return array 测试结果
     */
    public function testEncryption($testData = 'Hello World')
    {
        try {
            // 加密测试
            $encrypted = $this->encrypt($testData);

            // 解密测试
            $decrypted = $this->decrypt($encrypted);

            // 验证结果
            $success = ($decrypted === $testData);

            $this->logService->info("加密解密测试", [
                'original' => $testData,
                'encrypted' => $encrypted,
                'decrypted' => $decrypted,
                'success' => $success
            ], 'encryption');

            return [
                'success' => $success,
                'original' => $testData,
                'encrypted' => $encrypted,
                'decrypted' => $decrypted,
                'message' => $success ? '加密解密测试成功' : '加密解密测试失败'
            ];
        } catch (\Exception $e) {
            $this->logService->error("加密解密测试失败", [
                'error' => $e->getMessage()
            ], 'encryption');

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * 静态方法：获取或生成加密密钥
     * @return string
     */
    private static function getStaticEncryptionKey()
    {
        $key = HlwidcCache::value('zhaomu_encryption_key', null);

        if (empty($key)) {
            // 生成32字节的随机密钥
            $key = bin2hex(random_bytes(16)); // 32个字符的十六进制字符串
            HlwidcCache::setValue('zhaomu_encryption_key', $key, 0);
        }

        return $key;
    }

    /**
     * 静态方法：获取或生成加密IV
     * @return string
     */
    private static function getStaticEncryptionIv()
    {
        $iv = HlwidcCache::value('zhaomu_encryption_iv', null);

        if (empty($iv)) {
            // 生成16字节的随机IV
            $iv = bin2hex(random_bytes(16)); // 32个字符的十六进制字符串
            HlwidcCache::setValue('zhaomu_encryption_iv', $iv, 0);
        }

        return $iv;
    }

    /**
     * 静态方法：AES-128-CBC 加密
     * @param string $data 要加密的数据
     * @return string 加密后的数据（base64编码）
     * @throws \Exception
     */
    public static function staticEncrypt($data)
    {
        try {
            $key = self::getStaticEncryptionKey();
            $iv = self::getStaticEncryptionIv();

            // 将十六进制字符串转换为二进制
            $keyBinary = hex2bin($key);
            $ivBinary = hex2bin($iv);

            // 使用AES-128-CBC加密
            $encrypted = openssl_encrypt($data, 'AES-128-CBC', $keyBinary, OPENSSL_RAW_DATA, $ivBinary);

            if ($encrypted === false) {
                throw new \Exception('加密失败: ' . openssl_error_string());
            }

            // 返回base64编码的结果
            return base64_encode($encrypted);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * 获取并更新云服务器信息到host对象
     * @param int $businessId 业务ID
     * @param object $host host对象
     * @return array 更新后的云服务器信息
     * @throws \Exception
     */
    public function getAndUpdateCloudServerInfo($businessId, $host)
    {
        try {
            // 调用朝暮云官方API获取云服务器信息
            $cloudServerInfo = $this->getCloudServerInfo($businessId);
            
            // 获取销售折扣和汇率设置
            $salesDiscount = HlwidcCache::value('zhaomu_sales_discount', 90);
            $exchangeRate = HlwidcCache::value('zhaomu_exchange_rate', 1);
            $currencyUnit = HlwidcCache::value('zhaomu_currency_unit', 'CNY');
            
            // 重新计算价格：应用汇率和折扣
            if (isset($cloudServerInfo['price']) && $cloudServerInfo['price'] > 0) {
                $convertedPrice = $cloudServerInfo['price'] * $exchangeRate;
                $discountedPrice = $convertedPrice * ($salesDiscount / 100);
                $cloudServerInfo['price'] = round($discountedPrice, 2);
            }
            
            if (isset($cloudServerInfo['priceQuarter']) && $cloudServerInfo['priceQuarter'] > 0) {
                $convertedPrice = $cloudServerInfo['priceQuarter'] * $exchangeRate;
                $discountedPrice = $convertedPrice * ($salesDiscount / 100);
                $cloudServerInfo['priceQuarter'] = round($discountedPrice, 2);
            }
            
            if (isset($cloudServerInfo['priceHalfYear']) && $cloudServerInfo['priceHalfYear'] > 0) {
                $convertedPrice = $cloudServerInfo['priceHalfYear'] * $exchangeRate;
                $discountedPrice = $convertedPrice * ($salesDiscount / 100);
                $cloudServerInfo['priceHalfYear'] = round($discountedPrice, 2);
            }
            
            if (isset($cloudServerInfo['priceYear']) && $cloudServerInfo['priceYear'] > 0) {
                $convertedPrice = $cloudServerInfo['priceYear'] * $exchangeRate;
                $discountedPrice = $convertedPrice * ($salesDiscount / 100);
                $cloudServerInfo['priceYear'] = round($discountedPrice, 2);
            }
            
            // 添加价格计算元数据
            $cloudServerInfo['price_meta'] = [
                'sales_discount' => $salesDiscount,
                'exchange_rate' => $exchangeRate,
                'currency_unit' => $currencyUnit,
                'applied_discount' => true,
                'applied_exchange' => $exchangeRate != 1
            ];
            
            // 根据当前支付周期更新amount字段
            $currentBillingCycle = $host->billingcycle ?? 'monthly';
            switch ($currentBillingCycle) {
                case 'monthly':
                    $host->amount = $cloudServerInfo['price'] ?? 0;
                    break;
                case 'quarterly':
                    $host->amount = $cloudServerInfo['priceQuarter'] ?? 0;
                    break;
                case 'semiannually':
                    $host->amount = $cloudServerInfo['priceHalfYear'] ?? 0;
                    break;
                case 'annually':
                    $host->amount = $cloudServerInfo['priceYear'] ?? 0;
                    break;
                default:
                    $host->amount = $cloudServerInfo['price'] ?? 0;
                    break;
            }
            
            // 将官方数据附加到host对象
            $host->cloud_server_info = $cloudServerInfo;
            
            // 从服务器信息中提取并更新host数据
            if (isset($cloudServerInfo['ip']) && !empty($cloudServerInfo['ip'])) {
                $host->dedicatedip = $cloudServerInfo['ip'];
            }
            
            if (isset($cloudServerInfo['endTime']) && !empty($cloudServerInfo['endTime'])) {
                // 将到期时间转换为时间戳
                $endTime = strtotime($cloudServerInfo['endTime']);
                if ($endTime !== false) {
                    $host->nextduedate = $endTime;
                }
            }
            
            if (isset($cloudServerInfo['password']) && !empty($cloudServerInfo['password'])) {
                // 使用 cmf_encrypt 方法加密密码
                $host->password = cmf_encrypt($cloudServerInfo['password']);
            }
            
            if (isset($cloudServerInfo['root']) && !empty($cloudServerInfo['root'])) {
                // 更新远程账号
                $host->username = $cloudServerInfo['root'];
            }
            $host->domainstatus = 'Active';
            
            // 保存更新后的host数据
            $host->save();
            
            return $cloudServerInfo;
            
        } catch (\Exception $e) {
            throw new \Exception('获取并更新云服务器信息失败: ' . $e->getMessage());
        }
    }

    /**
     * 提交订单
     * @param array $orderData 订单数据
     * @return array 订单创建结果
     * @throws \Exception
     */
    public function submitOrder($orderData)
    {
        // 检查是否需要实名认证
        $realNameAuthRequired = HlwidcCache::value('zhaomu_real_name_auth', true);
        
        if ($realNameAuthRequired) {
            // 获取客户ID
            $customerId = $orderData['customer']['id'] ?? 0;
            if (empty($customerId)) {
                throw new \Exception('客户ID不能为空');
            }
            
            // 检查客户是否已实名认证
            $hasPersonCert = \addons\zhaomu_cloud\model\CertifiPerson::where('auth_user_id', $customerId)
                ->where('status', 1)
                ->find();
            
            // 如果个人已认证，直接通过
            if ($hasPersonCert) {
                // 个人认证通过，无需检查企业认证
            } else {
                // 个人未认证，检查企业认证
                $hasCompanyCert = \addons\zhaomu_cloud\model\CertifiCompany::where('auth_user_id', $customerId)
                    ->where('status', 1)
                    ->find();
                
                // 如果企业也未认证，抛出错误
                if (!$hasCompanyCert) {
                    throw new \Exception('购买云服务器需要实名认证，请先完成个人认证或企业认证');
                }
            }
        }

        // 开启数据库事务
        Db::startTrans();

        try {
            // 构建产品名称
            $productName = [];
            if ($orderData['location']['country']['name'] != '中国') {
                $productName[] = $orderData['location']['country']['name'];
            }
            if ($orderData['location']['province']['name'] != '默认') {
                $productName[] = $orderData['location']['province']['name'];
            }
            $productName[] = $orderData['location']['zone']['city'];
            $productName[] = $orderData['product']['cpu'] . '核';
            $productName[] = ($orderData['product']['ram'] / 1024) . 'G';
            $productName = implode('', $productName);

            // 先获取产品价格
            $productPrice = 0;
            try {
                $priceParams = [];
                if (!empty($orderData['product']['disk'])) {
                    $priceParams['disk'] = $orderData['product']['disk'];
                }
                if (!empty($orderData['product']['diskData'])) {
                    $priceParams['diskData'] = $orderData['product']['diskData'];
                }
                if (!empty($orderData['product']['bandwidth'])) {
                    $priceParams['bandwidth'] = $orderData['product']['bandwidth'];
                }

                $prices = $this->getProductPrice($orderData['product']['id'], $priceParams);

                // 根据支付周期获取对应价格
                $cycle = $orderData['payment']['cycle'] ?? 'monthly';
                $cycleMap = [
                    'monthly' => '1',
                    'quarterly' => '2',
                    'yearly' => '4'
                ];
                $priceKey = $cycleMap[$cycle] ?? '1';

                if (isset($prices[$priceKey])) {
                    $productPrice = $prices[$priceKey];
                    
                    // 获取销售折扣和汇率设置
                    $salesDiscount = HlwidcCache::value('zhaomu_sales_discount', 90);
                    $exchangeRate = HlwidcCache::value('zhaomu_exchange_rate', 1);
                    
                    // 应用汇率和折扣
                    $convertedPrice = $productPrice * $exchangeRate;
                    $discountedPrice = $convertedPrice * ($salesDiscount / 100);
                    $productPrice = round($discountedPrice, 2);

                } else {
                    // 如果没有对应周期的价格，直接抛出错误
                    throw new \Exception('[ERROR]产品不支持' . $cycle . '支付周期，可用周期: ' . implode(', ', array_keys($prices)));
                }
            } catch (\Exception $e) {
                // 如果获取价格失败，直接抛出错误
                throw new \Exception('[ERROR]取产品价格失败: ' . $e->getMessage());
            }

            // 验证客户余额
            $client = \addons\zhaomu_cloud\model\Clients::find($orderData['customer']['id']);
            if (!$client) {
                throw new \Exception('[ERROR]客户不存在');
            }

            // 计算可用余额（账户余额 + 信用额度余额）
            $availableBalance = $client->credit + $client->credit_limit_balance;
            if ($availableBalance < $productPrice) {
                throw new \Exception('[ERROR]余额不足，当前可用余额: ' . $availableBalance . '，订单金额: ' . $productPrice);
            }

            // 创建产品
            $product = (new \addons\zhaomu_cloud\model\Product())->addOrExit([
                'config_option1' => $orderData['product']['id'],
                'name' => $productName,
            ]);


            $businessCustomField = (new \addons\zhaomu_cloud\model\CustomField())->addOrExit([
                'relid' => $product->id,
                'fieldname' => '业务id',
                'fieldtype' => 'text',
                'description' => '业务id',
            ]);
            $systemDiskCustomField = (new \addons\zhaomu_cloud\model\CustomField())->addOrExit([
                'relid' => $product->id,
                'fieldname' => '系统盘',
                'fieldtype' => 'text',
                'description' => '系统盘',
            ]);
            $dataDiskCustomField = (new \addons\zhaomu_cloud\model\CustomField())->addOrExit([
                'relid' => $product->id,
                'fieldname' => '数据盘',
                'fieldtype' => 'text',
                'description' => '数据盘',
            ]);
            $bandwidthCustomField = (new \addons\zhaomu_cloud\model\CustomField())->addOrExit([
                'relid' => $product->id,
                'fieldname' => '带宽',
                'fieldtype' => 'text',
                'description' => '带宽',
            ]);
            $operatingSystemCustomField = (new \addons\zhaomu_cloud\model\CustomField())->addOrExit([
                'relid' => $product->id,
                'fieldname' => '操作系统',
                'fieldtype' => 'text',
                'description' => '操作系统',
            ]);

            // 1. 先创建订单
            $order = (new \addons\zhaomu_cloud\model\Order())->addOrExit([
                'uid' => $orderData['customer']['id'],
                'ordernum' => 'ORD-' . date('YmdHis') . '-' . rand(1000, 9999),
                'status' => 'Pending',
                'amount' => $productPrice,
                'payment' => $orderData['payment']['cycle'] ?? 'monthly',
                'notes' => '产品订单'
            ]);

            // 2. 创建Host（Pending状态）
            $host = (new \addons\zhaomu_cloud\model\Host())->addOrExit([
                'uid' => $orderData['customer']['id'],
                'orderid' => $order->id,
                'productid' => $product->id,
                'serverid' => 0, // 暂时设为0，后续分配
                'domain' => 'ser' . rand(100000000000, 999999999999),
                'payment' => $orderData['payment']['cycle'] ?? 'monthly',
                'firstpaymentamount' => $productPrice, // 首付金额（当前支付的价格）
                'amount' => $productPrice, // 续费金额（下次续费的价格）
                'billingcycle' => $orderData['payment']['cycle'] ?? 'monthly',
                'domainstatus' => 'Pending',
                'username' => '',
                'password' => '',
                'notes' => $productName,
                'os' => $orderData['product']['os'] ?? '',
                'os_url' => $orderData['product']['os_url'] ?? '',
                'create_time' => time(),
                'nextduedate' => time() + 30 * 24 * 3600, // 30天后到期
                'nextinvoicedate' => time() + 30 * 24 * 3600
            ]);

            // 3. 创建发票
            $invoice = (new \addons\zhaomu_cloud\model\Invoice())->addOrExit([
                'uid' => $orderData['customer']['id'],
                'type' => 'product',
                'subtotal' => $productPrice,
                'status' => 'Paid',
                'payment' => $orderData['payment']['cycle'] ?? 'monthly',
                'credit' => $productPrice,
                'tax' => 0.00,
                'tax2' => 0.00,
                'payment_status' => 'Paid',
                'billingcycle' => $orderData['payment']['cycle'] ?? 'monthly'
            ]);
            //关联item
            $invoiceItem = (new \addons\zhaomu_cloud\model\InvoiceItem())->addOrExit([
                'invoice_id' => $invoice->id,
                'uid' => $orderData['customer']['id'],
                'billingcycle' => $orderData['payment']['cycle'] ?? 'monthly',
                'type' => 'product',
                'rel_id' => $host->id,
                'description' => $productName,
                'amount' => $productPrice,
                'payment' => $orderData['payment']['cycle'] ?? 'monthly'
            ]);

            // 扣除客户余额
            $remainingAmount = $productPrice;

            // 先扣除账户余额
            if ($client->credit > 0) {
                $deductFromCredit = min($client->credit, $remainingAmount);
                $client->credit -= $deductFromCredit;
                $remainingAmount -= $deductFromCredit;
            }

            // 如果还有剩余金额，从信用额度扣除
            if ($remainingAmount > 0 && $client->credit_limit_balance > 0) {
                $deductFromCreditLimit = min($client->credit_limit_balance, $remainingAmount);
                $client->credit_limit_balance -= $deductFromCreditLimit;
            }

            // 保存客户余额更新
            $client->save();
            $order->invoiceid = $invoice->id;
            $order->save();

          
            //添加系统盘到CustomFieldValue
            (new \addons\zhaomu_cloud\model\CustomFieldValue())->addOrExit([
                'fieldid' => $systemDiskCustomField->id,
                'relid' => $host->id,
                'value' => $orderData['configuration']['systemDisk']
            ]);
            //添加数据盘到CustomFieldValue
            (new \addons\zhaomu_cloud\model\CustomFieldValue())->addOrExit([
                'fieldid' => $dataDiskCustomField->id,
                'relid' => $host->id,
                'value' => $orderData['configuration']['dataDisk']
            ]);
            //添加带宽到CustomFieldValue
            (new \addons\zhaomu_cloud\model\CustomFieldValue())->addOrExit([
                'fieldid' => $bandwidthCustomField->id,
                'relid' => $host->id,
                'value' => $orderData['configuration']['bandwidth']
            ]);
            //添加操作系统到CustomFieldValue
            (new \addons\zhaomu_cloud\model\CustomFieldValue())->addOrExit([
                'fieldid' => $operatingSystemCustomField->id,
                'relid' => $host->id,
                'value' => $orderData['image']['id']
            ]);
            // 提交事务
            Db::commit();
            //使用真实请求，用try处理
            try {
                $result = $this->orderCloudServer([
                    'productId' => $orderData['product']['id'],
                    'disk' => $orderData['product']['disk'],
                    'diskData' => $orderData['product']['diskData'],
                    'bandwidth' => $orderData['product']['bandwidth'],
                    'imageId' => $orderData['image']['id'],
                    'paymentCycle' => intval($orderData['payment']['cycle'])
                ]);


                //添加业务ID到CustomFieldValue

                (new \addons\zhaomu_cloud\model\CustomFieldValue())->addOrExit([
                    'fieldid' => $businessCustomField->id,
                    'relid' => $host->id,
                    'value' => $result['info']['id']
                ]);

                //更新订单状态
                $order->status = 'Active';
                $order->save();
                //更新host状态
                $host->domainstatus = 'Active';
                $host->save();
                
                // 延时一秒后获取并更新云服务器信息
                sleep(1);
                try {
                    $this->getAndUpdateCloudServerInfo($result['info']['id'], $host);
                } catch (\Exception $e) {
                    // 记录错误但不影响订单创建结果
                    $this->logService->error("延时获取云服务器信息失败", [
                        'business_id' => $result['info']['id'],
                        'host_id' => $host->id,
                        'error' => $e->getMessage()
                    ], 'order');
                }
                
                // 返回订单创建结果
                return [
                    'success' => true,
                    'data' => [
                        'received_data' => $orderData,
                        'timestamp' => date('Y-m-d H:i:s'),
                        'status' => 'received',
                        'created_records' => [
                            'product' => [
                                'id' => $product->id,
                                'name' => $product->name,
                                'config_option1' => $product->config_option1
                            ],
                            'order' => [
                                'id' => $order->id,
                                'ordernum' => $order->ordernum,
                                'status' => $order->status,
                                'amount' => $order->amount
                            ],
                            'host' => [
                                'id' => $host->id,
                                'domain' => $host->domain,
                                'domainstatus' => $host->domainstatus,
                                'productid' => $host->productid,
                                'orderid' => $host->orderid
                            ],
                            'invoice' => [
                                'id' => $invoice->id,
                                'invoice_num' => $invoice->invoice_num,
                                'total' => $invoice->total,
                                'status' => $invoice->status
                            ],
                            'invoice_item' => [
                                'id' => $invoiceItem->id,
                                'invoice_id' => $invoiceItem->invoice_id,
                                'rel_id' => $invoiceItem->rel_id,
                                'description' => $invoiceItem->description,
                                'amount' => $invoiceItem->amount
                            ]
                        ]
                    ]
                ];
            } catch (\Exception $e) {
                //记录失败到order的notes
                $order->notes = $e->getMessage();
                $order->save();
                throw new \Exception($e->getMessage());
            }
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            throw $e;
        }
    }
}
