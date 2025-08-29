<script setup>
import { ref, onMounted, computed } from 'vue';
import { getUserID } from "../../../routes/user";
import { getCurriculumSubject, 
    getEnrollment,
     getMilestone, 
    addMilestone, 
    updateEnrollment, 
    updateMilestone, 
    getCommandUpdateCurriculum, 
    getAcademicStatus, 
    getArchiveMerge, 
     } 
    from "../../Fetchers.js";
import {
    pdfGenerator
} from "../../Generators.js";
import Loader from '../loaders/Loader1.vue';

import { useRouter, useRoute } from 'vue-router'

const router = useRouter();
const route = useRoute();
const path = computed(() => route.path)

const props = defineProps({
    student: {
    },
    curriculum: {
    },
    section: {
    },
    subject: {
    },
    moduleType: {
    }
})


const emit = defineEmits(['close-modal'])
const close = (data) => {
    window.stop()
    emit('close-modal')
}

const studentData = computed(() => {
    return props.student
});
const moduleData = computed(() => {
    return props.moduleType
});

const userID = ref('')
const preloading = ref(true)
const milestoneLoading = ref(true)

const milestoneCompData = ref([])
const milestoneCompHeader = ref([])
const milestoneCompLoading = ref(true)
const studentID = ref('')
onMounted(async () => {
    window.stop()
    try {
        console.log(studentData.value)
        console.log(moduleData.value)

        if (moduleData.value == 2) {
            studentID.value = studentData.value.arc_personid
        } else {
            studentID.value = studentData.value.per_id
        }

        getArchiveMerge(studentID.value).then((results) => {
            // milestoneCompData.value = results
            milestoneCompLoading.value = false
            // group naten yung archive para makapag create ng headers to be looped
            milestoneCompHeader.value = results.filter((value, index, self) =>
                index === self.findIndex((t) => t.arc_id === value.arc_id)
            );
            milestoneCompData.value = Object.groupBy(results, item => item.arc_id);
            // console.log(milestoneCompData.value )
            // console.log(milestoneCompHeader.value )
            preloading.value = false

        })

        getUserID().then((results) => {
            userID.value = results.account.data.id
        })

    } catch (err) {
        // alert('error loading the list default components')
        Swal.fire({
            icon: "error",
            title: "Oops...",
            text: "Something went wrong!",
            footer: '<a href="#" disabled>Have you checked your internet connection?</a>'
        }).then(() => {
            // preLoading.value = false
        });
    }
})

const formType = ref(0)

const downloadPdf = (filetype) => {
    // var element = document.getElementById('printform');
    // html2pdf(element);
    let name = studentID.value + '_' + filetype
    pdfGenerator(name,'a4','landscape',0.1)
    Swal.fire({
        icon: "success",
        title: "Download Complete",
        text: "Check your file manager, refreshing the page",
    }).then(()=>{
        location.reload()
    });

}

</script>

