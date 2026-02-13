<script setup>
import { ref, onMounted, computed } from 'vue';
import {
    setCommandUpdate
} from "../../../Fetchers.js";

const props = defineProps({
    quarterdata: {
    },
    userIdData: {
    },
})

const semester = computed(() => {
    return props.quarterdata
});

const userID = computed(() => {
    return props.userIdData
});

const disabled = ref(false)
const saving = ref(false)
const quarter = ref('')

const save = ()=>{
    saving.value = true
    let x = {
        sett_semester:quarter.value,
        sett_code:'cs_02',
        sett_updatedby:userID.value
    }
    Swal.fire({
        title: "Saving Updates",
        text: "Please wait while we check all necessary details.",
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    setCommandUpdate(x).then((results)=>{
        if (results.status == 200) {
            // alert('Update Successful')
            // location.reload()
            Swal.fire({
                title: "Update Successful",
                text: "Changes applied, refreshing the page",
                icon: "success"
            }).then(()=>{
                Swal.close()
                // location.reload()
            });
        } else {
            // alert('Update Failed')
            // location.reload()
            Swal.fire({
                title: "Update Failed",
                text: "Unknown error occured, try again later",
                icon: "error"
            }).then(()=>{
                Swal.close()
                // location.reload()
            });
        }
    })
}
</script>

<template>
    <form @submit.prevent="save">
        <div class="d-flex flex-wrap form-group">
            <label for="sem">Semester / Quarter</label>
            <select class="neu-input neu-select" v-model="quarter">
                <option value="" disabled>-- Select Type --</option>
                <option v-for="(q, index) in semester" :value="q.quar_id">{{ q.quar_desc }}</option>
            </select>
        </div>
        <button type="submit" class="neu-btn neu-green p-2 mt-3 " :disabled="saving? true:false">
            <font-awesome-icon icon="fa-solid fa-wrench"/> Save
        </button>
    </form>
    
</template>