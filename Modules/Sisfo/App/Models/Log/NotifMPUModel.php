<?php

namespace Modules\Sisfo\App\Models\Log;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Route;
use Modules\Sisfo\App\Models\SistemInformasi\EForm\PermohonanInformasiModel;
use Modules\Sisfo\App\Models\SistemInformasi\EForm\PernyataanKeberatanModel;
use Modules\Sisfo\App\Models\SistemInformasi\EForm\PengaduanMasyarakatModel;
use Modules\Sisfo\App\Models\SistemInformasi\EForm\WBSModel;
use Modules\Sisfo\App\Models\SistemInformasi\EForm\PermohonanPerawatanModel;

class NotifMPUModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'log_notif_mpu';
    protected $primaryKey = 'notif_mpu_id';
    public $timestamps = false;
    protected $fillable = [
        'kategori_notif_mpu',
        'notif_mpu_form_id',
        'pesan_notif_mpu',
        'sudah_dibaca_notif_mpu',
        'isDeleted',
        'created_at',
        'deleted_by',
        'deleted_at'
    ];

    // Relasi dengan model form
    public function t_permohonan_informasi()
    {
        return $this->belongsTo(PermohonanInformasiModel::class, 'notif_mpu_form_id', 'permohonan_informasi_id');
    }

    public function t_pernyataan_keberatan()
    {
        return $this->belongsTo(PernyataanKeberatanModel::class, 'notif_mpu_form_id', 'pernyataan_keberatan_id');
    }

    public function t_pengaduan_masyarakat()
    {
        return $this->belongsTo(PengaduanMasyarakatModel::class, 'notif_mpu_form_id', 'pengaduan_masyarakat_id');
    }

    public function t_wbs()
    {
        return $this->belongsTo(WBSModel::class, 'notif_mpu_form_id', 'wbs_id');
    }

    public function t_permohonan_perawatan()
    {
        return $this->belongsTo(PermohonanPerawatanModel::class, 'notif_mpu_form_id', 'permohonan_perawatan_id');
    }

    public static function createData($formId, $message, $kategori = null)
    {
        // Jika kategori tidak diberikan, tentukan berdasarkan route
        if (!$kategori) {
            $currentRoute = Route::currentRouteName() ?? Route::current()->uri();
            $kategori = self::menentukanKategoriForm($currentRoute);
        }

        return self::create([
            'kategori_notif_mpu' => $kategori,
            'notif_mpu_form_id' => $formId,
            'pesan_notif_mpu' => $message,
            'created_at' => now(),
            'isDeleted' => 0
        ]);
    }

    private static function menentukanKategoriForm($route)
    {
        // Memetakan kata kunci rute ke nama kategori masing-masing
        $categoryMappings = [
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
        return 'Notification MPU';
    }

    public static function tandaiDibaca($id)
    {
        $notifikasi = self::findOrFail($id);
        $notifikasi->sudah_dibaca_notif_mpu = now();
        $notifikasi->save();

        return [
            'success' => true,
            'message' => 'Notifikasi MPU berhasil ditandai telah dibaca'
        ];
    }

    public static function hapusNotifikasi($id)
    {
        $notifikasi = self::findOrFail($id);

        // Cek apakah notifikasi sudah dibaca
        if (!$notifikasi->sudah_dibaca_notif_mpu) {
            return [
                'success' => false,
                'message' => 'Notifikasi harus ditandai dibaca terlebih dahulu sebelum dihapus'
            ];
        }

        $notifikasi->isDeleted = 1;
        $notifikasi->deleted_at = now();
        $notifikasi->deleted_by = session('alias') ?? 'System';
        $notifikasi->save();

        return [
            'success' => true,
            'message' => 'Notifikasi MPU berhasil dihapus'
        ];
    }

    public static function tandaiSemuaDibaca($kategori = 'E-Form Permohonan Informasi')
    {
        $notifikasi = self::where('kategori_notif_mpu', $kategori)
            ->where('isDeleted', 0)
            ->whereNull('sudah_dibaca_notif_mpu')
            ->get();

        if ($notifikasi->isEmpty()) {
            return [
                'success' => false,
                'message' => 'Tidak ada notifikasi MPU yang dapat ditandai.'
            ];
        }

        foreach ($notifikasi as $item) {
            $item->sudah_dibaca_notif_mpu = now();
            $item->save();
        }

        return [
            'success' => true,
            'message' => 'Semua notifikasi MPU berhasil ditandai telah dibaca'
        ];
    }

    public static function hapusSemuaDibaca($kategori = 'E-Form Permohonan Informasi')
    {
        $notifikasi = self::where('kategori_notif_mpu', $kategori)
            ->where('isDeleted', 0)
            ->whereNotNull('sudah_dibaca_notif_mpu')
            ->get();

        if ($notifikasi->isEmpty()) {
            return [
                'success' => false,
                'message' => 'Tidak ada Notifikasi MPU yang telah dibaca. Anda harus menandai notifikasi dengan "Tandai telah dibaca" terlebih dahulu.'
            ];
        }

        foreach ($notifikasi as $item) {
            $item->isDeleted = 1;
            $item->deleted_at = now();
            $item->deleted_by = session('alias') ?? 'System';
            $item->save();
        }

        return [
            'success' => true,
            'message' => 'Semua notifikasi MPU yang telah dibaca berhasil dihapus'
        ];
    }
}