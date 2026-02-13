<?php

namespace Modules\Sisfo\App\Http\Controllers\SistemInformasi\DaftarPengajuan\ReviewPengajuan;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Sisfo\App\Http\Controllers\TraitsController;
use Modules\Sisfo\App\Models\SistemInformasi\EForm\PengaduanMasyarakatModel;
use Modules\Sisfo\App\Models\Website\WebMenuModel;

class ReviewPMController extends Controller
{
    use TraitsController;

    public $breadcrumb = 'Daftar Review Pengajuan Pengaduan Masyarakat';
    public $pagename = 'Review Pengajuan Pengaduan Masyarakat';
    public $daftarReviewPengajuanUrl;

    public function __construct()
    {
        $this->daftarReviewPengajuanUrl = WebMenuModel::getDynamicMenuUrl('daftar-review-pengajuan');
    }

    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Review Pengaduan Masyarakat',
            'list' => ['Home', $this->breadcrumb, 'Review Pengaduan Masyarakat']
        ];

        $page = (object) [
            'title' => 'Review Pengaduan Masyarakat'
        ];

        // Ambil daftar pengaduan masyarakat untuk review dari model
        $pengaduanMasyarakat = PengaduanMasyarakatModel::getDaftarReview();

        return view('sisfo::SistemInformasi.DaftarPengajuan.ReviewPengajuan.ReviewPengaduanMasyarakat.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'pengaduanMasyarakat' => $pengaduanMasyarakat,
            'daftarReviewPengajuanUrl' => $this->daftarReviewPengajuanUrl
        ]);
    }

    public function getData()
    {
        try {
            $pengaduanMasyarakat = PengaduanMasyarakatModel::getDaftarReview();
            return view('sisfo::SistemInformasi.DaftarPengajuan.ReviewPengajuan.ReviewPengaduanMasyarakat.data', [
                'pengaduanMasyarakat' => $pengaduanMasyarakat,
                'daftarReviewPengajuanUrl' => $this->daftarReviewPengajuanUrl
            ])->render();
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Gagal memuat data review pengaduan masyarakat');
        }
    }

    public function editData($id)
    {
        try {
            $pengaduanMasyarakat = PengaduanMasyarakatModel::findOrFail($id);
            $actionType = request('type', 'approve');
            
            return view('sisfo::SistemInformasi.DaftarPengajuan.ReviewPengajuan.ReviewPengaduanMasyarakat.update', [
                'pengaduanMasyarakat' => $pengaduanMasyarakat,
                'actionType' => $actionType
            ])->render();
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Gagal memuat data pengaduan masyarakat');
        }
    }

    public function updateData(Request $request, $id)
    {
        try {
            $action = $request->input('action');
            $pengaduanMasyarakat = PengaduanMasyarakatModel::findOrFail($id);

            if ($action === 'approve') {
                $request->validate([
                    'jawaban' => 'required',
                    'jawaban_file' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240'
                ]);

                $jawaban = $request->jawaban;
                
                if ($request->hasFile('jawaban_file')) {
                    $file = $request->file('jawaban_file');
                    $fileName = 'jawaban_pm_' . $id . '_' . time() . '.' . $file->getClientOriginalExtension();
                    $filePath = $file->storeAs('jawaban_pengaduan_masyarakat', $fileName, 'public');
                    $jawaban = $filePath;
                }

                $result = $pengaduanMasyarakat->validasiDanSetujuiReview($jawaban);
                return $this->jsonSuccess($result, 'Review pengaduan masyarakat berhasil disetujui');
                
            } elseif ($action === 'decline') {
                $request->validate([
                    'alasan_penolakan' => 'required'
                ]);
                
                $result = $pengaduanMasyarakat->validasiDanTolakReview($request->alasan_penolakan);
                return $this->jsonSuccess($result, 'Review pengaduan masyarakat berhasil ditolak');
                
            } elseif ($action === 'read') {
                $result = $pengaduanMasyarakat->validasiDanTandaiDibacaReview();
                return $this->jsonSuccess($result, 'Review pengaduan masyarakat berhasil ditandai dibaca');
            }

            return response()->json(['error' => 'Invalid action'], 400);
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Gagal memproses data review pengaduan masyarakat');
        }
    }

    public function deleteData($id)
    {
        try {
            $pengaduanMasyarakat = PengaduanMasyarakatModel::findOrFail($id);
            $result = $pengaduanMasyarakat->validasiDanHapusReview();
            return $this->jsonSuccess($result, 'Review pengajuan ini telah dihapus dari halaman daftar Review Pengajuan');
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Gagal menghapus review pengaduan masyarakat');
        }
    }
}