<script setup>
import { ref, onMounted, computed } from 'vue';
import {
    getOccupancy,
    getCurriculumSubject,
    getScheduledFaculty,
    getFacultyAvailability
} from "../../Fetchers.js";
import Loader from '../loaders/Loading1.vue';
const props = defineProps({
    subjectData: {
    },
    timeidData: {
    },
    timedayData: {
    },
    subjidData: {
    },
    buildingData: {
    },
    classroomData: {
    },
    buildingIdData: {
    },
    classroomIdData: {
    },
    curriculumData: {
    },
    curriculumIdData: {
    },
    launchIdData: {
    },
    launchdata: {
    }

})

const launch = computed(() => {
    return props.launchdata
});

const subject = computed(() => {
    return props.subjectData
});

const timeId = computed(() => {
    return props.timeidData
});

const timeDay = computed(() => {
    return props.timedayData
});

const subjId = computed(() => {
    return props.subjidData
});

const classroom = computed(() => {
    return props.classroomData
});

const building = computed(() => {
    return props.buildingData
});

const curriculum = computed(() => {
    return props.curriculumData
});

const currId = computed(() => {
    return props.curriculumIdData
});

const bid = computed(() => {
    return props.buildingIdData
});

const classrid = computed(() => {
    return props.classroomIdData
});

const launchId = computed(() => {
    return props.launchIdData
});

 
const emit = defineEmits(['close-modal', 'assign-sched'])
const assign = (id, name, bldg, classr, remove) => {
    // alert('Successful')
    // emit('assign-sched', id, name, bldg, classr, remove, facultyId.value)
    Swal.fire({
        title: "Update Successful",
        text: "Changes applied",
        icon: "success"
    }).then(()=>{
        emit('assign-sched', id, name, bldg, classr, remove, facultyId.value)
    });
}

const activeID = ref('')
const activeName = ref('')
const curriculumId = ref('')

const buildingId = ref('')
const facultyId = ref('')
const classroomId = ref('')
const searchValue = ref('')
const filteredClassroom = ref([])
const occupancy = ref([])
const loading = ref(false)

const filteredSubject = ref([])
const availability = ref([])
const curriculumSubject = ref([])
const faculty = ref([])
const occupier = ref({
    subj_code: '',
    sec_name: '',
    gradelvl_name: '',
})
const occupied = ref(false)
const checking = ref(false)

const filterClassroom = () => {
    classroomId.value = ''
    filteredClassroom.value = classroom.value.filter(e => {
        if (
            (buildingId.value == e.classr_bid)
        ) {
            return e
        }
    })
}


const filterSubject = () => {
    filteredSubject.value = curriculumSubject.value.filter(e => {
        if (
            (e.subj_name.toUpperCase().includes(searchValue.value.toUpperCase())) ||
            (e.subj_code.toUpperCase().includes(searchValue.value.toUpperCase()))
        ) {
            return e
        }
    })
}

const filterOccupancy = (id) => {
    classroomId.value = id
    occupied.value = false
    checking.value = true
    getOccupancy(buildingId.value, classroomId.value).then((results) => {

        occupancy.value = results.filter((e) => {
            if (e.occ_time == timeId.value) {
                switch (timeDay.value) {
                    case 'Monday':
                        !e.mon_subj_code ? occupied.value = false : occupied.value = true
                        occupier.value = {
                            subj_code: e.mon_subj_code,
                            subj_name: e.mon_subj_name,
                            sec_name: e.mon_sec_name,
                            gradelvl_name: e.mon_gradelvl_name,
                        }
                        break;
                    case 'Tuesday':
                        !e.tue_subj_code ? occupied.value = false : occupied.value = true
                        occupier.value = {
                            subj_code: e.tue_subj_code,
                            subj_name: e.tue_subj_name,
                            sec_name: e.tue_sec_name,
                            gradelvl_name: e.tue_gradelvl_name,
                        }
                        break;
                    case 'Wednesday':
                        !e.wed_subj_code ? occupied.value = false : occupied.value = true
                        occupier.value = {
                            subj_code: e.wed_subj_code,
                            subj_name: e.wed_subj_name,
                            sec_name: e.wed_sec_name,
                            gradelvl_name: e.wed_gradelvl_name,
                        }
                        break;
                    case 'Thursday':
                        !e.thurs_subj_code ? occupied.value = false : occupied.value = true
                        occupier.value = {
                            subj_code: e.thurs_subj_code,
                            subj_name: e.thurs_subj_name,
                            sec_name: e.thurs_sec_name,
                            gradelvl_name: e.thurs_gradelvl_name,
                        }
                        break;
                    case 'Friday':
                        !e.fri_subj_code ? occupied.value = false : occupied.value = true
                        occupier.value = {
                            subj_code: e.fri_subj_code,
                            subj_name: e.fri_subj_name,
                            sec_name: e.fri_sec_name,
                            gradelvl_name: e.fri_gradelvl_name,
                        }
                        break;
                    case 'Saturday':
                        !e.sat_subj_code ? occupied.value = false : occupied.value = true
                        occupier.value = {
                            subj_code: e.sat_subj_code,
                            sec_name: e.sat_sec_name,
                            gradelvl_name: e.sat_gradelvl_name,
                        }
                        break;
                }
            }
        })
        checking.value = false

    })
}

