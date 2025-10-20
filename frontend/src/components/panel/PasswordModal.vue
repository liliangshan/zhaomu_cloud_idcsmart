<template>
  <div v-if="show" class="hlwidc-modal-overlay" @click="closeModal">
    <div class="hlwidc-modal-content hlwidc-password-modal" @click.stop>
      <div class="hlwidc-modal-header">
        <h3 class="hlwidc-modal-title">修改服务器密码</h3>
        <button @click="closeModal" class="hlwidc-modal-close">×</button>
      </div>
      <div class="hlwidc-modal-body">
        <div class="hlwidc-password-form">
          <div class="hlwidc-form-group">
            <label for="newPassword">新密码</label>
            <div class="hlwidc-password-input-group">
              <div class="hlwidc-password-input-wrapper">
                <input 
                  v-model="newPassword" 
                  :type="showNewPassword ? 'text' : 'password'" 
                  id="newPassword"
                  class="hlwidc-password-input"
                  placeholder="请输入新密码"
                  :disabled="loading"
                />
                <button 
                  @click="togglePasswordVisibility" 
                  type="button"
                  class="hlwidc-password-toggle-btn"
                  :disabled="loading"
                  :title="showNewPassword ? '隐藏密码' : '显示密码'"
                >
                  <svg v-if="showNewPassword" class="hlwidc-toggle-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
                  </svg>
                  <svg v-else class="hlwidc-toggle-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                  </svg>
                </button>
              </div>
              <button @click="generatePassword" class="hlwidc-generate-btn" :disabled="loading">
                <svg class="hlwidc-generate-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                随机生成
              </button>
            </div>
          </div>
          <div class="hlwidc-password-tips">
            <p>密码要求：8-20位，包含字母和数字</p>
          </div>
        </div>
      </div>
      <div class="hlwidc-modal-footer">
        <button @click="closeModal" class="hlwidc-modal-btn hlwidc-cancel-btn">
          取消
        </button>
        <button @click="confirmPasswordChange" class="hlwidc-modal-btn hlwidc-confirm-btn" :disabled="loading || !newPassword">
          {{ loading ? '修改中...' : '确定修改' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'

const props = defineProps<{
  show: boolean
  loading: boolean
}>()

const emit = defineEmits<{
  close: []
  confirm: [password: string]
}>()

const newPassword = ref('')
const showNewPassword = ref(false)

// 切换密码可见性
const togglePasswordVisibility = () => {
  showNewPassword.value = !showNewPassword.value
}

// 生成随机密码
const generatePassword = () => {
  const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*'
  let password = ''
  
  // 确保至少包含一个大写字母、一个小写字母和一个数字
  password += 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'[Math.floor(Math.random() * 26)]
  password += 'abcdefghijklmnopqrstuvwxyz'[Math.floor(Math.random() * 26)]
  password += '0123456789'[Math.floor(Math.random() * 10)]
  
  // 生成剩余字符
  for (let i = 3; i < 12; i++) {
    password += chars[Math.floor(Math.random() * chars.length)]
  }
  
  // 打乱密码顺序
  newPassword.value = password.split('').sort(() => Math.random() - 0.5).join('')
}

// 确认修改密码
const confirmPasswordChange = () => {
  if (!newPassword.value) {
    return
  }
  
  if (newPassword.value.length < 8 || newPassword.value.length > 20) {
    return
  }
  
  emit('confirm', newPassword.value)
}

const closeModal = () => {
  newPassword.value = ''
  showNewPassword.value = false
  emit('close')
}
</script>

<style scoped>
/* 弹窗样式 */
.hlwidc-modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 99999;
  padding: 20px;
}

.hlwidc-modal-content {
  background: white;
  border-radius: 12px;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
  max-width: 400px;
  width: 100%;
  max-height: 90vh;
  overflow: hidden;
  animation: modalSlideIn 0.3s ease-out;
  position: relative;
  z-index: 100000;
}

@keyframes modalSlideIn {
  from {
    opacity: 0;
    transform: scale(0.9) translateY(-20px);
  }
  to {
    opacity: 1;
    transform: scale(1) translateY(0);
  }
}

/* 密码修改弹窗样式 */
.hlwidc-password-modal {
  max-width: 480px;
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
}

.hlwidc-password-modal .hlwidc-modal-header {
  background: linear-gradient(135deg, #8b5cf6, #7c3aed);
  color: white;
  border-bottom: none;
  padding: 24px 28px 20px;
}

.hlwidc-password-modal .hlwidc-modal-title {
  color: white;
  font-size: 20px;
  font-weight: 700;
  display: flex;
  align-items: center;
  gap: 10px;
}

.hlwidc-password-modal .hlwidc-modal-title::before {
  content: '';
  display: inline-block;
  width: 24px;
  height: 24px;
  background-image: url("data:image/svg+xml,%3Csvg fill='none' stroke='white' viewBox='0 0 24 24' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z'%3E%3C/path%3E%3C/svg%3E");
  background-size: contain;
  background-repeat: no-repeat;
  filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.2));
}

