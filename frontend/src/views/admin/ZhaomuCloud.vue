<template>
  <div class="hlwidc-zhaomu-cloud">
    <h1>朝暮云管理系统</h1>

      <!-- 配置设置区域 -->
      <div class="hlwidc-config-sections">
        <!-- 导航菜单设置 -->
        <div class="hlwidc-config-item">
          <label class="hlwidc-config-label">导航菜单</label>
          <select v-model="selectedNavigation" class="hlwidc-config-select" :disabled="loading">
            <option value="">请选择导航菜单</option>
            <option v-for="nav in navigationOptions" :key="nav.id" :value="nav.id">
              {{ nav.name }}
            </option>
          </select>
        </div>

        <!-- 销售折扣设置 -->
        <div class="hlwidc-config-item">
          <label class="hlwidc-config-label">销售折扣 (%)</label>
          <input v-model="salesDiscount" type="number" min="0" step="1" placeholder="请输入折扣百分比"
            class="hlwidc-config-input" :disabled="loading" />
        </div>

        <!-- 实名认证设置 -->
        <div class="hlwidc-config-item">
          <label class="hlwidc-config-label">实名认证</label>
          <label class="hlwidc-config-switch">
            <input type="checkbox" v-model="realNameAuthRequired" :disabled="loading" />
            <span class="hlwidc-switch-slider"></span>
            <span class="hlwidc-switch-label">{{ realNameAuthRequired ? '需要实名认证购买' : '不需要实名认证购买' }}</span>
          </label>
        </div>

        <!-- 汇率设置 -->
        <div class="hlwidc-config-item">
          <label class="hlwidc-config-label">汇率设置</label>
          <div class="hlwidc-exchange-inputs">
            <input v-model="exchangeRate" type="number" min="0.000001" max="1000" step="0.000001" placeholder="请输入汇率"
              class="hlwidc-config-input" :disabled="loading" />
            <select v-model="currencyUnit" class="hlwidc-config-select" :disabled="loading">
              <option v-for="currency in currencies" :key="currency.code" :value="currency.code">
                {{ currency.symbol }} {{ currency.name }}
              </option>
            </select>
          </div>
        </div>
      </div>

      <!-- 功能参数比较设置 -->
      <div class="hlwidc-comparison-section">
        <div class="hlwidc-comparison-header">
          <h3>功能参数(先去购买中多点几个区域载入参数后在此隐藏不想显示的)</h3>
          <div class="hlwidc-comparison-controls">
            <button @click="selectAllFeatures" :disabled="loading" class="hlwidc-control-btn">
              全选
            </button>
            <button @click="selectNoneFeatures" :disabled="loading" class="hlwidc-control-btn">
              全不选
            </button>
            <button @click="saveComparisonSettings" :disabled="loading" class="hlwidc-save-btn">
              保存设置
            </button>
          </div>
        </div>
        
        <div class="hlwidc-features-grid">
          <div v-for="feature in comparisonFeatures" :key="feature.name" class="hlwidc-feature-item">
            <label class="hlwidc-feature-label">
              <input type="checkbox" v-model="feature.use" :disabled="loading" />
              <span class="hlwidc-feature-name">{{ feature.name }}</span>
            </label>
          </div>
        </div>
      </div>

      <!-- 统一提交按钮 -->
      <div class="hlwidc-submit-section">
        <button @click="saveAllConfig" :disabled="loading" class="hlwidc-submit-btn"
          :class="{ 'hlwidc-success-btn': allSaveStatus === 'success' }">
          {{ allSaveStatus === 'success' ? '保存成功' : '保存所有设置' }}
        </button>
      </div>
    
    <!-- 所有大洲和国家列表 -->
      <div v-if="continents.length > 0" class="hlwidc-all-countries-section">

      <!-- 汇率说明 -->
      <div class="hlwidc-exchange-rate-notice">
        <div class="hlwidc-notice-icon">💱</div>
        <div class="hlwidc-notice-content">
          <strong>当前价格是人民币价格</strong>
          <p>如果您的系统为非人民币，请设置相应的汇率和价格单位</p>
        </div>
      </div>
      
      <!-- 全选控制 -->
      <div class="hlwidc-select-controls">
        <button @click="selectAll" :disabled="loading" class="hlwidc-control-btn">
          全选
        </button>
        <button @click="selectNone" :disabled="loading" class="hlwidc-control-btn">
          全不选
        </button>
        <button @click="selectInverse" :disabled="loading" class="hlwidc-control-btn">
          反选
        </button>
        <span class="hlwidc-selection-info">
          已选择 {{ selectedCountries.length }} / {{ allCountries.length }} 个地区
        </span>
      </div>

      <!-- 大洲和国家列表 -->
      <div class="hlwidc-continents-list">
        <div v-for="continent in continents" :key="continent.name" class="hlwidc-continent-section">
          <div class="hlwidc-continent-header" @click="toggleContinent(continent.name)">
            <h3>{{ continent.name }}</h3>
            <span class="hlwidc-country-count">{{ continent.countries?.length || 0 }} 个地区</span>
            <span class="hlwidc-continent-actions" @click.stop>
              <button class="hlwidc-link-btn" @click.stop="selectContinentAll(continent.name)">全选</button>
              <button class="hlwidc-link-btn" @click.stop="invertContinentSelection(continent.name)">反选</button>
            </span>
            <span class="hlwidc-toggle-icon" :class="{ expanded: expandedContinents.includes(continent.name) }">
              ▼
            </span>
          </div>
          
          <div v-if="expandedContinents.includes(continent.name)" class="hlwidc-countries-list">
            <label v-for="country in continent.countries || []" :key="`${continent.name}-${country.name}`"
              class="hlwidc-country-item">
              <input type="checkbox" :value="country.name" v-model="selectedCountries" />
              <span>{{ country.name }}</span>
            </label>
          </div>
        </div>
      </div>
      
      <!-- 操作按钮 -->
      <div class="hlwidc-actions">
        <button @click="saveCountries" :disabled="loading || selectedCountries.length === 0" class="hlwidc-save-btn">
          {{ loading ? '保存中...' : `保存选中的 ${selectedCountries.length} 个地区` }}
        </button>
        <button @click="clearSelection" :disabled="loading" class="hlwidc-clear-btn">
          清空选择
        </button>
      </div>
    </div>

    <!-- 加载状态 -->
    <div v-if="loading" class="hlwidc-loading">
      <p>加载中...</p>
    </div>

    <!-- 错误信息 -->
    <div v-if="error" class="hlwidc-error">
      <h3>错误信息:</h3>
      <pre>{{ error }}</pre>
    </div>

    <!-- 成功信息 -->
    <div v-if="successMessage" class="hlwidc-success">
      <h3>操作成功:</h3>
      <p>{{ successMessage }}</p>
    </div>

    <!-- 调试信息 -->
    <div v-if="isDev" class="hlwidc-debug-info">
      <h3>调试信息 (开发环境):</h3>
      <p>大洲数量: {{ continents.length }}</p>
      <p>总地区数量: {{ allCountries.length }}</p>
      <p>选中的地区: {{ selectedCountries.length }} 个</p>
      <p>展开的大洲: {{ expandedContinents.length }} 个</p>
      <p>加载状态: {{ loading ? '加载中' : '已完成' }}</p>
      <p>错误信息: {{ error || '无' }}</p>
      <details>
        <summary>大洲数据详情</summary>
        <pre>{{ JSON.stringify(continents, null, 2) }}</pre>
      </details>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { zhaomuApiService } from '@/services/api'
