<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageOption extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'contents',
        'enable_page_header',
        'enable_page_footer',
        'store_id',
        'created_by',
    ];

    public static function create($data)
    {
        $obj          = new Utility();
        $table        = with(new PageOption)->getTable();
        $data['slug'] = $obj->createSlug($table, $data['name']);
        $store        = static::query()->create($data);

        return $store;
    }

}
