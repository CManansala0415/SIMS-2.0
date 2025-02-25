<script setup>
import { ref, onMounted, computed } from 'vue';
import Loader from '../loaders/Loading1.vue';

import {
    getCommandUsers,
    updateCommandUsers,
    addCommandUsers

} from "../../Fetchers.js";

const props = defineProps({
    userIdData: {
    },
    title: {
    }
})

const userID = computed(() => {
    return props.userIdData
});

const emit = defineEmits(['close'])

const saving = ref(false)
const preLoading = ref(false)
const addNew = ref(false)
const searchValue = ref('')
const limit = ref(10)
const offset = ref(0)
const usersCount = ref(0)
const users = ref([])
const name = ref('')
const email = ref('')
const password = ref('')
const confirmpassword = ref('')
const id = ref('')

onMounted(async () => {

    try {
        preLoading.value = true
        getCommandUsers().then((results) => {
            users.value = results.data
            usersCount.value = results.count
            preLoading.value = false
        })

    } catch (err) {
        preLoading.value = false
        alert('error loading the list default components')
    }
})

const banAccount = (id) => {
    let x = {
        id: id,
        updated_by: userID.value,
        mode: 2
    }
    updateCommandUsers(x).then((results) => {
        if (results.status != 200) {
            alert('Update Failed')
            // location.reload()
        } else {
            alert('Update Successful')
            // location.reload()
        }
    })
}
const editData = (data) => {
    if (data) {
        addNew.value = false
        id.value = data.id
        name.value = data.name
        email.value = data.email
    } else {
        addNew.value = true
        id.value = ''
        name.value = ''
        email.value = ''
        password.value = ''
        confirmpassword.value = ''
    }
}

const handleAccount = async () => {
    saving.value = true
    if (addNew.value) {
        let x = {
            name: name.value,
            email: email.value,
            password: password.value,
            password_confirmation: password.value
        }
        addCommandUsers(x).then((results) => {
            if (results.status != 200) {
                alert('Update Failed')
                // location.reload()
            } else {
                alert('Update Successful')
                location.reload()
            }
        })
    } else {
        let x = {
            id: id.value,
            name: name.value,
            email: email.value,
            mode: 1,
            updated_by: userID.value
        }
        updateCommandUsers(x).then((results) => {
            if (results.status != 200) {
                alert('Update Failed')
                // location.reload()
            } else {
                alert('Update Successful')
                location.reload()
            }
        })
    }
}

const accessData = async (data) => {
}

const handleAccess = async () => {
    console.log(accessModuleData.value)
}

const modify = (moduleIndex, moduleType, actionIndex) =>{
    // console.log(moduleIndex)
    // console.log(moduleType)
    // console.log(actionIndex)
    if(moduleType == 1){
        accessModuleData.value[moduleIndex].module_access[actionIndex].access_checked = !accessModuleData.value[moduleIndex].module_access[actionIndex].access_checked
        accessModuleData.value[moduleIndex].module_access[actionIndex].access_action[0].action_checked = !accessModuleData.value[moduleIndex].module_access[actionIndex].access_action[0].action_checked
        accessModuleData.value[moduleIndex].module_access[actionIndex].access_action[1].action_checked = !accessModuleData.value[moduleIndex].module_access[actionIndex].access_action[1].action_checked
    }else{
        accessModuleData.value[moduleIndex].module_access[actionIndex].access_action[actionIndex].action_checked = !accessModuleData.value[moduleIndex].module_access[actionIndex].access_action[actionIndex].action_checked
    }

    console.log(accessModuleData.value)
}



