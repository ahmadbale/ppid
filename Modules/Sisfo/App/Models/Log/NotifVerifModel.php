<?php

namespace Modules\Sisfo\App\Models\Log;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Route;
use Modules\Sisfo\App\Models\SistemInformasi\EForm\PengaduanMasyarakatModel;
use Modules\Sisfo\App\Models\SistemInformasi\EForm\PermohonanInformasiModel;
use Modules\Sisfo\App\Models\SistemInformasi\EForm\PermohonanPerawatanModel;
use Modules\Sisfo\App\Models\SistemInformasi\EForm\PernyataanKeberatanModel;
use Modules\Sisfo\App\Models\SistemInformasi\EForm\WBSModel;

class NotifVerifModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'log_notif_verif';
    protected $primaryKey = 'notif_verif_id';
    public $timestamps = false;
    protected $fillable = [
        'notif_verif_kategori',
        'notif_verif_form_id',
        'notif_verif_pesan',
        'notif_verif_dibaca_oleh',
        'notif_verif_dibaca_tgl',
        'isDeleted',
        'created_at',
        'deleted_by',
        'deleted_at'
    ];

    // Relasi dengan Permohonan Informasi
    public function t_permohonan_informasi()
    {
        return $this->belongsTo(PermohonanInformasiModel::class, 'notif_verif_form_id', 'permohonan_informasi_id');
    }

    public function t_pernyataan_keberatan()
    {
        return $this->belongsTo(PernyataanKeberatanModel::class, 'notif_verif_form_id', 'pernyataan_keberatan_id');
    }

    public function t_pengaduan_masyarakat()
    {
        return $this->belongsTo(PengaduanMasyarakatModel::class, 'notif_verif_form_id', 'pengaduan_masyarakat_id');
    }

    public function t_wbs()
    {
        return $this->belongsTo(WBSModel::class, 'notif_verif_form_id', 'wbs_id');
    }

    public function t_permohonan_perawatan()
    {
        return $this->belongsTo(PermohonanPerawatanModel::class, 'notif_verif_form_id', 'permohonan_perawatan_id');
    }

    public static function createData($formId, $message, $kategori = null)
    {
        // Jika kategori tidak diberikan, tentukan dari route
        if ($kategori === null) {
            $currentRoute = Route::currentRouteName() ?? Route::current()->uri();
            $kategori = self::menentukanKategoriForm($currentRoute);
        }

        return self::create([
            'notif_verif_kategori' => $kategori,
            'notif_verif_form_id' => $formId,
            'notif_verif_pesan' => $message,
            'created_at' => now(),
        ]);
    }

    private static function menentukanKategoriForm($route)
    {
        $categoryMappings = [
            'permohonan-informasi' => 'E-Form Permohonan Informasi',
            'pernyataan-keberatan' => 'E-Form Pernyataan Keberatan',
            'pengaduan-masyarakat' => 'E-Form Pengaduan Masyarakat',
            'whistle-blowing-system' => 'E-Form Whistle Blowing System',
            'permohonan-perawatan' => 'E-Form Permohonan Perawatan Sarana Prasarana',
            'wbs' => 'E-Form Whistle Blowing System',
        ];
        
        foreach ($categoryMappings as $keyword => $category) {
            if (str_contains($route, $keyword)) {
                return $category;
            }
        }
        
        return 'Notification';
    }

    public static function tandaiDibaca($id)
    {
        $notifikasi = self::findOrFail($id);
        
        $userName = auth()->user()->pengguna_nama ?? session('nama') ?? 'System';
        $hakAksesNama = auth()->user()->level->hak_akses_nama ?? session('level_nama') ?? 'Unknown';
        $dibacaOleh = "{$userName} [{$hakAksesNama}]";
        
        $notifikasi->update([
            'notif_verif_dibaca_oleh' => $dibacaOleh,
            'notif_verif_dibaca_tgl' => now(),
        ]);

        return $notifikasi;
    }

    public static function hapusNotifikasi($id)
    {
        $notifikasi = self::findOrFail($id);
        
        $userName = auth()->user()->pengguna_nama ?? session('nama') ?? 'System';
        $hakAksesNama = auth()->user()->level->hak_akses_nama ?? session('level_nama') ?? 'Unknown';
        $deletedBy = "{$userName} [{$hakAksesNama}]";
        
        $notifikasi->update([
            'isDeleted' => 1,
            'deleted_by' => $deletedBy,
            'deleted_at' => now(),
        ]);

        return $notifikasi;
    }
    
    public static function tandaiSemuaDibaca()
    {
        $userId = auth()->user()->user_id ?? null;
        
        if (!$userId) {
            throw new \Exception('User tidak terautentikasi');
        }

        $userName = auth()->user()->pengguna_nama ?? session('nama') ?? 'System';
        $hakAksesNama = auth()->user()->level->hak_akses_nama ?? session('level_nama') ?? 'Unknown';
        $dibacaOleh = "{$userName} [{$hakAksesNama}]";

        $updated = self::whereNull('notif_verif_dibaca_tgl')
            ->where('isDeleted', 0)
            ->update([
                'notif_verif_dibaca_oleh' => $dibacaOleh,
                'notif_verif_dibaca_tgl' => now(),
            ]);

        return $updated;
    }

    public static function hapusSemuaDibaca()
    {
        $userId = auth()->user()->user_id ?? null;
        
        if (!$userId) {
            throw new \Exception('User tidak terautentikasi');
        }

        $userName = auth()->user()->pengguna_nama ?? session('nama') ?? 'System';
        $hakAksesNama = auth()->user()->level->hak_akses_nama ?? session('level_nama') ?? 'Unknown';
        $deletedBy = "{$userName} [{$hakAksesNama}]";

        $updated = self::whereNotNull('notif_verif_dibaca_tgl')
            ->where('isDeleted', 0)
            ->update([
                'isDeleted' => 1,
                'deleted_by' => $deletedBy,
                'deleted_at' => now(),
            ]);

        return $updated;
    }

    public static function tandaiSemuaDibacaByKategori($kategori)
    {
        $kategoriMap = [
            1 => 'E-Form Permohonan Informasi',
            2 => 'E-Form Pernyataan Keberatan',
            3 => 'E-Form Pengaduan Masyarakat',
            4 => 'E-Form Whistle Blowing System',
            5 => 'E-Form Permohonan Perawatan Sarana Prasarana',
        ];

        $kategoriId = (int)$kategori;
        $kategoriNama = $kategoriMap[$kategoriId] ?? null;

        if (!$kategoriNama) {
            throw new \Exception('Kategori tidak valid');
        }

        $userId = auth()->user()->user_id ?? null;
        
        if (!$userId) {
            throw new \Exception('User tidak terautentikasi');
        }

        $userName = auth()->user()->pengguna_nama ?? session('nama') ?? 'System';
        $hakAksesNama = auth()->user()->level->hak_akses_nama ?? session('level_nama') ?? 'Unknown';
        $dibacaOleh = "{$userName} [{$hakAksesNama}]";

        $updated = self::where('notif_verif_kategori', $kategoriNama)
            ->whereNull('notif_verif_dibaca_tgl')
            ->where('isDeleted', 0)
            ->update([
                'notif_verif_dibaca_oleh' => $dibacaOleh,
                'notif_verif_dibaca_tgl' => now(),
            ]);

        return $updated;
    }

    public static function hapusSemuaDibacaByKategori($kategori)
    {
        $kategoriMap = [
            1 => 'E-Form Permohonan Informasi',
            2 => 'E-Form Pernyataan Keberatan',
            3 => 'E-Form Pengaduan Masyarakat',
            4 => 'E-Form Whistle Blowing System',
            5 => 'E-Form Permohonan Perawatan Sarana Prasarana',
        ];

        $kategoriId = (int)$kategori;
        $kategoriNama = $kategoriMap[$kategoriId] ?? null;

        if (!$kategoriNama) {
            throw new \Exception('Kategori tidak valid');
        }

        $userId = auth()->user()->user_id ?? null;
        
        if (!$userId) {
            throw new \Exception('User tidak terautentikasi');
        }

        $userName = auth()->user()->pengguna_nama ?? session('nama') ?? 'System';
        $hakAksesNama = auth()->user()->level->hak_akses_nama ?? session('level_nama') ?? 'Unknown';
        $deletedBy = "{$userName} [{$hakAksesNama}]";

        $updated = self::where('notif_verif_kategori', $kategoriNama)
            ->whereNotNull('notif_verif_dibaca_tgl')
            ->where('isDeleted', 0)
            ->update([
                'isDeleted' => 1,
                'deleted_by' => $deletedBy,
                'deleted_at' => now(),
            ]);

        return $updated;
    }
}
