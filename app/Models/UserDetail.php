<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDetail extends Model
{
    protected $fillable = [
        'store_id',
        'name',
        'email',
        'last_name',
        'phone',
        'billing_address',
        'billing_country',
        'billing_city',
        'billing_postalcode',
        'shipping_address',
        'shipping_country',
        'shipping_city',
        'shipping_postalcode',
];
}
