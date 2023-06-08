<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Mail\OrderDeliveredMail;
use App\Mail\OrderMail;
use App\Models\Order;
use App\Exports\OrderExport;
use App\Models\Store;
use App\Models\UserDetail;
use App\Models\Utility;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Auth::user()->type == 'super admin') {
            $user  = \Auth::user();
            $store = Store::where('id', $user->current_store)->first();

            $orders = Order::orderBy('id', 'DESC')->get();
        } else {
            $user  = \Auth::user();
            $store = Store::where('id', $user->current_store)->first();

            $orders = Order::orderBy('id', 'DESC')->where('user_id', $store->id)->get();
        }

        return view('orders.index', compact('orders'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Order $order
     *
     * @return \Illuminate\Http\Response
     */
    public function show($order)
    {
        $id    = Crypt::decrypt($order);
        $order = Order::find($id);

        $store = Store::where('id', $order->user_id)->first();

        $user_details = UserDetail::where('id', $order->user_address_id)->first();

        if (!empty($order->shipping_data)) {
            $shipping_data = json_decode($order->shipping_data);
            $location_data = Location::where('id', $shipping_data->location_id)->first();
        } else {
            $shipping_data = '';
            $location_data = '';
        }

        $order_products = json_decode($order->product);
        $sub_total      = 0;
        if (!empty($order_products)) {
            $grand_total = 0;
            $total_taxs  = 0;
            foreach ($order_products as $product) {
                if (isset($product->variant_id) && $product->variant_id != 0) {
                    if (!empty($product->tax)) {
                        foreach ($product->tax as $tax) {
                            $sub_tax    = ($product->variant_price * $product->quantity * $tax->tax) / 100;
                            $total_taxs += $sub_tax;
                        }
                    }

                    $totalprice  = $product->variant_price * $product->quantity + $total_taxs;
                    $subtotal    = $product->variant_price * $product->quantity;
                    $sub_total   += $subtotal;
                    $grand_total += $totalprice;
                } else {
                    if (!empty($product->tax)) {
                        foreach ($product->tax as $tax) {
                            $sub_tax    = ($product->price * $product->quantity * $tax->tax) / 100;
                            $total_taxs += $sub_tax;
                        }
                    }

                    $totalprice  = $product->price * $product->quantity + $total_taxs;
                    $subtotal    = $product->price * $product->quantity;
                    $sub_total   += $subtotal;
                    $grand_total += $totalprice;
                }
            }
            if (!empty($order->coupon_json)) {
                $coupon = json_decode($order->coupon_json);
            }
            if (!empty($order->discount_price)) {
                $discount_price = $order->discount_price;
            } else {
                $discount_price = '';
            }
            $discount_value = 0;
            if (!empty($coupon)) {
                if ($coupon->enable_flat == 'on') {
                    $discount_value = $coupon->flat_discount;
                } else {
                    $discount_value = ($grand_total / 100) * $coupon->discount;
                }
            }
        }
        $store_payment_setting = \Utility::getPaymentSetting($store->id);
        $order_id              = Crypt::encrypt($order->id);


        return view('orders.view', compact('store_payment_setting', 'discount_price', 'order', 'store', 'grand_total', 'order_products', 'sub_total', 'total_taxs', 'user_details', 'order_id', 'shipping_data', 'location_data', 'discount_value'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Order $order
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Order $order
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Order $order)
    {
        $store = Store::where('id', $order->user_id)->first();

        $order['status'] = $request->delivered;
        $order->update();
        $order_email = $order->email;

        if (isset($store->mail_driver) && !empty($store->mail_driver)) {

            $dArr  = [
                'order_name' => $order['name'],
                'order_status' => $order['status']
            ];

            try
            {
                $resp  = Utility::sendEmailTemplate('Status Change', $order_email, $dArr, $store, $order);
            }
            catch(\Exception $e)
            {

            }

        }


        $order = Crypt::encrypt($order->id);
        if (isset($store->is_twilio_enabled) && $store->is_twilio_enabled == "on")
        {
            $order = Order::find(Crypt::decrypt($order));
            $customer = Customer::where('id', $order->customer_id)->first();
            Utility::status_change_customer($order, $customer, $store);
        }

        $return =  response()->json(
            [
                'success' => __('Successfully Updated.') . ((isset($smtp_error)) ? '<br> <span class="text-danger">' . $smtp_error . '</span>' : ''),
            ]
        );

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Order $order
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order)
    {
        $order->delete();

        return redirect()->back()->with(
            'success',
            __('Order Deleted!')
        );
    }

    public function receipt($id)
    {
        $order = Order::find($id);
        $store = Store::where('id', $order->user_id)->first();

        if (!empty($order->shipping_data)) {
            $shipping_data = json_decode($order->shipping_data);
            $location_data = Location::where('id', $shipping_data->location_id)->first();
        } else {
            $shipping_data = '';
            $location_data = '';
        }

        $user_details = UserDetail::where('id', $order->user_address_id)->first();

        $order_products = json_decode($order->product);
        $sub_total      = 0;
        if (!empty($order_products)) {
            $grand_total = 0;
            $total_taxs  = 0;
            foreach ($order_products as $k => $product) {
                if (isset($product->variant_id) && $product->variant_id != 0) {
                    if (!empty($product->tax)) {
                        foreach ($product->tax as $tax) {
                            $sub_tax    = ($product->variant_price * $product->quantity * $tax->tax) / 100;
                            $total_taxs += $sub_tax;
                        }
                    }
                    $totalprice  = $product->variant_price * $product->quantity + $total_taxs;
                    $subtotal    = $product->variant_price * $product->quantity;
                    $sub_total   += $subtotal;
                    $grand_total += $totalprice;
                } else {
                    if (!empty($product->tax)) {
                        foreach ($product->tax as $tax) {
                            $sub_tax    = ($product->price * $product->quantity * $tax->tax) / 100;
                            $total_taxs += $sub_tax;
                        }
                    }

                    $totalprice  = $product->price * $product->quantity + $total_taxs;
                    $subtotal    = $product->price * $product->quantity;
                    $sub_total   += $subtotal;
                    $grand_total += $totalprice;
                }
            }
        }
        $discount_value = 0;
        $plan_price     = 0;
        if (!empty($order->coupon_json)) {
            $coupons = json_decode($order->coupon_json);
            if (!empty($coupons)) {
                if ($coupons->enable_flat == 'on') {
                    $discount_value = $coupons->flat_discount;
                } else {
                    $discount_value = ($grand_total / 100) * $coupons->discount;
                }
            }

            $plan_price = $grand_total - $discount_value;
        }

        $order_id = Crypt::encrypt($order->id);

        return view('orders.receipt', compact('order', 'store', 'grand_total', 'order_products', 'sub_total', 'total_taxs', 'user_details', 'order_id', 'shipping_data', 'location_data', 'discount_value', 'plan_price'));
    }

    public function fileExport()
    {

        $name = 'Order_' . date('Y-m-d i:h:s');
        $data = Excel::download(new OrderExport(), $name . '.xlsx');


        return $data;
    }
}
