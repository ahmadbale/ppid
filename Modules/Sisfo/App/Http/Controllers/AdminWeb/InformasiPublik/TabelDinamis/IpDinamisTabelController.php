<?php

namespace Modules\Sisfo\App\Http\Controllers\AdminWeb\InformasiPublik\TabelDinamis;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Sisfo\App\Http\Controllers\TraitsController;
use Illuminate\Validation\ValidationException;
use Modules\Sisfo\App\Models\Website\InformasiPublik\TabelDinamis\IpDinamisTabelModel;

class IpDinamisTabelController extends Controller
{
    use TraitsController;

    public $breadcrumb = 'Informasi Publik Dinamis Tabel';
    public $pagename = 'AdminWeb/InformasiPublik/IpDinamisTabel';

    public function index(Request $request)
    {
        $search = $request->query('search', '');
        $kategori = $request->query('kategori', ''); // Tambahkan parameter kategori

        $breadcrumb = (object)[
            'title' => 'Pengaturan Informasi Publik Dinamis Tabel',
            'list' => ['Home', 'Pengaturan Informasi Publik Dinamis Tabel']
        ];

        $page = (object)[
            'title' => 'Daftar Informasi Publik Dinamis Tabel'
        ];

        $activeMenu = 'Informasi Publik Dinamis Tabel';

        // Filter berdasarkan kategori jika diperlukan
        $ipDinamisTabel = IpDinamisTabelModel::selectData(10, $search, $kategori);

        return view("sisfo::AdminWeb/InformasiPublik/IpDinamisTabel.index", [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu,
            'ipDinamisTabel' => $ipDinamisTabel,
            'search' => $search,
            'kategori' => $kategori // Kirim variabel kategori ke view
        ]);
    }

    public function getData(Request $request)
    {
        $search = $request->query('search', '');
        $kategori = $request->query('kategori', ''); // Tambahkan parameter kategori
        
        $ipDinamisTabel = IpDinamisTabelModel::selectData(10, $search, $kategori);

        if ($request->ajax()) {
            return view('sisfo::AdminWeb/InformasiPublik/IpDinamisTabel.data', compact('ipDinamisTabel', 'search', 'kategori'))->render();
        }

        return redirect()->route('kategori-informasi-publik-dinamis-tabel.index');
    }

    // ... existing methods remain the same ...
    public function addData()
    {
        return view("sisfo::AdminWeb/InformasiPublik/IpDinamisTabel.create");
    }

    public function createData(Request $request)
    {
        try {
            IpDinamisTabelModel::validasiData($request);
            $result = IpDinamisTabelModel::createData($request);

            return $this->jsonSuccess(
                $result['data'] ?? null,
                $result['message'] ?? 'IpDinamis Tabel berhasil dibuat'
            );
        } catch (ValidationException $e) {
            return $this->jsonValidationError($e);
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan saat membuat IpDinamis Tabel');
        }
    }

    public function editData($id)
    {
        $ipDinamisTabel = IpDinamisTabelModel::detailData($id);

        return view("sisfo::AdminWeb/InformasiPublik/IpDinamisTabel.update", [
            'IpDinamisTabel' => $ipDinamisTabel
        ]);
    }

    public function updateData(Request $request, $id)
    {
        try {
            IpDinamisTabelModel::validasiData($request);
            $result = IpDinamisTabelModel::updateData($request, $id);

            return $this->jsonSuccess(
                $result['data'] ?? null,
                $result['message'] ?? 'IpDinamis Tabel berhasil diperbarui'
            );
        } catch (ValidationException $e) {
            return $this->jsonValidationError($e);
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan saat memperbarui IpDinamis Tabel');
        }
    }

    public function detailData($id)
    {
        $ipDinamisTabel = IpDinamisTabelModel::detailData($id);

        return view("sisfo::AdminWeb/InformasiPublik/IpDinamisTabel.detail", [
            'IpDinamisTabel' => $ipDinamisTabel,
            'title' => 'Detail IpDinamis Tabel'
        ]);
    }

    public function deleteData(Request $request, $id)
    {
        if ($request->isMethod('get')) {
            $ipDinamisTabel = IpDinamisTabelModel::detailData($id);

            return view("sisfo::AdminWeb/InformasiPublik/IpDinamisTabel.delete", [
                'IpDinamisTabel' => $ipDinamisTabel
            ]);
        }

        try {
            $result = IpDinamisTabelModel::deleteData($id);

            return $this->jsonSuccess(
                $result['data'] ?? null,
                $result['message'] ?? 'IpDinamis Tabel berhasil dihapus'
            );
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan saat menghapus IpDinamis Tabel');
        }
    }
}