<?php

namespace App\Http\Controllers;

use App\Models\PageOption;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PageOptionController extends Controller
{
    public function __construct()
    {
        if(\Auth::check())
        {
             \App::setLocale(\Auth::user()->lang);
        }
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $store_id    = Auth::user();
        $store_settings = Store::where('id', $store_id->current_store)->first();
        $pageoptions = PageOption::where('store_id', $store_settings->id)->where('created_by', \Auth::user()->creatorId())->get();

        // Get the remote domain
        $store = Store::where('id',$store_settings->id)->where('enable_domain' , 'on')->first();

        // If the subdomain exists
        $sub_store = \App\Models\Store::where('id', $store_settings->id)->where('enable_subdomain' , 'on')->first();


        return view('pageoption.index', compact('pageoptions','store_id','store_settings','store','sub_store'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pageoption.create');
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
                               'name' => 'required|max:120',
                           ]
        );
        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }


        $data     = [
            'name' => $request->name,
            'contents' => $request->contents,
            'store_id' => \Auth::user()->current_store,
            'enable_page_header' => !empty($request->enable_page_header) ? $request->enable_page_header : 'off',
            'enable_page_footer' => !empty($request->enable_page_footer) ? $request->enable_page_footer : 'off',
            'created_by' => \Auth::user()->creatorId(),
        ];
        $pageslug = PageOption::create($data);

        return redirect()->back()->with('success', __('Page Successfully added!'));

    }

    /**
     * Display the specified resource.
     *
     * @param \App\PageOption $pageOption
     *
     * @return \Illuminate\Http\Response
     */
    public function show(PageOption $pageOption)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\PageOption $pageOption
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $pageOption = PageOption::find($id);
        return view('pageoption.edit', compact('pageOption'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\PageOption $pageOption
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id)
    {
        $pageOption = PageOption::find($id);
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

        $pageOption->name     = $request->name;
        $pageOption->contents = $request->contents;
        $pageOption->enable_page_header = $request->enable_page_header;
        $pageOption->enable_page_footer = $request->enable_page_footer;
        $pageOption->update();

        return redirect()->back()->with('success', __('Page Successfully Updated!'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\PageOption $pageOption
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $pageOption = PageOption::find($id);

        $pageOption->delete();

        return redirect()->back()->with('success', __('Page Deleted!'));
    }
}
