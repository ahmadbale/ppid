
<?php

use Illuminate\Support\Facades\Route;
use Modules\User\App\Http\Controllers\EFormController;
use Modules\User\App\Http\Controllers\PermohonanController;
use Modules\User\App\Http\Controllers\InformasiPublikController;
use Modules\User\App\Http\Controllers\HomeController;
use Modules\User\App\Http\Controllers\LhkpnController;
use Modules\User\App\Http\Controllers\UserController;
use Modules\User\App\Http\Controllers\FooterController;
use Modules\User\App\Http\Controllers\TestController;
use Modules\User\App\Http\Controllers\TimelineController;
use Modules\User\App\Http\Controllers\BeritaController;
use Modules\User\App\Http\Controllers\Form\InformasiController;
use Modules\User\App\Http\Controllers\Form\KeberatanController;
use Modules\User\App\Http\Controllers\Form\WBSController;
use Modules\User\App\Http\Controllers\Form\PengaduanMasyarakatController;


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

Route::group([], function () {
    Route::resource('user', UserController::class)->names('user');
});


Route::get('/', [HomeController::class, 'index'])->name('beranda');

Route::get('/landing_page', [HomeController::class, 'index']);


// Route::get('/footer', [FooterController::class, 'index']);


// Form Controller ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
Route::prefix('form-permohonan-informasi')->group(function () {
    Route::get('/', [InformasiController::class, 'index'])->name('form-informasi-publik');
});
Route::prefix('form-pernyataan-keberatan')->group(function () {
    Route::get('/', [KeberatanController::class, 'index'])->name('form-keberatan');
});
Route::prefix('form-whistle-blowing')->group(function () {
    Route::get('/', [WBSController::class, 'index'])->name('form-wbs');
});
Route::prefix('form-pengaduan-masyarakat')->group(function () {
    Route::get('/', [PengaduanMasyarakatController::class, 'index'])->name('form-aduanmasyarakat');
});
// ---- form dinamis untuk self made new form -----


// Timeline Controller ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
Route::get('/permohonan-informasi', [TimelineController::class, 'permohonan_informasi'])->name('permohonan_informasi');
Route::get('/pernyataan-keberatan', [TimelineController::class, 'pernyataan_keberatan'])->name('pernyataan_keberatan');
Route::get('/whistle-blowing-system', [TimelineController::class, 'wbs'])->name('wbs');
Route::get('/pengaduan-masyarakat', [TimelineController::class, 'pengaduan_masyarakat'])->name('pengaduan_masyarakat');

// Profil Page ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
Route::get('/profil', function () {
    return view('user::profil.profil');})->name('profil');
Route::get('/profil-polinema', function () {
    return view('user::profil.ppolinema');})->name('ppolinema');
Route::get('/profil/dasar-hukum', function () {
    return view('user::profil.dasarhukum');})->name('dasar_hukum');
Route::get('/profil/maklumat-ppid', function () {
    return view('user::profil.Maklumatppid');})->name('maklumat_ppid');
Route::get('/profil/struktur-organisasi', function () {
    return view('user::profil.SO');})->name('struktur_organisasi');
Route::get('/profil/tugas-fungsi', function () {
    return view('user::profil.tugasfungsi');})->name('tugas_fungsi');


// SOP Controller
// ~~~ soon ~~~

//  Berita Controller ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
Route::get('/berita', [BeritaController::class, 'index'])->name('berita');
Route::get('/berita-1', [BeritaController::class, 'detail'])->name('berita-1');


// Informasi Publik ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
Route::get('/LHKPN', [LhkpnController::class, 'getDataLhkpn'])->name('LHKPN');

    

// Page Dinamis with
Route::get('/content-dinamis', function () {
    return view('user::content');
})->name('content');


Route::get('/e-form_keberatan', function () {
    return view('user::e-form_keberatan');
});

Route::get('/e-form_wbs', function () {
    return view('user::e-form_wbs');
});

// Route::get('/login-ppid', function () {
//     return view('user::login');
// }) ->name('login');

Route::get('/register', function () {
    return view('user::register');
}) ->name('register');


Route::prefix('informasi-publik')->group(function () {
    Route::get('/setiap-saat', [InformasiPublikController::class, 'setiapSaat'])->name('informasi-publik.setiap-saat');
    Route::get('/berkala', [InformasiPublikController::class, 'berkala'])->name('informasi-publik.berkala');
    Route::get('/serta-merta', [InformasiPublikController::class, 'sertaMerta'])->name('informasi-publik.serta-merta');
});

Route::get('/permohonan/lacak', [PermohonanController::class, 'lacak'])->name('permohonan.lacak');

// route kemarin tanggal 11 maret
// Route::get('/login-ppid', [UserController::class, 'showLoginForm']);
// Route::post('/login', [UserController::class, 'login']);
// Route::post('/logout', [UserController::class, 'logout'])->name('logout');
// Route::get('/footer-data', [FooterController::class, 'index']);
// Route::get('/', [TestController::class, 'getData']);
// Route::get('/footer', function () {
//     $footerController = new FooterController();
//     $footerData = $footerController->getFooterData();

//     return view('user::layouts.footer', $footerData);
// });

// tanggal 12 maret
Route::get('/login-ppid', [UserController::class, 'showLoginForm'])->name('login-ppid');
Route::post('/login', [UserController::class, 'login'])->name('login');
// Routes yang memerlukan autentikasi 12 maret
Route::middleware(['token'])->group(function () {
    Route::post('/logout', [UserController::class, 'logout'])->name('logout');
    // Route::get('/', [TestController::class, 'getData']);
    // Rute lain yang memerlukan autentikasi
});

// Route untuk dashboard berdasarkan level
Route::get('/dashboardSAR', function () {
    $activeMenu = 'dashboard'; // Sesuaikan dengan kebutuhan Anda
    $breadcrumb = (object) [
        'title' => 'Selamat Datang Super Administrator',
        'list' => ['Home', 'welcome']
    ];
    return view('sisfo::dashboardSAR', compact('activeMenu', 'breadcrumb'));
})->name('dashboard.sar');

Route::get('/dashboardADM', function () {
    $activeMenu = 'dashboard';
    $breadcrumb = (object) [
        'title' => 'Selamat Datang Administrator',
        'list' => ['Home', 'welcome']
    ];
    return view('sisfo::dashboardADM', compact('activeMenu', 'breadcrumb'));
})->name('dashboard.adm');

Route::get('/dashboardMPU', function () {
    $activeMenu = 'dashboard';
    $breadcrumb = (object) [
        'title' => 'Selamat Datang Super Manajemen dan Pimpinan Unit',
        'list' => ['Home', 'welcome']
    ];
    return view('sisfo::dashboardMPU', compact('activeMenu', 'breadcrumb'));
})->name('dashboard.mpu');

Route::get('/dashboardVFR', function () {
    $activeMenu = 'dashboard';
    $breadcrumb = (object) [
        'title' => 'Selamat Datang Super Verifikator',
        'list' => ['Home', 'welcome']
    ];
    return view('sisfo::dashboardVFR', compact('activeMenu', 'breadcrumb'));
})->name('dashboard.vfr');

Route::get('/dashboardRPN', function () {
    $activeMenu = 'dashboard';
    $breadcrumb = (object) [
        'title' => 'Selamat Datang Super Responden',
        'list' => ['Home', 'welcome']
    ];
    return view('sisfo::dashboardRPN', compact('activeMenu', 'breadcrumb'));
})->name('dashboard.rpn');
