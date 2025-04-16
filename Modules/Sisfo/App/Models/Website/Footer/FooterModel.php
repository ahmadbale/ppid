<?php

namespace Modules\Sisfo\App\Models\Website\Footer;

use Modules\Sisfo\App\Models\Log\TransactionModel;
use Modules\Sisfo\App\Models\TraitsModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class FooterModel extends Model
{
    use TraitsModel;

    protected $table = 't_footer';
    protected $primaryKey = 'footer_id';
    protected $fillable = [
        'fk_m_kategori_footer',
        'f_judul_footer',
        'f_icon_footer',
        'f_url_footer',
    ];
     // Relasi dengan kategori footer
     public function kategoriFooter()
     {
         return $this->belongsTo(KategoriFooterModel::class, 'fk_m_kategori_footer', 'kategori_footer_id');
     }

    const ICON_PATH = 'footer_icons';

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable = array_merge($this->fillable, $this->getCommonFields());
    }

    public static function selectData($perPage = null, $search = '')
    {
        $query = self::with('kategoriFooter')
            ->where('isDeleted', 0);

        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('f_judul_footer', 'like', "%{$search}%")
                  ->orWhereHas('kategoriFooter', function ($subQuery) use ($search) {
                      $subQuery->where('kt_footer_nama', 'like', "%{$search}%");
                  });
            });
        }

        return self::paginateResults($query, $perPage);
    }

    public static function createData($request)
    {
        try {
            DB::beginTransaction();

            $data = $request->only([
                'fk_m_kategori_footer',
                'f_judul_footer', 
                'f_url_footer'
            ]);
            
            // Proses upload ikon
            if ($request->hasFile('f_icon_footer')) {
                $iconPath = $request->file('f_icon_footer')->store(self::ICON_PATH, 'public');
                $data['f_icon_footer'] = basename($iconPath);
            }

            $footer = self::create($data);

            TransactionModel::createData(
                'CREATED',
                $footer->footer_id,
                $footer->f_judul_footer
            );

            DB::commit();

            return self::responFormatSukses($footer, 'Footer berhasil dibuat');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal membuat footer');
        }
    }

    public static function updateData($request, $id)
    {
        try {
            DB::beginTransaction();

            $footer = self::findOrFail($id);

            $data = $request->only([
                'fk_m_kategori_footer',
                'f_judul_footer', 
                'f_url_footer'
            ]);
            
            // Proses upload ikon
            if ($request->hasFile('f_icon_footer')) {
                // Hapus ikon lama jika ada
                if ($footer->f_icon_footer) {
                    self::deleteIconFile($footer->f_icon_footer);
                }

                $iconPath = $request->file('f_icon_footer')->store(self::ICON_PATH, 'public');
                $data['f_icon_footer'] = basename($iconPath);
            }

            $footer->update($data);

            TransactionModel::createData(
                'UPDATED',
                $footer->footer_id,
                $footer->f_judul_footer
            );

            DB::commit();

            return self::responFormatSukses($footer, 'Footer berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal memperbarui footer');
        }
    }

    public static function deleteData($id)
    {
        try {
            DB::beginTransaction();

            $footer = self::findOrFail($id);

            // Hapus file ikon jika ada
            if ($footer->f_icon_footer) {
                self::deleteIconFile($footer->f_icon_footer);
            }

            $footer->delete();

            TransactionModel::createData(
                'DELETED',
                $footer->footer_id,
                $footer->f_judul_footer
            );

            DB::commit();

            return self::responFormatSukses($footer, 'Footer berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return self::responFormatError($e, 'Gagal menghapus footer');
        }
    }

    public static function detailData($id)
    {
        return self::with('kategoriFooter')->findOrFail($id);
    }

    public static function validasiData($request, $id = null)
    {
        $rules = [
            'fk_m_kategori_footer' => 'required|exists:m_kategori_footer,kategori_footer_id',
            'f_judul_footer' => 'required|max:100',
            'f_url_footer' => 'nullable|url|max:100',
            'f_icon_footer' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,gif,svg',
                'max:2048'
            ],
        ];

        $messages = [
            'fk_m_kategori_footer.required' => 'Kategori footer wajib dipilih',
            'fk_m_kategori_footer.exists' => 'Kategori footer tidak valid',
            'f_judul_footer.required' => 'Judul footer wajib diisi',
            'f_judul_footer.max' => 'Judul footer maksimal 100 karakter',
            'f_url_footer.url' => 'URL footer harus berupa URL yang valid',
            'f_url_footer.max' => 'URL footer maksimal 100 karakter',
            'f_icon_footer.image' => 'Ikon harus berupa gambar',
            'f_icon_footer.mimes' => 'Ikon hanya boleh berupa file: jpeg, png, jpg, gif, atau svg',
            'f_icon_footer.max' => 'Ukuran ikon maksimal 2MB',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return true;
    }


    // Helper method untuk menghapus file ikon
    private static function deleteIconFile($filename)
    {
        try {
            $path = self::ICON_PATH . '/' . $filename;
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        } catch (\Exception $e) {
            // Log error jika diperlukan
        }
    }
}