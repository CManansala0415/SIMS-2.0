<script setup>
import { ref, onMounted, computed } from 'vue';
import {
    getCurriculumStudent,
    getCurriculumSubject,
    addSchedule,
    getSchedule,
    getSubject,
    getScheduledFaculty,
    getFacultyAvailability

} from "../../Fetchers.js";
import Loader from '../loaders/Loading1.vue';
import LaunchSchedulerModal from './LaunchSchedulerModal.vue';
import LaunchOccupancyModal from './LaunchOccupancyModal.vue';
import LaunchFacultyModal from './LaunchFacultyModal.vue';

import { getUserID } from '../../../routes/user.js';

const props = defineProps({
    launchData: {
    },
    buildingData: {
    },
    classroomData: {
    },
})

const launch = computed(() => {
    return props.launchData
});
const building = computed(() => {
    return props.buildingData
});
const classroom = computed(() => {
    return props.classroomData
});

const emit = defineEmits(['close-sched'])
const close = (data) => {
    window.stop()
    emit('close-sched')
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
])
const time = ref([
    {
        timeid: '06000630A',
        timename: '6:00 - 6:30',
        daytime: 'AM',
        classname: 'border align-middle bg-primary-subtle small-font',
    },
    {
        timeid: '06300700A',
        timename: '6:30 - 7:00',
        daytime: 'AM',
        classname: 'border align-middle bg-primary-subtle small-font',
    },
    {
        timeid: '07000730A',
        timename: '7:00 - 7:30',
        daytime: 'AM',
        classname: 'border align-middle bg-primary-subtle small-font',
    },
    {
        timeid: '07300800A',
        timename: '7:30 - 8:00',
        daytime: 'AM',
        classname: 'border align-middle bg-primary-subtle small-font',
    },
    {
        timeid: '08000830A',
        timename: '8:00 - 8:30',
        daytime: 'AM',
        classname: 'border align-middle bg-primary-subtle small-font',
    },
    {
        timeid: '08300900A',
        timename: '8:30 - 9:00',
        daytime: 'AM',
        classname: 'border align-middle bg-primary-subtle small-font',
    },
    {
        timeid: '09000930A',
        timename: '9:00 - 9:30',
        daytime: 'AM',
        classname: 'border align-middle bg-primary-subtle small-font',
    },
    {
        timeid: '09301000A',
        timename: '9:30 - 10:00',
        daytime: 'AM',
        classname: 'border align-middle bg-primary-subtle small-font',
    },
    {
        timeid: '10001030A',
        timename: '10:00 - 10:30',
        daytime: 'AM',
        classname: 'border align-middle bg-primary-subtle small-font',
    },
    {
        timeid: '10301100A',
        timename: '10:30 - 11:00',
        daytime: 'AM',
        classname: 'border align-middle bg-primary-subtle small-font',
    },
    {
        timeid: '11001130A',
        timename: '11:00 - 11:30',
        daytime: 'AM',
        classname: 'border align-middle bg-primary-subtle small-font',
    },
    {
        timeid: '11301200A',
        timename: '11:30 - 12:00',
        daytime: 'AM',
        classname: 'border align-middle bg-primary-subtle small-font',
    },
    {
        timeid: '12001230P',
        timename: '12:00 - 12:30',
        daytime: 'PM',
        classname: 'border align-middle bg-warning-subtle small-font',
    },
    {
        timeid: '12300100P',
        timename: '12:30 - 1:00',
        daytime: 'PM',
        classname: 'border align-middle bg-warning-subtle small-font',
    },
    {
        timeid: '01000130P',
        timename: '01:00 - 01:30',
        daytime: 'PM',
        classname: 'border align-middle bg-info-subtle small-font',
    },
    {
        timeid: '01300200P',
        timename: '1:30 - 2:00',
        daytime: 'PM',
        classname: 'border align-middle bg-info-subtle small-font',
    },
    {
        timeid: '02000230P',
        timename: '2:00 - 2:30',
        daytime: 'PM',
        classname: 'border align-middle bg-info-subtle small-font',
    },
    {
        timeid: '02300300P',
        timename: '2:30 - 3:00',
        daytime: 'PM',
        classname: 'border align-middle bg-info-subtle small-font',
    },
    {
        timeid: '03000330P',
        timename: '3:00 - 3:30',
        daytime: 'PM',
        classname: 'border align-middle bg-info-subtle small-font',
    },
    {
        timeid: '03300400P',
        timename: '3:30 - 4:00',
        daytime: 'PM',
        classname: 'border align-middle bg-info-subtle small-font',
    },
    {
        timeid: '04000430P',
        timename: '4:00 - 4:30',
        daytime: 'PM',
        classname: 'border align-middle bg-info-subtle small-font',
    },
    {
        timeid: '04300500P',
        timename: '4:30 - 5:00',
        daytime: 'PM',
        classname: 'border align-middle bg-info-subtle small-font',
    },
    {
        timeid: '05000530P',
        timename: '5:00 - 5:30',
        daytime: 'PM',
        classname: 'border align-middle bg-info-subtle small-font',
    },
    {
        timeid: '05300600P',
        timename: '5:30 - 6:00',
        daytime: 'PM',
        classname: 'border align-middle bg-info-subtle small-font',
    },
    {
        timeid: '06000630P',
        timename: '6:00 - 6:30',
        daytime: 'PM',
        classname: 'border align-middle bg-info-subtle small-font',
    },
    {
        timeid: '06300700P',
        timename: '6:30 - 7:00',
        daytime: 'PM',
        classname: 'border align-middle bg-info-subtle small-font',
    }
])

