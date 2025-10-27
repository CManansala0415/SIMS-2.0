<script setup>
import { ref, onMounted, computed } from 'vue';
import {
    getCommandUpdate,
    setCommandUpdate
} from "../../../Fetchers.js";
import Loader1 from '../../loaders/Loader1.vue';

const props = defineProps({
    userIdData: {
    },
    courseData: {
    },
    gradelvlData: {
    },
    programData: {
    }
})

const userID = computed(() => {
    return props.userIdData
});

const course = computed(() => {
    return props.courseData
});

const gradelvl = computed(() => {
    return props.gradelvlData
});

const program = computed(() => {
    return props.programData
});

const disabled = ref(false)
const saving = ref(false)
const downpayment = ref(0)
const courseId = ref(0)
const currId = ref(0)
const programId = ref(0)
const gradelvlId = ref(0)
const termId = ref('cs_08')
const filteredCurriculum = ref([])
const filteredCourse = ref([])
const filteredGradelvl = ref([])
const preLoading = ref(true)

const prelimData = ref({
    course: 0,
    gradelvl: 0,
    program: 0
})

const midtermData = ref({
    course: 0,
    gradelvl: 0,
    program: 0
})

const prefinalData = ref({
    course: 0,
    gradelvl: 0,
    program: 0
})

const finalData = ref({
    course: 0,
    gradelvl: 0,
    program: 0
})


const filterData = (mode) => {

    switch (mode) {
        case 1:
            reset()
            filteredGradelvl.value = gradelvl.value
            filteredGradelvl.value = gradelvl.value.filter((e) => {
                if (e.grad_dtypeid == programId.value) {
                    return {
                        e,
                    }
                }
            })
            break;
        case 2:
            filteredCourse.value = course.value
            filteredCourse.value = course.value.filter((e) => {
                if (e.prog_progtype == programId.value) {
                    return {
                        e,
                    }
                }
            })
            break;
        default:
            reset()
            break;
    }
}

const reset = () => {
    gradelvlId.value = 0
    courseId.value = 0
    currId.value = 0
    // termId.value = 'cs_08'
}

const save = (mode, term) => {
    saving.value = true
    let x = {}
    if(mode == 'clear'){
        x = {
            sett_course_progid: 0, // 0 means all
            sett_course_gradelvl: 0, 
            sett_course_cid: 0, 
            sett_code: termId.value, 
            sett_course_addedby: userID.value, 
            sett_status: 1
        }
    }
    else if(mode == 'deactivate'){
        x = {
            sett_course_progid: '', // '' means closed
            sett_course_gradelvl: '',
            sett_course_cid: '',
            sett_code: term,
            sett_course_addedby: userID.value,
            sett_status: 0
        }
    }else{
        x = {
            sett_course_progid: programId.value,
            sett_course_gradelvl: gradelvlId.value,
            sett_course_cid: courseId.value,
            sett_code: termId.value,
            sett_course_addedby: userID.value,
            sett_status: 1
        }
    }

    // switch(term){
    //     case 'cs_08':
    //         prelimData.value = {
    //             course: x.sett_course_cid,
    //             gradelvl: x.sett_course_gradelvl,
    //             program: x.sett_course_progid
    //         }
    //         break;
    //     case 'cs_09':
    //         midtermData.value = {
    //             course: x.sett_course_cid,
    //             gradelvl: x.sett_course_gradelvl,
    //             program: x.sett_course_progid
    //         }
    //         break;
    //     case 'cs_10':
    //         prefinalData.value = {
    //             course: x.sett_course_cid,
    //             gradelvl: x.sett_course_gradelvl,
    //             program: x.sett_course_progid
    //         }
    //         break;
    //     case 'cs_11':
    //         finalData.value = {
    //             course: x.sett_course_cid,
    //             gradelvl: x.sett_course_gradelvl,
    //             program: x.sett_course_progid
    //         }
    //         break;
    // }
    


    setCommandUpdate(x).then((results) => {
        if (results.status == 200) {
            // alert('Update Successful')
            // location.reload()
            Swal.fire({
                title: "Update Successful",
                text: "Changes applied, refreshing the page",
                icon: "success"
            }).then(() => {
                saving.value = false
                // location.reload()
            });
        } else {
            // alert('Update Failed')
            // location.reload()
            Swal.fire({
                title: "Update Failed",
                text: "Unknown error occured, try again later",
                icon: "error"
            }).then(() => {
                saving.value = false
                // location.reload()
            });
        }
    })
}

