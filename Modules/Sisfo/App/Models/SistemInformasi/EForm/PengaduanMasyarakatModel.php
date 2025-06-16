<?php

namespace Modules\Sisfo\App\Models\SistemInformasi\EForm;

use App\Mail\VerifPengaduanMasyarakatMail;
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
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Modules\Sisfo\App\Models\Log\EmailModel;

class PengaduanMasyarakatModel extends Model
{
    use TraitsModel;

    protected $table = 't_pengaduan_masyarakat';
    protected $primaryKey = 'pengaduan_masyarakat_id';
    protected $fillable = [
        'pm_kategori_aduan',
        'pm_bukti_aduan',
        'pm_nama_tanpa_gelar',
        'pm_nik_pengguna',
        'pm_upload_nik_pengguna',
        'pm_email_pengguna',
        'pm_no_hp_pengguna',
        'pm_jenis_laporan',
        'pm_yang_dilaporkan',
        'pm_jabatan',
        'pm_waktu_kejadian',
        'pm_lokasi_kejadian',
        'pm_kronologis_kejadian',
        'pm_bukti_pendukung',
        'pm_catatan_tambahan',
        'pm_status',
        'pm_jawaban',
        'pm_alasan_penolakan',
        'pm_sudah_dibaca',
        'pm_tanggal_dibaca',
        'pm_review',
        'pm_tanggal_review',
        'pm_tanggal_dijawab',
        'pm_verif_isDeleted'
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable = array_merge($this->fillable, $this->getCommonFields());
    }

    public static function createData($request)
    {
        $uploadNikPelaporFile = self::uploadFile(
            $request->file('pm_upload_nik_pengguna'),
            'pm_identitas_pelapor'
        );

        $buktiPendukungFile = self::uploadFile(
            $request->file('pm_bukti_pendukung'),
            'pm_bukti_pendukung'
        );

        $buktiAduanFile = self::uploadFile(
            $request->file('pm_bukti_aduan'),
            'pm_bukti_aduan'
        );

        try {
            DB::beginTransaction();

            $data = $request->t_pengaduan_masyarakat;
            $userLevel = Auth::user()->level->hak_akses_kode;
            $kategoriAduan = $userLevel === 'ADM' ? 'offline' : 'online';

            // Jika user RPN, gunakan data dari auth
            if ($userLevel === 'RPN') {
                $data['pm_no_hp_pengguna'] = Auth::user()->no_hp_pengguna;
                $data['pm_email_pengguna'] = Auth::user()->email_pengguna;
                $data['pm_nik_pengguna'] = Auth::user()->nik_pengguna;
                $data['pm_upload_nik_pengguna'] = Auth::user()->upload_nik_pengguna;
            } else if ($userLevel === 'ADM') {
                $data['pm_upload_nik_pengguna'] = $uploadNikPelaporFile;
                $data['pm_bukti_aduan'] = $buktiAduanFile;
            }

            $data['pm_kategori_aduan'] = $kategoriAduan;
            $data['pm_bukti_pendukung'] = $buktiPendukungFile;
            $data['pm_status'] = 'Masuk';

            $saveData = self::create($data);
            $pengaduanId = $saveData->pengaduan_masyarakat_id;

            // Create notifications
            $notifMessage = "{$saveData->pm_nama_tanpa_gelar} Mengajukan Pengaduan Masyarakat";
            NotifAdminModel::createData($pengaduanId, $notifMessage);
            NotifVerifikatorModel::createData($pengaduanId, $notifMessage);

            // Mencatat log transaksi
            TransactionModel::createData(
                'CREATED',
                $saveData->pengaduan_masyarakat_id,
                $saveData->pm_jenis_laporan
            );

            $result = self::responFormatSukses($saveData, 'Pengaduan Masyarakat berhasil diajukan.');

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
            return self::responFormatError($e, 'Terjadi kesalahan saat mengajukan pengaduan');
        }
    }

