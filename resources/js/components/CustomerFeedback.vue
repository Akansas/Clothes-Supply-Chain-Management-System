<template>
  <div style="margin: 1rem 0; background: #fffde7; border-radius: 8px; padding: 1rem;">
    <h4>Customer Feedback Overview</h4>
    <div style="height: 220px; background: #fffbe7; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: #ff6f00; margin-bottom: 1rem;">
      <canvas v-if="chartData.labels.length" ref="chartCanvas"></canvas>
      <span v-else>Loading chart...</span>
    </div>
    <div v-if="loading">Loading feedback...</div>
    <ul v-else>
      <li v-for="item in feedback" :key="item.order_id + '-' + item.customer_name">
        <span style="font-weight:bold;">{{ item.customer_name }}</span> (Order #{{ item.order_id }}) -
        <span v-for="n in item.rating" :key="n">⭐</span>
        <span v-for="n in 5 - item.rating" :key="'empty' + n" style="color:#ccc;">⭐</span>
        <br />
        <span style="font-style:italic;">"{{ item.comment }}"</span>
        <span style="color:#888; font-size:0.9em;">({{ item.date }})</span>
      </li>
    </ul>
  </div>
</template>
<script setup>
import { ref, onMounted, watch } from 'vue';
const props = defineProps({ role: String, userId: [String, Number] });
const loading = ref(true);
const feedback = ref([]);
const chartCanvas = ref(null);
let chartInstance = null;

const chartData = ref({ labels: [], data: [] });

async function fetchFeedback() {
  try {
    const res = await fetch('/api/delivery/feedback', { headers: { 'Accept': 'application/json' } });
    if (!res.ok) throw new Error('Failed to fetch feedback');
    feedback.value = await res.json();
    // Prepare chart data: average rating per day (last 7 days)
    const byDate = {};
    feedback.value.forEach(f => {
      if (!byDate[f.date]) byDate[f.date] = [];
      byDate[f.date].push(f.rating);
    });
    const dates = Object.keys(byDate).sort().slice(-7);
    chartData.value.labels = dates;
    chartData.value.data = dates.map(d => {
      const arr = byDate[d];
      return arr.reduce((a, b) => a + b, 0) / arr.length;
    });
  } catch (e) {
    feedback.value = [];
    chartData.value = { labels: [], data: [] };
  } finally {
    loading.value = false;
  }
}

onMounted(async () => {
  await fetchFeedback();
  if (typeof window.Chart === 'undefined') {
    await loadChartJs();
  }
  renderChart();
});

watch(chartData, renderChart);

function renderChart() {
  if (!chartCanvas.value || !window.Chart) return;
  if (chartInstance) chartInstance.destroy();
  chartInstance = new window.Chart(chartCanvas.value.getContext('2d'), {
    type: 'bar',
    data: {
      labels: chartData.value.labels,
      datasets: [{
        label: 'Avg Rating',
        data: chartData.value.data,
        backgroundColor: '#ffb300',
      }]
    },
    options: {
      scales: {
        y: { min: 0, max: 5, ticks: { stepSize: 1 } }
      }
    }
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