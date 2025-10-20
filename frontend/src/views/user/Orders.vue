<template>
  <div class="hlwidc-orders-page">
    <div class="hlwidc-page-header">
      <h1>订单管理</h1>
    </div>
    
    <div class="hlwidc-content">
      <!-- 筛选和搜索区域 -->
      <div class="hlwidc-filters-section">
        <div class="hlwidc-search-box">
          <input 
            v-model="searchKeyword" 
            type="text" 
            placeholder="搜索订单号、用户名、邮箱..." 
            class="hlwidc-search-input"
            @keyup.enter="handleSearch"
          />
          <button @click="handleSearch" class="hlwidc-search-btn">搜索</button>
        </div>
        
        <div class="hlwidc-filter-row">
          <select v-model="statusFilter" @change="handleFilter" class="hlwidc-status-select">
            <option value="">全部状态</option>
            <option value="Pending">待处理</option>
            <option value="Active">活跃</option>
            <option value="Suspended">暂停</option>
            <option value="Cancelled">已取消</option>
            <option value="Fraud">欺诈</option>
            <option value="Completed">已完成</option>
          </select>
          
          <input 
            v-model="startDate" 
            type="date" 
            placeholder="开始日期" 
            class="hlwidc-date-input"
            @change="handleFilter"
          />
          <input 
            v-model="endDate" 
            type="date" 
            placeholder="结束日期" 
            class="hlwidc-date-input"
            @change="handleFilter"
          />
          
          <button @click="resetFilters" class="hlwidc-reset-btn">重置</button>
        </div>
      </div>

      <!-- 订单列表 -->
      <div class="hlwidc-orders-table-container">
        <table class="hlwidc-orders-table">
          <thead>
            <tr>
              <th>订单号</th>
              <th>客户</th>
              <th>状态</th>
              <th>金额</th>
              <th>支付方式</th>
              <th>备注</th>
              <th>创建时间</th>
              <th>操作</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="loading" class="hlwidc-loading-row">
              <td colspan="8" class="hlwidc-loading-cell">
                <div class="hlwidc-loading-spinner"></div>
                <span>加载中...</span>
              </td>
            </tr>
            <tr v-else-if="orders.length === 0" class="hlwidc-empty-row">
              <td colspan="8" class="hlwidc-empty-cell">暂无订单数据</td>
            </tr>
            <tr v-else v-for="order in orders" :key="order.id" class="hlwidc-order-row">
              <td class="hlwidc-order-num">{{ order.ordernum }}</td>
              <td class="hlwidc-client-info">
                <div class="hlwidc-client-name">{{ order.client?.username || '-' }}</div>
                <div class="hlwidc-client-email">{{ order.client?.email || '-' }}</div>
              </td>
              <td class="hlwidc-status-cell">
                <span :class="`hlwidc-status-badge hlwidc-status-${order.status.toLowerCase()}`">
                  {{ getStatusText(order.status) }}
                </span>
              </td>
              <td class="hlwidc-amount">¥{{ order.invoice?.total || 0 }}</td>
              <td class="hlwidc-payment">{{ getPaymentText(order.payment) }}</td>
              <td class="hlwidc-notes">{{ order.notes || '-' }}</td>
              <td class="hlwidc-create-time">{{ formatDate(order.create_time) }}</td>
              <td class="hlwidc-actions">
                <button 
                  v-if="order.status === 'Pending'" 
                  @click="continueProcessing(order, $event)" 
                  class="hlwidc-continue-btn"
                >
                  继续处理
                </button>
                <span v-else class="hlwidc-no-action">-</span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- 分页 -->
      <div class="hlwidc-pagination" v-if="totalPages > 1">
        <button 
          @click="goToPage(currentPage - 1)" 
          :disabled="currentPage <= 1"
          class="hlwidc-page-btn"
        >
          上一页
        </button>
        
        <span class="hlwidc-page-info">
          第 {{ currentPage }} 页，共 {{ totalPages }} 页，总计 {{ total }} 条
        </span>
        
        <button 
          @click="goToPage(currentPage + 1)" 
          :disabled="currentPage >= totalPages"
          class="hlwidc-page-btn"
        >
          下一页
        </button>
      </div>
    </div>

    <!-- 自定义提示组件 -->
    <div v-if="showToast" class="hlwidc-toast" :class="`hlwidc-toast-${toastType}`">
      <div class="hlwidc-toast-content">
        <div class="hlwidc-toast-icon">
          <span v-if="toastType === 'success'">✓</span>
          <span v-else-if="toastType === 'error'">✗</span>
          <span v-else>ℹ</span>
        </div>
        <div class="hlwidc-toast-message">{{ toastMessage }}</div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { zhaomuApiService, type Order, type OrderStatus, type PaymentMethod } from '@/services/client'

// 响应式数据
const orders = ref<Order[]>([])
const loading = ref(false)
const currentPage = ref(1)
const pageSize = ref(15)
const total = ref(0)
const totalPages = ref(0)

// 筛选条件
const searchKeyword = ref('')
const statusFilter = ref('')
const startDate = ref('')
const endDate = ref('')

