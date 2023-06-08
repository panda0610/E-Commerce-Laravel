<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'product_categorie',
        'price',
        'quantity',
        'SKU',
        'product_tax',
        'product_display',
        'rating_display',
        'is_cover',
        'description',
        'detail',
        'specification',
        'created_by',
    ];

    public function categories()
    {
        return $this->hasOne('App\Models\ProductCategorie', 'id', 'product_categorie');
    }

    public function product_taxs()
    {
        return $this->hasOne('App\Models\ProductTax', 'id', 'product_tax');
    }

    public function product_category()
    {
        $result = ProductCategorie::whereIn('id', explode(',', $this->product_categorie))->get()->pluck('name')->toArray();

        return implode(', ', $result);
    }

    public function product_rating()
    {
        $ratting    = Ratting::where('product_id', $this->id)->where('rating_view', 'on')->sum('ratting');
        $user_count = Ratting::where('product_id', $this->id)->where('rating_view', 'on')->count();
        if($user_count > 0)
        {
            $avg_rating = number_format($ratting / $user_count, 1);
        }
        else
        {
            $avg_rating = number_format($ratting / 1, 1);

        }

        return $avg_rating;
    }

    public static function getCategoryById($productId)
    {
        $product = Product::find($productId);

        $result = ProductCategorie::whereIn('id', explode(',', $product->product_categorie))->get()->pluck('name')->toArray();

        return implode(', ', $result);
    }

    public static function getRatingById($productId)
    {
        $ratting    = Ratting::where('product_id', $productId)->where('rating_view', 'on')->sum('ratting');
        $user_count = Ratting::where('product_id', $productId)->where('rating_view', 'on')->count();
        if($user_count > 0)
        {
            $avg_rating = number_format($ratting / $user_count, 1);
        }
        else
        {
            $avg_rating = number_format($ratting / 1, 1);

        }

        return $avg_rating;
    }

    public static function possibleVariants($groups, $prefix = '')
    {
        $result = [];
        $group  = array_shift($groups);
        foreach($group as $selected)
        {
            if($groups)
            {
                $result = array_merge($result, self::possibleVariants($groups, $prefix . $selected . ' : '));
            }
            else
            {
                $result[] = $prefix . $selected;
            }
        }

        return $result;
    }

    public function product_img()
    {
        return $this->hasOne('App\Models\product_images', 'product_id', 'id');
    }
}
