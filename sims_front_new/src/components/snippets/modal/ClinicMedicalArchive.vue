<script setup>
import { ref, onMounted, computed } from 'vue';
import NeuLoader2 from '../loaders/NeuLoader2.vue';
import NeuLoader4 from '../loaders/NeuLoader4.vue';
import { formatDateTime } from '../../Generators.js';
import {
    getMedicalFiles,
    getMedicalFileHeaders,
    addMedicalFileHeader,
    uploadMedicalFileImage,
    uploadMedicalFileLink,
} from "../../Fetchers.js";

const props = defineProps({
    userIdData: {
    },
    personIdData: {
    }
})

const userId = computed(() => {
    return props.userIdData
});
const personId = computed(() => {
    return props.personIdData
});

const saving = ref(false)
const preLoading = ref(false)
const showImage = ref(false)
const imageLoading = ref(false)
const medicalFiles = ref([])
const medicalFilesHeaders = ref([])
const filteredMedicalFilesHeaders = ref([])
const fileName = ref('')
const folderName = ref('cbc')
const folderId = ref('1')
const archiveDesc = ref('')
const activeHeader = ref('')
const uploading = ref(false)
onMounted(() => {
    preLoading.value = true
    getMedicalFileHeaders(personId.value).then((results) => {
        medicalFilesHeaders.value = results
        filteredMedicalFilesHeaders.value = results
        preLoading.value = false
    })

})

const viewImage = (id) => {
    activeHeader.value = id // set naten header id value para sa ibang purpose

    if (id != 0) {
        imageLoading.value = true
        showImage.value = !showImage.value
        getMedicalFiles(id, folderId.value).then((results) => {
            if (Object.keys(results).length) {
                medicalFiles.value = results
                fileName.value = results[0].clmf_filename
                imageLoading.value = false
            } else {
                medicalFiles.value = results
                fileName.value = ''
                imageLoading.value = false
            }
        })
    } else {
        folderId.value = '1'
        folderName.value = 'cbc'
        showImage.value = !showImage.value
    }
}

const folderSwitch = (index) => {
    let selectedFile = document.getElementById('files' + index).value
    let selectbox = document.getElementById('files' + index)
    selectbox.value = selectedFile


    switch (selectedFile) {
        case '1':
            folderName.value = 'cbc'
            folderId.value = selectedFile
            break;
        case '2':
            folderName.value = 'ecg'
            folderId.value = selectedFile
            break;
        case '3':
            folderName.value = 'xray'
            folderId.value = selectedFile
            break;
        case '4':
            folderName.value = 'urinalysis'
            folderId.value = selectedFile
            break;
        case '5':
            folderName.value = 'drugtest'
            folderId.value = selectedFile
            break;
        case '6':
            folderName.value = 'hepab'
            folderId.value = selectedFile
            break;
        case '7':
            folderName.value = 'pregnancy'
            folderId.value = selectedFile
            break;
        case '8':
            folderName.value = 'fecalysis'
            folderId.value = selectedFile
            break;
        case '9':
            folderName.value = 'physical'
            folderId.value = selectedFile
            break;
        default:
            folderName.value = ''
    }
}

const addNew = () => {
    // if (confirm("Are you sure you want to create this archive?") == true) {
    //     // await axios.post('/logout');
    //     // alert('Logged Out')
    //     // router.push("/");
    //     uploading.value=true
    //     let x = {
    //         clfh_personid: personId.value,
    //         clfh_desc: archiveDesc.value,
    //         clfh_user: userId.value
    //     }

    //     addMedicalFileHeader(x).then((results) => {
    //         if (results.status == 200) {
    //             alert('Update Successful')
    //             location.reload()
    //         } else {
    //             alert('Update Failed')
    //             // location.reload()
    //         }
    //     })
    // } else {
    //     return false;
    // }

    Swal.fire({
        title: "Generate Record",
        text: "Are you sure you want to create this archive?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, Im going to create it!"
    }).then(async (result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: "Generating Archive",
                text: "Please wait while we check all necessary details.",
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            let x = {
                clfh_personid: personId.value,
                clfh_desc: archiveDesc.value,
                clfh_user: userId.value
            }
            addMedicalFileHeader(x).then((results) => {
                if (results.status == 200) {
                    Swal.fire({
                        title: "Generate Successful",
                        text: "Changes applied, refreshing the page",
                        icon: "success"
                    }).then(()=>{
                        Swal.close()
                        location.reload()
                    });
                } else {
                    Swal.fire({
                        title: "Generate Failed",
                        text: "Error occured, contact your administrator",
                        icon: "error"
                    }).then(()=>{
                        Swal.close()
                        location.reload()
                    });
                }
                
            })
        }
    });
}


