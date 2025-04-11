<?php

namespace Modules\Sisfo\App\Http\Controllers\AdminWeb\Footer;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Sisfo\App\Http\Controllers\TraitsController;
use Modules\Sisfo\App\Models\Website\Footer\KategoriFooterModel;
use Illuminate\Validation\ValidationException;

class KategoriFooterController extends Controller
{
    use TraitsController;

    public $breadcrumb = 'Manajemen Kategori Footer';
    public $pagename = 'AdminWeb/KategoriFooter';

    public function index(Request $request)
    {
        $search = $request->query('search', '');

        $breadcrumb = (object) [
            'title' => 'Manajemen Kategori Footer',
            'list' => ['Home', 'Kategori Footer']
        ];

        $page = (object) [
            'title' => 'Daftar Kategori Footer'
        ];

        $activeMenu = 'kategori-footer';
        
        $kategoriFooter = KategoriFooterModel::selectData(10, $search);

        return view("sisfo::AdminWeb/KategoriFooter.index", [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu,
            'kategoriFooter' => $kategoriFooter,
            'search' => $search
        ]);
    }

    public function getData(Request $request)
    {
        $search = $request->query('search', '');
        $kategoriFooter = KategoriFooterModel::selectData(10, $search);
        
        if ($request->ajax()) {
            return view('sisfo::AdminWeb/KategoriFooter.data', compact('kategoriFooter', 'search'))->render();
        }
        
        return redirect()->route('kategori-footer.index');
    }

    public function addData()
    {
        return view("sisfo::AdminWeb/KategoriFooter.create");
    }

    public function createData(Request $request)
    {
        try {
            KategoriFooterModel::validasiData($request);
            $result = KategoriFooterModel::createData($request);

            return $this->jsonSuccess(
                $result['data'] ?? null, 
                $result['message'] ?? 'Kategori footer berhasil dibuat'
            );
        } catch (ValidationException $e) {
            return $this->jsonValidationError($e);
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan saat membuat kategori footer');
        }
    }

    public function editData($id)
    {
        $kategoriFooter = KategoriFooterModel::detailData($id);

        return view("sisfo::AdminWeb/KategoriFooter.update", [
            'kategoriFooter' => $kategoriFooter
        ]);
    }

    public function updateData(Request $request, $id)
{
    try {
        
        KategoriFooterModel::validasiData($request, $id);
        $result = KategoriFooterModel::updateData($request, $id);

        return $this->jsonSuccess(
            $result['data'] ?? null, 
            $result['message'] ?? 'Kategori footer berhasil diperbarui'
        );
    } catch (ValidationException $e) {
        return $this->jsonValidationError($e);
    } catch (\Exception $e) {
        return $this->jsonError($e, 'Terjadi kesalahan saat memperbarui kategori footer');
    }
}

    public function detailData($id)
    {
        $kategoriFooter = KategoriFooterModel::detailData($id);
        
        return view("sisfo::AdminWeb/KategoriFooter.detail", [
            'kategoriFooter' => $kategoriFooter,
            'title' => 'Detail Kategori Footer'
        ]);
    }

    public function deleteData(Request $request, $id)
    {
        if ($request->isMethod('get')) {
            $kategoriFooter = KategoriFooterModel::detailData($id);
            
            return view("sisfo::AdminWeb/KategoriFooter.delete", [
                'kategoriFooter' => $kategoriFooter
            ]);
        }
        
        try {
            $result = KategoriFooterModel::deleteData($id);
            
            return $this->jsonSuccess(
                $result['data'] ?? null, 
                $result['message'] ?? 'Kategori footer berhasil dihapus'
            );
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan saat menghapus kategori footer');
        }
    }
}