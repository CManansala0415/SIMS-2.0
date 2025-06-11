<script setup>
import { ref, onMounted, computed } from 'vue';
import Loader from '../snippets/loaders/Loading1.vue';
import ExcelDownloaderGrades from '../snippets/modal/ExcelDownloaderGrades.vue';
// import TipForm from '../snippets/modal/TipForm.vue';
// import ApplicationForm from './ApplicationForm.vue';
import { getUserID } from "../../routes/user";
import { useRouter, useRoute } from 'vue-router';
import {
    getFacultyAssignment,
    getFacultyStudent,
    getFacultyStudentMilestone,
    getGradelvl,
    getProgram,
    getQuarter,
    getDegree,
    getProgramList,
    getSemester,
    getSection,
    getGradingSheetHeader,
    getGradingSheetGrade,
    addGradingSheet,
    addGradingGrade,
    getAcademicDefaults
} from "../Fetchers.js";


const assignment = ref([])
const assignmentCount = ref(0)
const preLoading = ref(true)
const userID = ref('')
const router = useRouter();
const showForm = ref(false)
const showExcelDownload = ref(false)
const groupedAssignmentSection = ref([])
const groupedAssignmentSubject = ref([])
const groupedAssignmentStudent = ref([])
const finalStudentList = ref([])
const studentSubjects = ref([])
const filterSubjectId = ref(0)
const filterLnid = ref(0)
const toBeDownloaded = ref([])
const gradingHeader = ref([])
const gradingGrades = ref([])
const filteringData = ref(false)
const savingData = ref(false)
const openTipModal = ref(false)
const lnid = ref('')
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
const emit = defineEmits(['fetchUser'])

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

onMounted(async () => {

    getUserID().then(async (results1) => {
        userID.value = results1.account.data.id
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
                    let sectionkeys = Object.keys(groupedAssignmentSection.value)

                    sectionkeys.forEach((e) => {
                        let section = groupedAssignmentSection.value[e][0].ln_section
                        let course = groupedAssignmentSection.value[e][0].ln_course
                        let gradelvl = groupedAssignmentSection.value[e][0].ln_gradelvl

                        getFacultyStudentMilestone(section, gradelvl, course).then((results3) => {
                            groupedAssignmentStudent.value.push(results3.students)
                            studentSubjects.value.push(...results3.milestone)
                        })
                    })
                    preLoading.value = false
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
            });
        }
    }).catch((err) => {
        // alert('Unauthorized Session, Please Log In')
        Swal.fire({
            icon: "error",
            title: "Oops...",
            text: "Session expired, log in again",
        }).then(()=>{
            router.push("/");
            window.stop()
        });
    })
})




const filterSubject = () => {
    filteringData.value = true
    if (filterSubjectId.value && filterLnid.value) {
        finalStudentList.value = []
        let list = []
        let enrids = []
        let students = []

        // filter muna yung subjects na enrolled ni students
        studentSubjects.value.forEach((e, index) => {
            e.filter(f => {
                if (f.subj_id == filterSubjectId.value) {
                    enrids.push(f.enr_id)
                }
            })
        })

        // kunin lahat ng list ng students regardless anong section
        groupedAssignmentStudent.value.forEach((e, index1) => {
            e.forEach((f, index2) => {
                list.push(f.enr_id)
                students.push(f)
            })
        })

        // match students array vs subjects nila
        let exist = enrids.filter(element => list.includes(element));
        let filtration1 = []
        let filtration2 = []
        // run yung correct matches then filter naten students masterlist then push if match yung enr_id
        exist.forEach((e) => {
            students.filter((f) => {
                if (f.enr_id == e) {
                    filtration1.push(f)
                }
            })
        })


        //find lf_lnid para dun sa fetcher ng header ni grading sheet
        let sectionId = ''
        let lflnid = ''
        groupedAssignmentSubject.value[filterSubjectId.value].filter(e => {
            if (e.lf_lnid === filterLnid.value) {
                sectionId = e.sec_id
                lflnid = e.lf_lnid
            }
        });

        // catch if walang laman list ng students para di mag error
        if (!sectionId && !lflnid) {
            filtration1 = []
        }

        gradingHeader.value = []
        getGradingSheetHeader(lflnid).then((results) => {
            gradingHeader.value = results

            if (Object.keys(gradingHeader.value).length) {
                getGradingSheetGrade(gradingHeader.value[0].grh_id, filterSubjectId.value).then((results) => {

                    gradingGrades.value = results

                    let y = []
                    filtration1.forEach(e => {
                        let enrid = gradingGrades.value.findIndex(f => {
                            return e.enr_id === f.grs_enrid
                        });

                        let z = {
                            ...e,
                            ...gradingGrades.value[enrid]
                        }
                        y.push(z)
                    })

                    filtration2 = Object.groupBy(y, section => section.enr_section);
                    finalStudentList.value = filtration2[sectionId]
                    filteringData.value = false
                })
            } else {

                //filter by lnid ang ginamit natin dahil para unique sya na di mag merge sa ibang section
                if (!Object.keys(filtration1).length) {
                    finalStudentList.value = []
                    // alert('Records are Empty')
                    Swal.fire({
                        title: "Notice",
                        text: "Records are Empty",
                        icon: "question"
                    });
                } else {
                    filtration2 = Object.groupBy(filtration1, section => section.enr_section);
                    finalStudentList.value = filtration2[sectionId]
                    // alert('Records Loaded Successfully')
                    Swal.fire({
                        title: "Notice",
                        text: "Records Loaded Successfully",
                        icon: "success"
                    });
                }
                filteringData.value = false

            }
        })




    } else {
        // alert('Please Select Subject and Section')
        Swal.fire({
            title: "Notice",
            text: "Please Select Subject and Section",
            icon: "question"
        }).then(()=>{
            filteringData.value = false
        });
    }

}

