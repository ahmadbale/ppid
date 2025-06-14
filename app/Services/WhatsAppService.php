<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Modules\Sisfo\App\Models\Log\WhatsAppModel;

class WhatsAppService
{
    private $baseUrl;
    private $token;
    private $enabled;
    private $timeout;

    public function __construct()
    {
        $this->baseUrl = config('services.whatsapp.base_url', env('WHATSAPP_FREE_BASE_URL'));
        $this->token = config('services.whatsapp.token', env('WHATSAPP_FREE_TOKEN'));
        $this->enabled = config('services.whatsapp.enabled', env('WHATSAPP_FREE_ENABLED', true));
        $this->timeout = config('services.whatsapp.timeout', env('WHATSAPP_FREE_TIMEOUT', 30));
        
        Log::info('WhatsApp Service initialized with config:', [
            'base_url' => $this->baseUrl,
            'token' => $this->token ? 'Set' : 'Not Set',
            'enabled' => $this->enabled,
            'timeout' => $this->timeout
        ]);
    }

    /**
     * Kirim pesan WhatsApp - DIPERBAIKI DENGAN TOKEN
     */
    public function kirimPesan($nomorTujuan, $pesan, $status = 'Notifikasi')
    {
        // Cek apakah WhatsApp service diaktifkan
        if (!$this->enabled) {
            Log::info('WhatsApp service dinonaktifkan');
            return false;
        }

        $logEntry = null;

        try {
            // Format nomor telepon
            $nomorFormatted = $this->formatNomorTelepon($nomorTujuan);
            
            if (!$nomorFormatted) {
                Log::warning("Nomor WhatsApp tidak valid: {$nomorTujuan}");
                return false;
            }

            Log::info("Attempting to send WhatsApp to: {$nomorFormatted}");

            // Buat log sebelum mengirim
            $logEntry = WhatsAppModel::createData($status, $nomorFormatted, $pesan, 'Pending');

            if (!$logEntry) {
                Log::error('Failed to create WhatsApp log entry');
                return false;
            }

            Log::info("WhatsApp log created with ID: {$logEntry->log_whatsapp_id}");

            // Cek apakah WhatsApp server berjalan
            if (!$this->cekServerBerjalan()) {
                Log::warning('WhatsApp server tidak berjalan');
                if ($logEntry) {
                    $logEntry->updateDeliveryStatus('Error');
                }
                return false;
            }

            // Prepare headers - MENGGUNAKAN TOKEN
            $headers = [
                'Content-Type' => 'application/json',
            ];

            // Tambahkan token jika ada
            if (!empty($this->token)) {
                $headers['Authorization'] = 'Bearer ' . $this->token;
                // Atau jika server Anda menggunakan custom header
                $headers['X-Token'] = $this->token;
            }

            Log::info('Sending WhatsApp with headers:', $headers);

            // Kirim pesan melalui API
            $response = Http::timeout($this->timeout)
                ->withHeaders($headers)
                ->post($this->baseUrl . '/send-message', [
                    'number' => $nomorFormatted,
                    'message' => $pesan,
                    'token' => $this->token // Tambahkan token di body juga jika diperlukan
                ]);

            Log::info("WhatsApp API Response Status: " . $response->status());
            Log::info("WhatsApp API Response Body: " . $response->body());

            if ($response->successful()) {
                $responseData = $response->json();
                
                if (isset($responseData['success']) && $responseData['success']) {
                    // Update log sebagai berhasil
                    if ($logEntry) {
                        $logEntry->updateDeliveryStatus('Sent');
                    }
                    
                    Log::info("WhatsApp berhasil dikirim ke: {$nomorFormatted}");
                    return true;
                } else {
                    // API response successful tapi success = false
                    if ($logEntry) {
                        $logEntry->updateDeliveryStatus('Error');
                    }
                    
                    Log::error("WhatsApp API returned success=false untuk {$nomorFormatted}: " . $response->body());
                    return false;
                }
            } else {
                // Update log sebagai gagal
                if ($logEntry) {
                    $logEntry->updateDeliveryStatus('Error');
                }
                
                Log::error("Gagal mengirim WhatsApp ke {$nomorFormatted}. HTTP Status: {$response->status()}, Body: " . $response->body());
                return false;
            }

        } catch (\Exception $e) {
            // Update log sebagai error jika log entry sudah dibuat
            if ($logEntry) {
                $logEntry->updateDeliveryStatus('Error');
            }
            
            Log::error("Exception saat mengirim WhatsApp: " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());
            return false;
        }
    }

