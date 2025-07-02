<template>
  <div>
    <!-- Header Bar -->
    <HeaderBar :role="role" />

    <!-- Top KPIs -->
    <KpiCards :role="role" :user-id="userId" />

    <!-- Live Delivery Map -->
    <LiveMap :role="role" :user-id="userId" />

    <!-- Order and Status Table -->
    <OrderTable :role="role" :user-id="userId" />

    <!-- Workforce/Asset Management -->
    <WorkforceManagement v-if="role === 'manager'" />
    <AssetManagement v-else />

    <!-- Customer Feedback Overview -->
    <CustomerFeedback :role="role" :user-id="userId" />

    <!-- Delivery Trends and Insights (Manager only) -->
    <TrendsInsights v-if="role === 'manager'" />

    <!-- Digital Delivery Form (Partner only) -->
    <DigitalDeliveryForm v-if="role === 'partner'" :user-id="userId" />
  </div>
</template>

<script setup>
import { defineProps } from 'vue';
import HeaderBar from './HeaderBar.vue';
import KpiCards from './KpiCards.vue';
import LiveMap from './LiveMap.vue';
import OrderTable from './OrderTable.vue';
import WorkforceManagement from './WorkforceManagement.vue';
import AssetManagement from './AssetManagement.vue';
import CustomerFeedback from './CustomerFeedback.vue';
import TrendsInsights from './TrendsInsights.vue';
import DigitalDeliveryForm from './DigitalDeliveryForm.vue';

defineProps({
  role: String,
  userId: [String, Number],
});
</script>

<style scoped>
/* Add layout and spacing styles here */
</style> 