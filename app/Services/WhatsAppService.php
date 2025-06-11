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
    }

    /**
     * Kirim pesan WhatsApp
     */
    public function kirimPesan($nomorTujuan, $pesan, $status = 'Notifikasi')
    {
        // Cek apakah WhatsApp service diaktifkan
        if (!$this->enabled) {
            Log::info('WhatsApp service dinonaktifkan');
            return false;
        }

        try {
            // Format nomor telepon
            $nomorFormatted = $this->formatNomorTelepon($nomorTujuan);
            
            if (!$nomorFormatted) {
                Log::warning("Nomor WhatsApp tidak valid: {$nomorTujuan}");
                return false;
            }

            // Buat log sebelum mengirim
            $logEntry = WhatsAppModel::createData($status, $nomorFormatted, $pesan, 'Pending');

            // Kirim pesan melalui API
            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->token,
                    'Content-Type' => 'application/json',
                ])
                ->post($this->baseUrl . '/send-message', [
                    'number' => $nomorFormatted,
                    'message' => $pesan
                ]);

            if ($response->successful()) {
                // Update log sebagai berhasil
                if ($logEntry) {
                    $logEntry->update(['log_whatsapp_delivery_status' => 'Sent']);
                }
                
                Log::info("WhatsApp berhasil dikirim ke: {$nomorFormatted}");
                return true;
            } else {
                // Update log sebagai gagal
                if ($logEntry) {
                    $logEntry->update(['log_whatsapp_delivery_status' => 'failed']);
                }
                
                Log::error("Gagal mengirim WhatsApp ke {$nomorFormatted}: " . $response->body());
                return false;
            }

        } catch (\Exception $e) {
            // Update log sebagai error
            if (isset($logEntry) && $logEntry) {
                $logEntry->update(['log_whatsapp_delivery_status' => 'Error']);
            }
            
            Log::error("Error saat mengirim WhatsApp: " . $e->getMessage());
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
     * Cek status koneksi WhatsApp
     */
    public function cekStatus()
    {
        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->token,
                ])
                ->get($this->baseUrl . '/status');

            return $response->successful();
        } catch (\Exception $e) {
            Log::error("Error cek status WhatsApp: " . $e->getMessage());
            return false;
        }
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
        $template .= "• Telepon: (0341) 404424\n";
        $template .= "• Website: ppid.polinema.ac.id\n\n";
        $template .= "---\n";
        $template .= "Politeknik Negeri Malang\n";
        $template .= "Pesan otomatis dari Sistem PPID";

        return $template;
    }
}