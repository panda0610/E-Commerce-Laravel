<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\Order;
use App\Models\Plan;
use App\Models\PlanOrder;
use App\Models\Product;
use App\Models\ProductVariantOption;
use App\Models\Shipping;
use App\Models\Store;
use App\Models\UserCoupon;
use App\Models\UserDetail;
use App\Models\User;
use App\Models\UserStore;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use PayPal\Api\Amount;
use PayPal\Api\Item;
use App\Models\Customer;
use App\Models\PurchasedProducts;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;

class PaypalController extends Controller
{
    private $_api_context;

    public function setApiContext($slug = '')
    {

        if(Auth::check() && Auth::guard('customers')->check() == false)
        {

            $admin_payment_setting           = Utility::getAdminPaymentSetting();
            $paypal_conf['settings']['mode'] = $admin_payment_setting['paypal_mode'];
            $paypal_conf['client_id']        = $admin_payment_setting['paypal_client_id'];
            $paypal_conf['secret_key']       = $admin_payment_setting['paypal_secret_key'];
        }
        else
        {

            $store                           = Store::where('slug', $slug)->first();
            $store_payment_setting           = Utility::getPaymentSetting($store->id);
            $paypal_conf['settings']['mode'] = $store_payment_setting['paypal_mode'];
            $paypal_conf['client_id']        = $store_payment_setting['paypal_client_id'];
            $paypal_conf['secret_key']       = $store_payment_setting['paypal_secret_key'];
        }


        $this->_api_context = new ApiContext(new OAuthTokenCredential($paypal_conf['client_id'], $paypal_conf['secret_key']));
        $this->_api_context->setConfig($paypal_conf['settings']);

        return $this;

    }

