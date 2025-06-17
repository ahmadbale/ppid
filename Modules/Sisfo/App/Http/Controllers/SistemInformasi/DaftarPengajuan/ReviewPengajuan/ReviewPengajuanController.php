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
    public $daftarReviewPengajuanUrl;

    public function __construct()
    {
        $this->daftarReviewPengajuanUrl = WebMenuModel::getDynamicMenuUrl('daftar-review-pengajuan');
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

        // Ambil jumlah review dari masing-masing model
        $jumlahReview = [
            'permohonanInformasi' => PermohonanInformasiModel::hitungJumlahReview(),
            'pernyataanKeberatan' => 0, // Akan diimplementasikan nanti
            'pengaduanMasyarakat' => 0, // Akan diimplementasikan nanti
            'wbs' => 0, // Akan diimplementasikan nanti
            'permohonanPerawatan' => 0 // Akan diimplementasikan nanti
        ];

        return view('sisfo::SistemInformasi.DaftarPengajuan.ReviewPengajuan.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'jumlahDaftarReviewPermohonanInformasi' => $jumlahReview['permohonanInformasi'],
            'jumlahDaftarReviewPernyataanKeberatan' => $jumlahReview['pernyataanKeberatan'],
            'jumlahDaftarReviewPengaduanMasyarakat' => $jumlahReview['pengaduanMasyarakat'],
            'jumlahDaftarReviewWBS' => $jumlahReview['wbs'],
            'jumlahDaftarReviewPermohonanPerawatan' => $jumlahReview['permohonanPerawatan'],
            'daftarReviewPengajuanUrl' => $this->daftarReviewPengajuanUrl
        ]);
    }
}