<template>
  <div style="display: flex; gap: 1rem; margin: 1rem 0; flex-wrap: wrap;">
    <div v-if="loading">Loading KPIs...</div>
    <div v-else v-for="kpi in kpis" :key="kpi.title" @click="alert(kpi.title)" style="background: #f5f5f5; border-radius: 8px; padding: 1rem; min-width: 180px; cursor: pointer; box-shadow: 0 2px 8px #0001;">
      <div style="font-size: 0.9rem; color: #888;">{{ kpi.title }}</div>
      <div style="font-size: 1.5rem; font-weight: bold;">{{ kpi.value }}</div>
    </div>
  </div>
</template>
<script setup>
import { ref, onMounted } from 'vue';
const props = defineProps({ role: String, userId: [String, Number] });
const loading = ref(true);
const kpis = ref([
  { title: 'On-time delivery rate', value: '-' },
  { title: 'Average delivery time', value: '-' },
  { title: 'Active deliveries', value: '-' },
  { title: 'Delivery exceptions', value: '-' },
  { title: 'Cost per delivery', value: '-' },
  { title: 'Customer satisfaction', value: '-' },
]);

onMounted(async () => {
  try {
    const res = await fetch('/api/delivery/kpis', { headers: { 'Accept': 'application/json' } });
    if (!res.ok) throw new Error('Failed to fetch KPIs');
    const data = await res.json();
    kpis.value = [
      { title: 'On-time delivery rate', value: (data.on_time_delivery_rate * 100).toFixed(1) + '%' },
      { title: 'Average delivery time', value: data.avg_delivery_time + 'h' },
      { title: 'Active deliveries', value: data.active_deliveries },
      { title: 'Delivery exceptions', value: data.delivery_exceptions },
      { title: 'Cost per delivery', value: '$' + data.cost_per_delivery.toFixed(2) },
      { title: 'Customer satisfaction', value: data.customer_satisfaction + '/5' },
    ];
  } catch (e) {
    kpis.value = [{ title: 'Error loading KPIs', value: '' }];
  } finally {
    loading.value = false;
  }
});
</script> 