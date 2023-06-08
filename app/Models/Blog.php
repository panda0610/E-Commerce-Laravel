<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    protected $fillable = [
        'title',
        'blog_cover_image',
        'detail',
        'store_id',
        'created_by',
    ];
}
