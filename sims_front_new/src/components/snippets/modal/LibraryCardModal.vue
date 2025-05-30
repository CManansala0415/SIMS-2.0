<script setup>
import { ref, onMounted, computed } from 'vue';
import Loader from '../loaders/Loading1.vue';
import {
    getLibraryCardIssue,
    getBorrowedBooksBy,
    deactivateLibraryCard,
    addLibraryCard
} from "../../Fetchers.js";

import {
    qrImageGenerator,
    pdfGenerator
} from "../../Generators.js";


const props = defineProps({
    useriddata: {
    },
    studentdata: {
    }
})


const student = computed(() => {
    return props.studentdata
});
const userId = computed(() => {
    return props.useriddata
});


const preLoading = ref(false)
const libraryCards = ref([])
const cardNo = ref('')
const cardDate = ref('')
const hasActiveCard = ref(false)
const verifying = ref(false)
const qrimage = ref('')

onMounted(() => {
    preLoading.value = true
    getLibraryCardIssue(student.value.per_id, student.value.enr_id, 0).then((results) => {
        libraryCards.value = results
        let x = libraryCards.value.findIndex((e) => {
            return e.lbrd_status === 1
        })

        if (x !== -1) {
            hasActiveCard.value = true
        }
        preLoading.value = false
    })
})

const deactivateCard = (card) => {
    // if (confirm("Are you sure you want to deactivate this card") == true) {
    //     verifying.value = true
    //     getBorrowedBooksBy(card, student.value.per_id, student.value.enr_id).then((results) => {
    //         let x = {
    //             lbrd_id: card,
    //             lbrd_enrid: student.value.per_id,
    //             lbrd_personid: student.value.enr_id,
    //             lbrd_user: userId.value,
    //         }
    //         console.log(results)
    //         if (Object.keys(results).length) {
    //             // alert('This card has an active borrowed books, to deactivate this card return all the books currently borrowed')
    //             // verifying.value = false
    //             Swal.fire({
    //                 title: "Notice",
    //                 text: "This card has an active borrowed books, to deactivate this card return all the books currently borrowed",
    //                 icon: "warning"
    //             }).then(()=>{
    //                 verifying.value = false
    //             });
    //         } else {
    //             deactivateLibraryCard(x).then((results) => {
    //                 if (results.status == 200) {
    //                     // alert('Update Successful')
    //                     // location.reload()
    //                     Swal.fire({
    //                         title: "Update Successful",
    //                         text: "Changes applied, refreshing the page",
    //                         icon: "success"
    //                     }).then(()=>{
    //                         location.reload()
    //                     });
    //                 } else {
    //                     // alert('Update Failed')
    //                     // location.reload()
    //                     Swal.fire({
    //                         title: "Update Failed",
    //                         text: "Unknown error occured, try again later",
    //                         icon: "error"
    //                     }).then(()=>{
    //                         location.reload()
    //                     });
    //                 }
    //             })
    //         }
    //     })
    // } else {
    //     return false;
    // }

    Swal.fire({
        title: "Delete Record",
        text: "Are you sure you want to deactivate this card?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, Im deactivate it!"
    }).then(async (result) => {
        if (result.isConfirmed) {
            verifying.value = true
            getBorrowedBooksBy(card, student.value.per_id, student.value.enr_id).then((results) => {
                let x = {
                    lbrd_id: card,
                    lbrd_enrid: student.value.per_id,
                    lbrd_personid: student.value.enr_id,
                    lbrd_user: userId.value,
                }
                console.log(results)
                if (Object.keys(results).length) {
                    // alert('This card has an active borrowed books, to deactivate this card return all the books currently borrowed')
                    // verifying.value = false
                    Swal.fire({
                        title: "Notice",
                        text: "This card has an active borrowed books, to deactivate this card return all the books currently borrowed",
                        icon: "warning"
                    }).then(() => {
                        verifying.value = false
                    });
                } else {
                    deactivateLibraryCard(x).then((results) => {
                        if (results.status == 200) {
                            // alert('Update Successful')
                            // location.reload()
                            Swal.fire({
                                title: "Update Successful",
                                text: "Changes applied, refreshing the page",
                                icon: "success"
                            }).then(() => {
                                location.reload()
                            });
                        } else {
                            // alert('Update Failed')
                            // location.reload()
                            Swal.fire({
                                title: "Update Failed",
                                text: "Unknown error occured, try again later",
                                icon: "error"
                            }).then(() => {
                                location.reload()
                            });
                        }
                    })
                }
            })
        }
    });
}

