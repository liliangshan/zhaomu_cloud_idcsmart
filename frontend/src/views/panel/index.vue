<template>
  <div class="hlwidc-machine-info-container">
  

    <!-- 弹窗 -->
    <Modal 
      :show="showModal"
      :title="modalTitle"
      :message="modalMessage"
      :type="modalType"
      @close="closeModal"
      @confirm="confirmAction"
      @cancel="cancelAction"
    />

    <!-- 密码修改弹窗 -->
    <PasswordModal 
      :show="showPasswordModalFlag"
      :loading="passwordLoading"
      @close="closePasswordModal"
      @confirm="confirmPasswordChange"
    />

    <!-- 重装系统弹窗 -->
    <RebuildModal 
      :show="showRebuildModalFlag"
      :image-groups="imageGroups"
      :image-loading="imageLoading"
      :image-error="imageError"
      :loading="rebuildLoading"
      :current-image="machineInfo?.cloud_server_info?.image"
      @close="closeRebuildModal"
      @load-images="loadImages"
      @confirm="confirmRebuild"
    />
      <!-- 机器信息 -->
    <div v-if="machineInfo" class="machine-content">
      <!-- 基础信息和官方服务器信息 - 单列显示 -->
      <div class="hlwidc-single-column">
        <!-- 基础信息 -->
        <BasicInfo 
          :machine-info="machineInfo"
          :action-loading="actionLoading"
          :should-disable-buttons="shouldDisableButtons"
          :payment-loading="paymentLoading"
          @start-server="startServer"
          @stop-server="stopServer"
          @reboot-server="rebootServer"
          @switch-payment-cycle="switchPaymentCycle"
        />

        <!-- 官方服务器信息 -->
        <CloudServerInfo 
          :machine-info="machineInfo"
          :should-disable-buttons="shouldDisableButtons"
          @show-password-modal="showPasswordModal"
        />
      </div>

      <!-- 服务器配置 -->
      <ServerConfig 
        :machine-info="machineInfo"
        :should-disable-buttons="shouldDisableButtons"
        @show-rebuild-modal="showRebuildModal"
      />


    </div>
</div>
</template>


<script setup lang="ts">
import { ref, onMounted, onUnmounted, computed } from 'vue'
import { zhaomuApiService, type MachineInfo, type ImageGroup } from '@/services/panel'
import BasicInfo from '@/components/panel/BasicInfo.vue'
import CloudServerInfo from '@/components/panel/CloudServerInfo.vue'
import ServerConfig from '@/components/panel/ServerConfig.vue'
import Modal from '@/components/panel/Modal.vue'
import PasswordModal from '@/components/panel/PasswordModal.vue'
import RebuildModal from '@/components/panel/RebuildModal.vue'

// 响应式数据
const machineInfo = ref<MachineInfo | null>(null)
const loading = ref(false)
const error = ref('')
const showPassword = ref(false)
const actionLoading = ref(false)

// 定时器相关
const statusTimer = ref<number | null>(null)
const isStatusPolling = ref(false)

// 弹窗相关
const showModal = ref(false)
const modalTitle = ref('')
const modalMessage = ref('')
const modalType = ref<'alert' | 'confirm'>('alert')
const pendingAction = ref<(() => void) | null>(null)

// 密码修改相关
const showPasswordModalFlag = ref(false)
const passwordLoading = ref(false)

// 重装系统相关
const showRebuildModalFlag = ref(false)
const imageGroups = ref<ImageGroup[]>([])
const imageLoading = ref(false)
const imageError = ref('')
const rebuildLoading = ref(false)

// 支付周期切换相关
const paymentLoading = ref(false)

// 加载机器信息
const loadMachineInfo = async () => {
  loading.value = true
  error.value = ''

  try {
    const response = await zhaomuApiService.getMachineInfo()
    if (response.code === 1 && response.data) {
      machineInfo.value = response.data
      
      // 检查是否需要开始状态轮询
      if (shouldPollStatus(response.data.cloud_server_info?.status)) {
        startStatusPolling()
      }
    } else {
      error.value = response.msg || '获取机器信息失败'
    }
  } catch (err) {
    error.value = err instanceof Error ? err.message : '网络请求失败'
  } finally {
    loading.value = false
  }
}

