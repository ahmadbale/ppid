<?php

namespace Modules\Sisfo\App\Models\Website\InformasiPublik\LHKPN;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Modules\Sisfo\App\Models\TraitsModel;

class LHKPNModel extends Model
{
    use TraitsModel;

    protected $table = 'm_lhkpn';
    protected $primaryKey = 'lhkpn_id';
    protected $fillable = [
        'lhkpn_tahun',
        'lhkpn_judul_informasi',
        'lhkpn_deskripsi_informasi',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable = array_merge($this->fillable, $this->getCommonFields());
    }

    public static function selectData($tahun = null, $page = 1, $perPage = 5, $limitKaryawan = 5)
    {
        // Query dasar untuk tabel LHKPN
        $query = self::where('isDeleted', 0)
            ->select('lhkpn_id', 'lhkpn_tahun', 'lhkpn_judul_informasi', 'lhkpn_deskripsi_informasi')
            ->orderBy('lhkpn_tahun', 'desc');
        
        // Filter berdasarkan tahun jika parameter tahun diberikan
        if ($tahun !== null) {
            $query->where('lhkpn_tahun', $tahun);
        }
        
        // Hitung total data untuk pagination
        $totalData = $query->count();
        $totalPages = ceil($totalData / $perPage);
        
        // Ambil data LHKPN dengan pagination
        $offset = ($page - 1) * $perPage;
        $dataLhkpn = $query->skip($offset)->take($perPage)->get();
        
        // Dapatkan semua ID LHKPN untuk query detail
        $lhkpnIds = $dataLhkpn->pluck('lhkpn_id')->toArray();
        
        // Array untuk menyimpan jumlah total karyawan per LHKPN
        $totalKaryawanPerLhkpn = [];
        
        // Hitung total karyawan untuk setiap LHKPN
        $karyawanCounts = DetailLhkpnModel::whereIn('fk_m_lhkpn', $lhkpnIds)
            ->where('isDeleted', 0)
            ->select('fk_m_lhkpn', DB::raw('count(*) as total'))
            ->groupBy('fk_m_lhkpn')
            ->get();
        
        foreach ($karyawanCounts as $count) {
            $totalKaryawanPerLhkpn[$count->fk_m_lhkpn] = $count->total;
        }
        
        // Ambil detail LHKPN dengan limit per LHKPN
        $detailsByLhkpnId = [];
        
        foreach ($lhkpnIds as $lhkpnId) {
            $details = DetailLhkpnModel::where('fk_m_lhkpn', $lhkpnId)
                ->where('isDeleted', 0)
                ->select('detail_lhkpn_id', 'fk_m_lhkpn', 'dl_nama_karyawan', 'dl_file_lhkpn')
                ->orderBy('dl_nama_karyawan')
                ->limit($limitKaryawan)
                ->get()
                ->map(function ($detail) {
                    return [
                        'id' => $detail->detail_lhkpn_id,
                        'nama_karyawan' => $detail->dl_nama_karyawan,
                        'file' => $detail->dl_file_lhkpn ? asset('storage/lhkpn/' . $detail->dl_file_lhkpn) : null
                    ];
                })->toArray();
            
            $detailsByLhkpnId[$lhkpnId] = $details;
        }
        
        // Format hasil akhir
        $data = $dataLhkpn->map(function ($lhkpn) use ($detailsByLhkpnId, $totalKaryawanPerLhkpn) {
            $lhkpnId = $lhkpn->lhkpn_id;
            $totalKaryawan = $totalKaryawanPerLhkpn[$lhkpnId] ?? 0;
            $hasMoreKaryawan = $totalKaryawan > count($detailsByLhkpnId[$lhkpnId]);
            
            return [
                'id' => $lhkpnId,
                'tahun' => $lhkpn->lhkpn_tahun,
                'judul' => $lhkpn->lhkpn_judul_informasi,
                'deskripsi' => $lhkpn->lhkpn_deskripsi_informasi,
                'details' => $detailsByLhkpnId[$lhkpnId] ?? [],
                'total_karyawan' => $totalKaryawan,
                'has_more' => $hasMoreKaryawan
            ];
        })->toArray();
        
        // Tambahkan informasi pagination
        $result = [
            'data' => $data,
            'pagination' => [
                'total_data' => $totalData,
                'per_page' => $perPage,
                'current_page' => $page,
                'total_pages' => $totalPages,
                'has_next' => $page < $totalPages,
                'has_prev' => $page > 1
            ]
        ];
        
        return $result;
    }

    public static function createData()
    {
      //
    }

    public static function updateData()
    {
        //
    }

    public static function deleteData()
    {
        //
    }

    public static function validasiData()
    {
        //
    }
}