import type { ApiResponse } from '@/services/api'
import { currencies, getCurrencySymbol } from '@/config/currencies'

const isDev = import.meta.env.VITE_APP_ENV === 'development'
const apiBaseUrl = import.meta.env.VITE_API_BASE_URL || 'http://127.0.0.1:8082'
const isAdmin = window.APP_CONFIG?.isAdmin ?? true
const appVersion = window.APP_CONFIG?.version ?? '1.0.0'

// 响应式数据
const loading = ref(false)
const error = ref<string | null>(null)
const successMessage = ref<string | null>(null)
const continents = ref<any[]>([])
const selectedCountries = ref<string[]>([])
const expandedContinents = ref<string[]>([])
const salesDiscount = ref<number>(0)
const exchangeRate = ref<number>(0)
const currencyUnit = ref<string>('CNY')

// 按钮状态
const discountSaveStatus = ref<'normal' | 'success'>('normal')
const exchangeSaveStatus = ref<'normal' | 'success'>('normal')
const navigationSaveStatus = ref<'normal' | 'success'>('normal')
const realNameAuthSaveStatus = ref<'normal' | 'success'>('normal')

// 导航菜单相关
const navigationOptions = ref<any[]>([])
const selectedNavigation = ref<string>('')
const selectedNavigationName = ref<string>('')

// 实名认证相关
const realNameAuthRequired = ref<boolean>(true)

// 功能参数比较相关
const comparisonFeatures = ref<any[]>([])

// 统一保存相关
const allSaveStatus = ref<'normal' | 'success'>('normal')

// 计算属性：所有国家的列表
const allCountries = computed(() => {
  const countries: string[] = []
  console.log('计算所有国家，大洲数量:', continents.value.length)
  continents.value.forEach((continent, index) => {
    console.log(`大洲 ${index}:`, continent.name, '国家数量:', continent.countries?.length || 0)
    if (continent.countries) {
      continent.countries.forEach((country: any) => {
        countries.push(country.name)
      })
    }
  })
  console.log('计算出的所有国家数量:', countries.length)
  return countries
})

// 页面加载时获取大洲列表
onMounted(async () => {
  await loadRegions()
  // 默认展开所有大洲
  if (continents.value.length > 0) {
    expandedContinents.value = continents.value.map(c => c.name)
  }
  // 加载已选中的国家（延迟执行，确保大洲数据已加载）
  await loadSelectedCountries()
  // 加载所有配置
  await loadAllConfig()
})

// 获取大洲列表
const loadRegions = async () => {
  console.log('开始加载大洲列表...')
  loading.value = true
  error.value = null
  successMessage.value = null
  
  try {
    const response = await zhaomuApiService.getAllRegions(true)
    console.log('API响应:', response) // 调试日志
    
    // 检查不同的响应格式
    if (response && Array.isArray(response)) {
      // 直接是数组格式
      continents.value = response
      console.log('使用直接数组格式，大洲数量:', continents.value.length)
    } else if (response && response.data && Array.isArray(response.data)) {
      // 有data字段的格式
      continents.value = response.data
      console.log('使用data字段格式，大洲数量:', continents.value.length)
    } else if (response && response.code === 1 && response.data) {
      // 标准API响应格式
      continents.value = response.data
      console.log('使用标准API格式，大洲数量:', continents.value.length)
    } else {
      // 尝试直接使用响应数据
      throw new Error('未获取到有效的大洲数据')
    }
    
    // 如果还是没有数据，显示错误
    if (continents.value.length === 0) {
      throw new Error('未获取到有效的大洲数据')
    }
    
    console.log('大洲列表加载完成:', continents.value)

  } catch (err) {
    console.error('获取大洲列表错误:', err)
    error.value = err instanceof Error ? err.message : '未知错误'
  } finally {
    loading.value = false
    console.log('大洲列表加载状态结束，loading:', loading.value)
  }
}

