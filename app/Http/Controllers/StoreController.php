<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\BlogSocial;
use App\Models\Location;
use App\Mail\OrderMail;
use App\Mail\ProdcutMail;
use App\Models\Order;
use App\Models\PageOption;
use App\Models\Plan;
use App\Models\PlanOrder;
use App\Models\Product;
use App\Models\Product_images;
use App\Models\ProductCategorie;
use App\Models\ProductCoupon;
use App\Models\ProductTax;
use App\Models\ProductVariantOption;
use App\Models\Ratting;
use App\Models\Shipping;
use App\Models\Store;
use App\Models\StoreThemeSettings;
use App\Models\Subscription;
use App\Models\User;
use App\Models\UserDetail;
use App\Models\UserStore;
use App\Models\Utility;
use App\Models\PurchasedProducts;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class StoreController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {

        $storelang=session()->get('lang');

        if(isset($storelang))
        {
        \App::setLocale(isset($storelang) ? $storelang : 'en');
        }

    }

    public function index()
    {
        if(\Auth::user()->type == 'super admin')
        {
             $users = User::select(
                [
                    'users.*',
                    'stores.is_store_enabled as store_display',
                ]
            )->join('stores', 'stores.created_by', '=', 'users.id')->where('users.created_by', \Auth::user()->creatorId())->where('users.type', '=', 'Owner')->groupBy('users.id')->get();

            // $users  = User::where('created_by', '=', \Auth::user()->creatorId())->where('type', '=', 'Owner')->get();
            $stores = Store::get();

            return view('admin_store.index', compact('stores', 'users'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin_store.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {


        if(\Auth::user()->type == 'super admin')
        {
            $settings = Utility::settings();

            $objUser = User::create(
                [
                    'name' => $request['name'],
                    'email' => $request['email'],
                    'password' => Hash::make($request['password']),
                    'type' => 'Owner',
                    'lang' => !empty($settings['default_language']) ? $settings['default_language'] : 'en',
                    'avatar' => 'avatar.png',
                    'plan' => Plan::first()->id,
                    'created_by' => 1,
                ]
            );
            // dd($request['is_store_enabled']);
            $objStore                   = Store::create(
                [
                    'name' => $request['store_name'],
                    'email' => $request['email'],
                    'logo' => !empty($settings['logo']) ? $settings['logo'] : 'logo-dark.png',
                    'invoice_logo' => !empty($settings['logo']) ? $settings['logo'] : 'invoice_logo.png',
                    'lang' => !empty($settings['default_language']) ? $settings['default_language'] : 'en',
                    'currency' => !empty($settings['currency_symbol']) ? $settings['currency_symbol'] : '$',
                    'currency_code' => !empty($settings->currency) ? $settings->currency : 'USD',
                    'paypal_mode' => 'sandbox',
                    'created_by' => $objUser->id,
                ]
            );
            $objStore->is_store_enabled = 1;
            $objStore->enable_storelink = 'on';
            $objStore->content          = 'Hi,
*Welcome to* {store_name},
Your order is confirmed & your order no. is {order_no}
Your order detail is:
Name : {customer_name}
Address : {billing_address} {billing_city} , {shipping_address} {shipping_city}
~~~~~~~~~~~~~~~~
{item_variable}
~~~~~~~~~~~~~~~~
Qty Total : {qty_total}
Sub Total : {sub_total}
Discount Price : {discount_amount}
Shipping Price : {shipping_amount}
Tax : {total_tax}
Total : {final_total}
~~~~~~~~~~~~~~~~~~
To collect the order you need to show the receipt at the counter.
                Thanks {store_name}
                ';

            $objStore->item_variable    = '{sku} : {quantity} x {product_name} - {variant_name} + {item_tax} = {item_total}';
            $objStore->theme_dir        = 'theme1';
            $objStore->store_theme      = 'green-color.css';
            $objStore->save();
            $objUser->current_store = $objStore->id;
            $objUser->save();
            UserStore::create(
                [
                    'user_id' => $objUser->id,
                    'store_id' => $objStore->id,
                    'permission' => 'Owner',
                ]
            );

            return redirect()->back()->with('success', __('Successfully Created!'));
        }
        else
        {
            if(\Auth::user()->type == 'Owner')
            {
                $user        = \Auth::user();
                $total_store = $user->countStore();
                $creator     = User::find($user->creatorId());
                $plan        = Plan::find($creator->plan);
                $settings    = Utility::settings();

                if($total_store < $plan->max_stores || $plan->max_stores == -1)
                {
                    $objStore                   = Store::create(
                        [
                            'created_by' => \Auth::user()->id,
                            'name' => $request['store_name'],
                            'logo' => !empty($settings['logo']) ? $settings['logo'] : 'logo-dark.png',
                            'invoice_logo' => !empty($settings['logo']) ? $settings['logo'] : 'invoice_logo.png',
                            'lang' => !empty($settings['default_language']) ? $settings['default_language'] : 'en',
                            'currency' => !empty($settings['currency_symbol']) ? $settings['currency_symbol'] : '$',
                            'currency_code' => !empty($settings['currency']) ? $settings['currency'] : 'USD',
                            'paypal_mode' => 'sandbox',
                        ]
                    );
                    $objStore->email = \Auth::user()->email;
                    $objStore->is_store_enabled=1;
                    $objStore->enable_storelink = 'on';
                    $objStore->content          = 'Hi,
*Welcome to* {store_name},
Your order is confirmed & your order no. is {order_no}
Your order detail is:
Name : {customer_name}
Address : {billing_address} {billing_city} , {shipping_address} {shipping_city}
~~~~~~~~~~~~~~~~
{item_variable}
~~~~~~~~~~~~~~~~
Qty Total : {qty_total}
Sub Total : {sub_total}
Discount Price : {discount_amount}
Shipping Price : {shipping_amount}
Tax : {total_tax}
Total : {final_total}
~~~~~~~~~~~~~~~~~~
To collect the order you need to show the receipt at the counter.
                Thanks {store_name}
                ';
                    $objStore->item_variable    = '{sku} : {quantity} x {product_name} - {variant_name} + {item_tax} = {item_total}';
                    $objStore->theme_dir        = 'theme1';
                    $objStore->store_theme      = 'green-color.css';
                    $objStore->save();
                    \Auth::user()->current_store = $objStore->id;
                    \Auth::user()->save();
                    UserStore::create(
                        [
                            'user_id' => \Auth::user()->id,
                            'store_id' => $objStore->id,
                            'permission' => 'Owner',
                        ]
                    );

                    return redirect()->back()->with('success', __('Successfully Created!'));
                }
                else
                {
                    return redirect()->back()->with('error', __('Your Store limit is over, Please upgrade plan'));
                }

            }
        }

    }

    /**
     * Display the specified resource.
     *
     * @param \App\Store $store
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Store $store)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Store $store
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if(\Auth::user()->type == 'super admin')
        {
            $user       = User::find($id);
            $user_store = UserStore::where('user_id', $id)->first();
            $store      = Store::where('id', $user_store->store_id)->first();

            return view('admin_store.edit', compact('store', 'user'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Store $store
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if(\Auth::user()->type == 'super admin')
        {
            $store      = Store::find($id);
            $user_store = UserStore::where('store_id', $id)->first();
            $user       = User::where('id', $user_store->user_id)->first();
            $validator  = \Validator::make(
                $request->all(), [
                                   'name' => 'required|max:120',
                                   'store_name' => 'required|max:120',
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $store['name']  = $request->store_name;
            $store['email'] = $request->email;
            $store->update();

            $user['name']  = $request->name;
            $user['email'] = $request->email;
            $user->update();
            return redirect()->back()->with('Success', __('Successfully Updated!'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Store $store
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(\Auth::user()->type == 'super admin')
        {
            if(isset($id))
            {
                $user       = User::find($id);
                $user_store = UserStore::where('user_id', $id)->first();
                $store      = Store::where('id', $user_store->store_id)->first();

                UserStore::where('store_id', $store->id)->delete();
                PageOption::where('store_id', $store->id)->delete();
                Order::where('user_id', $store->id)->delete();
                ProductCategorie::where('store_id', $store->id)->delete();
                ProductCoupon::where('store_id', $store->id)->delete();
                ProductTax::where('store_id', $store->id)->delete();
                Blog::where('store_id', $store->id)->delete();
                BlogSocial::where('store_id', $store->id)->delete();
                StoreThemeSettings::where('store_id', $store->id)->delete();
                Subscription::where('store_id', $store->id)->delete();
                Shipping::where('store_id', $store->id)->delete();
                Location::where('store_id', $store->id)->delete();
                Ratting::where('slug', $store->slug)->delete();
                $products = Product::where('store_id', $store->id)->get();
                $pro_img  = new ProductController();
                foreach($products as $pro)
                {
                    $pro_img->fileDelete($pro->id);
                    ProductVariantOption::where('product_id', $pro->id)->delete();
                }
                UserDetail::where('store_id', $store->id)->delete();
                PlanOrder::where('store_id', $store->id)->delete();
                Product::where('store_id', $store->id)->delete();

                $store->delete();
                $user->delete();
                $user_store->delete();

                return redirect()->back()->with(
                    'success', __('Store Deleted!')
                );
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function customDomain()
    {
        if(\Auth::user()->type == 'super admin')
        {
            $serverName = str_replace(
                [
                    'http://',
                    'https://',
                ], '', env('APP_URL')
            );
            $serverIp   = gethostbyname($serverName);

            if($serverIp == $_SERVER['SERVER_ADDR'])
            {
                $serverIp;
            }
            else
            {
                $serverIp = request()->server('SERVER_ADDR');
            }
            $users  = User::where('created_by', '=', \Auth::user()->creatorId())->where('type', '=', 'owner')->get();
            $stores = Store::where('enable_domain', 'on')->get();

            return view('admin_store.custom_domain', compact('users', 'stores', 'serverIp'));
        }
        else
        {
            return redirect()->back()->with('error', __('permission Denied'));
        }

    }

    public function subDomain()
    {
        if(\Auth::user()->type == 'super admin')
        {
            $serverName = str_replace(
                [
                    'http://',
                    'https://',
                ], '', env('APP_URL')
            );
            $serverIp   = gethostbyname($serverName);

            if($serverIp != $serverName)
            {
                $serverIp;
            }
            else
            {
                $serverIp = request()->server('SERVER_ADDR');
            }
            $users  = User::where('created_by', '=', \Auth::user()->creatorId())->where('type', '=', 'owner')->get();
            $stores = Store::where('enable_subdomain', 'on')->get();

            return view('admin_store.subdomain', compact('users', 'stores', 'serverIp'));
        }
        else
        {
            return redirect()->back()->with('error', __('permission Denied'));
        }

    }

    public function ownerstoredestroy($id)
    {
        $user        = Auth::user();
        $store       = Store::find($id);
        $user_stores = UserStore::where('user_id', $user->id)->count();

        if($user_stores > 1)
        {
            UserStore::where('store_id', $store->id)->delete();
            PageOption::where('store_id', $store->id)->delete();
            Order::where('user_id', $store->id)->delete();
            ProductCategorie::where('store_id', $store->id)->delete();
            ProductCoupon::where('store_id', $store->id)->delete();
            ProductTax::where('store_id', $store->id)->delete();
            Blog::where('store_id', $store->id)->delete();
            BlogSocial::where('store_id', $store->id)->delete();
            StoreThemeSettings::where('store_id', $store->id)->delete();
            Subscription::where('store_id', $store->id)->delete();
            Shipping::where('store_id', $store->id)->delete();
            Location::where('store_id', $store->id)->delete();
            Ratting::where('slug', $store->slug)->delete();
            $products = Product::where('store_id', $store->id)->get();
            $pro_img  = new ProductController();
            foreach($products as $pro)
            {
                $pro_img->fileDelete($pro->id);
                ProductVariantOption::where('product_id', $pro->id)->delete();
            }
            UserDetail::where('store_id', $store->id)->delete();
            PlanOrder::where('store_id', $store->id)->delete();
            Product::where('store_id', $store->id)->delete();

            $store->delete();
            $userstore = UserStore::where('user_id', $user->id)->first();

            $user->current_store = $userstore->id;
            $user->save();

            return redirect()->route('dashboard');
        }
        else
        {
            return redirect()->back()->with('error', __('You have only one store'));
        }


    }

    public function savestoresetting(Request $request, $id)
    {

        $validator = \Validator::make(
            $request->all(), [
                               'name' => 'required|max:120',
                               'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                               'logo' => 'mimes:jpeg,png,jpg,gif,svg,pdf,doc|max:20480',
                               'invoice_logo' => 'mimes:jpeg,png,jpg,gif,svg,pdf,doc|max:20480',
                           ]
        );
        if($request->enable_domain == 'enable_domain')
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'domains' => 'required',
                               ]
            );
        }
        if($request->enable_domain == 'enable_subdomain')
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'subdomain' => 'required',
                               ]
            );
        }
        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }
        if(!empty($request->logo))
        {
            $filenameWithExt = $request->file('logo')->getClientOriginalName();
            $filename        = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension       = $request->file('logo')->getClientOriginalExtension();
            $fileNameToStore = $filename . '_' . time() . '.' . $extension;
            $dir             = storage_path('uploads/store_logo/');
            if(!file_exists($dir))
            {
                mkdir($dir, 0777, true);
            }
            $path = $request->file('logo')->storeAs('uploads/store_logo/', $fileNameToStore);

        }
        if(!empty($request->invoice_logo))
        {
            $extension              = $request->file('invoice_logo')->getClientOriginalExtension();
            $fileNameToStoreInvoice = 'invoice_logo' . '_' . $id . '.' . $extension;
            $dir                    = storage_path('uploads/store_logo/');
            if(!file_exists($dir))
            {
                mkdir($dir, 0777, true);
            }
            $path = $request->file('invoice_logo')->storeAs('uploads/store_logo/', $fileNameToStoreInvoice);
        }

        if($request->enable_domain == 'enable_domain')
        {
            // Remove the http://, www., and slash(/) from the URL
            $input = $request->domains;
            // If URI is like, eg. www.way2tutorial.com/
            $input = trim($input, '/');
            // If not have http:// or https:// then prepend it
            if(!preg_match('#^http(s)?://#', $input))
            {
                $input = 'http://' . $input;
            }

            $urlParts = parse_url($input);
            // Remove www.
            $domain_name = preg_replace('/^www\./', '', $urlParts['host']);
            // Output way2tutorial.com
        }
        if($request->enable_domain == 'enable_subdomain')
        {
            // Remove the http://, www., and slash(/) from the URL
            $input = env('APP_URL');

            // If URI is like, eg. www.way2tutorial.com/
            $input = trim($input, '/');
            // If not have http:// or https:// then prepend it
            if(!preg_match('#^http(s)?://#', $input))
            {
                $input = 'http://' . $input;
            }

            $urlParts = parse_url($input);

            // Remove www.
            $subdomain_name = preg_replace('/^www\./', '', $urlParts['host']);
            // Output way2tutorial.com
            $subdomain_name = $request->subdomain . '.' . $subdomain_name;
        }

        $store = Store::find($id);
        if($store->name != $request->name)
        {
            $data          = ['name' => $request->name];
            $slug          = Store::create($data);
            $store['slug'] = $slug->slug;
        }
        $store['name']  = $request->name;
        $store['email'] = $request->email;
        if($request->enable_domain == 'enable_domain')
        {
            $store['domains'] = $domain_name;
        }

        $store['enable_storelink'] = ($request->enable_domain == 'enable_storelink' || empty($request->enable_domain)) ? 'on' : 'off';
        $store['enable_domain']    = ($request->enable_domain == 'enable_domain') ? 'on' : 'off';
        $store['enable_subdomain'] = ($request->enable_domain == 'enable_subdomain') ? 'on' : 'off';

        if($request->enable_domain == 'enable_subdomain')
        {
            $store['subdomain'] = $subdomain_name;
        }
        $store['tagline']         = $request->tagline;
        $store['enable_rating']   = $request->enable_rating ?? 'off';
        $store['blog_enable']     = $request->blog_enable ?? 'off';
        $store['enable_shipping'] = $request->enable_shipping ?? 'off';
        $store['address']         = $request->address;
        $store['city']            = $request->city;
        $store['state']           = $request->state;
        $store['zipcode']         = $request->zipcode;
        $store['country']         = $request->country;
        $store['lang']            = $request->store_default_language;
        $store['google_analytic'] = $request->google_analytic;
        $store['fbpixel_code']    = $request->fbpixel_code;
        $store['storejs']         = $request->storejs;
        $store['metakeyword']     = $request->metakeyword;
        $store['metadesc']        = $request->metadesc;
        $store['decimal_number']  = $request->decimal_number;


        if(!empty($fileNameToStore))
        {
            $store['logo'] = $fileNameToStore;
        }
        if(!empty($fileNameToStoreInvoice))
        {
            $store['invoice_logo'] = $fileNameToStoreInvoice;
        }
        $store['created_by'] = \Auth::user()->creatorId();
        $store->update();

        return redirect()->back()->with('success', __('Store successfully Update.'));
    }

    public function storeSlug($slug)
    {

        $store = Store::where('slug', $slug)->where('is_store_enabled','1')->first();

        if(isset($store->lang))
        {

            $lang = session()->get('lang');

            if(!isset($lang))
            {
             session(['lang' => $store->lang]);
             $storelang=session()->get('lang');
             \App::setLocale(isset($storelang) ? $storelang : 'en');
            }
            else
            {
                session(['lang' => $lang]);
                $storelang=session()->get('lang');
                \App::setLocale(isset($storelang) ? $storelang : 'en');
            }
        }

        if(!empty($store))
        {
            if(!Auth::check())
            {
                visitor()->visit($slug);
            }
            if(Utility::CustomerAuthCheck($slug) == false)
            {
                visitor()->visit($slug);
            }
            $userstore      = UserStore::where('store_id', $store->id)->first();
            $settings       = \DB::table('settings')->where('name', 'company_favicon')->where('created_by', $userstore->user_id)->first();
            $page_slug_urls = PageOption::where('store_id', $store->id)->get();
            $blog           = Blog::where('store_id', $store->id)->count();

            $storethemesetting = \App\Models\Utility::demoStoreThemeSetting($store->id, $store->theme_dir);
            $topRatedProducts  = Ratting::select(DB::raw('*,(SUM(ratting) / COUNT(product_id)) as avg_ratting'))->groupBy('product_id')->orderBy('avg_ratting', 'DESC')->where('slug', $slug);

            if($store->theme_dir == 'theme4')
            {
                $topRatedProducts = $topRatedProducts->limit(3)->get();
            }
            else
            {
                $topRatedProducts = $topRatedProducts->limit(4)->get();
            }

            if(empty($store))
            {
                return redirect()->back()->with('error', __('Store not available'));
            }
            session(['slug' => $slug]);
            $cart = session()->get($slug);

            $pro_categories = ProductCategorie::where('store_id', $userstore->store_id)->get();
            $categories     = ProductCategorie::where('store_id', $userstore->store_id)->get()->pluck('name', 'id');
            $categories->prepend('Start shopping', 0);

            $products      = [];
            $product_count = [];

            foreach($categories as $id => $category)
            {
                $product = Product::where('store_id', $store->id)->where('product_display','on');
                if($id != 0)
                {
                    $product->whereRaw('FIND_IN_SET("' . $id . '", product_categorie)');
                }
                $product = $product->get();
                if($id != 0)
                {
                    $product_count[] = count($product);
                }
                $products[$category] = $product;
            }

            $total_item = 0;
            if(isset($cart['products']))
            {
                if(isset($cart) && !empty($cart['products']))
                {
                    $total_item = count($cart['products']);
                }
                else
                {
                    $total_item = 0;
                }
            }
            if(isset($cart['wishlist']))
            {
                $wishlist = $cart['wishlist'];
            }
            else
            {
                $wishlist = [];
            }
            $theme3_product_image = null;
            $theme3_product       = Product::where('store_id', $store->id)->where('product_display','on')->orderBy('id', 'DESC')->first();
            if(!empty($theme3_product))
            {
                $theme3_product_image = Product_images::where('product_id', $theme3_product->id)->limit(2)->get();
            }
            $blogs                 = Blog::where('store_id', $store->id)->orderby('id', 'DESC')->limit(3)->get();
            $theme3_product_random = Product::where('store_id', $store->id)->where('product_display','on')->inRandomOrder()->first();

            $lang=$store->lang;


            return view('storefront.' . $store->theme_dir . '.index', compact('theme3_product_random', 'blogs', 'theme3_product_image', 'theme3_product', 'wishlist', 'products', 'settings', 'store', 'categories', 'total_item', 'page_slug_urls', 'blog', 'storethemesetting', 'pro_categories', 'topRatedProducts', 'product_count'));
        }
        else
        {
            return abort('404', 'Page not found');
            // return redirect('/');
        }
    }

    public function product($slug, $categorie_name = null, Request $request)
    {

        $store          = Store::where('slug', $slug)->where('is_store_enabled','1')->first();
        if(empty($store))
        {
            return redirect()->back()->with('error', __('Store not available'));
        }
        $userstore      = UserStore::where('store_id', $store->id)->first();
        $settings       = \DB::table('settings')->where('name', 'company_favicon')->where('created_by', $userstore->user_id)->first();
        $page_slug_urls = PageOption::where('store_id', $store->id)->get();
        $blog           = Blog::where('store_id', $store->id)->count();


        $storethemesetting = \App\Models\Utility::demoStoreThemeSetting($store->id, $store->theme_dir);

        $topRatedProducts = Ratting::where('slug', $store->slug)->orderBy('ratting', 'DESC')->limit(4)->get();


        session(['slug' => $slug]);
        $cart = session()->get($slug);

        $pro_categories = ProductCategorie::where('store_id', $userstore->store_id)->get();
        $categories     = ProductCategorie::where('store_id', $userstore->store_id)->get()->pluck('name', 'id');
        $categories->prepend('Start shopping', 0);
        $products      = [];
        $product_count = [];
        foreach($categories as $id => $category)
        {
            $product = Product::where('store_id', $store->id)->where('product_display','on');
            if($id != 0)
            {
                $product->whereRaw('FIND_IN_SET("' . $id . '", product_categorie)');
            }
            $product       = $product->get();
            $product_count = count($product);

            $products[$category] = $product;

            if(!empty($request->search_data))
            {
                $product                    = Product::where('store_id', $store->id)->where('product_display','on')->where('name', 'like', '%' . $request->search_data . '%')->get();
                $products['Start shopping'] = $product;
            }

        }

        $total_item = 0;
        if(isset($cart['products']))
        {
            if(isset($cart) && !empty($cart['products']))
            {
                $total_item = count($cart['products']);
            }
            else
            {
                $total_item = 0;
            }
        }

        if(!$categorie_name)
        {
            $categorie_name = 'Start shopping';
        }

        if(isset($cart['wishlist']))
        {
            $wishlist = $cart['wishlist'];
        }
        else
        {
            $wishlist = [];
        }

        return view('storefront.' . $store->theme_dir . '.product', compact('wishlist', 'products', 'categorie_name', 'settings', 'store', 'categories', 'total_item', 'page_slug_urls', 'blog', 'storethemesetting', 'pro_categories', 'topRatedProducts', 'product_count'));
    }

    public function pageOptionSlug($slug)
    {
        $pageoption     = PageOption::where('slug', $slug)->first();
        $store          = Store::where('id', $pageoption->store_id)->first();
        $page_slug_urls = PageOption::where('store_id', $store->id)->get();
        $blog           = Blog::where('store_id', $store->id)->first();

        return view('storefront.' . $store->theme_dir . '.pageslug', compact('pageoption', 'store', 'page_slug_urls', 'blog'));
    }

    public function StoreBlog($slug)
    {
        $store          = Store::where('slug', $slug)->where('is_store_enabled','1')->first();
        if(empty($store))
        {
            return redirect()->back()->with('error', __('Store not available'));
        }
        $page_slug_urls = PageOption::where('store_id', $store->id)->get();
        $blog           = Blog::where('store_id', $store->id)->first();
        $blogs          = Blog::where('store_id', $store->id)->get();

        return view('storefront.' . $store->theme_dir . '.store_blog', compact('store', 'page_slug_urls', 'blog', 'blogs'));
    }

    public function StoreBlogView($slug, $blog_id)
    {
        $store          = Store::where('slug', $slug)->where('is_store_enabled','1')->first();
         if(empty($store))
        {
            return redirect()->route('store.slug', $slug);
        }
        $page_slug_urls = PageOption::where('store_id', $store->id)->get();
        $blog           = Blog::where('store_id', $store->id)->first();
        $blogs          = Blog::where('store_id', $store->id)->where('id', $blog_id)->first();
        $socialblogs    = BlogSocial::where('store_id', $store->id)->first();
        $socialblogsarr = [];

        if(!empty($socialblogs))
        {
            $arrSocialDatas = $socialblogs->toArray();
            unset($arrSocialDatas['id'], $arrSocialDatas['enable_social_button'], $arrSocialDatas['store_id'], $arrSocialDatas['created_by'], $arrSocialDatas['created_at'], $arrSocialDatas['updated_at']);

            foreach($arrSocialDatas as $k => $v)
            {
                if($v == 'on')
                {
                    $newName = str_replace('enable_', '', $k);
                    array_push($socialblogsarr, strtolower($newName));
                }
            }
        }

        $socialblogsarr = json_encode($socialblogsarr);
        if(!empty($blogs))
        {
            return view('storefront.' . $store->theme_dir . '.store_blog_view', compact('store', 'blog', 'page_slug_urls', 'blogs', 'socialblogs', 'socialblogsarr'));

        }
        else
        {
            return redirect()->route('store.slug', $store->slug);
        }
    }

    public function productView($slug, $id)
    {
        $product_ratings = Ratting::where('slug', $slug)->where('product_id', $id)->get();
        $store_setting   = Store::where('slug', $slug)->first();
        $cart            = session()->get($slug);
        $page_slug_urls  = PageOption::where('store_id', $store_setting->id)->get();
        $blog            = Blog::where('store_id', $store_setting->id)->count();

        $ratting    = Ratting::where('product_id', $id)->where('rating_view', 'on')->sum('ratting');
        $user_count = Ratting::where('product_id', $id)->where('rating_view', 'on')->count();
        if($user_count > 0)
        {
            $avg_rating = number_format($ratting / $user_count, 1);
        }
        else
        {
            $avg_rating = number_format($ratting / 1, 1);
        }

        $store = Store::where('slug', $slug)->where('is_store_enabled','1')->first();
        if(empty($store))
        {
            return redirect()->route('store.slug', $slug);
        }

        $products     = Product::where('id', $id)->where('product_display','on')->first();

        if($products=="")
        {
             return redirect()->back()->with('error', __('Product not available'));
        }
        $all_products = Product::where('store_id', $store->id)->where('product_categorie', $products->product_categorie)->where('product_display','on')->get();

        $products_image = Product_images::where('product_id', $products->id)->get();

        $variant_item = 0;
        $total_item   = 0;
        if(isset($cart['products']))
        {
            foreach($cart['products'] as $item)
            {
                if(isset($cart) && !empty($cart['products']))
                {
                    if(isset($item['variants']))
                    {
                        $variant_item += count($item['variants']);
                    }
                    else
                    {
                        $product_item = count($cart['products']);
                        $total_item   = $variant_item + $product_item;
                    }
                }
                else
                {
                    $total_item = 0;
                }
            }
        }

        $variant_name          = json_decode($products->variants_json);
        $product_variant_names = $variant_name;

        if(isset($cart['wishlist']))
        {
            $wishlist = $cart['wishlist'];
        }
        else
        {
            $wishlist = [];
        }

        return view('storefront.' . $store->theme_dir . '.view', compact('wishlist', 'products', 'store', 'user_count', 'avg_rating', 'products_image', 'total_item', 'product_ratings', 'store_setting', 'product_variant_names', 'page_slug_urls', 'blog', 'all_products'));
    }

    public function StoreCart($slug)
    {

        $store = Store::where('slug', $slug)->where('is_store_enabled','1')->first();
        if(empty($store))
        {
            return redirect()->route('store.slug', $slug);
        }
        $page_slug_urls = PageOption::where('store_id', $store->id)->get();
        $blog           = Blog::where('store_id', $store->id)->first();

        $cart = session()->get($slug);


                if(!empty($cart))
                        {
                            $products = $cart;

                        }
                        else
                        {
                            $products = '';
                        }
                        $total_item = 0;
                        if(isset($cart['products']))
                        {
                            if(isset($cart) && !empty($cart['products']))
                            {
                                $total_item = count($cart['products']);
                            }
                            else
                            {
                                $total_item = 0;
                            }
                        }
                        if(isset($cart['wishlist']))
                        {
                            $wishlist = $cart['wishlist'];
                        }
                        else
                        {
                            $wishlist = [];
                        }

                        return view('storefront.' . $store->theme_dir . '.cart', compact('wishlist', 'products', 'total_item', 'store', 'page_slug_urls', 'blog'));

    }

    public function userAddress($slug)
    {
         $store = Store::where('slug', $slug)->where('is_store_enabled','1')->first();
        if(empty($store))
        {
            return redirect()->route('store.slug', $slug);
        }
        $locations = Location::where('store_id', $store->id)->get()->pluck('name', 'id');
        $locations->prepend('Select Location', 0);
        $shippings      = Shipping::where('store_id', $store->id)->get();
        $page_slug_urls = PageOption::where('store_id', $store->id)->get();
        $blog           = Blog::where('store_id', $store->id)->first();


        $cart = session()->get($slug);
        if(!empty($cart))
        {
            $products = $cart['products'];
        }
        else
        {
            return redirect()->back()->with('error', __('Please add to product into cart'));
        }
        if(!empty($cart['customer']))
        {
            $cust_details = $cart['customer'];
        }
        else
        {
            $cust_details = '';
        }
        $tax_name  = [];
        $tax_price = [];
        $i         = 0;

        if(!empty($products))
        {
            if(!empty(Auth::guard('customers')->user())){
                foreach($products as $product)
                {
                    if($product['variant_id'] != 0)
                    {
                        foreach($product['tax'] as $key => $taxs)
                        {
                            if(!in_array($taxs['tax_name'], $tax_name))
                            {
                                $tax_name[]  = $taxs['tax_name'];
                                $price       = $product['variant_price'] * $product['quantity'] * $taxs['tax'] / 100;
                                $tax_price[] = $price;
                            }
                            else
                            {
                                $price                                                 = $product['variant_price'] * $product['quantity'] * $taxs['tax'] / 100;
                                $tax_price[array_search($taxs['tax_name'], $tax_name)] += $price;
                            }
                        }
                    }
                    else
                    {
                        foreach($product['tax'] as $key => $taxs)
                        {
                            if(!in_array($taxs['tax_name'], $tax_name))
                            {
                                $tax_name[]  = $taxs['tax_name'];
                                $price       = $product['price'] * $product['quantity'] * $taxs['tax'] / 100;
                                $tax_price[] = $price;
                            }
                            else
                            {
                                $price                                                 = $product['price'] * $product['quantity'] * $taxs['tax'] / 100;
                                $tax_price[array_search($taxs['tax_name'], $tax_name)] += $price;
                            }
                        }
                    }
                    $i++;
                }
                $total_item            = $i;
                $taxArr['tax']         = $tax_name;
                $taxArr['rate']        = $tax_price;
                $store_payment_setting = Utility::getPaymentSetting($store->id);

                return view('storefront.' . $store->theme_dir . '.shipping', compact('store_payment_setting', 'products', 'store', 'taxArr', 'total_item', 'cust_details', 'locations', 'shippings', 'page_slug_urls', 'blog'));
            }
            else{
                $is_cart = true;

                return view('storefront.' . $store->theme_dir . '.user.login', compact('blog','slug', 'store', 'page_slug_urls', 'is_cart'));
            }

        }
        else
        {

            return redirect()->back()->with('error', __('Please add to product into cart.'));
        }
    }

    public function UserLocation($slug, $location_id)
    {
         $store = Store::where('slug', $slug)->where('is_store_enabled','1')->first();
        if(empty($store))
        {
            return redirect()->route('store.slug', $slug);
        }
        $shippings = Shipping::where('store_id', $store->id)->whereRaw('FIND_IN_SET("' . $location_id . '", location_id)')->get()->toArray();

        return response()->json(
            [
                'code' => 200,
                'status' => 'Success',
                'shipping' => $shippings,
            ]
        );
    }

    public function UserShipping(Request $request, $slug, $shipping_id)
    {
         $store = Store::where('slug', $slug)->where('is_store_enabled','1')->first();
        if(empty($store))
        {
            return redirect()->route('store.slug', $slug);
        }
        $shippings       = Shipping::where('store_id', $store->id)->where('id', $shipping_id)->first();
        $shipping_price  = Utility::priceFormat($shippings->price);
        $pro_total_price = str_replace(' ', '', str_replace(',', '', str_replace($store->currency, '', $request->pro_total_price)));
        $total_price     = $shippings->price + $pro_total_price;
        if(!empty($request->coupon))
        {
            $coupons = ProductCoupon::where('code', strtoupper($request->coupon))->first();
            if(!empty($coupons))
            {
                if($coupons->enable_flat == 'on')
                {
                    $discount_value = $coupons->flat_discount;
                }
                else
                {
                    $discount_value = ($pro_total_price / 100) * $coupons->discount;
                }
            }
            else
            {
                $discount_value = 0;
            }
            $total_price = $total_price - $discount_value;
        }

        return response()->json(
            [
                'code' => 200,
                'status' => 'Success',
                'price' => $shipping_price,
                'total_price' => Utility::priceFormat($total_price),
            ]
        );
    }

    public function userPayment($slug)
    {
         $store = Store::where('slug', $slug)->where('is_store_enabled','1')->first();
        if(empty($store))
        {
            return redirect()->route('store.slug', $slug);
        }
        $order = Order::where('user_id', $store->id)->orderBy('id', 'desc')->first();
        $blog  = Blog::where('store_id', $store->id)->count();

        if(\Auth::check())
        {
            $store_payments = Utility::getPaymentSetting();
        }
        else
        {
            $store_payments = Utility::getPaymentSetting($store->id);
        }

        $page_slug_urls = PageOption::where('store_id', $store->id)->get();


        $cart = session()->get($slug);
        if(!empty($cart))
        {
            $products = $cart['products'];
        }
        else
        {
            return redirect()->back()->with('error', __('Please add to product into cart'));
        }

        if(!empty($cart['customer']))
        {
            $cust_details = $cart['customer'];
        }
        else
        {
            return redirect()->back()->with('error', __('Please add your information'));
        }
        $shippings = Shipping::where('store_id', $store->id)->get();

        if($store->enable_shipping == 'on' && count($shippings) > 0)
        {
            if(!empty($cart['shipping']))
            {
                $shipping         = $cart['shipping'];
                $shipping_details = Shipping::where('store_id', $store->id)->where('id', $shipping['shipping_id'])->first();
                $shipping_price   = floor($shipping_details->price);
            }
            else
            {
                return redirect()->back()->with('error', __('Please Select Shipping Location'));
            }
        }
        else
        {
            $shipping_price = 0;
        }

        if(!empty($cart['coupon']))
        {
            $discount_price = $cart['coupon']['discount_price'];
            $coupon_price   = str_replace('-' . $store->currency, '', $cart['coupon']['discount_price']);
            $coupon_id      = $cart['coupon']['data_id'];
        }
        else
        {
            $discount_price = Utility::priceFormat(0);
            $coupon_price   = 0;
            $coupon_id      = 0;

        }

        $store     = Store::where('slug', $slug)->first();
        $tax_name  = [];
        $tax_price = [];
        $i         = 0;
        if(!empty($products))
        {
            if(!empty($cust_details))
            {
                foreach($products as $product)
                {
                    if($product['variant_id'] != 0)
                    {
                        foreach($product['tax'] as $key => $taxs)
                        {

                            if(!in_array($taxs['tax_name'], $tax_name))
                            {
                                $tax_name[]  = $taxs['tax_name'];
                                $price       = $product['variant_price'] * $product['quantity'] * $taxs['tax'] / 100;
                                $tax_price[] = $price;
                            }
                            else
                            {
                                $price                                                 = $product['variant_price'] * $product['quantity'] * $taxs['tax'] / 100;
                                $tax_price[array_search($taxs['tax_name'], $tax_name)] += $price;
                            }
                        }
                    }
                    else
                    {

                        foreach($product['tax'] as $key => $taxs)
                        {
                            if(!in_array($taxs['tax_name'], $tax_name))
                            {
                                $tax_name[]  = $taxs['tax_name'];
                                $price       = $product['price'] * $product['quantity'] * $taxs['tax'] / 100;
                                $tax_price[] = $price;
                            }
                            else
                            {
                                $price                                                 = $product['price'] * $product['quantity'] * $taxs['tax'] / 100;
                                $tax_price[array_search($taxs['tax_name'], $tax_name)] += $price;
                            }
                        }
                    }
                    $i++;
                }
                $encode_product = json_encode($products);
                $total_item     = $i;
                $taxArr['tax']  = $tax_name;
                $taxArr['rate'] = $tax_price;

                return view('storefront.' . $store->theme_dir . '.payment', compact('coupon_id', 'discount_price', 'coupon_price', 'products', 'order', 'cust_details', 'store', 'taxArr', 'total_item', 'encode_product', 'shipping_price', 'page_slug_urls', 'store_payments', 'blog', 'cart'));
            }
            else
            {
                return redirect()->back()->with('error', __('Please fill your details.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Please add to product into cart.'));
        }
    }

   public function getWhatsappUrl(Request $request, $slug)
    {
        $store = Store::where('slug', $slug)->first();
        $cart  = session()->get($slug);

        if(!empty($cart))
        {
            $products = $cart['products'];
        }
        else
        {
            return response()->json(
                [
                    'status' => 'error',
                    'msg' => __('Please add to product into cart'),
                ]
            );
        }
        $shipping_price = 0;
        if($store->enable_shipping == 'on')
        {
            if(!empty($cart['shipping']))
            {
                $shipping         = $cart['shipping'];
                $shipping_details = Shipping::where('store_id', $store->id)->where('id', $shipping['shipping_id'])->first();
                if(!empty($shipping_details->price))
                {
                    $shipping_price = $shipping_details->price;
                }
            }
        }

        // For Url
        $pro_qty  = [];
        $pro_name = [];
        $order_id = '#' . time();

        $lists     = [];
        $total_tax = 0;
        foreach($products as $item)
        {
            $pro_data = Product::where('id', $item['id'])->first();

            if($item['variant_id'] == 0)
            {
                $pro_qty[] = $item['quantity'] . ' x ' . $item['product_name'];
                $total_tax = 0;
                foreach($item['tax'] as $tax)
                {
                    $sub_tax   = ($item['price'] * $item['quantity'] * $tax['tax']) / 100;
                    $total_tax += $sub_tax;
                }
                $lists[] = array(
                    'sku' => $pro_data->SKU,
                    'quantity' => $item['quantity'],
                    'product_name' => $item['product_name'],
                    'item_tax' => $total_tax,
                    'item_total' => $item['price'] * $item['quantity'],
                );
            }
            elseif($item['variant_id'] != 0)
            {
                $pro_data  = Product::where('id', $item['id'])->first();
                $pro_qty[] = $item['quantity'] . ' x ' . $item['product_name'] . ' - ' . $item['variant_name'];
                foreach($item['tax'] as $tax)
                {
                    $sub_tax   = ($item['variant_price'] * $item['quantity'] * $tax['tax']) / 100;
                    $total_tax += $sub_tax;
                }

                $lists[] = [
                    'sku' => $pro_data->SKU,
                    'quantity' => $item['quantity'],
                    'product_name' => $item['product_name'],
                    'variant_name' => $item['variant_name'],
                    'item_tax' => $total_tax,
                    'item_total' => $item['variant_price'] * $item['quantity'],
                ];
            }
        }

        $item_variable = '';
        $qty_total     = 0;
        $sub_total     = 0;
        $total_tax     = 0;

        foreach($lists as $l)
        {
            $arrList = [
                'sku' => $l['sku'],
                'quantity' => $l['quantity'],
                'product_name' => $l['product_name'],
                'item_tax' => $l['item_tax'],
                'item_total' => Utility::priceFormat($l['item_total']),
            ];

            if(isset($l['variant_name']) && !empty($l['variant_name']))
            {
                $arrList['variant_name'] = $l['variant_name'];
            }

            $resp = Utility::replaceVariable($store->item_variable, $arrList);
            $resp = str_replace('-  ', '', $resp);;
            $item_variable .= $resp . PHP_EOL;

            $qty_total = $qty_total + $l['quantity'];
            $sub_total += $l['item_total'] * $l['quantity'];
            $total_tax += $l['item_tax'];
        }

        $total_price = Utility::priceFormat(floatval($sub_total) + (int)$shipping_price + floatval($total_tax));

        $arr = [
            'store_name' => $store->name,
            'order_no' => $order_id,
            'customer_name' => $cart['customer']['name'],
            'billing_address' => $cart['customer']['billing_address'],
            'billing_country' => $cart['customer']['billing_country'],
            'billing_city' => $cart['customer']['billing_city'],
            'billing_postalcode' => $cart['customer']['billing_postalcode'],
            'shipping_address' => !empty($cart['customer']['shipping_address']) ? $cart['customer']['shipping_address'] : '-',
            'shipping_country' => !empty($cart['customer']['shipping_country']) ? $cart['customer']['shipping_country'] : '-',
            'shipping_city' => !empty($cart['customer']['shipping_city']) ? $cart['customer']['shipping_city'] : '-',
            'shipping_postalcode' => !empty($cart['customer']['shipping_postalcode']) ? $cart['customer']['shipping_postalcode'] : '-',
            'item_variable' => $item_variable,
            'qty_total' => $qty_total,
            'sub_total' => Utility::priceFormat($sub_total),
            'shipping_amount' => Utility::priceFormat(!empty($shipping_price) ? $shipping_price : '0'),
            'total_tax' => Utility::priceFormat($total_tax),
            'final_total' => $total_price,
        ];

        if(isset($request->coupon_id) && !empty($request->coupon_id))
        {
            $arr['discount_amount'] = !empty($request->dicount_price) ? $request->dicount_price : '0';
        }

        if(isset($request->finalprice) && !empty($request->finalprice))
        {
            $arr['final_total'] = $request->finalprice;
        }

        $resp = Utility::replaceVariable($store->content, $arr);

        if($request['data']['type'] == 'telegram')
        {
            $msg = $resp;

            // Set your Bot ID and Chat ID.
            $telegrambot    = $store->telegrambot;
            $telegramchatid = $store->telegramchatid;

            // Function call with your own text or variable
            $url     = 'https://api.telegram.org/bot' . $telegrambot . '/sendMessage';
            $data    = array(
                'chat_id' => $telegramchatid,
                'text' => $msg,
            );
            $options = array(
                'http' => array(
                    'method' => 'POST',
                    'header' => "Content-Type:application/x-www-form-urlencoded\r\n",
                    'content' => http_build_query($data),
                ),
            );
            $context = stream_context_create($options);
            $result  = file_get_contents($url, false, $context);
            $url     = $url;
        }
        else
        {
            $url = 'https://api.whatsapp.com/send?phone=' . $store->whatsapp_number . '&text=' . urlencode($resp);
        }

        $new_order_id = str_replace('#', '', $request->order_id);

        return response()->json(
            [
                'status' => 'success',
                'order_id' => Crypt::encrypt($new_order_id),
                'url' => $url,
            ]
        );
    }

    public function addToCart(Request $request, $product_id, $slug, $variant_id = 0)
    {
        if($request->ajax())
        {
            $store   = Store::where('slug', $slug)->where('is_store_enabled','1')->get();
            if(empty($store))
            {
                return response()->json(
                    [
                        'code' => 404,
                        'status' => 'Error',
                        'error' => __('Page not found'),
                    ]
                );
            }
            $variant = ProductVariantOption::find($variant_id);



            $product = Product::find($product_id);
            $cart    = session()->get($slug);

            $quantity = $product->quantity;
            if($variant_id > 0)
            {
                $quantity = $variant->quantity;
            }

            if(!empty($product->is_cover))
            {
                $pro_img = $product->is_cover;
            }
            else
            {
                $pro_img = 'default.jpg';
            }

            $productquantity = $product->quantity;
            $i               = 0;

            //            if(!$product && $quantity == 0)
            if($quantity == 0)
            {
                return response()->json(
                    [
                        'code' => 404,
                        'status' => 'Error',
                        'error' => __('This product is out of stock!'),
                    ]
                );
            }

            $productname      = $product->name;
            $productprice     = $product->price != 0 ? $product->price : 0;
            $originalquantity = (int)$productquantity;

            //product count tax
            $taxes      = Utility::tax($product->product_tax);
            $itemTaxes  = [];
            $producttax = 0;

            if(!empty($taxes))
            {
                foreach($taxes as $tax)
                {
                    if(!empty($tax))
                    {
                        $producttax          = Utility::taxRate($tax->rate, $product->price, 1);
                        $itemTax['tax_name'] = $tax->name;
                        $itemTax['tax']      = $tax->rate;
                        $itemTaxes[]         = $itemTax;
                    }
                }
            }

            $subtotal = Utility::priceFormat($productprice + $producttax);

            if($variant_id > 0)
            {
                $variant_itemTaxes       = [];
                $variant_name            = $variant->name;
                $variant_price           = $variant->price;
                $originalvariantquantity = (int)$variant->quantity;
                //variant count tax
                $variant_taxes      = Utility::tax($product->product_tax);
                $variant_producttax = 0;

                if(!empty($variant_taxes))
                {
                    foreach($variant_taxes as $variant_tax)
                    {
                        if(!empty($variant_tax))
                        {
                            $variant_producttax  = Utility::taxRate($variant_tax->rate, $variant_price, 1);
                            $itemTax['tax_name'] = $variant_tax->name;
                            $itemTax['tax']      = $variant_tax->rate;
                            $variant_itemTaxes[] = $itemTax;
                        }
                    }
                }
                $variant_subtotal = Utility::priceFormat($variant_price * $variant->quantity);
            }

            $time = time();
            // if cart is empty then this the first product
            if(!$cart || !isset($cart['products']))
            {
                if($variant_id > 0)
                {
                    $cart['products'][$time] = [
                        "product_id" => $product->id,
                        "product_name" => $productname,
                        "image" => Storage::url('uploads/is_cover_image/' . $pro_img),
                        "quantity" => 1,
                        "price" => $productprice,
                        "id" => $product_id,
                        "downloadable_prodcut" => $product->downloadable_prodcut,
                        "tax" => $variant_itemTaxes,
                        "subtotal" => $subtotal,
                        "originalquantity" => $originalquantity,
                        "variant_name" => $variant_name,
                        "variant_price" => $variant_price,
                        "variant_qty" => $variant->quantity,
                        "variant_subtotal" => $variant_subtotal,
                        "originalvariantquantity" => $originalvariantquantity,
                        'variant_id' => $variant_id,
                    ];
                }
                else if($variant_id <= 0)
                {
                    $cart['products'][$time] = [
                        "product_id" => $product->id,
                        "product_name" => $productname,
                        "image" => Storage::url('uploads/is_cover_image/' . $pro_img),
                        "quantity" => 1,
                        "price" => $productprice,
                        "id" => $product_id,
                        "downloadable_prodcut" => $product->downloadable_prodcut,
                        "tax" => $itemTaxes,
                        "subtotal" => $subtotal,
                        "originalquantity" => $originalquantity,
                        'variant_id' => 0,
                    ];
                }

                session()->put($slug, $cart);

                return response()->json(
                    [
                        'code' => 200,
                        'status' => 'Success',
                        'success' => $productname . __('added to cart successfully!'),
                        'cart' => $cart['products'],
                        'item_count' => count($cart['products']),
                    ]
                );
            }

            // if cart not empty then check if this product exist then increment quantity
            if($variant_id > 0)
            {
                $key = false;
                foreach($cart['products'] as $k => $value)
                {
                    if($variant_id == $value['variant_id'])
                    {
                        $key = $k;
                    }
                }

                if($key !== false && isset($cart['products'][$key]['variant_id']) && $cart['products'][$key]['variant_id'] != 0)
                {
                    if(isset($cart['products'][$key]))
                    {
                        $cart['products'][$key]['quantity']         = $cart['products'][$key]['quantity'] + 1;
                        $cart['products'][$key]['variant_subtotal'] = $cart['products'][$key]['variant_price'] * $cart['products'][$key]['quantity'];

                        if($originalvariantquantity < $cart['products'][$key]['quantity'])
                        {
                            return response()->json(
                                [
                                    'code' => 404,
                                    'status' => 'Error',
                                    'error' => __('This product is out of stock!'),
                                ]
                            );
                        }

                        session()->put($slug, $cart);

                        return response()->json(
                            [
                                'code' => 200,
                                'status' => 'Success',
                                'success' => $productname . __('added to cart successfully!'),
                                'cart' => $cart['products'],
                                'item_count' => count($cart['products']),
                            ]
                        );
                    }
                }
            }
            else if($variant_id <= 0)
            {
                $key = false;

                foreach($cart['products'] as $k => $value)
                {
                    if($product_id == $value['product_id'])
                    {
                        $key = $k;
                    }
                }

                if($key !== false)
                {
                    if(isset($cart['products'][$key]))
                    {
                        $cart['products'][$key]['quantity'] = $cart['products'][$key]['quantity'] + 1;
                        $cart['products'][$key]['subtotal'] = $cart['products'][$key]['price'] * $cart['products'][$key]['quantity'];
                        if($originalquantity < $cart['products'][$key]['quantity'])
                        {
                            return response()->json(
                                [
                                    'code' => 404,
                                    'status' => 'Error',
                                    'error' => __('This product is out of stock!'),
                                ]
                            );
                        }

                        session()->put($slug, $cart);

                        return response()->json(
                            [
                                'code' => 200,
                                'status' => 'Success',
                                'success' => $productname . __('added to cart successfully!'),
                                'cart' => $cart['products'],
                                'item_count' => count($cart['products']),
                            ]
                        );
                    }
                }
            }

            // if item not exist in cart then add to cart with quantity = 1
            if($variant_id > 0)
            {
                $cart['products'][$time] = [
                    "product_id" => $product->id,
                    "product_name" => $productname,
                    "image" => Storage::url('uploads/is_cover_image/' . $pro_img),
                    "quantity" => 1,
                    "price" => $productprice,
                    "id" => $product_id,
                    "downloadable_prodcut" => $product->downloadable_prodcut,
                    "tax" => $variant_itemTaxes,
                    "subtotal" => $subtotal,
                    "originalquantity" => $originalquantity,
                    "variant_name" => $variant->name,
                    "variant_price" => $variant->price,
                    "variant_qty" => $variant->quantity,
                    "variant_subtotal" => $variant_subtotal,
                    "originalvariantquantity" => $originalvariantquantity,
                    'variant_id' => $variant_id,
                ];
            }
            else if($variant_id <= 0)
            {
                $cart['products'][$time] = [
                    "product_id" => $product->id,
                    "product_name" => $productname,
                    "image" => Storage::url('uploads/is_cover_image/' . $pro_img),
                    "quantity" => 1,
                    "price" => $productprice,
                    "id" => $product_id,
                    "downloadable_prodcut" => $product->downloadable_prodcut,
                    "tax" => $itemTaxes,
                    "subtotal" => $subtotal,
                    "originalquantity" => $originalquantity,
                    'variant_id' => 0,
                ];
            }

            session()->put($slug, $cart);

            return response()->json(
                [
                    'code' => 200,
                    'status' => 'Success',
                    'success' => $productname . __('added to cart successfully!'),
                    'cart' => $cart['products'],
                    'item_count' => count($cart['products']),
                ]
            );
        }
    }

    public function productqty(Request $request, $product_id, $slug, $key = 0)
    {
        $cart = session()->get($slug);
        if($cart['products'][$key]['variant_id'] > 0 && $cart['products'][$key]['originalvariantquantity'] < $request->product_qty)
        {
            return response()->json(
                [
                    'code' => 404,
                    'status' => 'Error',
                    'error' => __('You can only purchese max') . ' ' . $cart['products'][$key]['originalvariantquantity'] . ' ' . __('product!'),
                ]
            );
        }
        else if($cart['products'][$key]['originalquantity'] < $request->product_qty && $cart['products'][$key]['variant_id'] == 0)
        {
            return response()->json(
                [
                    'code' => 404,
                    'status' => 'Error',
                    'error' => __('You can only purchese max') . ' ' . $cart['products'][$key]['originalquantity'] . ' ' . __('product!'),
                ]
            );
        }
        if(isset($cart['products'][$key]))
        {
            $cart['products'][$key]['quantity'] = $request->product_qty;
            $cart['products'][$key]['id']       = $product_id;

            $subtotal = $cart['products'][$key]["price"] * $cart['products'][$key]["quantity"];

            $protax = $cart['products'][$key]["tax"];
            if($protax != 0)
            {
                $taxs = 0;
                foreach($protax as $tax)
                {
                    $taxs += ($subtotal * $tax['tax']) / 100;
                }
            }
            else
            {
                $taxs = 0;
                $taxs += ($subtotal * 0) / 100;

            }
            $cart['products'][$key]["subtotal"] = $subtotal + $taxs;

            session()->put($slug, $cart);

            return response()->json(
                [
                    'code' => 200,
                    'status' => 'Success',
                    'success' => $cart['products'][$key]["product_name"] . __('added to cart successfully!'),
                    'product' => $cart['products'],
                    'carttotal' => $cart['products'],
                ]
            );

        }
    }

    public function delete_cart_item($slug, $id, $variant_id = 0)
    {
        $cart = session()->get($slug);

        foreach($cart['products'] as $key => $product)
        {
            if(($variant_id > 0 && $cart['products'][$key]['variant_id'] == $variant_id))
            {
                unset($cart['products'][$key]);
            }
            else if($cart['products'][$key]['product_id'] == $id && $variant_id == 0)
            {
                unset($cart['products'][$key]);
            }

        }

        $cart['products'] = array_values($cart['products']);

        session()->put($slug, $cart);

        return redirect()->back()->with('success', __('Item successfully Deleted.'));
    }

    public function customer(Request $request, $slug)
    {
        $store = Store::where('slug', $slug)->where('is_store_enabled','1')->first();

        if(empty($store))
        {
            return redirect()->back()->with('error', __('Store not available'));
        }

        $cart = session()->get($slug);

        $products = $cart['products'];

        $validator = \Validator::make(
            $request->all(), [
                               'name' => 'required|max:120',
                               'last_name' => 'required|max:120',
                           ]
        );
        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return redirect()->back()->withInput()->with('error', $messages->first());
        }
        $shippings = Shipping::where('store_id', $store->id)->where('id', $request->shipping_id)->get();

        if($store->enable_shipping == "on" && count($shippings) > 0)
        {
            if($request->location_id == 0 && empty($request->location_id))
            {
                return redirect()->back()->withInput()->with('error', __('Please Select Location'));
            }
            if(empty($request->shipping_id))
            {
                return redirect()->back()->withInput()->with('error', __('Please Select Shipping Method'));
            }
        }
        else
        {
            $request->location_id = 0;
            $request->shipping_id = 0;
        }

        if($request->location_id != 0 && !empty($request->location_id) && !empty($request->shipping_id))
        {
            $cart['shipping'] = [
                'location_id' => $request->location_id,
                'shipping_id' => $request->shipping_id,
            ];
        }

        if(!empty($cart['customer']['id']))
        {
            $userdetail = UserDetail::where('id', $cart['customer']['id'])->where('store_id', $store->id)->first();
        }
        else
        {
            $userdetail = new UserDetail();
        }

        $userdetail['store_id']  = $store->id;
        $userdetail['name']      = $request->name;
        $userdetail['last_name'] = $request->last_name;
        $userdetail['email']     = $request->email;
        $userdetail['phone']     = $request->phone;

        $userdetail['custom_field_title_1'] = $request->custom_field_title_1;
        $userdetail['custom_field_title_2'] = $request->custom_field_title_2;
        $userdetail['custom_field_title_3'] = $request->custom_field_title_3;
        $userdetail['custom_field_title_4'] = $request->custom_field_title_4;


        $userdetail['billing_address']     = $request->billing_address;
        $userdetail['billing_country']     = $request->billing_country;
        $userdetail['billing_city']        = $request->billing_city;
        $userdetail['billing_postalcode']  = $request->billing_postalcode;
        $userdetail['shipping_address']    = $request->shipping_address;
        $userdetail['shipping_country']    = $request->shipping_country;
        $userdetail['shipping_city']       = $request->shipping_city;
        $userdetail['shipping_postalcode'] = $request->shipping_postalcode;
        $userdetail['location_id']         = $request->location_id;
        $userdetail['shipping_id']         = $request->shipping_id;
        $userdetail->save();
        $userdetail->id;


        $cart['customer'] = [
            "id" => $userdetail->id,
            "name" => $request->name,
            "last_name" => $request->last_name,
            "phone" => $request->phone,
            "email" => $request->email,

            "custom_field_title_1" => $request->custom_field_title_1,
            "custom_field_title_2" => $request->custom_field_title_2,
            "custom_field_title_3" => $request->custom_field_title_3,
            "custom_field_title_4" => $request->custom_field_title_4,

            "billing_address" => $request->billing_address,
            "billing_country" => $request->billing_country,
            "billing_city" => $request->billing_city,
            "billing_postalcode" => $request->billing_postalcode,
            "shipping_address" => $request->shipping_address,
            "shipping_country" => $request->shipping_country,
            "shipping_city" => $request->shipping_city,
            "shipping_postalcode" => $request->shipping_postalcode,
            "location_id" => $request->location_id,
            "shipping_id" => $request->shipping_id,
        ];

        session()->put($slug, $cart);

        return redirect()->route('store-payment.payment', $slug);

    }

    public function complete($slug, $order_id)
    {
        $order = Order::where('id', Crypt::decrypt($order_id))->first();
        $store = Store::where('slug', $slug)->where('is_store_enabled','1')->first();
         if(empty($store))
        {
            return redirect()->back()->with('error', __('Store not available'));
        }
        return view('storefront.complete', compact('slug', 'store', 'order_id', 'order'));
    }

      public function customerorder($slug, $order_id)
    {

        $id    = Crypt::decrypt($order_id);
        $store = Store::where('slug', $slug)->where('is_store_enabled','1')->first();
         if(empty($store))
        {
            return redirect()->back()->with('error', __('Store not available'));
        }
        $order = Order::where('id', $id)->first();
        $storethemesetting = \App\Models\Utility::demoStoreThemeSetting($store->id, $store->theme_dir);
        if(!empty($order->coupon_json))
        {
            $coupon = json_decode($order->coupon_json);
        }
        if(!empty($order->discount_price))
        {
            $discount_price = $order->discount_price;
        }
        else
        {
            $discount_price = '';
        }

        if(!empty($order->shipping_data))
        {
            $shipping_data = json_decode($order->shipping_data);
            $location_data = Location::where('id', $shipping_data->location_id)->first();
        }
        else
        {
            $shipping_data = '';
            $location_data = '';
        }

        $user_details   = UserDetail::where('id', $order->user_address_id)->first();
        $order_products = json_decode($order->product);

        $sub_total = 0;

        if(!empty($order_products))
        {
            $grand_total    = 0;
            $discount_value = 0;
            $final_taxs     = 0;
            $total_taxs     = 0;
            foreach($order_products as $product)
            {
                if(isset($product->variant_id) && $product->variant_id != 0)
                {
                    $total_taxs = 0;
                    if(!empty($product->tax))
                    {
                        foreach($product->tax as $tax)
                        {
                            $sub_tax    = ($product->variant_price * $product->quantity * $tax->tax) / 100;
                            $total_taxs += $sub_tax;
                            $final_taxs += $sub_tax;
                        }
                    }
                    else
                    {
                        $total_taxs = 0;
                    }

                    $totalprice = $product->variant_price * $product->quantity + $total_taxs;
                    $subtotal1  = $product->variant_price * $product->quantity;

                    $sub_total   += $subtotal1;
                    $grand_total += $totalprice;
                }
                else
                {
                    if(!empty($product->tax))
                    {
                        $total_taxs = 0;
                        foreach($product->tax as $tax)
                        {
                            $sub_tax    = ($product->price * $product->quantity * $tax->tax) / 100;
                            $total_taxs += $sub_tax;
                            $final_taxs += $sub_tax;

                        }
                    }
                    else
                    {
                        $total_taxs = 0;
                    }

                    $totalprice  = $product->price * $product->quantity + $final_taxs;
                    $subtotal1   = $product->price * $product->quantity;
                    $sub_total   += $subtotal1;
                    $grand_total += $totalprice;
                }

            }
        }

        if(!empty($coupon))
        {
            if($coupon->enable_flat == 'on')
            {
                $discount_value = $coupon->flat_discount;
            }
            else
            {
                $discount_value = ($grand_total / 100) * $coupon->discount;
            }
        }

        $store_payment_setting = Utility::getPaymentSetting($store->id);

        // custuserorder.blade.php
        return view('storefront.' . $store->theme_dir . '.customer.custuserorder', compact('slug','storethemesetting','store_payment_setting', 'store', 'order', 'grand_total', 'order_products', 'sub_total', 'total_taxs', 'user_details', 'shipping_data', 'location_data', 'discount_price', 'discount_value', 'final_taxs'));
    }

    public function userorder($slug, $order_id)
    {

        $id    = Crypt::decrypt($order_id);
        $store = Store::where('slug', $slug)->where('is_store_enabled','1')->first();
         if(empty($store))
        {
            return redirect()->back()->with('error', __('Store not available'));
        }

        if(isset($store->lang))
        {

            $lang = session()->get('lang');

            if(!isset($lang))
            {
             session(['lang' => $store->lang]);
             $storelang=session()->get('lang');
             \App::setLocale(isset($storelang) ? $storelang : 'en');
            }
            else
            {
                session(['lang' => $lang]);
                $storelang=session()->get('lang');
                \App::setLocale(isset($storelang) ? $storelang : 'en');
            }
        }
        $order = Order::where('id', $id)->first();

        if(!empty($order->coupon_json))
        {
            $coupon = json_decode($order->coupon_json);
        }
        if(!empty($order->discount_price))
        {
            $discount_price = $order->discount_price;
        }
        else
        {
            $discount_price = '';
        }

        if(!empty($order->shipping_data))
        {
            $shipping_data = json_decode($order->shipping_data);
            $location_data = Location::where('id', $shipping_data->location_id)->first();
        }
        else
        {
            $shipping_data = '';
            $location_data = '';
        }

        $user_details   = UserDetail::where('id', $order->user_address_id)->first();
        $order_products = json_decode($order->product);

        $sub_total = 0;

        if(!empty($order_products))
        {
            $grand_total    = 0;
            $discount_value = 0;
            $final_taxs     = 0;
            $total_taxs     = 0;
            foreach($order_products as $product)
            {
                if(isset($product->variant_id) && $product->variant_id != 0)
                {
                    $total_taxs = 0;
                    if(!empty($product->tax))
                    {
                        foreach($product->tax as $tax)
                        {
                            $sub_tax    = ($product->variant_price * $product->quantity * $tax->tax) / 100;
                            $total_taxs += $sub_tax;
                            $final_taxs += $sub_tax;
                        }
                    }
                    else
                    {
                        $total_taxs = 0;
                    }

                    $totalprice = $product->variant_price * $product->quantity + $total_taxs;
                    $subtotal1  = $product->variant_price * $product->quantity;

                    $sub_total   += $subtotal1;
                    $grand_total += $totalprice;
                }
                else
                {
                    if(!empty($product->tax))
                    {
                        $total_taxs = 0;
                        foreach($product->tax as $tax)
                        {
                            $sub_tax    = ($product->price * $product->quantity * $tax->tax) / 100;
                            $total_taxs += $sub_tax;
                            $final_taxs += $sub_tax;

                        }
                    }
                    else
                    {
                        $total_taxs = 0;
                    }

                    $totalprice  = $product->price * $product->quantity + $final_taxs;
                    $subtotal1   = $product->price * $product->quantity;
                    $sub_total   += $subtotal1;
                    $grand_total += $totalprice;
                }

            }
        }

        if(!empty($coupon))
        {
            if($coupon->enable_flat == 'on')
            {
                $discount_value = $coupon->flat_discount;
            }
            else
            {
                $discount_value = ($grand_total / 100) * $coupon->discount;
            }
        }

        $store_payment_setting = Utility::getPaymentSetting($store->id);

        return view('storefront.userorder', compact('slug', 'store_payment_setting', 'store', 'order', 'grand_total', 'order_products', 'sub_total', 'total_taxs', 'user_details', 'shipping_data', 'location_data', 'discount_price', 'discount_value', 'final_taxs'));
    }

      public function whatsapp(Request $request, $slug)
    {
        $store = Store::where('slug', $slug)->first();

        $shipping = Shipping::where('store_id', $store->id)->first();
        if(!empty($shipping) && $store->enable_shipping == 'on')
        {
            if($request->shipping_price == '0.00')
            {
                return response()->json(
                    [
                        'status' => 'error',
                        'success' => __('Please select shipping.'),
                    ]
                );
            }
        }
        if(empty($store))
        {
            return response()->json(
                [
                    'status' => 'error',
                    'success' => __('Store not available.'),
                ]
            );
        }
        $validator = \Validator::make(
            $request->all(), [
                               'wts_number' => 'required',
                           ]
        );
        if($validator->fails())
        {
            return response()->json(
                [
                    'status' => 'error',
                    'success' => __('The Phone number field is required.'),
                ]
            );
        }
        $products     = $request['product'];
        $order_id     = $request['order_id'];
        $cart         = session()->get($slug);
        $cust_details = $cart['customer'];
        if(!empty($request->coupon_id))
        {
            $coupon = ProductCoupon::where('id', $request->coupon_id)->first();
        }
        else
        {
            $coupon = '';
        }

        $product_name = [];
        $product_id   = [];
        $tax_name     = [];
        $totalprice   = 0;
        foreach($products as $key => $product)
        {
            if($product['variant_id'] == 0)
            {
                $new_qty                = $product['originalquantity'] - $product['quantity'];
                $product_edit           = Product::find($product['product_id']);
                $product_edit->quantity = $new_qty;
                $product_edit->save();

                $tax_price = 0;
                if(!empty($product['tax']))
                {
                    foreach($product['tax'] as $key => $taxs)
                    {
                        $tax_price += $product['price'] * $product['quantity'] * $taxs['tax'] / 100;

                    }
                }
                $totalprice     += $product['price'] * $product['quantity'] + $tax_price;
                $product_name[] = $product['product_name'];
                $product_id[]   = $product['id'];
            }
            elseif($product['variant_id'] != 0)
            {
                $new_qty                   = $product['originalvariantquantity'] - $product['quantity'];
                $product_variant           = ProductVariantOption::find($product['variant_id']);
                $product_variant->quantity = $new_qty;
                $product_variant->save();

                $tax_price = 0;
                if(!empty($product['tax']))
                {
                    foreach($product['tax'] as $key => $taxs)
                    {
                        $tax_price += $product['variant_price'] * $product['quantity'] * $taxs['tax'] / 100;

                    }
                }
                $totalprice     += $product['variant_price'] * $product['quantity'] + $tax_price;
                $product_name[] = $product['product_name'] . ' - ' . $product['variant_name'];
                $product_id[]   = $product['id'];
            }
        }

        if(isset($cart['shipping']) && isset($cart['shipping']['shipping_id']) && !empty($cart['shipping']))
        {
            $shipping = Shipping::find($cart['shipping']['shipping_id']);
            if(!empty($shipping))
            {
                $totalprice     = $totalprice + $shipping->price;
                $shipping_name  = $shipping->name;
                $shipping_price = $shipping->price;
                $shipping_data  = json_encode(
                    [
                        'shipping_name' => $shipping_name,
                        'shipping_price' => $shipping_price,
                        'location_id' => $cart['shipping']['location_id'],
                    ]
                );
            }
        }
        else
        {
            $shipping_data = '';
        }
        if($product)
        {
            $customer               = Auth::guard('customers')->user();
            $order                  = new Order();
            $order->order_id        = '#' . time();
            $order->name            = $cust_details['name'];
            $order->email           = $cust_details['email'];
            $order->card_number     = '';
            $order->card_exp_month  = '';
            $order->card_exp_year   = '';
            $order->status          = 'pending';
            $order->phone           = $request->wts_number;
            $order->user_address_id = $cust_details['id'];
            $order->shipping_data   = !empty($shipping_data) ? $shipping_data : '';
            $order->product_id      = implode(',', $product_id);
            $order->price           = $totalprice;
            $order->coupon          = $request->coupon_id;
            $order->coupon_json     = json_encode($coupon);
            $order->discount_price  = !empty($request->dicount_price) ? $request->dicount_price : '0';
            $order->coupon          = $request->coupon_id;
            $order->product         = json_encode($products);
            $order->price_currency  = $store->currency_code;
            $order->txn_id          = '';
            $order->payment_type    = __('Whatsapp');
            $order->payment_status  = 'approved';
            $order->receipt         = '';
            $order->user_id         = $store['id'];
            $order->customer_id     = $customer->id;
            $order->save();
            foreach($products as $product_id)
            {
                $purchased_product = new PurchasedProducts();
                $purchased_product->product_id  = $product_id['product_id'];
                $purchased_product->customer_id = $customer->id;
                $purchased_product->order_id   = $order->id;
                $purchased_product->save();
            }
            $msg = response()->json(
                [
                    'status' => 'success',
                    'success' => __('Your Order Successfully Added'),
                    'order_id' => Crypt::encrypt($order->id),
                ]
            );

            $order_email = $order->email;
             $owner=User::find($store->created_by);
            $owner_email=$owner->email;
            $order_id = Crypt::encrypt($order->id);

            if(isset($store->mail_driver) && !empty($store->mail_driver))
            {
                $dArr = [
                    'order_name' => $order->name,
                ];

                $resp = Utility::sendEmailTemplate('Order Created', $order_email, $dArr, $store, $order_id);

                $resp1=Utility::sendEmailTemplate('Order Created For Owner', $owner_email, $dArr, $store, $order_id);


            }
            if(isset($store->is_twilio_enabled) && $store->is_twilio_enabled=="on")
            {
                 Utility::order_create_owner($order,$owner,$store);
                 Utility::order_create_customer($order,$customer,$store);
            }

            return $msg;
        }
        else
        {
            return response()->json(
                [
                    'status' => 'error',
                    'success' => __('Failed'),
                ]
            );
        }
    }

    public function telegram(Request $request, $slug)
    {
        $store = Store::where('slug', $slug)->first();

        $shipping = Shipping::where('store_id', $store->id)->first();

        if(!empty($shipping))
        {
            if($request->shipping_price == '0.00')
            {
                return response()->json(
                    [
                        'status' => 'error',
                        'success' => __('Please select shipping.'),
                    ]
                );
            }
        }

        $products     = $request['product'];
        $order_id     = $request['order_id'];
        $cart         = session()->get($slug);
        $cust_details = $cart['customer'];

        if(!empty($request->coupon_id))
        {
            $coupon = ProductCoupon::where('id', $request->coupon_id)->first();
        }
        else
        {
            $coupon = '';
        }

        $product_name = [];
        $product_id   = [];
        $tax_name     = [];
        $totalprice   = 0;

        foreach($products as $key => $product)
        {
            if($product['variant_id'] == 0)
            {
                $new_qty                = $product['originalquantity'] - $product['quantity'];
                $product_edit           = Product::find($product['product_id']);
                $product_edit->quantity = $new_qty;
                $product_edit->save();

                $tax_price = 0;
                if(!empty($product['tax']))
                {
                    foreach($product['tax'] as $key => $taxs)
                    {
                        $tax_price += $product['price'] * $product['quantity'] * $taxs['tax'] / 100;

                    }
                }
                $totalprice     += $product['price'] * $product['quantity'] + $tax_price;
                $product_name[] = $product['product_name'];
                $product_id[]   = $product['id'];
            }
            elseif($product['variant_id'] != 0)
            {
                $new_qty                   = $product['originalvariantquantity'] - $product['quantity'];
                $product_variant           = ProductVariantOption::find($product['variant_id']);
                $product_variant->quantity = $new_qty;
                $product_variant->save();

                $tax_price = 0;
                if(!empty($product['tax']))
                {
                    foreach($product['tax'] as $key => $taxs)
                    {
                        $tax_price += $product['variant_price'] * $product['quantity'] * $taxs['tax'] / 100;

                    }
                }
                $totalprice     += $product['variant_price'] * $product['quantity'] + $tax_price;
                $product_name[] = $product['product_name'] . ' - ' . $product['variant_name'];
                $product_id[]   = $product['id'];
            }
        }

        if(isset($cart['shipping']) && isset($cart['shipping']['shipping_id']) && !empty($cart['shipping']))
        {
            $shipping = Shipping::find($cart['shipping']['shipping_id']);
            if(!empty($shipping))
            {
                $totalprice     = $totalprice + $shipping->price;
                $shipping_name  = $shipping->name;
                $shipping_price = $shipping->price;
                $shipping_data  = json_encode(
                    [
                        'shipping_name' => $shipping_name,
                        'shipping_price' => $shipping_price,
                        'location_id' => $cart['shipping']['location_id'],
                    ]
                );
            }
        }
        else
        {
            $shipping_data = '';
        }

        if($product)
        {
            $customer               = Auth::guard('customers')->user();
            $order                  = new Order();
            $order->order_id        = '#' . time();
            $order->name            = $cust_details['name'];
            $order->email           = $cust_details['email'];
            $order->card_number     = '';
            $order->card_exp_month  = '';
            $order->card_exp_year   = '';
            $order->status          = 'pending';
            $order->phone           = $request->wts_number;
            $order->user_address_id = $cust_details['id'];
            $order->shipping_data   = !empty($shipping_data) ? $shipping_data : '';
            $order->product_id      = implode(',', $product_id);
            $order->price           = $totalprice;
            $order->coupon          = $request->coupon_id;
            $order->coupon_json     = json_encode($coupon);
            $order->discount_price  = !empty($request->dicount_price) ? $request->dicount_price : '0';
            $order->coupon          = $request->coupon_id;
            $order->product         = json_encode($products);
            $order->price_currency  = $store->currency_code;
            $order->txn_id          = '';
            $order->payment_type    = __('Telegram');
            $order->payment_status  = 'approved';
            $order->receipt         = '';
            $order->user_id         = $store['id'];
             $order->customer_id     = $customer->id;
            $order->save();

            $msg = response()->json(
                [
                    'status' => 'success',
                    'success' => __('Your Order Successfully Added'),
                    'order_id' => Crypt::encrypt($order->id),
                ]
            );

            $order_email = $order->email;
            $order_id = Crypt::encrypt($order->id);

            $owner=User::find($store->created_by);
            $owner_email=$owner->email;

            if(isset($store->mail_driver) && !empty($store->mail_driver))
            {
                $dArr = [
                    'order_name' => $order->name,
                ];

                $resp = Utility::sendEmailTemplate('Order Created', $order_email, $dArr, $store, $order_id);

                $resp1=Utility::sendEmailTemplate('Order Created For Owner', $owner_email, $dArr, $store, $order_id);

            }
            if(isset($store->is_twilio_enabled) && $store->is_twilio_enabled=="on")
            {
                 Utility::order_create_owner($order,$owner,$store);
                 Utility::order_create_customer($order,$customer,$store);
            }

            return $msg;
        }
        else
        {
            return response()->json(
                [
                    'status' => 'error',
                    'success' => __('Failed'),
                ]
            );

        }
    }

    public function cod(Request $request, $slug)
    {
        $store        = Store::where('slug', $slug)->first();
        $products     = $request['product'];
        $order_id     = $request['order_id'];
        $cart         = session()->get($slug);
        $cust_details = $cart['customer'];
        if(!empty($request->coupon_id))
        {
            $coupon = ProductCoupon::where('id', $request->coupon_id)->first();
        }
        else
        {
            $coupon = '';
        }

        $product_name = [];
        $product_id   = [];
        $tax_name     = [];
        $totalprice   = 0;
        foreach($products as $key => $product)
        {
            if($product['variant_id'] == 0)
            {
                $new_qty                = $product['originalquantity'] - $product['quantity'];
                $product_edit           = Product::find($product['product_id']);
                $product_edit->quantity = $new_qty;
                $product_edit->save();

                $tax_price = 0;
                if(!empty($product['tax']))
                {
                    foreach($product['tax'] as $key => $taxs)
                    {
                        $tax_price += $product['price'] * $product['quantity'] * $taxs['tax'] / 100;

                    }
                }
                $totalprice     += $product['price'] * $product['quantity'] + $tax_price;
                $product_name[] = $product['product_name'];
                $product_id[]   = $product['id'];
            }
            elseif($product['variant_id'] != 0)
            {
                $new_qty                   = $product['originalvariantquantity'] - $product['quantity'];
                $product_variant           = ProductVariantOption::find($product['variant_id']);
                $product_variant->quantity = $new_qty;
                $product_variant->save();

                $tax_price = 0;
                if(!empty($product['tax']))
                {
                    foreach($product['tax'] as $key => $taxs)
                    {
                        $tax_price += $product['variant_price'] * $product['quantity'] * $taxs['tax'] / 100;

                    }
                }
                $totalprice     += $product['variant_price'] * $product['quantity'] + $tax_price;
                $product_name[] = $product['product_name'];
                $product_id[]   = $product['id'];
            }
        }
        if(isset($cart['shipping']) && isset($cart['shipping']['shipping_id']) && !empty($cart['shipping']))
        {
            $shipping = Shipping::find($cart['shipping']['shipping_id']);
            if(!empty($shipping))
            {
                $totalprice     = $totalprice + $shipping->price;
                $shipping_name  = $shipping->name;
                $shipping_price = $shipping->price;
                $shipping_data  = json_encode(
                    [
                        'shipping_name' => $shipping_name,
                        'shipping_price' => $shipping_price,
                        'location_id' => $cart['shipping']['location_id'],
                    ]
                );
            }

        }
        else
        {
            $shipping_data = '';
        }

        if($product)
        {
             $customer               = Auth::guard('customers')->user();
            $order                  = new Order();
            $order->order_id        = $order_id;
            $order->email           = $cust_details['email'];
            $order->card_number     = '';
            $order->card_exp_month  = '';
            $order->card_exp_year   = '';
            $order->status          = 'pending';
            $order->user_address_id = $cust_details['id'];
            $order->shipping_data   = !empty($shipping_data) ? $shipping_data : '';
            $order->coupon          = $request->coupon_id;
            $order->coupon_json     = json_encode($coupon);
            $order->discount_price  = $request->dicount_price;
            $order->product_id      = implode(',', $product_id);
            $order->price           = $totalprice;
            $order->product         = json_encode($products);
            $order->price_currency  = $store->currency_code;
            $order->txn_id          = '';
            $order->payment_type    = __('COD');
            $order->payment_status  = 'approved';
            $order->receipt         = '';
            $order->user_id         = $store['id'];
             $order->customer_id     = $customer->id;
            $order->save();

            foreach($products as $product_id)
                {
                    $purchased_products = new PurchasedProducts();
                    $purchased_products->product_id  = $product_id['product_id'];
                    $purchased_products->customer_id = $customer->id;
                    $purchased_products->order_id   = $order->id;
                    $purchased_products->save();
                }

            $msg = response()->json(
                [
                    'status' => 'success',
                    'success' => __('Your Order Successfully Added'),
                    'order_id' => Crypt::encrypt($order->id),
                ]
            );
            session()->forget($slug);
           $order_email = $order->email;
                $owner=User::find($store->created_by);

                $owner_email=$owner->email;
                $order_id    = Crypt::encrypt($order->id);


            if(isset($store->mail_driver) && !empty($store->mail_driver))
            {
                $dArr = [
                    'order_name' => $order->name,
                ];

                $resp = Utility::sendEmailTemplate('Order Created', $order_email, $dArr, $store, $order_id);


                    $resp1=Utility::sendEmailTemplate('Order Created For Owner', $owner_email, $dArr, $store, $order_id);



            }
            if(isset($store->is_twilio_enabled) && $store->is_twilio_enabled=="on")
                {
                     Utility::order_create_owner($order,$owner,$store);
                     Utility::order_create_customer($order,$customer,$store);
                }

            return $msg;
        }
        else
        {
            return response()->json(
                [
                    'status' => 'error',
                    'success' => __('Failed'),
                ]
            );

        }
    }

    public function bank_transfer(Request $request, $slug)
    {
        $store        = Store::where('slug', $slug)->first();
        $products     = $request['product'];
        $order_id     = $request['order_id'];
        $cart         = session()->get($slug);
        $cust_details = $cart['customer'];
        if(!empty($request->coupon_id))
        {
            $coupon = ProductCoupon::where('id', $request->coupon_id)->first();
        }
        else
        {
            $coupon = '';
        }

        $product_name = [];
        $product_id   = [];
        $tax_name     = [];
        $totalprice   = 0;
        foreach($products as $key => $product)
        {
            if($product['variant_id'] == 0)
            {
                $new_qty                = $product['originalquantity'] - $product['quantity'];
                $product_edit           = Product::find($product['product_id']);
                $product_edit->quantity = $new_qty;
                $product_edit->save();

                $tax_price = 0;
                if(!empty($product['tax']))
                {
                    foreach($product['tax'] as $key => $taxs)
                    {
                        $tax_price += $product['price'] * $product['quantity'] * $taxs['tax'] / 100;

                    }
                }
                $totalprice     += $product['price'] * $product['quantity'] + $tax_price;
                $product_name[] = $product['product_name'];
                $product_id[]   = $product['id'];
            }
            elseif($product['variant_id'] != 0)
            {
                $new_qty                   = $product['originalvariantquantity'] - $product['quantity'];
                $product_variant           = ProductVariantOption::find($product['variant_id']);
                $product_variant->quantity = $new_qty;
                $product_variant->save();

                $tax_price = 0;
                if(!empty($product['tax']))
                {
                    foreach($product['tax'] as $key => $taxs)
                    {
                        $tax_price += $product['variant_price'] * $product['quantity'] * $taxs['tax'] / 100;

                    }
                }
                $totalprice     += $product['variant_price'] * $product['quantity'] + $tax_price;
                $product_name[] = $product['product_name'];
                $product_id[]   = $product['id'];
            }
        }
        if(isset($cart['shipping']) && isset($cart['shipping']['shipping_id']) && !empty($cart['shipping']))
        {
            $shipping = Shipping::find($cart['shipping']['shipping_id']);
            if(!empty($shipping))
            {
                $totalprice     = $totalprice + $shipping->price;
                $shipping_name  = $shipping->name;
                $shipping_price = $shipping->price;
                $shipping_data  = json_encode(
                    [
                        'shipping_name' => $shipping_name,
                        'shipping_price' => $shipping_price,
                        'location_id' => $cart['shipping']['location_id'],
                    ]
                );
            }

        }
        else
        {
            $shipping_data = '';
        }
        if($product)
        {
            $customer               = Auth::guard('customers')->user();
            $order                  = new Order();
            $order->order_id        = $order_id;
            $order->name            = $cust_details['name'];
            $order->email           = $cust_details['email'];
            $order->card_number     = '';
            $order->card_exp_month  = '';
            $order->card_exp_year   = '';
            $order->status          = 'pending';
            $order->user_address_id = $cust_details['id'];
            $order->shipping_data   = !empty($shipping_data) ? $shipping_data : '';
            $order->product_id      = implode(',', $product_id);
            $order->price           = $totalprice;
            $order->coupon          = $request->coupon_id;
            $order->coupon_json     = json_encode($coupon);
            $order->discount_price  = $request->dicount_price;
            $order->product         = json_encode($products);
            $order->price_currency  = $store->currency_code;
            $order->txn_id          = '';
            $order->payment_type    = __('Bank Transfer');
            $order->payment_status  = 'approved';
            $order->receipt         = '';
            $order->user_id         = $store['id'];
            $order->customer_id     = $customer->id;
            $order->save();

                foreach($products as $product_id)
                    {
                        $purchased_product = new PurchasedProducts();
                        $purchased_product->product_id  = $product_id['product_id'];
                        $purchased_product->customer_id = $customer->id;
                        $purchased_product->order_id   = $order->id;
                        $purchased_product->save();
                    }
            $msg = response()->json(
                [
                    'status' => 'success',
                    'success' => __('Your Order Successfully Added'),
                    'order_id' => Crypt::encrypt($order->id),
                ]
            );
            session()->forget($slug);
            $order_email = $order->email;
                $owner=User::find($store->created_by);

                $owner_email=$owner->email;
                $order_id    = Crypt::encrypt($order->id);
            try
            {
                if(isset($store->mail_driver) && !empty($store->mail_driver))
                {
                    $dArr = [
                        'order_name' => $order->name,
                    ];

                    $order = Crypt::encrypt($order->id);

                    $resp = Utility::sendEmailTemplate('Order Created', $order_email, $dArr, $store, $order_id);

                    $resp1=Utility::sendEmailTemplate('Order Created For Owner', $owner_email, $dArr, $store, $order_id);

                }

                if(isset($store->is_twilio_enabled) && $store->is_twilio_enabled=="on")
                {
                     Utility::order_create_owner($order,$owner,$store);
                     Utility::order_create_customer($order,$customer,$store);
                }

                return $msg;
            }
            catch(\Exception $e)
            {
                return $msg;
            }
        }
        else
        {
            return response()->json(
                [
                    'status' => 'error',
                    'success' => __('Failed'),
                ]
            );
        }
    }

    public function removeSession($slug)
    {
        session()->forget($slug);
    }

    public function grid()
    {
        if(\Auth::user()->type == 'super admin')
        {
            $users  = User::where('created_by', '=', \Auth::user()->creatorId())->where('type', '=', 'owner')->get();
            $stores = Store::get();

            return view('user.grid', compact('users', 'stores'));
        }
        else
        {
            return redirect()->back()->with('error', __('permission Denied'));
        }

    }

    public function upgradePlan($user_id)
    {
        if(\Auth::user()->type == 'super admin')
        {
            $user = User::find($user_id);

            $plans = Plan::get();

            return view('user.plan', compact('user', 'plans'));
        }
    }

    public function activePlan($user_id, $plan_id)
    {
        if(\Auth::user()->type == 'super admin')
        {

            $user       = User::find($user_id);
            $assignPlan = $user->assignPlan($plan_id);
            $plan       = Plan::find($plan_id);
            if($assignPlan['is_success'] == true && !empty($plan))
            {
                $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
                PlanOrder::create(
                    [
                        'order_id' => $orderID,
                        'name' => null,
                        'card_number' => null,
                        'card_exp_month' => null,
                        'card_exp_year' => null,
                        'plan_name' => $plan->name,
                        'plan_id' => $plan->id,
                        'price' => $plan->price,
                        'price_currency' => Utility::getValByName('site_currency'),
                        'txn_id' => '',
                        'payment_status' => 'succeeded',
                        'receipt' => null,
                        'payment_type' => __('Manually'),
                        'user_id' => $user->id,
                    ]
                );

                return redirect()->back()->with('success', __('Plan successfully upgraded.'));
            }
            else
            {
                return redirect()->back()->with('error', __('Plan fail to upgrade.'));
            }
        }

    }

    public function storedit($id)
    {
        if(\Auth::user()->type == 'super admin')
        {
            $user       = User::find($id);
            $user_store = UserStore::where('user_id', $id)->first();
            $store      = Store::where('id', $user_store->store_id)->first();

            return view('admin_store.edit', compact('store', 'user'));
        }
        else
        {
            return redirect()->back()->with('error', __('permission Denied'));
        }
    }

    public function storeupdate(Request $request, $id)
    {
        $user      = User::find($id);
        $validator = \Validator::make(
            $request->all(), [
                               'username' => 'required|max:120',
                               'name' => 'required|max:120',
                           ]
        );
        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }

        $user['username']   = $request->username;
        $user['name']       = $request->name;
        $user['title']      = $request->title;
        $user['phone']      = $request->phone;
        $user['gender']     = $request->gender;
        $user['is_active']  = ($request->is_active == 'on') ? 1 : 0;
        $user['user_roles'] = $request->user_roles;
        $user->update();

        Stream::create(
            [
                'user_id' => \Auth::user()->id,
                'created_by' => \Auth::user()->creatorId(),
                'log_type' => 'updated',
                'remark' => json_encode(
                    [
                        'owner_name' => \Auth::user()->username,
                        'title' => 'user',
                        'stream_comment' => '',
                        'user_name' => $request->name,
                    ]
                ),
            ]
        );

        return redirect()->back()->with('success', __('User Successfully Updated'));

    }

    public function storedestroy($id)
    {
        if(\Auth::user()->type == 'super admin')
        {
            $user      = User::find($id);
            $userstore = UserStore::where('user_id', $user->id)->first();
            $store     = Store::where('id', $userstore->store_id)->first();
            PageOption::where('store_id', $store->id)->delete();

            $user->delete();
            $userstore->delete();
            $store->delete();

            return redirect()->back()->with('success', __('User Store Successfully Deleted'));
        }
        else
        {
            return redirect()->back()->with('error', __('permission Denied'));
        }
    }

    public function changeTheme(Request $request, $slug)
    {
        $validator = \Validator::make(
            $request->all(), [
                               'theme_color' => 'required',
                               'themefile' => 'required',
                           ]
        );
        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }
        $store                = Store::find($slug);
        $store['store_theme'] = $request->theme_color;
        $store['theme_dir']   = $request->themefile;
        $store->save();

        return redirect()->back()->with('success', __('Theme Successfully Updated.'));

    }

    public function changeCurrantStore($storeID)
    {
        $objStore = Store::find($storeID);
        if($objStore->is_active)
        {
            $objUser                = Auth::user();
            $objUser->current_store = $storeID;
            $objUser->update();

            return redirect()->route('dashboard')->with('success', __('Store Change Successfully!'));
        }
        else
        {
            return redirect()->back()->with('error', __('Store is locked'));
        }
    }

    public function customMassage(Request $request, $slug)
    {
        $validator = \Validator::make(
            $request->all(), [
                               'content' => 'required',
                           ]
        );

        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }

        $store                = Store::where('slug', $slug)->first();
        $store->content       = $request['content'];
        $store->item_variable = $request['item_variable'];
        $store->update();

        return redirect()->back()->with('success', __('Massage successfully updated.'));
    }

    public function Editproducts($slug, $theme)
    {
        $store                = Store::where('slug', $slug)->first();
        $getStoreThemeSetting = Utility::getStoreThemeSetting($store->id, $theme);

        return view('settings.edit_theme', compact('store', 'theme', 'getStoreThemeSetting'));
    }

    public function StoreEditProduct(Request $request, $slug, $theme)
    {

        $store = Store::where('slug', $slug)->where('created_by', Auth::user()->id)->first();


        $post = [];

        //Top Bar Setting
        if(isset($request->enable_top_bar) && !empty($request->enable_top_bar) && $request->enable_top_bar == 'on')
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'top_bar_title' => 'required|string|max:255',
                                   'top_bar_number' => 'required|string|max:255',
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $post['enable_top_bar']    = $request->enable_top_bar;
            $post['top_bar_title']     = $request->top_bar_title;
            $post['top_bar_number']    = $request->top_bar_number;
            $post['top_bar_whatsapp']  = $request->top_bar_whatsapp;
            $post['top_bar_instagram'] = $request->top_bar_instagram;
            $post['top_bar_twitter']   = $request->top_bar_twitter;
            $post['top_bar_messenger'] = $request->top_bar_messenger;
        }
        else
        {
            $post['enable_top_bar'] = 'off';
        }

        //Banner Img Setting
        if(isset($request->enable_banner_img) && !empty($request->enable_banner_img) && $request->enable_banner_img == 'on')
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'banner_img' => 'mimes:jpeg,png,jpg,gif,svg,pdf,doc|max:20480',
                               ]
            );

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            if(!empty($request->banner_img))
            {
                $filenameWithExt  = $request->file('banner_img')->getClientOriginalName();
                $filename         = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                $extension        = $request->file('banner_img')->getClientOriginalExtension();
                $fileNameToStores = $filename . '_' . time() . '.' . $extension;
                $dir              = storage_path('uploads/store_logo/');

                if(!file_exists($dir))
                {
                    mkdir($dir, 0777, true);
                }

                $path                      = $request->file('banner_img')->storeAs('uploads/store_logo/', $fileNameToStores);
                $post['enable_banner_img'] = $request->enable_banner_img;
                $post['banner_img']        = $fileNameToStores;
            }

        }
        else
        {
            $post['enable_banner_img'] = 'off';
        }

        //Header Setting
        if(isset($request->enable_header_img) && $request->enable_header_img == 'on')
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'header_title' => 'required|string|max:255',
                                   'header_desc' => 'required|string|max:255',
                                   'button_text' => 'required|string|max:255',
                                   'header_img' => 'mimes:jpeg,png,jpg,gif,svg,pdf,doc|max:20480',
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            if(!empty($request->header_img))
            {
                $filenameWithExt  = $request->file('header_img')->getClientOriginalName();
                $filename         = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                $extension        = $request->file('header_img')->getClientOriginalExtension();
                $fileNameToStores4 = $filename . '_' . time() . '.' . $extension;
                $dir              = storage_path('uploads/store_logo/');

                if(!file_exists($dir))
                {
                    mkdir($dir, 0777, true);
                }

                $path = $request->file('header_img')->storeAs('uploads/store_logo/', $fileNameToStores4);
            }


            $post['enable_header_img'] = $request->enable_header_img;
            $post['header_title']      = $request->header_title;
            $post['header_desc']       = $request->header_desc;
            $post['button_text']       = $request->button_text;
            if(!empty($fileNameToStores4))
            {
                $post['header_img'] = $fileNameToStores4;
            }
        }
        else
        {
            $post['enable_header_img'] = 'off';
        }

        //Features Setting
        if(isset($request->enable_features) && $request->enable_features == 'on')
        {
            /*Enable Features 1*/
            if(isset($request->enable_features1) && $request->enable_features1 == 'on')
            {
                $validator = \Validator::make(
                    $request->all(), [
                                       'features_icon1' => 'required',
                                       'features_title1' => 'required',
                                       'features_description1' => 'required',
                                   ]
                );
                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }
                $post['enable_features1']      = $request->enable_features1;
                $post['features_icon1']        = $request->features_icon1;
                $post['features_title1']       = $request->features_title1;
                $post['features_description1'] = $request->features_description1;
            }
            else
            {
                $post['enable_features1'] = 'off';
            }

            /*Enable Features 1*/
            if(isset($request->enable_features2) && $request->enable_features2 == 'on')
            {
                $validator = \Validator::make(
                    $request->all(), [
                                       'features_icon2' => 'required',
                                       'features_title2' => 'required',
                                       'features_description2' => 'required',
                                   ]
                );
                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }
                $post['enable_features2']      = $request->enable_features2;
                $post['features_icon2']        = $request->features_icon2;
                $post['features_title2']       = $request->features_title2;
                $post['features_description2'] = $request->features_description2;
            }
            else
            {
                $post['enable_features2'] = 'off';
            }

            /*Enable Features 1*/
            if(isset($request->enable_features3) && $request->enable_features3 == 'on')
            {
                $validator = \Validator::make(
                    $request->all(), [
                                       'features_icon3' => 'required',
                                       'features_title3' => 'required',
                                       'features_description3' => 'required',
                                   ]
                );
                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }
                $post['enable_features3']      = $request->enable_features3;
                $post['features_icon3']        = $request->features_icon3;
                $post['features_title3']       = $request->features_title3;
                $post['features_description3'] = $request->features_description3;
            }
            else
            {
                $post['enable_features3'] = 'off';
            }

            $post['enable_features'] = 'on';
        }
        else
        {
            $post['enable_features'] = 'off';
        }

        //Header Img
        if(isset($request->enable_email_subscriber) && $request->enable_email_subscriber == 'on')
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'subscriber_title' => 'required',
                                   'subscriber_sub_title' => 'required',
                                   'subscriber_img' => 'mimes:jpeg,png,jpg,gif,svg,pdf,doc|max:20480',
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }


            if(!empty($request->subscriber_img))
            {
                $filenameWithExt  = $request->file('subscriber_img')->getClientOriginalName();
                $filename         = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                $extension        = $request->file('subscriber_img')->getClientOriginalExtension();
                $fileNameToStores5 = $filename . '_' . time() . '.' . $extension;
                $dir              = storage_path('uploads/store_logo/');

                if(!file_exists($dir))
                {
                    mkdir($dir, 0777, true);
                }

                $path = $request->file('subscriber_img')->storeAs('uploads/store_logo/', $fileNameToStores5);
            }

            $post['enable_email_subscriber'] = $request->enable_email_subscriber;
            $post['subscriber_title']        = $request->subscriber_title;
            $post['subscriber_sub_title']    = $request->subscriber_sub_title;

            if(!empty($fileNameToStores5))
            {
                $post['subscriber_img'] = $fileNameToStores5;
            }
        }
        else
        {
            $post['enable_email_subscriber'] = 'off';
        }

        //Testimonial Setting
        if(isset($request->enable_testimonial) && $request->enable_testimonial == 'on')
        {
            if(!empty($request->testimonial_main_heading))
            {
                $post['testimonial_main_heading'] = $request->testimonial_main_heading;
            }
            if(!empty($request->testimonial_main_heading))
            {
                $post['testimonial_main_heading_title'] = $request->testimonial_main_heading_title;
            }
            /*TESTIMONIAL 1*/
            if(isset($request->enable_testimonial1) && $request->enable_testimonial1 == 'on')
            {
                $validator = \Validator::make(
                    $request->all(), [
                                       'testimonial_name1' => 'required',
                                       'testimonial_description1' => 'required',
                                       'testimonial_about_us1' => 'required',
                                       'testimonial_img1' => 'mimes:jpeg,png,jpg,gif,svg,pdf,doc|max:20480',
                                   ]
                );
                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }
                if(!empty($request['testimonial_img1']))
                {
                    $filenameWithExt   = $request['testimonial_img1']->getClientOriginalName();
                    $filename          = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                    $extension         = $request['testimonial_img1']->getClientOriginalExtension();
                    $fileNameToStores1 = $filename . '_' . time() . 1 . '.' . $extension;
                    $dir               = storage_path('uploads/store_logo/');

                    if(!file_exists($dir))
                    {
                        mkdir($dir, 0777, true);
                    }
                    $path                     = $request['testimonial_img1']->storeAs('uploads/store_logo/', $fileNameToStores1);
                    $post['testimonial_img1'] = $fileNameToStores1;
                }
                $post['enable_testimonial1']      = $request->enable_testimonial1;
                $post['testimonial_name1']        = $request->testimonial_name1;
                $post['testimonial_about_us1']    = $request->testimonial_about_us1;
                $post['testimonial_description1'] = $request->testimonial_description1;
            }
            else
            {
                $post['enable_testimonial1'] = 'off';
            }

            /*TESTIMONIAL 2*/
            if(isset($request->enable_testimonial2) && $request->enable_testimonial2 == 'on')
            {
                $validator = \Validator::make(
                    $request->all(), [
                                       'testimonial_description2' => 'required',
                                       'testimonial_name2' => 'required',
                                       'testimonial_about_us2' => 'required',
                                       'testimonial_img2' => 'mimes:jpeg,png,jpg,gif,svg,pdf,doc|max:20480',
                                   ]
                );
                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }
                if(!empty($request['testimonial_img2']))
                {
                    $filenameWithExt   = $request['testimonial_img2']->getClientOriginalName();
                    $filename          = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                    $extension         = $request['testimonial_img2']->getClientOriginalExtension();
                    $fileNameToStores2 = $filename . '_' . time() . 2 . '.' . $extension;
                    $dir               = storage_path('uploads/store_logo/');

                    if(!file_exists($dir))
                    {
                        mkdir($dir, 0777, true);
                    }
                    $path                     = $request['testimonial_img2']->storeAs('uploads/store_logo/', $fileNameToStores2);
                    $post['testimonial_img2'] = $fileNameToStores2;
                }
                $post['enable_testimonial2']      = $request->enable_testimonial2;
                $post['testimonial_name2']        = $request->testimonial_name2;
                $post['testimonial_about_us2']    = $request->testimonial_about_us2;
                $post['testimonial_description2'] = $request->testimonial_description2;
            }
            else
            {
                $post['enable_testimonial2'] = 'off';
            }

            /*TESTIMONIAL 2*/
            if(isset($request->enable_testimonial3) && $request->enable_testimonial3 == 'on')
            {
                $validator = \Validator::make(
                    $request->all(), [
                                       'testimonial_description3' => 'required',
                                       'testimonial_name3' => 'required',
                                       'testimonial_about_us3' => 'required',
                                       'testimonial_img3' => 'mimes:jpeg,png,jpg,gif,svg,pdf,doc|max:20480',
                                   ]
                );
                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }
                if(!empty($request['testimonial_img3']))
                {
                    $filenameWithExt   = $request['testimonial_img3']->getClientOriginalName();
                    $filename          = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                    $extension         = $request['testimonial_img3']->getClientOriginalExtension();
                    $fileNameToStores3 = $filename . '_' . time() . 3 . '.' . $extension;
                    $dir               = storage_path('uploads/store_logo/');

                    if(!file_exists($dir))
                    {
                        mkdir($dir, 0777, true);
                    }
                    $path                     = $request['testimonial_img3']->storeAs('uploads/store_logo/', $fileNameToStores3);
                    $post['testimonial_img3'] = $fileNameToStores3;
                }
                $post['enable_testimonial3']      = $request->enable_testimonial3;
                $post['testimonial_name3']        = $request->testimonial_name3;
                $post['testimonial_about_us3']    = $request->testimonial_about_us3;
                $post['testimonial_description3'] = $request->testimonial_description3;
            }
            else
            {
                $post['enable_testimonial3'] = 'off';
            }

            $post['enable_testimonial'] = $request->enable_testimonial;
        }
        else
        {
            $post['enable_testimonial'] = 'off';
        }

        //Categories
        if(isset($request->enable_categories) && $request->enable_categories == 'on')
        {
            if($store->theme_dir != 'theme5')
            {
                $validator = \Validator::make(
                    $request->all(), [
                                       'categories' => 'required',
                                       'categories_title' => 'required',
                                   ]
                );
                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }
            }

            $post['enable_categories'] = $request->enable_categories;
            $post['categories']        = !empty($request->categories) ? $request->categories : '';
            $post['categories_title']  = !empty($request->categories_title) ? $request->categories_title : '';
        }
        else
        {
            $post['enable_categories'] = 'off';
        }

        //Brand Logo Setting
        if(isset($request->enable_brand_logo) && $request->enable_brand_logo == 'on')
        {
            $post['enable_brand_logo'] = $request->enable_brand_logo;
        }
        else
        {
            $post['enable_brand_logo'] = 'off';
        }

        if(isset($request->file) && !empty($request->file))
        {
            $file_name = [];
            if(!empty($request->file) && count($request->file) > 0)
            {
                $i = 0;
                foreach($request->file as $file)
                {
                    $i++;
                    $filenameWithExt = $file->getClientOriginalName();
                    $filename        = pathinfo($filenameWithExt, PATHINFO_FILENAME) . '_brand';
                    $extension       = $file->getClientOriginalExtension();
                    $fileNameToStore = $filename . '_' . $i . time() . '.' . $extension;
                    $file_name[]     = $fileNameToStore;
                    $dir             = storage_path('uploads/store_logo/');
                    if(!file_exists($dir))
                    {
                        mkdir($dir, 0777, true);
                    }
                    $path = $file->storeAs('uploads/store_logo/', $fileNameToStore);
                }
            }

            if(!empty($file_name) && count($file_name) > 0)
            {
                $post['brand_logo'] = implode(',', $file_name);
            }
        }
        //End Brand Logo Setting

        //Footer 1 Setting
        if(isset($request->enable_footer_note) && $request->enable_footer_note == 'on')
        {
            if(!empty($request->footer_logo))
            {
                $filenameWithExt  = $request->file('footer_logo')->getClientOriginalName();
                $filename         = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                $extension        = $request->file('footer_logo')->getClientOriginalExtension();
                $fileNameToStores6 = $filename . '_' . time() . '.' . $extension;
                $dir              = storage_path('uploads/store_logo/');
                if(!file_exists($dir))
                {
                    mkdir($dir, 0777, true);
                }
                $footerlogo='footerlogo'.$store->theme_dir.'.'.$extension ;


                $path = $request->file('footer_logo')->storeAs('uploads/store_logo/', $footerlogo);
            }
            $post['enable_footer_note'] = $request->enable_footer_note;
            $post['footer_desc']        = !empty($request->footer_desc) ? $request->footer_desc : '';
            $post['footer_number']      = !empty($request->footer_number) ? $request->footer_number : '';
            /*QUICK LINK 1*/
            if(isset($request->enable_quick_link1) && $request->enable_quick_link1 == 'on')
            {
                $post['enable_quick_link1']      = $request->enable_quick_link1;
                $post['quick_link_header_name1'] = $request->quick_link_header_name1;
                $post['quick_link_name1']        = $request->quick_link_name1;
                $post['quick_link_url1']         = $request->quick_link_url1;
                $post['quick_link_name12']       = $request->quick_link_name12;
                $post['quick_link_url12']        = $request->quick_link_url12;
                $post['quick_link_name13']       = $request->quick_link_name13;
                $post['quick_link_url13']        = $request->quick_link_url13;
                $post['quick_link_name14']       = $request->quick_link_name14;
                $post['quick_link_url14']        = $request->quick_link_url14;
            }
            else
            {
                $post['enable_quick_link1'] = 'off';
            }

            /*QUICk LINK 2*/
            if(isset($request->enable_quick_link2) && $request->enable_quick_link2 == 'on')
            {
                $post['enable_quick_link2']      = $request->enable_quick_link2;
                $post['quick_link_header_name2'] = $request->quick_link_header_name2;
                $post['quick_link_name21']       = $request->quick_link_name21;
                $post['quick_link_url21']        = $request->quick_link_url21;
                $post['quick_link_name22']       = $request->quick_link_name22;
                $post['quick_link_url22']        = $request->quick_link_url22;
                $post['quick_link_name23']       = $request->quick_link_name23;
                $post['quick_link_url23']        = $request->quick_link_url23;
                $post['quick_link_name24']       = $request->quick_link_name24;
                $post['quick_link_url24']        = $request->quick_link_url24;
            }
            else
            {
                $post['enable_quick_link2'] = 'off';
            }

            /*QUICK LINK 3*/
            if(isset($request->enable_quick_link3) && $request->enable_quick_link3 == 'on')
            {
                $post['enable_quick_link3']      = $request->enable_quick_link3;
                $post['quick_link_header_name3'] = $request->quick_link_header_name3;
                $post['quick_link_name31']       = $request->quick_link_name31;
                $post['quick_link_url31']        = $request->quick_link_url31;
                $post['quick_link_name32']       = $request->quick_link_name32;
                $post['quick_link_url32']        = $request->quick_link_url32;
                $post['quick_link_name33']       = $request->quick_link_name33;
                $post['quick_link_url33']        = $request->quick_link_url33;
                $post['quick_link_name34']       = $request->quick_link_name34;
                $post['quick_link_url34']        = $request->quick_link_url34;
            }
            else
            {
                $post['enable_quick_link3'] = 'off';
            }

            /*QUICK LINK 4*/
            if(isset($request->enable_quick_link4) && $request->enable_quick_link4 == 'on')
            {
                $post['enable_quick_link4']      = $request->enable_quick_link4;
                $post['quick_link_header_name4'] = $request->quick_link_header_name4;
                $post['quick_link_name41']       = $request->quick_link_name41;
                $post['quick_link_url41']        = $request->quick_link_url41;
                $post['quick_link_name42']       = $request->quick_link_name42;
                $post['quick_link_url42']        = $request->quick_link_url42;
                $post['quick_link_name43']       = $request->quick_link_name43;
                $post['quick_link_url43']        = $request->quick_link_url43;
                $post['quick_link_name44']       = $request->quick_link_name44;
                $post['quick_link_url44']        = $request->quick_link_url44;
            }
            else
            {
                $post['enable_quick_link4'] = 'off';
            }
            if(!empty($footerlogo))
            {

                $post['footer_logo'] = $footerlogo;

            }
        }
        else
        {
            $post['enable_footer_note'] = 'off';
        }

        //Footer 2 Setting
        if(isset($request->enable_footer) && $request->enable_footer == 'on')
        {
            $post['enable_footer'] = $request->enable_footer;
            $post['email']         = $request->email;
            $post['whatsapp']      = $request->whatsapp;
            $post['facebook']      = $request->facebook;
            $post['instagram']     = $request->instagram;
            $post['twitter']       = $request->twitter;
            $post['youtube']       = $request->youtube;
            $post['footer_note']   = $request->footer_note;
            $post['storejs']       = $request->storejs;
        }
        else
        {
            $post['enable_footer'] = 'off';
        }

        foreach($post as $key => $data)
        {
            $arr = [
                'name' => $key,
                'value' => $data,
                'type' => null,
                'store_id' => $store->id,
                'theme_name' => $store->theme_dir,
                'created_by' => Auth::user()->creatorId(),
            ];

            StoreThemeSettings::updateOrCreate(
                [
                    'name' => $key,
                    'store_id' => $store->id,
                    'theme_name' => $store->theme_dir,
                ], $arr
            );
        }

        return redirect()->back()->with('success', __('Successfully Saved!'));
    }

    public function brandfileDelete($slug, $theme, $name)
    {
        $store                = Store::where('slug', $slug)->where('created_by', Auth::user()->id)->first();
        $getStoreThemeSetting = Utility::getStoreThemeSetting($slug, $theme, $name);
        $dir                  = storage_path('uploads/store_logo/');
        $brandarray           = explode(',', $getStoreThemeSetting['brand_logo']);
        if(!empty($name))
        {
            foreach($brandarray as $k => $val)
            {
                if($val == $name)
                {
                    if(!file_exists($dir . $name))
                    {
                        unset($brandarray[$k]);
                        $brand_logo_update        = StoreThemeSettings::where('name', 'brand_logo')->where('store_id', $store->id)->where('theme_name', $store->theme_dir)->first();
                        $brand_logo_update->value = implode(',', $brandarray);
                        $brand_logo_update->save();

                        return response()->json(
                            [
                                'error' => __('File not exists in folder!'),
                                'id' => $name,
                            ]
                        );
                    }
                    else
                    {
                        unlink($dir . $name);
                        unset($brandarray[$k]);
                        $post['brand_logo']       = implode(',', $brandarray);
                        $brand_logo_update        = StoreThemeSettings::where('name', 'brand_logo')->where('store_id', $store->id)->where('theme_name', $store->theme_dir)->first();
                        $brand_logo_update->value = implode(',', $brandarray);
                        $brand_logo_update->save();

                        return response()->json(
                            [
                                'success' => __('Record deleted successfully!'),
                                'name' => $name,
                            ]
                        );
                    }
                }

            }
        }

    }

    public function AddToWishlist($slug, $id)
    {
        //dd("hii");
        if(Utility::CustomerAuthCheck($slug) == false)
        {

            return response()->json(
                [
                    'code' => 200,
                    'status' => 'error',
                    'error' => 'You need to login',
                ]
            );
        }
        else
        {
            $store   = Store::where('slug', $slug)->where('is_store_enabled','1')->first();
            if(empty($store))
            {
                return response()->json(
                [
                    'code' => 404,
                    'status' => 'error',
                    'error' => 'Page not found',
                ]
            );
            }

            $product = Product::where('store_id', $store->id)->where('product_display','on')->where('id', $id)->first();

            $cart = session()->get($store->slug);

            if(!empty($cart['wishlist']) && $cart['wishlist'] != null)
            {
                $key = false;
                foreach($cart['wishlist'] as $k => $value)
                {
                    if($id == $value['product_id'])
                    {
                        $key = $k;
                    }
                }


                if($key !== false)
                {
                    if($cart['wishlist'][$key]['product_id'] == $id)
                    {
                        return response()->json(
                            [
                                'is_success' => true,
                                'id' => $id,
                                'status' => __('error'),
                                'message' => __('Already in Wish List.'),
                            ]
                        );
                    }
                    else
                    {
                        if($product->is_cover != null)
                        {
                            $img = \Storage::exists('uploads/is_cover_image/' . ($product->is_cover));
                        }
                        else
                        {
                            $img = false;
                        }

                        $cart['wishlist'][$id] = [
                            "product_id" => $product->id,
                            "store_id" => $product->store_id,
                            "product_name" => $product->name,
                            "product_categorie" => $product->product_categorie,
                            "price" => $product->price,
                            "quantity" => $product->quantity,
                            "SKU" => $product->SKU,
                            "product_tax" => $product->product_tax,
                            "product_display" => $product->product_display,
                            "enable_product_variant" => $product->enable_product_variant,
                            "variants_json" => $product->variants_json,
                            "image" => ($img == true) ? Storage::url('uploads/is_cover_image/' . $product->is_cover) : '',
                            "is_active" => $product->is_active,
                            "description" => $product->description,
                            "created_by" => $product->created_by,
                        ];
                    }
                }
                else
                {
                    if($product->is_cover != null)
                    {
                        $img = \Storage::exists('uploads/is_cover_image/' . ($product->is_cover));
                    }
                    else
                    {
                        $img = false;
                    }

                    $cart['wishlist'][$id] = [
                        "product_id" => $product->id,
                        "store_id" => $product->store_id,
                        "product_name" => $product->name,
                        "product_categorie" => $product->product_categorie,
                        "price" => $product->price,
                        "quantity" => $product->quantity,
                        "SKU" => $product->SKU,
                        "product_tax" => $product->product_tax,
                        "product_display" => $product->product_display,
                        "enable_product_variant" => $product->enable_product_variant,
                        "variants_json" => $product->variants_json,
                        "image" => ($img == true) ? Storage::url('uploads/is_cover_image/' . $product->is_cover) : '',
                        "is_active" => $product->is_active,
                        "description" => $product->description,
                        "created_by" => $product->created_by,
                    ];
                }
            }
            else
            {
                if($product->is_cover != null)
                {
                    $img = \Storage::exists('uploads/is_cover_image/' . ($product->is_cover));
                }
                else
                {
                    $img = false;
                }

                $cart['wishlist'][$id] = [
                    "product_id" => $product->id,
                    "store_id" => $product->store_id,
                    "product_name" => $product->name,
                    "product_categorie" => $product->product_categorie,
                    "price" => $product->price,
                    "quantity" => $product->quantity,
                    "SKU" => $product->SKU,
                    "product_tax" => $product->product_tax,
                    "product_display" => $product->product_display,
                    "enable_product_variant" => $product->enable_product_variant,
                    "variants_json" => $product->variants_json,
                    "image" => ($img == true) ? Storage::url('uploads/is_cover_image/' . $product->is_cover) : '',
                    "is_active" => $product->is_active,
                    "description" => $product->description,
                    "created_by" => $product->created_by,
                ];
            }

            session()->put($store->slug, $cart);
            $wishlist_count = count($cart['wishlist']);


        return response()->json(
            [
                'id' => $id,
                'count' => $wishlist_count,
                'status' => __('Success'),
                'message' => __('Added To Wish List.'),
            ]
        );
       }
    }

    public function delete_wishlist_item($slug, $id)
    {
        if(Utility::CustomerAuthCheck($slug) == false)
        {
            return redirect()->back()->with('error', __('You need to login!'));
        }
        else
        {
            $cart = session()->get($slug);

            foreach($cart['wishlist'] as $key => $product)
            {
                if($key == $id)
                {
                    unset($cart['wishlist'][$key]);
                }
            }

            $wishlist_count = count($cart['wishlist']);

            if($wishlist_count == 0)
            {
                unset($cart['wishlist']);
            }

            session()->put($slug, $cart);

            return response()->json(
                [
                    'id' => $id,
                    'count' => $wishlist_count,
                    'status' => __('success'),
                    'message' => __('Item successfully Deleted.'),
                ]
            );
        }
    }

    public function Wishlist($slug)
    {
        if(Utility::CustomerAuthCheck($slug) == false)
        {
            return redirect($slug . '/customer-login');
        }
        else
        {
            $store          = Store::where('slug', $slug)->where('is_store_enabled','1')->first();
            if(empty($store))
            {
                 return abort('404', 'Page not found');
            }
            $page_slug_urls = PageOption::where('store_id', $store->id)->get();
            $blog           = Blog::where('store_id', $store->id)->count();
            $cart           = session()->get($slug);

            if(!empty($cart) && isset($cart['wishlist']))
            {
                $products = $cart['wishlist'];
            }
            else
            {
                $products = [];
            }

            if(isset($cart['wishlist']))
            {
                $wishlist = $cart['wishlist'];
            }
            else
            {
                $wishlist = [];
            }
            $total_item = 0;
            if(isset($cart['products']))
            {
                if(isset($cart) && !empty($cart['products']))
                {
                    $total_item = count($cart['products']);
                }
                else
                {
                    $total_item = 0;
                }
            }

            return view('storefront.' . $store->theme_dir . '.wishlist', compact('page_slug_urls', 'blog', 'wishlist', 'total_item', 'store', 'products'));
        }
    }

    public function downloadable_prodcut(Request $request, $slug)
    {
        $store = Store::where('slug', $slug)->where('is_store_enabled','1')->first();
        if(empty($store))
        {
            return response()->json(
                [
                    'status' => __('error'),
                    'message' => __('Page Not Found.'),
                ]
            );
        }

        $order = Order::where('id', $request->order_id)->where('user_id', $store->id)->first();

        if($order->status == 'pending')
        {
            return response()->json(
                [
                    'status' => __('error'),
                    'message' => __('Your product is not delivered.'),
                ]
            );
        }
        if($order->status == 'Cancel Order')
        {
            return response()->json(
                [
                    'status' => __('error'),
                    'message' => __('Your Order is Cancelled.'),
                ]
            );
        }
        if($order->status == 'delivered')
        {
            if(isset($store->mail_driver) && !empty($store->mail_driver))
            {
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

                    Mail::to(
                        [
                            $order['email'],
                        ]
                    )->send(new ProdcutMail($order, $request['download_product'], $store));

                    return response()->json(
                        [
                            'status' => __('success'),
                            'msg' => __('Please check your email'),
                            'message' => __('successfully send'),
                        ]
                    );
                }
                catch(\Exception $e)
                {
                     return response()->json(
                    [
                        'status' => __('error'),
                        'msg' => __('Please contact your shop owner'),
                        'message' => __('E-Mail has been not sent due to SMTP configuration'),
                    ]
                );
                }
            }
            else
            {
                return response()->json(
                    [
                        'status' => __('error'),
                        'msg' => __('Please contact your shop owner'),
                        'message' => __('E-Mail has been not sent due to SMTP configuration'),
                    ]
                );
            }
        }
    }

    public function userCreate($slug)
    {
        $store          = Store::where('slug', $slug)->where('is_store_enabled','1')->first();
         if(empty($store))
        {
            return redirect()->back()->with('error', __('Store not available'));
        }
        $page_slug_urls = PageOption::where('store_id', $store->id)->get();
        $blog           = Blog::where('store_id', $store->id)->first();
        $cart = session()->get($slug);
        $total_item = 0;
        if(isset($cart['products']))
        {
            if(isset($cart) && !empty($cart['products']))
            {
                $total_item = count($cart['products']);
            }
            else
            {
                $total_item = 0;
            }
        }




        /*return view('storefront.' . $store->theme_dir . '.user.login', compact('blog', 'store', 'slug', 'page_slug_urls'));

        $store                 = Store::where('slug', $slug)->first();
        $blog                  = Blog::where('store_id', $store->id);
        $page_slug_urls        = PageOption::where('store_id', $store->id)->get();
        $demoStoreThemeSetting = Utility::demoStoreThemeSetting($store->id);
        if(empty($store))
        {
            return redirect()->back()->with('error', __('Store not available'));
        }*/

        return view('storefront.' . $store->theme_dir . '.user.create', compact('blog', 'total_item','slug', 'store', 'page_slug_urls'));
    }

    protected function userStore($slug, Request $request)
    {
        $store          = Store::where('slug', $slug)->where('is_store_enabled','1')->first();
        if(empty($store))
        {
            return redirect()->back()->with('error', __('Store not available'));
        }
        $page_slug_urls = PageOption::where('store_id', $store->id)->get();
        $blog           = Blog::where('store_id', $store->id);
        $settings = Utility::settings();

        if(empty($store))
        {
            return redirect()->back()->with('error', __('Store not available'));
        }
        $validate = Validator::make(
            $request->all(), [
                               'name' => [
                                   'required',
                                   'string',
                                   'max:255',
                               ],
                               'phone_number' => [
                                   'required',
                                   'max:255',
                               ],
                               'email' => [
                                   'required',
                                   'string',
                                   'email',
                                   'max:255',
                               ],
                               'password' => [
                                   'required',
                                   'string',
                                   'min:8',
                                   'confirmed',
                               ],
                           ]
        );
        $vali     = Customer::where('email', $request->email)->where('store_id', $store->id)->where('phone_number', $request->phone_number)->count();
        if($validate->fails())
        {
            $message = $validate->getMessageBag();

            return redirect()->back()->with('error', $message->first());
        }
        elseif($vali > 0)
        {
            return redirect()->back()->with('error', __('Email already exists'));
        }

        $customer               = new Customer();
        $customer->name         = $request->name;
        $customer->email        = $request->email;
        $customer->phone_number = $request->phone_number;
        $customer->password     = Hash::make($request->password);
        $customer->lang         = !empty($settings['default_language']) ? $settings['default_language'] : 'en';
        $customer->avatar       = 'avatar.png';
        $customer->store_id     = $store->id;

        $customer->save();
        if(Auth::guard('customers')->attempt($request->only('email', 'password'), $request->filled('remember')))
        {
             $cart = session()->get($slug);
            //Authentication passed...
            if($cart == 1)
            {
                return redirect()->route('store.cart', $slug)->with('success', __('You can checkout now.'));
            }
            else
            {
                return redirect()->route('customer.home', $slug);
            }

        }


        //return redirect()->route('customer.home', $slug)->with('success', __('Account Created Successfully.'));
        //return redirect()->back()->with('success', __('Account Created Successfully.'));
    }

    public function customerHome($slug)
    {

           //dd(Auth::guard('customers')->user());
            $store                 = Store::where('slug', $slug)->where('is_store_enabled','1')->first();
            if(empty($store))
            {
                return abort('404', 'Page not found');
            }
             $orders = Order::where('customer_id',Auth::guard('customers')->user()->id)->orderBy('id', 'DESC')->get();
            // $blog                  = Blog::where('store_id', $store->id);
            // $page_slug_urls        = PageOption::where('store_id', $store->id)->get();
            $storethemesetting = \App\Models\Utility::demoStoreThemeSetting($store->id, $store->theme_dir);

            //  $total_item = 0;
            //   $cart = session()->get($slug);
            // if(isset($cart['products']))
            // {

            //     if(isset($cart) && !empty($cart['products']))
            //     {
            //         $total_item = count($cart['products']);
            //     }
            //     else
            //     {
            //         $total_item = 0;
            //     }
            // }

            // if(empty($store))
            // {
            //     return redirect()->back()->with('error', __('Store not available'));
            // }
            // $purchased_products = Product::where('store_id', $store->id)->where('product_display','on')->get();



            return view('storefront.' . $store->theme_dir . '.customer.index',compact('orders','storethemesetting','store'));
    }

    public function remcoup(Request $request){
        $store          = Store::where('id', $request->slug)->where('is_store_enabled','1')->first();
        if(empty($store))
        {
            return abort('404', 'Page not found');
        }
        $cart = session()->get($store->slug);

        if(isset($cart['coupon'])){
            unset($cart['coupon']);
            session()->put($store->slug, $cart);

        }
    }

    public function employeePassword($id)
    {
        $eId        = \Crypt::decrypt($id);
        $user = User::find($eId);
        return view('admin_store.reset', compact('user'));
    }

    public function employeePasswordReset(Request $request, $id){
        $validator = \Validator::make(
            $request->all(), [
                               'password' => 'required|confirmed|same:password_confirmation',
                           ]
        );

        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }


        $user                 = User::where('id', $id)->first();
        $user->forceFill([
                             'password' => Hash::make($request->password),
                         ])->save();

        return redirect()->back()->with(
            'success', 'User Password successfully updated.'
        );

    }

    public function paymentwallstoresession(Request $request,$slug)
    {

        $store    = Store::where('slug', $slug)->first();
        $products = '';
        $cart     = session()->get($slug);
        if(\Auth::check())
        {
            $store_payment_setting = Utility::getPaymentSetting();
        }
        else
        {
            $store_payment_setting = Utility::getPaymentSetting($store->id);
        }
        $cust_details = $cart['customer'];

        if(!empty($cart))
        {
            $products = $cart['products'];
        }
        else
        {
            return redirect()->back()->with('error', __('Please add to product into cart'));
        }

        if(isset($cart['coupon']['data_id']))
        {
            $coupon = ProductCoupon::where('id', $cart['coupon']['data_id'])->first();
        }
        else
        {
            $coupon = '';
        }
        $product_name = [];
        $product_id   = [];
        $tax_name     = [];
        $totalprice   = 0;

        foreach($products as $key => $product)
        {
            if($product['variant_id'] == 0)
            {
                $new_qty                = $product['originalquantity'] - $product['quantity'];
                $product_edit           = Product::find($product['product_id']);
                $product_edit->quantity = $new_qty;
                $product_edit->save();

                $tax_price = 0;
                if(!empty($product['tax']))
                {
                    foreach($product['tax'] as $key => $taxs)
                    {
                        $tax_price += $product['price'] * $product['quantity'] * $taxs['tax'] / 100;

                    }
                }
                $totalprice     += $product['price'] * $product['quantity'] + $tax_price;
                $product_name[] = $product['product_name'];
                $product_id[]   = $product['id'];
            }
            elseif($product['variant_id'] != 0)
            {
                $new_qty                   = $product['originalvariantquantity'] - $product['quantity'];
                $product_variant           = ProductVariantOption::find($product['variant_id']);
                $product_variant->quantity = $new_qty;
                $product_variant->save();

                $tax_price = 0;
                if(!empty($product['tax']))
                {
                    foreach($product['tax'] as $key => $taxs)
                    {
                        $tax_price += $product['variant_price'] * $product['quantity'] * $taxs['tax'] / 100;

                    }
                }
                $totalprice     += $product['variant_price'] * $product['quantity'] + $tax_price;
                $product_name[] = $product['product_name'];
                $product_id[]   = $product['id'];
            }
        }

        return  redirect()->route('paymentwall.index',[$slug,
                                                        "totalprice"=> $totalprice]);
    }

    public function storeenable($userid)
    {


        if(\Auth::user()->type == 'super admin')
        {
            $users = User::select(
                [
                    'users.*',
                    'stores.is_store_enabled as store_display',
                ]
            )->join('stores', 'stores.created_by', '=', 'users.id')->where('users.id',$userid)->where('users.type', '=', 'Owner')->groupBy('users.id')->first();

            return view('admin_store.storeenabled', compact('users'));
        }
    else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

    }

    public function storeenableupdate(Request $request,$userid)
    {
        $users = User::select(
                [
                    'users.*',
                    'stores.is_store_enabled as store_display',
                ]
            )->join('stores', 'stores.created_by', '=', 'users.id')->where('users.id',$userid)->where('users.type', '=', 'Owner')->groupBy('users.id')->first();
        $stores=Store::where('created_by',$users->creatorId())->get();

        foreach ($stores as $key => $value) {
            if($value->is_store_enabled==1)
            {
                $value->is_store_enabled=0;
                $value->update();
                $msg="store disabled successfully...";
            }
            elseif($value->is_store_enabled==0)
            {
                $value->is_store_enabled=1;
                $value->update();
                $msg="store enable successfully...";
            }

        }

        return redirect()->back()->with('success', __($msg));
    }

    public function customerindex()
    {
        if(\Auth::user()->type == 'Owner')
        {

            $customers=Customer::where('store_id',\Auth::user()->current_store)->get();



            return view('customer.index', compact('customers'));
        }
        else
        {
             return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
     public function customershow($id)
    {
        if(\Auth::user()->type == 'Owner')
        {
           $orders=Order::where('customer_id',$id)->get();

           return view('orders.index', compact('orders'));
        }
       else
       {
         return redirect()->back()->with('error', __('Permission denied.'));
       }

    }

}
