<script setup>
import { ref, onMounted, computed } from 'vue';
import Loader from '../loaders/Loading1.vue';
import LoaderMini from '../loaders/Loader1.vue';
import { getUserID } from "../../../routes/user";
import { addLaunch, getLaunchChecker, getCommandUpdate } from "../../Fetchers.js";

const props = defineProps({
    degreeData: {
    },
    semesterData: {
    },
    courseData: {
    },
    sectionData: {
    },
    gradelvlData: {
    },
    curriculumData: {
    }
})


const degree = computed(() => {
    return props.degreeData
});
const semester = computed(() => {
    return props.semesterData
});
const course = computed(() => {
    return props.courseData
});
const section = computed(() => {
    return props.sectionData
});
const gradelvl = computed(() => {
    return props.gradelvlData
});
const curriculum = computed(() => {
    return props.curriculumData
});

const userID = ref('')
const saving = ref(false)
const filteredGradelvl = ref([])
const filteredCourse = ref([])
const filteredSemester = ref([])
const filteredCurriculum = ref([])
const search = ref('')
const showItems = ref(false)
const isLoading = ref(true)

const launch = ref({
    ln_dtype: '',
    ln_quarter: '',
    ln_course: '',
    ln_gradelvl: '',
    ln_curriculum: '',
    ln_section: '',
    ln_slots: '',
    ln_year: '',
})


// const semInfo = ref('')
// const enrollmentInfo = ref('')
// const yrFromInfo = ref('')
// const yrFromto = ref('')



onMounted(async () => {
    filteredGradelvl.value = gradelvl.value
    filteredCourse.value = course.value
    filteredSemester.value = semester.value
    filteredCurriculum.value = curriculum.value
    filterSemester()

    getCommandUpdate().then((result)=>{
        launch.value.ln_year = result[1].sett_yearfrom
        launch.value.ln_quarter = result[0].sett_semester

        // semInfo.value = result[0].quar_code
        // yrFromInfo.value = result[1].sett_yearfrom
        // yrFromto.value = result[1].sett_yearto
        // enrollmentInfo.value = result[4].sett_status == 1?'Active':'Inactive'

        getUserID().then((results) => {
            userID.value = results.account.data.id
            isLoading.value = false
        })
    })

    
})

const filterSemester = () => {
    filteredSemester.value = semester.value.filter(e => {
        if (e.quar_dtypeid == 2) {
            return e
        }
    })
}

const filterGradelvl = () => {
    launch.value.ln_gradelvl = ''
    launch.value.ln_course = ''
    launch.value.ln_section = ''

    filteredGradelvl.value = gradelvl.value.filter(e => {
        if (launch.value.ln_dtype == e.grad_dtypeid) {
            return e
        }
    })
}

// for typing search input
// interval for focusout, need ng interval para kapag nag select sa dropdown mahabol nya
let hiding = ref()
const doneInterval = ref(150)
const interval = () => {
    clearTimeout(hiding.value);
    hiding.value = setTimeout(() => setFalse(), doneInterval.value);
}
const setFalse = () => {
    showItems.value = false
}

const filterCourse = () => {
    filteredCourse.value = course.value.filter(e => {
        if (
            (
                (e.prog_name.toUpperCase().includes(search.value.toUpperCase())) ||
                (e.prog_code.toUpperCase().includes(search.value.toUpperCase()))
            )
            &&
            (launch.value.ln_dtype == e.prog_progtype)

        ) {
            return e
        }
    })
}
// for typing search input

const filterCurriculum = (id) => {
    filteredCurriculum.value = curriculum.value.filter(e => {
        if (e.curr_progid == id) {
            return e
        }
    })
}

 

