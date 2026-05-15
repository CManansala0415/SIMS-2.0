<script setup>
import { ref, onMounted, computed } from 'vue';
import Loader from '../loaders/Loading1.vue';
import {
    getFamily,
    getStudentIdDetails,
    getCommandUpdate
} from "../../Fetchers.js";

import {
    qrImageGenerator,
    pdfGenerator,
    imageFetcher,
    lvl2Encrypt,
    lvl2Decrypt,
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
const signatureId = ref('')
const indicator = ref('#000000')
const idGenerator = ref('')
const family = ref([])
const preLoading = ref(true)
const studentData = ref([])
const semInfo = ref('')
const yrFromInfo = ref('')
const yrFromto = ref('')
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

            getCommandUpdate().then((result) => {
                semInfo.value = result[0].quar_code;
                yrFromInfo.value = result[1].sett_yearfrom;
                yrFromto.value = result[1].sett_yearto;
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

                profileId.value = student.value.per_profile ? 'http://sims.clcst.edu.local:8000/api/get-person-image/' + student.value.per_profile +'/1' : '/img/profile_default.png'
                signatureId.value = student.value.per_signature ? 'http://sims.clcst.edu.local:8000/api/get-person-image/' + student.value.per_signature+'/3' : '/img/sig1.png'
                // console.log( signatureId.value)
                // idGenerator.value = '<img style="height: 100px; width: 100px; border-radius:100%;" src="'+profileId.value+'"/>'
                preLoading.value = false

                // let decrypted = lvl2Decrypt('OG4wOjB9aWdxZHdjbnxzY2RtcTg=', 'CLCST_SECRET');
                // let obj = JSON.parse(decrypted);
                // console.log(obj.sid);
                // console.log(studentData.value)
            });

        })
    })
    

})

