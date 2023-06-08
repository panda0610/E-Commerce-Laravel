@extends('storefront.layout.theme4')
@section('page-title')
    {{__('Cart')}}
@endsection
@section('content')
@section('head-title')
    {{__('Welcome').', '.\Illuminate\Support\Facades\Auth::guard('customers')->user()->name}}
@endsection
@section('content')
    @if($storethemesetting['enable_header_img'] == 'on')
        <section class="slice slice-xl bg-cover bg-size--cover home-banner" data-offset-top="#header-main" style="background-image: url({{asset(Storage::url('uploads/store_logo/'.(!empty($storethemesetting['header_img'])?$storethemesetting['header_img']:'home-banner.png')))}}); background-position: center center;">
            <div class="container py-6">
                <div class="row">
                    <div class="col-xl-5 col-lg-6 col-md-8">
                        <h2 class="h1 text-white store-title mb-5">
                            {{__('Products you purchased')}}
                        </h2>
                        <p class="lead text-white mt-4 w-75">
                            
                        </p>
                        <div class="two-button">
                            <a href="{{route('store.slug',$store->slug)}}" class="big-btn bg-white rounded-pill hover-translate-y-n3 mt-50 d-inline-block" id="pro_scroll">
                                <span class="nav-text">{{__('Back to home')}}</span>
                                <i class="fas fa-shopping-basket"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif

    
    <section class="slice slice-lg delimiter-bottom">
        <div class="container">
            
                @if(!empty($orders) && count($orders) > 0)
                    <div class="card">
                        <div class="table-responsive">
                            <table class="table align-items-center">
                                <thead>
                                <tr>
                                    <th scope="col">{{__('Order')}}</th>
                                    <th scope="col" class="sort">{{__('Date')}}</th>
                                    <th scope="col" class="sort">{{__('Value')}}</th>
                                    <th scope="col" class="sort">{{__('Payment Type')}}</th>
                                    <th scope="col" class="text-right">{{__("Action")}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($orders as $order)
                                    <tr>
                                        <th scope="row">
                                            <a href="{{route('customer.order',[$store->slug,Crypt::encrypt($order->id)])}}" class="btn btn-sm btn-white btn-icon rounded-pill text-dark">
                                                <span class="btn-inner--text">{{'#'.$order->order_id}}</span>
                                            </a>
                                        </th>
                                        <td class="order">
                                            <span class="h6 text-sm font-weight-bold mb-0">{{\App\Models\Utility::dateFormat($order->created_at)}}</span>
                                        </td>
                                       
                                        <td>
                                            <span class="value text-sm mb-0">{{\App\Models\Utility::priceFormat($order->price)}}</span>
                                        </td>
                                        <td>
                                            <span class="taxes text-sm mb-0">{{$order->payment_type}}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center justify-content-end">
                                                @if($order->status != 'Cancel Order')
                                                    <button type="button" class="btn btn-sm {{($order->status == 'pending')?'btn-soft-info':'btn-soft-success'}} btn-icon rounded-pill">
                                                        <span class="btn-inner--icon">
                                                         @if($order->status == 'pending')
                                                                <i class="fas fa-check soft-success"></i>
                                                            @else
                                                                <i class="fa fa-check-double soft-success"></i>
                                                            @endif
                                                        </span>
                                                        @if($order->status == 'pending')
                                                            <span class="btn-inner--text">
                                                                {{__('Pending')}}: {{\App\Models\Utility::dateFormat($order->created_at)}}
                                                            </span>
                                                        @else
                                                            <span class="btn-inner--text">
                                                                {{__('Delivered')}}: {{\App\Models\Utility::dateFormat($order->updated_at)}}
                                                            </span>
                                                        @endif
                                                    </button>
                                                @else
                                                    <button type="button" class="btn btn-sm btn-soft-danger btn-icon rounded-pill">
                                                        <span class="btn-inner--icon">
                                                            @if($order->status == 'pending')
                                                                <i class="fas fa-check soft-success"></i>
                                                            @else
                                                                <i class="fa fa-check-double soft-success"></i>
                                                            @endif
                                                        </span>
                                                        <span class="btn-inner--text">
                                                            {{__('Cancel Order')}}: {{\App\Models\Utility::dateFormat($order->created_at)}}
                                                        </span>
                                                    </button>
                                            @endif
                                            <!-- Actions -->
                                                <div class="actions ml-3">
                                                    <a href="{{route('customer.order',[$store->slug,Crypt::encrypt($order->id)])}}" class="action-item mr-2"  data-toggle="tooltip" data-title="{{__('Details')}}">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @else
                    <tr>
                        <td colspan="7">
                            <div class="text-center">
                                <i class="fas fa-folder-open text-gray" style="font-size: 48px;"></i>
                                <h2>{{ __('Opps...') }}</h2>
                                <h6> {!! __('No data Found.') !!} </h6>
                            </div>
                        </td>
                    </tr>
                @endif
            
        </div>
    </section>
@endsection
@push('script-page')
    <script>
        $(".add_to_cart").click(function (e) {
            e.preventDefault();
            var id = $(this).attr('data-id');
            var variants = [];
            $(".variant-selection").each(function (index, element) {
                variants.push(element.value);
            });

            if (jQuery.inArray('', variants) != -1) {
                show_toastr('Error', "{{ __('Please select all option.') }}", 'error');
                return false;
            }
            var variation_ids = $('#variant_id').val();

            $.ajax({
                url: '{{route('user.addToCart', ['__product_id',$store->slug,'variation_id'])}}'.replace('__product_id', id).replace('variation_id', variation_ids ?? 0),
                type: "POST",
                data: {
                    "_token": "{{ csrf_token() }}",
                    variants: variants.join(' : '),
                },
                success: function (response) {
                    if (response.status == "Success") {
                        show_toastr('Success', response.success, 'success');
                        $("#shoping_counts").html(response.item_count);
                    } else {
                        show_toastr('Error', response.error, 'error');
                    }
                },
                error: function (result) {
                    console.log('error');
                }
            });
        });

        $(document).on('click', '.add_to_wishlist', function (e) {
            e.preventDefault();
            var id = $(this).attr('data-id');
            $.ajax({
                type: "POST",
                url: '{{route('store.addtowishlist', [$store->slug,'__product_id'])}}'.replace('__product_id', id),
                data: {
                    "_token": "{{ csrf_token() }}",
                },
                success: function (response) {
                    if (response.status == "Success") {
                        show_toastr('Success', response.message, 'success');
                        $('.wishlist_' + response.id).removeClass('add_to_wishlist');
                        $('.wishlist_' + response.id).html('<i class="fas fa-heart"></i>');
                        $('.wishlist_count').html(response.count);
                    } else {
                        show_toastr('Error', response.message, 'error');
                    }
                },
                error: function (result) {
                }
            });
        });

        $(".productTab").click(function (e) {
            e.preventDefault();
            $('.productTab').removeClass('active')

        });

    $("#pro_scroll").click(function () {
        $('html, body').animate({
            scrollTop: $("#pro_items").offset().top
        }, 1000);
    });

   
    </script>
@endpush
