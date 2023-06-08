<?php

namespace App\Http\Controllers;

use App\Models\LandingPageSections;
use App\Models\Order;
use App\Models\Plan;
use App\Models\PlanOrder;
use App\Models\Product;
use App\Models\Store;
use App\Models\Stream;
use App\Models\Task;
use App\Models\User;
use App\Models\UserDetail;
use App\Models\UserStore;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Constraint\Count;
use Spatie\Permission\Models\Role;
use Stripe;

class DashboardController extends Controller
{
    use \RachidLaasri\LaravelInstaller\Helpers\MigrationsHelper;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        if(!file_exists(storage_path() . "/installed"))
        {
            header('location:install');
            die;
        }
        else
        {
            $local = parse_url(config('app.url'))['host'];

            // Get the request host
            $remote = request()->getHost();
            // Get the remote domain

            // remove WWW
            $remote = str_replace('www.', '', $remote);

            $store = Store::where('domains', '=', $remote)->where('enable_domain', 'on')->first();
            // If the domain exists
            if($store && $store->enable_domain == 'on')
            {
                return app('App\Http\Controllers\StoreController')->storeSlug($store->slug);
            }
            $sub_store = Store::where('subdomain', '=', $remote)->where('enable_subdomain', 'on')->first();
            if($sub_store && $sub_store->enable_subdomain == 'on')
            {
                return app('App\Http\Controllers\StoreController')->storeSlug($sub_store->slug);
            }
        }

