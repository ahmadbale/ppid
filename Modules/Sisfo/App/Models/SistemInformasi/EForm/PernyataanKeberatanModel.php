<?php

namespace Modules\Sisfo\App\Models\SistemInformasi\EForm;

use Modules\Sisfo\App\Models\Log\NotifAdminModel;
use Modules\Sisfo\App\Models\Log\NotifVerifikatorModel;
use Modules\Sisfo\App\Models\Log\TransactionModel;
use Modules\Sisfo\App\Models\TraitsModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

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
        'pk_review',
        'pk_tanggal_review',
        'pk_tanggal_jawaban',
        'pk_verif_isDeleted'
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
            ->where('pk_verif_isDeleted', 0)
            ->whereNull('pk_sudah_dibaca')
            ->count();
    }

    public static function getDaftarVerifikasi()
    {
        // Mengambil daftar pernyaataan Keberatan untuk verifikasi
        return self::with(['PkDiriSendiri', 'PkOrangLain'])
            ->where('isDeleted', 0)
            ->where('pk_verif_isDeleted', 0)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function validasiDanSetujuiPermohonan()
    {
        // Validasi status
        if ($this->pk_status !== 'Masuk') {
            throw new \Exception('Pengajuan Keberatan sudah diverifikasi sebelumnya');
        }

        // Update status menjadi Verifikasi
        $this->pk_status = 'Verifikasi';
        $this->pk_review = session('alias') ?? 'System';
        $this->pk_tanggal_review = now();
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
        if ($this->pk_status !== 'Masuk') {
            throw new \Exception('Pengajuan sudah diverifikasi sebelumnya');
        }

        // Update status menjadi Ditolak
        $this->pk_status = 'Ditolak';
        $this->pk_alasan_penolakan = $alasanPenolakan;
        $this->pk_review = session('alias') ?? 'System';
        $this->pk_tanggal_review = now();
        $this->save();

        return $this;
    }

    public function validasiDanTandaiDibaca()
    {
        // Validasi status permohonan
        if (!in_array($this->pk_status, ['Verifikasi', 'Ditolak'])) {
            throw new \Exception('Anda harus menyetujui/menolak pengajuan ini terlebih dahulu');
        }

        // Tandai sebagai dibaca
        $this->pk_sudah_dibaca = session('alias') ?? 'System';
        $this->pk_tanggal_dibaca = now();
        $this->save();

        return $this;
    }

    public function validasiDanHapusPermohonan()
    {
        // Validasi status dibaca
        if (empty($this->pk_sudah_dibaca)) {
            throw new \Exception('Anda harus menandai pengajuan ini telah dibaca terlebih dahulu');
        }

        // Update flag hapus
        $this->pk_verif_isDeleted = 1;
        $this->pk_tanggal_dijawab = now();
        $this->save();

        return $this;
    }
}
