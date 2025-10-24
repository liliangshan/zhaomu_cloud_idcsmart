// API配置
const apiConfig = {
  baseURL: import.meta.env.VITE_API_BASE_URL || 'http://127.0.0.1:8082',
  timeout: parseInt(import.meta.env.VITE_API_TIMEOUT || '30000'),
  title: import.meta.env.VITE_APP_TITLE || '朝暮云管理系统',
  env: import.meta.env.VITE_APP_ENV || 'development',
  isDev: import.meta.env.VITE_APP_ENV === 'development',
  isProd: import.meta.env.VITE_APP_ENV === 'production'
}

// 获取完整的API URL
function getApiUrl(path: string): string {
  const baseUrl = apiConfig.baseURL
  return `${baseUrl}${path}`
}

// 朝暮云API端点
const zhaomuApi = {
  getRegions: () => getApiUrl('_controller=api&_action=getRegions'),
  getAllRegions: () => getApiUrl('_controller=api&_action=getAllRegions'),
  getProductsByRegion: (regionId: string) => getApiUrl(`_controller=api&_action=getProductsByRegion&regionId=${regionId}`),
  cacheCountries: () => getApiUrl('_controller=api&_action=cacheCountries'),
  getSelectedCountries: () => getApiUrl('_controller=api&_action=getSelectedCountries'),
  checkCacheKey: () => getApiUrl('_controller=api&_action=checkCacheKey'),
  saveApiKey: () => getApiUrl('_controller=api&_action=saveApiKey'),
  getSalesDiscount: () => getApiUrl('_controller=api&_action=getSalesDiscount'),
  setSalesDiscount: () => getApiUrl('_controller=api&_action=setSalesDiscount'),
  getExchangeSettings: () => getApiUrl('_controller=api&_action=getExchangeSettings'),
  setExchangeSettings: () => getApiUrl('_controller=api&_action=setExchangeSettings'),
  getAllConfig: () => getApiUrl('_controller=api&_action=getAllConfig'),
  getProductPrice: (params: any) => {
    const queryString = new URLSearchParams(params).toString()
    return getApiUrl(`_controller=api&_action=getProductPrice&${queryString}`)
  },
  searchUsers: (params: any) => {
    const queryString = new URLSearchParams(params).toString()
    return getApiUrl(`_controller=api&_action=searchUsers&${queryString}`)
  },
  getProductImages: (params: any) => {
    const queryString = new URLSearchParams(params).toString()
    return getApiUrl(`_controller=api&_action=getProductImages&${queryString}`)
  },
  getRegionFeatureComparison: (regionId: string) => getApiUrl(`_controller=api&_action=getRegionFeatureComparison&regionId=${regionId}`),
  submitOrder: () => getApiUrl('_controller=api&_action=submitOrder'),
  setNavigation: () => getApiUrl('_controller=api&_action=setNavigation'),
  setRealNameAuth: () => getApiUrl('_controller=api&_action=setRealNameAuth'),
  getOrderList: (params: any) => {
    const queryString = new URLSearchParams(params).toString()
    return getApiUrl(`_controller=api&_action=getOrderList&${queryString}`)
  },
  updateOrderStatus: () => getApiUrl('_controller=api&_action=updateOrderStatus'),
  continueProcessing: () => getApiUrl('_controller=api&_action=continueProcessing')
}

/**
 * API响应接口
 */
export interface ApiResponse<T = any> {
  code: number
  msg: string
  data?: T
}

/**
 * 订单相关类型定义
 */
export type OrderStatus = 'Pending' | 'Active' | 'Suspended' | 'Cancelled' | 'Fraud' | 'Completed'
export type PaymentMethod = 'monthly' | 'quarterly' | 'yearly' | 'StripeAli' | 'wechat' | 'bank' | 'credit' | 'manual' | 'UserCustom'

export interface Client {
  id: number
  username: string
  email: string
}

export interface Host {
  id: number
  domain: string
}

export interface InvoiceItem {
  id: number
  invoice_id: number
  uid: number
  type: string
  rel_id: number
  description: string
  description2: string
  amount: number
  taxed: boolean
  due_time: number
  payment: string
  notes: string
  delete_time: number
  aff_sure_time: number | null
  aff_commission: number | null
  aff_commmission_bates: number | null
  aff_commmission_bates_type: string | null
  is_aff: number
  host: Host
}

