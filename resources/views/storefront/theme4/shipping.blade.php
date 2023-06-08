@extends('storefront.layout.theme4')
@section('page-title')
    {{__('Shipping')}}
@endsection
@section('content')
    <main>
        <section class="my-cart-section">
            <div class="container">
                <!-- Shopping cart table -->
                <div class="row">
                    <div class="pr-title mb-4">
                        <h3 class=" mt-4 store-title text-primary">{{__('My Cart')}}</h3>
                        <div class="payment-step">
                            <a href="{{route('store.cart',$store->slug)}}" class="btn btn-mycart">1 - {{__('My Cart')}}</a>
                            <a href="{{route('user-address.useraddress',$store->slug)}}" class="btn btn-mycart active">2 - {{__('Customer')}}</a>
                            <a href="{{route('store-payment.payment',$store->slug)}}" class="btn btn-mycart">3 - {{__('Payment')}}</a>
                        </div>
                    </div>
                </div>
                {{Form::model($cust_details,array('route' => array('store.customer',$store->slug), 'method' => 'POST')) }}
                <div class="row row-grid">
                    <div class="col-xl-8 col-lg-7">
                        <!-- General -->
                        <div class="actions-toolbar py-2 mb-4">
                            <h5 class="mb-1 text-primary">{{__('Billing information')}}</h5>
                            <p class="text-sm text-muted mb-0">{{__('Fill the form below so we can send you the orders invoice.')}}</p>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    {{Form::label('name',__('First Name'),array("class"=>"form-control-label")) }}
                                    {{Form::text('name',old('name'),array('class'=>'form-control','placeholder'=>__('Enter Your First Name'),'required'=>'required'))}}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    {{Form::label('last_name',__('Last Name'),array("class"=>"form-control-label")) }}
                                    {{Form::text('last_name',old('last_name'),array('class'=>'form-control','placeholder'=>__('Enter Your Last Name'),'required'=>'required'))}}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    {{Form::label('phone',__('Phone'),array("class"=>"form-control-label")) }}
                                    {{Form::text('phone',old('phone'),array('class'=>'form-control','placeholder'=>'(99) 12345 67890','required'=>'required'))}}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    {{Form::label('email',__('Email'),array("class"=>"form-control-label")) }}
                                    {{Form::email('email',Auth::guard('customers')->user()->email,array('class'=>'form-control','placeholder'=>__('Enter Your Email Address'),'required'=>'required','readonly'=>'true'))}}
                                </div>
                            </div>


                            @if(!empty($store_payment_setting['custom_field_title_1']))
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {{Form::label('custom_field_title_1',$store_payment_setting['custom_field_title_1'],array("class"=>"form-control-label")) }}
                                        {{Form::text('custom_field_title_1',old('custom_field_title_1'),array('class'=>'form-control','placeholder'=>'Enter '.$store_payment_setting['custom_field_title_1'],'required'=>'required'))}}
                                    </div>
                                </div>
                            @endif
                            @if(!empty($store_payment_setting['custom_field_title_2']))
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {{Form::label('custom_field_title_2',$store_payment_setting['custom_field_title_2'],array("class"=>"form-control-label")) }}
                                        {{Form::text('custom_field_title_2',old('custom_field_title_2'),array('class'=>'form-control','placeholder'=>'Enter '.$store_payment_setting['custom_field_title_1'],'required'=>'required'))}}
                                    </div>
                                </div>
                            @endif
                            @if(!empty($store_payment_setting['custom_field_title_3']))
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {{Form::label('custom_field_title_3',$store_payment_setting['custom_field_title_3'],array("class"=>"form-control-label")) }}
                                        {{Form::text('custom_field_title_3',old('custom_field_title_3'),array('class'=>'form-control','placeholder'=>'Enter '.$store_payment_setting['custom_field_title_1'],'required'=>'required'))}}
                                    </div>
                                </div>
                            @endif
                            @if(!empty($store_payment_setting['custom_field_title_4']))
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {{Form::label('custom_field_title_4',$store_payment_setting['custom_field_title_4'],array("class"=>"form-control-label")) }}
                                        {{Form::text('custom_field_title_4',old('custom_field_title_4'),array('class'=>'form-control','placeholder'=>'Enter '.$store_payment_setting['custom_field_title_1'],'required'=>'required'))}}
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="row align-items-center">
                            <div class="col-md-12">
                                <div class="form-group">
                                    {{Form::label('billingaddress',__('Address'),array("class"=>"form-control-label")) }}
                                    {{Form::text('billing_address',old('billing_address'),array('class'=>'form-control','placeholder'=>__('Billing Address'),'required'=>'required'))}}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    {{Form::label('billing_country',__('Country'),array("class"=>"form-control-label")) }}
                                    {{Form::text('billing_country',old('billing_country'),array('class'=>'form-control','placeholder'=>__('Billing Country'),'required'=>'required'))}}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    {{Form::label('billing_city',__('City'),array("class"=>"form-control-label")) }}
                                    {{Form::text('billing_city',old('billing_city'),array('class'=>'form-control','placeholder'=>__('Billing City'),'required'=>'required'))}}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    {{Form::label('billing_postalcode',__('Postal Code'),array("class"=>"form-control-label")) }}
                                    {{Form::text('billing_postalcode',old('billing_postalcode'),array('class'=>'form-control','placeholder'=>__('Billing Postal Code')))}}
                                </div>
                            </div>

                            @if($store->enable_shipping == "on" && $shippings->count() > 0)
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {{Form::label('location_id',__('Location'),array("class"=>"form-control-label")) }}
                                        {{ Form::select('location_id', $locations, null,array('class' => 'form-control change_location','required'=>'required')) }}
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="actions-toolbar py-2 mb-4 shop-title">
                            <div class="left-cart">
                                <h5 class="mb-1 text-primary">{{__('Shipping informations')}}</h5>
                                <p class="text-sm text-muted mb-0">{{__('Fill the form below so we can send you the orders invoice.')}}</p>
                            </div>
                            <a class="btn btn-sm btn-primary rounded-pill btn-icon shadow hover-shadow-lg hover-translate-y-n3" onclick="billing_data()" id="billing_data" data-toggle="tooltip" data-placement="top" title="Same As Billing Address">
                                <span class="btn-inner--text">{{__('Copy Address')}}</span>
                            </a>
                        </div>
                        <div class="row align-items-center">
                            <div class="col-md-12">
                                <div class="form-group">
                                    {{Form::label('shipping_address',__('Address'),array("class"=>"form-control-label")) }}
                                    {{Form::text('shipping_address',old('shipping_address'),array('class'=>'form-control','placeholder'=>__('Shipping Address')))}}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    {{Form::label('shipping_country',__('Country'),array("class"=>"form-control-label")) }}
                                    {{Form::text('shipping_country',old('shipping_country'),array('class'=>'form-control','placeholder'=>__('Shipping Country')))}}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    {{Form::label('shipping_city',__('City'),array("class"=>"form-control-label")) }}
                                    {{Form::text('shipping_city',old('shipping_city'),array('class'=>'form-control','placeholder'=>__('Shipping City')))}}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    {{Form::label('shipping_postalcode',__('Postal Code'),array("class"=>"form-control-label")) }}
                                    {{Form::text('shipping_postalcode',old('shipping_postalcode'),array('class'=>'form-control','placeholder'=>__('Shipping Postal Code')))}}
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 text-right">
                            <a href="{{route('store.slug',$store->slug)}}" class="btn btn-link text-sm text-dark font-weight-bold">{{__('Return to shop')}}</a>
                            <button type="submit" href="#" class="btn btn-sm btn-primary rounded-pill btn-icon shadow hover-shadow-lg hover-translate-y-n3">
                                <span class="btn-inner--text">{{__('Next step')}}</span>
                                <span class="btn-inner--icon">

                                </span>
                            </button>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-5">
                        <div id="location_hide" style="display: none">
                            <div class="card round-card">
                                <div class="card-header">
                                    <h6>{{__('Select Shipping')}}</h6>
                                </div>
                                <div class="card-body" id="shipping_location_content">
                                </div>
                            </div>
                        </div>
                        <div class="card round-card">
                            <div class="col-md-10">
                                <br>
                                <div class="form-group">
                                    <label for="stripe_coupon">{{__('Coupon')}}</label>
                                    <input type="text" id="stripe_coupon" name="coupon" class="form-control coupon hidd_val" placeholder="{{ __('Enter Coupon Code') }}">
                                    <input type="hidden" name="coupon" class="form-control hidden_coupon" value="">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group apply-stripe-btn-coupon">
                                    <a href="#" class="btn btn-primary apply-coupon btn-sm">{{ __('Apply') }}</a>
                                </div>
                            </div>
                        </div>
                        <div data-toggle="sticky" data-sticky-offset="30">
                            <div class="card round-card shadow" id="card-summary">
                                <div class="card-header b-0 py-3">
                                    <div class="row align-items-center">
                                        <h3 class="ml-3 store-title-medium text-primary">{{__('Summary')}}</h3>
                                    </div>
                                </div>
                                <div class="card-body b-bottom">
                                    @if(!empty($products))
                                        @php
                                            $total = 0;
                                            $sub_tax = 0;
                                            $sub_total= 0;
                                        @endphp
                                        @foreach($products as $product)
                                            @if(isset($product['variant_id']) && !empty($product['variant_id']))
                                                <div class="row align-items-center mt-2 pt-2 delimiter-top">
                                                    <div class="col-8">
                                                        <div class="media align-items-center">
                                                            <img alt="Image placeholder" class="mr-2" src="{{asset($product['image'])}}" style="width: 42px;">
                                                            <div class="media-body">
                                                                <div class="sum-title lh-100">
                                                                    <small class="font-weight-bold mb-0">{{$product['product_name'].' - ( ' . $product['variant_name'] .' ) '}}</small>
                                                                </div>
                                                                @php
                                                                    $total_tax=0;
                                                                @endphp
                                                                <small class="text-muted s-dim">
                                                                    {{$product['quantity']}} x {{\App\Models\Utility::priceFormat($product['variant_price'])}}
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
                                                    <div class="col-4 text-right lh-100">
                                                        <small class="text-dark">{{__('Price')}}</small>
                                                        <p class="text-dark s-rate t-black15">
                                                            @php
                                                                $totalprice = $product['variant_price'] * $product['quantity'] + $total_tax;
                                                                $subtotal = $product['variant_price'] * $product['quantity'];
                                                                $sub_total += $subtotal;
                                                            @endphp
                                                            {{\App\Models\Utility::priceFormat($totalprice)}}
                                                        </p>
                                                        @php
                                                            $total += $totalprice;
                                                        @endphp
                                                    </div>
                                                </div>
                                            @else
                                                <div class="row align-items-center mt-2 pt-2 delimiter-top">
                                                    <div class="col-8">
                                                        <div class="media align-items-center">
                                                            <img alt="Image placeholder" class="mr-2" src="{{asset($product['image'])}}" style="width: 42px;">
                                                            <div class="media-body">
                                                                <div class="sum-title lh-100">
                                                                    <small class="font-weight-bold mb-0">{{$product['product_name']}}</small>
                                                                </div>
                                                                @php
                                                                    $total_tax=0;
                                                                @endphp
                                                                <small class="text-muted s-dim">
                                                                    {{$product['quantity']}} x {{\App\Models\Utility::priceFormat($product['price'])}}
                                                                    @if(!empty($product['tax']))
                                                                        +
                                                                        @foreach($product['tax'] as $tax)
                                                                            @php
                                                                                $sub_tax = ($product['price'] * $product['quantity'] * $tax['tax']) / 100;
                                                                                $total_tax += $sub_tax;
                                                                            @endphp

                                                                            {{\App\Models\Utility::priceFormat($sub_tax).' ('.$tax['tax_name'].' '.($tax['tax']).'%)'}}
                                                                        @endforeach
                                                                    @endif
                                                                </small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-4 text-right lh-100">
                                                        <small class="text-dark">{{__('Price')}}</small>
                                                        <p class="text-dark s-rate t-black15">
                                                            @php
                                                                $totalprice = $product['price'] * $product['quantity'] + $total_tax;
                                                                $subtotal = $product['price'] * $product['quantity'];
                                                                $sub_total += $subtotal;
                                                            @endphp
                                                            {{\App\Models\Utility::priceFormat($totalprice)}}
                                                        </p>
                                                        @php
                                                            $total += $totalprice;
                                                        @endphp
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                </div>
                                <!-- Tax -->
                                <div class="card-body pt-0">
                                    <div class="row mt-2 pt-2 p-2 border-top">
                                        <div class="col-7 text-right">
                                            <small class="font-weight-bold">{{ __('Subtotal (Before Tax)')}} :</small>
                                        </div>
                                        <div class="col-5 text-right">
                                            <span class="text-sm font-weight-bold t-black15">{{\App\Models\Utility::priceFormat(!empty($sub_total)?$sub_total:0)}}</span>
                                        </div>
                                    </div>

                                    @foreach($taxArr['tax'] as $k=>$tax)
                                        <div class="row mt-2 pt-2 p-2 border-top">
                                            <div class="col-7 text-right">
                                                <div class="media align-items-center">
                                                    <div class="media-body">
                                                        <div class="text-limit lh-100">
                                                            <small class="font-weight-bold mb-0">{{$tax}}</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-5 text-right">
                                                <span class="text-sm font-weight-bold t-black15">{{\App\Models\Utility::priceFormat($taxArr['rate'][$k])}}</span>
                                            </div>
                                        </div>
                                    @endforeach

                                <!-- Coupon -->
                                    <div class="row mt-2 pt-2 p-2 border-top">
                                        <div class="col-7 text-right">
                                            <div class="media align-items-center">
                                                <div class="media-body">
                                                    <div class="text-limit lh-100">
                                                        <small class="font-weight-bold mb-0">{{__('Coupon')}} :</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-5 text-right">
                                            <span class="text-sm font-weight-bold dicount_price t-black15">{{\App\Models\Utility::priceFormat(0)}}</span>
                                        </div>
                                    </div>

                                    <!-- Shipping -->
                                    @if($store->enable_shipping == "on")
                                        <div class="shipping_price_add" style="display: none">
                                            <div class="row mt-2 pt-2 p-2 border-top">
                                                <div class="col-7 text-right pt-2">
                                                    <small class="font-weight-bold mb-0">{{__('Shipping Price')}} :</small>
                                                </div>
                                                <div class="col-5 text-right"><span class="text-sm font-weight-bold t-black15 shipping_price" data-value=""></span></div>
                                            </div>
                                        </div>
                                @endif

                                <!-- Final total -->
                                    <div class="row mt-2 pt-2 border-top">
                                        <input type="hidden" class="product_total" value="{{$total}}">
                                        <input type="hidden" class="total_pay_price" value="{{App\Models\Utility::priceFormat($total)}}">
                                        <div class="col-7 text-right">
                                            <small class="text-uppercase font-weight-bold ">{{__('Total')}} :</small>
                                        </div>
                                        <div class="col-5 text-right final_total_price">
                                            <span class="text-sm font-weight-bold s-p-total pro_total_price t-black15" data-original="{{\App\Models\Utility::priceFormat(!empty($total)?$total:0)}}"> {{\App\Models\Utility::priceFormat(!empty($total)?$total:'0')}}</span>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{Form::close()}}
            </div>
        </section>
    </main>
