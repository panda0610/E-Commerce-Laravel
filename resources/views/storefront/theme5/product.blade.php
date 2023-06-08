@extends('storefront.layout.theme5')
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
        <section class="bestsellers-section">
            <div class="container">
                <div class="row">
                    <div class="pr-title mb-4">
                        <div class="">
                            <h3 class="mt-4 store-title text-primary">{{__('Products')}}</h3>
                            <div class="p-tablist">
                                <ul class="nav nav-tabs" id="myTab" role="tablist">
                                    @foreach($categories as $key=>$category)
                                        <li class="nav-item">
                                            <a href="#{!!preg_replace('/[^A-Za-z0-9\-]/','_',$category)!!}" data-id="{{$key}}" class="nav-link  {{($category==$categorie_name)?'active':''}} productTab" id="electronic-tab" data-toggle="tab" role="tab" aria-controls="home" aria-selected="false">
                                                {{$category}}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="tab-content bestsellers-tabs" id="myTabContent">
                        @foreach($products as $key=>$items)
                            <div class="tab-pane fade {{($key==$categorie_name)?'active show':''}}" id="{!! preg_replace('/[^A-Za-z0-9\-]/', '_', $key)!!}" role="tabpanel" aria-labelledby="shopping-tab">
                                <div class="col-lg-12">
                                    <div class="row">
                                        @if($items->count() > 0)
                                            @foreach($items as $product)
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
@endsection
@push('script-page')
    {!! $storethemesetting['storejs'] !!}

    <script>

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
