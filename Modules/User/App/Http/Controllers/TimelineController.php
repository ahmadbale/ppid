<?php

namespace Modules\User\App\Http\Controllers;

use Illuminate\Http\Request;

class TimelineController extends Controller
{
    public function permohonan_informasi()
    {
        $title = "Permohonan Informasi Publik";
        $description = "E-Form ini digunakan untuk mengajukan permohonan akses informasi publik di Politeknik Negeri Malang<br>sesuai dengan ketentuan yang berlaku.";
        $titlemekanisme = "Mekanisme Permohonan Informasi Publik";
        $steps = [
            ["number" => "1", "text" => "Ini permohonan informasi Pemohon mengajukan keberatan melalui formulir yang tersedia", "position" => "right"],
            ["number" => "2", "text" => "Petugas PPID Seksi Aduan Masyarakat menerima dan mencatat permohonan keberatan", "position" => "left"],
            ["number" => "3", "text" => "Seksi Aduan Masyarakat meneruskan permohonan keberatan kepada PPID Pusat untuk ditindaklanjuti", "position" => "right"],
            ["number" => "4", "text" => "PPID Pusat menyampaikan permohonan keberatan kepada atasan", "position" => "left"],
            ["number" => "5", "text" => "Atasan PPID pusat menentukan untuk menggugurkan atau menyetujui keputusan PPID", "position" => "right"],
            ["number" => "6", "text" => "Keputusan akhir mengenai keberatan disampaikan secara tertulis kepada pemohon", "position" => "left"],
        ];

        return view('user::timeline_informasi', compact('steps', 'title', 'description', 'titlemekanisme'));
    }

    public function pernyataan_keberatan()
    {
        $title = "Pernyataan Keberatan";
        $description = "E-Form Pengajuan Keberatan atas Permohonan Informasi di Lingkungan Politeknik Negeri Malang<br>Pengajuan Keberatan Dapat Dilakukan oleh Diri Sendiri atau Atas Permohonan Orang Lain.";
        $titlemekanisme = "Mekanisme Pengajuan Keberatan Informasi";
        $steps = [
            ["number" => "1", "text" => "Pemohon mengajukan keberatan melalui formulir yang tersedia", "position" => "right"],
            ["number" => "2", "text" => "Petugas PPID Seksi Aduan Masyarakat menerima dan mencatat permohonan keberatan", "position" => "left"],
            ["number" => "3", "text" => "Seksi Aduan Masyarakat meneruskan permohonan keberatan kepada PPID Pusat untuk ditindaklanjuti", "position" => "right"],
            ["number" => "4", "text" => "PPID Pusat menyampaikan permohonan keberatan kepada atasan", "position" => "left"],
            ["number" => "5", "text" => "Atasan PPID pusat menentukan untuk menggugurkan atau menyetujui keputusan PPID", "position" => "right"],
            ["number" => "6", "text" => "Keputusan akhir mengenai keberatan disampaikan secara tertulis kepada pemohon", "position" => "left"],
        ];

        
        return view('user::timeline_keberatan', compact('steps', 'title', 'description',  'titlemekanisme'));

    }

    public function wbs()
    {
        $title = "Pelaporan Whistle Blowing System";
        $description = "E-Form ini digunakan untuk melaporkan dugaan pelanggaran kode etik, penyalahgunaan wewenang,<br>korupsi, atau tindakan tidak etis lainnya
        yang dilakukan oleh pegawai di lingkungan Politeknik Negeri Malang.";
        $titlemekanisme = "Mekanisme Pelaporan Whistle Blowing System";
        $steps = [
            ["number" => "1", "text" => "Ini punya wbs Pemohon mengajukan keberatan melalui formulir yang tersedia", "position" => "right"],
            ["number" => "2", "text" => "Petugas PPID Seksi Aduan Masyarakat menerima dan mencatat permohonan keberatan", "position" => "left"],
            ["number" => "3", "text" => "Seksi Aduan Masyarakat meneruskan permohonan keberatan kepada PPID Pusat untuk ditindaklanjuti", "position" => "right"],
            ["number" => "4", "text" => "PPID Pusat menyampaikan permohonan keberatan kepada atasan", "position" => "left"],
            ["number" => "5", "text" => "Atasan PPID pusat menentukan untuk menggugurkan atau menyetujui keputusan PPID", "position" => "right"],
            ["number" => "6", "text" => "Keputusan akhir mengenai keberatan disampaikan secara tertulis kepada pemohon", "position" => "left"],
        ];

        return view('user::timeline_wbs', compact('steps', 'title', 'description',  'titlemekanisme'));
    }

    public function pengaduan_masyarakat()
    {
        $title = "Pengaduan Masyarakat";
        $description = "E-Form ini digunakan untuk menyampaikan keluhan, aspirasi, atau laporan terkait pelayanan publik di<br>Politeknik Negeri Malang, yang dapat mencakup ketidaksesuaian layanan, pungutan liar, atau lainnya<br>yang berdampak pada masyarakat.";
        $titlemekanisme = "Mekanisme Pengaduan Masyarakat";
        $steps = [
            ["number" => "1", "text" => "Pengaduan masyarakat Pemohon mengajukan keberatan melalui formulir yang tersedia", "position" => "right"],
            ["number" => "2", "text" => "Petugas PPID Seksi Aduan Masyarakat menerima dan mencatat permohonan keberatan", "position" => "left"],
            ["number" => "3", "text" => "Seksi Aduan Masyarakat meneruskan permohonan keberatan kepada PPID Pusat untuk ditindaklanjuti", "position" => "right"],
            ["number" => "4", "text" => "PPID Pusat menyampaikan permohonan keberatan kepada atasan", "position" => "left"],
            ["number" => "5", "text" => "Atasan PPID pusat menentukan untuk menggugurkan atau menyetujui keputusan PPID", "position" => "right"],
            ["number" => "6", "text" => "Keputusan akhir mengenai keberatan disampaikan secara tertulis kepada pemohon", "position" => "left"],
        ];

        return view('user::timeline_pengaduan-masyarakat', compact('steps', 'title', 'description', 'titlemekanisme'));
    }
}
