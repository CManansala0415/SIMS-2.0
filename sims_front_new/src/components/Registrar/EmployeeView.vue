<script setup>
import { ref, onMounted, computed } from 'vue';
import {
    getEmployee,
    getGender,
    getCivilStatus,
    getTaggedSubject,
    deleteEmployee,
    getSubject
} from "../Fetchers.js";
import { getUserID } from "../../routes/user";
import NeuLoader1 from '../snippets/loaders/NeuLoader1.vue';
import NeuLoader4 from '../snippets/loaders/NeuLoader4.vue';

// import EmployeeForm from './EmployeeForm.vue'
import EmployeeAccountTagging from '../snippets/modal/EmployeeAccountTagging.vue';
import Employee from '../snippets/modal/EmployeeModal.vue';
import EmployeeTags from '../snippets/modal/EmployeeTagsModal.vue';
import { useRouter, useRoute } from 'vue-router'

const router = useRouter();
const preLoading = ref(true)
const newEmployeeModal = ref(false)
const updateEmployeeModal = ref(false)
const tagEmployeeModal = ref(false)
const bootingCount = ref(0)
const booting = ref('')
const limit = ref(10)
const offset = ref(0)
const searchValue = ref('')
const searchFname = ref('')
const searchMname = ref('')
const searchLname = ref('')
const employeeCount = ref(0)
const employee = ref([])
const gender = ref([])
const civilstatus = ref([])
const editEmployeeModal = ref(false)
const employeeToUpdate = ref([])
const subject = ref([])
const subjectAll = ref([])
const userID = ref('')
const emit = defineEmits(['fetchUser', 'doneLoading'])
const accessData = ref([])
const accountEmployeeModal = ref(false)
const booter = async () => {

    getSubject().then((results) => {
        subjectAll.value = results
        booting.value = 'Loading All Subjects'
        bootingCount.value += 1
    })

    getGender().then((results) => {
        gender.value = results
        booting.value = 'Loading Genders'
        bootingCount.value += 1
    })

    getCivilStatus().then((results) => {
        civilstatus.value = results
        booting.value = 'Loading Civil Status'
        bootingCount.value += 1
    })
    // getTaggedSubject
    getSubject().then((results) => {
        subject.value = results
        booting.value = 'Loading Subjects...'
        bootingCount.value += 1
    })
    // await router.isReady()
    // getUserID().then((results) => {
    //     userID.value = results.account.data.id
    //     booting.value = 'Loading Users...'
    //     bootingCount.value += 1
    //     emit('fetchUser', results)
    // })
}