const registerNewCard = () => {
    let x = {
        lbrd_enrid: student.value.enr_id,
        lbrd_personid: student.value.per_id,
        lbrd_cardno: cardNo.value,
        lbrd_dateissued: cardDate.value,
        lbrd_issuedby: userId.value,
        lbrd_user: userId.value,
    }

    addLibraryCard(x).then((results) => {
        if (results.status == 200) {
            // alert('Update Successful')
            // location.reload()
            Swal.fire({
                title: "Update Successful",
                text: "Changes applied, refreshing the page",
                icon: "success"
            }).then(() => {
                location.reload()
            });
        } else {
            // alert('Update Failed')
            // location.reload()
            Swal.fire({
                title: "Update Failed",
                text: "Unknown error occured, try again later",
                icon: "error"
            }).then(() => {
                location.reload()
            });
        }
    })

}

const printForm = (enrid,data) => {
    let name = 'LC-'+enrid+data
    qrImageGenerator(data).then((result) => {
        qrimage.value = result
        pdfGenerator(name, 'a6', 'landscape', 0)
        Swal.fire({
            icon: "success",
            title: "Download Complete",
            text: "Check your file manager, refreshing the page",
        }).then(()=>{
            location.reload()
        });
    })
}
</script>
<template>
    <div class="small-font">

        <div v-if="preLoading">
            <Loader />
        </div>
        <div v-else>
            <div class="d-flex gap-2">
                <div class="w-50">
                    <p class="fw-bold text-white bg-secondary rounded-2 p-1">Issue New Card</p>
                    <div class="border card">
                        <form @submit.prevent="registerNewCard" class="p-2 d-flex flex-column gap-2">
                            <div class="form-group p-2 text-start">
                                <label class="">Card No.</label>
                                <input v-model="cardNo" required :disabled="hasActiveCard ? true : false"
                                    placeholder="Ex. QF-LIB-01-04-03-SEPT22" type="text"
                                    class="form-control form-control-sm" />
                            </div>
                            <div class="form-group p-2 text-start">
                                <label class="">Issue Date: </label>
                                <input v-model="cardDate" required :disabled="hasActiveCard ? true : false" type="date"
                                    class="form-control form-control-sm" />
                            </div>
                            <div class="form-group p-2 text-center">
                                <button v-if="!hasActiveCard" type="submit" class="btn btn-sm btn-dark w-100">Issue New
                                    Card</button>
                                <p v-else class="">
                                    <span class="fw-bold text-danger">Notice: </span> To issue a new card, the person
                                    must not
                                    have
                                    any active card.
                                </p>
                            </div>
                            <div class="form-group p-3 text-start bg-secondary-subtle">
                                <p class="">
                                    <span class="fw-bold text-primary">Note: </span> You can release a new library card
                                    one
                                    at a
                                    time,
                                    to release a new card you must disable current active card and ensure that the books
                                    is
                                    borrowed are returned, otherwise the system will not allow the release.
                                </p>
                            </div>
                        </form>
                    </div>

                </div>
                <div class="w-50">
                    <p class="fw-bold text-white bg-secondary rounded-2 p-1">Issued Cards</p>

                    <div v-if="!Object.keys(libraryCards).length && !verifying" class="p-2 border card">
                        <div class="p-4 bg-white align-content-center">
                            No Library Card Issued
                        </div>
                    </div>
                    <div v-if="verifying" class="p-2 border card">
                        <div class="p-4 bg-white d-flex flex-column gap-2">
                            <Loader />
                        </div>
                    </div>
                    <div v-if="Object.keys(libraryCards).length && !verifying" class="p-2 border card">
                        <div class="card text-start" v-for="(lc, index) in libraryCards">
                            <div class="card-body">
                                <h5 class="card-title">{{ lc.lbrd_cardno }}</h5>
                                <p class="card-text">Issued Library Card to this student with corresponding details
                                    below:</p>
                            </div>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item"><span class="fw-bold">Date Issued:</span> {{
                                    lc.lbrd_dateissued }}</li>
                                <li class="list-group-item"><span class="fw-bold">Issued By:</span> {{ lc.emp_firstname
                                }} {{
                                        lc.emp_middlename }} {{ lc.emp_lastname }}</li>
                                <li class="list-group-item"><span class="fw-bold">Card Status:</span> {{ lc.lbrd_status
                                    == 1 ?
                                    'Active' : 'Inactive' }}</li>
                                <li class="list-group-item" v-show="false">
                                    <div class="d-flex justify-content-center small-font">
                                        <div class="w-100 row bg-opaque" style="height:375px; font-size:12px" id="printform">
                                            <!-- <div class="col-3 bg-opaque d-flex flex-column justify-content-center align-content-center p-0">
                                                <div class="bg-danger">
                                                    <img class="card-img-top" src="/img/clcst_logo.png" height="50px" width="50px" alt="...">
                                                </div>
                                                <div class="bg-success d-flex align-items-center justify-content-center  h-100">
                                                    <p style="writing-mode: vertical-rl; text-orientation: upright; font-size:6px; font-weight:bold;">
                                                        {{ lc.lbrd_cardcode }}
                                                    </p>
                                                </div>
                                            </div> -->
                                            <div class="">
                                                <div class="card-body p-3">
                                                    <div class="row">
                                                        <div
                                                            class="col-3 d-flex justify-content-center align-items-center">
                                                            <img class="card-img-top" src="/img/clcst_logo.png"
                                                                alt="...">
                                                        </div>
                                                        <div
                                                            class="col-9 justify-content-center align-content-center">
                                                            <p class="m-0 fw-bold">CENTRAL LUZON COLLEGE OF SCIENCE AND
                                                                TECHNOLOGY, INC.
                                                                CELTECH COLLEGE</p>
                                                            <p class="m-0 fw-normal small-font">B. Mendoza St., Brgy.
                                                                Sto. Rosario, City of San Fernando,
                                                                Pampanga, Philippines, 2000</p>
                                                            <!-- <p class="m-0 fw-normal small-font">Tel. Nos: (045) 435-1495</p>
                                                                <p class="m-0 fw-normal small-font">Founded 1959</p> -->
                                                        </div>
                                                        <!-- <div class="col-3 border d-flex justify-content-center align-items-center">
                                                            <div class="" id="qrcode" v-html="qrimage"></div>
                                                        </div> -->
                                                    </div>

                                                </div>
                                                <div class="row bg-white">
                                                    <div class="col-9">
                                                        <ul class="list-group list-group-flush p-3">
                                                            <li class="list-group-item">
                                                                <span>
                                                                    Name:
                                                                    <span class="fw-bold">
                                                                        {{ lc.per_firstname }}
                                                                        {{ lc.per_middlename ? lc.per_middlename : ' ' }}
                                                                        {{ lc.per_lastname }}
                                                                        {{ lc.per_suffixname ? lc.per_suffixname : ' ' }}
                                                                    </span>
                                                                </span>
                                                            </li>
                                                            <li class="list-group-item">
                                                                <span>
                                                                    Date Issued:
                                                                    <span class="fw-bold">
                                                                        {{ lc.lbrd_dateissued }}
                                                                    </span>
                                                                </span>
                                                            </li>
                                                            <li class="list-group-item">
                                                                <span>
                                                                    Library ID:
                                                                    <span class="fw-bold">
                                                                        {{ lc.lbrd_cardno }}
                                                                    </span>
                                                                </span>
                                                            </li>
                                                            <li class="list-group-item">
                                                                <span>
                                                                    Card Code:
                                                                    <span class="fw-bold">
                                                                        {{ lc.lbrd_cardcode }}
                                                                    </span>
                                                                </span>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                    <div class="col-3 border">
                                                        <div class="d-flex flex-column justify-content-center align-items-center h-100">
                                                            <div class="" id="qrcode" v-html="qrimage"></div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="card-body p-3">
                                                    <p class="m-0 fw-normal small-font">Tel. Nos: (045) 435-1495</p>
                                                    <p class="m-0 fw-normal small-font">Founded 1959</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                            <div class="card-body d-flex gap-1">
                                <button @click="printForm(lc.lbrd_enrid,lc.lbrd_cardcode)" v-if="lc.lbrd_status == 1" type="button"
                                    class="btn btn-sm btn-primary w-100">
                                    Download Card
                                </button>
                                <button @click="deactivateCard(lc.lbrd_id)" v-if="lc.lbrd_status == 1"
                                    class="btn btn-sm btn-danger w-100" type="button">
                                    Deactivate
                                </button>
                                <button v-else disabled class="btn btn-sm btn-danger pe-none w-100" type="button">
                                    Deactivated
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </div>
</template>