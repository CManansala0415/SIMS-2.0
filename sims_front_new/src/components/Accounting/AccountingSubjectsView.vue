<script setup>
import { ref, onMounted, computed } from 'vue';
import {
    addSubject,
    getSubject,
    getProgram,
    getSpecialization,
} from "../Fetchers.js";
import { getUserID } from "../../routes/user";
import { useRouter, useRoute } from 'vue-router';
import NeuLoader1 from '../snippets/loaders/NeuLoader1.vue';
import NeuLoader4 from '../snippets/loaders/NeuLoader4.vue';
import { pesoConverter } from '../Generators.js';
const router = useRouter();
const subject = ref([])
const program = ref([])
const specialization = ref([])
const userID = ref('')

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
    subj_lec_units: '',
    subj_lab_units: '',
    subj_lec_hrs: '',
    subj_lab_hrs: '',
    subj_hrs_week: '',
    subj_preq: '',
    subj_dtypeid: '',
    subj_specid: '',
    subj_schedpass: '',
    subj_lec_units_rate:0,
    subj_lab_units_rate:0,
    mode:1

})
const edit = (data) => {

    editForm.value = !editForm.value

    if (data) {
        editData.value.subj_id = data.subj_id
        editData.value.subj_name = data.subj_name
        editData.value.subj_code = data.subj_code
        editData.value.subj_lec_units = data.subj_lec_units
        editData.value.subj_lab_units = data.subj_lab_units
        editData.value.subj_lec_hrs = data.subj_lec_hrs
        editData.value.subj_lab_hrs = data.subj_lab_hrs
        editData.value.subj_hrs_week = data.subj_hrs_week
        editData.value.subj_preq = data.subj_preq
        editData.value.subj_dtypeid = data.subj_dtypeid
        editData.value.subj_specid = data.subj_specid
        editData.value.subj_schedpass = data.subj_schedpass
        editData.value.subj_lec_units_rate = data.subj_lec_units_rate
        editData.value.subj_lab_units_rate = data.subj_lab_units_rate
        editData.value.subj_updatedby = userID.value
        searchValueModal.value = data.subj_preq

    } else {
        editData.value.subj_id = ''
        editData.value.subj_name = ''
        editData.value.subj_code = ''
        editData.value.subj_lec_units = ''
        editData.value.subj_lab_units = ''
        editData.value.subj_lec_hrs = ''
        editData.value.subj_lab_hrs = ''
        editData.value.subj_hrs_week = ''
        editData.value.subj_preq = ''
        editData.value.subj_dtypeid = ''
        editData.value.subj_specid = ''
        editData.value.subj_schedpass = ''
        editData.value.subj_addedby = userID.value
        searchValueModal.value = ''

    }
}

