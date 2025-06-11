<script setup>
import { ref, onMounted, computed } from 'vue';
import {
    getStudent,
    getQuarter,
    getProgram,
    getGradelvl,
    getProgramList,
    getStudentByCourse,
    getCurriculumStudent,
    getSection,
    getTaggedSubject,
    getAccountsDetails,
    getMilestone,
    getPriceDetails,
    getAcademicDefaults,
    getStudentFiltering
} from "../Fetchers.js";
import Loader from '../snippets/loaders/Loading1.vue';
import AccountingPaymentModal from '../snippets/modal/AccountingPaymentModal.vue';
import AccountingDownloadModal from '../snippets/modal/AccountingDownloadModal.vue';

import { getUserID } from "../../routes/user";
import { useRouter, useRoute } from 'vue-router'
import SearchQR from '../snippets/tech/SearchQR.vue';

const router = useRouter();
const preLoading = ref(true)
const milestoneLoading = ref(false)
const student = ref([])
const quarter = ref([])
const gradelvl = ref([])
const program = ref([])
const course = ref([])
const curriculum = ref([])
const section = ref([])
const subject = ref([])
const booting = ref('')
const bootingCount = ref(0)
const limit = ref(10)
const offset = ref(0)
const studentCount = ref(0)
const searchValue = ref('')
const userID = ref('')
const accounts = ref([])
const balance = ref(false)
const studentAccount = ref({})
const milestone = ref([])
const billLoading = ref([])
const price = ref([])
const grandTotal = ref(0)
const showPaymentModal = ref(false)
const emit = defineEmits(['fetchUser'])
const accessData = ref([])

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
        program.value = results.program
        booting.value = 'Loading Academic Information'
        bootingCount.value += 1
    })
    getTaggedSubject().then((results) => {
        subject.value = results
        booting.value = 'Loading Subject...'
        bootingCount.value += 1
    })
    getAccountsDetails().then((results) => {
        accounts.value = results
        booting.value = 'Loading Accounts...'
        bootingCount.value += 1
    })
    getPriceDetails().then((results) => {
        price.value = results
        booting.value = 'Loading Prices...'
        bootingCount.value += 1
    })
    // getUserID().then((results) => {
    //     userID.value = results.account.data.id
    //     emit('fetchUser', results)
    //     booting.value = 'Loading Users...'
    //     bootingCount.value += 1
    // })


}

