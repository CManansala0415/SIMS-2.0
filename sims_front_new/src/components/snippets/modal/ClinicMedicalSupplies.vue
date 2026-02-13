<script setup>
import { ref, onMounted, computed } from 'vue';
import {
    updateMedicalSupply,
} from "../../Fetchers.js";

const props = defineProps({
    medicalSupplyData: {
    },
    userData: {
    }
})
const medicalSupply = computed(() => {
    return props.medicalSupplyData
});
const userId = computed(() => {
    return props.userData
});

const replenishItem = ref()
const saving = ref(false)
const item = ref([])
onMounted(() => {
    replenishItem.value = medicalSupply.value.clms_stocks
})

const updateMedicalInventory = () => {
    saving.value = true
    Swal.fire({
        title: "Saving Updates",
        text: "Please wait while we check all necessary details.",
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    item.value.push(medicalSupply.value)
    let x = item.value.map((e) => {
        if (e.clms_id && !e.replenish) {
            return {
                ...e,
                clms_mode: 1,
                clms_stocks: replenishItem.value,
                clms_user: userId.value
            }
        } else if (e.clms_id && e.replenish) {
            return {
                ...e,
                clms_mode: 3,
                clsh_replenish: replenishItem.value,
                clsh_deduct: 0,
                clms_user: userId.value
            }
        } else {
            return {
                ...e,
                clms_mode: 2,
                clms_stocks: replenishItem.value,
                clms_user: userId.value
            }
        }
    })

    updateMedicalSupply(x[0]).then((results) => {
        if (results.status == 200) {
            // alert('Update Successful')
            // location.reload()
            Swal.fire({
                title: "Update Successful",
                text: "Changes applied, refreshing the page",
                icon: "success"
            }).then(()=>{
                Swal.close();
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
                Swal.close();
                location.reload()
            });
        }
    })

}
</script>

<template>
    <div class="p-2 neu-card small-font">
        <div class="p-2">
            <p v-if="!medicalSupply.clms_id && !medicalSupply.replenish" class="fw-bold text-success">Add New Medical
                Supply</p>
            <p v-else-if="medicalSupply.clms_id && !medicalSupply.replenish" class="fw-bold text-success">Update New
                Medical Supply</p>
            <p v-else class="fw-bold text-success">Replenish Medical Supply</p>
        </div>
        <div class="neu-card-inner p-4">
            <form @submit.prevent="updateMedicalInventory" class="w-100">
                <div class="d-flex flex-column gap-1 text-start">
                    <div v-if="!medicalSupply.clms_id || !medicalSupply.replenish" class="">
                        <label class="">Item Description</label>
                        <input v-model="medicalSupply.clms_desc" type="text"
                            class="neu-input" />
                    </div>
                    <div v-if="!medicalSupply.clms_id || !medicalSupply.replenish" class="">
                        <label class="">Item Type</label>
                        <select class="neu-input neu-select" v-model="medicalSupply.clms_itemtype"
                            required>
                            <option value="1">
                                Consumables
                            </option>
                            <option value="2">
                                Medicine
                            </option>
                            <option value="3">
                                Equipment
                            </option>
                        </select>
                    </div>
                    <div v-if="!medicalSupply.clms_id || medicalSupply.replenish" class="">
                        <label class="">Stock Count</label>
                        <input v-model="medicalSupply.clms_stocks" disabled type="number"
                            class="neu-input" />
                    </div>
                    <div v-if="!medicalSupply.clms_id || medicalSupply.replenish" class="">
                        <label class="">Stock Replenish</label>
                        <input v-model="replenishItem" required min="1" type="number"
                            class="neu-input" />
                    </div>
                    <div class="d-flex mt-3">
                        <button v-if="!medicalSupply.clms_id" :disabled="saving ? true : false" type="submit"
                            class="neu-btn neu-green p-2"><font-awesome-icon icon="fa-solid fa-add" /> Add
                        </button>
                        <button v-else :disabled="saving ? true : false" type="submit"
                            class="neu-btn neu-blue p-2"><font-awesome-icon icon="fa-solid fa-wrench" /> Update
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>