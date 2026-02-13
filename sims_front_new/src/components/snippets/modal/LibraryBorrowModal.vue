<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import NeuLoader2 from '../loaders/Loading1.vue';
import {
    getStudent,
    getBooksAccession,
    addBorrowedBooks,
    getLibraryCardIssue
} from "../../Fetchers.js";
import { getUserID } from "../../../routes/user";

const props = defineProps({
    bookdata: {
    },
    borrowdata: {
    },
    useriddata: {
    }
})

const books = computed(() => {
    return props.bookdata
});
const borrowedBooksId = computed(() => {
    return props.borrowdata
});
const userId = computed(() => {
    return props.useriddata
});



onMounted(async () => {

})
const saving = ref(false)
const loadingStudents = ref(false)
const loadingBooks = ref(false)
const checkingLibraryCard = ref(false)
const borrowerPerid = ref('')
const borrowerEnrid = ref('')
const borrowerName = ref('')
const searchStudent = ref('')
const searchBook = ref('')
const filteredStudent = ref([])
const filteredBook = ref([])
const bookCart = ref([])
const bookCartId = ref([])
const students = ref([])
const cardId = ref('')
const cardIdNo = ref('')

const reset = () => {
    filteredStudent.value = []
    filteredBook.value = []
    bookCart.value = []
    bookCartId.value = []
    students.value = []
    searchBook.value = ''
    cardId.value = ''
    cardIdNo.value = ''
    borrowerPerid.value = ''
    borrowerEnrid.value = ''
    borrowerName.value = ''
    filterStudent()
}

const filterStudent = () => {

    if (searchStudent.value) {
        loadingStudents.value = true
        getStudent(1, 1, searchStudent.value).then((results) => {
            students.value = results.data
            filteredStudent.value = results.data
            loadingStudents.value = false
        })
    } else {
        // alert('Please input a valid search.')
        // filteredStudent.value = []
        // loadingStudents.value = false
        Swal.fire({
            title: "Requirement",
            text: "Please input a valid search",
            icon: "question"
        }).then(()=>{
            filteredStudent.value = []
            loadingStudents.value = false
        });
    }
}

const filterBook = () => {
    if (searchBook.value) {
        loadingBooks.value = true
        getBooksAccession(1, 1, searchBook.value).then((results) => {
            filteredBook.value = results.data
            borrowedBooksId.value.forEach((e) => {
                let itemIndex = ''

                let indexer = filteredBook.value.findIndex((f, index) => {
                    itemIndex = index
                    return e === f.lbrb_id
                });

                if (indexer !== -1) {
                    filteredBook.value.splice(itemIndex, 1)
                }
            })

            loadingBooks.value = false
        })
    } else {
        // alert('Please input a valid search.')
        // filteredBook.value = []
        Swal.fire({
            title: "Requirement",
            text: "Please input a valid search",
            icon: "question"
        }).then(()=>{
            filteredBook.value = []
        });
    }

}

const addToCart = (mode, item, index) => {
    if (mode == 'add') {
        let exist = bookCart.value.filter(e => {
            if (e.lbrb_id == item.lbrb_id) {
                return e
            }
        })

        Object.keys(exist).length ? 
        Swal.fire({
            title: "Duplicate Detected",
            text: "Already included, try selecting another",
            icon: "question"
        }) : bookCart.value.push(item)
        Object.keys(exist).length ? false : bookCartId.value.push(item.lbrb_id)

    } else {
        bookCart.value.splice(index, 1)
        bookCartId.value.splice(index, 1)
    }
}

const setBorrower = (perid, enrid, fname, lname) => {
    borrowerPerid.value = perid
    borrowerEnrid.value = enrid
    borrowerName.value = fname + ' ' + lname

    checkingLibraryCard.value = true
    getLibraryCardIssue(perid, enrid, 1).then((results) => {
        if (Object.keys(results).length) {
            cardId.value = results[0].lbrd_id
            cardIdNo.value = results[0].lbrd_cardno
        } else {
            cardId.value = ''
            cardIdNo.value = ''
            // alert('This person does not have any active issued library card, in order to borrow a book, the person must have at least one active library card.')
            Swal.fire({
                title: "Notice",
                text: "This person does not have any active issued library card, in order to borrow a book, the person must have at least one active library card",
                icon: "warning"
            })
        }
        checkingLibraryCard.value = false
    })
}

