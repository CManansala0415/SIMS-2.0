<script setup>
import { ref, onMounted, computed } from 'vue';
import { getUserID } from "../../../routes/user";
import { getCurriculumSubject, getEnrollment, getMilestone, addMilestone, updateEnrollment, updateMilestone, getCommandUpdateCurriculum, getAcademicStatus, getArchiveMerge } from "../../Fetchers.js";
import Loader from '../loaders/Loader1.vue';

import { useRouter, useRoute } from 'vue-router'

const router = useRouter();
const route = useRoute();
const path = computed(() => route.path)

const props = defineProps({
    student: {
    },
    curriculum: {
    },
    section: {
    },
    subject: {
    }
})


const emit = defineEmits(['close-modal'])
const close = (data) => {
    window.stop()
    emit('close-modal')
}

const studentData = computed(() => {
    return props.student
});
const curriculumData = computed(() => {
    return props.curriculum
});
const sectionData = computed(() => {
    return props.section
});
const subjectData = computed(() => {
    return props.subject
});

const userID = ref('')
const subjectFilter = ref([])
const preloading = ref(true)
const milestoneLoading = ref(true)
const currSubject = ref([])
const enrolleeData = ref([])
const milestone = ref([])
const milestoneSubject = ref([])
const enr_section = ref('')
const enr_curriculum = ref('')
const settingscurr = ref([])
const activeEnrollment = ref(false)
const milestoneCompData = ref([])
const milestoneCompHeader = ref([])
const milestoneCompLoading = ref(true)

