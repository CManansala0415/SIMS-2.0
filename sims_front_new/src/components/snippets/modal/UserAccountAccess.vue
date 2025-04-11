<script setup>
import { ref, onMounted, computed } from 'vue';
import Loader from '../loaders/Loading1.vue';

import {
    getCommandUsers,
    updateCommandUsers,
    addCommandUsers,
    saveCommandAccess,
    getCommandAccess

} from "../../Fetchers.js";

const props = defineProps({
    userIdData: {
    },
    accountData:{
    },
    title: {
    }
})

const userID = computed(() => {
    return props.userIdData
});

const accessModuleData = computed(() => {
    return props.accountData
});

const emit = defineEmits(['close'])
const saving = ref(false)

onMounted(async () => {
    console.log(accessModuleData.value)
})

const handleAccess = async () => {
    saveCommandAccess(accessModuleData.value).then((results)=>{
        if (results.status == 200) {
            alert('Update Successful')
            location.reload()
        } else {
            alert('Update Failed')
            location.reload()
        }
    })
}

const modify = (moduleIndex, actionIndex, val) =>{
    accessModuleData.value[moduleIndex].module_access[actionIndex].access_checked = !accessModuleData.value[moduleIndex].module_access[actionIndex].access_checked
    if(val == true){
        accessModuleData.value[moduleIndex].module_access[actionIndex].access_viewing = 0
        accessModuleData.value[moduleIndex].module_access[actionIndex].access_modifying = 0
    }else{
        accessModuleData.value[moduleIndex].module_access[actionIndex].access_viewing = 1
        accessModuleData.value[moduleIndex].module_access[actionIndex].access_modifying = 1
    }
        // accessModuleData.value[moduleIndex].module_access[actionIndex].access_checked = !accessModuleData.value[moduleIndex].module_access[actionIndex].access_checked
        // accessModuleData.value[moduleIndexmodule_access[actionIndex].access_viewing = !accessModuleData.value[moduleIndex].module_access[actionIndex].access_viewing
        // accessModuleData.value[moduleIndex].module_access[actionIndex].access_modifying = !accessModuleData.value[moduleIndex].module_access[actionIndex].access_modifying
}

const mode = (index)=>{
    accessModuleData.value[index].module_grant == 0? accessModuleData.value[index].module_grant=1:accessModuleData.value[index].module_grant=0
    
    let x = accessModuleData.value[index].module_grant
    for(let i = 0; i < Object.keys(accessModuleData.value[index].module_access).length; i++){
        accessModuleData.value[index].module_access[i].access_disabled = !x
        // off lahat kung naka on yung disable
        if(!x){
            accessModuleData.value[index].module_access[i].access_checked = 0
            accessModuleData.value[index].module_access[i].access_viewing = 0
            accessModuleData.value[index].module_access[i].access_modifying = 0
        }
    }

    
}

</script>
<template>
    <form @submit.prevent="handleAccess" class="d-flex flex-column align-items-start small-font">
        <div class="mb-3 w-100">
            <div class="accordion accordion-flush" id="accordionFlushExample">
                <div class="accordion-item" v-for="(mod, modindex) in accessModuleData">
                    <h2 class="accordion-header" id="flush-headingOne">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            :data-bs-target="'#module' + mod.module_id" aria-expanded="false"
                            :aria-controls="'module' + mod.module_id">
                            {{ mod.module_description }}
                        </button>
                    </h2>
                    <div :id="'module' + mod.module_id" class="accordion-collapse collapse"
                        aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                        <div class="accordion-body">
                            <ul class="list-group">
                                <li class="list-group-item">
                                    <div class="p-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox"
                                                @change="mode(modindex)"
                                                :checked="mod.module_grant"
                                                :value="mod.module_grant">
                                            <label class="form-check-label">Grant Module Access</label>
                                        </div>
                                    </div> 
                                </li>
                                <li class="list-group-item" v-for="(acc, accindex) in mod.module_access">
                                    
                                    <div class="border p-3 ">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox"
                                                :checked="acc.access_checked" @change="modify(modindex, accindex, acc.access_checked)"
                                                :disabled="acc.access_disabled" :value="acc.access_value">
                                            <label class="form-check-label fw-bold">{{
                                                acc.access_description }}</label>
                                        </div>
                                    </div>
                                    <div class="border">
                                        <div class="accordion accordion-flush" id="crudaccordion">
                                            <div class="accordion-item">
                                                <p class="accordion-header" id="flush-crud">
                                                    <button class="accordion-button collapsed" type="button"
                                                        data-bs-toggle="collapse"
                                                        :data-bs-target="'#action' + acc.access_id" aria-expanded="false"
                                                        :aria-controls="'action' + acc.access_id">
                                                        <span class="small-font">Actions Granted</span>
                                                        <code class="mx-3 text-danger"
                                                            v-show="!acc.access_viewing">(Enable Access First)</code>
                                                        <code class="mx-3 text-success"
                                                            v-show="acc.access_viewing">(Access Enabled)</code>
                                                    </button>
                                                </p>
                                                <div :id="'action' + acc.access_id" class="accordion-collapse collapse"
                                                    aria-labelledby="flush-crud" data-bs-parent="#crudaccordion">
                                                    <div class="p-3">
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox"
                                                                @change="acc.access_viewing==0? acc.access_viewing=1:acc.access_viewing=0"
                                                                :checked="acc.access_viewing"
                                                                disabled="true"
                                                                :value="acc.access_viewing">
                                                            <label class="form-check-label">View / General
                                                                Access</label>
                                                        </div>
                                                    </div>
                                                    <div class="p-3">
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox"
                                                                @change="acc.access_modifying==0? acc.access_modifying=1:acc.access_modifying=0"
                                                                :checked="acc.access_modifying"
                                                                :disabled="!acc.access_checked"
                                                                :value="acc.access_modifying">
                                                            <label class="form-check-label">Add, Edit, Delete / Modifier
                                                                Access</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-success w-100" :disabled="saving ? true : false">
            Save
        </button>
    </form>
</template>