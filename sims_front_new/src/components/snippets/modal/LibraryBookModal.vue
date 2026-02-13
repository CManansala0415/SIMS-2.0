<script setup>
import { ref, onMounted, computed } from 'vue';
import {  
            addBookInformation,
       } from "../../Fetchers.js";

const props = defineProps({
    bookiddata: {
    },
    bookdata: {
    },
    modedata:{
    },
    useriddata:{
    },
    borrowdata:{
    }
})

const bookId = computed(() => {
  return props.bookiddata
});
const bookData = computed(() => {
  return props.bookdata
});
const mode = computed(() => {
  return props.modedata
});
const userId = computed(() => {
  return props.useriddata
});
const borrowId = computed(() => {
  return props.borrowdata
});

const saving = ref(false)
const book = ref({
    lbrb_id:'',
    lbrb_accession_no:'',
    lbrb_call_no:'',
    lbrb_author:'',
    lbrb_title:'',
    lbrb_edition:'',
    lbrb_volume:'',
    lbrb_pages:'',
    lbrb_publisher:'',
    lbrb_year:'',
    lbrb_source:1,
    lbrb_cost:'',
    lbrb_datereceived:'',
})

onMounted(()=>{
    if(mode.value == 1){
        book.value.lbrb_id = bookData.value.lbrb_id
        book.value.lbrb_accession_no = bookData.value.lbrb_accession_no
        book.value.lbrb_call_no = bookData.value.lbrb_call_no
        book.value.lbrb_author = bookData.value.lbrb_author
        book.value.lbrb_title = bookData.value.lbrb_title
        book.value.lbrb_edition = bookData.value.lbrb_edition
        book.value.lbrb_volume = bookData.value.lbrb_volume
        book.value.lbrb_pages = bookData.value.lbrb_pages
        book.value.lbrb_publisher = bookData.value.lbrb_publisher
        book.value.lbrb_year = bookData.value.lbrb_year
        book.value.lbrb_source = bookData.value.lbrb_source
        book.value.lbrb_cost = bookData.value.lbrb_cost
        book.value.lbrb_datereceived = bookData.value.lbrb_datereceived
    }
})

