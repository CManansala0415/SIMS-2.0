<script setup>
import { ref, onMounted, computed } from 'vue';
import {
    getApplicant,
    addItemRequest
} from "../../Fetchers.js";
import { pdfGenerator, qrImageGenerator } from '../../Generators.js';
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
const userName = ref()
const disabler = ref(false)
const fullName = ref('')
const docStamp = ref('')
const hasDocStamp = ref(false)

const searchFname = ref('')
const searchMname = ref('')
const searchLname = ref('')
const qrimage = ref('')
const itemDesc = ref('')

const searchStudent = () => {
    itemRequested.value = 0
    requestingStud.value = 0
    fullName.value = ''

    getApplicant(0, 0, searchFname.value, searchMname.value, searchLname.value, 2).then((results) => {
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

        addItemRequest(x).then(async (results) => {
            let headers = results.header;

            if (results.status != 204) {
                await Swal.fire({
                    title: "Update Failed",
                    text: "Unknown error occured, try again later",
                    icon: "error"
                });
                location.reload();
            } else {
                const result = await Swal.fire({
                    title: "Update Successful",
                    text: "Changes applied, preparing receipt...",
                    icon: "success",
                    confirmButtonText: "Ok, Got it!"
                });

                if (result.isConfirmed) {
                    // Generate QR first
                    qrimage.value = await qrImageGenerator(headers);

                    // 🔄 Show loading Swal
                    Swal.fire({
                        title: "Generating PDF...",
                        text: "Please wait while we prepare your receipt.",
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    let name = "request";

                    // Wait until PDF is generated
                    await pdfGenerator(name, "a6", "landscape", 0.03);

                    // ⏳ Keep loader for 1s more before closing + reloading
                    setTimeout(() => {
                        Swal.close();
                        location.reload();
                    }, 1000);
                }
            }
        });

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

        let fname = results.employee.emp_firstname ? results.employee.emp_firstname : ''
        let mname = results.employee.emp_middlename ? results.employee.emp_middlename : ''
        let lname = results.employee.emp_lastname ? results.employee.emp_lastname : ''
        let sname = results.employee.emp_suffixname ? results.employee.emp_suffixname : ''

        userName.value = fname + ' ' + mname + ' ' + lname + ' ' + sname
    })
})




const itemCost = () => {
    // items.value.filter((e) => {
    //     if (itemRequested.value == e.acf_id) {
    //         return itemPrice.value = e.acf_price
    //     }
    // })
    // items.value.filter((e) => {
    //     if (itemRequested.value == e.acf_id) {
    //         return hasDocStamp.value = e.acf_docstamp == 1 ? true : false
    //     }
    // })
    let selected = items.value.find(e => e.acf_id === itemRequested.value)
    if (selected) {
        itemPrice.value = selected.acf_price
        hasDocStamp.value = selected.acf_docstamp === 1
        itemDesc.value = selected.acf_desc   // ✅ add description here
    }
}

</script>
<template>
  <div class="d-flex p-3 gap-3 align-items-stretch">

    <!-- LEFT SIDE FORM -->
    <form @submit.prevent="saveRequest"
          class="d-flex flex-column border rounded shadow-sm p-3 flex-fill">

      <p class="text-success fw-bold mb-1">Request Item</p>
      <p class="fst-italic border p-2 rounded-3 bg-secondary-subtle small-font">
        <span class="fw-bold">Note: </span>
        <span>Select the requesting student then tag the requested item.</span>
      </p>

      <!-- Search Student -->
      <div class="mt-3 mb-2 text-start d-flex gap-2">
        <div class="w-100">
          <label for="searchstudent" class="form-label">Search Student</label>
          <input type="text"
                 class="form-control form-control-sm"
                 id="searchstudent"
                 placeholder="Enter Name Here..."
                 v-model="searchFname"
                 @keyup.enter="searchStudent()"
                 onkeydown="return /[a-z, ]/i.test(event.key)">
        </div>
        <div class="align-content-end">
          <button class="btn btn-sm btn-primary" type="button" @click="searchStudent()">Search</button>
        </div>
      </div>

      <!-- Select Item -->
      <div class="mb-2 text-start">
        <label for="itemrequested" class="form-label">Select Item</label>
        <select @change="itemCost()" 
                class="form-control form-control-sm"
                v-model="itemRequested"
                id="itemrequested" required>
          <option value="0" disabled>-- Select Item --</option>
          <option v-for="(itm, index) in items" :key="itm.acf_id" :value="itm.acf_id">
            {{ itm.acf_desc }}
          </option>
        </select>
      </div>

      <!-- Doc Stamp -->
      <div v-if="hasDocStamp" class="mb-2 text-start">
        <label for="stampno" class="form-label">Document Stamp No.</label>
        <input type="number"
               class="form-control form-control-sm"
               id="stampno"
               placeholder="Enter Stamp No..."
               v-model="docStamp"
               required
               min="0">
      </div>

      <!-- Student List -->
      <div class="card mt-3 flex-fill">
        <div class="table-responsive p-2 small-font" style="height: 200px;">
          <table class="table table-hover text-uppercase">
            <thead>
              <tr><th>Select Student</th></tr>
            </thead>
            <tbody>
              <tr v-if="Object.keys(students).length"
                  v-for="(stud, index) in students"
                  :key="stud.per_id"
                  @click="setValues(stud)">
                <td :class="stud.per_id == requestingStud 
                           ? 'align-middle text-start bg-secondary text-white' 
                           : 'align-middle text-start bg-white text-black'">
                  {{ stud.per_firstname }} {{ stud.per_middlename }} {{ stud.per_lastname }} {{ stud.per_suffixname }}
                </td>
              </tr>
              <tr v-else>
                <td class="align-middle text-start">No Student Found</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Buttons -->
      <div class="d-flex flex-column gap-2 mt-3">
        <button :disabled="disabler" type="submit" class="btn btn-sm btn-success">Save Request</button>
        <button :disabled="disabler"
                type="button"
                @click="students = [], searchValue = '', itemRequested = 0, requestingStud = 0"
                class="btn btn-sm btn-warning text-white">Clear Data</button>
      </div>
    </form>

    <!-- RIGHT SIDE PREVIEW -->
    <div class="bg-secondary p-3 flex-fill d-flex justify-content-center align-items-center">
      <div class="border shadow-lg rounded bg-white w-100 d-flex justify-content-center align-items-center">
        
        <!-- Fixed A6 slip -->
        <div id="printform"
             style="width: 148mm; height: 103mm; font-family: 'Segoe UI', sans-serif; position: relative; box-sizing: border-box; border: 1px solid #ddd; padding: 12px;">
          
          <!-- Header -->
          <div class="d-flex justify-content-between align-items-start mb-3">
            <div>
              <h5 class="fw-bold mb-0">REQUEST SLIP</h5>
              <small class="text-muted">For Internal Use Only</small>
            </div>
            <div id="qrcode" v-html="qrimage"
                 style="width: 80px; height: 80px; border: 1px solid #ddd; padding: 4px; border-radius: 6px;">
            </div>
          </div>

          <!-- Body -->
          <div class="mt-3">
            <div class="row mb-2">
              <div class="col-4 fw-semibold">Requesting Party:</div>
              <div class="col text-uppercase">{{ fullName }}</div>
            </div>
            <div class="row mb-2">
              <div class="col-4 fw-semibold">Requested Item:</div>
              <div class="col text-uppercase">{{ itemDesc }}</div>
            </div>
            <div class="row mb-2">
              <div class="col-4 fw-semibold">Amount:</div>
              <div class="col text-uppercase">₱ {{ itemPrice }}</div>
            </div>
            <div class="row mb-2">
              <div class="col-4 fw-semibold">Doc Stamp:</div>
              <div class="col text-uppercase">{{ docStamp ? docStamp : 'N/A' }}</div>
            </div>
            <div class="row mb-2">
              <div class="col-4 fw-semibold">Payment Status:</div>
              <div class="col text-uppercase">
                <span class="badge bg-warning text-dark">Unpaid</span>
              </div>
            </div>
          </div>

          <!-- Footer -->
          <div class="position-absolute bottom-0 start-0 end-0 p-2 border-top d-flex justify-content-between align-items-center">
            <small class="text-muted">Prepared by: <strong>{{ userName }}</strong></small>
            <small class="text-muted">Generated on: {{ new Date().toLocaleDateString() }}</small>
          </div>
        </div>
      </div>
    </div>

  </div>
</template>
