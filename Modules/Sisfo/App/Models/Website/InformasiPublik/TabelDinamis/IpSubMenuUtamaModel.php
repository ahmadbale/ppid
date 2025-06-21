<?php

namespace Modules\Sisfo\App\Models\Website\InformasiPublik\TabelDinamis;

use Modules\Sisfo\App\Models\TraitsModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Modules\Sisfo\App\Models\Log\TransactionModel;

class IpSubMenuUtamaModel extends Model
{
    use TraitsModel;

    protected $table = 't_ip_sub_menu_utama';
    protected $primaryKey = 'ip_sub_menu_utama_id';
    protected $fillable = [
        'fk_ip_menu_utama',
        'nama_ip_smu',
        'dokumen_ip_smu'
    ];

    public function IpMenuUtama()
    {
        return $this->belongsTo(IpMenuUtamaModel::class, 'fk_ip_menu_utama', 'ip_menu_utama_id');
    }

    public function IpSubMenu()
    {
        return $this->hasMany(IpSubMenuModel::class, 'fk_t_ip_sub_menu_utama', 'ip_sub_menu_utama_id')
            ->where('isDeleted', 0);
    }

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable = array_merge($this->fillable, $this->getCommonFields());
    }

    public static function createDataForMenuUtama($menuUtamaId, $request, $menuIndex, $jumlahSubMenuUtama, &$dokumenFiles)
    {
        $createdSubMenus = [];

        for ($j = 1; $j <= $jumlahSubMenuUtama; $j++) {
            $namaSubMenuUtama = $request->{"nama_sub_menu_utama_{$menuIndex}_{$j}"};
            $adaSubMenu = $request->{"ada_sub_menu_{$menuIndex}_{$j}"};

            $subMenuUtamaData = [
                'fk_ip_menu_utama' => $menuUtamaId,
                'nama_ip_smu' => $namaSubMenuUtama,
                'dokumen_ip_smu' => null
            ];

            if ($adaSubMenu === 'tidak') {
                $dokumenFile = self::uploadFile(
                    $request->file("dokumen_sub_menu_utama_{$menuIndex}_{$j}"),
                    'dokumen_ip_sub_menu_utama'
                );
                if ($dokumenFile) {
                    $dokumenFiles[] = $dokumenFile;
                    $subMenuUtamaData['dokumen_ip_smu'] = $dokumenFile;
                }
            }

            $subMenuUtama = self::create($subMenuUtamaData);
            $createdSubMenus[] = $subMenuUtama;

            if ($adaSubMenu === 'ya') {
                $jumlahSubMenu = $request->{"jumlah_sub_menu_{$menuIndex}_{$j}"};
                IpSubMenuModel::createDataForSubMenuUtama(
                    $subMenuUtama->ip_sub_menu_utama_id,
                    $request,
                    $menuIndex,
                    $j,
                    $jumlahSubMenu,
                    $dokumenFiles
                );
            }
        }

        return $createdSubMenus;
    }

    public static function updateDataForMenuUtama($menuUtamaId, $request, &$dokumenFiles)
    {
        // Implementasi update logic untuk sub menu utama
        // Mirip dengan logic timeline update
        $existingSubMenus = self::where('fk_ip_menu_utama', $menuUtamaId)
            ->where('isDeleted', 0)
            ->get();

        // Handle update/delete existing sub menus
        foreach ($existingSubMenus as $subMenu) {
            $updateKey = "update_sub_menu_utama_{$subMenu->ip_sub_menu_utama_id}";

            if ($request->has($updateKey)) {
                $subMenu->nama_ip_smu = $request->{$updateKey};

                // Handle dokumen update
                $dokumenKey = "dokumen_sub_menu_utama_{$subMenu->ip_sub_menu_utama_id}";
                if ($request->hasFile($dokumenKey)) {
                    $dokumenFile = self::uploadFile(
                        $request->file($dokumenKey),
                        'dokumen_ip_sub_menu_utama'
                    );
                    if ($dokumenFile) {
                        $dokumenFiles[] = $dokumenFile;
                        if ($subMenu->dokumen_ip_smu) {
                            self::removeFile($subMenu->dokumen_ip_smu);
                        }
                        $subMenu->dokumen_ip_smu = $dokumenFile;
                    }
                }

                $subMenu->save();
            }

            $deleteKey = "delete_sub_menu_utama_{$subMenu->ip_sub_menu_utama_id}";
            if ($request->has($deleteKey)) {
                // Validasi apakah masih ada children
                if ($subMenu->IpSubMenu->count() > 0) {
                    throw new \Exception('Sub Menu Utama ini tidak bisa dihapus dikarenakan masih terdapat children');
                }

                if ($subMenu->dokumen_ip_smu) {
                    self::removeFile($subMenu->dokumen_ip_smu);
                }

                $subMenu->delete();
            }
        }

        return true;
    }

    public static function deleteDataWithValidation($id)
    {
        try {
            DB::beginTransaction();

            $subMenuUtama = self::with('IpSubMenu')->findOrFail($id);

            // Validasi apakah masih ada children
            if ($subMenuUtama->IpSubMenu->count() > 0) {
                throw new \Exception('Sub Menu Utama ini tidak bisa dihapus dikarenakan masih terdapat children');
            }

            // Hapus dokumen jika ada
            if ($subMenuUtama->dokumen_ip_smu) {
                self::removeFile($subMenuUtama->dokumen_ip_smu);
            }

            $subMenuUtama->delete();

            TransactionModel::createData(
                'DELETED',
                $subMenuUtama->ip_sub_menu_utama_id,
                'Sub Menu Utama - ' . $subMenuUtama->nama_ip_smu
            );

            DB::commit();
            return self::responFormatSukses($subMenuUtama, 'Sub Menu Utama berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, $e->getMessage());
        }
    }

    public static function validasiData($request)
    {
        $rules = [];
        $messages = [];

        $jumlahMenuUtama = $request->jumlah_menu_utama;
        if (is_numeric($jumlahMenuUtama)) {
            for ($i = 1; $i <= $jumlahMenuUtama; $i++) {
                $adaSubMenuUtama = $request->{"ada_sub_menu_utama_$i"};

                if ($adaSubMenuUtama === 'ya') {
                    $jumlahSubMenuUtama = $request->{"jumlah_sub_menu_utama_$i"};

                    if (is_numeric($jumlahSubMenuUtama)) {
                        for ($j = 1; $j <= $jumlahSubMenuUtama; $j++) {
                            $rules["nama_sub_menu_utama_{$i}_{$j}"] = 'required|max:255';
                            $rules["ada_sub_menu_{$i}_{$j}"] = 'required|in:ya,tidak';

                            $messages["nama_sub_menu_utama_{$i}_{$j}.required"] = "Nama Sub Menu Utama ke-$i-$j wajib diisi";
                            $messages["nama_sub_menu_utama_{$i}_{$j}.max"] = "Nama Sub Menu Utama ke-$i-$j maksimal 255 karakter";
                            $messages["ada_sub_menu_{$i}_{$j}.required"] = "Pertanyaan Sub Menu ke-$i-$j wajib dijawab";
                            $messages["ada_sub_menu_{$i}_{$j}.in"] = "Jawaban Sub Menu ke-$i-$j harus Ya atau Tidak";

                            $adaSubMenu = $request->{"ada_sub_menu_{$i}_{$j}"};

                            if ($adaSubMenu === 'tidak') {
                                $rules["dokumen_sub_menu_utama_{$i}_{$j}"] = 'required|file|mimes:pdf|max:5120';
                                $messages["dokumen_sub_menu_utama_{$i}_{$j}.required"] = "Dokumen Sub Menu Utama ke-$i-$j wajib diupload";
                                $messages["dokumen_sub_menu_utama_{$i}_{$j}.file"] = "Dokumen Sub Menu Utama ke-$i-$j harus berupa file";
                                $messages["dokumen_sub_menu_utama_{$i}_{$j}.mimes"] = "Dokumen Sub Menu Utama ke-$i-$j harus berformat PDF";
                                $messages["dokumen_sub_menu_utama_{$i}_{$j}.max"] = "Ukuran Dokumen Sub Menu Utama ke-$i-$j maksimal 5 MB";
                            } elseif ($adaSubMenu === 'ya') {
                                $rules["jumlah_sub_menu_{$i}_{$j}"] = 'required|integer|min:1|max:20';
                                $messages["jumlah_sub_menu_{$i}_{$j}.required"] = "Jumlah Sub Menu ke-$i-$j wajib diisi";
                                $messages["jumlah_sub_menu_{$i}_{$j}.integer"] = "Jumlah Sub Menu ke-$i-$j harus berupa angka";
                                $messages["jumlah_sub_menu_{$i}_{$j}.min"] = "Minimal 1 Sub Menu ke-$i-$j";
                                $messages["jumlah_sub_menu_{$i}_{$j}.max"] = "Maksimal 20 Sub Menu ke-$i-$j";
                            }
                        }
                    }
                }
            }
        }

        if (!empty($rules)) {
            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }
        }

        // Validasi Sub Menu
        IpSubMenuModel::validasiData($request);

        return true;
    }

    public static function updateDataWithChildren($request, $id)
    {
        $dokumenFiles = [];

        try {
            DB::beginTransaction();

            $subMenuUtama = self::findOrFail($id);
            $oldDokumen = $subMenuUtama->dokumen_ip_smu;

            // Update nama sub menu utama
            $subMenuUtama->nama_ip_smu = $request->nama_ip_smu;

            // Logic 1: Sub Menu Utama dengan children (memiliki sub menu)
            if ($subMenuUtama->IpSubMenu->count() > 0) {
                // Handle update/delete existing sub menu
                foreach ($subMenuUtama->IpSubMenu as $subMenu) {
                    $updateKey = "update_sub_menu_{$subMenu->ip_sub_menu_id}";
                    $deleteKey = "delete_sub_menu_{$subMenu->ip_sub_menu_id}";

                    if ($request->has($deleteKey)) {
                        if ($subMenu->dokumen_ip_sm) {
                            self::removeFile($subMenu->dokumen_ip_sm);
                        }
                        $subMenu->delete();
                    } elseif ($request->has($updateKey)) {
                        $subMenu->nama_ip_sm = $request->{$updateKey};

                        // Handle dokumen update for sub menu
                        $dokumenKey = "dokumen_sub_menu_{$subMenu->ip_sub_menu_id}";
                        if ($request->hasFile($dokumenKey)) {
                            $dokumenFile = self::uploadFile(
                                $request->file($dokumenKey),
                                'dokumen_ip_sub_menu'
                            );
                            if ($dokumenFile) {
                                $dokumenFiles[] = $dokumenFile;
                                if ($subMenu->dokumen_ip_sm) {
                                    self::removeFile($subMenu->dokumen_ip_sm);
                                }
                                $subMenu->dokumen_ip_sm = $dokumenFile;
                            }
                        }

                        $subMenu->save();
                    }
                }

                // Handle new sub menu
                if ($request->has('new_sub_menu')) {
                    $newSubMenus = $request->new_sub_menu;
                    foreach ($newSubMenus as $newId) {
                        $namaKey = "new_sub_menu_nama_{$newId}";
                        $dokumenKey = "new_sub_menu_dokumen_{$newId}";

                        if ($request->has($namaKey)) {
                            $dokumenFile = null;
                            if ($request->hasFile($dokumenKey)) {
                                $dokumenFile = self::uploadFile(
                                    $request->file($dokumenKey),
                                    'dokumen_ip_sub_menu'
                                );
                                if ($dokumenFile) {
                                    $dokumenFiles[] = $dokumenFile;
                                }
                            }

                            IpSubMenuModel::create([
                                'fk_t_ip_sub_menu_utama' => $subMenuUtama->ip_sub_menu_utama_id,
                                'nama_ip_sm' => $request->{$namaKey},
                                'dokumen_ip_sm' => $dokumenFile
                            ]);
                        }
                    }
                }

                // Cek apakah semua sub menu dihapus
                $remainingSubMenu = $subMenuUtama->IpSubMenu()->where('isDeleted', 0)->count();
                if ($remainingSubMenu == 0) {
                    // Jika tidak ada sub menu lagi, wajib ada dokumen
                    if ($request->hasFile('dokumen_ip_smu')) {
                        $dokumenFile = self::uploadFile(
                            $request->file('dokumen_ip_smu'),
                            'dokumen_ip_sub_menu_utama'
                        );
                        if ($dokumenFile) {
                            $dokumenFiles[] = $dokumenFile;
                            $subMenuUtama->dokumen_ip_smu = $dokumenFile;
                        }
                    } else {
                        throw new \Exception('Dokumen Sub Menu Utama wajib diupload karena tidak memiliki Sub Menu');
                    }
                }
            } else {
                // Logic 2: Sub Menu Utama tanpa children (tidak memiliki sub menu)

                // Handle dokumen update
                if ($request->hasFile('dokumen_ip_smu')) {
                    $dokumenFile = self::uploadFile(
                        $request->file('dokumen_ip_smu'),
                        'dokumen_ip_sub_menu_utama'
                    );
                    if ($dokumenFile) {
                        $dokumenFiles[] = $dokumenFile;
                        if ($oldDokumen) {
                            self::removeFile($oldDokumen);
                        }
                        $subMenuUtama->dokumen_ip_smu = $dokumenFile;
                    }
                }

                // Jika menambah sub menu, hapus dokumen
                if ($request->has('menambah_sub_menu') && $request->menambah_sub_menu === 'ya') {
                    if ($oldDokumen) {
                        self::removeFile($oldDokumen);
                    }
                    $subMenuUtama->dokumen_ip_smu = null;

                    // Create new sub menu
                    $jumlahSubMenuBaru = $request->jumlah_sub_menu_baru;
                    for ($i = 1; $i <= $jumlahSubMenuBaru; $i++) {
                        $namaKey = "new_sub_menu_nama_{$i}";
                        $dokumenKey = "new_sub_menu_dokumen_{$i}";

                        if ($request->has($namaKey)) {
                            $dokumenFile = null;
                            if ($request->hasFile($dokumenKey)) {
                                $dokumenFile = self::uploadFile(
                                    $request->file($dokumenKey),
                                    'dokumen_ip_sub_menu'
                                );
                                if ($dokumenFile) {
                                    $dokumenFiles[] = $dokumenFile;
                                }
                            }

                            IpSubMenuModel::create([
                                'fk_t_ip_sub_menu_utama' => $subMenuUtama->ip_sub_menu_utama_id,
                                'nama_ip_sm' => $request->{$namaKey},
                                'dokumen_ip_sm' => $dokumenFile
                            ]);
                        }
                    }
                }
            }

            $subMenuUtama->save();

            TransactionModel::createData(
                'UPDATED',
                $subMenuUtama->ip_sub_menu_utama_id,
                'Sub Menu Utama - ' . $subMenuUtama->nama_ip_smu
            );

            DB::commit();
            return self::responFormatSukses($subMenuUtama, 'Sub Menu Utama berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            foreach ($dokumenFiles as $file) {
                self::removeFile($file);
            }
            return self::responFormatError($e, $e->getMessage());
        }
    }
}
