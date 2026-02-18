<?php

namespace Modules\Sisfo\App\Http\Controllers\Notifikasi;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Sisfo\App\Http\Controllers\TraitsController;
use Modules\Sisfo\App\Models\Log\NotifVerifModel;

class NotifVerifikasiController extends Controller
{
    use TraitsController;

    public function index(Request $request)
    {
        $breadcrumb = (object) [
            'title' => 'Notifikasi Verifikasi',
            'list' => ['Home', 'Notifikasi Verifikasi']
        ];

        $page = (object) [
            'title' => 'Daftar Notifikasi Verifikasi'
        ];

        $activeMenu = 'notifikasi-verifikasi';

        return view('sisfo::Notifikasi.NotifVerif.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu
        ]);
    }

    public function getData(Request $request)
    {
        $kategori = $request->input('kategori');
        
        $kategoriMap = [
            'permohonan-informasi' => 'E-Form Permohonan Informasi',
            'pernyataan-keberatan' => 'E-Form Pernyataan Keberatan',
            'pengaduan-masyarakat' => 'E-Form Pengaduan Masyarakat',
            'whistle-blowing-system' => 'E-Form Whistle Blowing System',
            'permohonan-perawatan' => 'E-Form Permohonan Perawatan Sarana Prasarana',
        ];

        $kategoriNama = $kategoriMap[$kategori] ?? null;

        if (!$kategoriNama) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kategori tidak valid'
            ], 400);
        }

        $notifikasi = NotifVerifModel::with([
            't_permohonan_informasi',
            't_pernyataan_keberatan',
            't_pengaduan_masyarakat',
            't_wbs',
            't_permohonan_perawatan'
        ])
        ->where('notif_verif_kategori', $kategoriNama)
        ->where('isDeleted', 0)
        ->orderBy('created_at', 'desc')
        ->get();

        return response()->json([
            'status' => 'success',
            'data' => $notifikasi
        ]);
    }

    public function detailData($kategori)
    {
        $breadcrumb = (object) [
            'title' => 'Detail Notifikasi Verifikasi',
            'list' => ['Home', 'Notifikasi Verifikasi', 'Detail']
        ];

        $kategoriMap = [
            1 => 'E-Form Permohonan Informasi',
            2 => 'E-Form Pernyataan Keberatan',
            3 => 'E-Form Pengaduan Masyarakat',
            4 => 'E-Form Whistle Blowing System',
            5 => 'E-Form Permohonan Perawatan Sarana Prasarana',
        ];

        $kategoriId = (int)$kategori;
        $kategoriNama = $kategoriMap[$kategoriId] ?? null;

        $page = (object) [
            'title' => 'Notifikasi ' . $kategoriNama
        ];

        $activeMenu = 'notifikasi-verifikasi';

        $notifikasi = NotifVerifModel::with([
            't_permohonan_informasi',
            't_pernyataan_keberatan',
            't_pengaduan_masyarakat',
            't_wbs',
            't_permohonan_perawatan'
        ])
        ->where('notif_verif_kategori', $kategoriNama)
        ->where('isDeleted', 0)
        ->orderBy('created_at', 'desc')
        ->get();

        return view('sisfo::Notifikasi.NotifVerif.detail', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu,
            'notifikasi' => $notifikasi,
            'kategori' => $kategoriId
        ]);
    }

    public function updateData(Request $request, $id)
    {
        try {
            $action = $request->input('action');
            
            if ($action === 'tandai-dibaca') {
                NotifVerifModel::tandaiDibaca($id);
                return response()->json(['status' => 'success', 'message' => 'Notifikasi berhasil ditandai sebagai dibaca']);
            } elseif ($action === 'tandai-semua-dibaca') {
                $kategori = $request->input('kategori');
                NotifVerifModel::tandaiSemuaDibacaByKategori($kategori);
                return response()->json(['status' => 'success', 'message' => 'Semua notifikasi berhasil ditandai sebagai dibaca']);
            }

            return response()->json(['status' => 'error', 'message' => 'Action tidak valid'], 400);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function deleteData(Request $request, $id)
    {
        try {
            $action = $request->input('action');
            
            if ($action === 'hapus-single') {
                NotifVerifModel::hapusNotifikasi($id);
                return response()->json(['status' => 'success', 'message' => 'Notifikasi berhasil dihapus']);
            } elseif ($action === 'hapus-semua-dibaca') {
                $kategori = $request->input('kategori');
                NotifVerifModel::hapusSemuaDibacaByKategori($kategori);
                return response()->json(['status' => 'success', 'message' => 'Semua notifikasi yang sudah dibaca berhasil dihapus']);
            }

            return response()->json(['status' => 'error', 'message' => 'Action tidak valid'], 400);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function getBadgeCount()
    {
        return NotifVerifModel::whereNull('notif_verif_dibaca_tgl')->where('isDeleted', 0)->count();
    }
}
