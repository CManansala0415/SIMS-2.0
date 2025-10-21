<script setup>
import { ref, onMounted, computed } from 'vue';
import { enrollApplicant, getEnrollment, getCommandUpdate } from "../../Fetchers.js";
import { getUserID } from "../../../routes/user";
import Loader1 from '../loaders/Loader1.vue';
const userID = ref('')

const props = defineProps({
    personid: {
    },
    personname: {
    },
    programdata: {
    },
    gradelvldata: {
    }, 
    quarterdata: {
    },
    coursedata: {
    },
})



defineEmits(['close-modal', 'add-new']);

const personID = computed(() => {
    return props.personid
});
const program = computed(() => {
    return props.programdata
});
const gradelvl = computed(() => {
    return props.gradelvldata
});
const quarter = computed(() => {
    return props.quarterdata
});
const course = computed(() => {
    return props.coursedata
});
const fullName = computed(() => {
    return props.personname
});

const showItems = ref(false)
const filteredCourse = ref([])
const filteredQuarter = ref([])
const filteredGradelvl = ref([])
const settings = ref([])
const enrollChecker = ref(true)
const saving = ref(false)
const search = ref('')
const preLoading = ref(true)
const enrollData = ref({
    userid: '',
    personid: '',
    gradelvl: '',
    program: '',
    quarter: '',
    course: '',
    lrn: ''
})

const booter = async () => {
    getUserID().then((results) => {
        userID.value = results.account.data.id
    }).catch((err) => {
        router.push("/");
    })
    getCommandUpdate().then((results) => {
        settings.value=results
    })
  
}
const enrolleeData = ref([])
onMounted(async () => {
    window.stop()
    try {
        filteredCourse.value = program.value
        filteredGradelvl.value = gradelvl.value
        filteredQuarter.value = quarter.value

        await booter().then(() => {
            getEnrollment(personID.value).then((results) => {
                if (results.length != 0) {
                    enrolleeData.value = results[0]
                    detectCourse(enrolleeData.value.enr_course)
                    enrollData.value.gradelvl = enrolleeData.value.enr_gradelvl
                    enrollData.value.program = enrolleeData.value.enr_program
                    enrollData.value.quarter = enrolleeData.value.enr_quarter
                    enrollData.value.course = enrolleeData.value.enr_course
                    enrollData.value.lrn = enrolleeData.value.enr_lrn
                } else {
                    enrolleeData.value = []
                    enrollData.value.quarter = settings.value[0].sett_semester
                }
                enrollChecker.value = false
                preLoading.value = false
            })

        })
        
    } catch (err) {
        // alert('error loading the list default components')
        Swal.fire({
            icon: "error",
            title: "Oops...",
            text: "Something went wrong!",
            footer: '<a href="#" disabled>Have you checked your internet connection?</a>'
        }).then(()=>{
            // preLoading.value = false
        });
    }

})


// para to sa auto load ng course if enrolled na sya, to optimized in future
const detectCourse = (data) => {
    let x = []
    x = course.value.filter(e => {
        if (
            (e.prog_id == data)
        ) {
            return e
        }
    })

    search.value = x[0].prog_name
}
// for typing search input

const filterQuarter = () => {
    enrollData.value.quarter = settings.value[1].sett_semester
    // filteredQuarter.value = quarter.value.filter(e => {
    //   if(enrollData.value.program==e.quar_dtypeid){
    //       return e
    //   }
    // })
    filteredQuarter.value = quarter.value.filter(e => {
        if (e.quar_dtypeid == 2) {
            return e
        }
    })
}
const filterGradelvl = () => {
    enrollData.value.gradelvl = ''
    filteredGradelvl.value = gradelvl.value.filter(e => {
        if (enrollData.value.program == e.grad_dtypeid) {
            return e
        }
    })

}
const filterCourses = () => {
    enrollData.value.course = ''
    search.value = ''
}

