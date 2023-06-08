<?php

namespace App\Models;

use App\Mail\CommonEmailTemplate;
use App\Mail\OrderMail;
use App\Models\LandingPageSections;
use App\Models\EmailTemplateLang;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Jenssegers\Date\Date;
use Twilio\Rest\Client;
use phpDocumentor\Reflection\DocBlock\Tags\Author;
use Illuminate\Support\Facades\Crypt;

class Utility extends Model
{
    public function createSlug($table, $title, $id = 0)
    {
        // Normalize the title
        $slug = Str::slug($title, '-');
        // Get any that could possibly be related.
        // This cuts the queries down by doing it once.
        $allSlugs = $this->getRelatedSlugs($table, $slug, $id);
        // If we haven't used it before then we are all good.
        if(!$allSlugs->contains('slug', $slug))
        {
            return $slug;
        }
        // Just append numbers like a savage until we find not used.
        for($i = 1; $i <= 100; $i++)
        {
            $newSlug = $slug . '-' . $i;
            if(!$allSlugs->contains('slug', $newSlug))
            {
                return $newSlug;
            }
        }
        throw new \Exception('Can not create a unique slug');
    }

    protected function getRelatedSlugs($table, $slug, $id = 0)
    {
        return DB::table($table)->select()->where('slug', 'like', $slug . '%')->where('id', '<>', $id)->get();
    }

    public static function settings()
    {
        $data = DB::table('settings');

        if(\Auth::check())
        {
            if(\Auth::user()->type == 'super admin')
            {
                $userId = \Auth::user()->creatorId();
                $data   = $data->where('created_by', '=', 1);
            }
            else
            {
                $userId = \Auth::user()->current_store;
                $data   = $data->where('created_by', '=', $userId);
            }
        }
        else
        {
            $data = $data->where('created_by', '=', 1);
        }
        $data = $data->get();

        $settings = [
            "site_currency" => "USD",
            "site_currency_symbol" => "$",
            "currency_symbol_position" => "pre",
            "logo_dark" => "logo-dark.png",
            "logo_light" => "logo-light.png",
            "currency_symbol" => "",
            "currency" => "",
            "site_date_format" => "M j, Y",
            "site_time_format" => "g:i A",
            "company_name" => "",
            "company_address" => "",
            "company_city" => "",
            "company_state" => "",
            "company_zipcode" => "",
            "company_country" => "",
            "company_telephone" => "",
            "company_email" => "",
            "company_email_from_name" => "",
            "invoice_prefix" => "#INV",
            "invoice_color" => "ffffff",
            "quote_template" => "template1",
            "quote_color" => "ffffff",
            "salesorder_template" => "template1",
            "salesorder_color" => "ffffff",
            "proposal_prefix" => "#PROP",
            "proposal_color" => "fffff",
            "bill_prefix" => "#BILL",
            "bill_color" => "fffff",
            "quote_prefix" => "#QUO",
            "salesorder_prefix" => "#SOP",
            "vender_prefix" => "#VEND",
            "footer_title" => "",
            "footer_notes" => "",
            "invoice_template" => "template1",
            "bill_template" => "template1",
            "proposal_template" => "template1",
            "default_language" => "en",
            "enable_stripe" => "",
            "enable_paypal" => "",
            "paypal_mode" => "",
            "paypal_client_id" => "",
            "paypal_secret_key" => "",
            "stripe_key" => "",
            "stripe_secret" => "",
            "decimal_number" => "2",
            "tax_type" => "VAT",
            "shipping_display" => "on",
            "footer_link_1" => "Support",
            "footer_value_1" => "#",
            "footer_link_2" => "Terms",
            "footer_value_2" => "#",
            "footer_link_3" => "Privacy",
            "footer_value_3" => "#",
            "display_landing_page" => "on",
            "title_text" => "",
            "footer_text" => "",
            "company_logo_light" => "logo-light.png",
            "company_logo_dark" =>  "logo-dark.png",
            "company_favicon" => "",
            "gdpr_cookie" => "",
            "cookie_text" => "",
            "signup_button"=>"on",
            "cust_theme_bg"=>"off",
            "cust_darklayout"=>"off",
            "color"=>"theme-3",

        ];


        foreach($data as $row)
        {
            $settings[$row->name] = $row->value;
        }
        $data = [];
        $useradmin = User::where('type','super admin')->first();
        $adminsetting = DB::table('settings')->where('created_by',$useradmin->id)->pluck('value','name')->toArray();

        $settings['cust_theme_bg'] = isset($adminsetting['cust_theme_bg']) ? $adminsetting['cust_theme_bg'] : $settings['cust_theme_bg'];
        $settings['cust_darklayout'] = isset($adminsetting['cust_darklayout']) ? $adminsetting['cust_darklayout'] : $settings['cust_darklayout'];
        $settings['color'] = isset($adminsetting['color']) ? $adminsetting['color'] : $settings['color'];
        return $settings;
    }

    public static function languages()
    {
        $dir     = base_path() . '/resources/lang/';
        $glob    = glob($dir . "*", GLOB_ONLYDIR);
        $arrLang = array_map(
            function ($value) use ($dir){
                return str_replace($dir, '', $value);
            }, $glob
        );
        $arrLang = array_map(
            function ($value) use ($dir){
                return preg_replace('/[0-9]+/', '', $value);
            }, $arrLang
        );
        $arrLang = array_filter($arrLang);

        return $arrLang;
    }

    public static function getValByName($key)
    {
        $setting = Utility::settings();

        if(!isset($setting[$key]) || empty($setting[$key]))
        {
            $setting[$key] = '';
        }

        return $setting[$key];
    }

    public static function getPaymentSetting($store_id = null)
    {
        $data     = DB::table('store_payment_settings');
        $settings = [];
        if(\Auth::check())
        {
            $store_id = \Auth::user()->current_store;
            $data     = $data->where('store_id', '=', $store_id);

        }
        else
        {
            $data = $data->where('store_id', '=', $store_id);
        }
        $data = $data->get();
        foreach($data as $row)
        {
            $settings[$row->name] = $row->value;
        }

        return $settings;
    }

    public static function getStoreThemeSetting($store_id = null, $theme_name = null)
    {
        $data     = DB::table('store_theme_settings');
        $settings = [];

        $store_id = \Auth::user()->current_store;
        if(\Auth::check())
        {
            $data = $data->where('store_id', '=', $store_id)->where('theme_name', $theme_name);
        }
        else
        {
            $data = $data->where('store_id', '=', $store_id)->where('theme_name', $theme_name);
        }
        $data = $data->get();

        if($data->count() > 0)
        {
            foreach($data as $row)
            {
                $settings[$row->name] = $row->value;
            }
        }

        return $settings;
    }


