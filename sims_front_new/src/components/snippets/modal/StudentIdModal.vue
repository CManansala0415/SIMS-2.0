<script setup>
import { ref, onMounted, computed } from 'vue';
import {
    getStudentIdentification,
    addStudentIdentification
} from "../../Fetchers.js";
import { getUserID } from "../../../routes/user.js";
import Loader from '../loaders/loader1.vue';

const props = defineProps({
    studentData: {
    }
})
const studentInfo = computed(() => {
    return props.studentData
});

const customNumber = ref('')
const userID = ref('')
const saving = ref(false)
const preloading = ref(false)
const identity = ref([])
onMounted(() => {
    preloading.value = true
    getUserID().then((results) => {
        userID.value = results.data.id
        getStudentIdentification(studentInfo.value.per_id).then((results) => {
            identity.value = results
            customNumber.value = identity.value.ident_identification
            preloading.value = false
        })
    })


})


const save = () => {
    saving.value = true
    let x = {
        ident_idno: identity.value.ident_idno,
        ident_identification: customNumber.value,
        ident_personid: studentInfo.value.per_id,
        ident_addedby: userID.value
    }

    addStudentIdentification(x).then((results) => {
        if (results.status != 204) {
            alert('Update Failed')
            location.reload()
        } else {
            alert('Configuration Successful')
            location.reload()
        }
    })
}
</script>

<template>
    <div class="d-flex flex-column p-2 gap-2">
        <Loader v-if="preloading"/>
        
        <form v-else @submit.prevent="save" class="flex flex-col gap-2 overflow-auto">
            <div class="d-flex flex-column">
                <div class="d-flex flex-column">
                    <p class="fw-bold">Name</p>
                    <p class="p-2 bg-secondary-subtle">{{ studentInfo.per_firstname }} {{ studentInfo.per_middlename }} {{
                        studentInfo.per_lastname }} {{ studentInfo.per_suffixname }}</p>
                </div>
                <div class="d-flex flex-column">
                    <p class="fw-bold">Contact No.</p>
                    <p class="p-2 bg-secondary-subtle">{{ studentInfo.per_contact }}</p>
                </div>
                <div class="d-flex flex-column">
                    <p class="fw-bold">Email</p>
                    <p class="p-2 bg-secondary-subtle">{{ studentInfo.per_email }}</p>
                </div>
                <div class="d-flex flex-column border p-2">
                    <p class="fw-bold">Identification</p>
                    <input v-model="customNumber" :disabled="saving ? true : false" placeholder="Ex. 20222121" type="text"
                        class="form-control" tabindex="-1" />
                    <!-- <button type="button" :disabled="saving?true:false" class="p-2 bg-teal-500 text-white rounded-md disabled:opacity-70 disabled:cursor-not-allowed">Generate Identification</button> -->
                </div>

                <div class="flex flex-col mt-4 gap-2">
                    <p class="p-2 bg-secondary-subtle rounded-1"><span class="fw-bold">Note: </span><span
                            class="italic">Once identification is generated, it is linked to all records in the system.
                            it cannot be modified and it is for one time generation only.
                        </span></p>
                    <button type="submit" :disabled="saving ? true : false"
                        class="btn btn-sm btn-primary w-100">Save
                        Identification</button>
                </div>
            </div>
        </form>
    </div>
</template>