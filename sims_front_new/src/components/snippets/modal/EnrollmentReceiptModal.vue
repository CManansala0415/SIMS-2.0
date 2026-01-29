<script setup>
import { ref, onMounted, computed } from 'vue';
import { getUserID } from "../../../routes/user.js";
import {
    getCurriculumSubject,
    getEnrollment,
    getMilestone,
    addMilestone,
    updateEnrollment,
    updateMilestone,
    getCommandUpdateCurriculum,
    getEnrollmentSchedule,
    getLaunchChecker,
    getPaymentDetails,
    getTotalCharges,
    getStudentAccount,
    getScholarshipDetails
} from "../../Fetchers.js";
import Loader from '../loaders/Loader1.vue';
import {
    pdfGenerator,
    pesoConverter
} from "../../Generators.js";
import { useRouter, useRoute } from 'vue-router'

const router = useRouter();
const route = useRoute();
const path = computed(() => route.path)

const props = defineProps({
    student: {
    },
    curriculum: {
    },
    section: {
    },
    subject: {
    }
})


const emit = defineEmits(['close-modal'])
const close = (data) => {
    window.stop()
    emit('close-modal')
}

const studentData = computed(() => {
    return props.student
});
const curriculumData = computed(() => {
    return props.curriculum
});
const sectionData = computed(() => {
    return props.section
});
const subjectData = computed(() => {
    return props.subject
});

const userID = ref('')
const subjectFilter = ref([])
const preloading = ref(true)
const milestoneLoading = ref(true)
const currSubject = ref([])
const enrolleeData = ref([])
const milestone = ref([])
const milestoneSubject = ref([])
const enr_section = ref('')
const enr_curriculum = ref('')
const settingscurr = ref([])
const studentID = ref('')
const studentSem = ref('')
const studentSemId = ref('')
const studentDateEnr = ref('')
const scheduleData = ref([])
const grandTotal = ref(0)
const paymentDetails = ref([])
const chargeBreakdown = ref([])
const totalPayment = ref(0)