// 提示相关
const showToast = ref(false)
const toastMessage = ref('')
const toastType = ref<'success' | 'error' | 'info'>('info')

// 计算属性
const hasMore = computed(() => currentPage.value < totalPages.value)

// 显示提示
const showToastMessage = (message: string, type: 'success' | 'error' | 'info' = 'info') => {
  toastMessage.value = message
  toastType.value = type
  showToast.value = true
  
  // 3秒后自动隐藏
  setTimeout(() => {
    showToast.value = false
  }, 3000)
}

// 获取订单列表
const fetchOrders = async () => {
  loading.value = true
  try {
    const params = {
      page: currentPage.value,
      limit: pageSize.value,
      keyword: searchKeyword.value,
      status: statusFilter.value,
      startDate: startDate.value,
      endDate: endDate.value
    }
    
    const response = await zhaomuApiService.getOrderList(params)
    
    if (response.code === 1 && response.data) {
      orders.value = response.data.orders || []
      total.value = response.data.total || 0
      totalPages.value = response.data.pages || 0
    } else {
      console.error('获取订单列表失败:', response.msg)
    }
  } catch (error) {
    console.error('获取订单列表异常:', error)
  } finally {
    loading.value = false
  }
}

// 搜索处理
const handleSearch = () => {
  currentPage.value = 1
  fetchOrders()
}

// 筛选处理
const handleFilter = () => {
  currentPage.value = 1
  fetchOrders()
}

// 重置筛选
const resetFilters = () => {
  searchKeyword.value = ''
  statusFilter.value = ''
  startDate.value = ''
  endDate.value = ''
  currentPage.value = 1
  fetchOrders()
}

// 分页处理
const goToPage = (page: number) => {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page
    fetchOrders()
  }
}


// 继续处理订单
const continueProcessing = async (order: Order, event: Event) => {
  const button = event.target as HTMLButtonElement
  const originalText = button.textContent
  
  try {
    // 显示加载状态
    button.disabled = true
    button.textContent = '处理中...'
    
    // 调用后端API
    const response = await zhaomuApiService.continueProcessing(order.id)
    
    if (response.code === 1) {
      // 成功处理
      showToastMessage('订单继续处理成功！', 'success')
      // 刷新订单列表
      await fetchOrders()
    } else {
      // 处理失败
      showToastMessage('继续处理失败: ' + response.msg, 'error')
    }
    
  } catch (error) {
    console.error('继续处理订单异常:', error)
    showToastMessage('继续处理失败，请稍后重试', 'error')
  } finally {
    // 恢复按钮状态
    button.disabled = false
    button.textContent = originalText
  }
}

// 状态文本转换
const getStatusText = (status: OrderStatus): string => {
  const statusMap: Record<OrderStatus, string> = {
    'Pending': '待处理',
    'Active': '活跃',
    'Suspended': '暂停',
    'Cancelled': '已取消',
    'Fraud': '欺诈',
    'Completed': '已完成'
  }
  return statusMap[status] || status
}

// 支付方式文本转换
const getPaymentText = (payment: PaymentMethod): string => {
  const paymentMap: Record<PaymentMethod, string> = {
    'monthly': '月付',
    'quarterly': '季付',
    'yearly': '年付',
    'StripeAli': '支付宝',
    'wechat': '微信支付',
    'bank': '银行转账',
    'credit': '余额支付',
    'manual': '手动支付',
    'UserCustom': '用户自定义'
  }
  return paymentMap[payment] || payment
}

// 日期格式化
const formatDate = (dateStr: string) => {
  if (!dateStr) return '-'
  const date = new Date(dateStr)
  return date.toLocaleString('zh-CN')
}

// 组件挂载时获取数据
onMounted(() => {
  fetchOrders()
})
</script>

<style scoped>
.hlwidc-orders-page {
  padding: 30px;
}

.hlwidc-page-header {
  margin-bottom: 30px;
}

.hlwidc-page-header h1 {
  margin: 0;
  font-size: 2rem;
  color: #1f2937;
}

.hlwidc-content {
  background: white;
  border-radius: 12px;
  padding: 30px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  border: 1px solid #e5e7eb;
}

.hlwidc-filters-section {
  margin-bottom: 30px;
  padding-bottom: 20px;
  border-bottom: 1px solid #e5e7eb;
}

.hlwidc-search-box {
  display: flex;
  gap: 10px;
  margin-bottom: 20px;
}

.hlwidc-search-input {
  flex: 1;
  padding: 10px 15px;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 14px;
}

.hlwidc-search-btn {
  padding: 10px 20px;
  background: #3b82f6;
  color: white;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-size: 14px;
}

.hlwidc-search-btn:hover {
  background: #2563eb;
}

.hlwidc-filter-row {
  display: flex;
  gap: 15px;
  align-items: center;
  flex-wrap: wrap;
}

.hlwidc-status-select,
.hlwidc-date-input {
  padding: 8px 12px;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  font-size: 14px;
}

.hlwidc-reset-btn {
  padding: 8px 16px;
  background: #6b7280;
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-size: 14px;
}

