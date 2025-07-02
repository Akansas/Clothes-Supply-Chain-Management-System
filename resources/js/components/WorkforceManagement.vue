<template>
  <div style="margin: 1rem 0; background: #f9fbe7; border-radius: 8px; padding: 1rem;">
    <h4>Workforce Management</h4>
    <div v-if="loading">Loading workforce...</div>
    <div v-else>
      <ul>
        <li v-for="p in partners" :key="p.name">
          <b>{{ p.name }}</b> - On-time: {{ p.on_time_rate }}%, Feedback: {{ p.avg_feedback }}/5, Efficiency: {{ p.efficiency }}<br>
          <span style="font-size:0.95em; color:#666;">Vehicle: Fuel {{ p.vehicle.fuel }}, Maintenance: {{ p.vehicle.maintenance }}</span>
        </li>
      </ul>
    </div>
  </div>
</template>
<script setup>
import { ref, onMounted } from 'vue';
const loading = ref(true);
const partners = ref([]);

async function fetchWorkforce() {
  const res = await fetch('/api/delivery/workforce', { headers: { 'Accept': 'application/json' } });
  if (!res.ok) return;
  partners.value = await res.json();
  loading.value = false;
}

onMounted(fetchWorkforce);
</script> 