        if(\Auth::check())
        {
            if(\Auth::user()->type == 'Owner')
            {
                $store      = Auth::user();
                $newproduct = Product::where('store_id', $store->current_store)->count();
                $products   = Product::where('store_id', $store->current_store)->limit(5)->get();
                $new_orders = Order::where('user_id', $store->current_store)->limit(8)->orderBy('id', 'DESC')->get();
                $orders     = Order::where('user_id', $store->current_store)->get();
                $chartData  = $this->getOrderChart(['duration' => 'week']);
                $store_id   = Store::where('id', $store->current_store)->first();
                if($store_id)
                {
                    $app_url               = trim(env('APP_URL'), '/');
                    $store_id['store_url'] = $app_url . '/store/' . $store_id['slug'];
                }

                $totle_sale  = 0;
                $totle_order = 0;
                if(!empty($orders))
                {
                    $pro_qty   = 0;
                    $item_id   = [];
                    $totle_qty = [];
                    foreach($orders as $order)
                    {
                        $order_array = json_decode($order->product);
                        $pro_id      = [];
                        foreach($order_array as $key => $item)
                        {
                            if(!in_array($item->id, $item_id))
                            {
                                $item_id[]   = $item->id;
                                $totle_qty[] = $item->quantity;
                            }
                            else
                            {

                                $totle_qty[array_search($item->id, $item_id)] += $item->quantity;
                            }
                        }
                        $totle_sale += $order['price'];
                        $totle_order++;
                    }
                }

                return view('home', compact('products', 'store_id', 'totle_sale', 'store', 'orders', 'totle_order', 'newproduct', 'item_id', 'totle_qty', 'chartData', 'new_orders'));
            }
            else
            {
                $user                       = \Auth::user();
                $user['total_user']         = $user->countCompany();
                $user['total_paid_user']    = $user->countPaidCompany();
                $user['total_orders']       = Order::total_orders();
                $user['total_orders_price'] = Order::total_orders_price();
                $user['total_plan_price']   = PlanOrder::total_plan_price();
                $user['total_plan']         = Plan::total_plan();
                $user['most_purchese_plan'] = (!empty(Plan::most_purchese_plan()) ? Plan::most_purchese_plan()->name : '-');
                $chartData                  = $this->getOrderChart(['duration' => 'week']);


                return view('home', compact('user', 'chartData'));

            }
        }
        else
        {
            if(!file_exists(storage_path() . "/installed"))
            {
                header('location:install');
                die;
            }
            else
            {
                $settings = Utility::settings();

                if($settings['display_landing_page'] == 'on')
                {
                    return view('layouts.landing');
                }
                else
                {
                    return redirect('login');
                }
            }
        }
    }

    public function     getOrderChart($arrParam)
    {
        $user        = Auth::user();
        $userstore   = UserStore::where('store_id', $user->current_store)->first();
        $arrDuration = [];
        if($arrParam['duration'])
        {
            if($arrParam['duration'] == 'week')
            {
                $previous_week = strtotime("-2 week +1 day");
                for($i = 0; $i < 7; $i++)
                {
                    $arrDuration[date('Y-m-d', $previous_week)] = date('d-M', $previous_week);
                    $previous_week                              = strtotime(date('Y-m-d', $previous_week) . " +1 day");
                }
            }
        }
        $arrTask          = [];
        $arrTask['label'] = [];
        $arrTask['data']  = [];
        foreach($arrDuration as $date => $label)
        {
            if(Auth::user()->type == 'Owner')
            {
                $data = Order::select(\DB::raw('count(*) as total'))->where('user_id', $userstore->store_id)->whereDate('created_at', '=', $date)->first();
            }
            else
            {
                $data = Order::select(\DB::raw('count(*) as total'))->whereDate('created_at', '=', $date)->first();
            }

            $arrTask['label'][] = $label;
            $arrTask['data'][]  = $data->total;
        }
        return $arrTask;
    }

    public function stripe(Request $request)
    {
        $price   = 100;
        $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
        if($price > 0.0)
        {
            Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
            $data = Stripe\Charge::create(
                [
                    "amount" => 100 * $price,
                    "currency" => "usd",
                    "source" => $request->stripeToken,
                    "description" => " Test Plan ",
                    "metadata" => ["order_id" => $orderID],
                ]
            );
        }
    }

    public function check()
    {

    }

    public function profile()
    {
        $userDetail = \Auth::user();

        return view('profile', compact('userDetail'));
    }

    public function editprofile(Request $request)
    {
        $userDetail = \Auth::user();

        $user = User::findOrFail($userDetail['id']);
        $this->validate(
            $request, [
                        'name' => 'required|max:120',
                        'email' => 'required|email|unique:users,email,' . $userDetail['id'],
                        'profile' => 'mimes:jpeg,png,jpg,gif,svg,pdf,doc|max:20480',
                    ]
        );

        if($request->hasFile('profile'))
        {
            $filenameWithExt = $request->file('profile')->getClientOriginalName();
            $filename        = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension       = $request->file('profile')->getClientOriginalExtension();
            $fileNameToStore = $filename . '_' . time() . '.' . $extension;

            $dir        = storage_path('uploads/profile/');
            $image_path = $dir . $userDetail['avatar'];

            if(!file_exists($dir))
            {
                mkdir($dir, 0777, true);
            }

            $path = $request->file('profile')->storeAs('uploads/profile/', $fileNameToStore);

        }

        if(!empty($request->profile))
        {
            $user['avatar'] = $fileNameToStore;
        }
        $user['name']  = $request['name'];
        $user['email'] = $request['email'];
        $user->save();


        return redirect()->back()->with('success', __('Profile successfully updated.'));
    }

    public function updatePassword(Request $request)
    {
        if(\Auth::Check())
        {
            $request->validate(
                [
                    'current_password' => 'required',
                    'new_password' => 'required|min:6',
                    'confirm_password' => 'required|same:new_password',
                ]
            );
            $objUser          = \Auth::user();
            $request_data     = $request->All();
            $current_password = $objUser->password;
            if(Hash::check($request_data['current_password'], $current_password))
            {
                $user_id            = \Auth::User()->id;
                $obj_user           = User::find($user_id);
                $obj_user->password = Hash::make($request_data['new_password']);;
                $obj_user->save();

                return redirect()->route('profile', $objUser->id)->with('success', __('Password successfully updated.'));
            }
            else
            {
                return redirect()->route('profile', $objUser->id)->with('error', __('Please enter correct current password.'));
            }
        }
        else
        {
            return redirect()->route('profile', \Auth::user()->id)->with('error', __('Something is wrong.'));
        }
    }

    public function changeMode()
    {
        $usr = Auth::user();
        if($usr->mode == 'light')
        {
            $usr->mode = 'dark';
        }
        else
        {
            $usr->mode = 'light';
        }
        $usr->save();

        return redirect()->back();
    }
}
