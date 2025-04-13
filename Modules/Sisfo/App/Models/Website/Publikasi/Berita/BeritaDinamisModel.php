<?php

namespace Modules\Sisfo\App\Models\Website\Publikasi\Berita;

use Carbon\Carbon;

use Modules\Sisfo\App\Models\TraitsModel;
use Illuminate\Support\Facades\DB;
use Modules\Sisfo\App\Models\Log\TransactionModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Contracts\Providers\Storage;


class BeritaDinamisModel extends Model
{
    use TraitsModel;

    protected $table = 'm_berita_dinamis';
    protected $primaryKey = 'berita_dinamis_id';
    protected $fillable = [
        'bd_nama_submenu',
    ];

    public static function getDataBeritaLandingPage()
    {
        $kategori = 1;
        $arr_data = DB::table('t_berita', 'tb')
            ->select([
                'tb.berita_id',
                'tb.berita_judul',
                'tb.berita_slug',
                'm_berita_dinamis.bd_nama_submenu',
                'tb.berita_thumbnail_deskripsi'
            ])
            ->join('m_berita_dinamis', 'tb.fk_m_berita_dinamis', '=', 'm_berita_dinamis.berita_dinamis_id')
            ->where('tb.isDeleted', 0)
            ->where('tb.status_berita', 'aktif')
            ->where('m_berita_dinamis.berita_dinamis_id', $kategori)
            ->orderBy('tb.berita_id', 'DESC')
            ->limit(3)
            ->get()
            ->map(function ($berita) {
                $deskripsiThumbnail = trim($berita->berita_thumbnail_deskripsi);

                return [
                    'id' => $berita->berita_id,
                    'kategori' => $berita->bd_nama_submenu,
                    'judul' => $berita->berita_judul,
                    'slug' => $berita->berita_slug,
                    'deskripsiThumbnail' => strlen($deskripsiThumbnail) > 200
                        ? substr($deskripsiThumbnail, 0, 200) . '...'
                        : $deskripsiThumbnail,
                    'url_selengkapnya' => url('#')
                ];
            })
            ->toArray();

        return $arr_data;
    }

