<?php

namespace Modules\Sisfo\App\Http\Controllers\SistemInformasi\DaftarPengajuan\VerifPengajuan;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Sisfo\App\Http\Controllers\TraitsController;
use Modules\Sisfo\App\Models\SistemInformasi\EForm\PermohonanInformasiModel;
use Modules\Sisfo\App\Models\Website\WebMenuModel;

class VerifPengajuanController extends Controller
{
    use TraitsController;

    public $breadcrumb = 'Daftar Verifikasi Pengajuan';
    public $pagename = 'Verifikasi Pengajuan';
    public $daftarPengajuanUrl;

    public function __construct()
    {
        $this->daftarPengajuanUrl = WebMenuModel::getDynamicMenuUrl('daftar-verifikasi-pengajuan');
    }

    public function index()
    {
        $breadcrumb = (object) [
            'title' => $this->pagename,
            'list' => ['Home', $this->pagename]
        ];

        $page = (object) [
            'title' => $this->pagename
        ];

        // Ambil jumlah verifikasi dari model
        $jumlahVerifikasi = PermohonanInformasiModel::hitungJumlahVerifikasi();

        return view('sisfo::SistemInformasi.DaftarPengajuan.VerifPengajuan.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'jumlahDaftarVerifPermohonanInformasi' => $jumlahVerifikasi['permohonanInformasi'],
            'jumlahDaftarVerifPernyataanKeberatan' => $jumlahVerifikasi['pernyataanKeberatan'],
            'jumlahDaftarVerifPengaduanMasyarakat' => $jumlahVerifikasi['pengaduanMasyarakat'],
            'jumlahDaftarVerifWBS' => $jumlahVerifikasi['wbs'],
            'jumlahDaftarVerifPermohonanPerawatan' => $jumlahVerifikasi['permohonanPerawatan'],
            'daftarPengajuanUrl' => $this->daftarPengajuanUrl
        ]);
    }
}
