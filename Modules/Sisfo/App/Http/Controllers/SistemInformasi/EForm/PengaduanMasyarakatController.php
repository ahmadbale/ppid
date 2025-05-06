<?php

namespace Modules\Sisfo\App\Http\Controllers\SistemInformasi\EForm;

use Modules\Sisfo\App\Http\Controllers\TraitsController;
use Modules\Sisfo\App\Models\SistemInformasi\EForm\PengaduanMasyarakatModel;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Modules\Sisfo\App\Models\HakAkses\SetHakAksesModel;
use Modules\Sisfo\App\Models\Website\WebMenuModel;

class PengaduanMasyarakatController extends Controller
{
    use TraitsController;

    public $breadcrumb = 'Pengaduan Masyarakat';
    public $pagename = 'SistemInformasi/EForm/PengaduanMasyarakat';

    public function index()
    {
        $folder = $this->getUserFolder();

        $breadcrumb = (object) [
            'title' => 'Pengaduan Masyarakat',
            'list' => ['Home', 'Pengaduan Masyarakat']
        ];

        $page = (object) [
            'title' => 'Pengajuan Pengaduan Masyarakat'
        ];

        $activeMenu = 'PengaduanMasyarakat';

        return view("sisfo::SistemInformasi/EForm/$folder/PengaduanMasyarakat.index", [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu
        ]);
    }

    public function getData()
    {
        $timeline = PengaduanMasyarakatModel::getTimeline();
        $ketentuanPelaporan = PengaduanMasyarakatModel::getKetentuanPelaporan();

        return [
            'timeline' => $timeline,
            'ketentuanPelaporan' => $ketentuanPelaporan
        ];
    }

    public function addData()
    {
        $folder = $this->getUserFolder();

        $breadcrumb = (object) [
            'title' => 'Pengaduan Masyarakat',
            'list' => ['Home', 'Pengaduan Masyarakat', 'Tambah']
        ];

        $page = (object) [
            'title' => 'Pengajuan Pengaduan Masyarakat'
        ];

        $activeMenu = 'PengaduanMasyarakat';

        return view("sisfo::SistemInformasi/EForm/$folder/PengaduanMasyarakat.pengisianForm", [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu,
        ]);
    }

    public function createData(Request $request)
    {
        try {
            $folder = $this->getUserFolder();
            PengaduanMasyarakatModel::validasiData($request);
            $result = PengaduanMasyarakatModel::createData($request);

        //     if ($result['success']) {
        //         return $this->redirectSuccess("/SistemInformasi/EForm/$folder/PengaduanMasyarakat", $result['message']);
        //      }
            if ($request->ajax()) { // << cek kalau request dari AJAX
                if ($result['success']) {
                    return response()->json([
                        'success' => true,
                        'message' => $result['message']
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => $result['message']
                    ]);
                }
            }

            return $this->redirectError($result['message']);
        } catch (ValidationException $e) {
            if ($request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengajukan permohonan',
                'errors' => $e->errors()
            ]);
        }
            return $this->redirectValidationError($e);
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat memproses permohonan'
                ]);
            }
            return $this->redirectException($e, 'Terjadi kesalahan saat memproses permohonan');
        }
    }

    // public function createData(Request $request)
    // {
    //     try {
    //         $folder = $this->getUserFolder();
    //         PengaduanMasyarakatModel::validasiData($request);
    //         $result = PengaduanMasyarakatModel::createData($request);

    //     if ($result['success']) {
    //            return $this->redirectSuccess("/SistemInformasi/EForm/$folder/PengaduanMasyarakat", $result['message']);
    //     }

    //         return $this->redirectError($result['message']);
    //     } catch (ValidationException $e) {
    //         return $this->redirectValidationError($e);
    //     } catch (\Exception $e) {
    //         return $this->redirectException($e, 'Terjadi kesalahan saat mengajukan pengaduan');
    //     }
    // }

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
