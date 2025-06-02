<?php
namespace Modules\User\App\Http\Controllers\Form;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Services\JwtTokenService;


class PengaduanMasyarakatController extends Controller
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
            $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vcHBpZC1wb2xpbmVtYS50ZXN0L2FwaS9hdXRoL2xvZ2luIiwiaWF0IjoxNzQ4Nzg4MzEzLCJleHAiOjE3NDg3OTE5MTIsIm5iZiI6MTc0ODc4ODMxMywianRpIjoiR293eThYY0psU3ZiZFFTTSIsInN1YiI6IjUiLCJwcnYiOiI3MDBmMzdkN2Q5MDU2MmQyYTYyNTU3ZDg4OWRlZTJlMDA5ODI3NzM1IiwidHlwZSI6InVzZXIiLCJ1c2VyX2lkIjo1LCJyb2xlIjoiUmVzcG9uZGVuIn0.oR-F8KBZKap_3boTnaGu920f4xBbsCkT3_o1-ECh8qo';
            
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
        return view('user::e-form.aduan-masyarakat');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            
              $request->validate([
               	'pm_nama_pelapor' => 'required|string',
                'pm_nik_pelapor' => 'required|string|min:16|max:16',
                'pm_no_hp_pelapor' => 'required|string',
                'pm_email_pelapor' => 'required|email',
                'pm_jenis_laporan' => 'required|string',
                'pm_nama_terlapor' => 'required|string',
                'pm_jabatan_terlapor' => 'required|string',
                'pm_tanggal_kejadian' => 'required|date',
                'pm_lokasi_kejadian' => 'required|string',
                'pm_uraian_pengaduan' => 'required|string',
                'pm_upload_nik_pengguna' => 'required|file|mimes:jpg,jpeg,png|max:2048',
                'pm_catatan' => 'nullable|string',
                'pm_bukti_pendukung' => 'required|file|max:51200', 
            ]);

            // Siapkan data untuk API
            $apiData = [
                't_pengaduan_masyarakat' => [
                    'pm_nama_tanpa_gelar' => $request->input('pm_nama_pelapor'),
                    'pm_nik_pengguna' => $request->input('pm_nik_pelapor'),
                    'pm_email_pengguna' => $request->input('pm_email_pelapor'),
                    'pm_no_hp_pengguna' => $request->input('pm_no_hp_pelapor'),
                    'pm_jenis_laporan' => $request->input('pm_jenis_laporan'),
                    'pm_yang_dilaporkan' => $request->input('pm_nama_terlapor'),
                    'pm_jabatan' => $request->input('pm_jabatan_terlapor'),
                    'pm_waktu_kejadian' => $request->input('pm_tanggal_kejadian'),
                    'pm_lokasi_kejadian' => $request->input('pm_lokasi_kejadian'),
                    'pm_kronologis_kejadian' => $request->input('pm_uraian_pengaduan'),
                    'pm_catatan_tambahan' => $request->input('pm_catatan'),
                ]
            ];

             // Array untuk file yang akan dikirim
            $files = [];

            // Upload KTP/NIK pengguna (untuk Admin)
            if ($request->hasFile('pm_upload_nik_pelapor')) {
                $files['pm_upload_nik_pengguna'] = $request->file('pm_upload_nik_pelapor');
            }

            // ✅ PERBAIKAN UTAMA: Upload bukti pendukung (WAJIB untuk semua user)
            if ($request->hasFile('pm_bukti_pendukung')) {
                $files['pm_bukti_pendukung'] = $request->file('pm_bukti_pendukung');
            } else {
                return redirect()->back()->with('error', 'File bukti pendukung wajib diupload!');
            }

            // Log data yang akan dikirim ke API
            Log::info('Data Laporan Masyarakat yang akan dikirim ke API:', [
                'data' => $apiData,
                'files' => array_keys($files)
            ]);

            // Kirim data ke API dengan file menggunakan multipart/form-data
            $response = $this->makeAuthenticatedRequest('auth/pengaduan-masyarakat/create', 'POST', $apiData, $files);

            if ($response->successful()) {
                $responseData = $response->json();
                Log::info('Laporan Pengaduan Masyarakat berhasil dikirim', [
                    'response' => $responseData
                ]);
                
                return redirect()->back()->with('success', 'Laporan Pengaduan Masyarakat berhasil dikirim!');
            } else {
                Log::error('API Error Response', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                
                $errorMessage = 'Gagal mengirim laporan Laporan Masyarakat. ';
                if ($response->status() === 422) {
                    $responseData = $response->json();
                    $errorMessage .= $responseData['message'] ?? 'Validasi gagal.';
                } else {
                    $errorMessage .= 'Silakan coba lagi.';
                }
                
                return redirect()->back()->with('error', $errorMessage);
            }

        } catch (\Exception $e) {
            Log::error('Error saat mengirim laporan Laporan Masyarakat', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }
}