<script setup>
import { ref, onMounted, computed } from 'vue';
import Loader from '../loaders/Loading1.vue';
import {  
        addClinicHearing,
        getMedicalHearing,
        getMedicalHeader
       } from "../../Fetchers.js";

const props = defineProps({
    medicicalItemsData: {
    },
    userIdData: {
    },
    personIdData:{
    }
})
const medicalItems = computed(() => {
  return props.medicicalItemsData
});
const userId = computed(() => {
  return props.userIdData
});
const personId = computed(() => {
  return props.personIdData
});

const saving = ref(false)
onMounted(()=>{
    preLoading.value = true
    getMedicalHeader(personId.value, 2).then((results)=>{
        viewAllData.value = results
        preLoading.value = false
    })
})

const formType = ref(0)

const items = ref([
    {
        cleh_remarks:'',
        cleh_assessment:1,
        cleh_id: 1,
        cleh_ear: 'Left Ear',
        cleh_orientation: 'Front',
        cleh_user:userId.value,
        cleh_personid:personId.value,
    },
    {
        cleh_remarks:'',
        cleh_assessment:1,
        cleh_id: 1,
        cleh_ear: 'Left Ear',
        cleh_orientation: 'Back',
        cleh_user:userId.value,
        cleh_personid:personId.value,
    },
    {
        cleh_remarks:'',
        cleh_assessment:1,
        cleh_id: 1,
        cleh_ear: 'Right Ear',
        cleh_orientation: 'Front',
        cleh_user:userId.value,
        cleh_personid:personId.value,
    },
    {
        cleh_remarks:'',
        cleh_assessment:1,
        cleh_id: 1,
        cleh_ear: 'Right Ear',
        cleh_orientation: 'Back',
        cleh_user:userId.value,
        cleh_personid:personId.value,
    }
])



const activeFailedClass = ref('btn btn-sm btn-danger w-100');
const activePassedClass = ref('btn btn-sm btn-success w-100');
const defaultFailedClass = ref('btn btn-sm btn-secondary w-100');
const defaultPassedClass = ref('btn btn-sm btn-secondary w-100');

const unknownClass = ref();
const viewAll = ref(true)
const preLoading = ref(false)
const viewAllData = ref([])
const viewSubData = ref([])
const subLoading = ref(false)
const showResult = ref(false)


const save = () =>{
    saving.value = true;
    addClinicHearing(items.value, 2, userId.value, personId.value).then((results)=>{
        if(results.status == 200){
           alert('Examination Successful')
           location.reload()
        }else{
           alert('Examination Failed')
           location.reload()
        }
    })
}

const setAssessment = (index, assess, pass, fail) =>{
    if(pass == 1 && fail == 0){
        items.value.forEach((e) => {
            e.cleh_assessment = assess
        });
    }else if(pass == 0 && fail == 1){
        items.value.forEach((e) => {
            e.cleh_assessment = assess
        });
    }else{
        let x = items.value[index].cleh_assessment = assess
    }
}

const viewAssessment = () =>{
    viewAll.value = !viewAll.value
}

const viewResult = (headerid) =>{
    showResult.value = !showResult.value
    subLoading.value = true

    getMedicalHearing(personId.value, headerid).then((results)=>{
        viewSubData.value=[]
        subLoading.value = false

        viewSubData.value = results.map((e, index)=>{
            return{
                ...e,
                cleh_ear: items.value[index].cleh_ear,
                cleh_orientation: items.value[index].cleh_orientation,
            }
        })

    })
}
</script>

