<script setup>
import { ref, onMounted, computed } from 'vue';
import { getUserID } from "../../routes/user";
import {
    getBuilding,
    getClassroom,
    getLaunch,
    getProgram,
    getQuarter,
    getProgramList,
    getSection,
    getGradelvl,
    getCurriculum,
    getCurriculumStudent,
    getCurriculumSubject,
    getAcademicDefaults
} from "../Fetchers.js";
import Loader from '../snippets/loaders/Loading1.vue';

import LaunchSemesterModal from '../snippets/modal/LaunchSemesterModal.vue';
import LaunchScheduleModal from '../snippets/modal/LaunchScheduleModal.vue';
import { useRouter, useRoute } from 'vue-router'

const router = useRouter();
const preLoading = ref(true)
const bootingCount = ref(0)
const booting = ref('')
const limit = ref(10)
const offset = ref(0)
const classroomCount = ref(0)
const searchValue = ref('')
const showForm = ref(false)
const userID = ref('')


const building = ref([])
const degree = ref([])
const semester = ref([])
const classroom = ref([])
const course = ref([])
const section = ref([])
const gradelvl = ref([])
const launch = ref([])
const launchCount = ref([])
const sched = ref(false)
const launchData = ref([])
const curriculum = ref([])
const emit = defineEmits(['fetchUser', 'doneLoading'])
const accessData = ref([])
const booter = async () => {

    getCurriculum(0, 0).then((results) => {
        curriculum.value = results
        booting.value = 'Loading Curriculums...'
        bootingCount.value += 1
    })
    // getBuilding().then((results) => {
    //     building.value = results
    //     booting.value = 'Loading Buildings...'
    //     bootingCount.value += 1

    // })
    // getClassroom().then((results) => {
    //     classroom.value = results
    //     booting.value = 'Loading Classrooms...'
    //     bootingCount.value += 1
    // })
    // getProgram().then((results) => {
    //     degree.value = results
    //     booting.value = 'Loading Degrees...'
    //     bootingCount.value += 1
    // })
    // getQuarter().then((results) => {
    //     semester.value = results
    //     booting.value = 'Loading Degrees...'
    //     bootingCount.value += 1
    // })
    // getProgramList().then((results) => {
    //     course.value = results
    //     booting.value = 'Loading Degrees...'
    //     bootingCount.value += 1
    // })
    // getSection().then((results) => {
    //     section.value = results
    //     booting.value = 'Loading Degrees...'
    //     bootingCount.value += 1
    // })
    // getGradelvl().then((results) => {
    //     gradelvl.value = results
    //     booting.value = 'Loading Degrees...'
    //     bootingCount.value += 1
    // })

    getAcademicDefaults().then((results) => {
        gradelvl.value = results.gradelvl
        degree.value = results.program
        // quarter.value = results.quarter
        course.value = results.course
        semester.value = results.quarter
        section.value = results.section
        building.value = results.building
        classroom.value = results.classroom
        booting.value = 'Loading Academic Information'
        bootingCount.value += 1
    })

}
const showSched = (data) => {
    launchData.value = data
    sched.value = !sched.value
}

const paginate = (mode) => {
    switch (mode) {
        case 'prev':
            if (offset.value <= 0) {
                offset.value = 0
            } else {
                launch.value = []
                offset.value -= 10
                launchCount.value = 0
                preLoading.value = true
                getLaunch(limit.value, offset.value).then((results) => {
                    launch.value = results.data
                    launchCount.value = results.count
                    preLoading.value = false
                })
            }
            break;
        case 'next':

            if (offset.value >= launchCount.value) {
                offset.value = launchCount.value
            } else {
                launch.value = []
                offset.value += 10
                launchCount.value = 0
                preLoading.value = true
                getLaunch(limit.value, offset.value, null).then((results) => {
                    launch.value = results.data
                    launchCount.value = results.count
                    preLoading.value = false
                })
            }
            break;
        case 'search':
            searchValue.value = searchValue.value.trim()
            if (searchValue.value || searchValue.value == '') {
                launch.value = []
                offset.value = 0
                launchCount.value = 0
                preLoading.value = true
                getLaunch(limit.value, offset.value, searchValue.value).then((results) => {
                    launch.value = results.data
                    launchCount.value = results.count
                    preLoading.value = false
                }).catch((err) => {
                    // console.log(err)
                })
            } else {
                // alert('Please search a valid record')
                // preLoading.value = false
                Swal.fire({
                    title: "Search Failed",
                    text: "Please search a valid record",
                    icon: "error"
                }).then(()=>{
                    preLoading.value = false
                });
            }
            break;

    }
}

const search = () => {
    paginate('search')
}

