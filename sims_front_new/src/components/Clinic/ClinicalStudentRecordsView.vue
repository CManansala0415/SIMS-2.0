<script setup>
import { ref, onMounted, computed } from 'vue';
import Loader from '../snippets/loaders/Loading1.vue';
// import ClinicalStudentRecordsForm from './ClinicalStudentRecordsForm.vue';
// import MedicalDispense from '../snippets/modal/MedicalDispense.vue';
// import MedicalIshihara from '../snippets/modal/MedicalIshihara.vue';
// import MedicalHearing from '../snippets/modal/MedicalHearing.vue';
// import MedicalFile from '../snippets/modal/MedicalFile.vue';

import { getUserID } from "../../routes/user";
import { useRouter, useRoute } from 'vue-router';
import {
    getApplicant,
    getGender,
    getNationality,
    getCivilStatus,
    getGradelvl,
    getProgram,
    getQuarter,
    getDegree,
    getProgramList,
    getSemester,
    getSection,
    getRegion,
    getProvince,
    getCity,
    getBarangay,
    getMedicalSupplies
} from "../Fetchers.js";
import ClinicMedicalForm from '../snippets/modal/ClinicStudentMedicalModal.vue';
import ClinicDispenseModal from '../snippets/modal/ClinicDispenseModal.vue';
import ClinicVisionModal from '../snippets/modal/ClinicVisionModal.vue';
import ClinicHearingModal from '../snippets/modal/ClinicHearingModal.vue';
import ClinicMedicalArchive from '../snippets/modal/ClinicMedicalArchive.vue';

const limit = ref(10)
const offset = ref(0)
const applicant = ref([])
const applicantCount = ref(0)
const preLoading = ref(false)
const searchValue = ref('')
const userID = ref('')
const router = useRouter();
const showForm = ref(false)
const showDispense = ref(false)
const showIshihara = ref(false)
const showHearing = ref(false)
const showFile = ref(false)

const gender = ref([])
const nationality = ref([])
const region = ref([])
const city = ref([])
const province = ref([])
const barangay = ref([])
const civilstatus = ref([])
const quarter = ref([])
const gradelvl = ref([])
const degree = ref([])
const course = ref([])
const semester = ref([])
const section = ref([])
const medicalSupplies = ref([])
const editId = ref('')
const booting = ref('')
const bootingCount = ref(0)
const emit = defineEmits(['fetchUser'])

const booter = async () => {

    getGender().then((results) => {
        gender.value = results
        booting.value = 'Loading Genders'
        bootingCount.value += 1
    })

    getNationality().then((results) => {
        nationality.value = results
        booting.value = 'Loading Nationalities'
        bootingCount.value += 1
    })

    getCivilStatus().then((results) => {
        civilstatus.value = results
        booting.value = 'Loading Civil Status'
        bootingCount.value += 1
    })

    getGradelvl().then((results) => {
        gradelvl.value = results
        booting.value = 'Loading Grade Levels'
        bootingCount.value += 1
    })

    getProgram().then((results) => {
        degree.value = results
        booting.value = 'Loading Degrees'
        bootingCount.value += 1
    })

    getQuarter().then((results) => {
        quarter.value = results
        booting.value = 'Loading Quarters'
        bootingCount.value += 1
    })

    getDegree().then((results) => {
        course.value = results
        booting.value = 'Loading Courses'
        bootingCount.value += 1
    })

    getProgramList().then((results) => {
        course.value = results
        booting.value = 'Loading Courses'
        bootingCount.value += 1
    })

    getSemester().then((results) => {
        semester.value = results
        booting.value = 'Loading Semesters'
        bootingCount.value += 1
    })

    getSection().then((results) => {
        section.value = results
        booting.value = 'Loading Sections'
        bootingCount.value += 1
    })

    getRegion().then((results) => {
        region.value = results
        booting.value = 'Loading Regions'
        bootingCount.value += 1
    })

    getProvince().then((results) => {
        province.value = results
        booting.value = 'Loading Provinces'
        bootingCount.value += 1
    })

    getCity().then((results) => {
        city.value = results
        booting.value = 'Loading Cities'
        bootingCount.value += 1
    })

    getBarangay().then((results) => {
        barangay.value = results
        booting.value = 'Loading Barangays'
        bootingCount.value += 1
    })

    getMedicalSupplies(0, 0).then((results) => {
        medicalSupplies.value = results.data
        booting.value = 'Loading Medical Supplies'
        bootingCount.value += 1
    })

}

