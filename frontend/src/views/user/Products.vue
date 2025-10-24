<template>
  <div class="hlwidc-products-page">
    <div class="hlwidc-page-header">
      <h1>产品订购</h1>
    </div>
    
    <!-- 销售大洲选择 -->
    <div v-if="continents && continents.length > 0" class="hlwidc-continents-section">
      <div v-if="isMobile" class="hlwidc-mobile-select-wrapper">
        <el-select
          v-model="selectedContinent"
          placeholder="请选择销售大洲"
          size="large"
          class="hlwidc-mobile-select"
          @change="selectContinent"
          value-key="name"
        >
          <el-option
            v-for="continent in continents"
            :key="continent.name"
            :label="`${continent.name} (${continent.countries?.length || 0} 个地区)`"
            :value="continent"
          />
        </el-select>
      </div>
      <div v-else class="hlwidc-continents-buttons">
        <button 
          v-for="continent in continents" 
          :key="continent.name"
          @click="selectContinent(continent)"
          :class="['hlwidc-continent-btn', { active: selectedContinent && selectedContinent.name === continent.name }]"
        >
          {{ continent.name }}
          <span class="hlwidc-country-count">({{ continent.countries?.length || 0 }} 个地区)</span>
        </button>
      </div>
    </div>

    <!-- 国家选择 -->
    <div v-if="selectedContinent && selectedContinentCountries && selectedContinentCountries.length > 0" class="hlwidc-countries-section">
      <div v-if="isMobile" class="hlwidc-mobile-select-wrapper">
        <el-select
          v-model="selectedCountry"
          placeholder="请选择国家/地区"
          size="large"
          class="hlwidc-mobile-select"
          @change="selectCountry"
          value-key="flag"
        >
          <el-option
            v-for="country in selectedContinentCountries"
            :key="country.flag"
            :label="country.name"
            :value="country"
          />
        </el-select>
      </div>
      <div v-else class="hlwidc-countries-buttons">
        <button 
          v-for="country in selectedContinentCountries" 
          :key="country.flag"
          @click="selectCountry(country)"
          :class="['hlwidc-country-btn', { active: selectedCountry && selectedCountry.flag === country.flag }]"
        >
          {{ country.name }}
        </button>
      </div>
    </div>

    <!-- 省份选择 -->
    <div v-if="selectedCountry && selectedCountryProvinces && selectedCountryProvinces.length > 1"  class="hlwidc-provinces-section">
      <div v-if="isMobile" class="hlwidc-mobile-select-wrapper">
        <el-select
          v-model="selectedProvince"
          placeholder="请选择省份"
          size="large"
          class="hlwidc-mobile-select"
          @change="selectProvince"
          value-key="flag"
        >
          <el-option
            v-for="province in selectedCountryProvinces"
            :key="province.flag"
            :label="`${province.name} (${province.zones?.length || 0} 个可用区)`"
            :value="province"
          />
        </el-select>
      </div>
      <div v-else class="hlwidc-provinces-buttons">
        <button 
          v-for="province in selectedCountryProvinces" 
          :key="province.flag"
          @click="selectProvince(province)"
          :class="['hlwidc-province-btn', { active: selectedProvince && selectedProvince.flag === province.flag }]"
        >
          {{ province.name }}
          <span class="hlwidc-zone-count">({{ province.zones?.length || 0 }} 个可用区)</span>
        </button>
      </div>
    </div>
    
    <!-- 可用区选择 -->
    <div v-if="selectedProvince && selectedProvinceZones && selectedProvinceZones.length > 0" class="hlwidc-zones-section">
      <div v-if="isMobile" class="hlwidc-mobile-select-wrapper">
        <el-select
          v-model="selectedZone"
          placeholder="请选择可用区"
          size="large"
          class="hlwidc-mobile-select"
          @change="selectZone"
          value-key="id"
        >
          <el-option
            v-for="zone in selectedProvinceZones"
            :key="zone.id"
            :label="`${zone.name} (${zone.city}) ${zone.area ? `(${zone.area})` : ''}`"
            :value="zone"
          />
        </el-select>
      </div>
      <div v-else class="hlwidc-zones-buttons">
        <button 
          v-for="zone in selectedProvinceZones" 
          :key="zone.id"
          @click="selectZone(zone)"
          :class="['hlwidc-zone-btn', { active: selectedZone && selectedZone.id === zone.id }]"
        >
          <div class="hlwidc-zone-name">{{ zone.name }}</div>
          <div class="hlwidc-zone-city">{{ zone.city }}</div>
          <div v-if="zone.area" class="hlwidc-zone-area">{{ zone.area }}</div>
        </button>
      </div>
    </div>

    <!-- 功能参数 -->
    <div v-if="selectedZone && selectedZone.features && selectedZone.features.length > 0" class="hlwidc-features-section">
      <h3 class="hlwidc-features-title">功能参数</h3>
      <div class="hlwidc-features-grid">
        <div v-for="feature in selectedZone.features" :key="feature.target_id" class="hlwidc-feature-item">
          <div class="hlwidc-feature-name">{{ feature.name }}</div>
          <div class="hlwidc-feature-status" :class="getFeatureStatusClass(feature.explain)">
            {{ feature.explain }}
          </div>
        </div>
      </div>
    </div>

    <!-- 产品列表 -->
    <div v-if="selectedContinent">
      <div v-if="error" class="hlwidc-error">
        <p>{{ error }}</p>
      </div>
      <template v-else>
        <el-table
          v-if="products && products.length > 0"
          :data="products"
          v-loading="loading"
          style="width: 100%"
          class="hlwidc-products-el-table"
          border
        >
          <el-table-column label="配置" min-width="80px" align="center">
            <template #default="scope">
              <div class="hlwidc-config-info">
                <span class="hlwidc-config-name">{{ scope.row.cpu }}核{{ scope.row.ram / 1024 }}G</span>
              </div>
            </template>
          </el-table-column>

          <el-table-column label="系统盘" min-width="100px" align="center">
            <template #default="scope">
              <span v-if="scope.row.disk === scope.row.diskMax">
                {{ scope.row.disk }}G {{ scope.row.diskMedia }}
              </span>
              <span v-else>
                {{ scope.row.disk }}-{{ scope.row.diskMax }}G {{ scope.row.diskMedia }}
              </span>
            </template>
          </el-table-column>

          <el-table-column label="数据盘" min-width="100px" align="center">
            <template #default="scope">
              <span v-if="scope.row.diskData === scope.row.diskDataMax">
                {{ scope.row.diskData }}G
              </span>
              <span v-else>
                {{ scope.row.diskData }}-{{ scope.row.diskDataMax }}G
              </span>
            </template>
          </el-table-column>

          <el-table-column label="带宽" min-width="100px" align="center">
            <template #default="scope">
              <span v-if="!scope.row.bandwidth || scope.row.bandwidth === 0" class="hlwidc-unlimited-bandwidth">不限</span>
              <span v-else-if="scope.row.bandwidth === scope.row.bandwidthMax">
                {{ scope.row.bandwidth }}Mbps
              </span>
              <span v-else>
                {{ scope.row.bandwidth }}-{{ scope.row.bandwidthMax }}Mbps
              </span>
            </template>
          </el-table-column>

          <el-table-column label="流量" min-width="100px" align="center">
            <template #default="scope">
              <span v-if="scope.row.traffic && scope.row.traffic > 0">
                {{ scope.row.traffic }}GB
              </span>
              <span v-else class="hlwidc-unlimited-traffic">不限</span>
            </template>
          </el-table-column>

          <el-table-column label="月付" min-width="100px" align="center">
            <template #default="scope">
              <span v-if="scope.row.minPaymentCycle&&scope.row.minPaymentCycle > 1" class="hlwidc-price-disabled">-</span>
              <span v-else class="hlwidc-price-month">{{ getCurrencySymbol(currencyUnit) }}{{ formatPrice(scope.row.price) }}</span>
            </template>
          </el-table-column>

          <el-table-column label="季付" min-width="100px" align="center">
            <template #default="scope">
              <span v-if="scope.row.minPaymentCycle&&scope.row.minPaymentCycle > 2" class="hlwidc-price-disabled">-</span>
              <span v-else-if="scope.row.minPaymentCycle&&scope.row.minPaymentCycle > 1" class="hlwidc-price-quarter">{{ getCurrencySymbol(currencyUnit) }}{{ formatPrice(scope.row.priceQuarter) }}</span>
              <span v-else class="hlwidc-price-disabled">{{ getCurrencySymbol(currencyUnit) }}{{ formatPrice(scope.row.priceQuarter) }}</span>
            </template>
          </el-table-column>

          <el-table-column label="年付" min-width="100px" align="center">
            <template #default="scope">
              <span class="hlwidc-price-disabled">{{ getCurrencySymbol(currencyUnit) }}{{ formatPrice(scope.row.priceYear) }}</span>
            </template>
          </el-table-column>

          <el-table-column label="Windows" min-width="100px" align="center">
            <template #default="scope">
              <el-tag
                :type="scope.row.noWindows === 1 ? 'danger' : 'success'"
                size="small"
              >
                {{ scope.row.noWindows === 1 ? '不支持' : '支持' }}
              </el-tag>
            </template>
          </el-table-column>

          <el-table-column label="操作" min-width="100px" align="center" fixed="right">
            <template #default="scope">
              <el-button
                type="primary"
                size="small"
                :disabled="!!scope.row.outOfStock"
                @click="openOrderModal(scope.row)"
              >
                订购
              </el-button>
            </template>
          </el-table-column>
        </el-table>
        <div v-else class="hlwidc-no-products">
          <p>该地区产品补货中</p>
        </div>
      </template>
    </div>
    <!-- 未选择大洲时的提示 -->
    <div v-else class="hlwidc-content">
      <div class="hlwidc-select-hint">
        <h3>请先选择销售大洲</h3>
        <p>选择上方的大洲按钮来查看该地区的产品</p>
      </div>
    </div>

    <!-- 订购弹窗 -->
    <OrderModal
      :visible="showOrderModal"
      :product="selectedProduct"
      :currency-symbol="getCurrencySymbol(currencyUnit)"
      :format-price="formatPrice"
      :continent="selectedContinent"
      :country="selectedCountry"
      :province="selectedProvince"
      :zone="selectedZone"
      @close="closeOrderModal"
      @confirm="handleOrderConfirm"
      v-if="selectedProduct"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, watch, onUnmounted } from 'vue'
