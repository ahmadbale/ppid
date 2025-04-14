<?php

namespace Modules\User\App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function index()
    {
        try {
            Log::info('Mengambil data dari API');

            // Ambil data pintasan
            $pintasanResponse = Http::get('http://ppid-polinema.test/api/public/getDataPintasanLainnya');
            $pintasanMenus = $this->fetchPintasanData($pintasanResponse);

            // Ambil data akses cepat
            $aksesCepatResponse = Http::get('http://ppid-polinema.test/api/public/getDataAksesCepat');
            $aksesCepatMenus = $this->fetchAksesCepatData($aksesCepatResponse);

            $pengumumanResponse = Http::get('http://ppid-polinema.test/api/public/getDataPengumumanLandingPage');
            $pengumumanMenus = $this->fetchPengumumanData($pengumumanResponse);

            $beritaResponse = Http::get('http://ppid-polinema.test/api/public/getDataBeritaLandingPage');
            $beritaMenus = $this->fetchBeritaData($beritaResponse);

            $heroSectionResponse = Http::get('http://ppid-polinema.test/api/public/getDataHeroSection');
            $heroSectionMenus = $this->fetchHeroSectionData($heroSectionResponse);

            $dokumentasiResponse = Http::get('http://ppid-polinema.test/api/public/getDataDokumentasi');
            $dokumentasiMenus = $this->fetchDokumentasiData($dokumentasiResponse);

            $mediaInformasiPublikResponse = Http::get('http://ppid-polinema.test/api/public/getDataMediaInformasiPublik');
            $mediaInformasiPublikMenus = $this->fetchMediaInformasiPublikData($mediaInformasiPublikResponse);

            return view('user::landing_page', compact(
                'pintasanMenus',
                'aksesCepatMenus',
                'pengumumanMenus',
                'beritaMenus',
                'heroSectionMenus',
                'dokumentasiMenus',
                'mediaInformasiPublikMenus'
            ));
        } catch (\Exception $e) {
            Log::error('Error saat mengambil data dari API', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return view('user::landing_page', [
                'pintasanMenus' => [],
                'aksesCepatMenus' => [],
                'pengumumanMenus' => [],
                'beritaMenus' => [],
                'heroSectionMenus' => [],
                'dokumentasiMenus' => [],
                'mediaInformasiPublikMenus' => [],
            ]);
        }
    }

    private function fetchPintasanData($response)
    {
        if ($response->failed() || !$response->json('success')) {
            Log::warning('API Pintasan gagal atau data tidak lengkap', [
                'response' => $response->json() ?? 'Tidak ada response'
            ]);
            return [];
        }

        return $this->processPintasanData($response->json('data'));
    }

    private function processPintasanData($data)
    {
        $result = [];
        foreach ($data as $item) {
            foreach ($item['pintasan'] as $pintasan) {
                $result[] = [
                    'title' => $pintasan['nama_kategori'],
                    'menu' => array_map(function ($detail) {
                        return [
                            'name' => $detail['judul'],
                            'route' => $detail['url']
                        ];
                    }, $pintasan['detail'])
                ];
            }
        }
        return $result;
    }

    private function fetchAksesCepatData($response)
    {
        if ($response->failed() || !$response->json('success')) {
            Log::warning('API Akses Cepat gagal atau data tidak lengkap', [
                'response' => $response->json() ?? 'Tidak ada response'
            ]);
            return [];
        }

        return $this->processAksesCepatData($response->json('data'));
    }

    private function processAksesCepatData($data)
    {
        $result = [];
        foreach ($data as $item) {
            $result[] = [
                'title' => $item['kategori_judul'],
                'menu' => array_map(function ($akses) {
                    return [
                        'name' => $akses['judul'],
                        'static_icon' => $akses['static_icon'],
                        'animation_icon' => $akses['animation_icon'],
                        'route' => $akses['url']
                    ];
                }, $item['akses_cepat'])
            ];
        }
        return $result;
    }

    private function fetchPengumumanData($response)
    {
        if ($response->failed() || !$response->json('success')) {
            Log::warning('API Pengumuman gagal atau data tidak lengkap', [
                'response' => $response->json() ?? 'Tidak ada response'
            ]);
            return [];
        }

        return $this->processPengumumanData($response->json('data'));
    }
    private function processPengumumanData($data)
    {
        $result = [];
        foreach ($data as $item) {
            $result[] = [
                'berita_id' => $item['id'] ?? null,
                'judul' => $item['judul'] ?? 'Tanpa Judul',
                'slug' => $item['slug'] ?? null,
                'kategoriSubmenu' => $item['kategoriSubmenu'] ?? null,
                'thumbnail' => $item['thumbnail'] ?? null,
                'tipe' => $item['tipe'] ?? null,
                'value' => $item['value'] ?? null,
                'deskripsi' => $item['deskripsi'] ?? null,
                'url_selengkapnya' => $item['url_selengkapnya'] ?? null,
                'created_at' => $item['created_at'] ?? null,
            ];
        }
        return $result;
    }



    private function fetchBeritaData($response)
    {
        if ($response->failed() || !$response->json('success')) {
            Log::warning('API Pengumuman gagal atau data tidak lengkap', [
                'response' => $response->json() ?? 'Tidak ada response'
            ]);
            return [];
        }

        return $this->processBeritaData($response->json('data'));
    }

    private function processBeritaData($data)
    {
        $result = [];
        foreach ($data as $itemBerita) {
            $result[] = [
                'berita_id' => $itemBerita['berita_id'] ?? null,
                'kategori' => $itemBerita['kategori'] ?? 'Berita',
                'judul' => $itemBerita['judul'] ?? 'Tanpa Judul',
                'slug' => $itemBerita['slug'] ?? null,
                'deskripsiThumbnail' => $itemBerita['deskripsiThumbnail'] ?? null,
                'url_selengkapnya' => $itemBerita['url_selengkapnya'] ?? null,
            ];
        }
        return $result;
    }


    private function fetchHeroSectionData($response)
    {
        if ($response->failed() || !$response->json('success')) {
            Log::warning('API Pengumuman gagal atau data tidak lengkap', [
                'response' => $response->json() ?? 'Tidak ada response'
            ]);
            return [];
        }

        return $this->processHeroSectionData($response->json('data'));
    }

    private function processHeroSectionData($data)
    {
        $result = [];
        foreach ($data as $item) {
            $result[] = [
                'title' => $item['kategori_nama'] ?? 'Hero Section',
                'media' => array_map(function ($media) {
                    return [
                        'id' => $media['id'] ?? null,
                        'type' => $media['tipe upload'] ?? null,
                        'url' => $media['media'] ?? null,
                    ];
                }, $item['media'] ?? [])
            ];
        }
        return $result;
    }

    private function fetchDokumentasiData($response)
    {
        if ($response->failed() || !$response->json('success')) {
            Log::warning('API Pengumuman gagal atau data tidak lengkap', [
                'response' => $response->json() ?? 'Tidak ada response'
            ]);
            return [];
        }

        return $this->processDokumentasiData($response->json('data'));
    }

    private function processDokumentasiData($data)
    {
        $result = [];
        foreach ($data as $item) {
            $result[] = [
                'title' => $item['kategori_nama'] ?? 'Dokumentasi PPID',
                'media' => array_map(function ($media) {
                    return [
                        'id' => $media['id'] ?? null,
                        'type' => $media['tipe upload'] ?? null,
                        'url' => $media['media'] ?? null,
                    ];
                }, $item['media'] ?? [])
            ];
        }
        return $result;
    }

    private function fetchMediaInformasiPublikData($response)
    {
        if ($response->failed() || !$response->json('success')) {
            Log::warning('API Pengumuman gagal atau data tidak lengkap', [
                'response' => $response->json() ?? 'Tidak ada response'
            ]);
            return [];
        }

        return $this->processMediaInformasiPublikData($response->json('data'));
    }

    private function processMediaInformasiPublikData($data)
    {
        $result = [];
        foreach ($data as $item) {
            $result[] = [
                'title' => $item['kategori_nama'] ?? 'Media Informasi Publik',
                'has_more' => $item['has_more'] ?? false,
                'total' => $item['total'] ?? 0,
                'media' => array_map(function ($media) {
                    return [
                        'id' => $media['id'] ?? null,
                        'title' => $media['judul'] ?? null,
                        'type' => $media['tipe'] ?? null,
                        'url' => $media['media'] ?? null,
                    ];
                }, $item['media'] ?? [])
            ];
        }
        return $result;
    }
}
