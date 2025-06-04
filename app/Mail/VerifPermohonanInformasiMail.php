<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerifPermohonanInformasiMail extends Mailable
{
    use Queueable, SerializesModels;

    public $nama;
    public $status;
    public $reason;
    public $kategori;
    public $statusPemohon;
    public $informasiYangDibutuhkan;
    public $pesanVerifikasi;

    /**
     * Create a new message instance.
     */
    public function __construct($nama, $status, $kategori, $statusPemohon, $informasiYangDibutuhkan, $reason = null)
    {
        $this->nama = $nama;
        $this->status = $status;
        $this->reason = $reason;
        $this->kategori = $kategori;
        $this->statusPemohon = $statusPemohon;
        $this->informasiYangDibutuhkan = $informasiYangDibutuhkan;
        
        // Generate pesan berdasarkan status
        $this->pesanVerifikasi = $this->generatePesanVerifikasi();
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $subject = $this->status === 'Disetujui' 
            ? 'Permohonan Informasi Anda Disetujui' 
            : 'Permohonan Informasi Anda Ditolak';

        return $this->subject($subject)
                    ->view('sisfo::Email.verif-pengajuan');
    }

    /**
     * Generate pesan verifikasi berdasarkan status
     */
    private function generatePesanVerifikasi()
    {
        if ($this->status === 'Disetujui') {
            return "Pengajuan {$this->informasiYangDibutuhkan} anda disetujui pada tahap verifikasi dan saat ini sedang dilakukan review untuk mempertimbangkan pengajuan.";
        } else {
            return "Mohon maaf pengajuan {$this->informasiYangDibutuhkan} anda ditolak karena {$this->reason}.";
        }
    }
}