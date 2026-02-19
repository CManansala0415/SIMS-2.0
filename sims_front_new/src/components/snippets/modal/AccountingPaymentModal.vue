<script setup>
import { ref, onMounted, computed } from 'vue';
import Loader from '../loaders/Loading1.vue';
import { getUserID } from "../../../routes/user.js";
import {
    addPayment,
    getTransactionDetails,
    getPaymentDetails,
    getSetSeries,
    getUsedSeries,
    getCollectionStatus,
} from "../../Fetchers.js";
import {
    pdfAutoPrint,
    pdfGenerator,
    numberToWords,
    formatDateTime,
    getDateToday
} from "../../Generators.js";
import ProvisionalReceipt from '../forms/accountingforms/ProvisionalReceipt.vue';
import OfficialReceipt from '../forms/accountingforms/OfficialReceipt.vue';
import NeuLoader4 from '../loaders/NeuLoader4.vue';
import NeuLoader2 from '../loaders/NeuLoader2.vue';
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

const generateDefault = ref(1)
const disabler = ref(false)
const receiptType = ref(1)
const seriesNoStart = ref('')
const seriesNoLimit = ref('')
const seriesNoActual = ref('')
const paymentMode = ref('')
const amountPaid = ref(0)
const byPassRemarks = ref('')
const amountTobePaid = ref(0)
const userID = ref(0)
const userName = ref('')
const payment = ref([])
const checking = ref(false)
const balance = ref(0)
const paid = ref(0)
const accountId = ref('')
const latestPayment = ref([])

const chequeBankName = ref('')
const chequeNo = ref('')

const bankTransferName = ref('')
const bankTransferNo = ref('')

const setValues = () => {
    amountPaid.value = account.value.by_pass? amountTobePaid.value:0
    chequeBankName.value = ''
    chequeNo.value = ''
    bankTransferName.value = ''
    bankTransferNo.value = ''
}

const personName = ref('')

const receiptOrSeries = ref([])
const receiptPrSeries = ref([])
const receiptSeries = ref([])
const today = ref('');

