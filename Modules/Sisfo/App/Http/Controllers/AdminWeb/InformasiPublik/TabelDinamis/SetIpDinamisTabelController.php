<?php

namespace Modules\Sisfo\App\Http\Controllers\AdminWeb\InformasiPublik\TabelDinamis;

use Modules\Sisfo\App\Http\Controllers\TraitsController;
use Modules\Sisfo\App\Models\Website\InformasiPublik\TabelDinamis\IpMenuUtamaModel;
use Modules\Sisfo\App\Models\Website\InformasiPublik\TabelDinamis\IpDinamisTabelModel;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\ValidationException;

class SetIpDinamisTabelController extends Controller
{
    use TraitsController;

    public $breadcrumb = 'Set Informasi Publik Dinamis Tabel';
    public $pagename = 'AdminWeb/InformasiPublik/SetIpDinamisTabel';

    public function index(Request $request)
    {
        $search = $request->query('search', '');

        $breadcrumb = (object) [
            'title' => 'Set Informasi Publik Dinamis Tabel',
            'list' => ['Home', 'Informasi Publik', 'Set Informasi Publik Dinamis Tabel']
        ];

        $page = (object) [
            'title' => 'Daftar Set Informasi Publik Dinamis Tabel'
        ];

        $activeMenu = 'setipdinamistabel';

        // Gunakan hierarkis dan pencarian
        $setIpDinamisTabel = IpMenuUtamaModel::selectDataWithHierarchy(null, $search);

        // Ambil kategori IP dinamis tabel untuk dropdown
        $ipDinamisTabel = IpDinamisTabelModel::where('isDeleted', 0)->get();

        return view("sisfo::AdminWeb.InformasiPublik.SetIpDinamisTabel.index", [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu,
            'setIpDinamisTabel' => $setIpDinamisTabel,
            'search' => $search,
            'ipDinamisTabel' => $ipDinamisTabel
        ]);
    }

    public function getData(Request $request)
    {
        $search = $request->query('search', '');
        $kategori = $request->query('kategori', '');

        if ($kategori) {
            // Filter berdasarkan kategori IP dinamis tabel
            $setIpDinamisTabel = IpMenuUtamaModel::getMenusByKategori($kategori);
        } else {
            // Gunakan filter cerdas dengan pencarian
            $setIpDinamisTabel = IpMenuUtamaModel::selectDataWithHierarchy(null, $search);
        }

        if ($request->ajax()) {
            return view('sisfo::AdminWeb.InformasiPublik.SetIpDinamisTabel.data', compact('setIpDinamisTabel', 'search'))->render();
        }

        return redirect()->back();
    }

    public function addData()
    {
        // Ambil daftar kategori IP dinamis tabel untuk dropdown
        $ipDinamisTabel = IpDinamisTabelModel::where('isDeleted', 0)->get();

        return view("sisfo::AdminWeb.InformasiPublik.SetIpDinamisTabel.create", [
            'ipDinamisTabel' => $ipDinamisTabel
        ]);
    }

    public function createData(Request $request)
    {
        try {
            IpMenuUtamaModel::validasiData($request);
            $result = IpMenuUtamaModel::createDataWithHierarchy($request);

            return $this->jsonSuccess(
                $result['data'] ?? null,
                $result['message'] ?? 'Set Informasi Publik Dinamis Tabel berhasil dibuat'
            );
        } catch (ValidationException $e) {
            return $this->jsonValidationError($e);
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan saat membuat Set Informasi Publik Dinamis Tabel');
        }
    }

    public function editData($id)
    {
        // Ambil data menu utama dengan hierarki
        $setIpDinamisTabel = IpMenuUtamaModel::detailDataWithHierarchy($id);

        // Ambil daftar kategori IP dinamis tabel untuk dropdown
        $ipDinamisTabel = IpDinamisTabelModel::where('isDeleted', 0)->get();

        return view("sisfo::AdminWeb.InformasiPublik.SetIpDinamisTabel.update", [
            'setIpDinamisTabel' => $setIpDinamisTabel,
            'ipDinamisTabel' => $ipDinamisTabel
        ]);
    }

    public function updateData(Request $request, $id)
    {
        try {
            IpMenuUtamaModel::validasiDataUpdate($request);
            $result = IpMenuUtamaModel::updateDataWithHierarchy($request, $id);

            return $this->jsonSuccess(
                $result['data'] ?? null,
                $result['message'] ?? 'Set Informasi Publik Dinamis Tabel berhasil diperbarui'
            );
        } catch (ValidationException $e) {
            return $this->jsonValidationError($e);
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan saat memperbarui Set Informasi Publik Dinamis Tabel');
        }
    }

    public function detailData($id)
    {
        $setIpDinamisTabel = IpMenuUtamaModel::detailDataWithHierarchy($id);

        return view("sisfo::AdminWeb.InformasiPublik.SetIpDinamisTabel.detail", [
            'setIpDinamisTabel' => $setIpDinamisTabel,
            'title' => 'Detail Set Informasi Publik Dinamis Tabel'
        ]);
    }

    public function deleteData(Request $request, $id)
    {
        if ($request->isMethod('get')) {
            $setIpDinamisTabel = IpMenuUtamaModel::detailDataWithHierarchy($id);

            return view("sisfo::AdminWeb.InformasiPublik.SetIpDinamisTabel.delete", [
                'setIpDinamisTabel' => $setIpDinamisTabel
            ]);
        }

        try {
            $result = IpMenuUtamaModel::deleteDataWithValidation($id);

            return $this->jsonSuccess(
                $result['data'] ?? null,
                $result['message'] ?? 'Set Informasi Publik Dinamis Tabel berhasil dihapus'
            );
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}