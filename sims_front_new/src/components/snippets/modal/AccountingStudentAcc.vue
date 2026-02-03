<script setup>
import { ref, computed, onMounted, watch } from "vue"
import AccountingPaymentModal from "../modal/AccountingPaymentModal.vue"
import { getStudentAccount, getPaymentDetails, getScholarshipDetails } from "../../Fetchers.js"
import { pesoConverter,formatDateTime, pdfGenerator } from "../../Generators.js"
import Loader1 from "../loaders/Loader1.vue"

/* ───────────── props ───────────── */
const props = defineProps({
    personId: {},
    userId: {},
    modeId: {}
})

const personID = computed(() => props.personId)
const modeID = computed(() => props.modeId)

/* ───────────── state ───────────── */
const preLoading = ref(true)

const studentAccount = ref([])     // RAW billing rows (never mutate)
const studentSettlement = ref([])
const studentPayment = ref([])

const selectedAcsId = ref("")

const showPaymentModal = ref(false)

/* ───────────── totals ───────────── */
const currentDate = new Date();
const totalPayment = ref(0)
const totalLecCost = ref(0)
const totalLabCost = ref(0)
const totalItemCost = ref(0)
const totalMiscCost = ref(0)
const totalFixedDiscount = ref(0)
const totalPercentDiscount = ref(0)
const totalDiscount = ref(0)
const grandTotal = ref(0)
const totalTuition = ref(0)
const prelimAmount = ref(0)
const midtermAmount = ref(0)
const preFinalAmount = ref(0)
const finalAmount = ref(0)
const prelimBalanceAmount = ref(0)
const midtermBalanceAmount = ref(0)
const preFinalBalanceAmount = ref(0)
const finalBalanceAmount = ref(0)
const soaStatus = ref(false)
const studentSettlements = ref([])
const scholarshipDetails = ref([])
const studentAccountSubjects = ref([])
const totalTuionFeeNodeduction = ref(0)
const deductionFromSoa = ref(0)
const studentAccounts = ref([])
const selectedAccountHeader = ref('')
const filteredStudentAccount = ref([])
const filteredStudentSettlement = ref([])

/* ───────────── lifecycle ───────────── */
onMounted(async () => {
    preLoading.value = true

    const [accountRes, scholarshipRes] = await Promise.all([
        getStudentAccount(personID.value),
        getScholarshipDetails(personID.value)
    ])

    scholarshipDetails.value = scholarshipRes.joined.map(e => ({
        ...e,
        sch_total: e.sch_type == 1 ? e.sch_value / 100 : e.sch_value
    }))
    
    
    studentAccounts.value = groupByAcsIdArray(accountRes.student_account)
    studentSettlements.value = accountRes.student_settlement || []
    selectedAcsId.value = studentAccounts.value[Object.keys(studentAccounts.value).length-1].soa_acsid

    loadAccount()
    preLoading.value = false
})

const groupByAcsIdArray = (rows) => {
    const map = {}

    rows.forEach(row => {
        const key = row.soa_acsid
        if (!map[key]) map[key] = []
        map[key].push(row)
    })

    return Object.entries(map).map(([acsid, items]) => ({
        soa_acsid: Number(acsid),
        items
    }))
}


const loadAccount = async () =>{
    preLoading.value = true

    let data1 = studentSettlements.value.filter((e)=>{
        if(e.acs_id == selectedAcsId.value){
            return e
        }
    })

    filteredStudentSettlement.value = data1[0]

    let data2 = studentAccounts.value.filter((e)=>{
        if(e.soa_acsid == selectedAcsId.value){
            return e
        }
    })

    filteredStudentAccount.value = data2[0].items

    //set data for headers
    selectedAccountHeader.value = filteredStudentAccount.value[0]
    studentAccount.value = filteredStudentAccount.value
    soaStatus.value = studentAccount.value.some(i => i.soa_status === 3)

    await recomputeAccountTotals()
    preLoading.value = false
}


