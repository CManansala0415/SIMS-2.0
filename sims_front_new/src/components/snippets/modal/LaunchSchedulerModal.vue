<script setup>
import { ref, onMounted, computed } from 'vue';
import {
    getOccupancy,
    getCurriculumSubject,
    getScheduledFaculty,
    getFacultyAvailability,
    getMergedClass
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
    },
    mergeableData: {
    },
    schedData: {
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

const mergeable = computed(() => {
    return props.mergeableData
});

const schedule = computed(() => {
    return props.schedData
});

const emit = defineEmits(['close-modal', 'assign-sched'])
const assign = (id, name, bldg, classr, remove) => {
    // alert('Successful')
    // emit('assign-sched', id, name, bldg, classr, remove, facultyId.value)

    // console.log(occupier.value)
    Swal.fire({
        title: "Update Successful",
        text: "Changes applied",
        icon: "success"
    }).then(() => {
        emit('assign-sched', id, name, bldg, classr, remove, facultyId.value, mergeClass.value, enableClassMerge.value, occupier.value.sched_id)
    });
}

const activeID = ref('')
const activeName = ref('')
const curriculumId = ref('')

const mergeClass = ref(0)
const buildingId = ref('')
const facultyId = ref('')
const classroomId = ref('')
const searchValue = ref('')
const filteredClassroom = ref([])
const occupancy = ref([])
const loading = ref(false)
const forMerge = ref(false)
const filteredSubject = ref([])
const availability = ref([])
const curriculumSubject = ref([])
const faculty = ref([])
const occupier = ref({
    sched_id: '',
    subj_id: '',
    subj_code: '',
    sec_name: '',
    gradelvl_name: '',
    course_name: 'test',
    can_merge: 0,
    faculty: ''
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

const tempFacultId = ref('') // para to sa icocompare na current faculty vs existing faculty na nasa sched for merging
const filterSubject = (data) => {
    if (data) {
        filteredSubject.value = curriculumSubject.value.filter(e => {
            if (
                (e.subj_id == data)
            ) {
                return e
            }
        })

        if (filteredSubject.value[0].faculty_id == tempFacultId.value) {
            occupied.value = false
            forMerge.value = true
        } else {
            Swal.fire({
                title: "Notice",
                text: 'The subject you are trying to merge has a different faculty assigned. Please assign the same faculty to proceed with merging.',
                icon: "question"
            })
        }
    } else {
        filteredSubject.value = curriculumSubject.value.filter(e => {
            if (
                (e.subj_name.toUpperCase().includes(searchValue.value.toUpperCase())) ||
                (e.subj_code.toUpperCase().includes(searchValue.value.toUpperCase()))
            ) {
                return e
            }
        })

        forMerge.value = false
    }

}

const filterOccupancy = (id) => {
    classroomId.value = id
    occupied.value = false
    checking.value = true
    getOccupancy(buildingId.value, classroomId.value).then((results) => {
        occupancy.value = results.filter((e) => {


            if (e.occ_time == timeId.value) {
                // console.log(e)
                switch (timeDay.value) {
                    case 'Monday':
                        !e.mon_subj_code ? occupied.value = false : occupied.value = true
                        occupier.value = {
                            sched_id: e.mon_sched_id,
                            subj_id: e.mon_subj_id,
                            subj_code: e.mon_subj_code,
                            subj_name: e.mon_subj_name,
                            sec_name: e.mon_sec_name,
                            gradelvl_name: e.mon_gradelvl_name,
                            course_name: e.mon_course_name,
                            can_merge: e.sched_mon_mergeable,
                            faculty: e.mon_sched_faculty
                        }
                        break;
                    case 'Tuesday':
                        !e.tue_subj_code ? occupied.value = false : occupied.value = true
                        occupier.value = {
                            sched_id: e.tue_sched_id,
                            subj_id: e.tue_subj_id,
                            subj_code: e.tue_subj_code,
                            subj_name: e.tue_subj_name,
                            sec_name: e.tue_sec_name,
                            gradelvl_name: e.tue_gradelvl_name,
                            course_name: e.tue_course_name,
                            can_merge: e.sched_tue_mergeable,
                            faculty: e.tue_sched_faculty
                        }
                        break;
                    case 'Wednesday':
                        !e.wed_subj_code ? occupied.value = false : occupied.value = true
                        occupier.value = {
                            sched_id: e.wed_sched_id,
                            subj_id: e.wed_subj_id,
                            subj_code: e.wed_subj_code,
                            subj_name: e.wed_subj_name,
                            sec_name: e.wed_sec_name,
                            gradelvl_name: e.wed_gradelvl_name,
                            course_name: e.wed_course_name,
                            can_merge: e.sched_wed_mergeable,
                            faculty: e.wed_sched_faculty
                        }
                        break;
                    case 'Thursday':
                        !e.thurs_subj_code ? occupied.value = false : occupied.value = true
                        occupier.value = {
                            sched_id: e.thurs_sched_id,
                            subj_id: e.thurs_subj_id,
                            subj_code: e.thurs_subj_code,
                            subj_name: e.thurs_subj_name,
                            sec_name: e.thurs_sec_name,
                            gradelvl_name: e.thurs_gradelvl_name,
                            course_name: e.thurs_course_name,
                            can_merge: e.sched_thurs_mergeable,
                            faculty: e.thurs_sched_faculty
                        }
                        break;
                    case 'Friday':
                        !e.fri_subj_code ? occupied.value = false : occupied.value = true
                        occupier.value = {
                            sched_id: e.fri_sched_id,
                            subj_id: e.fri_subj_id,
                            subj_code: e.fri_subj_code,
                            subj_name: e.fri_subj_name,
                            sec_name: e.fri_sec_name,
                            gradelvl_name: e.fri_gradelvl_name,
                            course_name: e.fri_course_name,
                            can_merge: e.sched_fri_mergeable,
                            faculty: e.fri_sched_faculty
                        }
                        break;
                    case 'Saturday':
                        !e.sat_subj_code ? occupied.value = false : occupied.value = true
                        occupier.value = {
                            sched_id: e.sat_sched_id,
                            subj_id: e.sat_subj_id,
                            subj_code: e.sat_subj_code,
                            sec_name: e.sat_sec_name,
                            gradelvl_name: e.sat_gradelvl_name,
                            course_name: e.sat_course_name,
                            can_merge: e.sched_sat_mergeable,
                            faculty: e.sat_sched_faculty
                        }
                        break;
                }
            }
        })
        checking.value = false
    })
}


const removeSubject = ref(false)
const hasMergedClass = ref(false)
const detectingMergedClass = ref(true)
onMounted(async () => {
    //if may subject na remove nalang dapat ang options to avoid
    if (subjId.value) {
        removeSubject.value = true
        if (['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'].includes(timeDay.value)) {
            getMergedClass(schedule.value.sched_id, timeDay.value).then((results) => {
                let hasResults = Object.keys(results).length > 0
                hasMergedClass.value = hasResults
                detectingMergedClass.value = false
            })
        } else {
            detectingMergedClass.value = false
        }
    } else {
        activeID.value = subjId.value
        mergeClass.value = mergeable.value == 1 ? 1 : 0

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
                    curriculumSubject.value = results.map((e, index) => {

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

                    filteredSubject.value = curriculumSubject.value

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
    }


})

const assignSubject = (subj_id, subj_code, faculty_id, pass) => {
    let msg = 'The faculty assigned for this subject has already a schedule for this time slot. These are the actions you can perform: \n\n- Change the assigned instructor for the subject. \n- Assign this subject to a different time slot'
    let data = availability.value.filter((e) => {
        if (
            (e.occ_time === timeId.value) &&
            (e.occ_day == timeDay.value) &&
            (e.occ_faculty == faculty_id) &&
            (pass != 1) // means ojt or practicum, should not overlap
            // ((e.occ_subjid == subj_id))
        ) {
            return e
        }
    })
    // console.log(forMerge.value)
    // console.log(timeId.value)
    // console.log(timeDay.value)
    // console.log(subj_id)
    // console.log(faculty_id)
    // console.log(availability.value)
    // data[0].occ_faculty === faculty_id && object.occ_day == 'tuesday'
    switch (timeDay.value) {
        case 'Monday':
            if (Object.keys(data).length > 0 && !forMerge.value) {
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
            if (Object.keys(data).length > 0 && !forMerge.value) {
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
            if (Object.keys(data).length > 0 && !forMerge.value) {
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
            if (Object.keys(data).length > 0 && !forMerge.value) {
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
            if (Object.keys(data).length > 0 && !forMerge.value) {
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
            if (Object.keys(data).length > 0 && !forMerge.value) {
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
    // para sa class merging
    // console.log(mergeClass.value)
    if (value == 91) {
        if (!mergeable.value || mergeable.value == 0) {
            mergeClass.value = !mergeClass.value
        }
    } else {
        filterClassroom()
        buildingId.value = value
        classroomId.value = value
        checking.value = false
    }

}


const enableClassMerge = ref(false)
const mergeClassTo = (from) => {
    enableClassMerge.value = true
    tempFacultId.value = from.faculty
    filterSubject(from.subj_id)
}
</script>
<template>
  <div class="w-100 text-center p-3" v-if="checking">
    <Loader />
  </div>

  <div v-else class="container-fluid">
    <div v-if="!removeSubject" class="row g-3">
      <!-- LEFT PANEL -->
      <div class="col-md-4 overflow-auto small-font" style="height: 40rem;">
        <!-- OTHER ALTERNATIVES -->
        <div class="card shadow-sm border-0 mb-3">
          <div class="card-header bg-light fw-semibold d-flex align-items-center">
            <i class="bi bi-sliders me-2 text-primary"></i> Other Alternatives
          </div>
          <div class="card-body">
            <div class="row g-2">
              <div class="col-6">
                <div
                  class="tile-option"
                  :class="{ active: buildingId === 90 }"
                  @click="buildingId = 90; setOthers(90)"
                >
                  <i class="bi bi-laptop display-6 text-primary"></i>
                  <p class="mb-0 fw-semibold">Online Class</p>
                </div>
              </div>

              <div class="col-6">
                <div
                  class="tile-option"
                  :class="{ active: mergeClass == 1 }"
                  @click="setOthers(91)"
                >
                  <i class="bi bi-people-fill display-6 text-primary"></i>
                  <p class="mb-0 fw-semibold text-center">Merge Class</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- BUILDING SELECTION -->
        <div class="card shadow-sm border-0 mb-3">
          <div class="card-header bg-light fw-semibold d-flex align-items-center">
            <i class="bi bi-building me-2 text-primary"></i> Choose Building
          </div>
          <div class="card-body">
            <div class="row g-2">
              <div
                v-for="bl in building"
                :key="bl.buil_id"
                class="col-6"
              >
                <div
                  class="tile-option"
                  :class="{ active: buildingId === bl.buil_id }"
                  @click="buildingId = bl.buil_id; filterClassroom()"
                >
                  <i class="bi bi-house-door text-primary display-6"></i>
                  <p class="mb-0 fw-semibold text-center">{{ bl.buil_name }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- CLASSROOM SELECTION -->
        <div class="card shadow-sm border-0">
          <div class="card-header bg-light fw-semibold d-flex align-items-center">
            <i class="bi bi-door-closed me-2 text-primary"></i> Choose Classroom
          </div>
          <div class="card-body">
            <div v-if="Object.keys(filteredClassroom).length" class="row g-2">
              <div
                v-for="cl in filteredClassroom"
                :key="cl.classr_id"
                class="col-6"
              >
                <div
                  class="tile-option"
                  :class="{ active: classroomId === cl.classr_id }"
                  @click="filterOccupancy(cl.classr_id)"
                >
                  <i class="bi bi-easel text-primary display-6"></i>
                  <p class="mb-0 fw-semibold text-center">{{ cl.classr_name }}</p>
                </div>
              </div>
            </div>

            <p v-else class="text-center text-muted fst-italic mt-2">
              <i class="bi bi-info-circle me-1"></i>
              Select a building to view classrooms.
            </p>
          </div>
        </div>
      </div>

      <!-- RIGHT PANEL -->
      <div class="col-md-8 overflow-auto" style="height: 40rem;">
        <div class="d-flex flex-column gap-3 h-100">

          <!-- Step Info -->
          <div v-if="(!buildingId || !classroomId) && !loading" class="border bg-light-subtle p-3 text-center rounded">
            <i class="bi bi-info-circle display-6 text-primary"></i>
            <h5 class="fw-semibold mt-2">Select a Building and Classroom</h5>
            <p class="small text-muted mb-0">
              Choose a building and classroom to view or assign schedules.
            </p>
          </div>

          <div v-if="(!buildingId || !classroomId) && loading" class="border align-content-center p-3 h-100">
            <Loader />
          </div>

          <!-- OCCUPIED ROOM -->
          <div v-if="(buildingId && classroomId) && !loading && occupied" class="p-3">
            <div class="card border-0 shadow-sm bg-warning bg-opacity-25">
              <div class="card-header fw-bold text-dark">
                ⚠️ Room Occupied
              </div>
              <div class="card-body">
                <h5 class="card-title mb-1">{{ occupier.subj_name }} ({{ occupier.subj_code }})</h5>
                <p class="mb-2 text-muted">
                  {{ occupier.sec_name }} – {{ occupier.gradelvl_name }} • {{ occupier.course_name }}
                </p>

                <p class="small mb-3">
                  This room is already <strong>occupied</strong> for this time slot.
                  Please choose another room or adjust the schedule.
                </p>

                <button
                  v-if="(buildingId && classroomId && occupier.can_merge == 1)"
                  @click="mergeClassTo(occupier)"
                  class="btn btn-sm btn-primary"
                >
                  <i class="bi bi-people-fill me-1"></i> Merge Class Here
                </button>

                <div v-else class="alert alert-info py-2 small mb-0">
                  <p class="mb-1">
                    <strong>ℹ️ Info:</strong> Merging is not enabled for this schedule.
                  </p>
                  <p class="mb-0">
                    Enable merging from the <strong>original schedule holder</strong> if needed.
                  </p>
                </div>
              </div>
            </div>
          </div>

          <!-- AVAILABLE ROOM -->
          <div v-if="(buildingId && classroomId) && !loading && !occupied" class="border bg-light-subtle p-3 rounded">
            <input
              v-if="!forMerge"
              v-model="searchValue"
              @keyup="filterSubject()"
              class="form-control mb-2"
              placeholder="🔍 Search subjects..."
            />

            <div class="card border-0 shadow-sm">
              <div class="table-responsive small-font p-2" v-if="!loading">
                <table class="table table-hover align-middle">
                  <thead class="table-light">
                    <tr>
                      <th>Available Subjects</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-if="!Object.keys(filteredSubject).length">
                      <td class="text-muted fst-italic">No matching subjects found.</td>
                    </tr>

                    <tr
                      v-for="subj in filteredSubject"
                      :key="subj.subj_id"
                      @click="assignSubject(subj.subj_id, subj.subj_code, subj.faculty_id, subj.subj_schedpass)"
                      :class="activeID == subj.subj_id ? 'table-active' : ''"
                      style="cursor:pointer;"
                    >
                      <td>
                        <p class="fw-bold mb-0">{{ subj.subj_code }}</p>
                        <small class="text-muted fst-italic">{{ subj.subj_name }}</small>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <div class="p-3" v-else>
                <Loader />
              </div>
            </div>

            <div class="mt-3 d-flex gap-2">
              <button
                :disabled="!activeID || !buildingId || !classroomId"
                @click="assign(activeID, activeName, buildingId, classroomId, false)"
                class="btn btn-sm btn-success w-100"
              >
                ✅ Assign Subject
              </button>
              <button
                @click="assign('', '', '', '', true)"
                class="btn btn-sm btn-outline-danger w-100"
              >
                🗑️ Remove Subject
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- REMOVE SUBJECT CONFIRM -->
    <div v-else class="p-4 rounded border bg-light-subtle text-center">
      <div v-if="detectingMergedClass">
        <Loader />
      </div>
      <div v-else>
        <div v-if="!hasMergedClass">
          <p class="fw-semibold text-danger mb-2">⚠️ Subject Already Assigned</p>
          <p class="small text-muted mb-3">
            Removing this will unassign the subject from this schedule.
          </p>
          <button
            @click="assign('', '', '', '', true)"
            class="btn btn-sm btn-danger"
          >
            🗑️ Yes, Remove This Subject
          </button>
        </div>

        <div v-else class="alert alert-warning py-2 small mb-0">
          <p class="mb-1"><strong>Heads up!</strong> This time slot has a merged class schedule.</p>
          <p class="mb-0">Please remove all merged classes before deleting this one.</p>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.tile-option {
  background: #f8f9fa;
  border: 1px solid #dee2e6;
  border-radius: 0.75rem;
  padding: 1rem;
  text-align: center;
  transition: all 0.2s ease;
  cursor: pointer;
}
.tile-option:hover {
  background: #e9f3ff;
  transform: translateY(-2px);
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
}
.tile-option.active {
  background: #0d6efd;
  color: #fff;
  border-color: #0d6efd;
  box-shadow: 0 2px 8px rgba(13, 110, 253, 0.2);
}
.tile-option.active i {
  color: #fff !important;
}
</style>
