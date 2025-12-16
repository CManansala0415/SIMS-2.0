import { createRouter, createMemoryHistory, createWebHashHistory } from "vue-router";
import Home from '../components/Home.vue';
import ApplicationView from '../components/Registrar/ApplicationView.vue';
import EnrollmentView from '../components/Registrar/EnrollmentView.vue';
// import ApplicationForm from '../components/Registrar/ApplicationForm.vue';
import SemesterLaunchView from '../components/Registrar/SemesterLaunchView.vue';
import EmployeeView from '../components/Registrar/EmployeeView.vue';
import RegistrarSettings from '../components/Registrar/RegistrarSettingsView.vue';
import BillingRequestView from '../components/Accounting/BillingRequestView.vue';
import BillingTuitionView from '../components/Accounting/BillingTuitionView.vue';
import StudentRequestView from '../components/Registrar/StudentRequestView.vue';
import AccountingItemsView from '../components/Accounting/AccountingItemsView.vue';
import AccountingSubjectsView from '../components/Accounting/AccountingSubjectsView.vue';
import AccountingTuitionView from '../components/Accounting/AccountingTuitionView.vue';
import CashierDailyCollections from '../components/Accounting/CashierDailyCollections.vue';
import AccountingDailyCollections from '../components/Accounting/AccountingDailyCollections.vue';
// import AccountingPackage from '../components/Accounting/AccountingPackage.vue';
import ClinicalStudentRecordsView from '../components/Clinic/ClinicalStudentRecordsView.vue';
import ClinicalMedicalSupplies from '../components/Clinic/ClinicalMedicalSuppliesView.vue';
import ClinicalEmployeeRecordsView  from '../components/Clinic/ClinicalEmployeeRecordsView.vue';
import LibraryBooksView from '../components/Library/LibraryBooksView.vue';
import LibraryBooksDdcView from '../components/Library/LibraryBooksDdcView.vue';
import LibraryBooksBorrowersView from '../components/Library/LibraryBooksBorrowersView.vue';
import LibraryCardsView from '../components/Library/LibraryCardsView.vue';
import FacultyClassView from '../components/Faculty/FacultyClassView.vue';
import FacultyGradesView from '../components/Faculty/FacultyGradesView.vue';
import FacultyMasterlist from '../components/Faculty/FacultyMasterlist.vue';
import AlumniTracker from '../components/Registrar/AlumniTracker.vue';

const routes = [
     
    {
        path: '/', name:'Login', component: () => import("../components/Login.vue")
    },
    {
        path: '/home', name:'home', component: Home
    },
    {
        path: '/registrar-application', name:'registrar-application', component: ApplicationView
    },
    {
        path: '/registrar-enrollment', name:'registrar-enrollment', component: EnrollmentView
    },
    // {
    //     path: '/registrar-applicant/new-applicant', name:'new-applicant', component: ApplicationForm
    // },
    {
        path: '/registrar-launch', name:'/registrar-launch', component: SemesterLaunchView
    },
    {
        path: '/registrar-personnel', name:'/registrar-personnel', component: EmployeeView
    },
    {
        path: '/registrar-settings', name:'/registrar-settings', component: RegistrarSettings
    },
    {
        path: '/registrar-alumni', name:'/registrar-alumni', component: AlumniTracker
    },
    {
        path: '/registrar-library-books', name:'/registrar-library-books', component: LibraryBooksView
    },
    {
        path: '/registrar-library-books-ddc', name:'/registrar-library-books-ddc', component: LibraryBooksDdcView
    },
    {
        path: '/registrar-library-books-borrowers', name:'/registrar-library-books-borrowers', component: LibraryBooksBorrowersView
    },
    {
        path: '/registrar-library-cards', name:'/registrar-library-cards', component: LibraryCardsView
    },
    {
        path: '/registrar-clinical-students', name:'/registrar-clinical-students', component: ClinicalStudentRecordsView
    },
    {
        path: '/registrar-clinical-employee', name:'/registrar-clinical-employee', component: ClinicalEmployeeRecordsView 
    },
    {
        path: '/registrar-clinical-medical-supplies', name:'/registrar-clinical-medical-supplies', component: ClinicalMedicalSupplies
    },
    {
        path: '/accounting-billing', name:'/accounting-billing', component: BillingTuitionView
    },
    {
        path: '/registrar-request', name:'/registrar-request', component: StudentRequestView
    },
    { 
        path: '/accounting-request', name:'/accounting-request', component: BillingRequestView
    },
    {
        path: '/accounting-items', name:'/accounting-items', component: AccountingItemsView
    },
    {
        path: '/accounting-subjects', name:'/accounting-subjects', component: AccountingSubjectsView
    },
    {
        path: '/accounting-tuition', name:'/accounting-tuition', component: AccountingTuitionView
    },
    {
        path: '/daily-collections-accounting', name:'/daily-collections-accounting', component: AccountingDailyCollections
    },
    {
        path: '/daily-collections-cashier', name:'/daily-collections-cashier', component: CashierDailyCollections
    },
    {
        path: '/faculty-classes', name:'/faculty-classes', component: FacultyClassView
    },
    {
        path: '/faculty-grading-sheet', name:'/faculty-grading-sheet', component: FacultyGradesView
    },
    {
        path: '/faculty-student', name:'/faculty-student', component: FacultyMasterlist
    },

    
    
];


const router = createRouter({
    history:createWebHashHistory(),
    routes
})

export default router;
