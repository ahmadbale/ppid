<?php

namespace Modules\User\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\User\Services\ApiService;
use Illuminate\Support\Facades\Log;

class FooterController extends Controller
{
    public function getData()
    {
        try {
            $footerResponse = ApiService::get('/auth/footerData');
            if (!$footerResponse || !isset($footerResponse['data']) || !$footerResponse['success']) {
                Log::warning('Footer API gagal diambil atau data tidak lengkap');
                return [
                    'headerData' => null,
                    'links' => null,
                    'offlineInfo' => null,
                    'contactInfo' => [],
                    'socialIcons' => []
                ];
            }

            $data = $footerResponse['data'];
            $result = [
                'headerData' => null,
                'links' => null,
                'offlineInfo' => null,
                'contactInfo' => [],
                'socialIcons' => []
            ];
          

            // Memproses data dari API sesuai dengan kategori yang ada
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
                        $result['offlineInfo'] = $item['items'][0] ?? null;
                        break;
                    
                    case 'HKI': // Hubungi Kami
                        $result['contactInfo'] = $item['items'];
                        break;
                    
                    case 'KIG': // Khusus Icon atau Gambar (Social Media)
                        $result['socialIcons'] = array_map(function($icon) {
                            return [
                                'logo' => $icon['icon'],
                                'title' => $icon['judul'],
                                'route' => $icon['url'] ?? '#'
                            ];
                        }, $item['items']);
                        break;
                }
            }

            Log::info('Data footer berhasil diproses dari API', [
                'kategori_count' => count($data)
            ]);

            return $result;

        } catch (\Exception $e) {
            Log::error('Footer Data Fetch Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Mengembalikan data kosong, bukan default
            return [
                'headerData' => null,
                'links' => null,
                'offlineInfo' => null,
                'contactInfo' => [],
                'socialIcons' => []
            ];
        }
    }
}