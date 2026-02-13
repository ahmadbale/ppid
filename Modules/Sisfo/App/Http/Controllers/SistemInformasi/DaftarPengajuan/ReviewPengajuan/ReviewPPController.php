<?php

namespace Modules\Sisfo\App\Http\Controllers\SistemInformasi\DaftarPengajuan\ReviewPengajuan;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Sisfo\App\Http\Controllers\TraitsController;
use Modules\Sisfo\App\Models\SistemInformasi\EForm\PermohonanPerawatanModel;
use Modules\Sisfo\App\Models\Website\WebMenuModel;

class ReviewPPController extends Controller
{
    use TraitsController;

    public $breadcrumb = 'Daftar Review Pengajuan Permohonan Perawatan';
    public $pagename = 'Review Pengajuan Permohonan Perawatan';
    public $reviewPPUrl;

    public function __construct()
    {
        $this->reviewPPUrl = WebMenuModel::getDynamicMenuUrl('daftar-review-pengajuan-permohonan-perawatan');
    }

    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Review Permohonan Perawatan',
            'list' => ['Home', $this->breadcrumb, 'Review Permohonan Perawatan']
        ];

        $page = (object) [
            'title' => 'Review Permohonan Perawatan'
        ];

        // Ambil daftar Permohonan Perawatan untuk review dari model
        $permohonanPerawatan = PermohonanPerawatanModel::getDaftarReview();

        return view('sisfo::SistemInformasi.DaftarPengajuan.ReviewPengajuan.ReviewPermohonanPerawatan.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'permohonanPerawatan' => $permohonanPerawatan,
            'reviewPPUrl' => $this->reviewPPUrl,
            'daftarReviewPengajuanUrl' => WebMenuModel::getDynamicMenuUrl('daftar-review-pengajuan')
        ]);
    }

    public function getData()
    {
        try {
            $permohonanPerawatan = PermohonanPerawatanModel::getDaftarReview();
            return view('sisfo::SistemInformasi.DaftarPengajuan.ReviewPengajuan.ReviewPermohonanPerawatan.data', [
                'permohonanPerawatan' => $permohonanPerawatan
            ])->render();
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function editData($id)
    {
        try {
            $permohonanPerawatan = PermohonanPerawatanModel::findOrFail($id);
            $actionType = request('type', 'approve');
            
            return view('sisfo::SistemInformasi.DaftarPengajuan.ReviewPengajuan.ReviewPermohonanPerawatan.update', [
                'permohonanPerawatan' => $permohonanPerawatan,
                'actionType' => $actionType
            ])->render();
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function updateData(Request $request, $id)
    {
        try {
            $action = $request->input('action');
            
            if ($action === 'approve') {
                $request->validate([
                    'jawaban' => 'required|string',
                    'jawaban_file' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240'
                ]);
                
                $jawaban = $request->input('jawaban');
                
                // Handle file upload jika ada
                if ($request->hasFile('jawaban_file')) {
                    $file = $request->file('jawaban_file');
                    $filename = 'jawaban_pp_' . $id . '_' . time() . '.' . $file->getClientOriginalExtension();
                    $file->storeAs('jawaban_permohonan_perawatan', $filename, 'public');
                } else {
                    $filename = null;
                }
                
                $permohonanPerawatan = PermohonanPerawatanModel::findOrFail($id);
                $result = $permohonanPerawatan->validasiDanSetujuiReview($jawaban);
                return $this->jsonSuccess($result, 'Review permohonan perawatan berhasil disetujui');
                
            } elseif ($action === 'decline') {
                $request->validate([
                    'alasan_penolakan' => 'required|string'
                ]);
                
                $permohonanPerawatan = PermohonanPerawatanModel::findOrFail($id);
                $result = $permohonanPerawatan->validasiDanTolakReview($request->input('alasan_penolakan'));
                return $this->jsonSuccess($result, 'Review permohonan perawatan berhasil ditolak');
                
            } elseif ($action === 'read') {
                $permohonanPerawatan = PermohonanPerawatanModel::findOrFail($id);
                $result = $permohonanPerawatan->validasiDanTandaiDibacaReview();
                return $this->jsonSuccess($result, 'Review permohonan perawatan berhasil ditandai telah dibaca');
            }
            
            return response()->json(['error' => 'Invalid action'], 400);
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan pada server');
        }
    }

    public function deleteData($id)
    {
        try {
            $permohonanPerawatan = PermohonanPerawatanModel::findOrFail($id);
            $result = $permohonanPerawatan->validasiDanHapusReview();
            return $this->jsonSuccess($result, 'Review pengajuan ini telah dihapus dari halaman daftar Review Pengajuan');
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Gagal menghapus review');
        }
    }
}