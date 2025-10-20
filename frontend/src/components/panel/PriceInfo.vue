<template>
  <div v-if="machineInfo.cloud_server_info" class="hlwidc-price-section">
    <h3 class="hlwidc-price-title">续费方案</h3>
    <div class="hlwidc-price-grid">
      <div 
        class="hlwidc-price-item" 
        :class="{ 'active': machineInfo.payment === 'monthly', 'loading': paymentLoading }"
        @click="$emit('switchPaymentCycle', 'monthly')"
      >
        <label>月付价格</label>
        <span class="hlwidc-price-value">¥{{ machineInfo.cloud_server_info.price }}</span>
        <span v-if="machineInfo.payment === 'monthly'" class="hlwidc-current-plan">当前方案</span>
      </div>
      <div 
        class="hlwidc-price-item" 
        :class="{ 'active': machineInfo.payment === 'quarterly', 'loading': paymentLoading }"
        @click="$emit('switchPaymentCycle', 'quarterly')"
      >
        <label>季付价格</label>
        <span class="hlwidc-price-value">¥{{ machineInfo.cloud_server_info.priceQuarter }}</span>
        <span v-if="machineInfo.payment === 'quarterly'" class="hlwidc-current-plan">当前方案</span>
      </div>
      <div 
        class="hlwidc-price-item" 
        :class="{ 'active': machineInfo.payment === 'semiannually', 'loading': paymentLoading }"
        @click="$emit('switchPaymentCycle', 'semiannually')"
      >
        <label>半年付价格</label>
        <span class="hlwidc-price-value">¥{{ machineInfo.cloud_server_info.priceHalfYear }}</span>
        <span v-if="machineInfo.payment === 'semiannually'" class="hlwidc-current-plan">当前方案</span>
      </div>
      <div 
        class="hlwidc-price-item" 
        :class="{ 'active': machineInfo.payment === 'annually', 'loading': paymentLoading }"
        @click="$emit('switchPaymentCycle', 'annually')"
      >
        <label>年付价格</label>
        <span class="hlwidc-price-value">¥{{ machineInfo.cloud_server_info.priceYear }}</span>
        <span v-if="machineInfo.payment === 'annually'" class="hlwidc-current-plan">当前方案</span>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { MachineInfo } from '@/services/panel'

defineProps<{
  machineInfo: MachineInfo
  paymentLoading: boolean
}>()

defineEmits<{
  switchPaymentCycle: [cycle: string]
}>()
</script>

<style scoped>
/* 价格信息区域 */
.hlwidc-price-section {
  margin-top: 24px;
  padding-top: 24px;
  border-top: 1px solid #e5e7eb;
}

.hlwidc-price-title {
  font-size: 16px;
  font-weight: 600;
  color: #1f2937;
  margin: 0 0 16px 0;
}

/* 价格网格 */
.hlwidc-price-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 16px;
}

.hlwidc-price-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 16px;
  background-color: #f9fafb;
  border-radius: 8px;
  border: 1px solid #e5e7eb;
  position: relative;
  transition: all 0.3s ease;
  cursor: pointer;
}

.hlwidc-price-item:hover {
  background-color: #f3f4f6;
  border-color: #d1d5db;
  transform: translateY(-1px);
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.hlwidc-price-item.active {
  background: linear-gradient(135deg, #dbeafe, #bfdbfe);
  border-color: #3b82f6;
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
}

.hlwidc-current-plan {
  position: absolute;
  top: -8px;
  right: -8px;
  background: #3b82f6;
  color: white;
  font-size: 10px;
  font-weight: 600;
  padding: 4px 8px;
  border-radius: 12px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);
}

.hlwidc-price-item.loading {
  opacity: 0.6;
  cursor: not-allowed;
  pointer-events: none;
}

.hlwidc-price-item.loading::after {
  content: '';
  position: absolute;
  top: 50%;
  left: 50%;
  width: 16px;
  height: 16px;
  margin: -8px 0 0 -8px;
  border: 2px solid #3b82f6;
  border-top-color: transparent;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

.hlwidc-price-item label {
  font-weight: 500;
  color: #6b7280;
  font-size: 14px;
}

.hlwidc-price-value {
  font-size: 18px;
  font-weight: 600;
  color: #059669;
}

/* 响应式设计 */
@media (max-width: 768px) {
  .hlwidc-price-grid {
    grid-template-columns: 1fr;
  }
}
</style>
