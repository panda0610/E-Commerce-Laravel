<?php

namespace App\Http\Controllers;


use App\Models\Coupon;
use App\Models\Order;
use App\Models\Plan;
use App\Models\PlanOrder;
use App\Models\Product;
use App\Models\User;
use App\Models\ProductVariantOption;
use App\Models\Shipping;
use App\Models\Store;
use App\Models\UserCoupon;
use App\Models\Utility;
use App\Models\PurchasedProducts;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Stripe;

class StripePaymentController extends Controller
{
    public $settings;

    public function index()
    {
        $objUser = \Auth::user();
        if($objUser->type == 'super admin')
        {
            $orders = Order::select(
                [
                    'orders.*',
                    'users.name as user_name',
                ]
            )->join('users', 'orders.user_id', '=', 'users.id')->orderBy('orders.created_at', 'DESC')->get();
        }
        else
        {
            $orders = Order::select(
                [
                    'orders.*',
                    'users.name as user_name',
                ]
            )->join('users', 'orders.user_id', '=', 'users.id')->orderBy('orders.created_at', 'DESC')->where('users.id', '=', $objUser->id)->get();
        }

        return view('order.index', compact('orders'));
    }

    public function stripe($code)
    {
        $plan_id = \Illuminate\Support\Facades\Crypt::decrypt($code);
        $plan    = Plan::find($plan_id);
        if($plan)
        {
            $admin_payments_details = Utility::getAdminPaymentSetting();

            return view('plans/stripe', compact('plan', 'admin_payments_details'));
        }
        else
        {
            return redirect()->back()->with('error', __('Plan is deleted.'));
        }
    }

