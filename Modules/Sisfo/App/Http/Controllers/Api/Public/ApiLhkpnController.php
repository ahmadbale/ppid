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
            function () use ($request) {
                $lhkpnData = LhkpnModel::getDataLhkpn($request);
                return $lhkpnData;
            },
            'Data LHKPN'
        );
    }

}