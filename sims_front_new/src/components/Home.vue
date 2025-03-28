<script setup>
import axios from 'axios';
import { ref, onMounted, computed } from 'vue';
import { getUserID } from "../routes/user";
import { useRouter, useRoute } from 'vue-router'

const user = ref('')
const userID = ref('')
const router = useRouter();
const route = useRoute();
const path = computed(() => route.path)
const isLoading = ref(false)
const emit = defineEmits(['fetchUser'])

onMounted(async () => {
  //get user here

  isLoading.value = true
  await router.isReady()
  getUserID().then((results) => {
    user.value = results.account.data.name
    userID.value = results.account.data.id
    isLoading.value = false

    emit('fetchUser', results)

  }).catch((err) => {
    alert('Unauthorized Session, Please Log In')
    router.push("/");
  })
})


</script>
<template>
  <h2>Dashboard</h2>
  <div class="d-flex gap-2 mt-3 small-font ">
    <div class="card" style="width: 18rem;">
      <img src="/public/src/bsmt.jpg" class="card-img-top" alt="...">
      <div class="card-body h-50 overflow-auto text-start d-flex flex-column justify-content-around">
        <h6 class="card-title">Bachelor's of Science in Marine Transportation</h6>
        <p class="card-text">is a four-year degree program in the Philippines designed to train students to become
          marine deck officers, equipping them with the knowledge and skills for navigation, ship handling, and maritime
          operations.</p>
      </div>
      <ul class="list-group list-group-flush">
        <li class="list-group-item text-success fw-bold">BSMT</li>
        <li class="list-group-item">Bachelor's Degree</li>
        <li class="list-group-item">4 Year Term</li>
      </ul>
      <div class="card-body  d-flex gap-2 align-content-around justify-content-center">
        <button type="button" class="btn btn-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#exampleModal">
          View Track
        </button>
        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#exampleModal">
          Enroll Now
        </button>
      </div>
    </div>
    <div class="card" style="width: 18rem;">
      <img src="/public/src/ccje.jpg" class="card-img-top" alt="...">
      <div class="card-body h-50 overflow-auto text-start d-flex flex-column justify-content-around">
        <h6 class="card-title">Bachelor's of Science in Criminal Justice Education</h6>
        <p class="card-text">a four-year degree program that focuses on the study of crime, the justice system, and
          related fields, preparing graduates for careers in law enforcement, corrections, and other areas of criminal
          justice. </p>
      </div>
      <ul class="list-group list-group-flush">
        <li class="list-group-item text-primary fw-bold">BSCJE</li>
        <li class="list-group-item">Bachelor's Degree</li>
        <li class="list-group-item">4 Year Term</li>
      </ul>
      <div class="card-body  d-flex gap-2 align-content-around justify-content-center">
        <button type="button" class="btn btn-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#exampleModal">
          View Track
        </button>
        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#exampleModal">
          Enroll Now
        </button>
      </div>
    </div>
    <div class="card" style="width: 18rem;">
      <img src="/public/src/bsit.jpg" class="card-img-top" alt="...">
      <div class="card-body h-50 overflow-auto text-start d-flex flex-column justify-content-around">
        <h6 class="card-title">Bachelor's of Science in Information and Technology</h6>
        <p class="card-text">is a four-year degree program focusing on the study and application of computer utilization
          and software to plan, install, customize, operate, manage, administer, and maintain information technology
          infrastructure, with the goal of developing IT professionals.</p>
      </div>
      <ul class="list-group list-group-flush">
        <li class="list-group-item text-info fw-bold">BSIT</li>
        <li class="list-group-item">Bachelor's Degree</li>
        <li class="list-group-item">4 Year Term</li>
      </ul>
      <div class="card-body  d-flex gap-2 align-content-around justify-content-center">
        <button type="button" class="btn btn-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#exampleModal">
          View Track
        </button>
        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#exampleModal">
          Enroll Now
        </button>
      </div>
    </div>
    <div class="card" style="width: 18rem;">
      <img src="/public/src/bshm.jpg" class="card-img-top" alt="...">
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
        <button type="button" class="btn btn-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#exampleModal">
          View Track
        </button>
        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#exampleModal">
          Enroll Now
        </button>
      </div>
    </div>
  </div>
  <!-- Button trigger modal -->
  <!-- <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
    Launch demo modal
  </button> -->

  <!-- Modal -->
  <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary">Save changes</button>
        </div>
      </div>
    </div>
  </div>

</template>
