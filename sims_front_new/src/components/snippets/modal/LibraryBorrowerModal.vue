<script setup>
import { ref, onMounted, computed } from 'vue';
import {
    updateBorrowedBooks,
} from "../../Fetchers.js";

import NeuLoader2 from '../loaders/NeuLoader2.vue';

const props = defineProps({
    borrowerdata: {
    },
    borrowerid: {
    },
    modedata: {
    },
    useriddata: {
    }
})

const borrowerId = computed(() => {
    return props.borrowerid
});
const borrowerData = computed(() => {
    return props.borrowerdata
});
const mode = computed(() => {
    return props.modedata
});
const userId = computed(() => {
    return props.useriddata
});


const fullName = ref('')
const saving = ref(false)
const daysBorrowed = ref(0)
const dayReturned = ref('')

onMounted(() => {
    fullName.value = borrowerData.value.per_firstname + ' ' + borrowerData.value.per_lastname
})

const computeDays = () => {
    if (borrowerData.value.lbrr_dateborrowed > dayReturned.value) {
        // alert("Can't compute less than the day of borrowing")
        Swal.fire({
            title: "Notice",
            text: "Can't compute less than the day of borrowing",
            icon: "warning"
        }).then(()=>{
            daysBorrowed.value = 0
            dayReturned.value = ''
        });

    } else {
        var day1 = new Date(borrowerData.value.lbrr_dateborrowed);
        var day2 = new Date(dayReturned.value);

        var difference = day2.getTime() - day1.getTime();
        daysBorrowed.value = difference / (1000 * 3600 * 24)
    }
}

const updateData = () => {
    let x = {
        lbrr_id: borrowerId.value,
        lbrr_datereturned: dayReturned.value,
        lbrr_day_borrowed: daysBorrowed.value,
        lbrr_penalty: daysBorrowed.value > 1 ? 1 : 0,
        lbrr_user: userId.value
    }

    saving.value = true
    Swal.fire({
        title: "Saving Updates",
        text: "Please wait while we check all necessary details.",
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    updateBorrowedBooks(x).then((results) => {
        if (results.status != 200) {
            // alert('Return Failed')
            // location.reload()
            Swal.fire({
                title: "Update Failed",
                text: "Unknown error occured, try again later",
                icon: "error"
            }).then(()=>{
                Swal.close()
                location.reload()
            });
        } else {
            // alert('Return Successful')
            // location.reload()
            Swal.fire({
                title: "Update Successful",
                text: "Changes applied, refreshing the page",
                icon: "success"
            }).then(()=>{
                Swal.close()
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
            <p class="text-success fw-bold">Borrower Name</p>
            <p class="fw-bold">{{ fullName }}</p>
            <p class=" fst-italic p-2 small-font"><span class="fw-bold">Note:
                </span><span class="italic">Check and fillout required details to complete the transaction and the book to be marked as available.
                </span></p>
        </div>
        <div class="d-flex flex-wrap flex-column neu-card p-3 small-font">
            <form @submit.prevent="updateData()" class="d-flex flex-column gap-2">
                <div class="d-flex flex-wrap form-group">
                    <label>Borrower Name</label>
                    <input v-model="fullName" disabled type="text"
                        class="neu-input" />
                </div>
                <div class="d-flex flex-wrap form-group">
                    <label>Book Title</label>
                    <textarea v-model="borrowerData.lbrb_title" disabled onkeydown="return /[a-z, ]/i.test(event.key)"
                        type="text"
                        class="neu-input" style="height: 200px;">
                        </textarea>
                </div>
                <div class="d-flex flex-wrap form-group">
                    <label>Book Author</label>
                    <textarea v-model="borrowerData.lbrb_author" disabled onkeydown="return /[a-z, ]/i.test(event.key)"
                        type="text"
                        class="neu-input">
                        </textarea>
                    <!-- <input v-model="borrowerData.lbrb_author" disabled onkeydown="return /[a-z, ]/i.test(event.key)"
                        type="text"
                        class="neu-input" /> -->
                </div>
                <div class="d-flex flex-wrap form-group">
                    <label>Date Borrowed</label>
                    <input v-model="borrowerData.lbrr_dateborrowed" disabled
                        onkeydown="return /[a-z, ]/i.test(event.key)" type="date"
                        class="neu-input" />
                </div>
                <div class="d-flex flex-wrap form-group">
                    <label>Date Returned</label>
                    <input v-model="dayReturned" @change="computeDays" required
                        onkeydown="return /[a-z, ]/i.test(event.key)" type="date"
                        class="neu-input" />
                </div>
                <div class="d-flex flex-wrap form-group">
                    <label>Days Borrowed</label>
                    <input v-model="daysBorrowed" disabled onkeydown="return /[a-z, ]/i.test(event.key)" type="text"
                        class="neu-input" />
                </div>
                <div class="mt-2">
                    <button :disabled="saving ? true : false" type="submit"
                        class="neu-btn neu-green"> <font-awesome-icon icon="fa-solid fa-wrench" /> Return Book
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>