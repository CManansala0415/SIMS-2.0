<script setup>
import { ref, onMounted, computed } from 'vue';
import { getStudentFiltering } from "../../Fetchers.js";
import NeuLoader2 from '../loaders/NeuLoader2.vue';
import Loading1 from '../loaders/Loading1.vue';

const props = defineProps({
    gradelvldata: {
    },    
    programdata: {
    },
    coursedata: {
    },
    degreedata: {
    },
})

const gradelvl = computed(() => {
  return props.gradelvldata
});
const program = computed(() => {
  return props.programdata
});
const course = computed(() => {
  return props.coursedata
});
const degree = computed(() => {
  return props.degreedata
});

const students = ref([])
const limit = ref(10)
const offset = ref(0)
const preLoading = ref(false)
const paramsProgram = ref(0)
const paramsGradelvl = ref(0)
const paramsCourse = ref(0)
const paramsDegree = ref(0)
const studentCount = ref(0)

onMounted(async () => {
    // paginate('search')
})

const paginate = (mode) => {
    switch (mode) {
        case 'prev':
            if (offset.value <= 0) {
                offset.value = 0
            } else {
                students.value = []
                offset.value -= 10
                studentCount.value = 0
                preLoading.value = true
                getStudentFiltering(limit.value, offset.value, 0, 0, 0, paramsProgram.value, paramsGradelvl.value, paramsCourse.value, 2).then((results) => {
                    students.value = results.data
                    studentCount.value = results.count
                    preLoading.value = false
                })
            }
            break;
        case 'next':
            if (offset.value >= studentCount.value) {
                offset.value = studentCount.value
            } else {
                students.value = []
                offset.value += 10
                studentCount.value = 0
                preLoading.value = true
                getStudentFiltering(limit.value, offset.value, 0, 0, 0, paramsProgram.value, paramsGradelvl.value, paramsCourse.value, 2).then((results) => {
                    students.value = results.data
                    studentCount.value = results.count
                    preLoading.value = false
                })
            }
            break;
        case 'search':
                students.value = []
                offset.value = 0
                studentCount.value = 0
                preLoading.value = true
                getStudentFiltering(limit.value, offset.value, 0, 0, 0, paramsProgram.value, paramsGradelvl.value, paramsCourse.value, 2).then((results) => {
                    students.value = results.data
                    studentCount.value = results.count
                    preLoading.value = false
                })
            break;
    }
}
</script>
<template>
    <div>        
        <div class="p-1 d-flex gap-2 justify-content-between mb-3">
            <div class="d-flex gap-2 w-100">
                <div class="d-flex gap-2 w-100">
                    <select @change="paramsGradelvl = 0, paramsCourse= 0" class="neu-input neu-select" tabindex="-1" v-model="paramsProgram" :disabled="preLoading?true:false">
                        <option value="0" >--Select Program--</option>
                        <option v-for="(p, index) in program" :value="p.dtype_id">{{ p.dtype_desc }}</option>
                    </select>
                    <!-- <select class="neu-input neu-select" tabindex="-1" v-model="paramsDegree" :disabled="preLoading?true:false">
                        <option value="0" >--Select Degree--</option>
                        <option v-for="(d, index) in degree" :value="d.deg_id" v-show="d.deg_type == paramsProgram">{{ d.deg_desc }}</option>
                    </select> -->
                    <select class="neu-input neu-select" tabindex="-1" v-model="paramsCourse" :disabled="preLoading?true:false">
                        <option value="0" >--Select Course--</option>
                        <option v-for="(c, index) in course" :value="c.prog_id" v-show="c.prog_progtype == paramsProgram">{{ c.prog_code }}</option>
                    </select>
                    <select class="neu-input neu-select" tabindex="-1" v-model="paramsGradelvl" :disabled="preLoading?true:false">
                        <option value="0" >--Select Grade Level--</option>
                        <option v-for="(g, index) in gradelvl" :value="g.grad_id" v-show="g.grad_dtypeid == paramsProgram">{{ g.grad_name }}</option>
                    </select>
                    <div class="d-flex justify-content-center align-content-center w-50">
                        <button @click="paginate('search')" type="button" class="neu-btn neu-blue" tabindex="-1" :disabled="preLoading?true:false">
                            <font-awesome-icon icon="fa-solid fa-magnifying-glass"/> Search
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="table-responsive border p-3 small-font">
            <table class="neu-table mb-3" style="text-transform:uppercase">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>First Name</th>
                        <th>Middle Name</th>
                        <th>Last Name</th>
                        <th>Suffix Name</th>
                        <th>Details</th>
                        <th>Date Applied</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="!preLoading && Object.keys(students).length" v-for="(app, index) in students">
                        <td class="align-middle">
                            {{ app.per_id }}
                        </td>
                        <td class="align-middle">
                            {{ app.per_firstname }}
                        </td>
                        <td class="align-middle">
                            {{ app.per_middlename ? app.per_middlename : 'N/A' }}
                        </td>
                        <td class="align-middle">
                            {{ app.per_lastname }}
                        </td>
                        <td class="align-middle">
                            {{ app.per_suffixname ? app.per_suffixname : 'N/A' }}
                        </td>
                        <td class="align-middle p-2">
                            <div class="d-flex flex-column gap-1">
                                <select class="border-0 p-1" disabled tabindex="-1" v-model="app.enr_program"
                                    :id="index + 'program'">
                                    <option v-for="(p, index) in program" :value="p.dtype_id">{{ p.dtype_desc }}
                                    </option>
                                </select>
                                <select class="border-0 p-1" disabled tabindex="-1" v-model="app.enr_course"
                                    :id="index + 'course'">
                                    <option v-for="(c, index) in course" :value="c.prog_id">{{ c.prog_code }}</option>
                                </select>
                                <select class="border-0 p-1" disabled tabindex="-1" v-model="app.enr_gradelvl"
                                    :id="index + 'gradelvl'">
                                    <option v-for="(g, index) in gradelvl" :value="g.grad_id">{{ g.grad_name }}</option>
                                </select>
                                <!-- <select class="border-0 p-1" disabled tabindex="-1" v-model="app.enr_degree"
                                    :id="index + 'quarter'">
                                    <option v-for="(d, index) in degree" :value="d.deg_id" >{{ d.deg_desc }}</option>
                                </select> -->
                            </div>
                        </td>
                        <td class="align-middle">
                            {{ app.per_dateapplied }}
                        </td>
                    </tr>
                    <tr v-if="!preLoading && !Object.keys(students).length">
                        <td class="p-3 text-center" colspan="7">
                            No Records Found
                        </td>
                    </tr>
                    <tr v-if="preLoading && !Object.keys(students).length" style="text-transform:none">
                        <td class="p-3 text-center" colspan="7">
                            <div class="m-3">
                                <NeuLoader2 />
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="d-flex justify-content-between align-content-center" v-if="!preLoading">
                <div class="d-flex gap-1">
                    <button :disabled="offset == 0 ? true : false" @click="paginate('prev')"
                        class="neu-btn neu-light-gray">Prev</button>
                    <button :disabled="Object.keys(students).length < 10 ? true : false" @click="paginate('next')"
                        class="neu-btn neu-dark-gray">Next</button>
                </div>
                <p class="">showing total of <span class="font-semibold">({{ Object.keys(students).length }})</span> items</p>
            </div>
        </div>
    </div>
</template>