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
import ApplicationFormModal from '../snippets/modal/ApplicationFormModal.vue';
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
    getAcademicStatus,
    getScholarshipDetails,
    addScholarshipDetails,
    getStudentAccount
} from "../Fetchers.js";
import ApplicationPrintIdModal from '../snippets/modal/ApplicationPrintIdModal.vue';
import SearchQR from '../snippets/tech/SearchQR.vue';
import ApplicationMilestoneModal from '../snippets/modal/ApplicationMilestoneModal.vue';
import AccountingStudentAcc from '../snippets/modal/AccountingStudentAcc.vue';
import AccountingExaminationPermits from '../snippets/modal/AccountingExaminationPermits.vue';

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
const showStudAccModal = ref(false)
const showEnroll = ref(false)
const showIdentification = ref(false)
const showPrintID = ref(false)
const showQRScanner = ref(false)
const showApplicationForm = ref(false)
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
const scholarshipType = ref('')
const scholarshipAmount = ref(0)
const scholarshipDetails = ref([])
const scholarshipDescription = ref('')
const scholarshipAccountId = ref('')
const loadingScholarship = ref(true)
const showScholarship = ref(false)
const showStudPermitModal = ref(false)
const studentSettlement = ref([])
const emit = defineEmits(['fetchUser','doneLoading'])


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
                getApplicant(limit.value, offset.value, searchFname.value, searchMname.value, searchLname.value,1).then((results) => {
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
                getApplicant(limit.value, offset.value, searchFname.value, searchMname.value, searchLname.value,1).then((results) => {
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
                getApplicant(limit.value, offset.value, searchFname.value, searchMname.value, searchLname.value,4).then((results) => {
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
const editData = (id, mode) => {

    editId.value = id
    formMode.value = id
    switch(mode){
        case 1:
            // student account
            showStudAccModal.value = !showStudAccModal.value
            break;
        case 2:
            //scholarship
            loadingScholarship.value = true
            scholarshipDetails.value = []
            getStudentAccount(id).then((results) => {
                studentSettlement.value = results.student_settlement
                refreshState()
            })
            break;
        case 3:
            // permits
            showStudPermitModal.value = !showStudPermitModal.value
            break;
    }
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

const getData = (data) =>{
    applicant.value = data
    showQRScanner.value = !showQRScanner
    document.getElementById('hideqrscanner').click();
}

const viewApplicationFormModal = (data) => {
    editId.value = data
    formMode.value = data
    showApplicationForm.value = !showApplicationForm.value
}

const addScholarship = () =>{
    if(!scholarshipDescription.value || !scholarshipType.value || !scholarshipAmount.value || scholarshipAmount.value <= 0 || !scholarshipAccountId.value){
         Swal.fire({
            title: "Saving Declined",
            html: `Please complete the form before adding`,
            icon: "warning",
            confirmButtonText: "Ok, Got it!"
        });
    }else{
        let x = {
            sch_description: scholarshipDescription.value,
            sch_type: scholarshipType.value,
            sch_personid: editId.value,
            sch_value: scholarshipAmount.value,
            sch_acsid: scholarshipAccountId.value,
            user_id: userID.value
        }

        saveScholarship(1, x)
        // scholarshipDetails.value.push(x)
        // resetScholarshipDetails()

    }
    
}

const saveScholarship = async (mode, data) => {

    const actionMap = {
        1: {
            title: 'New Record',
            text: 'Are you sure you want to add this scholarship?',
            confirmText: "Yes, Add it!"
        },
        2: {
            title: 'Update',
            text: 'Are you sure you want to update this scholarship? There’s no turning back.',
            confirmText: "Yes, update it!"
        },
        3: {
            title: 'Delete',
            text: 'Are you sure you want to delete this scholarship? There’s no turning back.',
            confirmText: "Yes, delete it!"
        }
    }

    const action = actionMap[mode]
    if (!action) return

    const payload = {
        ...data,
        sch_mode: mode,
        user_id: userID.value
    }

    const confirm = await Swal.fire({
        title: action.title,
        text: action.text,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: action.confirmText
    })

    if (!confirm.isConfirmed) return

    Swal.fire({
        title: "Processing",
        text: "Please wait...",
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    })

    try {
        const results = await addScholarshipDetails(payload)
        Swal.close()

        if (results.status === 200) {
            await Swal.fire({
                title: "Success",
                text: "Changes applied successfully",
                icon: "success"
            })

            refreshState()

        } else {
            throw new Error('Request failed')
        }

    } catch (err) {
        Swal.fire({
            title: "Action Failed",
            text: "Cannot proceed. Contact the Administrator for assistance.",
            icon: "error"
        })

        refreshState()
    }
}

const resetScholarshipDetails = () =>{
    scholarshipDescription.value = ''
    scholarshipType.value = ''
    scholarshipAmount.value = ''
    scholarshipAccountId.value = ''
}

const refreshState = () =>{
    resetScholarshipDetails()
    scholarshipDetails.value = []

    getScholarshipDetails(editId.value).then((results) => {
        scholarshipDetails.value = results.raw
        loadingScholarship.value = false
    })
}
</script>
<template>
    <div>
        <div class="p-3 mb-4 border-bottom">
            <h5 class=" text-uppercase fw-bold">Student Accounts</h5>
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
            <!-- <div class="d-flex flex-wrap w-50 justify-content-end gap-2">
                <button tabindex="-1" data-bs-toggle="modal" data-bs-target="#editdatamodal" @click="editData('new')"
                    type="button" class="btn btn-sm btn-primary" :disabled="preLoading? true:false">
                    <font-awesome-icon icon="fa-solid fa-add"  /> New Record
                </button>
                <button tabindex="-1" data-bs-toggle="modal" data-bs-target="#editdatamodal" @click="editData('old')"
                    type="button" class="btn btn-sm btn-info" :disabled="preLoading? true:false">
                    <font-awesome-icon icon="fa-solid fa-add"  /> Old Record
                </button>
            </div> -->
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
                        <td v-if="accessData[0].useracc_modifying == 1" class="align-middled d-flex gap-1 justify-content-center">
                            <div class="d-flex gap-2 justify-content-center">
                                <button data-bs-toggle="modal" data-bs-target="#editdatamodal" @click="editData(app.per_id, 1)"
                                    type="button" title="View Accounts" class="btn btn-secondary btn-sm">
                                    <font-awesome-icon icon="fa-solid fa-gear"/></button>
                            </div>
                            <div class="d-flex gap-2 justify-content-center">
                                <button data-bs-toggle="modal" data-bs-target="#scholarshipmodal" @click="editData(app.per_id, 2)"
                                    type="button" title="Add Scholarship" class="btn btn-secondary btn-sm">
                                    <font-awesome-icon icon="fa-solid fa-graduation-cap"/></button>
                            </div>
                            <!-- <div class="d-flex gap-2 justify-content-center">
                                <button data-bs-toggle="modal" data-bs-target="#permitmodal" @click="editData(app.per_id, 3)"
                                    type="button" title="Permit" class="btn btn-secondary btn-sm">
                                    <font-awesome-icon icon="fa-solid fa-id-card"/></button>
                            </div> -->
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


    <!-- Accounts Modal -->
    <div class="modal fade" id="editdatamodal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Accounts</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        @click="showStudAccModal = false"></button>
                </div>
                <div class="modal-body">
                        <AccountingStudentAcc v-if="showStudAccModal" :personId="editId" :userId="userID" :modeId="1"/>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <div class="form-group">
                        <small id="emailHelp" class="form-text text-muted">We'll never share your personal information
                            with anyone
                            else (Data Privacy Act of 2012)</small>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                            @click="showStudAccModal = false">Close</button>
                        <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Permits Modal -->
    <div class="modal fade" id="permitmodal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Examination Permit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        @click="showStudPermitModal = false"></button>
                </div>
                <div class="modal-body">
                        <AccountingExaminationPermits v-if="showStudPermitModal" :personId="editId" :userId="userID"/>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <div class="form-group">
                        <small id="emailHelp" class="form-text text-muted">We'll never share your personal information
                            with anyone
                            else (Data Privacy Act of 2012)</small>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                            @click="showStudAccModal = false">Close</button>
                        <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
                    </div>
                </div>
            </div>
        </div>
    </div>

        <!-- Scholarship Modal -->
    <div class="modal fade" id="scholarshipmodal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Scholarships</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        @click="showStudAccModal = false"></button>
                </div>
                <div class="modal-body overflow-auto" style="height: 500px;">
                        <!-- <div class="w-100 d-flex justify-content-end p-2">
                            <button type="button" class="btn btn-sm btn-dark" title="Save Updates">
                                <font-awesome-icon icon="fa-solid fa-add"/> Add new
                            </button>
                        </div> -->
                         <table class="table table-bordered">
                            <thead>
                                 <tr>
                                    <td class="align-middle">
                                        <input type="text" class="form-control form-control-sm" placeholder="Description Here" v-model="scholarshipDescription"/>
                                    </td>
                                    <td class="align-middle">
                                        <select class="form-select form-select-sm" v-model="scholarshipType">
                                            <option value="" disabled>-- Select Type --</option>
                                            <option value="1">Percentage</option>
                                            <option value="2">Amount</option>
                                        </select>
                                    </td>
                                    <td class="align-middle">
                                        <input v-if="scholarshipType" type="number" class="form-control form-control-sm" 
                                         :placeholder="scholarshipType == 1? 'Enter Amount':'Enter percentage'" v-model="scholarshipAmount"/>
                                        <span v-else>Please select scholarship type first</span>
                                    </td>
                                    <td class="align-middle">
                                        <select class="form-select form-select-sm" v-model="scholarshipAccountId">
                                            <option value="" disabled>-- Select Type --</option>
                                            <option v-for="(sts, index) in studentSettlement" :value="sts.acs_id">{{ sts.acs_accheader }}</option>
                                        </select>
                                    </td>
                                    <td class="align-middle">
                                        <button type="button" class="btn btn-sm btn-dark" title="Save Updates" @click="addScholarship()">
                                            <font-awesome-icon icon="fa-solid fa-add"/> Add new
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <th colspan="5" class="p-3 bg-secondary-subtle"></th>
                                </tr>
                                <tr>
                                    <th>Scholarship Description</th>
                                    <th>Discount Percentage</th>
                                    <th>Discount Amount</th>
                                    <th>Account</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="!Object.keys(scholarshipDetails).length && loadingScholarship">
                                    <td colspan="5">
                                        <Loader/>
                                    </td>
                                </tr>
                                <tr v-if="!Object.keys(scholarshipDetails).length && !loadingScholarship">
                                    <td colspan="5">
                                        No Scholarship Applied
                                    </td>
                                </tr>
                                <tr v-for="(sch, index) in scholarshipDetails" v-if="Object.keys(scholarshipDetails).length && !loadingScholarship">
                                    <td class="align-middle">
                                        <input type="text" class="form-control form-control-sm" placeholder="Description Here" v-model="sch.sch_description"/>
                                    </td>
                                    <td class="align-middle">
                                        <select class="form-select form-select-sm" v-model="sch.sch_type">
                                            <option value="" disabled>-- Select Type --</option>
                                            <option value="1">Percentage</option>
                                            <option value="2">Amount</option>
                                        </select>
                                    </td>
                                    <td class="align-middle">
                                        <input v-if="sch.sch_value" type="number" class="form-control form-control-sm" 
                                         :placeholder="sch.sch_value == 1? 'Enter Amount':'Enter percentage'" v-model="sch.sch_value"/>
                                        <span v-else>Please select scholarship type first</span>
                                    </td>
                                     <td class="align-middle">
                                        <select class="form-select form-select-sm" v-model="sch.sch_acsid">
                                            <option value="" disabled>-- Select Type --</option>
                                            <option v-for="(sts, index) in studentSettlement" :value="sts.acs_id">{{ sts.acs_accheader }}</option>
                                        </select>
                                    </td>
                                    <td class="align-middle">
                                        <div class="d-flex gap-1 justify-content-center">
                                            <button v-if="sch.sch_id" type="button" class="btn btn-sm btn-primary" title="Save Updates" @click="saveScholarship(2,sch)">
                                                <font-awesome-icon icon="fa-solid fa-floppy-disk"/>  
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger" title="Delete Scholarship" @click="saveScholarship(3, sch)">
                                                <font-awesome-icon icon="fa-solid fa-trash"/> 
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <!-- <tr>
                                    <td colspan="4">
                                        <div class="d-flex justify-content-end">
                                            <button type="button" class="btn btn-sm btn-success" title="Save Updates" @click="saveScholarship(1)">
                                                <font-awesome-icon icon="fa-solid fa-floppy-disk"/>&nbsp;Save Scholarship
                                            </button>
                                        </div>
                                    </td>
                                </tr> -->

                                <!-- More scholarship records can be added here -->
                            </tbody>
                         </table>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <div class="form-group">
                        <small id="emailHelp" class="form-text text-muted">We'll never share your personal information
                            with anyone
                            else (Data Privacy Act of 2012)</small>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                            @click="showStudAccModal = false">Close</button>
                        <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    
</template>