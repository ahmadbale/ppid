<?php

namespace Modules\Sisfo\App\Models\SistemInformasi\EForm;

use App\Mail\ReviewWBSMail;
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
use App\Mail\VerifWBSMail;
use App\Services\WhatsAppService;
use Modules\Sisfo\App\Models\Log\EmailModel;
use Modules\Sisfo\App\Models\Log\NotifVerifModel;

class WBSModel extends Model
{
    use TraitsModel;

    protected $table = 't_wbs';
    protected $primaryKey = 'wbs_id';
    protected $fillable = [
        'wbs_kategori_aduan',
        'wbs_bukti_aduan',
        'wbs_nama_tanpa_gelar',
        'wbs_nik_pengguna',
        'wbs_upload_nik_pengguna',
        'wbs_email_pengguna',
        'wbs_no_hp_pengguna',
        'wbs_jenis_laporan',
        'wbs_yang_dilaporkan',
        'wbs_jabatan',
        'wbs_waktu_kejadian',
        'wbs_lokasi_kejadian',
        'wbs_kronologis_kejadian',
        'wbs_bukti_pendukung',
        'wbs_catatan_tambahan',
        'wbs_status',
        'wbs_jawaban',
        'wbs_alasan_penolakan',
        'wbs_sudah_dibaca',
        'wbs_tanggal_dibaca',
        'wbs_verifikasi',
        'wbs_tanggal_verifikasi',
        'wbs_review_sudah_dibaca',
        'wbs_review_tanggal_dibaca',
        'wbs_dijawab',
        'wbs_tanggal_dijawab',
        'wbs_verifikasi_isDeleted',
        'wbs_review_isDeleted'
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable = array_merge($this->fillable, $this->getCommonFields());
    }

    public static function createData($request)
    {
        $uploadNikPelaporFile = self::uploadFile(
            $request->file('wbs_upload_nik_pengguna'),
            'wbs_identitas_pelapor'
        );

        $buktiPendukungFile = self::uploadFile(
            $request->file('wbs_bukti_pendukung'),
            'wbs_bukti_pendukung'
        );

        $buktiAduanFile = self::uploadFile(
            $request->file('wbs_bukti_aduan'),
            'wbs_bukti_aduan'
        );

        try {
            DB::beginTransaction();

            $data = $request->t_wbs;
            $userLevel = Auth::user()->level->hak_akses_kode;
            $kategoriAduan = $userLevel === 'ADM' ? 'offline' : 'online';

            // Jika user RPN, gunakan data dari auth
            if ($userLevel === 'RPN') {
                $data['wbs_no_hp_pengguna'] = Auth::user()->no_hp_pengguna;
                $data['wbs_email_pengguna'] = Auth::user()->email_pengguna;
                $data['wbs_nik_pengguna'] = Auth::user()->nik_pengguna;
                $data['wbs_upload_nik_pengguna'] = Auth::user()->upload_nik_pengguna;
            } else if ($userLevel === 'ADM') {
                $data['wbs_upload_nik_pengguna'] = $uploadNikPelaporFile;
                $data['wbs_bukti_aduan'] = $buktiAduanFile;
            }

            $data['wbs_kategori_aduan'] = $kategoriAduan;
            $data['wbs_bukti_pendukung'] = $buktiPendukungFile;
            $data['wbs_status'] = 'Masuk';

            $saveData = self::create($data);
            $wbsId = $saveData->wbs_id;

            // Buat notifikasi Masuk (untuk Admin dan Verifikator)
            $notifMessage = "{$saveData->wbs_nama_tanpa_gelar} Mengajukan Whistle Blowing System";
            NotifMasukModel::createData(
                $wbsId,
                $notifMessage,
                'E-Form Whistle Blowing System'
            );

            // Mencatat log transaksi
            TransactionModel::createData(
                'CREATED',
                $saveData->wbs_id,
                $saveData->wbs_jenis_laporan
            );

            $result = self::responFormatSukses($saveData, 'Whistle Blowing System berhasil diajukan.');

            DB::commit();

            return $result;
        } catch (ValidationException $e) {
            DB::rollBack();
            self::removeFile($uploadNikPelaporFile);
            self::removeFile($buktiPendukungFile);
            self::removeFile($buktiAduanFile);
            return self::responValidatorError($e);
        } catch (\Exception $e) {
            DB::rollBack();
            self::removeFile($uploadNikPelaporFile);
            self::removeFile($buktiPendukungFile);
            self::removeFile($buktiAduanFile);
            return self::responFormatError($e, 'Terjadi kesalahan saat mengajukan Whistle Blowing System');
        }
    }

