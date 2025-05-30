<?php

namespace Modules\User\App\Http\Controllers\Form;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Services\JwtTokenService;

class WBSController extends Controller
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
            $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vcHBpZC1wb2xpbmVtYS50ZXN0L2FwaS9hdXRoL2xvZ2luIiwiaWF0IjoxNzQ4NTk0NDI2LCJleHAiOjE3NDg1OTgwMjYsIm5iZiI6MTc0ODU5NDQyNiwianRpIjoibzg4Z1YyWmowamcwY25PUiIsInN1YiI6IjUiLCJwcnYiOiI3MDBmMzdkN2Q5MDU2MmQyYTYyNTU3ZDg4OWRlZTJlMDA5ODI3NzM1IiwidHlwZSI6InVzZXIiLCJ1c2VyX2lkIjo1LCJyb2xlIjoiUmVzcG9uZGVuIn0.9EZX6PJe1j3okzYSW52KePaFquxLzyIx5OsoSe9Z9z4';

            // Create HTTP client with authorization
            $httpClient = Http::withOptions(['verify' => false])
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $token,
                    'Accept' => 'application/json'
                ]);

            // Handle request based on method and if we have files
            if ($method === 'POST') {
                if (!empty($files)) {
                    // Use multipart for file uploads
                    foreach ($data as $key => $value) {
                        if (is_array($value)) {
                            foreach ($value as $nestedKey => $nestedValue) {
                                $httpClient = $httpClient->attach("{$key}[{$nestedKey}]", $nestedValue);
                            }
                        } else {
                            $httpClient = $httpClient->attach($key, $value);
                        }
                    }

                    // Attach all files
                    foreach ($files as $key => $file) {
                        $httpClient = $httpClient->attach(
                            $key,
                            file_get_contents($file->getRealPath()),
                            $file->getClientOriginalName(),
                            ['Content-Type' => $file->getMimeType()]
                        );
                    }

                    $response = $httpClient->post($this->baseUrl . '/api/' . $endpoint);
                } else {
                    // Regular JSON request without files
                    $response = $httpClient->withHeaders(['Content-Type' => 'application/json'])
                        ->post($this->baseUrl . '/api/' . $endpoint, $data);
                }
            } else {
                // GET request
                $response = $httpClient->get($this->baseUrl . '/api/' . $endpoint);
            }

            // Check if token expired
            if ($response->status() === 401) {
                // Generate new token and retry
                $tokenData = $this->jwtTokenService->generateSystemToken();

                // Recreate client with new token
                $httpClient = Http::withOptions(['verify' => false])
                    ->withHeaders([
                        'Authorization' => 'Bearer ' . $tokenData['token'],
                        'Accept' => 'application/json'
                    ]);

                // Repeat request with new token
                if ($method === 'POST') {
                    if (!empty($files)) {
                        // Repeat multipart upload with new token
                        foreach ($data as $key => $value) {
                            if (is_array($value)) {
                                foreach ($value as $nestedKey => $nestedValue) {
                                    $httpClient = $httpClient->attach("{$key}[{$nestedKey}]", $nestedValue);
                                }
                            } else {
                                $httpClient = $httpClient->attach($key, $value);
                            }
                        }

                        foreach ($files as $key => $file) {
                            $httpClient = $httpClient->attach(
                                $key,
                                file_get_contents($file->getRealPath()),
                                $file->getClientOriginalName(),
                                ['Content-Type' => $file->getMimeType()]
                            );
                        }

                        $response = $httpClient->post($this->baseUrl . '/api/' . $endpoint);
                    } else {
                        // Regular JSON request
                        $response = $httpClient->withHeaders(['Content-Type' => 'application/json'])
                            ->post($this->baseUrl . '/api/' . $endpoint, $data);
                    }
                } else {
                    $response = $httpClient->get($this->baseUrl . '/api/' . $endpoint);
                }
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

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Return view for WBS form
        return view('user::e-form.wbs');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            // Validasi input dasar
            $request->validate([
                'wbs_nama_pelapor' => 'required|string',
                'wbs_nik_pelapor' => 'required|string|min:16|max:16',
                'wbs_no_hp_pelapor' => 'required|string',
                'wbs_email_pelapor' => 'required|email',
                'wbs_jenis_laporan' => 'required|string',
                'wbs_yang_dilaporkan' => 'required|string',
                'wbs_jabatan_terlapor' => 'required|string',
                'wbs_tanggal_kejadian' => 'required|date',
                'wbs_lokasi_kejadian' => 'required|string',
                'wbs_kronologis_kejadian' => 'required|string',
                'wbs_upload_nik_pelapor' => 'required|file|mimes:jpg,jpeg,png|max:2048',
                'wbs_bukti_pendukung' => 'nullable|array',
                'wbs_bukti_pendukung.*' => 'nullable|file|max:5120', // 5MB per file
                'wbs_catatan' => 'nullable|string',
            ]);

            // Siapkan data untuk API
            $apiData = [
                't_wbs' => [
                    'wbs_nama_tanpa_gelar' => $request->input('wbs_nama_pelapor'),
                    'wbs_nik_pengguna' => $request->input('wbs_nik_pelapor'),
                    'wbs_email_pengguna' => $request->input('wbs_email_pelapor'),
                    'wbs_no_hp_pengguna' => $request->input('wbs_no_hp_pelapor'),
                    'wbs_jenis_laporan' => $request->input('wbs_jenis_laporan'),
                    'wbs_yang_dilaporkan' => $request->input('wbs_yang_dilaporkan'),
                    'wbs_jabatan' => $request->input('wbs_jabatan_terlapor'),
                    'wbs_waktu_kejadian' => $request->input('wbs_tanggal_kejadian'),
                    'wbs_lokasi_kejadian' => $request->input('wbs_lokasi_kejadian'),
                    'wbs_kronologis_kejadian' => $request->input('wbs_kronologis_kejadian'),
                    'wbs_catatan_tambahan' => $request->input('wbs_catatan'),
                ]
            ];

            // Array untuk file yang akan dikirim
            $files = [];

            // Upload KTP/NIK pengguna
            if ($request->hasFile('wbs_upload_nik_pelapor')) {
                $files['wbs_upload_nik_pengguna'] = $request->file('wbs_upload_nik_pelapor');
            }

            // Proses upload file bukti pendukung jika ada
            if ($request->hasFile('wbs_bukti_pendukung')) {
                if (count($request->file('wbs_bukti_pendukung')) > 0) {
                    // PENTING: Kirim bukti pendukung pertama sebagai field utama
                    $files['wbs_bukti_pendukung'] = $request->file('wbs_bukti_pendukung')[0];
                    
                    // Kirim bukti pendukung tambahan (jika ada)
                    $additionalFiles = array_slice($request->file('wbs_bukti_pendukung'), 1);
                    foreach ($additionalFiles as $index => $file) {
                        $files['wbs_bukti_pendukung_' . ($index + 1)] = $file;
                    }
                }
            }

            // Log data yang akan dikirim ke API
            Log::info('Data WBS yang akan dikirim ke API:', [
                'data' => $apiData,
                'files' => array_keys($files)
            ]);

            // Kirim data ke API dengan file menggunakan multipart/form-data
            $response = $this->makeAuthenticatedRequest('auth/wbs/create', 'POST', $apiData, $files);

            if ($response->successful()) {
                $responseData = $response->json();
                Log::info('Laporan WBS berhasil dikirim', [
                    'response' => $responseData
                ]);
                
                return redirect()->back()->with('success', 'Laporan berhasil dikirim!');
            } else {
                Log::error('API Error Response', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                
                $errorMessage = 'Gagal mengirim laporan. ';
                if ($response->status() === 401) {
                    $errorMessage .= 'Token authentication error.';
                } else {
                    $errorMessage .= 'Silakan coba lagi. ' . $response->body();
                }
                
                return redirect()->back()->with('error', $errorMessage);
            }

        } catch (\Exception $e) {
            Log::error('Error saat mengirim laporan WBS', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }
}