const paginate = (mode) => {
    searchFname.value = searchFname.value.trim()
    searchMname.value = searchMname.value.trim()
    searchLname.value = searchLname.value.trim()
    
    switch (mode) {
        case 'prev':
            if (offset.value <= 0) {
                offset.value = 0
            } else {
                employee.value = []
                offset.value -= 10
                employeeCount.value = 0
                preLoading.value = true
                getEmployee(limit.value, offset.value, searchFname.value, searchMname.value, searchLname.value).then((results) => {
                    employee.value = results.data
                    employeeCount.value = results.count
                    preLoading.value = false
                })
            }
            break;
        case 'next':

            if (offset.value >= employeeCount.value) {
                offset.value = employeeCount.value
            } else {
                employee.value = []
                offset.value += 10
                employeeCount.value = 0
                preLoading.value = true
                getEmployee(limit.value, offset.value, searchFname.value, searchMname.value, searchLname.value).then((results) => {
                    employee.value = results.data
                    employeeCount.value = results.count
                    preLoading.value = false
                })
            }
            break;
        case 'search':
            searchValue.value = searchValue.value.trim()
            if (searchValue.value || searchValue.value == '') {
                employee.value = []
                offset.value = 0
                employeeCount.value = 0
                preLoading.value = true
                getEmployee(limit.value, offset.value, searchFname.value, searchMname.value, searchLname.value).then((results) => {
                    employee.value = results.data
                    employeeCount.value = results.count
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

const updateEmployee = (data) => {
    employeeToUpdate.value = data
    updateEmployeeModal.value = !updateEmployeeModal.value
}

const tagEmployee = (data) => {
    employeeToUpdate.value = data
    tagEmployeeModal.value = !tagEmployeeModal.value
}

const AccountEmployee = (data) => {
    employeeToUpdate.value = data
    accountEmployeeModal.value = !accountEmployeeModal.value
}

const removeEmployee = (id) => {
    // let x = {
    //     emp_id: id
    // }
    // if (confirm("Are you sure you want to delete this employee? this cannot be reverted") == true) {
    //     deleteEmployee(x).then(() => {
    //         location.reload()
    //     })
    // } else {
    //     return false;
    // }

    Swal.fire({
        title: "Delete Record",
        text: "Are you sure you want to delete this employee? this cannot be reverted",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, Im Delete it!"
    }).then(async (result) => {
        if (result.isConfirmed) {
            preLoading.value = true
            let x = {
                emp_id: id
            }
            deleteEmployee(x).then(() => {
                location.reload()
            })
        }
    });
}

onMounted(async () => {
    window.stop()
    getUserID().then(async(results) => {
        userID.value = results.account.data.id
        emit('fetchUser', results)
        accessData.value = results.access
        try {
            preLoading.value = true
            booting.value = 'Loading Employees...'
            bootingCount.value += 1
            await booter().then((results1) => {
                getEmployee(limit.value, offset.value, searchFname.value, searchMname.value, searchLname.value).then((results2) => {
                    employee.value = results2.data
                    employeeCount.value = results2.count
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
</script>
<template>
    <div>
        <div class="p-3 mb-4 border-bottom">
            <h5 class=" text-uppercase fw-bold">Employee Management</h5>
        </div>

        <div v-if="preLoading">
            <NeuLoader1/>
        </div>

        <div v-else >
            <div class="p-3 d-flex gap-2 justify-content-between mb-3">
                <!-- <div class="input-group w-50">
                    <span class="input-group-text" id="searchaddon"><font-awesome-icon icon="fa-solid fa-search"
                            /></span>
                    <input type="text" class="form-control" placeholder="Search Here..." aria-label="search"
                        v-model="searchValue" @keyup.enter="search()" aria-describedby="searchaddon"
                        :disabled="preLoading ? true : false">
                </div> -->
                <div class="d-flex gap-2 justify-content-center align-content-center">
                    <input type="text" v-model="searchFname" @keyup.enter="search()"
                        class="neu-input" :disabled="preLoading?true:false" placeholder="First Name"/>
                    <input type="text" v-model="searchMname" @keyup.enter="search()"
                        class="neu-input" :disabled="preLoading?true:false" placeholder="Middle Name"/>
                    <input type="text" v-model="searchLname" @keyup.enter="search()"
                        class="neu-input" :disabled="preLoading?true:false" placeholder="Last Name"/>
                    <button @click="search()" type="button" class="neu-btn neu-blue" tabindex="-1" :disabled="preLoading?true:false">
                        <font-awesome-icon icon="fa-solid fa-magnifying-glass"/> Search
                    </button>
                </div>
                <div class="d-flex flex-wrap justify-content-end">
                    <button tabindex="-1" data-bs-toggle="modal" data-bs-target="#addemployeemodal"
                        @click="newEmployeeModal = true, employeeToUpdate = []" type="button" class="neu-btn neu-green"
                        :disabled="preLoading ? true : false">
                        <font-awesome-icon icon="fa-solid fa-add"  /> Add New
                    </button>
                </div>
            </div>
            <div class="table-responsive border p-3 small-font">
                <table class="neu-table mb-3" style="text-transform:uppercase">
                    <thead>
                        <tr>
                            <th style="color:#555555">#</th>
                            <th style="color:#555555">First Name</th>
                            <th style="color:#555555">Middle Name</th>
                            <th style="color:#555555">Last Name</th>
                            <th style="color:#555555">Suffix Name</th>
                            <th style="color:#555555">Status</th>
                            <th style="color:#555555" class="text-center">Commands</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="!preLoading && Object.keys(employee).length" v-for="(emp, index) in employee">
                            <td class="align-middle">
                                {{ emp.emp_id }}
                            </td>
                            <td class="align-middle">
                                {{ emp.emp_firstname }}
                            </td>
                            <td class="align-middle">
                                {{ emp.emp_middlename ? emp.emp_middlename : 'N/A' }}
                            </td>
                            <td class="align-middle">
                                {{ emp.emp_lastname }}
                            </td>
                            <td class="align-middle">
                                {{ emp.emp_suffixname ? emp.emp_suffixname : 'N/A' }}
                            </td>
                            <td class="align-middle">
                                {{ emp.emp_status == 1 ? 'Acitve' : 'Inactive' }}
                            </td>
                            <td v-if="accessData[4].useracc_modifying == 1" class="align-middle">
                                <div class="d-flex gap-2 justify-content-center">
                                    <button data-bs-toggle="modal" data-bs-target="#editemployeemodal"
                                        @click="updateEmployee(emp)" type="button" title="Edit Record"
                                        class="neu-btn-sm neu-white">
                                        <font-awesome-icon icon="fa-solid fa-pen"/></button>
                                    <button @click="removeEmployee(emp.emp_id)" type="button" title="Set as Inactive"
                                        class="neu-btn-sm neu-white"> <font-awesome-icon icon="fa-solid fa-trash"
                                        /></button>
                                    <button data-bs-toggle="modal" data-bs-target="#assignemployeemodal"
                                        @click="tagEmployee(emp)" type="button" title="Tag Subjects"
                                        class="neu-btn-sm neu-white"> <font-awesome-icon icon="fa-solid fa-tag"
                                        /></button>
                                    <button data-bs-toggle="modal" data-bs-target="#accountemployeemodal"
                                        @click="AccountEmployee(emp)" type="button" title="Tag Account"
                                        class="neu-btn-sm neu-white"> <font-awesome-icon icon="fa-solid fa-user-plus"
                                        /></button>    
                                </div>
                            </td>
                            <td v-else class="align-middle">
                                N/A
                            </td>
                        </tr>
                        <tr v-if="!preLoading && !Object.keys(employee).length" style="text-transform:none">
                            <td class="p-3 text-center" colspan="7">
                                <NeuLoader4/>
                                <p class="fw-bold m-0">Nothing here yet!</p>
                                <p>The hamster took a break 💤 — try adding something new.</p>
                            </td>
                        </tr>
                        <!-- <tr v-if="preLoading && !Object.keys(employee).length" style="text-transform:none">
                            <td class="p-3 text-center" colspan="7">
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
                        <button :disabled="Object.keys(employee).length < 10 ? true : false" @click="paginate('next')"
                            class="neu-btn neu-dark-gray">Next</button>
                    </div>
                    <p class="">showing total of <span class="font-semibold">({{ employeeCount }})</span> items</p>
                </div>
            </div>    
        </div>
    </div>

    <!-- Add New Modal -->
    <div class="modal fade" id="addemployeemodal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Employee</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        @click="newEmployeeModal = false"></button>
                </div>
                <div class="modal-body neu-bg">
                    <Employee v-if="newEmployeeModal" :useriddata="userID" :genderData="gender"
                        :civilstatusData="civilstatus" />
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <div class="form-group">
                        <small id="emailHelp" class="form-text text-muted">We'll never share your personal information
                            with anyone
                            else (Data Privacy Act of 2012)</small>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                            @click="newEmployeeModal = false">Close</button>
                        <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Update Modal -->
    <div class="modal fade" id="editemployeemodal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Employee</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        @click="updateEmployeeModal = false"></button>
                </div>
                <div class="modal-body neu-bg">
                    <Employee v-if="updateEmployeeModal" :useriddata="userID" :genderData="gender"
                        :civilstatusData="civilstatus" :employeeData="employeeToUpdate" :toUpdate="true" />
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <div class="form-group">
                        <small id="emailHelp" class="form-text text-muted">We'll never share your personal information
                            with anyone
                            else (Data Privacy Act of 2012)</small>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                            @click="updateEmployeeModal = false">Close</button>
                        <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tag Modal -->
    <div class="modal fade" id="assignemployeemodal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Tag Subjects</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        @click="tagEmployeeModal = false"></button>
                </div>
                <div class="modal-body neu-bg">
                    <EmployeeTags v-if="tagEmployeeModal" :subjectData="subject" :employeeData="employeeToUpdate"/>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <div class="form-group">
                        <small id="emailHelp" class="form-text text-muted">We'll never share your personal information
                            with anyone
                            else (Data Privacy Act of 2012)</small>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                            @click="tagEmployeeModal = false">Close</button>
                        <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
                    </div>
                </div>
            </div>
        </div>
    </div>

     <!-- Tag Modal -->
     <div class="modal fade" id="accountemployeemodal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Tag Account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        @click="accountEmployeeModal = false"></button>
                </div>
                <div class="modal-body neu-bg">
                    <EmployeeAccountTagging v-if="accountEmployeeModal" :employeeData="employeeToUpdate" :userId="userID"/>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <div class="form-group">
                        <small id="emailHelp" class="form-text text-muted">We'll never share your personal information
                            with anyone
                            else (Data Privacy Act of 2012)</small>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                            @click="accountEmployeeModal = false">Close</button>
                        <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
                    </div>
                </div>
            </div>
        </div>
    </div>


</template>