<?php

namespace Modules\User\App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Services\JwtTokenService;

class RegulasiController extends Controller
{
    protected $jwtTokenService;
    protected $baseUrl;

    public function __construct(JwtTokenService $jwtTokenService)
    {
        $this->jwtTokenService = $jwtTokenService;
        $this->baseUrl = config('BASE_URL', env('BASE_URL'));
    }

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
                $response = Http::withHeaders([
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

    private function fetchRegulasiData($response)
    {
        if ($response->failed() || !$response->json('success')) {
            Log::warning('API Regulasi gagal atau data tidak lengkap', [
                'response' => $response->json() ?? 'Tidak ada response'
            ]);
            return [];
        }

        return $this->processRegulasiData($response->json('data'));
    }

   private function processRegulasiData($data)
{
    $result = [];

    foreach ($data as $item) {
        if (isset($item['kategori_regulasi']) && is_array($item['kategori_regulasi'])) {
            foreach ($item['kategori_regulasi'] as $kategori) {
                $regulasiList = [];
                $latestUpdate = $kategori['updated_at'] ?? date('Y-m-d H:i:s');
                
                if (isset($kategori['regulasi_list']) && is_array($kategori['regulasi_list'])) {
                    foreach ($kategori['regulasi_list'] as $regulasi) {
                        $regulasiList[] = [
                            'id' => $regulasi['id'] ?? null,
                            'judul' => $regulasi['judul'] ?? 'Tanpa Judul',
                            'tipe_dokumen' => $regulasi['tipe_dokumen'] ?? 'file',
                            'dokumen' => $regulasi['dokumen'] ?? null,
                        ];

                         // Check for more recent update date
                        if (isset($regulasi['updated_at'])) {
                            if (strtotime($regulasi['updated_at']) > strtotime($latestUpdate)) {
                                $latestUpdate = $regulasi['updated_at'];
                            }
                        }
                    }
                }
                
                $result[$kategori['kategori_kode']] = [
                    'kategori_id' => $kategori['kategori_id'] ?? null,
                    'kategori_nama' => $kategori['kategori_nama'] ?? null,
                      'updated_at' => $latestUpdate,
                    'regulasi_list' => $regulasiList
                ];
            }
        }
    }

    return $result;
}

    public function DHSOP()
    {
        try {
            $regulasiResponse = $this->makeAuthenticatedRequest('public/getDataRegulasi');
            $regulasiData = $this->fetchRegulasiData($regulasiResponse);
            
            $sopList = $regulasiData['DH03']['regulasi_list'] ?? [];
             $updated_at = $regulasiData['DH03']['updated_at'] ?? date('Y-m-d H:i:s');
            
            return view('user::informasi-publik.regulasi-DHSOP', compact('sopList', 'updated_at'));
        } catch (\Exception $e) {
            Log::error('Error saat mengambil data regulasi SOP dari API', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Fallback data jika API gagal
            $sopList = [
                [
                    'judul' => 'SOP Layanan Permohonan Informasi Publik',
                    'dokumen' => '#'
                ],
                [
                    'judul' => 'SOP Layanan Keberatan Atas Permohonan Informasi Publik',
                    'dokumen' => '#'
                ],
                [
                    'judul' => 'SOP Penetapan dan Pemutakhiran Daftar Informasi Publik',
                    'dokumen' => '#'
                ],
                [
                    'judul' => 'SOP Penanganan Sengketa Informasi',
                    'dokumen' => '#'
                ],
                [
                    'judul' => 'SOP Pengujian tentang Konsekuensi',
                    'dokumen' => '#'
                ],
                [
                    'judul' => 'SOP Pendokumentasian Informasi Publik',
                    'dokumen' => '#'
                ],
                [
                    'judul' => 'SOP Pendokumentasian Informasi Publik yang Dikecualikan',
                    'dokumen' => '#'
                ],
            ];
             $updated_at = date('Y-m-d H:i:s');
            
            return view('user::informasi-publik.regulasi-DHSOP', compact('sopList','updated_at'));
        }
    }

    public function DHKIP()
    {
        try {
            $regulasiResponse = $this->makeAuthenticatedRequest('public/getDataRegulasi');
            $regulasiData = $this->fetchRegulasiData($regulasiResponse);
            
            $dhkip = $regulasiData['DH02']['regulasi_list'] ?? [];
            $updated_at = $regulasiData['DH02']['updated_at'] ?? date('Y-m-d H:i:s');
            
            return view('user::informasi-publik.regulasi-DHKIP', compact('dhkip','updated_at'));
        } catch (\Exception $e) {
            Log::error('Error saat mengambil data regulasi DHKIP dari API', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Fallback data jika API gagal
            $dhkip = [
                [
                    'judul' => 'Undang-Undang Republik Indonesia Nomor 14 Tahun 2008',
                    'dokumen' => '#'
                ],
                [
                    'judul' => 'Undang-Undang Republik Indonesia Nomor 25 Tahun 2009',
                    'dokumen' => '#'
                ],
                // ... other fallback items
            ];
             $updated_at = date('Y-m-d H:i:s');
            
            return view('user::informasi-publik.regulasi-DHKIP', compact('dhkip','updated_at'));
        }
    }

    public function DHLIP()
    {
        try {
            $regulasiResponse = $this->makeAuthenticatedRequest('public/getDataRegulasi');
            $regulasiData = $this->fetchRegulasiData($regulasiResponse);
            
            $dhlip = $regulasiData['DH01']['regulasi_list'] ?? [];
            $updated_at = $regulasiData['DH01']['updated_at'] ?? date('Y-m-d H:i:s');
            
            return view('user::informasi-publik.regulasi-DHLIP', compact('dhlip','updated_at'));
        } catch (\Exception $e) {
            Log::error('Error saat mengambil data regulasi DHLIP dari API', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Fallback data jika API gagal
            $dhlip = [
                [
                    'judul' => 'Keputusan Direktur Politeknik Negeri Malang No.3047/PL34/OT/2018',
                    'dokumen' => '#'
                ],
                [
                    'judul' => 'Keputusan Direktur Politeknik Negeri Malang No.36/PL34/OT.01.02/2022',
                    'dokumen' => '#'
                ],
                // ... other fallback items
            ];
             $updated_at = date('Y-m-d H:i:s');
            
            return view('user::informasi-publik.regulasi-DHLIP', compact('dhlip','updated_at'));
        }
    }
}