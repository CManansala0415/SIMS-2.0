<script setup>
import { ref, onMounted, computed } from 'vue';
import Loader from '../loaders/Loading1.vue';
import { getUserID } from "../../../routes/user";
import {
    addPayment,
    getRequestDetails,
    getPaymentDetails
} from "../../Fetchers.js";

const props = defineProps({
    accountData: {
    },
    billTypeData: {
    },
})
const account = computed(() => {
    return props.accountData
});
const billType = computed(() => {
    return props.billTypeData
});

const disabler = ref(false)
const receiptType = ref('')
const paymentMode = ref('')
const amountPaid = ref('')
const amountTobePaid = ref(0)
const userID = ref(0)
const payment = ref([])
const checking = ref(false)
const balance = ref(0)
const paid = ref(0)
const accountId = ref('')

const chequeBankName = ref('')
const chequeNo = ref('')

const bankTransferName = ref('')
const bankTransferNo = ref('')

const setValues = () => {
    amountPaid.value = 0
    chequeBankName.value = ''
    chequeNo.value = ''
    bankTransferName.value = ''
    bankTransferNo.value = ''
}



onMounted(() => {

    checking.value = true

    // amountTobePaid.value = account.value.acr_amount? account.value.acr_amount:''
    // receiptType.value = account.value.acr_receipt? account.value.acr_receipt:''
    // paymentMode.value = account.value.acr_mode? account.value.acr_mode:''
    // amountPaid.value = account.value.acr_payment? account.value.acr_payment:''

    getUserID().then((results) => {
        userID.value = results.data.id

        // identify if tuition of not
        if (billType.value == 1) {
            accountId.value = account.value.acs_id
        } else {
            accountId.value = account.value.acr_id
        }
        console.log(accountId.value)
        getPaymentDetails(accountId.value, billType.value).then((results) => {
            payment.value = results.data
            let x = payment.value.slice(-1).pop()

            balance.value = typeof x !== 'undefined' ? x.acy_balance : account.value.acr_amount
            amountTobePaid.value = balance.value

            payment.value.forEach((e) => {
                paid.value += e.acy_payment
            })

            checking.value = false
        })
    })

})


const initPayment = () => {
    disabler.value = true
    let balance = amountTobePaid.value - amountPaid.value
    let x = {
        acy_accid: accountId.value,
        acy_mode: paymentMode.value,
        acy_receipt: receiptType.value,
        acy_payment: amountPaid.value,
        acy_amount: amountTobePaid.value,
        acr_paystatus: amountTobePaid.value == amountPaid.value ? 2 : 1,
        acy_partial: amountTobePaid.value == amountPaid.value ? 2 : 1,
        acy_cashier: userID.value,
        acy_balance: balance,
        acy_billtype: billType.value,

        acy_cheque_no: chequeNo.value,
        acy_cheque_bank: chequeBankName.value,
        acy_bank_no: bankTransferNo.value,
        acy_transferred_bank: bankTransferName.value,
    }


    if (amountPaid.value && receiptType.value && paymentMode.value) {
        if (billType.value == 1) {
            addPayment(x).then((results) => {
                if (results.status != 204) {
                    alert('Saving Failed')
                    location.reload()
                } else {
                    alert('Saving Successful')
                    location.reload()
                }
            })
        } else {
            getRequestDetails(0, 0, accountId.value).then((results) => {
                if (results.data[0].acr_status == 1) {
                    addPayment(x).then((results) => {
                        if (results.status != 204) {
                            alert('Saving Failed')
                            location.reload()
                        } else {
                            alert('Saving Successful')
                            location.reload()
                        }
                    })
                } else {
                    alert('Cannot proceed payment. /n This Item is removed from registrar. Please refresh the page')
                }
            })
        }
    } else {
        alert('Please fill out all the fields')
        disabler.value = false
    }
}

</script>