const searchArchive = () => {
    filteredMedicalFilesHeaders.value = medicalFilesHeaders.value.filter((e) => {
        if (e.clfh_desc.toUpperCase().includes(archiveDesc.value.toUpperCase())) {
            return {
                ...e
            }
        }
    })
}

// image uploading
const image = ref('')
const formData = new FormData
const upload = () => {
    Swal.fire({
        title: "Uploading File",
        text: "Please wait while we check all necessary details.",
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });


    uploading.value = true
    formData.set('image', image.value)
    uploadMedicalFileImage(formData, folderName.value).then((results) => {
        console.log(folderName.value)
        if (results.status == 200) {
            let x = {
                clmf_headerid: activeHeader.value,
                clmf_filename: image.value.name,
                clmf_resulttype: folderId.value,
                clmf_user: userId.value
            }
            uploadMedicalFileLink(x).then((results) => {
                // alert('Upload Successful')
                // location.reload()
                Swal.fire({
                    title: "Upload Successful",
                    text: "Changes applied, refreshing the page",
                    icon: "success"
                }).then(()=>{
                    Swal.close()
                    location.reload()
                });
            })
        } else {
            // alert('Upload Failed')
            Swal.fire({
                title: "Upload Failed",
                text: "Unknown error occured, try again later",
                icon: "error"
            }).then(()=>{
                Swal.close()
                location.reload()
            });
        }
    })

}

const handleImage = (e) => {
    image.value = e.target.files[0]
}
// image uploading

const downloadImage = (folder, file) => {
    let link = "http://localhost:8000/storage/clinic/" + folder + "/" + file
    window.open(link)
}

</script>

