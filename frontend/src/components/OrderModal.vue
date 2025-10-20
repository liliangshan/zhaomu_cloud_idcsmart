<template>
  <div v-if="visible" class="hlwidc-modal-overlay" @click="handleOverlayClick">
    <div class="hlwidc-modal-container" @click.stop>
      <div class="hlwidc-modal-header">
        <div class="hlwidc-modal-title-section">
          <h3 class="hlwidc-modal-title">产品订购</h3>
          <div class="hlwidc-modal-location">
            <span v-if="continent" class="hlwidc-location-text">{{ continent.name }}</span>
            <span v-if="country" class="hlwidc-location-text">{{ country.name }}</span>
            <span v-if="province" class="hlwidc-location-text">{{ province.name }}</span>
            <span v-if="zone" class="hlwidc-location-text">{{ zone.name }}</span>
          </div>
        </div>
        <button class="hlwidc-modal-close" @click="closeModal">
          <XMarkIcon class="hlwidc-close-icon" />
        </button>
      </div>
      
      <div class="hlwidc-modal-body">
        <!-- 产品信息 -->
        <div class="hlwidc-product-info">
          <div class="hlwidc-product-specs">
            <div class="hlwidc-spec-row">
              <span class="hlwidc-spec-label">CPU：</span>
              <span class="hlwidc-spec-value">{{ product.cpu }}核</span>
            </div>
            <div class="hlwidc-spec-row">
              <span class="hlwidc-spec-label">内存：</span>
              <span class="hlwidc-spec-value">{{ product.ram / 1024 }}G</span>
            </div>
            <div class="hlwidc-spec-row">
              <span class="hlwidc-spec-label">系统盘：</span>
              <div class="hlwidc-spec-control">
                <span v-if="product.disk === product.diskMax" class="hlwidc-spec-value">
                  {{ product.disk }}G
                </span>
                <div v-else class="hlwidc-range-control">
                  <input 
                    type="range" 
                    :min="product.disk" 
                    :max="product.diskMax" 
                    v-model="selectedSystemDisk"
                    class="hlwidc-range-slider"
                    @input="updateSystemDisk"
                  />
                  <input 
                    type="number" 
                    v-model="selectedSystemDisk"
                    :min="product.disk"
                    :max="product.diskMax"
                    class="hlwidc-range-input"
                    @input="updateSystemDisk"
                    @change="updateSystemDisk"
                  />
                  <span class="hlwidc-range-unit">G</span>
                </div>
              </div>
            </div>
            <div class="hlwidc-spec-row">
              <span class="hlwidc-spec-label">数据盘：</span>
              <div class="hlwidc-spec-control">
                <span v-if="product.diskData === product.diskDataMax" class="hlwidc-spec-value">
                  {{ product.diskData }}G
                </span>
                <div v-else class="hlwidc-range-control">
                  <input 
                    type="range" 
                    :min="product.diskData" 
                    :max="product.diskDataMax" 
                    v-model="selectedDataDisk"
                    class="hlwidc-range-slider"
                    @input="updateDataDisk"
                  />
                  <input 
                    type="number" 
                    v-model="selectedDataDisk"
                    :min="product.diskData"
                    :max="product.diskDataMax"
                    class="hlwidc-range-input"
                    @input="updateDataDisk"
                    @change="updateDataDisk"
                  />
                  <span class="hlwidc-range-unit">G</span>
                </div>
              </div>
            </div>
            <div class="hlwidc-spec-row">
              <span class="hlwidc-spec-label">带宽：</span>
              <div class="hlwidc-spec-control">
                <span v-if="!product.bandwidth || product.bandwidth === 0" class="hlwidc-spec-value">不限</span>
                <span v-else-if="product.bandwidth === product.bandwidthMax" class="hlwidc-spec-value">
                  {{ product.bandwidth }}M
                </span>
                <div v-else class="hlwidc-range-control">
                  <input 
                    type="range" 
                    :min="product.bandwidth" 
                    :max="product.bandwidthMax" 
                    :step="1"
                    v-model="selectedBandwidth"
                    class="hlwidc-range-slider"
                    @input="updateBandwidth"
                  />
                  <input 
                    type="number" 
                    v-model="selectedBandwidth"
                    :min="product.bandwidth"
                    :max="product.bandwidthMax"
                    class="hlwidc-range-input"
                    @input="updateBandwidth"
                    @change="updateBandwidth"
                  />
                  <span class="hlwidc-range-unit">M</span>
                </div>
              </div>
            </div>
            <div class="hlwidc-spec-row">
              <span class="hlwidc-spec-label">流量：</span>
              <span class="hlwidc-spec-value">
                <span v-if="product.traffic && product.traffic > 0">
                  {{ product.traffic }}GB
                </span>
                <span v-else>不限</span>
              </span>
            </div>
            <div class="hlwidc-spec-row">
              <span class="hlwidc-spec-label">操作系统类型：</span>
              <select 
                v-model="selectedImageType" 
                class="hlwidc-form-select"
                @change="handleImageTypeChange"
              >
                <option value="">请选择操作系统类型</option>
                <option 
                  v-for="type in imageTypes" 
                  :key="type" 
                  :value="type"
                >
                  {{ type }}
                </option>
              </select>
            </div>
            <div class="hlwidc-spec-row">
              <span class="hlwidc-spec-label">系统镜像：</span>
              <select 
                v-model="selectedImage" 
                class="hlwidc-form-select"
                :disabled="!selectedImageType"
              >
                <option value="">请选择系统镜像</option>
                <option 
                  v-for="image in availableImages" 
                  :key="image.id" 
                  :value="image.id"
                >
                  {{ image.name }}
                </option>
              </select>
            </div>
          </div>
        </div>

        <!-- 订购选项 -->
        <div class="hlwidc-order-options">
          <!-- 订购周期选择 -->
          <div class="hlwidc-cycle-selection">
            <div class="hlwidc-cycle-options">
              <label 
                v-for="cycle in availableCycles" 
                :key="cycle.value"
                class="hlwidc-cycle-option"
                :class="{ disabled: cycle.disabled }"
              >
                <input 
                  type="radio" 
                  :value="cycle.value"
                  v-model="selectedCycle"
                  :disabled="cycle.disabled"
                />
                <span class="hlwidc-cycle-text">
                  {{ cycle.label }}
                  <span class="hlwidc-cycle-price">{{ cycle.price }}</span>
                </span>
              </label>
            </div>
          </div>


        </div>

        <!-- 客户信息 -->
        <div class="hlwidc-customer-info">
          <div class="hlwidc-form-group">
            <label class="hlwidc-form-label">客户用户名：</label>
            <div class="hlwidc-search-dropdown">
              <input 
                v-model="customerSearchKeyword" 
                type="text" 
                class="hlwidc-form-input"
                placeholder="搜索客户用户名"
                @input="handleCustomerSearch"
                @focus="handleCustomerFocus"
                @blur="handleCustomerBlur"
              />
              <div v-if="showCustomerDropdown" class="hlwidc-dropdown-menu">
                <div v-if="customerSearchResults.length === 0 && !loadingCustomers" class="hlwidc-dropdown-empty">
                  未找到匹配的用户
                </div>
                <div v-else-if="customerSearchResults.length > 0" class="hlwidc-dropdown-content">
                  <div 
                    v-for="user in customerSearchResults" 
                    :key="user.id"
                    class="hlwidc-dropdown-item"
                    @click="selectCustomer(user)"
                  >
                    <div class="hlwidc-user-info">
                      <div class="hlwidc-username">{{ user.username }}</div>
                      <div class="hlwidc-user-details">
                        <span class="hlwidc-user-email">{{ user.email }}</span>
                        <span v-if="user.phonenumber" class="hlwidc-user-phone">{{ user.phonenumber }}</span>
                      </div>
                    </div>
                    <div class="hlwidc-user-status">
                      <span :class="['hlwidc-status-tag', user.status === 1 ? 'active' : 'inactive']">
                        {{ user.status_text }}
                      </span>
                    </div>
                  </div>
                </div>
                <!-- 半透明加载动画 -->
                <div v-if="loadingCustomers" class="hlwidc-dropdown-loading-overlay">
                  <div class="hlwidc-loading-spinner"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <div class="hlwidc-modal-footer">
        <div class="hlwidc-footer-left">
          <div class="hlwidc-total-price">
            <span class="hlwidc-total-label">总价：</span>
            <span class="hlwidc-total-amount">{{ totalPrice }}</span>
          </div>
        </div>
        <div class="hlwidc-footer-right">
          <button class="hlwidc-btn-cancel" @click="closeModal" :disabled="isSubmitting">
            取消
          </button>
          <button 
            class="hlwidc-btn-confirm" 
            @click="confirmOrder"
            :disabled="!canConfirm"
          >
            <span v-if="isSubmitting" class="hlwidc-loading-spinner-small"></span>
            {{ isSubmitting ? '提交中...' : '确认订购' }}
          </button>
        </div>
      </div>
      
      <!-- 消息提示组件 -->
      <div v-if="showMessage" class="hlwidc-message-toast" :class="`hlwidc-message-${messageType}`">
        <div class="hlwidc-message-content">
          <div class="hlwidc-message-icon">
            <span v-if="messageType === 'success'">✓</span>
            <span v-else-if="messageType === 'error'">✕</span>
            <span v-else-if="messageType === 'warning'">⚠</span>
            <span v-else>ℹ</span>
          </div>
          <div class="hlwidc-message-text">{{ messageText }}</div>
          <button class="hlwidc-message-close" @click="showMessage = false">×</button>
        </div>
      </div>
    </div>
  </div>
  
  <!-- 错误弹窗组件 -->
  <ErrorModal 
    :visible="showErrorModal" 
    @close="handleErrorConfirm"
    @confirm="handleErrorConfirm"
  />
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import { XMarkIcon } from '@heroicons/vue/24/outline'
import { zhaomuApiService } from '@/services/api'
import ErrorModal from './ErrorModal.vue'

