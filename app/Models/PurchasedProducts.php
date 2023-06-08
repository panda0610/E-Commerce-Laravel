<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchasedProducts extends Model
{
    protected $table = 'purchased_products';

    protected $fillable = [
        'customer_id',
        'course_id',
        'order_id',
    ];
}
