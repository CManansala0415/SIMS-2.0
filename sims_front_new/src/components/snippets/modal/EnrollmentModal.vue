<script setup>
import { ref, onMounted, computed } from 'vue';
import { enrollApplicant, getEnrollment, getCommandUpdate, getSettlementDetails } from "../../Fetchers.js";
import { getUserID } from "../../../routes/user";
import NeuLoader2 from '../loaders/NeuLoader2.vue';
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



const emit = defineEmits(['close-modal'])
const close = () => {
    window.stop()
    emit('close-modal')
}

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
const accounts = ref([])
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

    getSettlementDetails(personID.value).then((results) => {
        accounts.value=results
    })
  
}
const enrolleeData = ref([])
const hasBalance = ref(false)
onMounted(async () => {
    window.stop()
    try {
        filteredCourse.value = program.value
        filteredGradelvl.value = gradelvl.value
        filteredQuarter.value = quarter.value

        await booter().then(() => {
           
            getEnrollment(personID.value).then((results) => {

                hasBalance.value = accounts.value.some(a => parseFloat(a.acs_balance) > 0);
                // console.log('Has Balance:', hasBalance.value);


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
    if(hasBalance.value){
        Swal.fire({
            title: "Enrollment Restricted",
            text: "This applicant has an outstanding balance. Please settle the account before proceeding with enrollment.",
            icon: "warning"
        }).then(()=>{
            enrollChecker.value = true
            preLoading.value = false
            close();
        });
    } else{
        if (
            (enrollData.value.program) &&
            (enrollData.value.quarter) &&
            (enrollData.value.course) &&
            (enrollData.value.gradelvl)
        ) {
            Swal.fire({
                title: "Saving Updates",
                text: "Please wait while we check all necessary details.",
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
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
                        Swal.close()
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
                        Swal.close()
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
}
</script>
<template>
    <NeuLoader2 v-if="preLoading"></NeuLoader2>
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
            <select v-model="enrollData.program" class="neu-input neu-select" title="Click Edit to modify details"
                @change="filterGradelvl(), filterCourses(), enrollData.lrn = ''"
                :disabled="enrollChecker || enrolleeData.enr_id ? true : false" id="type" aria-describedby="type">
                <option value="" disabled>-- Select Type --</option>
                <option v-for="(p, index) in program" :value="p.dtype_id">{{ p.dtype_desc }}</option>
            </select>
        </div>
        <!-- <div class="d-flex flex-wrap form-group">
            <label for="lrn">LRN (if applicable)</label>
            <input v-model="enrollData.lrn" type="text" class="neu-input"
                :disabled="enrollChecker || enrolleeData.enr_id || enrollData.program == 2 ? true : false"
                title="Click Edit to modify details" id="lrn" aria-describedby="lrn" />
        </div> -->
        <!-- <div class="d-flex flex-wrap form-group">
            <label for="sem">Semester / Quarter</label>
            <select v-model="enrollData.quarter" class="neu-input neu-select"
                :disabled="!enrollData.program || enrolleeData.enr_id || enrollData.quarter  ? true : false"
                title="Click Edit to modify details" id="sem" aria-describedby="sem">
                <option value="" disabled>-- Select Type --</option>
                <option v-for="(q, index) in filteredQuarter" :value="q.quar_id">{{ q.quar_desc }}</option>
            </select>
        </div> -->
        <div class="d-flex flex-wrap form-group">
            <label for="sem">Semester / Quarter</label>
            <select v-model="enrollData.quarter" class="neu-input neu-select"
                disabled
                title="Click Edit to modify details" id="sem" aria-describedby="sem">
                <option value="" disabled>-- Select Type --</option>
                <option v-for="(q, index) in filteredQuarter" :value="q.quar_id">{{ q.quar_desc }}</option>
            </select>
        </div>
        <div class="d-flex flex-wrap form-group">
            <label for="prog">Program / Strand</label>
            <select v-model="enrollData.course" class="neu-input neu-select"
                :disabled="!enrollData.program || enrolleeData.enr_id ? true : false" @change="enrollData.gradelvl = ''"
                title="Click Edit to modify details" id="prog" aria-describedby="prog">
                <option value="" disabled>-- Select Type --</option>
                <option v-for="(c, index) in course" :value="c.prog_id"
                    v-show="enrollData.program == c.prog_progtype ? true : false">{{ c.prog_name }}</option>
            </select>
        </div>
        <div class="d-flex flex-wrap form-group">
            <label for="gradelvl">Grade / Year Level</label>
            <select v-model="enrollData.gradelvl" class="neu-input neu-select"
                :disabled="!enrollData.program || enrolleeData.enr_id ? true : false"
                title="Click Edit to modify details" id="gradelvl" aria-describedby="gradelvl">
                <option value="" disabled>-- Select Type --</option>
                <option v-for="(g, index) in filteredGradelvl" :value="g.grad_id"
                    v-show="enrollData.program == g.grad_dtypeid ? true : false">{{ g.grad_name }}</option>
            </select>
        </div>
        <div class="d-flex flex-column mt-3">
            <button v-if="!enrolleeData.enr_id && enrollChecker == false" @click="enroll()"
                :disabled="saving ? true : false" type="button" class="neu-btn neu-blue w-100 p-2"
                tabindex="-1"><font-awesome-icon icon="fa-solid fa-graduation-cap" /> Enroll</button>
            <p v-if="enrolleeData.enr_id" class="fw-bold text-success">Student is currently Enrolled</p>
            <p v-if="!enrolleeData.enr_id && enrollChecker == true" class="fw-bold text-success">Checking Enrollment Status</p>
        </div>

    </div>
</template>