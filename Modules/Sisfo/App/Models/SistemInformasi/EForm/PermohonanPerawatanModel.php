<?php

namespace Modules\Sisfo\App\Models\SistemInformasi\EForm;

use Modules\Sisfo\App\Models\Log\NotifAdminModel;
use Modules\Sisfo\App\Models\Log\NotifVerifikatorModel;
use Modules\Sisfo\App\Models\Log\TransactionModel;
use Modules\Sisfo\App\Models\TraitsModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerifPermohonanPerawatanMail;
use App\Services\WhatsAppService;
use Modules\Sisfo\App\Models\Log\EmailModel;

class PermohonanPerawatanModel extends Model
{
    use TraitsModel;

    protected $table = 't_permohonan_perawatan';
    protected $primaryKey = 'permohonan_perawatan_id';
    protected $fillable = [
        'pp_kategori_aduan',
        'pp_bukti_aduan',
        'pp_nama_pengguna',
        'pp_no_hp_pengguna',
        'pp_email_pengguna',
        'pp_unit_kerja',
        'pp_perawatan_yang_diusulkan',
        'pp_keluhan_kerusakan',
        'pp_lokasi_perawatan',
        'pp_foto_kondisi',
        'pp_status',
        'pp_jawaban',
        'pp_alasan_penolakan',
        'pp_sudah_dibaca',
        'pp_tanggal_dibaca',
        'pp_review',
        'pp_tanggal_review',
        'pp_tanggal_dijawab',
        'pp_verif_isDeleted'
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable = array_merge($this->fillable, $this->getCommonFields());
    }

    public static function createData($request)
    {
        // Periksa apakah ada file foto kondisi yang diupload
        $fotoKondisiSarpras = null;
        if ($request->hasFile('pp_foto_kondisi')) {
            $fotoKondisiSarpras = self::uploadFile(
                $request->file('pp_foto_kondisi'),
                'pp_foto_kondisi'
            );
        }

        $buktiAduanFile = self::uploadFile(
            $request->file('pp_bukti_aduan'),
            'pp_bukti_aduan'
        );

        try {
            DB::beginTransaction();

            $data = $request->t_permohonan_perawatan;
            $userLevel = Auth::user()->level->hak_akses_kode;
            $kategoriAduan = $userLevel === 'ADM' ? 'offline' : 'online';

            // Jika user RPN, gunakan data dari auth
            if ($userLevel === 'RPN') {
                $data['pp_nama_pengguna'] = Auth::user()->nama_pengguna;
                $data['pp_no_hp_pengguna'] = Auth::user()->no_hp_pengguna;
                $data['pp_email_pengguna'] = Auth::user()->email_pengguna;
            } else if ($userLevel === 'ADM') {
                $data['pp_bukti_aduan'] = $buktiAduanFile;
            }

            $data['pp_kategori_aduan'] = $kategoriAduan;
            
            // Tetapkan nilai foto kondisi jika ada
            if ($fotoKondisiSarpras) {
                $data['pp_foto_kondisi'] = $fotoKondisiSarpras;
            }
            
            $data['pp_status'] = 'Masuk';

            $saveData = self::create($data);
            $permohonanPerawatan = $saveData->permohonan_perawatan_id;

            // Create notifications
            $notifMessage = "{$saveData->pp_nama_tanpa_gelar} Mengajukan Permohonan Perawatan Sarana Prasarana";
            NotifAdminModel::createData($permohonanPerawatan, $notifMessage);
            NotifVerifikatorModel::createData($permohonanPerawatan, $notifMessage);

            // Mencatat log transaksi
            TransactionModel::createData(
                'CREATED',
                $saveData->permohonan_perawatan_id,
                $saveData->pp_jenis_laporan
            );

            $result = self::responFormatSukses($saveData, 'Permohonan Perawatan Sarana Prasarana berhasil diajukan.');

            DB::commit();

            return $result;
        } catch (ValidationException $e) {
            DB::rollBack();
            if ($fotoKondisiSarpras) {
                self::removeFile($fotoKondisiSarpras);
            }
            self::removeFile($buktiAduanFile);
            return self::responValidatorError($e);
        } catch (\Exception $e) {
            DB::rollBack();
            if ($fotoKondisiSarpras) {
                self::removeFile($fotoKondisiSarpras);
            }
            self::removeFile($buktiAduanFile);
            return self::responFormatError($e, 'Terjadi kesalahan saat mengajukan Permohonan Perawatan Sarana Prasarana');
        }
    }

