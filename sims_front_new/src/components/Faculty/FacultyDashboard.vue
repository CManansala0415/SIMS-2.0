<script setup>
import { ref, onMounted, computed } from 'vue';
import Loader from '../snippets/loaders/Loading1.vue';
// import NeuLoader1 from '../snippets/loaders/NeuLoader1.vue';
import SkeletonTableLoader from '../snippets/loaders/SkeletonTableLoader.vue';
import SkeletonHeaderLoader from '../snippets/loaders/SkeletonHeaderLoader.vue';
import NeuLoader4 from '../snippets/loaders/NeuLoader4.vue';

// import Enroll from '../snippets/modal/Enrollment.vue';
// import ApplicationForm from './ApplicationForm.vue';
import { getUserID } from "../../routes/user.js";
import {
    getFacultyAssignment,
    getGradelvl,
    getProgram,
    getQuarter,
    getDegree,
    getProgramList,
    getSemester,
    getSection,
    getFacultyStudent,
    getAcademicDefaults,
    getEnrollmentSchedule
} from "../Fetchers.js";

import { useRouter, useRoute } from 'vue-router'

const assignment = ref([])
const assignmentCount = ref(0)
const preLoading = ref(true)
const userID = ref('')
const empID = ref('')
const countIndex = ref('')
const router = useRouter();
const showForm = ref(false)
const showEnroll = ref(false)
const groupedAssignmentSection = ref([])
const groupedAssignmentSubject = ref([])
const groupedAssignmentStudent = ref([])
const switcher = ref(0)
const quarter = ref([])
const gradelvl = ref([])
const degree = ref([])
const course = ref([])
const dtype = ref([])
const semester = ref([])
const section = ref([])
const booting = ref('')
const bootingCount = ref(0)
const emit = defineEmits(['fetchUser', 'doneLoading'])

const booter = async () => {

    // getGradelvl().then((results) => {
    //     gradelvl.value = results
    //     booting.value = 'Loading Grade Levels'
    //     bootingCount.value += 1
    // })

    // getProgram().then((results) => {
    //     degree.value = results
    //     booting.value = 'Loading Degrees'
    //     bootingCount.value += 1
    // })

    // getQuarter().then((results) => {
    //     quarter.value = results
    //     booting.value = 'Loading Quarters'
    //     bootingCount.value += 1
    // })

    getDegree().then((results) => {
        dtype.value = results
        booting.value = 'Loading Courses'
        bootingCount.value += 1
    })

    // getProgramList().then((results) => {
    //     course.value = results
    //     booting.value = 'Loading Courses'
    //     bootingCount.value += 1
    // })

    // getSemester().then((results) => {
    //     semester.value = results
    //     booting.value = 'Loading Semesters'
    //     bootingCount.value += 1
    // })

    // getSection().then((results) => {
    //     section.value = results
    //     booting.value = 'Loading Sections'
    //     bootingCount.value += 1
    // })
    getAcademicDefaults().then((results) => {
        gradelvl.value = results.gradelvl
        degree.value = results.program
        quarter.value = results.quarter
        course.value = results.course
        semester.value = results.semester
        section.value = results.section
        booting.value = 'Loading Academic Information'
        bootingCount.value += 1
    })

}


const accessData = ref([])
onMounted(async () => {
    getUserID().then(async (results1) => {
        userID.value = results1.account.data.id
        empID.value = results1.employee.emp_id
        accessData.value = results1.access
        emit('fetchUser', results1)
        try {
            await booter().then(() => {
                booting.value = 'Loading assignments...'
                bootingCount.value += 1
                getFacultyAssignment(results1.employee.emp_id).then((results2) => {
                    assignment.value = results2.data
                    assignmentCount.value = results2.count

                    groupedAssignmentSection.value = Object.groupBy(assignment.value, assignments => assignments.lf_lnid);
                    groupedAssignmentSubject.value = Object.groupBy(assignment.value, assignments => assignments.lf_subjid);

                    // kunin mga name ng keys to belooped para makuha yung sections course and gradelvl 
                    let keys = Object.keys(groupedAssignmentSection.value)
                    // console.log(groupedAssignmentSection.value)

                    keys.forEach((e) => {
                        let section = groupedAssignmentSection.value[e][0].ln_section
                        let course = groupedAssignmentSection.value[e][0].ln_course
                        let gradelvl = groupedAssignmentSection.value[e][0].ln_gradelvl

                        getFacultyStudent(section, gradelvl, course).then((results3) => {
                            groupedAssignmentStudent.value.push(results3)
                        })
                    })

                    getEnrollmentSchedule(null, null, null, null, null, null, 'faculty', empID.value).then((results) => {
                        mapData(results.data, empID.value)
                        preLoading.value = false
                        emit('doneLoading', false)
                        // console.log(results.data)
                    })
                 
                })
            })

        } catch (err) {
            // preLoading.value = false
            // alert('error loading the list default components')
            Swal.fire({
                icon: "error",
                title: "Oops...",
                text: "Something went wrong!",
                footer: '<a href="#" disabled>Have you checked your internet connection?</a>'
            }).then(()=>{
                preLoading.value = false
                emit('doneLoading', false)
            });
        }
    }).catch((err) => {
        // alert('Unauthorized Session, Please Log In')
        // Swal.fire({
        //     icon: "error",
        //     title: "Oops...",
        //     text: "Session expired, log in again",
        // }).then(()=>{
        //     router.push("/");
        //     window.stop()
        // });
    })
})