import { zhaomuApiService } from '@/services/client'
import { getCurrencySymbol } from '@/config/currencies'
import OrderModal from '@/components/UserOrderModal.vue'
import { ElTable, ElTableColumn, ElButton, ElTag, ElSelect, ElOption } from 'element-plus'

// 响应式数据
const loading = ref(false)
const error = ref<string | null>(null)
const continents = ref<any[]>([])
const selectedContinent = ref<any>(null)
const selectedContinentCountries = ref<any[]>([])
const selectedCountry = ref<any>(null)
const selectedCountryProvinces = ref<any[]>([])
const selectedProvince = ref<any>(null)
const selectedProvinceZones = ref<any[]>([])
const selectedZone = ref<any>(null)
const products = ref<any[]>([])

// 移动端状态
const isMobile = ref(window.innerWidth <= 768)

const handleResize = () => {
  isMobile.value = window.innerWidth <= 768
}

onMounted(() => {
  window.addEventListener('resize', handleResize)
})

onUnmounted(() => {
  window.removeEventListener('resize', handleResize)
})

// 配置数据
const config = ref<any>(null)
const currencyUnit = ref<string>('CNY')

// 订购弹窗
const showOrderModal = ref<boolean>(false)
const selectedProduct = ref<any>(null)

// 页面加载时先获取配置，再获取大洲列表
onMounted(async () => {
  await loadConfig()
  await loadContinents()
})



