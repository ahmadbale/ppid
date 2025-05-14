<?php

namespace Modules\Sisfo\App\Http\Controllers\Api\Public;

use Modules\Sisfo\App\Http\Controllers\Api\BaseApiController;
use Modules\Sisfo\App\Models\Website\LandingPage\KategoriAkses\KategoriAksesModel;

class ApiAksesCepatController extends BaseApiController
{
    public function getDataAksesCepat()
    {
        return $this->executeWithSystemAuth(
            function() {
                $aksesCepat = KategoriAksesModel::getDataAksesCepat();
                return $aksesCepat;
            },
            'Laman Akses Cepat'
        );
    }
}