<?php

namespace Modules\Sisfo\App\Models\Website\InformasiPublik\TabelDinamis;

use Modules\Sisfo\App\Models\TraitsModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class IpSubMenuModel extends Model
{
    use TraitsModel;

    protected $table = 't_ip_sub_menu';
    protected $primaryKey = 'ip_sub_menu_id';
    protected $fillable = [
        'fk_t_ip_sub_menu_utama',
        'nama_ip_sm',
        'dokumen_ip_sm'
    ];

    public function IpSubMenuUtama()
    {
        return $this->belongsTo(IpSubMenuUtamaModel::class, 'fk_t_ip_sub_menu_utama', 'ip_sub_menu_utama_id');
    }

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable = array_merge($this->fillable, $this->getCommonFields());
    }

    public static function createDataForSubMenuUtama($subMenuUtamaId, $request, $menuIndex, $subMenuIndex, $jumlahSubMenu, &$dokumenFiles)
    {
        $createdSubMenus = [];
        
        for ($k = 1; $k <= $jumlahSubMenu; $k++) {
            $namaSubMenu = $request->{"nama_sub_menu_{$menuIndex}_{$subMenuIndex}_{$k}"};
            
            $dokumenFile = self::uploadFile(
                $request->file("dokumen_sub_menu_{$menuIndex}_{$subMenuIndex}_{$k}"),
                'dokumen_ip_sub_menu'
            );

            $subMenuData = [
                'fk_t_ip_sub_menu_utama' => $subMenuUtamaId,
                'nama_ip_sm' => $namaSubMenu,
                'dokumen_ip_sm' => $dokumenFile
            ];

            if ($dokumenFile) {
                $dokumenFiles[] = $dokumenFile;
            }

            $subMenu = self::create($subMenuData);
            $createdSubMenus[] = $subMenu;
        }
        
        return $createdSubMenus;
    }

    public static function updateDataForSubMenuUtama($subMenuUtamaId, $request, &$dokumenFiles)
    {
        $existingSubMenus = self::where('fk_t_ip_sub_menu_utama', $subMenuUtamaId)
            ->where('isDeleted', 0)
            ->get();

        foreach ($existingSubMenus as $subMenu) {
            $updateKey = "update_sub_menu_{$subMenu->ip_sub_menu_id}";
            
            if ($request->has($updateKey)) {
                $subMenu->nama_ip_sm = $request->{$updateKey};
                
                // Handle dokumen update
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
            
            $deleteKey = "delete_sub_menu_{$subMenu->ip_sub_menu_id}";
            if ($request->has($deleteKey)) {
                if ($subMenu->dokumen_ip_sm) {
                    self::removeFile($subMenu->dokumen_ip_sm);
                }
                
                $subMenu->delete();
            }
        }

        return true;
    }

    public static function deleteDataWithValidation($id)
    {
        $subMenu = self::findOrFail($id);

        // Hapus dokumen jika ada
        if ($subMenu->dokumen_ip_sm) {
            self::removeFile($subMenu->dokumen_ip_sm);
        }

        $subMenu->delete();

        return true;
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
                            $adaSubMenu = $request->{"ada_sub_menu_{$i}_{$j}"};
                            
                            if ($adaSubMenu === 'ya') {
                                $jumlahSubMenu = $request->{"jumlah_sub_menu_{$i}_{$j}"};
                                
                                if (is_numeric($jumlahSubMenu)) {
                                    for ($k = 1; $k <= $jumlahSubMenu; $k++) {
                                        $rules["nama_sub_menu_{$i}_{$j}_{$k}"] = 'required|max:255';
                                        $rules["dokumen_sub_menu_{$i}_{$j}_{$k}"] = 'required|file|mimes:pdf|max:5120';

                                        $messages["nama_sub_menu_{$i}_{$j}_{$k}.required"] = "Nama Sub Menu ke-$i-$j-$k wajib diisi";
                                        $messages["nama_sub_menu_{$i}_{$j}_{$k}.max"] = "Nama Sub Menu ke-$i-$j-$k maksimal 255 karakter";
                                        $messages["dokumen_sub_menu_{$i}_{$j}_{$k}.required"] = "Dokumen Sub Menu ke-$i-$j-$k wajib diupload";
                                        $messages["dokumen_sub_menu_{$i}_{$j}_{$k}.file"] = "Dokumen Sub Menu ke-$i-$j-$k harus berupa file";
                                        $messages["dokumen_sub_menu_{$i}_{$j}_{$k}.mimes"] = "Dokumen Sub Menu ke-$i-$j-$k harus berformat PDF";
                                        $messages["dokumen_sub_menu_{$i}_{$j}_{$k}.max"] = "Ukuran Dokumen Sub Menu ke-$i-$j-$k maksimal 5 MB";
                                    }
                                }
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

        return true;
    }
}