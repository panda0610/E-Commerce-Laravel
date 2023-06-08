<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\Plan;
use App\Models\PlanOrder;
use App\Models\User;
use App\Models\Utility;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PlanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $objUser = \Auth::user();
        if($objUser->type == 'super admin' || $objUser->type == 'Owner')
        {
            if($objUser->type == 'super admin')
            {
                $orders = PlanOrder::select(
                    [
                        'plan_orders.*',
                        'users.name as user_name',
                    ]
                )->join('users', 'plan_orders.user_id', '=', 'users.id')->orderBy('plan_orders.created_at', 'DESC')->get();
            }
            else
            {
                $orders = PlanOrder::select(
                    [
                        'plan_orders.*',
                        'users.name as user_name',
                    ]
                )->join('users', 'plan_orders.user_id', '=', 'users.id')->orderBy('plan_orders.created_at', 'DESC')->where('users.id', '=', $objUser->id)->get();
            }

            $plans = Plan::get();
            $admin_payments_setting = Utility::getAdminPaymentSetting();
            return view('plans.index', compact('plans', 'orders','admin_payments_setting'));
        }
        else
        {
            return redirect()->route('dashboard');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(\Auth::user()->type == 'super admin')
        {
            $arrDuration = Plan::$arrDuration;
           
            return view('plans.create', compact('arrDuration'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

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
            $admin_payments_setting = Utility::getAdminPaymentSetting();
            if((isset($admin_payments_setting['is_stripe_enabled']) && $admin_payments_setting['is_stripe_enabled'] == 'on')
                || (isset($admin_payments_setting['is_paypal_enabled']) && $admin_payments_setting['is_paypal_enabled'] == 'on')
                || (isset($admin_payments_setting['is_paystack_enabled']) && $admin_payments_setting['is_paystack_enabled'] == 'on')
                || (isset($admin_payments_setting['is_flutterwave_enabled']) && $admin_payments_setting['is_flutterwave_enabled'] == 'on')
                || (isset($admin_payments_setting['is_razorpay_enabled']) && $admin_payments_setting['is_razorpay_enabled'] == 'on')
                || (isset($admin_payments_setting['is_mercado_enabled']) && $admin_payments_setting['is_mercado_enabled'] == 'on')
            ){
                $validation                 = [];
                $validation['name']         = 'required|unique:plans';
                $validation['price']        = 'required|numeric|min:0';
                $validation['duration']     = 'required';
                $validation['max_stores']   = 'required|numeric';
                $validation['max_products'] = 'required|numeric';

                if($request->image)
                {
                    $validation['image'] = 'required|mimes:jpeg,png,jpg,gif,svg,pdf,doc|max:20480';
                }

                $request->validate($validation);
                $post = $request->all();
                if($request->hasFile('image'))
                {
                    $filenameWithExt = $request->file('image')->getClientOriginalName();
                    $filename        = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                    $extension       = $request->file('image')->getClientOriginalExtension();
                    $fileNameToStore = 'plan_' . time() . '.' . $extension;

                    $dir = storage_path('uploads/plan/');
                    if(!file_exists($dir))
                    {
                        mkdir($dir, 0777, true);
                    }
                    $path          = $request->file('image')->storeAs('uploads/plan/', $fileNameToStore);
                    $post['image'] = $fileNameToStore;
                }

                if(!isset($request->enable_custdomain))
                {
                    $post['enable_custdomain'] = 'off';
                }


                if(!isset($request->enable_custsubdom2ain))
                {
                    $post['enable_custsubdomain'] = 'off';
                }
                if(!isset($request->additional_page))
                {
                    $post['additional_page'] = 'off';
                }
                if(!isset($request->blog))
                {
                    $post['blog'] = 'off';
                }

                if(Plan::create($post))
                {
                    return redirect()->back()->with('success', __('Plan created Successfully!'));
                }
                else
                {
                    return redirect()->back()->with('error', __('Something is wrong'));
                }
            }
            else
            {
                return redirect()->back()->with('error', __('Please set stripe/paypal api key & secret key for add new plan'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Plan $plan
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Plan $plan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Plan $plan
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($plan_id)
    {
        if(\Auth::user()->type == 'super admin')
        {
            $arrDuration = Plan::$arrDuration;
            $plan        = Plan::find($plan_id);

            return view('plans.edit', compact('plan', 'arrDuration'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Plan $plan
     *
     * @return \Illuminate\Http\Response
     */
    public function update($planID, Request $request)
    {
        if(\Auth::user()->type == 'super admin')
        {
            $admin_payments_setting = Utility::getAdminPaymentSetting();
            if((isset($admin_payments_setting['is_stripe_enabled']) && $admin_payments_setting['is_stripe_enabled'] == 'on')
            || (isset($admin_payments_setting['is_paypal_enabled']) && $admin_payments_setting['is_paypal_enabled'] == 'on')
            || (isset($admin_payments_setting['is_paystack_enabled']) && $admin_payments_setting['is_paystack_enabled'] == 'on')
            || (isset($admin_payments_setting['is_flutterwave_enabled']) && $admin_payments_setting['is_flutterwave_enabled'] == 'on')
            || (isset($admin_payments_setting['is_razorpay_enabled']) && $admin_payments_setting['is_razorpay_enabled'] == 'on')
            || (isset($admin_payments_setting['is_mercado_enabled']) && $admin_payments_setting['is_mercado_enabled'] == 'on')
            ){
                $plan = Plan::find($planID);
                if($plan)
                {
                    if($plan->price > 0)
                    {
                        $validator = \Validator::make(
                            $request->all(), [
                                               'name' => 'required|unique:plans,name,' . $planID,
                                               'price' => 'required|numeric|min:0',
                                               'duration' => 'required',
                                               'max_stores' => 'required|numeric',
                                               'max_products' => 'required|numeric',
                                           ]
                        );
                    }
                    else
                    {
                        $validator = \Validator::make(
                            $request->all(), [
                                               'name' => 'required|unique:plans,name,' . $planID,
                                               'duration' => 'required',
                                               'max_stores' => 'required|numeric',
                                               'max_products' => 'required|numeric',
                                               'image' => 'mimes:jpeg,png,jpg,gif,svg,pdf,doc|max:20480',
                                           ]
                        );
                    }
                    {

                    }
                    if($validator->fails())
                    {
                        $messages = $validator->getMessageBag();

                        return redirect()->back()->with('error', $messages->first());
                    }

                    $post = $request->all();
                    if(!isset($request->enable_custdomain))
                    {
                        $post['enable_custdomain'] = 'off';
                    }
                    if(!isset($request->enable_custsubdomain))
                    {
                        $post['enable_custsubdomain'] = 'off';
                    }
                    if(!isset($request->additional_page))
                    {
                        $post['additional_page'] = 'off';
                    }
                    if(!isset($request->blog))
                    {
                        $post['blog'] = 'off';
                    }
                    if(!isset($request->shipping_method))
                    {
                        $post['shipping_method'] = 'off';
                    }
                    if($request->hasFile('image'))
                    {
                        $filenameWithExt = $request->file('image')->getClientOriginalName();
                        $filename        = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                        $extension       = $request->file('image')->getClientOriginalExtension();
                        $fileNameToStore = 'plan_' . time() . '.' . $extension;

                        $dir = storage_path('uploads/plan/');
                        if(!file_exists($dir))
                        {
                            mkdir($dir, 0777, true);
                        }
                        $image_path = $dir . '/' . $plan->image;  // Value is not URL but directory file path
                        if(\File::exists($image_path))
                        {
                            chmod($image_path, 0755);
                            \File::delete($image_path);
                        }

                        $path          = $request->file('image')->storeAs('uploads/plan/', $fileNameToStore);
                        $post['image'] = $fileNameToStore;
                    }

                    if($plan->update($post))
                    {
                        return redirect()->back()->with('success', __('Plan updated Successfully!'));
                    }
                    else
                    {
                        return redirect()->back()->with('error', __('Something is wrong'));
                    }
                }
                else
                {
                    return redirect()->back()->with('error', __('Plan not found'));
                }
            }
            else
            {
                return redirect()->back()->with('error', __('Please set stripe/paypal api key & secret key for add new plan'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Plan $plan
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($planID)
    {

    }

    public function userPlan(Request $request)
    {
        $objUser = \Auth::user();
        $planID  = \Illuminate\Support\Facades\Crypt::decrypt($request->code);
        $plan    = Plan::find($planID);
        if($plan)
        {
            if($plan->monthly_price <= 0)
            {
                $objUser->assignPlan($plan->id);

                return redirect()->route('plans.index')->with('success', __('Plan activated Successfully!'));
            }
            else
            {
                return redirect()->back()->with('error', __('Something is wrong'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Plan not found'));
        }
    }

    public function payment($code)
    {
        $planID = \Illuminate\Support\Facades\Crypt::decrypt($code);
        $plan   = Plan::find($planID);
        if($plan)
        {
            return view('plans.payment', compact('plan'));
        }
        else
        {
            return redirect()->back()->with('error', __('Plan is deleted.'));
        }
    }

    public function planPrepareAmount(Request $request){

        $plan = Plan::find(\Illuminate\Support\Facades\Crypt::decrypt($request->plan_id));

        if($plan)
        {
            $original_price = number_format($plan->price);
            $coupons        = Coupon::where('code', strtoupper($request->coupon))->where('is_active', '1')->first();
            $coupon_id = null;
            if(!empty($coupons))
            {
                $usedCoupun = $coupons->used_coupon();
                if($coupons->limit == $usedCoupun)
                {
                }
                else
                {
                    $discount_value = ($plan->price / 100) * $coupons->discount;
                    $plan_price     = $plan->price - $discount_value;
                    $price          = $plan->price - $discount_value;
                    $discount_value = '-' . $discount_value;
                    $coupon_id = $coupons->id;
                    return response()->json(
                        [
                            'is_success' => true,
                            'discount_price' => $discount_value,
                            'final_price' => $price,
                            'price' => $plan_price,
                            'coupon_id'=>$coupon_id,
                            'message' => __('Coupon code has applied successfully.'),
                        ]
                    );
                }
            }
            else
            {
                return response()->json(
                    [
                        'is_success' => true,
                        'final_price' => $original_price,
                        'coupon_id'=>$coupon_id,
                        'price' => $plan->price,
                    ]
                );
            }
        }
    }
    
}