interface Product {
  id: number
  name?: string
  cpu: number
  ram: number
  disk: number
  diskMax: number
  diskData: number
  diskDataMax: number
  diskMedia: string
  bandwidth: number
  bandwidthMax: number
  traffic: number
  price: number
  priceQuarter: number
  priceYear: number
  minPaymentCycle: number
  noWindows: number
  outOfStock: number
}

interface CycleOption {
  value: number
  label: string
  price: string
  disabled: boolean
}

interface Props {
  visible: boolean
  product: Product
  currencySymbol: string
  formatPrice: (price: number) => string
  continent: any
  country: any
  province: any
  zone: any
}

const props = defineProps<Props>()

const emit = defineEmits<{
  close: []
  confirm: [orderData: any]
}>()

// 响应式数据
const selectedCycle = ref<number>(2) // 默认月付
const customerName = ref<string>('')

// 客户搜索相关
const customerSearchKeyword = ref<string>('')
const showCustomerDropdown = ref<boolean>(false)
const customerSearchResults = ref<any[]>([])
const loadingCustomers = ref<boolean>(false)
const selectedCustomer = ref<any>(null)

// 提示消息相关
const showMessage = ref<boolean>(false)
const messageText = ref<string>('')
const messageType = ref<'success' | 'error' | 'warning' | 'info'>('info')
const isSubmitting = ref<boolean>(false)