onMounted(async () => {

    activeID.value = subjId.value

    curriculumId.value = currId.value
    loading.value = true
    checking.value = true
    getFacultyAvailability().then((results) => {
        availability.value = results

        getScheduledFaculty().then((results) => {
            faculty.value = results

            getCurriculumSubject(curriculumId.value, launch.value.ln_quarter, launch.value.ln_gradelvl).then((results) => {
                curriculumSubject.value = results

                let empid = ''
                filteredSubject.value = curriculumSubject.value.map((e, index) => {

                    let indexer = faculty.value.findIndex(object => {
                        empid = object.emp_id
                        return ((object.lf_subjid === e.subj_id) && (object.lf_lnid == launchId.value));
                    });

                    if (indexer !== -1) {
                        return {
                            ...e,
                            faculty_id: empid,
                        }
                    } else {

                        return {
                            ...e,
                            faculty_id: '',
                        }
                    }
                })

                loading.value = false
                checking.value = false

            })
        })
    })




    if (bid.value) {
        buildingId.value = bid.value
        filterClassroom()
        classroomId.value = classrid.value


    } else {
        buildingId.value = ''
        classroomId.value = ''
    }
})

const assignSubject = (subj_id, subj_code, faculty_id) => {

    let msg = 'The faculty assigned for this subject has already a schedule for this time slot. These are the actions you can perform: \n\n- Change the assigned instructor for the subject. \n- Assign this subject to a different time slot'
    let data = availability.value.filter((e) => {
        if (
            (e.occ_time === timeId.value) &&
            (e.occ_day == timeDay.value) &&
            (e.occ_faculty == faculty_id)
            // ((e.occ_subjid == subj_id))
        ) {
            return e
        }
    })
    // console.log(timeId.value)
    // console.log(timeDay.value)
    // console.log(subj_id)
    // console.log(faculty_id)
    // console.log(availability.value)
    // console.log(data)
    // data[0].occ_faculty === faculty_id && object.occ_day == 'tuesday'
    switch (timeDay.value) {
        case 'Monday':
            if (Object.keys(data).length > 0) {
                // alert(msg)
                Swal.fire({
                    title: "Notice",
                    text: msg,
                    icon: "question"
                })
            } else {
                activeID.value = subj_id
                activeName.value = subj_code
                facultyId.value = faculty_id
            }
            break;
        case 'Tuesday':
            if (Object.keys(data).length > 0) {
                // alert(msg)
                Swal.fire({
                    title: "Notice",
                    text: msg,
                    icon: "question"
                })
            } else {
                activeID.value = subj_id
                activeName.value = subj_code
                facultyId.value = faculty_id
            }
            break;
        case 'Wednesday':
            if (Object.keys(data).length > 0) {
                // alert(msg)
                Swal.fire({
                    title: "Notice",
                    text: msg,
                    icon: "question"
                })
            } else {
                activeID.value = subj_id
                activeName.value = subj_code
                facultyId.value = faculty_id
            }
            break;
        case 'Thursday':
            if (Object.keys(data).length > 0) {
                // alert(msg)
                Swal.fire({
                    title: "Notice",
                    text: msg,
                    icon: "question"
                })
            } else {
                activeID.value = subj_id
                activeName.value = subj_code
                facultyId.value = faculty_id
            }
            break;
        case 'Friday':
            if (Object.keys(data).length > 0) {
                // alert(msg)
                Swal.fire({
                    title: "Notice",
                    text: msg,
                    icon: "question"
                })
            } else {
                activeID.value = subj_id
                activeName.value = subj_code
                facultyId.value = faculty_id
            }
            break;
        case 'Saturday':
            if (Object.keys(data).length > 0) {
                // alert(msg)
                Swal.fire({
                    title: "Notice",
                    text: msg,
                    icon: "question"
                })
            } else {
                activeID.value = subj_id
                activeName.value = subj_code
                facultyId.value = faculty_id
            }
            break;
    }

}


