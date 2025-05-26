<?php

namespace Modules\Sisfo\App\Models\Website\LayananInformasi;

use Modules\Sisfo\App\Models\Log\TransactionModel;
use Modules\Sisfo\App\Models\TraitsModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class LIDinamisModel extends Model
{
    use TraitsModel;

    protected $table = 'm_li_dinamis';
    protected $primaryKey = 'li_dinamis_id';
    protected $fillable = [
        'li_dinamis_kode',
        'li_dinamis_nama',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable = array_merge($this->fillable, $this->getCommonFields());
    }
   // select api
   public static function getDataLayananInformasiDinamis()
   {
       $arr_data = self::query()
           ->from('m_li_dinamis')
           ->select([
               'li_dinamis_id',
               'li_dinamis_kode',
               'li_dinamis_nama',
               'created_at'
           ])
           ->where('isDeleted', 0)
           ->get()
           ->map(function($liDinamis){
               // Store the original LI Dinamis record data
               $liDinamisData = [
                   'li_dinamis_id' => $liDinamis->li_dinamis_id,
                   'li_dinamis_kode' => $liDinamis->li_dinamis_kode,
                   'li_dinamis_nama' => $liDinamis->li_dinamis_nama,
                   'tanggal_dibuat' => $liDinamis->created_at
               ];
               
               // Get upload details for this LI Dinamis
               $uploadDetails = DB::table('t_lid_upload')
                   ->select([
                       'lid_upload_id',
                       'fk_m_li_dinamis',
                       'lid_upload_type',
                       'lid_upload_value'
                   ])
                   ->where('fk_m_li_dinamis', $liDinamis->li_dinamis_id)
                   ->where('isDeleted', 0)
                   ->get()
                   ->map(function($upload){
                       // Only include 'dokumen' property for file type
                       $result = [
                           'upload_detail_id' => $upload->lid_upload_id,
                           'upload_type' => $upload->lid_upload_type,
                       ];
                       
                       if ($upload->lid_upload_type == 'file') {
                           $result['dokumen'] = asset('storage/' . $upload->lid_upload_value);
                       }
                       
                       return $result;
                   });
               
               // Add upload details to the result
               $liDinamisData['upload_detail'] = $uploadDetails;
               
               return $liDinamisData;
           })
           ->toArray();
       
       return $arr_data;   
   }


    public static function selectData($perPage = null, $search = '')
    {
        $query = self::query()
            ->where('isDeleted', 0);

        // Tambahkan fungsionalitas pencarian
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('li_dinamis_kode', 'like', "%{$search}%")
                  ->orWhere('li_dinamis_nama', 'like', "%{$search}%");
            });
        }

        return self::paginateResults($query, $perPage);
    }

    public static function createData($request)
    {
        try {
            DB::beginTransaction();

            // Validasi data
            self::validasiData($request);

            $data = $request->m_li_dinamis;
            $liDinamis = self::create($data);

            TransactionModel::createData(
                'CREATED',
                $liDinamis->li_dinamis_id,
                $liDinamis->li_dinamis_nama
            );

            DB::commit();

            return self::responFormatSukses($liDinamis, 'Layanan Informasi Dinamis berhasil dibuat');
        } catch (ValidationException $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal membuat Layanan Informasi Dinamis');
        }
    }

    public static function updateData($request, $id)
    {
        try {
            DB::beginTransaction();

            // Validasi data
            self::validasiData($request, $id);

            $liDinamis = self::findOrFail($id);
            $data = $request->m_li_dinamis;
            $liDinamis->update($data);

            TransactionModel::createData(
                'UPDATED',
                $liDinamis->li_dinamis_id,
                $liDinamis->li_dinamis_nama
            );

            DB::commit();

            return self::responFormatSukses($liDinamis, 'Layanan Informasi Dinamis berhasil diperbarui');
        } catch (ValidationException $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal memperbarui Layanan Informasi Dinamis');
        }
    }

    public static function deleteData($id)
    {
        try {
            DB::beginTransaction();

            $liDinamis = self::findOrFail($id);

            $isUsed = LIDinamisModel::where('li_dinamis_id', $id)
                ->where('isDeleted', 0)
                ->exists();
            if ($isUsed) {
                DB::rollBack();
                throw new \Exception('Layanan Informasi Dinamis tidak dapat dihapus karena sedang digunakan.');
            }
            $liDinamis->delete();

            TransactionModel::createData(
                'DELETED',
                $liDinamis->li_dinamis_id,
                $liDinamis->li_dinamis_nama
            );

            DB::commit();

            return self::responFormatSukses($liDinamis, 'Layanan Informasi Dinamis berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal menghapus Layanan Informasi Dinamis');
        }
    }

    public static function detailData($id)
    {
        return self::findOrFail($id);
    }

    public static function validasiData($request, $id = null)
    {
        $rules = [
            'm_li_dinamis.li_dinamis_kode' => 'required|string|max:20',
            'm_li_dinamis.li_dinamis_nama' => 'required|string|max:255',
        ];

        $messages = [
            'm_li_dinamis.li_dinamis_kode.required' => 'Kode Layanan Informasi Dinamis wajib diisi',
            'm_li_dinamis.li_dinamis_kode.max' => 'Kode Layanan Informasi Dinamis maksimal 20 karakter',
            'm_li_dinamis.li_dinamis_nama.required' => 'Nama Layanan Informasi Dinamis wajib diisi',
            'm_li_dinamis.li_dinamis_nama.max' => 'Nama Layanan Informasi Dinamis maksimal 255 karakter',
        ];

        // Tambahkan validasi unique untuk kode jika diperlukan
        if ($id === null) {
            // Untuk create (penambahan data baru)
            $rules['m_li_dinamis.li_dinamis_kode'] .= '|unique:m_li_dinamis,li_dinamis_kode,NULL,li_dinamis_id,isDeleted,0';
            $messages['m_li_dinamis.li_dinamis_kode.unique'] = 'Kode Layanan Informasi Dinamis sudah digunakan';
        } else {
            // Untuk update (perubahan data)
            $rules['m_li_dinamis.li_dinamis_kode'] .= '|unique:m_li_dinamis,li_dinamis_kode,' . $id . ',li_dinamis_id,isDeleted,0';
            $messages['m_li_dinamis.li_dinamis_kode.unique'] = 'Kode Layanan Informasi Dinamis sudah digunakan';
        }

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return true;
    }
}