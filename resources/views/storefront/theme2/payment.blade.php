@extends('storefront.layout.theme2')
@section('page-title')
    {{__('Payment')}}
@endsection
@push('css-page')
    <style>
        .box-space .text-right i {
            color: #0b3050;
        }
    </style>
@endpush
@section('content')
    @php
        $coupon_price = !empty($coupon_price)?$coupon_price:0;
        $shipping_price = !empty($shipping_price)?$shipping_price:0;
    @endphp
    <input type="hidden" id="return_url">
    <input type="hidden" id="return_order_id">
    <main>
        <section class="my-cart-section my-payment-box sec-top">
            <div class="container">
                <!-- Shopping cart table -->
                <div class="row">
                    <div class="pr-title mb-4">
                        <h3 class="mt-4 store-title t-secondary playfair">{{__('Payment')}}</h3>
                        <div class="payment-step">
                            <a href="{{route('store.cart',$store->slug)}}" class="btn btn-mycart">1 - {{__('My Cart')}}</a>
                            <a href="{{route('user-address.useraddress',$store->slug)}}" class="btn btn-mycart">2 - {{__('Customer')}}</a>
                            <a href="{{route('store-payment.payment',$store->slug)}}" class="btn btn-mycart active">3 - {{__('Payment')}}</a>
                        </div>
                    </div>
                </div>
                <div class="row row-grid">
                    <div class="col-xl-8 col-lg-7">
                        <!-- COD -->
                        @if($store['enable_cod'] == 'on')
                            <div class="card">
                                <div class="box-space">
                                    <div class="row">
                                        <div class="col-lg-8 col-md-8 col-sm-12">
                                            <div>
                                                <label class="h6 mb-0 lh-180" for="radio-payment-paypal">{{__('COD')}}</label>
                                            </div>
                                            <p class="text-muted mt-2 mb-0 text-12">{{__('Cash on delivery is a type of transaction in which payment for a good is made at the time of delivery.')}}</p>
                                        </div>
                                        <div class="col-12 col-lg col-md-4 text-right">
                                            <i class="fas fa-truck fa-2x mb-2"></i>
                                            <form class="w3-container w3-display-middle w3-card-4" method="POST" action="{{ route('user.cod',$store->slug) }}">
                                                @csrf
                                                <input type="hidden" name="product_id">
                                                <div class="form-group">
                                                    <button type="button" class="btn btn-primary btn-sm" id="cash_on_delivery">{{__('Pay Now')}}</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                    <!-- Add money using Stripe -->
                        @if(isset($store_payments['is_stripe_enabled']) && $store_payments['is_stripe_enabled'] == 'on')
                            <form role="form" action="{{ route('stripe.post',$store->slug) }}" method="post" class="require-validation" id="payment-form">
                                @csrf
                                <div class="card">
                                    <div class="box-space">
                                        <div class="row">
                                            <div class="col-lg-4">
                                                <div>
                                                    <label class="h6 mb-0 lh-180" for="radio-payment-paypal">{{__('Stripe')}}</label>
                                                </div>
                                            </div>
                                            <div class="col-lg-5 col-md-5 col-sm-12">
                                                <p class="text-muted mt-2 mb-0 text-12">{{__('Safe money transfer using your bank account. We support Mastercard, Visa, Maestro and Skrill.')}}</p>
                                            </div>
                                            <div class="col-lg-3 col-md-3 col-sm-12 text-right">
                                                <img alt="Image placeholder" src="{{asset('assets/theme1/img/mastercard.png')}}" width="40" class="mr-2">
                                                <img alt="Image placeholder" src="{{asset('assets/theme1/img/visa.png')}}" width="40" class="mr-2">
                                                <img alt="Image placeholder" src="{{asset('assets/theme1/img/skrill.png')}}" width="40">
                                            </div>
                                        </div>
                                        <div class="row align-items-center mt-3">
                                            <div class="col-lg-12 col-md-12 col-sm-12">
                                                <div class="form-group">
                                                    <label for="card-name-on" class="form-control-label t-gray font-600">{{__('Name on card')}}</label>
                                                    <input type="text" name="name" id="card-name-on" class="form-control required" placeholder="Enter Your Name">
                                                </div>
                                            </div>
                                            <div class="col-lg-12 col-md-12 col-sm-12">
                                                <div id="card-element"></div>
                                                <div id="card-errors" role="alert"></div>
                                            </div>
                                            <div class="col-md-10">
                                                <br>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="error" style="display: none;">
                                                    <div class='alert-danger alert text_sm'>{{__('Please correct the errors and try again.')}}</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-12 col-md-12 col-sm-12 text-right pr-0">
                                            <div class="form-group">
                                                <input type="hidden" name="plan_id">
                                                <button class="btn btn-primary btn-sm pt-0" type="submit">
                                                    <i class="mdi mdi-cash-multiple mr-1"></i> {{__('Pay Now')}}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        @endif

                    <!-- Add money using PayPal -->
                        @if(isset($store_payments['is_paypal_enabled']) && $store_payments['is_paypal_enabled'] == 'on')
                            <div class="card">
                                <div class="box-space">
                                    <div class="row">
                                        <div class="col-lg-8 col-md-8 col-sm-12">
                                            <label class="h6 mb-0 lh-180" for="radio-payment-paypal">{{('Paypal')}}</label>
                                            <p class="text-muted mt-2 mb-0 text-12">{{__('Pay your order using the most known and secure platform for online money transfers. You will be redirected to PayPal to finish complete your purchase.')}}</p>
                                        </div>
                                        <div class="col-lg-4 col-md-4 col-sm-12 text-right">
                                            <img alt="Image placeholder" src="{{asset('assets/theme1/img/paypa.png')}}" width="100" class="ml-2">
                                            <form class="w3-container w3-display-middle w3-card-4" method="POST" action="{{ route('pay.with.paypal',$store->slug) }}">
                                                @csrf
                                                <input type="hidden" name="product_id">
                                                <div class="form-group mt-3">
                                                    <button class="btn btn-primary btn-sm pt-0" type="submit">
                                                        <i class="mdi mdi-cash-multiple mr-1"></i> {{__('Pay Now')}}
                                                    </button>
                                                </div>
                                            </form>
                                        </div>


                                    </div>
                                </div>
                            </div>
                        @endif

                    <!-- Add money using whatsapp -->
                        @if($store['enable_whatsapp'] == 'on')
                            <div class="card">
                                <div class="box-space">
                                    <div class="row">
                                        <div class="col-lg-8 col-md-8 col-sm-12">
                                            <label class="h6 mb-0 lh-180" for="radio-payment-whatsapp">{{__('WhatsApp')}}</label>
                                            <p class="text-muted mt-2 mb-0 text-12">{{ __('Click to chat. The click to chat feature lets customers click an URL in order to directly start a chat with another person or business via WhatsApp. ... QR code. As you know, having to add a phone number to
                                                your contacts in order to start up a WhatsApp message can take a little while')}}.....</p>
                                        </div>
                                        <div class="col-md-4 col-md-4 col-sm-12 text-right">
                                            <img alt="Image placeholder" src="{{asset('assets/img/whatsapp.png')}}" width="75" class="ml-2">
                                            <form class="w3-container w3-display-middle w3-card-4" method="POST" action="{{ route('user.whatsapp',$store->slug) }}">
                                                @csrf
                                                <input type="hidden" name="product_id">
                                                <div class="form-group mt-3">
                                                    <button type="button" class="btn btn-primary btn-sm" id="owner-whatsapp">{{__('Pay Now')}}</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="box-space pt-0">
                                    <div class="row align-items-center">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="form-control-label t-gray font-600">{{__('Phone Number')}}</label>
                                                <input class="form-control input-primary" name="wts_number" id="wts_number" type="text" placeholder="Enter Your Phone Number">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                    <!-- Add money using Telegram -->
                        @if($store['enable_telegram'] == 'on')
                            <div class="card">
                                <div class="box-space">
                                    <div class="row">
                                        <div class="col-lg-8 col-md-8 col-sm-12">
                                            <label class="h6 mb-0 lh-180" for="radio-payment-whatsapp">{{__('Telegram')}}</label>
                                            <p class="text-muted mt-2 mb-0 text-12">
                                                {{ __('Click to chat. The click to chat feature lets customers click an URL in order to directly start a chat with another person or business via WhatsApp. ... QR code. As you know, having to add a phone number to
                                                your contacts in order to start up a WhatsApp message can take a little while')}}.....</p>
                                        </div>
                                        <div class="col-md-4 col-md-4 col-sm-12 text-right">
                                            <img alt="Image placeholder" src="{{asset('assets/img/telegram.svg')}}" width="75" class="ml-2">
                                            <form class="w3-container w3-display-middle w3-card-4" method="POST" action="{{ route('user.telegram',$store->slug) }}">
                                                @csrf
                                                <input type="hidden" name="product_id">
                                                <div class="form-group mt-3">
                                                    <button type="button" class="btn btn-primary btn-sm" id="owner-telegram">{{__('Pay Now')}}</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                    <!-- Add money using Bank Transfer -->
                        @if($store['enable_bank'] == 'on')
                            <div class="card">
                                <div class="box-space">
                                    <div class="row">

                                        <div class="col-lg-7 col-md-7 col-sm-12">
                                            <p class="white_space text_sm">{{$store->bank_number}}</p>
                                        </div>
                                        <div class="col-lg-5 col-md-5 col-sm-12 text-right">
                                            <img alt="Image placeholder" src="{{asset('assets/img/bank.png')}}" width="70">
                                            <form class="w3-container w3-display-middle w3-card-4" method="POST" action="{{ route('user.bank_transfer',$store->slug) }}">
                                                @csrf
                                                <input type="hidden" name="product_id">
                                                <div class="form-group mt-3">
                                                    <button type="button" class="btn btn-primary btn-sm" id="bank_transfer">{{__('Pay Now')}}</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                    <!-- Add money using Paystack -->
                        @if(isset($store_payments['is_paystack_enabled']) && $store_payments['is_paystack_enabled']=='on')
                            <script src="https://js.paystack.co/v1/inline.js"></script>
                            <script src="https://checkout.paystack.com/service-worker.js"></script>
                            {{-- PAYSTACK JAVASCRIPT FUNCTION --}}
                            <script>

                                function payWithPaystack() {
                                    var paystack_callback = "{{ url('/paystack') }}";
                                    var order_id = '{{$order_id = str_pad(!empty($order->id) ? $order->id + 1 : 0 + 1, 4, "100", STR_PAD_LEFT)}}';
                                    var slug = '{{$store->slug}}';
                                    var handler = PaystackPop.setup({
                                        key: '{{ $store_payments['paystack_public_key']  }}',
                                        email: '{{$cust_details['email']}}',
                                        amount: $('.total_price').data('value') * 100,
                                        currency: '{{$store['currency_code']}}',
                                        ref: 'pay_ref_id' + Math.floor((Math.random() * 1000000000) +
                                            1
                                        ), // generates a pseudo-unique reference. Please replace with a reference you generated. Or remove the line entirely so our API will generate one for you
                                        metadata: {
                                            custom_fields: [{
                                                display_name: "Mobile Number",
                                                variable_name: "mobile_number",
                                                value: "{{$cust_details['phone']}}"
                                            }]
                                        },

                                        callback: function (response) {
                                            console.log(response.reference, order_id);
                                            window.location.href = paystack_callback + '/' + slug + '/' + response.reference + '/' + {{$order_id}};
                                        },
                                        onClose: function () {
                                            alert('window closed');
                                        }
                                    });
                                    handler.openIframe();
                                }

                            </script>
                            {{-- /PAYSTACK JAVASCRIPT FUNCTION --}}
                            <div class="card">
                                <div class="card-header">
                                    <div class="row">
                                        <div class="col-12 col-lg-8 col-md-8">
                                            <div>
                                                <label class="h6 mb-0 lh-180" for="radio-payment-paypal">{{__('Paystack')}}</label>
                                            </div>
                                            <p class="text_sm text-muted mt-2 mb-0">
                                                {{__('Pay your order using the most known and secure platform for online money transfers. You will be redirected to Paystack to finish complete your purchase')}}.
                                            </p>
                                        </div>
                                        <div class="col-12 col-lg-4 col-md-4 text-right">
                                            <img alt="Image placeholder" src="{{asset('assets/img/paystack-logo.jpg')}}" width="110">
                                            <input type="hidden" name="product_id">
                                            <div class="form-group mt-3">
                                                <div class="text-sm-right ">
                                                    <button class="btn btn-primary btn-sm float-right" type="button" onclick="payWithPaystack()">
                                                        <i class="mdi mdi-cash-multiple mr-1"></i> {{__('Pay Now')}}
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        @endif

                        @if(isset($store_payments['is_flutterwave_enabled']) && $store_payments['is_flutterwave_enabled']=='on')

                            <script src="https://api.ravepay.co/flwv3-pug/getpaidx/api/flwpbf-inline.js"></script>
                            {{-- Flutterwave JAVASCRIPT FUNCTION --}}
                            <script>

                                function payWithRave() {
                                    var API_publicKey = '{{ $store_payments['flutterwave_public_key']  }}';
                                    var nowTim = "{{ date('d-m-Y-h-i-a') }}";
                                    var order_id = '{{$order_id = time()}}';
                                    var flutter_callback = "{{ url('/flutterwave') }}";
                                    var x = getpaidSetup({
                                        PBFPubKey: API_publicKey,
                                        customer_email: '{{$cust_details['email']}}',
                                        amount: $('.total_price').data('value'),
                                        customer_phone: '{{$cust_details['phone']}}',
                                        currency: '{{$store['currency_code']}}',
                                        txref: nowTim + '__' + Math.floor((Math.random() * 1000000000)) + 'fluttpay_online-' +
                                        {{ date('Y-m-d') }},
                                        meta: [{
                                            metaname: "payment_id",
                                            metavalue: "id"
                                        }],
                                        onclose: function () {
                                        },
                                        callback: function (response) {

                                            var txref = response.tx.txRef;

                                            if (
                                                response.tx.chargeResponseCode == "00" ||
                                                response.tx.chargeResponseCode == "0"
                                            ) {
                                                window.location.href = flutter_callback + '/{{$store->slug}}/' + txref + '/' + order_id;
                                            } else {
                                                // redirect to a failure page.
                                            }
                                            x.close(); // use this to close the modal immediately after payment.
                                        }
                                    });
                                }
                            </script>
                            {{-- /PAYSTACK JAVASCRIPT FUNCTION --}}
                            <div class="card">
                                <div class="card-header">
                                    <div class="row">
                                        <div class="col-12 col-lg-8 col-md-8">
                                            <div>
                                                <label class="h6 mb-0 lh-180" for="radio-payment-paypal">{{__('Flutterwave')}}</label>
                                            </div>
                                            <p class="text_sm text-muted mt-2 mb-0">
                                                {{__('Pay your order using the most known and secure platform for online money transfers. You will be redirected to Flutterwave to finish complete your purchase')}}.
                                            </p>
                                        </div>
                                        <div class="col-12 col-lg-4 col-md-4 text-right">
                                            <img alt="Image placeholder" src="{{asset('assets/img/flutterwave_logo.png')}}" width="110">
                                            <input type="hidden" name="product_id">
                                            <div class="form-group mt-3">
                                                <div class="text-sm-right ">
                                                    <button class="btn btn-primary btn-sm float-right" type="button" onclick="payWithRave()">
                                                        <i class="mdi mdi-cash-multiple mr-1"></i> {{__('Pay Now')}}
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>

                        @endif

                        @if(isset($store_payments['is_razorpay_enabled']) && $store_payments['is_razorpay_enabled'] == 'on')
                            @php
                                $logo         =asset(Storage::url('uploads/logo/'));
                                $company_logo =\App\Models\Utility::getValByName('company_logo');
                            @endphp
                            <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
                            {{-- Flutterwave JAVASCRIPT FUNCTION --}}
                            <script>

                                function payRazorPay() {

                                    var getAmount = $('.total_price').data('value');
                                    var order_id = '{{$order_id = str_pad(!empty($order->id) ? $order->id + 1 : 0 + 1, 4, "100", STR_PAD_LEFT)}}';
                                    var product_id = '{{$order_id}}';
                                    var useremail = '{{$cust_details['email']}}';
                                    var razorPay_callback = '{{url('razorpay')}}';
                                    var totalAmount = getAmount * 100;
                                    var product_array = '{{$encode_product}}';
                                    var product = JSON.parse(product_array.replace(/&quot;/g, '"'));


                                    var coupon_id = $('.hidden_coupon').attr('data_id');
                                    var dicount_price = $('.dicount_price').html();

                                    var options = {
                                        "key": "{{ $store_payments['razorpay_public_key']  }}", // your Razorpay Key Id
                                        "amount": totalAmount,
                                        "name": product,
                                        "currency": '{{$store['currency_code']}}',
                                        "description": "Order Id : " + order_id,
                                        "image": "{{$logo.'/'.(isset($company_logo) && !empty($company_logo)?$company_logo:'logo-dark.png')}}",
                                        "handler": function (response) {
                                            window.location.href = razorPay_callback + '/{{$store->slug}}/' + response.razorpay_payment_id + '/' + order_id;
                                        },
                                        "theme": {
                                            "color": "#528FF0"
                                        }
                                    };

                                    var rzp1 = new Razorpay(options);
                                    rzp1.open();
                                }
                            </script>
                            {{-- /Razerpay JAVASCRIPT FUNCTION --}}
                            <div class="card">
                                <div class="card-header">
                                    <div class="row">
                                        <div class="col-12 col-lg-8 col-md-8">
                                            <div>
                                                <label class="h6 mb-0 lh-180" for="radio-payment-paypal">{{__('Razorpay')}}</label>
                                            </div>
                                            <p class="text_sm text-muted mt-2 mb-0">
                                                {{__('Pay your order using the most known and secure platform for online money transfers. You will be redirected to Razorpay to finish complete your purchase')}}.
                                            </p>
                                        </div>
                                        <div class="col-12 col-lg-4 col-md-4 text-right">
                                            <img alt="Image placeholder" src="{{asset('assets/img/razorpay.png')}}" width="110">
                                            <input type="hidden" name="product_id">
                                            <div class="form-group mt-3">
                                                <div class="text-sm-right ">
                                                    <button class="btn btn-primary btn-sm float-right" type="button" onclick="payRazorPay()">
                                                        <i class="mdi mdi-cash-multiple mr-1"></i> {{__('Pay Now')}}
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>

                        @endif

                        @if(isset($store_payments['is_paytm_enabled']) && $store_payments['is_paytm_enabled'] == 'on')
                            <div class="card">
                                <div class="card-header">
                                    <div class="row">
                                        <div class="col-12 col-lg-8 col-md-8">
                                            <div>
                                                <label class="h6 mb-0 lh-180" for="radio-payment-paypal">{{__('Paytm')}}</label>
                                            </div>
                                            <p class="text_sm text-muted mt-2 mb-0">
                                                {{__('Pay your order using the most known and secure platform for online money transfers. You will be redirected to Paytm to finish complete your purchase')}}.
                                            </p>
                                        </div>
                                        <div class="col-12 col-lg-4 col-md-4 text-right">
                                            <img alt="Image placeholder" src="{{asset('assets/img/Paytm.png')}}" width="110">
                                            <form class="w3-container w3-display-middle w3-card-4" method="POST" action="{{ route('paytm.prepare.payments',$store->slug) }}">
                                                @csrf
                                                <input type="hidden" name="id" value="{{ date('Y-m-d') }}-{{ strtotime(date('Y-m-d H:i:s')) }}-payatm">
                                                <input type="hidden" name="order_id" value="{{str_pad(!empty($order->id) ? $order->id + 1 : 0 + 1, 4, "100", STR_PAD_LEFT)}}">
                                                @php
                                                    $skrill_data = [
                                                        'transaction_id' => md5(date('Y-m-d') . strtotime('Y-m-d H:i:s') . 'user_id'),
                                                        'user_id' => 'user_id',
                                                        'amount' => 'amount',
                                                        'currency' => 'currency',
                                                    ];
                                                    session()->put('skrill_data', $skrill_data);

                                                @endphp
                                                <div class="form-group mt-3">
                                                    <div class="text-sm-right ">
                                                        <button type="submit" class="btn btn-primary btn-sm float-right">{{__('Pay Now')}}</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>

                                    </div>
                                </div>
                            </div>

                        @endif

                        @if(isset($store_payments['is_mercado_enabled']) && $store_payments['is_mercado_enabled'] == 'on')
                            <script>
                                function payMercado() {

                                    var product_array = '{{$encode_product}}';
                                    var product = JSON.parse(product_array.replace(/&quot;/g, '"'));
                                    var order_id = '{{$order_id = time()}}';

                                    var total_price = $('#Subtotal .total_price').attr('data-value');
                                    var coupon_id = $('.hidden_coupon').attr('data_id');
                                    var dicount_price = $('.dicount_price').html();

                                    var data = {
                                        coupon_id: coupon_id,
                                        dicount_price: dicount_price,
                                        total_price: total_price,
                                        product: product,
                                        order_id: order_id,
                                    }
                                    $.ajax({
                                        url: '{{ route('mercadopago.prepare',$store->slug) }}',
                                        method: 'POST',
                                        data: data,
                                        headers: {
                                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                        },
                                        success: function (data) {
                                            if (data.status == 'success') {
                                                window.location.href = data.url;
                                            } else {
                                                show_toastr("Error", data.success, data["status"]);
                                            }
                                        }
                                    });
                                }
                            </script>
                            <div class="card">
                                <div class="card-header">
                                    <div class="row">
                                        <div class="col-12 col-lg-8 col-md-8">
                                            <div>
                                                <label class="h6 mb-0 lh-180" for="radio-payment-paypal">{{__('Mercado Pago')}}</label>
                                            </div>
                                            <p class="text_sm text-muted mt-2 mb-0">
                                                {{__('Pay your order using the most known and secure platform for online money transfers. You will be redirected to Mercado Pago to finish complete your purchase')}}.
                                            </p>
                                        </div>
                                        <div class="col-12 col-lg-4 col-md-4 text-right">
                                            <img alt="Image placeholder" src="{{asset('assets/img/mercadopago.png')}}" width="110">

                                            <div class="form-group mt-3">
                                                <div class="text-sm-right ">
                                                    <button type="button" class="btn btn-primary btn-sm float-right" onclick="payMercado()">{{__('Pay Now')}}</button>
                                                </div>
                                            </div>

                                        </div>

                                    </div>
                                </div>
                            </div>

                        @endif

                        @if(isset($store_payments['is_mollie_enabled']) && $store_payments['is_mollie_enabled'] == 'on')
                            <div class="card">
                                <div class="card-header">
                                    <div class="row">
                                        <div class="col-12 col-lg-8 col-md-8">
                                            <div>
                                                <label class="h6 mb-0 lh-180" for="radio-payment-paypal">{{__('Mercado Pago')}}</label>
                                            </div>
                                            <p class="text_sm text-muted mt-2 mb-0">
                                                {{__('Pay your order using the most known and secure platform for online money transfers. You will be redirected to Mercado Pago to finish complete your purchase')}}.
                                            </p>
                                        </div>
                                        <div class="col-12 col-lg-4 col-md-4 text-right">
                                            <img alt="Image placeholder" src="{{asset('assets/img/mollie.png')}}" width="100">
                                            <form class="w3-container w3-display-middle w3-card-4" method="POST" action="{{ route('mollie.prepare.payments',$store->slug) }}">
                                                @csrf
                                                <input type="hidden" name="id" value="{{ date('Y-m-d') }}-{{ strtotime(date('Y-m-d H:i:s')) }}-payatm">
                                                <input type="hidden" name="desc" value="{{time()}}">
                                                <div class="form-group mt-3">
                                                    <div class="text-sm-right ">
                                                        <button type="submit" class="btn btn-primary btn-sm float-right">{{__('Pay Now')}}</button>
                                                    </div>
                                                </div>
                                            </form>

                                        </div>

                                    </div>
                                </div>
                            </div>
                        @endif

                        @if(isset($store_payments['is_skrill_enabled']) && $store_payments['is_skrill_enabled'] == 'on')
                            <div class="card">
                                <div class="card-header">
                                    <div class="row">
                                        <div class="col-12 col-lg-8 col-md-8">
                                            <div>
                                                <label class="h6 mb-0 lh-180" for="radio-payment-paypal">{{__('Skrill')}}</label>
                                            </div>
                                            <p class="text_sm text-muted mt-2 mb-0">
                                                {{__('Pay your order using the most known and secure platform for online money transfers. You will be redirected to Mercado Pago to finish complete your purchase')}}.
                                            </p>
                                        </div>
                                        <div class="col-12 col-lg-4 col-md-4 text-right">
                                            <img alt="Image placeholder" src="{{asset('assets/img/skrill.png')}}" width="100">
                                            <form class="w3-container w3-display-middle w3-card-4" method="POST" action="{{ route('skrill.prepare.payments',$store->slug) }}">
                                                @csrf
                                                <input type="hidden" name="transaction_id" value="{{ date('Y-m-d') . strtotime('Y-m-d H:i:s') . 'user_id' }}">
                                                <input type="hidden" name="desc" value="{{time()}}">
                                                <div class="form-group mt-3">
                                                    <div class="text-sm-right ">
                                                        <button type="submit" class="btn btn-primary btn-sm float-right">{{__('Pay Now')}}</button>
                                                    </div>
                                                </div>
                                            </form>

                                        </div>

                                    </div>
                                </div>
                            </div>
                        @endif

                        @if(isset($store_payments['is_coingate_enabled']) && $store_payments['is_coingate_enabled'] == 'on')
                            <div class="card">
                                <div class="card-header">
                                    <div class="row">
                                        <div class="col-12 col-lg-8 col-md-8">
                                            <div>
                                                <label class="h6 mb-0 lh-180" for="radio-payment-paypal">{{__('CoinGate')}}</label>
                                            </div>
                                            <p class="text_sm text-muted mt-2 mb-0">
                                                {{__('Pay your order using the most known and secure platform for online money transfers. You will be redirected to Mercado Pago to finish complete your purchase')}}.
                                            </p>
                                        </div>
                                        <div class="col-12 col-lg-4 col-md-4 text-right">
                                            <img alt="Image placeholder" src="{{asset('assets/img/coingate.png')}}" width="100">
                                            <form class="w3-container w3-display-middle w3-card-4" method="POST" action="{{ route('coingate.prepare',$store->slug) }}">
                                                @csrf
                                                <input type="hidden" name="transaction_id" value="{{ date('Y-m-d') . strtotime('Y-m-d H:i:s') . 'user_id' }}">
                                                <input type="hidden" name="desc" value="{{time()}}">
                                                <div class="form-group mt-3">
                                                    <div class="text-sm-right ">
                                                        <button type="submit" class="btn btn-primary btn-sm float-right">{{__('Pay Now')}}</button>
                                                    </div>
                                                </div>
                                            </form>

                                        </div>

                                    </div>
                                </div>
                            </div>
                        @endif

                        @if(isset($store_payments['is_paymentwall_enabled']) && $store_payments['is_paymentwall_enabled'] == 'on')
                            <div class="card">
                                <div class="card-header">
                                    <div class="row">
                                        <div class="col-12 col-lg-8 col-md-8">
                                            <div>
                                                <label class="h6 mb-0 lh-180" for="radio-payment-paypal">{{__('PaymentWall')}}</label>
                                            </div>
                                            <p class="text_sm text-muted mt-2 mb-0">
                                                {{__('Pay your order using the most known and secure platform for online money transfers. You will be redirected to PaymentWall to finish complete your purchase')}}.
                                            </p>
                                        </div>
                                        <div class="col-12 col-lg-4 col-md-4 text-right">
                                            <img alt="Image placeholder" src="{{asset('assets/img/Paymentwall.png')}}" width="100">
                                            <form class="w3-container w3-display-middle w3-card-4" method="POST" action="{{ route('paymentwall.session.store',$store->slug) }}">
                                                @csrf
                                                <input type="hidden" name="transaction_id" value="{{ date('Y-m-d') . strtotime('Y-m-d H:i:s') . 'user_id' }}">
                                                <input type="hidden" name="desc" value="{{time()}}">
                                                <div class="form-group mt-3">
                                                    <div class="text-sm-right ">
                                                        <button type="submit" class="btn btn-primary btn-sm float-right">{{__('Pay Now')}}</button>
                                                    </div>
                                                </div>
                                            </form>

                                        </div>

                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="mt-4 text-right">
                            {{--                            <a href="{{route('store.slug',$store->slug)}}" class="btn btn-link text-primary text-sm text-dark font-weight-bold">{{__('Return to shop')}}</a>--}}
                            <a href="{{route('store.slug',$store->slug)}}" class="btn btn-sm btn-primary rounded-pill btn-icon shadow hover-shadow-lg hover-translate-y-n3">
                                <span class="btn-inner--text">{{__('Return to shop')}}</span>
                                {{--                                <span class="btn-inner--icon">--}}
                                {{--                                    <i class="fas fa-shopping-basket"></i>--}}
                                {{--                                </span>--}}
                            </a>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-5">
                        <div data-toggle="sticky" data-sticky-offset="30">
                            <div class="card" id="card-summary">
                                <div class="card-header py-3">
                                    <div class="row align-items-center">
                                        <h3 class="ml-3 playfair store-title-medium t-secondary">{{__('Summary')}}</h3>
                                    </div>
                                </div>
                                <div class="card-body">
                                    @if(!empty($products))
                                        @php
                                            $total = 0;
                                            $sub_tax = 0;
                                            $sub_total= 0;
                                        @endphp
                                        @foreach($products as $product)
                                            @if($product['variant_id'] !=0)
                                                <div class="row mb-2 pb-2 delimiter-top">
                                                    <div class="col-9">
                                                        <div class="media align-items-center">
                                                            <img alt="Image placeholder" class="mr-2" src="{{asset($product['image'])}}" style="width: 42px;">
                                                            <div class="media-body">
                                                                <div class="text-limit lh-100">
                                                                    <small class="font-weight-bold mb-0">{{$product['product_name'].' - ( ' . $product['variant_name'] .' ) '}}</small>
                                                                </div>
                                                                @php
                                                                    $total_tax=0;
                                                                @endphp
                                                                <small class="text-muted">{{$product['quantity']}} x {{\App\Models\Utility::priceFormat($product['variant_price'])}}
                                                                    @if(!empty($product['tax']))
                                                                        +
                                                                        @foreach($product['tax'] as $tax)
                                                                            @php
                                                                                $sub_tax = ($product['variant_price'] * $product['quantity'] * $tax['tax']) / 100;
                                                                                $total_tax += $sub_tax;
                                                                            @endphp

                                                                            {{\App\Models\Utility::priceFormat($sub_tax).' ('.$tax['tax_name'].' '.($tax['tax']).'%)'}}
                                                                        @endforeach
                                                                    @endif
                                                                </small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-3 text-right lh-100">
                                                        <small class="text-dark">
                                                            @php
                                                                $totalprice = $product['variant_price'] * $product['quantity'] + $total_tax;
                                                                $subtotal = $product['variant_price'] * $product['quantity'];
                                                                $sub_total += $subtotal;
                                                            @endphp
                                                            {{\App\Models\Utility::priceFormat($totalprice)}}
                                                        </small>
                                                        @php
                                                            $total += $totalprice;
                                                        @endphp
                                                    </div>
                                                </div>
                                            @else
                                                <div class="row mb-2 pb-2 delimiter-top">
                                                    <div class="col-9">
                                                        <div class="media align-items-center">
                                                            <img alt="Image placeholder" class="mr-2" src="{{asset($product['image'])}}" style="width: 42px;">
                                                            <div class="media-body">
                                                                <div class="sum-title lh-100">
                                                                    <p class="mb-0">{{$product['product_name']}}</p>
                                                                </div>
                                                                @php
                                                                    $total_tax=0;
                                                                @endphp
                                                                <small class="text-muted s-dim">{{$product['quantity']}} x {{\App\Models\Utility::priceFormat($product['price'])}}
                                                                    @if(!empty($product['tax']))
                                                                        +
                                                                        @foreach($product['tax'] as $tax)
                                                                            @php
                                                                                $sub_tax = ($product['price'] * $product['quantity'] * $tax['tax']) / 100;
                                                                                $total_tax += $sub_tax;
                                                                            @endphp

                                                                            {{\App\Models\Utility::priceFormat($sub_tax).' ('.$tax['tax_name'].' '.$tax['tax'].'%)'}}
                                                                        @endforeach
                                                                    @endif
                                                                </small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-3 text-right lh-100">
                                                        <small class="text-dark">Price</small>
                                                        <p class="text-dark s-rate t-black15">
                                                            @php
                                                                $totalprice = $product['price'] * $product['quantity'] + $total_tax;
                                                                $subtotal = $product['price'] * $product['quantity'];
                                                                $sub_total += $subtotal;
                                                            @endphp
                                                            {{\App\Models\Utility::priceFormat($totalprice)}}
                                                        </p>
                                                        @php($total += $totalprice)
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    @endif
                                </div>
                                <div class="card-body cart-subtotal">
                                    <!-- Tax -->
                                    <div class="row mt-2 pt-2">
                                        <div class="col-8 text-right">
                                            <small class="font-weight-bold">{{__('Subtotal (Before Tax)')}} :</small>
                                        </div>
                                        <div class="col-4 text-right">
                                            <span class="text-sm font-weight-bold t-black15"> {{\App\Models\Utility::priceFormat(!empty($sub_total)?$sub_total:0)}}</span>

                                        </div>
                                    </div>
                                    @foreach($taxArr['tax'] as $k=>$tax)
                                        <div class="row mt-2 pt-2 border-top">
                                            <div class="col-8 text-right">
                                                <div class="media align-items-center">
                                                    <div class="media-body">
                                                        <div class="text-limit lh-100">
                                                            <small class="font-weight-bold mb-0">{{$tax}}</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-4 text-right">
                                                <span class="text-sm font-weight-bold t-black15">{{\App\Models\Utility::priceFormat($taxArr['rate'][$k])}}</span>
                                            </div>
                                        </div>
                                    @endforeach

                                <!-- Discount -->
                                    <div class="row mt-2 pt-2 border-top">
                                        <div class="col-8 text-right">
                                            <div class="media align-items-center">
                                                <div class="media-body">
                                                    <div class="text-limit lh-100">
                                                        <small class="font-weight-bold mb-0">{{__('Coupon')}} :</small>
                                                        <input type="hidden" name="coupon" class="form-control hidden_coupon" data_id="{{$coupon_id}}" value="{{$coupon_price}}"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-4 text-right">
                                            <span class="text-sm font-weight-bold t-black15 dicount_price">{{!empty($discount_price)?$discount_price:'0.00'}}</span>
                                        </div>
                                    </div>

                                    <!-- Shipping -->
                                    @if($store->enable_shipping == 'on')
                                        <div class="shipping_price_add">
                                            <div class="row mt-2 pt-2 border-top">
                                                <div class="col-8 text-right pt-2">
                                                    <div class="media align-items-center">
                                                        <div class="media-body">
                                                            <div class="text-dark lh-100 text-sm">{{__('Shipping Price')}} :</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-4 text-right"><span class="text-sm font-weight-bold t-black15 shipping_price" data-value="{{$shipping_price}}">{{\App\Models\Utility::priceFormat(!empty($shipping_price)?$shipping_price:0)}}</span></div>
                                            </div>
                                        </div>
                                @endif

                                <!-- Final total -->
                                    <div class="row mt-2 pt-2 border-top" id="Subtotal">
                                        <div class="col-8 text-right">
                                            <small class="text-uppercase font-weight-bold">{{__('Total')}} :</small>
                                        </div>
                                        <input type="hidden" class="product_total" value="{{$total}}">
                                        <div class="col-4 text-right final_total_price">
                                            <span class="text-sm font-weight-bold t-black15 total_price" data-value="{{$total+$shipping_price-$coupon_price}}"> {{\App\Models\Utility::priceFormat(!empty($total)?$total+$shipping_price-$coupon_price:0)}}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </div>
        </section>
    </main>
@endsection
@push('script-page')
    <script src="{{asset('custom/libs/jquery-mask-plugin/dist/jquery.mask.min.js')}}"></script>
     @if(isset($store_payments['is_stripe_enabled']) && $store_payments['is_stripe_enabled'] == 'on')
        <script src="https://js.stripe.com/v3/"></script>
        <script type="text/javascript">
            var stripe = Stripe('{{ isset($store_payments['stripe_key'])?$store_payments['stripe_key']:'' }}');
            var elements = stripe.elements();

            // Custom styling can be passed to options when creating an Element.
            var style = {
                base: {
                    // Add your base input styles here. For example:
                    fontSize: '14px',
                    color: '#32325d',
                },
            };

            // Create an instance of the card Element.
            var card = elements.create('card', {style: style});

            // Add an instance of the card Element into the `card-element` <div>.
            card.mount('#card-element');

            // Create a token or display an error when the form is submitted.
            var form = document.getElementById('payment-form');
            form.addEventListener('submit', function (event) {
                event.preventDefault();

                stripe.createToken(card).then(function (result) {
                    if (result.error) {
                        $("#card-errors").html(result.error.message);
                        show_toastr('Error', result.error.message, 'error');
                    } else {
                        // Send the token to your server.
                        stripeTokenHandler(result.token);
                    }
                });
            });

            function stripeTokenHandler(token) {
                // Insert the token ID into the form so it gets submitted to the server
                var form = document.getElementById('payment-form');
                var hiddenInput = document.createElement('input');
                hiddenInput.setAttribute('type', 'hidden');
                hiddenInput.setAttribute('name', 'stripeToken');
                hiddenInput.setAttribute('value', token.id);
                form.appendChild(hiddenInput);

                form.submit();
            }
        </script>
    @endif
    <script>
        $(document).on('click', '#owner-whatsapp', function () {
            var product_array = '{{$encode_product}}';
            var product = JSON.parse(product_array.replace(/&quot;/g, '"'));
            var order_id = '{{$order_id = time()}}';
            var total_price = $('#Subtotal .total_price').attr('data-value');
            var coupon_id = $('.hidden_coupon').attr('data_id');
            var dicount_price = $('.dicount_price').html();



            var data = {
                type:'whatsapp',
                coupon_id: coupon_id,
                dicount_price: dicount_price,
                total_price: total_price,
                product: product,
                order_id: order_id,
                wts_number: $('#wts_number').val()
            }
            getWhatsappUrl(dicount_price, total_price, coupon_id,data);

            $.ajax({
                url: '{{ route('user.whatsapp',$store->slug) }}',
                method: 'POST',
                data: data,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (data) {
                    if (data.status == 'success') {

                        removesession();
                        show_toastr(data["success"], '{!! session('+data["status"]+') !!}', data["status"]);

                        setTimeout(function () {
                            var url = '{{ route('store-complete.complete', [$store->slug, ":id"]) }}';
                            url = url.replace(':id', data.order_id);

                            window.location.href = url;
                        }, 1000);

                        setTimeout(function () {
                            var get_url_msg_url = $('#return_url').val();
                            var append_href = get_url_msg_url + '{{route('user.order',[$store->slug,Crypt::encrypt(!empty($order->id) ? $order->id + 1 : 0 + 1)])}}';
                            window.open(append_href, '_blank');
                        }, 20);


                    } else {
                        show_toastr("Error", data.success, data["status"]);
                    }

                }
            });

        });

        $(document).on('click', '#owner-telegram', function () {
            var product_array = '{{$encode_product}}';
            var product = JSON.parse(product_array.replace(/&quot;/g, '"'));
            var order_id = '{{$order_id = time()}}';
            var total_price = $('#Subtotal .total_price').attr('data-value');
            var coupon_id = $('.hidden_coupon').attr('data_id');
            var dicount_price = $('.dicount_price').html();


            var data = {
                type: 'telegram',
                coupon_id: coupon_id,
                dicount_price: dicount_price,
                total_price: total_price,
                product: product,
                order_id: order_id,
            }

            getWhatsappUrl(dicount_price, total_price, coupon_id,data);

            $.ajax({
                url: '{{ route('user.telegram',$store->slug) }}',
                method: 'POST',
                data: data,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (data) {
                    if (data.status == 'success') {

                        show_toastr(data["success"], '{!! session('+data["status"]+') !!}', data["status"]);

                        setTimeout(function () {

                            removesession();

                            var url = '{{ route('store-complete.complete', [$store->slug, ":id"]) }}';
                            url = url.replace(':id', data.order_id);

                            window.location.href = url;
                        }, 1000);

                    } else {
                        show_toastr("Error", data.success, data["status"]);
                    }
                }
            });
        });

        $(document).on('click', '#cash_on_delivery', function () {
            var product_array = '{{$encode_product}}';
            var product = JSON.parse(product_array.replace(/&quot;/g, '"'));
            var order_id = '{{$order_id = time()}}';

            var total_price = $('#Subtotal .total_price').attr('data-value');
            var coupon_id = $('.hidden_coupon').attr('data_id');
            var dicount_price = $('.dicount_price').html();

            var data = {
                coupon_id: coupon_id,
                dicount_price: dicount_price,
                total_price: total_price,
                product: product,
                order_id: order_id,
            }

            $.ajax({
                url: '{{ route('user.cod',$store->slug) }}',
                method: 'POST',
                data: data,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (data) {
                    if (data.status == 'success') {
                        show_toastr(data["success"], '{!! session('+data["status"]+') !!}', data["status"]);

                        setTimeout(function () {
                            var url = '{{ route('store-complete.complete', [$store->slug, ":id"]) }}';
                            url = url.replace(':id', data.order_id);
                            window.location.href = url;
                        }, 1000);
                        removesession();
                    } else {
                        show_toastr("Error", data.success, data["status"]);
                    }
                }
            });
        });

        $(document).on('click', '#bank_transfer', function () {
            var product_array = '{{$encode_product}}';
            var product = JSON.parse(product_array.replace(/&quot;/g, '"'));
            var order_id = '{{$order_id = time()}}';
            var total_price = $('#Subtotal .total_price').attr('data-value');
            var coupon_id = $('.hidden_coupon').attr('data_id');
            var dicount_price = $('.dicount_price').html();

            var data = {
                coupon_id: coupon_id,
                dicount_price: dicount_price,
                total_price: total_price,
                product: product,
                order_id: order_id,
            }
            $.ajax({
                url: '{{ route('user.bank_transfer',$store->slug) }}',
                method: 'POST',
                data: data,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (data) {
                    if (data.status == 'success') {

                        removesession();

                        show_toastr(data["success"], '{!! session('+data["status"]+') !!}', data["status"]);
                        setTimeout(function () {
                            var url = '{{ route('store-complete.complete', [$store->slug, ":id"]) }}';
                            url = url.replace(':id', data.order_id);
                            window.location.href = url;
                        }, 1000);
                    } else {
                        show_toastr("Error", data.success, data["status"]);
                    }
                }
            });
        });
    </script>
    <script>
        // Apply Coupon
        $(document).on('click', '.apply-coupon', function (e) {
            e.preventDefault();

            var ele = $(this);
            var coupon = ele.closest('.row').find('.coupon').val();
            var hidden_field = $('.hidden_coupon').val();
            var price = $('#card-summary .product_total').val();
            var shipping_price = $('#card-summary .shipping_price').attr('data-value');

            if (coupon == hidden_field) {
                show_toastr('Error', 'Coupon Already Used', 'error');
            } else {
                if (coupon != '') {
                    $.ajax({
                        url: '{{route('apply.productcoupon')}}',
                        datType: 'json',
                        data: {
                            price: price,
                            shipping_price: shipping_price,
                            store_id: {{$store->id}},
                            coupon: coupon
                        },
                        success: function (data) {
                            $('#stripe_coupon, #paypal_coupon').val(coupon);
                            if (data.is_success) {
                                $('.hidden_coupon').val(coupon);
                                $('.hidden_coupon').attr(data);

                                $('.dicount_price').html(data.discount_price);

                                var html = '';
                                html += '<span class="text-sm font-weight-bold total_price" data-value="' + data.final_price_data_value + '">' + data.final_price + '</span>'
                                $('.final_total_price').html(html);


                                // $('.coupon-tr').show().find('.coupon-price').text(data.discount_price);
                                // $('.final-price').text(data.final_price);
                                show_toastr('Success', data.message, 'success');
                            } else {
                                // $('.coupon-tr').hide().find('.coupon-price').text('');
                                // $('.final-price').text(data.final_price);
                                show_toastr('Error', data.message, 'error');
                            }
                        }
                    })
                } else {
                    show_toastr('Error', '{{__('Invalid Coupon Code.')}}', 'error');
                }
            }

        });

        //for create/get Whatsapp Url
        function getWhatsappUrl(coupon = '', finalprice = '', coupon_id = '', data = '') {
            $.ajax({
                url: '{{ route('get.whatsappurl',$store->slug) }}',
                method: 'post',
                data: {dicount_price: coupon, finalprice: finalprice, coupon_id: coupon_id,data:data},
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (data) {
                    if (data.status == 'success') {
                        $('#return_url').val(data.url);
                        $('#return_order_id').val(data.order_id);

                    } else {
                        $('#return_url').val('')
                        show_toastr("Error", data.success, data["status"]);
                    }
                }
            });
        }

        //for create/get Telegram Url
        function getTelegramUrl(coupon = '', finalprice = '', coupon_id = '', data = '') {
            $.ajax({
                url: '{{ route('get.whatsappurl',$store->slug) }}',
                method: 'post',
                data: {dicount_price: coupon, finalprice: finalprice, coupon_id: coupon_id,data:data},
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (data) {
                    if (data.status == 'success') {
                        $('#return_url').val(data.url);
                        $('#return_order_id').val(data.order_id);

                    } else {
                        $('#return_url').val('')
                        show_toastr("Error", data.success, data["status"]);
                    }
                }
            });
        }

        function removesession(slug) {
            $.ajax({
                url: '{{ route('remove.session',$store->slug) }}',
                method: 'get',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (data) {

                }
            });
        }
    </script>
@endpush
