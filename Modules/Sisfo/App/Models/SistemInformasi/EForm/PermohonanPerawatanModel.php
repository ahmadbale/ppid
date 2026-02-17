<?php

namespace Modules\Sisfo\App\Models\SistemInformasi\EForm;

use App\Mail\ReviewPermohonanPerawatanMail;
use Modules\Sisfo\App\Models\Log\NotifMasukModel;
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
use Modules\Sisfo\App\Models\Log\NotifVerifModel;

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
        'pp_verifikasi',
        'pp_tanggal_verifikasi',
        'pp_review_sudah_dibaca',
        'pp_review_tanggal_dibaca',
        'pp_dijawab',
        'pp_tanggal_dijawab',
        'pp_verifikasi_isDeleted',
        'pp_review_isDeleted'
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

            // Buat notifikasi Masuk (untuk Admin dan Verifikator)
            $notifMessage = "{$saveData->pp_nama_pengguna} Mengajukan Permohonan Perawatan Sarana Prasarana";
            NotifMasukModel::createData(
                $permohonanPerawatan,
                $notifMessage,
                'E-Form Permohonan Perawatan Sarana Prasarana'
            );

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
            ->where('pp_verifikasi_isDeleted', 0)
            ->whereNull('pp_sudah_dibaca')
            ->count();
    }

    public static function getDaftarVerifikasi()
    {
        // Mengambil daftar permohonan perawatan untuk verifikasi
        return self::where('isDeleted', 0)
            ->where('pp_verifikasi_isDeleted', 0)
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

        $aliasReview = $this->getAliasWithHakAkses();

        // Update status menjadi Verifikasi
        $this->pp_status = 'Verifikasi';
        $this->pp_verifikasi = $aliasReview;
        $this->pp_tanggal_verifikasi = now();
        $this->save();

        // Kirim email notifikasi
        $this->kirimEmailNotifikasi('Disetujui');

        // Kirim WhatsApp notifikasi
        $this->kirimWhatsAppNotifikasi('Disetujui');

        // Buat notifikasi Verifikasi (untuk MPU/Reviewer)
        $pesanNotifVerif = "{$this->pp_nama_pengguna} mengajukan Permohonan Perawatan Sarana Prasarana yang telah disetujui dan memerlukan tindak lanjut.";
        NotifVerifModel::createData(
            $this->permohonan_perawatan_id,
            $pesanNotifVerif,
            'E-Form Permohonan Perawatan Sarana Prasarana'
        );

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

        $aliasReview = $this->getAliasWithHakAkses();

        // Update status menjadi Ditolak
        $this->pp_status = 'Ditolak';
        $this->pp_alasan_penolakan = $alasanPenolakan;
        $this->pp_verifikasi = $aliasReview;
        $this->pp_tanggal_verifikasi = now();
        $this->save();

        // Kirim email notifikasi
        $this->kirimEmailNotifikasi('Ditolak', $alasanPenolakan);

        // Kirim WhatsApp notifikasi
        $this->kirimWhatsAppNotifikasi('Ditolak', $alasanPenolakan);

        // TIDAK membuat notifikasi MPU untuk permohonan yang ditolak
        // (sesuai ketentuan: notif_mpu hanya dibuat ketika disetujui) - SESUAI PERMOHONAN INFORMASI

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

        $aliasDibaca = $this->getAliasWithHakAkses();

        // Tandai sebagai dibaca
        $this->pp_sudah_dibaca = $aliasDibaca;
        $this->pp_tanggal_dibaca = now();
        $this->save();

        $this->updateAllNotifikasi('E-Form Permohonan Perawatan Sarana Prasarana', $this->permohonan_perawatan_id);

        return $this;
    }

    public function validasiDanHapusPermohonan()
    {
        // Validasi status dibaca
        if (empty($this->pp_sudah_dibaca)) {
            throw new \Exception('Anda harus menandai pengajuan ini telah dibaca terlebih dahulu');
        }

        // Update flag hapus
        $this->pp_verifikasi_isDeleted = 1;
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

    public static function hitungJumlahReview()
    {
        // Hanya menghitung review untuk Permohonan Perawatan
        return self::where('pp_status', 'Verifikasi')
            ->where('isDeleted', 0)
            ->where('pp_review_isDeleted', 0)
            ->whereNull('pp_review_sudah_dibaca')
            ->count();
    }

    public static function getDaftarReview()
    {
        // Mengambil daftar Permohonan Perawatan untuk review
        return self::where('isDeleted', 0)
            ->where('pp_review_isDeleted', 0)
            ->where('pp_status', '!=', 'Masuk')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function validasiDanSetujuiReview($jawaban)
    {
        // Validasi jawaban
        $validator = Validator::make(
            ['jawaban' => $jawaban],
            ['jawaban' => 'required|string'],
            [
                'jawaban.required' => 'Jawaban wajib diisi',
                'jawaban.string' => 'Format jawaban tidak valid'
            ]
        );

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        // Validasi status
        if ($this->pp_status !== 'Verifikasi') {
            throw new \Exception('Permohonan Perawatan harus dalam status Verifikasi untuk dapat direview');
        }

        // Ambil nama pengguna yang mereview
        $namaReviewer = Auth::user()->nama_pengguna;

        // Ambil alias dan hak akses untuk format review
        $aliasReview = $this->getAliasWithHakAkses();

        // Update status menjadi Disetujui
        $this->pp_status = 'Disetujui';
        $this->pp_jawaban = $jawaban;
        $this->pp_dijawab = $aliasReview;
        $this->pp_tanggal_dijawab = now();
        $this->save();

        // Kirim email notifikasi
        $this->kirimEmailNotifikasiReview('Disetujui', $jawaban);

        // Kirim WhatsApp notifikasi
        $this->kirimWhatsAppNotifikasiReview('Disetujui', $jawaban);

        // Buat log transaksi untuk persetujuan review
        $aktivitasSetuju = "{$namaReviewer} menyetujui review permohonan perawatan {$this->pp_perawatan_yang_diusulkan}";
        TransactionModel::createData(
            'APPROVED',
            $this->permohonan_perawatan_id,
            $aktivitasSetuju
        );

        return $this;
    }

    public function validasiDanTolakReview($alasanPenolakan)
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
        if ($this->pp_status !== 'Verifikasi') {
            throw new \Exception('Permohonan Perawatan harus dalam status Verifikasi untuk dapat direview');
        }

        // Ambil nama pengguna yang menolak
        $namaPenolak = Auth::user()->nama_pengguna;

        // Ambil alias dan hak akses untuk format review
        $aliasReview = $this->getAliasWithHakAkses();

        // Update status menjadi Ditolak
        $this->pp_status = 'Ditolak';
        $this->pp_alasan_penolakan = $alasanPenolakan;
        $this->pp_dijawab = $aliasReview;
        $this->pp_tanggal_dijawab = now();
        $this->save();

        // Kirim email notifikasi
        $this->kirimEmailNotifikasiReview('Ditolak', null, $alasanPenolakan);

        // Kirim WhatsApp notifikasi
        $this->kirimWhatsAppNotifikasiReview('Ditolak', null, $alasanPenolakan);

        // Buat log transaksi untuk penolakan review
        $aktivitasTolak = "{$namaPenolak} menolak review permohonan perawatan {$this->pp_perawatan_yang_diusulkan} dengan alasan {$alasanPenolakan}";
        TransactionModel::createData(
            'REJECTED',
            $this->permohonan_perawatan_id,
            $aktivitasTolak
        );

        return $this;
    }

    public function validasiDanTandaiDibacaReview()
    {
        // Validasi status Permohonan Perawatan
        if (!in_array($this->pp_status, ['Disetujui', 'Ditolak'])) {
            throw new \Exception('Anda harus menyetujui/menolak review ini terlebih dahulu');
        }

        // Ambil alias dan hak akses untuk format sudah dibaca
        $aliasDibaca = $this->getAliasWithHakAkses();

        // Tandai sebagai dibaca
        $this->pp_review_sudah_dibaca = $aliasDibaca;
        $this->pp_review_tanggal_dibaca = now();
        $this->save();

        // Update notifikasi Verif jika masih NULL
        $this->updateNotifikasiVerif('E-Form Permohonan Perawatan Sarana Prasarana', $this->permohonan_perawatan_id);

        return $this;
    }

    public function validasiDanHapusReview()
    {
        // Validasi status dibaca
        if (empty($this->pp_review_sudah_dibaca)) {
            throw new \Exception('Anda harus menandai review ini telah dibaca terlebih dahulu');
        }

        // Update flag hapus
        $this->pp_review_isDeleted = 1;
        $this->save();

        return $this;
    }

    private function kirimEmailNotifikasiReview($status, $jawaban = null, $alasanPenolakan = null)
    {
        try {
            // Ambil email pemohon
            $email = $this->pp_email_pengguna;

            if (empty($email) || !$this->isValidEmail($email)) {
                Log::info("Email tidak valid atau kosong untuk review Permohonan Perawatan ID: {$this->permohonan_perawatan_id}");
                return;
            }

            // Kirim email
            try {
                Mail::to($email)->send(new ReviewPermohonanPerawatanMail(
                    $this->pp_nama_pengguna,
                    $status,
                    $this->pp_unit_kerja,
                    $this->pp_perawatan_yang_diusulkan,
                    $this->pp_keluhan_kerusakan,
                    $this->pp_lokasi_perawatan,
                    $jawaban,
                    $alasanPenolakan
                ));

                // Log email yang berhasil dikirim
                EmailModel::createData($status, $email);

                Log::info("Email review {$status} berhasil dikirim ke: {$email}");
            } catch (\Exception $e) {
                Log::error("Gagal mengirim email review ke {$email}: " . $e->getMessage());
            }
        } catch (\Exception $e) {
            Log::error("Error saat mengirim email notifikasi review: " . $e->getMessage());
        }
    }

    private function kirimWhatsAppNotifikasiReview($status, $jawaban = null, $alasanPenolakan = null)
    {
        try {
            Log::info("Starting WhatsApp review notification for Permohonan Perawatan ID: {$this->permohonan_perawatan_id}");

            // Ambil nomor HP pemohon
            $nomorHp = $this->pp_no_hp_pengguna;

            if (empty($nomorHp)) {
                Log::info("Nomor WhatsApp kosong untuk review Permohonan Perawatan ID: {$this->permohonan_perawatan_id}");
                return;
            }

            // Inisialisasi WhatsApp service
            $whatsappService = new WhatsAppService();

            Log::info("Attempting to send WhatsApp review to: {$nomorHp}");

            // IMPLEMENTASI SOLUSI: Cek ukuran file dan tentukan strategi pengiriman
            $strategiPengiriman = $this->tentukanStrategiPengiriman($status, $jawaban);

            // Generate pesan WhatsApp berdasarkan strategi
            $pesanWhatsApp = $whatsappService->generatePesanReviewPermohonanPerawatan(
                $this->pp_nama_pengguna,
                $status,
                $this->pp_unit_kerja,
                $this->pp_perawatan_yang_diusulkan,
                $this->pp_keluhan_kerusakan,
                $this->pp_lokasi_perawatan,
                $jawaban,
                $alasanPenolakan,
                $strategiPengiriman
            );

            $berhasil = false;

            // PERBAIKAN: Ubah status log WhatsApp sesuai permintaan
            $statusLog = $status; // Hapus '_REVIEW' suffix

            // Kirim berdasarkan strategi yang ditentukan
            switch ($strategiPengiriman['metode']) {
                case 'kirim_file':
                    // File < 9MB - kirim dengan attachment
                    $berhasil = $whatsappService->kirimPesanDenganFile(
                        $nomorHp,
                        $pesanWhatsApp,
                        $strategiPengiriman['file_path'],
                        $statusLog
                    );
                    Log::info("WhatsApp dengan file dikirim: {$strategiPengiriman['file_path']} ({$strategiPengiriman['ukuran_mb']} MB)");
                    break;

                case 'notif_file_besar':
                    // File > 9MB - kirim pesan notifikasi saja (TETAP BERHASIL)
                    $berhasil = $whatsappService->kirimPesan($nomorHp, $pesanWhatsApp, $statusLog);
                    Log::info("WhatsApp notifikasi file besar dikirim: {$strategiPengiriman['file_path']} ({$strategiPengiriman['ukuran_mb']} MB)");
                    break;

                case 'pesan_biasa':
                    // Jawaban text atau tidak ada file
                    $berhasil = $whatsappService->kirimPesan($nomorHp, $pesanWhatsApp, $statusLog);
                    Log::info("WhatsApp pesan biasa dikirim");
                    break;

                case 'file_tidak_ada':
                    // File tidak ditemukan - kirim notifikasi
                    $berhasil = $whatsappService->kirimPesan($nomorHp, $pesanWhatsApp, $statusLog);
                    Log::warning("File tidak ditemukan: {$strategiPengiriman['file_path']}");
                    break;
            }

            if ($berhasil) {
                Log::info("WhatsApp review {$status} berhasil dikirim ke: {$nomorHp} (strategi: {$strategiPengiriman['metode']})");
            } else {
                Log::error("Gagal mengirim WhatsApp review ke: {$nomorHp}");
            }

            Log::info("WhatsApp review notification process completed");
        } catch (\Exception $e) {
            Log::error("Error saat mengirim WhatsApp notifikasi review: " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());
        }
    }

    /**
     * Tentukan strategi pengiriman berdasarkan ukuran file - SAMA SEPERTI PERMOHONAN INFORMASI
     */
    private function tentukanStrategiPengiriman($status, $jawaban)
    {
        // Default strategy untuk status ditolak atau tidak ada jawaban
        if ($status !== 'Disetujui' || !$jawaban) {
            return [
                'metode' => 'pesan_biasa',
                'ukuran_mb' => 0,
                'file_path' => null,
                'keterangan' => 'Tidak ada file'
            ];
        }

        // Cek apakah jawaban berupa file
        if (!preg_match('/\.(pdf|doc|docx|jpg|jpeg|png|gif)$/i', $jawaban)) {
            return [
                'metode' => 'pesan_biasa',
                'ukuran_mb' => 0,
                'file_path' => null,
                'keterangan' => 'Jawaban berupa teks'
            ];
        }

        // Cek keberadaan dan ukuran file
        $filePath = storage_path('app/public/' . $jawaban);

        if (!file_exists($filePath)) {
            return [
                'metode' => 'file_tidak_ada',
                'ukuran_mb' => 0,
                'file_path' => $filePath,
                'keterangan' => 'File tidak ditemukan'
            ];
        }

        $ukuranByte = filesize($filePath);
        $ukuranMB = round($ukuranByte / 1024 / 1024, 2);

        // PERBAIKAN: Turunkan batas menjadi 9 MB untuk antisipasi selisih ukuran
        $batasUkuranMB = 9;

        Log::info("File size analysis", [
            'file_path' => $filePath,
            'ukuran_byte' => $ukuranByte,
            'ukuran_mb' => $ukuranMB,
            'batas_mb' => $batasUkuranMB
        ]);

        if ($ukuranMB <= $batasUkuranMB) {
            return [
                'metode' => 'kirim_file',
                'ukuran_mb' => $ukuranMB,
                'file_path' => $filePath,
                'keterangan' => 'File kecil, kirim via WhatsApp'
            ];
        } else {
            return [
                'metode' => 'notif_file_besar',
                'ukuran_mb' => $ukuranMB,
                'file_path' => $filePath,
                'keterangan' => 'File besar, hanya notifikasi'
            ];
        }
    }
}