const curriculum = ref([])
const checking = ref(false)
const showScheduler = ref(false)
const showOccupancy = ref(false)
const showCalendar = ref(false)
const buildingId = ref('')
const classroomId = ref('')
const subjects = ref([])
const filteredSubjects = ref([])
const timeId = ref('')
const timeDay = ref('')
const timeIndex = ref('')
const addedSubjId = ref('')
const userID = ref('')
const saving = ref(false)
const savingCount = ref(0)
const scheduleList = ref([])
const loadedSched = ref([])
const preLoading = ref(false)
const showFaculty = ref(false)
const curriculumSubject = ref([])
const scheduledFaculty = ref([])
const faculty = ref([])
const facultyId = ref('')
const allow = ref(false)
const availability = ref([])

const booter = async () => {
    getCurriculumStudent(launch.value.prog_id, launch.value.dtype_id).then((results) => {
        curriculum.value = results
    })

    getSubject().then((results) => {
        subjects.value = results
        filteredSubjects.value = results
    })

    getCurriculumSubject(launch.value.ln_curriculum, launch.value.ln_quarter, launch.value.ln_gradelvl).then((results) => {
        curriculumSubject.value = results
    })

    getScheduledFaculty().then((results) => {
        faculty.value = results

    })

    getFacultyAvailability().then((results) => {
        availability.value = results
    })
}
onMounted(async () => {
    try {
        checking.value = true
        await booter().then((results) => {
            getUserID().then(async (results) => {
                userID.value = results.data.id

                let empid = ''
                curriculumSubject.value = curriculumSubject.value.map((e, index) => {

                    let indexer = faculty.value.findIndex(object => {
                        empid = object.emp_id
                        return ((object.lf_subjid === e.subj_id) && (object.lf_lnid == launch.value.ln_id));
                    });

                    if (indexer !== -1) {
                        return {
                            ...e,
                            faculty_id: empid,
                        }
                    } else {

                        return {
                            ...e,
                            faculty_id: '',
                        }
                    }
                })

                for await (const e of curriculumSubject.value) {
                    scheduledFaculty.value.push(e.faculty_id ? true : false)
                };

                //check if may false it means may subject pang walang faculty
                let checker = arr => arr.every(v => v === true);
                allow.value = checker(scheduledFaculty.value)
                checking.value = false
            })
        })
    } catch (err) {
        checking.value = false
        alert('error loading the list default components')
    }
})



const filterSubject = () => {
    filteredSubjects.value = subjects.value.filter(e => {
        if (
            (e.currtag_gradelvl == launch.value.ln_gradelvl) &&
            (e.currtag_sem == launch.value.ln_quarter)
        ) {
            return e
        }
    })
}

const assignSubject = (timeid, timeday, index, subjectid, building, classroom) => {
    timeId.value = timeid
    timeDay.value = timeday
    timeIndex.value = index
    showScheduler.value = true
    addedSubjId.value = subjectid
    buildingId.value = building
    classroomId.value = classroom
}

