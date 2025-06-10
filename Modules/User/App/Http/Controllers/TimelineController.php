<?php

namespace Modules\User\App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Services\JwtTokenService;
class TimelineController extends Controller
{

     // tambahan kinboy 
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
    private function getTimelineData($kategoriId)
    {
        try {
            $response = $this->makeAuthenticatedRequest('public/getDataTimeline');
            
            if ($response->successful()) {
                $data = $response->json('data');
                
                // Find the specific category
                $category = collect($data)->firstWhere('kategori_form_id', $kategoriId);
                
                if ($category && !empty($category['timeline'])) {
                    $timeline = $category['timeline'][0];
                    
                    // Transform langkah into steps format
                    $steps = collect($timeline['langkah'])->map(function ($item, $index) {
                        return [
                            'number' => (string)($index + 1),
                            'text' => $item['deskripsi'],
                            'position' => ($index % 2 === 0) ? 'right' : 'left'
                        ];
                    })->all();

                    return [
                        'title' => $category['nama_kategori_form'],
                        'titlemekanisme' => $timeline['judul'],
                        'steps' => $steps
                    ];
                }
            }
            
            Log::error('Failed to fetch timeline data', [
                'kategori_id' => $kategoriId,
                'status' => $response->status(),
                'response' => $response->json()
            ]);
            
            return null;
            
        } catch (\Exception $e) {
            Log::error('Error fetching timeline data', [
                'kategori_id' => $kategoriId,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return null;
        }
    }

    public function permohonan_informasi()
    {
        $data = $this->getTimelineData(1);
        
        if (!$data) {
            // Fallback data if API fails
            $data = $this->getFallbackData('permohonan_informasi');
        }

        $description = "E-Form ini digunakan untuk mengajukan permohonan akses informasi publik di Politeknik Negeri Malang<br>sesuai dengan ketentuan yang berlaku.";
        
        return view('user::timeline_informasi', array_merge($data, ['description' => $description]));
    }

    public function pernyataan_keberatan()
    {
        $data = $this->getTimelineData(2);
        
        if (!$data) {
            $data = $this->getFallbackData('pernyataan_keberatan');
        }

        $description = "E-Form Pengajuan Keberatan atas Permohonan Informasi di Lingkungan Politeknik Negeri Malang<br>Pengajuan Keberatan Dapat Dilakukan oleh Diri Sendiri atau Atas Permohonan Orang Lain.";
        
        return view('user::timeline_keberatan', array_merge($data, ['description' => $description]));
    }

    public function pengaduan_masyarakat()
    {
        $data = $this->getTimelineData(3);
        
        if (!$data) {
            $data = $this->getFallbackData('pengaduan_masyarakat');
        }

        $description = "E-Form ini digunakan untuk menyampaikan keluhan, aspirasi, atau laporan terkait pelayanan publik di<br>Politeknik Negeri Malang.";
        
        return view('user::timeline_pengaduan-masyarakat', array_merge($data, ['description' => $description]));
    }

    public function wbs()
    {
        $data = $this->getTimelineData(4);
        
        if (!$data) {
            $data = $this->getFallbackData('wbs');
        }

        $description = "E-Form ini digunakan untuk melaporkan dugaan pelanggaran kode etik, penyalahgunaan wewenang,<br>korupsi, atau tindakan tidak etis lainnya.";
        
        return view('user::timeline_wbs', array_merge($data, ['description' => $description]));
    }

    public function sarana_prasarana()
{
    $data = $this->getTimelineData(5);
    
    if (!$data) {
        $data = $this->getFallbackData('sarana_prasarana');
    }

    $description = "E-Form ini digunakan untuk mengajukan permohonan perawatan sarana dan prasarana di lingkungan<br>Politeknik Negeri Malang.";
    
    return view('user::timeline_sarpras', array_merge($data, ['description' => $description]));
}

    private function getFallbackData($type)
    {
        // Fallback data in case API fails
        $fallbackData = [
            'permohonan_informasi' => [
                'title' => 'Permohonan Informasi Publik',
                'titlemekanisme' => 'Mekanisme Permohonan Informasi Publik',
                'steps' => [
                    [
                        'number' => '1',
                        'text' => 'Ini permohonan informasi Pemohon mengajukan keberatan melalui formulir yang tersedia',
                        'position' => 'right'
                    ],
                    [
                        'number' => '2',
                        'text' => 'Petugas PPID Seksi Aduan Masyarakat menerima dan mencatat permohonan keberatan',
                        'position' => 'left'
                    ],
                    [
                        'number' => '3',
                        'text' => 'Seksi Aduan Masyarakat meneruskan permohonan keberatan kepada PPID Pusat untuk ditindaklanjuti',
                        'position' => 'right'
                    ],
                    [
                        'number' => '4',
                        'text' => 'PPID Pusat menyampaikan permohonan keberatan kepada atasan',
                        'position' => 'left'
                    ],
                    [
                        'number' => '5',
                        'text' => 'Atasan PPID pusat menentukan untuk menggugurkan atau menyetujui keputusan PPID',
                        'position' => 'right'
                    ],
                    [
                        'number' => '6',
                        'text' => 'Keputusan akhir mengenai keberatan disampaikan secara tertulis kepada pemohon',
                        'position' => 'left'
                    ]
                ]
            ],
            'pernyataan_keberatan' => [
                'title' => 'Pernyataan Keberatan',
                'titlemekanisme' => 'Mekanisme Pernyataan Keberatan',
                'steps' => [
                    [
                        'number' => '1',
                        'text' => 'Pemohon mengajukan keberatan melalui formulir yang tersedia',
                        'position' => 'right'
                    ],
                    [
                        'number' => '2',
                        'text' => 'Verifikasi pernyataan keberatan oleh petugas PPID',
                        'position' => 'left'
                    ],
                    [
                        'number' => '3',
                        'text' => 'Proses penyelesaian keberatan',
                        'position' => 'right'
                    ],
                    [
                        'number' => '4',
                        'text' => 'Penyampaian tanggapan keberatan kepada pemohon',
                        'position' => 'left'
                    ]
                ]
            ],
            'pengaduan_masyarakat' => [
                'title' => 'Pengaduan Masyarakat',
                'titlemekanisme' => 'Mekanisme Pengaduan Masyarakat',
                'steps' => [
                    [
                        'number' => '1',
                        'text' => 'Mengisi formulir pengaduan masyarakat',
                        'position' => 'right'
                    ],
                    [
                        'number' => '2',
                        'text' => 'Verifikasi dan penelaahan pengaduan',
                        'position' => 'left'
                    ],
                    [
                        'number' => '3',
                        'text' => 'Proses tindak lanjut pengaduan',
                        'position' => 'right'
                    ],
                    [
                        'number' => '4',
                        'text' => 'Penyampaian hasil tindak lanjut kepada pelapor',
                        'position' => 'left'
                    ]
                ]
            ],
            'wbs' => [
                'title' => 'Whistleblowing System',
                'titlemekanisme' => 'Mekanisme Pelaporan Whistleblowing System',
                'steps' => [
                    [
                        'number' => '1',
                        'text' => 'Mengisi formulir pelaporan WBS',
                        'position' => 'right'
                    ],
                    [
                        'number' => '2',
                        'text' => 'Verifikasi dan penelaahan laporan',
                        'position' => 'left'
                    ],
                    [
                        'number' => '3',
                        'text' => 'Investigasi dan tindak lanjut',
                        'position' => 'right'
                    ],
                    [
                        'number' => '4',
                        'text' => 'Penyampaian hasil investigasi dan tindakan yang diambil',
                        'position' => 'left'
                    ],
                    [
                        'number' => '5',
                        'text' => 'Perlindungan terhadap pelapor',
                        'position' => 'right'
                    ]
                ]
                    ],
                     'sarana_prasarana' => [
            'title' => 'Permohonan Perawatan Sarana Prasarana',
            'titlemekanisme' => 'Prosedure Permohonan Perawatan Sarana Prasarana',
            'steps' => [
                [
                    'number' => '1',
                    'text' => 'Melihat Ketentuan Pelaporan Mengisi Form Permohonan Perawatan Sarana Prasarana',
                    'position' => 'right'
                ],
                [
                    'number' => '2',
                    'text' => 'Mengisi Form Permohonan Perawatan Sarana Prasarana',
                    'position' => 'left'
                ],
                [
                    'number' => '3',
                    'text' => 'Menerima Hasil',
                    'position' => 'right'
                ]
            ]
        ]
        ];

        return $fallbackData[$type] ?? [
            'title' => 'Data tidak tersedia',
            'titlemekanisme' => 'Mekanisme tidak tersedia',
            'steps' => [
                [
                    'number' => '1',
                    'text' => 'Data tidak tersedia saat ini. Silakan coba lagi nanti.',
                    'position' => 'right'
                ]
            ]
        ];
    }
}