<?php

namespace Modules\Sisfo\App\Http\Controllers\SistemInformasi\DaftarPengajuan\ReviewPengajuan;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Sisfo\App\Http\Controllers\TraitsController;
use Modules\Sisfo\App\Models\SistemInformasi\EForm\PengaduanMasyarakatModel;
use Modules\Sisfo\App\Models\SistemInformasi\EForm\PermohonanInformasiModel;
use Modules\Sisfo\App\Models\SistemInformasi\EForm\PermohonanPerawatanModel;
use Modules\Sisfo\App\Models\SistemInformasi\EForm\PernyataanKeberatanModel;
use Modules\Sisfo\App\Models\SistemInformasi\EForm\WBSModel;
use Modules\Sisfo\App\Models\Website\WebMenuModel;

class ReviewPengajuanController extends Controller
{
    use TraitsController;

    public $breadcrumb = 'Daftar Review Pengajuan';
    public $pagename = 'Review Pengajuan';
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

        // Ambil jumlah verifikasi dari masing-masing model
        $jumlahVerifikasi = [
            'permohonanInformasi' => PermohonanInformasiModel::hitungJumlahVerifikasi(),
            'pernyataanKeberatan' => PernyataanKeberatanModel::hitungJumlahVerifikasi(),
            'pengaduanMasyarakat' => PengaduanMasyarakatModel::hitungJumlahVerifikasi(),
            'wbs' => WBSModel::hitungJumlahVerifikasi(),
            'permohonanPerawatan' => PermohonanPerawatanModel::hitungJumlahVerifikasi()
        ];

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
