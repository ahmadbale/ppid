<?php

namespace Modules\User\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\User\App\Services\ApiService;
use Illuminate\Support\Facades\Log;

class FooterController extends Controller
{
    public function getFooterData()
    {
        try {
            $footerResponse = ApiService::get('/auth/footerData');

            if (!$footerResponse || !isset($footerResponse['data'])) {
                return $this->getDefaultFooterData();
            }

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
                            $links = array_map(function($item) {
                                return [
                                    'name' => $item['judul'] ?? 'Layanan',
                                    'route' => $item['url'] ?? '#'
                                ];
                            }, $kategori['items'] ?? []);
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

            return [
                'headerData' => $headerData,
                'links' => $links,
                'offlineInfo' => $offlineInfo,
                'contactInfo' => $contactInfo,
                'socialIcons' => $socialIcons
            ];

        } catch (\Exception $e) {
            Log::error('Footer Data Fetch Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $this->getDefaultFooterData();
        }
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