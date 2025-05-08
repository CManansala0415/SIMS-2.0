<script setup>
import { ref, onMounted, computed } from 'vue';
import {
    setCommandUpdate
} from "../../../Fetchers.js";

const props = defineProps({
    userIdData: {
    },
    courseData: {
    },
    curriculumData: {
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

const curriculum = computed(() => {
    return props.curriculumData
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
const filteredCurriculum = ref([])
const filteredCourse= ref([])
const filteredGradelvl = ref([])

const filterData = (mode) => {
    


    switch(mode){
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
        case 3:
            currId.value = 0
            filteredCurriculum.value = curriculum.value
            filteredCurriculum.value = curriculum.value.filter((e) => {
                if (e.curr_progid == courseId.value) {
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

const reset = () =>{
    gradelvlId.value = 0
    courseId.value = 0
    currId.value = 0
}

const save = () => {
    saving.value = true

    let x = {
        sett_course_progid: programId.value,
        sett_course_gradelvl: gradelvlId.value,
        sett_course_cid: courseId.value,
        sett_course_currid: currId.value,
        sett_code: 'cs_04',
        sett_course_addedby: userID.value
    }
    setCommandUpdate(x).then((results) => {
        if (results.status == 200) {
            // alert('Update Successful')
            // location.reload()
            Swal.fire({
                title: "Update Successful",
                text: "Changes applied, refreshing the page",
                icon: "success"
            }).then(()=>{
                location.reload()
            });
        } else {
            // alert('Update Failed')
            // location.reload()
            Swal.fire({
                title: "Update Failed",
                text: "Unknown error occured, try again later",
                icon: "error"
            }).then(()=>{
                location.reload()
            });
        }
    })
}

onMounted(async () => {

})
</script>
<template>
    <form @submit.prevent="save">

        <div class="d-flex flex-wrap form-group">
            <label>Program</label>
            <select class="form-control w-100" tabindex="-1" v-model="programId" @change="filterData(1)" required>
                <option value="0">-- Select Here --</option>
                <option v-for="(p, index) in program" :value="p.dtype_id">{{ p.dtype_desc }}</option>
            </select>
        </div>

        <div class="d-flex flex-wrap form-group">
            <label>Grade Level</label>
            <select class="form-control w-100" tabindex="-1" v-model="gradelvlId" :disabled="programId != 0? false:true" @change="filterData(2)" required>
                <option value="0">-- Select Here --</option>
                <option v-for="(g, index) in filteredGradelvl" :value="g.grad_id">{{ g.grad_name }}</option>
            </select>
        </div>

        <div class="d-flex flex-wrap form-group mb-2">
            <label>Course</label>
            <select class="form-control w-100" tabindex="-1" v-model="courseId" :disabled="gradelvlId != 0? false:true" @change="filterData(3)" required>
                <option value="0">-- Select Here --</option>
                <option v-for="(c, index) in filteredCourse" :value="c.prog_id"> {{ c.prog_code }}</option>
            </select>
        </div>

        <div class="d-flex flex-wrap form-group">
            <label>Curriculum</label>
            <select class="form-control w-100" tabindex="-1" v-model="currId" :disabled="gradelvlId != 0? false:true" required>
                <option value="0">-- Select Here --</option>
                <option v-for="(curr, index) in filteredCurriculum" :value="curr.curr_id">
                    {{ curr.curr_code }}
                </option>
            </select>
        </div>

        <button type="submit" class="btn btn-sm btn-success mt-3 w-100" :disabled="saving ? true : false">
            Save
        </button>
    </form>
</template>