<script setup>
import { ref, onMounted, computed } from 'vue';
import NeuLoader2 from '../loaders/NeuLoader2.vue';
import {
    getLibraryCardIssue,
    getBorrowedBooksBy,
    deactivateLibraryCard,
    addLibraryCard,
    getProgramList, 
    getGradelvl, 
    getStudent,
    getLibraryCardMassPrint
} from "../../Fetchers.js";

import {
    qrImageGenerator,
    pdfGenerator,
    pdfAutoPrint,
    lvl2Encrypt,
    lvl2Decrypt,
    getDateToday
} from "../../Generators.js";


const props = defineProps({
    useriddata: {
    },
})



const userId = computed(() => {
    return props.useriddata
});


const preLoading = ref(false)
const libraryCards = ref([])
const cardNo = ref('QF-LIB-01-04-03-SEPT22')
const cardDate = ref('')
const hasActiveCard = ref(false)
const verifying = ref(false)
const qrimage = ref('')
const year = ref('')
const gradelvl = ref([])
const course = ref([])
const student = ref([])

const booter = async () => {
    await getProgramList().then(async(results) => {
        course.value = results
    })
    await getGradelvl().then(async(results) => {
        gradelvl.value = results
    })
    await getStudent(0, 0).then(async(results) => {
        student.value = results.data
    })
}

const cardCurrentColor = ref('')
const cardColor = ref([
    { id: 1, bgcolor: '#f01f1f', desc: 'BSCRIM', deg: 1 },
    { id: 2, bgcolor: '#1f34f0', desc: 'BSIT', deg: 1 },
    { id: 3, bgcolor: '#951ff0', desc: 'HUMSS', deg: 2 },
    { id: 4, bgcolor: '#951ff0', desc: 'STEM', deg: 2 },
    { id: 5, bgcolor: '#f0aa1f', desc: 'TVL', deg: 1 },
    { id: 6, bgcolor: '#951ff0', desc: 'PRE-BACC', deg: 2 },
    { id: 7, bgcolor: '#f01fd4', desc: 'BSHM', deg: 1 },
    { id: 8, bgcolor: '#f0f01f', desc: 'BSMT', deg: 1 },
    { id: 10, bgcolor: '#951ff0', desc: 'ABM', deg: 2 },
]);
const cards = ref([])
onMounted(async() => {
     try {
        cardCurrentColor.value = cardColor.value.find(c => c.id === student.value.enr_course)?.bgcolor || '#ffffff';
        var date = new Date();
        // var day = date.getDate();
        // var month = date.getMonth() + 1;
        // var year = date.getFullYear();
        cardDate.value = date.toISOString().split('T')[0]
        // console.log(cardDate.value)

        preLoading.value = true
        await booter().then(async() => {

           student.value.forEach(s => {
                let x = {
                    per_id: s.per_id,
                    enr_id: s.enr_id,
                }

                cards.value.push(x)

            })

            getLibraryCardMassPrint(cards.value).then((results) => {
                libraryCards.value = results.data.map((e) => {
                    let z = ''
                    if (e.per_profile) {
                        // z = 'http://sims.clcst.edu.local:8000/storage/profiles/' + e.per_profile
                        z = 'http://sims.clcst.edu.local:8000/api/get-person-image/' + e.per_profile +'/1'
                    } else {
                        if (e.per_gender == 2) {
                            z = '/img/woman.png'
                        } else {
                            z = '/img/man.png'
                        }
                    }

                    return {
                        ...e,
                        profile_picture: z,
                    }
                })

                groupedLibraryCards.value = groupCard(libraryCards.value)
                preLoading.value = false
                console.log(groupedLibraryCards.value)
            })
            // getLibraryCardIssue(student.value.per_id, student.value.enr_id, 0).then((results) => {
            //     // libraryCards.value = results
            //     let x = results.cards.findIndex((e) => {
            //         return e.lbrd_status === 1
            //     })
            //     year.value = results.year[1].sett_yearfrom + ' - ' + results.year[1].sett_yearto
            //     //map profile picture
            //     libraryCards.value = results.cards.map((e) => {
            //         let z = ''
            //         if (e.per_profile) {
            //             // z = 'http://sims.clcst.edu.local:8000/storage/profiles/' + e.per_profile
            //             z = 'http://sims.clcst.edu.local:8000/api/get-person-image/' + e.per_profile +'/1'
            //         } else {
            //             if (e.per_gender == 2) {
            //                 z = '/img/woman.png'
            //             } else {
            //                 z = '/img/man.png'
            //             }
            //         }

            //         return {
            //             ...e,
            //             profile_picture: z,
            //         }
            //     })

            //     if (x !== -1) {
            //         hasActiveCard.value = true
            //     }
            //     preLoading.value = false
            // })
        })
    } catch (err) {
        // preLoading.value = false
        // alert('error loading the list default components')
        Swal.fire({
            icon: "error",
            title: "Oops...",
            text: "Something went wrong!",
            footer: '<a href="#" disabled>Have you checked your internet connection?</a>'
        }).then(()=>{
            preLoading.value = false
            emit('doneLoading', false)
        });
    }
    
})


