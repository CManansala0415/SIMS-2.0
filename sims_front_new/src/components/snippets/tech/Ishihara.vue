<script setup>
import { ref, onMounted } from 'vue';

const canvas = ref(null);
const answer = ref('');
const result = ref('');

const plate = {
  number: 74, // hidden number
  width: 300,
  height: 300,
  dotCount: 1500,
  numberDotCount: 400, // increase to fill number better
  colors: {
    background: ['#f4a261', '#e76f51', '#e9c46a'], // orange shades
    number: ['#2a9d8f', '#264653', '#88b04b']      // green/blue shades
  }
};

const drawPlate = () => {
  if (!canvas.value) return;
  const ctx = canvas.value.getContext('2d');
  ctx.clearRect(0, 0, plate.width, plate.height);

  // Draw background dots
  for (let i = 0; i < plate.dotCount; i++) {
    const x = Math.random() * plate.width;
    const y = Math.random() * plate.height;
    const radius = Math.random() * 12 + 4;
    ctx.beginPath();
    ctx.arc(x, y, radius, 0, Math.PI * 2);
    const bgColor = plate.colors.background[Math.floor(Math.random() * plate.colors.background.length)];
    ctx.fillStyle = bgColor;
    ctx.fill();
  }

  // Draw number dots using offscreen canvas
  const offCanvas = document.createElement('canvas');
  offCanvas.width = plate.width;
  offCanvas.height = plate.height;
  const offCtx = offCanvas.getContext('2d');

  offCtx.font = 'bold 150px Arial';
  offCtx.textAlign = 'center';
  offCtx.textBaseline = 'middle';
  offCtx.fillStyle = '#000';
  offCtx.fillText(plate.number, plate.width / 2, plate.height / 2);

  const imageData = offCtx.getImageData(0, 0, plate.width, plate.height);

  let placedDots = 0;
  while (placedDots < plate.numberDotCount) {
    const x = Math.floor(Math.random() * plate.width);
    const y = Math.floor(Math.random() * plate.height);

    // Only place dot if pixel is part of number
    const alpha = imageData.data[(y * plate.width + x) * 4 + 3];
    if (alpha > 0) {
      const radius = Math.random() * 8 + 3;
      ctx.beginPath();
      ctx.arc(x, y, radius, 0, Math.PI * 2);
      const numColor = plate.colors.number[Math.floor(Math.random() * plate.colors.number.length)];
      ctx.fillStyle = numColor;
      ctx.fill();
      placedDots++;
    }
  }
};

const checkAnswer = () => {
  if (parseInt(answer.value) === plate.number) {
    result.value = 'Correct! You can see the number.';
  } else {
    result.value = 'Incorrect. This simulates color blindness!';
  }
};

onMounted(() => {
  drawPlate();
});
</script>

<template>
  <div class="ishihara-test">
    <h5 class="text-monospace text-white-glow">Ishihara Test Simulation</h5>
    <p class="text-white-glow">Plate 1</p>
    <canvas ref="canvas" :width="plate.width" :height="plate.height"></canvas>
    <div class="controls">
      <input type="form-control form-control-sm" v-model="answer" placeholder="Answer" />
      <button class="btn btn-sm btn-outline-primary p-2" @click="checkAnswer">Confirm</button>
    </div>
    <!-- <div class="result">{{ result }}</div> -->
  </div>
</template>

<style scoped>
.ishihara-test {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 20px;
  
  /* background: #f0f0f0; */
}

canvas {
  border-radius: 50%;
  border: 2px solid #333;
  margin: 20px 0;
  filter: drop-shadow(0 0 18px rgba(255, 255, 255, 0.4));
}

.controls {
  display: flex;
  gap: 10px;
  margin-top: 10px;
}

input {
  padding: 0.5rem;
  font-size: 1rem;
  width: 100px;
  text-align: center;
}

button {
  padding: 0.5rem 1rem;
  font-size: 1rem;
  cursor: pointer;
}

.result {
  margin-top: 20px;
  font-weight: bold;
  font-size: 1.2rem;
  /* color: #333; */
}
</style>