const downloadExcel = () => {
    showExcelDownload.value = !showExcelDownload.value
    toBeDownloaded.value = finalStudentList.value
}

const saveGrades = () => {
    savingData.value = true
    let x = finalStudentList.value.map((e) => {
        let prelims = document.getElementById('prelims' + e.enr_id).value
        let midterms = document.getElementById('midterms' + e.enr_id).value
        let prefinals = document.getElementById('prefinals' + e.enr_id).value
        let finals = document.getElementById('finals' + e.enr_id).value

        return {
            ...e,
            grade_prelims: prelims,
            grade_midterms: midterms,
            grade_prefinals: prefinals,
            grade_finals: finals,
            grs_headerid: gradingHeader.value[0].grh_id,
            grs_user: userID.value,
            grs_mode: e.grs_id ? 1 : 0,
            grs_subjid: filterSubjectId.value
        }
    })

    addGradingGrade(x).then((results) => {
        if(results.status == 200){
            Swal.fire({
                title: "Update Successful",
                text: "Changes applied, refreshing the page",
                icon: "success"
            }).then(()=>{
                location.reload()
            });
        }else{
           Swal.fire("Changes are not saved", "", "info");
        }
    })
}

const generateSheet = (type) => {
    // if (confirm("Are you sure you want to generate a grading sheet?") == true) {
    //     savingData.value = true
    //     //find lf_lnid para dun sa fetcher ng header ni grading sheet
    //     let indexer = groupedAssignmentSubject.value[filterSubjectId.value].findIndex(e => {
    //         return e.lf_lnid === filterLnid.value
    //     });

    //     let x = {
    //         grh_lnid: groupedAssignmentSubject.value[filterSubjectId.value][indexer].lf_lnid,
    //         grh_addedby: userID.value,
    //         grs_headerid: Object.keys(gradingHeader.value).length ? gradingHeader.value[0].grh_id : null
    //     }

    //     addGradingSheet(x, type).then((results) => {
    //         if (results.status == 200) {
    //             // alert('Update Successful')
    //             // location.reload()
    //             Swal.fire({
    //                 title: "Update Successful",
    //                 text: "Changes applied, refreshing the page",
    //                 icon: "success"
    //             }).then(()=>{
    //                 location.reload()
    //             });
    //         } else {
    //             // alert('Update Failed')
    //             // location.reload()
    //             Swal.fire({
    //                 title: "Update Failed",
    //                 text: "Unknown error occured, try again later",
    //                 icon: "error"
    //             }).then(()=>{
    //                 location.reload()
    //             });
    //         }
    //     })
    // } else {
    //     return false;
    // }

    Swal.fire({
        title: "Generate Record",
        text: "Are you sure you want to generate a grading sheet?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, Im create it!"
    }).then(async (result) => {
        if (result.isConfirmed) {
            savingData.value = true
            //find lf_lnid para dun sa fetcher ng header ni grading sheet
            let indexer = groupedAssignmentSubject.value[filterSubjectId.value].findIndex(e => {
                return e.lf_lnid === filterLnid.value
            });

            let x = {
                grh_lnid: groupedAssignmentSubject.value[filterSubjectId.value][indexer].lf_lnid,
                grh_addedby: userID.value,
                grs_headerid: Object.keys(gradingHeader.value).length ? gradingHeader.value[0].grh_id : null
            }
            addGradingSheet(x, type).then((results) => {
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
    });
}


const openTip = () => {
    openTipModal.value = !openTipModal.value
}

</script>
<template>
    <div>
        <div class="p-3 mb-4 border-bottom">
            <h5 class=" text-uppercase fw-bold">Grading Sheet</h5>
        </div>
    </div>
    <div v-if="!showForm" class="table-responsive border p-3 small-font">
        <table v-if="switcher == 0" class="table table-hover">
            <thead>
                <tr>
                    <th style="background-color: #237a5b;" class="text-white">Course/Section</th>
                    <th style="background-color: #237a5b;" class="text-white">Subject</th>
                    <th style="background-color: #237a5b;" class="text-white">Action</th>
                </tr>
            </thead>
            <tbody>
                <tr v-if="!preLoading && Object.keys(groupedAssignmentSection).length"
                    >
                   
                    <td class="align-middle">
                        <select class="form-select form-select-sm w-100" v-model="filterLnid">
                            <option value="0">
                                Select Section
                            </option>
                            <option v-for="(gps, index1, index2) in groupedAssignmentSection" :value="gps[0].lf_lnid">
                                {{ gps[0].prog_code }} ({{ gps[0].sec_name }})
                            </option>
                        </select>
                    </td>
                    <td class="align-middle">
                        <select class="form-select form-select-sm w-100" v-model="filterSubjectId">
                            <option value="0">
                                Select Subject
                            </option>
                            <option v-for="(gpa, indexer) in groupedAssignmentSubject" :value="gpa[0].subj_id">
                                {{ gpa[0].subj_name }} {{ gpa[0].subj_code }}
                            </option>
                        </select>
                    </td>
                    <td class="align-middle">
                        <div class="d-flex gap-1">
                            <button :disabled="filteringData || savingData ? true : false" @click="filterSubject()"
                                tabindex="-1" type="button" class="btn btn-sm btn-secondary w-100">
                                <i class="mr-2 fa-solid fa-eye"></i>Open Grading Sheet
                            </button>
                            <!-- <button :disabled="filteringData || savingData ? true : false" @click="openTip()"
                                tabindex="-1" type="button" class="btn btn-sm btn-secondary w-100">
                                <i class="mr-2 fa-solid fa-question"></i>Open Help Tips
                            </button> -->
                        </div>
                    </td>
                </tr>
                <tr v-if="!preLoading && !Object.keys(groupedAssignmentSection).length">
                    <td class="align-middle" colspan="5">
                        No Records Found
                    </td>
                </tr>
                <tr v-if="preLoading && !Object.keys(groupedAssignmentSection).length">
                    <td class="align-middle" colspan="5">
                        <div class="m-3">
                            <Loader />
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="table-responsive border p-3 small-font" style="text-transform:uppercase">
            <table v-if="!preLoading && switcher == 0" class="table table-hover">
                <thead>
                    <tr>
                        <th class="fw-bold p-3 bg-secondary-subtle">Student ID</th>
                        <th class="fw-bold p-3 bg-secondary-subtle">Student Type</th>
                        <th class="fw-bold p-3 bg-secondary-subtle">Last Name</th>
                        <th class="fw-bold p-3 bg-secondary-subtle">First Name</th>
                        <th class="fw-bold p-3 bg-secondary-subtle">Middle Name</th>
                        <th class="fw-bold p-3 bg-secondary-subtle">Suffix Name</th>
                        <th class="fw-bold p-3 bg-secondary-subtle">Preliminary</th>
                        <th class="fw-bold p-3 bg-secondary-subtle">Midterms</th>
                        <th class="fw-bold p-3 bg-secondary-subtle">Pre-finals</th>
                        <th class="fw-bold p-3 bg-secondary-subtle">Finals</th> 
                        <!-- <th class="text-xs bg-light-gray p-3">No.</th> -->
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="(!preLoading && Object.keys(finalStudentList).length) && (!filteringData)"
                        v-for="(app, index) in finalStudentList">
                        <td class="align-middle p-3">
                            {{ app.grad_dtypeid == 2 ? app.ident_identification : app.enr_lrn }}
                        </td>
                        <td class="align-middle p-3">
                            {{ app.grad_dtypeid == 2 ? 'College' : 'SHS' }}
                        </td>
                        <td class="align-middle" p-3>
                            {{ app.per_lastname ? app.per_lastname : '' }}
                        </td>
                        <td class="align-middle" p-3>
                            {{ app.per_firstname ? app.per_firstname : '' }}
                        </td>
                        <td class="align-middle" p-3>
                            {{ app.per_middlename ? app.per_middlename : 'N/A' }}
                        </td>
                        <td class="align-middle" p-3>
                            {{ app.per_suffixname ? app.per_suffixname : 'N/A' }}
                        </td>
                        <td class="align-middle" p-3>
                            <input type="number" :id="'prelims' + app.enr_id" min="60" max="100" required
                                :value="app.grs_prelims ? app.grs_prelims : 60"
                                oninput="this.value = Math.abs(this.value)"
                                class="border border-gray-300 rounded-md px-3 py-1 w-full text-xs" />
                        </td>
                        <td class="align-middle" p-3>
                            <input type="number" :id="'midterms' + app.enr_id" min="60" max="100" required
                                :value="app.grs_midterms ? app.grs_midterms : 60"
                                oninput="this.value = Math.abs(this.value)"
                                class="border border-gray-300 rounded-md px-3 py-1 w-full text-xs" />
                        </td>
                        <td class="align-middle" p-3>
                            <input type="number" :id="'prefinals' + app.enr_id" min="60" max="100" required
                                :value="app.grs_prefinals ? app.grs_prefinals : 60"
                                oninput="this.value = Math.abs(this.value)"
                                class="border border-gray-300 rounded-md px-3 py-1 w-full text-xs" />
                        </td>
                        <td class="align-middle" p-3>
                            <input type="number" :id="'finals' + app.enr_id" min="60" max="100" required
                                :value="app.grs_finals ? app.grs_finals : 60"
                                oninput="this.value = Math.abs(this.value)"
                                class="border border-gray-300 rounded-md px-3 py-1 w-full text-xs" />
                        </td>

                    </tr>
                    <tr v-if="(!preLoading && !Object.keys(finalStudentList).length) && (!filteringData)">
                        <td class="p-3 text-center border border-mid-gray" colspan="9">
                            No Records Found
                        </td>
                    </tr>
                    <tr v-if="(!Object.keys(finalStudentList).length) && (filteringData)">
                        <td class="p-3 text-center border border-mid-gray" colspan="10">
                            Loading Please Wait...
                        </td>
                    </tr>
                    <tr>
                        <td colspan="10">
                            <div class="text-end" v-if="!preLoading">
                                <span>showing total of <span class="font-semibold">({{ Object.keys(finalStudentList).length
                                    }})</span> items</span>
                            </div>
                        </td>
                    </tr>
                </tbody>
                
            </table>
        </div>
        <div v-if="!preLoading && Object.keys(finalStudentList).length" class="w-100 mt-2">
            <div class="d-flex shadow rounded-3 border w-100 justify-content-between p-3 gap-2">
                <div class="w-75 align-content-center text-md-start">
                    <span class="fw-bold text-primary">Note: </span><span class="italic">Once
                        grading sheet is generated, it is linked to all records above.
                        Once deleted, all the grades encoded above will be deleted too.
                    </span>
                </div>
                <div class="w-50 d-flex border justify-content-center align-content-center p-3 gap-2">
                    <button v-if="!Object.keys(gradingHeader).length"
                        :disabled="savingData || filteringData ? true : false" @click="generateSheet(1)" type="button"
                        class="btn btn-sm btn-dark w-100">
                        <i class="mr-2 fa-solid fa-bolt"></i>Generate
                    </button>
                    <button v-else :disabled="savingData || filteringData ? true : false" @click="generateSheet(2)"
                        type="button" class="btn btn-sm btn-dark w-100">
                        <i class="mr-2 fa-solid fa-trash"></i>Delete
                    </button>
                    <button v-if="Object.keys(gradingHeader).length"
                        :disabled="savingData || filteringData ? true : false" @click="saveGrades()" type="button"
                        class="btn btn-sm btn-dark w-100">
                        <i class="mr-2 fa-solid fa-floppy-disk"></i>Save
                    </button>
                    <button :disabled="savingData || filteringData ? true : false" @click="downloadExcel()"
                        type="button" class="btn btn-sm btn-dark w-100"
                        data-bs-toggle="modal" data-bs-target="#downloadgradesmodal">
                        <i class="mr-2 fa-solid fa-file-excel"></i>Download
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- Edit Medical Modal -->
    <div class="modal fade" id="downloadgradesmodal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" >
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Students</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        @click="showExcelDownload = false"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex flex-wrap flex-column">
                        <p class="text-success fw-bold">Generated Grading Sheet</p>
                        <!-- <p class=" fst-italic border p-3 rounded-3 bg-secondary-subtle small-font"><span
                                class="fw-bold">Note:
                            </span><span class="italic">Students handled for this section is listed below.
                                If the student does not included to the list provided by the official masterlist of the registrar,
                                verify the student enrollment to the registrar's office.
                            </span></p> -->
                    </div>
                    <div class="table-responsive border p-3 small-font">
                        <ExcelDownloaderGrades :gradingsheetdata="toBeDownloaded"/>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <div class="form-group">
                        <small id="emailHelp" class="form-text text-muted">We'll never share your personal information
                            with anyone
                            else (Data Privacy Act of 2012)</small>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                            @click="showExcelDownload = false">Close</button>
                        <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
                    </div>
                </div>
            </div>
        </div>
    </div>

</template>