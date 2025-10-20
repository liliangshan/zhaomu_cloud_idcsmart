<template>
  <div class="hlwidc-info-card">
    <div class="hlwidc-card-header">
      <h2 class="hlwidc-card-title">基础信息</h2>
      <div class="hlwidc-action-buttons">
        <button 
          @click="$emit('startServer')"
          :disabled="actionLoading || shouldDisableButtons"
          class="hlwidc-action-btn hlwidc-start-btn"
          title="开机"
        >
          <svg class="hlwidc-btn-icon" viewBox="0 0 1024 1024" xmlns="http://www.w3.org/2000/svg">
            <path d="M813.290667 433.770667a85.333333 85.333333 0 0 1 0 147.2l-428.8 251.733333A85.333333 85.333333 0 0 1 256 759.018667V255.701333a85.333333 85.333333 0 0 1 128.533333-73.6z" fill="currentColor"/>
          </svg>
          开机
        </button>
        <button 
          @click="$emit('stopServer')" 
          :disabled="actionLoading || shouldDisableButtons"
          class="hlwidc-action-btn hlwidc-stop-btn"
          title="关机"
        >
          <svg class="hlwidc-btn-icon" viewBox="0 0 1024 1024" xmlns="http://www.w3.org/2000/svg">
            <path d="M554.666667 426.666667a42.666667 42.666667 0 0 1-85.333334 0V128a42.666667 42.666667 0 0 1 85.333334 0v298.666667z m101.973333-133.376a42.666667 42.666667 0 0 1 41.429333-74.581334 384 384 0 1 1-372.138666 0 42.666667 42.666667 0 0 1 41.386666 74.581334 298.666667 298.666667 0 1 0 289.365334 0z" fill="currentColor"/>
          </svg>
          关机
        </button>
        <button 
          @click="$emit('rebootServer')" 
          :disabled="actionLoading || shouldDisableButtons"
          class="hlwidc-action-btn hlwidc-reboot-btn"
          title="重启"
        >
          <svg class="hlwidc-btn-icon" viewBox="0 0 1024 1024" xmlns="http://www.w3.org/2000/svg">
            <path d="M990.208 750.933333h-306.517333v238.933334h153.6a17.066667 17.066667 0 0 1 0 34.133333h-648.533334a17.066667 17.066667 0 0 1 0-34.133333h153.6v-238.933334H34.474667a34.133333 34.133333 0 0 1-34.133334-34.133333V34.133333a34.133333 34.133333 0 0 1 34.133334-34.133333h955.733333a34.133333 34.133333 0 0 1 34.133333 34.133333v682.666667a34.133333 34.133333 0 0 1-34.133333 34.133333z m-613.717333 238.933334h273.066666v-238.933334h-273.066666v238.933334z m614.4-955.733334h-955.733334v682.666667h955.733334V34.133333zM319.488 284.330667a33.1776 33.1776 0 0 1 0 46.421333A32.836267 32.836267 0 1 1 273.066667 284.330667a33.1776 33.1776 0 0 1 46.421333 0z m-16.725333 100.352a36.352 36.352 0 1 1-36.522667 36.522666 36.283733 36.283733 0 0 1 36.522667-36.522666z m68.949333 125.952a40.96 40.96 0 0 1 0 57.344 40.413867 40.413867 0 1 1 0-57.344zM334.848 240.64a29.525333 29.525333 0 1 1 29.354667 29.354667 29.661867 29.661867 0 0 1-29.354667-29.354667zM513.024 102.4a68.266667 68.266667 0 1 1-68.266667 68.266667 68.266667 68.266667 0 0 1 68.266667-68.266667z m-3.754667 460.458667a44.885333 44.885333 0 1 1-45.056 45.056 44.9536 44.9536 0 0 1 45.056-45.056z m172.714667-40.277334a49.834667 49.834667 0 1 1-70.314667 0 50.0736 50.0736 0 0 1 70.314667 0z m75.434667-175.104a55.466667 55.466667 0 1 1-55.637334 55.637334 55.569067 55.569067 0 0 1 55.637334-55.637334z m-105.130667-63.146666a61.201067 61.201067 0 0 1 0-87.04 61.5424 61.5424 0 0 1 87.04 87.04 61.201067 61.201067 0 0 1-87.04 0z" fill="currentColor"/>
          </svg>
          重启
        </button>
      </div>
    </div>
    <div class="hlwidc-info-grid">
      <div class="hlwidc-info-item">
        <label>运行状态</label>
        <div class="hlwidc-status-container">
          <span v-if="isStableStatus" class="hlwidc-status-text" :class="getStatusClass()">
            {{ getStatusText() }}
          </span>
          <div v-else class="hlwidc-loading-status">
            <div class="hlwidc-loading-spinner"></div>
            <span class="hlwidc-loading-text">{{ getStatusText() }}</span>
          </div>
        </div>
      </div>
      <div class="hlwidc-info-item">
        <label>产品标志</label>
        <span>{{ machineInfo.domain }}</span>
      </div>
      <div class="hlwidc-info-item">
        <label>产品名称</label>
        <span>{{ machineInfo.product?.name }}</span>
      </div>
      <div class="hlwidc-info-item">
        <label>创建时间</label>
        <span>{{ formatDate(machineInfo.create_time) }}</span>
      </div>
      <div class="hlwidc-info-item">
        <label>到期时间</label>
        <span>{{ formatDate(machineInfo.nextduedate) }}</span>
      </div>
    </div>
    
    <!-- 续费方案 -->
    <PriceInfo 
      :machineInfo="machineInfo" 
      :paymentLoading="paymentLoading"
      @switchPaymentCycle="$emit('switchPaymentCycle', $event)"
    />
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import type { MachineInfo } from '@/services/panel'
import PriceInfo from './PriceInfo.vue'

