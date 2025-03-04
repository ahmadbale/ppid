<?php

namespace Modules\Sisfo\App\Http\Controllers\Api\Auth;

use Modules\Sisfo\App\Http\Controllers\Api\BaseApiController;
use Modules\Sisfo\App\Models\Website\WebMenuModel;

class BeritaPengumumanController extends BaseApiController
{
    public function getBeritaPengumuman()
    {
        return $this->executeWithAuth(
            function() {
                $beritaPengumuman = WebMenuModel::selectBeritaPengumuman();
                return  $beritaPengumuman ;
            },
            'beritaPengumuman'
        );
    }
}