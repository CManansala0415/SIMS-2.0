<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegistrarController;
use App\Http\Controllers\DefaultsController;
use App\Http\Controllers\TransactionsController;
use App\Http\Controllers\ClinicController;
use App\Http\Controllers\LibraryController;
use App\Http\Controllers\FacultyController;
use App\Http\Controllers\FileManagement;
use App\Http\Controllers\CommandController;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});
Route::middleware(['auth:sanctum'])->get('/get-user-access/{userid}', [DefaultsController::class,'getUserAccess']);


Route::middleware(['auth:sanctum'])->get('/get-applicant/{limit}/{offset}/{fname}/{mname}/{lname}', [RegistrarController::class,'getApplicant']);
Route::middleware(['auth:sanctum'])->get('/get-gender', [DefaultsController::class,'getGender']);
Route::middleware(['auth:sanctum'])->get('/get-nationality', [DefaultsController::class,'getNationality']);
Route::middleware(['auth:sanctum'])->get('/get-civilstatus', [DefaultsController::class,'getCivilStatus']);
Route::middleware(['auth:sanctum'])->get('/get-gradelvl', [DefaultsController::class,'getGradelvl']);
Route::middleware(['auth:sanctum'])->get('/get-program', [DefaultsController::class,'getProgram']);
Route::middleware(['auth:sanctum'])->get('/get-program-list/{id?}', [DefaultsController::class,'getProgramList']);
Route::middleware(['auth:sanctum'])->get('/get-quarter', [DefaultsController::class,'getQuarter']);
Route::middleware(['auth:sanctum'])->get('/get-degree/{type?}', [DefaultsController::class,'getDegree']);
Route::middleware(['auth:sanctum'])->get('/get-semester', [DefaultsController::class,'getSemester']);
Route::middleware(['auth:sanctum'])->get('/get-section', [DefaultsController::class,'getSection']);
Route::middleware(['auth:sanctum'])->get('/get-country', [DefaultsController::class,'getCountry']);
Route::middleware(['auth:sanctum'])->get('/get-region', [DefaultsController::class,'getRegion']);
Route::middleware(['auth:sanctum'])->get('/get-province', [DefaultsController::class,'getProvince']);
Route::middleware(['auth:sanctum'])->get('/get-city', [DefaultsController::class,'getCity']);
Route::middleware(['auth:sanctum'])->get('/get-barangay', [DefaultsController::class,'getBarangay']);
Route::middleware(['auth:sanctum'])->get('/get-demograph', [DefaultsController::class,'getDemograph']);
Route::middleware(['auth:sanctum'])->get('/get-academicdefaults', [DefaultsController::class,'getAcademicDefaults']);
Route::middleware(['auth:sanctum'])->get('/get-tagged-subject', [DefaultsController::class,'getTaggedSubject']);
Route::middleware(['auth:sanctum'])->get('/get-subject', [DefaultsController::class,'getSubject']);
Route::middleware(['auth:sanctum'])->post('/add-program', [DefaultsController::class,'addProgram']);
Route::middleware(['auth:sanctum'])->post('/add-section', [DefaultsController::class,'addSection']);
Route::middleware(['auth:sanctum'])->post('/add-subject', [DefaultsController::class,'addSubject']);
Route::middleware(['auth:sanctum'])->post('/add-curriculum', [DefaultsController::class,'addCurriculum']);
Route::middleware(['auth:sanctum'])->get('/get-curriculum-sett', [DefaultsController::class,'getCurriculumSett']);
Route::middleware(['auth:sanctum'])->post('/add-curriculum-tagging', [DefaultsController::class,'addCurriculumTagging']);
Route::middleware(['auth:sanctum'])->get('/get-specialization', [DefaultsController::class,'getSpecialization']);



Route::middleware(['auth:sanctum'])->get('/get-person/{id}', [RegistrarController::class,'getPerson']);
Route::middleware(['auth:sanctum'])->get('/get-family/{id}', [RegistrarController::class,'getFamily']);
Route::middleware(['auth:sanctum'])->get('/get-attainment/{id}', [RegistrarController::class,'getAttainment']);
Route::middleware(['auth:sanctum'])->get('/get-award/{id}', [RegistrarController::class,'getAward']);
Route::middleware(['auth:sanctum'])->post('/add-applicant/{type}', [RegistrarController::class,'addApplicant']);
Route::middleware(['auth:sanctum'])->post('/update-applicant', [RegistrarController::class,'updateApplicant']);
Route::middleware(['auth:sanctum'])->post('/delete-applicant', [RegistrarController::class,'deleteApplicant']);
Route::middleware(['auth:sanctum'])->post('/update-perdetails/{type}', [RegistrarController::class,'updatePersonDetails']);
Route::middleware(['auth:sanctum'])->post('/delete-perdetails/{type}', [RegistrarController::class,'deleteFamAwrAtt']);
Route::middleware(['auth:sanctum'])->get('/get-enrollment/{personid}', [RegistrarController::class,'getEnrollment']);
Route::middleware(['auth:sanctum'])->post('/delete-enrollment', [RegistrarController::class,'deleteEnrollment']);
Route::middleware(['auth:sanctum'])->post('/enroll-applicant', [RegistrarController::class,'enrollApplicant']);


