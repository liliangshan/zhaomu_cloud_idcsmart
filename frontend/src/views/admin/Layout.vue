<template>
  <div class="hlwidc-admin-layout">
    <!-- 顶部菜单栏 -->
    <header class="hlwidc-admin-header">
      <div class="hlwidc-header-container">
        <div class="hlwidc-logo">
          <h2>管理后台</h2>
        </div>
        <nav class="hlwidc-top-menu">
          <ul class="hlwidc-menu-list">
           
            <li v-if="keyStatus === 2" class="hlwidc-menu-item">
              <router-link to="/buy" class="hlwidc-menu-link">
                <CubeIcon class="hlwidc-menu-icon" />
                产品订购
              </router-link>
            </li>
            <li class="hlwidc-menu-item">
              <router-link to="/orders" class="hlwidc-menu-link">
                <ShoppingCartIcon class="hlwidc-menu-icon" />
                订单管理
              </router-link>
            </li>
            <li class="hlwidc-menu-item">
              <router-link to="/introduction" class="hlwidc-menu-link">
                <DocumentTextIcon class="hlwidc-menu-icon" />
                集成指南
              </router-link>
            </li>
            <li class="hlwidc-menu-item">
              <router-link to="/settings" class="hlwidc-menu-link">
                <Cog6ToothIcon class="hlwidc-menu-icon" />
                系统设置
              </router-link>
            </li>
          </ul>
        </nav>
      </div>
    </header>

    <!-- 加载状态 -->
    <div v-if="keyStatus === 1" class="hlwidc-loading-section">
      <div class="hlwidc-loading-container">
        <div class="hlwidc-loading-spinner"></div>
        <p class="hlwidc-loading-text">正在检查 API Key 状态...</p>
      </div>
    </div>

    <!-- API Key 输入区域 -->
    <div v-if="keyStatus === 3" class="hlwidc-key-input-section">
      <div class="hlwidc-key-input-container">
        <div class="hlwidc-key-input-header">
          <KeyIcon class="hlwidc-key-icon" />
          <h3>请输入朝暮云 API Key</h3>
        </div>
        <div class="hlwidc-key-input-body">
          <p class="hlwidc-key-input-desc">系统检测到您还没有配置朝暮云 API Key，请先输入您的 API Key 以继续使用。</p>
          <div class="hlwidc-key-input-tip">
            <p class="hlwidc-tip-text">如果您还没有 API Key，请 <a href="https://www.zhaomu.com/" target="_blank" class="hlwidc-tip-link">点此联系朝暮云官网客服</a> 开通服务。</p>
          </div>
          <div class="hlwidc-key-input-form">
            <input 
              v-model="apiKey"
              type="text" 
              placeholder="请输入您的 API Key"
              class="hlwidc-key-input-field"
              :disabled="loading"
            />
            <button 
              @click="saveApiKey" 
              :disabled="loading || !apiKey.trim()"
              class="hlwidc-key-input-btn"
            >
              {{ loading ? '保存中...' : '保存' }}
            </button>
          </div>
          <div v-if="error" class="hlwidc-key-input-error">
            {{ error }}
          </div>
        </div>
      </div>
    </div>

    <!-- 主要内容区域 -->
    <main v-if="keyStatus === 2" class="hlwidc-admin-main">
      <div class="hlwidc-content-container">
        <RouterView />
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { RouterView } from 'vue-router'
import {
  CubeIcon,
  ShoppingCartIcon,
  Cog6ToothIcon,
  KeyIcon,
  DocumentTextIcon
} from '@heroicons/vue/24/outline'
import { zhaomuApiService } from '@/services/api'

// 响应式数据
// 状态说明: 1=等待载入数据, 2=有key, 3=无key
const keyStatus = ref(1)
const apiKey = ref('')
const loading = ref(false)
const error = ref<string | null>(null)

