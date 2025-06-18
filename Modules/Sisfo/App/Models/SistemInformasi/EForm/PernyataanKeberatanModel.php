<?php

namespace Modules\Sisfo\App\Models\SistemInformasi\EForm;

use App\Mail\ReviewPernyataanKeberatanMail;
use App\Services\WhatsAppService;
use Modules\Sisfo\App\Models\Log\NotifAdminModel;
use Modules\Sisfo\App\Models\Log\NotifVerifikatorModel;
use Modules\Sisfo\App\Models\Log\TransactionModel;
use Modules\Sisfo\App\Models\TraitsModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerifPernyataanKeberatanMail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Modules\Sisfo\App\Models\Log\EmailModel;
use Modules\Sisfo\App\Models\Log\NotifMPUModel;

class PernyataanKeberatanModel extends Model
{
    use TraitsModel;

    protected $table = 't_pernyataan_keberatan';
    protected $primaryKey = 'pernyataan_keberatan_id';
    protected $fillable = [
        'fk_t_form_pk_diri_sendiri',
        'fk_t_form_pk_orang_lain',
        'pk_kategori_pemohon',
        'pk_kategori_aduan',
        'pk_bukti_aduan',
        'pk_alasan_pengajuan_keberatan',
        'pk_kasus_posisi',
        'pk_status',
        'pk_jawaban',
        'pk_alasan_penolakan',
        'pk_sudah_dibaca',
        'pk_tanggal_dibaca',
        'pk_verifikasi',
        'pk_tanggal_verifikasi',
        'pk_review_sudah_dibaca',
        'pk_review_tanggal_dibaca',
        'pk_dijawab',
        'pk_tanggal_dijawab',
        'pk_verifikasi_isDeleted',
        'pk_review_isDeleted'
    ];

