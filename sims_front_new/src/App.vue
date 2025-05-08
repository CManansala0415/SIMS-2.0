<script setup>
import axios from 'axios';
import { ref, onMounted, computed } from 'vue';
import { getUserID } from "./routes/user";
import { useRouter, useRoute } from 'vue-router'
import loader from './components/snippets/loaders/Loading1.vue'
const user = ref('')
const userID = ref('')
const router = useRouter();
const route = useRoute();
const path = computed(() => route.path)
const isLoading = ref(true)
const accessData = ref([])
onMounted(async () => {

  window.stop()
  //get user here
  isLoading.value = true
  await router.isReady()
  getUserID().then((results) => {
    user.value = results.account.data.name
    userID.value = results.account.data.id
    linker()
    // isLoading.value = false
  }).catch((err) => {
    //alert('Unauthorized Session, Please Log In')
    router.push("/");
    isLoading.value = false
  })

})

const administrativeAccess = ref([])
const transactionsAccess = ref([])
const academicsAccess = ref([])
const administrative = ref(0)
const transactions = ref(0)
const academics = ref(0)
const userName = ref('')
const fetchingUserAccess = ref(true)

const getUser = (data) => {
 
  accessData.value = data.access
  // console.log(accessData.value)
  // filter muna sa 3 categories
  // administrative
  let x = data.access.filter((e) => {
    return e.useracc_category == 1
  })
  // transactions
  let y = data.access.filter((e) => {
    return e.useracc_category == 2
  })
  // academics
  let z = data.access.filter((e) => {
    return e.useracc_category == 3
  })
  
  // find filtered categories if meron silang atleast 1 grant or viewing and modifying is 1
  // administrative
  let a = x.filter((e) => {
    return ((e.useracc_grant == 1)&&(e.useracc_viewing == 1 || e.useracc_modifying == 1))
  })
  // transactions
  let b = y.filter((e) => {
    return ((e.useracc_grant == 1)&&(e.useracc_viewing == 1 || e.useracc_modifying == 1))
  })
  // academics
  let c = z.filter((e) => {
    return ((e.useracc_grant == 1)&&(e.useracc_viewing == 1 || e.useracc_modifying == 1))
  })

  administrative.value = a.length
  transactions.value = b.length
  academics.value = c.length
  userName.value = data.employee.emp_firstname + ' ' + data.employee.emp_lastname


  let adminPrep = a.map((e)=>{
    let link = ''
    let desc = ''
    if(e.useracc_modulecode == 1){
      link = '/registrar-application'
      desc = 'Registrar'
    }
    if(e.useracc_modulecode == 2){
      link = '/registrar-library-books'
      desc = 'Library'
    }
    if(e.useracc_modulecode == 3){
      link = '/registrar-clinical-students'
      desc = 'Clinic'
    }
    return{
      ...e,
      link:link,
      description:desc
    }
  })

  administrativeAccess.value = [...new Map(adminPrep.map(item =>
  [item['useracc_modulecode'], item])).values()]

  // administrativeAccess.value = [
  //   ...adminPrep
  //     .reduce((uniq, curr) => {
  //       if (!uniq.has(curr['useracc_modulecode'])) {
  //         uniq.set(curr['useracc_modulecode'], curr);
  //       }
  //       return uniq;
  //     }, new Map())
  //     .values()
  // ]

  let transacPrep = b.map((e)=>{
    let link = ''
    let desc = ''
    if((e.useracc_modulecode == 4)&&(e.useracc_accesscode==1)){
      link = '/accounting-items'
      desc = 'Accounting'
    }
    if((e.useracc_modulecode == 4)&&(e.useracc_accesscode==2)){
      link = '/accounting-billing'
      desc = 'Billing / Cashier'
    }
    return{
      ...e,
      link:link,
      description:desc
    }
  })

  // transactionsAccess.value = [...new Map(transacPrep.map(item =>
  // [item['useracc_modulecode'], item])).values()]
  transactionsAccess.value = transacPrep
  

  let academicPrep = c.map((e)=>{
    let link = ''
    let desc = ''
    if((e.useracc_modulecode == 5)&&(e.useracc_accesscode==1)){
      link = '/faculty-classes'
      desc = 'Class'
    }
    if((e.useracc_modulecode == 5)&&(e.useracc_accesscode==2)){
      link = '/faculty-student'
      desc = 'Grades'
    }
    if((e.useracc_modulecode == 5)&&(e.useracc_accesscode==3)){
      link = '/faculty-grading-sheet'
      desc = 'Masterlist'
    }
    return{
      ...e,
      link:link,
      description:desc
    }
  })

  // academicsAccess.value = [...new Map(academicPrep.map(item =>
  // [item['useracc_modulecode'], item])).values()]
  academicsAccess.value = academicPrep


  linker()
  fetchingUserAccess.value = false
  isLoading.value = false
}