     public static function getDateFormated($date, $time = false)
    {
        if(!empty($date) && $date != '0000-00-00')
        {
            if($time == true)
            {
                return date("d M Y H:i A", strtotime($date));
            }
            else
            {
                return date("d M Y", strtotime($date));
            }
        }
        else
        {
            return '';
        }
    }

    public static function demoStoreThemeSetting($store_id = null, $theme_name = null)
    {
        $data = StoreThemeSettings::where('store_id', $store_id)->where('theme_name', $theme_name)->get();

        $settings = [
            "enable_top_bar" => "on",
            "top_bar_title" => "FREE SHIPPING world wide for all orders over $199",
            "top_bar_number" => "(212) 308-1220",
            "top_bar_whatsapp" => "https://web.whatsapp.com/",
            "top_bar_instagram" => "https://instagram.com/",
            "top_bar_twitter" => "https://twitter.com/",
            "top_bar_messenger" => "https://messenger.com/",

            "enable_header_img" => "on",
            "header_title" => "Home Accessories",
            "header_desc" => "There is only that moment and the incredible certainty that everything under the sun has been written by one hand only.",
            "button_text" => "Start shopping",
            "header_img" => "header_img_1.png",

            "enable_features" => "on",
            "enable_features1" => "on",
            "enable_features2" => "on",
            "enable_features3" => "on",

            "features_icon1" => '<i class="fa fa-tags"></i>',
            "features_title1" => 'Many promotions',
            "features_description1" => 'From pixel-perfect icons and scalable vector graphics, to full user flows',

            "features_icon2" => '<i class="fas fa-store"></i>',
            "features_title2" => 'Many promotions',
            "features_description2" => 'From pixel-perfect icons and scalable vector graphics, to full user flows',

            "features_icon3" => '<i class="fa fa-percentage"></i>',
            "features_title3" => 'Many promotions',
            "features_description3" => 'From pixel-perfect icons and scalable vector graphics, to full user flows',

            "enable_email_subscriber" => "on",
            "subscriber_title" => "Always on time",
            "subscriber_sub_title" => "There is only that moment and the incredible certainty that everything under the sun has been written by one hand only.",
            "subscriber_img" => "email_subscriber_1.png",

            "enable_categories" => "on",
            "categories" => "Categories",
            "categories_title" => "There is only that moment and the incredible certainty that everything under the sun has been written by one hand only.",

            "enable_testimonial" => "on",

            "enable_testimonial1" => "on",
            "testimonial_main_heading_title" => 'There is only that moment and the incredible certainty that everything under the sun has been written by one hand only.',
            "testimonial_main_heading" => 'Testimonial',
            "testimonial_img1" => 'avatar.png',

            "testimonial_name1" => 'Rajodiya Infotech',
            "testimonial_about_us1" => 'CEO Rajodiya Infotech',
            "testimonial_description1" => '‘Nowadays, it isnt great uncommon to see lenders rapidly adopting a new digital lending strategy to make most popular streamline the web process',

            "enable_testimonial2" => "on",
            "testimonial_img2" => 'avatar.png',
            "testimonial_name2" => 'Rajodiya Infotech',
            "testimonial_about_us2" => 'CEO Rajodiya Infotech',
            "testimonial_description2" => '‘Nowadays, it isnt great uncommon to see lenders rapidly adopting a new digital lending strategy to make most popular streamline the web process',

            "enable_testimonial3" => "on",
            "testimonial_img3" => 'avatar.png',
            "testimonial_name3" => 'Rajodiya Infotech',
            "testimonial_about_us3" => 'CEO Rajodiya Infotech',
            "testimonial_description3" => '‘Nowadays, it isnt great uncommon to see lenders rapidly adopting a new digital lending strategy to make most popular streamline the web process',

            "enable_brand_logo" => "on",
            "brand_logo" => implode(
                ',', [
                       'brand_logo.png',
                       'brand_logo.png',
                       'brand_logo.png',
                       'brand_logo.png',
                       'brand_logo.png',
                       'brand_logo.png',
                   ]
            ),

            "quick_link_header_name21" => "About",
            "quick_link_header_name41" => "Company",

            "quick_link_name1" => __('Home Pages'),
            "quick_link_url1" => '#Home Pages',

            "enable_footer_note" => "on",
            "enable_quick_link1" => "on",
            "enable_quick_link2" => "on",
            "enable_quick_link3" => "on",
            "enable_quick_link4" => "on",

            "quick_link_header_name1" => __("Theme Pages"),
            "quick_link_header_name2" => __("About"),
            "quick_link_header_name3" => __("Company"),
            "quick_link_header_name4" => __("Company"),

            "quick_link_name11" => __('Home Pages'),
            "quick_link_name12" => __('Pricing'),
            "quick_link_name13" => __('Contact Us'),
            "quick_link_name14" => __('Team'),

            "quick_link_name21" => __('Blog'),
            "quick_link_name22" => __('Help Center'),
            "quick_link_name23" => __('Sales Tools Catalog'),
            "quick_link_name24" => __('Academy'),

            "quick_link_name31" => __('Terms and Policy'),
            "quick_link_name32" => __('About us'),
            "quick_link_name33" => __('Support'),
            "quick_link_name34" => __('About us'),

            "quick_link_name41" => __('Terms and Policy'),
            "quick_link_name42" => __('About us'),
            "quick_link_name43" => __('Support'),
            "quick_link_name44" => __('About us'),

            "quick_link_url11" => '#Home Pages',
            "quick_link_url12" => '#Home Pages',
            "quick_link_url13" => '#Home Pages',
            "quick_link_url14" => '#Home Pages',

            "quick_link_url21" => '#Blog',
            "quick_link_url22" => '#Blog',
            "quick_link_url23" => '#Blog',
            "quick_link_url24" => '#Blog',

            "quick_link_url31" => '#Terms and Policy',
            "quick_link_url32" => '#Terms and Policy',
            "quick_link_url33" => '#Terms and Policy',
            "quick_link_url34" => '#Terms and Policy',

            "quick_link_url41" => '#About us',
            "quick_link_url42" => '#About us',
            "quick_link_url43" => '#About us',
            "quick_link_url44" => '#About us',


            "footer_logo" => "footer_logo.png",
            "footer_desc" => "Nowadays, it isnt great uncommon to see lenders rapidly adopting a new digital",
            "footer_number" => "(987)654321",

            "enable_footer" => "on",
            "email" => "test@test.com",
            "whatsapp" => "https://api.whatsapp.com/",
            "facebook" => "https://www.facebook.com/",
            "instagram" => "https://www.instagram.com/",
            "twitter" => "https://twitter.com/",
            "youtube" => "https://www.youtube.com/",
            "footer_note" => "© 2021 My Store. All rights reserved",
            "storejs" => "<script>console.log('hello');</script>",

            /*THEME 3*/

        ];

        if($theme_name == 'theme2')
        {
            $settings['header_img']     = 'header_img_2.png';
            $settings['subscriber_img'] = "email_subscriber_2.png";
            $settings['footer_logo2']   = "footer_logo2.png";
            $settings['brand_logo']     = implode(
                ',', [
                       'brand_logo2.png',
                       'brand_logo2.png',
                       'brand_logo2.png',
                       'brand_logo2.png',
                       'brand_logo2.png',
                       'brand_logo2.png',
                   ]
            );
        }

        if($theme_name == 'theme3')
        {
            $settings['header_img']                     = 'header_img_3.png';
            $settings['testimonial_img1']               = 'testimonail-img_3.png';
            $settings['testimonial_img2']               = 'testimonail-img_3.png';
            $settings['testimonial_img3']               = 'testimonail-img_3.png';
            $settings['banner_img']                     = 'header_img_3.png';
            $settings['enable_banner_img']              = 'on';
            $settings['testimonial_main_heading_title'] = 'StoreGo';
            $settings['footer_logo3']                   = "footer_logo3.png";

        }

        if($theme_name == 'theme4')
        {
            $settings['header_img']        = 'header_img_4.png';
            $settings['banner_img']        = 'image-big-4.jpg';
            $settings['enable_banner_img'] = 'on';
            $settings['subscriber_img']    = "email_subscriber_2.png";
            $settings['brand_logo']        = implode(
                ',', [
                       'brand_logo4.png',
                       'brand_logo4.png',
                       'brand_logo4.png',
                       'brand_logo4.png',
                       'brand_logo4.png',
                       'brand_logo4.png',
                   ]
            );
            $settings['footer_logo4']      = "footer_logo4.png";
        }

        if($theme_name == 'theme5')
        {
            $settings['header_img']   = 'header_img_5.png';
            $settings['brand_logo']   = implode(
                ',', [
                       'brand_logo5.png',
                       'brand_logo5.png',
                       'brand_logo5.png',
                       'brand_logo5.png',
                       'brand_logo5.png',
                       'brand_logo5.png',
                   ]
            );
            $settings['footer_logo5'] = "footer_logo5.png";
        }


        if($data->count() > 0)
        {
            foreach($data as $row)
            {
                $settings[$row->name] = $row->value;
            }
        }



         $store = Store::where('id',$store_id)->first();

        foreach($settings as $key => $data)
        {

            $arr = [
                'name' => $key,
                'value' => $data,
                'type' => null,
                'store_id' => $store->id,
                'theme_name' => $store->theme_dir,
                'created_by' => $store->created_by,
            ];

            StoreThemeSettings::updateOrCreate(
                [
                    'name' => $key,
                    'store_id' => $store->id,
                    'theme_name' => $store->theme_dir,
                ], $arr
            );
        }

        return $settings;
    }

