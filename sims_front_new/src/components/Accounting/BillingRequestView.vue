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
    getTransactionDetails,
    deleteItemRequest,
    getApplicant,
    getAcademicDefaults

} from "../Fetchers.js";
import Loader from '../snippets/loaders/Loading1.vue';
import { getUserID } from "../../routes/user";
import AccountingPaymentModal from '../snippets/modal/AccountingPaymentModal.vue';
import { useRouter, useRoute } from 'vue-router'
import SearchQR from '../snippets/tech/SearchQR.vue';

const router = useRouter();
const showDownloadModal = ref(false)
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
const showPaymentModal = ref(false)
const price = ref([])
const fee = ref([])
const requestedItems = ref([])
const studentsAccount = ref([])
const emit = defineEmits(['fetchUser', 'doneLoading'])
const accessData = ref([])
const searchFname = ref('')
const searchMname = ref('')
const searchLname = ref('')

const booter = async () => {
    // getProgram().then((results) => {
    //     program.value = results
    //     booting.value = 'Loading Program...'
    //     bootingCount.value += 1
    // })
    // getProgramList().then((results) => {
    //     course.value = results
    //     booting.value = 'Loading Courses...'
    //     bootingCount.value += 1
    // })
    // getGradelvl().then((results) => {
    //     gradelvl.value = results
    //     booting.value = 'Loading Levels...'
    //     bootingCount.value += 1
    // })
    // getQuarter().then((results) => {
    //     quarter.value = results
    //     booting.value = 'Loading Quarters...'
    //     bootingCount.value += 1
    // })
    // getSection().then((results) => {
    //     section.value = results
    //     booting.value = 'Loading Sections...'
    //     bootingCount.value += 1
    // })
    getAcademicDefaults().then((results) => {
        gradelvl.value = results.gradelvl
        quarter.value = results.quarter
        course.value = results.course
        section.value = results.section
        booting.value = 'Loading Academic Information'
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
    //     // user.value = results.account.data.name
    //     userID.value = results.account.data.id
    //     emit('fetchUser', results)
    //     booting.value = 'Loading Users...'
    //     bootingCount.value += 1
    // })

}

const mapper = (results) => {
    requestedItems.value = results.data.map((e) => {
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

    requestedItemsCount.value = results.count
    preLoading.value = false
    emit('doneLoading', false)
}

onMounted(async () => {
    getUserID().then(async(results1) => {
        // user.value = results.account.data.name
        userID.value = results1.account.data.id
        accessData.value = results1.access

        emit('fetchUser', results1)
        try {
            preLoading.value = true
            await booter().then(() => {
                getTransactionDetails(limit.value, offset.value, searchFname.value, searchMname.value, searchLname.value, 1).then((results2) => {
                    // console.log(results2)
                    mapper(results2)
                })
            })

        } catch (err) {
            
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
        // router.push("/");
        // window.stop()
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
                requestedItems.value = []
                offset.value -= 10
                requestedItemsCount.value = 0
                preLoading.value = true
                getTransactionDetails(limit.value, offset.value, searchFname.value, searchMname.value, searchLname.value,3).then((results) => {
                    mapper(results)
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
                getTransactionDetails(limit.value, offset.value, searchFname.value, searchMname.value, searchLname.value,3).then((results) => {
                    mapper(results)
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
                getTransactionDetails(limit.value, offset.value, searchFname.value, searchMname.value, searchLname.value,3).then((results) => {
                    mapper(results)
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

const requestedItem = ref({
    acr_personid: '',
    acr_reqitem: '',
    acr_paytype: '',
    acr_paystatus: '',
})

const settlement = (data, mode) => {
    if (mode == 2) {
        studentsAccount.value = data
        showPaymentModal.value = true
    }
}


const excelDownload = () => {
    showDownloadModal.value = true
}

const showQRScanner = ref(false)
const getData = (result) =>{
    // console.log(result)
    requestedItems.value = result
    showQRScanner.value = !showQRScanner
    document.getElementById('hideqrscanner').click();
}

</script>
<template>
    <div>
        <div class="p-3 mb-4 border-bottom">
            <h5 class=" text-uppercase fw-bold">Billing Request</h5>
        </div>

        <div class="p-1 d-flex gap-2 justify-content-between mb-3">
            <div class="d-flex gap-2 justify-content-center align-content-center">
                <input type="text" v-model="searchFname" @keyup.enter="search()"
                    class="form-control w-100" :disabled="preLoading?true:false" placeholder="First Name"/>
                <input type="text" v-model="searchMname" @keyup.enter="search()"
                    class="form-control w-100" :disabled="preLoading?true:false" placeholder="Middle Name"/>
                <input type="text" v-model="searchLname" @keyup.enter="search()"
                    class="form-control w-100" :disabled="preLoading?true:false" placeholder="Last Name"/>
                <button @click="search()" type="button" class="btn btn-sm btn-info text-white w-100" tabindex="-1" :disabled="preLoading?true:false">
                    Search
                </button>
                <button @click="showQRScanner = true" data-bs-toggle="modal" data-bs-target="#scanqrmodal" type="button" class="btn btn-sm btn-dark text-white w-100" tabindex="-1" :disabled="preLoading?true:false">
                    Scan QR 
                </button>
            </div>
            <div class="d-flex flex-wrap w-50 justify-content-end gap-2">
                <button tabindex="-1" data-bs-toggle="modal" data-bs-target="#downloadmodal" @click="excelDownload()"
                    type="button" class="btn btn-sm btn-primary" :disabled="preLoading ? true : false">
                    <font-awesome-icon icon="fa-solid fa-add" /> Download Excel
                </button>
            </div>
        </div>

        <div class="table-responsive border p-3 small-font">
            <table class="table table-hover table-fixed" style="text-transform:uppercase">
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
                        <td v-if="accessData[19].useracc_modifying == 1" class="align-middle p-2">
                            <div class="d-flex gap-2 justify-content-center align-content-center">
                                <div v-if="req.acr_status == 0" class="text-center">
                                    <span class="text-danger fw-bold">Cancelled</span>
                                </div>
                                <div v-else>
                                    <div v-if="req.acr_paystatus == 2" class="text-center">
                                        <button class="btn btn-sm btn-secondary" title="Complete Payment" data-bs-toggle="modal" data-bs-target="#settlementmodal" @click="settlement(req, 2)">
                                            Completed
                                        </button>
                                    </div>
                                    <div v-else class="rounded-md flex gap-1 items-center">
                                        <button class="btn btn-sm btn-secondary" title="Complete Payment" data-bs-toggle="modal" data-bs-target="#settlementmodal" @click="settlement(req, 2)">
                                            Payment
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td v-else class="align-middle p-2">
                            N/A
                        </td>
                    </tr>
                    <tr v-if="!preLoading && !Object.keys(requestedItems).length" style="text-transform:none">
                        <td class="p-3 text-center" colspan="6">
                            No Records Found
                        </td>
                    </tr>
                    <tr v-if="preLoading && !Object.keys(requestedItems).length" style="text-transform:none">
                        <td class="p-3 text-center" colspan="6">
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
                <p class="">showing total of <span class="font-semibold">({{ requestedItemsCount }})</span> items</p>
            </div>
        </div>
    </div>

    <!-- Settlement Modal -->
    <div class="modal fade" id="settlementmodal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Payment Settlement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        @click="showPaymentModal = false"></button>
                </div>
                <div class="modal-body">
                    <AccountingPaymentModal v-if="showPaymentModal" :accountData="studentsAccount" :billTypeData="2" />
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <div class="form-group">
                        <small id="emailHelp" class="form-text text-muted">We'll never share your personal information
                            with anyone
                            else (Data Privacy Act of 2012)</small>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                            @click="showPaymentModal = false">Close</button>
                        <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <!-- Scan ID Modal -->
    <div class="modal fade" id="scanqrmodal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">QR Scanner</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        @click="showQRScanner = false" id="hideqrscanner"></button>
                </div>
                <div class="modal-body">
                     <SearchQR v-if="showQRScanner" @fetchData="getData" modeData="5"/>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <div class="form-group">
                        <small id="emailHelp" class="form-text text-muted">We'll never share your personal information
                            with anyone
                            else (Data Privacy Act of 2012)</small>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                            @click="showQRScanner = false">Close</button>
                        <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>