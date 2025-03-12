<?php
namespace Modules\User\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Modules\User\App\Http\Controllers\FooterController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class FooterServiceProvider extends ServiceProvider
{
    public function boot()
    {
        
        // Pastikan path view sesuai dengan yang ada di master.blade.php
        View::composer('user::layout.footer', function ($view) {
            try {
                // Log token status untuk debugging
                $hasToken = !empty(Session::get('api_token'));
                Log::info('Footer View Composer', [
                    'has_token' => $hasToken
                ]);

                // Ambil data footer
                $footerController = new FooterController();
                $footerData = $footerController->getFooterData();
                
                // Log data yang akan dikirim ke view
                Log::info('Footer Data Sent to View', [
                    'headerData' => !empty($footerData['headerData']),
                    'links' => !empty($footerData['links']),
                    'offlineInfo' => !empty($footerData['offlineInfo']),
                    'contactInfo' => count($footerData['contactInfo'] ?? []),
                    'socialIcons' => count($footerData['socialIcons'] ?? [])
                ]);

                // Kirim data ke view
                $view->with($footerData);
            } catch (\Exception $e) {
                Log::error('Error in FooterServiceProvider', [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                
                // Mengirim data kosong jika ada error
                $view->with([
                    'headerData' => null,
                    'links' => null,
                    'offlineInfo' => null,
                    'contactInfo' => [],
                    'socialIcons' => []
                ]);
            }
        });
    }
}