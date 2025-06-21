<?php

namespace Modules\Sisfo\App\Http\Controllers\Api\DaftarPengajuan\VerifPengajuan;

use Illuminate\Http\Request;
use Modules\Sisfo\App\Http\Controllers\Api\BaseApiController;
use Modules\Sisfo\App\Models\SistemInformasi\EForm\PengaduanMasyarakatModel;

class ApiVerifPMController extends BaseApiController
{
    public function index(Request $request)
    {
        return $this->executeWithAuthentication(
            function ($user) use ($request) {
                $perPage = $request->get('per_page', 10);
                $page = $request->get('page', 1);
                $status = $request->get('status');

                $query = PengaduanMasyarakatModel::where('isDeleted', 0)
                    ->where('pm_verifikasi_isDeleted', 0);

                if ($status) {
                    $query->where('pm_status', $status);
                }

                $pengaduanMasyarakat = $query->orderBy('created_at', 'desc')
                    ->paginate($perPage, ['*'], 'page', $page);

                return [
                    'data' => $pengaduanMasyarakat->items(),
                    'pagination' => [
                        'current_page' => $pengaduanMasyarakat->currentPage(),
                        'last_page' => $pengaduanMasyarakat->lastPage(),
                        'per_page' => $pengaduanMasyarakat->perPage(),
                        'total' => $pengaduanMasyarakat->total()
                    ]
                ];
            },
            'daftar verifikasi pengaduan masyarakat',
            self::ACTION_GET
        );
    }

    public function getApproveModal($id)
    {
        return $this->executeWithAuthentication(
            function ($user) use ($id) {
                return PengaduanMasyarakatModel::findOrFail($id);
            },
            'detail pengaduan masyarakat untuk approval',
            self::ACTION_GET
        );
    }

    public function getDeclineModal($id)
    {
        return $this->executeWithAuthentication(
            function ($user) use ($id) {
                return PengaduanMasyarakatModel::findOrFail($id);
            },
            'detail pengaduan masyarakat untuk penolakan',
            self::ACTION_GET
        );
    }

    public function setujuiPermohonan($id)
    {
        return $this->executeWithAuthentication(
            function ($user) use ($id) {
                $pengaduanMasyarakat = PengaduanMasyarakatModel::findOrFail($id);
                return $pengaduanMasyarakat->validasiDanSetujuiPermohonan();
            },
            'persetujuan pengaduan masyarakat',
            self::ACTION_UPDATE
        );
    }

    public function tolakPermohonan(Request $request, $id)
    {
        return $this->executeWithAuthAndValidation(
            function ($user) use ($request, $id) {
                // Validasi input
                $request->validate([
                    'alasan_penolakan' => 'required|string|max:255'
                ], [
                    'alasan_penolakan.required' => 'Alasan penolakan harus diisi',
                    'alasan_penolakan.max' => 'Alasan penolakan maksimal 255 karakter'
                ]);

                $pengaduanMasyarakat = PengaduanMasyarakatModel::findOrFail($id);
                return $pengaduanMasyarakat->validasiDanTolakPermohonan($request->alasan_penolakan);
            },
            'penolakan pengaduan masyarakat',
            self::ACTION_UPDATE
        );
    }

    public function tandaiDibaca($id)
    {
        return $this->executeWithAuthentication(
            function ($user) use ($id) {
                $pengaduanMasyarakat = PengaduanMasyarakatModel::findOrFail($id);
                return $pengaduanMasyarakat->validasiDanTandaiDibaca();
            },
            'penandaan sebagai dibaca pengaduan masyarakat',
            self::ACTION_UPDATE
        );
    }

    public function hapusPermohonan($id)
    {
        return $this->executeWithAuthentication(
            function ($user) use ($id) {
                $pengaduanMasyarakat = PengaduanMasyarakatModel::findOrFail($id);
                return $pengaduanMasyarakat->validasiDanHapusPermohonan();
            },
            'pengaduan masyarakat',
            self::ACTION_DELETE
        );
    }
}