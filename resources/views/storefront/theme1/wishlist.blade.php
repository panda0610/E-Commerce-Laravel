@extends('storefront.layout.theme1')
@section('page-title')
    {{__('Wish list')}}
@endsection
@push('css-page')
@endpush
@section('content')
    <section class="top-product">
        <div class="container">
            <div class="row">
                @foreach($products as $k => $product)
                    <div class="col-xl-3 col-lg-4 col-sm-6 product-box wishlist_{{$product['product_id']}}">
                        <div class="card card-product">
                            <div class="card-image">
                                <a href="{{route('store.product.product_view',[$store->slug,$product['product_id']])}}">
                                    @if(!empty($product['image']))
                                        <img alt="Image placeholder" src="{{asset($product['image'])}}" class="img-center img-fluid">
                                    @else
                                        <img alt="Image placeholder" src="{{asset(Storage::url('uploads/is_cover_image/default.jpg'))}}" class="img-center img-fluid">
                                    @endif
                                </a>
                            </div>
                            <div class="card-body pt-0">
                                <span class="static-rating static-rating-sm">
                                    @if($store['enable_rating'] == 'on')
                                        @for($i =1;$i<=5;$i++)
                                            @php
                                                $icon = 'fa-star';
                                                $color = '';
                                                $newVal1 = ($i-0.5);
                                                if(\App\Models\Product::getRatingById($product['product_id']) < $i && \App\Models\Product::getRatingById($product['product_id']) >= $newVal1)
                                                {
                                                    $icon = 'fa-star-half-alt';
                                                }
                                                if(\App\Models\Product::getRatingById($product['product_id']) >= $newVal1)
                                                {
                                                    $color = 'text-warning';
                                                }
                                            @endphp
                                            <i class="star fas {{$icon .' '. $color}}"></i>
                                        @endfor
                                    @endif
                                </span>
                                <h6><a class="t-black13" href="{{route('store.product.product_view',[$store->slug,$product['product_id']])}}">{{$product['product_name']}}</a></h6>
                                <p class="text-sm">
                                    <span class="td-gray">{{__('Category')}}:</span> {{\App\Models\Product::getCategoryById($product['product_id'])}}
                                </p>
                                <div class="product-price mt-3">
                                    <span class="card-price t-black15">
                                        @if($product['enable_product_variant'] == 'on')
                                            {{__('In variant')}}
                                        @else
                                            {{\App\Models\Utility::priceFormat($product['price'])}}
                                        @endif
                                    </span>
                                        @if($product['enable_product_variant'] == 'on')
                                            <a href="{{route('store.product.product_view',[$store->slug,$product['product_id']])}}" class="action-item pcart-icon bg-primary" >
                                                <i class="fas fa-shopping-basket"></i>
                                            </a>
                                        @else
                                            <a href="javascript:void(0)" class="action-item pcart-icon bg-primary add_to_cart" data-id="{{$product['product_id']}}">
                                                <i class="fas fa-shopping-basket"></i>
                                            </a>
                                        @endif
                                    </div>
                            </div>
                            <div class="actions card-product-actions">
                                <button type="button" class="action-item wishlist-icon bg-light-gray delete_wishlist_item" id="delete_wishlist_item1" data-id="{{$product['product_id']}}">
                                    <i class="fas fa-heart"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection
@push('script-page')
    <script>
        $(document).on('click', '.delete_wishlist_item', function (e) {
            e.preventDefault();
            var id = $(this).attr('data-id');

            $.ajax({
                type: "DELETE",
                url: '{{route('delete.wishlist_item', [$store->slug,'__product_id'])}}'.replace('__product_id', id),
                data: {
                    "_token": "{{ csrf_token() }}",
                },
                success: function (response) {
                    if (response.status == "success") {
                        show_toastr('Success', response.message, 'success');
                        $('.wishlist_' + response.id).remove();
                        $('.wishlist_count').html(response.count);

                    } else {
                        show_toastr('Error', response.message, 'error');
                    }
                },
                error: function (result) {
                }
            });
        });

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
    </script>
@endpush
