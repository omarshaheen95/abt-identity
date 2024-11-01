<?php

use Illuminate\Support\Facades\Route;

Route::get('/',[\App\Http\Controllers\Student\HomeController::class,'index'])->name('home');
Route::get('/home',[\App\Http\Controllers\Student\HomeController::class,'index'])->name('home-main');
Route::get('/term/{id}',[\App\Http\Controllers\Student\TermController::class,'termStart'])->name('term');
Route::post('/term_save/{id}',[\App\Http\Controllers\Student\TermController::class,'termSave'])->name('term-save');
Route::post('/term_leave',[\App\Http\Controllers\Student\TermController::class,'termLeave'])->name('term-leave');

