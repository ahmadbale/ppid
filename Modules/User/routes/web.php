<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InformasiPublikController;
use App\Http\Controllers\PermohonanController;
use Modules\User\App\Http\Controllers\HomeController;
use App\Http\Controllers\FooterController;
use App\Http\Controllers\EFormController;
use Modules\User\App\Http\Controllers\TimelineController;

use Modules\User\App\Http\Controllers\UserController;

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

// Route::group([], function () {
//     Route::resource('user', UserController::class)->names('user');
// });


Route::get('/', [HomeController::class, 'index'])-> name('beranda');

Route::get('/landing_page', [HomeController::class, 'index']);


Route::get('/footer', [FooterController::class, 'index']);

Route::prefix('form')->group(function () {
    Route::get('/informasi-publik', function () {
        return view('user::e-form.informasi-publik');})->name('form-informasi-publik');
    Route::get('/keberatan', function () {
        return view('user::e-form.keberatan');})->name('form-keberatan');
    Route::get('/wbs', function () {
        return view('user::e-form.wbs');})->name('form-wbs');
    Route::get('/pengaduan-masyarakat', function () {
        return view('user::e-form.aduan-masyarakat');})->name('form-aduanmasyarakat');
});

Route::get('/permohonan-informasi', [TimelineController::class, 'permohonan_informasi'])-> name('permohonan_informasi');
Route::get('/pernyataan-keberatan', [TimelineController::class, 'pernyataan_keberatan'])-> name('pernyataan_keberatan');
Route::get('/whistle-blowing-system', [TimelineController::class, 'wbs'])-> name('wbs');
Route::get('/pengaduan-masyarakat', [TimelineController::class, 'pengaduan_masyarakat'])-> name('pengaduan_masyarakat');

Route::get('/dashboard', function () {
    return view('user::dashboard');})->name('dashboard');
Route::get('/content-dinamis', function () {
    return view('user::content');})->name('content');


Route::get('/e-form_keberatan', function () {
    return view('user::e-form_keberatan');
});

Route::get('/e-form_wbs', function () {
    return view('user::e-form_wbs');
});

Route::get('/login-ppid', function () {
    return view('user::login');
}) ->name('login');

Route::get('/register', function () {
    return view('user::register');
}) ->name('register');


Route::prefix('informasi-publik')->group(function () {
    Route::get('/setiap-saat', [InformasiPublikController::class, 'setiapSaat'])->name('informasi-publik.setiap-saat');
    Route::get('/berkala', [InformasiPublikController::class, 'berkala'])->name('informasi-publik.berkala');
    Route::get('/serta-merta', [InformasiPublikController::class, 'sertaMerta'])->name('informasi-publik.serta-merta');
});

Route::get('/permohonan/lacak', [PermohonanController::class, 'lacak'])->name('permohonan.lacak');
