<?php

namespace Modules\Sisfo\App\Http\Controllers\SistemInformasi\DaftarPengajuan\ReviewPengajuan;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Sisfo\App\Http\Controllers\TraitsController;
use Modules\Sisfo\App\Models\SistemInformasi\EForm\WBSModel;
use Modules\Sisfo\App\Models\Website\WebMenuModel;

class ReviewWBSController extends Controller
{
    use TraitsController;

    public $breadcrumb = 'Daftar Review Pengajuan Whistle Blowing System';
    public $pagename = 'Review Pengajuan Whistle Blowing System';
    public $daftarReviewPengajuanUrl;

    public function __construct()
    {
        $this->daftarReviewPengajuanUrl = WebMenuModel::getDynamicMenuUrl('daftar-review-pengajuan');
    }

    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Review Whistle Blowing System',
            'list' => ['Home', $this->breadcrumb, 'Review Whistle Blowing System']
        ];

        $page = (object) [
            'title' => 'Review Whistle Blowing System'
        ];

        // Ambil daftar WBS untuk review dari model
        $whistleBlowingSystem = WBSModel::getDaftarReview();

        return view('sisfo::SistemInformasi.DaftarPengajuan.ReviewPengajuan.ReviewWBS.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'whistleBlowingSystem' => $whistleBlowingSystem,
            'daftarReviewPengajuanUrl' => $this->daftarReviewPengajuanUrl
        ]);
    }

    public function getData()
    {
        try {
            $whistleBlowingSystem = WBSModel::getDaftarReview();
            return view('sisfo::SistemInformasi.DaftarPengajuan.ReviewPengajuan.ReviewWBS.data', [
                'whistleBlowingSystem' => $whistleBlowingSystem
            ])->render();
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function editData($id)
    {
        try {
            $whistleBlowingSystem = WBSModel::findOrFail($id);
            $actionType = request('type', 'approve');
            
            return view('sisfo::SistemInformasi.DaftarPengajuan.ReviewPengajuan.ReviewWBS.update', [
                'whistleBlowingSystem' => $whistleBlowingSystem,
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
                    $filename = 'jawaban_wbs_' . $id . '_' . time() . '.' . $file->getClientOriginalExtension();
                    $file->storeAs('jawaban_whistle_blowing_system', $filename, 'public');
                } else {
                    $filename = null;
                }
                
                $whistleBlowingSystem = WBSModel::findOrFail($id);
                $result = $whistleBlowingSystem->validasiDanSetujuiReview($jawaban);
                return $this->jsonSuccess($result, 'Review whistle blowing system berhasil disetujui');
                
            } elseif ($action === 'decline') {
                $request->validate([
                    'alasan_penolakan' => 'required|string'
                ]);
                
                $whistleBlowingSystem = WBSModel::findOrFail($id);
                $result = $whistleBlowingSystem->validasiDanTolakReview($request->input('alasan_penolakan'));
                return $this->jsonSuccess($result, 'Review whistle blowing system berhasil ditolak');
                
            } elseif ($action === 'read') {
                $whistleBlowingSystem = WBSModel::findOrFail($id);
                $result = $whistleBlowingSystem->validasiDanTandaiDibacaReview();
                return $this->jsonSuccess($result, 'Review whistle blowing system berhasil ditandai telah dibaca');
            }
            
            return response()->json(['error' => 'Invalid action'], 400);
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan pada server');
        }
    }

    public function deleteData($id)
    {
        try {
            $whistleBlowingSystem = WBSModel::findOrFail($id);
            $result = $whistleBlowingSystem->validasiDanHapusReview();
            return $this->jsonSuccess($result, 'Review pengajuan ini telah dihapus dari halaman daftar Review Pengajuan');
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Gagal menghapus review');
        }
    }
}