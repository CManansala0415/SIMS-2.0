<script setup>
import { ref, onMounted, computed } from 'vue';
import {
    getOccupancy,
    getOccupancyOthers,
    getScheduledFaculty
} from "../../Fetchers.js";
import Loader from '../loaders/Loading1.vue';

const props = defineProps({
    launchData: {
    },
    buildingData: {
    },
    classroomData: {
    },
    timeData: {
    },
    headerData: {
    }
})

const classroom = computed(() => {
    return props.classroomData
});
const building = computed(() => {
    return props.buildingData
});
const launch = computed(() => {
    return props.launchData
});
const time = computed(() => {
    return props.timeData
});
const headers = computed(() => {
    return props.headerData
});


const buildingId = ref('')
const classroomId = ref('')
const othersId = ref('')
const filteredClassroom = ref([])
const mappedSched = ref([])
const occupancy = ref([])
const preloading = ref(false)

const filterClassroom = () => {
    classroomId.value = ''
    othersId.value = ''
    filteredClassroom.value = classroom.value.filter(e => {
        if (
            (buildingId.value == e.classr_bid)
        ) {
            return e
        }
    })
}

const filterOccupancy = () => {

    if ((buildingId.value && classroomId.value) && !othersId.value) {
        preloading.value = true
        occupancy.value = []
        mappedSched.value = []
        getOccupancy(buildingId.value, classroomId.value).then((results) => {
            occupancy.value = results
            mapSchedule()
        })
    } else if ((!buildingId.value && !classroomId.value) && othersId.value) {
        preloading.value = true
        occupancy.value = []
        mappedSched.value = []
        getOccupancyOthers(othersId.value).then((results) => {
            console.log(results)
            occupancy.value = results
            mapSchedule()
        })
    } else {
        // alert('Please select building and classroom or others')
        Swal.fire({
            title: "Requirement",
            text: "Please select building and classroom or others",
            icon: "question"
        })
    }



}

const mapSchedule = () => {
    mappedSched.value = time.value.map((e) => {
        let schedule = occupancy.value.findIndex(object => {
            return object.occ_time === e.timeid;
        });



        if (schedule !== -1) {
            let monFacultyName = ''
            let tueFacultyName = ''
            let wedFacultyName = ''
            let thursFacultyName = ''
            let friFacultyName = ''
            let satFacultyName = ''

            let monFaculty = faculty.value.findIndex(object => {
                return ((object.lf_lnid == launch.value.ln_id) && (object.lf_subjid == occupancy.value[schedule].mon_subj_id));
            });
            if (monFaculty !== -1) {
                monFacultyName = faculty.value[monFaculty].emp_firstname + ' ' + faculty.value[monFaculty].emp_lastname
            }

            let tueFaculty = faculty.value.findIndex(object => {
                return ((object.lf_lnid == launch.value.ln_id) && (object.lf_subjid == occupancy.value[schedule].tue_subj_id));
            });
            if (tueFaculty !== -1) {
                tueFacultyName = faculty.value[tueFaculty].emp_firstname + ' ' + faculty.value[tueFaculty].emp_lastname
            }

            let wedFaculty = faculty.value.findIndex(object => {
                return ((object.lf_lnid == launch.value.ln_id) && (object.lf_subjid == occupancy.value[schedule].wed_subj_id));
            });
            if (wedFaculty !== -1) {
                wedFacultyName = faculty.value[wedFaculty].emp_firstname + ' ' + faculty.value[wedFaculty].emp_lastname
            }

            let thursFaculty = faculty.value.findIndex(object => {
                return ((object.lf_lnid == launch.value.ln_id) && (object.lf_subjid == occupancy.value[schedule].thurs_subj_id));
            });
            if (thursFaculty !== -1) {
                thursFacultyName = faculty.value[thursFaculty].emp_firstname + ' ' + faculty.value[thursFaculty].emp_lastname
            }
            let friFaculty = faculty.value.findIndex(object => {
                return ((object.lf_lnid == launch.value.ln_id) && (object.lf_subjid == occupancy.value[schedule].fri_subj_id));
            });
            if (friFaculty !== -1) {
                friFacultyName = faculty.value[friFaculty].emp_firstname + ' ' + faculty.value[friFaculty].emp_lastname
            }

            let satFaculty = faculty.value.findIndex(object => {
                return ((object.lf_lnid == launch.value.ln_id) && (object.lf_subjid == occupancy.value[schedule].sat_subj_id));
            });
            if (satFaculty !== -1) {
                satFacultyName = faculty.value[satFaculty].emp_firstname + ' ' + faculty.value[satFaculty].emp_lastname
            }

            return {
                ...e,
                mon_faculty: monFacultyName,
                tue_faculty: tueFacultyName,
                wed_faculty: wedFacultyName,
                thurs_faculty: thursFacultyName,
                fri_faculty: friFacultyName,
                sat_faculty: satFacultyName,
                ...occupancy.value[schedule]
            }
        } else {
            return {
                ...e,
                mon_subj_code: '',
                mon_sec_code: '',
                mon_sec_name: '',
                mon_gradelvl_code: '',
                mon_gradelvl_name: '',
                tue_subj_code: '',
                tue_sec_code: '',
                tue_sec_name: '',
                tue_gradelvl_code: '',
                tue_gradelvl_name: '',
                wed_subj_code: '',
                wed_sec_code: '',
                wed_sec_name: '',
                wed_gradelvl_code: '',
                wed_gradelvl_name: '',
                thurs_subj_code: '',
                thurs_sec_code: '',
                thurs_sec_name: '',
                thurs_gradelvl_code: '',
                thurs_gradelvl_name: '',
                fri_subj_code: '',
                fri_sec_code: '',
                fri_sec_name: '',
                fri_gradelvl_code: '',
                fri_gradelvl_name: '',
                sat_subj_code: '',
                sat_sec_code: '',
                sat_sec_name: '',
                sat_gradelvl_code: '',
                sat_gradelvl_name: '',
            }
        }
    })
    preloading.value = false

}