const accessModuleData = ref(
    {
        0: {
            module_id: 1,
            module_value: 1,
            module_description: 'Registrar Module',
            module_access: {
                0: {
                    access_id: 1,
                    access_value: 1,
                    access_description: 'Student Admission Access',
                    access_checked: false,
                    access_disabled: false,
                    access_action:{
                        0: {
                            action_id:1,
                            action_value: 1,
                            action_description:'View / General Access',
                            action_checked: false,
                            action_disabled: true,
                        },
                        1: {
                            action_id:2,
                            action_value: 2,
                            action_description:'Add, Edit, Delete / Modifier Access',
                            action_checked: false,
                            action_disabled: false,
                        },
                    }
                },
                1: {
                    access_id: 2,
                    access_value: 2,
                    access_description: 'Enrollment Access',
                    access_checked: false,
                    access_disabled: false,
                    access_action:{
                        0: {
                            action_id:1,
                            action_value: 1,
                            action_description:'View / General Access',
                            action_checked: false,
                            action_disabled: true,
                        },
                        1: {
                            action_id:2,
                            action_value: 2,
                            action_description:'Add, Edit, Delete / Modifier Access',
                            action_checked: false,
                            action_disabled: false,
                        },
                    }
                },
                2: {
                    access_id: 3,
                    access_value: 3,
                    access_description: 'Student Request Access',
                    access_checked: false,
                    access_disabled: false,
                    access_action:{
                        0: {
                            action_id:1,
                            action_value: 1,
                            action_description:'View / General Access',
                            action_checked: false,
                            action_disabled: true,
                        },
                        1: {
                            action_id:2,
                            action_value: 2,
                            action_description:'Add, Edit, Delete / Modifier Access',
                            action_checked: false,
                            action_disabled: false,
                        },
                    }
                },
                3: {
                    access_id: 4,
                    access_value: 4,
                    access_description: 'Semester Launch Access',
                    access_checked: false,
                    access_disabled: false,
                    access_action:{
                        0: {
                            action_id:1,
                            action_value: 1,
                            action_description:'View / General Access',
                            action_checked: false,
                            action_disabled: true,
                        },
                        1: {
                            action_id:2,
                            action_value: 2,
                            action_description:'Add, Edit, Delete / Modifier Access',
                            action_checked: false,
                            action_disabled: false,
                        },
                    }
                },
                4: {
                    access_id: 5,
                    access_value: 5,
                    access_description: 'Employee Management Access',
                    access_checked: false,
                    access_disabled: false,
                    access_action:{
                        0: {
                            action_id:1,
                            action_value: 1,
                            action_description:'View / General Access',
                            action_checked: false,
                            action_disabled: true,
                        },
                        1: {
                            action_id:2,
                            action_value: 2,
                            action_description:'Add, Edit, Delete / Modifier Access',
                            action_checked: false,
                            action_disabled: false,
                        },
                    }
                },
                5: {
                    access_id: 6,
                    access_value: 6,
                    access_description: 'Registrar Settings Access',
                    access_checked: false,
                    access_disabled: false,
                    access_action:{
                        0: {
                            action_id:1,
                            action_value: 1,
                            action_description:'View / General Access',
                            action_checked: false,
                            action_disabled: true,
                        },
                        1: {
                            action_id:2,
                            action_value: 2,
                            action_description:'Add, Edit, Delete / Modifier Access',
                            action_checked: false,
                            action_disabled: false,
                        },
                    }
                },
            }
        },
        1: {
            module_id: 2,
            module_value: 2,
            module_description: 'Library Module',
            module_access: {
                0: {
                    access_id: 1,
                    access_value: 1,
                    access_description: 'Book Supplies',
                    access_checked: false,
                    access_disabled: false,
                    access_action:{
                        0: {
                            action_id:1,
                            action_value: 1,
                            action_description:'View / General Access',
                            action_checked: false,
                            action_disabled: true,
                        },
                        1: {
                            action_id:2,
                            action_value: 2,
                            action_description:'Add, Edit, Delete / Modifier Access',
                            action_checked: false,
                            action_disabled: false,
                        },
                    }
                },
                1: {
                    access_id: 2,
                    access_value: 2,
                    access_description: 'Book Borrowers',
                    access_checked: false,
                    access_disabled: false,
                    access_action:{
                        0: {
                            action_id:1,
                            action_value: 1,
                            action_description:'View / General Access',
                            action_checked: false,
                            action_disabled: true,
                        },
                        1: {
                            action_id:2,
                            action_value: 2,
                            action_description:'Add, Edit, Delete / Modifier Access',
                            action_checked: false,
                            action_disabled: false,
                        },
                    }
                },
                2: {
                    access_id: 3,
                    access_value: 3,
                    access_description: 'Book DDC',
                    access_checked: false,
                    access_disabled: false,
                    access_action:{
                        0: {
                            action_id:1,
                            action_value: 1,
                            action_description:'View / General Access',
                            action_checked: false,
                            action_disabled: true,
                        },
                        1: {
                            action_id:2,
                            action_value: 2,
                            action_description:'Add, Edit, Delete / Modifier Access',
                            action_checked: false,
                            action_disabled: false,
                        },
                    }
                },
                3: {
                    access_id: 4,
                    access_value: 4,
                    access_description: 'Library Cards',
                    access_checked: false,
                    access_disabled: false,
                    access_action:{
                        0: {
                            action_id:1,
                            action_value: 1,
                            action_description:'View / General Access',
                            action_checked: false,
                            action_disabled: true,
                        },
                        1: {
                            action_id:2,
                            action_value: 2,
                            action_description:'Add, Edit, Delete / Modifier Access',
                            action_checked: false,
                            action_disabled: false,
                        },
                    }
                },
            }
        },
        2: {
            module_id: 3,
            module_value: 3,
            module_description: 'Clinic Module',
            module_access: {
                0: {
                    access_id: 1,
                    access_value: 1,
                    access_description: 'Student Records',
                    access_checked: false,
                    access_disabled: false,
                    access_action:{
                        0: {
                            action_id:1,
                            action_value: 1,
                            action_description:'View / General Access',
                            action_checked: false,
                            action_disabled: true,
                        },
                        1: {
                            action_id:2,
                            action_value: 2,
                            action_description:'Add, Edit, Delete / Modifier Access',
                            action_checked: false,
                            action_disabled: false,
                        },
                    }
                },
                1: {
                    access_id: 2,
                    access_value: 2,
                    access_description: 'Employee Records',
                    access_checked: false,
                    access_disabled: false,
                    access_action:{
                        0: {
                            action_id:1,
                            action_value: 1,
                            action_description:'View / General Access',
                            action_checked: false,
                            action_disabled: true,
                        },
                        1: {
                            action_id:2,
                            action_value: 2,
                            action_description:'Add, Edit, Delete / Modifier Access',
                            action_checked: false,
                            action_disabled: false,
                        },
                    }
                },
                2: {
                    access_id: 3,
                    access_value: 3,
                    access_description: 'Medical Supplies',
                    access_checked: false,
                    access_disabled: false,
                    access_action:{
                        0: {
                            action_id:1,
                            action_value: 1,
                            action_description:'View / General Access',
                            action_checked: false,
                            action_disabled: true,
                        },
                        1: {
                            action_id:2,
                            action_value: 2,
                            action_description:'Add, Edit, Delete / Modifier Access',
                            action_checked: false,
                            action_disabled: false,
                        },
                    }
                },
            }
        },
        3: {
            module_id: 4,
            module_value: 4,
            module_description: 'Accounting Module',
            module_access: {
                0: {
                    access_id: 1,
                    access_value: 1,
                    access_description: 'Accounting',
                    access_checked: false,
                    access_disabled: false,
                    access_action:{
                        0: {
                            action_id:1,
                            action_value: 1,
                            action_description:'View / General Access',
                            action_checked: false,
                            action_disabled: true,
                        },
                        1: {
                            action_id:2,
                            action_value: 2,
                            action_description:'Add, Edit, Delete / Modifier Access',
                            action_checked: false,
                            action_disabled: false,
                        },
                    }
                },
                1: {
                    access_id: 2,
                    access_value: 2,
                    access_description: 'Billing & Cashier',
                    access_checked: false,
                    access_disabled: false,
                    access_action:{
                        0: {
                            action_id:1,
                            action_value: 1,
                            action_description:'View / General Access',
                            action_checked: false,
                            action_disabled: true,
                        },
                        1: {
                            action_id:2,
                            action_value: 2,
                            action_description:'Add, Edit, Delete / Modifier Access',
                            action_checked: false,
                            action_disabled: false,
                        },
                    }
                },
            }
        },
        4: {
            module_id: 5,
            module_value: 5,
            module_description: 'Faculty Module',
            module_access: {
                0: {
                    access_id: 1,
                    access_description: 'Viewing / General Access',
                    access_checked: true,
                    access_disabled: true,
                },
            }
        }
    }
)

