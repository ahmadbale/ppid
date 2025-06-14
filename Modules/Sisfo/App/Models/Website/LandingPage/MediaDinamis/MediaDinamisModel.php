<?php

namespace Modules\Sisfo\App\Models\Website\LandingPage\MediaDinamis;

use Modules\Sisfo\App\Models\Log\TransactionModel;
use Modules\Sisfo\App\Models\TraitsModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class MediaDinamisModel extends Model
{
    use TraitsModel;

    protected $table = 'm_media_dinamis';
    protected $primaryKey = 'media_dinamis_id';
    protected $fillable = [
        'md_kategori_media',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable = array_merge($this->fillable, $this->getCommonFields());
    }
    // function untuk API Hero section
    public static function getDataHeroSection()
    {
        $arr_data = self::query()
        ->from('m_media_dinamis')
        ->select([
            'media_dinamis_id',
            'md_kategori_media'
        ])
        ->where('media_dinamis_id', 1)
        ->where('isDeleted', 0)
        ->get()
        ->map(function ($kategori) {
            // Ambil Detail Media untuk Dokumentasi
            $dokumentasiMedia = DetailMediaDinamisModel::where('fk_m_media_dinamis', $kategori->media_dinamis_id)
                ->where('isDeleted', 0)
                ->where('status_media', 'aktif')
                ->orderBy('detail_media_dinamis_id', 'desc')
                ->limit(6)
                ->get()
                ->map(function ($media) {
                    // Tampung nilai media sesuai tipe
                    $mediaValue = null;
                    if ($media->dm_type_media == 'file') {
                        $mediaValue = image_asset( $media->dm_media_upload);
                    } elseif ($media->dm_type_media == 'link') {
                        $mediaValue = $media->dm_media_upload; 
                    }

                    // Kembalikan array yang mencakup ID dan nilai media
                    return [
                        'id' => $media->detail_media_dinamis_id,
                        'tipe upload' => $media->dm_type_media,
                        'media' => $mediaValue
                    ];
                })
                ->filter(function ($item) {
                    return !is_null($item['media']);
                })
                ->values()
                ->toArray();

            return [
                'kategori_id' => $kategori->media_dinamis_id,
                'kategori_nama' => $kategori->md_kategori_media,
                'media' => $dokumentasiMedia
            ];
        })
        ->toArray();

    return $arr_data;
    }
    public static function getDataDokumentasi()
    {
        $arr_data = self::query()
            ->from('m_media_dinamis')
            ->select([
                'media_dinamis_id',
                'md_kategori_media'
            ])
            ->where('media_dinamis_id', 2)
            ->where('isDeleted', 0)
            ->get()
            ->map(function ($kategori) {
                // Ambil Detail Media untuk Dokumentasi
                $dokumentasiMedia = DetailMediaDinamisModel::where('fk_m_media_dinamis', $kategori->media_dinamis_id)
                    ->where('isDeleted', 0)
                    ->where('status_media', 'aktif')
                    ->orderBy('detail_media_dinamis_id', 'desc')
                    ->limit(6)
                    ->get()
                    ->map(function ($media) {
                        // Tampung nilai media sesuai tipe
                        $mediaValue = null;
                        if ($media->dm_type_media == 'file') {
                            $mediaValue = image_asset( $media->dm_media_upload);
                        } elseif ($media->dm_type_media == 'link') {
                            $mediaValue = $media->dm_media_upload; // Kembalikan link asli
                        }

                        // Kembalikan array yang mencakup ID dan nilai media
                        return [
                            'id' => $media->detail_media_dinamis_id,
                            'tipe upload' => $media->dm_type_media,
                            'media' => $mediaValue
                        ];
                    })
                    ->filter(function ($item) {
                        return !is_null($item['media']);
                    })
                    ->values()
                    ->toArray();

                return [
                    'kategori_id' => $kategori->media_dinamis_id,
                    'kategori_nama' => $kategori->md_kategori_media,
                    'media' => $dokumentasiMedia
                ];
            })
            ->toArray();

        return $arr_data;
    }
    public static function getDataMediaInformasiPublik($showAll = false)
    {
        $arr_data = self::query()
            ->from('m_media_dinamis')
            ->select([
                'media_dinamis_id',
                'md_kategori_media'
            ])
            ->where('media_dinamis_id', 3)
            ->where('isDeleted', 0)
            ->get()
            ->map(function ($kategori) use ($showAll) {
                // Tentukan limit berdasarkan parameter showAll
                $query = DetailMediaDinamisModel::where('fk_m_media_dinamis', $kategori->media_dinamis_id)
                    ->where('isDeleted', 0)
                    ->where('status_media', 'aktif')
                    ->orderBy('detail_media_dinamis_id', 'desc');
                
                // Jika tidak showAll, batasi hanya 1 data
                if (!$showAll) {
                    $query->limit(1);
                }
                
                $dokumentasiMedia = $query->get()
                    ->map(function ($media) {
                        // Tampung nilai media sesuai tipe
                        $mediaValue = null;
                        if ($media->dm_type_media == 'file') {
                            $mediaValue = image_asset( $media->dm_media_upload);
                        } elseif ($media->dm_type_media == 'link') {
                            $mediaValue = $media->dm_media_upload; // Kembalikan link asli
                        }
    
                        return [
                            'id' => $media->detail_media_dinamis_id,
                            'judul' => $media->dm_judul_media,
                            'tipe' => $media->dm_type_media,
                            'media' => $mediaValue
                        ];
                    })
                    ->filter(function ($item) {
                        return !is_null($item['media']);
                    })
                    ->values()
                    ->toArray();
    
                // Hitung total data untuk "has_more"
                $totalMedia = DetailMediaDinamisModel::where('fk_m_media_dinamis', $kategori->media_dinamis_id)
                    ->where('isDeleted', 0)
                    ->where('status_media', 'aktif')
                    ->count();
    
                return [
                    'kategori_id' => $kategori->media_dinamis_id,
                    'kategori_nama' => $kategori->md_kategori_media,
                    'media' => $dokumentasiMedia,
                    'has_more' => $totalMedia > 1 && !$showAll, // True jika ada lebih dari 1 data dan tidak showAll
                    'total' => $totalMedia
                ];
            })
            ->toArray();
    
        return $arr_data;
    }

    
    public static function selectData($perPage = 10, $search = '')
    {
        $query = self::query()
            ->where('isDeleted', 0);

        // Add search functionality
        if (!empty($search)) {
            $query->where('md_kategori_media', 'like', "%{$search}%");
        }

        return self::paginateResults($query, $perPage);
    }


    public static function createData($request)
    {
        try {
            DB::beginTransaction();

            $data = $request->m_media_dinamis;
            $mediaDinamis = self::create($data);

            TransactionModel::createData(
                'CREATED',
                $mediaDinamis->media_dinamis_id,
                $mediaDinamis->md_kategori_media
            );

            DB::commit();

            return self::responFormatSukses($mediaDinamis, 'Media dinamis berhasil dibuat');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal membuat media dinamis');
        }
    }

    public static function updateData($request, $id)
    {
        try {
            DB::beginTransaction();

            $mediaDinamis = self::findOrFail($id);

            $data = $request->m_media_dinamis;
            $mediaDinamis->update($data);

            TransactionModel::createData(
                'UPDATED',
                $mediaDinamis->media_dinamis_id,
                $mediaDinamis->md_kategori_media
            );

            DB::commit();

            return self::responFormatSukses($mediaDinamis, 'Media dinamis berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal memperbarui media dinamis');
        }
    }

    public static function deleteData($id)
    {
        try {
            DB::beginTransaction();
    
            $mediaDinamis = self::findOrFail($id);
    
            // Check if Media dinamis being used in Detail Media Dinamis
            $isUsed = DetailMediaDinamisModel::where('fk_m_media_dinamis', $id)
                ->where('isDeleted', 0)
                ->exists();

            if ($isUsed) {
                DB::rollBack();
                throw new \Exception('Maaf, Media Dinamis masih digunakan di tempat lain');
            }
    
            $mediaDinamis->delete();
    
            TransactionModel::createData(
                'DELETED',
                $mediaDinamis->media_dinamis_id,
                $mediaDinamis->md_kategori_media
            );
    
            DB::commit();
    
            return self::responFormatSukses($mediaDinamis, 'Media dinamis berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e,  'Gagal menghapus Media Dinamis');
        }
    }

    public static function detailData($id)
    {
        return self::findOrFail($id);
    }

    public static function validasiData($request)
    {
        $rules = [
            'm_media_dinamis.md_kategori_media' => 'required|max:100',
        ];

        $messages = [
            'm_media_dinamis.md_kategori_media.required' => 'Kategori media wajib diisi',
            'm_media_dinamis.md_kategori_media.max' => 'Kategori media maksimal 100 karakter',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return true;
    }
}