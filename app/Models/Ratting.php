<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ratting extends Model
{
    protected $fillable = [
        'slug',
        'product_id',
        'title',
        'ratting',
        'description',
    ];

    public function avg_rating()
    {
        return $this->ratting;
    }

    public function product()
    {
        return $this->hasOne('App\Models\Product', 'id', 'product_id');
    }

}
