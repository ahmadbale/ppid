<?php

namespace Modules\Sisfo\App\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Sisfo\App\Models\HakAksesModel;
use Modules\Sisfo\App\Models\SetUserHakAksesModel;

class SwitchRoleController extends Controller
{
    use TraitsController;

    public function index($hakAksesId)
    {
        // Cek apakah user memiliki hak akses tersebut
        $hakAkses = SetUserHakAksesModel::where('fk_m_user', Auth::id())
            ->where('fk_m_hak_akses', $hakAksesId)
            ->where('isDeleted', 0)
            ->first();
            
        if (!$hakAkses) {
            return $this->redirectError('Anda tidak memiliki hak akses tersebut');
        }
        
        // Set hak akses aktif ke session
        session(['active_hak_akses_id' => $hakAksesId]);
        
        // Ambil kode hak akses untuk redirect ke dashboard
        $hakAksesInfo = HakAksesModel::find($hakAksesId);
        $levelCode = $hakAksesInfo->hak_akses_kode;
        
        // Tambahkan flash message untuk notifikasi sukses
        session()->flash('success', 'Berhasil beralih ke level ' . $hakAksesInfo->hak_akses_nama);
        
        // Tentukan URL redirect berdasarkan level menggunakan logika yang sama
        $redirectUrl = $this->getDashboardUrl($levelCode);
        
        return redirect($redirectUrl);
    }

    /**
     * Menentukan URL dashboard berdasarkan kode level
     */
    private function getDashboardUrl($levelCode)
    {
        // Daftar level yang memiliki dashboard khusus
        $specialDashboards = ['SAR', 'ADM', 'MPU', 'VFR', 'RPN'];

        if (in_array($levelCode, $specialDashboards)) {
            return url('/dashboard' . $levelCode);
        }

        // Jika bukan level khusus, arahkan ke dashboard default
        return url('/dashboard');
    }
}