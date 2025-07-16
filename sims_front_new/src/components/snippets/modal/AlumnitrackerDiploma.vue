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
    <div class="d-flex flex-wrap w-100 ">
        <div v-if="preloading" class="d-flex w-100 mt-4 justify-content-center align-content-center">
            <Loader />
        </div>
        <div v-else class="container p-3">
            <div v-if="Object.keys(milestoneCompHeader).length" class="d-flex flex-column">
                <div class="col-12 border-bottom">
                    <div class="p-3">
                        <span class="fw-bold border bg-primary text-white p-2 rounded-3">Diploma</span>
                    </div>
                </div>
                <div class="col-12 small-font" id="printform">
                    <div
                        class="p-3 border shadow d-flex flex-column card justify-content-center align-items-center mb-2"
                        v-for="(mtc, index) in milestoneCompHeader">
                        <div class="row w-100">
                            <div class="border p-2 col-12">
                                <div class="d-flex flex align-items-start">
                                    <span class="fw-bold">Course: &nbsp;</span>{{ mtc.arc_coursename }}
                                </div>
                            </div>
                            <div class="border p-2 col-4">
                                <div class="d-flex flex align-items-start">
                                    <span class="fw-bold">Grade Level: &nbsp;</span>{{ mtc.arc_yearlevel }}
                                </div>
                            </div>
                            <div class="border p-2 col-4">
                                <div class="d-flex flex align-items-start">
                                    <span class="fw-bold">Quarter: &nbsp;</span>{{ mtc.arc_semester }}
                                </div>
                            </div>
                            <div class="border p-2 col-4">
                                <div class="d-flex flex align-items-start">
                                    <span class="fw-bold">Curriculum Code: &nbsp;</span> {{
                                        mtc.arc_curriculum ? mtc.arc_curriculum : 'N/A' }}
                                </div>

                            </div>
                            <div class="border p-2 col-4">
                                <div class="d-flex flex align-items-start">
                                    <span class="fw-bold">Section: &nbsp;</span> {{ mtc.arc_section ?
                                        mtc.arc_section : 'N/A' }}
                                </div>

                            </div>
                            <div class="border p-2 col-4">
                                <div class="d-flex flex align-items-start">
                                    <span class="fw-bold">Date of Application: &nbsp;</span> {{
                                        mtc.arc_dateapplied }}
                                </div>
                            </div>
                            <div class="border p-2 col-4">
                                <div class="d-flex flex align-items-start">
                                    <span class="fw-bold">Student ID: &nbsp;</span> {{
                                        mtc.arc_studentid }}
                                </div>
                            </div>
                        </div>
                        <div class="row w-100 mt-3">
                            <div class="border p-2 col-12">
                                <div class="d-flex flex justify-content-center">
                                    <span class="fw-bold">Subjects Taken</span>
                                </div>
                            </div>
                        </div>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th class="border p-2">
                                        <span class="fw-bold">Code</span>
                                    </th>
                                    <th class="border p-2">
                                        <span class="fw-bold">Subjects Description</span>
                                    </th>
                                    <th class="border p-2">
                                        <span class="fw-bold">Lec</span>
                                    </th>
                                    <th class="border p-2">
                                        <span class="fw-bold">Lab</span>
                                    </th>
                                    <th class="border p-2">
                                        <span class="fw-bold">Total</span>
                                    </th>
                                    <th class="border p-2">
                                        <span class="fw-bold">Prelims</span>
                                    </th>
                                    <th class="border p-2">
                                        <span class="fw-bold">Midterms</span>
                                    </th>
                                    <th class="border p-2">
                                        <span class="fw-bold">Pre-Finals</span>
                                    </th>
                                    <th class="border p-2">
                                        <span class="fw-bold">Finals</span>
                                    </th>
                                    <th class="border p-2">
                                        <span class="fw-bold">Faculty</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="!Object.keys(milestoneCompData).length && !milestoneCompLoading">
                                    <td class="align-middle">
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
                                    <td class="align-middle">
                                        <span>{{ c.arc_lecture }}</span>
                                    </td>
                                    <td class="align-middle">
                                        <span>{{ c.arc_laboratory }}</span>
                                    </td>
                                    <td class="align-middle">
                                        <span class="fw-bold">{{ c.arc_lecture + c.arc_laboratory }}</span>
                                    </td>
                                    <td class="align-middle">
                                        <span class="text-primary">{{ c.arc_prelimgrade }}</span>
                                    </td>
                                    <td class="align-middle">
                                        <span class="text-primary">{{ c.arc_midtermgrade }}</span>
                                    </td>
                                    <td class="align-middle">
                                        <span class="text-primary">{{ c.arc_prefinalgrade }}</span>
                                    </td>
                                    <td class="align-middle">
                                        <span class="text-primary">{{ c.arc_finalgrade }}</span>
                                    </td>
                                    <td class="align-middle">
                                        <span class="text-primary">{{ c.arc_facultyname }}</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="border mt-3 p-2 rounded-2 d-flex gap-2 justify-content-end">
                        <button class="btn btn-sm btn-success" @click="downloadPdf()">Download Diploma</button>
                    </div>
                </div>
            </div>
            <div v-else class="">
                <span>No Milestone Found</span>
            </div>
        </div>
    </div>

</template>