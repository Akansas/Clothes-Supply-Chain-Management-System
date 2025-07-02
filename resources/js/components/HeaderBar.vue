<template>
  <header style="background: #1976d2; color: #fff; padding: 1rem; display: flex; align-items: center; justify-content: space-between;">
    <div style="font-weight: bold; font-size: 1.3rem;">
      <span v-if="role === 'manager'">Gen Z Fashion Delivery Management Dashboard</span>
      <span v-else>Gen Z Fashion Delivery Dashboard</span>
    </div>
    <div style="display: flex; gap: 1rem; align-items: center;">
      <span>📅</span> <!-- Date/Time Filter -->
      <span>🌍</span> <!-- Region Selector -->
      <span>🔍</span> <!-- Search -->
      <span>👤</span> <!-- User Profile -->
    </div>
  </header>
</template>
<script setup>
defineProps({ role: String });
</script> 