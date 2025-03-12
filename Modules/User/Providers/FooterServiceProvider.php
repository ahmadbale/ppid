<?php
namespace Modules\User\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Modules\User\App\Http\Controllers\FooterController;
use Illuminate\Support\Facades\Log;

class FooterServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Pastikan path view sesuai dengan yang ada di master.blade.php
        View::composer('user::layout.footer', function ($view) {
            try {
                $footerController = new FooterController();
                $footerData = $footerController->getFooterData();
                
                Log::info('Footer Data Sent to View', [
                    'headerData' => !empty($footerData['headerData']),
                    'links' => !empty($footerData['links']),
                    'offlineInfo' => !empty($footerData['offlineInfo']),
                    'contactInfo' => count($footerData['contactInfo'] ?? []),
                    'socialIcons' => count($footerData['socialIcons'] ?? [])
                ]);

                $view->with($footerData);
            } catch (\Exception $e) {
                Log::error('Error in FooterServiceProvider', [
                    'message' => $e->getMessage()
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