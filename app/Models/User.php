<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'currant_store',
        'type',
        'created_by',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];



    public function creatorId()
    {
        if($this->type == 'Owner' || $this->type == 'super admin')
        {
            return $this->id;
        }
        else
        {
            return $this->created_by;
        }
    }

    public function currentLanguages()
    {
        return $this->lang;
    }

    public function countCompany()
    {
        return User::where('type', '=', 'Owner')->where('created_by', '=', $this->creatorId())->count();
    }

    public function countPaidCompany()
    {
        return User::where('type', '=', 'Owner')->whereNotIn(
            'plan', [
                      0,
                      1,
                  ]
        )->where('created_by', '=', \Auth::user()->id)->count();
    }

    public function assignPlan($planID)
    {
        $plan = Plan::find($planID);
        if($plan)
        {
            $this->plan = $plan->id;
            if($plan->duration == 'Month')
            {
                $this->plan_expire_date = Carbon::now()->addMonths(1)->isoFormat('YYYY-MM-DD');
            }
            elseif($plan->duration == 'Year')
            {
                $this->plan_expire_date = Carbon::now()->addYears(1)->isoFormat('YYYY-MM-DD');
            }
            else
            {
                $this->plan_expire_date=null;
            }
            $this->save();

            $users    = User::where('created_by', '=', \Auth::user()->creatorId())->where('type', '!=', 'super admin')->get();
            $products = Product::where('created_by', '=', \Auth::user()->creatorId())->get();
            $stores   = Store::where('created_by', '=', \Auth::user()->creatorId())->get();


            if($plan->max_stores == -1)
            {
                foreach($stores as $store)
                {
                    $store->is_active = 1;
                    $store->save();
                }
            }
            else
            {
                $storeCount = 0;
                foreach($stores as $store)
                {
                    $storeCount++;
                    if($storeCount <= $plan->max_stores)
                    {
                        $store->is_active = 1;
                        $store->save();
                    }
                    else
                    {
                        $store->is_active = 0;
                        $store->save();
                    }
                }
            }

            if($plan->max_products == -1)
            {
                foreach($products as $product)
                {
                    $product->is_active = 1;
                    $product->save();
                }
            }
            else
            {
                $productCount = 0;
                foreach($products as $product)
                {
                    $productCount++;
                    if($productCount <= $plan->max_products)
                    {
                        $product->is_active = 1;
                        $product->save();
                    }
                    else
                    {
                        $product->is_active = 0;
                        $product->save();
                    }
                }
            }

            return ['is_success' => true];
        }
        else
        {
            return [
                'is_success' => false,
                'error' => 'Plan is deleted.',
            ];
        }
    }

    public function countProducts()
    {
        return Product::where('created_by', '=', $this->creatorId())->count();
    }

    public function countStores($id)
    {
        return Store::where('created_by', $id)->count();
    }

    public function countStore()
    {
        return Store::where('created_by', '=', $this->creatorId())->count();
    }

    public function currentPlan()
    {
        return $this->hasOne('App\Models\Plan', 'id', 'plan');
    }

    public function stores()
    {
        return $this->belongsToMany('App\Models\Store', 'user_stores', 'user_id', 'store_id')->withPivot('permission');
    }

    public function dateFormat($date)
    {
        $settings = Utility::settings();

        return date($settings['site_date_format'], strtotime($date));
    }


}