// 错误弹窗相关
const showErrorModal = ref<boolean>(false)

// 拖动条数据
const selectedSystemDisk = ref<number>(0)
const selectedDataDisk = ref<number>(0)
const selectedBandwidth = ref<number>(0)

// 价格数据
const productPrices = ref<any>(null)
const loadingPrices = ref<boolean>(false)

// 镜像选择相关
const imageGroups = ref<any[]>([])
const imageTypes = ref<string[]>([])
const selectedImageType = ref<string>('')
const selectedImage = ref<number>(0)
const loadingImages = ref<boolean>(false)

// 计算属性
const availableCycles = computed<CycleOption[]>(() => {
  // 使用获取到的价格数据
  const monthlyPrice = productPrices.value?.['1']
  const quarterlyPrice = productPrices.value?.['2'] 
  const yearlyPrice = productPrices.value?.['4']
  
  const cycles: CycleOption[] = []
  
  // 月付（可能没有）
  if (monthlyPrice !== undefined && monthlyPrice !== null) {
    cycles.push({
      value: 2,
      label: '月付',
      price: monthlyPrice.toFixed(2),
      disabled: false
    })
  }
  
  // 季付（可能没有）
  if (quarterlyPrice !== undefined && quarterlyPrice !== null) {
    cycles.push({
      value: 3,
      label: '季付',
      price: quarterlyPrice.toFixed(2),
      disabled: false
    })
  }
  
  // 年付
  if (yearlyPrice !== undefined && yearlyPrice !== null) {
    cycles.push({
      value: 4,
      label: '年付',
      price: yearlyPrice.toFixed(2),
      disabled: false
    })
  }
  
  return cycles
})

