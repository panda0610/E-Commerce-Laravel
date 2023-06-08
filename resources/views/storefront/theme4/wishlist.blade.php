@extends('storefront.layout.theme4')
@section('page-title')
    {{__('Wish list')}}
@endsection
@push('css-page')
@endpush
@section('content')
    <div class="top-product mb-0">
        <div class="container py-6">
            <div class="row top-product">
                @foreach($products as $k => $product)
                    <div class="col-xl-3 col-lg-4 col-sm-6 product-box wishlist_{{$product['product_id']}}">
                        <div class="card card-product">
                            <div class="card-image bg-white">
                                <a href="{{route('store.product.product_view',[$store->slug,$product['product_id']])}}">
                                    @if(!empty($product['image']))
                                        <img alt="Image placeholder" src="{{asset(!empty($product['image'])?$product['image']:'')}}" class="img-center img-fluid">
                                    @else
                                        <img src="{{asset(Storage::url('uploads/is_cover_image/default.jpg'))}}" class="img-center img-fluid">
                                    @endif
                                </a>
                            </div>
                            <div class="card-body pt-0">
                                <h6><a class="t-black13" href="#">{{$product['product_name']}}</a></h6>
                                <p class="text-sm">
                                    <span class="td-gray">{{__('Category')}}:</span> {{\App\Models\Product::getCategoryById($product['product_id'])}}
                                </p>
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
                                @if($product['enable_product_variant'] == 'on')
                                    <div class="product-price mt-3">
                                        <span class="card-price t-black15">{{__('In variant')}}</span>
                                    </div>
                                    <a href="{{route('store.product.product_view',[$store->slug,$product['product_id']])}}" type="button" class="action-item pcart-icon bg-primary">
                                        {{__('Add To Cart')}}
                                        <i class="ml-2 fas fa-shopping-basket"></i>
                                    </a>
                                @else
                                    <div class="product-price mt-3">
                                        <span class="card-price t-black15">{{\App\Models\Utility::priceFormat($product['price'])}}</span>
                                    </div>
                                    <a type="button" class="action-item pcart-icon bg-primary add_to_cart" data-id="{{$product['product_id']}}">
                                        {{__('Add To Cart')}}
                                        <i class="ml-2 fas fa-shopping-basket"></i>
                                    </a>
                                @endif
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
    </div>
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

        $(document).on('click', '.add_to_cart', function (e) {
            e.preventDefault();
            var id = $(this).attr('data-id');
            $.ajax({
                type: "POST",
                url: '{{route('user.addToCart', ['__product_id',$store->slug])}}'.replace('__product_id', id),
                data: {
                    "_token": "{{ csrf_token() }}",
                },
                success: function (response) {
                    if (response.status == "Success") {
                        show_toastr('Success', response.success, 'success');
                        $(".shoping_counts").attr("value", response.item_count);
                        $(".shoping_counts").html(response.item_count);
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
