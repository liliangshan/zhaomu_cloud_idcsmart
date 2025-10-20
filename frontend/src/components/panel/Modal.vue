<template>
  <div v-if="show" class="hlwidc-modal-overlay hlwidc-alert-modal-overlay" @click="closeModal">
    <div class="hlwidc-modal-content hlwidc-alert-modal-content" @click.stop>
      <div class="hlwidc-modal-header">
        <h3 class="hlwidc-modal-title">{{ title }}</h3>
        <button @click="closeModal" class="hlwidc-modal-close">×</button>
      </div>
      <div class="hlwidc-modal-body">
        <p>{{ message }}</p>
      </div>
      <div class="hlwidc-modal-footer">
        <button v-if="type === 'confirm'" @click="confirmAction" class="hlwidc-modal-btn hlwidc-confirm-btn">
          确定
        </button>
        <button v-if="type === 'confirm'" @click="cancelAction" class="hlwidc-modal-btn hlwidc-cancel-btn">
          取消
        </button>
        <button v-else @click="closeModal" class="hlwidc-modal-btn hlwidc-confirm-btn">
          确定
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
const props = defineProps<{
  show: boolean
  title: string
  message: string
  type: 'alert' | 'confirm'
}>()

const emit = defineEmits<{
  close: []
  confirm: []
  cancel: []
}>()

const closeModal = () => {
  emit('close')
}

const confirmAction = () => {
  emit('confirm')
}

const cancelAction = () => {
  emit('cancel')
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

/* Alert弹窗专用样式 */
.hlwidc-alert-modal-overlay {
  z-index: 999999;
}

.hlwidc-alert-modal-content {
  z-index: 1000000;
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

.hlwidc-modal-body p {
  margin: 0;
  color: #4b5563;
  line-height: 1.5;
  font-size: 14px;
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
