import { createApp } from 'vue'
import router from './routes'
import './axios'
import './style.css'
import App from './App.vue'

/* import the bootstrap */
import 'bootstrap/dist/css/bootstrap.css'
import bootstrap  from 'bootstrap/dist/js/bootstrap.bundle.js'

/* import the fontawesome core */
import { library } from '@fortawesome/fontawesome-svg-core'

/* import font awesome icon component */
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
    faUserPlus

 } from '@fortawesome/free-solid-svg-icons'

/* add icons to the library */
library.add(faUserSecret,faPen,faTrash,faGear,faIdCardClip,faSearch,faPowerOff,faAdd,faPrint,faTag,faRotateLeft,faEye,faEarListen,faFolder,faPills,faKey,faUserPlus
)
// focus trapper
import { FocusTrap } from 'focus-trap-vue';

const app = createApp(App)
            .component('font-awesome-icon', FontAwesomeIcon)
            .component('FocusTrap', FocusTrap);
            
app.use(router,bootstrap)
app.mount('#app')