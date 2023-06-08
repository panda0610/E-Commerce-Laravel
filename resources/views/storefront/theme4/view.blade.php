@extends('storefront.layout.theme4')
@section('page-title')
    {{__('Product Details')}}
@endsection
@section('content')
    <!-- Product Details -->
    <div class="product-section pt-6">
        <div class="container">
            <div class="row row-grid">
                <div class="col-lg-12">
                    <div class="product-details">
                        <div class="left-side">
                            <!-- Product title -->
                            <button type="button" class="action-item p-new bg-primary p-stoke">
                                @if($products->quantity == 0)
                                    {{__('OUT OF STOCK')}}
                                @else
                                    {{__('IN STOCK')}}
                                @endif
                            </button>
                            <h5 class="h4 store-title">{{$products->name}}</h5>
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
                                    <p class="mb-0 ml-3"><span class="t-gray">{{$avg_rating}}/5 ({{$user_count}} reviews)</span></p>
                                </div>
                            </div>
                            <div class="product-price">
                                 <span class="h3 mb-0 p-price variation_price">
                                    @if($products->enable_product_variant =='on')
                                         {{\App\Models\Utility::priceFormat(0)}}
                                     @else
                                         {{\App\Models\Utility::priceFormat($products->price)}}
                                     @endif
                                </span>
                                <sup class="h3 mb-0 sub-price">{{ \App\Models\Utility::priceFormat($products->last_price) }}</sup>
                            </div>
                        </div>
                        <div class="center-side">
                            <div class="container">
                                <div class="product-slider">
                                    <div class="carousel-container position-relative">
                                        <div id="myCarousel" class="carousel slide" data-ride="carousel">
                                            <div class="carousel-inner">
                                                @foreach($products_image as $key => $productss)
                                                    <div class="carousel-item {{($key == 0)?'active':''}}" data-slide-number="{{$key}}">
                                                        @if(!empty($products_image[$key]->product_images) && \Storage::exists('uploads/product_image/'.$products_image[$key]->product_images))
                                                            <img src="{{asset(Storage::url('uploads/product_image/'.$products_image[$key]->product_images))}}" class="d-block w-100" alt="image">
                                                        @else
                                                            <img src="{{asset(Storage::url('uploads/product_image/default.jpg'))}}" class="d-block w-100" alt="image">
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>

                                        <!-- Carousel Navigation -->
                                        <a class="carousel-control-prev" href="#myCarousel" role="button" data-slide="prev">
                                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                            <span class="sr-only">{{__('Previous')}}</span>
                                        </a>
                                        <a class="carousel-control-next" href="#myCarousel" role="button" data-slide="next">
                                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                            <span class="sr-only">{{__('Next')}}</span>
                                        </a>

                                    </div> <!-- /row -->
                                </div>
                                <div class="cart-buttons">
                                    <a href="#" class="btn btn-primary rounded-pill btn-icon shadow hover-translate-y-n3 add_to_cart" data-id="{{$products->id}}">
                                        <span class="btn-inner--text">{{__('Add to cart')}}</span>
                                        <span class="btn-inner--icon">
                                            <i class="fas fa-shopping-basket"></i>
                                        </span>
                                    </a>
                                    <div class="cat-id-block">
                                        <p class="mb-0 t-black14"><span class="t-gray">{{__('Category')}}:</span> {{$products->product_category()}}</p>
                                        <p class="mb-0 t-black14"><span class="t-gray">{{__('ID')}}:</span> {{$products->SKU}}</p>
                                    </div>
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
                        </div>
                        <div class="right-side">
                            <p class="text-sm mb-0 product-detail">{!! $products->detail!!}</p>
                            <div class="p-color mt-3">
                                @if($products->enable_product_variant =='on')
                                    <input type="hidden" id="product_id" value="{{$products->id}}">
                                    <input type="hidden" id="variant_id" value="">
                                    <input type="hidden" id="variant_qty" value="">
                                    <div class="p-color mt-3">
                                        <p class="mb-2">{{__('COLOR VARIATION')}}:</p>
                                        @foreach($product_variant_names as $key => $variant)
                                            <div class="col-sm-6 mb-4 mb-sm-0">
                                                <p class="d-block h6 mb-0">
                                                <p class="mb-0 text-primary">{{ $variant->variant_name }}</p>
                                                <select name="product[{{$key}}]" id="pro_variants_name" class="form-control custom-select variant-selection  pro_variants_name{{$key}}">
                                                    <option value="">{{ __('Select')  }}</option>
                                                    @foreach($variant->variant_options as $key => $values)
                                                        <option value="{{$values}}">{{$values}}</option>
                                                    @endforeach
                                                </select>
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="product-section pt-5 responsive-order">
                        <div class="container">
                            <div class="row align-items-center">
                                <div class="col-lg-6">
                                    <div class="store-tabs" id="accordion" role="tablist">
                                        @if(!empty($products->description))
                                            <div class="card">
                                                <div class="card-header" role="tab" id="headingOne">
                                                    <h5 class="mb-0">
                                                        <a data-toggle="collapse" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                                            {{__('DESCRIPTION')}}
                                                        </a>
                                                    </h5>
                                                </div>
                                                <div id="collapseOne" class="collapse show" role="tabpanel" aria-labelledby="headingOne">
                                                    <div class="card-body">
                                                        {!! $products->description!!}
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        @if(!empty($products->specification))
                                            <div class="card">
                                                <div class="card-header" role="tab" id="headingTwo">
                                                    <h5 class="mb-0">
                                                        <a class="collapsed" data-toggle="collapse" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
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
                                                        <a class="collapsed" data-toggle="collapse" href="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                                            {{__('DETAILS')}}
                                                        </a>
                                                    </h5>
                                                </div>
                                                <div id="collapseThree" class="collapse" role="tabpanel" aria-labelledby="headingThree">
                                                    <div class="card-body">
                                                        {!! $products->detail !!}
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    @if(!empty($products->attachment))
                                        <div class="button">
                                            <a href="{{asset(Storage::url('uploads/is_cover_image/'.$products->attachment))}}" class="text-primary btn-instruction" download="{{$products->attachment}}">
                                                    <span class="btn-inner--icon">
                                                        <i class="fas fa-shopping-basket"></i>
                                                    </span>
                                                {{__('Download instruction .pdf')}}
                                            </a>
                                        </div>
                                    @endif
                                </div>
                                <div class="col-lg-6">
                                    <div class="product-right-img">
                                        @if(!empty($products->is_cover) && \Storage::exists('uploads/is_cover_image/'.$products->is_cover))
                                            <img src="{{asset(Storage::url('uploads/is_cover_image/'.$products->is_cover))}}" class="product-img" alt="#">
                                        @else
                                            <img src="{{asset(Storage::url('uploads/is_cover_image/default.jpg'))}}" class="d-block w-100">
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="product-section pt-5">
                        <div class="container">
                            <div class="row align-items-center">
                                <div class="col-lg-6">
                                    <div class="bg-blue">
                                        @if(!empty($products->is_cover) && \Storage::exists('uploads/is_cover_image/'.$products->is_cover))
                                            <img src="{{asset(Storage::url('uploads/is_cover_image/'.$products->is_cover))}}" class="product-img" alt="#" style="width:525px; height: 343px ">
                                        @else
                                            <img src="{{asset(Storage::url('uploads/is_cover_image/default.jpg'))}}" class="d-block w-100">
                                        @endif
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="customer-product-review">
                                        <div class="review-title">
                                            <h5><span class="r-title">{{__('Reviews')}}:</span> <span class="r-rate">{{$avg_rating}}/5</span> <span class="t-gray"> ({{$user_count}} {{__('reviews')}})</span></h5>
                                            <div class="p-rateing  d-flex">
                                                <span class="static-rating static-rating-sm d-block mt-0">
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
                                            </div>
                                            @if(Auth::guard('customers')->check())
                                                <a href="#" class="btn btn-sm btn-primary btn-icon-only rounded-circle float-right text-white" data-size="lg" data-toggle="modal" data-url="{{route('rating',[$store->slug,$products->id])}}" data-ajax-popup="true" data-title="{{__('Create New Rating')}}">
                                                    <i class="fas fa-plus"></i>
                                                </a>
                                            @endif    
                                        </div>
                                        @foreach($product_ratings as $product_key => $product_rating)
                                            @if($product_rating->rating_view == 'on')
                                                <hr>
                                                <div class="pd-rate">
                                                    <div class="p-rateing  d-flex">
                                                        <span class="static-rating static-rating-sm d-block">
                                                            @for($i =0;$i<5;$i++)
                                                                <i class="star fas fa-star {{($product_rating->ratting > $i  ? 'text-primary' : '')}}"></i>
                                                            @endfor
                                                        </span>
                                                        <p class="mb-0 ml-3"><span class="t-gray"> {{$product_rating->created_at->diffForHumans()}}  </span></p>
                                                    </div>

                                                </div>
                                                <!-- Product title -->
                                                <p class="text-sm mb-0 mt-2 product-detail">
                                                    {{$product_rating->description}}
                                                </p>
                                                <div class="mt-2">
                                                    <p class="mb-0 t-black13">{{$product_rating->name}}</p>
                                                    <span>{{$product_rating->title}}</span>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                    <hr>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Products -->
    <section class="top-product bg-none">
        <div class="container">
            <div class="row">
                <div class="pr-title">
                    <h3 class=" mt-4 store-title-medium text-primary">{{__('Related products')}}</h3>
                </div>
            </div>
            <div class="row">
                @foreach($all_products as $key=>$product)
                    @if($product->id != $products->id)
                        <div class="col-xl-3 col-lg-4 col-sm-6 product-box">
                            <div class="card card-product">
                                <div class="card-image bg-lighgray">
                                    <a href="{{route('store.product.product_view',[$store->slug,$product->id])}}">
                                        @if(!empty($product->is_cover) && \Storage::exists('uploads/is_cover_image/'.$product->is_cover))
                                            <img alt="Image placeholder" src="{{asset(Storage::url('uploads/is_cover_image/'.$product->is_cover))}}" class="img-center img-fluid">
                                        @else
                                            <img alt="Image placeholder" src="{{asset(Storage::url('uploads/is_cover_image/default.jpg'))}}" class="img-center img-fluid">
                                        @endif
                                    </a>
                                </div>
                                <div class="card-body pt-0">
                                    <h6><a class="t-black13" href="{{route('store.product.product_view',[$store->slug,$product->id])}}"> {{$product->name}}</a></h6>
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
                                                        $color = 'text-primary';
                                                    }
                                                @endphp
                                                <i class="star fas {{$icon .' '. $color}}"></i>
                                            @endfor
                                        @endif
                                    </span>
                                    <span class="card-price t-black15 mt-3">
                                         @if($product->enable_product_variant == 'on')
                                            {{__('In variant')}}
                                        @else
                                            {{\App\Models\Utility::priceFormat($product->price)}}
                                        @endif
                                    </span>
                                    <div class="mt-3">
                                        @if($product->enable_product_variant == 'on')
                                            <a href="{{route('store.product.product_view',[$store->slug,$product->id])}}" class="action-item pcart-icon bg-primary">
                                                {{__('Add To Cart')}}
                                                <i class="ml-2 fas fa-shopping-basket"></i>
                                            </a>
                                        @else
                                            <a href="{{route('store.product.product_view',[$store->slug,$product->id])}}" class="action-item pcart-icon bg-primary" data-id="{{$product->id}}">
                                                {{__('Add To Cart')}}
                                                <i class="ml-2 fas fa-shopping-basket"></i>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                                <div class="actions card-product-actions">
                                @if(Auth::guard('customers')->check())    
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
                                @else
                                    <button type="button" class="action-item wishlist-icon bg-light-gray add_to_wishlist wishlist_{{$product->id}}" data-id="{{$product->id}}">
                                            <i class="far fa-heart"></i>
                                    </button>
                                @endif    
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