const loadSched = ref([]);

const mapData = (data, facultyid) => {

    loadSched.value = time.value.map(tm => {

        const sched = data.filter(item => item.sched_time === tm.timeid);

        let facultySched = [];

        const days = ['mon', 'tue', 'wed', 'thurs', 'fri', 'sat', 'sun'];

        sched.forEach(item => {

            days.forEach(day => {

                if (item[`${day}_faculty`] == facultyid) {

                    facultySched.push({

                        // launch information
                        ln_id: item.ln_id,
                        ln_dtype: item.ln_dtype,
                        ln_quarter: item.ln_quarter,
                        ln_course: item.ln_course,
                        ln_gradelvl: item.ln_gradelvl,
                        ln_curriculum: item.ln_curriculum,
                        ln_section: item.ln_section,
                        ln_slots: item.ln_slots,
                        ln_year: item.ln_year,
                        prog_code: item.prog_code,
                        grad_name: item.grad_name,
                        sec_name: item.sec_name,

                        // schedule information
                        day: day,
                        time: item.sched_time,

                        subject_id: item[`sched_${day}`],
                        subject_code: item[`sched_${day}_code`],

                        faculty_id: item[`${day}_faculty`],
                        faculty_firstname: item[`${day}_faculty_firstname`],
                        faculty_middlename: item[`${day}_faculty_middlename`],
                        faculty_lastname: item[`${day}_faculty_lastname`],
                        faculty_suffixname: item[`${day}_faculty_suffixname`],

                        room_id: item[`${day}_room_id`],
                        room_name: item[`${day}_room_name`],

                        building_id: item[`${day}_buil_id`],
                        building_name: item[`${day}_buil_name`],

                    });

                }

            });

        });


        return {
            ...tm,

            // all faculty schedules on this time slot
            schedules: facultySched
        };

    });

    console.log(loadSched.value);

};

const getDaySchedule = (tm, day) => {
    return tm.schedules.find(x => x.day === day);
};


const isSameSchedule = (current, next) => {

    if(!current || !next) return false;

    return (
        current.subject_code === next.subject_code &&
        current.faculty_id === next.faculty_id &&
        current.ln_course === next.ln_course &&
        current.ln_section === next.ln_section
    );

};


const shouldRenderCell = (index, day) => {

    if(index === 0) return true;

    const current = getDaySchedule(loadSched.value[index], day);
    const previous = getDaySchedule(loadSched.value[index - 1], day);

    return !isSameSchedule(current, previous);

};


const getRowSpan = (index, day) => {

    let rowspan = 1;

    const current = getDaySchedule(loadSched.value[index], day);

    if(!current) return rowspan;


    for(let i = index + 1; i < loadSched.value.length; i++){

        const next = getDaySchedule(loadSched.value[i], day);

        if(isSameSchedule(current, next)){
            rowspan++;
        } else {
            break;
        }

    }

    return rowspan;

};

const sectionColors = ref({});

const colors = [
    '#FDE68A', // Soft Yellow
    '#93C5FD', // Soft Blue
    '#86EFAC', // Soft Green
    '#F9A8D4', // Soft Pink
    '#C4B5FD', // Soft Purple
    '#FDBA74', // Soft Orange
    '#67E8F9', // Soft Cyan
    '#A7F3D0', // Soft Mint
    '#FCA5A5', // Soft Red
    '#DDD6FE', // Lavender
    '#BAE6FD', // Sky
    '#D9F99D', // Lime
];


