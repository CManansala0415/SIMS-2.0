<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { getUserID } from "../../../routes/user.js";
import NeuLoader2 from '../loaders/NeuLoader2.vue';
import {
    getPerson,
    addStudentClinicalRecord,
    getStudentClinicalRecords
} from "../../Fetchers.js";
import { formatDateTime } from '../../Generators.js';

const props = defineProps({
    genderData: {
    },
    civilstatusData: {
    },
    nationalityData: {
    },
    regionData: {
    },
    provinceData: {
    },
    cityData: {
    },
    barangayData: {
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
const nationality = computed(() => {
    return props.nationalityData
});
const region = computed(() => {
    return props.regionData
});
const province = computed(() => {
    return props.provinceData
});
const city = computed(() => {
    return props.cityData
});
const barangay = computed(() => {
    return props.barangayData
});
const personID = computed(() => {
    return props.formId
});

const router = useRouter();

// for select box div absolute
const userID = ref('');
const sameAddress = ref(false);
const studentClinicalRecords = ref([]);
const age = ref(0)
const yearToday = ref(0)
const saving = ref(false);
const checking = ref(false);
const userName = ref('')
const filteredBirthProvince = ref([])
const filteredBirthCity = ref([])

const filteredCurrRegion = ref([])
const filteredCurrProvince = ref([])
const filteredCurrCity = ref([])
const filteredCurrBarangay = ref([])
const filteredCurrZipcode = ref([])

const filteredPermRegion = ref([])
const filteredPermProvince = ref([])
const filteredPermCity = ref([])
const filteredPermBarangay = ref([])
const filteredPermZipcode = ref([])
const clinicalRecordsList = ref([])
const clinicalRecords = ref({
    clnc_personid: personID.value,
    clnc_date: '',
    clnc_complaint: '',
    clnc_intervention: '',
    clnc_evaluation: '',
    clnc_nurse: userID.value,

    clnc_1st_dose: '',
    clnc_2nd_dose: '',
    clnc_3rd_dose: '',
    clnc_fam_illness: '',
    clnc_social_history: 0,
    clnc_height: '',
    clnc_weight: '',
    clnc_allergy: 0,
    clnc_item: '',
    clnc_diet: '',
})


const personal = ref({
    per_firstname: '',
    per_middlename: '',
    per_lastname: '',
    per_suffixname: '',
    per_gender: '',
    per_contact: '',
    per_email: '',
    per_birthday: '',
    per_birth_province: '',
    per_birth_city: '',
    per_birth_zipcode: '',
    per_civilstatus: '',
    per_nationality: '',

    per_curr_home: '',
    per_curr_region: '',
    per_curr_province: '',
    per_curr_city: '',
    per_curr_barangay: '',
    per_curr_zipcode: '',
    per_perm_home: '',
    per_perm_region: '',
    per_perm_province: '',
    per_perm_city: '',
    per_perm_barangay: '',
    per_perm_zipcode: '',
})


const booter = async () => {
    await getPerson(personID.value).then(async (results) => {
        if (results) {
            updateAddress('birth-city', results.per_birth_province)
            updateAddress('current-province', results.per_curr_region)
            updateAddress('current-city', results.per_curr_province)
            updateAddress('current-barangay', results.per_curr_city)
            updateAddress('permanent-province', results.per_perm_region)
            updateAddress('permanent-city', results.per_perm_province)
            updateAddress('permanent-barangay', results.per_perm_city)
            personal.value = results


        }
    })
}

onMounted(async () => {
    var date = new Date();
    // var day = date.getDate();
    // var month = date.getMonth() + 1;
    // var year = date.getFullYear();
    yearToday.value = date.getFullYear();
    personal.value.per_birthday = date.toISOString().split('T')[0]

    filteredBirthProvince.value = province.value
    filteredCurrRegion.value = region.value
    filteredPermRegion.value = region.value

    getUserID().then((results) => {
        userID.value = results.account.data.id
        userName.value = results.account.data.name
        clinicalRecords.value.clnc_nurse = userID.value
        clinicalRecords.value.clnc_personid = personID.value
    })

    checking.value = true
    if (personID.value) {
        await booter().then((results) => {
            getStudentClinicalRecords(1, personID.value).then((results) => {
                clinicalRecordsList.value = results
                checking.value = false
            })
        })
    } else {
        checking.value = false
    }

    getAge(personal.value.per_birthday)

})

const updateAddress = (type, code) => {
    switch (type) {
        case 'birth-city':
            filteredBirthCity.value = city.value
            filteredBirthCity.value = city.value.filter(e => {
                if (e.provCode == code) {
                    return e
                }
            })
            break;

        case 'current-province':
            personal.value.per_curr_city = ''
            personal.value.per_curr_barangay = ''
            filteredCurrCity.value = []
            filteredCurrBarangay.value = []
            filteredCurrProvince.value = province.value
            filteredCurrProvince.value = province.value.filter(e => {
                if (e.regCode == code) {
                    return e
                }
            })
            break;

        case 'current-city':
            personal.value.per_curr_barangay = ''
            filteredCurrCity.value = city.value
            filteredCurrCity.value = city.value.filter(e => {
                if (e.provCode == code) {
                    return e
                }
            })
            break;

        case 'current-barangay':
            filteredCurrBarangay.value = barangay.value
            filteredCurrBarangay.value = barangay.value.filter(e => {
                if (e.citymunCode == code) {
                    return e
                }
            })
            break;

        case 'permanent-province':
            personal.value.per_perm_city = ''
            personal.value.per_perm_barangay = ''
            filteredPermCity.value = []
            filteredPermBarangay.value = []
            filteredPermProvince.value = province.value
            filteredPermProvince.value = province.value.filter(e => {
                if (e.regCode == code) {
                    return e
                }
            })
            break;

        case 'permanent-city':
            personal.value.per_perm_barangay = ''
            filteredPermCity.value = city.value
            filteredPermCity.value = city.value.filter(e => {
                if (e.provCode == code) {
                    return e
                }
            })
            break;

        case 'permanent-barangay':
            filteredPermBarangay.value = barangay.value
            filteredPermBarangay.value = barangay.value.filter(e => {
                if (e.citymunCode == code) {
                    return e
                }
            })
            break;


    }
}

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


    addStudentClinicalRecord(clinicalRecords.value, 1).then((results) => {
        if (results.status == 200) {
            // alert('Update Successful')
            // location.reload()
            Swal.fire({
                title: "Update Successful",
                text: "Changes applied, refreshing the page",
                icon: "success"
            }).then(()=>{
                Swal.close();
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
                                <p class="m-0 text-uppercase">&nbsp;{{ personal.per_lastname }}, {{ personal.per_firstname }} {{ personal.per_middlename }}</p>
                            </div>
                            <div class="col-md-12 col-lg-6 px-3">
                                <p class="fw-bold">Personal Information</p>
                                <p>Gender: <span class="text-uppercase">{{ gender.find(gen => gen.gen_id === personal.per_gender)?.gen_desc || '—' }}</span></p>
                                <p>Civil Status: <span class="text-uppercase">{{ civilstatus.find(cv => cv.cv_id === personal.per_civilstatus)?.cv_desc || '—' }}</span></p>
                                <p>Nationality: <span class="text-uppercase">{{ nationality.find(nat => nat.nat_id === personal.per_nationality)?.nat_desc || '—' }}</span></p>
                                <p>Cellphone No.: <span class="text-uppercase">{{ personal.per_contact }}</span></p>
                                <p>Email: <span class="text-uppercase">{{ personal.per_email }}</span></p>
                            </div>
                            <div class="col-md-12 col-lg-6 px-3">
                                <p class="fw-bold">Birth Information</p>
                                <p>Birthday: <span class="text-uppercase">{{ personal.per_birthday }}</span></p>
                                <p>Age: <span class="text-uppercase">{{ age }}</span></p>
                                <p>Birth Province: <span class="text-uppercase">{{ filteredBirthProvince.find(prov => prov.provCode === personal.per_birth_province)?.provDesc || '—' }}</span></p>
                                <p>Birth City: <span class="text-uppercase">{{ filteredBirthCity.find(city => city.citymunCode === personal.per_birth_city)?.citymunDesc || '—' }}</span></p>
                            </div>
                            <div class="col-md-12 col-lg-6 px-3">
                                <p class="fw-bold">Present Address</p>
                                <p>Home: <span class="text-uppercase">{{ personal.per_home }}</span></p>
                                <p>Region: <span class="text-uppercase">{{ filteredCurrRegion.find(reg => reg.regCode === personal.per_curr_region)?.regDesc || '—' }}</span></p>
                                <p>Province: <span class="text-uppercase">{{ filteredCurrProvince.find(prov => prov.provCode === personal.per_curr_province)?.provDesc || '—' }}</span></p>
                                <p>City: <span class="text-uppercase">{{ filteredCurrCity.find(city => city.citymunCode === personal.per_curr_city)?.citymunDesc || '—' }}</span></p>
                                <p>Barangay: <span class="text-uppercase">{{ filteredCurrBarangay.find(bar => bar.brgyCode === personal.per_curr_barangay)?.brgyDesc || '—' }}</span></p>
                                <p>Zipcode: <span class="text-uppercase">{{ personal.per_curr_zipcode }}</span></p>
                            </div>
                            <div class="col-md-12 col-lg-6 px-3">
                                <p class="fw-bold">Present Address</p>
                                <p>Home: <span class="text-uppercase">{{ personal.per_home }}</span></p>
                                <p>Region: <span class="text-uppercase">{{ filteredPermRegion.find(reg => reg.regCode === personal.per_perm_region)?.regDesc || '—' }}</span></p>
                                <p>Province: <span class="text-uppercase">{{ filteredPermProvince.find(prov => prov.provCode === personal.per_perm_province)?.provDesc || '—' }}</span></p>
                                <p>City: <span class="text-uppercase">{{ filteredPermCity.find(city => city.citymunCode === personal.per_perm_city)?.citymunDesc || '—' }}</span></p>
                                <p>Barangay: <span class="text-uppercase">{{ filteredPermBarangay.find(bar => bar.brgyCode === personal.per_perm_barangay)?.brgyDesc || '—' }}</span></p>
                                <p>Zipcode: <span class="text-uppercase">{{ personal.per_perm_zipcode }}</span></p>
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
                                <p class="m-0 text-uppercase">&nbsp;{{ personal.per_lastname }}, {{ personal.per_firstname }} {{ personal.per_middlename }}</p>
                            </div>
                            <div class="col-md-12 col-lg-6 mb-3">
                                <form @submit.prevent="updateMedicalRecords()" class="h-100">
                                    <p class="fw-bold">Consultation Information</p>
                                    <div class="w-100 d-flex flex-column gap-2 h-100">
                                        <div class="form-group col text-start">
                                            <label>1st Dose Vaccine</label>
                                            <input v-model="clinicalRecords.clnc_1st_dose" type="text"
                                                class="neu-input" />
                                        </div>
                                        <div class="form-group col text-start">
                                            <label>2nd Dose Vaccine</label>
                                            <input v-model="clinicalRecords.clnc_2nd_dose" type="text"
                                                class="neu-input" />
                                        </div>
                                        <div class="form-group col text-start">
                                            <label>3rd Dose Vaccine</label>
                                            <input v-model="clinicalRecords.clnc_3rd_dose" type="text"
                                                class="neu-input" />
                                        </div>
                                        <div class="form-group col text-start">
                                            <label>Family illness History</label>
                                            <input v-model="clinicalRecords.clnc_fam_illness" type="text"
                                                class="neu-input" />
                                        </div>
                                        <div class="form-group col text-start">
                                            <label>Social History</label>
                                            <select class="neu-input neu-select"
                                                v-model="clinicalRecords.clnc_social_history" required>
                                                <option value="0">N/A</option>
                                                <option value="1">Tobacco</option>
                                                <option value="2">Alcohol</option>
                                                <option value="3">Illicit Drug</option>
                                            </select>
                                        </div>
                                        <div class="form-group col text-start">
                                            <label>Medication</label>
                                            <input v-model="clinicalRecords.clnc_medication" type="text"
                                                class="neu-input" />
                                        </div>
                                        <div class="form-group col text-start">
                                            <label>Height (Ft)</label>
                                            <input v-model="clinicalRecords.clnc_height" required type="number"
                                                step="0.01" class="neu-input" />
                                        </div>
                                        <div class="form-group col text-start">
                                            <label>Weight (Kg)</label>
                                            <input v-model="clinicalRecords.clnc_weight" required type="number"
                                                step="0.01" class="neu-input" />
                                        </div>
                                        <div class="form-group col text-start">
                                            <label>Allergy</label>
                                            <select class="neu-input neu-select"
                                                v-model="clinicalRecords.clnc_allergy" required>
                                                <option value="0">N/A</option>
                                                <option value="1">Food</option>
                                                <option value="2">Drug</option>
                                                <option value="3">Chemicals</option>
                                            </select>
                                        </div>
                                        <div class="form-group col text-start">
                                            <label>Specify</label>
                                            <input v-model="clinicalRecords.clnc_item" required
                                                :disabled="clinicalRecords.clnc_allergy == 0 ? true : false" type="text"
                                                class="neu-input disabled:cursor-not-allowed disabled:opacity-50" />
                                        </div>
                                        <div class="form-group col text-start">
                                            <label>Diet</label>
                                            <input v-model="clinicalRecords.clnc_diet" type="text"
                                                class="neu-input" />
                                        </div>
                                        <div class="form-group col text-start">
                                            <label>Chief Complaints / Assessment</label>
                                            <textarea class="h-36 neu-input"
                                                v-model="clinicalRecords.clnc_complaint" required>
                                                </textarea>
                                        </div>
                                        <div class="form-group col text-start">
                                            <label>Evaluation</label>
                                            <input v-model="clinicalRecords.clnc_evaluation" required type="text"
                                                class="neu-input" />
                                        </div>
                                        <div class="form-group col text-start">
                                            <label>Intervention</label>
                                            <input v-model="clinicalRecords.clnc_intervention" type="text"
                                                class="neu-input" />
                                        </div>
                                        
                                        <button :disabled="saving ? true : false" type="submit"
                                            class="neu-btn neu-green w-100 mt-3 p-2">
                                            <font-awesome-icon icon="fa-solid fa-plus"/> Add Data
                                        </button>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-12 col-lg-6 mb-3" style="height:70rem;">
                                <p class="fw-bold">Medical Examinations Conducted</p>
                                <div class="neu-card-inner w-100 p-3 h-100 overflow-auto ">
                                    <div v-if="!Object.keys(clinicalRecordsList).length"
                                        class="p-2 text-center">
                                        <span class="fw-bold text-danger">No Medical Records Found</span>
                                    </div>
                                    <div v-else class="p-3 d-flex flex-column gap-2"> 
                                        <div v-for="(stud, index) in clinicalRecordsList">
                                            <div class="neu-card">
                                                <ul class="list-group list-group-flush text-dim py-4">
                                                    <li class="list-group-item bg-transparent px-4">
                                                        Medical information as of last checkup: <span
                                                            class="fw-bold"> <br/>{{
                                                                formatDateTime(stud.clnc_date)
                                                            }}</span>
                                                    </li>
                                                    <li class="list-group-item bg-transparent px-4">
                                                        1st Dose Vaccine: <span class="fw-bold">{{
                                                            stud.clnc_1st_dose ?
                                                                stud.clnc_1st_dose : 'N/A' }}</span>
                                                    </li>
                                                    <li class="list-group-item bg-transparent px-4">
                                                        2nd Dose Vaccine: <span class="fw-bold">{{
                                                            stud.clnc_2nd_dose ?
                                                                stud.clnc_2nd_dose : 'N/A' }}</span>
                                                    </li>
                                                    <li class="list-group-item bg-transparent px-4">
                                                        3rd Dose Vaccine: <span class="fw-bold">{{
                                                            stud.clnc_3rd_dose ?
                                                                stud.clnc_3rd_dose : 'N/A' }}</span>
                                                    </li>
                                                    <li class="list-group-item bg-transparent px-4">
                                                        Family Illness History: <span class="fw-bold">{{
                                                            stud.clnc_fam_illness ?
                                                                stud.clnc_fam_illness : 'No History' }}</span>
                                                    </li>
                                                    <li class="list-group-item bg-transparent px-4">
                                                        Social Illness History:
                                                        <span v-if="stud.clnc_social_history == 0"
                                                            class="fw-bold">No
                                                            History</span>
                                                        <span v-if="stud.clnc_social_history == 1"
                                                            class="fw-bold">Tobacco</span>
                                                        <span v-if="stud.clnc_social_history == 2"
                                                            class="fw-bold">Alcohol</span>
                                                        <span v-if="stud.clnc_social_history == 3"
                                                            class="fw-bold">Illicit
                                                            Drug</span>
                                                    </li>
                                                    <li class="list-group-item bg-transparent px-4">
                                                        Medication: <span class="fw-bold">{{
                                                            stud.clnc_medication? stud.clnc_medication:'No Medication'
                                                        }}</span>
                                                    </li>
                                                    <li class="list-group-item bg-transparent px-4">
                                                        Height: <span class="fw-bold">{{ stud.clnc_height
                                                            }}ft</span>
                                                    </li>
                                                    <li class="list-group-item bg-transparent px-4">
                                                        Weight: <span class="fw-bold">{{ stud.clnc_weight
                                                            }}Kg</span>
                                                    </li>
                                                    <li class="list-group-item bg-transparent px-4">
                                                        Has Allergy:
                                                        <span v-if="stud.clnc_allergy == 0" class="fw-bold">No
                                                            Allergy</span>
                                                        <span v-if="stud.clnc_allergy == 1"
                                                            class="fw-bold">Food</span>
                                                        <span v-if="stud.clnc_allergy == 2"
                                                            class="fw-bold">Drug</span>
                                                        <span v-if="stud.clnc_allergy == 3"
                                                            class="fw-bold">Chemical</span>
                                                    </li>
                                                    <li class="list-group-item bg-transparent px-4">
                                                        Specification: <span v-if="stud.clnc_allergy != 0"
                                                            class="fw-bold">{{
                                                                stud.clnc_item }}</span>
                                                        <span v-else class="fw-bold">No Allergy</span>
                                                    </li>
                                                    <li class="list-group-item bg-transparent px-4">
                                                        Diet: <span v-if="stud.clnc_diet" class="fw-bold">{{
                                                            stud.clnc_diet }}</span>
                                                        <span v-else class="fw-bold">No Diet</span>
                                                    </li>
                                                    <li class="list-group-item bg-transparent px-4">
                                                        Complaint: <span class="fw-bold">{{ stud.clnc_complaint
                                                            }}</span>
                                                    </li>
                                                    <li class="list-group-item bg-transparent px-4">
                                                        Evaluation: <span class="fw-bold">{{
                                                            stud.clnc_evaluation
                                                        }}</span>
                                                    </li>
                                                    <li class="list-group-item bg-transparent px-4">
                                                        Intervention: <span class="fw-bold">{{
                                                            stud.clnc_intervention ?
                                                                stud.clnc_intervention : 'no interventions needed'
                                                        }}</span>
                                                    </li>
                                                    <li class="list-group-item bg-transparent px-4">
                                                        <button :disabled="saving ? true : false" type="button"
                                                            class="neu-btn neu-purple p-2">
                                                            <font-awesome-icon icon="fa-solid fa-print"/> Print Form
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
        <!-- <div class="accordion" id="medicalaccordion">
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
                                            aria-describedby="firstname" v-model="personal.per_firstname" disabled>
                                    </div>
                                    <div class="form-group col">
                                        <label for="middlename">Middle Name</label>
                                        <input type="text" class="form-control" id="middlename"
                                            aria-describedby="middlename" v-model="personal.per_middlename" disabled>
                                    </div>
                                    <div class="form-group col">
                                        <label for="lastname">Last Name</label>
                                        <input type="text" class="form-control" id="lastname"
                                            aria-describedby="lastname" v-model="personal.per_lastname" disabled>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="form-group col">
                                        <label for="suffixname">Suffix Name</label>
                                        <input type="text" class="form-control" id="suffixname"
                                            aria-describedby="suffixname" maxlength="3"
                                            v-model="personal.per_suffixname" disabled>
                                    </div>
                                    <div class="form-group col">
                                        <label for="gender">Gender</label>
                                        <select class="form-control" id="gender" v-model="personal.per_gender" required
                                            disabled>
                                            <option v-for="(gen, index) in gender" :value="gen.gen_id">{{ gen.gen_desc
                                                }}</option>
                                        </select>
                                    </div>
                                    <div class="form-group col">
                                        <label for="civilstatus">Civil Status</label>
                                        <select class="form-control" id="civilstatus" v-model="personal.per_civilstatus"
                                            required disabled>
                                            <option v-for="(cs, index) in civilstatus" :value="cs.cv_id">
                                                {{ cs.cv_desc }}
                                            </option>
                                        </select>
                                    </div>
                                    <div class="form-group col">
                                        <label for="nationality">Nationality</label>
                                        <select class="form-control" id="nationality" v-model="personal.per_nationality"
                                            required disabled>
                                            <option v-for="(nat, index) in nationality" :value="nat.nat_id">
                                                {{ nat.nat_desc }}
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
                                    <div class="form-group col">
                                        <label for="exampleInputEmail1">Telephone No.</label>
                                        <input class="form-control" type="email" name="email" id="email" placeholder="Email">
                                    </div>
                                    <div class="form-group col">
                                        <label for="cellphone">Cellphone No.</label>
                                        <input class="form-control" type="text" name="cellphone" id="cellphone"
                                            placeholder="Cellphone No." v-model="personal.per_contact"
                                            onkeydown="return /[0-9]/i.test(event.key)" minlength="9" maxlength="9"
                                            required disabled>
                                    </div>
                                    <div class="form-group col">
                                        <label for="peremail">Email</label>
                                        <input class="form-control" type="peremail" name="peremail" id="peremail"
                                            placeholder="Email" v-model="personal.per_email" required disabled>
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
                                            v-model="personal.per_birthday" @focusout="getAge(personal.per_birthday)"
                                            required disabled>
                                    </div>
                                    <div class="form-group col">
                                        <label for="age">Age</label>
                                        <input type="number" class="form-control" id="age" aria-describedby="emailHelp"
                                            v-model="age" disabled>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="form-group col">
                                        <label for="birthprovince">Birth Province</label>
                                        <select class="form-control" id="birthprovince"
                                            @change="updateAddress('birth-city', personal.per_birth_province)"
                                            v-model="personal.per_birth_province" required disabled>
                                            <option>- Select Here -</option>
                                            <option v-for="(prov, index) in filteredBirthProvince"
                                                :value="prov.provCode">{{
                                                    prov.provDesc }}</option>
                                        </select>
                                    </div>
                                    <div class="form-group col">
                                        <label for="birthcity">Birth City</label>
                                        <select class="form-control" id="birthcity" v-model="personal.per_birth_city"
                                            disabled>
                                            <option>- Select Here -</option>
                                            <option v-for="(ct, index) in filteredBirthCity" :value="ct.citymunCode">{{
                                                ct.citymunDesc
                                                }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group border-bottom">
                                <p class="text-secondary">Present Address</p>
                            </div>
                            <div class="form-group d-flex flex-column gap-2">
                                <div class="row mb-2">
                                    <div class="form-group col">
                                        <label for="presenthome">Address</label>
                                        <input type="text" class="form-control" id="presenthome"
                                            aria-describedby="presenthome" v-model="personal.per_curr_home" disabled>
                                    </div>
                                    <div class="form-group col">
                                        <label for="presentregion">Region</label>
                                        <select class="form-control" id="presentregion"
                                            v-model="personal.per_curr_region"
                                            @change="updateAddress('current-province', personal.per_curr_region)"
                                            required disabled>
                                            <option>- Select Here -</option>
                                            <option v-for="(reg, index) in filteredCurrRegion" :value="reg.regCode">
                                                {{ reg.regDesc }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="form-group col">
                                        <label for="presentprovince">Province</label>
                                        <select class="form-control" id="presentprovince"
                                            v-model="personal.per_curr_province"
                                            @change="updateAddress('current-city', personal.per_curr_province)" required
                                            disabled>
                                            <option>- Select Here -</option>
                                            <option v-for="(prov, index) in filteredCurrProvince"
                                                :value="prov.provCode">
                                                {{ prov.provDesc }}
                                            </option>
                                        </select>
                                    </div>
                                    <div class="form-group col">
                                        <label for="presentcity">City</label>
                                        <select class="form-control" id="presentcity" v-model="personal.per_curr_city"
                                            @change="updateAddress('current-barangay', personal.per_curr_city)" required
                                            disabled>
                                            <option>- Select Here -</option>
                                            <option v-for="(ct, index) in filteredCurrCity" :value="ct.citymunCode">
                                                {{ ct.citymunDesc }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col">
                                        <label for="preentbarangay">Barangay</label>
                                        <select class="form-control" id="preentbarangay"
                                            v-model="personal.per_curr_barangay" disabled>
                                            <option>- Select Here -</option>
                                            <option v-for="(brgy, index) in filteredCurrBarangay"
                                                :value="brgy.brgyCode">
                                                {{ brgy.brgyDesc }}
                                            </option>
                                        </select>
                                    </div>
                                    <div class="form-group col">
                                        <label for="presentzipcode">Zip Code</label>
                                        <input type="text" class="form-control" id="presentzipcode"
                                            aria-describedby="emailHelp" v-model="personal.per_curr_zipcode" disabled>
                                    </div>
                                </div>
                            </div>
                            <div
                                class="form-group border-bottom d-flex justify-content-center align-content-center gap-2">
                                <input @change="copyAddress()" type="checkbox" class="form-check-input" id="sameas"
                                    v-model="sameAddress" disabled>
                                <p class="text-secondary">Permanent Address</p>
                            </div>
                            <div class="form-group d-flex flex-column gap-2">
                                <div class="row">
                                    <div class="form-group col">
                                        <label for="permanenthome">Address</label>
                                        <input type="text" class="form-control" id="permanenthome"
                                            aria-describedby="emailHelp" v-model="personal.per_perm_home" disabled>
                                    </div>
                                    <div class="form-group col">
                                        <label for="permanentregion">Region</label>
                                        <select class="form-control" id="permanentregion"
                                            v-model="personal.per_perm_region"
                                            @change="updateAddress('permanent-province', personal.per_perm_region)"
                                            disabled>
                                            <option>- Select Here -</option>
                                            <option v-for="(reg, index) in filteredPermRegion" :value="reg.regCode">
                                                {{ reg.regDesc }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col">
                                        <label for="permanentprovince">Province</label>
                                        <select class="form-control" id="permanentprovince"
                                            v-model="personal.per_perm_province"
                                            @change="updateAddress('permanent-city', personal.per_perm_province)"
                                            disabled>
                                            <option>- Select Here -</option>
                                            <option v-for="(prov, index) in filteredPermProvince"
                                                :value="prov.provCode">
                                                {{ prov.provDesc }}
                                            </option>
                                        </select>
                                    </div>
                                    <div class="form-group col">
                                        <label for="permanentcity">City</label>
                                        <select class="form-control" id="permanentcity" v-model="personal.per_perm_city"
                                            @change="updateAddress('permanent-barangay', personal.per_perm_city)"
                                            disabled>
                                            <option>- Select Here -</option>
                                            <option v-for="(ct, index) in filteredPermCity" :value="ct.citymunCode">
                                                {{ ct.citymunDesc }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col">
                                        <label for="permanentbarangay">Barangay</label>
                                        <select class="form-control" id="permanentbarangay"
                                            v-model="personal.per_perm_barangay" disabled>
                                            <option>- Select Here -</option>
                                            <option v-for="(brgy, index) in filteredPermBarangay"
                                                :value="brgy.brgyCode">
                                                {{ brgy.brgyDesc }}
                                            </option>
                                        </select>
                                    </div>
                                    <div class="form-group col">
                                        <label for="permanentzipcode">Zip Code</label>
                                        <input type="text" class="form-control" id="permanentzipcode"
                                            aria-describedby="permanentzipcode" disabled
                                            v-model="personal.per_perm_zipcode">
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
                                            <input v-model="clinicalRecords.clnc_1st_dose" type="text"
                                                class="form-control form-control-sm" />
                                        </div>
                                        <div class="form-group col text-start">
                                            <label>2nd Dose Vaccine</label>
                                            <input v-model="clinicalRecords.clnc_2nd_dose" type="text"
                                                class="form-control form-control-sm" />
                                        </div>
                                        <div class="form-group col text-start">
                                            <label>3rd Dose Vaccine</label>
                                            <input v-model="clinicalRecords.clnc_3rd_dose" type="text"
                                                class="form-control form-control-sm" />
                                        </div>
                                        <div class="form-group col text-start">
                                            <label>Family illness History</label>
                                            <input v-model="clinicalRecords.clnc_fam_illness" type="text"
                                                class="form-control form-control-sm" />
                                        </div>
                                        <div class="form-group col text-start">
                                            <label>Social History</label>
                                            <select class="neu-input neu-select"
                                                v-model="clinicalRecords.clnc_social_history" required>
                                                <option value="0">N/A</option>
                                                <option value="1">Tobacco</option>
                                                <option value="2">Alcohol</option>
                                                <option value="3">Illicit Drug</option>
                                            </select>
                                        </div>
                                        <div class="form-group col text-start">
                                            <label>Medication</label>
                                            <input v-model="clinicalRecords.clnc_medication" type="text"
                                                class="form-control form-control-sm" />
                                        </div>
                                        <div class="form-group col text-start">
                                            <label>Height (Ft)</label>
                                            <input v-model="clinicalRecords.clnc_height" required type="number"
                                                step="0.01" class="form-control form-control-sm" />
                                        </div>
                                        <div class="form-group col text-start">
                                            <label>Weight (Kg)</label>
                                            <input v-model="clinicalRecords.clnc_weight" required type="number"
                                                step="0.01" class="form-control form-control-sm" />
                                        </div>
                                        <div class="form-group col text-start">
                                            <label>Allergy</label>
                                            <select class="neu-input neu-select"
                                                v-model="clinicalRecords.clnc_allergy" required>
                                                <option value="0">N/A</option>
                                                <option value="1">Food</option>
                                                <option value="2">Drug</option>
                                                <option value="3">Chemicals</option>
                                            </select>
                                        </div>
                                        <div class="form-group col text-start">
                                            <label>Specify</label>
                                            <input v-model="clinicalRecords.clnc_item" required
                                                :disabled="clinicalRecords.clnc_allergy == 0 ? true : false" type="text"
                                                class="form-control form-control-sm disabled:cursor-not-allowed disabled:opacity-50" />
                                        </div>
                                        <div class="form-group col text-start">
                                            <label>Diet</label>
                                            <input v-model="clinicalRecords.clnc_diet" type="text"
                                                class="form-control form-control-sm" />
                                        </div>
                                        <div class="form-group col text-start">
                                            <label>Chief Complaints / Assessment</label>
                                            <textarea class="h-36 form-control form-control-sm"
                                                v-model="clinicalRecords.clnc_complaint" required>
                                                </textarea>
                                        </div>
                                        <div class="form-group col text-start">
                                            <label>Evaluation</label>
                                            <input v-model="clinicalRecords.clnc_evaluation" required type="text"
                                                class="form-control form-control-sm" />
                                        </div>
                                        <div class="form-group col text-start">
                                            <label>Intervention</label>
                                            <input v-model="clinicalRecords.clnc_intervention" type="text"
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
                                                                    stud.clnc_date
                                                                }}</span>
                                                        </li>
                                                        <li class="list-group-item">
                                                            1st Dose Vaccine: <span class="fw-bold">{{
                                                                stud.clnc_1st_dose ?
                                                                    stud.clnc_1st_dose : 'N/A' }}</span>
                                                        </li>
                                                        <li class="list-group-item">
                                                            2nd Dose Vaccine: <span class="fw-bold">{{
                                                                stud.clnc_2nd_dose ?
                                                                    stud.clnc_2nd_dose : 'N/A' }}</span>
                                                        </li>
                                                        <li class="list-group-item">
                                                            3rd Dose Vaccine: <span class="fw-bold">{{
                                                                stud.clnc_3rd_dose ?
                                                                    stud.clnc_3rd_dose : 'N/A' }}</span>
                                                        </li>
                                                        <li class="list-group-item">
                                                            Family Illness History: <span class="fw-bold">{{
                                                                stud.clnc_fam_illness ?
                                                                    stud.clnc_fam_illness : 'No History' }}</span>
                                                        </li>
                                                        <li class="list-group-item">
                                                            Social Illness History:
                                                            <span v-if="stud.clnc_social_history == 0"
                                                                class="fw-bold">No
                                                                History</span>
                                                            <span v-if="stud.clnc_social_history == 1"
                                                                class="fw-bold">Tobacco</span>
                                                            <span v-if="stud.clnc_social_history == 2"
                                                                class="fw-bold">Alcohol</span>
                                                            <span v-if="stud.clnc_social_history == 3"
                                                                class="fw-bold">Illicit
                                                                Drug</span>
                                                        </li>
                                                        <li class="list-group-item">
                                                            Medication: <span class="fw-bold">{{
                                                                stud.clnc_medication
                                                            }}</span>
                                                        </li>
                                                        <li class="list-group-item">
                                                            Height: <span class="fw-bold">{{ stud.clnc_height
                                                                }}ft</span>
                                                        </li>
                                                        <li class="list-group-item">
                                                            Weight: <span class="fw-bold">{{ stud.clnc_weight
                                                                }}Kg</span>
                                                        </li>
                                                        <li class="list-group-item">
                                                            Has Allergy:
                                                            <span v-if="stud.clnc_allergy == 0" class="fw-bold">No
                                                                Allergy</span>
                                                            <span v-if="stud.clnc_allergy == 1"
                                                                class="fw-bold">Food</span>
                                                            <span v-if="stud.clnc_allergy == 2"
                                                                class="fw-bold">Drug</span>
                                                            <span v-if="stud.clnc_allergy == 3"
                                                                class="fw-bold">Chemical</span>
                                                        </li>
                                                        <li class="list-group-item">
                                                            Specification: <span v-if="stud.clnc_allergy != 0"
                                                                class="fw-bold">{{
                                                                    stud.clnc_item }}</span>
                                                            <span v-else class="fw-bold">No Allergy</span>
                                                        </li>
                                                        <li class="list-group-item">
                                                            Diet: <span v-if="stud.clnc_diet" class="fw-bold">{{
                                                                stud.clnc_diet }}</span>
                                                            <span v-else class="fw-bold">No Diet</span>
                                                        </li>
                                                        <li class="list-group-item">
                                                            Complaint: <span class="fw-bold">{{ stud.clnc_complaint
                                                                }}</span>
                                                        </li>
                                                        <li class="list-group-item">
                                                            Evaluation: <span class="fw-bold">{{
                                                                stud.clnc_evaluation
                                                            }}</span>
                                                        </li>
                                                        <li class="list-group-item">
                                                            Intervention: <span class="fw-bold">{{
                                                                stud.clnc_intervention ?
                                                                    stud.clnc_intervention : 'no interventions needed'
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

        </div>-->
    </div>
</template>