    public static function getAdminPaymentSetting()
    {
        $data     = DB::table('admin_payment_settings');
        $settings = [];
        if(\Auth::check())
        {
            $user_id = 1;
            $data    = $data->where('created_by', '=', $user_id);

        }
        $data = $data->get();
        foreach($data as $row)
        {
            $settings[$row->name] = $row->value;
        }

        return $settings;
    }

    public static function setEnvironmentValue(array $values)
    {
        $envFile = app()->environmentFilePath();
        $str     = file_get_contents($envFile);
        if(count($values) > 0)
        {
            foreach($values as $envKey => $envValue)
            {
                $keyPosition       = strpos($str, "{$envKey}=");
                $endOfLinePosition = strpos($str, "\n", $keyPosition);
                $oldLine           = substr($str, $keyPosition, $endOfLinePosition - $keyPosition);
                // If key does not exist, add it
                if(!$keyPosition || !$endOfLinePosition || !$oldLine)
                {
                    $str .= "{$envKey}='{$envValue}'\n";
                }
                else
                {
                    $str = str_replace($oldLine, "{$envKey}='{$envValue}'", $str);
                }
            }
        }
        $str = substr($str, 0, -1);
        $str .= "\n";
        if(!file_put_contents($envFile, $str))
        {
            return false;
        }

        return true;
    }

    public static function templateData()
    {
        $arr              = [];
        $arr['colors']    = [
            '003580',
            '666666',
            '6676ef',
            'f50102',
            'f9b034',
            'fbdd03',
            'c1d82f',
            '37a4e4',
            '8a7966',
            '6a737b',
            '050f2c',
            '0e3666',
            '3baeff',
            '3368e6',
            'b84592',
            'f64f81',
            'f66c5f',
            'fac168',
            '46de98',
            '40c7d0',
            'be0028',
            '2f9f45',
            '371676',
            '52325d',
            '511378',
            '0f3866',
            '48c0b6',
            '297cc0',
            'ffffff',
            '000',
        ];
        $arr['templates'] = [
            "template1" => "New York",
            "template2" => "Toronto",
            "template3" => "Rio",
            "template4" => "London",
            "template5" => "Istanbul",
            "template6" => "Mumbai",
            "template7" => "Hong Kong",
            "template8" => "Tokyo",
            "template9" => "Sydney",
            "template10" => "Paris",
        ];

        return $arr;
    }

