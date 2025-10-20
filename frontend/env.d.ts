/// <reference types="vite/client" />

interface ImportMetaEnv {
  readonly VITE_APP_TITLE: string
  readonly VITE_API_BASE_URL: string
  readonly VITE_API_TIMEOUT: string
  readonly VITE_APP_ENV: 'development' | 'production' | 'test'
}

interface ImportMeta {
  readonly env: ImportMetaEnv
}

// 全局window变量类型声明
declare global {
  interface Window {
    APP_CONFIG: {
      isAdmin: boolean
      version: string
      environment: string
    }
  }
}

export {}
