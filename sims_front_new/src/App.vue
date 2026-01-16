<script setup>
import axios from 'axios';
import { ref, onMounted, computed } from 'vue';
import { getUserID } from "./routes/user";
import { useRouter, useRoute } from 'vue-router';
import { getCommandUpdate } from './components/Fetchers.js'
import loader from './components/snippets/loaders/Loading1.vue';
const user = ref('')
const userID = ref('')
const router = useRouter();
const route = useRoute();
const path = computed(() => route.path)
const isLoading = ref(true)
const accessData = ref([])
const semInfo = ref('')
const enrollmentInfo = ref('')
const yrFromInfo = ref('')
const yrFromto = ref('')
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

const releaseMenu = (bool) =>{
  // console.log(bool)
  fetchingUserAccess.value = bool
  isLoading.value = bool
}

const getUser = (data) => {
  // route -> [category, modulecode, accesscode]
  const moduleMap = {
    // Administrative (Category 1)
    "/registrar-application":   ["1", "1", "1"],
    "/registrar-enrollment":    ["1", "1", "2"],
    "/registrar-request":       ["1", "1", "3"],
    "/registrar-launch":        ["1", "1", "4"],
    "/registrar-personnel":     ["1", "1", "5"],
    "/registrar-settings":      ["1", "1", "6"],
    "/registrar-alumni":        ["1", "1", "7"],

    "/registrar-library-books":           ["1", "2", "1"],
    "/registrar-library-books-ddc":       ["1", "2", "2"],
    "/registrar-library-books-borrowers": ["1", "2", "3"],
    "/registrar-library-cards":           ["1", "2", "4"],

    "/registrar-clinical-students":         ["1", "3", "1"],
    "/registrar-clinical-employee":         ["1", "3", "2"],
    "/registrar-clinical-medical-supplies": ["1", "3", "3"],
 
    // Transactions (Category 2)

    "/daily-collections-accounting":  ["2", "1", "1"],    // Accounting module
    "/accounting-items":              ["2", "1", "2"],       // Accounting module
    "/accounting-tuition":            ["2", "1", "3"],    // Accounting module
    "/accounting-subjects":            ["2", "1", "4"],    // Accounting module
    "/accounting-student-accounts":            ["2", "1", "5"],    // Accounting module

    "/daily-collections-cashier": ["2", "2", "1"],    // Billing module
    "/accounting-billing":        ["2", "2", "2"],    // Billing module
    "/accounting-request":        ["2", "2", "3"],    // Billing module

    // Academics (Category 3)
    "/faculty-classes":       ["3", "1", "1"],
    "/faculty-student":       ["3", "1", "2"],
    "/faculty-grading-sheet": ["3", "1", "3"],
  };

  const currentModule = moduleMap[path.value];

  if (currentModule && Array.isArray(data?.access)) {
    const [category, moduleCode, accessCode] = currentModule;

    const hasFullPermission = data.access.some(
      (item) =>
        String(item.useracc_category) === category &&
        String(item.useracc_modulecode) === moduleCode &&
        String(item.useracc_accesscode) === accessCode &&
        Number(item.useracc_viewing) === 1 &&
        Number(item.useracc_modifying) === 1
    );

    // console.log("Access array:", data.access);
    // console.log("Current module triple:", currentModule);
    // console.log("Has full permission:", hasFullPermission);

    if (!hasFullPermission) {
      Swal.fire({
        title: "Error",
        text: "Unauthorized Operations Detected",
        icon: "error",
        timer: 1000,
        showConfirmButton: false,
        willClose: () => {
          router.push("/home");
        },
      });
      return; // stop here on unauthorized
    }
  }

  // if no module mapping or user has permission → continue
  menuItemsHandler(data);
};


// --- helper to return link/description based on the actual object values ---
function getMetaForEntry(modulecode, accesscode, category) {
  modulecode = String(modulecode);
  accesscode = String(accesscode);
  category = String(category);

  // Administrative (category 1)
  if (category === "1") {
    if (modulecode === "1") return { link: "/registrar-application", description: "Registrar" };
    if (modulecode === "2") return { link: "/registrar-library-books", description: "Library" };
    if (modulecode === "3") return { link: "/registrar-clinical-students", description: "Clinic" };
  }

  // Transactions (category 2)
  if (category === "2") {
    if (modulecode === "1") return { link: "/daily-collections-accounting", description: "Accounting" };
    if (modulecode === "2") return { link: "/daily-collections-cashier", description: "Billing / Cashier" };
  }

  // Academics (category 3)
  if (category === "3") {
    if (modulecode === "1") return { link: "/faculty-classes", description: "Class" };
    // Expand as needed:
    // if (modulecode === "1" && accesscode === "3") return { link: "/faculty-student", description: "Grades" };
    // if (modulecode === "1" && accesscode === "4") return { link: "/faculty-grading-sheet", description: "Masterlist" };
  }

  return { link: "", description: "" };
}


