<?php

namespace Modules\Sisfo\App\Http\Controllers\Api\Public;

use  Modules\Sisfo\App\Models\SistemInformasi\DashboardStatistics\DashboardStatisticsModel;
use Modules\Sisfo\App\Http\Controllers\Api\BaseApiController;

class ApiDashboardStatisticsController extends BaseApiController
{
    public function getDashboardStatistics()
    {
        return $this->executeWithSystemAuth(
            function () {
                $statistics = DashboardStatisticsModel::getDashboardStatistics();
                return $statistics;
            },
            'Laman Dashboard Statistics'
        );
    }
}