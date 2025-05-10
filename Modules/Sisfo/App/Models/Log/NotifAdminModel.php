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

class NotifAdminModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'log_notif_admin';
    protected $primaryKey = 'notif_admin_id';
    public $timestamps = false;
    protected $fillable = [
        'kategori_notif_admin',
        'notif_admin_form_id',
        'pesan_notif_admin',
        'sudah_dibaca_notif_admin',
        'isDeleted',
        'created_at',
        'deleted_by',
        'deleted_at'
    ];

    // Relasi dengan Permohonan Informasi
    public function t_permohonan_informasi()
    {
        return $this->belongsTo(PermohonanInformasiModel::class, 'notif_admin_form_id', 'permohonan_informasi_id');
    }

    public function t_pernyataan_keberatan()
    {
        return $this->belongsTo(PernyataanKeberatanModel::class, 'notif_admin_form_id', 'pernyataan_keberatan_id');
    }

    public function t_pengaduan_masyarakat()
    {
        return $this->belongsTo(PengaduanMasyarakatModel::class, 'notif_admin_form_id', 'pengaduan_masyarakat_id');
    }

    public function t_wbsi()
    {
        return $this->belongsTo(WBSModel::class, 'notif_admin_form_id', 'wbs_id');
    }

    public function t_permohonan_perawatan()
    {
        return $this->belongsTo(PermohonanPerawatanModel::class, 'notif_admin_form_id', 'permohonan_perawatan_id');
    }

    public static function createData($formId, $message)
    {
        $currentRoute = Route::currentRouteName() ?? Route::current()->uri();
        $kategori = self::menentukanKategoriForm($currentRoute);

        return self::create([
            'kategori_notif_admin' => $kategori,
            'notif_admin_form_id' => $formId,
            'pesan_notif_admin' => $message,
            'created_at' => now(),
            'isDeleted' => 0
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
        $notifikasi->sudah_dibaca_notif_admin = now();
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
        if (!$notifikasi->sudah_dibaca_notif_admin) {
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
            'message' => 'Notifikasi berhasil dihapus'
        ];
    }
    public static function tandaiSemuaDibaca()
    {
        $notifikasi = self::where('kategori_notif_admin', 'E-Form Permohonan Informasi')
            ->where('isDeleted', 0)
            ->whereNull('sudah_dibaca_notif_admin')
            ->get();

        if ($notifikasi->isEmpty()) {
            return [
                'success' => false,
                'message' => 'Tidak ada notifikasi yang dapat ditandai.'
            ];
        }

        foreach ($notifikasi as $item) {
            $item->sudah_dibaca_notif_admin = now();
            $item->save();
        }

        return [
            'success' => true,
            'message' => 'Semua notifikasi berhasil ditandai telah dibaca'
        ];
    }

    public static function hapusSemuaDibaca()
    {
        $notifikasi = self::where('kategori_notif_admin', 'E-Form Permohonan Informasi')
            ->where('isDeleted', 0)
            ->whereNotNull('sudah_dibaca_notif_admin')
            ->get();

        if ($notifikasi->isEmpty()) {
            return [
                'success' => false,
                'message' => 'Tidak ada Notifikasi yang telah dibaca. Anda harus menandai notifikasi dengan "Tandai telah dibaca" terlebih dahulu.'
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
            'message' => 'Semua notifikasi yang telah dibaca berhasil dihapus'
        ];
    }
}
