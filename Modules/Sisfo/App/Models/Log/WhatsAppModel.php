<?php

namespace Modules\Sisfo\App\Models\Log;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use DateTime;
use Illuminate\Support\Facades\Log;

class WhatsAppModel extends Model
{
    protected $table = 'log_whatsapp';
    protected $primaryKey = 'log_whatsapp_id';
    public $timestamps = false; // Tambahkan ini karena tidak ada created_at/updated_at
    
    protected $fillable = [
        'log_whatsapp_status',
        'log_whatsapp_nama_pengirim',
        'log_whatsapp_nomor_tujuan',
        'log_whatsapp_pesan',
        'log_whatsapp_delivery_status',
        'log_whatsapp_tanggal_dikirim'
    ];

    /**
     * Buat log WhatsApp baru - DIPERBAIKI
     */
    public static function createData($status, $nomorTujuan, $pesan, $deliveryStatus = 'Pending')
    {
        try {
            $namaPengirim = Auth::check() ? Auth::user()->nama_pengguna : 'System';

            // VALIDASI: Trim data sesuai dengan panjang kolom database
            $data = [
                'log_whatsapp_status' => substr($status, 0, 255), // varchar(255)
                'log_whatsapp_nama_pengirim' => substr($namaPengirim, 0, 255), // varchar(255)
                'log_whatsapp_nomor_tujuan' => substr($nomorTujuan, 0, 255), // varchar(255)
                'log_whatsapp_pesan' => $pesan, // text - tidak perlu trim
                'log_whatsapp_delivery_status' => $deliveryStatus, // enum
                'log_whatsapp_tanggal_dikirim' => (new DateTime())->format('Y-m-d H:i:s')
            ];

            Log::info('Creating WhatsApp log with data:', $data);

            $result = self::create($data);

            if ($result) {
                Log::info('WhatsApp log created successfully with ID: ' . $result->log_whatsapp_id);
                return $result;
            } else {
                Log::error('Failed to create WhatsApp log - create() returned null');
                return null;
            }

        } catch (\Exception $e) {
            Log::error('Error creating WhatsApp log: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return null;
        }
    }

    /**
     * Update delivery status - TAMBAHAN
     */
    public function updateDeliveryStatus($newStatus)
    {
        try {
            $this->log_whatsapp_delivery_status = $newStatus;
            $result = $this->save();
            
            if ($result) {
                Log::info("WhatsApp log ID {$this->log_whatsapp_id} delivery status updated to: {$newStatus}");
            } else {
                Log::error("Failed to update WhatsApp log ID {$this->log_whatsapp_id} delivery status");
            }
            
            return $result;
        } catch (\Exception $e) {
            Log::error("Error updating WhatsApp log delivery status: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Ambil log WhatsApp berdasarkan rentang tanggal
     */
    public static function getLogByDateRange($startDate, $endDate)
    {
        $startDateTime = (new DateTime($startDate))->format('Y-m-d 00:00:00');
        $endDateTime = (new DateTime($endDate))->format('Y-m-d 23:59:59');
        
        return self::whereBetween('log_whatsapp_tanggal_dikirim', [$startDateTime, $endDateTime])
            ->orderBy('log_whatsapp_tanggal_dikirim', 'desc')
            ->get();
    }

    /**
     * Statistik WhatsApp
     */
    public static function getStatistikDetail()
    {
        $today = (new DateTime())->format('Y-m-d');
        $thisWeek = (new DateTime())->modify('monday this week')->format('Y-m-d 00:00:00');
        $thisMonth = (new DateTime())->modify('first day of this month')->format('Y-m-d 00:00:00');

        return [
            'total' => self::count(),
            'disetujui' => self::where('log_whatsapp_status', 'Disetujui')->count(),
            'ditolak' => self::where('log_whatsapp_status', 'Ditolak')->count(),
            'sent' => self::where('log_whatsapp_delivery_status', 'Sent')->count(),
            'failed' => self::where('log_whatsapp_delivery_status', 'Error')->count(),
            'hari_ini' => self::whereDate('log_whatsapp_tanggal_dikirim', $today)->count(),
            'minggu_ini' => self::where('log_whatsapp_tanggal_dikirim', '>=', $thisWeek)->count(),
            'bulan_ini' => self::where('log_whatsapp_tanggal_dikirim', '>=', $thisMonth)->count(),
        ];
    }

    /**
     * Format tanggal display
     */
    public function getFormattedTanggalDikirimAttribute()
    {
        return $this->log_whatsapp_tanggal_dikirim ? 
            (new DateTime($this->log_whatsapp_tanggal_dikirim))->format('d M Y H:i:s') : '-';
    }

    /**
     * Get log by status
     */
    public static function getByStatus($status)
    {
        return self::where('log_whatsapp_status', $status)
            ->orderBy('log_whatsapp_tanggal_dikirim', 'desc')
            ->get();
    }

    /**
     * Get log by delivery status
     */
    public static function getByDeliveryStatus($deliveryStatus)
    {
        return self::where('log_whatsapp_delivery_status', $deliveryStatus)
            ->orderBy('log_whatsapp_tanggal_dikirim', 'desc')
            ->get();
    }
}