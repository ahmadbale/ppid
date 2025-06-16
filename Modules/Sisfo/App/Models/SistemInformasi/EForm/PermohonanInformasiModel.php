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
use Modules\Sisfo\App\Models\Log\NotifMPUModel;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerifPermohonanInformasiMail;
use App\Services\WhatsAppService;
use Modules\Sisfo\App\Models\Log\EmailModel;

class PermohonanInformasiModel extends Model
{
    use TraitsModel;

    protected $table = 't_permohonan_informasi';
    protected $primaryKey = 'permohonan_informasi_id';
    protected $fillable = [
        'fk_t_form_pi_diri_sendiri',
        'fk_t_form_pi_orang_lain',
        'fk_t_form_pi_organisasi',
        'pi_kategori_pemohon',
        'pi_kategori_aduan',
        'pi_bukti_aduan',
        'pi_informasi_yang_dibutuhkan',
        'pi_alasan_permohonan_informasi',
        'pi_sumber_informasi',
        'pi_alamat_sumber_informasi',
        'pi_status',
        'pi_jawaban',
        'pi_alasan_penolakan',
        'pi_sudah_dibaca',
        'pi_tanggal_dibaca',
        'pi_verifikasi',
        'pi_tanggal_verifikasi',
        'pi_review_sudah_dibaca',
        'pi_review_tanggal_dibaca',
        'pi_dijawab',
        'pi_tanggal_jawaban',
        'pi_verifikasi_isDeleted',
        'pi_review_isDeleted'
    ];

    public function PiDiriSendiri()
    {
        return $this->belongsTo(FormPiDiriSendiriModel::class, 'fk_t_form_pi_diri_sendiri', 'form_pi_diri_sendiri_id');
    }

    public function PiOrangLain()
    {
        return $this->belongsTo(FormPiOrangLainModel::class, 'fk_t_form_pi_orang_lain', 'form_pi_orang_lain_id');
    }

