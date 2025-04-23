<?php

namespace Modules\Sisfo\App\Http\Controllers\Api\Public;

use Modules\Sisfo\App\Http\Controllers\Api\BaseApiController;
use Modules\Sisfo\App\Models\SistemInformasi\KategoriForm\KategoriFormModel;

class ApiTimelineController extends BaseApiController
{
     public function getDataTimeline()
     {
         return $this->execute(
             function() {
                 $getDataTimeline = KategoriFormModel::getDataTimeline();
                 return $getDataTimeline;
             },
             'laman Data Timeline'
         );
     }
 }