const enroll = () => {
    if (
        (enrollData.value.program) &&
        (enrollData.value.quarter) &&
        (enrollData.value.course) &&
        (enrollData.value.gradelvl)
    ) {
        saving.value = true
        enrollData.value.personid = personID.value
        enrollData.value.userid = userID.value
        enrollApplicant(enrollData.value).then((results) => {

            if (results.data == 500) {
                // alert('error enrolling applicant')
                Swal.fire({
                    title: "Update Failed",
                    text: "Unknown error occured, try again later",
                    icon: "error"
                }).then(()=>{
                    // location.reload()
                });
            } else {
                // alert('Applicant Enrolled')
                // location.reload()
                Swal.fire({
                    title: "Update Successful",
                    text: "Changes applied, refreshing the page",
                    icon: "success"
                }).then(()=>{
                    location.reload()
                });
            }
        })

    } else {
        // alert('Please Fillout all the Fields')
        Swal.fire({
            title: "Requirement",
            text: "Please Fillout all the Fields",
            icon: "question"
        })
    }
}
</script>
<template>
    <Loader1 v-if="preLoading"></Loader1>
    <div v-else class="d-flex flex-column p-2 gap-2">
        <div class="d-flex flex-wrap flex-column">
            <p class="text-success fw-bold">Applicant Name</p>
            <p class="fw-bold" style="text-transform: uppercase;">{{ fullName }}</p>
            <p class=" fst-italic border p-2 rounded-3 bg-secondary-subtle small-font"><span class="fw-bold">Note:
                </span><span class="italic">Ensure that the details of the following applicant are correct.
                    To enroll this applicant, select the appropiate academic status and refresh the page.
                </span></p>
        </div>
        <div class="d-flex flex-wrap form-group">
            <label for="type">Type</label>
            <select v-model="enrollData.program" class="form-control" title="Click Edit to modify details"
                @change="filterGradelvl(), filterCourses(), enrollData.lrn = ''"
                :disabled="enrollChecker || enrolleeData.enr_id ? true : false" id="type" aria-describedby="type">
                <option value="" disabled>-- Select Type --</option>
                <option v-for="(p, index) in program" :value="p.dtype_id">{{ p.dtype_desc }}</option>
            </select>
        </div>
        <!-- <div class="d-flex flex-wrap form-group">
            <label for="lrn">LRN (if applicable)</label>
            <input v-model="enrollData.lrn" type="text" class="form-control"
                :disabled="enrollChecker || enrolleeData.enr_id || enrollData.program == 2 ? true : false"
                title="Click Edit to modify details" id="lrn" aria-describedby="lrn" />
        </div> -->
        <!-- <div class="d-flex flex-wrap form-group">
            <label for="sem">Semester / Quarter</label>
            <select v-model="enrollData.quarter" class="form-control"
                :disabled="!enrollData.program || enrolleeData.enr_id || enrollData.quarter  ? true : false"
                title="Click Edit to modify details" id="sem" aria-describedby="sem">
                <option value="" disabled>-- Select Type --</option>
                <option v-for="(q, index) in filteredQuarter" :value="q.quar_id">{{ q.quar_desc }}</option>
            </select>
        </div> -->
        <div class="d-flex flex-wrap form-group">
            <label for="sem">Semester / Quarter</label>
            <select v-model="enrollData.quarter" class="form-control"
                disabled
                title="Click Edit to modify details" id="sem" aria-describedby="sem">
                <option value="" disabled>-- Select Type --</option>
                <option v-for="(q, index) in filteredQuarter" :value="q.quar_id">{{ q.quar_desc }}</option>
            </select>
        </div>
        <div class="d-flex flex-wrap form-group">
            <label for="prog">Program / Strand</label>
            <select v-model="enrollData.course" class="form-control"
                :disabled="!enrollData.program || enrolleeData.enr_id ? true : false" @change="enrollData.gradelvl = ''"
                title="Click Edit to modify details" id="prog" aria-describedby="prog">
                <option value="" disabled>-- Select Type --</option>
                <option v-for="(c, index) in course" :value="c.prog_id"
                    v-show="enrollData.program == c.prog_progtype ? true : false">{{ c.prog_name }}</option>
            </select>
        </div>
        <div class="d-flex flex-wrap form-group">
            <label for="gradelvl">Grade / Year Level</label>
            <select v-model="enrollData.gradelvl" class="form-control"
                :disabled="!enrollData.program || enrolleeData.enr_id ? true : false"
                title="Click Edit to modify details" id="gradelvl" aria-describedby="gradelvl">
                <option value="" disabled>-- Select Type --</option>
                <option v-for="(g, index) in filteredGradelvl" :value="g.grad_id"
                    v-show="enrollData.program == g.grad_dtypeid ? true : false">{{ g.grad_name }}</option>
            </select>
        </div>
        <div class="d-flex flex-column mt-3">
            <button v-if="!enrolleeData.enr_id && enrollChecker == false" @click="enroll()"
                :disabled="saving ? true : false" type="button" class="btn btn-sm btn-success w-100"
                tabindex="-1">Enroll</button>

            <button v-if="enrolleeData.enr_id" type="button" class="btn btn-sm btn-primary w-100" tabindex="-1"
                aria-disabled="true">Enrolled</button>

            <button v-if="!enrolleeData.enr_id && enrollChecker == true" type="button"
                class="btn btn-sm btn-warning w-100" tabindex="-1">Checking
                Enrollment Status</button>
        </div>

    </div>
</template>