<?php

namespace Modules\Sisfo\App\Http\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class DashboardVerifikatorController extends Controller
{
    public function index() {
        $breadcrumb = (object) [
            'title' => 'Selamat Datang Pengguna',
            'list' => ['Home', 'welcome']
        ];

        $activeMenu = 'dashboard';

        return view('sisfo::dashboardVFR', ['breadcrumb' => $breadcrumb, 'activeMenu' => $activeMenu]);
    }
}
