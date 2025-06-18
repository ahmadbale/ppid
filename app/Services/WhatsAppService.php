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

    public function generatePesanVerifikasiKeberatan($nama, $status, $kategori, $alasanPengajuanKeberatan, $kasusPosisi, $alasanPenolakan = null)
    {
        $template = "🏛️ *PPID POLINEMA* 🏛️\n\n";
        $template .= "Halo *{$nama}*,\n\n";

        if ($status === 'Disetujui') {
            $template .= "✅ *PERNYATAAN KEBERATAN DISETUJUI*\n\n";
            $template .= "Pernyataan keberatan Anda telah *DISETUJUI* pada tahap verifikasi.\n\n";
            $template .= "📋 *Detail Pernyataan Keberatan:*\n";
            $template .= "• Kategori: {$kategori}\n";
            $template .= "• Alasan Keberatan: {$alasanPengajuanKeberatan}\n";
            $template .= "• Kasus Posisi: {$kasusPosisi}\n\n";
            $template .= "📝 Pengajuan keberatan Anda sedang dalam proses review untuk mempertimbangkan tindak lanjut.\n\n";
        } else {
            $template .= "❌ *PERNYATAAN KEBERATAN DITOLAK*\n\n";
            $template .= "Mohon maaf, pernyataan keberatan Anda *DITOLAK*.\n\n";
            $template .= "📋 *Detail Pernyataan Keberatan:*\n";
            $template .= "• Kategori: {$kategori}\n";
            $template .= "• Alasan Keberatan: {$alasanPengajuanKeberatan}\n";
            $template .= "• Kasus Posisi: {$kasusPosisi}\n";
            $template .= "• Alasan Penolakan: {$alasanPenolakan}\n\n";
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

    public function generatePesanVerifikasiPengaduan($nama, $status, $jenisLaporan, $yangDilaporkan, $lokasiKejadian, $waktuKejadian, $alasanPenolakan = null)
    {
        $template = "🏛️ *PPID POLINEMA* 🏛️\n\n";
        $template .= "Halo *{$nama}*,\n\n";

        // Format tanggal menggunakan date() dan strtotime()
        $tanggalKejadian = date('d M Y', strtotime($waktuKejadian));

        if ($status === 'Disetujui') {
            $template .= "✅ *PENGADUAN MASYARAKAT DISETUJUI*\n\n";
            $template .= "Pengaduan masyarakat Anda telah *DISETUJUI* pada tahap verifikasi.\n\n";
            $template .= "📋 *Detail Pengaduan:*\n";
            $template .= "• Jenis Laporan: {$jenisLaporan}\n";
            $template .= "• Yang Dilaporkan: {$yangDilaporkan}\n";
            $template .= "• Lokasi Kejadian: {$lokasiKejadian}\n";
            $template .= "• Waktu Kejadian: {$tanggalKejadian}\n\n";
            $template .= "📝 Pengaduan Anda sedang dalam proses review untuk mempertimbangkan tindak lanjut.\n\n";
        } else {
            $template .= "❌ *PENGADUAN MASYARAKAT DITOLAK*\n\n";
            $template .= "Mohon maaf, pengaduan masyarakat Anda *DITOLAK*.\n\n";
            $template .= "📋 *Detail Pengaduan:*\n";
            $template .= "• Jenis Laporan: {$jenisLaporan}\n";
            $template .= "• Yang Dilaporkan: {$yangDilaporkan}\n";
            $template .= "• Lokasi Kejadian: {$lokasiKejadian}\n";
            $template .= "• Waktu Kejadian: {$tanggalKejadian}\n";
            $template .= "• Alasan Penolakan: {$alasanPenolakan}\n\n";
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

    public function generatePesanVerifikasiWBS($nama, $status, $jenisLaporan, $yangDilaporkan, $jabatan, $lokasiKejadian, $waktuKejadian, $alasanPenolakan = null)
    {
        $template = "🏛️ *PPID POLINEMA* 🏛️\n\n";
        $template .= "Halo *{$nama}*,\n\n";

        // Format tanggal menggunakan date() dan strtotime()
        $tanggalKejadian = date('d M Y', strtotime($waktuKejadian));

        if ($status === 'Disetujui') {
            $template .= "✅ *WHISTLE BLOWING SYSTEM DISETUJUI*\n\n";
            $template .= "Laporan Whistle Blowing System Anda telah *DISETUJUI* pada tahap verifikasi.\n\n";
            $template .= "📋 *Detail Laporan:*\n";
            $template .= "• Jenis Laporan: {$jenisLaporan}\n";
            $template .= "• Yang Dilaporkan: {$yangDilaporkan}\n";
            $template .= "• Jabatan: {$jabatan}\n";
            $template .= "• Lokasi Kejadian: {$lokasiKejadian}\n";
            $template .= "• Waktu Kejadian: {$tanggalKejadian}\n\n";
            $template .= "📝 Laporan Anda sedang dalam proses review untuk mempertimbangkan tindak lanjut.\n\n";
        } else {
            $template .= "❌ *WHISTLE BLOWING SYSTEM DITOLAK*\n\n";
            $template .= "Mohon maaf, laporan Whistle Blowing System Anda *DITOLAK*.\n\n";
            $template .= "📋 *Detail Laporan:*\n";
            $template .= "• Jenis Laporan: {$jenisLaporan}\n";
            $template .= "• Yang Dilaporkan: {$yangDilaporkan}\n";
            $template .= "• Jabatan: {$jabatan}\n";
            $template .= "• Lokasi Kejadian: {$lokasiKejadian}\n";
            $template .= "• Waktu Kejadian: {$tanggalKejadian}\n";
            $template .= "• Alasan Penolakan: {$alasanPenolakan}\n\n";
        }

        $template .= "🔒 *Kerahasiaan Terjamin*\n";
        $template .= "Identitas Anda akan dijaga kerahasiaannya sesuai dengan kebijakan WBS.\n\n";

        $template .= "📞 *Butuh Bantuan?*\n";
        $template .= "• Email: ppid@polinema.ac.id\n";
        $template .= "• Telepon: 085804049240\n";
        $template .= "• Website: ppid.polinema.ac.id\n\n";
        $template .= "Keterangan:\n";
        $template .= "Politeknik Negeri Malang\n";
        $template .= "Pesan otomatis dari Sistem PPID";

        return $template;
    }

    public function generatePesanVerifikasiPerawatan($nama, $status, $unitKerja, $perawatanYangDiusulkan, $lokasiPerawatan, $keluhanKerusakan, $alasanPenolakan = null)
    {
        $template = "🏛️ *PPID POLINEMA* 🏛️\n\n";
        $template .= "Halo *{$nama}*,\n\n";

        if ($status === 'Disetujui') {
            $template .= "✅ *PERMOHONAN PERAWATAN DISETUJUI*\n\n";
            $template .= "Permohonan perawatan sarana prasarana Anda telah *DISETUJUI* pada tahap verifikasi.\n\n";
            $template .= "📋 *Detail Permohonan:*\n";
            $template .= "• Unit Kerja: {$unitKerja}\n";
            $template .= "• Perawatan Diusulkan: {$perawatanYangDiusulkan}\n";
            $template .= "• Lokasi Perawatan: {$lokasiPerawatan}\n";
            $template .= "• Keluhan: {$keluhanKerusakan}\n\n";
            $template .= "🔧 Permohonan Anda sedang dalam proses review untuk mempertimbangkan tindak lanjut perawatan.\n\n";
        } else {
            $template .= "❌ *PERMOHONAN PERAWATAN DITOLAK*\n\n";
            $template .= "Mohon maaf, permohonan perawatan sarana prasarana Anda *DITOLAK*.\n\n";
            $template .= "📋 *Detail Permohonan:*\n";
            $template .= "• Unit Kerja: {$unitKerja}\n";
            $template .= "• Perawatan Diusulkan: {$perawatanYangDiusulkan}\n";
            $template .= "• Lokasi Perawatan: {$lokasiPerawatan}\n";
            $template .= "• Keluhan: {$keluhanKerusakan}\n";
            $template .= "• Alasan Penolakan: {$alasanPenolakan}\n\n";
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

    public function generatePesanReviewPermohonanInformasi($nama, $status, $kategori, $informasiYangDibutuhkan, $jawaban = null, $alasanPenolakan = null, $strategiPengiriman = null)
    {
        $template = "🏛️ *PPID POLINEMA* 🏛️\n\n";
        $template .= "Halo *{$nama}*,\n\n";

        if ($status === 'Disetujui') {
            $template .= "✅ *PERMOHONAN INFORMASI DISETUJUI*\n\n";
            $template .= "Permohonan informasi Anda telah *SELESAI DIPROSES* dan disetujui.\n\n";
            $template .= "📋 *Detail Permohonan:*\n";
            $template .= "• Kategori: {$kategori}\n";
            $template .= "• Informasi Diminta: {$informasiYangDibutuhkan}\n\n";

            // IMPLEMENTASI STRATEGI PENGIRIMAN - DIPERBAIKI
            if ($strategiPengiriman && isset($strategiPengiriman['metode'])) {
                $template .= "📝 *Jawaban:*\n";

                switch ($strategiPengiriman['metode']) {
                    case 'kirim_file':
                        // File kecil ≤ 9MB - akan dikirim via WhatsApp
                        $template .= "📎 Dokumen jawaban dikirim bersamaan dengan pesan ini.\n";
                        $template .= "📊 Ukuran file: {$strategiPengiriman['ukuran_mb']} MB\n\n";
                        break;

                    case 'notif_file_besar':
                        // File besar > 9MB - hanya notifikasi
                        $template .= "📎 *Jawaban yang diberikan berupa media yang ukurannya terlalu besar (melebihi 10 MB) silahkan check email anda untuk melihat jawabannya*\n";
                        $template .= "📊 Ukuran file: {$strategiPengiriman['ukuran_mb']} MB\n\n";
                        break;

                    case 'file_tidak_ada':
                        // File tidak ditemukan
                        $template .= "⚠️ Dokumen jawaban tidak dapat dikirim via WhatsApp\n";
                        $template .= "📧 *Silakan cek EMAIL Anda untuk melihat jawaban lengkap*\n\n";
                        break;

                    case 'pesan_biasa':
                    default:
                        // Jawaban berupa teks
                        if ($jawaban) {
                            // Batasi panjang jawaban untuk WhatsApp
                            $jawabanPendek = strlen($jawaban) > 200 ? substr($jawaban, 0, 200) . '...' : $jawaban;
                            $template .= "{$jawabanPendek}\n\n";
                            if (strlen($jawaban) > 200) {
                                $template .= "📧 Jawaban lengkap dapat dilihat di email.\n\n";
                            }
                        }
                        break;
                }
            } else {
                // Fallback untuk backward compatibility
                if ($jawaban) {
                    if (preg_match('/\.(pdf|doc|docx|jpg|jpeg|png|gif)$/i', $jawaban)) {
                        $template .= "📎 Dokumen jawaban telah dikirim melalui email.\n\n";
                    } else {
                        $jawabanPendek = strlen($jawaban) > 200 ? substr($jawaban, 0, 200) . '...' : $jawaban;
                        $template .= "{$jawabanPendek}\n\n";
                        if (strlen($jawaban) > 200) {
                            $template .= "📧 Jawaban lengkap dapat dilihat di email.\n\n";
                        }
                    }
                }
            }
        } else {
            $template .= "❌ *PERMOHONAN INFORMASI DITOLAK*\n\n";
            $template .= "Mohon maaf, permohonan informasi Anda *TIDAK DAPAT DIPROSES*.\n\n";
            $template .= "📋 *Detail Permohonan:*\n";
            $template .= "• Kategori: {$kategori}\n";
            $template .= "• Informasi Diminta: {$informasiYangDibutuhkan}\n";
            $template .= "• Alasan Penolakan: {$alasanPenolakan}\n\n";
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
     * Kirim pesan dengan file attachment - DIPERBAIKI DENGAN VALIDASI UKURAN
     */
    public function kirimPesanDenganFile($nomorTujuan, $pesan, $filePath = null, $status = 'Notifikasi')
    {
        // Cek apakah WhatsApp service diaktifkan
        if (!$this->enabled) {
            Log::info('WhatsApp service dinonaktifkan');
            return false;
        }

        $logEntry = null;

        try {
            $nomorFormatted = $this->formatNomorTelepon($nomorTujuan);

            if (!$nomorFormatted) {
                Log::warning("Nomor WhatsApp tidak valid: {$nomorTujuan}");
                return false;
            }

            // Buat log sebelum mengirim
            $logEntry = WhatsAppModel::createData($status, $nomorFormatted, $pesan, 'Pending');

            if (!$logEntry) {
                Log::error('Failed to create WhatsApp log entry');
                return false;
            }

            // Cek apakah WhatsApp server berjalan
            if (!$this->cekServerBerjalan()) {
                Log::warning('WhatsApp server tidak berjalan');
                if ($logEntry) {
                    $logEntry->updateDeliveryStatus('Error');
                }
                return false;
            }

            // VALIDASI UKURAN FILE - DIPERBAIKI: Gunakan batas 9MB untuk antisipasi
            if ($filePath && file_exists($filePath)) {
                $ukuranByte = filesize($filePath);
                $ukuranMB = round($ukuranByte / 1024 / 1024, 2);
                $batasUkuranMB = 9; // Turunkan dari 10MB ke 9MB

                Log::info("File size check: {$ukuranMB} MB (limit: {$batasUkuranMB} MB)");

                if ($ukuranMB > $batasUkuranMB) {
                    // File terlalu besar - fallback ke pesan tanpa file
                    Log::warning("File terlalu besar untuk WhatsApp: {$ukuranMB} MB, mengirim pesan fallback");

                    $pesanFallback = $pesan . "\n\n📎 *Catatan:* Dokumen jawaban ({$ukuranMB} MB) terlalu besar untuk WhatsApp.\n📧 Silakan cek email Anda untuk melihat dokumen lengkap.";

                    return $this->kirimPesan($nomorFormatted, $pesanFallback, $status);
                }
            }

            // Prepare headers
            $headers = [
                'Content-Type' => 'application/json',
            ];

            if (!empty($this->token)) {
                $headers['Authorization'] = 'Bearer ' . $this->token;
                $headers['X-Token'] = $this->token;
            }

            // Prepare data
            $requestData = [
                'number' => $nomorFormatted,
                'message' => $pesan,
                'token' => $this->token
            ];

            // Tambahkan file jika ada dan ukurannya valid
            if ($filePath && file_exists($filePath)) {
                // Convert file ke base64
                $fileContent = base64_encode(file_get_contents($filePath));
                $fileName = basename($filePath);
                $mimeType = mime_content_type($filePath);

                $requestData['media'] = [
                    'data' => $fileContent,
                    'mimetype' => $mimeType,
                    'filename' => $fileName
                ];

                Log::info("Sending WhatsApp with file: {$fileName} ({$mimeType})");
            }

            // Kirim pesan melalui API
            $response = Http::timeout($this->timeout)
                ->withHeaders($headers)
                ->post($this->baseUrl . '/send-message-with-media', $requestData);

            Log::info("WhatsApp API Response Status: " . $response->status());
            Log::info("WhatsApp API Response Body: " . $response->body());

            if ($response->successful()) {
                $responseData = $response->json();

                if (isset($responseData['success']) && $responseData['success']) {
                    if ($logEntry) {
                        $logEntry->updateDeliveryStatus('Sent');
                    }
                    Log::info("WhatsApp dengan file berhasil dikirim ke: {$nomorFormatted}");
                    return true;
                } else {
                    if ($logEntry) {
                        $logEntry->updateDeliveryStatus('Error');
                    }
                    Log::error("WhatsApp API returned success=false untuk {$nomorFormatted}: " . $response->body());
                    return false;
                }
            } else {
                if ($logEntry) {
                    $logEntry->updateDeliveryStatus('Error');
                }
                Log::error("Gagal mengirim WhatsApp ke {$nomorFormatted}. HTTP Status: {$response->status()}, Body: " . $response->body());
                return false;
            }
        } catch (\Exception $e) {
            if ($logEntry) {
                $logEntry->updateDeliveryStatus('Error');
            }
            Log::error("Exception saat mengirim WhatsApp dengan file: " . $e->getMessage());
            return false;
        }
    }

    public function generatePesanReviewPernyataanKeberatan($nama, $status, $kategori, $alasanPengajuanKeberatan, $kasusPosisi, $jawaban = null, $alasanPenolakan = null, $strategiPengiriman = null)
    {
        $template = "🏛️ *PPID POLINEMA* 🏛️\n\n";
        $template .= "Halo *{$nama}*,\n\n";

        if ($status === 'Disetujui') {
            $template .= "✅ *PERNYATAAN KEBERATAN DISETUJUI*\n\n";
            $template .= "Pernyataan keberatan Anda telah *SELESAI DIPROSES* dan disetujui.\n\n";
            $template .= "📋 *Detail Pernyataan Keberatan:*\n";
            $template .= "• Kategori: {$kategori}\n";
            $template .= "• Alasan Pengajuan: {$alasanPengajuanKeberatan}\n";
            $template .= "• Kasus Posisi: {$kasusPosisi}\n\n";

            // IMPLEMENTASI STRATEGI PENGIRIMAN - SAMA SEPERTI PERMOHONAN INFORMASI
            if ($strategiPengiriman && isset($strategiPengiriman['metode'])) {
                $template .= "📝 *Jawaban:*\n";

                switch ($strategiPengiriman['metode']) {
                    case 'kirim_file':
                        // File kecil ≤ 9MB - akan dikirim via WhatsApp
                        $template .= "📎 Dokumen jawaban dikirim bersamaan dengan pesan ini.\n";
                        $template .= "📊 Ukuran file: {$strategiPengiriman['ukuran_mb']} MB\n\n";
                        break;

                    case 'notif_file_besar':
                        // File besar > 9MB - hanya notifikasi
                        $template .= "📎 *Jawaban yang diberikan berupa media yang ukurannya terlalu besar (melebihi 10 MB) silahkan check email anda untuk melihat jawabannya*\n";
                        $template .= "📊 Ukuran file: {$strategiPengiriman['ukuran_mb']} MB\n\n";
                        break;

                    case 'file_tidak_ada':
                        // File tidak ditemukan
                        $template .= "⚠️ Dokumen jawaban tidak dapat dikirim via WhatsApp\n";
                        $template .= "📧 *Silakan cek EMAIL Anda untuk melihat jawaban lengkap*\n\n";
                        break;

                    case 'pesan_biasa':
                    default:
                        // Jawaban berupa teks
                        if ($jawaban) {
                            // Batasi panjang jawaban untuk WhatsApp
                            $jawabanPendek = strlen($jawaban) > 200 ? substr($jawaban, 0, 200) . '...' : $jawaban;
                            $template .= "{$jawabanPendek}\n\n";
                            if (strlen($jawaban) > 200) {
                                $template .= "📧 Jawaban lengkap dapat dilihat di email.\n\n";
                            }
                        }
                        break;
                }
            } else {
                // Fallback untuk backward compatibility
                if ($jawaban) {
                    if (preg_match('/\.(pdf|doc|docx|jpg|jpeg|png|gif)$/i', $jawaban)) {
                        $template .= "📎 Dokumen jawaban telah dikirim melalui email.\n\n";
                    } else {
                        $jawabanPendek = strlen($jawaban) > 200 ? substr($jawaban, 0, 200) . '...' : $jawaban;
                        $template .= "{$jawabanPendek}\n\n";
                        if (strlen($jawaban) > 200) {
                            $template .= "📧 Jawaban lengkap dapat dilihat di email.\n\n";
                        }
                    }
                }
            }
        } else {
            $template .= "❌ *PERNYATAAN KEBERATAN DITOLAK*\n\n";
            $template .= "Mohon maaf, pernyataan keberatan Anda *TIDAK DAPAT DIPROSES*.\n\n";
            $template .= "📋 *Detail Pernyataan Keberatan:*\n";
            $template .= "• Kategori: {$kategori}\n";
            $template .= "• Alasan Pengajuan: {$alasanPengajuanKeberatan}\n";
            $template .= "• Kasus Posisi: {$kasusPosisi}\n";
            $template .= "• Alasan Penolakan: {$alasanPenolakan}\n\n";
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

    public function generatePesanReviewPengaduanMasyarakat($nama, $status, $jenisLaporan, $yangDilaporkan, $lokasiKejadian, $waktuKejadian, $kronologisKejadian, $jawaban = null, $alasanPenolakan = null, $strategiPengiriman = null)
    {
        $template = "🏛️ *PPID POLINEMA* 🏛️\n\n";
        $template .= "Halo *{$nama}*,\n\n";

        // Format tanggal menggunakan date() dan strtotime()
        $tanggalKejadian = date('d M Y', strtotime($waktuKejadian));

        if ($status === 'Disetujui') {
            $template .= "✅ *PENGADUAN MASYARAKAT DISETUJUI*\n\n";
            $template .= "Pengaduan masyarakat Anda telah *SELESAI DIPROSES* dan disetujui.\n\n";
            $template .= "📋 *Detail Pengaduan:*\n";
            $template .= "• Jenis Laporan: {$jenisLaporan}\n";
            $template .= "• Yang Dilaporkan: {$yangDilaporkan}\n";
            $template .= "• Lokasi Kejadian: {$lokasiKejadian}\n";
            $template .= "• Waktu Kejadian: {$tanggalKejadian}\n\n";

            // IMPLEMENTASI STRATEGI PENGIRIMAN - SAMA SEPERTI PERMOHONAN INFORMASI
            if ($strategiPengiriman && isset($strategiPengiriman['metode'])) {
                $template .= "📝 *Tanggapan:*\n";

                switch ($strategiPengiriman['metode']) {
                    case 'kirim_file':
                        // File kecil ≤ 9MB - akan dikirim via WhatsApp
                        $template .= "📎 Dokumen tanggapan dikirim bersamaan dengan pesan ini.\n";
                        $template .= "📊 Ukuran file: {$strategiPengiriman['ukuran_mb']} MB\n\n";
                        break;

                    case 'notif_file_besar':
                        // File besar > 9MB - hanya notifikasi
                        $template .= "📎 *Tanggapan yang diberikan berupa media yang ukurannya terlalu besar (melebihi 10 MB) silahkan check email anda untuk melihat tanggapannya*\n";
                        $template .= "📊 Ukuran file: {$strategiPengiriman['ukuran_mb']} MB\n\n";
                        break;

                    case 'file_tidak_ada':
                        // File tidak ditemukan
                        $template .= "⚠️ Dokumen tanggapan tidak dapat dikirim via WhatsApp\n";
                        $template .= "📧 *Silakan cek EMAIL Anda untuk melihat tanggapan lengkap*\n\n";
                        break;

                    case 'pesan_biasa':
                    default:
                        // Jawaban berupa teks
                        if ($jawaban) {
                            // Batasi panjang jawaban untuk WhatsApp
                            $jawabanPendek = strlen($jawaban) > 200 ? substr($jawaban, 0, 200) . '...' : $jawaban;
                            $template .= "{$jawabanPendek}\n\n";
                            if (strlen($jawaban) > 200) {
                                $template .= "📧 Tanggapan lengkap dapat dilihat di email.\n\n";
                            }
                        }
                        break;
                }
            } else {
                // Fallback untuk backward compatibility
                if ($jawaban) {
                    if (preg_match('/\.(pdf|doc|docx|jpg|jpeg|png|gif)$/i', $jawaban)) {
                        $template .= "📎 Dokumen tanggapan telah dikirim melalui email.\n\n";
                    } else {
                        $jawabanPendek = strlen($jawaban) > 200 ? substr($jawaban, 0, 200) . '...' : $jawaban;
                        $template .= "{$jawabanPendek}\n\n";
                        if (strlen($jawaban) > 200) {
                            $template .= "📧 Tanggapan lengkap dapat dilihat di email.\n\n";
                        }
                    }
                }
            }
        } else {
            $template .= "❌ *PENGADUAN MASYARAKAT DITOLAK*\n\n";
            $template .= "Mohon maaf, pengaduan masyarakat Anda *TIDAK DAPAT DIPROSES*.\n\n";
            $template .= "📋 *Detail Pengaduan:*\n";
            $template .= "• Jenis Laporan: {$jenisLaporan}\n";
            $template .= "• Yang Dilaporkan: {$yangDilaporkan}\n";
            $template .= "• Lokasi Kejadian: {$lokasiKejadian}\n";
            $template .= "• Waktu Kejadian: {$tanggalKejadian}\n";
            $template .= "• Alasan Penolakan: {$alasanPenolakan}\n\n";
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

    public function generatePesanReviewWBS($nama, $status, $jenisLaporan, $yangDilaporkan, $jabatan, $lokasiKejadian, $waktuKejadian, $kronologisKejadian, $jawaban = null, $alasanPenolakan = null, $strategiPengiriman = null)
    {
        $template = "🏛️ *PPID POLINEMA* 🏛️\n\n";
        $template .= "Halo *{$nama}*,\n\n";

        // Format tanggal menggunakan date() dan strtotime()
        $tanggalKejadian = date('d M Y', strtotime($waktuKejadian));

        if ($status === 'Disetujui') {
            $template .= "✅ *WHISTLE BLOWING SYSTEM DISETUJUI*\n\n";
            $template .= "Laporan Whistle Blowing System Anda telah *SELESAI DIPROSES* dan disetujui.\n\n";
            $template .= "📋 *Detail Laporan:*\n";
            $template .= "• Jenis Laporan: {$jenisLaporan}\n";
            $template .= "• Yang Dilaporkan: {$yangDilaporkan}\n";
            $template .= "• Jabatan: {$jabatan}\n";
            $template .= "• Lokasi Kejadian: {$lokasiKejadian}\n";
            $template .= "• Waktu Kejadian: {$tanggalKejadian}\n\n";

            // IMPLEMENTASI STRATEGI PENGIRIMAN - SAMA SEPERTI PERMOHONAN INFORMASI
            if ($strategiPengiriman && isset($strategiPengiriman['metode'])) {
                $template .= "📝 *Tanggapan:*\n";

                switch ($strategiPengiriman['metode']) {
                    case 'kirim_file':
                        // File kecil ≤ 9MB - akan dikirim via WhatsApp
                        $template .= "📎 Dokumen tanggapan dikirim bersamaan dengan pesan ini.\n";
                        $template .= "📊 Ukuran file: {$strategiPengiriman['ukuran_mb']} MB\n\n";
                        break;

                    case 'notif_file_besar':
                        // File besar > 9MB - hanya notifikasi
                        $template .= "📎 *Tanggapan yang diberikan berupa media yang ukurannya terlalu besar (melebihi 10 MB) silahkan check email anda untuk melihat tanggapannya*\n";
                        $template .= "📊 Ukuran file: {$strategiPengiriman['ukuran_mb']} MB\n\n";
                        break;

                    case 'file_tidak_ada':
                        // File tidak ditemukan
                        $template .= "⚠️ Dokumen tanggapan tidak dapat dikirim via WhatsApp\n";
                        $template .= "📧 *Silakan cek EMAIL Anda untuk melihat tanggapan lengkap*\n\n";
                        break;

                    case 'pesan_biasa':
                    default:
                        // Jawaban berupa teks
                        if ($jawaban) {
                            // Batasi panjang jawaban untuk WhatsApp
                            $jawabanPendek = strlen($jawaban) > 200 ? substr($jawaban, 0, 200) . '...' : $jawaban;
                            $template .= "{$jawabanPendek}\n\n";
                            if (strlen($jawaban) > 200) {
                                $template .= "📧 Tanggapan lengkap dapat dilihat di email.\n\n";
                            }
                        }
                        break;
                }
            } else {
                // Fallback untuk backward compatibility
                if ($jawaban) {
                    if (preg_match('/\.(pdf|doc|docx|jpg|jpeg|png|gif)$/i', $jawaban)) {
                        $template .= "📎 Dokumen tanggapan telah dikirim melalui email.\n\n";
                    } else {
                        $jawabanPendek = strlen($jawaban) > 200 ? substr($jawaban, 0, 200) . '...' : $jawaban;
                        $template .= "{$jawabanPendek}\n\n";
                        if (strlen($jawaban) > 200) {
                            $template .= "📧 Tanggapan lengkap dapat dilihat di email.\n\n";
                        }
                    }
                }
            }

            $template .= "🔒 *Kerahasiaan Terjamin*\n";
            $template .= "Identitas Anda akan dijaga kerahasiaannya sesuai dengan kebijakan WBS.\n\n";
        } else {
            $template .= "❌ *WHISTLE BLOWING SYSTEM DITOLAK*\n\n";
            $template .= "Mohon maaf, laporan Whistle Blowing System Anda *TIDAK DAPAT DIPROSES*.\n\n";
            $template .= "📋 *Detail Laporan:*\n";
            $template .= "• Jenis Laporan: {$jenisLaporan}\n";
            $template .= "• Yang Dilaporkan: {$yangDilaporkan}\n";
            $template .= "• Jabatan: {$jabatan}\n";
            $template .= "• Lokasi Kejadian: {$lokasiKejadian}\n";
            $template .= "• Waktu Kejadian: {$tanggalKejadian}\n";
            $template .= "• Alasan Penolakan: {$alasanPenolakan}\n\n";

            $template .= "🔒 *Kerahasiaan Tetap Terjamin*\n";
            $template .= "Meskipun laporan ditolak, identitas Anda tetap dijaga kerahasiaannya.\n\n";
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

    public function generatePesanReviewPermohonanPerawatan($nama, $status, $unitKerja, $perawatanYangDiusulkan, $keluhanKerusakan, $lokasiPerawatan, $jawaban = null, $alasanPenolakan = null, $strategiPengiriman = null)
    {
        $template = "🏛️ *PPID POLINEMA* 🏛️\n\n";
        $template .= "Halo *{$nama}*,\n\n";

        if ($status === 'Disetujui') {
            $template .= "✅ *PERMOHONAN PERAWATAN DISETUJUI*\n\n";
            $template .= "Permohonan perawatan sarana prasarana Anda telah *SELESAI DIPROSES* dan disetujui.\n\n";
            $template .= "📋 *Detail Permohonan:*\n";
            $template .= "• Unit Kerja: {$unitKerja}\n";
            $template .= "• Perawatan Diusulkan: {$perawatanYangDiusulkan}\n";
            $template .= "• Keluhan: " . \Illuminate\Support\Str::limit($keluhanKerusakan, 100) . "\n";
            $template .= "• Lokasi Perawatan: {$lokasiPerawatan}\n\n";

            // IMPLEMENTASI STRATEGI PENGIRIMAN - SAMA SEPERTI PERMOHONAN INFORMASI
            if ($strategiPengiriman && isset($strategiPengiriman['metode'])) {
                $template .= "📝 *Tanggapan:*\n";

                switch ($strategiPengiriman['metode']) {
                    case 'kirim_file':
                        // File kecil ≤ 9MB - akan dikirim via WhatsApp
                        $template .= "📎 Dokumen tanggapan dikirim bersamaan dengan pesan ini.\n";
                        $template .= "📊 Ukuran file: {$strategiPengiriman['ukuran_mb']} MB\n\n";
                        break;

                    case 'notif_file_besar':
                        // File besar > 9MB - hanya notifikasi
                        $template .= "📎 *Tanggapan yang diberikan berupa media yang ukurannya terlalu besar (melebihi 10 MB) silahkan check email anda untuk melihat tanggapannya*\n";
                        $template .= "📊 Ukuran file: {$strategiPengiriman['ukuran_mb']} MB\n\n";
                        break;

                    case 'file_tidak_ada':
                        // File tidak ditemukan
                        $template .= "⚠️ Dokumen tanggapan tidak dapat dikirim via WhatsApp\n";
                        $template .= "📧 *Silakan cek EMAIL Anda untuk melihat tanggapan lengkap*\n\n";
                        break;

                    case 'pesan_biasa':
                    default:
                        // Jawaban berupa teks
                        if ($jawaban) {
                            // Batasi panjang jawaban untuk WhatsApp
                            $jawabanPendek = strlen($jawaban) > 200 ? substr($jawaban, 0, 200) . '...' : $jawaban;
                            $template .= "{$jawabanPendek}\n\n";
                            if (strlen($jawaban) > 200) {
                                $template .= "📧 Tanggapan lengkap dapat dilihat di email.\n\n";
                            }
                        }
                        break;
                }
            } else {
                // Fallback untuk backward compatibility
                if ($jawaban) {
                    if (preg_match('/\.(pdf|doc|docx|jpg|jpeg|png|gif)$/i', $jawaban)) {
                        $template .= "📎 Dokumen tanggapan telah dikirim melalui email.\n\n";
                    } else {
                        $jawabanPendek = strlen($jawaban) > 200 ? substr($jawaban, 0, 200) . '...' : $jawaban;
                        $template .= "{$jawabanPendek}\n\n";
                        if (strlen($jawaban) > 200) {
                            $template .= "📧 Tanggapan lengkap dapat dilihat di email.\n\n";
                        }
                    }
                }
            }

            $template .= "🔧 *Proses Selanjutnya*\n";
            $template .= "Tim teknis akan segera melakukan koordinasi untuk pelaksanaan perawatan.\n\n";
        } else {
            $template .= "❌ *PERMOHONAN PERAWATAN DITOLAK*\n\n";
            $template .= "Mohon maaf, permohonan perawatan sarana prasarana Anda *TIDAK DAPAT DIPROSES*.\n\n";
            $template .= "📋 *Detail Permohonan:*\n";
            $template .= "• Unit Kerja: {$unitKerja}\n";
            $template .= "• Perawatan Diusulkan: {$perawatanYangDiusulkan}\n";
            $template .= "• Keluhan: " . \Illuminate\Support\Str::limit($keluhanKerusakan, 100) . "\n";
            $template .= "• Lokasi Perawatan: {$lokasiPerawatan}\n";
            $template .= "• Alasan Penolakan: {$alasanPenolakan}\n\n";

            $template .= "💡 *Saran:*\n";
            $template .= "Anda dapat mengajukan permohonan baru dengan melengkapi informasi atau dokumen yang diperlukan.\n\n";
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
}
