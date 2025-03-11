<?php

namespace Modules\Sisfo\App\Http\Controllers\Api\Auth;

use Modules\Sisfo\App\Models\Website\Footer\FooterModel;
use Modules\Sisfo\App\Http\Controllers\Api\BaseApiController;


class ApiFooterController extends BaseApiController
{
     public function getDataFooter()
     {
         return $this->eksekusiDenganOtentikasi(
             function() {
                 $footerData = FooterModel::getDataFooter();
                 return $footerData;
             },
             'footer_data'
         );
     }
 }