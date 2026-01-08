<?php

use App\Http\Controllers\KhmerOCRController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [KhmerOCRController::class, 'show'])->name('ocr.show');
Route::post('/', [KhmerOCRController::class, 'recognize'])->name('ocr.recognize');
