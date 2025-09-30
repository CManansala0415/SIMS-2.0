<script setup>
import { ref, onMounted, computed } from 'vue';
import Loader from '../snippets/loaders/Loading1.vue';
import LibraryBorrowerModal from '../snippets/modal/LibraryBorrowerModal.vue';
import { getUserID } from "../../routes/user";
import { useRouter, useRoute } from 'vue-router';
import {
    getBorrowedBooks,
} from "../Fetchers.js";

const limit = ref(10)
const offset = ref(0)
const borrower = ref([])
const borrowerCount = ref(0)
const preLoading = ref(true)
const searchValue = ref('')
const userID = ref('')
const router = useRouter();
const showForm = ref(false)
const showModal = ref(false)
const booting = ref('')
const bootingCount = ref(0)
const editId = ref('')
const borrowerData = ref([])
const modeData = ref('')
const emit = defineEmits(['fetchUser', 'doneLoading'])
const accessData = ref([])
const booter = async () => {

    // getBookType().then((results)=>{
    //     books.value = results
    //     booting.value = 'Loading Genders'
    //     bootingCount.value += 1
    // })

    // getUserID().then((results) => {
    //     userID.value = results.account.data.id
    //     emit('fetchUser', results)
    //     booting.value = 'Loading Users'
    //     bootingCount.value += 1
    // })

}