// 切换大洲展开/收起
const toggleContinent = (continentName: string) => {
  const index = expandedContinents.value.indexOf(continentName)
  if (index > -1) {
    expandedContinents.value.splice(index, 1)
  } else {
    expandedContinents.value.push(continentName)
  }
}

// 全选
const selectAll = () => {
  selectedCountries.value = [...allCountries.value]
}

// 全不选
const selectNone = () => {
  selectedCountries.value = []
}

// 反选
const selectInverse = () => {
  const selected = new Set(selectedCountries.value)
  selectedCountries.value = allCountries.value.filter(country => !selected.has(country))
}

// 大洲内全选
const selectContinentAll = (continentName: string) => {
  const continent = continents.value.find((c: any) => c.name === continentName)
  if (!continent || !Array.isArray(continent.countries)) return
  const names: string[] = continent.countries.map((c: any) => c.name)
  const set = new Set(selectedCountries.value)
  names.forEach(n => set.add(n))
  selectedCountries.value = Array.from(set)
}

// 大洲内反选
const invertContinentSelection = (continentName: string) => {
  const continent = continents.value.find((c: any) => c.name === continentName)
  if (!continent || !Array.isArray(continent.countries)) return
  const set = new Set(selectedCountries.value)
  continent.countries.forEach((c: any) => {
    const name = c.name
    if (set.has(name)) {
      set.delete(name)
    } else {
      set.add(name)
    }
  })
  selectedCountries.value = Array.from(set)
}

// 保存选中的国家到缓存
const saveCountries = async () => {
  if (selectedCountries.value.length === 0) return
  
  loading.value = true
  error.value = null
  successMessage.value = null
  
  try {
    const response = await zhaomuApiService.cacheCountries(selectedCountries.value)
    console.log('保存国家响应:', response) // 调试日志
    
    // 检查不同的响应格式
    if (response && response.code === 1) {
      successMessage.value = `成功保存 ${selectedCountries.value.length} 个国家到缓存`
    } else if (response && response.msg) {
      // 有消息字段，可能是成功或失败
      if (response.msg.includes('成功') || response.msg.includes('缓存')) {
        successMessage.value = `成功保存 ${selectedCountries.value.length} 个国家到缓存`
      } else {
        throw new Error(response.msg)
      }
    } else if (response && response.data) {
      // 有数据字段，可能是成功
      successMessage.value = `成功保存 ${selectedCountries.value.length} 个国家到缓存`
    } else {
      // 没有明确的成功标识，但也没有错误，可能是成功
      successMessage.value = `已保存 ${selectedCountries.value.length} 个国家到缓存`
    }
    
  } catch (err) {
    console.error('保存国家错误:', err)
    error.value = err instanceof Error ? err.message : '未知错误'
  } finally {
    loading.value = false
  }
}

// 清空选择
const clearSelection = () => {
  selectedCountries.value = []
  successMessage.value = null
}

// 加载所有配置
const loadAllConfig = async () => {
  try {
    console.log('开始加载所有配置...')
    const response = await zhaomuApiService.getAllConfig()
    console.log('获取配置响应:', response)

    if (response && response.code === 1 && response.data) {
      // 加载销售折扣
      if (response.data.salesDiscount) {
        salesDiscount.value = response.data.salesDiscount.discount || 90
        console.log('已加载销售折扣:', salesDiscount.value)
      }

      // 加载汇率设置
      if (response.data.exchangeSettings) {
        exchangeRate.value = response.data.exchangeSettings.exchangeRate || 1
        currencyUnit.value = response.data.exchangeSettings.currencyUnit || 'CNY'
        console.log('已加载汇率设置:', { exchangeRate: exchangeRate.value, currencyUnit: currencyUnit.value })
      }

      // 加载导航菜单选项
      if (response.data.ptype) {
        navigationOptions.value = response.data.ptype || []
        console.log('已加载导航菜单选项:', navigationOptions.value.length)
      }
      // 加载导航菜单
      if (response.data.menu) {
        selectedNavigation.value = response.data.menu || ''
        console.log('已加载导航菜单:', selectedNavigation.value)
      }

      // 加载实名认证设置
      if (response.data.realNameAuth) {
        realNameAuthRequired.value = response.data.realNameAuth.required
        console.log('已加载实名认证设置:', realNameAuthRequired.value)
      }

      // 加载功能参数比较设置
      if (response.data.comparison && response.data.comparison.data) {
        comparisonFeatures.value = response.data.comparison.data
        console.log('已加载功能参数比较设置:', comparisonFeatures.value.length, '个功能')
      }
    } else {
      // 如果获取失败，使用默认值
      salesDiscount.value = 90
      exchangeRate.value = 1
      currencyUnit.value = 'CNY'
      realNameAuthRequired.value = true // 默认需要实名认证
      console.log('使用默认配置:', { salesDiscount: salesDiscount.value, exchangeRate: exchangeRate.value, currencyUnit: currencyUnit.value, realNameAuth: realNameAuthRequired.value })
    }
  } catch (err) {
    console.error('获取配置错误:', err)
    // 获取失败时使用默认值
    salesDiscount.value = 90
    exchangeRate.value = 1
    currencyUnit.value = 'CNY'
    realNameAuthRequired.value = true // 默认需要实名认证
  }
}