// 切换密码显示
const togglePassword = () => {
  showPassword.value = !showPassword.value
}

// 获取状态文本
const getStatusText = (status?: string) => {
  const statusMap: Record<string, string> = {
    'Active': '运行中',
    'Suspended': '已暂停',
    'Terminated': '已终止',
    'Pending': '待处理',
    'Cancelled': '已取消'
  }
  return statusMap[status || ''] || status || '未知'
}

// 获取云服务器状态文本
const getCloudServerStatusText = (status?: number) => {
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
  return statusMap[status || 0] || '未知状态'
}

// 获取云服务器状态样式类
const getCloudServerStatusClass = (status?: number) => {
  const classMap: Record<number, string> = {
    1: 'status-active',
    2: 'status-active',
    3: 'status-stopped',
    4: 'status-stopping',
    5: 'status-starting',
    6: 'status-starting',
    7: 'status-stopping',
    8: 'status-pausing',
    9: 'status-paused'
  }
  return classMap[status || 0] || 'status-unknown'
}

// 获取状态样式类
const getStatusClass = (status?: string) => {
  const classMap: Record<string, string> = {
    'Active': 'status-active',
    'Suspended': 'status-suspended',
    'Terminated': 'status-terminated',
    'Pending': 'status-pending',
    'Cancelled': 'status-cancelled'
  }
  return classMap[status || ''] || 'status-unknown'
}

// 格式化日期
const formatDate = (date: string | number) => {
  if (!date) return '未知'
  const d = new Date(typeof date === 'string' ? date : date * 1000)
  return d.toLocaleString('zh-CN')
}

// 格式化内存
const formatMemory = (ram: number) => {
  if (ram >= 1024) {
    return `${(ram / 1024).toFixed(1)} GB`
  }
  return `${ram} MB`
}

// 检查状态是否需要轮询
const shouldPollStatus = (status?: number) => {
  if (!status) return false
  // 状态码1(运行中)、2(运行中)、3(已关机)不需要轮询
  return status !== 1 && status !== 2 && status !== 3
}

// 检查是否应该禁用所有按钮
const shouldDisableButtons = computed(() => {
  const status = machineInfo.value?.cloud_server_info?.status
  if (!status) return true
  // 只有状态码1、2、3时才不禁用按钮
  return status !== 1 && status !== 2 && status !== 3
})

// 开始状态轮询
const startStatusPolling = () => {
  if (statusTimer.value) {
    clearInterval(statusTimer.value)
  }
  
  statusTimer.value = setInterval(async () => {
    if (!machineInfo.value?.cloud_server_info?.status) return
    
    // 检查是否还需要轮询
    if (!shouldPollStatus(machineInfo.value.cloud_server_info.status)) {
      stopStatusPolling()
      return
    }
    
    try {
      // 静默刷新机器信息
      const response = await zhaomuApiService.getMachineInfo()
      if (response.code === 1 && response.data) {
        machineInfo.value = response.data
        
        // 检查新状态是否还需要轮询
        if (!shouldPollStatus(response.data.cloud_server_info?.status)) {
          stopStatusPolling()
        }
      }
    } catch (err) {
      console.warn('状态轮询失败:', err)
    }
  }, 10000) // 10秒
  
  isStatusPolling.value = true
}

// 停止状态轮询
const stopStatusPolling = () => {
  if (statusTimer.value) {
    clearInterval(statusTimer.value)
    statusTimer.value = null
  }
  isStatusPolling.value = false
}

// 弹窗方法
const showAlert = (title: string, message: string) => {
  modalTitle.value = title
  modalMessage.value = message
  modalType.value = 'alert'
  showModal.value = true
}

const showConfirm = (title: string, message: string, action: () => void) => {
  modalTitle.value = title
  modalMessage.value = message
  modalType.value = 'confirm'
  pendingAction.value = action
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  pendingAction.value = null
}

const confirmAction = () => {
  if (pendingAction.value) {
    pendingAction.value()
  }
  closeModal()
}

