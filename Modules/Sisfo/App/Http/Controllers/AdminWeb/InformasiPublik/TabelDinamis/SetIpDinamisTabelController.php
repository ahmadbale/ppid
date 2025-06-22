<?php

namespace Modules\Sisfo\App\Http\Controllers\AdminWeb\InformasiPublik\TabelDinamis;

use Modules\Sisfo\App\Http\Controllers\TraitsController;
use Modules\Sisfo\App\Models\Website\InformasiPublik\TabelDinamis\IpMenuUtamaModel;
use Modules\Sisfo\App\Models\Website\InformasiPublik\TabelDinamis\IpDinamisTabelModel;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\ValidationException;
use Modules\Sisfo\App\Models\Website\InformasiPublik\TabelDinamis\IpSubMenuModel;
use Modules\Sisfo\App\Models\Website\InformasiPublik\TabelDinamis\IpSubMenuUtamaModel;

class SetIpDinamisTabelController extends Controller
{
    use TraitsController;

    public $breadcrumb = 'Set Informasi Publik Dinamis Tabel';
    public $pagename = 'AdminWeb/InformasiPublik/SetIpDinamisTabel';

    public function index(Request $request)
    {
        $search = $request->query('search', '');
        $kategori = $request->query('kategori', '');

        $breadcrumb = (object) [
            'title' => 'Set Informasi Publik Dinamis Tabel',
            'list' => ['Home', 'Informasi Publik', 'Set Informasi Publik Dinamis Tabel']
        ];

        $page = (object) [
            'title' => 'Daftar Set Informasi Publik Dinamis Tabel'
        ];

        $activeMenu = 'setipdinamistabel';

        // Ambil kategori IP dinamis tabel untuk dropdown filter
        $ipDinamisTabel = IpDinamisTabelModel::where('isDeleted', 0)
            ->orderBy('ip_nama_submenu', 'asc')
            ->get();

        // Pastikan kategori adalah string/integer yang valid
        $kategoriId = null;
        if (!empty($kategori) && is_numeric($kategori)) {
            $kategoriId = (int) $kategori;
        }

        // Filter data berdasarkan kategori jika dipilih
        if ($kategoriId) {
            $setIpDinamisTabel = IpMenuUtamaModel::getMenusByKategori($kategoriId, $search);
        } else {
            $setIpDinamisTabel = IpMenuUtamaModel::selectDataWithHierarchy(null, $search);
        }

        return view("sisfo::AdminWeb.InformasiPublik.SetIpDinamisTabel.index", [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu,
            'setIpDinamisTabel' => $setIpDinamisTabel,
            'search' => $search,
            'kategori' => $kategori, // Kirim sebagai string untuk view
            'ipDinamisTabel' => $ipDinamisTabel
        ]);
    }

    public function getData(Request $request)
    {
        $search = $request->query('search', '');
        $kategori = $request->query('kategori', '');

        // Pastikan kategori adalah string/integer yang valid
        $kategoriId = null;
        if (!empty($kategori) && is_numeric($kategori)) {
            $kategoriId = (int) $kategori;
        }

        if ($kategoriId) {
            // Filter berdasarkan kategori IP dinamis tabel
            $setIpDinamisTabel = IpMenuUtamaModel::getMenusByKategori($kategoriId, $search);
        } else {
            // Gunakan filter cerdas dengan pencarian
            $setIpDinamisTabel = IpMenuUtamaModel::selectDataWithHierarchy(null, $search);
        }

        // Ambil info kategori untuk pesan kosong
        $kategoriInfo = null;
        if ($kategoriId) {
            $kategoriInfo = IpDinamisTabelModel::find($kategoriId);
        }

        if ($request->ajax()) {
            return view('sisfo::AdminWeb.InformasiPublik.SetIpDinamisTabel.data', compact('setIpDinamisTabel', 'search', 'kategori', 'kategoriInfo'))->render();
        }

        return redirect()->back();
    }

