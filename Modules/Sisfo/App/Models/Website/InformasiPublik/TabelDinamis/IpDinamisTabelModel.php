<?php

namespace Modules\Sisfo\App\Models\Website\InformasiPublik\TabelDinamis;

use Modules\Sisfo\App\Models\TraitsModel;
use Modules\Sisfo\App\Models\Log\TransactionModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class IpDinamisTabelModel extends Model
{
    use TraitsModel;

    protected $table = 'm_ip_dinamis_tabel';
    protected $primaryKey = 'ip_dinamis_tabel_id';
    protected $fillable = [
        'ip_nama_submenu',
        'ip_judul',
        'ip_deskripsi'
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable = array_merge($this->fillable, $this->getCommonFields());
    }

    public static function selectData($perPage = null, $search = '', $kategori = '')
    {
        $query = self::query()
            ->where('isDeleted', 0);

        // Tambahkan fungsionalitas pencarian
        if (!empty($search) && is_string($search)) {
            $search = trim($search);
            if (strlen($search) > 0) {
                $query->where(function ($q) use ($search) {
                    $q->where('ip_nama_submenu', 'like', "%{$search}%")
                        ->orWhere('ip_judul', 'like', "%{$search}%");
                });
            }
        }

        // Tambahkan filter kategori jika diperlukan
        if (!empty($kategori) && is_numeric($kategori)) {
            $kategoriId = (int) $kategori;
            $query->where('ip_dinamis_tabel_id', $kategoriId);
        }

        $query->orderBy('created_at', 'desc');

        return self::paginateResults($query, $perPage);
    }

    // ... existing methods remain the same ...
    public static function createData($request)
    {
        try {
            DB::beginTransaction();

            $data = $request->m_ip_dinamis_tabel;
            $ipDinamisTabel = self::create($data);

            TransactionModel::createData(
                'CREATED',
                $ipDinamisTabel->ip_dinamis_tabel_id,
                $ipDinamisTabel->ip_nama_submenu
            );

            DB::commit();

            return self::responFormatSukses($ipDinamisTabel, 'IpDinamis Tabel  berhasil dibuat');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal membuat IpDinamis Tabel');
        }
    }

    public static function updateData($request, $id)
    {
        try {
            DB::beginTransaction();
            $ipDinamisTabel = self::findOrFail($id);

            $data = $request->m_ip_dinamis_tabel;
            $ipDinamisTabel->update($data);

            TransactionModel::createData(
                'UPDATED',
                $ipDinamisTabel->ip_dinamis_tabel_id,
                $ipDinamisTabel->ip_nama_submenu
            );

            DB::commit();

            return self::responFormatSukses($ipDinamisTabel, 'IpDinamis Tabel  berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal memperbarui IpDinamis Tabel');
        }
    }

    public static function deleteData($id)
    {
        try {
            DB::beginTransaction();
            $ipDinamisTabel = self::findOrFail($id);

            $ipDinamisTabel->delete();

            TransactionModel::createData(
                'DELETED',
                $ipDinamisTabel->ip_dinamis_tabel_id,
                $ipDinamisTabel->ip_nama_submenu
            );
            DB::commit();
            return self::responFormatSukses($ipDinamisTabel, 'IpDinamis Tabel  berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal menghapus IpDinamis Tabel');
        }
    }

    public static function detailData($id)
    {
        return self::findOrFail($id);
    }

    public static function validasiData($request)
    {
        $rules = [
            'm_ip_dinamis_tabel.ip_nama_submenu' => 'required|max:100',
            'm_ip_dinamis_tabel.ip_judul' => 'required|max:100',
            'm_ip_dinamis_tabel.ip_deskripsi' => 'nullable|max:1000', // Tambahkan validasi untuk deskripsi
        ];
        $messages = [
            'm_ip_dinamis_tabel.ip_nama_submenu.required' => 'Nama IpDinamis Tabel wajib diisi',
            'm_ip_dinamis_tabel.ip_nama_submenu.max' => 'Nama submenu maksimal 100 karakter',
            'm_ip_dinamis_tabel.ip_judul.required' => 'Judul wajib diisi',
            'm_ip_dinamis_tabel.ip_judul.max' => 'Judul maksimal 100 karakter',
            'm_ip_dinamis_tabel.ip_deskripsi.max' => 'Deskripsi maksimal 1000 karakter',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return true;
    }
}
