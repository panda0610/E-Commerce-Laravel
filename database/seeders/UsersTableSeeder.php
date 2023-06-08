<?php

namespace Database\Seeders;
use App\Models\User;
use App\Models\Store;
use App\Models\UserStore;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use App\Models\Utility;
use App\Models\BlogSocial;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $superAdmin              = User::create(
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@example.com',
                'password' => Hash::make('1234'),
                'type' => 'super admin',
                'lang' => 'en',
                'created_by' => 0,
            ]
        );
        $_ENV['CURRENCY_SYMBOL'] = '$';
        $_ENV['CURRENCY']        = 'USD ';

        $admin = User::create(
            [
                'name' => 'Owner',
                'email' => 'owner@example.com',
                'password' => Hash::make('1234'),
                'type' => 'Owner',
                'created_by' => $superAdmin->id,
            ]
        );

        $objStore             = Store::create(
            [
                'name' => 'My Store',
                'email' => 'owner@example.com',
                'enable_storelink' => 'on',
                'content' => 'Hi,
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
',
                'item_variable' => '{sku} : {quantity} x {product_name} - {variant_name} + {item_tax} = {item_total}',
                'store_theme' => 'green-color.css',
                'theme_dir' => 'theme1',
                'enable_rating' => 'on',
                'logo' => 'logo-dark.png',
                'created_by' => $admin->id,
            ]
        );
        $admin->current_store = $objStore->id;
        $admin->save();

        UserStore::create(
            [
                'user_id' => $admin->id,
                'store_id' => $objStore->id,
                'permission' => 'Owner',
            ]
        );
       BlogSocial::create(
            [
                'enable_social_button' => 'on',
                'enable_email' => 'on',
                'enable_twitter' => 'on',
                'enable_facebook' => 'on',
                'enable_googleplus' => 'on',
                'enable_linkedIn' => 'on',
                'enable_pinterest' => 'on',
                'enable_stumbleupon' => 'on',
                'enable_whatsapp' => 'on',
                'store_id' => $objStore->id,
                'created_by' => $admin->id,
            ]
        );

        Utility::defaultEmail();
        Utility::userDefaultData();

    }
}
