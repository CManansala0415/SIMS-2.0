<script setup>
import { ref, onMounted, computed } from 'vue';
import Loader from '../snippets/loaders/Loading1.vue';
// import Enroll from '../snippets/modal/Enrollment.vue';
// import ApplicationForm from './ApplicationForm.vue';
import { getUserID } from "../../routes/user";
import { useRouter, useRoute } from 'vue-router';
import {
    getFacultyAssignment,
    getGradelvl,
    getProgram,
    getQuarter,
    getDegree,
    getProgramList,
    getSemester,
    getSection,
} from "../Fetchers.js";


const assignment = ref([])
const assignmentCount = ref(0)
const preLoading = ref(false)
const userID = ref('')
const router = useRouter();
const showForm = ref(false)
const showEnroll = ref(false)
const groupedAssignmentSection = ref([])
const groupedAssignmentSubject = ref([])

const switcher = ref(0)
const quarter = ref([])
const gradelvl = ref([])
const degree = ref([])
const course = ref([])
const semester = ref([])
const section = ref([])
const booting = ref('')
const bootingCount = ref(0)
const booter = async () => {

    getGradelvl().then((results) => {
        gradelvl.value = results
        booting.value = 'Loading Grade Levels'
        bootingCount.value += 1
    })

    getProgram().then((results) => {
        degree.value = results
        booting.value = 'Loading Degrees'
        bootingCount.value += 1
    })

    getQuarter().then((results) => {
        quarter.value = results
        booting.value = 'Loading Quarters'
        bootingCount.value += 1
    })

    getDegree().then((results) => {
        course.value = results
        booting.value = 'Loading Courses'
        bootingCount.value += 1
    })

    getProgramList().then((results) => {
        course.value = results
        booting.value = 'Loading Courses'
        bootingCount.value += 1
    })

    getSemester().then((results) => {
        semester.value = results
        booting.value = 'Loading Semesters'
        bootingCount.value += 1
    })

    getSection().then((results) => {
        section.value = results
        booting.value = 'Loading Sections'
        bootingCount.value += 1
    })

}

onMounted(async () => {
    getUserID().then((results) => {
        userID.value = results.account.data.id
    }).catch((err) => {

    })


    try {
        preLoading.value = true
        await booter().then((results) => {
            booting.value = 'Loading assignments...'
            bootingCount.value += 1
            getFacultyAssignment(2).then((results) => {
                assignment.value = results.data
                assignmentCount.value = results.count
                preLoading.value = false

                let groupBySection = Object.groupBy(assignment.value, assignments => assignments.lf_lnid);
                let groupBySubject = Object.groupBy(assignment.value, assignments => assignments.lf_subjid);
                // console.log(groupBySection[2])
                // console.log(groupBySection)
                groupedAssignmentSection.value = groupBySection
                groupedAssignmentSubject.value = groupBySubject
                // console.log(groupBySection)

            })
        })

    } catch (err) {
        preLoading.value = false
        alert('error loading the list default components')
    }

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
            <div class="input-group w-50">
                <span class="input-group-text" id="searchaddon"><font-awesome-icon icon="fa-solid fa-search" /></span>
                <input type="text" class="form-control" placeholder="Search Here..." aria-label="search"
                    v-if="!showForm" v-model="searchValue" @keyup.enter="search()" aria-describedby="searchaddon"
                    :disabled="preLoading ? true : false">
            </div>
            <div class="d-flex w-50 justify-content-end gap-1">
                <button tabindex="-1" data-bs-toggle="modal" data-bs-target="#editdatamodal" @click="editData()"
                    type="button" class="btn btn-sm btn-primary w-25" :disabled="preLoading ? true : false">
                    <font-awesome-icon icon="fa-solid fa-add" /> Add New
                </button>
                <button @click="switcher = 0" tabindex="-1" type="button"
                    :class="switcher == 0 ? 'btn btn-sm btn-dark active w-25' : 'btn btn-sm btn-dark w-25'">
                    View Sections
                </button>
                <button @click="switcher = 1" tabindex="-1" type="button"
                    :class="switcher == 1 ? 'btn btn-sm btn-dark active w-25' : 'btn btn-sm btn-dark w-25'">
                    View Preparations
                </button>
            </div>
        </div>
        <div v-if="!showForm" class="table-responsive border p-3 small-font">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th style="background-color: #237a5b;" class="text-white">No.</th>
                        <th style="background-color: #237a5b;" class="text-white">Subject Code</th>
                        <th style="background-color: #237a5b;" class="text-white">Subject Name</th>
                        <th style="background-color: #237a5b;" class="text-white">Lecture Units</th>
                        <th style="background-color: #237a5b;" class="text-white">Laboratory Units</th>
                        <th style="background-color: #237a5b;" class="text-white">Total Units</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="!preLoading && Object.keys(groupedAssignmentSection).length" v-for="(app, index) in groupedAssignmentSection">
                        
                    </tr>
                    <tr v-if="!preLoading && !Object.keys(groupedAssignmentSection).length">
                        <td class="p-3 text-center" colspan="7">
                            No Records Found
                        </td>
                    </tr>
                    <tr v-if="preLoading && !Object.keys(groupedAssignmentSection).length">
                        <td class="p-3 text-center" colspan="7">
                            <div class="m-3">
                                <Loader />
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>