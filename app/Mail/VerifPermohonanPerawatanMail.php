<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerifPermohonanPerawatanMail extends Mailable
{
    use Queueable, SerializesModels;

    public $nama;
    public $status;
    public $reason;
    public $unitKerja;
    public $perawatanYangDiusulkan;
    public $keluhanKerusakan;
    public $lokasiPerawatan;
    public $pesanVerifikasi;

    /**
     * Create a new message instance.
     */
    public function __construct($nama, $status, $unitKerja, $perawatanYangDiusulkan, $keluhanKerusakan, $lokasiPerawatan, $reason = null)
    {
        $this->nama = $nama;
        $this->status = $status;
        $this->reason = $reason;
        $this->unitKerja = $unitKerja;
        $this->perawatanYangDiusulkan = $perawatanYangDiusulkan;
        $this->keluhanKerusakan = $keluhanKerusakan;
        $this->lokasiPerawatan = $lokasiPerawatan;
        
        // Generate pesan berdasarkan status
        $this->pesanVerifikasi = $this->generatePesanVerifikasi();
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $subject = $this->status === 'Disetujui' 
            ? 'Permohonan Perawatan Sarana Prasarana Anda Disetujui' 
            : 'Permohonan Perawatan Sarana Prasarana Anda Ditolak';

        return $this->subject($subject)
                    ->view('sisfo::Email.verif-permohonan-perawatan');
    }

    /**
     * Generate pesan verifikasi berdasarkan status
     */
    private function generatePesanVerifikasi()
    {
        if ($this->status === 'Disetujui') {
            return "Permohonan perawatan sarana prasarana mengenai {$this->perawatanYangDiusulkan} anda disetujui pada tahap verifikasi dan saat ini sedang dilakukan review untuk mempertimbangkan tindak lanjut permohonan.";
        } else {
            return "Mohon maaf permohonan perawatan sarana prasarana mengenai {$this->perawatanYangDiusulkan} anda ditolak karena {$this->reason}.";
        }
    }
}