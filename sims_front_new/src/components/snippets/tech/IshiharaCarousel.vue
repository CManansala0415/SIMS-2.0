<script setup>
import { ref, computed, onMounted } from 'vue'

/* ===============================
   STATE
================================ */
const emit = defineEmits(['saveData'])
const itemPlates1 = ref([
    {
        no_plate: 1,
        normal: 12,
        def_red_green: 3,
        cler_assessment: null,
        cler_remarks: ''
    },
    {
        no_plate: 2,
        normal: 10,
        def_red_green: 5,
        cler_assessment: null,
        cler_remarks: ''
    }
])

const props = defineProps({
    itemsData: {
    },
    userIdData: {
    }
})
const itemPlates = computed(() => {
    return props.itemsData
});

const userId = computed(() => {
    return props.userIdData
});

const saving = ref(false)
const currentIndex = ref(0)

/* ===============================
   CAROUSEL NAV
================================ */
const next = () => {
    if (currentIndex.value < itemPlates.value.length - 1) {
        currentIndex.value++
    }
}

const prev = () => {
    if (currentIndex.value > 0) {
        currentIndex.value--
    }
}

/* ===============================
   ASSESSMENT LOGIC
   2 = Passed
   1 = Failed
================================ */
const setAssessment = (index, value) => {
    itemPlates.value[index].cler_assessment = value
}

const passAll = () => {
    itemPlates.value.forEach(i => (i.cler_assessment = 2))
}

const failAll = () => {
    itemPlates.value.forEach(i => (i.cler_assessment = 1))
}

const viewAssessment = () => {
    console.table(itemPlates.value)
}

const save = () => {
    saving.value = true
    console.log('Saving...', itemPlates.value)
    emit('saveData', itemPlates.value)
    setTimeout(() => (saving.value = false), 1000)
}

onMounted(() => {
    console.log('Loaded item plates:', itemPlates.value)
    console.log('Loaded item plates 1:', itemPlates1.value)
})
</script>

<template>
    <form @submit.prevent="save" class="neu-carousel-form">

        <div class="text-center text-white">
            Plate {{ currentIndex + 1 }} of {{ itemPlates.length }}
        </div>

        <div class="carousel">
            <div class="carousel-track" :style="{ transform: `translateX(-${currentIndex * 100}%)` }">
                <div v-for="(itm, index) in itemPlates" :key="itm.cler_id" class="carousel-slide">
                    <table class="table-dark w-100">
                        <tbody>
                            <tr>
                                <th>No. of Plates</th>
                                <td>{{ itm.no_plate }}</td>
                            </tr>
                            <tr>
                                <th>Normal</th>
                                <td>{{ itm.normal }}</td>
                            </tr>
                            <tr>
                                <th>Defective (Red & Green)</th>
                                <td>{{ itm.def_red_green }}</td>
                            </tr>
                            <tr>
                                <th>Assessment</th>
                                <td>
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-sm" :class="itm.cler_assessment === 2
                                            ? 'btn-success px-3 py-1 w-100'
                                            : 'btn-outline-success px-3 py-1 w-100'" @click="setAssessment(index, 2)">
                                            Passed
                                        </button>

                                        <button type="button" class="btn btn-sm" :class="itm.cler_assessment === 1
                                            ? 'btn-danger px-3 py-1 w-100'
                                            : 'btn-outline-danger px-3 py-1 w-100'" @click="setAssessment(index, 1)">
                                            Failed
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>Remarks</th>
                                <td>
                                    <textarea v-model="itm.cler_remarks" class="neu-input"
                                        rows="3"></textarea>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between p-2">
            <button type="button" class="btn btn-sm btn-outline-secondary p-2" @click="prev" :disabled="currentIndex === 0">
                ‹ Previous
            </button>

            <button type="button" class="btn btn-sm btn-outline-secondary p-2" @click="next"
                :disabled="currentIndex === itemPlates.length - 1">
                Next ›
            </button>
        </div>

        <div class="bg-dark bg-opacity-25 p-4">
            <div class="d-flex gap-2">
                <!-- <button type="button" class="btn btn-sm btn-outline-secondary w-100 p-2" @click="viewAssessment">
                    View All
                </button> -->
                <button type="button" class="btn btn-sm btn-outline-secondary w-100 p-2" @click="passAll">
                    Pass All
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary w-100 p-2" @click="failAll">
                    Fail All
                </button>
                <button type="submit" class="btn btn-sm btn-outline-secondary w-100 p-2">
                    Save All
                </button>
            </div>
        </div>
    </form>

</template>

<style scoped>

.table-dark th, .table-dark td {
    border-color: #444;
}
.table-dark, td{
    padding: 0.5rem;
    border: 1px solid #444;
}
.neu-carousel-form {
    width: 100%;
    max-width: 100%;
    overflow: hidden;
}

.carousel {
  width: 100%;
  overflow: hidden; /* critical to stay inside parent */
  position: relative;
}

.carousel-track {
  display: flex;
  transition: transform 0.4s ease;
  width: 100%;
  will-change: transform;
}

.carousel-slide {
  flex: 0 0 100%; /* slide takes 100% of container width */
  box-sizing: border-box;
  padding: 1rem;
}

</style>
