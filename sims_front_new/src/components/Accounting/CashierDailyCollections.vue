<script setup>
import { ref, onMounted, computed } from 'vue';
import {
    getAllPayments,
    getCurrentWeekDailyCollection,
    dateFormatterWord,
    getBarHeights,
    getSetSeries
} from "../Fetchers.js";

import Loader from '../snippets/loaders/Loading1.vue';
import LineChart from '../snippets/tech/LineChart.vue';
import ExcelDownloaderDCR from  '../snippets/modal/ExcelDownloaderDCR.vue';

import { getUserID } from "../../routes/user.js";
import { useRouter, useRoute } from 'vue-router'


const router = useRouter();
const preLoading = ref(true)
const fetchingCollection = ref(true)
const emit = defineEmits(['fetchUser', 'doneLoading'])
const accessData = ref([])
const userID = ref('')


const payment = ref([])
const totalDailyPayment = ref(0)
const totalTuitionPayment = ref(0)
const totalMiscPayment = ref(0)
const dateFrom = ref('')
const dateToday = ref('')
const selectDateFrom = ref('')
const selectDateTo = ref('')
const transactionType = ref('0')

// for bar chart
const formattedWeekStart = ref('')
const formattedWeekEnd = ref('')
const currentDay = ref('')
const days = ref([])
const barHeights = ref([])
const weekCollection = ref([])
const receiptOrSeries = ref([])
const receiptPrSeries = ref([])
const receiptSeries = ref([])

const booter = async () => {
    // getAllPayments(transactionType.value, '2025-10-05', '2025-11-05', userID.value).then((results) => {
    getAllPayments(transactionType.value, dateFrom.value, dateToday.value, userID.value).then((results) => {
        payment.value = results.data

        //sum all payment based sa date
        payment.value.forEach((pay) => {
            totalDailyPayment.value += parseFloat(pay.acy_payment)
            totalTuitionPayment.value += pay.acy_billtype == 1 ? 1: 0
            totalMiscPayment.value += pay.acy_billtype == 2 ? 1: 0
        })
    })

    getSetSeries(userID.value).then((results) => {
        receiptOrSeries.value.push(...results.or_series)
        receiptPrSeries.value.push(...results.pr_series)
        receiptSeries.value.push(...results.or_series, ...results.pr_series)    
    })
}