    public static function themeOne()
    {
        $arr = [];

        $arr = [
            'theme1' => [
                'green-color.css' => [
                    'img_path' => asset(Storage::url('uploads/store_theme/theme1/Home.png')),
                    'color' => '92bd88',
                ],
                'geen-blue-color.css' => [
                    'img_path' => asset(Storage::url('uploads/store_theme/theme1/Home-1.png')),
                    'color' => '276968',
                ],
                'geen-brown-color.css' => [
                    'img_path' => asset(Storage::url('uploads/store_theme/theme1/Home-2.png')),
                    'color' => 'af8637',
                ],
                'geen-white-color.css' => [
                    'img_path' => asset(Storage::url('uploads/store_theme/theme1/Home-3.png')),
                    'color' => 'e7d7bd',
                ],
                'green-Pink-color.css' => [
                    'img_path' => asset(Storage::url('uploads/store_theme/theme1/Home-4.png')),
                    'color' => 'b7786f',
                ],
            ],

            'theme2' => [
                'blue-yellow-color.css' => [
                    'img_path' => asset(Storage::url('uploads/store_theme/theme2/Home.png')),
                    'color' => 'f5ba20',
                ],
                'blue-pink-color.css' => [
                    'img_path' => asset(Storage::url('uploads/store_theme/theme2/Home-1.png')),
                    'color' => 'fa747d',
                ],
                'blue-cream-color.css' => [
                    'img_path' => asset(Storage::url('uploads/store_theme/theme2/Home-2.png')),
                    'color' => 'c8ae9d',
                ],
                'blue-white-color.css' => [
                    'img_path' => asset(Storage::url('uploads/store_theme/theme2/Home-3.png')),
                    'color' => 'd7e2dc',
                ],
                'blue-sky-color.css' => [
                    'img_path' => asset(Storage::url('uploads/store_theme/theme2/Home-4.png')),
                    'color' => '5ea5ab',
                ],
            ],

            'theme3' => [
                'white-yellow-color.css' => [
                    'img_path' => asset(Storage::url('uploads/store_theme/theme3/Home.png')),
                    'color' => 'f6e32f',
                ],
                'white-geen-color.css' => [
                    'img_path' => asset(Storage::url('uploads/store_theme/theme3/Home-1.png')),
                    'color' => '7db802',
                ],
                'white-blue-color.css' => [
                    'img_path' => asset(Storage::url('uploads/store_theme/theme3/Home-2.png')),
                    'color' => '3e77ea',
                ],
                'white-black-color.css' => [
                    'img_path' => asset(Storage::url('uploads/store_theme/theme3/Home-3.png')),
                    'color' => '2b2d2d',
                ],
                'white-pink-color.css' => [
                    'img_path' => asset(Storage::url('uploads/store_theme/theme3/Home-4.png')),
                    'color' => 'ffccb4',
                ],
            ],

            'theme4' => [
                'light-blue-color.css' => [
                    'img_path' => asset(Storage::url('uploads/store_theme/theme4/Home.png')),
                    'color' => '5e7698',
                ],
                'light-green-color.css' => [
                    'img_path' => asset(Storage::url('uploads/store_theme/theme4/Home-1.png')),
                    'color' => '88d297',
                ],
                'light-cream-color.css' => [
                    'img_path' => asset(Storage::url('uploads/store_theme/theme4/Home-2.png')),
                    'color' => 'c9aea7',
                ],
                'light-black-color.css' => [
                    'img_path' => asset(Storage::url('uploads/store_theme/theme4/Home-3.png')),
                    'color' => '2f343a',
                ],
                'light-orange-color.css' => [
                    'img_path' => asset(Storage::url('uploads/store_theme/theme4/Home-4.png')),
                    'color' => 'f3ba51',
                ],
            ],

            'theme5' => [
                'dark-sky-blue-color.css' => [
                    'img_path' => asset(Storage::url('uploads/store_theme/theme5/Home.png')),
                    'color' => '007aff',
                ],
                'dark-yellow-color.css' => [
                    'img_path' => asset(Storage::url('uploads/store_theme/theme5/Home-1.png')),
                    'color' => 'febd00',
                ],
                'dark-green-color.css' => [
                    'img_path' => asset(Storage::url('uploads/store_theme/theme5/Home-2.png')),
                    'color' => '05d79f',
                ],
                'dark-pink-color.css' => [
                    'img_path' => asset(Storage::url('uploads/store_theme/theme5/Home-3.png')),
                    'color' => 'e91e63',
                ],
                'dark-blue-color.css' => [
                    'img_path' => asset(Storage::url('uploads/store_theme/theme5/Home-4.png')),
                    'color' => '2b2d42',
                ],
            ],
        ];

        return $arr;
    }

    public static function priceFormat($price,$slug = null)
    {

        $settings = Utility::settings();
        if(\Auth::check() && \Auth::User()->type == 'Owner')
        {
            $user     = Auth::user()->current_store;
            $settings = Store::where('id', $user)->first();

            if($settings['currency_symbol_position'] == "pre" && $settings['currency_symbol_space'] == "with")
            {
                return $settings['currency'] . ' ' . number_format($price, isset($settings->decimal_number)?$settings->decimal_number:2);
            }
            elseif($settings['currency_symbol_position'] == "pre" && $settings['currency_symbol_space'] == "without")
            {
                return $settings['currency'] . number_format($price, isset($settings->decimal_number)?$settings->decimal_number:2);
            }
            elseif($settings['currency_symbol_position'] == "post" && $settings['currency_symbol_space'] == "with")
            {
                return number_format($price, isset($settings->decimal_number)?$settings->decimal_number:2) . ' ' . $settings['currency'];
            }
            elseif($settings['currency_symbol_position'] == "post" && $settings['currency_symbol_space'] == "without")
            {
                return number_format($price, isset($settings->decimal_number)?$settings->decimal_number:2) . $settings['currency'];
            }
        }
        else
        {
            if(!isset($slug))
            {
                $slug = session()->get('slug');
            }

            if(!empty($slug))
            {
                $store = Store::where('slug', $slug)->first();

                if($store['currency_symbol_position'] == "pre" && $store['currency_symbol_space'] == "with")
                {
                    return $store['currency'] . ' ' . number_format($price, isset($store->decimal_number)?$store->decimal_number:2);
                }
                elseif($store['currency_symbol_position'] == "pre" && $store['currency_symbol_space'] == "without")
                {
                    return $store['currency'] . number_format($price, isset($store->decimal_number)?$store->decimal_number:2);
                }
                elseif($store['currency_symbol_position'] == "post" && $store['currency_symbol_space'] == "with")
                {
                    return number_format($price, isset($store->decimal_number)?$store->decimal_number:2) . ' ' . $store['currency'];
                }
                elseif($store['currency_symbol_position'] == "post" && $store['currency_symbol_space'] == "without")
                {
                    return number_format($price, isset($store->decimal_number)?$store->decimal_number:2) . $store['currency'];
                }
            }

            //            return (($settings['currency_symbol_position'] == "pre") ? $settings['currency_symbol'] : '') . number_format($price, 2) . (($settings['currency_symbol_position'] == "post") ? $settings['currency_symbol'] : '');
            return (($settings['currency_symbol_position'] == "pre") ? $settings['site_currency_symbol'] : '') . number_format($price, Utility::getValByName('decimal_number')) . (($settings['currency_symbol_position'] == "post") ? $settings['site_currency_symbol'] : '');
        }
    }

    public static function currencySymbol($settings)
    {
        return $settings['site_currency_symbol'];
    }

    public static function timeFormat($settings, $time)
    {
        return date($settings['site_date_format'], strtotime($time));
    }

    public static function dateFormat($date)
    {
        $settings = Utility::settings();

        return date($settings['site_date_format'], strtotime($date));
    }

    public static function proposalNumberFormat($settings, $number)
    {
        return $settings["proposal_prefix"] . sprintf("%05d", $number);
    }

    public static function billNumberFormat($settings, $number)
    {
        return $settings["bill_prefix"] . sprintf("%05d", $number);
    }

    public static function tax($taxes)
    {
        $taxArr = explode(',', $taxes);
        $taxes  = [];
        foreach($taxArr as $tax)
        {
            $taxes[] = ProductTax::find($tax);
        }

        return $taxes;
    }