const totalPrice = computed(() => {
  const cycle = availableCycles.value.find(c => c.value === selectedCycle.value)
  if (!cycle) return '0.00'
  
  // 使用获取到的价格数据
  const monthlyPrice = productPrices.value?.['1']
  const quarterlyPrice = productPrices.value?.['2']
  const yearlyPrice = productPrices.value?.['4']
  
  const basePrice = selectedCycle.value === 2 ? monthlyPrice :
                   selectedCycle.value === 3 ? quarterlyPrice :
                   yearlyPrice
  
  if (basePrice === undefined || basePrice === null) return '0.00'
  
  return `${props.currencySymbol}${basePrice.toFixed(2)}`
})

const canConfirm = computed(() => {
  return customerName.value.trim() && 
         !props.product.outOfStock &&
         !isSubmitting.value
})

// 可用镜像列表
const availableImages = computed(() => {
  if (!selectedImageType.value || !imageGroups.value.length) {
    return []
  }
  
  const group = imageGroups.value.find(g => g.type === selectedImageType.value)
  return group ? group.images : []
})

// 方法
const closeModal = () => {
  emit('close')
}

const handleOverlayClick = () => {
  // 点击遮罩不关闭弹窗
  // closeModal()
}

// 处理错误弹窗确认
const handleErrorConfirm = () => {
  showErrorModal.value = false
  closeModal()
}


// 获取产品价格
const fetchProductPrice = async (forceFetch = false) => {
  if (loadingPrices.value && !forceFetch) return
  
  try {
    loadingPrices.value = true
    
    const params: any = {
      productId: props.product.id
    }
    
    // 传递所有配置参数（系统盘、数据盘、带宽）
    params.disk = selectedSystemDisk.value.toString()
    params.diskData = selectedDataDisk.value.toString()
    params.bandwidth = selectedBandwidth.value.toString()
    
    const response = await zhaomuApiService.getProductPrice(params)
    if (response.code === 1) {
      productPrices.value = response.data
    }
  } catch (error) {
    console.error('获取产品价格失败:', error)
  } finally {
    loadingPrices.value = false
  }
}

// 更新拖动条值
const updateSystemDisk = () => {
  fetchProductPrice()
}

const updateDataDisk = () => {
  fetchProductPrice()
}

const updateBandwidth = () => {
  fetchProductPrice()
}

// 客户搜索相关方法
const handleCustomerSearch = async () => {
  const keyword = customerSearchKeyword.value.trim()
  
  try {
    loadingCustomers.value = true
    const response = await zhaomuApiService.searchUsers({
      keyword: keyword,
      page: 1,
      limit: 10
    })
    
    if (response.code === 1 && response.data) {
      customerSearchResults.value = response.data.users || []
    } else {
      // 搜索失败时不清空原数据，保持现有结果
      console.warn('搜索客户失败，保持现有数据')
    }
  } catch (error) {
    console.error('搜索客户失败:', error)
    // 搜索异常时不清空原数据，保持现有结果
  } finally {
    loadingCustomers.value = false
  }
}

const selectCustomer = (user: any) => {
  selectedCustomer.value = user
  customerName.value = user.username
  showCustomerDropdown.value = false
  customerSearchKeyword.value = user.username
}

const handleCustomerFocus = async () => {
  showCustomerDropdown.value = true
  // 如果还没有加载过用户列表，则加载前10个用户
  if (customerSearchResults.value.length === 0 && !loadingCustomers.value) {
    await handleCustomerSearch()
  }
}

const handleCustomerBlur = () => {
  // 延迟关闭下拉菜单，让点击事件先执行
  setTimeout(() => {
    showCustomerDropdown.value = false
  }, 200)
}

// 获取产品镜像列表
const fetchProductImages = async () => {
  try {
    loadingImages.value = true
    const response = await zhaomuApiService.getProductImages({ productId: props.product.id })
    
    if (response.code === 1 && response.data) {
      imageGroups.value = response.data
      imageTypes.value = response.data.map((group: any) => group.type)
      
      // 自动选择第一个类型和第一个镜像
      if (imageTypes.value.length > 0) {
        selectedImageType.value = imageTypes.value[0] || ''
        
        // 选择第一个镜像
        const firstGroup = imageGroups.value.find((group: any) => group.type === selectedImageType.value)
        if (firstGroup && firstGroup.images && firstGroup.images.length > 0) {
          selectedImage.value = firstGroup.images[0].id
        }
      }
    } else {
      imageGroups.value = []
      imageTypes.value = []
    }
  } catch (error) {
    console.error('获取镜像列表失败:', error)
    imageGroups.value = []
    imageTypes.value = []
  } finally {
    loadingImages.value = false
  }
}

