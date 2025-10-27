<script setup>
import { ref, onMounted, computed } from 'vue';
import {
    addCurriculum, addCurriculumTagging, getCurriculumSubject, setAcademicStatus, getAcademicStatus, setCommandUpdate
} from "../../Fetchers.js";
import Loading1 from '../loaders/Loading1.vue';
import Loader1 from '../loaders/Loader1.vue';
import CommandCenterModal from '../modal/CommandCenterModal.vue';
import AcademicYear from './commandcenterforms/AcademicYear.vue';
import AcademicSemester from './commandcenterforms/AcademicSemester.vue';
import AcademicSection from './commandcenterforms/AcademicSection.vue';
import AcademicDownpayment from './commandcenterforms/AcademicDownpayment.vue';
import AcademicCurriculum from './commandcenterforms/AcademicCurriculum.vue';
import AcademicGrades from './commandcenterforms/AcademicGrades.vue';
import AcademicGradingTerm from './commandcenterforms/AcademicGradingTerm.vue';

const props = defineProps({
    subjectData: {
    },
    curriculumData: {
    },
    courseData: {
    },
    programData: {
    },
    quarterData: {
    },
    gradelvlData: {
    },
    userIdData: {
    },
    title: {
    }
})

const subject = computed(() => {
    return props.subjectData
});
const curriculum = computed(() => {
    return props.curriculumData
});
const program = computed(() => {
    return props.programData
});
const quarter = computed(() => {
    return props.quarterData
});
const course = computed(() => {
    return props.courseData
});
const gradelvl = computed(() => {
    return props.gradelvlData
});
const userID = computed(() => {
    return props.userIdData
});

