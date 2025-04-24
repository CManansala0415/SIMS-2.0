<script setup>
import { ref, onMounted, computed } from 'vue';
import {
    addCurriculum, addCurriculumTagging, getCurriculumSubject
} from "../../Fetchers.js";

const props = defineProps({
    subjectData: {
    },
    curriculumData: {
    },
    courseData: {
    },
    programData: {
    },
    quarterData: {
    },
    gradelvlData: {
    },
    userIdData: {
    },
    title: {
    }
})

const subject = computed(() => {
    return props.subjectData
});
const curriculum = computed(() => {
    return props.curriculumData
});
const program = computed(() => {
    return props.programData
});
const quarter = computed(() => {
    return props.quarterData
});
const course = computed(() => {
    return props.courseData
});
const gradelvl = computed(() => {
    return props.gradelvlData
});
const userID = computed(() => {
    return props.userIdData
});

const searchValue = ref('')
const editForm = ref(false)
const saving = ref(false)
const filteredCourseModal = ref([])
const filteredCurriculum = ref([])
const emit = defineEmits(['close'])
const tagForm = ref(false)
const addedSubject = ref([])
const addedSubjectId = ref([])
const currData = ref({})
const activeQuarter = ref('')
const activeGradelvl = ref('')
const searchValueSubject = ref('')
const filteredSubject = ref([])
const currSubject = ref([])
const savingTag = ref(false)
const savingCount = ref(0)
const editData = ref({
    curr_code: '',
    curr_progid: '',
    curr_progtype: '',
    curr_from: '',
    curr_to: '',
    curr_addedby: ''
})
const edit = (data) => {

    editForm.value = !editForm.value

    if (data) {
        editData.value.curr_id = data.curr_id
        editData.value.curr_code = data.curr_code
        editData.value.curr_progid = data.curr_progid
        editData.value.curr_progtype = data.curr_progtype
        editData.value.curr_from = data.curr_from
        editData.value.curr_to = data.curr_to
        editData.value.curr_addedby = userID.value
        filterCourse(data.curr_progtype)

    } else {
        editData.value.curr_id = ''
        editData.value.curr_code = ''
        editData.value.curr_progid = ''
        editData.value.curr_progtype = ''
        editData.value.curr_from = ''
        editData.value.curr_to = ''
        editData.value.curr_addedby = userID.value

    }
}

const registerCurriculum = () => {
    saving.value = true
    // console.log(editData.value)
    addCurriculum(editData.value).then((results) => {
        alert('Successfull Registered')
        location.reload()
    })
}

const search = () => {
    filteredCurriculum.value = curriculum.value.filter((e) => {
        if (
            (e.curr_code.toUpperCase().includes(searchValue.value.toUpperCase())) ||
            (e.curr_from == searchValue.value) ||
            (e.curr_to == searchValue.value)
        ) {
            return e
        }
    })
}

const filterCourse = () => {
    filteredCourseModal.value = course.value.filter((e) => {
        if (e.prog_progtype == editData.value.curr_progtype) {
            return e
        }
    })
}


onMounted(async () => {
    filteredCourseModal.value = course.value
    filteredCurriculum.value = curriculum.value
    filteredSubject.value = subject.value

    console.log(curriculum.value)
})

const reset = () => {
    addedSubject.value = []
    addedSubjectId.value = []
    currData.value = {}
    activeGradelvl.value = ''
    activeQuarter.value = ''
    tagForm.value = false
    loadItems.value = false

}

const addSubject = (type, data, index) => {
    if (type == 'add') {

        let exist = addedSubject.value.filter(e => {
            if (e.subj_id == data.subj_id) {
                return e
            }
        })

        Object.keys(exist).length ? alert('Already Included') : addedSubject.value.push(data)
        Object.keys(exist).length ? false : addedSubjectId.value.push(data.subj_id)

    } else {
        removeTag(data, index)
    }
}

const loadItems = ref(false)
const showItems = () => {

    addedSubject.value = [] //reset bago iload ulit
    addedSubjectId.value = [] //reset bago iload ulit

    if (!activeGradelvl.value || !activeQuarter.value) {
        alert('Please select semester and grade / year level')
    } else {
        loadItems.value = true

        let x = currSubject.value.filter((e) => {
            if (
                (e.currtag_gradelvl == activeGradelvl.value) &&
                (e.currtag_sem == activeQuarter.value)
            ) {
                return e
            }
        })

        x.forEach((e, index) => {
            addSubject('add', e, index)
        })

    }
}

