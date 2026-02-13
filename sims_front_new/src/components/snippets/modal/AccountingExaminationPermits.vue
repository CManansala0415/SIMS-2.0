<script setup>
import { ref, computed, onMounted, watch } from "vue"
import AccountingPaymentModal from "../modal/AccountingPaymentModal.vue"
import { getStudentAccount, getPaymentDetails, getScholarshipDetails } from "../../Fetchers.js"
import { pesoConverter,formatDateTime } from "../../Generators.js"
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
const settlementAccount = ref([])
const scholarshipDetails = ref([])
const totalTuionFeeNodeduction = ref(0)
const deductionFromSoa = ref(0)
/* ───────────── lifecycle ───────────── */
onMounted(async () => {
    const results = await getStudentAccount(personID.value)

    // get scholarship first then loop i add na sa totalPercentDiscount bago mag main compute
    await getScholarshipDetails(personID.value).then((res)=>{

        scholarshipDetails.value = res.joined.map((e)=>{
            return {
                ...e,
                sch_total: e.sch_type == 1? e.sch_value / 100:e.sch_value
            }
        })

        studentAccount.value = results.student_account || []
        studentSettlement.value = results.student_settlement || []

        // get the status of the latest account
        settlementAccount.value = studentSettlement.value.slice(-1).pop()

        // if true means hindi dropped si student
        for (const item of studentAccount.value) {
            if (item.soa_status !== 0) {
                soaStatus.value = true;
                break;
            }
        }

        preLoading.value = false
    })
})

/* ───────────── group accounts (UI only) ───────────── */
const groupedAccounts = computed(() => {
    const map = {}

    studentAccount.value.forEach(item => {
        const key = item.soa_acsid
        if (!map[key]) {
            map[key] = {
                soa_acsid: key,
                items: []
            }
        }
        map[key].items.push(item)
    })

    return Object.values(map)
})

/* auto-select when only one account exists */
watch(groupedAccounts, groups => {
    if (groups.length === 1) {
        selectedAcsId.value = groups[0].soa_acsid
    }
})

/* ───────────── filtered account (RAW rows) ───────────── */
const filteredStudentAccount = computed(() => {
    if (!selectedAcsId.value) return []

    return studentAccount.value.filter(
        item => String(item.soa_acsid) === String(selectedAcsId.value)
    )
})

/* header row for selected account */
const selectedAccountHeader = computed(() => {
    return filteredStudentAccount.value[0] || null
})

