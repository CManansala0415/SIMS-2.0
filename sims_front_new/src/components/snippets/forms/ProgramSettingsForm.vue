<script setup>
import { ref, onMounted, computed } from 'vue';
import {
    addProgram
} from "../../Fetchers.js";
import NeuLoader4 from '../loaders/NeuLoader4.vue'
import NeuLoader1 from '../loaders/NeuLoader1.vue'

const props = defineProps({
    degreeData: {
    },
    programData: {
    },
    courseData: {
    },
    semesterData: {
    },
    userIdData: {
    },
    title: {
    }
})

const degree = computed(() => {
    return props.degreeData
});
const program = computed(() => {
    return props.programData
});
const course = computed(() => {
    return props.courseData
});
const semester = computed(() => {
    return props.semesterData
});
const userID = computed(() => {
    return props.userIdData
});

const searchValue = ref('')
const editForm = ref(false)
const saving = ref(false)
const filteredCourse = ref([])
const editData = ref({
    prog_id: '',
    prog_name: '',
    prog_code: '',
    prog_progtype: '',
    prog_degree: '',
    prog_year: '',
    prog_semtype: '',
    prog_addedby: '',
})
const preLoading = ref(true)
const edit = (data) => {

    editForm.value = !editForm.value

    if (data) {
        editData.value.prog_id = data.prog_id
        editData.value.prog_name = data.prog_name
        editData.value.prog_code = data.prog_code
        editData.value.prog_progtype = data.prog_progtype
        editData.value.prog_degree = data.prog_degree
        editData.value.prog_year = data.prog_year
        editData.value.prog_semtype = data.prog_semtype
        editData.value.prog_addedby = userID.value
    } else {
        editData.value.prog_id = ''
        editData.value.prog_name = ''
        editData.value.prog_code = ''
        editData.value.prog_progtype = ''
        editData.value.prog_degree = ''
        editData.value.prog_year = ''
        editData.value.prog_semtype = ''
        editData.value.prog_addedby = userID.value

    }
}

