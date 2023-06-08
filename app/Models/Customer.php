<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Customer extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'password',
        'store_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /*public function course_wl()
    {
        return $this->belongsToMany(
            'App\Models\Course', 'wishlists', 'student_id', 'course_id'
        );
    }

    public function course_purchased()
    {
        return $this->belongsToMany(
            'App\Models\Course', 'purchased_courses', 'student_id', 'course_id'
        );
    }
*/
    public function purchasedProducts()
    {
        return $this->hasMany('App\Models\PurchasedProducts', 'customer_id', 'id')->get()->pluck('product_id')->toArray();
    }

}
