<script setup>
import { ref, onMounted, computed } from 'vue';
import NeuLoader1 from '../snippets/loaders/NeuLoader1.vue';
import NeuLoader4 from '../snippets/loaders/NeuLoader4.vue';
import LibraryBook from '../snippets/modal/LibraryBookModal.vue';
import LibraryBorrow from '../snippets/modal/LibraryBorrowModal.vue';
import { getUserID } from "../../routes/user";
import { useRouter, useRoute } from 'vue-router';
import {
    addBookInformation,
    getBooksAccession,
    getBorrowedBooks
} from "../Fetchers.js";

const limit = ref(10)
const offset = ref(0)
const books = ref([])
const booksCount = ref(0)
const preLoading = ref(true)
const searchValue = ref('')
const userID = ref('')
const router = useRouter();
const showForm = ref(false)
const booting = ref('')
const bootingCount = ref(0)
const editId = ref('')
const bookData = ref([])
const borrowedBooks = ref([])
const modeData = ref('')
const showModal = ref(false)
const showBorrow = ref(false)
const emit = defineEmits(['fetchUser', 'doneLoading'])
const accessData = ref([])
const booter = async () => {

    getBorrowedBooks(0, 0, 200).then((results) => {
        borrowedBooks.value = results.data
        booting.value = 'Loading Borrowed Books'
        bootingCount.value += 1
    })

    // getUserID().then((results) => {
    //     userID.value = results.account.data.id
    //     emit('fetchUser', results)
    //     booting.value = 'Loading User'
    //     bootingCount.value += 1
    // })

}

