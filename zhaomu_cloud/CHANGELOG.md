# 更新日志

## v1.0.0 (2024-01-XX)

### 新增功能
- ✅ 创建了完整的ZhaoMuCloudService.php服务类
- ✅ 支持朝暮数据.NET API的所有接口
- ✅ 基于Guzzle HTTP客户端，提供现代化的HTTP请求处理
- ✅ 完善的错误处理和日志记录功能
- ✅ 针对.NET API优化的请求头配置
- ✅ 支持配置文件管理
- ✅ 提供详细的使用示例和文档

### 技术特性
- **API支持**: 完整的朝暮数据.NET API接口支持
- **HTTP客户端**: 基于Guzzle HTTP 7.x
- **错误处理**: 完善的异常处理和错误日志记录
- **日志系统**: 集成LogService，支持多级别日志记录
- **配置管理**: 支持通过配置文件进行详细配置
- **兼容性**: 专门针对.NET Web API进行优化

### 文件结构
```
addons/zhaomu_cloud/
├── services/
│   ├── ZhaoMuCloudService.php    # 主要服务类
│   └── LogService.php            # 日志服务类
├── config/
│   └── zhaomu_cloud.php          # 配置文件
├── examples/
│   └── ZhaoMuCloudService_usage.php  # 使用示例
├── test_api.php                  # API测试文件
├── README.md                     # 详细文档
├── CHANGELOG.md                  # 更新日志
└── ZhaomuCloudPlugin.php         # 插件主类
```

### API接口支持
- ✅ 获取可用区列表 (`getRegions($grouped)`) - 支持按层级分组
- ✅ 获取云服务器列表 (`getServers()`)
- ✅ 获取云服务器产品信息 (`getProducts()`)
- ✅ 获取某个可用区下的云服务器产品列表 (`getProductsByRegion()`)
- ✅ 订购云服务器 (`createServer()`)
- ✅ 获取云服务器详情 (`getServer()`)
- ✅ 云服务器管理 (重启、开机、关机、销毁)
- ✅ 云服务器操作 (重装、重置密码、续费、变更)
- ✅ 获取镜像列表 (`getImages()`)
- ✅ 获取功能参数比较 (`getFeatureComparison()`)
- ✅ 获取云服务器控制台 (`getServerConsole()`)
- ✅ 获取变更价格 (`getResizePrice()`)

### 配置特性
- **API配置**: 基础URL、超时时间、SSL验证
- **.NET API配置**: 专门的请求头配置
- **日志配置**: 日志级别、文件路径、轮转设置
- **缓存配置**: 缓存开关、TTL设置
- **重试配置**: 重试次数、延迟设置
- **限流配置**: 请求频率限制
- **调试配置**: 调试模式、请求/响应日志

### 使用示例
```php
<?php
use addons\zhaomu_cloud\services\ZhaoMuCloudService;

// 创建服务实例
$zhaomuService = new ZhaoMuCloudService('your_api_key_here');

// 获取可用区列表
$regions = $zhaomuService->getRegions();

// 订购云服务器
$result = $zhaomuService->createServer([
    'product_id' => 1,
    'region_id' => 3,
    'image_id' => 1,
    'hostname' => 'my-server',
    'password' => 'secure_password'
]);
```

### 依赖要求
- PHP >= 8.0
- Guzzle HTTP >= 7.10
- ThinkPHP >= 8.0

### 安装说明
1. 安装Guzzle HTTP客户端：`composer require guzzlehttp/guzzle`
2. 配置API密钥
3. 运行测试：`php test_api.php`

### 文档
- [README.md](README.md) - 详细使用文档
- [API文档](https://www.showdoc.com.cn/2072093438137669/9333513581813676) - 朝暮数据API文档
- [使用示例](examples/ZhaoMuCloudService_usage.php) - 完整使用示例

---

**注意**: 此版本专门针对朝暮数据的.NET API进行了优化，确保与.NET Web API的完全兼容性。