    public function PayWithPaypal(Request $request, $slug)
    {


        $cart     = session()->get($slug);
        $products = $cart['products'];

        $store   = Store::where('slug', $slug)->first();
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

                $product_name[] = $product['product_name'];
                $product_id[]   = $key;

                foreach($product['tax'] as $tax)
                {
                    $sub_tax   = ($product['variant_price'] * $product['quantity'] * $tax['tax']) / 100;
                    $total_tax += $sub_tax;
                }
                $totalprice = $product['variant_price'] * $product['quantity'];
                $total      += $totalprice;
            }
            else
            {
                $product_name[] = $product['product_name'];
                $product_id[]   = $key;

                foreach($product['tax'] as $tax)
                {
                    $sub_tax   = ($product['price'] * $product['quantity'] * $tax['tax']) / 100;
                    $total_tax += $sub_tax;
                }
                $totalprice = $product['price'] * $product['quantity'];
                $total      += $totalprice;
            }
        }

        if($products)
        {
            try
            {
                $coupon_id = null;
                $price     = $total + $total_tax;
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

                if(isset($cart['shipping']) && isset($cart['shipping']['shipping_id']) && !empty($cart['shipping']))
                {
                    $shipping = Shipping::find($cart['shipping']['shipping_id']);
                    if(!empty($shipping))
                    {
                        $price = $price + $shipping->price;
                    }
                }

                $this->setApiContext($slug);
                $name  = implode(',', $product_name);
                $payer = new Payer();
                $payer->setPaymentMethod('paypal');
                $item_1 = new Item();
                $item_1->setName($name)->setCurrency($store->currency_code)->setQuantity(1)->setPrice($price);
                $item_list = new ItemList();
                $item_list->setItems([$item_1]);
                $amount = new Amount();
                $amount->setCurrency($store->currency_code)->setTotal($price);
                $transaction = new Transaction();
                $transaction->setAmount($amount)->setItemList($item_list)->setDescription($name);
                $redirect_urls = new RedirectUrls();
                $redirect_urls->setReturnUrl(
                    route('get.payment.status', $store->slug)
                )->setCancelUrl(
                    route('get.payment.status', $store->slug)
                );

                $payment = new Payment();
                $payment->setIntent('Sale')->setPayer($payer)->setRedirectUrls($redirect_urls)->setTransactions([$transaction]);

                try
                {
                    $payment->create($this->_api_context);
                }
                catch(\PayPal\Exception\PayPalConnectionException $ex) //PPConnectionException
                {
                    return redirect()->back()->with('error', $ex->getMessage());
                }
                foreach($payment->getLinks() as $link)
                {
                    if($link->getRel() == 'approval_url')
                    {
                        $redirect_url = $link->getHref();
                        break;
                    }
                }
                Session::put('paypal_payment_id', $payment->getId());
                if(isset($redirect_url))
                {
                    return Redirect::away($redirect_url);
                }

                return redirect()->back()->with('error', __('Unknown error occurred'));
            }
            catch(\Exception $e)
            {
                return redirect()->back()->with('error', __('Unknown error occurred'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('is deleted.'));
        }
    }

    public function GetPaymentStatus(Request $request, $slug)
    {
        
        $cart = session()->get($slug);
        if(isset($cart['coupon']))
        {
            $coupon = $cart['coupon']['coupon'];
        }
        $products     = $cart['products'];
        $store        = Store::where('slug', $slug)->first();
        $user_details = $cart['customer'];

        $total        = 0;
        $new_qty      = 0;
        $sub_total    = 0;
        $total_tax    = 0;
        $product_name = [];
        $product_id   = [];
        $quantity     = [];
        $pro_tax      = [];

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
        $user = Auth::user();

        if($product)
        {
            $this->setApiContext($slug);
            $payment_id = Session::get('paypal_payment_id');
            Session::forget('paypal_payment_id');
            if(empty($request->PayerID || empty($request->token)))
            {
                return redirect()->route('store-payment.payment', $slug)->with('error', __('Payment failed'));
            }
            $payment   = Payment::get($payment_id, $this->_api_context);
            $execution = new PaymentExecution();
            $execution->setPayerId($request->PayerID);
            try
            {
                $result = $payment->execute($execution, $this->_api_context)->toArray();

                $order          = new Order();
                $order->user_id = Auth()->id();
                $latestOrder    = Order::orderBy('created_at', 'DESC')->first();
                if(!empty($latestOrder))
                {
                    $order->order_nr = '#' . str_pad($latestOrder->id + 1, 4, "100", STR_PAD_LEFT);
                }
                else
                {
                    $order->order_nr = '#' . str_pad(1, 4, "100", STR_PAD_LEFT);

                }
                $orderID = $order->order_nr;
                $status  = ucwords(str_replace('_', ' ', $result['state']));
                if($result['state'] == 'approved')
                {   
                    $customer               = Auth::guard('customers')->user();
                    $order                  = new Order();
                    $order->order_id        = $orderID;
                    $order->name            = $user_details['name'];
                    $order->email            = $user_details['email'];
                    $order->card_number     = '';
                    $order->card_exp_month  = '';
                    $order->card_exp_year   = '';
                    $order->status          = 'pending';
                    $order->user_address_id = $user_details['id'];
                    $order->shipping_data   = !empty($shipping_data) ? $shipping_data : '';
                    $order->coupon          = !empty($cart['coupon']['coupon']['id']) ? $cart['coupon']['coupon']['id'] : '';
                    $order->coupon_json     = json_encode(!empty($coupon) ? $coupon : '');
                    $order->discount_price  = !empty($cart['coupon']['discount_price']) ? $cart['coupon']['discount_price'] : '';
                    $order->price           = $result['transactions'][0]['amount']['total'];
                    $order->product         = json_encode($products);
                    $order->price_currency  = $store->currency_code;
                    $order->txn_id          = $payment_id;
                    $order->payment_type    = __('PAYPAL');
                    $order->payment_status  = $result['state'];
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
                    )->with('success', __('Transaction has been') . $status);
                    
                    
                }
                else
                {
                    return redirect()->back()->with('error', __('Transaction has been') . $status);
                }
            }
            catch(\Exception $e)
            {
                return redirect()->back()->with('error', __('Transaction has been failed.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __(' is deleted.'));
        }
    }

    public function planPayWithPaypal(Request $request)
    {

        $planID = \Illuminate\Support\Facades\Crypt::decrypt($request->plan_id);

        $plan   = Plan::find($planID);

        if($plan)
        {
            try
            {
                $coupon_id = null;
                $price     = $plan->price;
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
                        $coupon_id = $coupons->id;
                    }
                    else
                    {
                        return redirect()->back()->with('error', __('This coupon code is invalid or has expired.'));
                    }
                }
                $this->setApiContext();
                $name  = $plan->name;
                $payer = new Payer();
                $payer->setPaymentMethod('paypal');
                $item_1 = new Item();
                $item_1->setName($name)->setCurrency(env('CURRENCY'))->setQuantity(1)->setPrice($price);
                $item_list = new ItemList();
                $item_list->setItems([$item_1]);
                $amount = new Amount();
                $amount->setCurrency(env('CURRENCY'))->setTotal($price);
                $transaction = new Transaction();
                $transaction->setAmount($amount)->setItemList($item_list)->setDescription($name);
                $redirect_urls = new RedirectUrls();
                $redirect_urls->setReturnUrl(
                    route(
                        'get.store.payment.status', [
                                                      $plan->id,
                                                      'coupon_id' => $coupon_id,
                                                  ]
                    )
                )->setCancelUrl(
                    route(
                        'get.store.payment.status', [
                                                      $plan->id,
                                                      'coupon_id' => $coupon_id,
                                                  ]
                    )
                );
                $payment = new Payment();
                $payment->setIntent('Sale')->setPayer($payer)->setRedirectUrls($redirect_urls)->setTransactions([$transaction]);

                try
                {
                    $payment->create($this->_api_context);
                    
                }
                catch(\PayPal\Exception\PayPalConnectionException $ex) //PPConnectionException
                {
                    if(config('app.debug'))
                    {
                        return redirect()->route('stripe', \Illuminate\Support\Facades\Crypt::encrypt($plan->id))->with('error', __('Connection timeout'));
                    }
                    else
                    {
                        return redirect()->route('stripe', \Illuminate\Support\Facades\Crypt::encrypt($plan->id))->with('error', __('Some error occur, sorry for inconvenient'));
                    }
                }
                foreach($payment->getLinks() as $link)
                {
                    if($link->getRel() == 'approval_url')
                    {
                        $redirect_url = $link->getHref();
                        break;
                    }
                }
                Session::put('paypal_payment_id', $payment->getId());
                if(isset($redirect_url))
                {
                    return Redirect::away($redirect_url);
                }

                return redirect()->route('payment', \Illuminate\Support\Facades\Crypt::encrypt($plan->id))->with('error', __('Unknown error occurred'));
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

    public function storeGetPaymentStatus(Request $request, $plan_id)
    {
        $user     = Auth::user();
        $store_id = Auth::user()->current_store;

        $plan = Plan::find($plan_id);

        if($plan)
        {
            $this->setApiContext();
            $payment_id = Session::get('paypal_payment_id');
            Session::forget('paypal_payment_id');
            if(empty($request->PayerID || empty($request->token)))
            {
                return redirect()->route('payment', \Illuminate\Support\Facades\Crypt::encrypt($plan->id))->with('error', __('Payment failed'));
            }
            $payment   = Payment::get($payment_id, $this->_api_context);
            $execution = new PaymentExecution();
            $execution->setPayerId($request->PayerID);
            try
            {
                $result  = $payment->execute($execution, $this->_api_context)->toArray();
                $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
                $status  = ucwords(str_replace('_', ' ', $result['state']));
                if($request->has('coupon_id') && $request->coupon_id != '')
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
                }

                if($result['state'] == 'approved')
                {

                    $planorder                 = new PlanOrder();
                    $planorder->order_id       = $orderID;
                    $planorder->name           = $user->name;
                    $planorder->card_number    = '';
                    $planorder->card_exp_month = '';
                    $planorder->card_exp_year  = '';
                    $planorder->plan_name      = $plan->name;
                    $planorder->plan_id        = $plan->id;
                    $planorder->price          = $result['transactions'][0]['amount']['total'];
                    $planorder->price_currency = env('CURRENCY');
                    $planorder->txn_id         = $payment_id;
                    $planorder->payment_type   = __('PAYPAL');
                    $planorder->payment_status = $result['state'];
                    $planorder->receipt        = '';
                    $planorder->user_id        = $user->id;
                    $planorder->store_id       = $store_id;
                    $planorder->save();

                    $assignPlan = $user->assignPlan($plan->id);

                    if($assignPlan['is_success'])
                    {


                        return redirect()->route('plans.index')->with('success', __('Plan activated Successfully.'));
                    }
                    else
                    {


                        return redirect()->route('plans.index')->with('error', $assignPlan['error']);
                    }
                }
                else
                {
                    return redirect()->route('plans.index')->with('error', __('Transaction has been') . $status);
                }
            }
            catch(\Exception $e)
            {
                return redirect()->route('plans.index')->with('error', __('Transaction has been failed.'));
            }
        }
        else
        {
            return redirect()->route('plans.index')->with('error', __('Plan is deleted.'));
        }
    }
}