    public static function validasiData($request)
    {
        // Dapatkan level user saat ini
        $userLevel = Auth::user()->level->hak_akses_kode;

        // rules validasi dasar untuk Whistle Blowing System
        $rules = [
            't_wbs.wbs_nama_tanpa_gelar' => 'required',
            't_wbs.wbs_jenis_laporan' => 'required',
            't_wbs.wbs_yang_dilaporkan' => 'required',
            't_wbs.wbs_jabatan' => 'required',
            't_wbs.wbs_waktu_kejadian' => 'required|date',
            't_wbs.wbs_lokasi_kejadian' => 'required',
            't_wbs.wbs_kronologis_kejadian' => 'required',
            'wbs_bukti_pendukung' => 'required|file|mimes:pdf,jpg,jpeg,png,svg,doc,docx,mp4,avi,mov,wmv,3gp,mp3,wav,ogg,m4a|max:100000',
        ];

        // message validasi
        $message = [
            't_wbs.wbs_nama_tanpa_gelar.required' => 'Nama wajib diisi',
            't_wbs.wbs_jenis_laporan.required' => 'Jenis laporan wajib diisi',
            't_wbs.wbs_yang_dilaporkan.required' => 'Yang dilaporkan wajib diisi',
            't_wbs.wbs_jabatan.required' => 'Jabatan wajib diisi',
            't_wbs.wbs_waktu_kejadian.required' => 'Waktu kejadian wajib diisi',
            't_wbs.wbs_waktu_kejadian.date' => 'Format tanggal tidak valid',
            't_wbs.wbs_lokasi_kejadian.required' => 'Lokasi kejadian wajib diisi',
            't_wbs.wbs_kronologis_kejadian.required' => 'Kronologis kejadian wajib diisi',
            'wbs_bukti_pendukung.required' => 'Upload Bukti Aduan penginput wajib diisi',
            'wbs_bukti_pendukung.file' => 'Bukti pendukung harus berupa file',
            'wbs_bukti_pendukung.mimes' => 'Format file tidak didukung. Format yang didukung: PDF, gambar, dokumen, video (MP4, AVI, MOV, WMV, 3GP), atau audio (MP3, WAV, OGG, M4A)',
            'wbs_bukti_pendukung.max' => 'Ukuran file tidak boleh lebih dari 100MB',
        ];

        // Tambahkan validasi khusus untuk ADM
        if ($userLevel === 'ADM') {
            $rules['t_wbs.wbs_nik_pengguna'] = 'required|numeric|digits:16';
            $rules['t_wbs.wbs_email_pengguna'] = 'required|email';
            $rules['t_wbs.wbs_no_hp_pengguna'] = 'required';
            $rules['wbs_upload_nik_pengguna'] = 'required|image|max:10240';
            $rules['wbs_bukti_aduan'] = 'required|file|mimes:pdf,jpg,jpeg,png,svg,doc,docx|max:10240';
            $message['t_wbs.wbs_nik_pengguna.required'] = 'NIK wajib diisi';
            $message['t_wbs.wbs_nik_pengguna.numeric'] = 'NIK harus berupa angka';
            $message['t_wbs.wbs_nik_pengguna.digits'] = 'NIK harus 16 digit';
            $message['t_wbs.wbs_email_pengguna.required'] = 'Email wajib diisi';
            $message['t_wbs.wbs_email_pengguna.email'] = 'Format email tidak valid';
            $message['t_wbs.wbs_no_hp_pengguna.required'] = 'Nomor HP wajib diisi';
            $message['wbs_upload_nik_pengguna.required'] = 'Upload NIK wajib diisi';
            $message['wbs_upload_nik_pengguna.image'] = 'File harus berupa gambar';
            $message['wbs_upload_nik_pengguna.max'] = 'Ukuran file tidak boleh lebih dari 10MB';
            $message['wbs_bukti_aduan.required'] = 'Bukti aduan wajib diupload untuk Admin';
            $message['wbs_bukti_aduan.file'] = 'Bukti aduan harus berupa file';
            $message['wbs_bukti_aduan.mimes'] = 'Format file bukti aduan tidak valid';
            $message['wbs_bukti_aduan.max'] = 'Ukuran file bukti aduan maksimal 10MB';
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
        return self::getTimelineByKategoriForm('Whistle Blowing System');
    }

    public static function getKetentuanPelaporan()
    {
        // Menggunakan fungsi dari BaseModelFunction
        return self::getKetentuanPelaporanByKategoriForm('Whistle Blowing System');
    }

    public static function hitungJumlahVerifikasi()
    {
        // Hanya menghitung verifikasi untuk WBS
        return self::where('wbs_status', 'Masuk')
            ->where('isDeleted', 0)
            ->where('wbs_verifikasi_isDeleted', 0)
            ->whereNull('wbs_sudah_dibaca')
            ->count();
    }

    public static function getDaftarVerifikasi()
    {
        // Mengambil daftar WBS untuk verifikasi
        return self::where('isDeleted', 0)
            ->where('wbs_verifikasi_isDeleted', 0)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function validasiDanSetujuiPermohonan()
    {
        // Validasi status
        if ($this->wbs_status !== 'Masuk') {
            throw new \Exception('Pengajuan Whistle Blowing System sudah diverifikasi sebelumnya');
        }

        // Ambil nama pengguna yang menyetujui
        $namaPenyetuju = Auth::user()->nama_pengguna;

        $aliasReview = $this->getAliasWithHakAkses();

        // Update status menjadi Verifikasi
        $this->wbs_status = 'Verifikasi';
        $this->wbs_verifikasi = $aliasReview;
        $this->wbs_tanggal_verifikasi = now();
        $this->save();

        // Kirim email notifikasi
        $this->kirimEmailNotifikasi('Disetujui');

        // Kirim WhatsApp notifikasi
        $this->kirimWhatsAppNotifikasi('Disetujui');

        // Buat notifikasi Verifikasi (untuk MPU/Reviewer)
        $pesanNotifVerif = "{$this->wbs_nama_tanpa_gelar} mengajukan Whistle Blowing System yang telah disetujui dan memerlukan tindak lanjut.";
        NotifVerifModel::createData(
            $this->wbs_id,
            $pesanNotifVerif,
            'E-Form Whistle Blowing System'
        );

        // Buat log transaksi untuk persetujuan
        $aktivitasSetuju = "{$namaPenyetuju} menyetujui whistle blowing system {$this->wbs_jenis_laporan}";
        TransactionModel::createData(
            'APPROVED',
            $this->wbs_id,
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
        if ($this->wbs_status !== 'Masuk') {
            throw new \Exception('Pengajuan sudah diverifikasi sebelumnya');
        }

        // Ambil nama pengguna yang menolak
        $namaPenolak = Auth::user()->nama_pengguna;

        $aliasReview = $this->getAliasWithHakAkses();

        // Update status menjadi Ditolak
        $this->wbs_status = 'Ditolak';
        $this->wbs_alasan_penolakan = $alasanPenolakan;
        $this->wbs_verifikasi = $aliasReview;
        $this->wbs_tanggal_verifikasi = now();
        $this->save();

        // Kirim email notifikasi
        $this->kirimEmailNotifikasi('Ditolak', $alasanPenolakan);

        // Kirim WhatsApp notifikasi
        $this->kirimWhatsAppNotifikasi('Ditolak', $alasanPenolakan);

        // TIDAK membuat notifikasi MPU untuk WBS yang ditolak
        // (sesuai ketentuan: notif_mpu hanya dibuat ketika disetujui) - SESUAI PERMOHONAN INFORMASI

        // Buat log transaksi untuk penolakan
        $aktivitasTolak = "{$namaPenolak} menolak whistle blowing system {$this->wbs_jenis_laporan} dengan alasan {$alasanPenolakan}";
        TransactionModel::createData(
            'REJECTED',
            $this->wbs_id,
            $aktivitasTolak
        );

        return $this;
    }

    public function validasiDanTandaiDibaca()
    {
        // Validasi status permohonan
        if (!in_array($this->wbs_status, ['Verifikasi', 'Ditolak'])) {
            throw new \Exception('Anda harus menyetujui/menolak pengajuan ini terlebih dahulu');
        }

        $aliasDibaca = $this->getAliasWithHakAkses();

        // Tandai sebagai dibaca
        $this->wbs_sudah_dibaca = $aliasDibaca;
        $this->wbs_tanggal_dibaca = now();
        $this->save();

        $this->updateAllNotifikasi('E-Form Whistle Blowing System', $this->wbs_id);

        return $this;
    }

    public function validasiDanHapusPermohonan()
    {
        // Validasi status dibaca
        if (empty($this->wbs_sudah_dibaca)) {
            throw new \Exception('Anda harus menandai pengajuan ini telah dibaca terlebih dahulu');
        }

        // Update flag hapus
        $this->wbs_verifikasi_isDeleted = 1;
        $this->wbs_tanggal_dijawab = now();
        $this->save();

        return $this;
    }

    private function kirimEmailNotifikasi($status, $alasanPenolakan = null)
    {
        try {
            // Ambil email pelapor
            $email = $this->wbs_email_pengguna;

            if (empty($email) || !$this->isValidEmail($email)) {
                Log::info("Email tidak valid atau kosong untuk WBS ID: {$this->wbs_id}");
                return;
            }

            // Kirim email
            try {
                Mail::to($email)->send(new VerifWBSMail(
                    $this->wbs_nama_tanpa_gelar,
                    $status,
                    $this->wbs_jenis_laporan,
                    $this->wbs_yang_dilaporkan,
                    $this->wbs_jabatan,
                    $this->wbs_lokasi_kejadian,
                    $this->wbs_waktu_kejadian,
                    $this->wbs_kronologis_kejadian,
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
            Log::info("Starting WhatsApp notification for WBS ID: {$this->wbs_id}");

            // Ambil nomor HP pelapor
            $nomorHp = $this->wbs_no_hp_pengguna;

            if (empty($nomorHp)) {
                Log::info("Nomor WhatsApp kosong untuk WBS ID: {$this->wbs_id}");
                return;
            }

            Log::info("WhatsApp data - Nama: {$this->wbs_nama_tanpa_gelar}, Nomor: {$nomorHp}");

            // Inisialisasi WhatsApp service
            $whatsappService = new WhatsAppService();

            // Generate pesan WhatsApp untuk WBS
            $pesanWhatsApp = $whatsappService->generatePesanVerifikasiWBS(
                $this->wbs_nama_tanpa_gelar,
                $status,
                $this->wbs_jenis_laporan,
                $this->wbs_yang_dilaporkan,
                $this->wbs_jabatan,
                $this->wbs_lokasi_kejadian,
                $this->wbs_waktu_kejadian,
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
        // Hanya menghitung review untuk WBS
        return self::where('wbs_status', 'Verifikasi')
            ->where('isDeleted', 0)
            ->where('wbs_review_isDeleted', 0)
            ->whereNull('wbs_review_sudah_dibaca')
            ->count();
    }

    public static function getDaftarReview()
    {
        // Mengambil daftar WBS untuk review
        return self::where('isDeleted', 0)
            ->where('wbs_review_isDeleted', 0)
            ->where('wbs_status', '!=', 'Masuk')
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
        if ($this->wbs_status !== 'Verifikasi') {
            throw new \Exception('WBS harus dalam status Verifikasi untuk dapat direview');
        }

        // Ambil nama pengguna yang mereview
        $namaReviewer = Auth::user()->nama_pengguna;

        // Ambil alias dan hak akses untuk format review
        $aliasReview = $this->getAliasWithHakAkses();

        // Update status menjadi Disetujui
        $this->wbs_status = 'Disetujui';
        $this->wbs_jawaban = $jawaban;
        $this->wbs_dijawab = $aliasReview;
        $this->wbs_tanggal_dijawab = now();
        $this->save();

        // Kirim email notifikasi
        $this->kirimEmailNotifikasiReview('Disetujui', $jawaban);

        // Kirim WhatsApp notifikasi
        $this->kirimWhatsAppNotifikasiReview('Disetujui', $jawaban);

        // Buat log transaksi untuk persetujuan review
        $aktivitasSetuju = "{$namaReviewer} menyetujui review whistle blowing system {$this->wbs_jenis_laporan}";
        TransactionModel::createData(
            'APPROVED',
            $this->wbs_id,
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
        if ($this->wbs_status !== 'Verifikasi') {
            throw new \Exception('WBS harus dalam status Verifikasi untuk dapat direview');
        }

        // Ambil nama pengguna yang menolak
        $namaPenolak = Auth::user()->nama_pengguna;

        // Ambil alias dan hak akses untuk format review
        $aliasReview = $this->getAliasWithHakAkses();

        // Update status menjadi Ditolak
        $this->wbs_status = 'Ditolak';
        $this->wbs_alasan_penolakan = $alasanPenolakan;
        $this->wbs_dijawab = $aliasReview;
        $this->wbs_tanggal_dijawab = now();
        $this->save();

        // Kirim email notifikasi
        $this->kirimEmailNotifikasiReview('Ditolak', null, $alasanPenolakan);

        // Kirim WhatsApp notifikasi
        $this->kirimWhatsAppNotifikasiReview('Ditolak', null, $alasanPenolakan);

        // Buat log transaksi untuk penolakan review
        $aktivitasTolak = "{$namaPenolak} menolak review whistle blowing system {$this->wbs_jenis_laporan} dengan alasan {$alasanPenolakan}";
        TransactionModel::createData(
            'REJECTED',
            $this->wbs_id,
            $aktivitasTolak
        );

        return $this;
    }

    public function validasiDanTandaiDibacaReview()
    {
        // Validasi status WBS
        if (!in_array($this->wbs_status, ['Disetujui', 'Ditolak'])) {
            throw new \Exception('Anda harus menyetujui/menolak review ini terlebih dahulu');
        }

        // Ambil alias dan hak akses untuk format sudah dibaca
        $aliasDibaca = $this->getAliasWithHakAkses();

        // Tandai sebagai dibaca
        $this->wbs_review_sudah_dibaca = $aliasDibaca;
        $this->wbs_review_tanggal_dibaca = now();
        $this->save();

        // Update notifikasi Verif jika masih NULL
        $this->updateNotifikasiVerif('E-Form Whistle Blowing System', $this->wbs_id);

        return $this;
    }

    public function validasiDanHapusReview()
    {
        // Validasi status dibaca
        if (empty($this->wbs_review_sudah_dibaca)) {
            throw new \Exception('Anda harus menandai review ini telah dibaca terlebih dahulu');
        }

        // Update flag hapus
        $this->wbs_review_isDeleted = 1;
        $this->save();

        return $this;
    }

    private function kirimEmailNotifikasiReview($status, $jawaban = null, $alasanPenolakan = null)
    {
        try {
            // Ambil email pelapor
            $email = $this->wbs_email_pengguna;

            if (empty($email) || !$this->isValidEmail($email)) {
                Log::info("Email tidak valid atau kosong untuk review WBS ID: {$this->wbs_id}");
                return;
            }

            // Kirim email
            try {
                Mail::to($email)->send(new ReviewWBSMail(
                    $this->wbs_nama_tanpa_gelar,
                    $status,
                    $this->wbs_jenis_laporan,
                    $this->wbs_yang_dilaporkan,
                    $this->wbs_jabatan,
                    $this->wbs_lokasi_kejadian,
                    $this->wbs_waktu_kejadian,
                    $this->wbs_kronologis_kejadian,
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
            Log::info("Starting WhatsApp review notification for WBS ID: {$this->wbs_id}");

            // Ambil nomor HP pelapor
            $nomorHp = $this->wbs_no_hp_pengguna;

            if (empty($nomorHp)) {
                Log::info("Nomor WhatsApp kosong untuk review WBS ID: {$this->wbs_id}");
                return;
            }

            // Inisialisasi WhatsApp service
            $whatsappService = new WhatsAppService();

            Log::info("Attempting to send WhatsApp review to: {$nomorHp}");

            // IMPLEMENTASI SOLUSI: Cek ukuran file dan tentukan strategi pengiriman
            $strategiPengiriman = $this->tentukanStrategiPengiriman($status, $jawaban);

            // Generate pesan WhatsApp berdasarkan strategi
            $pesanWhatsApp = $whatsappService->generatePesanReviewWBS(
                $this->wbs_nama_tanpa_gelar,
                $status,
                $this->wbs_jenis_laporan,
                $this->wbs_yang_dilaporkan,
                $this->wbs_jabatan,
                $this->wbs_lokasi_kejadian,
                $this->wbs_waktu_kejadian,
                $this->wbs_kronologis_kejadian,
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
