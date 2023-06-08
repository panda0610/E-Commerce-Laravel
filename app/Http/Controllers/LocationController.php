<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $locations = Location::where('created_by', \Auth::user()->creatorId())->get();

        return view('location.index', compact('locations'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('location.create');
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
        $this->validate(
            $request, [
                        'name' => 'required|max:40',
                    ]
        );

        $location             = new Location();
        $location->name       = $request->name;
        $location->store_id   = \Auth::user()->current_store;
        $location->created_by = \Auth::user()->creatorId();
        $location->save();

        return redirect()->back()->with('success', __('Location added!'));
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Location $location
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Location $location)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Location $location
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Location $location)
    {
        return view('location.edit', compact('location'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Location $location
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Location $location)
    {
        $this->validate(
            $request, [
                        'name' => 'required|max:40',
                    ]
        );

        $location->name       = $request->name;
        $location->created_by = \Auth::user()->creatorId();
        $location->save();

        return redirect()->back()->with('success', __('Location Updated!'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Location $location
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Location $location)
    {
        $location->delete();

        return redirect()->back()->with('success',__('Location Deleted!'));
    }
}
