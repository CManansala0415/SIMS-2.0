<script setup>
import { ref, onMounted, computed } from 'vue';
import { getUserID } from "../../../routes/user";
import {
    getCurriculumSubject,
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

const pagesContent = ref([])
const milestoneCompData = ref([])
const milestoneCompHeader = ref([])
const milestoneCompLoading = ref(true)
const studentID = ref('')
const profileId = ref('')


onMounted(async () => {
    window.stop()
    try {
        // console.log(studentData.value)
        // console.log(moduleData.value)

        if (moduleData.value == 2) {
            studentID.value = studentData.value.arc_personid
        } else {
            studentID.value = studentData.value.per_id
        }
        console.log(studentData.value)
        getArchiveMerge(studentID.value).then((results) => {
            // milestoneCompData.value = results

            milestoneCompLoading.value = false
            // group naten yung archive para makapag create ng headers to be looped
            milestoneCompHeader.value = results.filter((value, index, self) =>
                index === self.findIndex((t) => t.arc_id === value.arc_id)
            );

            console.log(results)

            let x = results.map((e) => {
                let rgrade = (
                    parseFloat(e.arc_prelimgrade) +
                    parseFloat(e.arc_midtermgrade) +
                    parseFloat(e.arc_prefinalgrade) +
                    parseFloat(e.arc_finalgrade)
                ) / 4
                let cgrade = 0
                switch (true) {
                    case rgrade >= 97.50:
                        cgrade = 1.0
                        break;
                    case rgrade >= 92.50 && rgrade <= 97.49:
                        cgrade = 1.25
                        break;
                    case rgrade >= 87.50 && rgrade <= 92.49:
                        cgrade = 1.5
                        break;
                    case rgrade >= 82.50 && rgrade <= 87.49:
                        cgrade = 1.75
                        break;
                    case rgrade >= 77.50 && rgrade <= 82.49:
                        cgrade = 2.0
                        break;
                    case rgrade >= 72.50 && rgrade <= 77.49:
                        cgrade = 2.25
                        break;
                    case rgrade >= 65.00 && rgrade <= 72.49:
                        cgrade = 2.5
                        break;
                    case rgrade >= 57.50 && rgrade <= 64.99:
                        cgrade = 2.75
                        break;
                    case rgrade >= 50.50 && rgrade <= 57.49:
                        cgrade = 3.0
                        break;
                    case rgrade >= 46.00 && rgrade <= 49.99:
                        cgrade = 4.0
                        break;
                    case rgrade <= 45.99:
                        cgrade = 5.0
                        break;
                    default:
                        cgrade = 0
                        break;
                }

                return {
                    raw_grade: rgrade ? rgrade : '0',
                    conv_grade: cgrade,
                    ...e
                }
            })

            milestoneCompData.value = Object.groupBy(x, item => item.arc_id);
            // console.log(milestoneCompData.value )
            // console.log(milestoneCompHeader.value )

            profileId.value = milestoneCompHeader.value[0].arc_profile ? 'http://localhost:8000/api/get-person-image/' + milestoneCompHeader.value[0].arc_profile + '/2' : '/img/profile_default.png'
            
            for (let i = 0; i < milestoneCompHeader.value.length; i += 5) {
                let group = {
                    items: milestoneCompHeader.value.slice(i, i + 5)
                };
                pagesContent.value.push(group);
            }

            preloading.value = false
            // console.log(milestoneCompHeader.value)

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
 

const downloadPdf = (filetype) => {
    // var element = document.getElementById('printform');
    // html2pdf(element);
    let name = studentID.value + '_' + filetype
    pdfGenerator(name, 'legal', 'portrait', 0.1)
    Swal.fire({
        icon: "success",
        title: "Download Complete",
        text: "Check your file manager, refreshing the page",
    }).then(() => {
        location.reload()
    });

}


</script>
 
<template>
    <div class="w-100">
        <div v-if="preloading" class="d-flex w-100 mt-4 justify-content-center align-content-center">
            <Loader />
        </div>
        <div v-else class="w-100 p-3  d-flex flex-column justify-content-center align-items-center">
            <div v-if="Object.keys(milestoneCompHeader).length">
                <div class="mb-3">
                    <div class="p-3">
                        <span class="fw-bold bg-primary text-white px-3 py-1 rounded-3">Form 137</span>
                    </div>
                </div>
                <div id="printform" class="h-auto d-flex flex-column justify-content-center align-items-start">

                    <!--Page 1-->
                    <div v-for="(page, pageIndex) in pagesContent">    
                        <div class="small-font"
                            style="width: 795px;  height:1215px; border:2px solid black; font-size: 10px; font-family: 'Times New Roman', Times, serif;">
                            <!-- height:1315px; width: 745px; height:1215px; -->
                            <div class="h-100 backdrop d-flex flex-column justify-content-between">
                                <div class="container w-100">
                                    <div class="d-flex flex-column gap-1">
                                        <div class="border p-1">
                                            <div class="d-flex">
                                                <div class="p-1 w-50">
                                                    <img src="/img/deped_seal.png" height="75px" width="75px" alt="...">
                                                </div>
                                                <div class="p-1 w-100 d-flex flex-column blackletter-header-1" style="text-align: center;">
                                                    <span>Republic of the Philippines</span>
                                                    <span>Department of Education</span>
                                                    <span class="fw-bold mt-3 blackletter-header-2" style="font-size: 12px;">Senior High School Student Permanent Record</span>
                                                </div>
                                                <div class="p-1 w-50">
                                                    <img src="/img/deped_logo.png" height="75px" width="auto" alt="...">
                                                </div>
                                            </div>
                                            <div class="d-flex">
                                                <div class="p-0 border w-100 d-flex flex-column bg-secondary-subtle">
                                                    <span class="fw-bold">Learner's Information</span>
                                                </div>
                                            </div>
                                            <div class="d-flex">
                                                <div class="p-0 w-100 d-flex flex-column text-start">
                                                    <span>Last Name</span>
                                                    <input type="text" class="border-0 border-bottom fw-bold fst-italic" :value="milestoneCompHeader[0].arc_lastname"/>
                                                </div>
                                                <div class="p-0 w-100 d-flex flex-column text-start">
                                                    <span>First Name</span>
                                                    <input type="text" class="border-0 border-bottom fw-bold fst-italic" :value="milestoneCompHeader[0].arc_firstname"/>
                                                </div>
                                                <div class="p-0 w-100 d-flex flex-column text-start">
                                                    <span>Middle Name</span>
                                                    <input type="text" class="border-0 border-bottom fw-bold fst-italic" :value="milestoneCompHeader[0].arc_middlename"/>
                                                </div>
                                                <div class="p-0 w-25 d-flex flex-column text-start">
                                                    <span>Suffix Name</span>
                                                    <input type="text" class="border-0 border-bottom fw-bold fst-italic" :value="milestoneCompHeader[0].arc_suffixname"/>
                                                </div>
                                            </div>
                                            <div class="d-flex">
                                                <div class="p-0 w-100 d-flex flex-column text-start">
                                                    <span>LRN</span>
                                                    <input type="text" class="border-0 border-bottom fw-bold fst-italic" value=""/>
                                                </div>
                                                <div class="p-0 w-100 d-flex flex-column text-start">
                                                    <span>Date of Birth (MM/DD/YYYY)</span>
                                                    <input type="text" class="border-0 border-bottom fw-bold fst-italic" :value="milestoneCompHeader[0].arc_birthday"/>
                                                </div>
                                                <div class="p-0 w-100 d-flex flex-column text-start">
                                                    <span>Sex</span>
                                                    <input type="text" class="border-0 border-bottom fw-bold fst-italic" :value="milestoneCompHeader[0].arc_gender == 1? 'Male':'Female'"/>
                                                </div>
                                                <div class="p-0 w-100 d-flex flex-column text-start">
                                                    <span>Date of SHS Admission (MM/DD/YYYY)</span>
                                                    <input type="text" class="border-0 border-bottom fw-bold fst-italic" :value="milestoneCompHeader[0].arc_dateenrolled"/>
                                                </div>
                                            </div>
                                            <div class="d-flex">
                                                <div class="p-0 border w-100 d-flex flex-column bg-secondary-subtle">
                                                    <span class="fw-bold">Elegibility for SHS Enrolment</span>
                                                </div>
                                            </div>
                                            <div class="d-flex">
                                                <div class="p-0 w-75 d-flex gap-1 justify-content-start align-items-center">
                                                    <input type="checkbox"/>
                                                    <span>High School Completer*</span>
                                                </div>
                                                <div class="p-0 w-100 d-flex gap-1 justify-content-start align-items-center">
                                                    <span>General Average</span>
                                                    <input type="text" class="border-0 border-bottom fw-bold fst-italic"/>
                                                </div>
                                                <div class="p-0 w-75 d-flex gap-1 justify-content-start align-items-center">
                                                    <input type="checkbox"/>
                                                    <span>Junior High School Completer</span>
                                                </div>
                                                <div class="p-0 w-100 d-flex gap-1 justify-content-start align-items-center">
                                                    <span>General Average</span>
                                                    <input type="text" class="border-0 border-bottom fw-bold fst-italic"/>
                                                </div>
                                            </div>
                                            <div class="d-flex">
                                                <div class="p-0 w-100 d-flex flex-column text-start">
                                                    <span>Date of School Graduation (MM/DD/YYYY)</span>
                                                    <input type="text" class="border-0 border-bottom fw-bold fst-italic"/>
                                                </div>
                                                <div class="p-0 w-100 d-flex flex-column text-start">
                                                    <span>Name of School</span>
                                                    <input type="text" class="border-0 border-bottom fw-bold fst-italic"/>
                                                </div>
                                                <div class="p-0 w-100 d-flex flex-column text-start">
                                                    <span>School Address</span>
                                                    <input type="text" class="border-0 border-bottom fw-bold fst-italic"/>
                                                </div>
                                            </div>
                                            <div class="d-flex">
                                                <div class="p-0 w-50 d-flex gap-1 justify-content-start align-items-center">
                                                    <input type="checkbox"/>
                                                    <span>PEPT Passer**</span>
                                                </div>
                                                <div class="p-0 w-25 d-flex gap-1 justify-content-start align-items-center">
                                                    <span>Rating</span>
                                                    <input type="text" class="border-0 border-bottom fw-bold fst-italic"/>
                                                </div>
                                                <div class="p-0 w-50 d-flex gap-1 justify-content-start align-items-center">
                                                    <input type="checkbox"/>
                                                    <span>ALS A&E Passer***</span>
                                                </div>
                                                <div class="p-0 w-25 d-flex gap-1 justify-content-start align-items-center">
                                                    <span>Rating</span>
                                                    <input type="text" class="border-0 border-bottom fw-bold fst-italic"/>
                                                </div>
                                                <div class="p-0 w-100 d-flex gap-1 justify-content-start align-items-center">
                                                    <input type="checkbox"/>
                                                    <span>Others (Please Specify)</span>
                                                    <input type="text" class="border-0 border-bottom fw-bold fst-italic"/>
                                                </div>
                                            </div>
                                            <div class="d-flex">
                                                <div class="p-0 w-100 d-flex flex-column text-start">
                                                    <span>Date of Examination/Assessment (MM/DD/YYYY)</span>
                                                    <input type="text" class="border-0 border-bottom fw-bold fst-italic"/>
                                                </div>
                                                <div class="p-0 w-100 d-flex flex-column text-start">
                                                    <span>Name and Address of Community Learning Center</span>
                                                    <input type="text" class="border-0 border-bottom fw-bold fst-italic"/>
                                                </div>
                                            </div>
                                            <div class="d-flex" style="font-size: 8.8px;">
                                                <div class="p-0 w-100 d-flex flex-column text-start">
                                                    <span class="fst-italic">*High School Completers are students who graduated from secondary school under old curriculum</span>
                                                    <span class="fst-italic">*PEPT - Philippine Educational Placement Test for JHS</span>
                                                </div>
                                                <div class="p-0 w-100 d-flex flex-column text-start">
                                                    <span class="fst-italic">***ALS and A&E - Alternative learning System Accreditation and Equivalency test for JHS</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="border p-1">
                                            <div class="d-flex">
                                                <div class="p-0 border w-100 d-flex flex-column bg-secondary-subtle">
                                                    <span class="fw-bold">Scholastic Records</span>
                                                </div>
                                            </div>
                                            <div class="d-flex">
                                                <div class="p-0 w-100 d-flex flex-column text-start">
                                                    <span>School Name</span>
                                                    <input type="text" class="border-0 border-bottom fw-bold w-100 fst-italic" value="Central Luzon College of Science and Technology"/>
                                                </div>
                                                <div class="p-0 w-100 d-flex flex-column text-start">
                                                    <span>Track/Strand</span>
                                                    <input type="text" class="border-0 border-bottom fw-bold w-100 fst-italic" :value="milestoneCompHeader[0].arc_coursename"/>
                                                </div>
                                                <div class="p-0 w-50 d-flex flex-column text-start">
                                                    <span>School ID</span>
                                                    <input type="text" class="border-0 border-bottom fw-bold w-100 fst-italic" value="401951"/>
                                                </div>
                                            </div>
                                            <div class="d-flex gap-2">
                                                <div class="p-0 w-25 d-flex gap-1 justify-content-start align-items-center">
                                                    <span>Grade Level</span>
                                                    <input type="text" class="border-0 border-bottom fw-bold fst-italic"/>
                                                </div>
                                                <div class="p-0 w-25 d-flex gap-1 justify-content-start align-items-center">
                                                    <span>School Year</span>
                                                    <input type="text" class="border-0 border-bottom fw-bold fst-italic"/>
                                                </div>
                                                <div class="p-0 w-25 d-flex gap-1 justify-content-start align-items-center">
                                                    <span>Semester</span>
                                                    <input type="text" class="border-0 border-bottom fw-bold fst-italic"/>
                                                </div>
                                                <div class="p-0 w-25 d-flex gap-1 justify-content-start align-items-center">
                                                    <span>Section</span>
                                                    <input type="text" class="border-0 border-bottom fw-bold fst-italic"/>
                                                </div>
                                            </div>
                                            <div class="d-flex mt-2">
                                                <table class="w-100">
                                                    <thead class="bg-secondary-subtle">
                                                        <tr>
                                                            <th style="align-content: center;border: 1px solid #c9c9c9" rowspan="2" width="150px">Indicite if Subject if CORE, APPLIED, or SPECIALIZED</th>
                                                            <th style="align-content: center;border: 1px solid #c9c9c9" rowspan="2" width="350px">Subjects</th>
                                                            <th style="align-content: center;border: 1px solid #c9c9c9" colspan="2" >Quarter</th>
                                                            <th style="align-content: center;border: 1px solid #c9c9c9" rowspan="2">Sem Final Grade</th>
                                                            <th style="align-content: center;border: 1px solid #c9c9c9" rowspan="2">Actions Taken</th>
                                                        </tr>
                                                        <tr>
                                                            <th style="align-content: center;border: 1px solid #c9c9c9" >1st</th>
                                                            <th style="align-content: center;border: 1px solid #c9c9c9" >2nd</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td style="align-content: center;border: 1px solid #c9c9c9">asd</td>
                                                            <td style="align-content: center;border: 1px solid #c9c9c9">asd</td>
                                                            <td style="align-content: center;border: 1px solid #c9c9c9">asd</td>
                                                            <td style="align-content: center;border: 1px solid #c9c9c9">asd</td>
                                                            <td style="align-content: center;border: 1px solid #c9c9c9">asd</td>
                                                            <td style="align-content: center;border: 1px solid #c9c9c9">asd</td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="4" style="text-align:end; align-content: center;border: 1px solid #c9c9c9">General Average for the Semester &nbsp;</td>
                                                            <td style="align-content: center;border: 1px solid #c9c9c9">asd</td>
                                                            <td style="align-content: center;border: 1px solid #c9c9c9">PASSED</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="d-flex">
                                                <div class="p-0 w-100 d-flex gap-1 justify-content-start align-items-center">
                                                    <span>Remarks</span>
                                                    <input type="text" class="border-0 border-bottom fw-bold fst-italic w-100"/>
                                                </div>
                                            </div>
                                            <div class="d-flex gap-2">
                                                <div class="p-0 w-100 d-flex flex-column text-start">
                                                    <span>Prepared BY</span>
                                                    <input type="text" class="border-0 border-bottom fw-bold fst-italic"/>
                                                    <span>PAOLO Y. BACANI. LPT</span>
                                                    <span style="font-size: 7px; font-style: italic;">Signature of Adviser over Printed Name</span>
                                                </div>
                                                <div class="p-0 w-100 d-flex flex-column text-start">
                                                    <span>Certified True and Correct</span>
                                                    <input type="text" class="border-0 border-bottom fw-bold fst-italic"/>
                                                    <span>Rene Paulo M. Legaspi, Ph.d</span>
                                                    <span style="font-size: 7px; font-style: italic;">Signature of Authorized Person over Printed Name, Designation</span>
                                                </div>
                                                <div class="p-0 w-100 d-flex flex-column text-start">
                                                    <span>Date Checked (MM/DD/YYYY)</span>
                                                    <input type="text" class="border-0 border-bottom fw-bold fst-italic"/>
                                                </div>
                                            </div>
                                            <div class="d-flex bg-secondary-subtle mt-2">
                                                <div class="p-0 border w-100 d-flex flex-column">
                                                    <span class="fw-bold">Remidial Classes</span>
                                                </div>
                                                <div class="p-0 w-100 d-flex gap-1 justify-content-start align-items-center">
                                                    <span>Conducted From</span>
                                                    <input type="text" class="border-0 border-bottom fw-bold fst-italic bg-secondary-subtle"/>
                                                </div>
                                                <div class="p-0 w-100 d-flex gap-1 justify-content-start align-items-center">
                                                    <span>Conducted To</span>
                                                    <input type="text" class="border-0 border-bottom fw-bold fst-italic bg-secondary-subtle"/>
                                                </div>
                                            </div>
                                            <div class="d-flex">
                                                <div class="p-0 w-100 d-flex flex-column text-start">
                                                    <span>School</span>
                                                    <input type="text" class="border-0 border-bottom fw-bold w-100 fst-italic"/>
                                                </div>
                                                <div class="p-0 w-50 d-flex flex-column text-start">
                                                    <span>School ID</span>
                                                    <input type="text" class="border-0 border-bottom fw-bold w-100 fst-italic" value="401951"/>
                                                </div>
                                            </div>
                                            <div class="d-flex mt-2">
                                                <table class="w-100">
                                                    <thead class="bg-secondary-subtle">
                                                        <tr>
                                                            <th style="align-content: center;border: 1px solid #c9c9c9" rowspan="2" width="150px">Indicite if Subject if CORE, APPLIED, or SPECIALIZED</th>
                                                            <th style="align-content: center;border: 1px solid #c9c9c9" rowspan="2" width="350px">Subjects</th>
                                                            <th style="align-content: center;border: 1px solid #c9c9c9" colspan="2" >Quarter</th>
                                                            <th style="align-content: center;border: 1px solid #c9c9c9" rowspan="2">Sem Final Grade</th>
                                                            <th style="align-content: center;border: 1px solid #c9c9c9" rowspan="2">Actions Taken</th>
                                                        </tr>
                                                        <tr>
                                                            <th style="align-content: center;border: 1px solid #c9c9c9" >1st</th>
                                                            <th style="align-content: center;border: 1px solid #c9c9c9" >2nd</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td style="align-content: center;border: 1px solid #c9c9c9">asd</td>
                                                            <td style="align-content: center;border: 1px solid #c9c9c9">asd</td>
                                                            <td style="align-content: center;border: 1px solid #c9c9c9">asd</td>
                                                            <td style="align-content: center;border: 1px solid #c9c9c9">asd</td>
                                                            <td style="align-content: center;border: 1px solid #c9c9c9">asd</td>
                                                            <td style="align-content: center;border: 1px solid #c9c9c9">asd</td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="4" style="text-align:end; align-content: center;border: 1px solid #c9c9c9">General Average for the Semester &nbsp;</td>
                                                            <td style="align-content: center;border: 1px solid #c9c9c9">asd</td>
                                                            <td style="align-content: center;border: 1px solid #c9c9c9">PASSED</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="d-flex">
                                                <div class="p-0 w-100 d-flex flex-column text-start">
                                                    <span>Name of Teacher/Adviser</span>
                                                    <input type="text" class="border-0 border-bottom fw-bold fst-italic"/>
                                                </div>
                                                <div class="p-0 w-100 d-flex flex-column text-start">
                                                    <span>Signature</span>
                                                    <input type="text" class="border-0 border-bottom fw-bold fst-italic"/>
                                                </div>
                                            </div>
                                        </div>

                                        <div>
                                            <div class="d-flex">
                                                <div class="p-0 border w-100 d-flex flex-column bg-secondary-subtle">
                                                    <span class="fw-bold">Scholastic Records</span>
                                                </div>
                                            </div>
                                            <div class="d-flex">
                                                <div class="p-0 w-100 d-flex flex-column text-start">
                                                    <span>School Name</span>
                                                    <input type="text" class="border-0 border-bottom fw-bold w-100 fst-italic" value="Central Luzon College of Science and Technology"/>
                                                </div>
                                                <div class="p-0 w-100 d-flex flex-column text-start">
                                                    <span>Track/Strand</span>
                                                    <input type="text" class="border-0 border-bottom fw-bold w-100 fst-italic"/>
                                                </div>
                                                <div class="p-0 w-50 d-flex flex-column text-start">
                                                    <span>School ID</span>
                                                    <input type="text" class="border-0 border-bottom fw-bold w-100 fst-italic" value="401951"/>
                                                </div>
                                            </div>
                                            <div class="d-flex gap-2">
                                                <div class="p-0 w-25 d-flex gap-1 justify-content-start align-items-center">
                                                    <span>Grade Level</span>
                                                    <input type="text" class="border-0 border-bottom fw-bold fst-italic"/>
                                                </div>
                                                <div class="p-0 w-25 d-flex gap-1 justify-content-start align-items-center">
                                                    <span>School Year</span>
                                                    <input type="text" class="border-0 border-bottom fw-bold fst-italic"/>
                                                </div>
                                                <div class="p-0 w-25 d-flex gap-1 justify-content-start align-items-center">
                                                    <span>Semester</span>
                                                    <input type="text" class="border-0 border-bottom fw-bold fst-italic"/>
                                                </div>
                                                <div class="p-0 w-25 d-flex gap-1 justify-content-start align-items-center">
                                                    <span>Section</span>
                                                    <input type="text" class="border-0 border-bottom fw-bold fst-italic"/>
                                                </div>
                                            </div>
                                            <div class="d-flex mt-2">
                                                <table class="w-100">
                                                    <thead class="bg-secondary-subtle">
                                                        <tr>
                                                            <th style="align-content: center;border: 1px solid #c9c9c9" rowspan="2" width="150px">Indicite if Subject if CORE, APPLIED, or SPECIALIZED</th>
                                                            <th style="align-content: center;border: 1px solid #c9c9c9" rowspan="2" width="350px">Subjects</th>
                                                            <th style="align-content: center;border: 1px solid #c9c9c9" colspan="2" >Quarter</th>
                                                            <th style="align-content: center;border: 1px solid #c9c9c9" rowspan="2">Sem Final Grade</th>
                                                            <th style="align-content: center;border: 1px solid #c9c9c9" rowspan="2">Actions Taken</th>
                                                        </tr>
                                                        <tr>
                                                            <th style="align-content: center;border: 1px solid #c9c9c9" >3rd</th>
                                                            <th style="align-content: center;border: 1px solid #c9c9c9" >4th</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td style="align-content: center;border: 1px solid #c9c9c9">asd</td>
                                                            <td style="align-content: center;border: 1px solid #c9c9c9">asd</td>
                                                            <td style="align-content: center;border: 1px solid #c9c9c9">asd</td>
                                                            <td style="align-content: center;border: 1px solid #c9c9c9">asd</td>
                                                            <td style="align-content: center;border: 1px solid #c9c9c9">asd</td>
                                                            <td style="align-content: center;border: 1px solid #c9c9c9">asd</td>
                                                        </tr>
                                                        <tr class="bg-secondary-subtle">
                                                            <td colspan="4" style="text-align:end; align-content: center;border: 1px solid #c9c9c9">General Average for the Semester &nbsp;</td>
                                                            <td style="align-content: center;border: 1px solid #c9c9c9">asd</td>
                                                            <td style="align-content: center;border: 1px solid #c9c9c9">PASSED</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="d-flex">
                                                <div class="p-0 w-100 d-flex gap-1 justify-content-start align-items-center">
                                                    <span>Remarks</span>
                                                    <input type="text" class="border-0 border-bottom fw-bold fst-italic w-100"/>
                                                </div>
                                            </div>
                                            <div class="d-flex gap-2">
                                                <div class="p-0 w-100 d-flex flex-column text-start">
                                                    <span>Prepared BY</span>
                                                    <input type="text" class="border-0 border-bottom fw-bold fst-italic"/>
                                                    <span>PAOLO Y. BACANI. LPT</span>
                                                    <span style="font-size: 7px; font-style: italic;">Signature of Adviser over Printed Name</span>
                                                </div>
                                                <div class="p-0 w-100 d-flex flex-column text-start">
                                                    <span>Certified True and Correct</span>
                                                    <input type="text" class="border-0 border-bottom fw-bold fst-italic"/>
                                                    <span>Rene Paulo M. Legaspi, Ph.d</span>
                                                    <span style="font-size: 7px; font-style: italic;">Signature of Authorized Person over Printed Name, Designation</span>
                                                </div>
                                                <div class="p-0 w-100 d-flex flex-column text-start">
                                                    <span>Date Checked (MM/DD/YYYY)</span>
                                                    <input type="text" class="border-0 border-bottom fw-bold fst-italic"/>
                                                </div>
                                            </div>
                                            <div class="d-flex bg-secondary-subtle mt-2">
                                                <div class="p-0 border w-100 d-flex flex-column">
                                                    <span class="fw-bold">Remidial Classes</span>
                                                </div>
                                                <div class="p-0 w-100 d-flex gap-1 justify-content-start align-items-center">
                                                    <span>Conducted From</span>
                                                    <input type="text" class="border-0 border-bottom fw-bold fst-italic bg-secondary-subtle"/>
                                                </div>
                                                <div class="p-0 w-100 d-flex gap-1 justify-content-start align-items-center">
                                                    <span>Conducted To</span>
                                                    <input type="text" class="border-0 border-bottom fw-bold fst-italic bg-secondary-subtle"/>
                                                </div>
                                            </div>
                                            <div class="d-flex">
                                                <div class="p-0 w-100 d-flex flex-column text-start">
                                                    <span>School</span>
                                                    <input type="text" class="border-0 border-bottom fw-bold w-100 fst-italic"/>
                                                </div>
                                                <div class="p-0 w-50 d-flex flex-column text-start">
                                                    <span>School ID</span>
                                                    <input type="text" class="border-0 border-bottom fw-bold w-100 fst-italic" value="401951"/>
                                                </div>
                                            </div>
                                            <div class="d-flex mt-2">
                                                <table class="w-100">
                                                    <thead class="bg-secondary-subtle">
                                                        <tr>
                                                            <th style="align-content: center;border: 1px solid #c9c9c9" rowspan="2" width="150px">Indicite if Subject if CORE, APPLIED, or SPECIALIZED</th>
                                                            <th style="align-content: center;border: 1px solid #c9c9c9" rowspan="2" width="350px">Subjects</th>
                                                            <th style="align-content: center;border: 1px solid #c9c9c9" colspan="2" >Quarter</th>
                                                            <th style="align-content: center;border: 1px solid #c9c9c9" rowspan="2">Sem Final Grade</th>
                                                            <th style="align-content: center;border: 1px solid #c9c9c9" rowspan="2">Actions Taken</th>
                                                        </tr>
                                                        <tr>
                                                            <th style="align-content: center;border: 1px solid #c9c9c9" >1st</th>
                                                            <th style="align-content: center;border: 1px solid #c9c9c9" >2nd</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td style="align-content: center;border: 1px solid #c9c9c9">asd</td>
                                                            <td style="align-content: center;border: 1px solid #c9c9c9">asd</td>
                                                            <td style="align-content: center;border: 1px solid #c9c9c9">asd</td>
                                                            <td style="align-content: center;border: 1px solid #c9c9c9">asd</td>
                                                            <td style="align-content: center;border: 1px solid #c9c9c9">asd</td>
                                                            <td style="align-content: center;border: 1px solid #c9c9c9">asd</td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="4" style="text-align:end; align-content: center;border: 1px solid #c9c9c9">General Average for the Semester &nbsp;</td>
                                                            <td style="align-content: center;border: 1px solid #c9c9c9">asd</td>
                                                            <td style="align-content: center;border: 1px solid #c9c9c9">PASSED</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="d-flex">
                                                <div class="p-0 w-100 d-flex flex-column text-start">
                                                    <span>Name of Teacher/Adviser</span>
                                                    <input type="text" class="border-0 border-bottom fw-bold fst-italic"/>
                                                </div>
                                                <div class="p-0 w-100 d-flex flex-column text-start">
                                                    <span>Signature</span>
                                                    <input type="text" class="border-0 border-bottom fw-bold fst-italic"/>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                          
                </div><!-- print div --> 


                <div class="border mt-3 p-0 rounded-2 d-flex gap-1 justify-content-end">
                    <button class="btn btn-sm btn-success" @click="downloadPdf('tor')">Download TOR</button>
                </div>
            </div>
            <div v-else class="">
                <span>No Milestone Found</span>
            </div>
        </div>
    </div>

</template>