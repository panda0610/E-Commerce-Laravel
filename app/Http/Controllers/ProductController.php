<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Product;
use App\Models\Product_images;
use App\Models\ProductCategorie;
use App\Models\ProductVariantOption;
use App\Models\ProductTax;
use App\Models\Ratting;
use App\Models\Store;
use App\Exports\ProductExport;
use App\Imports\ProductImport;
use App\Models\User;
use App\Models\UserStore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{
    public function __construct()
    {
        if(Auth::check())
        {
            $userlang=\Auth::user()->lang;
            \App::setLocale($userlang);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user             = \Auth::user();
        $store_id         = Store::where('id', $user->current_store)->first();
        $products         = Product::where('store_id', $store_id->id)->orderBy('id', 'DESC')->get();
        $productcategorie = ProductCategorie::where('store_id', $store_id->id)->where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');

        return view('product.index', compact('products', 'productcategorie'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user              = \Auth::user();
        $store_id          = Store::where('id', $user->current_store)->first();
        $product_categorie = ProductCategorie::where('store_id', $store_id->id)->where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
        $product_tax       = ProductTax::where('store_id', $store_id->id)->where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');


        return view('product.create', compact('product_categorie', 'product_tax'));
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
        $user     = \Auth::user();
        $store_id = Store::where('id', $user->current_store)->first();

        $validator = \Validator::make(
            $request->all(), [
                               'name' => 'required|max:120',
                           ]
        );
        if($request->enable_product_variant == '')
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'price' => 'required',
                                   'quantity' => 'required',
                                   'is_cover_image' => 'mimes:jpeg,png,jpg,gif,svg,pdf,doc|max:20480',
                                   'downloadable_prodcut' => 'mimes:jpeg,png,jpg,gif,svg,pdf,doc|max:20480',
                               ]
            );
        }
        if($request->enable_product_variant == 'on')
        {
            if(!empty($request->verians))
            {
                foreach($request->verians as $k => $items)
                {
                    foreach($items as $item_k => $item)
                    {
                        if(empty($item) && $item < 0)
                        {
                            $msg['flag'] = 'error';
                            $msg['msg']  = __('Please Fill The Form');

                            return $msg;
                        }
                    }
                }
            }
            else
            {
                $msg['flag'] = 'error';
                $msg['msg']  = __('Please Add Variants');

                return $msg;
            }
        }
        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            $msg['flag'] = 'error';
            $msg['msg']  = $messages->first();

            return $msg;
        }

        $file_name = [];
        if(!empty($request->multiple_files) && count($request->multiple_files) > 0)
        {
            foreach($request->multiple_files as $file)
            {
                $filenameWithExt = $file->getClientOriginalName();
                $filename        = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                $extension       = $file->getClientOriginalExtension();
                $fileNameToStore = $filename . '_' . time() . '.' . $extension;
                $file_name[]     = $fileNameToStore;
                $dir             = storage_path('uploads/product_image/');
                if(!file_exists($dir))
                {
                    mkdir($dir, 0777, true);
                }
                $path = $file->storeAs('uploads/product_image/', $fileNameToStore);
            }

        }

        if(!empty($request->is_cover_image))
        {
            $filenameWithExt  = $request->file('is_cover_image')->getClientOriginalName();
            $filename         = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension        = $request->file('is_cover_image')->getClientOriginalExtension();
            $fileNameToStores = $filename . '_' . time() . '.' . $extension;
            $dir              = storage_path('uploads/is_cover_image/');
            if(!file_exists($dir))
            {
                mkdir($dir, 0777, true);
            }
            $path = $request->file('is_cover_image')->storeAs('uploads/is_cover_image/', $fileNameToStores);
        }

        if(!empty($request->attachment))
        {
            $filenameWithExt = $request->file('attachment')->getClientOriginalName();
            $filename        = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension       = $request->file('attachment')->getClientOriginalExtension();
            $fileAttachment  = $filename . '_' . time() . '.' . $extension;
            $dir             = storage_path('uploads/is_cover_image/');
            if(!file_exists($dir))
            {
                mkdir($dir, 0777, true);
            }
            $path = $request->file('attachment')->storeAs('uploads/is_cover_image/', $fileAttachment);
        }
        if(!empty($request->downloadable_prodcut))
        {
            $filenameWithExt   = $request->file('downloadable_prodcut')->getClientOriginalName();
            $filename          = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension         = $request->file('downloadable_prodcut')->getClientOriginalExtension();
            $filedownloadable1 = $filename . '_' . time() . '.' . $extension;
            $dir               = storage_path('uploads/downloadable_prodcut/');
            if(!file_exists($dir))
            {
                mkdir($dir, 0777, true);
            }
            $filedownloadable = str_replace(' ', '_', $filedownloadable1);

            $path = $request->file('downloadable_prodcut')->storeAs('uploads/downloadable_prodcut/', $filedownloadable);
        }

        if(!empty($request->product_tax))
        {
            if(count($request->product_tax) > 1 && in_array(0, $request->product_tax))
            {
                $msg['flag'] = 'error';
                $msg['msg']  = __('Please select valid tax');

                return $msg;
            }
        }
        if(!empty($request->product_categorie))
        {
            if(count($request->product_categorie) > 1 && in_array(0, $request->product_categorie))
            {
                $msg['flag'] = 'error';
                $msg['msg']  = __('Please select valid Categorie');

                return $msg;
            }
        }

        $user          = \Auth::user();
        $creator       = User::find($user->creatorId());
        $total_product = $user->countProducts();
        $plan          = Plan::find($creator->plan);


        if($total_product < $plan->max_products || $plan->max_products == -1)
        {
            $product             = new Product();
            $product['store_id'] = $store_id->id;
            $product['name']     = $request->name;
            if(!empty($request->product_categorie))
            {
                $product['product_categorie'] = implode(',', $request->product_categorie);
            }
            else
            {
                $product['product_categorie'] = $request->product_categorie;
            }
            if(!empty($request->price))
            {
                $product['price']      = !empty($request->price) ? $request->price : '0';
                $product['last_price'] = !empty($request->last_price) ? $request->last_price : '0';
            }
            if(!empty($request->quantity))
            {
                $product['quantity'] = !empty($request->quantity) ? $request->quantity : '0';
            }
            $product['SKU'] = $request->SKU;
            if(!empty($request->product_tax))
            {
                $product['product_tax'] = implode(',', $request->product_tax);
            }
            else
            {
                $product['product_tax'] = $request->product_tax;
            }

            $product['custom_field_1'] = $request->custom_field_1;
            $product['custom_value_1'] = $request->custom_value_1;
            $product['custom_field_2'] = $request->custom_field_2;
            $product['custom_value_2'] = $request->custom_value_2;
            $product['custom_field_3'] = $request->custom_field_3;
            $product['custom_value_3'] = $request->custom_value_3;
            $product['custom_field_4'] = $request->custom_field_4;
            $product['custom_value_4'] = $request->custom_value_4;

            $product['product_display']        = isset($request->product_display) ? 'on' : 'off';
            $product['enable_product_variant'] = isset($request->enable_product_variant) ? 'on' : 'off';
            $product['variants_json']          = $request->hiddenVariantOptions;
            $product['is_cover']               = !empty($request->is_cover_image) ? $fileNameToStores : '';
            $product['attachment']             = !empty($request->attachment) ? $fileAttachment : '';
            $product['downloadable_prodcut']   = !empty($request->downloadable_prodcut) ? $filedownloadable : '';
            $product['description']            = $request->description;
            $product['specification']          = $request->specification;
            $product['detail']                 = $request->detail;
            $product['created_by']             = \Auth::user()->creatorId();

            $product->save();

            if(!empty($file_name))
            {
                foreach($file_name as $file)
                {
                    $objStore = Product_images::create(
                        [
                            'product_id' => $product->id,
                            'product_images' => $file,
                        ]
                    );
                }
            }
            if($request->enable_product_variant == 'on')
            {
                $product->variants_json = json_decode($product->variants_json, true);

                $variant_options = array_column($product->variants_json, 'variant_options');

                $possibilities = Product::possibleVariants($variant_options);

                foreach($possibilities as $key => $possibility)
                {
                    $VariantOption             = new ProductVariantOption();
                    $VariantOption->name       = $possibility;
                    $VariantOption->product_id = $product->id;
                    $VariantOption->price      = $request->verians[$key]['price'];
                    $VariantOption->quantity   = $request->verians[$key]['qty'];
                    $VariantOption->created_by = Auth::user()->creatorId();
                    $VariantOption->save();
                }
            }
            if(!empty($product))
            {
                $msg['flag'] = 'success';
                $msg['msg']  = __('Product Successfully Created');
            }
            else
            {
                $msg['flag'] = 'error';
                $msg['msg']  = __('Product Created Failed');
            }

            return $msg;
        }
        else
        {
            $msg['flag'] = 'error';
            $msg['msg']  = __('Your product limit is over Please upgrade plan');

            return $msg;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Product $product
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        $user  = \Auth::user();
        $store = Store::where('id', $user->current_store)->first();

        $product_image = Product_images::where('product_id', $product->id)->get();

        $product_tax     = ProductTax::where('store_id', $store->id)->where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
        $product_ratings = Ratting::where('product_id', $product->id)->get();

        $ratting    = Ratting::where('product_id', $product->id)->where('rating_view', 'on')->sum('ratting');
        $user_count = Ratting::where('product_id', $product->id)->where('rating_view', 'on')->count();
        if($user_count > 0)
        {
            $avg_rating = number_format($ratting / $user_count, 1);
        }
        else
        {
            $avg_rating = number_format($ratting / 1, 1);

        }

        $variant_name          = json_decode($product->variants_json);
        $product_variant_names = $variant_name;

        return view('product.view', compact('product', 'product_image', 'product_tax', 'product_ratings', 'store', 'avg_rating', 'user_count', 'product_variant_names'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Product $product
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        $user              = \Auth::user();
        $store_id          = Store::where('id', $user->current_store)->first();
        $product_categorie = ProductCategorie::where('store_id', $store_id->id)->where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
        $product_image     = Product_images::where('product_id', $product->id)->get();
        $product_tax       = ProductTax::where('store_id', $store_id->id)->where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');

        $productVariantArrays  = [];
        $product_variant_names = [];
        $variant_options       = [];
        if($product->enable_product_variant == 'on')
        {
            $productVariants = ProductVariantOption::where('product_id', $product->id)->get();

            if(!empty(json_decode($product->variants_json)))
            {
                $variant_options       = array_column(json_decode($product->variants_json), 'variant_name');
                $product_variant_names = $variant_options;
            }

            foreach($productVariants as $key => $productVariant)
            {
                $productVariantArrays[$key]['product_variants'] = $productVariant->toArray();
            }
        }

        return view('product.edit', compact('product', 'product_categorie', 'product_image', 'product_tax', 'productVariantArrays', 'product_variant_names', 'variant_options'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Product $product
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $product)
    {
        //
    }

    public function productUpdate(Request $request, $product_id)
    {
        $product = Product::find($product_id);

        $user     = \Auth::user();
        $store_id = Store::where('id', $user->current_store)->first();

        $validator = \Validator::make(
            $request->all(), [
                               'name' => 'required|max:120',
                           ]
        );
        if($request->enable_product_variant == '')
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'price' => 'required',
                                   'quantity' => 'required',
                                   'is_cover_image' => 'mimes:jpeg,png,jpg,gif,svg,pdf,doc|max:20480',
                                   'downloadable_prodcut' => 'mimes:jpeg,png,jpg,gif,svg,pdf,doc|max:20480',
                               ]
            );
        }
        if($request->enable_product_variant == 'on')
        {
            if(!empty($request->variants))
            {
                foreach($request->variants as $k => $items)
                {
                    foreach($items as $item_k => $item)
                    {
                        if(empty($item))
                        {
                            $msg['flag'] = 'error';
                            $msg['msg']  = __('Please Fill The Form');

                            return $msg;
                        }
                    }
                }
            }
            else
            {
                $msg['flag'] = 'error';
                $msg['msg']  = __('Please Add Variants');

                return $msg;
            }
        }
        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            $msg['flag'] = 'error';
            $msg['msg']  = $messages->first();

            return $msg;
        }

        $file_name = [];

        if(!empty($request->multiple_files) && count($request->multiple_files) > 0)
        {
            foreach($request->multiple_files as $file)
            {

                $filenameWithExt = $file->getClientOriginalName();
                $filename        = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                $extension       = $file->getClientOriginalExtension();
                $fileNameToStore = $filename . '_' . time() . '.' . $extension;
                $file_name[]     = $fileNameToStore;
                $dir             = storage_path('uploads/product_image/');
                if(!file_exists($dir))
                {
                    mkdir($dir, 0777, true);
                }
                $path = $file->storeAs('uploads/product_image/', $fileNameToStore);
            }

        }

        if(!empty($request->attachment))
        {
            if(asset(Storage::exists('uploads/is_cover_image/' . $product->attachment)))
            {
                asset(Storage::delete('uploads/is_cover_image/' . $product->attachment));
            }

            $filenameWithExt = $request->file('attachment')->getClientOriginalName();
            $filename        = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension       = $request->file('attachment')->getClientOriginalExtension();
            $fileAttachment  = $filename . '_' . time() . '.' . $extension;
            $dir             = storage_path('uploads/is_cover_image/');
            if(!file_exists($dir))
            {
                mkdir($dir, 0777, true);
            }
            $path = $request->file('attachment')->storeAs('uploads/is_cover_image/', $fileAttachment);
        }

        if(!empty($request->downloadable_prodcut))
        {
            if(asset(Storage::exists('uploads/is_cover_image/' . $product->downloadable_prodcut)))
            {
                asset(Storage::delete('uploads/is_cover_image/' . $product->downloadable_prodcut));
            }

            $filenameWithExt   = $request->file('downloadable_prodcut')->getClientOriginalName();
            $filename          = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension         = $request->file('downloadable_prodcut')->getClientOriginalExtension();
            $filedownloadable1 = $filename . '_' . time() . '.' . $extension;
            $dir               = storage_path('uploads/downloadable_prodcut/');
            if(!file_exists($dir))
            {
                mkdir($dir, 0777, true);
            }
            $filedownloadable = str_replace(' ', '_', $filedownloadable1);

            $path = $request->file('downloadable_prodcut')->storeAs('uploads/downloadable_prodcut/', $filedownloadable);
        }

        if(!empty($request->is_cover_image))
        {
            if(asset(Storage::exists('uploads/is_cover_image/' . $product->is_cover)))
            {
                asset(Storage::delete('uploads/is_cover_image/' . $product->is_cover));
            }

            $filenameWithExt  = $request->file('is_cover_image')->getClientOriginalName();
            $filename         = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension        = $request->file('is_cover_image')->getClientOriginalExtension();
            $fileNameToStores = $filename . '_' . time() . '.' . $extension;
            $dir              = storage_path('uploads/is_cover_image/');
            if(!file_exists($dir))
            {
                mkdir($dir, 0777, true);
            }
            $path = $request->file('is_cover_image')->storeAs('uploads/is_cover_image/', $fileNameToStores);
        }

        if(!empty($request->product_tax))
        {
            if(count($request->product_tax) > 1 && in_array(0, $request->product_tax))
            {
                return redirect()->back()->with('error', __('Please select valid tax'));
            }
        }

        if(!empty($request->product_categorie))
        {
            if(count($request->product_categorie) > 1 && in_array(0, $request->product_categorie))
            {
                return redirect()->back()->with('error', __('Please select valid Categorie'));
            }
        }

        $product['store_id'] = $store_id->id;
        $product['name']     = $request->name;
        if(!empty($request->product_categorie))
        {
            $product['product_categorie'] = implode(',', $request->product_categorie);
        }
        else
        {
            $product['product_categorie'] = $request->product_categorie;
        }
        if(!empty($request->price))
        {
            $product['price']      = !empty($request->price) ? $request->price : '0';
            $product['last_price'] = !empty($request->last_price) ? $request->last_price : '0';
        }
        if(!empty($request->quantity))
        {
            $product['quantity'] = !empty($request->quantity) ? $request->quantity : '0';
        }
        $product['SKU'] = $request->SKU;
        if(!empty($request->product_tax))
        {
            $product['product_tax'] = implode(',', $request->product_tax);
        }
        else
        {
            $product['product_tax'] = $request->product_tax;
        }

        $product['custom_field_1'] = $request->custom_field_1;
        $product['custom_value_1'] = $request->custom_value_1;
        $product['custom_field_2'] = $request->custom_field_2;
        $product['custom_value_2'] = $request->custom_value_2;
        $product['custom_field_3'] = $request->custom_field_3;
        $product['custom_value_3'] = $request->custom_value_3;
        $product['custom_field_4'] = $request->custom_field_4;
        $product['custom_value_4'] = $request->custom_value_4;

        $product['attachment']             = !empty($request->attachment) ? $fileAttachment : '';
        $product['downloadable_prodcut']   = !empty($request->downloadable_prodcut) ? $filedownloadable : '';
        $product['product_display']        = isset($request->product_display) ? 'on' : 'off';
        $product['enable_product_variant'] = isset($request->enable_product_variant) ? 'on' : 'off';
        if(!empty($request->is_cover_image))
        {
            $product['is_cover'] = $fileNameToStores;
        }
        $product['description']   = $request->description;
        $product['specification'] = $request->specification;
        $product['detail']        = $request->detail;
        $product['created_by']    = \Auth::user()->creatorId();
        foreach($file_name as $file)
        {
            $objStore = Product_images::create(
                [
                    'product_id' => $product->id,
                    'product_images' => $file,

                ]
            );
        }
        $product->save();
        if($product->enable_product_variant == 'on')
        {
            foreach($request->variants as $key => $variant)
            {
                $newVal = '';
                foreach(array_values($variant['variants']) as $k => $v)
                {
                    if(!empty($newVal))
                    {
                        $newVal .= ' : ' . $v[0];
                    }
                    else
                    {
                        $newVal .= $v[0];
                    }
                }

                $VariantOption = ProductVariantOption::find($key);
                $VariantOption->name     = $newVal;
                $VariantOption->price    = $variant['price'];
                $VariantOption->quantity = $variant['quantity'];
                $VariantOption->save();
            }
        }

        if(!empty($product))
        {
            $msg['flag'] = 'success';
            $msg['msg']  = __('Product Successfully Created');
        }
        else
        {
            $msg['flag'] = 'error';
            $msg['msg']  = __('Product Created Failed');
        }

        return $msg;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Product $product
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        Ratting::where('product_id', $product->id)->delete();

        $Product_images = Product_images::where('product_id', $product->id)->get();
        $pro_img        = new ProductController();
        foreach($Product_images as $pro)
        {
            $pro_img->fileDelete($pro->id);
        }
        $dir = storage_path('uploads/is_cover_image/');
        if(!empty($product->is_cover))
        {
            unlink($dir . $product->is_cover);
        }
        ProductVariantOption::where('product_id', $product->id)->delete();
        $product->delete();

        return redirect()->back()->with('success', __('Product successfully deleted.'));
    }

    public function grid()
    {
        $user     = \Auth::user();
        $store_id = Store::where('id', $user->current_store)->first();
        $products = Product::where('store_id', $store_id->id)->orderBy('id', 'DESC')->get();

        return view('product.grid', compact('products'));
    }

    public function fileDelete($id)
    {
        $product_img_id = Product_images::find($id);

        $dir = storage_path('uploads/product_image/');
        if(!empty($product_img_id->product_images))
        {
            if(!file_exists($dir . $product_img_id->product_images))
            {
                Product_images::where('id', $id)->delete();

                return response()->json(
                    [
                        'error' => __('File not exists in folder!'),
                        'id' => $id,
                    ]
                );
            }
            else
            {
                unlink($dir . $product_img_id->product_images);
                Product_images::where('id', '=', $id)->delete();

                return response()->json(
                    [
                        'success' => __('Record deleted successfully!'),
                        'id' => $id,
                    ]
                );
            }
        }

        return response()->json(
            [
                'success' => __('Record deleted successfully!'),
                'id' => $id,
            ]
        );
    }

    public function productVariantsCreate(Request $request)
    {
        return view('product.variants.create')->render();
    }

    public function getProductVariantsPossibilities(Request $request)
    {
        $variant_name         = $request->variant_name;
        $variant_options      = $request->variant_options;
        $hiddenVariantOptions = $request->hiddenVariantOptions;

        $hiddenVariantOptions = json_decode($hiddenVariantOptions, true);

        $variants = [
            [
                'variant_name' => $variant_name,
                'variant_options' => explode('|', $variant_options),
            ],
        ];

        $hiddenVariantOptions = array_merge($hiddenVariantOptions, $variants);

        $optionArray = $variantArray = [];
        foreach($hiddenVariantOptions as $variant)
        {
            $variantArray[] = $variant['variant_name'];
            $optionArray[]  = $variant['variant_options'];
        }
        $possibilities = Product::possibleVariants($optionArray);

        $varitantHTML = view('product.variants.list', compact('possibilities', 'variantArray'))->render();

        $result = [
            'status' => false,
            'hiddenVariantOptions' => json_encode($hiddenVariantOptions),
            'varitantHTML' => $varitantHTML,
        ];

        return response()->json($result);
    }

    public function getProductsVariantQuantity(Request $request)
    {
        $status       = false;
        $quantity     = $variant_id = 0;
        $quantityHTML = '<strong>' . __('Please select variants to get available quantity.') . '</strong>';
        $priceHTML    = '';

        $product = Product::find($request->product_id);
        $price   = \App\Models\Utility::priceFormat(0);
        $status  = false;

        if($product && $request->variants != '')
        {
            $variant = ProductVariantOption::where('product_id', $product['id'])->where('name', $request->variants)->first();

            if($variant)
            {
                $status     = true;
                $quantity   = $variant->quantity - (isset($cart[$variant->id]['quantity']) ? $cart[$variant->id]['quantity'] : 0);
                $price      = \App\Models\Utility::priceFormat($variant->price);
                $variant_id = $variant->id;
            }
        }

        return response()->json(
            [
                'status' => $status,
                'price' => $price,
                'quantity' => $quantity,
                'variant_id' => $variant_id,
            ]
        );
    }

    public function VariantDelete($id)
    {
        ProductVariantOption::find($id)->delete();

        return redirect()->back()->with('success', __('Variant successfully deleted.'));
    }

       public function fileExport()
    {


        $name = 'product_' . date('Y-m-d i:h:s');
        $data = Excel::download(new ProductExport(), $name . '.xlsx');


        return $data;
    }

    public function fileImportExport()
    {
        return view('product.import');
    }

     public function fileImport(Request $request)
    {

        $rules = [
            'file' => 'required|mimes:csv,txt,xlsx',
        ];
        $user     = \Auth::user();
        $store_id = Store::where('id', $user->current_store)->first();


        $validator = \Validator::make($request->all(), $rules);

        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }

        $products = (new ProductImport())->toArray(request()->file('file'))[0];

        $totalproduct = count($products) - 1;

        $errorArray    = [];
        for($i = 1; $i <= count($products) - 1; $i++)
        {
            $product = $products[$i];
            $productBySku = Product::where('SKU', $product[2])->first();


            if(!empty($productByname))
            {
                $productData = $productBySku;
            }
            else
            {
                $productData = new Product();

            }

            $productData->name            = $product[0];
            $productData->description         = $product[1];
            $productData->SKU          = $product[2];
            $productData->price          = $product[3];
            $productData->quantity          = $product[4];
            $productData->store_id = $store_id->id;
            $productData->created_by        = \Auth::user()->creatorId();



            if(empty($productData))
            {
                $errorArray[] = $productData;
            }
            else
            {
                $productData->save();
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
            $data['msg']    = count($errorArray) . ' ' . __('Record imported fail out of' . ' ' . $totalproduct . ' ' . 'record');


            foreach($errorArray as $errorData)
            {

                $errorRecord[] = implode(',', $errorData);

            }

            \Session::put('errorArray', $errorRecord);
        }

        return redirect()->back()->with($data['status'], $data['msg']);
    }
}

