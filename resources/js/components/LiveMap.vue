<template>
  <div style="height: 300px; background: #e3f2fd; border-radius: 8px; margin: 1rem 0;">
    <div ref="mapContainer" style="height: 100%; width: 100%;"></div>
  </div>
</template>
<script setup>
import { ref, onMounted } from 'vue';
const mapContainer = ref(null);
let map = null;
let markers = [];

async function fetchDeliveries() {
  const res = await fetch('/api/delivery/map', { headers: { 'Accept': 'application/json' } });
  if (!res.ok) return [];
  return await res.json();
}

onMounted(async () => {
  if (!window.L) {
    await loadLeaflet();
  }
  map = window.L.map(mapContainer.value).setView([40, -95], 4); // Center US
  window.L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
  }).addTo(map);
  const deliveries = await fetchDeliveries();
  if (!deliveries.length) {
    window.L.marker([40, -95]).addTo(map).bindPopup('No active deliveries');
    return;
  }
  deliveries.forEach(d => {
    const marker = window.L.marker([d.latitude, d.longitude]).addTo(map);
    marker.bindPopup(`<b>Order #${d.order_id}</b><br>Status: ${d.status}<br>Partner: ${d.delivery_partner || 'N/A'}`);
    markers.push(marker);
  });
});

async function loadLeaflet() {
  return new Promise(resolve => {
    const link = document.createElement('link');
    link.rel = 'stylesheet';
    link.href = 'https://unpkg.com/leaflet/dist/leaflet.css';
    document.head.appendChild(link);
    const script = document.createElement('script');
    script.src = 'https://unpkg.com/leaflet/dist/leaflet.js';
    script.onload = resolve;
    document.head.appendChild(script);
  });
}
</script> 