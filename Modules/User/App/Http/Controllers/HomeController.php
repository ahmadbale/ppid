<?php

namespace Modules\User\App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use App\Services\JwtTokenService;

class HomeController extends Controller
{
    protected $jwtTokenService;
    protected $baseUrl;

    public function __construct(JwtTokenService $jwtTokenService)
    {
        $this->jwtTokenService = $jwtTokenService;
        $this->baseUrl = config('BASE_URL', env('BASE_URL'));
    }


    /**
     * Make authenticated request with JWT token
     */
    private function makeAuthenticatedRequest($endpoint)
    {
        try {
            // Get active token
            $tokenData = $this->jwtTokenService->getActiveToken();

            // Make request with token
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $tokenData['token']
            ])->get($this->baseUrl . '/api/' . $endpoint);

            // Check if token expired
            if ($response->status() === 401) {
                // Generate new token and retry
                $tokenData = $this->jwtTokenService->generateSystemToken();
                // $response = Http::withHeaders([
                //     'Authorization' => 'Bearer ' . $tokenData['token']
                // ])->get($this->baseUrl . '/api/' . $endpoint);
                 $response = Http::withOptions(['verify' => false])->withHeaders([
                'Authorization' => 'Bearer ' . $tokenData['token']
            ])->get($this->baseUrl . '/api/' . $endpoint);
            }

            return $response;
        } catch (\Exception $e) {
            Log::error('API request failed', [
                'endpoint' => $endpoint,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
    public function index()
    {
        try {
            // Update semua request menggunakan makeAuthenticatedRequest
            $pintasanResponse = $this->makeAuthenticatedRequest('public/getDataPintasanLainnya');
            $pintasanMenus = $this->fetchPintasanData($pintasanResponse);

            $aksesCepatResponse = $this->makeAuthenticatedRequest('public/getDataAksesCepat');
            $aksesCepatMenus = $this->fetchAksesCepatData($aksesCepatResponse);

            $pengumumanResponse = $this->makeAuthenticatedRequest('public/getDataPengumumanLandingPage');
            $pengumumanMenus = $this->fetchPengumumanData($pengumumanResponse);

            $beritaResponse = $this->makeAuthenticatedRequest('public/getDataBeritaLandingPage');
            $beritaMenus = $this->fetchBeritaData($beritaResponse);

            $heroSectionResponse = $this->makeAuthenticatedRequest('public/getDataHeroSection');
            $heroSectionMenus = $this->fetchHeroSectionData($heroSectionResponse);

            $dokumentasiResponse = $this->makeAuthenticatedRequest('public/getDataDokumentasi');
            $dokumentasiMenus = $this->fetchDokumentasiData($dokumentasiResponse);

            $mediaInformasiPublikResponse = $this->makeAuthenticatedRequest('public/getDataMediaInformasiPublik');
            $mediaInformasiPublikMenus = $this->fetchMediaInformasiPublikData($mediaInformasiPublikResponse);

            $statisticResponse = $this->makeAuthenticatedRequest('public/getDashboardStatistics');
            $statisticData = $this->fetchStatisticData($statisticResponse);

            return view('user::landing_page', compact(
                'pintasanMenus',
                'aksesCepatMenus',
                'pengumumanMenus',
                'beritaMenus',
                'heroSectionMenus',
                'dokumentasiMenus',
                'mediaInformasiPublikMenus',
                'statisticData'
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
                'statisticData' => []
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
            $kategoriJudul = $item['kategori_judul'] ?? 'Pintasan Lainnya';

            foreach ($item['pintasan'] as $pintasan) {
                $result[] = [
                    'kategori_judul' => $kategoriJudul,
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

    // Tambahkan method baru untuk fetch dan process data statistik
    private function fetchStatisticData($response)
    {
        if ($response->failed() || !$response->json('success')) {
            Log::warning('API Statistik Dashboard gagal atau data tidak lengkap', [
                'response' => $response->json() ?? 'Tidak ada response'
            ]);
            return [];
        }

        return $this->processStatisticData($response->json('data'));
    }

    private function processStatisticData($data)
    {
        $result = [];
        try {
            if (isset($data['periode']) && isset($data['jenis_kasus'])) {
                $result = [
                    'periode' => [
                        'tahun' => $data['periode']['tahun'],
                        'pengajuan_total' => $data['periode']['pengajuan_total'],
                        'pengajuan_diterima' => $data['periode']['pengajuan_diterima'],
                        'pengajuan_ditolak' => $data['periode']['pengajuan_ditolak']
                    ],
                    'jenis_kasus' => [
                        'permohonan_informasi' => $data['jenis_kasus']['permohonan_informasi'],
                        'whistle_blowing_system' => $data['jenis_kasus']['whistle_blowing_system'],
                        'pernyataan_keberatan' => $data['jenis_kasus']['pernyataan_keberatan'],
                        'aduan_masyarakat' => $data['jenis_kasus']['aduan_masyarakat'],
                        'permohonan_pemeliharaan' => $data['jenis_kasus']['permohonan_pemeliharaan']
                    ]
                ];
            }
            return $result;
        } catch (\Exception $e) {
            Log::error('Error saat memproses data statistik', [
                'message' => $e->getMessage(),
                'data' => $data
            ]);
            return [];
        }
    }

    public function detail($slug, $encryptedId)
    {
        try {
            // Dekripsi ID dari URL
            $beritaId = Crypt::decryptString(urldecode($encryptedId));

            // Ambil data detail berita dari API berdasarkan ID
            $detailResponse = $this->makeAuthenticatedRequest("public/getDetailBeritaById/{$slug}/{$beritaId}");

            $detailData = $this->fetchBeritaDetail($detailResponse);

            // Validasi data kosong atau slug tidak cocok
            if (empty($detailData) || $detailData['slug'] !== $slug) {
                return view('user::404', [
                    'message' => 'Laman Tidak Ditemukan',
                ]);
            }

            return view('user::landing_page', [
                'beritaDetail' => $detailData
            ]);
        } catch (\Exception $e) {
            Log::error('Error saat mengambil detail berita dari API', [
                'encrypted_id' => $encryptedId,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Jika gagal dekripsi atau terjadi error lain, arahkan ke 404
            return view('user::404', [
                'message' => 'Laman Tidak Ditemukan',
            ]);
        }
    }


    private function fetchBeritaDetail($response)
    {
        if ($response->failed() || !$response->json('success')) {
            Log::warning('API Detail Berita gagal atau data tidak lengkap', [
                'response' => $response->json() ?? 'Tidak ada response'
            ]);
            return null;
        }

        return $this->processBeritaDetail($response->json('data'));
    }
}