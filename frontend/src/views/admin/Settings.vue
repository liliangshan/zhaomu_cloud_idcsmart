<template>
  <div class="settings-page">
    <div class="page-header">
      <h1>系统设置</h1>
    </div>
    
    <div class="content">
      <p>系统设置页面内容</p>
    </div>
  </div>
</template>

<script setup lang="ts">
</script>

<style scoped>
.settings-page {
  padding: 30px;
}

.page-header {
  margin-bottom: 30px;
}

.page-header h1 {
  margin: 0;
  font-size: 2rem;
  color: #1f2937;
}

.content {
  background: white;
  border-radius: 12px;
  padding: 30px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  border: 1px solid #e5e7eb;
}
</style>
