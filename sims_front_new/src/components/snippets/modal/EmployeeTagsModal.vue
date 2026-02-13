<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import NeuLoader2 from '../loaders/NeuLoader2.vue';
import {
    updateEmployee,
    addEmployee,
    addEmployeeLoad,
    deleteEmployeeLoad,
    getEmployeeLoad
} from "../../Fetchers.js";
import { getUserID } from "../../../routes/user.js";

const props = defineProps({

    employeeData: {
    },
    subjectData: {
    }
})

const employee = computed(() => {
    return props.employeeData
});

const subject = computed(() => {
    return props.subjectData
});

const router = useRouter();
const saving = ref(false)
const saved = ref(false)
const checking = ref(false)
const userID = ref('')
const subjectFilter = ref([])
const searchSubject = ref('')
const addedSubjectFilter = ref([])
const addSearchSubject = ref('')
const addedSubject = ref([])
const addedSubjectId = ref([])
const savingLoads = ref(false)
const loadSaved = ref(false)
const loadCount = ref(0)
const totalUnits = ref(0)

const personal = ref({
    emp_id:'',
    emp_firstname:'',
    emp_middlename:'',
    emp_lastname:'',
    emp_suffixname:'',
    emp_gender:'',
    emp_civilstatus:'',
    emp_contact:'',
    emp_email:'',
    emp_birthday: '',
    emp_status: '',

})

const booter = async () => {
    personal.value.emp_id = employee.value.emp_id
    personal.value.emp_firstname = employee.value.emp_firstname
    personal.value.emp_middlename = employee.value.emp_middlename
    personal.value.emp_lastname = employee.value.emp_lastname
    personal.value.emp_suffixname = employee.value.emp_suffixname
    personal.value.emp_gender = employee.value.emp_gender
    personal.value.emp_civilstatus = employee.value.emp_civilstatus
    personal.value.emp_contact = employee.value.emp_contact
    personal.value.emp_email = employee.value.emp_email
    personal.value.emp_birthday = employee.value.emp_birthday
    personal.value.emp_status = employee.value.emp_status

    getEmployeeLoad(employee.value.emp_id).then((results) => {
        results.forEach((e) => {

            let indexer = subject.value.findIndex(f => {
                return e.ld_subjid === f.subj_id
            });

            let x = {
                ...subject.value[indexer],
                ld_id: e.ld_id ? e.ld_id : 0,
            }
            addSubject(x)
        })

        // for total units
        addedSubject.value.forEach(e => {
            let x = e.subj_lec_units + e.subj_lab_units
            totalUnits.value += x
        })

        checking.value = false
    })
}
onMounted(async () => {
    console.log(subject.value)
    try {
        subjectFilter.value = subject.value
        checking.value = true
        await booter().then((results) => {
            getUserID().then((results) => {
                userID.value = results.account.data.id
                checking.value = false
            })
        })


    } catch (err) {
        // preLoading.value = false
        // alert('error loading the list default components')
        Swal.fire({
            icon: "error",
            title: "Oops...",
            text: "Something went wrong!",
            footer: '<a href="#" disabled>Have you checked your internet connection?</a>'
        }).then(()=>{
            preLoading.value = false
        });
    }
})

const clearFields = () => {
    personal.value = []
    // alert('Sheet Cleared')
    Swal.fire({
        title: "Cleared",
        text: "Sheet Cleared",
        icon: "success"
    })
}

const filteredSubject = () => {
    subjectFilter.value = subject.value.filter(e => {
        if (
            (e.subj_code.toUpperCase().includes(searchSubject.value.toUpperCase())) ||
            (e.subj_name.toUpperCase().includes(searchSubject.value.toUpperCase()))
        ) {
            return e
        }
    })

}

const filteredAddedSubject = () => {
    addedSubjectFilter.value = addedSubject.value.filter(e => {
        if (
            (e.subj_code.toUpperCase().includes(addSearchSubject.value.toUpperCase())) ||
            (e.subj_name.toUpperCase().includes(addSearchSubject.value.toUpperCase()))
        ) {
            return e
        }
    })

}

const addSubject = (data) => {
    let exist = addedSubject.value.filter(e => {
        if (e.subj_id == data.subj_id) {
            return e
        }
    })

    Object.keys(exist).length ?
    Swal.fire({
        title: "Duplicate",
        text: "Already Included",
        icon: "question"
    }) : addedSubject.value.push(data), filteredAddedSubject()
    Object.keys(exist).length ? false : addedSubjectId.value.push(data.subj_id)


}
const removeSubject = (index, id) => {
    if (id) {
        // let x = {
        //     ld_id: id
        // }
        // if (confirm("This load is already saved, are you sure you want to delete load? this cannot be reverted") == true) {
        //     deleteEmployeeLoad(x).then(() => {
        //         addedSubject.value.splice(index, 1)
        //         addedSubjectId.value.splice(index, 1)
        //         filteredAddedSubject()
        //     })
        // } else {
        //     return false;
        // }
        Swal.fire({
            title: "Delete Record",
            text: "This load is already saved, are you sure you want to delete load? this cannot be reverted",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, Im Delete it!"
        }).then(async (result) => {
            if (result.isConfirmed) {
                let x = {
                    ld_id: id
                }
                deleteEmployeeLoad(x).then((results) => {
                    // alert('Delete Successful')
                    // location.reload()
                    Swal.fire({
                        title: "Delete Successful",
                        text: "Changes applied",
                        icon: "success"
                    }).then(()=>{
                        addedSubject.value.splice(index, 1)
                        addedSubjectId.value.splice(index, 1)
                        filteredAddedSubject()
                    });
                })
            }
        });
    } else {
        addedSubject.value.splice(index, 1)
        addedSubjectId.value.splice(index, 1)
        filteredAddedSubject()
    }
}