<template>
<div class="d-flex flex-column p-2 gap-2">
        <div class="d-flex flex-wrap flex-column">
            <p class="text-success fw-bold">Hearing Test</p>
            <p class="fw-bold small-font fst-italic">Ear Hearing Capability Examination</p>
            <p class="fst-italic border p-2 rounded-3 bg-secondary-subtle small-font"><span class="fw-bold">Note:
                </span><span class="italic">Encode the following details and make sure they are correct to avoid
                    misinformation.
                </span></p>
        </div>

        <div v-if="viewAll" class="d-flex flex-column gap-2 small-font justify-content-center">
            <div class="card shadow w-100">
                <div v-if="!preLoading && viewAll" class="border p-2 d-flex justify-content-end">
                    <button :disabled="saving ? true : false" type="button" @click="viewAssessment()"
                        class="btn btn-sm btn-dark">
                        Conduct Assessment
                    </button>
                </div>
                <div v-if="!preLoading && !Object.keys(viewAllData).length" class="border p-2 align-content-center p-3">
                    <span class="fw-bold text-danger">No Items Found</span>
                </div>
                <div v-if="preLoading && !Object.keys(viewAllData).length" class="border p-2 align-content-center">
                    <Loader />
                </div>
                <div v-if="!preLoading && Object.keys(viewAllData).length">
                    <div class="w-100 p-2">
                        <div v-if="!showResult">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th style="background-color: #237a5b;" class="text-white">Date</th>
                                        <th style="background-color: #237a5b;" class="text-white">Identification</th>
                                        <th style="background-color: #237a5b;" class="text-white">Personnel</th>
                                        <th style="background-color: #237a5b;" class="text-white">Commands</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(vad, index) in viewAllData">
                                        <td class="align-middle">
                                            {{ vad.clhd_dateadded }}
                                        </td>
                                        <td class="align-middle">
                                            {{ vad.clhd_header }}
                                        </td>
                                        <td class="align-middle">
                                            {{vad.emp_firstname}} {{vad.emp_lastname}}
                                        </td>
                                        <td class="align-middle">
                                            <div class="d-flex gap-2 justify-content-center">
                                                <button :disabled="saving ? true : false" type="button"
                                                    @click="viewResult(vad.clhd_header)"
                                                    class="btn btn-secondary btn-sm">
                                                    <i class="mr-2 fa-solid fa-eye"></i>View Results
                                                </button>
                                            </div>
                                        </td>

                                    </tr>
                                    <tr v-if="!preLoading && !Object.keys(viewAllData).length">
                                        <td class="p-3 text-center" colspan="4">
                                            No Records Found
                                        </td>
                                    </tr>
                                    <tr v-if="preLoading && !Object.keys(viewAllData).length">
                                        <td class="p-3 text-center" colspan="4">
                                            <div class="m-3">
                                                <Loader />
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                           
                        </div>
                        <div v-else>
                            <div class="w-100 d-flex justify-content-end mb-2">
                                <button :disabled="saving ? true : false" type="button" @click="viewResult(0)"
                                    class="btn btn-sm btn-primary">
                                    Back to List
                                </button>
                            </div>
                            <div class="w-100">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th style="background-color: #237a5b;" class="text-white  w-25">Ear</th>
                                            <th style="background-color: #237a5b;" class="text-white  w-25">Position</th>
                                            <th style="background-color: #237a5b;" class="text-white  w-25">Assessment / Remarks</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(itm, index) in viewSubData">
                                            <td class="align-middle">
                                                {{ itm.cleh_ear }}
                                            </td>
                                            <td class="align-middle">
                                                {{ itm.cleh_orientation }}
                                            </td>
                                            <td class="align-middle">
                                                <div class="d-flex gap-1 justify-content-evenly">
                                                    <button type="button" @click="setAssessment(index, 2, 0, 0)"
                                                        :class="itm.cleh_assessment == 2 ? activePassedClass : defaultPassedClass"
                                                        disabled>Passed</button>
                                                    <button type="button" @click="setAssessment(index, 1, 0, 0)"
                                                        :class="itm.cleh_assessment == 1 ? activeFailedClass : defaultFailedClass"
                                                        disabled>Failed</button>
                                                </div>
                                                <div class="mt-2">
                                                    <label>Remarks</label>
                                                    <textarea v-model="itm.cleh_remarks" disabled
                                                        class="form-control form-control-sm">
                                                    </textarea>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr v-if="!subLoading && !Object.keys(viewSubData).length">
                                            <td class="p-3 text-center" colspan="7">
                                                No Records Found
                                            </td>
                                        </tr>
                                        <tr v-if="subLoading && !Object.keys(viewSubData).length">
                                            <td class="p-3 text-center" colspan="7">
                                                <div class="m-3">
                                                    <Loader />
                                                </div>
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

        <div v-else class="d-flex flex-column gap-2 small-font justify-content-center">
            <div class="w-100 d-flex justify-content-end">
                <button :disabled="saving ? true : false" type="button" @click="viewAll = true"
                    class="btn btn-sm btn-primary">
                    Back to List
                </button>
            </div>
            <form @submit.prevent="save" class="w-100 card">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th style="background-color: #237a5b;" class="text-white  w-25">Ear</th>
                            <th style="background-color: #237a5b;" class="text-white  w-25">Position</th>
                            <th style="background-color: #237a5b;" class="text-white  w-25">Assessment/ Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(itm, index) in items">
                            <td class="align-middle">
                                {{ itm.cleh_ear }}
                            </td>
                            <td class="align-middle">
                                {{ itm.cleh_orientation }}
                            </td>
                            <td class="align-middle">
                                <div class="d-flex gap-1 justify-content-evenly">
                                    <button type="button" @click="setAssessment(index, 2, 0, 0)"
                                        :class="itm.cleh_assessment == 2 ? activePassedClass : defaultPassedClass"
                                        :disabled="saving ? true : false">Passed</button>
                                    <button type="button" @click="setAssessment(index, 1, 0, 0)"
                                        :class="itm.cleh_assessment == 1 ? activeFailedClass : defaultFailedClass"
                                        :disabled="saving ? true : false">Failed</button>
                                </div>
                                <div class="mt-2">
                                    <label>Remarks</label>
                                    <textarea v-model="itm.cleh_remarks" class="form-control form-control-sm">
                                </textarea>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="card bg-secondary-subtle p-2">
                    <div class="d-flex gap-2 justify-content-end p-2">
                        <button :disabled="saving ? true : false" type="button" @click="viewAssessment()"
                            class="btn btn-sm btn-dark w-100">
                            View All
                        </button>
                        <button :disabled="saving ? true : false" type="button" @click="setAssessment(index, 2, 1, 0)"
                            class="btn btn-sm btn-dark w-100">
                            Pass All
                        </button>
                        <button :disabled="saving ? true : false" type="button" @click="setAssessment(index, 1, 0, 1)"
                            class="btn btn-sm btn-dark w-100">
                            Fail All
                        </button>
                        <button :disabled="saving ? true : false" type="submit" class="btn btn-sm btn-dark w-100">
                            Save All
                        </button>
                    </div>
                </div>
            </form>
        </div>

    </div>


</template>