<template>
  <div style="margin: 1rem 0; background: #e1f5fe; border-radius: 8px; padding: 1rem;">
    <h4>Delivery Trends & Insights</h4>
    <div v-if="loading">Loading trends...</div>
    <div v-else>
      <div style="height: 180px; margin-bottom: 1rem;">
        <canvas ref="historyChart"></canvas>
      </div>
      <div style="height: 120px;">
        <canvas ref="forecastChart"></canvas>
      </div>
    </div>
  </div>
</template>
<script setup>
import { ref, onMounted } from 'vue';
const loading = ref(true);
const stats = ref([]);
const forecast = ref([]);
const historyChart = ref(null);
const forecastChart = ref(null);
let historyInstance = null;
let forecastInstance = null;

async function fetchTrends() {
  const res = await fetch('/api/delivery/trends', { headers: { 'Accept': 'application/json' } });
  if (!res.ok) return;
  const data = await res.json();
  stats.value = data.stats;
  forecast.value = data.forecast;
}

onMounted(async () => {
  await fetchTrends();
  if (typeof window.Chart === 'undefined') {
    await loadChartJs();
  }
  renderCharts();
  loading.value = false;
});

function renderCharts() {
  if (!historyChart.value || !forecastChart.value || !window.Chart) return;
  if (historyInstance) historyInstance.destroy();
  if (forecastInstance) forecastInstance.destroy();
  // Historical chart
  historyInstance = new window.Chart(historyChart.value.getContext('2d'), {
    type: 'line',
    data: {
      labels: stats.value.map(s => s.date),
      datasets: [
        { label: 'Total', data: stats.value.map(s => s.total_deliveries), borderColor: '#0288d1', fill: false },
        { label: 'On-Time', data: stats.value.map(s => s.on_time_deliveries), borderColor: '#43a047', fill: false },
        { label: 'Exceptions', data: stats.value.map(s => s.exceptions), borderColor: '#e53935', fill: false },
      ]
    },
    options: { responsive: true, plugins: { legend: { position: 'top' } } }
  });
  // Forecast chart
  forecastInstance = new window.Chart(forecastChart.value.getContext('2d'), {
    type: 'line',
    data: {
      labels: forecast.value.map(f => f.date),
      datasets: [
        { label: 'Predicted Deliveries', data: forecast.value.map(f => f.predicted_deliveries), borderColor: '#ffb300', fill: false }
      ]
    },
    options: { responsive: true, plugins: { legend: { position: 'top' } } }
  });
}

async function loadChartJs() {
  return new Promise(resolve => {
    const script = document.createElement('script');
    script.src = 'https://cdn.jsdelivr.net/npm/chart.js';
    script.onload = resolve;
    document.head.appendChild(script);
  });
}
</script> 