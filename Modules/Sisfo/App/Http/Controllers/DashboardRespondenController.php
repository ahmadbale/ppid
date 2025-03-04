<?php

namespace Modules\Sisfo\App\Http\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class DashboardRespondenController extends Controller
{
    public function index() {
        $breadcrumb = (object) [
            'title' => 'Selamat Datang Pengguna',
            'list' => ['Home', 'welcome']
        ];

        $activeMenu = 'dashboard';

        return view('sisfo::dashboardResponden', ['breadcrumb' => $breadcrumb, 'activeMenu' => $activeMenu]);
    }
}