.hlwidc-reset-btn:hover {
  background: #4b5563;
}

.hlwidc-orders-table-container {
  overflow-x: auto;
}

.hlwidc-orders-table {
  width: 100%;
  border-collapse: collapse;
  margin-bottom: 20px;
}

.hlwidc-orders-table th {
  background: #f9fafb;
  padding: 12px;
  text-align: left;
  font-weight: 600;
  color: #374151;
  border-bottom: 2px solid #e5e7eb;
}

.hlwidc-orders-table td {
  padding: 12px;
  border-bottom: 1px solid #e5e7eb;
}

.hlwidc-order-row:hover {
  background: #f9fafb;
}

.hlwidc-loading-cell,
.hlwidc-empty-cell {
  text-align: center;
  color: #6b7280;
  padding: 40px;
}

.hlwidc-loading-spinner {
  display: inline-block;
  width: 20px;
  height: 20px;
  border: 2px solid #e5e7eb;
  border-top: 2px solid #3b82f6;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-right: 10px;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.hlwidc-order-num {
  font-family: monospace;
  font-size: 13px;
  color: #3b82f6;
}

.hlwidc-client-info {
  min-width: 150px;
}

.hlwidc-client-name {
  font-weight: 500;
  color: #1f2937;
}

.hlwidc-client-email {
  font-size: 12px;
  color: #6b7280;
}

.hlwidc-status-badge {
  display: inline-block;
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 12px;
  font-weight: 500;
}

.hlwidc-status-pending {
  background: #fef3c7;
  color: #92400e;
}

.hlwidc-status-active {
  background: #d1fae5;
  color: #065f46;
}

.hlwidc-status-suspended {
  background: #fecaca;
  color: #991b1b;
}

.hlwidc-status-cancelled {
  background: #f3f4f6;
  color: #374151;
}

.hlwidc-status-fraud {
  background: #fecaca;
  color: #991b1b;
}

.hlwidc-status-completed {
  background: #d1fae5;
  color: #065f46;
}

.hlwidc-amount {
  font-weight: 600;
  color: #059669;
}

.hlwidc-notes {
  max-width: 200px;
  font-size: 13px;
  color: #6b7280;
  word-break: break-word;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.hlwidc-notes:hover {
  white-space: normal;
  overflow: visible;
  position: relative;
  z-index: 10;
  background: white;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  padding: 8px;
  border-radius: 4px;
}

.hlwidc-create-time {
  font-size: 13px;
  color: #6b7280;
}

.hlwidc-actions {
  display: flex;
  gap: 8px;
}

.hlwidc-continue-btn {
  padding: 4px 8px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 12px;
  background: #10b981;
  color: white;
}

.hlwidc-continue-btn:hover {
  background: #059669;
}

.hlwidc-no-action {
  color: #9ca3af;
  font-size: 14px;
}

.hlwidc-pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 20px;
  margin-top: 20px;
}

.hlwidc-page-btn {
  padding: 8px 16px;
  border: 1px solid #d1d5db;
  background: white;
  color: #374151;
  border-radius: 6px;
  cursor: pointer;
  font-size: 14px;
}

.hlwidc-page-btn:hover:not(:disabled) {
  background: #f9fafb;
}

.hlwidc-page-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.hlwidc-page-info {
  color: #6b7280;
  font-size: 14px;
}

/* 提示组件样式 */
.hlwidc-toast {
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  z-index: 9999;
  min-width: 300px;
  max-width: 500px;
  border-radius: 8px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
  animation: toastSlideIn 0.3s ease-out;
}

.hlwidc-toast-content {
  display: flex;
  align-items: center;
  padding: 16px 20px;
  border-radius: 8px;
}

.hlwidc-toast-icon {
  width: 24px;
  height: 24px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 12px;
  font-weight: bold;
  font-size: 14px;
}

.hlwidc-toast-message {
  flex: 1;
  font-size: 14px;
  font-weight: 500;
}

.hlwidc-toast-success {
  background: #d1fae5;
  border: 1px solid #10b981;
}

.hlwidc-toast-success .hlwidc-toast-icon {
  background: #10b981;
  color: white;
}

.hlwidc-toast-success .hlwidc-toast-message {
  color: #065f46;
}

.hlwidc-toast-error {
  background: #fecaca;
  border: 1px solid #ef4444;
}

.hlwidc-toast-error .hlwidc-toast-icon {
  background: #ef4444;
  color: white;
}

.hlwidc-toast-error .hlwidc-toast-message {
  color: #991b1b;
}

.hlwidc-toast-info {
  background: #dbeafe;
  border: 1px solid #3b82f6;
}

.hlwidc-toast-info .hlwidc-toast-icon {
  background: #3b82f6;
  color: white;
}

.hlwidc-toast-info .hlwidc-toast-message {
  color: #1e40af;
}

@keyframes toastSlideIn {
  from {
    opacity: 0;
    transform: translate(-50%, -50%) scale(0.9);
  }
  to {
    opacity: 1;
    transform: translate(-50%, -50%) scale(1);
  }
}
</style>