    public static function validasiData($request)
    {
        // Dapatkan level user saat ini
        $userLevel = Auth::user()->level->hak_akses_kode;

        // rules validasi dasar untuk Permohonan Perawatan Sarana Prasarana
        $rules = [
            't_permohonan_perawatan.pp_unit_kerja' => 'required',
            't_permohonan_perawatan.pp_perawatan_yang_diusulkan' => 'required',
            't_permohonan_perawatan.pp_keluhan_kerusakan' => 'required',
            't_permohonan_perawatan.pp_lokasi_perawatan' => 'required',
            'pp_foto_kondisi' => 'nullable|image|max:10240', // Diubah dari required menjadi nullable
        ];

        // message validasi
        $message = [
            't_permohonan_perawatan.pp_unit_kerja.required' => 'Unit Kerja wajib diisi',
            't_permohonan_perawatan.pp_perawatan_yang_diusulkan.required' => 'Perawatan yang diusulkan wajib diisi',
            't_permohonan_perawatan.pp_keluhan_kerusakan.required' => 'Keluhan Kerusakan wajib diisi',
            't_permohonan_perawatan.pp_lokasi_perawatan.required' => 'Lokasi perawatan wajib diisi',
            'pp_foto_kondisi.image' => 'Foto Kondisi harus berupa gambar',
            'pp_foto_kondisi.mimes' => 'Format file tidak didukung. Format yang didukung: PDF, gambar, dokumen, video (MP4, AVI, MOV, WMV, 3GP), atau audio (MP3, WAV, OGG, M4A)',
            'pp_foto_kondisi.max' => 'Ukuran file tidak boleh lebih dari 10MB',
        ];

        // Tambahkan validasi khusus untuk ADM
        if ($userLevel === 'ADM') {
            $rules['t_permohonan_perawatan.pp_nama_pengguna'] = 'required';
            $rules['t_permohonan_perawatan.pp_email_pengguna'] = 'required|email';
            $rules['t_permohonan_perawatan.pp_no_hp_pengguna'] = 'required';
            $rules['pp_bukti_aduan'] = 'required|file|mimes:pdf,jpg,jpeg,png,svg,doc,docx|max:10240';
            $message['t_permohonan_perawatan.pp_nama_pengguna.required'] = 'Nama Pengusul Wajib diisi';
            $message['t_permohonan_perawatan.pp_email_pengguna.required'] = 'Email Pengusul wajib diisi';
            $message['t_permohonan_perawatan.pp_email_pengguna.email'] = 'Format email tidak valid';
            $message['t_permohonan_perawatan.pp_no_hp_pengguna.required'] = 'Nomor HP Pengusul wajib diisi';
            $message['pp_bukti_aduan.required'] = 'Bukti aduan wajib diupload untuk Admin';
            $message['pp_bukti_aduan.file'] = 'Bukti aduan harus berupa file';
            $message['pp_bukti_aduan.mimes'] = 'Format file bukti aduan tidak valid';
            $message['pp_bukti_aduan.max'] = 'Ukuran file bukti aduan maksimal 10MB';
        }

        // Lakukan validasi
        $validator = Validator::make($request->all(), $rules, $message);

        // Lemparkan exception jika validasi gagal
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return true;
    }
    
    public static function getTimeline()
    {
        // Menggunakan fungsi dari BaseModelFunction
        return self::getTimelineByKategoriForm('Permohonan Perawatan Sarana Prasarana');
    }

    public static function getKetentuanPelaporan()
    {
        // Menggunakan fungsi dari BaseModelFunction
        return self::getKetentuanPelaporanByKategoriForm('Permohonan Perawatan Sarana Prasarana');
    }

    public static function hitungJumlahVerifikasi()
    {
        // Hanya menghitung verifikasi untuk Permohonan Perawatan
        return self::where('pp_status', 'Masuk')
            ->where('isDeleted', 0)
            ->where('pp_verif_isDeleted', 0)
            ->whereNull('pp_sudah_dibaca')
            ->count();
    }

