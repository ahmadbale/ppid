
<?php

use Modules\Sisfo\App\Http\Controllers\Api\ApiAuthController;
use Illuminate\Support\Facades\Route;
use Modules\Sisfo\App\Http\Controllers\Api\Auth\AuthMenuController;
use Modules\Sisfo\App\Http\Controllers\Api\Auth\BeritaPengumumanController;
use Modules\Sisfo\App\Http\Controllers\Api\Public\PublicMenuController;

// Grup route untuk autentikasi
Route::prefix('auth')->group(function () {
    // Route publik (tidak perlu autentikasi)
    Route::post('masuk', [ApiAuthController::class, 'login']);
    Route::post('daftar', [ApiAuthController::class, 'register']);
    
    // Route terproteksi (perlu autentikasi)
    Route::middleware('auth:api')->group(function () {
        Route::post('keluar', [ApiAuthController::class, 'logout']);
        Route::get('pengguna', [ApiAuthController::class, 'getData']);
        Route::get('menu', [AuthMenuController::class, 'getAuthMenus']);
        Route::get('berita-pengumuman', [BeritaPengumumanController::class, 'getBeritaPengumuman']);
    });
});

// Route publik
Route::prefix('publik')->group(function () {
    Route::get('menu', [PublicMenuController::class, 'getPublicMenus']);
});