    public function stripePost(Request $request, $slug)
    {

        $cart     = session()->get($slug);
        $products = $cart['products'];
         $cust_details = $cart['customer'];


        if(isset($cart['coupon']))
        {
            $coupon = $cart['coupon']['coupon'];

        }
        else
        {
            $coupon = [];
        }

        $store        = Store::where('slug', $slug)->first();
        $user_details = $cart['customer'];

        $store_payment_setting = Utility::getPaymentSetting($store->id);

        $objUser = \Auth::user();

        $total        = 0;
        $sub_tax      = 0;
        $sub_total    = 0;
        $total_tax    = 0;
        $product_name = [];
        $product_id   = [];

        foreach($products as $key => $product)
        {
            if($product['variant_id'] != 0)
            {
                $new_qty                = $product['originalvariantquantity'] - $product['quantity'];
                $product_edit           = ProductVariantOption::find($product['variant_id']);
                $product_edit->quantity = $new_qty;
                $product_edit->save();

                $product_name[] = $product['product_name'];
                $product_id[]   = $key;
                $quantity[]     = $product['quantity'];


                foreach($product['tax'] as $tax)
                {
                    $sub_tax   = ($product['variant_price'] * $product['quantity'] * $tax['tax']) / 100;
                    $total_tax += $sub_tax;
                    $pro_tax[] = $sub_tax;
                }
                $totalprice = $product['variant_price'] * $product['quantity'] + $total_tax;
                $subtotal   = $product['variant_price'] * $product['quantity'];
                $sub_total  += $subtotal;
                $total      += $totalprice;
            }
            else
            {
                $new_qty                = $product['originalquantity'] - $product['quantity'];
                $product_edit           = Product::find($product['product_id']);
                $product_edit->quantity = $new_qty;
                $product_edit->save();

                $product_name[] = $product['product_name'];
                $product_id[]   = $key;
                $quantity[]     = $product['quantity'];


                foreach($product['tax'] as $tax)
                {
                    $sub_tax   = ($product['price'] * $product['quantity'] * $tax['tax']) / 100;
                    $total_tax += $sub_tax;
                    $pro_tax[] = $sub_tax;
                }
                $totalprice = $product['price'] * $product['quantity'] + $total_tax;
                $subtotal   = $product['price'] * $product['quantity'];
                $sub_total  += $subtotal;
                $total      += $totalprice;
            }
        }

        $coupon_id = null;
        $price     = $total + $total_tax;

        if($products)
        {
            try
            {
                if(isset($cart['coupon']))
                {
                    if($cart['coupon']['coupon']['enable_flat'] == 'off')
                    {
                        $discount_value = ($price / 100) * $cart['coupon']['coupon']['discount'];
                        $price          = $price - $discount_value;
                    }
                    else
                    {
                        $discount_value = $cart['coupon']['coupon']['flat_discount'];
                        $price          = $price - $discount_value;
                    }
                }
                $price = number_format($total, 2);
                if(isset($cart['shipping']) && isset($cart['shipping']['shipping_id']) && !empty($cart['shipping']))
                {
                    $shipping = Shipping::find($cart['shipping']['shipping_id']);
                    if(!empty($shipping))
                    {
                        $shipping_name  = $shipping->name;
                        $shipping_price = $shipping->price;

                        $shipping_data = json_encode(
                            [
                                'shipping_name' => $shipping_name,
                                'shipping_price' => $shipping_price,
                                'location_id' => $cart['shipping']['location_id'],
                            ]
                        );

                        $price = $price + $shipping->price;
                    }
                    else
                    {
                        $shipping_data = '';
                    }
                }

                $orderID = strtoupper(str_replace('.', '', uniqid('', true)));

                if($price > 0.0)
                {
                    Stripe\Stripe::setApiKey($store_payment_setting['stripe_secret']);
                    $data = Stripe\Charge::create(
                        [
                            "amount" => 100 * $price,
                            "currency" => $store->currency_code,
                            "source" => $request->stripeToken,
                            "description" => " Stripe payment of order - " . $orderID,
                            "metadata" => ["order_id" => $orderID],
                        ]
                    );
                }
                else
                {
                    $data['amount_refunded'] = 0;
                    $data['failure_code']    = '';
                    $data['paid']            = 1;
                    $data['captured']        = 1;
                    $data['status']          = 'succeeded';
                }

                if($data['amount_refunded'] == 0 && empty($data['failure_code']) && $data['paid'] == 1 && $data['captured'] == 1)
                {
                    $customer= Auth::guard('customers')->user();
                    $order = Order::create(
                        [
                            'order_id' => $orderID,
                            'name' => $request->name,
                            'email'=> $cust_details['email'],
                            'card_number' => isset($data['payment_method_details']['card']['last4']) ? $data['payment_method_details']['card']['last4'] : '',
                            'card_exp_month' => isset($data['payment_method_details']['card']['exp_month']) ? $data['payment_method_details']['card']['exp_month'] : '',
                            'card_exp_year' => isset($data['payment_method_details']['card']['exp_year']) ? $data['payment_method_details']['card']['exp_year'] : '',
                            'status' => 'pending',
                            'user_address_id' => $user_details['id'],
                            'shipping_data' => !empty($shipping_data) ? $shipping_data : '',
                            'coupon' => !empty($cart['coupon']['coupon']['id']) ? $cart['coupon']['coupon']['id'] : '',
                            'coupon_json' => json_encode($coupon),
                            'discount_price' => !empty($cart['coupon']['discount_price']) ? $cart['coupon']['discount_price'] : '',
                            'price' => $price,
                            'product' => json_encode($products),
                            'price_currency' => $store->currency,
                            'txn_id' => isset($data['balance_transaction']) ? $data['balance_transaction'] : '',
                            'payment_type' => __('STRIPE'),
                            'payment_status' => isset($data['status']) ? $data['status'] : 'succeeded',
                            'receipt' => isset($data['receipt_url']) ? $data['receipt_url'] : 'free coupon',
                            'user_id' => $store['id'],
                            'customer_id' => $customer->id,
                        ]
                    );


                    foreach($products as $product_id)
                    {
                        $purchased_products = new PurchasedProducts();
                        $purchased_products->product_id  = $product_id['product_id'];
                        $purchased_products->customer_id = $customer->id;
                        $purchased_products->order_id   = $order->id;
                        $purchased_products->save();
                    }
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
                    return redirect()->route(
                        'store-complete.complete', [
                                                     $store->slug,
                                                     Crypt::encrypt($order->id),
                                                 ]
                    )->with('success', __('Transaction has been success'));
                }
                else
                {
                    return redirect()->back()->with('error', __('Transaction has been failed.'));
                }
            }
            catch(\Exception $e)
            {

                return redirect()->back()->with('error', __($e->getMessage()));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Plan is deleted.'));
        }
    }

    public function addPayment(Request $request)
    {
        $objUser               = \Auth::user();
        $planID                = \Illuminate\Support\Facades\Crypt::decrypt($request->plan_id);
        $plan                  = Plan::find($planID);
        $admin_payment_setting = Utility::getAdminPaymentSetting();
        if($plan)
        {
            try
            {
                $price = $plan->price;
                if(!empty($request->coupon))
                {
                    $coupons = Coupon::where('code', strtoupper($request->coupon))->where('is_active', '1')->first();
                    if(!empty($coupons))
                    {
                        $usedCoupun     = $coupons->used_coupon();
                        $discount_value = ($plan->price / 100) * $coupons->discount;
                        $price          = $plan->price - $discount_value;

                        if($coupons->limit == $usedCoupun)
                        {
                            return redirect()->back()->with('error', __('This coupon code has expired.'));
                        }
                    }
                    else
                    {
                        return redirect()->back()->with('error', __('This coupon code is invalid or has expired.'));
                    }
                }

                $orderID = strtoupper(str_replace('.', '', uniqid('', true)));

                if($price > 0.0)
                {
                    Stripe\Stripe::setApiKey($admin_payment_setting['stripe_secret']);
                    $data = Stripe\Charge::create(
                        [
                            "amount" => 100 * $price,
                            "currency" => env('CURRENCY'),
                            "source" => $request->stripeToken,
                            "description" => " Plan - " . $plan->name,
                            "metadata" => ["order_id" => $orderID],
                        ]
                    );
                }
                else
                {
                    $data['amount_refunded'] = 0;
                    $data['failure_code']    = '';
                    $data['paid']            = 1;
                    $data['captured']        = 1;
                    $data['status']          = 'succeeded';


                }
                $shipping_data = '';
                if(isset($cart['shipping']) && isset($cart['shipping']['shipping_id']) && !empty($cart['shipping']))
                {
                    $shipping = Shipping::find($cart['shipping']['shipping_id']);
                    if(!empty($shipping))
                    {
                        $shipping_name  = $shipping->name;
                        $shipping_price = $shipping->price;

                        $shipping_data = json_encode(
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
                }

                if($data['amount_refunded'] == 0 && empty($data['failure_code']) && $data['paid'] == 1 && $data['captured'] == 1)
                {

                    PlanOrder::create(
                        [
                            'order_id' => $orderID,
                            'name' => $request->name,
                            'card_number' => isset($data['payment_method_details']['card']['last4']) ? $data['payment_method_details']['card']['last4'] : '',
                            'card_exp_month' => isset($data['payment_method_details']['card']['exp_month']) ? $data['payment_method_details']['card']['exp_month'] : '',
                            'card_exp_year' => isset($data['payment_method_details']['card']['exp_year']) ? $data['payment_method_details']['card']['exp_year'] : '',
                            'plan_name' => $plan->name,
                            'plan_id' => $plan->id,
                            'shipping_data' => !empty($shipping_data) ? $shipping_data : '',
                            'price' => $price,
                            'price_currency' => env('CURRENCY'),
                            'txn_id' => isset($data['balance_transaction']) ? $data['balance_transaction'] : '',
                            'payment_type' => __('STRIPE'),
                            'payment_status' => isset($data['status']) ? $data['status'] : 'succeeded',
                            'receipt' => isset($data['receipt_url']) ? $data['receipt_url'] : 'free coupon',
                            'user_id' => $objUser->id,
                        ]
                    );

                    if(!empty($request->coupon))
                    {
                        $userCoupon         = new UserCoupon();
                        $userCoupon->user   = $objUser->id;
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
                    if($data['status'] == 'succeeded')
                    {
                        $assignPlan = $objUser->assignPlan($plan->id);
                        if($assignPlan['is_success'])
                        {
                            return redirect()->route('plans.index')->with('success', __('Plan successfully activated.'));
                        }
                        else
                        {
                            return redirect()->route('plans.index')->with('error', __($assignPlan['error']));
                        }
                    }
                    else
                    {
                        return redirect()->route('plans.index')->with('error', __('Your payment has failed.'));
                    }
                }
                else
                {
                    return redirect()->route('plans.index')->with('error', __('Transaction has been failed.'));
                }
            }
            catch(\Exception $e)
            {
                return redirect()->route('plans.index')->with('error', __($e->getMessage()));
            }
        }
        else
        {
            return redirect()->route('plans.index')->with('error', __('Plan is deleted.'));
        }
    }
}
