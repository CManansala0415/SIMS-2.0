<script setup>
import { computed } from 'vue';

const props = defineProps({
  elementcount: Number
});

const elementNum = computed(() => props.elementcount);
</script>

<template>
  <div class="skeleton-box">
    <div class="skeleton shimmer" v-for="n in elementNum" :key="n"></div>
  </div>
</template>

<style scoped>
.skeleton-box {
  display: flex;
  gap: 15px;
  width: 100%;
}

/* Base skeleton */
.skeleton {
  height: 400px;
  width: 100%;
  border-radius: 14px;
  position: relative;
  overflow: hidden;
  background-color: #e5e7eb;
}

/* Shimmer effect */
.shimmer::after {
  content: "";
  position: absolute;
  top: 0;
  left: -150%;
  height: 100%;
  width: 150%;
  background: linear-gradient(
    90deg,
    transparent,
    #d8e0e7,
    transparent
  );
  animation: shimmer 1.2s infinite;
}

@keyframes shimmer {
  100% {
    left: 150%;
  }
}

/* Optional: variation per column for realism */
.skeleton-td:nth-child(1) .skeleton {
  width: 80%;
}
.skeleton-td:nth-child(2) .skeleton {
  width: 60%;
}
.skeleton-td:nth-child(3) .skeleton {
  width: 40%;
}
</style>