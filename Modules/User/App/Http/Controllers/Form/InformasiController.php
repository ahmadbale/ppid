<?php

namespace Modules\User\App\Http\Controllers\Form;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Services\JwtTokenService;

class InformasiController extends Controller
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
            $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vcHBpZC1wb2xpbmVtYS50ZXN0L2FwaS9hdXRoL2xvZ2luIiwiaWF0IjoxNzQ4NDE1OTQ4LCJleHAiOjE3NDg0MTk1NDgsIm5iZiI6MTc0ODQxNTk0OCwianRpIjoia1NYTHMwVXRzcjF3UEFUUyIsInN1YiI6IjUiLCJwcnYiOiI3MDBmMzdkN2Q5MDU2MmQyYTYyNTU3ZDg4OWRlZTJlMDA5ODI3NzM1IiwidHlwZSI6InVzZXIiLCJ1c2VyX2lkIjo1LCJyb2xlIjoiUmVzcG9uZGVuIn0.c1OQn2A7VMxUK1Sn-GgLxdSyjqcqV8j1Fe0RMthJqcM';

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
            ['id' => 3, 'nama' => 'Organisasi'],
        ];

        $pertanyaanForm = [
            [
                "label" => "Informasi yang Dibutuhkan",
                "name" => "pi_informasi_yang_dibutuhkan",
                "type" => "textarea",
                "required" => true
            ],
            [
                "label" => "Alasan Permohonan Informasi",
                "name" => "pi_alasan_permohonan_informasi",
                "type" => "textarea",
                "required" => true
            ],
            [
                "label" => "Sumber Informasi",
                "name" => "pi_sumber_informasi",
                "type" => "radiobutton",
                "options" => [
                    "Pertanyaan Langsung Pemohon",
                    "Website / Media Sosial Milik Polinema",
                    "Website / Media Sosial Bukan Milik Polinema"
                ],
                "required" => true
            ],
            [
                "label" => "Alamat Sumber Informasi",
                "name" => "pi_alamat_sumber_informasi",
                "type" => "text",
                "required" => true
            ]
        ];

        return view('user::e-form.informasi-publik', compact('kategori', 'pertanyaanForm'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            // Validasi input dasar
            $request->validate([
                't_permohonan_informasi.pi_kategori_pemohon' => 'required|string',
                't_permohonan_informasi.pi_informasi_yang_dibutuhkan' => 'required|string',
                't_permohonan_informasi.pi_alasan_permohonan_informasi' => 'required|string',
                't_permohonan_informasi.pi_sumber_informasi' => 'required',  // Ubah validasi
                't_permohonan_informasi.pi_alamat_sumber_informasi' => 'required|string',
            ]);

            // Siapkan data untuk API
            $apiData = [
                't_permohonan_informasi' => [
                    'pi_kategori_pemohon' => $request->input('t_permohonan_informasi.pi_kategori_pemohon'),
                    'pi_informasi_yang_dibutuhkan' => $request->input('t_permohonan_informasi.pi_informasi_yang_dibutuhkan'),
                    'pi_alasan_permohonan_informasi' => $request->input('t_permohonan_informasi.pi_alasan_permohonan_informasi'),
                    'pi_alamat_sumber_informasi' => $request->input('t_permohonan_informasi.pi_alamat_sumber_informasi'),
                ]
            ];

            // Tangani array sumber informasi jika diperlukan
            if (is_array($request->input('t_permohonan_informasi.pi_sumber_informasi'))) {
                $apiData['t_permohonan_informasi']['pi_sumber_informasi'] =
                    implode(', ', $request->input('t_permohonan_informasi.pi_sumber_informasi'));
            } else {
                $apiData['t_permohonan_informasi']['pi_sumber_informasi'] =
                    $request->input('t_permohonan_informasi.pi_sumber_informasi');
            }

            // Array untuk file yang akan dikirim
            $files = [];

            // Data tambahan berdasarkan kategori pemohon
            if ($request->input('t_permohonan_informasi.pi_kategori_pemohon') === 'Diri Sendiri') {
                // Untuk kategori diri sendiri tidak perlu validasi tambahan

            } elseif ($request->input('t_permohonan_informasi.pi_kategori_pemohon') === 'Organisasi') {
                $request->validate([
                    't_form_pi_organisasi.pi_nama_organisasi' => 'required|string',
                    't_form_pi_organisasi.pi_no_telp_organisasi' => 'required|string',
                    't_form_pi_organisasi.pi_email_atau_medsos_organisasi' => 'required|string',
                    't_form_pi_organisasi.pi_nama_narahubung' => 'required|string',
                    't_form_pi_organisasi.pi_no_telp_narahubung' => 'required|string',
                    'pi_identitas_narahubung' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
                ]);

                $apiData['t_form_pi_organisasi'] = [
                    'pi_nama_organisasi' => $request->input('t_form_pi_organisasi.pi_nama_organisasi'),
                    'pi_no_telp_organisasi' => $request->input('t_form_pi_organisasi.pi_no_telp_organisasi'),
                    'pi_email_atau_medsos_organisasi' => $request->input('t_form_pi_organisasi.pi_email_atau_medsos_organisasi'),
                    'pi_nama_narahubung' => $request->input('t_form_pi_organisasi.pi_nama_narahubung'),
                    'pi_no_telp_narahubung' => $request->input('t_form_pi_organisasi.pi_no_telp_narahubung'),
                ];

                // Tambahkan file ke daftar file yang akan dikirim, tanpa base64
                if ($request->hasFile('pi_identitas_narahubung')) {
                    $files['pi_identitas_narahubung'] = $request->file('pi_identitas_narahubung');
                }

            } elseif ($request->input('t_permohonan_informasi.pi_kategori_pemohon') === 'Orang Lain') {
                $request->validate([
                    't_form_pi_orang_lain.pi_nama_pengguna_penginput' => 'required|string',
                    't_form_pi_orang_lain.pi_alamat_pengguna_penginput' => 'required|string',
                    't_form_pi_orang_lain.pi_no_hp_pengguna_penginput' => 'required|string',
                    't_form_pi_orang_lain.pi_email_pengguna_penginput' => 'required|email',
                    't_form_pi_orang_lain.pi_nama_pengguna_informasi' => 'required|string',
                    't_form_pi_orang_lain.pi_alamat_pengguna_informasi' => 'required|string',
                    't_form_pi_orang_lain.pi_no_hp_pengguna_informasi' => 'required|string',
                    't_form_pi_orang_lain.pi_email_pengguna_informasi' => 'required|email',
                    'pi_upload_nik_pengguna_penginput' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
                    'pi_upload_nik_pengguna_informasi' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
                ]);

                $apiData['t_form_pi_orang_lain'] = [
                    'pi_nama_pengguna_penginput' => $request->input('t_form_pi_orang_lain.pi_nama_pengguna_penginput'),
                    'pi_alamat_pengguna_penginput' => $request->input('t_form_pi_orang_lain.pi_alamat_pengguna_penginput'),
                    'pi_no_hp_pengguna_penginput' => $request->input('t_form_pi_orang_lain.pi_no_hp_pengguna_penginput'),
                    'pi_email_pengguna_penginput' => $request->input('t_form_pi_orang_lain.pi_email_pengguna_penginput'),
                    'pi_nama_pengguna_informasi' => $request->input('t_form_pi_orang_lain.pi_nama_pengguna_informasi'),
                    'pi_alamat_pengguna_informasi' => $request->input('t_form_pi_orang_lain.pi_alamat_pengguna_informasi'),
                    'pi_no_hp_pengguna_informasi' => $request->input('t_form_pi_orang_lain.pi_no_hp_pengguna_informasi'),
                    'pi_email_pengguna_informasi' => $request->input('t_form_pi_orang_lain.pi_email_pengguna_informasi'),
                ];

                // Tambahkan file ke daftar file yang akan dikirim, tanpa base64
                if ($request->hasFile('pi_upload_nik_pengguna_penginput')) {
                    $files['pi_upload_nik_pengguna_penginput'] = $request->file('pi_upload_nik_pengguna_penginput');
                }

                if ($request->hasFile('pi_upload_nik_pengguna_informasi')) {
                    $files['pi_upload_nik_pengguna_informasi'] = $request->file('pi_upload_nik_pengguna_informasi');
                }
            }

            // Log data yang akan dikirim ke API
            Log::info('Data yang akan dikirim ke API:', [
                'data' => $apiData,
                'files' => array_keys($files)
            ]);

            // Kirim data ke API dengan file menggunakan multipart/form-data
            $response = $this->makeAuthenticatedRequest('auth/permohonan-informasi/create', 'POST', $apiData, $files);

            if ($response->successful()) {
                $responseData = $response->json();
                Log::info('Permohonan informasi berhasil dikirim', [
                    'response' => $responseData
                ]);

                return redirect()->back()->with('success', 'Permohonan informasi berhasil dikirim!');
            } else {
                Log::error('API Error Response', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                $errorMessage = 'Gagal mengirim permohonan. ';
                if ($response->status() === 401) {
                    $errorMessage .= 'Token authentication error.';
                } else {
                    $errorMessage .= 'Silakan coba lagi.';
                }

                return redirect()->back()->with('error', $errorMessage);
            }

        } catch (\Exception $e) {
            Log::error('Error saat mengirim permohonan informasi', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'Terjadi kesalahan sistem. Silakan coba lagi.');
        }
    }

    // Method lainnya tetap sama (create, show, edit, update, destroy)
}