<?php

namespace Modules\Sisfo\App\Models\Website\Publikasi\Berita;

use Modules\Sisfo\App\Models\TraitsModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BeritaModel extends Model
{
    use TraitsModel;

    protected $table = 't_berita';
    protected $primaryKey = 'berita_id';
    protected $fillable = [
        'fk_m_berita_dinamis',
        'berita_judul',
        'berita_slug',
        'berita_thumbnail',
        'berita_thumbnail_deskripsi',
        'berita_deskripsi',
        'status_berita'
    ];

    public function BeritaDinamis()
    {
        return $this->belongsTo(BeritaDinamisModel::class, 'fk_m_berita_dinamis', 'berita_dinamis_id');
    }

    
    public static function getDataBeritaLandingPage()
    {
        $kategori=1;
        $arr_data = DB::table('t_berita', 'tb')
            ->select([
                'tb.berita_id', 
                'tb.berita_judul', 
                'tb.berita_slug',
                'm_berita_dinamis.bd_nama_submenu',
                'tb.created_at',
                'tb.berita_deskripsi'
            ])
            ->join('m_berita_dinamis', 'tb.fk_m_berita_dinamis', '=', 'm_berita_dinamis.berita_dinamis_id')
            ->where('tb.isDeleted', 0)
            ->where('tb.status_berita', 'aktif')
            ->where('m_berita_dinamis.berita_dinamis_id', $kategori)
            ->orderBy('tb.created_at', 'DESC')
            ->limit(3)
            ->get()
            ->map(function ($berita) {
                $deskripsi = strip_tags($berita->berita_deskripsi);
                $paragraf = preg_split('/\n\s*\n/', $deskripsi)[0] ?? '';
                
                return [
                    'kategori' => $berita->bd_nama_submenu,
                    'judul' => $berita->berita_judul,
                    'slug'=> $berita->berita_slug,
                    'deskripsi' => strlen($paragraf) > 200 
                        ? substr($paragraf, 0, 200) . '...' 
                        : $paragraf,
                    'url_selengkapnya' => url('#')
                ];
            })
            ->toArray();
        
        return $arr_data;
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

    public static function createData()
    {
      //
    }

    public static function updateData()
    {
        //
    }

    public static function deleteData()
    {
        //
    }

    public static function validasiData()
    {
        //
    }
}
