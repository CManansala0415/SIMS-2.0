<script setup>
import { ref, onMounted, computed } from 'vue';
import { getUserID } from "../../../routes/user.js";
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

const milestoneCompData = ref([])
const milestoneCompHeader = ref([])
const milestoneCompLoading = ref(true)
const studentID = ref('')
const yearFilter = ref('')
const filteredMilestone = ref([])

onMounted(async () => {
    window.stop()
    try {

        if (moduleData.value == 2) {
            studentID.value = studentData.value.arc_personid
        } else {
            studentID.value = studentData.value.per_id
        }

        getArchiveMerge(studentID.value).then((results) => {
            // // milestoneCompData.value = results
            // milestoneCompLoading.value = false
            // // group naten yung archive para makapag create ng headers to be looped
            // milestoneCompHeader.value = results.filter((value, index, self) =>
            //     index === self.findIndex((t) => t.arc_id === value.arc_id)
            // );
            // milestoneCompData.value = Object.groupBy(results, item => item.arc_id);

            // filteredMilestone.value = milestoneCompData.value 
            // // console.log(milestoneCompData.value )
            // // console.log(milestoneCompHeader.value )
            // // console.log(Array.isArray(milestoneCompData.value)) 
            // preloading.value = false

            milestoneCompLoading.value = false;
            milestoneCompHeader.value = results.filter(
                (value, index, self) => index === self.findIndex((t) => t.arc_id === value.arc_id)
            );
            milestoneCompData.value = Object.groupBy(results, item => item.arc_id);
            // initialize the filtered version with ALL headers
            filteredMilestone.value = milestoneCompHeader.value;
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


const downloadPdf = (filetype) => {
    // var element = document.getElementById('printform');
    // html2pdf(element);
    let name = studentID.value + '_' + filetype
    pdfGenerator(name, 'a4', 'portrait', 0.1)
    Swal.fire({
        icon: "success",
        title: "Download Complete",
        text: "Check your file manager, refreshing the page",
    }).then(() => {
        location.reload()
    });

}

const filterCard = () => {
  if (yearFilter.value === '') {
    filteredMilestone.value = milestoneCompHeader.value
  } else {
    let index = parseInt(yearFilter.value)
    filteredMilestone.value = [milestoneCompHeader.value[index]]
  }
}

</script>

<template>
<div class="d-flex flex-wrap w-100 ">
        <div v-if="preloading" class="d-flex w-100 mt-4 justify-content-center align-content-center">
            <Loader />
        </div>
        <div v-else class="container p-3">
            <div v-if="Object.keys(filteredMilestone).length" class="d-flex flex-column">
                <div class="col-12 border-bottom">
                    <div class="p-3">
                        <span class="fw-bold border bg-primary text-white p-2 rounded-3">PERMANENT RECORD / CARD</span>
                    </div>
                </div>
                <div class="col-12 border-bottom mb-4">
                    <div class="p-3 d-flex justify-content-center">
                        <select class="form-select w-50" @change="filterCard()" v-model="yearFilter">
                            <option value="">-- All School Year--</option>
                            <option :value="index" v-for="(mtc, index) in milestoneCompHeader">{{ mtc.arc_schoolyear }} | {{ mtc.arc_semester }}</option>
                        </select>
                    </div>
                </div>
                <div class="col-12 small-font d-flex flex-column justify-content-center align-items-center" id="printform">
                    <div v-for="(mtc, index) in filteredMilestone"
                        style="width: 770px; height: 1104px;  font-size: 8.8px;">

                        <div class="d-flex flex-column justify-content-between border small-font bg-opaque h-100" >
                            <div>
                                <table class="table table-fixed" style="text-transform:uppercase">
                                    <thead>
                                        <tr>
                                            <th class="align-middle">
                                                <img src="/img/clcst_logo.png" height="60px" width="60px" alt="...">
                                            </th>
                                            <th class="align-middle text-center">
                                                <p class="m-0" style="font-size: 12px;">CENTRAL LUZON COLLEGE OF SCIENCE AND TECHNOLOGY, INC.</p>
                                                <p class="mb-2">CELTECH COLLEGE</p>
                                                <p class="m-0 fw-normal small-font">B. Mendoza St., Brgy. Sto. Rosario, City
                                                    of San Fernando,
                                                    Pampanga, Philippines, 2000</p>
                                                <p class="m-0 fw-normal small-font">Tel. Nos: (045) 435-1495</p>
                                                <p class="m-0 fw-normal small-font">Founded 1959</p>
                                            </th>
                                            <th class="align-middle"><img src="/img/clcst_logo.png" height="60px" width="60px"
                                                    alt="..."></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td colspan="3">
                                                <div class="d-flex flex-column text-center w-100">
                                                    <span class="fw-bold">CERTIFICATE OF GRADES</span>
                                                    <span>Subject Grades</span>
                                                    <span class="fw-bold mt-2">{{mtc.arc_schoolyear}}</span>
                                                    
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="align-middle" colspan="3">
                                                <table class="table">
                                                    <tbody>
                                                        <tr class="text-start">
                                                            <td class="p-2 border" colspan="2">
                                                                <span style="text-transform:none">Name: </span>
                                                                <span class="fw-semibold">
                                                                    {{ mtc.arc_firstname }}
                                                                    {{ mtc.arc_middlename ? mtc.arc_middlename : ' ' }}
                                                                    {{ mtc.arc_lastname }}
                                                                    {{ mtc.arc_suffixname ? mtc.arc_suffixname : ' ' }}
                                                                </span>
                                                            </td>
                                                            <td class="p-2 border">
                                                                <span style="text-transform:none">Student ID: </span>
                                                                <span class="fw-semibold">
                                                                    {{ mtc.arc_studentid }}
                                                                </span>
                                                            </td>
                                                            <td class="p-2 border">
                                                                <span style="text-transform:none">Date: </span>
                                                                <span class="fw-semibold">
                                                                    {{ mtc.arc_dateenrolled? mtc.arc_dateenrolled:'N/A' }}
                                                                </span>
                                                            </td>
                                                        </tr>
                                                        <tr class="text-start">
                                                            <td class="p-2 border">
                                                                <span style="text-transform:none">Course: </span>
                                                                <span class="fw-semibold">
                                                                    {{ mtc.arc_coursename }}
                                                                </span>
                                                            </td>
                                                            <td class="p-2 border">
                                                                <span style="text-transform:none">Grade/Year: </span>
                                                                <span class="fw-semibold">
                                                                    {{ mtc.arc_yearlevel }}
                                                                </span>
                                                            </td>
                                                            <td class="p-2 border">
                                                                <span style="text-transform:none">Section: </span>
                                                                <span class="fw-semibold">
                                                                    {{ mtc.arc_section ? mtc.arc_section : 'N/A' }}
                                                                </span>
                                                            </td>
                                                            <td class="p-2 border">
                                                                <span style="text-transform:none">Semester: </span>
                                                                <span class="fw-semibold">
                                                                    {{ mtc.arc_semester }}
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="text-center w-100">
                                    <p class="fw-bold">Subjects Enrolled</p>
                                </div>
                                <table class="table table-fixed" style="text-transform:uppercase">
                                    <thead>
                                        <tr>
                                            <th style="background-color: #000000;" class="text-white">Code</th>
                                            <th style="background-color: #000000; width: 300px;" class="text-white">Subject
                                            </th>
                                            <th style="background-color: #000000;" class="text-white">Lecture</th>
                                            <th style="background-color: #000000;" class="text-white">Laboratory</th>
                                            <!-- <th style="background-color: #000000;" class="text-white">Total</th> -->
                                            <!-- <th style="background-color: #000000;" class="text-white">Prelim</th> -->
                                            <!-- <th style="background-color: #000000;" class="text-white">Midterm</th> -->
                                            <!-- <th style="background-color: #000000;" class="text-white">Pre-Final</th> -->
                                            <!-- <th style="background-color: #000000;" class="text-white">Final</th> -->
                                            <th style="background-color: #000000;" class="text-white">Final Grade</th>
                                            <!-- <th style="background-color: #000000;" class="text-white">Faculty</th> -->
                                            <!-- <th style="background-color: #000000;" class="text-white">Remarks</th> -->
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(c, index) in milestoneCompData[mtc.arc_id]">
                                            <td class="align-middle p-2 text-start">
                                                {{ c.arc_subjectcode }}
                                            </td>
                                            <td class="align-middle p-2 text-start">
                                                {{ c.arc_subjectname }}
                                            </td>
                                            <td class="align-middle p-2">
                                                {{ c.arc_lecture? c.arc_lecture:'N/A' }}
                                            </td>
                                            <td class="align-middle p-2">
                                                {{ c.arc_laboratory? c.arc_laboratory:'N/A' }}
                                            </td>
                                            <!-- <td class="align-middle p-2">
                                                    {{ c.subj_lec + c.subj_lab }}
                                                </td> -->
                                            <!-- <td class="align-middle p-2">
                                                    {{ c.grs_prelims? c.grs_prelims:'N/A' }}
                                                </td>
                                                <td class="align-middle p-2">
                                                    {{ c.grs_midterms? c.grs_midterms:'N/A' }}
                                                </td>
                                                <td class="align-middle p-2">
                                                    {{ c.grs_prefinals? c.grs_prefinals:'N/A' }}
                                                </td>
                                                <td class="align-middle p-2">
                                                    {{ c.grs_finals? c.grs_finals:'N/A' }}
                                                </td> -->
                                            <td class="align-middle p-2">
                                                {{ (parseInt(c.arc_prelimgrade) + parseInt(c.arc_midtermgrade) +
                                                    parseInt(c.arc_prefinalgrade) +
                                                parseInt(c.arc_finalgrade))/4 }}
                                            </td>
                                            <!-- <td class="align-middle p-2">
                                                    {{ c.arc_facultyname }}
                                                </td> -->
                                            <!-- <td class="align-middle p-2">
                                                    N/A
                                                </td> -->
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div>
                                <div class="d-flex flex-column justify-content-center align-items-center mb-2"
                                    style="font-size:8px">
                                    <div class="w-75 p-1 ">
                                        <span class="fst-italic">
                                            I shall abide by all the rules and regulations now enforced or may be
                                            promulgated by Central Luzon
                                            College of Science and Technology, Inc. from time to time
                                            Likewise, I agree to the cancellation of the credits I have earned in subjects I
                                            have enrolled under
                                            false pretenses.
                                        </span>
                                    </div>
                                    <div
                                        class="p-1 d-flex flex-column gap-1 justify-content-center align-content-center align-items-center border border-dark-subtle rounded bg-body-tertiary shadow-sm">
                                        <span class="text-danger fw-bold mt-1"> IMPORTANT </span>
                                        <span class="fw-bold mt-1">
                                            OFFICIAL STUDY LOAD - means officially enrolled subjects and it will serve as an
                                            admission slip to the classroom.
                                        </span>
                                        <span class="fw-bold fa-underline mt-1  border-0 border-bottom border-dark-subtle">
                                            * CHARGES TO STUDENTS WITHDRAWING/ DROPPING *
                                        </span>
                                        <ul class="text-start">
                                            <li>
                                                <strong>Before the start of classes:</strong> charged the full amount of the
                                                registration
                                                fee, School ID, report cards, etc., <em>plus</em> <strong>10% of the total
                                                    tuition
                                                    fees</strong>.
                                            </li>
                                            <li>
                                                <strong>Within the first week of classes:</strong> charged an amount equal
                                                to <strong>30% of
                                                    the total charges for the whole semester</strong>, regardless of whether
                                                or not he has
                                                actually attended classes.
                                            </li>
                                            <li>
                                                <strong>Within the second week of classes:</strong> charged an amount equal
                                                to <strong>80%
                                                    of the total charges for the whole semester</strong>, regardless of
                                                whether or not he
                                                has actually attended classes.
                                            </li>
                                            <li>
                                                <strong>After the second week of classes:</strong> charged the <strong>full
                                                    amount due for
                                                    the whole semester</strong>, regardless of whether or not he has
                                                actually attended
                                                classes.
                                            </li>
                                        </ul>
                                        <small>
                                            Note: This statement serves as the official policy regarding
                                            withdrawal/drop charges. For questions, contact the Registrar's Office.
                                        </small>

                                        <div class="d-flex gap-2 w-75 justify-content-between mt-3">
                                            <div class="w-100 text-center">
                                                <div style="height:1px; background:#808080;"></div>
                                                <p style="font-size:8px; color:#374151;">Student Signature over
                                                    Printed Name</p>
                                            </div>
                                            <div class="w-100 text-center">
                                                <div style="height:1px; background:#808080;"></div>
                                                <p style="font-size:8px; color:#374151;">Registrar / Authorized
                                                    Signature</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    
                        <div class="page-break"></div>
                    </div>
                    
                </div>

                <div class="border mt-3 p-2 rounded-2 d-flex gap-2 justify-content-end">
                    <button class="btn btn-sm btn-success" @click="downloadPdf('card')">Download Card</button>
                </div>
            </div>
            <div v-else class="">
                <span>No Milestone Found</span>
            </div>
        </div>
    </div>

</template>