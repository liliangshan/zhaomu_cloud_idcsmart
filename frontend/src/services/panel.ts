// API配置
const apiConfig = {
  baseURL: import.meta.env.VITE_APP_PANEL_URL + 'token=' + window.APP_CONFIG.customParam + '&',
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
  getMachineInfo: () => getApiUrl('_action=getMachineInfo'),
  rebootServer: () => getApiUrl('_action=rebootServer'),
  shutdownServer: () => getApiUrl('_action=shutdownServer'),
  resetPassword: () => getApiUrl('_action=resetPassword'),
  rebuildServer: () => getApiUrl('_action=rebuildServer'),
  getCloudServerImages: () => getApiUrl('_action=getCloudServerImages'),
  switchPaymentCycle: (cycle: string) => getApiUrl(`_action=switchPaymentCycle&cycle=${cycle}`)
}

// 通用API响应接口
export interface ApiResponse<T = any> {
  code: number
  msg: string
  data?: T
}

// 自定义字段值接口
export interface CustomFieldValue {
  id: number
  fieldid: number
  relid: number
  value: string
  create_time: string
  update_time: string
}

// 产品信息接口
export interface Product {
  id: number
  type: string
  gid: number
  name: string
  description: string | null
  config_option1: string
  create_time: string
  update_time: string
}

// 朝暮云官方服务器信息接口
export interface CloudServerInfo {
  id: number
  ip: string
  root: string
  password: string
  port: number
  ipv6: string
  cpu: number
  ram: number
  disk: number
  diskData: number
  diskMedia: string
  bandwidth: number | null
  traffic: number
  image: string
  imageIdentity: string
  renewPrice: number
  paymentCycle: number
  priceHour: number | null
  price: number
  priceQuarter: number
  priceHalfYear: number
  priceYear: number
  startTime: string
  endTime: string
  status: number
  note: string | null
  noteUser: string | null
  isAutoRenew: number
  user_id: number
  region_id: number
}

// 镜像信息接口
export interface ImageInfo {
  id: number
  name: string
  type: string
}

// 镜像分组接口
export interface ImageGroup {
  type: string
  images: ImageInfo[]
}

// 机器信息接口
export interface MachineInfo {
  // 基础信息
  id: number
  uid: number
  orderid: number
  productid: number
  serverid: number
  regdate: number
  domain: string
  payment: string
  firstpaymentamount: number
  amount: number
  billingcycle: string
  last_settle: number
  nextduedate: number
  nextinvoicedate: number
  termination_date: number
  completed_date: number
  domainstatus: string
  username: string
  password: string
  notes: string
  subscriptionid: string
  promoid: number
  suspendreason: string
  overideautosuspend: boolean
  overidesuspenduntil: number
  dedicatedip: string
  assignedips: string
  ns1: string
  ns2: string
  diskusage: number
  disklimit: number
  bwusage: number
  bwlimit: number
  user_cate_id: number
  lastupdate: number
  create_time: string
  update_time: string
  suspend_time: number
  auto_terminate_end_cycle: boolean
  auto_terminate_reason: string
  dcimid: number
  dcim_os: number
  os: string
  os_url: string
  reinstall_info: string
  remark: string
  show_last_act_message: number
  port: number
  dcim_area: number
  flag: number
  flag_cycle: string
  stream_info: string
  initiative_renew: number
  agent_client: boolean
  percent_value: number
  upstream_cost: string
  
  // 关联数据
  customFieldValues: CustomFieldValue[]
  product: Product
  
  // 扩展字段
  business_id: string
  cloud_server_info: CloudServerInfo
}

// 朝暮云API服务类
export class ZhaomuApiService {
  private baseURL: string
  private timeout: number

  constructor() {
    this.baseURL = apiConfig.baseURL
    this.timeout = apiConfig.timeout
  }

  // 发送HTTP请求
  private async sendRequest<T>(url: string, options: RequestInit = {}): Promise<ApiResponse<T>> {
    const controller = new AbortController()
    const timeoutId = setTimeout(() => controller.abort(), this.timeout)

    try {
      const response = await fetch(url, {
        ...options,
        signal: controller.signal,
        headers: {
          'Content-Type': 'application/json',
          ...options.headers
        }
      })

      clearTimeout(timeoutId)

      if (!response.ok) {
        throw new Error(`HTTP ${response.status}: ${response.statusText}`)
      }

      const data = await response.json()
      return data
    } catch (error) {
      clearTimeout(timeoutId)
      if (error instanceof Error) {
        if (error.name === 'AbortError') {
          throw new Error('请求超时')
        }
        throw error
      }
      throw new Error('网络请求失败')
    }
  }

  // 获取机器信息
  async getMachineInfo(): Promise<ApiResponse<MachineInfo>> {
    const url = zhaomuApi.getMachineInfo()
    return this.sendRequest<MachineInfo>(url)
  }

  // 重启/开机服务器
  async rebootServer(): Promise<ApiResponse<any>> {
    const url = zhaomuApi.rebootServer()
    return this.sendRequest<any>(url)
  }

  // 关机服务器
  async shutdownServer(): Promise<ApiResponse<any>> {
    const url = zhaomuApi.shutdownServer()
    return this.sendRequest<any>(url)
  }

  // 重置服务器密码
  async resetPassword(password: string): Promise<ApiResponse<any>> {
    const url = zhaomuApi.resetPassword()
    return this.sendRequest<any>(url, {
      method: 'POST',
      body: JSON.stringify({ password })
    })
  }

  // 重装服务器
  async rebuildServer(imageId: number): Promise<ApiResponse<any>> {
    const url = zhaomuApi.rebuildServer()
    return this.sendRequest<any>(url, {
      method: 'POST',
      body: JSON.stringify({ imageId })
    })
  }

  // 获取重装云服务器的镜像列表
  async getCloudServerImages(): Promise<ApiResponse<ImageGroup[]>> {
    const url = zhaomuApi.getCloudServerImages()
    return this.sendRequest<ImageGroup[]>(url)
  }

  // 切换支付周期
  async switchPaymentCycle(cycle: string): Promise<ApiResponse> {
    const url = zhaomuApi.switchPaymentCycle(cycle)
    return this.sendRequest(url)
  }
}

// 导出API服务实例
export const zhaomuApiService = new ZhaomuApiService()

// 导出API配置
export { apiConfig }