    /**
     * Cek apakah server WhatsApp berjalan - DENGAN TOKEN
     */
    private function cekServerBerjalan()
    {
        try {
            // Prepare headers
            $headers = [
                'Content-Type' => 'application/json',
            ];

            // Tambahkan token jika ada
            if (!empty($this->token)) {
                $headers['Authorization'] = 'Bearer ' . $this->token;
                $headers['X-Token'] = $this->token;
            }

            $response = Http::timeout(5)
                ->withHeaders($headers)
                ->get($this->baseUrl . '/status');
                
            Log::info('Server status check response:', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
                
            return $response->successful();
        } catch (\Exception $e) {
            Log::error("Error checking WhatsApp server status: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Cek status koneksi WhatsApp - DENGAN TOKEN
     */
    public function cekStatus()
    {
        try {
            // Prepare headers
            $headers = [
                'Content-Type' => 'application/json',
            ];

            // Tambahkan token jika ada
            if (!empty($this->token)) {
                $headers['Authorization'] = 'Bearer ' . $this->token;
                $headers['X-Token'] = $this->token;
            }

            $response = Http::timeout(10)
                ->withHeaders($headers)
                ->get($this->baseUrl . '/status');

            Log::info('WhatsApp status check:', [
                'url' => $this->baseUrl . '/status',
                'status' => $response->status(),
                'success' => $response->successful()
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error("Error cek status WhatsApp: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Format nomor telepon Indonesia
     */
    private function formatNomorTelepon($nomor)
    {
        // Hapus semua karakter non-digit
        $nomor = preg_replace('/[^0-9]/', '', $nomor);
        
        // Cek apakah nomor kosong setelah cleaning
        if (empty($nomor)) {
            return false;
        }

        // Format nomor Indonesia
        if (substr($nomor, 0, 1) === '0') {
            // Jika dimulai dengan 0, ganti dengan 62
            $nomor = '62' . substr($nomor, 1);
        } elseif (substr($nomor, 0, 2) !== '62') {
            // Jika tidak dimulai dengan 62, tambahkan 62
            $nomor = '62' . $nomor;
        }

        // Validasi panjang nomor (minimal 10 digit setelah 62)
        if (strlen($nomor) < 12 || strlen($nomor) > 15) {
            return false;
        }

        return $nomor;
    }

    /**
     * Generate template pesan WhatsApp untuk verifikasi
     */
    public function generatePesanVerifikasi($nama, $status, $kategori, $informasiYangDibutuhkan, $alasanPenolakan = null)
    {
        $template = "🏛️ *PPID POLINEMA* 🏛️\n\n";
        $template .= "Halo *{$nama}*,\n\n";
        
        if ($status === 'Disetujui') {
            $template .= "✅ *PERMOHONAN DISETUJUI*\n\n";
            $template .= "Permohonan informasi Anda telah *DISETUJUI* pada tahap verifikasi.\n\n";
            $template .= "📋 *Detail Permohonan:*\n";
            $template .= "• Kategori: {$kategori}\n";
            $template .= "• Informasi: {$informasiYangDibutuhkan}\n\n";
            $template .= "📝 Pengajuan Anda sedang dalam proses review untuk mempertimbangkan tindak lanjut.\n\n";
        } else {
            $template .= "❌ *PERMOHONAN DITOLAK*\n\n";
            $template .= "Mohon maaf, permohonan informasi Anda *DITOLAK*.\n\n";
            $template .= "📋 *Detail Permohonan:*\n";
            $template .= "• Kategori: {$kategori}\n";
            $template .= "• Informasi: {$informasiYangDibutuhkan}\n";
            $template .= "• Alasan: {$alasanPenolakan}\n\n";
        }
        
        $template .= "📞 *Butuh Bantuan?*\n";
        $template .= "• Email: ppid@polinema.ac.id\n";
        $template .= "• Telepon: 085804049240\n";
        $template .= "• Website: ppid.polinema.ac.id\n\n";
        $template .= "Keterangan:\n";
        $template .= "Politeknik Negeri Malang\n";
        $template .= "Pesan otomatis dari Sistem PPID";

        return $template;
    }

    /**
     * Test kirim pesan untuk debugging - DENGAN TOKEN
     */
    public function testKirimPesan($nomorTujuan)
    {
        $pesanTest = "🧪 *TEST PESAN*\n\nIni adalah pesan test dari sistem PPID POLINEMA.\n\nWaktu: " . date('Y-m-d H:i:s') . "\nToken: " . ($this->token ? 'Available' : 'Not Available');
        
        return $this->kirimPesan($nomorTujuan, $pesanTest, 'Test');
    }
}