const borrowBook = () => {
    if (
        borrowerPerid.value &&
        borrowerEnrid.value &&
        Object.keys(bookCart.value).length
    ) {
        saving.value = true
        Swal.fire({
            title: "Saving Updates",
            text: "Please wait while we check all necessary details.",
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        let x = bookCart.value.map((e) => {
            return {
                lbrr_cardid: cardId.value,
                lbrr_personid: borrowerPerid.value,
                lbrr_enrid: borrowerEnrid.value,
                lbrr_accessionid: e.lbrb_accession_no,
                lbrr_bookid: e.lbrb_id,
                lbrr_user: userId.value,
                lbrr_day_borrowed: 1,
            }
        })

        addBorrowedBooks(x).then((results) => {
            if (results.status != 200) {
                // alert('Borrow Failed')
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
                // alert('Borrow Successful')
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

    } else {
        // alert('Please select borrower and books to borrow.')
        Swal.fire({
            title: "Requirement",
            text: "Please select borrower and books to borrow",
            icon: "question"
        }).then(()=>{
            // location.reload()
        });
    }
}

</script>
<template>
    <form @submit.prevent="registerApplicant()" class="w-100 gap-2 p-4 small-font neu-card-inner">
        <div class="row gap-2">
            <div class="col p-4 neu-card">
                <div class="mb-2 p-3 align-content-center">
                    <span class="fw-bold text-success">Step 1: </span>Search Borrower Information
                </div>
                <div class="d-flex flex-column gap-2 p-4 neu-card-inner">
                    <div class="w-100 text-start form-group">
                        <label>Borrower Name</label>
                        <input required v-model="searchStudent" onkeydown="return /[a-z, ]/i.test(event.key)"
                            type="text" class="neu-input" />
                    </div>
                    <div class="w-100 text-start form-group">
                        <label>Borrower Card</label>
                        <input required disabled v-model="cardIdNo" type="text"
                            class="neu-input" />
                    </div>
                    <div class="w-100 text-start form-group">
                        <button :disabled="saving ? true : false" @click="reset()" class="neu-btn neu-purple p-2"
                            type="button"><font-awesome-icon icon="fa-solid fa-user-plus"  /> Search Student</button>
                    </div>
                </div>
                <div class="border d-flex flex-column gap-2 overflow-auto mt-3 neu-card-inner p-3" style="height:250px;">
                    <table class="neu-table-flat table-fixed">
                        <thead>
                            <tr>
                                <th>Select Student</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="!loadingStudents && Object.keys(filteredStudent).length"
                                v-for="(fs, index) in filteredStudent" :class="savingLoads ? 'pe-none' : 'pe-auto'"
                                @click="setBorrower(fs.per_id, fs.enr_id, fs.per_firstname, fs.per_lastname)">

                                <td
                                    :class="fs.per_id == borrowerPerid ? 'align-middle text-start neu-pastel-gray' : 'align-middle text-start '">
                                    {{ fs.per_firstname }} {{ fs.per_middlename }} {{ fs.per_lastname }} {{
                                    fs.per_suffixname }}
                                </td>
                            </tr>
                            <tr v-if="!loadingStudents && !Object.keys(filteredStudent).length">
                                <td class="p-3 text-center" colspan="1">
                                    No Records Found
                                </td>
                            </tr>
                            <tr v-if="loadingStudents && !Object.keys(filteredStudent).length">
                                <td class="p-3 text-center" colspan="1">
                                    <div class="m-3">
                                        <NeuLoader2 />
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col p-4 neu-card">
                <div class="mb-2 p-3 align-content-center">
                    <span class="fw-bold text-success">Step 2: </span>Search and add books to borrow
                </div>
                <div class="d-flex flex-column gap-2 p-4 neu-card-inner">
                    <div class="w-100 text-start form-group">
                        <label>Book Information</label>
                        <input required v-model="searchBook" onkeydown="return /[a-z, ]/i.test(event.key)"
                            type="text" class="neu-input" />
                    </div>
                    <div class="w-100 text-start form-group">
                        <label>Book Count</label>
                        <input  required disabled v-model="Object.keys(bookCart).length"
                        onkeydown="return /[a-z, ]/i.test(event.key)" type="text" 
                            class="neu-input" />
                    </div>
                    <div class="w-100 text-start form-group">
                        <button :disabled="(saving ? true : false)" @click="filterBook()" class="neu-btn neu-blue p-2"
                            type="button"><font-awesome-icon icon="fa-solid fa-book"  /> Search Book</button>
                    </div>
                </div>
                <div class="border d-flex flex-column gap-2 overflow-auto mt-3 neu-card-inner p-3" style="height:250px;">
                    <table class="neu-table-flat table-fixed">
                        <thead>
                            <tr>
                                <th>Select Student</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="!loadingBooks && Object.keys(filteredBook).length"
                                v-for="(bk, index) in filteredBook" :class="savingLoads ? 'pe-none' : 'pe-auto'"
                                @click="addToCart('add', bk, index)">

                                <td
                                    :class="bookCartId.includes(bk.lbrb_id) ? 'align-middle text-start neu-pastel-gray' : 'align-middle text-start '">
                                    <p class="fw-bold fw-bold">Accession No: {{ bk.lbrb_accession_no }}</p>
                                    <p class="">{{ bk.lbrb_title }}</p>
                                </td>
                            </tr>
                            <tr v-if="!loadingBooks && !Object.keys(filteredBook).length">
                                <td class="p-3 text-center" colspan="1">
                                    No Records Found
                                </td>
                            </tr>
                            <tr v-if="loadingBooks && !Object.keys(filteredBook).length">
                                <td class="p-3 text-center" colspan="1">
                                    <div class="m-3">
                                        <NeuLoader2 />
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col p-4 neu-card">
                <div class="mb-2 p-3 align-content-center">
                    <span class="fw-bold text-success">Step 3: </span>Confirm books inside the cart
                </div>
                <div class="d-flex flex-column gap-2 p-4 neu-card-inner">
                    <div class="w-100 text-start form-group">
                        <label>Borrower Name</label>
                        <input  disabled v-model="borrowerName" onkeydown="return /[a-z, ]/i.test(event.key)"
                            type="text" class="neu-input" />
                    </div>
                    <div class="w-100 text-start form-group">
                        <label>Borrower Card</label>
                        <input required disabled v-model="cardIdNo"
                        onkeydown="return /[a-z, ]/i.test(event.key)" type="text" 
                            class="neu-input" />
                    </div>
                    <div class="w-100 text-start form-group">
                        <button :disabled="(saving ? true : false) ||
                                (!Object.keys(bookCart).length ? true : false) ||
                                (!cardId ? true : false)" @click="borrowBook()" class="neu-btn neu-green p-2"
                            type="button"><font-awesome-icon icon="fa-solid fa-hand"  /> Borrow</button>
                    </div>
                </div>
                <div class="border d-flex flex-column gap-2 overflow-auto mt-3 neu-card-inner p-3" style="height:250px;">
                    <table class="neu-table-flat table-fixed">
                        <thead>
                            <tr>
                                <th>Select Student</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="!loadingBooks && Object.keys(bookCart).length"
                                v-for="(bk, index) in bookCart" :class="savingLoads ? 'pe-none' : 'pe-auto'" >
                                <td
                                    class="align-middle text-start neu-card text-black d-flex flex-column gap-3">
                                    <p class="fw-bold fw-bold">Accession No: {{ bk.lbrb_accession_no }}</p>
                                    <p class="">{{ bk.lbrb_title }}</p>
                                    <button @click="addToCart('remove', bk, index)"
                                        class="neu-btn neu-red"
                                        type="button" title="Click to remove">
                                        Remove
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="!loadingBooks && !Object.keys(bookCart).length">
                                <td class="p-3 text-center" colspan="1">
                                    No Records Found
                                </td>
                            </tr>
                            <tr v-if="loadingBooks && !Object.keys(bookCart).length">
                                <td class="p-3 text-center" colspan="1">
                                    <div class="m-3">
                                        <NeuLoader2 />
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </form>
</template>