const fetching = ref(false)
const setData = (data) => {
    fetching.value = true
    getCurriculumSubject(data.curr_id, 0, 0).then((results) => {
        currSubject.value = results
        currData.value = data
        tagForm.value = true
        fetching.value = false
    })
}

const searchSubject = () => {
    filteredSubject.value = subject.value.filter((e) => {
        if (
            (e.subj_code.toUpperCase().includes(searchValueSubject.value.toUpperCase())) ||
            (e.subj_name.toUpperCase().includes(searchValueSubject.value.toUpperCase()))
        ) {
            return e
        }
    })
}
const deactivate = (id, type) => {
    //means delete yung mismong whole curriculum
    let data = {}
    if (type == 'deac') {
        data = {
            currtag_id: id
        }
    } else { // means tagging ang inaalis
        data = id
    }

    if (confirm("Are you sure you want to deactivate this record?") == true) {
        removeTag(data)
    } else {
        return false;
    }


}

const removeTag = (data, index) => {
    let x = {
        currtag_id: data.currtag_id,
        currtag_updatedby: userID.value,
        deactivate: true
    }

    console.log(data)
    addCurriculumTagging(x).then((results) => {
        alert('Successfull Deactivated')
        addedSubject.value.splice(index, 1)
        addedSubjectId.value.splice(index, 1)
        // location.reload()
        saving.value = false
    })

}