/* ───────────── recalc totals PER account ───────────── */
watch(filteredStudentAccount, async (rows) => {
    resetTotals()

    if (!rows.length) return

    /* payments */
    const payments = await getPaymentDetails(rows[0].soa_acsid, 1)
    payments.data.forEach(p => {
        totalPayment.value += Number(p.acy_payment || 0)
    })

    /* scholarships */    
    scholarshipDetails.value.forEach((e)=>{
        totalPercentDiscount.value += e.sch_type == 1? e.sch_total:null
        totalFixedDiscount.value += e.sch_type == 2? e.sch_total:null
    })

    /* charges */
    rows.forEach(item => {
        if (item.soa_subjid) {
            totalLecCost.value += (item.soa_lec_price || 0) * (item.soa_lec || 0)
            totalLabCost.value += (item.soa_lab_price || 0) * (item.soa_lab || 0)
        }
        else if (item.soa_custype === 4) {
            const amount = (item.soa_price || 0) * (item.soa_quantity || 0)
            if (item.soa_disc_type === 1) {
                totalPercentDiscount.value += amount / 100
                deductionFromSoa.value += amount / 100 // for viewing na separate yung deductions from soa vs scholarship
            } else {
                totalFixedDiscount.value += amount
                deductionFromSoa.value += amount
            }
        }
        else {
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

    totalDiscount.value =
        subtotal * totalPercentDiscount.value + totalFixedDiscount.value

    grandTotal.value =
        subtotal - (totalDiscount.value + totalPayment.value)

    totalTuition.value = subtotal - totalDiscount.value

    prelimAmount.value = totalTuition.value / 4
    midtermAmount.value = totalTuition.value / 4
    preFinalAmount.value = totalTuition.value / 4
    finalAmount.value = totalTuition.value / 4

    fillTheGaps()
    subtractPayments()
})

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

const settlement = () => {
    studentPayment.value = []
    let x = {
        acs_id: selectedAccountHeader.value.soa_acsid,
        per_id: selectedAccountHeader.value.soa_personidd,
        enr_id: selectedAccountHeader.value.acs_enrid,
        per_firstname: selectedAccountHeader.value.per_firstname,
        per_middlename: selectedAccountHeader.value.per_middlename,
        per_lastname: selectedAccountHeader.value.per_lastname,
        per_suffixname: selectedAccountHeader.value.per_suffixname,
        acs_balance: grandTotal.value,
        acs_status:settlementAccount.value.acs_status
    }

    // console.log(settlementAccount.value)
    studentPayment.value = x

    showPaymentModal.value = !showPaymentModal.value
    if (!showPaymentModal.value) window.stop()
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
                <select class="form-select mt-2" v-model="selectedAcsId">
                    <option disabled value="">Select Account Set</option>

                    <option v-for="group in groupedAccounts" :key="group.soa_acsid" :value="group.soa_acsid">
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
                            <p class="mb-1"><strong>Date Enrolled:</strong>
                                {{ selectedAccountHeader.soa_dateupdated ? selectedAccountHeader.soa_dateupdated : '-'
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
                                {{ selectedAccountHeader.soa_dateenrolled ? selectedAccountHeader.soa_dateenrolled :
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
                    <div class="row justify-content-end mt-4">
                        <div class="col-md-6 col-lg-6">
                            <div class="border rounded p-3">

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
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td :class="prelimBalanceAmount == 0? 'text-start bg-success-subtle text-success':'text-start'">Prelim</td>
                                            <td :class="prelimBalanceAmount == 0? 'text-start bg-success-subtle text-success':'text-start'">{{ pesoConverter(prelimAmount) }}</td>
                                            <td :class="prelimBalanceAmount == 0? 'text-start bg-success-subtle text-success':'text-start'">{{ pesoConverter(prelimAmount - prelimBalanceAmount) }}</td>
                                            <td :class="prelimBalanceAmount == 0? 'text-start bg-success-subtle text-success':'text-start'">{{ pesoConverter(prelimBalanceAmount) }}</td>
                                            <td :class="prelimBalanceAmount == 0? 'text-start bg-success-subtle text-success':'text-start'">{{ prelimBalanceAmount == 0 ? 'Paid' : 'Unpaid' }}</td>
                                        </tr>
                                        <tr>
                                            <td :class="midtermBalanceAmount == 0? 'text-start bg-success-subtle text-success':'text-start'">Midterm</td>
                                            <td :class="midtermBalanceAmount == 0? 'text-start bg-success-subtle text-success':'text-start'">{{ pesoConverter(midtermAmount) }}</td>
                                            <td :class="midtermBalanceAmount == 0? 'text-start bg-success-subtle text-success':'text-start'">{{ pesoConverter(midtermAmount - midtermBalanceAmount) }}</td>
                                            <td :class="midtermBalanceAmount == 0? 'text-start bg-success-subtle text-success':'text-start'">{{ pesoConverter(midtermBalanceAmount) }}</td>
                                            <td :class="midtermBalanceAmount == 0? 'text-start bg-success-subtle text-success':'text-start'">{{ midtermBalanceAmount == 0 ? 'Paid' : 'Unpaid' }}</td>
                                        </tr>
                                        <tr>
                                            <td :class="preFinalBalanceAmount == 0? 'text-start bg-success-subtle text-success':'text-start'">Pre-Final</td>
                                            <td :class="preFinalBalanceAmount == 0? 'text-start bg-success-subtle text-success':'text-start'">{{ pesoConverter(preFinalAmount) }}</td>
                                            <td :class="preFinalBalanceAmount == 0? 'text-start bg-success-subtle text-success':'text-start'">{{ pesoConverter(preFinalAmount - preFinalBalanceAmount) }}</td>
                                            <td :class="preFinalBalanceAmount == 0? 'text-start bg-success-subtle text-success':'text-start'">{{ pesoConverter(preFinalBalanceAmount) }}</td>
                                            <td :class="preFinalBalanceAmount == 0? 'text-start bg-success-subtle text-success':'text-start'">{{ preFinalBalanceAmount == 0 ? 'Paid' : 'Unpaid' }}</td>
                                        </tr>
                                        <tr>
                                            <td :class="finalBalanceAmount == 0? 'text-start bg-success-subtle text-success':'text-start'">Finals</td>
                                            <td :class="finalBalanceAmount == 0? 'text-start bg-success-subtle text-success':'text-start'">{{ pesoConverter(finalAmount) }}</td>
                                            <td :class="finalBalanceAmount == 0? 'text-start bg-success-subtle text-success':'text-start'">{{ pesoConverter(finalAmount - finalBalanceAmount) }}</td>
                                            <td :class="finalBalanceAmount == 0? 'text-start bg-success-subtle text-success':'text-start'">{{ pesoConverter(finalBalanceAmount) }}</td>
                                            <td :class="finalBalanceAmount == 0? 'text-start bg-success-subtle text-success':'text-start'">{{ finalBalanceAmount == 0 ? 'Paid' : 'Unpaid' }}</td>
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
                        <div class="col-md-6 col-lg-6">
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
                                    <div v-if="!soaStatus" class="alert alert-warning w-100" role="alert">
                                        <span class="small-font">Account Not Settled!</span><br/>
                                        <span class="small-font fw-medium">
                                        This student was dropped during academic days and has a remaining balance!
                                        </span>
                                    </div>

                                    <!-- Add Payment button only in mode 2 -->
                                    <button v-if="modeID === 2" type="button" @click="settlement()"
                                        class="btn btn-sm btn-dark">
                                        Add Payment
                                    </button>
                                </div>

                                <div class="d-flex flex-column justify-content-end fw-bold mt-3 bg-success-subtle p-2" v-else>
                                    <span class="text-success">Account is settled on {{ formatDateTime(settlementAccount.acs_dateupdated) }}</span>
                                     <!-- Add Payment button only in mode 2 -->
                                    <button v-if="modeID === 2" type="button" @click="settlement()"
                                        class="btn btn-sm btn-dark mt-2">
                                        View Payment Details
                                    </button>
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
