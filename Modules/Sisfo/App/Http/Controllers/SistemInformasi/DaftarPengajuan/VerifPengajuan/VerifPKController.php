<?php

namespace Modules\Sisfo\App\Http\Controllers\SistemInformasi\DaftarPengajuan\VerifPengajuan;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Sisfo\App\Http\Controllers\TraitsController;
use Modules\Sisfo\App\Models\SistemInformasi\EForm\PermohonanInformasiModel;
use Modules\Sisfo\App\Models\Website\WebMenuModel;

class VerifPKController extends Controller
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

    }

    public function daftarVerifPermohonanInformasi()
    {

    }

    public function getApproveModal($id)
    {
 
    }

    public function getDeclineModal($id)
    {

    }

    public function setujuiPermohonan($id)
    {

    }

    public function tolakPermohonan(Request $request, $id)
    {
 
    }

    public function tandaiDibaca($id)
    {

    }

    public function hapusPermohonan($id)
    {

    }
}