const linker = () => {
  switch (true) {
    case [
      "/",
    ].includes(path.value):
      router.push("/home");
      switchItem(0, 0)
      break;

    case [
      "/registrar-application",
      "/registrar-enrollment",
      "/registrar-request",
      "/registrar-launch",
      "/registrar-personnel",
      "/registrar-settings"
    ].includes(path.value):

      switchItem(1, 1)
      break;

    case [
      "/registrar-library-books",
      "/registrar-library-books-ddc",
      "/registrar-library-books-borrowers",
      "/registrar-library-cards",
    ].includes(path.value):

      switchItem(1, 2)
      break;

    case [
      "/registrar-clinical-students",
      "/registrar-clinical-employee",
      "/registrar-clinical-medical-supplies"
    ].includes(path.value):

      switchItem(1, 3)
      break;

    case [
      "/accounting-items",
    ].includes(path.value):

      switchItem(2, 1)
      break;

    case [
      "/accounting-billing",
      "/accounting-request",
    ].includes(path.value):

      switchItem(2, 2)
      break;

    case [
      "/faculty-classes",
      "/faculty-student",
      "/faculty-grading-sheet",
    ].includes(path.value):

      switchItem(3, 1)
      break;
  }
}

const handleLogout = async () => {
  Swal.fire({
    title: "Log Out",
    text: "Are you sure you want to logout",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Yes, Im Going Out!"
  }).then(async(result) => {
    if (result.isConfirmed) {
      await axios.post('/logout').then(()=>{
        Swal.fire({
          title: "Logged Out!",
          text: "You Have Been Logged Out",
          icon: "success"
        }).then(()=>{
          router.push("/");
        });
      });
    }
  });

  // if (confirm("Are you sure you want to logout") == true) {
  //   await axios.post('/logout');
  //   alert('Logged Out')
  //   router.push("/");
  // } else {
  //   return false;
  // }

}

const academicItems = ref(false)
const transactionItems = ref(false)
const administrativeItems = ref(false)
const generalItems = ref(false)
const itemSelected = ref(0)
const accessSelected = ref(0)

let hiding = ref()
const doneInterval = ref(150)
const interval = (item) => {
  clearTimeout(hiding.value);
  hiding.value = setTimeout(() => showItems(item), doneInterval.value);
}

const showItems = (item) => {
  generalItems.value = false
  administrativeItems.value = false
  transactionItems.value = false
  academicItems.value = false

  switch (item) {
    case 0:
      generalItems.value = !generalItems.value
      break;
    case 1:
      administrativeItems.value = !administrativeItems.value
      break;
    case 2:
      transactionItems.value = !transactionItems.value
      break;
    case 3:
      academicItems.value = !academicItems.value
      break;
    default:
      return false;
  }
}

const switchItem = (access, item) => {

  switch (access) {
    case 0:
      accessSelected.value = access
      itemSelected.value = item
      break;
    case 1:
      accessSelected.value = access
      itemSelected.value = item
      break;
    case 2:
      accessSelected.value = access
      itemSelected.value = item
      break;
    case 3:
      accessSelected.value = access
      itemSelected.value = item
      break;
    default:
      return false;
  }

}


const def_class = ref("nav-static border p-2");
const active_class = ref("nav-static border p-2 active");