const saveLoad = () => {
    if (!addedSubject.value.length) {
        // alert('Please add subjects first')
        Swal.fire({
            title: "Requirement",
            text: "Please add subjects first",
            icon: "question"
        })
    } else {
        savingLoads.value = true
        addedSubject.value.forEach(async (e) => {
            let x = {
                ld_id: e.ld_id ? e.ld_id : 0,
                ld_empid: personal.value.emp_id,
                ld_subjid: e.subj_id,
                ld_addedby: userID.value
            }
            addEmployeeLoad(x).then((results) => {
                loadCount.value += 1
                loadCount.value == Object.keys(addedSubject.value).length ? loadSaved.value = true : loadSaved.value = false
                if(loadSaved.value){
                    // alert('Successfully Saved')
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
       
            
        })
        // console.log(loadSaved.value)
        //console.log(Object.keys(addedSubject.value).length)
        //console.log(loadCount.value)

       
        
    } 
}


const refresh = () => {
    location.reload()
}

</script>
<template>
    <div class="w-100 text-center p-3" v-if="checking">
        <NeuLoader2 />
    </div>
    <div v-else class="container text-dim small-font">
        <div class="row gap-2">
            <div class="col neu-card p-3">
                <div class="d-flex flex-column gap-2 ">
                    <input v-model="searchSubject" class="neu-input" @keyup="filteredSubject"
                        placeholder="Search Subjects Here..." />
                    <div class="p-3 neu-card-inner">
                        <div class="table-responsive border p-2 small-font" style="height: 500px;">
                            <table class="neu-table">
                                <thead>
                                    <tr>
                                        <th>Select Subjects</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="!Object.keys(subjectFilter).length">
                                        <td class="align-middle text-start">
                                            No Data Found
                                        </td>
                                    </tr>
                                    <tr v-else v-for="(s, index) in subjectFilter" @click="addSubject(s)" :class="savingLoads? 'pe-none':'pe-auto'">
                                        <td
                                            :class="addedSubjectId.includes(s.subj_id) ? 'align-middle text-start neu-pastel-gray' : 'align-middle text-start '">
                                            <p class="fw-bold ">{{ s.subj_code }}</p>
                                            <p class=" fst-italic">{{ s.subj_name }}</p>
                                        </td>
                                    </tr>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col neu-card p-3">
                <div>
                    <div class="d-flex flex-column gap-2 ">
                        <input v-model="addSearchSubject" class="neu-input" @keyup="filteredAddedSubject"
                            placeholder="Search Subjects Here..." />
                        <div class="p-3 neu-card-inner">
                            <div class="table-responsive border p-2 small-font" style="height: 500px;">
                                <table class="w-100">
                                    <thead>
                                        <tr>
                                            <th>Added Subjects</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-if="!Object.keys(addedSubjectFilter).length">
                                            <td class="align-middle text-start">
                                                No Data Found
                                            </td>
                                        </tr>
                                        <tr v-else v-for="(s, index) in addedSubjectFilter">
                                            <td
                                                class="align-middle text-start">
                                                <div class="mt-2 w-100 neu-card p-3">
                                                    <div class="d-flex flex-column">
                                                        <div class="mb-3">
                                                            <span class="mt-3 fw-bold">{{ s.subj_code }}</span>&nbsp;
                                                            <span class="mb-3 fst-italic">{{ s.subj_name }}</span>
                                                        </div>
                                                        <div class="mb-1">
                                                            <span class="fw-semibold">Lecture Units</span>
                                                            <input v-model="s.subj_lec_units" type="text" class="neu-input" disabled>
                                                        </div>
                                                        <div class="mb-1">
                                                            <span class="fw-semibold">Laboratory Units</span>
                                                            <input v-model="s.subj_lab_units" type="text" class="neu-input" disabled>
                                                        </div>
                                                        <div class="mb-3">
                                                            <span class="fw-semibold">Total Units / Hours</span>
                                                            <input :value="s.subj_lab_units + s.subj_lec_units" type="text" class="neu-input" disabled>
                                                        </div>  
                                                        <button :disabled="savingLoads? true:false" @click="removeSubject(index, s.ld_id)" type="button" class="neu-btn neu-red p-2">&times; Remove</button> 
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 p-2 d-flex align-content-center justify-content-end gap-2">
                <button :disabled="savingLoads? true:false" @click="addedSubjectFilter = [], addedSubject = [], addedSubjectId = []" type="button" class="neu-btn neu-orange p-2" tabindex="-1">
                    <font-awesome-icon icon="fa-solid fa-arrows-rotate"/> Reset Added Subjects
                </button>
                <button :disabled="savingLoads? true:false" @click="saveLoad" type="button" class="neu-btn neu-green p-2" tabindex="-1">
                    <font-awesome-icon icon="fa-solid fa-floppy-disk"/> Save Subject Loads
                </button>
            </div>
        </div>
    </div>
</template>