    // ... existing methods remain the same ...
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
        try {
            // Ambil data menu utama dengan hierarki
            $ipMenuUtama = IpMenuUtamaModel::detailDataWithHierarchy($id);

            // Ambil daftar kategori IP dinamis tabel untuk dropdown
            $ipDinamisTabel = IpDinamisTabelModel::where('isDeleted', 0)->get();

            return view("sisfo::AdminWeb.InformasiPublik.SetIpDinamisTabel.update", [
                'ipMenuUtama' => $ipMenuUtama,
                'ipDinamisTabel' => $ipDinamisTabel
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateData(Request $request, $id)
    {
        try {
            $result = IpMenuUtamaModel::updateDataWithComplexHierarchy($request, $id);

            return $this->jsonSuccess(
                $result['data'] ?? null,
                $result['message'] ?? 'Menu Utama berhasil diperbarui'
            );
        } catch (ValidationException $e) {
            return $this->jsonValidationError($e);
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan saat memperbarui Menu Utama');
        }
    }

    public function detailData($id)
    {
        try {
            $ipMenuUtama = IpMenuUtamaModel::detailDataWithHierarchy($id);

            return view("sisfo::AdminWeb.InformasiPublik.SetIpDinamisTabel.detail", [
                'ipMenuUtama' => $ipMenuUtama,
                'title' => 'Detail Set Informasi Publik Dinamis Tabel'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteData(Request $request, $id)
    {
        if ($request->isMethod('get')) {
            try {
                $ipMenuUtama = IpMenuUtamaModel::detailDataWithHierarchy($id);

                return view("sisfo::AdminWeb.InformasiPublik.SetIpDinamisTabel.delete", [
                    'ipMenuUtama' => $ipMenuUtama
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }
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

    public function editSubMenuUtama($id)
    {
        try {
            $ipSubMenuUtama = IpSubMenuUtamaModel::with(['IpMenuUtama', 'IpSubMenu'])->findOrFail($id);

            return view("sisfo::AdminWeb.InformasiPublik.SetIpDinamisTabel.updateSubMenuUtama", [
                'ipSubMenuUtama' => $ipSubMenuUtama
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateSubMenuUtama(Request $request, $id)
    {
        try {
            IpSubMenuUtamaModel::validasiDataUpdate($request);
            $result = IpSubMenuUtamaModel::updateDataWithChildren($request, $id);

            return $this->jsonSuccess(
                $result['data'] ?? null,
                $result['message'] ?? 'Sub Menu Utama berhasil diperbarui'
            );
        } catch (ValidationException $e) {
            return $this->jsonValidationError($e);
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan saat memperbarui Sub Menu Utama');
        }
    }

    public function detailSubMenuUtama($id)
    {
        try {
            $ipSubMenuUtama = IpSubMenuUtamaModel::with(['IpMenuUtama', 'IpSubMenu'])->findOrFail($id);

            return view("sisfo::AdminWeb.InformasiPublik.SetIpDinamisTabel.detailSubMenuUtama", [
                'ipSubMenuUtama' => $ipSubMenuUtama,
                'title' => 'Detail Sub Menu Utama'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteSubMenuUtama(Request $request, $id)
    {
        if ($request->isMethod('get')) {
            try {
                $ipSubMenuUtama = IpSubMenuUtamaModel::with(['IpMenuUtama', 'IpSubMenu'])->findOrFail($id);

                return view("sisfo::AdminWeb.InformasiPublik.SetIpDinamisTabel.deleteSubMenuUtama", [
                    'ipSubMenuUtama' => $ipSubMenuUtama
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }
        }

        try {
            $result = IpSubMenuUtamaModel::deleteDataWithValidation($id);

            return $this->jsonSuccess(
                $result['data'] ?? null,
                $result['message'] ?? 'Sub Menu Utama berhasil dihapus'
            );
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Sub Menu Methods
    public function editSubMenu($id)
    {
        try {
            $ipSubMenu = IpSubMenuModel::with(['IpSubMenuUtama.IpMenuUtama'])->findOrFail($id);

            return view("sisfo::AdminWeb.InformasiPublik.SetIpDinamisTabel.updateSubMenu", [
                'ipSubMenu' => $ipSubMenu
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateSubMenu(Request $request, $id)
    {
        try {
            IpSubMenuModel::validasiDataUpdate($request);
            $result = IpSubMenuModel::updateDataSimple($request, $id);

            return $this->jsonSuccess(
                $result['data'] ?? null,
                $result['message'] ?? 'Sub Menu berhasil diperbarui'
            );
        } catch (ValidationException $e) {
            return $this->jsonValidationError($e);
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan saat memperbarui Sub Menu');
        }
    }

    public function detailSubMenu($id)
    {
        try {
            $ipSubMenu = IpSubMenuModel::with(['IpSubMenuUtama.IpMenuUtama'])->findOrFail($id);

            return view("sisfo::AdminWeb.InformasiPublik.SetIpDinamisTabel.detailSubMenu", [
                'ipSubMenu' => $ipSubMenu,
                'title' => 'Detail Sub Menu'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteSubMenu(Request $request, $id)
    {
        if ($request->isMethod('get')) {
            try {
                $ipSubMenu = IpSubMenuModel::with(['IpSubMenuUtama.IpMenuUtama'])->findOrFail($id);

                return view("sisfo::AdminWeb.InformasiPublik.SetIpDinamisTabel.deleteSubMenu", [
                    'ipSubMenu' => $ipSubMenu
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }
        }

        try {
            $result = IpSubMenuModel::deleteDataWithValidation($id);

            return $this->jsonSuccess(
                $result['data'] ?? null,
                $result['message'] ?? 'Sub Menu berhasil dihapus'
            );
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
