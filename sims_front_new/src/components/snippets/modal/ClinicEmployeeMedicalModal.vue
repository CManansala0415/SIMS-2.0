<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import Loader from '../loaders/Loading1.vue';
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
    clne_personid:personID.value,
    clne_date:'',
    clne_complaint:'',
    clne_intervention:'',
    clne_evaluation:'',
    clne_nurse: userID.value,

    clne_1st_dose:'',
    clne_2nd_dose:'',
    clne_3rd_dose:'',
    clne_fam_illness:'',
    clne_social_history:0,
    clne_height:'',
    clne_weight:'',
    clne_allergy:0,
    clne_item:'',
    clne_diet:'',
})

const personal = ref({
    emp_firstname:'',
    emp_middlename:'',
    emp_lastname:'',
    emp_suffixname:'',
    emp_gender:'',
    emp_contact:'',
    emp_email:'',
    emp_birthday: '',
})


const booter = async() =>{
   
}

onMounted(async () => {

    var date = new Date();
    // var day = date.getDate();
    // var month = date.getMonth() + 1;
    // var year = date.getFullYear();
    yearToday.value = date.getFullYear();
    personal.value.per_birthday = date.toISOString().split('T')[0]

    getUserID().then((results) => {
        userID.value = results.account.data.id
        userName.value = results.account.data.name
        clinicalRecords.value.clne_nurse = userID.value
        clinicalRecords.value.clne_personid = personID.value
    })
    
    checking.value = true
    if(personID.value){
        await booter().then((results)=>{
            getEmployeeClinicalRecords(1, personID.value).then((results)=>{
                clinicalRecordsList.value = results
                checking.value = false
            })
        })
    }else{
        checking.value = false
    }
    
    personal.value = employee.value
    if(personal.value.emp_birthday){
        getAge(personal.value.emp_birthday)
    }
})


const getAge = (bday) =>{
    var date = new Date(bday);
    var bdayyear = date.getFullYear();
    age.value = yearToday.value - bdayyear
}