/* ───────────── recalc totals PER account ───────────── */
const recomputeAccountTotals = async () => {
    resetTotals()

    const rows = filteredStudentAccount.value
    if (!rows.length) return

    const payments = await getPaymentDetails(rows[0].soa_acsid, 1)
    payments.data.forEach(p => {
        totalPayment.value += Number(p.acy_payment || 0)
    })

    scholarshipDetails.value.forEach(e => {
        if (e.sch_type === 1) totalPercentDiscount.value += e.sch_total
        if (e.sch_type === 2) totalFixedDiscount.value += e.sch_total
    })

    rows.forEach(item => {
        if (item.soa_subjid) {
            totalLecCost.value += (item.soa_lec_price || 0) * (item.soa_lec || 0)
            totalLabCost.value += (item.soa_lab_price || 0) * (item.soa_lab || 0)
        } else if (item.soa_custype === 4) {
            const amount = (item.soa_price || 0) * (item.soa_quantity || 0)
            if (item.soa_disc_type === 1) {
                totalPercentDiscount.value += amount / 100
                deductionFromSoa.value += amount / 100
            } else {
                totalFixedDiscount.value += amount
                deductionFromSoa.value += amount
            }
        } else {
            const amount = (item.soa_price || 0) * (item.soa_quantity || 0)
            if (item.soa_custid && !item.soa_itemid) totalMiscCost.value += amount
            if (!item.soa_custid && item.soa_itemid) totalItemCost.value += amount
        }
    })

    const subtotal =
        totalLecCost.value +
        totalLabCost.value +
        totalItemCost.value +
        totalMiscCost.value

    totalTuionFeeNodeduction.value = subtotal
    totalDiscount.value = subtotal * totalPercentDiscount.value + totalFixedDiscount.value
    totalTuition.value = subtotal - totalDiscount.value
    grandTotal.value = totalTuition.value - totalPayment.value

    prelimAmount.value =
    midtermAmount.value =
    preFinalAmount.value =
    finalAmount.value = totalTuition.value / 4

    fillTheGaps()
    subtractPayments()
}

const fillTheGaps = () => {
    if (isNaN(totalPayment.value)) totalPayment.value = 0
    if (isNaN(totalLecCost.value)) totalLecCost.value = 0
    if (isNaN(totalLabCost.value)) totalLabCost.value = 0
    if (isNaN(totalItemCost.value)) totalItemCost.value = 0
    if (isNaN(totalMiscCost.value)) totalMiscCost.value = 0
    if (isNaN(totalFixedDiscount.value)) totalFixedDiscount.value = 0
    if (isNaN(totalPercentDiscount.value)) totalPercentDiscount.value = 0
    if (isNaN(totalDiscount.value)) totalDiscount.value = 0
    if (isNaN(grandTotal.value)) grandTotal.value = 0
    if (isNaN(totalTuition.value)) totalTuition.value = 0
    if (isNaN(prelimAmount.value)) prelimAmount.value = 0
    if (isNaN(midtermAmount.value)) midtermAmount.value = 0
    if (isNaN(preFinalAmount.value)) preFinalAmount.value = 0
    if (isNaN(finalAmount.value)) finalAmount.value = 0
}

const subtractPayments = () => {
    let remainingPayment = totalPayment.value

    let prelim_change   = Math.max(prelimAmount.value   - remainingPayment, 0)
    remainingPayment   = Math.max(remainingPayment - prelimAmount.value, 0)

    let midterm_change  = Math.max(midtermAmount.value  - remainingPayment, 0)
    remainingPayment   = Math.max(remainingPayment - midtermAmount.value, 0)

    let prefinal_change = Math.max(preFinalAmount.value - remainingPayment, 0)
    remainingPayment   = Math.max(remainingPayment - preFinalAmount.value, 0)

    let final_change    = Math.max(finalAmount.value    - remainingPayment, 0)

    prelimBalanceAmount.value = prelim_change
    midtermBalanceAmount.value = midterm_change
    preFinalBalanceAmount.value = prefinal_change
    finalBalanceAmount.value = final_change
}



/* ───────────── helpers ───────────── */
const resetTotals = () => {
    totalPayment.value = 0
    totalLecCost.value = 0
    totalLabCost.value = 0
    totalItemCost.value = 0
    totalMiscCost.value = 0
    totalFixedDiscount.value = 0
    totalPercentDiscount.value = 0
    totalDiscount.value = 0
    grandTotal.value = 0
}

