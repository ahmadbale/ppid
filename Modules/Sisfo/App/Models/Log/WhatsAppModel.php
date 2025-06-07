<?php

namespace Modules\Sisfo\App\Models\Log;

use Modules\Sisfo\App\Models\TraitsModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use DateTime;
use Illuminate\Support\Facades\Log;

class WhatsAppModel extends Model
{
    use TraitsModel;

    protected $table = 'log_whatsapp';
    protected $primaryKey = 'log_whatsapp_id';
    
    protected $fillable = [
        'log_whatsapp_status',
        'log_whatsapp_nama_pengirim',
        'log_whatsapp_nomor_tujuan',
        'log_whatsapp_pesan',
        'log_whatsapp_tanggal_dikirim',
        'log_whatsapp_delivery_status'
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable = array_merge($this->fillable, $this->getCommonFields());
    }

    /**
     * Buat log WhatsApp baru
     */
    public static function createData($status, $nomorTujuan, $pesan, $deliveryStatus = 'sent')
    {
        try {
            $namaPengirim = Auth::check() ? Auth::user()->nama_pengguna : 'System';

            return self::create([
                'log_whatsapp_status' => $status,
                'log_whatsapp_nama_pengirim' => $namaPengirim,
                'log_whatsapp_nomor_tujuan' => $nomorTujuan,
                'log_whatsapp_pesan' => $pesan,
                'log_whatsapp_tanggal_dikirim' => (new DateTime())->format('Y-m-d H:i:s'),
                'log_whatsapp_delivery_status' => $deliveryStatus
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal membuat log WhatsApp: ' . $e->getMessage());
            return null;
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
}