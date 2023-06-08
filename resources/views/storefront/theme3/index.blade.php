@extends('storefront.layout.theme3')
@section('page-title')
    {{__('Home')}}
@endsection
@push('css-page')
    <style>
        .product-box .product-price {
            justify-content: unset;
        }

        .p-tablist .nav-tabs .nav-item .nav-link.active {
            background-color: #fff !important;
            border-radius: 25px;
            padding: 10px;
        }

        .p-tablist .nav-tabs .nav-item .nav-link {
            border-radius: 25px;
            padding: 10px;
        }
        .nav-tabs {
            border-bottom: none;
        }
    </style>
@endpush
@section('content')
    <!-- Header -->
    <div class="home-banner-slider">
        @if(isset($storethemesetting['enable_banner_img']) && $storethemesetting['enable_banner_img'] == 'on')
            <div class="banner-img" width="660" height="766" style="background: url({{asset(Storage::url('uploads/store_logo/'.(!empty($storethemesetting['banner_img'])?$storethemesetting['banner_img']:'header_img_3.png')))}});"></div>
        @endif
        @if($theme3_product != null)
            <div class="container">
                <div class="row">
                    <div class="col-lg-6 col-md-12 col-sm-12 p-0">
                        <h1 class=" mt-4 store-title t-secondary w-75">{{$theme3_product->name}}</h1>
                        <div class="row mt-5">
                            <div class="col-lg-6 col-md-6 col-sm-12">
                                <div class="black-border"></div>
                                <ul class="banner-list">
                                    <li><a href="{{route('store.product.product_view',[$store->slug,$theme3_product->id])}}" class="text-dark">{{__('DESCRIPTION')}}</a></li>
                                    <li><a href="{{route('store.product.product_view',[$store->slug,$theme3_product->id])}}" class="text-dark">{{__('SPECIFICATION')}}</a></li>
                                    <li><a href="{{route('store.product.product_view',[$store->slug,$theme3_product->id])}}" class="text-dark">{{__('DETAILS')}}</a></li>
                                </ul>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12 banner-center-img">
                                @if($theme3_product_image != null && $theme3_product_image->count()>0)
                                    <img class="b1" width="216" height="268" src="{{asset(Storage::url('uploads/product_image/'.$theme3_product_image[0]['product_images']))}}" alt="New collection" title="New collection">
                                @endif
                                @if($theme3_product_image != null &&  $theme3_product_image->count()>1)
                                    <img class="b2" width="142" height="188" src="{{asset(Storage::url('uploads/product_image/'.$theme3_product_image[1]['product_images']))}}" alt="New collection" title="New collection">
                                @endif
                            </div>
                        </div>
                        @if($theme3_product_image != null )
                            <div class="row m-t-40">
                                <div class="col-lg-6 col-md-6 col-sm-12 d-flex" style="height: 100px;">
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-12 p-detail">
                                    @if($theme3_product['enable_product_variant'] == 'on')
                                        <h3 class="store-title-medium t-secondary ml-5 mb-4">{{__('In variant')}}</h3>
                                        <a class="btn btn-sm btn-dark  btn-icon hover-translate-y-n3" href="{{route('store.product.product_view',[$store->slug,$theme3_product->id])}}">
                                <span class="btn-inner--icon">
                                    <span class="btn-inner--text text-white">{{__('ADD TO CART')}}</span>
                                    <i class="fas fa-shopping-basket"></i>
                                </span>
                                        </a>
                                    @else
                                        <h3 class="store-title-medium t-secondary ml-5 mb-4">{{\App\Models\Utility::priceFormat($theme3_product->price)}}</h3>
                                        <a class="btn btn-sm btn-black btn-icon add_to_cart" data-id="{{$theme3_product->id}}">
                                            <span class="btn-inner--text text-white">{{__('ADD TO CART')}}</span>
                                            <span class="btn-inner--icon">
                                            <i class="fas fa-shopping-basket"></i>
                                        </span>
                                        </a>
                                        @if(!empty($wishlist) && isset($wishlist[$theme3_product->id]['product_id']))
                                            @if($wishlist[$theme3_product->id]['product_id'] != $theme3_product->id)
                                                <button type="button" style="font-size: 20px" class="btn btn-sm btn-dark  btn-icon hover-translate-y-n3 add_to_wishlist wishlist_{{$theme3_product->id}}" data-id="{{$theme3_product->id}}">
                                                    <i class="far fa-heart"></i>
                                                </button>
                                            @else
                                                <button type="button" style="font-size: 20px" class="btn btn-sm btn-dark  btn-icon hover-translate-y-n3 bg-light-gray" data-id="{{$theme3_product->id}}" disabled>
                                                    <i class="fas fa-heart"></i>
                                                </button>
                                            @endif
                                        @else
                                            <button type="button" style="font-size: 20px" class="btn btn-sm btn-dark  btn-icon hover-translate-y-n3 add_to_wishlist wishlist_{{$theme3_product->id}}" data-id="{{$theme3_product->id}}">
                                                <i class="far fa-heart"></i>
                                            </button>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Features -->
    @if(isset($storethemesetting['enable_features']) && $storethemesetting['enable_features'] == 'on')
        <section class="store-promotions bg-primary">
            <div class="container">
                <div class="row">
                    @if(isset($storethemesetting['enable_features1']) &&$storethemesetting['enable_features1'] == 'on')
                        @if(isset($storethemesetting['features_icon1']))
                            <div class="col-lg-3 col-sm-6">
                                <div class="mb-4 text-center">
                                    <div class="icon text-primary">
                                        {!! $storethemesetting['features_icon1'] !!}
                                        <strong class="t-black">{{$storethemesetting['features_title1']}}</strong>
                                    </div>
                                    <p class="t-black">{{$storethemesetting['features_description1']}}</p>
                                </div>
                            </div>
                        @endif
                    @endif
                    @if(isset($storethemesetting['enable_features2']) &&$storethemesetting['enable_features2'] == 'on')
                        @if(isset($storethemesetting['features_icon2']))
                            <div class="col-lg-3 col-sm-6">
                                <div class="mb-4 text-center">
                                    <div class="icon text-primary">
                                        {!! $storethemesetting['features_icon2'] !!}
                                        <strong class="t-black">{{$storethemesetting['features_title2']}}</strong>
                                    </div>
                                    <p class="t-black">{{$storethemesetting['features_description2']}}</p>
                                </div>
                            </div>
                        @endif
                    @endif
                    @if(isset($storethemesetting['enable_features3']) &&$storethemesetting['enable_features3'] == 'on')
                        @if(isset($storethemesetting['features_icon3']))
                            <div class="col-lg-3 col-sm-6">
                                <div class="mb-4 text-center">
                                    <div class="icon text-primary">
                                        {!! $storethemesetting['features_icon3'] !!}
                                        <strong class="t-black">{{$storethemesetting['features_title3']}}</strong>
                                    </div>
                                    <p class="t-black">{{$storethemesetting['features_description3']}}</p>
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </section>
    @endif

    <!-- Blog-->
    @if($store->blog_enable == 'on')
        <section class="new-collection-section">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="bd-example">
                            <div id="carouselExampleCaptions" class="carousel slide" data-ride="carousel">
                                <div class="carousel-inner" role="listbox">
                                    <div class="carousel-item active">
                                        @if($blogs->count()>0)
                                            @if(\Storage::exists('uploads/store_logo/'.($blogs[0]['blog_cover_image'])))
                                                <img class="d-block" width="837" height="566" src="{{asset(Storage::url('uploads/store_logo/'.($blogs[0]['blog_cover_image'])))}}" alt="New collection" title="New collection">
                                            @else
                                                <img class="d-block" width="837" height="566" src="{{asset(Storage::url('uploads/store_logo/default.jpg'))}}" alt="New collection" title="New collection">
                                            @endif
                                            <div class="carousel-caption d-none d-md-block">
                                                <h3>{{$blogs[0]['title']}}</h3>
                                                <a href="{{route('store.store_blog_view',[$store->slug,$blogs[0]['id']])}}" class="btn btn-sm btn-white btn-icon  hover-translate-y-n3">
                                                    <span class="btn-inner--text">{{__('Show More')}}</span>
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-12 col-sm-12">
                        <div class="row">
                            <div class="col-lg-12 col-md-6 col-sm-12">
                                <div class="new-collection-box bt-0">
                                    @if($blogs->count()>1)
                                        <img class="d-block" width="403" height="255" src="{{asset(Storage::url('uploads/store_logo/'.($blogs[1]['blog_cover_image'])))}}" alt="New collection" title="New collection">
                                        <div class="new-collection-content">
                                            <h3>{{$blogs[1]['title']}}</h3>
                                            <a href="{{route('store.store_blog_view',[$store->slug,$blogs[1]['id']])}}">{{__('SHOW MORE')}}</a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-6 col-sm-12">
                                <div class="new-collection-box bt-10">
                                    @if($blogs->count()>2)
                                        <img class="d-block" width="403" height="255" src="{{asset(Storage::url('uploads/store_logo/'.($blogs[2]['blog_cover_image'])))}}" alt="New collection" title="New collection">
                                        <div class="new-collection-content">
                                            <h3>{{$blogs[2]['title']}}</h3>
                                            <a href="{{route('store.store_blog_view',[$store->slug,$blogs[2]['id']])}}">{{__('SHOW MORE')}}</a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif

    <!-- Top Rated Products -->
    @if(count($topRatedProducts)>0)
        <section class="store-feature-section bg-body">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12 mb-5 text-center">
                        <h3 class="store-title t-secondary">{{__('Featured Collections')}}</h3>
                    </div>
                </div>
                <div class="row justify-content-center">
                    @foreach($topRatedProducts  as $k => $topRatedProduct)
                        <div class="col-xl-3 col-lg-4 col-sm-6 product-box">
                            <div class="card card-product">
                                <div class="card-image">
                                    <a href="{{route('store.product.product_view',[$store->slug,$topRatedProduct->product->id])}}">
                                        @if(!empty($topRatedProduct->product->is_cover) && \Storage::exists('uploads/is_cover_image/'.$topRatedProduct->product->is_cover))
                                            <img alt="Image placeholder" height="163" width="123" src="{{asset(Storage::url('uploads/is_cover_image/'.(!empty($topRatedProduct->product->is_cover)?$topRatedProduct->product->is_cover:'')))}}" class="img-center">
                                        @else
                                            <img alt="Image placeholder" height="163" width="123" src="{{asset(Storage::url('uploads/is_cover_image/default.jpg'))}}" class="img-center">
                                        @endif
                                    </a>
                                </div>
                                <div class="card-body pt-0">
                                    <h6><a class="t-black13" href="{{route('store.product.product_view',[$store->slug,$topRatedProduct->product->id])}}">{{$topRatedProduct['product']['name']}}</a></h6>
                                    <p class="text-sm">
                                        {{__('Category')}}:<span class="t-black">
                                            {{$topRatedProduct->product->product_category()}}
                                        </span>
                                    </p>
                                    @if($topRatedProduct->product['enable_product_variant'] == 'on')
                                        <div class="product-price m-0">
                                            <span class="card-price t-black15">{{__('In variant')}}</span>
                                            <a href="{{route('store.product.product_view',[$store->slug,$topRatedProduct->product->id])}}">
                                                <button type="button" class="m-4 btn btn-sm btn-primary rounded-pill btn-icon hover-translate-y-n3">
                                                    <i class="fas fa-shopping-basket"></i>
                                                </button>
                                            </a>
                                        </div>
                                    @else
                                        <p><span class="card-price t-black15">{{\App\Models\Utility::priceFormat($topRatedProduct->product->price)}}</span></p>
                                        <div class="product-price m-0">
                                            <a class="btn btn-sm btn-black btn-icon add_to_cart" data-id="{{$topRatedProduct->product->id}}">
                                                <span class="btn-inner--text text-white">{{__('Add to cart')}}</span>
                                                <span class="btn-inner--icon text-white">
                                                                            <i class="fas fa-shopping-basket"></i>
                                                                        </span>
                                            </a>
                                            @if(Auth::guard('customers')->check())
                                                @if(!empty($wishlist) && isset($wishlist[$topRatedProduct->product->id]['product_id']))
                                                    @if($wishlist[$topRatedProduct->product->id]['product_id'] != $topRatedProduct->product->id)
                                                        <button type="button" class="btn btn-sm btn-primary rounded-pill btn-icon hover-translate-y-n3 add_to_wishlist wishlist_{{$topRatedProduct->product->id}}" data-id="{{$topRatedProduct->product->id}}">
                                                            <i class="far fa-heart"></i>
                                                        </button>
                                                    @else
                                                        <button type="button" class="btn btn-sm btn-primary rounded-pill btn-icon hover-translate-y-n3 bg-light-gray" data-id="{{$topRatedProduct->product->id}}" disabled>
                                                            <i class="fas fa-heart"></i>
                                                        </button>
                                                    @endif
                                                @else
                                                    <button type="button" class="btn btn-sm btn-primary rounded-pill btn-icon hover-translate-y-n3 add_to_wishlist wishlist_{{$topRatedProduct->product->id}}" data-id="{{$topRatedProduct->product->id}}">
                                                        <i class="far fa-heart"></i>
                                                    </button>
                                                @endif
                                            @else
                                                <button type="button" class="btn btn-sm btn-primary rounded-pill btn-icon hover-translate-y-n3 add_to_wishlist wishlist_{{$topRatedProduct->product->id}}" data-id="{{$topRatedProduct->product->id}}">
                                                        <i class="far fa-heart"></i>
                                                </button>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{--RANDOM--}}
    @if($theme3_product_random != null && $theme3_product_random->count()>0)
        <section class="your-time-section mt-6">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-12">
                        <div class="right-img"></div>
                        @if(!empty($theme3_product_random->is_cover) && \Storage::exists('uploads/is_cover_image/'.$theme3_product_random->is_cover))
                            <img class="hoodie-img" src="{{asset(Storage::url('uploads/is_cover_image/'.(!empty($theme3_product_random->is_cover)?$theme3_product_random->is_cover:'')))}}" title="{{$theme3_product_random->name}}" alt="Image Placeholder">
                        @else
                            <img class="hoodie-img" src="{{asset(Storage::url('uploads/is_cover_image/default.jpg'))}}" title="{{$theme3_product_random->name}}" alt="Image Placeholder">
                        @endif
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12 left-img">
                        <a href="{{route('store.product.product_view',[$store->slug,$theme3_product_random->id])}}" class="store-title t-secondary">{{$theme3_product_random->name}}</a>
                        <p class="mb-4 w-50 mt-3">{!! $theme3_product_random->detail !!}</p>
                        <div class="d-flex">
                            @if($theme3_product_random['enable_product_variant'] == 'on')
                                <div class="product-price m-0">
                                    <a href="{{route('store.product.product_view',[$store->slug,$theme3_product_random->id])}}" class="btn btn-sm btn-primary rounded-pill btn-icon hover-translate-y-n3">
                                        <i class="fas fa-shopping-basket"></i>
                                    </a>
                                    <h3 class="store-title-medium t-secondary ml-5">{{__('In variant')}}</h3>
                                </div>
                            @else
                                <div class="product-price m-0">
                                    <a class="btn btn-sm btn-black btn-icon add_to_cart" data-id="{{$theme3_product_random->id}}">
                                        <span class="btn-inner--text text-white">{{__('Add to cart')}}</span>
                                        <span class="btn-inner--icon text-white">
                                        <i class="fas fa-shopping-basket"></i>
                                    </span>
                                    </a>
                                    @if(Auth::guard('customers')->check())
                                        @if(!empty($wishlist) && isset($wishlist[$theme3_product_random->id]['product_id']))
                                            @if($wishlist[$theme3_product_random->id]['product_id'] != $theme3_product_random->id)
                                                <button type="button" class="btn btn-sm btn-primary rounded-pill btn-icon hover-translate-y-n3 add_to_wishlist wishlist_{{$theme3_product_random->id}}" data-id="{{$theme3_product_random->id}}">
                                                    <i class="far fa-heart"></i>
                                                </button>
                                            @else
                                                <button type="button" class="btn btn-sm btn-primary rounded-pill btn-icon hover-translate-y-n3 bg-light-gray" data-id="{{$theme3_product_random->id}}" disabled>
                                                    <i class="fas fa-heart"></i>
                                                </button>
                                            @endif
                                        @else
                                            <button type="button" class="btn btn-sm btn-primary rounded-pill btn-icon hover-translate-y-n3 add_to_wishlist wishlist_{{$theme3_product_random->id}}" data-id="{{$theme3_product_random->id}}">
                                                <i class="far fa-heart"></i>
                                            </button>
                                        @endif
                                    @else
                                        <button type="button" class="btn btn-sm btn-primary rounded-pill btn-icon hover-translate-y-n3 add_to_wishlist wishlist_{{$theme3_product_random->id}}" data-id="{{$theme3_product_random->id}}">
                                                <i class="far fa-heart"></i>
                                        </button>
                                    @endif    
                                </div>
                                <h3 class="store-title-medium t-secondary ml-5">{{\App\Models\Utility::priceFormat($theme3_product_random->price)}}</h3>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif

    <section id="pro_items" class="bestsellers-section bg-body">
        @if($products['Start shopping']->count() > 0)
            <div class="container mt-10 mb-5">
                <div class="row">
                    <div class="pr-title mb-4 bg-primary">
                        <h3 class="mt-4 store-title product_title">{{__('Products')}}</h3>
                        <div class="p-tablist">
                            <ul class="nav nav-tabs" id="myTab" role="tablist">
                                @foreach($categories as $key => $category)
                                <li class="nav-item">
                                    <a href="#{!! preg_replace('/[^A-Za-z0-9\-]/','_',$category)!!}" data-id="{{$key}}" class="nav-link bor-radius bg-primary text-dark product_title {{($key==0)?'active':''}} productTab" id="electronic-tab" data-toggle="tab" role="tab" aria-controls="home"
                                        aria-selected="false">
                                            {{__($category)}}
                                        </a>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <div class="tab-content bestsellers-tabs" id="myTabContent">
                        @foreach($products as $key => $items)
                            <div class="tab-pane fade {{($key=="Start shopping")?'active show':''}}" id="{!! preg_replace('/[^A-Za-z0-9\-]/', '_', $key)!!}" role="tabpanel" aria-labelledby="shopping-tab">
                                <div class="col-lg-12">
                                    <div class="row">
                                        @if($items->count() > 0)
                                            @foreach($items as $product)
                                                <div class="col-xl-4 col-lg-4 col-sm-6 product-box">
                                                    <div class="card card-fluid card-product">
                                                        <div class="card-image">
                                                            <a href="{{route('store.product.product_view',[$store->slug,$product->id])}}">
                                                                @if(!empty($product->is_cover) && \Storage::exists('uploads/is_cover_image/'.$product->is_cover))
                                                                    <img alt="Image placeholder" width="123" height="163" src="{{asset(Storage::url('uploads/is_cover_image/'.$product->is_cover))}}" class="img-center img-fluid">
                                                                @else
                                                                    <img alt="Image placeholder" width="123" height="163" src="{{asset(Storage::url('uploads/is_cover_image/default.jpg'))}}" class="img-center img-fluid">
                                                                @endif
                                                            </a>
                                                        </div>
                                                        <div class="card-body pt-0">
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
                                                            <h6>
                                                                <a class="t-black13" href="{{route('store.product.product_view',[$store->slug,$product->id])}}">
                                                                    {{$product->name}}
                                                                </a>
                                                            </h6>
                                                            <p class="text-sm">
                                                                <span class="td-gray">{{__('Category')}}:</span> {{$product->product_category()}}
                                                            </p>
                                                        </div>
                                                        <div class="card-body">
                                                            @if($product['enable_product_variant'] == 'on')
                                                                <div class="product-price m-0">
                                                                    {{--                                                                    <span class="card-price t-black15">{{__('In variant')}}</span>--}}
                                                                    <a href="{{route('store.product.product_view',[$store->slug,$product->id])}}">
                                                                        <button type="button" class="m-4 btn btn-sm btn-primary rounded-pill btn-icon hover-translate-y-n3">
                                                                            {{__('In variant')}}
                                                                            <i class="fas fa-shopping-basket"></i>
                                                                        </button>
                                                                    </a>
                                                                </div>
                                                            @else
                                                                <p><span class="card-price t-black15">{{\App\Models\Utility::priceFormat($product->price)}}</span></p>
                                                                <div class="product-price m-0">
                                                                    <a class="btn btn-sm btn-black btn-icon add_to_cart" data-id="{{$product->id}}">
                                                                        <span class="btn-inner--text text-white">{{__('Add to cart')}}</span>
                                                                        <span class="btn-inner--icon text-white">
                                                                            <i class="fas fa-shopping-basket"></i>
                                                                        </span>
                                                                    </a>
                                                                    @if(!empty($wishlist) && isset($wishlist[$product->id]['product_id']))
                                                                        @if($wishlist[$product->id]['product_id'] != $product->id)
                                                                            <button type="button" class="btn btn-sm btn-primary rounded-pill btn-icon hover-translate-y-n3 add_to_wishlist wishlist_{{$product->id}}" data-id="{{$product->id}}">
                                                                                <i class="far fa-heart"></i>
                                                                            </button>
                                                                        @else
                                                                            <button type="button" class="btn btn-sm btn-primary rounded-pill btn-icon hover-translate-y-n3 bg-light-gray" data-id="{{$product->id}}" disabled>
                                                                                <i class="fas fa-heart"></i>
                                                                            </button>
                                                                        @endif
                                                                    @else
                                                                        <button type="button" class="btn btn-sm btn-primary rounded-pill btn-icon hover-translate-y-n3 add_to_wishlist wishlist_{{$product->id}}" data-id="{{$product->id}}">
                                                                            <i class="far fa-heart"></i>
                                                                        </button>
                                                                    @endif
                                                                </div>
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
    </section>

    <!-- Products categories-->
    @if(isset($storethemesetting['enable_categories']) && $storethemesetting['enable_categories'] == 'on' && !empty($pro_categories))
        <section class="categories-section">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12 mb-5 text-center">
                        <h3 class="store-title t-secondary">{{$storethemesetting['categories']}}</h3>
                        <p>{{$storethemesetting['categories_title']}}</p>
                    </div>
                </div>
                <div class="row">
                    @foreach($pro_categories as $key=>$pro_categorie)
                        @if($product_count[$key] > 0)
                            <div class="col-lg-4 col-md-6 col-sm-12 c-box">
                                <div class="cat-box" style="height:  245px;">
                                    <h4 class="store-title-small">{{$pro_categorie->name}}</h4>
                                    <a class="see-more" href="{{route('store.categorie.product',[$store->slug,$pro_categorie->name])}}">{{__('SHOW MORE')}}</a>
                                </div>
                                <div class="cat-img">
                                    <a class="see-more" href="{{route('store.categorie.product',[$store->slug,$pro_categorie->name])}}">
                                        @if(!empty($pro_categorie->categorie_img) && \Storage::exists('uploads/product_image/'.$pro_categorie->categorie_img))
                                            <img height="221" width="112" src="{{asset(Storage::url('uploads/product_image/').(!empty($pro_categorie->categorie_img)?$pro_categorie->categorie_img:'default.jpg'))}}">
                                        @else
                                            <img height="221" width="112" src="{{asset(Storage::url('uploads/product_image/default.jpg'))}}">
                                        @endif
                                    </a>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- Testimonials (v1) -->
    @if(isset($storethemesetting['enable_testimonial']) && $storethemesetting['enable_testimonial']=='on')
        <section class="testimonial-section mt-5">
            <div class="container">
                <div class="row testimonial-slider">
                    <div class="col-lg-12">
                        <div class="swiper-js-container overflow-hidden">
                            <div class="swiper-container" data-swiper-items="1" data-swiper-space-between="0" data-swiper-sm-items="1" data-swiper-xl-items="1">
                                <div class="swiper-wrapper">
                                    @if(isset($storethemesetting['enable_testimonial1']) && $storethemesetting['enable_testimonial1']=='on')
                                        <div class="swiper-slide">
                                            <div class="row align-items-center">
                                                <div class="col-lg-5 col-md-12 col-sm-12">
                                                    <div class="col-lg-12 text-right">
                                                        <p class="sub-title">{{$storethemesetting['testimonial_main_heading_title']}}</p>
                                                        <h3 class="store-title t-secondary">{{$storethemesetting['testimonial_main_heading']}}</h3>
                                                    </div>
                                                    <div class="swiper-slide p-3">
                                                        <div class="card bg-transparent">
                                                            <div class="card-body">
                                                                <p class="t-dcs t-gray">{{$storethemesetting['testimonial_description1']}}</p>
                                                                <div class="d-flex collection-qoute">
                                                                    <h5 class="t-author t-black14">{{$storethemesetting['testimonial_name1']}}</h5>
                                                                    <small class="d-block t-author-dcs t-black">{{$storethemesetting['testimonial_about_us1']}}</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-7 col-md-12 col-sm-12">
                                                    @if(!empty($storethemesetting['testimonial_img1']) && \Storage::exists('uploads/store_logo/'.$storethemesetting['testimonial_img1']))
                                                        <img alt="Image placeholder" height="530px" width="700px" src="{{asset(Storage::url('uploads/store_logo/'.$storethemesetting['testimonial_img1']))}}" class="tes-img">
                                                    @else
                                                        <img alt="Image placeholder" height="530px" width="700px" src="{{asset(Storage::url('uploads/store_logo/default.jpg'))}}" class="tes-img">
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if(isset($storethemesetting['enable_testimonial2']) && $storethemesetting['enable_testimonial2']=='on')
                                        <div class="swiper-slide">
                                            <div class="row align-items-center">
                                                <div class="col-lg-5 col-md-12 col-sm-12">
                                                    <div class="col-lg-12 text-right">
                                                        <p class="sub-title">{{$storethemesetting['testimonial_main_heading_title']}}</p>
                                                        <h3 class="store-title t-secondary">{{$storethemesetting['testimonial_main_heading']}}</h3>
                                                    </div>
                                                    <div class="swiper-slide p-3">
                                                        <div class="card bg-transparent">
                                                            <div class="card-body">
                                                                <p class="t-dcs t-gray">{{$storethemesetting['testimonial_description2']}}</p>
                                                                <div class="d-flex collection-qoute">
                                                                    <h5 class="t-author t-black14">{{$storethemesetting['testimonial_name2']}}</h5>
                                                                    <small class="d-block t-author-dcs t-black">{{$storethemesetting['testimonial_about_us2']}}</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-7 col-md-12 col-sm-12">
                                                    @if(!empty($storethemesetting['testimonial_img2']) && \Storage::exists('uploads/store_logo/'.$storethemesetting['testimonial_img2']))
                                                        <img alt="Image placeholder" height="530px" width="700px" src="{{asset(Storage::url('uploads/store_logo/'.(!empty($storethemesetting['testimonial_img2'])?$storethemesetting['testimonial_img2']:'qo.png')))}}" class="tes-img">
                                                    @else
                                                        <img alt="Image placeholder" height="530px" width="700px" src="{{asset(Storage::url('uploads/store_logo/default.jpg'))}}" class="tes-img">
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if(isset($storethemesetting['enable_testimonial3']) && $storethemesetting['enable_testimonial3']=='on')
                                        <div class="swiper-slide">
                                            <div class="row align-items-center">
                                                <div class="col-lg-5 col-md-12 col-sm-12">
                                                    <div class="col-lg-12 text-right">
                                                        <p class="sub-title">{{$storethemesetting['testimonial_main_heading_title']}}</p>
                                                        <h3 class="store-title t-secondary">{{$storethemesetting['testimonial_main_heading']}}</h3>
                                                    </div>
                                                    <div class="swiper-slide p-3">
                                                        <div class="card bg-transparent">
                                                            <div class="card-body">
                                                                <p class="t-dcs t-gray">{{$storethemesetting['testimonial_description3']}}</p>
                                                                <div class="d-flex collection-qoute">
                                                                    <h5 class="t-author t-black14">{{$storethemesetting['testimonial_name3']}}</h5>
                                                                    <small class="d-block t-author-dcs t-black">{{$storethemesetting['testimonial_about_us3']}}</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-7 col-md-12 col-sm-12">
                                                    @if(!empty($storethemesetting['testimonial_img3']) && \Storage::exists('uploads/store_logo/'.$storethemesetting['testimonial_img3']))
                                                        <img alt="Image placeholder" height="530px" width="700px" src="{{asset(Storage::url('uploads/store_logo/'.(!empty($storethemesetting['testimonial_img3'])?$storethemesetting['testimonial_img3']:'qo.png')))}}" class="tes-img">
                                                    @else
                                                        <img alt="Image placeholder" height="530px" width="700px" src="{{asset(Storage::url('uploads/store_logo/default.jpg'))}}" class="tes-img">
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <!-- navigation buttons -->
                            <div id="js-prev1" class="swiper-button-prev"></div>
                            <div id="js-next1" class="swiper-button-next"></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif
@endsection