export interface Invoice {
  id: number
  uid: number
  invoice_num: string
  create_time: string
  update_time: string
  due_time: number
  paid_time: number
  last_capture_attempt: number
  subtotal: number
  credit: number
  tax: number
  tax2: number
  total: number
  taxrate: number
  taxrate2: number
  status: string
  payment: string
  notes: string | null
  delete_time: number
  due_email_times: number
  type: string
  payment_status: string
  aff_sure_time: number | null
  aff_commission: number | null
  aff_commmission_bates: number | null
  aff_commmission_bates_type: string | null
  is_aff: number
  is_cron: boolean
  suffix: number
  use_credit_limit: boolean
  invoice_id: number
  url: string
  paymt: string
  is_delete: boolean
  credit_limit_prepayment: boolean
  credit_limit_prepayment_invoices: any | null
  item: InvoiceItem[]
}

export interface Order {
  id: number
  uid: number
  ordernum: string
  status: OrderStatus
  pay_time: number
  create_time: string
  update_time: string
  amount: number
  payment: PaymentMethod
  promo_code: string
  promo_type: string
  promo_value: number
  invoiceid: number
  delete_time: number
  notes: string | null
  client: Client
  invoice: Invoice
}

export interface OrderListResponse {
  orders: Order[]
  total: number
  page: number
  limit: number
  pages: number
  has_more: boolean
  current_page: number
  per_page: number
}

/**
 * 朝暮云API服务
 */
export class ZhaomuApiService {
  private baseURL: string

  constructor() {
    this.baseURL = apiConfig.baseURL
  }

  /**
   * 发送HTTP请求
   * @param url 请求URL
   * @param options 请求选项
   * @returns Promise<ApiResponse>
   */
  private async request<T = any>(
    url: string, 
    options: RequestInit = {}
  ): Promise<ApiResponse<T>> {
    const defaultOptions: RequestInit = {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
        ...options.headers
      },
      ...options
    }

