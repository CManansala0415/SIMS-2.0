<script setup>
import { ref, onMounted, computed } from 'vue';
import {
    getQuarter,
    getProgram,
    getGradelvl,
    getProgramList,
    getSection,
    getPriceDetails,
    getFeeDetails,
    getRequestDetails,
    deleteItemRequest,
    getApplicant

} from "../Fetchers.js";
import Loader from '../snippets/loaders/Loading1.vue';
import StudentRequestModal from '../snippets/modal/StudentRequestModal.vue';
// import RenderModal from '../snippets/modal/RenderItem.vue';
import { getUserID } from "../../routes/user";
import { useRouter, useRoute } from 'vue-router'

const router = useRouter();
const preLoading = ref(true)
const quarter = ref([])
const gradelvl = ref([])
const program = ref([])
const course = ref([])
const section = ref([])
const booting = ref('')
const bootingCount = ref(0)
const limit = ref(10)
const offset = ref(0)
const requestedItemsCount = ref(0)
const searchValue = ref('')
const userID = ref('')
const showRequestModal = ref(false)
const showRenderModal = ref(false)
const price = ref([])
const fee = ref([])
const requestedItems = ref([])
const students = ref([])
const emit = defineEmits(['fetchUser'])
const accessData = ref([])

const booter = async () => {
    getProgram().then((results) => {
        program.value = results
        booting.value = 'Loading Program...'
        bootingCount.value += 1
    })
    getProgramList().then((results) => {
        course.value = results
        booting.value = 'Loading Courses...'
        bootingCount.value += 1
    })
    getGradelvl().then((results) => {
        gradelvl.value = results
        booting.value = 'Loading Levels...'
        bootingCount.value += 1
    })
    getQuarter().then((results) => {
        quarter.value = results
        booting.value = 'Loading Quarters...'
        bootingCount.value += 1
    })
    getSection().then((results) => {
        section.value = results
        booting.value = 'Loading Sections...'
        bootingCount.value += 1
    })
    getPriceDetails().then((results) => {
        price.value = results
        booting.value = 'Loading Prices...'
        bootingCount.value += 1
    })
    getFeeDetails(0, 0).then((results) => {
        fee.value = results.data
        booting.value = 'Loading Fees...'
        bootingCount.value += 1
    })
    // getUserID().then((results) => {
    //     userID.value = results.account.data.id
    //     emit('fetchUser', results)
    //     booting.value = 'Loading Users...'
    //     bootingCount.value += 1
    // })
}


onMounted(async () => {
    window.stop()
    getUserID().then(async(results1) => {
        userID.value = results1.account.data.id
        emit('fetchUser', results1)
        accessData.value = results1.access
        try {
            preLoading.value = true
            await booter().then(() => {
                getRequestDetails(limit.value, offset.value).then((results2) => {
                    requestedItems.value = results2.data.map((e) => {
                        let itemdesc = ''
                        let paystat = ''
                        let textcolor = ''
                        fee.value.forEach((f) => {
                            if (f.acf_id == e.acr_reqitem) {
                                itemdesc = f.acf_desc
                            }
                        })

                        if (e.acr_paystatus == 2) {
                            paystat = 'Paid'
                            textcolor = 'text-success fw-bold'
                        }
                        else if (e.acr_paystatus == 1) {
                            paystat = 'Partial'
                            textcolor = 'text-warning fw-bold'
                        }
                        else {
                            paystat = 'Unpaid'
                            textcolor = 'text-danger fw-bold'
                        }

                        return {
                            ...e,
                            acr_paystatusdesc: paystat,
                            paystat_color: textcolor,
                            acr_itemrequested: itemdesc
                        }

                    })


                    requestedItemsCount.value = results2.count
                    preLoading.value = false

                })
            })


        } catch (err) {
            preLoading.value = false
            alert('error loading the list default components')
        }
    }).catch((err) => {
        alert('Unauthorized Session, Please Log In')
        router.push("/");
        window.stop()
    })


})