<template>
    <div class="d-flex flex-column p-2 gap-2">
        <div class="d-flex flex-wrap flex-column">
            <p class="text-success fw-bold">Payment Settlement</p>
            <p class="fst-italic border p-2 rounded-3 bg-secondary-subtle small-font"><span class="fw-bold">Note:
                </span><span class="italic">Ensure that the details of the payment is correct.
                </span></p>
        </div>

        <div class="d-flex align-content-center justify-content-center gap-2 small-font">

            <Loader v-if="checking" />
            <div v-else class="d-flex gap-1 w-100 ">

                <form @submit.prevent="initPayment" class="card p-3 text-start w-25">
                    <div class="form-group p-1">
                        <label class="text-xs">Receipt Type</label>
                        <select class="form-control form-select-sm" required v-model="receiptType"
                            :disabled="balance == 0 ? true : false">
                            <option value="" disabled>--Select Receipt Type--</option>
                            <option value="1">Provisional (PR)</option>
                            <option value="2">Official (OR)</option>
                        </select>
                    </div>
                    <div class="form-group p-1">
                        <label class="text-xs">Mode of Payment</label>
                        <select @change="setValues()" class="form-control form-select-sm" required v-model="paymentMode"
                            :disabled="balance == 0 ? true : false">
                            <option value="" disabled>--Select Payment Type--</option>
                            <option value="1">Cash</option>
                            <option value="2">Bank</option>
                            <option value="3">Cheque</option>
                        </select>
                    </div>

                    <div class="form-group p-1">
                        <label class="text-xs">Actual Payment</label>
                        <input v-model="amountPaid" required min="0" :max="amountTobePaid"
                            :disabled="balance == 0 ? true : false" oninput="this.value = Math.abs(this.value)"
                            type="number" class="form-control form-control-sm" />
                    </div>

                    <div v-if="paymentMode == 2" class="form-group p-1">
                        <label class="text-xs">Bank Name</label>
                        <input v-model="bankTransferName" required :disabled="balance == 0 ? true : false" type="text"
                            class="form-control form-control-sm" />
                    </div>
                    <div v-if="paymentMode == 2" class="form-group p-1">
                        <label class="text-xs">Bank No.</label>
                        <input v-model="bankTransferNo" required :disabled="balance == 0 ? true : false"
                            oninput="this.value = Math.abs(this.value)" type="number"
                            class="form-control form-control-sm" />
                    </div>

                    <div v-if="paymentMode == 3" class="form-group p-1">
                        <label class="text-xs">Bank Name</label>
                        <input v-model="chequeBankName" required :disabled="balance == 0 ? true : false" type="text"
                            class="form-control form-control-sm" />
                    </div>
                    <div v-if="paymentMode == 3" class="form-group p-1">
                        <label class="text-xs">Cheque No.</label>
                        <input v-model="chequeNo" required :disabled="balance == 0 ? true : false"
                            oninput="this.value = Math.abs(this.value)" type="number"
                            class="form-control form-control-sm" />
                    </div>

                    <div class="form-group p-1">
                        <label class="text-xs">Amount to be paid</label>
                        <input v-model="amountTobePaid" onkeydown="return /[a-z, ]/i.test(event.key)" type="text"
                            class="form-control form-control-sm" disabled />
                    </div>
                    <div class="form-group p-1">
                        <label class="text-xs">Amount Paid</label>
                        <input v-model="amountPaid" onkeydown="return /[a-z, ]/i.test(event.key)" type="text"
                            class="form-control form-control-sm" disabled />
                    </div>
                    <div class="form-group p-1">
                        <label class="text-xs">Remaining Balance</label>
                        <input :value="amountTobePaid - amountPaid" onkeydown="return /[a-z, ]/i.test(event.key)"
                            type="text" class="form-control form-control-sm" disabled />
                    </div>
                    <div class="mt-3 d-flex justify-content-center align-content-center">
                        <button v-if="balance != 0" :disabled="disabler ? true : false" type="submit"
                            class="btn btn-sm btn-success w-100">Proceed
                            Payment</button>
                        <p v-if="balance == 0 && account.acr_dateupdated" class="fw-bold text-success">Payment Completed ({{
                            account.acr_dateupdated }})</p>
                    </div>
                </form>

                <div class="p-3 card w-75 h-100">
                    <div class="bg-secondary-subtle rounded-3 p-3 mb-3 fw-bold">
                        Payment History                            
                    </div>
                    <div class="table-responsive border d-flex flex-column justify-content-between h-100">
                       
                        <table class="table table-hover table-fixed">
                            <thead>
                                <tr>
                                    <th style="background-color: #237a5b;" class="text-white">Paid</th>
                                    <th style="background-color: #237a5b;" class="text-white">Amount</th>
                                    <th style="background-color: #237a5b;" class="text-white">Balance</th>
                                    <th style="background-color: #237a5b;" class="text-white">Mode</th>
                                    <th style="background-color: #237a5b;" class="text-white">Receipt</th>
                                    <th style="background-color: #237a5b;" class="text-white">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="Object.keys(payment).length" v-for="(py, index) in payment">
                                    <td class="align-middle">{{ py.acy_payment }}</td>
                                    <td class="align-middle">{{ py.acy_amount }}</td>
                                    <td class="align-middle text-danger fw-bold">{{ py.acy_balance }}</td>
                                    <td class="align-middle">
                                        <select class="form-control form-select-sm" required v-model="py.acy_mode" disabled>
                                            <option value="1">Cash</option>
                                            <option value="2">Bank</option>
                                            <option value="3">Cheque</option>
                                        </select>
                                        <input v-if="py.acy_mode == 2" disabled :value="py.acy_transferred_bank" type="text" class="form-control form-control-sm" />
                                        <input v-if="py.acy_mode == 2" disabled :value="py.acy_bank_no" type="text" class="form-control form-control-sm" />
                                        <input v-if="py.acy_mode == 3" disabled :value="py.acy_cheque_bank" type="text" class="form-control form-control-sm" />
                                        <input v-if="py.acy_mode == 3" disabled :value="py.acy_cheque_no" type="text" class="form-control form-control-sm" />
                                    </td>
                                    <td class="align-middle">
                                        <select class="form-control form-select-sm" v-model="py.acy_receipt" disabled>
                                            <option value="1">Provisional</option>
                                            <option value="2">Official</option>
                                        </select>
                                    </td>
                                    <td class="align-middle">{{ py.acy_datepaid }}</td>
                                </tr>
                                <tr v-if="!preLoading && !Object.keys(payment).length">
                                    <td class="p-3 text-center" colspan="6">
                                        No Records Found
                                    </td>
                                </tr>
                                <tr v-if="preLoading && !Object.keys(payment).length">
                                    <td class="p-3 text-center" colspan="6">
                                        <div class="m-3">
                                            <Loader />
                                        </div>
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                        <div class="mt-3 d-flex gap-1 bg-secondary-subtle p-3 justify-content-end align-content-center">
                            <span class="fw-bold">Total Amount Paid: <span class="text-success">{{ paid
                                    }}</span>
                            </span>
                            <span class="fw-bold">Remaining Balance: <span class="text-danger">{{ balance
                                    }}</span>
                            </span>
                        </div>
                    </div>
                </div>


            </div>

        </div>
    </div>

</template>