// 处理镜像类型变化
const handleImageTypeChange = () => {
  // 选择新类型下的第一个镜像
  const group = imageGroups.value.find((g: any) => g.type === selectedImageType.value)
  if (group && group.images && group.images.length > 0) {
    selectedImage.value = group.images[0].id
  } else {
    selectedImage.value = 0
  }
}

// 显示消息提示
const showMessageToast = (text: string, type: 'success' | 'error' | 'warning' | 'info' = 'info') => {
  messageText.value = text
  messageType.value = type
  showMessage.value = true
  
  // 3秒后自动隐藏
  setTimeout(() => {
    showMessage.value = false
  }, 3000)
}

const confirmOrder = async () => {
  if (!canConfirm.value) return
  
  isSubmitting.value = true
  
  const orderData = {
    product: {
      id: props.product.id,
      cpu: props.product.cpu,
      ram: props.product.ram,
      disk: selectedSystemDisk.value,
      diskData: selectedDataDisk.value,
      bandwidth: selectedBandwidth.value,
      os: selectedImageType.value,
      os_url: availableImages.value.find((img: any) => img.id === selectedImage.value)?.name || ''
    },
    location: {
      continent: props.continent,
      country: props.country,
      province: props.province,
      zone: props.zone
    },
    payment: {
      cycle: selectedCycle.value === 2 ? 'monthly' : 
             selectedCycle.value === 3 ? 'quarterly' : 'yearly'
    },
    customer: selectedCustomer.value || {
      id: null,
      name: customerName.value
    },
    totalPrice: totalPrice.value,
    image: {
      type: selectedImageType.value,
      id: selectedImage.value,
      name: availableImages.value.find((img: any) => img.id === selectedImage.value)?.name || ''
    },
    // 添加配置参数
    configuration: {
      systemDisk: selectedSystemDisk.value,
      dataDisk: selectedDataDisk.value,
      bandwidth: selectedBandwidth.value
    }
  }
  
  try {
    // 调用提交订单API
    const response = await zhaomuApiService.submitOrder(orderData)
    
    if (response.code === 1) {
      console.log('订单提交成功:', response.data)
      showMessageToast('订单提交成功！', 'success')
      // 成功后关闭弹窗
      setTimeout(() => {
        closeModal()
      }, 1500)
    } else {
      console.error('订单提交失败:', response.msg)
      
      // 检查错误信息中是否包含[ERROR]
      if (response.msg && !response.msg.includes('[ERROR]')) {
        // 不包含[ERROR]，显示错误弹窗
        showErrorModal.value = true
      } else {
        // 包含[ERROR]，显示普通错误提示
        showMessageToast('订单提交失败：' + response.msg, 'error')
      }
    }
  } catch (error) {
    console.error('订单提交异常:', error)
    
    // 异常时也检查错误信息
    const errorMessage = error instanceof Error ? error.message : String(error)
    if (errorMessage && !errorMessage.includes('[ERROR]')) {
      // 不包含[ERROR]，显示错误弹窗
      showErrorModal.value = true
    } else {
      // 包含[ERROR]，显示普通错误提示
      showMessageToast('订单提交异常，请重试', 'error')
    }
  } finally {
    isSubmitting.value = false
  }
}

// 监听产品变化，重置表单
watch(() => props.product, async () => {
  customerName.value = ''
  
  // 重置价格数据
  productPrices.value = null
  
  // 重置镜像选择
  selectedImageType.value = ''
  selectedImage.value = 0
  imageGroups.value = []
  imageTypes.value = []
  
  // 初始化拖动条值
  if (props.product) {
    selectedSystemDisk.value = props.product.disk
    selectedDataDisk.value = props.product.diskData
    selectedBandwidth.value = props.product.bandwidth || 0
    
    // 载入时立即获取价格和镜像
    await Promise.all([
      fetchProductPrice(true),
      fetchProductImages()
    ])
    
    // 获取价格后，选择第一个可用的周期
    if (availableCycles.value.length > 0) {
      selectedCycle.value = availableCycles.value[0]?.value || 2
    }
  }
}, { immediate: true })