// 保存销售折扣
const saveSalesDiscount = async () => {
  try {
    console.log('开始保存销售折扣:', salesDiscount.value)

    // 验证折扣值
    if (salesDiscount.value < 0) {
      error.value = '销售折扣不能低于0%'
      return
    }

    const response = await zhaomuApiService.setSalesDiscount(salesDiscount.value)
    console.log('保存销售折扣响应:', response)

    if (response && response.code === 1) {
      successMessage.value = `销售折扣已设置为 ${salesDiscount.value}%`
      error.value = null

      // 显示成功状态
      discountSaveStatus.value = 'success'
      setTimeout(() => {
        discountSaveStatus.value = 'normal'
      }, 3000)
    } else {
      error.value = response.msg || '保存销售折扣失败'
    }
  } catch (err) {
    console.error('保存销售折扣错误:', err)
    error.value = '网络错误，请检查连接后重试'
  }
}

// 加载已选中的国家
const loadSelectedCountries = async () => {
  try {
    console.log('开始加载已选中的国家...')
    console.log('当前大洲数据长度:', continents.value.length)

    // 确保大洲数据已加载
    if (continents.value.length === 0) {
      console.log('大洲数据未加载，跳过选中国家加载')
      return
    }

    const response = await zhaomuApiService.getSelectedCountries()
    console.log('获取选中国家响应:', response) // 调试日志

    // 检查不同的响应格式
    if (response && response.code === 1 && response.data) {
      // 标准API响应格式
      selectedCountries.value = response.data
      console.log('已加载选中的国家:', response.data)
    } else if (response && Array.isArray(response)) {
      // 直接是数组格式
      selectedCountries.value = response
      console.log('已加载选中的国家:', response)
    } else if (response && response.data && Array.isArray(response.data)) {
      // 有data字段的格式
      selectedCountries.value = response.data
      console.log('已加载选中的国家:', response.data)
    } else {
      console.log('没有找到已选中的国家数据，保持当前选择')
    }

    console.log('当前选中的国家:', selectedCountries.value)
    console.log('当前大洲数据长度:', continents.value.length)

  } catch (err) {
    console.error('获取选中国家错误:', err)
    // 不显示错误信息，因为这是可选功能
  }
}



// 保存汇率设置
const saveExchangeSettings = async () => {
  try {
    console.log('开始保存汇率设置:', { exchangeRate: exchangeRate.value, currencyUnit: currencyUnit.value })

    // 验证汇率值
    if (exchangeRate.value <= 0) {
      error.value = '汇率必须大于0'
      return
    }

    if (exchangeRate.value > 1000) {
      error.value = '汇率不能超过1000'
      return
    }

    // 验证小数位数（最多6位小数）
    const decimalPlaces = (exchangeRate.value.toString().split('.')[1] || '').length
    if (decimalPlaces > 6) {
      error.value = '汇率最多支持6位小数'
      return
    }

    const response = await zhaomuApiService.setExchangeSettings({
      exchangeRate: exchangeRate.value,
      currencyUnit: currencyUnit.value
    })
    console.log('保存汇率设置响应:', response)

    if (response && response.code === 1) {
      successMessage.value = `汇率设置已保存：1人民币 = ${exchangeRate.value}${getCurrencySymbol(currencyUnit.value)}，价格单位：${currencyUnit.value}`
      error.value = null

      // 显示成功状态
      exchangeSaveStatus.value = 'success'
      setTimeout(() => {
        exchangeSaveStatus.value = 'normal'
      }, 3000)
    } else {
      error.value = response.msg || '保存汇率设置失败'
    }
  } catch (err) {
    console.error('保存汇率设置错误:', err)
    error.value = '网络错误，请检查连接后重试'
  }
}

// 保存导航菜单设置
const saveNavigation = async () => {
  try {
    console.log('开始保存导航菜单设置:', selectedNavigation.value)

    if (!selectedNavigation.value) {
      error.value = '请选择导航菜单'
      return
    }

    // 找到选中的导航菜单名称
    const selectedNav = navigationOptions.value.find(nav => nav.id === selectedNavigation.value)
    if (selectedNav) {
      selectedNavigationName.value = selectedNav.name
    }

    // 调用API保存导航菜单设置
    const response = await zhaomuApiService.setNavigation({
      navigationId: selectedNavigation.value
    })
    console.log('保存导航菜单响应:', response)

    if (response && response.code === 1) {
      successMessage.value = `导航菜单已选择：${selectedNavigationName.value}`
      error.value = null

      // 显示成功状态
      navigationSaveStatus.value = 'success'
      setTimeout(() => {
        navigationSaveStatus.value = 'normal'
      }, 3000)
    } else {
      error.value = response.msg || '保存导航菜单设置失败'
    }

  } catch (err) {
    console.error('保存导航菜单设置错误:', err)
    error.value = '网络错误，请检查连接后重试'
  }
}

// 保存实名认证设置
const saveRealNameAuth = async () => {
  try {
    console.log('开始保存实名认证设置:', realNameAuthRequired.value)

    // 调用API保存实名认证设置
    const response = await zhaomuApiService.setRealNameAuth({
      required: realNameAuthRequired.value
    })
    console.log('保存实名认证设置响应:', response)

    if (response && response.code === 1) {
      successMessage.value = `实名认证设置已保存：${realNameAuthRequired.value ? '需要实名认证' : '不需要实名认证'}`
      error.value = null

      // 显示成功状态
      realNameAuthSaveStatus.value = 'success'
      setTimeout(() => {
        realNameAuthSaveStatus.value = 'normal'
      }, 3000)
    } else {
      error.value = response.msg || '保存实名认证设置失败'
    }

  } catch (err) {
    console.error('保存实名认证设置错误:', err)
    error.value = '网络错误，请检查连接后重试'
  }
}

