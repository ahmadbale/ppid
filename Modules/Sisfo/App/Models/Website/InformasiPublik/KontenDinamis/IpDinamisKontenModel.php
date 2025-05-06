<?php

namespace Modules\Sisfo\App\Models\Website\InformasiPublik\KontenDinamis;

use Modules\Sisfo\App\Models\TraitsModel;
use Modules\Sisfo\App\Models\Log\TransactionModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class IpDinamisKontenModel extends  Model
{
    use TraitsModel;

    protected $table = 'm_ip_dinamis_konten';
    protected $primaryKey = 'ip_dinamis_konten_id';
    protected $fillable = [
        'kd_nama_konten_dinamis'
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable = array_merge($this->fillable, $this->getCommonFields());
    }
    public static function getDataIPDaftarInformasi()
    {
        $arr_data = self::query()
            ->from('m_ip_dinamis_konten')
            ->select([
                'ip_dinamis_konten_id',
                'kd_nama_konten_dinamis',
                'created_at'
            ])
            ->where('isDeleted', 0)
            ->get()
            ->map(function ($konten_dinamis) {
                // Ambil data upload konten untuk setiap konten dinamis
                $upload_konten = DB::table('t_ip_upload_konten')
                    ->select([
                        'ip_upload_konten_id',
                        'fk_m_ip_dinamis_konten',
                        'uk_judul_konten',
                        'uk_dokumen_konten'
                    ])
                    ->where('fk_m_ip_dinamis_konten', $konten_dinamis->ip_dinamis_konten_id)
                    ->where('isDeleted', 0)
                    ->get()
                    ->map(function ($upload) {
                        return [
                            'upload_konten_id' => $upload->ip_upload_konten_id,
                            'judul_konten' => $upload->uk_judul_konten,
                            'dokumen' => asset('storage/' . $upload->uk_dokumen_konten)
                        ];
                    });
    
                return [
                    'konten_dinamis_id' => $konten_dinamis->ip_dinamis_konten_id,
                    'nama_konten' => $konten_dinamis->kd_nama_konten_dinamis,
                    'tanggal_dibuat' => $konten_dinamis->created_at,
                    'upload_konten' => $upload_konten
                ];
            })
            ->toArray();
    
        return $arr_data;
    }

    public static function selectData($perPage = null, $search = '')
    {
      $query = self::query()
      ->where('isDeleted', 0);
      if(!empty($search)){
        $query->where('kd_nama_konten_dinamis', 'like', "%{$search}%");
      }
      return self::paginateResults($query, $perPage);
    }

    public static function createData($request)
    {
      try{
        DB::beginTransaction();
        
        $data = $request->m_ip_dinamis_konten;
        $ipDinamisKonten = self :: create($data);
        
        TransactionModel::createData(
            'CREATED',
            $ipDinamisKonten->ip_dinamis_konten_id,
            $ipDinamisKonten->kd_nama_konten_dinamis
        );
        DB :: commit();
        
        return self::responFormatSukses($ipDinamisKonten, 'IpDinamis Konten berhasil dibuat');
    } catch (\Exception $e) {
        DB::rollBack();
        return self::responFormatError($e, 'Gagal membuat IpDinamis Konten');
    }
    }

    public static function updateData($request, $id)
    {
        try {
            DB::beginTransaction();
            $ipDinamisKonten = self :: findOrFail($id);

            $data = $request->m_ip_dinamis_konten;
            $ipDinamisKonten->update($data);
            
               
        TransactionModel::createData(
            'UPDATED',
            $ipDinamisKonten->ip_dinamis_konten_id,
            $ipDinamisKonten->kd_nama_konten_dinamis
        );
        DB::commit();
               
        return self::responFormatSukses($ipDinamisKonten, 'IpDinamis Konten berhasil diperbarui');
    } catch (\Exception $e) {
        DB::rollBack();
        return self::responFormatError($e, 'Gagal memperbarui IpDinamis Konten');
    }
            
        
    }

    public static function deleteData($id)
    {
        try {
              DB::beginTransaction();
            
            $ipDinamisKonten = self::findOrFail($id);
            // check if diamis konten use upload konten
            $isUsed = IpUploadKontenModel::where('fk_m_ip_dinamis_konten', $id)
            ->where('isDeleted', 0)
            ->exists();
              if ($isUsed) {
                DB::rollBack();
                throw new \Exception('Maaf, IpDinamis Konten masih digunakan di tempat lain');
            }
            $ipDinamisKonten->delete();
            TransactionModel::createData(
                'DELETED',
                $ipDinamisKonten->ip_dinamis_konten_id,
                $ipDinamisKonten->kd_nama_konten_dinamis
            );
            DB::commit();
            return self::responFormatSukses($ipDinamisKonten, 'IpDinamis Konten berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal menghapus IpDinamis Konten');
        }
    }
    public static function detailData($id) {
        return self::findOrFail($id);
    }

    public static function validasiData($request)
    {
        $rules = [
            'm_ip_dinamis_konten.kd_nama_konten_dinamis'=>'required|max:100',
        ];
        $messages = [
            'm_ip_dinamis_konten.kd_nama_konten_dinamis.required' => 'Judul Nama Konten Dinamis wajib diisi',
            'm_ip_dinamis_konten.kd_nama_konten_dinamis.max' => 'Judul Nama Konten Dinamis Maksimal 100 Karakter',
        ];
        
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return true;
    }
}