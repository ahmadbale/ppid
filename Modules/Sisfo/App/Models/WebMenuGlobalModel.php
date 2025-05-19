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
        'wmg_nama_default'
    ];

    // Relationships
    public function WebMenuUrl()
    {
        return $this->belongsTo(WebMenuUrlModel::class, 'fk_web_menu_url', 'web_menu_url_id');
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
            ->with('WebMenuUrl.application');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('wmg_nama_default', 'like', "%{$search}%")
                    ->orWhereHas('WebMenuUrl', function ($url) use ($search) {
                        $url->where('wmu_nama', 'like', "%{$search}%")
                            ->orWhere('wmu_keterangan', 'like', "%{$search}%")
                            ->orWhereHas('application', function ($app) use ($search) {
                                $app->where('app_nama', 'like', "%{$search}%");
                            });
                    });
            });
        }

        $query->orderBy('created_at', 'desc');

        return self::paginateResults($query, $perPage);
    }

    public static function createData($request)
    {
        try {
            DB::beginTransaction();

            $data = $request->web_menu_global;

            // Handle 'Group Menu' option which should save NULL
            if ($data['fk_web_menu_url'] === 'null') {
                $data['fk_web_menu_url'] = null;
            }

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

            $data = $request->web_menu_global;

            // Handle 'Group Menu' option which should save NULL
            if ($data['fk_web_menu_url'] === 'null') {
                $data['fk_web_menu_url'] = null;
            }

            $webMenuGlobal->update($data);

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

            // Check if the menu global is being used by a web_menu
            $menuUsage = DB::table('web_menu')->where('fk_web_menu_global', $id)->where('isDeleted', 0)->count();

            if ($menuUsage > 0) {
                // Kembalikan pesan error langsung tanpa menggunakan exception
                DB::rollBack();
                return [
                    'success' => false,
                    'message' => "Gagal menghapus menu global ({$webMenuGlobal->wmg_nama_default}) dikarenakan sedang digunakan pada tabel web_menu"
                ];
            }

            $webMenuGlobal->delete();

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
        return self::with('WebMenuUrl.application')->findOrFail($id);
    }

    public static function validasiData($request)
    {
        $rules = [
            'web_menu_global.wmg_nama_default' => 'required|max:255',
            // fk_web_menu_url bisa null (untuk Group Menu) atau berupa ID valid
            'web_menu_global.fk_web_menu_url' => 'nullable|string',
        ];

        $messages = [
            'web_menu_global.wmg_nama_default.required' => 'Nama default menu wajib diisi',
            'web_menu_global.wmg_nama_default.max' => 'Nama default menu maksimal 255 karakter',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return true;
    }
}