const getScheduleColor = (sched) => {

    if(!sched) return '';

    const key = `${sched.ln_course}-${sched.ln_section}-${sched.ln_gradelvl}`;

    if(!sectionColors.value[key]){

        const index = Object.keys(sectionColors.value).length % colors.length;

        sectionColors.value[key] = colors[index];

    }

    return sectionColors.value[key];

};

const downloadExcel = () => {
    let table = document.getElementById('main-table')
    const wb = XLSX.utils.table_to_book(table, { sheet: 'sheet-1' });
    /* Export to file (start a download) */
    XLSX.writeFile(wb, 'MyTable.xlsx');
}



const headers = ref([
    {
        title: 'Time'
    },
    {
        title: 'Monday'
    },
    {
        title: 'Tuesday'
    },
    {
        title: 'Wednesday'
    },
    {
        title: 'Thursday'
    },
    {
        title: 'Friday'
    },
    {
        title: 'Saturday'
    },
    {
        title: 'Sunday'
    },
])
const time = ref([
    {
        timeid: '06000630A',
        timename: '06:00 - 6:30',
        daytime: 'AM',
        classname: 'p-1',
        style:''
    },
    {
        timeid: '06300700A',
        timename: '06:30 - 7:00',
        daytime: 'AM',
        classname: 'p-1',
        style:''
    },
    {
        timeid: '07000730A',
        timename: '07:00 - 7:30',
        daytime: 'AM',
        classname: 'p-1',
        style:''
    },
    {
        timeid: '07300800A',
        timename: '07:30 - 8:00',
        daytime: 'AM',
        classname: 'p-1',
        style:''
    },
    {
        timeid: '08000830A',
        timename: '08:00 - 8:30',
        daytime: 'AM',
        classname: 'p-1',
        style:''
    },
    {
        timeid: '08300900A',
        timename: '08:30 - 9:00',
        daytime: 'AM',
        classname: 'p-1',
        style:''
    },
    {
        timeid: '09000930A',
        timename: '09:00 - 9:30',
        daytime: 'AM',
        classname: 'p-1',
        style:''
    },
    {
        timeid: '09301000A',
        timename: '09:30 - 10:00',
        daytime: 'AM',
        classname: 'p-1',
        style:''
    },
    {
        timeid: '10001030A',
        timename: '10:00 - 10:30',
        daytime: 'AM',
        classname: 'p-1',
        style:''
    },
    {
        timeid: '10301100A',
        timename: '10:30 - 11:00',
        daytime: 'AM',
        classname: 'p-1',
        style:''
    },
    {
        timeid: '11001130A',
        timename: '11:00 - 11:30',
        daytime: 'AM',
        classname: 'p-1',
        style:''
    },
    {
        timeid: '11301200A',
        timename: '11:30 - 12:00',
        daytime: 'AM',
        classname: 'p-1',
        style:''
    },
    {
        timeid: '12001230P',
        timename: '12:00 - 12:30',
        daytime: 'PM',
        classname: 'p-1',
        style:''
    },
    {
        timeid: '12300100P',
        timename: '12:30 - 1:00',
        daytime: 'PM',
        classname: 'p-1',
        style:''
    },
    {
        timeid: '01000130P',
        timename: '01:00 - 01:30',
        daytime: 'PM',
        classname: 'p-1',
        style:''
    },
    {
        timeid: '01300200P',
        timename: '01:30 - 2:00',
        daytime: 'PM',
        classname: 'p-1',
        style:''
    },
    {
        timeid: '02000230P',
        timename: '02:00 - 2:30',
        daytime: 'PM',
        classname: 'p-1',
        style:''
    },
    {
        timeid: '02300300P',
        timename: '02:30 - 3:00',
        daytime: 'PM',
        classname: 'p-1',
        style:''
    },
    {
        timeid: '03000330P',
        timename: '03:00 - 3:30',
        daytime: 'PM',
        classname: 'p-1',
        style:''
    },
    {
        timeid: '03300400P',
        timename: '03:30 - 4:00',
        daytime: 'PM',
        classname: 'p-1',
        style:''
    },
    {
        timeid: '04000430P',
        timename: '04:00 - 4:30',
        daytime: 'PM',
        classname: 'p-1',
        style:''
    },
    {
        timeid: '04300500P',
        timename: '04:30 - 5:00',
        daytime: 'PM',
        classname: 'p-1',
        style:''
    },
    {
        timeid: '05000530P',
        timename: '05:00 - 5:30',
        daytime: 'PM',
        classname: 'p-1',
        style:''
    },
    {
        timeid: '05300600P',
        timename: '05:30 - 6:00',
        daytime: 'PM',
        classname: 'p-1',
        style:''
    },
    {
        timeid: '06000630P',
        timename: '06:00 - 6:30',
        daytime: 'PM',
        classname: 'p-1',
        style:''
    },
    {
        timeid: '06300700P',
        timename: '06:30 - 7:00',
        daytime: 'PM',
        classname: 'p-1',
        style:''
    },
    {
        timeid: '07000730P',
        timename: '07:00 - 7:30',
        daytime: 'PM',
        classname: 'p-1',
        style:''
    },
    {
        timeid: '07300800P',
        timename: '07:30 - 8:00',
        daytime: 'PM',
        classname: 'p-1',
        style:''
    },
    {
        timeid: '08000830P',
        timename: '08:00 - 8:30',
        daytime: 'PM',
        classname: 'p-1',
        style:''
    },
    {
        timeid: '08300900P',
        timename: '08:30 - 9:00',
        daytime: 'PM',
        classname: 'p-1',
        style:''
    },
    {
        timeid: '09000930P',
        timename: '09:00 - 9:30',
        daytime: 'PM',
        classname: 'p-1',
        style:''
    },
    {
        timeid: '09301000P',
        timename: '09:30 - 10:00',
        daytime: 'PM',
        classname: 'p-1',
        style:''
    },
    {
        timeid: '10001030P',
        timename: '10:00 - 10:30',
        daytime: 'PM',
        classname: 'p-1',
        style:''
    },
    {
        timeid: '10301100P',
        timename: '10:30 - 11:00',
        daytime: 'PM',
        classname: 'p-1',
        style:''
    }
])


