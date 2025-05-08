
<script setup>
import { ref, onMounted, computed } from 'vue';
import {
    setCommandUpdate
} from "../../../Fetchers.js";

const props = defineProps({
    userIdData: {
    }
})

const userID = computed(() => {
    return props.userIdData
});


const disabled = ref(false)
const saving = ref(false)
const downpayment = ref(0)


const save = ()=>{
    saving.value = true
    let x = {
        sett_downpayment:downpayment.value,
        sett_code:'cs_03',
        sett_updatedby:userID.value
    }
    setCommandUpdate(x).then((results)=>{
        if (results.status == 200) {
            // alert('Update Successful')
            // location.reload()
            Swal.fire({
                title: "Update Successful",
                text: "Changes applied, refreshing the page",
                icon: "success"
            }).then(()=>{
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
                location.reload()
            });
        }
    })
}
</script>
<template>
    <form @submit.prevent="save">
        <div class="d-flex flex-wrap form-group">
            <label for="edyearfrom">Amount</label>
            <input type="number" class="form-control" v-model="downpayment"
                max="9999" min="500" :disabled="saving?true:false" required>
        </div>
        <button type="submit" class="btn btn-sm btn-success mt-3 w-100" :disabled="saving? true:false">
            Save
        </button>
    </form>

</template>