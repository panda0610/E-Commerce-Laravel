@extends('storefront.layout.theme3')
@section('page-title')
    {{__('Home')}}
@endsection
@push('css-page')
    <style>
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

        @if(($store->store_theme == 'white-black-color.css'))
        .p-tablist .nav-tabs .nav-item .nav-link {
            color: white !important;
        }

        .store-title {
            color: white;
        }
        @endif
    </style>
@endpush
@section('content')
    <!-- Products -->
    @if($products['Start shopping']->count() > 0)
        <div class="container mt-10 mb-5">
            <div class="row">
                <div class="pr-title mb-4 bg-primary">
                    <h3 class="mt-4 store-title">{{__('Products')}}</h3>
                    <div class="p-tablist">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            @foreach($categories as $key=>$category)
                                <li class="nav-item">
                                    <a href="#{!!preg_replace('/[^A-Za-z0-9\-]/','_',$category)!!}" data-id="{{$key}}" class="nav-link bor-radius bg-primary text-dark {{($category==$categorie_name)?'active':''}} productTab" id="electronic-tab" data-toggle="tab" role="tab" aria-controls="home" aria-selected="false">
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
                                                    <div class="card-image">
                                                        <a href="{{route('store.product.product_view',[$store->slug,$product->id])}}">
                                                            @if(!empty($product->is_cover) && \Storage::exists('uploads/is_cover_image/'.$product->is_cover))
                                                                <img alt="Image placeholder" src="{{asset(Storage::url('uploads/is_cover_image/'.$product->is_cover))}}" class="img-center img-fluid" style="width:auto; height:221px;">
                                                            @else
                                                                <img alt="Image placeholder" src="{{asset(Storage::url('uploads/is_cover_image/default.jpg'))}}" class="img-center img-fluid" style="width:auto; height:221px;">
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
                                                    <div class="card-body ">
                                                        @if($product['enable_product_variant'] == 'on')
                                                            <div class="">
                                                                <a href="{{route('store.product.product_view',[$store->slug,$product->id])}}">
                                                                    <button type="button" class="btn btn-sm btn-primary rounded-pill btn-icon hover-translate-y-n3">
                                                                        {{__('In variant')}}
                                                                        <i class="fas fa-shopping-basket"></i>
                                                                    </button>
                                                                </a>
                                                                @if(!empty($wishlist) && isset($wishlist[$product->id]['product_id']))
                                                                    @if($wishlist[$product->id]['product_id'] != $product->id)
                                                                        <button type="button" class="ml-2 btn btn-sm btn-primary  btn-icon hover-translate-y-n3 add_to_wishlist wishlist_{{$product->id}}" data-id="{{$product->id}}">
                                                                            <i class="far fa-heart"></i>
                                                                        </button>
                                                                    @else
                                                                        <button type="button" class="btn btn-sm btn-primary  btn-icon hover-translate-y-n3 bg-light-gray" data-id="{{$product->id}}" disabled>
                                                                            <i class="fas fa-heart"></i>
                                                                        </button>
                                                                    @endif
                                                                @else
                                                                    <button type="button" class="btn btn-sm btn-primary  btn-icon hover-translate-y-n3 add_to_wishlist wishlist_{{$product->id}}" data-id="{{$product->id}}">
                                                                        <i class="far fa-heart"></i>
                                                                    </button>
                                                                @endif
                                                            </div>
                                                        @else
                                                            <div class="product-price m-0">
                                                                <span class="card-price t-black15">{{\App\Models\Utility::priceFormat($product['price'])}}</span>
                                                                <button type="button" class="btn btn-sm btn-primary rounded-pill btn-icon hover-translate-y-n3 add_to_cart" data-id="{{$product->id}}">
                                                                    <i class="fas fa-shopping-basket"></i>
                                                                </button>
                                                                @if(!empty($wishlist) && isset($wishlist[$product->id]['product_id']))
                                                                    @if($wishlist[$product->id]['product_id'] != $product->id)
                                                                        <button type="button" class="btn btn-sm btn-primary  btn-icon hover-translate-y-n3 add_to_wishlist wishlist_{{$product->id}}" data-id="{{$product->id}}">
                                                                            <i class="far fa-heart"></i>
                                                                        </button>
                                                                    @else
                                                                        <button type="button" class="btn btn-sm btn-primary  btn-icon hover-translate-y-n3 bg-light-gray" data-id="{{$product->id}}" disabled>
                                                                            <i class="fas fa-heart"></i>
                                                                        </button>
                                                                    @endif
                                                                @else
                                                                    <button type="button" class="btn btn-sm btn-primary  btn-icon hover-translate-y-n3 add_to_wishlist wishlist_{{$product->id}}" data-id="{{$product->id}}">
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
    @else
        <div class="container mt-10 mb-5">
            {{__('No data found')}}
        </div>
    @endif
@endsection