// 监听拖动条值变化，获取价格
watch([selectedSystemDisk, selectedDataDisk, selectedBandwidth], () => {
  // 延迟获取价格，避免频繁请求
  setTimeout(() => {
    fetchProductPrice()
  }, 500)
}, { deep: true })

// 监听价格变化，自动选择第一个可用周期
watch(() => productPrices.value, () => {
  if (productPrices.value && availableCycles.value.length > 0) {
    // 如果当前选择的周期不可用，选择第一个可用的
    const currentCycle = availableCycles.value.find(c => c.value === selectedCycle.value)
    if (!currentCycle) {
      selectedCycle.value = availableCycles.value[0]?.value || 2
    }
  }
})
</script>

<style scoped>
.hlwidc-modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999999;
}

.hlwidc-modal-container {
  background: white;
  border-radius: 12px;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
  max-width: 600px;
  width: 90%;
  max-height: 90vh;
  overflow: visible;
  display: flex;
  flex-direction: column;
}

.hlwidc-modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 20px 24px;
  border-bottom: 1px solid #e5e7eb;
}

.hlwidc-modal-title-section {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.hlwidc-modal-title {
  margin: 0;
  font-size: 1.25rem;
  font-weight: 600;
  color: #111827;
}

.hlwidc-modal-location {
  display: flex;
  align-items: center;
  gap: 8px;
  flex-wrap: wrap;
}

.hlwidc-location-text {
  color: #6b7280;
  font-size: 0.875rem;
  font-weight: 400;
}

.hlwidc-modal-close {
  background: none;
  border: none;
  cursor: pointer;
  padding: 4px;
  border-radius: 4px;
  color: #6b7280;
  transition: all 0.2s;
}

.hlwidc-modal-close:hover {
  background: #f3f4f6;
  color: #374151;
}

.hlwidc-close-icon {
  width: 20px;
  height: 20px;
}

.hlwidc-modal-body {
  padding: 24px;
  overflow-y: auto; /* 允许内容滚动 */
  flex: 1;
}


.hlwidc-product-info {
  margin-bottom: 24px;
}

.hlwidc-product-title {
  margin: 0 0 16px 0;
  font-size: 1.125rem;
  font-weight: 600;
  color: #111827;
}

.hlwidc-product-specs {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.hlwidc-spec-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 8px 0;
  border-bottom: 1px solid #f3f4f6;
}

.hlwidc-spec-row:last-child {
  border-bottom: none;
}

.hlwidc-spec-label {
  font-weight: 500;
  color: #6b7280;
  min-width: 80px;
}

.hlwidc-spec-value {
  color: #111827;
  font-weight: 500;
}

.hlwidc-spec-control {
  display: flex;
  flex-direction: row;
  gap: 12px;
  flex: 1;
  align-items: center;
}

.hlwidc-range-control {
  display: flex;
  flex-direction: row;
  gap: 12px;
  align-items: center;
  flex: 1;
}

.hlwidc-range-slider {
  flex: 1;
  height: 6px;
  border-radius: 3px;
  background: #e5e7eb;
  outline: none;
  -webkit-appearance: none;
  appearance: none;
}

.hlwidc-range-slider::-webkit-slider-thumb {
  -webkit-appearance: none;
  appearance: none;
  width: 20px;
  height: 20px;
  border-radius: 50%;
  background: #3b82f6;
  cursor: pointer;
  border: 2px solid white;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.hlwidc-range-slider::-moz-range-thumb {
  width: 20px;
  height: 20px;
  border-radius: 50%;
  background: #3b82f6;
  cursor: pointer;
  border: 2px solid white;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.hlwidc-range-input {
  width: 80px;
  padding: 4px 8px;
  border: 1px solid #d1d5db;
  border-radius: 4px;
  font-size: 0.875rem;
  font-weight: 500;
  color: #111827;
  text-align: center;
  -moz-appearance: textfield;
  appearance: textfield;
}

.hlwidc-range-input::-webkit-outer-spin-button,
.hlwidc-range-input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

.hlwidc-range-input:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.1);
}

.hlwidc-range-unit {
  color: #6b7280;
  font-size: 0.875rem;
  font-weight: 500;
  min-width: 30px;
}


.hlwidc-form-select {
  width: 200px;
  padding: 6px 10px;
  border: 1px solid #d1d5db;
  border-radius: 4px;
  font-size: 14px;
  background: white;
  transition: border-color 0.2s;
}

.hlwidc-form-select:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.1);
}

