<?php

namespace Modules\Sisfo\App\Models\Website\InformasiPublik\TabelDinamis;

use Modules\Sisfo\App\Models\TraitsModel;
use Modules\Sisfo\App\Models\Log\TransactionModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class IpMenuUtamaModel extends Model
{
    use TraitsModel;

    protected $table = 't_ip_menu_utama';
    protected $primaryKey = 'ip_menu_utama_id';
    protected $fillable = [
        'fk_t_ip_dinamis_tabel',
        'nama_ip_mu',
        'dokumen_ip_mu'
    ];

    public function IpDinamisTabel()
    {
        return $this->belongsTo(IpDinamisTabelModel::class, 'fk_t_ip_dinamis_tabel', 'ip_dinamis_tabel_id');
    }

    public function IpSubMenuUtama()
    {
        return $this->hasMany(IpSubMenuUtamaModel::class, 'fk_ip_menu_utama', 'ip_menu_utama_id')
            ->where('isDeleted', 0);
    }

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable = array_merge($this->fillable, $this->getCommonFields());
    }

    public static function selectDataWithHierarchy($perPage = null, $search = '')
    {
        // Mengambil data dengan hierarki dan filter cerdas seperti WebMenuGlobal
        return self::getHierarchicalMenusWithSmartFilter($search);
    }

    /**
     * Mengambil data menu dengan struktur hierarkis dan filter cerdas
     */
    private static function getHierarchicalMenusWithSmartFilter($search = '')
    {
        if (empty($search)) {
            // Jika tidak ada pencarian, tampilkan semua data dengan hierarki normal
            return self::where('isDeleted', 0)
                ->with([
                    'IpDinamisTabel',
                    'IpSubMenuUtama' => function ($query) {
                        $query->where('isDeleted', 0)
                            ->with(['IpSubMenu' => function ($subQuery) {
                                $subQuery->where('isDeleted', 0);
                            }]);
                    }
                ])
                ->orderBy('created_at', 'desc')
                ->get();
        }

        // Jika ada pencarian, implementasi filter cerdas
        $foundMenus = collect();

        // 1. Cari semua menu yang cocok dengan pencarian
        $matchingMenus = self::where('isDeleted', 0)
            ->with([
                'IpDinamisTabel',
                'IpSubMenuUtama' => function ($query) {
                    $query->where('isDeleted', 0)
                        ->with(['IpSubMenu' => function ($subQuery) {
                            $subQuery->where('isDeleted', 0);
                        }]);
                }
            ])
            ->where(function ($q) use ($search) {
                $q->where('nama_ip_mu', 'like', "%{$search}%")
                    ->orWhereHas('IpDinamisTabel', function ($kategori) use ($search) {
                        $kategori->where('ip_nama_submenu', 'like', "%{$search}%")
                            ->orWhere('ip_judul', 'like', "%{$search}%");
                    })
                    ->orWhereHas('IpSubMenuUtama', function ($smu) use ($search) {
                        $smu->where('nama_ip_smu', 'like', "%{$search}%")
                            ->orWhereHas('IpSubMenu', function ($sm) use ($search) {
                                $sm->where('nama_ip_sm', 'like', "%{$search}%");
                            });
                    });
            })
            ->get();

        return $matchingMenus;
    }

    /**
     * Mengambil data menu berdasarkan kategori IP dinamis tabel
     */
    public static function getMenusByKategori($kategoriId = null)
    {
        if (empty($kategoriId)) {
            return self::selectDataWithHierarchy();
        }

        return self::where('isDeleted', 0)
            ->where('fk_t_ip_dinamis_tabel', $kategoriId)
            ->with([
                'IpDinamisTabel',
                'IpSubMenuUtama' => function ($query) {
                    $query->where('isDeleted', 0)
                        ->with(['IpSubMenu' => function ($subQuery) {
                            $subQuery->where('isDeleted', 0);
                        }]);
                }
            ])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public static function createDataWithHierarchy($request)
    {
        $dokumenFiles = [];

        try {
            DB::beginTransaction();

            $jumlahMenuUtama = $request->jumlah_menu_utama;
            $createdMenus = [];

            for ($i = 1; $i <= $jumlahMenuUtama; $i++) {
                $namaMenuUtama = $request->{"nama_menu_utama_$i"};
                $adaSubMenuUtama = $request->{"ada_sub_menu_utama_$i"};

                $menuUtamaData = [
                    'fk_t_ip_dinamis_tabel' => $request->fk_t_ip_dinamis_tabel,
                    'nama_ip_mu' => $namaMenuUtama,
                    'dokumen_ip_mu' => null
                ];

                if ($adaSubMenuUtama === 'tidak') {
                    $dokumenFile = self::uploadFile(
                        $request->file("dokumen_menu_utama_$i"),
                        'dokumen_ip_menu_utama'
                    );
                    if ($dokumenFile) {
                        $dokumenFiles[] = $dokumenFile;
                        $menuUtamaData['dokumen_ip_mu'] = $dokumenFile;
                    }
                }

                $menuUtama = self::create($menuUtamaData);
                $createdMenus[] = $menuUtama;

                if ($adaSubMenuUtama === 'ya') {
                    $jumlahSubMenuUtama = $request->{"jumlah_sub_menu_utama_$i"};
                    IpSubMenuUtamaModel::createDataForMenuUtama(
                        $menuUtama->ip_menu_utama_id,
                        $request,
                        $i,
                        $jumlahSubMenuUtama,
                        $dokumenFiles
                    );
                }
            }

            TransactionModel::createData(
                'CREATED',
                $createdMenus[0]->ip_menu_utama_id,
                'Set Informasi Publik Dinamis Tabel - ' . $createdMenus[0]->nama_ip_mu
            );

            DB::commit();
            return self::responFormatSukses($createdMenus, 'Set Informasi Publik Dinamis Tabel berhasil dibuat');
        } catch (\Exception $e) {
            DB::rollBack();
            foreach ($dokumenFiles as $file) {
                self::removeFile($file);
            }
            return self::responFormatError($e, 'Gagal membuat Set Informasi Publik Dinamis Tabel');
        }
    }

    public static function updateDataWithHierarchy($request, $id)
    {
        $dokumenFiles = [];

        try {
            DB::beginTransaction();

            $menuUtama = self::findOrFail($id);
            $oldDokumen = $menuUtama->dokumen_ip_mu;

            // Update data menu utama
            $menuUtamaData = [
                'nama_ip_mu' => $request->nama_ip_mu
            ];

            // Handle dokumen update
            if ($request->hasFile('dokumen_ip_mu')) {
                $dokumenFile = self::uploadFile(
                    $request->file('dokumen_ip_mu'),
                    'dokumen_ip_menu_utama'
                );
                if ($dokumenFile) {
                    $dokumenFiles[] = $dokumenFile;
                    $menuUtamaData['dokumen_ip_mu'] = $dokumenFile;
                    if ($oldDokumen) {
                        self::removeFile($oldDokumen);
                    }
                }
            }

            // Jika menambah sub menu utama, hapus dokumen
            if ($request->has('menambah_sub_menu_utama') && $request->menambah_sub_menu_utama === 'ya') {
                $menuUtamaData['dokumen_ip_mu'] = null;
                if ($oldDokumen) {
                    self::removeFile($oldDokumen);
                }
            }

            $menuUtama->update($menuUtamaData);

            // Update sub menu utama jika diperlukan
            if ($request->has('update_sub_menu_utama')) {
                IpSubMenuUtamaModel::updateDataForMenuUtama($menuUtama->ip_menu_utama_id, $request, $dokumenFiles);
            }

            TransactionModel::createData(
                'UPDATED',
                $menuUtama->ip_menu_utama_id,
                'Set Informasi Publik Dinamis Tabel - ' . $menuUtama->nama_ip_mu
            );

            DB::commit();
            return self::responFormatSukses($menuUtama, 'Set Informasi Publik Dinamis Tabel berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            foreach ($dokumenFiles as $file) {
                self::removeFile($file);
            }
            return self::responFormatError($e, 'Gagal memperbarui Set Informasi Publik Dinamis Tabel');
        }
    }

    public static function deleteDataWithValidation($id)
    {
        try {
            DB::beginTransaction();

            $menuUtama = self::with(['IpSubMenuUtama.IpSubMenu'])->findOrFail($id);

            // Hapus semua sub menu terlebih dahulu
            foreach ($menuUtama->IpSubMenuUtama as $subMenuUtama) {
                // Hapus semua sub menu dari sub menu utama
                foreach ($subMenuUtama->IpSubMenu as $subMenu) {
                    if ($subMenu->dokumen_ip_sm) {
                        self::removeFile($subMenu->dokumen_ip_sm);
                    }
                    $subMenu->delete();
                }

                // Hapus dokumen sub menu utama
                if ($subMenuUtama->dokumen_ip_smu) {
                    self::removeFile($subMenuUtama->dokumen_ip_smu);
                }
                $subMenuUtama->delete();
            }

            // Hapus dokumen menu utama
            if ($menuUtama->dokumen_ip_mu) {
                self::removeFile($menuUtama->dokumen_ip_mu);
            }

            $menuUtama->delete();

            TransactionModel::createData(
                'DELETED',
                $menuUtama->ip_menu_utama_id,
                'Set Informasi Publik Dinamis Tabel - ' . $menuUtama->nama_ip_mu
            );

            DB::commit();
            return self::responFormatSukses($menuUtama, 'Set Informasi Publik Dinamis Tabel berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, $e->getMessage());
        }
    }

    public static function detailDataWithHierarchy($id)
    {
        return self::with([
            'IpDinamisTabel',
            'IpSubMenuUtama' => function ($query) {
                $query->where('isDeleted', 0)
                    ->with(['IpSubMenu' => function ($subQuery) {
                        $subQuery->where('isDeleted', 0);
                    }]);
            }
        ])->findOrFail($id);
    }

    // Validasi data tetap sama seperti sebelumnya
    public static function validasiData($request)
    {
        $rules = [
            'fk_t_ip_dinamis_tabel' => 'required|exists:m_ip_dinamis_tabel,ip_dinamis_tabel_id',
            'jumlah_menu_utama' => 'required|integer|min:1|max:20',
        ];

        $messages = [
            'fk_t_ip_dinamis_tabel.required' => 'Informasi Publik Dinamis Tabel wajib dipilih',
            'fk_t_ip_dinamis_tabel.exists' => 'Informasi Publik Dinamis Tabel tidak valid',
            'jumlah_menu_utama.required' => 'Jumlah Menu Utama wajib diisi',
            'jumlah_menu_utama.integer' => 'Jumlah Menu Utama harus berupa angka',
            'jumlah_menu_utama.min' => 'Minimal 1 Menu Utama',
            'jumlah_menu_utama.max' => 'Maksimal 20 Menu Utama',
        ];

        $jumlahMenuUtama = $request->jumlah_menu_utama;
        if (is_numeric($jumlahMenuUtama)) {
            for ($i = 1; $i <= $jumlahMenuUtama; $i++) {
                $rules["nama_menu_utama_$i"] = 'required|max:255';
                $rules["ada_sub_menu_utama_$i"] = 'required|in:ya,tidak';

                $messages["nama_menu_utama_$i.required"] = "Nama Menu Utama ke-$i wajib diisi";
                $messages["nama_menu_utama_$i.max"] = "Nama Menu Utama ke-$i maksimal 255 karakter";
                $messages["ada_sub_menu_utama_$i.required"] = "Pertanyaan Sub Menu Utama ke-$i wajib dijawab";
                $messages["ada_sub_menu_utama_$i.in"] = "Jawaban Sub Menu Utama ke-$i harus Ya atau Tidak";

                $adaSubMenuUtama = $request->{"ada_sub_menu_utama_$i"};

                if ($adaSubMenuUtama === 'tidak') {
                    $rules["dokumen_menu_utama_$i"] = 'required|file|mimes:pdf|max:5120';
                    $messages["dokumen_menu_utama_$i.required"] = "Dokumen Menu Utama ke-$i wajib diupload";
                    $messages["dokumen_menu_utama_$i.file"] = "Dokumen Menu Utama ke-$i harus berupa file";
                    $messages["dokumen_menu_utama_$i.mimes"] = "Dokumen Menu Utama ke-$i harus berformat PDF";
                    $messages["dokumen_menu_utama_$i.max"] = "Ukuran Dokumen Menu Utama ke-$i maksimal 5 MB";
                } elseif ($adaSubMenuUtama === 'ya') {
                    $rules["jumlah_sub_menu_utama_$i"] = 'required|integer|min:1|max:20';
                    $messages["jumlah_sub_menu_utama_$i.required"] = "Jumlah Sub Menu Utama ke-$i wajib diisi";
                    $messages["jumlah_sub_menu_utama_$i.integer"] = "Jumlah Sub Menu Utama ke-$i harus berupa angka";
                    $messages["jumlah_sub_menu_utama_$i.min"] = "Minimal 1 Sub Menu Utama ke-$i";
                    $messages["jumlah_sub_menu_utama_$i.max"] = "Maksimal 20 Sub Menu Utama ke-$i";
                }
            }
        }

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        // Validasi Sub Menu Utama dan Sub Menu
        IpSubMenuUtamaModel::validasiData($request);

        return true;
    }

    public static function validasiDataUpdate($request)
    {
        $rules = [
            'nama_ip_mu' => 'required|max:255',
        ];

        $messages = [
            'nama_ip_mu.required' => 'Nama Menu Utama wajib diisi',
            'nama_ip_mu.max' => 'Nama Menu Utama maksimal 255 karakter',
        ];

        if ($request->hasFile('dokumen_ip_mu')) {
            $rules['dokumen_ip_mu'] = 'file|mimes:pdf|max:5120';
            $messages['dokumen_ip_mu.file'] = 'Dokumen Menu Utama harus berupa file';
            $messages['dokumen_ip_mu.mimes'] = 'Dokumen Menu Utama harus berformat PDF';
            $messages['dokumen_ip_mu.max'] = 'Ukuran Dokumen Menu Utama maksimal 5 MB';
        }

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return true;
    }

    public static function updateDataWithComplexHierarchy($request, $id)
    {
        $dokumenFiles = [];

        try {
            DB::beginTransaction();

            $menuUtama = self::findOrFail($id);
            $oldDokumen = $menuUtama->dokumen_ip_mu;

            // Update nama menu utama
            $menuUtama->nama_ip_mu = $request->nama_ip_mu;

            // Logic 1: Menu Utama dengan children (memiliki sub menu utama)
            if ($menuUtama->IpSubMenuUtama->count() > 0) {
                // Handle update/delete existing sub menu utama
                foreach ($menuUtama->IpSubMenuUtama as $subMenuUtama) {
                    $updateKey = "update_sub_menu_utama_{$subMenuUtama->ip_sub_menu_utama_id}";
                    $deleteKey = "delete_sub_menu_utama_{$subMenuUtama->ip_sub_menu_utama_id}";

                    if ($request->has($deleteKey)) {
                        // Validasi apakah masih ada children
                        if ($subMenuUtama->IpSubMenu->count() > 0) {
                            throw new \Exception('Sub Menu Utama ini tidak bisa dihapus dikarenakan masih terdapat children');
                        }

                        if ($subMenuUtama->dokumen_ip_smu) {
                            self::removeFile($subMenuUtama->dokumen_ip_smu);
                        }

                        $subMenuUtama->delete();
                    } elseif ($request->has($updateKey)) {
                        $subMenuUtama->nama_ip_smu = $request->{$updateKey};
                        $subMenuUtama->save();
                    }
                }

                // Handle new sub menu utama
                if ($request->has('new_sub_menu_utama')) {
                    $newSubMenus = $request->new_sub_menu_utama;
                    foreach ($newSubMenus as $newId) {
                        $namaKey = "new_sub_menu_utama_nama_{$newId}";
                        $typeKey = "new_sub_menu_utama_type_{$newId}";
                        $dokumenKey = "new_sub_menu_utama_dokumen_{$newId}";

                        if ($request->has($namaKey)) {
                            $subMenuUtamaData = [
                                'fk_ip_menu_utama' => $menuUtama->ip_menu_utama_id,
                                'nama_ip_smu' => $request->{$namaKey},
                                'dokumen_ip_smu' => null
                            ];

                            // Jika type dokumen, upload file
                            if ($request->{$typeKey} === 'dokumen' && $request->hasFile($dokumenKey)) {
                                $dokumenFile = self::uploadFile(
                                    $request->file($dokumenKey),
                                    'dokumen_ip_sub_menu_utama'
                                );
                                if ($dokumenFile) {
                                    $dokumenFiles[] = $dokumenFile;
                                    $subMenuUtamaData['dokumen_ip_smu'] = $dokumenFile;
                                }
                            }

                            IpSubMenuUtamaModel::create($subMenuUtamaData);
                        }
                    }
                }

                // Cek apakah semua sub menu utama dihapus
                $remainingSubMenuUtama = $menuUtama->IpSubMenuUtama()->where('isDeleted', 0)->count();
                if ($remainingSubMenuUtama == 0) {
                    // Jika tidak ada sub menu utama lagi, wajib ada dokumen
                    if ($request->hasFile('dokumen_ip_mu')) {
                        $dokumenFile = self::uploadFile(
                            $request->file('dokumen_ip_mu'),
                            'dokumen_ip_menu_utama'
                        );
                        if ($dokumenFile) {
                            $dokumenFiles[] = $dokumenFile;
                            $menuUtama->dokumen_ip_mu = $dokumenFile;
                        }
                    } else {
                        throw new \Exception('Dokumen Menu Utama wajib diupload karena tidak memiliki Sub Menu Utama');
                    }
                }
            } else {
                // Logic 2: Menu Utama tanpa children (tidak memiliki sub menu utama)

                // Handle dokumen update
                if ($request->hasFile('dokumen_ip_mu')) {
                    $dokumenFile = self::uploadFile(
                        $request->file('dokumen_ip_mu'),
                        'dokumen_ip_menu_utama'
                    );
                    if ($dokumenFile) {
                        $dokumenFiles[] = $dokumenFile;
                        if ($oldDokumen) {
                            self::removeFile($oldDokumen);
                        }
                        $menuUtama->dokumen_ip_mu = $dokumenFile;
                    }
                }

                // Jika menambah sub menu utama, hapus dokumen
                if ($request->has('menambah_sub_menu_utama') && $request->menambah_sub_menu_utama === 'ya') {
                    if ($oldDokumen) {
                        self::removeFile($oldDokumen);
                    }
                    $menuUtama->dokumen_ip_mu = null;

                    // Create new sub menu utama
                    $jumlahSubMenuUtamaBaru = $request->jumlah_sub_menu_utama_baru;
                    for ($i = 1; $i <= $jumlahSubMenuUtamaBaru; $i++) {
                        $namaKey = "new_sub_menu_utama_nama_{$i}";
                        $dokumenKey = "new_sub_menu_utama_dokumen_{$i}";

                        if ($request->has($namaKey)) {
                            $subMenuUtamaData = [
                                'fk_ip_menu_utama' => $menuUtama->ip_menu_utama_id,
                                'nama_ip_smu' => $request->{$namaKey},
                                'dokumen_ip_smu' => null
                            ];

                            if ($request->hasFile($dokumenKey)) {
                                $dokumenFile = self::uploadFile(
                                    $request->file($dokumenKey),
                                    'dokumen_ip_sub_menu_utama'
                                );
                                if ($dokumenFile) {
                                    $dokumenFiles[] = $dokumenFile;
                                    $subMenuUtamaData['dokumen_ip_smu'] = $dokumenFile;
                                }
                            }

                            IpSubMenuUtamaModel::create($subMenuUtamaData);
                        }
                    }
                }
            }

            $menuUtama->save();

            TransactionModel::createData(
                'UPDATED',
                $menuUtama->ip_menu_utama_id,
                'Menu Utama - ' . $menuUtama->nama_ip_mu
            );

            DB::commit();
            return self::responFormatSukses($menuUtama, 'Menu Utama berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            foreach ($dokumenFiles as $file) {
                self::removeFile($file);
            }
            return self::responFormatError($e, $e->getMessage());
        }
    }
}
