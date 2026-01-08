<script setup>
import { ref, onMounted, computed } from 'vue';
// import AccItem from '../snippets/modal/AccItem.vue';
import Loader from '../snippets/loaders/Loading1.vue';
import { getUserID } from "../../routes/user";
import { useRouter, useRoute } from 'vue-router';
import { getFeeDetails, getTuitionInformation, editAccountingTuition, getAcademicDefaults, getQuarter, getAcademicStatus } from '../Fetchers.js';
import AccountingTuitionEdit from '../snippets/modal/AccountingTuitionEdit.vue';

const router = useRouter();
const limit = ref(10)
const offset = ref(0)
const fee = ref([])
const tuitionInfoCount = ref(0)
const tuitionInfo = ref([])
const preLoading = ref(true)
const searchValue = ref('')
const userID = ref('')
const showAddItemModal = ref(false)

const booting = ref('')
const bootingCount = ref(0)
const emit = defineEmits(['fetchUser', 'doneLoading'])
const accessData = ref([])
const course = ref([])
const section = ref([])
const program = ref([])
const degree = ref([])
const gradelvl = ref([])
const quarter = ref([])
const activeEnrollment = ref(false)
const booter = async () => {
    // getUserID().then((results) => {
    //     booting.value = 'Loading Items...'
    //     bootingCount.value += 1
    //     userID.value = results.account.data.id
    //     emit('fetchUser', results)
    // })
    getFeeDetails(limit.value, offset.value).then((results2) => {
        fee.value = results2.data
    })
    getAcademicDefaults().then((results) => {
        gradelvl.value = results.gradelvl
        // quarter.value = results.quarter
        course.value = results.course
        section.value = results.section
        program.value = results.program
        degree.value = results.degree
        // subject.value = results.subject
        booting.value = 'Loading Academic Information'
        bootingCount.value += 1
    })

    getQuarter().then((results)=>{
       quarter.value = results
    })

    getAcademicStatus(1,'cs_05').then((results) => {
        activeEnrollment.value = results[0].sett_status == 1? true:false
    })
}
onMounted(async () => {
    
    getUserID().then(async(results1) => {
        userID.value = results1.account.data.id
        emit('fetchUser', results1)
        accessData.value = results1.access

        
        try {

            await booter().then(() => {
                booting.value = 'Loading Items...'
                bootingCount.value += 1

                getTuitionInformation(limit.value, offset.value).then((results2) => {
                    console.log(activeEnrollment.value)
                    tuitionInfo.value = results2.data
                    tuitionInfoCount.value = results2.count
                    preLoading.value = false
                    emit('doneLoading', false)
                })
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

const paginate = (mode) => {
    switch (mode) {
        case 'prev':
            if (offset.value <= 0) {
                offset.value = 0
            } else {
                tuitionInfo.value = []
                offset.value -= 10
                tuitionInfoCount.value = 0
                getTuitionInformation(limit.value, offset.value).then((results) => {
                    tuitionInfo.value = results.data
                    tuitionInfoCount.value = results.count
                    preLoading.value = false
                })
            }
            break;
        case 'next':

            if (offset.value >= tuitionInfoCount.value) {
                offset.value = tuitionInfoCount.value
            } else {
                tuitionInfo.value = []
                offset.value += 10
                tuitionInfoCount.value = 0
                preLoading.value = true
                getTuitionInformation(limit.value, offset.value, null).then((results) => {
                    tuitionInfo.value = results.data
                    tuitionInfoCount.value = results.count
                    preLoading.value = false
                })
            }
            break;
        case 'search':
            searchValue.value = searchValue.value.trim()
            if (searchValue.value || searchValue.value == '') {
                tuitionInfo.value = []
                offset.value = 0
                tuitionInfoCount.value = 0
                preLoading.value = true
                getTuitionInformation(limit.value, offset.value, searchValue.value).then((results) => {
                    tuitionInfo.value = results.data
                    tuitionInfoCount.value = results.count
                    preLoading.value = false
                }).catch((err) => {
                    // console.log(err)
                })
            } else {
                // alert('Please search a valid record')
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

const toBeEdited = ref([])
const manageSetup = ref(false)

const itemModal = (mode, data) =>{
    if(mode == 1){
        //add new
        toBeEdited.value = []
        showAddItemModal.value = !showAddItemModal.value  
        manageSetup.value = false
    }else if (mode == 2){
        //edit data
        toBeEdited.value = data
        showAddItemModal.value = !showAddItemModal.value    
        manageSetup.value = false
    }else if (mode == 3){
        toBeEdited.value = data
        showAddItemModal.value = !showAddItemModal.value    
        manageSetup.value = true
    }
    else{
        //delete data
        manageSetup.value = false
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
                preLoading.value = true

                let x = {
                    act_id: data.act_id,
                    act_user: userID.value,
                    act_mode: 'delete'
                }

                editAccountingTuition(x).then((results) => {
                    if (results.status != 200) {
                        // alert('Delete Failed')
                        Swal.fire({
                            title: "Action Failed",
                            text: "Delete failed, please try again later or contact the administrator",
                            icon: "error"
                        }).then(()=>{
                            preLoading.value = false
                        });
                        // location.reload()
                    } else {
                        // alert('Delete Successful')
                        Swal.fire({
                            title: "Action Completed",
                            text: "Successfully Deleted",
                            icon: "success"
                        }).then(()=>{
                            location.reload()
                        });
                    }
                })
            }
        });
    }
}

const deactivateCharge = (data) =>{

    Swal.fire({
            title: 'Update Item',
            html: `Are you sure to to change its status?`,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, Im Change it!"
    }).then(async (result) => {
        if (result.isConfirmed) {
            getAcademicStatus(1,'cs_05').then((results) => {
                activeEnrollment.value = results[0].sett_status == 1? true:false

                if(activeEnrollment.value){
                    Swal.fire({
                        title: "Action Denied",
                        text: "Cannot change charge status while enrollment is active.",
                        icon: "error"
                    });
                    return
                }else{
                    Swal.fire({
                        title: "Saving Updates",
                        text: "Please wait while we check all series details.",
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    let x = {
                        ...data,
                        act_mode: 'charge',
                        act_status: data.act_status == 1? data.act_status = 0: data.act_status = 1
                    }

                    editAccountingTuition(x).then((results)=>{
                        Swal.close()
                
                        if (results.status == 200) {
                            Swal.fire({
                                title: "Update Successful",
                                text: "Changes applied, refreshing the page",
                                icon: "success"
                            }).then(()=>{
                                location.reload()
                            });
                        } else {
                            Swal.fire({
                                title: "Update Failed",
                                text: "Unknown error occured, try again later",
                                icon: "error"
                            }).then(()=>{
                                // location.reload()
                            });
                        }
                    })
                }
            })
        }
    })
}



</script>
<template>
    <div>
        <div class="p-3 mb-4 border-bottom">
            <h5 class=" text-uppercase fw-bold">Tuition / Charges Setup</h5>
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
                        <th style="background-color: #237a5b;" class="text-white w-25">Description</th>
                        <th style="background-color: #237a5b;" class="text-white w-10">Semester</th>
                        <th style="background-color: #237a5b;" class="text-white w-10">Program</th>
                        <th style="background-color: #237a5b;" class="text-white w-25">Course</th>
                        <th style="background-color: #237a5b;" class="text-white w-10">Grade/Year Level</th>
                        <th style="background-color: #237a5b;" class="text-white w-10">Section</th>
                        <th style="background-color: #237a5b;" class="text-white w-10">Commands</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="!preLoading && Object.keys(tuitionInfo).length" v-for="(act, index) in tuitionInfo">
                        <td class="align-middle">
                            {{ act.act_description }}
                        </td>
                        <td class="align-middle">
                             {{ act.quar_desc?act.quar_desc:'All Semester' }}
                        </td>
                        <td class="align-middle">
                            {{ act.prog_desc? act.prog_desc == 'Senior High-School'? 'SHS':act.prog_desc:'All Programs' }}
                        </td>
                        <td class="align-middle">
                            {{ act.prog_code?act.prog_code:'All Courses' }}
                        </td>
                        <td class="align-middle">
                            {{ act.gradelvl_desc?act.gradelvl_desc:'All Grade Levels' }}
                        </td>
                        <td class="align-middle">
                            {{ act.sec_desc?act.sec_desc:'All Sections' }}
                        </td>
                        <td v-if="accessData[16].useracc_modifying == 1" class="align-middle">
                            <div class="d-flex gap-2 justify-content-center">
                                <button  v-if="!activeEnrollment" class="btn btn-sm btn-secondary" data-bs-toggle="modal" @click="itemModal(2, act)"
                                    data-bs-target="#editdatamodal" title="Edit Data">
                                    <font-awesome-icon icon="fa-solid fa-pen" />
                                </button>
                                <button  v-if="!activeEnrollment" class="btn btn-sm btn-secondary"
                                    title="Delete Item" @click="itemModal(4, act)">
                                    <font-awesome-icon icon="fa-solid fa-trash" />
                                </button>
                                <button class="btn btn-sm btn-secondary" data-bs-toggle="modal" @click="itemModal(3, act)"
                                    data-bs-target="#editdatamodal" title="Edit Data">
                                    <font-awesome-icon icon="fa-solid fa-gear" />
                                </button>
                                <button v-if="!activeEnrollment" class="btn btn-sm" :class="act.act_status == 1?'btn-success':'btn-danger'" @click="deactivateCharge(act)"
                                    :title="act.act_status == 1?'Active':'Inactive'">
                                    <font-awesome-icon icon="fa-solid fa-power-off"/>
                                </button>
                            </div>
                        </td>
                        <td v-else class="align-middle">
                            N/A
                        </td>
                    </tr>
                    <tr v-if="!preLoading && !Object.keys(tuitionInfo).length">
                        <td class="p-3 text-center" colspan="7">
                            No Records Found
                        </td>
                    </tr>
                    <tr v-if="preLoading && !Object.keys(tuitionInfo).length">
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
                    <button :disabled="Object.keys(tuitionInfo).length < 10 ? true : false" @click="paginate('next')"
                        class="btn btn-sm btn-secondary">Next</button>
                </div>
                <p class="">showing total of <span class="font-semibold">({{ tuitionInfoCount }})</span> items</p>
            </div>
        </div>
    </div>

    <!-- Edit Medical Modal -->
    <div class="modal fade" id="editdatamodal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div 
            class="modal-dialog modal-dialog-centered modal-dialog-scrollable"
            :class="manageSetup ? 'modal-fullscreen' : 'modal-md'"
            >
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Accounting Items</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        @click="showAddItemModal = false"></button>
                </div>
                <div class="modal-body">
                    <AccountingTuitionEdit v-if="showAddItemModal" :itemData="toBeEdited"
                    :courseData="course" :sectionData="section" :programData="program" :gradelvlData="gradelvl" :manageSetupData="manageSetup" :quarterData="quarter" :activeEnrollmentData="activeEnrollment"/>
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