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
import { getUserID } from "../../routes/user.js";
import { useRouter, useRoute } from 'vue-router'
import { counter } from '@fortawesome/fontawesome-svg-core';


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
const countersData = ref([])
const totalCountersAmount = ref(0)
// for bar chart
const formattedWeekStart = ref('')
const formattedWeekEnd = ref('')
const currentDay = ref('')
const days = ref([])
const barHeights = ref([])
const weekCollection = ref([])
const showSeriesModal = ref(false)

const booter = async () => {
    // getAllPayments(transactionType.value, '2025-10-05', '2025-11-05', userID.value, 1).then((results) => {
    getAllPayments(transactionType.value, dateFrom.value, dateToday.value, userID.value, 1).then((results) => {
        payment.value = results.data

        //sum all payment based sa date
        payment.value.forEach((pay) => {
            totalDailyPayment.value += parseFloat(pay.acy_payment)
            totalTuitionPayment.value += pay.acy_billtype == 1 ? 1: 0
            totalMiscPayment.value += pay.acy_billtype == 2 ? 1: 0
        })
    })

    getSetSeries(1).then((results) => {
        console.log(results.data)
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
        accessData.value = results1.access

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


                    getAllPayments(transactionType.value, formattedWeekStart.value, formattedWeekEnd.value, userID.value, 1).then((results) => {

                        // group counters per cashier para makuha yung collections per counter
                        let raw = results.data;
                        let counters = Object.groupBy(raw, r => r.acy_cashier);

                        // Build summarized totals
                        let cashierTotals = Object.keys(counters).map(cashierId => {
                            const group = counters[cashierId];

                            // Sum all payments for this cashier
                            const totalAmount = group.reduce((sum, item) => sum + item.acy_payment, 0);

                            // Get cashier name (from emp_* fields)
                            const cashierName = `${group[0].emp_firstname} ${group[0].emp_lastname}`;

                            return {
                                cashier: cashierName,
                                cashierId: cashierId,
                                amount: totalAmount
                            };
                        });

                        countersData.value = cashierTotals
                        // get lahat ng total collections amount ng counters
                        totalCountersAmount.value = countersData.value.reduce((sum, item) => sum + item.amount, 0);

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
        getAllPayments(transactionType.value, formattedDateFrom, formattedDateTo, userID.value, 1).then((results) => {
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

const seriesPrYear = new Date().getFullYear();
const seriesPrPrefix = ref('PR')
const seriesPrStart = ref('000001')
const seriesPrEnd = ref('001000')

const seriesOrYear = new Date().getFullYear();
const seriesOrPrefix = ref('OR')
const seriesOrStart = ref('000001')
const seriesOrEnd = ref('001000')
// const seriesStartNumber = ref('')
// const seriesEndNumber = ref('')

const editSeriesCashierName = ref('')
const seriesData = ref([])
const showSeries = ref(false)
const getSeries = (data) =>{
    showSeriesModal.value = !showSeriesModal.value
    showSeries.value = true
    editSeriesCashierName.value = data.cashier

    getSetSeries(data.cashierId).then((results) => {
        seriesData.value = results.data
        console.log(seriesData.value)
        showSeries.value = false
    })
}

const saveSeries = (data) =>{
    console.log(data)
}


</script>
<template>
    <div>
        <div class="p-3 mb-4 border-bottom">
            <h5 class="text-uppercase fw-bold">Counters Daily Collection</h5>
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

                    <div class=" col-lg-3 d-flex align-content-center justify-content-end">
                       <div class="d-inline align-content-end justify-content-end">
                            <button class="btn btn-primary btn-sm me-2" @click="filterDcr()">Load Collection</button>
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
                    <div class="col-lg-7 d-flex flex-column align-content-center justify-content-center">
                        <div class="row g-3 mt-4 mb-4">
                            <div class="col-md-4 col-sm-6">
                                <div class="card shadow border-0 rounded-3">
                                    <div class="card-body">
                                        <p class="text-muted small mb-1">Total Counters</p>
                                        <h2 class="fw-bold mb-0" >{{ Object.keys(countersData).length }}</h2>
                                        <small class="text-secondary">actively working</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-8 col-sm-6">
                                <div class="card shadow border-0 rounded-3">
                                    <div class="card-body">
                                        <p class="text-muted small mb-1">Total Payment collected</p>
                                        <h2 class="fw-bold mb-0 text-primary">{{ new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP' }).format(totalCountersAmount) }}</h2>
                                        <small class="text-secondary">All Counters this week</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card shadow-sm border-0 rounded-4 bg-body-secondary w-100">
                            <div class="card-body d-flex flex-column align-content-center justify-content-center">
                                <p class="fw-semibold mb-3">
                                Total Collection (all counters week collection)
                                <p class="text-muted small mt-2">
                                    [{{ formattedWeekStart }} → {{ formattedWeekEnd }}]
                                </p>
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
                    <div class="col-lg-5 col-sm-6">
                        <div class="row g-3 mt-4 mb-4">
                            <div class="col-md-12 col-sm-6">
                                <div class="card shadow border-0 rounded-3">
                                    <div class="card-body">
                                        <p class="text-muted small mb-1">Total Earnings</p>
                                        <h2 class="fw-bold mb-0 text-success">{{ new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP' }).format(totalDailyPayment) }}</h2>
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
                                        <h2 class="fw-bold mb-0 text-primary">{{ totalTuitionPayment }}</h2>
                                        <small class="text-secondary">transactions</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-6">
                                <div class="card shadow border-0 rounded-3">
                                    <div class="card-body">
                                        <p class="text-muted small mb-1">Misc Payment</p>
                                        <h2 class="fw-bold mb-0 text-warning">{{ totalMiscPayment }}</h2>
                                        <small class="text-secondary">transactions</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 col-sm-6">
                                <div class="card shadow border-0 rounded-3">
                                    <div class="card-body">
                                        <p class="text-muted small mb-1">Active Cashiers</p>
                                        <div style="height:160px; overflow: auto;" class="p-2">
                                            <ul class="list-group text-uppercase">
                                                <li class="list-group-item">
                                                   <div class="d-flex justify-content-between fw-bold">
                                                        <small>Name</small> <small>This Week Collection</small>
                                                   </div>
                                                </li>
                                                <li class="list-group-item" v-for="(cd, index) in countersData">
                                                   <div class="d-flex justify-content-between align-items-center">
                                                        <span>{{ cd.cashier }}</span> → <span class="fw-bold text-primary">{{ new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP' }).format(cd.amount) }}</span>
                                                        <button @click="getSeries(cd)" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#seriesmodal">Edit Series</button>
                                                    </div>
                                                </li>
                                            </ul>
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
                                <th style="background-color: #237a5b;" class="text-white">Cashier</th>
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
                                <td class="align-middle p-2 ">
                                    {{ pay.emp_firstname }} {{ pay.emp_lastname }}
                                </td>
                            </tr>    
                            <tr v-if="!fetchingCollection && !Object.keys(payment).length" style="text-transform:none">
                                <td class="p-3 text-center" colspan="8">
                                    No Records Found
                                </td>
                            </tr>
                            <tr v-if="fetchingCollection && !Object.keys(payment).length" style="text-transform:none">
                                <td class="p-3 text-center" colspan="8">
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


    <!-- Series Modal -->
    <div class="modal fade" id="seriesmodal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Set Series</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        @click="showSeriesModal = false"></button>
                </div>
                <div class="modal-body">
                    <div class="card shadow border-0 rounded-3">
                        <div class="card-body">
                            <div v-if="!showSeries" class="p-2">
                                <div class="d-flex flex-wrap flex-column">
                                    <p class="text-success fw-bold">Series Setup</p>
                                    <p class=" fst-italic border p-3 rounded-3 bg-secondary-subtle small-font"><span
                                            class="fw-bold">Note:
                                        </span><span class="italic">The series here must follow the institutional
                                            policy on
                                            official and provisional receipt numbering.
                                            Please ensure that the series numbers are unique and sequential to avoid any
                                            discrepancies in financial records.
                                        </span></p>
                                </div>
                                <div class="mb-3">
                                    <label for="name1" class="form-label">Cashier Name</label>
                                    <input type="email" class="form-control" id="name1" disabled v-model="editSeriesCashierName">
                                </div>

                               <div class="row g-3 border p-2 mt-2">
                                    <div class="col-12 col-lg-6">
                                        <!-- OR Start -->
                                        <form @submit.prevent="saveSeries(1)">
                                            <div class="col-12 text-start">
                                                <label class="form-label"><small>Official Receipt Series Start</small></label>
                                                <div class="row g-1">
                                                    <div class="col-3">
                                                        <input type="text" class="form-control form-control-sm text-center" :value="seriesOrPrefix + '-' + seriesOrYear " disabled/>
                                                    </div>
                                                    <div class="col-1 text-center d-flex justify-content-center align-items-center">
                                                        -
                                                    </div>
                                                    <div class="col-8">
                                                        <input type="number" class="form-control form-control-sm text-center" v-model="seriesOrStart" required>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- OR End -->
                                            <div class="col-12 text-start mt-2">
                                                <label class="form-label"><small>Official Receipt Series End</small></label>
                                                <div class="row g-1">
                                                    <div class="col-3">
                                                        <input type="text" class="form-control form-control-sm text-center" :value="seriesOrPrefix + '-' + seriesOrYear " disabled/>
                                                    </div>
                                                    <div class="col-1 text-center d-flex justify-content-center align-items-center">
                                                        -
                                                    </div>
                                                    <div class="col-8">
                                                        <input type="number" class="form-control form-control-sm text-center" v-model="seriesOrEnd" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mt-3">
                                                <button type="submit" class="btn btn-sm btn-success w-100">Save Official Receipt Series</button>
                                            </div>
                                        </form>
                                    </div>

                                    <div class="col-12 col-lg-6">
                                        <form @submit.prevent="saveSeries(2)">
                                            <div class="col-12 text-start">
                                                <label class="form-label"><small>Provisional Receipt Series Start</small></label>
                                                <div class="row g-1">
                                                    <div class="col-3">
                                                        <input type="text" class="form-control form-control-sm text-center" :value="seriesPrPrefix + '-' + seriesPrYear " disabled/>
                                                    </div>
                                                    <div class="col-1 text-center d-flex justify-content-center align-items-center">
                                                        -
                                                    </div>
                                                    <div class="col-8">
                                                        <input type="number" class="form-control form-control-sm text-center" v-model="seriesPrStart" required>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- OR End -->
                                            <div class="col-12 text-start mt-2">
                                                <label class="form-label"><small>Provisional Receipt Series End</small></label>
                                                <div class="row g-1">
                                                    <div class="col-3">
                                                        <input type="text" class="form-control form-control-sm text-center" :value="seriesPrPrefix + '-' + seriesPrYear " disabled/>
                                                    </div>
                                                    <div class="col-1 text-center d-flex justify-content-center align-items-center">
                                                        -
                                                    </div>
                                                    <div class="col-8">
                                                        <input type="number" class="form-control form-control-sm text-center" v-model="seriesPrEnd" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mt-3">
                                                <button type="submit" class="btn btn-sm btn-success w-100">Save Provisional Receipt Series</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                            </div>
                            <div v-else class="d-flex justify-content-center align-items-center">
                                <div class="m-3">
                                    <Loader />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <div class="form-group">
                        <small id="emailHelp" class="form-text text-muted">Never share your personal or our information
                            with anyone
                            else (Data Privacy Act of 2012)</small>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                            @click="showSeriesModal = false">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>