<script setup>
import { ref, onMounted, computed } from 'vue';
import NeuLoader2 from '../loaders/NeuLoader2.vue';
import NeuLoader7 from '../loaders/NeuLoader7.vue';
import NeuLoader4 from '../loaders/NeuLoader4.vue';
import NeuLoader9 from '../loaders/NeuLoader9.vue';
import NeuLoader10 from '../loaders/NeuLoader10.vue';
import Ishihara from '../tech/Ishihara.vue';
import Carousel1 from '../tech/HearingCarousel.vue';
import {  
        addClinicHearing,
        getMedicalHearing,
        getMedicalHeader
       } from "../../Fetchers.js";

const props = defineProps({
    medicicalItemsData: { 
    },
    userIdData: {
    },
    personIdData:{
    }
})
const medicalItems = computed(() => {
  return props.medicicalItemsData
});
const userId = computed(() => {
  return props.userIdData
});
const personId = computed(() => {
  return props.personIdData
});

const saving = ref(false)
onMounted(()=>{
    preLoading.value = true
    getMedicalHeader(personId.value, 2).then((results)=>{
        viewAllData.value = results
        preLoading.value = false
    })
})

const formType = ref(0)

const items = ref([
    {
        cleh_remarks:'',
        cleh_assessment:1,
        cleh_id: 1,
        cleh_ear: 'Left Ear',
        cleh_orientation: 'Front',
        cleh_user:userId.value,
        cleh_personid:personId.value,
    },
    {
        cleh_remarks:'',
        cleh_assessment:1,
        cleh_id: 1,
        cleh_ear: 'Left Ear',
        cleh_orientation: 'Back',
        cleh_user:userId.value,
        cleh_personid:personId.value,
    },
    {
        cleh_remarks:'',
        cleh_assessment:1,
        cleh_id: 1,
        cleh_ear: 'Right Ear',
        cleh_orientation: 'Front',
        cleh_user:userId.value,
        cleh_personid:personId.value,
    },
    {
        cleh_remarks:'',
        cleh_assessment:1,
        cleh_id: 1,
        cleh_ear: 'Right Ear',
        cleh_orientation: 'Back',
        cleh_user:userId.value,
        cleh_personid:personId.value,
    }
])



const activeFailedClass = ref('btn btn-sm btn-danger w-100');
const activePassedClass = ref('btn btn-sm btn-success w-100');
const defaultFailedClass = ref('btn btn-sm btn-secondary w-100');
const defaultPassedClass = ref('btn btn-sm btn-secondary w-100');

const preLoading = ref(false)
const viewAllData = ref([])
const viewSubData = ref([])
const subLoading = ref(false)
const showResult = ref(false)
const showTest = ref(false)
const showVirtualTest = ref(false)
const showManualTest = ref(false)


