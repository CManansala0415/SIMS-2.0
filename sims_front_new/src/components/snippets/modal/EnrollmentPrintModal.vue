<script setup>
import { ref, onMounted, computed } from 'vue';
import { getUserID } from "../../../routes/user";
import { getCurriculumSubject, getEnrollment, getMilestone, addMilestone, updateEnrollment, updateMilestone, getCommandUpdateCurriculum } from "../../Fetchers.js";
import Loader from '../loaders/Loader1.vue';
import {
    pdfGenerator
} from "../../Generators.js";
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
const preloading = ref(false)
const milestoneLoading = ref(false)
const currSubject = ref([])
const enrolleeData = ref([])
const milestone = ref([])
const milestoneSubject = ref([])
const enr_section = ref('')
const enr_curriculum = ref('')
const settingscurr = ref([])
const studentID = ref('')
const studentSem = ref('')
const studentDateEnr = ref('')

onMounted(async () => {
    window.stop()
    enr_section.value = studentData.value.enr_section
    // enr_curriculum.value = studentData.value.enr_curriculum
    subjectFilter.value = subjectData.value
    sectionFilter.value = sectionData.value
    curriculumFilter.value = curriculumData.value

    try {
        preloading.value = true
        milestoneLoading.value = true

        getUserID().then((results) => {
            userID.value = results.account.data.id
        })

        getEnrollment(studentData.value.per_id).then((results) => {
            enrolleeData.value = results
            // for printing
            studentID.value = results[0].ident_identification
            studentSem.value = results[0].quar_code
            studentDateEnr.value = results[0].enr_dateenrolled 

            let curr = enrolleeData.value[0].enr_curriculum
            let prog = enrolleeData.value[0].enr_program
            let grad = enrolleeData.value[0].enr_gradelvl
            let cour = enrolleeData.value[0].enr_course

            getCommandUpdateCurriculum(prog, grad, cour).then((results) => {
                settingscurr.value = results
                //if wala pa syang saved curriculum, automatic na base yung default sa settings na curriculum
                if (Object.keys(results).length == 0) {
                    curr = 0
                } else {
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
                    // ginamit naten yung filtered by grade lvl and sem type para tama mag reflect sa list ng curriculum current subjects
                    milestoneSubject.value.forEach((e) => {
                        addedSubject.value.push(e)
                        addedSubjectId.value.push(e.subj_id)
                    })

                    getMilestone(studentData.value.enr_id).then((results) => {
                        milestone.value = results
                        milestone.value.forEach((e) => {
                            addedSubject.value.push(e)
                            addedSubjectId.value.push(e.subj_id)
                        })

                        preloading.value = false
                        milestoneLoading.value = false


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
        }).then(() => {
            // preLoading.value = false
        });
    }
})

const loadCurrItems = ref(false)
const monitorCurriculum = (data) => {
    loadCurrItems.value = true
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
            }).then(() => {
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
        }).then(() => {
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

const downloadPdf = () => {
    // var element = document.getElementById('printform');
    // html2pdf(element);
    let name = studentID.value + '_' + studentSem.value + '_' + studentDateEnr.value.split(" ")[0]
    pdfGenerator(name,'a4','landscape',0.1)
    Swal.fire({
        icon: "success",
        title: "Download Complete",
        text: "Check your file manager, refreshing the page",
    }).then(()=>{
        location.reload()
    });

}
</script>

<template>
    <div class="table-responsive border mb-2 small-font" id="printform">
        <table class="table table-fixed" style="text-transform:uppercase">
            <thead>
                <tr>
                    <th class="align-middle">
                        <img src="/img/clcst_logo.png" height="100px" width="100px" alt="...">
                    </th>
                    <th class="align-middle text-center">
                        <p class="m-0">CENTRAL LUZON COLLEGE OF SCIENCE AND TECHNOLOGY, INC.
                            CELTECH COLLEGE</p>
                        <p class="m-0 fw-normal small-font">B. Mendoza St., Brgy. Sto. Rosario, City of San Fernando,
                            Pampanga, Philippines, 2000</p>
                        <p class="m-0 fw-normal small-font">Tel. Nos: (045) 435-1495</p>
                        <p class="m-0 fw-normal small-font">Founded 1959</p>
                    </th>
                    <th class="align-middle"><img src="/img/clcst_logo.png" height="100px" width="100px"
                            alt="..."></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="align-middle" colspan="3" v-if="!milestoneLoading && Object.keys(milestone).length">
                        <table class="table" v-for="(e, index) in enrolleeData">
                            <tbody>
                            <tr class="text-start">
                                <td class="p-2 border" colspan="2">
                                    <span style="text-transform:none">Name: </span>
                                    <span class="fw-semibold">
                                        {{ studentData.per_firstname }}
                                        {{ studentData.per_middlename ? studentData.per_middlename : ' ' }}
                                        {{ studentData.per_lastname }}
                                        {{ studentData.per_suffixname ? studentData.per_suffixname : ' ' }}
                                    </span>
                                </td>
                                <td class="p-2 border">
                                    <span style="text-transform:none">Student ID: </span>
                                    <span class="fw-semibold">
                                        {{ e.ident_identification }}
                                    </span>
                                </td>
                                <td class="p-2 border">
                                    <span style="text-transform:none">Date: </span>
                                    <span class="fw-semibold">
                                        {{ e.enr_dateenrolled }}
                                    </span>
                                </td>
                            </tr>
                            <tr class="text-start">
                                <td class="p-2 border">
                                    <span style="text-transform:none">Course: </span>
                                    <span class="fw-semibold">
                                        {{ e.prog_name }}
                                    </span>
                                </td>
                                <td class="p-2 border">
                                    <span style="text-transform:none">Grade/Year: </span>
                                    <span class="fw-semibold">
                                        {{ e.grad_name }}
                                    </span>
                                </td>
                                <td class="p-2 border">
                                    <span style="text-transform:none">Section: </span>
                                    <span class="fw-semibold">
                                        {{ e.sec_name ? e.sec_name : 'N/A' }}
                                    </span>
                                </td>
                                <td class="p-2 border">
                                    <span style="text-transform:none">Semester: </span>
                                    <span class="fw-semibold">
                                        {{ e.quar_desc }}
                                    </span>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="text-center w-100">
            <p class="fw-bold">Subjects Enrolled</p>
        </div>
        <table class="table table-fixed" style="text-transform:uppercase">
            <thead>
                <tr>
                    <th style="background-color: #000000;" class="text-white">Code</th>
                    <th style="background-color: #000000;" class="text-white">Subject</th>
                    <th style="background-color: #000000;" class="text-white">Lecture Units</th>
                    <th style="background-color: #000000;" class="text-white">Laboratory Units</th>
                    <th style="background-color: #000000;" class="text-white">Total Units</th>
                </tr>
            </thead>
            <tbody>
                <tr v-if="!milestoneLoading && Object.keys(milestone).length" v-for="(c, index) in milestone">
                    <td class="align-middle p-2">
                        {{ c.subj_code }}
                    </td>
                    <td class="align-middle p-2">
                        {{ c.subj_name }}
                    </td>
                    <td class="align-middle p-2">
                        {{ c.subj_lec }}
                    </td>
                    <td class="align-middle p-2">
                        {{ c.subj_lab }}
                    </td>
                    <td class="align-middle p-2">
                        {{ c.subj_lec + c.subj_lab }}
                    </td>
                </tr>
                <tr v-if="!milestoneLoading && !Object.keys(milestone).length" style="text-transform:none">
                    <td class="p-3 text-center" colspan="9" style="text-transform:none">
                        No Records Found
                    </td>
                </tr>
                <tr v-if="milestoneLoading && !Object.keys(milestone).length" style="text-transform:none">
                    <td class="p-3 text-center" colspan="9" style="text-transform:none">
                        <div class="m-3">
                            <Loader />
                        </div>
                    </td>
                </tr>

            </tbody>
        </table>
    </div>
    <button class="btn btn-success w-100" @click="downloadPdf()"
        v-show="!milestoneLoading && Object.keys(milestone).length">Download Form</button>
</template>