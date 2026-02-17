<?php

namespace Modules\Sisfo\App\Models;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Modules\Sisfo\App\Models\Log\NotifMasukModel;
use Modules\Sisfo\App\Models\Log\NotifVerifModel;
use Modules\Sisfo\App\Models\SistemInformasi\Timeline\TimelineModel;
use Modules\Sisfo\App\Models\SistemInformasi\KategoriForm\KategoriFormModel;
use Tymon\JWTAuth\Facades\JWTAuth;
trait BaseModelFunction
{
    protected static $defaultPerPage = 10;

    protected $commonFields = [
        'isDeleted',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by'
    ];

    public static function bootBaseModelFunction()
    {
        // Event ketika model dibuat, isi created_by otomatis
        // static::creating(function ($model) {
        //     if (!isset($model->created_by)) {
        //         if (session()->has('alias')) {
        //             $model->created_by = session('alias');
        //         } else {
        //             // Tambahkan default value untuk kasus registrasi
        //             $model->created_by = 'System';
        //         }
        //     }
        // });
        static::creating(function ($model) {
            if (!isset($model->created_by)) {
                Log::info('BaseModelFunction::creating - Model: ' . get_class($model));
                
                $alias = null;
                
                // Prioritas 1: Cek session alias (untuk user yang sudah login via web)
                if (session()->has('alias')) {
                    $alias = session('alias');
                    Log::info('Alias dari session: ' . $alias);
                } else {
                    // Prioritas 2: Cek Auth user (login web/API)
                    $user = Auth::user();
                    
                    // Prioritas 3: Cek JWT token (untuk API)
                    if (!$user) {
                        try {
                            $user = JWTAuth::parseToken()->authenticate();
                            Log::info('User dari JWT token');
                        } catch (\Exception $e) {
                            $user = null;
                            Log::info('Tidak ada JWT token');
                        }
                    }
                    
                    // Jika user ditemukan, ambil alias-nya
                    if ($user && isset($user->alias)) {
                        $alias = $user->alias;
                        Log::info('Alias dari user: ' . $alias);
                    } else {
                        Log::info('User tidak ditemukan atau alias tidak ada');
                    }
                }
                
                // Jika alias masih null (tidak ada user login), set ke 'System'
                $model->created_by = $alias ?? 'System';
                Log::info('created_by diset ke: ' . $model->created_by);
            }
        });
    
        // Event ketika model diupdate, isi updated_by otomatis
        // static::updating(function ($model) {
        //     if (session()->has('alias')) {
        //         $model->updated_by = session('alias');
        //     }

        //     // Pastikan updated_at diisi dengan timestamp sekarang
        //     $model->updated_at = now();
        // });
        static::updating(function ($model) {
            $alias = null;
            
            // Prioritas 1: Cek session alias
            if (session()->has('alias')) {
                $alias = session('alias');
            } else {
                // Prioritas 2: Cek Auth user
                $user = Auth::user();
                
                // Prioritas 3: Cek JWT token
                if (!$user) {
                    try {
                        $user = JWTAuth::parseToken()->authenticate();
                    } catch (\Exception $e) {
                        $user = null;
                    }
                }
                
                // Jika user ditemukan, ambil alias-nya
                if ($user && isset($user->alias)) {
                    $alias = $user->alias;
                }
            }
            
            // Jika alias masih null, set ke 'System'
            $model->updated_by = $alias ?? 'System';
            $model->updated_at = now();
        });

        // Event ketika model dihapus (soft delete), isi deleted_by dan deleted_at otomatis
        // static::deleting(function ($model) {
        //     if (session()->has('alias')) {
        //         $model->deleted_by = session('alias');
        //     } else {
        //         $model->deleted_by = 'System';
        //     }

        //     // Ubah kode ini dari conditional menjadi selalu mengisi
        //     $model->deleted_at = now();
        // });
        static::deleting(function ($model) {
            $alias = null;
            
            // Prioritas 1: Cek session alias
            if (session()->has('alias')) {
                $alias = session('alias');
            } else {
                // Prioritas 2: Cek Auth user
                $user = Auth::user();
                
                // Prioritas 3: Cek JWT token
                if (!$user) {
                    try {
                        $user = JWTAuth::parseToken()->authenticate();
                    } catch (\Exception $e) {
                        $user = null;
                    }
                }
                
                // Jika user ditemukan, ambil alias-nya
                if ($user && isset($user->alias)) {
                    $alias = $user->alias;
                }
            }
            
            // Jika alias masih null, set ke 'System'
            $model->deleted_by = $alias ?? 'System';
            $model->deleted_at = now();
        });
    }

