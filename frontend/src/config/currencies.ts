/**
 * 货币单位配置
 * 包含常用国家和地区的货币信息
 */

export interface Currency {
  code: string
  symbol: string
  name: string
  nameEn: string
}

export const currencies: Currency[] = [
  // 主要货币
  { code: 'CNY', symbol: '¥', name: '人民币', nameEn: 'Chinese Yuan' },
  { code: 'USD', symbol: '$', name: '美元', nameEn: 'US Dollar' },
  { code: 'EUR', symbol: '€', name: '欧元', nameEn: 'Euro' },
  { code: 'GBP', symbol: '£', name: '英镑', nameEn: 'British Pound' },
  { code: 'JPY', symbol: '¥', name: '日元', nameEn: 'Japanese Yen' },
  
  // 亚洲货币
  { code: 'KRW', symbol: '₩', name: '韩元', nameEn: 'South Korean Won' },
  { code: 'HKD', symbol: 'HK$', name: '港币', nameEn: 'Hong Kong Dollar' },
  { code: 'TWD', symbol: 'NT$', name: '台币', nameEn: 'Taiwan Dollar' },
  { code: 'SGD', symbol: 'S$', name: '新加坡元', nameEn: 'Singapore Dollar' },
  { code: 'AUD', symbol: 'A$', name: '澳元', nameEn: 'Australian Dollar' },
  { code: 'CAD', symbol: 'C$', name: '加元', nameEn: 'Canadian Dollar' },
  { code: 'CHF', symbol: 'CHF', name: '瑞士法郎', nameEn: 'Swiss Franc' },
  { code: 'RUB', symbol: '₽', name: '卢布', nameEn: 'Russian Ruble' },
  { code: 'INR', symbol: '₹', name: '印度卢比', nameEn: 'Indian Rupee' },
  { code: 'THB', symbol: '฿', name: '泰铢', nameEn: 'Thai Baht' },
  { code: 'VND', symbol: '₫', name: '越南盾', nameEn: 'Vietnamese Dong' },
  { code: 'MYR', symbol: 'RM', name: '马来西亚林吉特', nameEn: 'Malaysian Ringgit' },
  { code: 'IDR', symbol: 'Rp', name: '印尼盾', nameEn: 'Indonesian Rupiah' },
  { code: 'PHP', symbol: '₱', name: '菲律宾比索', nameEn: 'Philippine Peso' },
  
  // 美洲货币
  { code: 'BRL', symbol: 'R$', name: '巴西雷亚尔', nameEn: 'Brazilian Real' },
  { code: 'MXN', symbol: '$', name: '墨西哥比索', nameEn: 'Mexican Peso' },
  { code: 'ARS', symbol: '$', name: '阿根廷比索', nameEn: 'Argentine Peso' },
  { code: 'CLP', symbol: '$', name: '智利比索', nameEn: 'Chilean Peso' },
  { code: 'COP', symbol: '$', name: '哥伦比亚比索', nameEn: 'Colombian Peso' },
  { code: 'PEN', symbol: 'S/', name: '秘鲁索尔', nameEn: 'Peruvian Sol' },
  { code: 'UYU', symbol: '$U', name: '乌拉圭比索', nameEn: 'Uruguayan Peso' },
  
  // 非洲货币
  { code: 'ZAR', symbol: 'R', name: '南非兰特', nameEn: 'South African Rand' },
  { code: 'EGP', symbol: 'E£', name: '埃及镑', nameEn: 'Egyptian Pound' },
  { code: 'NGN', symbol: '₦', name: '尼日利亚奈拉', nameEn: 'Nigerian Naira' },
  { code: 'KES', symbol: 'KSh', name: '肯尼亚先令', nameEn: 'Kenyan Shilling' },
  { code: 'GHS', symbol: '₵', name: '加纳塞地', nameEn: 'Ghanaian Cedi' },
  { code: 'MAD', symbol: 'MAD', name: '摩洛哥迪拉姆', nameEn: 'Moroccan Dirham' },
  { code: 'TND', symbol: 'TND', name: '突尼斯第纳尔', nameEn: 'Tunisian Dinar' },
  { code: 'DZD', symbol: 'DZD', name: '阿尔及利亚第纳尔', nameEn: 'Algerian Dinar' },
  
  // 中东货币
  { code: 'TRY', symbol: '₺', name: '土耳其里拉', nameEn: 'Turkish Lira' },
  { code: 'ILS', symbol: '₪', name: '以色列新谢克尔', nameEn: 'Israeli New Shekel' },
  { code: 'AED', symbol: 'AED', name: '阿联酋迪拉姆', nameEn: 'UAE Dirham' },
  { code: 'SAR', symbol: 'SAR', name: '沙特里亚尔', nameEn: 'Saudi Riyal' },
  { code: 'QAR', symbol: 'QAR', name: '卡塔尔里亚尔', nameEn: 'Qatari Riyal' },
  { code: 'KWD', symbol: 'KWD', name: '科威特第纳尔', nameEn: 'Kuwaiti Dinar' },
  { code: 'BHD', symbol: 'BHD', name: '巴林第纳尔', nameEn: 'Bahraini Dinar' },
  { code: 'OMR', symbol: 'OMR', name: '阿曼里亚尔', nameEn: 'Omani Rial' },
  { code: 'JOD', symbol: 'JOD', name: '约旦第纳尔', nameEn: 'Jordanian Dinar' },
  { code: 'LBP', symbol: 'LBP', name: '黎巴嫩镑', nameEn: 'Lebanese Pound' },
  { code: 'IQD', symbol: 'IQD', name: '伊拉克第纳尔', nameEn: 'Iraqi Dinar' },
  { code: 'IRR', symbol: 'IRR', name: '伊朗里亚尔', nameEn: 'Iranian Rial' },
  
  // 南亚货币
  { code: 'AFN', symbol: 'AFN', name: '阿富汗尼', nameEn: 'Afghan Afghani' },
  { code: 'PKR', symbol: 'PKR', name: '巴基斯坦卢比', nameEn: 'Pakistani Rupee' },
  { code: 'BDT', symbol: 'BDT', name: '孟加拉塔卡', nameEn: 'Bangladeshi Taka' },
  { code: 'LKR', symbol: 'LKR', name: '斯里兰卡卢比', nameEn: 'Sri Lankan Rupee' },
  { code: 'NPR', symbol: 'NPR', name: '尼泊尔卢比', nameEn: 'Nepalese Rupee' },
  { code: 'BTN', symbol: 'BTN', name: '不丹努尔特鲁姆', nameEn: 'Bhutanese Ngultrum' },
  
  // 东南亚货币
  { code: 'MMK', symbol: 'MMK', name: '缅甸元', nameEn: 'Myanmar Kyat' },
  { code: 'LAK', symbol: 'LAK', name: '老挝基普', nameEn: 'Lao Kip' },
  { code: 'KHR', symbol: 'KHR', name: '柬埔寨瑞尔', nameEn: 'Cambodian Riel' },
  { code: 'BND', symbol: 'BND', name: '文莱元', nameEn: 'Brunei Dollar' },
  
  // 大洋洲货币
  { code: 'FJD', symbol: 'FJD', name: '斐济元', nameEn: 'Fijian Dollar' },
  { code: 'NZD', symbol: 'NZ$', name: '新西兰元', nameEn: 'New Zealand Dollar' },
  
  // 非洲法郎
  { code: 'XOF', symbol: 'XOF', name: '西非法郎', nameEn: 'West African CFA Franc' },
  { code: 'XAF', symbol: 'XAF', name: '中非法郎', nameEn: 'Central African CFA Franc' },
  { code: 'XPF', symbol: 'XPF', name: '太平洋法郎', nameEn: 'CFP Franc' }
]

