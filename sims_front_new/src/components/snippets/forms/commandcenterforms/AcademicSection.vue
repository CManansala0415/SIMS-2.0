
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
const yearfrom = ref(0)
const yearto = ref(0)


const save = ()=>{
    saving.value = true
    let x = {
        sett_yearfrom:yearfrom.value,
        sett_yearto:yearto.value,
        sett_code:'cs_01',
        sett_updatedby:userID.value
    }
    setCommandUpdate(x).then((results)=>{
        if (results.status == 200) {
            alert('Update Successful')
            location.reload()
        } else {
            alert('Update Failed')
            // location.reload()
        }
    })
}
</script>
<template>
    <form @submit.prevent="save">
        <div class="d-flex flex-wrap form-group">
            <label for="edyearfrom"></label>
            <input type="number" class="form-control" v-model="yearfrom"
                max="9999" min="1999" :disabled="saving?true:false" required>
        </div>
        <div class="d-flex flex-wrap form-group">
            <label for="edyearto">Year To</label>
            <input type="number" class="form-control" v-model="yearto"
                max="9999" min="1999" :disabled="saving?true:false" required>
        </div>
        <button type="submit" class="btn btn-sm btn-success mt-3 w-100" :disabled="saving? true:false">
            Save
        </button>
    </form>

</template>