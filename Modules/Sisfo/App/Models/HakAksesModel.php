<?php

namespace Modules\Sisfo\App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Modules\Sisfo\App\Models\TraitsModel;
use Illuminate\Validation\ValidationException;
use Modules\Sisfo\App\Models\Log\TransactionModel;
use Modules\Sisfo\App\Models\Website\WebMenuModel;

class HakAksesModel extends Model
{
    use TraitsModel;

    protected $table = 'm_hak_akses';
    protected $primaryKey = 'hak_akses_id';
    protected $fillable = [
        'hak_akses_kode',
        'hak_akses_nama'
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable = array_merge($this->fillable, $this->getCommonFields());
    }

    // public static function selectData($perPage = null, $search = '')
    // {
    //     $query = self::query()
    //         ->where('isDeleted', 0);

    //     // Tambahkan fungsionalitas pencarian
    //     if (!empty($search)) {
    //         $query->where(function($q) use ($search) {
    //             $q->where('hak_akses_kode', 'like', "%{$search}%")
    //               ->orWhere('hak_akses_nama', 'like', "%{$search}%");
    //         });
    //     }

    //     return self::paginateResults($query, $perPage);
    // }
    // tambahan
    // Tambahkan relasi ke web_menu
    public function webMenus()
    {
        return $this->hasMany(WebMenuModel::class, 'fk_m_hak_akses', 'hak_akses_id');
    }
    // tambahan
    public static function selectData($perPage = null, $search = '', $appKey = 'app ppid')
    {
        
        $query = self::query()
            ->select('m_hak_akses.*')
            ->join('web_menu as wm', 'wm.fk_m_hak_akses', '=', 'm_hak_akses.hak_akses_id')
            ->join('web_menu_global as wmg', 'wm.fk_web_menu_global', '=', 'wmg.web_menu_global_id')
            ->join('web_menu_url as wmu', 'wmg.fk_web_menu_url', '=', 'wmu.web_menu_url_id')
            ->join('m_application as ma', 'wmu.fk_m_application', '=', 'ma.application_id')
            ->where('m_hak_akses.isDeleted', 0)
            ->where('wm.isDeleted', 0)
            ->where('wmg.isDeleted', 0)
            ->where('wmu.isDeleted', 0)
            ->where('ma.isDeleted', 0)
            ->where('ma.app_key', $appKey)
            ->distinct();
    
        // Tambahkan fungsionalitas pencarian
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('m_hak_akses.hak_akses_kode', 'like', "%{$search}%")
                  ->orWhere('m_hak_akses.hak_akses_nama', 'like', "%{$search}%");
            });
        }
    
        return self::paginateResults($query, $perPage);
    }

    public static function createData($request)
    {
        try {
            DB::beginTransaction();

            $data = $request->m_hak_akses;
            $level = self::create($data);

            TransactionModel::createData(
                'CREATED',
                $level->hak_akses_id,
                $level->hak_akses_nama
            );

            DB::commit();

            return self::responFormatSukses($level, 'Level berhasil dibuat');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal membuat level');
        }
    }

    public static function updateData($request, $id)
    {
        try {
            DB::beginTransaction();

            $level = self::findOrFail($id);

            $data = $request->m_hak_akses;
            $level->update($data);

            TransactionModel::createData(
                'UPDATED',
                $level->hak_akses_id,
                $level->hak_akses_nama
            );

            DB::commit();

            return self::responFormatSukses($level, 'Level berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal memperbarui level');
        }
    }


    public static function deleteData($id)
    {
        try {
            DB::beginTransaction();

            $level = self::findOrFail($id);

            $level->delete();

            TransactionModel::createData(
                'DELETED',
                $level->hak_akses_id,
                $level->hak_akses_nama
            );

            DB::commit();


            return self::responFormatSukses($level, 'Level berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal menghapus level');
        }
    }

    public static function detailData($id)
    {
        return self::findOrFail($id);
    }

    public static function validasiData($request)
    {
        $rules = [
            'm_hak_akses.hak_akses_kode' => 'required|max:50',
            'm_hak_akses.hak_akses_nama' => 'required|max:255',
        ];

        $messages = [
            'm_hak_akses.hak_akses_kode.required' => 'Kode level wajib diisi',
            'm_hak_akses.hak_akses_kode.max' => 'Kode level maksimal 50 karakter',
            'm_hak_akses.hak_akses_nama.required' => 'Nama level wajib diisi',
            'm_hak_akses.hak_akses_nama.max' => 'Nama level maksimal 255 karakter',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return true;
    }
}