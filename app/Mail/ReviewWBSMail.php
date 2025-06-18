<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ReviewWBSMail extends Mailable
{
    use Queueable, SerializesModels;

    public $nama;
    public $status;
    public $reason;
    public $jenisLaporan;
    public $yangDilaporkan;
    public $jabatan;
    public $lokasiKejadian;
    public $waktuKejadian;
    public $kronologisKejadian;
    public $jawaban;
    public $pesanReview;

    /**
     * Create a new message instance.
     */
    public function __construct($nama, $status, $jenisLaporan, $yangDilaporkan, $jabatan, $lokasiKejadian, $waktuKejadian, $kronologisKejadian, $jawaban = null, $reason = null)
    {
        $this->nama = $nama;
        $this->status = $status;
        $this->reason = $reason;
        $this->jenisLaporan = $jenisLaporan;
        $this->yangDilaporkan = $yangDilaporkan;
        $this->jabatan = $jabatan;
        $this->lokasiKejadian = $lokasiKejadian;
        $this->waktuKejadian = $waktuKejadian;
        $this->kronologisKejadian = $kronologisKejadian;
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
            ? 'Hasil Review Whistle Blowing System Anda - Disetujui' 
            : 'Hasil Review Whistle Blowing System Anda - Ditolak';

        $email = $this->subject($subject)
                    ->view('sisfo::Email.review-wbs');

        // Tambahkan lampiran jika ada file jawaban (untuk status disetujui)
        if ($this->status === 'Disetujui' && $this->jawaban && $this->isFilePath($this->jawaban)) {
            try {
                $filePath = storage_path('app/public/' . $this->jawaban);
                if (file_exists($filePath)) {
                    $email->attach($filePath, [
                        'as' => 'Dokumen_Tanggapan_WBS_' . date('Y-m-d') . '.pdf',
                        'mime' => 'application/pdf',
                    ]);
                }
            } catch (\Exception $e) {
                // Log error tapi tetap kirim email tanpa attachment
                Log::error("Gagal melampirkan file tanggapan WBS: " . $e->getMessage());
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
            return "Laporan Whistle Blowing System Anda mengenai {$this->jenisLaporan} telah selesai diproses dan disetujui. Tanggapan dari laporan Anda dapat dilihat di bawah.";
        } else {
            return "Mohon maaf, laporan Whistle Blowing System Anda mengenai {$this->jenisLaporan} tidak dapat diproses karena {$this->reason}.";
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