<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Plan;
use App\Models\Utility;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Spatie\Permission\Models\Role;
use App\Models\Store;
use App\Models\UserStore;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     *
     * @return \Illuminate\View\View
     */

    public function __construct()
    {
        $this->middleware('guest');
    }

    public function create()
    {
        if(Utility::getValByName('signup_button') == 'on')
        {
            return view('auth.register');
        }
        else
        {
            return abort('404', 'Page not found');
        }

    }


    /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        if(env('RECAPTCHA_MODULE') == 'yes')
        {
            $validation['g-recaptcha-response'] = 'required|captcha';
        }
         else
        {
            $validation=[];
        }
        $this->validate($request, $validation);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $objUser = User::create([

            'name' => $request->name,
            'email' =>$request->email,
            'password' => Hash::make($request->password),
            'type' => 'Owner',
            'lang' => !empty($settings['default_language']) ? $settings['default_language'] : 'en',
            'avatar' => 'avatar.png',
            'created_by' => 1,
        ]);

        $objStore = Store::create(
            [
                'created_by' => $objUser->id,
                'name' => $request->store_name,
                'email' => $request->email,
                'logo' => !empty($settings['logo']) ? $settings['logo'] : 'logo-dark.png',
                'invoice_logo' => !empty($settings['logo']) ? $settings['logo'] : 'invoice_logo.png',
                'lang' => !empty($settings['default_language']) ? $settings['default_language'] : 'en',
                'currency' => !empty($settings['currency_symbol']) ? $settings['currency_symbol'] : '$',
                'currency_code' => !empty($settings->currency) ? $settings->currency : 'USD',
                'paypal_mode' => 'sandbox',
            ]
        );

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
        $objUser->plan          = Plan::first()->id;
        $objUser->save();
        UserStore::create(
            [
                'user_id' => $objUser->id,
                'store_id' => $objStore->id,
                'permission' => 'Owner',
            ]
        );


        return redirect(RouteServiceProvider::HOME);

    }

    public function showRegistrationForm($lang = '')
    {
        if(empty($lang))
        {
            $lang = Utility::getValByName('default_language');
        }

        \App::setLocale($lang);
        if(Utility::getValByName('signup_button') == 'on')
        {
            return view('auth.register', compact('lang'));
        }
        else
        {
            return abort('404', 'Page not found');
        }

    }
}