const showCommandCenterModal = ref(false)
const formValue = ref(0)
const preLoading = ref(false)
const execute = (type, mode) => {
    formValue.value = 0
    showCommandCenterModal.value = true
    switch (type) {
        case 1:
            switch (mode) {
                case 1:
                    formValue.value = 1
                    preLoading.value=false
                    break;
                case 2:
                    formValue.value = 2
                    preLoading.value=false
                    break;
                case 3:
                    formValue.value = 3
                    preLoading.value=false
                    break;
                case 4:
                    formValue.value = 4
                    preLoading.value=false
                    break;
                case 5:
                    formValue.value = 5
                    preLoading.value=false
                    break;
                case 6:
                    formValue.value = 6
                    preLoading.value=false
                    break;
                default:

                    break;
            }
            break;
        case 2:
            switch (mode) {
                case 1:
                    Swal.fire({
                        title: "Warning",
                        text: "Do you want to activate enrollment status?",
                        showCancelButton: true,
                        confirmButtonText: "Activate",
                        confirmButtonColor: '#237a5b',
                    }).then((confirm) => {
                        if(confirm.isConfirmed){
                            preLoading.value=true
                            let x = {
                                mode:1,//means activate,
                                userid:userID.value,
                                code:'cs_05'
                            }
                            setAcademicStatus(x).then((result)=>{
                                if (result.status == 200) {
                                    Swal.fire("Activated!", "", "success");
                                    preLoading.value=false
                                } else {
                                    Swal.fire("Changes are not saved", "", "info");
                                    preLoading.value=false
                                }
                            })
                        }
                    });
                    break;
                case 2:
                     Swal.fire({
                        title: "Warning",
                        text: "Do you want to deactivate enrollment status?",
                        showCancelButton: true,
                        confirmButtonText: "Deactivate",
                        confirmButtonColor: '#c91c22',
                    }).then((confirm) => {
                        if(confirm.isConfirmed){
                            preLoading.value=true
                            let x = {
                                mode:2,//means deactivate,
                                userid:userID.value,
                                code:'cs_05'
                            }
                            setAcademicStatus(x).then((result)=>{
                                if (result.status == 200) {
                                    Swal.fire("Deactivated!", "", "success");
                                    preLoading.value=false
                                } else {
                                    Swal.fire("Changes are not saved", "", "info");
                                    preLoading.value=false
                                }
                            })
                        }
                    });
                    break;
                case 3:
                     Swal.fire({
                        title: "Warning",
                        text: "Do you want to activate current semester?",
                        showCancelButton: true,
                        confirmButtonText: "Activate",
                        confirmButtonColor: '#237a5b',
                    }).then((confirm) => {
                        if(confirm.isConfirmed){
                            preLoading.value=true
                            let x = {
                                mode:1,//means end semester,
                                userid:userID.value,
                                code:'cs_06'
                            }
                            setAcademicStatus(x).then((result)=>{
                                if (result.status == 200) {
                                    Swal.fire("Activated!", "", "success");
                                    preLoading.value=false
                                } else {
                                    Swal.fire("Changes are not saved", "", "info");
                                    preLoading.value=false
                                }
                            })
                        }
                    });
                    break;
                case 4:
                     Swal.fire({
                        title: "Warning",
                        text: "Do you want to deactivate the semester?",
                        showCancelButton: true,
                        confirmButtonText: "Deactivate",
                        confirmButtonColor: '#c91c22',
                    }).then((confirm) => {
                        if(confirm.isConfirmed){
                            preLoading.value=true
                            let x = {
                                mode:2,//means end semester,
                                userid:userID.value,
                                code:'cs_06'
                            }
                            setAcademicStatus(x).then((result)=>{
                                if (result.status == 200) {
                                    Swal.fire("Deactivated!", "", "success");
                                    preLoading.value=false
                                } else {
                                    Swal.fire("Changes are not saved", "", "info");
                                    preLoading.value=false
                                }
                            })
                        }
                    });
                    break;
                case 5:
                     Swal.fire({
                        title: "Warning",
                        text: "Do you want to archive current semester information?",
                        showCancelButton: true,
                        confirmButtonText: "Generate",
                        confirmButtonColor: '#237a5b',
                    }).then((confirm) => {
                        if(confirm.isConfirmed){
                            // preLoading.value=true
                            
                            Swal.fire({
                                title: "Saving Please wait...",
                                text: "Do not click, close or refresh the page to avoid archiving failure",
                                html: `
                                    Do not click, close or refresh the page to avoid archiving failure
                                    <div id="savingProgress" class="mt-2">
                                        <div id="savingStatus"></div>
                                    </div>
                                    <span class="fw-bold mt-3" id="percentage">0</span><span>% Completed</span>
                                `,
                                showConfirmButton: false,
                                allowOutsideClick: false6
                            });
                            
                            resetStopwatch()
                            startStopwatch()// trigger progress bar
                            

                            let semesterstat = false
                            getAcademicStatus(1,'cs_06').then((results)=>{
                                results[0].sett_status == 1? semesterstat = true: semesterstat = false

                                if(semesterstat){
                                    stopStopwatch()
                                    setTimeout(Swal.fire("Terminate semester first to use this command", "", "info"), 1500);
                                    
                                }else{
                                    let x = {
                                        mode:3,//means archive semester,
                                        userid:userID.value,
                                    }
                                    setAcademicStatus(x).then((result)=>{
                                        if (result.status == 200) {
                                            stopStopwatch();
                                            setTimeout(noticeAlert, 1800);
                                            alertNotice.value = 1
                                        } else {
                                            stopStopwatch();
                                            setTimeout(noticeAlert, 1800);
                                            alertNotice.value = 2
                                        }
                                    })
                                }
                            })
                        }
                    });
                    break;
                case 6:
                    Swal.fire({
                        title: "Warning",
                        text: "Do you want to enable encoding of grades?",
                        showCancelButton: true,
                        confirmButtonText: "Activate",
                        confirmButtonColor: '#237a5b',
                    }).then((confirm) => {
                        if(confirm.isConfirmed){
                            preLoading.value=true
                            let x = {
                                mode:1,//means activate,
                                userid:userID.value,
                                sett_code:'cs_07'
                            }
                            setCommandUpdate(x).then((result)=>{
                                if (result.status == 200) {
                                    Swal.fire("Activated!", "", "success");
                                    preLoading.value=false
                                } else {
                                    Swal.fire("Changes are not saved", "", "info");
                                    preLoading.value=false
                                }
                            })
                        }
                    });
                    break;
                case 7:
                    Swal.fire({
                        title: "Warning",
                        text: "Do you want to disable encoding of grades?",
                        showCancelButton: true,
                        confirmButtonText: "Dectivate",
                        confirmButtonColor: '#c91c22',
                    }).then((confirm) => {
                        if(confirm.isConfirmed){
                            preLoading.value=true
                            let x = {
                                mode:2,//means deactivate,
                                userid:userID.value,
                                sett_code:'cs_07'
                            }
                            setCommandUpdate(x).then((result)=>{
                                if (result.status == 200) {
                                    Swal.fire("Deactivated!", "", "success");
                                    preLoading.value=false
                                } else {
                                    Swal.fire("Changes are not saved", "", "info");
                                    preLoading.value=false
                                }
                            })
                        }
                    });
                    break;
                default:
                    break;
            }
            break;

        default:
            break;
    }

    
}