Route::middleware(['auth:sanctum'])->get('/get-student/{limit}/{offset}/{search}', [RegistrarController::class,'getStudent']);
Route::middleware(['auth:sanctum'])->get('/get-student-filtering/{limit}/{offset}/{fname}/{mname}/{lname}/{program}/{gradelvl}/{course}/{mode}', [RegistrarController::class,'getStudentFiltering']);
Route::middleware(['auth:sanctum'])->post('/upload-profile', [FileManagement::class,'uploadProfile']);
Route::middleware(['auth:sanctum'])->post('/upload-link', [FileManagement::class,'uploadLink']);
Route::middleware(['auth:sanctum'])->get('/get-student-by-course/{limit}/{offset}/{search}', [RegistrarController::class,'getStudentByCourse']);
Route::middleware(['auth:sanctum'])->get('/get-curriculum/{limit}/{offset}/{search}', [RegistrarController::class,'getCurriculum']);
Route::middleware(['auth:sanctum'])->get('/get-curriculum-student/{prog}/{type}', [RegistrarController::class,'getCurriculumStudent']);
Route::middleware(['auth:sanctum'])->get('/get-curriculum-subject/{curr}/{sem}/{gradelvl}', [RegistrarController::class,'getCurriculumSubject']);
Route::middleware(['auth:sanctum'])->post('/add-milestone', [RegistrarController::class,'addMilestone']);
Route::middleware(['auth:sanctum'])->get('/get-milestone/{curr}', [RegistrarController::class,'getMilestone']);
Route::middleware(['auth:sanctum'])->post('/update-enrollment', [RegistrarController::class,'updateEnrollment']);
Route::middleware(['auth:sanctum'])->post('/update-milestone', [RegistrarController::class,'updateMilestone']);


Route::middleware(['auth:sanctum'])->get('/get-building', [DefaultsController::class,'getBuilding']);
Route::middleware(['auth:sanctum'])->get('/get-classroom', [DefaultsController::class,'getClassroom']);
Route::middleware(['auth:sanctum'])->get('/get-launch/{limit}/{offset}/{search}', [RegistrarController::class,'getLaunch']);
Route::middleware(['auth:sanctum'])->get('/get-launch-checker', [RegistrarController::class,'getLaunchChecker']);

Route::middleware(['auth:sanctum'])->post('/add-launch', [RegistrarController::class,'addLaunch']);
Route::middleware(['auth:sanctum'])->post('/add-schedule', [RegistrarController::class,'addSchedule']);
Route::middleware(['auth:sanctum'])->get('/get-schedule/{launchid}', [RegistrarController::class,'getSchedule']);
Route::middleware(['auth:sanctum'])->get('/get-occupancy/{bird}/{classrid}', [RegistrarController::class,'getOccupancy']);
Route::middleware(['auth:sanctum'])->get('/get-occupancy-others/{othersid}', [RegistrarController::class,'getOccupancyOthers']);

Route::middleware(['auth:sanctum'])->get('/get-employee/{limit}/{offset}/{fname}/{mname}/{lname}', [RegistrarController::class,'getEmployee']);
Route::middleware(['auth:sanctum'])->post('/update-employee', [RegistrarController::class,'updateEmployee']);
Route::middleware(['auth:sanctum'])->post('/add-employee', [RegistrarController::class,'addEmployee']);
Route::middleware(['auth:sanctum'])->post('/add-employee-load', [RegistrarController::class,'addEmployeeLoad']);
Route::middleware(['auth:sanctum'])->get('/get-employee-load/{empid}', [RegistrarController::class,'getEmployeeLoad']);
Route::middleware(['auth:sanctum'])->post('/delete-employee-load', [RegistrarController::class,'deleteEmployeeLoad']);
Route::middleware(['auth:sanctum'])->post('/delete-employee', [RegistrarController::class,'deleteEmployee']);
Route::middleware(['auth:sanctum'])->get('/get-student-master/{limit}/{offset}/{search}', [RegistrarController::class,'getStudentMaster']);
Route::middleware(['auth:sanctum'])->get('/get-scheduled-subjects/{sched_id}', [RegistrarController::class,'getScheduledSubjects']);