// 加载配置
const loadConfig = async () => {
  try {
    console.log('开始加载配置...')
    const response = await zhaomuApiService.getAllConfig()
    console.log('获取配置响应:', response)
    
    if (response && response.code === 1 && response.data) {
      config.value = response.data
      
      // 加载汇率设置
      if (response.data.exchangeSettings) {
        currencyUnit.value = response.data.exchangeSettings.currencyUnit || 'CNY'
        console.log('已加载汇率设置:', { currencyUnit: currencyUnit.value })
      }
    } else {
      // 如果获取失败，使用默认值
      currencyUnit.value = 'CNY'
      console.log('使用默认配置:', { currencyUnit: currencyUnit.value })
    }
  } catch (err) {
    console.error('获取配置错误:', err)
    // 获取失败时使用默认值
    currencyUnit.value = 'CNY'
  }
}

// 获取大洲列表
const loadContinents = async () => {
  try {
    loading.value = true
    error.value = null
    
    const response = await zhaomuApiService.getRegions(true)
    console.log('获取大洲响应:', response)
    
    if (response && Array.isArray(response)) {
      continents.value = response
    } else if (response && response.data && Array.isArray(response.data)) {
      continents.value = response.data
    } else if (response && response.code === 1 && response.data) {
      continents.value = response.data
    } else {
      throw new Error('未获取到有效的大洲数据')
    }
    
    if (continents.value.length === 0) {
      throw new Error('未获取到有效的大洲数据')
    }
    
    // 默认选中第一个大洲
    if (continents.value.length > 0) {
      selectedContinent.value = continents.value[0]
      await loadProducts(continents.value[0].name)
    }
    
  } catch (err) {
    console.error('获取大洲列表错误:', err)
    error.value = err instanceof Error ? err.message : '未知错误'
  } finally {
    loading.value = false
  }
}

