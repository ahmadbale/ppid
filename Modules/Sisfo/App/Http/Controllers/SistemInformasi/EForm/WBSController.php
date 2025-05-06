<?php

namespace Modules\Sisfo\App\Http\Controllers\SistemInformasi\EForm;

use Modules\Sisfo\App\Http\Controllers\TraitsController;
use Modules\Sisfo\App\Models\SistemInformasi\EForm\WBSModel;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Modules\Sisfo\App\Models\HakAkses\SetHakAksesModel;
use Modules\Sisfo\App\Models\Website\WebMenuModel;

class WBSController extends Controller
{
    use TraitsController;

    public $breadcrumb = 'Whistle Blowing System';
    public $pagename = 'SistemInformasi/EForm/WBS';

    public function index()
    {
        $folder = $this->getUserFolder();

        $breadcrumb = (object) [
            'title' => 'Whistle Blowing System',
            'list' => ['Home', 'Whistle Blowing System']
        ];

        $page = (object) [
            'title' => 'Whistle Blowing System'
        ];

        $activeMenu = 'WBS';

        return view("sisfo::SistemInformasi/EForm/$folder/WBS.index", [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu
        ]);
    }

    public function getData()
    {
        $timeline = WBSModel::getTimeline();
        $ketentuanPelaporan = WBSModel::getKetentuanPelaporan();

        return [
            'timeline' => $timeline,
            'ketentuanPelaporan' => $ketentuanPelaporan
        ];
    }

    public function addData()
    {
        $folder = $this->getUserFolder();

        $breadcrumb = (object) [
            'title' => 'Whistle Blowing System',
            'list' => ['Home', 'Whistle Blowing System', 'Tambah']
        ];

        $page = (object) [
            'title' => 'Pengajuan Whistle Blowing System'
        ];

        $activeMenu = 'WBS';

        return view("sisfo::SistemInformasi/EForm/$folder/WBS.pengisianForm", [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu,
        ]);
    }

    public function createData(Request $request)
    {
        try {
            $folder = $this->getUserFolder();
            WBSModel::validasiData($request);
            $result = WBSModel::createData($request);

            if ($result['success']) {
                return $this->redirectSuccess("/SistemInformasi/EForm/$folder/WBS", $result['message']);
            }

            return $this->redirectError($result['message']);
        } catch (ValidationException $e) {
            return $this->redirectValidationError($e);
        } catch (\Exception $e) {
            return $this->redirectException($e, 'Terjadi kesalahan saat mengajukan Whistle Blowing System');
        }
    }

    private function getUserFolder()
    {
        $user = Auth::user();
        $hakAksesKode = $user->level->hak_akses_kode;
        $levelKode = $user->level->level_kode;

        // Super Admin always has access
        if ($hakAksesKode === 'SAR') {
            return 'ADM'; // Return ADM folder for SAR users
        }

        // Allow users with proper permissions
        if ($levelKode === 'ADM' || $levelKode === 'RPN') {
            return $levelKode;
        }

        // Check if user has specific permission for this menu
        $menuUrl = WebMenuModel::getDynamicMenuUrl('permohonan-informasi-admin');
        if (SetHakAksesModel::cekHakAkses($user->user_id, $menuUrl, 'view')) {
            return 'ADM'; // Default to ADM folder if they have permission
        }

        // No permission
        abort(403, 'Forbidden. Kamu tidak punya akses ke halaman ini');
    }
}
