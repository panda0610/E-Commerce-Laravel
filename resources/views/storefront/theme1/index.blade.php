@extends('storefront.layout.theme1')
@section('page-title')
    {{__('Home')}}
@endsection
@push('css-page')

@endpush
@section('content')
    @if($storethemesetting['enable_header_img'] == 'on')
        <section class="slice slice-xl bg-cover bg-size--cover home-banner" data-offset-top="#header-main" style="background-image: url({{asset(Storage::url('uploads/store_logo/'.(!empty($storethemesetting['header_img'])?$storethemesetting['header_img']:'home-banner.png')))}}); background-position: center center;">
            <span class="mask bg-dark opacity-3"></span>
            <div class="container py-6">
                <div class="row justify-content-center">
                    <div class="col-lg-8 text-center">
                        <h2 class="h1 text-white store-title">
                            {{!empty($storethemesetting['header_title'])?$storethemesetting['header_title']:'Home Accessories'}}
                        </h2>
                        <p class="lead text-white mt-4 store-dcs">
                            {{!empty($storethemesetting['header_desc'])?$storethemesetting['header_desc']:'There is only that moment and the incredible certainty that everything under the sun has been written by one hand only.'}}
                        </p>
                        <button href="#" class="btn btn-white btn-white btn-icon rounded-pill hover-translate-y-n3 mt-4" id="pro_scroll">
                            <span class="btn-inner--text">{{!empty($storethemesetting['button_text'])?$storethemesetting['button_text']:__('Start shopping')}}</span>
                            <span class="btn-inner--icon"><i class="fas fa-angle-right"></i></span>
                        </button>
                    </div>
                </div>
            </div>
        </section>
    @endif

    <!-- Features -->
    @if(isset($storethemesetting['enable_features']) && $storethemesetting['enable_features'] == 'on')
        <section class="store-promotions mt-70">
            <div class="container">
                <div class="row">
                    @if(isset($storethemesetting['enable_features1']) &&$storethemesetting['enable_features1'] == 'on')
                        @if(isset($storethemesetting['features_icon1']))
                            <div class="col-lg-4 col-sm-6">
                                <div class="mb-4">
                                    <div class="icon text-primary">
                                        {!! $storethemesetting['features_icon1'] !!}
                                    </div>
                                    <strong class="text-primary">{{$storethemesetting['features_title1']}}</strong>
                                    <p class=" mt-2 mb-0 t-gray">{{$storethemesetting['features_description1']}}</p>
                                </div>
                            </div>
                        @endif
                    @endif
                    @if(isset($storethemesetting['enable_features2']) &&$storethemesetting['enable_features2'] == 'on')
                        @if(isset($storethemesetting['features_icon2']))
                            <div class="col-lg-4 col-sm-6">
                                <div class="mb-4">
                                    <div class="icon text-primary">
                                        {!! $storethemesetting['features_icon2'] !!}
                                    </div>
                                    <strong class="text-primary">{{$storethemesetting['features_title2']}}</strong>
                                    <p class=" mt-2 mb-0 t-gray">{{$storethemesetting['features_description2']}}</p>
                                </div>
                            </div>
                        @endif
                    @endif
                    @if(isset($storethemesetting['enable_features3']) &&$storethemesetting['enable_features3'] == 'on')
                        @if(isset($storethemesetting['features_icon3']))
                            <div class="col-lg-4 col-sm-6">
                                <div class="mb-4">
                                    <div class="icon text-primary">
                                        {!! $storethemesetting['features_icon3'] !!}
                                    </div>
                                    <strong class="text-primary">{{$storethemesetting['features_title3']}}</strong>
                                    <p class=" mt-2 mb-0 t-gray">{{$storethemesetting['features_description3']}}</p>
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </section>
    @endif

    <!-- Products -->
    @if($products['Start shopping']->count() > 0)
        <section id="pro_items" class="bestsellers-section">
            <div class="container">
                <div class="row">
                    <div class="pr-title mb-4">
                        <h3 class=" mt-4 store-title text-primary">{{__('Products')}}</h3>
                        <div class="p-tablist">
                            <ul class="nav nav-tabs" id="myTab" role="tablist">
                                @foreach($categories as $key=>$category)
                                    <li class="nav-item">
                                        <a href="#{!!preg_replace('/[^A-Za-z0-9\-]/','_',$category)!!}" data-id="{{$key}}" class="nav-link {{($key==0)?'active':''}} productTab" id="electronic-tab" data-toggle="tab" role="tab" aria-controls="home" aria-selected="false">
                                            {{__($category)}}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <div class="tab-content bestsellers-tabs" id="myTabContent">

                        @foreach($products as $key=>$items)
                            <div class="tab-pane fade {{($key=="Start shopping")?'active show':''}}" id="{!! preg_replace('/[^A-Za-z0-9\-]/', '_', $key)!!}" role="tabpanel" aria-labelledby="shopping-tab">
                                <div class="col-lg-12">
                                    <div class="row">
                                        @if($items->count() > 0)
                                            @foreach($items as $product)
                                                
                                                    <div class="col-xl-3 col-lg-4 col-sm-6 product-box">
                                                        <div class="card card-fluid card-product">
                                                            <div class="card-image">
                                                                <a href="{{route('store.product.product_view',[$store->slug,$product->id])}}">
                                                                    @if(!empty($product->is_cover) && \Storage::exists('uploads/is_cover_image/'.$product->is_cover))
                                                                        <img alt="Image placeholder" src="{{asset(Storage::url('uploads/is_cover_image/'.$product->is_cover))}}" class="img-center img-fluid">
                                                                    @else
                                                                        <img alt="Image placeholder" src="{{asset(Storage::url('uploads/is_cover_image/default.jpg'))}}" class="img-center img-fluid">
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
                                                                <div class="product-price mt-3">
                                                                <span class="card-price t-black15">
                                                                    @if($product->enable_product_variant == 'on')
                                                                        {{__('In variant')}}
                                                                    @else
                                                                        {{\App\Models\Utility::priceFormat($product->price)}}
                                                                    @endif
                                                                </span>
                                                                    @if($product->enable_product_variant == 'on')
                                                                        <a href="{{route('store.product.product_view',[$store->slug,$product->id])}}" class="action-item pcart-icon bg-primary">
                                                                            <i class="fas fa-shopping-basket"></i>
                                                                        </a>
                                                                    @else
                                                                        <a class="action-item pcart-icon bg-primary add_to_cart" data-id="{{$product->id}}">
                                                                            <i class="fas fa-shopping-basket"></i>
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
    @endif

    <!-- subscriber-->
    @if(isset($storethemesetting['enable_email_subscriber']) && $storethemesetting['enable_email_subscriber']=='on')
        @if($storethemesetting['enable_email_subscriber'] == 'on')
            <section class="slice slice-xl bg-cover bg-size--cover" style="background-image: url({{asset(Storage::url('uploads/store_logo/'.(!empty($storethemesetting['subscriber_img'])?$storethemesetting['subscriber_img']:'img-17.jpg')))}}); background-position: center center;">
                <div class="container py-6">
                    <div class="row justify-content-center">
                        <div class="col-sm-12 col-lg-12 col-xl-12 text-center">
                            <div class="mb-5">
                                <h1 class="text-white store-title">{{!empty($storethemesetting['subscriber_title'])?$storethemesetting['subscriber_title']:'Always on time'}}</h1>
                                <p class="lead text-white mt-2 store-dcs">{{!empty($storethemesetting['subscriber_sub_title'])?$storethemesetting['subscriber_sub_title']:'Subscription here'}}</p>
                            </div>
                            {{Form::open(array('route' => array('subscriptions.store_email', $store->id),'method' => 'POST'))}}
                            <div class="form-group mb-0 form-subscribe">
                                <div class="input-group input-group-lg input-group-merge">
                                    {{Form::email('email',null,array('class'=>'form-control bg-white form-control-flush rounded-pill','aria-label'=>'Enter your email address','placeholder'=>__('Enter Your Email Address')))}}
                                    <div class="input-group-append ml-3">
                                        <button type="submit" class="btn btn-primary rounded-pill hover-translate-y-n3 btn-icon mr-sm-4 scroll-me">
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
    @endif

    <!-- Products -->
    @if(count($topRatedProducts)>0)
        <section class="top-product">
            <div class="container">
                <div class="row">
                    <div class="pr-title">
                        <h3 class=" mt-4 store-title text-primary">{{__('Top rated products')}}</h3>
                        <a href="{{route('store.categorie.product',$store->slug)}}" class="btn btn-sm btn-primary rounded-pill btn-icon">
                            <span class="btn-inner--text">{{__('Show more products')}}</span>
                            <i class="fas fa-shopping-basket"></i>
                            </span>
                        </a>
                    </div>
                </div>
                <div class="row">
                    @foreach($topRatedProducts as $k => $topRatedProduct)
                        <div class="col-xl-3 col-lg-4 col-sm-6 product-box">
                            <div class="card card-product">
                                <div class="card-image">
                                    <a href="{{route('store.product.product_view',[$store->slug,$product->id])}}">
                                        @if(!empty($topRatedProduct->product->is_cover) && \Storage::exists('uploads/is_cover_image/'.$topRatedProduct->product->is_cover))
                                            <img alt="Image placeholder" src="{{asset(Storage::url('uploads/is_cover_image/'.$topRatedProduct->product->is_cover))}}" class="img-center img-fluid">
                                        @else
                                            <img alt="Image placeholder" src="{{asset(Storage::url('uploads/is_cover_image/default.jpg'))}}" class="img-center img-fluid">
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
                                                if($topRatedProduct->product->product_rating() < $i && $product->product_rating() >= $newVal1)
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
                                    <h6><a class="t-black13" href="#">{{$topRatedProduct->product->name}}</a></h6>
                                    <p class="text-sm">
                                        <span class="td-gray">{{__('Category')}}:</span> {{$topRatedProduct->product->product_category()}}
                                    </p>
                                    <div class="product-price mt-3">
                                        <span class="card-price t-black15">
                                            @if($topRatedProduct->product->enable_product_variant == 'on')
                                                {{__('In variant')}}
                                            @else
                                                {{\App\Models\Utility::priceFormat($topRatedProduct->product->price)}}
                                            @endif
                                        </span>
                                        @if($topRatedProduct->product->enable_product_variant == 'on')
                                            <a href="{{route('store.product.product_view',[$store->slug,$topRatedProduct->product->id])}}" class="action-item pcart-icon bg-primary">
                                                <i class="fas fa-shopping-basket"></i>
                                            </a>
                                        @else
                                            <a href="javascript:void(0)" class="action-item pcart-icon bg-primary add_to_cart" data-id="{{$topRatedProduct->product->id}}">
                                                <i class="fas fa-shopping-basket"></i>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                                <div class="actions card-product-actions">
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
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- Products categories-->
    @if(isset($storethemesetting['enable_categories']) && $storethemesetting['enable_categories'] == 'on' && !empty($pro_categories))
        @if($storethemesetting['enable_categories'] == 'on')
            <section class="categorie-section">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="mb-5 text-center">
                                <h3 class=" mt-4 store-title text-primary">{{!empty($storethemesetting['categories'])?$storethemesetting['categories']:'Categories'}}</h3>
                                <div class="fluid-paragraph mt-3">
                                    <p class="lead lh-180 store-dcs">{{!empty($storethemesetting['categories_title'])?$storethemesetting['categories_title']:'There is only that moment and the incredible certainty <br> that
                                    everything
                                    under the sun has been written by one hand only.'}}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        @foreach($pro_categories as $key=>$pro_categorie)
                            @if($product_count[$key] > 0)
                                <div class="col-lg-4 col-md-6 col-sm-6 categories-box mb-4">
                                    <div class="cat-box">
                                        @if(!empty($pro_categorie->categorie_img) && \Storage::exists('uploads/product_image/'.$product->categorie_img))
                                            <img alt="Image placeholder" src="{{asset(Storage::url('uploads/product_image/'.$pro_categorie->categorie_img))}}" class="product bor-radius" height="350px">
                                        @else
                                            <img alt="Image placeholder" src="{{asset(Storage::url('uploads/product_image/default.jpg'))}}" class="product bor-radius" height="350px">
                                        @endif
                                    </div>
                                    <div class="cat-dcs">
                                        <h3 class="t-white">{{$pro_categorie->name}}</h3>
                                        <p class="t-white">{{__('Products')}}: {{!empty($product_count[$key])?$product_count[$key]:'0'}}</p>
                                        <a href="{{route('store.categorie.product',[$store->slug,$pro_categorie->name])}}" class="btn btn-sm btn-primary rounded-pill btn-icon">
                                            <span class="btn-inner--text">{{__('Show more products')}}</span>
                                            <span class="btn-inner--icon">
                                                <i class="fas fa-shopping-basket"></i>
                                            </span>
                                        </a>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </section>
        @endif
    @endif

    <!-- Testimonials (v1) -->
    @if(isset($storethemesetting['enable_testimonial']) && $storethemesetting['enable_testimonial']=='on')
        <section class="slice testimonial-section">
            <div class="container-fulid">
                <div class="mb-5 text-center">
                    <h3 class=" mt-4 store-title text-primary">{{!empty($storethemesetting['testimonial_main_heading'])?$storethemesetting['testimonial_main_heading']:'Testimonials'}}</h3>
                    <div class="fluid-paragraph mt-3">
                        <p class="lead lh-180 store-dcs">{{!empty($storethemesetting['testimonial_main_heading_title'])?$storethemesetting['testimonial_main_heading_title']:'There is only that moment and the incredible certainty that <br> everything
                            under the sun has been written by one hand only.'}}</p>
                    </div>
                </div>
                <div class="container">
                    <div class="row testimonial-slider">
                        <div class="col-lg-12">
                            <div class="swiper-js-container overflow-hidden">
                                <div class="swiper-container" data-swiper-items="1" data-swiper-space-between="0" data-swiper-sm-items="2"
                                     data-swiper-xl-items="3">
                                    <div class="swiper-wrapper">
                                        @if(isset($storethemesetting['enable_testimonial1']) && $storethemesetting['enable_testimonial1']=='on')
                                            <div class="swiper-slide p-3">
                                                <div class="card bg-transparent">
                                                    <div class="card-body">
                                                        <p class="t-dcs t-gray">{{$storethemesetting['testimonial_description1']}}</p>
                                                        <div class="d-flex align-items-center">
                                                            <div>
                                                                <img alt="" src="{{asset(Storage::url('uploads/store_logo/'.(!empty($storethemesetting['testimonial_img1'])?$storethemesetting['testimonial_img1']:'qo.png')))}}" class="avatar rounded-circle">
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
                                                                <img alt="" src="{{asset(Storage::url('uploads/store_logo/'.(!empty($storethemesetting['testimonial_img2'])?$storethemesetting['testimonial_img2']:'qo.png')))}}" class="avatar rounded-circle">
                                                            </div>
                                                            <div class="pl-3">
                                                                <h5 class="t-author t-black14">{{$storethemesetting['testimonial_name2']}}</h5>
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
                                                                <img alt="" src="{{asset(Storage::url('uploads/store_logo/'.(!empty($storethemesetting['testimonial_img3'])?$storethemesetting['testimonial_img3']:'qo.png')))}}" class="avatar rounded-circle">
                                                            </div>
                                                            <div class="pl-3">
                                                                <h5 class="t-author t-black14">{{$storethemesetting['testimonial_name3']}}</h5>
                                                                <small class="d-block t-author-dcs">{{$storethemesetting['testimonial_about_us3']}}</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <!-- Add Pagination -->
                                <div class="swiper-pagination w-100 mt-4 d-flex align-items-center justify-content-center"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif

    <!-- Client Logo -->
    @if(isset($storethemesetting['enable_brand_logo']) && $storethemesetting['enable_brand_logo']=='on')
        <div class="client-logo">
            <div class="container">
                <div class="row">
                    @if(!empty($storethemesetting['brand_logo']))
                        @foreach(explode(',',$storethemesetting['brand_logo']) as $k => $value)
                            <div class="col-lg-2 col-md-3 col-sm-4 col-6">
                                <a href="#">
                                    @if(!empty($value) && \Storage::exists('uploads/store_logo/'.$value))
                                        <img src="{{asset(Storage::url('uploads/store_logo/').(!empty($value)?$value:'storego-image.png'))}}" alt="Footer logo">
                                    @else
                                        <img src="{{asset(Storage::url('uploads/store_logo/brand_logo.png'))}}" alt="Footer logo">
                                    @endif
                                </a>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    @endif
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
                       
                        show_toastr('Error', response.error, 'error');
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
