<script setup>
import { ref, onMounted, computed } from 'vue';
const props = defineProps({
    color: {
    },
})
</script>
<template>
<!-- From Uiverse.io by gagan-gv --> 
<div class="spinner-10">
  <span class="m-3" >L</span>
  <span class="m-3" >O</span>
  <span class="m-3" >A</span>
  <span class="m-3" >D</span>
  <span class="m-3" >I</span>
  <span class="m-3" >N</span>
  <span class="m-3" >G</span>
</div>
</template>
<style scoped>
/* From Uiverse.io by gagan-gv */ 
.spinner-10 {
 /* height: 50px; */
 width: max-content;
 font-size: 18px;
 font-weight: 600;
 font-family: monospace;
 /* letter-spacing: 1em; */
 color: #f5f5f5;
 filter: drop-shadow(0 0 10px);
 display: flex;
 justify-content: center;
 align-items: center;
 text-align: center;
 /* border: 1px solid blue; */
}

.spinner-10 span {
 animation: loading6454 1.75s ease infinite;
}

.spinner-10 span:nth-child(2) {
 animation-delay: 0.25s;
}

.spinner-10 span:nth-child(3) {
 animation-delay: 0.5s;
}

.spinner-10 span:nth-child(4) {
 animation-delay: 0.75s;
}

.spinner-10 span:nth-child(5) {
 animation-delay: 1s;
}

.spinner-10 span:nth-child(6) {
 animation-delay: 1.25s;
}

.spinner-10 span:nth-child(7) {
 animation-delay: 1.5s;
}

@keyframes loading6454 {
 0%, 100% {
  transform: translateY(0);
 }

 50% {
  transform: translateY(-10px);
 }
}
</style>