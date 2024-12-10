<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { addApplicant, getPerson, getFamily, getAward, getAttainment, updateApplicant, updatePersonDetails, deleteFamAwrAtt } from "../../Fetchers.js";
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
const familyMembers = ref([]);
const awardList = ref([]);
const attainmentList = ref([]);
const age = ref(0)
const yearToday = ref(0)
const saving = ref(false);
const checking = ref(false);
const forUpdate = ref(false) 

const filteredBirthProvince = ref([])
const filteredBirthCity = ref([])

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
    per_firstname:'',
    per_middlename:'',
    per_lastname:'',
    per_suffixname:'',
    per_gender:'',
    per_contact:'',
    per_email:'',
    per_birthday: '',
    per_birth_province:'',
    per_birth_city:'',
    per_birth_zipcode:'',
    per_civilstatus:'',
    per_nationality:'',

    per_curr_home:'',
    per_curr_region:'',
    per_curr_province:'',
    per_curr_city:'',
    per_curr_barangay:'',
    per_curr_zipcode:'',
    per_perm_home:'',
    per_perm_region:'',
    per_perm_province:'',
    per_perm_city:'',
    per_perm_barangay:'',
    per_perm_zipcode:'',
})

const famMembersInfo = ref({
    fam_firstname:'',
    fam_middlename:'',
    fam_lastname:'',
    fam_suffixname:'',
    fam_relationship:'',
    fam_contact:'',
    fam_email:'',
})

const awardInfo = ref({
    awr_desc:'',
    awr_year:'',
})

const attainmentInfo = ref({
    pered_school:'',
    pered_from:'',
    pered_to:'',
})