const alertNotice = ref('') // for detection if nag error o hindi yung saving ng archive

const noticeAlert = () =>{
    Swal.fire(alertNotice.value == 1? 'Generated':'Changes are not saved', "", alertNotice.value == 1? 'success':'info')
}

const savingBar = (data) =>{
    var width = 10;
    var elem = document.getElementById("savingStatus");
    var percent = document.getElementById("percentage");

    if(data == 'done'){
        width = 100
    }else{
        width+=data
    }

    if(width<=100){
        elem.style.width = width + "%";
    }

    percent.innerHTML = width
    // var i = 0;
    // if (i == 0) {
    //     i = 1;
    //     var elem = document.getElementById("savingStatus");
    //     var width = 1;
    //     var id = setInterval(frame, 10);
    //     function frame() {
    //         if (width >= 100) {
    //             clearInterval(id);
    //             i = 0;
    //         } else {
    //             width++;
    //             elem.style.width = width + "%";
    //         }
    //     }
    // }
}

const seconds = ref(0);
const timerInterval=ref('');

const startStopwatch = () => {
  timerInterval.value = setInterval(() => {
    seconds.value++;
    // console.log(seconds.value); // Or update an HTML element
    savingBar(seconds.value) 
  }, 1000);
}

const stopStopwatch = () => {
  clearInterval(timerInterval.value);
  savingBar('done')
}

const resetStopwatch = () => {
    clearInterval(timerInterval.value);
    seconds.value = 0;
    // console.log(seconds.value); // Or update an HTML element
}

