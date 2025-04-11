<?php

namespace Modules\Sisfo\App\Models\Website\Footer;

use Modules\Sisfo\App\Models\Log\TransactionModel;
use Modules\Sisfo\App\Models\TraitsModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class KategoriFooterModel extends Model
{
    use TraitsModel;

    protected $table = 'm_kategori_footer';
    protected $primaryKey = 'kategori_footer_id';
    protected $fillable = [
        'kt_footer_kode',
        'kt_footer_nama',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable = array_merge($this->fillable, $this->getCommonFields());
    }
    // Metode untuk select 
    public static function getDataFooter()
    {
        // Get all categories
        $categories = self::where('isDeleted', 0)
            ->select('kategori_footer_id', 'kt_footer_kode', 'kt_footer_nama')
            ->orderBy('kategori_footer_id')
            ->get();
    
        // Initialize result array
        $result = [];
    
        // For each category, get its footer items
        foreach ($categories as $category) {
            $footerItems = FooterModel::where('fk_m_kategori_footer', $category->kategori_footer_id)
                ->where('isDeleted', 0)
                ->select('footer_id', 'f_judul_footer', 'f_icon_footer', 'f_url_footer')
                ->orderBy('footer_id')
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->footer_id,
                        'judul' => $item->f_judul_footer,
                        'icon' => $item->f_icon_footer ? asset('storage/footer_icons/' . $item->f_icon_footer) : null,
                        'url' => $item->f_url_footer
                    ];
                })->toArray();
    
            // Add category with its footer items to result
            $result[] = [
                'kategori_id' => $category->kategori_footer_id,
                'kategori_kode' => $category->kt_footer_kode,
                'kategori_nama' => $category->kt_footer_nama,
                'items' => $footerItems
            ];
        }
    
        return $result;
    }
    public static function selectData($perPage = null, $search = '')
    {
        $query = self::query()
            ->where('isDeleted', 0);

        // Tambahkan fungsionalitas pencarian
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('kt_footer_kode', 'like', "%{$search}%")
                  ->orWhere('kt_footer_nama', 'like', "%{$search}%");
            });
        }

        return self::paginateResults($query, $perPage);
    }

    public static function createData($request)
    {
        try {
            DB::beginTransaction();

            $data = $request->only([
                'kt_footer_kode', 
                'kt_footer_nama'
            ]);
            
            $kategoriFooter = self::create($data);

            TransactionModel::createData(
                'CREATED',
                $kategoriFooter->kategori_footer_id,
                $kategoriFooter->kt_footer_nama
            );

            DB::commit();

            return self::responFormatSukses($kategoriFooter, 'Kategori footer berhasil dibuat');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal membuat kategori footer');
        }
    }

    public static function updateData($request, $id)
    {
        try {
            DB::beginTransaction();

            $kategoriFooter = self::findOrFail($id);

            $data = $request->only([
                'kt_footer_kode', 
                'kt_footer_nama'
            ]);
            
            $kategoriFooter->update($data);

            TransactionModel::createData(
                'UPDATED',
                $kategoriFooter->kategori_footer_id,
                $kategoriFooter->kt_footer_nama
            );

            DB::commit();

            return self::responFormatSukses($kategoriFooter, 'Kategori footer berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal memperbarui kategori footer');
        }
    }

    public static function deleteData($id)
    {
        try {
            DB::beginTransaction();

            $kategoriFooter = self::findOrFail($id);

            $kategoriFooter->delete();

            TransactionModel::createData(
                'DELETED',
                $kategoriFooter->kategori_footer_id,
                $kategoriFooter->kt_footer_nama
            );

            DB::commit();

            return self::responFormatSukses($kategoriFooter, 'Kategori footer berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal menghapus kategori footer');
        }
    }

    public static function detailData($id)
    {
        return self::findOrFail($id);
    }

    public static function validasiData($request, $id = null)
    {
        $rules = [
            'kt_footer_kode' => [
                'required',
                'max:20',
                function ($attribute, $value, $fail) use ($id, $request) {
                    // Cari data dengan kode yang sama (hanya yang TIDAK soft deleted)
                    $query = self::where('kt_footer_kode', $value)
                        ->where('isDeleted', 0);
    
                    // Jika sedang update, kecualikan ID saat ini
                    if ($id) {
                        $query->where('kategori_footer_id', '!=', $id);
                    }
    
                    $existingData = $query->first();
    
                    if ($existingData) {
                        $fail('Kode footer sudah digunakan');
                    }
                }
            ],
            'kt_footer_nama' => [
                'required',
                'max:100',
                function ($attribute, $value, $fail) use ($id) {
                    // Cari data dengan nama yang sama (hanya yang TIDAK soft deleted)
                    $query = self::where('kt_footer_nama', $value)
                        ->where('isDeleted', 0);
    
                    // Jika sedang update, kecualikan ID saat ini
                    if ($id) {
                        $query->where('kategori_footer_id', '!=', $id);
                    }
    
                    $existingData = $query->first();
    
                    if ($existingData) {
                        $fail('Nama footer sudah digunakan');
                    }
                }
            ],
        ];
    
        $messages = [
            'kt_footer_kode.required' => 'Kode footer wajib diisi',
            'kt_footer_kode.max' => 'Kode footer maksimal 20 karakter',
            'kt_footer_nama.required' => 'Nama footer wajib diisi',
            'kt_footer_nama.max' => 'Nama footer maksimal 100 karakter',
        ];
    
        $validator = Validator::make($request->all(), $rules, $messages);
    
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    
        return true;
    }
}