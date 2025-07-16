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
    getAlumniStudents,
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
    getAcademicStatus,
    uploadProfile,
    uploadLinkProfile
} from "../Fetchers.js";
import ApplicationPrintIdModal from '../snippets/modal/ApplicationPrintIdModal.vue';
import SearchQR from '../snippets/tech/SearchQR.vue';
import ApplicationMilestoneModal from '../snippets/modal/ApplicationMilestoneModal.vue';
import AlumniTrackerTor from '../snippets/modal/AlumniTrackerTor.vue';
import AlumnitrackerDiploma from '../snippets/modal/AlumnitrackerDiploma.vue';

const limit = ref(10)
const offset = ref(0)
const alumni = ref([])
const alumniCount = ref(0)
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

    // getDemograph().then((results) => {
    //     country.value = results.country
    //     region.value = results.region
    //     province.value = results.province
    //     city.value = results.city
    //     barangay.value = results.barangay
    //     gender.value = results.gender
    //     nationality.value = results.nationality
    //     civilstatus.value = results.civilstatus
    //     booting.value = 'Loading Demographic Information'
    //     bootingCount.value += 1
    // })
    // getAcademicDefaults().then((results) => {
    //     gradelvl.value = results.gradelvl
    //     degree.value = results.program
    //     quarter.value = results.quarter
    //     course.value = results.course
    //     semester.value = results.semester
    //     section.value = results.section
    //     booting.value = 'Loading Academic Information'
    //     bootingCount.value += 1
    // })

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
                booting.value = 'Loading alumnis...'
                bootingCount.value += 1
            
                getAlumniStudents(limit.value, offset.value, searchFname.value, searchMname.value, searchLname.value,1).then((results) => {
                    alumni.value = results.data.filter((value, index, self) =>
                        index === self.findIndex((t) => t.arc_studentid === value.arc_studentid)
                    );
                    alumniCount.value = results.count
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

const paginate = (mode) => {
    searchFname.value = searchFname.value.trim()
    searchMname.value = searchMname.value.trim()
    searchLname.value = searchLname.value.trim()
    
    switch (mode) {
        case 'prev':
            if (offset.value <= 0) {
                offset.value = 0
            } else {
                alumni.value = []
                offset.value -= 10
                alumniCount.value = 0
                preLoading.value = true
                getAlumniStudents(limit.value, offset.value, searchFname.value, searchMname.value, searchLname.value,2).then((results) => {
                    // alumni.value = results.data
                    alumni.value = results.data.filter((value, index, self) =>
                        index === self.findIndex((t) => t.arc_studentid === value.arc_studentid)
                    );
                    alumniCount.value = results.count
                    preLoading.value = false
                })
            }
            break;
        case 'next':

            if (offset.value >= alumniCount.value) {
                offset.value = alumniCount.value
            } else {
                alumni.value = []
                offset.value += 10
                alumniCount.value = 0
                preLoading.value = true
                getAlumniStudents(limit.value, offset.value, searchFname.value, searchMname.value, searchLname.value,2).then((results) => {
                    // alumni.value = results.data
                    alumni.value = results.data.filter((value, index, self) =>
                        index === self.findIndex((t) => t.arc_studentid === value.arc_studentid)
                    );
                    alumniCount.value = results.count
                    preLoading.value = false
                })
            }
            break;
        case 'search':
            if (searchValue.value || searchValue.value == '') {
                alumni.value = []
                offset.value = 0
                alumniCount.value = 0
                preLoading.value = true
                getAlumniStudents(limit.value, offset.value, searchFname.value, searchMname.value, searchLname.value,2).then((results) => {
                    // alumni.value = results.data
                    alumni.value = results.data.filter((value, index, self) =>
                        index === self.findIndex((t) => t.arc_studentid === value.arc_studentid)
                    );
                    alumniCount.value = results.count
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

const getData = (result) =>{
    alumni.value = result
    showQRScanner.value = !showQRScanner
    document.getElementById('hideqrscanner').click();
}

const identificationData = ref([])
const modalMode = ref('')
const viewMilestones = (data, mode) => {
    identificationData.value = data
    showMilestones.value = !showMilestones.value
    modalMode.value = mode
}

const showLinkProfile = ref(false)
const linkId = ref('')
const holdSubmit = ref(false)
const image = ref('')
const formData = new FormData
const upload = (personID, existing) => {
    let old_pic = existing ? existing : 0
    holdSubmit.value = true

    formData.set('image', image.value)
    let folder = 'Alumni'
    //profile
    uploadProfile(formData, old_pic, folder).then((results) => {
        showLinkProfile.value = !showLinkProfile.value, linkId.value = ''
        image.value = ''
        if (results.status == 200) {
            let data = {
                profile: results.link,
                personid: personID,
                type:'Alumni'
            }
            uploadLinkProfile(data).then((results) => {
                if (results.status == 200) {
                    // alert('Upload Successful')
                    // location.reload()
                    // holdSubmit.value = false
                    Swal.fire({
                        title: "Upload Successful",
                        text: "Changes applied, refreshing the page",
                        icon: "success"
                    }).then(() => {
                        location.reload()
                        holdSubmit.value = false
                    });
                } else {
                    // alert('Upload Successful but Linking Failed')
                    // holdSubmit.value = false
                    Swal.fire({
                        title: "Upload Notice",
                        text: "Upload Successful but Linking Failed, try again later",
                        icon: "question"
                    }).then(() => {
                        holdSubmit.value = false
                    });
                }
            })
        } else {
            // alert('Upload Failed')
            // holdSubmit.value = false
            Swal.fire({
                title: "Upload Error",
                text: "Upload failed, try again later",
                icon: "error"
            }).then(() => {
                holdSubmit.value = false
            });
        }
    }).catch((err) => {
        // console.log(err)
    })
}

const handleImage = (e) => {
    image.value = e.target.files[0]
}

</script>
<template>
    <div>
        <div class="p-3 mb-4 border-bottom">
            <h5 class=" text-uppercase fw-bold">Alumni Tracker</h5>
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
                        <th style="background-color: #237a5b;" class="text-white">Student Profile</th>
                        <th style="background-color: #237a5b;" class="text-white">Student ID</th>
                        <th style="background-color: #237a5b;" class="text-white">First Name</th>
                        <th style="background-color: #237a5b;" class="text-white">Middle Name</th>
                        <th style="background-color: #237a5b;" class="text-white">Last Name</th>
                        <th style="background-color: #237a5b;" class="text-white">Suffix Name</th>
                        <th style="background-color: #237a5b;" class="text-white">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="!preLoading && Object.keys(alumni).length" v-for="(app, index) in alumni">
                        <td class="align-middle p-2">
                            <div class="d-flex flex-column justify-content-center align-content-center">
                                <div class="d-flex justify-content-center align-content-center w-100" title="Click to Upload Profile Picture">
                                    <label @click="showLinkProfile = !showLinkProfile, linkId = index" :for="index" class="m-2" style="cursor: pointer;">
                                        <img :src="app.arc_profile ? 'http://localhost:8000/storage/alumni/' + app.arc_profile : '/img/profile_default.png'"
                                            class="img-size" />
                                    </label>
                                </div>
                                <div v-if="showLinkProfile" 
                                    class="d-flex flex-column justify-content-center align-content-center p-2 w-100">
                                    <form v-if="showLinkProfile && index == linkId" @submit.prevent="upload(app.arc_personid,app.arc_profile)"
                                        method="post" enctype="multipart/form-data">
                                        <input type="file" :id="index" class="hidden" @change="handleImage">
                                        <button type="submit" class="btn btn-primary btn-sm m-1"
                                            :disabled="holdSubmit ? true : false">Upload Photo</button>
                                        <button class="btn btn-danger btn-sm m-1" @click="showLinkProfile = false">Cancel</button>
                                    </form>
                                    <p v-if="showLinkProfile && index == linkId" class="fw-regular">{{ image.name }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="align-middle">
                            {{ app.arc_studentid }}
                        </td>
                        <td class="align-middle">
                            {{ app.arc_firstname }}
                        </td>
                        <td class="align-middle">
                            {{ app.arc_middlename ? app.arc_middlename : 'N/A' }}
                        </td>
                        <td class="align-middle">
                            {{ app.arc_lastname }}
                        </td>
                        <td class="align-middle">
                            {{ app.arc_suffixname ? app.arc_suffixname : 'N/A' }}
                        </td>
                        <td v-if="accessData[6].useracc_modifying == 1" class="align-middle">
                            <div class="d-flex gap-2 justify-content-center">
                                <button data-bs-toggle="modal" data-bs-target="#milestonemodal" @click="viewMilestones(app,1)"
                                    type="button" title="View Milestone" class="btn btn-secondary btn-sm">
                                    <font-awesome-icon icon="fa-solid fa-folder"/></button>
                                <button data-bs-toggle="modal" data-bs-target="#milestonemodal" @click="viewMilestones(app,2)"
                                    type="button" title="View TOR" class="btn btn-secondary btn-sm">
                                    <font-awesome-icon icon="fa-solid fa-id-card"/></button>
                                <button data-bs-toggle="modal" data-bs-target="#milestonemodal" @click="viewMilestones(app,3)"
                                    type="button" title="View Diploma" class="btn btn-secondary btn-sm">
                                    <font-awesome-icon icon="fa-solid fa-graduation-cap"/></button>
                            </div>
                        </td> 
                        <td v-else class="align-middle">
                            N/A
                        </td>
                        
                    </tr>
                    <tr v-if="!preLoading && !Object.keys(alumni).length" style="text-transform:none">
                        <td class="p-3 text-center" colspan="7">
                            No Records Found
                        </td>
                    </tr>
                    <tr v-if="preLoading && !Object.keys(alumni).length" style="text-transform:none">
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
                    <button :disabled="Object.keys(alumni).length < 10 ? true : false" @click="paginate('next')"
                        class="btn btn-sm btn-secondary">Next</button>
                </div>
                <p class="">showing total of <span class="font-semibold">({{ alumniCount }})</span> items</p>
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
                     <SearchQR v-if="showQRScanner" @fetchData="getData" modeData="4"/>
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
                    <h5 class="modal-title" id="staticBackdropLabel">Student Information</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        @click="showMilestones= false"></button>
                </div>
                <div class="modal-body">
                    <ApplicationMilestoneModal v-if="showMilestones && modalMode==1" :student="identificationData" moduleType="2" />
                    <AlumniTrackerTor v-if="showMilestones && modalMode==2" :student="identificationData" moduleType="2"/>
                    <AlumnitrackerDiploma v-if="showMilestones && modalMode==3" :student="identificationData" moduleType="2"/>
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