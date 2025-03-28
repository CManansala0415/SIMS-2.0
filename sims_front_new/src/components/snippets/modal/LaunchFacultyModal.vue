<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import {
    getScheduledSubjects,
    getEmployee,
    getEmployeeLoad,
    getCurriculumSubject,
    getScheduledFaculty,
    addScheduledFaculty,
    deleteScheduledFaculty,
    getFacultyAvailability,

} from "../../Fetchers.js";
import Loader from '../loaders/Loading1.vue';
import { getUserID } from "../../../routes/user.js";

const props = defineProps({
    subjectData: {
    },
    curriculumIdData: {
    },
    launchIdData: {
    },
    semIdData: {
    },
    gradelvlData: {
    }
})

const subject = computed(() => {
    return props.subjectData
});
const curriculumId = computed(() => {
    return props.curriculumIdData
});
const launchId = computed(() => {
    return props.launchIdData
});
const semId = computed(() => {
    return props.semIdData
});
const gradelvlId = computed(() => {
    return props.gradelvlData
});

const emit = defineEmits(['close-modal'])
const close = () => {
    window.stop()
    emit('close-modal')
}


const userID = ref('')
const checking = ref(false)
const preLoading = ref(false)
const employee = ref([])
const employeeLoad = ref([])
const curriculumSubject = ref([])
const availability = ref([])
const faculty = ref([])


const booter = async () => {
    getEmployeeLoad(0).then((results) => {
        employeeLoad.value = results
    })
    getEmployee(0, 0).then((results) => {
        employee.value = results.data
    })
    getScheduledSubjects(curriculumId.value).then((results) => {
        // console.log(results)
    })
    getCurriculumSubject(curriculumId.value, semId.value, gradelvlId.value).then((results) => {
        curriculumSubject.value = results
    })
    getScheduledFaculty().then((results) => {
        faculty.value = results
        // console.log(faculty.value)
    })
    getFacultyAvailability().then((results) => {
        availability.value = results
    })

}
const saveFaculty = (index, subjid, mode) => {
    let facultyId = document.getElementById('assignedFaculty' + index).value
    let lfid = ''
    //locate empolyee id sa faculty array
    let lfid_detector = faculty.value.findIndex(object => {
        return ((object.lf_empid == facultyId) && (object.lf_subjid == subjid));
    });

    //locate if yung subject ay for update
    let subjid_detector = faculty.value.findIndex(object => {
        return (object.lf_subjid == subjid);
    });

    curriculumSubject.value.filter(object => {
        if ((object.subj_id == subjid) && (object.lf_lnid == launchId.value)) {
            lfid = object.lf_id
        }
    });


    if (mode == 'add') {
        if ((lfid_detector !== -1) && (subjid_detector !== -1)) {
            if (facultyId) {

                // checking.value = true
                let x = {
                    lf_id: lfid,
                    lf_empid: Number(facultyId),
                    lf_subjid: subjid,
                    lf_addedby: userID.value,
                    lf_updatedby: userID.value,
                    lf_lnid: launchId.value
                }
                addScheduledFaculty(x).then((results) => {
                    checking.value = false
                    alert('Successfully Saved')
                    close()
                })
                // console.log(x)
            } else {
                alert('Please Select faculty')
            }
        } else if ((lfid_detector === -1) && (subjid_detector !== -1)) {
            if (facultyId) {

                // checking.value = true
                let x = {
                    lf_id: lfid,
                    lf_empid: Number(facultyId),
                    lf_subjid: subjid,
                    lf_addedby: userID.value,
                    lf_updatedby: userID.value,
                    lf_lnid: launchId.value
                }
                addScheduledFaculty(x).then((results) => {
                    checking.value = false
                    alert('Successfully Saved')
                    close()
                })
                // console.log(x)
            } else {
                alert('Please Select faculty')
            }
        } else {
            if (facultyId) {
                // checking.value = true
                let x = {
                    lf_id: '',
                    lf_empid: Number(facultyId),
                    lf_subjid: subjid,
                    lf_addedby: userID.value,
                    lf_updatedby: userID.value,
                    lf_lnid: launchId.value
                }
                addScheduledFaculty(x).then((results) => {
                    checking.value = false
                    alert('Successfully Saved')
                    close()
                })
                // console.log(x)
            } else {
                alert('Please Select faculty')
            }
        }

    } else {
        if (confirm("Are you sure you want to remove assign faculty?") == true) {
            if (facultyId) {
                checking.value = true
                let x = {
                    lf_id: lfid,
                    lf_empid: facultyId,
                    lf_lnid: launchId.value,
                    lf_updatedby: userID.value,
                }

                deleteScheduledFaculty(x).then((results) => {
                    checking.value = false
                    // console.log(results)
                    alert('Successfully Removed')
                    close()
                })
            } else {
                alert('No faculty assigned.')
            }

        } else {
            return false;
        }

    }

}

