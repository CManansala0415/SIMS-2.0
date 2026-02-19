import { createApp, nextTick } from 'vue'
import router from './routes'
import './axios'
import './style.css'
import App from './App.vue'

// ✅ always initialize global access
window.__userAccess = [];

/* import the bootstrap */
import 'bootstrap/dist/css/bootstrap.css'
import bootstrap from 'bootstrap/dist/js/bootstrap.bundle.js'

/* import the fontawesome core */
import { library } from '@fortawesome/fontawesome-svg-core'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

/* import specific icons */
import { 
  faUserSecret, 
  faPen,
  faTrash,
  faGear,
  faIdCardClip,
  faSearch,
  faPowerOff,
  faAdd,
  faPrint,
  faTag,
  faRotateLeft,
  faEye,
  faEarListen,
  faFolder,
  faPills,
  faKey,
  faUserPlus,
  faIdCard,
  faGraduationCap,
  faCashRegister,
  faFloppyDisk,
  faArrowsRotate,
  faWrench,
  faHand, 
  faBook,
  faDownload,
  faBan,
  faUpload,
  faFileExcel,
  faThumbsUp,
  faThumbsDown,
} from '@fortawesome/free-solid-svg-icons'

/* add icons to the library */
library.add(
  faUserSecret, faPen, faTrash, faGear, faIdCardClip, faSearch,
  faPowerOff, faAdd, faPrint, faTag, faRotateLeft, faEye, faEarListen,
  faFolder, faPills, faKey, faUserPlus, faIdCard, faGraduationCap, faCashRegister, faFloppyDisk, faArrowsRotate, faWrench,
  faHand, faBook, faDownload, faBan, faUpload, faFileExcel, faThumbsUp, faThumbsDown
)

// focus trapper
import { FocusTrap } from 'focus-trap-vue';

const app = createApp(App)
  .component('font-awesome-icon', FontAwesomeIcon)
  .component('FocusTrap', FocusTrap)

// ✅ Global error handler using CDN Swal
app.config.errorHandler = (err, instance, info) => {
  console.error("Vue Global Error:", err, info)

  if (typeof Swal !== "undefined") {
    Swal.fire({
      title: "Error",
      text: "Unauthorized operation detected",
      icon: "error"
    }).then(() => {
      nextTick(() => {
        router.replace({ name: "home" }).then(() => {
          location.reload() // 🔄 always force fresh load of /home
        })
      })
    })
  } else {
    alert("Unauthorized operation detected")
    nextTick(() => {
      router.replace({ name: "home" }).then(() => {
        location.reload() // 🔄 fallback reload
      })
    })
  }
}

app.use(router, bootstrap)
app.mount('#app')
