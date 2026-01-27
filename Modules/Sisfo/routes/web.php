<?php
;
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
use Modules\Sisfo\App\Http\Controllers\ManagePengguna\UserController;
use Modules\Sisfo\App\Http\Controllers\DashboardVerifikatorController;
use Modules\Sisfo\App\Http\Controllers\HakAkses\SetHakAksesController;
use Modules\Sisfo\App\Http\Controllers\Notifikasi\NotifAdminController;
use Modules\Sisfo\App\Http\Controllers\AdminWeb\Berita\BeritaController;
use Modules\Sisfo\App\Http\Controllers\AdminWeb\Footer\FooterController;
use Modules\Sisfo\App\Http\Controllers\ManagePengguna\HakAksesController;
use Modules\Sisfo\App\Http\Controllers\SistemInformasi\EForm\WBSController;
use Modules\Sisfo\App\Http\Controllers\AdminWeb\Berita\BeritaDinamisController;
use Modules\Sisfo\App\Http\Controllers\AdminWeb\Footer\KategoriFooterController;
use Modules\Sisfo\App\Http\Controllers\AdminWeb\Pengumuman\PengumumanController;
use Modules\Sisfo\App\Http\Controllers\AdminWeb\KategoriAkses\AksesCepatController;
use Modules\Sisfo\App\Http\Controllers\SistemInformasi\Timeline\TimelineController;
use Modules\Sisfo\App\Http\Controllers\AdminWeb\MediaDinamis\MediaDinamisController;
use Modules\Sisfo\App\Http\Controllers\AdminWeb\MenuManagement\WebMenuUrlController;
use Modules\Sisfo\App\Http\Controllers\AdminWeb\LayananInformasi\LIDinamisController;
use Modules\Sisfo\App\Http\Controllers\AdminWeb\LayananInformasi\LIDUploadController;
use Modules\Sisfo\App\Http\Controllers\AdminWeb\InformasiPublik\LHKPN\LhkpnController;
use Modules\Sisfo\App\Http\Controllers\AdminWeb\KategoriAkses\KategoriAksesController;
use Modules\Sisfo\App\Http\Controllers\AdminWeb\MenuManagement\WebMenuGlobalController;
use Modules\Sisfo\App\Http\Controllers\AdminWeb\Pengumuman\PengumumanDinamisController;
use Modules\Sisfo\App\Http\Controllers\AdminWeb\KategoriAkses\PintasanLainnyaController;
use Modules\Sisfo\App\Http\Controllers\AdminWeb\MenuManagement\MenuManagementController;
use Modules\Sisfo\App\Http\Controllers\AdminWeb\MediaDinamis\DetailMediaDinamisController;
use Modules\Sisfo\App\Http\Controllers\SistemInformasi\EForm\PengaduanMasyarakatController;
use Modules\Sisfo\App\Http\Controllers\SistemInformasi\EForm\PermohonanInformasiController;
use Modules\Sisfo\App\Http\Controllers\SistemInformasi\EForm\PermohonanPerawatanController;
use Modules\Sisfo\App\Http\Controllers\SistemInformasi\EForm\PernyataanKeberatanController;
use Modules\Sisfo\App\Http\Controllers\SistemInformasi\KategoriForm\KategoriFormController;
use Modules\Sisfo\App\Http\Controllers\AdminWeb\InformasiPublik\LHKPN\DetailLhkpnController;
use Modules\Sisfo\App\Http\Controllers\AdminWeb\InformasiPublik\Regulasi\RegulasiController;
use Modules\Sisfo\App\Http\Controllers\AdminWeb\KategoriAkses\DetailPintasanLainnyaController;
use Modules\Sisfo\App\Http\Controllers\AdminWeb\InformasiPublik\Regulasi\RegulasiDinamisController;
use Modules\Sisfo\App\Http\Controllers\AdminWeb\InformasiPublik\Regulasi\KategoriRegulasiController;
use Modules\Sisfo\App\Http\Controllers\SistemInformasi\KetentuanPelaporan\KetentuanPelaporanController;
use Modules\Sisfo\App\Http\Controllers\AdminWeb\InformasiPublik\KontenDinamis\IpDinamisKontenController;
use Modules\Sisfo\App\Http\Controllers\AdminWeb\InformasiPublik\KontenDinamis\IpUploadKontenController;
use Modules\Sisfo\App\Http\Controllers\SistemInformasi\DaftarPengajuan\VerifPengajuan\VerifPIController;
use Modules\Sisfo\App\Http\Controllers\AdminWeb\InformasiPublik\PenyelesaianSengketa\PenyelesaianSengketaController;
use Modules\Sisfo\App\Http\Controllers\AdminWeb\InformasiPublik\PenyelesaianSengketa\UploadPSController;
use Modules\Sisfo\App\Http\Controllers\AdminWeb\InformasiPublik\TabelDinamis\GetIPInformasiBerkalaController;
use Modules\Sisfo\App\Http\Controllers\AdminWeb\InformasiPublik\TabelDinamis\GetIPInformasiSertaMertaController;
use Modules\Sisfo\App\Http\Controllers\AdminWeb\InformasiPublik\TabelDinamis\GetIPInformasiSetiapSaatController;
use Modules\Sisfo\App\Http\Controllers\AdminWeb\InformasiPublik\TabelDinamis\IpDinamisTabelController;
use Modules\Sisfo\App\Http\Controllers\AdminWeb\InformasiPublik\TabelDinamis\SetIpDinamisTabelController;
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
use Modules\Sisfo\App\Http\Controllers\PageController; // 🚀 Dynamic Routing Handler

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