// 检查缓存键
const checkCacheKey = async () => {
  try {
    loading.value = true
    error.value = null
    
    const response = await zhaomuApiService.checkCacheKey()
    console.log('检查缓存键响应:', response)
    
    if (response && response.code === 1 && response.data) {
      if (response.data.exists) {
        keyStatus.value = 2 // 有key
        console.log('缓存键已存在')
      } else {
        keyStatus.value = 3 // 无key
        console.log('缓存键不存在，需要输入')
      }
    } else {
      keyStatus.value = 3 // 无key
    }
  } catch (err) {
    console.error('检查缓存键错误:', err)
    error.value = err instanceof Error ? err.message : '检查缓存键失败'
    keyStatus.value = 3 // 无key
  } finally {
    loading.value = false
  }
}

// 保存 API Key
const saveApiKey = async () => {
  if (!apiKey.value.trim()) {
    error.value = '请输入 API Key'
    return
  }
  
  try {
    loading.value = true
    error.value = null
    
    // 调用后端 API 保存
    const response = await zhaomuApiService.saveApiKey(apiKey.value)
    
    if (response.code === 1) {
      // 保存成功，设置为有key状态
      keyStatus.value = 2
      console.log('API Key 保存成功:', response.msg)
    } else {
      error.value = response.msg || '保存失败，请重试'
    }
    
  } catch (err) {
    console.error('保存 API Key 失败:', err)
    error.value = '网络错误，请检查连接后重试'
  } finally {
    loading.value = false
  }
}

// 页面加载时检查缓存键
onMounted(() => {
  checkCacheKey()
})
</script>

<style scoped>

.hlwidc-admin-layout button,.hlwidc-admin-layout button>*{
  font-size: 1rem!important;
}

.hlwidc-admin-layout {
  min-height: 100vh;
  background-color: #f5f5f5;
}

.hlwidc-admin-header {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  position: sticky;
  top: 0;
  z-index: 1000;
}

.hlwidc-header-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  height: 60px;
}

.hlwidc-logo h2 {
  margin: 0;
  font-size: 1.5rem;
  font-weight: 600;
}

.hlwidc-top-menu {
  flex: 1;
  margin: 0 40px;
}

.hlwidc-menu-list {
  display: flex;
  list-style: none;
  margin: 0;
  padding: 0;
  gap: 20px;
}

.hlwidc-menu-item {
  position: relative;
}

.hlwidc-menu-link {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 16px;
  color: white;
  text-decoration: none;
  border-radius: 8px;
  transition: all 0.3s ease;
  font-weight: 500;
}

.hlwidc-menu-link:hover {
  background-color: rgba(255, 255, 255, 0.1);
  transform: translateY(-1px);
}

