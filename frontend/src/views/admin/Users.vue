<template>
  <div class="users-page">
    <div class="page-header">
      <h1>用户管理</h1>
      <button class="add-btn">
        <PlusIcon class="btn-icon" />
        添加用户
      </button>
    </div>
    
    <div class="filters">
      <div class="search-box">
        <MagnifyingGlassIcon class="search-icon" />
        <input type="text" placeholder="搜索用户..." class="search-input" />
      </div>
      <select class="filter-select">
        <option>全部状态</option>
        <option>活跃</option>
        <option>禁用</option>
      </select>
    </div>
    
    <div class="table-container">
      <table class="users-table">
        <thead>
          <tr>
            <th>用户ID</th>
            <th>用户名</th>
            <th>邮箱</th>
            <th>状态</th>
            <th>注册时间</th>
            <th>操作</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="user in users" :key="user.id">
            <td>{{ user.id }}</td>
            <td>
              <div class="user-info">
                <div class="avatar">{{ user.name.charAt(0) }}</div>
                <span>{{ user.name }}</span>
              </div>
            </td>
            <td>{{ user.email }}</td>
            <td>
              <span :class="['status', user.status]">
                {{ user.status === 'active' ? '活跃' : '禁用' }}
              </span>
            </td>
            <td>{{ user.createdAt }}</td>
            <td>
              <div class="actions">
                <button class="action-btn edit">
                  <PencilIcon class="action-icon" />
                </button>
                <button class="action-btn delete">
                  <TrashIcon class="action-icon" />
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    
    <div class="pagination">
      <button class="page-btn" disabled>
        <ChevronLeftIcon class="page-icon" />
        上一页
      </button>
      <span class="page-info">第 1 页，共 10 页</span>
      <button class="page-btn">
        下一页
        <ChevronRightIcon class="page-icon" />
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { 
  PlusIcon, 
  MagnifyingGlassIcon, 
  PencilIcon, 
  TrashIcon,
  ChevronLeftIcon,
  ChevronRightIcon 
} from '@heroicons/vue/24/outline'

const users = ref([
  { id: 1, name: '张三', email: 'zhangsan@example.com', status: 'active', createdAt: '2024-01-15' },
  { id: 2, name: '李四', email: 'lisi@example.com', status: 'inactive', createdAt: '2024-01-14' },
  { id: 3, name: '王五', email: 'wangwu@example.com', status: 'active', createdAt: '2024-01-13' },
  { id: 4, name: '赵六', email: 'zhaoliu@example.com', status: 'active', createdAt: '2024-01-12' },
  { id: 5, name: '钱七', email: 'qianqi@example.com', status: 'inactive', createdAt: '2024-01-11' }
])
</script>

<style scoped>
.users-page {
  padding: 30px;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 30px;
}

.page-header h1 {
  margin: 0;
  font-size: 2rem;
  color: #1f2937;
}

.add-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 16px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 500;
  transition: all 0.3s ease;
}

.add-btn:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.btn-icon {
  width: 16px;
  height: 16px;
}

.filters {
  display: flex;
  gap: 16px;
  margin-bottom: 20px;
}

.search-box {
  position: relative;
  flex: 1;
  max-width: 300px;
}

.search-icon {
  position: absolute;
  left: 12px;
  top: 50%;
  transform: translateY(-50%);
  width: 16px;
  height: 16px;
  color: #6b7280;
}

.search-input {
  width: 100%;
  padding: 10px 12px 10px 40px;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 14px;
}

.search-input:focus {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.filter-select {
  padding: 10px 12px;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  background: white;
  font-size: 14px;
}

.table-container {
  background: white;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  border: 1px solid #e5e7eb;
}

.users-table {
  width: 100%;
  border-collapse: collapse;
}

.users-table th {
  background: #f9fafb;
  padding: 16px;
  text-align: left;
  font-weight: 600;
  color: #374151;
  border-bottom: 1px solid #e5e7eb;
}

.users-table td {
  padding: 16px;
  border-bottom: 1px solid #f3f4f6;
}

.user-info {
  display: flex;
  align-items: center;
  gap: 12px;
}

.avatar {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
  font-size: 14px;
}

.status {
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 12px;
  font-weight: 500;
}

.status.active {
  background: #dcfce7;
  color: #166534;
}

.status.inactive {
  background: #fee2e2;
  color: #991b1b;
}

.actions {
  display: flex;
  gap: 8px;
}

.action-btn {
  width: 32px;
  height: 32px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
}

.action-btn.edit {
  background: #fef3c7;
  color: #d97706;
}

.action-btn.edit:hover {
  background: #fde68a;
}

.action-btn.delete {
  background: #fee2e2;
  color: #dc2626;
}

.action-btn.delete:hover {
  background: #fecaca;
}

.action-icon {
  width: 16px;
  height: 16px;
}

.pagination {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 16px;
  margin-top: 30px;
}

.page-btn {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 8px 16px;
  border: 1px solid #d1d5db;
  background: white;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.3s ease;
}

.page-btn:hover:not(:disabled) {
  background: #f9fafb;
  border-color: #9ca3af;
}

.page-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.page-icon {
  width: 16px;
  height: 16px;
}

.page-info {
  color: #6b7280;
  font-size: 14px;
}
</style>
