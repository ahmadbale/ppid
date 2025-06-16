<?php


namespace Modules\User\App\Http\Controllers;

use App\Services\JwtTokenService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class InformasiDikecualikanController extends Controller
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

    private function fetchDaftarInformasiDikecualikanData($response)
    {
        if ($response->failed() || !$response->json('success')) {
            Log::warning('API Daftar Informasi Dikecualikan gagal atau data tidak lengkap', [
                'response' => $response->json() ?? 'Tidak ada response'
            ]);
            return null;
        }

        $allData = $response->json('data');
        
        // Filter untuk hanya mendapatkan data dengan nama_konten "Daftar Informasi yang Dikecualikan"
        foreach ($allData as $item) {
            if ($item['nama_konten'] === 'Daftar Informasi yang Dikecualikan') {
                return $item;
            }
        }
        
        return null;
    }

    public function index()
    {
        try {
            $response = $this->makeAuthenticatedRequest('public/getDataIPDaftarInformasi');
            $informasiDikecualikanData = $this->fetchDaftarInformasiDikecualikanData($response);
            
            if ($informasiDikecualikanData && !empty($informasiDikecualikanData['upload_konten'])) {
                $pdfData = $informasiDikecualikanData['upload_konten'][0];
                $pdfFile = $pdfData['dokumen'] ?? null;
                $pdfName = $pdfData['judul_konten'] ?? 'Daftar Informasi Dikecualikan.pdf';
                $updated_at = $informasiDikecualikanData['tanggal_dibuat'] ?? date('Y-m-d H:i:s');
                $sharedBy = 'PPID Politeknik Negeri Malang';
                
                return view('user::informasi-publik.dikecualikan', 
                    compact('pdfFile', 'pdfName', 'sharedBy', 'updated_at')
                );
            } else {
                // Fallback jika data tidak ditemukan
                $pdfFile = 'storage/test-pdfview1.pdf';
                $pdfName = 'Daftar Informasi Dikecualikan.pdf';
                $sharedBy = 'superadmin';
                $updated_at = date('Y-m-d H:i:s');
                
                return view('user::informasi-publik.dikecualikan', 
                    compact('pdfFile', 'pdfName', 'sharedBy', 'updated_at')
                );
            }
            
        } catch (\Exception $e) {
            Log::error('Error saat mengambil data informasi dikecualikan dari API', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Fallback data jika API gagal
            $pdfFile = 'storage/test-pdfview1.pdf';
            $pdfName = 'Daftar Informasi Dikecualikan.pdf';
            $sharedBy = 'superadmin';
            $updated_at = date('Y-m-d H:i:s');
            
            return view('user::informasi-publik.dikecualikan', 
                compact('pdfFile', 'pdfName', 'sharedBy', 'updated_at')
            );
        }
    }
}