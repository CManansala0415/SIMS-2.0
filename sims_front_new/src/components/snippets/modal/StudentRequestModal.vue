<script setup>
import { ref, onMounted, computed } from 'vue';
import {
    getApplicant,
    addItemRequest
} from "../../Fetchers.js";
import { getUserID } from "../../../routes/user";

const props = defineProps({
    feeData: {
    },
})
const items = computed(() => {
    return props.feeData
});



const students = ref([])
const searchValue = ref('')
const studentCount = ref('')
const itemRequested = ref(0)
const itemPrice = ref(0)
const requestingStud = ref('')
const userID = ref()
const disabler = ref(false)
const fullName = ref('')
const docStamp = ref('')
const hasDocStamp = ref(false)

const searchFname = ref('')
const searchMname = ref('')
const searchLname = ref('')

const searchStudent = () => {
    itemRequested.value = 0
    requestingStud.value = 0
    fullName.value = ''
    
    getApplicant(0, 0, searchFname.value, searchMname.value, searchLname.value,2).then((results) => {
        console.log(results)
        students.value = results.data
        studentCount.value = results.count
    })
}


const setValues = (stud) => {
    let fname = stud.per_firstname
    let mname = stud.per_middlename ? stud.per_middlename : ''
    let lname = stud.per_lastname
    let sname = stud.per_suffixname ? stud.per_suffixname : ''
    fullName.value = fname + ' ' + mname + ' ' + lname + ' ' + sname

    requestingStud.value = stud.per_id
    searchValue.value = fullName.value
}

const saveRequest = () => {
    if ((requestingStud.value) && (itemRequested.value)) {
        disabler.value = true
        let x = {
            acr_amount: itemPrice.value,
            acr_personid: requestingStud.value,
            acr_personname: fullName.value,
            acr_reqitem: itemRequested.value,
            acr_paystatus: 0,
            acr_addedby: userID.value,
            acr_docstamp: docStamp.value,
        }

        addItemRequest(x).then((results) => {
            if (results.status != 204) {
                // alert('Saving Failed')
                // location.reload()
                Swal.fire({
                    title: "Update Failed",
                    text: "Unknown error occured, try again later",
                    icon: "error"
                }).then(()=>{
                    location.reload()
                });
            } else {
                // alert('Saving Successful')
                // location.reload()
                Swal.fire({
                    title: "Update Successful",
                    text: "Changes applied, refreshing the page",
                    icon: "success"
                }).then(()=>{
                    location.reload()
                });
            }
        })
    } else {
        // alert('Please fillout all fields')
        Swal.fire({
            title: "Requirement",
            text: "Please fillout all fields",
            icon: "question"
        })
    }

}

onMounted(() => {
    getUserID().then((results) => {
        userID.value = results.account.data.id
    })
})


const itemCost = () => {
    items.value.filter((e) => {
        if (itemRequested.value == e.acf_id) {
            return itemPrice.value = e.acf_price 
        }
    })
    items.value.filter((e) => {
        if (itemRequested.value == e.acf_id) {
            return hasDocStamp.value = e.acf_docstamp == 1 ? true : false
        }
    })
}

</script>
<template>
    <div class="d-flex flex-column p-2 gap-2">
        <form @submit.prevent="saveRequest" class="d-flex flex-wrap flex-column">
            <p class="text-success fw-bold">Request Item</p>
            <p class="fst-italic border p-2 rounded-3 bg-secondary-subtle small-font">
                <span class="fw-bold">Note: </span>
                <span class="italic">Select the requesting student then tag the requested item.</span>
            </p>
            <div class="mt-3 mb-1 text-start d-flex gap-2">
                <div class="w-100">
                    <label for="searchstudent" class="form-label">Search Student</label>
                    <input type="text" class="form-control form-control-sm" id="searchstudent"
                        placeholder="Enter Name Here..." v-model="searchFname" @keyup.enter="searchStudent()"
                        onkeydown="return /[a-z, ]/i.test(event.key)">
                </div>
                <div class=" align-content-end">
                    <button class="btn btn-sm btn-primary" type="button" @click="searchStudent()">Search</button>
                </div>
            </div>
            <div class="mb-1 text-start">
                <label for="itemrequested" class="form-label">Select Item</label>
                <select @change="itemCost()" class="form-control form-control-sm" v-model="itemRequested" id="itemrequested" required>
                    <option value="0" disabled>-- Select Item --</option>
                    <option v-for="(itm, index) in items" :value="itm.acf_id">{{ itm.acf_desc }}</option>
                </select>
            </div>
            <div v-if="hasDocStamp" class="mb-1 text-start">
                <label for="stampno" class="form-label">Document Stamp No.</label>
                <input type="number" class="form-control form-control-sm" id="stampno"
                    placeholder="Enter Name Here..." v-model="docStamp" required
                    onkeydown="return /[a-z, ]/i.test(event.key)" min="0"
                    >
            </div>
           
            <div class="card mt-3">
                <div class="d-flex flex-column gap-2 text-start">
                        <div class="table-responsive p-2 small-font" style="height: 200px;">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Select Student</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr @click="setValues(stud)" 
                                            v-if="Object.keys(students).length" 
                                            v-for="(stud, index) in students" >
                                        <td 
                                            :class="stud.per_id == requestingStud ? 'align-middle text-start bg-secondary text-white' : 'align-middle text-start bg-white text-black'">
                                            {{ stud.per_firstname }} {{ stud.per_middlename }} {{ stud.per_lastname }}
                                            {{ stud.per_suffixname }}
                                        </td>
                                    </tr>
                                    <tr v-else>
                                        <td class="align-middle text-start">
                                            No Student Found
                                        </td>
                                    </tr>

                                </tbody>
                            </table>
                        </div>
                </div>
            </div>
            <div class="d-flex flex-column gap-1 mt-3">
                <button :disabled="disabler? true:false" type="submit" class="btn btn-sm btn-success">Save Request</button>
                <button :disabled="disabler? true:false" type="button" @click="students=[], searchValue = '', itemRequested = 0, requestingStud = 0" class="btn btn-sm btn-warning text-white">Clear Data</button>
            </div>
        </form>
    </div>
</template>