<?php

namespace App\Http\Controllers\Customer\Auth;

use App\Models\Blog;
use App\Http\Controllers\Controller;
use App\Models\PageOption;
use App\Models\Store;
use App\Models\Student;
use App\Models\User;
use App\Models\Customer;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CustomerLoginController extends Controller
{
    public function __construct()
    {
        $lang = session()->get('lang');
        \App::setLocale(isset($lang) ? $lang : 'en');
    }

    public function showLoginForm($slug)
    {   
        $store          = Store::where('slug', $slug)->first();
        $page_slug_urls = PageOption::where('store_id', $store->id)->get();
        $blog           = Blog::where('store_id', $store->id)->first();
        $cart = session()->get($slug);
        $total_item = 0;
        if(isset($cart['products']))
        {
            if(isset($cart) && !empty($cart['products']))
            {
                $total_item = count($cart['products']);
            }
            else
            {
                $total_item = 0;
            }
        }

        
        if(empty($store))
        {
            return redirect()->back()->with('error', __('Store not available'));
        }

        return view('storefront.' . $store->theme_dir . '.user.login', compact('blog','total_item', 'store', 'slug', 'page_slug_urls'));
    }

    public function login(Request $request, $slug, $cart = 0)
    {
       
        if($this->validator($request, $slug) == true)
        {
            if(Auth::guard('customers')->attempt($request->only('email', 'password'), $request->filled('remember')))
            {
               
                //Authentication passed...
                if($cart == 1)
                {
                    return redirect()->route('store.cart', $slug)->with('success', __('You can checkout now.'));
                }
                else
                {
                    return redirect()->route('customer.home', $slug);
                }

            }
        }
        else
        {
            return redirect()->back()->with('error', 'These credentials do not match our records.');
        }


        //Authentication failed...
        return $this->loginFailed();
    }

    private function validator(Request $request, $slug)
    {
        //custom validation error messages.
        $messages = [
            'email.exists' => 'These credentials do not match our records.',
        ];
        $validate = Validator::make(
            $request->all(), [
                               'email' => [
                                   'required',
                                   'string',
                                   'email',
                                   'min:5',
                                   'max:191',
                               ],
                               'password' => [
                                   'required',
                                   'string',
                                   'min:4',
                                   'max:255',
                               ],
                           ]
        );
        $store    = Store::where('slug', $slug)->first();
        $vali     = Customer::where('email', $request->email)->where('store_id', $store->id)->count();
        if($validate->fails())
        {
            $message = $validate->getMessageBag();

            return redirect()->back()->with('error', $message->first());
        }
        elseif($vali > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    private function loginFailed()
    {
        return redirect()->back()->withInput()->with('error', 'These credentials do not match our records!');
    }

    public function logout($slug)
    {
        $store          = Store::where('slug', $slug)->first();
        $blog           = Blog::where('store_id', $store->id);
        $page_slug_urls = PageOption::where('store_id', $store->id)->get();
        if(empty($store))
        {
            return redirect()->back()->with('error', __('Store not available'));
        }
        Auth::guard('customers')->logout();

        return redirect()->route('store.slug', $slug);
    }

    public function profile($slug, $id)
    {
        $store                 = Store::where('slug', $slug)->first();
        $blog                  = Blog::where('store_id', $store->id);
        $page_slug_urls        = PageOption::where('store_id', $store->id)->get();
        //$demoStoreThemeSetting = Utility::demoStoreThemeSetting($store->id);
        if(empty($store))
        {
            return redirect()->back()->with('error', __('Store not available'));
        }
        $userDetail = Customer::find(Crypt::decrypt($id));

        return view('storefront.' . $store->theme_dir . '.customer.profile', compact('blog', 'slug', 'store', 'page_slug_urls', 'userDetail'));
    }

    public function profileUpdate($slug, Request $request)
    {
        $store          = Store::where('slug', $slug)->first();
        $blog           = Blog::where('store_id', $store->id);
        $page_slug_urls = PageOption::where('store_id', $store->id)->get();
        if(empty($store))
        {
            return redirect()->back()->with('error', __('Store not available'));
        }

        $validate = Validator::make(
            $request->all(), [
                               'name' => [
                                   'required',
                                   'string',
                                   'max:255',
                               ],
                               'email' => [
                                   'required',
                                   'string',
                                   'email',
                                   'max:255',
                               ],
                           ]
        );
        $vali     = Customer::where('email', $request->email)->where('store_id', $store->id)->count();
        if($validate->fails())
        {
            $message = $validate->getMessageBag();

            return redirect()->back()->with('error', $message->first());
        }
        $customer = Customer::find($request->id);

        if($request->hasFile('profile'))
        {
            $filenameWithExt = $request->file('profile')->getClientOriginalName();
            $filename        = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension       = $request->file('profile')->getClientOriginalExtension();
            $fileNameToStore = $filename . '_' . time() . '.' . $extension;

            $dir        = storage_path('uploads/customerprofile/');
            $image_path = $dir . $customer['avatar'];

            if(\File::exists($image_path))
            {
                \File::delete($image_path);
            }

            if(!file_exists($dir))
            {
                mkdir($dir, 0777, true);
            }

            $path = $request->file('profile')->storeAs('uploads/customerprofile/', $fileNameToStore);

        }

        if(!empty($request->profile))
        {
            $customer['avatar'] = $fileNameToStore;
        }
        if($request->email != $customer->email)
        {
            if($vali > 0)
            {
                return redirect()->back()->with('error', __('Email already exists'));
            }
            else
            {
                $customer->email = $request->email;
            }
        }


        $customer->name = $request->name;
        $customer->update();
        if(!empty($request->current_password) && !empty($request->new_password) && !empty($request->confirm_password))
        {
            if(Auth::guard('customers')->check())
            {   

                $request->validate(
                    [
                        'current_password' => 'required',
                        'new_password' => 'required|min:6',
                        'confirm_password' => 'required|same:new_password',
                    ]
                );
                $objUser          = Auth::guard('customers')->user();
                $request_data     = $request->All();
                $current_password = $objUser->password;
                if($request_data['new_password'] != $request_data['confirm_password'])
                {   
                    return redirect()->back()->with('error', __('Confirm password and new password are not same.'));   
                }
                else if(Hash::check($request_data['current_password'], $current_password)){
                    $user_id            = Auth::guard('customers')->user()->id;
                    $obj_user           = Customer::find($user_id);
                    $obj_user->password = Hash::make($request_data['new_password']);;
                    $obj_user->update();

                    return redirect()->back()->with('success', __('Password successfully updated.'));
                    
                }
                else
                {
                    return redirect()->back()->with('error', __('Please enter correct current password.'));
                }
            }
            else
            {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        }

        return redirect()->back()->with('success', __('Profile successfully updated.'));
        
    }

    public function updatePassword(Request $request)
    {
        if(Auth::guard('customers')->check())
        {
            $request->validate(
                [
                    'current_password' => 'required',
                    'new_password' => 'required|min:6',
                    'confirm_password' => 'required|same:new_password',
                ]
            );
            $objUser          = Auth::guard('students')->user();
            $request_data     = $request->All();
            $current_password = $objUser->password;
            if(Hash::check($request_data['current_password'], $current_password))
            {
                $user_id            = Auth::guard('customers')->user()->id;
                $obj_user           = Customer::find($user_id);
                $obj_user->password = Hash::make($request_data['new_password']);;
                $obj_user->update();

                return redirect()->back()->with('success', __('Password successfully updated.'));
            }
            else
            {
                return redirect()->back()->with('error', __('Please enter correct current password.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Something is wrong.'));
        }
    }

}