const booter = async () => {
   
    
}
onMounted(async () => {
    window.stop()
    enr_section.value = studentData.value.enr_section
    // enr_curriculum.value = studentData.value.enr_curriculum
    subjectFilter.value = subjectData.value
    sectionFilter.value = sectionData.value
    curriculumFilter.value = curriculumData.value

    try {
        preloading.value = true
        milestoneLoading.value = true

        await booter().then(() => {
            getUserID().then((results) => {
                userID.value = results.account.data.id
            })


            getEnrollment(studentData.value.per_id).then((results) => {
                enrolleeData.value = results
                // for printing
                studentID.value = results[0].ident_identification
                studentSem.value = results[0].quar_code
                studentSemId.value = results[0].quar_id
                studentDateEnr.value = results[0].enr_dateenrolled

                let curr = enrolleeData.value[0].enr_curriculum
                let prog = enrolleeData.value[0].enr_program
                let grad = enrolleeData.value[0].enr_gradelvl
                let cour = enrolleeData.value[0].enr_course
                
                getCommandUpdateCurriculum(prog, grad, cour).then((results) => {
                    settingscurr.value = results
                    //if wala pa syang saved curriculum, automatic na base yung default sa settings na curriculum
                    if(!curr){
                        if (Object.keys(results).length == 0) {
                            curr = 0
                        } else {
                            curr = settingscurr.value[0].sett_course_currid
                        }
                    }

                    // if(!enrolleeData.value[0].enr_curriculum){
                    //    curr = settingscurr.value[0].sett_course_currid
                    // }

                    enr_curriculum.value = curr
                    getCurriculumSubject(curr, 0, 0).then((results) => {
                        currSubject.value = results

                        // para to dun sa required subjects list ng milestones page
                        milestoneSubject.value = currSubject.value.filter(e => {
                            if (
                                (e.currtag_gradelvl == enrolleeData.value[0].enr_gradelvl) &&
                                (e.currtag_sem == enrolleeData.value[0].enr_quarter)
                            ) {
                                return e
                            }
                        })
                        // ginamit naten yung filtered by grade lvl and sem type para tama mag reflect sa list ng curriculum current subjects
                        milestoneSubject.value.forEach((e) => {
                            addedSubject.value.push(e)
                            addedSubjectId.value.push(e.subj_id)
                        })

                        getMilestone(studentData.value.enr_id).then((results) => {
                            milestone.value = results
                            milestone.value.forEach((e) => {
                                addedSubject.value.push(e)
                                addedSubjectId.value.push(e.subj_id)
                            })
                                // console.log('program: ', prog)
                                // console.log('semester: ', studentSemId.value)
                                // console.log('course: ', cour)
                                // console.log('gradelevel: ', grad)
                                // console.log('curriculum: ', curr)
                                // console.log('section: ', enr_section.value)

                            getLaunchChecker(
                                prog,
                                studentSemId.value,
                                cour,
                                grad,
                                curr,
                                enr_section.value
                            ).then(async (results1) => {

                                // console.log(results1)
                                getEnrollmentSchedule(curr, prog, grad, cour, enr_section.value, results1.ln_id).then((results2) => {
                                    scheduleData.value = results2.data
                                    console.log(scheduleData.value)
                                    preloading.value = false
                                    milestoneLoading.value = false

                                    getPaymentDetails(enrolleeData.value[0].acs_id, 1).then((results) => {
                                        let x = results.data.slice(-1).pop()
                                        paymentDetails.value = results.data
                                        grandTotal.value = typeof x !== 'undefined' ? x.acy_balance : enrolleeData.value[0].acs_amount

                                        results.data.forEach((e) => {
                                            totalPayment.value += Number(e.acy_payment)
                                        })
                                    })

                                    getTotalCharges(
                                        enrolleeData.value[0].enr_curriculum, 
                                        enrolleeData.value[0].enr_quarter, 
                                        enrolleeData.value[0].enr_program, 
                                        enrolleeData.value[0].enr_course, 
                                        enrolleeData.value[0].enr_gradelvl, 
                                        enrolleeData.value[0].enr_section, 
                                        enrolleeData.value[0].enr_id,
                                        enrolleeData.value[0].enr_personid
                                    ).then((results) => {
                                        chargeBreakdown.value = results
                                        console.log(chargeBreakdown.value)
                                    })
                                })
                            })


                        })

                    })
                })
            })
        })

    } catch (err) {
        // alert('error loading the list default components')
        Swal.fire({
            icon: "error",
            title: "Oops...",
            text: "Something went wrong!",
            footer: '<a href="#" disabled>Have you checked your internet connection?</a>'
        }).then(() => {
            // preLoading.value = false
        });
    }
})

const loadCurrItems = ref(false)
const monitorCurriculum = (data) => {
    loadCurrItems.value = true
    currSubject.value = []
    addedSubject.value = []
    addedSubjectId.value = []
    getCurriculumSubject(data, 0, 0).then(async (results) => {
        currSubject.value = results
        currSubject.value = currSubject.value.filter(e => {
            if (
                (e.currtag_gradelvl == studentData.value.enr_gradelvl) &&
                (e.currtag_sem == studentData.value.enr_quarter)
            ) {
                return e
            }
        })

        for await (const e of currSubject.value) {
            addedSubject.value.push(e)
        };

        loadCurrItems.value = false
    })
}

const searchSubject = ref('')
const filteredSubject = () => {
    subjectFilter.value = subjectData.value.filter(e => {
        if (
            (e.subj_code.toUpperCase().includes(searchSubject.value.toUpperCase())) ||
            (e.subj_name.toUpperCase().includes(searchSubject.value.toUpperCase()))
        ) {
            return e
        }
    })

}

const addedSubject = ref([])
const addedSubjectId = ref([])
const addSubject = (data) => {
    let exist = addedSubject.value.filter(e => {
        if (e.subj_id == data.subj_id) {
            return e
        }
    })

    Object.keys(exist).length ?
        Swal.fire({
            title: "Duplicate Detected",
            text: "Already included, try selecting another",
            icon: "question"
        }) : addedSubject.value.push(data)
    Object.keys(exist).length ? false : addedSubjectId.value.push(data.subj_id)

}
const removeSubject = (index) => {
    addedSubject.value.splice(index, 1)
    addedSubjectId.value.splice(index, 1)
}

const showMilestone = ref(true)

