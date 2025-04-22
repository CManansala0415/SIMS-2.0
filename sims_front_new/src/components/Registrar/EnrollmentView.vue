<script setup>
import { ref, onMounted, computed } from 'vue';
import {
    getStudent,
    getQuarter,
    getProgram,
    getGradelvl,
    getProgramList,
    uploadProfile,
    uploadLink,
    getStudentByCourse,
    getCurriculumStudent,
    getSection,
    getTaggedSubject,
    deleteEnrollment,
    getSubject,
    getAccountsDetails,
    getStudentFiltering
} from "../Fetchers.js";
import Loader from '../snippets/loaders/Loading1.vue';
// import PrintForm from '../snippets/modal/PrintForms.vue';
import Taggings from '../snippets/modal/EnrollmentTagging.vue';
import { getUserID } from "../../routes/user.js";

const preLoading = ref(true)
const student = ref([])
const quarter = ref([])
const gradelvl = ref([])
const program = ref([])
const course = ref([])
const curriculum = ref([])
const section = ref([])
const subject = ref([])
const accounts = ref([])
const taggedSubject = ref([])
const booting = ref('')
const bootingCount = ref(0)
const showTaggingModal = ref(false)
const showFormData = ref([])
const activeForm = ref(1)
const limit = ref(10)
const offset = ref(0)
const studentCount = ref(0)
const searchValue = ref([])
const searchFname = ref('')
const searchMname = ref('')
const searchLname = ref('')
const showLink = ref(false)
const linkId = ref('')
const holdSubmit = ref(false)
const image = ref('')
const userID = ref('')
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
    getSubject().then((results) => {
        subject.value = results
        booting.value = 'Loading Sections...'
        bootingCount.value += 1
    })
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
    getUserID().then(async(results) => {
        userID.value = results.account.data.id
        accessData.value = results.access.data
        emit('fetchUser', results)
        try {
            await booter().then((results) => {
                booting.value = 'Loading Students...'
                bootingCount.value += 1
                getStudentFiltering(limit.value, offset.value, searchFname.value, searchMname.value, searchLname.value, paramsProgram.value, paramsGradelvl.value, paramsCourse.value).then((results) => {
                    student.value = results.data
                    console.log(student.value)
                    studentCount.value = results.count
                    preLoading.value = false

                    let x = student.value.map((e) => {
                        let y = accounts.value.findIndex((f) => {
                            return f.acs_enrid === e.enr_id
                        })
                        return {
                            ...e,
                            ...accounts.value[y],
                        }
                    })

                    student.value = x
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
                getStudentFiltering(limit.value, offset.value, searchFname.value, searchMname.value, searchLname.value, paramsProgram.value, paramsGradelvl.value, paramsCourse.value).then((results) => {
                    student.value = results.data
                    studentCount.value = results.count
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
                getStudentFiltering(limit.value, offset.value, searchFname.value, searchMname.value, searchLname.value, paramsProgram.value, paramsGradelvl.value, paramsCourse.value).then((results) => {
                    student.value = results.data
                    studentCount.value = results.count
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
                getStudentFiltering(limit.value, offset.value, searchFname.value, searchMname.value, searchLname.value, paramsProgram.value, paramsGradelvl.value, paramsCourse.value).then((results) => {
                    student.value = results.data
                    studentCount.value = results.count
                    preLoading.value = false
                }).catch((err) => {
                    // console.log(err)
                })
            } else {
                alert('Please search a valid record')
                preLoading.value = false
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
const upload = (personID) => {
    holdSubmit.value = true

    formData.set('image', image.value)
    uploadProfile(formData).then((results) => {
        showLink.value = !showLink.value, linkId.value = ''
        image.value = ''
        if (results.status == 200) {
            let data = {
                profile: results.link,
                personid: personID
            }
            uploadLink(data).then((results) => {
                if (results.status == 200) {
                    alert('Upload Successful')
                    location.reload()
                    holdSubmit.value = false
                } else {
                    alert('Upload Successful but Linking Failed')
                    holdSubmit.value = false
                }
            })
        } else {
            alert('Upload Failed')
            holdSubmit.value = false
        }
    }).catch((err) => {
        // console.log(err)
    })
}

const handleImage = (e) => {
    image.value = e.target.files[0]
}
// image uploading


const search = () => {
    paginate('search')
}
const studentVal = ref([])
const showForm = (type, data) => {


    switch (type) {
        case 2:
            showFormData.value = data
            getCurriculumStudent(data.enr_course, data.enr_program).then((results) => {
                curriculum.value = results
                showTaggingModal.value = true
            })

            break;
        case 3:
            studentVal.value = data
            // showTaggingModal.value = true
            activeForm.value = type
            break;
        default:
            activeForm.value = 1
    }
}

const dropStudent = (id) => {

    if (confirm("Are you sure you want to drop this student? this action cannot be reverted") == true) {
        let x = {
            enr_updatedby: userID.value,
            enr_id: id
        }

        deleteEnrollment(x).then((results) => {
            alert('Student Dropped')
            location.reload()
        })
    } else {
        return false;
    }
}


const paramsProgram = ref(0)
const paramsGradelvl = ref(0)
const paramsCourse = ref(0)

const validate = () => {
    const regEx1 = /[^a-zA-Z\s]+/;
    searchValue.value = searchValue.value.replace(regEx1, '');
}

</script>
<template>
    <div>
        <div class="p-3 mb-4 border-bottom">
            <h5 class=" text-uppercase fw-bold">Enrolled Students</h5>
        </div>

        <div class="p-1 d-flex gap-2 justify-content-between mb-3">
            <div class="d-flex gap-2 w-100">
                <div class="d-flex gap-2">
                    <div class="d-flex gap-2 justify-content-center align-content-center">
                        <input type="text" @keyup="validate()" v-model="searchFname" @keyup.enter="search()"
                            class="form-control w-100" :disabled="preLoading?true:false" placeholder="First Name"/>
                        <input type="text" @keyup="validate()" v-model="searchMname" @keyup.enter="search()"
                            class="form-control w-100" :disabled="preLoading?true:false" placeholder="Middle Name"/>
                        <input type="text" @keyup="validate()" v-model="searchLname" @keyup.enter="search()"
                            class="form-control w-100" :disabled="preLoading?true:false" placeholder="Last Name"/>
                    </div>
                </div>
            </div>
            <div class="d-flex gap-2 w-100">
                <div class="d-flex gap-2 w-100">
                    <!-- <select class="form-select form-select-sm w-100" tabindex="-1" v-model="paramsProgram" :disabled="preLoading?true:false">
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
                    </select> -->
                    <div class="d-flex justify-content-center align-content-center">
                        <button @click="search()" type="button" class="btn btn-sm btn-info text-white w-100" tabindex="-1" :disabled="preLoading?true:false">
                            Search
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-responsive border p-3 small-font">
            <table class="table table-hover table-fixed">
                <thead>
                    <tr>
                        <th style="background-color: #237a5b;" class="text-white">Profile</th>
                        <th style="background-color: #237a5b;" class="text-white">Full Name</th>
                        <th style="background-color: #237a5b;" class="text-white">Details</th>
                        <th style="background-color: #237a5b;" class="text-white">Date Enrolled</th>
                        <th style="background-color: #237a5b;" class="text-white">Downpayment</th>
                        <th style="background-color: #237a5b;" class="text-white">Commands</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="!preLoading && Object.keys(student).length" v-for="(stud, index) in student">
                        <td class="align-middle p-2">
                            <div class="d-flex flex-column justify-content-center align-content-center">
                                <div class="d-flex justify-content-center align-content-center w-100">
                                    <label @click="showLink = !showLink, linkId = index" :for="index" class="m-2">
                                        <img :src="stud.per_profile ? 'http://localhost:8000/storage/profiles/' + stud.per_profile : '/img/profile_default.png'"
                                            class="img-size" />
                                    </label>
                                </div>
                                <div v-if="showLink"
                                    class="d-flex flex-column justify-content-center align-content-center p-2 w-100">
                                    <form v-if="showLink && index == linkId" @submit.prevent="upload(stud.per_id)"
                                        method="post" enctype="multipart/form-data">
                                        <input type="file" :id="index" class="hidden" @change="handleImage">
                                        <button type="submit" class="btn btn-primary btn-sm m-2"
                                            :disabled="holdSubmit ? true : false">Upload Photo</button>
                                    </form>
                                    <p v-if="showLink && index == linkId" class="fw-regular">{{ image.name }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="align-middle p-2">
                            {{ stud.per_firstname }} {{ stud.per_middlename }} {{ stud.per_lastname }} {{
                                stud.per_suffixname }}
                        </td>
                        <td class="align-middle p-2">
                            <div class="d-flex flex-column gap-1">
                                <select class="border-0 p-1" disabled tabindex="-1" v-model="stud.enr_program"
                                    :id="index + 'program'">
                                    <option v-for="(p, index) in program" :value="p.dtype_id">{{ p.dtype_desc }}
                                    </option>
                                </select>
                                <select class="border-0 p-1" disabled tabindex="-1" v-model="stud.enr_course"
                                    :id="index + 'course'">
                                    <option v-for="(c, index) in course" :value="c.prog_id">{{ c.prog_code }}</option>
                                </select>
                                <select class="border-0 p-1" disabled tabindex="-1" v-model="stud.enr_gradelvl"
                                    :id="index + 'gradelvl'">
                                    <option v-for="(g, index) in gradelvl" :value="g.grad_id">{{ g.grad_name }}</option>
                                </select>
                                <select class="border-0 p-1" disabled tabindex="-1" v-model="stud.enr_quarter"
                                    :id="index + 'quarter'">
                                    <option v-for="(q, index) in quarter" :value="q.quar_id">{{ q.quar_desc }}</option>
                                </select>
                            </div>
                        </td>
                        <td class="align-middle p-2">
                            {{ stud.enr_dateenrolled }}
                        </td>
                        <td class="align-middle p-2">
                            {{ stud.acs_payment > 0 ? 'Yes' : 'No' }}
                        </td>
                        <td v-if="accessData[1].useracc_modifying == 1" class="align-middle p-2">
                            <div class="d-flex gap-2 justify-content-center">
                                <button tabindex="-1" title="Subject Taggings" @click="showForm(2, stud)"
                                    data-bs-toggle="modal" data-bs-target="#taggingmodal"
                                    class="btn btn-secondary btn-sm">
                                    <font-awesome-icon icon="fa-solid fa-pen"/>
                                </button>
                                <button tabindex="-1" title="Print Forms" @click="showForm(3, stud)"
                                    class="btn btn-secondary btn-sm">
                                    <font-awesome-icon icon="fa-solid fa-print"/>
                                </button>
                                <button tabindex="-1" title="Drop Student" @click="dropStudent(stud.enr_id)"
                                    class="btn btn-secondary btn-sm">
                                    <font-awesome-icon icon="fa-solid fa-trash"/>
                                </button>
                            </div>
                        </td>
                        <td v-else class="align-middle p-2">
                            N/A
                        </td>
                    </tr>
                    <tr v-if="!preLoading && !Object.keys(student).length">
                        <td class="p-3 text-center" colspan="9">
                            No Records Found
                        </td>
                    </tr>
                    <tr v-if="preLoading && !Object.keys(student).length">
                        <td class="p-3 text-center" colspan="9">
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
                    <h5 class="modal-title" id="staticBackdropLabel">Application</h5>
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

</template>