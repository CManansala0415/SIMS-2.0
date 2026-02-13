<script setup>
import { ref, onMounted, computed } from 'vue';
import {
    getOccupancy,
    getOccupancyOthers,
    getScheduledFaculty
} from "../../Fetchers.js";
import NeuLoader2 from '../loaders/NeuLoader2.vue';

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
        <div class="d-flex justify-content-around gap-1 neu-card p-3 small-font">
            <div class="w-100 d-flex gap-2 flex-column text-start">
                <span class="">Choose Building</span>
                <select @change="filterClassroom()" class="neu-input neu-select" v-model="buildingId">
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
                <select class="neu-input neu-select" v-model="classroomId" :disabled="buildingId ? false : true">
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
                <select @change="buildingId = '', classroomId = ''" class="neu-input neu-select"  
                        v-model="othersId">
                    <option value="" disabled>Select Here--</option>
                    <option value="90">Online Class</option>
                    <option value="91">Others</option>
                </select>
            </div> -->
            <div class="w-50 d-flex justify-content-center align-items-end">
                <button @click="filterOccupancy" class="neu-btn neu-dark-gray p-2">
                    <font-awesome-icon icon="fa-solid fa-magnifying-glass"/> Load
                </button>
            </div>
        </div>

        <div>
            <div class="mt-3 overflow-auto text-dim">
                <div class="schedule-calendar p-3 border rounded-3 neu-card-inner">

                    <!-- Empty or Loading -->
                    <div v-if="!Object.keys(mappedSched).length && !preloading" class="text-center py-5">
                        <p class="text-danger fw-bold fs-5 mb-1">No Schedule Found</p>
                        <p class="text-muted">Select a building and classroom to access schedule list.</p>
                    </div>

                    <div v-if="!Object.keys(mappedSched).length && preloading" class="text-center py-5">
                        <NeuLoader2 />
                    </div>

                    <!-- Calendar Grid -->
                    <div v-if="Object.keys(mappedSched).length && !preloading" class="calendar-grid small-font">
                        <!-- Calendar Header -->
                        <div class="calendar-header">
                            <!-- <div class="time-header"></div> -->
                            <div v-for="(hd, idx) in headers" :key="idx" class="day-header text-center fw-bold">
                                {{ hd.title }}
                            </div>
                        </div>

                        <!-- Calendar Body -->
                        <div v-for="(tm, index) in mappedSched" :key="index" class="calendar-row align-items-stretch">
                            <!-- Time Slot Label -->
                            <div class="time-cell fw-semibold d-flex align-items-center" style="font-size: 11px;">{{ tm.timename }} {{ tm.daytime }}</div>

                            <!-- Monday -->
                            <div class="day-cell" :class="tm.classname">
                                <div v-if="tm.mon_subj_code" class="sched-tile neu-pastel-grass">
                                    <div class="fw-bold text-primary small">{{ tm.mon_subj_code }}</div>
                                    <div class="small text-dark">{{ tm.mon_sec_name }}</div>
                                    <div class="small text-muted">{{ tm.mon_faculty }}</div>
                                </div>
                                <div v-else class="empty-tile text-muted small">—</div>
                            </div>

                            <!-- Tuesday -->
                            <div class="day-cell" :class="tm.classname">
                                <div v-if="tm.tue_subj_code" class="sched-tile neu-pastel-mint">
                                    <div class="fw-bold text-success small">{{ tm.tue_subj_code }}</div>
                                    <div class="small text-dark">{{ tm.tue_sec_name }}</div>
                                    <div class="small text-muted">{{ tm.tue_faculty }}</div>
                                </div>
                                <div v-else class="empty-tile text-muted small">—</div>
                            </div>

                            <!-- Wednesday -->
                            <div class="day-cell" :class="tm.classname">
                                <div v-if="tm.wed_subj_code" class="sched-tile neu-pastel-sky">
                                    <div class="fw-bold text-info small">{{ tm.wed_subj_code }}</div>
                                    <div class="small text-dark">{{ tm.wed_sec_name }}</div>
                                    <div class="small text-muted">{{ tm.wed_faculty }}</div>
                                </div>
                                <div v-else class="empty-tile text-muted small">—</div>
                            </div>

                            <!-- Thursday -->
                            <div class="day-cell" :class="tm.classname">
                                <div v-if="tm.thurs_subj_code" class="sched-tile neu-pastel-blue">
                                    <div class="fw-bold text-warning small">{{ tm.thurs_subj_code }}</div>
                                    <div class="small text-dark">{{ tm.thurs_sec_name }}</div>
                                    <div class="small text-muted">{{ tm.thurs_faculty }}</div>
                                </div>
                                <div v-else class="empty-tile text-muted small">—</div>
                            </div>

                            <!-- Friday -->
                            <div class="day-cell" :class="tm.classname">
                                <div v-if="tm.fri_subj_code" class="sched-tile neu-pastel-purple">
                                    <div class="fw-bold text-danger small">{{ tm.fri_subj_code }}</div>
                                    <div class="small text-dark">{{ tm.fri_sec_name }}</div>
                                    <div class="small text-muted">{{ tm.fri_faculty }}</div>
                                </div>
                                <div v-else class="empty-tile text-muted small">—</div>
                            </div>

                            <!-- Saturday -->
                            <div class="day-cell" :class="tm.classname">
                                <div v-if="tm.sat_subj_code" class="sched-tile neu-pastel-pink">
                                    <div class="fw-bold text-secondary small">{{ tm.sat_subj_code }}</div>
                                    <div class="small text-dark">{{ tm.sat_sec_name }}</div>
                                    <div class="small text-muted">{{ tm.sat_faculty }}</div>
                                </div>
                                <div v-else class="empty-tile text-muted small">—</div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</template>
<style scoped>
.calendar-grid {
    display: flex;
    flex-direction: column;
    width: 100%;
}

.calendar-header,
.calendar-row {
    display: grid;
    grid-template-columns: 120px repeat(6, 1fr);
    align-items: stretch;
}

.calendar-header {
    /* background: #f8f9fa; */
    border-bottom: 2px solid #dee2e6;
}

.time-header {
    border-right: 2px solid #dee2e6;
}

.time-cell {
    padding: 0.75rem;
    border-right: 2px solid #dee2e6;
    border-bottom: 1px solid #e9ecef;
}

.day-header {
    padding: 0.75rem;
    border-right: 1px solid #dee2e6;
    text-transform: uppercase;
    font-size: 0.8rem;
    /* background-color: #fdfdfd; */
}

.day-cell {
    padding: 0.5rem;
    border-right: 1px solid #e9ecef;
    border-bottom: 1px solid #e9ecef;
    min-height: 80px;
    position: relative;
}

.sched-tile {
   border-radius: 10px;
    padding: 10px 12px;
    width: 95%;
    margin: 0 auto;
    box-shadow: 
      2px 2px 7px rgba(119, 119, 119, 0.6),
        -2px -2px 10px rgba(255, 255, 255, 0.6);
    cursor: pointer;
    transition: transform 0.12s ease, box-shadow 0.12s ease;
}

.sched-tile:hover {
    transform: translateY(-1px);
    box-shadow: 
      inset 2px 2px 7px rgba(119, 119, 119, 0.6),
        inset -2px -2px 10px rgba(255, 255, 255, 0.6);
}

.empty-tile {
    text-align: center;
    /* line-height: 60px; */
}
</style>