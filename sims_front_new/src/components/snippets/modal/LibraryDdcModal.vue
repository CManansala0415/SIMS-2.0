<script setup>
import { ref, onMounted, computed } from 'vue';
import {
    addBooksDdc,
} from "../../Fetchers.js";

const props = defineProps({
    ddcdata: {
    },
    ddciddata: {
    },
    modedata: {
    },
    useriddata: {
    }
})

const ddcId = computed(() => {
    return props.ddciddata
});
const ddcData = computed(() => {
    return props.ddcdata
});
const mode = computed(() => {
    return props.modedata
});
const userId = computed(() => {
    return props.useriddata
});

const ddcNo = ref('')
const ddcDesc = ref('')
const saving = ref(false)

onMounted(() => {
    if (mode.value == 2) {
        ddcNo.value = ''
        ddcDesc.value = ''
    } else {
        ddcNo.value = ddcData.value.lbrc_ddc
        ddcDesc.value = ddcData.value.lbrc_desc
    }
})

const registerDdc = () => {
    //1 means update 2 means add 3 means delete
    saving.value = true
    let x = {}
    if (mode.value == 1) {
        x = {
            lbrc_id: ddcId.value,
            lbrc_ddc: ddcNo.value,
            lbrc_desc: ddcDesc.value,
            lbrc_updatedby: userId.value,
            lbrc_mode: mode.value
        }
    } else {
        x = {
            lbrc_ddc: ddcNo.value,
            lbrc_desc: ddcDesc.value,
            lbrc_addedby: userId.value,
            lbrc_mode: mode.value
        }
    }
    addBooksDdc(x).then((results) => {
        if (results.status != 200) {
            // alert('Update Failed')
            // location.reload()
            Swal.fire({
                title: "Update Failed",
                text: "Unknown error occured, try again later",
                icon: "error"
            }).then(()=>{
                location.reload()
            });
        } else {
            // alert('Configuration Successful')
            // location.reload()
            Swal.fire({
                title: "Update Successful",
                text: "Changes applied, refreshing the page",
                icon: "success"
            }).then(()=>{
                location.reload()
            });
        }
    })
}
const formType = ref(0)
</script>

<template>
    <div class="neu-card-inner p-3">
        <div class="d-flex flex-wrap flex-column">
            <p class="text-success fw-bold">Dewey Decimal Classification</p>
            <p class="fw-bold">{{ ddcDesc }}</p>
            <p class=" fst-italic p-2 small-font"><span class="fw-bold">Note:
                </span><span class="italic">Check and fillout required details to complete the details and update the book's
                    DDC details.
                </span></p>
        </div>
        <div class="d-flex flex-wrap flex-column neu-card p-3 small-font">
            <form @submit.prevent="registerDdc()" class="d-flex flex-column gap-2">
                <div class="d-flex flex-wrap form-group">
                    <label>DDC</label>
                    <input v-model="ddcNo" required type="number" class="neu-input" />
                </div>
                <div class="d-flex flex-wrap form-group">
                    <label>Title</label>
                    <input v-model="ddcDesc" required onkeydown="return /[a-z, ]/i.test(event.key)" type="text"
                        class="neu-input" />
                </div>
                <div class="mt-2">
                    <button :disabled="saving ? true : false" type="submit"
                        class="neu-btn neu-green"> <font-awesome-icon icon="fa-solid fa-wrench" /> Save Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>