const faculty = ref([])
onMounted(() => {
    getScheduledFaculty().then((results) => {
        faculty.value = results
    })
})



</script>
<template>
    <div class="border p-3">
        <div class="d-flex justify-content-around gap-1">
            <div class="w-100 d-flex gap-2 flex-column text-start">
                <span class="">Choose Building</span>
                <select @change="filterClassroom()"
                    class="form-control form-select-sm"
                    v-model="buildingId">
                    <option value="" disabled>
                        Select Here--
                    </option>
                    <option v-for="(bl, index) in building" :value="bl.buil_id">
                        {{ bl.buil_name }}
                    </option>
                </select>
            </div>
            <div class="w-100 d-flex gap-2 flex-column text-start">
                <span class="">Choose Classroom</span>
                <select
                    class="form-control form-select-sm"
                    v-model="classroomId" :disabled="buildingId ? false : true">
                    <option value="" disabled>
                        --Select Here--
                    </option>
                    <option v-for="(cl, index) in filteredClassroom" :value="cl.classr_id">
                        {{ cl.classr_name }}
                    </option>
                </select>
            </div>
            <!-- <div class="w-100 d-flex gap-2 flex-column text-start">
                <span class="">Choose Classroom</span>
                <select @change="buildingId = '', classroomId = ''" class="form-control form-select-sm"  
                        v-model="othersId">
                    <option value="" disabled>Select Here--</option>
                    <option value="90">Online Class</option>
                    <option value="91">Others</option>
                </select>
            </div> -->
            <div class="align-content-end w-25">
                <button @click="filterOccupancy"
                    class="btn btn-sm btn-dark w-100">
                    Load
                </button>
            </div>
        </div>

        <div>
            <div class="mt-3 overflow-auto">
                <table class="w-100 table-fixed table">
                    <thead>
                        <th v-for="(hd, index) in headers" class="p-3">{{ hd.title }}</th>
                    </thead>
                    <tbody>
                        <tr v-if="!Object.keys(mappedSched).length && !preloading">
                            <td class="p-5 text-center border" :colspan="Object.keys(headers).length">
                                <p class="text-danger fw-bold">No Schedule Found</p>
                                <p class="fs-6">Select building and classroom to access schedule list</p>
                            </td>
                        </tr>
                        <tr v-if="!Object.keys(mappedSched).length && preloading">
                            <td class="p-2 text-center border" :colspan="Object.keys(headers).length">
                                <Loader/>
                                <p class="">Loading schedules please wait...</p>
                            </td>
                        </tr>
                        <tr v-if="Object.keys(mappedSched).length && !preloading" v-for="(tm, index) in mappedSched" class="p-3">
                            <td :class="tm.classname">
                                <p class="p-2">{{ tm.timename }}</p>
                            </td>
                            <td :class="tm.classname" >
                                <div class="p-2 d-flex flex-column gap-1 text-start">
                                    <p class="fw-6">Subject: <span class="fst-italic fw-bold">{{ tm.mon_subj_code }}</span></p>
                                    <p class="fw-6">Section: <span class="fst-italic fw-bold">{{ tm.mon_sec_name }}</span></p>
                                    <p class="fw-6">Year: <span class="fst-italic fw-bold">{{ tm.mon_gradelvl_name }}</span></p>
                                    <!-- <p class="fw-6">Faculty: <span class="fst-italic fw-bold">{{ tm.mon_faculty }}</span></p> -->
                                </div>
                            </td>
                            <td :class="tm.classname" >
                                <div class="p-2 d-flex flex-column gap-1 text-start">
                                    <p class="fw-6">Subject: <span class="fst-italic fw-bold">{{ tm.tue_subj_code }}</span></p>
                                    <p class="fw-6">Section: <span class="fst-italic fw-bold">{{ tm.tue_sec_name }}</span></p>
                                    <p class="fw-6">Year: <span class="fst-italic fw-bold">{{ tm.tue_gradelvl_name }}</span></p>
                                    <!-- <p class="fw-6">Faculty: <span class="fst-italic fw-bold">{{ tm.tue_faculty }}</span></p> -->
                                </div>
                            </td>
                            <td :class="tm.classname" >
                                <div class="p-2 d-flex flex-column gap-1 text-start">
                                    <p class="fw-6">Subject: <span class="fst-italic fw-bold">{{ tm.wed_subj_code }}</span></p>
                                    <p class="fw-6">Section: <span class="fst-italic fw-bold">{{ tm.wed_sec_name }}</span></p>
                                    <p class="fw-6">Year: <span class="fst-italic fw-bold">{{ tm.wed_gradelvl_name }}</span></p>
                                    <!-- <p class="fw-6">Faculty: <span class="fst-italic fw-bold">{{ tm.wed_faculty }}</span></p> -->
                                </div>
                            </td>
                            <td :class="tm.classname" >
                                <div class="p-2 d-flex flex-column gap-1 text-start">
                                    <p class="fw-6">Subject: <span class="fst-italic fw-bold">{{ tm.thurs_subj_code }}</span></p>
                                    <p class="fw-6">Section: <span class="fst-italic fw-bold">{{ tm.thurs_sec_name }}</span></p>
                                    <p class="fw-6">Year: <span class="fst-italic fw-bold">{{ tm.thurs_gradelvl_name }}</span></p>
                                    <!-- <p class="fw-6">Faculty: <span class="fst-italic fw-bold">{{ tm.thurs_faculty }}</span></p> -->
                                </div>
                            </td>
                            <td :class="tm.classname" >
                                <div class="p-2 d-flex flex-column gap-1 text-start">
                                    <p class="fw-6">Subject: <span class="fst-italic fw-bold">{{ tm.fri_subj_code }}</span></p>
                                    <p class="fw-6">Section: <span class="fst-italic fw-bold">{{ tm.fri_sec_name }}</span></p>
                                    <p class="fw-6">Year: <span class="fst-italic fw-bold">{{ tm.fri_gradelvl_name }}</span></p>
                                    <!-- <p class="fw-6">Faculty: <span class="fst-italic fw-bold">{{ tm.fri_faculty }}</span></p> -->
                                </div>
                            </td>
                            <td :class="tm.classname" >
                                <div class="p-2 d-flex flex-column gap-1 text-start">
                                    <p class="fw-6">Subject: <span class="fst-italic fw-bold">{{ tm.sat_subj_code }}</span></p>
                                    <p class="fw-6">Section: <span class="fst-italic fw-bold">{{ tm.sat_sec_name }}</span></p>
                                    <p class="fw-6">Year: <span class="fst-italic fw-bold">{{ tm.sat_gradelvl_name }}</span></p>
                                    <!-- <p class="fw-6">Faculty: <span class="fst-italic fw-bold">{{ tm.sat_faculty }}</span></p> -->
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>