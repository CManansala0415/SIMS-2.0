<script setup>
import { ref, onMounted, computed } from 'vue';
import Loader from '../loaders/Loading1.vue';
import {
    getFamily,
    getStudentIdDetails
} from "../../Fetchers.js";

import {
    qrImageGenerator,
    pdfGenerator,
    imageFetcher
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

const qrimage = ref('')
const profileId = ref('')
const indicator = ref('#000000')
const idGenerator = ref('')
const family = ref([])
const preLoading = ref(true)
const studentData = ref([])

onMounted(() => {
    getStudentIdDetails(student.value.enr_id).then((result1)=>{
        studentData.value = result1[0]
        getFamily(student.value.per_famid,2).then((result2)=>{
            if(Object.keys(result2).length){
                family.value = result2[0]
            }else{
                family.value = 
                    {
                        fam_firstname:'',
                        fam_middlename:'',
                        fam_lastname:'',
                        fam_suffixname:'',
                        fam_contact:''
                    }
            }
            // eb1757 1st
            // c0c90e 2nd
            // 9cff12 3rd
            // 23377a 4th
            // 1202c4 G11
            // b8048e G12
            switch(student.value.enr_gradelvl){
                case 1:
                    indicator.value = '#eb1757'
                    break;
                case 2:
                    indicator.value = '#c0c90e'
                    break;
                case 3:
                    indicator.value = '#9cff12'
                    break;
                case 4:
                    indicator.value = '#23377a'
                    break;
                case 5:
                    indicator.value = '#1202c4'
                    break;
                case 6:
                    indicator.value = '#b8048e'
                    break;
            }   


            // imageFetcher(student.value.per_profile,'myImage').then(response => {
            //         // console.log(response)
            // });

            profileId.value = student.value.per_profile ? 'http://localhost:8000/api/get-person-image/' + student.value.per_profile : '/img/profile_default.png'
            // idGenerator.value = '<img style="height: 100px; width: 100px; border-radius:100%;" src="'+profileId.value+'"/>'
            preLoading.value = false
        })
    })
    

})

const printForm = (studentid) => {
    let name = 'LC-'+studentid
    qrImageGenerator(studentid).then((result) => {
        qrimage.value = result
        let size = [2.125,3.375]
        pdfGenerator(name, size, 'portrait', 0.03)
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
    <!-- CR80 cards are 3.375" x 2.125" (the same size as a credit card) and are the standard, most commonly used size of PVC card. -->
     <!-- height: 640px; width: 480px; -->
        <Loader v-if="preLoading"/>
        <div v-else class="overflow-auto">
            <div id="printform">
                <!-- Front -->
                <div class="position-relative d-flex flex-column justify-content-center align-items-center align-content-start" style="height: 319px;">
                    <div style="height: 81px; width:100%; background-color: #237a5b; color:#FFFFFF" class="bg-opaque">
                        <div style="width:100%; line-height: 0.3;text-align: center; padding: 1px; margin-top: 12px;">
                            <span class="classic-font" style="font-size:7px; font-weight: bold;">
                                CENTRAL LUZON COLLEGE OF SCIENCE & TECHNOLOGY, INC <br/>
                                <span style="font-weight:100;font-size:5px; margin-top: 2px;">Mendoza St., Brgy.
                                Sto. Rosario, City of San Fernando,
                                Pampanga, Philippines, 2000
                                <br/>Since 1959</span> 
                            </span>
                        </div>
                    </div>
                    <div style="height: 235px; width:100%;" class="idbg">
                        <div style="margin-top: 75px; width:100%; line-height: 0.1;text-align: center; padding: 1px;">
                            <span style="font-size: 8px; font-weight: bold;">
                                {{ studentData.per_firstname }}
                                {{ studentData.per_middlename ? studentData.per_middlename : ' ' }}
                                {{ studentData.per_lastname }}
                                {{ studentData.per_suffixname ? studentData.per_suffixname : ' ' }}<br/>
                                <!-- <span style="font-size: 4px; font-weight: thin;">Student Name</span> -->
                            </span>
                        </div>  
                        <div style="width:100%; line-height: 0.1;text-align: center; padding: 1px;">
                            <span style="font-size: 5px; font-weight: bold;">
                                Bachelor of Science in Information and Technology<br/>
                                <!-- <span style="font-size: 4px; font-weight: thin;">Course</span> -->
                            </span>
                        </div>
                        <div style="width:100%; margin-top: 5px; display: flex; justify-content: center; align-items: center; align-content: center;">
                            <span class="px-5 py-1" 
                                    :style="`color:#FFFFFF;
                                    background-color: `+indicator+`; 
                                    border-radius: 10px; 
                                    font-size:10px; 
                                    font-weight: bolder;`">
                                    {{ studentData.studentid }}
                            </span>
                        </div>
                        <div style="width:100%; line-height: 0.1;text-align: center; padding: 1px;">
                            <span style="font-size: 5px; font-weight: bold;">
                                {{ studentData.program }} Department<br/>
                                <!-- <span style="font-size: 4px; font-weight: thin;">Course</span> -->
                            </span>
                        </div>
                        <!-- eb1757 -->
                        <!-- c0c90e -->
                        <!-- 9cff12 -->
                        <!-- 23377a -->

                        <!-- 1202c4 -->
                        <!-- b8048e -->
                    
                        <div style="margin-top:1px; width:100%; line-height: 0.1;text-align: center; padding: 1px; display: flex; flex-direction: column; justify-content: center; align-items: center; align-content: center;">
                            <img src="/img/sig1.png" height="45px" width="45px"/>
                            <p style="font-size: 4px; font-weight: bold;">
                                Student's Signature
                            </p>
                        </div>  
                        <div style="width:100%;background-color: #237a5b;" class="d-flex justify-content-center align-content-center align-items-center">
                        <div style="width: 150px; text-align: center; padding: 5px; color: #FFFFFF;font-size: 4px; font-weight: thin;">
                            To protect your ID card, be mindful of where you display it and be cautious about sharing information online. 
                            Avoid posting your ID card on social media, and consider using security features like laminating or badge holders. 
                            Also, be aware of your surroundings and keep your ID card in a secure location to prevent theft or loss. 
                                <!-- <span style="font-size: 4px; font-weight: thin;">Student Name</span> -->
                        </div>
                        </div>
                        <br/>
                    </div>
                    <!-- <div class="border shadow rounded-circle position-absolute bg-white shadow" 
                        :style="`
                            top:47px; height: 100px; width: 100px; 
                            background-image: url('`+profileId+`');
                            background-position: center;
                            background-repeat: no-repeat;
                            background-size: cover;
                        `">
                    </div> -->
                    <div class="border shadow rounded-circle position-absolute bg-white shadow" style="top:47px;">
                        <img style="height: 100px; width: 100px; border-radius:100%;" :src="profileId"/>
                    </div>
                </div>
                <!-- back -->
                <div class="position-relative d-flex flex-column justify-content-center align-items-center align-content-start" style="height: 319px;">
                    <div style="height: 319px; width:100%;" class="bg-opaque">
                        <div class="d-flex flex-column justify-content-center align-items-center w-100 mt-2">
                            <div class="d-flex flex-column justify-content-center align-items-center card" 
                                style="height:100px; width: 100px;" 
                                id="qrcode" v-html="qrimage"></div>
                        </div>
                        <div class="d-flex flex-column justify-content-center align-items-center mt-2">
                            <div style="width: 150px; text-align: center; padding: 5px; font-size: 7px;">
                                Use this QR code for fast-tracking when engaging in transactions within school premises
                            </div>
                        </div>
                        <div class="d-flex flex-column justify-content-center align-items-center mt-2 text-center">
                            <span style="font-size: 8px; font-weight: bold; text-transform: uppercase;">
                                {{ studentData.per_curr_home }}, 
                                <br/>{{ studentData.brgyDesc }}, {{ studentData.citymunDesc }}, 
                                <br/>{{ studentData.provDesc }}, {{ studentData.countryName }}
                            </span>
                            <span style="font-size: 8px; font-weight: bold; margin-top: 4px;">
                                <br/>
                                <span style="font-size: 8px; font-weight: bold; text-transform: uppercase;">
                                    {{ family.fam_firstname }}
                                    {{ family.fam_middlename ? family.fam_middlename : ' ' }}
                                    {{ family.fam_lastname }}
                                    {{ family.fam_suffixname ? family.fam_suffixname : ' ' }}
                                </span> 
                                <br/>{{ family.fam_contact }}
                            </span>
                        </div>
                        <div style="margin-top:4px; width:100%; line-height: 1.2;text-align: center; padding: 1px; display: flex; flex-direction: column; justify-content: center; align-items: center; align-content: center;">
                            <img src="/img/sig1.png" height="45px" width="45px"/>
                            <p style="font-size: 8px;">
                                <span style="font-weight:bold;">Renato P. Legaspi, Ph.D.</span>
                                <br/> President / CEO
                            </p>
                        </div>  
                    </div>
                </div>
            </div>
            <button class="btn btn-success mt-2" @click="printForm(studentData.studentid)">Download ID</button>
        </div>
</template>