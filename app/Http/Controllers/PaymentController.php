<?php

namespace App\Http\Controllers;

use Anand\LaravelPaytmWallet\Facades\PaytmWallet;
use App\Models\Coupon;
use App\Mail\OrderMail;
use App\Models\Order;
use App\Models\Plan;
use App\Models\PlanOrder;
use App\Models\Product;
use App\Models\ProductCoupon;
use App\Models\ProductVariantOption;
use App\Models\Shipping;
use App\Models\Store;
use App\Models\UserCoupon;
use App\Models\User;
use App\Models\Utility;
use CoinGate\CoinGate;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Mollie\Laravel\Facades\Mollie;
use Obydul\LaraSkrill\SkrillClient;
use Obydul\LaraSkrill\SkrillRequest;
use App\Models\PurchasedProducts;

class PaymentController extends Controller
{
    //paystackPayment
    public function paystackPayment($slug, $code, $order_id)
    {
        $store    = Store::where('slug', $slug)->first();
        $products = '';
        $cart     = session()->get($slug);
        if(\Auth::check() && Utility::CustomerAuthCheck($slug) == false)
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
            $url = "https://api.paystack.co/transaction/verify/$code";
            $ch  = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt(
                $ch, CURLOPT_HTTPHEADER, [
                       'Authorization: Bearer ' . $store_payment_setting['paystack_secret_key'],
                   ]
            );
            $request = curl_exec($ch);
            curl_close($ch);
            if($request)
            {
                $result = json_decode($request, true);
            }

            if(array_key_exists('data', $result) && array_key_exists('status', $result['data']) && ($result['data']['status'] === 'success'))
            {

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
                $order->txn_id          = isset($result['data']['id']) ? $result['data']['id'] : '';
                $order->payment_type    = 'paystack';
                $order->payment_status  = isset($result['data']['status']) ? $result['data']['status'] : 'succeeded';
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
                return redirect()->back()->with('error', __('Transaction Unsuccesfull'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Plan is deleted.'));
        }
    }

    //FlutterwavePayment
    public function flutterwavePayment($slug, $tran_id, $order_id)
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

            $data = array(
                'txref' => $tran_id,
                'SECKEY' => $store_payment_setting['flutterwave_secret_key'],
                //secret key from pay button generated on rave dashboard
            );

            // make request to endpoint using unirest.
            $headers = array('Content-Type' => 'application/json');
            $body    = \Unirest\Request\Body::json($data);
            $url     = "https://api.ravepay.co/flwv3-pug/getpaidx/api/v2/verify"; //please make sure to change this to production url when you go live

            // Make `POST` request and handle response with unirest
            $response = \Unirest\Request::post($url, $headers, $body);


            if($response->body->data->status === "successful" && $response->body->data->chargecode === "00")
            {

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

                return $msg;

            }
            else
            {
                return redirect()->back()->with('error', __('Transaction Unsuccesfull'));

            }

        }
        else
        {
            return redirect()->back()->with('error', __('Plan is deleted.'));
        }
    }

    //RazerPayment
    public function razerpayPayment($slug, $pay_id, $order_id)
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

            $ch = curl_init('https://api.razorpay.com/v1/payments/' . $pay_id . '');
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
            curl_setopt($ch, CURLOPT_USERPWD, $store_payment_setting['razorpay_public_key'] . ':' . $store_payment_setting['razorpay_secret_key']); // Input your Razorpay Key Id and Secret Id here
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = json_decode(curl_exec($ch));
            // check that payment is authorized by razorpay or not

