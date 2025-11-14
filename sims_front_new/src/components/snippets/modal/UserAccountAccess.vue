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
    accountDetail:{
    },
    title: {
    }
})
 
const userID = computed(() => {
    return props.userIdData
});

const accountModuleDetail = computed(() => {
    return props.accountDetail
});

const emit = defineEmits(['close'])
const saving = ref(false)

const accessModuleData = ref(
    {
        0: {
            user_id:0,
            sett_addedby:userID.value,
            module_id: 1,
            module_value: 1,
            module_category: 1,
            module_category_desc: 'Administrative',
            module_description: 'Registrar Module',
            module_grant: 0,
            module_access: {
                0: {
                    useracc_id:0,
                    access_id: 1,
                    access_value: 1,
                    access_description: 'Student Admission',
                    access_checked: 0,
                    access_disabled: 1,
                    access_viewing:0,
                    access_modifying:0
                },
                1: {
                    useracc_id:0,
                    access_id: 2,
                    access_value: 2,
                    access_description: 'Enrollment',
                    access_checked: 0,
                    access_disabled: 1,
                    access_viewing:0,
                    access_modifying:0
                },
                2: {
                    useracc_id:0,
                    access_id: 3,
                    access_value: 3,
                    access_description: 'Student Request',
                    access_checked: 0,
                    access_disabled: 1,
                    access_viewing:0,
                    access_modifying:0
                },
                3: {
                    useracc_id:0,
                    access_id: 4,
                    access_value: 4,
                    access_description: 'Semester Launch',
                    access_checked: 0,
                    access_disabled: 1,
                    access_viewing:0,
                    access_modifying:0
                },
                4: {
                    useracc_id:0,
                    access_id: 5,
                    access_value: 5,
                    access_description: 'Employee Management',
                    access_checked: 0,
                    access_disabled: 1,
                    access_viewing:0,
                    access_modifying:0
                },
                5: {
                    useracc_id:0,
                    access_id: 6,
                    access_value: 6,
                    access_description: 'Registrar Settings',
                    access_checked: 0,
                    access_disabled: 1,
                    access_viewing:0,
                    access_modifying:0
                },
                6: {
                    useracc_id:0,
                    access_id: 7,
                    access_value: 7,
                    access_description: 'Alumni Tracker',
                    access_checked: 0,
                    access_disabled: 1,
                    access_viewing:0,
                    access_modifying:0
                },
            }
        },
        1: {
            user_id:0,
            sett_addedby:userID.value,
            module_id: 2,
            module_value: 2,
            module_category: 1,
            module_category_desc: 'Administrative',
            module_description: 'Library Module',
            module_grant: 0,
            module_access: {
                0: {
                    useracc_id:0,
                    access_id: 1,
                    access_value: 1,
                    access_description: 'Book Supplies',
                    access_checked: 0,
                    access_disabled: 1,
                    access_viewing:0,
                    access_modifying:0
                },
                1: {
                    useracc_id:0,
                    access_id: 2,
                    access_value: 2,
                    access_description: 'Book Borrowers',
                    access_checked: 0,
                    access_disabled: 1,
                    access_viewing:0,
                    access_modifying:0
                },
                2: {
                    useracc_id:0,
                    access_id: 3,
                    access_value: 3,
                    access_description: 'Book DDC',
                    access_checked: 0,
                    access_disabled: 1,
                    access_viewing:0,
                    access_modifying:0
                },
                3: {
                    useracc_id:0,
                    access_id: 4,
                    access_value: 4,
                    access_description: 'Library Cards',
                    access_checked: 0,
                    access_disabled: 1,
                    access_viewing:0,
                    access_modifying:0
                },
            }
        },
        2: {
            user_id:0,
            sett_addedby:userID.value,
            module_id: 3,
            module_value: 3,
            module_category: 1,
            module_category_desc: 'Administrative',
            module_description: 'Clinic Module',
            module_grant: 0,
            module_access: {
                0: {
                    useracc_id:0,
                    access_id: 1,
                    access_value: 1,
                    access_description: 'Student Records',
                    access_checked: 0,
                    access_disabled: 1,
                    access_viewing:0,
                    access_modifying:0
                },
                1: {
                    useracc_id:0,
                    access_id: 2,
                    access_value: 2,
                    access_description: 'Employee Records',
                    access_checked: 0,
                    access_disabled: 1,
                    access_viewing:0,
                    access_modifying:0
                },
                2: {
                    useracc_id:0,
                    access_id: 3,
                    access_value: 3,
                    access_description: 'Medical Supplies',
                    access_checked: 0,
                    access_disabled: 1,
                    access_viewing:0,
                    access_modifying:0
                },
            }
        },    
        3: {
            user_id:0,
            sett_addedby:userID.value,
            module_id: 1,
            module_value: 1,
            module_category: 2,
            module_category_desc: 'Transactions',
            module_description: 'Accounting Module',
            module_grant: 0,
            module_access: {
                0: {
                    useracc_id:0,
                    access_id: 1,
                    access_value: 1,
                    access_description: 'Daily Collections',
                    access_checked: 0,
                    access_disabled: 1,
                    access_viewing:0,
                    access_modifying:0
                },
                1: {
                    useracc_id:0,
                    access_id: 2,
                    access_value: 2,
                    access_description: 'Miscellaneous',
                    access_checked: 0,
                    access_disabled: 1,
                    access_viewing:0,
                    access_modifying:0
                },
                2: {
                    useracc_id:0,
                    access_id: 3,
                    access_value: 3,
                    access_description: 'Tuition Setup',
                    access_checked: 0,
                    access_disabled: 1,
                    access_viewing:0,
                    access_modifying:0
                }
            }
        },
        4: {
            user_id:0,
            sett_addedby:userID.value,
            module_id: 2,
            module_value: 2,
            module_category: 2,
            module_category_desc: 'Transactions',
            module_description: 'Billing/Cashier Module',
            module_grant: 0,
            module_access: {
                0: {
                    useracc_id:0,
                    access_id: 1,
                    access_value: 1,
                    access_description: 'Billing / Daily Collections',
                    access_checked: 0,
                    access_disabled: 1,
                    access_viewing:0,
                    access_modifying:0
                },
                1: {
                    useracc_id:0,
                    access_id: 2,
                    access_value: 2,
                    access_description: 'Billing / Cashier Request',
                    access_checked: 0,
                    access_disabled: 1,
                    access_viewing:0,
                    access_modifying:0
                },
                2: {
                    useracc_id:0,
                    access_id: 3,
                    access_value: 3,
                    access_description: 'Billing / Cashier Tuition',
                    access_checked: 0,
                    access_disabled: 1,
                    access_viewing:0,
                    access_modifying:0
                }
            }
        },
        5: {
            user_id:0,
            sett_addedby:userID.value,
            module_id: 1,
            module_value: 1,
            module_category: 3,
            module_category_desc: 'Academics',
            module_description: 'Faculty Module',
            module_grant: 0,
            module_access: {
                0: {
                    useracc_id:0,
                    access_id: 1,
                    access_description: 'Faculty Loadings & Preparations',
                    access_checked: 0,
                    access_disabled: 1,
                    access_viewing:0,
                    access_modifying:0
                },
                1: {
                    useracc_id:0,
                    access_id: 2,
                    access_value: 2,
                    access_description: 'Faculty Grading Sheet',
                    access_checked: 0,
                    access_disabled: 1,
                    access_viewing:0,
                    access_modifying:0
                },
                2: {
                    useracc_id:0,
                    access_id: 3,
                    access_value: 3,
                    access_description: 'Faculty Student Masterlist',
                    access_checked: 0,
                    access_disabled: 1,
                    access_viewing:0,
                    access_modifying:0
                }
            }
        }
    }
)

