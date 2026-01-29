<script setup>
import { ref, onMounted, computed } from 'vue';
import {
    getAllPayments,
    getCurrentWeekDailyCollection,
    dateFormatterWord,
    getBarHeights,
    getSetSeries,
    saveSetSeries,
    getCashiersDetails,
    turnOffCollection,
    getCollectionStatus
} from "../Fetchers.js";

import {
    pesoConverter
} from "../Generators.js";

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
const receiptOrSeries = ref([])
const receiptPrSeries = ref([])
const receiptSeries = ref([])
const employeeCashier = ref([])
const graphColor = ref('bg-success')
const textColorHead = ref('enabled-text')


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

    getSetSeries(0).then((results) => {
        receiptOrSeries.value.push(...results.or_series)
        receiptPrSeries.value.push(...results.pr_series)
        receiptSeries.value.push(...results.or_series, ...results.pr_series)    
        // console.log(receiptOrSeries.value)
        // console.log(receiptPrSeries.value)
    })

    getCashiersDetails(3).then((results) => {
        employeeCashier.value = results.data
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


 
const checkHighlights = () =>{
  graphColor.value = counterStatus.value == false? 'bg-secondary-subtle':'bg-success'
  textColorHead.value = counterStatus.value == false? 'disabled-text':'enabled-text'
}
onMounted(async () => {

    // var date = new Date();
    // var day = date.getDate();
    // var month = date.getMonth() + 1;
    // var year = date.getFullYear();
    // dateToday.value = date.toISOString().split('T')[0]

    getDateToday()
    selectDateTo.value = dateToday.value

    getCollectionStatus().then((results)=>{
        counterStatus.value = results.data.sett_status == 0? false : true
        checkHighlights()
    })

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
                                amount: totalAmount,
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

const textColor = ref('')

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

const seriesPrYear = ref('');
const seriesPrPrefix = ref('')
const seriesPrStart = ref('')
const seriesPrEnd = ref('')

const seriesOrYear = ref('')
const seriesOrPrefix = ref('')
const seriesOrStart = ref('')
const seriesOrEnd = ref('')
// const seriesStartNumber = ref('')
// const seriesEndNumber = ref('')

const editSeriesCashierName = ref('')
const editSeriesCashierId = ref('')
const seriesPrData = ref([])
const seriesOrData = ref([])
const showSeries = ref(false)

const getSeries = (data) =>{
    // console.log(data)
    showSeriesModal.value = !showSeriesModal.value
    showSeries.value = true
    editSeriesCashierName.value = [
        data.emp_firstname,
        data.emp_middlename || "",
        data.emp_lastname,
        data.emp_suffixname || ""
    ].filter(Boolean).join(" ");
    editSeriesCashierId.value = data.emp_accid

    getSetSeries(data.emp_accid).then((results) => {

        let year = new Date().getFullYear();

        seriesOrData.value = results.or_series.length > 0 ? results.or_series[0] : {}
        seriesPrData.value = results.pr_series.length > 0 ? results.pr_series[0] : {}

        seriesOrPrefix.value = seriesOrData.value.sr_prefix?seriesOrData.value.sr_prefix:'OR'
        seriesOrYear.value = seriesOrData.value.sr_year?seriesOrData.value.sr_year:year
        seriesOrStart.value = seriesOrData.value.sr_start?seriesOrData.value.sr_start:null
        seriesOrEnd.value = seriesOrData.value.sr_end?seriesOrData.value.sr_end:null

        seriesPrPrefix.value = seriesPrData.value.sr_prefix?seriesPrData.value.sr_prefix:'PR'
        seriesPrYear.value = seriesPrData.value.sr_year?seriesPrData.value.sr_year:year
        seriesPrStart.value = seriesPrData.value.sr_start?seriesPrData.value.sr_start:null
        seriesPrEnd.value = seriesPrData.value.sr_end?seriesPrData.value.sr_end:null

        showSeries.value = false
    })
}


const savingSeries = ref(false)
const saveSeries = (data) =>{

    savingSeries.value = true

    Swal.fire({
        title: "Saving Updates",
        text: "Please wait while we check all necessary details.",
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    var x = {}
    let ormode = Object.keys(seriesOrData.value).length > 0 ? 2 : 1 // 1 for insert, 2 for update
    let prmode = Object.keys(seriesPrData.value).length > 0 ? 2 : 1 // 1 for insert, 2 for update

    if(data == 1){

        //OR Series
        let srid = ''
        if(ormode == 2 && seriesOrData.value.sr_receipt == data){
            srid = seriesOrData.value.sr_id
        }else{
            srid = null
            ormode = 1
        }

        var x = {
            cashierId: editSeriesCashierId.value,
            sr_receipt:data,
            sr_or_prefix: seriesOrPrefix.value,
            sr_or_year: seriesOrYear.value,
            sr_or_start: seriesOrStart.value,
            sr_or_end: seriesOrEnd.value,
            sr_user: userID.value,
            sr_mode:ormode,
            sr_id: srid
        }

    }else{

        //PR Series
        let srid = ''
        if(prmode == 2 && seriesPrData.value.sr_receipt == data){
            srid = seriesPrData.value.sr_id
        }else{
            srid = null
            prmode = 1
        }

        var x = {
            cashierId: editSeriesCashierId.value,
            sr_receipt: data,
            sr_pr_prefix: seriesPrPrefix.value,
            sr_pr_year: seriesPrYear.value,
            sr_pr_start: seriesPrStart.value,
            sr_pr_end: seriesPrEnd.value,
            sr_user: userID.value,
            sr_mode: prmode,
            sr_id: srid
        }

    }

    saveSetSeries(x, data).then((results) => {
        Swal.close();
        if(results.status == 200){
            Swal.fire({
                icon: "success",
                title: "Success",
                text: "Series saved successfully.",
            }).then(() => {
                hideMyModal()
                savingSeries.value = false
            })
        }else{
            Swal.fire({
                icon: "error",
                title: "Error",
                text: "Failed to save series. Please try again.",
            })
        }
    })
}

const hideMyModal = () => {
    document.getElementById('closeSeriesBtn').click();
};


const counterStatus = ref(false)
const turnOffCounters = () => {
    let title = counterStatus.value == true?'Notice on Turning OFF Counters':'Notice on Turning ON Counters'
    let confirmationtext = counterStatus.value == true?'Yes, turn OFF Counters':'Yes, turn ON Counters'
    let canceltext = counterStatus.value == true?'No, dont turn OFF Counters':'No, dont turn ON Counters'
    let btncolor = counterStatus.value == true?'#cc103c':'#07db6a'
    let successtext =  counterStatus.value == true?'Turn Off Successful':'Turn On Successful'

    Swal.fire({
        title: title,
        html: `Are you sure to update counters from collecting payments?`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: btncolor,
        cancelButtonText: canceltext,
        confirmButtonText: confirmationtext
    }).then(async (result) => {
        if (result.isConfirmed) {

            // Show loading alert first
            Swal.fire({
                title: "Saving Updates",
                text: "Please wait while we update all counters.",
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            try {
                // Flip status locally
                let newStatus = !counterStatus.value;
                let payload = {
                    status: newStatus ? 1 : 0,
                    user_id: userID.value
                };

                // Wait for async request
                const results = await turnOffCollection(payload);

                // Update counter status based on server response
                counterStatus.value = results.data == 1;

                // Close loading alert
                Swal.close();

                // Show success or error alert
                if (results.status == 200) {
                    Swal.fire({
                        title: successtext,
                        text: "Counters status is updated",
                        icon: "success"
                    });

                    checkHighlights()
                } else {
                    Swal.fire({
                        title: "Error",
                        text: "Server Error, Try Again Later",
                        icon: "error"
                    });
                }
            } catch (error) {
                Swal.close();
                Swal.fire({
                    title: "Error",
                    text: "Something went wrong. Please try again.",
                    icon: "error"
                });
            }
        }
    });
}


const validateData = (type, value) =>{
    switch(type){
        case 'or-start':
            if(seriesOrStart.value && parseInt(value) > parseInt(seriesOrEnd.value)){
                Swal.fire({
                    icon: "warning",
                    title: "Invalid Series Range",
                    text: "The 'Start' series cannot be greater than the 'End' series.",
                });
                seriesOrStart.value = ''
            }
            break;
        case 'or-end':
            if(seriesOrEnd.value && parseInt(value) < parseInt(seriesOrStart.value)){
                Swal.fire({
                    icon: "warning",
                    title: "Invalid Series Range",
                    text: "The 'End' series cannot be less than the 'Start' series.",
                });
                seriesOrEnd.value = ''
            }
            break;
        case 'pr-start':
            if(seriesPrStart.value && parseInt(value) > parseInt(seriesPrEnd.value)){
                Swal.fire({
                    icon: "warning",
                    title: "Invalid Series Range",
                    text: "The 'Start' series cannot be greater than the 'End' series.",
                });
                seriesPrStart.value = ''
            }
            break;
        case 'pr-end':
            if(seriesPrEnd.value && parseInt(value) < parseInt(seriesPrStart.value)){
                Swal.fire({
                    icon: "warning",
                    title: "Invalid Series Range",
                    text: "The 'End' series cannot be less than the 'Start' series.",
                });
                seriesPrEnd.value = ''
            }
            break;
    }
}
</script>
<template>
    <div id="main-component">
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
                            <!-- <div class="form-check form-switch">
                                <input 
                                    class="form-check-input"
                                    type="checkbox"
                                    role="switch"
                                    id="counterSwitch"
                                    :checked="counterStatus"
                                    @change="onSwitchToggle"
                                >
                                <label class="form-check-label" for="counterSwitch">
                                    {{ counterStatus ? 'Counters are ON' : 'Counters are OFF' }}
                                </label>
                            </div> -->
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
                    <div class="col-lg-7 d-flex flex-column align-content-center justify-content-start">
                        
                        <div class="row g-3 mt-4 mb-3">
                            <div class="col-md-3 col-sm-6">
                                <div class="card shadow border-0 rounded-3">
                                    <div class="card-body">
                                        <button :class="counterStatus == true? 'btn btn-success btn-sm':'btn btn-danger btn-sm'" @click="turnOffCounters()">
                                            <font-awesome-icon icon="fa-solid fa-power-off" />
                                        </button>
                                        <p :class="counterStatus == true? 'small mb-1 mt-2 fw-bold enabled-text':'small mb-1 mt-2 fw-bold text-danger'">
                                            {{ counterStatus == true ?'ACTIVATED': 'DEACTIVATED' }}</p>
                                        <small class="text-secondary">{{ counterStatus == true ?'Counters are Active': 'Counters are Inactive' }}</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <div class="card shadow border-0 rounded-3">
                                    <div class="card-body">
                                        <p class="text-muted small mb-1">Total Counters</p> 
                                        <h2 :class="['fw-bold', 'mb-0', textColorHead]">{{ Object.keys(countersData).length }}</h2>
                                        <small class="text-secondary">
                                            {{ counterStatus == true? 'actively working' : 'are inactive' }}
                                        </small>
                                        
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-6">
                                <div class="card shadow border-0 rounded-3">
                                    <div class="card-body">
                                        <p class="text-muted small mb-1">Total Payment collected</p>
                                        <h2 :class="['fw-bold', 'mb-0', textColorHead]">{{ pesoConverter(totalCountersAmount) }}</h2>
                                        <small class="text-secondary">All Counters this week</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card shadow-sm border-0 rounded-4 bg-body-secondary w-100">
                            <div class="card-body d-flex flex-column align-content-center justify-content-center">
                                <p class="fw-semibold mb-3">
                                    Total Collection (all counters week collection)
                                </p>
                                <p class="text-muted small mt-2">
                                    [{{ formattedWeekStart }} → {{ formattedWeekEnd }}]
                                </p>

                                <!-- Bars -->
                                <div class="d-flex align-items-end justify-content-between" style="height: 191px;">
                                    <div
                                        v-for="(height, index) in barHeights"
                                        :key="index"
                                        class="rounded-2 transition-all"
                                        :class="[
                                        graphColor,
                                        days[index] === currentDay ? 'opacity-100' : 'bg-opacity-50'
                                        ]"
                                        :style="{ width: '10%', height: height + '%' }"
                                    >
                                    </div>
                                </div>

                                <!-- Day labels -->
                                <div class="d-flex justify-content-between text-muted small mt-2">

                                    <span v-for="(day, index) in ['Mon','Tue','Wed','Thu','Fri','Sat','Sun']" :key="day"  style="width: 10%">
                                        <span class="text-primary fw-bold">{{ pesoConverter(weekCollection[index]) }}</span>
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
                                        <h2 :class="['fw-bold', 'mb-0', textColorHead]">{{ pesoConverter(totalDailyPayment) }}</h2>
                                        <small class="text-secondary">as of the date</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-6">
                                <div class="card shadow border-0 rounded-3">
                                    <div class="card-body">
                                        <p class="text-muted small mb-1">Total Collections</p>
                                        <h2 :class="['fw-bold', 'mb-0', textColorHead]">{{ Object.keys(payment).length }}</h2>
                                        <small class="text-secondary">transactions</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-6">
                                <div class="card shadow border-0 rounded-3">
                                    <div class="card-body">
                                        <p class="text-muted small mb-1">Tuition Payment</p>
                                        <h2 :class="['fw-bold', 'mb-0', textColorHead]">{{ totalTuitionPayment }}</h2>
                                        <small class="text-secondary">transactions</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-6">
                                <div class="card shadow border-0 rounded-3">
                                    <div class="card-body">
                                        <p class="text-muted small mb-1">Misc Payment</p>
                                        <h2 :class="['fw-bold', 'mb-0', textColorHead]">{{ totalMiscPayment }}</h2>
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
                                                        <span>{{ cd.cashier }}</span> → 
                                                        <span class="fw-bold text-primary">{{ pesoConverter(cd.amount) }}</span>
                                                        <!-- <button @click="getSeries(cd)" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#seriesmodal">Edit Series</button> -->
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
                <div class="row g-3">
                    <div class="col-md-8 col-sm-6">
                        <div class="card shadow border-0 rounded-3">
                            <div class="card-body">
                                <p class="text-muted small mb-1">Counters w/ Corresponding Receipt Series</p>
                                <div style="height:200px; overflow: auto;" class="p-2">
                                    <table class="table table-bordered">
                                        <thead>
                                           <tr>
                                                <th>Cashier</th>
                                                <th>Start Series</th>
                                                <th>End Series</th>
                                                <th>Receipt Type</th>
                                           </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(rs, index) in receiptSeries">
                                                <td class="text-uppercase">{{ rs.fullname }}</td>
                                                <td>{{ rs.sr_prefix }}-{{ rs.sr_year }}-{{ rs.sr_start }}</td>
                                                <td>{{ rs.sr_prefix }}-{{ rs.sr_year }}-{{ rs.sr_end }}</td>
                                                <td>
                                                    <span v-if="rs.sr_receipt == 1">Official Receipt</span>
                                                    <span v-if="rs.sr_receipt == 2">Provisional Receipt</span>
                                                </td>
                                            </tr>
                                            <tr v-if="!Object.keys(receiptSeries).length">
                                                <td colspan="4">
                                                    Active Counters does not have updated series
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>     
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <div class="card shadow border-0 rounded-3">
                            <div class="card-body">
                                <p class="text-muted small mb-1">Cashiers</p>
                                <div style="height:200px; overflow: auto;" class="p-2">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Cashier</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(emp, index) in employeeCashier">
                                                <td class="text-uppercase align-middle">
                                                    <span>{{ emp.emp_firstname }} {{ emp.emp_lastname }}</span>
                                                </td>
                                                <td>
                                                    <span v-if="!counterStatus" class="btn btn-sm btn-success" @click="getSeries(emp)" data-bs-toggle="modal" data-bs-target="#seriesmodal">Edit Series</span>
                                                    <span v-else class="fw-bold text-success" title="to edit series, deactivate all counters first">Active</span>
                                                </td>
                                            </tr>
                                            <tr v-if="!Object.keys(employeeCashier).length">
                                                <td colspan="4">
                                                    No Active Counters
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>     
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
                                                        <input type="text" class="form-control form-control-sm text-center"  :value="seriesOrPrefix + '-' + seriesOrYear " disabled/>
                                                    </div>
                                                    <div class="col-1 text-center d-flex justify-content-center align-items-center">
                                                        -
                                                    </div>
                                                    <div class="col-8">
                                                        <!-- <input type="text" :disabled="savingSeries" class="form-control form-control-sm text-center" v-model="seriesOrStart" required> -->
                                                        <!-- <input 
                                                            v-model.number="seriesOrStart"
                                                            required
                                                            step="0" 
                                                            min="0"
                                                            :max="seriesOrEnd"
                                                            :disabled="savingSeries"
                                                            @focusout="
                                                            if (seriesOrStart <= 0) seriesOrStart = Number(seriesOrEnd) - 10, console.log(seriesOrStart);
                                                            if (seriesOrStart >= seriesOrEnd) seriesOrStart = (Number(seriesOrEnd)-10);
                                                            "
                                                            type="number"
                                                            class="form-control form-control-sm text-center"
                                                        /> -->
                                                        <input 
                                                            v-model.number="seriesOrStart"
                                                            required
                                                            step="0" 
                                                            min="0"
                                                            :max="seriesOrEnd"
                                                            :disabled="savingSeries"
                                                            @focusout="validateData('or-start', seriesOrStart)"
                                                            type="number"
                                                            class="form-control form-control-sm text-center"
                                                        />
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- OR End -->
                                            <div class="col-12 text-start mt-2">
                                                <label class="form-label"><small>Official Receipt Series End</small></label>
                                                <div class="row g-1">
                                                    <div class="col-3">
                                                        <input type="text" class="form-control form-control-sm text-center"  :value="seriesOrPrefix + '-' + seriesOrYear " disabled/>
                                                    </div>
                                                    <div class="col-1 text-center d-flex justify-content-center align-items-center">
                                                        -
                                                    </div>
                                                    <div class="col-8">
                                                        <!-- <input type="text" :disabled="savingSeries" class="form-control form-control-sm text-center" v-model="seriesOrEnd" required> -->
                                                        <!-- <input 
                                                            v-model.number="seriesOrEnd"
                                                            required
                                                            step="0" 
                                                            :min="Number(seriesOrStart) + 10"
                                                            :disabled="savingSeries"
                                                            @focusout="
                                                            if (seriesOrStart <= 0 || !seriesOrStart) seriesOrStart = Number(seriesOrEnd) - 10;
                                                            if (seriesOrEnd <= seriesOrStart ) seriesOrEnd = Number(seriesOrStart) + 10;
                                                            "
                                                            type="number"
                                                            class="form-control form-control-sm text-center"
                                                        /> -->
                                                        <input 
                                                            v-model.number="seriesOrEnd"
                                                            required
                                                            step="0" 
                                                            :min="Number(seriesOrStart) + 10"
                                                            :disabled="savingSeries"
                                                            @focusout="validateData('or-end', seriesOrEnd)"
                                                            type="number"
                                                            class="form-control form-control-sm text-center"
                                                        />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mt-3">
                                                <button v-show="!savingSeries" type="submit" class="btn btn-sm btn-success w-100">Save Official Receipt Series</button>
                                            </div>
                                        </form>
                                    </div>

                                    <div class="col-12 col-lg-6">
                                        <form @submit.prevent="saveSeries(2)">
                                            <div class="col-12 text-start">
                                                <label class="form-label"><small>Provisional Receipt Series Start</small></label>
                                                <div class="row g-1">
                                                    <div class="col-3">
                                                        <input type="text" class="form-control form-control-sm text-center"  :value="seriesPrPrefix + '-' + seriesPrYear " disabled/>
                                                    </div>
                                                    <div class="col-1 text-center d-flex justify-content-center align-items-center">
                                                        -
                                                    </div>
                                                    <div class="col-8">
                                                        <!-- <input type="text" :disabled="savingSeries" class="form-control form-control-sm text-center" v-model="seriesPrStart" required> -->
                                                        <!-- <input 
                                                            v-model.number="seriesPrStart"
                                                            required
                                                            step="0" 
                                                            min="0"
                                                            :max="seriesPrEnd"
                                                            :disabled="savingSeries"
                                                            @focusout="
                                                            if (seriesPrStart <= 0) seriesPrStart = Number(seriesPrEnd) - 10;
                                                            "
                                                            @input="
                                                            if (seriesPrStart >= seriesPrEnd) seriesPrStart = (Number(seriesPrEnd)-10);
                                                            "
                                                            type="number"
                                                            class="form-control form-control-sm text-center"
                                                        /> -->
                                                        <input 
                                                            v-model.number="seriesPrStart"
                                                            required
                                                            step="0" 
                                                            min="0"
                                                            :max="seriesPrEnd"
                                                            :disabled="savingSeries"
                                                            @focusout="validateData('pr-start', seriesPrStart)"
                                                            type="number"
                                                            class="form-control form-control-sm text-center"
                                                        />
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- OR End -->
                                            <div class="col-12 text-start mt-2">
                                                <label class="form-label"><small>Provisional Receipt Series End</small></label>
                                                <div class="row g-1">
                                                    <div class="col-3">
                                                        <input type="text" class="form-control form-control-sm text-center"  :value="seriesPrPrefix + '-' + seriesPrYear " disabled/>
                                                    </div>
                                                    <div class="col-1 text-center d-flex justify-content-center align-items-center">
                                                        -
                                                    </div>
                                                    <div class="col-8">
                                                        <!-- <input type="text" :disabled="savingSeries" class="form-control form-control-sm text-center" v-model="seriesPrEnd" required> -->
                                                        <!-- <input 
                                                            v-model.number="seriesPrEnd"
                                                            required
                                                            step="0" 
                                                            :min="Number(seriesPrStart) + 10"
                                                            :disabled="savingSeries"
                                                            @focusout="
                                                            if (seriesPrStart <= 0 || !seriesPrStart) seriesPrStart = Number(seriesPrEnd) - 10;
                                                            if (seriesPrEnd <= seriesPrStart ) seriesPrEnd = Number(seriesPrStart) + 10;
                                                            "
                                                            type="number"
                                                            class="form-control form-control-sm text-center"
                                                        /> -->
                                                        <input 
                                                            v-model.number="seriesPrEnd"
                                                            required
                                                            step="0" 
                                                            :min="Number(seriesPrStart) + 10"
                                                            :disabled="savingSeries"
                                                            @focusout="validateData('pr-end', seriesPrEnd)"
                                                            type="number"
                                                            class="form-control form-control-sm text-center"
                                                        />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mt-3">
                                                <button v-show="!savingSeries" type="submit" class="btn btn-sm btn-success w-100">Save Provisional Receipt Series</button>
                                            </div>
                                        </form>
                                    </div>
                                    <p class="fw-bold" v-show="savingSeries">Saving Series...</p>
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
                        <button type="button" class="btn btn-secondary" id="closeSeriesBtn" data-bs-dismiss="modal"
                            @click="showSeriesModal = false">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>

.disabled-text {
    color: #b5b5b5;
}
.disabled-bg {
    background-color: #b5b5b5;
}

.enabled-text{
    color:#2b2b2b;
}
.enabled-bg {
    background-color: #2b2b2b;
}
</style>