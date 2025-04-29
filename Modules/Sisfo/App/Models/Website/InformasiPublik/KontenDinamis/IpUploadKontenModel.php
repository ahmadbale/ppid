<?php

namespace Modules\Sisfo\App\Models\Website\InformasiPublik\KontenDinamis;

use Modules\Sisfo\App\Models\Log\TransactionModel;
use Modules\Sisfo\App\Models\TraitsModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class IpUploadKontenModel extends Model
{
    use TraitsModel;

    protected $table = 't_ip_upload_konten';
    protected $primaryKey = 'ip_upload_konten_id';
    protected $fillable = [
        'fk_m_ip_dinamis_konten',
        'uk_judul_konten',
        'uk_dokumen_konten'
    ];

    public function IpDinamisKonten()
    {
        return $this->belongsTo(IpDinamisKontenModel::class, 'fk_m_ip_dinamis_konten', 'ip_dinamis_konten_id');
    }

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable = array_merge($this->fillable, $this->getCommonFields());
    }

    public static function selectData($perPage = null, $search = '')
    {
        $query = self::with('IpDinamisKonten')
            ->where('isDeleted', 0);

        // Tambahkan fungsionalitas pencarian
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('uk_judul_konten', 'like', "%{$search}%")
                  ->orWhereHas('IpDinamisKonten', function ($subQuery) use ($search) {
                      $subQuery->where('kd_nama_konten_dinamis', 'like', "%{$search}%");
                  });
            });
        }

        return self::paginateResults($query, $perPage);
    }

    public static function createData($request)
    {
        $dokumenFile = self::uploadFile(
            $request->file('uk_dokumen_konten'),
            'IpUpload_KontenFile'
        );
    
        try {
            DB::beginTransaction();
    
            $data = $request->t_ip_upload_konten;
            
            // Jika dokumen diupload
            if ($dokumenFile) {
                $data['uk_dokumen_konten'] = $dokumenFile;
            }
    
           $ipUploadKonten = self::create($data);
    
            TransactionModel::createData(
                'CREATED',
               $ipUploadKonten->ip_upload_konten_id,
               $ipUploadKonten->uk_judul_konten
            );
            $result = self::responFormatSukses($ipUploadKonten, 'detail upload konten berhasil dibuat');
            
            DB::commit();
            return $result;
        } catch (ValidationException $e) {
            DB::rollBack();
            self::removeFile($dokumenFile);
            return self::responValidatorError($e);
        } catch (\Exception $e) {
            DB::rollBack();
            self::removeFile($dokumenFile);
            return self::responFormatError($e, 'Gagal membuat detail upload konten');
        }
    }
    
    public static function updateData($request, $id)
    {
        $dokumenFile = self::uploadFile(
            $request->file('uk_dokumen_konten'),
            'IpUpload_KontenFile'
        );
    
        try {
            DB::beginTransaction();
    
           $ipUploadKonten = self::findOrFail($id);
            $data = $request->t_ip_upload_konten;
    
            // Jika dokumen diupload
            if ($dokumenFile) {
                // Hapus dokumen lama jika ada
                if ($ipUploadKonten->uk_dokumen_konten) {
                    self::removeFile($ipUploadKonten->uk_dokumen_konten);
                }
    
                $data['uk_dokumen_konten'] = $dokumenFile;
            }
    
           $ipUploadKonten->update($data);
    
            TransactionModel::createData(
                'UPDATED',
               $ipUploadKonten->ip_upload_konten_id,
               $ipUploadKonten->uk_judul_konten
            );
            $result = self::responFormatSukses($ipUploadKonten, 'Detail upload konten berhasil diperbarui');
            
            DB::commit();
            return $result;
        } catch (ValidationException $e) {
            DB::rollBack();
            self::removeFile($dokumenFile);
            return self::responValidatorError($e);
        } catch (\Exception $e) {
            DB::rollBack();
            self::removeFile($dokumenFile);
            return self::responFormatError($e, 'Gagal memperbarui detail upload konten');
        }
    }

    public static function deleteData($id)
    {
        try {
            DB::beginTransaction();

           $ipUploadKonten = self::findOrFail($id);

            // Hapus file dokumen jika ada
            if ($ipUploadKonten->uk_dokumen_konten) {
                self::removeFile($ipUploadKonten->uk_dokumen_konten);
            }

           $ipUploadKonten->delete();

            TransactionModel::createData(
                'DELETED',
               $ipUploadKonten->ip_upload_konten_id,
               $ipUploadKonten->uk_judul_konten
            );

            DB::commit();

            return self::responFormatSukses($ipUploadKonten, 'Detail upload konten berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal menghapus detail upload konten');
        }
    }

    public static function detailData($id)
    {
        return self::with('IpDinamisKonten')->findOrFail($id);
    }

    public static function validasiData($request)
    {
        $rules = [
            't_ip_upload_konten.fk_m_ip_dinamis_konten' => 'required|exists:m_ip_dinamis_konten,ip_dinamis_konten_id',
            't_ip_upload_konten.uk_judul_konten' => 'required|max:200',
        ];
    
        // Validasi dokumen (hanya menerima PDF)
        if ($request->hasFile('uk_dokumen_konten')) {
            $rules['uk_dokumen_konten'] = [
                'file',
                'mimes:pdf',
                'max:5120'
            ];
        }
    
        $messages = [
            't_ip_upload_konten.fk_m_ip_dinamis_konten.required' => 'Kategori konten dinamis wajib dipilih',
            't_ip_upload_konten.fk_m_ip_dinamis_konten.exists' => 'Kategori konten dinamis tidak valid',
            't_ip_upload_konten.uk_judul_konten.required' => 'Judul detail upload konten wajib diisi',
            't_ip_upload_konten.uk_judul_konten.max' => 'Judul detail upload konten maksimal 200 karakter',
            'uk_dokumen_konten.file' => 'Dokumen harus berupa file',
            'uk_dokumen_konten.mimes' => 'Dokumen hanya boleh berupa file PDF',
            'uk_dokumen_konten.max' => 'Ukuran dokumen maksimal 5MB',
        ];
    
        $validator = Validator::make($request->all(), $rules, $messages);
    
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    
        return true;
    }
}