const registerSubject = () => {
    saving.value = true

    Swal.fire({
        title: "Saving Updates",
        text: "Please wait while we check all subject details.",
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    addSubject(editData.value).then((results) => {
        // alert('Successfull Registered')
        // location.reload()
        Swal.close()
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

const booter = async () => {
    await getSpecialization().then((results) => {
        specialization.value = results
    })
    await getProgram().then((results) => {
        program.value = results
    })
    await getSubject().then((results) => {
        subject.value = results
    })
}

const preLoading = ref(true)
const emit = defineEmits(['fetchUser', 'doneLoading'])
const accessData = ref([])
onMounted(async () => {
    
    getUserID().then(async(results1) => {
        userID.value = results1.account.data.id
        emit('fetchUser', results1)
        accessData.value = results1.access

        try {
            await booter().then((results) => {
                filteredSubject.value = subject.value
                preLoading.value = false
                emit('doneLoading', false)
            })

        } catch (err) {
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

const labRate = ref(0)
const lecRate = ref(0)
</script>
<template>
    <div>
        <div class="p-3 mb-4 border-bottom">
            <h5 class=" text-uppercase fw-bold">Subject Rates</h5>
        </div>
        <div v-if="preLoading">
            <NeuLoader1/>
        </div>

        <div v-else>
            <div class="p-3 d-flex gap-2 justify-content-between mb-3">
                <div class="input-group w-50">
                    <!-- <span class="input-group-text" id="searchaddon"><font-awesome-icon icon="fa-solid fa-search" /></span> -->
                    <input type="text" class="neu-input" placeholder="Search Here..." aria-label="search"
                        v-model="searchValue" @keyup="search()" aria-describedby="searchaddon">
                </div>
                <!-- <div class="d-flex flex-wrap w-50 justify-content-end">
                    <button tabindex="-1" data-bs-toggle="modal" data-bs-target="#addnewmodal" @click="edit()" type="button"
                        class="btn btn-sm btn-primary">
                        <font-awesome-icon icon="fa-solid fa-add" /> Add New
                    </button>
                </div> -->
            </div>
            <div class="table-responsive border p-3 small-font">
                <table class="neu-table mb-3">
                    <thead>
                        <tr>
                            <th style="color:#555555">Code</th>
                            <th style="color:#555555">Name</th>
                            <th style="color:#555555">Details</th>
                            <th style="color:#555555">Default Rate</th>
                            <th style="color:#555555">Status</th>
                            <th style="color:#555555" class="text-center">Commands</th>
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
                                    <div class="d-flex justify-content-between gap-1 mb-1">
                                        <span class="">Lecture Units ({{ sj.subj_lec_units }})</span>
                                    </div>
                                    <div class="d-flex gap-1 mb-1">
                                        <span class="">Laboratory Units ({{ sj.subj_lab_units }})</span>
                                    </div>
                                    <div class="d-flex gap-1 mb-3">
                                        <span class="">Hours /week ({{ sj.subj_hrs_week }})</span>
                                    </div>
                                </div>
                            </td>
                            <td class="align-middle">
                                <!-- {{ sj.subj_preq_code ? sj.subj_preq_code : 'N/A' }} -->
                                <!-- {{ pesoConverter(sj.subj_lec_rate + sj.subj_lab_rate) }} -->
                                {{ pesoConverter((sj.subj_lab_rate || 0) * (sj.subj_lab_units) + (sj.subj_lec_rate || 0) * (sj.subj_lec_units || 0)) }}
                            </td>
                            <td class="align-middle">
                                {{ sj.subj_status == 1 ? 'Active' : 'Inactive' }}
                            </td>
                            <td class="align-middle">
                                <div class="d-flex gap-2 justify-content-center" v-if="accessData[15].useracc_modifying == 1">
                                    <button data-bs-toggle="modal" data-bs-target="#addnewmodal" @click="edit(sj)"
                                        type="button" title="Edit Record" class="neu-btn-sm neu-white">
                                        <font-awesome-icon icon="fa-solid fa-pen" /></button>
                                </div>
                                <span v-else>N/A</span>
                            </td>
                        </tr>
                        <tr v-if="!Object.keys(filteredSubject).length && !preLoading">
                            <td class="p-3 text-center" colspan="8">
                                <NeuLoader4/>
                                <p class="fw-bold m-0">Nothing here yet!</p>
                                <p>The hamster took a break 💤 — try adding something new.</p>
                            </td>
                        </tr>
                        <tr v-if="!Object.keys(filteredSubject).length && preLoading">
                            <td class="p-3 text-center" colspan="8">
                                <Loader />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add New Modal -->
    <div class="modal fade" id="addnewmodal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Settings</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        @click="editForm = false"></button>
                </div>
                <div class="modal-body neu-bg">
                    <form @submit.prevent="registerSubject()" class="d-flex flex-column p-2 gap-2">
                        <div class="d-flex flex-wrap flex-column">
                            <p class="text-success fw-bold">Rate Setup</p>
                            <p class=" fst-italic border p-2 rounded-3 bg-secondary-subtle small-font"><span
                                    class="fw-bold">Note:
                                </span><span class="italic">Ensure that details are
                                    correct before tagging the rates to ensure that the prices will appear correctly.
                                </span></p>
                        </div>
                        <div class="p-3 neu-card">
                            <table class="neu-table-flat small-font">
                                <thead>
                                    <tr>
                                        <th colspan="3" class="text-start fw-normal">
                                            <span>{{ editData.subj_name }}</span> (<span class="fw-bold">{{ editData.subj_code }}</span>)
                                        </th>
                                    </tr>
                                    <tr>
                                        <th class="w-25 text-center">Type</th>
                                        <th class="w-10 text-center">Units</th>
                                        <th class="w-10 text-center">Hours</th>
                                        <th class="w-25 text-center">Rate</th>
                                        <th class="w-25 text-center">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="align-middle text-center"><span class="fw-bold">Lecture</span></td>
                                        <td class="align-middle text-center">{{ editData.subj_lec_units }}</td>
                                        <td class="align-middle text-center">{{ editData.subj_lec_hrs }}</td>
                                        <td class="align-middle text-center">
                                            <input  v-model.number="editData.subj_lec_units_rate"
                                                    required
                                                    step="0.01" 
                                                    min="0.00"
                                                    @input="if (editData.subj_lec_units_rate <= 0) editData.subj_lec_units_rate = 0;"
                                                    type="number"
                                                    class="neu-input" placeholder="Price Per Unit"/>
                                        </td>
                                        <td class="align-middle text-center">
                                            <div class="d-flex flex-column gap-2">
                                                <span>{{ pesoConverter((editData.subj_lec_units_rate || 0) * (editData.subj_lec_units)) }}</span>
                                            </div>    
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="align-middle text-center"><span class="fw-bold">Laboratory</span></td>
                                        <td class="align-middle text-center">{{ editData.subj_lab_units }}</td>
                                        <td class="align-middle text-center">{{ editData.subj_lab_hrs }}</td>
                                        <td class="align-middle text-center">
                                            <input  v-model.number="editData.subj_lab_units_rate"
                                                    required
                                                    step="0.01" 
                                                    min="0.00"
                                                    @input="if (editData.subj_lab_units_rate <= 0) editData.subj_lab_units_rate = 0;"
                                                    type="number"
                                                    :disabled="editData.subj_lab_units==0?true:false"
                                                    class="neu-input" placeholder="Price Per Unit"/>
                                        </td>
                                        <td class="align-middle text-center">
                                            <div class="d-flex flex-column gap-2">
                                                <span>{{ pesoConverter((editData.subj_lab_units_rate || 0) * (editData.subj_lab_units)) }}</span>
                                            </div>    
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="align-middle text-end fw-bold" colspan="4">Total</td>
                                        <td class="align-middle text-center">
                                            <div class="d-flex flex-column gap-2 fw-bold">
                                                <span>{{ pesoConverter((editData.subj_lab_units_rate || 0) * (editData.subj_lab_units) + (editData.subj_lec_units_rate || 0) * (editData.subj_lec_units || 0)) }}</span>
                                            </div>    
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex flex-column mt-3">
                            <button :disabled="saving ? true : false" type="submit"
                                class="neu-btn neu-green p-2"><font-awesome-icon icon="fa-solid fa-floppy-disk"/> Assign Rate</button>
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