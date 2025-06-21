<?php

namespace Modules\Sisfo\App\Models\Website;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Modules\Sisfo\App\Models\TraitsModel;
use Illuminate\Validation\ValidationException;
use Modules\Sisfo\App\Models\ApplicationModel;
use Modules\Sisfo\App\Models\WebMenuGlobalModel;
use Modules\Sisfo\App\Models\Log\TransactionModel;

class WebMenuUrlModel extends Model
{
    use TraitsModel;

    protected $table = 'web_menu_url';
    protected $primaryKey = 'web_menu_url_id';
    protected $fillable = [
        'fk_m_application',
        'wmu_nama',
        'wmu_keterangan'
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable = array_merge($this->fillable, $this->getCommonFields());
    }

    // Relationship with Application model
    public function application()
    {
        return $this->belongsTo(ApplicationModel::class, 'fk_m_application', 'application_id');
    }
    // tambahan
    public function webMenuGlobal()
    {
        return $this->hasMany(WebMenuGlobalModel::class, 'fk_web_menu_url', 'web_menu_url_id');
    }

    // public static function selectData($perPage = null, $search = '')
    // {
    //     $query = self::query()
    //         ->where('isDeleted', 0)
    //         ->with('application');

    //     // Tambahkan fungsionalitas pencarian
    //     if (!empty($search)) {
    //         $query->where(function ($q) use ($search) {
    //             $q->where('wmu_nama', 'like', "%{$search}%")
    //                 ->orWhereHas('application', function ($app) use ($search) {
    //                     $app->where('app_nama', 'like', "%{$search}%");
    //                 });
    //         });
    //     }

    //     $query->orderBy('created_at', 'desc');

    //     return self::paginateResults($query, $perPage);
    // }
    public static function selectData($perPage = null, $search = '', $appKey = null)
{
    $query = self::query()
        ->where('isDeleted', 0)
        ->with('application');

    //  Filter berdasarkan app_key jika diberikan
    if ($appKey) {
        $query->whereHas('application', function ($q) use ($appKey) {
            $q->where('app_key', $appKey);
        });
    }

    // Tambahkan fungsionalitas pencarian
    if (!empty($search)) {
        $query->where(function ($q) use ($search) {
            $q->where('wmu_nama', 'like', "%{$search}%")
                ->orWhereHas('application', function ($app) use ($search) {
                    $app->where('app_nama', 'like', "%{$search}%");
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

            $data = $request->web_menu_url;
            $webMenuUrl = self::create($data);

            TransactionModel::createData(
                'CREATED',
                $webMenuUrl->web_menu_url_id,
                $webMenuUrl->wmu_nama
            );

            DB::commit();

            return self::responFormatSukses($webMenuUrl, 'URL menu berhasil dibuat');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal membuat URL menu');
        }
    }

    public static function updateData($request, $id)
    {
        try {
            DB::beginTransaction();

            $webMenuUrl = self::findOrFail($id);

            $data = $request->web_menu_url;
            $webMenuUrl->update($data);

            TransactionModel::createData(
                'UPDATED',
                $webMenuUrl->web_menu_url_id,
                $webMenuUrl->wmu_nama
            );

            DB::commit();

            return self::responFormatSukses($webMenuUrl, 'URL menu berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal memperbarui URL menu');
        }
    }

    public static function deleteData($id)
    {
        try {
            DB::beginTransaction();

            $webMenuUrl = self::findOrFail($id);

            // Check if the URL is being used by a menu
            $menuUsage = DB::table('web_menu_global')->where('fk_web_menu_url', $id)->where('isDeleted', 0)->count();

            if ($menuUsage > 0) {
                // Tampilkan pesan tanpa menggunakan exception yang akan digabung dengan pesan lain
                DB::rollBack();
                return [
                    'success' => false,
                    'message' => "Gagal menghapus URL ({$webMenuUrl->wmu_nama}) dikarenakan sedang digunakan pada tabel web_menu_global"
                ];
            }

            $webMenuUrl->delete();

            TransactionModel::createData(
                'DELETED',
                $webMenuUrl->web_menu_url_id,
                $webMenuUrl->wmu_nama
            );

            DB::commit();

            return self::responFormatSukses($webMenuUrl, 'URL menu berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal menghapus URL menu: ' . $e->getMessage());
        }
    }

    public static function detailData($id)
    {
        return self::with('application')->findOrFail($id);
    }

    public static function validasiData($request)
    {
        $rules = [
            'web_menu_url.fk_m_application' => 'required|exists:m_application,application_id',
            'web_menu_url.wmu_nama' => 'required|max:255',
            'web_menu_url.wmu_keterangan' => 'nullable|max:1000',
        ];

        $messages = [
            'web_menu_url.fk_m_application.required' => 'Aplikasi wajib dipilih',
            'web_menu_url.fk_m_application.exists' => 'Aplikasi tidak valid',
            'web_menu_url.wmu_nama.required' => 'Nama URL menu wajib diisi',
            'web_menu_url.wmu_nama.max' => 'Nama URL menu maksimal 255 karakter',
            'web_menu_url.wmu_keterangan.max' => 'Keterangan maksimal 1000 karakter',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return true;
    }
    public static function validateAppKey($appKey)
    {
        return ApplicationModel::where('app_key', $appKey)->exists();
    }

    public function scopeByAppKey($query, $app_key)
    {
        return $query->whereHas('application', function ($q) use ($app_key) {
            $q->where('app_key', $app_key);
        });
    }
    public static function getApplications()
{
    return ApplicationModel::where('isDeleted', 0)
        ->orderBy('app_nama')
        ->get();
}
}