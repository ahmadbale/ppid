<?php

namespace Modules\Sisfo\App\Models\Website\LandingPage\KategoriAkses;

use Modules\Sisfo\App\Models\Log\TransactionModel;
use Modules\Sisfo\App\Models\TraitsModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AksesCepatModel extends Model
{
    use TraitsModel;

    protected $table = 't_akses_cepat';
    protected $primaryKey = 'akses_cepat_id';

    // Kolom yang dapat diisi
    protected $fillable = [
        'fk_m_kategori_akses',
        'ac_judul',
        'ac_static_icon',
        'ac_animation_icon',
        'ac_url'
    ];
     // Relasi dengan Kategori Akses
     public function kategoriAkses()
     {
         return $this->belongsTo(KategoriAksesModel::class, 'fk_m_kategori_akses', 'kategori_akses_id');
     }

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable = array_merge($this->fillable, $this->getCommonFields());
    }
       // Konstanta untuk path folder icon
       const STATIC_ICON_PATH = 'akses_cepat_static_icons';
       const ANIMATION_ICON_PATH = 'akses_cepat_animation_icons';
   


    public static function selectData($perPage = null, $search = '', $kategoriAksesId = null)
    {
        $query = self::with('kategoriAkses')
            ->where('isDeleted', 0);

        // Tambahkan fungsionalitas pencarian
        if (!empty($search)) {
            $query->where('ac_judul', 'like', "%{$search}%");
        }

        // Filter berdasarkan kategori akses
        if ($kategoriAksesId !== null) {
            $query->where('fk_m_kategori_akses', $kategoriAksesId);
        }

        return self::paginateResults($query, $perPage);
    }

    public static function createData($request)
    {
        try {
            DB::beginTransaction();

            // Validasi input
            self::validasiData($request);

            // Persiapan data
            $data = $request->t_akses_cepat;

            // Proses upload static icon
            if ($request->hasFile('t_akses_cepat.ac_static_icon')) {
                $staticIconPath = $request->file('t_akses_cepat.ac_static_icon')->store(self::STATIC_ICON_PATH, 'public');
                $data['ac_static_icon'] = basename($staticIconPath);
            }

            // Proses upload animation icon
            if ($request->hasFile('t_akses_cepat.ac_animation_icon')) {
                $animationIconPath = $request->file('t_akses_cepat.ac_animation_icon')->store(self::ANIMATION_ICON_PATH, 'public');
                $data['ac_animation_icon'] = basename($animationIconPath);
            }

            // Buat record
            $aksesCepat = self::create($data);

            // Catat log transaksi
            TransactionModel::createData(
                'CREATED',
                $aksesCepat->akses_cepat_id,
                $aksesCepat->ac_judul
            );

            DB::commit();

            return self::responFormatSukses($aksesCepat, 'Akses Cepat berhasil dibuat');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal membuat Akses Cepat');
        }
    }

    public static function updateData($request, $id)
    {
        try {
            DB::beginTransaction();

            // Validasi input
            self::validasiData($request, $id);

            // Cari record
            $aksesCepat = self::findOrFail($id);

            // Persiapan data
            $data = $request->t_akses_cepat;

            // Proses upload static icon
            if ($request->hasFile('t_akses_cepat.ac_static_icon')) {
                // Hapus static icon lama jika ada
                if ($aksesCepat->ac_static_icon) {
                    self::deleteIconFile($aksesCepat->ac_static_icon, self::STATIC_ICON_PATH);
                }

                // Upload static icon baru
                $staticIconPath = $request->file('t_akses_cepat.ac_static_icon')->store(self::STATIC_ICON_PATH, 'public');
                $data['ac_static_icon'] = basename($staticIconPath);
            }

            // Proses upload animation icon
            if ($request->hasFile('t_akses_cepat.ac_animation_icon')) {
                // Hapus animation icon lama jika ada
                if ($aksesCepat->ac_animation_icon) {
                    self::deleteIconFile($aksesCepat->ac_animation_icon, self::ANIMATION_ICON_PATH);
                }

                // Upload animation icon baru
                $animationIconPath = $request->file('t_akses_cepat.ac_animation_icon')->store(self::ANIMATION_ICON_PATH, 'public');
                $data['ac_animation_icon'] = basename($animationIconPath);
            }

            // Update record
            $aksesCepat->update($data);

            // Catat log transaksi
            TransactionModel::createData(
                'UPDATED',
                $aksesCepat->akses_cepat_id,
                $aksesCepat->ac_judul
            );

            DB::commit();

            return self::responFormatSukses($aksesCepat, 'Akses Cepat berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal memperbarui Akses Cepat');
        }
    }

    public static function deleteData($id)
    {
        try {
            DB::beginTransaction();

            // Cari record
            $aksesCepat = self::findOrFail($id);

            // Hapus file icon jika ada
            if ($aksesCepat->ac_static_icon) {
                self::deleteIconFile($aksesCepat->ac_static_icon, self::STATIC_ICON_PATH);
            }

            if ($aksesCepat->ac_animation_icon) {
                self::deleteIconFile($aksesCepat->ac_animation_icon, self::ANIMATION_ICON_PATH);
            }

            // Soft delete
            $aksesCepat->delete();

            // Catat log transaksi
            TransactionModel::createData(
                'DELETED',
                $aksesCepat->akses_cepat_id,
                $aksesCepat->ac_judul
            );

            DB::commit();

            return self::responFormatSukses($aksesCepat, 'Akses Cepat berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal menghapus Akses Cepat');
        }
    }

    public static function detailData($id)
    {
        return self::with('kategoriAkses')->findOrFail($id);
    }

    public static function validasiData($request, $id = null)
    {
        // Aturan validasi dasar
        $rules = [
            't_akses_cepat.fk_m_kategori_akses' => 'required|exists:m_kategori_akses,kategori_akses_id',
            't_akses_cepat.ac_judul' => 'required|max:100',
            't_akses_cepat.ac_url' => 'required|url|max:100',
        ];

        // Jika create baru atau update dengan file baru
        if ($id === null) {
            // Untuk create baru
            $rules['t_akses_cepat.ac_static_icon'] = 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2500';
            $rules['t_akses_cepat.ac_animation_icon'] = 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2500';
        } else {
            // Untuk update
            if (request()->hasFile('t_akses_cepat.ac_static_icon')) {
                $rules['t_akses_cepat.ac_static_icon'] = 'image|mimes:jpeg,png,jpg,gif,svg|max:2500';
            }

            if (request()->hasFile('t_akses_cepat.ac_animation_icon')) {
                $rules['t_akses_cepat.ac_animation_icon'] = 'image|mimes:jpeg,png,jpg,gif,svg|max:2500';
            }
        }

        $messages = [
            't_akses_cepat.fk_m_kategori_akses.required' => 'Kategori akses wajib dipilih',
            't_akses_cepat.fk_m_kategori_akses.exists' => 'Kategori akses tidak valid',
            't_akses_cepat.ac_judul.required' => 'Judul akses cepat wajib diisi',
            't_akses_cepat.ac_judul.max' => 'Judul akses cepat maksimal 100 karakter',
            't_akses_cepat.ac_url.required' => 'URL akses cepat wajib diisi',
            't_akses_cepat.ac_url.url' => 'URL akses cepat harus berupa URL yang valid',
            't_akses_cepat.ac_url.max' => 'URL akses cepat maksimal 100 karakter',
            't_akses_cepat.ac_static_icon.required' => 'Ikon statis wajib diunggah',
            't_akses_cepat.ac_static_icon.image' => 'Ikon statis harus berupa gambar',
            't_akses_cepat.ac_static_icon.mimes' => 'Ikon statis hanya boleh berupa file: jpeg, png, jpg, gif, atau svg',
            't_akses_cepat.ac_static_icon.max' => 'Ukuran ikon statis maksimal 2.5MB',
            't_akses_cepat.ac_animation_icon.image' => 'Ikon animasi harus berupa gambar',
            't_akses_cepat.ac_animation_icon.mimes' => 'Ikon animasi hanya boleh berupa file: jpeg, png, jpg, gif, atau svg',
            't_akses_cepat.ac_animation_icon.max' => 'Ukuran ikon animasi maksimal 2.5MB',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return true;
    }

    /**
     * Helper  untuk menghapus file ikon
     */
    private static function deleteIconFile($filename, $path)
    {
        try {
            $fullPath = $path . '/' . $filename;
            if (Storage::disk('public')->exists($fullPath)) {
                Storage::disk('public')->delete($fullPath);
                return true;
            }
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }
}