const cancelAction = () => {
  closeModal()
}

// 密码修改相关方法
const showPasswordModal = () => {
  showPasswordModalFlag.value = true
}

const closePasswordModal = () => {
  showPasswordModalFlag.value = false
  passwordLoading.value = false
}

// 确认修改密码
const confirmPasswordChange = async (password: string) => {
  passwordLoading.value = true
  
  try {
    const response = await zhaomuApiService.resetPassword(password)
    if (response.code === 1) {
      showAlert('修改成功', '服务器密码修改成功')
      closePasswordModal()
      // 刷新机器信息以获取新密码
      await loadMachineInfo()
    } else {
      showAlert('修改失败', response.msg || '密码修改失败')
    }
  } catch (err) {
    showAlert('修改失败', err instanceof Error ? err.message : '网络请求失败')
  } finally {
    passwordLoading.value = false
  }
}

// 重装系统相关方法
const showRebuildModal = async () => {
  showRebuildModalFlag.value = true
  imageError.value = ''
  
  // 自动加载镜像列表
  await loadImages()
}

const closeRebuildModal = () => {
  showRebuildModalFlag.value = false
  imageGroups.value = []
  imageError.value = ''
  rebuildLoading.value = false
}


const loadImages = async () => {
  imageLoading.value = true
  imageError.value = ''
  
  try {
    const response = await zhaomuApiService.getCloudServerImages()
    if (response.code === 1 && response.data) {
      imageGroups.value = response.data
    } else {
      imageError.value = response.msg || '获取镜像列表失败'
    }
  } catch (err) {
    imageError.value = err instanceof Error ? err.message : '网络请求失败'
  } finally {
    imageLoading.value = false
  }
}

const confirmRebuild = async (imageId: number) => {
  if (!imageId) {
    showAlert('选择错误', '请选择要安装的操作系统')
    return
  }
  
  showConfirm('确认重装', '确定要重装系统吗？这将删除所有数据！', async () => {
    rebuildLoading.value = true
    
    try {
      const response = await zhaomuApiService.rebuildServer(imageId)
      if (response.code === 1) {
        showAlert('重装成功', '系统重装命令已发送，大约需要10-15分钟完成')
        closeRebuildModal()
        // 开始状态轮询
        startStatusPolling()
      } else {
        showAlert('重装失败', response.msg || '重装操作失败')
      }
    } catch (err) {
      showAlert('重装失败', err instanceof Error ? err.message : '网络请求失败')
    } finally {
      rebuildLoading.value = false
    }
  })
}

// 切换支付周期
const switchPaymentCycle = async (cycle: string) => {
  if (paymentLoading.value) return
  
  // 如果已经是当前周期，不需要切换
  if (machineInfo.value?.payment === cycle) {
    return
  }
  
  paymentLoading.value = true
  
  try {
    const response = await zhaomuApiService.switchPaymentCycle(cycle)
    if (response.code === 1) {
      showAlert('切换成功', response.msg || '支付周期切换成功')
      // 重新加载机器信息以获取最新数据
      await loadMachineInfo()
    } else {
      showAlert('切换失败', response.msg || '支付周期切换失败')
    }
  } catch (err) {
    showAlert('切换失败', err instanceof Error ? err.message : '网络请求失败')
  } finally {
    paymentLoading.value = false
  }
}

// 开机服务器
const startServer = async () => {
  if (!machineInfo.value?.business_id) {
    showAlert('操作失败', '业务ID不存在，无法执行操作')
    return
  }
  
  actionLoading.value = true
  
  // 立即设置状态为开机中
  if (machineInfo.value.cloud_server_info) {
    machineInfo.value.cloud_server_info.status = 5 // 开机中
  }
  
  try {
    const response = await zhaomuApiService.rebootServer()
    if (response.code === 1) {
      showAlert('操作成功', response.msg || '开机命令发送成功，大约需要2分钟时间')
      // 开始状态轮询
      startStatusPolling()
    } else {
      showAlert('开机失败', response.msg || '开机操作失败')
      // 如果操作失败，恢复原状态
      if (machineInfo.value.cloud_server_info) {
        machineInfo.value.cloud_server_info.status = 3 // 恢复为已关机
      }
    }
  } catch (err) {
    showAlert('开机失败', err instanceof Error ? err.message : '网络请求失败')
    // 如果操作失败，恢复原状态
    if (machineInfo.value.cloud_server_info) {
      machineInfo.value.cloud_server_info.status = 3 // 恢复为已关机
    }
  } finally {
    actionLoading.value = false
  }
}

