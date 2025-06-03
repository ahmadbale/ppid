<?php

namespace Modules\Sisfo\App\Models\Log;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Route;

class NotifMPUModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'log_notif_mpu';
    protected $primaryKey = 'notif_mpu_id';
    public $timestamps = false;
    protected $fillable = [
        'kategori_notif_mpu',
        'notif_mpu_form_id',
        'pesan_notif_mpu',
        'sudah_dibaca_notif_mpu',
        'isDeleted',
        'created_at',
        'deleted_by',
        'deleted_at'
    ];
}