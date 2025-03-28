<script setup>
import { ref, onMounted, computed } from 'vue';
import Loader from '../snippets/loaders/Loading1.vue';
import ProgramForms from '../snippets/forms/ProgramSettingsForm.vue';
import SectionForms from '../snippets/forms/SectionSettingsForm.vue';
import SubjectForms from '../snippets/forms/SubjectSettingsForm.vue';
import CurriculumForms from '../snippets/forms/CurriculumSettingsForm.vue';
import CommandSettings from '../snippets/forms/CommandSettings.vue';
//import Form2 from './RegistrarSettingsForm2.vue';
//import Form3 from './RegistrarSettingsForm3.vue';
//import Form4 from './RegistrarSettingsForm4.vue';
//import Form5 from './RegistrarSettingsForm5.vue';
import { getUserID } from "../../routes/user.js";

import {
    getProgramList,
    getSection,
    getSubject,
    getCurriculumSett,
    getGradelvl,
    getProgram,
    getQuarter,
    getDegree,
    getSemester,
    getStudent,
    getSpecialization
} from "../Fetchers.js";
import UserAccountSettings from '../snippets/forms/UserAccountSettings.vue';
const booter = async () => {

    getGradelvl().then((results) => {
        gradelvl.value = results
        booting.value = 'Loading Grade Levels'
        bootingCount.value += 1
    })

    getSpecialization().then((results) => {
        specialization.value = results
        booting.value = 'Loading Specialization'
        bootingCount.value += 1
    })

    getProgram().then((results) => {
        program.value = results
        booting.value = 'Loading Degrees'
        bootingCount.value += 1
    })

    getQuarter().then((results) => {
        quarter.value = results
        booting.value = 'Loading Quarters'
        bootingCount.value += 1
    })

    getDegree().then((results) => {
        degree.value = results
        booting.value = 'Loading Degrees'
        bootingCount.value += 1
    })

    getSemester().then((results) => {
        semester.value = results
        booting.value = 'Loading Semesters'
        bootingCount.value += 1
    })

}

onMounted(async () => {
    try {
        preLoading.value = true
        await booter().then((results) => {
            booting.value = 'Loading Applicants...'
            bootingCount.value += 1
            getUserID().then((results) => {
                userID.value = results.account.data.id
                preLoading.account.value = false
            })
        })

    } catch (err) {
        preLoading.value = false
        alert('error loading the list default components')
    }
})

const userID = ref('')
const quarter = ref([])
const semester = ref([])
const section = ref([])
const degree = ref([])
const gradelvl = ref([])
const course = ref([])
const program = ref([])
const subject = ref([])
const curriculum = ref([])
const student = ref([])
const specialization = ref([])

const booting = ref('')
const bootingCount = ref(0)
const preLoading = ref(false)
const form = ref(1)
const limit = ref(10)
const offset = ref(0)
const settingsTitle = ref('')

const reset = async () => {
    course.value = []
    // program.value = []
    section.value = []
    subject.value = []
    curriculum.value = []
}

