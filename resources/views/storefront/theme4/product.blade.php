@extends('storefront.layout.theme4')
@section('page-title')
    {{__('Home')}}
@endsection
@section('content')
    <!-- Products -->
    <div class="top-product pt-8 m-0">
        @if($products['Start shopping']->count() > 0)
            <div class="container">
                <div class="row">
                    <div class="pr-title mb-4">
                        <h3 class=" mt-4 store-title text-primary">{{__('Products')}}</h3>
                        <div class="p-tablist">
                            <ul class="nav nav-tabs" id="myTab" role="tablist">
                                @foreach($categories as $key=>$category)
                                    <li class="nav-item">
                                        <a href="#{!!preg_replace('/[^A-Za-z0-9\-]/','_',$category)!!}" data-id="{{$key}}" class="nav-link {{($category==$categorie_name)?'active':''}} productTab" id="electronic-tab" data-toggle="tab" role="tab" aria-controls="home" aria-selected="false">
                                            {{$category}}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <div class="tab-content bestsellers-tabs" id="myTabContent">
                        @foreach($products as $key=>$items)
                            <div class="tab-pane fade {{($key==$categorie_name)?'active show':''}}" id="{!! preg_replace('/[^A-Za-z0-9\-]/', '_', $key)!!}" role="tabpanel" aria-labelledby="shopping-tab">
                                <div class="col-lg-12">
                                    <div class="row">
                                        @if($items->count() > 0)
                                            @foreach($items as $product)
                                                <div class="col-xl-3 col-lg-4 col-sm-6 product-box">
                                                    <div class="card card-fluid card-product">
                                                        <div class="card-image bg-white">
                                                            <a href="{{route('store.product.product_view',[$store->slug,$product->id])}}">
                                                                @if(!empty($product->is_cover) && \Storage::exists('uploads/is_cover_image/'.$product->is_cover    ))
                                                                    <img alt="Image placeholder" src="{{asset(Storage::url('uploads/is_cover_image/'.$product->is_cover))}}" class="img-center img-fluid">
                                                                @else
                                                                    <img alt="Image placeholder" src="{{asset(Storage::url('uploads/is_cover_image/default.jpg'))}}" class="img-center img-fluid">
                                                                @endif
                                                            </a>
                                                        </div>
                                                        <div class="card-body pt-0">
                                                            <h6>
                                                                <a class="t-black13" href="{{route('store.product.product_view',[$store->slug,$product->id])}}">
                                                                    {{$product->name}}
                                                                </a>
                                                            </h6>
                                                            <p class="text-sm">
                                                                <span class="td-gray">{{__('Category')}}:</span> {{$product->product_category()}}
                                                            </p>
                                                            <span class="static-rating static-rating-sm">
                                                                @if($store->enable_rating == 'on')
                                                                    @for($i =1;$i<=5;$i++)
                                                                        @php
                                                                            $icon = 'fa-star';
                                                                            $color = '';
                                                                            $newVal1 = ($i-0.5);
                                                                            if($product->product_rating() < $i && $product->product_rating() >= $newVal1)
                                                                            {
                                                                                $icon = 'fa-star-half-alt';
                                                                            }
                                                                            if($product->product_rating() >= $newVal1)
                                                                            {
                                                                                $color = 'text-warning';
                                                                            }
                                                                        @endphp
                                                                        <i class="star fas {{$icon .' '. $color}}"></i>
                                                                    @endfor
                                                                @endif
                                                            </span>
                                                            <div class="product-price mt-3">
                                                            <span class="card-price t-black15">
                                                                @if($product->enable_product_variant == 'on')
                                                                    {{__('In variant')}}
                                                                @else
                                                                    {{\App\Models\Utility::priceFormat($product->price)}}
                                                                @endif
                                                            </span>
                                                            </div>
                                                            <div class="product-price mt-3">
                                                                @if($product->enable_product_variant == 'on')
                                                                    <a href="{{route('store.product.product_view',[$store->slug,$product->id])}}" class="action-item pcart-icon bg-primary">
                                                                        {{__('Add To Cart')}}
                                                                        <i class="fas fa-shopping-basket ml-2"></i>
                                                                    </a>
                                                                @else
                                                                    <a class="action-item pcart-icon bg-primary add_to_cart"  data-id="{{$product->id}}">
                                                                        {{__('Add To Cart')}}
                                                                        <i class="fas fa-shopping-basket ml-2"></i>
                                                                    </a>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="actions card-product-actions">
                                                            @if(!empty($wishlist) && isset($wishlist[$product->id]['product_id']))
                                                                @if($wishlist[$product->id]['product_id'] != $product->id)
                                                                    <button type="button" class="action-item wishlist-icon bg-light-gray add_to_wishlist wishlist_{{$product->id}}" data-id="{{$product->id}}">
                                                                        <i class="far fa-heart"></i>
                                                                    </button>
                                                                @else
                                                                    <button type="button" class="action-item wishlist-icon bg-light-gray" data-id="{{$product->id}}" disabled>
                                                                        <i class="fas fa-heart"></i>
                                                                    </button>
                                                                @endif
                                                            @else
                                                                <button type="button" class="action-item wishlist-icon bg-light-gray add_to_wishlist wishlist_{{$product->id}}" data-id="{{$product->id}}">
                                                                    <i class="far fa-heart"></i>
                                                                </button>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="col-12 product-box">
                                                <div class="card card-product">
                                                    <h6 class="m-0 text-center no_record"><i class="fas fa-ban"></i> {{__('No Record Found')}}</h6>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>
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
    </script>
@endpush