    public static function taxRate($taxRate, $price, $quantity)
    {

        return ($taxRate / 100) * ($price * $quantity);
    }

    public static function totalTaxRate($taxes)
    {

        $taxArr  = explode(',', $taxes);
        $taxRate = 0;

        foreach($taxArr as $tax)
        {

            $tax     = ProductTax::find($tax);
            $taxRate += !empty($tax->rate) ? $tax->rate : 0;
        }

        return $taxRate;
    }

    public static function userBalance($users, $id, $amount, $type)
    {
        if($users == 'customer')
        {
            $user = Customer::find($id);
        }
        else
        {
            $user = Vender::find($id);
        }

        if(!empty($user))
        {
            if($type == 'credit')
            {
                $oldBalance    = $user->balance;
                $user->balance = $oldBalance + $amount;
                $user->save();
            }
            elseif($type == 'debit')
            {
                $oldBalance    = $user->balance;
                $user->balance = $oldBalance - $amount;
                $user->save();
            }
        }
    }

    public static function bankAccountBalance($id, $amount, $type)
    {
        $bankAccount = BankAccount::find($id);
        if($bankAccount)
        {
            if($type == 'credit')
            {
                $oldBalance                   = $bankAccount->opening_balance;
                $bankAccount->opening_balance = $oldBalance + $amount;
                $bankAccount->save();
            }
            elseif($type == 'debit')
            {
                $oldBalance                   = $bankAccount->opening_balance;
                $bankAccount->opening_balance = $oldBalance - $amount;
                $bankAccount->save();
            }
        }

    }

    // get font-color code accourding to bg-color
    public static function hex2rgb($hex)
    {
        $hex = str_replace("#", "", $hex);

        if(strlen($hex) == 3)
        {
            $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
            $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
            $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
        }
        else
        {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
        }
        $rgb = array(
            $r,
            $g,
            $b,
        );

        //return implode(",", $rgb); // returns the rgb values separated by commas
        return $rgb; // returns an array with the rgb values
    }

    public static function getFontColor($color_code)
    {
        $rgb = self::hex2rgb($color_code);
        $R   = $G = $B = $C = $L = $color = '';

        $R = (floor($rgb[0]));
        $G = (floor($rgb[1]));
        $B = (floor($rgb[2]));

        $C = [
            $R / 255,
            $G / 255,
            $B / 255,
        ];

        for($i = 0; $i < count($C); ++$i)
        {
            if($C[$i] <= 0.03928)
            {
                $C[$i] = $C[$i] / 12.92;
            }
            else
            {
                $C[$i] = pow(($C[$i] + 0.055) / 1.055, 2.4);
            }
        }

        $L = 0.2126 * $C[0] + 0.7152 * $C[1] + 0.0722 * $C[2];

        if($L > 0.179)
        {
            $color = 'black';
        }
        else
        {
            $color = 'white';
        }

        return $color;
    }

    public static function delete_directory($dir)
    {
        if(!file_exists($dir))
        {
            return true;
        }
        if(!is_dir($dir))
        {
            return unlink($dir);
        }
        foreach(scandir($dir) as $item)
        {
            if($item == '.' || $item == '..')
            {
                continue;
            }
            if(!self::delete_directory($dir . DIRECTORY_SEPARATOR . $item))
            {
                return false;
            }
        }

        return rmdir($dir);
    }

    public static function getSuperAdminValByName($key)
    {
        $data = DB::table('settings');
        $data = $data->where('name', '=', $key);
        $data = $data->first();
        if(!empty($data))
        {
            $record = $data->value;
        }
        else
        {
            $record = '';
        }

        return $record;
    }


    // used for replace email variable (parameter 'template_name','id(get particular record by id for data)')
    public static function replaceVariable($content, $obj)
    {
        $arrVariable = [
            '{store_name}',
            '{order_no}',
            '{customer_name}',
            '{billing_address}',
            '{billing_country}',
            '{billing_city}',
            '{billing_postalcode}',
            '{shipping_address}',
            '{shipping_country}',
            '{shipping_city}',
            '{shipping_postalcode}',
            '{item_variable}',
            '{qty_total}',
            '{sub_total}',
            '{discount_amount}',
            '{shipping_amount}',
            '{total_tax}',
            '{final_total}',
            '{sku}',
            '{quantity}',
            '{product_name}',
            '{variant_name}',
            '{item_tax}',
            '{item_total}',
        ];
        $arrValue    = [
            'store_name' => '',
            'order_no' => '',
            'customer_name' => '',
            'billing_address' => '',
            'billing_country' => '',
            'billing_city' => '',
            'billing_postalcode' => '',
            'shipping_address' => '',
            'shipping_country' => '',
            'shipping_city' => '',
            'shipping_postalcode' => '',
            'item_variable' => '',
            'qty_total' => '',
            'sub_total' => '',
            'discount_amount' => '',
            'shipping_amount' => '',
            'total_tax' => '',
            'final_total' => '',
            'sku' => '',
            'quantity' => '',
            'product_name' => '',
            'variant_name' => '',
            'item_tax' => '',
            'item_total' => '',
        ];

        foreach($obj as $key => $val)
        {
            $arrValue[$key] = $val;
        }

        $arrValue['app_name'] = env('APP_NAME');
        $arrValue['app_url']  = '<a href="' . env('APP_URL') . '" target="_blank">' . env('APP_URL') . '</a>';

        return str_replace($arrVariable, array_values($arrValue), $content);
    }

    // Email Template Modules Function START

    public static function userDefaultData()
    {
        // Make Entry In User_Email_Template
        $allEmail = EmailTemplate::all();
        foreach($allEmail as $email)
        {
            UserEmailTemplate::create(
                [
                    'template_id' => $email->id,
                    'user_id' => 1,
                    'is_active' => 1,
                ]
            );
        }
    }

