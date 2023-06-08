@php
    $userstore = \App\Models\UserStore::where('store_id', $store->id)->first();
    $settings   =\DB::table('settings')->where('name','company_favicon')->where('created_by', $userstore->user_id)->first();
@endphp
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>@yield('page-title') - {{($store->tagline) ?  $store->tagline : env('APP_NAME', ucfirst($store->name))}}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{asset(Storage::url('uploads/logo/').(!empty($settings->value)?$settings->value:'favicon.png'))}}" type="image/png">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<script src="https://api.paymentwall.com/brick/build/brick-default.1.5.0.min.js"> </script>
<div id="payment-form-container"> </div>
@php
    $cart = session()->get($slug);
@endphp
<script>
    var brick = new Brick({
        public_key: '{{ $admin_payment_setting['paymentwall_public_key']  }}', // please update it to Brick live key before launch your project
        amount: {{$data['totalprice']}},
        currency: '{{$store->currency_code}}',
        container: 'payment-form-container',
        action: '{{route("order.pay.with.paymentwall",$slug)}}',
        form: {
            merchant: 'Paymentwall',
            product:  '{{$store->name}}',
            pay_button: 'Pay',
            show_zip: true, // show zip code
            show_cardholder: true // show card holder name
        }
    });

    brick.showPaymentForm(function(data) {
        if(data.flag == 1){
            window.location.href ='{{route("store-complete.complete",[1,"_slug","_order_id"])}}'.replace('_slug', data.slug).replace('_order_id', data.order_id);
            window.location.href ='{{route("store-complete.complete",[1,"_slug","_order_id"])}}'.replace('_slug', data.slug).replace('_order_id', data.order_id);
        }else{
            window.location.href ='{{route("order.callback.error",[2,"_slug"])}}'.replace('_slug', data.slug);
        }
    }, function(errors) {
        if(errors.flag == 1){
            window.location.href ='{{route("order.callback.error",[1,"_slug"])}}'.replace('_slug', errors.slug);
        }else{
            window.location.href ='{{route("order.callback.error",[2,"_slug"])}}'.replace('_slug', errors.slug);
        }
    });

</script>
