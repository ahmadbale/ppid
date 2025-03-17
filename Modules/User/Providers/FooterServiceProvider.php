<?php

namespace Modules\User\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;

class FooterServiceProvider extends ServiceProvider
{

  
    public function boot()
    {
        Log::info('FooterServiceProvider: Boot method dipanggil');
        View::composer('user::layouts.footer', function ($view) {
            try {
                Log::info('Memulai proses pengambilan data footer');

                $footerData = $this->getFooterData();
                Log::info('Data Footer berhasil diperoleh', ['footerData' => $footerData]);

                $view->with($footerData);
            } catch (\Exception $e) {
                Log::error('Error in FooterServiceProvider', ['message' => $e->getMessage()]);
                $view->with($this->getDefaultFooterData());
            }
        });
    }

    /**
     * Mengambil data footer dari API publik tanpa autentikasi
     */
    private function getFooterData()
    {
        try {
            // Panggil API langsung tanpa autentikasi
            Log::info('Mengambil data footer dari API publik');
            $response = Http::get('http://ppid-polinema.test/api/public/getDataFooter');

            if ($response->failed() || !$response->json('success')) {
                Log::warning('Footer API gagal diambil atau data tidak lengkap', [
                    'response' => $response->json() ?? 'Tidak ada response'
                ]);
                return $this->getDefaultFooterData();
            }
            
            Log::info('FooterServiceProvider: Response API', ['response' => $response->json()]);
            return $this->processFooterData($response->json('data'));
        } catch (\Exception $e) {
            Log::error('Footer Data Fetch Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $this->getDefaultFooterData();
        }
    }

    /**
     * Memproses data footer dari API
     */
    private function processFooterData($data)
    {
        $result = [
            'headerData' => null,
            'links' => [],
            'offlineInfo' => [],
            'contactInfo' => [],
            'socialIcons' => []
        ];
    
        foreach ($data as $item) {
            switch ($item['kategori_kode']) {
                case 'KPP': // Kantor PPID
                    $result['headerData'] = [
                        'kategori_nama' => $item['kategori_nama'],
                        'items' => $item['items']
                    ];
                    break;
                
                case 'PUL': // Pusat Unit Layanan
                    $result['links'] = [
                        'title' => $item['kategori_nama'],
                        'menu' => array_map(function($menuItem) {
                            return [
                                'name' => $menuItem['judul'],
                                'route' => $menuItem['url'] ?? '#'
                            ];
                        }, $item['items'])
                    ];
                    break;
                
                case 'LIO': // Layanan Informasi Offline
                    $result['offlineInfo'] = array_map(function ($offlineItem) {
                        return [
                            'title' => $offlineItem['judul'],
                        ];
                    }, $item['items']);
                    break;
                
                case 'HKI': // Hubungi Kami
                    $result['contactInfo'] = array_map(function ($contactItem) {
                        return [
                            'info' => $contactItem['judul']
                        ];
                    }, $item['items']);
                    break;
                
                case 'KIG': // Ikon Media Sosial
                    $result['socialIcons'] = array_map(function($icon) {
                        return [
                            'logo' => $icon['icon'] ?? null,
                            'title' => $icon['judul'],
                            'route' => $icon['url'] ?? '#'
                        ];
                    }, $item['items']);
                    break;
            }
        }
    
        return $result;
    }
    
    /**
     * Data default jika API gagal
     */
    private function getDefaultFooterData()
    {
        return [
            'headerData' => null,
            'links' => [],
            'offlineInfo' => null,
            'contactInfo' => [],
            'socialIcons' => []
        ];
    }
}