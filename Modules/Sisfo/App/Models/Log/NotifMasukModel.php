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

class NotifMasukModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'log_notif_masuk';
    protected $primaryKey = 'notif_masuk_id';
    public $timestamps = false;
    protected $fillable = [
        'notif_masuk_kategori',
        'notif_masuk_form_id',
        'notif_masuk_pesan',
        'notif_masuk_dibaca_oleh',
        'notif_masuk_dibaca_tgl',
        'isDeleted',
        'created_at',
        'deleted_by',
        'deleted_at'
    ];

    // Relasi dengan Permohonan Informasi
    public function t_permohonan_informasi()
    {
        return $this->belongsTo(PermohonanInformasiModel::class, 'notif_masuk_form_id', 'permohonan_informasi_id');
    }

    public function t_pernyataan_keberatan()
    {
        return $this->belongsTo(PernyataanKeberatanModel::class, 'notif_masuk_form_id', 'pernyataan_keberatan_id');
    }

    public function t_pengaduan_masyarakat()
    {
        return $this->belongsTo(PengaduanMasyarakatModel::class, 'notif_masuk_form_id', 'pengaduan_masyarakat_id');
    }

    public function t_wbs()
    {
        return $this->belongsTo(WBSModel::class, 'notif_masuk_form_id', 'wbs_id');
    }

    public function t_permohonan_perawatan()
    {
        return $this->belongsTo(PermohonanPerawatanModel::class, 'notif_masuk_form_id', 'permohonan_perawatan_id');
    }

    public static function createData($formId, $message, $kategori = null)
    {
        // Jika kategori tidak diberikan, tentukan dari route
        if ($kategori === null) {
            $currentRoute = Route::currentRouteName() ?? Route::current()->uri();
            $kategori = self::menentukanKategoriForm($currentRoute);
        }

        return self::create([
            'notif_masuk_kategori' => $kategori,
            'notif_masuk_form_id' => $formId,
            'notif_masuk_pesan' => $message,
            'created_at' => now(),
        ]);
    }

    private static function menentukanKategoriForm($route)
    {
        // Memetakan kata kunci rute ke nama kategori masing-masing
        $categoryMappings = [
            // Pemetaan segmen URL
            'permohonan-informasi' => 'E-Form Permohonan Informasi',
            'pernyataan-keberatan' => 'E-Form Pernyataan Keberatan',
            'pengaduan-masyarakat' => 'E-Form Pengaduan Masyarakat',
            'whistle-blowing-system' => 'E-Form Whistle Blowing System',
            'permohonan-sarana-dan-prasarana' => 'E-Form Permohonan Perawatan Sarana Prasarana',
        ];
        
        // Periksa setiap pemetaan terhadap rute
        foreach ($categoryMappings as $keyword => $category) {
            if (strpos($route, $keyword) !== false) {
                return $category;
            }
        }
        
        // Pengembalian default jika tidak ada kecocokan
        return 'Notification';
    }

    public static function tandaiDibaca($id)
    {
        $notifikasi = self::findOrFail($id);
        
        // Get user info dengan format: "Nama [Hak Akses]"
        $userName = auth()->user()->pengguna_nama ?? session('nama') ?? 'System';
        $hakAksesNama = auth()->user()->level->hak_akses_nama ?? session('level_nama') ?? 'Unknown';
        $dibacaOleh = "{$userName} [{$hakAksesNama}]";
        
        $notifikasi->notif_masuk_dibaca_oleh = $dibacaOleh;
        $notifikasi->notif_masuk_dibaca_tgl = now();
        $notifikasi->save();

        return [
            'success' => true,
            'message' => 'Notifikasi berhasil ditandai telah dibaca'
        ];
    }

    public static function hapusNotifikasi($id)
    {
        $notifikasi = self::findOrFail($id);

        // Cek apakah notifikasi sudah dibaca
        if (!$notifikasi->notif_masuk_dibaca_tgl) {
            return [
                'success' => false,
                'message' => 'Notifikasi harus ditandai dibaca terlebih dahulu sebelum dihapus'
            ];
        }

        // Get user info dengan format: "Nama [Hak Akses]"
        $userName = auth()->user()->pengguna_nama ?? session('nama') ?? 'System';
        $hakAksesNama = auth()->user()->level->hak_akses_nama ?? session('level_nama') ?? 'Unknown';
        $deletedBy = "{$userName} [{$hakAksesNama}]";

        $notifikasi->isDeleted = 1;
        $notifikasi->deleted_at = now();
        $notifikasi->deleted_by = $deletedBy;
        $notifikasi->save();

        return [
            'success' => true,
            'message' => 'Notifikasi berhasil dihapus'
        ];
    }
    
    public static function tandaiSemuaDibaca()
    {
        $notifikasi = self::where('notif_masuk_kategori', 'E-Form Permohonan Informasi')
            ->where('isDeleted', 0)
            ->whereNull('notif_masuk_dibaca_tgl')
            ->get();

        if ($notifikasi->isEmpty()) {
            return [
                'success' => false,
                'message' => 'Tidak ada notifikasi yang dapat ditandai.'
            ];
        }

        // Get user info dengan format: "Nama [Hak Akses]"
        $userName = auth()->user()->pengguna_nama ?? session('nama') ?? 'System';
        $hakAksesNama = auth()->user()->level->hak_akses_nama ?? session('level_nama') ?? 'Unknown';
        $dibacaOleh = "{$userName} [{$hakAksesNama}]";

        foreach ($notifikasi as $item) {
            $item->notif_masuk_dibaca_oleh = $dibacaOleh;
            $item->notif_masuk_dibaca_tgl = now();
            $item->save();
        }

        return [
            'success' => true,
            'message' => 'Semua notifikasi berhasil ditandai telah dibaca'
        ];
    }

    public static function hapusSemuaDibaca()
    {
        $notifikasi = self::where('notif_masuk_kategori', 'E-Form Permohonan Informasi')
            ->where('isDeleted', 0)
            ->whereNotNull('notif_masuk_dibaca_tgl')
            ->get();

        if ($notifikasi->isEmpty()) {
            return [
                'success' => false,
                'message' => 'Tidak ada Notifikasi yang telah dibaca. Anda harus menandai notifikasi dengan "Tandai telah dibaca" terlebih dahulu.'
            ];
        }

        // Get user info dengan format: "Nama [Hak Akses]"
        $userName = auth()->user()->pengguna_nama ?? session('nama') ?? 'System';
        $hakAksesNama = auth()->user()->level->hak_akses_nama ?? session('level_nama') ?? 'Unknown';
        $deletedBy = "{$userName} [{$hakAksesNama}]";

        foreach ($notifikasi as $item) {
            $item->isDeleted = 1;
            $item->deleted_at = now();
            $item->deleted_by = $deletedBy;
            $item->save();
        }

        return [
            'success' => true,
            'message' => 'Semua notifikasi yang telah dibaca berhasil dihapus'
        ];
    }

    // ✅ NEW: Method untuk tandai semua dibaca by kategori
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
            return [
                'success' => false,
                'message' => 'Kategori tidak valid'
            ];
        }

        $notifikasi = self::where('notif_masuk_kategori', $kategoriNama)
            ->where('isDeleted', 0)
            ->whereNull('notif_masuk_dibaca_tgl')
            ->get();

        if ($notifikasi->isEmpty()) {
            return [
                'success' => false,
                'message' => 'Tidak ada notifikasi yang dapat ditandai.'
            ];
        }

        // Get user info dengan format: "Nama [Hak Akses]"
        $userName = auth()->user()->pengguna_nama ?? session('nama') ?? 'System';
        $hakAksesNama = auth()->user()->level->hak_akses_nama ?? session('level_nama') ?? 'Unknown';
        $dibacaOleh = "{$userName} [{$hakAksesNama}]";

        foreach ($notifikasi as $item) {
            $item->notif_masuk_dibaca_oleh = $dibacaOleh;
            $item->notif_masuk_dibaca_tgl = now();
            $item->save();
        }

        return [
            'success' => true,
            'message' => 'Semua notifikasi berhasil ditandai telah dibaca'
        ];
    }

    // ✅ NEW: Method untuk hapus semua yang sudah dibaca by kategori
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
            return [
                'success' => false,
                'message' => 'Kategori tidak valid'
            ];
        }

        $notifikasi = self::where('notif_masuk_kategori', $kategoriNama)
            ->where('isDeleted', 0)
            ->whereNotNull('notif_masuk_dibaca_tgl')
            ->get();

        if ($notifikasi->isEmpty()) {
            return [
                'success' => false,
                'message' => 'Tidak ada Notifikasi yang telah dibaca. Anda harus menandai notifikasi dengan "Tandai telah dibaca" terlebih dahulu.'
            ];
        }

        // Get user info dengan format: "Nama [Hak Akses]"
        $userName = auth()->user()->pengguna_nama ?? session('nama') ?? 'System';
        $hakAksesNama = auth()->user()->level->hak_akses_nama ?? session('level_nama') ?? 'Unknown';
        $deletedBy = "{$userName} [{$hakAksesNama}]";

        foreach ($notifikasi as $item) {
            $item->isDeleted = 1;
            $item->deleted_at = now();
            $item->deleted_by = $deletedBy;
            $item->save();
        }

        return [
            'success' => true,
            'message' => 'Semua notifikasi yang telah dibaca berhasil dihapus'
        ];
    }
}