    // Common Function That used to send mail with check all cases
    public static function sendEmailTemplate($emailTemplate, $mailTo, $obj, $store, $order)
    {
        // find template is exist or not in our record
        $template = EmailTemplate::where('name', 'LIKE', $emailTemplate)->first();

        if(isset($template) && !empty($template))
        {
            // check template is active or not by company
            $is_actives = UserEmailTemplate::where('template_id', '=', $template->id)->first();
            // dd($is_active);

            if($is_actives->is_active == 1)
            {
                // get email content language base
                $content = EmailTemplateLang::where('parent_id', '=', $template->id)->where('lang', 'LIKE', $store->lang)->first();

                $content->from = $template->from;


                if(!empty($content->content))
                {
                    $content->content = self::replaceVariables($content->content, $obj, $store, $order);

                    // send email
                    try
                    {

                        config(
                            [
                                'mail.driver' => $store->mail_driver,
                                'mail.host' => $store->mail_host,
                                'mail.port' => $store->mail_port,
                                'mail.encryption' => $store->mail_encryption,
                                'mail.username' => $store->mail_username,
                                'mail.password' => $store->mail_password,
                                'mail.from.address' => $store->mail_from_address,
                                'mail.from.name' => $store->mail_from_name,
                            ]
                        );
                        $orders=Order::find(Crypt::decrypt($order));
                        $ownername=User::where('id',$store->created_by)->first();

                        if($mailTo==$ownername->email)
                        {

                            Mail::to(
                            [
                                    $store['email'],
                            ]
                            )->send(new CommonEmailTemplate($content, $store));

                        }
                        else
                        {

                            Mail::to(
                            [
                                    $mailTo,

                            ]
                            )->send(new CommonEmailTemplate($content, $store));

                            $user=(Auth::guard('customers')->user());
                        }


                    }
                    catch(\Exception $e)
                    {

                        $error = __('E-Mail has been not sent due to SMTP configuration');
                    }
                    if(isset($error))
                    {
                        $arReturn = [
                            'is_success' => false,
                            'error' => $error,
                        ];
                    }
                    else
                    {
                        $arReturn = [
                            'is_success' => true,
                            'error' => false,
                        ];
                    }
                }
                else
                {
                    $arReturn = [
                        'is_success' => false,
                        'error' => __('Mail not send, email is empty'),
                    ];
                }

                return $arReturn;
            }
            else
            {
                return [
                    'is_success' => true,
                    'error' => false,
                ];
            }
        }
        else
        {
            return [
                'is_success' => false,
                'error' => __('Mail not send, email not found'),
            ];
        }
        //        }
    }




    // used for replace email variable (parameter 'template_name','id(get particular record by id for data)')
    public static function replaceVariables($content, $obj, $store, $order)
    {
        $arrVariable = [
            '{app_name}',
            '{order_name}',
            '{order_status}',
            '{app_url}',
            '{order_url}',
            '{order_id}',
            '{owner_name}',
            '{order_date}',
        ];
        $arrValue    = [
            'app_name' => '-',
            'order_name' => '-',
            'order_status' => '-',
            'app_url' => '-',
            'order_url' => '-',
            'order_id' =>'-',
            'owner_name' => '-',
            'order_date' => '-',
        ];
        foreach($obj as $key => $val)
        {
            $arrValue[$key] = $val;
        }

        $arrValue['app_name']  = $store->name;
        $arrValue['app_url']   = '<a href="' . env('APP_URL') . '" target="_blank">' . env('APP_URL') . '</a>';
        $arrValue['order_url'] = '<a href="' . env('APP_URL') . '/' . $store->slug . '/order/' . $order . '" target="_blank">' . env('APP_URL') . '/' . $store->slug . '/order/' . $order . '</a>';

        $ownername=User::where('id',$store->created_by)->first();
        $id    = Crypt::decrypt($order);
        $order=Order::where('id',$id)->first();

        $arrValue['owner_name']  = $ownername->name;
        $arrValue['order_id']      =$order->order_id;
        $arrValue['order_date']    =self::dateFormat($order->created_at);

        return str_replace($arrVariable, array_values($arrValue), $content);
    }

    // Make Entry in email_tempalte_lang table when create new language
    public static function makeEmailLang($lang)
    {
        $template = EmailTemplate::all();
        foreach($template as $t)
        {
            $default_lang                 = EmailTemplateLang::where('parent_id', '=', $t->id)->where('lang', 'LIKE', 'en')->first();
            $emailTemplateLang            = new EmailTemplateLang();
            $emailTemplateLang->parent_id = $t->id;
            $emailTemplateLang->lang      = $lang;
            $emailTemplateLang->subject   = $default_lang->subject;
            $emailTemplateLang->content   = $default_lang->content;
            $emailTemplateLang->save();
        }
    }

