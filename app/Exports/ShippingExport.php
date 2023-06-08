<?php

namespace App\Exports;

use App\Models\Shipping;
use App\Models\Location;
use App\Models\Store;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ShippingExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
    	$user = \Auth::user()->current_store;
    	$data = Shipping::where('store_id', $user)->where('created_by', \Auth::user()->creatorId())->get();

    	

    	foreach($data as $k => $shipping)
        {
        	$locations=Location::find($shipping->location_id);
        	$location_id=isset($locations)?$locations->name:'';

        	$store=Store::find($shipping->store_id);
        	$store_id=isset($store)?$store->name:'';

        	$created_bys=User::find($shipping->created_by);
        	$created_by=isset($created_bys)?$created_bys->name:'';

        	$data[$k]["location_id"]=$location_id;
        	$data[$k]["store_id"]=$store_id;
        	$data[$k]["created_by"]=$created_by;
        }
        return $data;
    }

      public function headings(): array
    {
        return [
            "Shipping Id",
            "Name",
            "Price",
            "Location",
            "Store Name",
            "created_by",
            "created_at",
            "updated_at",
        ];
    }
}