onMounted(() => {
    //prevent e and negative
    document.querySelector(".amount-text").addEventListener("keypress", function (evt) {
        if (evt.which != 8 && evt.which != 0 && evt.which < 48 || evt.which > 57)
        {
            evt.preventDefault();
        }
    });

    today.value = getDateToday();

    checking.value = true
    // amountTobePaid.value = account.value.acr_amount? account.value.acr_amount:''
    // receiptType.value = account.value.acr_receipt? account.value.acr_receipt:''
    // paymentMode.value = account.value.acr_mode? account.value.acr_mode:''
    // amountPaid.value = account.value.acr_payment? account.value.acr_payment:''

    getUserID().then((results) => {
        userID.value = results.account.data.id
        let userfname = results.employee.emp_firstname? results.employee.emp_firstname : ''
        let usermname = results.employee.emp_middlename? results.employee.emp_middlename : ''
        let userlname = results.employee.emp_lastname? results.employee.emp_lastname : ''
        let usersname = results.employee.emp_suffixname? results.employee.emp_suffixname : ''

        userName.value = userfname + ' ' + usermname + ' ' + userlname + ' ' + usersname
        
        let accfname = account.value.per_firstname? account.value.per_firstname : ''
        let accmname = account.value.per_middlename? account.value.per_middlename : ''
        let acclname = account.value.per_lastname? account.value.per_lastname : ''
        let accsname = account.value.per_suffixname? account.value.per_suffixname : ''

        account.value.acr_personname? personName.value = account.value.acr_personname : personName.value = accfname + ' ' + accmname + ' ' + acclname + ' ' + accsname

        // identify if tuition of not
        if (billType.value == 1) {
            accountId.value = account.value.acs_id
        } else {
            accountId.value = account.value.acr_id
        }

        getPaymentDetails(accountId.value, billType.value).then((results) => {
            payment.value = results.data
            let x = payment.value.slice(-1).pop()

            
            if(billType.value == 1){ // means tuition = 1 ,  request = 2
                balance.value = typeof x !== 'undefined' ? x.acy_balance : account.value.acs_balance
            }else{
                balance.value = typeof x !== 'undefined' ? x.acy_balance : account.value.acr_total
            }

            balance.value = Number(balance.value)
            amountTobePaid.value = balance.value.toFixed(2)

            payment.value.forEach((e) => {
                paid.value += Number(e.acy_payment)
            })


                getTransactionDetails(0, 0, '', '', '', 2, accountId.value, billType.value).then((transact) => {

                    getSetSeries(userID.value).then(async(series) => {
                        receiptOrSeries.value.push(...series.or_series)
                        receiptPrSeries.value.push(...series.pr_series)
                        receiptSeries.value.push(...series.or_series, ...series.pr_series)  

                        if(billType.value == 1){
                            if(receiptPrSeries.value.length <= 0){
                                Swal.fire({
                                    title: "Error",
                                    text: "Series not found, please setup the series first to proceed payment",
                                    icon: "error"
                                }).then(() => {
                                    location.reload()
                                });
                            }else{
                                seriesNoStart.value = receiptPrSeries.value[0].sr_prefix+ '-' + receiptPrSeries.value[0].sr_year+ '-' + receiptPrSeries.value[0].sr_start
                                seriesNoLimit.value = receiptPrSeries.value[0].sr_prefix+ '-' + receiptPrSeries.value[0].sr_year+ '-' + receiptPrSeries.value[0].sr_end

                                let exist = await getUsedSeries(receiptPrSeries.value[0].sr_start, receiptPrSeries.value[0].sr_end, receiptPrSeries.value[0].sr_year);
                                let latestpattern = receiptPrSeries.value[0].sr_start; // default if no data found

                                if (exist.data && exist.data.length > 0) {
                                    latestpattern = Math.max(...exist.data.map(item => Number(item.acy_series_pattern)));
                                }

                                seriesNoActual.value =receiptPrSeries.value[0].sr_prefix + '-' +receiptPrSeries.value[0].sr_year + '-' + (Number(latestpattern) + 1);
                                checking.value = false
                            }
                        }else{
                             if(receiptPrSeries.value.length <= 0){
                                Swal.fire({
                                    title: "Error",
                                    text: "Series not found, please setup the series first to proceed payment",
                                    icon: "error"
                                }).then(() => {
                                    location.reload()
                                });
                            }else{
                                if(transact.data[0].acr_docstamp === 'underfined'){
                                    Swal.fire({
                                        title: "Error",
                                        text: "Unknown error occured, try again later",
                                        icon: "error"
                                    }).then(() => {
                                        location.reload()
                                    });
                                }else{
                                    !transact.data[0].acr_docstamp?receiptType.value=1:receiptType.value=2
                                    seriesNoStart.value = receiptOrSeries.value[0].sr_prefix+ '-' + receiptOrSeries.value[0].sr_year+ '-' + receiptOrSeries.value[0].sr_start
                                    seriesNoLimit.value = receiptOrSeries.value[0].sr_prefix+ '-' + receiptOrSeries.value[0].sr_year+ '-' + receiptOrSeries.value[0].sr_end
                                    
                                    let exist = await getUsedSeries(receiptOrSeries.value[0].sr_start, receiptOrSeries.value[0].sr_end, receiptOrSeries.value[0].sr_year);
                                    let latestpattern = receiptOrSeries.value[0].sr_start; // default if no data found

                                    if (exist.data && exist.data.length > 0) {
                                        latestpattern = Math.max(...exist.data.map(item => Number(item.acy_series_pattern)));
                                    }
                                    
                                    seriesNoActual.value = receiptOrSeries.value[0].sr_prefix+ '-' + receiptOrSeries.value[0].sr_year+ '-' + (Number(latestpattern) + 1)
                                    checking.value = false

                                }
                            }
                        }

                        if(account.value.by_pass){
                            amountPaid.value = amountTobePaid.value
                        }else{
                            amountPaid.value = 0
                        }
                    })

                    
                    
                })

            // console.log(account.value)
        })
    })

})


