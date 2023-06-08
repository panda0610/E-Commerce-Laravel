@extends('storefront.layout.theme2')
@section('page-title')
    {{__('Product Details')}}
@endsection
@section('content')
    <!-- Product Details -->
    <section class="product-section pt-3 mt-6">
        <div class="container">
            <div class="row row-grid">
                <div class="breadcrumb-section">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('store.slug',$store->slug)}}">{{__('Main site')}}</a></li>
                        <li class="breadcrumb-item active m-0" aria-current="page">{{$products->name}}</li>
                    </ul>
                </div>
            </div>
            <div class="row row-grid">
                <div class="col-lg-6">
                    <img class="p-img"  src="{{asset(Storage::url('uploads/is_cover_image/'.$products->is_cover))}}" alt="shofe" title="shofe image">
                </div>
                <div class="col-lg-6">
                    <div class="pd-rate">
                        <div class="p-rateing d-flex">
                            <button type="button" class="action-item p-new bg-primary p-stoke">
                                @if($products->quantity == 0)
                                    {{__('OUT OF STOCK')}}
                                @else
                                    {{__('IN STOCK')}}
                                @endif
                            </button>
                            <span class="static-rating static-rating-sm d-block">
                                @if($store_setting->enable_rating == 'on')
                                    @for($i =1;$i<=5;$i++)
                                        @php
                                            $icon = 'fa-star';
                                            $color = '';
                                            $newVal1 = ($i-0.5);
                                            if($avg_rating < $i && $avg_rating >= $newVal1)
                                            {
                                                $icon = 'fa-star-half-alt';
                                            }
                                            if($avg_rating >= $newVal1)
                                            {
                                                $color = 'text-primary';
                                            }
                                        @endphp
                                        <i class="star fas {{$icon .' '. $color}}"></i>
                                    @endfor
                                @endif
                            </span>
                            <p class="mb-0 ml-3"><span class="t-gray">{{$avg_rating}}/5 ({{$user_count}} {{__('reviews')}}) </span></p>
                        </div>
                        @if(Auth::guard('customers')->check())
                            @if(!empty($wishlist) && isset($wishlist[$products->id]['product_id']))
                                @if($wishlist[$products->id]['product_id'] != $products->id)
                                    <button href="#" class="btn btn-sm bg-gray shadow  btn-icon hover-shadow-lg hover-translate-y-n3 add_to_wishlist wishlist_{{$products->id}}" data-id="{{$products->id}}">
                                        <span class="btn-inner--icon">
                                            <i class="far fa-heart"></i>
                                        </span>
                                    </button>
                                @else
                                    <button href="#" class="btn btn-sm bg-gray shadow  btn-icon hover-shadow-lg hover-translate-y-n3" data-id="{{$products->id}}" disabled>
                                        <span class="btn-inner--icon">
                                            <i class="fas fa-heart"></i>
                                        </span>
                                    </button>
                                @endif
                            @else
                                <button href="#" class="btn btn-sm bg-gray shadow  btn-icon hover-shadow-lg hover-translate-y-n3 add_to_wishlist wishlist_{{$products->id}}" data-id="{{$products->id}}">
                                    <span class="btn-inner--icon">
                                        <i class="far fa-heart"></i>
                                    </span>
                                </button>
                            @endif
                        @else
                            <button href="#" class="btn btn-sm bg-gray shadow  btn-icon hover-shadow-lg hover-translate-y-n3 add_to_wishlist wishlist_{{$products->id}}" data-id="{{$products->id}}">
                                    <span class="btn-inner--icon">
                                        <i class="far fa-heart"></i>
                                    </span>
                            </button>
                        @endif    
                    </div>
                    <!-- Product title -->
                    <h5 class="h4 store-title">{{$products->name}}</h5>
                    @if($products->enable_product_variant =='on')
                        <input type="hidden" id="product_id" value="{{$products->id}}">
                        <input type="hidden" id="variant_id" value="">
                        <input type="hidden" id="variant_qty" value="">
                        <div class="p-color mt-3">
                            <p class="mb-0">{{__('COLOR VARIATION')}}:</p>
                            <ul class="mt-3">
                                @foreach($product_variant_names as $key => $variant)
                                    <div class="col-sm-6 mb-4 mb-sm-0">
                                        <p class="d-block h6 mb-0">
                                        <p class="mb-0">{{ $variant->variant_name }}</p>
                                        <select name="product[{{$key}}]" id="pro_variants_name" class="form-control custom-select variant-selection  pro_variants_name{{$key}}">
                                            <option value="">{{ __('Select')  }}</option>
                                            @foreach($variant->variant_options as $key => $values)
                                                <option value="{{$values}}">{{$values}}</option>
                                            @endforeach
                                        </select>
                                        </span>
                                    </div>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <p class="text-sm mb-0 product-detail">
                        {!! $products->detail !!}
                    </p>
                    <div class="product-price">
                        <span class="h3 mb-0 p-price variation_price">
                            @if($products->enable_product_variant =='on')
                                {{\App\Models\Utility::priceFormat(0)}}
                            @else
                                {{\App\Models\Utility::priceFormat($products->price)}}
                            @endif
                        </span>
                        <sup class="h3 mb-0 sub-price">{{ \App\Models\Utility::priceFormat($products->last_price) }}</sup>
                        <a href="#" class="btn btn-primary rounded-pill btn-icon shadow hover-shadow-lg hover-translate-y-n3 add_to_cart" data-id="{{$products->id}}">
                            <span class="btn-inner--text">{{__('Add to cart')}}</span>
                            <span class="btn-inner--icon">
                                <i class="fas fa-shopping-basket"></i>
                            </span>
                        </a>
                    </div>
                    <div class="cart-buttons">
                        <p class="mb-0 t-black14 mr-3"><span class="t-gray">{{__('Category')}}:</span> {{$products->product_category()}}</p>
                        <p class="mb-0 t-black14"><span class="t-gray">{{__('SKU')}}:</span> {{$products->SKU}}</p>
                    </div>
                    @if(!empty($products->custom_field_1) && !empty($products->custom_value_1))
                        <div class="cart-buttons">
                            <div class="mb-0 t-black14"><span class="t-gray">{{$products->custom_field_1}} : </span> {{$products->custom_value_1}}</div>
                        </div>
                    @endif
                    @if(!empty($products->custom_field_2) && !empty($products->custom_value_2))
                        <div class="cart-buttons">
                            <div class="mb-0 t-black14"><span class="t-gray">{{$products->custom_field_2}} : </span> {{$products->custom_value_2}}</div>
                        </div>
                    @endif
                    @if(!empty($products->custom_field_3) && !empty($products->custom_value_3))
                        <div class="cart-buttons">
                            <div class="mb-0 t-black14"><span class="t-gray">{{$products->custom_field_3}} : </span> {{$products->custom_value_3}}</div>
                        </div>
                    @endif
                    @if(!empty($products->custom_field_4) && !empty($products->custom_value_4))
                        <div class="cart-buttons">
                            <div class="mb-0 t-black14"><span class="t-gray">{{$products->custom_field_4}} : </span> {{$products->custom_value_4}}</div>
                        </div>
                    @endif
                </div>
                <div class="product-detail-data mt-5">
                    <ul class="nav nav-tabs" id="myTab1" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="shopping-tab" data-toggle="tab" href="#p1" role="tab"
                               aria-controls="start-shopping" aria-selected="true">{{__('Description')}}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="electronic-tab" data-toggle="tab" href="#p2" role="tab"
                               aria-controls="Electronic" aria-selected="true">{{__('Reviews')}}</a>
                        </li>
                        @if(!empty($products->detail))
                            <li class="nav-item">
                                <a class="nav-link" id="software-tab" data-toggle="tab" href="#p3" role="tab"
                                   aria-controls="Software" aria-selected="true">{{__('Details')}}</a>
                            </li>
                        @endif
                    </ul>
                    <div class="tab-content bestsellers-tabs" id="myTabContent">
                        <div class="tab-pane fade show active" id="p1" role="tabpanel" aria-labelledby="shopping-tab">
                            <div class="store-tabs" id="accordion" role="tablist">
                                @if(!empty($products->description))
                                    <div class="card">
                                        <div class="card-header" role="tab" id="headingOne">
                                            <h5 class="mb-0">
                                                <a data-toggle="collapse" href="#collapseOne" aria-expanded="true"
                                                   aria-controls="collapseOne">
                                                    {{__('DESCRIPTION')}}
                                                </a>
                                            </h5>
                                        </div>
                                        <div id="collapseOne" class="collapse show" role="tabpanel"
                                             aria-labelledby="headingOne">
                                            <div class="card-body">
                                                {!! $products->description !!}
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                @if(!empty($products->specification))
                                    <div class="card">
                                        <div class="card-header" role="tab" id="headingTwo">
                                            <h5 class="mb-0">
                                                <a class="collapsed" data-toggle="collapse" href="#collapseTwo"
                                                   aria-expanded="false" aria-controls="collapseTwo">
                                                    {{__('SPECIFICATION')}}
                                                </a>
                                            </h5>
                                        </div>
                                        <div id="collapseTwo" class="collapse" role="tabpanel" aria-labelledby="headingTwo">
                                            <div class="card-body">
                                                {!! $products->specification !!}
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                @if(!empty($products->detail))
                                    <div class="card">
                                        <div class="card-header" role="tab" id="headingThree">
                                            <h5 class="mb-0">
                                                <a class="collapsed" data-toggle="collapse" href="#collapseThree"
                                                   aria-expanded="false" aria-controls="collapseThree">
                                                    {{__('DETAILS')}}
                                                </a>
                                            </h5>
                                        </div>
                                        <div id="collapseThree" class="collapse" role="tabpanel"
                                             aria-labelledby="headingThree">
                                            <div class="card-body">
                                                {!! $products->detail !!}
                                            </div>
                                        </div>
                                        @if(!empty($products->attachment))
                                            <div class="button mt-4">
                                                <a href="{{asset(Storage::url('uploads/is_cover_image/'.$products->attachment))}}" class="text-primary btn-instruction" download="{{$products->attachment}}">
                                                    <span class="btn-inner--icon">
                                                        <i class="fas fa-shopping-basket"></i>
                                                    </span>
                                                    {{__('Download instruction .pdf')}}
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="tab-pane fade" id="p2" role="tabpanel" aria-labelledby="electronic-tab">
                            <div class="customer-product-review">
                                <div class="review-title">
                                    <h5>
                                        <span class="r-title">{{__('Reviews')}}:</span> <span class="r-rate">{{$avg_rating}}/5</span>
                                        <span
                                            class="t-gray"> ({{__('reviews')}})
                                        </span>
                                    </h5>
                                    @if(Auth::guard('customers')->check())
                                        <a href="#" class="btn btn-sm btn-primary btn-icon-only rounded-circle float-right text-white" data-size="lg" data-toggle="modal" data-url="{{route('rating',[$store->slug,$products->id])}}" data-ajax-popup="true" data-title="{{__('Create New Rating')}}">
                                            <i class="fas fa-plus"></i>
                                        </a>
                                    @endif
                                </div>
                                @foreach($product_ratings as $product_key => $product_rating)
                                    <div class="pd-rate">
                                        <div class="p-rateing  d-flex">
                                        <span class="static-rating static-rating-sm d-block">
                                         @if($store_setting->enable_rating == 'on')
                                                @for($i =1;$i<=5;$i++)
                                                    @php
                                                        $icon = 'fa-star';
                                                        $color = '';
                                                        $newVal1 = ($i-0.5);
                                                        if($avg_rating < $i && $avg_rating >= $newVal1)
                                                        {
                                                            $icon = 'fa-star-half-alt';
                                                        }
                                                        if($avg_rating >= $newVal1)
                                                        {
                                                            $color = 'text-primary';
                                                        }
                                                    @endphp
                                                    <i class="star fas {{$icon .' '. $color}}"></i>
                                                @endfor
                                            @endif
                                        </span>
                                            <p class="mb-0 ml-3"><span class="t-gray">{{$avg_rating}}/5 ({{$user_count}} reviews) </span></p>
                                        </div>
                                    </div>
                                    <p class="text-sm mb-0 mt-2 product-detail">
                                        {{$product_rating->description}}<
                                    </p>
                                    <div class="mt-2">
                                        <p class="mb-0 t-black13">{{$product_rating->name}}</p>
                                        <span>{{$product_rating->title}}</span>
                                    </div>
                                    <hr>
                                @endforeach
                            </div>
                        </div>
                        <div class="tab-pane fade" id="p3" role="tabpanel" aria-labelledby="software-tab">
                            <div class="mt-4 mb-2">
                                <p>
                                    {!! $products->detail !!}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Products -->
    <section class="top-product">
        <div class="container">
            <div class="row">
                <div class="pr-title">
                    <h3 class=" mt-4 store-title-medium text-primary">{{__('Related products')}}</h3>
                </div>
            </div>
            <div class="row related-product">
                @foreach($all_products as $key=>$product)
                    @if($product->id != $products->id)
                        <div class="col-xl-3 col-lg-4 col-sm-6 product-box">
                            <div class="card card-product">
                                <div class="card-image">
                                    <a href="{{route('store.product.product_view',[$store->slug,$product->id])}}">
                                        @if(!empty($product->is_cover) && \Storage::exists('uploads/is_cover_image/'.$product->is_cover))
                                            <img alt="Image123 placeholder" src="{{asset(Storage::url('uploads/is_cover_image/'.$product->is_cover))}}" class="img-center img-fluid">
                                        @else
                                            <img alt="Image placeholder" src="{{asset(Storage::url('uploads/is_cover_image/default.jpg'))}}" class="img-center img-fluid">
                                        @endif
                                    </a>
                                </div>
                                <div class="card-body mt-3">
                                    <h6><a class="t-black13" href="{{route('store.product.product_view',[$store->slug,$product->id])}}"> {{$product->name}}</a></h6>
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
                                                        $color = 'text-primary';
                                                    }
                                                @endphp
                                                <i class="star fas {{$icon .' '. $color}}"></i>
                                            @endfor
                                        @endif
                                    </span>
                                    <div class="product-price mt-3 mb-3">
                                        <span class="card-price t-black15">
                                         @if($product->enable_product_variant == 'on')
                                                {{__('In variant')}}
                                            @else
                                                {{\App\Models\Utility::priceFormat($product->price)}}
                                            @endif
                                    </span>
                                    </div>
                                    <div class="product-buttons">
                                        @if($product->enable_product_variant == 'on')
                                            <a href="{{route('store.product.product_view',[$store->slug,$product->id])}}" class="btn btn-sm btn-primary rounded-pill btn-icon shadow hover-shadow-lg hover-translate-y-n3">
                                                <span class="btn-inner--text">{{__('Add to cart')}}</span>
                                                <span class="btn-inner--icon text-white">
                                                    <i class="fas fa-shopping-basket"></i>
                                                </span>
                                            </a>
                                        @else
                                            <a href="{{route('store.product.product_view',[$store->slug,$product->id])}}" class="btn btn-sm btn-primary rounded-pill btn-icon shadow hover-shadow-lg hover-translate-y-n3">
                                                <span class="btn-inner--text">{{__('Add to cart')}}</span>
                                                <span class="btn-inner--icon text-white">
                                                    <i class="fas fa-shopping-basket"></i>
                                                </span>
                                            </a>
                                        @endif
                                        @if(Auth::guard('customers')->check())
                                            @if(!empty($wishlist) && isset($wishlist[$product->id]['product_id']))
                                                @if($wishlist[$product->id]['product_id'] != $product->id)
                                                    <button href="#" class="btn btn-sm bg-gray shadow  btn-icon hover-shadow-lg hover-translate-y-n3 add_to_wishlist wishlist_{{$product->id}}" data-id="{{$product->id}}">
                                                        <span class="btn-inner--icon">
                                                            <i class="far fa-heart"></i>
                                                        </span>
                                                    </button>
                                                @else
                                                    <button href="#" class="btn btn-sm bg-gray shadow  btn-icon hover-shadow-lg hover-translate-y-n3" data-id="{{$product->id}}" disabled>
                                                        <span class="btn-inner--icon">
                                                            <i class="fas fa-heart"></i>
                                                        </span>
                                                    </button>
                                                @endif
                                            @else
                                                <button href="#" class="btn btn-sm bg-gray shadow  btn-icon hover-shadow-lg hover-translate-y-n3 add_to_wishlist wishlist_{{$product->id}}" data-id="{{$product->id}}">
                                                    <span class="btn-inner--icon">
                                                        <i class="far fa-heart"></i>
                                                    </span>
                                                </button>
                                            @endif
                                        @else
                                            <button href="#" class="btn btn-sm bg-gray shadow  btn-icon hover-shadow-lg hover-translate-y-n3 add_to_wishlist wishlist_{{$product->id}}" data-id="{{$product->id}}">
                                                    <span class="btn-inner--icon">
                                                        <i class="far fa-heart"></i>
                                                    </span>
                                            </button>
                                        @endif    
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </section>
@endsection
@push('script-page')
    <script>

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
        $(document).on('change', '#pro_variants_name', function () {
        var variants = [];
        $(".variant-selection").each(function (index, element) {
            variants.push(element.value);
        });
        if (variants.length > 0) {
            $.ajax({
                url: '{{route('get.products.variant.quantity')}}',
                data: {
                    "_token": $('meta[name="csrf-token"]').attr('content'),
                    variants: variants.join(' : '),
                    product_id: $('#product_id').val()
                },

                success: function (data) {
                    $('.variation_price').html(data.price);
                    $('#variant_id').val(data.variant_id);
                    $('#variant_qty').val(data.quantity);
                }
            });
        }
    });
    </script>
@endpush