.hlwidc-password-modal .hlwidc-modal-close {
  color: white;
  background: rgba(255, 255, 255, 0.1);
  border-radius: 8px;
  width: 36px;
  height: 36px;
  font-size: 20px;
  font-weight: 300;
}

.hlwidc-password-modal .hlwidc-modal-close:hover {
  background: rgba(255, 255, 255, 0.2);
  transform: scale(1.05);
}

.hlwidc-password-form {
  padding: 0;
}

.hlwidc-form-group {
  margin-bottom: 24px;
}

.hlwidc-form-group label {
  display: block;
  margin-bottom: 12px;
  font-weight: 600;
  color: #1f2937;
  font-size: 15px;
  letter-spacing: 0.025em;
}

.hlwidc-password-input-group {
  display: flex;
  gap: 12px;
  align-items: stretch;
}

.hlwidc-password-input-wrapper {
  flex: 1;
  position: relative;
  display: flex;
  align-items: center;
}

.hlwidc-password-input {
  width: 100%;
  padding: 14px 50px 14px 16px;
  border: 2px solid #e5e7eb;
  border-radius: 12px;
  font-size: 15px;
  font-weight: 500;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  background: #fafafa;
  letter-spacing: 0.5px;
  font-family: 'Courier New', monospace;
}

.hlwidc-password-toggle-btn {
  position: absolute;
  right: 12px;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  font-size: 18px;
  cursor: pointer;
  padding: 4px;
  border-radius: 6px;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 32px;
  height: 32px;
}

.hlwidc-password-toggle-btn:hover:not(:disabled) {
  background: rgba(139, 92, 246, 0.1);
  transform: translateY(-50%) scale(1.1);
}

.hlwidc-password-toggle-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.hlwidc-password-input:focus {
  outline: none;
  border-color: #8b5cf6;
  background: white;
  box-shadow: 0 0 0 4px rgba(139, 92, 246, 0.1);
  transform: translateY(-1px);
}

.hlwidc-password-input:disabled {
  background-color: #f3f4f6;
  color: #9ca3af;
  cursor: not-allowed;
  border-color: #d1d5db;
}

.hlwidc-generate-btn {
  padding: 14px 20px;
  background: linear-gradient(135deg, #10b981, #059669);
  color: white;
  border: none;
  border-radius: 12px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  white-space: nowrap;
  box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
  position: relative;
  overflow: hidden;
}

.hlwidc-generate-btn::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
  transition: left 0.5s;
}

.hlwidc-generate-btn:hover::before {
  left: 100%;
}

