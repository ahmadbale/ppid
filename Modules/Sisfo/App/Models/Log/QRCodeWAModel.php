<?php

namespace Modules\Sisfo\App\Models\Log;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class QRCodeWAModel extends Model
{
    protected $table = 'log_qrcode_wa';
    protected $primaryKey = 'log_qrcode_wa_id';

    protected $fillable = [
        'log_qrcode_wa_nomor_pengirim',
        'log_qrcode_wa_user_scan',
        'log_qrcode_wa_ha_scan',
        'log_qrcode_wa_tanggal_scan',
        'isDeleted',
        'deleted_at'
    ];

    // Disable timestamps karena kita menggunakan custom timestamp
    public $timestamps = false;

    /**
     * Get latest active QR code scan log
     */
    public static function getLatestActiveScan()
    {
        try {
            return self::where('isDeleted', 0)
                ->orderBy('log_qrcode_wa_id', 'desc')
                ->first();
        } catch (\Exception $e) {
            Log::error('Error getting latest active scan: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Mark all active scans as deleted (untuk reset session)
     */
    public static function markAllAsDeleted()
    {
        try {
            $result = self::where('isDeleted', 0)
                ->update([
                    'isDeleted' => 1,
                    'deleted_at' => date('Y-m-d H:i:s') // Format database
                ]);

            Log::info('Marked ' . $result . ' scan logs as deleted');
            return $result;
        } catch (\Exception $e) {
            Log::error('Error marking scans as deleted: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Create new QR code scan log
     */
    public static function createScanLog($nomorPengirim, $userScan, $haScan)
    {
        try {
            // Mark previous scans as deleted first
            self::markAllAsDeleted();

            $data = [
                'log_qrcode_wa_nomor_pengirim' => $nomorPengirim,
                'log_qrcode_wa_user_scan' => $userScan,
                'log_qrcode_wa_ha_scan' => $haScan,
                'log_qrcode_wa_tanggal_scan' => date('Y-m-d H:i:s'), // Format database
                'isDeleted' => 0,
                'deleted_at' => null
            ];

            Log::info('Creating scan log with data: ' . json_encode($data));

            $scanLog = self::create($data);

            if ($scanLog) {
                Log::info('Scan log created successfully with ID: ' . $scanLog->log_qrcode_wa_id);
                return $scanLog;
            }

            Log::error('Failed to create scan log - no object returned');
            return null;
        } catch (\Exception $e) {
            Log::error('Error creating scan log: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return null;
        }
    }

    /**
     * Check if there's an active scan
     */
    public static function hasActiveScan()
    {
        try {
            return self::where('isDeleted', 0)->exists();
        } catch (\Exception $e) {
            Log::error('Error checking active scan: ' . $e->getMessage());
            return false;
        }
    }
}
