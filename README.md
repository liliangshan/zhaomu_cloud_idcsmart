# 朝暮数据智简魔方财务插件

[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)
[![Vue](https://img.shields.io/badge/Vue-3.5.18-green.svg)](https://vuejs.org/)
[![TypeScript](https://img.shields.io/badge/TypeScript-5.9.2-blue.svg)](https://www.typescriptlang.org/)
[![Vite](https://img.shields.io/badge/Vite-7.0.6-646CFF.svg)](https://vitejs.dev/)

## 项目简介

朝暮数据智简魔方财务插件是一个专为魔方财务系统设计的云服务器管理插件，提供完整的云服务器生命周期管理功能。项目包含后端插件和开源前端两个部分，支持云服务器的订购、管理、监控等核心功能。

## 项目结构

```
zhaomu_cloud_idcsmart/
├── zhaomu_cloud/          # 魔方财务插件（后端）
│   ├── config/            # 插件配置文件
│   ├── controller/        # 控制器
│   ├── model/             # 数据模型
│   ├── services/          # 业务服务
│   ├── template/          # 模板文件
│   └── ZhaomuCloudPlugin.php
└── frontend/              # 开源前端项目
    ├── src/               # 源代码
    ├── dist/              # 构建输出
    └── package.json
```

## 功能特性

### 后端插件功能
- ✅ 完整的朝暮数据.NET API集成
- ✅ 云服务器生命周期管理（订购、启动、停止、重启、销毁）
- ✅ 云服务器配置管理（重装、密码重置、配置变更）
- ✅ 产品管理和定价
- ✅ 订单管理
- ✅ 用户管理
- ✅ 系统监控和日志记录
- ✅ 魔方财务系统无缝集成

### 前端功能
- ✅ 现代化Vue 3 + TypeScript技术栈
- ✅ 响应式设计，支持多设备访问
- ✅ 管理后台界面
- ✅ 用户控制面板
- ✅ 实时数据展示
- ✅ 完整的API集成

## 安装指南

### 第一步：安装魔方财务插件

1. **上传插件文件**
   ```bash
   # 将 zhaomu_cloud 目录上传到魔方财务插件目录
   cp -r zhaomu_cloud /path/to/魔方财务/public/plugins/addons/
   ```

2. **设置目录权限**
   ```bash
   # 确保插件目录有正确的权限
   chmod -R 755 /path/to/魔方财务/public/plugins/addons/zhaomu_cloud
   chown -R www-data:www-data /path/to/魔方财务/public/plugins/addons/zhaomu_cloud
   ```

3. **安装依赖**
   ```bash
   # 进入插件目录
   cd /path/to/魔方财务/public/plugins/addons/zhaomu_cloud
   
   # 安装PHP依赖
   composer install
   ```

4. **后台安装插件**
   - 登录魔方财务管理后台
   - 进入 `系统管理` → `插件管理`
   - 找到"朝暮数据智简魔方财务插件"
   - 点击"安装"按钮
   - 按照提示完成配置

### 第二步：配置插件

1. **API配置**
   - 在插件设置中输入朝暮数据API密钥
   - 配置API基础地址
   - 设置请求超时时间

2. **产品配置**
   - 同步朝暮数据产品信息
   - 配置产品定价策略
   - 设置可用区域

## 前端开发指南

### 环境要求

- Node.js >= 20.19.0 或 >= 22.12.0
- npm 或 yarn

### 快速开始

1. **安装依赖**
   ```bash
   cd frontend
   npm install
   ```

2. **开发环境启动**
   ```bash
   # 开发环境
   npm run dev
   
   # 生产环境测试
   npm run dev:prod
   ```

3. **构建项目**
   ```bash
   # 构建生产版本
   npm run build
   
   # 构建开发版本
   npm run build:dev
   ```

### 技术栈

- **Vue 3** - 渐进式JavaScript框架
- **TypeScript** - 类型安全的JavaScript超集
- **Vite** - 下一代前端构建工具
- **Vue Router** - 官方路由管理器
- **Element Plus** - Vue 3 UI组件库
- **Heroicons** - 现代化图标库

### 项目结构

```
frontend/
├── src/
│   ├── components/          # Vue组件
│   │   ├── panel/          # 控制面板组件
│   │   ├── ErrorModal.vue  # 错误弹窗
│   │   ├── OrderModal.vue  # 订单弹窗
│   │   └── UserOrderModal.vue
│   ├── views/              # 页面视图
│   │   ├── admin/          # 管理后台页面
│   │   ├── panel/          # 用户控制面板
│   │   └── user/           # 用户页面
│   ├── services/           # API服务
│   │   ├── api.ts         # API接口
│   │   ├── client.ts      # 客户端配置
│   │   └── panel.ts       # 控制面板服务
│   ├── config/             # 配置文件
│   │   └── currencies.ts  # 货币配置
│   ├── router/             # 路由配置
│   └── assets/             # 静态资源
├── dist/                   # 构建输出目录
├── public/                 # 公共资源
└── package.json           # 项目配置
```

### 开发说明

1. **环境变量配置**
   - 开发环境：`env.development`
   - 生产环境：`env.production`
   - 环境变量以 `VITE_` 开头

2. **API集成**
   - 已集成朝暮数据API服务
   - 支持获取可用区、产品列表等功能
   - 提供完整的错误处理机制

3. **组件开发**
   - 使用TypeScript提供类型安全
   - 支持Vue 3 Composition API
   - 集成Element Plus UI组件

### 自定义开发

有开发能力的用户可以在现有基础上进行二次开发：

1. **添加新页面**
   ```typescript
   // 在 src/views/ 目录下创建新页面
   // 在 src/router/index.ts 中添加路由配置
   ```

2. **扩展API服务**
   ```typescript
   // 在 src/services/api.ts 中添加新的API接口
   // 在 src/services/panel.ts 中添加业务逻辑
   ```

3. **自定义组件**
   ```vue
   <!-- 在 src/components/ 目录下创建新组件 -->
   <template>
     <div class="custom-component">
       <!-- 组件内容 -->
     </div>
   </template>
   ```

## API文档

### 后端API接口

| 接口 | 方法 | 说明 |
|------|------|------|
| `/admin/api/regions` | GET | 获取可用区列表 |
| `/admin/api/products` | GET | 获取产品列表 |
| `/admin/api/servers` | GET | 获取服务器列表 |
| `/admin/api/servers` | POST | 创建服务器 |
| `/admin/api/servers/{id}` | GET | 获取服务器详情 |
| `/admin/api/servers/{id}/reboot` | POST | 重启服务器 |
| `/admin/api/servers/{id}/start` | POST | 启动服务器 |
| `/admin/api/servers/{id}/stop` | POST | 停止服务器 |

### 前端API服务

```typescript
// 获取可用区列表
const regions = await zhaomuApiService.getRegions(true)

// 获取产品列表
const products = await zhaomuApiService.getProductsByRegion('576')

// 获取服务器列表
const servers = await zhaomuApiService.getServers()
```

## 配置说明

### 后端配置

在 `zhaomu_cloud/config/zhaomu_cloud.php` 中配置：

```php
return [
    'api_key' => 'your_api_key_here',
    'api_url' => 'https://api.zhaomu.net',
    'timeout' => 30,
    'verify_ssl' => true,
];
```

### 前端配置

在环境变量文件中配置：

```bash
# 开发环境
VITE_API_BASE_URL=http://127.0.0.1:8082
VITE_API_TIMEOUT=30000

# 生产环境
VITE_API_BASE_URL=https://api.zhaomu.net
VITE_API_TIMEOUT=30000
```

## 故障排除

### 常见问题

1. **插件安装失败**
   - 检查目录权限
   - 确认PHP版本兼容性
   - 查看错误日志

2. **API连接失败**
   - 验证API密钥是否正确
   - 检查网络连接
   - 确认API地址配置

3. **前端构建失败**
   - 检查Node.js版本
   - 清除node_modules重新安装
   - 查看构建日志

### 日志查看

```bash
# 查看插件日志
tail -f /path/to/魔方财务/runtime/log/zhaomu_cloud.log

# 查看API日志
tail -f /path/to/魔方财务/runtime/log/api.log
```

## 更新日志

### v1.0.0
- 初始版本发布
- 完整的云服务器管理功能
- Vue 3 + TypeScript前端
- 魔方财务系统集成

## 贡献指南

欢迎贡献代码！请遵循以下步骤：

1. Fork 本项目
2. 创建功能分支 (`git checkout -b feature/AmazingFeature`)
3. 提交更改 (`git commit -m 'Add some AmazingFeature'`)
4. 推送到分支 (`git push origin feature/AmazingFeature`)
5. 创建 Pull Request

## 许可证

本项目基于 [MIT许可证](LICENSE) 开源。

## 联系方式

- 项目主页：[GitHub Repository]
- 问题反馈：[Issues]
- 技术文档：[Documentation]

## 致谢

感谢以下开源项目的支持：

- [Vue.js](https://vuejs.org/) - 渐进式JavaScript框架
- [Element Plus](https://element-plus.org/) - Vue 3 UI组件库
- [Vite](https://vitejs.dev/) - 下一代前端构建工具
- [魔方财务](https://www.idcsmart.com/) - 财务管理系统

---

**注意**：使用本插件前请确保已获得朝暮数据的API访问权限，并正确配置相关参数。