const getSched = (subjid, subjname, building, classroom, remove, facultyid) => {

    buildingId.value = building
    classroomId.value = classroom
    // showScheduler.value = false
    facultyId.value = facultyid

    //id na pinapasa sa scheduler if may nadd kanang subject
    switch (timeDay.value) {
        case 'Monday':
            loadedSched.value[timeIndex.value].sched_mon = subjid
            loadedSched.value[timeIndex.value].sched_mon_code = subjname
            loadedSched.value[timeIndex.value].sched_mon_bid = buildingId.value
            loadedSched.value[timeIndex.value].sched_mon_classrid = classroomId.value
            // loadedSched.value[timeIndex.value].sched_mon_faculty = facultyId.value
            break;
        case 'Tuesday':
            loadedSched.value[timeIndex.value].sched_tue = subjid
            loadedSched.value[timeIndex.value].sched_tue_code = subjname
            loadedSched.value[timeIndex.value].sched_tue_bid = buildingId.value
            loadedSched.value[timeIndex.value].sched_tue_classrid = classroomId.value
            // loadedSched.value[timeIndex.value].sched_tue_faculty = facultyId.value

            break;
        case 'Wednesday':
            loadedSched.value[timeIndex.value].sched_wed = subjid
            loadedSched.value[timeIndex.value].sched_wed_code = subjname
            loadedSched.value[timeIndex.value].sched_wed_bid = buildingId.value
            loadedSched.value[timeIndex.value].sched_wed_classrid = classroomId.value
            // loadedSched.value[timeIndex.value].sched_wed_faculty = facultyId.value

            break;
        case 'Thursday':
            loadedSched.value[timeIndex.value].sched_thurs = subjid
            loadedSched.value[timeIndex.value].sched_thurs_code = subjname
            loadedSched.value[timeIndex.value].sched_thurs_bid = buildingId.value
            loadedSched.value[timeIndex.value].sched_thurs_classrid = classroomId.value
            // loadedSched.value[timeIndex.value].sched_thurs_faculty = facultyId.value

            break;
        case 'Friday':
            loadedSched.value[timeIndex.value].sched_fri = subjid
            loadedSched.value[timeIndex.value].sched_fri_code = subjname
            loadedSched.value[timeIndex.value].sched_fri_bid = buildingId.value
            loadedSched.value[timeIndex.value].sched_fri_classrid = classroomId.value
            // loadedSched.value[timeIndex.value].sched_fri_faculty = facultyId.value
            break;
        case 'Saturday':
            loadedSched.value[timeIndex.value].sched_sat = subjid
            loadedSched.value[timeIndex.value].sched_sat_code = subjname
            loadedSched.value[timeIndex.value].sched_sat_bid = buildingId.value
            loadedSched.value[timeIndex.value].sched_sat_classrid = classroomId.value
            // loadedSched.value[timeIndex.value].sched_sat_faculty = facultyId.value
            break;
    }

    let indexer = scheduleList.value.findIndex(object => {
        return timeId.value === object.sched_time;
    });

    switch (timeDay.value) {
        case 'Monday':
            if ((Object.keys(scheduleList.value).length >= 1) && (typeof scheduleList.value[indexer] !== 'undefined')) {

                scheduleList.value[indexer].sched_mon = subjid
                scheduleList.value[indexer].sched_mon_code = subjname

                scheduleList.value[indexer].sched_mon_bid = building
                scheduleList.value[indexer].sched_mon_classrid = classroom
                scheduleList.value[indexer].sched_mon_remove = remove
                scheduleList.value[indexer].sched_mon_faculty = facultyid

            } else {

                scheduleList.value.push({
                    sched_time: timeId.value,
                    sched_lnid: launch.value.ln_id,

                    sched_mon: subjid,
                    sched_mon_code: subjname,
                    sched_mon_bid: building,
                    sched_mon_classrid: classroom,
                    sched_mon_remove: false,
                    sched_mon_faculty: facultyid,

                    sched_tue: '',
                    sched_tue_code: '',
                    sched_tue_bid: '',
                    sched_tue_classrid: '',
                    sched_tue_remove: false,
                    sched_tue_faculty: '',

                    sched_wed: '',
                    sched_wed_code: '',
                    sched_wed_bid: '',
                    sched_wed_classrid: '',
                    sched_wed_remove: false,
                    sched_wed_faculty: '',

                    sched_thurs: '',
                    sched_thurs_code: '',
                    sched_thurs_bid: '',
                    sched_thurs_classrid: '',
                    sched_thurs_remove: false,
                    sched_thurs_faculty: '',

                    sched_fri: '',
                    sched_fri_code: '',
                    sched_fri_bid: '',
                    sched_fri_classrid: '',
                    sched_fri_remove: false,
                    sched_fri_faculty: '',

                    sched_sat: '',
                    sched_sat_code: '',
                    sched_sat_bid: '',
                    sched_sat_classrid: '',
                    sched_sat_remove: false,
                    sched_sat_faculty: '',

                    sched_addedby: userID.value,
                })
            }
            break;
        case 'Tuesday':
            if ((Object.keys(scheduleList.value).length >= 1) && (typeof scheduleList.value[indexer] !== 'undefined')) {

                scheduleList.value[indexer].sched_tue = subjid
                scheduleList.value[indexer].sched_tue_code = subjname

                scheduleList.value[indexer].sched_tue_bid = building
                scheduleList.value[indexer].sched_tue_classrid = classroom
                scheduleList.value[indexer].sched_tue_remove = remove
                scheduleList.value[indexer].sched_tue_faculty = facultyid

            } else {

                scheduleList.value.push({
                    sched_time: timeId.value,
                    sched_lnid: launch.value.ln_id,

                    sched_mon: '',
                    sched_mon_code: '',
                    sched_mon_bid: '',
                    sched_mon_classrid: '',
                    sched_mon_remove: false,
                    sched_mon_faculty: '',

                    sched_tue: subjid,
                    sched_tue_code: subjname,
                    sched_tue_bid: building,
                    sched_tue_classrid: classroom,
                    sched_tue_remove: false,
                    sched_tue_faculty: facultyid,

                    sched_wed: '',
                    sched_wed_code: '',
                    sched_wed_bid: '',
                    sched_wed_classrid: '',
                    sched_wed_remove: false,
                    sched_wed_faculty: '',

                    sched_thurs: '',
                    sched_thurs_code: '',
                    sched_thurs_bid: '',
                    sched_thurs_classrid: '',
                    sched_thurs_remove: false,
                    sched_thurs_faculty: '',

                    sched_fri: '',
                    sched_fri_code: '',
                    sched_fri_bid: '',
                    sched_fri_classrid: '',
                    sched_fri_remove: false,
                    sched_fri_faculty: '',

                    sched_sat: '',
                    sched_sat_code: '',
                    sched_sat_bid: '',
                    sched_sat_classrid: '',
                    sched_sat_remove: false,
                    sched_sat_faculty: '',

                    sched_addedby: userID.value,
                })
            }
            break;
        case 'Wednesday':
            if ((Object.keys(scheduleList.value).length >= 1) && (typeof scheduleList.value[indexer] !== 'undefined')) {

                scheduleList.value[indexer].sched_wed = subjid
                scheduleList.value[indexer].sched_wed_code = subjname

                scheduleList.value[indexer].sched_wed_bid = building
                scheduleList.value[indexer].sched_wed_classrid = classroom
                scheduleList.value[indexer].sched_wed_remove = remove
                scheduleList.value[indexer].sched_wed_faculty = facultyid

            } else {
                scheduleList.value.push({
                    sched_time: timeId.value,
                    sched_lnid: launch.value.ln_id,

                    sched_mon: '',
                    sched_mon_code: '',
                    sched_mon_bid: '',
                    sched_mon_classrid: '',
                    sched_mon_remove: false,
                    sched_mon_faculty: '',

                    sched_tue: '',
                    sched_tue_code: '',
                    sched_tue_bid: '',
                    sched_tue_classrid: '',
                    sched_tue_remove: false,
                    sched_tue_faculty: '',

                    sched_wed: subjid,
                    sched_wed_code: subjname,
                    sched_wed_bid: building,
                    sched_wed_classrid: classroom,
                    sched_wed_remove: false,
                    sched_wed_faculty: facultyid,

                    sched_thurs: '',
                    sched_thurs_code: '',
                    sched_thurs_bid: '',
                    sched_thurs_classrid: '',
                    sched_thurs_remove: false,
                    sched_thurs_faculty: '',

                    sched_fri: '',
                    sched_fri_code: '',
                    sched_fri_bid: '',
                    sched_fri_classrid: '',
                    sched_fri_remove: false,
                    sched_fri_faculty: '',

                    sched_sat: '',
                    sched_sat_code: '',
                    sched_sat_bid: '',
                    sched_sat_classrid: '',
                    sched_sat_remove: false,
                    sched_sat_faculty: '',

                    sched_addedby: userID.value,
                })
            }
            break;
        case 'Thursday':
            if ((Object.keys(scheduleList.value).length >= 1) && (typeof scheduleList.value[indexer] !== 'undefined')) {

                scheduleList.value[indexer].sched_thurs = subjid
                scheduleList.value[indexer].sched_thurs_code = subjname

                scheduleList.value[indexer].sched_thurs_bid = building
                scheduleList.value[indexer].sched_thurs_classrid = classroom
                scheduleList.value[indexer].sched_thurs_remove = remove
                scheduleList.value[indexer].sched_thurs_faculty = facultyid

            } else {
                scheduleList.value.push({
                    sched_time: timeId.value,
                    sched_lnid: launch.value.ln_id,

                    sched_mon: '',
                    sched_mon_code: '',
                    sched_mon_bid: '',
                    sched_mon_classrid: '',
                    sched_mon_remove: false,
                    sched_mon_faculty: '',

                    sched_tue: '',
                    sched_tue_code: '',
                    sched_tue_bid: '',
                    sched_tue_classrid: '',
                    sched_tue_remove: false,
                    sched_tue_faculty: '',

                    sched_wed: '',
                    sched_wed_code: '',
                    sched_wed_bid: '',
                    sched_wed_classrid: '',
                    sched_wed_remove: false,
                    sched_wed_faculty: '',

                    sched_thurs: subjid,
                    sched_thurs_code: subjname,
                    sched_thurs_bid: building,
                    sched_thurs_classrid: classroom,
                    sched_thurs_remove: false,
                    sched_thurs_faculty: facultyid,

                    sched_fri: '',
                    sched_fri_code: '',
                    sched_fri_bid: '',
                    sched_fri_classrid: '',
                    sched_fri_remove: false,
                    sched_fri_faculty: '',

                    sched_sat: '',
                    sched_sat_code: '',
                    sched_sat_bid: '',
                    sched_sat_classrid: '',
                    sched_sat_remove: false,
                    sched_sat_faculty: '',

                    sched_addedby: userID.value,
                })
            }
            break;
        case 'Friday':
            if ((Object.keys(scheduleList.value).length >= 1) && (typeof scheduleList.value[indexer] !== 'undefined')) {

                scheduleList.value[indexer].sched_fri = subjid
                scheduleList.value[indexer].sched_fri_code = subjname

                scheduleList.value[indexer].sched_fri_bid = building
                scheduleList.value[indexer].sched_fri_classrid = classroom
                scheduleList.value[indexer].sched_fri_remove = remove
                scheduleList.value[indexer].sched_fri_faculty = facultyid

            } else {
                scheduleList.value.push({
                    sched_time: timeId.value,
                    sched_lnid: launch.value.ln_id,

                    sched_mon: '',
                    sched_mon_code: '',
                    sched_mon_bid: '',
                    sched_mon_classrid: '',
                    sched_mon_remove: false,
                    sched_mon_faculty: '',

                    sched_tue: '',
                    sched_tue_code: '',
                    sched_tue_bid: '',
                    sched_tue_classrid: '',
                    sched_tue_remove: false,
                    sched_tue_faculty: '',

                    sched_wed: '',
                    sched_wed_code: '',
                    sched_wed_bid: '',
                    sched_wed_classrid: '',
                    sched_wed_remove: false,
                    sched_wed_faculty: '',

                    sched_thurs: '',
                    sched_thurs_code: '',
                    sched_thurs_bid: '',
                    sched_thurs_classrid: '',
                    sched_thurs_remove: false,
                    sched_thurs_faculty: '',

                    sched_fri: subjid,
                    sched_fri_code: subjname,
                    sched_fri_bid: building,
                    sched_fri_classrid: classroom,
                    sched_fri_remove: false,
                    sched_fri_faculty: facultyid,

                    sched_sat: '',
                    sched_sat_code: '',
                    sched_sat_bid: '',
                    sched_sat_classrid: '',
                    sched_sat_remove: false,
                    sched_sat_faculty: '',


                    sched_addedby: userID.value,
                })
            }
            break;
        case 'Saturday':
            if ((Object.keys(scheduleList.value).length >= 1) && (typeof scheduleList.value[indexer] !== 'undefined')) {

                scheduleList.value[indexer].sched_sat = subjid
                scheduleList.value[indexer].sched_sat_code = subjname

                scheduleList.value[indexer].sched_sat_bid = building
                scheduleList.value[indexer].sched_sat_classrid = classroom
                scheduleList.value[indexer].sched_sat_remove = remove
                scheduleList.value[indexer].sched_sat_faculty = facultyid

            } else {
                scheduleList.value.push({
                    sched_time: timeId.value,
                    sched_lnid: launch.value.ln_id,

                    sched_mon: '',
                    sched_mon_code: '',
                    sched_mon_bid: '',
                    sched_mon_classrid: '',
                    sched_mon_remove: false,
                    sched_mon_faculty: '',

                    sched_tue: '',
                    sched_tue_code: '',
                    sched_tue_bid: '',
                    sched_tue_classrid: '',
                    sched_tue_remove: false,
                    sched_tue_faculty: '',

                    sched_wed: '',
                    sched_wed_code: '',
                    sched_wed_bid: '',
                    sched_wed_classrid: '',
                    sched_wed_remove: false,
                    sched_wed_faculty: '',

                    sched_thurs: '',
                    sched_thurs_code: '',
                    sched_thurs_bid: '',
                    sched_thurs_classrid: '',
                    sched_thurs_remove: false,
                    sched_thurs_faculty: '',

                    sched_fri: '',
                    sched_fri_code: '',
                    sched_fri_bid: '',
                    sched_fri_classrid: '',
                    sched_fri_remove: false,
                    sched_fri_faculty: '',

                    sched_sat: subjid,
                    sched_sat_code: subjname,
                    sched_sat_bid: building,
                    sched_sat_classrid: classroom,
                    sched_sat_remove: false,
                    sched_sat_faculty: facultyid,


                    sched_addedby: userID.value,
                })
            }
            break;
    }


}

