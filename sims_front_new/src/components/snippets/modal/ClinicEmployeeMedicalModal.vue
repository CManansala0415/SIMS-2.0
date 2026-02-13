<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import NeuLoader2 from '../loaders/NeuLoader2.vue';
import { formatDateTime } from '../../Generators.js';
import {
    addEmployeeClinicalRecord,
    getEmployeeClinicalRecords
} from "../../Fetchers.js";
import { getUserID } from "../../../routes/user";

const props = defineProps({
    genderData: {
    },
    civilstatusData: {
    },
    employeeData: {
    },
    formId: {
    },
})

const gender = computed(() => {
    return props.genderData
});
const civilstatus = computed(() => {
    return props.civilstatusData
});

const personID = computed(() => {
    return props.formId
});

const employee = computed(() => {
    return props.employeeData
});

const router = useRouter();

// for select box div absolute
const userID = ref('');
const age = ref(0)
const yearToday = ref(0)
const saving = ref(false);
const checking = ref(false);
const userName = ref('')

const clinicalRecordsList = ref([])
const clinicalRecords = ref({
    clne_personid: personID.value,
    clne_date: '',
    clne_complaint: '',
    clne_intervention: '',
    clne_evaluation: '',
    clne_nurse: userID.value,

    clne_1st_dose: '',
    clne_2nd_dose: '',
    clne_3rd_dose: '',
    clne_fam_illness: '',
    clne_social_history: 0,
    clne_height: '',
    clne_weight: '',
    clne_allergy: 0,
    clne_item: '',
    clne_diet: '',
})

const personal = ref({
    emp_firstname: '',
    emp_middlename: '',
    emp_lastname: '',
    emp_suffixname: '',
    emp_gender: '',
    emp_contact: '',
    emp_email: '',
    emp_birthday: '',
})


const booter = async () => {

}

onMounted(async () => {

    var date = new Date();
    // var day = date.getDate();
    // var month = date.getMonth() + 1;
    // var year = date.getFullYear();
    yearToday.value = date.getFullYear();
    personal.value.emp_birthday = date.toISOString().split('T')[0]

    getUserID().then((results) => {
        userID.value = results.account.data.id
        userName.value = results.account.data.name
        clinicalRecords.value.clne_nurse = userID.value
        clinicalRecords.value.clne_personid = personID.value
    })

    checking.value = true
    if (personID.value) {
        await booter().then((results) => {
            getEmployeeClinicalRecords(1, personID.value).then((results) => {
                clinicalRecordsList.value = results
                checking.value = false
            })
        })
    } else {
        checking.value = false
    }

    personal.value = employee.value
    if (personal.value.emp_birthday) {
        getAge(personal.value.emp_birthday)
    }
})


const getAge = (bday) => {
    var date = new Date(bday);
    var bdayyear = date.getFullYear();
    age.value = yearToday.value - bdayyear
}

