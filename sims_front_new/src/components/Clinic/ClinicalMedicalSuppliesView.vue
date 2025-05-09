<script setup>
import { ref, onMounted, computed } from 'vue';
import Loader from '../snippets/loaders/Loading1.vue';
// import MedicalSupplies from '../snippets/modal/MedicalSupplies.vue';
import { getUserID } from "../../routes/user.js";
import { useRouter, useRoute } from 'vue-router';
import {
    updateMedicalSupply,
    getMedicalSupplies
} from "../Fetchers.js";
import ClinicMedicalSupplies from '../snippets/modal/ClinicMedicalSupplies.vue';

const limit = ref(10)
const offset = ref(0)
const medicalSupplies = ref([])
const medicalSuppliesCount = ref(0)
const preLoading = ref(true)
const searchValue = ref('')
const userID = ref('')
const router = useRouter();
const showForm = ref(false)
const booting = ref('')
const bootingCount = ref(0)
const medicalItem = ref([])
const saving = ref(false)
const emit = defineEmits(['fetchUser'])
const accessData = ref([])
// const booter = async () => {

//     getUserID().then((results) => {
//         booting.value = 'Loading Users...'
//         bootingCount.value += 1
//         userID.value = results.account.data.id
//         emit('fetchUser', results)
//     })

// }

