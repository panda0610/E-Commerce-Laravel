@extends('storefront.layout.theme5')
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
                    <div class="col-xl-3 col-lg-4 col-sm-6 wishlist_{{$product['product_id']}}">
                        <div class="product-box">
                            <div class="card card-product">
                                <div class="box-rate">
                                    <div class="static-rating static-rating-sm">
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
                                    </div>
                                    <div class="card-product-actions">
                                        <button type="button" class="action-item wishlist-icon bg-light-gray delete_wishlist_item" id="delete_wishlist_item1" data-id="{{$product['product_id']}}">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-image py-3">
                                    <a href="{{route('store.product.product_view',[$store->slug,$product['product_id']])}}">
                                        @if(!empty($product['image']))
                                            <img class="img-center img-fluid" style="width:135px; height:167px" src="{{asset(!empty($product['image'])?$product['image']:'')}}" alt="New collection" title="New collection">
                                        @else
                                            <img src="{{asset(Storage::url('uploads/is_cover_image/default.jpg'))}}" class="img-center img-fluid">
                                        @endif
                                    </a>
                                </div>
                                <div class="card-body pt-0">
                                    <h6><a href="{{route('store.product.product_view',[$store->slug,$product['product_id']])}}" class="t-black13">{{$product['product_name']}}</a></h6>
                                    @if($product['enable_product_variant'] != 'on')
                                        <div class="product-price mt-3">
                                            <span class="card-price t-black15 mb-2">{{\App\Models\Utility::priceFormat($product['price'])}}</span>
                                        </div>
                                    @else
                                        <div class="product-price mt-3">
                                            <span class="card-price t-black15 mb-2">{{__('In Variant')}}</span>
                                        </div>
                                    @endif

                                    <div class="p-button">
                                        <button type="button" class="action-item pcart-icon bg-primary">
                                            <i class="fas fa-shopping-basket"></i>
                                        </button>
                                        @if($product['enable_product_variant'] == 'on')
                                            <a href="{{route('store.product.product_view',[$store->slug,$product['product_id']])}}" class="btn btn-sm btn-white btn-icon">
                                               <span class="btn-inner--text text-primary">
                                                    {{__('Add to cart')}}
                                                </span>
                                                <span class="btn-inner--icon">
                                                    <i class="fas fa-shopping-basket"></i>
                                                </span>
                                            </a>
                                        @else
                                            <a type="button" class="btn btn-sm btn-white btn-icon add_to_cart" data-id="{{$product['product_id']}}">
                                           <span class="btn-inner--text text-primary">
                                                {{__('Add to cart')}}
                                            </span>
                                                <span class="btn-inner--icon">
                                                <i class="fas fa-shopping-basket"></i>
                                            </span>
                                            </a>
                                        @endif
                                    </div>
                                </div>
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
