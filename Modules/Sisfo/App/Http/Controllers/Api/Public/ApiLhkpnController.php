<?php

namespace Modules\Sisfo\App\Http\Controllers\Api\Public;

use Modules\Sisfo\App\Http\Controllers\Api\BaseApiController;
use Modules\Sisfo\App\Models\Website\InformasiPublik\LHKPN\LHKPNModel;
use Illuminate\Http\Request;

class ApiLhkpnController extends BaseApiController
{
    public function getDataLhkpn(Request $request)
    {
        return $this->execute(
            function () use ($request) {
                $tahun = $request->input('tahun');
                $perPage = $request->input('per_page', 10);
                $detailPage = $request->input('detail_page', []);

                $lhkpnData = LhkpnModel::getDataLhkpn($perPage, $tahun, $detailPage);
                return $lhkpnData;
            },
            'Laman LHKPN'
        );
    }
}