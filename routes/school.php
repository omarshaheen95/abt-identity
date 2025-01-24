<?php

use Illuminate\Support\Facades\Route;

Route::get('/home', [\App\Http\Controllers\School\SettingController::class,'home'])->name('home');
//Statistics Route
Route::post('statistics/student_login_data', [\App\Http\Controllers\School\SettingController::class,'studentLoginData'])->name('statistics.student_login_data');
Route::post('statistics/assessments_data', [\App\Http\Controllers\School\SettingController::class,'assessmentsData'])->name('statistics.assessments_data');

//Set Local
Route::get('lang/{local}', [\App\Http\Controllers\School\SettingController::class, 'lang'])->name('switch-language');

//profile
Route::get('profile', [\App\Http\Controllers\School\SchoolController::class, 'viewUpdateProfile'])->name('edit-profile');
Route::post('update-profile', [\App\Http\Controllers\School\SchoolController::class, 'updateProfile'])->name('update-profile');
Route::get('password', [\App\Http\Controllers\School\SchoolController::class, 'viewUpdatePassword'])->name('edit-password');
Route::post('update-password', [\App\Http\Controllers\School\SchoolController::class, 'updatePassword'])->name('update-password');


//Student
Route::resource('student', \App\Http\Controllers\School\StudentController::class)->except(['destroy']);
Route::delete('delete-student', [\App\Http\Controllers\School\StudentController::class, 'delete'])->name('student.delete-student');
Route::post('student-export', [\App\Http\Controllers\School\StudentController::class, 'studentExport'])->name('student.student-export');
Route::get('students-cards-export', [\App\Http\Controllers\School\StudentController::class, 'studentsCards'])->name('student.student-cards-export');
Route::post('student-marks-export', [\App\Http\Controllers\School\StudentController::class, 'studentMarksExport'])->name('student.student-marks-export');
Route::get('student-login/{id}', [\App\Http\Controllers\School\StudentController::class, 'studentLogin'])->name('student.student-login');
Route::get('student/{id}/card', [\App\Http\Controllers\School\StudentController::class, 'studentCard'])->name('student-card');
Route::post('students-cards-by-section', [\App\Http\Controllers\School\StudentController::class, 'studentCardBySections'])->name('student.students-cards-by-section');
Route::get('student-report/{id}', [\App\Http\Controllers\School\StudentController::class, 'studentReport'])->name('student.student-report');
Route::post('/pdfReports', [\App\Http\Controllers\School\StudentController::class,'pdfReports'])->name('reports.pdfReports');



Route::get('levelGrades', [\App\Http\Controllers\School\SettingController::class, 'getLevelsByYear'])->name('getLevelsByYear');

//Terms Scheduling
Route::resource('scheduling',\App\Http\Controllers\School\SchedulingController::class);

Route::get('students_terms/{type}', [\App\Http\Controllers\School\TermController::class, 'index'])->name('students-terms');

Route::get('students_not_submitted_terms',  [\App\Http\Controllers\School\TermController::class, 'studentsNotSubmittedTerms'])->name('term.students-not-submitted-terms');
Route::post('students_not_submitted_terms/export', [\App\Http\Controllers\School\TermController::class, 'studentsNotSubmittedTermsExport'])->name('term.students-not-submitted-terms-export');

//Marking Requests
Route::resource('marking_requests', \App\Http\Controllers\School\MarkingRequestController::class)->except(['destroy']);
Route::delete('marking_requests', [\App\Http\Controllers\School\MarkingRequestController::class, 'destroy'])->name('marking_requests.destroy');
Route::get('completed_terms_total', [\App\Http\Controllers\School\MarkingRequestController::class, 'getCompletedTermsTotal'])->name('completed-terms-total');

Route::get('get-sections', [\App\Http\Controllers\School\StudentController::class, 'getSectionsByYear'])->name('get-sections');

Route::get('pre-attainment-report', [\App\Http\Controllers\School\ReportController::class, 'preAttainmentReport'])->name('report.pre-attainment');
Route::get('attainment-report', [\App\Http\Controllers\School\ReportController::class, 'attainmentReport'])->name('report.attainment');



