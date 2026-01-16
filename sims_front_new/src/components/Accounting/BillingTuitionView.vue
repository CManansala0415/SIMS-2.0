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
    getStudentFiltering,
    getPaymentDetails,
    getChargesTemplateHeader,


} from "../Fetchers.js";

import { pesoConverter } from '../Generators.js'
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
const emit = defineEmits(['fetchUser', 'doneLoading'])
const accessData = ref([])
const templatePricesData = ref([])
const totalCost = ref(0)
const totalItemCost = ref(0)
const totalSubjCost = ref(0)
const totalLecCost = ref(0)
const totalLabCost = ref(0)
const totalExactDiscount = ref(0)
const totalPercentDiscount = ref(0)
const totalPayment = ref(0)
const templateArrayItems = ref(0)
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
    getUserID().then(async (results1) => {
        userID.value = results1.account.data.id
        accessData.value = results1.access
        emit('fetchUser', results1)
        try {
            preLoading.value = true
            await booter().then((results) => {

                booting.value = 'Loading Students...'
                bootingCount.value += 1
                getStudentFiltering(limit.value, offset.value, searchFname.value, searchMname.value, searchLname.value, paramsProgram.value, paramsGradelvl.value, paramsCourse.value, 1).then((results) => {
                    student.value = results.data
                    studentCount.value = results.count
                    preLoading.value = false
                    emit('doneLoading', false)
                    // console.log(accounts.value)
                    // let x = student.value.map((e) => {
                    //     let y = accounts.value.findIndex((f) => {
                    //         return f.acs_enrid === e.enr_id
                    //     })
                    //     return {
                    //         ...e,
                    //         ...accounts.value[y],
                    //     }
                    // })

                    // studentAccount.value = x
                    // console.log(studentAccount.value)

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
        // router.push("/");
        // window.stop()
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
    templatePricesData.value = []
    //get account first
    let a = student.value.map((e) => {
        let b = accounts.value.findIndex((f) => {
            return f.acs_enrid === e.enr_id
        })
        return {
            ...e,
            ...accounts.value[b],
        }
    })

    studentAccount.value = a

    // get latest balance
    try {

        getPaymentDetails(studentAccount.value[0].acs_id, 1).then((results) => {
            let x = results.data.slice(-1).pop()
            grandTotal.value = typeof x !== 'undefined' ? x.acy_balance : studentAccount.value[0].acs_amount
            // console.log(x)
            results.data.forEach((e) => {
                totalPayment.value += Number(e.acy_payment)
            })
        })

        getChargesTemplateHeader(stud.enr_curriculum, stud.enr_quarter, stud.enr_program, stud.enr_course, stud.enr_gradelvl, stud.enr_section).then((results) => {
            // console.log(results.template)

            for (const key in results.template) {
                templatePricesData.value.push({
                    ...results.template[key].data
                })
            }
        })

    } catch (err) {
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



    let x = accounts.value.filter((e) => {
        if (e.acs_personid == stud.enr_personid) {
            return {
                e,
            }
        }
    })



    getMilestone(stud.enr_id).then((results) => {

        /* ----------------------------------------
         * RESET ALL TOTALS (VERY IMPORTANT)
         * ---------------------------------------- */
        billLoading.value = false

        grandTotal.value = 0
        totalCost.value = 0
        totalItemCost.value = 0
        totalExactDiscount.value = 0
        totalSubjCost.value = 0
        totalLecCost.value = 0
        totalLabCost.value = 0

        milestone.value = results

        /* ----------------------------------------
         * PREP DATA
         * ---------------------------------------- */
        const templateArray = Object.values(templatePricesData.value[0] || {})
        let mergedData = []

        /* ----------------------------------------
         * SUBJECT COMPUTATION
         * ---------------------------------------- */
        milestone.value.forEach(ms => {

            let template = templateArray.find(tp =>
                Number(tp.tuitemp_subjid) === Number(ms.mi_subjid)
            )

            let lecAmount = 0
            let labAmount = 0
            let totalPrice = 0

            if (template) {
                lecAmount = (template.tuitemp_lec_price || 0) * (template.tuitemp_lec || 0)
                labAmount = (template.tuitemp_lab_price || 0) * (template.tuitemp_lab || 0)
                totalPrice = lecAmount + labAmount
            } else {
                lecAmount = (ms.subj_lec_units_rate || 0) * (ms.subj_lec_units || 0)
                labAmount = (ms.subj_lab_units_rate || 0) * (ms.subj_lab_units || 0)
                totalPrice = lecAmount + labAmount
            }

            // Track totals
            totalSubjCost.value += totalPrice
            totalLecCost.value += lecAmount
            totalLabCost.value += labAmount

            // Only billable subjects
            if (Number(ms.mi_tag) !== 1) {
                totalCost.value += totalPrice
            }

            mergedData.push({
                ...ms,
                ...(template || {}),
                lec_amount: lecAmount,
                lab_amount: labAmount,
                total_price: totalPrice
            })
        })

        /* ----------------------------------------
         * ITEMS & MISC CHARGES
         * ---------------------------------------- */
        templateArray.forEach(tp => {
            if (
                tp.tuitemp_subjid == null &&
                (Number(tp.tuitemp_custype) === 3 || tp.tuitemp_custid == null)
            ) {
                const amount = Number(tp.tuitemp_price || 0) * Number(tp.tuitemp_quantity || 0)
                totalCost.value += amount
                totalItemCost.value += amount
            }
        })

        /* ----------------------------------------
         * DISCOUNTS (APPLIED LAST)
         * ---------------------------------------- */
        let discount = 0
        const discountBase = totalCost.value // freeze base

        templateArray.forEach(tp => {
            if (tp.tuitemp_subjid == null && Number(tp.tuitemp_custype) === 4) {

                if (tp.tuitemp_disc_type == 1) {
                    // Percent discount
                    const percentDiscount = discountBase * (Number(tp.tuitemp_price) / 100)
                    discount += percentDiscount
                    totalExactDiscount.value += percentDiscount
                } else {
                    // Fixed discount
                    const fixedDiscount =
                        Number(tp.tuitemp_price || 0) * Number(tp.tuitemp_quantity || 0)
                    discount += fixedDiscount
                    totalExactDiscount.value += fixedDiscount
                }
            }
        })

        /* ----------------------------------------
         * FINAL TOTALS
         * ---------------------------------------- */
        totalCost.value = Math.max(0, totalCost.value - discount)
        grandTotal.value = totalCost.value

        templatePricesData.value = mergedData
        templateArrayItems.value = templateArray

        /* ----------------------------------------
         * STUDENT ACCOUNT
         * ---------------------------------------- */
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
const getData = (result) => {
    console.log(result)
    student.value = result
    showQRScanner.value = !showQRScanner
    document.getElementById('hideqrscanner').click();
}

</script>

<template>
    <div class="small-font">
        <div class="p-3 mb-4 border-bottom">
            <h5 class=" text-uppercase fw-bold">Student Scholarship</h5>
        </div>

        <div v-if="!balance" class="p-1 d-flex gap-2 justify-content-between mb-3">
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
                <button @click="showQRScanner = true" data-bs-toggle="modal" data-bs-target="#scanqrmodal" type="button"
                    class="btn btn-sm btn-dark text-white w-100" tabindex="-1" :disabled="preLoading ? true : false">
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
                <button tabindex="-1" @click="balance = false" type="button" class="btn btn-sm btn-primary"
                    :disabled="preLoading || billLoading ? true : false">
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
                            {{ stud.acs_amount > 0 ? 'Yes' : 'No' }}
                        </td>
                        <td v-if="accessData[18].useracc_modifying == 1" class="align-middle p-2">
                            <div class="d-flex gap-2 justify-content-center">
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
            <div v-if="!billLoading" class="container overflow-hidden bg-secondary-subtle">
                <div class="container my-4 print-area">

                    <!-- Print Button -->
                    <div class="d-print-none mb-3 text-end">
                        <button onclick="window.print()" class="btn btn-dark">
                            🖨 Print Statement
                        </button>
                    </div>

                    <div class="card border shadow-sm">

                        <!-- HEADER -->
                        <div class="card-header bg-dark text-white py-3 print-header">
                            <div class="text-center">
                                <h4 class="mb-0 fw-bold">STATEMENT OF ACCOUNT</h4>
                                <small class="opacity-75">Academic Billing Summary</small>
                            </div>
                        </div>

                        <div class="card-body">

                            <!-- ACCOUNT INFO -->
                            <div class="row mb-4 small">
                                <div class="col-6 text-start">
                                    <p class="mb-1"><strong>Name:</strong>
                                        {{ `${studentAccount.per_firstname || ''} ${studentAccount.per_middlename || ''}
                                        ${studentAccount.per_lastname || ''} ${studentAccount.per_suffixname ||
                                        ''}`.trim() }}
                                    </p>
                                    <p class="mb-1"><strong>Student No:</strong> {{ studentAccount.acs_enrid || 'N/A' }}
                                    </p>
                                    <p class="mb-1"><strong>Email:</strong> {{ studentAccount.per_email || 'N/A' }}</p>
                                </div>
                                <div class="col-6 text-end">
                                    <p class="mb-1"><strong>Course:</strong>
                                        {{course.find(c => c.prog_id === studentAccount.enr_course)?.prog_code || '—'
                                        }}
                                    </p>
                                    <p class="mb-1"><strong>Grade Level:</strong>
                                        {{gradelvl.find(g => g.grad_id === studentAccount.enr_gradelvl)?.grad_name ||
                                        '—' }}
                                    </p>
                                    <p class="mb-1"><strong>Section:</strong>
                                        {{section.find(s => s.sec_id === studentAccount.enr_section)?.sec_name || '—'
                                        }}
                                    </p>
                                </div>
                            </div>

                            <!-- BREAKDOWN TABLE -->
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle soa-table">

                                    <thead class="table-light">
                                        <tr>
                                            <th>Description</th>
                                            <th class="text-end">Lec</th>
                                            <th class="text-end">Lab</th>
                                            <th class="text-end">Qty</th>
                                            <th class="text-end">Units</th>
                                            <th class="text-end text-muted">Lec Rate</th>
                                            <th class="text-end text-muted">Lab Rate</th>
                                            <th class="text-end text-muted">Item</th>
                                            <th class="text-end">Line Total</th>
                                            <th class="text-end fw-bold">Amount (₱)</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <tr v-if="!Object.keys(templateArrayItems).length && !milestoneLoading">
                                            <td colspan="10" class="text-center text-danger fw-bold py-4">
                                                No Billing Items Found
                                            </td>
                                        </tr>

                                        <tr v-if="Object.keys(templateArrayItems).length && !milestoneLoading"
                                            v-for="(c, index) in templateArrayItems" :key="index">

                                            <td class="text-start">
                                                <span class="fst-italic">{{ c.tuitemp_desc }}</span>
                                                <span v-if="c.tuitemp_subjid" class="fw-bold"> ({{ c.subj_code
                                                    }})</span>
                                            </td>

                                            <td class="text-end">{{ c.tuitemp_lec || '-' }}</td>
                                            <td class="text-end">{{ c.tuitemp_lab || '-' }}</td>
                                            <td class="text-end">{{ c.tuitemp_quantity || '-' }}</td>

                                            <td class="text-end">
                                                <span v-if="c.tuitemp_subjid">
                                                    {{ (c.tuitemp_lec || 0) + (c.tuitemp_lab || 0) }}
                                                </span>
                                                <span v-else>
                                                    {{ c.tuitemp_quantity || '-' }}
                                                </span>
                                            </td>

                                            <td class="text-end text-muted">{{ c.tuitemp_lec_price || 0 }}</td>
                                            <td class="text-end text-muted">{{ c.tuitemp_lab_price || 0 }}</td>
                                            <td class="text-end text-muted">
                                                <span v-if="c.tuitemp_disc_type == 1">
                                                    {{ (c.tuitemp_price * c.tuitemp_quantity).toFixed(2) }}%
                                                </span>
                                                <span v-else>
                                                    {{c.tuitemp_price * c.tuitemp_quantity}}
                                                </span>
                                            </td>

                                            <td class="text-end">
                                                <span v-if="c.tuitemp_subjid">
                                                    {{ (c.tuitemp_lec_price || 0) + (c.tuitemp_lab_price || 0) }}
                                                </span>
                                                <span v-else>
                                                    {{ (c.tuitemp_item_price || 0) * (c.tuitemp_quantity || 0) }}
                                                </span>
                                            </td>

                                            <td class="text-end fw-bold">
                                                <span v-if="c.tuitemp_subjid">
                                                    {{ pesoConverter(
                                                        ((c.tuitemp_lec_price || 0) * (c.tuitemp_lec || 0)) +
                                                        ((c.tuitemp_lab_price || 0) * (c.tuitemp_lab || 0))
                                                    ) }}
                                                </span>
                                                <span v-else>
                                                    <span v-if="c.tuitemp_disc_type == 1">
                                                        - {{ (c.tuitemp_price * c.tuitemp_quantity).toFixed(2) }}%
                                                    </span>
                                                    <span v-else>
                                                        {{ pesoConverter(c.tuitemp_price * c.tuitemp_quantity) }}
                                                    </span>
                                                </span>
                                            </td>
                                        </tr>
                                    </tbody>

                                </table>
                            </div>

                            <!-- SUMMARY -->
                            <div class="row justify-content-end mt-4">
                                <div class="col-md-6 col-lg-5">
                                    <div class="border rounded p-3">

                                        <div class="d-flex justify-content-between mb-1">
                                            <span>Lecture Cost</span>
                                            <span>{{ pesoConverter(totalLecCost) }}</span>
                                        </div>

                                        <div class="d-flex justify-content-between mb-1">
                                            <span>Laboratory Cost</span>
                                            <span>{{ pesoConverter(totalLabCost) }}</span>
                                        </div>

                                        <div class="d-flex justify-content-between mb-1">
                                            <span>Other Charges</span>
                                            <span>{{ pesoConverter(totalItemCost) }}</span>
                                        </div>

                                        <hr>

                                        <div class="d-flex justify-content-between text-danger mb-1">
                                            <span>Deductions / Discounts</span>
                                            <span>- {{ pesoConverter(totalExactDiscount) }}</span>
                                        </div>

                                        <div class="d-flex justify-content-between text-danger mb-1">
                                            <span>Payments</span>
                                            <span>- {{ pesoConverter(totalPayment) }}</span>
                                        </div>

                                        <hr>

                                        <div class="d-flex justify-content-between fw-bold fs-5">
                                            <span>Remaining Balance</span>
                                            <span class="text-success">{{ pesoConverter(grandTotal) }}</span>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-end fw-bold fs-5 mt-2">
                                        <button type="button" data-bs-toggle="modal" data-bs-target="#settlementmodal"
                                            @click="settlePayments()" class="btn btn-sm btn-dark">Add Payment</button>
                                    </div>
                                </div>
                            </div>

                            <!-- FOOTER -->
                            <p class="text-center small mt-4">
                                This is a system-generated statement and is valid without signature.
                            </p>

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

    <!-- Download Modal -->
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
                    <SearchQR v-if="showQRScanner" @fetchData="getData" modeData="2" />
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