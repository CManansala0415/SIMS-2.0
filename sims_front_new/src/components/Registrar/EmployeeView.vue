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
import Loader from '../snippets/loaders/Loading1.vue';
// import EmployeeForm from './EmployeeForm.vue'
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

const employeeCount = ref(0)
const employee = ref([])
const gender = ref([])
const civilstatus = ref([])
const editEmployeeModal = ref(false)
const employeeToUpdate = ref([])
const subject = ref([])
const subjectAll = ref([])
const userID = ref('')
const emit = defineEmits(['fetchUser'])
const accessData = ref([])
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
    switch (mode) {
        case 'prev':
            if (offset.value <= 0) {
                offset.value = 0
            } else {
                employee.value = []
                offset.value -= 10
                employeeCount.value = 0
                preLoading.value = true
                getEmployee(limit.value, offset.value).then((results) => {
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
                getEmployee(limit.value, offset.value, null).then((results) => {
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
                getEmployee(limit.value, offset.value, searchValue.value).then((results) => {
                    employee.value = results.data
                    employeeCount.value = results.count
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

const updateEmployee = (data) => {
    employeeToUpdate.value = data
    updateEmployeeModal.value = !updateEmployeeModal.value
}

const tagEmployee = (data) => {
    employeeToUpdate.value = data
    tagEmployeeModal.value = !tagEmployeeModal.value
}

const removeEmployee = (id) => {
    let x = {
        emp_id: id
    }
    if (confirm("Are you sure you want to delete this employee? this cannot be reverted") == true) {
        deleteEmployee(x).then(() => {
            location.reload()
        })
    } else {
        return false;
    }
}

onMounted(async () => {
    window.stop()
    getUserID().then(async(results) => {
        userID.value = results.account.data.id
        emit('fetchUser', results)
        accessData.value = results.access.data
        try {
            preLoading.value = true
            booting.value = 'Loading Employees...'
            bootingCount.value += 1
            await booter().then((results1) => {
                getEmployee(limit.value, offset.value).then((results2) => {
                    employee.value = results2.data
                    employeeCount.value = results2.count
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
</script>
<template>
    <div>
        <div class="p-3 mb-4 border-bottom">
            <h5 class=" text-uppercase fw-bold">Employee Management</h5>
        </div>

        <div class="p-1 d-flex gap-2 justify-content-between mb-3">
            <div class="input-group w-50">
                <span class="input-group-text" id="searchaddon"><font-awesome-icon icon="fa-solid fa-search"
                         /></span>
                <input type="text" class="form-control" placeholder="Search Here..." aria-label="search"
                    v-model="searchValue" @keyup.enter="search()" aria-describedby="searchaddon"
                    :disabled="preLoading ? true : false">
            </div>
            <div class="d-flex flex-wrap w-50 justify-content-end">
                <button tabindex="-1" data-bs-toggle="modal" data-bs-target="#addemployeemodal"
                    @click="newEmployeeModal = true, employeeToUpdate = []" type="button" class="btn btn-sm btn-primary"
                    :disabled="preLoading ? true : false">
                    <font-awesome-icon icon="fa-solid fa-add"  /> Add New
                </button>
            </div>
        </div>
        <div class="table-responsive border p-3 small-font">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th style="background-color: #237a5b;" class="text-white">#</th>
                        <th style="background-color: #237a5b;" class="text-white">First Name</th>
                        <th style="background-color: #237a5b;" class="text-white">Middle Name</th>
                        <th style="background-color: #237a5b;" class="text-white">Last Name</th>
                        <th style="background-color: #237a5b;" class="text-white">Suffix Name</th>
                        <th style="background-color: #237a5b;" class="text-white">Status</th>
                        <th style="background-color: #237a5b;" class="text-white">Commands</th>
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
                                    class="btn btn-secondary btn-sm">
                                    <font-awesome-icon icon="fa-solid fa-pen"/></button>
                                <button @click="removeEmployee(emp.emp_id)" type="button" title="Set as Inactive"
                                    class="btn btn-secondary btn-sm"> <font-awesome-icon icon="fa-solid fa-trash"
                                    /></button>
                                <button data-bs-toggle="modal" data-bs-target="#assignemployeemodal"
                                    @click="tagEmployee(emp)" type="button" title="Tag Subjects"
                                    class="btn btn-secondary btn-sm"> <font-awesome-icon icon="fa-solid fa-tag"
                                    /></button>
                            </div>
                        </td>
                        <td v-else class="align-middle">
                            N/A
                        </td>
                    </tr>
                    <tr v-if="!preLoading && !Object.keys(employee).length">
                        <td class="p-3 text-center" colspan="7">
                            No Records Found
                        </td>
                    </tr>
                    <tr v-if="preLoading && !Object.keys(employee).length">
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
                    <button :disabled="Object.keys(employee).length < 10 ? true : false" @click="paginate('next')"
                        class="btn btn-sm btn-secondary">Next</button>
                </div>
                <p class="">showing total of <span class="font-semibold">({{ employeeCount }})</span> items</p>
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
                <div class="modal-body">
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
                <div class="modal-body">
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
                <div class="modal-body">
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


</template>