// 关机服务器
const stopServer = async () => {
  if (!machineInfo.value?.business_id) {
    showAlert('操作失败', '业务ID不存在，无法执行操作')
    return
  }
  
  showConfirm('确认关机', '确定要关机服务器吗？', async () => {
    actionLoading.value = true
    
    // 保存原状态用于失败时恢复
    const originalStatus = machineInfo.value?.cloud_server_info?.status
    
    // 立即设置状态为关机中
    if (machineInfo.value?.cloud_server_info) {
      machineInfo.value.cloud_server_info.status = 7 // 关机中
    }
    
    try {
      const response = await zhaomuApiService.shutdownServer()
      if (response.code === 1) {
        showAlert('操作成功', response.msg || '关机命令发送成功，大约需要2分钟时间')
        // 开始状态轮询
        startStatusPolling()
      } else {
        showAlert('关机失败', response.msg || '关机操作失败')
        // 如果操作失败，恢复原状态
        if (machineInfo.value?.cloud_server_info && originalStatus !== undefined) {
          machineInfo.value.cloud_server_info.status = originalStatus
        }
      }
    } catch (err) {
      showAlert('关机失败', err instanceof Error ? err.message : '网络请求失败')
      // 如果操作失败，恢复原状态
      if (machineInfo.value?.cloud_server_info && originalStatus !== undefined) {
        machineInfo.value.cloud_server_info.status = originalStatus
      }
    } finally {
      actionLoading.value = false
    }
  })
}

// 重启服务器
const rebootServer = async () => {
  if (!machineInfo.value?.business_id) {
    showAlert('操作失败', '业务ID不存在，无法执行操作')
    return
  }
  
  showConfirm('确认重启', '确定要重启服务器吗？', async () => {
    actionLoading.value = true
    
    // 保存原状态用于失败时恢复
    const originalStatus = machineInfo.value?.cloud_server_info?.status
    
    // 立即设置状态为重启中
    if (machineInfo.value?.cloud_server_info) {
      machineInfo.value.cloud_server_info.status = 5 // 重启中（使用开机中状态）
    }
    
    try {
      const response = await zhaomuApiService.rebootServer()
      if (response.code === 1) {
        showAlert('操作成功', response.msg || '重启命令发送成功，大约需要2分钟时间')
        // 开始状态轮询
        startStatusPolling()
      } else {
        showAlert('重启失败', response.msg || '重启操作失败')
        // 如果操作失败，恢复原状态
        if (machineInfo.value?.cloud_server_info && originalStatus !== undefined) {
          machineInfo.value.cloud_server_info.status = originalStatus
        }
      }
    } catch (err) {
      showAlert('重启失败', err instanceof Error ? err.message : '网络请求失败')
      // 如果操作失败，恢复原状态
      if (machineInfo.value?.cloud_server_info && originalStatus !== undefined) {
        machineInfo.value.cloud_server_info.status = originalStatus
      }
    } finally {
      actionLoading.value = false
    }
  })
}

// 组件挂载时加载数据
onMounted(() => {
  loadMachineInfo()
})

// 组件卸载时清理定时器
onUnmounted(() => {
  stopStatusPolling()
})
</script>

<style scoped>
.hlwidc-machine-info-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 20px;
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

/* 单列显示样式 */
.hlwidc-single-column {
  display: flex;
  flex-direction: column;
  gap: 20px;
  margin-bottom: 20px;
}

/* 机器内容区域 */
.machine-content {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

/* 响应式设计 */
@media (max-width: 768px) {
  .hlwidc-machine-info-container {
    padding: 16px;
  }
  
  .hlwidc-single-column {
    gap: 16px;
  }
  
  .machine-content {
    gap: 16px;
  }
}
</style>