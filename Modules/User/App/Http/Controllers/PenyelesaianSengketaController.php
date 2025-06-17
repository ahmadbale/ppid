<?php

namespace Modules\User\App\Http\Controllers;

use App\Services\JwtTokenService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PenyelesaianSengketaController extends Controller
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

    private function fetchPenyelesaianSengketaData($response)
    {
        if ($response->failed() || !$response->json('success')) {
            Log::warning('API Penyelesaian Sengketa gagal atau data tidak lengkap', [
                'response' => $response->json() ?? 'Tidak ada response'
            ]);
            return [];
        }

        return $this->processPenyelesaianSengketaData($response->json('data'));
    }

    private function processPenyelesaianSengketaData($data)
    {
        $result = [];

        foreach ($data as $item) {
            $uploadFiles = [];
            
            if (isset($item['uploads']) && is_array($item['uploads'])) {
                foreach ($item['uploads'] as $upload) {
                    $uploadFiles[] = [
                        'id' => $upload['upload_ps_id'] ?? null,
                        'kategori' => $upload['kategori_upload_ps'] ?? 'file',
                        'dokumen' => $upload['dokumen'] ?? null,
                    ];
                }
            }

            $result[$item['ps_kode']] = [
                'id' => $item['penyelesaian_sengketa_id'] ?? null,
                'nama' => $item['ps_nama'] ?? 'Tanpa Nama',
                'deskripsi' => $item['ps_deskripsi'] ?? '',
                'uploads' => $uploadFiles
            ];
        }

        return $result;
    }

    public function index()
    {
        try {
            $response = $this->makeAuthenticatedRequest('public/getDataPenyelesaianSengketa');
            $penyelesaianSengketaData = $this->fetchPenyelesaianSengketaData($response);
            
            // Assuming PS01 is the code for permohonan penyelesaian sengketa 
            $permohonanData = $penyelesaianSengketaData['PS-001'] ?? null;
            
            if ($permohonanData) {
                $nama = $permohonanData['nama'];
                $deskripsi = $permohonanData['deskripsi'];
                $fileUploads = $permohonanData['uploads'];
                
                return view('user::informasi-publik.penyelesaian-sengketa', compact(
                    'nama',
                    'deskripsi',
                    'fileUploads'
                ));
            } else {
                // Fallback data if API data is not found
                $nama = "Permohonan Penyelesaian Sengketa Informasi Publik";
                $deskripsi = "Sengketa yang terjadi antara badan publik dan pengguna informasi publik yang berkaitan dengan hak memperoleh dan menggunakan informasi berdasarkan perundang-undangan...";
                $fileUploads = [
                    [
                        'id' => 1,
                        'kategori' => 'file',
                        'dokumen' => 'storage/default-files/penyelesaian-sengketa.pdf'
                    ]
                ];
                
                return view('user::informasi-publik.penyelesaian-sengketa', compact(
                    'nama',
                    'deskripsi',
                    'fileUploads'
                ));
            }
            
        } catch (\Exception $e) {
            Log::error('Error saat mengambil data penyelesaian sengketa dari API', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Fallback data if API fails
            $nama = "Permohonan Penyelesaian Sengketa Informasi Publik";
            $deskripsi = "Sengketa yang terjadi antara badan publik dan pengguna informasi publik yang berkaitan dengan hak memperoleh dan menggunakan informasi berdasarkan perundang-undangan...";
            $fileUploads = [
                [
                    'id' => 1,
                    'kategori' => 'file',
                    'dokumen' => 'storage/default-files/penyelesaian-sengketa.pdf'
                ]
            ];
            
            return view('user::informasi-publik.penyelesaian-sengketa', compact(
                'nama',
                'deskripsi',
                'fileUploads'
            ));
        }
    }
}