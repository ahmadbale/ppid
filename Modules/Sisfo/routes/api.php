<?php

use Spatie\FlareClient\Api;
use Illuminate\Support\Facades\Route;
use Modules\Sisfo\App\Http\Controllers\Api\ApiAuthController;
use Modules\Sisfo\App\Http\Controllers\Api\Auth\AuthMenuController;
use Modules\Sisfo\App\Http\Controllers\Api\Public\ApiMenuController;
use Modules\Sisfo\App\Http\Controllers\Api\Public\ApiLhkpnController;
use Modules\Sisfo\App\Http\Controllers\Api\Public\ApiFooterController;
use Modules\Sisfo\App\Http\Controllers\Api\Public\PublicMenuController;
use Modules\Sisfo\App\Http\Controllers\Api\Public\ApiAksesCepatController;
use Modules\Sisfo\App\Http\Controllers\Api\Auth\BeritaPengumumanController;
use Modules\Sisfo\App\Http\Controllers\Api\Public\ApiMediaDinamisController;
use Modules\Sisfo\App\Http\Controllers\Api\Public\ApiPintasanLainnyaController;
use Modules\Sisfo\App\Http\Controllers\Api\Public\ApiBeritaLandingPageController;
use Modules\Sisfo\App\Http\Controllers\Api\Public\ApiBeritaController;
use Modules\Sisfo\App\Http\Controllers\Api\Public\ApiPengumumanLandingPageController;



/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Grup route untuk autentikasi
Route::prefix('auth')->group(function () {
    // Public routes (tidak perlu autentikasi)
    Route::post('login', [ApiAuthController::class, 'login']);
    // Route::post('register', [ApiAuthController::class, 'register']);

    // Protected routes (perlu autentikasi)
    Route::middleware('auth:api')->group(function () {
        Route::post('logout', [ApiAuthController::class, 'logout']);
        Route::get('user', [ApiAuthController::class, 'getData']);

        // Route::get('menus', [AuthMenuController::class, 'getAuthMenus']);
        Route::get('berita-pengumuman', [BeritaPengumumanController::class, 'getBeritaPengumuman']);
        Route::get('footerData', [ApiFooterController::class, 'getDataFooter']);


        // Route::get('getMenu', [AuthMenuController::class, 'getMenu']);
        // Route::get('getBeritaPengumuman', [BeritaPengumumanController::class, 'getBeritaPengumuman']);
        // Route::get('getDataFooter', [ApiFooterController::class, 'getDataFooter']);
        Route::post('refresh-token', [ApiAuthController::class, 'refreshToken']);
    });
});

// Route publik
Route::prefix('public')->group(function () {
    Route::post('logout', [ApiAuthController::class, 'logout']);
    Route::get('user', [ApiAuthController::class, 'getData']);
    // Route::get('menus', [AuthMenuController::class, 'getAuthMenus']);
    Route::get('berita-pengumuman', [BeritaPengumumanController::class, 'getBeritaPengumuman']);
    Route::get('getDataFooter', [ApiFooterController::class, 'getDataFooter']);
    Route::post('refresh-token', [ApiAuthController::class, 'refreshToken']);
    Route::get('getDataLhkpn', [ApiLhkpnController::class, 'getDataLhkpn']);
    Route::get('getDataPintasanLainnya', [ApiPintasanLainnyaController::class, 'getDataPintasanLainnya']);
    Route::get('getDataAksesCepat', [ApiAksesCepatController::class, 'getDataAksesCepat']);
    Route::get('getDataMenu', [ApiMenuController::class, 'getDataMenu']);
    Route::get('getDataPengumumanLandingPage', [ApiPengumumanLandingPageController::class, 'getDataPengumumanLandingPage']);
    Route::get('getDataBeritaLandingPage', [ApiBeritaLandingPageController::class, 'getDataBeritaLandingPage']);
    Route::get('getDataHeroSection', [ApiMediaDinamisController::class, 'getDataHeroSection']);
    Route::get('getDataDokumentasi', [ApiMediaDinamisController::class, 'getDataDokumentasi']);
    Route::get('getDataMediaInformasiPublik', [ApiMediaDinamisController::class, 'getDataMediaInformasiPublik']);
    Route::get('getDataBerita', [ApiBeritaController::class, 'getDataBerita']);
});
