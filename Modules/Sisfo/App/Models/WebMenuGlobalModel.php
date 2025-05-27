<?php

namespace Modules\Sisfo\App\Models;

use Modules\Sisfo\App\Models\Website\WebMenuUrlModel;
use Modules\Sisfo\App\Models\Log\TransactionModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class WebMenuGlobalModel extends Model
{
    use TraitsModel;

    protected $table = 'web_menu_global';
    protected $primaryKey = 'web_menu_global_id';

    protected $fillable = [
        'fk_web_menu_url',
        'wmg_parent_id',
        'wmg_kategori_menu',
        'wmg_urutan_menu',
        'wmg_nama_default',
        'wmg_status_menu'
    ];

    // Relationships
    public function WebMenuUrl()
    {
        return $this->belongsTo(WebMenuUrlModel::class, 'fk_web_menu_url', 'web_menu_url_id');
    }

    public function parentMenu()
    {
        return $this->belongsTo(WebMenuGlobalModel::class, 'wmg_parent_id', 'web_menu_global_id');
    }

    public function children()
    {
        return $this->hasMany(WebMenuGlobalModel::class, 'wmg_parent_id', 'web_menu_global_id')
            ->orderBy('wmg_urutan_menu');
    }

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable = array_merge($this->fillable, $this->getCommonFields());
    }

    public static function selectData($perPage = null, $search = '')
    {
        $query = self::query()
            ->where('isDeleted', 0)
            ->with(['WebMenuUrl.application', 'parentMenu']);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('wmg_nama_default', 'like', "%{$search}%")
                    ->orWhere('wmg_kategori_menu', 'like', "%{$search}%")
                    ->orWhereHas('WebMenuUrl', function ($url) use ($search) {
                        $url->where('wmu_nama', 'like', "%{$search}%")
                            ->orWhere('wmu_keterangan', 'like', "%{$search}%")
                            ->orWhereHas('application', function ($app) use ($search) {
                                $app->where('app_nama', 'like', "%{$search}%");
                            });
                    });
            });
        }

        $query->orderBy('wmg_urutan_menu', 'asc')
              ->orderBy('created_at', 'desc');

        return self::paginateResults($query, $perPage);
    }

    public static function getMenusByKategori($kategori = null)
    {
        $query = self::where('isDeleted', 0)
            ->with(['WebMenuUrl.application', 'children'])
            ->whereNull('wmg_parent_id') // Hanya menu utama
            ->orderBy('wmg_urutan_menu');

        if ($kategori) {
            $query->where('wmg_kategori_menu', $kategori);
        }

        return $query->get();
    }

    public static function getParentMenus($kategori = 'Group Menu')
    {
        return self::where('wmg_kategori_menu', $kategori)
            ->where('wmg_status_menu', 'aktif')
            ->where('isDeleted', 0)
            ->orderBy('wmg_nama_default')
            ->get();
    }

    public static function createData($request)
    {
        try {
            DB::beginTransaction();

            $data = $request->web_menu_global;

            // Handle kategori menu
            if ($data['wmg_kategori_menu'] === 'Group Menu') {
                $data['fk_web_menu_url'] = null;
                $data['wmg_parent_id'] = null;
            } elseif ($data['wmg_kategori_menu'] === 'Sub Menu') {
                // Sub menu harus memiliki parent
                if (!isset($data['wmg_parent_id']) || $data['wmg_parent_id'] === '') {
                    throw new \Exception('Sub menu harus memiliki menu induk');
                }
            } else {
                // Menu Biasa
                $data['wmg_parent_id'] = null;
            }

            // Set urutan menu otomatis berdasarkan parent_id
            $data['wmg_urutan_menu'] = self::getNextOrder($data['wmg_parent_id']);

            $webMenuGlobal = self::create($data);

            TransactionModel::createData(
                'CREATED',
                $webMenuGlobal->web_menu_global_id,
                $webMenuGlobal->wmg_nama_default
            );

            DB::commit();

            return self::responFormatSukses($webMenuGlobal, 'Menu global berhasil dibuat');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal membuat menu global');
        }
    }

    public static function updateData($request, $id)
    {
        try {
            DB::beginTransaction();

            $webMenuGlobal = self::findOrFail($id);
            $oldParentId = $webMenuGlobal->wmg_parent_id;
            $data = $request->web_menu_global;

            // Handle kategori menu
            if ($data['wmg_kategori_menu'] === 'Group Menu') {
                $data['fk_web_menu_url'] = null;
                $data['wmg_parent_id'] = null;
            } elseif ($data['wmg_kategori_menu'] === 'Sub Menu') {
                if (!isset($data['wmg_parent_id']) || $data['wmg_parent_id'] === '') {
                    throw new \Exception('Sub menu harus memiliki menu induk');
                }
            } else {
                // Menu Biasa
                $data['wmg_parent_id'] = null;
            }

            // Jika parent berubah, set urutan baru
            if ($oldParentId != $data['wmg_parent_id']) {
                $data['wmg_urutan_menu'] = self::getNextOrder($data['wmg_parent_id']);
            }
            // Jika parent tidak berubah, pertahankan urutan yang ada
            else {
                $data['wmg_urutan_menu'] = $webMenuGlobal->wmg_urutan_menu;
            }

            $webMenuGlobal->update($data);

            // Jika parent berubah, reorder menu lama
            if ($oldParentId != $data['wmg_parent_id']) {
                self::reorderAfterDelete($oldParentId);
            }

            TransactionModel::createData(
                'UPDATED',
                $webMenuGlobal->web_menu_global_id,
                $webMenuGlobal->wmg_nama_default
            );

            DB::commit();

            return self::responFormatSukses($webMenuGlobal, 'Menu global berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal memperbarui menu global');
        }
    }

    public static function deleteData($id)
    {
        try {
            DB::beginTransaction();

            $webMenuGlobal = self::findOrFail($id);

            // Cek apakah menu global memiliki submenu
            $hasChildren = self::where('wmg_parent_id', $id)->where('isDeleted', 0)->count();
            if ($hasChildren > 0) {
                DB::rollBack();
                return [
                    'success' => false,
                    'message' => "Gagal menghapus menu global ({$webMenuGlobal->wmg_nama_default}) karena masih memiliki submenu"
                ];
            }

            // Cek apakah menu global sedang digunakan pada web_menu
            $menuUsage = DB::table('web_menu')->where('fk_web_menu_global', $id)->where('isDeleted', 0)->count();
            if ($menuUsage > 0) {
                DB::rollBack();
                return [
                    'success' => false,
                    'message' => "Gagal menghapus menu global ({$webMenuGlobal->wmg_nama_default}) dikarenakan sedang digunakan pada tabel web_menu"
                ];
            }

            $parentId = $webMenuGlobal->wmg_parent_id;
            $webMenuGlobal->delete();

            // Reorder urutan menu setelah delete
            self::reorderAfterDelete($parentId);

            TransactionModel::createData(
                'DELETED',
                $webMenuGlobal->web_menu_global_id,
                $webMenuGlobal->wmg_nama_default
            );

            DB::commit();

            return self::responFormatSukses($webMenuGlobal, 'Menu global berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal menghapus menu global: ' . $e->getMessage());
        }
    }

    public static function detailData($id)
    {
        return self::with(['WebMenuUrl.application', 'parentMenu', 'children'])->findOrFail($id);
    }

    public static function validasiData($request)
    {
        $rules = [
            'web_menu_global.wmg_nama_default' => 'required|max:255',
            'web_menu_global.wmg_kategori_menu' => 'required|in:Menu Biasa,Group Menu,Sub Menu',
            'web_menu_global.wmg_status_menu' => 'required|in:aktif,nonaktif',
        ];

        $messages = [
            'web_menu_global.wmg_nama_default.required' => 'Nama default menu wajib diisi',
            'web_menu_global.wmg_nama_default.max' => 'Nama default menu maksimal 255 karakter',
            'web_menu_global.wmg_kategori_menu.required' => 'Kategori menu wajib dipilih',
            'web_menu_global.wmg_kategori_menu.in' => 'Kategori menu tidak valid',
            'web_menu_global.wmg_status_menu.required' => 'Status menu wajib dipilih',
            'web_menu_global.wmg_status_menu.in' => 'Status menu tidak valid',
        ];

        // Validasi khusus berdasarkan kategori
        $kategori = $request->input('web_menu_global.wmg_kategori_menu');
        
        if ($kategori === 'Sub Menu') {
            $rules['web_menu_global.wmg_parent_id'] = 'required|exists:web_menu_global,web_menu_global_id';
            $messages['web_menu_global.wmg_parent_id.required'] = 'Menu induk wajib dipilih untuk sub menu';
            $messages['web_menu_global.wmg_parent_id.exists'] = 'Menu induk tidak valid';
        }

        if ($kategori !== 'Group Menu') {
            $rules['web_menu_global.fk_web_menu_url'] = 'required|exists:web_menu_url,web_menu_url_id';
            $messages['web_menu_global.fk_web_menu_url.required'] = 'Menu URL wajib dipilih';
            $messages['web_menu_global.fk_web_menu_url.exists'] = 'Menu URL tidak valid';
        }

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return true;
    }

    /**
     * Mendapatkan urutan menu berikutnya berdasarkan parent_id
     * Jika parent_id null (menu utama), hitung dari semua menu utama
     * Jika parent_id ada (sub menu), hitung dari submenu parent tersebut
     */
    private static function getNextOrder($parentId = null)
    {
        $maxOrder = self::where('wmg_parent_id', $parentId)
            ->where('isDeleted', 0)
            ->max('wmg_urutan_menu');

        return ($maxOrder ?? 0) + 1;
    }

    /**
     * Mengurutkan ulang menu setelah ada yang dihapus
     */
    private static function reorderAfterDelete($parentId)
    {
        $menus = self::where('wmg_parent_id', $parentId)
            ->where('isDeleted', 0)
            ->orderBy('wmg_urutan_menu')
            ->get();

        foreach ($menus as $index => $menu) {
            $menu->update(['wmg_urutan_menu' => $index + 1]);
        }
    }

    public function getDisplayName()
    {
        return $this->wmg_nama_default;
    }

    public function getKategoriLabel()
    {
        $labels = [
            'Menu Biasa' => 'Menu Biasa',
            'Group Menu' => 'Group Menu',
            'Sub Menu' => 'Sub Menu'
        ];

        return $labels[$this->wmg_kategori_menu] ?? $this->wmg_kategori_menu;
    }
}