const setData = (type, data) => {
    switch (type) {
        case 'curriculum':
            monitorCurriculum(data)
            enr_curriculum.value = data
            break;
        case 'section':
            enr_section.value = data
            break;
    }
}

const saving = ref(false)
const saveData = async () => {
    saving.value = true
    addedSubject.value = addedSubject.value.map(itemObj => {
        return {
            ...itemObj,
            enr_id: studentData.value.enr_id,
            user_id: userID.value,
            mi_tag: itemObj.mi_tag ? itemObj.mi_tag : '',
            mi_crossenr: itemObj.mi_crossenr ? itemObj.mi_crossenr : ''
        }
    })

    // let counter = 0
    // addedSubject.value.forEach(async (e) => {
    //     addMilestone(e).then((results) => {
    //         counter+=1
    //         counter==Object.keys(addedSubject.value).length? saving.value = false:saving.value = true
    //     })
    // })

    let counter = 0
    let x = {
        ...studentData.value,
        enr_updatedby: userID.value,
        enr_section: enr_section.value,
        enr_curriculum: enr_curriculum.value
    }

    for await (const e of addedSubject.value) {
        counter += 1
        addMilestone(e).then((results) => { })
    }
    if (counter == Object.keys(addedSubject.value).length) {
        updateEnrollment(x).then((results) => {
            // alert('Tagging Successful')
            // //router.replace({ name: 'Academics', params: { id: 2}});
            // location.reload()
            // saving.value = false
            Swal.fire({
                title: "Tagging Successful",
                text: "Changes applied, refreshing the page",
                icon: "success"
            }).then(() => {
                location.reload()
            });
        })
    }


}


const deleteSubject = (id, index) => {
    let x = {
        mi_id: id,
        mi_status: 0,
        mi_updatedby: userID.value
    }
    updateMilestone(x).then((results) => {
        // alert('Delete Successful')
        // milestone.value.splice(index, 1)
        // location.reload()

        Swal.fire({
            title: "Delete Successful",
            text: "Changes applied, refreshing the page",
            icon: "success"
        }).then(() => {
            milestone.value.splice(index, 1)
            location.reload()
        });
    })
}



const searchSection = ref('')
const sectionFilter = ref([])
const filterSection = () => {
    sectionFilter.value = sectionData.value.filter(e => {
        if (
            (e.sec_name.toUpperCase().includes(searchSection.value.toUpperCase())) ||
            (e.sec_code.toUpperCase().includes(searchSection.value.toUpperCase()))
        ) {
            return e
        }
    })

}

const searchCurriculum = ref('')
const curriculumFilter = ref([])
const filterCurriculum = () => {
    curriculumFilter.value = curriculumData.value.filter(e => {
        if (e.curr_code.toUpperCase().includes(searchCurriculum.value.toUpperCase())) {
            return e
        }
    })

}

const downloadPdf = () => {
    // var element = document.getElementById('printform');
    // html2pdf(element);
    let name = studentID.value + '_' + studentSem.value + '_' + studentDateEnr.value.split(" ")[0]
    pdfGenerator(name, 'a4', 'portrait', 0.1)
    Swal.fire({
        icon: "success",
        title: "Download Complete",
        text: "Check your file manager, refreshing the page",
    }).then(() => {
        location.reload()
    });

}

const formType = ref(1)

// Chat GPT Helper

