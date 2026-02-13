<script setup>
import { ref, onMounted, computed } from 'vue';
import NeuLoader1 from '../loaders/NeuLoader1.vue';
import UserAccountAccess from '../modal/UserAccountAccess.vue';
import NeuLoader4 from '../loaders/NeuLoader4.vue'

import {
    getCommandUsers,
    updateCommandUsers,
    addCommandUsers,
    saveCommandAccess,
    getCommandAccess

} from "../../Fetchers.js";

const props = defineProps({
    userIdData: {
    },
    title: {
    }
})

const userID = computed(() => {
    return props.userIdData
});

const emit = defineEmits(['close'])

const accountId = ref('')
const saving = ref(false)
const preLoading = ref(false)
const addNew = ref(false)
const searchValue = ref('')
const limit = ref(10)
const offset = ref(0)
const usersCount = ref(0)
const users = ref([])
const name = ref('')
const email = ref('')
const password = ref('')
const confirmpassword = ref('')
const id = ref('')
const loadingAccess = ref(false)


onMounted(async () => {
    try {
        preLoading.value = true
        getCommandUsers().then((results) => {
            users.value = results.data
            usersCount.value = results.count
            preLoading.value = false
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
        });
    }
})

const banAccount = (id) => {
    let x = {
        id: id,
        updated_by: userID.value,
        mode: 2
    }
    Swal.fire({
        title: "Saving Updates",
        text: "Please wait while we check all necessary details.",
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    updateCommandUsers(x).then((results) => {
        if (results.status != 200) {
            // alert('Update Failed')
            // location.reload()
            Swal.fire({
                title: "Update Failed",
                text: "Unknown error occured, try again later",
                icon: "error"
            }).then(()=>{
                Swal.close()
                location.reload()
            });
        } else {
            // alert('Update Successful')
            // location.reload()
            Swal.fire({
                title: "Update Successful",
                text: "Changes applied, refreshing the page",
                icon: "success"
            }).then(()=>{
                Swal.close()
                location.reload()
            });
        }
    })
}
const editData = (data) => {
    if (data) {
        addNew.value = false
        id.value = data.id
        name.value = data.name
        email.value = data.email
    } else {
        addNew.value = true
        id.value = ''
        name.value = ''
        email.value = ''
        password.value = ''
        confirmpassword.value = ''
    }
}

const handleAccount = async () => {
    saving.value = true
    if (addNew.value) {
        let x = {
            name: name.value,
            email: email.value,
            password: password.value,
            password_confirmation: password.value
        }
        Swal.fire({
            title: "Saving Updates",
            text: "Please wait while we check all necessary details.",
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        addCommandUsers(x).then((results) => {
            if (results.status != 200) {
                // alert('Update Failed')
                // location.reload()
                Swal.fire({
                    title: "Update Failed",
                    text: "Unknown error occured, try again later",
                    icon: "error"
                }).then(()=>{
                    Swal.close()
                    location.reload()
                });
            } else {
                // alert('Update Successful')
                // location.reload()
                Swal.fire({
                    title: "Update Successful",
                    text: "Changes applied, refreshing the page",
                    icon: "success"
                }).then(()=>{
                    Swal.close()
                    location.reload()
                });
            }
        })
    } else {
        let x = {
            id: id.value,
            name: name.value,
            email: email.value,
            mode: 1,
            updated_by: userID.value
        }
        Swal.fire({
            title: "Saving Updates",
            text: "Please wait while we check all necessary details.",
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        updateCommandUsers(x).then((results) => {
            if (results.status != 200) {
                // alert('Update Failed')
                // location.reload()
                Swal.fire({
                    title: "Update Failed",
                    text: "Unknown error occured, try again later",
                    icon: "error"
                }).then(()=>{
                    Swal.close()
                    location.reload()
                });
            } else {
                // alert('Update Successful')
                // location.reload()
                Swal.fire({
                    title: "Update Successful",
                    text: "Changes applied, refreshing the page",
                    icon: "success"
                }).then(()=>{
                    Swal.close()
                    location.reload()
                });
            }
        })
    }
}

const accessKey = ref(false)
const accessModuleData = ref([])

const accessData = async (data) => {
    accessKey.value = !accessKey.value
    accessModuleData.value = data
}
 

</script>
<template>
    <div class="small-font">
        <div class="p-3">
            <p class="text-uppercase fw-bold">User Account Settings</p>
        </div>
        <div v-if="preLoading">
            <NeuLoader1/>
        </div>
        <div v-else>
            <div class="p-3 d-flex gap-1 justify-content-between">
                <div class="input-group w-50">
                    <!-- <span class="input-group-text" id="searchaddon"><font-awesome-icon icon="fa-solid fa-search" /></span> -->
                    <input type="text" class="neu-input" placeholder="Search Here..." aria-label="search"
                        v-model="searchValue" @keyup.enter="search()" aria-describedby="searchaddon"
                        :disabled="preLoading ? true : false">
                </div>
                <div class="d-flex w-25 justify-content-end gap-2">
                    <button tabindex="-1" data-bs-toggle="modal" data-bs-target="#editdatamodal" @click="editData()"
                        type="button" class="neu-btn neu-green" :disabled="preLoading ? true : false">
                        <font-awesome-icon icon="fa-solid fa-add" /> Add New
                    </button>
                    <button class="neu-btn neu-blue" :disabled="preLoading ? true : false"
                        @click="$emit('close')"><font-awesome-icon icon="fa-solid fa-rotate-left" size="sm" /> Back
                    </button>
                </div>
            </div>
            <div class="table-responsive border p-3 small-font">
                <table class="neu-table mb-3">
                    <thead>
                        <tr>
                            <th style="color:#555555">#</th>
                            <th style="color:#555555">Name</th>
                            <th style="color:#555555">Email</th>
                            <th style="color:#555555">Status</th>
                            <th style="color:#555555" class="text-center">Commands</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="!preLoading && Object.keys(users).length" v-for="(app, index) in users">
                            <td class="align-middle">
                                {{ app.id }}
                            </td>
                            <td class="align-middle">
                                {{ app.name }}
                            </td>
                            <td class="align-middle">
                                {{ app.email }}
                            </td>
                            <td class="align-middle">
                                {{ app.status == 1 ? 'Active' : 'Inactive' }}
                            </td>
                            <td class="align-middle">
                                <div class="d-flex gap-2 justify-content-center">
                                    <button data-bs-toggle="modal" data-bs-target="#editdatamodal" @click="editData(app)"
                                        type="button" title="Edit Record" class="neu-btn-sm neu-white">
                                        <font-awesome-icon icon="fa-solid fa-pen" /></button>
                                    <button data-bs-toggle="modal" data-bs-target="#accessdatamodal"
                                        @click="accessData(app)" type="button" title="Edit Record"
                                        class="neu-btn-sm neu-white">
                                        <font-awesome-icon icon="fa-solid fa-key" /></button>
                                    <button @click="banAccount(app.id)" type="button" title="Delete Record"
                                        class="neu-btn-sm neu-white"> <font-awesome-icon
                                            icon="fa-solid fa-trash" /></button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!preLoading && !Object.keys(users).length">
                            <td class="p-3 text-center" colspan="5">
                                <NeuLoader4/>
                                <p class="fw-bold m-0">Nothing here yet!</p>
                                <p>The hamster took a break 💤 — try adding something new.</p>
                            </td>
                        </tr>
                        <!-- <tr v-if="preLoading && !Object.keys(users).length">
                            <td class="p-3 text-center" colspan="5">
                                <div class="m-3">
                                    <NeuLoader1 />
                                </div>
                            </td>
                        </tr> -->
                    </tbody>
                </table>
                <div class="d-flex justify-content-between align-content-center" v-if="!preLoading">
                    <div class="d-flex gap-1">
                        <button :disabled="offset == 0 ? true : false" @click="paginate('prev')"
                            class="neu-btn neu-light-gray">Prev</button>
                        <button :disabled="Object.keys(users).length < 10 ? true : false" @click="paginate('next')"
                            class="neu-btn neu-dark-gray">Next</button>
                    </div>
                    <p class="">showing total of <span class="font-semibold">({{ usersCount }})</span> items</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editdatamodal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel">
        <div class="modal-dialog modal-md modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Application</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body neu-bg small-font">
                    <form @submit.prevent="handleAccount" class="d-flex flex-column align-items-start neu-card p-3">
                        <div class="mb-3 d-flex flex-column align-items-start w-100">
                            <label for="username" class="form-label">Name</label>
                            <input type="text" class="neu-input" id="name" aria-describedby="name" v-model="name"
                                required>
                        </div>
                        <div class="mb-3 d-flex flex-column align-items-start w-100">
                            <label for="username" class="form-label">Email</label>
                            <input type="email" class="neu-input" id="email" aria-describedby="email" v-model="email"
                                required>
                        </div>
                        <div v-if="addNew" class="w-100">
                            <div class="mb-3 d-flex flex-column align-items-start w-100">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="neu-input" id="password" aria-describedby="password"
                                    v-model="password" required>
                            </div>
                            <div class="mb-3 d-flex flex-column align-items-start w-100">
                                <label for="confirm" class="form-label">Confirm Password</label>
                                <input type="password" class="neu-input" id="confirm" aria-describedby="confirm"
                                    v-model="confirmpassword" required>
                            </div>
                        </div>
                        <div class="w-100" v-if="addNew">
                            <div class="alert alert-danger" role="alert"
                                v-if="password != confirmpassword || !password || !confirmpassword">
                                Password does not match
                            </div>
                            <div class="alert alert-success" role="alert" v-else>
                                Password Matched
                            </div>
                        </div>
                        <button type="submit" class="neu-btn neu-green p-2"
                            :disabled="(saving || (password != confirmpassword || !password || !confirmpassword)) && addNew ? true : false">
                            <font-awesome-icon icon="fa-solid fa-gear"/> Register
                        </button>
                    </form>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <div class="form-group">
                        <small id="emailHelp" class="form-text text-muted">We'll never share your personal information
                            with anyone
                            else (Data Privacy Act of 2012)</small>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- access Modal -->
    <div class="modal fade" id="accessdatamodal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Key Access</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" @click="accessKey = false"></button>
                </div>
                <div class="modal-body neu-bg">
                    <UserAccountAccess v-if="accessKey"  :accountDetail="accessModuleData" :userIdData="userID"/>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <div class="form-group">
                        <small id="emailHelp" class="form-text text-muted">Make sure that the level of access are
                            correct to avoid irreversible
                            actions done by unauthorized staff.
                        </small>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" @click="accessKey = false">Close</button>
                        <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
                    </div>
                </div>
            </div>
        </div>
    </div>

</template>