.hlwidc-generate-btn:hover:not(:disabled) {
  background: linear-gradient(135deg, #059669, #047857);
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(16, 185, 129, 0.4);
}

.hlwidc-generate-btn:active:not(:disabled) {
  transform: translateY(-1px);
}

.hlwidc-generate-btn:disabled {
  background: linear-gradient(135deg, #9ca3af, #6b7280);
  cursor: not-allowed;
  transform: none;
  box-shadow: 0 2px 4px rgba(156, 163, 175, 0.3);
}

.hlwidc-generate-icon {
  width: 16px;
  height: 16px;
  margin-right: 6px;
}

.hlwidc-toggle-icon {
  width: 20px;
  height: 20px;
  transition: all 0.2s;
}

.hlwidc-password-tips {
  margin-top: 16px;
  padding: 16px 20px;
  background: linear-gradient(135deg, #f0f9ff, #e0f2fe);
  border: 1px solid #bae6fd;
  border-radius: 12px;
  position: relative;
  overflow: hidden;
}

.hlwidc-password-tips::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 3px;
  background: linear-gradient(90deg, #3b82f6, #8b5cf6, #10b981);
}

.hlwidc-password-tips p {
  margin: 0;
  font-size: 14px;
  color: #0369a1;
  line-height: 1.5;
  font-weight: 500;
  display: flex;
  align-items: center;
  gap: 8px;
}

.hlwidc-password-tips p::before {
  content: '';
  display: inline-block;
  width: 16px;
  height: 16px;
  background-image: url("data:image/svg+xml,%3Csvg fill='none' stroke='currentColor' viewBox='0 0 24 24' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z'%3E%3C/path%3E%3C/svg%3E");
  background-size: contain;
  background-repeat: no-repeat;
  margin-right: 8px;
}

.hlwidc-modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px 24px 16px;
  border-bottom: 1px solid #e5e7eb;
}

.hlwidc-modal-title {
  font-size: 18px;
  font-weight: 600;
  color: #1f2937;
  margin: 0;
}

.hlwidc-modal-close {
  background: none;
  border: none;
  font-size: 24px;
  color: #6b7280;
  cursor: pointer;
  padding: 0;
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 6px;
  transition: all 0.2s;
}

.hlwidc-modal-close:hover {
  background-color: #f3f4f6;
  color: #374151;
}

.hlwidc-modal-body {
  padding: 20px 24px;
}

.hlwidc-modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  padding: 16px 24px 20px;
  border-top: 1px solid #e5e7eb;
}

.hlwidc-modal-btn {
  padding: 8px 16px;
  border: none;
  border-radius: 6px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
  min-width: 80px;
}

.hlwidc-confirm-btn {
  background-color: #3b82f6;
  color: white;
}

.hlwidc-confirm-btn:hover {
  background-color: #2563eb;
}

.hlwidc-cancel-btn {
  background-color: #f3f4f6;
  color: #374151;
  border: 1px solid #d1d5db;
}

.hlwidc-cancel-btn:hover {
  background-color: #e5e7eb;
}

/* 响应式设计 */
@media (max-width: 768px) {
  .hlwidc-modal-overlay {
    padding: 16px;
  }
  
  .hlwidc-modal-content {
    max-width: none;
    width: 100%;
  }
  
  .hlwidc-password-modal {
    max-width: none;
    width: 100%;
    border-radius: 12px;
  }
  
  .hlwidc-password-modal .hlwidc-modal-header {
    padding: 20px 24px 16px;
  }
  
  .hlwidc-password-modal .hlwidc-modal-title {
    font-size: 18px;
  }
  
  .hlwidc-password-input-group {
    flex-direction: column;
    gap: 12px;
  }
  
  .hlwidc-password-input-wrapper {
    width: 100%;
  }
  
  .hlwidc-generate-btn {
    width: 100%;
    justify-content: center;
  }
  
  .hlwidc-password-toggle-btn {
    right: 8px;
    width: 28px;
    height: 28px;
    font-size: 16px;
  }
  
  .hlwidc-modal-header {
    padding: 16px 20px 12px;
  }
  
  .hlwidc-modal-body {
    padding: 16px 20px;
  }
  
  .hlwidc-modal-footer {
    padding: 12px 20px 16px;
    flex-direction: column;
    gap: 8px;
  }
  
  .hlwidc-modal-btn {
    width: 100%;
    padding: 12px 16px;
  }
}
</style>