onMounted(async () => {
    getUserID().then(async(results1) => {
        userID.value = results1.account.data.id
        accessData.value = results1.access
        emit('fetchUser', results1)
        try {
            preLoading.value = true
            // await booter().then((results) => {
                getMedicalSupplies(limit.value, offset.value).then((results2) => {
                    medicalSupplies.value = results2.data
                    medicalSuppliesCount.value = results2.count
                    booting.value = 'Loading Medical Supplies'
                    bootingCount.value += 1
                    preLoading.value = false
                })
            // })

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

const paginate = (mode) => {
    switch (mode) {
        case 'prev':
            if (offset.value <= 0) {
                offset.value = 0
            } else {
                medicalSupplies.value = []
                offset.value -= 10
                medicalSuppliesCount.value = 0
                preLoading.value = true
                getMedicalSupplies(limit.value, offset.value).then((results) => {
                    medicalSupplies.value = results.data
                    medicalSuppliesCount.value = results.count
                    preLoading.value = false
                })
            }
            break;
        case 'next':

            if (offset.value >= medicalSuppliesCount.value) {
                offset.value = medicalSuppliesCount.value
            } else {
                medicalSupplies.value = []
                offset.value += 10
                medicalSuppliesCount.value = 0
                preLoading.value = true
                getMedicalSupplies(limit.value, offset.value, null).then((results) => {
                    medicalSupplies.value = results.data
                    medicalSuppliesCount.value = results.count
                    preLoading.value = false
                })
            }
            break;
        case 'search':
            searchValue.value = searchValue.value.trim()
            if (searchValue.value || searchValue.value == '') {
                medicalSupplies.value = []
                offset.value = 0
                medicalSuppliesCount.value = 0
                preLoading.value = true
                getMedicalSupplies(limit.value, offset.value, searchValue.value).then((results) => {
                    medicalSupplies.value = results.data
                    medicalSuppliesCount.value = results.count
                    preLoading.value = false
                }).catch((err) => {
                    // console.log(err)
                })
            } else {
                // alert('Please search a valid record')
                // preLoading.value = false
                Swal.fire({
                    title: "Search Failed",
                    text: "Please search a valid record",
                    icon: "error"
                }).then(()=>{
                    preLoading.value = false
                });
            }
            break;

    }
}

const search = () => {
    paginate('search')
}

const editData = (mode, data) => {
    if (mode == 0) {
        medicalItem.value = []
        showForm.value = !showForm.value
    } else if (mode == 1) {
        medicalItem.value = { ...data }
        showForm.value = !showForm.value
    } else if (mode == 3) {
        medicalItem.value = {
            ...data,
            replenish: true
        }
        showForm.value = !showForm.value
    } else {
        // if (confirm("Are you sure you want to delete this item?") == true) {
        //     saving.value = true
        //     updateMedicalSupply(data).then((results) => {
        //         if (results.status == 200) {
        //             // alert('Update Successful')
        //             // location.reload()
        //             Swal.fire({
        //                 title: "Update Successful",
        //                 text: "Changes applied, refreshing the page",
        //                 icon: "success"
        //             }).then(()=>{
        //                 location.reload()
        //             });
        //         } else {
        //             // alert('Update Failed')
        //             // location.reload()
        //             Swal.fire({
        //                 title: "Update Failed",
        //                 text: "Unknown error occured, try again later",
        //                 icon: "error"
        //             }).then(()=>{
        //                 location.reload()
        //             });
        //         }
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
                saving.value = true
                updateMedicalSupply(data).then((results) => {
                    if (results.status == 200) {
                        // alert('Update Successful')
                        // location.reload()
                        Swal.fire({
                            title: "Update Successful",
                            text: "Changes applied, refreshing the page",
                            icon: "success"
                        }).then(()=>{
                            location.reload()
                        });
                    } else {
                        // alert('Update Failed')
                        // location.reload()
                        Swal.fire({
                            title: "Update Failed",
                            text: "Unknown error occured, try again later",
                            icon: "error"
                        }).then(()=>{
                            location.reload()
                        });
                    }
                })
            }
        });
    }
}

</script>
<template>
    <div>
        <div class="p-3 mb-4 border-bottom">
            <h5 class=" text-uppercase fw-bold">Clinic Medical Supplies</h5>
        </div>

        <div class="p-1 d-flex gap-2 justify-content-between mb-3">
            <div class="input-group w-50">
                <span class="input-group-text" id="searchaddon"><font-awesome-icon icon="fa-solid fa-search" /></span>
                <input type="text" class="form-control" placeholder="Search Here..." aria-label="search"
                    v-if="!showForm" v-model="searchValue" @keyup.enter="search()" aria-describedby="searchaddon"
                    :disabled="preLoading ? true : false">
            </div>
            <div class="d-flex flex-wrap w-50 justify-content-end">
                <button tabindex="-1" data-bs-toggle="modal" data-bs-target="#editdatamodal" @click="editData(0)"
                    type="button" class="btn btn-sm btn-primary" :disabled="preLoading ? true : false">
                    <font-awesome-icon icon="fa-solid fa-add" /> Add New
                </button>
            </div>
        </div>
        <div class="table-responsive border p-3 small-font">
            <table class="table table-hover" style="text-transform:uppercase">
                <thead>
                    <tr>
                        <th style="background-color: #237a5b;" class="text-white">Item ID</th>
                        <th style="background-color: #237a5b;" class="text-white">Description</th>
                        <th style="background-color: #237a5b;" class="text-white">Type</th>
                        <th style="background-color: #237a5b;" class="text-white">Stocks</th>
                        <th style="background-color: #237a5b;" class="text-white">Commands</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="!preLoading && Object.keys(medicalSupplies).length"
                        v-for="(meds, index) in medicalSupplies">
                        <td class="p-3 border border-mid-gray">
                            {{ meds.clms_id }}
                        </td>
                        <td class="p-3 border border-mid-gray">
                            {{ meds.clms_desc }}
                        </td>
                        <td class="p-3 border border-mid-gray">
                            <span class="fw-bold" v-show="meds.clms_itemtype == 1">Consumables</span>
                            <span class="fw-bold" v-show="meds.clms_itemtype == 2">Medicines</span>
                            <span class="fw-bold" v-show="meds.clms_itemtype == 3">Equipments</span>
                        </td>
                        <td class="p-3 border border-mid-gray">
                            {{ meds.clms_stocks }}
                        </td>
                        <td v-if="accessData[12].useracc_modifying == 1" class="align-middle">
                            <div class="d-flex gap-2 justify-content-center">
                                <button data-bs-toggle="modal" data-bs-target="#editdatamodal"
                                    @click="editData(1, meds)" :disabled="saving ? true : false" type="button"
                                    title="Edit Record" class="btn btn-secondary btn-sm">
                                    <font-awesome-icon icon="fa-solid fa-pen" /></button>
                                <button data-bs-toggle="modal" data-bs-target="#editdatamodal"
                                    @click="editData(3, meds)" :disabled="saving ? true : false" type="button"
                                    title="Replenish Stocks" class="btn btn-secondary btn-sm">
                                    <font-awesome-icon icon="fa-solid fa-pills" /></button>
                            </div>
                        </td>
                        <td v-else class="align-middle">
                            N/A
                        </td>
                    </tr>
                    <tr v-if="!preLoading && !Object.keys(medicalSupplies).length" style="text-transform:none">
                        <td class="p-3 text-center" colspan="7">
                            No Records Found
                        </td>
                    </tr>
                    <tr v-if="preLoading && !Object.keys(medicalSupplies).length" style="text-transform:none">
                        <td class="p-3 text-center" colspan="7">
                            <div class="m-3">
                                <Loader />
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="d-flex justify-content-between align-content-center" v-if="!preLoading">
                <div class="d-flex gap-1">
                    <button :disabled="offset == 0 ? true : false" @click="paginate('prev')"
                        class="btn btn-sm btn-secondary">Prev</button>
                    <button :disabled="Object.keys(medicalSupplies).length < 10 ? true : false"
                        @click="paginate('next')" class="btn btn-sm btn-secondary">Next</button>
                </div>
                <p class="">showing total of <span class="font-semibold">({{ medicalSuppliesCount }})</span>
                    items</p>
            </div>
        </div>
    </div>

    <!-- Application Modal -->
    <div class="modal fade" id="editdatamodal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Application</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        @click="showForm = false"></button>
                </div>
                <div class="modal-body">
                    <ClinicMedicalSupplies v-if="showForm" :medicalSupplyData="medicalItem" :userData="userID" />
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <div class="form-group">
                        <small id="emailHelp" class="form-text text-muted">We'll never share your personal information
                            with anyone
                            else (Data Privacy Act of 2012)</small>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                            @click="showForm = false">Close</button>
                        <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
                    </div>
                </div>
            </div>
        </div>
    </div>

</template>