    public function delete()
    {
        // Panggil event deleting yang akan mengisi deleted_by dan deleted_at
        if ($this->fireModelEvent('deleting') === false) {
            return false;
        }

        // Update isDeleted jika belum diubah
        if ($this->isDeleted !== 1) {
            $this->isDeleted = 1;
        }

        // Pastikan deleted_at diisi dengan timestamp sekarang
        // Tambahan ini memastikan field deleted_at selalu terisi
        $this->deleted_at = now();

        // Simpan perubahan
        $this->save();

        // Fire event deleted
        $this->fireModelEvent('deleted');

        return true;
    }

    /**
     * Method paginate untuk digunakan di berbagai model
     * 
     * @param Builder $query Query builder
     * @param int|null $perPage Jumlah item per halaman, null akan menggunakan default
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    protected static function paginateResults(Builder $query, $perPage = null)
    {
        if ($perPage === null) {
            $perPage = static::$defaultPerPage;
        }
        
        return $query->paginate($perPage);
    }

    public function getCommonFields()
    {
        return $this->commonFields;
    }

    protected static function uploadFile($file, $prefix)
    {
        if (!$file) {
            return null;
        }

        $fileName = $prefix . '/' . Str::random(40) . '.' . $file->getClientOriginalExtension();
        $file->storeAs('public', $fileName);
        return $fileName;
    }

    protected static function removeFile($fileName)
    {
        if ($fileName) {
            $filePath = storage_path('app/public/' . $fileName);
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
    }

    protected static function responFormatSukses($data, $message = 'Data berhasil diproses', array $additionalParams = [])
    {
        $response = [
            'success' => true,
            'message' => $message,
            'data' => $data
        ];

        // Menggabungkan parameter tambahan ke dalam respons
        return array_merge($response, $additionalParams);
    }

    protected static function responValidatorError(ValidationException $e, array $additionalParams = [])
    {
        $response = [
            'success' => false,
            'message' => 'Validasi gagal',
            'errors' => $e->errors()
        ];

        // Menggabungkan parameter tambahan ke dalam respons
        return array_merge($response, $additionalParams);
    }

    protected static function responFormatError(\Exception $e, $prefix = 'Terjadi kesalahan saat memproses data', array $additionalParams = [])
    {
        $response = [
            'success' => false,
            'message' => $prefix . ': ' . $e->getMessage()
        ];

        // Menggabungkan parameter tambahan ke dalam respons
        return array_merge($response, $additionalParams);
    }

    /**
     * Mendapatkan data timeline berdasarkan nama kategori form
     *
     * @param string $kategoriFormNama Nama kategori form
     * @return mixed Timeline data atau null jika tidak ditemukan
     */
    protected static function getTimelineByKategoriForm($kategoriFormNama)
    {
        // Ambil ID kategori form berdasarkan nama kategori
        $kategoriForm = KategoriFormModel::where('kf_nama', $kategoriFormNama)
            ->where('isDeleted', 0)
            ->first();

        // Jika kategori form ditemukan, cari timeline terkait
        $timeline = null;
        if ($kategoriForm) {
            $timeline = TimelineModel::with('langkahTimeline')
                ->where('fk_m_kategori_form', $kategoriForm->kategori_form_id)
                ->where('isDeleted', 0)
                ->first();
        }

        return $timeline;
    }

    /**
     * Mendapatkan data ketentuan pelaporan berdasarkan nama kategori form
     *
     * @param string $kategoriFormNama Nama kategori form
     * @return mixed Ketentuan pelaporan data atau null jika tidak ditemukan
     */
    protected static function getKetentuanPelaporanByKategoriForm($kategoriFormNama)
    {
        // Ambil ID kategori form berdasarkan nama kategori
        $kategoriForm = KategoriFormModel::where('kf_nama', $kategoriFormNama)
            ->where('isDeleted', 0)
            ->first();

        // Jika kategori form ditemukan, cari ketentuan pelaporan terkait
        $ketentuanPelaporan = null;
        if ($kategoriForm) {
            $ketentuanPelaporan = DB::table('m_ketentuan_pelaporan')
                ->where('fk_m_kategori_form', $kategoriForm->kategori_form_id)
                ->where('isDeleted', 0)
                ->first();
        }

        return $ketentuanPelaporan;
    }