// 选择大洲
const selectContinent = (continent: any) => {
  // 清空下级选择
  selectedCountry.value = null
  selectedCountryProvinces.value = []
  selectedProvince.value = null
  selectedProvinceZones.value = []
  selectedZone.value = null
  
  selectedContinent.value = continent
  // 手动触发下级选择逻辑
  if (continent && continent.countries) {
    selectedContinentCountries.value = continent.countries.map((c: any) => {
      return {
        ...c,
        flag: `${continent.name}-${c.name}`
      }
    })
    console.log('大洲选择变化:', selectedContinentCountries.value)
    if (selectedContinentCountries.value.length > 0) {
      selectedCountry.value = selectedContinentCountries.value[0]
    }
  }
}

// 选择国家
const selectCountry = (country: any) => {
  // 清空下级选择
  selectedProvince.value = null
  selectedProvinceZones.value = []
  selectedZone.value = null
  
  selectedCountry.value = country
  console.log('国家选择变化:', selectedCountry.value)
  // 手动触发下级选择逻辑
  if (country && country.provinces) {
    selectedCountryProvinces.value = country.provinces.map((p: any) => {
      return {
        ...p,
        flag: `${country.flag}-${p.name}`
      }
    })
    console.log('省份选择变化:', selectedCountryProvinces.value)
    if (selectedCountryProvinces.value.length > 0) {
      selectedProvince.value = selectedCountryProvinces.value[0]
    }
  }
}

// 选择省份
const selectProvince = (province: any) => {
  // 清空下级选择
  selectedZone.value = null
  
  selectedProvince.value = province
  // 手动触发下级选择逻辑
  if (province && province.zones) {
    selectedProvinceZones.value = province.zones
    console.log('省份选择变化:', selectedProvinceZones.value)
    if (selectedProvinceZones.value.length > 0) {
      selectedZone.value = selectedProvinceZones.value[0]
    }
  }
}

// 选择可用区
const selectZone = (zone: any) => {
  selectedZone.value = zone
  // watch 监听器会自动调用 loadProductsByZone
}