const props = defineProps<{
  machineInfo: MachineInfo
  actionLoading: boolean
  shouldDisableButtons: boolean
  paymentLoading: boolean
}>()

defineEmits<{
  startServer: []
  stopServer: []
  rebootServer: []
  switchPaymentCycle: [cycle: string]
}>()

// 检查是否为稳定状态（运行中或已关机）
const isStableStatus = computed(() => {
  const status = props.machineInfo.cloud_server_info?.status
  if (!status) return true
  // 状态码1(运行中)、2(运行中)、3(已关机)为稳定状态
  return status === 1 || status === 2 || status === 3
})

// 获取状态文本
const getStatusText = () => {
  const status = props.machineInfo.cloud_server_info?.status
  if (!status) return '未知状态'
  
  const statusMap: Record<number, string> = {
    1: '运行中',
    2: '运行中',
    3: '已关机',
    4: '关机中',
    5: '开机中',
    6: '开机中',
    7: '关机中',
    8: '暂停中',
    9: '已暂停'
  }
  return statusMap[status] || '未知状态'
}

// 获取状态样式类
const getStatusClass = () => {
  const status = props.machineInfo.cloud_server_info?.status
  if (!status) return 'hlwidc-status-unknown'
  
  if (status === 1 || status === 2) return 'hlwidc-status-running'
  if (status === 3) return 'hlwidc-status-stopped'
  if (status === 4 || status === 7) return 'hlwidc-status-stopping'
  if (status === 5 || status === 6) return 'hlwidc-status-starting'
  if (status === 8) return 'hlwidc-status-pausing'
  if (status === 9) return 'hlwidc-status-paused'
  
  return 'hlwidc-status-unknown'
}

// 格式化日期
const formatDate = (date: string | number) => {
  if (!date) return '未知'
  const d = new Date(typeof date === 'string' ? date : date * 1000)
  return d.toLocaleString('zh-CN')
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

/* 操作按钮 */
.hlwidc-action-buttons {
  display: flex;
  gap: 8px;
}

.hlwidc-action-btn {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 8px 16px;
  border: none;
  border-radius: 6px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
  white-space: nowrap;
}

.hlwidc-action-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.hlwidc-start-btn {
  background-color: #10b981;
  color: white;
}

.hlwidc-start-btn:hover:not(:disabled) {
  background-color: #059669;
}

.hlwidc-stop-btn {
  background-color: #ef4444;
  color: white;
}

.hlwidc-stop-btn:hover:not(:disabled) {
  background-color: #dc2626;
}

.hlwidc-reboot-btn {
  background-color: #3b82f6;
  color: white;
}

.hlwidc-reboot-btn:hover:not(:disabled) {
  background-color: #2563eb;
}

.hlwidc-btn-icon {
  font-size: 12px;
  width: 18px;
  height: 18px;
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

/* 状态容器 */
.hlwidc-status-container {
  display: flex;
  align-items: center;
  gap: 8px;
}

/* 状态文本 */
.hlwidc-status-text {
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.hlwidc-status-running {
  background-color: #dcfce7;
  color: #166534;
}

.hlwidc-status-stopped {
  background-color: #f3f4f6;
  color: #374151;
}

.hlwidc-status-starting {
  background-color: #dbeafe;
  color: #1e40af;
}

.hlwidc-status-stopping {
  background-color: #fef3c7;
  color: #92400e;
}

.hlwidc-status-pausing {
  background-color: #fef3c7;
  color: #92400e;
}

.hlwidc-status-paused {
  background-color: #fef3c7;
  color: #92400e;
}

.hlwidc-status-unknown {
  background-color: #f3f4f6;
  color: #6b7280;
}

/* 加载状态 */
.hlwidc-loading-status {
  display: flex;
  align-items: center;
  gap: 8px;
}

.hlwidc-loading-spinner {
  width: 16px;
  height: 16px;
  border: 2px solid #e5e7eb;
  border-top: 2px solid #3b82f6;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

.hlwidc-loading-text {
  color: #6b7280;
  font-size: 14px;
  font-weight: 500;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.hlwidc-business-id {
  background-color: #f3f4f6;
  padding: 4px 8px;
  border-radius: 4px;
  font-family: monospace;
}

/* 响应式设计 */
@media (max-width: 768px) {
  .hlwidc-card-header {
    flex-direction: column;
    gap: 16px;
    align-items: flex-start;
  }
  
  .hlwidc-action-buttons {
    width: 100%;
    justify-content: space-between;
  }
  
  .hlwidc-action-btn {
    flex: 1;
    justify-content: center;
    padding: 10px 12px;
    font-size: 13px;
  }
  
  .hlwidc-info-grid {
    grid-template-columns: 1fr;
  }
}
</style>
