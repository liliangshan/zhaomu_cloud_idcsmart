<template>
  <div v-if="machineInfo.cloud_server_info" class="hlwidc-info-card">
    <div class="hlwidc-card-header">
      <h2 class="hlwidc-card-title">服务器信息</h2>
      <button @click="$emit('showPasswordModal')" class="hlwidc-password-btn" :disabled="shouldDisableButtons" title="修改密码">
        <svg class="hlwidc-btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-3.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
        </svg>
        修改密码
      </button>
    </div>
    <div class="hlwidc-info-grid">
      <div class="hlwidc-info-item">
        <label>IP地址</label>
        <div class="hlwidc-copy-field">
          <span class="hlwidc-ip-address">{{ machineInfo.cloud_server_info.ip }}</span>
          <button @click="copyToClipboard(machineInfo.cloud_server_info.ip)" class="hlwidc-copy-btn" title="复制IP地址">
            <svg class="hlwidc-copy-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
            </svg>
          </button>
        </div>
      </div>
      <div class="hlwidc-info-item">
        <label>IPv6地址</label>
        <div class="hlwidc-copy-field">
          <span class="hlwidc-ipv6-address">{{ machineInfo.cloud_server_info.ipv6 }}</span>
          <button @click="copyToClipboard(machineInfo.cloud_server_info.ipv6)" class="hlwidc-copy-btn" title="复制IPv6地址">
            <svg class="hlwidc-copy-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
            </svg>
          </button>
        </div>
      </div>
      <div class="hlwidc-info-item">
        <label>SSH端口</label>
        <div class="hlwidc-copy-field">
          <span>{{ machineInfo.cloud_server_info.port }}</span>
          <button @click="copyToClipboard(machineInfo.cloud_server_info.port.toString())" class="hlwidc-copy-btn" title="复制SSH端口">
            <svg class="hlwidc-copy-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
            </svg>
          </button>
        </div>
      </div>
      <div class="hlwidc-info-item">
        <label>用户名</label>
        <div class="hlwidc-copy-field">
          <span>{{ machineInfo.cloud_server_info.root }}</span>
          <button @click="copyToClipboard(machineInfo.cloud_server_info.root)" class="hlwidc-copy-btn" title="复制用户名">
            <svg class="hlwidc-copy-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
            </svg>
          </button>
        </div>
      </div>
      <div class="hlwidc-info-item">
        <label>密码</label>
        <div class="hlwidc-password-field">
          <span v-if="showPassword">{{ machineInfo.cloud_server_info.password }}</span>
          <span v-else>••••••••••••••••</span>
          <div class="hlwidc-password-actions">
            <button @click="togglePassword" class="hlwidc-toggle-password-btn">
              {{ showPassword ? '隐藏' : '显示' }}
            </button>
            <button @click="copyToClipboard(machineInfo.cloud_server_info.password)" class="hlwidc-copy-btn" title="复制密码">
              <svg class="hlwidc-copy-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
              </svg>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Toast 提示 -->
  <div v-if="showToast" class="hlwidc-toast">
    <div class="hlwidc-toast-content">
      <svg class="hlwidc-toast-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
      </svg>
      <span>{{ toastMessage }}</span>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import type { MachineInfo } from '@/services/panel'

defineProps<{
  machineInfo: MachineInfo
  shouldDisableButtons: boolean
}>()

defineEmits<{
  showPasswordModal: []
}>()

const showPassword = ref(false)
const showToast = ref(false)
const toastMessage = ref('')

// 切换密码显示
const togglePassword = () => {
  showPassword.value = !showPassword.value
}

// 显示 Toast 提示
const showToastMessage = (message: string) => {
  toastMessage.value = message
  showToast.value = true
  setTimeout(() => {
    showToast.value = false
  }, 2000)
}

