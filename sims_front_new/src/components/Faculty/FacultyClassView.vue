<script setup>
import { ref, onMounted, computed } from 'vue';
import Loader from '../snippets/loaders/Loading1.vue';
// import Enroll from '../snippets/modal/Enrollment.vue';
// import ApplicationForm from './ApplicationForm.vue';
import { getUserID } from "../../routes/user.js";
import {
    getFacultyAssignment,
    getGradelvl,
    getProgram,
    getQuarter,
    getDegree,
    getProgramList,
    getSemester,
    getSection,
    getAcademicDefaults
} from "../Fetchers.js";

import { useRouter, useRoute } from 'vue-router'

const router = useRouter();
const assignment = ref([])
const assignmentCount = ref(0)
const preLoading = ref(true)
const userID = ref('')
const showForm = ref(false)
const showSubjects = ref(false)
const indexerId = ref('')
const groupedAssignmentSection = ref([])
const groupedAssignmentSubject = ref([])
const switcher = ref(0)
const quarter = ref([])
const gradelvl = ref([])
const degree = ref([])
const course = ref([])
const dtype = ref([])
const semester = ref([])
const section = ref([])
const booting = ref('')
const bootingCount = ref(0)
const emit = defineEmits(['fetchUser'])

const booter = async () => {

    // getGradelvl().then((results) => {
    //     gradelvl.value = results
    //     booting.value = 'Loading Grade Levels'
    //     bootingCount.value += 1
    // })

    // getProgram().then((results) => {
    //     degree.value = results
    //     booting.value = 'Loading Degrees'
    //     bootingCount.value += 1
    // })

    // getQuarter().then((results) => {
    //     quarter.value = results
    //     booting.value = 'Loading Quarters'
    //     bootingCount.value += 1
    // })

    getDegree().then((results) => {
        dtype.value = results
        booting.value = 'Loading Courses'
        bootingCount.value += 1
    })

    // getProgramList().then((results) => {
    //     course.value = results
    //     booting.value = 'Loading Courses'
    //     bootingCount.value += 1
    // })

    // getSemester().then((results) => {
    //     semester.value = results
    //     booting.value = 'Loading Semesters'
    //     bootingCount.value += 1
    // })

    // getSection().then((results) => {
    //     section.value = results
    //     booting.value = 'Loading Sections'
    //     bootingCount.value += 1
    // })

    getAcademicDefaults().then((results) => {
        gradelvl.value = results.gradelvl
        degree.value = results.program
        quarter.value = results.quarter
        course.value = results.course
        semester.value = results.semester
        section.value = results.section
        booting.value = 'Loading Academic Information'
        bootingCount.value += 1
    })

}

onMounted(async () => {
    getUserID().then(async (results1) => {
        userID.value = results1.account.data.id
        emit('fetchUser', results1)
        try {
            preLoading.value = true
            await booter().then(() => {
                booting.value = 'Loading assignments...'
                bootingCount.value += 1
                getFacultyAssignment(results1.employee.emp_id).then((results2) => {
                    assignment.value = results2.data
                    assignmentCount.value = results2.count
                    let groupBySection = Object.groupBy(assignment.value, assignments => assignments.lf_lnid);
                    let groupBySubject = Object.groupBy(assignment.value, assignments => assignments.lf_subjid);
                    // console.log(groupBySection[2])
                    // console.log(groupBySection)
                    groupedAssignmentSection.value = groupBySection
                    groupedAssignmentSubject.value = groupBySubject
                    // console.log(groupedAssignmentSection.value)
                    // console.log(groupedAssignmentSubject.value)
                    preLoading.value = false
                })
            })

        } catch (err) {
            // preLoading.value = false
            // alert('error loading the list default components')
            Swal.fire({
                icon: "error",
                title: "Oops...",
                text: "Something went wrong!",
                footer: '<a href="#" disabled>Have you checked your internet connection?</a>'
            }).then(()=>{
                preLoading.value = false
            });
        }
    }).catch((err) => {
        // alert('Unauthorized Session, Please Log In')
        Swal.fire({
            icon: "error",
            title: "Oops...",
            text: "Session expired, log in again",
        }).then(()=>{
            router.push("/");
            window.stop()
        });
    })
})


