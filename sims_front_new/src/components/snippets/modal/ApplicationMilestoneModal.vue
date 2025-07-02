<script setup>
import { ref, onMounted, computed } from 'vue';
import { getUserID } from "../../../routes/user";
import { getCurriculumSubject, getEnrollment, getMilestone, addMilestone, updateEnrollment, updateMilestone, getCommandUpdateCurriculum, getAcademicStatus, getArchiveMerge } from "../../Fetchers.js";
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


const userID = ref('')
const preloading = ref(true)
const milestoneLoading = ref(true)

const milestoneCompData = ref([])
const milestoneCompHeader = ref([])
const milestoneCompLoading = ref(true)

onMounted(async () => {
    window.stop()
    try {
        console.log(studentData.value)
        getArchiveMerge(studentData.value.per_id).then((results) => {
            // milestoneCompData.value = results
            milestoneCompLoading.value = false
            // group nate yung archive para makapag create ng headers to be looped
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


</script>

<template>
    <div class="d-flex flex-wrap w-100 ">
        <div v-if="preloading" class="d-flex w-100 mt-4 justify-content-center align-content-center">
            <Loader />
        </div>
        <div v-else>

            <div>
                <div class="container overflow-hidden p-3">
                    <div v-if="Object.keys(milestoneCompHeader).length" class="row gy-2 gx-2">
                        <div class="col-12 border-bottom">
                            <div class="p-3">
                                <span class="fw-bold border bg-primary text-white p-2 rounded-3">MILESTONE</span>
                            </div>
                        </div>
                        <div class="col-12 small-font">
                            <div class="p-3 border shadow d-flex flex-column card justify-content-center align-items-center mb-2"
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
                        </div>
                    </div>
                    <div v-else class="">
                        <span>No Milestone Found</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

</template>