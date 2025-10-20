import { fileURLToPath, URL } from 'node:url'
import { defineConfig, loadEnv } from 'vite'
import vue from '@vitejs/plugin-vue'
import vueDevTools from 'vite-plugin-vue-devtools'
import fs from 'fs'
import path from 'path'

// https://vite.dev/config/
export default defineConfig(({ mode }) => {
  // 加载环境变量
  const env = loadEnv(mode, process.cwd(), '')
  
  return {
  //  base: '/plugins/addons/zhaomu_cloud/',
    plugins: [
      vue(),
      vueDevTools(),
      // 自定义插件：打包完成后提取资源文件
      {
        name: 'extract-assets',
        writeBundle() {
          try {
            // 读取 dist/index.html 文件
            const indexPath = path.resolve(process.cwd(), 'dist/index.html')
            if (!fs.existsSync(indexPath)) {
              console.log('dist/index.html 文件不存在')
              return
            }
            
            const htmlContent = fs.readFileSync(indexPath, 'utf-8')
            
            // 提取 JS 和 CSS 文件
            const jsRegex = /<script[^>]*src="([^"]*\.js)"[^>]*><\/script>/g
            const cssRegex = /<link[^>]*href="([^"]*\.css)"[^>]*>/g
            
            const jsFiles = []
            const cssFiles = []
            
            let match
            while ((match = jsRegex.exec(htmlContent)) !== null) {
              jsFiles.push(`<script type="module" crossorigin src="/plugins/addons/zhaomu_cloud${match[1]}"></script>`)
            }
            
            while ((match = cssRegex.exec(htmlContent)) !== null) {
              cssFiles.push(`<link rel="stylesheet" crossorigin href="/plugins/addons/zhaomu_cloud${match[1]}">`)
            }
            
            // 生成新的模板内容
            const templateContent = `<?php
namespace addons\\zhaomu_cloud;

class ZhaoMuTemplete
{
    public function fetch()
    {
       
        return '${cssFiles.join('')}${jsFiles.join('')}';
    }
}`

            // 写入 ZhaoMuTemplete.php 文件
            const templatePath = path.resolve(process.cwd(), '../addons/zhaomu_cloud/ZhaoMuTemplete.php')
            fs.writeFileSync(templatePath, templateContent, 'utf-8')
            
            // 处理 assets 目录
            const distAssetsPath = path.resolve(process.cwd(), 'dist/assets')
            const targetAssetsPath = path.resolve(process.cwd(), '../addons/zhaomu_cloud/assets')
            
            // 删除目标 assets 目录（如果存在）
            if (fs.existsSync(targetAssetsPath)) {
              fs.rmSync(targetAssetsPath, { recursive: true, force: true })
              console.log('🗑️ 已删除旧的 assets 目录')
            }
            
            // 复制 dist/assets 到目标目录
            if (fs.existsSync(distAssetsPath)) {
              // 确保目标目录的父目录存在
              const targetDir = path.dirname(targetAssetsPath)
              if (!fs.existsSync(targetDir)) {
                fs.mkdirSync(targetDir, { recursive: true })
              }
              
              // 复制整个 assets 目录
              fs.cpSync(distAssetsPath, targetAssetsPath, { recursive: true })
              console.log('📁 已复制 assets 目录到目标位置')
            } else {
              console.log('⚠️ dist/assets 目录不存在，跳过复制')
            }
            
            console.log('✅ 资源文件提取完成，已更新 ZhaoMuTemplete.php')
            console.log(`📦 提取到 ${jsFiles.length} 个 JS 文件和 ${cssFiles.length} 个 CSS 文件`)
            
          } catch (error) {
            console.error('❌ 提取资源文件失败:', error.message)
          }
        }
      }
    ],
    resolve: {
      alias: {
        '@': fileURLToPath(new URL('./src', import.meta.url))
      },
    },
    server: {
      port: 3000,
      open: true,
      proxy: {
        '/addons': {
          target: 'http://127.0.0.1:8082/addons',
          changeOrigin: true,
          rewrite: (path) => path.replace(/^\/addons/, '')
        }
      }
    },
    build: {
      outDir: 'dist',
      sourcemap: mode === 'development'
    }
  }
})
