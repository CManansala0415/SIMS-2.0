<script setup>
import { ref, onMounted, computed } from 'vue';
// import AccItem from '../snippets/modal/AccItem.vue';
import Loader from '../snippets/loaders/Loading1.vue';
import { getUserID } from "../../routes/user";
import { useRouter, useRoute } from 'vue-router';
import { getFeeDetails, addAccountingItem } from '../Fetchers.js';
import AccountingItemsModal from '../snippets/modal/AccountingItemsModal.vue';

const limit = ref(10)
const offset = ref(0)
const fee = ref([])
const feeCount = ref(0)
const preLoading = ref(false)
const searchValue = ref('')
const userID = ref('')
const showAddItemModal = ref(false)

const booting = ref('')
const bootingCount = ref(0)
const emit = defineEmits(['fetchUser'])

const booter = async () => {
    getUserID().then((results) => {
        booting.value = 'Loading Items...'
        bootingCount.value += 1
        userID.value = results.account.data.id
        emit('fetchUser', results)
    })
}

onMounted(async () => {


    try {
        preLoading.value = true
        await booter().then((results) => {
            booting.value = 'Loading Items...'
            bootingCount.value += 1
            getFeeDetails(limit.value, offset.value).then((results) => {
                fee.value = results.data
                feeCount.value = results.count
                preLoading.value = false
            })
        })

    } catch (err) {
        preLoading.value = false
        alert('error loading the list default components')
    }

})

const paginate = (mode) => {
    switch (mode) {
        case 'prev':
            if (offset.value <= 0) {
                offset.value = 0
            } else {
                fee.value = []
                offset.value -= 10
                feeCount.value = 0
                preLoading.value = true
                getFeeDetails(limit.value, offset.value).then((results) => {
                    fee.value = results.data
                    feeCount.value = results.count
                    preLoading.value = false
                })
            }
            break;
        case 'next':

            if (offset.value >= feeCount.value) {
                offset.value = feeCount.value
            } else {
                fee.value = []
                offset.value += 10
                feeCount.value = 0
                preLoading.value = true
                getFeeDetails(limit.value, offset.value, null).then((results) => {
                    fee.value = results.data
                    feeCount.value = results.count
                    preLoading.value = false
                })
            }
            break;
        case 'search':
            searchValue.value = searchValue.value.trim()
            if (searchValue.value || searchValue.value == '') {
                fee.value = []
                offset.value = 0
                feeCount.value = 0
                preLoading.value = true
                getFeeDetails(limit.value, offset.value, searchValue.value).then((results) => {
                    fee.value = results.data
                    feeCount.value = results.count
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


const itemValue = ref([])
const preloading = ref(false)

const itemModal = (type, data) => {
    if (type == 1) {
        itemValue.value = []
        showAddItemModal.value = !showAddItemModal.value
    } else if (type == 2) {
        itemValue.value = data
        showAddItemModal.value = !showAddItemModal.value
    } else {
        if (confirm("Are you sure you want to delete this item") == true) {
            preloading.value = true
            let x = {
                acf_id: data.acf_id,
                acf_user: userID.value,
                acf_delete: true
            }
            addAccountingItem(x).then((results) => {
                if (results.status != 204) {
                    alert('Delete Failed')
                    // location.reload()
                } else {
                    alert('Delete Successful')
                    location.reload()
                }
            })
        } else {
            return false;
        }
    }
}

</script>
<template>
    <div>
        <div class="p-3 mb-4 border-bottom">
            <h5 class=" text-uppercase fw-bold">Accounting Items</h5>
        </div>

        <div class="p-1 d-flex gap-2 justify-content-between mb-3">
            <div class="input-group w-50">
                <span class="input-group-text" id="searchaddon"><font-awesome-icon icon="fa-solid fa-search" /></span>
                <input type="text" class="form-control" placeholder="Search Here..." aria-label="search"
                    v-model="searchValue" @keyup.enter="search()" aria-describedby="searchaddon"
                    :disabled="preLoading ? true : false">
            </div>
            <div class="d-flex flex-wrap w-50 justify-content-end gap-2">
                <button tabindex="-1" data-bs-toggle="modal" data-bs-target="#editdatamodal" @click="itemModal(1)"
                    type="button" class="btn btn-sm btn-primary" :disabled="preLoading ? true : false">
                    <font-awesome-icon icon="fa-solid fa-add" /> Add New
                </button>
            </div>
        </div>
        <div class="table-responsive border p-3 small-font">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th style="background-color: #237a5b;" class="text-white">Fee ID</th>
                        <th style="background-color: #237a5b;" class="text-white">First Name</th>
                        <th style="background-color: #237a5b;" class="text-white">Price</th>
                        <th style="background-color: #237a5b;" class="text-white">Commands</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="!preLoading && Object.keys(fee).length" v-for="(f, index) in fee">
                        <td class="align-middle">
                            {{ f.acf_id }}
                        </td>
                        <td class="align-middle">
                            {{ f.acf_desc }}
                        </td>
                        <td class="align-middle">
                            {{ f.acf_price }}
                        </td>
                        <td class="align-middle">
                            <div class="d-flex gap-2 justify-content-center">
                                <button class="btn btn-sm btn-secondary" data-bs-toggle="modal"
                                    data-bs-target="#editdatamodal" title="Edit Data"
                                    @click="itemModal(2, f)" :disabled="preloading? true:false">
                                    <font-awesome-icon icon="fa-solid fa-pen" />
                                </button>
                                <button class="btn btn-sm btn-secondary" data-bs-toggle="modal"
                                    data-bs-target="#dispensemodal" title="Delete Item"
                                    @click="itemModal(3, f)" :disabled="preloading? true:false">
                                    <font-awesome-icon icon="fa-solid fa-trash" />
                                </button>
                            </div>
                        </td>

                    </tr>
                    <tr v-if="!preLoading && !Object.keys(fee).length">
                        <td class="p-3 text-center" colspan="4">
                            No Records Found
                        </td>
                    </tr>
                    <tr v-if="preLoading && !Object.keys(fee).length">
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
                    <button :disabled="Object.keys(fee).length < 10 ? true : false" @click="paginate('next')"
                        class="btn btn-sm btn-secondary">Next</button>
                </div>
                <p class="">showing total of <span class="font-semibold">({{ feeCount }})</span> items</p>
            </div>
        </div>
    </div>

    <!-- Edit Medical Modal -->
    <div class="modal fade" id="editdatamodal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Accounting Items</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        @click="showAddItemModal = false"></button>
                </div>
                <div class="modal-body">
                    <AccountingItemsModal v-if="showAddItemModal" :itemData="itemValue"/>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <div class="form-group">
                        <small id="emailHelp" class="form-text text-muted">We'll never share your personal information
                            with anyone
                            else (Data Privacy Act of 2012)</small>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                            @click="showAddItemModal = false">Close</button>
                        <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>