onMounted(async () => {
    // console.log(accessModuleData.value)
    // console.log(accountModuleDetail.value)
    
    accessModuleData.value[0].user_id = accountModuleDetail.value.id // registrar
    accessModuleData.value[1].user_id = accountModuleDetail.value.id // library
    accessModuleData.value[2].user_id = accountModuleDetail.value.id // clinic
    accessModuleData.value[3].user_id = accountModuleDetail.value.id // billing
    accessModuleData.value[4].user_id = accountModuleDetail.value.id // accounting
    accessModuleData.value[5].user_id = accountModuleDetail.value.id // academics

    getCommandAccess(accountModuleDetail.value.id).then((results)=>{
        if(results.length > 0){
            Object.keys(accessModuleData.value).forEach(function(index1) {
                Object.keys(accessModuleData.value[index1].module_access).forEach(function(index2) {
                    let access_value = results.filter((e)=>{
                        if( (accessModuleData.value[index1].module_id == e.useracc_modulecode)
                            && 
                            (accessModuleData.value[index1].module_access[index2].access_id == e.useracc_accesscode)
                            && 
                            (accessModuleData.value[index1].module_category == e.useracc_category)){
                            return{
                                e
                            }
                        }
                    })

                    parseInt(access_value[0].useracc_viewing) == 1? accessModuleData.value[index1].module_access[index2].access_checked = true : accessModuleData.value[index1].module_access[index2].access_checked = false
                    // console.log(accessModuleData.value[index1].module_access[index2].access_checked)
                    accessModuleData.value[index1].module_access[index2].access_viewing = parseInt(access_value[0].useracc_viewing)
                    accessModuleData.value[index1].module_access[index2].access_modifying = parseInt(access_value[0].useracc_modifying)
                    accessModuleData.value[index1].module_grant = parseInt(access_value[0].useracc_grant)
                    accessModuleData.value[index1].module_access[index2].access_disabled = parseInt(access_value[0].useracc_grant)==1? 0:1 // if may grant na 1, mawawala disabled
                    accessModuleData.value[index1].module_access[index2].useracc_id = access_value[0].useracc_id
                }); 
            }); 
        }

        // console.log(accessModuleData.value)
    })        
})

