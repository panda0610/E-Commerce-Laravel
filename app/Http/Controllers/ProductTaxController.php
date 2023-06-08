<?php

namespace App\Http\Controllers;

use App\Models\ProductTax;
use Illuminate\Http\Request;

class ProductTaxController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user         = \Auth::user()->current_store;
        $product_taxs = ProductTax::where('store_id', $user)->where('created_by', \Auth::user()->creatorId())->get();

        return view('producttax.index', compact('product_taxs'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('producttax.create');
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
        $validator = \Validator::make(
            $request->all(), [
                               'tax_name' => 'required|max:120',

                           ]
        );
        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }
        $name                     = $request['tax_name'];
        $producttax               = new ProductTax();
        $producttax->name         = $name;
        $producttax->rate         = $request['rate'];
        $producttax['store_id']   = \Auth::user()->current_store;
        $producttax['created_by'] = \Auth::user()->creatorId();
        $producttax->save();

        return redirect()->back()->with('success', __('Product Tax added!'));
    }

    /**
     * Display the specified resource.
     *
     * @param \App\ProductTax $productTax
     *
     * @return \Illuminate\Http\Response
     */
    public function show(ProductTax $productTax)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\ProductTax $productTax
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(ProductTax $productTax)
    {
        return view('producttax.edit', compact('productTax'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\ProductTax $productTax
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ProductTax $productTax)
    {
        $validator = \Validator::make(
            $request->all(), [
                               'name' => 'required|max:120',
                           ]
        );
        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }
        $productTax['name']       = $request->name;
        $productTax['rate']       = $request->rate;
        $producttax['created_by'] = \Auth::user()->creatorId();
        $productTax->update();

        return redirect()->back()->with(
            'success', __('Product Tax updated!')
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\ProductTax $productTax
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProductTax $productTax)
    {
        $productTax->delete();

        return redirect()->back()->with(
            'success', __('Product Tax Deleted!')
        );
    }
}
