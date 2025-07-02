<script setup>
import { ref, onMounted, computed } from 'vue';

// import SearchHeader from '../snippets/headers/SearchHeader.vue';
// import MiniGreenBtn from '../snippets/buttons/MiniGreenBtn.vue';
// import MiniRedBtn from '../snippets/buttons/MiniRedBtn.vue';
// import MiniYellowBtn from '../snippets/buttons/MiniYellowBtn.vue';
// import MiniTealBtn from '../snippets/buttons/MiniTealBtn.vue';
// import RedBtn from '../snippets/buttons/RedBtn.vue';
import Loader from '../snippets/loaders/Loading1.vue';
// import Enroll from '../snippets/modal/Enrollment.vue';
import ApplicationModal from '../snippets/modal/ApplicationModal.vue';
import EnrollmentModal from '../snippets/modal/EnrollmentModal.vue';
import StudentIdModal from '../snippets/modal/StudentIdModal.vue';
// import ApplicationModal from '../snippets/modal/ApplicationForm.vue';
// import StudentIdentification from '../snippets/modal/StudentIdentification.vue';

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
    getDemograph,
    getAcademicDefaults,
    getCountry,
    getRegion,
    getProvince,
    getCity,
    getBarangay,
    updateApplicant,
    getAcademicStatus
} from "../Fetchers.js";
import ApplicationPrintIdModal from '../snippets/modal/ApplicationPrintIdModal.vue';
import SearchQR from '../snippets/tech/SearchQR.vue';
import ApplicationMilestoneModal from '../snippets/modal/ApplicationMilestoneModal.vue';

const limit = ref(10)
const offset = ref(0)
const applicant = ref([])
const applicantCount = ref(0)
const preLoading = ref(true)
const searchValue = ref('')
const searchFname = ref('')
const searchMname = ref('')
const searchLname = ref('')
const userID = ref('')
const router = useRouter();
const showForm = ref(false)
const showFormModal = ref(false)
const showEnroll = ref(false)
const showIdentification = ref(false)
const showPrintID = ref(false)
const showQRScanner = ref(false)
const activeEnrollment = ref(false)
const showMilestones = ref(false)

const gender = ref([])
const nationality = ref([])
const country = ref([])
const region = ref([])
const city = ref([])
const province = ref([])
const barangay = ref([])
const civilstatus = ref([])
const quarter = ref([])
const gradelvl = ref([])
const degree = ref([])
const course = ref([])
const dtype = ref([])
const semester = ref([])
const section = ref([])
const editId = ref('')
const fullName = ref('')
const booting = ref('')
const bootingCount = ref(0)
const accessData = ref([])
const emit = defineEmits(['fetchUser'])