const detectLoad = (index, subjid, lock) => {

    if (lock == false) {
        let facultyId = document.getElementById('assignedFaculty' + index).value
        let selectbox = document.getElementById('assignedFaculty' + index)

        let availability = employeeLoad.value.findIndex(object => {
            return ((object.ld_empid == facultyId) && (object.ld_subjid == subjid));
        });

        if (availability !== -1) {
            // console.log(subjid)
            // console.log(facultyId)
        } else {
            selectbox.value = ''
            alert('This faculty is not allowed to teach the subject because it is not included in his/her faculty loading.')
        }
    }
}

onMounted(async () => {
    try {
        preLoading.value = true
        await booter().then((results) => {
            getUserID().then((results) => {
                userID.value = results.account.data.id

                let empid = ''
                let lfid = ''
                let lnid = ''
                curriculumSubject.value = curriculumSubject.value.map((e, index) => {
                    let indexer = faculty.value.findIndex(object => {
                        empid = object.emp_id
                        lfid = object.lf_id
                        lnid = object.lf_lnid
                        return ((object.lf_subjid === e.subj_id) && (object.lf_lnid == launchId.value));
                    });

                    //check if may nassign na na sched sa sa subject na nakatag para di sya mapalitan kapag meron
                    let hasSched = availability.value.findIndex((obj) => {
                        return (
                            (e.subj_id == obj.occ_subjid) && (obj.occ_faculty == empid) ||
                            (e.subj_id == obj.occ_subjid) && (obj.occ_faculty == empid) ||
                            (e.subj_id == obj.occ_subjid) && (obj.occ_faculty == empid) ||
                            (e.subj_id == obj.occ_subjid) && (obj.occ_faculty == empid) ||
                            (e.subj_id == obj.occ_subjid) && (obj.occ_faculty == empid) ||
                            (e.subj_id == obj.occ_subjid) && (obj.occ_faculty == empid)
                        );
                    })

                    // console.log(e.subj_id + ' : ' + hasSched + ' : ' + empid)

                    if (indexer !== -1) {
                        return {
                            ...e,
                            lf_id: lfid,
                            lf_lnid: lnid,
                            faculty_id: empid,
                            lock: hasSched !== -1 ? true : false
                        }
                    } else {

                        return {
                            ...e,
                            lf_id: '',
                            faculty_id: '',
                            lock: hasSched !== -1 ? true : false
                        }
                    }
                })

                preLoading.value = false

                // console.log(curriculumSubject.value)
                // console.log(faculty.value)

                // console.log(curriculumSubject.value)
            })
        })


    } catch (err) {
        preLoading.value = false
        alert('error loading the list default components')
    }
})

</script>
<template>
    <div class="p-7 ">
        <div v-if="preLoading" class="w-100 flex flex-col gap-2 justify-center items-center">
            <Loader/>
        </div>
        <div v-else class="d-flex flex-column gap-1 overflow-auto border p-3 bg-body-secondary small-font">
            <div v-for="(sj, index) in curriculumSubject" class="">
                <div class="card">
                    <div class="card-header">
                        {{ sj.subj_code }}
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">{{ sj.subj_name }}</h5>
                        <p class="card-text">Select the faculty from options below then click the assign button to save. </p>
                        <!-- Once schedule is made and instructor is already assigned, 
                            you will not be able to change the assigned faculty unless you remove the created schedule. -->
                        <select @change="detectLoad(index, sj.subj_id, sj.lock)" :id="'assignedFaculty' + index"
                            :value="sj.faculty_id" :disabled="sj.lock == true ? true : false"
                            class="form-control form-select-sm w-100"
                            title="Click Edit to modify details">
                            <option value="" disabled>-- Select Faculty --</option>
                            <option v-for="(emp, index) in employee" :value="emp.emp_id">
                                {{ emp.emp_firstname }} {{ emp.emp_middlename }} {{ emp.emp_lastname }} {{
                                    emp.emp_suffixname ? emp.emp_suffixname : '' }}
                            </option>
                        </select>
                        <div v-if="sj.lock == false" class="d-flex gap-2 mt-3">
                            <button :disabled="checking ? true : false" @click="saveFaculty(index, sj.subj_id, 'add')"
                                tabindex="-1" title="Assign Faculty" type="button" class="btn btn-sm btn-primary w-100">
                                Assign
                            </button>
                            <button :disabled="checking ? true : false"
                                @click="saveFaculty(index, sj.subj_id, 'remove')" tabindex="-1" title="Remove Faculty"
                                type="button" class="btn btn-sm btn-danger w-100">
                                Remove
                            </button>
                        </div>
                        <div v-else class="align-content-center justify-content-center mt-3">
                            <p class="text-danger fw-bold">Unable to update faculty. Remove all active
                                schedules for this subject first</p>
                        </div>
                    </div>
                </div>
            </div>
            <!-- <select 
                        class="w-100 border border-gray-300 p-2 disabled:shadow-inner rounded-md disabled:bg-gray-50 disabled:cursor-not-allowed " 
                        title="Click Edit to modify details">
                    <option value="" disabled>-- Select Type --</option>
                    <option v-for="(sj, index) in curriculumSubject" :value="sj.subj_id">
                        {{ sj.subj_name }} ({{ sj.subj_code }})
                    </option>
                </select> -->
        </div>
    </div>
</template>