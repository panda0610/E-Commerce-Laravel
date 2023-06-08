<?php

namespace App\Exports;

use App\Models\Order;
use App\Models\Store;
use App\Models\Product;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OrderExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {

       
        $store = Store::where('id', \Auth::user()->current_store)->first();
        $data = Order::where('user_id', $store->id)->get();
        //dd($data);
         foreach($data as $k => $order)
        {
        	unset($order->id,$order->card_number,$order->card_exp_month,$order->card_exp_year,$order->user_address_id,$order->shipping_data,$order->coupon,$order->coupon_json,$order->discount_price,$order->plan_name,$order->plan_id,$order->product,$order->price_currency,$order->txn_id,$order->payment_status,$order->phone,$order->receipt,$order->subscription_id,$order->payer_id,$order->payment_frequency,$order->created_by);

            $products=Product::find($order->product_id);
            $product_id=isset($products)?$products->name:'';

            $users=User::find($order->user_id);
            $user_id=isset($users)?$users->name:'';

             $data[$k]["product_id"]=$product_id;
             $data[$k]["user_id"]=$user_id;
            
        }
        return$data;
    }

     public function headings(): array
    {
        return [
            "Order Id",
            "Name",
            "Email",
            "Product",
            "Price",
            "Payment Type",
            "Status",
            "User",
            "created_at",
            "updated_at",
        ];
    }

}
