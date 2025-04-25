<?php

namespace Modules\Sisfo\App\Http\Controllers\Api\Public;

use Modules\Sisfo\App\Http\Controllers\Api\BaseApiController;
use Modules\Sisfo\App\Models\Website\InformasiPublik\Regulasi\RegulasiDinamisModel;

class ApiRegulasiController extends BaseApiController
{
     public function getDataRegulasi()
     {
         return $this->execute(
             function() {
                 $getDataRegulasi = RegulasiDinamisModel::getDataRegulasi();
                 return $getDataRegulasi;
             },
             'laman Data Regulasi'
         );
     }
 }