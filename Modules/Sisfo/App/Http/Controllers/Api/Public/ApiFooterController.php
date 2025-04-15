<?php

namespace Modules\Sisfo\App\Http\Controllers\Api\Public;

use Modules\Sisfo\App\Http\Controllers\Api\BaseApiController;
use Modules\Sisfo\App\Models\Website\Footer\KategoriFooterModel;

class ApiFooterController extends BaseApiController
{
     public function getDataFooter()
     {
         return $this->execute(
             function() {
                 $getDataFooter = KategoriFooterModel::getDataFooter();
                 return $getDataFooter;
             },
             'laman Data Footer'
         );
     }
 }