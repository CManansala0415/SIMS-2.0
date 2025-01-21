<script setup>
import { ref, onMounted, computed } from 'vue';
import {
    addCurriculum, addCurriculumTagging, getCurriculumSubject
} from "../../Fetchers.js";

import CommandCenterModal from '../modal/CommandCenterModal.vue';
import AcademicYear from './commandcenterforms/AcademicYear.vue';
import AcademicSemester from './commandcenterforms/AcademicSemester.vue';
import AcademicSection from './commandcenterforms/AcademicSection.vue';
import AcademicDownpayment from './commandcenterforms/AcademicDownpayment.vue';
import AcademicCurriculum from './commandcenterforms/AcademicCurriculum.vue';

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
const execute = (type, mode) => {
    formValue.value = 0
    showCommandCenterModal.value = true
    switch (type) {
        case 1:
            switch (mode) {
                case 1:
                    formValue.value = 1
                    break;
                case 2:
                    formValue.value = 2
                    break;
                case 3:
                    formValue.value = 3
                    break;
                case 4:
                    formValue.value = 4
                    break;
                case 5:
                    formValue.value = 5
                    break;
                default:

                    break;
            }
            break;
        default:

            break;
    }
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

            <div class="tab-content" id="nav-tabContent">
                <div class="tab-pane fade  show active" id="nav-assign" role="tabpanel" aria-labelledby="assign-tab">
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
                                        <button type="button" class="btn btn-dark small-font">Execute</button>
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
                                        <button type="button" class="btn btn-dark small-font">Execute</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="align-middle">
                                        End Semester
                                    </td>
                                    <td class="align-middle">
                                        Archive all milestones and generate archive of this current academic term into
                                        the server.
                                    </td>
                                    <td class="align-middle">
                                        <button type="button" class="btn btn-dark small-font">Execute</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="align-middle">
                                        Archive Admission
                                    </td>
                                    <td class="align-middle">
                                        Generate all admission records into excel file (Data is based on active academic
                                        year of the system).
                                    </td>
                                    <td class="align-middle">
                                        <button type="button" class="btn btn-dark small-font">Execute</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="align-middle">
                                        Archive Enrollment
                                    </td>
                                    <td class="align-middle">
                                        Generate all enrollee records into excel file (Data is based on active academic
                                        year of the system).
                                    </td>
                                    <td class="align-middle">
                                        <button type="button" class="btn btn-dark small-font">Execute</button>
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
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
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