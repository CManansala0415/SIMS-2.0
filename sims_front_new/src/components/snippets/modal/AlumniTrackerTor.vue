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


const active_arc_fullname = ref('')
const active_arc_address = ref('')
const active_arc_birthday = ref('')
const active_arc_curr_home = ref('')
const active_arc_curr_barangay = ref('')
const active_arc_curr_city = ref('')
const active_arc_curr_province = ref('')
const active_arc_curr_zipcode = ref('')
const active_arc_email = ref('')
const active_arc_contact = ref('')
const active_arc_coursename = ref('')

const pagesContent = ref([])
const pagesCount = ref(0)

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
        // console.log(studentData.value)
        getArchiveMerge(studentID.value).then((results) => {
            // milestoneCompData.value = results

            milestoneCompLoading.value = false
            // group naten yung archive para makapag create ng headers to be looped
            milestoneCompHeader.value = results.filter((value, index, self) =>
                index === self.findIndex((t) => t.arc_id === value.arc_id)
            );

            // console.log(results)

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
            

            // pagesContent
            // pagesCount
             
            for (let i = 0; i < milestoneCompHeader.value.length; i += 5) {
                let group = {
                    items: milestoneCompHeader.value.slice(i, i + 5)
                };
                pagesContent.value.push(group);
            }

            preloading.value = false
            // console.log(pagesContent.value)


            let active_data = milestoneCompHeader.value.pop()
            active_arc_fullname.value = active_data.arc_firstname? active_data.arc_firstname + ' ' + (active_data.arc_middlename ? active_data.arc_middlename + ' ' : ' ') + active_data.arc_lastname + ' ' + (active_data.arc_suffixname ? active_data.arc_suffixname : ' ') : ''
            active_arc_address.value = active_data.arc_curr_home? active_data.arc_curr_home: ' ' + ', ' + active_data.arc_curr_barangay? active_data.arc_curr_barangay: ' ' + ', ' + active_data.arc_curr_city? active_data.arc_curr_city: ' ' + ', ' + active_data.arc_curr_province? active_data.arc_curr_province: ' ' + ', ' + active_data.arc_curr_zipcode? active_data.arc_curr_zipcode: ' '
            active_arc_birthday.value = active_data.arc_birthday
            active_arc_curr_home.value = active_data.arc_curr_home
            active_arc_curr_barangay.value = active_data.arc_curr_barangay
            active_arc_curr_city.value = active_data.arc_curr_city
            active_arc_curr_province.value = active_data.arc_curr_province
            active_arc_curr_zipcode.value = active_data.arc_curr_zipcode
            active_arc_email.value = active_data.arc_email
            active_arc_contact.value = active_data.arc_contact
            active_arc_coursename.value = active_data.arc_coursename
            
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
                        <span class="fw-bold bg-primary text-white p-1 rounded-3">Transcript of
                            Records</span>
                    </div>
                </div>
                <div id="printform" class="h-auto d-flex flex-column justify-content-center align-items-start">

                    <!--Page 1-->
                    <div v-for="(page, pageIndex) in pagesContent">
                        <div class="small-font bg-opaque mb-1"
                            style="width: 795px; height:1321px; border:2px solid black; font-size: 8.8px;">
                            <!-- height:1315px; width: 745px; height:1215px; -->
                            <div class="h-100 backdrop d-flex flex-column justify-content-between">
                                <div class="container w-100">
                                    <div class="row">
                                        <div class="p-1 col-3 d-flex justify-content-center align-content-center ">
                                            <div class="d-flex flex-column justify-content-center align-items-center">
                                                <img src="/img/clcst_logo.png" height="60px" width="60px" alt="...">
                                                <div
                                                    class="small-font d-flex flex-column justify-content-center align-items-center mt-2">
                                                    <p class="m-0 fw-bold">ISO 9001:2015 CERTIFIED</p>
                                                    <p class="m-0 text-danger fw-bolder">No. 12090</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="p-1 col-9">
                                            <div class="text-end p-2">
                                                <p class="m-0 fw-bold fs-6 text-success">CENTRAL LUZON COLLEGE OF SCIENCE
                                                    AND
                                                    TECHNOLOGY, INC.</p>
                                                <!-- <p class="m-0 fw-bold fs-6 text-success">CELTECH COLLEGE</p> -->
                                                <p class="m-0 fw-normal small-font">B. Mendoza St., Brgy. Sto. Rosario, City
                                                    of
                                                    San
                                                    Fernando,
                                                    Pampanga, Philippines, 2000</p>
                                                <p class="m-0 fw-normal small-font">Tel. Nos: (045) 435-1495</p>
                                                <p class="m-0 fw-normal small-font">Founded 1959</p>
                                                <br />
                                                <p class="m-0 fst-italic">
                                                    We <span class="fw-bold">Train</span>,
                                                    We <span class="fw-bold">Touch</span>
                                                    We <span class="fw-bold">Transform</span>
                                                </p>
                                            </div>
                                        </div>
                                        
                                        <div class="p-1 col-12">
                                            <div class="d-flex flex align-items-center justify-content-center">
                                                <span>OFFICIAL TRANSCRIPT OF RECORDS</span>
                                            </div>
                                        </div>
                                        <div class="p-1 col-12 d-flex justify-content-center align-content-center">
                                            <div
                                                class="text-start d-flex justify-content-between align-items-start w-100 border-0 border-bottom pb-2">
                                                <div class="h-100 w-75 p-2">
                                                    <div class="px-3">
                                                        <p class="m-0 p-0">
                                                            <span class="fw-bold">Name:</span> &nbsp;{{active_arc_fullname}}
                                                        </p>
                                                        <p class="m-0 p-0">
                                                            <span class="fw-bold">Date of Birth:</span> &nbsp;
                                                            {{ active_arc_birthday }}
                                                        </p>
                                                        <p class="m-0 p-0">
                                                            <span class="fw-bold">Address: &nbsp;</span>
                                                            {{ active_arc_address }}
                                                        </p>

                                                        <p class="m-0 p-0">
                                                            <span class="fw-bold">Email: &nbsp;</span>
                                                            {{ active_arc_email }}
                                                        </p>
                                                        <p class="m-0 p-0">
                                                            <span class="fw-bold">Contact No: &nbsp;</span>
                                                            0{{ active_arc_contact }}
                                                        </p>
                                                        <p class="m-0 p-0">
                                                            <span class="fw-bold">Course: &nbsp;</span>
                                                            {{ active_arc_coursename }}
                                                        </p>
                                                        <p class="m-0 p-0">
                                                            <span class="fw-bold">Date of Graduation: &nbsp;</span>
                                                            N/A
                                                        </p>
                                                    </div>
                                                </div>
                                                <div
                                                    class="h-100 p-1 d-flex flex-column justify-content-center align-items-center">
                                                    <div
                                                        class="d-flex flex-column flex justify-content-center align-items-center">
                                                        <img :src="profileId" class="img-size" />
                                                        <p class="m-0 p-0 mt-1">
                                                            <span class="fw-bold">#
                                                                {{ milestoneCompHeader[0].arc_studentid }}</span>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                <div class="container w-100 backdrop h-100 border-3 border-black">
                                    <div class="d-flex flex justify-content-center">
                                        <span class="fw-bold bg-success px-3 py-1 text-white rounded-2 mb-3">Subjects Taken</span>
                                    </div>
                                    <div class="h-100 d-flex flex-column justify-content-start text-center">
                                        <!-- 1st year 1st Sem-->
                                        <div class="container w-100" > <!--v-for="(mtc, index) in milestoneCompHeader"-->
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <td  class="align-middle fw-bolder" style="width: 100px;">Term</td>
                                                        <td class="align-middle fw-bolder" >Subjects</td>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                <tr v-for="(mtc, index) in page.items" v-if="milestoneCompHeader !== undefined">
                                                        <td class="align-middle">
                                                            <div class="d-flex flex-column">
                                                                <span class="fw-bold">{{ mtc.arc_yearlevel }}</span>
                                                                <span class="fw-bold">&nbsp;</span>({{mtc.arc_schoolyear}}, {{mtc.arc_semester}})
                                                                <span class="fw-bold">&nbsp;</span>{{mtc.arc_coursename}}
                                                            </div>
                                                        </td>
                                                        <td class="align-middle">
                                                            <table class="w-" style="table-layout: fixed;">
                                                                <thead>
                                                                    <tr>
                                                                        <th class="p-1" style="width:70px; white-space: nowrap;">
                                                                            <div
                                                                                style="display: flex; flex-direction: column; justify-content: center; align-items: center;">
                                                                                <span class="fw-bold">Code</span>
                                                                            </div>
                                                                        </th>
                                                                        <th class="p-1" style="width:400px; white-space: nowrap;">
                                                                            <div
                                                                                style="display: flex; flex-direction: column; justify-content: center; align-items: center;">
                                                                                <span class="fw-bold">Subjects Description</span>
                                                                            </div>
                                                                        </th>
                                                                        <th class="p-1" v-if="mtc.arc_migtype != 2">
                                                                            <div
                                                                                style="display: flex; flex-direction: column; justify-content: center; align-items: center;">
                                                                                <span class="fw-bold">Final Grade</span>
                                                                            </div>
                                                                        </th>
                                                                        <th class="p-1">
                                                                            <div
                                                                                style="display: flex; flex-direction: column; justify-content: center; align-items: center;">
                                                                                <span class="fw-bold">Rating</span>
                                                                            </div>
                                                                        </th>
                                                                        <th class="p-1">
                                                                            <div
                                                                                style="display: flex; flex-direction: column; justify-content: center; align-items: center;">
                                                                                <span class="fw-bold">Re-Exam</span>
                                                                            </div>
                                                                        </th>
                                                                        <th class="p-1">
                                                                            <div
                                                                                style="display: flex; flex-direction: column; justify-content: center; align-items: center;">
                                                                                <span class="fw-bold">Credit</span>
                                                                            </div>
                                                                        </th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                <tr v-if="Object.keys(milestoneCompData).length && !milestoneCompLoading"
                                                                    v-for="(c, index) in milestoneCompData[mtc.arc_id]">
                                                                <td class="align-middle">
                                                                    <div
                                                                        style="display: flex; flex-direction: column; justify-content: center; align-items: center;">
                                                                        <span class="fw-bold">{{ c.arc_subjectcode }}</span>
                                                                    </div>
                                                                    </td>
                                                                    <td class="align-middle">
                                                                        <div
                                                                            style="display: flex; flex-direction: column; justify-content: center; align-items: center;">
                                                                            <span class="">{{ c.arc_subjectname }}</span>
                                                                        </div>
                                                                    </td>
                                                                    <td class="align-middle" v-if="mtc.arc_migtype != 2">
                                                                        <div
                                                                            style="display: flex; flex-direction: column; justify-content: center; align-items: center;">
                                                                            <span>
                                                                                {{
                                                                                    c.raw_grade
                                                                                }}
                                                                            </span>
                                                                        </div>
                                                                    </td>
                                                                    <td class="align-middle">
                                                                        <div
                                                                            style="display: flex; flex-direction: column; justify-content: center; align-items: center;">
                                                                            <span class="fw-semibold">
                                                                                {{
                                                                                    c.conv_grade
                                                                                }}
                                                                            </span>
                                                                        </div>

                                                                    </td>
                                                                    <td class="align-middle">
                                                                        <!-- <span>{{ c.arc_lecture }}</span> -->
                                                                    </td>
                                                                    <td class="align-middle">
                                                                        <div v-if="mtc.arc_migtype != 2"
                                                                            style="display: flex; flex-direction: column; justify-content: center; align-items: center;">
                                                                            <span class="fw-bold">{{ c.arc_lecture +
                                                                                c.arc_laboratory
                                                                                }}</span>
                                                                        </div>
                                                                        <div v-else
                                                                            style="display: flex; flex-direction: column; justify-content: center; align-items: center;">
                                                                            <span class="fw-bold">{{ 
                                                                                c.arc_units
                                                                                }}</span>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                </tbody>
                                                            </table>
                                                        
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <p> --- End of Page {{ pageIndex+1 }} ---</p>
                                        <!-- 1st year 1st sem -->
                                    </div>
                                </div>
                                <div class="container w-100 backdrop mt-3 p-0">
                                    <div class="w-100">
                                        <div class="border p-1 col-12">
                                            <div class="d-flex justify-content-center p-1">
                                                <span class="fw-bold">PURPOSE: </span>
                                                <span class="fst-italic">&nbsp;VALID FOR CERTIFICATION, AUTHENTICATION &
                                                    VERIFICATION (CAV) PURPOSES ONLY</span>
                                            </div>
                                        </div>
                                        <div class="border p-1 col-12">
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
                                </div>
                            </div>
                        </div>
                        <!-- <div class="page-break"></div> -->
                    </div>
                    
                    <!--Page 1-->
                </div> <!-- print div -->


                <div class="border mt-3 p-1 rounded-2 d-flex gap-1 justify-content-end">
                    <button class="btn btn-sm btn-success" @click="downloadPdf('tor')">Download TOR</button>
                </div>
            </div>
            <div v-else class="">
                <span>No Milestone Found</span>
            </div>
        </div>
    </div>

</template>