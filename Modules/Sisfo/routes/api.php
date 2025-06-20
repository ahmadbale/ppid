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
use Modules\Sisfo\App\Http\Controllers\Api\Public\ApiLIDinamisController;
use Modules\Sisfo\App\Http\Controllers\Api\Public\ApiAksesCepatController;
use Modules\Sisfo\App\Http\Controllers\Api\ManagePengguna\ApiUserController;
use Modules\Sisfo\App\Http\Controllers\Api\Public\ApiMediaDinamisController;
use Modules\Sisfo\App\Http\Controllers\Api\HakAkses\ApiSetHakAksesController;
use Modules\Sisfo\App\Http\Controllers\Api\ManagePengguna\ApiProfileController;
use Modules\Sisfo\App\Http\Controllers\Api\Public\ApiIpDinamisKontenController;
use Modules\Sisfo\App\Http\Controllers\Api\Public\ApiPintasanLainnyaController;
use Modules\Sisfo\App\Http\Controllers\Api\ManagePengguna\ApiHakAksesController;
use Modules\Sisfo\App\Http\Controllers\Api\Public\ApiBeritaPengumumanController;
use Modules\Sisfo\App\Http\Controllers\Api\Public\ApiBeritaLandingPageController;
use Modules\Sisfo\App\Http\Controllers\Api\EForm\ApiPengaduanMasyarakatController;
use Modules\Sisfo\App\Http\Controllers\Api\EForm\ApiPermohonanInformasiController;
use Modules\Sisfo\App\Http\Controllers\Api\EForm\ApiPermohonanPerawatanController;
use Modules\Sisfo\App\Http\Controllers\Api\EForm\ApiPernyataanKeberatanController;
use Modules\Sisfo\App\Http\Controllers\Api\MenuManagement\ApiWebMenuUrlController;
use Modules\Sisfo\App\Http\Controllers\Api\Public\ApiKetentuanPelaporanController;
use Modules\Sisfo\App\Http\Controllers\Api\Public\ApiDashboardStatisticsController;
use Modules\Sisfo\App\Http\Controllers\Api\Public\ApiPenyelesaianSengketaController;
use Modules\Sisfo\App\Http\Controllers\Api\MenuManagement\ApiWebMenuGlobalController;
use Modules\Sisfo\App\Http\Controllers\Api\Public\ApiPengumumanLandingPageController;
use Modules\Sisfo\App\Http\Controllers\Api\MenuManagement\ApiMenuManagementController;
use Modules\Sisfo\App\Http\Controllers\Api\DaftarPengajuan\VerifPengajuan\ApiVerifPIController;
use Modules\Sisfo\App\Http\Controllers\Api\DaftarPengajuan\VerifPengajuan\ApiVerifPKController;
use Modules\Sisfo\App\Http\Controllers\Api\DaftarPengajuan\VerifPengajuan\ApiVerifPMController;
use Modules\Sisfo\App\Http\Controllers\Api\DaftarPengajuan\VerifPengajuan\ApiVerifPPController;
use Modules\Sisfo\App\Http\Controllers\Api\DaftarPengajuan\VerifPengajuan\ApiVerifWBSController;
use Modules\Sisfo\App\Http\Controllers\Api\DaftarPengajuan\ReviewPengajuan\ApiReviewPIController;
use Modules\Sisfo\App\Http\Controllers\Api\DaftarPengajuan\ReviewPengajuan\ApiReviewPKController;
use Modules\Sisfo\App\Http\Controllers\Api\DaftarPengajuan\ReviewPengajuan\ApiReviewPMController;
use Modules\Sisfo\App\Http\Controllers\Api\DaftarPengajuan\ReviewPengajuan\ApiReviewPPController;
use Modules\Sisfo\App\Http\Controllers\Api\DaftarPengajuan\ReviewPengajuan\ApiReviewWBSController;
use Modules\Sisfo\App\Http\Controllers\Api\DaftarPengajuan\VerifPengajuan\ApiVerifPengajuanController;
use Modules\Sisfo\App\Http\Controllers\Api\DaftarPengajuan\ReviewPengajuan\ApiReviewPengajuanController;

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

    // Protected routes (perlu autentikasi + validasi app key)
    Route::middleware(['jwt.user', 'validate.app_key'])->group(function () {
        Route::post('logout', [ApiAuthController::class, 'logout']);
        Route::get('user', [ApiAuthController::class, 'getData']);

        // api set_hak_akses
        Route::prefix('set-hak-akses')->group(function () {
            Route::get('/', [ApiSetHakAksesController::class, 'index']);
            Route::get('/getData', [ApiSetHakAksesController::class, 'getData']);
            Route::get('/addData', [ApiSetHakAksesController::class, 'addData']);
            Route::get('/getHakAksesData/{param1}/{param2?}', [ApiSetHakAksesController::class, 'editData']);
            Route::post('/updateData', [ApiSetHakAksesController::class, 'updateData']);
            Route::get('/detailData/{id}', [ApiSetHakAksesController::class, 'detailData']);
        });
        // api m_hak_akses
        Route::prefix('hak-akses')->group(function () {
            Route::get('/', [ApiHakAksesController::class, 'index']);
            Route::get('/getData', [ApiHakAksesController::class, 'getData']);
            Route::get('/addData', [ApiHakAksesController::class, 'addData']);
            Route::post('/createData', [ApiHakAksesController::class, 'createData']);
            Route::get('/editData/{id}', [ApiHakAksesController::class, 'editData']);
            Route::post('/updateData/{id}', [ApiHakAksesController::class, 'updateData']);
            Route::get('/detailData/{id}', [ApiHakAksesController::class, 'detailData']);
            Route::get('/deleteData/{id}', [ApiHakAksesController::class, 'deleteDataView']);
            Route::delete('/deleteData/{id}', [ApiHakAksesController::class, 'deleteData']);
        });

        // Route Api WebMenuGlobal
        Route::prefix('management-menu-global')->group(function () {
            Route::get('/', [ApiWebMenuGlobalController::class, 'index']);
            Route::get('/getData', [ApiWebMenuGlobalController::class, 'getData']);
            Route::get('/addData', [ApiWebMenuGlobalController::class, 'addData']);
            Route::post('/createData', [ApiWebMenuGlobalController::class, 'createData']);
            Route::get('/editData/{id}', [ApiWebMenuGlobalController::class, 'editData']);
            Route::post('/updateData/{id}', [ApiWebMenuGlobalController::class, 'updateData']);
            Route::get('/detailData/{id}', [ApiWebMenuGlobalController::class, 'detailData']);
            Route::get('/deleteData/{id}', [ApiWebMenuGlobalController::class, 'deleteDataView']);
            Route::delete('/deleteData/{id}', [ApiWebMenuGlobalController::class, 'deleteData']);
            Route::get('/getMenuUrl', [ApiWebMenuGlobalController::class, 'getMenuUrl']);
        });
        Route::prefix('management-menu-url')->group(function () {
            Route::get('/', [ApiWebMenuUrlController::class, 'index']);
            Route::get('/getData', [ApiWebMenuUrlController::class, 'getData']);
            Route::get('/addData', [ApiWebMenuUrlController::class, 'addData']);
            Route::post('/createData', [ApiWebMenuUrlController::class, 'createData']);
            Route::get('/editData/{id}', [ApiWebMenuUrlController::class, 'editData']);
            Route::post('/updateData/{id}', [ApiWebMenuUrlController::class, 'updateData']);
            Route::get('/detailData/{id}', [ApiWebMenuUrlController::class, 'detailData']);
            Route::delete('/deleteData/{id}', [ApiWebMenuUrlController::class, 'deleteData']);
            Route::get('/applications', [ApiWebMenuUrlController::class, 'getApplications']);
        });
        // Route Api untuk User Management
        Route::prefix('user')->group(function () {
            Route::get('/', [ApiUserController::class, 'index']);
            Route::get('/getData', [ApiUserController::class, 'getData']);
            Route::get('/addData', [ApiUserController::class, 'addData']);
            Route::post('/createData', [ApiUserController::class, 'createData']);
            Route::get('/editData/{id}', [ApiUserController::class, 'editData']);
            Route::post('/updateData/{id}', [ApiUserController::class, 'updateData']);
            Route::get('/detailData/{id}', [ApiUserController::class, 'detailData']);
            Route::get('/deleteData/{id}', [ApiUserController::class, 'deleteDataView']);
            Route::delete('/deleteData/{id}', [ApiUserController::class, 'deleteData']);
            Route::post('/addHakAkses/{userId}', [ApiUserController::class, 'addHakAkses']);
            Route::delete('/removeHakAkses/{userId}', [ApiUserController::class, 'removeHakAkses']);
            Route::get('/available-hak-akses/{userId}', [ApiUserController::class, 'getAvailableHakAkses']);
        });
        // Route Api untuk Menu Management
        Route::prefix('menu-management')->group(function () {
            Route::get('/', [ApiMenuManagementController::class, 'index']);
            Route::get('/addData/{hakAksesId}', [ApiMenuManagementController::class, 'addData']);
            Route::post('/createData', [ApiMenuManagementController::class, 'createData']);
            Route::get('/{id}/editData', [ApiMenuManagementController::class, 'editData']);
            Route::put('/{id}/updateData', [ApiMenuManagementController::class, 'updateData']);
            Route::get('/{id}/detailData', [ApiMenuManagementController::class, 'detailData']);
            Route::delete('/{id}/deleteData', [ApiMenuManagementController::class, 'deleteData']);
            Route::post('/reorder', [ApiMenuManagementController::class, 'reorder']);
            Route::get('/get-parent-menus/{hakAksesId}', [ApiMenuManagementController::class, 'getParentMenus']);
        });
        Route::prefix('profile')->group(function () {
            Route::get('/', [ApiProfileController::class, 'index']);
            Route::post('/update_pengguna/{id}', [ApiProfileController::class, 'updatePengguna']);
            Route::put('/update_password/{id}', [ApiProfileController::class, 'updatePassword']);
            Route::delete('/delete_foto_profil/{id}', [ApiProfileController::class, 'deleteFotoProfil']);
            Route::delete('/delete_foto_ktp/{id}', [ApiProfileController::class, 'deleteFotoKtp']);
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
        // Route Api untuk DaftarPengajuan/Verifikasi Pengajuan
        Route::prefix('daftar-verifikasi-pengajuan')->group(function () {
            // Route utama - dashboard verifikasi pengajuan
            Route::get('/', [ApiVerifPengajuanController::class, 'index']);
            Route::get('/summary', [ApiVerifPengajuanController::class, 'getSummary']);

            // Route untuk Verifikasi Permohonan Informasi 
            Route::prefix('permohonan-informasi')->group(function () {
                Route::get('/', [ApiVerifPIController::class, 'index']);
                Route::get('/approve-modal/{id}', [ApiVerifPIController::class, 'getApproveModal']);
                Route::get('/decline-modal/{id}', [ApiVerifPIController::class, 'getDeclineModal']);
                Route::post('/setujuiPermohonan/{id}', [ApiVerifPIController::class, 'setujuiPermohonan']);
                Route::post('/tolakPermohonan/{id}', [ApiVerifPIController::class, 'tolakPermohonan']);
                Route::post('/tandaiDibaca/{id}', [ApiVerifPIController::class, 'tandaiDibaca']);
                Route::post('/hapusPermohonan/{id}', [ApiVerifPIController::class, 'hapusPermohonan']);
            });

            // Route untuk Verifikasi Pernyataan Keberatan 
            Route::prefix('pernyataan-keberatan')->group(function () {
                Route::get('/', [ApiVerifPKController::class, 'index']);
                Route::get('/approve-modal/{id}', [ApiVerifPKController::class, 'getApproveModal']);
                Route::get('/decline-modal/{id}', [ApiVerifPKController::class, 'getDeclineModal']);
                Route::post('/setujuiPermohonan/{id}', [ApiVerifPKController::class, 'setujuiPermohonan']);
                Route::post('/tolakPermohonan/{id}', [ApiVerifPKController::class, 'tolakPermohonan']);
                Route::post('/tandaiDibaca/{id}', [ApiVerifPKController::class, 'tandaiDibaca']);
                Route::post('/hapusPermohonan/{id}', [ApiVerifPKController::class, 'hapusPermohonan']);
            });

            // Route untuk Verifikasi Pengaduan Masyarakat 
            Route::prefix('pengaduan-masyarakat')->group(function () {
                Route::get('/', [ApiVerifPMController::class, 'index']);
                Route::get('/approve-modal/{id}', [ApiVerifPMController::class, 'getApproveModal']);
                Route::get('/decline-modal/{id}', [ApiVerifPMController::class, 'getDeclineModal']);
                Route::post('/setujuiPermohonan/{id}', [ApiVerifPMController::class, 'setujuiPermohonan']);
                Route::post('/tolakPermohonan/{id}', [ApiVerifPMController::class, 'tolakPermohonan']);
                Route::post('/tandaiDibaca/{id}', [ApiVerifPMController::class, 'tandaiDibaca']);
                Route::post('/hapusPermohonan/{id}', [ApiVerifPMController::class, 'hapusPermohonan']);
            });

            // Route untuk Verifikasi Whistle Blowing System 
            Route::prefix('whistle-blowing-system')->group(function () {
                Route::get('/', [ApiVerifWBSController::class, 'index']);
                Route::get('/approve-modal/{id}', [ApiVerifWBSController::class, 'getApproveModal']);
                Route::get('/decline-modal/{id}', [ApiVerifWBSController::class, 'getDeclineModal']);
                Route::post('/setujuiPermohonan/{id}', [ApiVerifWBSController::class, 'setujuiPermohonan']);
                Route::post('/tolakPermohonan/{id}', [ApiVerifWBSController::class, 'tolakPermohonan']);
                Route::post('/tandaiDibaca/{id}', [ApiVerifWBSController::class, 'tandaiDibaca']);
                Route::post('/hapusPermohonan/{id}', [ApiVerifWBSController::class, 'hapusPermohonan']);
            });

            // Route untuk Verifikasi Permohonan Perawatan 
            Route::prefix('permohonan-perawatan')->group(function () {
                Route::get('/', [ApiVerifPPController::class, 'index']);
                Route::get('/approve-modal/{id}', [ApiVerifPPController::class, 'getApproveModal']);
                Route::get('/decline-modal/{id}', [ApiVerifPPController::class, 'getDeclineModal']);
                Route::post('/setujuiPermohonan/{id}', [ApiVerifPPController::class, 'setujuiPermohonan']);
                Route::post('/tolakPermohonan/{id}', [ApiVerifPPController::class, 'tolakPermohonan']);
                Route::post('/tandaiDibaca/{id}', [ApiVerifPPController::class, 'tandaiDibaca']);
                Route::post('/hapusPermohonan/{id}', [ApiVerifPPController::class, 'hapusPermohonan']);
            });
        });
        // Route Api untuk DaftarPengajuan/Review Pengajuan (Tahap setelah Verifikasi)
        Route::prefix('daftar-review-pengajuan')->group(function () {
            // Route utama - dashboard review pengajuan
            Route::get('/', [ApiReviewPengajuanController::class, 'index']);
            Route::get('/summary', [ApiReviewPengajuanController::class, 'getSummary']);

            // Route untuk Review Permohonan Informasi 
            Route::prefix('permohonan-informasi')->group(function () {
                Route::get('/', [ApiReviewPIController::class, 'index']);
                Route::get('/approve-modal/{id}', [ApiReviewPIController::class, 'getApproveModal']);
                Route::get('/decline-modal/{id}', [ApiReviewPIController::class, 'getDeclineModal']);
                Route::post('/setujuiPermohonan/{id}', [ApiReviewPIController::class, 'setujuiReview']);
                Route::post('/tolakPermohonan/{id}', [ApiReviewPIController::class, 'tolakReview']);
                Route::post('/tandaiDibaca/{id}', [ApiReviewPIController::class, 'tandaiDibacaReview']);
                Route::delete('/hapusPermohonan/{id}', [ApiReviewPIController::class, 'hapusReview']);
            });
            // Route untuk Review Pernyataan Keberatan
            Route::prefix('pernyataan-keberatan')->group(function () {
                Route::get('/', [ApiReviewPKController::class, 'index']);
                Route::get('/approve-modal/{id}', [ApiReviewPKController::class, 'getApproveModal']);
                Route::get('/decline-modal/{id}', [ApiReviewPKController::class, 'getDeclineModal']);
                Route::post('/setujuiPermohonan/{id}', [ApiReviewPKController::class, 'setujuiReview']);
                Route::post('/tolakPermohonan/{id}', [ApiReviewPKController::class, 'tolakReview']);
                Route::post('/tandaiDibaca/{id}', [ApiReviewPKController::class, 'tandaiDibacaReview']);
                Route::delete('/hapusPermohonan/{id}', [ApiReviewPKController::class, 'hapusReview']);
            });

            // Route untuk Review Pengaduan Masyarakat
            Route::prefix('pengaduan-masyarakat')->group(function () {
                Route::get('/', [ApiReviewPMController::class, 'index']);
                Route::get('/approve-modal/{id}', [ApiReviewPMController::class, 'getApproveModal']);
                Route::get('/decline-modal/{id}', [ApiReviewPMController::class, 'getDeclineModal']);
                Route::post('/setujuiPermohonan/{id}', [ApiReviewPMController::class, 'setujuiReview']);
                Route::post('/tolakPermohonan/{id}', [ApiReviewPMController::class, 'tolakReview']);
                Route::post('/tandaiDibaca/{id}', [ApiReviewPMController::class, 'tandaiDibacaReview']);
                Route::delete('/hapusPermohonan/{id}', [ApiReviewPMController::class, 'hapusReview']);
            });

            // Route untuk Review WBS
            Route::prefix('whistle-blowing-system')->group(function () {
                Route::get('/', [ApiReviewWBSController::class, 'index']);
                Route::get('/approve-modal/{id}', [ApiReviewWBSController::class, 'getApproveModal']);
                Route::get('/decline-modal/{id}', [ApiReviewWBSController::class, 'getDeclineModal']);
                Route::post('/setujuiPermohonan/{id}', [ApiReviewWBSController::class, 'setujuiReview']);
                Route::post('/tolakPermohonan/{id}', [ApiReviewWBSController::class, 'tolakReview']);
                Route::post('/tandaiDibaca/{id}', [ApiReviewWBSController::class, 'tandaiDibacaReview']);
                Route::delete('/hapusPermohonan/{id}', [ApiReviewWBSController::class, 'hapusReview']);
            });

            // Route untuk Review Permohonan Perawatan 
            Route::prefix('permohonan-perawatan')->group(function () {
                Route::get('/', [ApiReviewPPController::class, 'index']);
                Route::get('/approve-modal/{id}', [ApiReviewPPController::class, 'getApproveModal']);
                Route::get('/decline-modal/{id}', [ApiReviewPPController::class, 'getDeclineModal']);
                Route::post('/setujuiPermohonan/{id}', [ApiReviewPPController::class, 'setujuiReview']);
                Route::post('/tolakPermohonan/{id}', [ApiReviewPPController::class, 'tolakReview']);
                Route::post('/tandaiDibaca/{id}', [ApiReviewPPController::class, 'tandaiDibacaReview']);
                Route::delete('/hapusPermohonan/{id}', [ApiReviewPPController::class, 'hapusReview']);
            });
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
    Route::get('getDataLayananInformasiDinamis', [ApiLIDinamisController::class, 'getDataLayananInformasiDinamis']);
    Route::get('getDataPenyelesaianSengketa', [ApiPenyelesaianSengketaController::class, 'getDataPenyelesaianSengketa']);
});