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
    getAcademicDefaults,
    getEnrollmentSchedule
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
                    console.log(groupedAssignmentSection.value)
                    console.log(groupedAssignmentSubject.value)
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


// const switchPage = () =>{
//   switch(switcher.value){
//     case 0:

//     break;
//   }
// }

const scheduleData = ref([])
const loadingSchedule = ref(false)
const subjectDetails = (status, index) => {
    loadingSchedule.value = true
    showSubjects.value = status
    indexerId.value = index
    scheduleData.value = []
    // console.log(indexerId.value)
    // console.log(showSubjects.value)
    // console.log(groupedAssignmentSection.value[index][0].ln_curriculum)

    let curr = groupedAssignmentSection.value[index][0].ln_curriculum
    let prog = groupedAssignmentSection.value[index][0].prog_id
    let grad = groupedAssignmentSection.value[index][0].ln_gradelvl
    let cour = groupedAssignmentSection.value[index][0].ln_course 
    let sec = groupedAssignmentSection.value[index][0].ln_section
    let lnid = groupedAssignmentSection.value[index][0].ln_id
    // console.log(curr, prog, grad, cour, sec, lnid)

    getEnrollmentSchedule(curr, prog, grad, cour, sec, lnid).then((results) => {
        scheduleData.value = results.data
        loadingSchedule.value = false
        // console.log(scheduleData.value)
    })
}

// Chat GPT Helper
// Day mapping
const dayMap = [
    { field: "sched_mon", label: "Monday", key: "mon", order: 1 },
    { field: "sched_tue", label: "Tuesday", key: "tue", order: 2 },
    { field: "sched_wed", label: "Wednesday", key: "wed", order: 3 },
    { field: "sched_thurs", label: "Thursday", key: "thurs", order: 4 },
    { field: "sched_fri", label: "Friday", key: "fri", order: 5 },
    { field: "sched_sat", label: "Saturday", key: "sat", order: 6 },
]

// Format time like "0800","A" -> "08:00 AM"
function formatTime(hhmm, meridian, isEnd = false) {
    let suffix = meridian === "A" ? "AM" : "PM"
    if (isEnd && hhmm.startsWith("12")) suffix = "PM"

    let hours = parseInt(hhmm.slice(0, 2), 10)
    const minutes = hhmm.slice(2, 4)
    const displayHour = (hours % 12) === 0 ? 12 : (hours % 12)

    return `${String(displayHour).padStart(2, "0")}:${minutes} ${suffix}`
}

function parseScheduleEntry(code, sc, day) {
    if (!code) return null
    const startRaw = code.slice(0, 4)
    const endRaw = code.slice(4, 8)
    const meridian = code.slice(8) // "A" or "P"

    return {
        start: formatTime(startRaw, meridian, false),
        end: formatTime(endRaw, meridian, true),
        rawStart: startRaw + meridian,
        rawEnd: endRaw + (endRaw.startsWith("12") ? "P" : meridian),
        day: day.label,
        dayOrder: day.order,
        room: sc[`${day.key}_room_name`] || "",
        building: sc[`${day.key}_buil_name`] || "",
        faculty: [
            sc[`${day.key}_faculty_lastname`] || "",
            sc[`${day.key}_faculty_firstname`] || "",
            sc[`${day.key}_faculty_middlename`] || "",
            sc[`${day.key}_faculty_suffixname`] || ""
        ].filter(Boolean).join(" ").trim(),
    }
}

function getScheduleGroupsForSubject(subjId) {
    const sd = Array.isArray(scheduleData.value) ? scheduleData.value : []
    const entries = []

    for (const sc of sd) {
        for (const d of dayMap) {
            if (sc[d.field] === subjId) {
                const e = parseScheduleEntry(sc.sched_time, sc, d)
                if (e) entries.push(e)
            }
        }
    }

    if (!entries.length) return []

    entries.sort((a, b) => a.dayOrder - b.dayOrder || a.rawStart.localeCompare(b.rawStart))

    const groups = []
    let cur = null

    entries.forEach((e, i) => {
        if (!cur) {
            cur = { ...e }
            return
        }
        const prev = entries[i - 1]
        const canMerge =
            e.day === cur.day &&
            e.room === cur.room &&
            e.building === cur.building &&
            e.faculty === cur.faculty &&
            e.rawStart === prev.rawEnd

        if (canMerge) {
            cur.end = e.end
            cur.rawEnd = e.rawEnd
        } else {
            groups.push(cur)
            cur = { ...e }
        }
    })
    if (cur) groups.push(cur)

    return groups
}
// Chat GPT Helper

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
                                data-bs-target="#editdatamodal" title="View Subjects"
                                @click="subjectDetails(true,index)">
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
                                data-bs-target="#editdatamodal" title="View Subjects"
                                @click="subjectDetails(true,index)">
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
                <div class="modal-body" v-if="showSubjects">
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
                                        <th style="background-color: #237a5b;" class="text-white" colspan="3">Schedule</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(app, index) in groupedAssignmentSection[indexerId]" v-if="Object.keys(groupedAssignmentSection).length && !loadingSchedule">
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
                                        <!-- Days -->
                                        <td class="align-middle p-2">
                                            <template v-if="getScheduleGroupsForSubject(app.subj_id).length">
                                                <div v-for="(g, i) in getScheduleGroupsForSubject(app.subj_id)" :key="i">
                                                    {{ g.day }}
                                                </div>
                                            </template>
                                            <span v-else>TBA</span>
                                        </td>

                                        <!-- Time -->
                                        <td class="align-middle p-2">
                                            <template v-if="getScheduleGroupsForSubject(app.subj_id).length">
                                                <div v-for="(g, i) in getScheduleGroupsForSubject(app.subj_id)" :key="i">
                                                    {{ g.start }} - {{ g.end }}
                                                </div>
                                            </template>
                                            <span v-else>TBA</span>
                                        </td>

                                        <!-- Room -->
                                        <td class="align-middle p-2">
                                            <template v-if="getScheduleGroupsForSubject(app.subj_id).length">
                                                <div v-for="(g, i) in getScheduleGroupsForSubject(app.subj_id)" :key="i">
                                                    {{ g.room }} <span v-if="g.building">- {{ g.building }}</span>
                                                </div>
                                            </template>
                                            <span v-else>TBA</span>
                                        </td>
                                        <!-- <td class="align-middle">
                                            {{ app.lf_lnid }}
                                            </td> -->
                                    </tr>
                                    <tr v-if="!Object.keys(groupedAssignmentSection).length && !loadingSchedule && !Object.keys(scheduleData).length">
                                        <td class="align-middle" colspan="7">
                                            No Records Found
                                        </td>
                                    </tr>
                                    <tr v-if="loadingSchedule && !Object.keys(scheduleData).length">
                                        <td class="align-middle" colspan="7">
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