const paginate = (mode) => {
    switch (mode) {
        case 'prev':
            if (offset.value <= 0) {
                offset.value = 0
            } else {
                requestedItems.value = []
                offset.value -= 10
                requestedItemsCount.value = 0
                preLoading.value = true
                getRequestDetails(limit.value, offset.value).then((results) => {
                    requestedItems.value = results.data
                    requestedItemsCount.value = results.count
                    preLoading.value = false
                })
            }
            break;
        case 'next':

            if (offset.value >= requestedItemsCount.value) {
                offset.value = requestedItemsCount.value
            } else {
                requestedItems.value = []
                offset.value += 10
                requestedItemsCount.value = 0
                preLoading.value = true
                getRequestDetails(limit.value, offset.value, null).then((results) => {
                    requestedItems.value = results.data
                    requestedItemsCount.value = results.count
                    preLoading.value = false
                })
            }
            break;
        case 'search':
            searchValue.value = searchValue.value.trim()
            if (searchValue.value || searchValue.value == '') {
                requestedItems.value = []
                offset.value = 0
                requestedItemsCount.value = 0
                preLoading.value = true
                getRequestDetails(limit.value, offset.value, searchValue.value).then((results) => {
                    requestedItems.value = results.data
                    requestedItemsCount.value = results.count
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

const requestedItem = ref({
    acr_personid: '',
    acr_reqitem: '',
    acr_paytype: '',
    acr_paystatus: '',
})

const settlement = (data, mode) => {
    if (mode == 1) {
        showRequestModal.value = true
    } else if (mode == 2) {
        showRenderModal.value = true
    } else {
        if (confirm("Are you sure you want to delete this item? this action cannot be reverted.") == true) {
            let del = {
                acr_id: data.acr_id,
                acr_updatedby: userID.value,
            }

            // getRequestDetails(0,0, data.acr_id).then((results)=>{
            //     if(results.data[0].acr_status == 1){
            //       deleteItemRequest(del, 2).then((results)=>{
            //           alert('Successfully Removed')
            //           location.reload()
            //       })
            //     }else{
            //         alert('Cannot proceed payment. /n This Item is removed from registrar. Please refresh the page')
            //     }
            // })
            deleteItemRequest(del, 2).then((results) => {
                alert('Successfully Removed')
                location.reload()
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
            <h5 class=" text-uppercase fw-bold">Students Request</h5>
        </div>

        <div class="p-1 d-flex gap-2 justify-content-between mb-3">
            <div class="input-group w-50">
                <span class="input-group-text" id="searchaddon"><font-awesome-icon icon="fa-solid fa-search"
                        /></span>
                <input type="text" class="form-control" placeholder="Search Here..." aria-label="search"
                    v-model="searchValue" @keyup.enter="search()" aria-describedby="searchaddon" :disabled="preLoading? true:false">
            </div>
            <div class="d-flex flex-wrap w-50 justify-content-end">
                <button tabindex="-1" data-bs-toggle="modal" data-bs-target="#addrequestmodal" @click="settlement('', 1)"
                    type="button" class="btn btn-sm btn-primary" :disabled="preLoading? true:false">
                    <font-awesome-icon icon="fa-solid fa-add" /> Add New
                </button>
            </div>
        </div>

        <div class="table-responsive border p-3 small-font">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th style="background-color: #237a5b;" class="text-white">Request ID</th>
                        <th style="background-color: #237a5b;" class="text-white">Requestor</th>
                        <th style="background-color: #237a5b;" class="text-white">Requested Item</th>
                        <th style="background-color: #237a5b;" class="text-white">Date Requested</th>
                        <th style="background-color: #237a5b;" class="text-white">Payment Status</th>
                        <th style="background-color: #237a5b;" class="text-white">Commands</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="!preLoading && Object.keys(requestedItems).length" v-for="(req, index) in requestedItems">
                        <td class="align-middle">
                           {{ req.acr_id }}
                        </td>
                        <td class="align-middle">
                           {{ req.acr_personname }}
                        </td>
                        <td class="align-middle">
                           {{ req.acr_itemrequested }}
                        </td>
                        <td class="align-middle">
                           {{ req.acr_dateadded }}
                        </td>
                        <td class="align-middle">
                            <span :class="req.paystat_color">{{ req.acr_paystatusdesc }}</span>
                        </td>
                        <td v-if="accessData[2].useracc_modifying == 1" class="align-middle">
                            <div v-if="req.acr_status == 0" class="text-center">
                                <p class="text-danger fw-bold">Cancelled</p>
                            </div>
                            <div v-else>
                                <div v-if="req.acr_paystatus == 1 && req.acr_rendered == 1" class="text-center">
                                    <p class="text-success fw-bold">Completed</p>
                                </div>
                                <div v-else class="d-flex gap-1 align-content-center justify-content-center">
                                    <button title="Render Request" @click="settlement(req, 2)"
                                        :disabled="req.acr_paystatus != 2 ? true : false" class="btn btn-sm btn-primary">
                                        Render
                                    </button>
                                    <button title="Delete Request" @click="settlement(req, 3)" class="btn btn-sm btn-danger">
                                        Drop
                                    </button>
                                </div>
                            </div>
                        </td>
                        <td v-else class="align-middle">
                            N/A
                        </td>
                    </tr>
                    <tr v-if="!preLoading && !Object.keys(requestedItems).length">
                        <td class="align-middle" colspan="7">
                            No Records Found
                        </td>
                    </tr>
                    <tr v-if="preLoading && !Object.keys(requestedItems).length">
                        <td class="align-middle" colspan="7">
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
                    <button :disabled="Object.keys(requestedItems).length < 10 ? true : false" @click="paginate('next')"
                        class="btn btn-sm btn-secondary">Next</button>
                </div>
                <p class="">showing total of <span class="font-semibold">({{ requestedItemsCount }})</span> items
                </p>
            </div>
        </div>
    </div>

     <!-- Add Request Modal -->
     <div class="modal fade" id="addrequestmodal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        @click="showRequestModal = false"></button>
                </div>
                <div class="modal-body">
                    <StudentRequestModal v-if="showRequestModal" :feeData="fee"/>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <div class="form-group">
                        <small id="emailHelp" class="form-text text-muted">We'll never share your personal information
                            with anyone
                            else (Data Privacy Act of 2012)</small>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                            @click="showRequestModal = false">Close</button>
                        <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
                    </div>
                </div>
            </div>
        </div>
    </div>

     <!-- Render Request Modal -->
     <div class="modal fade" id="renderrequestmodal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        @click="showRenderModal = false"></button>
                </div>
                <div class="modal-body">
                    <StudentRequestModal v-if="showRenderModal" :feeData="fee"/>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <div class="form-group">
                        <small id="emailHelp" class="form-text text-muted">We'll never share your personal information
                            with anyone
                            else (Data Privacy Act of 2012)</small>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                            @click="showRenderModal = false">Close</button>
                        <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
                    </div>
                </div>
            </div>
        </div>
    </div>

</template>