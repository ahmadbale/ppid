<?php

namespace Modules\Sisfo\App\Models\Website\Publikasi\Berita;

use Modules\Sisfo\App\Models\TraitsModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BeritaDinamisModel extends Model
{
    use TraitsModel;

    protected $table = 'm_berita_dinamis';
    protected $primaryKey = 'berita_dinamis_id';
    protected $fillable = [
        'bd_nama_submenu',
    ];

    public static function getDataBeritaLandingPage()
    {
     $kategori = 1;
     $arr_data = DB::table('t_berita', 'tb')
         ->select([
             'tb.berita_id',
             'tb.berita_judul',
             'tb.berita_slug',
             'm_berita_dinamis.bd_nama_submenu',
             'tb.berita_thumbnail_deskripsi'
         ])
         ->join('m_berita_dinamis', 'tb.fk_m_berita_dinamis', '=', 'm_berita_dinamis.berita_dinamis_id')
         ->where('tb.isDeleted', 0)
         ->where('tb.status_berita', 'aktif')
         ->where('m_berita_dinamis.berita_dinamis_id', $kategori)
         ->orderBy('tb.berita_id', 'DESC')
         ->limit(3)
         ->get()
         ->map(function ($berita) {
             $deskripsiThumbnail = trim($berita->berita_thumbnail_deskripsi);
 
             return [
                'id'=>$berita->berita_id,
                 'kategori' => $berita->bd_nama_submenu,
                 'judul' => $berita->berita_judul,
                 'slug' => $berita->berita_slug,
                 'deskripsiThumbnail' => strlen($deskripsiThumbnail) > 200
                     ? substr($deskripsiThumbnail, 0, 200) . '...'
                     : $deskripsiThumbnail,
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