<script setup>
import { ref, onMounted, computed } from 'vue';

onMounted(()=>{
    
})

const formType = ref(0)
</script>

<template>
    <div class="h-screen w-full bg-black absolute top-0 bg-opacity-60 flex flex-col justify-center items-center" tabindex="-1" aria-hidden="true" role="dialog" >
        <div class="p-7 bg-white rounded-md flex flex-col w-1/4">  <!--style="height:40rem;"-->
            <div class="border-0 border-b-2 border-gray-300 p-1 mb-4 flex justify-between items-center">
                    <p class="mb-2 text-md font-semibold">Help</p>   
                    <button type="button" @click="$emit('close-modal')" class="mb-2 bg-red-500 hover:bg-red-400 px-3 rounded-md font-semibold text-white">&times;</button>  
            </div>
            <div class="flex flex-col gap-2 overflow-auto p-4">
                
            </div>
        </div>
    </div>
</template>