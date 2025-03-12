<?php

namespace Modules\Sisfo\App\Http\Controllers\AdminWeb\Footer;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

use Modules\Sisfo\App\Http\Controllers\TraitsController;
use Modules\Sisfo\App\Models\Website\Footer\FooterModel;
use Modules\Sisfo\App\Models\Website\Footer\KategoriFooterModel;

class FooterController extends Controller
{
    use TraitsController;
    public $breadcrumb = 'Menu Footer';
    public $pagename = 'AdminWeb/footer';
    
    // Halaman index footer
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Manajemen Footer',
            'list' => ['Home', 'Footer', 'Daftar']
        ];

        $page = (object) [
            'title' => 'Daftar Footer'
        ];
        
        $activeMenu = 'footer';
        $kategoriFooters = KategoriFooterModel::all();

        return view('AdminWeb.Footer.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu,
            'kategoriFooters' => $kategoriFooters
        ]);
    }

    // Endpoint untuk DataTables
    public function list(Request $request)
    {
        return FooterModel::getDataTableList($request);
    }
    
    // Modal tambah footer
    public function create()
    {
        $kategoriFooters = KategoriFooterModel::all();
        return view('AdminWeb.Footer.create', compact('kategoriFooters'));
    }

    // Proses simpan footer
    public function store(Request $request)
    {
        $result = FooterModel::createData($request);
        return response()->json($result);
    }

    // Modal edit footer
    public function edit($id)
    {
        $result = FooterModel::getEditData($id);

        // Pastikan view bisa mengakses data 'footer'
        if ($result['success']) {
            $kategoriFooters = KategoriFooterModel::all();
            return view('AdminWeb.Footer.edit', [
                'footer' => $result['footer'],
                'kategoriFooters' => $kategoriFooters
            ]);
        }
        // Tangani kasus gagal
        return response()->json($result);
    }

    // Proses update footer
    public function update(Request $request, $id)
    {
        $result = FooterModel::updateData($request, $id);
        return response()->json($result);
    }

    // Lihat detail footer
    public function detail_footer($id)
    {
        $result = FooterModel::getDetailData($id);
        return response()->json($result);
    }

    // Proses hapus footer
    public function delete($id)
    {
        $result = FooterModel::deleteData($id);
        return response()->json($result);
    }
    
}