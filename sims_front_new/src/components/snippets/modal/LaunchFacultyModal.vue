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
import NeuLoader2 from '../loaders/NeuLoader2.vue';
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
        // console.log(employee.value)
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
                    // checking.value = false
                    // alert('Successfully Saved')
                    // close()
                    Swal.fire({
                        title: "Update Successful",
                        text: "Changes applied",
                        icon: "success"
                    }).then(() => {
                        checking.value = false
                        close()
                    });
                })
                // console.log(x)
            } else {
                // alert('Please Select faculty')
                Swal.fire({
                    title: "Requirement",
                    text: "Please Select faculty",
                    icon: "question"
                })
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
                    // checking.value = false
                    // alert('Successfully Saved')
                    // close()
                    Swal.fire({
                        title: "Update Successful",
                        text: "Changes applied",
                        icon: "success"
                    }).then(() => {
                        checking.value = false
                        close()
                    });
                })
                // console.log(x)
            } else {
                // alert('Please Select faculty')
                Swal.fire({
                    title: "Requirement",
                    text: "Please Select faculty",
                    icon: "question"
                })
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
                    // checking.value = false
                    // alert('Successfully Saved')
                    // close()
                    Swal.fire({
                        title: "Update Successful",
                        text: "Changes applied",
                        icon: "success"
                    }).then(() => {
                        checking.value = false
                        close()
                    });
                })
                // console.log(x)
            } else {
                // alert('Please Select faculty')
                Swal.fire({
                    title: "Requirement",
                    text: "Please Select faculty",
                    icon: "question"
                })
            }
        }

    } else {
        // if (confirm("Are you sure you want to remove assign faculty?") == true) {
        //     if (facultyId) {
        //         checking.value = true
        //         let x = {
        //             lf_id: lfid,
        //             lf_empid: facultyId,
        //             lf_lnid: launchId.value,
        //             lf_updatedby: userID.value,
        //         }

        //         deleteScheduledFaculty(x).then((results) => {
        //             // checking.value = false
        //             // console.log(results)
        //             // alert('Successfully Removed')
        //             // close()
        //             Swal.fire({
        //                 title: "Successfully Removed",
        //                 text: "Changes applied",
        //                 icon: "success"
        //             }).then(()=>{
        //                 checking.value = false
        //                 close()
        //             });
        //         })
        //     } else {
        //         // alert('No faculty assigned.')
        //         Swal.fire({
        //             title: "Requirement",
        //             text: "No faculty assigned",
        //             icon: "question"
        //         })
        //     }

        // } else {
        //     return false;
        // }

        Swal.fire({
            title: "Delete Record",
            text: "Are you sure you want to remove assign faculty?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, Im Delete it!"
        }).then(async (result) => {
            if (result.isConfirmed) {
                if (facultyId) {
                    checking.value = true
                    let x = {
                        lf_id: lfid,
                        lf_empid: facultyId,
                        lf_lnid: launchId.value,
                        lf_updatedby: userID.value,
                    }

                    deleteScheduledFaculty(x).then((results) => {
                        // checking.value = false
                        // console.log(results)
                        // alert('Successfully Removed')
                        // close()
                        Swal.fire({
                            title: "Successfully Removed",
                            text: "Changes applied",
                            icon: "success"
                        }).then(() => {
                            checking.value = false
                            close()
                        });
                    })
                } else {
                    // alert('No faculty assigned.')
                    Swal.fire({
                        title: "Requirement",
                        text: "No faculty assigned",
                        icon: "question"
                    })
                }
            }
        });

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
            // alert('This faculty is not allowed to teach the subject because it is not included in his/her faculty loading.')
            Swal.fire({
                title: "Requirement",
                text: "This faculty is not allowed to teach the subject because it is not included in his/her faculty loading",
                icon: "question"
            })
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
                        // return (
                        //     (e.subj_id == obj.occ_subjid) && (obj.occ_faculty == empid) ||
                        //     (e.subj_id == obj.occ_subjid) && (obj.occ_faculty == empid) ||
                        //     (e.subj_id == obj.occ_subjid) && (obj.occ_faculty == empid) ||
                        //     (e.subj_id == obj.occ_subjid) && (obj.occ_faculty == empid) ||
                        //     (e.subj_id == obj.occ_subjid) && (obj.occ_faculty == empid) ||
                        //     (e.subj_id == obj.occ_subjid) && (obj.occ_faculty == empid)
                        // );
                        return (
                            (e.subj_id == obj.occ_subjid) && (obj.occ_faculty == empid) && (obj.occ_lnid == launchId.value)
                        );
                    })

                    // console.log(hasSched)
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
        // preLoading.value = false
        // alert('error loading the list default components')
        Swal.fire({
            icon: "error",
            title: "Oops...",
            text: "Something went wrong!",
            footer: '<a href="#" disabled>Have you checked your internet connection?</a>'
        }).then(() => {
            preLoading.value = false
        });
    }
})