            if($response->status == 'authorized')
            {
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
                $order->txn_id          = isset($pay_id) ? $pay_id : '';
                $order->payment_type    = 'razerpay';
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
                return redirect()->back()->with('error', __('Transaction Unsuccesfull'));

            }

        }
        else
        {
            return redirect()->back()->with('error', __('Plan is deleted.'));
        }
    }

    //Mercado Pago Prepare Payment
    public function mercadopagoPayment($slug, Request $request)
    {
        $cart     = session()->get($slug);
        $products = $cart['products'];

        $store = Store::where('slug', $slug)->first();

        if(\Auth::check())
        {
            $store_payment_setting = Utility::getPaymentSetting();
        }
        else
        {
            $store_payment_setting = Utility::getPaymentSetting($store->id);
        }

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
            if(isset($cart['customer']) && !empty($cart['customer']))
            {

                $pdata['phone']   = isset($cart['customer']['phone']) ? $cart['customer']['phone'] : '';
                $pdata['email']   = isset($cart['customer']['email']) ? $cart['customer']['email'] : '';
                $pdata['user_id'] = isset($cart['customer']['id']) ? $cart['customer']['id'] : '';
            }
            else
            {
                $pdata['phone']   = '';
                $pdata['email']   = '';
                $pdata['user_id'] = '';
            }


            $head_array = $request->headers;

            $request = $request->all();

            \MercadoPago\SDK::setAccessToken($store_payment_setting['mercado_access_token']);
            try
            {
                // Create a preference object
                $preference = new \MercadoPago\Preference();
                // Create an item in the preference
                $item              = new \MercadoPago\Item();
                $item->title       = $store->name . "Order";
                $item->quantity    = 1;
                $item->unit_price  = (float)$price;
                $preference->items = array($item);
                //                $coupons_id = $request->input('coupon_id');
                $success_url = route(
                    'mercado.callback', [
                                          $slug,
                                          'flag' => 'success',
                                      ]
                );
                $failure_url = route(
                    'mercado.callback', [
                                          $slug,
                                          'flag' => 'failure',
                                      ]
                );
                $pending_url = route(
                    'mercado.callback', [
                                          $slug,
                                          'flag' => 'pending',
                                      ]
                );

                $preference->back_urls = array(
                    "success" => $success_url,
                    "failure" => $failure_url,
                    "pending" => $pending_url,
                );

                $preference->auto_return = "approved";
                $preference->save();

                // Create a customer object
                $payer = new \MercadoPago\Payer();
                // Create payer information
                $payer->name    = $pdata['user_id'];
                $payer->email   = $pdata['email'];
                $payer->address = array(
                    "street_name" => '',
                );
                if($store_payment_setting['mercado_mode'] == 'live')
                {
                    $redirectUrl = $preference->init_point;
                }
                else
                {
                    $redirectUrl = $preference->sandbox_init_point;
                }

                return response()->json(
                    [
                        'status' => 'success',
                        'url' => $redirectUrl,
                    ]
                );
            }
            catch(Exception $e)
            {
                return redirect()->back()->with('error', $e->getMessage());
            }

        }
        else
        {
            return redirect()->back()->with('error', __('is deleted.'));
        }
    }

    //Mercado Pago
    public function mercadopagoCallback($slug, Request $request)
    {
        if(!empty($slug))
        {
            $store        = Store::where('slug', $slug)->first();
            $products     = '';
            $cart         = session()->get($slug);

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

            if($request->has('status'))
            {

                if($request->status == 'approved' && $request->flag == 'success')
                {
                    if($products)
                    {
                        $customer               = Auth::guard('customers')->user();
                        $order                  = new Order();
                        $order->order_id        = '#' . time();
                        $order->name            = $cust_details['name'];
                        $order->email           = $cust_details['email'];
                        $order->card_number     = '';
                        $order->card_exp_month  = '';
                        $order->card_exp_year   = '';
                        $order->status          = 'success';
                        $order->user_address_id = $cust_details['id'];
                        $order->shipping_data   = $shipping_data;
                        $order->product_id      = implode(',', $product_id);
                        $order->price           = $totalprice;
                        $order->coupon          = isset($cart['coupon']['data_id']) ? $cart['coupon']['data_id'] : '';
                        $order->coupon_json     = json_encode($coupon);
                        $order->discount_price  = isset($cart['coupon']['discount_price']) ? $cart['coupon']['discount_price'] : '';
                        $order->product         = json_encode($products);
                        $order->price_currency  = $store->currency_code;
                        $order->txn_id          = $request->has('preference_id') ? $request->preference_id : '';
                        $order->payment_type    = 'mercadopago';
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

                        $owner=User::find($store->created_by);
                        $owner_email=$owner->email;

                        $order_id    = Crypt::encrypt($order->id);

                        if(isset($store->mail_driver) && !empty($store->mail_driver))
                        {
                            $dArr = [
                                'order_name' => $order->name,
                            ];
                            $resp1=Utility::sendEmailTemplate('Order Created For Owner', $owner_email, $dArr, $store, $order_id);


                            $resp = Utility::sendEmailTemplate('Order Created', $order_email, $dArr, $store, $order_id);

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
                        return redirect()->back()->with('error', __('Transaction Unsuccessful.'));
                    }
                }
                else
                {
                    session()->flash('error', 'Transaction Unsuccessful');

                    return redirect('/');
                }
            }
            else
            {
                session()->flash('error', 'Transaction Unsuccessful');

                return redirect('/');
            }
        }
        else
        {
            session()->flash('error', 'Transaction Unsuccessful');

            return redirect('/');
        }
    }

    //Paytm Prepare payment
    public function paytmOrder($slug, Request $request)
    {

        $cart     = session()->get($slug);
        $products = $cart['products'];

        $store   = Store::where('slug', $slug)->first();
        $objUser = \Auth::user();

        if(\Auth::check())
        {
            $store_payment_setting = Utility::getPaymentSetting();
        }
        else
        {
            $store_payment_setting = Utility::getPaymentSetting($store->id);
        }
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

            if(Utility::CustomerAuthCheck($slug))
            {
                $customer_data     = Auth::guard('customers')->user();
                $pdata['phone']   = $customer_data->phone_number;
                $pdata['email']   = $customer_data->email;
                $pdata['user_id'] = $customer_data->id;
            }
            else
            {
                $pdata['phone']   = '';
                $pdata['email']   = '';
                $pdata['user_id'] = '';
            }

            config(
                [
                    'services.paytm-wallet.env' => $store_payment_setting['paytm_mode'],
                    'services.paytm-wallet.merchant_id' => $store_payment_setting['paytm_merchant_id'],
                    'services.paytm-wallet.merchant_key' => $store_payment_setting['paytm_merchant_key'],
                    'services.paytm-wallet.merchant_website' => 'WEBSTAGING',
                    'services.paytm-wallet.channel' => 'WEB',
                    'services.paytm-wallet.industry_type' => $store_payment_setting['paytm_industry_type'],
                ]
            );
            $payment = PaytmWallet::with('receive');

            $payment->prepare(
                [
                    'order' => date('Y-m-d') . '-' . strtotime(date('Y-m-d H:i:s')),
                    'user' => $pdata['user_id'],
                    'mobile_number' => $pdata['phone'],
                    'email' => $pdata['email'],
                    'amount' => $price,
                    'callback_url' => route('paytm.callback', 'store=' . $slug),
                ]
            );

            return $payment->receive();

        }
        else
        {
            return redirect()->back()->with('error', __('is deleted.'));
        }
    }

    //Paytm Prepare payment
    public function paytmCallback(Request $request)
    {

        $slug     = $request->store;
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
            config(
                [
                    'services.paytm-wallet.env' => $store_payment_setting['paytm_mode'],
                    'services.paytm-wallet.merchant_id' => $store_payment_setting['paytm_merchant_id'],
                    'services.paytm-wallet.merchant_key' => $store_payment_setting['paytm_merchant_key'],
                    'services.paytm-wallet.merchant_website' => 'WEBSTAGING',
                    'services.paytm-wallet.channel' => 'WEB',
                    'services.paytm-wallet.industry_type' => $store_payment_setting['paytm_industry_type'],
                ]
            );

            $transaction = PaytmWallet::with('receive');

            $response = $transaction->response();
            if($transaction->isSuccessful())
            {
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
                $order->txn_id          = $response['TXNID'];
                $order->payment_type    = 'paytm';
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

                return $msg;

            }
            else if($transaction->isFailed())
            {
                return redirect()->back()->with('error', __('Transaction Unsuccesfull'));
            }
            else if($transaction->isOpen())
            {
                //Transaction Open/Processing
                return redirect('/');
            }
            else
            {
                return redirect()->back()->with('error', __('Payment not made'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Plan is deleted.'));
        }
    }

    //Mollie Prepare payment
    public function mollieOrder($slug, Request $request)
    {
        $cart     = session()->get($slug);
        $products = $cart['products'];

        $store   = Store::where('slug', $slug)->first();
        $objUser = \Auth::user();
        if(\Auth::check())
        {
            $store_payment_setting = Utility::getPaymentSetting();
        }
        else
        {
            $store_payment_setting = Utility::getPaymentSetting($store->id);
        }
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
            if(Utility::CustomerAuthCheck($slug))
            {
                $customer_data     = Auth::guard('customers')->user();
                $pdata['phone']   = $customer_data->phone_number;
                $pdata['email']   = $customer_data->email;
                $pdata['user_id'] = $customer_data->id;
            }
            else
            {
                $pdata['phone']   = '';
                $pdata['email']   = '';
                $pdata['user_id'] = '';
            }
            $request = $request->all();
            $mollie  = new \Mollie\Api\MollieApiClient();
            $mollie->setApiKey($store_payment_setting['mollie_api_key']);
            //var_dump(intval($request['amount']));

            $payment = $mollie->payments->create(
                [
                    "amount" => [
                        "currency" => "$store->currency_code",
                        "value" => number_format($price, 2),
                    ],
                    "description" => "payment for product",
                    "redirectUrl" => route(
                        'mollie.callback', [
                                             $store->slug,
                                             $request['desc'],
                                         ]
                    ),

                ]
            );

            session()->put('mollie_payment_id', $payment->id);

            return redirect($payment->getCheckoutUrl())->with('payment_id', $payment->id);

        }
        else
        {
            return redirect()->back()->with('error', __('is deleted.'));
        }
    }

    //Mollie Callback payment
    public function mollieCallback($slug, $order_id, Request $request)
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
            $mollie = new \Mollie\Api\MollieApiClient();
            $mollie->setApiKey($store_payment_setting['mollie_api_key']);

            if(session()->has('mollie_payment_id'))
            {
                $payment = $mollie->payments->get(session()->get('mollie_payment_id'));

                if($payment->isPaid())
                {
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
                    $order->txn_id          = isset($pay_id) ? $pay_id : '';
                    $order->payment_type    = 'mollie';
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
                    return redirect()->back()->with('error', __('Transaction Unsuccesfull'));
                }
            }
            else
            {
                session()->flash('warning', 'Payment not made!');

                return redirect('/');
            }

        }
        else
        {
            return redirect()->back()->with('error', __('Plan is deleted.'));
        }
    }

    //skrillPayment Prepare payment
    public function skrillPayment($slug, Request $request)
    {
        $cart     = session()->get($slug);
        $products = $cart['products'];

        $store   = Store::where('slug', $slug)->first();
        $objUser = \Auth::user();
        if(\Auth::check())
        {
            $store_payment_setting = Utility::getPaymentSetting();
        }
        else
        {
            $store_payment_setting = Utility::getPaymentSetting($store->id);
        }

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
            if(Utility::CustomerAuthCheck($slug))
            {
                $customer_data     = Auth::guard('customers')->user();
                $pdata['phone']   = $customer_data->phone_number;
                $pdata['email']   = $customer_data->email;
                $pdata['user_id'] = $customer_data->id;
            }
            else
            {
                $pdata['phone']   = '';
                $pdata['email']   = '';
                $pdata['user_id'] = '';
            }


            $head_array = $request->headers;

            $request = $request->all();
            if(!empty($store->logo))
            {
                $logo = asset(Storage::url('uploads/store_logo/' . $store->logo));
            }
            else
            {

                $logo = asset(Storage::url('uploads/store_logo/logo-dark.png'));
            }

            $skill               = new SkrillRequest();
            $skill->pay_to_email = $store_payment_setting['skrill_email'];
            $skill->return_url   = route('skrill.callback') . '?transaction_id=' . MD5($request['transaction_id']);
            $skill->cancel_url   = route('skrill.callback');
            $skill->logo_url     = $logo;

            // create object instance of SkrillRequest
            $skill->transaction_id  = MD5($request['transaction_id']); // generate transaction id
            $skill->amount          = $price;
            $skill->currency        = $store->currency_code;
            $skill->language        = 'EN';
            $skill->prepare_only    = '1';
            $skill->merchant_fields = 'site_name, customer_email';
            $skill->site_name       = $store->name;
            $skill->customer_email  = $pdata['email'];

            // create object instance of SkrillClient
            $client = new SkrillClient($skill);
            $sid    = $client->generateSID(); //return SESSION ID

            // handle error
            $jsonSID = json_decode($sid);
            if($jsonSID != null && $jsonSID->code == "BAD_REQUEST")
            {
                return redirect()->back()->with('error', $jsonSID->message);
            }


            // do the payment
            $redirectUrl = $client->paymentRedirectUrl($sid); //return redirect url
            if($request['transaction_id'])
            {
                $data = [
                    'amount' => $price,
                    'trans_id' => MD5($request['transaction_id']),
                    'currency' => $store->currency_code,
                    'slug' => $store->slug,
                ];
                session()->put('skrill_data', $data);

            }

            return redirect($redirectUrl);
        }
        else
        {
            return redirect()->back()->with('error', __('is deleted.'));
        }


    }

    //skrillPayment Callback payment
    public function skrillCallback(Request $request)
    {
        if(session()->has('skrill_data'))
        {
            $get_data = session()->get('skrill_data');
            $slug     = $get_data['slug'];
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
            if(isset($cart['shipping']) && isset($cart['shipping']['shipping_id']) && !empty($cart['shipping']))
            {
                $shipping       = Shipping::find($cart['shipping']['shipping_id']);
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
                $order->txn_id          = $request->has('transaction_id') ? $request->transaction_id : '';
                $order->payment_type    = 'skrill';
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

                return $msg;
            }
            else
            {
                return redirect()->back()->with('error', __('Transaction Unsuccessful.'));
            }
        }
        else
        {
            session()->flash('error', 'Transaction Unsuccessful');

            return redirect('/');

        }
    }

    //Coingate Pago Prepare Payment
    public function coingatePayment($slug, Request $request)
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
        $product_name    = [];
        $product_id      = [];
        $tax_name        = [];
        $totalprice      = 0;
        $preference_data = [];
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
            $shipping       = Shipping::find($cart['shipping']['shipping_id']);
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
        $order_id = $request->order_id;
        if($products)
        {
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
            $order->txn_id          = isset($pay_id) ? $pay_id : '';
            $order->payment_type    = 'coingate';
            $order->payment_status  = 'pendding';
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
            try
            {

                CoinGate::config(
                    array(
                        'environment' => $store_payment_setting['coingate_mode'],
                        // sandbox OR live
                        'auth_token' => $store_payment_setting['coingate_auth_token'],
                        'curlopt_ssl_verifypeer' => FALSE
                        // default is false
                    )
                );

                $post_params = array(
                    'order_id' => $order->id,
                    'price_amount' => $totalprice,
                    'price_currency' => $store['currency_code'],
                    'receive_currency' => $store['currency_code'],
                    'callback_url' => url('coingate/callback') . '?slug=' . $store->slug . '&order_id=' . $order->id,
                    'cancel_url' => url('/'),
                    'success_url' => route(
                        'store-complete.complete', [
                                                     $store->slug,
                                                     Crypt::encrypt($order->id),
                                                 ]
                    ),
                    'title' => 'Order #' . time(),
                );
                $order_email=$order->email;
                $order_name=$order->name;

                $order       = \CoinGate\Merchant\Order::create($post_params);

                if($order)
                {

                    session()->forget($slug);


                    $owner=User::find($store->created_by);

                    $owner_email=$owner->email;
                    $order_id    = Crypt::encrypt($order->id);

                    if(isset($store->mail_driver) && !empty($store->mail_driver))
                    {
                        $dArr = [
                            'order_name' => $order_name,
                        ];

                        $resp = Utility::sendEmailTemplate('Order Created', $order_email, $dArr, $store, $order_id);

                        $resp1=Utility::sendEmailTemplate('Order Created For Owner', $owner_email, $dArr, $store, $order_id);


                    }
                    if(isset($store->is_twilio_enabled) && $store->is_twilio_enabled=="on")
                    {
                         Utility::order_create_owner($order,$owner,$store);
                         Utility::order_create_customer($order,$customer,$store);
                    }
                    return redirect($order->payment_url);
                }
                else
                {

                    return redirect()->back()->with('error', __('opps something wren wrong.'));
                }


            }
            catch(Exception $e)
            {

                return redirect()->back()->with('error', $e->getMessage());
            }
            $msg = redirect()->route(
                'store-complete.complete', [
                                             $store->slug,
                                             Crypt::encrypt($order->id),
                                         ]
            )->with('success', __('Transaction has been success'));

            session()->forget($slug);



            return $msg;
        }
        else
        {
            return redirect()->back()->with('error', __('Transaction Unsuccesfull'));
        }

    }

    //Coingate Pago
    public function coingateCallback(Request $request)
    {

        if($request->has('order_id'))
        {
            $order = Order::where('id', $request->order_id)->first();
            if($order)
            {
                $order->payment_status = 'succuss';
                $order->save();
            }
        }
    }


    //Plan

    // Plan purchase  Payments methods
    public function paystackPlanGetPayment($code, $pid, Request $request)
    {
        $user                  = Auth::user();
        $store_id              = Auth::user()->current_store;
        $admin_payment_setting = Utility::getAdminPaymentSetting();
        $plan_id=\Illuminate\Support\Facades\Crypt::decrypt($pid);

        $plan              = Plan::find(\Illuminate\Support\Facades\Crypt::decrypt($pid));


        //dd($plan->name);

        if($plan)
        {

            try
            {
                $orderID = strtoupper(str_replace('.', '', uniqid('', true)));

                $result = array();
                //The parameter after verify/ is the transaction reference to be verified
                $url = "https://api.paystack.co/transaction/verify/$code";
                $ch  = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt(
                    $ch, CURLOPT_HTTPHEADER, [
                           'Authorization: Bearer ' . $admin_payment_setting['paystack_secret_key'],
                       ]
                );
                $responce = curl_exec($ch);
                curl_close($ch);
                if($responce)
                {
                    $result = json_decode($responce, true);
                }
                if(array_key_exists('data', $result) && array_key_exists('status', $result['data']) && ($result['data']['status'] === 'success'))
                {
                    $status = $result['data']['status'];
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
                    $planorder                 = new PlanOrder();
                    $planorder->order_id       = $orderID;
                    $planorder->name           = $user->name;
                    $planorder->card_number    = '';
                    $planorder->card_exp_month = '';
                    $planorder->card_exp_year  = '';
                    $planorder->plan_name      = $plan->name;
                    $planorder->plan_id        = $plan->id;
                    $planorder->price          = $result['data']['amount'] / 100;
                    $planorder->price_currency = env('CURRENCY');
                    $planorder->txn_id         = $code;
                    $planorder->payment_type   = __('Paystack');
                    $planorder->payment_status = $result['data']['status'];
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
                    return redirect()->back()->with('error', __('Transaction Unsuccesfull'));
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

    // Plan flutterwave  Payments methods
    public function flutterwavePlanGetPayment($code, $plan_id, Request $request)
    {

        $user                  = Auth::user();
        $store_id              = Auth::user()->current_store;
        $admin_payment_setting = Utility::getAdminPaymentSetting();
        $plan_id               = \Illuminate\Support\Facades\Crypt::decrypt($plan_id);
        $plan                  = Plan::find($plan_id);

        if($plan)
        {
            $orderID = strtoupper(str_replace('.', '', uniqid('', true)));

            $data = array(
                'txref' => $code,
                'SECKEY' => $admin_payment_setting['flutterwave_secret_key'],
                //secret key from pay button generated on rave dashboard
            );

            // make request to endpoint using unirest.
            $headers = array('Content-Type' => 'application/json');
            $body    = \Unirest\Request\Body::json($data);
            $url     = "https://api.ravepay.co/flwv3-pug/getpaidx/api/v2/verify"; //please make sure to change this to production url when you go live

            // Make `POST` request and handle response with unirest
            $response = \Unirest\Request::post($url, $headers, $body);


            if($response->body->data->status === "successful" && $response->body->data->chargecode === "00")
            {

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
                $planorder                 = new PlanOrder();
                $planorder->order_id       = $orderID;
                $planorder->name           = $user->name;
                $planorder->card_number    = '';
                $planorder->card_exp_month = '';
                $planorder->card_exp_year  = '';
                $planorder->plan_name      = $plan->name;
                $planorder->plan_id        = $plan->id;
                $planorder->price          = $response->body->data->amount;
                $planorder->price_currency = env('CURRENCY');
                $planorder->txn_id         = $response->body->data->txid;
                $planorder->payment_type   = __('Flutterwave ');
                $planorder->payment_status = $response->body->data->status;
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
                return redirect()->back()->with('error', __('Transaction Unsuccesfull'));
            }
        }
        else
        {
            return redirect()->route('plans.index')->with('error', __('Plan is deleted.'));
        }
    }

    // Plan razorpay  Payments methods
    public function razorpayPlanGetPayment($pay_id, $plan_id, Request $request)
    {
        $user                  = Auth::user();
        $store_id              = Auth::user()->current_store;
        $admin_payment_setting = Utility::getAdminPaymentSetting();
        $plan_id               = \Illuminate\Support\Facades\Crypt::decrypt($plan_id);
        $plan                  = Plan::find($plan_id);

        if($plan)
        {

            try
            {
                $orderID = strtoupper(str_replace('.', '', uniqid('', true)));

                $result = array();
                //The parameter after verify/ is the transaction reference to be verified
                $ch = curl_init('https://api.razorpay.com/v1/payments/' . $pay_id . '');
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
                curl_setopt($ch, CURLOPT_USERPWD, $admin_payment_setting['razorpay_public_key'] . ':' . $admin_payment_setting['razorpay_secret_key']); // Input your Razorpay Key Id and Secret Id here
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $response = json_decode(curl_exec($ch));
                // check that payment is authorized by razorpay or not

                if($response->status == 'authorized')
                {

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
                    $planorder                 = new PlanOrder();
                    $planorder->order_id       = $orderID;
                    $planorder->name           = $user->name;
                    $planorder->card_number    = '';
                    $planorder->card_exp_month = '';
                    $planorder->card_exp_year  = '';
                    $planorder->plan_name      = $plan->name;
                    $planorder->plan_id        = $plan->id;
                    $planorder->price          = $response->amount / 100;
                    $planorder->price_currency = env('CURRENCY');
                    $planorder->txn_id         = $pay_id;
                    $planorder->payment_type   = __('Razorpay');
                    $planorder->payment_status = $response->status == 'authorized' ? 'success' : 'failed';
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
                    return redirect()->back()->with('error', __('Transaction Unsuccesfull'));
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

    // Mercado Plan PreparePayment
    public function mercadopagoPaymentPrepare(Request $request)
    {
        $validator = \Validator::make(
            $request->all(), [
                               'plan' => 'required',
                               'total_price' => 'required',
                           ]
        );
        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return response()->json(
                [
                    'status' => 'error',
                    'error' => $messages->first(),
                ]
            );
        }
        $plan = Plan::find($request->plan);
        if($plan)
        {
            $admin_payment_setting = Utility::getAdminPaymentSetting();

            \MercadoPago\SDK::setAccessToken($admin_payment_setting['mercado_access_token']);
            try
            {
                $amount = (float)$request->total_price;
                // Create a preference object
                $preference = new \MercadoPago\Preference();
                // Create an item in the preference
                $item              = new \MercadoPago\Item();
                $item->title       = "Plan : " . $plan->name;
                $item->quantity    = 1;
                $item->unit_price  = $amount;
                $preference->items = array($item);
                $coupons_id        = $request->input('coupon_id');
                $success_url       = route(
                    'plan.mercado.callback', [
                                               encrypt($request->plan),
                                               'coupon_id=' . $coupons_id,
                                               'flag' => 'success',
                                           ]
                );
                $failure_url       = route(
                    'plan.mercado.callback', [
                                               encrypt($request->plan),
                                               'flag' => 'failure',
                                           ]
                );
                $pending_url       = route(
                    'plan.mercado.callback', [
                                               encrypt($request->plan),
                                               'flag' => 'pending',
                                           ]
                );

                $preference->back_urls = array(
                    "success" => $success_url,
                    "failure" => $failure_url,
                    "pending" => $pending_url,
                );

                $preference->auto_return = "approved";
                $preference->save();

                // Create a customer object
                $payer = new \MercadoPago\Payer();
                // Create payer information
                $payer->name    = \Auth::user()->name;
                $payer->email   = \Auth::user()->email;
                $payer->address = array(
                    "street_name" => '',
                );
                if($admin_payment_setting['mercado_mode'] == 'live')
                {
                    $redirectUrl = $preference->init_point;
                }
                else
                {
                    $redirectUrl = $preference->sandbox_init_point;
                }

                return response()->json(
                    [
                        'status' => 'success',
                        'url' => $redirectUrl,
                    ]
                );
            }
            catch(Exception $e)
            {
                return response()->json(
                    [
                        'status' => 'error',
                        'error' => $e->getMessage(),
                    ]
                );
            }
        }

    }

    // Mercado mercadopagoPaymentCallback
    public function mercadopagoPaymentCallback($plan, Request $request)
    {
        $user                  = Auth::user();
        $store_id              = Auth::user()->current_store;
        $admin_payment_setting = Utility::getAdminPaymentSetting();
        $plan_id               = Crypt::decrypt($plan);
        $plan                  = Plan::find($plan_id);
        if($plan)
        {
            $orderID = time();
            if($plan && $request->has('status'))
            {
                $price = $plan->price;
                if($request->status == 'approved' && $request->flag == 'success')
                {
                    if($request->has('coupon_id') && $request->coupon_id != '')
                    {
                        $coupons = Coupon::find($request->coupon_id);
                        if(!empty($coupons))
                        {
                            $discount_value = ($price / 100) * $coupons->discount;

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
                            $price = $price - $discount_value;
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
                    $planorder->txn_id         = $request->has('preference_id') ? $request->preference_id : '';
                    $planorder->payment_type   = __('Mercado Pago');
                    $planorder->payment_status = 'success';
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
                    return redirect()->back()->with('error', __('Transaction Unsuccesfull'));
                }
            }
            else
            {
                return redirect()->back()->with('error', __('Transaction Unsuccesfull'));
            }

            session()->forget('mollie_payment_id');
        }
        else
        {
            return redirect()->route('plans.index')->with('error', __('Plan is deleted.'));
        }
    }

    // Paytm Plan PreparePayment
    public function paytmPaymentPrepare(Request $request)
    {
        $validator = \Validator::make(
            $request->all(), [
                               'plan_id' => 'required',
                               'total_price' => 'required',
                               'mobile_number' => 'required|numeric',
                           ]
        );
        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }
        $user    = Auth::user()->current_store;
        $store   = Store::where('id', $user)->first();
        $plan_id = decrypt($request->plan_id);
        $plan    = Plan::find($plan_id);

        if($plan)
        {
            $admin_payment_setting = Utility::getAdminPaymentSetting();
            $order                 = $request->all();
            config(
                [
                    'services.paytm-wallet.env' => $admin_payment_setting['paytm_mode'],
                    'services.paytm-wallet.merchant_id' => $admin_payment_setting['paytm_merchant_id'],
                    'services.paytm-wallet.merchant_key' => $admin_payment_setting['paytm_merchant_key'],
                    'services.paytm-wallet.merchant_website' => 'WEBSTAGING',
                    'services.paytm-wallet.channel' => 'WEB',
                    'services.paytm-wallet.industry_type' => $admin_payment_setting['paytm_industry_type'],
                ]
            );

            $payment = PaytmWallet::with('receive');

            $payment->prepare(
                [
                    'order' => $plan_id,
                    'user' => Auth::user()->id,
                    'mobile_number' => $request->mobile_number,
                    'email' => Auth::user()->email,
                    'amount' => $request->total_price,
                    'callback_url' => route('plan.paytm.callback', 'store=' . $store->slug),
                ]
            );

            return $payment->receive();

        }

    }

    public function paytmPlanGetPayment(Request $request)
    {
        // dd($request->all());
        $user                  = Auth::user();
        $store_id              = Auth::user()->current_store;
        $admin_payment_setting = Utility::getAdminPaymentSetting();
        $plan_id               = $request->ORDERID;
        $plan                  = Plan::find($plan_id);

        if($plan)
        {

            $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
            config(
                [
                    'services.paytm-wallet.env' => $admin_payment_setting['paytm_mode'],
                    'services.paytm-wallet.merchant_id' => $admin_payment_setting['paytm_merchant_id'],
                    'services.paytm-wallet.merchant_key' => $admin_payment_setting['paytm_merchant_key'],
                    'services.paytm-wallet.merchant_website' => 'WEBSTAGING',
                    'services.paytm-wallet.channel' => 'WEB',
                    'services.paytm-wallet.industry_type' => $admin_payment_setting['paytm_industry_type'],
                ]
            );
            $transaction = PaytmWallet::with('receive');

            // To get raw response as array
            $response = $transaction->response();

            if($transaction->isSuccessful())
            {

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
                $planorder                 = new PlanOrder();
                $planorder->order_id       = $orderID;
                $planorder->name           = $user->name;
                $planorder->card_number    = '';
                $planorder->card_exp_month = '';
                $planorder->card_exp_year  = '';
                $planorder->plan_name      = $plan->name;
                $planorder->plan_id        = $plan->id;
                $planorder->price          = $response['TXNAMOUNT'];
                $planorder->price_currency = env('CURRENCY');
                $planorder->txn_id         = $response['MID'];
                $planorder->payment_type   = __('Razorpay');
                $planorder->payment_status = 'success';
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
                return redirect()->back()->with('error', __('Transaction Unsuccesfull'));
            }

            session()->forget('mollie_payment_id');
        }
        else
        {
            return redirect()->route('plans.index')->with('error', __('Plan is deleted.'));
        }
    }

    // Mollie Plan PreparePayment
    public function molliePaymentPrepare(Request $request)
    {
        $validator = \Validator::make(
            $request->all(), [
                               'plan_id' => 'required',
                               'total_price' => 'required',
                           ]
        );
        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return response()->json(
                [
                    'status' => 'error',
                    'error' => $messages->first(),
                ]
            );
        }
        $user    = Auth::user()->current_store;
        $store   = Store::where('id', $user)->first();
        $plan_id = decrypt($request->plan_id);
        $plan    = Plan::find($plan_id);

        if($plan)
        {
            $admin_payment_setting = Utility::getAdminPaymentSetting();

            $mollie = new \Mollie\Api\MollieApiClient();
            $mollie->setApiKey($admin_payment_setting['mollie_api_key']);
            $value_price = str_replace(",","",number_format($request->total_price, 2));

            $payment = $mollie->payments->create(
                [
                    "amount" => [
                        "currency" => env('CURRENCY'),
                        "value" => $value_price,
                    ],
                    "description" => $plan->name,
                    "redirectUrl" => route(
                        'plan.mollie.callback', [
                                                  $store->slug,
                                                  $request->plan_id,
                                              ]
                    ),

                ]
            );
            session()->put('mollie_payment_id', $payment->id);

            return redirect($payment->getCheckoutUrl())->with('payment_id', $payment->id);

        }

    }

    public function molliePlanGetPayment(Request $request, $slug, $plan_id)
    {
        $user                  = Auth::user();
        $store_id              = Auth::user()->current_store;
        $admin_payment_setting = Utility::getAdminPaymentSetting();
        $plan_id               = \Illuminate\Support\Facades\Crypt::decrypt($plan_id);
        $plan                  = Plan::find($plan_id);

        if($plan)
        {
            try
            {
                $orderID = strtoupper(str_replace('.', '', uniqid('', true)));

                $mollie = new \Mollie\Api\MollieApiClient();
                $mollie->setApiKey($admin_payment_setting['mollie_api_key']);

                if(session()->has('mollie_payment_id'))
                {
                    $payment = $mollie->payments->get(session()->get('mollie_payment_id'));

                    if($payment->isPaid())
                    {
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
                        $planorder                 = new PlanOrder();
                        $planorder->order_id       = $orderID;
                        $planorder->name           = $user->name;
                        $planorder->card_number    = '';
                        $planorder->card_exp_month = '';
                        $planorder->card_exp_year  = '';
                        $planorder->plan_name      = $plan->name;
                        $planorder->plan_id        = $plan->id;
                        $planorder->price          = $payment->amount->value;
                        $planorder->price_currency = env('CURRENCY');
                        $planorder->txn_id         = $payment->id;
                        $planorder->payment_type   = __('Razorpay');
                        $planorder->payment_status = $payment->status == 'authorized' ? 'success' : 'failed';
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
                        return redirect()->back()->with('error', __('Transaction Unsuccesfull'));
                    }

                    session()->forget('mollie_payment_id');


                }
                else
                {
                    session()->flash('error', 'Transaction Error');

                    return redirect('/');
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

    // skrill Plan PreparePayment
    public function skrillPaymentPrepare(Request $request)
    {
        $validator = \Validator::make(
            $request->all(), [
                               'plan_id' => 'required',
                               'total_price' => 'required',
                           ]
        );
        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }
        $user    = Auth::user()->current_store;
        $store   = Store::where('id', $user)->first();
        $plan_id = decrypt($request->plan_id);
        $plan    = Plan::find($plan_id);
        $price   = $request->total_price;

        if($plan)
        {
            $admin_payment_setting = Utility::getAdminPaymentSetting();
            $order                 = $request->all();
            if(!empty($store->logo))
            {
                $logo = asset(Storage::url('uploads/store_logo/' . $store->logo));
            }
            else
            {
                $logo = asset(Storage::url('uploads/store_logo/logo-dark.png'));
            }

            $skill               = new SkrillRequest();
            $skill->pay_to_email = $admin_payment_setting['skrill_email'];
            $skill->return_url   = route('plan.skrill.callback') . '?transaction_id=' . MD5($request['transaction_id']);
            $skill->cancel_url   = route('plan.skrill.callback');
            $skill->logo_url     = $logo;

            // create object instance of SkrillRequest
            $skill->transaction_id  = MD5($request['transaction_id']); // generate transaction id
            $skill->amount          = $price;
            $skill->currency        = env('CURRENCY');
            $skill->language        = 'EN';
            $skill->prepare_only    = '1';
            $skill->merchant_fields = 'site_name, customer_email';
            $skill->site_name       = $store->name;
            $skill->customer_email  = Auth::user()->email;

            // create object instance of SkrillClient
            $client = new SkrillClient($skill);
            $sid    = $client->generateSID(); //return SESSION ID

            // handle error
            $jsonSID = json_decode($sid);
            if($jsonSID != null && $jsonSID->code == "BAD_REQUEST")
            {
                return redirect()->back()->with('error', $jsonSID->message);
            }


            // do the payment
            $redirectUrl = $client->paymentRedirectUrl($sid); //return redirect url
            if($request['transaction_id'])
            {
                $data = [
                    'amount' => $price,
                    'trans_id' => MD5($request['transaction_id']),
                    'currency' => $store->currency_code,
                    'slug' => $store->slug,
                ];
                session()->put('skrill_data', $data);

            }

            return redirect($redirectUrl);


        }

    }

    public function skrillPlanGetPayment(Request $request)
    {
        $user                  = Auth::user();
        $store_id              = Auth::user()->current_store;
        $admin_payment_setting = Utility::getAdminPaymentSetting();
        $plan_id               = $request->ORDERID;
        $plan                  = Plan::find($plan_id);

        if($plan)
        {

            if(session()->has('skrill_data'))
            {
                $get_data = session()->get('skrill_data');
                $orderID  = time();

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
                $planorder                 = new PlanOrder();
                $planorder->order_id       = $orderID;
                $planorder->name           = $user->name;
                $planorder->card_number    = '';
                $planorder->card_exp_month = '';
                $planorder->card_exp_year  = '';
                $planorder->plan_name      = $plan->name;
                $planorder->plan_id        = $plan->id;
                $planorder->price          = isset($get_data['amount']) ? $get_data['amount'] : 0;
                $planorder->price_currency = env('CURRENCY');
                $planorder->txn_id         = $request->has('transaction_id') ? $request->transaction_id : '';;
                $planorder->payment_type   = __('Skrill');
                $planorder->payment_status = 'success';
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
                return redirect()->back()->with('error', __('Transaction Unsuccesfull'));
            }

            session()->forget('mollie_payment_id');

        }
        else
        {
            return redirect()->route('plans.index')->with('error', __('Plan is deleted.'));
        }
    }

    //CoinGate
    public function coingatePaymentPrepare(Request $request)
    {

        $validator = \Validator::make(
            $request->all(), [
                               'plan_id' => 'required',
                               'total_price' => 'required',
                           ]
        );
        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }
        $user    = Auth::user()->current_store;
        $store   = Store::where('id', $user)->first();
        $plan_id = decrypt($request->plan_id);
        $plan    = Plan::find($plan_id);
        $price   = $request->total_price;

        if($plan)
        {
            $admin_payment_setting = Utility::getAdminPaymentSetting();
            $order                 = $request->all();
            CoinGate::config(
                array(
                    'environment' => $admin_payment_setting['coingate_mode'],
                    // sandbox OR live
                    'auth_token' => $admin_payment_setting['coingate_auth_token'],
                    'curlopt_ssl_verifypeer' => FALSE
                    // default is false
                )
            );
            $post_params = array(
                'order_id' => time(),
                'price_amount' => $price,
                'price_currency' => env('CURRENCY'),
                'receive_currency' => env('CURRENCY'),
                'callback_url' => url('coingate-payment-plan') . '?plan_id=' . $plan->id . '&user_id=' . Auth::user()->id,
                'cancel_url' => route('plans.index'),
                'success_url' => url('coingate-payment-plan') . '?plan_id=' . $plan->id . '&user_id=' . Auth::user()->id,
                'title' => 'Order #' . time(),
            );

            $order = \CoinGate\Merchant\Order::create($post_params);
            if($order)
            {
                return redirect($order->payment_url);
            }
            else
            {
                return redirect()->back()->with('error', __('opps something wren wrong.'));
            }
        }
    }

    public function coingatePlanGetPayment(Request $request)
    {
        $user                  = Auth::user();
        $plan_id               = $request->plan_id;
        $store_id              = Auth::user()->current_store;
        $admin_payment_setting = Utility::getAdminPaymentSetting();
        $plan                  = Plan::find($plan_id);

        if($plan)
        {
            try
            {
                $orderID = time();
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

                $planorder                 = new PlanOrder();
                $planorder->order_id       = $orderID;
                $planorder->name           = $user->name;
                $planorder->card_number    = '';
                $planorder->card_exp_month = '';
                $planorder->card_exp_year  = '';
                $planorder->plan_name      = $plan->name;
                $planorder->plan_id        = $plan->id;
                $planorder->price          = $plan->price;
                $planorder->price_currency = env('CURRENCY');
                $planorder->txn_id         = '-';
                $planorder->payment_type   = __('CoinGAte');
                $planorder->payment_status = 'success';
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
