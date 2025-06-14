<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerifPernyataanKeberatanMail extends Mailable
{
    use Queueable, SerializesModels;

    public $nama;
    public $status;
    public $reason;
    public $kategori;
    public $statusPemohon;
    public $alasanPengajuanKeberatan;
    public $kasusPosisi;
    public $pesanVerifikasi;

    /**
     * Create a new message instance.
     */
    public function __construct($nama, $status, $kategori, $statusPemohon, $alasanPengajuanKeberatan, $kasusPosisi, $reason = null)
    {
        $this->nama = $nama;
        $this->status = $status;
        $this->reason = $reason;
        $this->kategori = $kategori;
        $this->statusPemohon = $statusPemohon;
        $this->alasanPengajuanKeberatan = $alasanPengajuanKeberatan;
        $this->kasusPosisi = $kasusPosisi;
        
        // Generate pesan berdasarkan status
        $this->pesanVerifikasi = $this->generatePesanVerifikasi();
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $subject = $this->status === 'Disetujui' 
            ? 'Pernyataan Keberatan Anda Disetujui' 
            : 'Pernyataan Keberatan Anda Ditolak';

        return $this->subject($subject)
                    ->view('sisfo::Email.verif-pernyataan-keberatan');
    }

    /**
     * Generate pesan verifikasi berdasarkan status
     */
    private function generatePesanVerifikasi()
    {
        if ($this->status === 'Disetujui') {
            return "Pengajuan keberatan {$this->alasanPengajuanKeberatan} anda disetujui pada tahap verifikasi dan saat ini sedang dilakukan review untuk mempertimbangkan pengajuan.";
        } else {
            return "Mohon maaf pengajuan keberatan {$this->alasanPengajuanKeberatan} anda ditolak karena {$this->reason}.";
        }
    }
}