<?php

namespace Modules\Sisfo\App\Http\Controllers\Notifikasi;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Sisfo\App\Http\Controllers\TraitsController;
use Modules\Sisfo\App\Models\Log\NotifMasukModel;

class NotifMasukController extends Controller
{
    use TraitsController;

    // 1. ✅ LIST DATA (default GET /)
    public function index(Request $request)
    {
        $breadcrumb = (object) [
            'title' => 'Notifikasi Pengajuan Masuk',
            'list' => ['Home', 'Notifikasi']
        ];

        $page = (object) [
            'title' => 'Notifikasi Pengajuan Permohonan dan Pertanyaan'
        ];

        $activeMenu = 'notifikasi-masuk';

        return view('sisfo::Notifikasi.NotifMasuk.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu
        ]);
    }

    // 2. ✅ GET DATA (AJAX untuk datatable) - Digunakan untuk ambil data notifikasi by kategori
    public function getData(Request $request)
    {
        $kategori = $request->input('kategori');
        
        // Mapping kategori ID ke nama kategori di database
        $kategoriMap = [
            1 => 'E-Form Permohonan Informasi',
            2 => 'E-Form Pernyataan Keberatan',
            3 => 'E-Form Pengaduan Masyarakat',
            4 => 'E-Form Whistle Blowing System',
            5 => 'E-Form Permohonan Perawatan Sarana Prasarana',
        ];

        $kategoriNama = $kategoriMap[$kategori] ?? null;

        if (!$kategoriNama) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori tidak valid'
            ], 400);
        }

        // Query data notifikasi berdasarkan kategori
        $notifikasi = NotifMasukModel::with([
            't_permohonan_informasi',
            't_pernyataan_keberatan',
            't_pengaduan_masyarakat',
            't_wbs',
            't_permohonan_perawatan'
        ])
        ->where('notif_masuk_kategori', $kategoriNama)
        ->where('isDeleted', 0)
        ->orderBy('created_at', 'desc')
        ->get();

        return response()->json([
            'success' => true,
            'data' => $notifikasi,
            'kategori' => $kategori,
            'kategoriNama' => $kategoriNama
        ]);
    }

    // 3. ✅ SHOW DETAIL - Detail notifikasi by kategori
    public function detailData($id)
    {
        // $id adalah kategori ID (1, 2, 3, 4, 5)
        $kategoriMap = [
            1 => 'E-Form Permohonan Informasi',
            2 => 'E-Form Pernyataan Keberatan',
            3 => 'E-Form Pengaduan Masyarakat',
            4 => 'E-Form Whistle Blowing System',
            5 => 'E-Form Permohonan Perawatan Sarana Prasarana',
        ];

        $kategori = (int)$id;
        $kategoriNama = $kategoriMap[$kategori] ?? null;

        if (!$kategoriNama) {
            abort(404, 'Kategori notifikasi tidak ditemukan');
        }

        $notifikasi = NotifMasukModel::with([
            't_permohonan_informasi',
            't_pernyataan_keberatan',
            't_pengaduan_masyarakat',
            't_wbs',
            't_permohonan_perawatan'
        ])
        ->where('notif_masuk_kategori', $kategoriNama)
        ->where('isDeleted', 0)
        ->orderBy('created_at', 'desc')
        ->get();

        $breadcrumb = (object) [
            'title' => 'Notifikasi ' . $kategoriNama,
            'list' => ['Home', 'Notifikasi', $kategoriNama]
        ];

        $page = (object) [
            'title' => 'Notifikasi Pengajuan ' . str_replace('E-Form ', '', $kategoriNama)
        ];

        $activeMenu = 'notifikasi-masuk';

        return view('sisfo::Notifikasi.NotifMasuk.detail', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu,
            'notifikasi' => $notifikasi,
            'kategori' => $kategori,
            'kategoriNama' => $kategoriNama
        ]);
    }

    // 4. ✅ UPDATE DATA - Handle multiple actions (tandai-dibaca, tandai-semua-dibaca)
    public function updateData(Request $request, $id)
    {
        $action = $request->input('action');

        // Action: tandai-dibaca (single notification)
        if ($action === 'tandai-dibaca') {
            $result = NotifMasukModel::tandaiDibaca($id);
            return response()->json($result);
        }

        // Action: tandai-semua-dibaca (all notifications by kategori)
        if ($action === 'tandai-semua-dibaca') {
            $kategori = $request->input('kategori');
            $result = NotifMasukModel::tandaiSemuaDibacaByKategori($kategori);
            return response()->json($result);
        }

        return response()->json([
            'success' => false,
            'message' => 'Action tidak valid'
        ], 400);
    }

    // 5. ✅ DELETE DATA - Handle multiple actions (hapus single, hapus semua)
    public function deleteData(Request $request, $id)
    {
        // GET method: Show confirmation page (jika diperlukan)
        if ($request->isMethod('GET')) {
            return response()->json([
                'success' => false,
                'message' => 'Gunakan method DELETE untuk menghapus notifikasi'
            ], 405);
        }

        // DELETE method: Process delete
        $action = $request->input('action');

        // Action: hapus single notification
        if ($action === 'hapus') {
            $result = NotifMasukModel::hapusNotifikasi($id);
            return response()->json($result);
        }

        // Action: hapus semua notifikasi yang sudah dibaca by kategori
        if ($action === 'hapus-semua-dibaca') {
            $kategori = $request->input('kategori');
            $result = NotifMasukModel::hapusSemuaDibacaByKategori($kategori);
            return response()->json($result);
        }

        return response()->json([
            'success' => false,
            'message' => 'Action tidak valid'
        ], 400);
    }

    public function getUnreadCount()
    {
        return NotifMasukModel::where('notif_masuk_dibaca_tgl', null)
            ->where('isDeleted', 0)
            ->count();
    }

    public function getBadgeCount()
    {
        return $this->getUnreadCount();
    }
}