// 加载产品（根据可用区ID）
const loadProductsByZone = async (zone: any) => {
  try {
    loading.value = true
    error.value = null

    console.log('获取产品列表，可用区:', zone)
    
    // 并行获取产品列表和功能参数
    const [productsResponse, featuresResponse] = await Promise.all([
      zhaomuApiService.getProductsByRegion(zone.id.toString()),
      zhaomuApiService.getRegionFeatureComparison(zone.id.toString())
    ])
    
    console.log('获取产品响应:', productsResponse)
    console.log('获取功能参数响应:', featuresResponse)

    if (productsResponse && productsResponse.code === 1 && productsResponse.data) {
      products.value = productsResponse.data
    } else if (productsResponse && Array.isArray(productsResponse)) {
      products.value = productsResponse
    } else {
      throw new Error('未获取到有效的产品数据')
    }

    // 将功能参数数据存储到可用区对象中
    if (featuresResponse && featuresResponse.code === 1 && featuresResponse.data) {
      zone.features = featuresResponse.data
      console.log('可用区功能参数:', zone.features)
    }

  } catch (err) {
    console.error('获取产品错误:', err)
    error.value = err instanceof Error ? err.message : '未知错误'
    // 如果获取失败，显示模拟数据
    products.value = [
      {
        id: 1,
        name: `可用区 ${zone.name} 基础套餐`,
        price: '29.99',
        description: '适合个人用户的基础套餐'
      },
      {
        id: 2,
        name: `可用区 ${zone.name} 专业套餐`,
        price: '59.99',
        description: '适合企业用户的专业套餐'
      },
      {
        id: 3,
        name: `可用区 ${zone.name} 高级套餐`,
        price: '99.99',
        description: '适合大型企业的高级套餐'
      }
    ]
  } finally {
    loading.value = false
  }
}

// 加载产品（旧方法，保留兼容性）
const loadProducts = async (continentName: string) => {
  // 如果没有选择可用区，显示提示
  if (!selectedZone.value) {
    products.value = []
    return
  }
  
  // 使用可用区对象获取产品
  await loadProductsByZone(selectedZone.value)
}


// 监听大洲选择变化
watch(selectedContinent, (newContinent) => {
  if (newContinent) {
    selectContinent(newContinent)
  }
}, { immediate: true })

// 监听国家选择变化
watch(selectedCountry, (newCountry) => {
  if (newCountry) {
    selectCountry(newCountry)
  }
}, { immediate: true })

// 监听省份选择变化
watch(selectedProvince, (newProvince) => {
  if (newProvince) {
    selectProvince(newProvince)
  }
}, { immediate: true })

// 格式化价格（直接显示原价）
const formatPrice = (price: number | string) => {
  if (!price) return '0'
  
  const numPrice = typeof price === 'string' ? parseFloat(price) : price
  if (isNaN(numPrice)) return '0'
  
  // 直接返回原价，保留2位小数
  return numPrice.toFixed(2)
}

// 打开订购弹窗
const openOrderModal = (product: any) => {
  selectedProduct.value = product
  showOrderModal.value = true
}

// 关闭订购弹窗
const closeOrderModal = () => {
  showOrderModal.value = false
  selectedProduct.value = null
}

// 处理订购确认
const handleOrderConfirm = (orderData: any) => {
  console.log('订购确认:', orderData)
  
  // 这里可以调用API提交订单
  // 例如：await zhaomuApiService.submitOrder(orderData)
  
  // 构建地理位置信息
  const locationPath = []
  if (orderData.location.continent) locationPath.push(orderData.location.continent.name)
  if (orderData.location.country) locationPath.push(orderData.location.country.name)
  if (orderData.location.province) locationPath.push(orderData.location.province.name)
  if (orderData.location.zone) locationPath.push(orderData.location.zone.name)
  
  // 显示成功消息
  alert(`订购成功！\n产品：${orderData.product.name}\n部署位置：${locationPath.join(' → ')}\n周期：${orderData.cycle === 2 ? '月付' : orderData.cycle === 3 ? '季付' : '年付'}\n数量：${orderData.quantity}\n总价：${orderData.totalPrice}`)
  
  // 关闭弹窗
  closeOrderModal()
}

// 监听可用区选择变化
watch(selectedZone, (newZone) => {
  if (newZone) {
    loadProductsByZone(newZone)
  }
}, { immediate: true })

// 获取功能状态样式类
const getFeatureStatusClass = (explain: string) => {
  if (explain === '支持') {
    return 'supported'
  } else if (explain === '不支持') {
    return 'not-supported'
  } else if (explain.includes('提交工单')) {
    return 'ticket-required'
  } else if (explain.includes('小时')) {
    return 'time-limited'
  } else if (explain.includes('IP')) {
    return 'ip-info'
  } else {
    return 'other'
  }
}

