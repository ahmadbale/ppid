<?php

namespace Modules\Sisfo\App\Models\Website\InformasiPublik\LHKPN;

use Modules\Sisfo\App\Models\Log\TransactionModel;
use Modules\Sisfo\App\Models\TraitsModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class LhkpnModel extends Model
{
    use TraitsModel;

    protected $table = 'm_lhkpn';
    protected $primaryKey = 'lhkpn_id';
    protected $fillable = [
        'lhkpn_tahun',
        'lhkpn_judul_informasi',
        'lhkpn_deskripsi_informasi'
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable = array_merge($this->fillable, $this->getCommonFields());
    }

    public static function getDataLhkpn($per_page = 10, $tahun = null, $detail_page = [])
    {
        $query = DB::table('m_lhkpn as ml')
            ->select([
                'ml.lhkpn_id',
                'ml.lhkpn_tahun',
                'ml.lhkpn_judul_informasi',
                'ml.lhkpn_deskripsi_informasi',
                'ml.updated_at'  // Tambahkan updated_at dari tabel utama
            ])
            ->where('ml.isDeleted', 0);

        // Filter tahun jika disediakan
        if ($tahun !== null) {
            $query->where('ml.lhkpn_tahun', $tahun);
        }

        $arr_data = $query->orderBy('ml.lhkpn_id', 'DESC')
            ->paginate($per_page);

        // Transformasi data
        $transformedData = collect($arr_data->items())->map(function ($item) use ($detail_page) {
            $tahun = $item->lhkpn_tahun;
            $currentPage = isset($detail_page[$tahun]) ? (int)$detail_page[$tahun] : 1;
            $perDetailPage = 10;
            $offset = ($currentPage - 1) * $perDetailPage;

            // Subquery untuk mencari updated_at terbaru dari detail
            $latestDetailUpdate = DB::table('t_detail_lhkpn')
                ->where('fk_m_lhkpn', $item->lhkpn_id)
                ->where('isDeleted', 0)
                ->max('updated_at');

            $detailQuery = DB::table('t_detail_lhkpn')
                ->select([
                    'detail_lhkpn_id',
                    'dl_nama_karyawan',
                    'dl_file_lhkpn',
                    'updated_at'
                ])
                ->where('fk_m_lhkpn', $item->lhkpn_id)
                ->where('isDeleted', 0)
                ->orderBy('dl_nama_karyawan');

            $totalDetails = $detailQuery->count();

            $details = $detailQuery
                ->offset($offset)
                ->limit($perDetailPage)
                ->get()
                ->map(function ($row) {
                    return [
                        'id' => $row->detail_lhkpn_id,
                        'nama_karyawan' => $row->dl_nama_karyawan,
                        'file' => $row->dl_file_lhkpn ? asset('storage/' . $row->dl_file_lhkpn) : null,
                        'updated_at' => $row->updated_at
                            ? \Carbon\Carbon::parse($row->updated_at)->format('d F Y, H:i')
                            : null,
                    ];
                })->toArray();

            $totalDetailPages = ceil($totalDetails / $perDetailPage);

            return [
                'id' => $item->lhkpn_id,
                'tahun' => $item->lhkpn_tahun,
                'judul' => $item->lhkpn_judul_informasi,
                'deskripsi' => $item->lhkpn_deskripsi_informasi,
                'updated_at' => $latestDetailUpdate
                    ? \Carbon\Carbon::parse($latestDetailUpdate)->format('d F Y, H:i')
                    : (
                        $item->updated_at
                        ? \Carbon\Carbon::parse($item->updated_at)->format('d F Y, H:i')
                        : null
                    ),
                'details' => $details,
                'detail_pagination' => [
                    'current_page' => $currentPage,
                    'total_pages' => $totalDetailPages,
                    'per_page' => $perDetailPage,
                    'total_items' => $totalDetails,
                    'next_page_url' => $currentPage < $totalDetailPages ? url()->current() . '?detail_page[' . $tahun . ']=' . ($currentPage + 1) : null,
                    'prev_page_url' => $currentPage > 1 ? url()->current() . '?detail_page[' . $tahun . ']=' . ($currentPage - 1) : null,
                ]
            ];
        });

        // Format response pagination
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
    public static function selectData($perPage = null, $search = '')
    {
        $query = self::query()
            ->where('isDeleted', 0);

        // Tambahkan fungsionalitas pencarian
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('lhkpn_tahun', 'like', "%{$search}%")
                    ->orWhere('lhkpn_judul_informasi', 'like', "%{$search}%");
            });
        }


        return self::paginateResults($query, $perPage);
    }

    public static function createData($request)
    {
        try {
            DB::beginTransaction();

            $data = $request->m_lhkpn;
            $lhkpn = self::create($data);

            TransactionModel::createData(
                'CREATED',
                $lhkpn->lhkpn_id,
                $lhkpn->lhkpn_judul_informasi
            );

            DB::commit();

            return self::responFormatSukses($lhkpn, 'Data LHKPN berhasil dibuat');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal membuat data LHKPN');
        }
    }

    public static function updateData($request, $id)
    {
        try {
            DB::beginTransaction();

            $lhkpn = self::findOrFail($id);

            $data = $request->m_lhkpn;
            $lhkpn->update($data);

            TransactionModel::createData(
                'UPDATED',
                $lhkpn->lhkpn_id,
                $lhkpn->lhkpn_judul_informasi
            );

            DB::commit();

            return self::responFormatSukses($lhkpn, 'Data LHKPN berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal memperbarui data LHKPN');
        }
    }

    public static function deleteData($id)
    {
        try {
            DB::beginTransaction();

            $lhkpn = self::findOrFail($id);
            // Check if lhkpn is being used in detail lhkpn
            $isUsed = DetailLhkpnModel::where('fk_m_lhkpn', $id)
                ->where('isDeleted', 0)
                ->exists();

            if ($isUsed) {
                DB::rollBack();
                throw new \Exception('Maaf, LHKPN Tahun masih digunakan di tempat lain');
            }

            $lhkpn->delete();

            TransactionModel::createData(
                'DELETED',
                $lhkpn->lhkpn_id,
                $lhkpn->lhkpn_judul_informasi
            );

            DB::commit();

            return self::responFormatSukses($lhkpn, 'Data LHKPN berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal menghapus data LHKPN');
        }
    }

    public static function detailData($id)
    {
        return self::findOrFail($id);
    }

    public static function validasiData($request)
    {
        $rules = [
            'm_lhkpn.lhkpn_tahun' => 'required|max:4',
            'm_lhkpn.lhkpn_judul_informasi' => 'required|max:255',
            'm_lhkpn.lhkpn_deskripsi_informasi' => 'required',
        ];

        $messages = [
            'm_lhkpn.lhkpn_tahun.required' => 'Tahun LHKPN wajib diisi',
            'm_lhkpn.lhkpn_tahun.max' => 'Tahun LHKPN maksimal 4 karakter',
            'm_lhkpn.lhkpn_judul_informasi.required' => 'Judul informasi wajib diisi',
            'm_lhkpn.lhkpn_judul_informasi.max' => 'Judul informasi maksimal 255 karakter',
            'm_lhkpn.lhkpn_deskripsi_informasi.required' => 'Deskripsi informasi wajib diisi',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return true;
    }
}
