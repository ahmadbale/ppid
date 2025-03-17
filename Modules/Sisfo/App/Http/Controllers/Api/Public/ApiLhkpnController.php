<?php

namespace Modules\Sisfo\App\Http\Controllers\Api\Public;


use Illuminate\Http\Request;
use Modules\Sisfo\App\Http\Controllers\Api\BaseApiController;
use Modules\Sisfo\App\Models\Website\InformasiPublik\LHKPN\LHKPNModel;

class ApiLhkpnController extends BaseApiController
{

public function getDataLhkpn(Request $request)
{
    return $this->execute(
        function() use ($request) {
            $tahun = $request->input('tahun');
            $page = $request->input('page', 1);
            $perPage = $request->input('per_page', 5);
            $limitKaryawan = $request->input('limit_karyawan', 5);
            
            $getDataLhkpn = LHKPNModel::selectData($tahun, $page, $perPage, $limitKaryawan);
            return $getDataLhkpn;
        },
        'Data LHKPN'
    );
}
}