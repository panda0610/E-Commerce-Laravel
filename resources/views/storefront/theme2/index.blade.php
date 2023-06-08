@extends('storefront.layout.theme2')
@section('page-title')
    {{__('Home')}}
@endsection
@push('css-page')

@endpush
@section('content')
    <!-- Header_img -->
    @if($storethemesetting['enable_header_img'] == 'on')
        <div class="bd-example home-banner-slider">
            <div id="carouselExampleCaptions" class="carousel slide">
                <div class="carousel-inner" role="listbox">
                    <div class="bg-cover bg-size--cover home-banner carousel-item active" data-offset-top="#header-main" style="background-image: url({{asset(Storage::url('uploads/store_logo/'.(!empty($storethemesetting['header_img'])?$storethemesetting['header_img']:'home-banner1.png')))}}); background-position: center center; padding-top: 77px;">
                        <div class="carousel-caption  d-md-block">
                            <div class="container py-6 box-height">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <h2 class="h1 text-white store-title w-25">{{!empty($storethemesetting['header_title'])?$storethemesetting['header_title']:'Home Accessories'}}</h2>
                                        <p class="lead text-white mt-4 w-50">{{!empty($storethemesetting['header_desc'])?$storethemesetting['header_desc']:'There is only that moment and the incredible certainty that everything under the sun has been written by one hand only.'}}</p>
                                        <a href="#" class="btn btn-sm btn-primary btn-icon shadow hover-shadow-lg hover-translate-y-n3" id="pro_scroll">
                                            <span class="btn-inner--text t-secondary">{{__(!empty($storethemesetting['button_text'])?$storethemesetting['button_text']:__('Show more products'))}}</span>
                                            <span class="btn-inner--icon">
                                                <i class="fas fa-shopping-basket"></i>
                                            </span>
                                        </a>
                                    </div>
                                </div>
                                <div class="row banner-social">
                                    <ul>
                                        @if(!empty($storethemesetting['whatsapp']))
                                            <li class="nav-item">
                                                <a class="nav-link" href="https://wa.me/{{$storethemesetting['whatsapp']}}" target="_blank">
                                                    <i class="fab fa-whatsapp"></i>
                                                </a>
                                            </li>
                                        @endif
                                        @if(!empty($storethemesetting['facebook']))
                                            <li class="nav-item">
                                                <a class="nav-link" href="{{$storethemesetting['facebook']}}" target="_blank">
                                                    <i class="fab fa-facebook-square"></i>
                                                </a>
                                            </li>
                                        @endif
                                        @if(!empty($storethemesetting['twitter']))
                                            <li class="nav-item">
                                                <a class="nav-link" href="{{$storethemesetting['twitter']}}" target="_blank">
                                                    <i class="fab fa-twitter-square"></i>
                                                </a>
                                            </li>
                                        @endif
                                        @if(!empty($storethemesetting['email']))
                                            <li class="nav-item">
                                                <a class="nav-link" href="mailto:{{$storethemesetting['email']}}">
                                                    <i class="far fa-envelope"></i>
                                                </a>
                                            </li>
                                        @endif
                                        @if(!empty($storethemesetting['instagram']))
                                            <li class="nav-item">
                                                <a class="nav-link" href="{{$storethemesetting['instagram']}}" target="_blank">
                                                    <i class="fab fa-instagram"></i>
                                                </a>
                                            </li>
                                        @endif
                                        @if(!empty($storethemesetting['youtube']))
                                            <li class="nav-item">
                                                <a class="nav-link" href="{{$storethemesetting['youtube']}}" target="_blank">
                                                    <i class="fab fa-youtube"></i>
                                                </a>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Products -->
    @if($products['Start shopping']->count() > 0)
        <section class="bestsellers-section {{($storethemesetting['enable_header_img'] == 'off')?'mt-10':''}}" id="pro_items">
            <div class="container">
                <div class="row">
                    <div class="pr-title mb-4">
                        <div class="p-tablist">
                            <ul class="nav nav-tabs" id="myTab" role="tablist">
                                @foreach($categories as $key=>$category)
                                    <li class="nav-item">
                                        <a href="#{!! preg_replace('/[^A-Za-z0-9\-]/','_',$category)!!}" data-id="{{$key}}" class="nav-link {{($key==0)?'active':''}} productTab" id="electronic-tab" data-toggle="tab" role="tab" aria-controls="home" aria-selected="false">
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
                                                    <div class="card card-product">
                                                        <div class="card-image">
                                                            <a href="{{route('store.product.product_view',[$store->slug,$product->id])}}">
                                                                @if(!empty($product->is_cover) && \Storage::exists('uploads/is_cover_image/'.$product->is_cover))
                                                                    <img alt="Image placeholder" src="{{asset(Storage::url('uploads/is_cover_image/'.$product->is_cover))}}" class="img-center img-fluid" style="height:275px; width:255px;">
                                                                @else
                                                                    <img alt="Image placeholder" src="{{asset(Storage::url('uploads/is_cover_image/default.jpg'))}}" class="img-center img-fluid" style="height:275px; width:255px;">
                                                                @endif
                                                            </a>
                                                        </div>
                                                        <div class="card-body mt-3">
                                                            <h6><a href="{{route('store.product.product_view',[$store->slug,$product->id])}}" class="t-black13">{{$product->name}}</a></h6>
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
                                                                        <span class="btn-inner--icon">
                                                                            <i class="fas fa-shopping-basket"></i>
                                                                        </span>
                                                                    </a>
                                                                @else
                                                                    <a href="javascript:void(0)" class="btn btn-sm btn-primary rounded-pill btn-icon shadow hover-shadow-lg hover-translate-y-n3 add_to_cart" data-id="{{$product->id}}">
                                                                        <span class="btn-inner--text">{{__('Add to cart')}}</span>
                                                                        <span class="btn-inner--icon">
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
                                            @endforeach
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

    <!-- Products categories-->
    @if(isset($storethemesetting['enable_categories']) && $storethemesetting['enable_categories'] == 'on' && !empty($pro_categories))
        @if($storethemesetting['enable_categories'] == 'on')
            <section class="top-product">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 cat-main-boxes">
                            <div class="categories-content">
                                <h2 class=" mt-4 store-title t-secondary">{{!empty($storethemesetting['categories'])?$storethemesetting['categories']:'Categories'}}</h2>
                                <p class="t-l-gray mt-3 mb-5 w-75 w-custom">{{!empty($storethemesetting['categories_title'])?$storethemesetting['categories_title']:'There is only that moment and the incredible certainty that everything under the sun has been written by one hand only.'}}</p>
                            </div>
                            <div class="cat-button">
                                <a href="{{route('store.categorie.product',[$store->slug])}}" class="btn btn-sm btn-blue bg-gray btn-icon shadow hover-shadow-lg hover-translate-y-n3">
                                    <span class="btn-inner--text  t-white">{{__('Show more products')}}</span>
                                    <span class="btn-inner--icon">
                                    <i class="fas fa-shopping-basket"></i>
                                </span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        @foreach($pro_categories as $key=>$pro_categorie)
                            @if($product_count[$key] > 0)
                                <div class="col-xl-4 col-lg-4 col-sm-6 product-box product-cat">
                                    <div class="card card-product">
                                        <div class="card-image">
                                            <a href="{{route('store.categorie.product',[$store->slug,$pro_categorie->name])}}">
                                                @if(!empty($pro_categorie->categorie_img) && \Storage::exists('uploads/product_image/'.$pro_categorie->categorie_img))
                                                    <img alt="Image placeholder" src="{{asset(Storage::url('uploads/product_image/').$pro_categorie->categorie_img)}}" class="img-center img-fluid" style="height:335px; width:350px;">
                                                @else
                                                    <img alt="Image placeholder" src="{{asset(Storage::url('uploads/product_image/default.jpg'))}}" class="img-center img-fluid" style="height:335px; width:350px;">
                                                @endif
                                            </a>
                                        </div>
                                        <div class="card-body pt-0">
                                            <div class="product-price mt-3">
                                                <div class="p-title">
                                                    <h6><span class="card-price t-white">{{$pro_categorie->name}}</span></h6>
                                                    <p class="mb-0 text-white">{{__('Products')}}: {{!empty($product_count[$key])?$product_count[$key]:'0'}}</p>
                                                    <a href="{{route('store.categorie.product',[$store->slug,$pro_categorie->name])}}" class="btn btn-sm btn-primary btn-icon shadow hover-shadow-lg hover-translate-y-n3">
                                                        <span class="btn-inner--text t-white">{{__('Show more products')}}</span>
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
    @endif

    <!-- Top Rated Products -->
    @if(count($topRatedProducts)>0)
        <section class="top-product">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <h3 class=" mt-4 store-title t-secondary">{{__('Collections')}}</h3>
                        <p class="t-l-gray">{{__('There is only that moment and the incredible certainty that')}} <br> {{__('everything under the sun has been written by one hand only')}}.</p>
                    </div>
                </div>
                <div class="row">
                    @foreach($topRatedProducts as $k => $topRatedProduct)
                        <div class="col-xl-3 col-lg-4 col-sm-6 product-box">
                            <div class="card card-product">
                                <div class="card-image">
                                    <a href="{{route('store.product.product_view',[$store->slug,$topRatedProduct->product_id])}}">
                                        @if(!empty($topRatedProduct->product->is_cover) && \Storage::exists('uploads/is_cover_image/'.$topRatedProduct->product->is_cover))
                                            <img alt="Image placeholder" src="{{asset(Storage::url('uploads/is_cover_image/'.(!empty($topRatedProduct->product->is_cover)?$topRatedProduct->product->is_cover:'')))}}" class="img-center img-fluid" style="height:275px; width:255px;">
                                        @else
                                            <img alt="Image placeholder" src="{{asset(Storage::url('uploads/is_cover_image/default.jpg'))}}" class="img-center img-fluid" style="height:275px; width:255px;">
                                        @endif
                                    </a>
                                </div>
                                <div class="card-body pt-0">
                                    <div class="product-price mt-3">
                                        <div class="p-title">
                                            <h6><span class="card-price t-black15">{{$topRatedProduct->product->product_category()}}</span></h6>
                                        </div>
                                        <a href="{{route('store.product.product_view',[$store->slug,$topRatedProduct->product_id])}}" type="button" class="action-item pcart-icon" data-toggle="tooltip" data-original-title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="actions card-product-actions">
                                    <button type="button" class="action-item p-new">
                                        {{__('New')}}
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- subscriber-->
    @if(isset($storethemesetting['enable_email_subscriber']) && $storethemesetting['enable_email_subscriber']=='on')
        @if($storethemesetting['enable_email_subscriber'] == 'on')
            <section class="your-time-section">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-12 left-img">
                            <h3 class="medium-store-title t-secondary mb-3">{{!empty($storethemesetting['subscriber_title'])?$storethemesetting['subscriber_title']:'Always on time'}}</h3>
                            <p class="mb-4">{{!empty($storethemesetting['subscriber_sub_title'])?$storethemesetting['subscriber_sub_title']:'Subscription here'}}</p>
                            {{Form::open(array('route' => array('subscriptions.store_email', $store->id),'method' => 'POST'))}}
                            <div class="form-group mb-0 form-subscribe">
                                <div class="input-group input-group-lg input-group-merge">
                                    {{Form::email('email',null,array('class'=>'form-control bg-white form-control-flush rounded-pill','aria-label'=>__('Enter your email address'),'placeholder'=>__('Enter Your Email Address')))}}
                                    <div class="input-group-append">
                                        <button class="btn btn-primary rounded-pill  btn-icon mr-sm-4 scroll-me" type="submit">
                                            <span class="btn-inner--text">{{__('Subscribe')}}</span>
                                            <i class="fas fa-paper-plane"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            {{Form::close()}}
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <div class="right-img" style="background: url({{asset(Storage::url('uploads/store_logo/'.(!empty($storethemesetting['subscriber_img'])?$storethemesetting['subscriber_img']:'email_subscriber_2.png')))}});">
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        @endif
    @endif

    <!-- Testimonials (v1) -->
    @if(isset($storethemesetting['enable_testimonial']) && $storethemesetting['enable_testimonial']=='on')
        @if($storethemesetting['enable_testimonial'])
            <section class="slice testimonial-section ">
                <div class="container">
                    <div class="mb-5 text-center">
                        <h3 class=" mt-4 store-title t-secondary">{{!empty($storethemesetting['testimonial_main_heading'])?$storethemesetting['testimonial_main_heading']:'Testimonials'}}</h3>
                        <div class="fluid-paragraph mt-3">
                            <p class="lead lh-180 store-dcs t-l-gray">{{!empty($storethemesetting['testimonial_main_heading_title'])?$storethemesetting['testimonial_main_heading_title']:'There is only that moment and the incredible certainty that everything
                                                   under the sun has been written by one hand only.'}}</p>
                        </div>
                    </div>
                    <div class="row testimonial-slider">
                        <div class="col-lg-12">
                            <div class="swiper-js-container overflow-hidden">
                                <div class="swiper-container" data-swiper-items="1" data-swiper-space-between="0" data-swiper-sm-items="1" data-swiper-xl-items="1">
                                    <div class="swiper-wrapper">
                                        @if(isset($storethemesetting['enable_testimonial1']) && $storethemesetting['enable_testimonial1']=='on')
                                            <div class="swiper-slide p-3">
                                                <div class="card bg-transparent">
                                                    <div class="card-body">
                                                        <p class="t-dcs t-gray">{{$storethemesetting['testimonial_description1']}}</p>
                                                        <div class="d-flex align-items-center collection-qoute">
                                                            <img alt="Image placeholder" src="{{asset(Storage::url('uploads/store_logo/'.(!empty($storethemesetting['testimonial_img1'])?$storethemesetting['testimonial_img1']:'')))}}" class="avatar  rounded-circle">
                                                            <h5 class="t-author t-black14">{{$storethemesetting['testimonial_name1']}}</h5>
                                                            <small class="d-block t-author-dcs">{{$storethemesetting['testimonial_about_us1']}}</small>
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
                                                        <div class="d-flex align-items-center collection-qoute">
                                                            <img alt="Image placeholder" src="{{asset(Storage::url('uploads/store_logo/'.(!empty($storethemesetting['testimonial_img2'])?$storethemesetting['testimonial_img2']:'')))}}" class="avatar  rounded-circle">
                                                            <h5 class="t-author t-black14">{{$storethemesetting['testimonial_name2']}}</h5>
                                                            <small class="d-block t-author-dcs">{{$storethemesetting['testimonial_about_us2']}}</small>
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
                                                        <div class="d-flex align-items-center collection-qoute">
                                                            <img alt="Image placeholder" src="{{asset(Storage::url('uploads/store_logo/'.(!empty($storethemesetting['testimonial_img3'])?$storethemesetting['testimonial_img3']:'')))}}" class="avatar  rounded-circle">
                                                            <h5 class="t-author t-black14">{{$storethemesetting['testimonial_name3']}}</h5>
                                                            <small class="d-block t-author-dcs">{{$storethemesetting['testimonial_about_us3']}}</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <!-- Add Pagination -->
                                <div class="swiper-pagination w-100 mt-4 d-flex align-items-center justify-content-center">
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
    @endif

    <!-- Features -->
    @if(isset($storethemesetting['enable_features']) && $storethemesetting['enable_features'] == 'on')
        <section class="store-promotions mt-70">
            <div class="container">
                <div class="row">
                    @if($storethemesetting['enable_features1'] == 'on')
                        <div class="col-lg-3 col-sm-6">
                            <div class="mb-4">
                                <div class="icon text-primary">
                                    {!! $storethemesetting['features_icon1'] !!}
                                    <strong class="t-secondary">{{$storethemesetting['features_title1']}}</strong>
                                </div>
                                <p class=" mt-2 mb-0 t-gray">{{$storethemesetting['features_description1']}}</p>
                            </div>
                        </div>
                    @endif
                    @if($storethemesetting['enable_features2'] == 'on')
                        <div class="col-lg-3 col-sm-6">
                            <div class="mb-4">
                                <div class="icon text-primary">
                                    {!! $storethemesetting['features_icon2'] !!}
                                    <strong class="t-secondary">{{$storethemesetting['features_title2']}}</strong>
                                </div>
                                <p class=" mt-2 mb-0 t-gray">{{$storethemesetting['features_description2']}}</p>
                            </div>
                        </div>
                    @endif
                    @if($storethemesetting['enable_features3'] == 'on')
                        <div class="col-lg-3 col-sm-6">
                            <div class="mb-4">
                                <div class="icon text-primary">
                                    {!! $storethemesetting['features_icon3'] !!}
                                    <strong class="t-secondary">{{$storethemesetting['features_title3']}}</strong>
                                </div>
                                <p class=" mt-2 mb-0 t-gray">{{$storethemesetting['features_description3']}}</p>
                            </div>
                        </div>
                    @endif
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
                                        <img src="{{asset(Storage::url('uploads/store_logo/').$value)}}" alt="Footer logo">
                                    @else
                                        <img src="{{asset(Storage::url('uploads/store_logo/logo.png'))}}" alt="Footer logo">
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
