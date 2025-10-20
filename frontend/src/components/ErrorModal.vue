<template>
  <div v-if="visible" class="hlwidc-error-modal-overlay" @click="handleOverlayClick">
    <div class="hlwidc-error-modal-container" @click.stop>
      <div class="hlwidc-error-modal-header">
        <div class="hlwidc-error-modal-icon">
          <svg class="hlwidc-error-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 19.5c-.77.833.192 2.5 1.732 2.5z" />
          </svg>
        </div>
        <h3 class="hlwidc-error-modal-title">订单处理异常</h3>
      </div>
      
      <div class="hlwidc-error-modal-body">
        <p class="hlwidc-error-message">
          费用已扣，请联系客服处理
        </p>
        <div class="hlwidc-error-details">
          <p class="hlwidc-error-detail-text">
            如您已支付费用但订单未成功创建，请联系我们的客服团队协助处理。
          </p>
        </div>
      </div>
      
      <div class="hlwidc-error-modal-footer">
        <button class="hlwidc-error-btn-confirm" @click="handleConfirm">
          我知道了
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
interface Props {
  visible: boolean
}

const props = defineProps<Props>()

const emit = defineEmits<{
  close: []
  confirm: []
}>()

const handleOverlayClick = () => {
  // 点击遮罩不关闭弹窗
}

const handleConfirm = () => {
  emit('confirm')
  emit('close')
}
</script>

<style scoped>
.hlwidc-error-modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.6);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 99999999;
}

.hlwidc-error-modal-container {
  background: white;
  border-radius: 12px;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
  max-width: 480px;
  width: 90%;
  max-height: 90vh;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  animation: errorModalSlideIn 0.3s ease-out;
}

@keyframes errorModalSlideIn {
  from {
    transform: scale(0.9) translateY(-20px);
    opacity: 0;
  }
  to {
    transform: scale(1) translateY(0);
    opacity: 1;
  }
}

.hlwidc-error-modal-header {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 24px 24px 16px 24px;
  border-bottom: 1px solid #f3f4f6;
}

.hlwidc-error-modal-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 48px;
  height: 48px;
  background: #fef2f2;
  border-radius: 50%;
  flex-shrink: 0;
}

.hlwidc-error-icon {
  width: 24px;
  height: 24px;
  color: #ef4444;
}

.hlwidc-error-modal-title {
  margin: 0;
  font-size: 1.25rem;
  font-weight: 600;
  color: #111827;
}

.hlwidc-error-modal-body {
  padding: 24px;
  flex: 1;
}

.hlwidc-error-message {
  margin: 0 0 16px 0;
  font-size: 1rem;
  font-weight: 500;
  color: #dc2626;
  line-height: 1.5;
}

.hlwidc-error-details {
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  padding: 16px;
}

.hlwidc-error-detail-text {
  margin: 0;
  font-size: 0.875rem;
  color: #64748b;
  line-height: 1.6;
}

.hlwidc-error-modal-footer {
  padding: 16px 24px 24px 24px;
  display: flex;
  justify-content: flex-end;
  border-top: 1px solid #f3f4f6;
}

.hlwidc-error-btn-confirm {
  padding: 10px 24px;
  background: #3b82f6;
  color: white;
  border: 1px solid #3b82f6;
  border-radius: 8px;
  font-weight: 500;
  font-size: 0.875rem;
  cursor: pointer;
  transition: all 0.2s;
}

.hlwidc-error-btn-confirm:hover {
  background: #2563eb;
  border-color: #2563eb;
}

.hlwidc-error-btn-confirm:active {
  transform: translateY(1px);
}

/* 响应式设计 */
@media (max-width: 640px) {
  .hlwidc-error-modal-container {
    width: 95%;
    margin: 20px;
  }
  
  .hlwidc-error-modal-header {
    padding: 20px 20px 16px 20px;
  }
  
  .hlwidc-error-modal-body {
    padding: 20px;
  }
  
  .hlwidc-error-modal-footer {
    padding: 16px 20px 20px 20px;
  }
}
</style>