const setOthers = (value) => {
    filterClassroom()
    buildingId.value = value
    classroomId.value = value
    checking.value = false
}
</script>
<template>
    <div class="w-100 text-center p-3" v-if="checking">
        <Loader />
    </div>
    <div v-else class="container">
        <div class="row">
            <div class="col overflow-auto small-font" style="height: 30rem;">
                <div class="text-start border mb-2 card shadow p-3">
                    <p class="col-span-3 font-semibold">Other Alternatives</p>
                    <div class="d-flex flex-column gap-2">
                        <!-- 90 means ol class -->
                        <button @click="buildingId = 90, setOthers(90)" :disabled="buildingId == 90 ? true : false"
                            :class="buildingId == 90 ? 'btn btn-sm btn-primary w-100' : 'btn btn-sm btn-dark w-100'">
                            Online Class
                        </button>
                        <!-- 91 means others -->
                        <button @click="buildingId = 91, setOthers(91)" :disabled="buildingId == 91 ? true : false"
                            :class="buildingId == 91 ? 'btn btn-sm btn-primary w-100' : 'btn btn-sm btn-dark w-100'">
                            Others
                        </button>
                    </div>
                </div>
                <div class="text-start border mb-2 card shadow p-3">
                    <p class="col-span-3 font-semibold">Choose Building</p>
                    <div class="d-flex flex-column gap-2">
                        <button v-for="(bl, index) in building" @click="buildingId = bl.buil_id, filterClassroom()"
                            :disabled="buildingId == bl.buil_id ? true : false"
                            :class="buildingId == bl.buil_id ? 'btn btn-sm btn-primary w-100' : 'btn btn-sm btn-dark w-100'">
                            {{ bl.buil_name }}
                        </button>
                    </div>
                </div>
                <div class="text-start border mb-2 card shadow p-3">
                    <p class="col-span-3 font-semibold">Choose Classroom</p>
                    <div class="d-flex flex-column gap-2">
                        <button v-if="Object.keys(filteredClassroom).length" v-for="(cl, index) in filteredClassroom"
                            @click="filterOccupancy(cl.classr_id)"
                            :disabled="classroomId == cl.classr_id ? true : false"
                            :class="classroomId == cl.classr_id ? 'btn btn-sm btn-primary w-100' : 'btn btn-sm btn-dark w-100'">
                            {{ cl.classr_name }}
                        </button>
                        <p v-else class="text-danger fw-bold text-center">
                            Select Building First to view classroom.
                        </p>
                    </div>
                </div>

            </div>

            <div class="col overflow-auto" style="height: 40rem;">
                <div class="d-flex flex-column gap-2 h-100">
                    <div v-if="(!buildingId || !classroomId) && !loading" class=" border align-content-center p-3 h-100">
                        <div class="card bg-primary bg-opacity-50">
                            <div class="card-header">
                                Notice
                            </div>
                            <div class="card-body">
                                <h5 class="card-title">Verification</h5>
                                <p class="card-text">To initialize occupancy detection, select building and classroom first.</p>
                                <!-- <a href="#" class="btn btn-primary">Go somewhere</a> -->
                            </div>
                        </div>
                    </div>
                    <div v-if="(!buildingId || !classroomId) && loading" class=" border align-content-center p-3 h-100">
                        <Loader/>
                    </div>
                    <div v-if="(buildingId && classroomId) && !loading" class="border align-content-center h-100 bg-secondary-subtle">
                        <div v-if="occupied" class="p-3 w-100 align-content-center w-100">
                            <div class="card bg-warning bg-opacity-50">
                                <div class="card-header">
                                    Notice
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title">{{ occupier.subj_name }} ({{ occupier.subj_code }})</h5>
                                    <p class="card-text">This room is already occupied within the selected timeslot. Try selecting another room or timeslot.</p>
                                    <!-- <a href="#" class="btn btn-primary">Go somewhere</a> -->
                                </div>
                            </div>
                        </div>
                        <div v-if="!occupied" class="border p-2">
                            <div class="">
                                <input v-model="searchValue" @keyup="filterSubject()" class="form-control mb-1"
                                    placeholder="Search Subjects Here..." />
                                <div class="p-3 card">
                                    <div class="table-responsive border p-2 small-font" v-if="!loading">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Select Subjects</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-if="!Object.keys(filteredSubject).length">
                                                    <td class="align-middle text-start">
                                                        No Data Found
                                                    </td>
                                                </tr>
                                                <tr v-else v-for="(subj, index) in filteredSubject"
                                                    @click="assignSubject(subj.subj_id, subj.subj_code, subj.faculty_id)"
                                                    :class="activeID == subj.subj_id ? 'pe-none' : 'pe-auto'"
                                                    :disabled="activeID == subj.subj_id ? true : false">
                                                    <td
                                                        :class="activeID == subj.subj_id ? 'align-middle text-start bg-secondary text-white' : 'align-middle text-start bg-white text-black'">
                                                        <p class="fw-bold ">{{ subj.subj_code }}</p>
                                                        <p class=" fst-italic">{{ subj.subj_name }}</p>
                                                    </td>
                                                </tr>

                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="p-3" v-else>
                                        <Loader />
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3">
                                <div class="d-flex gap-1">
                                    <button
                                        :disabled="!activeID || !activeName || !buildingId || !classroomId ? true : false"
                                        @click="assign(activeID, activeName, buildingId, classroomId, false)"
                                        tabindex="-1" title="Assign Schedule" type="button"
                                        class="btn btn-sm btn-primary w-100">
                                        Assign Subject
                                    </button>
                                    <button @click="assign('', '', '', '', true)" tabindex="-1" title="Assign Schedule"
                                        type="button" class="btn btn-sm btn-danger w-100">
                                        Remove Subject
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>


    </div>
</template>