const booter = async() =>{
    await getPerson(personID.value).then(async(results) => {
        if(results){
            updateAddress('birth-city', results.per_birth_province)
            updateAddress('current-province', results.per_curr_region)
            updateAddress('current-city', results.per_curr_province)
            updateAddress('current-barangay', results.per_curr_city)
            updateAddress('permanent-province', results.per_perm_region)
            updateAddress('permanent-city', results.per_perm_province)
            updateAddress('permanent-barangay', results.per_perm_city)
            personal.value = results
            personal.value.per_id? forUpdate.value = true:forUpdate.value = false
        }

        await getFamily(results.per_famid).then((results) => {
            if(results){
                familyMembers.value = results
            }
        })
        await getAward(results.per_educid).then((results) => {
            if(results){
                awardList.value = results
            }
        })
        await getAttainment(results.per_attainmentid).then((results) => {
            if(results){
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


    filteredBirthProvince.value = province.value
    filteredCurrRegion.value = region.value
    filteredPermRegion.value = region.value

    getUserID().then((results) => {
        userID.value = results.data.id
    })
    
    checking.value = true
    if(personID.value){
        await booter().then((results)=>{
            checking.value = false
        })
    }else{
        checking.value = false
    }
    
    getAge(personal.value.per_birthday)
})

const getAge = (bday) =>{
    var date = new Date(bday);
    var bdayyear = date.getFullYear();
    age.value = yearToday.value - bdayyear
}

const copyAddress = () =>{
    if(sameAddress.value){


        updateAddress('permanent-province', personal.value.per_curr_region)
        updateAddress('permanent-city', personal.value.per_curr_province)
        updateAddress('permanent-barangay', personal.value.per_curr_city)

        personal.value.per_perm_home = personal.value.per_curr_home
        personal.value.per_perm_region = personal.value.per_curr_region
        personal.value.per_perm_province = personal.value.per_curr_province
        personal.value.per_perm_city = personal.value.per_curr_city
        personal.value.per_perm_barangay = personal.value.per_curr_barangay
        personal.value.per_perm_zipcode =  personal.value.per_curr_zipcode
        
    }else{
        filteredPermProvince.value = []
        filteredPermCity.value = []
        filteredPermBarangay.value = []
        personal.value.per_perm_home = ''
        personal.value.per_perm_region = ''
        personal.value.per_perm_province = ''
        personal.value.per_perm_city = ''
        personal.value.per_perm_barangay = ''
        personal.value.per_perm_zipcode =  ''
    }
    
}

const addFamMembers = (type, data, index) =>{
    if(type == 'add'){
        let convert = {...data}
        if(
            (!convert.fam_firstname)||
            (!convert.fam_lastname)||
            (!convert.fam_relationship)||
            (!convert.fam_contact) ){
                alert('Please fillout fields of a family member')
        }else{
            familyMembers.value.push(convert)
            famMembersInfo.value = {
                fam_firstname:'',
                fam_middlename:'',
                fam_lastname:'',
                fam_suffixname:'',
                fam_relationship:'',
                fam_contact:'',
                fam_email:'',
            }
        }
    }
    if(type == 'remove'){
        if(data.fam_id){
            if (confirm("Are you sure you want to delete this item? this action cannot be reverted.") == true) {
                let del = {
                    id:data.fam_id
                }
                deleteFamAwrAtt(del, 1).then((results)=>{
                    alert('Successfully Removed')
                    familyMembers.value.splice(index,1)
                })

            } else {
                return false;
            }
            
        }else{
            familyMembers.value.splice(index,1)
        }   
    }
}

const addAwards = (type, data, index) =>{
    if(type == 'add'){
        let convert = {...data}
        if(
            (!convert.awr_desc)||
            (!convert.awr_year)
          ){
                alert('Please fillout fields for the award')
        }else{
            awardList.value.push(convert)
            awardInfo.value = {
                awr_desc:'',
                awr_year:'',
            }
        }
        
    }
    if(type == 'remove'){
        if(data.awr_id){
            if (confirm("Are you sure you want to delete this item? this action cannot be reverted.") == true) {
                let del = {
                    id:data.awr_id
                }
                deleteFamAwrAtt(del, 2).then((results)=>{
                    alert('Successfully Removed')
                    awardList.value.splice(index,1)
                })

            } else {
                return false;
            }
            
        }else{
            awardList.value.splice(index,1)
        }   
        
    }
}

const addAttainment = (type, data, index) =>{
    if(type == 'add'){
        let convert = {...data}
        if(
            (!convert.pered_school)||
            (!convert.pered_from)||
            (!convert.pered_to)
          ){
                alert('Please fillout fields for educational background')
        }else{
            attainmentList.value.push(convert)
            attainmentInfo.value = {
                pered_school:'',
                pered_from:'',
                pered_to:'',
            }
        }
    }
    if(type == 'remove'){
        console.log(data)
        if(data.pered_id){
            if (confirm("Are you sure you want to delete this item? this action cannot be reverted.") == true) {
                let del = {
                    id:data.pered_id
                }
                deleteFamAwrAtt(del, 3).then((results)=>{
                    alert('Successfully Removed')
                    attainmentList.value.splice(index,1)
                })

            } else {
                return false;
            }
            
        }else{
            attainmentList.value.splice(index,1)
        }   
        
    }
}

const updateAddress =(type, code) =>{
    switch(type){
        case 'birth-city':
        filteredBirthCity.value = city.value
        filteredBirthCity.value = city.value.filter(e => {
                if(e.provCode == code){
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
                if(e.regCode == code){
                    return e
                }
            })
        break;

        case 'current-city':
        personal.value.per_curr_barangay = ''
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
        personal.value.per_perm_city = ''
        personal.value.per_perm_barangay = ''
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
        personal.value.per_perm_barangay = ''
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


const clearFields = () =>{
    personal.value = []
    familyMembers.value = []
    awardList.value = []
    attainmentList.value = []
    famMembersInfo.value = []
    awardInfo.value = []
    attainmentInfo.value = []
    sameAddress.value=false
    alert('Sheet Cleared')
}



const percounter = ref(0)
const famcounter = ref(0)
const awrcounter = ref(0)
const attcounter = ref(0)
const registerApplicant = () =>{
    

    let pers = {
        ...personal.value,
        per_user: userID.value
    }

    let fam = JSON.parse(JSON.stringify(familyMembers.value))
    let awr = JSON.parse(JSON.stringify(awardList.value))
    let att = JSON.parse(JSON.stringify(attainmentList.value))

    if(forUpdate.value){

        saving.value=true
        //person update
        updateApplicant(pers, 1).then((results)=>{
            percounter.value+=1

             //family update
            fam.forEach(async (items) => {
                let famdata = {
                    ...items,
                    fam_id: items.fam_id,
                    fam_personid: pers.per_famid,
                }
                updatePersonDetails(famdata, 1).then((results)=>{
                    famcounter.value+=1
                })
            })
            //award update
            awr.forEach(async (items) => {
                let awrdata = {
                    ...items,
                    awr_id: items.awr_id,
                    awr_personid: pers.per_educid,
                }
                updatePersonDetails(awrdata, 2).then((results)=>{
                    awrcounter.value+=1
                })
            })
            //attainment update
            att.forEach(async (items) => {
                let attdata = {
                    ...items,
                    pered_id: items.pered_id,
                    pered_personid: pers.per_attainmentid,
                }
                updatePersonDetails(attdata, 3).then((results)=>{
                    attcounter.value+=1
                })
            })
        })



    }else{

        saving.value=true
        //person registry
        addApplicant(pers, 1).then((results)=>{
            percounter.value+=1

            //family registry
            fam.forEach(async (items) => {
                
                let famdata = {
                    ...items,
                    fam_personid: results.fam_id,
                    fam_user: userID.value
                }
                addApplicant(famdata, 2).then((results)=>{
                    famcounter.value+=1
                })
            })
            //award registry
            awr.forEach(async (items) => {
                let awrdata = {
                    ...items,
                    awr_personid: results.educ_id,
                    awr_user: userID.value
                }
                addApplicant(awrdata, 3).then((results)=>{
                    awrcounter.value+=1
                })
            })
            //atainment registry
            att.forEach(async (items) => {
                let attdata = {
                    ...items,
                    pered_personid: results.att_id,
                    pered_user: userID.value
                }
                addApplicant(attdata, 4).then((results)=>{
                    attcounter.value+=1
                })
            })
            
        })
    }
}



const refresh = () => {
    location.reload()
}
</script>
<template>
    <div v-if="checking">
        <Loader1></Loader1>
    </div>
    <form v-else method="post" @submit.prevent="registerApplicant()" class="d-flex flex-column gap-2 small-font">
        <div class="form-group border p-4 rounded  d-flex flex-column gap-2">
            <div class="form-group border-bottom">
                <p class="fw-bold text-white bg-dark p-1 rounded-3">Personal Information</p>
            </div>
            <div class="form-group d-flex flex-column gap-2">
                <div class="row mb-2">
                    <div class="form-group col">
                        <label for="firstname">First Name</label>
                        <input type="text" class="form-control" id="firstname" aria-describedby="firstname"
                            v-model="personal.per_firstname" required :disabled="saving?true:false">
                    </div>
                    <div class="form-group col">
                        <label for="middlename">Middle Name</label>
                        <input type="text" class="form-control" id="middlename" aria-describedby="middlename"
                            v-model="personal.per_middlename" :disabled="saving?true:false">
                    </div>
                    <div class="form-group col">
                        <label for="lastname">Last Name</label>
                        <input type="text" class="form-control" id="lastname" aria-describedby="lastname"
                            v-model="personal.per_lastname" required :disabled="saving?true:false">
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="form-group col">
                        <label for="suffixname">Suffix Name</label>
                        <input type="text" class="form-control" id="suffixname" aria-describedby="suffixname"
                            maxlength="3" v-model="personal.per_suffixname" :disabled="saving?true:false">
                    </div>
                    <div class="form-group col">
                        <label for="gender">Gender</label>
                        <select class="form-control" id="gender" v-model="personal.per_gender" required :disabled="saving?true:false">
                            <option v-for="(gen, index) in gender" :value="gen.gen_id">{{ gen.gen_desc }}</option>
                        </select>
                    </div>
                    <div class="form-group col">
                        <label for="civilstatus">Civil Status</label>
                        <select class="form-control" id="civilstatus" v-model="personal.per_civilstatus" required :disabled="saving?true:false">
                            <option v-for="(cs, index) in civilstatus" :value="cs.cv_id">
                                {{ cs.cv_desc }}
                            </option>
                        </select>
                    </div>
                    <div class="form-group col">
                        <label for="nationality">Nationality</label>
                        <select class="form-control" id="nationality" v-model="personal.per_nationality" required :disabled="saving?true:false">
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
                    <div class="form-group col">
                        <label for="cellphone">Cellphone No.</label>
                        <input class="form-control" type="text" name="cellphone" id="cellphone"
                            placeholder="Cellphone No." v-model="personal.per_contact"
                            onkeydown="return /[0-9]/i.test(event.key)" minlength="9" maxlength="9" required :disabled="saving?true:false">
                    </div>
                    <div class="form-group col">
                        <label for="peremail">Email</label>
                        <input class="form-control" type="peremail" name="peremail" id="peremail" placeholder="Email"
                            v-model="personal.per_email" required :disabled="saving?true:false">
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
                            v-model="personal.per_birthday" @focusout="getAge(personal.per_birthday)" required :disabled="saving?true:false">
                    </div>
                    <div class="form-group col">
                        <label for="age">Age</label>
                        <input type="number" class="form-control" id="age" aria-describedby="emailHelp" v-model="age"
                            disabled>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="form-group col">
                        <label for="birthprovince">Birth Province</label>
                        <select class="form-control" id="birthprovince"
                            @change="updateAddress('birth-city', personal.per_birth_province)"
                            v-model="personal.per_birth_province" required :disabled="saving?true:false">
                            <option>- Select Here -</option>
                            <option v-for="(prov, index) in filteredBirthProvince" :value="prov.provCode">{{
                                prov.provDesc }}</option>
                        </select>
                    </div>
                    <div class="form-group col">
                        <label for="birthcity">Birth City</label>
                        <select class="form-control" id="birthcity" v-model="personal.per_birth_city"
                            :disabled="personal.per_birth_province || saving ? false : true" required>
                            <option>- Select Here -</option>
                            <option v-for="(ct, index) in filteredBirthCity" :value="ct.citymunCode">{{ ct.citymunDesc
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
                        <input type="text" class="form-control" id="presenthome" aria-describedby="presenthome"
                            v-model="personal.per_curr_home">
                    </div>
                    <div class="form-group col">
                        <label for="presentregion">Region</label>
                        <select class="form-control" id="presentregion" v-model="personal.per_curr_region"
                            @change="updateAddress('current-province', personal.per_curr_region)" required :disabled="saving?true:false">
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
                        <select class="form-control" id="presentprovince" v-model="personal.per_curr_province"
                            @change="updateAddress('current-city', personal.per_curr_province)" required :disabled="saving?true:false">
                            <option>- Select Here -</option>
                            <option v-for="(prov, index) in filteredCurrProvince" :value="prov.provCode">
                                {{ prov.provDesc }}
                            </option>
                        </select>
                    </div>
                    <div class="form-group col">
                        <label for="presentcity">City</label>
                        <select class="form-control" id="presentcity" v-model="personal.per_curr_city"
                            @change="updateAddress('current-barangay', personal.per_curr_city)" required :disabled="saving?true:false">
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
                        <select class="form-control" id="preentbarangay" v-model="personal.per_curr_barangay"
                            :disabled="personal.per_curr_city || saving ? false : true" required>
                            <option>- Select Here -</option>
                            <option v-for="(brgy, index) in filteredCurrBarangay" :value="brgy.brgyCode">
                               {{ brgy.brgyDesc }}
                            </option>
                        </select>
                    </div>
                    <div class="form-group col">
                        <label for="presentzipcode">Zip Code</label>
                        <input type="text" class="form-control" id="presentzipcode" aria-describedby="emailHelp"
                            v-model="personal.per_curr_zipcode" :disabled="saving?true:false">
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
                        <input type="text" class="form-control" id="permanenthome" aria-describedby="emailHelp"
                            v-model="personal.per_perm_home" :disabled="saving?true:false">
                    </div>
                    <div class="form-group col">
                        <label for="permanentregion">Region</label>
                        <select class="form-control" id="permanentregion" v-model="personal.per_perm_region"
                            @change="updateAddress('permanent-province', personal.per_perm_region)" :disabled="saving?true:false">
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
                        <select class="form-control" id="permanentprovince" v-model="personal.per_perm_province"
                            @change="updateAddress('permanent-city', personal.per_perm_province)" :disabled="saving?true:false">
                            <option>- Select Here -</option>
                            <option v-for="(prov, index) in filteredPermProvince" :value="prov.provCode">
                               {{ prov.provDesc }}
                            </option>
                        </select>
                    </div>
                    <div class="form-group col">
                        <label for="permanentcity">City</label>
                        <select class="form-control" id="permanentcity" v-model="personal.per_perm_city"
                            @change="updateAddress('permanent-barangay', personal.per_perm_city)" :disabled="saving?true:false">
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
                        <select class="form-control" id="permanentbarangay" v-model="personal.per_perm_barangay" :disabled="saving?true:false">
                            <option>- Select Here -</option>
                            <option v-for="(brgy, index) in filteredPermBarangay" :value="brgy.brgyCode">
                               {{ brgy.brgyDesc }}
                            </option>
                        </select>
                    </div>
                    <div class="form-group col">
                        <label for="permanentzipcode">Zip Code</label>
                        <input type="text" class="form-control" id="permanentzipcode" aria-describedby="permanentzipcode" :disabled="saving?true:false" v-model="personal.per_perm_zipcode">
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group border p-4 rounded  d-flex flex-column gap-2">
            <div class="form-group border-bottom">
                <p class="fw-bold text-white bg-dark p-1 rounded-3">Educational Background Information</p>
            </div>
            <div class="form-group d-flex flex-wrap">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th scope="col">School</th>
                            <th scope="col">From</th>
                            <th scope="col">To</th>
                            <th scope="col" class="w-25">Command</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(attain, index) in attainmentList">
                            <th scope="row">{{ attain.pered_school }}</th>
                            <td>{{ attain.pered_from }}</td>
                            <td>{{ attain.pered_to }}</td>
                            <td>
                                <button @click="addAttainment('remove', attain, index)" type="button"
                                    class="btn btn-sm btn-danger" :disabled="saving?true:false">
                                    Remove
                                </button>
                            </td>
                        </tr>
                        <tr v-if="!Object.keys(attainmentList).length">
                            <td colspan="4">Empty List</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="form-group col">
                        <label for="edschoolname">School</label>
                        <input type="text" class="form-control" id="edschoolname" aria-describedby="emailHelp"
                            v-model="attainmentInfo.pered_school" :disabled="saving?true:false">
                    </div>
                    <div class="form-group col">
                        <label for="edyearfrom">Year From</label>
                        <input type="text" class="form-control" id="edyearfrom" aria-describedby="emailHelp"
                            v-model="attainmentInfo.pered_from" onkeydown="return /[0-9]/i.test(event.key)"
                            maxlength="4" minlength="4" :disabled="saving?true:false">
                    </div>
                    <div class="form-group col">
                        <label for="edyearto">Year To</label>
                        <input type="text" class="form-control" id="edyearto" aria-describedby="emailHelp"
                            v-model="attainmentInfo.pered_to" onkeydown="return /[0-9]/i.test(event.key)" maxlength="4"
                            minlength="4" :disabled="saving?true:false">
                    </div>
                </div>
                <div class="form-group d-flex justify-content-center mt-3  bg-secondary-subtle p-2">
                    <button @click="addAttainment('add', attainmentInfo)" class="btn btn-primary w-25" type="button"
                        id="submit_data" name="submit_data" :disabled="saving?true:false">Add</button>
                </div>
            </div>

        </div>
        <div class="form-group border p-4 rounded  d-flex flex-column gap-2">
            <div class="form-group border-bottom">
                <p class="fw-bold text-white bg-dark p-1 rounded-3">Family Background Information</p>
            </div>
            <div class="form-group d-flex flex-wrap">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th scope="col">Name</th>
                            <th scope="col">Relationship</th>
                            <th scope="col">Contact No.</th>
                            <th scope="col">Email</th>
                            <th scope="col" class="w-25">Command</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(fam, index) in familyMembers">
                            <th scope="row">{{ fam.fam_firstname }} {{ fam.fam_middlename }} {{ fam.fam_lastname }} {{
                                fam.fam_suffixname }}</th>
                            <td>{{ fam.fam_relationship }}</td>
                            <td>{{ fam.fam_contact }}</td>
                            <td>{{ fam.fam_email }}</td>
                            <td>
                                <button @click="addFamMembers('remove', fam, index)" type="button"
                                    class="btn btn-sm btn-danger" :disabled="saving?true:false">
                                    Remove
                                </button>
                            </td>
                        </tr>
                        <tr v-if="!Object.keys(familyMembers).length">
                            <td colspan="5">Empty List</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="form-group d-flex flex-column">
                <div class="row">
                    <div class="form-group col">
                        <label for="famfirstname">First Name</label>
                        <input type="text" class="form-control" id="famfirstname" aria-describedby="famfirstname"
                            v-model="famMembersInfo.fam_firstname" onkeydown="return /[a-z, ]/i.test(event.key)" :disabled="saving?true:false">
                    </div>
                    <div class="form-group col">
                        <label for="fammiddlename">Middle Name</label>
                        <input type="text" class="form-control" id="fammiddlename" aria-describedby="fammiddlename"
                            v-model="famMembersInfo.fam_middlename" onkeydown="return /[a-z, ]/i.test(event.key)" :disabled="saving?true:false">
                    </div>
                    <div class="form-group col">
                        <label for="famlastname">Last Name</label>
                        <input type="text" class="form-control" id="famlastname" aria-describedby="famlastname"
                            v-model="famMembersInfo.fam_lastname" onkeydown="return /[a-z, ]/i.test(event.key)" :disabled="saving?true:false">
                    </div>
                    <div class="form-group col">
                        <label for="famsuffixname">Suffix Name</label>
                        <input type="text" class="form-control" id="famsuffixname" aria-describedby="famsuffixname"
                            v-model="famMembersInfo.fam_suffixname" onkeydown="return /[a-z, ]/i.test(event.key)" :disabled="saving?true:false">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col">
                        <label for="famrelationship">Relationship</label>
                        <input type="text" class="form-control" id="famrelationship" aria-describedby="famrelationship"
                            v-model="famMembersInfo.fam_relationship" onkeydown="return /[a-z, ]/i.test(event.key)" :disabled="saving?true:false">
                    </div>
                    <div class="form-group col">
                        <label for="famcontactno">Contact No.</label>
                        <input type="text" class="form-control" id="famcontactno" aria-describedby="famcontactno"
                            v-model="famMembersInfo.fam_contact" onkeydown="return /[0-9]/i.test(event.key)"
                            minlength="9" maxlength="9" :disabled="saving?true:false">
                    </div>
                    <div class="form-group col">
                        <label for="famemail">Email</label>
                        <input type="email" class="form-control" id="famemail" aria-describedby="famemail"
                            v-model="famMembersInfo.fam_email" :disabled="saving?true:false">
                    </div>
                </div>
                <div class="form-group d-flex justify-content-center mt-3  bg-secondary-subtle p-2">
                    <button @click="addFamMembers('add', famMembersInfo)" class="btn btn-primary w-25" type="button"
                        id="submit_data" name="submit_data" :disabled="saving?true:false">Add</button>
                </div>
            </div>

        </div>
        <div class="form-group border p-4 rounded  d-flex flex-column gap-2">
            <div class="form-group border-bottom">
                <p class="fw-bold text-white bg-dark p-1 rounded-3">School Awards Information</p>
            </div>
            <div class="form-group d-flex flex-wrap">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th scope="col">Title</th>
                            <th scope="col">Year</th>
                            <th scope="col" class="w-25">Command</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(award, index) in awardList">
                            <th scope="row">{{ award.awr_desc }}</th>
                            <td>{{ award.awr_year }}</td>
                            <td>
                                <button @click="addAwards('remove',award ,index)" type="button"
                                    class="btn btn-sm btn-danger" :disabled="saving?true:false">
                                    Remove
                                </button>
                            </td>
                        </tr>
                        <tr v-if="!Object.keys(awardList).length">
                            <td colspan="3">Empty List</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="form-group">
                <div class="row w-100">
                    <div class="form-group col">
                        <label for="awardtitle">Title</label>
                        <input type="email" class="form-control" id="awardtitle" aria-describedby="awardtitle"
                            v-model="awardInfo.awr_desc" :disabled="saving?true:false">
                    </div>
                    <div class="form-group col">
                        <label for="awardyear">Year Acquired</label>
                        <input type="text" class="form-control" id="awardyear" aria-describedby="awardyear"
                            v-model="awardInfo.awr_year" onkeydown="return /[0-9]/i.test(event.key)" maxlength="4"
                            minlength="4" :disabled="saving?true:false">
                    </div>
                </div>
                <div class="form-group d-flex justify-content-center mt-3  bg-secondary-subtle p-2">
                    <button @click="addAwards('add', awardInfo)" class="btn btn-primary w-25" type="button"
                        id="submit_data" name="submit_data" :disabled="saving?true:false">Add</button>
                </div>
            </div>

        </div>
        <div v-if="saving" class="bg-white w-100 h-100 d-flex justify-content-center align-content-center bg-opacity-75">
            <div v-if=" (percounter == 1) &&
                        (famcounter == Object.keys(familyMembers).length) &&
                        (awrcounter == Object.keys(awardList).length) &&
                        (attcounter == Object.keys(attainmentList).length)" class="py-3 text-center">
                        <div class="alert alert-success" role="alert">
                            <h4 class="alert-heading">Well done!</h4>
                            <p>You have successfully made changes to this record. Click now refresh to retrieve changes.</p>
                            <hr>
                            <button class="btn btn-sm btn-success" @click="refresh()">Refresh</button>
                        </div>
            </div>
            <div v-else class="py-3 text-center">
                <div class="alert alert-dark" role="alert">
                    <Loader>
                        <p class="fw-bold">Registering Applicant Information</p>
                        <p class="fw-bold">Saving personal details, generating identity</p>
                    </Loader>
                    <p class="fw-regular">Registering Person <span class="fw-bold">{{ percounter }} out of 1</span></p>
                    <p class="fw-regular">Updating Family <span class="fw-bold">{{ famcounter }} out of {{ Object.keys(familyMembers).length }}</span></p>
                    <p class="fw-regular">Listing Awards <span class="fw-bold">{{ awrcounter }} out of {{ Object.keys(awardList).length }}</span></p>
                    <p class="fw-regular">Listing Educational Attainments <span class="fw-bold">{{ attcounter }} out of {{ Object.keys(attainmentList).length }}</span></p>
                </div>
            </div>
        </div>
        <div class="form-group border p-4 rounded d-flex flex-column justify-content-center gap-2 bg-secondary-subtle">
           <div class="form-group border rounded d-flex justify-content-center gap-2">
                <input type="checkbox" class="form-check-input" required :disabled="saving?true:false">
                <label class="form-check-label" for="agreeChecker">I have read and accepted the terms and
                    conditions</label>
           </div>
           <div class="form-group border rounded d-flex justify-content-center gap-2" tabindex="-1" :disabled="saving?true:false">
                <button @click="clearFields" type="button" class="btn btn-md btn-secondary w-100">
                    <i class="mr-2 fa-solid fa-rotate-left"></i> Reset Data
                </button>
                <button v-if="!forUpdate" type="submit" class="btn btn-md btn-success w-100" tabindex="-1" :disabled="saving?true:false">
                    <i class="mr-2 fa-solid fa-plus"></i> Register Applicant
                </button>
                <button v-else type="submit" class="btn btn-md btn-primary w-100" tabindex="-1" :disabled="saving?true:false">
                    <i class="mr-2 fa-solid fa-plus"></i> Update Applicant
                </button>
            </div>
        </div>

       
    </form>
    

</template>