const clearSched = () => {
    if (confirm("Are you sure you want to revert values?") == true) {
        // var elements = document.getElementsByTagName("input");
        // for (var ii=0; ii < elements.length; ii++) {
        //     if (elements[ii].type == "text") {
        //         elements[ii].value = "";
        //     }
        //     if (elements[ii].type == "hidden") {
        //         elements[ii].value = "";
        //     }
        //     scheduleList.value = []
        // }
        loadSched()
    } else {
        return false;
    }
}

const saveSched = () => {
    saving.value = true
    scheduleList.value.forEach(async (e) => {
        addSchedule(e).then((results) => {
            savingCount.value += 1
            if (Object.keys(scheduleList.value).length == savingCount.value) {
                alert('Successfull Saved')
                saving.value = false
                savingCount.value = 0
                scheduleList.value = []
                loadedSched.value = []
                loadSched()
            }
        })
    })
}

const loadSched = () => {

    preLoading.value = true
    getFacultyAvailability().then((results) => {
        availability.value = results

        getSchedule(launch.value.ln_id).then((results) => {
            loadedSched.value = time.value.map((e, index) => {

                let indexer = results.findIndex(object => {
                    return object.sched_time === e.timeid;
                });

                let mon_faculty = ''
                let mon_faculty_occid = ''
                availability.value.filter((object) => {
                    if (object.occ_time === e.timeid && object.occ_day == 'Monday') {
                        mon_faculty = object.occ_faculty
                        mon_faculty_occid = object.occ_id
                    }
                })

                let tue_faculty = ''
                let tue_faculty_occid = ''
                availability.value.filter((object) => {
                    if (object.occ_time === e.timeid && object.occ_day == 'Tuesday') {
                        tue_faculty = object.occ_faculty
                        tue_faculty_occid = object.occ_id
                    }
                })

                let wed_faculty = ''
                let wed_faculty_occid = ''
                availability.value.filter((object) => {
                    if (object.occ_time === e.timeid && object.occ_day == 'Wednesday') {
                        wed_faculty = object.occ_faculty
                        wed_faculty_occid = object.occ_id
                    }
                })

                let thurs_faculty = ''
                let thurs_faculty_occid = ''
                availability.value.filter((object) => {
                    if (object.occ_time === e.timeid && object.occ_day == 'Thursday') {
                        thurs_faculty = object.occ_faculty
                        thurs_faculty_occid = object.occ_id
                    }
                })

                let fri_faculty = ''
                let fri_faculty_occid = ''
                availability.value.filter((object) => {
                    if (object.occ_time === e.timeid && object.occ_day == 'Friday') {
                        fri_faculty = object.occ_faculty
                        fri_faculty_occid = object.occ_id
                    }
                })

                let sat_faculty = ''
                let sat_faculty_occid = ''
                availability.value.filter((object) => {
                    if (object.occ_time === e.timeid && object.occ_day == 'Saturday') {
                        sat_faculty = object.occ_faculty
                        sat_faculty_occid = object.occ_id
                    }
                })

                if (indexer !== -1) {

                    return {
                        ...e,
                        sched_id: results[indexer].sched_id,
                        sched_lnid: results[indexer].sched_lnid,
                        sched_time: results[indexer].sched_time,

                        sched_mon_remove: false,
                        sched_mon: results[indexer].sched_mon,
                        sched_mon_code: results[indexer].sched_mon_code,
                        sched_mon_bid: results[indexer].sched_mon_bid,
                        sched_mon_classrid: results[indexer].sched_mon_classrid,
                        sched_mon_faculty: mon_faculty,
                        sched_mon_faculty_occid: mon_faculty_occid,

                        sched_tue_remove: false,
                        sched_tue: results[indexer].sched_tue,
                        sched_tue_code: results[indexer].sched_tue_code,
                        sched_tue_bid: results[indexer].sched_tue_bid,
                        sched_tue_classrid: results[indexer].sched_tue_classrid,
                        sched_tue_faculty: tue_faculty,
                        sched_tue_faculty_occid: tue_faculty_occid,

                        sched_wed_remove: false,
                        sched_wed: results[indexer].sched_wed,
                        sched_wed_code: results[indexer].sched_wed_code,
                        sched_wed_bid: results[indexer].sched_wed_bid,
                        sched_wed_classrid: results[indexer].sched_wed_classrid,
                        sched_wed_faculty: wed_faculty,
                        sched_wed_faculty_occid: wed_faculty_occid,

                        sched_thurs_remove: false,
                        sched_thurs: results[indexer].sched_thurs,
                        sched_thurs_code: results[indexer].sched_thurs_code,
                        sched_thurs_bid: results[indexer].sched_thurs_bid,
                        sched_thurs_classrid: results[indexer].sched_thurs_classrid,
                        sched_thurs_faculty: thurs_faculty,
                        sched_thurs_faculty_occid: thurs_faculty_occid,

                        sched_fri_remove: false,
                        sched_fri: results[indexer].sched_fri,
                        sched_fri_code: results[indexer].sched_fri_code,
                        sched_fri_bid: results[indexer].sched_fri_bid,
                        sched_fri_classrid: results[indexer].sched_fri_classrid,
                        sched_fri_faculty: fri_faculty,
                        sched_fri_faculty_occid: fri_faculty_occid,

                        sched_sat_remove: false,
                        sched_sat: results[indexer].sched_sat,
                        sched_sat_code: results[indexer].sched_sat_code,
                        sched_sat_bid: results[indexer].sched_sat_bid,
                        sched_sat_classrid: results[indexer].sched_sat_classrid,
                        sched_sat_faculty: sat_faculty,
                        sched_sat_faculty_occid: sat_faculty_occid,

                    }
                } else {
                    return {
                        ...e,
                        sched_mon: '',
                        sched_mon_code: '',
                        sched_tue: '',
                        sched_tue_code: '',
                        sched_wed: '',
                        sched_wed_code: '',
                        sched_thurs: '',
                        sched_thurs_code: '',
                        sched_fri: '',
                        sched_fri_code: '',
                        sched_sat: '',
                        sched_sat_code: '',
                    }
                }

            })


            loadedSched.value.forEach(async (e) => {
                if (e.sched_id) {
                    scheduleList.value.push(e)
                }
            })

            preLoading.value = false
            showCalendar.value = true
            // console.log(loadedSched.value)
            // console.log(showCalendar.value)


        })
    })




}