// 复制到剪贴板
const copyToClipboard = async (text: string) => {
  try {
    await navigator.clipboard.writeText(text)
    showToastMessage('已复制到剪贴板')
  } catch (err) {
    console.error('复制失败:', err)
    // 降级方案：使用传统的复制方法
    const textArea = document.createElement('textarea')
    textArea.value = text
    document.body.appendChild(textArea)
    textArea.select()
    try {
      document.execCommand('copy')
      showToastMessage('已复制到剪贴板')
    } catch (fallbackErr) {
      console.error('降级复制也失败:', fallbackErr)
      showToastMessage('复制失败，请手动复制')
    }
    document.body.removeChild(textArea)
  }
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

/* 密码修改按钮 */
.hlwidc-password-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 20px;
  background: linear-gradient(135deg, #8b5cf6, #7c3aed);
  color: white;
  border: none;
  border-radius: 12px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);
  position: relative;
  overflow: hidden;
}

.hlwidc-password-btn::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
  transition: left 0.5s;
}

.hlwidc-password-btn:hover::before {
  left: 100%;
}

.hlwidc-password-btn:hover {
  background: linear-gradient(135deg, #7c3aed, #6d28d9);
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(139, 92, 246, 0.4);
}

.hlwidc-password-btn:active {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);
}

.hlwidc-password-btn:disabled {
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

/* 信息网格 */
.hlwidc-info-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 16px;
}

.hlwidc-info-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 0;
  border-bottom: 1px solid #f3f4f6;
}

.hlwidc-info-item:last-child {
  border-bottom: none;
}

.hlwidc-info-item label {
  font-weight: 500;
  color: #6b7280;
  font-size: 14px;
}

.hlwidc-info-item span {
  color: #1f2937;
  font-weight: 500;
}

/* 复制字段容器 */
.hlwidc-copy-field {
  display: flex;
  align-items: center;
  gap: 8px;
}

/* 复制按钮 */
.hlwidc-copy-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 32px;
  height: 32px;
  background-color: #f3f4f6;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.2s ease;
  color: #6b7280;
}

.hlwidc-copy-btn:hover {
  background-color: #e5e7eb;
  border-color: #9ca3af;
  color: #374151;
}

.hlwidc-copy-btn:active {
  background-color: #d1d5db;
  transform: scale(0.95);
}

.hlwidc-copy-icon {
  width: 16px;
  height: 16px;
}

/* 密码字段操作按钮 */
.hlwidc-password-actions {
  display: flex;
  align-items: center;
  gap: 8px;
}

/* Toast 提示 */
.hlwidc-toast {
  position: fixed;
  top: 20px;
  right: 20px;
  z-index: 100000;
  animation: hlwidc-toast-slide-in 0.3s ease-out;
}

.hlwidc-toast-content {
  display: flex;
  align-items: center;
  gap: 8px;
  background-color: #10b981;
  color: white;
  padding: 12px 16px;
  border-radius: 8px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
  font-size: 14px;
  font-weight: 500;
  min-width: 200px;
}

.hlwidc-toast-icon {
  width: 18px;
  height: 18px;
  flex-shrink: 0;
}

@keyframes hlwidc-toast-slide-in {
  from {
    transform: translateX(100%);
    opacity: 0;
  }
  to {
    transform: translateX(0);
    opacity: 1;
  }
}

.hlwidc-ip-address {
  font-family: monospace;
  background-color: #eff6ff;
  padding: 4px 8px;
  border-radius: 4px;
  color: #1e40af;
}

.hlwidc-ipv6-address {
  font-family: monospace;
  background-color: #f0fdf4;
  padding: 4px 8px;
  border-radius: 4px;
  color: #166534;
  font-size: 12px;
}

.hlwidc-password-field {
  display: flex;
  align-items: center;
  gap: 8px;
}

.hlwidc-toggle-password-btn {
  background-color: #6b7280;
  color: white;
  border: none;
  padding: 4px 8px;
  border-radius: 4px;
  cursor: pointer;
  font-size: 12px;
}

.hlwidc-toggle-password-btn:hover {
  background-color: #4b5563;
}

/* 响应式设计 */
@media (max-width: 768px) {
  .hlwidc-card-header {
    flex-direction: column;
    gap: 16px;
    align-items: flex-start;
  }
  
  .hlwidc-password-btn {
    padding: 8px 16px;
    font-size: 13px;
  }
  
  .hlwidc-info-grid {
    grid-template-columns: 1fr;
  }
}
</style>
