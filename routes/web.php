<?php

use App\Http\Controllers\FileUploadController;
use Illuminate\Support\Facades\Route;



Route::get('/', [FileUploadController::class, 'index'])->name('home');
Route::post('/', [FileUploadController::class, 'store'])->name('store');