<template>
    <div class="d-flex flex-wrap w-100">
        <div v-if="preloading" class="d-flex w-100 mt-4 justify-content-center align-content-center">
            <Loader />
        </div>
        <div v-else class=" w-100">

            <div v-if="Object.keys(milestoneCompHeader).length" class="d-flex flex-column">
                <div class="col-12 border-bottom">
                    <div class="p-3">
                        <span class="fw-bold border bg-primary text-white p-2 rounded-3">Diploma</span>
                    </div>
                     <div class="p-3 w-100 d-flex justify-content-center">
                        <select v-model="formType" class="form-select w-50">
                            <option value="0">-- Please Select Here --</option>
                            <option value="2">College</option>
                            <option value="1">Senior High School</option>
                        </select>
                    </div>
                </div>
                
                <div class="col-12 small-font bg-opaque text-center" id="printform">
                    <div v-if="formType == 1" style="height: 770px; width: 1105px; border:3px solid black; padding: 2px; font-family: Georgia, 'Times New Roman', Times, serif;">
                        <div style="height: 100%; width: 100%; border:2px solid black">
                            <div class="d-flex flex-column gap-1 justify-content-around">
                                <div class="p-2 d-flex gap-1 ">
                                     <div class="w-50">
                                        <img src="/img/deped_seal.png" height="120px" width="120px" alt="...">
                                    </div>
                                    <div class="w-100 d-flex flex-column justify-content-center blackletter-header-1" style="text-align: center; ;">
                                        <span class="fs-5">Republika ng Pilipinas</span>
                                        <span class="fs-6">Republic of the Philippines</span>
                                        <span class="fs-4">Kagawaran ng Edukasyon</span>
                                        <span class="fs-6">Department of Education</span>
                                    </div>
                                    <div class="w-50">
                                        <img src="/img/clcst_logo.png" height="120px" width="auto" alt="...">
                                    </div>
                                </div>

                                <div class="p-1 w-100 d-flex flex-column blackletter-header-3">
                                    <span class="blackletter-header-2 fw-bold">REHIYON III</span>
                                    <span class="blackletter-header-2 fst-italic" style="font-size:9px;">REGION III</span>
                                    <span class="blackletter-header-2 fw-bold ">SANGAY NG LUNGSOD NG SAN FERNANDO</span>
                                    <span class="blackletter-header-2 fst-italic" style="font-size:9px;">DIVISION OF CITY OF SAN FERNANDO PAMPANGA</span>
                                    <span class="blackletter-header-2 fw-bold fs-5 mt-2">CENTRAL LUZON COLLEGE OF SCIENCE & TECHNOLOGY, INC.</span>
                                    <span class="mt-2 fw-bold">Pinatutunayan nito na si</span>
                                    <span class="fst-italic">This certifies that</span>
                                    <span v-for="(h, index) in milestoneCompHeader" class="blackletter-header-2 fw-bold fs-3">
                                        <span v-if="index === Object.keys(milestoneCompHeader).length - 1">
                                            {{ h.arc_firstname }} 
                                            {{ h.arc_middlename ? h.arc_middlename: ' ' }}
                                            {{ h.arc_lastname }}
                                            {{ h.arc_suffixname ? h.arc_suffixname: ' ' }}
                                        </span>
                                    </span>
                                    <span class="fst-italic">Learner Reference Number (LRN): {{ milestoneCompHeader[0].arc_studentid }}</span>
                                    <span class="mt-2 fw-bold">
                                        ay kasiya-siyang nakatupad sa mga kinakailangan sa Pagtatapos ng Senior High School
                                    </span>
                                    <span class="fst-italic">
                                        has satisfactorily completed the requirements for graduation in Senior High School
                                    </span>

                                    <span v-for="(h, index) in milestoneCompHeader" class="mt-1 fw-bold fs-6">
                                        <span v-if="index === Object.keys(milestoneCompHeader).length - 1">
                                            {{ h.arc_coursename }}
                                        </span>
                                    </span>

                                    <span class="mt-2 fw-bold">na itinakda para sa Mataas na Paaralan ng Kagawaran ng Edukasyon, kaya pinagkalooban siya nitong</span>
                                    <span class="fst-italic">prescribed for Secondary Schools of the Department of Education and is therefore awarded this</span>

                                    <span class="mt-2 fw-bold fs-4">KATIBAYAN</span>
                                    <span class="fw-bold">DIPLOMA</span>

                                    <span class="mt-2 fw-bold">Nilagdaan sa Lungsod ng City of San Fernando, Pampanga, Pilipinas nitong ika-27 ng Hunyo, 2024.</span>
                                    <span class="fst-italic">Signed in City of San Fernando, Philippines on the 27th day of June 2024.</span>
                                    <span class="fw-bold">Special Order No. (A) (R-III) 1165, s.2024 issued on June 2024</span>

                                </div>
                                <div class="d-flex gap-1 justify-content-around  mt-5">
                                    <div class="d-flex flex-column position-relative">
                                        <img src="/img/sig1.png" height="100px" width="100px" style="position: absolute;top:-55px; left: 55px;"/>
                                        <span class="fw-bold">RENATO P. LEGASPI, Ph.D.</span>
                                        <span>Pangulo/Punong Tagapagpaganap</span>
                                        <span class="fst-italic">President / CEO</span>
                                    </div>
                                    <div class="d-flex flex-column position-relative">
                                        <img src="/img/sig1.png" height="100px" width="100px" style="position: absolute;top:-55px; left: 55px;"/>
                                        <span class="fw-bold">RENE PAULO M. LEGASPI, Ph.D.</span>
                                        <span>Punong-Guro</span>
                                        <span class="fst-italic">Principal</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div v-if="formType == 2" style="height: 770px; width: 1105px; border:3px solid black; padding: 2px; font-family: Georgia, 'Times New Roman', Times, serif;">
                        <div style="height: 100%; width: 100%; border:2px solid black">
                            <div class="d-flex flex-column gap-1 justify-content-around">
                                <div class="p-2 d-flex gap-1 ">
                                     <div class="w-50">
                                        <img src="/img/ched.png" height="120px" width="120px" alt="...">
                                    </div>
                                    <div class="w-100 d-flex flex-column justify-content-center blackletter-header-1" style="text-align: center; ;">
                                        <span class="fs-5">Republika ng Pilipinas</span>
                                        <span class="fs-6">Republic of the Philippines</span>
                                        <span class="fs-4">Komisyon sa Mas Mataas na Edukasyon</span>
                                        <span class="fs-6">Commision on Higher Education</span>
                                    </div>
                                    <div class="w-50">
                                        <img src="/img/clcst_logo.png" height="120px" width="auto" alt="...">
                                    </div>
                                </div>

                                <div class="p-1 w-100 d-flex flex-column blackletter-header-3">
                                    <span class="blackletter-header-2 fw-bold">REHIYON III</span>
                                    <span class="blackletter-header-2 fst-italic" style="font-size:9px;">REGION III</span>
                                    <span class="blackletter-header-2 fw-bold ">SANGAY NG LUNGSOD NG SAN FERNANDO</span>
                                    <span class="blackletter-header-2 fst-italic" style="font-size:9px;">DIVISION OF CITY OF SAN FERNANDO PAMPANGA</span>
                                    <span class="blackletter-header-2 fw-bold fs-5 mt-2">CENTRAL LUZON COLLEGE OF SCIENCE & TECHNOLOGY, INC.</span>
                                    <span class="mt-2 fw-bold">Pinatutunayan nito na si</span>
                                    <span class="fst-italic">This certifies that</span>
                                    <span v-for="(h, index) in milestoneCompHeader" class="blackletter-header-2 fw-bold fs-3">
                                        <span v-if="index === Object.keys(milestoneCompHeader).length - 1">
                                            {{ h.arc_firstname }} 
                                            {{ h.arc_middlename ? h.arc_middlename: ' ' }}
                                            {{ h.arc_lastname }}
                                            {{ h.arc_suffixname ? h.arc_suffixname: ' ' }}
                                        </span>
                                    </span>
                                    <span class="fst-italic">Learner Reference Number (LRN): {{ milestoneCompHeader[0].arc_studentid }}</span>
                                    <span class="mt-2 fw-bold">
                                        ay kasiya-siyang nakatupad sa mga kinakailangan sa Pagtatapos ng Senior High School
                                    </span>
                                    <span class="fst-italic">
                                        has satisfactorily completed the requirements for graduation in Senior High School
                                    </span>

                                    <span v-for="(h, index) in milestoneCompHeader" class="mt-1 fw-bold fs-6">
                                        <span v-if="index === Object.keys(milestoneCompHeader).length - 1">
                                            {{ h.arc_coursename }}
                                        </span>
                                    </span>

                                    <span class="mt-2 fw-bold">na itinakda para sa Mataas na Paaralan ng Komisyon sa Mas Mataas na Edukasyon, kaya pinagkalooban siya nitong</span>
                                    <span class="fst-italic">prescribed for Secondary Schools of the Commission on Higher Education and is therefore awarded this</span>

                                    <span class="mt-2 fw-bold fs-4">KATIBAYAN</span>
                                    <span class="fw-bold">DIPLOMA</span>

                                    <span class="mt-2 fw-bold">Nilagdaan sa Lungsod ng City of San Fernando, Pampanga, Pilipinas nitong ika-27 ng Hunyo, 2024.</span>
                                    <span class="fst-italic">Signed in City of San Fernando, Philippines on the 27th day of June 2024.</span>
                                    <span class="fw-bold">Special Order No. (A) (R-III) 1165, s.2024 issued on June 2024</span>

                                </div>
                                <div class="d-flex gap-1 justify-content-around  mt-5">
                                    <div class="d-flex flex-column position-relative">
                                        <img src="/img/sig1.png" height="100px" width="100px" style="position: absolute;top:-55px; left: 55px;"/>
                                        <span class="fw-bold">RENATO P. LEGASPI, Ph.D.</span>
                                        <span>Pangulo/Punong Tagapagpaganap</span>
                                        <span class="fst-italic">President / CEO</span>
                                    </div>
                                    <div class="d-flex flex-column position-relative">
                                        <img src="/img/sig1.png" height="100px" width="100px" style="position: absolute;top:-55px; left: 55px;"/>
                                        <span class="fw-bold">RENE PAULO M. LEGASPI, Ph.D.</span>
                                        <span>Punong-Guro</span>
                                        <span class="fst-italic">Principal</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-if="formType == 0" style="height: 770px; width: 1105px; border:3px solid black; padding: 2px; font-family: Georgia, 'Times New Roman', Times, serif;">
                        <div style="height: 100%; width: 100%; border:2px solid black" class="d-flex justify-content-center align-items-center">
                            <span class="fw-bold fs-6">Please Select Degree to Generate</span>
                        </div>
                    </div>
                    
                </div>
    
                <div class="border mt-2 p-2 rounded-2 d-flex gap-2 justify-content-end"v-if="formType != 0">
                    <button class="btn btn-sm btn-success" @click="downloadPdf()">Download Diploma</button>
                </div>

            </div>

            <div v-else class="">
                <span>No Diploma Found</span>
            </div>
        </div>
    </div>

</template>