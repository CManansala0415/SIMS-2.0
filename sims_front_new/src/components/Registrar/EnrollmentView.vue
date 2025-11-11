<script setup>
import { ref, onMounted, computed } from 'vue';
import {
    getStudent,
    getQuarter,
    getProgram,
    getGradelvl,
    getProgramList,
    uploadProfile,
    uploadLinkProfile,
    uploadSignature,
    uploadLinkSignature,
    getStudentByCourse,
    getCurriculumStudent,
    getSection,
    getTaggedSubject,
    deleteEnrollment,
    getSubject,
    getAccountsDetails,
    getStudentFiltering,
    getAcademicDefaults
} from "../Fetchers.js";
import Loader from '../snippets/loaders/Loading1.vue';
// import PrintForm from '../snippets/modal/PrintForms.vue';
import Taggings from '../snippets/modal/EnrollmentTagging.vue';
import { getUserID } from "../../routes/user.js";
import EnrollmentListFIlterModal from '../snippets/modal/EnrollmentListFIlterModal.vue';
import EnrollmentPrintModal from '../snippets/modal/EnrollmentPrintModal.vue';
import ApplicationPrintIdModal from '../snippets/modal/ApplicationPrintIdModal.vue';
import SearchQR from '../snippets/tech/SearchQR.vue';
import EnrollmentReceiptModal from '../snippets/modal/EnrollmentReceiptModal.vue';

const preLoading = ref(true)
const student = ref([])
const quarter = ref([])
const gradelvl = ref([])
const program = ref([])
const degree = ref([])
const course = ref([])
const curriculum = ref([])
const section = ref([])
const subject = ref([])
const accounts = ref([])
const taggedSubject = ref([])
const booting = ref('')
const bootingCount = ref(0)
const showTaggingModal = ref(false)
const showFilterModal = ref(false)
const showPrintID = ref(false)
const showFormData = ref([])
const activeForm = ref(1)
const limit = ref(10)
const offset = ref(0)
const studentCount = ref(0)
const searchValue = ref([])
const searchFname = ref('')
const searchMname = ref('')
const searchLname = ref('')
const showLinkProfile = ref(false)
const showLinkSignature = ref(false)
const linkId = ref('')
const holdSubmit = ref(false)
const image = ref('')
const userID = ref('')
const showPrintModal = ref('')
const printType = ref('')
const emit = defineEmits(['fetchUser', 'doneLoading'])
const accessData = ref([])

