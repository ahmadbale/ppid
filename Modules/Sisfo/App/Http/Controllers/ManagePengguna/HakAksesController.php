<?php

namespace Modules\Sisfo\App\Http\Controllers\ManagePengguna;

use Modules\Sisfo\App\Http\Controllers\TraitsController;
use Modules\Sisfo\App\Models\HakAksesModel;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class HakAksesController extends Controller
{
    use TraitsController;

    public $breadcrumb = 'Pengaturan Level';
    public $pagename = 'ManagePengguna/ManageLevel';

    public function index(Request $request)
    {
        $search = $request->query('search', '');

        $breadcrumb = (object) [
            'title' => 'Pengaturan Level',
            'list' => ['Home', 'Pengaturan Level']
        ];

        $page = (object) [
            'title' => 'Daftar Level'
        ];

        $activeMenu = 'managelevel';

        // Gunakan pagination dan pencarian
        $level = HakAksesModel::selectData(10, $search);
        
        return view("sisfo::ManagePengguna/ManageLevel.index", [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu,
            'level' => $level,
            'search' => $search
        ]);
    }

    // Update getData untuk mendukung pagination dan pencarian
    public function getData(Request $request)
    {
        $search = $request->query('search', '');
        $level = HakAksesModel::selectData(10, $search);

        if ($request->ajax()) {
            return view('sisfo::ManagePengguna/ManageLevel.data', compact('level', 'search'))->render();
        }

        return redirect()->route('level.index');
    }

    public function addData()
    {
        try {
            return view("sisfo::ManagePengguna/ManageLevel.create");
        } catch (\Exception $e) {
            Log::error('Add Data Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function createData(Request $request)
    {
        try {
            HakAksesModel::validasiData($request);
            $result = HakAksesModel::createData($request);

            return $this->jsonSuccess(
                $result['data'] ?? null,
                $result['message'] ?? 'Level berhasil dibuat'
            );
        } catch (ValidationException $e) {
            return $this->jsonValidationError($e);
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan saat membuat level');
        }
    }

    public function editData($id)
    {
        $level = HakAksesModel::detailData($id);

        return view("sisfo::ManagePengguna/ManageLevel.update", [
            'level' => $level
        ]);
    }

    public function updateData(Request $request, $id)
    {
        try {
            HakAksesModel::validasiData($request);
            $result = HakAksesModel::updateData($request, $id);

            return $this->jsonSuccess(
                $result['data'] ?? null,
                $result['message'] ?? 'Level berhasil diperbarui'
            );
        } catch (ValidationException $e) {
            return $this->jsonValidationError($e);
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan saat memperbarui level');
        }
    }

    public function detailData($id)
    {
        $level = HakAksesModel::detailData($id);

        return view("sisfo::ManagePengguna/ManageLevel.detail", [
            'level' => $level,
            'title' => 'Detail Level'
        ]);
    }

    public function deleteData(Request $request, $id)
    {
        if ($request->isMethod('get')) {
            $level = HakAksesModel::detailData($id);

            return view("sisfo::ManagePengguna/ManageLevel.delete", [
                'level' => $level
            ]);
        }

        try {
            $result = HakAksesModel::deleteData($id);

            return $this->jsonSuccess(
                $result['data'] ?? null,
                $result['message'] ?? 'Level berhasil dihapus'
            );
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan saat menghapus level');
        }
    }
}