const mode = (type) => {
    switch (type) {
        case 1:
            preLoading.value = true
            booting.value = "Program Settings"
            reset().then(() => {
                getProgramList().then((results) => {
                    course.value = results
                    settingsTitle.value = 'Program Settings'
                    form.value = 2
                    preLoading.value = false
                })
            })

            break;
        case 2:
            preLoading.value = true
            booting.value = "Sections Settings"
            reset().then((results) => {
                getSection().then((results) => {
                    section.value = results
                    settingsTitle.value = 'Sections Settings'
                    form.value = 3
                    preLoading.value = false
                })
            })

            break;
        case 3:
            preLoading.value = true
            booting.value = "Subject Settings"
            reset().then((results) => {
                getSubject().then((results) => {
                    subject.value = results
                    settingsTitle.value = 'Subject Settings'
                    form.value = 4
                    preLoading.value = false
                })
            })

            break;
        case 4:
            preLoading.value = true
            booting.value = "Curriculum Settings"
            reset().then((results) => {
                getSubject().then((results) => {
                    subject.value = results
                    settingsTitle.value = 'Curriculum Settings'
                    getProgramList().then((results) => {
                        course.value = results
                        getCurriculumSett().then((results) => {
                            curriculum.value = results
                            form.value = 5
                            preLoading.value = false
                        })
                    })
                })
            })

            break;
        case 5:
            preLoading.value = true
            booting.value = "User Account Settings"
            reset().then((results) => {
                settingsTitle.value = 'User Account Settings'
                form.value = 6
                preLoading.value = false
            })

            break;
        case 6:
            preLoading.value = true
            booting.value = "Command Center Settings"
            reset().then((results) => {
                getStudent(0, 0).then((results) => {
                    student.value = results
                    getSection().then((results) => {
                        section.value = results
                        getProgramList().then((results) => {
                            course.value = results
                            getProgram().then((results) => {
                                program.value = results
                                getCurriculumSett().then((results) => {
                                    curriculum.value = results
                                    settingsTitle.value = 'Command Center Settings'
                                    form.value = 7
                                    preLoading.value = false
                                })
                            })
                        })
                    })
                })
            })
            break;
        default:
            form.value = 1
            break;
    }
}
</script>
<template>
    <div class="p-3 mb-4 border-bottom">
        <h5 class=" text-uppercase fw-bold">Registrar Settings</h5>
    </div>
    <div v-if="form == 1">
        <Loader v-if="preLoading" />
        <div v-else class="container">
            <div class="row gap-2">
                <div class="col card shadow-sm mb-2">
                    <div class="card-header">Settings</div>
                    <div class="card-body">
                        <h5 class="card-title">Programs</h5>
                        <p class="card-text justify-text small-font">Contains Program (Courses & Strands) list for
                            academic tracks, to modify settings click action to access settings.</p>
                    </div>
                    <div class="card-footer">
                        <button class="btn btn-sm btn-dark w-100" @click="mode(1)">Take Action</button>
                    </div>
                </div>
                <div class="col card shadow-sm mb-2">
                    <div class="card-header">Settings</div>
                    <div class="card-body">
                        <h5 class="card-title">Sections</h5>
                        <p class="card-text justify-text small-font">Contains Sections list for students class grouping,
                            to modify settings click action to access settings.</p>
                    </div>
                    <div class="card-footer">
                        <button class="btn btn-sm btn-dark w-100" @click="mode(2)">Take Action</button>
                    </div>
                </div>
                <div class="col card shadow-sm mb-2">
                    <div class="card-header">Settings</div>
                    <div class="card-body">
                        <h5 class="card-title">Subject</h5>
                        <p class="card-text justify-text small-font">Contains Subject list for students & faculties to
                            be taken and to be grouped to create a curriculum, to modify settings click action to access
                            settings.</p>
                    </div>
                    <div class="card-footer">
                        <button class="btn btn-sm btn-dark w-100" @click="mode(3)">Take Action</button>
                    </div>
                </div>
            </div>
            <div class="row gap-2">
                <div class="col card shadow-sm mb-2">
                    <div class="card-header">Settings</div>
                    <div class="card-body">
                        <h5 class="card-title">Curriculum</h5>
                        <p class="card-text justify-text small-font">Contains Curriculum list to make a course pack that
                            contains all the units and subjects needed for the semester, to modify settings click action
                            to access settings.</p>
                    </div>
                    <div class="card-footer">
                        <button class="btn btn-sm btn-dark w-100" @click="mode(4)">Take Action</button>
                    </div>
                </div>
                <div class="col card shadow-sm mb-2">
                    <div class="card-header">Settings</div>
                    <div class="card-body">
                        <h5 class="card-title">User Account</h5>
                        <p class="card-text justify-text small-font">Contains User accounts for system access, manage
                            level of restrictions and update credentials, to modify settings click action to access
                            settings.</p>
                    </div>
                    <div class="card-footer">
                        <button class="btn btn-sm btn-dark w-100" @click="mode(5)">Take Action</button>
                    </div>
                </div>
                <div class="col card shadow-sm mb-2">
                    <div class="card-header">Settings</div>
                    <div class="card-body">
                        <h5 class="card-title">Command Center</h5>
                        <p class="card-text justify-text small-font">Take control over academic activity, such as
                            registration and disabling events.</p>
                    </div>
                    <div class="card-footer">
                        <button class="btn btn-sm btn-dark w-100" @click="mode(6)">Take Action</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div v-if="form == 2">
        <Loader v-if="preLoading" />
        <div v-else>
            <div class="d-flex justify-content-end mb-3">
                <button class="btn btn-sm btn-info text-white" @click="form = 1"><font-awesome-icon
                        icon="fa-solid fa-rotate-left" size="sm" /> Back</button>
            </div>
            <div class="">
                <ProgramForms :degreeData="degree" :programData="program" :courseData="course" :userIdData="userID"
                    :title="settingsTitle" :semesterData="semester" />
            </div>
        </div>
    </div>
    <div v-if="form == 3">

        <Loader v-if="preLoading" />
        <div v-else>
            <div class="d-flex justify-content-end mb-3">
                <button class="btn btn-sm btn-info text-white" @click="form = 1"><font-awesome-icon
                        icon="fa-solid fa-rotate-left" size="sm" /> Back</button>
            </div>
            <div class="">
                <SectionForms :sectionData="section" :userIdData="userID" :title="settingsTitle" />
            </div>
        </div>
    </div>
    <div v-if="form == 4">
        <Loader v-if="preLoading" />
        <div v-else>
            <div class="d-flex justify-content-end mb-3">
                <button class="btn btn-sm btn-info text-white" @click="form = 1"><font-awesome-icon
                        icon="fa-solid fa-rotate-left" size="sm" /> Back</button>
            </div>
            <div class="">
                <SubjectForms :subjectData="subject" :specializationData="specialization" :programData="program"
                    :userIdData="userID" :title="settingsTitle" />
            </div>
        </div>
    </div>
    <div v-if="form == 5">
        <Loader v-if="preLoading" />
        <div v-else>
            <div class="d-flex justify-content-end mb-3">
                <button class="btn btn-sm btn-info text-white" @click="form = 1"><font-awesome-icon
                        icon="fa-solid fa-rotate-left" size="sm" /> Back</button>
            </div>
            <div class="">
                <CurriculumForms :subjectData="subject" :curriculumData="curriculum" :programData="program"
                    :courseData="course" :quarterData="quarter" :gradelvlData="gradelvl" :userIdData="userID"
                    :title="settingsTitle" />
            </div>
        </div>
    </div>
    <div v-if="form == 6">
        <Loader v-if="preLoading" />
        <div v-else>
            <!-- <div class="d-flex justify-content-end mb-3">
                <button class="btn btn-sm btn-info text-white" @click="form = 1"><font-awesome-icon
                        icon="fa-solid fa-rotate-left" size="sm" /> Back</button>
            </div> -->
            <div class="">
                <UserAccountSettings :userIdData="userID" :title="settingsTitle" @close="form = 1"/>
            </div>
        </div>
    </div>
    <div v-if="form == 7">
        <Loader v-if="preLoading" />
        <div v-else>
            <div class="d-flex justify-content-end mb-3">
                <button class="btn btn-sm btn-info text-white" @click="form = 1"><font-awesome-icon
                        icon="fa-solid fa-rotate-left" size="sm" /> Back</button>
            </div>
            <div class="">
                <CommandSettings :subjectData="subject" :curriculumData="curriculum" :programData="program"
                    :courseData="course" :quarterData="quarter" :gradelvlData="gradelvl" :userIdData="userID"
                    :title="settingsTitle" />
            </div>
        </div>
    </div>

</template>