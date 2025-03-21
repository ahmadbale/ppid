<?php

namespace Modules\Sisfo\App\Http\Controllers\Api\Public;

use Modules\Sisfo\App\Http\Controllers\Api\BaseApiController;
use Modules\Sisfo\App\Models\Website\WebMenuModel;

class ApiMenuController extends BaseApiController
{
    
    public function getDataMenu()
    {
        return $this->execute(
            function() {
                $menu = WebMenuModel::getDataMenu();
                return $menu;
            },
            'menu' // hanya penanda data yang akan diambil
        );
    }
}