@endsection
@push('script-page')
    <script>
        function billing_data() {
            $("[name='shipping_address']").val($("[name='billing_address']").val());
            $("[name='shipping_city']").val($("[name='billing_city']").val());
            $("[name='shipping_state']").val($("[name='billing_state']").val());
            $("[name='shipping_country']").val($("[name='billing_country']").val());
            $("[name='shipping_postalcode']").val($("[name='billing_postalcode']").val());
        }

        $(document).ready(function () {
            $('.change_location').trigger('change');

            setTimeout(function () {
                var shipping_id = $("input[name='shipping_id']:checked").val();
                getTotal(shipping_id);
            }, 200);
        });

        $(document).on('change', '.shipping_mode', function () {
            var shipping_id = this.value;
            getTotal(shipping_id);
        });

        function getTotal(shipping_id) {
            var pro_total_price = $('.pro_total_price').attr('data-original');
            if (shipping_id == undefined) {
                $('.shipping_price_add').hide();
                return false
            } else {
                $('.shipping_price_add').show();
            }

            $.ajax({
                url: '{{ route('user.shipping', [$store->slug,'_shipping'])}}'.replace('_shipping', shipping_id),
                data: {
                    "pro_total_price": pro_total_price,
                    "_token": "{{ csrf_token() }}",
                },
                method: 'POST',
                context: this,
                dataType: 'json',

                success: function (data) {
                    var price = data.price + pro_total_price;
                    $('.shipping_price').html(data.price);
                    $('.shipping_price').attr('data-value', data.price);
                    $('.pro_total_price').html(data.total_price);
                }
            });
        }

        $(document).on('change', '.change_location', function () {
            var location_id = $('.change_location').val();

            if (location_id == 0) {
                $('#location_hide').hide();

            } else {
                $('#location_hide').show();

            }

            $.ajax({
                url: '{{ route('user.location', [$store->slug,'_location_id'])}}'.replace('_location_id', location_id),
                data: {
                    "_token": "{{ csrf_token() }}",
                },
                method: 'POST',
                context: this,
                dataType: 'json',

                success: function (data) {
                    var html = '';
                    var shipping_id = '{{(isset($cust_details['shipping_id']) ? $cust_details['shipping_id'] : '')}}';
                    $.each(data.shipping, function (key, value) {
                        var checked = '';
                        if (shipping_id != '' && shipping_id == value.id) {
                            checked = 'checked';
                        }

                        html += '<div class="shipping_location"><input type="radio" name="shipping_id" data-id="' + value.price + '" value="' + value.id + '" id="shipping_price' + key + '" class="shipping_mode" ' + checked + '>' +
                            ' <label name="shipping_label" for="shipping_price' + key + '" class="shipping_label"> ' + value.name + '</label></div>';

                    });
                    $('#shipping_location_content').html(html);
                }
            });
        });

        $(document).on('click', '.apply-coupon', function (e) {
            e.preventDefault();

            var ele = $(this);
            var coupon = ele.closest('.row').find('.coupon').val();
            var hidden_field = $('.hidden_coupon').val();
            var price = $('#card-summary .product_total').val();
            var shipping_price = $('#card-summary .shipping_price').attr('data-value');

            if (coupon == hidden_field && coupon != "") {
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
                                html += '<span class="text-sm font-weight-bold s-p-total pro_total_price" data-original="' + data.final_price_data_value + '">' + data.final_price + '</span>'
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
                    $.ajax({
                        url: '{{route('apply.removecoupn')}}',
                        datType: 'json',
                        data: {
                            price: "price",
                            shipping_price: "shipping_price",
                            slug:{{$store->id}} ,
                            coupon: "coupon"
                        },
                        success: function (data) {
                        }
                    });
                    var hidd_cou = $('.hidd_val').val();
                  
                    if(hidd_cou == ""){
                       var total_pa_val =  $(".total_pay_price").val();
                       $(".final_total_price").html(total_pa_val);
                       $(".dicount_price").html(0.00);
                      
                    }
                    show_toastr('Error', '{{__('Invalid Coupon Code.')}}', 'error');
                }
            }

        });
    </script>
@endpush