    // For Email template Module
    public static function defaultEmail()
    {
        // Email Template
        $emailTemplate = [
            'Order Created',
            'Status Change',
            'Order Created For Owner',
        ];

        foreach($emailTemplate as $eTemp)
        {
            EmailTemplate::create(
                [
                    'name' => $eTemp,
                    'from' => env('APP_NAME'),
                    'created_by' => 1,
                ]
            );
        }

        $defaultTemplate = [
            'Order Created' => [
                'subject' => 'Order Complete',
                'lang' => [
                    'ar' => '<p>مرحبا ،</p><p>مرحبا بك في {app_name}.</p><p>مرحبا ، {order_name} ، شكرا للتسوق</p><p>قمنا باستلام طلب الشراء الخاص بك ، سيتم الاتصال بك قريبا !</p><p>شكرا ،</p><p>{app_name}</p><p>{order_url}</p>',
                    'da' => '<p>Hej, &nbsp;</p><p>Velkommen til {app_name}.</p><p>Hej, {order_name}, tak for at Shopping</p><p>Vi har modtaget din købsanmodning.</p><p>Tak,</p><p>{app_navn}</p><p>{order_url}</p>',
                    'de' => '<p>Hello, &nbsp;</p><p>Willkommen bei {app_name}.</p><p>Hi, {order_name}, Vielen Dank für Shopping</p><p>Wir haben Ihre Kaufanforderung erhalten, wir werden in Kürze in Kontakt sein!</p><p>Danke,</p><p>{app_name}</p><p>{order_url}</p>',
                    'en' => '<p>Hello,&nbsp;</p><p>Welcome to {app_name}.</p><p>Hi, {order_name}, Thank you for Shopping</p><p>We received your purchase request, we\'ll be in touch shortly!</p><p>Thanks,</p><p>{app_name}</p><p>{order_url}</p>',
                    'es' => '<p>Hola, &nbsp;</p><p>Bienvenido a {app_name}.</p><p>Hi, {order_name}, Thank you for Shopping</p><p>Recibimos su solicitud de compra, ¡estaremos en contacto en breve!</p><p>Gracias,</p><p>{app_name}</p><p>{order_url}</p>',
                    'fr' => '<p>Bonjour, &nbsp;</p><p>Bienvenue dans {app_name}.</p><p>Hi, {order_name}, Thank you for Shopping</p><p>We reçus your purchase request, we \'ll be in touch incess!</p><p>Thanks,</p><p>{app_name}</p><p>{order_url}</p>',
                    'it' => '<p>Ciao, &nbsp;</p><p>Benvenuti in {app_name}.</p><p>Ciao, {order_name}, Grazie per Shopping</p><p>Abbiamo ricevuto la tua richiesta di acquisto, noi \ saremo in contatto a breve!</p><p>Grazie,</p><p>{app_name}</p><p>{order_url}</p>',
                    'ja' => '<p>こんにちは &nbsp;</p><p>{app_name}へようこそ。</p></p><p><p>こんにちは、 {order_name}、お客様の購買要求書をお受け取りいただき、すぐにご連絡いたします。</p><p>ありがとうございます。</p><p>{app_name}</p><p>{order_url}</p>',
                    'nl' => '<p>Hallo, &nbsp;</p><p>Welkom bij {app_name}.</p><p>Hallo, {order_name}, Dank u voor Winkelen</p><p>We hebben uw aankoopaanvraag ontvangen, we zijn binnenkort in contact!</p><p>Bedankt,</p><p>{ app_name }</p><p>{order_url}</p>',
                    'pl' => '<p>Hello, &nbsp;</p><p>Witamy w aplikacji {app_name}.</p><p>Hi, {order_name}, Dziękujemy za zakupy</p><p>Otrzymamy Twój wniosek o zakup, wkrótce będziemy w kontakcie!</p><p>Dzięki,</p><p>{app_name}</p><p>{order_url}</p>',
                    'ru' => '<p>Здравствуйте, &nbsp;</p><p>Вас приветствует {app_name}.</p><p>Hi, {order_name}, Спасибо за Шоппинг</p><p>Мы получили ваш запрос на покупку, мы \ скоро свяжемся!</p><p>Спасибо,</p><p>{app_name}</p><p>{order_url}</p>',
                    'pt' => '<p>NAVE ÓRICA-Тутутутугальстугальский (app_name}).</p><p>Hi, {order_name}, пасссский</p><p>польстугальский потугальский (португальский), "скортугальский".</p><p>nome_do_appсссский!</p><p>{app_name}</p><p>{app_name}</p><p>{order_url}</p>',
                ],
            ],
            'Status Change' => [
                'subject' => 'Order Status',
                'lang' => [
                    'ar' => '<p>Здравствуйте, &nbsp;</p><p>Вас приветствует {app_name}.</p><p>Ваш заказ-{order_status}!</p><p>Hi {order_name}, Thank you for Shopping</p><p>Thanks,</p><p>{app_name}</p><p>{order_url}</p>',
                    'da' => '<p>Hej, &nbsp;</p><p>Velkommen til {app_name}.</p><p>Din ordre er {order_status}!</p><p>Hej {order_navn}, Tak for at Shopping</p><p>Tak,</p><p>{app_navn}</p><p>{order_url}</p>',
                    'de' => '<p>Hello, &nbsp;</p><p>Willkommen bei {app_name}.</p><p>Ihre Bestellung lautet {order_status}!</p><p>Hi {order_name}, Danke für Shopping</p><p>Danke,</p><p>{app_name}</p><p>{order_url}</p>',
                    'en' => '<p>Hello,&nbsp;</p><p>Welcome to {app_name}.</p><p>Your Order is {order_status}!</p><p>Hi {order_name}, Thank you for Shopping</p><p>Thanks,</p><p>{app_name}</p><p>{order_url}</p>',
                    'es' => '<p>Hola, &nbsp;</p><p>Bienvenido a {app_name}.</p><p>Your Order is {order_status}!</p><p>Hi {order_name}, Thank you for Shopping</p><p>Thanks,</p><p>{app_name}</p><p>{order_url}</p>',
                    'fr' => '<p>Bonjour, &nbsp;</p><p>Bienvenue dans {app_name}.</p><p>Votre commande est {order_status} !</p><p>Hi {order_name}, Thank you for Shopping</p><p>Thanks,</p><p>{app_name}</p><p>{order_url}</p>',
                    'it' => '<p>Ciao, &nbsp;</p><p>Benvenuti in {app_name}.</p><p>Il tuo ordine è {order_status}!</p><p>Ciao {order_name}, Grazie per Shopping</p><p>Grazie,</p><p>{app_name}</p><p>{order_url}</p>',
                    'ja' => '<p>Ciao, &nbsp;</p><p>Benvenuti in {app_name}.</p><p>Il tuo ordine è {order_status}!</p><p>Ciao {order_name}, Grazie per Shopping</p><p>Grazie,</p><p>{app_name}</p><p>{order_url}</p>',
                    'nl' => '<p>Hallo, &nbsp;</p><p>Welkom bij {app_name}.</p><p>Uw bestelling is {order_status}!</p><p>Hi {order_name}, Dank u voor Winkelen</p><p>Bedankt,</p><p>{app_name}</p><p>{order_url}</p>',
                    'pl' => '<p>Hello, &nbsp;</p><p>Witamy w aplikacji {app_name}.</p><p>Twoje zamówienie to {order_status}!</p><p>Hi {order_name}, Dziękujemy za zakupy</p><p>Thanks,</p><p>{app_name}</p><p>{order_url}</p>',
                    'ru' => '<p>Здравствуйте, &nbsp;</p><p>Вас приветствует {app_name}.</p><p>Ваш заказ-{order_status}!</p><p>Hi {order_name}, Thank you for Shopping</p><p>Thanks,</p><p>{app_name}</p><p>{order_url}</p>',
                    'pt' => '<p>SHOPPING CENTER-Тутутутугальстугальский (app_name}).</p><p>nomeia альстугальский (order_status}!</p><p>Hi {order_name}, Obrigado por Shopping</p><p>Obrigado,</p><p>{app_name}</p><p>{order_url}</p>',
                ],
            ],

            'Order Created For Owner' => [
                'subject' => 'Order Detail',
                'lang' => [
                    'ar' => '<p> مرحبًا ، </ p> <p> عزيزي {owner_name}. </p> <p> هذا أمر تأكيد {order_id} ضعه على <span style = \"font-size: 1rem؛\"> {order_date}. </span> </p> <p> شكرًا ، </ p> <p> {order_url} </p>',
                    'da' => '<p>Hej </p><p>Kære {owner_name}.</p><p>Dette er ordrebekræftelse {order_id} sted på <span style=\"font-size: 1rem;\">{order_date}. </span></p><p>Tak,</p><p>{order_url}</p>',
                    'de' => '<p>Hallo, </p><p>Sehr geehrter {owner_name}.</p><p>Dies ist die Auftragsbestätigung {order_id}, die am <span style=\"font-size: 1rem;\">{order_date} aufgegeben wurde. </span></p><p>Danke,</p><p>{order_url}</p>',
                    'en' => '<p>Hello,&nbsp;</p><p>Dear {owner_name}.</p><p>This is Confirmation Order {order_id} place on&nbsp;<span style=\"font-size: 1rem;\">{order_date}.</span></p><p>Thanks,</p><p>{order_url}</p>',
                    'es' => '<p> Hola, </p> <p> Estimado {owner_name}. </p> <p> Este es el lugar de la orden de confirmación {order_id} en <span style = \"font-size: 1rem;\"> {order_date}. </span> </p> <p> Gracias, </p> <p> {order_url} </p>',
                    'fr' => '<p>Bonjour, </p><p>Cher {owner_name}.</p><p>Ceci est la commande de confirmation {order_id} passée le <span style=\"font-size: 1rem;\">{order_date}. </span></p><p>Merci,</p><p>{order_url}</p>',
                    'it' => '<p>Ciao, </p><p>Gentile {owner_name}.</p><p>Questo è l\'ordine di conferma {order_id} effettuato su <span style=\"font-size: 1rem;\">{order_date}. </span></p><p>Grazie,</p><p>{order_url}</p>',
                    'ja' => '<p>こんにちは、</ p> <p>親愛なる{owner_name}。</ p> <p>これは、<span style = \"font-size：1rem;\"> {order_date}の確認注文{order_id}の場所です。 </ span> </ p> <p>ありがとうございます</ p> <p> {order_url} </ p>',
                    'nl' => '<p>Hallo, </p><p>Beste {owner_name}.</p><p>Dit is de bevestigingsopdracht {order_id} die is geplaatst op <span style=\"font-size: 1rem;\">{order_date}. </span></p><p>Bedankt,</p><p>{order_url}</p>',
                    'pl' => '<p>Witaj, </p><p>Drogi {owner_name}.</p><p>To jest potwierdzenie zamówienia {order_id} złożone na <span style=\"font-size: 1rem;\">{order_date}. </span></p><p>Dzięki,</p><p>{order_url}</p>',
                    'ru' => '<p> Здравствуйте, </p> <p> Уважаемый {owner_name}. </p> <p> Это подтверждение заказа {order_id} на <span style = \"font-size: 1rem;\"> {order_date}. </span> </p> <p> Спасибо, </p> <p> {order_url} </p>',
                    'pt' => '<p> Térica-Dicas de Cadeia Pública de Тутутугальский (owner_name}). </p> <p> Тугальстугальстугальский (order_id} ний <span style = \" font-size: 1rem; \ "> {order_date}. </span> </p> <p> nome_do_chave de vida, </p> <p> {order_url} </p> <p> {order_url}',
                ],
            ],

        ];

        $email = EmailTemplate::all();

        foreach($email as $e)
        {
            foreach($defaultTemplate[$e->name]['lang'] as $lang => $content)
            {
                EmailTemplateLang::create(
                    [
                        'parent_id' => $e->id,
                        'lang' => $lang,
                        'subject' => $defaultTemplate[$e->name]['subject'],
                        'content' => $content,
                    ]
                );
            }
        }
    }


