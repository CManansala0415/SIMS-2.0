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

        getArchiveMerge(studentID.value).then((results) => {
            // milestoneCompData.value = results
            milestoneCompLoading.value = false
            // group naten yung archive para makapag create ng headers to be looped
            milestoneCompHeader.value = results.filter((value, index, self) =>
                index === self.findIndex((t) => t.arc_id === value.arc_id)
            );


            let x = results.map((e)=>{
                let rgrade = (
                                parseFloat(e.arc_prelimgrade)+ 
                                parseFloat(e.arc_midtermgrade)+ 
                                parseFloat(e.arc_prefinalgrade) + 
                                parseFloat(e.arc_finalgrade)
                            )/4
                let cgrade = 0
                switch(true){
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

                return{
                    raw_grade:rgrade? rgrade:'0',
                    conv_grade:cgrade,
                    ...e
                }
            })

            milestoneCompData.value = Object.groupBy(x, item => item.arc_id);
            // console.log(milestoneCompData.value )
            // console.log(milestoneCompHeader.value )


            profileId.value = milestoneCompHeader.value[0].arc_profile ? 'http://localhost:8000/api/get-person-image/' + milestoneCompHeader.value[0].arc_profile + '/2' : '/img/profile_default.png'
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
                        <span class="fw-bold border bg-primary text-white p-2 rounded-3">Transcript of
                            Records</span>
                    </div>
                </div>
                <div class="small-font bg-opaque " id="printform" style="width: 810px;">
                    <div class="container w-100 backdrop">
                        <div class="row">
                            <div class="border p-2 col-3 d-flex justify-content-center align-content-center ">
                                <div class="d-flex flex-column justify-content-center align-items-center">
                                    <img src="/img/clcst_logo.png" height="100px" width="100px" alt="...">
                                    <div class="small-font d-flex flex-column justify-content-center align-items-center mt-2">
                                        <p class="m-0 fw-bold">ISO 9001:2015 CERTIFIED</p>
                                        <p class="m-0 text-danger fw-bolder">No. 12090</p>
                                    </div>
                                </div>
                            </div>
                            <div class="border p-2 col-9">
                                <div class="text-end p-2">
                                    <p class="m-0 fw-bold fs-6 text-success">CENTRAL LUZON COLLEGE OF SCIENCE AND
                                        TECHNOLOGY, INC.</p>
                                    <!-- <p class="m-0 fw-bold fs-6 text-success">CELTECH COLLEGE</p> -->
                                    <p class="m-0 fw-normal small-font">B. Mendoza St., Brgy. Sto. Rosario, City of San
                                        Fernando,
                                        Pampanga, Philippines, 2000</p>
                                    <p class="m-0 fw-normal small-font">Tel. Nos: (045) 435-1495</p>
                                    <p class="m-0 fw-normal small-font">Founded 1959</p>
                                    <br/>
                                    <p class="m-0 fst-italic">
                                        We <span class="fw-bold">Train</span>,
                                        We <span class="fw-bold">Touch</span>
                                        We <span class="fw-bold">Transform</span>
                                    </p>
                                </div>
                            </div>
                            <!-- <div class="border p-2 col-2 d-flex justify-content-center align-items-center">
                                <div class="d-flex flex-column justify-content-center align-items-center">
                                    <p class="m-0 fw-bold">ISO 9001:2015 CERTIFIED</p>
                                    <p class="m-0 text-danger fw-bolder">No. 12090</p>
                                </div>
                            </div> -->
                            <div class="border p-2 col-12">
                                <div class="d-flex flex align-items-center justify-content-center">
                                    <span class="fs-5">OFFICIAL TRANSCRIPT OF RECORDS</span>
                                </div>
                            </div>
                            <div class="border p-1 col-9 d-flex justify-content-center align-content-center">
                                <div class="text-start d-flex flex-column justify-content-center align-content-center w-100">
                                    <p class="m-0 p-0">
                                        <span class="fw-bold">Name:</span> &nbsp;{{ milestoneCompHeader[0].arc_firstname
                                        }}
                                        {{ milestoneCompHeader[0].arc_middlename ? milestoneCompHeader[0].arc_middlename
                                            : ' ' }}
                                        {{ milestoneCompHeader[0].arc_lastname }}
                                        {{ milestoneCompHeader[0].arc_suffixname ? milestoneCompHeader[0].arc_suffixname
                                            : ' ' }}
                                    </p>
                                    <p class="m-0 p-0">
                                        <span class="fw-bold">Date of Birth:</span> &nbsp;
                                        {{ milestoneCompHeader[0].arc_birthday }}
                                    </p>
                                    <p class="m-0 p-0">
                                        <span class="fw-bold">Address: &nbsp;</span>
                                        {{ milestoneCompHeader[0].arc_curr_home }},
                                        {{ milestoneCompHeader[0].arc_curr_barangay }},
                                        {{ milestoneCompHeader[0].arc_curr_city }},
                                        {{ milestoneCompHeader[0].arc_curr_province }},
                                        {{ milestoneCompHeader[0].arc_curr_zipcode }}
                                    </p>

                                    <p class="m-0 p-0">
                                        <span class="fw-bold">Email: &nbsp;</span>
                                        {{ milestoneCompHeader[0].arc_email }}
                                    </p>
                                    <p class="m-0 p-0">
                                        <span class="fw-bold">Contact No: &nbsp;</span>
                                        0{{ milestoneCompHeader[0].arc_contact }}
                                    </p>
                                    <p class="m-0 p-0">
                                        <span class="fw-bold">Course: &nbsp;</span>
                                        {{ milestoneCompHeader[0].arc_coursename }}
                                    </p>
                                    <p class="m-0 p-0">
                                        <span class="fw-bold">Date of Graduation: &nbsp;</span>
                                        N/A
                                    </p>
                                </div>
                            </div>
                            <div class="border p-2 col-3 d-flex flex-column justify-content-center align-content-center">
                                <div class="d-flex flex justify-content-center">
                                    <img :src="profileId" class="img-size" />
                                </div>
                                <p class="m-0 p-0 mt-1">
                                    <span class="fw-bold">#
                                    {{ milestoneCompHeader[0].arc_studentid }}</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="container w-100 backdrop mt-3">
                        <div class="row bg-body-subtle border mb-3 p-2"
                            v-for="(mtc, index) in milestoneCompHeader">
                            <div class="border p-1 col-12">
                                <div class="d-flex flex justify-content-center p-1 green-mid text-white">
                                    <span class="fw-bold"></span>{{ mtc.arc_yearlevel }}
                                    <span class="fw-bold">&nbsp;</span>({{ mtc.arc_semester }})
                                </div>
                            </div>
                            <!-- <div class="border p-1 col-4">
                                <div class="d-flex flex align-items-start">
                                    <span class="fw-bold">Quarter: &nbsp;</span>{{ mtc.arc_semester }}
                                </div>
                            </div> -->
                            <div class="border p-1 col-6">
                                <div class="d-flex flex align-items-start">
                                    <span class="fw-bold">Curriculum Code: &nbsp;</span> {{
                                        mtc.arc_curriculum ? mtc.arc_curriculum : 'N/A' }}
                                </div>

                            </div>
                            <div class="border p-1 col-6">
                                <div class="d-flex flex align-items-start">
                                    <span class="fw-bold">Section: &nbsp;</span> {{ mtc.arc_section ?
                                        mtc.arc_section : 'N/A' }}
                                </div>

                            </div>
                            <!-- <div class="border p-1 col-6">
                                <div class="d-flex flex align-items-start">
                                    <span class="fw-bold">Application Date: &nbsp;</span> {{
                                        mtc.arc_dateapplied }}
                                </div>
                            </div> -->
                            <!-- <div class="border p-1 col-6">
                                <div class="d-flex flex align-items-start">
                                    <span class="fw-bold">Student ID: &nbsp;</span> {{
                                        mtc.arc_studentid }}
                                </div>
                            </div> -->
                            <!-- <div class="border p-1 col-4">
                                <div class="d-flex flex align-items-start">
                                    <span class="fw-bold">LRN: &nbsp;</span> 
                                </div>
                            </div> -->
                            <div class="border p-1 col-12 bg-body-secondary">
                                <div class="d-flex flex justify-content-center">
                                    <span class="fw-bold">Subjects Taken</span>
                                </div>
                            </div>
                            <div class="border col-12 p-0">
                                <table class="backdrop border-secondary-subtle w-100">
                                    <thead>
                                        <tr>
                                            <th class="p-2">
                                                <span class="fw-bold">Code</span>
                                            </th>
                                            <th class="p-2">
                                                <span class="fw-bold">Subjects Description</span>
                                            </th>
                                            <!-- <th class="p-2">
                                                <span class="fw-bold">Lec</span>
                                            </th>
                                            <th class="p-2">
                                                <span class="fw-bold">Lab</span>
                                            </th> -->
                                            <!-- <th class="p-2">
                                                <span class="fw-bold">Total</span>
                                            </th> -->
                                            <!-- <th class="p-2">
                                                <span class="fw-bold">Prelims</span>
                                            </th>
                                            <th class="p-2">
                                                <span class="fw-bold">Midterms</span>
                                            </th>
                                            <th class="p-2">
                                                <span class="fw-bold">Pre-Finals</span>
                                            </th> -->
                                            <th class="p-2">
                                                <span class="fw-bold">Final Grade</span>
                                            </th>
                                            <th class="p-2">
                                                <span class="fw-bold">Rating</span>
                                            </th>
                                            <th class="p-2">
                                                <span class="fw-bold">Re-Exam</span>
                                            </th>
                                            <!-- <th class="p-2">
                                                <span class="fw-bold">Lec</span>
                                            </th>
                                            <th class="p-2">
                                                <span class="fw-bold">Lab</span>
                                            </th> -->
                                            <th class="p-2">
                                                <span class="fw-bold">Credit</span>
                                            </th>
                                            <!-- <th class="p-2">
                                                <span class="fw-bold">Faculty</span>
                                            </th> -->
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-if="!Object.keys(milestoneCompData).length && !milestoneCompLoading">
                                            <td class="align-middle border-0">
                                                No Subjects Added
                                            </td>
                                        </tr>
                                        <tr v-if="!Object.keys(milestoneCompData).length && milestoneCompLoading">
                                            <td class="align-middle">
                                                <Loader />
                                            </td>
                                        </tr>
                                        <tr v-if="Object.keys(milestoneCompData).length && !milestoneCompLoading"
                                            v-for="(c, index) in milestoneCompData[mtc.arc_id]">
                                            <td class="align-middle">
                                                <span class="fw-bold">{{ c.arc_subjectcode }}</span>
                                            </td>
                                            <td class="align-middle">
                                                <span class="">{{ c.arc_subjectname }}</span>
                                                <!-- <span v-if="c.mi_crossenr" class="mt-3">Cross Enrolled to: <span
                                                                class=" text-red-500"> {{ c.mi_crossenr }}</span></span> -->
                                            </td>
                                            <!-- <td class="align-middle">
                                                <span>{{ c.arc_lecture }}</span>
                                            </td>
                                            <td class="align-middle">
                                                <span>{{ c.arc_laboratory }}</span>
                                            </td>
                                            <td class="align-middle">
                                                <span class="fw-bold">{{ c.arc_lecture + c.arc_laboratory }}</span>
                                            </td> -->
                                            <!-- <td class="align-middle">
                                                <span class="text-primary">{{ c.arc_prelimgrade }}</span>
                                            </td>
                                            <td class="align-middle">
                                                <span class="text-primary">{{ c.arc_midtermgrade }}</span>
                                            </td>
                                            <td class="align-middle">
                                                <span class="text-primary">{{ c.arc_prefinalgrade }}</span>
                                            </td> -->
                                            <td class="align-middle">
                                                <span>
                                                    {{ 
                                                        c.raw_grade
                                                    }} 
                                                </span>
                                            </td>
                                            <td class="align-middle">
                                                <span class="fw-semibold">
                                                    {{ 
                                                        c.conv_grade
                                                    }} 
                                                </span>
                                            </td>
                                            <td class="align-middle">
                                                <!-- <span>{{ c.arc_lecture }}</span> -->
                                            </td>
                                            <!-- <td class="align-middle">
                                                <span>{{ c.arc_lecture }}</span>
                                            </td>
                                            <td class="align-middle">
                                                <span>{{ c.arc_laboratory }}</span>
                                            </td> -->
                                            <td class="align-middle">
                                                <span class="fw-bold">{{ c.arc_lecture + c.arc_laboratory }}</span>
                                            </td>
                                            <!-- <td class="align-middle">
                                                <span class="text-primary">{{ c.arc_facultyname }}</span>
                                            </td> -->
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                           
                        </div>
                    </div>
                    <div class="container w-100 backdrop mt-3">
                        <div class="border p-1 col-12">
                            <div class="d-flex justify-content-center p-1">
                                <span class="fw-bold">PURPOSE: </span>
                                <span class="fst-italic">&nbsp;VALID FOR CERTIFICATION, AUTHENTICATION & VERIFICATION (CAV) PURPOSES ONLY</span>
                            </div>
                        </div>
                        <div class="border p-1 col-12">
                            <!-- <div class="border w-100">
                                <span class="small-font fw-bold">Rating System</span>
                            </div> -->
                            
                            <div class="d-flex gap-1 justify-content-center p-1 small-font">
                                <div class="border w-100">
                                    <div class="d-flex gap-1 justify-content-center ">
                                        <div class="w-100">
                                           <span class="small-font fw-bold">Rating System</span>
                                        </div>
                                    </div>
                                    <div class="d-flex gap-1 justify-content-center ">
                                        <div class="w-100">
                                            97.50 - 100 
                                        </div>
                                        <div class="w-100">
                                            1.0
                                        </div>
                                    </div>
                                    <div class="d-flex gap-1 justify-content-center ">
                                        <div class="w-100">
                                            92.50 - 97.49 
                                        </div>
                                        <div class="w-100">
                                            1.25
                                        </div>
                                    </div>
                                </div>
                                <div class="border w-100">
                                    <div class="d-flex gap-1 justify-content-center">
                                        <div class="w-100">
                                            87.50 - 92.49 
                                        </div>
                                        <div class="w-100">
                                            1.5
                                        </div>
                                    </div>
                                    <div class="d-flex gap-1 justify-content-center">
                                        <div class="w-100">
                                            82.50 - 87.49 
                                        </div>
                                        <div class="w-100">
                                            1.75
                                        </div>
                                    </div>
                                    <div class="d-flex gap-1 justify-content-center">
                                        <div class="w-100">
                                            77.50 - 82.49 
                                        </div>
                                        <div class="w-100">
                                            2.0
                                        </div>
                                    </div>
                                </div>
                                <div class="border w-100">
                                    <div class="d-flex gap-1 justify-content-center">
                                        <div class="w-100">
                                            72.50 - 77.49 
                                        </div>
                                        <div class="w-100">
                                            2.25
                                        </div>
                                    </div>
                                    <div class="d-flex gap-1 justify-content-center">
                                        <div class="w-100">
                                            65.00 - 72.49 
                                        </div>
                                        <div class="w-100">
                                            2.5
                                        </div>
                                    </div>
                                    <div class="d-flex gap-1 justify-content-center">
                                        <div class="w-100">
                                            57.50 - 64.99 
                                        </div>
                                        <div class="w-100">
                                            2.75
                                        </div>
                                    </div>
                                </div>
                                <div class="border w-100">
                                    <div class="d-flex gap-1 justify-content-center">
                                        <div class="w-100">
                                            50.00 - 57.49 
                                        </div>
                                        <div class="w-100">
                                            3.0
                                        </div>
                                    </div>
                                    <div class="d-flex gap-1 justify-content-center">
                                        <div class="w-100">
                                            46.00 - 49.99 
                                        </div>
                                        <div class="w-100">
                                            2.5 Conditional
                                        </div>
                                    </div>
                                    <div class="d-flex gap-1 justify-content-center">
                                        <div class="w-100">
                                            45.99 - Below
                                        </div>
                                        <div class="w-100">
                                            5.0 Failure
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex gap-1 justify-content-center p-1 small-font">
                                <div class="border w-100">
                                    <div class="d-flex gap-1 justify-content-center ">
                                        <div class="w-100">
                                            <span class="fw-bold">INC</span> - Incomplete
                                        </div>
                                        <div class="w-100">
                                            <span class="fw-bold">AW</span> - Authorized Withdrawal
                                        </div>
                                        <div class="w-100">
                                            <span class="fw-bold">UW</span> - Unauthorized Withdrawal
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="container w-100 backdrop mt-3">
                        <div class="border p-1 col-12">
                            <div class="d-flex gap-1 justify-content-center align-content-end p-1 small-font" style="height: 100px;">
                                <div class="w-100 d-flex justify-content-center align-items-center">
                                    <div class="d-flex gap-1 justify-content-center ">
                                        <div class="w-100">
                                            School Seal
                                        </div>
                                    </div>
                                </div>
                                <div class="w-100 d-flex justify-content-center align-items-end">
                                    <div class="d-flex flex-column w-100">
                                        <div class="w-100 border-0 border-bottom">
                                            <span class="fw-bold">Rowendy G. Mallari</span>
                                        </div>
                                        <div class="w-100">
                                            Prepared By
                                        </div>
                                    </div>
                                </div>
                                <div class="w-100 d-flex justify-content-center align-items-end">
                                    <div class="d-flex flex-column w-100">
                                        <div class="w-100 border-0 border-bottom">
                                            <span class="fw-bold">Rowena C. David</span>
                                        </div>
                                        <div class="w-100">
                                            Accounts Verified By
                                        </div>
                                    </div>
                                </div>
                                <div class="w-100 d-flex justify-content-center align-items-end">
                                    <div class="d-flex flex-column w-100">
                                        <div class="w-100 border-0 border-bottom">
                                            <span class="fw-bold">April Joy R. Manalo</span>
                                        </div>
                                        <div class="w-100">
                                            Registrar
                                        </div>
                                    </div>
                                </div>
                                <div class="w-100 d-flex justify-content-center align-items-end">
                                    <div class="d-flex flex-column w-100">
                                        <div class="w-100 border-0 border-bottom">
                                            <span class="fw-bold">Renato P. Legaspi, Ph.D.</span>
                                        </div>
                                        <div class="w-100">
                                            President
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex gap-1 justify-content-center p-1 small-font mt-3">
                                <div class="w-100">
                                    <div class="d-flex gap-1 justify-content-center p-2">
                                        <div class="w-100">
                                            <span class="fw-semibold fst-italic">Not valid without school seal. Any erasure or alteration renders the whole Transcript of Records 
                                                <span class="text-danger fw-bolder">INVALID</span>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="d-flex gap-1 justify-content-around">
                                        <div class="w-100 d-flex justify-content-start">
                                            <span class="fw-light">QF-REG 05-07-10APR13</span>
                                        </div>
                                        <div class="w-100 d-flex justify-content-end">
                                            <span class="fw-light">OR No. 35622 | Date 06.18.2024</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="border mt-3 p-2 rounded-2 d-flex gap-2 justify-content-end">
                    <button class="btn btn-sm btn-success" @click="downloadPdf()">Download TOR</button>
                </div>
            </div>
            <div v-else class="">
                <span>No Milestone Found</span>
            </div>
        </div>
    </div>

</template>