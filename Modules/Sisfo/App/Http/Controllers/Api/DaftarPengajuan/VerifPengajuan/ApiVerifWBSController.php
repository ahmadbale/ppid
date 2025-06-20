<?php

namespace Modules\Sisfo\App\Http\Controllers\Api\DaftarPengajuan\VerifPengajuan;

use Illuminate\Http\Request;
use Modules\Sisfo\App\Http\Controllers\Api\BaseApiController;
use Modules\Sisfo\App\Models\SistemInformasi\EForm\WBSModel;

class ApiVerifWBSController extends BaseApiController
{
    public function index(Request $request)
    {
        return $this->executeWithAuthentication(
            function ($user) use ($request) {
                $perPage = $request->get('per_page', 10);
                $page = $request->get('page', 1);
                $status = $request->get('status');

                $query = WBSModel::where('isDeleted', 0)
                    ->where('wbs_verifikasi_isDeleted', 0);

                if ($status) {
                    $query->where('wbs_status', $status);
                }

                $whistleBlowingSystem = $query->orderBy('created_at', 'desc')
                    ->paginate($perPage, ['*'], 'page', $page);

                return [
                    'data' => $whistleBlowingSystem->items(),
                    'pagination' => [
                        'current_page' => $whistleBlowingSystem->currentPage(),
                        'last_page' => $whistleBlowingSystem->lastPage(),
                        'per_page' => $whistleBlowingSystem->perPage(),
                        'total' => $whistleBlowingSystem->total()
                    ]
                ];
            },
            'daftar verifikasi WBS',
            self::ACTION_GET
        );
    }

    public function getApproveModal($id)
    {
        return $this->executeWithAuthentication(
            function ($user) use ($id) {
                return WBSModel::findOrFail($id);
            },
            'detail WBS untuk approval',
            self::ACTION_GET
        );
    }

    public function getDeclineModal($id)
    {
        return $this->executeWithAuthentication(
            function ($user) use ($id) {
                return WBSModel::findOrFail($id);
            },
            'detail WBS untuk penolakan',
            self::ACTION_GET
        );
    }

    public function setujuiPermohonan($id)
    {
        return $this->executeWithAuthentication(
            function ($user) use ($id) {
                $whistleBlowingSystem = WBSModel::findOrFail($id);
                return $whistleBlowingSystem->validasiDanSetujuiPermohonan();
            },
            'persetujuan WBS',
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

                $whistleBlowingSystem = WBSModel::findOrFail($id);
                return $whistleBlowingSystem->validasiDanTolakPermohonan($request->alasan_penolakan);
            },
            'penolakan WBS',
            self::ACTION_UPDATE
        );
    }

    public function tandaiDibaca($id)
    {
        return $this->executeWithAuthentication(
            function ($user) use ($id) {
                $whistleBlowingSystem = WBSModel::findOrFail($id);
                return $whistleBlowingSystem->validasiDanTandaiDibaca();
            },
            'penandaan sebagai dibaca WBS',
            self::ACTION_UPDATE
        );
    }

    public function hapusPermohonan($id)
    {
        return $this->executeWithAuthentication(
            function ($user) use ($id) {
                $whistleBlowingSystem = WBSModel::findOrFail($id);
                return $whistleBlowingSystem->validasiDanHapusPermohonan();
            },
            'WBS',
            self::ACTION_DELETE
        );
    }
}