</script>
<template>
  <div class="container border rounded-3 shadow p-5 flex-column bg-white">

    <nav class="navbar navbar-light bg-light mb-5">
      <div class="container-fluid">

        <div class="flex-column mb-2">
          <div class="row">
            <div class="col d-flex justify-content-start">
              <h3 class="fw-bold">SIMS</h3>
            </div>
          </div>
          <div class="row">
            <div class="col d-flex justify-content-start">
              <small id="emailHelp" class="form-text text-muted">School Information & Management System</small>
            </div>
          </div>
        </div>

        <div class="flex-column mb-2">
          <div class="row">
            <div class="col d-flex justify-content-end">
              <h6 class="fw-bold">CLCST</h6>
            </div>
          </div>
          <div class="row">
            <div class="col d-flex justify-content-end">
              <small id="emailHelp" class="form-text text-muted">City of San Fernando, Pampanga</small>
            </div>
          </div>
        </div>

      </div>
    </nav>

    <div>
      <div v-if="path != '/'" class="d-flex align-content-center justify-content-end flex-wrap small-font">
        
        <span v-if="fetchingUserAccess"class="fw-regular">Identifying User...</span>
        <span v-else class="fw-regular">Welcome <span class="fw-bold">{{ userName }}</span></span>
      </div>
      <div v-if="path != '/'" class="container w-100 m-0 border mb-4 mt-2">
        <div class="row g-2">
          <div class="col-12 d-flex justify-content-between">
            <div v-if="fetchingUserAccess" class="d-flex gap-2 align-content-center flex-wrap">
              <nav class="nav gap-2 p-2">
                <div class="dropdown">
                  <button class="btn btn-sm" type="button" 
                    data-bs-toggle="dropdown" aria-expanded="false">
                    Loading Please Wait...
                  </button>
                </div>
              </nav>
            </div>
            <div v-else class="d-flex gap-2 align-content-center flex-wrap">
              <nav class="nav gap-2 p-2">
                <div class="dropdown">
                  <button class="btn btn-sm dropdown-toggle" type="button" id="dropdownMenuButton2"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    General
                  </button>
                  <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="dropdownMenuButton2">
                    <li><router-link to="/home" class="dropdown-item" href="#">Announcement</router-link></li>
                  </ul>
                </div>
                <div class="dropdown">
                  <button class="btn btn-sm dropdown-toggle" type="button" id="dropdownMenuButton2"
                    v-if="administrative > 0" data-bs-toggle="dropdown" aria-expanded="false">
                    Administrative
                  </button>
                  <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="dropdownMenuButton2">
                    <li v-for="(admin, index) in administrativeAccess">
                      <router-link :to="admin.link" @click="switchItem(admin.useracc_category,admin.useracc_modulecode) " class="dropdown-item">
                        {{ admin.description }}
                      </router-link>
                    </li>
                    <!-- <li><router-link to="/registrar-application" class="dropdown-item"
                        @click="switchItem(1, 1)">Registrar</router-link></li>
                    <li><router-link to="/registrar-library-books" class="dropdown-item"
                        @click="switchItem(1, 2)">Library</router-link></li>
                    <li><router-link to="/registrar-clinical-students" class="dropdown-item"
                        @click="switchItem(1, 3)">Clinic</router-link></li> -->
                  </ul>
                </div>
                <div class="dropdown">
                  <button class="btn btn-sm dropdown-toggle" type="button" id="dropdownMenuButton2"
                    v-if="transactions > 0" data-bs-toggle="dropdown" aria-expanded="false">
                    Transactions
                  </button>
                  <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="dropdownMenuButton2">
                    <li v-for="(transac, index) in transactionsAccess"><router-link :to="transac.link" @click="switchItem(transac.useracc_category,transac.useracc_modulecode) " class="dropdown-item"
                        href="#">{{ transac.description }}</router-link></li>
                    <!-- <li><router-link to="/accounting-items" @click="switchItem(2, 1)" class="dropdown-item"
                        href="#">Accounting</router-link></li>
                    <li><router-link to="/accounting-billing" @click="switchItem(2, 2)" class="dropdown-item"
                        href="#">Billing / Cashier</router-link></li> -->
                  </ul>
                </div>
                <div class="dropdown">
                  <button class="btn btn-sm dropdown-toggle" type="button" id="dropdownMenuButton2" v-if="academics > 0"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    Academics
                  </button>
                  <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="dropdownMenuButton2">
                    <li v-for="(academe, index) in academicsAccess"><router-link :to="academe.link" @click="switchItem(academe.useracc_category,academe.useracc_modulecode) " class="dropdown-item"
                      href="#">{{ academe.description }}</router-link></li>
                    <!-- <li><router-link to="/faculty-classes" @click="switchItem(3, 1)" class="dropdown-item"
                        href="#">Faculty</router-link></li>
                    <li><router-link to="" @click="switchItem(3, 1)" class="dropdown-item"
                        href="#">Students</router-link>
                    </li> -->
                  </ul>
                </div>
              </nav>
            </div>

            <div class="d-flex align-content-center flex-wrap gap-2">
              <div v-if="!fetchingUserAccess" class="d-flex align-content-center flex-wrap">
                <nav class="nav gap-2 mb-2 mt-2" v-if="accessSelected == 1 && itemSelected == 1">
                  <div class="dropdown">
                    <button class="btn btn-sm dropdown-toggle" type="button" id="dropdownMenuButton2"
                      data-bs-toggle="dropdown" aria-expanded="false">
                      Menu
                    </button>
                    <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="dropdownMenuButton2">
                      <li>
                        <router-link v-if="accessData[0].useracc_grant == 1" to="/registrar-application" class="dropdown-item" title="Student Application"
                          tabindex="-1">
                          <p class="m-2">Student Admission</p>
                        </router-link>
                      </li>

                      <li>
                        <router-link v-if="accessData[1].useracc_grant == 1" to="/registrar-enrollment" class="dropdown-item" title="Student Enrollment"
                          tabindex="-1">
                          <p class="m-2">Enrolled Students</p>
                        </router-link>
                      </li>

                      <li>
                        <router-link v-if="accessData[2].useracc_grant == 1" to="/registrar-request" class="dropdown-item" title="Student Request"
                          tabindex="-1">
                          <p class="m-2">Student Requests</p>
                        </router-link>
                      </li>

                      <li>
                        <router-link v-if="accessData[3].useracc_grant == 1" to="/registrar-launch" class="dropdown-item" title="Student Schedules"
                          tabindex="-1">
                          <p class="m-2">Semester Launch</p>
                        </router-link>
                      </li>

                      <li>
                        <router-link v-if="accessData[4].useracc_grant == 1" to="/registrar-personnel" class="dropdown-item" title="Personnel Management"
                          tabindex="-1">
                          <p class="m-2">Employee Management</p>
                        </router-link>
                      </li>

                      <li>
                        <router-link v-if="accessData[5].useracc_grant == 1" to="/registrar-settings" class="dropdown-item" title="Registrar Settings"
                          tabindex="-1">
                          <p class="m-2">Registrar Settings</p>
                        </router-link>
                      </li>
                    </ul>
                  </div>
                </nav>

                <nav class="nav gap-2 mb-2 mt-2" v-if="accessSelected == 1 && itemSelected == 2">
                  <div class="dropdown">
                    <button class="btn btn-sm dropdown-toggle" type="button" id="dropdownMenuButton2"
                      data-bs-toggle="dropdown" aria-expanded="false">
                      Menu
                    </button>
                    <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="dropdownMenuButton2">
                      <li>
                        <router-link v-if="accessData[6].useracc_grant == 1" to="/registrar-library-books" class="dropdown-item" title="Book Inventory"
                          tabindex="-1">
                          <p class="m-2">Book Supplies</p>
                        </router-link>
                      </li>
                      <li>
                        <router-link v-if="accessData[7].useracc_grant == 1" to="/registrar-library-books-borrowers" class="dropdown-item"
                          title="Book Borrowers" tabindex="-1">
                          <p class="m-2">Book Borrowers</p>
                        </router-link>
                      </li>
                      <li>
                        <router-link v-if="accessData[8].useracc_grant == 1" to="/registrar-library-books-ddc" class="dropdown-item" title="Book DDC"
                          tabindex="-1">
                          <p class="m-2">Book DDC</p>
                        </router-link>
                      </li>
                      <li>
                        <router-link to="/registrar-library-cards" class="dropdown-item" title="Library Cards"
                          tabindex="-1">
                          <p class="m-2">Library Cards</p>
                        </router-link>
                      </li>
                    </ul>
                  </div>
                </nav>

                <nav class="nav gap-2 mb-2 mt-2" v-if="accessSelected == 1 && itemSelected == 3">
                  <div class="dropdown">
                    <button class="btn btn-sm dropdown-toggle" type="button" id="dropdownMenuButton2"
                      data-bs-toggle="dropdown" aria-expanded="false">
                      Menu
                    </button>
                    <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="dropdownMenuButton2">
                      <li>
                        <router-link v-if="accessData[10].useracc_grant == 1" to="/registrar-clinical-students" class="dropdown-item" title="Clinical Records"
                          tabindex="-1">
                          <p class="m-2">Student Records</p>
                        </router-link>
                      </li>
                      <li>
                        <router-link v-if="accessData[11].useracc_grant == 1" to="/registrar-clinical-employee" class="dropdown-item" title="Clinical Records"
                          tabindex="-1">
                          <p class="m-2">Employee Records</p>
                        </router-link>
                      </li>
                      <li>
                        <router-link v-if="accessData[12].useracc_grant == 1" to="/registrar-clinical-medical-supplies" class="dropdown-item"
                          title="Clinical Records" tabindex="-1">
                          <p class="m-2">Medical Supplies</p>
                        </router-link>
                      </li>
                    </ul>
                  </div>
                </nav>

                <nav class="nav gap-2 mb-2 mt-2" v-if="accessSelected == 2 && itemSelected == 2">
                  <div class="dropdown">
                    <button class="btn btn-sm dropdown-toggle" type="button" id="dropdownMenuButton2"
                      data-bs-toggle="dropdown" aria-expanded="false">
                      Menu
                    </button>
                    <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="dropdownMenuButton2">
                      <li>
                        <router-link v-if="accessData[13].useracc_grant == 1" to="/accounting-billing" class="dropdown-item" title="Billing" tabindex="-1">
                          <p class="m-2">Tuition Payment</p>
                        </router-link>
                      </li>
                      <li>
                        <router-link v-if="accessData[14].useracc_grant == 1" to="/accounting-request" class="dropdown-item" title="request" tabindex="-1">
                          <p class="m-2">Request Payment</p>
                        </router-link>
                      </li>

                    </ul>
                  </div>
                </nav>

                <nav class="nav gap-2 mb-2 mt-2" v-if="accessSelected == 2 && itemSelected == 1">
                  <div class="dropdown">
                    <button class="btn btn-sm dropdown-toggle" type="button" id="dropdownMenuButton2"
                      data-bs-toggle="dropdown" aria-expanded="false">
                      Menu
                    </button>
                    <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="dropdownMenuButton2">
                      <li>
                        <router-link v-if="accessData[15].useracc_grant == 1" to="/accounting-items" class="dropdown-item" title="items" tabindex="-1">
                          <p class="m-2">Miscellaneuos / Items</p>
                        </router-link>
                      </li>
                      <!-- <li>
                    <router-link to="/accounting-package" class="dropdown-item" tabindex="-1">
                      <i class="m-2 fa-solid fa-paper-plane"></i>
                      <p class="m-2">Packages</p>
                    </router-link>
                  </li> -->
                    </ul>
                  </div>
                </nav>

                <nav class="nav gap-2 mb-2 mt-2" v-if="accessSelected == 3 && itemSelected == 1">
                  <div class="dropdown">
                    <button class="btn btn-sm dropdown-toggle" type="button" id="dropdownMenuButton2"
                      data-bs-toggle="dropdown" aria-expanded="false">
                      Menu
                    </button>
                    <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="dropdownMenuButton2">
                      <!-- <li>
                        <router-link to="/faculty-classes" class="dropdown-item" tabindex="-1">
                          <p class="m-2">Faculty Loadings</p>
                        </router-link>
                      </li>
                      <li>
                        <router-link to="/faculty-assignment" class="dropdown-item" tabindex="-1">
                          <p class="m-2">Faculty Assignments</p>
                        </router-link>
                      </li> -->
                      <li>
                        <router-link v-if="accessData[16].useracc_grant == 1" to="/faculty-classes" class="dropdown-item" tabindex="-1">
                          <p class="m-2">Faculty Loadings</p>
                        </router-link>
                      </li>
                      <li>
                        <router-link v-if="accessData[17].useracc_grant == 1" to="/faculty-student" class="dropdown-item" tabindex="-1">
                          <p class="m-2">Faculty Students</p>
                        </router-link>
                      </li>
                      <li>
                        <router-link v-if="accessData[18].useracc_grant == 1" to="/faculty-grading-sheet" class="dropdown-item" title="items" tabindex="-1">
                          <p class="m-2">Grading Sheet</p>
                        </router-link>
                      </li>
                    </ul>
                  </div>
                </nav>
              </div>
              <div class="d-flex align-content-center flex-wrap">
                <button type="button" @click="handleLogout()" class="btn btn-sm btn-danger p-2" title="items"
                  :disabled="isLoading ? true : false" tabindex="-1"><font-awesome-icon icon="fa-solid fa-power-off" />
                  Logout
                </button>
              </div>
            </div>

          </div>
        </div>
      </div>

      <div class="container w-100 m-0 p-0">
        <div class="row g-2">
          <div v-if="path != '/'" class="col-3">
            <div class="rounded-0 text-start d-flex flex-column nav-static">




            </div>
          </div>
          <div class="col-12">
            <!-- <loader v-if="isLoading"/> -->
            <RouterView @fetchUser="getUser"></RouterView>
          </div>
        </div>
      </div>
    </div>

  </div>

</template>