</script>

<style scoped>
.hlwidc-products-page {
  padding: 30px;
}

.hlwidc-page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 30px;
}

.hlwidc-page-header h1 {
  margin: 0;
  font-size: 2rem;
  color: #1f2937;
}


/* 大洲选择区域 */
.hlwidc-continents-section {
  margin-bottom: 30px;
}

.hlwidc-continents-section h2 {
  margin: 0 0 20px 0;
  font-size: 1.5rem;
  color: #374151;
}

.hlwidc-continents-buttons {
  display: flex;
  flex-wrap: wrap;
  gap: 12px;
}

.hlwidc-continent-btn {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 16px 20px;
  background: white;
  border: 2px solid #e5e7eb;
  border-radius: 12px;
  cursor: pointer;
  transition: all 0.3s ease;
  min-width: 120px;
  text-align: center;
}

.hlwidc-continent-btn:hover {
  border-color: #3b82f6;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
}

.hlwidc-continent-btn.active {
  background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
  border-color: #3b82f6;
  color: white;
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(59, 130, 246, 0.3);
}

.hlwidc-continent-btn span {
  font-size: 0.875rem;
  margin-top: 4px;
  opacity: 0.8;
}

.hlwidc-country-count {
  font-size: 0.75rem;
  opacity: 0.7;
}

/* 国家选择区域 */
.hlwidc-countries-section {
  margin-bottom: 20px;
}

.hlwidc-countries-section h2 {
  margin: 0 0 15px 0;
  font-size: 1.25rem;
  color: #374151;
}

.hlwidc-countries-buttons {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}

.hlwidc-country-btn {
  padding: 8px 16px;
  background: white;
  border: 2px solid #e5e7eb;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.3s ease;
  font-size: 0.875rem;
  font-weight: 500;
  color: #374151;
  min-width: 80px;
  text-align: center;
}

.hlwidc-country-btn:hover {
  border-color: #3b82f6;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
}

