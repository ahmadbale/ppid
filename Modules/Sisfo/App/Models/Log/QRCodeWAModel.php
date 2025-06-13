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
        'deleted_at',
        'is_confirmed',           // Tambah field baru
        'pending_confirmation',   // Tambah field baru
        'confirmation_expires_at' // Tambah field baru
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
     * Get pending confirmation scan
     */
    public static function getPendingConfirmationScan()
    {
        try {
            return self::where('isDeleted', 0)
                ->where('pending_confirmation', 1)
                ->where('is_confirmed', 0)
                ->where('confirmation_expires_at', '>', date('Y-m-d H:i:s'))
                ->orderBy('log_qrcode_wa_id', 'desc')
                ->first();
        } catch (\Exception $e) {
            Log::error('Error getting pending confirmation scan: ' . $e->getMessage());
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
                    'deleted_at' => date('Y-m-d H:i:s')
                ]);

            Log::info('Marked ' . $result . ' scan logs as deleted');
            return $result;
        } catch (\Exception $e) {
            Log::error('Error marking scans as deleted: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Create pending scan log (belum dikonfirmasi)
     */
    public static function createPendingScanLog($nomorPengirim)
    {
        try {
            // Mark previous scans as deleted first
            self::markAllAsDeleted();

            // Create expiration time (3 minutes from now)
            $expiresAt = date('Y-m-d H:i:s', strtotime('+3 minutes'));

            // VALIDASI: Pastikan data sesuai panjang kolom database
            $data = [
                'log_qrcode_wa_nomor_pengirim' => substr($nomorPengirim, 0, 20), // varchar(20)
                'log_qrcode_wa_user_scan' => 'Pending Confirmation', // varchar(255) - OK
                'log_qrcode_wa_ha_scan' => 'Pending', // varchar(50) - OK
                'log_qrcode_wa_tanggal_scan' => date('Y-m-d H:i:s'),
                'isDeleted' => 0,
                'deleted_at' => null,
                'is_confirmed' => 0,
                'pending_confirmation' => 1,
                'confirmation_expires_at' => $expiresAt
            ];

            Log::info('Creating pending scan log with data: ' . json_encode($data));

            $scanLog = self::create($data);

            if ($scanLog) {
                Log::info('Pending scan log created successfully with ID: ' . $scanLog->log_qrcode_wa_id);
                return $scanLog;
            }

            Log::error('Failed to create pending scan log - no object returned');
            return null;
        } catch (\Exception $e) {
            Log::error('Error creating pending scan log: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return null;
        }
    }

    /**
     * Confirm scan log with user data
     */
    public static function confirmScanLog($scanLogId, $userScan, $haScan)
    {
        try {
            $scanLog = self::find($scanLogId);

            if (!$scanLog) {
                Log::error('Scan log not found: ' . $scanLogId);
                return false;
            }

            // Check if not expired
            if (strtotime($scanLog->confirmation_expires_at) < time()) {
                Log::error('Scan log expired: ' . $scanLogId);
                // Mark as deleted
                $scanLog->update([
                    'isDeleted' => 1,
                    'deleted_at' => date('Y-m-d H:i:s')
                ]);
                return false;
            }

            // VALIDASI: Pastikan data sesuai panjang kolom database
            $updateData = [
                'log_qrcode_wa_user_scan' => substr($userScan, 0, 255), // varchar(255)
                'log_qrcode_wa_ha_scan' => substr($haScan, 0, 50), // varchar(50) - PENTING!
                'is_confirmed' => 1,
                'pending_confirmation' => 0,
                'confirmation_expires_at' => null
            ];

            Log::info('Updating scan log with data: ' . json_encode($updateData));

            // Update with confirmation
            $result = $scanLog->update($updateData);

            if ($result) {
                Log::info('Scan log confirmed successfully: ' . $scanLogId);
                return $scanLog->fresh();
            }

            return false;
        } catch (\Exception $e) {
            Log::error('Error confirming scan log: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return false;
        }
    }

    /**
     * Clean up expired pending scans
     */
    public static function cleanupExpiredScans()
    {
        try {
            $expiredScans = self::where('pending_confirmation', 1)
                ->where('is_confirmed', 0)
                ->where('confirmation_expires_at', '<=', date('Y-m-d H:i:s'))
                ->get();

            $deletedCount = 0;
            foreach ($expiredScans as $scan) {
                $scan->update([
                    'isDeleted' => 1,
                    'deleted_at' => date('Y-m-d H:i:s')
                ]);
                $deletedCount++;
            }

            if ($deletedCount > 0) {
                Log::info('Cleaned up ' . $deletedCount . ' expired pending scans');
            }

            return $deletedCount;
        } catch (\Exception $e) {
            Log::error('Error cleaning up expired scans: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Check if there's an active confirmed scan
     */
    public static function hasActiveConfirmedScan()
    {
        try {
            return self::where('isDeleted', 0)
                ->where('is_confirmed', 1)
                ->exists();
        } catch (\Exception $e) {
            Log::error('Error checking active confirmed scan: ' . $e->getMessage());
            return false;
        }
    }
}
