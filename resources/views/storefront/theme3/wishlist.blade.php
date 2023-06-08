@extends('storefront.layout.theme3')
@section('page-title')
    {{__('Wish list')}}
@endsection
@push('css-page')
@endpush
@section('content')
    <section class="top-product mt-10">
        <div class="container">
            <div class="row">
                @foreach($products as $k => $product)
                    <div class="col-xl-3 col-lg-4 col-sm-6 product-box wishlist_{{$product['product_id']}}">
                        <div class="card card-product">
                            <div class="card-image">
                                <a href="{{route('store.product.product_view',[$store->slug,$product['product_id']])}}">
                                    @if(!empty($product['image']))
                                        <img alt="Image placeholder" src="{{asset($product['image'])}}" class="img-center img-fluid" style="width:auto; height:221px;">
                                    @else
                                        <img src="{{asset(Storage::url('uploads/is_cover_image/default.jpg'))}}" class="img-center img-fluid" style="width:auto; height:221px;">
                                    @endif
                                </a>
                            </div>
                            <div class="card-body pt-4">
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
                            </div>
                            <div class="card-body pt-0">
                                @if($product['enable_product_variant'] == 'on')
                                    <div class="product-price">
                                        <a href="{{route('store.product.product_view',[$store->slug,$product['product_id']])}}">
                                            <button type="button" class="btn btn-sm btn-primary rounded-pill btn-icon hover-translate-y-n3">
                                                {{__('In variant')}}
                                                <i class="fas fa-shopping-basket"></i>
                                            </button>
                                        </a>
                                        <button type="button" class="ml-1 btn btn-sm btn-primary rounded-pill btn-icon hover-translate-y-n3 delete_wishlist_item" id="delete_wishlist_item1" data-id="{{$product['product_id']}}">
                                            <i class="fas fa-heart"></i>
                                        </button>
                                    </div>
                                @else
                                    <div class="product-price">
                                        <span class="card-price t-black15">{{\App\Models\Utility::priceFormat($product['price'])}}</span>
                                        <button type="button" class="btn btn-sm btn-primary rounded-pill btn-icon hover-translate-y-n3 add_to_cart" data-id="{{$product['product_id']}}">
                                            <i class="fas fa-shopping-basket"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-primary rounded-pill btn-icon hover-translate-y-n3 delete_wishlist_item" id="delete_wishlist_item1" data-id="{{$product['product_id']}}">
                                            <i class="fas fa-heart"></i>
                                        </button>
                                    </div>
                                @endif
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
    </script>
@endpush
