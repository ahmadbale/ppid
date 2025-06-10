<?php
namespace Modules\User\App\Http\Controllers\Form;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Services\JwtTokenService;

class SaranaPrasaranaController extends Controller
{
    protected $jwtTokenService;
    protected $baseUrl;

    public function __construct(JwtTokenService $jwtTokenService)
    {
        $this->jwtTokenService = $jwtTokenService;
        $this->baseUrl = config('BASE_URL', env('BASE_URL'));
    }

    private function makeAuthenticatedRequest($endpoint, $method = 'GET', $data = [], $files = [])
    {
        try {
            // Get active token
            $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vcHBpZC1wb2xpbmVtYS50ZXN0L2FwaS9hdXRoL2xvZ2luIiwiaWF0IjoxNzQ5NTI1NjQzLCJleHAiOjE3NDk1MjkyNDMsIm5iZiI6MTc0OTUyNTY0MywianRpIjoiQkprYzZ6Y3hVSnU2amtvdSIsInN1YiI6IjUiLCJwcnYiOiI3MDBmMzdkN2Q5MDU2MmQyYTYyNTU3ZDg4OWRlZTJlMDA5ODI3NzM1IiwidHlwZSI6InVzZXIiLCJ1c2VyX2lkIjo1LCJyb2xlIjoiUmVzcG9uZGVuIn0.4HvvkwJVCNozIaJatdeVYc-iCkEICK6ijPwlHEvG4T4';
            
            // Create HTTP client with authorization
            $httpClient = Http::withOptions(['verify' => false])
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $token,
                    'Accept' => 'application/json'
                ]);

            // Handle POST request with multipart data
            if ($method === 'POST' && !empty($files)) {
                // Add text fields
                foreach ($data as $key => $value) {
                    if (is_array($value)) {
                        foreach ($value as $nestedKey => $nestedValue) {
                            $httpClient = $httpClient->attach("{$key}[{$nestedKey}]", $nestedValue);
                        }
                    } else {
                        $httpClient = $httpClient->attach($key, $value);
                    }
                }

                // Add file fields
                foreach ($files as $key => $file) {
                    if ($file && $file->isValid()) {
                        $httpClient = $httpClient->attach(
                            $key,
                            file_get_contents($file->getRealPath()),
                            $file->getClientOriginalName(),
                            ['Content-Type' => $file->getMimeType()]
                        );
                    }
                }

                $response = $httpClient->post($this->baseUrl . '/api/' . $endpoint);
            } else {
                // Regular JSON request
                $response = $httpClient->withHeaders(['Content-Type' => 'application/json'])
                    ->post($this->baseUrl . '/api/' . $endpoint, $data);
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
        return view('user::e-form.sarana-prasarana');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            $request->validate([
                'pp_unit_kerja' => 'required|string',
                'pp_perawatan_yang_diusulkan' => 'required|string',
                'pp_keluhan_kerusakan' => 'required|string',
                'pp_lokasi_perawatan' => 'required|string',
                'pp_foto_kondisi' => 'required|file|mimes:jpg,jpeg,png|max:5120',
            ]);

            // Siapkan data untuk API
            $apiData = [
                't_permohonan_perawatan' => [
                    'pp_unit_kerja' => $request->input('pp_unit_kerja'),
                    'pp_perawatan_yang_diusulkan' => $request->input('pp_perawatan_yang_diusulkan'),
                    'pp_keluhan_kerusakan' => $request->input('pp_keluhan_kerusakan'),
                    'pp_lokasi_perawatan' => $request->input('pp_lokasi_perawatan'),
                ]
            ];

            // Array untuk file yang akan dikirim
            $files = [];

            // Upload foto kondisi
            if ($request->hasFile('pp_foto_kondisi')) {
                $files['pp_foto_kondisi'] = $request->file('pp_foto_kondisi');
            } else {
                return redirect()->back()->with('error', 'Foto kondisi wajib diupload!');
            }

            // Log data yang akan dikirim ke API
            Log::info('Data Permohonan Sarana Prasarana yang akan dikirim ke API:', [
                'data' => $apiData,
                'files' => array_keys($files)
            ]);

            // Kirim data ke API dengan file menggunakan multipart/form-data
            $response = $this->makeAuthenticatedRequest('auth/permohonan-perawatan/create', 'POST', $apiData, $files);

            if ($response->successful()) {
                $responseData = $response->json();
                Log::info('Permohonan Sarana dan Prasarana berhasil dikirim', [
                    'response' => $responseData
                ]);
                
                return redirect()->back()->with('success', 'Permohonan Sarana dan Prasarana berhasil dikirim!');
            } else {
                Log::error('API Error Response', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                
                $errorMessage = 'Gagal mengirim permohonan Sarana dan Prasarana. ';
                if ($response->status() === 422) {
                    $responseData = $response->json();
                    $errorMessage .= $responseData['message'] ?? 'Validasi gagal.';
                } else {
                    $errorMessage .= 'Silakan coba lagi.';
                }
                
                return redirect()->back()->with('error', $errorMessage);
            }

        } catch (\Exception $e) {
            Log::error('Error saat mengirim permohonan Sarana dan Prasarana', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }
}