function timeToMinutes(raw) {
    // raw example: "0600A", "0130P"
    let hh = parseInt(raw.slice(0, 2), 10)
    const mm = parseInt(raw.slice(2, 4), 10)
    const mer = raw.slice(4) // A or P

    if (mer === "P" && hh !== 12) hh += 12
    if (mer === "A" && hh === 12) hh = 0

    return hh * 60 + mm
}


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

    // entries.sort((a, b) => a.dayOrder - b.dayOrder || a.rawStart.localeCompare(b.rawStart))
    entries.sort((a, b) =>
        a.dayOrder - b.dayOrder ||
        timeToMinutes(a.rawStart) - timeToMinutes(b.rawStart)
    )

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
            // e.rawStart === prev.rawEnd
            timeToMinutes(e.rawStart) === timeToMinutes(prev.rawEnd)

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
    <div class="col-12 border-bottom" v-if="!milestoneLoading && Object.keys(milestone).length">
        <div class="p-3 w-100 d-flex justify-content-center">
            <select v-model="formType" class="form-select w-50">
                <option value="1">Registrar</option>
                <option value="2">Cashier</option>
            </select>
        </div>
    </div>
    <div id="printform" v-if="!milestoneLoading && Object.keys(milestone).length" class="d-flex justify-content-center">
        <div class="border small-font bg-opaque h-100"
            style="width: 770px; height: 1105px; border:2px solid black; font-size: 8.8px;">
            <table class="table table-fixed" style="text-transform:uppercase">
                <thead>
                    <tr>
                        <th class="align-middle">
                            <img src="/img/clcst_logo.png" height="60px" width="60px" alt="...">
                        </th>
                        <th class="align-middle text-center">
                            <p class="m-0">CENTRAL LUZON COLLEGE OF SCIENCE AND TECHNOLOGY, INC.
                                CELTECH COLLEGE</p>
                            <p class="m-0 fw-normal small-font">B. Mendoza St., Brgy. Sto. Rosario, City of San
                                Fernando,
                                Pampanga, Philippines, 2000</p>
                            <p class="m-0 fw-normal small-font">Tel. Nos: (045) 435-1495</p>
                            <p class="m-0 fw-normal small-font">Founded 1959</p>
                        </th>
                        <th class="align-middle"><img src="/img/clcst_logo.png" height="60px" width="60px" alt="...">
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="3">
                            <div class="d-flex flex-column text-center w-100">
                                <span class="fw-bold">CERTIFICATE OF REGISTRATION</span>
                                <span v-if="formType == 1" class="fw-bold">REGISTRAR'S COPY</span>
                                <span v-if="formType == 2" class="fw-bold">CASHIER'S COPY</span>
                                <span>No. SF-12606</span>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="align-middle" colspan="3">
                            <table class="table" v-for="(e, index) in enrolleeData">
                                <tbody>
                                    <tr class="text-start">
                                        <td class="p-2 border" colspan="2">
                                            <span style="text-transform:none">Name: </span>
                                            <span class="fw-semibold">
                                                {{ studentData.per_firstname }} 
                                                {{ studentData.per_middlename ? studentData.per_middlename : ' ' }}
                                                {{ studentData.per_lastname }}
                                                {{ studentData.per_suffixname ? studentData.per_suffixname : ' ' }}
                                            </span>
                                        </td>
                                        <td class="p-2 border">
                                            <span style="text-transform:none">Student ID: </span>
                                            <span class="fw-semibold">
                                                {{ e.ident_identification }}
                                            </span>
                                        </td>
                                        <td class="p-2 border">
                                            <span style="text-transform:none">Date: </span>
                                            <span class="fw-semibold">
                                                {{ e.enr_dateenrolled }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr class="text-start">
                                        <td class="p-2 border">
                                            <span style="text-transform:none">Course: </span>
                                            <span class="fw-semibold">
                                                {{ e.prog_name }}
                                            </span>
                                        </td>
                                        <td class="p-2 border">
                                            <span style="text-transform:none">Grade/Year: </span>
                                            <span class="fw-semibold">
                                                {{ e.grad_name }}
                                            </span>
                                        </td>
                                        <td class="p-2 border">
                                            <span style="text-transform:none">Section: </span>
                                            <span class="fw-semibold">
                                                {{ e.sec_name ? e.sec_name : 'N/A' }}
                                            </span>
                                        </td>
                                        <td class="p-2 border">
                                            <span style="text-transform:none">Semester: </span>
                                            <span class="fw-semibold">
                                                {{ e.quar_desc }}
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="text-center w-100">
                <p class="fw-bold">Subjects Enrolled</p>
            </div>
            <table class="table table-fixed" style="text-transform:uppercase">
                <thead>
                    <tr>
                        <th style="background-color: #000000;" class="text-white">Code</th>
                        <th style="background-color: #000000; width: 120px;" class="text-white">Subject</th>
                        <th style="background-color: #000000;" class="text-white">Units</th>
                        <th style="background-color: #000000;" class="text-white">Days</th>
                        <th style="background-color: #000000;" class="text-white">Time</th>
                        <th style="background-color: #000000;" class="text-white">Room</th>
                        <th style="background-color: #000000; width: 120px;" class="text-white">Faculty</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(c, index) in milestone" :key="index">
                        <!-- Code -->
                        <td class="align-middle p-2">
                            {{ c.subj_code }}
                        </td>

                        <!-- Subject -->
                        <td class="align-middle p-2">
                            {{ c.subj_name }}
                        </td>

                        <!-- Units -->
                        <td class="align-middle p-2">
                            Lec: {{ c.subj_lec_units }} | Lab: {{ c.subj_lab_units }} | Total: {{ c.subj_lec_units + c.subj_lab_units }}
                        </td>

                        <!-- Days -->
                        <td class="align-middle p-2">
                            <template v-if="getScheduleGroupsForSubject(c.subj_id).length">
                                <div v-for="(g, i) in getScheduleGroupsForSubject(c.subj_id)" :key="i">
                                    {{ g.day }}
                                </div>
                            </template>
                            <span v-else>TBA</span>
                        </td>

                        <!-- Time -->
                        <td class="align-middle p-2">
                            <template v-if="getScheduleGroupsForSubject(c.subj_id).length">
                                <div v-for="(g, i) in getScheduleGroupsForSubject(c.subj_id)" :key="i">
                                    {{ g.start }} - {{ g.end }}
                                </div>
                            </template>
                            <span v-else>TBA</span>
                        </td>

                        <!-- Room -->
                        <td class="align-middle p-2">
                            <template v-if="getScheduleGroupsForSubject(c.subj_id).length">
                                <div v-for="(g, i) in getScheduleGroupsForSubject(c.subj_id)" :key="i">
                                    {{ g.room }} <span v-if="g.building">- {{ g.building }}</span>
                                </div>
                            </template>
                            <span v-else>TBA</span>
                        </td>

                        <!-- Faculty -->
                        <td class="align-middle p-2">
                            <template v-if="getScheduleGroupsForSubject(c.subj_id).length">
                                <div v-for="(g, i) in getScheduleGroupsForSubject(c.subj_id)" :key="i">
                                    {{ g.faculty }}
                                </div>
                            </template>
                            <span v-else>TBA</span>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="d-flex flex-column justify-content-center align-items-center" style="font-size:8px">
                <div class="w-75 p-1 ">
                    <span class="fst-italic">
                        I shall abide by all the rules and regulations now enforced or may be promulgated by Central
                        Luzon
                        College of Science and Technology, Inc. from time to time
                        Likewise, I agree to the cancellation of the credits I have earned in subjects I have enrolled
                        under
                        false pretenses.
                    </span>
                </div>
                <div
                    class="p-1 d-flex flex-column gap-1 justify-content-center align-content-center align-items-center border border-dark-subtle rounded bg-body-tertiary shadow-sm">
                    <span class="text-danger fw-bold mt-1"> IMPORTANT </span>
                    <span class="fw-bold mt-1">
                        CERTIFICATE OF REGISTRATION - means officially enrolled subjects and it will serve as an admission slip
                        to the
                        classroom.
                    </span>
                    <span class="fw-bold fa-underline mt-1  border-0 border-bottom border-dark-subtle">
                        * CHARGES TO STUDENTS WITHDRAWING/ DROPPING *
                    </span>
                    <ul class="text-start">
                        <li>
                            <strong>Before the start of classes:</strong> charged the full amount of the registration
                            fee, School ID, report cards, etc., <em>plus</em> <strong>10% of the total tuition
                                fees</strong>.
                        </li>
                        <li>
                            <strong>Within the first week of classes:</strong> charged an amount equal to <strong>30% of
                                the total charges for the whole semester</strong>, regardless of whether or not he has
                            actually attended classes.
                        </li>
                        <li>
                            <strong>Within the second week of classes:</strong> charged an amount equal to <strong>80%
                                of the total charges for the whole semester</strong>, regardless of whether or not he
                            has actually attended classes.
                        </li>
                        <li>
                            <strong>After the second week of classes:</strong> charged the <strong>full amount due for
                                the whole semester</strong>, regardless of whether or not he has actually attended
                            classes.
                        </li>
                    </ul>
                    <small>
                        Note: This statement serves as the official policy regarding
                        withdrawal/drop charges. For questions, contact the Registrar's Office.
                    </small>

                    <div class="d-flex gap-2 w-75 justify-content-between mt-3">
                        <div class="w-100 text-center">
                            <div style="height:1px; background:#808080;"></div>
                            <p style="font-size:8px; color:#374151;">Student Signature over
                                Printed Name</p>
                        </div>
                        <div class="w-100 text-center">
                            <div style="height:1px; background:#808080;"></div>
                            <p style="font-size:8px; color:#374151;">Registrar / Authorized
                                Signature</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex flex-column justify-content-center align-items-center mt-3" style="font-size:8px"
                v-if="formType == 1">
                <div class="d-flex flex-column gap-1 justify-content-center align-content-center align-items-center">
                    <span class="fw-bold mt-1">
                        STUDENT AFFAIRS OFFICE
                    </span>
                    <span class="fw-bold fa-underline mt-1  border-0 border-bottom border-dark-subtle">
                        UNDERTAKING
                    </span>
                    <span class="w-75 p-1 fst-italic" style="text-align: justify;">
                        I, <span class="fw-semibold text-uppercase">
                            {{ studentData.per_firstname }}
                            {{ studentData.per_middlename ? studentData.per_middlename : ' ' }}
                            {{ studentData.per_lastname }}
                            {{ studentData.per_suffixname ? studentData.per_suffixname : ' ' }}
                        </span>
                        <span class="fw-semibold text-uppercase">
                            {{ enrolleeData[0].prog_code }}-{{ enrolleeData[0].grad_code }}
                        </span>, do hereby state that I am NOT A MEMBER OF ANY FRATERNITY/ SORORITY or any UNDERGROUND
                        OR ILLEGAL
                        ORGANIZATION OR AGGRUPATION THAT ADVOCATES VIOLENCE. ERODES THE VALUES AND TEACHINGS OF CLCST,
                        and/ or
                        UNDERMINES THE SAFETY AND SECURITY OF THE SCHOOL AND IT'S STUDENTS AND EMPLOYEES.
                        I also state that I shall abide with all College policies and regulations and shall subject
                        myself to
                        disciplinary procedure in accordance with the
                        School's existing Student Handbook should I commit any violation as prescribed in the said
                        Student Handbook
                        and other duly issued policies.
                        I am executing this Undertaking as a necessary condition and/ or requisite for my admission and
                        continuance
                        of study in Central Luzon College of Science and Technology, Inc.. I fully understand that if I
                        should
                        commit any violation of this Undertaking, I shall be disqualified for admission/ readmission
                        without
                        prejudice to the right of the School to initiate
                        <span class="fw-bold">CRIMINAL</span> <span class="fw-bold">CIVIL</span> and/ or <span
                            class="fw-bold">ADMINISTRATIVE</span> charges against me.
                        Done this 27th day of August, 20 25 at Central Luzon College of Science and Technology, Inc., .
                    </span>
                    <small>
                        Note: This statement serves as the official policy regarding
                        withdrawal/drop charges. For questions, contact the Registrar's Office.
                    </small>

                    <div class="d-flex gap-2 w-75 justify-content-between mt-3">
                        <div class="w-100 text-center">
                            <div style="height:1px; background:#808080;"></div>
                            <p style="font-size:8px; color:#374151;">Student Signature over
                                Printed Name</p>
                        </div>
                        <div class="w-100 text-center">
                            <div style="height:1px; background:#808080;"></div>
                            <p style="font-size:8px; color:#374151;">Parent / Guardian</p>
                        </div>
                    </div>

                </div>
            </div>
            <div class="w-100 p-2 d-flex gap-2 justify-content-around" v-if="formType == 2" style="height: 180px;">
                <div class="w-100 border bg-body-secondary">
                    <span class="fw-bold">Payments (5 latest Transaction)</span>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <td class="p-1">Date</td>
                                <td class="p-1">OR No.</td>
                                <td class="p-1">Amount</td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(pay, index) in paymentDetails.slice(-5)" :key="index">
                                <td class="p-1">{{ pay.acy_datepaid }}</td>
                                <td class="p-1">
                                    {{ pay.acy_series_prefix }}-{{ pay.acy_series_year }}-{{ pay.acy_series_pattern }}
                                </td>
                                <td class="p-1">{{ pesoConverter(pay.acy_payment) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="w-100 p-2">
                    
                    <!-- <div class="d-flex gap-1 justify-content-between">
                        <span>Total Subject Fees</span>
                        <span class="fw-bold">{{ chargeBreakdown.subjects_amount?  pesoConverter(chargeBreakdown.subjects_amount) : 0.00 }}</span>
                    </div> -->
                    <div class="d-flex gap-1 justify-content-between">
                        <span>Total lecture Fees</span>
                        <span class="fw-bold">{{ chargeBreakdown.misc_amount?  pesoConverter(chargeBreakdown.lec_amount) : 0.00 }}</span>
                    </div>
                    <div class="d-flex gap-1 justify-content-between">
                        <span>Total Laboratory Fees</span>
                        <span class="fw-bold">{{ chargeBreakdown.misc_amount?  pesoConverter(chargeBreakdown.lab_amount) : 0.00 }}</span>
                    </div>
                    <div class="d-flex gap-1 justify-content-between">
                        <span>Total  Item/Miscellaneous Fees</span>
                        <span class="fw-bold">{{ chargeBreakdown.item_amount?  pesoConverter(chargeBreakdown.item_amount) : 0.00 }}</span>
                    </div>
                    <div class="d-flex gap-1 justify-content-between">
                        <span>Total Other Charges</span>
                        <span class="fw-bold">{{ chargeBreakdown.misc_amount?  pesoConverter(chargeBreakdown.misc_amount) : 0.00 }}</span>
                    </div>
                   
                    <div class="d-flex gap-1 justify-content-between mt-2 border-top pt-2">
                        <span>Total Tuition</span>
                        <span class="fw-bold">{{ enrolleeData[0].acs_amount?  pesoConverter(enrolleeData[0].acs_amount) : 0.00 }}</span>
                    </div>
                     <div class="d-flex gap-1 justify-content-between">
                        <span>Total Deductions</span>
                        <span class="fw-bold"> - {{ pesoConverter(chargeBreakdown.deductions) }}</span>
                    </div>
                    <div class="d-flex gap-1 justify-content-between mt-2 border-top pt-2">
                        <span>Remaining Balance</span>
                        <span class="fw-bold">{{  chargeBreakdown.overall_amount ? pesoConverter(chargeBreakdown.overall_amount - totalPayment) :enrolleeData[0].acs_amount }}</span>
                    </div>
                    <!-- <div class="d-flex gap-1 justify-content-between">
                        <span>Mode of Payment</span>
                        <span class="fw-bold">INSTALLMENT</span>
                    </div> -->
                    <!-- <div class="d-flex gap-1 justify-content-between">
                        <span>Prelim</span>
                        <span class="fw-bold">0.00</span>

                    </div>
                    <div class="d-flex gap-1 justify-content-between">
                        <span>Midterm</span>
                        <span class="fw-bold">0.00</span>

                    </div>
                    <div class="d-flex gap-1 justify-content-between">
                        <span>Pre-Final</span>
                        <span class="fw-bold">0.00</span>

                    </div>
                    <div class="d-flex gap-1 justify-content-between">
                        <span>Final</span>
                        <span class="fw-bold">0.00</span>
                    </div> -->
                    <div class="text-center mt-1 fst-italic">
                        <span class="fw-bold">Note: </span>
                        <span>Above fees exclude other applicable charges to be paid at cashiers office.</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div v-if="!milestoneLoading && !Object.keys(milestone).length" style="text-transform:none">
        <div class="p-3 text-center" style="text-transform:none">
            No Records Found
        </div>
    </div>
    <div v-if="milestoneLoading && !Object.keys(milestone).length" style="text-transform:none">
        <div class="p-3 text-center" style="text-transform:none">
            <div class="m-3">
                <Loader />
            </div>
        </div>
    </div>


    <button class="btn btn-success w-100 mt-3" @click="downloadPdf()"
        v-if="!milestoneLoading && Object.keys(milestone).length"
        v-show="!milestoneLoading && Object.keys(milestone).length">Download Form</button>

</template>