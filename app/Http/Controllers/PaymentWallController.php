<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductCoupon;
use App\Models\ProductVariantOption;
use App\Models\PurchasedProducts;
use App\Models\Shipping;
use App\Models\Store;
use App\Models\Plan;
use App\Models\PlanOrder;
use App\Models\User;
use App\Models\Utility;
use App\Models\InvoicePayment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserCoupon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;

class PaymentWallController extends Controller
{

	public function planpay(Request $request)
    {
    	$data=$request->all();
    	 $admin_payment_setting = Utility::getAdminPaymentSetting();

    	return view('plans.planpay',compact('data','admin_payment_setting'));

    }

    public function planerror(Request $request,$flag)
    {
          if($flag == 1){
            return redirect()->route("plans.index")->with('success', __('Plan activated Successfully! '));
        }else{
                return redirect()->route("plans.index")->with('error', __('Transaction has been failed! '));
        }

    }

   public function planPayWithPaymentWall(Request $request,$plan_id){

       $user                  = Auth::user();
       $store_id              = Auth::user()->current_store;
        $admin_payment_setting = Utility::getAdminPaymentSetting();
        $this->planpaymentSetting();



        $planID         = \Illuminate\Support\Facades\Crypt::decrypt($plan_id);
        $plan           = Plan::find($planID);

        $authuser       = Auth::user();
        $coupon_id ='';
        if($plan)
        {

            /* Check for code usage */
            $plan->discounted_price = false;
            $price                  = $plan->price;


            if($price >= 0)
            {


                \Paymentwall_Config::getInstance()->set(array(

                                                            'private_key' => $admin_payment_setting['paymentwall_private_key']
                                                        ));

                $parameters = $request->all();

                $chargeInfo = array(
                    'email' => $parameters['email'],
                    'history[registration_date]' => '1489655092',
                    'amount' => $price,
                    'currency' => !empty($this->currancy) ? $this->currancy : 'USD',
                    'token' => $parameters['brick_token'],
                    'fingerprint' => $parameters['brick_fingerprint'],
                    'description' => 'Order #123'
                );

                $charge = new \Paymentwall_Charge();
                $charge->create($chargeInfo);
                $responseData = json_decode($charge->getRawResponseData(),true);
                $response = $charge->getPublicData();

                if ($charge->isSuccessful() AND empty($responseData['secure'])) {
                    if ($charge->isCaptured()) {
                        if(isset($request->coupon) && !empty($request->coupon))
                        {
                            $coupons = Coupon::find($request->coupon_id);
                            if(!empty($coupons))
                            {
                                $userCoupon         = new UserCoupon();
                                $userCoupon->user   = $user->id;
                                $userCoupon->coupon = $coupons->id;
                                $userCoupon->order  = $orderID;
                                $userCoupon->save();
                                $usedCoupun = $coupons->used_coupon();
                                if($coupons->limit <= $usedCoupun)
                                {
                                    $coupons->is_active = 0;
                                    $coupons->save();

                                }

                            }
                            else
                            {
                                $res['flag'] = 2;
                                return $res;
                            }
                        }

                        $planorder                 = new PlanOrder();
                        $planorder->order_id       = $orderID;
                        $planorder->name           = $user->name;
                        $planorder->card_number    = '';
                        $planorder->card_exp_month = '';
                        $planorder->card_exp_year  = '';
                        $planorder->plan_name      = $plan->name;
                        $planorder->plan_id        = $plan->id;
                        $planorder->price          = $price;
                        $planorder->price_currency = env('CURRENCY');
                        $planorder->txn_id         = isset($paydata['txid']) ? $paydata['txid'] : 0;
                        $planorder->payment_type   = __('Flutterwave ');
                        $planorder->payment_status = 'success';
                        $planorder->receipt        = '';
                        $planorder->user_id        = $user->id;
                        $planorder->store_id       = $store_id;
                        $planorder->save();

                        $assignPlan = $user->assignPlan($plan->id);

                        if($assignPlan['is_success'])
                        {

                            $res['flag'] = 1;
                            return $res;

                        }
                    } elseif ($charge->isUnderReview()) {
                        $res['flag'] = 2;
                        return $res;
                    }
                } elseif (!empty($responseData['secure'])) {
                    $response = json_encode(array('secure' => $responseData['secure']));
                } else {
                    $errors = json_decode($response, true);
                    $res['msg'] = __("Trasnsaction has been Fail.");

                    $res['flag'] = 2;
                    return $res;
                }
            }
            else
            {
                $res['flag'] = 2;
                return $res;

            }

            $res['flag'] = 2;
            return $res;
        }
        else
        {
            $res['flag'] = 2;
            return $res;
        }
    }

