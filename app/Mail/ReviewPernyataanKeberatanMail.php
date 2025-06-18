<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ReviewPernyataanKeberatanMail extends Mailable
{
    use Queueable, SerializesModels;

    public $nama;
    public $status;
    public $reason;
    public $kategori;
    public $alasanPengajuanKeberatan;
    public $kasusPosisi;
    public $jawaban;
    public $pesanReview;

    /**
     * Create a new message instance.
     */
    public function __construct($nama, $status, $kategori, $alasanPengajuanKeberatan, $kasusPosisi, $jawaban = null, $reason = null)
    {
        $this->nama = $nama;
        $this->status = $status;
        $this->reason = $reason;
        $this->kategori = $kategori;
        $this->alasanPengajuanKeberatan = $alasanPengajuanKeberatan;
        $this->kasusPosisi = $kasusPosisi;
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
            ? 'Hasil Review Pernyataan Keberatan Anda - Disetujui' 
            : 'Hasil Review Pernyataan Keberatan Anda - Ditolak';

        $email = $this->subject($subject)
                    ->view('sisfo::Email.review-pernyataan-keberatan');

        // Tambahkan lampiran jika ada file jawaban (untuk status disetujui)
        if ($this->status === 'Disetujui' && $this->jawaban && $this->isFilePath($this->jawaban)) {
            try {
                $filePath = storage_path('app/public/' . $this->jawaban);
                if (file_exists($filePath)) {
                    $email->attach($filePath, [
                        'as' => 'Dokumen_Jawaban_Keberatan_' . date('Y-m-d') . '.pdf',
                        'mime' => 'application/pdf',
                    ]);
                }
            } catch (\Exception $e) {
                // Log error tapi tetap kirim email tanpa attachment
                Log::error("Gagal melampirkan file jawaban keberatan: " . $e->getMessage());
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
            return "Pernyataan keberatan Anda mengenai {$this->alasanPengajuanKeberatan} telah selesai diproses dan disetujui. Jawaban dari pernyataan keberatan Anda dapat dilihat di bawah.";
        } else {
            return "Mohon maaf, pernyataan keberatan Anda mengenai {$this->alasanPengajuanKeberatan} tidak dapat diproses karena {$this->reason}.";
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