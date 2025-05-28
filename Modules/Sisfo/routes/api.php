<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SystemAuthController;
use Modules\Sisfo\App\Http\Controllers\Api\ApiAuthController;
use Modules\Sisfo\App\Http\Controllers\Api\EForm\ApiWBSController;
use Modules\Sisfo\App\Http\Controllers\Api\Public\ApiMenuController;
use Modules\Sisfo\App\Http\Controllers\Api\Public\ApiLhkpnController;
use Modules\Sisfo\App\Http\Controllers\Api\Public\ApiBeritaController;
use Modules\Sisfo\App\Http\Controllers\Api\Public\ApiFooterController;
use Modules\Sisfo\App\Http\Controllers\Api\Public\ApiRegulasiController;
use Modules\Sisfo\App\Http\Controllers\Api\Public\ApiTimelineController;
use Modules\Sisfo\App\Http\Controllers\Api\Public\ApiAksesCepatController;
use Modules\Sisfo\App\Http\Controllers\Api\Public\ApiMediaDinamisController;
use Modules\Sisfo\App\Http\Controllers\Api\HakAkses\ApiSetHakAksesController;
use Modules\Sisfo\App\Http\Controllers\Api\Public\ApiIpDinamisKontenController;
use Modules\Sisfo\App\Http\Controllers\Api\Public\ApiPintasanLainnyaController;
use Modules\Sisfo\App\Http\Controllers\Api\ManagePengguna\ApiHakAksesController;
use Modules\Sisfo\App\Http\Controllers\Api\Public\ApiBeritaPengumumanController;
use Modules\Sisfo\App\Http\Controllers\Api\Public\ApiBeritaLandingPageController;
use Modules\Sisfo\App\Http\Controllers\Api\EForm\ApiPengaduanMasyarakatController;
use Modules\Sisfo\App\Http\Controllers\Api\EForm\ApiPermohonanInformasiController;
use Modules\Sisfo\App\Http\Controllers\Api\EForm\ApiPermohonanPerawatanController;
use Modules\Sisfo\App\Http\Controllers\Api\EForm\ApiPernyataanKeberatanController;
use Modules\Sisfo\App\Http\Controllers\Api\ManagePengguna\ApiUserController;
use Modules\Sisfo\App\Http\Controllers\Api\Public\ApiKetentuanPelaporanController;
use Modules\Sisfo\App\Http\Controllers\Api\Public\ApiDashboardStatisticsController;
use Modules\Sisfo\App\Http\Controllers\Api\MenuManagement\ApiWebMenuGlobalController;
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

// Grup route untuk autentikasi pengguna
Route::prefix('auth')->group(function () {
    // Public routes (tidak perlu autentikasi)
    Route::post('login', [ApiAuthController::class, 'login']);
    Route::post('register', [ApiAuthController::class, 'register']);

    // Protected routes (perlu autentikasi)
    Route::middleware('jwt.user')->group(function () {
        Route::post('logout', [ApiAuthController::class, 'logout']);
        Route::get('user', [ApiAuthController::class, 'getData']);
        // Route::post('permohonan-informasi/create', [ApiPermohonanInformasiController::class, 'createPermohonanInformasi']);
        // Route::post('refresh-token', [ApiAuthController::class, 'refreshToken']);
        // Route::post('permohonan-informasi/create', [ApiPermohonanInformasiController::class, 'createPermohonanInformasi']);
        // api set_hak_akses
        Route::prefix('set-hak-akses')->group(function () {
            Route::get('/', [ApiSetHakAksesController::class, 'index']);
            Route::get('/getHakAksesData/{param1}/{param2?}', [ApiSetHakAksesController::class, 'editData']);
            Route::post('/update', [ApiSetHakAksesController::class, 'updateData']);
        });
        // api m_hak_akses
        Route::prefix('hak-akses')->group(function () {
            Route::get('/', [ApiHakAksesController::class, 'index']);
            Route::post('/create', [ApiHakAksesController::class, 'createData']);
            Route::put('/update/{id}', [ApiHakAksesController::class, 'updateData']);
            Route::delete('/delete/{id}', [ApiHakAksesController::class, 'deleteData']);
            Route::get('/detail/{id}', [ApiHakAksesController::class, 'detailData']);
        });
        // Route Api EForm
        // permohonan informasi
        Route::prefix('permohonan-informasi')->group(function () {
            Route::post('/create', [ApiPermohonanInformasiController::class, 'createData']);
        });
        // pernyataan keberatan
        Route::prefix('pernyataan-keberatan')->group(function () {
            Route::post('/create', [ApiPernyataanKeberatanController::class, 'createData']);
        });
        // pengaduan Masyarakat
        Route::prefix('pengaduan-masyarakat')->group(function () {
            Route::post('/create', [ApiPengaduanMasyarakatController::class, 'createData']);
        });
        // permohonan perawatan
        Route::prefix('permohonan-perawatan')->group(function () {
            Route::post('/create', [ApiPermohonanPerawatanController::class, 'createData']);
        });
        // WBS
        Route::prefix('wbs')->group(function () {
            Route::post('/create', [ApiWBSController::class, 'createData']);
        });
        // Route Api WebMenuGlobal
        Route::prefix('management-menu-global')->group(function () {
            Route::get('/', [ApiWebMenuGlobalController::class, 'index']);
            Route::post('/create', [ApiWebMenuGlobalController::class, 'createData']);
            Route::put('/update/{id}', [ApiWebMenuGlobalController::class, 'updateData']);
            Route::delete('/delete/{id}', [ApiWebMenuGlobalController::class, 'deleteData']);
            Route::get('/detail/{id}', [ApiWebMenuGlobalController::class, 'detailData']);
            Route::get('/getMenuUrl', [ApiWebMenuGlobalController::class, 'getMenuUrl']);
        });
        // Routr Api UserController
        Route::prefix('user')->group(function () {
            Route::get('/', [ApiUserController::class, 'index']);
            Route::post('/create', [ApiUserController::class, 'createData']);
            Route::put('/update/{id}', [ApiUserController::class, 'updateData']);
            Route::delete('/delete/{id}', [ApiUserController::class, 'deleteData']);
            Route::get('/detail/{id}', [ApiUserController::class, 'detailData']);
        });
    });
});
// 
Route::get('system/get-token', [SystemAuthController::class, 'getToken']);
Route::post('system/refresh-token', [SystemAuthController::class, 'refreshToken']);
// Route api konten untuk halaman web frontend
Route::prefix('public')->middleware('jwt.system')->group(function () {
    Route::get('getBeritaPengumuman', [ApiBeritaPengumumanController::class, 'getBeritaPengumuman']);
    Route::get('getDataFooter', [ApiFooterController::class, 'getDataFooter']);
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
    Route::get('getDetailBeritaById/{slug}/{berita_id}', [ApiBeritaController::class, 'getDetailBeritaById']);
    Route::get('getDashboardStatistics', [ApiDashboardStatisticsController::class, 'getDashboardStatistics']);
    Route::get('getDataTimeline', [ApiTimelineController::class, 'getDataTimeline']);
    Route::get('getDataKetentuanPelaporan', [ApiKetentuanPelaporanController::class, 'getDataKetentuanPelaporan']);
    Route::get('getDataRegulasi', [ApiRegulasiController::class, 'getDataRegulasi']);
    Route::get('getDataIPDaftarInformasi', [ApiIpDinamisKontenController::class, 'getDataIPDaftarInformasi']);
});