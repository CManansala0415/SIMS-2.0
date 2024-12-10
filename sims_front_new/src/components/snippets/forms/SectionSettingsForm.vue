<script setup>
import { ref, onMounted, computed } from 'vue';
import {
    addSection
} from "../../Fetchers.js";

const props = defineProps({
    sectionData: {
    },
    userIdData: {
    },
    title: {
    }
})

const section = computed(() => {
    return props.sectionData
});
const program = computed(() => {
    return props.programData
});
const userID = computed(() => {
    return props.userIdData
});

const searchValue = ref('')
const editForm = ref(false)
const saving = ref(false)
const filteredSection = ref([])
const editData = ref({
    sec_id: '',
    sec_name: '',
    sec_code: '',
    sec_addedby: '',
})
const edit = (data) => {

    editForm.value = !editForm.value

    if (data) {
        editData.value.sec_id = data.sec_id
        editData.value.sec_name = data.sec_name
        editData.value.sec_code = data.sec_code
        editData.value.sec_addedby = userID.value
    } else {
        editData.value.sec_id = ''
        editData.value.sec_name = ''
        editData.value.sec_code = ''
        editData.value.sec_addedby = userID.value

    }
}

const registerSection = () => {
    saving.value = true
    addSection(editData.value).then((results) => {
        alert('Successfull Registered')
        location.reload()
        saving.value = false
    })
}

const deactivate = (id) => {
    let x = {
        sec_id: id,
        sec_updatedby: userID.value,
        deactivate: true
    }
    if (confirm("Are you sure you want to deactivate this record?") == true) {
        addSection(x).then((results) => {
            alert('Successfull Deactivated')
            location.reload()
            saving.value = false
        })
    } else {
        return false;
    }


}

const search = () => {
    filteredSection.value = section.value.filter((e) => {
        if (
            (e.sec_code.toUpperCase().includes(searchValue.value.toUpperCase())) ||
            (e.sec_name.toUpperCase().includes(searchValue.value.toUpperCase()))
        ) {
            return e
        }
    })
}


const emit = defineEmits(['close'])
onMounted(async () => {
    filteredSection.value = section.value
})

</script>
<template>
    <div>
        <div class="p-3 d-flex align-content-center justify-content-center">
            <p class="text-uppercase fw-bold green-mid text-white rounded-3 p-2 small-font">Section Settings</p>
        </div>

        <div class="p-1 d-flex gap-2 justify-content-between mb-3">
            <div class="input-group w-50">
                <span class="input-group-text" id="searchaddon"><font-awesome-icon icon="fa-solid fa-search" /></span>
                <input type="text" class="form-control" placeholder="Search Here..." aria-label="search"
                    v-model="searchValue" @keyup="search()" aria-describedby="searchaddon">
            </div>
            <div class="d-flex flex-wrap w-50 justify-content-end">
                <button tabindex="-1" data-bs-toggle="modal" data-bs-target="#addnewmodal" @click="edit()" type="button"
                    class="btn btn-sm btn-primary">
                    <font-awesome-icon icon="fa-solid fa-add" /> Add New
                </button>
            </div>
        </div>
        <div class="table-responsive border p-3 small-font">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th style="background-color: #237a5b;" class="text-white">Code</th>
                        <th style="background-color: #237a5b;" class="text-white">Name</th>
                        <th style="background-color: #237a5b;" class="text-white">Status</th>
                        <th style="background-color: #237a5b;" class="text-white">Commands</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(sc, index) in filteredSection">
                        <td class="align-middle">
                            {{ sc.sec_code }}
                        </td>
                        <td class="align-middle">
                            {{ sc.sec_name }}
                        </td>
                        <td class="align-middle">
                            {{ sc.sec_status == 1 ? 'Active' : 'Inactive' }}
                        </td>
                        <td class="align-middle">
                            <div class="d-flex gap-2 justify-content-center">
                                <button data-bs-toggle="modal" data-bs-target="#addnewmodal" @click="edit(sc)" type="button"
                                    title="Edit Record" class="btn btn-secondary btn-sm">
                                    <font-awesome-icon icon="fa-solid fa-pen" /></button>
                                <button @click="deactivate(sc.sec_id)" type="button" title="Delete Record"
                                    class="btn btn-secondary btn-sm"> <font-awesome-icon
                                        icon="fa-solid fa-trash" /></button>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="!Object.keys(filteredSection).length">
                        <td class="p-3 text-center" colspan="4">
                            No Records Found
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add New Modal -->
    <div class="modal fade" id="addnewmodal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Settings</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        @click="editForm = false"></button>
                </div>
                <div class="modal-body">
                    <form @submit.prevent="registerSection()" class="d-flex flex-column p-2 gap-2">
                        <div class="d-flex flex-wrap flex-column">
                            <p class="text-success fw-bold">Section Settings</p>
                            <p class=" fst-italic border p-2 rounded-3 bg-secondary-subtle small-font"><span
                                    class="fw-bold">Note:
                                </span><span class="italic">Ensure that details are
                                    correct.
                                    To commit changes click the register button and wait for saving notification.
                                </span></p>
                        </div>
                        <div class="d-flex flex-wrap form-group">
                            <label for="type">Section Title</label>
                            <input v-model="editData.sec_name" required type="text"
                                class="form-control form-control-sm" />
                        </div>
                        <div class="d-flex flex-wrap form-group">
                            <label for="type">Section Code</label>
                            <input v-model="editData.sec_code" required type="text"
                                class="form-control form-control-sm" />
                        </div>
                        <div class="d-flex flex-column mt-3">
                            <button :disabled="saving ? true : false" type="submit"
                                class="btn btn-sm btn-primary">Register</button>
                        </div>
                    </form>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <div class="form-group">
                        <small id="emailHelp" class="form-text text-muted">We'll never share your personal information
                            with anyone
                            else (Data Privacy Act of 2012)</small>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                            @click="editForm = false">Close</button>
                        <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>