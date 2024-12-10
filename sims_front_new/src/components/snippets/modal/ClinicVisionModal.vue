<script setup>
import { ref, onMounted, computed } from 'vue';
import Loader from '../loaders/Loading1.vue';
import {
    addClinicIshihara,
    getMedicalIshihara,
    getMedicalHeader,
} from "../../Fetchers.js";

const props = defineProps({
    medicicalItemsData: {
    },
    userIdData: {
    },
    personIdData: {
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
onMounted(() => {
    preLoading.value = true
    getMedicalHeader(personId.value, 1).then((results) => {
        viewAllData.value = results
        preLoading.value = false
    })
})

const formType = ref(0)

const items = ref([
    {
        cler_remarks: '',
        cler_assessment: 1,
        cler_id: 1,
        no_plate: 'Plate 1',
        normal: 'Visible to all',
        def_red_green: 'OK',
        cler_user: userId.value,
        cler_personid: personId.value,
    },
    {
        cler_remarks: '',
        cler_assessment: 1,
        cler_id: 2,
        no_plate: 'Plate 2',
        normal: 'View as 8',
        def_red_green: 'View as 3',
        cler_user: userId.value,
        cler_personid: personId.value,
    },
    {
        cler_remarks: '',
        cler_assessment: 1,
        cler_id: 3,
        no_plate: 'Plate 3',
        normal: 'View as 6',
        def_red_green: 'View as 5',
        cler_user: userId.value,
        cler_personid: personId.value,
    },
    {
        cler_remarks: '',
        cler_assessment: 1,
        cler_id: 4,
        no_plate: 'Plate 4',
        normal: 'View as 29',
        def_red_green: 'View as 70',
        cler_user: userId.value,
        cler_personid: personId.value,
    },
    {
        cler_remarks: '',
        cler_assessment: 1,
        cler_id: 5,
        no_plate: 'Plate 5',
        normal: 'View as 57',
        def_red_green: 'View as 35',
        cler_user: userId.value,
        cler_personid: personId.value,
    },
    {
        cler_remarks: '',
        cler_assessment: 1,
        cler_id: 6,
        no_plate: 'Plate 6',
        normal: 'View as 5',
        def_red_green: 'View as 2',
        cler_user: userId.value,
        cler_personid: personId.value,
    },
    {
        cler_remarks: '',
        cler_assessment: 1,
        cler_id: 7,
        no_plate: 'Plate 7',
        normal: 'View as 3',
        def_red_green: 'View as 5',
        cler_user: userId.value,
        cler_personid: personId.value,
    },
    {
        cler_remarks: '',
        cler_assessment: 1,
        cler_id: 8,
        no_plate: 'Plate 8',
        normal: 'View as 15',
        def_red_green: 'View as 17',
        cler_user: userId.value,
        cler_personid: personId.value,
    },
    {
        cler_remarks: '',
        cler_assessment: 1,
        cler_id: 9
        , no_plate: 'Plate 9',
        normal: 'View as 74',
        def_red_green: 'View as 21',
        cler_user: userId.value,
        cler_personid: personId.value,
    },
    {
        cler_remarks: '',
        cler_assessment: 1,
        cler_id: 10,
        no_plate: 'Plate 10',
        normal: 'View as 2',
        def_red_green: 'See Nothing/Wrong',
        cler_user: userId.value,
        cler_personid: personId.value,
    },
    {
        cler_remarks: '',
        cler_assessment: 1,
        cler_id: 11,
        no_plate: 'Plate 11',
        normal: 'View as 6',
        def_red_green: 'See Nothing/Wrong',
        cler_user: userId.value,
        cler_personid: personId.value,
    },
    {
        cler_remarks: '',
        cler_assessment: 1,
        cler_id: 12,
        no_plate: 'Plate 12',
        normal: 'View as 97',
        def_red_green: 'See Nothing/Wrong',
        cler_user: userId.value,
        cler_personid: personId.value,
    },
    {
        cler_remarks: '',
        cler_assessment: 1,
        cler_id: 13,
        no_plate: 'Plate 13',
        normal: 'View as 41',
        def_red_green: 'See Nothing/Wrong',
        cler_user: userId.value,
        cler_personid: personId.value,
    },
    {
        cler_remarks: '',
        cler_assessment: 1,
        cler_id: 14,
        no_plate: 'Plate 14',
        normal: 'View as 5',
        def_red_green: 'See Nothing/Wrong',
        cler_user: userId.value,
        cler_personid: personId.value,
    },
    {
        cler_remarks: '',
        cler_assessment: 1,
        cler_id: 15,
        no_plate: 'Plate 15',
        normal: 'View as 7',
        def_red_green: 'See Nothing/Wrong',
        cler_user: userId.value,
        cler_personid: personId.value,
    },
    {
        cler_remarks: '',
        cler_assessment: 1,
        cler_id: 16,
        no_plate: 'Plate 16',
        normal: 'View as 16',
        def_red_green: 'See Nothing/Wrong',
        cler_user: userId.value,
        cler_personid: personId.value,
    },
    {
        cler_remarks: '',
        cler_assessment: 1,
        cler_id: 17,
        no_plate: 'Plate 17',
        normal: 'View as 73',
        def_red_green: 'See Nothing/Wrong',
        cler_user: userId.value,
        cler_personid: personId.value,
    },
    {
        cler_remarks: '',
        cler_assessment: 1,
        cler_id: 18,
        no_plate: 'Plate 18',
        normal: 'View Nothing',
        def_red_green: 'View as 5',
        cler_user: userId.value,
        cler_personid: personId.value,
    },
    {
        cler_remarks: '',
        cler_assessment: 1,
        cler_id: 19,
        no_plate: 'Plate 19',
        normal: 'View Nothing',
        def_red_green: 'View as 2',
        cler_user: userId.value,
        cler_personid: personId.value,
    },
    {
        cler_remarks: '',
        cler_assessment: 1,
        cler_id: 20,
        no_plate: 'Plate 20',
        normal: 'View Nothing',
        def_red_green: 'View as 45',
        cler_user: userId.value,
        cler_personid: personId.value,
    },
    {
        cler_remarks: '',
        cler_assessment: 1,
        cler_id: 21,
        no_plate: 'Plate 21',
        normal: 'View Nothing',
        def_red_green: 'View as 73',
        cler_user: userId.value,
        cler_personid: personId.value,
    },
    {
        cler_remarks: '',
        cler_assessment: 1,
        cler_id: 22,
        no_plate: 'Plate 22',
        normal: 'View as 26',
        def_red_green: '(Protanopia or Protanomaly) 6 (Deuteranopia or Deuteranomaly) 2',
        cler_user: userId.value,
        cler_personid: personId.value,
    },
    {
        cler_remarks: '',
        cler_assessment: 1,
        cler_id: 23,
        no_plate: 'Plate 23',
        normal: 'View as 42',
        def_red_green: '(Protanopia or Protanomaly) 2 (Deuteranopia or Deuteranomaly) 4',
        cler_user: userId.value,
        cler_personid: personId.value,
    },
    {
        cler_remarks: '',
        cler_assessment: 1,
        cler_id: 24,
        no_plate: 'Plate 24',
        normal: 'View as 35',
        def_red_green: '(Protanopia or Protanomaly) 5 (Deuteranopia or Deuteranomaly) 3',
        cler_user: userId.value,
        cler_personid: personId.value,
    },
    {
        cler_remarks: '',
        cler_assessment: 1,
        cler_id: 25,
        no_plate: 'Plate 25',
        normal: 'View as 96',
        def_red_green: '(Protanopia or Protanomaly) 6 (Deuteranopia or Deuteranomaly) 9',
        cler_user: userId.value,
        cler_personid: personId.value,
    },
    // {
    //     cler_remarks:'',
    //     cler_assessment:1,
    //     cler_id: 26,
    //     no_plate: 'Plate 26',
    //     normal: 'View as Purple and Spots',
    //     def_red_green: '(Protanopia or Protanomaly) Purple (Deuteranopia or Deuteranomaly) Red Line',
    //     cler_user:userId.value,
    //     cler_personid:personId.value,
    // },
    // {
    //     cler_remarks:'',
    //     cler_assessment:1,
    //     cler_id: 27,
    //     no_plate: 'Plate 27',
    //     normal: 'View as Purple and Spots',
    //     def_red_green: '(Protanopia or Protanomaly) Purple Line (Deuteranopia or Deuteranomaly) Red Line',
    //     cler_user:userId.value,
    //     cler_personid:personId.value,
    // },
    // {
    //     cler_remarks:'',
    //     cler_assessment:1,
    //     cler_id: 28,
    //     no_plate: 'Plate 28',
    //     normal: 'View Nothing',
    //     def_red_green: 'View Only Line',
    //     cler_user:userId.value,
    //     cler_personid:personId.value,
    // },
    // {
    //     cler_remarks:'',
    //     cler_assessment:1,
    //     cler_id: 29,
    //     no_plate: 'Plate 29',
    //     normal: 'View Nothing',
    //     def_red_green: 'View Only Line',
    //     cler_user:userId.value,
    //     cler_personid:personId.value,
    // },
    // {
    //     cler_remarks:'',
    //     cler_assessment:1,
    //     cler_id: 30,
    //     no_plate: 'Plate 30',
    //     normal: 'View as Blue Green Line',
    //     def_red_green: 'View Nothing',
    //     cler_user:userId.value,
    //     cler_personid:personId.value,
    // },
    // {
    //     cler_remarks:'',
    //     cler_assessment:1,
    //     cler_id: 31,
    //     no_plate: 'Plate 31',
    //     normal: 'View as Blue Green Line',
    //     def_red_green: 'View Nothing',
    //     cler_user:userId.value,
    //     cler_personid:personId.value,
    // },
    // {
    //     cler_remarks:'',
    //     cler_assessment:1,
    //     cler_id: 32,
    //     no_plate: 'Plate 32',
    //     normal: 'View as Orange Line',
    //     def_red_green: 'Nothing or False Line',
    //     cler_user:userId.value,
    //     cler_personid:personId.value,
    // },
    // {
    //     cler_remarks:'',
    //     cler_assessment:1,
    //     cler_id: 33,
    //     no_plate: 'Plate 33',
    //     normal: 'View as Orange Line',
    //     def_red_green: 'Nothing or False Line',
    //     cler_user:userId.value,
    //     cler_personid:personId.value,
    // },
    // {
    //     cler_remarks:'',
    //     cler_assessment:1,
    //     cler_id: 34,
    //     no_plate: 'Plate 34',
    //     normal: 'Blue Green / Yellow Green',
    //     def_red_green: 'View as Red Green and Violet Line',
    //     cler_user:userId.value,
    //     cler_personid:personId.value,
    // },
    // {
    //     cler_remarks:'',
    //     cler_assessment:1,
    //     cler_id: 35,
    //     no_plate: 'Plate 35',
    //     normal: 'Green / Yellow Green Line',
    //     def_red_green: 'Only Green and Violet Line',
    //     cler_user:userId.value,
    //     cler_personid:personId.value,
    // },
    // {
    //     cler_remarks:'',
    //     cler_assessment:1,
    //     cler_id: 36,
    //     no_plate: 'Plate 36',
    //     normal: 'Violet / Orange Line',
    //     def_red_green: 'Green and Violet Line',
    //     cler_user:userId.value,
    //     cler_personid:personId.value,
    // },
    // {
    //     cler_remarks:'',
    //     cler_assessment:1,
    //     cler_id: 37,
    //     no_plate: 'Plate 37',
    //     normal: 'Violet / Orange Line',
    //     def_red_green: 'Blue Green and Violet Line',
    //     cler_user:userId.value,
    //     cler_personid:personId.value,
    // },
    // {
    //     cler_remarks:'',
    //     cler_assessment:1,
    //     cler_id: 38,
    //     no_plate: 'Plate 38',
    //     normal: 'Everyone should see the same line',
    //     def_red_green: 'Ok',
    // }
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

const save = () => {
    saving.value = true;
    addClinicIshihara(items.value, 1, userId.value, personId.value).then((results) => {
        if (results.status == 200) {
            alert('Examination Successful')
            location.reload()
        } else {
            alert('Examination Failed')
            location.reload()
        }
    })
}

const setAssessment = (index, assess, pass, fail) => {
    if (pass == 1 && fail == 0) {
        items.value.forEach((e) => {
            e.cler_assessment = assess
        });
    } else if (pass == 0 && fail == 1) {
        items.value.forEach((e) => {
            e.cler_assessment = assess
        });
    } else {
        let x = items.value[index].cler_assessment = assess
    }
}

const viewAssessment = () => {
    viewAll.value = !viewAll.value
}

const viewResult = (headerid) => {
    showResult.value = !showResult.value
    subLoading.value = true
    getMedicalIshihara(personId.value, headerid).then((results) => {
        viewSubData.value = []
        subLoading.value = false

        viewSubData.value = results.map((e, index) => {
            return {
                ...e,
                no_plate: items.value[index].no_plate,
                normal: items.value[index].normal,
                def_red_green: items.value[index].def_red_green,
            }
        })

    })
}

</script>

<template>
    <div class="d-flex flex-column p-2 gap-2">
        <div class="d-flex flex-wrap flex-column">
            <p class="text-success fw-bold">Color Vision Test</p>
            <p class="fw-bold small-font fst-italic">Ishihara Vision Test</p>
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
                                            {{ vad.emp_firstname }} {{ vad.emp_lastname }}
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
                                            <th style="background-color: #237a5b;" class="text-white  w-25">No. of
                                                Plates</th>
                                            <th style="background-color: #237a5b;" class="text-white  w-25">Normal</th>
                                            <th style="background-color: #237a5b;" class="text-white  w-25">Defective
                                                (Red & Green)</th>
                                            <th style="background-color: #237a5b;" class="text-white  w-25">Assessment
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(itm, index) in viewSubData">
                                            <td class="align-middle">
                                                {{ itm.no_plate }}
                                            </td>
                                            <td class="align-middle">
                                                {{ itm.normal }}
                                            </td>
                                            <td class="align-middle">
                                                {{ itm.def_red_green }}
                                            </td>
                                            <td class="align-middle">
                                                <div class="d-flex gap-1 justify-content-evenly">
                                                    <button type="button" @click="setAssessment(index, 2, 0, 0)"
                                                        :class="itm.cler_assessment == 2 ? activePassedClass : defaultPassedClass"
                                                        disabled>Passed</button>
                                                    <button type="button" @click="setAssessment(index, 1, 0, 0)"
                                                        :class="itm.cler_assessment == 1 ? activeFailedClass : defaultFailedClass"
                                                        disabled>Failed</button>
                                                </div>
                                                <div class="mt-2">
                                                    <label>Remarks</label>
                                                    <textarea v-model="itm.cler_remarks" disabled
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
                            <th style="background-color: #237a5b;" class="text-white  w-25">No. of Plates</th>
                            <th style="background-color: #237a5b;" class="text-white  w-25">Normal</th>
                            <th style="background-color: #237a5b;" class="text-white  w-25">Defective (Red & Green)</th>
                            <th style="background-color: #237a5b;" class="text-white  w-25">Assessment</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(itm, index) in items">
                            <td class="align-middle">
                                {{ itm.no_plate }}
                            </td>
                            <td class="align-middle">
                                {{ itm.normal }}
                            </td>
                            <td class="align-middle">
                                {{ itm.def_red_green }}
                            </td>
                            <td class="align-middle">
                                <div class="d-flex gap-1 justify-content-evenly">
                                    <button type="button" @click="setAssessment(index, 2, 0, 0)"
                                        :class="itm.cler_assessment == 2 ? activePassedClass : defaultPassedClass"
                                        :disabled="saving ? true : false">Passed</button>
                                    <button type="button" @click="setAssessment(index, 1, 0, 0)"
                                        :class="itm.cler_assessment == 1 ? activeFailedClass : defaultFailedClass"
                                        :disabled="saving ? true : false">Failed</button>
                                </div>
                                <div class="mt-2">
                                    <label>Remarks</label>
                                    <textarea v-model="itm.cler_remarks" class="form-control form-control-sm">
                                </textarea>
                                </div>
                            </td>
                            <td class="align-middle">

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