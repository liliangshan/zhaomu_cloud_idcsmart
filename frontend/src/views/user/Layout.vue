<template>
  <div class="hlwidc-admin-layout">
   

    <!-- 加载状态 -->
    <div v-if="keyStatus === 1" class="hlwidc-loading-section">
      <div class="hlwidc-loading-container">
        <div class="hlwidc-loading-spinner"></div>
        
      </div>
    </div>

    <!-- 未开通服务提示 -->
    <div v-if="keyStatus === 3" class="hlwidc-service-notice">
      <div class="hlwidc-notice-container">
        <div class="hlwidc-notice-icon">⚠️</div>
        <h3 class="hlwidc-notice-title">当前未开通服务</h3>
        <p class="hlwidc-notice-desc">请联系客服解决</p>
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
  Cog6ToothIcon
} from '@heroicons/vue/24/outline'
import { zhaomuApiService } from '@/services/client'

// 响应式数据
// 状态说明: 1=等待载入数据, 2=有key, 3=无key
const keyStatus = ref(1)
const loading = ref(false)

// 检查缓存键
const checkCacheKey = async () => {
  try {
    loading.value = true
    
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
    keyStatus.value = 3 // 无key
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

/* 未开通服务提示样式 */
.hlwidc-service-notice {
  background: #f8fafc;
  border-bottom: 1px solid #e2e8f0;
  padding: 80px 20px;
}

.hlwidc-notice-container {
  max-width: 500px;
  margin: 0 auto;
  text-align: center;
  background: white;
  border-radius: 12px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
  border: 1px solid #e2e8f0;
  padding: 40px 30px;
}

.hlwidc-notice-icon {
  font-size: 48px;
  margin-bottom: 20px;
}

.hlwidc-notice-title {
  margin: 0 0 12px 0;
  font-size: 1.5rem;
  color: #dc2626;
  font-weight: 600;
}

.hlwidc-notice-desc {
  margin: 0;
  color: #6b7280;
  font-size: 16px;
  line-height: 1.5;
}

/* 响应式设计 */
@media (max-width: 768px) {
  .hlwidc-service-notice {
    padding: 40px 15px;
  }
  
  .hlwidc-notice-container {
    padding: 30px 20px;
  }
  
  .hlwidc-notice-title {
    font-size: 1.25rem;
  }
}
</style>
