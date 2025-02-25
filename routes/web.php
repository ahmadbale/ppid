<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InformasiPublikController;
use App\Http\Controllers\PermohonanController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\FooterController;
use App\Http\Controllers\EFormController;

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

// Route::get('/', function () {
//     return view('tryit');
// });
Route::get('/', [HomeController::class, 'index']);

Route::get('/landing_page', [HomeController::class, 'index']);

Route::get('/footer', [FooterController::class, 'index']);
 

Route::get('/e-form_informasi', function () {
    return view('e-form_informasi');
})->name('e-form');

Route::get('/e-form_keberatan', function () {
    return view('e-form_keberatan');
});

Route::get('/e-form_wbs', function () {
    return view('e-form_wbs');
});

Route::get('/login', function () {
    return view('login');
}) ->name('login');

Route::get('/register', function () {
    return view('register');
}) ->name('register');

Route::get('/eform', function () {
    return view('timeline');
})->name('eform');

Route::prefix('informasi-publik')->group(function () {
    Route::get('/setiap-saat', [InformasiPublikController::class, 'setiapSaat'])->name('informasi-publik.setiap-saat');
    Route::get('/berkala', [InformasiPublikController::class, 'berkala'])->name('informasi-publik.berkala');
    Route::get('/serta-merta', [InformasiPublikController::class, 'sertaMerta'])->name('informasi-publik.serta-merta');
});

Route::get('/permohonan/lacak', [PermohonanController::class, 'lacak'])->name('permohonan.lacak');
