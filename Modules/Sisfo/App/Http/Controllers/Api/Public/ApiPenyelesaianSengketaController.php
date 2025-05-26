<?php

namespace Modules\Sisfo\App\Http\Controllers\Api\Public;
use Modules\Sisfo\App\Http\Controllers\Api\BaseApiController;
use Modules\Sisfo\App\Models\Website\InformasiPublik\PenyelesaianSengketa\PenyelesaianSengketaModel;

class ApiPenyelesaianSengketaController extends BaseApiController
{
    public function getDataPenyelesaianSengketa()
    {
        return $this->executeWithSystemAuth(
            function() {
                $getDataPenyelesaianSengketa = PenyelesaianSengketaModel::getDataPenyelesaianSengketa();
                return $getDataPenyelesaianSengketa;
            },
            'laman Penyelesaian Sengketa'
        );
    }
}