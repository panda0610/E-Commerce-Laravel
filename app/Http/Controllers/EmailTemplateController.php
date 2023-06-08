<?php

namespace App\Http\Controllers;

use App\Models\EmailTemplate;
use App\Models\EmailTemplateLang;
use App\Models\UserEmailTemplate;
use App\Models\Utility;
use Illuminate\Http\Request;

class EmailTemplateController extends Controller
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
        $usr = \Auth::user();

        $EmailTemplates = EmailTemplate::all();

        return view('email_templates.index', compact('EmailTemplates'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('email_templates.create');
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
        $usr = \Auth::user();

        $validator = \Validator::make(
            $request->all(), [
                               'name' => 'required',
                           ]
        );

        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }

        $EmailTemplate             = new EmailTemplate();
        $EmailTemplate->name       = $request->name;
        $EmailTemplate->created_by = $usr->id;
        $EmailTemplate->save();

        return redirect()->route('email_template.index')->with('success', __('Email Template successfully created.'));
    }

    /**
     * Display the specified resource.
     *
     * @param \App\EmailTemplate $emailTemplate
     *
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        return redirect()->back()->with('error', 'Permission denied.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\EmailTemplate $emailTemplate
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(EmailTemplate $emailTemplate)
    {
        return redirect()->back()->with('error', 'Permission denied.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\EmailTemplate $emailTemplate
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EmailTemplate $emailTemplate)
    {
        $validator = \Validator::make(
            $request->all(), [
                               'from' => 'required',
                           ]
        );

        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }

        $emailTemplate       = EmailTemplate::find($emailTemplate->id);
        $emailTemplate->from = $request->from;
        $emailTemplate->save();

        return redirect()->route(
            'manage.email.language', [
                                       $emailTemplate->id,
                                       $request->lang,
                                   ]
        )->with('success', __('Email Template successfully updated.'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\EmailTemplate $emailTemplate
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(EmailTemplate $emailTemplate)
    {
        return redirect()->back()->with('error', __('This operation is not perform due to demo mode.'));

        return redirect()->back()->with('error', 'Permission denied.');
    }

    // Used For View Email Template Language Wise
    public function manageEmailLang($id, $lang = 'en')
    {

        $languages         = Utility::languages();
        $emailTemplate     = EmailTemplate::where('id', '=', $id)->first();

        $currEmailTempLang = EmailTemplateLang::where('parent_id', '=', $id)->where('lang', $lang)->first();
      
        if(!isset($currEmailTempLang) || empty($currEmailTempLang))
        {
            $currEmailTempLang       = EmailTemplateLang::where('parent_id', '=', $id)->where('lang', 'en')->first();
            $currEmailTempLang->lang = $lang;
        }

        return view('email_templates.show', compact('emailTemplate', 'languages', 'currEmailTempLang'));
    }

    // Used For Store Email Template Language Wise
    public function storeEmailLang(Request $request, $id)
    {
        $validator = \Validator::make(
            $request->all(), [
                               'subject' => 'required',
                               'content' => 'required',
                           ]
        );

        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }

        $emailLangTemplate = EmailTemplateLang::where('parent_id', '=', $id)->where('lang', '=', $request->lang)->first();

        // if record not found then create new record else update it.
        if(empty($emailLangTemplate))
        {
            $emailLangTemplate            = new EmailTemplateLang();
            $emailLangTemplate->parent_id = $id;
            $emailLangTemplate->lang      = $request['lang'];
            $emailLangTemplate->subject   = $request['subject'];
            $emailLangTemplate->content   = $request['content'];
            $emailLangTemplate->save();
        }
        else
        {
            $emailLangTemplate->subject = $request['subject'];
            $emailLangTemplate->content = $request['content'];
            $emailLangTemplate->save();
        }

        return redirect()->route(
            'manage.email.language', [
                                       $id,
                                       $request->lang,
                                   ]
        )->with('success', __('Email Template Detail successfully updated.'));

    }

    // Used For Update Status Company Wise.
    public function updateStatus(Request $request, $id)
    {
        $usr = \Auth::user();

        $user_email = UserEmailTemplate::where('id', '=', $id)->where('user_id', '=', $usr->id)->first();
        if(!empty($user_email))
        {
            if($request->status == 1)
            {
                $user_email->is_active = 0;
            }
            else
            {
                $user_email->is_active = 1;
            }

            $user_email->save();

            return response()->json(
                [
                    'is_success' => true,
                    'success' => __('Status successfully updated!'),
                ], 200
            );
        }
    }
}
