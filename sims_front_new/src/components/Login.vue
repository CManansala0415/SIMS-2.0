<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router'
const router = useRouter();

const isLogging = ref(false)
const form = ref({
    email: '',
    password: ''
})

const getToken = async () => {
    await axios.get('/sanctum/csrf-cookie')
}

const handleLogin = async () => {
    isLogging.value = true
    await getToken();
    try{
        await axios({
            method: "POST",
            url: '/login',
            data:{
                email: form.value.email,
                password: form.value.password
            }
        }).then(async (results) => {
            // console.log(results.config.headers)
            isLogging.value = false
            router.push("/home");

        });
    }catch(err){
        alert('Login Failed, Please Try Again')
        isLogging.value = false
        // location.reload()
    }
}

</script>
<template>
    <div class="container  w-100 m-0 p-0 h-100">
        <div class="row g-2">
            <div class=" shadow border  col-5 p-5">
                <h2 class="mb-5 fw-bolder">Login</h2>
                    <form @submit.prevent="handleLogin" class="d-flex flex-column align-items-start">
                        <div class="mb-3 d-flex flex-column align-items-start w-100">
                            <label for="username" class="form-label">Email address</label>
                            <input type="email" class="form-control" id="username" aria-describedby="emailHelp" v-model="form.email" required>
                            <div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div>
                        </div>
                        <div class="mb-3 d-flex flex-column align-items-start w-100">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" v-model="form.password" required>
                        </div>
                        <button type="submit" class="btn btn-primary" :disabled="isLogging? true:false">
                            <span v-if="isLogging">Logging In...</span> 
                            <span v-else>Log me in</span> 
                        </button>
                    </form>
            </div>
            <div class="col-7">
                <div class="d-flex flex-column overflow-auto p-2 h-100 d-flex align-content-center justify-content-center">
                    <div class="mb-2">
                        <p class=" fs-3 fw-bold">Welcome to SIMS</p>
                        <small class="form-text text-muted">School Information and Management System</small>
                    </div>
                    <div class="mt-4 mb-4 justify-text p-5">
                        <p >
                        CELTECH COLLEGE adheres to the philosophy of transformative education for personal integrity, professionalism, 
                        social responsibility, productive citizenship, enlightened nationalism and global competitiveness.
                    </p>
                    </div>
                <div class="mt-2">
                        <p small class="form-text text-muted ">Central Luzon College of Technology</p>
                        <p small class="form-text text-muted">2023 Key. All Rights Reserved | Design By: CG</p>
                </div>
                </div>
            </div>
        </div>
    </div>

</template>
  