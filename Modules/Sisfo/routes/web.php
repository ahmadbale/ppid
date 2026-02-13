<?php
;

use App\Helpers\RouteHelper;
use Illuminate\Support\Facades\Route;
use Modules\Sisfo\App\Models\Website\WebMenuModel;
use Modules\Sisfo\App\Http\Controllers\AuthController;
use Modules\Sisfo\App\Http\Controllers\ProfileController;
use Modules\Sisfo\App\Http\Controllers\SummernoteController;
use Modules\Sisfo\App\Http\Controllers\SwitchRoleController;
use Modules\Sisfo\App\Http\Controllers\DashboardMPUController;
use Modules\Sisfo\App\Http\Controllers\DashboardSARController;
use Modules\Sisfo\App\Http\Controllers\DashboardAdminController;
use Modules\Sisfo\App\Http\Controllers\DashboardRespondenController;
use Modules\Sisfo\App\Http\Controllers\DashboardVerifikatorController;
use Modules\Sisfo\App\Http\Controllers\HakAkses\SetHakAksesController;
use Modules\Sisfo\App\Http\Controllers\Notifikasi\NotifAdminController;
use Modules\Sisfo\App\Http\Controllers\SistemInformasi\DaftarPengajuan\VerifPengajuan\VerifPIController;
use Modules\Sisfo\App\Http\Controllers\AdminWeb\InformasiPublik\TabelDinamis\GetIPInformasiBerkalaController;
use Modules\Sisfo\App\Http\Controllers\AdminWeb\InformasiPublik\TabelDinamis\GetIPInformasiSertaMertaController;
use Modules\Sisfo\App\Http\Controllers\AdminWeb\InformasiPublik\TabelDinamis\GetIPInformasiSetiapSaatController;
use Modules\Sisfo\App\Http\Controllers\AdminWeb\InformasiPublik\TabelDinamis\SetIpDinamisTabelController;
use Modules\Sisfo\App\Http\Controllers\AdminWeb\MenuManagement\MenuManagementController;
use Modules\Sisfo\App\Http\Controllers\DashboardDefaultController;
use Modules\Sisfo\App\Http\Controllers\SistemInformasi\DaftarPengajuan\ReviewPengajuan\ReviewPengajuanController;
use Modules\Sisfo\App\Http\Controllers\SistemInformasi\DaftarPengajuan\ReviewPengajuan\ReviewPIController;
use Modules\Sisfo\App\Http\Controllers\SistemInformasi\DaftarPengajuan\ReviewPengajuan\ReviewPKController;
use Modules\Sisfo\App\Http\Controllers\SistemInformasi\DaftarPengajuan\ReviewPengajuan\ReviewPMController;
use Modules\Sisfo\App\Http\Controllers\SistemInformasi\DaftarPengajuan\ReviewPengajuan\ReviewPPController;
use Modules\Sisfo\App\Http\Controllers\SistemInformasi\DaftarPengajuan\ReviewPengajuan\ReviewWBSController;
use Modules\Sisfo\App\Http\Controllers\SistemInformasi\DaftarPengajuan\VerifPengajuan\VerifPengajuanController;
use Modules\Sisfo\App\Http\Controllers\SistemInformasi\DaftarPengajuan\VerifPengajuan\VerifPKController;
use Modules\Sisfo\App\Http\Controllers\SistemInformasi\DaftarPengajuan\VerifPengajuan\VerifPMController;
use Modules\Sisfo\App\Http\Controllers\SistemInformasi\DaftarPengajuan\VerifPengajuan\VerifPPController;
use Modules\Sisfo\App\Http\Controllers\SistemInformasi\DaftarPengajuan\VerifPengajuan\VerifWBSController;
use Modules\Sisfo\App\Http\Controllers\WhatsAppController;
use Modules\Sisfo\App\Http\Controllers\PageController;

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

Route::pattern('id', '[0-9]+');

Route::get('login', [AuthController::class, 'login'])->name('login');
Route::post('login', [AuthController::class, 'postlogin']);
Route::get('logout', [AuthController::class, 'logout'])->middleware('auth');
Route::get('/pilih-level', [AuthController::class, 'pilihLevel'])->name('pilih.level');
Route::post('/pilih-level', [AuthController::class, 'pilihLevelPost'])->name('pilih.level.post');
Route::post('register', [AuthController::class, 'postRegister']);