// const switchPage = () =>{
//   switch(switcher.value){
//     case 0:

//     break;
//   }
// }


</script>
<template>
    <div>
        <div class="p-3 mb-4 border-bottom">
            <h5 class=" text-uppercase fw-bold">Faculty Loads</h5>
        </div>

        <div class="p-1 d-flex gap-2 justify-content-between mb-3">
            <!-- <div class="input-group w-50">
                <span class="input-group-text" id="searchaddon"><font-awesome-icon icon="fa-solid fa-search" /></span>
                <input type="text" class="form-control" placeholder="Search Here..." aria-label="search"
                    v-if="!showForm" v-model="searchValue" @keyup.enter="search(1)" aria-describedby="searchaddon"
                    :disabled="preLoading ? true : false">
            </div> -->
            <div class="d-flex w-100 justify-content-start gap-1">
                <!-- <button tabindex="-1" data-bs-toggle="modal" data-bs-target="#editdatamodal" @click="editData()"
                    type="button" class="btn btn-sm btn-primary w-25" :disabled="preLoading ? true : false">
                    <font-awesome-icon icon="fa-solid fa-add" /> Add New
                </button>
                <button @click="switcher = 0" tabindex="-1" type="button" :disabled="preLoading ? true : false"
                    :class="switcher == 0 ? 'btn btn-sm btn-dark active w-25' : 'btn btn-sm btn-dark w-25'">
                    Sections
                </button>
                <button @click="switcher = 1" tabindex="-1" type="button" :disabled="preLoading ? true : false"
                    :class="switcher == 1 ? 'btn btn-sm btn-dark active w-25' : 'btn btn-sm btn-dark w-25'">
                    Preparations
                </button> -->
                <!-- <select v-model="switcher" class="form-select w-25" :disabled="preLoading ? true : false">
                    <option value="0">Sections</option>
                    <option value="1">Preparations</option>
                </select> -->
            </div>
        </div>
        <div v-if="!showForm" class="table-responsive border p-3 small-font">
            <table v-if="switcher == 0" class="table table-hover">
                <thead>
                    <tr>
                        <th style="background-color: #237a5b;" class="text-white">No.</th>
                        <th style="background-color: #237a5b;" class="text-white">Year</th>
                        <th style="background-color: #237a5b;" class="text-white">Course</th>
                        <th style="background-color: #237a5b;" class="text-white">Section Name</th>
                        <th style="background-color: #237a5b;" class="text-white">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="!preLoading && Object.keys(groupedAssignmentSection).length"
                        v-for="(app, index) in groupedAssignmentSection">
                        <td class="align-middle">
                            {{ index }}
                        </td>
                        <td class="align-middle">
                            <select :value="groupedAssignmentSection[index][0].ln_gradelvl" disabled
                                class="form-select-sm" title="Click Edit to modify details">
                                <option v-for="(gr, index) in gradelvl" :value="gr.grad_id">{{ gr.grad_name }}</option>
                            </select>
                        </td>
                        <td class="align-middle">
                            {{ groupedAssignmentSection[index][0].prog_name }}
                        </td>
                        <td class="align-middle">
                            {{ groupedAssignmentSection[index][0].sec_name }}
                        </td>
                        <td class="align-middle">
                            <button class="btn btn-sm btn-secondary" data-bs-toggle="modal"
                                data-bs-target="#editdatamodal" title="Medical Form / Checkup"
                                @click="showSubjects = true, indexerId = index">
                                <font-awesome-icon icon="fa-solid fa-eye" />
                                View Subjects
                            </button>
                        </td>
                    </tr>
                    <tr v-if="!preLoading && !Object.keys(groupedAssignmentSection).length">
                        <td class="align-middle" colspan="5">
                            No Records Found
                        </td>
                    </tr>
                    <tr v-if="preLoading && !Object.keys(groupedAssignmentSection).length">
                        <td class="align-middle" colspan="5">
                            <div class="m-3">
                                <Loader />
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
            <table v-if="switcher == 1" class="table table-hover">
                <thead>
                    <tr>
                        <th style="background-color: #237a5b;" class="text-white">No.</th>
                        <th style="background-color: #237a5b;" class="text-white">Year</th>
                        <th style="background-color: #237a5b;" class="text-white">Course</th>
                        <th style="background-color: #237a5b;" class="text-white">Section Name</th>
                        <th style="background-color: #237a5b;" class="text-white">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="!preLoading && Object.keys(groupedAssignmentSection).length"
                        v-for="(app, index) in groupedAssignmentSection">
                        <td class="align-middle">
                            {{ index }}
                        </td>
                        <td class="align-middle">
                            <select :value="groupedAssignmentSection[index][0].ln_gradelvl" disabled
                                class="form-select-sm" title="Click Edit to modify details">
                                <option v-for="(gr, index) in gradelvl" :value="gr.grad_id">{{ gr.grad_name }}</option>
                            </select>
                        </td>
                        <td class="align-middle">
                            {{ groupedAssignmentSection[index][0].prog_name }}
                        </td>
                        <td class="align-middle">
                            {{ groupedAssignmentSection[index][0].sec_name }}
                        </td>
                        <td class="align-middle">
                            <button class="btn btn-sm btn-secondary" data-bs-toggle="modal"
                                data-bs-target="#editdatamodal" title="Medical Form / Checkup"
                                @click="showSubjects = true, indexerId = index">
                                <font-awesome-icon icon="fa-solid fa-eye" />
                                View Subjects
                            </button>
                        </td>
                    </tr>
                    <tr v-if="!preLoading && !Object.keys(groupedAssignmentSection).length">
                        <td class="align-middle" colspan="5">
                            No Records Found
                        </td>
                    </tr>
                    <tr v-if="preLoading && !Object.keys(groupedAssignmentSection).length">
                        <td class="align-middle" colspan="5">
                            <div class="m-3">
                                <Loader />
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Edit Medical Modal -->
    <div class="modal fade" id="editdatamodal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Subjects</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        @click="showSubjects = false"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex flex-wrap flex-column">
                        <p class="text-success fw-bold">Subjects handled</p>
                        <p class=" fst-italic border p-3 rounded-3 bg-secondary-subtle small-font"><span
                                class="fw-bold">Note:
                            </span><span class="italic">Subjects handled for this section is listed below. 
                                If subjects does not included to the loadings agreed by the management, 
                                verify the subject to the assigned personnel and request for update.
                            </span></p>
                    </div>
                    <div class="table-responsive border p-3 small-font">
                        <table class="table table-hover">
                            <!-- [index] = index nung group, [0] = yung first item na nasa group -->
                                <thead>
                                    <tr>
                                        <th style="background-color: #237a5b;" class="text-white">No.</th>
                                        <th style="background-color: #237a5b;" class="text-white">Subject Code</th>
                                        <th style="background-color: #237a5b;" class="text-white">Subject Name</th>
                                        <th style="background-color: #237a5b;" class="text-white">Units</th>
                                        <th style="background-color: #237a5b;" class="text-white">Total Units</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(app, index) in groupedAssignmentSection[indexerId]">
                                        <td class="align-middle">
                                            {{ index + 1 }}
                                        </td>
                                        <td class="align-middle">
                                            {{ app.subj_code }}
                                        </td>
                                        <td class="align-middle">
                                            {{ app.subj_name }}
                                        </td>
                                        <td class="align-middle">
                                            <div class="input-group input-group-sm mb-1">
                                                <span class=" input-group-text">Lecture Units</span>
                                                <input v-model="app.subj_lec" type="text" class="form-control" disabled>
                                            </div>
                                            <div class="input-group input-group-sm mb-1">
                                                <span class=" input-group-text">Laboratory Units</span>
                                                <input v-model="app.subj_lab" type="text" class="form-control" disabled>
                                            </div>
                                        </td>
                                        <td class="align-middle">
                                            {{ app.subj_lec + app.subj_lab }}
                                        </td>
                                        <!-- <td class="align-middle">
                          {{ app.lf_lnid }}
                        </td> -->
                                    </tr>
                                    <tr v-if="!Object.keys(groupedAssignmentSection).length">
                                        <td class="align-middle" colspan="4">
                                            No Records Found
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                    </div>

                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <div class="form-group">
                        <small id="emailHelp" class="form-text text-muted">We'll never share your personal information
                            with anyone
                            else (Data Privacy Act of 2012)</small>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                            @click="showSubjects = false">Close</button>
                        <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>