const searchFname = ref('')
const searchMname = ref('')
const searchLname = ref('')
const paramsProgram = ref(0)
const paramsGradelvl = ref(0)
const paramsCourse = ref(0)
onMounted(async () => {
    getUserID().then(async(results1) => {
        userID.value = results1.account.data.id
        accessData.value = results1.access
        emit('fetchUser', results1)
        try {
            preLoading.value = true
            await booter().then((results) => {

                booting.value = 'Loading Students...'
                bootingCount.value += 1
                getStudentFiltering(limit.value, offset.value, searchFname.value, searchMname.value, searchLname.value, paramsProgram.value, paramsGradelvl.value, paramsCourse.value,1).then((results) => {
                    student.value = results.data
                    studentCount.value = results.count
                    preLoading.value = false

                    // let x = studentAccount.value.map((e) => {
                    //     let y = accounts.value.findIndex((f) => {
                    //         return f.acs_enrid === e.enr_id
                    //     })
                    //     return {
                    //         ...e,
                    //         ...accounts.value[y],
                    //     }
                    // })

                    // studentAccount.value = x
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
                getStudentFiltering(limit.value, offset.value, searchFname.value, searchMname.value, searchLname.value, paramsProgram.value, paramsGradelvl.value, paramsCourse.value,0).then((results) => {
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
                getStudentFiltering(limit.value, offset.value, searchFname.value, searchMname.value, searchLname.value, paramsProgram.value, paramsGradelvl.value, paramsCourse.value,0).then((results) => {
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
                getStudentFiltering(limit.value, offset.value, searchFname.value, searchMname.value, searchLname.value, paramsProgram.value, paramsGradelvl.value, paramsCourse.value,0).then((results) => {
                    student.value = results.data
                    studentCount.value = results.count
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



const search = () => {
    paginate('search')
}
const showDownloadModal = ref(false)
const excelDownload = () => {
    showDownloadModal.value = true
}

const settlement = (stud) => {
    balance.value = true
    billLoading.value = true
    let x = accounts.value.filter((e) => {
        if (e.acs_personid == stud.enr_personid) {
            return {
                e,
            }
        }
    })



    getMilestone(stud.enr_id).then((results) => {
        grandTotal.value = 0
        milestone.value = results
        billLoading.value = false

        milestone.value = results.map((e, index) => {
            let lab_amount = 0
            let lec_amount = 0
            let hrs_amount = 0

            let indexer = price.value.findIndex(f => {
                lab_amount = f.acp_per_lab_unit ? f.acp_per_lab_unit : 0
                lec_amount = f.acp_per_lec_unit ? f.acp_per_lec_unit : 0
                hrs_amount = f.acp_per_hours ? f.acp_per_hours : 0
                return e.subj_id === f.acp_subjid
            });

            if (indexer !== -1) {

                let grand = lab_amount + lec_amount + hrs_amount
                let total = parseInt(grand * 100) / 100;
                grandTotal.value += total

                return {
                    ...e,
                    lab_amount: lab_amount,
                    lec_amount: lec_amount,
                    hrs_amount: hrs_amount,
                    total: total.toFixed(2)
                }
            } else {
                return {
                    ...e,
                    lab_amount: 0,
                    lec_amount: 0,
                    hrs_amount: 0,
                    total: 0
                }
            }

        })

        studentAccount.value = {
            ...x[0],
            ...stud,
            acr_amount: grandTotal.value
        }

    })

}

const settlePayments = () => {
    showPaymentModal.value = true
}

const showQRScanner = ref(false)
const getData = (result) =>{
    console.log(result)
    student.value = result
    showQRScanner.value = !showQRScanner
    document.getElementById('hideqrscanner').click();
}

</script>

<template>
    <div class="small-font">
        <div class="p-3 mb-4 border-bottom">
            <h5 class=" text-uppercase fw-bold">Billing Tuition</h5>
        </div>

        <div v-if="!balance" class="p-1 d-flex gap-2 justify-content-between mb-3">
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
                <button data-bs-toggle="modal" data-bs-target="#scanqrmodal" type="button" class="btn btn-sm btn-dark text-white w-100" tabindex="-1" :disabled="preLoading?true:false">
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
        <div v-else class="p-1 d-flex gap-2 justify-content-end mb-3">
            <div class="d-flex w-50 justify-content-end">
                <button tabindex="-1" @click="balance=false" 
                    type="button" class="btn btn-sm btn-primary" :disabled="preLoading || billLoading ? true : false">
                    <font-awesome-icon icon="fa-solid fa-rotate-left" /> Back
                </button>
            </div>
        </div>

        <div v-if="!balance" class="table-responsive border p-3">
            <table class="table table-hover table-fixed" style="text-transform:uppercase">
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
                                    <label :for="index" class="m-2">
                                        <img :src="stud.per_profile ? 'http://localhost:8000/storage/profiles/' + stud.per_profile : '/img/profile_default.png'"
                                            class="img-size" />
                                    </label>
                                </div>
                                <!-- <div v-if="showLink"
                                    class="d-flex flex-column justify-content-center align-content-center p-2 w-100">
                                    <form v-if="showLink && index == linkId" @submit.prevent="upload(stud.per_id)"
                                        method="post" enctype="multipart/form-data">
                                        <input type="file" :id="index" class="hidden" @change="handleImage">
                                        <button type="submit" class="btn btn-primary btn-sm m-2"
                                            :disabled="holdSubmit ? true : false">Upload Photo</button>
                                    </form> 
                                    <p v-if="showLink && index == linkId" class="fw-regular">{{ image.name }}</p>
                                </div> -->
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
                        <td v-if="accessData[13].useracc_modifying == 1" class="align-middle p-2">
                            <div  class="d-flex gap-2 justify-content-center">
                                <button tabindex="-1" title="Balance" @click="settlement(stud)"
                                    class="btn btn-secondary btn-sm">
                                    <font-awesome-icon icon="fa-solid fa-gear" />
                                </button>
                            </div>
                        </td>
                        <td v-else class="align-middle p-2">
                            N/A
                        </td>
                    </tr>
                    <tr v-if="!preLoading && !Object.keys(student).length" style="text-transform:none">
                        <td class="p-3 text-center" colspan="6">
                            No Records Found
                        </td>
                    </tr>
                    <tr v-if="preLoading && !Object.keys(student).length" style="text-transform:none">
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
                    <button :disabled="Object.keys(student).length < 10 ? true : false" @click="paginate('next')"
                        class="btn btn-sm btn-secondary">Next</button>
                </div>
                <p class="">showing total of <span class="font-semibold">({{ studentCount }})</span> items</p>
            </div>
        </div>

        <div v-else class="overflow-auto">
            <div v-if="!billLoading" class="d-flex flex-column gap-2">
                <div class="fw-bold bg-secondary-subtle p-3">
                    Statement of Account
                </div>
                <div class="d-flex gap-2">
                    <div class="card w-100 text-start">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                Name:
                                <input class="form-control form-control-sm"
                                    :value="studentAccount.per_firstname + ' ' + studentAccount.per_middlename + ' ' + studentAccount.per_lastname + ' ' + studentAccount.per_suffixname"
                                    disabled />
                            </li>
                            <li class="list-group-item">
                                Contact:
                                <input class="form-control form-control-sm" :value="studentAccount.per_contact"
                                    disabled />
                            </li>
                            <li class="list-group-item">
                                Email:
                                <input class="form-control form-control-sm" :value="studentAccount.per_email"
                                    disabled />
                            </li>
                            <li class="list-group-item">
                                Enrollee ID:
                                <input class="form-control form-control-sm" :value="studentAccount.acs_enrid"
                                    disabled />
                            </li>
                        </ul>
                    </div>
                    <div class="card w-100 text-start">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                Degree:
                                <select class="form-control form-select-sm" disabled
                                    v-model="studentAccount.enr_program">
                                    <option v-for="(p, index) in program" :value="p.dtype_id">{{ p.dtype_desc }}
                                    </option>
                                </select>
                            </li>
                            <li class="list-group-item">
                                Course:
                                <select class="form-control form-select-sm" disabled
                                    v-model="studentAccount.enr_course">
                                    <option v-for="(c, index) in course" :value="c.prog_id">{{ c.prog_code }}</option>
                                </select>
                            </li>
                            <li class="list-group-item">
                                Year / Grade Level:
                                <select class="form-control form-select-sm" disabled
                                    v-model="studentAccount.enr_gradelvl">
                                    <option v-for="(g, index) in gradelvl" :value="g.grad_id">{{ g.grad_name }}</option>
                                </select>
                            </li>
                            <li class="list-group-item">
                                Section:
                                <select class="form-control form-select-sm" disabled
                                    v-model="studentAccount.enr_section">
                                    <option v-for="(s, index) in section" :value="s.sec_id">{{ s.sec_name }}</option>
                                </select>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div v-if="!billLoading" class="container overflow-hidden p-3">
                <div class="row gy-2 gx-2">
                    <div class="col-12">
                        <div class="p-3 border bg-body-secondary">
                            <span class="fw-bold">Subjects Enrolled</span>
                        </div>
                    </div>
                    <div v-if="!Object.keys(milestone).length && !milestoneLoading" class="p-1">
                        <div class="shadow p-3 rounded-3 text-center border fw-bold text-danger">
                            No Subjects Added
                        </div>
                    </div>
                    <div v-if="!Object.keys(milestone).length && milestoneLoading" class="p-1">
                        <div class="shadow p-3 rounded-3 text-center border fw-bold text-danger">
                            <Loader />
                        </div>
                    </div>
                    <div v-if="Object.keys(milestone).length && !milestoneLoading" class="col-12"
                        v-for="(c, index) in milestone">
                        <div class="container">
                            <div class="row">
                                <div class="col p-3 border bg-white shadow d-flex flex-column text-start">
                                    <span class="fw-bold">{{ c.subj_code }}</span>
                                    <p class="">{{ c.subj_name }}</p>
                                    <p v-if="c.mi_crossenr" class="mt-3">Cross Enrolled to: <span class=" text-red-500">
                                            {{ c.mi_crossenr }}</span></p>
                                </div>
                                <div class="col p-3 border bg-white shadow d-flex flex-column text-start">
                                    <div class="input-group mb-1">
                                        <span class="input-group-text " id="inputGroup-sizing-default">Lecture</span>
                                        <input type="text" class="form-control form-control-sm"
                                            aria-label="Sizing example input"
                                            aria-describedby="inputGroup-sizing-default" :value="c.subj_lec" disabled>
                                    </div>
                                    <div class="input-group mb-1">
                                        <span class="input-group-text" id="inputGroup-sizing-default">Laboratory</span>
                                        <input type="text" class="form-control form-control-sm"
                                            aria-label="Sizing example input"
                                            aria-describedby="inputGroup-sizing-default" :value="c.subj_lab" disabled>
                                    </div>
                                    <div class="input-group mb-1">
                                        <span class="input-group-text" id="inputGroup-sizing-default">Total</span>
                                        <input type="text" class="form-control form-control-sm"
                                            aria-label="Sizing example input"
                                            aria-describedby="inputGroup-sizing-default"
                                            :value="c.subj_lec + c.subj_lab" disabled>
                                    </div>
                                </div>
                                <div class="col p-3 border bg-white shadow d-flex flex-column text-start">
                                    <p><span class="fw-bold">Per Week: </span>{{ c.subj_hrs_week }}</p>
                                    <p v-if="c.mi_tag == 1"><span class="fw-bold">Tags: </span>
                                        Taken</p>
                                    <p v-else-if="c.mi_tag == 2"><span class="fw-bold">Tags:
                                        </span>Advance</p>
                                    <p v-else-if="c.mi_tag == 3"><span class="fw-bold">Tags:
                                        </span>Re-take / Back Subject</p>
                                    <p v-else="c.mi_tag == 3"><span class="fw-bold">Tags:
                                        </span>N/A</p>
                                    <p><span class="fw-bold">Pre-requisite: </span>{{
                                        c.subj_preq_code ? c.subj_preq_code : 'N/A' }}</p>
                                    <p><span class="fw-bold">Grade: --</span></p>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class=" d-flex justify-content-end gap-2 mt-3">
                            <div class="form-group text-start">
                                <label class="fw-bold">Total Cost</label>
                                <input class="form-control form-control-sm" :value="grandTotal" disabled />
                            </div>
                            <div class="form-group align-content-end">
                                <button type="button" data-bs-toggle="modal" data-bs-target="#settlementmodal"
                                @click="settlePayments()" class="btn btn-sm btn-dark">Add Payment</button>
                            </div>
                           
                        </div>
                    </div>
                </div>
            </div>
            <Loader v-else>
                <p class="fw-semibold">Loading Please Wait...</p>
            </Loader>
        </div>
    </div>

    <!-- Payment Modal -->
    <div class="modal fade" id="settlementmodal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Account Settlement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        @click="showPaymentModal = false"></button>
                </div>
                <div class="modal-body">
                    <AccountingPaymentModal v-if="showPaymentModal" :accountData="studentAccount" :billTypeData="1" />
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

    <!-- Dlownload Modal -->
    <div class="modal fade" id="downloadmodal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Export Data</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        @click="showDownloadModal = false"></button>
                </div>
                <div class="modal-body">
                    <AccountingDownloadModal v-if="showDownloadModal" :billTypeData="1" />
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <div class="form-group">
                        <small id="emailHelp" class="form-text text-muted">We'll never share your personal information
                            with anyone
                            else (Data Privacy Act of 2012)</small>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                            @click="showDownloadModal = false">Close</button>
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
                     <SearchQR @fetchData="getData" modeData="2"/>
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