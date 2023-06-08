<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product_images extends Model
{
    protected $fillable = [
        'product_id',
        'product_images',
    ];


    protected $appends = ['product_image'];

    public function getProductImageAttribute()
    {
        return !empty($this->product_images)?$this->product_images:'default.jpg';
    }
}
