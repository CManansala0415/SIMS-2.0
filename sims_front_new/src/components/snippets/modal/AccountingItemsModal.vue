<script setup>
import { ref, onMounted, computed } from 'vue';
import { getUserID } from "../../../routes/user";
import {
    addAccountingItem
} from "../../Fetchers.js";

const props = defineProps({
    itemData: {
    }
})
const item = computed(() => {
    return props.itemData
});

const userID = ref('')
const itemDesc = ref('')
const itemPrice = ref(0)
const itemDocstamp = ref('')
const itemReceiptType = ref('')
const saving = ref(false)

onMounted(() => {
    getUserID().then((results) => {
        userID.value = results.account.data.id
    }).catch((err) => {
        // alert('Unauthorized Session, Please Log In')
        // router.push("/");
        Swal.fire({
            icon: "error",
            title: "Oops...",
            text: "Session expired, log in again",
        }).then(()=>{
            router.push("/");
            window.stop()
        });
    })

    if (Object.keys(item.value).length) {
        itemDesc.value = item.value.acf_desc
        itemPrice.value = item.value.acf_price
        itemReceiptType.value = item.value.acf_rectype
        itemDocstamp.value = item.value.acf_docstamp
    } else {
        itemDesc.value = ''
        itemPrice.value = 0
    }
    console.log(item.value)
})

const save = () => {
    saving.value = true
    let x = {
        acf_desc: itemDesc.value,
        acf_docstamp: itemDocstamp.value,
        acf_feetype: item.value.acf_feetype,
        acf_id: item.value.acf_id,
        acf_price: itemPrice.value,
        acf_rectype: itemReceiptType.value,
        acf_user: userID.value,
    }
    addAccountingItem(x).then((results) => {
        if (results.status != 204) {
            // alert('Saving Failed')
            // location.reload()
            Swal.fire({
                title: "Update Successful",
                text: "Changes applied, refreshing the page",
                icon: "success"
            }).then(()=>{
                location.reload()
            });
        } else {
            // alert('Saving Successful')
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
    <div class="p-7">
        <div class="d-flex flex-wrap flex-column">
            <p class="text-success fw-bold">Miscellaneous Supplies</p>
            <p class="fst-italic border p-2 rounded-3 bg-secondary-subtle small-font"><span class="fw-bold">Note:
                </span><span class="italic">Ensure that the details of the item to be encoded is correct.
                </span></p>
        </div>
        <div class="d-flex gap-2 small-font">
            <div class="w-100">
                <form @submit.prevent="save" class="card text-start h-100">
                    <div class="d-flex flex-column gap-2 w-100 p-3">
                        <div class="form-group">
                            <label>Item Description</label>
                            <input v-model="itemDesc" required type="text" class="form-control form-control-sm" />
                        </div>
                        <div class="form-group">
                            <label>Item Price</label>
                            <input v-model="itemPrice" min="0" max="999999" required
                                oninput="this.value = Math.abs(this.value)" type="number"
                                class="form-control form-control-sm" />
                        </div>
                        <div class="form-group">
                            <label>Document Stamp</label>
                            <select v-model="itemDocstamp" class="form-control form-select-sm" required>
                                <option value="" disabled>-- Select Availability</option>
                                <option value="0">No</option>
                                <option value="1">Yes</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Receipt Type</label>
                            <select v-model="itemReceiptType" class="form-control form-select-sm" required>
                                <option value="" disabled>-- Select Availability</option>
                                <option value="1">Provisional (PR)</option>
                                <option value="2">Official (OR)</option>
                            </select>
                        </div>
                        <div class="d-flex mt-3">
                            <button :disabled="saving ? true : false" type="submit"
                                class="btn btn-sm btn-primary w-100">Save
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>