<script setup>
import { ref, onMounted, computed, nextTick } from 'vue';
import { getUserID } from "../../../routes/user";
import Loader from '../loaders/Loading1.vue';
import LoaderSmall from '../loaders/Loader1.vue';

import {
    editAccountingTuition,
    getFeeDetails,
    getSubject,
    saveTuitionTemplate,
    getTuitionTemplate,
    getSubjectsFromRegistrar,
    getQuarter,
    getGradelvl,
    getCurriculumSett

} from "../../Fetchers.js";

import {
    pesoConverter
} from "../../Generators.js";

import { useRouter, useRoute } from 'vue-router';
const router = useRouter();

const props = defineProps({
    itemData: {
    },
    courseData: {
    },
    sectionData: {
    },
    programData: {
    },
    gradelvlData: {
    },
    manageSetupData: {
    },
    quarterData: {
    },
    activeEnrollmentData: {
    },
})
const item = computed(() => {
    return props.itemData
});

const course = computed(() => {
    return props.courseData
});
const section = computed(() => {
    return props.sectionData
});
const program = computed(() => {
    return props.programData
});
const gradelvl = computed(() => {
    return props.gradelvlData
});
const manageSetup = computed(() => {
    return props.manageSetupData
});
const quarter = computed(() => {
    return props.quarterData
});
const activeEnrollment = computed(() => {
    return props.activeEnrollmentData
});

const userID = ref('')
const actDescription = ref('')
const actCourse = ref(0)
const actProgram = ref('')
const actType = ref('')
const actSection = ref('')
const actGradelvl = ref('')
const actSem = ref('')
const actId = ref('')
const actCurriculum = ref('')
const saving = ref(false)
const mode = ref('')

const miscFees = ref([])
const subjFees = ref([])
const customFees = ref([])
const miscFeesMain = ref([])
const subjectsData = ref([])
const miscFeesMainFiltered = ref([])
const subjectsDataFiltered = ref([])
const miscFeesMainSearch = ref('')
const subjectsDataSearch = ref('')
const preLoading = ref(true)
const customPrice = ref(0)
const customDescription = ref('')
const tuitionTemplateItems = ref([])
const tuitionTemplateHolder = ref([])
const registrarSubjects = ref([])
const chargeType = ref(0)
const registrarSubjectsFiltered = ref([])
const registrarSubjectsSearch = ref('')
const periodMap = ref({})
const gradeMap = ref({})
const groupedSubjects = ref([])
const curriculumData = ref([])
const curriculumHolder = ref([])

const booter = async () => {
    getFeeDetails(0, 0).then((result) => {
        miscFeesMain.value = result.data
        miscFeesMainFiltered.value = miscFeesMain.value
    })
    getSubject().then((result) => {
        subjectsData.value = result
        subjectsDataFiltered.value = subjectsData.value
    })
    getTuitionTemplate(item.value.act_id).then((results) => {
        tuitionTemplateItems.value = results.data
        tuitionTemplateHolder.value = results.data // for comparison if may nadelete or nadagdag
    })
    getSubjectsFromRegistrar(item.value.act_course, item.value.act_gradelvl, item.value.act_sem, item.value.act_curriculum).then((results) => {
        registrarSubjects.value = results.data
        registrarSubjectsFiltered.value = results.data
    })
    getQuarter().then((results) => {
        let map = {}
        results.forEach(p => {
            map[p.quar_id] = p.quar_desc
        })
        periodMap.value = map
    })
    getGradelvl().then((results) => {
        let map = {}
        results.forEach(g => {
            map[g.grad_code] = g.grad_name
        })
        gradeMap.value = map
    })
    getCurriculumSett().then((results) => {
        curriculumHolder.value = results
        curriculumData.value = results
    })

}

// run this kapag tuition ang usapan and settings ng fees na
const setupLimiter = () => {
    if (chargeType.value == 1 && Object.keys(tuitionTemplateItems.value).length <= 0) {
        registrarSubjects.value.forEach((e) => {
            addMiscFee(e, 2)
        })
    }

    tuitionTemplateItems.value.forEach((e) => {
        if (e.tuitemp_itemid) {
            miscFees.value.push(e)
        } else if (e.tuitemp_subjid) {
            subjFees.value.push(e)
        } else {
            customFees.value.push(e)
        }
    })

    groupedSubjects.value = subjFees.value.reduce((acc, item) => {
        let key = `${item.tuitemp_gradelvl}-${item.tuitemp_sem}`;

        if (!acc[key]) {
            acc[key] = [];
        }

        acc[key].push(item); 
        return acc;
    }, {});


}

onMounted(async () => {
    await booter().then(() => {
        getUserID().then((results) => {
            userID.value = results.account.data.id

            chargeType.value = item.value.act_type // 1 tuition 2 charge

            if (manageSetup.value === true) {
                setupLimiter()
                saveMode.value = 'assess'
                manageDetails()
            }



            preLoading.value = false

        }).catch((err) => {
            Swal.fire({
                icon: "error",
                title: "Oops...",
                text: "Session expired, log in again",
            }).then(() => {
                router.push("/");
                location.reload()
                window.stop()
            });
        })

        if (Object.keys(item.value).length) {
            actId.value = item.value.act_id
            actSem.value = item.value.act_sem
            actDescription.value = item.value.act_description
            actCourse.value = item.value.act_course
            actProgram.value = item.value.act_program
            actType.value = item.value.act_type
            actSection.value = item.value.act_section
            actGradelvl.value = item.value.act_gradelvl
            actCurriculum.value = item.value.act_curriculum
            mode.value = 'edit'
        } else {
            actId.value = '0'
            actDescription.value = ''
            actCourse.value = '0'
            actProgram.value = '0'
            actSem.value = '0'
            actType.value = '0'
            actSection.value = '0'
            actGradelvl.value = '0'
            actCurriculum.value = '0'
            mode.value = 'new'
        }
    })
})


