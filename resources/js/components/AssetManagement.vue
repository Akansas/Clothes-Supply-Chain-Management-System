<template>
  <div style="margin: 1rem 0; background: #f1f8e9; border-radius: 8px; padding: 1rem;">
    <h4>Asset Management</h4>
    <div v-if="loading">Loading asset data...</div>
    <div v-else-if="partner">
      <ul>
        <li><b>Name:</b> {{ partner.name }}</li>
        <li><b>On-time rate:</b> {{ partner.on_time_rate }}%</li>
        <li><b>Feedback:</b> {{ partner.avg_feedback }}/5</li>
        <li><b>Efficiency:</b> {{ partner.efficiency }}</li>
        <li><b>Vehicle fuel:</b> {{ partner.vehicle.fuel }}</li>
        <li><b>Vehicle maintenance:</b> {{ partner.vehicle.maintenance }}</li>
      </ul>
    </div>
    <div v-else>No asset data found.</div>
  </div>
</template>
<script setup>
import { ref, onMounted } from 'vue';
const loading = ref(true);
const partner = ref(null);

async function fetchWorkforce() {
  const res = await fetch('/api/delivery/workforce', { headers: { 'Accept': 'application/json' } });
  if (!res.ok) return;
  const arr = await res.json();
  partner.value = arr[0] || null;
  loading.value = false;
}

onMounted(fetchWorkforce);
</script> 