    /**
     * Get alias with hak akses format: "Alias (Hak Akses)"
     * Method ini dapat digunakan di semua model untuk format yang konsisten
     */
    protected function getAliasWithHakAkses()
    {
        $alias = null;
        
        // Cek session alias (web)
        if (session()->has('alias')) {
            $alias = session('alias');
        } else {
            // Cek user login (web atau API)
            $user = Auth::user();
            if (!$user) {
                // Coba ambil dari JWT jika API
                try {
                    $user = JWTAuth::parseToken()->authenticate();
                } catch (\Exception $e) {
                    $user = null;
                }
            }
            if ($user) {
                // Gunakan properti alias jika ada, fallback ke nama_pengguna
                $alias = $user->alias ?: UserModel::generateAlias($user->nama_pengguna);
            }
        }
        
        // Format dengan hak akses jika bukan System
        if ($alias && $alias !== 'System' && Auth::check()) {
            $hakAksesNama = Auth::user()->level->hak_akses_nama ?? 'Unknown';
            return "{$alias} ({$hakAksesNama})";
        }
        
        return $alias ?? 'System';
    }

    /**
     * Update notifikasi masuk untuk form ini (mark as read)
     * 
     * @param string $kategoriNotif Kategori notifikasi (misal: 'E-Form Permohonan Informasi')
     * @param int $formId ID form yang terkait
     */
    protected function updateNotifikasiMasuk($kategoriNotif, $formId)
    {
        try {
            $notifikasiMasuk = NotifMasukModel::where('notif_masuk_kategori', $kategoriNotif)
                ->where('notif_masuk_form_id', $formId)
                ->where('isDeleted', 0)
                ->whereNull('notif_masuk_dibaca_oleh')
                ->get();

            if ($notifikasiMasuk->isNotEmpty()) {
                foreach ($notifikasiMasuk as $notif) {
                    $notif->notif_masuk_dibaca_oleh = $this->getAliasWithHakAkses();
                    $notif->notif_masuk_dibaca_tgl = now();
                    $notif->save();
                }
                
                Log::info("Updated {$notifikasiMasuk->count()} notif masuk for {$kategoriNotif} ID: {$formId}");
            }
        } catch (\Exception $e) {
            Log::error("Error updating notif masuk for {$kategoriNotif}: " . $e->getMessage());
        }
    }

    /**
     * Update notifikasi verifikasi untuk form ini (mark as read)
     * 
     * @param string $kategoriNotif Kategori notifikasi (misal: 'E-Form Permohonan Informasi')
     * @param int $formId ID form yang terkait
     */
    protected function updateNotifikasiVerif($kategoriNotif, $formId)
    {
        try {
            $notifikasiVerif = NotifVerifModel::where('notif_verif_kategori', $kategoriNotif)
                ->where('notif_verif_form_id', $formId)
                ->where('isDeleted', 0)
                ->whereNull('notif_verif_dibaca_oleh')
                ->get();

            if ($notifikasiVerif->isNotEmpty()) {
                foreach ($notifikasiVerif as $notif) {
                    $notif->notif_verif_dibaca_oleh = $this->getAliasWithHakAkses();
                    $notif->notif_verif_dibaca_tgl = now();
                    $notif->save();
                }
                
                Log::info("Updated {$notifikasiVerif->count()} notif verif for {$kategoriNotif} ID: {$formId}");
            }
        } catch (\Exception $e) {
            Log::error("Error updating notif verif for {$kategoriNotif}: " . $e->getMessage());
        }
    }

    /**
     * Update notifikasi masuk dan verif sekaligus
     * 
     * @param string $kategoriNotif Kategori notifikasi
     * @param int $formId ID form yang terkait
     */
    protected function updateAllNotifikasi($kategoriNotif, $formId)
    {
        $this->updateNotifikasiMasuk($kategoriNotif, $formId);
        $this->updateNotifikasiVerif($kategoriNotif, $formId);
    }
}