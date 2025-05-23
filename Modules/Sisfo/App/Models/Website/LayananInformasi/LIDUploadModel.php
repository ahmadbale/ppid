<?php

namespace Modules\Sisfo\App\Models\Website\LayananInformasi;

use Modules\Sisfo\App\Models\Log\TransactionModel;
use Modules\Sisfo\App\Models\TraitsModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class LIDUploadModel extends Model
{
    use TraitsModel;

    protected $table = 't_lid_upload';
    protected $primaryKey = 'lid_upload_id';
    protected $fillable = [
        'fk_m_li_dinamis',
        'lid_upload_type',
        'lid_upload_value'
    ];

    public function LiDinamis()
    {
        return $this->belongsTo(LIDinamisModel::class, 'fk_m_li_dinamis', 'li_dinamis_id');
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
            ->with('LiDinamis');

        // Tambahkan fungsionalitas pencarian
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->whereHas('LiDinamis', function($sq) use ($search) {
                    $sq->where('li_dinamis_nama', 'like', "%{$search}%");
                })
                ->orWhere('lid_upload_type', 'like', "%{$search}%")
                ->orWhere('lid_upload_value', 'like', "%{$search}%");
            });
        }

        return self::paginateResults($query, $perPage);
    }

    public static function createData($request)
    {
        $uploadFile = self::uploadFile(
            $request->file('lid_upload_file'),
            'lid_upload'
        );
    
        try {
            DB::beginTransaction();
    
            $data = $request->t_lid_upload;
            
            // Jika file diupload dan tipe adalah file
            if ($uploadFile && $data['lid_upload_type'] == 'file') {
                $data['lid_upload_value'] = $uploadFile;
            }
    
            $liUpload = self::create($data);
    
            TransactionModel::createData(
                'CREATED',
                $liUpload->lid_upload_id,
                $liUpload->lid_upload_type . ' - ' . $liUpload->LiDinamis->li_dinamis_nama
            );
    
            DB::commit();
    
            return self::responFormatSukses($liUpload, 'Layanan Informasi Upload berhasil dibuat');
        } catch (ValidationException $e) {
            DB::rollBack();
            
            // Hapus file jika terjadi error validasi
            if ($uploadFile) {
                self::removeFile($uploadFile);
            }
            
            return self::responValidatorError($e);
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Hapus file jika terjadi error
            if ($uploadFile) {
                self::removeFile($uploadFile);
            }
            
            return self::responFormatError($e, 'Gagal membuat Layanan Informasi Upload');
        }
    }

    public static function updateData($request, $id)
    {
        $uploadFile = self::uploadFile(
            $request->file('lid_upload_file'),
            'lid_upload'
        );
    
        try {
            DB::beginTransaction();
    
            $liUpload = self::findOrFail($id);
            $data = $request->t_lid_upload;
    
            // Jika file diupload dan tipe adalah file
            if ($uploadFile && $data['lid_upload_type'] == 'file') {
                // Hapus file lama jika ada
                if ($liUpload->lid_upload_value && $liUpload->lid_upload_type == 'file') {
                    self::removeFile($liUpload->lid_upload_value);
                }
    
                $data['lid_upload_value'] = $uploadFile;
            } 
            // Jika tidak ada upload file baru tetapi tipe berubah dari link ke file
            elseif ($data['lid_upload_type'] == 'file' && $liUpload->lid_upload_type == 'link') {
                // Gunakan nilai yang sudah ada jika ada
                if ($liUpload->lid_upload_value) {
                    $data['lid_upload_value'] = $liUpload->lid_upload_value;
                }
            }
    
            $liUpload->update($data);
    
            TransactionModel::createData(
                'UPDATED',
                $liUpload->lid_upload_id,
                $liUpload->lid_upload_type . ' - ' . $liUpload->LiDinamis->li_dinamis_nama
            );
    
            DB::commit();
    
            return self::responFormatSukses($liUpload, 'Layanan Informasi Upload berhasil diperbarui');
        } catch (ValidationException $e) {
            DB::rollBack();
            
            // Hapus file baru jika terjadi error validasi
            if ($uploadFile) {
                self::removeFile($uploadFile);
            }
            
            return self::responValidatorError($e);
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Hapus file baru jika terjadi error
            if ($uploadFile) {
                self::removeFile($uploadFile);
            }
            
            return self::responFormatError($e, 'Gagal memperbarui Layanan Informasi Upload');
        }
    }

    public static function deleteData($id)
    {
        try {
            DB::beginTransaction();

            $liUpload = self::findOrFail($id);

            // Jika tipe adalah file, hapus file fisik
            if ($liUpload->lid_upload_type == 'file' && $liUpload->lid_upload_value) {
                self::removeFile($liUpload->lid_upload_value);
            }

            $liUpload->delete();

            TransactionModel::createData(
                'DELETED',
                $liUpload->lid_upload_id,
                $liUpload->lid_upload_type . ' - ' . $liUpload->LiDinamis->li_dinamis_nama
            );

            DB::commit();

            return self::responFormatSukses($liUpload, 'Layanan Informasi Upload berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal menghapus Layanan Informasi Upload');
        }
    }

    public static function detailData($id)
    {
        return self::with('LiDinamis')->findOrFail($id);
    }

    public static function validasiData($request)
    {
        $rules = [
            't_lid_upload.fk_m_li_dinamis' => 'required|exists:m_li_dinamis,li_dinamis_id',
            't_lid_upload.lid_upload_type' => 'required|in:link,file',
        ];

        // Validasi berbeda untuk file dan link
        if ($request->input('t_lid_upload.lid_upload_type') == 'file') {
            // Cek apakah ini adalah edit (ada id) atau create
            if ($request->route('id')) {
                $liUpload = self::findOrFail($request->route('id'));
                // Jika file sudah ada dan tidak mengupload baru
                if ($liUpload->lid_upload_value && !$request->hasFile('lid_upload_file')) {
                    // Tidak perlu validasi file
                } else {
                    $rules['lid_upload_file'] = 'required|file|mimes:pdf|max:10240';
                }
            } else {
                $rules['lid_upload_file'] = 'required|file|mimes:pdf|max:10240';
            }
        } else {
            $rules['t_lid_upload.lid_upload_value'] = 'required|url';
        }

        $messages = [
            't_lid_upload.fk_m_li_dinamis.required' => 'Kategori layanan informasi wajib diisi',
            't_lid_upload.fk_m_li_dinamis.exists' => 'Kategori layanan informasi tidak valid',
            't_lid_upload.lid_upload_type.required' => 'Tipe upload wajib diisi',
            't_lid_upload.lid_upload_type.in' => 'Tipe upload hanya boleh berupa link atau file',
            't_lid_upload.lid_upload_value.required' => 'URL link wajib diisi',
            't_lid_upload.lid_upload_value.url' => 'Format URL tidak valid',
            'lid_upload_file.required' => 'File wajib diupload untuk tipe file',
            'lid_upload_file.file' => 'Upload harus berupa file',
            'lid_upload_file.mimes' => 'Format file hanya boleh PDF',
            'lid_upload_file.max' => 'Ukuran file maksimal 10MB',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return true;
    }
}