onMounted(async () => {
    getUserID().then((results) => {
        userID.value = results.account.data.id
        emit('fetchUser', results)
    })

    try {
        preLoading.value = true
        await booter().then((results) => {
            booting.value = 'Loading Applicants...'
            bootingCount.value += 1
            getApplicant(limit.value, offset.value).then((results) => {
                applicant.value = results.data
                applicantCount.value = results.count
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
                applicant.value = []
                offset.value -= 10
                applicantCount.value = 0
                preLoading.value = true
                getApplicant(limit.value, offset.value).then((results) => {
                    applicant.value = results.data
                    applicantCount.value = results.count
                    preLoading.value = false
                })
            }
            break;
        case 'next':

            if (offset.value >= applicantCount.value) {
                offset.value = applicantCount.value
            } else {
                applicant.value = []
                offset.value += 10
                applicantCount.value = 0
                preLoading.value = true
                getApplicant(limit.value, offset.value, null).then((results) => {
                    applicant.value = results.data
                    applicantCount.value = results.count
                    preLoading.value = false
                })
            }
            break;
        case 'search':
            searchValue.value = searchValue.value.trim()
            if (searchValue.value || searchValue.value == '') {
                applicant.value = []
                offset.value = 0
                applicantCount.value = 0
                preLoading.value = true
                getApplicant(limit.value, offset.value, searchValue.value).then((results) => {
                    applicant.value = results.data
                    applicantCount.value = results.count
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

const goTo = () => {
    router.push('/application')
}

const editData = (id) => {
    editId.value = id
    showForm.value = !showForm.value
}
const dispenseData = (id) => {
    editId.value = id
    showDispense.value = !showDispense.value
}

const ishiharaData = (id) => {
    editId.value = id
    showIshihara.value = !showIshihara.value
}

const hearingData = (id) => {
    editId.value = id
    showHearing.value = !showHearing.value
}

const fileUpload = (id) => {
    editId.value = id
    showFile.value = !showFile.value
}

</script>
<template>
    <div>
        <div class="p-3 mb-4 border-bottom">
            <h5 class=" text-uppercase fw-bold">Clinic Student Records</h5>
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
                        <th style="background-color: #237a5b;" class="text-white">First Name</th>
                        <th style="background-color: #237a5b;" class="text-white">Middle Name</th>
                        <th style="background-color: #237a5b;" class="text-white">Last Name</th>
                        <th style="background-color: #237a5b;" class="text-white">Suffix Name</th>
                        <th style="background-color: #237a5b;" class="text-white">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="!preLoading && Object.keys(applicant).length" v-for="(app, index) in applicant">
                        <td class="align-middle">
                            {{ app.per_firstname }}
                        </td>
                        <td class="align-middle">
                            {{ app.per_middlename ? app.per_middlename : 'N/A' }}
                        </td>
                        <td class="align-middle">
                            {{ app.per_lastname }}
                        </td>
                        <td class="align-middle">
                            {{ app.per_suffixname ? app.per_suffixname : 'N/A' }}
                        </td>
                        <td class="align-middle">
                            <div class="d-flex gap-2 justify-content-center">
                                <button class="btn btn-sm btn-secondary" data-bs-toggle="modal"
                                    data-bs-target="#editdatamodal" title="Medical Form / Checkup"
                                    @click="editData(app.per_id)">
                                    <font-awesome-icon icon="fa-solid fa-pen" />
                                </button>
                                <button class="btn btn-sm btn-secondary" data-bs-toggle="modal"
                                    data-bs-target="#dispensemodal" title="Dispense Medical Item"
                                    @click="dispenseData(app.per_id)">
                                    <font-awesome-icon icon="fa-solid fa-pills" />
                                </button>
                                <button class="btn btn-sm btn-secondary" data-bs-toggle="modal"
                                    data-bs-target="#visionmodal" title="Conduct Vision Examination"
                                    @click="ishiharaData(app.per_id)">
                                    <font-awesome-icon icon="fa-solid fa-eye" />
                                </button>
                                <button class="btn btn-sm btn-secondary" data-bs-toggle="modal"
                                    data-bs-target="#hearingmodal" title="Conduct Hearing Examination"
                                    @click="hearingData(app.per_id)">
                                    <font-awesome-icon icon="fa-solid fa-ear-listen" />
                                </button>
                                <button class="btn btn-sm btn-secondary" data-bs-toggle="modal"
                                    data-bs-target="#archivemodal" title="Upload Files" @click="fileUpload(app.per_id)">
                                    <font-awesome-icon icon="fa-solid fa-folder" />
                                </button>
                            </div>
                        </td>

                    </tr>
                    <tr v-if="!preLoading && !Object.keys(applicant).length">
                        <td class="p-3 text-center" colspan="7">
                            No Records Found
                        </td>
                    </tr>
                    <tr v-if="preLoading && !Object.keys(applicant).length">
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
                    <button :disabled="Object.keys(applicant).length < 10 ? true : false" @click="paginate('next')"
                        class="btn btn-sm btn-secondary">Next</button>
                </div>
                <p class="">showing total of <span class="font-semibold">({{ applicantCount }})</span> items</p>
            </div>
        </div>
    </div>

    <!-- Edit Medical Modal -->
    <div class="modal fade" id="editdatamodal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Medical Records</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        @click="showForm = false"></button>
                </div>
                <div class="modal-body">
                    <ClinicMedicalForm v-if="showForm" :genderData="gender" :civilstatusData="civilstatus"
                        :nationalityData="nationality" :regionData="region" :provinceData="province" :cityData="city"
                        :barangayData="barangay" :formId="editId" />
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <div class="form-group">
                        <small id="emailHelp" class="form-text text-muted">We'll never share your personal information
                            with anyone
                            else (Data Privacy Act of 2012)</small>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                            @click="showForm = false">Close</button>
                        <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Dispense Medical Modal -->
    <div class="modal fade" id="dispensemodal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Dispense Medical Items</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        @click="showDispense = false"></button>
                </div>
                <div class="modal-body">
                    <ClinicDispenseModal v-if="showDispense" :personIdData="editId"
                        :medicicalItemsData="medicalSupplies" :userIdData="userID" :modeData="1" />
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <div class="form-group">
                        <small id="emailHelp" class="form-text text-muted">We'll never share your personal information
                            with anyone
                            else (Data Privacy Act of 2012)</small>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                            @click="showDispense = false">Close</button>
                        <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Vision Examination Modal -->
    <div class="modal fade" id="visionmodal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Visual Examination</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        @click="showIshihara = false"></button>
                </div>
                <div class="modal-body">
                    <ClinicVisionModal v-if="showIshihara" :personIdData="editId" :medicicalItemsData="medicalSupplies"
                        :userIdData="userID" />
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <div class="form-group">
                        <small id="emailHelp" class="form-text text-muted">We'll never share your personal information
                            with anyone
                            else (Data Privacy Act of 2012)</small>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                            @click="showIshihara = false">Close</button>
                        <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Vision Examination Modal -->
    <div class="modal fade" id="hearingmodal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Hearing Examination</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        @click="showHearing = false"></button>
                </div>
                <div class="modal-body">
                    <ClinicHearingModal v-if="showHearing" :personIdData="editId" :medicicalItemsData="medicalSupplies"
                        :userIdData="userID" />
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <div class="form-group">
                        <small id="emailHelp" class="form-text text-muted">We'll never share your personal information
                            with anyone
                            else (Data Privacy Act of 2012)</small>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                            @click="showHearing = false">Close</button>
                        <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Medical Archive Modal -->
    <div class="modal fade" id="archivemodal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Medical Results Archive</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        @click="showFile = false"></button>
                </div>
                <div class="modal-body">
                    <ClinicMedicalArchive v-if="showFile" :personIdData="editId" :medicicalItemsData="medicalSupplies"
                        :userIdData="userID" />
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <div class="form-group">
                        <small id="emailHelp" class="form-text text-muted">We'll never share your personal information
                            with anyone
                            else (Data Privacy Act of 2012)</small>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                            @click="showFile = false">Close</button>
                        <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
                    </div>
                </div>
            </div>
        </div>
    </div>

</template>