onMounted(async () => {
    getCommandUpdate().then((result) => {
        prelimData.value = {
            course: result[7].sett_course,
            gradelvl: result[7].sett_gradelvl,
            program: result[7].sett_program
        }

        midtermData.value = {
            course: result[8].sett_course,
            gradelvl: result[8].sett_gradelvl,
            program: result[8].sett_program
        }

        prefinalData.value = {
            course: result[9].sett_course,
            gradelvl: result[9].sett_gradelvl,
            program: result[9].sett_program
        }

        finalData.value = {
            course: result[10].sett_course,
            gradelvl: result[10].sett_gradelvl,
            program: result[10].sett_program
        }

        preLoading.value = false
    })
})



</script>
<template>
    <div v-if="preLoading" class="w-100 p-2">
        <Loader1 />
    </div>


    <div v-else class="row">
        <div class="col-md-6">
            <form @submit.prevent="save" class="d-flex flex-column justify-content-evenly"
                style="height: 100%; min-height: 400px;">

                <h6>Select Details Here</h6>
                <div class="d-flex flex-wrap form-group">
                    <label>Term</label>
                    <select class="form-control w-100" tabindex="-1" v-model="termId" @change="filterData(1)" required>
                        <option value="cs_08">Prelim</option>
                        <option value="cs_09">Midterm</option>
                        <option value="cs_10">Pre-Final</option>
                        <option value="cs_11">Final</option>
                    </select>
                </div>

                <div class="d-flex flex-wrap form-group">
                    <label>Program</label>
                    <select class="form-control w-100" tabindex="-1" v-model="programId" @change="filterData(1)"
                        required>
                        <option value="0">All</option>
                        <option v-for="(p, index) in program" :value="p.dtype_id">{{ p.dtype_desc }}</option>
                    </select>
                </div>

                <div class="d-flex flex-wrap form-group">
                    <label>Grade Level</label>
                    <select class="form-control w-100" tabindex="-1" v-model="gradelvlId"
                        :disabled="programId != 0 ? false : true" @change="filterData(2)" required>
                        <option value="0">All</option>
                        <option v-for="(g, index) in filteredGradelvl" :value="g.grad_id">{{ g.grad_name }}</option>
                    </select>
                </div>

                <div class="d-flex flex-wrap form-group">
                    <label>Course</label>
                    <select class="form-control w-100" tabindex="-1" v-model="courseId"
                        :disabled="gradelvlId != 0 ? false : true" required>
                        <option value="0">All</option>
                        <option v-for="(c, index) in filteredCourse" :value="c.prog_id"> {{ c.prog_code }}</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-sm btn-success mt-3 w-100" :disabled="saving ? true : false">
                    Save
                </button>
            </form>
        </div>
        <div class="col-md-6" style="height: 400px; overflow: auto;">
            <table class="table table-bordered table-striped mb-4">
                <thead>
                    <tr>
                        <th class="text-center">Currently Active Grading Sheets</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="align-middle">
                            <p>Prelim</p>
                            <div class="d-flex flex-wrap form-group">
                                <label>Program</label>
                                <select class="form-control w-100" tabindex="-1" v-model="prelimData.program" disabled>
                                    <option value="null">Closed</option>
                                    <option value="0">All</option>
                                    <option v-for="(p, index) in program" :value="p.dtype_id">{{ p.dtype_desc }}
                                    </option>
                                </select>
                            </div>

                            <div class="d-flex flex-wrap form-group">
                                <label>Grade Level</label>
                                <select class="form-control w-100" tabindex="-1" v-model="prelimData.gradelvl" disabled>
                                    <option value="null">Closed</option>
                                    <option value="0">All</option>
                                    <option v-for="(g, index) in gradelvl" :value="g.grad_id">{{ g.grad_name }}
                                    </option>
                                </select>
                            </div>

                            <div class="d-flex flex-wrap form-group mb-2">
                                <label>Course</label>
                                <select class="form-control w-100" tabindex="-1" v-model="prelimData.course" disabled>
                                    <option value="null">Closed</option>
                                    <option value="0">All</option>
                                    <option v-for="(c, index) in course" :value="c.prog_id"> {{ c.prog_code }}
                                    </option>
                                </select>
                            </div>

                            <!-- <button @click="save('clear')" type="button" class="btn btn-sm btn-secondary mt-2 w-100" :disabled="saving ? true : false">
                                Set All as Active
                            </button> -->
                            <button @click="save('deactivate', 'cs_08')" type="button" class="btn btn-sm btn-dark mt-2 w-100" :disabled="saving ? true : false">
                                Close Grading Sheet
                            </button>
                            
                        </td>
                    </tr>
                    <tr>
                        <td class="align-middle">
                            <p>Midterm</p>
                            <div class="d-flex flex-wrap form-group">
                                <label>Program</label>
                                <select class="form-control w-100" tabindex="-1" v-model="midtermData.program" disabled>
                                    <option value="null">Closed</option>
                                    <option value="0">All</option>
                                    <option v-for="(p, index) in program" :value="p.dtype_id">{{ p.dtype_desc }}
                                    </option>
                                </select>
                            </div>

                            <div class="d-flex flex-wrap form-group">
                                <label>Grade Level</label>
                                <select class="form-control w-100" tabindex="-1" v-model="midtermData.gradelvl"
                                    disabled>
                                    <option value="null">Closed</option>
                                    <option value="0">All</option>
                                    <option v-for="(g, index) in gradelvl" :value="g.grad_id">{{ g.grad_name }}
                                    </option>
                                </select>
                            </div>

                            <div class="d-flex flex-wrap form-group mb-2">
                                <label>Course</label>
                                <select class="form-control w-100" tabindex="-1" v-model="midtermData.course" disabled>
                                    <option value="null">Closed</option>
                                    <option value="0">All</option>
                                    <option v-for="(c, index) in course" :value="c.prog_id"> {{ c.prog_code }}
                                    </option>
                                </select>
                            </div>

                            <button @click="save('deactivate', 'cs_09')" type="button" class="btn btn-sm btn-dark mt-2 w-100" :disabled="saving ? true : false">
                                Close Grading Sheet
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td class="align-middle">
                            <p>Pre-Finals</p>
                            <div class="d-flex flex-wrap form-group">
                                <label>Program</label>
                                <select class="form-control w-100" tabindex="-1" v-model="prefinalData.program"
                                    disabled>
                                    <option value="null">Closed</option>
                                    <option value="0">All</option>
                                    <option v-for="(p, index) in program" :value="p.dtype_id">{{ p.dtype_desc }}
                                    </option>
                                </select>
                            </div>

                            <div class="d-flex flex-wrap form-group">
                                <label>Grade Level</label>
                                <select class="form-control w-100" tabindex="-1" v-model="prefinalData.gradelvl"
                                    disabled>
                                    <option value="null">Closed</option>
                                    <option value="0">All</option>
                                    <option v-for="(g, index) in gradelvl" :value="g.grad_id">{{ g.grad_name }}
                                    </option>
                                </select>
                            </div>

                            <div class="d-flex flex-wrap form-group mb-2">
                                <label>Course</label>
                                <select class="form-control w-100" tabindex="-1" v-model="prefinalData.course" disabled>
                                    <option value="null">Closed</option>
                                    <option value="0">All</option>
                                    <option v-for="(c, index) in course" :value="c.prog_id"> {{ c.prog_code }}
                                    </option>
                                </select>
                            </div>

                            <button @click="save('deactivate', 'cs_10')" type="button" class="btn btn-sm btn-dark mt-2 w-100" :disabled="saving ? true : false">
                                Close Grading Sheet
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td class="align-middle">
                            <p>Finals</p>
                            <div class="d-flex flex-wrap form-group">
                                <label>Program</label>
                                <select class="form-control w-100" tabindex="-1" v-model="finalData.program" disabled>
                                    <option value="null">Closed</option>
                                    <option value="0">All</option>
                                    <option v-for="(p, index) in program" :value="p.dtype_id">{{ p.dtype_desc }}
                                    </option>
                                </select>
                            </div>

                            <div class="d-flex flex-wrap form-group">
                                <label>Grade Level</label>
                                <select class="form-control w-100" tabindex="-1" v-model="finalData.gradelvl" disabled>
                                    <option value="null">Closed</option>
                                    <option value="0">All</option>
                                    <option v-for="(g, index) in gradelvl" :value="g.grad_id">{{ g.grad_name }}
                                    </option>
                                </select>
                            </div>

                            <div class="d-flex flex-wrap form-group mb-2">
                                <label>Course</label>
                                <select class="form-control w-100" tabindex="-1" v-model="finalData.course" disabled>
                                    <option value="null">Closed</option>
                                    <option value="0">All</option>
                                    <option v-for="(c, index) in course" :value="c.prog_id"> {{ c.prog_code }}
                                    </option>
                                </select>
                            </div>

                            <button @click="save('deactivate', 'cs_11')" type="button" class="btn btn-sm btn-dark mt-2 w-100" :disabled="saving ? true : false">
                                Close Grading Sheet
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>

        </div>
    </div>
</template>