/**
 * 根据货币代码获取货币信息
 * @param code 货币代码
 * @returns 货币信息或undefined
 */
export function getCurrencyByCode(code: string): Currency | undefined {
  return currencies.find(currency => currency.code === code)
}

/**
 * 根据货币代码获取货币符号
 * @param code 货币代码
 * @returns 货币符号
 */
export function getCurrencySymbol(code: string): string {
  const currency = getCurrencyByCode(code)
  return currency?.symbol || code
}

/**
 * 根据货币代码获取货币名称
 * @param code 货币代码
 * @returns 货币名称
 */
export function getCurrencyName(code: string): string {
  const currency = getCurrencyByCode(code)
  return currency?.name || code
}

/**
 * 获取货币显示文本（符号 + 名称）
 * @param code 货币代码
 * @returns 显示文本
 */
export function getCurrencyDisplayText(code: string): string {
  const currency = getCurrencyByCode(code)
  return currency ? `${currency.symbol} ${currency.name}` : code
}

/**
 * 获取所有支持的货币代码列表
 * @returns 货币代码数组
 */
export function getSupportedCurrencyCodes(): string[] {
  return currencies.map(currency => currency.code)
}

/**
 * 验证货币代码是否支持
 * @param code 货币代码
 * @returns 是否支持
 */
export function isCurrencySupported(code: string): boolean {
  return currencies.some(currency => currency.code === code)
}
