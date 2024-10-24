<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    $students = \App\Models\Student::query()->count();
    $schools = \App\Models\School::query()->count();
    $terms = \App\Models\StudentTerm::query()->count();
    return view('welcome', compact( 'students', 'schools', 'terms'));
});

Route::group(['prefix' => 'manager'], function () {
    Route::get('/login', [\App\Http\Controllers\ManagerAuth\LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [\App\Http\Controllers\ManagerAuth\LoginController::class, 'login']);
    Route::post('/logout', [\App\Http\Controllers\ManagerAuth\LoginController::class, 'logout'])->name('logout');
    Route::post('/password/email', [\App\Http\Controllers\ManagerAuth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.request');
    Route::post('/password/reset', [\App\Http\Controllers\ManagerAuth\ResetPasswordController::class, 'reset'])->name('password.email');
    Route::get('/password/reset', [\App\Http\Controllers\ManagerAuth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.reset');
    Route::get('/password/reset/{token}', [\App\Http\Controllers\ManagerAuth\ResetPasswordController::class, 'showResetForm']);
});

Route::group(['prefix' => 'school'], function () {

    Route::get('/login', [\App\Http\Controllers\SchoolAuth\LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [\App\Http\Controllers\SchoolAuth\LoginController::class, 'login']);
    Route::post('/logout', [\App\Http\Controllers\SchoolAuth\LoginController::class, 'logout'])->name('school.logout');
    Route::post('/password/email', [\App\Http\Controllers\SchoolAuth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.request');
    Route::post('/password/reset', [\App\Http\Controllers\SchoolAuth\ResetPasswordController::class, 'reset'])->name('password.email');
    Route::get('/password/reset', [\App\Http\Controllers\SchoolAuth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.reset');
    Route::get('/password/reset/{token}', [\App\Http\Controllers\SchoolAuth\ResetPasswordController::class, 'showResetForm']);
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::group(['prefix' => 'inspection'], function () {

    Route::get('/login', [\App\Http\Controllers\InspectionAuth\LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [\App\Http\Controllers\InspectionAuth\LoginController::class, 'login']);
    Route::post('/logout', [\App\Http\Controllers\InspectionAuth\LoginController::class, 'logout'])->name('logout');
    Route::post('/password/email', [\App\Http\Controllers\InspectionAuth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.request');
    Route::post('/password/reset', [\App\Http\Controllers\InspectionAuth\ResetPasswordController::class, 'reset'])->name('password.email');
    Route::get('/password/reset', [\App\Http\Controllers\InspectionAuth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.reset');
    Route::get('/password/reset/{token}', [\App\Http\Controllers\InspectionAuth\ResetPasswordController::class, 'showResetForm']);
});

Route::group(['prefix' => 'student'], function () {
    Route::get('/login', [\App\Http\Controllers\StudentAuth\LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [\App\Http\Controllers\StudentAuth\LoginController::class, 'studentLogin']);
    Route::post('/logout', [\App\Http\Controllers\StudentAuth\LoginController::class, 'logout'])->name('logout');
});

Route::get('cache', function(){
    \Illuminate\Support\Facades\Artisan::call('cache:clear');
    \Illuminate\Support\Facades\Artisan::call('config:cache');
});
Route::get('migrate', function () {
    \Illuminate\Support\Facades\Artisan::call('migrate');
});
Route::get('migrate-fresh', function () {
    \Illuminate\Support\Facades\Artisan::call('migrate:refresh');
});
Route::get('rollback', function () {
    \Illuminate\Support\Facades\Artisan::call('migrate:rollback');
});
Route::get('seed', function () {
    \Illuminate\Support\Facades\Artisan::call('db:seed');
});
Route::get('view', function () {
    \Illuminate\Support\Facades\Artisan::call('view:clear');
});

Route::get('lang/{locale}', function ($locale) {
    session(['lang' => $locale]);
    if (Auth::guard('student')->check()){
        Auth::guard('student')->user()->update(['lang' => $locale,]);
    }
    if (Auth::guard('manager')->check()){
        Auth::guard('manager')->user()->update(['lang' => $locale,]);
    }
    if (Auth::guard('inspection')->check()){
        Auth::guard('inspection')->user()->update(['lang' => $locale,]);
    }
    if (Auth::guard('school')->check()){
        Auth::guard('school')->user()->update(['lang' => $locale,]);
    }

    app()->setLocale($locale);
    return back();
})->name('switch-language');