</script>
<template>
    <div class="p-7">
        <div v-if="preLoading" class="w-100 flex flex-col gap-2 justify-center items-center">
            <NeuLoader2 />
        </div>

        <div v-else class="neu-card overflow-auto border rounded-3 small-font p-3 text-dim">
            <!-- Header -->
            <div class="table-header d-flex fw-bold d-none d-md-flex">
                <div class="col-code text-center flex-fill">Code</div>
                <div class="col-name text-center flex-fill">Subject</div>
                <div class="col-faculty text-center flex-fill">Faculty</div>
                <div class="col-action text-center flex-fill">Action</div>
            </div>

            <!-- Rows -->
            <div v-for="(sj, index) in curriculumSubject" :key="index"
                class="table-row d-flex flex-column flex-md-row align-items-stretch">
                <!-- Code -->
                <div class="col-code flex-fill text-center fw-semibold align-content-center">
                    <span class="d-md-none fw-bold text-success">Code: </span>{{ sj.subj_code }}
                </div>

                <!-- Subject -->
                <div class="col-name flex-fill text-center align-content-center">
                    <span class="d-md-none fw-bold text-success">Subject: </span>{{ sj.subj_name }}
                </div>

                <!-- Faculty Selector -->
                <div class="col-faculty flex-fill">
                    <span class="d-md-none fw-bold text-success">Faculty: </span>
                    <select @change="detectLoad(index, sj.subj_id, sj.lock)" :id="'assignedFaculty' + index"
                        :value="sj.faculty_id" :disabled="sj.lock" class="neu-input neu-select text-uppercase"
                        title="Click Edit to modify details">
                        <option value="" disabled>-- Select Faculty --</option>
                        <option v-for="(emp, i) in employee" :key="i" :value="emp.emp_id">
                            {{ emp.emp_lastname }}, {{ emp.emp_firstname }}
                            {{ emp.emp_middlename ? emp.emp_middlename : '' }}
                            {{ emp.emp_suffixname ? emp.emp_suffixname : '' }}
                        </option>
                    </select>
                </div>

                <!-- Action Buttons -->
                <div class="col-action flex-fill text-center mt-2 mt-md-0">
                    <template v-if="!sj.lock">
                        <div class="action-btns d-flex gap-2 justify-content-center">
                            <button :disabled="checking" @click="saveFaculty(index, sj.subj_id, 'add')" type="button"
                                class="neu-btn neu-green p-2 flex-fill" title="Assign Faculty">
                                Assign
                            </button>
                            <button :disabled="checking" @click="saveFaculty(index, sj.subj_id, 'remove')" type="button"
                                class="neu-btn neu-red p-2 flex-fill" title="Remove Faculty">
                                Remove
                            </button>
                        </div>
                    </template>

                    <template v-else>
                        <p class="text-danger small fw-semibold mb-0 mt-2">
                            Locked — remove all schedules first
                        </p>
                    </template>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.table-header {
    /* background-color: #198754; */
    /* green accent */
    padding: 10px;
    border-radius: 6px 6px 0 0;
}

.table-row {
    display: flex;
    align-items: center;
    padding: 10px;
    border-bottom: 1px solid #dee2e6;
    transition: background 0.2s ease;
}

.table-row:hover {
  cursor: pointer;
  transform: translateY(-2px);
  box-shadow: 8px 8px 20px #bebebe, -8px -8px 20px #ffffff;
  color: #2c2c2c;
}

.table-row>div {
    flex: 1 1 0;
    padding: 5px;
    text-align: center;
}

/* Equal width columns */

.col-name,
.col-faculty,
.col-action {
    width: 25%;
}

.col-code{
    width: 5%;
}

/* Action buttons inline */
.action-btns {
    display: flex;
    justify-content: center;
    gap: 0.5rem;
}

/* ---------- RESPONSIVE ---------- */
@media (max-width: 991px) {

    .col-code,
    .col-name,
    .col-faculty,
    .col-action {
        width: 100%;
    }

    .table-row {
        flex-wrap: wrap;
    }
}

/* ---------- MOBILE ---------- */
@media (max-width: 767px) {
    .table-header {
        display: none;
    }

    .table-row {
        border: 1px solid #dee2e6;
        border-radius: 10px;
        margin-bottom: 10px;
        /* background: #fff; */
        box-shadow:
            inset 3px 3px 6px rgba(0, 0, 0, 0.18),
            inset -8px -8px 6px rgba(255, 255, 255, 0.3);
        flex-direction: column;
        align-items: flex-start;
        padding: 12px;
    }

    .table-row>div {
        text-align: left;
        width: 100%;
        padding: 6px 0;
    }

    /* Stack buttons vertically on mobile */
    .action-btns {
        flex-direction: column;
        width: 100%;
    }

    .btn {
        font-size: 0.85rem;
        width: 100%;
    }
}
</style>