const verifyReceiptSeries = async () => {

    var startseries = ''
    var limitseries = ''
    var status = 204
    var addedpattern = ''
    var finalseries = ''
    var date = new Date();
    var curryear = date.getFullYear();
    var start = ''
    var startpattern = ''
    var end = ''
    var endpattern = ''
    var exist = ''
    var latestpattern = ''

    await getSetSeries(userID.value).then(async(results) => {

        let orseries = results.or_series
        let prseries = results.pr_series

        if(billType.value == 1){
            startseries = prseries[0].sr_prefix+ '-' + prseries[0].sr_year+ '-' + prseries[0].sr_start
            limitseries = prseries[0].sr_prefix+ '-' + prseries[0].sr_year+ '-' + prseries[0].sr_end
        }else{
            startseries = orseries[0].sr_prefix+ '-' + orseries[0].sr_year+ '-' + orseries[0].sr_start
            limitseries = orseries[0].sr_prefix+ '-' + orseries[0].sr_year+ '-' + orseries[0].sr_end
        }

        start = startseries.split('-'); // splits by the hyphen
        startpattern = Number(start[2]); // make sure it's a number

        end = limitseries.split('-'); // splits by the hyphen
        endpattern = Number(end[2]); // make sure it's a number

        exist = await getUsedSeries(startpattern, endpattern, curryear);
        latestpattern = startpattern; // default if no data found

        if (exist.data && exist.data.length > 0) {
            latestpattern = Math.max(...exist.data.map(item => Number(item.acy_series_pattern)));
            addedpattern=latestpattern+1
        }else{
            addedpattern=latestpattern
        }

        if((addedpattern > endpattern)||(addedpattern < startpattern)){
            status = 500
            Swal.fire({
                title: "Transaction Declined",
                html: `<span class="fw-bold">${start[0]}-${curryear}-${addedpattern}</span> is already out for the series sequence, try to change the series set or contact the administrator for verification`,
                icon: "warning",
                confirmButtonText: "Ok, Got it!"
            });
            disabler.value = false;
        }else{
            status = 200
            finalseries = `${start[0]}-${curryear}-${addedpattern}`;
        }

    })

    // return finalseries; // <-- this returns the value from verifyReceiptSe`ries
    return {
        series: finalseries,
        year: curryear,
        pattern: addedpattern,
        prefix: start[0],
        status: status
    };
   
};