const registerCourse = () => {
    saving.value = true

    Swal.fire({
        title: "Saving Updates",
        text: "Please wait while we check all necessary details.",
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    addProgram(editData.value).then((results) => {
        // alert('Successfull Registered')
        // location.reload()
        Swal.fire({
            title: "Success",
            text: "Successfull registered, refreshing the page",
            icon: "success"
        }).then(()=>{
            Swal.close()
            location.reload()
        })
    })
}

const deactivate = (id) => {
    // let x = {
    //     prog_id: id,
    //     prog_updatedby: userID.value,
    //     deactivate: true
    // }
    // if (confirm("Are you sure you want to deactivate this record?") == true) {
    //     addProgram(x).then((results) => {
    //         // alert('Successfull Deactivated')
    //         // location.reload()
    //         // saving.value = false
    //     })
    // } else {
    //     return false;
    // }

    Swal.fire({
        title: "Delete Record",
        text: "Are you sure you want to deactivate this record?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, Im Delete it!"
    }).then(async (result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: "Saving Updates",
                text: "Please wait while we check all necessary details.",
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            let x = {
                prog_id: id,
                prog_updatedby: userID.value,
                deactivate: true
            }
            addProgram(x).then((results) => {
                Swal.fire({
                    title: "Delete Successful",
                    text: "Changes applied, refreshing the page",
                    icon: "success"
                }).then(()=>{
                    Swal.close()
                    location.reload()
                });
            })
        }
    });


}

const search = () => {
    filteredCourse.value = course.value.filter((e) => {
        if (
            (e.prog_code.toUpperCase().includes(searchValue.value.toUpperCase())) ||
            (e.prog_name.toUpperCase().includes(searchValue.value.toUpperCase()))
        ) {
            return e
        }
    })
}


const emit = defineEmits(['close'])
onMounted(async () => {
    filteredCourse.value = course.value
    preLoading.value = false
})

</script>
<template>
    <div class="small-font">
        <div class="p-3">
            <span class="text-uppercase fw-bold">Program Settings</span>
        </div>

        <!-- <div class="p-3 d-flex gap-2 justify-content-between mb-3">
            <div class="input-group w-50">
                <span class="input-group-text" id="searchaddon"><font-awesome-icon icon="fa-solid fa-search" /></span>
                <input type="text" class="neu-input" placeholder="Search Here..." aria-label="search"
                    v-model="searchValue" @keyup="search()" aria-describedby="searchaddon">
            </div>
            <div class="d-flex flex-wrap w-10 justify-content-end">
                <button tabindex="-1" data-bs-toggle="modal" data-bs-target="#addnewmodal" @click="edit()" type="button"
                    class="neu-btn neu-green">
                    <font-awesome-icon icon="fa-solid fa-add" /> Add New
                </button>
            </div>
        </div> -->
        <div v-if="preLoading">
            <NeuLoader1/>
        </div>

        <div v-else>

            <div class="p-3 d-flex gap-1 justify-content-between">
                <div class="input-group w-50">
                    <!-- <span class="input-group-text" id="searchaddon"><font-awesome-icon icon="fa-solid fa-search" /></span> -->
                    <input type="text" class="neu-input" placeholder="Search Here..." aria-label="search"
                        v-model="searchValue" @keyup.enter="search()" aria-describedby="searchaddon"
                        :disabled="preLoading ? true : false">
                </div>
                <div class="d-flex w-25 justify-content-end gap-2">
                    <button tabindex="-1" data-bs-toggle="modal" data-bs-target="#addnewmodal" @click="edit()"
                        type="button" class="neu-btn neu-green" :disabled="preLoading ? true : false">
                        <font-awesome-icon icon="fa-solid fa-add" /> Add New
                    </button>
                    <button class="neu-btn neu-blue" :disabled="preLoading ? true : false"
                        @click="$emit('close')"><font-awesome-icon icon="fa-solid fa-rotate-left" size="sm" /> Back
                    </button>
                </div>
            </div>

            <div class="table-responsive border p-3">
                <table class="neu-table">
                    <thead>
                        <tr>
                            <th style="color:#555555">Code</th>
                            <th style="color:#555555">Name</th>
                            <th style="color:#555555" class="text-center">Year</th>
                            <th style="color:#555555" class="text-center">Type</th>
                            <th style="color:#555555" class="text-center">Degree</th>
                            <th style="color:#555555" class="text-center">Semester</th>
                            <th style="color:#555555" class="text-center">Status</th>
                            <th style="color:#555555" class="text-center">Commands</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(pg, index) in filteredCourse">
                            <td class="align-middle">
                                {{ pg.prog_code }}
                            </td>
                            <td class="align-middle">
                                {{ pg.prog_name }}
                            </td>
                            <td class="align-middle text-center">
                                {{ pg.prog_year }}
                            </td>
                            <td class="align-middle text-center">
                            {{ program.find(p => p.dtype_id === pg.prog_progtype)?.dtype_desc || '—' }}
                            </td>

                            <td class="align-middle text-center">
                            {{ degree.find(d => d.deg_id === pg.prog_degree)?.deg_desc || '—' }}
                            </td>

                            <td class="align-middle text-center">
                            {{ semester.find(s => s.sem_id === pg.prog_semtype)?.sem_desc || '—' }}
                            </td>
                            <td class="align-middle  text-center">
                                {{ pg.prog_status == 1 ? 'Active' : 'Inactive' }}
                            </td>
                            <td class="align-middle  text-center">
                                <div class="d-flex gap-2 justify-content-center">
                                    <button data-bs-toggle="modal" data-bs-target="#addnewmodal" @click="edit(pg)" type="button"
                                        title="Edit Record" class="neu-btn-sm neu-white">
                                        <font-awesome-icon icon="fa-solid fa-pen" /></button>
                                    <button @click="deactivate(pg.prog_id)" type="button" title="Delete Record"
                                        class="neu-btn-sm neu-white"> <font-awesome-icon
                                            icon="fa-solid fa-trash" /></button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!Object.keys(filteredCourse).length">
                            <td class="p-3 text-center" colspan="8">
                                <NeuLoader4/>
                                <p class="fw-bold m-0">Nothing here yet!</p>
                                <p>The hamster took a break 💤 — try adding something new.</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
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
                <div class="modal-body neu-bg small-font">
                    <form @submit.prevent="registerCourse()" class="d-flex flex-column p-4 gap-2 neu-card">
                        <div class="d-flex flex-wrap flex-column">
                            <p class="text-success fw-bold">Program Settings</p>
                            <p class="fst-italic p-2 small-font"><span
                                    class="fw-bold">Note:
                                </span><span class="italic">Ensure that details are
                                    correct.
                                    To commit changes click the register button and wait for saving notification.
                                </span></p>
                        </div>
                        <div class="d-flex flex-wrap form-group">
                            <label for="type">Description</label>
                            <input v-model="editData.prog_name" required type="text"
                                class="neu-input" />
                        </div>
                        <div class="d-flex flex-wrap form-group">
                            <label for="type">Course Code</label>
                            <input v-model="editData.prog_code" required type="text"
                                class="neu-input" />
                        </div>
                        <div class="d-flex flex-wrap form-group">
                            <label for="type">Program Type</label>
                            <select class="neu-input neu-select" v-model="editData.prog_progtype" required @change=" editData.prog_degree = '',
                                editData.prog_year = '',
                                editData.prog_semtype = ''">
                                <option value="" disabled>-- Select Type --</option>
                                <option v-for="(p, index) in program" :value="p.dtype_id">{{ p.dtype_desc }}</option>
                            </select>
                        </div>
                        <div class="d-flex flex-wrap form-group">
                            <label for="type">Degree Type</label>
                            <select class="neu-input neu-select" v-model="editData.prog_degree" required>
                                <option value="" disabled>-- Select Type --</option>
                                <option v-for="(d, index) in degree" :value="d.deg_id"
                                    :disabled="d.deg_type == editData.prog_progtype ? false : true">{{ d.deg_desc }}</option>
                            </select>
                        </div>
                        <div class="d-flex flex-wrap form-group">
                            <label for="type">Program Years</label>
                            <select class="neu-input neu-select" v-model="editData.prog_year" required>
                                <option value="" disabled>-- Select Type --</option>
                                <option value="2">2 years</option>
                                <option value="4">4 years</option>
                            </select>
                        </div>
                        <div class="d-flex flex-wrap form-group">
                            <label for="type">Program Semester</label>
                            <select class="neu-input neu-select" v-model="editData.prog_semtype" required>
                                <option value="" disabled>-- Select Type --</option>
                                <option v-for="(s, index) in semester" :value="s.sem_id">{{ s.sem_desc }}</option>
                            </select>
                        </div>
                        <div class="d-flex flex-column mt-3">
                            <button :disabled="saving ? true : false" type="submit" class="neu-btn neu-green p-2"><font-awesome-icon icon="fa-solid fa-gear"/> Register</button>
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