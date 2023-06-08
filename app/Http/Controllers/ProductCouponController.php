<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\Plan;
use App\Models\ProductCoupon;
use App\Exports\ProductCouponExport;
use App\Imports\ProductCouponImport;
use App\Models\Store;
use App\Models\UserCoupon;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use MongoDB\Driver\Session;
use function Cassandra\Type;
use Maatwebsite\Excel\Facades\Excel;

class ProductCouponController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        $productcoupons = ProductCoupon::where('store_id',$user->current_store)->get();

        return view('product-coupon.index', compact('productcoupons'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('product-coupon.create');
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
        $arrValidate = [
            'name' => 'required',
            'limit' => 'required|numeric',
            'code' => 'required',
        ];

        if($request->enable_flat == 'on')
        {
            $arrValidate['pro_flat_discount'] = 'required';
        }
        else
        {
            $arrValidate['discount'] = 'required';
        }
        $validator = \Validator::make(
            $request->all(), $arrValidate
        );
        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }

        $productcoupon              = new ProductCoupon();
        $productcoupon->name        = $request->name;
        $productcoupon->enable_flat = !empty($request->enable_flat) ? $request->enable_flat : 'off';
        if($request->enable_flat == 'on')
        {
            $productcoupon->flat_discount = $request->pro_flat_discount;
        }
        if(empty($request->enable_flat))
        {
            $productcoupon->discount = $request->discount;
        }
        $productcoupon->limit      = $request->limit;
        $productcoupon->code       = strtoupper($request->code);
        $productcoupon->store_id   = \Auth::user()->current_store;
        $productcoupon->created_by = \Auth::user()->creatorId();

        $productcoupon->save();

        return redirect()->route('product-coupon.index')->with('success', __('Coupon successfully created.'));
    }

    /**
     * Display the specified resource.
     *
     * @param \App\ProductCoupon $productCoupon
     *
     * @return \Illuminate\Http\Response
     */
    public function show(ProductCoupon $productCoupon)
    {
        $productCoupons = Order::where('coupon', $productCoupon->id)->get();

        return view('product-coupon.view', compact('productCoupons', 'productCoupon'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\ProductCoupon $productCoupon
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(ProductCoupon $productCoupon)
    {
        return view('product-coupon.edit', compact('productCoupon'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\ProductCoupon $productCoupon
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ProductCoupon $productCoupon)
    {
        $arrValidate = [
            'name' => 'required',
            'limit' => 'required|numeric',
            'code' => 'required',
        ];

        if($request->enable_flat == 'on')
        {
            $arrValidate['pro_flat_discount'] = 'required';
        }
        else
        {
            $arrValidate['discount'] = 'required';
        }
        $validator = \Validator::make(
            $request->all(), $arrValidate
        );
        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }

        $productCoupon->name        = $request->name;
        $productCoupon->enable_flat = !empty($request->enable_flat) ? $request->enable_flat : 'off';
        if($request->enable_flat == 'on')
        {
            $productCoupon->flat_discount = $request->pro_flat_discount;
        }
        if(empty($request->enable_flat))
        {
            $productCoupon->discount = $request->discount;
        }
        $productCoupon->limit      = $request->limit;
        $productCoupon->code       = strtoupper($request->code);
        $productCoupon->store_id   = \Auth::user()->current_store;
        $productCoupon->created_by = \Auth::user()->creatorId();
        $productCoupon->update();

        return redirect()->route('product-coupon.index')->with('success', __('Coupon successfully updated.'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\ProductCoupon $productCoupon
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProductCoupon $productCoupon)
    {
        $productCoupon->delete();

        return redirect()->route('product-coupon.index')->with('success', __('Coupon successfully deleted.'));
    }

    public function applyProductCoupon(Request $request)
    {
        if($request->price != '' && $request->coupon != '')
        {
            $original_price = $request->price;
            $store          = Store::where('id', $request->store_id)->first();
            $cart           = session()->get($store->slug);


            $coupons = ProductCoupon::where('code', strtoupper($request->coupon))->first();

            if(!empty($coupons))
            {
                $usedCoupun = $coupons->product_coupon();

                if($coupons->limit == $usedCoupun)
                {
                    return response()->json(
                        [
                            'is_success' => false,
                            'final_price' => $original_price,
                            'price' => number_format($request->price, \Utility::getValByName('decimal_number')),
                            'message' => __('This coupon code has expired.'),
                        ]
                    );
                }
                else
                {
                    $requestprice = str_replace('$', '', $request->price);
                    if($coupons->enable_flat == 'on')
                    {
                        $discount_value = $coupons->flat_discount;
                    }
                    else
                    {
                        $discount_value = ($requestprice / 100) * $coupons->discount;
                    }

                    $plan_price = $requestprice - $discount_value;

                    if($plan_price < 0)
                    {
                        return response()->json(
                            [
                                'is_success' => false,
                                'final_price' => $original_price,
                                'price' => number_format($request->price),
                                'message' => __('This coupon is in valid.'),
                            ]
                        );
                    }
                    if(!empty($request->shipping_price))
                    {
                        $aa             = $store->currency;
                        $shipping_price = str_replace($aa, '', $request->shipping_price);

                        $price            = self::formatPrice($requestprice - $discount_value + $shipping_price, $request->store_id);
                        $data_value_price = $requestprice - floor($discount_value) + $shipping_price;
                    }
                    else
                    {
                        $price            = self::formatPrice($requestprice - $discount_value, $request->store_id);
                        $data_value_price = $requestprice - $discount_value;

                    }
                    $discount_value = '-' . self::formatPrice($discount_value, $request->store_id);

                    $cart['coupon'] = [
                        'coupon' => $coupons,
                        'discount_price' => $discount_value,
                        'final_price' => $price,
                        'final_price_data_value' => number_format($data_value_price, 2),
                        'data_id' => $coupons->id,
                    ];
                    session()->put($store->slug, $cart);

                    return response()->json(
                        [
                            'is_success' => true,
                            'discount_price' => $discount_value,
                            'final_price' => $price,
                            'final_price_data_value' => number_format($data_value_price, 2),
                            'data_id' => $coupons->id,
                            'price' => number_format($plan_price, Utility::getValByName('decimal_number')),
                            'message' => __('Coupon code has applied successfully.'),
                        ]
                    );
                }
            }
            else
            {

                return response()->json(
                    [
                        'is_success' => false,
                        'final_price' => $original_price,
                        'price' => $request->price,
                        Utility::getValByName('decimal_number'),
                        'message' => __('This coupon code is invalid or has expired.'),
                    ]
                );
            }
        }
    }

    public function formatPrice($price, $store_id)
    {
        $store = Store::where('id', $store_id)->first();

        return $store->currency . number_format((float)$price, 2, '.', '');
    }

    public function fileExport() 
    {
        $user = \Auth::user();
        $name = 'product_' . date('Y-m-d i:h:s');
        $data1 = ProductCoupon::where('store_id',$user->current_store)->get();
        if($data1->isEmpty()){
            return redirect()->back()->with('error', "Data Not Found");
        }
        else{
            $data = Excel::download(new ProductCouponExport(), $name . '.xlsx');
        }
        
        

        return $data;
    } 

       public function fileImportExport()
    {
      
        return view('product-coupon.import');
    }

    public function fileImport(Request $request)
    {
        $rules = [
            'file' => 'required|mimes:csv,txt,xlsx',
        ];

        $validator = \Validator::make($request->all(), $rules);

        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }

        $productcoupons = (new ProductCouponImport())->toArray(request()->file('file'))[0];
      
        $totalproductcoupon = count($productcoupons) - 1;

        $errorArray    = [];
        for($i = 1; $i <= count($productcoupons) - 1; $i++)
        {
            $productcoupon = $productcoupons[$i];

            $productcouponByname = ProductCoupon::where('name', $productcoupon[0])->first();


            if(!empty($productcouponByname))
            {
                $productcouponData = $productcouponByname;
            }
            else
            {
                $productcouponData = new ProductCoupon();
              
            }


            $productcouponData->name             = $productcoupon[0];
            $productcouponData->code             = $productcoupon[1];
            $productcouponData->enable_flat      = $productcoupon[2]; 
            $productcouponData->discount         = $productcoupon[3];  
            $productcouponData->flat_discount    = $productcoupon[4];  
            $productcouponData->limit            = $productcoupon[5];  
            $productcouponData->store_id         = \Auth::user()->current_store;
            $productcouponData->created_by       = \Auth::user()->creatorId();
        


            if(empty($productcouponData))
            {
                $errorArray[] = $productcouponData;
            }
            else
            {
                $productcouponData->save();
            }
        }

        $errorRecord = [];
        if(empty($errorArray))
        {
            $data['status'] = 'success';
            $data['msg']    = __('Record successfully imported');
        }
        else
        {
            $data['status'] = 'error';
            $data['msg']    = count($errorArray) . ' ' . __('Record imported fail out of' . ' ' . $totalproductcoupon . ' ' . 'record');


            foreach($errorArray as $errorData)
            {

                $errorRecord[] = implode(',', $errorData);

            }

            \Session::put('errorArray', $errorRecord);
        }

        return redirect()->back()->with($data['status'], $data['msg']);
    }
    
}
