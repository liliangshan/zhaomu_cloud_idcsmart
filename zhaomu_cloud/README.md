# 朝暮云服务类 (ZhaoMuCloudService)

这是一个用于调用朝暮数据.NET API的PHP服务类，基于Guzzle HTTP客户端开发，专门针对朝暮数据的.NET后端API进行优化。

## 功能特性

- ✅ 完整的朝暮数据.NET API支持
- ✅ 基于Guzzle HTTP客户端
- ✅ 针对.NET API优化的请求头配置
- ✅ 完善的错误处理和日志记录
- ✅ 支持所有API接口
- ✅ 易于使用和扩展

## 安装依赖

在使用前，请确保已安装Guzzle HTTP客户端：

```bash
composer require guzzlehttp/guzzle
```

## 快速开始

### 1. 基本使用

```php
<?php

use addons\zhaomu_cloud\services\ZhaoMuCloudService;

// 创建服务实例
$zhaomuService = new ZhaoMuCloudService('your_api_key_here');

// 获取可用区列表
$regions = $zhaomuService->getRegions();
foreach ($regions as $region) {
    echo "ID: {$region['id']}, 国家: {$region['country']}, 城市: {$region['city']}\n";
}
```

### 2. 高级配置

```php
<?php

$config = [
    'api_key' => 'your_api_key_here',
    'timeout' => 30,                   // 请求超时时间
    'verify_ssl' => true,              // 是否验证SSL证书
    'log_path' => '/path/to/logs'      // 日志文件路径
];

$zhaomuService = new ZhaoMuCloudService($config['api_key'], $config);
```

## API接口说明

### 基础信息接口

| 方法名 | 说明 | 参数 |
|--------|------|------|
| `getRegions($grouped)` | 获取可用区列表 | `$grouped` - 是否按层级分组（默认false） |
| `getProducts($params)` | 获取云服务器产品信息 | `$params` - 查询参数 |
| `getProductsByRegion($regionId)` | 获取某个可用区下的云服务器产品列表 | `$regionId` - 可用区ID |
| `getFeatureComparison()` | 获取功能参数比较 | 无 |

### 云服务器管理接口

| 方法名 | 说明 | 参数 |
|--------|------|------|
| `getServers($params)` | 获取云服务器列表 | `$params` - 查询参数 |
| `getServer($serverId)` | 获取云服务器详情 | `$serverId` - 服务器ID |
| `createServer($data)` | 订购云服务器 | `$data` - 订购数据 |
| `rebootServer($serverId)` | 重启云服务器 | `$serverId` - 服务器ID |
| `startServer($serverId)` | 开机云服务器 | `$serverId` - 服务器ID |
| `stopServer($serverId)` | 关机云服务器 | `$serverId` - 服务器ID |
| `destroyServer($serverId)` | 销毁云服务器 | `$serverId` - 服务器ID |

### 云服务器操作接口

| 方法名 | 说明 | 参数 |
|--------|------|------|
| `reinstallServer($serverId, $data)` | 重装云服务器 | `$serverId` - 服务器ID, `$data` - 重装数据 |
| `resetServerPassword($serverId, $data)` | 重置云服务器密码 | `$serverId` - 服务器ID, `$data` - 密码数据 |
| `renewServer($serverId, $data)` | 续费云服务器 | `$serverId` - 服务器ID, `$data` - 续费数据 |
| `resizeServer($serverId, $data)` | 变更云服务器 | `$serverId` - 服务器ID, `$data` - 变更数据 |
| `updateServerRemark($serverId, $data)` | 修改云服务器用户备注 | `$serverId` - 服务器ID, `$data` - 备注数据 |

### 其他接口

| 方法名 | 说明 | 参数 |
|--------|------|------|
| `getImages($regionId)` | 获取可用区下的镜像 | `$regionId` - 可用区ID |
| `getServerConsole($serverId)` | 获取云服务器控制台 | `$serverId` - 服务器ID |
| `getResizePrice($serverId, $data)` | 获取变更云服务器价格 | `$serverId` - 服务器ID, `$data` - 变更数据 |

## 使用示例

### 获取可用区列表

#### 普通格式
```php
<?php

try {
    $regions = $zhaomuService->getRegions();
    
    foreach ($regions as $region) {
        echo "ID: {$region['id']}\n";
        echo "大洲: {$region['continent']}\n";
        echo "国家: {$region['country']}\n";
        echo "城市: {$region['city']}\n";
        echo "可用区: {$region['zone']}\n";
        echo "---\n";
    }
} catch (Exception $e) {
    echo "错误: " . $e->getMessage() . "\n";
}
```

