<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import Loader from '../loaders/Loading1.vue';
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
        alert('Please input a valid search.')
        filteredStudent.value = []
        loadingStudents.value = false
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
        alert('Please input a valid search.')
        filteredBook.value = []
    }

}

const addToCart = (mode, item, index) => {
    if (mode == 'add') {
        let exist = bookCart.value.filter(e => {
            if (e.lbrb_id == item.lbrb_id) {
                return e
            }
        })

        Object.keys(exist).length ? alert('Already Included') : bookCart.value.push(item)
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
            alert('This person does not have any active issued library card, in order to borrow a book, the person must have at least one active library card.')
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
                alert('Borrow Failed')
                location.reload()
            } else {
                alert('Borrow Successful')
                location.reload()
            }
        })

    } else {
        alert('Please select borrower and books to borrow.')
    }
}

</script>
<template>
    <form @submit.prevent="registerApplicant()" class="w-100 gap-2 p-4 small-font">
        <div class="row gap-2">
            <div class="col p-4 card">
                <div class="mb-2 p-3 bg-secondary-subtle rounded-2 align-content-center">
                    <span class="fw-bold text-success">Step 1: </span>Search Borrower Information
                </div>
                <div class="d-flex flex-column gap-2 card p-3">
                    <div class="w-100 text-start form-group">
                        <label class="fw-bold">Borrower Name</label>
                        <input required v-model="searchStudent" onkeydown="return /[a-z, ]/i.test(event.key)"
                            type="text" class="form-control form-control-sm" />
                    </div>
                    <div class="w-100 text-start form-group">
                        <label class="fw-bold">Borrower Card</label>
                        <input required disabled v-model="cardIdNo" type="text"
                            class="form-control form-control-sm cursor-not-allowed bg-gray-200" />
                    </div>
                    <div class="w-100 text-start form-group">
                        <button :disabled="saving ? true : false" @click="reset()" class="btn btn-sm btn-dark w-100"
                            type="button">Search</button>
                    </div>
                </div>
                <div class="border d-flex flex-column gap-2 overflow-auto mt-3" style="height:250px;">
                    <table class="table table-hover table-fixed">
                        <thead>
                            <tr>
                                <th class="bg-secondary-subtle">Select Student</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="!loadingStudents && Object.keys(filteredStudent).length"
                                v-for="(fs, index) in filteredStudent" :class="savingLoads ? 'pe-none' : 'pe-auto'"
                                @click="setBorrower(fs.per_id, fs.enr_id, fs.per_firstname, fs.per_lastname)">

                                <td
                                    :class="fs.per_id == borrowerPerid ? 'align-middle text-start bg-secondary text-white' : 'align-middle text-start bg-white text-black'">
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
                                        <Loader />
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col p-4 card">
                <div class="mb-2 p-3 bg-secondary-subtle rounded-2 align-content-center">
                    <span class="fw-bold text-success">Step 2: </span>Search and add books to borrow
                </div>
                <div class="d-flex flex-column gap-2 card p-3">
                    <div class="w-100 text-start form-group">
                        <label class="fw-bold">Book Information</label>
                        <input required v-model="searchBook" onkeydown="return /[a-z, ]/i.test(event.key)"
                            type="text" class="form-control form-control-sm" />
                    </div>
                    <div class="w-100 text-start form-group">
                        <label class="fw-bold">Book Count</label>
                        <input  required disabled v-model="Object.keys(bookCart).length"
                        onkeydown="return /[a-z, ]/i.test(event.key)" type="text" 
                            class="form-control form-control-sm cursor-not-allowed bg-gray-200" />
                    </div>
                    <div class="w-100 text-start form-group">
                        <button :disabled="(saving ? true : false)" @click="filterBook()" class="btn btn-sm btn-dark w-100"
                            type="button">Search</button>
                    </div>
                </div>
                <div class="border d-flex flex-column gap-2 overflow-auto mt-3" style="height:250px;">
                    <table class="table table-hover table-fixed">
                        <thead>
                            <tr>
                                <th class="bg-secondary-subtle">Select Student</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="!loadingBooks && Object.keys(filteredBook).length"
                                v-for="(bk, index) in filteredBook" :class="savingLoads ? 'pe-none' : 'pe-auto'"
                                @click="addToCart('add', bk, index)">

                                <td
                                    :class="bookCartId.includes(bk.lbrb_id) ? 'align-middle text-start bg-secondary text-white' : 'align-middle text-start bg-white text-black'">
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
                                        <Loader />
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col p-4 card">
                <div class="mb-2 p-3 bg-secondary-subtle rounded-2 align-content-center">
                    <span class="fw-bold text-success">Step 3: </span>Confirm books inside the cart
                </div>
                <div class="d-flex flex-column gap-2 card p-3">
                    <div class="w-100 text-start form-group">
                        <label class="fw-bold">Borrower Name</label>
                        <input  disabled v-model="borrowerName" onkeydown="return /[a-z, ]/i.test(event.key)"
                            type="text" class="form-control form-control-sm" />
                    </div>
                    <div class="w-100 text-start form-group">
                        <label class="fw-bold">Borrower Card</label>
                        <input required disabled v-model="cardIdNo"
                        onkeydown="return /[a-z, ]/i.test(event.key)" type="text" 
                            class="form-control form-control-sm cursor-not-allowed bg-gray-200" />
                    </div>
                    <div class="w-100 text-start form-group">
                        <button :disabled="(saving ? true : false) ||
                                (!Object.keys(bookCart).length ? true : false) ||
                                (!cardId ? true : false)" @click="borrowBook()" class="btn btn-sm btn-dark w-100"
                            type="button">Borrow</button>
                    </div>
                </div>
                <div class="border d-flex flex-column gap-2 overflow-auto mt-3" style="height:250px;">
                    <table class="table table-hover table-fixed">
                        <thead>
                            <tr>
                                <th class="bg-secondary-subtle">Select Student</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="!loadingBooks && Object.keys(bookCart).length"
                                v-for="(bk, index) in bookCart" :class="savingLoads ? 'pe-none' : 'pe-auto'" >
                                <td
                                    class="align-middle text-start bg-white text-black d-flex flex-column gap-3">
                                    <p class="fw-bold fw-bold">Accession No: {{ bk.lbrb_accession_no }}</p>
                                    <p class="">{{ bk.lbrb_title }}</p>
                                    <button @click="addToCart('remove', bk, index)"
                                        class="btn btn-sm btn-danger"
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
                                        <Loader />
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