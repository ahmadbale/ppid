<?php

namespace Modules\Sisfo\App\Models\Website;

use Modules\Sisfo\App\Models\HakAkses\SetHakAksesModel;
use Modules\Sisfo\App\Models\HakAksesModel;
use Modules\Sisfo\App\Models\Log\NotifAdminModel;
use Modules\Sisfo\App\Models\Log\NotifVerifikatorModel;
use Modules\Sisfo\App\Models\Log\TransactionModel;
use Modules\Sisfo\App\Models\TraitsModel;
use Modules\Sisfo\App\Models\UserModel;
use Modules\Sisfo\App\Models\WebMenuGlobalModel;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Sisfo\App\Models\Website\WebKontenModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class WebMenuModel extends Model
{
    use TraitsModel;

    protected $table = 'web_menu';
    protected $primaryKey = 'web_menu_id';

    protected $fillable = [
        'fk_web_menu_global',
        'fk_m_hak_akses',
        'wm_parent_id',
        'wm_urutan_menu',
        'wm_menu_nama',
        'wm_status_menu'
    ];

    // Relationships
    public function parentMenu()
    {
        return $this->belongsTo(WebMenuModel::class, 'wm_parent_id', 'web_menu_id');
    }

    public function WebMenuGlobal()
    {
        return $this->belongsTo(WebMenuGlobalModel::class, 'fk_web_menu_global', 'web_menu_global_id');
    }

    public function Level()
    {
        return $this->belongsTo(HakAksesModel::class, 'fk_m_hak_akses', 'hak_akses_id');
    }

    public function WebMenuUrl()
    {
        return $this->hasOneThrough(
            WebMenuUrlModel::class,
            WebMenuGlobalModel::class,
            'web_menu_global_id', // Kunci asing pada WebMenuGlobal
            'web_menu_url_id',    // Kunci utama pada WebMenuUrl
            'fk_web_menu_global', // Kunci untuk menghubungkan WebMenu dengan WebMenuGlobal
            'fk_web_menu_url'     // Kunci untuk menghubungkan WebMenuGlobal dengan WebMenuUrl
        );
    }

    public function children()
    {
        return $this->hasMany(WebMenuModel::class, 'wm_parent_id', 'web_menu_id')
            ->orderBy('wm_urutan_menu');
    }

    public function konten()
    {
        return $this->hasOne(WebKontenModel::class, 'fk_web_menu', 'web_menu_id');
    }

    public function hakAkses()
    {
        return $this->hasMany(SetHakAksesModel::class, 'fk_web_menu', 'web_menu_id');
    }

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable = array_merge($this->fillable, $this->getCommonFields());
    }

    public static function getJenisMenuList()
    {
        // Dapatkan daftar jenis menu dari tabel level
        $levels = HakAksesModel::where('isDeleted', 0)->get();
        $jenisMenuList = [];

        foreach ($levels as $level) {
            $jenisMenuList[$level->hak_akses_kode] = $level->hak_akses_nama;
        }

        return $jenisMenuList;
    }

    public static function getDataMenu()
    {
        // Ambil data menu berdasarkan level 'RPN'
        $levelRPN = DB::table('m_hak_akses')->where('hak_akses_kode', 'RPN')->first();

        if (!$levelRPN) {
            return [];
        }

        $arr_data = self::query()
            ->select('web_menu.*')
            ->where('fk_m_hak_akses', $levelRPN->hak_akses_id)
            ->where('wm_status_menu', 'aktif')
            ->where('isDeleted', 0)
            ->orderBy('wm_urutan_menu')
            ->get()
            ->map(function ($menu) {
                // Ambil data menu global terkait
                $menuGlobal = DB::table('web_menu_global')
                    ->where('web_menu_global_id', $menu->fk_web_menu_global)
                    ->where('isDeleted', 0)
                    ->first();

                // Tentukan nama menu - prioritaskan wm_menu_nama, jika null gunakan nama dari menu global
                $menuName = $menu->wm_menu_nama;
                if ($menuName === null && $menuGlobal) {
                    $menuName = $menuGlobal->wmg_nama_default;
                }

                // Ambil data URL dari web_menu_url melalui web_menu_global
                $menuUrl = null;
                if ($menuGlobal && $menuGlobal->fk_web_menu_url) {
                    $menuUrlRecord = DB::table('web_menu_url')
                        ->where('web_menu_url_id', $menuGlobal->fk_web_menu_url)
                        ->where('isDeleted', 0)
                        ->first();

                    if ($menuUrlRecord) {
                        $menuUrl = $menuUrlRecord->wmu_nama;
                    }
                }

                // Ambil submenu (anak menu)
                $submenuItems = self::query()
                    ->where('wm_parent_id', $menu->web_menu_id)
                    ->where('wm_status_menu', 'aktif')
                    ->where('isDeleted', 0)
                    ->orderBy('wm_urutan_menu')
                    ->get();

                $submenu = [];
                foreach ($submenuItems as $submenuItem) {
                    // Ambil data menu global untuk submenu
                    $submenuGlobal = DB::table('web_menu_global')
                        ->where('web_menu_global_id', $submenuItem->fk_web_menu_global)
                        ->where('isDeleted', 0)
                        ->first();

                    // Tentukan nama submenu
                    $submenuName = $submenuItem->wm_menu_nama;
                    if ($submenuName === null && $submenuGlobal) {
                        $submenuName = $submenuGlobal->wmg_nama_default;
                    }

                    // Ambil URL submenu
                    $submenuUrl = null;
                    if ($submenuGlobal && $submenuGlobal->fk_web_menu_url) {
                        $submenuUrlRecord = DB::table('web_menu_url')
                            ->where('web_menu_url_id', $submenuGlobal->fk_web_menu_url)
                            ->where('isDeleted', 0)
                            ->first();

                        if ($submenuUrlRecord) {
                            $submenuUrl = $submenuUrlRecord->wmu_nama;
                        }
                    }

                    $submenu[] = [
                        'wm_menu_nama' => $submenuName,
                        'wm_menu_url' => $submenuUrl
                    ];
                }

                return [
                    'id' => $menu->web_menu_id,
                    'wm_parent_id' => $menu->wm_parent_id,
                    'wm_urutan_menu' => $menu->wm_urutan_menu,
                    'wm_menu_nama' => $menuName, // Sekarang diisi dengan nilai yang benar
                    'wm_menu_url' => $menuUrl,   // Sekarang diisi dengan URL yang benar
                    'children' => $submenu
                ];
            })->toArray();

        return $arr_data;
    }


    public static function selectBeritaPengumuman()
    {
        $arr_data = self::query()
            ->select([
                'web_menu_id',
                'wm_parent_id',
                'fk_web_menu_global',
                'wm_menu_nama',
                'wm_urutan_menu'
            ])
            ->where('wm_status_menu', 'aktif')
            ->where('isDeleted', 0)
            ->whereIn('wm_menu_nama', ['Berita', 'Pengumuman'])
            ->orderBy('wm_urutan_menu')
            ->get()
            ->map(function ($menu) {
                // Dapatkan nama menu dari web_menu_global jika wm_menu_nama kosong
                $menuName = $menu->wm_menu_nama ?: ($menu->WebMenuGlobal ? $menu->WebMenuGlobal->wmg_nama_default : null);
                // Dapatkan URL dari relasi
                $menuUrl = $menu->WebMenuUrl ? $menu->WebMenuUrl->wmu_nama : null;

                return [
                    'id' => $menu->web_menu_id,
                    'wm_parent_id' => $menu->wm_parent_id,
                    'wm_menu_url' => $menuUrl,
                    'wm_menu_nama' => $menuName,
                    'wm_urutan_menu' => $menu->wm_urutan_menu
                ];
            })->toArray();
        return $arr_data;
    }

    public static function mengecekKetersediaanMenu($menuName, $hakAksesId, $excludeId = null)
    {
        $query = self::where('fk_m_hak_akses', $hakAksesId)
            ->where('wm_menu_nama', $menuName)
            ->where('isDeleted', 0);

        if ($excludeId) {
            $query->where('web_menu_id', '!=', $excludeId);
        }

        $menuAktif = clone $query;
        $menuAktif = $menuAktif->where('wm_status_menu', 'aktif')->first();

        if ($menuAktif) {
            return [
                'exists' => true,
                'message' => 'Menu sudah ada dan berstatus aktif untuk level ini'
            ];
        }

        $menuNonaktif = clone $query;
        $menuNonaktif = $menuNonaktif->where('wm_status_menu', 'nonaktif')->first();

        if ($menuNonaktif) {
            return [
                'exists' => true,
                'message' => 'Menu sudah ada, tetapi saat ini berstatus nonaktif untuk level ini, silakan aktifkan menu ini'
            ];
        }

        return [
            'exists' => false
        ];
    }

    // Tambahkan di bagian createData pada WebMenuModel.php
    public static function createData($request)
    {
        // Cek apakah request berisi multiple menu
        if ($request->has('menus')) {
            $createdMenuIds = [];
            $hasError = false;
            $errorMessage = '';

            DB::beginTransaction();
            try {
                // Proses setiap entri menu
                foreach ($request->menus as $index => $menuData) {
                    // Siapkan data menu untuk model
                    $menuRequest = new \Illuminate\Http\Request();

                    // Gunakan method merge() untuk menambahkan data ke request
                    $menuRequest->merge([
                        'web_menu' => $menuData,
                        'kategori_menu' => $menuData['kategori_menu']
                    ]);

                    // Gunakan implementasi createData yang sudah ada untuk single menu
                    $result = self::createSingleMenu($menuRequest);

                    if ($result['success']) {
                        $createdMenuIds[] = $result['data']->web_menu_id;

                        // Tambahkan pengaturan hak akses untuk menu yang baru dibuat
                        self::setHakAksesForNewMenu($result['data']->web_menu_id, $menuData);
                    } else {
                        $hasError = true;
                        $errorMessage = 'Error membuat menu #' . ($index + 1) . ': ' . $result['message'];
                        break;
                    }
                }

                if ($hasError) {
                    DB::rollBack();
                    return [
                        'success' => false,
                        'message' => $errorMessage
                    ];
                } else {
                    DB::commit();
                    return [
                        'success' => true,
                        'message' => count($createdMenuIds) . ' menu berhasil dibuat',
                        'data' => [
                            'ids' => $createdMenuIds
                        ]
                    ];
                }
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error creating multiple menus: ' . $e->getMessage());
                return [
                    'success' => false,
                    'message' => 'Error saat membuat menu: ' . $e->getMessage()
                ];
            }
        }
        // Jika bukan multiple menu, gunakan implementasi yang sudah ada
        else {
            return self::createSingleMenu($request);
        }
    }

    private static function setHakAksesForNewMenu($menuId, $menuData)
    {
        // Ambil level berdasarkan menu
        $hakAksesId = $menuData['fk_m_hak_akses'] ?? null;

        if (!$hakAksesId) {
            return;
        }

        // Ambil semua user dengan level tersebut
        $userIds = DB::table('set_user_hak_akses')
            ->where('fk_m_hak_akses', $hakAksesId)
            ->where('isDeleted', 0)
            ->pluck('fk_m_user');

        if ($userIds->isEmpty()) {
            return;
        }

        $hakAksesValues = [
            'ha_menu' => 0,
            'ha_view' => 0,
            'ha_create' => 0,
            'ha_update' => 0,
            'ha_delete' => 0
        ];

        // Jika ada pengaturan hak akses yang dikirim dari form
        if (isset($menuData['hak_akses'])) {
            $hakAksesValues = [
                'ha_menu' => isset($menuData['hak_akses']['menu']) ? 1 : 0,
                'ha_view' => isset($menuData['hak_akses']['view']) ? 1 : 0,
                'ha_create' => isset($menuData['hak_akses']['create']) ? 1 : 0,
                'ha_update' => isset($menuData['hak_akses']['update']) ? 1 : 0,
                'ha_delete' => isset($menuData['hak_akses']['delete']) ? 1 : 0
            ];
        }

        // Buat hak akses untuk setiap user dengan level tersebut
        foreach ($userIds as $userId) {
            $hakAkses = SetHakAksesModel::firstOrNew([
                'ha_pengakses' => $userId,
                'fk_web_menu' => $menuId
            ]);

            $hakAkses->ha_menu = $hakAksesValues['ha_menu'];
            $hakAkses->ha_view = $hakAksesValues['ha_view'];
            $hakAkses->ha_create = $hakAksesValues['ha_create'];
            $hakAkses->ha_update = $hakAksesValues['ha_update'];
            $hakAkses->ha_delete = $hakAksesValues['ha_delete'];

            // Set created_by jika record baru
            if (!$hakAkses->exists) {
                $hakAkses->created_by = Auth::check() ? Auth::user()->nama_pengguna : 'system';
            } else {
                $hakAkses->updated_by = Auth::check() ? Auth::user()->nama_pengguna : 'system';
            }

            $hakAkses->save();
        }
    }

    // Method baru untuk memproses single menu (dipecah dari createData asli)
    private static function createSingleMenu($request)
    {
        DB::beginTransaction();
        try {
            self::validasiData($request);
            $data = $request->web_menu;
            $kategoriMenu = $request->kategori_menu; // Ambil kategori menu dari form

            // Validasi apakah user mencoba membuat menu dengan level SAR
            $hakAksesId = $data['fk_m_hak_akses'] ?? null;
            $level = HakAksesModel::find($hakAksesId);

            // Jika level adalah SAR dan user bukan SAR, tolak permintaan
            if ($level && $level->hak_akses_kode === 'SAR' && Auth::user()->level->hak_akses_kode !== 'SAR') {
                return [
                    'success' => false,
                    'message' => 'Hanya pengguna dengan level Super Administrator yang dapat menambahkan menu SAR'
                ];
            }

            $parentId = null;
            $webMenuGlobalId = null;

            if ($kategoriMenu === 'sub_menu') {
                $parentId = isset($data['wm_parent_id']) ? $data['wm_parent_id'] : null;
                $webMenuGlobalId = isset($data['fk_web_menu_global']) ? $data['fk_web_menu_global'] : null;
            } else if ($kategoriMenu === 'group_menu') {
                $parentId = null;
                $webMenuGlobalId = isset($data['wm_parent_id']) ? $data['wm_parent_id'] : null;
            } else {
                $parentId = null;
                $webMenuGlobalId = isset($data['fk_web_menu_global']) ? $data['fk_web_menu_global'] : null;
            }

            // Buat menu baru
            $saveData = self::create([
                'fk_web_menu_global' => $webMenuGlobalId,
                'fk_m_hak_akses' => $data['fk_m_hak_akses'],
                'wm_parent_id' => $parentId, // Set nilai wm_parent_id
                'wm_menu_nama' => $data['wm_menu_nama'] ? $data['wm_menu_nama'] : null, // Alias (opsional)
                'wm_status_menu' => $data['wm_status_menu'],
                'wm_urutan_menu' => self::where('wm_parent_id', $parentId)
                    ->where('isDeleted', 0)
                    ->count() + 1
            ]);

            // Tambahkan pengaturan hak akses hanya jika bukan group menu
            if ($saveData && $kategoriMenu !== 'group_menu') {
                // Tambahkan pengaturan hak akses untuk menu yang baru dibuat
                self::setHakAksesForNewMenu($saveData->web_menu_id, $data);
            }

            TransactionModel::createData(
                'CREATED',
                $saveData->web_menu_id,
                $saveData->wm_menu_nama ?: ($saveData->WebMenuGlobal ? $saveData->WebMenuGlobal->wmg_nama_default : 'Menu Baru')
            );

            DB::commit();
            return [
                'success' => true,
                'message' => 'Menu berhasil dibuat',
                'data' => $saveData
            ];
        } catch (ValidationException $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->validator->errors()
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat membuat menu: ' . $e->getMessage()
            ];
        }
    }

    public static function updateData($request, $id)
    {
        DB::beginTransaction();
        try {
            // Ambil data menu yang akan diupdate
            $saveData = self::findOrFail($id);

            // Tentukan kategori menu saat ini
            $currentKategoriMenu = 'menu_biasa';
            if ($saveData->wm_parent_id) {
                $currentKategoriMenu = 'sub_menu';
            } elseif ($saveData->WebMenuGlobal && is_null($saveData->WebMenuGlobal->fk_web_menu_url)) {
                $currentKategoriMenu = 'group_menu';
            }

            // Jika kategori_menu tidak ada di request dan ini adalah group menu, set otomatis
            if (!$request->has('kategori_menu') || empty($request->kategori_menu)) {
                if ($currentKategoriMenu === 'group_menu') {
                    $request->merge(['kategori_menu' => 'group_menu']);
                }
            }

            // Validasi data setelah penyesuaian kategori_menu
            self::validasiData($request);

            $data = $request->web_menu;
            $kategoriMenu = $request->kategori_menu ?? $currentKategoriMenu;

            // Periksa level menu
            $level = HakAksesModel::find($saveData->fk_m_hak_akses);
            if ($level && $level->hak_akses_kode === 'SAR' && Auth::user()->level->hak_akses_kode !== 'SAR') {
                return [
                    'success' => false,
                    'message' => 'Hanya pengguna dengan level Super Administrator yang dapat mengubah menu SAR'
                ];
            }

            // Validasi perubahan kategori menu sesuai aturan bisnis
            if ($currentKategoriMenu === 'group_menu' && $kategoriMenu !== 'group_menu') {
                return [
                    'success' => false,
                    'message' => 'Group menu tidak dapat diubah ke kategori lain'
                ];
            }

            // Menu biasa dan sub menu tidak bisa diubah menjadi group menu
            if ($currentKategoriMenu !== 'group_menu' && $kategoriMenu === 'group_menu') {
                return [
                    'success' => false,
                    'message' => 'Menu tidak dapat diubah menjadi group menu'
                ];
            }

            // Tentukan nilai-nilai berdasarkan kategori menu
            $parentId = null;
            $updateData = [
                'fk_m_hak_akses' => $data['fk_m_hak_akses'],
                'wm_menu_nama' => isset($data['wm_menu_nama']) && $data['wm_menu_nama'] ? $data['wm_menu_nama'] : null,
                'wm_status_menu' => $data['wm_status_menu']
            ];

            if ($kategoriMenu === 'sub_menu') {
                $parentId = isset($data['wm_parent_id']) && $data['wm_parent_id'] !== '' ? $data['wm_parent_id'] : null;
                $updateData['wm_parent_id'] = $parentId;
                $updateData['fk_web_menu_global'] = $data['fk_web_menu_global'] ?? $saveData->fk_web_menu_global;

                if ($parentId) {
                    $parentMenu = self::find($parentId);
                    if ($parentMenu) {
                        $updateData['fk_m_hak_akses'] = $parentMenu->fk_m_hak_akses;
                    }
                }
            } else if ($kategoriMenu === 'group_menu') {
                $updateData['wm_parent_id'] = null;
            } else {
                $updateData['wm_parent_id'] = null;
                $updateData['fk_web_menu_global'] = $data['fk_web_menu_global'] ?? $saveData->fk_web_menu_global;
            }

            // Update menu dengan updateData yang sudah disiapkan
            $saveData->update($updateData);

            // Update permissions jika bukan group menu
            if ($kategoriMenu !== 'group_menu') {
                $userIds = DB::table('set_user_hak_akses')
                    ->where('fk_m_hak_akses', $saveData->fk_m_hak_akses)
                    ->where('isDeleted', 0)
                    ->pluck('fk_m_user');

                if ($userIds->isNotEmpty()) {
                    $permissions = $data['permissions'] ?? [];

                    $permissionValues = [
                        'ha_menu' => isset($permissions['menu']) ? 1 : 0,
                        'ha_view' => isset($permissions['view']) ? 1 : 0,
                        'ha_create' => isset($permissions['create']) ? 1 : 0,
                        'ha_update' => isset($permissions['update']) ? 1 : 0,
                        'ha_delete' => isset($permissions['delete']) ? 1 : 0,
                    ];

                    foreach ($userIds as $userId) {
                        $hakAkses = SetHakAksesModel::firstOrNew([
                            'ha_pengakses' => $userId,
                            'fk_web_menu' => $saveData->web_menu_id
                        ]);

                        $hakAkses->ha_menu = $permissionValues['ha_menu'];
                        $hakAkses->ha_view = $permissionValues['ha_view'];
                        $hakAkses->ha_create = $permissionValues['ha_create'];
                        $hakAkses->ha_update = $permissionValues['ha_update'];
                        $hakAkses->ha_delete = $permissionValues['ha_delete'];

                        if (!$hakAkses->exists) {
                            $hakAkses->created_by = Auth::check() ? Auth::user()->nama_pengguna : 'system';
                        } else {
                            $hakAkses->updated_by = Auth::check() ? Auth::user()->nama_pengguna : 'system';
                        }

                        $hakAkses->isDeleted = 0;
                        $hakAkses->save();
                    }
                }
            }

            TransactionModel::createData(
                'UPDATED',
                $saveData->web_menu_id,
                $saveData->wm_menu_nama ?: ($saveData->WebMenuGlobal ? $saveData->WebMenuGlobal->wmg_nama_default : 'Menu')
            );

            DB::commit();
            return [
                'success' => true,
                'message' => 'Menu berhasil diperbarui',
                'data' => $saveData
            ];
        } catch (ValidationException $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->validator->errors()
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui menu: ' . $e->getMessage()
            ];
        }
    }

    public static function deleteData($id)
    {
        try {
            DB::beginTransaction();

            // Dapatkan menu yang akan dihapus
            $menu = self::findOrFail($id);

            // Cek level pengguna yang sedang login
            $level = HakAksesModel::find($menu->fk_m_hak_akses);

            // Jika level SAR dan pengguna bukan SAR, tolak
            if ($level && $level->hak_akses_kode === 'SAR' && Auth::user()->level->hak_akses_kode !== 'SAR') {
                return [
                    'success' => false,
                    'message' => 'Hanya pengguna dengan level Super Administrator yang dapat menghapus menu SAR'
                ];
            }

            // Cek apakah menu ini memiliki sub menu yang aktif
            $hasActiveSubMenus = self::where('wm_parent_id', $id)
                ->where('wm_status_menu', 'aktif')
                ->where('isDeleted', 0)
                ->exists();

            if ($hasActiveSubMenus) {
                return [
                    'success' => false,
                    'message' => 'Menu utama ini tidak dapat dihapus dikarenakan terdapat sub menu yang masih aktif'
                ];
            }

            // Hapus menu
            $menu->isDeleted = 1;
            $menu->deleted_at = now();
            $menu->save();

            // Atur ulang urutan menu
            self::reorderAfterDelete($menu->wm_parent_id ?? null);

            // Catat transaksi
            TransactionModel::createData(
                'DELETED',
                $id,
                $menu->wm_menu_nama ?: ($menu->WebMenuGlobal ? $menu->WebMenuGlobal->wmg_nama_default : 'Menu')
            );

            DB::commit();

            return [
                'success' => true,
                'message' => 'Menu berhasil dihapus',
                'data' => $menu
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting menu: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus menu: ' . $e->getMessage()
            ];
        }
    }

    public static function validasiData($request)
    {
        $rules = [];
        $messages = [];

        // Jika ada multiple menu
        if ($request->has('menus')) {
            // Kode yang sudah ada untuk multiple menu
            $rules['menus'] = 'required|array';
            foreach ($request->menus as $index => $menu) {
                $rules["menus.{$index}.fk_m_hak_akses"] = 'required|exists:m_hak_akses,hak_akses_id';
                $rules["menus.{$index}.fk_web_menu_global"] = 'required|exists:web_menu_global,web_menu_global_id';
                $rules["menus.{$index}.wm_status_menu"] = 'required|in:aktif,nonaktif';
            }

            $messages = [
                'menus.required' => 'Data menu wajib diisi',
                'menus.array' => 'Format data menu tidak valid',
            ];
        } else {
            // Untuk single menu
            $kategoriMenu = $request->kategori_menu ?? '';

            // PERBAIKAN: Aturan dasar yang selalu berlaku
            $rules = [
                'web_menu.fk_m_hak_akses' => 'required|exists:m_hak_akses,hak_akses_id',
                'web_menu.wm_menu_nama' => 'nullable|string|max:60',
                'web_menu.wm_status_menu' => 'required|in:aktif,nonaktif',
            ];

            // PERBAIKAN: Kategori menu wajib diisi kecuali untuk update group menu yang sudah ada
            // Cek apakah ini adalah update request untuk group menu
            $isGroupMenuUpdate = false;
            if ($request->getMethod() === 'PUT' || $request->getMethod() === 'PATCH') {
                // Ambil route parameter untuk ID menu
                $routeParams = $request->route()->parameters();
                if (isset($routeParams['id'])) {
                    $menuId = $routeParams['id'];
                    $existingMenu = self::with('WebMenuGlobal')->find($menuId);

                    if (
                        $existingMenu &&
                        $existingMenu->WebMenuGlobal &&
                        is_null($existingMenu->WebMenuGlobal->fk_web_menu_url) &&
                        empty($kategoriMenu)
                    ) {
                        // Ini adalah group menu dan kategori_menu kosong, maka set sebagai group_menu
                        $kategoriMenu = 'group_menu';
                        $request->merge(['kategori_menu' => 'group_menu']);
                        $isGroupMenuUpdate = true;

                        Log::debug('Mendeteksi update group menu, set kategori_menu = group_menu');
                    }
                }
            }

            // Jika bukan update group menu otomatis, maka kategori_menu wajib diisi
            if (!$isGroupMenuUpdate) {
                $rules['kategori_menu'] = 'required|in:menu_biasa,group_menu,sub_menu';
            }

            // PERBAIKAN: Aturan khusus berdasarkan kategori menu
            if ($kategoriMenu === 'sub_menu') {
                $rules['web_menu.wm_parent_id'] = 'required';
                $rules['web_menu.fk_web_menu_global'] = 'required|exists:web_menu_global,web_menu_global_id';
            } else if ($kategoriMenu === 'menu_biasa') {
                // Menu biasa memerlukan fk_web_menu_global
                $rules['web_menu.fk_web_menu_global'] = 'required|exists:web_menu_global,web_menu_global_id';
            }
            // Group menu tidak memerlukan validasi fk_web_menu_global

            $messages = [
                'web_menu.fk_m_hak_akses.required' => 'Level menu wajib dipilih',
                'web_menu.fk_m_hak_akses.exists' => 'Level menu tidak valid',
                'web_menu.wm_menu_nama.max' => 'Alias menu maksimal 60 karakter',
                'web_menu.wm_status_menu.required' => 'Status menu wajib diisi',
                'web_menu.wm_status_menu.in' => 'Status menu harus aktif atau nonaktif',
                'kategori_menu.required' => 'Kategori menu wajib dipilih',
                'kategori_menu.in' => 'Kategori menu tidak valid',
            ];

            if ($kategoriMenu === 'sub_menu') {
                $messages['web_menu.wm_parent_id.required'] = 'Nama group menu wajib dipilih';
                $messages['web_menu.fk_web_menu_global.required'] = 'Nama menu wajib dipilih';
                $messages['web_menu.fk_web_menu_global.exists'] = 'Menu global tidak valid';
            } else if ($kategoriMenu === 'menu_biasa') {
                $messages['web_menu.fk_web_menu_global.required'] = 'Nama menu wajib dipilih';
                $messages['web_menu.fk_web_menu_global.exists'] = 'Menu global tidak valid';
            }
        }

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return true;
    }

    public static function getParentMenusByLevel($hakAksesId, $excludeId = null)
    {
        // Dapatkan menu-menu untuk level tertentu yang merupakan group menu
        $query = self::with(['WebMenuGlobal.WebMenuUrl'])
            ->where('fk_m_hak_akses', $hakAksesId)
            ->whereNull('wm_parent_id')
            ->where('isDeleted', 0)
            ->where('wm_status_menu', 'aktif')
            // Hanya ambil menu yang merupakan group menu (tidak memiliki web_menu_url)
            ->whereHas('WebMenuGlobal', function ($q) {
                $q->whereNull('fk_web_menu_url');
            });

        // Jika ada ID yang perlu dikecualikan (menu yang sedang diupdate)
        if ($excludeId) {
            $query->where('web_menu_id', '!=', $excludeId);
        }

        return $query->get()
            ->map(function ($menu) {
                // Tambahkan nama yang benar untuk ditampilkan
                $displayName = $menu->wm_menu_nama ?: ($menu->WebMenuGlobal ? $menu->WebMenuGlobal->wmg_nama_default : 'Unnamed Menu');

                // Tambahkan properti untuk tampilan
                $menu->display_name = $displayName;

                return $menu;
            });
    }

    public static function getMenusWithChildren()
    {
        return self::with(['children' => function ($query) {
            $query->orderBy('wm_urutan_menu');
        }])
            ->whereNull('wm_parent_id')
            ->where('isDeleted', 0)
            ->orderBy('wm_urutan_menu')
            ->get();
    }

    public static function getEditData($id)
    {
        try {
            // Dapatkan menu dengan relasi yang diperlukan
            $menu = self::with(['WebMenuGlobal.WebMenuUrl', 'Level', 'parentMenu'])->findOrFail($id);

            // Tentukan kategori menu
            $kategoriMenu = 'menu_biasa'; // Default

            // Jika menu memiliki parent, maka ini adalah sub menu
            if ($menu->wm_parent_id) {
                $kategoriMenu = 'sub_menu';
            }
            // Jika menu global tidak memiliki URL (fk_web_menu_url null), maka ini adalah group menu
            elseif ($menu->WebMenuGlobal && is_null($menu->WebMenuGlobal->fk_web_menu_url)) {
                $kategoriMenu = 'group_menu';
            }

            // Dapatkan permissions untuk menu ini
            $permissions = [
                'menu' => false,
                'view' => false,
                'create' => false,
                'update' => false,
                'delete' => false
            ];

            // Ambil permission dari set_hak_akses untuk user tertentu (bisa diperluas untuk semua user)
            // Untuk sementara, kita ambil permission dari user yang sedang login
            $userId = Auth::user()->user_id;
            $hakAkses = SetHakAksesModel::where('ha_pengakses', $userId)
                ->where('fk_web_menu', $id)
                ->where('isDeleted', 0)
                ->first();

            if ($hakAkses) {
                $permissions = [
                    'menu' => $hakAkses->ha_menu == 1,
                    'view' => $hakAkses->ha_view == 1,
                    'create' => $hakAkses->ha_create == 1,
                    'update' => $hakAkses->ha_update == 1,
                    'delete' => $hakAkses->ha_delete == 1
                ];
            }

            // Dapatkan parent menus untuk level tersebut
            $parentMenus = self::getParentMenusByLevel($menu->fk_m_hak_akses, $id);

            $result = [
                'success' => true,
                'menu' => [
                    'web_menu_id' => $menu->web_menu_id,
                    'wm_menu_nama' => $menu->wm_menu_nama,
                    'menu_global_name' => $menu->WebMenuGlobal ? $menu->WebMenuGlobal->wmg_nama_default : '',
                    'wm_status_menu' => $menu->wm_status_menu,
                    'wm_parent_id' => $menu->wm_parent_id,
                    'fk_web_menu_global' => $menu->fk_web_menu_global,
                    'fk_web_menu_url' => $menu->WebMenuGlobal ? $menu->WebMenuGlobal->fk_web_menu_url : null,
                    'fk_m_hak_akses' => $menu->fk_m_hak_akses,
                    'hak_akses_kode' => $menu->Level ? $menu->Level->hak_akses_kode : null,
                    'kategori_menu' => $kategoriMenu,
                    'permissions' => $permissions
                ],
                'parentMenus' => $parentMenus
            ];

            return $result;
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error mengambil data menu: ' . $e->getMessage()
            ];
        }
    }

    public static function getDetailData($id)
    {
        try {
            // Dapatkan menu dengan semua relasi yang diperlukan
            $menu = self::with([
                'WebMenuGlobal.WebMenuUrl',
                'parentMenu',
                'Level'
            ])->findOrFail($id);

            $result = [
                'success' => true,
                'menu' => [
                    'wm_menu_nama' => $menu->wm_menu_nama ?: ($menu->WebMenuGlobal ? $menu->WebMenuGlobal->wmg_nama_default : ''),
                    'wm_menu_url' => $menu->WebMenuUrl ? $menu->WebMenuUrl->wmu_nama : null,
                    'wm_status_menu' => $menu->wm_status_menu,
                    'wm_parent_id' => $menu->wm_parent_id,
                    'wm_urutan_menu' => $menu->wm_urutan_menu,
                    'jenis_menu_nama' => $menu->Level ? $menu->Level->hak_akses_nama : 'Tidak terdefinisi',
                    'hak_akses_kode' => $menu->Level ? $menu->Level->hak_akses_kode : '',
                    'parent_menu_nama' => $menu->parentMenu ? ($menu->parentMenu->wm_menu_nama ?: ($menu->parentMenu->WebMenuGlobal ? $menu->parentMenu->WebMenuGlobal->wmg_nama_default : null)) : null,
                    'created_by' => $menu->created_by,
                    'created_at' => $menu->created_at->format('Y-m-d H:i:s'),
                    'updated_by' => $menu->updated_by,
                    'updated_at' => $menu->updated_at ? $menu->updated_at->format('Y-m-d H:i:s') : null,
                ]
            ];

            return $result;
        } catch (\Exception $e) {
            Log::error('Error in detail_menu: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil detail menu: ' . $e->getMessage()
            ];
        }
    }
    public static function reorderMenus($data)
    {
        try {
            DB::beginTransaction();

            // Kumpulkan semua ID menu untuk memudahkan pengecekan
            $menuIds = [];
            foreach ($data as $item) {
                $menuIds[] = $item['id'];

                if (isset($item['children'])) {
                    foreach ($item['children'] as $child) {
                        $menuIds[] = $child['id'];
                    }
                }
            }

            // Ambil informasi menu asli dari database
            $originalMenus = self::whereIn('web_menu_id', $menuIds)->get()->keyBy('web_menu_id');

            // Untuk memeriksa duplikasi nama menu dalam satu level
            $menuNamesByLevel = [];

            // Cek apakah ada menu SAR yang dimodifikasi oleh non-SAR
            $userhakAksesKode = Auth::user()->level->hak_akses_kode;
            $hasSARMenuModification = false;

            // Map dari menu ID ke level kode untuk validasi
            $menuLevelMap = [];
            foreach ($originalMenus as $menu) {
                if ($menu->Level) {
                    $menuLevelMap[$menu->web_menu_id] = $menu->Level->hak_akses_kode;
                }
            }

            // Cek perubahan pada menu SAR
            foreach ($data as $item) {
                $menuId = $item['id'];
                $originalLevel = $menuLevelMap[$menuId] ?? null;

                // Jika menu adalah SAR tapi user bukan SAR, tandai ada modifikasi menu SAR
                if ($originalLevel === 'SAR' && $userhakAksesKode !== 'SAR') {
                    if (isset($item['parent_id']) && $item['parent_id'] != null) {
                        $hasSARMenuModification = true;
                        break;
                    }
                }

                // Cek juga submenu
                if (isset($item['children'])) {
                    foreach ($item['children'] as $child) {
                        $childId = $child['id'];
                        $childOriginalLevel = $menuLevelMap[$childId] ?? null;

                        // Jika child menu adalah SAR tapi user bukan SAR, tandai ada modifikasi
                        if ($childOriginalLevel === 'SAR' && $userhakAksesKode !== 'SAR') {
                            $hasSARMenuModification = true;
                            break;
                        }

                        // Jika parent bukan SAR tapi child SAR, ini juga modifikasi tidak valid
                        if ($originalLevel !== 'SAR' && $childOriginalLevel === 'SAR' && $userhakAksesKode !== 'SAR') {
                            $hasSARMenuModification = true;
                            break;
                        }
                    }
                    if ($hasSARMenuModification) break;
                }
            }

            // Jika ada modifikasi menu SAR oleh non-SAR, tolak request
            if ($hasSARMenuModification) {
                DB::rollBack();
                return [
                    'success' => false,
                    'message' => 'Hanya pengguna dengan level Super Administrator yang dapat mengubah menu SAR'
                ];
            }

            // Mendapatkan mapping level kode ke ID
            $levelMapping = [];
            $levels = HakAksesModel::where('isDeleted', 0)->get();
            foreach ($levels as $level) {
                $levelMapping[$level->hak_akses_kode] = $level->hak_akses_id;
            }

            // Proses reordering dan pemindahan menu
            foreach ($data as $position => $item) {
                $menu = $originalMenus[$item['id']] ?? null;

                if (!$menu) continue;

                // Menentukan parent
                $parentId = $item['parent_id'] ?? null;

                // Data yang akan diupdate
                $updateData = [
                    'wm_parent_id' => $parentId,
                    'wm_urutan_menu' => $position + 1,
                ];

                // Update hak akses jika menu dipindahkan antar level
                if (isset($item['level']) && $parentId === null) {
                    // Hanya update level jika ini adalah menu utama (tanpa parent)
                    $levelId = $levelMapping[$item['level']] ?? null;
                    if ($levelId) {
                        $updateData['fk_m_hak_akses'] = $levelId;
                    }
                } elseif ($parentId) {
                    // Jika ini submenu, ambil level dari parent
                    $parentMenu = $originalMenus[$parentId] ?? null;
                    if ($parentMenu) {
                        $updateData['fk_m_hak_akses'] = $parentMenu->fk_m_hak_akses;
                    }
                }

                // Update menu (tanpa mengubah fk_web_menu_global)
                $menu->update($updateData);

                // Memperbarui submenu
                if (isset($item['children'])) {
                    foreach ($item['children'] as $childPosition => $child) {
                        $childMenu = $originalMenus[$child['id']] ?? null;
                        if ($childMenu) {
                            $childUpdateData = [
                                'wm_parent_id' => $item['id'],
                                'wm_urutan_menu' => $childPosition + 1
                            ];

                            // Set level child sama dengan parent
                            $childUpdateData['fk_m_hak_akses'] = $menu->fk_m_hak_akses;

                            // Update child menu (tanpa mengubah fk_web_menu_global)
                            $childMenu->update($childUpdateData);
                        }
                    }
                }
            }

            $result = [
                'success' => true,
                'message' => 'Urutan menu berhasil diperbarui'
            ];
            DB::commit();
            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengatur ulang urutan menu: ' . $e->getMessage()
            ];
        }
    }
    private static function reorderAfterDelete($parentId)
    {
        $menus = self::where('wm_parent_id', $parentId)
            ->where('isDeleted', 0)
            ->orderBy('wm_urutan_menu')
            ->get();

        foreach ($menus as $index => $menu) {
            $menu->update([
                'wm_urutan_menu' => $index + 1
            ]);
        }
    }

    public static function getDynamicMenuUrl($menuName, $appKey = 'app ppid')
    {
        // Mencari URL secara langsung berdasarkan nama menu dan app_key
        $menuUrl = WebMenuUrlModel::whereHas('Application', function ($query) use ($appKey) {
            $query->where('app_key', $appKey);
        })
            ->where('wmu_nama', $menuName)
            ->first();

        // Jika URL ditemukan, kembalikan nama URL
        if ($menuUrl) {
            return $menuUrl->wmu_nama;
        }

        // Jika URL tidak ditemukan, kembalikan nama menu sebagai fallback
        return $menuName;
    }

    public static function getMenusByLevelWithPermissions($hakAksesKode, $userId)
    {
        // Dapatkan hak_akses_id dari hak_akses_kode
        $level = HakAksesModel::where('hak_akses_kode', $hakAksesKode)->first();
        if (!$level) return collect([]);

        $hakAksesId = $level->hak_akses_id;

        // Cek apakah user memiliki hak akses dengan level ini
        $hasLevel = DB::table('set_user_hak_akses')
            ->where('fk_m_user', $userId)
            ->where('fk_m_hak_akses', $hakAksesId)
            ->where('isDeleted', 0)
            ->exists();

        if (!$hasLevel && $hakAksesKode !== 'SAR') {
            return collect([]);
        }

        // Ambil menu berdasarkan level
        $menus = self::where('fk_m_hak_akses', $hakAksesId)
            ->where('wm_status_menu', 'aktif')
            ->where('isDeleted', 0)
            ->whereNull('wm_parent_id')
            ->with(['children' => function ($query) use ($hakAksesId) {
                $query->where('fk_m_hak_akses', $hakAksesId)
                    ->where('wm_status_menu', 'aktif')
                    ->where('isDeleted', 0)
                    ->orderBy('wm_urutan_menu');
            }, 'WebMenuGlobal.WebMenuUrl', 'Level'])
            ->orderBy('wm_urutan_menu')
            ->get();

        // Filter menu berdasarkan hak akses
        $filteredMenus = $menus->filter(function ($menu) use ($userId, $hakAksesKode) {
            // Untuk menu utama (parent)
            if (!$menu->wm_parent_id) {
                // Jika tidak ada submenu, cek hak akses langsung
                if ($menu->children->isEmpty()) {
                    return SetHakAksesModel::cekHakAksesMenu($userId, $menu->WebMenuUrl->wmu_nama ?? '');
                }

                // Jika ada submenu, cek apakah salah satu submenu punya hak akses
                $hasAccessibleChildren = $menu->children->contains(function ($child) use ($userId) {
                    return SetHakAksesModel::cekHakAksesMenu($userId, $child->WebMenuUrl->wmu_nama ?? '');
                });

                return $hasAccessibleChildren;
            }

            return false;
        });

        return $filteredMenus;
    }

    // Method untuk mendapatkan notifikasi
    public static function getNotifikasiCount($hakAksesKode)
    {
        switch ($hakAksesKode) {
            case 'ADM':
                return NotifAdminModel::where('sudah_dibaca_notif_admin', null)->count();
            case 'VFR':
                return NotifVerifikatorModel::where('sudah_dibaca_notif_verif', null)->count();
            case 'MPU':
                // Sesuaikan dengan model notifikasi MPU jika ada
                return 0;
            default:
                return 0;
        }
    }
    public function getDisplayName()
    {
        // Gunakan wm_menu_nama jika ada, jika tidak gunakan nama default dari WebMenuGlobal
        return $this->wm_menu_nama ?: ($this->WebMenuGlobal ? $this->WebMenuGlobal->wmg_nama_default : 'Unnamed Menu');
    }

    public static function getActiveMenuByUrl($currentUrl)
    {
        // Cek apakah URL adalah e-form
        $eformActiveMenu = \Modules\Sisfo\App\Helpers\EFormActiveMenuHelper::getActiveMenu($currentUrl);
        if ($eformActiveMenu !== null) {
            return $eformActiveMenu;
        }

        // Jika bukan URL e-form, lanjutkan dengan logika standar...
        $menuUrl = DB::table('web_menu_url as wmu')
            ->join('web_menu_global as wmg', 'wmu.web_menu_url_id', '=', 'wmg.fk_web_menu_url')
            ->join('web_menu as wm', 'wmg.web_menu_global_id', '=', 'wm.fk_web_menu_global')
            ->where('wmu.wmu_nama', $currentUrl)
            ->where('wm.wm_status_menu', 'aktif')
            ->where('wm.isDeleted', 0)
            ->select('wmu.wmu_nama', 'wm.wm_menu_nama', 'wmg.wmg_nama_default')
            ->first();

        if ($menuUrl) {
            // Ambil nama menu yang akan digunakan sebagai active menu
            $menuName = $menuUrl->wm_menu_nama ?: $menuUrl->wmg_nama_default;

            // Standardisasi format: lowercase, tanpa spasi dan karakter khusus
            return strtolower(str_replace(' ', '', $menuName));
        }

        // Jika tidak ditemukan di database, gunakan format default dari URL
        // Ambil segmen terakhir dari URL (setelah slash terakhir)
        $urlSegments = explode('/', $currentUrl);
        $lastSegment = end($urlSegments);

        // Untuk URL dengan akhiran -admin, gunakan versi tanpa admin
        if (str_ends_with($lastSegment, '-admin')) {
            $lastSegment = str_replace('-admin', '', $lastSegment);
        }

        return strtolower($lastSegment);
    }
}