.hlwidc-form-select:disabled {
  background: #f9fafb;
  color: #6b7280;
  cursor: not-allowed;
}

.hlwidc-order-options {
  margin-bottom: 24px;
}

.hlwidc-options-title {
  margin: 0 0 16px 0;
  font-size: 1.125rem;
  font-weight: 600;
  color: #111827;
}

.hlwidc-cycle-selection {
  margin-bottom: 20px;
}

.hlwidc-cycle-label {
  display: block;
  margin-bottom: 12px;
  font-weight: 500;
  color: #374151;
}

.hlwidc-cycle-options {
  display: flex;
  gap: 12px;
}

.hlwidc-cycle-option {
  flex: 1;
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 12px 16px;
  border: 2px solid #e5e7eb;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.2s;
}

.hlwidc-cycle-option:hover:not(.disabled) {
  border-color: #3b82f6;
  background: #f8fafc;
}

.hlwidc-cycle-option:has(input:checked) {
  border-color: #3b82f6;
  background: #eff6ff;
}

.hlwidc-cycle-option.disabled {
  opacity: 0.5;
  cursor: not-allowed;
  background: #f9fafb;
}

.hlwidc-cycle-text {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.hlwidc-cycle-price {
  font-weight: 600;
  color: #059669;
}


.hlwidc-total-price {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 12px 16px;
  background: #f8fafc;
  border-radius: 8px;
  border: 1px solid #e5e7eb;
  max-width: fit-content;
}

.hlwidc-total-label {
  font-weight: 500;
  color: #374151;
  font-size: 14px;
}

.hlwidc-total-amount {
  font-size: 1.125rem;
  font-weight: 700;
  color: #059669;
}

.hlwidc-customer-info {
  margin-bottom: 24px;
}

/* 客户搜索下拉框样式 */
.hlwidc-search-dropdown {
  position: relative;
}

.hlwidc-dropdown-menu {
  position: absolute;
  bottom: 100%; /* 将下拉菜单放置在上方 */
  left: 0;
  right: 0;
  background: white;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
  z-index: 9999;
  max-height: 300px;
  overflow-y: auto;
  margin-bottom: 4px; /* 顶部间距 */
}

.hlwidc-dropdown-loading,
.hlwidc-dropdown-empty {
  padding: 12px 16px;
  text-align: center;
  color: #6b7280;
  font-size: 0.875rem;
}

.hlwidc-dropdown-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 12px 16px;
  cursor: pointer;
  transition: background-color 0.2s;
  border-bottom: 1px solid #f3f4f6;
}

.hlwidc-dropdown-item:last-child {
  border-bottom: none;
}

.hlwidc-dropdown-item:hover {
  background: #f8fafc;
}