const booter = async () => {

    // getGender().then((results) => {
    //     gender.value = results
    //     booting.value = 'Loading Genders'
    //     bootingCount.value += 1
    // })

    // getNationality().then((results) => {
    //     nationality.value = results
    //     booting.value = 'Loading Nationalities'
    //     bootingCount.value += 1
    // })

    // getCivilStatus().then((results) => {
    //     civilstatus.value = results
    //     booting.value = 'Loading Civil Status'
    //     bootingCount.value += 1
    // })

    // getGradelvl().then((results) => {
    //     gradelvl.value = results
    //     booting.value = 'Loading Grade Levels'
    //     bootingCount.value += 1
    // })

    // getProgram().then((results) => {
    //     degree.value = results
    //     booting.value = 'Loading Degrees'
    //     bootingCount.value += 1
    // })

    // getQuarter().then((results) => {
    //     quarter.value = results
    //     booting.value = 'Loading Quarters'
    //     bootingCount.value += 1
    // })

    getDegree().then((results) => {
        dtype.value = results
        booting.value = 'Loading Degree Types'
        bootingCount.value += 1
    })

    // getProgramList().then((results) => {
    //     course.value = results
    //     booting.value = 'Loading Courses'
    //     bootingCount.value += 1
    // })

    // getSemester().then((results) => {
    //     semester.value = results
    //     booting.value = 'Loading Semesters'
    //     bootingCount.value += 1
    // })

    // getSection().then((results) => {
    //     section.value = results
    //     booting.value = 'Loading Sections'
    //     bootingCount.value += 1
    // })

    getDemograph().then((results) => {
        country.value = results.country
        region.value = results.region
        province.value = results.province
        city.value = results.city
        barangay.value = results.barangay
        gender.value = results.gender
        nationality.value = results.nationality
        civilstatus.value = results.civilstatus
        booting.value = 'Loading Demographic Information'
        bootingCount.value += 1
    })
    getAcademicDefaults().then((results) => {
        gradelvl.value = results.gradelvl
        degree.value = results.program
        quarter.value = results.quarter
        course.value = results.course
        semester.value = results.semester
        section.value = results.section
        booting.value = 'Loading Academic Information'
        bootingCount.value += 1
    })

    getAcademicStatus(1,'cs_05').then((results) => {
        results[0].sett_status == 1? activeEnrollment.value = true: activeEnrollment.value = false
    })
    // getCountry().then((results) => {
    //     country.value = results
    //     booting.value = 'Loading Countries'
    //     bootingCount.value += 1
    // })

    // getRegion().then((results) => {
    //     region.value = results
    //     booting.value = 'Loading Regions'
    //     bootingCount.value += 1
    // })

    // getProvince().then((results) => {
    //     province.value = results
    //     booting.value = 'Loading Provinces'
    //     bootingCount.value += 1
    // })

    // getCity().then((results) => {
    //     city.value = results
    //     booting.value = 'Loading Cities'
    //     bootingCount.value += 1
    // })

    // getBarangay().then((results) => {
    //     barangay.value = results
    //     booting.value = 'Loading Barangays'
    //     bootingCount.value += 1
    // })
    // getUserID().then((results) => {
    //     userID.value = results.account.data.id
    //     booting.value = 'Loading Users'
    //     bootingCount.value += 1
    //     emit('fetchUser', results)
    // })
}

