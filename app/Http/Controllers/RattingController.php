<?php

namespace App\Http\Controllers;

use App\Models\Ratting;
use Illuminate\Http\Request;
use PHPUnit\Util\Json;

class RattingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * @param \App\Ratting $ratting
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Ratting $ratting)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Ratting $ratting
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Ratting $ratting, $id)
    {
        $rating = Ratting::where('id', $id)->first();

        return view('rating.edit', compact('rating'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Ratting $ratting
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Ratting $ratting, $id)
    {
        $ratting   = Ratting::where('id', $id)->first();
        $validator = \Validator::make(
            $request->all(), [
                               'name' => 'required|max:120',
                               'title' => 'required|max:120',
                           ]
        );
        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }

        $ratting->name        = $request->name;
        $ratting->title       = $request->title;
        $ratting->ratting     = $request->rate;
        $ratting->description = $request->description;
        $ratting->update();

        return redirect()->back()->with('success', __('Rating Updated!'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Ratting $ratting
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Ratting $ratting, $id)
    {
        $ratting = Ratting::where('id', $id)->first();
        $ratting->delete();

        return redirect()->back()->with(
            'success', __('Rating Deleted!')
        );
    }

    public function rating($slug, $id)
    {

        return view('storefront.rating', compact('slug', 'id'));
    }

    public function stor_rating(Request $request, $slug, $id)
    {
        $validator = \Validator::make(
            $request->all(), [
                               'name' => 'required|max:120',
                               'title' => 'required|max:120',
                           ]
        );
        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }
        $ratting              = new Ratting();
        $ratting->slug        = $slug;
        $ratting->product_id  = $id;
        $ratting->rating_view = 'on';
        $ratting->name        = $request->name;
        $ratting->title       = $request->title;
        $ratting->ratting     = $request->rate;
        $ratting->description = $request->description;
        $ratting->save();

        return redirect()->back()->with('success', __('Rating Created!'));
    }

    public function rating_view(Request $request)
    {
        $id = $request->id;

        $ratting              = Ratting::find($id);
        $ratting->rating_view = $request->status;
        $ratting->save();

        return response()->json(
            [
                'success' => __('Successfully!'),
            ]
        );
    }
}