const booter = async () => {
    getAcademicDefaults().then((results) => {
        // console.log(results)
        gradelvl.value = results.gradelvl
        quarter.value = results.quarter
        course.value = results.course
        section.value = results.section
        program.value = results.program
        degree.value = results.degree
        subject.value = results.subject
        booting.value = 'Loading Academic Information'
        bootingCount.value += 1
    })

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
    // getSubject().then((results) => {
    //     subject.value = results
    //     booting.value = 'Loading Sections...'
    //     bootingCount.value += 1
    // })
    getTaggedSubject().then((results) => {
        taggedSubject.value = results
        booting.value = 'Loading Subjects...'
        bootingCount.value += 1
    })
    getAccountsDetails().then((results) => {
        accounts.value = results
        booting.value = 'Loading Accounts...'
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
    getUserID().then(async (results) => {
        userID.value = results.account.data.id
        accessData.value = results.access
        emit('fetchUser', results)
        try {
            await booter().then((results) => {
                booting.value = 'Loading Students...'
                bootingCount.value += 1
                getStudentFiltering(limit.value, offset.value, searchFname.value, searchMname.value, searchLname.value, paramsProgram.value, paramsGradelvl.value, paramsCourse.value, 1).then((results) => {
                    student.value = results.data
                    studentCount.value = results.count

                    let x = student.value.map((e) => {
                        let y = accounts.value.findIndex((f) => {
                            return f.acs_enrid === e.enr_id
                        })

                        // stud.per_profile ? 'http://localhost:8000/storage/profiles/' + stud.per_profile : '/img/man.png'

                        let z = ''
                        if (e.per_profile) {
                            z = 'http://localhost:8000/storage/profiles/' + e.per_profile
                        } else {
                            if (e.per_gender == 2) {
                                z = '/img/woman.png'
                            } else {
                                z = '/img/man.png'
                            }
                        }


                        return {
                            ...e,
                            profile_picture: z,
                            ...accounts.value[y],
                        }
                    })

                    student.value = x
                    //    console.log(student.value)

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
            }).then(() => {
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
        }).then(() => {
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
                student.value = []
                offset.value -= 10
                studentCount.value = 0
                preLoading.value = true
                getStudentFiltering(limit.value, offset.value, searchFname.value, searchMname.value, searchLname.value, paramsProgram.value, paramsGradelvl.value, paramsCourse.value, 0).then((results) => {
                    student.value = results.data
                    studentCount.value = results.count

                    let x = student.value.map((e) => {
                        let y = accounts.value.findIndex((f) => {
                            return f.acs_enrid === e.enr_id
                        })

                        // stud.per_profile ? 'http://localhost:8000/storage/profiles/' + stud.per_profile : '/img/man.png'

                        let z = ''
                        if (e.per_profile) {
                            z = 'http://localhost:8000/storage/profiles/' + e.per_profile
                        } else {
                            if (e.per_gender == 2) {
                                z = '/img/woman.png'
                            } else {
                                z = '/img/man.png'
                            }
                        }


                        return {
                            ...e,
                            profile_picture: z,
                            ...accounts.value[y],
                        }
                    })

                    student.value = x
                    preLoading.value = false
                })
            }
            break;
        case 'next':

            if (offset.value >= studentCount.value) {
                offset.value = studentCount.value
            } else {
                student.value = []
                offset.value += 10
                studentCount.value = 0
                preLoading.value = true
                getStudentFiltering(limit.value, offset.value, searchFname.value, searchMname.value, searchLname.value, paramsProgram.value, paramsGradelvl.value, paramsCourse.value, 0).then((results) => {
                    student.value = results.data
                    studentCount.value = results.count

                    let x = student.value.map((e) => {
                        let y = accounts.value.findIndex((f) => {
                            return f.acs_enrid === e.enr_id
                        })

                        // stud.per_profile ? 'http://localhost:8000/storage/profiles/' + stud.per_profile : '/img/man.png'

                        let z = ''
                        if (e.per_profile) {
                            z = 'http://localhost:8000/storage/profiles/' + e.per_profile
                        } else {
                            if (e.per_gender == 2) {
                                z = '/img/woman.png'
                            } else {
                                z = '/img/man.png'
                            }
                        }


                        return {
                            ...e,
                            profile_picture: z,
                            ...accounts.value[y],
                        }
                    })

                    student.value = x
                    preLoading.value = false
                })
            }
            break;
        case 'search':
            // searchValue.value = searchValue.value.trim()
            if (searchValue.value || searchValue.value == '') {
                student.value = []
                offset.value = 0
                studentCount.value = 0
                preLoading.value = true
                getStudentFiltering(limit.value, offset.value, searchFname.value, searchMname.value, searchLname.value, paramsProgram.value, paramsGradelvl.value, paramsCourse.value, 0).then((results) => {
                    student.value = results.data
                    studentCount.value = results.count

                    let x = student.value.map((e) => {
                        let y = accounts.value.findIndex((f) => {
                            return f.acs_enrid === e.enr_id
                        })

                        // stud.per_profile ? 'http://localhost:8000/storage/profiles/' + stud.per_profile : '/img/man.png'

                        let z = ''
                        if (e.per_profile) {
                            z = 'http://localhost:8000/storage/profiles/' + e.per_profile
                        } else {
                            if (e.per_gender == 2) {
                                z = '/img/woman.png'
                            } else {
                                z = '/img/man.png'
                            }
                        }


                        return {
                            ...e,
                            profile_picture: z,
                            ...accounts.value[y],
                        }
                    })

                    student.value = x
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
                }).then(() => {
                    preLoading.value = false
                });
            }
            break;
        // case 'course':
        //     student.value = []
        //     offset.value = 0
        //     studentCount.value = 0
        //     preLoading.value = true
        //     getStudentByCourse(limit.value, offset.value, courseId.value).then((results) => {
        //         student.value = results.data
        //         studentCount.value = results.count
        //         preLoading.value = false
        //     }).catch((err) => {
        //         // console.log(err)
        //     })

        //     break;

    }
}

// image uploading
const formData = new FormData
const upload = (personID, pictype, existing) => {
    let old_pic = existing ? existing : 0
    holdSubmit.value = true

    formData.set('image', image.value)
    let folder = 'Enrollment'
    if (pictype == 1) {
        //profile
        uploadProfile(formData, old_pic, folder).then((results) => {
            showLinkProfile.value = !showLinkProfile.value, linkId.value = ''
            image.value = ''
            if (results.status == 200) {
                let data = {
                    profile: results.link,
                    personid: personID,
                    type: 'Enrollment'
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
    } else {
        //signature
        uploadSignature(formData, old_pic).then((results) => {
            showLinkSignature.value = !showLinkSignature.value, linkId.value = ''
            image.value = ''
            if (results.status == 200) {
                let data = {
                    signature: results.link,
                    personid: personID,

                }
                uploadLinkSignature(data).then((results) => {
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
}

const handleImage = (e) => {
    image.value = e.target.files[0]
}
// image uploading


const search = () => {
    paginate('search')
}
const studentVal = ref([])
const showForm = (type, data, printtype) => {


    switch (type) {
        case 2:
            showFormData.value = data
            getCurriculumStudent(data.enr_course, data.enr_program).then((results) => {
                curriculum.value = results
                showTaggingModal.value = true
            })

            break;
        case 3:
            showFormData.value = data
            getCurriculumStudent(data.enr_course, data.enr_program).then((results) => {
                curriculum.value = results
                showPrintModal.value = true
                printType.value = printtype
            })
            // var element = document.getElementById('printform');
            // html2pdf(element);
            break;
        default:
            activeForm.value = 1
    }
}

const dropStudent = (id) => {

    // if (confirm("Are you sure you want to drop this student? this action cannot be reverted") == true) {
    //     let x = {
    //         enr_updatedby: userID.value,
    //         enr_id: id
    //     }

    //     deleteEnrollment(x).then((results) => {
    //         // alert('Student Dropped')
    //         // location.reload()
    //         Swal.fire({
    //             title: "Update Success",
    //             text: "Student dropped, refreshing the page",
    //             icon: "success"
    //         }).then(()=>{
    //             location.reload()
    //         });
    //     })
    // } else {
    //     return false;
    // }
    Swal.fire({
        title: "Delete Record",
        text: "Are you sure you want to drop this student? this action cannot be reverted",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, Im Delete it!"
    }).then(async (result) => {
        if (result.isConfirmed) {
            let x = {
                enr_updatedby: userID.value,
                enr_id: id
            }
            deleteEnrollment(x).then((results) => {
                // alert('Student Dropped')
                // location.reload()
                Swal.fire({
                    title: "Update Success",
                    text: "Student dropped, refreshing the page",
                    icon: "success"
                }).then(() => {
                    location.reload()
                });
            })
        }
    });
}


const paramsProgram = ref(0)
const paramsGradelvl = ref(0)
const paramsCourse = ref(0)

const identificationData = ref([])
const printID = (data) => {
    identificationData.value = data
    showPrintID.value = !showPrintID.value
}

// const validate = () => {
//     const regEx1 = /[^a-zA-Z\s]+/;
//     searchValue.value = searchValue.value.replace(regEx1, '');
// }
const showQRScanner = ref(false)
const getData = (result) => {
    // console.log(result)
    student.value = result
    showQRScanner.value = !showQRScanner
    document.getElementById('hideqrscanner').click();
}

</script>
<template>
    <div>
        <div class="p-3 mb-4 border-bottom">
            <h5 class=" text-uppercase fw-bold">Enrolled Students</h5>
        </div>

        <div class="p-1 d-flex gap-2 justify-content-between mb-3">
            <div class="d-flex gap-2 justify-content-center align-content-center">
                <input type="text" v-model="searchFname" @keyup.enter="search()" class="form-control w-100"
                    :disabled="preLoading ? true : false" placeholder="First Name" />
                <input type="text" v-model="searchMname" @keyup.enter="search()" class="form-control w-100"
                    :disabled="preLoading ? true : false" placeholder="Middle Name" />
                <input type="text" v-model="searchLname" @keyup.enter="search()" class="form-control w-100"
                    :disabled="preLoading ? true : false" placeholder="Last Name" />
                <button @click="search()" type="button" class="btn btn-sm btn-info text-white w-100" tabindex="-1"
                    :disabled="preLoading ? true : false">
                    Search
                </button>
                <button @click="() => $refs.focusTrap.activate(), showQRScanner = true" data-bs-toggle="modal"
                    data-bs-target="#scanqrmodal" type="button" class="btn btn-sm btn-dark text-white w-100"
                    tabindex="-1" :disabled="preLoading ? true : false">
                    Scan QR
                </button>
            </div>
            <div class="d-flex flex-wrap w-50 justify-content-end gap-2">
                <button tabindex="-1" data-bs-toggle="modal" data-bs-target="#filterdatamodal"
                    @click="showFilterModal = true" type="button" class="btn btn-sm btn-dark"
                    :disabled="preLoading ? true : false">
                    <font-awesome-icon icon="fa-solid fa-eye" /> View Student Listing
                </button>
            </div>
            <!-- <div class="d-flex gap-2 w-100">
                <div class="d-flex gap-2 w-100">
                    <select class="form-select form-select-sm w-100" tabindex="-1" v-model="paramsProgram" :disabled="preLoading?true:false">
                        <option value="0">Select Program</option>
                        <option v-for="(p, index) in program" :value="p.dtype_id">{{ p.dtype_desc }}</option>
                    </select>
                    <select class="form-select form-select-sm w-100" tabindex="-1" v-model="paramsGradelvl" :disabled="preLoading?true:false">
                        <option value="0">Select Grade Level</option>
                        <option v-for="(g, index) in gradelvl" :value="g.grad_id">{{ g.grad_name }}</option>
                    </select>
                    <select class="form-select form-select-sm w-100" tabindex="-1" v-model="paramsCourse" :disabled="preLoading?true:false">
                        <option value="0">Select Course</option>
                        <option v-for="(c, index) in course" :value="c.prog_id">{{ c.prog_code }}</option>
                    </select>
                    <div class="d-flex justify-content-center align-content-center">
                        <button @click="search()" type="button" class="btn btn-sm btn-secondary text-white w-100" tabindex="-1" :disabled="preLoading?true:false">
                            Search
                        </button>
                    </div>
                </div>
            </div> -->
        </div>

        <div class="table-responsive border p-3 small-font">
            <div class="container">
                <!-- <div class="row justify-content-center">
                    <div class="col-12 col-sm-8 col-lg-6">
                        <div class="section_heading text-center wow fadeInUp" data-wow-delay="0.2s"
                            style="visibility: visible; animation-delay: 0.2s; animation-name: fadeInUp;">
                            <h3>Our Creative <span> Team</span></h3>
                            <p>Appland is completely creative, lightweight, clean &amp; super responsive app landing
                                page.</p>
                            <div class="line"></div>
                        </div>
                    </div>
                </div> -->
                <div class="row justify-content-center" v-if="!preLoading && !Object.keys(student).length">
                    <div class="col-12 col-sm-8 col-lg-6">
                        <div class="section_heading text-center wow fadeInUp" data-wow-delay="0.2s"
                            style="visibility: visible; animation-delay: 0.2s; animation-name: fadeInUp;">
                            <h3>No Record Found</h3>
                            <p>Enrollment Information is empty. Either the enrollment has not yet been started or it's
                                just reseted to default.</p>
                            <div class="line"></div>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-center" v-if="preLoading && !Object.keys(student).length">
                    <div class="col-12 col-sm-8 col-lg-6">
                        <div class="section_heading text-center wow fadeInUp" data-wow-delay="0.2s"
                            style="visibility: visible; animation-delay: 0.2s; animation-name: fadeInUp;">
                            <Loader />
                        </div>
                    </div>
                </div>
                <div class="row" v-if="!preLoading && Object.keys(student).length">
                    <!-- Single Advisor-->
                    <div class="col-12 col-md-6 col-lg-4" v-for="(stud, index) in student">
                        <div class="single_advisor_profile wow fadeInUp border shadow" data-wow-delay="0.2s"
                            style="visibility: visible; animation-delay: 0.2s; animation-name: fadeInUp;">
                            <!-- Team Thumb-->
                            <div class="advisor_thumb">
                                <div @click="showLinkProfile = !showLinkProfile, linkId = index" :for="index"
                                    class="text_guide img_hover p-3 d-flex justify-content-center align-items-center">
                                    <!-- stud.per_profile ? 'http://localhost:8000/storage/profiles/' + stud.per_profile : '/img/man.png' -->
                                    <img class=" img-size-card img_profile shadow" :src="stud.profile_picture" alt="">
                                </div>
                                <div v-if="showLinkProfile && index === linkId"
                                    class="upload-section d-flex flex-column align-items-center justify-content-center p-3 rounded-4 shadow-sm w-100">
                                    <form @submit.prevent="upload(stud.per_id, 1, stud.per_profile)"
                                        enctype="multipart/form-data" class="w-100 text-center">
                                        <!-- Hidden File Input -->
                                        <input type="file" :id="'fileInput-' + index" class="d-none"
                                            @change="handleImage" />

                                        <!-- Custom Upload Button -->
                                        <label :for="'fileInput-' + index"
                                            class="upload-label btn btn-outline-success btn-sm px-4 py-2 m-1">
                                            <i class="fa fa-upload me-2"></i> Choose Photo
                                        </label>

                                        <!-- Submit & Cancel Buttons -->
                                        <div class="mt-2">
                                            <button type="submit" class="btn btn-success btn-sm px-4 py-2 m-1"
                                                :disabled="holdSubmit">
                                                <i class="fa fa-cloud-upload-alt me-2"></i> Upload
                                            </button>

                                            <button type="button" class="btn btn-outline-danger btn-sm px-4 py-2 m-1"
                                                @click="showLinkProfile = false">
                                                <i class="fa fa-times me-2"></i> Cancel
                                            </button>
                                        </div>

                                        <!-- Filename Display -->
                                        <p v-if="image?.name" class="mt-2 text-muted small fst-italic">
                                            Selected: {{ image.name }}
                                        </p>
                                    </form>
                                </div>
                            </div>
                            <!-- Team Details-->
                            <div class="single_advisor_details_info border-0 border-bottom border-top">
                                <h6 class="text-uppercase fw-bold"  style="font-size: 13px;"> {{ stud.per_firstname }} {{ stud.per_middlename }} {{
                                    stud.per_lastname }} {{
                                        stud.per_suffixname }}</h6>
                                <p v-if="stud.acs_amount > 0" class="text-success fw-bold" style="font-size: 10px;">Officially Enrolled</p>
                                <p v-else class="text-danger fw-bold" style="font-size: 10px;">Not Officially Enrolled</p>
                                <div class="d-flex flex-column gap-1 mt-3">
                                    <select class="border-0 p-1" disabled tabindex="-1" v-model="stud.enr_program"
                                        :id="index + 'program'">
                                        <option v-for="(p, index) in program" :value="p.dtype_id">{{ p.dtype_desc }}
                                        </option>
                                    </select>
                                    <select class="border-0 p-1" disabled tabindex="-1" v-model="stud.enr_course"
                                        :id="index + 'course'">
                                        <option v-for="(c, index) in course" :value="c.prog_id">{{ c.prog_code }}
                                        </option>
                                    </select>
                                    <select class="border-0 p-1" disabled tabindex="-1" v-model="stud.enr_gradelvl"
                                        :id="index + 'gradelvl'">
                                        <option v-for="(g, index) in gradelvl" :value="g.grad_id">{{ g.grad_name }}
                                        </option>
                                    </select>
                                    <select class="border-0 p-1" disabled tabindex="-1" v-model="stud.enr_quarter"
                                        :id="index + 'quarter'">
                                        <option v-for="(q, index) in quarter" :value="q.quar_id">{{ q.quar_desc }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="advisor_thumb">
                                <div v-if="stud.per_signature" @click="showLinkSignature = !showLinkSignature, linkId = index"
                                    class="text_guide img_hover p-3 d-flex justify-content-center align-items-center">
                                    <img class="signature-size-card"
                                        :src="'http://localhost:8000/storage/signatures/' + stud.per_signature" alt="">
                                </div>
                                <div v-else class="text_guide img_hover p-3" @click="showLinkSignature = !showLinkSignature, linkId = index">
                                    <div
                                        class="signature-size-card-none d-flex justify-content-center align-items-center">
                                        <span class="text-muted">No Signature Uploaded</span>
                                    </div>
                                </div>

                                <div v-if="showLinkSignature && index == linkId"
                                    class="upload-section d-flex flex-column align-items-center justify-content-center p-3 rounded-4 shadow-sm w-100">
                                    <form @submit.prevent="upload(stud.per_id,2,stud.per_signature)"
                                        enctype="multipart/form-data" class="w-100 text-center">
                                        <!-- Hidden File Input -->
                                        <input type="file" :id="'fileInput-' + index" class="d-none"
                                            @change="handleImage" />

                                        <!-- Custom Upload Button -->
                                        <label :for="'fileInput-' + index"
                                            class="upload-label btn btn-outline-success btn-sm px-4 py-2 m-1">
                                            <i class="fa fa-upload me-2"></i> Choose Photo
                                        </label>

                                        <!-- Submit & Cancel Buttons -->
                                        <div class="mt-2">
                                            <button type="submit" class="btn btn-success btn-sm px-4 py-2 m-1"
                                                :disabled="holdSubmit">
                                                <i class="fa fa-cloud-upload-alt me-2"></i> Upload
                                            </button>

                                            <button type="button" class="btn btn-outline-danger btn-sm px-4 py-2 m-1"
                                                @click="showLinkSignature = false">
                                                <i class="fa fa-times me-2"></i> Cancel
                                            </button>
                                        </div>

                                        <!-- Filename Display -->
                                        <p v-if="image?.name" class="mt-2 text-muted small fst-italic">
                                            Selected: {{ image.name }}
                                        </p>
                                    </form>
                                </div>

                            </div>

                            <div v-if="accessData[1].useracc_modifying == 1" class="d-flex gap-2 justify-content-center border p-3 bg-secondary-subtle">
                                <button tabindex="-1" title="Subject Taggings" @click="showForm(2, stud)"
                                    data-bs-toggle="modal" data-bs-target="#taggingmodal"
                                    class="btn btn-secondary btn-sm">
                                    <font-awesome-icon icon="fa-solid fa-pen" />
                                </button>
                                <!-- v-if="stud.acs_amount > 0" -->
                                <button v-if="stud.acs_amount > 0" tabindex="-1" title="Print Grades"
                                    data-bs-toggle="modal" data-bs-target="#printmodal" @click="showForm(3, stud, 1)"
                                    class="btn btn-secondary btn-sm">
                                    <font-awesome-icon icon="fa-solid fa-print" />
                                </button>
                                <!-- v-if="stud.acs_amount > 0" -->
                                <button v-if="stud.acs_amount > 0" tabindex="-1" title="Print Receipt" data-bs-toggle="modal"
                                    data-bs-target="#printmodal" @click="showForm(3, stud, 2)"
                                    class="btn btn-secondary btn-sm">
                                    <font-awesome-icon icon="fa-solid fa-print" />
                                </button>
                                <button tabindex="-1" title="Drop Student" @click="dropStudent(stud.enr_id)"
                                    class="btn btn-secondary btn-sm">
                                    <font-awesome-icon icon="fa-solid fa-trash" />
                                </button>
                                <!-- v-if="stud.acs_amount > 0" -->
                                <button v-if="stud.acs_amount > 0" data-bs-toggle="modal" data-bs-target="#printidmodal" @click="printID(stud)"
                                    type="button" title="print ID" class="btn btn-secondary btn-sm">
                                    <font-awesome-icon icon="fa-solid fa-id-card-clip" />
                                </button>
                            </div>
                            <div v-else class="d-flex gap-2 justify-content-center border p-3 bg-secondary-subtle">
                                No access to modify student records.
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-between align-content-center" v-if="!preLoading">
                <div class="d-flex gap-1">
                    <button :disabled="offset == 0 ? true : false" @click="paginate('prev')"
                        class="btn btn-sm btn-secondary">Prev</button>
                    <button :disabled="Object.keys(student).length < 10 ? true : false" @click="paginate('next')"
                        class="btn btn-sm btn-secondary">Next</button>
                </div>
                <p class="">showing total of <span class="font-semibold">({{ studentCount }})</span> items</p>
            </div>
        </div>
    </div>

    <!-- Tagging Modal -->
    <div class="modal fade" id="taggingmodal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Subjects</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        @click="showTaggingModal = false"></button>
                </div>
                <div class="modal-body">
                    <Taggings v-if="showTaggingModal" :student="showFormData" :subject="subject" :section="section"
                        :curriculum="curriculum" />
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <div class="form-group">
                        <small id="emailHelp" class="form-text text-muted">We'll never share your personal information
                            with anyone
                            else (Data Privacy Act of 2012)</small>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                            @click="showTaggingModal = false">Close</button>
                        <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Modal -->
    <div class="modal fade" id="filterdatamodal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Enrolled Students Listing</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        @click="showFilterModal = false"></button>
                </div>
                <div class="modal-body">
                    <EnrollmentListFIlterModal v-if="showFilterModal" :gradelvldata="gradelvl" :programdata="program"
                        :coursedata="course" :degreedata="degree" />
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <div class="form-group">
                        <small id="emailHelp" class="form-text text-muted">We'll never share your personal information
                            with anyone
                            else (Data Privacy Act of 2012)</small>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                            @click="showFilterModal = false">Close</button>
                        <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Print Modal -->
    <div class="modal fade" id="printmodal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Print and Download Form</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        @click="showPrintModal = false"></button>
                </div>
                <div class="modal-body">
                    <EnrollmentPrintModal v-if="showPrintModal && printType == 1" :student="showFormData"
                        :subject="subject" :section="section" :curriculum="curriculum" />
                    <EnrollmentReceiptModal v-if="showPrintModal && printType == 2" :student="showFormData"
                        :subject="subject" :section="section" :curriculum="curriculum" />
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <div class="form-group">
                        <small id="emailHelp" class="form-text text-muted">We'll never share your personal information
                            with anyone
                            else (Data Privacy Act of 2012)</small>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                            @click="showPrintModal = false">Close</button>
                        <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Print ID Modal -->
    <div class="modal fade" id="printidmodal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Print ID</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        @click="showPrintID = false"></button>
                </div>
                <div class="modal-body d-flex flex-column justify-content-center align-items-center">
                    <ApplicationPrintIdModal v-if="showPrintID" :studentdata="identificationData"
                        :useriddata="userID" />
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
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" tabindex="-1"
                        @click="showQRScanner = false" id="hideqrscanner"></button>
                </div>
                <div class="modal-body" v-if="showQRScanner">
                    <SearchQR @fetchData="getData" modeData="2" />
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <div class="form-group">
                        <small id="emailHelp" class="form-text text-muted">We'll never share your personal information
                            with anyone
                            else (Data Privacy Act of 2012)</small>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" tabindex="-1"
                            @click="showQRScanner = false">Close</button>
                        <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--  <button @click="() => $refs.focusTrap.activate()">Show the modal</button>
    <focus-trap :active="false" ref="focusTrap" :initial-focus="() => $refs.qrInput">
        <modal-dialog>
            <p>Hello there!</p>
            <input ref="nameInput" />
            <button @click="() => $refs.focusTrap.deactivate()">Okay...</button>
        </modal-dialog>
    </focus-trap> -->

</template>

<style scoped>
.img_profile {
    border: 1px solid #ddd;
    border-radius: 100%;
    height: 150px;
    width: 150px;
}

.signature-size-card {
    height: 50px;
    width: 50px;
}

.signature-size-card-none {
    height: 50px;
    width: 100%;
}

.text_guide {
    position: relative;
    display: block;
    cursor: pointer;
    background-color: rgb(243, 243, 243);
}

.text_guide img {
    display: block;
    /* width: 100%; */
    transition: transform 0.3s ease;
}

/* The hover overlay layer */
.text_guide::before {
    content: "";
    position: absolute;
    inset: 0;
    background-color: rgba(0, 0, 0, 0);
    /* transparent by default */
    transition: background-color 0.3s ease;
    z-index: 1;
}

/* The tooltip text */
.text_guide::after {
    content: "Click to Update Picture";
    position: absolute;
    color: white;
    background-color: rgba(0, 0, 0, 0.4);
    padding: 5px 10px;
    border-radius: 5px;
    font-size: 0.8rem;
    visibility: hidden;
    opacity: 0;
    transition: opacity 0.3s ease;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 2;
}

/* Hover effects */
.text_guide:hover::before {
    background-color: rgba(0, 0, 0, 0.4);
    /* dark overlay without affecting the image */
}

.text_guide:hover::after {
    visibility: visible;
    opacity: 1;
}

.text_guide:hover img {
    transform: scale(1.03);
    /* optional: subtle zoom effect */
}

.single_advisor_profile {
    position: relative;
    margin-bottom: 50px;
    -webkit-transition-duration: 500ms;
    transition-duration: 500ms;
    z-index: 1;
    /* border-radius: 15px; */
    /* border:2px solid #be0505; */
    -webkit-box-shadow: 0 0.25rem 1rem 0 rgba(47, 91, 234, 0.125);
    box-shadow: 0 0.25rem 1rem 0 rgba(47, 91, 234, 0.125);
}

.single_advisor_profile .advisor_thumb {
    position: relative;
    z-index: 1;
    /* border-radius: 15px 15px 0 0; */
    margin: 0 auto;
    /* padding: 30px 30px 0 30px; */
    /* padding: 20px 0 0 0; */
    /* background-color: #3f43fd; */
    overflow: hidden;
}

/* .single_advisor_profile .advisor_thumb::after {
    -webkit-transition-duration: 500ms;
    transition-duration: 500ms;
    position: absolute;
    width: 150%;
    height: 90px;
    bottom: -45px;
    left: -25%;
    content: "";
    background-color: #ffffff;
    -webkit-transform: rotate(-11deg);
    transform: rotate(-11deg);
} */

@media only screen and (max-width: 575px) {
    .single_advisor_profile .advisor_thumb::after {
        height: 160px;
        bottom: -90px;
    }
}

.single_advisor_profile .advisor_thumb .social-info {
    position: absolute;
    z-index: 1;
    width: 100%;
    bottom: 0;
    right: 30px;
    text-align: right;
}

.single_advisor_profile .advisor_thumb .social-info a {
    font-size: 14px;
    color: #020710;
    padding: 0 5px;
}

.single_advisor_profile .advisor_thumb .social-info a:hover,
.single_advisor_profile .advisor_thumb .social-info a:focus {
    color: #3f43fd;
}

.single_advisor_profile .advisor_thumb .social-info a:last-child {
    padding-right: 0;
}

.single_advisor_profile .single_advisor_details_info {
    position: relative;
    z-index: 1;
    padding: 30px;
    text-align: right;
    -webkit-transition-duration: 500ms;
    transition-duration: 500ms;
    /* border-radius: 0 0 15px 15px; */
    background-color: #ffffff;
}

.single_advisor_profile .single_advisor_details_info::after {
    -webkit-transition-duration: 500ms;
    transition-duration: 500ms;
    position: absolute;
    z-index: 1;
    width: 50px;
    height: 3px;
    background-color: #1ae49a;
    content: "";
    top: 12px;
    right: 30px;
}

.single_advisor_profile .single_advisor_details_info h6 {
    margin-bottom: 0.25rem;
    -webkit-transition-duration: 500ms;
    transition-duration: 500ms;
}

@media only screen and (min-width: 768px) and (max-width: 991px) {
    .single_advisor_profile .single_advisor_details_info h6 {
        font-size: 14px;
    }
}

.single_advisor_profile .single_advisor_details_info p {
    -webkit-transition-duration: 500ms;
    transition-duration: 500ms;
    margin-bottom: 0;
    font-size: 14px;
}

@media only screen and (min-width: 768px) and (max-width: 991px) {
    .single_advisor_profile .single_advisor_details_info p {
        font-size: 12px;
    }
}

/* .single_advisor_profile:hover .advisor_thumb::after,
.single_advisor_profile:focus .advisor_thumb::after {
    background-color: #2d9c73;
}

.single_advisor_profile:hover .advisor_thumb .social-info a,
.single_advisor_profile:focus .advisor_thumb .social-info a {
    color: #ffffff;
}

.single_advisor_profile:hover .advisor_thumb .social-info a:hover,
.single_advisor_profile:hover .advisor_thumb .social-info a:focus,
.single_advisor_profile:focus .advisor_thumb .social-info a:hover,
.single_advisor_profile:focus .advisor_thumb .social-info a:focus {
    color: #ffffff;
}

.single_advisor_profile:hover .single_advisor_details_info,
.single_advisor_profile:focus .single_advisor_details_info {
    background-color: #2d9c73;
}

.single_advisor_profile:hover .single_advisor_details_info::after,
.single_advisor_profile:focus .single_advisor_details_info::after {
    background-color: #ffffff;
}

.single_advisor_profile:hover .single_advisor_details_info h6,
.single_advisor_profile:focus .single_advisor_details_info h6 {
    color: #ffffff;
}

.single_advisor_profile:hover .single_advisor_details_info p,
.single_advisor_profile:focus .single_advisor_details_info p {
    color: #ffffff;
} */

.upload-section {
  background: #ffffff;
  border-radius: 12px;
  box-shadow: 4px 4px 8px rgba(0,0,0,0.08),
              -4px -4px 8px rgba(255,255,255,0.9);
  transition: all 0.3s ease;
}

.upload-section:hover {
  box-shadow: 6px 6px 12px rgba(0,0,0,0.1),
              -6px -6px 12px rgba(255,255,255,1);
}

.upload-label {
  background-color: #f9f9f9;
  border: 1px solid #237a5b;
  color: #237a5b;
  border-radius: 6px;
  transition: all 0.2s ease;
}

.upload-label:hover {
  background-color: #237a5b;
  color: white;
}

</style>