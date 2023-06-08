@extends('storefront.layout.theme5')
@section('page-title')
    {{__('Home')}}
@endsection
@push('css-page')
    <style>
        .product-box .product-price {
            justify-content: unset;
        }
    </style>
@endpush
@section('content')
    {{--HEADER IMG--}}
    @if($storethemesetting['enable_header_img'] == 'on')
        <section class="contain-product container">
            <div class="row">
                <div class="col-lg-4 col-md-12">
                    <div class="banner-contain">
                        <h1>{{!empty($storethemesetting['header_title'])?$storethemesetting['header_title']:'Home Accessories'}}</h1>
                        <p>{{!empty($storethemesetting['header_desc'])?$storethemesetting['header_desc']:'There is only that moment and the incredible certainty that everything under the sun has been written by one hand only.'}}
                        </p>
                        <a href="#" class="btn btn-sm btn-primary btn-icon shadow hover-shadow-lg hover-translate-y-n3" id="pro_scroll">
                            <span class="btn-inner--text">{{!empty($storethemesetting['button_text'])?$storethemesetting['button_text']:__('Start shopping')}}</span>
                            <span class="btn-inner--icon">
                                <i class="fas fa-shopping-basket"></i>
                        </span>
                        </a>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="banner-product">
                        @if(!empty($storethemesetting['header_img']) && \Storage::exists('uploads/store_logo/'.$storethemesetting['header_img']))
                            <img width="350" height="433" src="{{asset(Storage::url('uploads/store_logo/'.$storethemesetting['header_img']))}}" alt="image"/>
                        @else
                            <img width="350" height="433" src="{{asset(Storage::url('uploads/store_logo/home-banner1.png'))}}" alt="image"/>

                        @endif
                    </div>
                </div>
                @if($theme3_product != null)
                    <div class="col-lg-4 col-md-6">
                    <div class="product-box">
                        <div class="card card-product">
                            <div class="box-rate">
                                <div class="static-rating static-rating-sm">
                                    @if($store->enable_rating == 'on')
                                        @for($i =1;$i<=5;$i++)
                                            @php
                                                $icon = 'fa-star';
                                                $color = '';
                                                $newVal1 = ($i-0.5);
                                                if($theme3_product->product_rating() < $i && $theme3_product->product_rating() >= $newVal1)
                                                {
                                                    $icon = 'fa-star-half-alt';
                                                }
                                                if($theme3_product->product_rating() >= $newVal1)
                                                {
                                                    $color = 'text-primary';
                                                }
                                            @endphp
                                            <i class="star fas {{$icon .' '. $color}}"></i>
                                        @endfor
                                    @endif
                                </div>
                                <div class="card-product-actions">
                                @if(Auth::guard('customers')->check())
                                    @if($theme3_product['enable_product_variant'] != 'on')
                                        @if(!empty($wishlist) && isset($wishlist[$theme3_product->id]['product_id']))
                                            @if($wishlist[$theme3_product->id]['product_id'] != $theme3_product->id)
                                                <button type="button" class="action-item wishlist-icon bg-light-gray add_to_wishlist wishlist_{{$theme3_product->id}}" data-id="{{$theme3_product->id}}">
                                                    <i class="far fa-heart"></i>
                                                </button>
                                            @else
                                                <button type="button" class="action-item wishlist-icon bg-light-gray" data-id="{{$theme3_product->id}}" disabled>
                                                    <i class="fas fa-heart"></i>
                                                </button>
                                            @endif
                                        @else
                                            <button type="button" class="action-item wishlist-icon bg-light-gray add_to_wishlist wishlist_{{$theme3_product->id}}" data-id="{{$theme3_product->id}}">
                                                <i class="far fa-heart"></i>
                                            </button>
                                        @endif
                                    @endif
                                @else
                                    <button type="button" class="action-item wishlist-icon bg-light-gray add_to_wishlist wishlist_{{$theme3_product->id}}" data-id="{{$theme3_product->id}}">
                                        <i class="far fa-heart"></i>
                                    </button>
                                @endif
                                </div>
                            </div>
                            <div class="card-image">
                                <a href="{{route('store.product.product_view',[$store->slug,$theme3_product->id])}}">
                                    @if($theme3_product_image->count() > 0 && \Storage::exists('uploads/product_image/'.$theme3_product_image[0]['product_images']))
                                        <img class="img-center img-fluid" width="135" height="167" src="{{asset(Storage::url('uploads/product_image/'.$theme3_product_image[0]['product_images']))}}" alt="New collection" title="New collection">
                                    @else
                                        <img class="img-center img-fluid" width="135" height="167" src="{{asset(Storage::url('uploads/product_image/default.jpg'))}}" alt="New collection" title="New collection">

                                    @endif
                                </a>

                            </div>
                            <div class="card-body pt-0">
                                <h6><a href="{{route('store.product.product_view',[$store->slug,$theme3_product->id])}}" class="t-black13">{{$theme3_product->name}}</a></h6>
                                @if($theme3_product['enable_product_variant'] != 'on')
                                    <div class="product-price mt-3">
                                        <span class="card-price t-black15 mb-2">{{\App\Models\Utility::priceFormat($theme3_product->price)}}</span>
                                    </div>
                                    <div class="p-button">
                                        <button type="button" class="action-item pcart-icon bg-primary">
                                            <i class="fas fa-shopping-basket"></i>
                                        </button>
                                        <a href="#" class="btn btn-sm btn-white btn-icon add_to_cart" data-id="{{$theme3_product['id']}}">
                                        <span class="btn-inner--text text-primary">
                                            {{__('Add to cart')}}
                                        </span>
                                            <span class="btn-inner--icon">
                                            <i class="fas fa-shopping-basket"></i>
                                        </span>
                                        </a>
                                    </div>
                                @else
                                    <div class="product-price mt-3">
                                        <span class="card-price t-black15 mb-2">{{__('In Variant')}}</span>
                                    </div>
                                    <div class="p-button">
                                        <button type="button" class="action-item pcart-icon bg-primary">
                                            <i class="fas fa-shopping-basket"></i>
                                        </button>
                                        <a href="{{route('store.product.product_view',[$store->slug,$theme3_product['id']])}}" class="btn btn-sm btn-white btn-icon">
                                        <span class="btn-inner--text text-primary">
                                            {{__('Add to cart')}}
                                        </span>
                                            <span class="btn-inner--icon">
                                            <i class="fas fa-shopping-basket"></i>
                                        </span>
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </section>
    @endif

    {{--BRAND LOGO--}}
    @if(isset($storethemesetting['enable_brand_logo']) && $storethemesetting['enable_brand_logo']=='on')
        <div class="client-logo">
            <div class="container">
                <div class="row">
                    @if(!empty($storethemesetting['brand_logo']))
                        @foreach(explode(',',$storethemesetting['brand_logo']) as $k => $value)
                            <div class="col-lg-2 col-md-3 col-sm-4 col-6">
                                <a href="#">
                                    @if(!empty($value) && \Storage::exists('uploads/store_logo/'.$value))
                                        <img src="{{asset(Storage::url('uploads/store_logo/').(!empty($value)?$value:'storego-image.png'))}}" alt="Brand logo">
                                    @else
                                        <img src="{{asset(Storage::url('uploads/store_logo/default.jpg'))}}" alt="Brand logo">
                                    @endif
                                </a>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Products categories-->
    @if(isset($storethemesetting['enable_categories']) && $storethemesetting['enable_categories'] == 'on' && !empty($pro_categories))
        <section class="electronic-access-section">
            <div class="container">
                <div class="row">
                    @foreach($pro_categories as $key=>$pro_categorie)
                        @if($product_count[$key] > 0)
                            <div class="col-lg-6 mt-2">
                                <div class="small-product small_product_custom">
                                    <div class="row">
                                        <div class="col-lg-5">
                                            @if(!empty($pro_categorie->categorie_img) && \Storage::exists('uploads/product_image/'.$pro_categorie->categorie_img))
                                                <img width="178" height="209" src="{{asset(Storage::url('uploads/product_image/'.$pro_categorie->categorie_img))}}" class="small-img" alt="image"/>
                                            @else
                                                <img width="178" height="209" src="{{asset(Storage::url('uploads/product_image/default.jpg'))}}" class="small-img" alt="image"/>
                                            @endif
                                        </div>
                                        <div class="col-lg-7">
                                            <div class="small-pro-detail">
                                                <h2>{{$pro_categorie->name}}</h2>
                                                <p>{{__('Products')}}: {{!empty($product_count[$key])?$product_count[$key]:'0'}}</p>
                                                <a href="{{route('store.categorie.product',[$store->slug,$pro_categorie->name])}}" class="btn btn-sm btn-primary btn-icon shadow hover-shadow-lg hover-translate-y-n3">
                                                    <span class="btn-inner--text">{{__('Start shopping')}}</span>
                                                    <span class="btn-inner--icon">
                                                  <i class="fas fa-shopping-basket"></i>
                                                </span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{--Start shopping--}}
    @if($products['Start shopping']->count() > 0)
        <section class="bestsellers-section" id="pro_items">
            <div class="container">
                <div class="row">
                    <div class="pr-title mb-4">
                        <div class="">
                            <h3 class="mt-4 store-title text-primary">{{__('Products')}}</h3>
                            <div class="p-tablist">
                                <ul class="nav nav-tabs" id="myTab" role="tablist">
                                    @foreach($categories as $key=>$category)
                                        <li class="nav-item">
                                            <a href="#{!!preg_replace('/[^A-Za-z0-9\-]/','_',$category)!!}" data-id="{{$key}}" class="nav-link  {{($category=='Start shopping')?'active':''}} productTab" id="electronic-tab" data-toggle="tab" role="tab" aria-controls="home" aria-selected="false">
                                                {{__($category)}}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        <div>
                            <a href="{{route('store.categorie.product',[$store->slug,'Start shopping'])}}" class="btn btn-sm btn-primary btn-icon shadow hover-shadow-lg hover-translate-y-n3">
                                <span class="btn-inner--text">{{__('Start shopping')}}</span>
                                <span class="btn-inner--icon">
                                <i class="fas fa-shopping-basket"></i>
                            </span>
                            </a>
                        </div>
                    </div>
                    <div class="tab-content bestsellers-tabs" id="myTabContent">
                        @foreach($products as $key=>$items)
                            <div class="tab-pane fade {{($key=='Start shopping')?'active show':''}}" id="{!! preg_replace('/[^A-Za-z0-9\-]/', '_', $key)!!}" role="tabpanel" aria-labelledby="shopping-tab">
                                <div class="col-lg-12">
                                    <div class="row">
                                        @if($items->count() > 0)
                                            @foreach($items as $product)
                                                <div class="col-xl-3 col-lg-4 col-sm-6">
                                                    <div class="product-box">
                                                        <div class="card card-product card-fluid">
                                                            <div class="box-rate">
                                                                <div class="static-rating static-rating-sm">
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
                                                                </div>
                                                                <div class="card-product-actions">
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
                                                            <div class="card-image py-3">
                                                                <a href="{{route('store.product.product_view',[$store->slug,$product->id])}}">
                                                                    @if(!empty($product->is_cover) && \Storage::exists('uploads/is_cover_image/'.$product->is_cover))
                                                                        <img class="img-center img-fluid" style="width:135px; height:167px" src="{{asset(Storage::url('uploads/is_cover_image/'.$product->is_cover))}}" alt="New collection" title="New collection">
                                                                    @else
                                                                        <img class="img-center img-fluid" style="width:135px; height:167px" src="{{asset(Storage::url('uploads/is_cover_image/default.jpg'))}}" alt="New collection" title="New collection">
                                                                    @endif
                                                                </a>
                                                            </div>
                                                            <div class="card-body pt-0">
                                                                <h6><a href="{{route('store.product.product_view',[$store->slug,$product->id])}}" class="t-black13">{{$product->name}}</a></h6>
                                                                @if($product['enable_product_variant'] != 'on')
                                                                    <div class="product-price mt-3">
                                                                        <span class="card-price t-black15 mb-2">{{\App\Models\Utility::priceFormat($product->price)}}</span>
                                                                    </div>
                                                                    <div class="p-button">
                                                                        <button type="button" class="action-item pcart-icon bg-primary">
                                                                            <i class="fas fa-shopping-basket"></i>
                                                                        </button>
                                                                        <a href="#" class="btn btn-sm btn-white btn-icon add_to_cart" data-id="{{$product['id']}}">
                                                                        <span class="btn-inner--text text-primary">
                                                                            {{__('Add to cart')}}
                                                                        </span>
                                                                            <span class="btn-inner--icon">
                                                                            <i class="fas fa-shopping-basket"></i>
                                                                        </span>
                                                                        </a>
                                                                    </div>
                                                                @else
                                                                    <div class="product-price mt-3">
                                                                        <span class="card-price t-black15 mb-2">{{__('In Variant')}}</span>
                                                                    </div>
                                                                    <div class="p-button">
                                                                        <button type="button" class="action-item pcart-icon bg-primary">
                                                                            <i class="fas fa-shopping-basket"></i>
                                                                        </button>
                                                                        <a href="{{route('store.product.product_view',[$store->slug,$product['id']])}}" class="btn btn-sm btn-white btn-icon">
                                                                        <span class="btn-inner--text text-primary">
                                                                            {{__('Add to cart')}}
                                                                        </span>
                                                                            <span class="btn-inner--icon">
                                                                            <i class="fas fa-shopping-basket"></i>
                                                                        </span>
                                                                        </a>
                                                                    </div>
                                                                @endif
                                                            </div>
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
        </section>
    @else
        <div class="container mt-10 mb-5">
            {{__('No data found')}}
        </div>
    @endif

    {{--EMAIL SUBSCRIPTION--}}
    @if(isset($storethemesetting['enable_email_subscriber']) && $storethemesetting['enable_email_subscriber']=='on')
        <section class="alwase-on-section">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-sm-12 col-lg-12 col-xl-7 text-center">
                        <div class="mb-5">
                            <h1 class="store-title text-primary">{{$storethemesetting['subscriber_title']}}</h1>
                            <p class="lead mt-2 store-dcs">{{$storethemesetting['subscriber_sub_title']}}</p>
                        </div>
                        {{Form::open(array('route' => array('subscriptions.store_email', $store->id),'method' => 'POST'))}}
                        <div class="form-group form-subscribe">
                            <div class="input-group input-group-lg input-group-merge">
                                {{Form::email('email',null,array('class'=>'form-control form-control-flush','aria-label'=>'Enter your email address','placeholder'=>__('Enter Your Email Address')))}}
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-primary btn-icon scroll-me">
                                        <span class="btn-inner--text">{{__('Subscribe')}}</span>
                                        <span class="far fa-paper-plane"></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        {{Form::close()}}
                    </div>
                </div>
            </div>
        </section>
    @endif

    <!-- Top Rated Products -->
    @if(count($topRatedProducts)>0)
        <section class="top-product mt-5">
            <div class="container">
                <div class="row">
                    <div class="pr-title">
                        <h3 class=" mt-4 store-title text-primary">{{__('Top rated products')}}</h3>
                        <a href="{{route('store.categorie.product',[$store->slug,'Start shopping'])}}" class="btn btn-sm btn-primary rounded-pill btn-icon">
                            <span class="btn-inner--text">{{__('Show more products')}}</span>
                            <span class="btn-inner--icon">
                          <i class="fas fa-shopping-basket"></i>
                        </span>
                        </a>
                    </div>
                </div>
                <div class="row justify-content-center">
                    @foreach($topRatedProducts  as $k => $topRatedProduct)
                        <div class="col-xl-3 col-lg-4 col-sm-6">
                            <div class="product-box">
                                <div class="card card-product">
                                    <div class="box-rate">
                                        <div class="static-rating static-rating-sm">
                                            @if($store->enable_rating == 'on')
                                                @for($i =1;$i<=5;$i++)
                                                    @php
                                                        $icon = 'fa-star';
                                                        $color = '';
                                                        $newVal1 = ($i-0.5);
                                                        if($topRatedProduct->product->product_rating() < $i && $topRatedProduct->product->product_rating() >= $newVal1)
                                                        {
                                                            $icon = 'fa-star-half-alt';
                                                        }
                                                        if($topRatedProduct->product->product_rating() >= $newVal1)
                                                        {
                                                            $color = 'text-primary';
                                                        }
                                                    @endphp
                                                    <i class="star fas {{$icon .' '. $color}}"></i>
                                                @endfor
                                            @endif
                                        </div>
                                        <div class="card-product-actions">
                                        @if(Auth::guard('customers')->check())
                                            @if(!empty($wishlist) && isset($wishlist[$topRatedProduct->product->id]['product_id']))
                                                @if($wishlist[$topRatedProduct->product->id]['product_id'] != $topRatedProduct->product->id)
                                                    <button type="button" class="action-item wishlist-icon bg-light-gray add_to_wishlist wishlist_{{$topRatedProduct->product->id}}" data-id="{{$topRatedProduct->product->id}}">
                                                        <i class="far fa-heart"></i>
                                                    </button>
                                                @else
                                                    <button type="button" class="action-item wishlist-icon bg-light-gray" data-id="{{$topRatedProduct->product->id}}" disabled>
                                                        <i class="fas fa-heart"></i>
                                                    </button>
                                                @endif
                                            @else
                                                <button type="button" class="action-item wishlist-icon bg-light-gray add_to_wishlist wishlist_{{$topRatedProduct->product->id}}" data-id="{{$topRatedProduct->product->id}}">
                                                    <i class="far fa-heart"></i>
                                                </button>
                                            @endif
                                        @else
                                            <button type="button" class="action-item wishlist-icon bg-light-gray add_to_wishlist wishlist_{{$topRatedProduct->product->id}}" data-id="{{$topRatedProduct->product->id}}">
                                                <i class="far fa-heart"></i>
                                            </button>
                                        @endif
                                        </div>
                                    </div>
                                    <div class="card-image py-3">
                                        <a href="{{route('store.product.product_view',[$store->slug,$topRatedProduct->product->id])}}">
                                            @if(!empty($pro_categorie->categorie_img) && \Storage::exists('uploads/is_cover_image/'.$topRatedProduct->product->is_cover))
                                                <img class="img-center img-fluid" style="width:135px; height:167px" src="{{asset(Storage::url('uploads/is_cover_image/'.$topRatedProduct->product->is_cover))}}" alt="New collection" title="New collection">
                                            @else
                                                <img class="img-center img-fluid" style="width:135px; height:167px" src="{{asset(Storage::url('uploads/is_cover_image/default.jpg'))}}" alt="New collection" title="New collection">
                                            @endif

                                        </a>
                                    </div>
                                    <div class="card-body pt-0">
                                        <h6><a href="{{route('store.product.product_view',[$store->slug,$topRatedProduct->product->id])}}" class="t-black13">{{$topRatedProduct->product->name}}</a></h6>
                                        @if($topRatedProduct->product->enable_product_variant != 'on')
                                            <div class="product-price mt-3">
                                                <span class="card-price t-black15 mb-2">{{\App\Models\Utility::priceFormat($topRatedProduct->product->price)}}</span>
                                            </div>
                                            <div class="p-button">
                                                <button type="button" class="action-item pcart-icon bg-primary">
                                                    <i class="fas fa-shopping-basket"></i>
                                                </button>
                                                <a href="#" class="btn btn-sm btn-white btn-icon add_to_cart" data-id="{{$topRatedProduct->product->id}}">
                <span class="btn-inner--text text-primary">
                    {{__('Add to cart')}}
                </span>
                                                    <span class="btn-inner--icon">
                    <i class="fas fa-shopping-basket"></i>
                </span>
                                                </a>
                                            </div>
                                        @else
                                            <div class="product-price mt-3">
                                                <span class="card-price t-black15 mb-2">{{__('In Variant')}}</span>
                                            </div>
                                            <div class="p-button">
                                                <button type="button" class="action-item pcart-icon bg-primary">
                                                    <i class="fas fa-shopping-basket"></i>
                                                </button>
                                                <a href="{{route('store.product.product_view',[$store->slug,$topRatedProduct->product->id])}}" class="btn btn-sm btn-white btn-icon">
                <span class="btn-inner--text text-primary">
                    {{__('Add to cart')}}
                </span>
                                                    <span class="btn-inner--icon">
                    <i class="fas fa-shopping-basket"></i>
                </span>
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- Testimonials (v1) -->
    @if(isset($storethemesetting['enable_testimonial']) && $storethemesetting['enable_testimonial']=='on')
        <section class="slice testimonial-section ">
            <div class="container-fulid">
                <div class="row testimonial-slider">
                    <div class="col-lg-4 col-md-12 col-sm-12">
                        <div class="mb-5">
                            <h3 class=" mt-4 store-title text-primary">{{$storethemesetting['testimonial_main_heading']}}</h3>
                            <div class="mt-3">
                                <p class="lead lh-180 store-dcs">{{$storethemesetting['testimonial_main_heading_title']}}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-8 col-md-12 col-sm-12">
                        <div class="swiper-js-container overflow-hidden">
                            <div class="swiper-container" data-swiper-items="1" data-swiper-space-between="0" data-swiper-sm-items="2"
                                 data-swiper-xl-items="2">
                                <div class="swiper-wrapper">
                                    @if(isset($storethemesetting['enable_testimonial1']) && $storethemesetting['enable_testimonial1']=='on')
                                        <div class="swiper-slide p-3">
                                            <div class="card bg-transparent">
                                                <div class="card-body">
                                                    <p class="t-dcs t-gray">{{$storethemesetting['testimonial_description1']}}</p>
                                                    <div class="d-flex align-items-center">
                                                        <div>
                                                            <img alt="Image placeholder" src="{{asset(Storage::url('uploads/store_logo/'.$storethemesetting['testimonial_img1']))}}" class="avatar  rounded-circle">
                                                        </div>
                                                        <div class="pl-3">
                                                            <h5 class="t-author t-black14">{{$storethemesetting['testimonial_name1']}}</h5>
                                                            <small class="d-block t-author-dcs">{{$storethemesetting['testimonial_about_us1']}}</small>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if(isset($storethemesetting['enable_testimonial2']) && $storethemesetting['enable_testimonial2']=='on')
                                        <div class="swiper-slide p-3">
                                            <div class="card bg-transparent">
                                                <div class="card-body">
                                                    <p class="t-dcs t-gray">{{$storethemesetting['testimonial_description2']}}</p>
                                                    <div class="d-flex align-items-center">
                                                        <div>
                                                            <img alt="Image placeholder" src="{{asset(Storage::url('uploads/store_logo/'.$storethemesetting['testimonial_img2']))}}" class="avatar  rounded-circle">
                                                        </div>
                                                        <div class="pl-3">
                                                            <h5 class="t-author t-black24">{{$storethemesetting['testimonial_name2']}}</h5>
                                                            <small class="d-block t-author-dcs">{{$storethemesetting['testimonial_about_us2']}}</small>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if(isset($storethemesetting['enable_testimonial3']) && $storethemesetting['enable_testimonial3']=='on')
                                        <div class="swiper-slide p-3">
                                            <div class="card bg-transparent">
                                                <div class="card-body">
                                                    <p class="t-dcs t-gray">{{$storethemesetting['testimonial_description3']}}</p>
                                                    <div class="d-flex align-items-center">
                                                        <div>
                                                            <img alt="Image placeholder" src="{{asset(Storage::url('uploads/store_logo/'.$storethemesetting['testimonial_img3']))}}" class="avatar  rounded-circle">
                                                        </div>
                                                        <div class="pl-3">
                                                            <h5 class="t-author t-black34">{{$storethemesetting['testimonial_name3']}}</h5>
                                                            <small class="d-block t-author-dcs">{{$storethemesetting['testimonial_about_us3']}}</small>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                </div>
                                <!-- Add Pagination -->
                                <!-- <div class="swiper-pagination w-100 mt-4 d-flex align-items-center justify-content-center"></div> -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif

    <!-- Top Enable Features -->
    @if(isset($storethemesetting['enable_features']) && $storethemesetting['enable_features'] == 'on')
        <section class="store-promotions common-space70">
            <div class="container">
                <div class="row">
                    @if(isset($storethemesetting['enable_features1']) &&$storethemesetting['enable_features1'] == 'on')
                        @if(isset($storethemesetting['features_icon1']))
                            <div class="col-lg-3 col-sm-6">
                                <div class="store-box">
                                    <div class="icon text-primary mr-3">
                                        {!! $storethemesetting['features_icon1'] !!}
                                    </div>
                                    <div class="s-data">
                                        <strong class="text-primary">{{$storethemesetting['features_title1']}}</strong>
                                        <p class=" mt-2 mb-0 t-gray">{{$storethemesetting['features_description1']}}</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif
                    @if(isset($storethemesetting['enable_features2']) &&$storethemesetting['enable_features2'] == 'on')
                        @if(isset($storethemesetting['features_icon2']))
                            <div class="col-lg-3 col-sm-6">
                                <div class="store-box">
                                    <div class="icon text-primary mr-3">
                                        {!! $storethemesetting['features_icon2'] !!}
                                    </div>
                                    <div class="s-data">
                                        <strong class="text-primary">{{$storethemesetting['features_title2']}}</strong>
                                        <p class=" mt-2 mb-0 t-gray">{{$storethemesetting['features_description2']}}</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif
                    @if(isset($storethemesetting['enable_features3']) &&$storethemesetting['enable_features3'] == 'on')
                        @if(isset($storethemesetting['features_icon3']))
                            <div class="col-lg-3 col-sm-6">
                                <div class="store-box">
                                    <div class="icon text-primary mr-3">
                                        {!! $storethemesetting['features_icon3'] !!}
                                    </div>
                                    <div class="s-data">
                                        <strong class="text-primary">{{$storethemesetting['features_title3']}}</strong>
                                        <p class=" mt-2 mb-0 t-gray">{{$storethemesetting['features_description3']}}</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </section>
    @endif
@endsection
