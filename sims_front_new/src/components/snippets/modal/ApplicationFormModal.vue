<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { addApplicant, getPerson, getFamily, getAward, getAttainment, updateApplicant, updatePersonDetails, deleteFamAwrAtt } from "../../Fetchers.js";
import { getUserID } from "../../../routes/user.js";
import Loader1 from '../loaders/Loader1.vue';
import {
    pdfGenerator
} from "../../Generators.js";

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
    countryData: {
    },
    formId: {
    },
    formMode: {
    }
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
const country = computed(() => {
    return props.countryData
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
const registrationMode = computed(() => {
    return props.formMode
});
const router = useRouter();

// for select box div absolute

const userID = ref('');
const sameAddress = ref(false);
const familyMembers = ref([]);
const awardList = ref([]);
const attainmentList = ref([]);
const age = ref(0)
const yearToday = ref(0)
const saving = ref(false);
const checking = ref(false);
const forUpdate = ref(false)

const filteredBirthCountry = ref([])
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



const personal = ref({
    per_firstname: '',
    per_middlename: '',
    per_lastname: '',
    per_suffixname: '',
    per_gender: '',
    per_contact: '',
    per_email: '',
    per_birthday: '',
    per_birth_country: '',
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

const famMembersInfo = ref({
    fam_firstname: '',
    fam_middlename: '',
    fam_lastname: '',
    fam_suffixname: '',
    fam_relationship: '',
    fam_contact: '',
    fam_email: '',
})

const awardInfo = ref({
    awr_desc: '',
    awr_year: '',
})

const attainmentInfo = ref({
    pered_school: '',
    pered_from: '',
    pered_to: '',
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
            personal.value.per_id ? forUpdate.value = true : forUpdate.value = false
        }

        await getFamily(results.per_famid, 1).then((results) => {
            if (results) {
                familyMembers.value = results
            }
        })
        await getAward(results.per_educid).then((results) => {
            if (results) {
                awardList.value = results
            }
        })
        await getAttainment(results.per_attainmentid).then((results) => {
            if (results) {
                attainmentList.value = results
            }
        })
    })
}

onMounted(async () => {
    var date = new Date();
    // var day = date.getDate();
    // var month = date.getMonth() + 1;
    // var year = date.getFullYear();
    yearToday.value = date.getFullYear();
    personal.value.per_birthday = date.toISOString().split('T')[0]

    filteredBirthCountry.value = country.value
    filteredBirthProvince.value = province.value
    filteredCurrRegion.value = region.value
    filteredPermRegion.value = region.value

    getUserID().then((results) => {
        userID.value = results.account.data.id
    })

    checking.value = true
    if (personID.value) {
        await booter().then((results) => {
            checking.value = false
        })
    } else {
        checking.value = false
    }

    getAge(personal.value.per_birthday)
})

const getAge = (bday) => {
    var date = new Date(bday);
    var bdayyear = date.getFullYear();
    age.value = yearToday.value - bdayyear
}


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


const downloadPdf = (filetype) => {
    // var element = document.getElementById('printform');
    // html2pdf(element);
    let name = 1111 + '_' + filetype
    pdfGenerator(name, 'a4', 'portrait', 0.1)
    Swal.fire({
        icon: "success",
        title: "Download Complete",
        text: "Check your file manager, refreshing the page",
    }).then(() => {
        location.reload()
    });

}

</script>
<template>
    <div v-if="checking">
        <Loader1></Loader1>
    </div>
    <div v-else>
        <div class="d-flex justify-content-center mb-2" id="printform" style="font-size: 9.5px;">
            <div class="bg-opaque"
                style="width: 770px; height: 1095px; border:2px solid black;padding:1px; text-align: center;">
                <div style="height: 100%; width: 100%; border:1px solid black">
                    <div class="p-2 d-flex gap-1">
                        <div class="w-25">
                            <img src="/img/clcst_logo.png" height="70px" width="70px" alt="...">
                        </div>
                        <div class="w-100 fw-bold text-center">
                            <p class="m-0">CENTRAL LUZON COLLEGE OF SCIENCE AND TECHNOLOGY, INC.</p>
                            <p class="m-0 fw-normal small-font">B. Mendoza St., Brgy. Sto. Rosario, City of San Fernando,
                                Pampanga, Philippines, 2000</p>
                            <p class="m-0 fw-normal small-font">Tel. Nos: (045) 435-1495</p>
                            <p class="m-0 fw-normal small-font">Founded 1959</p>
                        </div>
                        <div class="w-25">
                            <img src="/img/clcst_logo.png" height="70px" width="70px" alt="...">
                        </div>
                    </div>
                    <div class="p-2 fw-bold">
                        <span>Application Form</span>
                    </div>
                    <div class="text-uppercase bg-white">
                        <div class="p-1 bg-secondary-subtle text-start">
                            <span class="fst-italic fw-normal" style="font-size: 8px;">Personal Information</span>
                        </div>
                        <div class="d-flex fw-semibold">
                            <div class="w-100 p-1 border">
                                <div class="d-flex flex-column justify-content-center align-items-start">
                                    <label class="fst-italic fw-normal" style="font-size: 8px;">First Name</label>
                                    <span class="w-100">{{ personal.per_firstname }}</span>
                                </div>
                            </div>
                            <div class="w-100 p-1 border">
                                <div class="d-flex flex-column justify-content-center align-items-start">
                                    <label class="fst-italic fw-normal" style="font-size: 8px;">Middle Name</label>
                                    <span class="w-100">{{ personal.per_middlename }}</span>
                                </div>
                            </div>
                            <div class="w-100 p-1 border">
                                <div class="d-flex flex-column justify-content-center align-items-start">
                                    <label class="fst-italic fw-normal" style="font-size: 8px;">Last Name</label>
                                    <span class="w-100">{{ personal.per_lastname }}</span>
                                </div>
                            </div>
                            <div class="w-25 p-1 border">
                                <div class="d-flex flex-column justify-content-center align-items-start">
                                    <label class="fst-italic fw-normal" style="font-size: 8px;">Suffix Name</label>
                                    <span class="w-100">{{ personal.per_suffixname }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex fw-semibold">
                            <div class="w-100 p-1 border">
                                <div class="d-flex flex-column justify-content-center align-items-start">
                                    <label class="fst-italic fw-normal" style="font-size: 8px;">Birthday</label>
                                    <span class="w-100">{{ personal.per_birthday }}</span>
                                </div>
                            </div>
                            <div class="w-25 p-1 border">
                                <div class="d-flex flex-column justify-content-center align-items-start">
                                    <label class="fst-italic fw-normal" style="font-size: 8px;">Age</label>
                                    <span class="w-100">{{ age }}</span>
                                </div>
                            </div>
                            <div class="w-25 p-1 border">
                                <div class="d-flex flex-column justify-content-center align-items-start">
                                    <label class="fst-italic fw-normal" style="font-size: 8px;">Gender</label>
                                    <select class="label-selectbox fw-semibold" id="gender" v-model="personal.per_gender"
                                        disabled>
                                        <option v-for="(gen, index) in gender" :value="gen.gen_id">{{ gen.gen_desc }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="w-100 p-1 border">
                                <div class="d-flex flex-column justify-content-center align-items-start">
                                    <label class="fst-italic fw-normal" style="font-size: 8px;">Nationality</label>
                                    <select class="label-selectbox fw-semibold" id="nationality"
                                        v-model="personal.per_nationality" disabled>
                                        <option v-for="(nat, index) in nationality" :value="nat.nat_id">
                                            {{ nat.nat_desc }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="w-100 p-1 border">
                                <div class="d-flex flex-column justify-content-center align-items-start">
                                    <label class="fst-italic fw-normal" style="font-size: 8px;">Civil Status</label>
                                    <select class="label-selectbox fw-semibold" id="civilstatus"
                                        v-model="personal.per_civilstatus" disabled>
                                        <option v-for="(cs, index) in civilstatus" :value="cs.cv_id">
                                            {{ cs.cv_desc }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="p-1 bg-secondary-subtle text-start">
                            <span class="fst-italic fw-normal" style="font-size: 8px;">Contact Information</span>
                        </div>
                        <div class="d-flex fw-semibold">
                            <div class="w-100 p-1 border">
                                <div class="d-flex flex-column justify-content-center align-items-start">
                                    <label class="fst-italic fw-normal" style="font-size: 8px;">Active Contact No.</label>
                                    <span class="w-100">0{{ personal.per_contact }}</span>
                                </div>
                            </div>
                            <div class="w-100 p-1 border">
                                <div class="d-flex flex-column justify-content-center align-items-start">
                                    <label class="fst-italic fw-normal" style="font-size: 8px;">Active Email
                                        Address</label>
                                    <span class="w-100">{{ personal.per_email }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="p-1 bg-secondary-subtle text-start">
                            <span class="fst-italic fw-normal" style="font-size: 8px;">Birth Information</span>
                        </div>
                        <div class="d-flex fw-semibold">
                            <div class="w-100 p-1 border">
                                <div class="d-flex flex-column justify-content-center align-items-start">
                                    <label class="fst-italic fw-normal" style="font-size: 8px;">Birth Country</label>
                                    <select class="label-selectbox fw-bold" id="birthprovince"
                                        v-model="personal.per_birth_country" disabled>
                                        <option v-for="(country, index) in filteredBirthCountry" :value="country.countryID">
                                            {{
                                                country.name }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="w-100 p-1 border">
                                <div class="d-flex flex-column justify-content-center align-items-start">
                                    <label class="fst-italic fw-normal" style="font-size: 8px;">Birth Province</label>
                                    <select class="label-selectbox fw-bold" id="birthprovince"
                                        v-model="personal.per_birth_province" disabled>
                                        <option v-for="(prov, index) in filteredBirthProvince" :value="prov.provCode">{{
                                            prov.provDesc }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="w-100 p-1 border">
                                <div class="d-flex flex-column justify-content-center align-items-start">
                                    <label class="fst-italic fw-normal" style="font-size: 8px;">Birth City</label>
                                    <select class="label-selectbox fw-bold" id="birthcity" v-model="personal.per_birth_city"
                                        disabled>
                                        <option v-for="(ct, index) in filteredBirthCity" :value="ct.citymunCode">{{
                                            ct.citymunDesc
                                            }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="p-1 bg-secondary-subtle text-start">
                            <span class="fst-italic fw-normal" style="font-size: 8px;">Address Information</span>
                        </div>
                        <div class="d-flex flex-column fw-semibold">
                            <div class="text-center bg-body-secondary">
                                <span class="fst-italic fw-normal" style="font-size: 8px;">Current / Present Address</span>
                            </div>
                            <div class="d-flex">
                                <div class="w-100 p-1 border">
                                    <div class="d-flex flex-column justify-content-center align-items-start">
                                        <label class="fst-italic fw-normal" style="font-size: 8px;">Current Home
                                            Address</label>
                                        <input type="text" style="text-transform:uppercase" class="label-selectbox fw-bold"
                                            id="presentzipcode" aria-describedby="emailHelp"
                                            v-model="personal.per_curr_home">
                                    </div>
                                </div>
                                <div class="w-100 p-1 border">
                                    <div class="d-flex flex-column justify-content-center align-items-start">
                                        <label class="fst-italic fw-normal" style="font-size: 8px;">Current Region</label>
                                        <select class="label-selectbox fw-bold" id="presentregion"
                                            v-model="personal.per_curr_region" disabled>
                                            <option v-for="(reg, index) in filteredCurrRegion" :value="reg.regCode">
                                                {{ reg.regDesc }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex">
                                <div class="w-100 p-1 border">
                                    <div class="d-flex flex-column justify-content-center align-items-start">
                                        <label class="fst-italic fw-normal" style="font-size: 8px;">Current
                                            Province</label>
                                        <select class="label-selectbox fw-bold" id="presentprovince"
                                            v-model="personal.per_curr_province" disabled>
                                            <option v-for="(prov, index) in filteredCurrProvince" :value="prov.provCode">
                                                {{ prov.provDesc }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="w-100 p-1 border">
                                    <div class="d-flex flex-column justify-content-center align-items-start">
                                        <label class="fst-italic fw-normal" style="font-size: 8px;">Current City</label>
                                        <select class="label-selectbox fw-bold" id="presentcity"
                                            v-model="personal.per_curr_city" disabled>
                                            <option v-for="(ct, index) in filteredCurrCity" :value="ct.citymunCode">
                                                {{ ct.citymunDesc }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="w-100 p-1 border">
                                    <div class="d-flex flex-column justify-content-center align-items-start">
                                        <label class="fst-italic fw-normal" style="font-size: 8px;">Current City</label>
                                        <select class="label-selectbox fw-bold" id="preentbarangay"
                                            v-model="personal.per_curr_barangay" disabled>
                                            <option v-for="(brgy, index) in filteredCurrBarangay" :value="brgy.brgyCode">
                                                {{ brgy.brgyDesc }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="w-50 p-1 border">
                                    <div class="d-flex flex-column justify-content-center align-items-start">
                                        <label class="fst-italic fw-normal" style="font-size: 8px;">Current Zipcode</label>
                                        <input type="text" style="text-transform:uppercase" class="label-selectbox fw-bold"
                                            id="presentzipcode" aria-describedby="emailHelp"
                                            v-model="personal.per_curr_zipcode">
                                    </div>
                                </div>
                            </div>
                            <div class="text-center bg-body-secondary">
                                <span class="fst-italic fw-normal" style="font-size: 8px;">Permanent Address</span>
                            </div>
                            <div class="d-flex">
                                <div class="w-100 p-1 border">
                                    <div class="d-flex flex-column justify-content-center align-items-start">
                                        <label class="fst-italic fw-normal" style="font-size: 8px;">Permanent Home
                                            Address</label>
                                        <input type="text" style="text-transform:uppercase" class="label-selectbox fw-bold"
                                            id="presentzipcode" aria-describedby="emailHelp"
                                            v-model="personal.per_perm_home">
                                    </div>
                                </div>
                                <div class="w-100 p-1 border">
                                    <div class="d-flex flex-column justify-content-center align-items-start">
                                        <label class="fst-italic fw-normal" style="font-size: 8px;">Permanent
                                            Region</label>
                                        <select class="label-selectbox fw-bold" id="presentregion"
                                            v-model="personal.per_perm_region" disabled>
                                            <option v-for="(reg, index) in filteredPermRegion" :value="reg.regCode">
                                                {{ reg.regDesc }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex">
                                <div class="w-100 p-1 border">
                                    <div class="d-flex flex-column justify-content-center align-items-start">
                                        <label class="fst-italic fw-normal" style="font-size: 8px;">Permanent
                                            Province</label>
                                        <select class="label-selectbox fw-bold" id="presentprovince"
                                            v-model="personal.per_perm_province" disabled>
                                            <option v-for="(prov, index) in filteredPermProvince" :value="prov.provCode">
                                                {{ prov.provDesc }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="w-100 p-1 border">
                                    <div class="d-flex flex-column justify-content-center align-items-start">
                                        <label class="fst-italic fw-normal" style="font-size: 8px;">Permanent City</label>
                                        <select class="label-selectbox fw-bold" id="presentcity"
                                            v-model="personal.per_perm_city" disabled>
                                            <option v-for="(ct, index) in filteredPermCity" :value="ct.citymunCode">
                                                {{ ct.citymunDesc }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="w-100 p-1 border">
                                    <div class="d-flex flex-column justify-content-center align-items-start">
                                        <label class="fst-italic fw-normal" style="font-size: 8px;">Permanent City</label>
                                        <select class="label-selectbox fw-bold" id="preentbarangay"
                                            v-model="personal.per_perm_barangay" disabled>
                                            <option v-for="(brgy, index) in filteredPermBarangay" :value="brgy.brgyCode">
                                                {{ brgy.brgyDesc }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="w-50 p-1 border">
                                    <div class="d-flex flex-column justify-content-center align-items-start">
                                        <label class="fst-italic fw-normal" style="font-size: 8px;">Permanent
                                            Zipcode</label>
                                        <input type="text" style="text-transform:uppercase" class="label-selectbox fw-bold"
                                            id="presentzipcode" aria-describedby="emailHelp"
                                            v-model="personal.per_perm_zipcode">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="p-1 bg-secondary-subtle text-start">
                            <span class="fst-italic fw-normal" style="font-size: 8px;">Educational Background
                                Information</span>
                        </div>
                        <div class="d-flex fw-semibold">
                            <table class="w-100" style="text-transform:uppercase">
                                <thead>
                                    <tr class="border text-center bg-body-secondary fst-italic fw-normal">
                                        <td scope="col" style="font-size: 8px;" class="w-75">School</td>
                                        <td scope="col" style="font-size: 8px;" class="w-25"> Year From - Year To</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(attain, index) in attainmentList">
                                        <td class="border">{{ attain.pered_school }}</td>
                                        <td class="border">{{ attain.pered_from }} - {{ attain.pered_to }}</td>

                                    </tr>
                                    <tr v-if="!Object.keys(attainmentList).length">
                                        <td class="border" colspan="2">Empty List</td>
                                    </tr>
                                    <tr v-for="n in 5 - Object.keys(attainmentList).length" :key="n">
                                        <td class="border" colspan="2">
                                            <p class=""></p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="p-1 bg-secondary-subtle text-start">
                            <span class="fst-italic fw-normal" style="font-size: 8px;">Family Background Information</span>
                        </div>
                        <div class="d-flex fw-semibold">
                            <table class="w-100" style="text-transform:uppercase">
                                <thead>
                                    <tr class="border text-center bg-body-secondary fst-italic fw-normal"
                                        style="font-size: 8px;">
                                        <td scope="col">Name</td>
                                        <td scope="col">Relationship</td>
                                        <td scope="col">Contact No.</td>
                                        <td scope="col">Email</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(fam, index) in familyMembers">
                                        <td class="border">{{ fam.fam_firstname }} {{ fam.fam_middlename }} {{
                                            fam.fam_lastname }} {{
                                                fam.fam_suffixname }}</td>
                                        <td class="border">{{ fam.fam_relationship }}</td>
                                        <td class="border">{{ fam.fam_contact }}</td>
                                        <td class="border" style="text-transform:none">{{ fam.fam_email }}</td>
                                    </tr>
                                    <tr v-if="!Object.keys(familyMembers).length">
                                        <td colspan="4" class="border">Empty List</td>
                                    </tr>
                                    <tr v-for="n in 5 - Object.keys(familyMembers).length" :key="n">
                                        <td class="border" colspan="4">
                                            <p class=""></p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="p-1 bg-secondary-subtle text-start">
                            <span class="fst-italic fw-normal" style="font-size: 8px;">School Awards Information</span>
                        </div>
                        <div class="d-flex fw-semibold">
                            <table class="w-100" style="text-transform:uppercase">
                                <thead>
                                    <tr class="border text-center bg-body-secondary fst-italic fw-normal"
                                        style="font-size: 8px;">
                                        <td scope="col">Title</td>
                                        <td scope="col">Year</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(award, index) in awardList">
                                        <td class="border">{{ award.awr_desc }}</td>
                                        <td class="border">{{ award.awr_year }}</td>

                                    </tr>
                                    <tr v-if="!Object.keys(awardList).length">
                                        <td class="border" colspan="2">Empty List</td>
                                    </tr>
                                    <tr v-for="n in 5 - Object.keys(awardList).length" :key="n">
                                        <td class="border" colspan="2">
                                            <p class=""></p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="p-1 bg-secondary-subtle text-center">
                            <span class=" fw-normal" style="font-size: 8px;">Data Privacy Act Notice</span>
                        </div>
                        <div class="small-font d-flex flex-column justify-content-center align-items-center p-2 bg-white">
                            <div class="text-wrap w-75" style="text-align: justify; text-transform: none;">
                                This form is strictly for school purposes only.
                                The information contained herein is confidential and must not be shared, reproduced,
                                or used outside its intended academic purpose. Handling of this data complies with the
                                provisions of the Data Privacy Act of 2012 (R.A. 10173).
                                Any unauthorized disclosure or misuse of the information is prohibited and may be subject to
                                penalties under applicable laws.
                            </div>
                            <div class="d-flex gap-1 justify-content-around w-100" style="margin-top: 50px;">
                                <div class="d-flex flex-column position-relative">
                                    <img src="/img/sig1.png" height="100px" width="100px"
                                        style="position: absolute;top:-55px; left: 15px;" />
                                    <span class="fw-bold">RENATO P. LEGASPI, Ph.D.</span>
                                    <span>Pangulo/Punong Tagapagpaganap</span>
                                    <span class="fst-italic">President / CEO</span>
                                </div>
                                <div class="d-flex flex-column position-relative">
                                    <img src="/img/sig1.png" height="100px" width="100px"
                                        style="position: absolute;top:-55px; left: 15px;" />
                                    <span class="fw-bold">RENE PAULO M. LEGASPI, Ph.D.</span>
                                    <span>Punong-Guro</span>
                                    <span class="fst-italic">Principal</span>
                                </div>
                                <div class="d-flex flex-column position-relative">
                                    <img src="/img/sig1.png" height="100px" width="100px"
                                        style="position: absolute;top:-55px; left: 15px;" />
                                    <span class="fw-bold">RENE PAULO M. LEGASPI, Ph.D.</span>
                                    <span>Punong-Guro</span>
                                    <span class="fst-italic">Principal</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <form @submit.prevent="downloadPdf('application-form')"
            class="form-group border p-4 rounded d-flex flex-column justify-content-center gap-2 bg-secondary-subtle">
            <div class="form-group border rounded d-flex justify-content-center gap-2">
                <input type="checkbox" class="form-check-input" required :disabled="saving ? true : false">
                <label class="form-check-label" for="agreeChecker">I have read and accepted the terms and
                    conditions</label>
            </div>
            <div class="form-group border rounded d-flex justify-content-center gap-2" tabindex="-1"
                :disabled="saving ? true : false">
                <button type="submit" class="btn btn-md btn-success w-100">
                    <i class="mr-2 fa-solid fa-rotate-left"></i> Print and Download Form
                </button>

            </div>
        </form>
    </div>




</template>