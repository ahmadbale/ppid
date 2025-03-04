<?php

namespace Modules\Sisfo\App\Http\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class DashboardAdminController extends Controller
{
    public function index() {
        $breadcrumb = (object) [
            'title' => 'Selamat Datang Pengguna',
            'list' => ['Home', 'welcome']
        ];

        $activeMenu = 'dashboard';

        return view('sisfo::dashboardAdmin', ['breadcrumb' => $breadcrumb, 'activeMenu' => $activeMenu]);
    }
}
