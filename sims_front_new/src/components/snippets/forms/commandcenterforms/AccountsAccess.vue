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
                6: {
                    access_id: 7,
                    access_value: 7,
                    access_description: 'Alumni Tracker Access',
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
                    access_description: 'Viewing / General Access',
                    access_checked: true,
                    access_disabled: true,
                },
                1: {
                    access_id: 2,
                    access_description: 'Test',
                    access_checked: false,
                    access_disabled: false,
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
                    access_description: 'Viewing / General Access',
                    access_checked: true,
                    access_disabled: true,
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
                    access_description: 'Viewing / General Access',
                    access_checked: true,
                    access_disabled: true,
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

</template>