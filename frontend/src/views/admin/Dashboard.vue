<template>
  <div class="dashboard">
    <div class="page-header">
      <h1>仪表盘</h1>
      <p>欢迎来到管理后台</p>
    </div>
    
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon">
          <UserIcon class="icon" />
        </div>
        <div class="stat-content">
          <h3>总用户数</h3>
          <p class="stat-number">1,234</p>
        </div>
      </div>
      
      <div class="stat-card">
        <div class="stat-icon">
          <CubeIcon class="icon" />
        </div>
        <div class="stat-content">
          <h3>产品数量</h3>
          <p class="stat-number">56</p>
        </div>
      </div>
      
      <div class="stat-card">
        <div class="stat-icon">
          <ShoppingCartIcon class="icon" />
        </div>
        <div class="stat-content">
          <h3>订单总数</h3>
          <p class="stat-number">789</p>
        </div>
      </div>
      
      <div class="stat-card">
        <div class="stat-icon">
          <CurrencyDollarIcon class="icon" />
        </div>
        <div class="stat-content">
          <h3>总收入</h3>
          <p class="stat-number">¥12,345</p>
        </div>
      </div>
    </div>
    
    <div class="content-grid">
      <div class="chart-section">
        <h2>销售趋势</h2>
        <div class="chart-placeholder">
          <ChartBarIcon class="chart-icon" />
          <p>图表数据展示区域</p>
        </div>
      </div>
      
      <div class="recent-section">
        <h2>最近活动</h2>
        <div class="activity-list">
          <div class="activity-item">
            <UserIcon class="activity-icon" />
            <div class="activity-content">
              <p>新用户注册</p>
              <span class="activity-time">2分钟前</span>
            </div>
          </div>
          <div class="activity-item">
            <ShoppingCartIcon class="activity-icon" />
            <div class="activity-content">
              <p>新订单创建</p>
              <span class="activity-time">5分钟前</span>
            </div>
          </div>
          <div class="activity-item">
            <CubeIcon class="activity-icon" />
            <div class="activity-content">
              <p>产品更新</p>
              <span class="activity-time">10分钟前</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { 
  UserIcon, 
  CubeIcon, 
  ShoppingCartIcon, 
  CurrencyDollarIcon,
  ChartBarIcon 
} from '@heroicons/vue/24/outline'
</script>

<style scoped>
.dashboard {
  padding: 30px;
}

.page-header {
  margin-bottom: 30px;
}

.page-header h1 {
  margin: 0 0 10px 0;
  font-size: 2rem;
  color: #1f2937;
}

.page-header p {
  margin: 0;
  color: #6b7280;
  font-size: 1.1rem;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 20px;
  margin-bottom: 30px;
}

.stat-card {
  background: white;
  border-radius: 12px;
  padding: 24px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  display: flex;
  align-items: center;
  gap: 16px;
  border: 1px solid #e5e7eb;
}

.stat-icon {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.stat-icon .icon {
  width: 24px;
  height: 24px;
  color: white;
}

.stat-content h3 {
  margin: 0 0 8px 0;
  font-size: 0.875rem;
  color: #6b7280;
  font-weight: 500;
}

.stat-number {
  margin: 0;
  font-size: 1.875rem;
  font-weight: 700;
  color: #1f2937;
}

.content-grid {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 30px;
}

.chart-section,
.recent-section {
  background: white;
  border-radius: 12px;
  padding: 24px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  border: 1px solid #e5e7eb;
}

.chart-section h2,
.recent-section h2 {
  margin: 0 0 20px 0;
  font-size: 1.25rem;
  color: #1f2937;
}

.chart-placeholder {
  height: 300px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  background: #f9fafb;
  border-radius: 8px;
  border: 2px dashed #d1d5db;
}

.chart-icon {
  width: 48px;
  height: 48px;
  color: #9ca3af;
  margin-bottom: 12px;
}

.chart-placeholder p {
  margin: 0;
  color: #6b7280;
}

.activity-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.activity-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px;
  background: #f9fafb;
  border-radius: 8px;
}

.activity-icon {
  width: 20px;
  height: 20px;
  color: #6b7280;
}

.activity-content p {
  margin: 0 0 4px 0;
  font-weight: 500;
  color: #1f2937;
}

.activity-time {
  font-size: 0.875rem;
  color: #6b7280;
}

@media (max-width: 768px) {
  .content-grid {
    grid-template-columns: 1fr;
  }
  
  .stats-grid {
    grid-template-columns: 1fr;
  }
}
</style>
