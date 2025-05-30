<?php

namespace Modules\User\App\Http\Controllers\Form;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Services\JwtTokenService;

class KeberatanController extends Controller
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
            $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vcHBpZC1wb2xpbmVtYS50ZXN0L2FwaS9hdXRoL2xvZ2luIiwiaWF0IjoxNzQ4NTkzNzE3LCJleHAiOjE3NDg1OTczMTcsIm5iZiI6MTc0ODU5MzcxNywianRpIjoidnpnNXNaOFAwUklVUmtOQiIsInN1YiI6IjUiLCJwcnYiOiI3MDBmMzdkN2Q5MDU2MmQyYTYyNTU3ZDg4OWRlZTJlMDA5ODI3NzM1IiwidHlwZSI6InVzZXIiLCJ1c2VyX2lkIjo1LCJyb2xlIjoiUmVzcG9uZGVuIn0.g5dg7IblAYU2NxqAARvzfYJTl-gygLCWPibt2mTDBPU';

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
        $kategori = [
            ['id' => 1, 'nama' => 'Diri Sendiri'],
            ['id' => 2, 'nama' => 'Orang Lain'],
        ];

        $pertanyaanForm = [
            [
                'type' => 'select',
                'name' => 'pk_alasan_pengajuan_keberatan',
                'label' => 'Alasan Pengajuan Keberatan',
                'required' => true,
                'options' => [
                    'Permohonan Informasi Ditolak',
                    'Form Informasi Berkala Tidak Tersedia',
                    'Permintaan Informasi Tidak Dipenuhi',
                    'Permintaan Informasi Ditanggapi Tidak Sebagaimana Yang Diminta',
                    'Biaya Yang Dikenakan Tidak Wajar',
                    'Informasi Yang Disampaikan Melebihi Jangka Waktu Yang Ditentukan',
                ]
            ],
            [
                'type' => 'textarea',
                'name' => 'pk_kasus_posisi',
                'label' => 'Kasus Posisi',
                'required' => true,
                'rows' => 4
            ]
        ];
        return view('user::e-form.keberatan', compact('kategori', 'pertanyaanForm'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            // Validasi input dasar
            $request->validate([
                't_pernyataan_keberatan.pk_kategori_pemohon' => 'required|string',
                't_pernyataan_keberatan.pk_alasan_pengajuan_keberatan' => 'required|string',
                't_pernyataan_keberatan.pk_kasus_posisi' => 'required|string',
            ]);

            // Siapkan data untuk API
            $apiData = [
                't_pernyataan_keberatan' => [
                    'pk_kategori_pemohon' => $request->input('t_pernyataan_keberatan.pk_kategori_pemohon'),
                    'pk_alasan_pengajuan_keberatan' => $request->input('t_pernyataan_keberatan.pk_alasan_pengajuan_keberatan'),
                    'pk_kasus_posisi' => $request->input('t_pernyataan_keberatan.pk_kasus_posisi'),
                ]
            ];

            // Array untuk file yang akan dikirim
            $files = [];

            // Data tambahan berdasarkan kategori pemohon
            if ($request->input('t_pernyataan_keberatan.pk_kategori_pemohon') === 'Diri Sendiri') {
                // Untuk kategori diri sendiri tidak perlu validasi tambahan

            } elseif ($request->input('t_pernyataan_keberatan.pk_kategori_pemohon') === 'Orang Lain') {
                $request->validate([
                    't_form_pk_orang_lain.pk_nama_kuasa_pemohon' => 'required|string',
                    't_form_pk_orang_lain.pk_alamat_kuasa_pemohon' => 'required|string',
                    't_form_pk_orang_lain.pk_no_hp_kuasa_pemohon' => 'required|string',
                    't_form_pk_orang_lain.pk_email_kuasa_pemohon' => 'required|email',
                    'pk_upload_nik_kuasa_pemohon' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
                ]);

                $apiData['t_form_pk_orang_lain'] = [
                    'pk_nama_kuasa_pemohon' => $request->input('t_form_pk_orang_lain.pk_nama_kuasa_pemohon'),
                    'pk_alamat_kuasa_pemohon' => $request->input('t_form_pk_orang_lain.pk_alamat_kuasa_pemohon'),
                    'pk_no_hp_kuasa_pemohon' => $request->input('t_form_pk_orang_lain.pk_no_hp_kuasa_pemohon'),
                    'pk_email_kuasa_pemohon' => $request->input('t_form_pk_orang_lain.pk_email_kuasa_pemohon'),
                ];

                // Tambahkan file ke daftar file yang akan dikirim
                if ($request->hasFile('pk_upload_nik_kuasa_pemohon')) {
                    $files['pk_upload_nik_kuasa_pemohon'] = $request->file('pk_upload_nik_kuasa_pemohon');
                }
            }

            // Log data yang akan dikirim ke API
            Log::info('Data pernyataan keberatan yang akan dikirim ke API:', [
                'data' => $apiData,
                'files' => array_keys($files)
            ]);

            // Kirim data ke API dengan file menggunakan multipart/form-data
            $response = $this->makeAuthenticatedRequest('auth/pernyataan-keberatan/create', 'POST', $apiData, $files);

            if ($response->successful()) {
                $responseData = $response->json();
                Log::info('Pernyataan keberatan berhasil dikirim', [
                    'response' => $responseData
                ]);
                
                return redirect()->back()->with('success', 'Pernyataan keberatan berhasil dikirim!');
            } else {
                Log::error('API Error Response', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                
                $errorMessage = 'Gagal mengirim pernyataan keberatan. ';
                if ($response->status() === 401) {
                    $errorMessage .= 'Token authentication error.';
                } else {
                    $errorMessage .= 'Silakan coba lagi.';
                }
                
                return redirect()->back()->with('error', $errorMessage);
            }

        } catch (\Exception $e) {
            Log::error('Error saat mengirim pernyataan keberatan', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem. Silakan coba lagi.');
        }
    }
}