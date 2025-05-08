<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { addEmployee,updateEmployee } from "../../Fetchers.js";
import Loader1 from '../loaders/Loader1.vue';

const props = defineProps({
    useriddata: {
    },
    genderData: {
    },
    civilstatusData: {
    },
    employeeData: {
    },
    toUpdate: {
        type: Boolean,
        default: false,
    },
    
})

const userID = computed(() => {
    return props.useriddata
});
const gender = computed(() => {
    return props.genderData
});
const civilstatus = computed(() => {
    return props.civilstatusData
});
const employee = computed(() => {
    return props.employeeData
});
const update = computed(() => {
    return props.toUpdate
});

const age = ref(0)
const yearToday = ref(0)
const saving = ref(false)
const checking = ref(false)

const personal = ref({
    emp_id: '',
    emp_firstname: '',
    emp_middlename: '',
    emp_lastname: '',
    emp_suffixname: '',
    emp_gender: '',
    emp_civilstatus: '',
    emp_contact: '',
    emp_email: '',
    emp_birthday: '',
    emp_status: '',

})

onMounted(async () => {
    var date = new Date();
    yearToday.value = date.getFullYear();

    if(employee.value){
        personal.value.emp_id = employee.value.emp_id
        personal.value.emp_firstname = employee.value.emp_firstname
        personal.value.emp_middlename = employee.value.emp_middlename
        personal.value.emp_lastname = employee.value.emp_lastname
        personal.value.emp_suffixname = employee.value.emp_suffixname
        personal.value.emp_gender = employee.value.emp_gender
        personal.value.emp_civilstatus = employee.value.emp_civilstatus
        personal.value.emp_contact = employee.value.emp_contact
        personal.value.emp_email = employee.value.emp_email
        personal.value.emp_birthday = employee.value.emp_birthday
        personal.value.emp_status = employee.value.emp_status
    }

})

const getAge = (bday) => {
    var date = new Date(bday);
    var bdayyear = date.getFullYear();
    age.value = yearToday.value - bdayyear
}

const clearFields = () => {
    personal.value = []
    // alert('Sheet Cleared')
    Swal.fire({
        title: "Cleared",
        text: "Sheet Cleared",
        icon: "success"
    })
}

const registerApplicant = () => {
    let pers = {
        ...personal.value,
        emp_addedby: userID.value
    }

    saving.value = true
    if(update.value == false){
        addEmployee(pers, 1).then((results) => {
            // alert('Employee Registered')
            // location.reload()
            Swal.fire({
                title: "Employee Registered",
                text: "Changes applied, refreshing the page",
                icon: "success"
            }).then(()=>{
                location.reload()
            });
        })
    }else{

        updateEmployee(pers, 1).then((results)=>{
            // alert('Employee Updated')
            // location.reload()
            Swal.fire({
                title: "Employee Updated",
                text: "Changes applied, refreshing the page",
                icon: "success"
            }).then(()=>{
                location.reload()
            });
        })
    }
   
}

const save = () => {
    location.reload()
}


</script>
<template>
    <div v-if="checking">
        <Loader1></Loader1>
    </div>
    <form v-else method="post" @submit.prevent="registerApplicant()" class="d-flex flex-column gap-2">
        <div class="form-group border p-4 rounded  d-flex flex-column gap-2">
            <div class="form-group border-bottom">
                <p class="fw-bold text-white bg-dark p-1 rounded-3">Personal Information</p>
            </div>
            <div class="form-group d-flex flex-column gap-2">
                <div class="row mb-2">
                    <div class="form-group col">
                        <label for="firstname">First Name</label>
                        <input type="text" style="text-transform:uppercase" class="form-control" id="firstname" aria-describedby="firstname"
                            v-model="personal.emp_firstname" required :disabled="saving ? true : false">
                    </div>
                    <div class="form-group col">
                        <label for="middlename">Middle Name</label>
                        <input type="text" style="text-transform:uppercase" class="form-control" id="middlename" aria-describedby="middlename"
                            v-model="personal.emp_middlename" :disabled="saving ? true : false">
                    </div>
                    <div class="form-group col">
                        <label for="lastname">Last Name</label>
                        <input type="text" style="text-transform:uppercase" class="form-control" id="lastname" aria-describedby="lastname"
                            v-model="personal.emp_lastname" required :disabled="saving ? true : false">
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="form-group col">
                        <label for="suffixname">Suffix Name</label>
                        <input type="text" style="text-transform:uppercase" class="form-control" id="suffixname" aria-describedby="suffixname"
                            maxlength="3" v-model="personal.emp_suffixname" :disabled="saving ? true : false">
                    </div>
                    <div class="form-group col">
                        <label for="gender">Gender</label>
                        <select class="form-control" id="gender" v-model="personal.emp_gender" required
                            :disabled="saving ? true : false">
                            <option v-for="(gen, index) in gender" :value="gen.gen_id">{{ gen.gen_desc }}</option>
                        </select>
                    </div>
                    <div class="form-group col">
                        <label for="civilstatus">Civil Status</label>
                        <select class="form-control" id="civilstatus" v-model="personal.emp_civilstatus" required
                            :disabled="saving ? true : false">
                            <option v-for="(cs, index) in civilstatus" :value="cs.cv_id">
                                {{ cs.cv_desc }}
                            </option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group border-bottom">
                <p class="fw-bold text-white bg-dark p-1 rounded-3">Contact Information</p>
            </div>
            <div class="form-group d-flex flex-column gap-2">
                <div class="row mb-2">
                    <div class="form-group col">
                        <label for="cellphone">Cellphone No.</label>
                        <input class="form-control" type="text" name="cellphone" id="cellphone"
                            placeholder="Cellphone No." v-model="personal.emp_contact"
                            onkeydown="return /[0-9]/i.test(event.key)" minlength="9" maxlength="9" 
                            :disabled="saving ? true : false">
                    </div>
                    <div class="form-group col">
                        <label for="peremail">Email</label>
                        <input class="form-control" type="peremail" name="peremail" id="peremail" placeholder="Email"
                            v-model="personal.emp_email"  :disabled="saving ? true : false">
                    </div>

                </div>
                <div class="row mb-2">
                    <div class="form-group col">
                        <label for="bday">Birthday</label>
                        <input type="date" class="form-control" id="bday" aria-describedby="emailHelp"
                            v-model="personal.emp_birthday" @focusout="getAge(personal.emp_birthday)" required
                            :disabled="saving ? true : false">
                    </div>
                    <div class="form-group col">
                        <label for="age">Age</label>
                        <input type="number" class="form-control" id="age" aria-describedby="emailHelp" v-model="age"
                            disabled>
                    </div>
                </div>
                <div
                    class="form-group border p-4 rounded d-flex flex-column justify-content-center gap-2 bg-secondary-subtle">
                    <div class="form-group border rounded d-flex justify-content-center gap-2">
                        <input type="checkbox" class="form-check-input" required :disabled="saving ? true : false">
                        <label class="form-check-label" for="agreeChecker">I have read and accepted the terms and
                            conditions</label>
                    </div>
                    <div class="form-group border rounded d-flex justify-content-end gap-2" tabindex="-1">
                        <button type="submit" class="btn btn-md btn-success w-100" tabindex="-1" :disabled="saving">
                            <i class="mr-2 fa-solid fa-plus"></i> Register Employee
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</template>