// 全选功能参数
const selectAllFeatures = () => {
  comparisonFeatures.value.forEach(feature => {
    feature.use = true
  })
}

// 全不选功能参数
const selectNoneFeatures = () => {
  comparisonFeatures.value.forEach(feature => {
    feature.use = false
  })
}

// 保存功能参数比较设置
const saveComparisonSettings = async () => {
  try {
    console.log('开始保存功能参数比较设置:', comparisonFeatures.value)
    
    const response = await zhaomuApiService.setComparisonSettings(comparisonFeatures.value)
    console.log('保存功能参数比较设置响应:', response)
    
    if (response && response.code === 1) {
      successMessage.value = `功能参数比较设置已保存，共 ${comparisonFeatures.value.length} 个功能`
      error.value = null
    } else {
      error.value = response.msg || '保存功能参数比较设置失败'
    }
  } catch (err) {
    console.error('保存功能参数比较设置错误:', err)
    error.value = '网络错误，请检查连接后重试'
  }
}

// 统一保存所有配置
const saveAllConfig = async () => {
  try {
    console.log('开始保存所有配置...')
    loading.value = true
    error.value = null
    successMessage.value = null
    
    // 保存导航菜单
    if (selectedNavigation.value) {
      await saveNavigation()
    }
    
    // 保存销售折扣
    if (salesDiscount.value >= 0) {
      await saveSalesDiscount()
    }
    
    // 保存实名认证设置
    await saveRealNameAuth()
    
    // 保存汇率设置
    if (exchangeRate.value > 0) {
      await saveExchangeSettings()
    }
    
    // 保存功能参数比较设置
    if (comparisonFeatures.value.length > 0) {
      await saveComparisonSettings()
    }
    
    // 显示成功状态
    allSaveStatus.value = 'success'
    successMessage.value = '所有配置已保存成功'
    
    setTimeout(() => {
      allSaveStatus.value = 'normal'
    }, 3000)
    
  } catch (err) {
    console.error('保存所有配置错误:', err)
    error.value = '保存配置时发生错误，请重试'
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>

.hlwidc-zhaomu-cloud button,.hlwidc-zhaomu-cloud button>*{
  font-size: 1rem!important;
}

.hlwidc-zhaomu-cloud {
  max-width: 1400px;
  margin: 0 auto;
  padding: 20px;
}

/* 配置设置区域样式 */
.hlwidc-config-sections {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 15px;
  margin: 5px 0;
  padding: 20px;
  background: #f8f9fa;
  border-radius: 8px;
  border: 1px solid #e0e0e0;
  min-width: 800px; /* 确保有足够宽度显示4列 */
}

/* 配置项样式 */
.hlwidc-config-item {
  display: flex;
  flex-direction: column;
  gap: 8px;
  min-width: 0; /* 防止内容溢出 */
}

.hlwidc-config-label {
  font-weight: 500;
  color: #333;
  font-size: 14px;
}

.hlwidc-config-input,
.hlwidc-config-select {
  padding: 8px 12px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 14px;
  transition: all 0.3s ease;
  width: 100%;
  max-width: 200px; /* 限制最大宽度 */
}

.hlwidc-config-input:focus,
.hlwidc-config-select:focus {
  outline: none;
  border-color: #42b883;
  box-shadow: 0 0 0 2px rgba(66, 184, 131, 0.2);
}

.hlwidc-config-input:disabled,
.hlwidc-config-select:disabled {
  background-color: #f5f5f5;
  cursor: not-allowed;
}

/* 实名认证开关样式 */
.hlwidc-config-switch {
  display: flex;
  align-items: center;
  gap: 8px;
  cursor: pointer;
}

/* 汇率设置输入组 */
.hlwidc-exchange-inputs {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
}

.hlwidc-exchange-inputs .hlwidc-config-input {
  flex: 1;
  min-width: 80px;
  max-width: 100px;
}

.hlwidc-exchange-inputs .hlwidc-config-select {
  min-width: 80px;
  max-width: 120px;
}

/* 统一提交按钮样式 */
.hlwidc-submit-section {
  display: flex;
  justify-content: center;
  margin: 20px 0;
}

.hlwidc-submit-btn {
  padding: 12px 24px;
  background: linear-gradient(135deg, #42b883 0%, #369870 100%);
  color: white;
  border: none;
  border-radius: 6px;
  font-size: 16px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
}

.hlwidc-submit-btn:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(66, 184, 131, 0.3);
}

.hlwidc-submit-btn:disabled {
  background: #ccc;
  cursor: not-allowed;
  transform: none;
  box-shadow: none;
}

.hlwidc-success-btn {
  background: linear-gradient(135deg, #28a745 0%, #20c997 100%) !important;
}

/* 功能参数比较设置样式 */
.hlwidc-comparison-section {
  margin: 20px 0;
  padding: 20px;
  background: #f8f9fa;
  border-radius: 8px;
  border: 1px solid #e0e0e0;
}

.hlwidc-comparison-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
  flex-wrap: wrap;
  gap: 15px;
}

.hlwidc-comparison-header h3 {
  margin: 0;
  color: #333;
  font-size: 18px;
  font-weight: 600;
}

.hlwidc-comparison-controls {
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
}

.hlwidc-features-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  gap: 12px;
}

.hlwidc-feature-item {
  background: white;
  border: 1px solid #ddd;
  border-radius: 6px;
  padding: 12px 16px;
  transition: all 0.2s ease;
}

.hlwidc-feature-item:hover {
  border-color: #42b883;
  box-shadow: 0 2px 8px rgba(66, 184, 131, 0.1);
}

.hlwidc-feature-label {
  display: flex;
  align-items: center;
  gap: 10px;
  cursor: pointer;
  user-select: none;
}

.hlwidc-feature-label input[type="checkbox"] {
  transform: scale(1.1);
  accent-color: #42b883;
}

.hlwidc-feature-name {
  font-size: 14px;
  color: #333;
  font-weight: 500;
}

/* 响应式设计 */
@media (max-width: 1024px) {
  .hlwidc-config-sections {
    grid-template-columns: repeat(2, 1fr);
  }
  
  .hlwidc-features-grid {
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  }
}

@media (max-width: 768px) {
  .hlwidc-config-sections {
    grid-template-columns: 1fr;
    gap: 15px;
  }
  
  .hlwidc-exchange-inputs {
    flex-direction: column;
  }
}

/* 销售地区选择样式 */
.hlwidc-all-countries-section {
  margin: 5px 0;
}

/* 销售折扣设置样式 */
.hlwidc-discount-section {
  display: flex;
  align-items: center;
  gap: 20px;
  margin: 5px 0;
  padding: 20px;
  background: #f8f9fa;
  border-radius: 8px;
  border: 1px solid #e0e0e0;
  flex-wrap: wrap;
}

.hlwidc-discount-label {
  display: flex;
  align-items: center;
  gap: 10px;
  font-weight: 500;
  color: #333;
}

.hlwidc-discount-input {
  width: 80px;
  padding: 8px 12px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 14px;
  text-align: center;
  transition: all 0.3s ease;
}

.hlwidc-discount-input:focus {
  outline: none;
  border-color: #42b883;
  box-shadow: 0 0 0 2px rgba(66, 184, 131, 0.2);
}

.hlwidc-discount-input:disabled {
  background-color: #f5f5f5;
  cursor: not-allowed;
}

.hlwidc-discount-info {
  font-weight: 500;
  color: #42b883;
  background: rgba(66, 184, 131, 0.1);
  padding: 6px 12px;
  border-radius: 4px;
  border: 1px solid rgba(66, 184, 131, 0.2);
}

.hlwidc-discount-save-btn {
  padding: 8px 16px;
  background: linear-gradient(135deg, #42b883 0%, #369870 100%);
  color: white;
  border: none;
  border-radius: 6px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
}

.hlwidc-discount-save-btn:hover:not(:disabled) {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(66, 184, 131, 0.3);
}

.hlwidc-discount-save-btn:disabled {
  background: #9ca3af;
  cursor: not-allowed;
  transform: none;
  box-shadow: none;
}

.hlwidc-success-btn {
  background: linear-gradient(135deg, #28a745 0%, #20c997 100%) !important;
  animation: successPulse 0.6s ease-in-out;
}

@keyframes successPulse {
  0% {
    transform: scale(1);
  }

  50% {
    transform: scale(1.05);
  }

  100% {
    transform: scale(1);
  }
}

/* 实名认证设置样式 */
.hlwidc-real-name-auth-section {
  margin: 5px 0;
  padding: 20px;
  background: #f8f9fa;
  border-radius: 8px;
  border: 1px solid #e0e0e0;
}

.hlwidc-real-name-auth-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 15px;
}

.hlwidc-real-name-auth-header h3 {
  margin: 0;
  color: #333;
  font-size: 16px;
}

.hlwidc-real-name-auth-info {
  font-weight: 500;
  color: #42b883;
  background: rgba(66, 184, 131, 0.1);
  padding: 6px 12px;
  border-radius: 4px;
  border: 1px solid rgba(66, 184, 131, 0.2);
}

.hlwidc-real-name-auth-controls {
  display: flex;
  align-items: center;
  gap: 20px;
  flex-wrap: wrap;
}

.hlwidc-config-switch {
  display: flex;
  align-items: center;
  gap: 12px;
  cursor: pointer;
  user-select: none;
  position: relative;
}

.hlwidc-config-switch input[type="checkbox"] {
  display: none;
}

.hlwidc-switch-slider {
  position: relative;
  width: 50px;
  height: 24px;
  background: #ccc;
  border-radius: 12px;
  transition: all 0.3s ease;
}

.hlwidc-switch-slider::before {
  content: '';
  position: absolute;
  top: 2px;
  left: 2px;
  width: 20px;
  height: 20px;
  background: white;
  border-radius: 50%;
  transition: all 0.3s ease;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.hlwidc-config-switch input[type="checkbox"]:checked+.hlwidc-switch-slider {
  background: #42b883;
}

.hlwidc-config-switch input[type="checkbox"]:checked+.hlwidc-switch-slider::before {
  transform: translateX(26px);
}

.hlwidc-switch-label {
  font-weight: 500;
  color: #333;
  font-size: 14px;
  margin-left: 8px;
}

/* 开关悬停效果 */
.hlwidc-config-switch:hover .hlwidc-switch-slider {
  box-shadow: 0 0 0 2px rgba(66, 184, 131, 0.2);
}

/* 开关禁用状态 */
.hlwidc-config-switch input[type="checkbox"]:disabled + .hlwidc-switch-slider {
  opacity: 0.5;
  cursor: not-allowed;
}

.hlwidc-config-switch input[type="checkbox"]:disabled + .hlwidc-switch-slider + .hlwidc-switch-label {
  opacity: 0.5;
}

.hlwidc-real-name-auth-save-btn {
  padding: 8px 16px;
  background: linear-gradient(135deg, #42b883 0%, #369870 100%);
  color: white;
  border: none;
  border-radius: 6px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
  height: fit-content;
}

.hlwidc-real-name-auth-save-btn:hover:not(:disabled) {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(66, 184, 131, 0.3);
}

.hlwidc-real-name-auth-save-btn:disabled {
  background: #9ca3af;
  cursor: not-allowed;
  transform: none;
  box-shadow: none;
}

/* 汇率设置样式 */
.hlwidc-exchange-rate-section {
  margin: 5px 0;
  padding: 20px;
  background: #f8f9fa;
  border-radius: 8px;
  border: 1px solid #e0e0e0;
}

.hlwidc-exchange-rate-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 15px;
}

.hlwidc-exchange-rate-header h3 {
  margin: 0;
  color: #333;
  font-size: 16px;
}

.hlwidc-exchange-rate-info {
  font-weight: 500;
  color: #42b883;
  background: rgba(66, 184, 131, 0.1);
  padding: 6px 12px;
  border-radius: 4px;
  border: 1px solid rgba(66, 184, 131, 0.2);
}

.hlwidc-exchange-rate-controls {
  display: flex;
  align-items: center;
  gap: 20px;
  flex-wrap: wrap;
}

.hlwidc-currency-unit,
.hlwidc-exchange-rate-input {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.hlwidc-currency-label,
.hlwidc-rate-label {
  display: flex;
  flex-direction: column;
  gap: 5px;
  font-weight: 500;
  color: #333;
}

.hlwidc-currency-select,
.hlwidc-rate-input {
  padding: 8px 12px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 14px;
  transition: all 0.3s ease;
  min-width: 80px;
  max-width: 100px;
}

.hlwidc-currency-select:focus,
.hlwidc-rate-input:focus {
  outline: none;
  border-color: #42b883;
  box-shadow: 0 0 0 2px rgba(66, 184, 131, 0.2);
}

.hlwidc-currency-select:disabled,
.hlwidc-rate-input:disabled {
  background-color: #f5f5f5;
  cursor: not-allowed;
}

.hlwidc-exchange-rate-save-btn {
  padding: 8px 16px;
  background: linear-gradient(135deg, #42b883 0%, #369870 100%);
  color: white;
  border: none;
  border-radius: 6px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
  height: fit-content;
}

.hlwidc-exchange-rate-save-btn:hover:not(:disabled) {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(66, 184, 131, 0.3);
}

.hlwidc-exchange-rate-save-btn:disabled {
  background: #9ca3af;
  cursor: not-allowed;
  transform: none;
  box-shadow: none;
}

/* 汇率说明样式 */
.hlwidc-exchange-rate-notice {
  display: flex;
  align-items: flex-start;
  gap: 12px;
  margin: 5px 0;
  padding: 15px 20px;
  background: #fff3cd;
  border: 1px solid #ffeaa7;
  border-radius: 8px;
  border-left: 4px solid #f39c12;
}

.hlwidc-notice-icon {
  font-size: 20px;
  line-height: 1;
  margin-top: 2px;
}

.hlwidc-notice-content {
  flex: 1;
}

.hlwidc-notice-content strong {
  display: block;
  color: #856404;
  font-size: 14px;
  margin-bottom: 4px;
}

.hlwidc-notice-content p {
  margin: 0;
  color: #856404;
  font-size: 13px;
  line-height: 1.4;
}

.hlwidc-select-controls {
  display: flex;
  align-items: center;
  gap: 15px;
  margin: 5px 0;
  padding: 15px;
  background: #f8f9fa;
  border-radius: 8px;
  flex-wrap: wrap;
}

.hlwidc-control-btn {
  padding: 8px 16px;
  background-color: #6c757d;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 14px;
  transition: all 0.3s ease;
}

.hlwidc-control-btn:hover:not(:disabled) {
  background-color: #5a6268;
  transform: translateY(-1px);
}

.hlwidc-control-btn:disabled {
  background-color: #ccc;
  cursor: not-allowed;
  transform: none;
}

.hlwidc-selection-info {
  font-weight: 500;
  color: #495057;
  margin-left: auto;
}

/* 大洲列表样式 */
.hlwidc-continents-list {
  margin-top: 20px;
}

.hlwidc-continent-section {
  margin-bottom: 15px;
  border: 1px solid #e0e0e0;
  border-radius: 8px;
  background: white;
  overflow: hidden;
}

.hlwidc-continent-header {
  display: flex;
  align-items: center;
  padding: 15px 20px;
  background: #f8f9fa;
  cursor: pointer;
  transition: all 0.3s ease;
  border-bottom: 1px solid #e0e0e0;
}

.hlwidc-continent-header:hover {
  background: #e9ecef;
}

.hlwidc-continent-header h3 {
  margin: 0;
  color: #333;
  font-size: 18px;
  flex: 1;
}

.hlwidc-country-count {
  color: #666;
  font-size: 14px;
  margin-right: 10px;
}

.hlwidc-continent-actions {
  display: inline-flex;
  gap: 8px;
  margin-right: 10px;
}

.hlwidc-link-btn {
  appearance: none;
  background: transparent;
  border: none;
  color: #2563eb;
  padding: 4px 6px;
  border-radius: 4px;
  cursor: pointer;
  font-size: 13px;
}

.hlwidc-link-btn:hover {
  background: rgba(37, 99, 235, 0.1);
}

.hlwidc-toggle-icon {
  font-size: 12px;
  color: #666;
  transition: transform 0.3s ease;
}

.hlwidc-toggle-icon.expanded {
  transform: rotate(180deg);
}

/* 国家列表样式 */
.hlwidc-countries-list {
  padding: 15px 20px;
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
  gap: 8px;
  background: white;
}

.hlwidc-country-item {
  display: flex;
  align-items: center;
  padding: 8px 12px;
  background: white;
  border: 1px solid #ddd;
  border-radius: 4px;
  cursor: pointer;
  transition: all 0.2s ease;
}

.hlwidc-country-item:hover {
  background-color: #f0f9f4;
  border-color: #42b883;
}

.hlwidc-country-item input[type="checkbox"] {
  margin-right: 8px;
  transform: scale(1.1);
}

.hlwidc-country-item span {
  font-size: 14px;
  color: #333;
}

/* 按钮样式 */
.hlwidc-actions {
  margin: 5px 0;
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
}

button {
  padding: 12px 24px;
  background-color: #42b883;
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-size: 14px;
  font-weight: 500;
  transition: all 0.3s ease;
}

button:hover:not(:disabled) {
  background-color: #369870;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(66, 184, 131, 0.3);
}

button:disabled {
  background-color: #ccc;
  cursor: not-allowed;
  transform: none;
  box-shadow: none;
}

.hlwidc-save-btn {
  background-color: #28a745;
}

.hlwidc-save-btn:hover:not(:disabled) {
  background-color: #218838;
  box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
}

.hlwidc-clear-btn {
  background-color: #dc3545;
}

.hlwidc-clear-btn:hover:not(:disabled) {
  background-color: #c82333;
  box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
}

/* 状态信息样式 */
.hlwidc-loading {
  text-align: center;
  padding: 20px;
  color: #666;
  font-size: 16px;
}

.hlwidc-error {
  background-color: #f8d7da;
  color: #721c24;
  padding: 15px;
  border-radius: 6px;
  margin: 5px 0;
  border-left: 4px solid #dc3545;
}

.hlwidc-success {
  background-color: #d4edda;
  color: #155724;
  padding: 15px;
  border-radius: 6px;
  margin: 5px 0;
  border-left: 4px solid #28a745;
}

.hlwidc-debug-info {
  background-color: #e7f3ff;
  color: #004085;
  padding: 15px;
  border-radius: 6px;
  margin: 5px 0;
  border-left: 4px solid #007bff;
  font-size: 14px;
}

.hlwidc-debug-info h3 {
  margin: 0 0 10px 0;
  font-size: 16px;
}

.hlwidc-debug-info p {
  margin: 5px 0;
}

pre {
  white-space: pre-wrap;
  word-wrap: break-word;
  margin: 0;
}

/* 导航菜单设置样式 */
.hlwidc-navigation-section {
  margin: 5px 0;
  padding: 20px;
  background: #f8f9fa;
  border-radius: 8px;
  border: 1px solid #e0e0e0;
}

.hlwidc-navigation-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 15px;
}

.hlwidc-navigation-header h3 {
  margin: 0;
  color: #333;
  font-size: 18px;
  font-weight: 600;
}

.hlwidc-navigation-info {
  color: #666;
  font-size: 14px;
  font-weight: 500;
}

.hlwidc-navigation-controls {
  display: flex;
  align-items: center;
  gap: 15px;
  flex-wrap: wrap;
}

.hlwidc-navigation-select {
  flex: 1;
  min-width: 200px;
}

.hlwidc-navigation-dropdown {
  width: 100%;
  padding: 10px 12px;
  border: 2px solid #e0e0e0;
  border-radius: 6px;
  font-size: 14px;
  background: white;
  transition: all 0.3s ease;
  appearance: none;
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
  background-position: right 8px center;
  background-repeat: no-repeat;
  background-size: 16px;
  padding-right: 40px;
}

.hlwidc-navigation-dropdown:focus {
  outline: none;
  border-color: #42b883;
  box-shadow: 0 0 0 2px rgba(66, 184, 131, 0.2);
}

.hlwidc-navigation-dropdown:disabled {
  background-color: #f5f5f5;
  cursor: not-allowed;
}

.hlwidc-navigation-save-btn {
  padding: 8px 16px;
  background: linear-gradient(135deg, #42b883 0%, #369870 100%);
  color: white;
  border: none;
  border-radius: 6px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
  height: fit-content;
}

.hlwidc-navigation-save-btn:hover:not(:disabled) {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(66, 184, 131, 0.3);
}

.hlwidc-navigation-save-btn:disabled {
  background: #9ca3af;
  cursor: not-allowed;
  transform: none;
  box-shadow: none;
}

/* 响应式设计 */
@media (max-width: 768px) {
  .hlwidc-config-sections {
    grid-template-columns: 1fr;
    gap: 15px;
  }

  .hlwidc-continents-grid {
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
  }
  
  .hlwidc-countries-grid {
    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
  }
  
  .hlwidc-actions {
    flex-direction: column;
  }
  
  button {
    width: 100%;
  }

  .hlwidc-discount-section {
    flex-direction: column;
    align-items: flex-start;
    gap: 15px;
  }

  .hlwidc-discount-input {
    width: 100%;
    max-width: 120px;
  }

  .hlwidc-exchange-rate-notice {
    flex-direction: column;
    gap: 8px;
    padding: 12px 15px;
  }

  .hlwidc-notice-icon {
    align-self: center;
  }

  .hlwidc-exchange-rate-controls {
    flex-direction: column;
    align-items: flex-start;
    gap: 15px;
  }

  .hlwidc-currency-unit,
  .hlwidc-exchange-rate-input {
    width: 100%;
  }

  .hlwidc-currency-select,
  .hlwidc-rate-input {
    width: 100%;
    max-width: 120px;
  }
}
</style>
