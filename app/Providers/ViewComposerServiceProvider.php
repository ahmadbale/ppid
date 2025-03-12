<?php
namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Services\ApiService;
use Illuminate\Support\Facades\Log;

class ViewComposerServiceProvider extends ServiceProvider
{
    public function boot()
    {
        View::composer('user::layouts.footer', function ($view) {
            try {
                $footerResponse = ApiService::get('/auth/footerData');
    
                if (!$footerResponse || !isset($footerResponse['data'])) {
                    $footerData = $this->getDefaultFooterData();
                } else {
                    $headerData = null;
                    $links = [];
                    $offlineInfo = null;
                    $contactInfo = [];
                    $socialIcons = [];
        
                    foreach ($footerResponse['data'] as $kategori) {
                        switch ($kategori['kategori_kode'] ?? '') {
                            case 'KPP': // Kantor PPID
                                $headerData = [
                                    'kategori_nama' => $kategori['kategori_nama'] ?? 'Kantor PPID Politeknik Negeri Malang',
                                    'items' => $kategori['items'] ?? [],
                                    'icon' => $kategori['items'][0]['icon'] ?? null
                                ];
                                break;
                            case 'PUL': // Pusat Unit Layanan
                                $links = [
                                    [
                                        'title' => $kategori['kategori_nama'] ?? 'Pusat Unit Layanan',
                                        'menu' => array_map(function($item) {
                                            return [
                                                'name' => $item['judul'] ?? 'Layanan',
                                                'route' => $item['url'] ?? '#'
                                            ];
                                        }, $kategori['items'] ?? [])
                                    ]
                                ];
                                break;
                            case 'LIO': // Layanan Informasi Offline
                                $offlineInfo = $kategori['items'][0] ?? null;
                                break;
                            case 'HKI': // Hubungi Kami
                                $contactInfo = array_map(function($contact) {
                                    return [
                                        'type' => strpos($contact['judul'] ?? '', '@') !== false ? 'email' : 'phone',
                                        'value' => $contact['judul'] ?? ''
                                    ];
                                }, $kategori['items'] ?? []);
                                break;
                            case 'KIG': // Khusus Icon atau Gambar
                                $socialIcons = array_map(function($icon) {
                                    return [
                                        'logo' => $icon['icon'] ?? '',
                                        'title' => $icon['judul'] ?? 'Social Media',
                                        'route' => $icon['url'] ?? '#'
                                    ];
                                }, $kategori['items'] ?? []);
                                break;
                        }
                    }
        
                    $footerData = [
                        'headerData' => $headerData,
                        'links' => $links,
                        'offlineInfo' => $offlineInfo,
                        'contactInfo' => $contactInfo,
                        'socialIcons' => $socialIcons
                    ];
                }
    
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
                
            } catch (\Exception $e) {
                Log::error('Footer Data Fetch Error', [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                
                $defaultData = $this->getDefaultFooterData();
                $view->with($defaultData);
            }
        });
    }
    
    private function getDefaultFooterData()
    {
        return [
            'headerData' => [
                'kategori_nama' => 'Kantor PPID Politeknik Negeri Malang',
                'items' => [
                    ['judul' => 'Jl. Soekarno Hatta No.9, Jatimulyo, Lowokwaru, Kota Malang, Jawa Timur 65141']
                ],
                'icon' => null
            ],
            'links' => [],
            'offlineInfo' => null,
            'contactInfo' => [],
            'socialIcons' => []
        ];
    }
}

// namespace App\Providers;

// use Illuminate\Support\ServiceProvider;
// use Illuminate\Support\Facades\View;

// class ViewComposerServiceProvider extends ServiceProvider
// {
//     public function boot()
//     {
//         View::composer('user::layouts.footer', function ($view) {
//             $links = [
//                 [
//                     'title' => 'Pusat Unit Layanan',
//                     'menu' => [
//                         ['name' => 'Jaminan Mutu', 'route' => '#'],
//                         ['name' => 'Perpustakaan', 'route' => 'https://library.polinema.ac.id/'],
//                         ['name' => 'UPA TIK', 'route' => 'https://sipuskom.polinema.ac.id/'],
//                         ['name' => 'P2M', 'route' => '#'],
//                     ]
//                 ]
//             ];

//             $icons = [
//                 [
//                     'logo-polinema' => asset('img/logo-polinema.svg'),
//                     'logo-blu' => asset('img/logo-blu.svg')
//                 ]
//             ];

//             $iconsosmed = [
//                 [
//                     'logo' => asset('img/logo-twitter.svg'),
//                     'route' => '#'
//                 ],
//                 [
//                     'logo' => asset('img/logo-facebook.svg'),
//                     'route' => '#'
//                 ],
//                 [
//                     'logo' => asset('img/logo-instagram.svg'),
//                     'route' => '#'
//                 ],
//                 [
//                     'logo' => asset('img/logo-youtube.svg'),
//                     'route' => '#'
//                 ]
//             ];

//             $view->with(compact('links', 'icons', 'iconsosmed'));
//         });
//     }

// }

// namespace App\Providers;

// use Illuminate\Support\Facades\View;
// use Illuminate\Support\ServiceProvider;
// use Modules\User\App\Http\Controllers\FooterController;
// use Illuminate\Support\Facades\Log;

// class ViewComposerServiceProvider extends ServiceProvider
// {
//     public function boot()
//     {
//         View::composer('user::layout.footer', function ($view) {
//             $footerController = new FooterController();
//             $footerData = $footerController->getFooterData();
            
//             $viewData = [
//                 'headerData' => $footerData['headerData'] ?? null,
//                 'links' => $footerData['links'] ?? [],
//                 'offlineInfo' => $footerData['offlineInfo'] ?? null,
//                 'contactInfo' => $footerData['contactInfo'] ?? [],
//                 'socialIcons' => $footerData['socialIcons'] ?? []
//             ];

//             Log::info('Footer Data Sent to View', [
//                 'headerData' => $viewData['headerData'],
//                 'links_count' => count($viewData['links']),
//                 'offlineInfo' => $viewData['offlineInfo'],
//                 'contactInfo_count' => count($viewData['contactInfo']),
//                 'socialIcons_count' => count($viewData['socialIcons'])
//             ]);

//             $view->with($viewData);
//         });
//     }
// }