    public function PkDiriSendiri()
    {
        return $this->belongsTo(FormPkDiriSendiriModel::class, 'fk_t_form_pk_diri_sendiri', 'form_pk_diri_sendiri_id');
    }
    public function PkOrangLain()
    {
        return $this->belongsTo(FormPkOrangLainModel::class, 'fk_t_form_pk_orang_lain', 'form_pk_orang_lain_id');
    }

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable = array_merge($this->fillable, $this->getCommonFields());
    }

    public static function selectData()
    {
        //
    }

    public static function createData($request)
    {
        $buktiAduanFile = self::uploadFile(
            $request->file('pk_bukti_aduan'),
            'pk_bukti_aduan'
        );

        $notifMessage = '';
        try {
            $data = $request->t_pernyataan_keberatan;
            $kategoriPemohon = $data['pk_kategori_pemohon'];
            $userLevel = Auth::user()->level->hak_akses_kode;
            $kategoriAduan = $userLevel === 'ADM' ? 'offline' : 'online';

            if ($userLevel === 'ADM') {
                $data['pk_bukti_aduan'] = $buktiAduanFile;
            }

            switch ($kategoriPemohon) {

                case 'Diri Sendiri':
                    $child = FormPkDiriSendiriModel::createData($request);
                    break;

                case 'Orang Lain':
                    $child = FormPkOrangLainModel::createData($request);
                    break;
            }

            DB::beginTransaction();

            $data['pk_kategori_pemohon'] = $kategoriPemohon;
            $data['pk_kategori_aduan'] = $kategoriAduan;
            $data['pk_status'] = 'Masuk';

            $data[$child['pkField']] = $child['id'];
            $saveData = self::create($data);
            $notifMessage = $child['message'];
            $pernyataanKeberatanId = $saveData->pernyataan_keberatan_id;

            // Create notifications dengan pernyataan_keberatani_id
            NotifAdminModel::createData($pernyataanKeberatanId, $notifMessage);
            NotifVerifikatorModel::createData($pernyataanKeberatanId, $notifMessage);

            // Mencatat log transaksi
            TransactionModel::createData(
                'CREATED',
                $saveData->pernyataan_keberatan_id,
                $saveData->pk_alasan_pengajuan_keberatan
            );

            $result = self::responFormatSukses($saveData, 'Pernyataan Keberatan berhasil diajukan.');

            DB::commit();

            return $result;
        } catch (ValidationException $e) {
            DB::rollBack();
            self::removeFile($buktiAduanFile);
            return self::responValidatorError($e);
        } catch (\Exception $e) {
            DB::rollBack();
            self::removeFile($buktiAduanFile);
            return self::responFormatError($e, 'Terjadi kesalahan saat mengajukan Pernyataan Keberatan');
        }
    }

    public static function updateData()
    {
        //
    }

    public static function deleteData()
    {
        //
    }

    public static function validasiData($request)
    {
        // rules validasi dasar untuk permohonan informasi
        $rules = [
            't_pernyataan_keberatan.pk_kategori_pemohon' => 'required',
            't_pernyataan_keberatan.pk_alasan_pengajuan_keberatan' => 'required',
            't_pernyataan_keberatan.pk_kasus_posisi' => 'required',
        ];

        // message validasi dasar
        $message = [
            't_pernyataan_keberatan.pk_kategori_pemohon.required' => 'Kategori pemohon wajib diisi',
            't_pernyataan_keberatan.pk_alasan_pengajuan_keberatan.required' => 'Alasan pengajuan keberatan wajib diisi',
            't_pernyataan_keberatan.pk_kasus_posisi.required' => 'Kasus Posisi wajib diisi',
        ];

        // Tambahkan validasi untuk admin jika diperlukan
        if (Auth::user()->level->hak_akses_kode === 'ADM') {
            $rules['pk_bukti_aduan'] = 'required|file|mimes:pdf,jpg,jpeg,png,svg,doc,docx|max:10240';
            $message['pk_bukti_aduan.required'] = 'Bukti aduan wajib diupload untuk Admin';
            $message['pk_bukti_aduan.file'] = 'Bukti aduan harus berupa file';
            $message['pk_bukti_aduan.mimes'] = 'Format file bukti aduan tidak valid';
            $message['pk_bukti_aduan.max'] = 'Ukuran file bukti aduan maksimal 10MB';
        }

        // Validasi berdasarkan kategori pemohon
        $validator = Validator::make($request->all(), $rules, $message);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        // Validasi detail berdasarkan kategori pemohon
        $kategoriPemohon = $request->t_pernyataan_keberatan['pk_kategori_pemohon'];
        switch ($kategoriPemohon) {
            case 'Diri Sendiri':
                FormPkDiriSendiriModel::validasiData($request);
                break;
            case 'Orang Lain':
                FormPkOrangLainModel::validasiData($request);
                break;
        }

        return true;
    }

    public static function getTimeline()
    {
        // Menggunakan fungsi dari BaseModelFunction
        return self::getTimelineByKategoriForm('Pernyataan Keberatan');
    }

    public static function getKetentuanPelaporan()
    {
        // Menggunakan fungsi dari BaseModelFunction
        return self::getKetentuanPelaporanByKategoriForm('Pernyataan Keberatan');
    }

    public static function hitungJumlahVerifikasi()
    {
        // Hanya menghitung verifikasi untuk Pernyataan Keberatan
        return self::where('pk_status', 'Masuk')
            ->where('isDeleted', 0)
            ->where('pk_verifikasi_isDeleted', 0)
            ->whereNull('pk_sudah_dibaca')
            ->count();
    }

    public static function getDaftarVerifikasi()
    {
        // Mengambil daftar pernyaataan Keberatan untuk verifikasi
        return self::with(['PkDiriSendiri', 'PkOrangLain'])
            ->where('isDeleted', 0)
            ->where('pk_verifikasi_isDeleted', 0)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function validasiDanSetujuiPermohonan()
    {
        // Validasi status
        if ($this->pk_status !== 'Masuk') {
            throw new \Exception('Pengajuan Keberatan sudah diverifikasi sebelumnya');
        }

        // Load relasi yang diperlukan
        $this->load(['PkDiriSendiri', 'PkOrangLain']);

        // Ambil nama pengguna yang menyetujui
        $namaPenyetuju = Auth::user()->nama_pengguna;

        $aliasReview = $this->getAliasWithHakAkses();

        // Update status menjadi Verifikasi
        $this->pk_status = 'Verifikasi';
        $this->pk_verifikasi = $aliasReview;
        $this->pk_tanggal_verifikasi = now();
        $this->save();

        // Kirim email notifikasi
        $this->kirimEmailNotifikasi('Disetujui');

        // Kirim WhatsApp notifikasi
        $this->kirimWhatsAppNotifikasi('Disetujui');

        // Ambil nama pengaju berdasarkan kategori pemohon
        $namaPengaju = $this->getNamaPengaju();

        // Buat notifikasi MPU (hanya untuk permohonan yang disetujui)
        $pesanNotifMPU = "{$namaPengaju} mengajukan Pernyataan Keberatan yang telah disetujui dan memerlukan tindak lanjut.";
        NotifMPUModel::createData(
            $this->pernyataan_keberatan_id,
            $pesanNotifMPU,
            'E-Form Pernyataan Keberatan',
        );

        // Buat log transaksi untuk persetujuan
        $aktivitasSetuju = "{$namaPenyetuju} menyetujui pengajuan keberatan {$this->pk_alasan_pengajuan_keberatan}";
        TransactionModel::createData(
            'APPROVED',
            $this->pernyataan_keberatan_id,
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
        if ($this->pk_status !== 'Masuk') {
            throw new \Exception('Pengajuan sudah diverifikasi sebelumnya');
        }

        // Load relasi yang diperlukan
        $this->load(['PkDiriSendiri', 'PkOrangLain']);

        // Ambil nama pengguna yang menolak
        $namaPenolak = Auth::user()->nama_pengguna;

        $aliasReview = $this->getAliasWithHakAkses();

        // Update status menjadi Ditolak
        $this->pk_status = 'Ditolak';
        $this->pk_alasan_penolakan = $alasanPenolakan;
        $this->pk_verifikasi = $aliasReview;
        $this->pk_tanggal_verifikasi = now();
        $this->save();

        // Kirim email notifikasi
        $this->kirimEmailNotifikasi('Ditolak', $alasanPenolakan);

        // Kirim WhatsApp notifikasi
        $this->kirimWhatsAppNotifikasi('Ditolak', $alasanPenolakan);

        // Buat log transaksi untuk penolakan
        $aktivitasTolak = "{$namaPenolak} menolak pengajuan keberatan {$this->pk_alasan_pengajuan_keberatan} dengan alasan {$alasanPenolakan}";
        TransactionModel::createData(
            'REJECTED',
            $this->pernyataan_keberatan_id,
            $aktivitasTolak
        );

        return $this;
    }

    public function validasiDanTandaiDibaca()
    {
        // Validasi status permohonan
        if (!in_array($this->pk_status, ['Verifikasi', 'Ditolak'])) {
            throw new \Exception('Anda harus menyetujui/menolak pengajuan ini terlebih dahulu');
        }

        $aliasDibaca = $this->getAliasWithHakAkses();

        // Tandai sebagai dibaca
        $this->pk_sudah_dibaca = $aliasDibaca;
        $this->pk_tanggal_dibaca = now();
        $this->save();

        $this->updateAllNotifikasi('E-Form Pernyataan Keberatan', $this->pernyataan_keberatan_id);

        return $this;
    }

    public function validasiDanHapusPermohonan()
    {
        // Validasi status dibaca
        if (empty($this->pk_sudah_dibaca)) {
            throw new \Exception('Anda harus menandai pengajuan ini telah dibaca terlebih dahulu');
        }

        // Update flag hapus
        $this->pk_verifikasi_isDeleted = 1;
        $this->pk_tanggal_dijawab = now();
        $this->save();

        return $this;
    }

    private function getNamaPengaju()
    {
        switch ($this->pk_kategori_pemohon) {
            case 'Diri Sendiri':
                return $this->PkDiriSendiri->pk_nama_pengguna ?? 'Tidak Diketahui';

            case 'Orang Lain':
                return $this->PkOrangLain->pk_nama_kuasa_pemohon ?? 'Tidak Diketahui';

            default:
                return 'Tidak Diketahui';
        }
    }

    private function kirimEmailNotifikasi($status, $alasanPenolakan = null)
    {
        try {
            // Ambil data email berdasarkan kategori pemohon
            $emailData = $this->getEmailData();

            if (empty($emailData['emails'])) {
                Log::info("Tidak ada email yang valid untuk pernyataan keberatan ID: {$this->pernyataan_keberatan_id}");
                return;
            }

            // Kirim email ke setiap alamat yang valid
            foreach ($emailData['emails'] as $email) {
                if ($this->isValidEmail($email)) {
                    try {
                        // Kirim email
                        Mail::to($email)->send(new VerifPernyataanKeberatanMail(
                            $emailData['nama'],
                            $status,
                            $this->pk_kategori_pemohon,
                            $emailData['status_pemohon'],
                            $this->pk_alasan_pengajuan_keberatan,
                            $this->pk_kasus_posisi,
                            $alasanPenolakan
                        ));

                        // Log email yang berhasil dikirim
                        EmailModel::createData($status, $email);

                        Log::info("Email {$status} berhasil dikirim ke: {$email}");
                    } catch (\Exception $e) {
                        Log::error("Gagal mengirim email ke {$email}: " . $e->getMessage());
                    }
                } else {
                    Log::warning("Email tidak valid: {$email}");
                }
            }
        } catch (\Exception $e) {
            Log::error("Error saat mengirim email notifikasi: " . $e->getMessage());
        }
    }

    private function getEmailData()
    {
        $emails = [];
        $nama = '';
        $statusPemohon = '';

        switch ($this->pk_kategori_pemohon) {
            case 'Diri Sendiri':
                if ($this->PkDiriSendiri) {
                    $emails[] = $this->PkDiriSendiri->pk_email_pengguna;
                    $nama = $this->PkDiriSendiri->pk_nama_pengguna;
                    $statusPemohon = 'Perorangan (Diri Sendiri)';
                }
                break;

            case 'Orang Lain':
                if ($this->PkOrangLain) {
                    // Kirim ke 2 email: penginput dan kuasa pemohon
                    $emails[] = $this->PkOrangLain->pk_email_pengguna_penginput;
                    $emails[] = $this->PkOrangLain->pk_email_kuasa_pemohon;
                    $nama = $this->PkOrangLain->pk_nama_kuasa_pemohon;
                    $statusPemohon = 'Perorangan (Orang Lain)';
                }
                break;
        }

        // Filter email yang kosong
        $emails = array_filter($emails, function ($email) {
            return !empty($email) && $this->isValidEmail($email);
        });

        return [
            'emails' => $emails,
            'nama' => $nama ?: 'Tidak Diketahui',
            'status_pemohon' => $statusPemohon
        ];
    }

    private function isValidEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    private function kirimWhatsAppNotifikasi($status, $alasanPenolakan = null)
    {
        try {
            Log::info("Starting WhatsApp notification for pernyataan keberatan ID: {$this->pernyataan_keberatan_id}");

            // Ambil data WhatsApp berdasarkan kategori pemohon
            $whatsappData = $this->getWhatsAppData();

            Log::info("WhatsApp data retrieved:", $whatsappData);

            if (empty($whatsappData['nomor_hp'])) {
                Log::info("Tidak ada nomor WhatsApp yang valid untuk pernyataan keberatan ID: {$this->pernyataan_keberatan_id}");
                return;
            }

            // Inisialisasi WhatsApp service
            $whatsappService = new WhatsAppService();

            // Generate pesan WhatsApp untuk Pernyataan Keberatan
            $pesanWhatsApp = $whatsappService->generatePesanVerifikasiKeberatan(
                $whatsappData['nama'],
                $status,
                $this->pk_kategori_pemohon,
                $this->pk_alasan_pengajuan_keberatan,
                $this->pk_kasus_posisi,
                $alasanPenolakan
            );

            Log::info("Generated WhatsApp message:", ['message' => $pesanWhatsApp]);

            // Kirim WhatsApp ke setiap nomor yang valid
            foreach ($whatsappData['nomor_hp'] as $index => $nomorHp) {
                if (!empty($nomorHp)) {
                    Log::info("Attempting to send WhatsApp #{$index} to: {$nomorHp}");

                    $berhasil = $whatsappService->kirimPesan($nomorHp, $pesanWhatsApp, $status);

                    if ($berhasil) {
                        Log::info("WhatsApp {$status} berhasil dikirim ke: {$nomorHp}");
                    } else {
                        Log::error("Gagal mengirim WhatsApp ke: {$nomorHp}");
                    }
                } else {
                    Log::warning("Nomor WhatsApp kosong untuk kategori: {$this->pk_kategori_pemohon} index: {$index}");
                }
            }

            Log::info("WhatsApp notification process completed");
        } catch (\Exception $e) {
            Log::error("Error saat mengirim WhatsApp notifikasi: " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());
        }
    }

    private function getWhatsAppData()
    {
        $nomorHp = [];
        $nama = '';

        switch ($this->pk_kategori_pemohon) {
            case 'Diri Sendiri':
                if ($this->PkDiriSendiri) {
                    $nomorHp[] = $this->PkDiriSendiri->pk_no_hp_pengguna;
                    $nama = $this->PkDiriSendiri->pk_nama_pengguna;
                }
                break;

            case 'Orang Lain':
                if ($this->PkOrangLain) {
                    // Kirim ke 2 nomor: penginput dan kuasa pemohon
                    $nomorHp[] = $this->PkOrangLain->pk_no_hp_pengguna_penginput;
                    $nomorHp[] = $this->PkOrangLain->pk_no_hp_kuasa_pemohon;
                    $nama = $this->PkOrangLain->pk_nama_kuasa_pemohon;
                }
                break;
        }

        // Filter nomor HP yang kosong
        $nomorHp = array_filter($nomorHp, function ($nomor) {
            return !empty($nomor);
        });

        return [
            'nomor_hp' => $nomorHp,
            'nama' => $nama ?: 'Tidak Diketahui'
        ];
    }

    public static function hitungJumlahReview()
    {
        // Hanya menghitung review untuk Pernyataan Keberatan
        return self::where('pk_status', 'Verifikasi')
            ->where('isDeleted', 0)
            ->where('pk_review_isDeleted', 0)
            ->whereNull('pk_review_sudah_dibaca')
            ->count();
    }

    public static function getDaftarReview()
    {
        // Mengambil daftar pernyataan keberatan untuk review
        return self::with(['PkDiriSendiri', 'PkOrangLain'])
            ->where('isDeleted', 0)
            ->where('pk_review_isDeleted', 0)
            ->where('pk_status', '!=', 'Masuk')
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
        if ($this->pk_status !== 'Verifikasi') {
            throw new \Exception('Pernyataan keberatan harus dalam status Verifikasi untuk dapat direview');
        }

        // Load relasi yang diperlukan
        $this->load(['PkDiriSendiri', 'PkOrangLain']);

        // Ambil nama pengguna yang mereview
        $namaReviewer = Auth::user()->nama_pengguna;

        // Ambil alias dan hak akses untuk format review
        $aliasReview = $this->getAliasWithHakAkses();

        // Update status menjadi Disetujui
        $this->pk_status = 'Disetujui';
        $this->pk_jawaban = $jawaban;
        $this->pk_dijawab = $aliasReview;
        $this->pk_tanggal_dijawab = now();
        $this->save();

        // Kirim email notifikasi
        $this->kirimEmailNotifikasiReview('Disetujui', $jawaban);

        // Kirim WhatsApp notifikasi
        $this->kirimWhatsAppNotifikasiReview('Disetujui', $jawaban);

        // Buat log transaksi untuk persetujuan review
        $aktivitasSetuju = "{$namaReviewer} menyetujui review pernyataan keberatan {$this->pk_alasan_pengajuan_keberatan}";
        TransactionModel::createData(
            'APPROVED',
            $this->pernyataan_keberatan_id,
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
        if ($this->pk_status !== 'Verifikasi') {
            throw new \Exception('Pernyataan keberatan harus dalam status Verifikasi untuk dapat direview');
        }

        // Load relasi yang diperlukan
        $this->load(['PkDiriSendiri', 'PkOrangLain']);

        // Ambil nama pengguna yang menolak
        $namaPenolak = Auth::user()->nama_pengguna;

        // Ambil alias dan hak akses untuk format review
        $aliasReview = $this->getAliasWithHakAkses();

        // Update status menjadi Ditolak
        $this->pk_status = 'Ditolak';
        $this->pk_alasan_penolakan = $alasanPenolakan;
        $this->pk_dijawab = $aliasReview;
        $this->pk_tanggal_dijawab = now();
        $this->save();

        // Kirim email notifikasi
        $this->kirimEmailNotifikasiReview('Ditolak', null, $alasanPenolakan);

        // Kirim WhatsApp notifikasi
        $this->kirimWhatsAppNotifikasiReview('Ditolak', null, $alasanPenolakan);

        // Buat log transaksi untuk penolakan review
        $aktivitasTolak = "{$namaPenolak} menolak review pernyataan keberatan {$this->pk_alasan_pengajuan_keberatan} dengan alasan {$alasanPenolakan}";
        TransactionModel::createData(
            'REJECTED',
            $this->pernyataan_keberatan_id,
            $aktivitasTolak
        );

        return $this;
    }

    public function validasiDanTandaiDibacaReview()
    {
        // Validasi status pernyataan keberatan
        if (!in_array($this->pk_status, ['Disetujui', 'Ditolak'])) {
            throw new \Exception('Anda harus menyetujui/menolak review ini terlebih dahulu');
        }

        // Ambil alias dan hak akses untuk format sudah dibaca
        $aliasDibaca = $this->getAliasWithHakAkses();

        // Tandai sebagai dibaca
        $this->pk_review_sudah_dibaca = $aliasDibaca;
        $this->pk_review_tanggal_dibaca = now();
        $this->save();

        // Update notifikasi MPU jika masih NULL
        $this->updateNotifikasiMPU();

        return $this;
    }

    public function validasiDanHapusReview()
    {
        // Validasi status dibaca
        if (empty($this->pk_review_sudah_dibaca)) {
            throw new \Exception('Anda harus menandai review ini telah dibaca terlebih dahulu');
        }

        // Update flag hapus
        $this->pk_review_isDeleted = 1;
        $this->save();

        return $this;
    }

    private function updateNotifikasiMPU()
    {
        try {
            $notifikasiMPU = NotifMPUModel::where('kategori_notif_mpu', 'E-Form Pernyataan Keberatan')
                ->where('notif_mpu_form_id', $this->pernyataan_keberatan_id)
                ->where('isDeleted', 0)
                ->whereNull('sudah_dibaca_notif_mpu')
                ->get();

            if ($notifikasiMPU->isNotEmpty()) {
                foreach ($notifikasiMPU as $notif) {
                    $notif->sudah_dibaca_notif_mpu = now();
                    $notif->save();
                }

                Log::info("Updated {$notifikasiMPU->count()} MPU notifications for pernyataan keberatan ID: {$this->pernyataan_keberatan_id}");
            }
        } catch (\Exception $e) {
            Log::error("Error updating MPU notifications: " . $e->getMessage());
        }
    }

    private function kirimEmailNotifikasiReview($status, $jawaban = null, $alasanPenolakan = null)
    {
        try {
            // Ambil data email berdasarkan kategori pemohon
            $emailData = $this->getEmailData();

            if (empty($emailData['emails'])) {
                Log::info("Tidak ada email yang valid untuk review pernyataan keberatan ID: {$this->pernyataan_keberatan_id}");
                return;
            }

            // Kirim email ke setiap alamat yang valid
            foreach ($emailData['emails'] as $email) {
                if ($this->isValidEmail($email)) {
                    try {
                        // Kirim email
                        Mail::to($email)->send(new ReviewPernyataanKeberatanMail(
                            $emailData['nama'],
                            $status,
                            $this->pk_kategori_pemohon,
                            $this->pk_alasan_pengajuan_keberatan,
                            $this->pk_kasus_posisi,
                            $jawaban,
                            $alasanPenolakan
                        ));

                        // Log email yang berhasil dikirim
                        EmailModel::createData($status, $email);

                        Log::info("Email review {$status} berhasil dikirim ke: {$email}");
                    } catch (\Exception $e) {
                        Log::error("Gagal mengirim email review ke {$email}: " . $e->getMessage());
                    }
                } else {
                    Log::warning("Email tidak valid: {$email}");
                }
            }
        } catch (\Exception $e) {
            Log::error("Error saat mengirim email notifikasi review: " . $e->getMessage());
        }
    }

    private function kirimWhatsAppNotifikasiReview($status, $jawaban = null, $alasanPenolakan = null)
    {
        try {
            Log::info("Starting WhatsApp review notification for pernyataan keberatan ID: {$this->pernyataan_keberatan_id}");

            // Ambil data WhatsApp berdasarkan kategori pemohon
            $whatsappData = $this->getWhatsAppData();

            if (empty($whatsappData['nomor_hp'])) {
                Log::info("Tidak ada nomor WhatsApp yang valid untuk review pernyataan keberatan ID: {$this->pernyataan_keberatan_id}");
                return;
            }

            // Inisialisasi WhatsApp service
            $whatsappService = new WhatsAppService();

            // Kirim WhatsApp ke setiap nomor yang valid
            foreach ($whatsappData['nomor_hp'] as $index => $nomorHp) {
                if (!empty($nomorHp)) {
                    Log::info("Attempting to send WhatsApp review #{$index} to: {$nomorHp}");

                    // Tentukan strategi pengiriman
                    $strategiPengiriman = $this->tentukanStrategiPengiriman($status, $jawaban);

                    // Generate pesan WhatsApp berdasarkan strategi
                    $pesanWhatsApp = $whatsappService->generatePesanReviewPernyataanKeberatan(
                        $whatsappData['nama'],
                        $status,
                        $this->pk_kategori_pemohon,
                        $this->pk_alasan_pengajuan_keberatan,
                        $this->pk_kasus_posisi,
                        $jawaban,
                        $alasanPenolakan,
                        $strategiPengiriman
                    );

                    $berhasil = false;
                    $statusLog = $status;

                    // Kirim berdasarkan strategi yang ditentukan
                    switch ($strategiPengiriman['metode']) {
                        case 'kirim_file':
                            $berhasil = $whatsappService->kirimPesanDenganFile(
                                $nomorHp,
                                $pesanWhatsApp,
                                $strategiPengiriman['file_path'],
                                $statusLog
                            );
                            Log::info("WhatsApp dengan file dikirim: {$strategiPengiriman['file_path']} ({$strategiPengiriman['ukuran_mb']} MB)");
                            break;

                        case 'notif_file_besar':
                            $berhasil = $whatsappService->kirimPesan($nomorHp, $pesanWhatsApp, $statusLog);
                            Log::info("WhatsApp notifikasi file besar dikirim: {$strategiPengiriman['file_path']} ({$strategiPengiriman['ukuran_mb']} MB)");
                            break;

                        case 'pesan_biasa':
                            $berhasil = $whatsappService->kirimPesan($nomorHp, $pesanWhatsApp, $statusLog);
                            Log::info("WhatsApp pesan biasa dikirim");
                            break;

                        case 'file_tidak_ada':
                            $berhasil = $whatsappService->kirimPesan($nomorHp, $pesanWhatsApp, $statusLog);
                            Log::warning("File tidak ditemukan: {$strategiPengiriman['file_path']}");
                            break;
                    }

                    if ($berhasil) {
                        Log::info("WhatsApp review {$status} berhasil dikirim ke: {$nomorHp} (strategi: {$strategiPengiriman['metode']})");
                    } else {
                        Log::error("Gagal mengirim WhatsApp review ke: {$nomorHp}");
                    }
                }
            }

            Log::info("WhatsApp review notification process completed");
        } catch (\Exception $e) {
            Log::error("Error saat mengirim WhatsApp notifikasi review: " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());
        }
    }

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
        $batasUkuranMB = 9;

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
