<?php

namespace Modules\Sisfo\App\Http\Controllers\Api\DaftarPengajuan\VerifPengajuan;

use Illuminate\Http\Request;
use Modules\Sisfo\App\Http\Controllers\Api\BaseApiController;
use Modules\Sisfo\App\Models\SistemInformasi\EForm\PermohonanPerawatanModel;

class ApiVerifPPController extends BaseApiController
{
    public function index(Request $request)
    {
        return $this->executeWithAuthentication(
            function ($user) use ($request) {
                $perPage = $request->get('per_page', 10);
                $page = $request->get('page', 1);
                $status = $request->get('status');

                $query = PermohonanPerawatanModel::where('isDeleted', 0)
                    ->where('pp_verifikasi_isDeleted', 0);

                if ($status) {
                    $query->where('pp_status', $status);
                }

                $permohonanPerawatan = $query->orderBy('created_at', 'desc')
                    ->paginate($perPage, ['*'], 'page', $page);

                return [
                    'data' => $permohonanPerawatan->items(),
                    'pagination' => [
                        'current_page' => $permohonanPerawatan->currentPage(),
                        'last_page' => $permohonanPerawatan->lastPage(),
                        'per_page' => $permohonanPerawatan->perPage(),
                        'total' => $permohonanPerawatan->total()
                    ]
                ];
            },
            'daftar verifikasi permohonan perawatan',
            self::ACTION_GET
        );
    }

    public function getApproveModal($id)
    {
        return $this->executeWithAuthentication(
            function ($user) use ($id) {
                return PermohonanPerawatanModel::findOrFail($id);
            },
            'detail permohonan perawatan untuk approval',
            self::ACTION_GET
        );
    }

    public function getDeclineModal($id)
    {
        return $this->executeWithAuthentication(
            function ($user) use ($id) {
                return PermohonanPerawatanModel::findOrFail($id);
            },
            'detail permohonan perawatan untuk penolakan',
            self::ACTION_GET
        );
    }

    public function setujuiPermohonan($id)
    {
        return $this->executeWithAuthentication(
            function ($user) use ($id) {
                $permohonanPerawatan = PermohonanPerawatanModel::findOrFail($id);
                return $permohonanPerawatan->validasiDanSetujuiPermohonan();
            },
            'persetujuan permohonan perawatan',
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

                $permohonanPerawatan = PermohonanPerawatanModel::findOrFail($id);
                return $permohonanPerawatan->validasiDanTolakPermohonan($request->alasan_penolakan);
            },
            'penolakan permohonan perawatan',
            self::ACTION_UPDATE
        );
    }

    public function tandaiDibaca($id)
    {
        return $this->executeWithAuthentication(
            function ($user) use ($id) {
                $permohonanPerawatan = PermohonanPerawatanModel::findOrFail($id);
                return $permohonanPerawatan->validasiDanTandaiDibaca();
            },
            'penandaan sebagai dibaca permohonan perawatan',
            self::ACTION_UPDATE
        );
    }

    public function hapusPermohonan($id)
    {
        return $this->executeWithAuthentication(
            function ($user) use ($id) {
                $permohonanPerawatan = PermohonanPerawatanModel::findOrFail($id);
                return $permohonanPerawatan->validasiDanHapusPermohonan();
            },
            'permohonan perawatan',
            self::ACTION_DELETE
        );
    }
}