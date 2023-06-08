<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreThemeSettings extends Model
{
    protected $table    = 'store_theme_settings';

    protected $fillable = [
        'name',
        'value',
        'type',
        'store_id',
        'theme_name',
        'created_by',
    ];
}