Route::middleware(['auth:sanctum'])->get('/get-scheduled-faculty', [RegistrarController::class,'getScheduledFaculty']);
Route::middleware(['auth:sanctum'])->post('/add-scheduled-faculty', [RegistrarController::class,'addScheduledFaculty']);
Route::middleware(['auth:sanctum'])->post('/delete-scheduled-faculty', [RegistrarController::class,'deleteScheduledFaculty']);
Route::middleware(['auth:sanctum'])->get('/get-faculty-availability', [RegistrarController::class,'getFacultyAvailability']);
Route::middleware(['auth:sanctum'])->get('/get-student-identification/{id}', [RegistrarController::class,'getStudentIdentification']);
Route::middleware(['auth:sanctum'])->post('/add-student-identification', [RegistrarController::class,'addStudentIdentification']);


Route::middleware(['auth:sanctum'])->get('/get-accounts-details', [TransactionsController::class,'getAccountsDetails']);
Route::middleware(['auth:sanctum'])->get('/get-accounts-price', [TransactionsController::class,'getPriceDetails']);
Route::middleware(['auth:sanctum'])->get('/get-accounts-fee/{limit}/{offset}/{search}', [TransactionsController::class,'getFeeDetails']);
Route::middleware(['auth:sanctum'])->get('/get-accounts-request/{limit}/{offset}/{search}', [TransactionsController::class,'getRequestDetails']);
Route::middleware(['auth:sanctum'])->post('/add-requested-item', [TransactionsController::class,'addItemRequest']);
Route::middleware(['auth:sanctum'])->post('/delete-requested-item', [TransactionsController::class,'deleteItemRequest']);
Route::middleware(['auth:sanctum'])->post('/add-payment', [TransactionsController::class,'addPayment']);
Route::middleware(['auth:sanctum'])->get('/get-accounts-payment/{id}/{billtype}', [TransactionsController::class,'getPaymentDetails']);
Route::middleware(['auth:sanctum'])->get('/get-accounts-payment-all/{billtype}/{datefrom}/{dateto}/{paystat}', [TransactionsController::class,'getAllPayments']);
Route::middleware(['auth:sanctum'])->post('/add-accounting-item', [TransactionsController::class,'addAccountingItem']);


Route::middleware(['auth:sanctum'])->post('/add-clinical-students', [ClinicController::class,'addStudentClinicalRecord']);
Route::middleware(['auth:sanctum'])->post('/add-clinical-employee', [ClinicController::class,'addEmployeeClinicalRecord']);
Route::middleware(['auth:sanctum'])->get('/get-clinical-students/{mode}/{id}', [ClinicController::class,'getStudentClinicalRecord']);
Route::middleware(['auth:sanctum'])->get('/get-clinical-employees/{mode}/{id}', [ClinicController::class,'getEmployeeClinicalRecords']);
Route::middleware(['auth:sanctum'])->get('/get-clinical-medical-supplies/{limit}/{offset}/{search}', [ClinicController::class,'getMedicalSupplies']);
Route::middleware(['auth:sanctum'])->post('/add-clinical-medical-supply', [ClinicController::class,'updateMedicalSupply']);
Route::middleware(['auth:sanctum'])->post('/add-clinical-medical-supply-dispense-student', [ClinicController::class,'dispenseMedicalSupplyStudent']);
Route::middleware(['auth:sanctum'])->get('/get-clinical-dispensed-supplies-student/{id}', [ClinicController::class,'getDispensedMedicalSupplyStudent']);
Route::middleware(['auth:sanctum'])->post('/add-clinical-medical-supply-dispense-employee', [ClinicController::class,'dispenseMedicalSupplyEmployee']);
Route::middleware(['auth:sanctum'])->get('/get-clinical-dispensed-supplies-employee/{id}', [ClinicController::class,'getDispensedMedicalSupplyEmployee']);
Route::middleware(['auth:sanctum'])->post('/add-clinical-examination-ishihara/{type}/{id}/{personid}', [ClinicController::class,'addClinicIshihara']);
Route::middleware(['auth:sanctum'])->post('/add-clinical-examination-hearing/{type}/{id}/{personid}', [ClinicController::class,'addClinicHearing']);
Route::middleware(['auth:sanctum'])->get('/get-clinical-examination-ishihara/{id}/{headerid}', [ClinicController::class,'getMedicalIshihara']);
Route::middleware(['auth:sanctum'])->get('/get-clinical-examination-hearing/{id}/{headerid}', [ClinicController::class,'getMedicalHearing']);
Route::middleware(['auth:sanctum'])->get('/get-clinical-examination-header/{id}/{type}', [ClinicController::class,'getMedicalHeader']);
Route::middleware(['auth:sanctum'])->get('/get-clinical-medical-files/{id}/{folder}', [ClinicController::class,'getMedicalFiles']);
Route::middleware(['auth:sanctum'])->get('/get-clinical-medical-files-headers/{id}', [ClinicController::class,'getMedicalFileHeaders']);
Route::middleware(['auth:sanctum'])->post('/add-clinical-medical-files-headers', [ClinicController::class,'addMedicalFileHeader']);
Route::middleware(['auth:sanctum'])->post('/add-clinical-medical-files-image/{location}', [FileManagement::class,'addMedicalFilesImage']);
Route::middleware(['auth:sanctum'])->post('/add-clinical-medical-files-image-upload', [FileManagement::class,'uploadMedicalFileLink']);


