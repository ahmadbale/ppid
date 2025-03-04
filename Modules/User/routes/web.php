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

Route::get('/eform', [TimelineController::class, 'index'])-> name('eform');

Route::get('/Lsidebar', function () {
    return view('user::layouts.left_sidebar');
});
Route::get('/Rsidebar', function () {
    return view('user::layouts.right_sidebar');
});

Route::get('/landing_page', [HomeController::class, 'index']);


Route::get('/footer', [FooterController::class, 'index']);

Route::prefix('e-form')->group(function () {
    Route::get('/informasi-publik', function () {
        return view('user::e-form.informasi-publik');})->name('e-form.informasi-publik');
    Route::get('/keberatan', function () {
        return view('user::e-form.keberatan');})->name('e-form.keberatan');

    Route::get('/wbs', function () {
        return view('user::e-form.wbs');})->name('e-form.wbs');
});


Route::get('/e-form_informasi', function () {
    return view('user::e-form_informasi');
})->name('e-form');

Route::get('/e-form_keberatan', function () {
    return view('user::e-form_keberatan');
});

Route::get('/e-form_wbs', function () {
    return view('user::e-form_wbs');
});

Route::get('/login', function () {
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
