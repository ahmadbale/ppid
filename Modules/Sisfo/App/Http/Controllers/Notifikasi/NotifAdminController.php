<?php

namespace Modules\Sisfo\App\Http\Controllers\Notifikasi;

use Modules\Sisfo\App\Http\Controllers\TraitsController;
use Modules\Sisfo\App\Models\Log\NotifMasukModel;
use Illuminate\Routing\Controller;

class NotifAdminController extends Controller
{
    use TraitsController;

    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Notifikasi',
            'list' => ['Home', 'Notifikasi']
        ];

        $page = (object) [
            'title' => 'Notifikasi Pengajuan Permohonan dan Pertanyaan'
        ];

        $activeMenu = 'notifikasi';

        return view('sisfo::Notifikasi/NotifAdmin.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu
        ]);
    }

    public function notifikasiPermohonanInformasi()
    {
        $notifikasi = NotifMasukModel::with('t_permohonan_informasi')
            ->where('notif_masuk_kategori', 'E-Form Permohonan Informasi')
            ->where('isDeleted', 0)
            ->get();

        $breadcrumb = (object) [
            'title' => 'Notifikasi',
            'list' => ['Home', 'Notifikasi']
        ];

        $page = (object) [
            'title' => 'Notifikasi Pengajuan Permohonan Informasi'
        ];

        $activeMenu = 'notifikasi';

        return view('sisfo::Notifikasi/NotifAdmin.notifPI', [
            'notifikasi' => $notifikasi,
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu
        ]);
    }

    public function notifikasiPernyataanKeberatan()
    {
        $notifikasi = NotifMasukModel::with('t_pernyataan_keberatan')
            ->where('notif_masuk_kategori', 'E-Form Pernyataan Keberatan')
            ->where('isDeleted', 0)
            ->get();

        $breadcrumb = (object) [
            'title' => 'Notifikasi',
            'list' => ['Home', 'Notifikasi']
        ];

        $page = (object) [
            'title' => 'Notifikasi Pengajuan Pernyataan Keberatan'
        ];

        $activeMenu = 'notifikasi';

        return view('sisfo::Notifikasi/NotifAdmin.notifPK', [
            'notifikasi' => $notifikasi,
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu
        ]);
    }

    public function notifikasiPengaduanMasyarakat()
    {
        $notifikasi = NotifMasukModel::with('t_pengaduan_masyarakat')
            ->where('notif_masuk_kategori', 'E-Form Pengaduan Masyarakat')
            ->where('isDeleted', 0)
            ->get();

        $breadcrumb = (object) [
            'title' => 'Notifikasi',
            'list' => ['Home', 'Notifikasi']
        ];

        $page = (object) [
            'title' => 'Notifikasi Pengajuan Pengaduan Masyarakat'
        ];

        $activeMenu = 'notifikasi';

        return view('sisfo::Notifikasi/NotifAdmin.notifPM', [
            'notifikasi' => $notifikasi,
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu
        ]);
    }

    public function notifikasiWBS()
    {
        $notifikasi = NotifMasukModel::with('t_wbs')
            ->where('notif_masuk_kategori', 'E-Form Whistle Blowing System')
            ->where('isDeleted', 0)
            ->get();

        $breadcrumb = (object) [
            'title' => 'Notifikasi',
            'list' => ['Home', 'Notifikasi']
        ];

        $page = (object) [
            'title' => 'Notifikasi Pengajuan Whistle Blowing System'
        ];

        $activeMenu = 'notifikasi';

        return view('sisfo::Notifikasi/NotifAdmin.notifWBS', [
            'notifikasi' => $notifikasi,
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu
        ]);
    }

    public function notifikasiPermohonanPerawatan()
    {
        $notifikasi = NotifMasukModel::with('t_permohonan_perawatan')
            ->where('notif_masuk_kategori', 'E-Form Permohonan Perawatan Sarana Prasarana')
            ->where('isDeleted', 0)
            ->get();

        $breadcrumb = (object) [
            'title' => 'Notifikasi',
            'list' => ['Home', 'Notifikasi']
        ];

        $page = (object) [
            'title' => 'Notifikasi Pengajuan Permohonan Perawatan Sarana Prasarana'
        ];

        $activeMenu = 'notifikasi';

        return view('sisfo::Notifikasi/NotifAdmin.notifPP', [
            'notifikasi' => $notifikasi,
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu
        ]);
    }

    public function tandaiDibaca($id)
    {
        $result = NotifMasukModel::tandaiDibaca($id);
        return response()->json($result);
    }

    public function hapusNotifikasi($id)
    {
        $result = NotifMasukModel::hapusNotifikasi($id);
        return response()->json($result);
    }

    public function tandaiSemuaDibaca()
    {
        $result = NotifMasukModel::tandaiSemuaDibaca();
        return response()->json($result);
    }

    public function hapusSemuaDibaca()
    {
        $result = NotifMasukModel::hapusSemuaDibaca();
        return response()->json($result);
    }
}
