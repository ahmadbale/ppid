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
                             'deleted_at' => date('Y-m-d H:i:s')
                         ]);
            
            Log::info('Marked ' . $result . ' scan logs as deleted');
            return $result;
        } catch (\Exception $e) {
            Log::error('Error marking scans as deleted: ' . $e->getMessage());
            return false;
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
            
            // Convert ha_scan to string if it's an object/array
            $haScanString = $haScan;
            if (is_object($haScan) || is_array($haScan)) {
                if (is_object($haScan) && isset($haScan->nama_hak_akses)) {
                    $haScanString = $haScan->nama_hak_akses;
                } elseif (is_object($haScan) && isset($haScan->hak_akses_nama)) {
                    $haScanString = $haScan->hak_akses_nama;
                } elseif (is_array($haScan) && isset($haScan['nama_hak_akses'])) {
                    $haScanString = $haScan['nama_hak_akses'];
                } elseif (is_array($haScan) && isset($haScan['hak_akses_nama'])) {
                    $haScanString = $haScan['hak_akses_nama'];
                } else {
                    // If it's a complex object, try to extract the name
                    $haScanString = 'Unknown';
                    if (is_object($haScan)) {
                        $reflection = new \ReflectionObject($haScan);
                        $properties = $reflection->getProperties();
                        foreach ($properties as $property) {
                            $property->setAccessible(true);
                            $value = $property->getValue($haScan);
                            if (is_string($value) && (
                                strpos($property->getName(), 'nama') !== false ||
                                strpos($property->getName(), 'name') !== false
                            )) {
                                $haScanString = $value;
                                break;
                            }
                        }
                    }
                }
            }
            
            // Prepare data - hanya field yang ada di database
            $data = [
                'log_qrcode_wa_nomor_pengirim' => $nomorPengirim,
                'log_qrcode_wa_user_scan' => $userScan,
                'log_qrcode_wa_ha_scan' => $haScanString,
                'log_qrcode_wa_tanggal_scan' => date('Y-m-d H:i:s'),
                'isDeleted' => 0,
                'deleted_at' => null
            ];
            
            Log::info('Creating scan log with data:', $data);
            
            // Create new scan log using DB query builder to avoid TraitsModel issues
            $scanLogId = DB::table('log_qrcode_wa')->insertGetId($data);
            
            if ($scanLogId) {
                Log::info('Scan log created successfully with ID: ' . $scanLogId);
                // Return the created record
                return self::find($scanLogId);
            } else {
                Log::error('Failed to create scan log - insertGetId returned null');
                return null;
            }
            
        } catch (\Exception $e) {
            Log::error('Error creating scan log: ' . $e->getMessage(), [
                'nomor_pengirim' => $nomorPengirim,
                'user_scan' => $userScan,
                'ha_scan' => $haScan,
                'trace' => $e->getTraceAsString()
            ]);
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