    public static function validasiData($request)
    {
        // Dapatkan level user saat ini
        $userLevel = Auth::user()->level->hak_akses_kode;

        // rules validasi dasar untuk pengaduan masyarakat
        $rules = [
            't_pengaduan_masyarakat.pm_nama_tanpa_gelar' => 'required',
            't_pengaduan_masyarakat.pm_jenis_laporan' => 'required',
            't_pengaduan_masyarakat.pm_yang_dilaporkan' => 'required',
            't_pengaduan_masyarakat.pm_jabatan' => 'required',
            't_pengaduan_masyarakat.pm_waktu_kejadian' => 'required|date',
            't_pengaduan_masyarakat.pm_lokasi_kejadian' => 'required',
            't_pengaduan_masyarakat.pm_kronologis_kejadian' => 'required',
            'pm_bukti_pendukung' => 'required|file|mimes:pdf,jpg,jpeg,png,svg,doc,docx,mp4,avi,mov,wmv,3gp,mp3,wav,ogg,m4a|max:100000',
        ];

        // message validasi
        $message = [
            't_pengaduan_masyarakat.pm_nama_tanpa_gelar.required' => 'Nama wajib diisi',
            't_pengaduan_masyarakat.pm_jenis_laporan.required' => 'Jenis laporan wajib diisi',
            't_pengaduan_masyarakat.pm_yang_dilaporkan.required' => 'Yang dilaporkan wajib diisi',
            't_pengaduan_masyarakat.pm_jabatan.required' => 'Jabatan wajib diisi',
            't_pengaduan_masyarakat.pm_waktu_kejadian.required' => 'Waktu kejadian wajib diisi',
            't_pengaduan_masyarakat.pm_waktu_kejadian.date' => 'Format tanggal tidak valid',
            't_pengaduan_masyarakat.pm_lokasi_kejadian.required' => 'Lokasi kejadian wajib diisi',
            't_pengaduan_masyarakat.pm_kronologis_kejadian.required' => 'Kronologis kejadian wajib diisi',
            'pm_bukti_pendukung.required' => 'Upload Bukti Aduan penginput wajib diisi',
            'pm_bukti_pendukung.file' => 'Bukti pendukung harus berupa file',
            'pm_bukti_pendukung.mimes' => 'Format file tidak didukung. Format yang didukung: PDF, gambar, dokumen, video (MP4, AVI, MOV, WMV, 3GP), atau audio (MP3, WAV, OGG, M4A)',
            'pm_bukti_pendukung.max' => 'Ukuran file tidak boleh lebih dari 100MB',
        ];

        // Tambahkan validasi khusus untuk ADM
        // Tambahkan validasi khusus untuk ADM (Admin)
    if ($userLevel === 'ADM') {
        $rules = array_merge($rules, [
            't_pengaduan_masyarakat.pm_nik_pengguna' => 'required|numeric|digits:16',
            't_pengaduan_masyarakat.pm_email_pengguna' => 'required|email',
            't_pengaduan_masyarakat.pm_no_hp_pengguna' => 'required',
            'pm_upload_nik_pengguna' => 'required|image|mimes:jpg,jpeg,png|max:10240',
            'pm_bukti_aduan' => 'required|file|mimes:pdf,jpg,jpeg,png,svg,doc,docx|max:10240',
        ]);

        $message = array_merge($message, [
            't_pengaduan_masyarakat.pm_nik_pengguna.required' => 'NIK wajib diisi untuk Admin',
            't_pengaduan_masyarakat.pm_nik_pengguna.numeric' => 'NIK harus berupa angka',
            't_pengaduan_masyarakat.pm_nik_pengguna.digits' => 'NIK harus 16 digit',
            't_pengaduan_masyarakat.pm_email_pengguna.required' => 'Email wajib diisi untuk Admin',
            't_pengaduan_masyarakat.pm_email_pengguna.email' => 'Format email tidak valid',
            't_pengaduan_masyarakat.pm_no_hp_pengguna.required' => 'Nomor HP wajib diisi untuk Admin',
            'pm_upload_nik_pengguna.required' => 'Upload KTP/NIK wajib diupload untuk Admin',
            'pm_upload_nik_pengguna.image' => 'File KTP/NIK harus berupa gambar',
            'pm_upload_nik_pengguna.mimes' => 'Format file KTP/NIK harus JPG, JPEG, atau PNG',
            'pm_upload_nik_pengguna.max' => 'Ukuran file KTP/NIK tidak boleh lebih dari 10MB',
            'pm_bukti_aduan.required' => 'Bukti aduan wajib diupload untuk Admin',
            'pm_bukti_aduan.file' => 'Bukti aduan harus berupa file',
            'pm_bukti_aduan.mimes' => 'Format file bukti aduan: PDF, JPG, JPEG, PNG, SVG, DOC, DOCX',
            'pm_bukti_aduan.max' => 'Ukuran file bukti aduan maksimal 10MB',
        ]);
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
        return self::getTimelineByKategoriForm('Pengaduan Masyarakat');
    }

    public static function getKetentuanPelaporan()
    {
        // Menggunakan fungsi dari BaseModelFunction
        return self::getKetentuanPelaporanByKategoriForm('Pengaduan Masyarakat');
    }

    public static function hitungJumlahVerifikasi()
    {
        // Hanya menghitung verifikasi untuk Pengaduan Masyarakat
        return self::where('pm_status', 'Masuk')
            ->where('isDeleted', 0)
            ->where('pm_verif_isDeleted', 0)
            ->whereNull('pm_sudah_dibaca')
            ->count();
    }

    public static function getDaftarVerifikasi()
    {
        // Mengambil daftar pengaduan masyarakat untuk verifikasi
        return self::where('isDeleted', 0)
            ->where('pm_verif_isDeleted', 0)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function validasiDanSetujuiPermohonan()
    {
        // Validasi status
        if ($this->pm_status !== 'Masuk') {
            throw new \Exception('Pengajuan Pengaduan Masyarakat sudah diverifikasi sebelumnya');
        }

        // Ambil nama pengguna yang menyetujui
        $namaPenyetuju = Auth::user()->nama_pengguna;

        $aliasReview = $this->getAliasWithHakAkses();

        // Update status menjadi Verifikasi
        $this->pm_status = 'Verifikasi';
        $this->pm_review = $aliasReview;
        $this->pm_tanggal_review = now();
        $this->save();

        // Kirim email notifikasi
        $this->kirimEmailNotifikasi('Disetujui');

        // Kirim WhatsApp notifikasi
        $this->kirimWhatsAppNotifikasi('Disetujui');

        // Buat log transaksi untuk persetujuan
        $aktivitasSetuju = "{$namaPenyetuju} menyetujui pengaduan masyarakat {$this->pm_jenis_laporan}";
        TransactionModel::createData(
            'APPROVED',
            $this->pengaduan_masyarakat_id,
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
        if ($this->pm_status !== 'Masuk') {
            throw new \Exception('Pengajuan sudah diverifikasi sebelumnya');
        }

        // Ambil nama pengguna yang menolak
        $namaPenolak = Auth::user()->nama_pengguna;

        $aliasReview = $this->getAliasWithHakAkses();

        // Update status menjadi Ditolak
        $this->pm_status = 'Ditolak';
        $this->pm_alasan_penolakan = $alasanPenolakan;
        $this->pm_review = $aliasReview;
        $this->pm_tanggal_review = now();
        $this->save();

        // Kirim email notifikasi
        $this->kirimEmailNotifikasi('Ditolak', $alasanPenolakan);

        // Kirim WhatsApp notifikasi
        $this->kirimWhatsAppNotifikasi('Ditolak', $alasanPenolakan);

        // Buat log transaksi untuk penolakan
        $aktivitasTolak = "{$namaPenolak} menolak pengaduan masyarakat {$this->pm_jenis_laporan} dengan alasan {$alasanPenolakan}";
        TransactionModel::createData(
            'REJECTED',
            $this->pengaduan_masyarakat_id,
            $aktivitasTolak
        );

        return $this;
    }

    public function validasiDanTandaiDibaca()
    {
        // Validasi status permohonan
        if (!in_array($this->pm_status, ['Verifikasi', 'Ditolak'])) {
            throw new \Exception('Anda harus menyetujui/menolak pengajuan ini terlebih dahulu');
        }

        $aliasDibaca = $this->getAliasWithHakAkses();

        // Tandai sebagai dibaca
        $this->pm_sudah_dibaca = $aliasDibaca;
        $this->pm_tanggal_dibaca = now();
        $this->save();

        $this->updateAllNotifikasi('E-Form Pengaduan Masyarakat', $this->pengaduan_masyarakat_id);

        return $this;
    }

    public function validasiDanHapusPermohonan()
    {
        // Validasi status dibaca
        if (empty($this->pm_sudah_dibaca)) {
            throw new \Exception('Anda harus menandai pengajuan ini telah dibaca terlebih dahulu');
        }

        // Update flag hapus
        $this->pm_verif_isDeleted = 1;
        $this->pm_tanggal_dijawab = now();
        $this->save();

        return $this;
    }

    private function kirimEmailNotifikasi($status, $alasanPenolakan = null)
    {
        try {
            // Ambil email pengadu
            $email = $this->pm_email_pengguna;

            if (empty($email) || !$this->isValidEmail($email)) {
                Log::info("Email tidak valid atau kosong untuk pengaduan masyarakat ID: {$this->pengaduan_masyarakat_id}");
                return;
            }

            // Kirim email
            try {
                Mail::to($email)->send(new VerifPengaduanMasyarakatMail(
                    $this->pm_nama_tanpa_gelar,
                    $status,
                    $this->pm_jenis_laporan,
                    $this->pm_yang_dilaporkan,
                    $this->pm_lokasi_kejadian,
                    $this->pm_waktu_kejadian,
                    $this->pm_kronologis_kejadian,
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
            Log::info("Starting WhatsApp notification for pengaduan masyarakat ID: {$this->pengaduan_masyarakat_id}");

            // Ambil nomor HP pengadu
            $nomorHp = $this->pm_no_hp_pengguna;

            if (empty($nomorHp)) {
                Log::info("Nomor WhatsApp kosong untuk pengaduan masyarakat ID: {$this->pengaduan_masyarakat_id}");
                return;
            }

            Log::info("WhatsApp data - Nama: {$this->pm_nama_tanpa_gelar}, Nomor: {$nomorHp}");

            // Inisialisasi WhatsApp service
            $whatsappService = new WhatsAppService();

            // Generate pesan WhatsApp untuk Pengaduan Masyarakat
            $pesanWhatsApp = $whatsappService->generatePesanVerifikasiPengaduan(
                $this->pm_nama_tanpa_gelar,
                $status,
                $this->pm_jenis_laporan,
                $this->pm_yang_dilaporkan,
                $this->pm_lokasi_kejadian,
                $this->pm_waktu_kejadian,
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