const loadOccupancy = () => {
    showOccupancy.value = !showOccupancy.value
}

const loadFaculty = () => {
    showFaculty.value = !showFaculty.value
}

</script>
<template>
    <div v-if="!checking" class="p-1 d-flex gap-2 justify-content-end mb-3">
            <div class="d-flex flex-wrap w-50 justify-content-end">
                <button tabindex="-1"
                    @click="close" type="button" class="btn btn-sm btn-primary"
                    :disabled="preLoading ? true : false"> <font-awesome-icon icon="fa-solid fa-rotate-left"/> Back
                </button>
            </div>
        </div>
    <div class="border p-3">
        <div class="w-100">
            <div class="w-100 align-content-center p-3">
                <span class="fw-bold">{{ launch.prog_name }}({{ launch.prog_code }})</span>
            </div>
            <div class="d-flex gap-2 p-2 small-font mb-3">
                <div class="w-100 border align-content-center rounded-2 shadow p-3">
                    <span class="">Semester:</span><span class="text-success fw-bold">{{ launch.quar_code }}</span>
                </div>
                <div class="w-100 border align-content-center rounded-2 shadow p-3">
                    <span class="">Degree:</span> <span class="text-success fw-bold">{{ launch.dtype_desc }}</span>
                </div>
                <div class="w-100 border align-content-center rounded-2 shadow p-3">
                    <span class="">Grade Level:</span> <span class="text-success fw-bold">{{ launch.grad_name }}</span>
                </div>
                <div class="w-100 border align-content-center rounded-2 shadow p-3">
                    <span class="">Section:</span> <span class="text-success fw-bold">{{ launch.sec_name }}</span>
                </div>
                <div class="w-100 border align-content-center rounded-2 shadow p-3">
                    <span class="">Academic Year:</span> <span class="text-success fw-bold">{{ launch.ln_year }}</span>
                </div>
                <div class="w-100 border align-content-center rounded-2 shadow p-3">
                    <span class="">Slots:</span> <span class="text-success fw-bold">{{ launch.ln_slots }}</span>
                </div>
            </div>
        </div>
        <div class="">
            <div v-if="!checking && Object.keys(curriculum).length"
                :class="saving || preLoading ? 'd-flex gap-2 pe-none p-3 bg-secondary-subtle justify-content-center' : 'd-flex gap-2 pe-auto p-3 bg-secondary-subtle justify-content-center'">
                <button :disabled="saving || preLoading ? true : false" @click="loadSched()" tabindex="-1"
                    title="Load Schedule" type="button" class="btn btn-sm btn-dark">
                    Load Schedule
                </button>
                <button v-if="Object.keys(loadedSched).length" :disabled="saving || preLoading ? true : false"
                    @click="loadOccupancy()" tabindex="-1" title="Load Schedule" type="button" data-bs-toggle="modal"
                    data-bs-target="#occupancymodal" class="btn btn-sm btn-dark">
                    View Occupancy
                </button>
                <button v-if="Object.keys(loadedSched).length" :disabled="saving || preLoading ? true : false"
                    @click="loadFaculty()" tabindex="-1" title="Load Faculties" type="button" data-bs-toggle="modal"
                    data-bs-target="#facultymodal" class="btn btn-sm btn-dark">
                    Assign Faculties
                </button>
            </div>
            <div v-if="!checking && !Object.keys(curriculum).length" class="w-100 mt-4 p-4">
                <p class="text-danger fw-bold">No Curriculum Found</p>
                <p class="small-font">To Enable schedule customization, make sure the curriculum tagged is existing
                    and has a tagged subjects in it.</p>
            </div>
            <div v-if="checking && !Object.keys(curriculum).length" class="w-100 mt-4 p-4">
                <Loader />
            </div>

            <div v-if="!preLoading && !saving && showCalendar" class="w-100">
                <div v-if="!allow" class="w-100 h-100 flex flex-col items-center justify-center">
                    <p class="text 2xl font-semibold text-red-500">Restricted</p>
                    <p class="">Please assign corresponding faculty for each subject first to access schedule
                        editor.</p>
                </div>
                <table v-else class="w-100 table-fixed">
                    <thead>
                        <th v-for="(hd, index) in headers" class="p-3">{{
                            hd.title }}</th>
                    </thead>
                    <tbody>
                        <tr v-for="(tm, index) in loadedSched" class="p-3">
                            <td class="w-25 bg-success-subtle">
                                {{ tm.timename }} {{ tm.daytime }}
                            </td>
                            <td :class="tm.classname">
                                <input class="form-control form-control-sm border-0" data-bs-toggle="modal"
                                    data-bs-target="#schedulermodal"
                                    @click="assignSubject(tm.timeid, 'Monday', index, tm.sched_mon, tm.sched_mon_bid, tm.sched_mon_classrid)"
                                    readonly v-model="tm.sched_mon_code" />
                                <input type="hidden" readonly v-model="tm.sched_mon" />
                            </td>
                            <td :class="tm.classname">
                                <input class="form-control form-control-sm border-0" data-bs-toggle="modal"
                                    data-bs-target="#schedulermodal"
                                    @click="assignSubject(tm.timeid, 'Tuesday', index, tm.sched_tue, tm.sched_tue_bid, tm.sched_tue_classrid)"
                                    readonly v-model="tm.sched_tue_code" />
                                <input type="hidden" readonly v-model="tm.sched_tue" />
                            </td>
                            <td :class="tm.classname">
                                <input class="form-control form-control-sm border-0" data-bs-toggle="modal"
                                    data-bs-target="#schedulermodal"
                                    @click="assignSubject(tm.timeid, 'Wednesday', index, tm.sched_wed, tm.sched_wed_bid, tm.sched_wed_classrid)"
                                    readonly v-model="tm.sched_wed_code" />
                                <input type="hidden" readonly v-model="tm.sched_wed" />
                            </td>
                            <td :class="tm.classname">
                                <input class="form-control form-control-sm border-0" data-bs-toggle="modal"
                                    data-bs-target="#schedulermodal"
                                    @click="assignSubject(tm.timeid, 'Thursday', index, tm.sched_thurs, tm.sched_thurs_bid, tm.sched_thurs_classrid)"
                                    readonly v-model="tm.sched_thurs_code" />
                                <input type="hidden" readonly v-model="tm.sched_thurs" />
                            </td>
                            <td :class="tm.classname">
                                <input class="form-control form-control-sm border-0" data-bs-toggle="modal"
                                    data-bs-target="#schedulermodal"
                                    @click="assignSubject(tm.timeid, 'Friday', index, tm.sched_fri, tm.sched_fri_bid, tm.sched_fri_classrid)"
                                    readonly v-model="tm.sched_fri_code" />
                                <input type="hidden" readonly v-model="tm.sched_fri" />
                            </td>
                            <td :class="tm.classname">
                                <input class="form-control form-control-sm border-0" data-bs-toggle="modal"
                                    data-bs-target="#schedulermodal"
                                    @click="assignSubject(tm.timeid, 'Saturday', index, tm.sched_sat, tm.sched_sat_bid, tm.sched_sat_classrid)"
                                    readonly v-model="tm.sched_sat_code" />
                                <input type="hidden" readonly v-model="tm.sched_sat" />
                            </td>
                        </tr>

                    </tbody>
                </table>
                <div v-if="allow" class="mt-3 mb-3 w-100 d-flex gap-2 justify-content-end">
                    <button @click="clearSched()" tabindex="-1" title="Assign Schedule" type="button"
                        class="btn btn-sm btn-dark">
                        <i class="mr-2 fa-solid fa-arrows-rotate"></i>Refresh Schedule
                    </button>
                    <button @click="saveSched()" tabindex="-1" title="Assign Schedule" type="button"
                        class=" btn btn-sm btn-primary">
                        <i class="mr-2 fa-solid fa-floppy-disk"></i>Save Schedule
                    </button>
                </div>
            </div>

            <div v-if="preLoading" class="w-100 h-100 bg-opacity-55 border">
                <div class="p-3 flex flex-col items-center justify-center">
                    <Loader />
                    <p class=" mt-3">Retrieving Items Please Wait...</p>
                </div>
            </div>
            <div v-if="saving" class="w-100 h-100 bg-opacity-55 border">
                <div class="p-3 flex flex-col items-center justify-center">
                    <Loader />
                    <p class=" mt-3">{{ savingCount }} out of {{ Object.keys(scheduleList).length }}</p>
                    <p class=" mt-3">Saving Items Please Wait...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Scheduler Modal -->
    <div class="modal fade" id="schedulermodal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Schedule Assignment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        @click="showScheduler = false"></button>
                </div>
                <div class="modal-body">
                    <LaunchSchedulerModal v-if="showScheduler" :subjectData="filteredSubjects" :subjidData="addedSubjId"
                        :timeidData="timeId" :timedayData="timeDay" :buildingData="building" :classroomData="classroom"
                        :buildingIdData="buildingId" :classroomIdData="classroomId" :curriculumData="curriculum"
                        :curriculumIdData="launch.ln_curriculum" :launchIdData="launch.ln_id" :launchdata="launch"
                        @assign-sched="getSched" />
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <div class="form-group">
                        <small id="emailHelp" class="form-text text-muted">We'll never share your personal information
                            with anyone
                            else (Data Privacy Act of 2012)</small>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                            @click="showScheduler = false">Close</button>
                        <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Occupancy Modal -->
    <div class="modal fade" id="occupancymodal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Occupancy</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        @click="showOccupancy = false"></button>
                </div>
                <div class="modal-body">
                    <LaunchOccupancyModal :launchData="launch" :buildingData="building" :classroomData="classroom"
                        :headerData="headers" :timeData="time" />
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <div class="form-group">
                        <small id="emailHelp" class="form-text text-muted">We'll never share your personal information
                            with anyone
                            else (Data Privacy Act of 2012)</small>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                            @click="showOccupancy = false">Close</button>
                        <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Faculty Modal -->
    <div class="modal fade" id="facultymodal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Faculty Assignment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        @click="showFaculty = false"></button>
                </div>
                <div class="modal-body">
                    <LaunchFacultyModal :subjectData="filteredSubjects"
                           :curriculumIdData="launch.ln_curriculum"
                           :launchIdData="launch.ln_id"
                           :semIdData="launch.ln_quarter"
                           :gradelvlData="launch.ln_gradelvl"/>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <div class="form-group">
                        <small id="emailHelp" class="form-text text-muted">We'll never share your personal information
                            with anyone
                            else (Data Privacy Act of 2012)</small>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                            @click="showFaculty = false">Close</button>
                        <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
                    </div>
                </div>
            </div>
        </div>
    </div>

</template>