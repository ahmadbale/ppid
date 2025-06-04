<?php

namespace Modules\Sisfo\App\Models\Log;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use DateTime;

class EmailModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'log_email';
    protected $primaryKey = 'log_email_id';
    public $timestamps = false;
    protected $fillable = [
        'log_email_status',
        'log_email_nama_pengirim',
        'log_email_tujuan',
        'log_email_tanggal_dikirim'
    ];

    // Definisikan cast untuk tanggal
    protected $casts = [
        'log_email_tanggal_dikirim' => 'datetime'
    ];

    public static function createData($status, $emailTujuan)
    {
        try {
            // Ambil nama pengirim dari user yang sedang login
            $namaPengirim = Auth::check() ? Auth::user()->nama_pengguna : 'System';

            // Buat record log email
            return self::create([
                'log_email_status' => $status,
                'log_email_nama_pengirim' => $namaPengirim,
                'log_email_tujuan' => $emailTujuan,
                'log_email_tanggal_dikirim' => (new DateTime())->format('Y-m-d H:i:s')
            ]);
        } catch (\Exception $e) {
            // Log error tanpa menghentikan proses
            Log::error('Gagal membuat log email: ' . $e->getMessage());
            return null;
        }
    }

    public static function selectData()
    {
        return self::orderBy('log_email_tanggal_dikirim', 'desc')->get();
    }

    public static function updateData($id, $data)
    {
        try {
            $email = self::findOrFail($id);
            $email->update($data);
            return $email;
        } catch (\Exception $e) {
            Log::error('Gagal update log email: ' . $e->getMessage());
            throw $e;
        }
    }

    public static function deleteData($id)
    {
        try {
            $email = self::findOrFail($id);
            $email->delete();
            return true;
        } catch (\Exception $e) {
            Log::error('Gagal hapus log email: ' . $e->getMessage());
            throw $e;
        }
    }

    public static function validasiData($request)
    {
        // Implementasi validasi jika diperlukan
        return true;
    }

    /**
     * Helper method untuk mendapatkan statistik email
     */
    public static function getStatistik()
    {
        $today = (new DateTime())->format('Y-m-d');
        
        return [
            'total' => self::count(),
            'disetujui' => self::where('log_email_status', 'Disetujui')->count(),
            'ditolak' => self::where('log_email_status', 'Ditolak')->count(),
            'hari_ini' => self::whereDate('log_email_tanggal_dikirim', $today)->count()
        ];
    }

    /**
     * Helper method untuk mendapatkan email berdasarkan status
     */
    public static function getByStatus($status)
    {
        return self::where('log_email_status', $status)
            ->orderBy('log_email_tanggal_dikirim', 'desc')
            ->get();
    }

    /**
     * Helper method untuk mendapatkan email hari ini
     */
    public static function getEmailHariIni()
    {
        $today = (new DateTime())->format('Y-m-d');
        
        return self::whereDate('log_email_tanggal_dikirim', $today)
            ->orderBy('log_email_tanggal_dikirim', 'desc')
            ->get();
    }

    /**
     * Helper method untuk mendapatkan email berdasarkan rentang tanggal
     */
    public static function getEmailByDateRange($startDate, $endDate)
    {
        $startDateTime = (new DateTime($startDate))->format('Y-m-d 00:00:00');
        $endDateTime = (new DateTime($endDate))->format('Y-m-d 23:59:59');
        
        return self::whereBetween('log_email_tanggal_dikirim', [$startDateTime, $endDateTime])
            ->orderBy('log_email_tanggal_dikirim', 'desc')
            ->get();
    }

    /**
     * Helper method untuk mendapatkan statistik detail
     */
    public static function getStatistikDetail()
    {
        $today = (new DateTime())->format('Y-m-d');
        $thisWeek = (new DateTime())->modify('monday this week')->format('Y-m-d 00:00:00');
        $thisMonth = (new DateTime())->modify('first day of this month')->format('Y-m-d 00:00:00');

        return [
            'total' => self::count(),
            'disetujui' => self::where('log_email_status', 'Disetujui')->count(),
            'ditolak' => self::where('log_email_status', 'Ditolak')->count(),
            'hari_ini' => self::whereDate('log_email_tanggal_dikirim', $today)->count(),
            'minggu_ini' => self::where('log_email_tanggal_dikirim', '>=', $thisWeek)->count(),
            'bulan_ini' => self::where('log_email_tanggal_dikirim', '>=', $thisMonth)->count(),
        ];
    }

    /**
     * Helper method untuk format tanggal display (sama seperti di view)
     */
    public function getFormattedTanggalDikirimAttribute()
    {
        return $this->log_email_tanggal_dikirim ? 
            (new DateTime($this->log_email_tanggal_dikirim))->format('d M Y H:i:s') : '-';
    }

    /**
     * Helper method untuk format tanggal singkat
     */
    public function getFormattedTanggalSingkatAttribute()
    {
        return $this->log_email_tanggal_dikirim ? 
            (new DateTime($this->log_email_tanggal_dikirim))->format('d/m/Y H:i') : '-';
    }
}