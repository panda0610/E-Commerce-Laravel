<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{env('APP_NAME')}} - POS Receipt</title>
</head>
<body>
<div id="invoice-POS" style="width:100%">
    <style>
        #invoice-POS {
            /*box-shadow: 0 0 1in -0.25in rgba(0, 0, 0, 0.5);*/
        }

        #invoice-POS ::selection {
            /*background: #f31544;*/
            color: #fff;
        }

        #invoice-POS ::moz-selection {
            /*background: #f31544;*/
            color: #fff;
        }

        #invoice-POS h1 {
            font-size: 1.5em;
            color: #222;
            font-family: 'Trebuchet MS', sans-serif;
        }

        #invoice-POS h2 {
            font-size: 1.2em;
            font-family: 'Trebuchet MS', sans-serif;
        }

        #invoice-POS h3 {
            font-size: 1.2em;
            font-weight: bold;
            line-height: 2em;
            font-family: 'Trebuchet MS', sans-serif;
        }

        #invoice-POS p {
            font-size: 0.9em;
            color: #000;
            font-family: 'Trebuchet MS', sans-serif;
            font-weight: bold;
        }

        #invoice-POS .footer {
            font-size: 0.9em;
            color: #000;
            font-family: 'Trebuchet MS', sans-serif;
        }

        #invoice-POS #top, #invoice-POS #mid, #invoice-POS #bot {
            /* Targets all id with 'col-' */
            /*border-bottom: 1px solid #000;*/
        }

        #invoice-POS #top {
            min-height: 65px;
        }

        #invoice-POS #mid {
            min-height: 80px;
        }

        #invoice-POS #bot {
            min-height: 50px;
        }

        #invoice-POS #top .logo img {
            height: auto;
            width: 25%;
            height: auto;
            background-size: 60px 60px;
            filter: gray saturate(0%) brightness(70%) contrast(100%);
        }

        #invoice-POS .clientlogo {
            float: left;
            height: 60px;
            width: 60px;
            background-size: 60px 60px;
            border-radius: 50px;
        }

        #invoice-POS .info {
            display: block;
            margin-left: 0;
        }

        #invoice-POS .title {
            float: right;
        }

        #invoice-POS .title p {
            text-align: right;
        }

        #invoice-POS table {
            width: 100%;
            border-collapse: collapse;
        }

        #invoice-POS .tabletitle {
            font-size: 0.7em;
            /*background: #eee;*/
        }

        #invoice-POS .service {
            border-bottom: 2px solid #000;
        }

        #invoice-POS .item {
            width: 24mm;
        }

        #invoice-POS .itemtext {
            font-size: 0.9em;
            color: black;
        }

        #invoice-POS #legalcopy {
            margin-top: 5mm;
        }

        @media print {
            /*#invoice-POS {transform: scale(2);}*/
            /*@page {*/
            /*    size: 80mm 210mm; /* landscape */
            /* you can also specify margins here: */
            /*}*/
        }
    </style>
    <center id="top">
        <div class="logo">
            @php
                //$logo_url = 'uploads/logo.png';
                $logo_url = 'uploads/store_logo/invoice_logo.png';
                //$short_logo_url = 'uploads/logo-short.png';
            @endphp
            <center>
                @if(isset($store['invoice_logo']) && $store['invoice_logo'] !=='')
                    <img class="mt-2 mb-2" src="{{asset(Storage::url('uploads/store_logo/'.$store['invoice_logo']))}}" alt="Logo" width="120"/>
                @else
                    <img class="mt-2 mb-2" src="{{asset(Storage::url($logo_url))}}" alt="Logo" width="120">
            @endif
        </div>
        <div class="info">
            <h2>{{__('SALE RECEIPT')}}</h2>
        </div>
        <!--End Info-->
    </center><!--End InvoiceTop-->
    <div id="bot">
        <div id="table">
            <table>
                <tr class="tabletitle">
                    <td class="item"><h2>Item</h2></td>
                    <td class="Hours" style="text-align: center"><h2>Qty</h2></td>
                    <td class="Rate"><h2>Price</h2></td>
                </tr>
                @foreach($order_products as $k=>$product)
                    <tr class="service">
                        @if($product->variant_id != 0)
                            <td class="tableitem"><p class="itemtext">{{$product->product_name}}-{{$product->variant_name}}</p>
                                @if(!empty($product->tax))
                                    @php
                                        $total_tax=0;
                                    @endphp
                                    @foreach($product->tax as $tax)
                                        @php
                                            $sub_tax = ($product->variant_price* $product->quantity * $tax->tax) / 100;
                                            $total_tax += $sub_tax;
                                        @endphp
                                        {{$tax->tax_name.' '.$tax->tax.'%'.' ('.$sub_tax.')'}}
                                    @endforeach
                                @else
                                    @php
                                        $total_tax = 0;
                                     $total=  $product->variant_price*$product->quantity;
                                    @endphp
                                @endif
                            </td>
                            <td class="tableitem" style="text-align: center"><p class="itemtext">{{$product->quantity}}</p></td>
                            <td class="tableitem"><p class="itemtext"> {{App\Models\Utility::priceFormat($product->variant_price*$product->quantity+$total_tax)}}</p></td>
                        @else
                            <td class="tableitem"><p class="itemtext"> @if(isset($product->product_name)){{$product->product_name}}@else{{$product->name}}@endif</p>
                                @if(!empty($product->tax))
                                    @php
                                        $total_tax=0;
                                    @endphp
                                    @foreach($product->tax as $tax)
                                        @php
                                            $sub_tax = ($product->price* $product->quantity * $tax->tax) / 100;
                                            $total_tax += $sub_tax;
                                        @endphp
                                        {{$tax->tax_name.' '.$tax->tax.'%'.' ('.$sub_tax.')'}}
                                    @endforeach
                                @else
                                    @php
                                        $total_tax = 0;
                                        $total=  $product->price*$product->quantity;
                                    @endphp
                                @endif
                            </td>
                            <td class="tableitem" style="text-align: center"><p class="itemtext">{{$product->quantity}}</p></td>
                            <td class="tableitem"><p class="itemtext"> {{App\Models\Utility::priceFormat($product->price*$product->quantity+$total_tax)}}</p></td>
                        @endif
                    </tr>
                @endforeach
                @if(!empty($shipping_data) && $discount_value != 0)
                    <tr class="tabletitle">
                        <td></td>
                        <td class="Rate"><h2>{{__('Coupon')}} :</h2></td>
                        <td class="payment"><h2>{{App\Models\Utility::priceFormat($discount_value)}}</h2></td>
                    </tr>
                    <tr class="tabletitle">
                        <td></td>
                        <td class="Rate"><h2>{{__('Shipping Price')}} :</h2></td>
                        <td class="payment"><h2>{{App\Models\Utility::priceFormat($shipping_data->shipping_price)}}</h2></td>
                    </tr>
                    <tr class="tabletitle">
                        <td></td>
                        <td class="Rate"><h2>{{__('Grand Total')}}</h2></td>
                        <td class="payment"><h2>{{App\Models\Utility::priceFormat(($sub_total+$total_taxs+$shipping_data->shipping_price)-$discount_value)}}</h2></td>
                    </tr>
                @elseif(!empty($shipping_data))
                    <tr class="tabletitle">
                        <td></td>
                        <td class="Rate"><h2>{{__('Shipping Price')}} :</h2></td>
                        <td class="payment"><h2>{{App\Models\Utility::priceFormat($shipping_data->shipping_price)}}</h2></td>
                    </tr>
                    <tr class="tabletitle">
                        <td></td>
                        <td class="Rate"><h2>{{__('Grand Total')}}</h2></td>
                        <td class="payment"><h2>{{App\Models\Utility::priceFormat($sub_total+$total_taxs+$shipping_data->shipping_price)}}</h2></td>
                    </tr>
                @elseif($discount_value != 0)
                    <tr class="tabletitle">
                        <td></td>
                        <td class="Rate"><h2>{{__('Coupon')}} :</h2></td>
                        <td class="payment"><h2>{{App\Models\Utility::priceFormat($discount_value)}}</h2></td>
                    </tr>
                    <tr class="tabletitle">
                        <td></td>
                        <td class="Rate"><h2>{{__('Grand Total')}}</h2></td>
                        <td class="payment"><h2>{{App\Models\Utility::priceFormat(($sub_total+$total_taxs)-$discount_value)}}</h2></td>
                    </tr>
                @else
                    <tr class="tabletitle">
                        <td></td>
                        <td class="Rate"><h2>{{__('Grand Total')}}</h2></td>
                        <td class="payment"><h2>{{App\Models\Utility::priceFormat($sub_total+$total_taxs)}}</h2></td>
                    </tr>
                @endif
            </table>
        </div>
        <!--End Table-->
    </div>
    <!--End InvoiceBot-->
    <div class="footer pt-2">
        <center><p class="text-dark p-2">Merchandise may not be returned for refund at any time. Power bases cannot be exchanged and are non-refundable. For purchases made in a Milo Showroom, no refunds are available and sales c</p></center>
    </div>
</div>
<script>
    window.print();
    window.onafterprint = back;

    function back() {
        window.close();
        window.history.back();
    }
</script>
</body>
</html>