// --- menuItemsHandler: single-pass building + dedupe --- 
const menuItemsHandler = (data) => {
  accessData.value = Array.isArray(data?.access) ? data.access : [];
  userName.value = `${data?.employee?.emp_firstname || ""} ${data?.employee?.emp_lastname || ""}`.trim();

  // buckets keyed by category number strings "1","2","3"
  const buckets = { "1": [], "2": [], "3": [] };

  (accessData.value).forEach((e) => {
    // refer to actual object values (coerce to number for checks)
    const grant = Number(e.useracc_grant);
    const viewing = Number(e.useracc_viewing);
    const modifying = Number(e.useracc_modifying);
    const category = String(e.useracc_category);

    // only include if granted and either viewing or modifying is enabled
    if (grant === 1 && (viewing === 1 || modifying === 1)) {
      const meta = getMetaForEntry(e.useracc_modulecode, e.useracc_accesscode, e.useracc_category);
      buckets[category] = buckets[category] || [];
      buckets[category].push({
        ...e,
        link: meta.link,
        description: meta.description,
      });
    }
  });

  // dedupe by modulecode (keep the first occurrence per modulecode)
  const dedupeByModule = (arr) =>
    [...new Map(arr.map((it) => [String(it.useracc_modulecode), it])).values()];

  administrative.value = (buckets["1"] || []).length;
  transactions.value = (buckets["2"] || []).length;
  academics.value = (buckets["3"] || []).length;

  administrativeAccess.value = dedupeByModule(buckets["1"] || []);
  transactionsAccess.value = dedupeByModule(buckets["2"] || []);
  academicsAccess.value = dedupeByModule(buckets["3"] || []);


  // next steps
  linker();

  // existing post-fetch work
  getCommandUpdate().then((result) => {
    semInfo.value = result[0].quar_code;
    yrFromInfo.value = result[1].sett_yearfrom;
    yrFromto.value = result[1].sett_yearto;
    enrollmentInfo.value = result[4].sett_status == 1 ? "Active" : "Inactive";

    // fetchingUserAccess.value = false;
    // isLoading.value = false;
  });
};

// --- linker: simplified, uses route groups and path.value --- 
const linker = () => {
  const groups = [
    {
      paths: ["/"],
      action: () => {
        router.push("/home");
        switchItem(0, 0);
      },
    },
    {
      paths: [
        "/registrar-application",
        "/registrar-enrollment",
        "/registrar-request",
        "/registrar-launch",
        "/registrar-personnel",
        "/registrar-settings",
        "/registrar-alumni",
      ],
      action: () => switchItem(1, 1),
    },
    {
      paths: [
        "/registrar-library-books",
        "/registrar-library-books-ddc",
        "/registrar-library-books-borrowers",
        "/registrar-library-cards",
      ],
      action: () => switchItem(1, 2),
    },
    {
      paths: [
        "/registrar-clinical-students",
        "/registrar-clinical-employee",
        "/registrar-clinical-medical-supplies",
      ],
      action: () => switchItem(1, 3),
    },
    {
      paths: [
        "/daily-collections-accounting",
        "/accounting-items", 
        "/accounting-tuition",
        "/accounting-subjects", 
        "/accounting-student-accounts", 
      ], 
      action: () => switchItem(2, 1),
    },
    {
      paths: [

        "/daily-collections-cashier",
        "/accounting-billing", 
        "/accounting-request"
      ],
      action: () => switchItem(2, 2),
    },
    {
      paths: [
        "/faculty-classes",
        "/faculty-student",
        "/faculty-grading-sheet",
      ],
      action: () => switchItem(3, 1),
    },
  ];

  for (const g of groups) {
    if (g.paths.includes(path.value)) {
      g.action();
      break;
    }
  }
};



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
  
  fetchingUserAccess.value = true
  isLoading.value = true

  // console.log(access)
  // console.log(item)

  // console.log(accessSelected.value)
  // console.log(itemSelected.value)


  if(accessSelected.value == access && itemSelected.value == item){
      fetchingUserAccess.value = false
      isLoading.value = false
  }

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