onMounted(async () => {
    window.stop()
    getUserID().then(async(results1) => {
        // user.value = results1.account.data.name
        userID.value = results1.account.data.id
        emit('fetchUser', results1)
        accessData.value = results1.access
        try {
            preLoading.value = true
            await booter().then((results) => {
                booting.value = 'Loading Launched Semesters...'
                bootingCount.value += 1
                getLaunch(limit.value, offset.value).then((results2) => {
                    launch.value = results2.data
                    launchCount.value = results2.count
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
</script>
<template>
    <div class="p-3 mb-4 border-bottom">
        <h5 class=" text-uppercase fw-bold">Semester Launch</h5>
    </div>

    <div v-if="sched">
        <LaunchScheduleModal  :launchData="launchData" :buildingData="building" :classroomData="classroom" @close-sched="showSched()"/>
    </div>
    <div v-else>
        <div class="p-1 d-flex gap-2 justify-content-between mb-3">
            <div class="input-group w-50">
                <span class="input-group-text" id="searchaddon"><font-awesome-icon icon="fa-solid fa-search" /></span>
                <input type="text" class="form-control" placeholder="Search Here..." aria-label="search"
                    v-model="searchValue" @keyup.enter="search()" aria-describedby="searchaddon"
                    :disabled="preLoading ? true : false">
            </div>
            <div class="d-flex flex-wrap w-50 justify-content-end">
                <button tabindex="-1" data-bs-toggle="modal" data-bs-target="#adddatamodal"
                    @click="showForm = !showForm" type="button" class="btn btn-sm btn-primary"
                    :disabled="preLoading ? true : false">
                    <font-awesome-icon icon="fa-solid fa-add" /> Add New
                </button>
            </div>
        </div>

        <div class="table-responsive border p-3 small-font">
            <table class="table table-hover">
                <thead>
                    <tr>

                        <th style="background-color: #237a5b;" class="text-white">S.Y</th>
                        <th style="background-color: #237a5b;" class="text-white">Semester</th>
                        <th style="background-color: #237a5b;" class="text-white">Degree</th>
                        <th style="background-color: #237a5b;" class="text-white">Course</th>
                        <th style="background-color: #237a5b;" class="text-white">Section</th>
                        <th style="background-color: #237a5b;" class="text-white">Grade Level</th>
                        <th style="background-color: #237a5b;" class="text-white">Slots</th>
                        <th style="background-color: #237a5b;" class="text-white">Commands</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="!preLoading && Object.keys(launch).length" v-for="(ln, index) in launch">
                        <td class="align-middle">
                            {{ ln.ln_year }}
                        </td>
                        <td class="align-middle">
                            {{ ln.quar_desc }}
                        </td>
                        <td class="align-middle">
                            {{ ln.dtype_desc }}
                        </td>
                        <td class="align-middle">
                            {{ ln.prog_name }}
                        </td>
                        <td class="align-middle">
                            {{ ln.sec_name }}
                        </td>
                        <td class="align-middle">
                            {{ ln.grad_name }}
                        </td>
                        <td class="align-middle">
                            {{ ln.ln_slots }}
                        </td>
                        <td v-if="accessData[3].useracc_modifying == 1" class="align-middle">
                            <div class="d-flex gap-2 justify-content-center">
                                <button 
                                    @click="showSched(launch[index])" type="button" title="Edit Record"
                                    class="btn btn-secondary btn-sm">
                                    <font-awesome-icon icon="fa-solid fa-pen" /></button>
                            </div>
                        </td>
                        <td v-else class="align-middle">
                            N/A
                        </td>
                    </tr>
                    <tr v-if="!preLoading && !Object.keys(launch).length">
                        <td class="p-3 text-center" colspan="8">
                            No Records Found
                        </td>
                    </tr>
                    <tr v-if="preLoading && !Object.keys(launch).length">
                        <td class="p-3 text-center" colspan="8">
                            <div class="m-3">
                                <Loader />
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="d-flex justify-content-between align-content-center" v-if="!preLoading">
                <div class="d-flex gap-1">
                    <button :disabled="offset == 0 ? true : false" @click="paginate('prev')"
                        class="btn btn-sm btn-secondary">Prev</button>
                    <button :disabled="Object.keys(launch).length < 10 ? true : false" @click="paginate('next')"
                        class="btn btn-sm btn-secondary">Next</button>
                </div>
                <p class="">showing total of <span class="font-semibold">({{ launchCount }})</span> items</p>
            </div>
        </div>
    </div>


    <!-- Launch Modal -->
    <div class="modal fade" id="adddatamodal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Semester Launch</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        @click="showForm = false"></button>
                </div>
                <div class="modal-body">
                    <LaunchSemesterModal v-if="showForm" :degreeData="degree" :semesterData="semester"
                        :courseData="course" :sectionData="section" :gradelvlData="gradelvl"
                        :curriculumData="curriculum" />
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <div class="form-group">
                        <small id="emailHelp" class="form-text text-muted">We'll never share your personal information
                            with anyone
                            else (Data Privacy Act of 2012)</small>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                            @click="showForm = false">Close</button>
                        <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
                    </div>
                </div>
            </div>
        </div>
    </div>

</template>