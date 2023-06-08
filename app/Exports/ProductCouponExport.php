<?php

namespace App\Exports;

use App\Models\ProductCoupon;
use App\Models\Store;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductCouponExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $user = \Auth::user();
        $data = ProductCoupon::where('store_id',$user->current_store)->get();

        

        foreach($data as $k => $productcoupon)
        {
        	$store_id=Store::find($productcoupon->store_id);
        	$store=isset($store_id)?$store_id->name:'';

        	$created_bys=User::find($productcoupon->created_by);
        	$created_by=isset($created_bys)?$created_bys->name:'';	

        	$data[$k]["store_id"]=$store;
        	$data[$k]["created_by"]=$created_by;
        }
        return $data;
    }

    public function headings(): array
    {
        return [
            "ProductCoupon Id",
            "Name",
            "Code",
            "Enable_Flat",
            "Coupan Discount",
            "Flat Discount",
            "Limit",
            "Description",
            "Store Name",
            "Created_by",
            "Created_at",
            "updated_at",
        ];
    }
}
