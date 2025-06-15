<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerifPengaduanMasyarakatMail extends Mailable
{
    use Queueable, SerializesModels;

    public $nama;
    public $status;
    public $reason;
    public $jenisLaporan;
    public $yangDilaporkan;
    public $lokasiKejadian;
    public $waktuKejadian;
    public $kronologisKejadian;
    public $pesanVerifikasi;

    /**
     * Create a new message instance.
     */
    public function __construct($nama, $status, $jenisLaporan, $yangDilaporkan, $lokasiKejadian, $waktuKejadian, $kronologisKejadian, $reason = null)
    {
        $this->nama = $nama;
        $this->status = $status;
        $this->reason = $reason;
        $this->jenisLaporan = $jenisLaporan;
        $this->yangDilaporkan = $yangDilaporkan;
        $this->lokasiKejadian = $lokasiKejadian;
        $this->waktuKejadian = $waktuKejadian;
        $this->kronologisKejadian = $kronologisKejadian;
        
        // Generate pesan berdasarkan status
        $this->pesanVerifikasi = $this->generatePesanVerifikasi();
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $subject = $this->status === 'Disetujui' 
            ? 'Pengaduan Masyarakat Anda Disetujui' 
            : 'Pengaduan Masyarakat Anda Ditolak';

        return $this->subject($subject)
                    ->view('sisfo::Email.verif-pengaduan-masyarakat');
    }

    /**
     * Generate pesan verifikasi berdasarkan status
     */
    private function generatePesanVerifikasi()
    {
        if ($this->status === 'Disetujui') {
            return "Pengaduan masyarakat mengenai {$this->jenisLaporan} anda disetujui pada tahap verifikasi dan saat ini sedang dilakukan review untuk mempertimbangkan tindak lanjut pengaduan.";
        } else {
            return "Mohon maaf pengaduan masyarakat mengenai {$this->jenisLaporan} anda ditolak karena {$this->reason}.";
        }
    }
}