const saveLaunch = async () => {
    // saving.value = true
    // if (confirm("Are you sure you want to create a launch? this is ireversible.") == true) {
    //     getLaunchChecker(
    //         launch.value.ln_dtype,
    //         launch.value.ln_quarter,
    //         launch.value.ln_course,
    //         launch.value.ln_gradelvl,
    //         launch.value.ln_curriculum,
    //         launch.value.ln_section,
    //         launch.value.ln_year
    //     ).then(async (results) => {

    //         if (
    //             (launch.value.ln_dtype == results.ln_dtype) &&
    //             (launch.value.ln_quarter == results.ln_quarter) &&
    //             (launch.value.ln_course == results.ln_course) &&
    //             (launch.value.ln_gradelvl == results.ln_gradelvl) &&
    //             (launch.value.ln_curriculum == results.ln_curriculum) &&
    //             (launch.value.ln_section == results.ln_section) &&
    //             (launch.value.ln_year == results.ln_year)
    //         ) {
    //             // alert("Already existing.")
    //             // saving.value = false
    //             Swal.fire({
    //                 title: "Notice",
    //                 text: "Already existing",
    //                 icon: "question"
    //             }).then(()=>{
    //                 saving.value = false
    //             })
    //         } else {
    //             let data = {
    //                 ...launch.value,
    //                 ln_addedby: userID.value
    //             }
    //             await addLaunch(data).then((results) => {
    //                 // alert('Successfull Registered')
    //                 // location.reload()
    //                 Swal.fire({
    //                     title: "Update Successful",
    //                     text: "Changes applied, refreshing the page",
    //                     icon: "success"
    //                 }).then(()=>{
    //                     location.reload()
    //                 });
    //             })
    //         }
    //     })

    // } else {
    //     return false;
    // }

    Swal.fire({
        title: "Initialize Launch",
        text: "Are you sure you want to create a launch? this is ireversible?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, Im going to create it!"
    }).then(async (result) => {
        if (result.isConfirmed) {
            saving.value = true
            getLaunchChecker(
                launch.value.ln_dtype,
                launch.value.ln_quarter,
                launch.value.ln_course,
                launch.value.ln_gradelvl,
                launch.value.ln_curriculum,
                launch.value.ln_section,
                launch.value.ln_year
            ).then(async (results) => {
                if (
                    (launch.value.ln_dtype == results.ln_dtype) &&
                    (launch.value.ln_quarter == results.ln_quarter) &&
                    (launch.value.ln_course == results.ln_course) &&
                    (launch.value.ln_gradelvl == results.ln_gradelvl) &&
                    (launch.value.ln_curriculum == results.ln_curriculum) &&
                    (launch.value.ln_section == results.ln_section) &&
                    (launch.value.ln_year == results.ln_year)
                ) {
                    // alert("Already existing.")
                    // saving.value = false
                    Swal.fire({
                        title: "Notice",
                        text: "Already existing",
                        icon: "question"
                    }).then(()=>{
                        saving.value = false
                    })
                } else {
                    let data = {
                        ...launch.value,
                        ln_addedby: userID.value
                    }
                    await addLaunch(data).then((results) => {
                        // alert('Successfull Registered')
                        // location.reload()
                        Swal.fire({
                            title: "Update Successful",
                            text: "Changes applied, refreshing the page",
                            icon: "success"
                        }).then(()=>{
                            location.reload()
                        });
                    })
                }
            })
        }
    });



}


