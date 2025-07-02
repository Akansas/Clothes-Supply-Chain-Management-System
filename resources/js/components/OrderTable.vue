<template>
  <div style="margin: 1rem 0;">
    <div v-if="loading">Loading orders...</div>
    <table v-else style="width: 100%; border-collapse: collapse; background: #fff; border-radius: 8px; overflow: hidden;">
      <thead style="background: #1976d2; color: #fff;">
        <tr>
          <th>OrderID</th>
          <th>CustomerID</th>
          <th>Location</th>
          <th v-if="role === 'manager'">DeliveryPartner</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="order in orders" :key="order.id">
          <td>{{ order.id }}</td>
          <td>{{ order.customer_id }}</td>
          <td>{{ order.location }}</td>
          <td v-if="role === 'manager'">{{ order.delivery_partner }}</td>
          <td>
            <select :value="order.status" @change="updateStatus(order, $event)">
              <option>delivered</option>
              <option>failed</option>
              <option>pending</option>
              <option>out for delivery</option>
            </select>
            <span v-if="order._saving">⏳</span>
            <span v-if="order._saved">✅</span>
            <span v-if="order._error" style="color:red;">❌</span>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
<script setup>
import { ref, onMounted } from 'vue';
const props = defineProps({ role: String, userId: [String, Number] });
const loading = ref(true);
const orders = ref([]);

onMounted(async () => {
  try {
    const res = await fetch('/api/delivery/orders', { headers: { 'Accept': 'application/json' } });
    if (!res.ok) throw new Error('Failed to fetch orders');
    const data = await res.json();
    // Initialize status flags
    orders.value = data.map(o => ({ ...o, _saving: false, _saved: false, _error: false }));
  } catch (e) {
    orders.value = [];
  } finally {
    loading.value = false;
  }
  // Real-time updates
  if (window.Echo) {
    window.Echo.channel('deliveries')
      .listen('DeliveryStatusUpdated', (e) => {
        const order = orders.value.find(o => o.id === e.id);
        if (order) {
          order.status = e.status;
        }
      });
  }
});

function updateStatus(order, event) {
  const newStatus = event.target.value;
  order._saving = true;
  order._saved = false;
  order._error = false;
  fetch(`/api/delivery/orders/${order.id}/status`, {
    method: 'PATCH',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json'
    },
    body: JSON.stringify({ status: newStatus })
  })
    .then(res => {
      if (!res.ok) throw new Error('Failed');
      return res.json();
    })
    .then(data => {
      order.status = data.status;
      order._saved = true;
      setTimeout(() => order._saved = false, 1500);
    })
    .catch(() => {
      order._error = true;
      setTimeout(() => order._error = false, 2000);
    })
    .finally(() => {
      order._saving = false;
    });
}
</script> 