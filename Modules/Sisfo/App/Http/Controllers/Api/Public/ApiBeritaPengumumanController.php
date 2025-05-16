<?php

namespace Modules\Sisfo\App\Http\Controllers\Api\Public;

use Modules\Sisfo\App\Http\Controllers\Api\BaseApiController;
use Modules\Sisfo\App\Models\Website\WebMenuModel;

class ApiBeritaPengumumanController extends BaseApiController
{
    public function getBeritaPengumuman()
    {
        return $this->executeWithSystemAuth(
            function() {
                $beritaPengumuman = WebMenuModel::selectBeritaPengumuman();
                return  $beritaPengumuman ;
            },
            'beritaPengumuman'
        );
    }
}