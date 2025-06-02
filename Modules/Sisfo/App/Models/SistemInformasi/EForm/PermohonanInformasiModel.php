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
        'pi_review',
        'pi_tanggal_review',
        'pi_tanggal_jawaban',
        'pi_verif_isDeleted'
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
            ->where('pi_verif_isDeleted', 0)
            ->whereNull('pi_sudah_dibaca')
            ->count();
    }

    public static function getDaftarVerifikasi()
    {
        // Mengambil daftar permohonan untuk verifikasi
        return self::with(['PiDiriSendiri', 'PiOrangLain', 'PiOrganisasi'])
            ->where('isDeleted', 0)
            ->where('pi_verif_isDeleted', 0)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function validasiDanSetujuiPermohonan()
    {
        // Validasi status
        if ($this->pi_status !== 'Masuk') {
            throw new \Exception('Permohonan sudah diverifikasi sebelumnya');
        }

        // Update status menjadi Verifikasi
        $this->pi_status = 'Verifikasi';
        $this->pi_review = session('alias') ?? 'System';
        $this->pi_tanggal_review = now();
        $this->save();

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

        // Update status menjadi Ditolak
        $this->pi_status = 'Ditolak';
        $this->pi_alasan_penolakan = $alasanPenolakan;
        $this->pi_review = session('alias') ?? 'System';
        $this->pi_tanggal_review = now();
        $this->save();

        return $this;
    }

    public function validasiDanTandaiDibaca()
    {
        // Validasi status permohonan
        if (!in_array($this->pi_status, ['Verifikasi', 'Ditolak'])) {
            throw new \Exception('Anda harus menyetujui/menolak pengajuan ini terlebih dahulu');
        }

        // Tandai sebagai dibaca
        $this->pi_sudah_dibaca = session('alias') ?? 'System';
        $this->pi_tanggal_dibaca = now();
        $this->save();

        return $this;
    }

    public function validasiDanHapusPermohonan()
    {
        // Validasi status dibaca
        if (empty($this->pi_sudah_dibaca)) {
            throw new \Exception('Anda harus menandai pengajuan ini telah dibaca terlebih dahulu');
        }

        // Update flag hapus
        $this->pi_verif_isDeleted = 1;
        $this->pi_tanggal_dijawab = now();
        $this->save();

        return $this;
    }
}
