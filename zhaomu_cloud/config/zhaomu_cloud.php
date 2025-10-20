<?php

/**
 * 朝暮云.NET API配置文件
 * 
 * 此配置文件专门针对朝暮数据的.NET API进行优化
 */

return [
    // API基础配置
    'api' => [
        'base_url' => 'https://api.zhaomu.com',
        'timeout' => 30,
        'verify_ssl' => true,
        'version' => 'v1',
    ],
    
    // .NET API特殊配置
    'dotnet' => [
        'user_agent' => 'ZhaoMuCloud-PHP-SDK/1.0',
        'content_type' => 'application/json',
        'accept' => 'application/json',
        'x_requested_with' => 'XMLHttpRequest',
    ],
    
    // 认证配置
    'auth' => [
        'type' => 'bearer', // Bearer Token认证
        'header_name' => 'Authorization',
        'header_format' => 'Bearer {token}',
    ],
    
    // 日志配置
    'log' => [
        'enabled' => true,
        'level' => 'info',
        'path' => __DIR__ . '/../logs',
        'max_file_size' => 10485760, // 10MB
        'max_files' => 30,
    ],
    
    // 缓存配置
    'cache' => [
        'enabled' => true,
        'ttl' => 300, // 5分钟
        'prefix' => 'zhaomu_cloud_',
    ],
    
    // 重试配置
    'retry' => [
        'enabled' => true,
        'max_attempts' => 3,
        'delay' => 1000, // 1秒
        'backoff_multiplier' => 2,
    ],
    
    // 限流配置
    'rate_limit' => [
        'enabled' => true,
        'requests_per_minute' => 60,
        'burst_limit' => 10,
    ],
    
    // 错误处理配置
    'error_handling' => [
        'log_errors' => true,
        'throw_exceptions' => true,
        'return_errors' => false,
    ],
    
    // 调试配置
    'debug' => [
        'enabled' => false,
        'log_requests' => true,
        'log_responses' => true,
        'log_headers' => true,
    ],
    
    // 默认参数
    'defaults' => [
        'region_id' => null,
        'product_id' => null,
        'image_id' => null,
    ],
    
    // API端点配置
    'endpoints' => [
        'regions' => '/region',
        'servers' => '/servers',
        'products' => '/products',
        'products_by_region' => '/product/region/{region_id}',
        'images' => '/regions/{region_id}/images',
        'features' => '/features/comparison',
    ],
    
    // 响应格式配置
    'response' => [
        'format' => 'json',
        'encoding' => 'utf-8',
        'pretty_print' => false,
    ],
    
    // 请求配置
    'request' => [
        'method' => 'GET',
        'headers' => [],
        'body' => null,
        'query' => [],
    ],
    
    // 验证配置
    'validation' => [
        'required_fields' => [
            'api_key' => 'API密钥不能为空',
        ],
        'field_rules' => [
            'api_key' => 'string|min:1',
            'timeout' => 'integer|min:1|max:300',
            'region_id' => 'integer|min:1',
            'product_id' => 'integer|min:1',
        ],
    ],
];
