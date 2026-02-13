<script setup>
import { ref, watch, nextTick, computed } from 'vue'
import NeuLoader10 from './NeuLoader10.vue'

/* =====================
   Props
===================== */
const props = defineProps({
  trigger: {
    type: Boolean,
    required: true
  },
  mode: {
    type: Number,
    default: 1
  },
  text: {
    type: String,
    default: 'Loading...'
  }
})

/* =====================
   State
===================== */
const boxRef = ref(null)
const playing = ref(false)

/* =====================
   Computed
===================== */
const boxClass = computed(() => {
  if (!props.trigger) return ''
  return props.mode === 2 ? 'animate-2' : 'animate-1'
})

/* =====================
   Watchers
===================== */
watch(
  () => props.trigger,
  async (active) => {
    playing.value = active
    if (!active) return

    await nextTick()

    // retrigger animation safely
    if (boxRef.value) {
      boxRef.value.classList.remove('animate-1', 'animate-2')
      void boxRef.value.offsetWidth
      boxRef.value.classList.add(boxClass.value)
    }
  }
)
</script>

<template>
  <div class="loader-wrapper">
    
    <!-- Animated Box -->
    <div
      ref="boxRef"
      class="box mb-5"
      :class="boxClass"
    ></div>

    <!-- Loader / Text -->
    <div class="text-wrapper">
      <NeuLoader10 v-if="playing" />

      <div v-else class="loading-icon">
        <span
          v-for="(char, i) in text"
          :key="i"
        >
          {{ char }}
        </span>
      </div>
    </div>

  </div>
</template>

<style scoped>
/* =====================
   Layout
===================== */
.loader-wrapper {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
}

/* =====================
   Box Animation
===================== */
.box {
  width: 100px;
  height: 100px;
  margin-top: 20px;
  border-radius: 10%;
  background: #fff;
  transition: transform 0.4s ease, background 0.4s, box-shadow 0.4s ease;
  box-shadow:
    0 0 10px rgba(255, 255, 255, 0.6),
    0 0 25px rgba(255, 255, 255, 0.4),
    0 0 50px rgba(255, 255, 255, 0.25);
}

.animate-1 {
  transform: scale(1.3) rotate(-45deg);
  background: #00ff62;
  box-shadow:
    0 0 10px rgba(0, 255, 98, 0.6),
    0 0 25px rgba(0, 255, 98, 0.4),
    0 0 50px rgba(0, 255, 98, 0.25);
}

.animate-2 {
  transform: scale(1.3) rotate(45deg);
  background: #ff0062;
  box-shadow:
    0 0 10px rgba(255, 0, 98, 0.6),
    0 0 25px rgba(255, 0, 98, 0.4),
    0 0 50px rgba(255, 0, 98, 0.25);
}

/* =====================
   Loading Text
===================== */
.loading-icon {
  margin-top: 30px;
  display: flex;
  gap: 12px;
  font-family: monospace;
  font-size: 18px;
  font-weight: 600;
  color: #f5f5f5;
  filter: drop-shadow(0 0 10px rgba(255,255,255,0.4));
}
</style>