onMounted(async () => {
    window.stop()
    getUserID().then((results1) => {
        userID.value = results1.account.data.id
        accessData.value = results1.access
        emit('fetchUser', results1)
        try {
            preLoading.value = true
            // await booter().then(() => {
            booting.value = 'Loading Books...'
            bootingCount.value += 1
            getBorrowedBooks(limit.value, offset.value).then((results2) => {
                borrower.value = results2.data
                borrowerCount.value = results2.count
                preLoading.value = false
                emit('doneLoading', false)
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
            }).then(() => {
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
        }).then(() => {
            router.push("/");
            window.stop()
        });
    })
})

const deleteDdc = (id) => {
    // if (confirm("Are you sure you want to delete this registry? this action cannot be reverted.") == true) {
    //     let x = {
    //         lbrc_id: id,
    //         lbrc_updatedby: userID.value,
    //         lbrc_mode: 3 // means delete
    //     }
    //     addborrower(x).then((results) => {
    //         // alert('Delete Successful')
    //         // location.reload()
    //         Swal.fire({
    //             title: "Delete Successful",
    //             text: "Changes applied, refreshing the page",
    //             icon: "success"
    //         }).then(() => {
    //             location.reload()
    //         });
    //     })
    // } else {
    //     return false;
    // }

    let x = {
        lbrc_id: id,
        lbrc_updatedby: userID.value,
        lbrc_mode: 3 // means delete
    }
    Swal.fire({
        title: "Delete Record",
        text: "Are you sure you want to delete this record",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, Im Delete it!"
    }).then(async (result) => {
        if (result.isConfirmed) {
            addborrower(x).then((results) => {
                // alert('Delete Successful')
                // location.reload()
                Swal.fire({
                    title: "Delete Successful",
                    text: "Changes applied, refreshing the page",
                    icon: "success"
                }).then(() => {
                    location.reload()
                });
            })
        }
    });
}

const paginate = (mode) => {
    switch (mode) {
        case 'prev':
            if (offset.value <= 0) {
                offset.value = 0
            } else {
                borrower.value = []
                offset.value -= 10
                borrowerCount.value = 0
                preLoading.value = true
                getBorrowedBooks(limit.value, offset.value).then((results) => {
                    borrower.value = results.data
                    borrowerCount.value = results.count
                    preLoading.value = false
                })
            }
            break;
        case 'next':

            if (offset.value >= borrowerCount.value) {
                offset.value = borrowerCount.value
            } else {
                borrower.value = []
                offset.value += 10
                borrowerCount.value = 0
                preLoading.value = true
                getBorrowedBooks(limit.value, offset.value, null).then((results) => {
                    borrower.value = results.data
                    borrowerCount.value = results.count
                    preLoading.value = false
                })
            }
            break;
        case 'search':
            searchValue.value = searchValue.value.trim()
            if (searchValue.value || searchValue.value == '') {
                borrower.value = []
                offset.value = 0
                borrowerCount.value = 0
                preLoading.value = true
                getBorrowedBooks(limit.value, offset.value, searchValue.value).then((results) => {
                    borrower.value = results.data
                    borrowerCount.value = results.count
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
                }).then(() => {
                    preLoading.value = false
                });
            }
            break;

    }
}

const search = () => {
    paginate('search')
}

const editData = (id, data, mode) => {
    editId.value = id
    borrowerData.value = data
    modeData.value = mode
    showModal.value = !showModal.value
    // showForm.value=!showForm.value
}

</script>
<template>
    <div>
        <div class="p-3 mb-4 border-bottom">
            <h5 class=" text-uppercase fw-bold">Book Borrowers</h5>
        </div>

        <div class="p-1 d-flex gap-2 justify-content-between mb-3">
            <div class="input-group w-50">
                <span class="input-group-text" id="searchaddon"><font-awesome-icon icon="fa-solid fa-search" /></span>
                <input type="text" class="form-control" placeholder="Search Here..." aria-label="search"
                    v-model="searchValue" @keyup.enter="search()" aria-describedby="searchaddon"
                    :disabled="preLoading ? true : false">
            </div>
        </div>
        <div class="table-responsive border p-3 small-font">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <!-- <th style="background-color: #237a5b;" class="text-white">ID</th> -->
                        <th style="background-color: #237a5b;" class="text-white w-25">Accession No</th>
                        <th style="background-color: #237a5b;" class="text-white w-25">Details</th>
                        <th style="background-color: #237a5b;" class="text-white w-25">Borrower</th>
                        <th style="background-color: #237a5b;" class="text-white w-25">Commands</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="!preLoading && Object.keys(borrower).length" v-for="(app, index) in borrower">
                        <!-- <td class="align-middle">
                            {{ app.lbrr_id }}
                        </td> -->
                        <td class="align-middle">
                            {{ app.lbrr_accessionid }}
                        </td>
                        <td class="align-middle text-start">
                            <p><span class="fw-bold">Title: </span> {{ app.lbrb_title ? app.lbrb_title : 'N/A' }}</p>
                            <p><span class="fw-bold">Author: </span> {{ app.lbrb_author ? app.lbrb_author : 'N/A' }}</p>
                        </td>
                        <td class="align-middle">
                            {{ app.per_firstname }} {{ app.per_middlename }} {{ app.per_lastname }} {{
                                app.per_suffixname }}
                        </td>
                        <td v-if="accessData[7].useracc_modifying == 1" class="align-middle">
                            <div class="d-flex gap-2 justify-content-center">
                                <button v-if="app.lbrr_returned == 0" data-bs-toggle="modal"
                                    data-bs-target="#returnbookmodal" @click="editData(app.lbrr_id, app, 1)"
                                    type="button" title="Edit Record" class="btn btn-secondary btn-sm">
                                    <font-awesome-icon icon="fa-solid fa-gear" /> Return Book</button>
                                <p v-else class="fw-bold text-success">Returned <span class="text-dark">({{
                                    app.lbrr_datereturned }})</span></p>
                            </div>
                        </td>
                        <td v-else class="align-middle">
                            N/A
                        </td>
                    </tr>
                    <tr v-if="!preLoading && !Object.keys(borrower).length">
                        <td class="p-3 text-center" colspan="4">
                            No Records Found
                        </td>
                    </tr>
                    <tr v-if="preLoading && !Object.keys(borrower).length">
                        <td class="p-3 text-center" colspan="4">
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
                    <button :disabled="Object.keys(borrower).length < 10 ? true : false" @click="paginate('next')"
                        class="btn btn-sm btn-secondary">Next</button>
                </div>
                <p class="">showing total of <span class="font-semibold">({{ borrowerCount }})</span> items</p>
            </div>
        </div>
    </div>

    <!-- Return Modal -->
    <div class="modal fade" id="returnbookmodal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Return Book</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        @click="showModal = false"></button>
                </div>
                <div class="modal-body">
                    <LibraryBorrowerModal v-if="showModal" :borrowerid="editId" :borrowerdata="borrowerData"
                        :modedata="modeData" :useriddata="userID" />
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <div class="form-group">
                        <small id="emailHelp" class="form-text text-muted">We'll never share your personal information
                            with anyone
                            else (Data Privacy Act of 2012)</small>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                            @click="showModal = false">Close</button>
                        <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
                    </div>
                </div>
            </div>
        </div>
    </div>

</template>