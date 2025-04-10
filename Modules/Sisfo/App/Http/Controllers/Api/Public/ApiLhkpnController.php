<?php

namespace Modules\Sisfo\App\Http\Controllers\Api\Public;
use Modules\Sisfo\App\Http\Controllers\Api\BaseApiController;
use Modules\Sisfo\App\Models\Website\InformasiPublik\LHKPN\LHKPNModel;

class ApiLhkpnController extends BaseApiController
{
    public function getDataLhkpn()
    {
        return $this->execute(
            function () {
                $lhkpnData = LhkpnModel::getDataLhkpn();
                return $lhkpnData;
            },
            'Data LHKPN'
        );
    }

}