<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { updateArchiveDetails } from "../../Fetchers.js";
import { getUserID } from "../../../routes/user.js";
import Loader1 from '../loaders/Loader1.vue';


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
    studentData: {
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

const student = computed(() => {
  return props.studentData
});

const router = useRouter();

// for select box div absolute

const userID = ref('');
const sameAddress = ref(false);
const age = ref(0)
const yearToday = ref(0)
const saving = ref(false);
const checking = ref(true);

const filteredCurrRegion = ref([])
const filteredCurrProvince = ref([])
const filteredCurrCity= ref([])
const filteredCurrBarangay = ref([])
const filteredCurrZipcode = ref([])

const filteredPermRegion = ref([])
const filteredPermProvince = ref([])
const filteredPermCity= ref([])
const filteredPermBarangay = ref([])
const filteredPermZipcode = ref([])

const personal = ref({
    arc_firstname:'',
    arc_middlename:'',
    arc_lastname:'',
    arc_suffixname:'',
    arc_gender:'',
    arc_contact:'',
    arc_email:'',
    arc_birthday: '',
    arc_civilstatus:'',
    arc_nationality:'',

    arc_curr_home:'',
    arc_curr_region:'',
    arc_curr_province:'',
    arc_curr_city:'',
    arc_curr_barangay:'',
    arc_curr_zipcode:'',
    arc_perm_home:'',
    arc_perm_region:'',
    arc_perm_province:'',
    arc_perm_city:'',
    arc_perm_barangay:'',
    arc_perm_zipcode:'',
})

 
const booter = async() =>{
    personal.value = student.value
    updateAddress('current-province', '')
    updateAddress('current-city', '')
    updateAddress('current-barangay', '')
    updateAddress('permanent-province', '')
    updateAddress('permanent-city', '')
    updateAddress('permanent-barangay', '')
    
    personal.value.arc_curr_home = ''
    personal.value.arc_curr_region = ''
    personal.value.arc_perm_region = ''
    personal.value.arc_curr_zipcode = ''
    personal.value.arc_perm_zipcode = ''
    personal.value.arc_perm_home = ''
}

onMounted(async () => {
    var date = new Date();
    // var day = date.getDate();
    // var month = date.getMonth() + 1;
    // var year = date.getFullYear();
    yearToday.value = date.getFullYear();
    personal.value.arc_birthday = date.toISOString().split('T')[0]

    filteredCurrRegion.value = region.value
    filteredPermRegion.value = region.value

    getUserID().then((results) => {
        userID.value = results.account.data.id
    })
    
    await booter().then((results)=>{
        checking.value = false
    })
    
    
    getAge(personal.value.arc_birthday)
})

const getAge = (bday) =>{
    var date = new Date(bday);
    var bdayyear = date.getFullYear();
    age.value = yearToday.value - bdayyear
}

const copyAddress = () =>{
    if(sameAddress.value){

        updateAddress('permanent-province', personal.value.arc_curr_region)
        updateAddress('permanent-city', personal.value.arc_curr_province)
        updateAddress('permanent-barangay', personal.value.arc_curr_city)

        personal.value.arc_perm_home = personal.value.arc_curr_home
        personal.value.arc_perm_region = personal.value.arc_curr_region
        personal.value.arc_perm_province = personal.value.arc_curr_province
        personal.value.arc_perm_city = personal.value.arc_curr_city
        personal.value.arc_perm_barangay = personal.value.arc_curr_barangay
        personal.value.arc_perm_zipcode =  personal.value.arc_curr_zipcode
        
    }else{
        filteredPermProvince.value = []
        filteredPermCity.value = []
        filteredPermBarangay.value = []
        personal.value.arc_perm_home = ''
        personal.value.arc_perm_region = ''
        personal.value.arc_perm_province = ''
        personal.value.arc_perm_city = ''
        personal.value.arc_perm_barangay = ''
        personal.value.arc_perm_zipcode =  ''
    }
    
}

const updateAddress =(type, code) =>{
    switch(type){
        
        case 'current-province':
        personal.value.arc_curr_city = ''
        personal.value.arc_curr_barangay = ''
        filteredCurrCity.value = []
        filteredCurrBarangay.value = []
        filteredCurrProvince.value = province.value
        filteredCurrProvince.value = province.value.filter(e => {
                if(e.regCode == code){
                    return e
                }
            })
        break;

        case 'current-city':
        personal.value.arc_curr_barangay = ''
        filteredCurrCity.value = city.value
        filteredCurrCity.value = city.value.filter(e => {
                if(e.provCode == code){
                    return e
                }
            })
        break;

        case 'current-barangay':
        filteredCurrBarangay.value = barangay.value
        filteredCurrBarangay.value = barangay.value.filter(e => {
                if(e.citymunCode == code){
                    return e
                }
            })
        break;

        case 'permanent-province':
        personal.value.arc_perm_city = ''
        personal.value.arc_perm_barangay = ''
        filteredPermCity.value = []
        filteredPermBarangay.value = []
        filteredPermProvince.value = province.value
        filteredPermProvince.value = province.value.filter(e => {
                if(e.regCode == code){
                    return e
                }
            })
        break;

        case 'permanent-city':
        personal.value.arc_perm_barangay = ''
        filteredPermCity.value = city.value
        filteredPermCity.value = city.value.filter(e => {
                if(e.provCode == code){
                    return e
                }
            })
        break;

        case 'permanent-barangay':
        filteredPermBarangay.value = barangay.value
        filteredPermBarangay.value = barangay.value.filter(e => {
                if(e.citymunCode == code){
                    return e
                }
            })
        break;
        
        
    }
}

const editAlumni = () =>{
    let currreg = document.getElementById('presentregion')
    let currprov = document.getElementById('presentprovince')
    let currcity = document.getElementById('presentcity')
    let currbrgy = document.getElementById('presentbarangay')

    let currregtext = currreg.options[currreg.selectedIndex].text;
    let currprovtext = currprov.options[currprov.selectedIndex].text;
    let currcitytext = currcity.options[currcity.selectedIndex].text;
    let currbrgytext = currbrgy.options[currbrgy.selectedIndex].text;

    personal.value.arc_curr_region = currregtext
    personal.value.arc_curr_province = currprovtext
    personal.value.arc_curr_city = currcitytext
    personal.value.arc_curr_barangay = currbrgytext

    personal.value.arc_updatedby = userID.value
    saving.value = true
    updateArchiveDetails(personal.value).then((results)=>{
        if (results.status != 200) {
            Swal.fire({
                title: "Action Failed",
                text: "Delete failed, please try again later or contact the administrator",
                icon: "error"
            }).then(()=>{
                saving.value = false
                location.reload()
            });
        } else {
            Swal.fire({
                title: "Action Completed",
                text: "Successfully Deleted",
                icon: "success"
            }).then(()=>{
                location.reload()
            });
        }
    })
}

</script>
<template>
    <div v-if="checking">
        <Loader1></Loader1>
    </div>
    <form v-else method="post" @submit.prevent="editAlumni()" class="d-flex flex-column gap-2 small-font">
        <div class="form-group border p-4 rounded  d-flex flex-column gap-2">
            <div class="form-group border-bottom">
                <p class="fw-bold text-white bg-dark p-1 rounded-3">Personal Information</p>
            </div>
            <div class="form-group d-flex flex-column gap-2">
                <div class="row mb-2">
                    <div class="form-group col">
                        <label for="firstname">First Name</label>
                        <input type="text" onkeydown="return /[a-z'-, ]/i.test(event.key)" style="text-transform:uppercase" class="form-control" id="firstname" aria-describedby="firstname"
                            v-model="personal.arc_firstname" required :disabled="saving?true:false">
                    </div>
                    <div class="form-group col">
                        <label for="middlename">Middle Name</label>
                        <input type="text" onkeydown="return /[a-z'-, ]/i.test(event.key)" style="text-transform:uppercase" class="form-control" id="middlename" aria-describedby="middlename"
                            v-model="personal.arc_middlename" :disabled="saving?true:false">
                    </div>
                    <div class="form-group col">
                        <label for="lastname">Last Name</label>
                        <input type="text" onkeydown="return /[a-z'-, ]/i.test(event.key)" style="text-transform:uppercase" class="form-control" id="lastname" aria-describedby="lastname"
                            v-model="personal.arc_lastname" required :disabled="saving?true:false">
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="form-group col">
                        <label for="suffixname">Suffix Name</label>
                        <input type="text" onkeydown="return /[a-z'-, ]/i.test(event.key)" style="text-transform:uppercase" class="form-control" id="suffixname" aria-describedby="suffixname"
                            maxlength="3" v-model="personal.arc_suffixname" :disabled="saving?true:false">
                    </div>
                    <div class="form-group col">
                        <label for="gender">Gender</label>
                        <select class="form-control" id="gender" v-model="personal.arc_gender" required :disabled="saving?true:false">
                            <option v-for="(gen, index) in gender" :value="gen.gen_id">{{ gen.gen_desc }}</option>
                        </select>
                    </div>
                    <div class="form-group col">
                        <label for="civilstatus">Civil Status</label>
                        <select class="form-control" id="civilstatus" v-model="personal.arc_civilstatus" required :disabled="saving?true:false">
                            <option v-for="(cs, index) in civilstatus" :value="cs.cv_id">
                                {{ cs.cv_desc }}
                            </option>
                        </select>
                    </div>
                    <div class="form-group col">
                        <label for="nationality">Nationality</label>
                        <select class="form-control" id="nationality" v-model="personal.arc_nationality" required :disabled="saving?true:false">
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
                    <!-- <div class="form-group col">
                        <label for="exampleInputEmail1">Telephone No.</label>
                        <input class="form-control" type="email" name="email" id="email" placeholder="Email">
                    </div> -->
                    <!-- onkeydown="return /[0-9]/i.test(event.key)" -->
                    <div class="form-group col">
                        <label for="cellphone">Cellphone No.</label>
                        <input class="form-control" type="number" name="cellphone" id="cellphone"
                            placeholder="Cellphone No." v-model="personal.arc_contact"
                            minlength="9" maxlength="9" required :disabled="saving?true:false">
                    </div>
                    <div class="form-group col">
                        <label for="peremail">Email</label>
                        <input class="form-control" type="peremail" name="peremail" id="peremail" placeholder="Email"
                            v-model="personal.arc_email" required :disabled="saving?true:false">
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
                            v-model="personal.arc_birthday" @focusout="getAge(personal.arc_birthday)" required :disabled="saving?true:false">
                    </div>
                    <div class="form-group col">
                        <label for="age">Age</label>
                        <input type="number" class="form-control" id="age" aria-describedby="emailHelp" v-model="age"
                            disabled>
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
                        <input type="text" style="text-transform:uppercase" class="form-control" id="presenthome" aria-describedby="presenthome"
                            v-model="personal.arc_curr_home">
                    </div>
                    <div class="form-group col">
                        <label for="presentregion">Region</label>
                        <select class="form-control" id="presentregion" v-model="personal.arc_curr_region"
                            @change="updateAddress('current-province', personal.arc_curr_region)" required :disabled="saving?true:false">
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
                        <select class="form-control" id="presentprovince" v-model="personal.arc_curr_province"
                            @change="updateAddress('current-city', personal.arc_curr_province)" required :disabled="saving?true:false">
                            <option>- Select Here -</option>
                            <option v-for="(prov, index) in filteredCurrProvince" :value="prov.provCode">
                                {{ prov.provDesc }}
                            </option>
                        </select>
                    </div>
                    <div class="form-group col">
                        <label for="presentcity">City</label>
                        <select class="form-control" id="presentcity" v-model="personal.arc_curr_city"
                            @change="updateAddress('current-barangay', personal.arc_curr_city)" required :disabled="saving?true:false">
                            <option>- Select Here -</option>
                            <option v-for="(ct, index) in filteredCurrCity" :value="ct.citymunCode">
                                {{ ct.citymunDesc }}
                            </option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col">
                        <label for="presentbarangay">Barangay</label>
                        <select class="form-control" id="presentbarangay" v-model="personal.arc_curr_barangay"
                            :disabled="personal.arc_curr_city || saving ? false : true" required>
                            <option>- Select Here -</option>
                            <option v-for="(brgy, index) in filteredCurrBarangay" :value="brgy.brgyCode">
                               {{ brgy.brgyDesc }}
                            </option>
                        </select>
                    </div>
                    <div class="form-group col">
                        <label for="presentzipcode">Zip Code</label>
                        <input type="text" style="text-transform:uppercase" class="form-control" id="presentzipcode" aria-describedby="emailHelp"
                            v-model="personal.arc_curr_zipcode" :disabled="saving?true:false">
                    </div>
                </div>
            </div>
            <div class="form-group border-bottom d-flex justify-content-center align-content-center gap-2">
                <input @change="copyAddress()" type="checkbox" class="form-check-input" id="sameas" v-model="sameAddress" :disabled="saving?true:false">
                <p class="text-secondary">Permanent Address</p>
            </div>
            <div class="form-group d-flex flex-column gap-2">
                <div class="row">
                    <div class="form-group col">
                        <label for="permanenthome">Address</label>
                        <input type="text" style="text-transform:uppercase" class="form-control" id="permanenthome" aria-describedby="emailHelp"
                            v-model="personal.arc_perm_home" :disabled="saving?true:false">
                    </div>
                    <div class="form-group col">
                        <label for="permanentregion">Region</label>
                        <select class="form-control" id="permanentregion" v-model="personal.arc_perm_region"
                            @change="updateAddress('permanent-province', personal.arc_perm_region)" :disabled="saving?true:false">
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
                        <select class="form-control" id="permanentprovince" v-model="personal.arc_perm_province"
                            @change="updateAddress('permanent-city', personal.arc_perm_province)" :disabled="saving?true:false">
                            <option>- Select Here -</option>
                            <option v-for="(prov, index) in filteredPermProvince" :value="prov.provCode">
                               {{ prov.provDesc }}
                            </option>
                        </select>
                    </div>
                    <div class="form-group col">
                        <label for="permanentcity">City</label>
                        <select class="form-control" id="permanentcity" v-model="personal.arc_perm_city"
                            @change="updateAddress('permanent-barangay', personal.arc_perm_city)" :disabled="saving?true:false">
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
                        <select class="form-control" id="permanentbarangay" v-model="personal.arc_perm_barangay" :disabled="saving?true:false">
                            <option>- Select Here -</option>
                            <option v-for="(brgy, index) in filteredPermBarangay" :value="brgy.brgyCode">
                               {{ brgy.brgyDesc }}
                            </option>
                        </select>
                    </div>
                    <div class="form-group col">
                        <label for="permanentzipcode">Zip Code</label>
                        <input type="text" style="text-transform:uppercase" class="form-control" id="permanentzipcode" aria-describedby="permanentzipcode" :disabled="saving?true:false" v-model="personal.arc_perm_zipcode">
                    </div>
                </div>
            </div>
            <!-- <div class="form-group border p-4 rounded  d-flex flex-column gap-2">
                <div class="form-group border-bottom">
                    <p class="fw-bold text-white bg-dark p-1 rounded-3">Others</p>
                </div>
                <div class="form-group d-flex flex-column gap-2">
                    <div class="row mb-2">
                        <div class="form-group col">
                            <label for="bday">Date Graduated</label>
                            <input type="date" class="form-control" id="bday" aria-describedby="emailHelp"
                                v-model="personal.arc_dategraduated" required :disabled="saving?true:false">
                        </div>
                        <div class="form-group col">
                            <label for="age">Age</label>
                            <input type="number" class="form-control" id="age" aria-describedby="emailHelp" v-model="age"
                                disabled>
                        </div>
                    </div>
                </div>
            </div> -->
        </div>
        
        <div v-if="saving" class="bg-white w-100 h-100 d-flex justify-content-center align-content-center bg-opacity-75">
            <Loader1>
                <p class="fw-bold">Updating Applicant Information</p>
                <p class="fw-bold">To avoid unsuccessful results, avoid refreshing and clicking anywhere on the page. Please wait patiently.</p>
            </Loader1>
        </div>

        <div class="form-group border p-4 rounded d-flex flex-column justify-content-center gap-2 bg-secondary-subtle">
           <div class="form-group border rounded d-flex justify-content-center gap-2">
                <input type="checkbox" class="form-check-input" required :disabled="saving?true:false">
                <label class="form-check-label" for="agreeChecker">I have read and accepted the terms and
                    conditions</label>
           </div>
           <div class="form-group border rounded d-flex justify-content-center gap-2" tabindex="-1" :disabled="saving?true:false">
                <button type="submit" class="btn btn-md btn-primary w-100" tabindex="-1" :disabled="saving?true:false">
                    <i class="mr-2 fa-solid fa-plus"></i> Update Applicant
                </button>
            </div>
        </div>
    </form>
    
</template>