<?php

namespace Modules\User\App\Http\Controllers;

class LayananInformasiController extends Controller
{
    public function PedomanUmumPengelolaanLayanan()
    {
        $latestUpdateTime = ''; // get updated by atau created by
        $judul = 'Pedoman Umum Pengelolaan Pelayanan';
        $pdfName = 'Pedoman Umum Pengelolaan Pelayanan Informasi.pdf';
        $sharedBy = 'superadmin';
        $pdfFile = 'storage/test-pdfview1.pdf'; // bisa dirubah get by id

        return view('user::layouts.pdf_view', compact(
            'judul',
            'latestUpdateTime',
            'pdfName',
            'sharedBy',
            'pdfFile'
        ));
    }

    public function PedomanKerjaSama()
    {
        $latestUpdateTime = ''; // get updated by atau created by
        $judul = 'Pedoman Layanan Kerja Sama';
        $pdfName = 'Pedoman Layanan Kerja Sama.pdf';
        $sharedBy = 'admin';
        $pdfFile = 'storage/test-pdfview1.pdf'; // bisa dirubah get by id

        return view('user::layouts.pdf_view', compact(
            'judul',
            'latestUpdateTime',
            'pdfName',
            'sharedBy',
            'pdfFile'
        ));
    }

    public function ListSOPLainnya()
    {
        $sopLainnya = [
            [
                'judul' => 'Prosedur Pelayanan Permohonan Informasi',
                'route' => route('sopl-permohonan-informasi'),
            ],
            [
                'judul' => 'Prosedur Pengaduan Masyarakat Melalui PPID',
                'route' => route('sopl-pengaduan-masyarakat'),
            ],
            [
                'judul' => 'Prosedur Penanganan Keberatan',
                'route' => route('sopl-penanganan-keberatan'),
            ],
            [
                'judul' => 'Prosedur Memeriksa Akurasi Informasi',
                'route' => route('sopl-periksa-akurasi'),
            ],
            [
                'judul' => 'Prosedur Uji Konsekuensi Informasi',
                'route' => route('sopl-uji-konsekuensi'),
            ],
            [
                'judul' => 'Prosedur Penyusunan Daftar Informasi',
                'route' => route('sopl-penyusunan-daftar-info'),
            ],
            [
                'judul' => 'Prosedur Fasilitasi Keberatan',
                'route' => route('sopl-fasilitasi-keberatan'),
            ],
            [
                'judul' => 'Prosedur Publikasi Pengumuman',
                'route' => route('sopl-publikasi-pengumuman'),
            ],
            [
                'judul' => 'Prosedur Pengaduan Masyarakat melalui Whistle Blowing System',
                'route' => route('sopl-pengaduan-wbs'),
            ],
        ];

        return view('user::ListSOPInformasiLainnya', compact('sopLainnya'));
    }

    public function ProsedurPermohonanInformasi()
    {
        $latestUpdateTime = ''; // get updated by atau created by
        $judul = 'Prosedur Pelayanan Permohonan Informasi';
        $pdfName = 'Prosedur Pelayanan Permohonan Informasi.pdf';
        $sharedBy = 'admin';
        $pdfFile = 'storage/test-pdfview1.pdf'; // bisa dirubah get by id

        return view('user::layouts.pdf_view', compact(
            'judul',
            'latestUpdateTime',
            'pdfName',
            'sharedBy',
            'pdfFile'
        ));
    }

    public function ProsedurPengaduanMasyarakat()
    {
        $latestUpdateTime = ''; // get updated by atau created by
        $judul = 'Prosedur Pengaduan Masyarakat Melalui PPID';
        $pdfName = 'Prosedur Pengaduan Masyarakat Melalui PPID.pdf';
        $sharedBy = 'admin';
        $pdfFile = 'storage/test-pdfview1.pdf'; // bisa dirubah get by id

        return view('user::layouts.pdf_view', compact(
            'judul',
            'latestUpdateTime',
            'pdfName',
            'sharedBy',
            'pdfFile'
        ));
    }

