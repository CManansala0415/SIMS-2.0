<script setup>
import { ref, onMounted, computed } from 'vue';
import {
    addSubject
} from "../../Fetchers.js";

const props = defineProps({
    subjectData: {
    },
    userIdData: {
    },
    programData: {
    },
    specializationData: {
    },
    title: {
    }
})

const subject = computed(() => {
    return props.subjectData
});
const program = computed(() => {
    return props.programData
});
const specialization = computed(() => {
    return props.specializationData
});
const userID = computed(() => {
    return props.userIdData
});

const searchValue = ref('')
const searchValueModal = ref('')
const editForm = ref(false)
const saving = ref(false)
const filteredSubject = ref([])

const editData = ref({
    subj_id: '',
    subj_name: '',
    subj_code: '',
    subj_addedby: '',
    subj_lec: '',
    subj_lab: '',
    subj_hrs_week: '',
    subj_preq: '',
    subj_dtypeid: '',
    subj_specid: '',

})
const edit = (data) => {

    editForm.value = !editForm.value

    if (data) {
        editData.value.subj_id = data.subj_id
        editData.value.subj_name = data.subj_name
        editData.value.subj_code = data.subj_code
        editData.value.subj_lec = data.subj_lec
        editData.value.subj_lab = data.subj_lab
        editData.value.subj_hrs_week = data.subj_hrs_week
        editData.value.subj_preq = data.subj_preq
        editData.value.subj_dtypeid = data.subj_dtypeid
        editData.value.subj_specid = data.subj_specid
        editData.value.subj_addedby = userID.value
        searchValueModal.value = data.subj_preq
    } else {
        editData.value.subj_id = ''
        editData.value.subj_name = ''
        editData.value.subj_code = ''
        editData.value.subj_lec = ''
        editData.value.subj_lab = ''
        editData.value.subj_hrs_week = ''
        editData.value.subj_preq = ''
        editData.value.subj_dtypeid = ''
        editData.value.subj_specid = ''
        editData.value.subj_addedby = userID.value
        searchValueModal.value = ''

    }
}

const registerSubject = () => {
    saving.value = true
    addSubject(editData.value).then((results) => {
        // alert('Successfull Registered')
        // location.reload()
        Swal.fire({
            title: "Success",
            text: "Successfull registered, refreshing the page",
            icon: "success"
        }).then(()=>{
            location.reload()
        })
    })
}

const deactivate = (id) => {
    // let x = {
    //     subj_id: id,
    //     subj_updatedby: userID.value,
    //     deactivate: true
    // }
    // if (confirm("Are you sure you want to deactivate this record?") == true) {
    //     addSubject(x).then((results) => {
    //         alert('Successfull Deactivated')
    //         location.reload()
    //         saving.value = false
    //     })
    // } else {
    //     return false;
    // }

    Swal.fire({
        title: "Delete Record",
        text: "Are you sure you want to deactivate this record?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, Im Delete it!"
    }).then(async (result) => {
        if (result.isConfirmed) {
            let x = {
                subj_id: id,
                subj_updatedby: userID.value,
                deactivate: true
            }
            addSubject(x).then((results) => {
                Swal.fire({
                    title: "Delete Successful",
                    text: "Changes applied, refreshing the page",
                    icon: "success"
                }).then(()=>{
                    location.reload()
                });
            })
        }
    });
}

const search = () => {
    filteredSubject.value = subject.value.filter((e) => {
        if (
            (e.subj_code.toUpperCase().includes(searchValue.value.toUpperCase())) ||
            (e.subj_name.toUpperCase().includes(searchValue.value.toUpperCase()))
        ) {
            return e
        }
    })
}