const updateMedicalRecords = () => {
    saving.value = true
    Swal.fire({
        title: "Saving Updates",
        text: "Please wait while we check all necessary details.",
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    addEmployeeClinicalRecord(clinicalRecords.value, 1).then((results) => {
        if (results.status == 200) {
            //    alert('Update Successful')
            //    location.reload()
            Swal.fire({
                title: "Update Successful",
                text: "Changes applied, refreshing the page",
                icon: "success"
            }).then(() => {
                Swal.close();
                location.reload()
            });
        } else {
            //    alert('Update Failed')
            //    location.reload()
            Swal.fire({
                title: "Update Failed",
                text: "Unknown error occured, try again later",
                icon: "error"
            }).then(() => {
                Swal.close();
                location.reload()
            });
        }
    })
}

</script>
<template>
    <div v-if="checking">
        <NeuLoader2></NeuLoader2>
    </div>
    <div v-else class="d-flex flex-column gap-2 neu-card-inner p-4 text-dim small-font">
        <div class="neu-card">
            <div class="">
                <div class="d-flex flex-column gap-2 p-4">
                    <div class="text-start">
                        <div class="row p-3">
                            <div class="p-2">
                                <p class="fw-bold m-0">Student Information</p>
                            </div>
                            <div class="col-md-12 col-lg-12 neu-card p-3 mb-3 mt-3">
                                <p class="m-0 text-uppercase">&nbsp;{{ personal.emp_lastname }}, {{
                                    personal.emp_firstname }} {{ personal.emp_middlename }}</p>
                            </div>
                            <div class="col-md-12 col-lg-6 px-3">
                                <p class="fw-bold">Personal Information</p>
                                <p>Gender: <span class="text-uppercase">{{gender.find(gen => gen.gen_id ===
                                        personal.emp_gender)?.gen_desc || '—' }}</span></p>
                                <p>Civil Status: <span class="text-uppercase">{{civilstatus.find(cv => cv.cv_id ===
                                        personal.emp_civilstatus)?.cv_desc || '—' }}</span></p>
                                <p>Cellphone No.: <span class="text-uppercase">{{ personal.emp_contact || '—' }}</span></p>
                                <p>Email: <span class="text-uppercase">{{ personal.emp_email|| '—' }}</span></p>
                            </div>
                            <div class="col-md-12 col-lg-6 px-3">
                                <p class="fw-bold">Birth Information</p>
                                <p>Birthday: <span class="text-uppercase">{{ personal.emp_birthday }}</span></p>
                                <p>Age: <span class="text-uppercase">{{ age }}</span></p>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="neu-card">
            <div class="">
                <div class="d-flex flex-column gap-2 p-4">
                    <div class="text-start">
                        <div class="row p-3">
                            <div class="p-2">
                                <p class="fw-bold m-0">Medical Information</p>
                            </div>
                            <div class="col-md-12 col-lg-12 neu-card p-3 mb-3 mt-3">
                                <p class="m-0 text-uppercase">&nbsp;{{ personal.emp_lastname }}, {{
                                    personal.emp_firstname }} {{ personal.emp_middlename }}</p>
                            </div>
                            <div class="col-md-12 col-lg-6 mb-3">
                                <form @submit.prevent="updateMedicalRecords()" class="h-100">
                                    <p class="fw-bold">Consultation Information</p>
                                    <div class="w-100 d-flex flex-column gap-2 h-100">
                                        <div class="form-group col text-start">
                                            <label>1st Dose Vaccine</label>
                                            <input v-model="clinicalRecords.clne_1st_dose" type="text"
                                                class="neu-input" />
                                        </div>
                                        <div class="form-group col text-start">
                                            <label>2nd Dose Vaccine</label>
                                            <input v-model="clinicalRecords.clne_2nd_dose" type="text"
                                                class="neu-input" />
                                        </div>
                                        <div class="form-group col text-start">
                                            <label>3rd Dose Vaccine</label>
                                            <input v-model="clinicalRecords.clne_3rd_dose" type="text"
                                                class="neu-input" />
                                        </div>
                                        <div class="form-group col text-start">
                                            <label>Family illness History</label>
                                            <input v-model="clinicalRecords.clne_fam_illness" type="text"
                                                class="neu-input" />
                                        </div>
                                        <div class="form-group col text-start">
                                            <label>Social History</label>
                                            <select class="neu-input neu-select"
                                                v-model="clinicalRecords.clne_social_history" required>
                                                <option value="0">N/A</option>
                                                <option value="1">Tobacco</option>
                                                <option value="2">Alcohol</option>
                                                <option value="3">Illicit Drug</option>
                                            </select>
                                        </div>
                                        <div class="form-group col text-start">
                                            <label>Medication</label>
                                            <input v-model="clinicalRecords.clne_medication" type="text"
                                                class="neu-input" />
                                        </div>
                                        <div class="form-group col text-start">
                                            <label>Height (Ft)</label>
                                            <input v-model="clinicalRecords.clne_height" required type="number"
                                                step="0.01" class="neu-input" />
                                        </div>
                                        <div class="form-group col text-start">
                                            <label>Weight (Kg)</label>
                                            <input v-model="clinicalRecords.clne_weight" required type="number"
                                                step="0.01" class="neu-input" />
                                        </div>
                                        <div class="form-group col text-start">
                                            <label>Allergy</label>
                                            <select class="neu-input neu-select" v-model="clinicalRecords.clne_allergy"
                                                required>
                                                <option value="0">N/A</option>
                                                <option value="1">Food</option>
                                                <option value="2">Drug</option>
                                                <option value="3">Chemicals</option>
                                            </select>
                                        </div>
                                        <div class="form-group col text-start">
                                            <label>Specify</label>
                                            <input v-model="clinicalRecords.clne_item" required
                                                :disabled="clinicalRecords.clne_allergy == 0 ? true : false" type="text"
                                                class="neu-input disabled:cursor-not-allowed disabled:opacity-50" />
                                        </div>
                                        <div class="form-group col text-start">
                                            <label>Diet</label>
                                            <input v-model="clinicalRecords.clne_diet" type="text" class="neu-input" />
                                        </div>
                                        <div class="form-group col text-start">
                                            <label>Chief Complaints / Assessment</label>
                                            <textarea class="h-36 neu-input" v-model="clinicalRecords.clne_complaint"
                                                required>
                                                </textarea>
                                        </div>
                                        <div class="form-group col text-start">
                                            <label>Evaluation</label>
                                            <input v-model="clinicalRecords.clne_evaluation" required type="text"
                                                class="neu-input" />
                                        </div>
                                        <div class="form-group col text-start">
                                            <label>Intervention</label>
                                            <input v-model="clinicalRecords.clne_intervention" type="text"
                                                class="neu-input" />
                                        </div>

                                        <button :disabled="saving ? true : false" type="submit"
                                            class="neu-btn neu-green w-100 mt-3 p-2">
                                            <font-awesome-icon icon="fa-solid fa-plus" /> Add Data
                                        </button>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-12 col-lg-6 mb-3" style="height:55rem;">
                                <p class="fw-bold">Medical Examinations Conducted</p>
                                <div class="neu-card-inner w-100 p-3 h-100 overflow-auto ">
                                    <div v-if="!Object.keys(clinicalRecordsList).length" class="p-2 text-center">
                                        <span class="fw-bold text-danger">No Medical Records Found</span>
                                    </div>
                                    <div v-else class="p-3 d-flex flex-column gap-2">
                                        <div v-for="(stud, index) in clinicalRecordsList">
                                            <div class="neu-card">
                                                <ul class="list-group list-group-flush text-dim py-4">
                                                    <li class="list-group-item bg-transparent px-4">
                                                        Medical information as of last checkup: <span class="fw-bold">
                                                            <br />{{
                                                                formatDateTime(stud.clne_date)
                                                            }}</span>
                                                    </li>
                                                    <li class="list-group-item bg-transparent px-4">
                                                        1st Dose Vaccine: <span class="fw-bold">{{
                                                            stud.clne_1st_dose ?
                                                                stud.clne_1st_dose : 'N/A' }}</span>
                                                    </li>
                                                    <li class="list-group-item bg-transparent px-4">
                                                        2nd Dose Vaccine: <span class="fw-bold">{{
                                                            stud.clne_2nd_dose ?
                                                                stud.clne_2nd_dose : 'N/A' }}</span>
                                                    </li>
                                                    <li class="list-group-item bg-transparent px-4">
                                                        3rd Dose Vaccine: <span class="fw-bold">{{
                                                            stud.clne_3rd_dose ?
                                                                stud.clne_3rd_dose : 'N/A' }}</span>
                                                    </li>
                                                    <li class="list-group-item bg-transparent px-4">
                                                        Family Illness History: <span class="fw-bold">{{
                                                            stud.clne_fam_illness ?
                                                                stud.clne_fam_illness : 'No History' }}</span>
                                                    </li>
                                                    <li class="list-group-item bg-transparent px-4">
                                                        Social Illness History:
                                                        <span v-if="stud.clne_social_history == 0" class="fw-bold">No
                                                            History</span>
                                                        <span v-if="stud.clne_social_history == 1"
                                                            class="fw-bold">Tobacco</span>
                                                        <span v-if="stud.clne_social_history == 2"
                                                            class="fw-bold">Alcohol</span>
                                                        <span v-if="stud.clne_social_history == 3"
                                                            class="fw-bold">Illicit
                                                            Drug</span>
                                                    </li>
                                                    <li class="list-group-item bg-transparent px-4">
                                                        Medication: <span class="fw-bold">{{
                                                            stud.clne_medication ? stud.clne_medication : 'No Medication'
                                                            }}</span>
                                                    </li>
                                                    <li class="list-group-item bg-transparent px-4">
                                                        Height: <span class="fw-bold">{{ stud.clne_height
                                                        }}ft</span>
                                                    </li>
                                                    <li class="list-group-item bg-transparent px-4">
                                                        Weight: <span class="fw-bold">{{ stud.clne_weight
                                                        }}Kg</span>
                                                    </li>
                                                    <li class="list-group-item bg-transparent px-4">
                                                        Has Allergy:
                                                        <span v-if="stud.clne_allergy == 0" class="fw-bold">No
                                                            Allergy</span>
                                                        <span v-if="stud.clne_allergy == 1" class="fw-bold">Food</span>
                                                        <span v-if="stud.clne_allergy == 2" class="fw-bold">Drug</span>
                                                        <span v-if="stud.clne_allergy == 3"
                                                            class="fw-bold">Chemical</span>
                                                    </li>
                                                    <li class="list-group-item bg-transparent px-4">
                                                        Specification: <span v-if="stud.clne_allergy != 0"
                                                            class="fw-bold">{{
                                                                stud.clne_item }}</span>
                                                        <span v-else class="fw-bold">No Allergy</span>
                                                    </li>
                                                    <li class="list-group-item bg-transparent px-4">
                                                        Diet: <span v-if="stud.clne_diet" class="fw-bold">{{
                                                            stud.clne_diet }}</span>
                                                        <span v-else class="fw-bold">No Diet</span>
                                                    </li>
                                                    <li class="list-group-item bg-transparent px-4">
                                                        Complaint: <span class="fw-bold">{{ stud.clne_complaint
                                                        }}</span>
                                                    </li>
                                                    <li class="list-group-item bg-transparent px-4">
                                                        Evaluation: <span class="fw-bold">{{
                                                            stud.clne_evaluation
                                                            }}</span>
                                                    </li>
                                                    <li class="list-group-item bg-transparent px-4">
                                                        Intervention: <span class="fw-bold">{{
                                                            stud.clne_intervention ?
                                                                stud.clne_intervention : 'no interventions needed'
                                                        }}</span>
                                                    </li>
                                                    <li class="list-group-item bg-transparent px-4">
                                                        <button :disabled="saving ? true : false" type="button"
                                                            class="neu-btn neu-purple p-2">
                                                            <font-awesome-icon icon="fa-solid fa-print" /> Print Form
                                                        </button>
                                                    </li>
                                                </ul>
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
    </div>
</template>