const checkPath = (data) =>{

  if(path.value != data){
    fetchingUserAccess.value = true
    isLoading.value = true
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
      <div v-if="path != '/'" class="d-flex align-content-center justify-content-between flex-wrap small-font">
        
        <div>
          <span v-if="fetchingUserAccess"class="fw-regular">Identifying Academic Calendar...</span>
          <div class="dropdown" v-else>
              <button class="btn btn-sm dropdown-toggle" type="button" id="notifications"
                data-bs-toggle="dropdown" aria-expanded="false">
                Academic Status
              </button>
              <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="notifications">
                <li class="dropdown-item">SY {{ yrFromto }}-{{ yrFromInfo }}</li>
                <li class="dropdown-item">{{ semInfo }} Semester</li>
                <li class="dropdown-item">Enrollment is {{ enrollmentInfo }}</li>
              </ul>
            </div>
        </div>
        <div>
          <span v-if="fetchingUserAccess"class="fw-regular">Identifying User...</span>
          <span v-else class="fw-regular">Welcome <span class="fw-bold text-uppercase">{{ userName }}</span></span>
        </div>
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
                    <li v-for="(transac, index) in transactionsAccess">
                      <router-link :to="transac.link" @click="switchItem(transac.useracc_category,transac.useracc_modulecode) " class="dropdown-item"
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
                    <li v-for="(academe, index) in academicsAccess">
                      <router-link :to="academe.link" @click="switchItem(academe.useracc_category,academe.useracc_modulecode) " class="dropdown-item"
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
                        <router-link @click="checkPath('/registrar-application')" v-if="accessData[0].useracc_grant == 1" to="/registrar-application" class="dropdown-item" title="Student Application"
                          tabindex="-1">
                          <p class="m-2">Student Admission</p>
                        </router-link>
                      </li>

                      <li>
                        <router-link @click="checkPath('/registrar-enrollment')" v-if="accessData[1].useracc_grant == 1" to="/registrar-enrollment" class="dropdown-item" title="Student Enrollment"
                          tabindex="-1">
                          <p class="m-2">Enrolled Students</p>
                        </router-link>
                      </li>

                      <li>
                        <router-link @click="checkPath('/registrar-request')" v-if="accessData[2].useracc_grant == 1" to="/registrar-request" class="dropdown-item" title="Student Request"
                          tabindex="-1">
                          <p class="m-2">Student Requests</p>
                        </router-link>
                      </li>

                      <li>
                        <router-link @click="checkPath('/registrar-launch')" v-if="accessData[3].useracc_grant == 1" to="/registrar-launch" class="dropdown-item" title="Student Schedules"
                          tabindex="-1">
                          <p class="m-2">Semester Launch</p>
                        </router-link>
                      </li>

                      <li>
                        <router-link @click="checkPath('/registrar-personnel')" v-if="accessData[4].useracc_grant == 1" to="/registrar-personnel" class="dropdown-item" title="Personnel Management"
                          tabindex="-1">
                          <p class="m-2">Employee Management</p>
                        </router-link>
                      </li>

                      <li>
                        <router-link @click="checkPath('/registrar-settings')" v-if="accessData[5].useracc_grant == 1" to="/registrar-settings" class="dropdown-item" title="Registrar Settings"
                          tabindex="-1">
                          <p class="m-2">Registrar Settings</p>
                        </router-link>
                      </li>

                      <li>
                        <router-link @click="checkPath('/registrar-alumni')" v-if="accessData[6].useracc_grant == 1" to="/registrar-alumni" class="dropdown-item" title="Alumni Association"
                          tabindex="-1">
                          <p class="m-2">Alumni Tracker</p>
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
                        <router-link @click="checkPath('/registrar-library-books')" v-if="accessData[7].useracc_grant == 1" to="/registrar-library-books" class="dropdown-item" title="Book Inventory"
                          tabindex="-1">
                          <p class="m-2">Book Supplies</p>
                        </router-link>
                      </li>
                      <li>
                        <router-link @click="checkPath('/registrar-library-books-borrowers')" v-if="accessData[8].useracc_grant == 1" to="/registrar-library-books-borrowers" class="dropdown-item"
                          title="Book Borrowers" tabindex="-1">
                          <p class="m-2">Book Borrowers</p>
                        </router-link>
                      </li>
                      <li>
                        <router-link @click="checkPath('/registrar-library-books-ddc')" v-if="accessData[9].useracc_grant == 1" to="/registrar-library-books-ddc" class="dropdown-item" title="Book DDC"
                          tabindex="-1">
                          <p class="m-2">Book DDC</p>
                        </router-link>
                      </li>
                      <li>
                        <router-link @click="checkPath('/registrar-library-cards')" v-if="accessData[10].useracc_grant == 1" to="/registrar-library-cards" class="dropdown-item" title="Library Cards"
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
                        <router-link @click="checkPath('/registrar-clinical-students')" v-if="accessData[11].useracc_grant == 1" to="/registrar-clinical-students" class="dropdown-item" title="Clinical Records"
                          tabindex="-1">
                          <p class="m-2">Student Records</p>
                        </router-link>
                      </li>
                      <li>
                        <router-link @click="checkPath('/registrar-clinical-employee')" v-if="accessData[12].useracc_grant == 1" to="/registrar-clinical-employee" class="dropdown-item" title="Clinical Records"
                          tabindex="-1">
                          <p class="m-2">Employee Records</p>
                        </router-link>
                      </li>
                      <li>
                        <router-link @click="checkPath('/registrar-clinical-medical-supplies')" v-if="accessData[13].useracc_grant == 1" to="/registrar-clinical-medical-supplies" class="dropdown-item"
                          title="Clinical Records" tabindex="-1">
                          <p class="m-2">Medical Supplies</p>
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
                        <router-link @click="checkPath('/daily-collections-accounting')" v-if="accessData[14].useracc_grant == 1" to="/daily-collections-accounting" class="dropdown-item" title="items" tabindex="-1">
                          <p class="m-2">Daily Collections</p>
                        </router-link> 
                      </li>
                      <li>
                        <router-link @click="checkPath('/accounting-items')" v-if="accessData[15].useracc_grant == 1" to="/accounting-items" class="dropdown-item" title="items" tabindex="-1">
                          <p class="m-2">Miscellaneuos / Items</p>
                        </router-link> 
                      </li>
                      <li>
                        <router-link @click="checkPath('/accounting-tuition')" v-if="accessData[16].useracc_grant == 1" to="/accounting-tuition" class="dropdown-item" title="items" tabindex="-1">
                          <p class="m-2">Tuition / Charges</p>
                        </router-link> 
                      </li>
                      <li>
                        <router-link @click="checkPath('/accounting-subjects')" v-if="accessData[17].useracc_grant == 1" to="/accounting-subjects" class="dropdown-item" title="items" tabindex="-1">
                          <p class="m-2">Subject Rates</p>
                        </router-link> 
                      </li>
                      <li>
                        <router-link @click="checkPath('/accounting-student-accounts')" v-if="accessData[18].useracc_grant == 1" to="/accounting-student-accounts" class="dropdown-item" title="items" tabindex="-1">
                          <p class="m-2">Student Accounts</p>
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

                <nav class="nav gap-2 mb-2 mt-2" v-if="accessSelected == 2 && itemSelected == 2">
                  <div class="dropdown">
                    <button class="btn btn-sm dropdown-toggle" type="button" id="dropdownMenuButton2"
                      data-bs-toggle="dropdown" aria-expanded="false">
                      Menu
                    </button>
                    <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="dropdownMenuButton2">
                      <li>
                        <router-link @click="checkPath('/daily-collections-cashier')" v-if="accessData[19].useracc_grant == 1" to="/daily-collections-cashier" class="dropdown-item" title="request" tabindex="-1">
                          <p class="m-2">Daily Collections</p>
                        </router-link>
                      </li>
                      <li>
                        <router-link @click="checkPath('/accounting-billing')" v-if="accessData[20].useracc_grant == 1" to="/accounting-billing" class="dropdown-item" title="Billing" tabindex="-1">
                          <p class="m-2">Tuition Payment</p>
                        </router-link>
                      </li>
                      <li>
                        <router-link @click="checkPath('/accounting-request')" v-if="accessData[21].useracc_grant == 1" to="/accounting-request" class="dropdown-item" title="request" tabindex="-1">
                          <p class="m-2">Request Payment</p>
                        </router-link>
                      </li>
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
                        <router-link @click="checkPath('/faculty-classes')" v-if="accessData[22].useracc_grant == 1" to="/faculty-classes" class="dropdown-item" tabindex="-1">
                          <p class="m-2">Faculty Loadings</p>
                        </router-link>
                      </li>
                      <li>
                        <router-link @click="checkPath('/faculty-student')" v-if="accessData[23].useracc_grant == 1" to="/faculty-student" class="dropdown-item" tabindex="-1">
                          <p class="m-2">Faculty Students</p>
                        </router-link>
                      </li>
                      <li>
                        <router-link @click="checkPath('/faculty-grading-sheet')" v-if="accessData[24].useracc_grant == 1" to="/faculty-grading-sheet" class="dropdown-item" title="items" tabindex="-1">
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
            <RouterView @fetchUser="getUser" @doneLoading="releaseMenu"></RouterView>
          </div>
        </div>
      </div>
    </div>

  </div>

</template>