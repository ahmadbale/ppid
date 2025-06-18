<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ReviewPermohonanPerawatanMail extends Mailable
{
    use Queueable, SerializesModels;

    public $nama;
    public $status;
    public $reason;
    public $unitKerja;
    public $perawatanYangDiusulkan;
    public $keluhanKerusakan;
    public $lokasiPerawatan;
    public $jawaban;
    public $pesanReview;

    /**
     * Create a new message instance.
     */
    public function __construct($nama, $status, $unitKerja, $perawatanYangDiusulkan, $keluhanKerusakan, $lokasiPerawatan, $jawaban = null, $reason = null)
    {
        $this->nama = $nama;
        $this->status = $status;
        $this->reason = $reason;
        $this->unitKerja = $unitKerja;
        $this->perawatanYangDiusulkan = $perawatanYangDiusulkan;
        $this->keluhanKerusakan = $keluhanKerusakan;
        $this->lokasiPerawatan = $lokasiPerawatan;
        $this->jawaban = $jawaban;
        
        // Generate pesan berdasarkan status
        $this->pesanReview = $this->generatePesanReview();
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $subject = $this->status === 'Disetujui' 
            ? 'Hasil Review Permohonan Perawatan Anda - Disetujui' 
            : 'Hasil Review Permohonan Perawatan Anda - Ditolak';

        $email = $this->subject($subject)
                    ->view('sisfo::Email.review-permohonan-perawatan');

        // Tambahkan lampiran jika ada file jawaban (untuk status disetujui)
        if ($this->status === 'Disetujui' && $this->jawaban && $this->isFilePath($this->jawaban)) {
            try {
                $filePath = storage_path('app/public/' . $this->jawaban);
                if (file_exists($filePath)) {
                    $email->attach($filePath, [
                        'as' => 'Dokumen_Tanggapan_Perawatan_' . date('Y-m-d') . '.pdf',
                        'mime' => 'application/pdf',
                    ]);
                }
            } catch (\Exception $e) {
                // Log error tapi tetap kirim email tanpa attachment
                Log::error("Gagal melampirkan file tanggapan permohonan perawatan: " . $e->getMessage());
            }
        }

        return $email;
    }

    /**
     * Generate pesan review berdasarkan status
     */
    private function generatePesanReview()
    {
        if ($this->status === 'Disetujui') {
            return "Permohonan perawatan sarana prasarana Anda untuk {$this->perawatanYangDiusulkan} telah selesai diproses dan disetujui. Tanggapan dari permohonan Anda dapat dilihat di bawah.";
        } else {
            return "Mohon maaf, permohonan perawatan sarana prasarana Anda untuk {$this->perawatanYangDiusulkan} tidak dapat diproses karena {$this->reason}.";
        }
    }

    /**
     * Check if jawaban is a file path
     */
    private function isFilePath($jawaban)
    {
        // Cek apakah jawaban berupa path file (mengandung ekstensi file)
        return preg_match('/\.(pdf|doc|docx|jpg|jpeg|png|gif)$/i', $jawaban);
    }
}