const save = () =>{
    saving.value = true;
    Swal.fire({
        title: "Saving Updates",
        text: "Please wait while we check all necessary details.",
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    addClinicHearing(items.value, 2, userId.value, personId.value).then((results)=>{
        if(results.status == 200){
        //    alert('Examination Successful')
        //    location.reload()
            Swal.fire({
                title: "Update Successful",
                text: "Changes applied, refreshing the page",
                icon: "success"
            }).then(()=>{
                Swal.close();
                location.reload()
            });
        }else{
        //    alert('Examination Failed')
        //    location.reload()
            Swal.fire({
                title: "Update Failed",
                text: "Unknown error occured, try again later",
                icon: "error"
            }).then(()=>{
                Swal.close();
                location.reload()
            });
        }
    })
}

const setAssessment = (index, assess, pass, fail) =>{
    if(pass == 1 && fail == 0){
        items.value.forEach((e) => {
            e.cleh_assessment = assess
        });
    }else if(pass == 0 && fail == 1){
        items.value.forEach((e) => {
            e.cleh_assessment = assess
        });
    }else{
        let x = items.value[index].cleh_assessment = assess
    }
}

const viewingType = ref(2)
const loadingHearing = ref(false)

const viewAssessment = (mode) => {
    viewingType.value = mode
    examView.value = 0
    loadingHearing.value = false
    showResult.value = false
}

const viewResult = (headerid) =>{
    showResult.value = !showResult.value
    viewSubData.value = []
    subLoading.value = true

    getMedicalHearing(personId.value, headerid).then((results)=>{
        viewSubData.value = results.map((e, index)=>{
            return{
                ...e,
                cleh_ear: items.value[index].cleh_ear,
                cleh_orientation: items.value[index].cleh_orientation,
            }
        })
        subLoading.value = false
    })
}


const examView = ref(0)
const loaderColor = ref(0)
const examinationMode = (mode) => {
    loaderColor.value = mode
    loadingHearing.value = true 
    setTimeout(() => {
        loadingHearing.value = false
        examView.value = mode
    }, 3000);
}

</script>

<template>
    <div class="d-flex flex-column p-3 gap-2 neu-card-inner h-100">
        <div class="d-flex flex-wrap flex-column">
            <p class="m-0 text-success fw-bold">Hearing Test</p>
            <p class="m-0 fst-italic p-2 small-font"><span class="fw-bold">Note:
                </span><span class="italic">Ensure that the details of the receiver and the item to be dispensed is
                    correct.
                </span></p>
        </div>

        <div class="d-flex flex-column gap-2 justify-content-center h-100 mt-5">


            <div v-if="preLoading" class="p-2 align-content-center">
                <NeuLoader2 />
            </div>

            <div v-if="!preLoading" class="p-2 d-flex justify-content-end align-item-start">
                <div class="p-3 w-50">
                    <div class="d-flex gap-2">
                        <button :disabled="saving ? true : false" type="button" @click="viewAssessment(1)"
                            class="neu-btn neu-orange p-2">
                            <font-awesome-icon icon="fa-solid fa-eye" /> Conduct Assessment
                        </button>
                        <button :disabled="saving ? true : false" type="button" @click="viewAssessment(2)"
                            class="neu-btn neu-purple p-2">
                            <font-awesome-icon icon="fa-solid fa-eye" /> View Assessment
                        </button>
                    </div>
                </div>
            </div>

            <div v-if="!preLoading && viewingType == 1" class="p-2 h-100 ">
                <div class="d-flex flex-column align-items-center justify-content-center text-slight neu-dark-rainbow neu-card-inner-dark" v-if="examView == 0">
                    <div class="position-relative h-50">
                        <NeuLoader9 :trigger="loadingHearing" :mode="loaderColor" :text="'HEARING CAPABILITY'"/>
                    </div>
                    <div v-if="loadingHearing" class="">
                        <div class="bg-dark bg-opacity-10 p-3 bg-opacity-50 w-100 text-white d-flex flex-column align-items-center justify-content-center gap-1" >
                            <p class="fw-bold m-0">Initalizing Hearing Test</p>
                            <small>Your hamster is checking the hearing before you begin.</small>
                        </div>
                    </div>
                    <div v-else>
                        <div class="bg-dark bg-opacity-10 p-3 bg-opacity-50 w-100 text-white">
                        
                            <p class="fw-bold m-0">Hearing Test</p>
                            <small>Testing for hearing ability and detection of hearing loss</small>
                            <div class="d-flex gap-2 justify-content-center mt-3">
                                <button class="btn btn-sm btn-outline-success p-2" disabled>Virtual
                                    Assessment</button>
                                <button class="btn btn-sm btn-outline-info p-2" @click="examinationMode(2)">Manual
                                    Assessment</button>
                            </div>
                        </div>
                    </div>
                    <!-- <div id="box"></div> -->
                    <!-- <button id="btn" @click="animateBox()">Click me</button> -->    
                </div>
                <div v-if="examView == 1 && !loadingHearing"
                    class="d-flex flex-column text-slight neu-dark-rainbow neu-card-inner-dark p-4 text-white">
                        <div class="w-100 p-2 d-flex gap-2 justify-content-end">
                            <button class="btn btn-sm btn-outline-danger p-2" @click="viewAssessment(1), examView = 0, showVirtualTest = false">Cancel</button>
                        </div>
                        <div class="bg-dark bg-opacity-75 h-100 p-3 d-flex flex-column justify-content-center align-items-center">
                           
                            <div v-if="!showVirtualTest" class="w-50">
                                <h5 class="text-white-glow text-monospace">
                                    <span class="m-3">V</span>
                                    <span class="m-3">I</span>
                                    <span class="m-3">R</span>
                                    <span class="m-3">T</span>
                                    <span class="m-3">U</span>
                                    <span class="m-3">A</span>
                                    <span class="m-3">L</span>
                                    &nbsp;
                                    <span class="m-3">T</span>
                                    <span class="m-3">E</span>
                                    <span class="m-3">S</span>
                                    <span class="m-3">T</span>
                                </h5>
                                <p class="fw-bold m-0">This is a virtual color vision test.</p>
                                <small class=" w-50">In this test, you will be shown a series of plates with numbers on them. You will be
                                    asked to identify the numbers on the plates. This test is designed to assess your color
                                    vision and determine if you have any color vision deficiencies.</small>
                                <h6 class="mt-5">Just tell me what you see</h6>
                                <button class="btn btn-sm btn-outline-primary p-2" @click="showVirtualTest = true">Start Test</button>
                            </div>

                            <div v-if="showVirtualTest">
                                <div class="w-100 p-2">
                                    <!-- virtual test component here -->
                                </div>
                            </div>
                        </div>
                </div>
                <div v-if="examView == 2 && !loadingHearing"
                    class="d-flex flex-column text-slight neu-dark-rainbow neu-card-inner-dark p-4 text-white">
                    <div class="w-100 p-2 d-flex gap-2 justify-content-end">
                            <button class="btn btn-sm btn-outline-danger p-2" @click="viewAssessment(1), examView = 0, showManualTest = false">Cancel</button>
                        </div>
                        <div class="bg-dark bg-opacity-75 h-100 p-3 d-flex flex-column justify-content-center align-items-center">
                            <div v-if="!showManualTest" class="w-50">
                                <h5 class="text-white-glow text-monospace">
                                    <span class="m-3">M</span>
                                    <span class="m-3">A</span>
                                    <span class="m-3">N</span>
                                    <span class="m-3">U</span>
                                    <span class="m-3">A</span>
                                    <span class="m-3">L</span>
                                    &nbsp;
                                    <span class="m-3">T</span>
                                    <span class="m-3">E</span>
                                    <span class="m-3">S</span>
                                    <span class="m-3">T</span>
                                </h5>
                                <p class="fw-bold m-0">This is a Manual color Hearing test.</p>
                                <small class=" w-50">The reuslt handler will be shown and do the output encdoing and save the results of the physical test.</small>
                                <h6></h6>
                                <button class="btn btn-sm btn-outline-primary p-2" @click="showManualTest = true">Show Data</button>
                            </div>

                            <div v-if="showManualTest" class="w-75 h-100 overflow-auto">
                                <div class="p-3 position-relative" style="width: 100%; min-width: 800px; overflow: hidden;">
                                        <Carousel1 :itemsData="items" :userIdData="userId" @saveData="save"/>
                                </div>
                            </div>
                        </div>
                </div>
            </div>

            <div v-if="!preLoading && viewingType == 2" class="p-4 h-100 ">
                <div v-if="!showResult">
                    <table class="neu-table">
                        <thead>
                            <tr>
                                <th style="color:#555555">Date</th>
                                <th style="color:#555555">Identification</th>
                                <th style="color:#555555">Personnel</th>
                                <th style="color:#555555" class="text-center">Commands</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(vad, index) in viewAllData">
                                <td class="align-middle">
                                    {{ vad.clhd_dateadded }}
                                </td>
                                <td class="align-middle">
                                    {{ vad.clhd_header }}
                                </td>
                                <td class="align-middle">
                                    {{ vad.emp_firstname }} {{ vad.emp_lastname }}
                                </td>
                                <td class="align-middle">
                                    <div class="d-flex gap-2 justify-content-center">
                                        <button :disabled="saving ? true : false" type="button"
                                            @click="viewResult(vad.clhd_header)" class="neu-btn-sm neu-white">
                                            <font-awesome-icon icon="fa-solid fa-eye"/> View Results
                                        </button>
                                    </div>
                                </td>

                            </tr>
                            <tr v-if="!preLoading && !Object.keys(viewAllData).length">
                                <td class="p-3 text-center" colspan="4">
                                    <NeuLoader4 />
                                    <p class="fw-bold m-0">Nothing here yet!</p>
                                    <p>The hamster took a break 💤 — try adding something new.</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div v-else>
                    <div class="w-100 d-flex justify-content-end">
                        <div class="w-25 d-flex justify-content-end ">
                            <button :disabled="saving || subLoading ? true : false" type="button" @click="viewResult(0)"
                                class="neu-btn neu-blue p-2 w-50">
                                Back to List
                            </button>
                        </div>
                    </div>
                    <div class="w-100 mt-3">
                        <table class="neu-table">
                            <thead>
                                <tr>
                                    <th style="color:#555555" class="w-25">Ear</th>
                                    <th style="color:#555555" class="w-25">Position</th>
                                    <th style="color:#555555" class="w-25">Assessment/ Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(itm, index) in viewSubData">
                                    <td class="align-middle">
                                        {{ itm.cleh_ear }}
                                    </td>
                                    <td class="align-middle">
                                        {{ itm.cleh_orientation }}
                                    </td>
                                    <td class="align-middle">
                                        <div class="d-flex gap-1 justify-content-evenly">
                                            <button type="button" @click="setAssessment(index, 2, 0, 0)"
                                                :class="itm.cleh_assessment == 2 ? activePassedClass : defaultPassedClass"
                                                disabled>Passed</button>
                                            <button type="button" @click="setAssessment(index, 1, 0, 0)"
                                                :class="itm.cleh_assessment == 1 ? activeFailedClass : defaultFailedClass"
                                                disabled>Failed</button>
                                        </div>
                                        <div class="mt-2">
                                            <label>Remarks</label>
                                            <textarea v-model="itm.cleh_remarks" disabled
                                                class="form-control form-control-sm">
                                                </textarea>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="!subLoading && !Object.keys(viewSubData).length">
                                    <td class="p-3 text-center" colspan="7">
                                        No Records Found
                                    </td>
                                </tr>
                                <tr v-if="subLoading && !Object.keys(viewSubData).length">
                                    <td class="p-3 text-center" colspan="7">
                                        <div class="m-3">
                                            <NeuLoader2 />
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>


            <div v-if="!preLoading && showTest">
                <div class="w-100 p-2">
                    
                </div>
            </div>
        </div>
    </div>


</template>
<style scoped>
#box {
  width: 100px;
  height: 100px;
  background: #4caf50;
  margin-top: 20px;
  transition: transform 0.4s ease, background 0.4s;
}

#box.animate {
  transform: scale(1.3) rotate(10deg);
  background: #ff5722;
}



</style>