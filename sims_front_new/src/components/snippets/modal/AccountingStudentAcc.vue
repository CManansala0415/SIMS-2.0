<script setup>
import { ref, onMounted, computed } from 'vue';
import { getUserID } from "../../../routes/user";
import {
    getStudentAccount,
    getPaymentDetails
} from "../../Fetchers.js";
import {
    pesoConverter
} from "../../Generators.js";
import { useRouter, useRoute } from 'vue-router';
const router = useRouter();

const props = defineProps({
    personId: {
    },
    userId: {
    }
})
const personID = computed(() => {
    return props.personId
});

const userID = computed(() => {
    return props.userId
});


const preLoading = ref(true);
const studentSettlement = ref([]);
const studentAccount = ref([]);
const totalCost = ref(0)
const totalItemCost = ref(0)
const totalMiscCost = ref(0)
const totalSubjCost = ref(0)
const totalLecCost = ref(0)
const totalLabCost = ref(0)
const totalFixedDiscount = ref(0)
const totalPercentDiscount = ref(0)
const totalDiscount = ref(0)
const totalPayment = ref(0)
const grandTotal = ref(0)

onMounted(() => {
    getStudentAccount(personID.value).then((results) => {
        studentSettlement.value = results.student_settlement;
        studentAccount.value = results.student_account;
        preLoading.value = false;
        

        getPaymentDetails(studentAccount.value[0].soa_acsid, 1).then((results) => {
            let x = results.data.slice(-1).pop()
            // grandTotal.value = typeof x !== 'undefined' ? x.acy_balance : studentAccount.value[0].acs_amount
            results.data.forEach((e) => {
                totalPayment.value += Number(e.acy_payment)
            })
        })

        console.log(totalPayment.value)


        studentAccount.value.forEach((item) => {
            if (item.soa_subjid) {
                totalLecCost.value += (item.soa_lec_price || 0) * (item.soa_lec || 0);
                totalLabCost.value += (item.soa_lab_price || 0) * (item.soa_lab || 0);
                totalSubjCost.value += totalLabCost.value + totalLecCost.value;
            } else {
                
                let discountBase = 0;
                if(item.soa_custype == 4){ // discounts
                    if(item.soa_disc_type == 1){
                        discountBase = (item.soa_price || 0) * (item.soa_quantity || 0)
                        totalPercentDiscount.value += discountBase / 100; // percentage stored as whole number
                    }else{
                        totalFixedDiscount.value += (item.soa_price || 0) * (item.soa_quantity || 0); // exact amount
                        totalDiscount.value += totalFixedDiscount.value;
                    }

                }else{
                    if (item.soa_custid != null && item.soa_itemid == null &&  item.soa_disc_type == 0) { // misc
                        totalMiscCost.value += (item.soa_price || 0) * (item.soa_quantity || 0);
                    }
                    
                    if(item.soa_custid == null && item.soa_itemid != null &&  item.soa_disc_type == 0){ // item
                        totalItemCost.value += (item.soa_price || 0) * (item.soa_quantity || 0);
                    }
                }
            }

            let subtotal = totalLecCost.value + totalLabCost.value + totalItemCost.value + totalMiscCost.value;
            totalDiscount.value = (subtotal*totalPercentDiscount.value) + totalDiscount.value;
            grandTotal.value = subtotal - (totalDiscount.value + totalPayment.value);
        });
    });
})

</script>

<template>
    <div class="p-2">
        <div class="card border shadow-sm" v-if="!preLoading">

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
                            {{ `${studentAccount[0].per_firstname || ''} ${studentAccount[0].per_middlename || ''}
                            ${studentAccount[0].per_lastname || ''} ${studentAccount[0].per_suffixname ||
                                ''}`.trim() }}
                        </p>
                        <p class="mb-1"><strong>Student No:</strong> {{ studentAccount[0].acs_enrid || 'N/A' }}
                        </p>
                        <p class="mb-1"><strong>Email:</strong> {{ studentAccount[0].per_email || 'N/A' }}</p>
                         <p class="mb-1"><strong>Date Enrolled:</strong>
                            {{studentAccount[0].soa_dateupdated ? studentAccount[0].soa_dateupdated: '-'}}
                        </p>
                    </div>
                    <div class="col-6 text-end">
                        <p class="mb-1"><strong>Course:</strong>
                            {{studentAccount[0].prog_name? studentAccount[0].prog_name: 'N/A'}}
                        </p>
                        <p class="mb-1"><strong>Grade Level:</strong>
                            {{studentAccount[0].gradelvl_desc? studentAccount[0].gradelvl_desc: 'N/A'}}
                        </p>
                        <p class="mb-1"><strong>Section:</strong>
                            {{studentAccount[0].section_name ? studentAccount[0].section_name: 'N/A'}}
                        </p>
                        <p class="mb-1"><strong>Date Enrolled:</strong>
                            {{studentAccount[0].soa_dateenrolled ? studentAccount[0].soa_dateenrolled: 'N/A'}}
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
                                <span>Item/Miscellaneous Fees</span>
                                <span>{{ pesoConverter(totalItemCost) }}</span>
                            </div>

                            <div class="d-flex justify-content-between mb-1">
                                <span>Other Charges</span>
                                <span>{{ pesoConverter(totalMiscCost) }}</span>
                            </div>

                            <hr>

                            <div class="d-flex justify-content-between text-danger mb-1">
                                <span>Deductions / Discounts</span>
                                <span>- {{ pesoConverter(totalDiscount) }}</span>
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
</template>