const emit = defineEmits(['close'])
onMounted(async () => {
    filteredSubject.value = subject.value

})
</script>
<template>
    <div>
        <div class="p-3 d-flex align-content-center justify-content-center">
            <p class="text-uppercase fw-bold green-mid text-white rounded-3 p-2 small-font">Subject Settings</p>
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
                        <th style="background-color: #237a5b;" class="text-white">Name</th>
                        <th style="background-color: #237a5b;" class="text-white">Details</th>
                        <th style="background-color: #237a5b;" class="text-white w-25">Pre-requisite</th>
                        <th style="background-color: #237a5b;" class="text-white">Status</th>
                        <th style="background-color: #237a5b;" class="text-white">Commands</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(sj, index) in filteredSubject">
                        <td class="align-middle">
                            {{ sj.subj_code }}
                        </td>
                        <td class="align-middle">
                            {{ sj.subj_name }}
                        </td>
                        <td class="align-middle">
                            <div class="d-flex flex-column">
                                <div class="input-group input-group-sm mb-1">
                                    <span class=" input-group-text">Lecture Units</span>
                                    <input v-model="sj.subj_lec" type="text" class="form-control" disabled>
                                </div>
                                <div class="input-group input-group-sm mb-1">
                                    <span class=" input-group-text">Laboratory Units</span>
                                    <input v-model="sj.subj_lab" type="text" class="form-control" disabled>
                                </div>
                                <div class="input-group input-group-sm mb-3">
                                    <span class=" input-group-text">Hours /week</span>
                                    <input :value="sj.subj_hrs_week" type="text" class="form-control" disabled>
                                </div>
                            </div>
                        </td>
                        <td class="align-middle">
                            {{ sj.subj_preq_code ? sj.subj_preq_code : 'N/A' }}
                        </td>
                        <td class="align-middle">
                            {{ sj.subj_status == 1 ? 'Active' : 'Inactive' }}
                        </td>
                        <td class="align-middle">
                            <div class="d-flex gap-2 justify-content-center">
                                <button data-bs-toggle="modal" data-bs-target="#addnewmodal" @click="edit(sj)"
                                    type="button" title="Edit Record" class="btn btn-secondary btn-sm">
                                    <font-awesome-icon icon="fa-solid fa-pen" /></button>
                                <button @click="deactivate(sj.subj_id)" type="button" title="Delete Record"
                                    class="btn btn-secondary btn-sm"> <font-awesome-icon
                                        icon="fa-solid fa-trash" /></button>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="!Object.keys(filteredSubject).length">
                        <td class="p-3 text-center" colspan="8">
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
                    <form @submit.prevent="registerSubject()" class="d-flex flex-column p-2 gap-2">
                        <div class="d-flex flex-wrap flex-column">
                            <p class="text-success fw-bold">Subject Settings</p>
                            <p class=" fst-italic border p-2 rounded-3 bg-secondary-subtle small-font"><span
                                    class="fw-bold">Note:
                                </span><span class="italic">Ensure that details are
                                    correct.
                                    To commit changes click the register button and wait for saving notification.
                                </span></p>
                        </div>
                        <div class="d-flex flex-wrap form-group">
                            <label for="type">Subject Title</label>
                            <input v-model="editData.subj_name" required type="text"
                                class="form-control form-control-sm" />
                        </div>
                        <div class="d-flex flex-wrap form-group">
                            <label for="type">Subject Code</label>
                            <input v-model="editData.subj_code" required type="text"
                                class="form-control form-control-sm" />
                        </div>
                        <div class="d-flex flex-wrap form-group">
                            <label for="type">Lecture Units</label>
                            <input v-model="editData.subj_lec" required type="text"
                                class="form-control form-control-sm" />
                        </div>
                        <div class="d-flex flex-wrap form-group">
                            <label for="type">Laboratory Unit</label>
                            <input v-model="editData.subj_lab" required type="text"
                                class="form-control form-control-sm" />
                        </div>
                        <div class="d-flex flex-wrap form-group">
                            <label for="type">Hours</label>
                            <input v-model="editData.subj_hrs_week" required type="text"
                                class="form-control form-control-sm" />
                        </div>
                        <div class="d-flex flex-wrap form-group">
                            <label for="type">Program</label>
                            <select class="form-control" v-model="editData.subj_dtypeid" required
                                @change="editData.subj_specid = ''">
                                <option value="" disabled>-- Select Type --</option>
                                <option v-for="(pg, index) in program" :value="pg.dtype_id">
                                    {{ pg.dtype_desc }}
                                </option>
                            </select>
                        </div>
                        <div class="d-flex flex-wrap form-group">
                            <label for="type">Subject Type</label>
                            <select class="form-control" v-model="editData.subj_specid"
                                :disabled="editData.subj_dtypeid ? false : true" required>
                                <option value="" disabled>-- Select Type --</option>
                                <option v-for="(spc, index) in specialization" :value="spc.spec_id"
                                    :disabled="editData.subj_dtypeid == spc.spec_dtypeid ? false : true">
                                    {{ spc.spec_desc }}
                                </option>
                            </select>
                        </div>
                        <!-- <div class="d-flex flex-wrap form-group">
                            <label for="type">Pre-requisite</label>
                            <select class="form-control" v-model="editData.subj_preq" required>
                                <option value="" disabled>-- Select Type --</option>
                                <option v-for="(sj, index) in subject" :value="sj.subj_id">
                                    {{ sj.subj_name }} ({{ sj.subj_code }})
                                </option>
                            </select>
                        </div> -->
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
</template>