const groupedLibraryCards = ref([])
const groupCard = (data) => {
    let chunkSize = 4
    let groups = []

    for (let i = 0; i < data.length; i += chunkSize) {
        groups.push(data.slice(i, i + chunkSize))
    }

    return groups
}

const printCard = async (enrid, data) =>{
    let name = `LC-${enrid}-${data}`;
    let receiptWidth = 8.27; // in inches, your receipt width

    try {
        const pdfBlob = await pdfAutoPrint(name, receiptWidth, "portrait", 0.5);

        const pdfUrl = URL.createObjectURL(pdfBlob);
        const printWindow = window.open(pdfUrl, 'PrintWindow', 'width=900,height=700');

        printWindow.onload = () => {
            printWindow.focus();
            printWindow.print();
        };

    } catch (err) {
        console.error(err);
        Swal.fire("Error", "Failed to generate receipt.", "error");
        location.reload();
        return;
    }

    setTimeout(() => {
        Swal.close();
        // location.reload();
    }, 1000);
}
 
const printForm = async (enrid, data) => {
    const name = `LC-${enrid}-${data}`;

    Swal.fire({
        icon: "success",
        title: "Card is Ready",
        text: "Click OK to generate and download the PDF.",
        confirmButtonText: "Click to Download"
    }).then(async (result) => {
        if (!result.isConfirmed) return;

        Swal.fire({
            title: "Generating PDF...",
            text: "Please wait while we prepare your receipt.",
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // try {

        //     let key = "SIMS_CLCST_@2026!--*";
        //     let original = JSON.stringify({
        //         sid: data
        //     });

        //     const encrypted = lvl2Encrypt(original, key);
        //     // console.log("QR DATA:", encrypted);

        //     const decrypted = lvl2Decrypt(encrypted, key);
        //     // console.log("BACK:", JSON.parse(decrypted));

        //     // Generate QR first
        //     qrimage.value = await qrImageGenerator(encrypted);

        //     // Generate PDF
            let size = [8.27, 11.69]
            await pdfGenerator(name, size, 'portrait', 0.03)
            // await pdfGenerator(name, 'a6', 'landscape', 0);

            setTimeout(() => {
                Swal.close();
                location.reload();
            }, 1000);

        // } catch (error) {
        //     Swal.fire({
        //         icon: "error",
        //         title: "Generation Failed",
        //         text: "Something went wrong while creating the PDF."
        //     });
        //     console.error(error);
        // }
    });
};

</script>
<template>
    <div v-if="preLoading" >
        <NeuLoader2 />
    </div>
    <div v-else>
        <div id="printform">
            <div class="row page-break" style="height: 1123px; width: 794px;" 
                v-for="(page, pageIndex) in groupedLibraryCards"
                :key="pageIndex">
                <div class="col-6 p-1 border text-start" v-for="(lc, index) in page"
                    :key="index">
                    <div>
                        <div class="d-flex justify-content-center small-font">
                            <div class="bg-opaque position-relative"
                                style="height:197px; width:314px; font-size:8px;">

                                <!-- HEADER -->
                                <div class="p-1" style="font-size:6px;">
                                    <div class="row g-0">
                                        <div class="col-9 text-start">
                                            <p class="m-0 fw-bold">
                                                CENTRAL LUZON COLLEGE OF SCIENCE AND TECHNOLOGY, INC.
                                                CELTECH COLLEGE
                                            </p>

                                            <p class="m-0 fw-normal small-font">
                                                B. Mendoza St., Brgy. Sto. Rosario,
                                                City of San Fernando, Pampanga,
                                                Philippines, 2000
                                            </p>
                                        </div>

                                        <div class="col-3 d-flex justify-content-around align-items-center">
                                            <img src="/img/clcst_logo.png"
                                                alt=""
                                                height="30px"
                                                width="30px">
                                            <img src="/img/sims_logo.png"
                                                alt=""
                                                height="30px"
                                                width="30px">
                                        </div>
                                    </div>
                                </div>

                                <!-- TITLE -->
                                <div class="row g-0 bg-white">
                                    <div class="col-12 d-flex justify-content-center align-items-center"
                                        style="height:20px; background-color:#113d11;">

                                        <p class="text-white fw-bold m-0">
                                            Library Card (S.Y {{ year }})
                                        </p>
                                    </div>

                                    <!-- DETAILS -->
                                    <div class="col-9 border p-1">
                                        <ul class="list-group list-group-flush w-100">

                                            <li class="list-group-item p-1">
                                                Student ID:
                                                <span class="fw-bold">
                                                    {{ lc.studentid }}
                                                </span>
                                            </li>

                                            <li class="list-group-item p-1">
                                                Name:
                                                <span class="fw-bold text-uppercase">
                                                    {{ lc.per_firstname }}
                                                    {{ lc.per_middlename ? lc.per_middlename : '' }}
                                                    {{ lc.per_lastname }}
                                                    {{ lc.per_suffixname ? lc.per_suffixname : '' }}
                                                </span>
                                            </li>

                                            <li class="list-group-item p-1">
                                                Course:
                                                <span class="fw-bold">
                                                    {{ course.find(c => c.prog_id === lc.enr_course)?.prog_name || '—' }}
                                                </span>
                                            </li>

                                            <li class="list-group-item p-1">
                                                Grade Level:
                                                <span class="fw-bold">
                                                    {{ gradelvl.find(g => g.grad_id === lc.enr_gradelvl)?.grad_name || '—' }}
                                                </span>
                                            </li>

                                            <li class="list-group-item p-1">
                                                Date Issued:
                                                <span class="fw-bold">
                                                    {{ lc.lbrd_dateissued }}
                                                </span>
                                            </li>

                                        </ul>
                                    </div>

                                    <!-- PHOTO -->
                                    <div class="col-3 border">
                                        <div class="d-flex flex-column justify-content-center align-items-center h-100 p-1">
                                            <img class="neu-card p-1"
                                                height="60px"
                                                width="60px"
                                                :src="lc.profile_picture"
                                                alt="">

                                        </div>
                                    </div>
                                </div>

                                <!-- FOOTER -->
                                <div class="p-1 d-flex justify-content-between">
                                    <div>
                                        <p class="m-0 fw-normal small-font">
                                            Tel. Nos: (045) 435-1495
                                        </p>

                                        <p class="m-0 fw-normal small-font">
                                            Founded 1959
                                        </p>
                                    </div>

                                    <div>
                                        <p class="m-0 fw-bold small-font">
                                            QF-LIB-01-04-03-SEPT22
                                        </p>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="d-flex justify-content-center small-font">
                            <div class="bg-opaque position-relative"
                                style="height:197px; width:314px; font-size:8px;">

                                <!-- HEADER -->
                                <div class="p-1" style="font-size:6px; height: 18px;">
                                    
                                </div>
                                <!-- TITLE -->
                                <div class="row g-0 bg-white">
                                    <div class="col-12 d-flex justify-content-center align-items-center"
                                        :style="'height:20px; background-color:' + cardCurrentColor">
                                    </div>

                                    <!-- DETAILS -->
                                    <div class="col-9 border p-1">
                                        <ul class="list-group list-group-flush w-100">

                                            <li class="list-group-item p-1">
                                                In Case this card is found, please contact:<br/>
                                                <span class="fw-bold">
                                                    0{{ lc.per_contact }} / {{ lc.per_email }}
                                                </span>
                                            </li>
                                            <li class="list-group-item p-1">
                                                LIBRARY CARD GUIDELINES:<br/>
                                                <ul class="mb-0 ps-3" style="font-size:7px; line-height:1.3;">
                                                    <li>This card is non-transferable.</li>
                                                    <li>Present this card when borrowing books.</li>
                                                    <li>Report lost cards immediately to the library.</li>
                                                    <li>Damaged or altered cards are invalid.</li>
                                                    <li>Replacement of lost cards may require a fee.</li>
                                                    <li>Follow all library rules and regulations.</li>
                                                </ul>
                                            </li>
                                        </ul>
                                    </div>
                                    <!-- PHOTO -->
                                    <div class="col-3 border">
                                        <div class="d-flex flex-column justify-content-center align-items-center h-100 p-1">
                                            <div class="" id="qrcode" v-html="qrimage" ></div>
                                        </div>
                                    </div>
                                    <div class="col-12 d-flex justify-content-center align-items-center"
                                        :style="'height:20px; background-color:' + cardCurrentColor">
                                    </div>
                                </div>

                                <!-- FOOTER -->
                                <div class="p-1 d-flex justify-content-between">
                                    <div>
                                        <p class="m-0 fw-normal small-font">
                                        <span class="fw-bold">Ms.Krisha Angelique Rafanan</span><br/>
                                        <span class="fw-italic">Library In-charge</span>
                                        </p>
                                    </div>

                                    <div>
                                        <p class="m-0 small-font">
                                            Please return if found<br/>
                                            Property of CLCST Library 
                                        </p>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body d-flex gap-1 mt-3">
            <button @click="printCard('test','test')" type="button"
                class="neu-btn neu-blue p-2">
                <font-awesome-icon icon="fa-solid fa-print"  /> Print Card
            </button>
            <button @click="printForm('test','test')" type="button"
                class="neu-btn neu-green p-2">
                <font-awesome-icon icon="fa-solid fa-download"  /> Download Card
            </button>
        </div>
    </div>
    
    
</template>