const downloading = ref(false)
const printForm = (studentid) => {
    downloading.value = true
    let name = 'LC-'+studentid

    let key = "SIMS_CLCST_@2026!--*";
    let original = JSON.stringify({
        sid: studentid
    });

    const encrypted = lvl2Encrypt(original, key);
    // console.log("QR DATA:", encrypted);

    const decrypted = lvl2Decrypt(encrypted, key);
    // console.log("BACK:", JSON.parse(decrypted));

    qrImageGenerator(encrypted).then(async(result) => {
        qrimage.value = result
        let size = [2.125,3.375]
        await pdfGenerator(name, size, 'portrait', 0.03)
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
        <div v-else class="overflow-auto p-3">
            <div id="printform">
                <!-- Front -->
                <div class="position-relative d-flex flex-column justify-content-center align-items-center align-content-start" style="height: 319px;">
                    <div style="height: 81px; width:100%; background-color: #185718; color:#FFFFFF; border-bottom: 5px solid #f5e90f" class="bg-opaque">
                        <div style="width:100%; line-height: 0.6;text-align: center; padding: 1px; margin-top: 12px;">
                            <span class="classic-font" style="font-size:9px; font-weight: bold;">
                                CENTRAL LUZON COLLEGE <br/> OF SCIENCE & TECHNOLOGY, INC <br/>
                                <span style="font-weight:100;font-size:4px; margin-top: 2px;">B. Mendoza St.,
                                Sto. Rosario, City of San Fernando,
                                Pampanga, Philippines, 2000</span> 
                                <br/>
                            </span>
                        </div>
                    </div>
                    <div style="height: 235px; width:100%; color:black" class="idbg">
                        <div style="margin-top: 75px; width:100%; line-height: 0.1;text-align: center; padding: 1px;">
                            <span style="font-size: 9px; font-weight: bolder; text-transform: uppercase;">
                                {{ studentData.per_firstname }}
                                {{ studentData.per_middlename ? studentData.per_middlename : ' ' }}
                                {{ studentData.per_lastname }}
                                {{ studentData.per_suffixname ? studentData.per_suffixname : ' ' }}<br/>
                                <!-- <span style="font-size: 4px; font-weight: thin;">Student Name</span> -->
                            </span>
                        </div>  
                         <!-- display: flex; justify-content: center; align-items: center; align-content: center; -->
                        <div style="width:100%; margin-top: 5px; text-align: center;">
                            <span class="px-5 py-1" 
                                    :style="`color:#FFFFFF;
                                    background-color: `+indicator+`; 
                                    border-radius: 10px; 
                                    font-size:10px; 
                                    font-weight: bolder;`">
                                    {{ studentData.studentid? studentData.studentid: '000000' }}
                            </span>
                        </div>
                        <div style="width:100%; line-height: 0.6;text-align: center; padding: 1px; margin-top:3px">
                            <span style="font-size: 8px; font-weight: bold; text-transform: uppercase;">
                                {{ studentData.course }}<br/>
                                <!-- <span style="font-size: 4px; font-weight: thin;">Course</span> -->
                            </span>
                        </div>
                        <div style="width:100%; line-height: 0.1;text-align: center; padding: 1px; margin-top:4px">
                            <span style="font-size: 6px; font-weight: normal;">
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
                            <img :src="signatureId" height="44px" width="44px" style="opacity: 1;"/>
                            <p style="font-size: 7px; font-weight: bold;">
                                Student's Signature
                            </p>
                        </div>  
                        <div style="height:28px;width:100%;background-color: #185718; border-top: 5px solid #f5e90f;" class="d-flex justify-content-center align-content-center align-items-center">
                            <!-- <div style="width: 150px; text-align: center; padding: 5px; color: #FFFFFF;font-size: 6.5px; font-weight: thin; ">
                                Keep your ID card secure and avoid posting it on social media. Use a badge holder 
                                or laminate for protection, and always store it safely to prevent loss or theft.
                            </div> -->
                            <div style="width: 150px; text-align: center; padding: 5px; color: #FFFFFF;font-size: 8px; font-weight: bold; ">
                                A.Y {{ yrFromInfo }} - {{ yrFromto }}<br/>
                            </div>
                        </div>
                        <br/>
                    </div>
                    <!-- <div class="border shadow rounded-circle position-absolute bg-white shadow" 
                        :style="`
                            top:49px; height: 100px; width: 100px; 
                            background-image: url('`+profileId+`');
                            background-position: center;
                            background-repeat: no-repeat;
                            background-size: cover;
                        `">
                    </div> -->
                    <div class="border shadow rounded-circle position-absolute bg-white shadow" style="top:50px;">
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
                            <div style="width: 180px; text-align: center; padding: 5px; font-size: 6px;">
                                Use this QR code for fast-tracking when engaging in transactions within school premises.
                            </div>
                        </div>
                        <div class="d-flex flex-column justify-content-center align-items-center mt-2 text-center">
                            <span v-if="
                                studentData.per_curr_home&&
                                studentData.brgyDesc&&
                                studentData.citymunDesc&&
                                studentData.provDesc
                            " style="font-size: 9px; font-weight: bold; text-transform: uppercase;">
                                {{ studentData.per_curr_home }}, {{ studentData.brgyDesc }},
                                <br/>{{ studentData.citymunDesc }}, {{ studentData.provDesc }}
                            </span>
                            <span v-else style="font-size: 9px; font-weight: bold; text-transform: uppercase;">N/A</span>
                            <span style="font-size: 9px; width: 80%; border-top: 1px solid gray">Address</span>

                            <span v-if="
                                family.fam_firstname&&
                                family.fam_lastname
                            "
                            style="font-size: 9px; font-weight: bold; margin-top: 2px;">
                                <br/>
                                <span style="font-size: 9px; font-weight: bold; text-transform: uppercase;">
                                    {{ family.fam_firstname }}
                                    {{ family.fam_middlename ? family.fam_middlename : ' ' }}
                                    {{ family.fam_lastname }}
                                    {{ family.fam_suffixname ? family.fam_suffixname : ' ' }}
                                </span> 
                                <br/><span style="font-family:Arial, Helvetica, sans-serif;font-weight:bold">0{{ family.fam_contact }}</span>
                            </span>
                            <span v-else style="font-size: 9px; font-weight: bold; text-transform: uppercase;">N/A</span>
                            <span style="font-size: 9px; width: 80%; border-top: 1px solid gray">Emergency Contact</span>

                        </div>
                        <div style="margin-top:1.5px; width:100%; line-height: 1.2;text-align: center; padding: 1px; display: flex; flex-direction: column; justify-content: center; align-items: center; align-content: center;">
                            <img src="/img/rpl.png" height="43px" width="43px"/>
                            <p style="font-size: 8px;">
                                <span style="font-weight:bold;">Renato P. Legaspi, Ph.D.</span>
                                <br/> President / CEO
                            </p>
                        </div>  
                    </div>
                </div>
            </div>
            <button class="neu-btn neu-green mt-2" @click="printForm(studentData.studentid)" :disabled="downloading">Download ID</button>
        </div>
</template>