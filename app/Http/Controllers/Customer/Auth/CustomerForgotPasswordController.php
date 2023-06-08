<?php

namespace App\Http\Controllers\Student\Auth;

use App\Models\Blog;
use App\Http\Controllers\Controller;
use App\Models\PageOption;
use App\Models\Store;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;

class StudentForgotPasswordController extends Controller
{
    public function __construct()
    {
        if(Auth::check())
        {
            \App::setLocale(\Auth::user()->lang);
        }
    }

    public function showLinkRequestForm($slug){

        $store          = Store::where('slug', $slug)->first();
        $page_slug_urls = PageOption::where('store_id', $store->id)->get();
        $blog           = Blog::where('store_id', $store->id);
        if(empty($store))
        {
            return redirect()->back()->with('error', __('Store not available'));
        }
        return view('storefront.student.password',compact('store','page_slug_urls','slug','blog'));
    }

    public function postStudentEmail(Request $request,$slug)
    {
        $request->validate(
            [
                'email' => 'required|email|exists:students',
            ]
        );

        $token = \Str::random(60);

        DB::table('password_resets')->insert(
            [
                'email' => $request->email,
                'token' => $token,
                'created_at' => Carbon::now(),
            ]
        );

            Mail::send(
                'storefront.student.resetmail', ['token' => $token,'slug'=>$slug], function ($message) use ($request){
                $message->from(env('MAIL_USERNAME'), env('MAIL_FROM_NAME'));
                $message->to($request->email);
                $message->subject('Reset Password Notification');
            }
        );

        return back()->with('success', 'We have e-mailed your password reset link!');
    }

    public function getStudentPassword($slug,$token)
    {
        $store          = Store::where('slug', $slug)->first();
        $page_slug_urls = PageOption::where('store_id', $store->id)->get();
        $blog           = Blog::where('store_id', $store->id);
        if(empty($store))
        {
            return redirect()->back()->with('error', __('Store not available'));
        }
        return view('storefront.student.newpassword', compact('token','slug','store','page_slug_urls','blog'));
    }

    public function updateStudentPassword(Request $request,$slug)
    {
        $request->validate(
            [
                'email' => 'required|email|exists:students',
                'password' => 'required|string|min:6|confirmed',
                'password_confirmation' => 'required',

            ]
        );

        $updatePassword = DB::table('password_resets')->where(
            [
                'email' => $request->email,
                'token' => $request->token,
            ]
        )->first();

        if(!$updatePassword)
        {
            return back()->withInput()->with('error', 'Invalid token!');
        }

        $user = Student::where('email', $request->email)->update(['password' => Hash::make($request->password)]);

        DB::table('password_resets')->where(['email' => $request->email])->delete();

        return redirect()->route('student.loginform',$slug)->with('success', 'Your password has been changed.');

    }
}
