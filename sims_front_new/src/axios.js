import axios from "axios";


axios.defaults.withCredentials = true;
axios.defaults.withXSRFToken = true;
axios.defaults.baseURL = "http://sims.clcst.edu.local:8000";