<script setup>
import { ref, onMounted, computed } from 'vue';
import NeuLoader2 from '../loaders/NeuLoader2.vue';
import NeuLoader7 from '../loaders/NeuLoader7.vue';
import NeuLoader4 from '../loaders/NeuLoader4.vue';
import NeuLoader9 from '../loaders/NeuLoader9.vue';
import NeuLoader10 from '../loaders/NeuLoader10.vue';
import Ishihara from '../tech/Ishihara.vue';
import Carousel1 from '../tech/IshiharaCarousel.vue';

import {
    addClinicIshihara,
    getMedicalIshihara,
    getMedicalHeader,
} from "../../Fetchers.js";

const props = defineProps({
    medicicalItemsData: {
    },
    userIdData: {
    },
    personIdData: {
    },
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
onMounted(() => {
    preLoading.value = true
    getMedicalHeader(personId.value, 1).then((results) => {
        viewAllData.value = results
        preLoading.value = false
    })
})

const formType = ref(0)

const items = ref([
    {
        cler_remarks: '',
        cler_assessment: 1,
        cler_id: 1,
        no_plate: 'Plate 1',
        normal: 'Visible to all',
        def_red_green: 'OK',
        cler_user: userId.value,
        cler_personid: personId.value,
    },
    {
        cler_remarks: '',
        cler_assessment: 1,
        cler_id: 2,
        no_plate: 'Plate 2',
        normal: 'View as 8',
        def_red_green: 'View as 3',
        cler_user: userId.value,
        cler_personid: personId.value,
    },
    {
        cler_remarks: '',
        cler_assessment: 1,
        cler_id: 3,
        no_plate: 'Plate 3',
        normal: 'View as 6',
        def_red_green: 'View as 5',
        cler_user: userId.value,
        cler_personid: personId.value,
    },
    {
        cler_remarks: '',
        cler_assessment: 1,
        cler_id: 4,
        no_plate: 'Plate 4',
        normal: 'View as 29',
        def_red_green: 'View as 70',
        cler_user: userId.value,
        cler_personid: personId.value,
    },
    {
        cler_remarks: '',
        cler_assessment: 1,
        cler_id: 5,
        no_plate: 'Plate 5',
        normal: 'View as 57',
        def_red_green: 'View as 35',
        cler_user: userId.value,
        cler_personid: personId.value,
    },
    {
        cler_remarks: '',
        cler_assessment: 1,
        cler_id: 6,
        no_plate: 'Plate 6',
        normal: 'View as 5',
        def_red_green: 'View as 2',
        cler_user: userId.value,
        cler_personid: personId.value,
    },
    {
        cler_remarks: '',
        cler_assessment: 1,
        cler_id: 7,
        no_plate: 'Plate 7',
        normal: 'View as 3',
        def_red_green: 'View as 5',
        cler_user: userId.value,
        cler_personid: personId.value,
    },
    {
        cler_remarks: '',
        cler_assessment: 1,
        cler_id: 8,
        no_plate: 'Plate 8',
        normal: 'View as 15',
        def_red_green: 'View as 17',
        cler_user: userId.value,
        cler_personid: personId.value,
    },
    {
        cler_remarks: '',
        cler_assessment: 1,
        cler_id: 9
        , no_plate: 'Plate 9',
        normal: 'View as 74',
        def_red_green: 'View as 21',
        cler_user: userId.value,
        cler_personid: personId.value,
    },
    {
        cler_remarks: '',
        cler_assessment: 1,
        cler_id: 10,
        no_plate: 'Plate 10',
        normal: 'View as 2',
        def_red_green: 'See Nothing/Wrong',
        cler_user: userId.value,
        cler_personid: personId.value,
    },
    {
        cler_remarks: '',
        cler_assessment: 1,
        cler_id: 11,
        no_plate: 'Plate 11',
        normal: 'View as 6',
        def_red_green: 'See Nothing/Wrong',
        cler_user: userId.value,
        cler_personid: personId.value,
    },
    {
        cler_remarks: '',
        cler_assessment: 1,
        cler_id: 12,
        no_plate: 'Plate 12',
        normal: 'View as 97',
        def_red_green: 'See Nothing/Wrong',
        cler_user: userId.value,
        cler_personid: personId.value,
    },
    {
        cler_remarks: '',
        cler_assessment: 1,
        cler_id: 13,
        no_plate: 'Plate 13',
        normal: 'View as 41',
        def_red_green: 'See Nothing/Wrong',
        cler_user: userId.value,
        cler_personid: personId.value,
    },
    {
        cler_remarks: '',
        cler_assessment: 1,
        cler_id: 14,
        no_plate: 'Plate 14',
        normal: 'View as 5',
        def_red_green: 'See Nothing/Wrong',
        cler_user: userId.value,
        cler_personid: personId.value,
    },
    {
        cler_remarks: '',
        cler_assessment: 1,
        cler_id: 15,
        no_plate: 'Plate 15',
        normal: 'View as 7',
        def_red_green: 'See Nothing/Wrong',
        cler_user: userId.value,
        cler_personid: personId.value,
    },
    {
        cler_remarks: '',
        cler_assessment: 1,
        cler_id: 16,
        no_plate: 'Plate 16',
        normal: 'View as 16',
        def_red_green: 'See Nothing/Wrong',
        cler_user: userId.value,
        cler_personid: personId.value,
    },
    {
        cler_remarks: '',
        cler_assessment: 1,
        cler_id: 17,
        no_plate: 'Plate 17',
        normal: 'View as 73',
        def_red_green: 'See Nothing/Wrong',
        cler_user: userId.value,
        cler_personid: personId.value,
    },
    {
        cler_remarks: '',
        cler_assessment: 1,
        cler_id: 18,
        no_plate: 'Plate 18',
        normal: 'View Nothing',
        def_red_green: 'View as 5',
        cler_user: userId.value,
        cler_personid: personId.value,
    },
    {
        cler_remarks: '',
        cler_assessment: 1,
        cler_id: 19,
        no_plate: 'Plate 19',
        normal: 'View Nothing',
        def_red_green: 'View as 2',
        cler_user: userId.value,
        cler_personid: personId.value,
    },
    {
        cler_remarks: '',
        cler_assessment: 1,
        cler_id: 20,
        no_plate: 'Plate 20',
        normal: 'View Nothing',
        def_red_green: 'View as 45',
        cler_user: userId.value,
        cler_personid: personId.value,
    },
    {
        cler_remarks: '',
        cler_assessment: 1,
        cler_id: 21,
        no_plate: 'Plate 21',
        normal: 'View Nothing',
        def_red_green: 'View as 73',
        cler_user: userId.value,
        cler_personid: personId.value,
    },
    {
        cler_remarks: '',
        cler_assessment: 1,
        cler_id: 22,
        no_plate: 'Plate 22',
        normal: 'View as 26',
        def_red_green: '(Protanopia or Protanomaly) 6 (Deuteranopia or Deuteranomaly) 2',
        cler_user: userId.value,
        cler_personid: personId.value,
    },
    {
        cler_remarks: '',
        cler_assessment: 1,
        cler_id: 23,
        no_plate: 'Plate 23',
        normal: 'View as 42',
        def_red_green: '(Protanopia or Protanomaly) 2 (Deuteranopia or Deuteranomaly) 4',
        cler_user: userId.value,
        cler_personid: personId.value,
    },
    {
        cler_remarks: '',
        cler_assessment: 1,
        cler_id: 24,
        no_plate: 'Plate 24',
        normal: 'View as 35',
        def_red_green: '(Protanopia or Protanomaly) 5 (Deuteranopia or Deuteranomaly) 3',
        cler_user: userId.value,
        cler_personid: personId.value,
    },
    {
        cler_remarks: '',
        cler_assessment: 1,
        cler_id: 25,
        no_plate: 'Plate 25',
        normal: 'View as 96',
        def_red_green: '(Protanopia or Protanomaly) 6 (Deuteranopia or Deuteranomaly) 9',
        cler_user: userId.value,
        cler_personid: personId.value,
    },
    // {
    //     cler_remarks:'',
    //     cler_assessment:1,
    //     cler_id: 26,
    //     no_plate: 'Plate 26',
    //     normal: 'View as Purple and Spots',
    //     def_red_green: '(Protanopia or Protanomaly) Purple (Deuteranopia or Deuteranomaly) Red Line',
    //     cler_user:userId.value,
    //     cler_personid:personId.value,
    // },
    // {
    //     cler_remarks:'',
    //     cler_assessment:1,
    //     cler_id: 27,
    //     no_plate: 'Plate 27',
    //     normal: 'View as Purple and Spots',
    //     def_red_green: '(Protanopia or Protanomaly) Purple Line (Deuteranopia or Deuteranomaly) Red Line',
    //     cler_user:userId.value,
    //     cler_personid:personId.value,
    // },
    // {
    //     cler_remarks:'',
    //     cler_assessment:1,
    //     cler_id: 28,
    //     no_plate: 'Plate 28',
    //     normal: 'View Nothing',
    //     def_red_green: 'View Only Line',
    //     cler_user:userId.value,
    //     cler_personid:personId.value,
    // },
    // {
    //     cler_remarks:'',
    //     cler_assessment:1,
    //     cler_id: 29,
    //     no_plate: 'Plate 29',
    //     normal: 'View Nothing',
    //     def_red_green: 'View Only Line',
    //     cler_user:userId.value,
    //     cler_personid:personId.value,
    // },
    // {
    //     cler_remarks:'',
    //     cler_assessment:1,
    //     cler_id: 30,
    //     no_plate: 'Plate 30',
    //     normal: 'View as Blue Green Line',
    //     def_red_green: 'View Nothing',
    //     cler_user:userId.value,
    //     cler_personid:personId.value,
    // },
    // {
    //     cler_remarks:'',
    //     cler_assessment:1,
    //     cler_id: 31,
    //     no_plate: 'Plate 31',
    //     normal: 'View as Blue Green Line',
    //     def_red_green: 'View Nothing',
    //     cler_user:userId.value,
    //     cler_personid:personId.value,
    // },
    // {
    //     cler_remarks:'',
    //     cler_assessment:1,
    //     cler_id: 32,
    //     no_plate: 'Plate 32',
    //     normal: 'View as Orange Line',
    //     def_red_green: 'Nothing or False Line',
    //     cler_user:userId.value,
    //     cler_personid:personId.value,
    // },
    // {
    //     cler_remarks:'',
    //     cler_assessment:1,
    //     cler_id: 33,
    //     no_plate: 'Plate 33',
    //     normal: 'View as Orange Line',
    //     def_red_green: 'Nothing or False Line',
    //     cler_user:userId.value,
    //     cler_personid:personId.value,
    // },
    // {
    //     cler_remarks:'',
    //     cler_assessment:1,
    //     cler_id: 34,
    //     no_plate: 'Plate 34',
    //     normal: 'Blue Green / Yellow Green',
    //     def_red_green: 'View as Red Green and Violet Line',
    //     cler_user:userId.value,
    //     cler_personid:personId.value,
    // },
    // {
    //     cler_remarks:'',
    //     cler_assessment:1,
    //     cler_id: 35,
    //     no_plate: 'Plate 35',
    //     normal: 'Green / Yellow Green Line',
    //     def_red_green: 'Only Green and Violet Line',
    //     cler_user:userId.value,
    //     cler_personid:personId.value,
    // },
    // {
    //     cler_remarks:'',
    //     cler_assessment:1,
    //     cler_id: 36,
    //     no_plate: 'Plate 36',
    //     normal: 'Violet / Orange Line',
    //     def_red_green: 'Green and Violet Line',
    //     cler_user:userId.value,
    //     cler_personid:personId.value,
    // },
    // {
    //     cler_remarks:'',
    //     cler_assessment:1,
    //     cler_id: 37,
    //     no_plate: 'Plate 37',
    //     normal: 'Violet / Orange Line',
    //     def_red_green: 'Blue Green and Violet Line',
    //     cler_user:userId.value,
    //     cler_personid:personId.value,
    // },
    // {
    //     cler_remarks:'',
    //     cler_assessment:1,
    //     cler_id: 38,
    //     no_plate: 'Plate 38',
    //     normal: 'Everyone should see the same line',
    //     def_red_green: 'Ok',
    // }
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

const save = (data) => {
    saving.value = true;
    Swal.fire({
        title: "Saving Updates",
        text: "Please wait while we check all necessary details.",
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });


    addClinicIshihara(data, 1, userId.value, personId.value).then((results) => {
        if (results.status == 200) {
            // alert('Examination Successful')
            // location.reload()
            Swal.fire({
                title: "Examination Successful",
                text: "Changes applied, refreshing the page",
                icon: "success"
            }).then(() => {
                Swal.close()
                location.reload()
            });
        } else {
            // alert('Examination Failed')
            // location.reload()
            Swal.fire({
                title: "Examination Failed",
                text: "Unknown error occured, try again later",
                icon: "error"
            }).then(() => {
                Swal.close()
                location.reload()
            });
        }
    })
}

const setAssessment = (index, assess, pass, fail) => {
    if (pass == 1 && fail == 0) {
        items.value.forEach((e) => {
            e.cler_assessment = assess
        });
    } else if (pass == 0 && fail == 1) {
        items.value.forEach((e) => {
            e.cler_assessment = assess
        });
    } else {
        let x = items.value[index].cler_assessment = assess
    }
}


const viewingType = ref(2)
const loadingIshihara = ref(false)

const viewAssessment = (mode) => {
    viewingType.value = mode
    examView.value = 0
    loadingIshihara.value = false
    showResult.value = false
}

const viewResult = (headerid) => {
    showResult.value = !showResult.value
    viewSubData.value = []
    subLoading.value = true

    getMedicalIshihara(personId.value, headerid).then((results) => {
        viewSubData.value = results.map((e, index) => {
            return {
                ...e,
                no_plate: items.value[index].no_plate,
                normal: items.value[index].normal,
                def_red_green: items.value[index].def_red_green,
            }
        })
        subLoading.value = false
    })
}

const examView = ref(0)
const loaderColor = ref(0)
const examinationMode = (mode) => {
    loaderColor.value = mode
    loadingIshihara.value = true 
    setTimeout(() => {
        loadingIshihara.value = false
        examView.value = mode
    }, 3000);
}

const animateBox = () => {
  document.getElementById('box').classList.toggle('animate');
}

</script>

<template>
    <div class="d-flex flex-column p-3 gap-2 neu-card-inner h-100">
        <div class="d-flex flex-wrap flex-column">
            <p class="m-0 text-success fw-bold">Color Vision Test</p>
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
                        <NeuLoader9 :trigger="loadingIshihara" :mode="loaderColor" :text="'ISHIHARA'"/>
                    </div>
                    <div v-if="loadingIshihara" class="">
                        <div class="bg-dark bg-opacity-10 p-3 bg-opacity-50 w-100 text-white d-flex flex-column align-items-center justify-content-center gap-1" >
                            <p class="fw-bold m-0">Initalizing Color Vision Test</p>
                            <small>Your hamster is checking the colors before you begin.</small>
                        </div>
                    </div>
                    <div v-else>
                        <div class="bg-dark bg-opacity-10 p-3 bg-opacity-50 w-100 text-white">
                        
                            <p class="fw-bold m-0">Color Vision Test</p>
                            <small>Visual color testing for color blindness detection</small>
                            <div class="d-flex gap-2 justify-content-center mt-3">
                                <button class="btn btn-sm btn-outline-success p-2" @click="examinationMode(1)">Virtual
                                    Assessment</button>
                                <button class="btn btn-sm btn-outline-info p-2" @click="examinationMode(2)">Manual
                                    Assessment</button>
                            </div>
                        </div>
                    </div>
                    <!-- <div id="box"></div> -->
                    <!-- <button id="btn" @click="animateBox()">Click me</button> -->    
                </div>
                <div v-if="examView == 1 && !loadingIshihara"
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
                                    <Ishihara/>
                                </div>
                            </div>
                        </div>
                </div>
                <div v-if="examView == 2 && !loadingIshihara"
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
                                <p class="fw-bold m-0">This is a Manual color vision test.</p>
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
                                    <th style="color:#555555" class="w-25">No. of
                                        Plates</th>
                                    <th style="color:#555555" class="w-25">Normal</th>
                                    <th style="color:#555555" class="w-25">Defective
                                        (Red & Green)</th>
                                    <th style="color:#555555" class="w-25">Assessment
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(itm, index) in viewSubData">
                                    <td class="align-middle">
                                        {{ itm.no_plate }}
                                    </td>
                                    <td class="align-middle">
                                        {{ itm.normal }}
                                    </td>
                                    <td class="align-middle">
                                        {{ itm.def_red_green }}
                                    </td>
                                    <td class="align-middle">
                                        <div class="d-flex gap-1 justify-content-evenly">
                                            <button type="button" @click="setAssessment(index, 2, 0, 0)"
                                                :class="itm.cler_assessment == 2 ? activePassedClass : defaultPassedClass"
                                                disabled>Passed</button>
                                            <button type="button" @click="setAssessment(index, 1, 0, 0)"
                                                :class="itm.cler_assessment == 1 ? activeFailedClass : defaultFailedClass"
                                                disabled>Failed</button>
                                        </div>
                                        <div class="mt-2">
                                            <label>Remarks</label>
                                            <textarea v-model="itm.cler_remarks" disabled
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

        <!-- <div class="d-flex flex-column gap-2 justify-content-center neu-bg-dark neu-card-inner-dark p-4">
            <div class="w-100 d-flex justify-content-end">
                <button :disabled="saving ? true : false" type="button" @click="viewAll = true"
                    class="btn btn-sm btn-primary">
                    Back to List
                </button>
            </div>
            <form @submit.prevent="save" class="w-100 card">
                <table class="neu-table-flat">
                    <thead>
                        <tr>
                            <th class="text-white  w-25">No. of Plates</th>
                            <th class="text-white  w-25">Normal</th>
                            <th class="text-white  w-25">Defective (Red & Green)</th>
                            <th class="text-white  w-25">Assessment</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(itm, index) in items">
                            <td class="align-middle">
                                {{ itm.no_plate }}
                            </td>
                            <td class="align-middle">
                                {{ itm.normal }}
                            </td>
                            <td class="align-middle">
                                {{ itm.def_red_green }}
                            </td>
                            <td class="align-middle">
                                <div class="d-flex gap-1 justify-content-evenly">
                                    <button type="button" @click="setAssessment(index, 2, 0, 0)"
                                        :class="itm.cler_assessment == 2 ? activePassedClass : defaultPassedClass"
                                        :disabled="saving ? true : false">Passed</button>
                                    <button type="button" @click="setAssessment(index, 1, 0, 0)"
                                        :class="itm.cler_assessment == 1 ? activeFailedClass : defaultFailedClass"
                                        :disabled="saving ? true : false">Failed</button>
                                </div>
                                <div class="mt-2">
                                    <label>Remarks</label>
                                    <textarea v-model="itm.cler_remarks" class="form-control form-control-sm">
                                </textarea>
                                </div>
                            </td>
                            <td class="align-middle">

                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="card bg-secondary-subtle p-2">
                    <div class="d-flex gap-2 justify-content-end p-2">
                        <button :disabled="saving ? true : false" type="button" @click="viewAssessment()"
                            class="btn btn-sm btn-dark w-100">
                            View All
                        </button>
                        <button :disabled="saving ? true : false" type="button" @click="setAssessment(index, 2, 1, 0)"
                            class="btn btn-sm btn-dark w-100">
                            Pass All
                        </button>
                        <button :disabled="saving ? true : false" type="button" @click="setAssessment(index, 1, 0, 1)"
                            class="btn btn-sm btn-dark w-100">
                            Fail All
                        </button>
                        <button :disabled="saving ? true : false" type="submit" class="btn btn-sm btn-dark w-100">
                            Save All
                        </button>
                    </div>
                </div>
            </form>
        </div> -->

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