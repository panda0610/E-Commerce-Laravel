<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\UserStore;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = \Auth::user()->current_store;
        $subs = Subscription::where('store_id', $user)->get();

        return view('Subscription.index', compact('subs'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('Subscription.create');
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
            $request, ['email' => 'required']
        );

        $subscription             = new Subscription();
        $subscription->email      = $request->email;
        $subscription['store_id'] = \Auth::user()->current_store;
        $subscription->save();

        return redirect()->back()->with('success', __('Email added!'));
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Subscription $subscription
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Subscription $subscription)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Subscription $subscription
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Subscription $subscription)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Subscription $subscription
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Subscription $subscription)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Subscription $subscription
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Subscription $subscription)
    {
        $subscription->delete();

        return redirect()->back()->with(
            'success', __('subscription Deleted!')
        );
    }

    public function store_email(Request $request, $id)
    {

        $validator = \Validator::make(
            $request->all(), [
                               'email' => 'required|email',
                           ]
        );

        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }
        $subscription             = new Subscription();
        $subscription['email']    = $request->email;
        $subscription['store_id'] = $id;
        $subscription->save();

        return redirect()->back()->with('success', __('Succefully Subscribe'));
    }
}
