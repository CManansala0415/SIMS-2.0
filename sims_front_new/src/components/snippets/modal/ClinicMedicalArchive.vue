<script setup>
import { ref, onMounted, computed } from 'vue';
import Loader from '../loaders/Loading1.vue';
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
    if (confirm("Are you sure you want to create this archive?") == true) {
        // await axios.post('/logout');
        // alert('Logged Out')
        // router.push("/");
        uploading.value=true
        let x = {
            clfh_personid: personId.value,
            clfh_desc: archiveDesc.value,
            clfh_user: userId.value
        }

        addMedicalFileHeader(x).then((results) => {
            if (results.status == 200) {
                alert('Update Successful')
                location.reload()
            } else {
                alert('Update Failed')
                // location.reload()
            }
        })



    } else {
        return false;
    }
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
                alert('Upload Successful')
                location.reload()
            })
        } else {
            alert('Upload Failed')
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
    <div class="d-flex flex-column p-2 gap-2">
        <div class="d-flex flex-wrap flex-column">
            <p class="text-success fw-bold">Medical Archive</p>
            <p class="fw-bold small-font fst-italic">Uploaded Results</p>
            <p class="fst-italic border p-2 rounded-3 bg-secondary-subtle small-font"><span class="fw-bold">Note:
                </span><span class="italic">Type the name of archive to search and click search to proceed. If no
                    results found the system will suggest to add a new archive.
                </span></p>
        </div>

        <div v-if="!showImage" class="d-flex flex-column gap-2 small-font justify-content-center">
            <div class="card shadow w-100">
                <div v-if="!preLoading" class="border p-2 align-content-center p-3">
                    <div class="input-group">
                        <input @keyup="searchArchive()" v-model="archiveDesc" type="text"
                            class="form-control form-control-sm" placeholder="Archive Name" aria-label="Archive Name"
                            aria-describedby="button-addon2">
                        <button :disabled="!archiveDesc || uploading ? true : false" tabindex="-1" type="button" @click="addNew()"
                            class="btn btn-sm btn-dark" id="button-addon2">Add New</button>
                    </div>
                </div>
                <div v-if="!preLoading && !Object.keys(filteredMedicalFilesHeaders).length"
                    class="border p-2 align-content-center p-3">
                    <span class="fw-bold text-danger">No Items Found</span>
                </div>
                <div v-if="preLoading && !Object.keys(filteredMedicalFilesHeaders).length"
                    class="border p-2 align-content-center">
                    <Loader />
                </div>
                <div v-if="!preLoading && Object.keys(filteredMedicalFilesHeaders).length">
                    <div class="w-100 p-2">
                        <div>
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th style="background-color: #237a5b;" class="text-white">Date</th>
                                        <th style="background-color: #237a5b;" class="text-white">Archive Description
                                        </th>
                                        <th style="background-color: #237a5b;" class="text-white">Uploader</th>
                                        <th style="background-color: #237a5b;" class="text-white">Commands</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(mef, index) in filteredMedicalFilesHeaders">
                                        <td class="align-middle">
                                            {{ mef.clfh_dateadded }}
                                        </td>
                                        <td class="align-middle">
                                            {{ mef.clfh_desc }}
                                        </td>
                                        <td class="align-middle">
                                            {{ mef.emp_firstname }} {{ mef.emp_lastname }}
                                        </td>
                                        <td class="align-middle">
                                            <div class="d-flex gap-2 justify-content-center">
                                                <select class="form-control form-select-sm"
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
                                                    @click="viewImage(mef.clfh_id)" class="btn btn-secondary btn-sm">
                                                    <i class="mr-2 fa-solid fa-eye"></i>Results
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
                                                <Loader />
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
            <div v-if="!imageLoading" class="w-100 d-flex justify-content-end gap-2 mb-2">
                <button :disabled="saving || uploading ? true : false" type="button" @click="viewImage(0)"
                    class="btn btn-sm btn-primary">
                    Back to List
                </button>
                <button :disabled="saving || uploading ? true : false" type="button" @click="downloadImage(folderName, fileName)"
                    class="btn btn-sm btn-dark">
                    Download
                </button>
            </div>
            <div class="w-100">
                <div v-if="imageLoading"
                    class="border w-100 d-flex flex-column justify-content-center align-content-center p-3">
                    <Loader/>
                </div>
                <div v-else
                    class="border w-100 d-flex flex-column justify-content-center align-content-center p-3">
                    <div v-if="fileName" class="flex flex-col justify-center items-center overflow-auto">
                        <img :src="fileName ? 'http://localhost:8000/storage/clinic/' + folderName + '/' + fileName : '/img/profile_default.png'"
                            class="h-100 object-contain border-2 border-gray-300" />

                    </div>
                    <div v-else class="flex flex-col gap-2 justify-center items-center">
                        <p class="fw-bold text-danger">No Image Found</p>

                        <form @submit.prevent="upload()" method="post" enctype="multipart/form-data"
                            class="row p-3 bg-secondary-subtle rounded-3 border justify-content-center align-content-center">
                            <div class="col-auto">
                                <input type="file" class="form-control form-control-sm" @change="handleImage">
                            </div>
                            <div class="col-auto">
                                <button :disabled="!image || uploading ? true : false" type="submit"
                                    class="btn btn-sm btn-dark">Upload Image File
                                </button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</template>