.hlwidc-menu-link.router-link-active {
  background-color: rgba(255, 255, 255, 0.2);
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.hlwidc-menu-icon {
  width: 20px;
  height: 20px;
}

.hlwidc-user-info {
  display: flex;
  align-items: center;
  gap: 15px;
}

.hlwidc-username {
  font-weight: 500;
  color: rgba(255, 255, 255, 0.9);
}

.hlwidc-logout-btn {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 8px 12px;
  background-color: rgba(255, 255, 255, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.2);
  color: white;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.3s ease;
  font-size: 14px;
}

.hlwidc-logout-btn:hover {
  background-color: rgba(255, 255, 255, 0.2);
  transform: translateY(-1px);
}

.hlwidc-logout-icon {
  width: 16px;
  height: 16px;
}

.hlwidc-admin-main {
  padding: 20px;
}

.hlwidc-content-container {
  max-width: 1200px;
  margin: 0 auto;
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 20px rgba(0, 0, 0, 0.05);
  overflow: hidden;
}

/* 响应式设计 */
@media (max-width: 768px) {
  .hlwidc-header-container {
    flex-direction: column;
    height: auto;
    padding: 15px 20px;
  }
  
  .hlwidc-top-menu {
    margin: 15px 0;
    width: 100%;
  }
  
  .hlwidc-menu-list {
    flex-wrap: wrap;
    justify-content: center;
    gap: 10px;
  }
  
  .hlwidc-menu-link {
    padding: 8px 12px;
    font-size: 14px;
  }
  
  .hlwidc-user-info {
    margin-top: 10px;
  }
}

@media (max-width: 480px) {
  .hlwidc-menu-list {
    flex-direction: column;
    width: 100%;
  }
  
  .hlwidc-menu-item {
    width: 100%;
  }
  
  .hlwidc-menu-link {
    justify-content: center;
  }
}

/* 加载状态样式 */
.hlwidc-loading-section {
  background: #f8fafc;
  border-bottom: 1px solid #e2e8f0;
  padding: 60px 20px;
}

.hlwidc-loading-container {
  max-width: 400px;
  margin: 0 auto;
  text-align: center;
}

.hlwidc-loading-spinner {
  width: 40px;
  height: 40px;
  border: 4px solid #e2e8f0;
  border-top: 4px solid #667eea;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto 20px auto;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.hlwidc-loading-text {
  margin: 0;
  color: #6b7280;
  font-size: 16px;
  font-weight: 500;
}

/* API Key 输入区域样式 */
.hlwidc-key-input-section {
  background: #f8fafc;
  border-bottom: 1px solid #e2e8f0;
  padding: 40px 20px;
}

.hlwidc-key-input-container {
  max-width: 600px;
  margin: 0 auto;
  background: white;
  border-radius: 12px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
  border: 1px solid #e2e8f0;
}

.hlwidc-key-input-header {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 24px 24px 16px 24px;
  border-bottom: 1px solid #e5e7eb;
}

.hlwidc-key-icon {
  width: 24px;
  height: 24px;
  color: #667eea;
}

.hlwidc-key-input-header h3 {
  margin: 0;
  font-size: 1.25rem;
  color: #1f2937;
  font-weight: 600;
}

.hlwidc-key-input-body {
  padding: 24px;
}

.hlwidc-key-input-desc {
  margin: 0 0 20px 0;
  color: #6b7280;
  line-height: 1.5;
}

.hlwidc-key-input-tip {
  margin: 0 0 20px 0;
  padding: 12px 16px;
  background: #f0f9ff;
  border: 1px solid #bae6fd;
  border-radius: 8px;
}

.hlwidc-tip-text {
  margin: 0;
  color: #0369a1;
  font-size: 14px;
  line-height: 1.5;
}

.hlwidc-tip-link {
  color: #dc2626;
  text-decoration: none;
  font-weight: 600;
  background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
  padding: 4px 8px;
  border-radius: 6px;
  border: 1px solid #fecaca;
  transition: all 0.3s ease;
  display: inline-block;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.hlwidc-tip-link:hover {
  color: #b91c1c;
  background: linear-gradient(135deg, #fecaca 0%, #fca5a5 100%);
  transform: translateY(-1px);
  box-shadow: 0 4px 8px rgba(220, 38, 38, 0.2);
  text-decoration: none;
}

.hlwidc-key-input-form {
  display: flex;
  gap: 16px;
  align-items: flex-end;
}

.hlwidc-key-input-form .hlwidc-key-input-field {
  flex: 1;
}

.hlwidc-key-input-field {
  width: 100%;
  padding: 12px 16px;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 14px;
  transition: all 0.3s ease;
}

.hlwidc-key-input-field:focus {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.hlwidc-key-input-field:disabled {
  background-color: #f9fafb;
  cursor: not-allowed;
}

.hlwidc-key-input-btn {
  padding: 12px 24px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border: none;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
}

.hlwidc-key-input-btn:hover:not(:disabled) {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.hlwidc-key-input-btn:disabled {
  background: #9ca3af;
  cursor: not-allowed;
  transform: none;
  box-shadow: none;
}

.hlwidc-key-input-error {
  margin-top: 12px;
  padding: 12px;
  background: #fef2f2;
  border: 1px solid #fecaca;
  border-radius: 6px;
  color: #dc2626;
  font-size: 14px;
}

/* 响应式设计 */
@media (max-width: 768px) {
  .hlwidc-key-input-section {
    padding: 20px 15px;
  }
  
  .hlwidc-key-input-form {
    flex-direction: column;
    align-items: stretch;
  }
  
  .hlwidc-key-input-form .hlwidc-key-input-field {
    flex: none;
  }
}
</style>
