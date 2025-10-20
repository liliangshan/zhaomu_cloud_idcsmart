<template>
  <div v-if="machineInfo.cloud_server_info" class="hlwidc-info-card">
    <div class="hlwidc-card-header">
      <h2 class="hlwidc-card-title">服务器配置</h2>
      <button @click="$emit('showRebuildModal')" class="hlwidc-rebuild-btn" :disabled="shouldDisableButtons" title="重装系统">
        <svg class="hlwidc-btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
        </svg>
        重装系统
      </button>
    </div>
    <div class="hlwidc-config-grid">
      <div class="hlwidc-config-item">
        <div class="hlwidc-config-icon">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path>
          </svg>
        </div>
        <div class="hlwidc-config-details">
          <h4>CPU</h4>
          <p>{{ machineInfo.cloud_server_info.cpu }} 核</p>
        </div>
      </div>
      <div class="hlwidc-config-item">
        <div class="hlwidc-config-icon">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"></path>
          </svg>
        </div>
        <div class="hlwidc-config-details">
          <h4>内存</h4>
          <p>{{ formatMemory(machineInfo.cloud_server_info.ram) }}</p>
        </div>
      </div>
      <div class="hlwidc-config-item">
        <div class="hlwidc-config-icon">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"></path>
          </svg>
        </div>
        <div class="hlwidc-config-details">
          <h4>系统盘</h4>
          <p>{{ machineInfo.cloud_server_info.disk }} GB {{ machineInfo.cloud_server_info.diskMedia }}</p>
        </div>
      </div>
      <div class="hlwidc-config-item">
        <div class="hlwidc-config-icon">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
          </svg>
        </div>
        <div class="hlwidc-config-details">
          <h4>数据盘</h4>
          <p>{{ machineInfo.cloud_server_info.diskData }} GB</p>
        </div>
      </div>
      <div class="hlwidc-config-item">
        <div class="hlwidc-config-icon">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9v-9m0-9v9"></path>
          </svg>
        </div>
        <div class="hlwidc-config-details">
          <h4>流量</h4>
          <p>{{ machineInfo.cloud_server_info.traffic }} GB/月</p>
        </div>
      </div>
      <div class="hlwidc-config-item">
        <div class="hlwidc-config-icon">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
          </svg>
        </div>
        <div class="hlwidc-config-details">
          <h4>操作系统</h4>
          <p>{{ machineInfo.cloud_server_info.image }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { MachineInfo } from '@/services/panel'

defineProps<{
  machineInfo: MachineInfo
  shouldDisableButtons: boolean
}>()

defineEmits<{
  showRebuildModal: []
}>()

// 格式化内存
const formatMemory = (ram: number) => {
  if (ram >= 1024) {
    return `${(ram / 1024).toFixed(1)} GB`
  }
  return `${ram} MB`
}
</script>

<style scoped>
/* 信息卡片 */
.hlwidc-info-card {
  background: white;
  border-radius: 12px;
  padding: 24px;
  margin-bottom: 24px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  border: 1px solid #e5e7eb;
}

/* 卡片头部 */
.hlwidc-card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
  padding-bottom: 12px;
  border-bottom: 2px solid #f3f4f6;
}

.hlwidc-card-title {
  font-size: 20px;
  font-weight: 600;
  color: #1f2937;
  margin: 0;
}

/* 重装系统按钮 */
.hlwidc-rebuild-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 20px;
  background: linear-gradient(135deg, #f59e0b, #d97706);
  color: white;
  border: none;
  border-radius: 12px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
  position: relative;
  overflow: hidden;
}

.hlwidc-rebuild-btn::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
  transition: left 0.5s;
}

.hlwidc-rebuild-btn:hover::before {
  left: 100%;
}

.hlwidc-rebuild-btn:hover {
  background: linear-gradient(135deg, #d97706, #b45309);
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(245, 158, 11, 0.4);
}

.hlwidc-rebuild-btn:active {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
}

.hlwidc-rebuild-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
  background: #e5e7eb !important;
  color: #9ca3af !important;
  transform: none !important;
  box-shadow: none !important;
}

.hlwidc-btn-icon {
  width: 18px;
  height: 18px;
  filter: drop-shadow(0 1px 2px rgba(0, 0, 0, 0.1));
}

/* 配置网格样式 */
.hlwidc-config-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 20px;
  margin-top: 20px;
}

.hlwidc-config-item {
  display: flex;
  align-items: center;
  padding: 20px;
  background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
  border-radius: 12px;
  border: 1px solid #e2e8f0;
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
}

.hlwidc-config-item::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 3px;
  background: linear-gradient(90deg, #3b82f6, #8b5cf6, #06b6d4);
  opacity: 0;
  transition: opacity 0.3s ease;
}

.hlwidc-config-item:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
  border-color: #3b82f6;
}

.hlwidc-config-item:hover::before {
  opacity: 1;
}

.hlwidc-config-icon {
  width: 48px;
  height: 48px;
  background: linear-gradient(135deg, #3b82f6, #8b5cf6);
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 16px;
  flex-shrink: 0;
  transition: all 0.3s ease;
}

.hlwidc-config-icon svg {
  width: 24px;
  height: 24px;
  color: white;
  transition: all 0.3s ease;
}

.hlwidc-config-item:hover .hlwidc-config-icon {
  transform: scale(1.1);
  box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
}

.hlwidc-config-details h4 {
  margin: 0 0 8px 0;
  font-size: 16px;
  font-weight: 600;
  color: #1e293b;
  line-height: 1.4;
}

.hlwidc-config-details p {
  margin: 0;
  font-size: 14px;
  color: #64748b;
  font-weight: 500;
  line-height: 1.4;
}

/* 响应式设计 */
@media (max-width: 768px) {
  .hlwidc-card-header {
    flex-direction: column;
    gap: 16px;
    align-items: flex-start;
  }
  
  .hlwidc-rebuild-btn {
    padding: 8px 16px;
    font-size: 13px;
  }
  
  .hlwidc-config-grid {
    grid-template-columns: 1fr;
    gap: 16px;
  }
  
  .hlwidc-config-item {
    padding: 16px;
  }
  
  .hlwidc-config-icon {
    width: 40px;
    height: 40px;
    margin-right: 12px;
  }
  
  .hlwidc-config-icon svg {
    width: 20px;
    height: 20px;
  }
}
</style>