// Group route yang memerlukan autentikasi
Route::middleware('auth')->group(function () {
    Route::get('/dashboardSAR', [DashboardSARController::class, 'index'])->middleware('authorize:SAR');
    Route::get('/dashboardADM', [DashboardAdminController::class, 'index'])->middleware('authorize:ADM');
    Route::post('/dashboardADM/filter', [DashboardAdminController::class, 'filterData'])->name('sisfo.dashboard.admin.filter')->middleware('authorize:ADM');
    Route::get('/dashboardADM/lihat-semua', [DashboardAdminController::class, 'lihatSemua'])->name('sisfo.dashboard.admin.lihat-semua')->middleware('authorize:ADM');
    Route::get('/dashboardRPN', [DashboardRespondenController::class, 'index'])->middleware('authorize:RPN');
    Route::get('/dashboardMPU', [DashboardMPUController::class, 'index'])->middleware('authorize:MPU');
    Route::get('/dashboardVFR', [DashboardVerifikatorController::class, 'index'])->middleware('authorize:VFR');
    Route::get('/dashboard', [DashboardDefaultController::class, 'index']);

    Route::group(['prefix' => 'HakAkses', 'middleware' => 'authorize:SAR'], function () {
        Route::get('/', [SetHakAksesController::class, 'index']);
        Route::get('/addData', [SetHakAksesController::class, 'addData']);
        Route::post('/createData', [SetHakAksesController::class, 'createData']);
        Route::get('/getHakAksesData/{param1}/{param2?}', [SetHakAksesController::class, 'editData']);
        Route::post('/updateData', [SetHakAksesController::class, 'updateData']);
    });

    Route::get('/session', [AuthController::class, 'getData']);
    Route::get('/js/summernote.js', [SummernoteController::class, 'getSummernoteJS']);
    Route::get('/css/summernote.css', [SummernoteController::class, 'getSummernoteCSS']);
    Route::get('/switch-role/{hakAksesId}', [SwitchRoleController::class, 'index'])->name('switch.role');

    // Route::group(['prefix' => 'profile'], function () {
    //     Route::get('/', [ProfileController::class, 'index']);
    //     Route::put('/update_pengguna/{id}', [ProfileController::class, 'update_pengguna']);
    //     Route::put('/update_password/{id}', [ProfileController::class, 'update_password']);
    // });
    Route::group(['prefix' => 'profile'], function () {
        Route::get('/', [ProfileController::class, 'index']);
        Route::put('/update_pengguna/{id}', [ProfileController::class, 'update_pengguna']);
        Route::put('/update_password/{id}', [ProfileController::class, 'update_password']);
        Route::delete('/delete_foto_profil/{id}', [ProfileController::class, 'delete_foto_profil']); // Tambah ini
        Route::delete('/delete_foto_ktp/{id}', [ProfileController::class, 'delete_foto_ktp']); // Tambah ini
    });

    // ❌ ROUTE TIDAK STANDAR - NotifAdmin (Custom notification endpoints)
    Route::group(['prefix' => 'Notifikasi/NotifAdmin', 'middleware' => ['authorize:ADM']], function () {
        Route::get('/', [NotifAdminController::class, 'index']);
        Route::get('/notifPI', [NotifAdminController::class, 'notifikasiPermohonanInformasi']);
        Route::get('/notifPK', [NotifAdminController::class, 'notifikasiPernyataanKeberatan']);
        Route::get('/notifPM', [NotifAdminController::class, 'notifikasiPengaduanMasyarakat']);
        Route::get('/notifWBS', [NotifAdminController::class, 'notifikasiWBS']);
        Route::get('/notifPP', [NotifAdminController::class, 'notifikasiPermohonanPerawatan']);
        Route::post('/tandai-dibaca/{id}', [NotifAdminController::class, 'tandaiDibaca']);
        Route::delete('/hapus/{id}', [NotifAdminController::class, 'hapusNotifikasi']);
        Route::post('/tandai-semua-dibaca', [NotifAdminController::class, 'tandaiSemuaDibaca']);
        Route::delete('/hapus-semua-dibaca', [NotifAdminController::class, 'hapusSemuaDibaca']);
    });

    // ✅ ROUTE STANDAR - daftar-verifikasi-pengajuan (Parent only, sub-menu pakai dynamic routing)
    Route::group(['prefix' => WebMenuModel::getDynamicMenuUrl('daftar-verifikasi-pengajuan')], function () {
        Route::get('/', [VerifPengajuanController::class, 'index'])->middleware('permission:view');
    });

    Route::group(['prefix' => WebMenuModel::getDynamicMenuUrl('daftar-verifikasi-pengajuan-permohonan-informasi')], function () {
        Route::get('/', [VerifPIController::class, 'index']);
        Route::get('/getData', [VerifPIController::class, 'getData']);
        Route::get('/editData/{id}', [VerifPIController::class, 'editData']);
        Route::post('/updateData/{id}', [VerifPIController::class, 'updateData']);
        Route::delete('/deleteData/{id}', [VerifPIController::class, 'deleteData']);
    });

    Route::group(['prefix' => WebMenuModel::getDynamicMenuUrl('daftar-verifikasi-pengajuan-pernyataan-keberatan')], function () {
        Route::get('/', [VerifPKController::class, 'index'])->middleware('permission:view');
        Route::get('/getData', [VerifPKController::class, 'getData'])->middleware('permission:view');
        Route::get('/editData/{id}', [VerifPKController::class, 'editData'])->middleware('permission:update');
        Route::post('/updateData/{id}', [VerifPKController::class, 'updateData'])->middleware('permission:update');
        Route::delete('/deleteData/{id}', [VerifPKController::class, 'deleteData'])->middleware('permission:delete');
    });

    Route::group(['prefix' => WebMenuModel::getDynamicMenuUrl('daftar-verifikasi-pengajuan-pengaduan-masyarakat')], function () {
        Route::get('/', [VerifPMController::class, 'index'])->middleware('permission:view');
        Route::get('/getData', [VerifPMController::class, 'getData'])->middleware('permission:view');
        Route::get('/editData/{id}', [VerifPMController::class, 'editData'])->middleware('permission:update');
        Route::post('/updateData/{id}', [VerifPMController::class, 'updateData'])->middleware('permission:update');
        Route::delete('/deleteData/{id}', [VerifPMController::class, 'deleteData'])->middleware('permission:delete');
    });

    Route::group(['prefix' => WebMenuModel::getDynamicMenuUrl('daftar-verifikasi-pengajuan-whistle-blowing-system')], function () {
        Route::get('/', [VerifWBSController::class, 'index'])->middleware('permission:view');
        Route::get('/getData', [VerifWBSController::class, 'getData'])->middleware('permission:view');
        Route::get('/editData/{id}', [VerifWBSController::class, 'editData'])->middleware('permission:update');
        Route::post('/updateData/{id}', [VerifWBSController::class, 'updateData'])->middleware('permission:update');
        Route::delete('/deleteData/{id}', [VerifWBSController::class, 'deleteData'])->middleware('permission:delete');
    });

    // ✅ ROUTE STANDAR - daftar-verifikasi-pengajuan-permohonan-perawatan
    Route::group(['prefix' => WebMenuModel::getDynamicMenuUrl('daftar-verifikasi-pengajuan-permohonan-perawatan')], function () {
        Route::get('/', [VerifPPController::class, 'index'])->middleware('permission:view');
        Route::get('/getData', [VerifPPController::class, 'getData'])->middleware('permission:view');
        Route::get('/editData/{id}', [VerifPPController::class, 'editData'])->middleware('permission:update');
        Route::post('/updateData/{id}', [VerifPPController::class, 'updateData'])->middleware('permission:update');
        Route::delete('/deleteData/{id}', [VerifPPController::class, 'deleteData'])->middleware('permission:delete');
    });

    // ✅ ROUTE STANDAR - daftar-review-pengajuan (Parent only, sub-menu pakai dynamic routing)
    Route::group(['prefix' => WebMenuModel::getDynamicMenuUrl('daftar-review-pengajuan')], function () {
        Route::get('/', [ReviewPengajuanController::class, 'index'])->middleware('permission:view');
    });

    // ✅ ROUTE STANDAR - daftar-review-pengajuan-permohonan-informasi
    Route::group(['prefix' => WebMenuModel::getDynamicMenuUrl('daftar-review-pengajuan-permohonan-informasi')], function () {
        Route::get('/', [ReviewPIController::class, 'index'])->middleware('permission:view');
        Route::get('/getData', [ReviewPIController::class, 'getData'])->middleware('permission:view');
        Route::get('/editData/{id}', [ReviewPIController::class, 'editData'])->middleware('permission:update');
        Route::post('/updateData/{id}', [ReviewPIController::class, 'updateData'])->middleware('permission:update');
        Route::delete('/deleteData/{id}', [ReviewPIController::class, 'deleteData'])->middleware('permission:delete');
    });

    // ✅ ROUTE STANDAR - daftar-review-pengajuan-pernyataan-keberatan
    Route::group(['prefix' => WebMenuModel::getDynamicMenuUrl('daftar-review-pengajuan-pernyataan-keberatan')], function () {
        Route::get('/', [ReviewPKController::class, 'index'])->middleware('permission:view');
        Route::get('/getData', [ReviewPKController::class, 'getData'])->middleware('permission:view');
        Route::get('/editData/{id}', [ReviewPKController::class, 'editData'])->middleware('permission:update');
        Route::post('/updateData/{id}', [ReviewPKController::class, 'updateData'])->middleware('permission:update');
        Route::delete('/deleteData/{id}', [ReviewPKController::class, 'deleteData'])->middleware('permission:delete');
    });

    Route::group(['prefix' => WebMenuModel::getDynamicMenuUrl('daftar-review-pengajuan-pengaduan-masyarakat')], function () {
        Route::get('/', [ReviewPMController::class, 'index'])->middleware('permission:view');
        Route::get('/getData', [ReviewPMController::class, 'getData'])->middleware('permission:view');
        Route::get('/editData/{id}', [ReviewPMController::class, 'editData'])->middleware('permission:update');
        Route::post('/updateData/{id}', [ReviewPMController::class, 'updateData'])->middleware('permission:update');
        Route::delete('/deleteData/{id}', [ReviewPMController::class, 'deleteData'])->middleware('permission:delete');
    });

    Route::group(['prefix' => WebMenuModel::getDynamicMenuUrl('daftar-review-pengajuan-whistle-blowing-system')], function () {
        Route::get('/', [ReviewWBSController::class, 'index'])->middleware('permission:view');
        Route::get('/getData', [ReviewWBSController::class, 'getData'])->middleware('permission:view');
        Route::get('/editData/{id}', [ReviewWBSController::class, 'editData'])->middleware('permission:update');
        Route::post('/updateData/{id}', [ReviewWBSController::class, 'updateData'])->middleware('permission:update');
        Route::delete('/deleteData/{id}', [ReviewWBSController::class, 'deleteData'])->middleware('permission:delete');
    });

    Route::group(['prefix' => WebMenuModel::getDynamicMenuUrl('daftar-review-pengajuan-permohonan-perawatan')], function () {
        Route::get('/', [ReviewPPController::class, 'index'])->middleware('permission:view');
        Route::get('/getData', [ReviewPPController::class, 'getData'])->middleware('permission:view');
        Route::get('/editData/{id}', [ReviewPPController::class, 'editData'])->middleware('permission:update');
        Route::post('/updateData/{id}', [ReviewPPController::class, 'updateData'])->middleware('permission:update');
        Route::delete('/deleteData/{id}', [ReviewPPController::class, 'deleteData'])->middleware('permission:delete');
    });


    // ❌ ROUTE TIDAK STANDAR - whatsapp-management (9 standar + 8 extra functions)
    Route::group(['prefix' => WebMenuModel::getDynamicMenuUrl('whatsapp-management')], function () {
        Route::get('/', [WhatsAppController::class, 'index'])->middleware('permission:view');
        Route::post('/start', [WhatsAppController::class, 'startServer'])->middleware('permission:create');
        Route::post('/stop', [WhatsAppController::class, 'stopServer'])->middleware('permission:update');
        Route::post('/reset', [WhatsAppController::class, 'resetSession'])->middleware('permission:update');
        Route::get('/status', [WhatsAppController::class, 'getStatus']);
        Route::get('/qr-code', [WhatsAppController::class, 'getQRCode']);
        Route::get('/qrcode-status', [WhatsAppController::class, 'getQRCodeStatus']);
        Route::post('/auto-save-qrcode-log', [WhatsAppController::class, 'autoSaveQRCodeLog']);
        Route::get('/connected-phone', [WhatsAppController::class, 'getConnectedPhone']);
        Route::post('/trigger-scan-log', [WhatsAppController::class, 'triggerScanLog']);
        Route::post('/confirm-scan-log', [WhatsAppController::class, 'confirmScanLog'])->middleware('permission:create');
        Route::post('/reset-expired-scan', [WhatsAppController::class, 'resetExpiredScan'])->middleware('permission:update');
    });

    // Pattern 1: /{page} → index() atau store() tergantung HTTP method
    Route::match(['GET', 'POST'], '/{page}', [PageController::class, 'index'])
        ->middleware('check.dynamic.route')
        ->where('page', RouteHelper::getDynamicRoutePattern());

    // Pattern 2: /{page}/{action} → getData(), addData(), editData/{id}, dll
    Route::match(['GET', 'POST'], '/{page}/{action}', [PageController::class, 'index'])
        ->middleware('check.dynamic.route')
        ->where('page', RouteHelper::getDynamicRoutePattern())
        ->where('action', '[a-zA-Z0-9\-]+');

    // Pattern 3: /{page}/{action}/{id} → editData/123, updateData/123, deleteData/123
    Route::match(['GET', 'POST', 'PUT', 'DELETE'], '/{page}/{action}/{id}', [PageController::class, 'index'])
        ->middleware('check.dynamic.route')
        ->where('page', RouteHelper::getDynamicRoutePattern())
        ->where('action', '[a-zA-Z0-9\-]+')
        ->where('id', '[0-9]+');
});