const updateMedicalRecords = () =>{
    saving.value = true
    addEmployeeClinicalRecord(clinicalRecords.value, 1).then((results)=>{
        if(results.status == 200){
        //    alert('Update Successful')
        //    location.reload()
            Swal.fire({
                title: "Update Successful",
                text: "Changes applied, refreshing the page",
                icon: "success"
            }).then(()=>{
                location.reload()
            });
        }else{
        //    alert('Update Failed')
        //    location.reload()
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

</script>
<template>
    <div v-if="checking">
        <Loader/>
    </div>
    <div v-else class="d-flex flex-column gap-2 small-font">
        <div class="accordion" id="medicalaccordion">
            <div class="accordion-item">
                <h2 class="accordion-header" id="personalheading">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#personalinformation" aria-expanded="false" aria-controls="personalinformation">
                        Personal Information
                    </button>
                </h2>
                <div id="personalinformation" class="accordion-collapse collapse" aria-labelledby="personalheading"
                    data-bs-parent="#medicalaccordion">
                    <div class="accordion-body">
                        <div class="form-group border p-4 rounded  d-flex flex-column gap-2">
                            <div class="form-group border-bottom">
                                <p class="fw-bold text-white bg-dark p-1 rounded-3">Personal Information</p>
                            </div>
                            <div class="form-group d-flex flex-column gap-2">
                                <div class="row mb-2">
                                    <div class="form-group col">
                                        <label for="firstname">First Name</label>
                                        <input type="text" class="form-control" id="firstname"
                                            aria-describedby="firstname" v-model="personal.emp_firstname" disabled>
                                    </div>
                                    <div class="form-group col">
                                        <label for="middlename">Middle Name</label>
                                        <input type="text" class="form-control" id="middlename"
                                            aria-describedby="middlename" v-model="personal.emp_middlename" disabled>
                                    </div>
                                    <div class="form-group col">
                                        <label for="lastname">Last Name</label>
                                        <input type="text" class="form-control" id="lastname"
                                            aria-describedby="lastname" v-model="personal.emp_lastname" disabled>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="form-group col">
                                        <label for="suffixname">Suffix Name</label>
                                        <input type="text" class="form-control" id="suffixname"
                                            aria-describedby="suffixname" maxlength="3"
                                            v-model="personal.emp_suffixname" disabled>
                                    </div>
                                    <div class="form-group col">
                                        <label for="gender">Gender</label>
                                        <select class="form-control" id="gender" v-model="personal.emp_gender" required
                                            disabled>
                                            <option v-for="(gen, index) in gender" :value="gen.gen_id">{{ gen.gen_desc
                                                }}</option>
                                        </select>
                                    </div>
                                    <div class="form-group col">
                                        <label for="civilstatus">Civil Status</label>
                                        <select class="form-control" id="civilstatus" v-model="personal.emp_civilstatus"
                                            required disabled>
                                            <option v-for="(cs, index) in civilstatus" :value="cs.cv_id">
                                                {{ cs.cv_desc }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group border p-4 rounded  d-flex flex-column gap-2">
                            <div class="form-group border-bottom">
                                <p class="fw-bold text-white bg-dark p-1 rounded-3">Contact Information</p>
                            </div>
                            <div class="form-group d-flex flex-column gap-2">
                                <div class="row mb-2">
                                    <!-- <div class="form-group col">
                        <label for="exampleInputEmail1">Telephone No.</label>
                        <input class="form-control" type="email" name="email" id="email" placeholder="Email">
                    </div> -->
                                    <div class="form-group col">
                                        <label for="cellphone">Cellphone No.</label>
                                        <input class="form-control" type="text" name="cellphone" id="cellphone"
                                            placeholder="Cellphone No." v-model="personal.emp_contact"
                                            onkeydown="return /[0-9]/i.test(event.key)" minlength="9" maxlength="9"
                                            required disabled>
                                    </div>
                                    <div class="form-group col">
                                        <label for="peremail">Email</label>
                                        <input class="form-control" type="peremail" name="peremail" id="peremail"
                                            placeholder="Email" v-model="personal.emp_email" required disabled>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group border p-4 rounded  d-flex flex-column gap-2">
                            <div class="form-group border-bottom">
                                <p class="fw-bold text-white bg-dark p-1 rounded-3">Birth Information</p>
                            </div>
                            <div class="form-group d-flex flex-column gap-2">
                                <div class="row mb-2">
                                    <div class="form-group col">
                                        <label for="bday">Birthday</label>
                                        <input type="date" class="form-control" id="bday" aria-describedby="emailHelp"
                                            v-model="personal.emp_birthday" @focusout="getAge(personal.emp_birthday)"
                                            required disabled>
                                    </div>
                                    <div class="form-group col">
                                        <label for="age">Age</label>
                                        <input type="number" class="form-control" id="age" aria-describedby="emailHelp"
                                            v-model="age" disabled>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header" id="medicalheading">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                        data-bs-target="#medicalinformation" aria-expanded="true" aria-controls="medicalinformation">
                        Medical Information
                    </button>
                </h2>
                <div id="medicalinformation" class="accordion-collapse collapse show" aria-labelledby="medicalheading"
                    data-bs-parent="#medicalaccordion">
                    <div class="accordion-body">
                        <form @submit.prevent="updateMedicalRecords()">
                            <div class="container p-3 border">
                                <div class="bg-secondary-subtle align-middle p-3 mb-3">
                                    <span class="fw-bold">Consultation Information</span>
                                </div>
                                <div class="d-flex gap-2">
                                    <div class="p-2 card shadow w-100 d-flex flex-column gap-2">
                                        <div class="form-group col text-start">
                                            <label>1st Dose Vaccine</label>
                                            <input v-model="clinicalRecords.clne_1st_dose" type="text"
                                                class="form-control form-control-sm" />
                                        </div>
                                        <div class="form-group col text-start">
                                            <label>2nd Dose Vaccine</label>
                                            <input v-model="clinicalRecords.clne_2nd_dose" type="text"
                                                class="form-control form-control-sm" />
                                        </div>
                                        <div class="form-group col text-start">
                                            <label>3rd Dose Vaccine</label>
                                            <input v-model="clinicalRecords.clne_3rd_dose" type="text"
                                                class="form-control form-control-sm" />
                                        </div>
                                        <div class="form-group col text-start">
                                            <label>Family illness History</label>
                                            <input v-model="clinicalRecords.clne_fam_illness" type="text"
                                                class="form-control form-control-sm" />
                                        </div>
                                        <div class="form-group col text-start">
                                            <label>Social History</label>
                                            <select class="form-control form-select-sm"
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
                                                class="form-control form-control-sm" />
                                        </div>
                                        <div class="form-group col text-start">
                                            <label>Height (Ft)</label>
                                            <input v-model="clinicalRecords.clne_height" required type="number"
                                                step="0.01" class="form-control form-control-sm" />
                                        </div>
                                        <div class="form-group col text-start">
                                            <label>Weight (Kg)</label>
                                            <input v-model="clinicalRecords.clne_weight" required type="number"
                                                step="0.01" class="form-control form-control-sm" />
                                        </div>
                                        <div class="form-group col text-start">
                                            <label>Allergy</label>
                                            <select class="form-control form-select-sm"
                                                v-model="clinicalRecords.clne_allergy" required>
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
                                                class="form-control form-control-sm disabled:cursor-not-allowed disabled:opacity-50" />
                                        </div>
                                        <div class="form-group col text-start">
                                            <label>Diet</label>
                                            <input v-model="clinicalRecords.clne_diet" type="text"
                                                class="form-control form-control-sm" />
                                        </div>
                                        <div class="form-group col text-start">
                                            <label>Chief Complaints / Assessment</label>
                                            <textarea class="h-36 form-control form-control-sm"
                                                v-model="clinicalRecords.clne_complaint" required>
                                                </textarea>
                                        </div>
                                        <div class="form-group col text-start">
                                            <label>Evaluation</label>
                                            <input v-model="clinicalRecords.clne_evaluation" required type="text"
                                                class="form-control form-control-sm" />
                                        </div>
                                        <div class="form-group col text-start">
                                            <label>Intervention</label>
                                            <input v-model="clinicalRecords.clne_intervention" type="text"
                                                class="form-control form-control-sm" />
                                        </div>
                                        <div class="form-group col mt-2">
                                            <button :disabled="saving ? true : false" type="submit"
                                                class="btn btn-sm btn-dark w-100">
                                                Add Data
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card shadow w-100">
                                        <div class="align-content-center bg-dark-subtle p-2 fw-bold">
                                            Medical Examinations Conducted
                                        </div>
                                        <div v-if="!Object.keys(clinicalRecordsList).length"
                                            class="border p-2 align-content-center">
                                            <span class="fw-bold text-danger">No Medical Records Found</span>
                                        </div>
                                        <div v-else class="overflow-auto p-3 d-flex flex-column gap-2"
                                            style="height:55rem;">
                                            <div v-for="(stud, index) in clinicalRecordsList">

                                                <div class="card">
                                                    <ul class="list-group list-group-flush">
                                                        <li class="list-group-item bg-secondary-subtle">
                                                            Medical information as of last checkup: <span
                                                                class="fw-bold">{{
                                                                    stud.clne_date
                                                                }}</span>
                                                        </li>
                                                        <li class="list-group-item">
                                                            1st Dose Vaccine: <span class="fw-bold">{{
                                                                stud.clne_1st_dose ?
                                                                    stud.clne_1st_dose : 'N/A' }}</span>
                                                        </li>
                                                        <li class="list-group-item">
                                                            2nd Dose Vaccine: <span class="fw-bold">{{
                                                                stud.clne_2nd_dose ?
                                                                    stud.clne_2nd_dose : 'N/A' }}</span>
                                                        </li>
                                                        <li class="list-group-item">
                                                            3rd Dose Vaccine: <span class="fw-bold">{{
                                                                stud.clne_3rd_dose ?
                                                                    stud.clne_3rd_dose : 'N/A' }}</span>
                                                        </li>
                                                        <li class="list-group-item">
                                                            Family Illness History: <span class="fw-bold">{{
                                                                stud.clne_fam_illness ?
                                                                    stud.clne_fam_illness : 'No History' }}</span>
                                                        </li>
                                                        <li class="list-group-item">
                                                            Social Illness History:
                                                            <span v-if="stud.clne_social_history == 0"
                                                                class="fw-bold">No
                                                                History</span>
                                                            <span v-if="stud.clne_social_history == 1"
                                                                class="fw-bold">Tobacco</span>
                                                            <span v-if="stud.clne_social_history == 2"
                                                                class="fw-bold">Alcohol</span>
                                                            <span v-if="stud.clne_social_history == 3"
                                                                class="fw-bold">Illicit
                                                                Drug</span>
                                                        </li>
                                                        <li class="list-group-item">
                                                            Medication: <span class="fw-bold">{{
                                                                stud.clne_medication
                                                            }}</span>
                                                        </li>
                                                        <li class="list-group-item">
                                                            Height: <span class="fw-bold">{{ stud.clne_height
                                                                }}ft</span>
                                                        </li>
                                                        <li class="list-group-item">
                                                            Weight: <span class="fw-bold">{{ stud.clne_weight
                                                                }}Kg</span>
                                                        </li>
                                                        <li class="list-group-item">
                                                            Has Allergy:
                                                            <span v-if="stud.clne_allergy == 0" class="fw-bold">No
                                                                Allergy</span>
                                                            <span v-if="stud.clne_allergy == 1"
                                                                class="fw-bold">Food</span>
                                                            <span v-if="stud.clne_allergy == 2"
                                                                class="fw-bold">Drug</span>
                                                            <span v-if="stud.clne_allergy == 3"
                                                                class="fw-bold">Chemical</span>
                                                        </li>
                                                        <li class="list-group-item">
                                                            Specification: <span v-if="stud.clne_allergy != 0"
                                                                class="fw-bold">{{
                                                                    stud.clne_item }}</span>
                                                            <span v-else class="fw-bold">No Allergy</span>
                                                        </li>
                                                        <li class="list-group-item">
                                                            Diet: <span v-if="stud.clne_diet" class="fw-bold">{{
                                                                stud.clne_diet }}</span>
                                                            <span v-else class="fw-bold">No Diet</span>
                                                        </li>
                                                        <li class="list-group-item">
                                                            Complaint: <span class="fw-bold">{{ stud.clne_complaint
                                                                }}</span>
                                                        </li>
                                                        <li class="list-group-item">
                                                            Evaluation: <span class="fw-bold">{{
                                                                stud.clne_evaluation
                                                            }}</span>
                                                        </li>
                                                        <li class="list-group-item">
                                                            Intervention: <span class="fw-bold">{{
                                                                stud.clne_intervention ?
                                                                    stud.clne_intervention : 'no interventions needed'
                                                            }}</span>
                                                        </li>
                                                        <li class="list-group-item">
                                                            <button :disabled="saving ? true : false" type="button"
                                                                class="btn btn-sm btn-dark w-100">Print Form
                                                            </button>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</template>