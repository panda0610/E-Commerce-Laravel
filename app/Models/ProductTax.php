<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductTax extends Model
{
    protected $fillable = [
        'tax_name',
        'rate',
    ];
}