</script>
<template>
    <div>
        <div class="p-1 d-flex gap-2 justify-content-between mb-3">
            <div class="input-group w-50">
                <span class="input-group-text" id="searchaddon"><font-awesome-icon icon="fa-solid fa-search" /></span>
                <input type="text" class="form-control" placeholder="Search Here..." aria-label="search"
                    v-model="searchValue" @keyup.enter="search()" aria-describedby="searchaddon"
                    :disabled="preLoading ? true : false">
            </div>
            <div class="d-flex flex-wrap w-50 justify-content-end gap-2">
                <button tabindex="-1" data-bs-toggle="modal" data-bs-target="#editdatamodal" @click="editData()"
                    type="button" class="btn btn-sm btn-primary" :disabled="preLoading ? true : false">
                    <font-awesome-icon icon="fa-solid fa-add" /> Add New
                </button>
                <button class="btn btn-sm btn-info text-white" :disabled="preLoading ? true : false"
                    @click="$emit('close')"><font-awesome-icon icon="fa-solid fa-rotate-left" size="sm" /> Back
                </button>
            </div>
        </div>
        <div class="table-responsive border p-3 small-font">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th style="background-color: #237a5b;" class="text-white">#</th>
                        <th style="background-color: #237a5b;" class="text-white">Name</th>
                        <th style="background-color: #237a5b;" class="text-white">Email</th>
                        <th style="background-color: #237a5b;" class="text-white">Status</th>
                        <th style="background-color: #237a5b;" class="text-white">Commands</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="!preLoading && Object.keys(users).length" v-for="(app, index) in users">
                        <td class="align-middle">
                            {{ app.id }}
                        </td>
                        <td class="align-middle">
                            {{ app.name }}
                        </td>
                        <td class="align-middle">
                            {{ app.email }}
                        </td>
                        <td class="align-middle">
                            {{ app.status == 1 ? 'Active' : 'Inactive' }}
                        </td>
                        <td class="align-middle">
                            <div class="d-flex gap-2 justify-content-center">
                                <button data-bs-toggle="modal" data-bs-target="#editdatamodal" @click="editData(app)"
                                    type="button" title="Edit Record" class="btn btn-secondary btn-sm">
                                    <font-awesome-icon icon="fa-solid fa-pen" /></button>
                                <button data-bs-toggle="modal" data-bs-target="#accessdatamodal"
                                    @click="accessData(app)" type="button" title="Edit Record"
                                    class="btn btn-secondary btn-sm">
                                    <font-awesome-icon icon="fa-solid fa-key" /></button>
                                <button @click="banAccount(app.id)" type="button" title="Delete Record"
                                    class="btn btn-secondary btn-sm"> <font-awesome-icon
                                        icon="fa-solid fa-trash" /></button>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="!preLoading && !Object.keys(users).length">
                        <td class="p-3 text-center" colspan="5">
                            No Records Found
                        </td>
                    </tr>
                    <tr v-if="preLoading && !Object.keys(users).length">
                        <td class="p-3 text-center" colspan="5">
                            <div class="m-3">
                                <Loader />
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="d-flex justify-content-between align-content-center" v-if="!preLoading">
                <div class="d-flex gap-1">
                    <button :disabled="offset == 0 ? true : false" @click="paginate('prev')"
                        class="btn btn-sm btn-secondary">Prev</button>
                    <button :disabled="Object.keys(users).length < 10 ? true : false" @click="paginate('next')"
                        class="btn btn-sm btn-secondary">Next</button>
                </div>
                <p class="">showing total of <span class="font-semibold">({{ usersCount }})</span> items</p>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editdatamodal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Application</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form @submit.prevent="handleAccount" class="d-flex flex-column align-items-start">
                        <div class="mb-3 d-flex flex-column align-items-start w-100">
                            <label for="username" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" aria-describedby="name" v-model="name"
                                required>
                        </div>
                        <div class="mb-3 d-flex flex-column align-items-start w-100">
                            <label for="username" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" aria-describedby="email" v-model="email"
                                required>
                        </div>
                        <div v-if="addNew" class="w-100">
                            <div class="mb-3 d-flex flex-column align-items-start w-100">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" aria-describedby="password"
                                    v-model="password" required>
                            </div>
                            <div class="mb-3 d-flex flex-column align-items-start w-100">
                                <label for="confirm" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" id="confirm" aria-describedby="confirm"
                                    v-model="confirmpassword" required>
                            </div>
                        </div>
                        <div class="w-100" v-if="addNew">
                            <div class="alert alert-danger" role="alert"
                                v-if="password != confirmpassword || !password || !confirmpassword">
                                Password does not match
                            </div>
                            <div class="alert alert-success" role="alert" v-else>
                                Password Matched
                            </div>
                        </div>
                        <button type="submit" class="btn btn-success w-100"
                            :disabled="(saving || (password != confirmpassword || !password || !confirmpassword)) && addNew ? true : false">
                            Save
                        </button>
                    </form>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <div class="form-group">
                        <small id="emailHelp" class="form-text text-muted">We'll never share your personal information
                            with anyone
                            else (Data Privacy Act of 2012)</small>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- access Modal -->
    <div class="modal fade" id="accessdatamodal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Key Access</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" @click="accessData"></button>
                </div>
                <div class="modal-body">
                    <form @submit.prevent="handleAccess" class="d-flex flex-column align-items-start small-font">
                        <div class="mb-3 w-100">
                            <div class="accordion accordion-flush" id="accordionFlushExample">
                                <div class="accordion-item" v-for="(mod, modindex) in accessModuleData">
                                    <h2 class="accordion-header" id="flush-headingOne">
                                        <button class="accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse" :data-bs-target="'#module' + mod.module_id"
                                            aria-expanded="false" :aria-controls="'module' + mod.module_id">
                                            {{ mod.module_description }}
                                        </button>
                                    </h2>
                                    <div :id="'module' + mod.module_id" class="accordion-collapse collapse"
                                        aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                                        <div class="accordion-body">
                                            <ul class="list-group">
                                                <li class="list-group-item" v-for="(acc, accindex) in mod.module_access">
                                                    <div class="border p-3 ">
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox" :checked="acc.access_checked"
                                                                @change="modify(modindex, 1, accindex)"
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
                                                                        :data-bs-target="'#action'+acc.access_id"
                                                                        aria-expanded="false"
                                                                        :aria-controls="'action'+acc.access_id">
                                                                        <span class="small-font">Actions Granted</span>
                                                                        <code class="mx-3 text-danger" v-show="!acc.access_checked">(Enable Access First)</code>
                                                                        <code class="mx-3 text-success" v-show="acc.access_checked">(Access Enabled)</code>
                                                                    </button>
                                                                </p>
                                                                <div :id="'action'+acc.access_id"
                                                                    class="accordion-collapse collapse"
                                                                    aria-labelledby="flush-crud"
                                                                    data-bs-parent="#crudaccordion">
                                                                    <div class="p-3" v-for="(act, actindex) in acc.access_action">
                                                                        <div class="form-check form-switch">
                                                                            <input class="form-check-input" type="checkbox" :checked="acc.access_checked"
                                                                                @change="modify(accindex, 2, actindex)"
                                                                                :disabled="act.action_disabled || !acc.access_checked" :value="act.action_value">
                                                                            <label class="form-check-label">{{
                                                                                act.action_description }}</label>
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

                        <button type="submit" class="btn btn-success w-100"
                            :disabled="saving ? true : false">
                            Save
                        </button>
                    </form>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <div class="form-group">
                        <small id="emailHelp" class="form-text text-muted">Make sure that the level of access are
                            correct to avoid irreversible
                            actions done by unauthorized staff.
                        </small>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
                    </div>
                </div>
            </div>
        </div>
    </div>

</template>