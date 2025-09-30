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
    getFacultyStudent,
    getAcademicDefaults
} from "../Fetchers.js";

import { useRouter, useRoute } from 'vue-router'

const assignment = ref([])
const assignmentCount = ref(0)
const preLoading = ref(true)
const userID = ref('')
const countIndex = ref('')
const router = useRouter();
const showForm = ref(false)
const showEnroll = ref(false)
const groupedAssignmentSection = ref([])
const groupedAssignmentSubject = ref([])
const groupedAssignmentStudent = ref([])
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
const emit = defineEmits(['fetchUser', 'doneLoading'])

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


const accessData = ref([])
onMounted(async () => {
    getUserID().then(async (results1) => {
        userID.value = results1.account.data.id
        accessData.value = results1.access
        emit('fetchUser', results1)
        try {
            await booter().then(() => {
                booting.value = 'Loading assignments...'
                bootingCount.value += 1
                getFacultyAssignment(results1.employee.emp_id).then((results2) => {
                    assignment.value = results2.data
                    assignmentCount.value = results2.count

                    groupedAssignmentSection.value = Object.groupBy(assignment.value, assignments => assignments.lf_lnid);
                    groupedAssignmentSubject.value = Object.groupBy(assignment.value, assignments => assignments.lf_subjid);

                    // kunin mga name ng keys to belooped para makuha yung sections course and gradelvl 
                    let keys = Object.keys(groupedAssignmentSection.value)
                    // console.log(groupedAssignmentSection.value)

                    keys.forEach((e) => {
                        let section = groupedAssignmentSection.value[e][0].ln_section
                        let course = groupedAssignmentSection.value[e][0].ln_course
                        let gradelvl = groupedAssignmentSection.value[e][0].ln_gradelvl

                        getFacultyStudent(section, gradelvl, course).then((results3) => {
                            groupedAssignmentStudent.value.push(results3)
                        })
                    })
                    preLoading.value = false
                    emit('doneLoading', false)
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
                emit('doneLoading', false)
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

const downloadExcel = () => {
    let table = document.getElementById('main-table')
    const wb = XLSX.utils.table_to_book(table, { sheet: 'sheet-1' });
    /* Export to file (start a download) */
    XLSX.writeFile(wb, 'MyTable.xlsx');
}


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
                        v-for="(app, index, count) in groupedAssignmentSection">
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
                                @click="showSubjects = true, countIndex = count">
                                <font-awesome-icon icon="fa-solid fa-eye" />
                                View Students
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
                    <h5 class="modal-title" id="staticBackdropLabel">Students</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        @click="showSubjects = false"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex flex-wrap flex-column">
                        <p class="text-success fw-bold">Students handled</p>
                        <p class=" fst-italic border p-3 rounded-3 bg-secondary-subtle small-font"><span
                                class="fw-bold">Note:
                            </span><span class="italic">Students handled for this section is listed below.
                                If the student does not included to the list provided by the official masterlist of the registrar,
                                verify the student enrollment to the registrar's office.
                            </span></p>
                    </div>
                    <div class="table-responsive border p-3 small-font">
                        <table class="table table-hover" style="text-transform:uppercase">
                            <thead>
                                <tr>
                                    <th style="background-color: #237a5b;" class="text-white">No.</th>
                                    <th style="background-color: #237a5b;" class="text-white">Student ID</th>
                                    <th style="background-color: #237a5b;" class="text-white">Student Type</th>
                                    <th style="background-color: #237a5b;" class="text-white">Student Name</th>
                                    <th style="background-color: #237a5b;" class="text-white">Profile</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(app, index2) in groupedAssignmentStudent[countIndex]">
                                    <td class="align-middle">
                                        {{ index2 + 1 }}
                                    </td>
                                    <td class="align-middle">
                                        {{ app.grad_dtypeid == 2? app.ident_identification : app.enr_lrn }}
                                    </td>
                                    <td class="align-middle">
                                        {{ app.grad_dtypeid == 2? 'College' : 'SHS' }}
                                    </td>
                                    <td class="align-middle">
                                        {{ app.per_lastname ? app.per_lastname : '' }}, {{ app.per_middlename ?
                                        app.per_middlename:'' }} {{ app.per_firstname ? app.per_firstname : '' }} {{
                                            app.per_suffixname? app.per_suffixname:'' }}
                                    </td>
                                    <td class="align-middle">
                                        <div class="flex items-center w-full justify-center">
                                            <label class="cursor-pointer border hover:border-teal-500">
                                                <img :src="app.per_profile ? 'http://localhost:8000/storage/profiles/' + app.per_profile : '/img/profile_default.png'"
                                                    class="object-fit border-2 border-gray-300" height="100px" width="100px" />
                                            </label>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="!preLoading && !Object.keys(groupedAssignmentStudent).length">
                                    <td class="align-middle" colspan="5">
                                        No Records Found
                                    </td>
                                </tr>
                                <tr v-if="preLoading && !Object.keys(groupedAssignmentStudent).length">
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