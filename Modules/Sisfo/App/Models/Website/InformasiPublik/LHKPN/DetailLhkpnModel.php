<?php

namespace Modules\Sisfo\App\Models\Website\InformasiPublik\LHKPN;

use Modules\Sisfo\App\Models\TraitsModel;
use Illuminate\Database\Eloquent\Model;

class DetailLhkpnModel extends Model
{
    use TraitsModel;

    protected $table = 't_detail_lhkpn';
    protected $primaryKey = 'detail_lhkpn_id';
    protected $fillable = [
        'fk_m_lhkpn',
        'dl_nama_karyawan',
        'dl_file_lhkpn',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable = array_merge($this->fillable, $this->getCommonFields());
    }

    /**
     * Relasi ke model LHKPN
     */
    public function lhkpn()
    {
        return $this->belongsTo(LHKPNModel::class, 'fk_m_lhkpn', 'lhkpn_id');
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