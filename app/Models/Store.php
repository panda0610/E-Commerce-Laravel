<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    protected $fillable = [
        'name',
        'email',
        'store_theme',
        'theme_dir',
        'domains',
        'enable_storelink',
        'enable_subdomain',
        'subdomain',
        'enable_domain',
        'content',
        'item_variable',
        'about',
        'tagline',
        'slug',
        'lang',
        'storejs',
        'currency',
        'currency_code',
        'currency_symbol_position',
        'currency_symbol_space',
        'whatsapp',
        'facebook',
        'instagram',
        'twitter',
        'youtube',
        'google_analytic',
        'footer_note',
        'enable_header_img',
        'header_img',
        'header_title',
        'header_desc',
        'button_text',
        'enable_subscriber',
        'enable_rating',
        'blog_enable',
        'enable_shipping',
        'sub_img',
        'subscriber_title',
        'sub_title',
        'address',
        'city',
        'state',
        'zipcode',
        'country',
        'logo',
        'invoice_logo',
        'is_stripe_enabled',
        'stripe_key',
        'stripe_secret',
        'is_paypal_enabled',
        'paypal_mode',
        'paypal_client_id',
        'paypal_secret_key',
        'mail_driver',
        'mail_host',
        'mail_port',
        'mail_username',
        'mail_password',
        'mail_encryption',
        'mail_from_address',
        'mail_from_name',
        'is_active',
        'created_by',
        'enable_whatsapp',
        'whatsapp_number',
        'enable_telegram',
        'telegrambot',
        'telegramchatid',
        'enable_cod',
        'enable_bank',
        'bank_number',
    ];

    public static function create($data)
    {
        $obj          = new Utility();
        $table        = with(new Store)->getTable();
        $data['slug'] = $obj->createSlug($table, $data['name']);

        $store        = static::query()->create($data);

        return $store;
    }

    public function currentLanguage()
    {
        return $this->lang;
    }

    public function store_user()
    {
        return $this->hasOne('App\Models\UserStore', 'store_id', 'id');
    }

}