.hlwidc-country-btn.active {
  background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
  border-color: #3b82f6;
  color: white;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

/* 省份选择区域 */
.hlwidc-provinces-section {
  margin-bottom: 20px;
}

.hlwidc-provinces-section h2 {
  margin: 0 0 15px 0;
  font-size: 1.25rem;
  color: #374151;
}

.hlwidc-provinces-buttons {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}

.hlwidc-province-btn {
  padding: 8px 16px;
  background: white;
  border: 2px solid #e5e7eb;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.3s ease;
  font-size: 0.875rem;
  font-weight: 500;
  color: #374151;
  min-width: 100px;
  text-align: center;
  display: flex;
  flex-direction: row;
  align-items: center;
  gap: 4px;
}

.hlwidc-province-btn:hover {
  border-color: #3b82f6;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
}

.hlwidc-province-btn.active {
  background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
  border-color: #3b82f6;
  color: white;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

.hlwidc-zone-count {
  font-size: 0.75rem;
  opacity: 0.7;
}

/* 可用区选择区域 */
.hlwidc-zones-section {
  margin-bottom: 20px;
}

.hlwidc-zones-section h2 {
  margin: 0 0 15px 0;
  font-size: 1.25rem;
  color: #374151;
}

.hlwidc-zones-buttons {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}

.hlwidc-zone-btn {
  padding: 10px 16px;
  background: white;
  border: 2px solid #e5e7eb;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.3s ease;
  text-align: center;
  min-width: 120px;
  display: flex;
  flex-direction: row;
  align-items: center;
  gap: 4px;
}

.hlwidc-zone-btn:hover {
  border-color: #3b82f6;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
}

.hlwidc-zone-btn.active {
  background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
  border-color: #3b82f6;
  color: white;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

.hlwidc-zone-name {
  font-size: 0.875rem;
  font-weight: 600;
  margin-bottom: 2px;
}

.hlwidc-zone-city {
  font-size: 0.75rem;
  opacity: 0.8;
  margin-bottom: 1px;
}

.hlwidc-zone-area {
  font-size: 0.625rem;
  opacity: 0.6;
}

/* 内容区域 */
.hlwidc-content {
  background: white;
  border-radius: 12px;
  padding: 30px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  border: 1px solid #e5e7eb;
}

.hlwidc-content h3 {
  margin: 0 0 20px 0;
  font-size: 1.25rem;
  color: #374151;
}

/* 产品表格 */
.hlwidc-products-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 0.875rem;
  table-layout: fixed;
  overflow-x: auto;
  border-radius: 12px;
  border: 1px solid #e5e7eb;
  background: white;
  position: relative;
}

.hlwidc-products-table thead {
  background: #f8fafc;
  border-bottom: 2px solid #e5e7eb;
}

.hlwidc-products-table th {
  padding: 16px 12px;
  text-align: center;
  font-weight: 600;
  color: #374151;
  white-space: nowrap;
  vertical-align: top;
}

.hlwidc-products-table td {
  padding: 16px 12px;
  border-bottom: 1px solid #f3f4f6;
  vertical-align: middle;
}

.hlwidc-product-row:hover {
  background: #f8fafc;
}

.hlwidc-config-cell {
  width: 15%;
  min-width: 120px;
}

.hlwidc-system-disk-cell {
  width: 12%;
}

.hlwidc-data-disk-cell {
  width: 12%;
}

.hlwidc-bandwidth-cell {
  width: 10%;
}

.hlwidc-traffic-cell {
  width: 10%;
}

.hlwidc-price-cell {
  width: 12%;
}

.hlwidc-windows-cell {
  width: 10%;
}

.hlwidc-actions-cell {
  width: 9%;
}

.hlwidc-config-info {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.hlwidc-config-name {
  font-weight: 600;
  color: #1f2937;
}

.hlwidc-config-disk {
  font-size: 0.75rem;
  color: #6b7280;
}

.hlwidc-system-disk-cell,
.hlwidc-data-disk-cell,
.hlwidc-bandwidth-cell,
.hlwidc-traffic-cell {
  text-align: center;
  font-weight: 500;
  color: #374151;
}

.hlwidc-no-data-disk {
  color: #9ca3af;
  font-style: italic;
}

.hlwidc-price-cell {
  text-align: center;
  font-weight: 600;
}

.hlwidc-price-month {
  color: #dc2626;
  font-size: 1rem;
}

.hlwidc-price-quarter {
  color: #dc2626;
  font-size: 0.875rem;
}

.hlwidc-price-year {
  color: #dc2626;
  font-size: 0.875rem;
}

.hlwidc-price-disabled {
  color: #9ca3af;
  font-size: 0.875rem;
}

.hlwidc-windows-cell {
  min-width: 80px;
  text-align: center;
}

.hlwidc-windows-tag {
  display: inline-block;
  padding: 2px 8px;
  border-radius: 4px;
  font-size: 0.75rem;
  font-weight: 500;
}

.hlwidc-windows-tag.supported {
  background: #dcfce7;
  color: #166534;
}

.hlwidc-windows-tag.not-supported {
  background: #fef2f2;
  color: #dc2626;
}

.hlwidc-unlimited-traffic,
.hlwidc-unlimited-bandwidth {
  color: #059669;
  font-weight: 600;
}

.hlwidc-actions-cell {
  text-align: center;
}

.hlwidc-order-btn {
  padding: 6px 16px;
  background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-size: 0.875rem;
  font-weight: 500;
  transition: all 0.3s ease;
}

.hlwidc-order-btn:hover:not(:disabled) {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

.hlwidc-order-btn:disabled {
  background: #9ca3af;
  cursor: not-allowed;
  transform: none;
  box-shadow: none;
}

/* 状态样式 */

.hlwidc-error {
  background: #fef2f2;
  color: #dc2626;
  padding: 16px;
  border-radius: 8px;
  border: 1px solid #fecaca;
}

.hlwidc-no-products {
  text-align: center;
  padding: 40px;
  color: #6b7280;
}

.hlwidc-select-hint {
  text-align: center;
  padding: 40px;
  color: #6b7280;
}

.hlwidc-select-hint h3 {
  margin: 0 0 8px 0;
  color: #374151;
}

.hlwidc-select-hint p {
  margin: 0;
  font-size: 0.875rem;
}

/* 功能参数样式 */
.hlwidc-features-section {
  background: white;
  border-radius: 12px;
  padding: 24px;
  margin-bottom: 30px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  border: 1px solid #e5e7eb;
}

.hlwidc-features-title {
  margin: 0 0 20px 0;
  font-size: 1.25rem;
  color: #374151;
  font-weight: 600;
}

.hlwidc-features-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 12px;
}

.hlwidc-feature-item {
  display: flex;
  flex-direction: row;
  justify-content: space-between;
  align-items: center;
  padding: 8px 16px;
  background: #f8fafc;
  border-radius: 6px;
  border: 1px solid #e5e7eb;
  transition: all 0.2s ease;
  min-height: 40px;
}

.hlwidc-feature-item:hover {
  background: #f1f5f9;
  border-color: #cbd5e1;
}

.hlwidc-feature-name {
  font-size: 0.875rem;
  font-weight: 600;
  color: #374151;
  flex: 1;
}

.hlwidc-feature-status {
  font-size: 0.75rem;
  padding: 4px 8px;
  border-radius: 4px;
  font-weight: 500;
  text-align: center;
  min-width: 60px;
  white-space: nowrap;
}

.hlwidc-feature-status.supported {
  background: #dcfce7;
  color: #166534;
}

.hlwidc-feature-status.not-supported {
  background: #fef2f2;
  color: #dc2626;
}

.hlwidc-feature-status.ticket-required {
  background: #fef3c7;
  color: #d97706;
}

.hlwidc-feature-status.time-limited {
  background: #dbeafe;
  color: #1d4ed8;
}

.hlwidc-feature-status.ip-info {
  background: #e0e7ff;
  color: #5b21b6;
}

.hlwidc-feature-status.other {
  background: #f3f4f6;
  color: #6b7280;
}

/* 响应式设计 */
@media (max-width: 768px) {
  .hlwidc-continents-buttons {
    flex-direction: row; /* 改为 row */
    flex-wrap: wrap; /* 允许换行 */
    justify-content: space-between; /* 按钮之间留有空间 */
  }
  
  .hlwidc-continent-btn {
    min-width: unset; /* 移除最小宽度 */
    width: calc(50% - 6px); /* 每行两个，减去一半的gap */
  }
  
  .hlwidc-countries-buttons {
    flex-direction: row; /* 改为 row */
    flex-wrap: wrap; /* 允许换行 */
    justify-content: space-between; /* 按钮之间留有空间 */
  }
  
  .hlwidc-country-btn {
    min-width: unset; /* 移除最小宽度 */
    width: calc(50% - 4px); /* 每行两个，减去一半的gap */
  }
  
  .hlwidc-provinces-buttons {
    flex-direction: column;
  }
  
  .hlwidc-province-btn {
    min-width: auto;
    width: 100%;
  }
  
  .hlwidc-zones-buttons {
    flex-direction: row; /* 改为 row */
    flex-wrap: wrap; /* 允许换行 */
    justify-content: space-between; /* 按钮之间留有空间 */
  }
  
  .hlwidc-zone-btn {
    min-width: unset; /* 移除最小宽度 */
    width: calc(50% - 4px); /* 每行两个，减去一半的gap */
  }
  
  .hlwidc-products-el-table {
    font-size: 0.75rem; /* 应用到 el-table */
  }
  
  .hlwidc-products-table th,
  .hlwidc-products-table td {
    padding: 8px 6px;
  }
  
  .hlwidc-config-cell {
    min-width: 180px;
  }
  
  .hlwidc-tags-cell {
    min-width: 80px;
  }

  .hlwidc-features-grid {
    grid-template-columns: 1fr;
    gap: 8px;
  }

  .hlwidc-feature-item {
    padding: 6px 12px;
    min-height: 36px;
  }

  .hlwidc-feature-name {
    font-size: 0.8rem;
  }

  .hlwidc-feature-status {
    font-size: 0.7rem;
    padding: 2px 6px;
    min-width: 50px;
  }
}
</style>