.hlwidc-user-info {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.hlwidc-username {
  font-weight: 600;
  color: #111827;
  font-size: 0.875rem;
}

.hlwidc-user-details {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.hlwidc-user-email,
.hlwidc-user-phone {
  font-size: 0.75rem;
  color: #6b7280;
}

.hlwidc-user-status {
  margin-left: 12px;
}

.hlwidc-status-tag {
  display: inline-block;
  padding: 2px 8px;
  border-radius: 4px;
  font-size: 0.75rem;
  font-weight: 500;
}

.hlwidc-status-tag.active {
  background: #dcfce7;
  color: #166534;
}

.hlwidc-status-tag.inactive {
  background: #fef2f2;
  color: #dc2626;
}

/* 半透明加载动画 */
.hlwidc-dropdown-loading-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(255, 255, 255, 0.3);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  z-index: 10;
  border-radius: 8px;
}

.hlwidc-loading-spinner {
  width: 20px;
  height: 20px;
  border: 2px solid #e5e7eb;
  border-top: 2px solid #3b82f6;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* 下拉内容容器 */
.hlwidc-dropdown-content {
  position: relative;
}

.hlwidc-customer-title {
  margin: 0 0 16px 0;
  font-size: 1.125rem;
  font-weight: 600;
  color: #111827;
}

.hlwidc-form-group {
  margin-bottom: 16px;
}

.hlwidc-form-label {
  display: block;
  margin-bottom: 6px;
  font-weight: 500;
  color: #374151;
}

.hlwidc-form-input,
.hlwidc-form-textarea {
  width: 100%;
  padding: 8px 12px;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  font-size: 14px;
  transition: border-color 0.2s;
}

.hlwidc-form-input:focus,
.hlwidc-form-textarea:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.hlwidc-form-textarea {
  resize: vertical;
  min-height: 80px;
}

.hlwidc-modal-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 20px 24px;
  border-top: 1px solid #e5e7eb;
}

.hlwidc-footer-left {
  flex: 1;
}

.hlwidc-footer-right {
  display: flex;
  gap: 12px;
}

.hlwidc-btn-cancel,
.hlwidc-btn-confirm {
  padding: 10px 20px;
  border-radius: 6px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
}

.hlwidc-btn-cancel {
  background: white;
  border: 1px solid #d1d5db;
  color: #374151;
}

.hlwidc-btn-cancel:hover {
  background: #f9fafb;
  border-color: #9ca3af;
}

.hlwidc-btn-confirm {
  background: #3b82f6;
  border: 1px solid #3b82f6;
  color: white;
}

.hlwidc-btn-confirm:hover:not(:disabled) {
  background: #2563eb;
  border-color: #2563eb;
}

.hlwidc-btn-confirm:disabled {
  background: #9ca3af;
  border-color: #9ca3af;
  cursor: not-allowed;
}

/* 消息提示样式 */
.hlwidc-message-toast {
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  z-index: 1000000;
  max-width: 400px;
  animation: fadeInScale 0.3s ease-out;
}

.hlwidc-message-content {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 16px 20px;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  font-size: 14px;
  font-weight: 500;
}

.hlwidc-message-success {
  background: #f0fdf4;
  border: 1px solid #bbf7d0;
  color: #166534;
}

.hlwidc-message-error {
  background: #fef2f2;
  border: 1px solid #fecaca;
  color: #dc2626;
}

.hlwidc-message-warning {
  background: #fffbeb;
  border: 1px solid #fed7aa;
  color: #d97706;
}

.hlwidc-message-info {
  background: #eff6ff;
  border: 1px solid #bfdbfe;
  color: #2563eb;
}

.hlwidc-message-icon {
  font-size: 18px;
  font-weight: bold;
  flex-shrink: 0;
}

.hlwidc-message-text {
  flex: 1;
  line-height: 1.4;
}

.hlwidc-message-close {
  background: none;
  border: none;
  font-size: 18px;
  font-weight: bold;
  color: inherit;
  cursor: pointer;
  padding: 0;
  width: 20px;
  height: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  transition: background-color 0.2s;
  flex-shrink: 0;
}

.hlwidc-message-close:hover {
  background: rgba(0, 0, 0, 0.1);
}

/* 小号加载动画 */
.hlwidc-loading-spinner-small {
  display: inline-block;
  width: 14px;
  height: 14px;
  border: 2px solid transparent;
  border-top: 2px solid currentColor;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-right: 8px;
}

@keyframes fadeInScale {
  from {
    transform: translate(-50%, -50%) scale(0.8);
    opacity: 0;
  }
  to {
    transform: translate(-50%, -50%) scale(1);
    opacity: 1;
  }
}

/* 响应式设计 */
@media (max-width: 640px) {
  .hlwidc-modal-container {
    width: 95%;
    margin: 20px;
    overflow: hidden; /* 确保在移动端时主容器内容不溢出 */
  }
  
  .hlwidc-product-specs {
    grid-template-columns: 1fr;
  }
  
  .hlwidc-cycle-options {
    flex-direction: row; /* 改为 row */
    flex-wrap: wrap; /* 允许换行 */
    justify-content: space-between; /* 按钮之间留有空间 */
  }
  
  .hlwidc-cycle-option {
    width: calc(50% - 6px); /* 每行两个，减去一半的gap */
  }
  
  .hlwidc-modal-footer {
    flex-direction: column;
  }
  
  .hlwidc-btn-cancel,
  .hlwidc-btn-confirm {
    width: 100%;
  }
  
  .hlwidc-cycle-option input[type="radio"] {
    display: none; /* 移动端隐藏单选按钮 */
  }
}
</style>