</script>
<template>
    <div>
        <div class="p-3 d-flex align-content-center justify-content-center">
            <p class="text-uppercase fw-bold green-mid text-white rounded-3 p-2 small-font">{{ title }}</p>
        </div>

        <div class="border p-3">
            <nav>
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    <button class="nav-link active" id="assign-tab" data-bs-toggle="tab" data-bs-target="#nav-assign"
                        type="button" role="tab" aria-controls="nav-assign" aria-selected="false">Assign</button>
                    <button class="nav-link" id="event-tab" data-bs-toggle="tab" data-bs-target="#nav-event"
                        type="button" role="tab" aria-controls="nav-event" aria-selected="true">Event</button>
                    <button class="nav-link" id="reset-tab" data-bs-toggle="tab" data-bs-target="#nav-reset"
                        type="button" role="tab" aria-controls="nav-reset" aria-selected="true">Reset</button>
                </div>
            </nav>

            <Loading1 v-show="preLoading" class="mt-5"></Loading1>
            <div v-show="!preLoading" class="tab-content" id="nav-tabContent">
                <div  class="tab-pane fade  show active" id="nav-assign" role="tabpanel" aria-labelledby="assign-tab">
                    <div class="table-responsive border p-3 small-font">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th style="background-color: #237a5b;" class="text-white">Target</th>
                                    <th style="background-color: #237a5b;" class="text-white">Notes</th>
                                    <th style="background-color: #237a5b;" class="text-white">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="align-middle">
                                        Academic Year
                                    </td>
                                    <td class="align-middle">
                                        Set academic year to be followed by system.
                                    </td>
                                    <td class="align-middle">
                                        <button type="button" class="btn btn-dark small-font" data-bs-toggle="modal" data-bs-target="#executemodal"
                                            @click="execute(1, 1)">Execute</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="align-middle">
                                        Semester
                                    </td>
                                    <td class="align-middle">
                                        Set semester/term to be followed by system.
                                    </td>
                                    <td class="align-middle">
                                        <button type="button" class="btn btn-dark small-font" data-bs-toggle="modal" data-bs-target="#executemodal"
                                        @click="execute(1, 2)">Execute</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="align-middle">
                                        Dowpayment
                                    </td>
                                    <td class="align-middle">
                                        Set default downpayment for enrollment.
                                    </td>
                                    <td class="align-middle">
                                        <button type="button" class="btn btn-dark small-font" data-bs-toggle="modal" data-bs-target="#executemodal"
                                        @click="execute(1, 3)">Execute</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="align-middle">
                                        Curriculum
                                    </td>
                                    <td class="align-middle">
                                        Set curriculum to be used by default, but it is still updatable during enrollment..
                                    </td>
                                    <td class="align-middle">
                                        <button type="button" class="btn btn-dark small-font" data-bs-toggle="modal" data-bs-target="#executemodal"
                                        @click="execute(1, 4)">Execute</button>
                                    </td>
                                </tr>
                                <!-- <tr>
                                    <td class="align-middle">
                                        Section
                                    </td>
                                    <td class="align-middle">
                                        Set count of enrollees to be registered in a section, you can also determine by
                                        category or random.
                                    </td>
                                    <td class="align-middle">
                                        <button type="button" class="btn btn-dark small-font" data-bs-toggle="modal" data-bs-target="#executemodal"
                                        @click="execute(1, 5)">Execute</button>
                                    </td>
                                </tr> -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="nav-event" role="tabpanel" aria-labelledby="event-tab">
                    <div class="table-responsive border p-3 small-font">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th style="background-color: #237a5b;" class="text-white">Target</th>
                                    <th style="background-color: #237a5b;" class="text-white">Notes</th>
                                    <th style="background-color: #237a5b;" class="text-white">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="align-middle">
                                        Start Enrollment
                                    </td>
                                    <td class="align-middle">
                                        Trigger enrollment status to active.
                                    </td>
                                    <td class="align-middle">
                                        <button type="button" class="btn btn-dark small-font" @click="execute(2, 1)">Execute</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="align-middle">
                                        Stop Enrollment
                                    </td>
                                    <td class="align-middle">
                                        Trigger enrollment status to Inactive.
                                    </td>
                                    <td class="align-middle">
                                        <button type="button" class="btn btn-dark small-font" @click="execute(2, 2)">Execute</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="align-middle">
                                        Start Semester
                                    </td>
                                    <td class="align-middle">
                                        Start Semester
                                    </td>
                                    <td class="align-middle">
                                        <button type="button" class="btn btn-dark small-font" @click="execute(2, 3)">Execute</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="align-middle">
                                        End Semester
                                    </td>
                                    <td class="align-middle">
                                        Stop Semester
                                    </td>
                                    <td class="align-middle">
                                        <button type="button" class="btn btn-dark small-font" @click="execute(2, 4)">Execute</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="align-middle">
                                        Archive Semester
                                    </td>
                                    <td class="align-middle">
                                        Generate and save all current semester information to the server.
                                    </td>
                                    <td class="align-middle">
                                        <button type="button" class="btn btn-dark small-font" @click="execute(2, 5)">Execute</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="align-middle">
                                        Enable Encoding of Grades (All Terms)
                                    </td>
                                    <td class="align-middle">
                                        Allow encoding of grades (All Terms)
                                    </td>
                                    <td class="align-middle">
                                        <button type="button" class="btn btn-dark small-font" @click="execute(2, 6)">Execute</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="align-middle">
                                        Disable Encoding of Grades (All Terms)
                                    </td>
                                    <td class="align-middle">
                                        Restrict encoding of grades (All Terms)
                                    </td>
                                    <td class="align-middle">
                                        <button type="button" class="btn btn-dark small-font" @click="execute(2, 7)">Execute</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="align-middle">
                                        Encoding of Grades
                                    </td>
                                    <td class="align-middle">
                                        Encoding of Grades to a specific term
                                    </td>
                                    <td class="align-middle">
                                        <button type="button" class="btn btn-dark small-font" data-bs-toggle="modal" data-bs-target="#executemodal"
                                            @click="execute(1, 6)">Execute</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="nav-reset" role="tabpanel" aria-labelledby="reset-tab">
                    <div class="table-responsive border p-3 small-font">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th style="background-color: #237a5b;" class="text-white">Target</th>
                                    <th style="background-color: #237a5b;" class="text-white">Notes</th>
                                    <th style="background-color: #237a5b;" class="text-white">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="align-middle">
                                        Enrollees
                                    </td>
                                    <td class="align-middle">
                                        Delete all enrollees to current database and return to empty and fresh state.
                                    </td>
                                    <td class="align-middle">
                                        <button type="button" class="btn btn-dark small-font">Execute</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="align-middle">
                                        Faculty / Employee
                                    </td>
                                    <td class="align-middle">
                                        Delete all employees to current database and return to empty and fresh state.
                                    </td>
                                    <td class="align-middle">
                                        <button type="button" class="btn btn-dark small-font">Execute</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="align-middle">
                                        Subject Loadings
                                    </td>
                                    <td class="align-middle">
                                        Delete all subjects tagged to faculty and return to empty and fresh state.
                                    </td>
                                    <td class="align-middle">
                                        <button type="button" class="btn btn-dark small-font">Execute</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="align-middle">
                                        Semester Launch
                                    </td>
                                    <td class="align-middle">
                                        Delete all created launch, schedules, tagged faculties, room assignments and
                                        reset
                                        availabilities.
                                    </td>
                                    <td class="align-middle">
                                        <button type="button" class="btn btn-dark small-font">Execute</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="align-middle">
                                        Schedules
                                    </td>
                                    <td class="align-middle">
                                        Delete all schedules and return all time and rooms to available.
                                    </td>
                                    <td class="align-middle">
                                        <button type="button" class="btn btn-dark small-font">Execute</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

        </div>

    </div>

    <!-- Edit Medical Modal -->
    <div class="modal fade" id="executemodal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Execute</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        @click="showCommandCenterModal = false"></button>
                </div>
                <div class="modal-body">
                    <AcademicYear v-if="formValue == 1 && showCommandCenterModal" :userIdData="userID"/>
                    <AcademicSemester v-if="formValue == 2 && showCommandCenterModal" :quarterdata="quarter" :userIdData="userID"/>
                    <AcademicDownpayment v-if="formValue == 3 && showCommandCenterModal" :userIdData="userID"/>
                    <AcademicCurriculum v-if="formValue == 4 && showCommandCenterModal" :userIdData="userID" :courseData="course" 
                        :curriculumData="curriculum" :programData="program" :gradelvlData="gradelvl"/>
                    <AcademicSection v-if="formValue == 5 && showCommandCenterModal" :userIdData="userID" />
                    <!-- <AcademicGrades v-if="formValue == 6 && showCommandCenterModal" :userIdData="userID" /> -->
                    <AcademicGradingTerm v-if="formValue == 6 && showCommandCenterModal" :userIdData="userID" :courseData="course" 
                        :programData="program" :gradelvlData="gradelvl"/>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <div class="form-group">
                        <small  class="form-text text-muted">Make sure that the information you entered is correct
                            to avoid misinformation and errors to the records. This action will held you accountable if proved to be mistaken.
                        </small>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                            @click="showCommandCenterModal = false">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>