    public static function getDataBerita($per_page = 5, $kategori_id = null)
    {
        $query = DB::table('t_berita as tb')
            ->select([
                'tb.berita_id',
                'tb.berita_judul',
                'tb.berita_slug',
                'm_berita_dinamis.bd_nama_submenu',
                'tb.created_at',
                'tb.berita_thumbnail_deskripsi',
                'tb.berita_thumbnail'
            ])
            ->join('m_berita_dinamis', 'tb.fk_m_berita_dinamis', '=', 'm_berita_dinamis.berita_dinamis_id')
            ->where('tb.isDeleted', 0)
            ->where('tb.status_berita', 'aktif');

        // Filter berdasarkan kategori jika ada
        if ($kategori_id) {
            $query->where('m_berita_dinamis.berita_dinamis_id', $kategori_id);
        }

        $arr_data = $query->orderBy('tb.berita_id', 'DESC')
            ->paginate($per_page);

        $transformedData = collect($arr_data->items())->map(function ($berita) {
            $deskripsiThumbnail = trim($berita->berita_thumbnail_deskripsi);
            $thumbnail = asset('storage/' . $berita->berita_thumbnail);
            $tanggal = \Carbon\Carbon::parse($berita->created_at)->format('d F Y');

            return [
                'berita_id' => $berita->berita_id,
                'kategori' => $berita->bd_nama_submenu,
                'judul' => $berita->berita_judul,
                'slug' => $berita->berita_slug,
                'thumbnail' => $thumbnail,
                'deskripsiThumbnail' => strlen($deskripsiThumbnail) > 200
                    ? substr($deskripsiThumbnail, 0, 200) . '...'
                    : $deskripsiThumbnail,
                'tanggal' => $tanggal,
                'url_selengkapnya' => url('berita/' . $berita->berita_slug)
            ];
        })->toArray();

        return [
            'current_page' => $arr_data->currentPage(),
            'data' => $transformedData,
            'total_pages' => $arr_data->lastPage(),
            'total_items' => $arr_data->total(),
            'per_page' => $arr_data->perPage(),
            'next_page_url' => $arr_data->nextPageUrl(),
            'prev_page_url' => $arr_data->previousPageUrl()
        ];
    }
    public static function getDetailBeritaById($berita_id)
    {
        $berita = DB::table('t_berita as tb')
            ->select([
                'tb.berita_id',
                'tb.berita_judul',
                'tb.berita_slug',
                'm_berita_dinamis.bd_nama_submenu',
                'tb.created_at',
                'tb.berita_thumbnail',
                'tb.berita_thumbnail_deskripsi',
                'tb.berita_deskripsi' // Tambahkan kolom deskripsi lengkap
            ])
            ->join('m_berita_dinamis', 'tb.fk_m_berita_dinamis', '=', 'm_berita_dinamis.berita_dinamis_id')
            ->where('tb.berita_id', $berita_id)
            ->where('tb.isDeleted', 0)
            ->where('tb.status_berita', 'aktif')
            ->first();

        if (!$berita) {
            return null;
        }

        return [
            'berita_id' => $berita->berita_id,
            'kategori' => $berita->bd_nama_submenu,
            'judul' => $berita->berita_judul,
            'slug' => $berita->berita_slug,
            'thumbnail' => asset('storage/' . $berita->berita_thumbnail),
            'deskripsiThumbnail' => $berita->berita_thumbnail_deskripsi,
            'tanggal' => \Carbon\Carbon::parse($berita->created_at)->format('d F Y'),
            'konten' => $berita->berita_deskripsi // Konten lengkap dari Summernote
        ];
    }
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable = array_merge($this->fillable, $this->getCommonFields());
    }

    public static function selectData($perPage = 10, $search = '')
    {
        $query = self::query()
            ->where('isDeleted', 0);

        // Add search functionality
        if (!empty($search)) {
            $query->where('bd_nama_submenu', 'like', "%{$search}%");
        }

        // Gunakan paginateResults dari trait BaseModelFunction
        return self::paginateResults($query, $perPage);
    }

    // Fungsi untuk membuat data baru
    public static function createData($request)
    {
        try {
            // Validasi input
            self::validasiData($request);

            DB::beginTransaction();

            // Cek apakah ada data yang sudah dihapus dengan nama yang sama
            $existingDeleted = self::withTrashed()
                ->where('isDeleted', 1)
                ->where('bd_nama_submenu', $request->bd_nama_submenu)
                ->get();

            // Hapus data yang soft deleted dengan nama yang sama secara permanen
            foreach ($existingDeleted as $item) {
                $item->forceDelete();
            }

            // Persiapan data
            $data = $request->only([
                'bd_nama_submenu'
            ]);

            // Buat record
            $saveData = self::create($data);

            // Catat log transaksi
            TransactionModel::createData(
                'CREATED',
                $saveData->berita_dinamis_id,
                $saveData->bd_nama_submenu
            );

            DB::commit();

            return self::responFormatSukses($saveData, 'Berita Dinamis berhasil dibuat');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal membuat Berita Dinamis');
        }
    }

    // Fungsi untuk mengupdate data
    public static function updateData($request, $id)
    {
        try {
            // Validasi input
            self::validasiData($request, $id);

            // Cari record
            $saveData = self::findOrFail($id);

            DB::beginTransaction();

            // Cek apakah ada data yang sudah dihapus dengan nama yang sama
            $existingDeleted = self::withTrashed()
                ->where('isDeleted', 1)
                ->where('berita_dinamis_id', '!=', $id)
                ->where('bd_nama_submenu', $request->bd_nama_submenu)
                ->get();

            // Hapus data yang soft deleted dengan nama yang sama secara permanen
            foreach ($existingDeleted as $item) {
                $item->forceDelete();
            }

            // Persiapan data
            $data = $request->only([
                'bd_nama_submenu'
            ]);

            // Update record
            $saveData->update($data);

            // Catat log transaksi
            TransactionModel::createData(
                'UPDATED',
                $saveData->berita_dinamis_id,
                $saveData->bd_nama_submenu
            );

            DB::commit();

            return self::responFormatSukses($saveData, 'Berita Dinamis berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal memperbarui Berita Dinamis');
        }
    }

    // Fungsi untuk menghapus data
    public static function deleteData($id)
    {
        try {
            // Cari record
            $saveData = self::findOrFail($id);

            // Periksa apakah submenu sedang digunakan
            $beritaCount = BeritaModel::where('fk_m_berita_dinamis', $id)
                ->where('isDeleted', 0)
                ->count();

            if ($beritaCount > 0) {
                return self::responFormatError(
                    new \Exception('Submenu tidak dapat dihapus karena masih digunakan oleh berita'),
                    'Submenu tidak dapat dihapus karena masih digunakan oleh berita'
                );
            }

            DB::beginTransaction();

            // Set isDeleted = 1 secara manual sebelum memanggil delete()
            $saveData->isDeleted = 1;
            $saveData->deleted_at = now();
            $saveData->save();

            // Soft delete dengan menggunakan fitur SoftDeletes dari Trait
            $saveData->delete();

            // Catat log transaksi
            TransactionModel::createData(
                'DELETED',
                $saveData->berita_dinamis_id,
                $saveData->bd_nama_submenu
            );

            DB::commit();

            return self::responFormatSukses($saveData, 'Berita Dinamis berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal menghapus Berita Dinamis');
        }
    }

    // Fungsi untuk detail data
    public static function detailData($id)
    {
        try {
            $beritaDinamis = self::findOrFail($id);
            return $beritaDinamis;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    // Fungsi untuk memvalidasi data
    public static function validasiData($request, $id = null)
    {
        $rules = [
            'bd_nama_submenu' => [
                'required',
                'max:100',
                function ($attribute, $value, $fail) use ($id) {
                    // Cari data dengan nama yang sama (hanya yang TIDAK soft deleted)
                    $query = self::where('bd_nama_submenu', $value)
                        ->where('isDeleted', 0);

                    // Jika sedang update, kecualikan ID saat ini
                    if ($id) {
                        $query->where('berita_dinamis_id', '!=', $id);
                    }

                    $existingData = $query->first();

                    if ($existingData) {
                        $fail('Nama submenu berita sudah digunakan');
                    }
                }
            ],
        ];

        $messages = [
            'bd_nama_submenu.required' => 'Nama submenu berita wajib diisi',
            'bd_nama_submenu.max' => 'Nama submenu berita maksimal 100 karakter',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return true;
    }
}