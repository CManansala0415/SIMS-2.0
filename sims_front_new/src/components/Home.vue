<script setup>
import axios from 'axios';
import { ref, onMounted, computed } from 'vue';
import { getUserID } from "../routes/user";
import { useRouter, useRoute } from 'vue-router'
import { formatDateTime, getHolidays } from './Generators.js'
import { getAnnouncement, editAnnouncement } from './Fetchers.js'
import NeuLoader1 from './snippets/loaders/NeuLoader1.vue';
import NeuLoader4 from './snippets/loaders/NeuLoader4.vue';

const user = ref('')
const userID = ref('')
const router = useRouter();
const route = useRoute();
const path = computed(() => route.path)
const preLoading = ref(false)
const announcementData = ref([])
const searchAnnouncement = ref('')
const emit = defineEmits(['fetchUser', 'doneLoading'])

onMounted(async () => {
  //get user here

  preLoading.value = true
  await router.isReady()
  getUserID().then((results) => {
    window.__userAccess = results.access || [];
    user.value = results.account.data.name
    userID.value = results.account.data.id
    emit('fetchUser', results)
    emit('doneLoading', false)

    getAnnouncement(1).then((results) => {
      announcementData.value = results.data
      preLoading.value = false

    })
  }).catch((err) => {
    // alert('Unauthorized Session, Please Log In')
    // router.push("/");
  })


})


const search = () => {
 preLoading.value = true
 getAnnouncement(2, searchAnnouncement.value).then((results) => {
    announcementData.value = results.data
    preLoading.value = false
  })
}

const mode = ref(1)
const editId = ref(1)
const editData = (type, data) => {
  if (type === 'new') {
    mode.value = 1
    editId.value = 0
    annHeader.value = ''
    annSubHeader.value = ''
    annContent.value = ''

  } else if (type === 'update') {
    mode.value = 2
    editId.value = data.dsh_id
    annHeader.value = data.dsh_header
    annSubHeader.value = data.dsh_subheader
    annContent.value = data.dsh_content

  } else {
    mode.value = 3
    editId.value = data.dsh_id

    let msg_title = "Deleting Announcement"
    let msg_content = "Are you sure you want to delete this announcement?"

    annData.value = {
      dsh_id: editId.value,
      dsh_updatedby: userID.value,
      dsh_mode: mode.value,
    }

    executeAnnouncement(msg_title, msg_content)
  }
}

const annData = ref({
  dsh_header: '',
  dsh_subheader: '',
  dsh_content: '',
  dsh_addedby: '',
})

const annHeader = ref('')
const annSubHeader = ref('')
const annContent = ref('')
const saving = ref(false)
const saveAnnouncement = () => {

  if(mode.value == 1){
      annData.value = {
        dsh_header: annHeader.value,
        dsh_subheader: annSubHeader.value,
        dsh_content: annContent.value,
        dsh_addedby: userID.value,
        dsh_mode: mode.value,
        dsh_type: 1
      }
  }else{
      annData.value = {
        dsh_id: editId.value,
        dsh_header: annHeader.value,
        dsh_subheader: annSubHeader.value,
        dsh_content: annContent.value,
        dsh_updatedby: userID.value,
        dsh_mode: mode.value,
        dsh_type: 1
      }
  }

  let msg_title = ''
  let msg_content = ''

  if(mode.value == 1){
    msg_title = "Posting Announcement"
    msg_content = "Are you sure you want to post this announcement?"
  }
  if(mode.value == 2){
    msg_title = "Updating Announcement"
    msg_content = "Are you sure you want to update this announcement?"
  }

  executeAnnouncement(msg_title, msg_content)
}

const executeAnnouncement = (msg_title, msg_content) =>{
  Swal.fire({
      title: msg_title,
      text: msg_content,
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "Yes, Do It!"
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
        editAnnouncement(annData.value).then((results) => {
          if(results.status == 200){
            Swal.fire({
              title: "Success!",
              text: "Announcement has been saved.",
              icon: "success",
              confirmButtonText: "OK"
            }).then(() => {
              Swal.close();
              window.location.reload();
            });
          }else{
              Swal.close();
              Swal.fire({
                title: "Error!",
                text: "Failed to save announcement.",
                icon: "error",
                confirmButtonText: "OK"
              });
          }
          
        })
      }
  });
}




