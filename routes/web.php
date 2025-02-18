<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('tryit');
});

Route::get('/landing_page', function () {
    return view('landing_page');
});

Route::get('/footer', function () {
    return view('layouts.footer');
});

Route::get('/e-form', function () {
    return view('e-form_informasi');
});

Route::get('/login', function () {
    return view('login');
}) ->name('login');

Route::get('/register', function () {
    return view('register');
}) ->name('register');



Route::prefix('informasi-publik')->group(function () {
    Route::get('/setiap-saat', [InformasiPublikController::class, 'setiapSaat'])->name('informasi-publik.setiap-saat');
    Route::get('/berkala', [InformasiPublikController::class, 'berkala'])->name('informasi-publik.berkala');
    Route::get('/serta-merta', [InformasiPublikController::class, 'sertaMerta'])->name('informasi-publik.serta-merta');
});

Route::get('/permohonan/lacak', [PermohonanController::class, 'lacak'])->name('permohonan.lacak');