const saveData = () => {
    if (!activeGradelvl.value || !activeQuarter.value || !Object.keys(addedSubject.value).length) {
        alert('Please select semester and grade / year level and add subjects in the list')
    } else {
        let x = JSON.parse(JSON.stringify(addedSubject.value))
        savingTag.value = true
        x.forEach(async (items) => {
            let data = {
                ...items,
                currtag_addedby: userID.value,
                currtag_gradelvl: activeGradelvl.value,
                currtag_sem: activeQuarter.value,
                currtag_currid: currData.value.curr_id
            }
            addCurriculumTagging(data).then((results) => {
                console.log(results)
                savingCount.value += 1
                if (savingCount.value == Object.keys(addedSubject.value).length) {
                    alert('Taggings Saved, refresh the page')
                    location.reload()
                }
            })
        })
    }
}
</script>
<template>
    <div>
        <div class="p-3 d-flex align-content-center justify-content-center">
            <p class="text-uppercase fw-bold green-mid text-white rounded-3 p-2 small-font">Curriculum Settings</p>
        </div>

        <div class="p-1 d-flex gap-2 justify-content-between mb-3">
            <div class="input-group w-50">
                <span class="input-group-text" id="searchaddon"><font-awesome-icon icon="fa-solid fa-search" /></span>
                <input type="text" class="form-control" placeholder="Search Here..." aria-label="search"
                    v-model="searchValue" @keyup="search()" aria-describedby="searchaddon">
            </div>
            <div class="d-flex flex-wrap w-50 justify-content-end">
                <button tabindex="-1" data-bs-toggle="modal" data-bs-target="#addnewmodal" @click="edit()" type="button"
                    class="btn btn-sm btn-primary">
                    <font-awesome-icon icon="fa-solid fa-add" /> Add New
                </button>
            </div>
        </div>
        <div class="table-responsive border p-3 small-font">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th style="background-color: #237a5b;" class="text-white">Code</th>
                        <th style="background-color: #237a5b;" class="text-white">Course</th>
                        <th style="background-color: #237a5b;" class="text-white">Program</th>
                        <th style="background-color: #237a5b;" class="text-white">From</th>
                        <th style="background-color: #237a5b;" class="text-white">To</th>
                        <th style="background-color: #237a5b;" class="text-white">Status</th>
                        <th style="background-color: #237a5b;" class="text-white">Commands</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(cr, index) in filteredCurriculum">
                        <td class="align-middle">
                            {{ cr.curr_code }}
                        </td>
                        <td class="align-middle">
                            <select class="form-control form-select-sm pe-none bg-transparent border-0 text-center"
                                disabled v-model="cr.curr_progid">
                                <option value="" disabled>-- Select Type --</option>
                                <option v-for="(c, index) in course" :value="c.prog_id">{{ c.prog_name }}</option>
                            </select>
                        </td>
                        <td class="align-middle">
                            <select class="form-control form-select-sm pe-none bg-transparent border-0 text-center"
                                disabled v-model="cr.curr_progtype">
                                <option value="" disabled>-- Select Type --</option>
                                <option v-for="(p, index) in program" :value="p.dtype_id">{{ p.dtype_desc }}</option>
                            </select>
                        </td>
                        <td class="align-middle">
                            {{ cr.curr_from }}
                        </td>
                        <td class="align-middle">
                            {{ cr.curr_to }}
                        </td>
                        <td class="align-middle">
                            {{ cr.curr_status == 1 ? 'Active' : 'Inactive' }}
                        </td>
                        <td class="align-middle">
                            <div class="d-flex gap-2 justify-content-center">
                                <button data-bs-toggle="modal" data-bs-target="#addnewmodal" @click="edit(cr)" type="button"
                                    title="Edit Record" class="btn btn-secondary btn-sm">
                                    <font-awesome-icon icon="fa-solid fa-pen" /></button>
                                <button @click="deactivate(cr.sec_id)" type="button" title="Delete Record"
                                    class="btn btn-secondary btn-sm"> <font-awesome-icon
                                        icon="fa-solid fa-trash" /></button>
                                <button data-bs-toggle="modal" data-bs-target="#setdatamodal" @click="setData(cr)"
                                    type="button" title="Tag Subjects" class="btn btn-secondary btn-sm">
                                    <font-awesome-icon icon="fa-solid fa-add" /></button>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="!Object.keys(filteredCurriculum).length">
                        <td class="p-3 text-center" colspan="7">
                            No Records Found
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add New Modal -->
    <div class="modal fade" id="addnewmodal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Settings</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        @click="editForm = false"></button>
                </div>
                <div class="modal-body">
                    <form @submit.prevent="registerCurriculum()" class="d-flex flex-column p-2 gap-2">
                        <div class="d-flex flex-wrap flex-column">
                            <p class="text-success fw-bold">Curriculum Settings</p>
                            <p class=" fst-italic border p-2 rounded-3 bg-secondary-subtle small-font"><span
                                    class="fw-bold">Note:
                                </span><span class="italic">Ensure that the details of the following applicant are
                                    correct.
                                    To enroll this applicant, select the appropiate academic status and refresh the
                                    page.
                                </span></p>
                        </div>
                        <div class="d-flex flex-wrap form-group">
                            <label for="type">Curriculum Title</label>
                            <input v-model="editData.curr_code" required type="text"
                                class="form-control form-control-sm" />
                        </div>
                        <div class="d-flex flex-wrap form-group">
                            <label for="type">Program</label>
                            <select class="form-control" v-model="editData.curr_progtype"
                                @change="filterCourse(editData.curr_progtype)" required>
                                <option value="" disabled>-- Select Type --</option>
                                <option v-for="(p, index) in program" :value="p.dtype_id">{{ p.dtype_desc }}</option>
                            </select>
                        </div>
                        <div class="d-flex flex-wrap form-group">
                            <label for="type">Course</label>
                            <select class="form-control" v-model="editData.curr_progid"
                                :disabled="editData.curr_progtype ? false : true" required>
                                <option value="" disabled>-- Select Type --</option>
                                <option v-for="(cr, index) in filteredCourseModal" :value="cr.prog_id">{{ cr.prog_name
                                    }}</option>
                            </select>
                        </div>
                        <div class="d-flex flex-wrap form-group">
                            <label for="type">Year From</label>
                            <input v-model="editData.curr_from" required type="number" minlength="4" maxlength="4"
                                class="form-control form-control-sm" />
                        </div>
                        <div class="d-flex flex-wrap form-group">
                            <label for="type">Year To</label>
                            <input v-model="editData.curr_to" required type="number" minlength="4" maxlength="4"
                                class="form-control form-control-sm" />
                        </div>
                        <div class="d-flex flex-column mt-3">
                            <button :disabled="saving ? true : false" type="submit"
                                class="btn btn-sm btn-primary">Register</button>
                        </div>
                    </form>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <div class="form-group">
                        <small id="emailHelp" class="form-text text-muted">We'll never share your personal information
                            with anyone
                            else (Data Privacy Act of 2012)</small>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                            @click="editForm = false">Close</button>
                        <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tag Subject Modal -->
    <div class="modal fade" id="setdatamodal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Settings</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        @click="tagForm = false, reset()"></button>
                </div>
                <div class="modal-body">
                    <form @submit.prevent="registerCurriculum()" class="d-flex flex-column p-2 gap-1">
                        <div class="d-flex flex-wrap flex-column">
                            <p class="text-success fw-bold">Tagging Settings</p>
                            <p class=" fst-italic border p-2 rounded-3 bg-secondary-subtle small-font"><span
                                    class="fw-bold">Note:
                                </span><span class="italic">Ensure that the tagged subjects are
                                    correct.
                                    To commit changes click the register button and wait for saving notification.
                                </span></p>
                        </div>
                        <div class="container mb-3 shadow-sm">
                            <div class="row">
                                
                                <div class="col">
                                    <label for="type">Semester</label>
                                    <select class="form-control form-select-sm" v-model="activeQuarter">
                                        <option value="" disabled>-- Select Quarter --</option>
                                        <option v-for="(qr, index) in quarter" :value="qr.quar_id">
                                            {{ qr.quar_desc }}
                                        </option>
                                    </select>
                                </div>
                                <div class="col">
                                    <label for="type">Grade Level</label>
                                    <select class="form-control form-select-sm" v-model="activeGradelvl">
                                        <option value="" disabled>-- Select Grade Level --</option>
                                        <option v-for="(gr, index) in gradelvl" :value="gr.grad_id"
                                            :disabled="gr.grad_dtypeid == currData.curr_progtype ? false : true">
                                            {{ gr.grad_name }}
                                        </option>
                                    </select>
                                </div>
                                <div class="col align-content-end">
                                    <button @click="showItems()" :disabled="savingTag ? true : false" type="button"
                                        class="btn btn-sm btn-primary w-100">
                                        Load Data</button>
                                </div>
                            </div>
                        </div>

                        <div v-if="loadItems" class="container ">
                            <div class="row">
                                <div class="col">
                                    <div class="d-flex flex-column gap-2 ">
                                        <div class="p-3 card">
                                            <div class="border p-2 form-group">
                                                <label for="type">Search Subject</label>
                                                <input v-model="searchValueSubject" @keyup="searchSubject" type="text"
                                                    :disabled="!loadItems ? true : false" class="form-control form-control-sm" />
                                            </div>
                                            <div class="table-responsive border p-2 small-font" style="height: 227px;">
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
                                                        <tr v-else v-for="(sj, index) in filteredSubject"
                                                            @click="addSubject('add', sj, index)"
                                                            :class="savingTag ? 'pe-none' : 'pe-auto'">
                                                            <td
                                                                :class="addedSubjectId.includes(sj.subj_id) ? 'align-middle text-start bg-secondary text-white' : 'align-middle text-start bg-white text-black'">
                                                                <p class="fw-bold ">{{ sj.subj_code }}</p>
                                                                <p class=" fst-italic">{{ sj.subj_name }}</p>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="d-flex flex-column gap-2 ">
                                        <div class="p-3 card">
                                            <div class="table-responsive border p-2 small-font" style="height: 300px;">
                                                <table class="table table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th>Select Subjects</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr v-if="!Object.keys(addedSubject).length">
                                                            <td class="align-middle text-start">
                                                                No Data Found
                                                            </td>
                                                        </tr>
                                                        <tr v-else v-for="(asj, index) in addedSubject"
                                                            :class="savingTag ? 'pe-none' : 'pe-auto'">
                                                            <td class="align-middle text-start">
                                                                <div class="mt-2 w-full">
                                                                    <div class="d-flex flex-column">
                                                                        <div
                                                                            class="border-bottom d-flex flex-column text-start mb-3">
                                                                            <span class="mt-3 fw-bold">{{ asj.subj_code
                                                                                }}</span>
                                                                            <span class="mb-3 fst-italic">{{ asj.subj_name
                                                                                }}</span>
                                                                        </div>
                                                                        <button :disabled="savingTag ? true : false"
                                                                            @click="addSubject('remove',asj,index)" 
                                                                            type="button"
                                                                            class="btn btn-sm btn-danger">&times;
                                                                            Remove</button>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 mt-4 d-flex justify-content-end">
                                <button @click="saveData()" :disabled="savingTag || !loadItems? true:false" type="button" class="btn btn-sm btn-success">
                                    {{ savingTag? 'Saving Items Please Wait':'Register Tags' }}</button>
                            </div>
                        </div>

                    </form>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <div class="form-group">
                        <small id="emailHelp" class="form-text text-muted">We'll never share your personal information
                            with anyone
                            else (Data Privacy Act of 2012)</small>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                            @click="tagForm = false, reset()">Close</button>
                        <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