    public static function getDaftarVerifikasi()
    {
        // Mengambil daftar permohonan perawatan untuk verifikasi
        return self::where('isDeleted', 0)
            ->where('pp_verif_isDeleted', 0)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function validasiDanSetujuiPermohonan()
    {
        // Validasi status
        if ($this->pp_status !== 'Masuk') {
            throw new \Exception('Pengajuan Permohonan Perawatan sudah diverifikasi sebelumnya');
        }

        // Ambil nama pengguna yang menyetujui
        $namaPenyetuju = Auth::user()->nama_pengguna;

        // Update status menjadi Verifikasi
        $this->pp_status = 'Verifikasi';
        $this->pp_review = session('alias') ?? 'System';
        $this->pp_tanggal_review = now();
        $this->save();

        // Kirim email notifikasi
        $this->kirimEmailNotifikasi('Disetujui');

        // Kirim WhatsApp notifikasi
        $this->kirimWhatsAppNotifikasi('Disetujui');

        // Buat log transaksi untuk persetujuan
        $aktivitasSetuju = "{$namaPenyetuju} menyetujui permohonan perawatan {$this->pp_perawatan_yang_diusulkan}";
        TransactionModel::createData(
            'APPROVED',
            $this->permohonan_perawatan_id,
            $aktivitasSetuju
        );

        return $this;
    }    

    public function validasiDanTolakPermohonan($alasanPenolakan)
    {
        // Validasi alasan penolakan
        $validator = Validator::make(
            ['alasan_penolakan' => $alasanPenolakan],
            ['alasan_penolakan' => 'required|string|max:255'],
            [
                'alasan_penolakan.required' => 'Alasan penolakan wajib diisi',
                'alasan_penolakan.max' => 'Alasan penolakan maksimal 255 karakter'
            ]
        );

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        // Validasi status
        if ($this->pp_status !== 'Masuk') {
            throw new \Exception('Pengajuan sudah diverifikasi sebelumnya');
        }

        // Ambil nama pengguna yang menolak
        $namaPenolak = Auth::user()->nama_pengguna;

        // Update status menjadi Ditolak
        $this->pp_status = 'Ditolak';
        $this->pp_alasan_penolakan = $alasanPenolakan;
        $this->pp_review = session('alias') ?? 'System';
        $this->pp_tanggal_review = now();
        $this->save();

        // Kirim email notifikasi
        $this->kirimEmailNotifikasi('Ditolak', $alasanPenolakan);

        // Kirim WhatsApp notifikasi
        $this->kirimWhatsAppNotifikasi('Ditolak', $alasanPenolakan);

        // Buat log transaksi untuk penolakan
        $aktivitasTolak = "{$namaPenolak} menolak permohonan perawatan {$this->pp_perawatan_yang_diusulkan} dengan alasan {$alasanPenolakan}";
        TransactionModel::createData(
            'REJECTED',
            $this->permohonan_perawatan_id,
            $aktivitasTolak
        );

        return $this;
    }

    public function validasiDanTandaiDibaca()
    {
        // Validasi status permohonan
        if (!in_array($this->pp_status, ['Verifikasi', 'Ditolak'])) {
            throw new \Exception('Anda harus menyetujui/menolak pengajuan ini terlebih dahulu');
        }

        // Tandai sebagai dibaca
        $this->pp_sudah_dibaca = session('alias') ?? 'System';
        $this->pp_tanggal_dibaca = now();
        $this->save();

        return $this;
    }

    public function validasiDanHapusPermohonan()
    {
        // Validasi status dibaca
        if (empty($this->pp_sudah_dibaca)) {
            throw new \Exception('Anda harus menandai pengajuan ini telah dibaca terlebih dahulu');
        }

        // Update flag hapus
        $this->pp_verif_isDeleted = 1;
        $this->pp_tanggal_dijawab = now();
        $this->save();

        return $this;
    }

    private function kirimEmailNotifikasi($status, $alasanPenolakan = null)
    {
        try {
            // Ambil email pemohon
            $email = $this->pp_email_pengguna;

            if (empty($email) || !$this->isValidEmail($email)) {
                Log::info("Email tidak valid atau kosong untuk permohonan perawatan ID: {$this->permohonan_perawatan_id}");
                return;
            }

            // Kirim email
            try {
                Mail::to($email)->send(new VerifPermohonanPerawatanMail(
                    $this->pp_nama_pengguna,
                    $status,
                    $this->pp_unit_kerja,
                    $this->pp_perawatan_yang_diusulkan,
                    $this->pp_keluhan_kerusakan,
                    $this->pp_lokasi_perawatan,
                    $alasanPenolakan
                ));

                // Log email yang berhasil dikirim
                EmailModel::createData($status, $email);

                Log::info("Email {$status} berhasil dikirim ke: {$email}");
            } catch (\Exception $e) {
                Log::error("Gagal mengirim email ke {$email}: " . $e->getMessage());
            }

        } catch (\Exception $e) {
            Log::error("Error saat mengirim email notifikasi: " . $e->getMessage());
        }
    }

    private function isValidEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    private function kirimWhatsAppNotifikasi($status, $alasanPenolakan = null)
    {
        try {
            Log::info("Starting WhatsApp notification for permohonan perawatan ID: {$this->permohonan_perawatan_id}");

            // Ambil nomor HP pemohon
            $nomorHp = $this->pp_no_hp_pengguna;

            if (empty($nomorHp)) {
                Log::info("Nomor WhatsApp kosong untuk permohonan perawatan ID: {$this->permohonan_perawatan_id}");
                return;
            }

            Log::info("WhatsApp data - Nama: {$this->pp_nama_pengguna}, Nomor: {$nomorHp}");

            // Inisialisasi WhatsApp service
            $whatsappService = new WhatsAppService();

            // Generate pesan WhatsApp untuk Permohonan Perawatan
            $pesanWhatsApp = $whatsappService->generatePesanVerifikasiPerawatan(
                $this->pp_nama_pengguna,
                $status,
                $this->pp_unit_kerja,
                $this->pp_perawatan_yang_diusulkan,
                $this->pp_lokasi_perawatan,
                $this->pp_keluhan_kerusakan,
                $alasanPenolakan
            );

            Log::info("Generated WhatsApp message:", ['message' => $pesanWhatsApp]);

            // Kirim WhatsApp
            Log::info("Attempting to send WhatsApp to: {$nomorHp}");

            $berhasil = $whatsappService->kirimPesan($nomorHp, $pesanWhatsApp, $status);

            if ($berhasil) {
                Log::info("WhatsApp {$status} berhasil dikirim ke: {$nomorHp}");
            } else {
                Log::error("Gagal mengirim WhatsApp ke: {$nomorHp}");
            }

            Log::info("WhatsApp notification process completed");
        } catch (\Exception $e) {
            Log::error("Error saat mengirim WhatsApp notifikasi: " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());
        }
    }
}
