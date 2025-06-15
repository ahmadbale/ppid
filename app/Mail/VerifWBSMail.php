<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerifWBSMail extends Mailable
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
    public $pesanVerifikasi;

    /**
     * Create a new message instance.
     */
    public function __construct($nama, $status, $jenisLaporan, $yangDilaporkan, $jabatan, $lokasiKejadian, $waktuKejadian, $kronologisKejadian, $reason = null)
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
        
        // Generate pesan berdasarkan status
        $this->pesanVerifikasi = $this->generatePesanVerifikasi();
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $subject = $this->status === 'Disetujui' 
            ? 'Whistle Blowing System Anda Disetujui' 
            : 'Whistle Blowing System Anda Ditolak';

        return $this->subject($subject)
                    ->view('sisfo::Email.verif-wbs');
    }

    /**
     * Generate pesan verifikasi berdasarkan status
     */
    private function generatePesanVerifikasi()
    {
        if ($this->status === 'Disetujui') {
            return "Laporan Whistle Blowing System mengenai {$this->jenisLaporan} anda disetujui pada tahap verifikasi dan saat ini sedang dilakukan review untuk mempertimbangkan tindak lanjut laporan.";
        } else {
            return "Mohon maaf laporan Whistle Blowing System mengenai {$this->jenisLaporan} anda ditolak karena {$this->reason}.";
        }
    }
}