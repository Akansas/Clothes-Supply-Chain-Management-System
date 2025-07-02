<template>
  <div style="margin: 1rem 0; background: #fce4ec; border-radius: 8px; padding: 1rem;">
    <h4>Digital Delivery Confirmation</h4>
    <button @click="showForm = !showForm" style="background: #d81b60; color: #fff; border: none; border-radius: 4px; padding: 0.5rem 1rem; cursor: pointer;">
      {{ showForm ? 'Hide' : 'Show' }} Delivery Form
    </button>
    <div v-if="showForm" style="margin-top: 1rem;">
      <form @submit.prevent="confirmDelivery">
        <label>Customer Name: <input type="text" required /></label><br />
        <label>Order ID: <input type="text" required /></label><br />
        <label>Signature: <input type="text" placeholder="(Simulated)" required /></label><br />
        <button type="submit" style="background: #43a047; color: #fff; border: none; border-radius: 4px; padding: 0.5rem 1rem; margin-top: 0.5rem;">Confirm Receipt</button>
      </form>
      <div v-if="confirmed" style="color: #43a047; margin-top: 0.5rem;">Delivery confirmed!</div>
    </div>
  </div>
</template>
<script setup>
import { ref } from 'vue';
const showForm = ref(false);
const confirmed = ref(false);
function confirmDelivery() {
  confirmed.value = true;
}
</script> 