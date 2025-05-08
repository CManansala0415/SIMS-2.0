<script setup>
import { ref, onMounted, computed } from 'vue';
import {
    dispenseMedicalSupplyStudent,
    getDispensedMedicalSupplyStudent,
    dispenseMedicalSupplyEmployee,
    getDispensedMedicalSupplyEmployee
} from "../../Fetchers.js";

const props = defineProps({
    medicicalItemsData: {
    },
    userIdData: {
    },
    personIdData: {
    },
    modeData: {
    }
})
const medicalItems = computed(() => {
    return props.medicicalItemsData
});
const userId = computed(() => {
    return props.userIdData
});
const personId = computed(() => {
    return props.personIdData
});
const mode = computed(() => {
    return props.modeData
});

const preloading = ref(false)
const saving = ref(false)
const itemSelected = ref('')
const quantityCeiling = ref(0)
const quantity = ref(0)
const itemsDispensed = ref([])
onMounted(() => {
    preloading.value = true
    getDispensedMedicalSupplyStudent(personId.value).then((results) => {
        itemsDispensed.value = results
        preloading.value = false
    })
})

const filterQuantity = () => {
    quantityCeiling.value = 0
    let indexer = medicalItems.value.findIndex(f => {
        quantityCeiling.value = f.clms_stocks
        return itemSelected.value === f.clms_id
    });
}
const updateMedicalInventory = () => {
    saving.value = true
    let x = {
        clmd_itemid: itemSelected.value,
        clmd_quantity: quantity.value,
        clmd_stock: quantityCeiling.value,
        clmd_user: userId.value,
        clmd_personid: personId.value
    }


    dispenseMedicalSupplyStudent(x).then((results) => {

        if (results.status == 200) {
            // alert('Update Successful')
            //    location.reload()
            Swal.fire({
                title: "Update Successful",
                text: "Changes applied, refreshing the page",
                icon: "success"
            }).then(()=>{
                location.reload()
            });
        } else {
            // alert('Update Failed')
            //    location.reload()
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
    <div class="d-flex flex-column p-2 gap-2">
        <div class="d-flex flex-wrap flex-column">
            <p class="text-success fw-bold">Medical Supplies</p>
            <p class="fst-italic border p-2 rounded-3 bg-secondary-subtle small-font"><span class="fw-bold">Note:
                </span><span class="italic">Ensure that the details of the receiver and the item to be dispensed is correct.
                </span></p>
        </div>
        <div class="d-flex gap-2 small-font">
            <div class="w-100">
                <form @submit.prevent="updateMedicalInventory" class="card text-start h-100">
                    <div class="d-flex flex-column gap-2 w-100 p-3">
                        <div class="form-group">
                            <label>Item to be dispensed</label>
                            <select @change="filterQuantity()" class="form-control form-select-sm"
                                v-model="itemSelected" required>
                                <option value="" disabled>
                                    Select Item
                                </option>
                                <option v-for="(mdi, index) in medicalItems" :value="mdi.clms_id">
                                    {{ mdi.clms_desc }}
                                </option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Selected Item Stocks Available</label>
                            <input disabled :value="quantityCeiling" type="number"
                                class="form-control form-control-sm" />
                        </div>
                        <div class="form-group">
                            <label>Quantity</label>
                            <input min="1" :max="quantityCeiling" required
                                :disabled="quantityCeiling <= 0 ? true : false" v-model="quantity" type="number"
                                class="form-control form-control-sm" />
                        </div>
                        <div class="form-group">
                            <button :disabled="saving || quantityCeiling <= 0 ? true : false" type="submit"
                                class="btn btn-sm btn-dark w-100">
                                <i class="mr-2 fa-solid fa-plus"></i>Dispense
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="w-100">
                <div class="card shadow w-100">
                    <div class="align-content-center bg-dark-subtle p-2 fw-bold">
                        Medical Items Dispensed
                    </div>
                    <div v-if="!Object.keys(itemsDispensed).length && preloading == false"
                        class="border p-2 align-content-center">
                        <span class="fw-bold text-danger">No Items Found</span>
                    </div>
                    <div v-if="!Object.keys(itemsDispensed).length && preloading == true"
                        class="border p-2 align-content-center">
                        <span class="fw-bold text-danger">Loading Items...</span>
                    </div>
                    <div v-if="Object.keys(itemsDispensed).length && preloading == false"
                        class="overflow-auto p-3 d-flex flex-column gap-2" style="height:15rem;">
                        <div class="card" v-for="(itm, index) in itemsDispensed">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item bg-secondary-subtle">
                                    Date Dispensed: <span class="fw-bold">{{ itm.clmd_datedispensed}}</span>
                                </li>
                                <li class="list-group-item">
                                    Item Description: <span class="fw-bold">{{ itm.clms_desc }}</span>
                                </li>
                                <li class="list-group-item">
                                    Amount Dispensed: <span class="fw-bold">{{ itm.clmd_quantity}}</span>
                                </li>
                                <li class="list-group-item">
                                    Item Previous Stock: <span class="fw-bold">{{ itm.clmd_stock}}</span>
                                </li>
                                <li class="list-group-item">
                                    Remaining Stock as of Date: <span class="fw-bold">{{itm.clmd_deducted_stock }}</span>
                                </li>
                                <li class="list-group-item">
                                    Personnel: <span class="fw-bold">{{ itm.emp_firstname }}{{ itm.emp_lastname }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>