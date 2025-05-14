<?php

namespace Modules\Sisfo\App\Http\Controllers\Api\Public;

use Modules\Sisfo\App\Http\Controllers\Api\BaseApiController;
use Modules\Sisfo\App\Models\Website\LandingPage\KategoriAkses\KategoriAksesModel;


class ApiPintasanLainnyaController extends BaseApiController
{
    public function getDataPintasanLainnya()
    {
        return $this->executeWithSystemAuth(
            function() {
                $pintasanLainnya = KategoriAksesModel::getDataPintasanLainnya();
                return $pintasanLainnya;
            },
            'Laman Pintasan Lainnya'
        );
    }
}