<template>
    <div class="d-flex flex-column p-3 gap-2 neu-card-inner">
        <div class="d-flex flex-wrap flex-column">
            <p class="text-success fw-bold mb-3">Medical Archive</p>
            <p class="m-0 fw-bold small-font fst-italic">Uploaded Results</p>
            <p class="m-0 fst-italic p-2 small-font"><span class="fw-bold">Note:
                </span><span class="italic">Type the name of archive to search and click search to proceed. If no
                    results found the system will suggest to add a new archive.
                </span></p>
        </div>

        <div v-if="!showImage" class="d-flex flex-column gap-2 small-font justify-content-center">
            <div class="neu-card w-100">
                <div v-if="!preLoading" class="border p-2 align-content-center p-3">
                    <div class="d-flex gap-2 justify-content-center align-content-center">
                        <input @keyup="searchArchive()" v-model="archiveDesc" type="text"
                            class="neu-input" placeholder="Archive Name" aria-label="Archive Name"
                            aria-describedby="button-addon2">
                        <button :disabled="!archiveDesc || uploading ? true : false" tabindex="-1" type="button" @click="addNew()"
                            class="neu-btn neu-green w-25" id="button-addon2"><font-awesome-icon icon="fa-solid fa-plus"/> Add New</button>
                    </div>
                </div>
                <div v-if="!preLoading && !Object.keys(filteredMedicalFilesHeaders).length"
                    class="border p-2 align-content-center p-3">
                    <NeuLoader4/>
                    <p class="fw-bold m-0">Nothing here yet!</p>
                    <p>The hamster took a break 💤 — try adding something new.</p>
                </div>
                <div v-if="preLoading && !Object.keys(filteredMedicalFilesHeaders).length"
                    class="border p-2 align-content-center">
                    <NeuLoader2 />
                </div>
                <div v-if="!preLoading && Object.keys(filteredMedicalFilesHeaders).length">
                    <div class="w-100 p-2">
                        <div>
                            <table class="neu-table-flat">
                                <thead>
                                    <tr>
                                        <th style="color:#555555" class="w-25">Date</th>
                                        <th style="color:#555555" class="w-25">Archive Description
                                        </th>
                                        <th style="color:#555555" class="w-25">Uploader</th>
                                        <th style="color:#555555" class="text-center w-50">Commands</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(mef, index) in filteredMedicalFilesHeaders">
                                        <td class="align-middle">
                                            {{ formatDateTime(mef.clfh_dateadded) }}
                                        </td>
                                        <td class="align-middle">
                                            {{ mef.clfh_desc }}
                                        </td>
                                        <td class="align-middle">
                                            {{ mef.emp_firstname }} {{ mef.emp_lastname }}
                                        </td>
                                        <td class="align-middle">
                                            <div class="d-flex flex-column gap-2 justify-content-center">
                                                <select class="neu-input neu-select"
                                                    @change="folderSwitch(index)" :id="'files' + index">
                                                    <option value="0" disabled>-- Select File --</option>
                                                    <option value="1">CBC</option>
                                                    <option value="2">ECG</option>
                                                    <option value="3">Chest X-ray</option>
                                                    <option value="4">Urinalysis</option>
                                                    <option value="5">Drug Test</option>
                                                    <option value="6">Hepa B-Screening</option>
                                                    <option value="7">Pregnancy Test</option>
                                                    <option value="8">Fecalysis</option>
                                                    <option value="9">Physical Exam</option>
                                                </select>
                                                <button :disabled="saving ? true : false" type="button"
                                                    @click="viewImage(mef.clfh_id)" class="neu-btn-sm neu-white">
                                                    <font-awesome-icon icon="fa-solid fa-eye"/> Results
                                                </button>
                                            </div>
                                        </td>

                                    </tr>
                                    <tr v-if="!preLoading && !Object.keys(filteredMedicalFilesHeaders).length">
                                        <td class="p-3 text-center" colspan="4">
                                            No Records Found
                                        </td>
                                    </tr>
                                    <tr v-if="preLoading && !Object.keys(filteredMedicalFilesHeaders).length">
                                        <td class="p-3 text-center" colspan="4">
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
            </div>
        </div>

        <div v-else class="d-flex flex-column gap-2 small-font justify-content-center">
            <div v-if="!imageLoading" class="w-100 d-flex justify-content-center gap-2 mb-2">
                <div class="d-flex gap-2 w-50">
                    <button :disabled="saving || uploading ? true : false" type="button" @click="viewImage(0)"
                        class="neu-btn neu-blue p-2">
                        <font-awesome-icon icon="fa-solid fa-rotate-left"/> Back to List
                    </button>
                    <button :disabled="saving || uploading ? true : false" type="button" @click="downloadImage(folderName, fileName)"
                        class="neu-btn neu-green p-2">
                        <font-awesome-icon icon="fa-solid fa-download"/> Download
                    </button>
                </div>
            </div>
            <div class="w-100">
                <div v-if="imageLoading"
                    class="border w-100 d-flex flex-column justify-content-center align-content-center p-3">
                    <NeuLoader2/>
                </div>
                <div v-else
                    class=" w-100 d-flex flex-column justify-content-center align-content-center p-3">
                    <div v-if="fileName" class="flex flex-col justify-center items-center overflow-auto">
                       <div class="neu-card-inner-dark neu-bg-dark p-3">
                         <img :src="fileName ? 'http://localhost:8000/storage/clinic/' + folderName + '/' + fileName : '/img/profile_default.png'"
                            class="h-100 object-contain border-2 border-gray-300" />
                       </div>

                    </div>
                    <div v-else class="flex flex-col gap-2 justify-center items-center">
                        <form @submit.prevent="upload()" method="post" enctype="multipart/form-data"
                            class="p-3 neu-card rounded-3 d-flex justify-content-center align-content-center">
                                <div class="w-50 d-flex gap-2">
                                     <input type="file" class="neu-input" @change="handleImage">
                                    <button :disabled="!image || uploading ? true : false" type="submit"
                                        class="neu-btn neu-purple p-2"><font-awesome-icon icon="fa-solid fa-upload"/> Upload Image File
                                    </button>
                                </div>
                        </form>
                        <div class="h-100 neu-card p-4 mt-3">
                            <NeuLoader4/>
                            <p class="fw-bold m-0">Nothing here yet!</p>
                            <p>The hamster took a break 💤 — try adding something new.</p>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</template>