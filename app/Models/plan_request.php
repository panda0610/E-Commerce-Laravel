<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class plan_request extends Model
{
   
    use HasFactory;
    protected $fillable = [
        'user_id',
        'plan_id',
        'duration',
    ];

    public function plan()
    {
        return $this->hasOne('App\Models\Plan', 'id', 'plan_id');
    }

    public function user()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }
}