    try {
      const response = await fetch(url, defaultOptions)
      
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`)
      }

      const data = await response.json()
      return data
    } catch (error) {
      console.error('API请求失败:', error)
      throw error
    }
  }

  /**
   * 获取可用区列表
   * @param grouped 是否按层级分组
   * @returns Promise<ApiResponse>
   */
  async getRegions(grouped: boolean = true): Promise<ApiResponse> {
    const url = `${zhaomuApi.getRegions()}${grouped ? '&grouped=1' : ''}`
    return this.request(url)
  }

  /**
   * 获取所有可用区列表（不受缓存影响）
   * @param grouped 是否按层级分组
   * @returns Promise<ApiResponse>
   */
  async getAllRegions(grouped: boolean = true): Promise<ApiResponse> {
    const url = `${zhaomuApi.getAllRegions()}${grouped ? '&grouped=1' : ''}`
    return this.request(url)
  }

  /**
   * 获取指定可用区的产品列表
   * @param regionId 可用区ID
   * @returns Promise<ApiResponse>
   */
  async getProductsByRegion(regionId: string): Promise<ApiResponse> {
    const url = zhaomuApi.getProductsByRegion(regionId)
    return this.request(url)
  }

  /**
   * 缓存国家列表
   * @param countries 国家数组
   * @returns Promise<ApiResponse>
   */
  async cacheCountries(countries: string[]): Promise<ApiResponse> {
    const url = zhaomuApi.cacheCountries()
    return this.request(url, {
      method: 'POST',
      body: JSON.stringify({ countries })
    })
  }

  /**
   * 获取已选中的国家列表
   * @returns Promise<ApiResponse>
   */
  async getSelectedCountries(): Promise<ApiResponse> {
    const url = zhaomuApi.getSelectedCountries()
    return this.request(url)
  }

  /**
   * 检查缓存键是否存在
   * @returns Promise<ApiResponse>
   */
  async checkCacheKey(): Promise<ApiResponse> {
    const url = zhaomuApi.checkCacheKey()
    return this.request(url)
  }

  /**
   * 保存 API Key
   * @param apiKey API Key
   * @returns Promise<ApiResponse>
   */
  async saveApiKey(apiKey: string): Promise<ApiResponse> {
    const url = zhaomuApi.saveApiKey()
    return this.request(url, {
      method: 'POST',
      body: JSON.stringify({ apiKey })
    })
  }

  /**
   * 获取销售折扣
   * @returns Promise<ApiResponse>
   */
  async getSalesDiscount(): Promise<ApiResponse> {
    const url = zhaomuApi.getSalesDiscount()
    return this.request(url)
  }

  /**
   * 设置销售折扣
   * @param discount 折扣值 (0-100)
   * @returns Promise<ApiResponse>
   */
  async setSalesDiscount(discount: number): Promise<ApiResponse> {
    const url = zhaomuApi.setSalesDiscount()
    return this.request(url, {
      method: 'POST',
      body: JSON.stringify({ discount })
    })
  }

  /**
   * 获取汇率设置
   * @returns Promise<ApiResponse>
   */
  async getExchangeSettings(): Promise<ApiResponse> {
    const url = zhaomuApi.getExchangeSettings()
    return this.request(url)
  }

  /**
   * 设置汇率设置
   * @param settings 汇率设置对象
   * @returns Promise<ApiResponse>
   */
  async setExchangeSettings(settings: { exchangeRate: number; currencyUnit: string }): Promise<ApiResponse> {
    const url = zhaomuApi.setExchangeSettings()
    return this.request(url, {
      method: 'POST',
      body: JSON.stringify(settings)
    })
  }

  /**
   * 获取所有配置（销售折扣 + 汇率设置）
   * @returns Promise<ApiResponse>
   */
  async getAllConfig(): Promise<ApiResponse> {
    const url = zhaomuApi.getAllConfig()
    return this.request(url)
  }

  /**
   * 获取产品价格
   * @param params 价格查询参数
   * @returns Promise<ApiResponse>
   */
  async getProductPrice(params: any): Promise<ApiResponse> {
    const url = zhaomuApi.getProductPrice(params)
    return this.request(url)
  }

  /**
   * 搜索用户
   * @param params 搜索参数
   * @returns Promise<ApiResponse>
   */
  async searchUsers(params: any): Promise<ApiResponse> {
    const url = zhaomuApi.searchUsers(params)
    return this.request(url)
  }

  /**
   * 获取产品镜像列表
   * @param params 查询参数
   * @returns Promise<ApiResponse>
   */
  async getProductImages(params: any): Promise<ApiResponse> {
    const url = zhaomuApi.getProductImages(params)
    return this.request(url)
  }

  /**
   * 获取某个可用区的功能参数比较
   * @param regionId 可用区ID
   * @returns Promise<ApiResponse>
   */
  async getRegionFeatureComparison(regionId: string): Promise<ApiResponse> {
    const url = zhaomuApi.getRegionFeatureComparison(regionId)
    return this.request(url)
  }

  /**
   * 提交订单
   * @param orderData 订单数据
   * @returns Promise<ApiResponse>
   */
  async submitOrder(orderData: any): Promise<ApiResponse> {
    const url = zhaomuApi.submitOrder()
    return this.request(url, {
      method: 'POST',
      body: JSON.stringify(orderData)
    })
  }

  /**
   * 设置导航菜单
   * @param navigationData 导航菜单数据
   * @returns Promise<ApiResponse>
   */
  async setNavigation(navigationData: any): Promise<ApiResponse> {
    const url = zhaomuApi.setNavigation()
    return this.request(url, {
      method: 'POST',
      body: JSON.stringify(navigationData)
    })
  }

  /**
   * 设置实名认证
   * @param authData 实名认证数据
   * @returns Promise<ApiResponse>
   */
  async setRealNameAuth(authData: any): Promise<ApiResponse> {
    const url = zhaomuApi.setRealNameAuth()
    return this.request(url, {
      method: 'POST',
      body: JSON.stringify(authData)
    })
  }

  /**
   * 获取订单列表
   * @param params 查询参数
   * @returns Promise<ApiResponse<OrderListResponse>>
   */
  async getOrderList(params: any): Promise<ApiResponse<OrderListResponse>> {
    const url = zhaomuApi.getOrderList(params)
    return this.request<OrderListResponse>(url)
  }


  /**
   * 更新订单状态
   * @param orderData 订单数据
   * @returns Promise<ApiResponse>
   */
  async updateOrderStatus(orderData: any): Promise<ApiResponse> {
    const url = zhaomuApi.updateOrderStatus()
    return this.request(url, {
      method: 'POST',
      body: JSON.stringify(orderData)
    })
  }

  /**
   * 继续处理订单
   * @param orderId 订单ID
   * @returns Promise<ApiResponse>
   */
  async continueProcessing(orderId: number): Promise<ApiResponse> {
    const url = zhaomuApi.continueProcessing()
    return this.request(url, {
      method: 'POST',
      body: JSON.stringify({ orderId })
    })
  }
}

// 创建API服务实例
export const zhaomuApiService = new ZhaomuApiService()
