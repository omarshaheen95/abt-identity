<?php

use App\Models\Level;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

require base_path('routes/general.php');

Route::get('/home', [\App\Http\Controllers\Inspection\SettingController::class, 'home'])->name('home');

//Set Local
Route::get('lang/{local}', [\App\Http\Controllers\Inspection\SettingController::class, 'lang'])->name('switch-language');
//profile
Route::get('profile', [\App\Http\Controllers\Inspection\InspectionController::class, 'viewUpdateProfile'])->name('edit-profile');
Route::post('update-profile', [\App\Http\Controllers\Inspection\InspectionController::class, 'updateProfile'])->name('update-profile');
Route::get('password', [\App\Http\Controllers\Inspection\InspectionController::class, 'viewUpdatePassword'])->name('edit-password');
Route::post('update-password', [\App\Http\Controllers\Inspection\InspectionController::class, 'updatePassword'])->name('update-password');


//Student
Route::resource('student', \App\Http\Controllers\Inspection\StudentController::class)->except(['destroy']);
Route::delete('delete-student', [\App\Http\Controllers\Inspection\StudentController::class, 'delete'])->name('student.delete-student');
Route::get('student-export', [\App\Http\Controllers\Inspection\StudentController::class, 'studentExport'])->name('student.student-export');
Route::get('students-cards-export', [\App\Http\Controllers\Inspection\StudentController::class, 'studentsCards'])->name('student.student-cards-export');
Route::get('student-marks-export', [\App\Http\Controllers\Inspection\StudentController::class, 'studentMarksExport'])->name('student.student-marks-export');
Route::get('student-login/{id}', [\App\Http\Controllers\Inspection\StudentController::class, 'studentLogin'])->name('student.student-login');


//School
Route::resource('school', \App\Http\Controllers\Inspection\SchoolController::class)->except(['destroy']);
Route::get('school_login/{id}', [\App\Http\Controllers\Inspection\SchoolController::class, 'schoolLogin'])->name('school-login');
Route::post('export-schools', [\App\Http\Controllers\Inspection\SchoolController::class,'schoolExport'])->name('export-schools');

Route::get('levelGrades', function (Request $request) {
    $request->validate(['id'=>'required']);
    $levels = Level::query()->with(['year'])->where('year_id', $request->get('id'))->get();
    $html = '' . !$request->get('multipleOptions', false) ? '<option></option>' : '';
    foreach ($levels as $level) {
        $name = $level->year->name . '- Grade ' . $level->grade . '-' . ($level->arab ? 'Arab' : 'Non-arabs');
        $html .= '<option value="' . $level->id . '">' . $name . '</option>';
    }
    return response()->json(['html' => $html]);
})->name('level.levelGrades');


// Attainment Report Routes
Route::get('pre-attainment-report', [\App\Http\Controllers\Inspection\ReportController::class, 'preAttainmentReport'])->name('report.pre-attainment');
Route::get('attainment-report', [\App\Http\Controllers\Inspection\ReportController::class, 'attainmentReport'])->name('report.attainment');

Route::get('get-sections', [\App\Http\Controllers\School\StudentController::class, 'getSectionsByYear'])->name('get-sections');