</script>
<template>
    <div>
        <div class="p-3 mb-4 border-bottom">
            <h5 class=" text-uppercase fw-bold">Faculty Dashboard</h5>
        </div>

        <!-- <div v-if="preLoading">
            <NeuLoader1/>
        </div> -->

        <div>
            <SkeletonHeaderLoader :elementcount="1" v-if="preLoading"/>
            <div v-else class="p-3 d-flex gap-2 justify-content-between mb-3">
                <div class="neu-card p-4 d-flex flex-column justify-content-center align-items-center" style="width: 100%; height: 100%;">
                    <table class="w-100 table-fixed small-font">
                        <thead>
                            <tr>
                                <th v-for="(hd, index) in headers" :key="index"
                                    class="p-3 text-center text-uppercase">
                                    {{ hd.title }}
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            <!-- <tr v-for="(tm, index) in loadSched" :key="index">

                                <td class="border p-1 text-center">
                                    {{ tm.timename }} {{ tm.daytime }}
                                </td>

                                <td class="border p-1 text-center">

                                    <template v-for="sched in tm.schedules" :key="sched.ln_id">
                                        <div v-if="sched.day === 'mon'">
                                            <p>{{ sched.subject_code }}</p>
                                            <p>{{ sched.subject_description }}</p>
                                            <p>{{ sched.prog_code }} - {{ sched.grad_name }} - {{ sched.sec_name }}</p>
                                            <p>{{ sched.building_name }} - {{ sched.room_name }}</p>
                                        </div>
                                    </template>
                                </td>

                                <td class="border p-1 text-center">
                                    <template v-for="sched in tm.schedules" :key="sched.ln_id">
                                        <div v-if="sched.day === 'tue'">
                                            <p>{{ sched.subject_code }}</p>
                                            <p>{{ sched.subject_description }}</p>
                                            <p>{{ sched.prog_code }} - {{ sched.grad_name }} - {{ sched.sec_name }}</p>
                                            <p>{{ sched.building_name }} - {{ sched.room_name }}</p>
                                        </div>
                                    </template>
                                </td>

                                <td class="border p-1 text-center">
                                    <template v-for="sched in tm.schedules" :key="sched.ln_id">
                                        <div v-if="sched.day === 'wed'">
                                            <p>{{ sched.subject_code }}</p>
                                            <p>{{ sched.subject_description }}</p>
                                            <p>{{ sched.prog_code }} - {{ sched.grad_name }} - {{ sched.sec_name }}</p>
                                            <p>{{ sched.building_name }} - {{ sched.room_name }}</p>
                                        </div>
                                    </template>
                                </td>

                                <td class="border p-1 text-center">
                                    <template v-for="sched in tm.schedules" :key="sched.ln_id">
                                        <div v-if="sched.day === 'thurs'">
                                            <p>{{ sched.subject_code }}</p>
                                            <p>{{ sched.subject_description }}</p>
                                            <p>{{ sched.prog_code }} - {{ sched.grad_name }} - {{ sched.sec_name }}</p>
                                            <p>{{ sched.building_name }} - {{ sched.room_name }}</p>
                                        </div>
                                    </template>
                                </td>

                                <td class="border p-1 text-center">
                                    <template v-for="sched in tm.schedules" :key="sched.ln_id">
                                        <div v-if="sched.day === 'fri'">
                                            <p>{{ sched.subject_code }}</p>
                                            <p>{{ sched.subject_description }}</p>
                                            <p>{{ sched.prog_code }} - {{ sched.grad_name }} - {{ sched.sec_name }}</p>
                                            <p>{{ sched.building_name }} - {{ sched.room_name }}</p>
                                        </div>
                                    </template>
                                </td>

                                <td class="border p-1 text-center">
                                    <template v-for="sched in tm.schedules" :key="sched.ln_id">
                                        <div v-if="sched.day === 'sat'">
                                            <p>{{ sched.subject_code }}</p>
                                            <p>{{ sched.subject_description }}</p>
                                            <p>{{ sched.prog_code }} - {{ sched.grad_name }} - {{ sched.sec_name }}</p>
                                            <p>{{ sched.building_name }} - {{ sched.room_name }}</p>
                                        </div>
                                    </template>
                                </td>

                                <td class="border p-1 text-center">
                                    <template v-for="sched in tm.schedules" :key="sched.ln_id">
                                        <div v-if="sched.day === 'sun'">
                                            <p>{{ sched.subject_code }}</p>
                                            <p>{{ sched.subject_description }}</p>
                                            <p>{{ sched.prog_code }} - {{ sched.grad_name }} - {{ sched.sec_name }}</p>
                                            <p>{{ sched.building_name }} - {{ sched.room_name }}</p>
                                        </div>
                                    </template>
                                </td>

                            </tr> -->
                            <tr v-for="(tm,index) in loadSched" :key="index">

                                <!-- TIME -->
                                <td class="border p-1 text-center">
                                    {{tm.timename}} {{tm.daytime}}
                                </td>


                                <!-- DAYS -->
                                <template v-for="day in ['mon','tue','wed','thurs','fri','sat','sun']">

                                    <td
                                        v-if="shouldRenderCell(index, day)"
                                        :rowspan="getRowSpan(index, day)"
                                        class="border p-1 text-center align-middle"
                                        :style="{
                                            backgroundColor: getScheduleColor(getDaySchedule(tm, day)),
                                            border: '2px solid #000'
                                        }"
                                    >

                                        <template v-if="getDaySchedule(tm, day)">
                                            <p class="fw-bold">{{ getDaySchedule(tm, day).subject_code }}</p>
                                            <p>{{ getDaySchedule(tm, day).subject_description }}</p>
                                            <p>{{ getDaySchedule(tm, day).grad_name }} - {{ getDaySchedule(tm, day).prog_code }} (<span class="fw-bold">{{ getDaySchedule(tm, day).sec_name }}</span>)</p>
                                            <p>{{ getDaySchedule(tm, day).building_name }} - {{ getDaySchedule(tm, day).room_name }}</p>
                                        </template>

                                    </td>

                                </template>

                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
    .mon-color{
        background-color: aqua;
    }
    .tue-color{
        background-color: rgb(68, 0, 255);
    }
    .wed-color{
        background-color: rgb(21, 255, 0);
    }
    .thurs-color{
        background-color: rgb(68, 0, 255);
    }
    .fri-color{
        background-color: rgb(255, 0, 179);
    }
    .sat-color{
        background-color: rgb(208, 255, 0);
    }
    .sun-color{
        background-color: rgb(174, 0, 255);
    }
       
</style>