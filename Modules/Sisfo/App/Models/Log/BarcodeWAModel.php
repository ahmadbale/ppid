<?php
// filepath: c:\laragon\www\PPID-polinema\Modules\Sisfo\App\Models\Log\BarcodeWaModel.php

namespace Modules\Sisfo\App\Models\Log;

use Modules\Sisfo\App\Models\TraitsModel;
use Illuminate\Database\Eloquent\Model;
use Modules\Sisfo\App\Models\User\UserModel;

class BarcodeWAModel extends Model
{
    use TraitsModel;

    protected $table = 'log_barcode_wa';
    protected $primaryKey = 'log_barcode_wa_id';
    
    protected $fillable = [
        'log_barcode_wa_nomor_pengguna', // Perbaiki nama field
        'log_barcode_wa_user_scan',
        'log_barcode_wa_ha_scan',
        'log_barcode_wa_tanggal_scan',
        'isDeleted',
        'deleted_at'
    ];

    // Disable timestamps karena kita menggunakan custom timestamp
    public $timestamps = false;

    /**
     * Get latest active barcode scan log
     */
    public static function getLatestActiveScan()
    {
        return self::where('isDeleted', 0)
                  ->orderBy('log_barcode_wa_id', 'desc')
                  ->first();
    }

    /**
     * Mark all active scans as deleted
     */
    public static function markAllAsDeleted()
    {
        return self::where('isDeleted', 0)
                  ->update([
                      'isDeleted' => 1,
                      'deleted_at' => date('Y-m-d H:i:s')
                  ]);
    }

    /**
     * Create new barcode scan log
     */
    public static function createScanLog($nomorPengguna, $userScan, $haScan)
    {
        return self::create([
            'log_barcode_wa_nomor_pengguna' => $nomorPengguna, // Perbaiki nama field
            'log_barcode_wa_user_scan' => $userScan,
            'log_barcode_wa_ha_scan' => $haScan,
            'log_barcode_wa_tanggal_scan' => date('Y-m-d H:i:s'),
            'isDeleted' => 0,
            'deleted_at' => null
        ]);
    }
}