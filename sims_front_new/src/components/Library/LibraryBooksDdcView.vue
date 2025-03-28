<script setup>
import { ref, onMounted, computed } from 'vue';
import Loader from '../snippets/loaders/Loading1.vue';
import LibraryDdcModal from '../snippets/modal/LibraryDdcModal.vue';
import { getUserID } from "../../routes/user";
import { useRouter, useRoute } from 'vue-router';
import {
    getBooksDdc,
    addBooksDdc
} from "../Fetchers.js";

const limit = ref(10)
const offset = ref(0)
const booksDdc = ref([])
const booksDdcCount = ref(0)
const preLoading = ref(false)
const searchValue = ref('')
const userID = ref('')
const router = useRouter();
const showForm = ref(false)
const showModal = ref(false)
const booting = ref('')
const bootingCount = ref(0)
const editId = ref('')
const ddcData = ref([])
const modeData = ref('')


const booter = async () => {

    // getBookType().then((results)=>{
    //     books.value = results
    //     booting.value = 'Loading Genders'
    //     bootingCount.value += 1
    // })

}

onMounted(async () => {
    getUserID().then((results) => {
        userID.value = results.account.data.id
    }).catch((err) => {

    })

    try {
        preLoading.value = true
        await booter().then((results) => {
            booting.value = 'Loading Books...'
            bootingCount.value += 1
            getBooksDdc(limit.value, offset.value).then((results) => {
                // console.log(results)
                booksDdc.value = results.data
                booksDdcCount.value = results.count
                preLoading.value = false
            })
        })

    } catch (err) {
        preLoading.value = false
        alert('error loading the list default components')
    }

})

const deleteDdc = (id) => {
    if (confirm("Are you sure you want to delete this registry? this action cannot be reverted.") == true) {
        let x = {
            lbrc_id: id,
            lbrc_updatedby: userID.value,
            lbrc_mode: 3 // means delete
        }
        addBooksDdc(x).then((results) => {
            alert('Delete Successful')
            location.reload()
        })
    } else {
        return false;
    }
}

const paginate = (mode) => {
    switch (mode) {
        case 'prev':
            if (offset.value <= 0) {
                offset.value = 0
            } else {
                booksDdc.value = []
                offset.value -= 10
                booksDdcCount.value = 0
                preLoading.value = true
                getBooksDdc(limit.value, offset.value).then((results) => {
                    booksDdc.value = results.data
                    booksDdcCount.value = results.count
                    preLoading.value = false
                })
            }
            break;
        case 'next':

            if (offset.value >= booksDdcCount.value) {
                offset.value = booksDdcCount.value
            } else {
                booksDdc.value = []
                offset.value += 10
                booksDdcCount.value = 0
                preLoading.value = true
                getBooksDdc(limit.value, offset.value, null).then((results) => {
                    booksDdc.value = results.data
                    booksDdcCount.value = results.count
                    preLoading.value = false
                })
            }
            break;
        case 'search':
            searchValue.value = searchValue.value.trim()
            if (searchValue.value || searchValue.value == '') {
                booksDdc.value = []
                offset.value = 0
                booksDdcCount.value = 0
                preLoading.value = true
                getBooksDdc(limit.value, offset.value, searchValue.value).then((results) => {
                    booksDdc.value = results.data
                    booksDdcCount.value = results.count
                    preLoading.value = false
                }).catch((err) => {
                    // console.log(err)
                })
            } else {
                alert('Please search a valid record')
                preLoading.value = false
            }
            break;

    }
}

const search = () => {
    paginate('search')
}

const editData = (id, data, mode) => {
    editId.value = id
    ddcData.value = data
    modeData.value = mode
    showModal.value = !showModal.value
    // showForm.value=!showForm.value
}
</script> 
<template>
    <div>
        <div class="p-3 mb-4 border-bottom">
            <h5 class=" text-uppercase fw-bold">Book DDC</h5>
        </div>

        <div class="p-1 d-flex gap-2 justify-content-between mb-3">
            <div class="input-group w-50">
                <span class="input-group-text" id="searchaddon"><font-awesome-icon icon="fa-solid fa-search" /></span>
                <input type="text" class="form-control" placeholder="Search Here..." aria-label="search"
                    v-model="searchValue" @keyup.enter="search()" aria-describedby="searchaddon"
                    :disabled="preLoading ? true : false">
            </div>
            <div class="d-flex flex-wrap w-50 justify-content-end gap-2">
                <button tabindex="-1" data-bs-toggle="modal" data-bs-target="#editdatamodal"
                    @click="editData('', [], 2)" type="button" class="btn btn-sm btn-primary"
                    :disabled="preLoading ? true : false">
                    <font-awesome-icon icon="fa-solid fa-add" /> Add New
                </button>
            </div>
        </div>
        <div class="table-responsive border p-3 small-font">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th style="background-color: #237a5b;" class="text-white">ID</th>
                        <th style="background-color: #237a5b;" class="text-white">DDC</th>
                        <th style="background-color: #237a5b;" class="text-white">Title</th>
                        <th style="background-color: #237a5b;" class="text-white">Status</th>
                        <th style="background-color: #237a5b;" class="text-white">Commands</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="!preLoading && Object.keys(booksDdc).length" v-for="(app, index) in booksDdc">
                        <td class="align-middle">
                            {{ app.lbrc_id }}
                        </td>
                        <td class="align-middle">
                            {{ app.lbrc_ddc }}
                        </td>
                        <td class="align-middle">
                            {{ app.lbrc_desc }}
                        </td>
                        <td class="align-middle">
                            {{ app.lbrc_status == 1 ? 'Active' : 'Inactive' }}
                        </td>
                        <td class="align-middle">
                            <div class="d-flex gap-2 justify-content-center">
                                <button data-bs-toggle="modal" data-bs-target="#editdatamodal"
                                    @click="editData(app.lbrc_id, app, 1)" type="button" title="Edit Record"
                                    class="btn btn-secondary btn-sm">
                                    <font-awesome-icon icon="fa-solid fa-gear" /></button>
                                <button @click="deleteDdc(app.lbrc_id)" type="button" title="Edit Record"
                                    class="btn btn-secondary btn-sm">
                                    <font-awesome-icon icon="fa-solid fa-trash" /></button>
                            </div>
                        </td>

                    </tr>
                    <tr v-if="!preLoading && !Object.keys(booksDdc).length">
                        <td class="p-3 text-center" colspan="7">
                            No Records Found
                        </td>
                    </tr>
                    <tr v-if="preLoading && !Object.keys(booksDdc).length">
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
                    <button :disabled="Object.keys(booksDdc).length < 10 ? true : false" @click="paginate('next')"
                        class="btn btn-sm btn-secondary">Next</button>
                </div>
                <p class="">showing total of <span class="font-semibold">({{ booksDdcCount }})</span> items</p>
            </div>
        </div>
    </div>

    <!-- Edit DDC  Modal -->
    <div class="modal fade" id="editdatamodal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Book DDC Settings</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        @click="showModal = false"></button>
                </div>
                <div class="modal-body">
                    <LibraryDdcModal v-if="showModal" :ddciddata="editId" :ddcdata="ddcData" :modedata="modeData"
                        :useriddata="userID" />
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