onMounted(async () => {

    getUserID().then(async(results1) => {
        userID.value = results1.account.data.id
        accessData.value = results1.access
        emit('fetchUser', results1)
        try {
            preLoading.value = true
            await booter().then((results) => {
                booting.value = 'Loading Books...'
                bootingCount.value += 1
                getBooksAccession(limit.value, offset.value).then((results2) => {
                    books.value = results2.data
                    booksCount.value = results2.count
                    checkAvailability()
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

const deleteBook = (id) => {
    // if (confirm("Are you sure you want to delete this registry? this action cannot be reverted.") == true) {
    //     let x = {
    //         lbrb_id: id,
    //         lbrb_updatedby: userID.value,
    //         lbrb_mode: 3 // means delete
    //     }
    //     addBookInformation(x).then((results) => {
    //         alert('Delete Successful')
    //         location.reload()
    //     })
    // } else {
    //     return false;
    // }

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
            let x = {
                lbrb_id: id,
                lbrb_updatedby: userID.value,
                lbrb_mode: 3 // means delete
            }
            addBookInformation(x).then((results) => {
                // alert('Delete Successful')
                // location.reload()
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

const paginate = (mode) => {
    switch (mode) {
        case 'prev':
            if (offset.value <= 0) {
                offset.value = 0
            } else {
                books.value = []
                offset.value -= 10
                booksCount.value = 0
                preLoading.value = true
                getBooksAccession(limit.value, offset.value).then((results) => {
                    books.value = results.data
                    booksCount.value = results.count
                    preLoading.value = false
                })
            }
            break;
        case 'next':

            if (offset.value >= booksCount.value) {
                offset.value = booksCount.value
            } else {
                books.value = []
                offset.value += 10
                booksCount.value = 0
                preLoading.value = true
                getBooksAccession(limit.value, offset.value, null).then((results) => {
                    books.value = results.data
                    booksCount.value = results.count
                    preLoading.value = false
                })
            }
            break;
        case 'search':
            searchValue.value = searchValue.value.trim()
            if (searchValue.value || searchValue.value == '') {
                books.value = []
                offset.value = 0
                booksCount.value = 0
                preLoading.value = true
                getBooksAccession(limit.value, offset.value, searchValue.value).then((results) => {
                    books.value = results.data
                    booksCount.value = results.count
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

const editData = (id, data, mode) => {
    editId.value = id
    bookData.value = data
    modeData.value = mode
    showModal.value = !showModal.value
    // showForm.value=!showForm.value
}

const borrowData = (id, data, mode) => {
    showBorrow.value = !showBorrow.value
    // showForm.value=!showForm.value
}


const borrowedBookId = ref([])
const checkAvailability = () => {
    borrowedBooks.value.forEach((e) => {
        if (e.lbrr_returned == 0) {
            borrowedBookId.value.push(e.lbrr_bookid)
        }
    })

}

</script>
<template>
    <div>
        <div class="p-3 mb-4 border-bottom">
            <h5 class=" text-uppercase fw-bold">Book Shelf</h5>
        </div>

        <div v-if="preLoading">
            <NeuLoader1/>
        </div>

        <div v-else>
            <div class="p-3 d-flex gap-2 justify-content-between mb-3">
                <div class="input-group w-50">
                    <!-- <span class="input-group-text" id="searchaddon"><font-awesome-icon icon="fa-solid fa-search" /></span> -->
                    <input type="text" class="neu-input" placeholder="Search Here..." aria-label="search"
                        v-model="searchValue" @keyup.enter="search()" aria-describedby="searchaddon"
                        :disabled="preLoading ? true : false">
                </div>
                <div class="d-flex w-25 justify-content-end gap-2">
                    <button tabindex="-1" data-bs-toggle="modal" data-bs-target="#editdatamodal"
                        @click="editData('', [], 2)" type="button" class="neu-btn neu-green"
                        :disabled="preLoading ? true : false">
                        <font-awesome-icon icon="fa-solid fa-plus"/> Add New
                    </button>
                    <button tabindex="-1" data-bs-toggle="modal" data-bs-target="#borrowdatamodal" type="button"
                        @click="borrowData('', [], 2)"
                        class="neu-btn neu-blue" :disabled="preLoading ? true : false">
                        <font-awesome-icon icon="fa-solid fa-tag"/> Borrow
                    </button>

                </div>
            </div>
            <div class="table-responsive border p-3 small-font">
                <table class="neu-table mb-3">
                    <thead>
                        <tr>
                            <th style="color:#555555">#</th>
                            <th style="color:#555555" class="w-50">Book</th>
                            <th style="color:#555555" class="text-center">Commands</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="!preLoading && Object.keys(books).length" v-for="(app, index) in books">
                            <td class="align-middle">
                                <p>Accession No: <span class="fw-bold text-success"> {{ app.lbrb_accession_no ?
                                    app.lbrb_accession_no : 'N/A' }}</span></p>
                            </td>
                            <td class="align-middle text-start">
                                <p><span class="fw-bold">Title: </span> {{ app.lbrb_title ? app.lbrb_title : 'N/A' }}</p>
                                <p><span class="fw-bold">Author: </span> {{ app.lbrb_author ? app.lbrb_author : 'N/A' }}</p>
                                <p>
                                    <span class="fw-bold">Status: </span>
                                    {{ borrowedBookId.includes(app.lbrb_accession_no) ? 'Unavailable' : 'Available' }}
                                </p>
                                <p>
                                    <span class="fw-bold">Borrow: </span>
                                    {{ borrowedBookId.includes(app.lbrb_id) ? 'Borrowed' : 'Not Borrowed' }}
                                </p>



                            </td>
                            <td v-if="accessData[6].useracc_modifying == 1" class="align-middle">
                                <div class="d-flex gap-2 justify-content-center">
                                    <button data-bs-toggle="modal" data-bs-target="#editdatamodal"
                                        @click="editData(app.lbrb_id, app, 1)" type="button" title="Edit Record"
                                        class="neu-btn-sm neu-white">
                                        <font-awesome-icon icon="fa-solid fa-gear"/> Modify Book</button>
                                </div>
                            </td>
                            <td v-else class="align-middle">
                                N/A
                            </td>

                        </tr>
                        <tr v-if="!preLoading && !Object.keys(books).length">
                            <td class="p-3 text-center" colspan="7">
                                <NeuLoader4/>
                                <p class="fw-bold m-0">Nothing here yet!</p>
                                <p>The hamster took a break 💤 — try adding something new.</p>
                            </td>
                        </tr>
                        <tr v-if="preLoading && !Object.keys(books).length">
                            <td class="p-3 text-center" colspan="7">
                                <div class="m-3">
                                    <NeuLoader1 />
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="d-flex justify-content-between align-content-center" v-if="!preLoading">
                    <div class="d-flex gap-1">
                        <button :disabled="offset == 0 ? true : false" @click="paginate('prev')"
                            class="neu-btn neu-dark-gray">Prev</button>
                        <button :disabled="Object.keys(books).length < 10 ? true : false" @click="paginate('next')"
                            class="neu-btn neu-light-gray">Next</button>
                    </div>
                    <p class="">showing total of <span class="font-semibold">({{ booksCount }})</span> items</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Book  Modal -->
    <div class="modal fade" id="editdatamodal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Book Settings</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        @click="showModal = false"></button>
                </div>
                <div class="modal-body neu-bg small-font">
                    <LibraryBook v-if="showModal" :bookiddata="editId" :bookdata="bookData" :modedata="modeData"
                        :useriddata="userID" :borrowdata="borrowedBookId" />
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

    <!-- Borrow Book  Modal -->
    <div class="modal fade" id="borrowdatamodal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Borrow Book</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        @click="showBorrow = false"></button>
                </div>
                <div class="modal-body neu-bg small-font">
                    <LibraryBorrow v-if="showBorrow" :bookdata="books" :borrowdata="borrowedBookId" :useriddata="userID" />
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <div class="form-group">
                        <small id="emailHelp" class="form-text text-muted">We'll never share your personal information
                            with anyone
                            else (Data Privacy Act of 2012)</small>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                            @click="showBorrow = false">Close</button>
                        <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
                    </div>
                </div>
            </div>
        </div>
    </div>

</template>