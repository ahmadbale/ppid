<?php

namespace Modules\Sisfo\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

trait TraitsModel
{
    use HasFactory, SoftDeletes, BaseModelFunction;
}
