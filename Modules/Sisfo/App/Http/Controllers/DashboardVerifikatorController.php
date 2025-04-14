<?php

namespace Modules\Sisfo\App\Http\Controllers;

use Illuminate\Routing\Controller;

class DashboardVerifikatorController extends Controller
{
    use TraitsController;

    public function index() {
        $breadcrumb = (object) [
            'title' => 'Verifikator',
            'list' => ['Home', 'welcome']
        ];

        $activeMenu = 'dashboard';

        return view('sisfo::dashboardVFR', ['breadcrumb' => $breadcrumb, 'activeMenu' => $activeMenu]);
    }
}
