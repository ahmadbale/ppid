<?php

namespace Modules\Sisfo\App\Models\Log;

use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Database\Eloquent\Model;
use Modules\Sisfo\App\Models\HakAksesModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TransactionModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'log_transaction';
    protected $primaryKey = 'log_transaction_id';
    public $timestamps = false;
    protected $fillable = [
        'log_transaction_jenis',
        'log_transaction_aktivitas_id',
        'log_transaction_aktivitas',
        'log_transaction_level',
        'log_transaction_pelaku',
        'log_transaction_tanggal_aktivitas',
    ];

    // Di TransactionModel
    public static function createData($tipeTransaksi, $aktivitasId, $detailAktivitas)
{
    try {
        // Dapatkan controller dan action saat ini
        $route = Route::current();
        $controller = $route->getController();

        // Dapatkan tipe transaksi
        $transactionType = strtoupper($tipeTransaksi);

        // Tentukan jenis form/menu berdasarkan controller
        $formType = self::menentukanTipeForm($controller);

        // Ambil level dari user yang sedang login
      // Ambil level dari user yang sedang login
      $hakAksesNama = 'Tidak Diketahui';
            
      if (Auth::check()) {
          $user = Auth::user();
          
          // Coba ambil dari session terlebih dahulu (untuk web)
          $activeHakAksesId = session('active_hak_akses_id');
          
          // Jika tidak ada di session, coba ambil dari JWT token (untuk API)
          if (!$activeHakAksesId) {
              try {
                  // Coba dapatkan token JWT
                  $token = JWTAuth::getToken();
                  if ($token) {
                      // Decode token untuk mendapatkan payload
                      $payload = JWTAuth::getPayload($token)->toArray();
                      
                      // Periksa apakah ada klaim role
                      if (isset($payload['role'])) {
                          $hakAksesNama = $payload['role'];
                          Log::info("Nama hak akses dari JWT token: $hakAksesNama");
                      } else {
                          // Alternatiif: Ambil hak akses pertama dari user
                          $hakAksesId = $user->hak_akses_id;
                          $hakAkses = HakAksesModel::find($hakAksesId);   
                          $hakAksesNama = $hakAkses ? $hakAkses->hak_akses_nama : 'Tidak Diketahui';
                          Log::info("Nama hak akses dari relasi: $hakAksesNama");
                      }
                  }
              } catch (\Exception $e) {
                  Log::error("Error saat mengambil data dari token JWT: " . $e->getMessage());
                  
              
                  $hakAksesNama = $hakAkses ? $hakAkses->hak_akses_nama : 'Tidak Diketahui';
              }
          } else {
              // Ambil dari session (web)
              $level = HakAksesModel::find($activeHakAksesId);
              $hakAksesNama = $level ? $level->hak_akses_nama : 'Tidak Diketahui';
              Log::info("Nama hak akses dari session: $hakAksesNama");
          }
      }


        // Dapatkan alias dari session, jika tidak ada, gunakan dari UserModel
        $namaPelaku = null;
        if (Auth::check()) {
            // Coba ambil dari session dulu
            if (session()->has('alias')) {
                $namaPelaku = session('alias');
                Log::info("Menggunakan alias dari session: $namaPelaku");
            } else {
                // Jika tidak ada di session, gunakan fungsi yang sudah ada
                $namaPelaku = \Modules\Sisfo\App\Models\UserModel::generateAlias(Auth::user()->nama_pengguna);
                Log::info("Menggunakan alias yang di-generate: $namaPelaku");
                
                // Simpan ke session untuk penggunaan berikutnya
                session(['alias' => $namaPelaku]);
            }
        }

        // Generate pesan aktivitas
        $aktivitas = self::generateAktivitas(
            Auth::user()->nama_pengguna,
            $transactionType,
            $formType,
            $detailAktivitas
        );

        // Buat record log transaksi dengan namaPelaku yang sudah pasti ada nilainya
        self::create([
            'log_transaction_jenis' => $transactionType,
            'log_transaction_aktivitas_id' => $aktivitasId,
            'log_transaction_aktivitas' => $aktivitas,
            'log_transaction_level' => $hakAksesNama,
            'log_transaction_pelaku' => $namaPelaku,
            'log_transaction_tanggal_aktivitas' => now()
        ]);
    } catch (\Exception $e) {
        // Log error tanpa menghentikan proses
        Log::error('Gagal membuat log transaksi: ' . $e->getMessage());
    }
}

    private static function menentukanTipeForm($controller)
    {
        if (!$controller) {
            return 'tidak ada controller';
        }

        // Cek apakah controller memiliki properti breadcrumb
        if (property_exists($controller, 'breadcrumb')) {
            return $controller->breadcrumb;
        }

        // Jika tidak ada breadcrumb, coba ambil dari pagename
        if (property_exists($controller, 'pagename')) {
            $segments = explode('/', $controller->pagename);
            $lastSegment = end($segments);
            // Hapus kata 'Controller' dari nama
            return str_replace('Controller', '', $lastSegment);
        }

        return 'data';
    }

    private static function mendapatkanAksi($transactionType)
    {
        switch ($transactionType) {
            case 'CREATED':
                return 'membuat/mengajukan';
            case 'UPDATED':
                return 'memperbaharui';
            case 'DELETED':
                return 'menghapus';
            default:
                return 'melakukan aksi pada';
        }
    }

    private static function generateAktivitas($namaPengguna, $tipeTransaksi, $tipeForm, $detailAktivitas)
    {
        $aksi = self::mendapatkanAksi($tipeTransaksi);

        // Jika ada detail aktivitas, tambahkan ke pesan
        $detailTambahan = $detailAktivitas ? " $detailAktivitas" : '';

        return "{$namaPengguna} {$aksi} {$tipeForm}{$detailTambahan}";
    }
}