const searchItem = (type) => {

    if (type == 1) {
        miscFeesMainFiltered.value = miscFeesMain.value.filter((e) => {
            if (
                (e.acf_desc.toUpperCase().includes(miscFeesMainSearch.value.toUpperCase()))
            ) {
                return e
            }
        })

    } else if (type == 2) {
        subjectsDataFiltered.value = subjectsData.value.filter((e) => {
            if (
                (e.subj_code.toUpperCase().includes(subjectsDataSearch.value.toUpperCase())) ||
                (e.subj_name.toUpperCase().includes(subjectsDataSearch.value.toUpperCase()))
            ) {
                return e
            }
        })
    } else {
        registrarSubjectsFiltered.value = registrarSubjects.value.filter((e) => {
            if (
                (e.subj_code.toUpperCase().includes(registrarSubjectsSearch.value.toUpperCase())) ||
                (e.subj_name.toUpperCase().includes(registrarSubjectsSearch.value.toUpperCase()))
            ) {
                return e
            }
        })
    }
    // console.log(type)
}

const saveCharges = () => {
    saving.value = true

    Swal.fire({
        title: "Saving Updates",
        text: "Please wait while we check all necessary details.",
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    let x = {
        act_description: actDescription.value,
        act_course: actCourse.value,
        act_program: actProgram.value,
        act_section: actSection.value,
        act_type: actType.value,
        act_gradelvl: actGradelvl.value,
        act_user: userID.value,
        act_mode: mode.value,
        act_id: actId.value,
        act_sem: actSem.value,
        act_curriculum: actCurriculum.value

    }

    editAccountingTuition(x).then((results) => {
        Swal.close()

        if (results.status == 200) {
            Swal.fire({
                title: "Update Successful",
                text: "Changes applied, refreshing the page",
                icon: "success"
            }).then(() => {
                location.reload()
            });
        } else {
            Swal.fire({
                title: "Update Failed",
                text: "Unknown error occured, try again later",
                icon: "error"
            }).then(() => {
                // location.reload()
            });
        }
    })
}

const addMiscFee = (data, type) => {

    if (type == 1) {

        let x = {
            tuitemp_headerid: item.value.act_id,
            tuitemp_id: '',
            tuitemp_itemid: data.acf_id,
            tuitemp_subjid: '',
            tuitemp_custid: '',
            tuitemp_desc: data.acf_desc,
            tuitemp_price: data.acf_price,
            tuitemp_quantity: 1, // auto na 1 kapag nag add then pwede customize ilan later
            tuitemp_type: type, // means misc item, 2 for subject and 3 for custom
            tuitemp_user: userID.value
        }

        let check = miscFees.value.filter((e) => {
            if (e.tuitemp_itemid == data.acf_id) {
                return e
            }
        })

        if (!Object.keys(check).length) {
            miscFees.value.push(x)
        } else {

            Swal.fire({
                title: "Duplicate Entry",
                html: `This item is already added to the template. Try selecting another.`,
                icon: "warning",
                confirmButtonText: "Ok, Got it!"
            });
        }

    } else if (type == 2) {
        let x = {
            tuitemp_sem: data.tuitemp_sem ? data.tuitemp_sem : data.currtag_sem,
            tuitemp_gradelvl: data.tuitemp_gradelvl ? data.tuitemp_sem : data.currtag_gradelvl,
            tuitemp_headerid: item.value.act_id,
            tuitemp_id: '',
            tuitemp_itemid: '',
            tuitemp_subjid: data.subj_id,
            tuitemp_custid: '',
            tuitemp_desc: data.subj_name,
            tuitemp_lec: data.subj_lec_units,
            tuitemp_lab: data.subj_lab_units,
            tuitemp_lec_price: data.subj_lec_units_rate,
            tuitemp_lab_price: data.subj_lab_units_rate,
            tuitemp_price: parseFloat(data.subj_lec_units_rate) + parseFloat(data.subj_lab_units_rate),
            tuitemp_type: type, // means misc item, 2 for subject and 3 for custom
            tuitemp_user: userID.value,
            tuitemp_subjcode: data.subj_code
        }

        let check = subjFees.value.filter((e) => {
            if (e.tuitemp_subjid == data.subj_id) {
                return e
            }
        })

        if (!Object.keys(check).length) {
            subjFees.value.push(x)
        } else {
            Swal.fire({
                title: "Duplicate Entry",
                html: `This item is already added to the template. Try selecting another.`,
                icon: "warning",
                confirmButtonText: "Ok, Got it!"
            });
        }
    } else {
        let x = {
            tuitemp_headerid: item.value.act_id,
            tuitemp_id: '',
            tuitemp_itemid: '',
            tuitemp_subjid: '',
            tuitemp_custid: '',
            tuitemp_custype: type == 5 ? 4 : type, // 3 for custom fee, 4 for deduction, 5 for percent discount, gagamitin padin natin si 4 para mag fall si percent as deduction
            tuitemp_quantity: 1, // auto na 1 kapag nag add then pwede customize ilan later
            tuitemp_disc_type: type == 5 ? 1 : 0, // type 5 means percent discount
            tuitemp_desc: customDescription.value,
            tuitemp_price: customPrice.value,
            tuitemp_user: userID.value
        }
        customFees.value.push(x)
    }
}

const subjFeesTotal = ref(0)
const miscFeesTotal = ref(0)
const customFeesTotal = ref(0)
const customDeductionsExact = ref(0)
const customDeductionsPercent = ref(0)
const customDeductionsTotal = ref(0)

const totalFees = ref(0)

const checkCurr = () => {
    curriculumData.value = curriculumHolder.value.filter((e) => {
        if (e.curr_progid == actCourse.value) {
            return e
        }
    })
}

const manageDetails = () => {
    if (saveMode.value == 'clear') {
        miscFees.value = []
        subjFees.value = []
        customFees.value = []
    } else if (saveMode.value == 'assess') {
        miscFeesTotal.value = 0
        subjFeesTotal.value = 0
        customFeesTotal.value = 0
        customDeductionsExact.value = 0
        totalFees.value = 0

        miscFees.value.forEach((price) => {
            miscFeesTotal.value += parseFloat(price.tuitemp_price * price.tuitemp_quantity)
        })

        subjFees.value.forEach((price) => {
            subjFeesTotal.value += parseFloat((price.tuitemp_lec_price || 0) * price.tuitemp_lec)
            subjFeesTotal.value += parseFloat((price.tuitemp_lab_price || 0) * price.tuitemp_lab)
        })

        customFees.value.forEach((price) => {
            if (price.tuitemp_custype == 3) {
                customFeesTotal.value += parseFloat(price.tuitemp_price * price.tuitemp_quantity)
            }
            else {
                if (price.tuitemp_disc_type == 1) {
                    customDeductionsPercent.value += parseFloat(price.tuitemp_price * price.tuitemp_quantity)
                } else {
                    customDeductionsExact.value += parseFloat(price.tuitemp_price * price.tuitemp_quantity)
                }
            }
        })

        totalFees.value = parseFloat(miscFeesTotal.value) + parseFloat(subjFeesTotal.value) + parseFloat(customFeesTotal.value)
            - (parseFloat(customDeductionsExact.value) + ((parseFloat(miscFeesTotal.value) + parseFloat(subjFeesTotal.value) + parseFloat(customFeesTotal.value)) * parseFloat(customDeductionsPercent.value) / 100))
        // console.log(customDeductionsPercent.value)

    } else {
        Swal.fire({
            title: "Saving Updates",
            text: "Please wait while we check all necessary details.",
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        let x = [
            ...(miscFees.value || []),// handles the empty
            ...(subjFees.value || []),// handles the empty
            ...(customFees.value || [])// handles the empty
        ];

        let y = []

        x.forEach((e) => {
            // console.log(e)
            if (e.tuitemp_subjid) {
                let data = tuitionTemplateHolder.value.findIndex((f) => {
                    return f.tuitemp_subjid === e.tuitemp_subjid
                })
                // console.log(data)
                let response = detectMode(data, e.tuitemp_id, e)
                y.push(response)
            }
            else if (e.tuitemp_itemid) {
                let data = tuitionTemplateHolder.value.findIndex((f) => {
                    return f.tuitemp_itemid === e.tuitemp_itemid
                })
                // console.log(data)
                let response = detectMode(data, e.tuitemp_id, e)
                y.push(response)
            }
            else {
                let data = tuitionTemplateHolder.value.findIndex((f) => {
                    return f.tuitemp_custid === e.tuitemp_custid
                })
                // console.log(data)
                let response = detectMode(data, e.tuitemp_id, e)
                y.push(response)
            }
        })

        saveTuitionTemplate(y, 1).then((results) => {
            Swal.close()
            if (results.status == 200) {
                Swal.fire({
                    title: "Update Successful",
                    text: "Changes applied, refreshing the page",
                    icon: "success"
                }).then(() => {
                    location.reload()
                });
            } else {
                Swal.fire({
                    title: "Update Failed",
                    text: "Unknown error occured, try again later",
                    icon: "error"
                }).then(() => {
                    // location.reload()
                });
            }
        })

    }

}

const deleteMiscData = (mode, data, index) => {
    if (data.tuitemp_id) {
        Swal.fire({
            title: 'Remove Item',
            html: `Are you sure to delete this item from the list? This will permanently remove the item from the list`,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, Im Delete it!"
        }).then(async (result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: "Saving Updates",
                    text: "Please wait while we check all necessary details.",
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                saveTuitionTemplate(data, 2).then((results) => {
                    Swal.close()
                    if (results.status == 200) {
                        Swal.fire({
                            title: "Update Successful",
                            text: "Changes applied, refreshing the page",
                            icon: "success"
                        }).then(() => {
                            if (mode == 1) {
                                miscFees.value.splice(index, 1), clickSubmit('assess')
                            } else if (mode == 2) {
                                subjFees.value.splice(index, 1), clickSubmit('assess')
                            } else {
                                customFees.value.splice(index, 1), clickSubmit('assess')
                            }
                        });
                    } else {
                        Swal.fire({
                            title: "Update Failed",
                            text: "Unknown error occured, try again later",
                            icon: "error"
                        }).then(() => {
                            // location.reload()
                        });
                    }
                })
            }
        })
    } else {
        if (mode == 1) {
            miscFees.value.splice(index, 1), clickSubmit('assess')
        } else if (mode == 2) {
            subjFees.value.splice(index, 1), clickSubmit('assess')
        } else {
            customFees.value.splice(index, 1), clickSubmit('assess')
        }
    }


}

const detectMode = (index, id, obj) => {
    var item = {}
    if ((index < 0) && (!id)) { // means wala sa fetched list and wala pang id, means bago and to be inserted as new
        var item = {
            ...obj,
            item_mode: 'insert'
        }
    } else if ((index < 0) && (id)) { // means nasa fetched list and meron ng id, means existing pero tinaggal sa list to be saved
        var item = {
            ...obj,
            item_mode: 'delete'
        }
    } else { // update remaining items to catch also if binago yung price ng subjects or items
        var item = {
            ...obj,
            item_mode: 'update'
        }
    }

    return item
}

const saveMode = ref('')
const clickSubmit = (mode) => {
    saveMode.value = mode
    document.getElementById('submitDetails').click();
};

// const gradeMap = {
//   1: '1st Year',
//   2: '2nd Year',
//   3: '3rd Year',
//   4: '4th Year',
//   5: '5th Year',
//   11: 'Grade 11',
//   12: 'Grade 12'
// }

// const periodMap = {
//   5: 'Semester 1',
//   6: 'Semester 2',
//   7: 'Semester 3',
// }


const resolveGroupLabel = (groupKey) => {
    const [gradeCode, periodCode] = groupKey.split('-')

    const grade = gradeMap.value?.[gradeCode] ?? gradeCode
    const period = periodMap.value?.[periodCode] ?? periodCode

    return `${grade} – ${period}`
}


</script>

<template>
    <div class="p-7" v-if="!manageSetup">
        <div class="d-flex flex-wrap flex-column">
            <p class="text-success fw-bold">Tuition Fee Collection Setup</p>
            <p class="fst-italic border p-2 rounded-3 bg-secondary-subtle small-font"><span class="fw-bold">Note:
                </span><span class="italic">Ensure that the details of the item to be encoded is correct.
                </span></p>
        </div>
        <div class="d-flex flex-column gap-2 small-font">
            <LoaderSmall v-if="preLoading" />
            <div v-else class="w-100">
                <form @submit.prevent="saveCharges" class="card text-start h-100">
                    <div class="d-flex flex-column gap-2 w-100 p-3">
                        <div class="form-group">
                            <label>Template Type</label>
                            <select v-model="actType" class="form-control form-select-sm" required>
                                <option value="" disabled>Select Type</option>
                                <option value="1">Tuition</option>
                                <option value="2">Charges</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Tuition Fee Setup Description</label>
                            <input v-model="actDescription" required type="text" class="form-control form-control-sm"
                                :disabled="actType ? false : true" />
                        </div>
                        <div class="form-group">
                            <label>Semester</label>
                            <select v-model="actSem" class="form-control form-select-sm" required
                                :disabled="actType ? false : true">
                                <option value="0" v-if="actType == 2">All Semesters</option>
                                <option value="" v-else disabled>-- Select Semester -- </option>
                                <option :value="quar.quar_id" v-for="(quar, index) in quarter">{{ quar.quar_desc }}
                                </option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Program</label>
                            <select v-model="actProgram" class="form-control form-select-sm" required
                                :disabled="actType ? false : true">
                                <option value="0" v-if="actType == 2">All Programs</option>
                                <option value="" v-else disabled>-- Select Program -- </option>
                                <option :value="prog.dtype_id" v-for="(prog, index) in program">{{ prog.dtype_desc }}
                                </option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Grade / Year Level</label>
                            <select v-model="actGradelvl" class="form-control form-select-sm"
                                :disabled="actType ? false : true">
                                <option value="0" v-if="actType == 2">All Grade Levels</option>
                                <option value="" v-else disabled>-- Select Grade Level -- </option>
                                <template v-for="(grad, index) in gradelvl">
                                    <option v-if="actProgram == grad.grad_dtypeid" :value="grad.grad_id">{{
                                        grad.grad_name }}</option>
                                </template>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Course</label>
                            <select v-model="actCourse" class="form-control form-select-sm"
                                :disabled="actType ? false : true" @change="checkCurr()">
                                <option value="0" v-if="actType == 2">All Courses</option>
                                <option value="" v-else disabled>-- Select Course -- </option>
                                <template v-for="(cour, index) in course">
                                    <option v-if="actProgram == cour.prog_progtype" :value="cour.prog_id">{{
                                        cour.prog_name }}</option>
                                </template>
                            </select>
                        </div>
                        <div v-if="actType == 1 && actProgram != 0" class="form-group">
                            <label>Curriculum</label>
                            <select class="form-select form-select-sm mt-2" v-model="actCurriculum" required>
                                <option value="" disabled>-- Select Curriculum -- </option>
                                <option v-for="(cd, index) in curriculumData" :value="cd.curr_id"> {{ cd.curr_code }}
                                </option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Section</label>
                            <select v-model="actSection" class="form-control form-select-sm"
                                :disabled="actType ? false : true">
                                <option value="0">All Sections</option>
                                <template v-for="(sec, index) in section">
                                    <option :value="sec.sec_id">{{ sec.sec_name }}</option>
                                </template>
                            </select>
                        </div>
                        <div class="d-flex mt-3">
                            <button :disabled="saving || !actType ? true : false" type="submit"
                                class="btn btn-sm btn-primary w-100">Save
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="p-7 h-100" v-else>
        <!-- <div class="d-flex flex-wrap flex-column">
            <p class="text-success fw-bold">Tuition Fee Management</p>
            <p class="fst-italic border p-2 rounded-3 bg-secondary-subtle small-font"><span class="fw-bold">Note:
                </span><span class="italic">This module allows you to manage fees according to your finance settings.
                However, once enrollment is actively in progress, fee editing will be disabled.
                </span></p>
        </div> -->
        <div v-if="preLoading" class="w-100 h-100 d-flex justify-content-center align-items-center">
            <Loader />
        </div>
        <div v-else class="d-flex gap-2 small-font">
            <div class="row g-2 w-100 p-2">
                <div class="col-md-5 border">
                    <div class="row g-2 w-100 p-2">
                        <div class="col-md-12">
                            <p class="mb-2 fw-bold">Additional Items</p>
                            <input :disabled="preLoading ? true : false" v-model="miscFeesMainSearch"
                                @keyup="searchItem(1)" type="text" class="form-control form-control-sm mb-2"
                                placeholder="Search Item Here..." />
                            <div class="border overflow-auto py-2 bg-secondary-subtle" style="height: 320px;">
                                <ul class="list-group">
                                    <li class="list-group-item" v-for="(misc, index) in miscFeesMainFiltered"
                                        @click="addMiscFee(misc, 1), clickSubmit('assess')">
                                        <div class="d-flex justify-content-between">
                                            <span>{{ misc.acf_desc }}</span>
                                            <span>{{ new Intl.NumberFormat('en-PH', {
                                                style: 'currency', currency: 'PHP'
                                                }).format(misc.acf_price) }}</span>
                                        </div>
                                    </li>
                                    <li class="list-group-item"
                                        v-if="!Object.keys(miscFeesMainFiltered).length && !preLoading">No Record Found
                                    </li>
                                    <li class="list-group-item"
                                        v-if="!Object.keys(miscFeesMainFiltered).length && preLoading">Loading Items...
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <!-- <div class="col-md-12" v-if="chargeType == 1">
                            <p class="mb-2 fw-bold">Subject Details</p>
                            <ul class="nav nav-tabs" id="myTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home-tab-pane" type="button" role="tab" aria-controls="home-tab-pane" aria-selected="true">Subjects From Registrar</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile-tab-pane" type="button" role="tab" aria-controls="profile-tab-pane" aria-selected="false">Subjects From Curriculum</button>
                                </li>
                            </ul>
                            <div class="tab-content" id="myTabContent">
                                <div class="tab-pane fade show active" id="home-tab-pane" role="tabpanel" aria-labelledby="home-tab" tabindex="0">
                                    <div class="col-md-12">
                                        <input :disabled="preLoading? true:false" v-model="registrarSubjectsSearch" @keyup="searchItem(3)" type="text" class="mt-3 form-control form-control-sm mb-2" placeholder="Search Subject Here..."/>
                                        <div class="col-md-12 border overflow-auto py-2 bg-secondary-subtle" style="height: 320px;">
                                            <ul class="list-group">
                                                <li class="list-group-item" v-for="(subj, index) in registrarSubjectsFiltered" @click="addMiscFee(subj, 2), clickSubmit('assess')">
                                                    <div class="d-flex justify-content-between align-items-center text-start">
                                                        <span>
                                                            <span class="fw-bold">{{ subj.subj_code }}</span><br/>
                                                            {{ subj.subj_name }}
                                                        </span>
                                                        <span>
                                                            Lec: <span class="fw-bold">{{ subj.subj_lec_units }}</span> <br/>
                                                            Lec Rate: <span class="fw-bold">{{ new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP' }).format(subj.subj_lec_units_rate) }}</span><br/>
                                                            
                                                            Lab: <span class="fw-bold">{{ subj.subj_lab_units }}</span> <br/>
                                                            Lab Rate: <span class="fw-bold">{{ new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP' }).format(subj.subj_lab_units_rate) }}</span>
                                                        </span>
                                                    </div>
                                                </li>
                                                <li class="list-group-item" v-if="!Object.keys(subjectsDataFiltered).length && !preLoading">No Record Found</li>
                                                <li class="list-group-item" v-if="!Object.keys(subjectsDataFiltered).length && preLoading">Loading Items...</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade bg-sec" id="profile-tab-pane" role="tabpanel" aria-labelledby="profile-tab" tabindex="0">
                                    <div class="col-md-12">
                                        <input :disabled="preLoading? true:false" v-model="subjectsDataSearch" @keyup="searchItem(2)" type="text" class="mt-3 form-control form-control-sm mb-2" placeholder="Search Subject Here..."/>
                                        <div class="col-md-12 border overflow-auto py-2 bg-secondary-subtle" style="height: 320px;">
                                            <ul class="list-group">
                                                <li class="list-group-item" v-for="(subj, index) in subjectsDataFiltered" @click="addMiscFee(subj, 2), clickSubmit('assess')">
                                                    <div class="d-flex justify-content-between align-items-center text-start">
                                                        <span>
                                                            <span class="fw-bold">{{ subj.subj_code }}</span><br/>
                                                            {{ subj.subj_name }}
                                                        </span>
                                                        <span>
                                                            Lec: <span class="fw-bold">{{ subj.subj_lec_units }}</span> <br/>
                                                            Lec Rate: <span class="fw-bold">{{ new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP' }).format(subj.subj_lec_units_rate) }}</span><br/>
                                                            
                                                            Lab: <span class="fw-bold">{{ subj.subj_lab_units }}</span> <br/>
                                                            Lab Rate: <span class="fw-bold">{{ new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP' }).format(subj.subj_lab_units_rate) }}</span>
                                                        </span>
                                                    </div>
                                                </li>
                                                <li class="list-group-item" v-if="!Object.keys(subjectsDataFiltered).length && !preLoading">No Record Found</li>
                                                <li class="list-group-item" v-if="!Object.keys(subjectsDataFiltered).length && preLoading">Loading Items...</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> -->
                        <!-- <div class="col-md-12">
                            <p class="mb-2 fw-bold">Subject Details</p>
                            <input :disabled="preLoading? true:false" v-model="subjectsDataSearch" @keyup="searchItem(2)" type="text" class="form-control form-control-sm mb-2" placeholder="Search Subject Here..."/>
                            <div class="col-md-12 border overflow-auto py-2 bg-secondary-subtle" style="height: 320px;">
                                <ul class="list-group">
                                    <li class="list-group-item" v-for="(subj, index) in subjectsDataFiltered" @click="addMiscFee(subj, 2), clickSubmit('assess')">
                                        <div class="d-flex justify-content-between align-items-center text-start">
                                            <span>
                                                <span class="fw-bold">{{ subj.subj_code }}</span><br/>
                                                {{ subj.subj_name }}
                                            </span>
                                            <span>
                                                Lec: <span class="fw-bold">{{ subj.subj_lec_units }}</span> <br/>
                                                Lec Rate: <span class="fw-bold">{{ new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP' }).format(subj.subj_lec_units_rate) }}</span><br/>
                                                
                                                Lab: <span class="fw-bold">{{ subj.subj_lab_units }}</span> <br/>
                                                Lab Rate: <span class="fw-bold">{{ new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP' }).format(subj.subj_lab_units_rate) }}</span>
                                            </span>
                                        </div>
                                    </li>
                                    <li class="list-group-item" v-if="!Object.keys(subjectsDataFiltered).length && !preLoading">No Record Found</li>
                                    <li class="list-group-item" v-if="!Object.keys(subjectsDataFiltered).length && preLoading">Loading Items...</li>
                                </ul>
                            </div>
                        </div> -->
                        <div class="col-md-12">
                            <p class="mb-2 fw-bold">Custom Items</p>
                            <div class="row g-1 mb-2">
                                <div class="col-md-12">
                                    <input v-model="customDescription" :disabled="preLoading ? true : false" type="text"
                                        class="form-control form-control-sm" placeholder="Description" />
                                </div>
                                <div class="col-md-6">
                                    <input v-model.number="customPrice" required step="0.01" min="0.00"
                                        :disabled="preLoading ? true : false" @input="
                                        if (customPrice <= 0) customPrice = 0;" type="number"
                                        class="form-control form-control-sm amount-text" placeholder="Price" />
                                </div>
                                <!-- <div class="col-md-2">
                                    <input :disabled="preLoading? true:false" type="text" class="form-control form-control-sm" placeholder=""/>
                                </div> -->
                                <div class="col-md-6 d-flex gap-1">
                                    <button @click="addMiscFee([], 3), clickSubmit('assess')"
                                        class="btn btn-sm btn-primary w-100">Add as Item</button>
                                    <button @click="addMiscFee([], 4), clickSubmit('assess')"
                                        class="btn btn-sm btn-info w-100">Discount =</button>
                                    <button @click="addMiscFee([], 5), clickSubmit('assess')"
                                        class="btn btn-sm btn-info w-100">Discount %</button>
                                </div>
                            </div>
                            <div class="col-md-12 border overflow-auto py-2 bg-secondary-subtle" style="height: 320px;">
                                <ul class="list-group">
                                    <li class="list-group-item" v-for="(cust, index) in customFees">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span>{{ cust.tuitemp_desc ? cust.tuitemp_desc : 'N/A' }}</span>
                                            <span>
                                                <span v-if="cust.tuitemp_custype == 3">{{ new Intl.NumberFormat('en-PH',
                                                    {
                                                    style: 'currency', currency: 'PHP' }).format(cust.tuitemp_price)
                                                    }}</span>
                                                <span v-if="cust.tuitemp_custype == 4" class="text-danger">
                                                    <span v-if="cust.tuitemp_disc_type == 0">- {{ new
                                                        Intl.NumberFormat('en-PH', {
                                                            style: 'currency', currency: 'PHP'
                                                        }).format(cust.tuitemp_price) }}</span>
                                                    <span v-else>- {{ cust.tuitemp_price.toFixed(2) }}%</span>
                                                </span>
                                            </span>

                                            <!-- <button class="btn btn-sm btn-danger" 
                                                @click="customFees.splice(index, 1), clickSubmit('assess')">
                                                <font-awesome-icon icon="fa-solid fa-trash" />
                                            </button> -->
                                        </div>
                                    </li>
                                    <li class="list-group-item" v-if="!Object.keys(customFees).length && !preLoading">No
                                        Record Found</li>
                                    <li class="list-group-item" v-if="!Object.keys(customFees).length && preLoading">
                                        Loading
                                        Items...</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-7 border d-flex flex-column justify-content-between">
                    <div class="w-100 p-4 shadow mb-2">
                        <span class="mb-2 fw-bold">Payment Breakdown</span>
                    </div>
                    <form @submit.prevent="manageDetails"
                        class="row g-1 p-2 d-flex flex-column justify-content-start h-100">
                        <button type="submit" id="submitDetails" hidden></button>
                        <div class="col-md-12 text-start mb-2">
                            <span class="fst-italic fw-bold text-success">Additional Items</span>
                            <ul class="list-group mt-2">
                                <li class="list-group-item" v-for="(mf, index) in miscFees">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="w-50 d-flex justify-content-start align-items-center">
                                            <span>{{ mf.tuitemp_desc }}</span>
                                        </div>
                                        <div class="d-flex justify-content-start align-items-center">
                                            <span>{{ new Intl.NumberFormat('en-PH', {
                                                style: 'currency', currency: 'PHP'
                                                }).format(mf.tuitemp_price) }}</span>
                                        </div>
                                        <div class="d-flex justify-content-start align-items-center">
                                            <span>Qty</span> &nbsp;
                                            <!-- <input min="1" minlength="1" type="number" required
                                                :value="mf.tuitemp_quantity"
                                                @focusout="c = $event.target.value, clickSubmit('assess')"
                                                class="form-control form-control-sm" /> -->
                                                <input
                                                    type="number"
                                                    min="1"
                                                    required
                                                    class="form-control form-control-sm"
                                                    :value="mf.tuitemp_quantity"
                                                    @focus="oldQty = mf.tuitemp_quantity"
                                                    @focusout="($event.target.value != oldQty) && (mf.tuitemp_quantity = $event.target.value, clickSubmit('assess'))"
                                                />
                                        </div>
                                        <div class="d-flex justify-content-start align-items-center">
                                            <button class="btn btn-sm btn-danger" type="button" v-if="!activeEnrollment"
                                                @click="deleteMiscData(1, mf, index)">
                                                <font-awesome-icon icon="fa-solid fa-trash" />
                                            </button>
                                        </div>
                                    </div>
                                </li>
                                <li class="list-group-item" v-if="!Object.keys(miscFees).length && !preLoading">No
                                    Record
                                    Found</li>
                                <li class="list-group-item" v-if="!Object.keys(miscFees).length && preLoading">Loading
                                    Items...</li>
                            </ul>
                        </div>
                        <!-- <div class="col-md-12 text-start mb-2" v-if="chargeType == 1">
                            <span class="fst-italic fw-bold text-primary">Subjects Prices</span>
                            <ul class="list-group mt-2">
                                <li class="list-group-item" v-for="(sj, index) in subjFees">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="w-50 d-flex justify-content-start align-items-center">
                                            <span class="w-50">{{ sj.tuitemp_desc }}</span>
                                        </div>
                                        <div class="w-25 d-flex justify-content-start align-items-center">
                                            <div class="d-flex flex-column gap-1">
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text">Lec ({{ sj.tuitemp_lec }})</span>
                                                    <input  v-model.number="sj.tuitemp_lec_price"
                                                            required
                                                            step="0.01" 
                                                            min="0.00"
                                                            @input="if (sj.tuitemp_lec_price <= 0) sj.tuitemp_lec_price = 0;"
                                                            type="number"
                                                            @focusout="clickSubmit('assess')"
                                                            class="form-control" placeholder="Price Per Unit"/>
                                                </div>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text">Lab ({{ sj.tuitemp_lab }})</span>
                                                    <input  v-model.number="sj.tuitemp_lab_price"
                                                            required
                                                            step="0.01" 
                                                            min="0.00"
                                                            @input="if (sj.tuitemp_lab_price <= 0) sj.tuitemp_lab_price = 0;"
                                                            type="number"
                                                            @focusout="clickSubmit('assess')"
                                                            :disabled="sj.tuitemp_lab==0?true:false"
                                                            class="form-control" placeholder="Price Per Unit"/>
                                                </div>
                                            </div>    
                                        </div>
                                        <div class="d-flex justify-content-center align-items-center">
                                            <div class="d-flex flex-column gap-2 text-start">
                                                <span>{{ new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP' }).format((sj.tuitemp_lec_price || 0) * sj.tuitemp_lec) }}</span>
                                                <span>{{ new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP' }).format((sj.tuitemp_lab_price || 0) * sj.tuitemp_lab) }}</span>
                                            </div>    
                                        </div>
                                        <div class="d-flex justify-content-center align-items-center">
                                           <div class="d-flex gap-1 justify-content-center align-items-center w-50">
                                                <button class="btn btn-sm btn-danger " 
                                                    @click="deleteMiscData(2, sj, index)" type="button">
                                                    <font-awesome-icon icon="fa-solid fa-trash"/>
                                                </button>
                                            </div>     
                                        </div>
                                    </div>
                                </li>
                                <li class="list-group-item" v-if="!Object.keys(subjFees).length && !preLoading">No Record Found</li>
                                <li class="list-group-item" v-if="!Object.keys(subjFees).length && preLoading">Loading Items...</li>
                            </ul>
                        </div> -->
                        <div class="col-md-12 text-start mb-2" v-if="chargeType == 1">
                            <span class="fst-italic fw-bold text-primary">Subjects Prices</span>
                            <ul class="list-group mt-2" v-for="(items, groupKey) in groupedSubjects" :key="groupKey">
                                <p class="fw-bold p-2 bg-dark rounded-3 text-white mt-2">{{ resolveGroupLabel(groupKey)
                                    }}
                                </p>
                                <li class="list-group-item" v-for="(sj, index) in items" :key="index">
                                    <div class="d-flex justify-content-between align-items-center">

                                        <div class="w-50 d-flex justify-content-start align-items-center">
                                            <span class="w-50">{{ sj.tuitemp_desc }}</span>
                                        </div>
                                        <div class="w-25 d-flex justify-content-start align-items-center">
                                            <div class="d-flex flex-column gap-1">
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text">Lec ({{ sj.tuitemp_lec }})</span>
                                                    <input
                                                        type="number"
                                                        step="0.01"
                                                        min="0"
                                                        required
                                                        class="form-control"
                                                        placeholder="Price Per Unit"
                                                        :value="sj.tuitemp_lec_price"
                                                        @focus="oldLecPrice = sj.tuitemp_lec_price"
                                                        @input="$event.target.value <= 0 && ($event.target.value = 0)"
                                                        @focusout="
                                                            $event.target.value != oldLecPrice
                                                                ? (sj.tuitemp_lec_price = $event.target.value, clickSubmit('assess'))
                                                                : null
                                                        "
                                                    />
                                                </div>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text">Lab ({{ sj.tuitemp_lab }})</span>
                                                    <input
                                                        type="number"
                                                        step="0.01"
                                                        min="0"
                                                        required
                                                        class="form-control"
                                                        placeholder="Price Per Unit"
                                                        :disabled="sj.tuitemp_lab == 0"
                                                        :value="sj.tuitemp_lab_price"
                                                        @focus="oldLabPrice = sj.tuitemp_lab_price"
                                                        @input="$event.target.value <= 0 && ($event.target.value = 0)"
                                                        @focusout="
                                                            $event.target.value != oldLabPrice
                                                                ? (sj.tuitemp_lab_price = $event.target.value, clickSubmit('assess'))
                                                                : null
                                                        "
                                                    />
                                                </div>

                                            </div>
                                        </div>
                                        <div class="w-25 d-flex justify-content-center align-items-center">
                                            <div class="d-flex flex-column gap-2 text-start">
                                                <span>{{ pesoConverter((sj.tuitemp_lec_price || 0) * sj.tuitemp_lec) }}</span>
                                                <span>{{ pesoConverter((sj.tuitemp_lab_price || 0) * sj.tuitemp_lab) }}</span>
                                            </div>
                                        </div>
                                        <!-- <div class="d-flex justify-content-center align-items-center">
                                           <div class="d-flex gap-1 justify-content-center align-items-center w-50">
                                                <button class="btn btn-sm btn-danger " 
                                                    @click="deleteMiscData(2, sj, index)" type="button">
                                                    <font-awesome-icon icon="fa-solid fa-trash"/>
                                                </button>
                                            </div>     
                                        </div> -->
                                    </div>
                                </li>
                                <li class="list-group-item" v-if="!Object.keys(subjFees).length && !preLoading">No
                                    Record
                                    Found</li>
                                <li class="list-group-item" v-if="!Object.keys(subjFees).length && preLoading">Loading
                                    Items...</li>
                            </ul>
                        </div>
                        <!-- <div class="col-md-12">
                           <div class="col-md-12">
                            <div v-for="(items, groupKey) in groupedSubjects" :key="groupKey">
                                <h5>{{ resolveGroupLabel(groupKey) }}</h5>

                                <div v-for="(item, index) in items" :key="index">
                                {{ item }}
                                </div>
                             </div>
                            </div>
                        </div> -->
                        <div class="col-md-12 text-start mb-2">
                            <span class="fst-italic fw-bold text-info">Additional Charges</span>
                            <ul class="list-group mt-2">
                                <li class="list-group-item" v-for="(cq, index) in customFees">
                                    <div class="d-flex justify-content-between align-items-center gap-1">
                                        <div class="w-50 d-flex justify-content-start align-items-center">
                                            <span>{{ cq.tuitemp_desc }}</span>
                                        </div>
                                        <div class="w-25 d-flex justify-content-start align-items-center">
                                            <span v-if="cq.tuitemp_custype == 3">{{ pesoConverter(cq.tuitemp_price) }}</span>
                                            <span v-else class="text-danger">
                                                <span v-if="cq.tuitemp_disc_type == 1">
                                                    - {{ cq.tuitemp_price }}%
                                                </span>
                                                <span v-else>
                                                    - {{ pesoConverter(cq.tuitemp_price) }} 
                                                </span>
                                            </span>
                                        </div>
                                        <div class="w-25 d-flex justify-content-start align-items-center">
                                            <span>Qty</span> &nbsp;
                                            <input
                                                type="number"
                                                min="1"
                                                required
                                                class="form-control form-control-sm"
                                                :value="cq.tuitemp_quantity"
                                                @focus="oldQty = cq.tuitemp_quantity"
                                                @focusout="($event.target.value != oldQty) && (cq.tuitemp_quantity = $event.target.value, clickSubmit('assess'))"
                                            />

                                        </div>
                                        <div>
                                            <button class="btn btn-sm btn-danger" v-if="!activeEnrollment"
                                                @click="deleteMiscData(3, cq, index)" type="button">
                                                <font-awesome-icon icon="fa-solid fa-trash" />
                                            </button>
                                        </div>
                                    </div>
                                </li>
                                <li class="list-group-item" v-if="!Object.keys(customFees).length && !preLoading">No
                                    Record
                                    Found</li>
                                <li class="list-group-item" v-if="!Object.keys(customFees).length && preLoading">Loading
                                    Items...</li>
                            </ul>
                        </div>
                    </form>
                    <div class="w-100 p-3 border rounded mb-2">

                        <!-- CHARGES -->
                        <div class="mb-3">
                            <div class="fw-bold text-uppercase small border-bottom pb-1 mb-2">
                                Charges
                            </div>

                            <div class="d-flex justify-content-between mb-1">
                                <span>Additional Items</span>
                                <span>{{ pesoConverter(miscFeesTotal) }}</span>
                            </div>

                            <div class="d-flex justify-content-between mb-1" v-if="chargeType == 1">
                                <span>Subject Units</span>
                                <span>{{ pesoConverter(subjFeesTotal) }}</span>
                            </div>

                            <div class="d-flex justify-content-between mb-1">
                                <span>Other Charges</span>
                                <span>{{ pesoConverter(customFeesTotal) }}</span>
                            </div>

                            <div class="d-flex justify-content-between fw-bold border-top pt-2 mt-2">
                                <span>Total Charges</span>
                                <span>
                                    {{ pesoConverter(customFeesTotal + miscFeesTotal + subjFeesTotal) }}
                                </span>
                            </div>
                        </div>

                        <!-- DEDUCTIONS -->
                        <div class="mb-3">
                            <div class="fw-bold text-uppercase small border-bottom pb-1 mb-2 text-danger">
                                Deductions
                            </div>

                            <div class="d-flex justify-content-between mb-1 text-danger">
                                <span>Other Deductions</span>
                                <span>- {{ pesoConverter(customDeductionsExact) }}</span>
                            </div>

                            <div class="d-flex justify-content-between mb-1 text-danger">
                                <span>
                                    Discount ({{ customDeductionsPercent }}%)
                                </span>
                                <span>
                                    - {{ pesoConverter(
                                        (customFeesTotal + miscFeesTotal + subjFeesTotal)
                                        * customDeductionsPercent / 100
                                    ) }}
                                </span>
                            </div>

                            <div class="d-flex justify-content-between fw-bold border-top pt-2 mt-2 text-danger">
                                <span>Total Deductions</span>
                                <span>
                                    - {{ pesoConverter(
                                        customDeductionsExact +
                                        ((customFeesTotal + miscFeesTotal + subjFeesTotal)
                                            * customDeductionsPercent / 100)
                                    ) }}
                                </span>
                            </div>
                        </div>

                        <!-- GRAND TOTAL -->
                        <div class="d-flex justify-content-between align-items-center border-top pt-3 mt-3">
                            <span class="fw-bold fs-5">Grand Total</span>
                            <span class="fw-bold fs-4 text-dark">
                                {{ pesoConverter(totalFees) }}
                            </span>
                        </div>

                    </div>

                    <div class="w-100 p-4 border mb-2 d-flex justify-content-end gap-2" v-if="!activeEnrollment">
                        <!-- <button type="button" class="btn btn-sm btn-warning" @click="clickSubmit('clear')">Reset Details</button> -->
                        <button type="button" class="btn btn-sm btn-success" @click="clickSubmit('save')">Save
                            Details</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>