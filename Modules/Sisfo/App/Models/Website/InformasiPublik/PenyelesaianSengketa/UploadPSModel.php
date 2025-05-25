<?php

namespace Modules\Sisfo\App\Models\Website\InformasiPublik\PenyelesaianSengketa;

use Modules\Sisfo\App\Models\TraitsModel;
use Illuminate\Support\Facades\DB;
use Modules\Sisfo\App\Models\Log\TransactionModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class UploadPSModel extends Model
{
    use TraitsModel;

    protected $table = 't_upload_ps';
    protected $primaryKey = 'upload_ps_id';
    protected $fillable = [
        'fk_m_penyelesaian_sengketa',
        'kategori_upload_ps',
        'upload_ps'
    ];

    public function PenyelesaianSengketa()
    {
        return $this->belongsTo(PenyelesaianSengketaModel::class, 'fk_m_penyelesaian_sengketa', 'penyelesaian_sengketa_id');
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
            ->with('PenyelesaianSengketa');

        // Tambahkan fungsionalitas pencarian
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('PenyelesaianSengketa', function ($subQuery) use ($search) {
                    $subQuery->where('ps_nama', 'like', "%{$search}%");
                })
                ->orWhere('kategori_upload_ps', 'like', "%{$search}%")
                ->orWhere('upload_ps', 'like', "%{$search}%");
            });
        }
        
        return self::paginateResults($query, $perPage);
    }

    public static function createData($request)
    {
        $uploadFile = self::uploadFile(
            $request->file('upload_ps_file'),
            'penyelesaian_sengketa'
        );

        try {
            DB::beginTransaction();

            $data = $request->t_upload_ps;
            
            // Jika file diupload dan kategori adalah file
            if ($uploadFile && $data['kategori_upload_ps'] == 'file') {
                $data['upload_ps'] = $uploadFile;
            }

            $psUpload = self::create($data);

            TransactionModel::createData(
                'CREATED',
                $psUpload->upload_ps_id,
                $psUpload->kategori_upload_ps . ' - ' . $psUpload->PenyelesaianSengketa->ps_nama
            );

            DB::commit();
            
            return self::responFormatSukses($psUpload, 'Data Upload Penyelesaian Sengketa berhasil dibuat');
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

            return self::responFormatError($e, 'Gagal membuat data Upload Penyelesaian Sengketa');
        }
    }

    public static function updateData($request, $id)
    {
        $uploadFile = self::uploadFile(
            $request->file('upload_ps_file'),
            'penyelesaian_sengketa'
        );

        try {
            DB::beginTransaction();
            
            $psUpload = self::findOrFail($id);
            $data = $request->t_upload_ps;

            // Jika file diupload dan kategori adalah file
            if ($uploadFile && $data['kategori_upload_ps'] == 'file') {
                // Hapus file lama jika ada
                if ($psUpload->upload_ps && $psUpload->kategori_upload_ps == 'file') {
                    self::removeFile($psUpload->upload_ps);
                }
                $data['upload_ps'] = $uploadFile;
            } 
            // Jika tidak ada upload file baru tetapi kategori berubah dari link ke file
            elseif ($data['kategori_upload_ps'] == 'file' && $psUpload->kategori_upload_ps == 'link') {
                // Gunakan nilai yang sudah ada jika ada
                if ($psUpload->upload_ps) {
                    $data['upload_ps'] = $psUpload->upload_ps;
                }
            }

            $psUpload->update($data);

            TransactionModel::createData(
                'UPDATED',
                $psUpload->upload_ps_id,
                $psUpload->kategori_upload_ps . ' - ' . $psUpload->PenyelesaianSengketa->ps_nama
            );

            DB::commit();
            
            return self::responFormatSukses($psUpload, 'Data Upload Penyelesaian Sengketa berhasil diperbarui');
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
            
            return self::responFormatError($e, 'Gagal memperbarui data Upload Penyelesaian Sengketa');
        }
    }

    public static function deleteData($id)
    {
        try {
            DB::beginTransaction();

            $psUpload = self::findOrFail($id);

            // Jika kategori adalah file, hapus file fisik
            if ($psUpload->kategori_upload_ps == 'file' && $psUpload->upload_ps) {
                self::removeFile($psUpload->upload_ps);
            }
            
            $psUpload->delete();
            
            TransactionModel::createData(
                'DELETED',
                $psUpload->upload_ps_id,
                $psUpload->kategori_upload_ps . ' - ' . $psUpload->PenyelesaianSengketa->ps_nama
            );

            DB::commit();
            
            return self::responFormatSukses($psUpload, 'Data Upload Penyelesaian Sengketa berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return self::responFormatError($e, 'Gagal menghapus data Upload Penyelesaian Sengketa'); 
        }
    }

    public static function detailData($id)
    {
        return self::with('PenyelesaianSengketa')->findOrFail($id);
    }

    public static function validasiData($request)
    {
        $rules = [
            't_upload_ps.fk_m_penyelesaian_sengketa' => 'required|exists:m_penyelesaian_sengketa,penyelesaian_sengketa_id',
            't_upload_ps.kategori_upload_ps' => 'required|in:link,file',
        ];

        // Validasi berbeda untuk file dan link
        if ($request->input('t_upload_ps.kategori_upload_ps') == 'file') {
            // Cek apakah ini adalah edit (ada id) atau create
            if ($request->route('id')) {
                $psUpload = self::findOrFail($request->route('id'));
                // Jika file sudah ada dan tidak mengupload baru
                if ($psUpload->upload_ps && !$request->hasFile('upload_ps_file')) {
                    // Tidak perlu validasi file
                } else {
                    $rules['upload_ps_file'] = 'required|file|mimes:pdf|max:5120';
                }
            } else {
                $rules['upload_ps_file'] = 'required|file|mimes:pdf|max:5120';
            }
        } else {
            $rules['t_upload_ps.upload_ps'] = 'required|url';
        }

        $messages = [
            't_upload_ps.fk_m_penyelesaian_sengketa.required' => 'Penyelesaian sengketa wajib diisi',
            't_upload_ps.fk_m_penyelesaian_sengketa.exists' => 'Penyelesaian sengketa tidak valid',
            't_upload_ps.kategori_upload_ps.required' => 'Kategori upload wajib diisi',
            't_upload_ps.kategori_upload_ps.in' => 'Kategori upload hanya boleh berupa link atau file',
            't_upload_ps.upload_ps.required' => 'URL link wajib diisi',
            't_upload_ps.upload_ps.url' => 'Format URL tidak valid',
            'upload_ps_file.required' => 'File wajib diupload untuk kategori file',
            'upload_ps_file.file' => 'Upload harus berupa file',
            'upload_ps_file.mimes' => 'Format file hanya boleh PDF',
            'upload_ps_file.max' => 'Ukuran file maksimal 5MB',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return true;
    }
}