Route::middleware(['auth:sanctum'])->get('/get-library-books-accession/{limit}/{offset}/{search}', [LibraryController::class,'getBooksAccession']);
Route::middleware(['auth:sanctum'])->get('/get-library-books-ddc/{limit}/{offset}/{search}', [LibraryController::class,'getBooksDdc']);
Route::middleware(['auth:sanctum'])->post('/add-library-books-ddc', [LibraryController::class,'addBooksDdc']);
Route::middleware(['auth:sanctum'])->post('/add-library-books-information', [LibraryController::class,'addBookInformation']);
Route::middleware(['auth:sanctum'])->post('/add-library-books-borrowed', [LibraryController::class,'addBorrowedBooks']);
Route::middleware(['auth:sanctum'])->get('/get-library-books-borrowed/{limit}/{offset}/{search}', [LibraryController::class,'getBorrowedBooks']);
Route::middleware(['auth:sanctum'])->post('/update-library-books-borrowed', [LibraryController::class,'updateBorrowedBooks']);
Route::middleware(['auth:sanctum'])->get('/get-library-card-issue/{personid}/{enrid}/{active}', [LibraryController::class,'getLibraryCardIssue']);
Route::middleware(['auth:sanctum'])->get('/get-library-books-borrowed-by/{cardid}/{personid}/{enrid}', [LibraryController::class,'getBorrowedBooksBy']);
Route::middleware(['auth:sanctum'])->post('/deactivate-library-card', [LibraryController::class,'deactivateLibraryCard']);
Route::middleware(['auth:sanctum'])->post('/add-library-card', [LibraryController::class,'addLibraryCard']);

 
Route::middleware(['auth:sanctum'])->get('/get-faculty-class/{empid}', [FacultyController::class,'getFacultyClass']);
Route::middleware(['auth:sanctum'])->get('/get-faculty-assignment/{empid}', [FacultyController::class,'getFacultyAssignment']);
Route::middleware(['auth:sanctum'])->get('/get-faculty-student/{section}/{gradelvl}/{course}', [FacultyController::class,'getFacultyStudent']);
Route::middleware(['auth:sanctum'])->get('/get-faculty-student-milestone/{section}/{gradelvl}/{course}', [FacultyController::class,'getFacultyStudentMilestone']);
Route::middleware(['auth:sanctum'])->get('/get-faculty-grading-sheet-header/{lnid}', [FacultyController::class,'getGradingSheetHeader']);
Route::middleware(['auth:sanctum'])->get('/get-faculty-grading-sheet-grade/{hid}/{subjid}', [FacultyController::class,'getGradingSheetGrade']);
Route::middleware(['auth:sanctum'])->post('/add-faculty-grading-header/{type}', [FacultyController::class,'addGradingSheetHeader']);
Route::middleware(['auth:sanctum'])->post('/add-faculty-grading-grade', [FacultyController::class,'addGradingSheetGrade']);


// major fetchers
Route::middleware(['auth:sanctum'])->get('/get-enlistment', [DefaultsController::class,'getEnlistment']);

//command center
Route::middleware(['auth:sanctum'])->get('/command-drop-applicant', [CommandController::class,'dropApplicant']);
Route::middleware(['auth:sanctum'])->post('/add-command-update', [CommandController::class,'setCommandUpdate']);
Route::middleware(['auth:sanctum'])->get('/get-command-update', [CommandController::class,'getCommandUpdate']);
Route::middleware(['auth:sanctum'])->get('/get-command-update-curriculum/{prog}/{grad}/{course}', [CommandController::class,'getCommandUpdateCurriculum']);
Route::middleware(['auth:sanctum'])->get('/get-command-users/{userid}', [CommandController::class,'getCommandUsers']);
Route::middleware(['auth:sanctum'])->post('/update-command-user', [CommandController::class,'updateCommandUsers']);
Route::middleware(['auth:sanctum'])->post('/add-command-user', [CommandController::class,'addCommandUsers']);
Route::middleware(['auth:sanctum'])->post('/add-command-access', [CommandController::class,'saveCommandAccess']);
Route::middleware(['auth:sanctum'])->get('/get-command-access/{userid}', [CommandController::class,'getCommandAccess']);
Route::middleware(['auth:sanctum'])->post('/tag-employee-account', [CommandController::class,'tagEmployeeAccount']);
Route::middleware(['auth:sanctum'])->get('/get-employee-account/{userid}', [CommandController::class,'getEmployeeAccount']);