onMounted(async () => {
    window.stop()
    getUserID().then(async(results) => {
        userID.value = results.account.data.id
        accessData.value = results.access
        // console.log(accessData.value)
        emit('fetchUser', results)
        try {
            await booter().then((results) => {
                booting.value = 'Loading Applicants...'
                bootingCount.value += 1
            
                getApplicant(limit.value, offset.value, searchFname.value, searchMname.value, searchLname.value,1).then((results) => {
                    applicant.value = results.data
                    applicantCount.value = results.count
                    
                    preLoading.value = false
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
const deletePerson = (id) => {
    // if (confirm("Are you sure you want to delete this registry? this action cannot be reverted.") == true) {
    //     let pers = {
    //         per_updatedby: userID.value,
    //         per_id: id
    //     }
    //     updateApplicant(pers, 2).then((results) => {
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
            let pers = {
                per_updatedby: userID.value,
                per_id: id
            }
            updateApplicant(pers, 2).then((results) => {
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
    searchFname.value = searchFname.value.trim()
    searchMname.value = searchMname.value.trim()
    searchLname.value = searchLname.value.trim()
    
    switch (mode) {
        case 'prev':
            if (offset.value <= 0) {
                offset.value = 0
            } else {
                applicant.value = []
                offset.value -= 10
                applicantCount.value = 0
                preLoading.value = true
                getApplicant(limit.value, offset.value, searchFname.value, searchMname.value, searchLname.value,2).then((results) => {
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
                getApplicant(limit.value, offset.value, searchFname.value, searchMname.value, searchLname.value,2).then((results) => {
                    applicant.value = results.data
                    applicantCount.value = results.count
                    preLoading.value = false
                })
            }
            break;
        case 'search':
            if (searchValue.value || searchValue.value == '') {
                applicant.value = []
                offset.value = 0
                applicantCount.value = 0
                preLoading.value = true
                getApplicant(limit.value, offset.value, searchFname.value, searchMname.value, searchLname.value,2).then((results) => {
                    applicant.value = results.data
                    applicantCount.value = results.count
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

const goTo = () => {
    router.push('/application')
}

const formMode = ref('')
const editData = (id) => {
    editId.value = id
    formMode.value = id
    showFormModal.value = !showFormModal.value
}
const enrollApplicant = (data) => {
    getAcademicStatus(1,'cs_05').then((results) => {
        if(results[0].sett_status == 1){
            let middle = data.per_middlename ? data.per_middlename : ''
            let suffix = data.per_suffixname ? data.per_suffixname : ''

            editId.value = data.per_id
            fullName.value = data.per_firstname + ' ' + middle + ' ' + data.per_lastname + ' ' + suffix
            showEnroll.value = !showEnroll.value
        }else{
             Swal.fire({
                icon: "error",
                title: "Oops...",
                text: "Something went wrong!",
                footer: '<a href="#" disabled>Registrar Settings Detected Changes, Reloading the page?</a>'
            }).then(()=>{
                location.reload()
            });
        }
    })
    
}

const identificationData = ref([])
const addID = (data) => {
    identificationData.value = data
    showIdentification.value = !showIdentification.value
}

const viewMilestones = (data) => {
    identificationData.value = data
    showMilestones.value = !showMilestones.value
}

const printID = (data) => {
    identificationData.value = data
    showPrintID.value = !showPrintID.value
}

const getData = (result) =>{
    console.log(result)
    applicant.value = result
    showQRScanner.value = !showQRScanner
    document.getElementById('hideqrscanner').click();
}

</script>
<template>
    <div>
        <div class="p-3 mb-4 border-bottom">
            <h5 class=" text-uppercase fw-bold">Student Admission</h5>
        </div>

        <div class="p-1 d-flex gap-2 justify-content-between mb-3">
            <!-- <div class="input-group w-50">
                <span class="input-group-text" id="searchaddon"><font-awesome-icon icon="fa-solid fa-search"
                         /></span>
                <input type="text" class="form-control" placeholder="Search Here..." aria-label="search"
                    v-if="!showForm" v-model="searchValue" @keyup.enter="search()" aria-describedby="searchaddon" :disabled="preLoading? true:false">
            </div> -->
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
                <button tabindex="-1" data-bs-toggle="modal" data-bs-target="#editdatamodal" @click="editData('new')"
                    type="button" class="btn btn-sm btn-primary" :disabled="preLoading? true:false">
                    <font-awesome-icon icon="fa-solid fa-add"  /> New Record
                </button>
                <button tabindex="-1" data-bs-toggle="modal" data-bs-target="#editdatamodal" @click="editData('old')"
                    type="button" class="btn btn-sm btn-info" :disabled="preLoading? true:false">
                    <font-awesome-icon icon="fa-solid fa-add"  /> Old Record
                </button>
            </div>
        </div>
        <div class="table-responsive border p-3 small-font">
            <table class="table table-hover" style="text-transform:uppercase">
                <thead>
                    <tr>
                        <th style="background-color: #237a5b;" class="text-white">#</th>
                        <th style="background-color: #237a5b;" class="text-white">First Name</th>
                        <th style="background-color: #237a5b;" class="text-white">Middle Name</th>
                        <th style="background-color: #237a5b;" class="text-white">Last Name</th>
                        <th style="background-color: #237a5b;" class="text-white">Suffix Name</th>
                        <th style="background-color: #237a5b;" class="text-white">Date Applied</th>
                        <th style="background-color: #237a5b;" class="text-white">Commands</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="!preLoading && Object.keys(applicant).length" v-for="(app, index) in applicant">
                        <td class="align-middle">
                            {{ app.per_id }}
                        </td>
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
                            {{ app.per_dateapplied }}
                        </td>
                        <td v-if="accessData[0].useracc_modifying == 1" class="align-middle">
                            <div class="d-flex gap-2 justify-content-center">
                                <button data-bs-toggle="modal" data-bs-target="#editdatamodal" @click="editData(app.per_id)"
                                    type="button" title="Edit Record" class="btn btn-secondary btn-sm">
                                    <font-awesome-icon icon="fa-solid fa-pen"/></button>
                                <button @click="deletePerson(app.per_id)" type="button" title="Delete Record"
                                    class="btn btn-secondary btn-sm"> <font-awesome-icon icon="fa-solid fa-trash"
                                    /></button>

                                <button data-bs-toggle="modal" data-bs-target="#enrollmentmodal" v-if="activeEnrollment"
                                    @click="enrollApplicant(app)" type="button" title="Enroll Applicant"
                                    class="btn btn-secondary btn-sm"> <font-awesome-icon icon="fa-solid fa-gear"
                                    /></button>
                                <button data-bs-toggle="modal" data-bs-target="#identificationmodal" @click="addID(app)"
                                    type="button" title="Assign Identification" class="btn btn-secondary btn-sm">
                                    <font-awesome-icon icon="fa-solid fa-id-card-clip"/></button>
                                <button data-bs-toggle="modal" data-bs-target="#milestonemodal" @click="viewMilestones(app)"
                                    type="button" title="Assign Identification" class="btn btn-secondary btn-sm">
                                    <font-awesome-icon icon="fa-solid fa-tag"/></button>
                                <!-- <button data-bs-toggle="modal" data-bs-target="#printidmodal" @click="printID(app)"
                                    type="button" title="print ID" class="btn btn-secondary btn-sm">
                                    <font-awesome-icon icon="fa-solid fa-id-card-clip"/></button> -->
                            </div>
                        </td> 
                        <td v-else class="align-middle">
                            N/A
                        </td>
                    </tr>
                    <tr v-if="!preLoading && !Object.keys(applicant).length" style="text-transform:none">
                        <td class="p-3 text-center" colspan="7">
                            No Records Found
                        </td>
                    </tr>
                    <tr v-if="preLoading && !Object.keys(applicant).length" style="text-transform:none">
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


    <!-- Application Modal -->
    <div class="modal fade" id="editdatamodal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Application</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        @click="showFormModal = false"></button>
                </div>
                <div class="modal-body">
                    <ApplicationModal v-if="showFormModal" :genderData="gender" :civilstatusData="civilstatus"
                        :nationalityData="nationality" :regionData="region" :provinceData="province" :cityData="city"
                        :barangayData="barangay" :formId="editId" :formMode="formMode" :countryData="country"/>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <div class="form-group">
                        <small id="emailHelp" class="form-text text-muted">We'll never share your personal information
                            with anyone
                            else (Data Privacy Act of 2012)</small>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                            @click="showFormModal = false">Close</button>
                        <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Enrollment Modal -->
    <div class="modal fade" id="enrollmentmodal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Enroll Applicant</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        @click="showEnroll = false"></button>
                </div>
                <div class="modal-body">
                    <EnrollmentModal v-if="showEnroll" :personid="editId" :personname="fullName"
                        :gradelvldata="gradelvl" :programdata="degree" :quarterdata="quarter" :coursedata="course" />
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <div class="form-group">
                        <small id="emailHelp" class="form-text text-muted">We'll never share your personal information
                            with anyone
                            else (Data Privacy Act of 2012)</small>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                            @click="showEnroll = false">Close</button>
                        <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ID Modal -->
    <div class="modal fade" id="identificationmodal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Register Identification</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        @click="showIdentification = false"></button>
                </div>
                <div class="modal-body">
                    <StudentIdModal v-if="showIdentification" :studentData="identificationData" />
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <div class="form-group">
                        <small id="emailHelp" class="form-text text-muted">We'll never share your personal information
                            with anyone
                            else (Data Privacy Act of 2012)</small>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                            @click="showIdentification = false">Close</button>
                        <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Print ID Modal -->
    <div class="modal fade" id="printidmodal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Print ID</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        @click="showPrintID = false"></button>
                </div>
                <div class="modal-body">
                    <ApplicationPrintIdModal v-if="showPrintID" :studentdata="identificationData" :useriddata="userID"/>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <div class="form-group">
                        <small id="emailHelp" class="form-text text-muted">We'll never share your personal information
                            with anyone
                            else (Data Privacy Act of 2012)</small>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                            @click="showPrintID = false">Close</button>
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
                    <!-- <ApplicationPrintIdModal v-if="showQRScanner" :studentdata="identificationData" :useriddata="userID"/> -->
                     <SearchQR v-if="showQRScanner" @fetchData="getData" modeData="1"/>
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

    <!-- Milestone Modal -->
    <div class="modal fade" id="milestonemodal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Student Milestones</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        @click="showMilestones= false"></button>
                </div>
                <div class="modal-body">
                    <ApplicationMilestoneModal v-if="showMilestones" :student="identificationData" />
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <div class="form-group">
                        <small id="emailHelp" class="form-text text-muted">We'll never share your personal information
                            with anyone
                            else (Data Privacy Act of 2012)</small>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                            @click="showMilestones= false">Close</button>
                        <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    

</template>