const getDateToday = () =>{
    var date = new Date();
    var year = date.getFullYear();
    var month = String(date.getMonth() + 1).padStart(2, '0'); // Months are 0-indexed
    var day = String(date.getDate()).padStart(2, '0');

    var hours = String(date.getHours()).padStart(2, '0');
    var minutes = String(date.getMinutes()).padStart(2, '0');
    var seconds = String(date.getSeconds()).padStart(2, '0');

    var formattedDateTimeToday = `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
    var formattedDateTimeFrom = `${year}-${month}-${day} 01:00:00`;

    dateFrom.value = formattedDateTimeFrom;
    dateToday.value = formattedDateTimeToday;
}

const cashierId = ref('')
const cashierName = ref('')

onMounted(async () => {

    // var date = new Date();
    // var day = date.getDate();
    // var month = date.getMonth() + 1;
    // var year = date.getFullYear();
    // dateToday.value = date.toISOString().split('T')[0]

    getDateToday()
    selectDateTo.value = dateToday.value

    getUserID().then(async (results1) => {
        // user.value = results.account.data.name
        userID.value = results1.account.data.id
        cashierId.value = results1.account.data.accid
        cashierId.value = results1.account.data.name
        cashierName.value = [
            results1.employee.emp_firstname,
            results1.employee.emp_middlename || "",
            results1.employee.emp_lastname,
            results1.employee.emp_suffixname || ""
        ].filter(Boolean).join(" ");

        emit('fetchUser', results1)
        try {
            await booter().then(() => {

                // let collections = [10, 10, 10, 1000, 10, 10, 10]
                let collections = [0, 0, 0, 0, 0, 0, 0]

                 getCurrentWeekDailyCollection().then((weekData) => {
                    formattedWeekStart.value = weekData.formattedWeekStart
                    formattedWeekEnd.value = weekData.formattedWeekEnd
                    currentDay.value = weekData.currentDay
                    // days.value = weekData.days
                    days.value = [...weekData.days.slice(1), weekData.days[0]];


                    getAllPayments(transactionType.value, formattedWeekStart.value, formattedWeekEnd.value, userID.value).then((results) => {
                        let cashcollection = Array(7).fill(0);

                        // Loop through each data item
                        results.data.forEach(item => {
                            const datePaid = new Date(item.acy_datepaid);

                            // Only include dates within the range
                            if (datePaid >= new Date(results.datefrom) && datePaid <= new Date(results.dateto)) {
                                // Calculate day index (0 = Monday, 6 = Sunday)
                                const dayIndex = (datePaid.getDay() + 6) % 7; 
                                // Add payment to the corresponding day
                                cashcollection[dayIndex] += item.acy_payment;
                            }
                        });

                        // console.log(cashcollection);
                        collections = cashcollection
                        weekCollection.value = collections

                        getBarHeights(collections).then((bardata) => {
                            barHeights.value = bardata.barHeights
                        })

                        // convert formatted week start and end to word format
                        dateFormatterWord(weekData.formattedWeekStart).then((data) => {
                            formattedWeekStart.value = data.formattedDate
                        })
                        dateFormatterWord(weekData.formattedWeekEnd).then((data) => {
                            formattedWeekEnd.value = data.formattedDate
                        })
                
                        emit('doneLoading', false)
                        preLoading.value = false
                        fetchingCollection.value = false
                    })

                    
                })
              
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

    }).catch((err) => {
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


// compute current datetime up to minute for max value
const maxDateTime = computed(() => {
  var now = new Date()
  // Remove milliseconds
  now.setMilliseconds(0)
  // Format as "YYYY-MM-DDTHH:MM" or "YYYY-MM-DDTHH:MM:SS" if you want seconds
  return now.toISOString().slice(0, 19) // includes seconds: "YYYY-MM-DDTHH:MM:SS"
})


const filterDcr = () =>{
    

    totalDailyPayment.value = 0
    totalTuitionPayment.value = 0
    totalMiscPayment.value = 0
    payment.value = []

    let date = new Date(selectDateFrom.value);
    let formattedDateTo = selectDateTo.value.replace("T", " ");
    // Format as YYYY-MM-DD HH:MM:SS, convert yung may T
    let formattedDateFrom = date.getFullYear() + "-" +
    String(date.getMonth() + 1).padStart(2, '0') + "-" +
    String(date.getDate()).padStart(2, '0') + " " +
    String(date.getHours()).padStart(2, '0') + ":" +
    String(date.getMinutes()).padStart(2, '0') + ":" +
    String(date.getSeconds()).padStart(2, '0');

    if(formattedDateFrom > formattedDateTo){
        Swal.fire({
            icon: "warning",
            title: "Invalid Date Range",
            text: "The 'From' date cannot be later than the 'To' date.",
        });
    }else{
        fetchingCollection.value = true
        getAllPayments(transactionType.value, formattedDateFrom, formattedDateTo, userID.value).then((results) => {
            payment.value = results.data
            //sum all payment based sa date
            payment.value.forEach((pay) => {
                totalDailyPayment.value += parseFloat(pay.acy_payment)
                totalTuitionPayment.value += pay.acy_billtype == 1 ? 1: 0
                totalMiscPayment.value += pay.acy_billtype == 2 ? 1: 0
            })
            fetchingCollection.value = false

        })

    }

    // console.log(formattedDateFrom)
    // console.log(formattedDateTo)

   
}

const showExcelModal = ref(false)
const downloadExcel = () =>{

    let date = new Date(selectDateFrom.value);
    let formattedDateTo = selectDateTo.value.replace("T", " ");
    // Format as YYYY-MM-DD HH:MM:SS, convert yung may T
    let formattedDateFrom = date.getFullYear() + "-" +
    String(date.getMonth() + 1).padStart(2, '0') + "-" +
    String(date.getDate()).padStart(2, '0') + " " +
    String(date.getHours()).padStart(2, '0') + ":" +
    String(date.getMinutes()).padStart(2, '0') + ":" +
    String(date.getSeconds()).padStart(2, '0');

    if(formattedDateFrom > formattedDateTo){
        Swal.fire({
            icon: "warning",
            title: "Invalid Date Range",
            text: "The 'From' date cannot be later than the 'To' date. Select date first before generating excel ",
        });
    }else{
        showExcelModal.value = !showExcelModal.value
        document.getElementById('triggerModal').click();
    }
    
}

</script>
<template>
    <div>
        <div class="p-3 mb-4 border-bottom">
            <h5 class="text-uppercase fw-bold">Cashier's Daily Collection</h5>
        </div>

        <Loader v-if="preLoading" />
        <div v-else class="table-responsive border p-3 small-font">
            <div class="container-fluid py-3">

                <div class="row mb-3 border-0 border-bottom pb-3">
                    <div class=" col-lg-3 d-flex align-content-center justify-content-start">
                       <div class="d-flex flex-column align-items-start">
                            <label class="fw-bold">Date From</label>
                            <input 
                            type="datetime-local" 
                            class="form-control form-control-sm" 
                            v-model="selectDateFrom"
                            :max="maxDateTime"
                            />
                       </div>
                    </div>

                    <div class=" col-lg-3 d-flex align-content-center justify-content-start">
                        <div class="d-flex flex-column align-items-start">
                            <label class="fw-bold">Date To</label>
                            <input 
                            type="datetime-local" 
                            class="form-control form-control-sm" 
                            v-model="selectDateTo"
                            :max="maxDateTime"
                            />
                       </div>
                    </div>

                     <div class=" col-lg-3 d-flex align-content-center justify-content-start">
                        <div class="d-flex flex-column align-items-start">
                            <label class="fw-bold">Transaction Type</label>
                            <select class="form-select form-select-sm" v-model="transactionType">
                                <option value="0" selected>All Transactions</option>
                                <option value="1">Tuition</option>
                                <option value="2">Miscellaneous / Items</option>
                            </select>
                       </div>
                    </div>

                    <div class=" col-lg-3 d-flex align-content-center justify-content-end gap-2">
                        <div class="d-inline align-content-end justify-content-end">
                            <button class="btn btn-primary btn-sm" @click="filterDcr()">Load Collection</button>
                        </div>
                        <div class="d-inline align-content-end justify-content-end">
                            <button class="btn btn-success btn-sm" @click="downloadExcel()">Download Report</button>
                            <button v-show="false" id="triggerModal" data-bs-toggle="modal" data-bs-target="#dcrexcelmodal"></button>
                        </div>
                    </div>
                    
                </div>

                <div class="row g-3">
                    <!-- Bar Chart Imitation -->
                    <!-- <div class="col-lg-6 d-flex align-content-center justify-content-center">
                        <div class="card shadow-sm border-0 rounded-4 bg-body-secondary w-100">
                            <div class="card-body d-flex flex-column align-content-center justify-content-center">
                                <h6 class="fw-semibold mb-3">Total Collections (Week)</h6>

                                <div class="d-flex align-items-end justify-content-between" style="height: 160px;">
                                    <div class="bg-primary bg-opacity-25 rounded-2" style="width:10%;height:60%;"></div>
                                    <div class="bg-primary bg-opacity-50 rounded-2" style="width:10%;height:70%;"></div>
                                    <div class="bg-primary bg-opacity-75 rounded-2" style="width:10%;height:80%;"></div>
                                    <div class="bg-primary rounded-2" style="width:10%;height:100%;"></div>
                                    <div class="bg-primary bg-opacity-75 rounded-2" style="width:10%;height:85%;"></div>
                                    <div class="bg-primary bg-opacity-50 rounded-2" style="width:10%;height:70%;"></div>
                                    <div class="bg-primary bg-opacity-25 rounded-2" style="width:10%;height:50%;"></div>
                                </div>

                                <div class="d-flex justify-content-between text-muted small mt-2">
                                    <span>Mon</span><span>Tue</span><span>Wed</span><span>Thu</span><span>Fri</span><span>Sat</span><span>Sun</span>
                                </div>
                            </div>
                        </div>
                    </div> -->
                    <div class="col-lg-7 d-flex align-content-center justify-content-center">
                        <div class="card shadow-sm border-0 rounded-4 bg-body-secondary w-100">
                            <div class="card-body d-flex flex-column align-content-center justify-content-center">
                                <p class="fw-semibold mb-3">
                                Total Collection (Current Week on your Counter)</p>
                                <p class="text-muted small mt-2">
                                    [{{ formattedWeekStart }} → {{ formattedWeekEnd }}]
                                </p>
                                

                                <!-- Bars -->
                                <div class="d-flex align-items-end justify-content-between" style="height: 160px;">
                                    <div
                                        v-for="(height, index) in barHeights"
                                        :key="index"
                                        class="rounded-2 transition-all"
                                        :class="[
                                        'bg-success',
                                        days[index] === currentDay ? 'opacity-100' : 'bg-opacity-50'
                                        ]"
                                        :style="{ width: '10%', height: height + '%' }"
                                    >
                                    
                                    </div>
                                </div>

                                <!-- Day labels -->
                                <div class="d-flex justify-content-between text-muted small mt-2">

                                    <span v-for="(day, index) in ['Mon','Tue','Wed','Thu','Fri','Sat','Sun']" :key="day"  style="width: 10%">
                                        <span class="text-primary fw-bold">{{ new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP' }).format(weekCollection[index]) }}</span>
                                        <br/>
                                        {{ day }} 
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5 col-sm-12">
                        <div class="row g-3 mt-4 mb-4">
                            <div class="col-md-12 col-sm-6">
                                <div class="card shadow border-0 rounded-3">
                                    <div class="card-body">
                                        <p class="text-muted small mb-1">Total Earnings</p>
                                        <h2 class="fw-bold mb-0">{{ new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP' }).format(totalDailyPayment) }}</h2>
                                        <small class="text-secondary">as of the date</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-6">
                                <div class="card shadow border-0 rounded-3">
                                    <div class="card-body">
                                        <p class="text-muted small mb-1">Total Collections</p>
                                        <h2 class="fw-bold mb-0">{{ Object.keys(payment).length }}</h2>
                                        <small class="text-secondary">transactions</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-6">
                                <div class="card shadow border-0 rounded-3">
                                    <div class="card-body">
                                        <p class="text-muted small mb-1">Tuition Payment</p>
                                        <h2 class="fw-bold mb-0">{{ totalTuitionPayment }}</h2>
                                        <small class="text-secondary">transactions</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-6">
                                <div class="card shadow border-0 rounded-3">
                                    <div class="card-body">
                                        <p class="text-muted small mb-1">Misc Payment</p>
                                        <h2 class="fw-bold mb-0">{{ totalMiscPayment }}</h2>
                                        <small class="text-secondary">transactions</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 col-sm-6">
                                <div class="card shadow border-0 rounded-3">
                                    <div class="card-body">
                                        <p class="text-muted small mb-1">Receipt Series</p>
                                        <div class="p-2">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Receipt Type</th>
                                                        <th>Start Series</th>
                                                        <th>End Series</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="(rs, index) in receiptSeries">
                                                        <td>
                                                            <span v-if="rs.sr_receipt == 1">Official Receipt</span>
                                                            <span v-if="rs.sr_receipt == 2">Provisional Receipt</span>
                                                        </td>
                                                        <td>{{ rs.sr_prefix }}-{{ rs.sr_year }}-{{ rs.sr_start }}</td>
                                                        <td>{{ rs.sr_prefix }}-{{ rs.sr_year }}-{{ rs.sr_end }}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                
                <div class="table-responsive border p-3 mt-4">
                    <h6 class="fw-semibold mb-3">Transactions</h6>
                    <table class="table table-hover table-fixed" style="text-transform:uppercase">
                        <thead>
                            <tr>
                                <th style="background-color: #237a5b;" class="text-white">Payment ID</th>
                                <th style="background-color: #237a5b;" class="text-white">Full Name</th>
                                <th style="background-color: #237a5b;" class="text-white">Mode of Payment</th>
                                <th style="background-color: #237a5b;" class="text-white">Date of Payment</th>
                                <th style="background-color: #237a5b;" class="text-white">Transaction Type</th>
                                <th style="background-color: #237a5b;" class="text-white">Detail</th>
                                <th style="background-color: #237a5b;" class="text-white">Amount Paid</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="!fetchingCollection && Object.keys(payment).length" v-for="(pay, index) in payment">
                                <td class="align-middle p-2">
                                   {{ pay.acy_id }}
                                </td>
                                <td class="align-middle p-2">
                                    <span v-if="pay.acy_billtype == 1">
                                        {{ pay.per_firstname }} {{ pay.per_middlename? pay.per_middlename:' ' }} {{ pay.per_lastname }} {{pay.per_suffixname? pay.per_suffixname:' ' }}
                                    </span>
                                    <span v-if="pay.acy_billtype == 2">
                                        {{ pay.acr_personname }}
                                    </span>
                                </td>
                                <td class="align-middle p-2">
                                   <span v-if="pay.acy_mode == 1"> Cash</span>
                                   <span v-if="pay.acy_mode == 2"> Bank</span>
                                   <span v-if="pay.acy_mode == 3"> Cheque</span>
                                </td>
                                <td class="align-middle p-2">
                                   {{ pay.acy_datepaid.split('T')[0] }}
                                </td>
                                <td class="align-middle p-2">
                                    <span v-if="pay.acy_billtype == 1"> Tuition</span>
                                    <span v-if="pay.acy_billtype == 2"> Misc / Item</span>
                                </td>
                                <td class="align-middle p-2">
                                    <span v-if="pay.acy_billtype == 1">N/A</span>
                                    <span v-if="pay.acy_billtype == 2">{{ pay.acf_desc }}</span>
                                </td>
                                <td class="align-middle p-2 text-primary fw-bold">
                                    {{ new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP' }).format(pay.acy_payment) }}
                                </td>
                            </tr>    
                            <tr v-if="!fetchingCollection && !Object.keys(payment).length" style="text-transform:none">
                                <td class="p-3 text-center" colspan="7">
                                    No Records Found
                                </td>
                            </tr>
                            <tr v-if="fetchingCollection && !Object.keys(payment).length" style="text-transform:none">
                                <td class="p-3 text-center" colspan="7">
                                    <div class="m-3">
                                        <Loader />
                                    </div>
                                </td>
                            </tr>

                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>


    <!-- Report Modal -->
    <div class="modal fade" id="dcrexcelmodal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Daily Collection Report Download</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        @click="showExcelModal = false"></button>
                </div>

                <div class="modal-body small-font">
                    <ExcelDownloaderDCR v-if="showExcelModal"
                        :datefrom="selectDateFrom"
                        :dateto="selectDateTo"
                        :cashierid="cashierId"
                        :cashiername="cashierName"
                    />
                </div>

                <div class="modal-footer d-flex justify-content-between">
                    <div class="form-group">
                        <small id="emailHelp" class="form-text text-muted">We'll never share your personal information
                            with anyone
                            else (Data Privacy Act of 2012)</small>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                            @click="showExcelModal = false">Close</button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</template>