onMounted(async () => {
    window.stop()
    enr_section.value = studentData.value.enr_section
    // enr_curriculum.value = studentData.value.enr_curriculum
    subjectFilter.value = subjectData.value
    sectionFilter.value = sectionData.value
    curriculumFilter.value = curriculumData.value

    try {
        
        getArchiveMerge(studentData.value.per_id).then((results) => {
            // milestoneCompData.value = results
            milestoneCompLoading.value = false

            // group nate yung archive para makapag create ng headers to be looped
            milestoneCompHeader.value = results.filter((value, index, self) =>
                index === self.findIndex((t) => t.arc_id === value.arc_id)
            );

            milestoneCompData.value = Object.groupBy(results, item => item.arc_id);
           
            console.log(milestoneCompData.value )
            console.log(milestoneCompHeader.value )
           
        })

        getAcademicStatus(1,'cs_05').then((results) => {
            results[0].sett_status == 1? activeEnrollment.value = true: activeEnrollment.value = false
        })

        getUserID().then((results) => {
            userID.value = results.account.data.id
        })

        getEnrollment(studentData.value.per_id).then((results) => {
            enrolleeData.value = results
            let curr = enrolleeData.value[0].enr_curriculum
            let prog = enrolleeData.value[0].enr_program
            let grad = enrolleeData.value[0].enr_gradelvl
            let cour = enrolleeData.value[0].enr_course

            getCommandUpdateCurriculum(prog, grad, cour).then((results) => {
                settingscurr.value=results
                //if wala pa syang saved curriculum, automatic na base yung default sa settings na curriculum
                if(Object.keys(results).length == 0){
                    curr = 0
                }else{
                    curr = settingscurr.value[0].sett_course_currid
                }
                // if(!enrolleeData.value[0].enr_curriculum){
                //    curr = settingscurr.value[0].sett_course_currid
                // }
                enr_curriculum.value = curr
                getCurriculumSubject(curr, 0, 0).then((results) => {
                    currSubject.value = results

                    // para to dun sa required subjects list ng milestones page
                    milestoneSubject.value = currSubject.value.filter(e => {
                        if (
                            (e.currtag_gradelvl == enrolleeData.value[0].enr_gradelvl) &&
                            (e.currtag_sem == enrolleeData.value[0].enr_quarter)
                        ) {
                            return e
                        }
                    })
                    

                    getMilestone(studentData.value.enr_id).then((results) => {

                        if(!results.length){
                            // ginamit naten yung filtered by grade lvl and sem type para tama mag reflect sa list ng curriculum current subjects
                            milestoneSubject.value.forEach((e) => {
                                addedSubject.value.push(e)
                                addedSubjectId.value.push(e.subj_id)
                            })
                        }else{
                            milestone.value = results
                            milestone.value.forEach((e) => {
                                addedSubject.value.push(e)
                                addedSubjectId.value.push(e.subj_id)
                            })
                            
                        }

                        
                       
                        preloading.value = false
                        milestoneLoading.value = false
                        loadCurrItems.value = false
                    
                    })

                })
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

const loadCurrItems = ref(true)
const monitorCurriculum = (data) => {
    currSubject.value = []
    addedSubject.value = []
    addedSubjectId.value = []
    getCurriculumSubject(data, 0, 0).then(async (results) => {
        currSubject.value = results
        currSubject.value = currSubject.value.filter(e => {
            if (
                (e.currtag_gradelvl == studentData.value.enr_gradelvl) &&
                (e.currtag_sem == studentData.value.enr_quarter)
            ) {
                return e
            }
        })

        for await (const e of currSubject.value) {
            addedSubject.value.push(e)
        };

        loadCurrItems.value = false
    })
}

const searchSubject = ref('')
const filteredSubject = () => {
    subjectFilter.value = subjectData.value.filter(e => {
        if (
            (e.subj_code.toUpperCase().includes(searchSubject.value.toUpperCase())) ||
            (e.subj_name.toUpperCase().includes(searchSubject.value.toUpperCase()))
        ) {
            return e
        }
    })

}

const addedSubject = ref([])
const addedSubjectId = ref([])
const addSubject = (data) => {
    let exist = addedSubject.value.filter(e => {
        if (e.subj_id == data.subj_id) {
            return e
        }
    })

    Object.keys(exist).length ? 
    Swal.fire({
        title: "Duplicate Detected",
        text: "Already included, try selecting another",
        icon: "question"
    }) : addedSubject.value.push(data)
    Object.keys(exist).length ? false : addedSubjectId.value.push(data.subj_id)

}
const removeSubject = (index) => {
    addedSubject.value.splice(index, 1)
    addedSubjectId.value.splice(index, 1)
}

const showMilestone = ref(true)

const setData = (type, data) => {
    switch (type) {
        case 'curriculum':
            monitorCurriculum(data)
            enr_curriculum.value = data
            break;
        case 'section':
            enr_section.value = data
            break;
    }
}

const saving = ref(false)
const saveData = async () => {
    saving.value = true
    addedSubject.value = addedSubject.value.map(itemObj => {
        return {
            ...itemObj,
            enr_id: studentData.value.enr_id,
            user_id: userID.value,
            mi_tag: itemObj.mi_tag ? itemObj.mi_tag : '',
            mi_crossenr: itemObj.mi_crossenr ? itemObj.mi_crossenr : ''
        }
    })

    // let counter = 0
    // addedSubject.value.forEach(async (e) => {
    //     addMilestone(e).then((results) => {
    //         counter+=1
    //         counter==Object.keys(addedSubject.value).length? saving.value = false:saving.value = true
    //     })
    // })

    let counter = 0
    let x = {
        ...studentData.value,
        enr_updatedby: userID.value,
        enr_section: enr_section.value,
        enr_curriculum: enr_curriculum.value
    }

    for await (const e of addedSubject.value) {
        counter += 1
        addMilestone(e).then((results) => { })
    }
    if (counter == Object.keys(addedSubject.value).length) {
        updateEnrollment(x).then((results) => {
            // alert('Tagging Successful')
            // //router.replace({ name: 'Academics', params: { id: 2}});
            // location.reload()
            // saving.value = false
            Swal.fire({
                title: "Tagging Successful",
                text: "Changes applied, refreshing the page",
                icon: "success"
            }).then(()=>{
                location.reload()
            });
        })
    }


}


const deleteSubject = (id, index) => {
    let x = {
        mi_id: id,
        mi_status: 0,
        mi_updatedby: userID.value
    }
    updateMilestone(x).then((results) => {
        // alert('Delete Successful')
        // milestone.value.splice(index, 1)
        // location.reload()

        Swal.fire({
            title: "Delete Successful",
            text: "Changes applied, refreshing the page",
            icon: "success"
        }).then(()=>{
            milestone.value.splice(index, 1)
            location.reload()
        });
    })
}



const searchSection = ref('')
const sectionFilter = ref([])
const filterSection = () => {
    sectionFilter.value = sectionData.value.filter(e => {
        if (
            (e.sec_name.toUpperCase().includes(searchSection.value.toUpperCase())) ||
            (e.sec_code.toUpperCase().includes(searchSection.value.toUpperCase()))
        ) {
            return e
        }
    })

}

const searchCurriculum = ref('')
const curriculumFilter = ref([])
const filterCurriculum = () => {
    curriculumFilter.value = curriculumData.value.filter(e => {
        if (e.curr_code.toUpperCase().includes(searchCurriculum.value.toUpperCase())) {
            return e
        }
    })

}
</script>

<template>
    <div class="d-flex flex-wrap w-100 ">
        <div v-if="!Object.keys(currSubject).length && preloading"
            class="d-flex w-100 mt-4 justify-content-center align-content-center">
            <Loader />
        </div>
        <div v-else>
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="tab1" data-bs-toggle="tab" data-bs-target="#ctab1" type="button"
                        role="tab" aria-controls="ctab1" aria-selected="true">Current Academic Track</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab2" data-bs-toggle="tab" data-bs-target="#ctab2" type="button"
                        role="tab" aria-controls="ctab2" aria-selected="false">Course Tracking</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab3" data-bs-toggle="tab" data-bs-target="#ctab3" type="button"
                        role="tab" aria-controls="ctab3" aria-selected="false">Course Taken</button>
                </li>
            </ul>
            <!-- <div v-if="activeEnrollment">
                <span>Enrollment is already finished, updates are not allowed during this phase.</span>
            </div> -->
            <div class="tab-content" id="myTabContent">
                <div v-if="!activeEnrollment" class="tab-pane fade show active" id="ctab1" role="tabpanel" aria-labelledby="tab1">
                     <div class="container overflow-hidden p-3">
                        <span>Enrollment is already finished, updates are not allowed during this phase.</span>
                     </div>
                </div>
                <div v-else class="tab-pane fade show active" id="ctab1" role="tabpanel" aria-labelledby="tab1">
                    <div class="container overflow-hidden p-3">
                        <div v-for="(e, index) in enrolleeData" class="row gy-2 gx-2">
                            <div class="col-12 border-bottom">
                                <div class="p-3">
                                    <span class="fw-bold border bg-info text-white p-2 rounded-3">SUBJECTS TO BE ENROLLED</span>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="p-3 d-flex flex-column">
                                    <span class="fw-bold">Course</span>{{ e.prog_name }}
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 border shadow d-flex flex-column gap-2 card">
                                    <span class="fw-bold">Curriculum</span>
                                    <select class="form-control form-select-sm w-100" v-model="enr_curriculum"
                                        @change="setData('curriculum', enr_curriculum)">
                                        <option>-- Select Here --</option>
                                        <option v-for="(c, index) in curriculumFilter" :value="c.curr_id">{{ c.curr_code
                                            }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 border shadow d-flex flex-column gap-2 card">
                                    <span class="fw-bold">Section</span>
                                    <select class="form-control form-select-sm w-100" v-model="enr_section"
                                        @change="setData('section', enr_section)">
                                        <option>-- Select Here --</option>
                                        <option v-for="(s, index) in sectionFilter" :value="s.sec_id">{{ s.sec_name }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <!-- <div class="col-4">
                                <div class="p-3 border bg-body-secondary d-flex flex-column">
                                    <span class="fw-bold">Relevant</span>
                                </div>
                            </div> -->
                        </div>
                    </div>
                    <div class="container overflow-hidden p-3">
                        <div v-for="(e, index) in enrolleeData" class="row gy-2 gx-2">
                            <div class="col-12 mb-2">
                                <div class="p-3 border bg-body-secondary">
                                    <span class="fw-bold">Subjects Enrolled</span>
                                </div>
                            </div>
                            <div class="col-5">
                                <div class="d-flex flex-column gap-2 ">
                                    <input v-model="searchSubject" class="form-control form-control-sm" @keyup="filteredSubject"
                                        placeholder="Search Subjects Here..." />
                                    <div class="p-3 card">
                                        <div class="table-responsive border p-2 small-font" style="height: 500px;">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Select Subjects</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-if="!Object.keys(subjectFilter).length">
                                                        <td class="align-middle text-start">
                                                            No Data Found
                                                        </td>
                                                    </tr>
                                                    <tr v-else v-for="(s, index) in subjectFilter"
                                                        @click="addSubject(s)">
                                                        <td
                                                            :class="addedSubjectId.includes(s.subj_id) ? 'align-middle text-start bg-secondary text-white' : 'align-middle text-start bg-white text-black'">
                                                            <p class="fw-bold ">{{ s.subj_code }}</p>
                                                            <p class=" fst-italic">{{ s.subj_name }}</p>
                                                        </td>
                                                    </tr>

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-7">
                                <div class="d-flex flex-column">
                                    <div class="d-flex flex-column card">
                                        <!-- <div class="shadow p-3">
                                            <span class="fw-bold">Subjects and Units Applied</span>
                                        </div> -->
                                        <div class="container overflow-auto p-3" style="height: 578px;">
                                            <div v-if="!Object.keys(addedSubject).length && !loadCurrItems" class="p-1">
                                               <div class="shadow p-3 rounded-3 text-center border small-font fw-bold text-danger">
                                                No Subjects Added
                                               </div>
                                            </div>
                                            <div v-if="!Object.keys(addedSubject).length && loadCurrItems" class="p-1">
                                               <div class="shadow p-3 rounded-3 text-center border small-font fw-bold">
                                                <Loader/>
                                               </div>
                                            </div>
                                            <div v-if="Object.keys(addedSubject).length && !loadCurrItems" v-for="(a, index) in addedSubject" class="row gy-1 gx-1 mb-1">
                                                <div class="col-12">
                                                    <div class="p-3 border text-start">
                                                        <div class="w-100 small-font">
                                                            <div class="d-flex flex-column">
                                                                <div class="border-bottom d-flex flex-column text-start mb-3">
                                                                    <span class="mt-3 fw-bold">{{ a.subj_code }}</span>
                                                                    <span class="mb-3 fst-italic">{{ a.subj_name }}</span>
                                                                </div>
                                                                <div class="input-group input-group-sm mb-1">
                                                                    <span class=" input-group-text">Lecture Units</span>
                                                                    <input v-model="a.subj_lec" type="text" class="form-control form-control-sm" disabled>
                                                                </div>
                                                                <div class="input-group input-group-sm mb-1">
                                                                    <span class=" input-group-text">Laboratory Units</span>
                                                                    <input v-model="a.subj_lab" type="text" class="form-control form-control-sm" disabled>
                                                                </div>
                                                                <div class="input-group input-group-sm mb-3">
                                                                    <span class=" input-group-text">Total Units / Hours</span>
                                                                    <input :value="a.subj_lab + a.subj_lec" type="text" class="form-control form-control-sm" disabled>
                                                                </div>
                                                                <div class="mb-1">
                                                                    <label for="cross" class="form-label">Cross Enrolled (School)</label>
                                                                    <input class="form-control form-control-sm" type="text" v-model="addedSubject[index].mi_crossenr">
                                                                </div>
                                                                <div class="mb-1">
                                                                    <label for="cross" class="form-label">Subject Completion</label>
                                                                    <select class="form-select form-select-sm" v-model="addedSubject[index].mi_tag">
                                                                        <option value="">-- Select Type --</option>
                                                                        <option :value="1">Already Taken</option>
                                                                        <option :value="2">Advance</option>
                                                                        <option :value="3">Re-take / Back Subject</option>
                                                                    </select>
                                                                </div>
                                                                <div class="mt-3 mb-1">
                                                                    <button @click="removeSubject(index)" type="button"
                                                                    class="btn btn-sm btn-danger w-100">Remove this Subject</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="d-flex flex-column gap-2 border p-3">
                                    <p class="fst-italic"><span class="fw-bold text-primary">Note: </span> Ensure that the information encoded here are correct and validations based on the actual data of the student to avoid records mismatch.</p>   
                                    <button @click="saveData()" :disabled="saving || !Object.keys(addedSubject).length? true:false" type="button" class="btn btn-success btn-md">Save Taggings</button> 
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="ctab2" role="tabpanel" aria-labelledby="tab2">
                    <div class="container overflow-hidden p-3">
                        <div v-for="(e, index) in enrolleeData" class="row gy-2 gx-2 ">
                            <div class="col-12 border-bottom">
                                <div class="p-3">
                                    <span class="fw-bold border bg-primary text-white p-2 rounded-3">Subjects Enrolled</span>
                                </div>
                            </div>
                            <div class="col-12 small-font">
                                <div class="p-3 border shadow d-flex flex-column card justify-content-center align-items-center">
                                        <div class="row w-100">
                                            <div class="border p-2 col-12">
                                                <div class="d-flex flex align-items-start">
                                                    <span class="fw-bold">Course: &nbsp;</span>{{ e.prog_name }}
                                                 </div>
                                            </div>
                                            <div class="border p-2 col-4">
                                                <div class="d-flex flex align-items-start">
                                                    <span class="fw-bold">Grade Level: &nbsp;</span> {{e.grad_name }}
                                                 </div>
                                            </div>
                                            <div class="border p-2 col-4">
                                                <div class="d-flex flex align-items-start">
                                                    <span class="fw-bold">Quarter: &nbsp;</span> {{ e.quar_desc}}
                                                 </div>
                                            </div>
                                            <div class="border p-2 col-4">
                                                 <div class="d-flex flex align-items-start">
                                                    <span class="fw-bold">Curriculum Code: &nbsp;</span> {{
                                                        e.curr_code ? e.curr_code : 'N/A' }}
                                                 </div>
                                                
                                            </div>
                                            <div class="border p-2 col-4">
                                                <div class="d-flex flex align-items-start">
                                                    <span class="fw-bold">Section: &nbsp;</span> {{ e.sec_name ?
                                                        e.sec_name : 'N/A' }}
                                                 </div>
                                                
                                            </div>
                                            <div class="border p-2 col-4">
                                                 <div class="d-flex flex align-items-start">
                                                    <span class="fw-bold">Enrolled: &nbsp;</span> {{
                                                        e.enr_dateenrolled }}
                                                 </div>
                                            </div>
                                            <div class="border p-2 col-4">
                                                 <div class="d-flex flex align-items-start">
                                                    <span class="fw-bold">Student ID: &nbsp;</span> {{
                                                        e.ident_identification }}
                                                 </div>
                                            </div>
                                        </div>
                                        <div class="row w-100 mt-3">
                                            <div class="border p-2 col-12">
                                                 <div class="d-flex flex justify-content-center">
                                                    <span class="fw-bold">Subjects Taken</span>
                                                 </div>
                                            </div>
                                        </div>
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th class="border p-2">
                                                        <span class="fw-bold">Code</span>
                                                    </th>
                                                    <th class="border p-2">
                                                        <span class="fw-bold">Subjects Description</span>
                                                    </th>
                                                    <th class="border p-2">
                                                        <span class="fw-bold">Lec</span>
                                                    </th>
                                                    <th class="border p-2">
                                                        <span class="fw-bold">Lab</span>
                                                    </th>
                                                    <th class="border p-2">
                                                        <span class="fw-bold">Total</span>
                                                    </th>
                                                    <th class="border p-2">
                                                        <span class="fw-bold">Prelims</span>
                                                    </th>
                                                    <th class="border p-2">
                                                        <span class="fw-bold">Midterms</span>
                                                    </th>
                                                    <th class="border p-2">
                                                        <span class="fw-bold">Pre-Finals</span>
                                                    </th>
                                                    <th class="border p-2">
                                                        <span class="fw-bold">Finals</span>
                                                    </th>
                                                    <th class="border p-2" v-if="activeEnrollment">
                                                        <span class="fw-bold">Action</span>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-if="!Object.keys(milestone).length && !milestoneLoading" >
                                                    <td class="align-middle" :colspan="activeEnrollment?10:9">
                                                        No Subjects Added
                                                    </td>
                                                </tr>
                                                <tr v-if="!Object.keys(milestone).length && milestoneLoading" >
                                                    <td class="align-middle" :colspan="activeEnrollment?10:9">
                                                        <Loader/>
                                                    </td>
                                                </tr>
                                                <tr v-if="Object.keys(milestone).length && !milestoneLoading" v-for="(c, index) in milestone">
                                                    <td class="align-middle">
                                                        <span class="fw-bold">{{ c.subj_code }}</span>
                                                    </td>
                                                    <td class="align-middle">
                                                        <span class="">{{ c.subj_name }}</span>
                                                        <span v-if="c.mi_crossenr" class="mt-3">Cross Enrolled to: <span
                                                                class=" text-red-500"> {{ c.mi_crossenr }}</span></span>
                                                    </td>
                                                    <td class="align-middle">
                                                        <span>{{ c.subj_lec }}</span>
                                                    </td>
                                                    <td class="align-middle">
                                                        <span>{{ c.subj_lab }}</span>
                                                    </td>
                                                    <td class="align-middle">
                                                        <span class="fw-bold">{{ c.subj_lec + c.subj_lab  }}</span>
                                                    </td>
                                                    <td class="align-middle">
                                                        <span class="text-primary">{{ c.grs_prelims?c.grs_prelims:0 }}</span>
                                                    </td>
                                                     <td class="align-middle">
                                                        <span class="text-primary">{{ c.grs_midterms?c.grs_midterms:0 }}</span>
                                                    </td>
                                                     <td class="align-middle">
                                                        <span class="text-primary">{{ c.grs_prefinals?c.grs_prefinals:0 }}</span>
                                                    </td>
                                                     <td class="align-middle">
                                                        <span class="text-primary">{{ c.grs_finals?c.grs_finals:0 }}</span>
                                                    </td>
                                                    <td v-if="activeEnrollment" class="align-middle">
                                                         <button type="button" @click="deleteSubject(c.mi_id, index)"
                                                            class="mb-2 btn btn-sm btn-danger">&times;</button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="ctab3" role="tabpanel" aria-labelledby="tab3">
                    <div class="container overflow-hidden p-3">
                        <div class="row gy-2 gx-2 ">
                            <div class="col-12 border-bottom">
                                <div class="p-3">
                                    <span class="fw-bold border bg-primary text-white p-2 rounded-3">MILESTONE</span>
                                </div>
                            </div>
                            <div class="col-12 small-font">
                                <div class="p-3 border shadow d-flex flex-column card justify-content-center align-items-center mb-2"  v-for="(mtc, index) in milestoneCompHeader">
                                        <div class="row w-100">
                                            <div class="border p-2 col-12">
                                                <div class="d-flex flex align-items-start">
                                                    <span class="fw-bold">Course: &nbsp;</span>{{ mtc.arc_coursename }}
                                                 </div>
                                            </div>
                                            <div class="border p-2 col-4">
                                                <div class="d-flex flex align-items-start">
                                                    <span class="fw-bold">Grade Level: &nbsp;</span>{{ mtc.arc_yearlevel }}
                                                 </div>
                                            </div>
                                            <div class="border p-2 col-4">
                                                <div class="d-flex flex align-items-start">
                                                    <span class="fw-bold">Quarter: &nbsp;</span>{{ mtc.arc_semester }}
                                                 </div>
                                            </div>
                                            <div class="border p-2 col-4">
                                                 <div class="d-flex flex align-items-start">
                                                    <span class="fw-bold">Curriculum Code: &nbsp;</span> {{
                                                        mtc.arc_curriculum ? mtc.arc_curriculum : 'N/A' }}
                                                 </div>
                                                
                                            </div>
                                            <div class="border p-2 col-4">
                                                <div class="d-flex flex align-items-start">
                                                    <span class="fw-bold">Section: &nbsp;</span> {{ mtc.arc_section ?
                                                        mtc.arc_section : 'N/A' }}
                                                 </div>
                                                
                                            </div>
                                            <div class="border p-2 col-4">
                                                 <div class="d-flex flex align-items-start">
                                                    <span class="fw-bold">Date of Application: &nbsp;</span> {{
                                                        mtc.arc_dateapplied }}
                                                 </div>
                                            </div>
                                            <div class="border p-2 col-4">
                                                 <div class="d-flex flex align-items-start">
                                                    <span class="fw-bold">Student ID: &nbsp;</span> {{
                                                        mtc.arc_studentid }}
                                                 </div>
                                            </div>
                                        </div>
                                        <div class="row w-100 mt-3">
                                            <div class="border p-2 col-12">
                                                 <div class="d-flex flex justify-content-center">
                                                    <span class="fw-bold">Subjects Taken</span>
                                                 </div>
                                            </div>
                                        </div>
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                     <th class="border p-2">
                                                        <span class="fw-bold">Code</span>
                                                    </th>
                                                    <th class="border p-2">
                                                        <span class="fw-bold">Subjects Description</span>
                                                    </th>
                                                    <th class="border p-2">
                                                        <span class="fw-bold">Lec</span>
                                                    </th>
                                                    <th class="border p-2">
                                                        <span class="fw-bold">Lab</span>
                                                    </th>
                                                    <th class="border p-2">
                                                        <span class="fw-bold">Total</span>
                                                    </th>
                                                    <th class="border p-2">
                                                        <span class="fw-bold">Prelims</span>
                                                    </th>
                                                    <th class="border p-2">
                                                        <span class="fw-bold">Midterms</span>
                                                    </th>
                                                    <th class="border p-2">
                                                        <span class="fw-bold">Pre-Finals</span>
                                                    </th>
                                                    <th class="border p-2">
                                                        <span class="fw-bold">Finals</span>
                                                    </th>
                                                    <th class="border p-2">
                                                        <span class="fw-bold">Faculty</span>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-if="!Object.keys(milestoneCompData).length && !milestoneCompLoading">
                                                    <td class="align-middle">
                                                        No Subjects Added
                                                    </td>
                                                </tr>
                                                <tr v-if="!Object.keys(milestoneCompData).length && milestoneCompLoading">
                                                    <td class="align-middle">
                                                        <Loader/>
                                                    </td>
                                                </tr>
                                                <tr v-if="Object.keys(milestoneCompData).length && !milestoneCompLoading" v-for="(c, index) in milestoneCompData[mtc.arc_id]">
                                                    <td class="align-middle">
                                                        <span class="fw-bold">{{ c.arc_subjectcode }}</span>
                                                    </td>
                                                    <td class="align-middle">
                                                        <span class="">{{ c.arc_subjectname }}</span>
                                                        <!-- <span v-if="c.mi_crossenr" class="mt-3">Cross Enrolled to: <span
                                                                class=" text-red-500"> {{ c.mi_crossenr }}</span></span> -->
                                                    </td>
                                                    <td class="align-middle">
                                                        <span>{{ c.arc_lecture }}</span>
                                                    </td>
                                                    <td class="align-middle">
                                                        <span>{{ c.arc_laboratory }}</span>
                                                    </td>
                                                    <td class="align-middle">
                                                        <span class="fw-bold">{{ c.arc_lecture + c.arc_laboratory  }}</span>
                                                    </td>
                                                    <td class="align-middle">
                                                        <span class="text-primary">{{ c.arc_prelimgrade }}</span>
                                                    </td>
                                                    <td class="align-middle">
                                                        <span class="text-primary">{{ c.arc_midtermgrade }}</span>
                                                    </td>
                                                    <td class="align-middle">
                                                        <span class="text-primary">{{ c.arc_prefinalgrade }}</span>
                                                    </td>
                                                    <td class="align-middle">
                                                        <span class="text-primary">{{ c.arc_finalgrade }}</span>
                                                    </td>
                                                    <td class="align-middle">
                                                        <span class="text-primary">{{ c.arc_facultyname }}</span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>

</template>