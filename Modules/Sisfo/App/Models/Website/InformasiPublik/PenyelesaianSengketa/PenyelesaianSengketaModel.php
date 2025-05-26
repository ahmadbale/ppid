<?php

namespace Modules\Sisfo\App\Models\Website\InformasiPublik\PenyelesaianSengketa;

use Modules\Sisfo\App\Models\Log\TransactionModel;
use Modules\Sisfo\App\Models\TraitsModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class PenyelesaianSengketaModel extends Model
{
    use TraitsModel;

    protected $table = 'm_penyelesaian_sengketa';
    protected $primaryKey = 'penyelesaian_sengketa_id';
    protected $fillable = [
        'ps_kode',
        'ps_nama',
        'ps_deskripsi',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable = array_merge($this->fillable, $this->getCommonFields());
    }
    // select api
    public static function getDataPenyelesaianSengketa()
    {
        $arr_data = self::query()
            ->from('m_penyelesaian_sengketa')
            ->select([
                'penyelesaian_sengketa_id',
                'ps_kode',
                'ps_nama',
                'ps_deskripsi',
                'created_at'
            ])
            ->where('isDeleted', 0)
            ->get()
            ->map(function($penyelesaianSengketa){
                // Store the original Penyelesaian Sengketa record data
                $penyelesaianSengketaData = [
                    'penyelesaian_sengketa_id' => $penyelesaianSengketa->penyelesaian_sengketa_id,
                    'ps_kode' => $penyelesaianSengketa->ps_kode,
                    'ps_nama' => $penyelesaianSengketa->ps_nama,
                    'ps_deskripsi' => $penyelesaianSengketa->ps_deskripsi,
                    'tanggal_dibuat' => $penyelesaianSengketa->created_at
                ];
                
                // Get upload details for this Penyelesaian Sengketa
                $uploadDetails = DB::table('t_upload_ps') // Changed from t_ps_upload to match your model
                    ->select([
                        'upload_ps_id', // Changed from ps_upload_id to match your model
                        'fk_m_penyelesaian_sengketa',
                        'kategori_upload_ps',
                        'upload_ps'
                    ])
                    ->where('fk_m_penyelesaian_sengketa', $penyelesaianSengketa->penyelesaian_sengketa_id)
                    ->where('isDeleted', 0)
                    ->get()
                    ->map(function($upload){
                        $result = [
                            'upload_ps_id' => $upload->upload_ps_id, // Changed from ps_upload_id
                            'kategori_upload_ps' => $upload->kategori_upload_ps,
                        ];
                        
                        if ($upload->kategori_upload_ps == 'file') {
                            $result['dokumen'] = asset('storage/' . $upload->upload_ps);
                        } else if ($upload->kategori_upload_ps == 'link') {
                            $result['link'] = $upload->upload_ps;
                        }
                        
                        return $result;
                    });
                
                // Add the upload details to the penyelesaian sengketa data
                $penyelesaianSengketaData['uploads'] = $uploadDetails;
                
                return $penyelesaianSengketaData;
            })
            ->toArray();
        
        return $arr_data;
    }         
    
    public static function selectData($perPage = null, $search = '')
    {
      $query = self::query()

          ->where('isDeleted', 0);
        // Filter berdasarkan pencarian
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('ps_kode', 'like', "%$search%")
                    ->orWhere('ps_nama', 'like', "%$search%");
            });
        }
        return self::paginateResults($query, $perPage);
    }

    public static function createData($request)
    {
      try {
        DB::beginTransaction();
        
        $data = $request->m_penyelesaian_sengketa;
        $penyelesaianSengketa = self::create($data);
        
        TransactionModel::createData(
            'CREATED',
            $penyelesaianSengketa->penyelesaian_sengketa_id,
            $penyelesaianSengketa->ps_nama
        );
        DB::commit();
        return self::responFormatSukses($penyelesaianSengketa, 'Data Penyelesaian Sengketa berhasil dibuat');
      }catch (\Exception $e) {
        DB::rollBack();
        return self::responFormatGagal($e, 'Gagal membuat data Penyelesaian Sengketa');
      }
    }

    public static function updateData($request, $id)
    {
        try {
            DB::beginTransaction();

            $penyelesaianSengketa = self::findOrFail($id);

            $data = $request->m_penyelesaian_sengketa;
            $penyelesaianSengketa->update($data);

            TransactionModel::createData(
                'UPDATED',
                $penyelesaianSengketa->penyelesaian_sengketa_id,
                $penyelesaianSengketa->ps_nama
            );

            DB::commit();
            return self::responFormatSukses($penyelesaianSengketa, 'Data Penyelesaian Sengketa berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatGagal($e, 'Gagal memperbarui data Penyelesaian Sengketa');
        }
    }
    public static function deleteData($id)
    {
        try {
            DB::beginTransaction();

            $penyelesaianSengketa = self::findOrFail($id);
            $isUsed = UploadPSModel::where('fk_m_penyelesaian_sengketa', $id)
                ->where('isDeleted', 0)
                ->exists();
            if ($isUsed) {
                throw new \Exception('Maaf, data ini sedang digunakan pada detail upload penyelesaian sengketa');
            }
            $penyelesaianSengketa->delete();
            TransactionModel::createData(
                'DELETED',
                $penyelesaianSengketa->penyelesaian_sengketa_id,
                $penyelesaianSengketa->ps_nama
            );

            DB::commit();
            return self::responFormatSukses($penyelesaianSengketa, 'Data Penyelesaian Sengketa berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatGagal($e, 'Gagal menghapus data Penyelesaian Sengketa');
        }
    }
    public static function detailData($id)
    {
        return self::findOrFail($id);
    }

    public static function validasiData()
    {
        $rules = [
            'm_penyelesaian_sengketa.ps_kode' => 'required|string|max:20',
            'm_penyelesaian_sengketa.ps_nama' => 'required|string|max:255',
            'm_penyelesaian_sengketa.ps_deskripsi' => 'required|string',
        ];
        $messages = [
            'm_penyelesaian_sengketa.ps_kode.required' => 'Kode Penyelesaian Sengketa harus diisi',
            'm_penyelesaian_sengketa.ps_kode.string' => 'Kode Penyelesaian Sengketa harus berupa string',
            'm_penyelesaian_sengketa.ps_kode.max' => 'Kode Penyelesaian Sengketa maksimal 20 karakter',
            'm_penyelesaian_sengketa.ps_nama.required' => 'Nama Penyelesaian Sengketa harus diisi',
            'm_penyelesaian_sengketa.ps_nama.string' => 'Nama Penyelesaian Sengketa harus berupa string',
            'm_penyelesaian_sengketa.ps_nama.max' => 'Nama Penyelesaian Sengketa maksimal 255 karakter',
            'm_penyelesaian_sengketa.ps_deskripsi.required' => 'Deskripsi Penyelesaian Sengketa harus diisi',
        ];
        $validator = Validator::make(request()->all(), $rules, $messages);
        if ($validator->fails()){
            throw new ValidationException($validator);
        }
        return true;
    }
}