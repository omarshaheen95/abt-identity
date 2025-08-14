<?php
//Attainment reports routes
Route::get('pre-attainment-report', [\App\Http\Controllers\General\ReportController::class, 'preAttainmentReport'])
    ->name('report.pre-attainment-report');
Route::get('attainment-report', [\App\Http\Controllers\General\ReportController::class, 'attainmentReport'])
    ->name('report.attainment-report');


//Progress reports routes
Route::get('pre-progress-report', [\App\Http\Controllers\General\ReportController::class, 'preProgressReport'])
    ->name('report.pre-progress-report');
Route::get('progress-report', [\App\Http\Controllers\General\ReportController::class, 'progressReport'])
    ->name('report.progress-report');


//Year to Year reports routes
Route::get('pre-year-to-year-report', [\App\Http\Controllers\General\ReportController::class, 'preYearToYearReport'])
    ->name('report.pre-year-to-year-report');
Route::get('year-to-year-report', [\App\Http\Controllers\General\ReportController::class, 'yearToYearReport'])
    ->name('report.year-to-year-report');
Route::get('excel-year-to-year-report', [\App\Http\Controllers\General\ReportController::class, 'excelYearToYearReport'])
    ->name('report.excel-year-to-year-report');


//Student Mark reports routes
Route::get('pre-student-mark-report', [\App\Http\Controllers\General\ReportController::class, 'preStudentMarkReport'])
    ->name('report.pre-student-mark-report');
Route::get('student-mark-report', [\App\Http\Controllers\General\ReportController::class, 'studentMarkReport'])
    ->name('report.student-mark-report');

Route::get('student-report/{id}/report', [\App\Http\Controllers\General\ReportController::class, 'studentReport'])
    ->name('report.student-report');

Route::get('student-report/{id}/card', [\App\Http\Controllers\General\ReportController::class, 'studentReportCard'])
    ->name('report.student-report-card');
