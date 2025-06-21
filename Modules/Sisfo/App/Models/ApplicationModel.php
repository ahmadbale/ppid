<?php

namespace Modules\Sisfo\App\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Sisfo\App\Models\Website\WebMenuUrlModel;

class ApplicationModel extends Model
{
    use TraitsModel;

    protected $table = 'm_application';
    protected $primaryKey = 'application_id';

    protected $fillable = [
        'app_key',
        'app_nama'
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->fillable = array_merge($this->fillable, $this->getCommonFields());
    }
    // tambahan
      // Relasi ke WebMenuUrl
      public function webMenuUrls()
      {
          return $this->hasMany(WebMenuUrlModel::class, 'fk_m_application', 'application_id');
      }
  
      // Method untuk validasi app_key
      public static function validateAppKey($appKey)
      {
          return self::where('app_key', $appKey)
              ->where('isDeleted', 0)
              ->exists();
      }
  
      // Scope untuk filter berdasarkan app_key
      public function scopeByAppKey($query, $appKey)
      {
          return $query->where('app_key', $appKey)->where('isDeleted', 0);
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