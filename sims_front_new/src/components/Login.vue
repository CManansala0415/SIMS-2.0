<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { getUserID } from "../routes/user";
import { useRouter } from 'vue-router'
import NeuLoader3 from './snippets/loaders/NeuLoader3.vue';
const router = useRouter();
const emit = defineEmits(['fetchUser', 'doneLoading'])
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
            await getUserID().then((results) => {
                if(!results.employee){
                    // alert('This account does not belong to any employee, contact the administrator')
                    Swal.fire({
                        title: "Account Restricted",
                        text: "This account does not belong to any employee, contact the administrator",
                        icon: "error"
                    });

                    axios.post('/logout');
                    isLogging.value = false
                }else{
                    // alert('Welcome ' + results.employee.emp_firstname)
                    Swal.fire({
                        title: "Welcome Back!",
                        text: results.employee.emp_firstname,
                        icon: "success"
                    });

                    emit('fetchUser', results)
                    isLogging.value = false
                    // router.push("/home");
                }
            })
        });
    }catch(err){
        if(err.status == 422){
            // alert('Incorrect creadentials, please try again')
            Swal.fire({
                title: "Account Mismatch",
                text: "Incorrect creadentials, please try again",
                icon: "error"
            });
        }else{
            // alert('Login Failed, Please Try Again')
            Swal.fire({
                title: "Error Occured Somewhere",
                text: "Server Error, Try Again Later",
                icon: "question"
            });
        }
        isLogging.value = false
        // location.reload()
    }
}

</script>
<template>
    <div class="container  w-100 m-0 p-0 h-100">
        <div class="row g-2">
            <div class="col-5 p-5 neu-card">
                <h2 class="mb-5 fw-bolder">Login</h2>
                    <form @submit.prevent="handleLogin" class="d-flex flex-column align-items-start">
                        <div class="mb-3 d-flex flex-column align-items-start w-100">
                            <label for="username" class="form-label">Email address</label>
                            <input type="email" class="neu-input" id="username" aria-describedby="emailHelp" v-model="form.email" required>
                            <div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div>
                        </div>
                        <div class="mb-1 d-flex flex-column align-items-start w-100">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="neu-input" id="password" v-model="form.password" required>
                        </div>
                        <div class="mb-3 form-check">
                            <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault" onclick="password.type = this.checked? 'text':'password'">
                            <label class="form-check-label" for="flexCheckDefault">
                                Show Password
                            </label>
                        </div>
                        <button type="submit" class="neu-btn neu-green p-2" :disabled="isLogging? true:false">
                            <font-awesome-icon icon="fa-solid fa-key"/> &nbsp;
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
                    <div class="d-flex flex-column justify-content-center align-items-center">
                        <NeuLoader3/>
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
  