const settlement = (mode) => {
    studentPayment.value = []
    let x = {
        acs_id: selectedAccountHeader.value.soa_acsid,
        per_id: selectedAccountHeader.value.soa_personid,
        enr_id: selectedAccountHeader.value.soa_enrid,
        per_firstname: selectedAccountHeader.value.per_firstname,
        per_middlename: selectedAccountHeader.value.per_middlename,
        per_lastname: selectedAccountHeader.value.per_lastname,
        per_suffixname: selectedAccountHeader.value.per_suffixname,
        acs_balance: grandTotal.value,
        acs_status:filteredStudentSettlement.value.acs_status,
        by_pass: mode
    }

    console.log(selectedAccountHeader.value)
    studentPayment.value = x

    showPaymentModal.value = !showPaymentModal.value
    if (!showPaymentModal.value) window.stop()
}


const examType = ref('')
const customClass = ref('bg-white')
const tempWaterMark = ref(false)
const permitMap = {
    1: { label: 'Prelims',    balance: prelimBalanceAmount,    className: 'prelim-permit' },
    2: { label: 'Midterms',   balance: midtermBalanceAmount,   className: 'midterm-permit' },
    3: { label: 'Pre-Finals', balance: preFinalBalanceAmount,  className: 'prefinal-permit' },
    4: { label: 'Finals',     balance: finalBalanceAmount,     className: 'final-permit' }
}

const setPermit = (mode) => {
    tempWaterMark.value = false
    const cfg = permitMap[mode]
    if (!cfg) return

    // If balance is 0 → temporary permit
    if (cfg.balance.value === 0) {
        customClass.value = cfg.className
        examType.value = cfg.label
        return
    }

    // If balance > 0 → show Swal for unpaid term
    Swal.fire({
        title: "Notice",
        text: "This Term is not fully paid, are you sure you want to generate a permit?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, Generate it!"
    }).then(result => {
        if (result.isConfirmed) {
            examType.value = cfg.label
            customClass.value = 'temporary-permit'
            tempWaterMark.value = true
        }
    })
}




const printPermit = (mode) =>{
    let name = 'test'
    // let name = personID.value + '_' + studentSem.value + '_' + studentDateEnr.value.split(" ")[0]
    Swal.fire({
        icon: "success",
        title: "Permit Generated",
        text: "Check your file manager, refreshing the page",
         confirmButtonText: "Ok, Got it!"
    }).then(async(result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: "Generating PDF...",
                text: "Please wait while we prepare your receipt.",
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            await pdfGenerator(name, 'a6', 'portrait', 0)
            setTimeout(() => {
                    Swal.close();
                    location.reload();
                }, 1000);
        }

    });
}
</script>

