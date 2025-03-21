
<?php

use Spatie\FlareClient\Api;
use Illuminate\Support\Facades\Route;


// use App\Http\Controllers\Api\ApiAuthController;
// use App\Http\Controllers\Api\Auth\AuthMenuController;
// use App\Http\Controllers\Api\Auth\ApiFooterController;
// use App\Http\Controllers\Api\Public\PublicMenuController;
// use App\Http\Controllers\Api\Public\ApiAksesCepatController;
// use App\Http\Controllers\Api\Auth\BeritaPengumumanController;

use Modules\Sisfo\App\Http\Controllers\Api\Public\ApiFooterController;
use Modules\Sisfo\App\Http\Controllers\Api\Public\ApiPintasanLainnyaController;
use Modules\Sisfo\App\Http\Controllers\Api\Public\ApiAksesCepatController;
use Modules\Sisfo\App\Http\Controllers\Api\Public\ApiLhkpnController;
use Modules\Sisfo\App\Http\Controllers\Api\ApiAuthController;
use Modules\Sisfo\App\Http\Controllers\Api\Auth\AuthMenuController;
use Modules\Sisfo\App\Http\Controllers\Api\Public\PublicMenuController;
use Modules\Sisfo\App\Http\Controllers\Api\Auth\BeritaPengumumanController;


use Modules\Sisfo\App\Http\Controllers\Api\ApiAuthController;
use Modules\Sisfo\App\Http\Controllers\Api\Auth\GetMenuController;
use Modules\Sisfo\App\Http\Controllers\Api\Auth\AuthMenuController;
use Modules\Sisfo\App\Http\Controllers\Api\Public\ApiFooterController;

use Modules\Sisfo\App\Http\Controllers\Api\Public\PublicMenuController;
use Modules\Sisfo\App\Http\Controllers\Api\Auth\BeritaPengumumanController;
use Modules\Sisfo\App\Http\Controllers\Api\Auth\GetBeritaPengumumanController;
use Modules\Sisfo\App\Http\Controllers\Api\Public\ApiLhkpnController;

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

        Route::get('menus', [AuthMenuController::class, 'getAuthMenus']);
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
    Route::get('menu', [PublicMenuController::class, 'getPublicMenus']);
    Route::post('logout', [ApiAuthController::class, 'logout']);
    Route::get('user', [ApiAuthController::class, 'getData']);
    Route::get('menus', [AuthMenuController::class, 'getAuthMenus']);
    Route::get('berita-pengumuman', [BeritaPengumumanController::class, 'getBeritaPengumuman']);
    Route::get('getDataFooter', [ApiFooterController::class, 'getDataFooter']);
    Route::post('refresh-token', [ApiAuthController::class, 'refreshToken']);
    Route::get('getDataLhkpn', [ApiLhkpnController::class, 'getDataLhkpn']);
    Route::get('getDataPintasanLainnya', [ApiPintasanLainnyaController::class, 'getDataPintasanLainnya']);
    Route::get('getDataAksesCepat',[ApiAksesCepatController::class,'getDataAksesCepat']);
    Route::get('getMenu', [AuthMenuController::class, 'getMenu']);
    Route::get('getBeritaPengumuman', [BeritaPengumumanController::class, 'getBeritaPengumuman']);
    Route::get('getDataFooter', [ApiFooterController::class, 'getDataFooter']);
    Route::get('getDataLhkpn', [ApiLhkpnController::class, 'getDataLhkpn']);

});