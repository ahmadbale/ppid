<?php

namespace Modules\User\App\Http\Controllers;

use App\Services\JwtTokenService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class InformasiPublikController extends Controller
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

    private function fetchDaftarInformasiPublikData($response)
    {
        if ($response->failed() || !$response->json('success')) {
            Log::warning('API Daftar Informasi Publik gagal atau data tidak lengkap', [
                'response' => $response->json() ?? 'Tidak ada response'
            ]);
            return null;
        }

        $allData = $response->json('data');
        
        // Filter untuk hanya mendapatkan data dengan nama_konten "Daftar Informasi Publik"
        foreach ($allData as $item) {
            if ($item['nama_konten'] === 'Daftar Informasi Publik') {
                return $item;
            }
        }
        
        return null;
    }

    public function index()
    {
        try {
            $response = $this->makeAuthenticatedRequest('public/getDataIPDaftarInformasi');
            $informasiPublikData = $this->fetchDaftarInformasiPublikData($response);
            
            if ($informasiPublikData && !empty($informasiPublikData['upload_konten'])) {
                $pdfData = $informasiPublikData['upload_konten'][0];
                $pdfFile = $pdfData['dokumen'] ?? null;
                $pdfName = $pdfData['judul_konten'] ?? 'Daftar Informasi Publik.pdf';
                $tanggal = $informasiPublikData['tanggal_dibuat'] ?? date('Y-m-d H:i:s');
                $sharedBy = 'PPID Politeknik Negeri Malang';
                
                return view('user::informasi-publik.daftar', 
                    compact('pdfFile', 'pdfName', 'sharedBy', 'tanggal')
                );
            } else {
                // Fallback jika data tidak ditemukan
                $pdfFile = 'storage/test-pdfview1.pdf';
                $pdfName = 'Daftar Informasi Publik Polinema.pdf';
                $sharedBy = 'superadmin';
                $tanggal = date('Y-m-d H:i:s');
                
                return view('user::informasi-publik.daftar', 
                    compact('pdfFile', 'pdfName', 'sharedBy', 'tanggal')
                );
            }
            
        } catch (\Exception $e) {
            Log::error('Error saat mengambil data daftar informasi publik dari API', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Fallback data jika API gagal
            $pdfFile = 'storage/test-pdfview1.pdf';
            $pdfName = 'Daftar Informasi Publik Polinema.pdf';
            $sharedBy = 'superadmin';
            $tanggal = date('Y-m-d H:i:s');
            
            return view('user::informasi-publik.daftar', 
                compact('pdfFile', 'pdfName', 'sharedBy', 'tanggal')
            );
        }
    }

   public function setiapSaat()
    {
        $informasiSetiapSaat = [
            [
                'nama' => 'Seluruh informasi lengkap yang wajib disediakan dan diumumkan secara berkala',
                'tanggal' => '15 Sep 2025',
                'dokumen_url' => '#',
            ],
            [
                'nama' => 'Daftar Informasi Publik',
                'tanggal' => '15 Sep 2025',
                'dokumen_url' => '#',
            ],
            [
                'nama' => 'Risalah Rapat Pembentukan Peraturan Perundang-Undangan (Alih Jabatan Dosen)',
                'tanggal' => '15 Sep 2025',
                'dokumen_url' => '#',
            ],
            [
                'nama' => 'Pemberian Pertimbangan Draf Peraturan Direktur Tentang Pedoman Akademik',
                'tanggal' => '15 Sep 2025',
                'dokumen_url' => '#',
            ],
            [
                'nama' => 'Rancangan Peraturan atau Kebijakan yang Dibentuk',
                'tanggal' => '15 Sep 2025',
                'dokumen_url' => '#',
            ],
            [
                'nama' => 'Surat Keputusan 27 - 2023 Tentang Tarif Layanan non Akademik Aset Barang Milik Negara',
                'tanggal' => '15 Sep 2025',
                'dokumen_url' => '#',
            ],
            [
                'nama' => 'Informasi tentang peraturan, keputusan dan/atau kebijakan unit organisasi',
                'tanggal' => '15 Sep 2025',
                'dokumen_url' => '#',
            ],
        ];

        $updated_item = [
            ['updated_at' => '14 Juni 2025'],
        ];
        return view('user::informasi-publik.setiap-saat', compact('informasiSetiapSaat', 'updated_item'));
    }

    public function berkala(){

        $informasiBerkala = [
            [
                'nama' => 'Seluruh informasi lengkap yang wajib disediakan dan diumumkan secara berkala',
                'tanggal' => '15 Sep 2025',
                'dokumen_url' => '#',
            ],
            [
                'nama' => 'Daftar Informasi Publik',
                'tanggal' => '15 Sep 2025',
                'dokumen_url' => '#',
            ],
            [
                'nama' => 'Risalah Rapat Pembentukan Peraturan Perundang-Undangan (Alih Jabatan Dosen)',
                'tanggal' => '15 Sep 2025',
                'dokumen_url' => '#',
            ],
            [
                'nama' => 'Pemberian Pertimbangan Draf Peraturan Direktur Tentang Pedoman Akademik',
                'tanggal' => '15 Sep 2025',
                'dokumen_url' => '#',
            ],
            [
                'nama' => 'Rancangan Peraturan atau Kebijakan yang Dibentuk',
                'tanggal' => '15 Sep 2025',
                'dokumen_url' => '#',
            ],
            [
                'nama' => 'Surat Keputusan 27 - 2023 Tentang Tarif Layanan non Akademik Aset Barang Milik Negara',
                'tanggal' => '15 Sep 2025',
                'dokumen_url' => '#',
            ],
            [
                'nama' => 'Informasi tentang peraturan, keputusan dan/atau kebijakan unit organisasi',
                'tanggal' => '15 Sep 2025',
                'dokumen_url' => '#',
            ],
        ];

        $updated_item = [
            ['updated_at' => '14 Juni 2025'],
        ];
        return view('user::informasi-publik.berkala', compact('informasiBerkala', 'updated_item'));
    }

    public function sertaMerta(){

        $sertaMerta = [
            [
                'nama' => 'Seluruh informasi lengkap yang wajib disediakan dan diumumkan secara berkala',
                'tanggal' => '15 Sep 2025',
                'dokumen_url' => '#',
            ],
            [
                'nama' => 'Daftar Informasi Publik',
                'tanggal' => '15 Sep 2025',
                'dokumen_url' => '#',
            ],
            [
                'nama' => 'Risalah Rapat Pembentukan Peraturan Perundang-Undangan (Alih Jabatan Dosen)',
                'tanggal' => '15 Sep 2025',
                'dokumen_url' => '#',
            ],
            [
                'nama' => 'Pemberian Pertimbangan Draf Peraturan Direktur Tentang Pedoman Akademik',
                'tanggal' => '15 Sep 2025',
                'dokumen_url' => '#',
            ],
            [
                'nama' => 'Rancangan Peraturan atau Kebijakan yang Dibentuk',
                'tanggal' => '15 Sep 2025',
                'dokumen_url' => '#',
            ],
            [
                'nama' => 'Surat Keputusan 27 - 2023 Tentang Tarif Layanan non Akademik Aset Barang Milik Negara',
                'tanggal' => '15 Sep 2025',
                'dokumen_url' => '#',
            ],
            [
                'nama' => 'Informasi tentang peraturan, keputusan dan/atau kebijakan unit organisasi',
                'tanggal' => '15 Sep 2025',
                'dokumen_url' => '#',
            ],
        ];

        $updated_item = [
            ['updated_at' => '14 Juni 2025'],
        ];
        return view('user::informasi-publik.serta-merta', compact('sertaMerta', 'updated_item'));
    }
}