const handleAccess = async () => {
    // console.log(accessModuleData.value)
    saveCommandAccess(accessModuleData.value).then((results)=>{
        if (results.status == 200) {
            // alert('Update Successful')
            // location.reload()
            Swal.fire({
                title: "Update Successful",
                text: "Changes applied, refreshing the page",
                icon: "success"
            }).then(()=>{
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
                // location.reload()
            });
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
      <!-- OUTER ACCORDION -->
      <div class="accordion accordion-flush" id="moduleAccordion">
        <div
          class="accordion-item"
          v-for="(mod, modindex) in accessModuleData"
          :key="modindex"
        >
          <h2 class="accordion-header" :id="'module-heading-' + modindex">
            <button
              class="accordion-button collapsed"
              type="button"
              data-bs-toggle="collapse"
              :data-bs-target="'#module-collapse-' + modindex"
              aria-expanded="false"
              :aria-controls="'module-collapse-' + modindex"
            >
              {{ mod.module_description }}
            </button>
          </h2>

          <div
            :id="'module-collapse-' + modindex"
            class="accordion-collapse collapse"
            :aria-labelledby="'module-heading-' + modindex"
            data-bs-parent="#moduleAccordion"
          >
            <div class="accordion-body">
              <ul class="list-group">
                <li class="list-group-item">
                  <div class="p-3">
                    <div class="form-check form-switch">
                      <input
                        class="form-check-input"
                        type="checkbox"
                        @change="mode(modindex)"
                        :checked="mod.module_grant"
                        :value="mod.module_grant"
                      />
                      <label class="form-check-label">Grant Module Access</label>
                    </div>
                  </div>
                </li>

                <li
                  class="list-group-item"
                  v-for="(acc, accindex) in mod.module_access"
                  :key="accindex"
                >
                  <div class="border p-3">
                    <div class="form-check form-switch">
                      <input
                        class="form-check-input"
                        type="checkbox"
                        :checked="acc.access_checked"
                        @change="modify(modindex, accindex, acc.access_checked)"
                        :disabled="acc.access_disabled"
                        :value="acc.access_value"
                      />
                      <label class="form-check-label fw-bold">
                        {{ acc.access_description }}
                      </label>
                    </div>
                  </div>

                  <!-- INNER ACCORDION -->
                  <div class="border">
                    <div
                      class="accordion accordion-flush"
                      :id="'actionAccordion-' + modindex + '-' + accindex"
                    >
                      <div class="accordion-item">
                        <h2
                          class="accordion-header"
                          :id="'action-heading-' + modindex + '-' + accindex"
                        >
                          <button
                            class="accordion-button collapsed"
                            type="button"
                            data-bs-toggle="collapse"
                            :data-bs-target="'#action-collapse-' + modindex + '-' + accindex"
                            aria-expanded="false"
                            :aria-controls="'action-collapse-' + modindex + '-' + accindex"
                          >
                            <span class="small-font">Actions Granted</span>
                            <code class="mx-3 text-danger" v-show="!acc.access_viewing">
                              (Enable Access First)
                            </code>
                            <code class="mx-3 text-success" v-show="acc.access_viewing">
                              (Access Enabled)
                            </code>
                          </button>
                        </h2>

                        <div
                          :id="'action-collapse-' + modindex + '-' + accindex"
                          class="accordion-collapse collapse"
                          :aria-labelledby="'action-heading-' + modindex + '-' + accindex"
                          :data-bs-parent="'#actionAccordion-' + modindex + '-' + accindex"
                        >
                          <div class="p-3">
                            <div class="form-check form-switch">
                              <input
                                class="form-check-input"
                                type="checkbox"
                                @change="acc.access_viewing==0 ? acc.access_viewing=1 : acc.access_viewing=0"
                                :checked="acc.access_viewing"
                                disabled="true"
                                :value="acc.access_viewing"
                              />
                              <label class="form-check-label">
                                View / General Access
                              </label>
                            </div>
                          </div>

                          <div class="p-3">
                            <div class="form-check form-switch">
                              <input
                                class="form-check-input"
                                type="checkbox"
                                @change="acc.access_modifying==0 ? acc.access_modifying=1 : acc.access_modifying=0"
                                :checked="acc.access_modifying"
                                :disabled="!acc.access_checked"
                                :value="acc.access_modifying"
                              />
                              <label class="form-check-label">
                                Add, Edit, Delete / Modifier Access
                              </label>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <!-- END INNER ACCORDION -->
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>

    <button
      type="submit"
      class="btn btn-success w-100"
      :disabled="saving ? true : false"
    >
      Save
    </button>
  </form>
</template>