</script>
<template>
  <div>
    <div class="p-3 mb-4">
      <h5 class=" text-uppercase fw-bold">Dashboard</h5>
    </div>

    <div v-if="preLoading">
      <NeuLoader1 />
    </div>
    <div v-else>
      <div class="d-flex gap-2 justify-content-between mb-3">
        <div class="d-flex gap-2 w-75">
          <input type="text" v-model="searchAnnouncement" @keyup.enter="search()" class="neu-input w-100"
            :disabled="preLoading ? true : false" placeholder="Search Announcement" />
          <button @click="search()" type="button" class="neu-btn neu-blue" tabindex="-1"
            :disabled="preLoading ? true : false" style="width: 180px;">
            <font-awesome-icon icon="fa-solid fa-magnifying-glass" /> Search
          </button>
        </div>
        <div class="d-flex flex-wrap justify-content-end gap-2">
          <button tabindex="-1" data-bs-toggle="modal" data-bs-target="#editmodal" @click="editData('new')" 
            type="button" class="neu-btn neu-green" :disabled="preLoading ? true : false">
            <font-awesome-icon icon="fa-solid fa-add" /> New Announcement
          </button>
        </div>
      </div>
      <div class="small-font">
        <div class="row g-3">
          <div v-for="(dsh, index) in announcementData" class="col-12">
            <div class="neu-card p-3">
              <div class="d-flex flex-column gap-3 px-5 py-3">
                <div class="text-start text-dim d-flex gap-3 align-items-center justify-content-start">
                  <div>
                    <div class="neu-btn-sm neu-white rounded-2">
                      <img src="/img/user.png" class="img-fluid rounded-circle" width="40" height="40">
                    </div>
                  </div>
                  <div>
                    <p class="m-0  fw-bold">{{ dsh.emp_firstname }} {{ dsh.emp_middlename || '' }} {{ dsh.emp_lastname
                      }} {{ dsh.emp_suffixname || '' }}</p>
                    <small>{{ formatDateTime(dsh.dsh_dateadded) }}</small>
                  </div>

                </div>
                <div class="text-start text-dim border-bottom border-secondary-subtle mt-3">
                  <h6 class="m-0 fw-bold mb-2">{{ dsh.dsh_header }}</h6>
                  <p class="text-secondary  mb-3">{{ dsh.dsh_subheader }}</p>
                </div>
                <div class="text-start border-bottom border-secondary-subtle">
                  <div>
                    <p class="preserve-text">{{ dsh.dsh_content }}</p>
                  </div>
                  <!-- <div v-if="dsh.dsh_image" class="d-flex justify-content-center p-3 neu-card-inner">
                    <img :src="dsh.dsh_image" class="post-img">
                  </div> -->
                </div>
                <div class="text-start mt-3">
                  <div class="d-flex gap-3 justify-content-between align-items-center">
                    <div class="d-flex gap-2 align-items-center justify-content-start">
                      <button class="neu-btn-ack neu-white px-3"><font-awesome-icon
                          icon="fa-solid fa-thumbs-up" /></button>
                      <button class="neu-btn-ack neu-white px-3"><font-awesome-icon
                          icon="fa-solid fa-thumbs-down" /></button>
                      <button v-if="dsh.dsh_addedby == userID" class="neu-btn-ack neu-white px-3"
                        @click="editData('update', dsh)" data-bs-toggle="modal"
                        data-bs-target="#editmodal"><font-awesome-icon icon="fa-solid fa-pen" /></button>
                      <button v-if="dsh.dsh_addedby == userID" class="neu-btn-ack neu-white px-3"
                        @click="editData('delete', dsh)"><font-awesome-icon icon="fa-solid fa-trash" /></button>
                    </div>
                    <div class="d-flex gap-2 align-items-center justify-content-start">
                      <p class="m-0">Acknowledges: <span class="fw-bold">{{ dsh.dsh_total_ack }}</span></p>
                      <p class="m-0">Renounced: <span class="fw-bold">{{ dsh.dsh_total_ren }}</span></p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- <div class="p-3 mt-3 small-font">
        <div class="row mb-2">
          <div class="col-6 p-1">
            <div class="card w-100 h-100" style="width: 18rem;">
              <img src="/src/bsmt.jpg" class="card-img-top" alt="...">
              <div class="card-body h-50 overflow-auto text-start d-flex flex-column justify-content-around">
                <h6 class="card-title">Bachelor's of Science in Marine Transportation</h6>
                <p class="card-text">is a four-year degree program in the Philippines designed to train students to
                  become
                  marine deck officers, equipping them with the knowledge and skills for navigation, ship handling, and
                  maritime
                  operations.</p>
              </div>
              <ul class="list-group list-group-flush">
                <li class="list-group-item text-success fw-bold">BSMT</li>
                <li class="list-group-item">Bachelor's Degree</li>
                <li class="list-group-item">4 Year Term</li>
              </ul>
              <div class="card-body  d-flex gap-2 align-content-around justify-content-center">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-toggle="modal"
                  data-bs-target="#editmodal">
                  View Track
                </button>
                <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal"
                  data-bs-target="#editmodal">
                  Enroll Now
                </button>
              </div>
            </div>
          </div>
          <div class="col-6 p-1">
            <div class="card w-100 h-100" style="width: 18rem;">
              <img src="/src/ccje.jpg" class="card-img-top" alt="...">
              <div class="card-body h-50 overflow-auto text-start d-flex flex-column justify-content-around">
                <h6 class="card-title">Bachelor's of Science in Criminal Justice Education</h6>
                <p class="card-text">a four-year degree program that focuses on the study of crime, the justice system,
                  and
                  related fields, preparing graduates for careers in law enforcement, corrections, and other areas of
                  criminal
                  justice. </p>
              </div>
              <ul class="list-group list-group-flush">
                <li class="list-group-item text-primary fw-bold">BSCJE</li>
                <li class="list-group-item">Bachelor's Degree</li>
                <li class="list-group-item">4 Year Term</li>
              </ul>
              <div class="card-body  d-flex gap-2 align-content-around justify-content-center">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-toggle="modal"
                  data-bs-target="#editmodal">
                  View Track
                </button>
                <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal"
                  data-bs-target="#editmodal">
                  Enroll Now
                </button>
              </div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-6 p-1">
            <div class="card w-100" style="width: 18rem;">
              <img src="/src/bsit.jpg" class="card-img-top" alt="...">
              <div class="card-body h-50 overflow-auto text-start d-flex flex-column justify-content-around">
                <h6 class="card-title">Bachelor's of Science in Information and Technology</h6>
                <p class="card-text">is a four-year degree program focusing on the study and application of computer
                  utilization
                  and software to plan, install, customize, operate, manage, administer, and maintain information
                  technology
                  infrastructure, with the goal of developing IT professionals.</p>
              </div>
              <ul class="list-group list-group-flush">
                <li class="list-group-item text-info fw-bold">BSIT</li>
                <li class="list-group-item">Bachelor's Degree</li>
                <li class="list-group-item">4 Year Term</li>
              </ul>
              <div class="card-body  d-flex gap-2 align-content-around justify-content-center">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-toggle="modal"
                  data-bs-target="#editmodal">
                  View Track
                </button>
                <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal"
                  data-bs-target="#editmodal">
                  Enroll Now
                </button>
              </div>
            </div>
          </div>
          <div class="col-6 p-1">
            <div class="card w-100 h-100" style="width: 18rem;">
              <img src="/src/bshm.jpg" class="card-img-top" alt="...">
              <div class="card-body h-50 overflow-auto text-start d-flex flex-column justify-content-around">
                <h6 class="card-title">Bachelor of Science in Hospitality and Management</h6>
                <p class="card-text">is a four-year degree program that prepares students for careers in the hospitality
                  industry, focusing on areas like hotel, restaurant, resort, and event management. </p>
              </div>
              <ul class="list-group list-group-flush">
                <li class="list-group-item text-warning fw-bold">BSHM</li>
                <li class="list-group-item">Bachelor's Degree</li>
                <li class="list-group-item">4 Year Term</li>
              </ul>
              <div class="card-body  d-flex gap-2 align-content-around justify-content-center">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-toggle="modal"
                  data-bs-target="#editmodal">
                  View Track
                </button>
                <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal"
                  data-bs-target="#editmodal">
                  Enroll Now
                </button>
              </div>
            </div>
          </div>
        </div>
      </div> -->
    </div>
  </div>

  <!-- Button trigger modal -->
  <!-- <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editmodal">
    Launch demo modal
  </button> -->

  <!-- Modal -->
  <div class="modal fade" id="editmodal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg  modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="staticBackdropLabel">Announcement</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
            ></button>
        </div>
        <div class="modal-body neu-bg">
          <form @submit.prevent="saveAnnouncement" class="neu-card p-3">
            <input class="neu-input mb-3" placeholder="Header Title" required v-model="annHeader">
            <input class="neu-input mb-3" placeholder="Sub Header Title" required v-model="annSubHeader">
            <textarea class="neu-input mb-3" placeholder="Content" rows="5" required v-model="annContent"></textarea>
            <button type="submit" class="neu-btn neu-green mb-3 p-2"><font-awesome-icon icon="fa-solid fa-upload" :disabled="saving? true:false"/> Save</button>
          </form>
        </div>
        <div class="modal-footer d-flex justify-content-between">
          <div class="form-group">
            <small id="emailHelp" class="form-text text-muted">We'll never share your personal information
              with anyone
              else (Data Privacy Act of 2012)</small>
          </div>
          <div class="d-flex gap-2">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
              >Close</button>
            <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
          </div>
        </div>
      </div>
    </div>
  </div>

</template>
