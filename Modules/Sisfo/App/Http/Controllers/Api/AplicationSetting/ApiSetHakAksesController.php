<?php

namespace Modules\Sisfo\App\Http\Controllers\Api\AplicationSetting;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Modules\Sisfo\App\Models\UserModel;
use Illuminate\Support\Facades\Validator;
use Modules\Sisfo\App\Models\HakAksesModel;
use Modules\Sisfo\App\Models\SetUserHakAksesModel;
use Modules\Sisfo\App\Models\HakAkses\SetHakAksesModel;

class ApiSetHakAksesController extends Controller
{
    /**
     * Mendapatkan daftar hak akses berdasarkan level
     * @param string $hakAksesKode Kode hak akses
     * @return \Illuminate\Http\JsonResponse
     */
    public function getHakAksesByLevel($hakAksesKode)
    {
        try {
            // Verifikasi bahwa level dengan kode ini ada
            $level = HakAksesModel::where('hak_akses_kode', $hakAksesKode)
                ->where('isDeleted', 0)
                ->first();
                
            if (!$level) {
                return response()->json([
                    'success' => false,
                    'message' => 'Level dengan kode ' . $hakAksesKode . ' tidak ditemukan',
                ], 404);
            }

            // Log parameter untuk debugging
            Log::info('Mendapatkan hak akses untuk level: ' . $hakAksesKode);
            
            $hakAksesData = SetHakAksesModel::getHakAksesData($hakAksesKode);
            
            // Log hasil untuk debugging
            Log::info('Hasil query getHakAksesData: ', ['count' => count($hakAksesData)]);
            
            return response()->json([
                'success' => true,
                'message' => 'Berhasil mengambil data hak akses',
                'data' => $hakAksesData
            ]);
        } catch (\Exception $e) {
            Log::error('Error getHakAksesByLevel: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data hak akses',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mendapatkan hak akses spesifik untuk user dan menu tertentu
     * @param int $userId ID Pengguna
     * @param int $menuId ID Menu
     * @return \Illuminate\Http\JsonResponse
     */
    public function getHakAksesDetail($userId, $menuId)
    {
        try {
            $hakAksesDetail = SetHakAksesModel::getHakAksesData($userId, $menuId);
            
            return response()->json([
                'success' => true,
                'message' => 'Berhasil mengambil detail hak akses',
                'data' => $hakAksesDetail
            ]);
        } catch (\Exception $e) {
            Log::error('Error getHakAksesDetail: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail hak akses',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cek hak akses menu untuk user
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function cekHakAksesMenu(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:m_user,user_id',
            'menu_url' => 'required|string',
            'active_hak_akses_id' => 'nullable|exists:m_hak_akses,hak_akses_id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            // Jika active_hak_akses_id dikirim, simpan ke session sementara
            if ($request->has('active_hak_akses_id')) {
                session(['active_hak_akses_id' => $request->active_hak_akses_id]);
            }
            
            $hasAccess = SetHakAksesModel::cekHakAksesMenu(
                $request->user_id, 
                $request->menu_url
            );
            
            // Bersihkan session setelah penggunaan
            if ($request->has('active_hak_akses_id')) {
                session()->forget('active_hak_akses_id');
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Berhasil memeriksa hak akses menu',
                'has_access' => $hasAccess
            ]);
        } catch (\Exception $e) {
            Log::error('Error cekHakAksesMenu: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memeriksa hak akses menu',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    /**
 * Mendapatkan semua hak akses untuk user tertentu
 * @param int $userId ID Pengguna
 * @return \Illuminate\Http\JsonResponse
 */
public function getUserHakAkses($userId)
{
    try {
        // Verifikasi pengguna ada
        $user = UserModel::find($userId);
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Pengguna tidak ditemukan',
            ], 404);
        }
        
        // Ambil semua data hak akses pengguna
        $hakAksesList = DB::table('set_hak_akses as sha')
            ->join('web_menu as wm', 'sha.fk_web_menu', '=', 'wm.web_menu_id')
            ->leftJoin('web_menu_global as wmg', 'wm.fk_web_menu_global', '=', 'wmg.web_menu_global_id')
            ->leftJoin('web_menu_url as wmu', 'wmg.fk_web_menu_url', '=', 'wmu.web_menu_url_id')
            ->leftJoin('web_menu as wmparent', 'wm.wm_parent_id', '=', 'wmparent.web_menu_id')
            ->leftJoin('web_menu_global as wmgparent', 'wmparent.fk_web_menu_global', '=', 'wmgparent.web_menu_global_id')
            ->where('sha.ha_pengakses', $userId)
            ->where('sha.isDeleted', 0)
            ->where('wm.isDeleted', 0)
            ->select([
                'wm.web_menu_id',
                'wm.wm_menu_nama as menu_nama',
                'wmg.wmg_nama_default as menu_default_nama',
                'wmu.wmu_nama as menu_url',
                'wmparent.wm_menu_nama as parent_menu_nama',
                'wmgparent.wmg_nama_default as parent_menu_default_nama',
                'sha.ha_menu',
                'sha.ha_view',
                'sha.ha_create',
                'sha.ha_update',
                'sha.ha_delete'
            ])
            ->get()
            ->map(function($item) {
                return [
                    'menu_id' => $item->web_menu_id,
                    'menu_nama' => $item->menu_nama ?: $item->menu_default_nama,
                    'menu_url' => $item->menu_url,
                    'parent_menu' => $item->parent_menu_nama ?: $item->parent_menu_default_nama,
                    'hak_akses' => [
                        'menu' => (bool)$item->ha_menu,
                        'view' => (bool)$item->ha_view,
                        'create' => (bool)$item->ha_create,
                        'update' => (bool)$item->ha_update,
                        'delete' => (bool)$item->ha_delete
                    ]
                ];
            });
        
        return response()->json([
            'success' => true,
            'message' => 'Berhasil mengambil data hak akses pengguna',
            'data' => [
                'user_id' => $user->user_id,
                'nama_pengguna' => $user->nama_pengguna,
                'hak_akses' => $hakAksesList
            ]
        ]);
    } catch (\Exception $e) {
        Log::error('Error getUserHakAkses: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Gagal mengambil data hak akses pengguna',
            'error' => $e->getMessage()
        ], 500);
    }
}

    /**
     * Cek hak akses spesifik untuk user
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function cekHakAkses(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:m_user,user_id',
            'menu_url' => 'required|string',
            'hak' => 'required|in:menu,view,create,update,delete',
            'active_hak_akses_id' => 'nullable|exists:m_hak_akses,hak_akses_id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            // Jika active_hak_akses_id dikirim, simpan ke session sementara
            if ($request->has('active_hak_akses_id')) {
                session(['active_hak_akses_id' => $request->active_hak_akses_id]);
            }
            
            $hasAccess = SetHakAksesModel::cekHakAkses(
                $request->user_id, 
                $request->menu_url, 
                $request->hak
            );
            
            // Bersihkan session setelah penggunaan
            if ($request->has('active_hak_akses_id')) {
                session()->forget('active_hak_akses_id');
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Berhasil memeriksa hak akses',
                'has_access' => $hasAccess
            ]);
        } catch (\Exception $e) {
            Log::error('Error cekHakAkses: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memeriksa hak akses',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Memperbarui hak akses level atau individual
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateHakAkses(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'is_level' => 'required|boolean',
            // Tambahkan validasi lain sesuai kebutuhan
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $result = SetHakAksesModel::updateData(
                $request->except('is_level'), 
                $request->is_level
            );
            
            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('Error updateHakAkses: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui hak akses',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Membuat hak akses baru untuk user
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createHakAkses(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:m_user,user_id',
            'hak_akses_id' => 'required|exists:m_hak_akses,hak_akses_id',
            'menu_akses' => 'nullable|array',
            'menu_akses.*.menu_id' => 'exists:web_menu,web_menu_id',
            'menu_akses.*.ha_menu' => 'boolean',
            'menu_akses.*.ha_view' => 'boolean',
            'menu_akses.*.ha_create' => 'boolean',
            'menu_akses.*.ha_update' => 'boolean',
            'menu_akses.*.ha_delete' => 'boolean'
        ]);

        // Jika validasi gagal
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            // Mulai transaksi database
            DB::beginTransaction();

            // Buat entri set_user_hak_akses
            $userHakAkses = SetUserHakAksesModel::createData(
                $request->user_id, 
                $request->hak_akses_id
            );

            // Jika ada menu akses yang ingin ditambahkan
            if ($request->has('menu_akses')) {
                foreach ($request->menu_akses as $menuAkses) {
                    // Buat atau update hak akses untuk setiap menu
                    SetHakAksesModel::updateOrCreate(
                        [
                            'ha_pengakses' => $request->user_id,
                            'fk_web_menu' => $menuAkses['menu_id']
                        ],
                        [
                            'ha_menu' => $menuAkses['ha_menu'] ?? 0,
                            'ha_view' => $menuAkses['ha_view'] ?? 0,
                            'ha_create' => $menuAkses['ha_create'] ?? 0,
                            'ha_update' => $menuAkses['ha_update'] ?? 0,
                            'ha_delete' => $menuAkses['ha_delete'] ?? 0
                        ]
                    );
                }
            }

            // Commit transaksi
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Hak akses berhasil dibuat',
                'data' => $userHakAkses
            ], 201);

        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi kesalahan
            DB::rollBack();

            Log::error('Error createHakAkses: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat hak akses',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Membuat hak akses untuk level tertentu
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createHakAksesLevel(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'hak_akses_kode' => 'required|exists:m_hak_akses,hak_akses_kode',
            'menu_akses' => 'required|array',
            'menu_akses.*.menu_id' => 'exists:web_menu,web_menu_id',
            'menu_akses.*.ha_menu' => 'boolean',
            'menu_akses.*.ha_view' => 'boolean',
            'menu_akses.*.ha_create' => 'boolean',
            'menu_akses.*.ha_update' => 'boolean',
            'menu_akses.*.ha_delete' => 'boolean'
        ]);

        // Jika validasi gagal
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            // Dapatkan level berdasarkan kode
            $level = HakAksesModel::where('hak_akses_kode', $request->hak_akses_kode)
                ->where('isDeleted', 0)
                ->first();
                
            if (!$level) {
                return response()->json([
                    'success' => false,
                    'message' => 'Level dengan kode ' . $request->hak_akses_kode . ' tidak ditemukan',
                ], 404);
            }

            // Ambil semua user dengan level ini
            $userIds = SetUserHakAksesModel::getUsersByHakAkses($level->hak_akses_id)
                ->pluck('User.user_id')
                ->filter(); // Hapus null values

            if ($userIds->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada pengguna dengan level ' . $level->hak_akses_nama,
                ], 400);
            }

            // Mulai transaksi
            DB::beginTransaction();

            // Proses setiap menu akses
            foreach ($request->menu_akses as $menuAkses) {
                // Cek apakah menu ada
                if (!isset($menuAkses['menu_id'])) {
                    continue;
                }
                
                // Untuk setiap user dengan level ini
                foreach ($userIds as $userId) {
                    // Buat atau update hak akses
                    SetHakAksesModel::updateOrCreate(
                        [
                            'ha_pengakses' => $userId,
                            'fk_web_menu' => $menuAkses['menu_id']
                        ],
                        [
                            'ha_menu' => $menuAkses['ha_menu'] ?? 0,
                            'ha_view' => $menuAkses['ha_view'] ?? 0,
                            'ha_create' => $menuAkses['ha_create'] ?? 0,
                            'ha_update' => $menuAkses['ha_update'] ?? 0,
                            'ha_delete' => $menuAkses['ha_delete'] ?? 0
                        ]
                    );
                }
            }

            // Commit transaksi
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Hak akses level berhasil dibuat',
                'total_users_affected' => $userIds->count()
            ], 201);

        } catch (\Exception $e) {
            // Rollback transaksi
            DB::rollBack();

            Log::error('Error createHakAksesLevel: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat hak akses level',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mendapatkan struktur menu berdasarkan level
     * @param string $hakAksesKode Kode hak akses
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMenusByLevel($hakAksesKode)
    {
        try {
            // Verifikasi bahwa level dengan kode ini ada
            $level = HakAksesModel::where('hak_akses_kode', $hakAksesKode)
                ->where('isDeleted', 0)
                ->first();
                
            if (!$level) {
                return response()->json([
                    'success' => false,
                    'message' => 'Level dengan kode ' . $hakAksesKode . ' tidak ditemukan',
                ], 404);
            }
            
            $menuStructure = SetHakAksesModel::getMenusByJenisMenu($hakAksesKode);
            
            return response()->json([
                'success' => true,
                'message' => 'Berhasil mengambil struktur menu',
                'data' => $menuStructure
            ]);
        } catch (\Exception $e) {
            Log::error('Error getMenusByLevel: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil struktur menu',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}