<template>
    <div class="p-2 h-100">
        <!-- LOADER -->
        <div v-if="preLoading" class="p-2 h-100 d-flex justify-content-center align-items-center">
            <Loader1 />
        </div>

        <div v-else>
            <!-- ACCOUNT SELECT -->
            <div class="w-100 p-3 border shadow-lg text-start mb-3" v-if="!showPaymentModal">
                <span class="fw-bold">Accounts</span>
                <select class="form-select mt-2" v-model="selectedAcsId" @change="loadAccount()">
                    <option disabled value="">Select Account Set</option>

                    <option v-for="group in studentAccounts" :key="group.soa_acsid" :value="group.soa_acsid">
                        Account Set #{{ group.soa_acsid }}
                        ({{ group.items.length }} items)
                    </option>

                </select>
            </div>

            <!-- STATEMENT -->
            <div v-if="selectedAccountHeader && !showPaymentModal" class="card border shadow-sm">
                <div class="card-header bg-dark text-white text-center">
                    <h4 class="mb-0 fw-bold">Account Breakdown</h4>
                </div>

                <div class="card-body">
                    <!-- ACCOUNT INFO -->
                    <div class="row mb-4 small">
                        <div class="col-6 text-start">
                            <p class="mb-1"><strong>Name:</strong>
                                {{ `${selectedAccountHeader.per_firstname || ''} ${selectedAccountHeader.per_middlename
                                    || ''}
                                ${selectedAccountHeader.per_lastname || ''} ${selectedAccountHeader.per_suffixname ||
                                ''}`.trim() }}
                            </p>
                            <p class="mb-1"><strong>Student No:</strong> {{ selectedAccountHeader.acs_enrid || 'N/A' }}
                            </p>
                            <p class="mb-1"><strong>Email:</strong> {{ selectedAccountHeader.per_email || 'N/A' }}</p>
                            <p class="mb-1"><strong>Semester Enrolled:</strong>
                                {{ selectedAccountHeader.quarter_desc ? selectedAccountHeader.quarter_desc : '-'
                                }}
                            </p>
                        </div>
                        <div class="col-6 text-end">
                            <p class="mb-1"><strong>Course:</strong>
                                {{ selectedAccountHeader.prog_name ? selectedAccountHeader.prog_name : 'N/A' }}
                            </p>
                            <p class="mb-1"><strong>Grade Level:</strong>
                                {{ selectedAccountHeader.gradelvl_desc ? selectedAccountHeader.gradelvl_desc : 'N/A' }}
                            </p>
                            <p class="mb-1"><strong>Section:</strong>
                                {{ selectedAccountHeader.section_name ? selectedAccountHeader.section_name : 'N/A' }}
                            </p>
                            <p class="mb-1"><strong>Date Enrolled:</strong>
                                {{ selectedAccountHeader.soa_dateenrolled ? formatDateTime(selectedAccountHeader.soa_dateenrolled) :
                                'N/A' }}
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
                                <tr v-if="!Object.keys(studentAccount).length && !preLoading">
                                    <td colspan="10" class="text-center text-danger fw-bold py-4">
                                        No Billing Items Found
                                    </td>
                                </tr>

                                <tr v-if="Object.keys(studentAccount).length && !preLoading"
                                    v-for="(c, index) in studentAccount" :key="index">

                                    <td class="text-start">
                                        <span class="fst-italic">{{ c.soa_desc }}</span>
                                        <span v-if="c.soa_subjid" class="fw-bold"> ({{ c.subj_code
                                        }})</span>
                                    </td>

                                    <td class="text-end">{{ c.soa_lec || '-' }}</td>
                                    <td class="text-end">{{ c.soa_lab || '-' }}</td>
                                    <td class="text-end">{{ c.soa_quantity || '-' }}</td>

                                    <td class="text-end">
                                        <span v-if="c.soa_subjid">
                                            {{ (c.soa_lec || 0) + (c.soa_lab || 0) }}
                                        </span>
                                        <span v-else>
                                            {{ c.soa_quantity || '-' }}
                                        </span>
                                    </td>

                                    <td class="text-end text-muted">{{ c.soa_lec_price || 0 }}</td>
                                    <td class="text-end text-muted">{{ c.soa_lab_price || 0 }}</td>
                                    <td class="text-end text-muted">
                                        <span v-if="c.soa_disc_type == 1">
                                            {{ (c.soa_price * c.soa_quantity).toFixed(2) }}%
                                        </span>
                                        <span v-else>
                                            {{ c.soa_price * c.soa_quantity }}
                                        </span>
                                    </td>

                                    <td class="text-end">
                                        <span v-if="c.soa_subjid">
                                            {{ (c.soa_lec_price || 0) + (c.soa_lab_price || 0) }}
                                        </span>
                                        <span v-else>
                                            {{ (c.soa_item_price || 0) * (c.soa_quantity || 0) }}
                                        </span>
                                    </td>

                                    <td class="text-end fw-bold">
                                        <span v-if="c.soa_subjid">
                                            {{ pesoConverter(
                                                ((c.soa_lec_price || 0) * (c.soa_lec || 0)) +
                                                ((c.soa_lab_price || 0) * (c.soa_lab || 0))
                                            ) }}
                                        </span>
                                        <span v-else>
                                            <span v-if="c.soa_disc_type == 1">
                                                - {{ (c.soa_price * c.soa_quantity).toFixed(2) }}%
                                            </span>
                                            <span v-else>
                                                {{ pesoConverter(c.soa_price * c.soa_quantity) }}
                                            </span>
                                        </span>
                                    </td>
                                </tr>
                            </tbody>

                        </table>
                    </div>

                    <!-- SUMMARY -->
                    <div class="row justify-content-center align-items-center mt-4">
                        <div :class="modeID!==2?'col-md-12 col-lg-12':'col-md-6 col-lg-8'">
                            <div class="border rounded p-3"  style="height: 450px;">
                               <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th class="text-start" colspan="6">Payment Plan</th>
                                        </tr>
                                        <tr>
                                            <th class="text-start">Term</th>
                                            <th class="text-start">Term Plan</th>
                                            <th class="text-start">Amount Paid</th>
                                            <th class="text-start">Balance</th>
                                            <th class="text-start">Status</th>
                                            <th class="text-start">Permit</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td :class="prelimBalanceAmount == 0? 'text-start bg-success-subtle text-success align-middle':'text-start align-middle'">Prelim</td>
                                            <td :class="prelimBalanceAmount == 0? 'text-start bg-success-subtle text-success align-middle':'text-start align-middle'">{{ pesoConverter(prelimAmount) }}</td>
                                            <td :class="prelimBalanceAmount == 0? 'text-start bg-success-subtle text-success align-middle':'text-start align-middle'">{{ pesoConverter(prelimAmount - prelimBalanceAmount) }}</td>
                                            <td :class="prelimBalanceAmount == 0? 'text-start bg-success-subtle text-success align-middle':'text-start align-middle'">{{ pesoConverter(prelimBalanceAmount) }}</td>
                                            <td :class="prelimBalanceAmount == 0? 'text-start bg-success-subtle text-success align-middle':'text-start align-middle'">{{ prelimBalanceAmount == 0 ? 'Paid' : 'Unpaid' }}</td>
                                            <td :class="prelimBalanceAmount == 0? 'text-start bg-success-subtle text-success align-middle':'text-start align-middle'">
                                                <button @click="setPermit(1)" class="btn btn-sm btn-dark w-100" type="button" v-if="modeID===2">Generate</button>
                                                <span v-else>{{ studentAccount[0].soa_headerid }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td :class="midtermBalanceAmount == 0? 'text-start bg-success-subtle text-success align-middle':'text-start align-middle'">Midterm</td>
                                            <td :class="midtermBalanceAmount == 0? 'text-start bg-success-subtle text-success align-middle':'text-start align-middle'">{{ pesoConverter(midtermAmount) }}</td>
                                            <td :class="midtermBalanceAmount == 0? 'text-start bg-success-subtle text-success align-middle':'text-start align-middle'">{{ pesoConverter(midtermAmount - midtermBalanceAmount) }}</td>
                                            <td :class="midtermBalanceAmount == 0? 'text-start bg-success-subtle text-success align-middle':'text-start align-middle'">{{ pesoConverter(midtermBalanceAmount) }}</td>
                                            <td :class="midtermBalanceAmount == 0? 'text-start bg-success-subtle text-success align-middle':'text-start align-middle'">{{ midtermBalanceAmount == 0 ? 'Paid' : 'Unpaid' }}</td>
                                            <td :class="midtermBalanceAmount == 0? 'text-start bg-success-subtle text-success align-middle':'text-start align-middle'">
                                                <button @click="setPermit(2)" class="btn btn-sm btn-dark w-100" type="button" v-if="modeID===2">Generate</button>
                                                <span v-else>{{ studentAccount[0].soa_headerid }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td :class="preFinalBalanceAmount == 0? 'text-start bg-success-subtle text-success align-middle':'text-start align-middle'">Pre-Final</td>
                                            <td :class="preFinalBalanceAmount == 0? 'text-start bg-success-subtle text-success align-middle':'text-start align-middle'">{{ pesoConverter(preFinalAmount) }}</td>
                                            <td :class="preFinalBalanceAmount == 0? 'text-start bg-success-subtle text-success align-middle':'text-start align-middle'">{{ pesoConverter(preFinalAmount - preFinalBalanceAmount) }}</td>
                                            <td :class="preFinalBalanceAmount == 0? 'text-start bg-success-subtle text-success align-middle':'text-start align-middle'">{{ pesoConverter(preFinalBalanceAmount) }}</td>
                                            <td :class="preFinalBalanceAmount == 0? 'text-start bg-success-subtle text-success align-middle':'text-start align-middle'">{{ preFinalBalanceAmount == 0 ? 'Paid' : 'Unpaid' }}</td>
                                            <td :class="preFinalBalanceAmount == 0? 'text-start bg-success-subtle text-success align-middle':'text-start align-middle'">
                                                <button @click="setPermit(3)" class="btn btn-sm btn-dark w-100" type="button" v-if="modeID===2">Generate</button>
                                                <span v-else>{{ studentAccount[0].soa_headerid }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td :class="finalBalanceAmount == 0? 'text-start bg-success-subtle text-success align-middle':'text-start align-middle'">Finals</td>
                                            <td :class="finalBalanceAmount == 0? 'text-start bg-success-subtle text-success align-middle':'text-start align-middle'">{{ pesoConverter(finalAmount) }}</td>
                                            <td :class="finalBalanceAmount == 0? 'text-start bg-success-subtle text-success align-middle':'text-start align-middle'">{{ pesoConverter(finalAmount - finalBalanceAmount) }}</td>
                                            <td :class="finalBalanceAmount == 0? 'text-start bg-success-subtle text-success align-middle':'text-start align-middle'">{{ pesoConverter(finalBalanceAmount) }}</td>
                                            <td :class="finalBalanceAmount == 0? 'text-start bg-success-subtle text-success align-middle':'text-start align-middle'">{{ finalBalanceAmount == 0 ? 'Paid' : 'Unpaid' }}</td>
                                            <td :class="finalBalanceAmount == 0? 'text-start bg-success-subtle text-success align-middle':'text-start align-middle'">
                                                <button @click="setPermit(4)" class="btn btn-sm btn-dark w-100" type="button" v-if="modeID===2">Generate</button>
                                                <span v-else>{{ studentAccount[0].soa_headerid }}</span>
                                            </td>
                                        </tr>
                                    </tbody>
                               </table>

                                <hr>
                               
                                <div class="d-flex justify-content-between text-primary mb-1">
                                    <span>Special Scholarship Discount</span>
                                    <span>- {{ pesoConverter(0) }}</span>
                                </div>

                                <hr>

                                <div class="d-flex justify-content-between fw-bold fs-6">
                                    <span>Total Tuition</span>
                                    <span class="text-primary">{{ pesoConverter(totalTuition) }}</span>
                                </div>
                                <div class="d-flex justify-content-between fw-bold fs-6">
                                    <span>Total Tuition Paid</span>
                                    <span class="text-success">{{ pesoConverter(totalPayment) }}</span>
                                </div>

                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4" v-if="modeID===2">
                            <div class="border rounded p-2 overflow-auto">
                                <div class="h-100 w-100 bg-secondary rounded d-flex justify-content-center position-relative">
                                    <div v-if="tempWaterMark" class="h-100 w-100 position-absolute overflow-hidden">
                                        <span class="temporary-permit-watermark">Temporary</span>
                                    </div>
                                    <div class="h-100 w-100 d-flex p-3">
                                        <div
                                            class="h-100 w-100 bg-white"
                                            id="printform"
                                            style="font-size: 10px; line-height: 1.2;"
                                        >

                                            <!-- HEADER -->
                                            <div class="text-center border-bottom p-2 fw-bold d-flex flex-column" :class="customClass">
                                                <span>STUDENT EXAMINATION PERMIT</span>
                                                <small class="fw-normal">{{studentAccount[0].soa_headerid}}</small>
                                            </div>

                                            

                                            <!-- STUDENT INFO -->
                                            <div class="p-1 border-bottom text-start text-regular mt-2">
                                                <div><strong>Name:</strong> {{ studentAccount[0].fullname }}</div>
                                                <div><strong>Course:</strong> {{ studentAccount[0].prog_name }}</div>
                                                <div><strong>Year / Section:</strong> {{ studentAccount[0].section_name }}</div>
                                                <div><strong>Term:</strong> {{ studentAccount[0].quarter_desc }}</div>
                                            </div>

                                            <!-- EXAM DETAILS -->
                                            <div class="p-1 border-bottom d-flex justify-content-between mt-2">
                                                <div><strong>Exam Type:</strong> {{ examType ? examType : 'N/A' }}</div>
                                                <div><strong>Date Issued:</strong> {{ formatDateTime(currentDate) }}</div>
                                            </div>

                                            <!-- SUBJECTS -->
                                            <div class="p-1 border-bottom mt-1">
                                                <div class="fw-bold text-centerx">Subjects Cleared</div>
                                                <table class="w-100 border mt-1 mb-1" style="font-size: 10px;">
                                                    <thead>
                                                        <tr>
                                                            <th class="borders px-1">Code</th>
                                                            <th class="border px-1">Description</th>
                                                            <th class="border px-1">Signature</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr v-for="sub in studentAccountSubjects" :key="sub.soa_subjid">
                                                            <td class="border px-1">{{ sub.soa_subjcode }}</td>
                                                            <td class="border px-1">{{ sub.soa_desc }}</td>
                                                            <td class="border px-1"></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>

                                            <!-- FINANCIAL CLEARANCE -->
                                            <div class="p-1 text-end mt-2">
                                                <!-- <div>Total Payments: {{ pesoConverter(totalPayment) }}</div> -->
                                                <!-- <div>
                                                    Balance:
                                                    <span :class="grandTotal > 0 ? 'text-danger' : 'text-success'">
                                                        {{ pesoConverter(grandTotal) }}
                                                    </span>
                                                </div> -->
                                                <div v-if="examType == 'Prelims'">
                                                    Payment Status:
                                                        <span class="text-success" v-if="prelimBalanceAmount == 0">Paid</span>
                                                        <span class="text-danger" v-else>Not Paid</span>
                                                </div>
                                                <div v-if="examType == 'Midterms'">
                                                    Payment Status:
                                                        <span class="text-success" v-if="midtermBalanceAmount == 0">Paid</span>
                                                        <span class="text-danger" v-else>Not Paid</span>
                                                </div>
                                                <div v-if="examType == 'Pre-Finals'">
                                                    Payment Status:
                                                        <span class="text-success" v-if="preFinalBalanceAmount == 0">Paid</span>
                                                        <span class="text-danger" v-else>Not Paid</span>
                                                </div>
                                                <div v-if="examType == 'Finals'">
                                                    Payment Status:
                                                        <span class="text-success" v-if="finalBalanceAmount == 0">Paid</span>
                                                        <span class="text-danger" v-else>Not Paid</span>
                                                </div>
                                            </div>

                                            <!-- CONDITIONS -->
                                            <div class="p-1 border-bottom text-start mt-2" style="font-size: 10px;">
                                                <div class="fw-bold text-center">CONDITIONS</div>
                                                <ol class="ps-3 mb-1">
                                                    <li>This permit must be presented to the proctor before taking any examination.</li>
                                                    <li>No permit, no exam policy strictly enforced.</li>
                                                    <li>Alterations or erasures on this permit will render it <strong>INVALID</strong>.</li>
                                                    <li>This permit is valid only for the examination period indicated.</li>
                                                </ol>
                                            </div>

                                            <!-- AUTHORIZATION -->
                                            <div class="p-1 text-center mt-2" style="font-size: 10px;">
                                                <div class="fw-bold mb-1">AUTHORIZATION</div>
                                                <div class="d-flex justify-content-between gap-1 w-100">
                                                    <div class="text-center">
                                                        Prepared By<br/>
                                                        __________________________<br/>
                                                        Signature / Date
                                                    </div>
                                                    <div class="text-center">
                                                        Approved By<br/>
                                                        __________________________<br/>
                                                        Signature / Date
                                                    </div>
                                                </div>

                                                <div class="mt-2">
                                                    Official Seal / Stamp
                                                </div>

                                                <div class="mt-1 fst-italic">
                                                    This document is system-generated and valid without signature if electronically issued.
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                    <!-- ACTION BUTTON -->
                                    <div class="position-absolute bg-dark w-100 bg-opacity-75 p-2 bottom-0 text-center">
                                        <button v-if="examType"
                                            @click="printPermit()"
                                            class="btn btn-sm btn-success"
                                            type="button"
                                            :disabled="!examType"
                                        >
                                            Download Permit
                                        </button>
                                        <small v-else class="text-white">-- Please Select Term First -- </small>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>

                    <!-- SUMMARY -->
                    <div class="row justify-content-end mt-4">
                        <div class="col-md-12 col-lg-12">
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
                                    <span>Item/Miscellaneous Fees</span>
                                    <span>{{ pesoConverter(totalItemCost) }}</span>
                                </div>

                                <div class="d-flex justify-content-between mb-1">
                                    <span>Other Charges</span>
                                    <span>{{ pesoConverter(totalMiscCost) }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span>Total Tuition</span>
                                    <span>{{ pesoConverter(totalTuionFeeNodeduction) }}</span>
                                </div>
                                <hr>

                                <div class="d-flex justify-content-between text-danger mb-1">
                                    <span>Deductions / Discounts</span>
                                    <span>- {{ pesoConverter(deductionFromSoa) }}</span>
                                </div>

                                <div class="d-flex justify-content-between text-danger mb-1">
                                    <span>Scholarships / Vouchers</span>
                                    <div class="text-end">
                                        <div v-if="!Object.keys(scholarshipDetails).length">
                                             {{ pesoConverter(0) }}
                                        </div>
                                        <div v-else v-for="(sch, index) in scholarshipDetails" :key="index" class="ms-2">
                                            <span>
                                                - {{ sch.sch_description }}:
                                                <template v-if="sch.sch_type == 1">
                                                    {{ sch.sch_value }}% ({{ (totalTuionFeeNodeduction * (sch.sch_value / 100)).toFixed(2) }})
                                                </template>
                                                <template v-else-if="sch.sch_type == 2">
                                                    {{ pesoConverter(sch.sch_total) }}
                                                </template>
                                                <template v-else>
                                                    {{ pesoConverter(0) }}
                                                </template>
                                            </span>
                                        </div>
                                    </div>
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

                                <div class="d-flex flex-column justify-content-end fw-bold fs-5 mt-3 p-2" v-if="grandTotal > 0">
                                    <!-- Alert if the account is not settled -->
                                    <div v-if="soaStatus" class="alert alert-warning w-100" role="alert">
                                        <span class="small-font">Account Not Settled!</span><br/>
                                        <span class="small-font fw-medium">
                                        This student was dropped during academic days and has a remaining balance!
                                        </span>
                                    </div>

                                    <div class="d-flex justify-content-end">
                                        <!-- Add Payment button only in mode 2 -->
                                        <button v-if="modeID === 1 " type="button" @click="settlement(true)"
                                            class="btn btn-sm btn-dark">
                                            Add By Pass Payment
                                        </button>
                                    </div>

                                    <div class="d-flex justify-content-end">
                                        <!-- Add Payment button only in mode 2 -->
                                        <button v-if="modeID === 2" type="button" @click="settlement(false)"
                                            class="btn btn-sm btn-dark">
                                            Add Payment
                                        </button>
                                    </div>
                                </div>

                                <div class="d-flex flex-column justify-content-end fw-bold mt-3 bg-success-subtle p-3" v-else>
                                    <span class="text-success">Account is settled on {{ formatDateTime(filteredStudentSettlement.acs_dateupdated) }}</span>
                                     <!-- Add Payment button only in mode 2 -->
                                    <div>
                                        <button type="button" @click="settlement(false)"
                                            class="btn btn-sm btn-dark mt-2">
                                            View Payment Details
                                        </button>
                                    </div>
                                </div>

                            </div>

                            <!-- <div class="d-flex justify-content-end fw-bold fs-5 mt-2">
                            <button type="button" data-bs-toggle="modal" data-bs-target="#settlementmodal"
                                @click="settlePayments()" class="btn btn-sm btn-dark">Add Payment</button>
                        </div> -->
                        </div>
                    </div>

                    <!-- FOOTER -->
                    <p class="text-center small mt-4">
                        This is a system-generated statement and is valid without signature.
                    </p>
                </div>
            </div>

            <!-- PAYMENT -->
            <div v-if="showPaymentModal" class="card border shadow-sm">
                <div class="card-header bg-dark text-white text-center">
                    <h4 class="mb-0 fw-bold">Account Settlement</h4>
                </div>

                <div class="card-body">
                    <div class="w-100 d-flex justify-content-end">
                         <button class="btn btn-sm btn-primary mb-3" @click="settlement">
                            Return to Statement
                        </button>
                    </div>
                    <AccountingPaymentModal :accountData="studentPayment" :billTypeData="1" />
                </div>
            </div>
        </div>
    </div>
</template>
