<?php

namespace Modules\Sisfo\App\Http\Controllers;

use Illuminate\Routing\Controller;

class DashboardDefaultController extends Controller
{
    use TraitsController;

    public function index() {
        $breadcrumb = (object) [
            'title' => '',
            'list' => ['Home', 'welcome']
        ];

        $activeMenu = 'dashboard';

        return view('sisfo::dashboardDefault', ['breadcrumb' => $breadcrumb, 'activeMenu' => $activeMenu]);
    }
}
