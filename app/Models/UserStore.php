<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserStore extends Model
{
    protected $fillable = [
        'user_id',
        'store_id',
        'permission',
        'is_active',
    ];
    public function user()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }
}