    public function ProsedurPenangananKeberatan()
    {
        $latestUpdateTime = ''; // get updated by atau created by
        $judul = 'Prosedur Penanganan Keberatan';
        $pdfName = 'Prosedur Penanganan Keberatan.pdf';
        $sharedBy = 'admin';
        $pdfFile = 'storage/test-pdfview1.pdf'; // bisa dirubah get by id

        return view('user::layouts.pdf_view', compact(
            'judul',
            'latestUpdateTime',
            'pdfName',
            'sharedBy',
            'pdfFile'
        ));
    }

    public function ProsedurAkurasiInformasi()
    {
        $latestUpdateTime = ''; // get updated by atau created by
        $judul = 'Prosedur Memeriksa Akurasi Informasi';
        $pdfName = 'Prosedur Memeriksa Akurasi Informasi.pdf';
        $sharedBy = 'admin';
        $pdfFile = 'storage/test-pdfview1.pdf'; // bisa dirubah get by id

        return view('user::layouts.pdf_view', compact(
            'judul',
            'latestUpdateTime',
            'pdfName',
            'sharedBy',
            'pdfFile'
        ));
    }

    public function ProsedurKonsekuensiInformasi()
    {
        $latestUpdateTime = ''; // get updated by atau created by
        $judul = 'Prosedur Uji Konsekuensi Informasi';
        $pdfName = 'Prosedur Uji Konsekuensi Informasi.pdf';
        $sharedBy = 'admin';
        $pdfFile = 'storage/test-pdfview1.pdf'; // bisa dirubah get by id

        return view('user::layouts.pdf_view', compact(
            'judul',
            'latestUpdateTime',
            'pdfName',
            'sharedBy',
            'pdfFile'
        ));
    }

    public function ProsedurPenyusunanDaftarInfo()
    {
        $latestUpdateTime = ''; // get updated by atau created by
        $judul = 'Prosedur Penyusunan Daftar Informasi';
        $pdfName = 'Prosedur Penyusunan Daftar Informasi.pdf';
        $sharedBy = 'admin';
        $pdfFile = 'storage/test-pdfview1.pdf'; // bisa dirubah get by id

        return view('user::layouts.pdf_view', compact(
            'judul',
            'latestUpdateTime',
            'pdfName',
            'sharedBy',
            'pdfFile'
        ));
    }

    public function ProsedurFasilitasiKeberatan()
    {
        $latestUpdateTime = ''; // get updated by atau created by
        $judul = 'Prosedur Fasilitasi Keberatan';
        $pdfName = 'Prosedur Fasilitasi Keberatan.pdf';
        $sharedBy = 'admin';
        $pdfFile = 'storage/test-pdfview1.pdf'; // bisa dirubah get by id

        return view('user::layouts.pdf_view', compact(
            'judul',
            'latestUpdateTime',
            'pdfName',
            'sharedBy',
            'pdfFile'
        ));
    }

    public function ProsedurPublikasiPengumuman()
    {
        $latestUpdateTime = ''; // get updated by atau created by
        $judul = 'Prosedur Publikasi Pengumuman';
        $pdfName = 'Prosedur Publikasi Pengumuman.pdf';
        $sharedBy = 'admin';
        $pdfFile = 'storage/test-pdfview1.pdf'; // bisa dirubah get by id

        return view('user::layouts.pdf_view', compact(
            'judul',
            'latestUpdateTime',
            'pdfName',
            'sharedBy',
            'pdfFile'
        ));
    }

    public function ProsedurPengaduanWBS()
    {
        $latestUpdateTime = ''; // get updated by atau created by
        $judul = 'Prosedur Pengaduan Masyarakat melalui Whistle Blowing System';
        $pdfName = 'Prosedur Pengaduan Masyarakat melalui Whistle Blowing System.pdf';
        $sharedBy = 'admin';
        $pdfFile = 'storage/test-pdfview1.pdf'; // bisa dirubah get by id

        return view('user::layouts.pdf_view', compact(
            'judul',
            'latestUpdateTime',
            'pdfName',
            'sharedBy',
            'pdfFile'
        ));
    }
}