Route::pattern('id', '[0-9]+'); // Artinya: Ketika ada parameter {id}, maka harus berupa angka

Route::get('login', [AuthController::class, 'login'])->name('login');
Route::post('login', [AuthController::class, 'postlogin']);
Route::get('logout', [AuthController::class, 'logout'])->middleware('auth');
Route::get('/pilih-level', [AuthController::class, 'pilihLevel'])->name('pilih.level');
Route::post('/pilih-level', [AuthController::class, 'pilihLevelPost'])->name('pilih.level.post');

// Register GET route disabled - menggunakan User module untuk tampilan
// Route::get('register', [AuthController::class, 'register'])->name('register');
// POST tetap aktif untuk backend logic
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

    // ❌ ROUTE TIDAK STANDAR - menu-management (Pattern: {id}/editData, ada reorder & get-parent-menus)
    Route::group(['prefix' => WebMenuModel::getDynamicMenuUrl('menu-management'), 'middleware' => 'authorize:ADM,SAR'], function () {
        Route::get('/', [MenuManagementController::class, 'index'])->middleware('permission:view');
        Route::get('/addData/{hakAksesId}', [MenuManagementController::class, 'addData'])->middleware('permission:create');
        Route::post('/createData', [MenuManagementController::class, 'createData'])->middleware('permission:create');
        Route::get('/{id}/editData', [MenuManagementController::class, 'editData']);
        Route::put('/{id}/updateData', [MenuManagementController::class, 'updateData'])->middleware('permission:update');
        Route::get('/{id}/detailData', [MenuManagementController::class, 'detailData']);
        Route::delete('/{id}/deleteData', [MenuManagementController::class, 'deleteData'])->middleware('permission:delete');
        Route::post('/reorder', [MenuManagementController::class, 'reorder']); // New route for drag-drop reordering
        Route::get('/get-parent-menus/{hakAksesId}', [MenuManagementController::class, 'getParentMenus']);
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

    // ❌ ROUTE TIDAK STANDAR - set-informasi-publik-dinamis-tabel (Memiliki editSubMenuUtama & editSubMenu)
    Route::group(['prefix' => WebMenuModel::getDynamicMenuUrl('set-informasi-publik-dinamis-tabel')], function () {
        Route::get('/', [SetIpDinamisTabelController::class, 'index'])->middleware('permission:view');
        Route::get('/getData', [SetIpDinamisTabelController::class, 'getData']);
        Route::get('/addData', [SetIpDinamisTabelController::class, 'addData']);
        Route::post('/createData', [SetIpDinamisTabelController::class, 'createData'])->middleware('permission:create');
        Route::get('/editData/{id}', [SetIpDinamisTabelController::class, 'editData']);
        Route::post('/updateData/{id}', [SetIpDinamisTabelController::class, 'updateData'])->middleware('permission:update');
        Route::get('/detailData/{id}', [SetIpDinamisTabelController::class, 'detailData']);
        Route::get('/deleteData/{id}', [SetIpDinamisTabelController::class, 'deleteData']);
        Route::delete('/deleteData/{id}', [SetIpDinamisTabelController::class, 'deleteData'])->middleware('permission:delete');

        Route::get('/editSubMenuUtama/{id}', [SetIpDinamisTabelController::class, 'editSubMenuUtama']);
        Route::post('/updateSubMenuUtama/{id}', [SetIpDinamisTabelController::class, 'updateSubMenuUtama'])->middleware('permission:update');
        Route::get('/detailSubMenuUtama/{id}', [SetIpDinamisTabelController::class, 'detailSubMenuUtama']);
        Route::get('/deleteSubMenuUtama/{id}', [SetIpDinamisTabelController::class, 'deleteSubMenuUtama']);
        Route::delete('/deleteSubMenuUtama/{id}', [SetIpDinamisTabelController::class, 'deleteSubMenuUtama'])->middleware('permission:delete');

        Route::get('/editSubMenu/{id}', [SetIpDinamisTabelController::class, 'editSubMenu']);
        Route::post('/updateSubMenu/{id}', [SetIpDinamisTabelController::class, 'updateSubMenu'])->middleware('permission:update');
        Route::get('/detailSubMenu/{id}', [SetIpDinamisTabelController::class, 'detailSubMenu']);
        Route::get('/deleteSubMenu/{id}', [SetIpDinamisTabelController::class, 'deleteSubMenu']);
        Route::delete('/deleteSubMenu/{id}', [SetIpDinamisTabelController::class, 'deleteSubMenu'])->middleware('permission:delete');
    });

    // ❌ ROUTE TIDAK STANDAR - get-informasi-publik-* (Hanya view & download, tidak ada CRUD lengkap)
    Route::group(['prefix' => WebMenuModel::getDynamicMenuUrl('get-informasi-publik-informasi-berkala')], function () {
        Route::get('/', [GetIPInformasiBerkalaController::class, 'index'])->middleware('permission:view');
        Route::get('/download/{type}/{id}', [GetIPInformasiBerkalaController::class, 'downloadDocument'])->name('download.document');
        Route::get('/getData', [GetIPInformasiBerkalaController::class, 'getData']);
        Route::get('/view/{type}/{id}', [GetIPInformasiBerkalaController::class, 'viewDocument'])->name('view.document');
    });

    Route::group(['prefix' => WebMenuModel::getDynamicMenuUrl('get-informasi-publik-informasi-serta-merta')], function () {
        Route::get('/', [GetIPInformasiSertaMertaController::class, 'index'])->middleware('permission:view');
        Route::get('/download/{type}/{id}', [GetIPInformasiSertaMertaController::class, 'downloadDocument'])->name('download.document');
        Route::get('/getData', [GetIPInformasiSertaMertaController::class, 'getData']);
        Route::get('/view/{type}/{id}', [GetIPInformasiSertaMertaController::class, 'viewDocument'])->name('view.document');
    });

    Route::group(['prefix' => WebMenuModel::getDynamicMenuUrl('get-informasi-publik-informasi-setiap-saat')], function () {
        Route::get('/', [GetIPInformasiSetiapSaatController::class, 'index'])->middleware('permission:view');
        Route::get('/download/{type}/{id}', [GetIPInformasiSetiapSaatController::class, 'downloadDocument'])->name('download.document');
        Route::get('/getData', [GetIPInformasiSetiapSaatController::class, 'getData']);
        Route::get('/view/{type}/{id}', [GetIPInformasiSetiapSaatController::class, 'viewDocument'])->name('view.document');
    });

    // ❌ ROUTE TIDAK STANDAR - daftar-verifikasi-pengajuan (Nested structure dengan sub-prefix)
    Route::group(['prefix' => WebMenuModel::getDynamicMenuUrl('daftar-verifikasi-pengajuan')], function () {
        // Route utama - menampilkan index semua kategori Hasil/Review
        Route::get('/', [VerifPengajuanController::class, 'index'])->middleware('permission:view');
        
        // Route untuk Hasil/Review Permohonan Informasi
        Route::group(['prefix' => 'permohonan-informasi'], function() {
            Route::get('/', [VerifPIController::class, 'index'])->middleware('permission:view');
            Route::get('/approve-modal/{id}', [VerifPIController::class, 'getApproveModal'])->middleware('permission:update');
            Route::get('/decline-modal/{id}', [VerifPIController::class, 'getDeclineModal'])->middleware('permission:update');
            Route::post('/setujuiPermohonan/{id}', [VerifPIController::class, 'setujuiPermohonan'])->middleware('permission:update');
            Route::post('/tolakPermohonan/{id}', [VerifPIController::class, 'tolakPermohonan'])->middleware('permission:update');
            Route::post('/tandaiDibaca/{id}', [VerifPIController::class, 'tandaiDibaca'])->middleware('permission:update');
            Route::post('/hapusPermohonan/{id}', [VerifPIController::class, 'hapusPermohonan'])->middleware('permission:delete');
        });

        Route::group(['prefix' => 'pernyataan-keberatan'], function() {
            Route::get('/', [VerifPKController::class, 'index'])->middleware('permission:view');
            Route::get('/approve-modal/{id}', [VerifPKController::class, 'getApproveModal'])->middleware('permission:update');
            Route::get('/decline-modal/{id}', [VerifPKController::class, 'getDeclineModal'])->middleware('permission:update');
            Route::post('/setujuiPermohonan/{id}', [VerifPKController::class, 'setujuiPermohonan'])->middleware('permission:update');
            Route::post('/tolakPermohonan/{id}', [VerifPKController::class, 'tolakPermohonan'])->middleware('permission:update');
            Route::post('/tandaiDibaca/{id}', [VerifPKController::class, 'tandaiDibaca'])->middleware('permission:update');
            Route::post('/hapusPermohonan/{id}', [VerifPKController::class, 'hapusPermohonan'])->middleware('permission:delete');
        });

        Route::group(['prefix' => 'pengaduan-masyarakat'], function() {
            Route::get('/', [VerifPMController::class, 'index'])->middleware('permission:view');
            Route::get('/approve-modal/{id}', [VerifPMController::class, 'getApproveModal'])->middleware('permission:update');
            Route::get('/decline-modal/{id}', [VerifPMController::class, 'getDeclineModal'])->middleware('permission:update');
            Route::post('/setujuiPermohonan/{id}', [VerifPMController::class, 'setujuiPermohonan'])->middleware('permission:update');
            Route::post('/tolakPermohonan/{id}', [VerifPMController::class, 'tolakPermohonan'])->middleware('permission:update');
            Route::post('/tandaiDibaca/{id}', [VerifPMController::class, 'tandaiDibaca'])->middleware('permission:update');
            Route::post('/hapusPermohonan/{id}', [VerifPMController::class, 'hapusPermohonan'])->middleware('permission:delete');
        });

        Route::group(['prefix' => 'whistle-blowing-system'], function() {
            Route::get('/', [VerifWBSController::class, 'index'])->middleware('permission:view');
            Route::get('/approve-modal/{id}', [VerifWBSController::class, 'getApproveModal'])->middleware('permission:update');
            Route::get('/decline-modal/{id}', [VerifWBSController::class, 'getDeclineModal'])->middleware('permission:update');
            Route::post('/setujuiPermohonan/{id}', [VerifWBSController::class, 'setujuiPermohonan'])->middleware('permission:update');
            Route::post('/tolakPermohonan/{id}', [VerifWBSController::class, 'tolakPermohonan'])->middleware('permission:update');
            Route::post('/tandaiDibaca/{id}', [VerifWBSController::class, 'tandaiDibaca'])->middleware('permission:update');
            Route::post('/hapusPermohonan/{id}', [VerifWBSController::class, 'hapusPermohonan'])->middleware('permission:delete');
        });

        Route::group(['prefix' => 'permohonan-perawatan'], function() {
            Route::get('/', [VerifPPController::class, 'index'])->middleware('permission:view');
            Route::get('/approve-modal/{id}', [VerifPPController::class, 'getApproveModal'])->middleware('permission:update');
            Route::get('/decline-modal/{id}', [VerifPPController::class, 'getDeclineModal'])->middleware('permission:update');
            Route::post('/setujuiPermohonan/{id}', [VerifPPController::class, 'setujuiPermohonan'])->middleware('permission:update');
            Route::post('/tolakPermohonan/{id}', [VerifPPController::class, 'tolakPermohonan'])->middleware('permission:update');
            Route::post('/tandaiDibaca/{id}', [VerifPPController::class, 'tandaiDibaca'])->middleware('permission:update');
            Route::post('/hapusPermohonan/{id}', [VerifPPController::class, 'hapusPermohonan'])->middleware('permission:delete');
        });
    });

    // ❌ ROUTE TIDAK STANDAR - daftar-review-pengajuan (Nested structure dengan sub-prefix)
    Route::group(['prefix' => WebMenuModel::getDynamicMenuUrl('daftar-review-pengajuan')], function () {
        // Route utama - menampilkan index semua kategori Review
        Route::get('/', [ReviewPengajuanController::class, 'index'])->middleware('permission:view');
        
        // Route untuk Review Permohonan Informasi
        Route::group(['prefix' => 'permohonan-informasi'], function() {
            Route::get('/', [ReviewPIController::class, 'index'])->middleware('permission:view');
            Route::get('/approve-modal/{id}', [ReviewPIController::class, 'getApproveModal'])->middleware('permission:update');
            Route::get('/decline-modal/{id}', [ReviewPIController::class, 'getDeclineModal'])->middleware('permission:update');
            Route::post('/setujuiPermohonan/{id}', [ReviewPIController::class, 'setujuiReview'])->middleware('permission:update');
            Route::post('/tolakPermohonan/{id}', [ReviewPIController::class, 'tolakReview'])->middleware('permission:update');
            Route::post('/tandaiDibaca/{id}', [ReviewPIController::class, 'tandaiDibacaReview'])->middleware('permission:update');
            Route::delete('/hapusPermohonan/{id}', [ReviewPIController::class, 'hapusReview'])->middleware('permission:delete');
        });

        Route::group(['prefix' => 'pernyataan-keberatan'], function() {
            Route::get('/', [ReviewPKController::class, 'index'])->middleware('permission:view');
            Route::get('/approve-modal/{id}', [ReviewPKController::class, 'getApproveModal'])->middleware('permission:update');
            Route::get('/decline-modal/{id}', [ReviewPKController::class, 'getDeclineModal'])->middleware('permission:update');
            Route::post('/setujuiPermohonan/{id}', [ReviewPKController::class, 'setujuiReview'])->middleware('permission:update');
            Route::post('/tolakPermohonan/{id}', [ReviewPKController::class, 'tolakReview'])->middleware('permission:update');
            Route::post('/tandaiDibaca/{id}', [ReviewPKController::class, 'tandaiDibacaReview'])->middleware('permission:update');
            Route::delete('/hapusPermohonan/{id}', [ReviewPKController::class, 'hapusReview'])->middleware('permission:delete');
        });

        Route::group(['prefix' => 'pengaduan-masyarakat'], function() {
            Route::get('/', [ReviewPMController::class, 'index'])->middleware('permission:view');
            Route::get('/approve-modal/{id}', [ReviewPMController::class, 'getApproveModal'])->middleware('permission:update');
            Route::get('/decline-modal/{id}', [ReviewPMController::class, 'getDeclineModal'])->middleware('permission:update');
            Route::post('/setujuiPermohonan/{id}', [ReviewPMController::class, 'setujuiReview'])->middleware('permission:update');
            Route::post('/tolakPermohonan/{id}', [ReviewPMController::class, 'tolakReview'])->middleware('permission:update');
            Route::post('/tandaiDibaca/{id}', [ReviewPMController::class, 'tandaiDibacaReview'])->middleware('permission:update');
            Route::delete('/hapusPermohonan/{id}', [ReviewPMController::class, 'hapusReview'])->middleware('permission:delete');
        });

        Route::group(['prefix' => 'whistle-blowing-system'], function() {
            Route::get('/', [ReviewWBSController::class, 'index'])->middleware('permission:view');
            Route::get('/approve-modal/{id}', [ReviewWBSController::class, 'getApproveModal'])->middleware('permission:update');
            Route::get('/decline-modal/{id}', [ReviewWBSController::class, 'getDeclineModal'])->middleware('permission:update');
            Route::post('/setujuiPermohonan/{id}', [ReviewWBSController::class, 'setujuiReview'])->middleware('permission:update');
            Route::post('/tolakPermohonan/{id}', [ReviewWBSController::class, 'tolakReview'])->middleware('permission:update');
            Route::post('/tandaiDibaca/{id}', [ReviewWBSController::class, 'tandaiDibacaReview'])->middleware('permission:update');
            Route::delete('/hapusPermohonan/{id}', [ReviewWBSController::class, 'hapusReview'])->middleware('permission:delete');
        });

        Route::group(['prefix' => 'permohonan-perawatan'], function() {
            Route::get('/', [ReviewPPController::class, 'index'])->middleware('permission:view');
            Route::get('/approve-modal/{id}', [ReviewPPController::class, 'getApproveModal'])->middleware('permission:update');
            Route::get('/decline-modal/{id}', [ReviewPPController::class, 'getDeclineModal'])->middleware('permission:update');
            Route::post('/setujuiPermohonan/{id}', [ReviewPPController::class, 'setujuiReview'])->middleware('permission:update');
            Route::post('/tolakPermohonan/{id}', [ReviewPPController::class, 'tolakReview'])->middleware('permission:update');
            Route::post('/tandaiDibaca/{id}', [ReviewPPController::class, 'tandaiDibacaReview'])->middleware('permission:update');
            Route::delete('/hapusPermohonan/{id}', [ReviewPPController::class, 'hapusReview'])->middleware('permission:delete');
        });
    });

    // ❌ ROUTE TIDAK STANDAR - whatsapp-management (9 standar + 8 extra functions)
    Route::group(['prefix' => WebMenuModel::getDynamicMenuUrl('whatsapp-management')], function (){
        Route::get('/', [WhatsAppController::class, 'index'])->middleware('permission:view');
        Route::post('/start', [WhatsAppController::class, 'startServer'])->middleware('permission:create');
        Route::post('/stop', [WhatsAppController::class, 'stopServer'])->middleware('permission:update');
        Route::post('/reset', [WhatsAppController::class, 'resetSession'])->middleware('permission:update');
        Route::get('/status', [WhatsAppController::class, 'getStatus']);
        Route::get('/qr-code', [WhatsAppController::class, 'getQRCode']);
        // Route::post('/save-qrcode-log', [WhatsAppController::class, 'saveQRCodeLog'])->middleware('permission:create');
        Route::get('/qrcode-status', [WhatsAppController::class, 'getQRCodeStatus']);
        Route::post('/auto-save-qrcode-log', [WhatsAppController::class, 'autoSaveQRCodeLog']); // Route baru untuk auto save
        Route::get('/connected-phone', [WhatsAppController::class, 'getConnectedPhone']);
        Route::post('/trigger-scan-log', [WhatsAppController::class, 'triggerScanLog']);
        Route::post('/confirm-scan-log', [WhatsAppController::class, 'confirmScanLog'])->middleware('permission:create'); // Route baru
        Route::post('/reset-expired-scan', [WhatsAppController::class, 'resetExpiredScan'])->middleware('permission:update'); // Route baru
    });

    // Route::group(['prefix' => WebMenuModel::getDynamicMenuUrl('views-riwayat-pengajuan')], function (){
    //     Route::get('/', [WhatsAppController::class, 'index'])->middleware('permission:view');
    // });

    // =======================================
    // 🚀 DYNAMIC ROUTING - REFACTORED ROUTES (48 Routes → 3 Dynamic Patterns)
    // =======================================
    // Handler: Modules\Sisfo\App\Http\Controllers\PageController
    // Filter: App\Helpers\RouteHelper (exclude User module & Non-standard Sisfo URLs)
    // Middleware: check.dynamic.route (validate URL sebelum masuk PageController)
    // 
    // ✅ Route yang DI-HANDLE oleh Dynamic Routing (48 URLs):
    // - kategori-footer, detail-footer, kategori-akses-cepat, detail-akses-cepat
    // - kategori-berita, detail-berita, kategori-media, detail-media
    // - kategori-tahun-lhkpn, detail-lhkpn, kategori-pintasan-lainnya, detail-pintasan-lainnya
    // - regulasi-dinamis, detail-regulasi, kategori-regulasi
    // - permohonan-informasi-admin, pernyataan-keberatan-admin
    // - pengaduan-masyarakat-admin, whistle-blowing-system-admin
    // - permohonan-sarana-dan-prasarana-admin
    // - timeline, ketentuan-pelaporan, kategori-form
    // - kategori-pengumuman, detail-pengumuman
    // - management-level, management-user
    // - kategori-informasi-publik-dinamis-tabel
    // - dinamis-konten, upload-detail-konten
    // - management-menu-url, management-menu-global
    // - layanan-informasi-Dinamis, layanan-informasi-upload
    // - penyelesaian-sengketa, upload-penyelesaian-sengketa
    //
    // ❌ Route yang TIDAK DI-HANDLE (Defined by RouteHelper):
    // 1. User Module URLs (40+ URLs) - See RouteHelper::$userModuleUrls
    //    - beranda, berita, pengumuman, lhkpn, form-*, profile-*, regulasi, dll
    // 2. Non-Standard Sisfo URLs (8 URLs) - See RouteHelper::$nonStandardSisfoUrls
    //    - menu-management, set-informasi-publik-dinamis-tabel
    //    - get-informasi-publik-*, daftar-verifikasi-pengajuan
    //    - daftar-review-pengajuan, whatsapp-management
    // =======================================

    // Pattern 1: /{page} → index() atau store() tergantung HTTP method
    Route::match(['GET', 'POST'], '/{page}', [PageController::class, 'index'])
        ->middleware('check.dynamic.route')
        ->where('page', \App\Helpers\RouteHelper::getDynamicRoutePattern());

    // Pattern 2: /{page}/{action} → getData(), addData(), editData/{id}, dll
    Route::match(['GET', 'POST'], '/{page}/{action}', [PageController::class, 'index'])
        ->middleware('check.dynamic.route')
        ->where('page', \App\Helpers\RouteHelper::getDynamicRoutePattern())
        ->where('action', '[a-zA-Z0-9\-]+');

    // Pattern 3: /{page}/{action}/{id} → editData/123, updateData/123, deleteData/123
    Route::match(['GET', 'POST', 'DELETE'], '/{page}/{action}/{id}', [PageController::class, 'index'])
        ->middleware('check.dynamic.route')
        ->where('page', \App\Helpers\RouteHelper::getDynamicRoutePattern())
        ->where('action', '[a-zA-Z0-9\-]+')
        ->where('id', '[0-9]+');

    // =======================================
    // 📊 ROUTE SUMMARY:
    // - Total Original Routes: 700+ lines
    // - Routes Refactored: 48 route groups → 3 dynamic patterns
    // - Routes Kept (Non-standard): 8 route groups (defined in RouteHelper)
    // - Lines Reduced: ~600 lines (84% reduction)
    // - URL Management: Centralized in App\Helpers\RouteHelper
    // =======================================

    // 🔴 CATATAN PENTING:
    // - Daftar URL yang di-exclude ada di: App\Helpers\RouteHelper
    // - User Module URLs: RouteHelper::$userModuleUrls (40+ URLs)
    // - Non-Standard Sisfo URLs: RouteHelper::$nonStandardSisfoUrls (8 URLs)
    // - Middleware check.dynamic.route akan validate sebelum masuk PageController
    // 
    // Untuk menambah URL exception, edit file:
    // app/Helpers/RouteHelper.php
});