<template>
  <div v-if="machineInfo.cloud_server_info" class="hlwidc-info-card">
    <h2 class="hlwidc-card-title">时间信息</h2>
    <div class="hlwidc-time-grid">
      <div class="hlwidc-time-item">
        <label>开通时间</label>
        <span>{{ machineInfo.cloud_server_info.startTime }}</span>
      </div>
      <div class="hlwidc-time-item">
        <label>到期时间</label>
        <span>{{ machineInfo.cloud_server_info.endTime }}</span>
      </div>
      <div class="hlwidc-time-item">
        <label>自动续费</label>
        <span :class="machineInfo.cloud_server_info.isAutoRenew ? 'hlwidc-auto-renew-on' : 'hlwidc-auto-renew-off'">
          {{ machineInfo.cloud_server_info.isAutoRenew ? '已开启' : '未开启' }}
        </span>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { MachineInfo } from '@/services/panel'

defineProps<{
  machineInfo: MachineInfo
}>()
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

.hlwidc-card-title {
  font-size: 20px;
  font-weight: 600;
  color: #1f2937;
  margin: 0 0 20px 0;
}

/* 时间网格 */
.hlwidc-time-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 16px;
}

.hlwidc-time-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 0;
  border-bottom: 1px solid #f3f4f6;
}

.hlwidc-time-item:last-child {
  border-bottom: none;
}

.hlwidc-time-item label {
  font-weight: 500;
  color: #6b7280;
  font-size: 14px;
}

.hlwidc-time-item span {
  color: #1f2937;
  font-weight: 500;
}

.hlwidc-auto-renew-on {
  color: #059669;
  font-weight: 500;
}

.hlwidc-auto-renew-off {
  color: #dc2626;
  font-weight: 500;
}

/* 响应式设计 */
@media (max-width: 768px) {
  .hlwidc-time-grid {
    grid-template-columns: 1fr;
  }
}
</style>
