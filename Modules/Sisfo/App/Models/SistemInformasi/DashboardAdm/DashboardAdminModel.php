<?php

namespace Modules\Sisfo\App\Models\SistemInformasi\DashboardAdm;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;

class DashboardAdminModel extends Model
{
     protected $table = null;

     public static function getDashboardStatistics()
     {
          $tables = [
               ['key' => 'permohonan_informasi', 'table' => 't_permohonan_informasi', 'status' => 'pi_status'],
               ['key' => 'whistle_blowing_system', 'table' => 't_wbs', 'status' => 'wbs_status'],
               ['key' => 'pernyataan_keberatan', 'table' => 't_pernyataan_keberatan', 'status' => 'pk_status'],
               ['key' => 'aduan_masyarakat', 'table' => 't_pengaduan_masyarakat', 'status' => 'pm_status'],
               ['key' => 'permohonan_pemeliharaan', 'table' => 't_permohonan_perawatan', 'status' => 'pp_status']
          ];

          // Inisialisasi array dan total
          $results = [];
          $totalPermohonan = 0;
          $totalDiterima = 0;
          $totalDitolak = 0;
          $totalDiproses = 0;

          // Variabel untuk melacak tahun terbaru
          $latestYear = null;

          // Looping untuk setiap tabel
          foreach ($tables as $table) {
               // Ambil data statistik
               $data = DB::table($table['table'])
                    ->selectRaw("
                    COUNT(*) as total, 
                    SUM(CASE WHEN {$table['status']} = 'Disetujui' THEN 1 ELSE 0 END) as diterima, 
                    SUM(CASE WHEN {$table['status']} = 'Ditolak' THEN 1 ELSE 0 END) as ditolak,
                    SUM(CASE WHEN {$table['status']} IN ('Masuk', 'Diproses') THEN 1 ELSE 0 END) as diproses
                ")
                    ->where('isDeleted', 0)
                    ->first();

               // Ambil tahun terbaru untuk perhitungan periode
               $yearData = DB::table($table['table'])
                    ->selectRaw("MAX(YEAR(created_at)) as latest_year")
                    ->where('isDeleted', 0)
                    ->first();

               // Update tahun terbaru 
               if ($yearData->latest_year && ($latestYear === null || $yearData->latest_year > $latestYear)) {
                    $latestYear = $yearData->latest_year;
               }

               // Simpan hasil
               $results[$table['key']] = [
                    'total' => $data->total,
                    'diterima' => $data->diterima,
                    'ditolak' => $data->ditolak,
                    'diproses' => $data->diproses
               ];

               // Update total
               $totalPermohonan += $data->total;
               $totalDiterima += $data->diterima;
               $totalDitolak += $data->ditolak;
               $totalDiproses += $data->diproses;
          }

          // Jika tidak ada data, gunakan tahun sekarang
          if ($latestYear === null) {
               $latestYear = Carbon::now()->format('Y');
          }

          // Format string periode dengan tahun sebelumnya/tahun terbaru
          $periodString = ($latestYear - 1) . '/' . $latestYear;

          // hasil akhir
          $arr_data = [
               'periode' => [
                    'tahun' => $periodString,
                    'pengajuan_total' => $totalPermohonan,
                    'pengajuan_diterima' => $totalDiterima,
                    'pengajuan_ditolak' => $totalDitolak,
                    'pengajuan_diproses' => $totalDiproses
               ],
               'jenis_kasus' => $results
          ];

          return $arr_data;
     }

   
     public static function getPermintaanTerbaru($limit = 10, $startDate = null, $endDate = null, $jenisInformasi = null)
     {
         $permintaan = collect();
     
         try {
             // 1. Ambil data dari t_wbs
             if (!$jenisInformasi || $jenisInformasi === 'wbs') {
                 $query = DB::table('t_wbs')
                     ->select([
                         'created_at as tanggal',
                         'wbs_nama_tanpa_gelar as nama',
                         'wbs_status as status',
                         DB::raw("'WBS' as jenis")
                     ])
                     ->where('isDeleted', 0)
                     ->whereNotNull('wbs_nama_tanpa_gelar');
     
                 // Apply date filters
                 if ($startDate) $query->whereDate('created_at', '>=', $startDate);
                 if ($endDate) $query->whereDate('created_at', '<=', $endDate);
     
                 $wbsData = $query->orderBy('created_at', 'desc')->get();
                 $permintaan = $permintaan->merge($wbsData);
             }
     
             // 2. Ambil data dari t_pengaduan_masyarakat
             if (!$jenisInformasi || $jenisInformasi === 'pengaduan') {
                 $query = DB::table('t_pengaduan_masyarakat')
                     ->select([
                         'created_at as tanggal',
                         'pm_nama_tanpa_gelar as nama',
                         'pm_status as status',
                         DB::raw("'Pengaduan Masyarakat' as jenis")
                     ])
                     ->where('isDeleted', 0)
                     ->whereNotNull('pm_nama_tanpa_gelar');
     
                 // Apply date filters
                 if ($startDate) $query->whereDate('created_at', '>=', $startDate);
                 if ($endDate) $query->whereDate('created_at', '<=', $endDate);
     
                 $pengaduanData = $query->orderBy('created_at', 'desc')->get();
                 $permintaan = $permintaan->merge($pengaduanData);
             }
     
             // 3. Ambil data dari t_permohonan_perawatan
             if (!$jenisInformasi || $jenisInformasi === 'perawatan') {
                 $query = DB::table('t_permohonan_perawatan')
                     ->select([
                         'created_at as tanggal',
                         'pp_nama_pengguna as nama',
                         'pp_status as status',
                         DB::raw("'Permohonan Perawatan Sarana' as jenis")
                     ])
                     ->where('isDeleted', 0)
                     ->whereNotNull('pp_nama_pengguna');
     
                 // Apply date filters
                 if ($startDate) $query->whereDate('created_at', '>=', $startDate);
                 if ($endDate) $query->whereDate('created_at', '<=', $endDate);
     
                 $perawatanData = $query->orderBy('created_at', 'desc')->get();
                 $permintaan = $permintaan->merge($perawatanData);
             }
     
             // 4. Ambil data dari t_permohonan_informasi dengan JOIN
             if (!$jenisInformasi || $jenisInformasi === 'permohonan') {
                 $piData = collect();
     
                 // Data dari Diri Sendiri
                 $queryDS = DB::table('t_permohonan_informasi as pi')
                     ->join('t_form_pi_diri_sendiri as ds', 'pi.fk_t_form_pi_diri_sendiri', '=', 'ds.form_pi_diri_sendiri_id')
                     ->select([
                         'pi.created_at as tanggal',
                         'ds.pi_nama_pengguna as nama',
                         'pi.pi_status as status',
                         DB::raw("'Permohonan Informasi' as jenis")
                     ])
                     ->where('pi.isDeleted', 0)
                     ->where('pi.pi_kategori_pemohon', 'Diri Sendiri')
                     ->whereNotNull('ds.pi_nama_pengguna');
     
                 if ($startDate) $queryDS->whereDate('pi.created_at', '>=', $startDate);
                 if ($endDate) $queryDS->whereDate('pi.created_at', '<=', $endDate);
     
                 $piDiriSendiri = $queryDS->orderBy('pi.created_at', 'desc')->get();
                 $piData = $piData->merge($piDiriSendiri);
     
                 // Data dari Orang Lain
                 $queryOL = DB::table('t_permohonan_informasi as pi')
                     ->join('t_form_pi_orang_lain as ol', 'pi.fk_t_form_pi_orang_lain', '=', 'ol.form_pi_orang_lain_id')
                     ->select([
                         'pi.created_at as tanggal',
                         'ol.pi_nama_pengguna_informasi as nama',
                         'pi.pi_status as status',
                         DB::raw("'Permohonan Informasi' as jenis")
                     ])
                     ->where('pi.isDeleted', 0)
                     ->where('pi.pi_kategori_pemohon', 'Orang Lain')
                     ->whereNotNull('ol.pi_nama_pengguna_informasi');
     
                 if ($startDate) $queryOL->whereDate('pi.created_at', '>=', $startDate);
                 if ($endDate) $queryOL->whereDate('pi.created_at', '<=', $endDate);
     
                 $piOrangLain = $queryOL->orderBy('pi.created_at', 'desc')->get();
                 $piData = $piData->merge($piOrangLain);
     
                 // Data dari Organisasi
                 $queryOrg = DB::table('t_permohonan_informasi as pi')
                     ->join('t_form_pi_organisasi as org', 'pi.fk_t_form_pi_organisasi', '=', 'org.form_pi_organisasi_id')
                     ->select([
                         'pi.created_at as tanggal',
                         'org.pi_nama_organisasi as nama',
                         'pi.pi_status as status',
                         DB::raw("'Permohonan Informasi' as jenis")
                     ])
                     ->where('pi.isDeleted', 0)
                     ->where('pi.pi_kategori_pemohon', 'Organisasi')
                     ->whereNotNull('org.pi_nama_organisasi');
     
                 if ($startDate) $queryOrg->whereDate('pi.created_at', '>=', $startDate);
                 if ($endDate) $queryOrg->whereDate('pi.created_at', '<=', $endDate);
     
                 $piOrganisasi = $queryOrg->orderBy('pi.created_at', 'desc')->get();
                 $piData = $piData->merge($piOrganisasi);
     
                 $permintaan = $permintaan->merge($piData);
             }
     
             // 5. Ambil data dari t_pernyataan_keberatan dengan JOIN
             if (!$jenisInformasi || $jenisInformasi === 'keberatan') {
                 $pkData = collect();
     
                 // Data dari Diri Sendiri
                 $queryPKDS = DB::table('t_pernyataan_keberatan as pk')
                     ->join('t_form_pk_diri_sendiri as ds', 'pk.fk_t_form_pk_diri_sendiri', '=', 'ds.form_pk_diri_sendiri_id')
                     ->select([
                         'pk.created_at as tanggal',
                         'ds.pk_nama_pengguna as nama',
                         'pk.pk_status as status',
                         DB::raw("'Pernyataan Keberatan' as jenis")
                     ])
                     ->where('pk.isDeleted', 0)
                     ->where('pk.pk_kategori_pemohon', 'Diri Sendiri')
                     ->whereNotNull('ds.pk_nama_pengguna');
     
                 if ($startDate) $queryPKDS->whereDate('pk.created_at', '>=', $startDate);
                 if ($endDate) $queryPKDS->whereDate('pk.created_at', '<=', $endDate);
     
                 $pkDiriSendiri = $queryPKDS->orderBy('pk.created_at', 'desc')->get();
                 $pkData = $pkData->merge($pkDiriSendiri);
     
                 // Data dari Orang Lain
                 $queryPKOL = DB::table('t_pernyataan_keberatan as pk')
                     ->join('t_form_pk_orang_lain as ol', 'pk.fk_t_form_pk_orang_lain', '=', 'ol.form_pk_orang_lain_id')
                     ->select([
                         'pk.created_at as tanggal',
                         'ol.pk_nama_kuasa_pemohon as nama',
                         'pk.pk_status as status',
                         DB::raw("'Pernyataan Keberatan' as jenis")
                     ])
                     ->where('pk.isDeleted', 0)
                     ->where('pk.pk_kategori_pemohon', 'Orang Lain')
                     ->whereNotNull('ol.pk_nama_kuasa_pemohon');
     
                 if ($startDate) $queryPKOL->whereDate('pk.created_at', '>=', $startDate);
                 if ($endDate) $queryPKOL->whereDate('pk.created_at', '<=', $endDate);
     
                 $pkOrangLain = $queryPKOL->orderBy('pk.created_at', 'desc')->get();
                 $pkData = $pkData->merge($pkOrangLain);
     
                 $permintaan = $permintaan->merge($pkData);
             }
     
         } catch (\Exception $e) {
             Log::error("Error in getPermintaanTerbaru: " . $e->getMessage());
             Log::error("Stack trace: " . $e->getTraceAsString());
         }
     
         return $permintaan->sortByDesc('tanggal')->take($limit)->values();
     }

     public static function getDistributionData($startDate = null, $endDate = null, $jenisInformasi = null)
     {
          $tables = [
               'permohonan_informasi' => ['table' => 't_permohonan_informasi', 'label' => 'Permohonan Informasi'],
               'aduan_masyarakat' => ['table' => 't_pengaduan_masyarakat', 'label' => 'Pengaduan Masyarakat'],
               'permohonan_pemeliharaan' => ['table' => 't_permohonan_perawatan', 'label' => 'Permohonan Sarana Prasarana'],
               'pernyataan_keberatan' => ['table' => 't_pernyataan_keberatan', 'label' => 'Pernyataan Keberatan'],
               'whistle_blowing_system' => ['table' => 't_wbs', 'label' => 'Whistle Blowing']
          ];

          // Filter tabel berdasarkan jenis informasi
          if ($jenisInformasi) {
               $filteredTables = [];
               switch ($jenisInformasi) {
                    case 'permohonan':
                         $filteredTables['permohonan_informasi'] = $tables['permohonan_informasi'];
                         break;
                    case 'wbs':
                         $filteredTables['whistle_blowing_system'] = $tables['whistle_blowing_system'];
                         break;
                    case 'keberatan':
                         $filteredTables['pernyataan_keberatan'] = $tables['pernyataan_keberatan'];
                         break;
                    case 'pengaduan':
                         $filteredTables['aduan_masyarakat'] = $tables['aduan_masyarakat'];
                         break;
                    case 'perawatan':
                         $filteredTables['permohonan_pemeliharaan'] = $tables['permohonan_pemeliharaan'];
                         break;
               }
               $tables = $filteredTables;
          }

          $distributionData = [];
          $totalSemua = 0;

          // Hitung total untuk setiap tabel
          foreach ($tables as $key => $config) {
               $query = DB::table($config['table'])
                    ->where('isDeleted', 0);

               // Apply date filters
               if ($startDate) {
                    $query->whereDate('created_at', '>=', $startDate);
               }
               if ($endDate) {
                    $query->whereDate('created_at', '<=', $endDate);
               }

               $total = $query->count();
               $totalSemua += $total;

               // Simpan data sementara
               $distributionData[$key] = [
                    'label' => $config['label'],
                    'total' => $total
               ];
          }

          $maxReference = max(200, $totalSemua); // Minimal 200 sebagai referensi

          // Hitung persentase untuk setiap item
          $result = [];
          foreach ($distributionData as $data) {
               $percentage = $maxReference > 0 ? round(($data['total'] / $maxReference) * 100, 1) : 0;

               $result[] = [
                    'label' => $data['label'],
                    'total' => $data['total'],
                    'max' => $maxReference,
                    'percentage' => $percentage
               ];
          }

          return $result;
     }

     // Add method untuk mendapatkan filtered distribution data
     public static function getFilteredDistributionData($startDate = null, $endDate = null, $jenisInformasi = null)
     {
          return self::getDistributionData($startDate, $endDate, $jenisInformasi);
     }

     public static function getChartData()
     {
          $tables = [
               ['key' => 'Permohonan Informasi', 'table' => 't_permohonan_informasi'],
               ['key' => 'WBS', 'table' => 't_wbs'],
               ['key' => 'Pernyataan Keberatan', 'table' => 't_pernyataan_keberatan'],
               ['key' => 'Pengaduan Masyarakat', 'table' => 't_pengaduan_masyarakat'],
               ['key' => 'Permohonan Perawatan', 'table' => 't_permohonan_perawatan']
          ];

          $monthlyData = [];
          $months = [];

          // Generate 12 bulan terakhir
          for ($i = 11; $i >= 0; $i--) {
               $month = Carbon::now()->subMonths($i);
               $months[] = $month->format('M');
               $monthKey = $month->format('Y-m');

               foreach ($tables as $table) {
                    if (!isset($monthlyData[$table['key']])) {
                         $monthlyData[$table['key']] = [];
                    }

                    $count = DB::table($table['table'])
                         ->whereYear('created_at', $month->year)
                         ->whereMonth('created_at', $month->month)
                         ->where('isDeleted', 0)
                         ->count();

                    $monthlyData[$table['key']][] = $count;
               }
          }

          return [
               'labels' => $months,
               'datasets' => $monthlyData
          ];
     }

     public static function getMenuBoxesData()
     {
          $statistik = self::getDashboardStatistics();

          return [
               [
                    'jumlah' => $statistik['periode']['pengajuan_total'],
                    'status' => 'Masuk',
                    'bg' => 'primary',
                    'icon' => 'ion ion-bag'
               ],
               [
                    'jumlah' => $statistik['periode']['pengajuan_diproses'],
                    'status' => 'Diproses',
                    'bg' => 'warning',
                    'icon' => 'ion ion-stats-bars'
               ],
               [
                    'jumlah' => $statistik['periode']['pengajuan_diterima'],
                    'status' => 'Disetujui',
                    'bg' => 'success',
                    'icon' => 'ion ion-person-add'
               ],
               [
                    'jumlah' => $statistik['periode']['pengajuan_ditolak'],
                    'status' => 'Ditolak',
                    'bg' => 'danger',
                    'icon' => 'ion ion-pie-graph'
               ]
          ];
     }

     public static function getFilteredData($startDate = null, $endDate = null, $jenisInformasi = null)
     {
          $result = collect();

          // Tentukan tabel mana yang akan diquery berdasarkan jenis informasi
          $tablesToQuery = [];

          if (!$jenisInformasi || $jenisInformasi === '') {
               // Jika tidak ada filter jenis, ambil semua
               $tablesToQuery = [
                    'wbs' => ['table' => 't_wbs', 'nama' => 'wbs_nama_tanpa_gelar', 'status' => 'wbs_status', 'jenis' => 'Whistle Blowing System'],
                    'pengaduan' => ['table' => 't_pengaduan_masyarakat', 'nama' => 'pm_nama_tanpa_gelar', 'status' => 'pm_status', 'jenis' => 'Pengaduan Masyarakat'],
                    'perawatan' => ['table' => 't_permohonan_perawatan', 'nama' => 'pp_nama_pengguna', 'status' => 'pp_status', 'jenis' => 'Permohonan Perawatan']
               ];
          } else {
               // Filter berdasarkan jenis informasi
               switch ($jenisInformasi) {
                    case 'wbs':
                         $tablesToQuery['wbs'] = ['table' => 't_wbs', 'nama' => 'wbs_nama_tanpa_gelar', 'status' => 'wbs_status', 'jenis' => 'Whistle Blowing System'];
                         break;
                    case 'pengaduan':
                         $tablesToQuery['pengaduan'] = ['table' => 't_pengaduan_masyarakat', 'nama' => 'pm_nama_tanpa_gelar', 'status' => 'pm_status', 'jenis' => 'Pengaduan Masyarakat'];
                         break;
                    case 'perawatan':
                         $tablesToQuery['perawatan'] = ['table' => 't_permohonan_perawatan', 'nama' => 'pp_nama_pengguna', 'status' => 'pp_status', 'jenis' => 'Permohonan Perawatan'];
                         break;
                    case 'permohonan':
                         // Untuk permohonan informasi, perlu JOIN karena struktur yang kompleks
                         $result = $result->merge(self::getFilteredPermohonanInformasi($startDate, $endDate));
                         break;
                    case 'keberatan':
                         // Untuk pernyataan keberatan, perlu JOIN karena struktur yang kompleks
                         $result = $result->merge(self::getFilteredPernyataanKeberatan($startDate, $endDate));
                         break;
               }
          }

          // Query tabel-tabel yang sederhana (tanpa JOIN)
          foreach ($tablesToQuery as $config) {
               try {
                    $query = DB::table($config['table'])
                         ->select([
                              'created_at as tanggal',
                              $config['nama'] . ' as nama',
                              $config['status'] . ' as status',
                              DB::raw("'{$config['jenis']}' as jenis")
                         ])
                         ->where('isDeleted', 0)
                         ->whereNotNull($config['nama']);

                    // Apply date filters
                    if ($startDate) {
                         $query->whereDate('created_at', '>=', $startDate);
                    }
                    if ($endDate) {
                         $query->whereDate('created_at', '<=', $endDate);
                    }

                    $data = $query->orderBy('created_at', 'desc')
                         ->get()
                         ->map(function ($item) {
                              return (object) [
                                   'tanggal' => $item->tanggal,
                                   'nama' => $item->nama,
                                   'status' => $item->status,
                                   'jenis' => $item->jenis
                              ];
                         });

                    $result = $result->merge($data);
               } catch (\Exception $e) {
                    // Log error tapi lanjutkan
                    Log::error("Error querying {$config['table']}: " . $e->getMessage());
               }
          }

          // Jika tidak ada filter jenis atau filter khusus permohonan/keberatan
          if (!$jenisInformasi || $jenisInformasi === 'permohonan') {
               $result = $result->merge(self::getFilteredPermohonanInformasi($startDate, $endDate));
          }

          if (!$jenisInformasi || $jenisInformasi === 'keberatan') {
               $result = $result->merge(self::getFilteredPernyataanKeberatan($startDate, $endDate));
          }

          return $result->sortByDesc('tanggal')->values();
     }

     private static function getFilteredPermohonanInformasi($startDate = null, $endDate = null)
     {
          $result = collect();

          try {
               // Query untuk Diri Sendiri
               $queryDS = DB::table('t_permohonan_informasi as pi')
                    ->join('t_form_pi_diri_sendiri as ds', 'pi.fk_t_form_pi_diri_sendiri', '=', 'ds.form_pi_diri_sendiri_id')
                    ->select([
                         'pi.created_at as tanggal',
                         'ds.pi_nama_pengguna as nama',
                         'pi.pi_status as status',
                         DB::raw("'Permohonan Informasi' as jenis")
                    ])
                    ->where('pi.isDeleted', 0)
                    ->where('pi.pi_kategori_pemohon', 'Diri Sendiri')
                    ->whereNotNull('ds.pi_nama_pengguna');

               if ($startDate) $queryDS->whereDate('pi.created_at', '>=', $startDate);
               if ($endDate) $queryDS->whereDate('pi.created_at', '<=', $endDate);

               $result = $result->merge($queryDS->get()->map(function ($item) {
                    return (object) [
                         'tanggal' => $item->tanggal,
                         'nama' => $item->nama,
                         'status' => $item->status,
                         'jenis' => $item->jenis
                    ];
               }));

               // Query untuk Orang Lain
               $queryOL = DB::table('t_permohonan_informasi as pi')
                    ->join('t_form_pi_orang_lain as ol', 'pi.fk_t_form_pi_orang_lain', '=', 'ol.form_pi_orang_lain_id')
                    ->select([
                         'pi.created_at as tanggal',
                         'ol.pi_nama_pengguna_informasi as nama',
                         'pi.pi_status as status',
                         DB::raw("'Permohonan Informasi' as jenis")
                    ])
                    ->where('pi.isDeleted', 0)
                    ->where('pi.pi_kategori_pemohon', 'Orang Lain')
                    ->whereNotNull('ol.pi_nama_pengguna_informasi');

               if ($startDate) $queryOL->whereDate('pi.created_at', '>=', $startDate);
               if ($endDate) $queryOL->whereDate('pi.created_at', '<=', $endDate);

               $result = $result->merge($queryOL->get()->map(function ($item) {
                    return (object) [
                         'tanggal' => $item->tanggal,
                         'nama' => $item->nama,
                         'status' => $item->status,
                         'jenis' => $item->jenis
                    ];
               }));

               // Query untuk Organisasi
               $queryOrg = DB::table('t_permohonan_informasi as pi')
                    ->join('t_form_pi_organisasi as org', 'pi.fk_t_form_pi_organisasi', '=', 'org.form_pi_organisasi_id')
                    ->select([
                         'pi.created_at as tanggal',
                         'org.pi_nama_organisasi as nama',
                         'pi.pi_status as status',
                         DB::raw("'Permohonan Informasi' as jenis")
                    ])
                    ->where('pi.isDeleted', 0)
                    ->where('pi.pi_kategori_pemohon', 'Organisasi')
                    ->whereNotNull('org.pi_nama_organisasi');

               if ($startDate) $queryOrg->whereDate('pi.created_at', '>=', $startDate);
               if ($endDate) $queryOrg->whereDate('pi.created_at', '<=', $endDate);

               $result = $result->merge($queryOrg->get()->map(function ($item) {
                    return (object) [
                         'tanggal' => $item->tanggal,
                         'nama' => $item->nama,
                         'status' => $item->status,
                         'jenis' => $item->jenis
                    ];
               }));
          } catch (\Exception $e) {
               Log::error("Error getting filtered permohonan informasi: " . $e->getMessage());
          }

          return $result;
     }

     private static function getFilteredPernyataanKeberatan($startDate = null, $endDate = null)
     {
          $result = collect();

          try {
               // Query untuk Diri Sendiri
               $queryDS = DB::table('t_pernyataan_keberatan as pk')
                    ->join('t_form_pk_diri_sendiri as ds', 'pk.fk_t_form_pk_diri_sendiri', '=', 'ds.form_pk_diri_sendiri_id')
                    ->select([
                         'pk.created_at as tanggal',
                         'ds.pk_nama_pengguna as nama',
                         'pk.pk_status as status',
                         DB::raw("'Pernyataan Keberatan' as jenis")
                    ])
                    ->where('pk.isDeleted', 0)
                    ->where('pk.pk_kategori_pemohon', 'Diri Sendiri')
                    ->whereNotNull('ds.pk_nama_pengguna');

               if ($startDate) $queryDS->whereDate('pk.created_at', '>=', $startDate);
               if ($endDate) $queryDS->whereDate('pk.created_at', '<=', $endDate);

               $result = $result->merge($queryDS->get()->map(function ($item) {
                    return (object) [
                         'tanggal' => $item->tanggal,
                         'nama' => $item->nama,
                         'status' => $item->status,
                         'jenis' => $item->jenis
                    ];
               }));

               // Query untuk Orang Lain
               $queryOL = DB::table('t_pernyataan_keberatan as pk')
                    ->join('t_form_pk_orang_lain as ol', 'pk.fk_t_form_pk_orang_lain', '=', 'ol.form_pk_orang_lain_id')
                    ->select([
                         'pk.created_at as tanggal',
                         'ol.pk_nama_kuasa_pemohon as nama',
                         'pk.pk_status as status',
                         DB::raw("'Pernyataan Keberatan' as jenis")
                    ])
                    ->where('pk.isDeleted', 0)
                    ->where('pk.pk_kategori_pemohon', 'Orang Lain')
                    ->whereNotNull('ol.pk_nama_kuasa_pemohon');

               if ($startDate) $queryOL->whereDate('pk.created_at', '>=', $startDate);
               if ($endDate) $queryOL->whereDate('pk.created_at', '<=', $endDate);

               $result = $result->merge($queryOL->get()->map(function ($item) {
                    return (object) [
                         'tanggal' => $item->tanggal,
                         'nama' => $item->nama,
                         'status' => $item->status,
                         'jenis' => $item->jenis
                    ];
               }));
          } catch (\Exception $e) {
               Log::error("Error getting filtered pernyataan keberatan: " . $e->getMessage());
          }

          return $result;
     }

     public static function getFilteredStatistics($startDate = null, $endDate = null, $jenisInformasi = null)
     {
          $tables = [
               'permohonan_informasi' => ['table' => 't_permohonan_informasi', 'status' => 'pi_status'],
               'whistle_blowing_system' => ['table' => 't_wbs', 'status' => 'wbs_status'],
               'pernyataan_keberatan' => ['table' => 't_pernyataan_keberatan', 'status' => 'pk_status'],
               'aduan_masyarakat' => ['table' => 't_pengaduan_masyarakat', 'status' => 'pm_status'],
               'permohonan_pemeliharaan' => ['table' => 't_permohonan_perawatan', 'status' => 'pp_status']
          ];

          // Filter tabel berdasarkan jenis informasi
          if ($jenisInformasi) {
               $filteredTables = [];
               switch ($jenisInformasi) {
                    case 'permohonan':
                         $filteredTables['permohonan_informasi'] = $tables['permohonan_informasi'];
                         break;
                    case 'wbs':
                         $filteredTables['whistle_blowing_system'] = $tables['whistle_blowing_system'];
                         break;
                    case 'keberatan':
                         $filteredTables['pernyataan_keberatan'] = $tables['pernyataan_keberatan'];
                         break;
                    case 'pengaduan':
                         $filteredTables['aduan_masyarakat'] = $tables['aduan_masyarakat'];
                         break;
                    case 'perawatan':
                         $filteredTables['permohonan_pemeliharaan'] = $tables['permohonan_pemeliharaan'];
                         break;
               }
               $tables = $filteredTables;
          }

          $results = [];
          $totalPermohonan = 0;
          $totalDiterima = 0;
          $totalDitolak = 0;
          $totalDiproses = 0;

          foreach ($tables as $key => $table) {
               $query = DB::table($table['table'])
                    ->selectRaw("
                    COUNT(*) as total, 
                    SUM(CASE WHEN {$table['status']} = 'Disetujui' THEN 1 ELSE 0 END) as diterima, 
                    SUM(CASE WHEN {$table['status']} = 'Ditolak' THEN 1 ELSE 0 END) as ditolak,
                    SUM(CASE WHEN {$table['status']} IN ('Masuk', 'Diproses') THEN 1 ELSE 0 END) as diproses
                ")
                    ->where('isDeleted', 0);

               // Apply date filters
               if ($startDate) {
                    $query->whereDate('created_at', '>=', $startDate);
               }
               if ($endDate) {
                    $query->whereDate('created_at', '<=', $endDate);
               }

               $data = $query->first();

               $results[$key] = [
                    'total' => $data->total,
                    'diterima' => $data->diterima,
                    'ditolak' => $data->ditolak,
                    'diproses' => $data->diproses
               ];

               $totalPermohonan += $data->total;
               $totalDiterima += $data->diterima;
               $totalDitolak += $data->ditolak;
               $totalDiproses += $data->diproses;
          }

          return [
               'periode' => [
                    'pengajuan_total' => $totalPermohonan,
                    'pengajuan_diterima' => $totalDiterima,
                    'pengajuan_ditolak' => $totalDitolak,
                    'pengajuan_diproses' => $totalDiproses
               ],
               'jenis_kasus' => $results
          ];
     }

     public static function getFilteredChartData($startDate = null, $endDate = null, $jenisInformasi = null)
     {
          $tables = [
               ['key' => 'Permohonan Informasi', 'table' => 't_permohonan_informasi'],
               ['key' => 'WBS', 'table' => 't_wbs'],
               ['key' => 'Pernyataan Keberatan', 'table' => 't_pernyataan_keberatan'],
               ['key' => 'Pengaduan Masyarakat', 'table' => 't_pengaduan_masyarakat'],
               ['key' => 'Permohonan Perawatan', 'table' => 't_permohonan_perawatan']
          ];

          // Filter tabel berdasarkan jenis informasi
          if ($jenisInformasi) {
               $filteredTables = [];
               foreach ($tables as $table) {
                    $include = false;
                    switch ($jenisInformasi) {
                         case 'permohonan':
                              $include = $table['key'] === 'Permohonan Informasi';
                              break;
                         case 'wbs':
                              $include = $table['key'] === 'WBS';
                              break;
                         case 'keberatan':
                              $include = $table['key'] === 'Pernyataan Keberatan';
                              break;
                         case 'pengaduan':
                              $include = $table['key'] === 'Pengaduan Masyarakat';
                              break;
                         case 'perawatan':
                              $include = $table['key'] === 'Permohonan Perawatan';
                              break;
                    }
                    if ($include) {
                         $filteredTables[] = $table;
                    }
               }
               $tables = $filteredTables;
          }

          $monthlyData = [];
          $months = [];

          // Tentukan range bulan
          $startMonth = $startDate ? \Carbon\Carbon::parse($startDate) : \Carbon\Carbon::now()->subMonths(11);
          $endMonth = $endDate ? \Carbon\Carbon::parse($endDate) : \Carbon\Carbon::now();

          $currentMonth = $startMonth->copy();
          while ($currentMonth <= $endMonth) {
               $months[] = $currentMonth->format('M Y');

               foreach ($tables as $table) {
                    if (!isset($monthlyData[$table['key']])) {
                         $monthlyData[$table['key']] = [];
                    }

                    $query = DB::table($table['table'])
                         ->whereYear('created_at', $currentMonth->year)
                         ->whereMonth('created_at', $currentMonth->month)
                         ->where('isDeleted', 0);

                    // Apply additional date filters if needed
                    if ($startDate) {
                         $query->whereDate('created_at', '>=', $startDate);
                    }
                    if ($endDate) {
                         $query->whereDate('created_at', '<=', $endDate);
                    }

                    $count = $query->count();
                    $monthlyData[$table['key']][] = $count;
               }

               $currentMonth->addMonth();
          }

          return [
               'labels' => $months,
               'datasets' => $monthlyData
          ];
     }
}