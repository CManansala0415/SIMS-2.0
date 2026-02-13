
<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import NeuLoader2 from '../loaders/NeuLoader2.vue';
import {
    getCommandUsers,
    employeeAccountTag,
    getEmployeeAccount
    
} from "../../Fetchers.js";

const props = defineProps({
    employeeData: {
    },
    userId:{
    }
})

const employee = computed(() => {
    return props.employeeData
});

const userID = computed(() => {
    return props.userId
});

const accounts = ref([])
const fullName = ref('')
const empId = ref('')
const accountId = ref(0)
const preLoading = ref(true)
onMounted(async () => {

    let fname = employee.value.emp_firstname? employee.value.emp_firstname:''
    let mname = employee.value.emp_middlename? employee.value.emp_middlename:''
    let lname = employee.value.emp_lastname? employee.value.emp_lastname:''
    let sname = employee.value.emp_suffixname? employee.value.emp_suffixname:''
    fullName.value = fname + ' ' + mname + ' ' + lname + ' ' + sname
    empId.value = employee.value.emp_id

    getCommandUsers().then((res1)=>{
        accountId.value = employee.value.emp_accid
        getEmployeeAccount().then((res2)=>{
            accounts.value = res1.data
            preLoading.value = false
            console.log(res2)
        })
    })
})

const saveTag = (mode) =>{
    if(accountId.value){
        // if (confirm("Are you sure you want to tag this account") == true) {
        //     preLoading.value = true
        //     let x = {
        //         emp_id:empId.value,
        //         acc_id:accountId.value,
        //         updated_by:userID.value,
        //         mode:mode,
        //     }

        //     employeeAccountTag(x).then((res)=>{
        //         if(res.status == 200){
        //             // alert('Successful')
        //             // location.reload()
        //             Swal.fire({
        //                 title: "Update Successful",
        //                 text: "Changes applied, refreshing the page",
        //                 icon: "success"
        //             }).then(()=>{
        //                 location.reload()
        //             });
        //         }else{
        //             // alert('failed')
        //             // location.reload()
        //             Swal.fire({
        //                 title: "Update Failed",
        //                 text: "Unknown error occured, try again later",
        //                 icon: "error"
        //             }).then(()=>{
        //                 location.reload()
        //             });
        //         }
        //     })
        // } else {
        //     return false;
        // }
        Swal.fire({
            title: "Tag Account",
            text: "Are you sure you want to tag this account to this record?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, Im going to update it!"
        }).then(async (result) => {
            if (result.isConfirmed) {
                preLoading.value = true
                let x = {
                    emp_id:empId.value,
                    acc_id:accountId.value,
                    updated_by:userID.value,
                    mode:mode,
                }

                employeeAccountTag(x).then((res)=>{
                    if(res.status == 200){
                        // alert('Successful')
                        // location.reload()
                        Swal.fire({
                            title: "Update Successful",
                            text: "Changes applied, refreshing the page",
                            icon: "success"
                        }).then(()=>{
                            location.reload()
                        });
                    }else{
                        // alert('failed')
                        // location.reload()
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
        });
    }else{
        // alert('Please Select an Account')
        Swal.fire({
            title: "Requirements",
            text: "Please Select an Account",
            icon: "question"
        })
    }
    
}

</script>
<template>
    <NeuLoader2 v-if="preLoading"/>
    <div v-else class="d-flex gap-2 flex-column neu-card p-3">
        <input class="neu-input" disabled :value="fullName"/>
        <select class="neu-input neu-select" v-model="accountId" :disabled="employee.emp_accid? true:false">
            <option value="0" disabled>--Select Account--</option>
            <option v-for="(acc, index) in accounts" :value="acc.id">{{ acc.name }}</option>
        </select>
        <button v-if="!employee.emp_accid" class="neu-btn neu-green p-2" @click="saveTag(1)"><font-awesome-icon icon="fa-solid fa-floppy-disk"  /> Tag Account</button>
        <button v-else class="neu-btn neu-red p-2" @click="saveTag(2)"><font-awesome-icon icon="fa-solid fa-trash"  /> Remove Account</button>
    </div>
</template>