    public static function CustomerAuthCheck($slug = null)
    {
        if($slug == null)
        {
            $slug = \Request::segment(1);
        }
        $auth_customer = Auth::guard('customers')->user();
        if(!empty($auth_customer))
        //
        {
            $store_id = Store::where('slug', $slug)->pluck('id')->first();
            $customer  = Customer::where('store_id', $store_id)->where('email', $auth_customer->email)->count();
            if($customer > 0)
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }
    public static function success_res($msg = "", $args = array())
    {

        $msg       = $msg == "" ? "success" : $msg;
        $msg_id    = 'success.' . $msg;
        $converted = \Lang::get($msg_id, $args);
        $msg       = $msg_id == $converted ? $msg : $converted;
        $json      = array(
            'flag' => 1,
            'msg' => $msg,
        );

        return $json;
    }

    public static function error_res($msg = "", $args = array())
    {

        $msg       = $msg == "" ? "error" : $msg;
        $msg_id    = 'error.' . $msg;
        $converted = \Lang::get($msg_id, $args);
        $msg       = $msg_id == $converted ? $msg : $converted;
        $json      = array(
            'flag' => 0,
            'msg' => $msg,
        );

        return $json;
    }



         public static function send_twilio_msg($to, $msg,$store)
    {

        try
        {
            $account_sid    = $store->twilio_sid;

            $auth_token = $store->twilio_token;

            $twilio_number =$store->twilio_from;

            $client = new Client($account_sid, $auth_token);

            $client->messages->create($to, [
                'from' => $twilio_number,
                'body' => $msg]) ;
        }
        catch(\Exception $e)
        {
            return $e;
        }


    }

    public static function order_create_owner($order,$owner, $store)
    {
         try
        {
             $msg=__("Hello,
Dear".' '.$owner->name.' '.",
This is Confirmation Order ".' '.$order->order_id."place on".self::dateFormat($order->created_at)."
Thanks,");



        Utility::send_twilio_msg($store->notification_number,$msg,$store);
        }
        catch(\Exception $e)
        {
            return $e;
        }



        //  dd('SMS Sent Successfully.');

    }

    public static function order_create_customer($order,$customer, $store)
    {
         try
        {
             $msg=__("Hello,
Welcome to".' '.$store->name.' '.",
Thank you for your purchase from".' '.$store->name.".
Order Number:".' '.$order->order_id.".
Order Date:".' '.self::dateFormat($order->created_at));

        Utility::send_twilio_msg($customer->phone_number,$msg,$store);
        }
        catch(\Exception $e)
        {
            return $e;
        }


        //  dd('SMS Sent Successfully.');

    }

    public static function status_change_customer($order,$customer, $store)
    {
        try
        {
            $msg=__("Hello,
            Welcome to".' '.$store->name.' '.",
            Your Order is".' '.$order->status.".
            Hi".' '.$order->name.", Thank you for Shopping.
            Thanks,
            ".$store->name);

            Utility::send_twilio_msg($customer->phone_number,$msg,$store);

        }
        catch(\Exception $e)
        {
            return $e;
        }
    }

        public static function colorset(){
            if(\Auth::user())
            {
                if(\Auth::user()->type == 'super admin')
                {
                    $user = \Auth::user();
                    $setting = DB::table('settings')->where('created_by',$user->id)->pluck('value','name')->toArray();
                }
                else
                {
                    $setting = DB::table('settings')->where('created_by', \Auth::user()->creatorId())->pluck('value','name')->toArray();
                }
            }
            else
            {
                $user = User::where('type','super admin')->first();
                $setting = DB::table('settings')->where('created_by',$user->id)->pluck('value','name')->toArray();
            }
            if(!isset($setting['color']))
            {
                $setting = Utility::settings();
            }
            return $setting;
        }

        public static function GetLogo()
        {
            $setting = Utility::colorset();

            if(\Auth::user() && \Auth::user()->type != 'super admin')
            {
                if($setting['cust_darklayout'] == 'on')
                {
                    return Utility::getValByName('company_logo_light');
                }
                else
                {
                    return Utility::getValByName('company_logo_dark');
                }
            }
            else
            {
                if($setting['cust_darklayout'] == 'on')
                {
                    return Utility::getValByName('logo_light');
                }
                else
                {
                    return Utility::getValByName('logo_dark');
                }

            }

        }
}

