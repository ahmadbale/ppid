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
        View::composer('user::layout.footer', function ($view) {
            $footerController = new FooterController();
            $footerData = $footerController->getFooterData();
            
            $viewData = [
                'headerData' => $footerData['headerData'] ?? null,
                'links' => $footerData['links'] ?? [],
                'offlineInfo' => $footerData['offlineInfo'] ?? null,
                'contactInfo' => $footerData['contactInfo'] ?? [],
                'socialIcons' => $footerData['socialIcons'] ?? []
            ];

            Log::info('Footer Data Sent to View', [
                'headerData' => $viewData['headerData'],
                'links_count' => count($viewData['links']),
                'offlineInfo' => $viewData['offlineInfo'],
                'contactInfo_count' => count($viewData['contactInfo']),
                'socialIcons_count' => count($viewData['socialIcons'])
            ]);

            $view->with($viewData);
        });
    }
}