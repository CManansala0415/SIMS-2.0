// import axios from "axios";


// axios.defaults.withCredentials = true;
// axios.defaults.withXSRFToken = true;
// axios.defaults.baseURL = "http://sims.clcst.edu.local:8000";


import axios from "axios";
import router from "./routes";
import { nextTick } from 'vue'

axios.defaults.withCredentials = true;
axios.defaults.withXSRFToken = true;
axios.defaults.baseURL = "http://sims.clcst.edu.local:8000";

// ✅ shared lock (IMPORTANT)
let authErrorPromise = null;

axios.interceptors.response.use(
  (response) => response,
  (error) => {
    const status = error.response?.status;
    const currentRoute = router.currentRoute?.value?.name;
    const isLoginPage = currentRoute === "login";

    if ((status === 401 || status === 403) && !isLoginPage) {

      // // 🚨 if already handling error → reuse same flow
      // if (!authErrorPromise) {
      //   authErrorPromise = Swal.fire({
      //     title: "Session Expired",
      //     text: "Please login again",
      //     icon: "warning",
      //     allowOutsideClick: false,
      //     allowEscapeKey: false,
      //   }).then(() => {
      //       nextTick(() => {
      //           router.replace({ name: "login" }).then(() => {
      //           location.reload() // 🔄 always force fresh load of /login
      //           })
      //       })
      //   });
      // }

      if (!authErrorPromise) {
        authErrorPromise = Swal.fire({
          title: "Session Expired",
          text: "Please login again",
          icon: "warning",
          allowOutsideClick: false,
          allowEscapeKey: false,
        }).then(() => {

          // ✅ CLOSE ALL MODALS GLOBALLY
          document.querySelectorAll('.modal.show').forEach(el => {
            el.classList.remove('show');
            el.style.display = 'none';
          });
          document.body.classList.remove('modal-open');
          document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());

          nextTick(() => {
            router.replace({ name: "login" }).then(() => {
              // location.reload();
            });
          });
        });
      }

      return authErrorPromise;
    }

    return Promise.reject(error);
  }
);

export default axios;