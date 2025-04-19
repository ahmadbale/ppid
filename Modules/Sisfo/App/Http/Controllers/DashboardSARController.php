<?php

namespace Modules\Sisfo\App\Http\Controllers;

use Illuminate\Routing\Controller;

class DashboardSARController extends Controller
{
    use TraitsController;

    public function index() {
        $breadcrumb = (object) [
            'title' => '',
            'list' => ['Home', 'welcome']
        ];

        $activeMenu = 'dashboard';

        return view('sisfo::dashboardSAR', ['breadcrumb' => $breadcrumb, 'activeMenu' => $activeMenu]);
    }
}
