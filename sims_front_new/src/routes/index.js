import { createRouter, createWebHistory } from "vue-router";

// Registrar
import Home from '../components/Home.vue';
import ApplicationView from '../components/Registrar/ApplicationView.vue';
import EnrollmentView from '../components/Registrar/EnrollmentView.vue';
import SemesterLaunchView from '../components/Registrar/SemesterLaunchView.vue';
import EmployeeView from '../components/Registrar/EmployeeView.vue';
import RegistrarSettings from '../components/Registrar/RegistrarSettingsView.vue';
import AlumniTracker from '../components/Registrar/AlumniTracker.vue';
import StudentRequestView from '../components/Registrar/StudentRequestView.vue';

// Accounting
import BillingRequestView from '../components/Accounting/BillingRequestView.vue';
import BillingTuitionView from '../components/Accounting/BillingTuitionView.vue';
import AccountingItemsView from '../components/Accounting/AccountingItemsView.vue';
import AccountingSubjectsView from '../components/Accounting/AccountingSubjectsView.vue';
import AccountingStudentAccountsView from '../components/Accounting/AccountingStudentAccountsView.vue';
import AccountingTuitionView from '../components/Accounting/AccountingTuitionView.vue';
import CashierDailyCollections from '../components/Accounting/CashierDailyCollections.vue';
import AccountingDailyCollections from '../components/Accounting/AccountingDailyCollections.vue';

// Clinic
import ClinicalStudentRecordsView from '../components/Clinic/ClinicalStudentRecordsView.vue';
import ClinicalMedicalSupplies from '../components/Clinic/ClinicalMedicalSuppliesView.vue';
import ClinicalEmployeeRecordsView  from '../components/Clinic/ClinicalEmployeeRecordsView.vue';

// Library
import LibraryBooksView from '../components/Library/LibraryBooksView.vue';
import LibraryBooksDdcView from '../components/Library/LibraryBooksDdcView.vue';
import LibraryBooksBorrowersView from '../components/Library/LibraryBooksBorrowersView.vue';
import LibraryCardsView from '../components/Library/LibraryCardsView.vue';

// Faculty
import FacultyClassView from '../components/Faculty/FacultyClassView.vue';
import FacultyGradesView from '../components/Faculty/FacultyGradesView.vue';
import FacultyMasterlist from '../components/Faculty/FacultyMasterlist.vue';

// Auth
const Login = () => import("../components/Login.vue");

const routes = [
    { path: '/', name: 'login', component: Login },
    { path: '/home', name: 'home', component: Home },

    // Registrar
    { path: '/registrar-application', name: 'registrar-application', component: ApplicationView },
    { path: '/registrar-enrollment', name: 'registrar-enrollment', component: EnrollmentView },
    { path: '/registrar-launch', name: 'registrar-launch', component: SemesterLaunchView },
    { path: '/registrar-personnel', name: 'registrar-personnel', component: EmployeeView },
    { path: '/registrar-settings', name: 'registrar-settings', component: RegistrarSettings },
    { path: '/registrar-alumni', name: 'registrar-alumni', component: AlumniTracker },
    { path: '/registrar-request', name: 'registrar-request', component: StudentRequestView },

    // Accounting
    { path: '/accounting-billing', name: 'accounting-billing', component: BillingTuitionView },
    { path: '/accounting-request', name: 'accounting-request', component: BillingRequestView },
    { path: '/accounting-items', name: 'accounting-items', component: AccountingItemsView },
    { path: '/accounting-subjects', name: 'accounting-subjects', component: AccountingSubjectsView },
    { path: '/accounting-student-accounts', name: 'accounting-student-accounts', component: AccountingStudentAccountsView },
    { path: '/accounting-tuition', name: 'accounting-tuition', component: AccountingTuitionView },
    { path: '/daily-collections-accounting', name: 'daily-collections-accounting', component: AccountingDailyCollections },
    { path: '/daily-collections-cashier', name: 'daily-collections-cashier', component: CashierDailyCollections },

    // Clinic
    { path: '/registrar-clinical-students', name: 'registrar-clinical-students', component: ClinicalStudentRecordsView },
    { path: '/registrar-clinical-employee', name: 'registrar-clinical-employee', component: ClinicalEmployeeRecordsView },
    { path: '/registrar-clinical-medical-supplies', name: 'registrar-clinical-medical-supplies', component: ClinicalMedicalSupplies },

    // Library
    { path: '/registrar-library-books', name: 'registrar-library-books', component: LibraryBooksView },
    { path: '/registrar-library-books-ddc', name: 'registrar-library-books-ddc', component: LibraryBooksDdcView },
    { path: '/registrar-library-books-borrowers', name: 'registrar-library-books-borrowers', component: LibraryBooksBorrowersView },
    { path: '/registrar-library-cards', name: 'registrar-library-cards', component: LibraryCardsView },

    // Faculty
    { path: '/faculty-classes', name: 'faculty-classes', component: FacultyClassView },
    { path: '/faculty-grading-sheet', name: 'faculty-grading-sheet', component: FacultyGradesView },
    { path: '/faculty-student', name: 'faculty-student', component: FacultyMasterlist },
];

const router = createRouter({
    history: createWebHistory(), // <-- no more #
    routes
});

export default router;