const registerBook = () =>{
    //1 means update 2 means add 3 means delete
    saving.value = true
    Swal.fire({
        title: "Saving Updates",
        text: "Please wait while we check all necessary details.",
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    let x = {}
    if(mode.value == 1){
        x = {
            ...book.value,
            lbrb_updatedby: userId.value,
            lbrb_mode: mode.value
        }
    }else{
        x = {
            ...book.value,
            lbrb_addedby: userId.value,
            lbrb_mode: mode.value
        }
    }

    addBookInformation(x).then((results)=>{
        if(results.status != 200){
            // alert('Update Failed')
            // location.reload()
            Swal.fire({
                title: "Update Failed",
                text: "Unknown error occured, try again later",
                icon: "error"
            }).then(()=>{
                Swal.close()
                location.reload()
            });
        }else{
            // alert('Update Successful')
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

const deleteBook = () =>{
//   if (confirm("Are you sure you want to delete this registry? this action cannot be reverted.") == true) {
//     let x = {
//           lbrb_id:bookId.value,
//           lbrb_updatedby: userId.value,
//           lbrb_mode: 3 // means delete
//     }
//     addBookInformation(x).then((results)=>{
//         // alert('Delete Successful')
//         // location.reload()
//         Swal.fire({
//             title: "Update Successful",
//             text: "Changes applied, refreshing the page",
//             icon: "success"
//         }).then(()=>{
//             location.reload()
//         });
//     })
//   } else {
//       return false;
//   }
  Swal.fire({
        title: "Delete Record",
        text: "Are you sure you want to delete this registry? this action cannot be reverted?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, Im Delete it!"
    }).then(async (result) => {
        if (result.isConfirmed) {
            saving.value = true
             Swal.fire({
                title: "Saving Updates",
                text: "Please wait while we check all necessary details.",
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            let x = {
                lbrb_id:bookId.value,
                lbrb_updatedby: userId.value,
                lbrb_mode: 3 // means delete
            }
            addBookInformation(x).then((results)=>{
                // alert('Delete Successful')
                // location.reload()
                Swal.fire({
                    title: "Update Successful",
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

const formType = ref(0)
</script>

<template>
    <div class="d-flex flex-column p-3 gap-2 neu-card-inner">
            <!-- <div class="border-0 border-b-2 border-gray-300 p-1 mb-4 flex justify-between items-center">
                    <p class="mb-2 text-md font-semibold">Book Information</p>   
                    <button :disabled="saving?true:false" type="button" @click="$emit('close-modal')" class="mb-2 bg-red-500 hover:bg-red-400 px-3 rounded-md font-semibold text-white disabled:opacity-50 disabled:cursor-not-allowed">&times;</button>  
            </div> -->
            <div class="d-flex flex-wrap flex-column p-3">
                <p class="text-success fw-bold">Book Details</p>
                <div v-if="book.lbrb_title" class="neu-card p-2 d-flex justify-content-center align-items-center mb-3">
                    <p class="fw-bold m-0">{{ book.lbrb_title }}</p>
                </div>
                <p class=" fst-italic small-font"><span class="fw-bold">Note:
                    </span><span class="italic">Ensure that the details of the following applicant are correct.
                        To enroll this applicant, select the appropiate academic status and refresh the page.
                    </span></p>
            </div>
            <form @submit.prevent="registerBook()" class="row gy-2 gx-2 p-3 border neu-card">
                <div class="col-6 d-flex flex-wrap form-group">
                    <label class="">Call No.</label>
                    <input v-model="book.lbrb_call_no"
                        type="number" class="neu-input"
                        :disabled="borrowId.includes(book.lbrb_id)? true:false"/>
                </div>
                <div class="col-6 d-flex flex-wrap form-group">
                    <label class="">Accession No.</label>
                    <input v-model="book.lbrb_accession_no"
                        required
                        type="number" class="neu-input"
                        :disabled="borrowId.includes(book.lbrb_id)? true:false"/>
                </div>
                <div class="col-12 d-flex flex-wrap form-group">
                    <label class="">Title</label>
                    <textarea v-model="book.lbrb_title"
                        required
                        type="text" class="neu-input"
                        :disabled="borrowId.includes(book.lbrb_id)? true:false"></textarea>
                </div>
                <div class="col-12 d-flex flex-wrap form-group">
                    <label class="">Author</label>
                    <input v-model="book.lbrb_author"
                        required
                        type="text" class="neu-input"
                        :disabled="borrowId.includes(book.lbrb_id)? true:false"/>
                </div>
                <div class="col-6 d-flex flex-wrap form-group">
                    <label class="">Edition</label>
                    <input v-model="book.lbrb_edition"
                        type="text" class="neu-input"
                        :disabled="borrowId.includes(book.lbrb_id)? true:false"/>
                </div>
                <div class="col-6 d-flex flex-wrap form-group">
                    <label class="">Volume</label>
                    <input v-model="book.lbrb_volume"
                        type="number" class="neu-input"
                        :disabled="borrowId.includes(book.lbrb_id)? true:false"/>
                </div>
                <div class="col-6 d-flex flex-wrap form-group">
                    <label class="">Pages</label>
                    <input v-model="book.lbrb_pages"
                        required
                        type="number" class="neu-input"
                        :disabled="borrowId.includes(book.lbrb_id)? true:false"/>
                </div>
                <div class="col-6 d-flex flex-wrap form-group">
                    <label class="">Source</label>
                    <select class="neu-input neu-select"  v-model="book.lbrb_source"
                    :disabled="borrowId.includes(book.lbrb_id)? true:false">
                        <option value="1">Donated</option>
                        <option value="2">School Funds</option>
                    </select>
                </div>
                <div class="col-6 d-flex flex-wrap form-group">
                    <label class="">Cost (Peso)</label>
                    <input v-model="book.lbrb_cost"
                        min="0"
                        max="999999"
                        oninput="this.value = Math.abs(this.value)"
                        type="number" class="neu-input"
                        :disabled="borrowId.includes(book.lbrb_id)? true:false"/>
                </div>
                <div class="col-6 d-flex flex-wrap form-group">
                    <label class="">Publisher</label>
                    <input v-model="book.lbrb_publisher"
                        type="text" class="neu-input"
                        :disabled="borrowId.includes(book.lbrb_id)? true:false"/>
                </div>
                <div class="col-6 d-flex flex-wrap form-group">
                    <label class="">Year</label>
                    <input v-model="book.lbrb_year"
                        required
                        min="1980"
                        max="3000"
                        type="number" class="neu-input"
                        :disabled="borrowId.includes(book.lbrb_id)? true:false"/>
                </div>
                <div class="col-6 d-flex flex-wrap form-group">
                    <label class="">Date Received</label>
                    <input v-model="book.lbrb_datereceived"
                        type="date" class="neu-input"
                        :disabled="borrowId.includes(book.lbrb_id)? true:false"/>
                </div>
                <div v-if="!borrowId.includes(book.lbrb_id)" class="mt-3 d-flex gap-2 justify-content-end">
                    <button :disabled="saving?true:false" type="submit" class="neu-btn neu-green p-2">
                        <font-awesome-icon icon="fa-solid fa-floppy-disk"  /> Save Data
                    </button>
                    <button v-if="mode!=2" @click="deleteBook()" :disabled="saving?true:false" type="button" class="neu-btn neu-red p-2">
                        <font-awesome-icon icon="fa-solid fa-trash"  /> Delete Book
                    </button>
                </div>
                <div v-else class="mt-2 d-flex gap-2 justify-content-center p-2">
                    <label class="text-danger fw-bold">Note: This book is currently borrowed, you are not allowed to edit details.</label>
                </div>
            </form>
        </div>
</template>