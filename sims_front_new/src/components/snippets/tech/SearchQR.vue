
<script setup>
import { ref, onMounted, computed } from 'vue';
import Loader from '../loaders/Loader1.vue';
import {
    getQRData
} from "../../Fetchers.js";
const refid = ref('')
const preLoading = ref(false)
const inactive = ref(true)
const active = ref(false)
const progress = ref(false)
const disabler = ref(false)
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
const scan = () =>{
    console.log(disabler.value)
    setTimeout(disabler.value=true, 200);
    search()

}

const search = () =>{
    preLoading.value=true
    disabler.value=true
    active.value = inactive.value = false
    progress.value = true

    let x = refid.value? refid.value:0
    getQRData(x, mode.value).then((result)=>{
        emit('fetchData', result)
        refid.value = ''
        preLoading.value=false
        active.value = progress.value = false
        inactive.value = true
    })
}

const focusInput = () =>{
    active.value = true
    inactive.value = progress.value = false
    document.getElementById('qrInput').focus()
}
</script>


<template>
    <div id="scanner">
        <div class="mb-3 small-font d-flex flex-column gap-2" tabindex="-1">
            <span><span class="text-success fw-bold">Note: </span>&nbsp;Make sure that the scanner is plugged in</span>
            <div v-if="inactive" class="alert alert-danger" role="alert">
                Please click enable to start scanning
            </div>
            <div v-if="active" class="alert alert-info" role="alert">
                Scanner is now running...
            </div>
            <div v-if="progress" class="alert alert-success" role="alert">
                <!-- <Loader v-if="preLoading"/> -->
                Information detected, redirecting please wait...
            </div>
            <button class="btn btn-sm btn-primary" @click=" () => $refs.focusTrap.activate(), focusInput()" tabindex="-1">Enable Scanning</button>
        </div>
        <focus-trap :active="false" ref="focusTrap">
            <input id="qrInput" type="text" class="form-control" v-model="refid" @keyup.enter="scan()" :disabled="disabler" tabindex="-1" style="opacity:0; height: 0; width: 0; padding: 0;"/>
        </focus-trap>
        <!-- style="opacity:0; height: 0; width: 0; padding: 0;"  -->
    </div>

</template>