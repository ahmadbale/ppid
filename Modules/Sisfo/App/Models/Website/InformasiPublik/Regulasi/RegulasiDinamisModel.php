<?php

namespace Modules\Sisfo\App\Models\Website\InformasiPublik\Regulasi;

use Modules\Sisfo\App\Models\Log\TransactionModel;
use Modules\Sisfo\App\Models\TraitsModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class RegulasiDinamisModel extends Model
{
    use TraitsModel;

    protected $table = 'm_regulasi_dinamis';
    protected $primaryKey = 'regulasi_dinamis_id';
    protected $fillable = [
        'rd_judul_reg_dinamis'
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable = array_merge($this->fillable, $this->getCommonFields());
    }

    public static function getDataRegulasi()
    {
        $arr_data = self::query()
            ->from('m_regulasi_dinamis')
            ->select([
                'regulasi_dinamis_id',
                'rd_judul_reg_dinamis'
            ])
            ->where('isDeleted', 0)
            ->get()
            ->map(function ($regulasi_dinamis) {
                // Get categories for this regulasi dinamis
                $kategori_regulasi = DB::table('t_kategori_regulasi')
                    ->select([
                        'kategori_reg_id',
                        'kr_kategori_reg_kode',
                        'kr_nama_kategori',
                        'updated_at'
                    ])
                    ->where('fk_regulasi_dinamis', $regulasi_dinamis->regulasi_dinamis_id)
                    ->where('isDeleted', 0)
                    ->get()
                    ->map(function ($kategori) {
                        // Get regulasi items for this category
                        $regulasi_items = DB::table('t_regulasi')
                            ->select([
                                'regulasi_id',
                                'reg_judul',
                                'reg_tipe_dokumen',
                                'reg_dokumen'
                            ])
                            ->where('fk_t_kategori_regulasi', $kategori->kategori_reg_id)
                            ->where('isDeleted', 0)
                            ->get()
                            ->map(function ($regulasi) {
                                $dokumen_url = $regulasi->reg_tipe_dokumen === 'file'
                                    ? image_asset( $regulasi->reg_dokumen)
                                    : $regulasi->reg_dokumen;

                                return [
                                    'id' => $regulasi->regulasi_id,
                                    'judul' => $regulasi->reg_judul,
                                    'tipe_dokumen' => $regulasi->reg_tipe_dokumen,
                                    'dokumen' => $dokumen_url
                                ];
                            });

                        return [
                            'kategori_id' => $kategori->kategori_reg_id,
                            'kategori_kode' => $kategori->kr_kategori_reg_kode,
                            'kategori_nama' => $kategori->kr_nama_kategori,
                            'updated_at' => $kategori->updated_at,
                            'regulasi_list' => $regulasi_items
                        ];
                    });

                return [
                    'regulasi_dinamis_id' => $regulasi_dinamis->regulasi_dinamis_id,
                    'judul_regulasi' => $regulasi_dinamis->rd_judul_reg_dinamis,
                    'kategori_regulasi' => $kategori_regulasi
                ];
            })
            ->toArray();

        return $arr_data;
    }
    public static function selectData($perPage = null, $search = '')
    {
        $query = self::query()
            ->where('isDeleted', 0);

        // Tambahkan fungsionalitas pencarian
        if (!empty($search)) {
            $query->where('rd_judul_reg_dinamis', 'like', "%{$search}%");
        }

        return self::paginateResults($query, $perPage);
    }

    public static function createData($request)
    {
        try {
            DB::beginTransaction();

            $data = $request->m_regulasi_dinamis;
            $regulasiDinamis = self::create($data);

            TransactionModel::createData(
                'CREATED',
                $regulasiDinamis->regulasi_dinamis_id,
                $regulasiDinamis->rd_judul_reg_dinamis
            );

            DB::commit();

            return self::responFormatSukses($regulasiDinamis, 'Regulasi dinamis berhasil dibuat');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal membuat regulasi dinamis');
        }
    }

    public static function updateData($request, $id)
    {
        try {
            DB::beginTransaction();

            $regulasiDinamis = self::findOrFail($id);

            $data = $request->m_regulasi_dinamis;
            $regulasiDinamis->update($data);

            TransactionModel::createData(
                'UPDATED',
                $regulasiDinamis->regulasi_dinamis_id,
                $regulasiDinamis->rd_judul_reg_dinamis
            );

            DB::commit();

            return self::responFormatSukses($regulasiDinamis, 'Regulasi dinamis berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal memperbarui regulasi dinamis');
        }
    }

    public static function deleteData($id)
    {
        try {
            DB::beginTransaction();

            $regulasiDinamis = self::findOrFail($id);
            // Check if regulasi dinamis use kategori regulasi
            $isUsed = KategoriRegulasiModel::where('fk_regulasi_dinamis', $id)
                ->where('isDeleted', 0)
                ->exists();

            if ($isUsed) {
                DB::rollBack();
                throw new \Exception('Maaf, Regulasi Dinamis masih digunakan di tempat lain');
            }
            $regulasiDinamis->delete();

            TransactionModel::createData(
                'DELETED',
                $regulasiDinamis->regulasi_dinamis_id,
                $regulasiDinamis->rd_judul_reg_dinamis
            );

            DB::commit();

            return self::responFormatSukses($regulasiDinamis, 'Regulasi dinamis berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal menghapus regulasi dinamis');
        }
    }

    public static function detailData($id)
    {
        return self::findOrFail($id);
    }

    public static function validasiData($request)
    {
        $rules = [
            'm_regulasi_dinamis.rd_judul_reg_dinamis' => 'required|max:150',
        ];

        $messages = [
            'm_regulasi_dinamis.rd_judul_reg_dinamis.required' => 'Judul regulasi dinamis wajib diisi',
            'm_regulasi_dinamis.rd_judul_reg_dinamis.max' => 'Judul regulasi dinamis maksimal 150 karakter',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return true;
    }
}