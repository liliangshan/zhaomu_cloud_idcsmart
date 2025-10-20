# 朝暮云管理系统前端

基于 Vite + Vue 3 + TypeScript 构建的现代化前端项目。

## 技术栈

- **Vue 3** - 渐进式JavaScript框架
- **TypeScript** - JavaScript的超集，提供类型安全
- **Vite** - 下一代前端构建工具
- **Vue Router** - Vue.js官方路由管理器

## 环境配置

项目支持多环境配置，通过环境变量区分开发和生产环境。

### 环境文件

- `env.development` - 开发环境配置
- `env.production` - 生产环境配置

### 环境变量

| 变量名 | 说明 | 开发环境 | 生产环境 |
|--------|------|----------|----------|
| VITE_APP_TITLE | 应用标题 | 朝暮云管理系统 | 朝暮云管理系统 |
| VITE_API_BASE_URL | API基础地址 | http://127.0.0.1:8082 | https://api.zhaomu.net |
| VITE_API_TIMEOUT | 请求超时时间 | 30000 | 30000 |
| VITE_APP_ENV | 当前环境 | development | production |

## 开发命令

```bash
# 安装依赖
npm install

# 开发环境启动
npm run dev

# 生产环境启动（用于测试生产配置）
npm run dev:prod

# 构建生产版本
npm run build

# 构建开发版本
npm run build:dev

# 预览构建结果
npm run preview
```

## 项目结构

```
frontend/
├── src/
│   ├── components/          # Vue组件
│   │   └── ZhaomuCloud.vue # 朝暮云主组件
│   ├── config/             # 配置文件
│   │   └── api.ts         # API配置
│   ├── services/           # 服务层
│   │   └── api.ts         # API服务
│   ├── router/             # 路由配置
│   ├── assets/             # 静态资源
│   ├── App.vue            # 根组件
│   └── main.js            # 入口文件
├── env.development         # 开发环境配置
├── env.production          # 生产环境配置
├── tsconfig.json          # TypeScript配置
├── vite.config.js         # Vite配置
└── package.json           # 项目配置
```

## API集成

项目已集成朝暮云API服务，包括：

- 获取可用区列表
- 获取产品列表
- 缓存国家列表

### 使用示例

```typescript
import { zhaomuApiService } from '@/services/api'

// 获取可用区列表
const regions = await zhaomuApiService.getRegions(true)

// 获取产品列表
const products = await zhaomuApiService.getProductsByRegion('576')

// 缓存国家列表
const result = await zhaomuApiService.cacheCountries(['中国', '美国'])
```

## 开发说明

1. 项目使用TypeScript，提供完整的类型支持
2. 环境变量以`VITE_`开头，可在代码中通过`import.meta.env`访问
3. API请求已配置代理，开发环境下自动转发到后端服务
4. 支持热重载，修改代码后自动刷新页面

## 部署说明

1. 运行`npm run build`构建生产版本
2. 将`dist`目录部署到Web服务器
3. 确保环境变量配置正确