</script>
<template>
    <form @submit.prevent="saveLaunch" class="d-flex flex-column p-2 gap-2" v-if="!isLoading">
        <div class="d-flex flex-wrap flex-column">
            <p class="text-success fw-bold">Initialize Semester</p>
            <p class=" fst-italic border p-2 rounded-3 bg-secondary-subtle small-font"><span class="fw-bold">Note:
                </span><span class="italic">Ensure that the details of the following launch are correct
                    because once this item is created it <span class="text-danger fw-bold">cannot be editable</span>.
                    To create a semester launch, select the appropiate academic status and refresh the page
                </span></p>
        </div>

        <div class="d-flex flex-wrap form-group">
            <label for="slot">Slots</label>
            <input v-model="launch.ln_slots" required type="text" class="form-control form-control-sm"
                title="Click Edit to modify details" id="slot" aria-describedby="slot" />
        </div>
        <div class="d-flex flex-column text-start form-group">
            <label for="year">Academic Year (From/To)</label>
            <div class="d-flex gap-1" id="year">
                <input class="form-control form-control-sm" type="number" min="1900" max="2099" step="1"
                    v-model="launch.ln_year" required disabled/>
                <input class="form-control form-control-sm pe-none" type="number" min="1900" max="2099" step="1"
                    :value="launch.ln_year + 1" disabled />
            </div>
        </div>
        <div class="d-flex flex-wrap form-group">
            <label for="sem">Semester</label>
            <select class="form-control form-select-sm" v-model="launch.ln_quarter" required disabled
                title="Click Edit to modify details" id="sem" aria-describedby="sem">
                <option v-for="(sem, index) in filteredSemester" :value="sem.quar_id">
                    {{ sem.quar_desc }}
                </option>
            </select>
        </div>
        <div class="d-flex flex-wrap form-group">
            <label for="degree">Degree</label>
            <select @change="filterGradelvl()"
                class="form-control form-select-sm"
                title="Click Edit to modify details" id="degree" aria-describedby="degree"
                v-model="launch.ln_dtype" :disabled="launch.ln_quarter ? false : true" required>
                <option v-for="(deg, index) in degree" :value="deg.dtype_id">
                    {{ deg.dtype_desc }}
                </option>
            </select>
        </div>
        <div class="d-flex flex-wrap form-group">
            <label for="gradelvl">Year / Grade Level</label>
            <select @change="filterCourse()"
                class="form-control form-select-sm"
                title="Click Edit to modify details" id="gradelvl" aria-describedby="gradelvl"
                v-model="launch.ln_gradelvl" :disabled="launch.ln_dtype ? false : true" required>
                <option v-for="(gr, index) in filteredGradelvl" :value="gr.grad_id">
                    {{ gr.grad_name }}
                </option>
            </select>
        </div>
        <div class="d-flex flex-wrap form-group">
            <label for="gradelvl">Program / Strand</label>
            <select @change="filterCurriculum(launch.ln_course)"
                class="form-control form-select-sm"
                title="Click Edit to modify details" id="gradelvl" aria-describedby="gradelvl"
                v-model="launch.ln_course" :disabled="launch.ln_gradelvl ? false : true" required>
                <option v-for="(c, index) in filteredCourse" :value="c.prog_id">
                    {{ c.prog_name }}
                </option>
            </select>
        </div>
        <div class="d-flex flex-wrap form-group">
            <label for="gradelvl">Curriculum</label>
            <select 
                class="form-control form-select-sm"
                title="Click Edit to modify details" id="gradelvl" aria-describedby="gradelvl"
                v-model="launch.ln_curriculum" :disabled="launch.ln_course ? false : true" required>
                <option v-for="(curr, index) in filteredCurriculum" :value="curr.curr_id">
                    {{ curr.curr_code }}
                </option>
            </select>
        </div>
        <div class="d-flex flex-wrap form-group">
            <label for="gradelvl">Section</label>
            <select @change="filterCourse()"
                class="form-control form-select-sm"
                title="Click Edit to modify details" id="gradelvl" aria-describedby="gradelvl"
                v-model="launch.ln_section" :disabled="launch.ln_curriculum ? false : true" required>
                <option v-for="(sc, index) in section" :value="sc.sec_id">
                    {{ sc.sec_name }}
                </option>
            </select>
        </div>
        <div class="d-flex flex-wrap form-group mt-3">
            <button :disabled="saving? true:false" type="submit" class="btn btn-sm btn-success w-100">Create launch</button>
        </div>
    </form>
    <div v-else>
        <LoaderMini></LoaderMini>
    </div>
</template>