#### 分组格式
```php
<?php

try {
    $groupedRegions = $zhaomuService->getRegions(true);
    
    foreach ($groupedRegions as $continent) {
        echo "大洲: {$continent['name']}\n";
        
        foreach ($continent['countries'] as $country) {
            echo "  国家: {$country['name']}\n";
            
            foreach ($country['provinces'] as $province) {
                echo "    省份: {$province['name']}\n";
                
                foreach ($province['zones'] as $zone) {
                    echo "      可用区: {$zone['name']} (ID: {$zone['id']})\n";
                    echo "        城市: {$zone['city']}\n";
                }
            }
        }
    }
} catch (Exception $e) {
    echo "错误: " . $e->getMessage() . "\n";
}
```

分组后的数据结构：
```json
[
  {
    "name": "亚洲",
    "countries": [
      {
        "name": "中国",
        "provinces": [
          {
            "name": "浙江",
            "zones": [
              {
                "name": "电信C2",
                "id": 592,
                "city": "杭州",
                "area": "华东"
              }
            ]
          }
        ]
      }
    ]
  }
]
```

### 获取特定可用区的产品列表

```php
<?php

try {
    // 先获取可用区列表
    $regions = $zhaomuService->getRegions();
    
    if (!empty($regions)) {
        $regionId = $regions[0]['id']; // 使用第一个可用区
        $products = $zhaomuService->getProductsByRegion($regionId);
        
        foreach ($products as $product) {
            echo "产品ID: {$product['id']}\n";
            echo "CPU: {$product['cpu']}核\n";
            echo "内存: {$product['ram']}MB\n";
            echo "系统盘: {$product['disk']}GB\n";
            echo "月付价格: {$product['price']}元\n";
            echo "年付价格: {$product['priceYear']}元\n";
            echo "标签: {$product['tags']}\n";
            echo "---\n";
        }
    }
} catch (Exception $e) {
    echo "错误: " . $e->getMessage() . "\n";
}
```

### 订购云服务器

```php
<?php

$serverData = [
    'product_id' => 1,           // 产品ID
    'region_id' => 3,            // 可用区ID
    'image_id' => 1,             // 镜像ID
    'hostname' => 'my-server',   // 主机名
    'password' => 'secure_password', // 密码
    'backup' => false,           // 是否启用备份
    'monitoring' => true,        // 是否启用监控
    'ipv6' => false,            // 是否启用IPv6
];

try {
    $result = $zhaomuService->createServer($serverData);
    echo "云服务器订购成功: " . json_encode($result, JSON_UNESCAPED_UNICODE) . "\n";
} catch (Exception $e) {
    echo "订购失败: " . $e->getMessage() . "\n";
}
```

### 云服务器管理

```php
<?php

$serverId = 'your_server_id';

try {
    // 获取服务器详情
    $server = $zhaomuService->getServer($serverId);
    echo "服务器状态: " . $server['status'] . "\n";
    
    // 重启服务器
    $zhaomuService->rebootServer($serverId);
    echo "服务器重启命令已发送\n";
    
    // 修改备注
    $zhaomuService->updateServerRemark($serverId, [
        'remark' => '生产环境服务器'
    ]);
    echo "服务器备注已更新\n";
    
} catch (Exception $e) {
    echo "操作失败: " . $e->getMessage() . "\n";
}
```

## 错误处理

所有API调用都会抛出异常，建议使用try-catch进行错误处理：

```php
<?php

try {
    $regions = $zhaomuService->getRegions();
    // 处理成功结果
} catch (Exception $e) {
    // 处理错误
    echo "API调用失败: " . $e->getMessage() . "\n";
    echo "错误文件: " . $e->getFile() . "\n";
    echo "错误行号: " . $e->getLine() . "\n";
}
```

## 日志记录

服务类集成了日志记录功能，所有API调用都会自动记录日志：

```php
<?php

// 获取日志服务实例
$logService = $zhaomuService->getLogService();

// 查看日志文件
$logFiles = $logService->getLogFiles('api');
foreach ($logFiles as $file) {
    echo "日志文件: {$file['filename']}, 大小: {$file['size_formatted']}\n";
}
```

## 测试连接

在开始使用前，建议先测试API连接：

```php
<?php

if ($zhaomuService->testConnection()) {
    echo "API连接正常\n";
} else {
    echo "API连接失败，请检查API密钥和网络连接\n";
}
```

## 注意事项

1. 请确保API密钥有效且有足够的权限
2. 建议在生产环境中启用SSL证书验证
3. 合理设置请求超时时间
4. 定期清理日志文件
5. 处理异常情况，提供友好的错误提示

## 相关链接

- [朝暮数据API文档](https://www.showdoc.com.cn/2072093438137669/9333513581813676)
- [Guzzle HTTP客户端文档](https://docs.guzzlephp.org/)
- [ThinkPHP框架文档](https://www.thinkphp.cn/)

## 许可证

本项目基于MIT许可证开源。