    public function orderindex(Request $request,$slug)
    {
       
        $data=$request->all();
        $store    = Store::where('slug', $slug)->first();
        $admin_payment_setting = Utility::getAdminPaymentSetting($store->id);
        return view('storefront.paymentwall',compact('data','admin_payment_setting','store','slug'));

    }

    public function orderpaymenterror(Request $request,$flag,$slug)
    {

        if($flag == 1){
            return redirect()->route('store.slug',$slug)->with('error', __('Transaction has been Successfull! '));
        }else{
            return redirect()->route('store.slug',$slug)->with('error', __('Transaction has been failed! '));
        }

    }

    public function orderPayWithPaymentwall(Request $request,$slug){
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
            $res['flag'] = 2;
            $res['slug'] = $slug;
            return $res;
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

        if(isset($cart['shipping']) && isset($cart['shipping']['shipping_id']) && !empty($cart['shipping']))
        {
            $shipping = Shipping::find($cart['shipping']['shipping_id']);

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
        else
        {
            $shipping_data = '';
        }


        if($products)
        {

            $result = array();
            //The parameter after verify/ is the transaction reference to be verified
            \Paymentwall_Config::getInstance()->set(array(
                                                       'private_key' => $store_payment_setting['paymentwall_private_key']
                                                   ));
            $parameters = $_POST;
            $chargeInfo = array(
                'email' => $parameters['email'],
                'history[registration_date]' => '1489655092',
                'amount' => $totalprice,
                'currency' => !empty($store->currency_code) ? $store->currency_code : 'USD',
                'token' => $parameters['brick_token'],
                'fingerprint' => $parameters['brick_fingerprint'],
                'description' => 'Order #123'
            );

            $charge = new \Paymentwall_Charge();
            $charge->create($chargeInfo);
            $responseData = json_decode($charge->getRawResponseData(),true);
            $response = $charge->getPublicData();

            if ($charge->isSuccessful() AND empty($responseData['secure'])) {
                if ($charge->isCaptured()) {

                    $customer               = Auth::guard('customers')->user();
                    $order                  = new Order();
                    $order->order_id        = time();
                    $order->name            = $cust_details['name'];
                    $order->email           = $cust_details['email'];
                    $order->card_number     = '';
                    $order->card_exp_month  = '';
                    $order->card_exp_year   = '';
                    $order->status          = 'pending';
                    $order->user_address_id = $cust_details['id'];
                    $order->shipping_data   = $shipping_data;
                    $order->product_id      = implode(',', $product_id);
                    $order->price           = $totalprice;
                    $order->coupon          = isset($cart['coupon']['data_id']) ? $cart['coupon']['data_id'] : '';
                    $order->coupon_json     = json_encode($coupon);
                    $order->discount_price  = isset($cart['coupon']['discount_price']) ? $cart['coupon']['discount_price'] : '';
                    $order->product         = json_encode($products);
                    $order->price_currency  = $store->currency_code;
                    $order->txn_id          = isset($tran_id) ? $tran_id : '';
                    $order->payment_type    = 'flutterwave';
                    $order->payment_status  = 'success';
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
                    $msg = redirect()->route(
                        'store-complete.complete', [
                                                     $store->slug,
                                                     Crypt::encrypt($order->id),
                                                 ]
                    )->with('success', __('Transaction has been success'));

                    session()->forget($slug);
                    $order_email = $order->email;
                    $order_id    = Crypt::encrypt($order->id);

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

                    $res['flag'] = 1;
                    $res['slug'] = $slug;
                    $res['order_id'] = Crypt::encrypt($order->id);
                    return $res;

                }
                elseif($charge->isUnderReview()) {
                    $res['flag'] = 2;
                    $res['slug'] = $slug;
                    return $res;
                }
            }else {
                $res['flag'] = 2;
                $res['slug'] = $slug;
                return $res;
            }
        }
        else
        {
            $res['flag'] = 2;
            $res['slug'] = $slug;
            return $res;
        }
    }




    public function planpaymentSetting()
    {
        $payment_setting = Utility::getAdminPaymentSetting();

        $this->currancy = !empty(env('CURRENCY')) ? env('CURRENCY') : 'USD';

        $this->secret_key = isset($payment_setting['paymentwall_private_key'])?$payment_setting['paymentwall_private_key']:'';
        $this->public_key = isset($payment_setting['paymentwall_public_key'])?$payment_setting['paymentwall_public_key']:'';
        $this->is_enabled = isset($payment_setting['is_paymentwall_enabled'])?$payment_setting['is_paymentwall_enabled']:'off';
        return $this;
    }
}
