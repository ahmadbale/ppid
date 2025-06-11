<?php
// filepath: c:\laragon\www\PPID-polinema\Modules\Sisfo\App\Models\Log\QRCodeWAModel.php

namespace Modules\Sisfo\App\Models\Log;

use Modules\Sisfo\App\Models\TraitsModel;
use Illuminate\Database\Eloquent\Model;
use Modules\Sisfo\App\Models\User\UserModel;

class QRCodeWAModel extends Model
{
    use TraitsModel;

    protected $table = 'log_qrcode_wa';
    protected $primaryKey = 'log_qrcode_wa_id';
    
    protected $fillable = [
        'log_qrcode_wa_nomor_pengirim', // Perbaiki nama field
        'log_qrcode_wa_user_scan',
        'log_qrcode_wa_ha_scan',
        'log_qrcode_wa_tanggal_scan',
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
                  ->orderBy('log_qrcode_wa_id', 'desc')
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
            'log_qrcode_wa_nomor_pengirim' => $nomorPengguna, // Perbaiki nama field
            'log_qrcode_wa_user_scan' => $userScan,
            'log_qrcode_wa_ha_scan' => $haScan,
            'log_qrcode_wa_tanggal_scan' => date('Y-m-d H:i:s'),
            'isDeleted' => 0,
            'deleted_at' => null
        ]);
    }
}