    public function PiOrganisasi()
    {
        return $this->belongsTo(FormPiOrganisasiModel::class, 'fk_t_form_pi_organisasi', 'form_pi_organisasi_id');
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
        DB::beginTransaction();

        try {
            // Pindahkan upload file ke dalam try-catch
            $buktiAduanFile = null;
            if ($request->hasFile('pi_bukti_aduan')) {
                $buktiAduanFile = self::uploadFile(
                    $request->file('pi_bukti_aduan'),
                    'pi_bukti_aduan'
                );
            }

            $data = $request->t_permohonan_informasi;
            $kategoriPemohon = $data['pi_kategori_pemohon'];
            $userLevel = Auth::user()->level->hak_akses_kode;
            $kategoriAduan = $userLevel === 'ADM' ? 'offline' : 'online';

            if ($userLevel === 'ADM') {
                $data['pi_bukti_aduan'] = $buktiAduanFile;
            }

            switch ($kategoriPemohon) {
                case 'Diri Sendiri':
                    $child = FormPiDiriSendiriModel::createData($request);
                    break;
                case 'Orang Lain':
                    $child = FormPiOrangLainModel::createData($request);
                    break;
                case 'Organisasi':
                    $child = FormPiOrganisasiModel::createData($request);
                    break;
                default:
                    throw new \Exception('Kategori pemohon tidak valid');
            }

            $data['pi_kategori_pemohon'] = $kategoriPemohon;
            $data['pi_kategori_aduan'] = $kategoriAduan;
            $data['pi_status'] = 'Masuk';

            $data[$child['pkField']] = $child['id'];
            $saveData = self::create($data);
            $notifMessage = $child['message'];
            $permohonanId = $saveData->permohonan_informasi_id;

            // Buat notifikasi
            NotifAdminModel::createData($permohonanId, $notifMessage);
            NotifVerifikatorModel::createData($permohonanId, $notifMessage);

            // Catat log transaksi
            TransactionModel::createData(
                'CREATED',
                $saveData->permohonan_informasi_id,
                $saveData->pi_informasi_yang_dibutuhkan
            );

            $result = self::responFormatSukses($saveData, 'Permohonan Informasi berhasil diajukan.');

            DB::commit();
            return $result;
        } catch (ValidationException $e) {
            DB::rollBack();
            if ($buktiAduanFile) {
                self::removeFile($buktiAduanFile);
            }
            return self::responValidatorError($e);
        } catch (\Exception $e) {
            DB::rollBack();
            if ($buktiAduanFile) {
                self::removeFile($buktiAduanFile);
            }
            // Tambahkan log error untuk debugging
            Log::error('Error saat membuat permohonan informasi: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return self::responFormatError($e, 'Terjadi kesalahan saat mengajukan permohonan: ' . $e->getMessage());
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
            't_permohonan_informasi.pi_kategori_pemohon' => 'required',
            't_permohonan_informasi.pi_informasi_yang_dibutuhkan' => 'required',
            't_permohonan_informasi.pi_alasan_permohonan_informasi' => 'required',
            't_permohonan_informasi.pi_sumber_informasi' => 'required',
            't_permohonan_informasi.pi_alamat_sumber_informasi' => 'required',
        ];

        // message validasi dasar
        $message = [
            't_permohonan_informasi.pi_kategori_pemohon.required' => 'Kategori pemohon wajib diisi',
            't_permohonan_informasi.pi_informasi_yang_dibutuhkan.required' => 'Informasi yang dibutuhkan wajib diisi',
            't_permohonan_informasi.pi_alasan_permohonan_informasi.required' => 'Alasan permohonan informasi wajib diisi',
            't_permohonan_informasi.pi_sumber_informasi.required' => 'Sumber informasi wajib diisi',
            't_permohonan_informasi.pi_alamat_sumber_informasi.required' => 'Alamat sumber informasi wajib diisi',
        ];

        // Tambahkan validasi untuk admin jika diperlukan
        if (Auth::user()->level->hak_akses_kode === 'ADM') {
            $rules['pi_bukti_aduan'] = 'required|file|mimes:pdf,jpg,jpeg,png,svg,doc,docx|max:10240';
            $message['pi_bukti_aduan.required'] = 'Bukti aduan wajib diupload untuk Admin';
            $message['pi_bukti_aduan.file'] = 'Bukti aduan harus berupa file';
            $message['pi_bukti_aduan.mimes'] = 'Format file bukti aduan tidak valid';
            $message['pi_bukti_aduan.max'] = 'Ukuran file bukti aduan maksimal 10MB';
        }

        // Validasi berdasarkan kategori pemohon
        $validator = Validator::make($request->all(), $rules, $message);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        // Validasi detail berdasarkan kategori pemohon
        $kategoriPemohon = $request->t_permohonan_informasi['pi_kategori_pemohon'];
        switch ($kategoriPemohon) {
            case 'Diri Sendiri':
                FormPiDiriSendiriModel::validasiData($request);
                break;
            case 'Orang Lain':
                FormPiOrangLainModel::validasiData($request);
                break;
            case 'Organisasi':
                FormPiOrganisasiModel::validasiData($request);
                break;
        }

        return true;
    }

    public static function getTimeline()
    {
        // Menggunakan fungsi dari BaseModelFunction
        return self::getTimelineByKategoriForm('Permohonan Informasi');
    }

    public static function getKetentuanPelaporan()
    {
        // Menggunakan fungsi dari BaseModelFunction
        return self::getKetentuanPelaporanByKategoriForm('Permohonan Informasi');
    }

    public static function hitungJumlahVerifikasi()
    {
        // Hanya menghitung verifikasi untuk Permohonan Informasi
        return self::where('pi_status', 'Masuk')
            ->where('isDeleted', 0)
            ->where('pi_verifikasi_isDeleted', 0)
            ->whereNull('pi_sudah_dibaca')
            ->count();
    }

    public static function getDaftarVerifikasi()
    {
        // Mengambil daftar permohonan untuk verifikasi
        return self::with(['PiDiriSendiri', 'PiOrangLain', 'PiOrganisasi'])
            ->where('isDeleted', 0)
            ->where('pi_verifikasi_isDeleted', 0)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function validasiDanSetujuiPermohonan()
    {
        // Validasi status
        if ($this->pi_status !== 'Masuk') {
            throw new \Exception('Permohonan sudah diverifikasi sebelumnya');
        }

        // Load relasi yang diperlukan
        $this->load(['PiDiriSendiri', 'PiOrangLain', 'PiOrganisasi']);

        // Ambil nama pengguna yang menyetujui
        $namaPenyetuju = Auth::user()->nama_pengguna;

        $aliasReview = $this->getAliasWithHakAkses();

        // Update status menjadi Verifikasi
        $this->pi_status = 'Verifikasi';
        $this->pi_verifikasi = $aliasReview;
        $this->pi_tanggal_verifikasi = now();
        $this->save();

        // Kirim email notifikasi
        $this->kirimEmailNotifikasi('Disetujui');

        // Kirim WhatsApp notifikasi
        $this->kirimWhatsAppNotifikasi('Disetujui');

        // Ambil nama pengaju berdasarkan kategori pemohon
        $namaPengaju = $this->getNamaPengaju();

        // Buat notifikasi MPU (hanya untuk permohonan yang disetujui)
        $pesanNotifMPU = "{$namaPengaju} mengajukan Permohonan Informasi yang telah disetujui dan memerlukan tindak lanjut.";
        NotifMPUModel::createData(
            $this->permohonan_informasi_id,
            $pesanNotifMPU,
            'E-Form Permohonan Informasi'
        );

        // Buat log transaksi untuk persetujuan
        $aktivitasSetuju = "{$namaPenyetuju} menyetujui pengajuan informasi {$this->pi_informasi_yang_dibutuhkan}";
        TransactionModel::createData(
            'APPROVED',
            $this->permohonan_informasi_id,
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
        if ($this->pi_status !== 'Masuk') {
            throw new \Exception('Pengajuan sudah diverifikasi sebelumnya');
        }

        // Load relasi yang diperlukan
        $this->load(['PiDiriSendiri', 'PiOrangLain', 'PiOrganisasi']);

        // Ambil nama pengguna yang menolak
        $namaPenolak = Auth::user()->nama_pengguna;

        $aliasReview = $this->getAliasWithHakAkses();

        // Update status menjadi Ditolak
        $this->pi_status = 'Ditolak';
        $this->pi_alasan_penolakan = $alasanPenolakan;
        $this->pi_verifikasi = $aliasReview;
        $this->pi_tanggal_verifikasi = now();
        $this->save();

        // Kirim email notifikasi
        $this->kirimEmailNotifikasi('Ditolak', $alasanPenolakan);

        // Kirim WhatsApp notifikasi
        $this->kirimWhatsAppNotifikasi('Ditolak', $alasanPenolakan);

        // TIDAK membuat notifikasi MPU untuk permohonan yang ditolak
        // (sesuai ketentuan: notif_mpu hanya dibuat ketika disetujui)

        // Buat log transaksi untuk penolakan
        $aktivitasTolak = "{$namaPenolak} menolak pengajuan informasi {$this->pi_informasi_yang_dibutuhkan} dengan alasan {$alasanPenolakan}";
        TransactionModel::createData(
            'REJECTED',
            $this->permohonan_informasi_id,
            $aktivitasTolak
        );

        return $this;
    }

    public function validasiDanTandaiDibaca()
    {
        // Validasi status permohonan
        if (!in_array($this->pi_status, ['Verifikasi', 'Ditolak'])) {
            throw new \Exception('Anda harus menyetujui/menolak pengajuan ini terlebih dahulu');
        }

        $aliasDibaca = $this->getAliasWithHakAkses();

        // Tandai sebagai dibaca
        $this->pi_sudah_dibaca = $aliasDibaca;
        $this->pi_tanggal_dibaca = now();
        $this->save();

        $this->updateAllNotifikasi('E-Form Permohonan Informasi', $this->permohonan_informasi_id);

        return $this;
    }

    public function validasiDanHapusPermohonan()
    {
        // Validasi status dibaca
        if (empty($this->pi_sudah_dibaca)) {
            throw new \Exception('Anda harus menandai pengajuan ini telah dibaca terlebih dahulu');
        }

        // Update flag hapus
        $this->pi_verifikasi_isDeleted = 1;
        $this->save();

        return $this;
    }

    private function getNamaPengaju()
    {
        switch ($this->pi_kategori_pemohon) {
            case 'Diri Sendiri':
                return $this->PiDiriSendiri->pi_nama_pengguna ?? 'Tidak Diketahui';

            case 'Orang Lain':
                return $this->PiOrangLain->pi_nama_pengguna_informasi ?? 'Tidak Diketahui';

            case 'Organisasi':
                return $this->PiOrganisasi->pi_nama_organisasi ?? 'Tidak Diketahui';

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
                Log::info("Tidak ada email yang valid untuk permohonan ID: {$this->permohonan_informasi_id}");
                return;
            }

            // Kirim email ke setiap alamat yang valid
            foreach ($emailData['emails'] as $email) {
                if ($this->isValidEmail($email)) {
                    try {
                        // Kirim email
                        Mail::to($email)->send(new VerifPermohonanInformasiMail(
                            $emailData['nama'],
                            $status,
                            $this->pi_kategori_pemohon,
                            $emailData['status_pemohon'],
                            $this->pi_informasi_yang_dibutuhkan,
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

        switch ($this->pi_kategori_pemohon) {
            case 'Diri Sendiri':
                if ($this->PiDiriSendiri) {
                    $emails[] = $this->PiDiriSendiri->pi_email_pengguna;
                    $nama = $this->PiDiriSendiri->pi_nama_pengguna;
                    $statusPemohon = 'Perorangan (Diri Sendiri)';
                }
                break;

            case 'Orang Lain':
                if ($this->PiOrangLain) {
                    // Kirim ke 2 email: penginput dan penerima informasi
                    $emails[] = $this->PiOrangLain->pi_email_pengguna_penginput;
                    $emails[] = $this->PiOrangLain->pi_email_pengguna_informasi;
                    $nama = $this->PiOrangLain->pi_nama_pengguna_informasi;
                    $statusPemohon = 'Perorangan (Orang Lain)';
                }
                break;

            case 'Organisasi':
                if ($this->PiOrganisasi) {
                    $emailOrganisasi = $this->PiOrganisasi->pi_email_atau_medsos_organisasi;
                    // Cek apakah format email
                    if ($this->isValidEmail($emailOrganisasi)) {
                        $emails[] = $emailOrganisasi;
                    }
                    $nama = $this->PiOrganisasi->pi_nama_organisasi;
                    $statusPemohon = 'Organisasi';
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
            Log::info("Starting WhatsApp notification for permohonan ID: {$this->permohonan_informasi_id}");

            // Ambil data WhatsApp berdasarkan kategori pemohon
            $whatsappData = $this->getWhatsAppData();

            Log::info("WhatsApp data retrieved:", $whatsappData);

            if (empty($whatsappData['nomor_hp'])) {
                Log::info("Tidak ada nomor WhatsApp yang valid untuk permohonan ID: {$this->permohonan_informasi_id}");
                return;
            }

            // Inisialisasi WhatsApp service
            $whatsappService = new WhatsAppService();

            // Generate pesan WhatsApp
            $pesanWhatsApp = $whatsappService->generatePesanVerifikasi(
                $whatsappData['nama'],
                $status,
                $this->pi_kategori_pemohon,
                $this->pi_informasi_yang_dibutuhkan,
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
                    Log::warning("Nomor WhatsApp kosong untuk kategori: {$this->pi_kategori_pemohon} index: {$index}");
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

        switch ($this->pi_kategori_pemohon) {
            case 'Diri Sendiri':
                if ($this->PiDiriSendiri) {
                    $nomorHp[] = $this->PiDiriSendiri->pi_no_hp_pengguna;
                    $nama = $this->PiDiriSendiri->pi_nama_pengguna;
                }
                break;

            case 'Orang Lain':
                if ($this->PiOrangLain) {
                    // Kirim ke 2 nomor: penginput dan penerima informasi
                    $nomorHp[] = $this->PiOrangLain->pi_no_hp_pengguna_penginput;
                    $nomorHp[] = $this->PiOrangLain->pi_no_hp_pengguna_informasi;
                    $nama = $this->PiOrangLain->pi_nama_pengguna_informasi;
                }
                break;

            case 'Organisasi':
                if ($this->PiOrganisasi) {
                    $nomorHp[] = $this->PiOrganisasi->pi_no_telp_narahubung;
                    $nama = $this->PiOrganisasi->pi_nama_organisasi;
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
        // Hanya menghitung review untuk Permohonan Informasi
        return self::where('pi_status', 'Verifikasi')
            ->where('isDeleted', 0)
            ->where('pi_review_isDeleted', 0)
            ->whereNull('pi_review_sudah_dibaca')
            ->count();
    }

    public static function getDaftarReview()
    {
        // Mengambil daftar permohonan untuk review
        return self::with(['PiDiriSendiri', 'PiOrangLain', 'PiOrganisasi'])
            ->where('isDeleted', 0)
            ->where('pi_review_isDeleted', 0)
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
