
<script setup>
import { ref, onMounted, computed } from 'vue';
import Loader from '../loaders/Loader1.vue';
import {
    getQRData
} from "../../Fetchers.js";
const refid = ref('')
const preLoading = ref(false)
const emit = defineEmits(['fetchData'])

const props = defineProps({
    modeData: {
    },    
})

const mode = computed(() => {
  return props.modeData
});

// onMounted(async () => {
//     refid.value = ''
//     preLoading.value=false
// })

const search = () =>{
    preLoading.value=true
    let x = refid.value? refid.value:0
    getQRData(x, mode.value).then((result)=>{
        emit('fetchData', result)
        refid.value = ''
        preLoading.value=false
    })
}
</script>


<template>
    <Loader v-if="preLoading"/>
    <input v-else type="text" class="form-control" v-model="refid" @keyup.enter="search()"/>
</template>