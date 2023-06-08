<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogSocial extends Model
{
    protected $fillable = [
        'enable_social_button',
        'enable_email',
        'enable_twitter',
        'enable_facebook',
        'enable_googleplus',
        'enable_linkedIn',
        'enable_pinterest',
        'enable_stumbleupon',
        'enable_whatsapp',
        'store_id',
        'created_by',
    ];
}
