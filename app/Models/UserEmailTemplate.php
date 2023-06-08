<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserEmailTemplate extends Model
{
    protected $fillable = [
        'template_id',
        'user_id',
        'is_active',
    ];
}