const checkCounterStatus = () =>{
    disabler.value = true
    
    Swal.fire({
        title: "Saving Updates",
        text: "Please wait while we check all transaction details.",
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    getCollectionStatus().then((results)=>{
        if(results.data.sett_status == 0){
            Swal.close();
            Swal.fire({
                title: "Transaction Declined",
                html: `Counter has been set inactive during this transaction, please contact the accountant or the administrator for verification.`,
                icon: "warning",
                confirmButtonText: "Ok, Got it!"
            })
        }else{
            setTimeout(() => {
                initPayment();
            }, 2000);
        }
    })
}

const initPayment = () => {

    
    var balance = amountTobePaid.value - amountPaid.value
    var x = {}

    if (amountPaid.value && receiptType.value && paymentMode.value) {
        verifyReceiptSeries().then((seriesno) => {

        // console.log(seriesno); // now this will log the correct series
            if(seriesno.status == 200){
                var x = {
                    ...account.value,
                    acy_accid: accountId.value,
                    acy_mode: paymentMode.value,
                    acy_receipt: receiptType.value,
                    acy_payment: amountPaid.value,
                    acy_amount: amountTobePaid.value,
                    acr_paystatus: amountTobePaid.value == amountPaid.value ? 2 : 1,
                    acy_partial: amountTobePaid.value == amountPaid.value ? 2 : 1,
                    acy_cashier: userID.value,
                    acy_cashiername: userName.value,
                    acy_balance: balance,
                    acy_billtype: billType.value,

                    acy_series:seriesno.series,
                    acy_series_prefix:seriesno.prefix,
                    acy_series_year:seriesno.year,
                    acy_series_pattern:seriesno.pattern,

                    acy_cheque_no: chequeNo.value,
                    acy_cheque_bank: chequeBankName.value,
                    acy_bank_no: bankTransferNo.value,
                    acy_transferred_bank: bankTransferName.value,
                    acy_remarks: byPassRemarks.value,
                    
                }

                // console.log(seriesno.series)
                // console.log(x)

                latestPayment.value = x
                renderPayment(x)
            }
        });


    } else {
        // alert('Please fill out all the fields')
        Swal.close();
        Swal.fire({
            title: "Requirements",
            text: "Please fill out all the fields",
            icon: "question"
        }).then(() => {
            // location.reload()
            disabler.value = false
        });
    }
   

}

const renderPayment = (paymentdata) =>{
    if (billType.value == 1) {
        getTransactionDetails(0, 0, '', '', '', 2, accountId.value,1).then((results) => {
            // console.log(results.data[0].acs_status)
            // console.log(paymentdata.acs_status)
            // console.log(accountId.value)
            if (results.data[0].acs_status == paymentdata.acs_status) {
                addPayment(paymentdata).then((results) => {
                    if (results.status == 204) {
                        Swal.close();
                        Swal.fire({
                            title: "Update Successful",
                            text: "Changes applied, preparing receipt...",
                            icon: "success",
                            confirmButtonText: "Ok, Got it!"
                        }).then(async (result) => {
                            // console.log(result)
                            if (result.isConfirmed) {
                                // 🔄 Show loading Swal
                                Swal.fire({
                                    title: "Generating PDF...",
                                    text: "Please wait while we prepare your receipt.",
                                    allowOutsideClick: false,
                                    didOpen: () => {
                                        Swal.showLoading();
                                    }
                                });

                                let name = "receipt" + '-' + accountId.value;
                                let size = [6.823, 4.25];
                                // let size = [8.5, 4.25];

                                // Wait until PDF is generated
                                await pdfGenerator(name, size, "landscape", 0.03);

                                // ⏳ Keep loader for 1.5s more before closing + reloading
                                setTimeout(() => {
                                    Swal.close();
                                    location.reload();
                                }, 1000);
                            }
                        });


                    } else {

                        Swal.close();
                        Swal.fire({
                            title: "Payment Failed",
                            text: "Cannot proceed payment. Error occured, try again later",
                            icon: "question"
                        }).then(() => {
                            location.reload()
                        });
                    }
                })
            } else {
                // alert('Cannot proceed payment. /n This Item is removed from registrar. Please refresh the page')
                Swal.close();
                Swal.fire({
                    title: "Changes in the system detected",
                    text: "Cannot proceed payment. This Item is removed from registrar. Please refresh the page",
                    icon: "question"
                }).then(()=>{

                    // location.reload()
                });
            }
        })
    } else {
        getTransactionDetails(0, 0, '', '', '', 2, accountId.value,2).then((results) => {
            if (results.data[0].acr_status == 1) {
                addPayment(paymentdata).then(async(results) => {
                    // if (results.status == 204) {
                    //     Swal.fire({
                    //         title: "Update Successful",
                    //         text: "Changes applied, preparing receipt...",
                    //         icon: "success",
                    //         confirmButtonText: "Ok, Got it!"
                    //     }).then(async (result) => {
                    //         // console.log(result)
                    //         if (result.isConfirmed) {
                    //             // 🔄 Show loading Swal
                    //             Swal.fire({
                    //                 title: "Generating PDF...",
                    //                 text: "Please wait while we prepare your receipt.",
                    //                 allowOutsideClick: false,
                    //                 didOpen: () => {
                    //                     Swal.showLoading();
                    //                 }
                    //             });

                    //             let name = "receipt";
                    //             let size = [6.823, 4.25];
                    //             // let size = [8.5, 4.25];

                    //             // Wait until PDF is generated
                    //             await pdfGenerator(name, size, "landscape", 0.03);

                    //             // ⏳ Keep loader for 1.5s more before closing + reloading
                    //             setTimeout(() => {
                    //                 Swal.close();
                    //                 location.reload();
                    //             }, 1000);
                    //         }
                    //     });


                    // } 
                    if (results.status == 204) {
                    Swal.fire({
                        title: "Generating PDF...",
                        text: "Please wait while we prepare your receipt.",
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });

                    let name = "receipt";
                    let receiptWidth = 6.823; // in inches, your receipt width

                    try {
                        const pdfBlob = await pdfAutoPrint(name, receiptWidth, "portrait", 0.5);

                        const pdfUrl = URL.createObjectURL(pdfBlob);
                        const printWindow = window.open(pdfUrl, 'PrintWindow', 'width=900,height=700');

                        printWindow.onload = () => {
                            printWindow.focus();
                            printWindow.print();
                        };

                    } catch (err) {
                        console.error(err);
                        Swal.fire("Error", "Failed to generate receipt.", "error");
                        location.reload();
                        return;
                    }

                    setTimeout(() => {
                        Swal.close();
                        location.reload();
                    }, 1000);
                }

                    
                    else {
                        Swal.fire({
                            title: "Payment Failed",
                            text: "Cannot proceed payment. Error occured, try again later",
                            icon: "question"
                        }).then(() => {
                            location.reload()
                        });
                    }
                })
            } else {
                // alert('Cannot proceed payment. /n This Item is removed from registrar. Please refresh the page')
                Swal.fire({
                    title: "Changes in the system detected",
                    text: "Cannot proceed payment. This Item is removed from registrar. Please refresh the page",
                    icon: "question"
                }).then(()=>{
                    location.reload()
                });
            }
        })
    }
    
}

</script>

<template>
   
    <div class="d-flex flex-column p-2 gap-3">
        <div class="d-flex flex-wrap flex-column">
            <!-- <p class="text-success fw-bold">Payment Settlement</p> -->
            <p class="fst-italic border p-2 small-font"><span class="fw-bold">Note:
                </span><span class="italic">Ensure that the details of the payment is correct.
                </span></p>
        </div>
        <div class="small-font">
            <NeuLoader2 v-if="checking" />
            <div v-else class="row mb-3">
                <div class="col-md-12 col-lg-4">
                    <form @submit.prevent="checkCounterStatus" class="neu-card p-3 text-start">
                        <div class="form-group p-1">
                            <label class="text-xs">Series No.</label>
                            <input class="neu-input" type="text"
                                required v-model="seriesNoActual" disabled
                            />
                        </div>
                        <div class="form-group p-1">
                            <label class="text-xs">Receipt Type</label>
                            <select class="neu-input neu-select" required v-model="receiptType" disabled="">
                                <!-- :disabled="balance == 0 ? true : false"> -->
                                <option value="" disabled>--Select Receipt Type--</option>
                                <option value="1">Provisional (PR)</option>
                                <option value="2">Official (OR)</option>
                            </select>
                        </div>
                        <div class="form-group p-1">
                            <label class="text-xs">Mode of Payment</label>
                            <select @change="setValues()" class="neu-input neu-select" required v-model="paymentMode"
                                :disabled="balance == 0 ? true : false">
                                <option value="" disabled>--Select Payment Type--</option>
                                <option value="1">Cash</option>
                                <option value="2">Bank</option>
                                <option value="3">Cheque</option>
                                <option value="4">Credit</option>
                            </select>
                        </div>

                        <div class="form-group p-1">
                            <label class="text-xs">Actual Payment</label>
                            <input 
                                v-model.number="amountPaid"
                                required
                                step="0.01" 
                                min="0.00"
                                :max="amountTobePaid"
                                :disabled="balance == 0 || account.by_pass"
                                @input="
                                if (amountPaid < 0) amountPaid = 0;
                                if (amountPaid > amountTobePaid) amountPaid = amountTobePaid;
                                "
                                type="number"
                                class="neu-input amount-text"
                            />
                            <!-- Helper message -->
                            <small v-if="amountPaid === amountTobePaid" class="text-red-500">
                                ⚠️ Maximum allowed is {{ amountTobePaid }}
                            </small>
                            <small v-else-if="amountPaid === 0" class="text-yellow-500">
                                ⚠️ Minimum is 0
                            </small>
                        </div>



                        <div v-if="paymentMode == 2" class="form-group p-1">
                            <label class="text-xs">Bank Name</label>
                            <input v-model="bankTransferName" required :disabled="balance == 0 ? true : false" type="text"
                                class="neu-input" />
                        </div>
                        <div v-if="paymentMode == 2" class="form-group p-1">
                            <label class="text-xs">Bank No.</label>
                            <input v-model="bankTransferNo" required :disabled="balance == 0 ? true : false"
                                oninput="this.value = Math.abs(this.value)" type="number"
                                class="neu-input" />
                        </div>

                        <div v-if="paymentMode == 3" class="form-group p-1">
                            <label class="text-xs">Bank Name</label>
                            <input v-model="chequeBankName" required :disabled="balance == 0 ? true : false" type="text"
                                class="neu-input" />
                        </div>
                        <div v-if="paymentMode == 3" class="form-group p-1">
                            <label class="text-xs">Cheque No.</label>
                            <!-- <input v-model="chequeNo" required :disabled="balance == 0 ? true : false"
                                oninput="this.value = Math.abs(this.value)" type="number"
                                class="neu-input" /> -->
                            <input v-model="chequeNo" required :disabled="balance == 0 ? true : false"
                                type="text"
                                class="neu-input" />
                        </div>

                        <div class="form-group p-1">
                            <label class="text-xs">Amount to be paid</label>
                            <input v-model="amountTobePaid" onkeydown="return /[a-z, ]/i.test(event.key)" type="text""
                                class="neu-input" disabled />
                        </div>
                        <div class="form-group p-1">
                            <label class="text-xs">Amount Paid</label>
                            <input v-model="amountPaid" onkeydown="return /[a-z, ]/i.test(event.key)" type="text"
                                class="neu-input" disabled />
                        </div>
                        <div class="form-group p-1">
                            <label class="text-xs">Remaining Balance</label>
                            <input :value="(amountTobePaid - amountPaid).toFixed(2)" onkeydown="return /[a-z, ]/i.test(event.key)"
                                type="text" class="neu-input" disabled />
                        </div>
                        <div class="form-group p-1" v-if="account.by_pass">
                            <label class="text-xs">Remarks</label>
                            <textarea v-model="byPassRemarks" style="height: 200px;" required minlength="5"
                                type="text" class="neu-input"></textarea>
                        </div>
                        <div class="mt-3 d-flex justify-content-center align-content-center">
                            <button v-if="balance != 0" :disabled="disabler ? true : false" type="submit"
                                class="neu-btn neu-green p-2">
                                <font-awesome-icon icon="fa-solid fa-floppy-disk"/> Proceed Payment</button>
                            <p v-if="balance == 0 && account.acr_dateupdated" class="fw-bold text-success">Payment Completed
                                ({{
                                    account.acr_dateupdated }})</p>
                        </div>
                    </form>
                </div>
                <div class="col-md-12 col-lg-8">
                    <div class="p-3 neu-card-inner h-100">
                        <div class="p-3 mb-3 fw-bold">
                            Payment History
                        </div>
                        <div class="table-responsive border d-flex flex-column justify-content-between p-2" >
                            <div class="neu-card p-3" style="height:390px; overflow: auto;" >
                                <table class="neu-table-flat">
                                    <thead>
                                        <tr>
                                            <th style="color:#555555">Paid</th>
                                            <th style="color:#555555">Amount</th>
                                            <th style="color:#555555">Balance</th>
                                            <th style="color:#555555">Mode</th>
                                            <th style="color:#555555">Remarks</th>
                                            <th style="color:#555555">Receipt</th>
                                            <th style="color:#555555">Series</th>
                                            <th style="color:#555555">Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-if="Object.keys(payment).length" v-for="(py, index) in payment">
                                            <td class="align-middle">{{ py.acy_payment }}</td>
                                            <td class="align-middle">{{ py.acy_amount }}</td>
                                            <td class="align-middle text-danger fw-bold">{{ py.acy_balance }}</td>
                                            <td class="align-middle">
                                                <select class="neu-input neu-select" required v-model="py.acy_mode"
                                                    disabled>
                                                    <option value="1">Cash</option>
                                                    <option value="2">Bank</option>
                                                    <option value="3">Cheque</option>
                                                </select>
                                                <input v-if="py.acy_mode == 2" disabled :value="py.acy_transferred_bank"
                                                    type="text" class="neu-input" />
                                                <input v-if="py.acy_mode == 2" disabled :value="py.acy_bank_no" type="text"
                                                    class="neu-input" />
                                                <input v-if="py.acy_mode == 3" disabled :value="py.acy_cheque_bank" type="text"
                                                    class="neu-input" />
                                                <input v-if="py.acy_mode == 3" disabled :value="py.acy_cheque_no" type="text"
                                                    class="neu-input" />
                                            </td>
                                            <td class="align-middle">
                                                {{ py.acy_remarks }}
                                            </td>
                                            <td class="align-middle">
                                                <select class="neu-input neu-select" v-model="py.acy_receipt" disabled>
                                                    <option value="1">Provisional</option>
                                                    <option value="2">Official</option>
                                                </select>
                                            </td>
                                            <td class="align-middle">
                                                {{ py.acy_series }}
                                            </td>
                                            <td class="align-middle">{{ formatDateTime(py.acy_datepaid) }}</td>
                                        </tr>
                                        <tr v-if="!checking && !Object.keys(payment).length">
                                            <td class="p-3 text-center" colspan="8">
                                                <NeuLoader4/>
                                                <p class="fw-bold m-0">Nothing here yet!</p>
                                                <p>The hamster took a break 💤 — No payment has been recorded</p>
                                            </td>
                                        </tr>
                                        <tr v-if="checking && !Object.keys(payment).length">
                                            <td class="p-3 text-center" colspan="8">
                                                <div class="m-3">
                                                    <Loader />
                                                </div>
                                            </td>
                                        </tr>

                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="mt-3 d-flex gap-1 bg-secondary-subtle p-3 justify-content-end align-content-center">
                                <span class="fw-bold">Total Amount Paid: <span class="text-success">{{ paid
                                }}</span>
                                </span>
                                <span class="fw-bold">Remaining Balance: <span class="text-danger">{{ balance.toFixed(2)
                                }}</span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div v-if="!checking" class="w-100 border d-flex flex-column justify-content-center align-items-center p-4 bg-secondary rounded-3">
            <div class="mb-3 form-group">
                <select v-model="generateDefault" class="form-select form-select-sm" :disabled="receiptType==0">
                    <option value="0" disabled>Select Receipt Type</option>
                    <option value="1">Default</option>
                    <option value="2">Generated</option>
                </select>
                <small v-if="receiptType==0" class="text-red-500">
                    ⚠️ Select Receipt Type First 
                </small>
            </div>

            <div id="printform" class="text-uppercase border"
                style="width: 655px; height: 395px; position: relative; overflow: hidden;">
                <div v-if="generateDefault == 1 && receiptType == 1" style="height: 100%; width: 100%;  font-weight: bold;" class="receipt-2 times ">
                    <span style="position:absolute;top:160px; left: 280px; font-size:10px;">
                        {{ personName }}
                    </span>
                    <span style="position:absolute;top:181px; left:280px; font-size:10px;">
                        {{
                            [
                            account.per_curr_home,
                            account.currbarangayname,
                            account.currcityname,
                            account.currprovincename
                            ].filter(Boolean).join(', ')
                        }}
                    </span>

                    <span v-if="paymentMode == 1" style="position:absolute;top:265px; left:25px; font-size:10px;">
                        &#8369; &nbsp;{{ amountPaid }} 
                    </span>
                    <span v-if="paymentMode == 1" style="position:absolute;top:225px; left:210px; font-size:10px;">
                        &#8369; &nbsp;{{ numberToWords(amountPaid) }} 
                    </span>
                    <span v-if="paymentMode == 1" style="position:absolute;top:250px; left:220px; font-size:10px;">
                        &#8369; &nbsp;{{ amountPaid }} 
                    </span>

                    <span v-if="paymentMode ==  2" style="position:absolute;top:300px; left:25px; font-size:10px;">
                        ({{ bankTransferName }}) - {{ bankTransferNo }}
                    </span>

                    <span v-if="paymentMode == 3" style="position:absolute;top:300px; left:25px; font-size:10px;">
                        ({{ chequeBankName }}) - {{ chequeNo }}
                    </span>
                </div>
                <div v-if="generateDefault == 2 && receiptType == 1" style="height: 100%; width: 100%;">
                    <ProvisionalReceipt :data="latestPayment" />
                </div>

                <div v-if="generateDefault == 1 && receiptType == 2" style="height: 100%; width: 100%;  font-weight: bold;" class="times ">
                    <span style="position:absolute;top:100px; left: 65px; font-size:10px;">
                        {{ personName }}
                    </span>
                     <span style="position:absolute;top:100px; left: 500px; font-size:10px;">
                        {{ today }}
                    </span>
                    <span style="position:absolute;top:115px; left: 50px; font-size:10px;"> 
                        {{account.per_curr_home}}, {{account.currbarangayname}}, {{account.currcityname}}, {{account.currprovincename}}
                    </span>
                    <span style="position:absolute;top:130px; left: 50px; font-size:10px;"> 
                        {{account.per_id}}
                    </span>
                    <span style="position:absolute;top:170px; left: 160px; font-size:10px;"> 
                        {{account.acf_desc}}
                    </span>
                    <span style="position:absolute;top:170px; left: 100px; font-size:10px;"> 
                        {{account.acf_price}}
                    </span>
                    <span style="position:absolute;top:170px; left: 50px; font-size:10px;"> 
                        {{account.acr_qty}}
                    </span>
                    <span style="position:absolute;top:170px; left: 590px; font-size:10px;"> 
                        {{account.acr_total}}
                    </span>
                    <span style="position:absolute;top:293px; left: 590px; font-size:10px;"> 
                        {{balance}}
                    </span>
                    <span style="position:absolute;top:330px; left: 440px; font-size:10px; width: 200px;"> 
                        {{userName}}
                    </span>
                </div> 
                <div v-if="generateDefault == 2 && receiptType == 2" style="height: 100%; width: 100%;  font-weight: bold;">
                    <OfficialReceipt :data="latestPayment" />
                </div>

            </div>
            <!-- <div class="p-2">
                <button v-if="receiptType != 0" @click="generateDefault = !generateDefault" class="btn btn-sm btn-dark">Switch to Default</button>
            </div> -->
        </div>
    </div>

    
<!-- 
    <div id="printform" >
        <ProvisionalReceipt v-if="receiptType == 1" :data="latestPayment" />
        <OfficialReceipt v-if="receiptType == 2" :data="latestPayment" />
    </div> -->



</template>
