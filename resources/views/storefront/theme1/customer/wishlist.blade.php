@extends('storefront.user.user')
@section('page-title')
    {{__('Wishlist')}} - {{($store->tagline) ?  $store->tagline : config('APP_NAME', ucfirst($store->name))}}
@endsection
@push('css-page')
@endpush
@section('head-title')
    {{__('Your Wishlist')}}
@endsection
@section('content')
    <div class="course-page hero-section tutor-page">
        <div class="container-lg">
            <div class="row">
                <div class="col-xl-6 col-lg-6">
                    <div class="course-page-text pt-100">
                        <div class="course-category">
                            <div class="course-back">
                                <a href="{{route('store.slug',$store->slug)}}">
                                    <i class="fa fa-angle-left" aria-hidden="true"></i>
                                    {{__('Back to Category')}}
                                </a>
                            </div>
                        </div>
                        <div class="category-heading">
                            <h2>{{__('Wishlist')}}</h2>
                            <p>{{__('Lorem Ipsum is simply dummy text of the printing and typesetting industry.')}} </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="course-second chapter-original-video">
        <div class="container-lg">
            <div class="row">
                <div class="categories-card">
                    @if(!empty($courses) && count($courses) > 0)
                        @foreach($courses as $key => $course)
                            @if(sizeof($course->student_wl) > 0)
                                @foreach($course->student_wl as $student_wl)
                                    <div class="col-lg-3 col-md-6 col-sm-12">
                                        <div class="card-section">
                                            <div class="card">
                                                <div class="card-main">
                                                    @php
                                                        $cart = session()->get($slug);
                                                        $key = false;
                                                    @endphp
                                                    @if(!empty($cart['products']))
                                                        @foreach($cart['products'] as $k => $value)
                                                            @if($course->id == $value['product_id'])
                                                                @php
                                                                    $key = $k
                                                                @endphp
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                    <div class="card-img">
                                                        <div class="card-img-main">
                                                            @if(!empty($course->thumbnail))
                                                                <img alt="card" src="{{asset(Storage::url('uploads/thumbnail/'.$course->thumbnail))}}" class="img-fluid">
                                                            @else
                                                                <img src="{{asset('assets/img/card-img.svg')}}" alt="card" class="img-fluid">
                                                            @endif
                                                        </div>
                                                        <div class="card-tag">
                                                            <div class="design-tag text-center">
                                                                <span>{{!empty($course->category_id)?$course->category_id->name:''}}</span>
                                                            </div>
                                                            <div class="like-tag">
                                                                @if(Auth::guard('students')->check())
                                                                    @if(sizeof($course->student_wl)>0)
                                                                        @foreach($course->student_wl as $student_wl)
                                                                            <a href="" class="like-a-tag remove_from_wishlist" data-confirm="{{__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-product-cart-{{$key}}').submit();">
                                                                                <img src="{{asset('assets/img/wishlist.svg')}}" alt="like" class="img-fluid">
                                                                            </a>
                                                                            {!! Form::open(['method' => 'POST', 'route' => ['student.removeFromWishlist',[$slug,$course->id]],'id'=>'delete-product-cart-'.$key]) !!}
                                                                            {!! Form::close() !!}
                                                                        @endforeach
                                                                    @else
                                                                        <a class="like-a-tag add_to_wishlist" data-id="{{$course->id}}">
                                                                            <img src="{{asset('assets/img/like.svg')}}" alt="like" class="img-fluid">
                                                                        </a>
                                                                    @endif
                                                                @else
                                                                    <a class="like-a-tag add_to_wishlist" data-id="{{$course->id}}">
                                                                        <img src="{{asset('assets/img/like.svg')}}" alt="like" class="img-fluid">
                                                                    </a>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="card-text">
                                                        <div class="card-heading">
                                                            <a href="{{route('store.viewcourse',[$store->slug,\Illuminate\Support\Facades\Crypt::encrypt($course->id)])}}">
                                                                <h4>{{$course->title}}</h4>
                                                            </a>
                                                        </div>
                                                        @if($store->enable_rating == 'on')
                                                            <div class="card-rate-section d-flex align-items-center">
                                                        <span class="static-rating static-rating-sm d-block">
                                                            @for($i =1;$i<=5;$i++)
                                                                @php
                                                                    $icon = 'fa-star';
                                                                    $color = '';
                                                                    $newVal1 = ($i-0.5);
                                                                    if($course->course_rating() < $i && $course->course_rating() >= $newVal1)
                                                                    {
                                                                        $icon = 'fa-star-half-alt';
                                                                    }
                                                                    if($course->course_rating() >= $newVal1)
                                                                    {
                                                                        $color = 'text-warning';
                                                                    }
                                                                @endphp
                                                                <i class="fas {{$icon .' '. $color}}"></i>
                                                            @endfor
                                                        </span>
                                                            </div>
                                                        @endif
                                                        <div class="card-detail-main">
                                                            <div class="card-detail">
                                                                <div class="card-detail-sub">
                                                                    <div class="card-icon">
                                                                        <img src="{{asset('assets/img/user.svg')}}" alt="user" class="img-fluid">
                                                                    </div>
                                                                    <h4>{{$course->student_count->count()}}<span>{{__('Students')}}</span></h4>
                                                                </div>
                                                                <div class="card-detail-sub">
                                                                    <div class="card-icon">
                                                                        <img src="{{asset('assets/img/layer.svg')}}" alt="layer" class="img-fluid">
                                                                    </div>
                                                                    <h4>{{$course->chapter_count->count()}}<span>{{__('Chapters')}}</span></h4>
                                                                </div>
                                                                <div class="card-detail-sub card-detail-beginner">
                                                                    <h4> {{$course->level}} </h4>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="card-price-section">
                                                            @if(Auth::guard('students')->check())
                                                                @if(in_array($course->id,Auth::guard('students')->user()->purchasedCourse()))
                                                                    <div class="card-price">
                                                                    </div>
                                                                    <div class="add-cart">
                                                                        <a href="{{route('store.fullscreen',[$store->slug,Crypt::encrypt($course->id),''])}}">
                                                                            {{__('Start Watching')}}
                                                                        </a>
                                                                    </div>
                                                                @else
                                                                    <div class="card-price">
                                                                        @if($course->has_discount == 'on')
                                                                            <h3> {{ ($course->is_free == 'on')? __('Free') : \App\Utility::priceFormat($course->price)}}</h3>
                                                                        @else
                                                                            <h3> {{ ($course->is_free == 'on')? __('Free') : \App\Utility::priceFormat($course->price)}}</h3>
                                                                        @endif
                                                                    </div>
                                                                @endif
                                                            @else
                                                                <div class="card-price">
                                                                    @if($course->has_discount == 'on')
                                                                        <h3>{{ ($course->is_free == 'on')? __('Free') : \App\Utility::priceFormat($course->price)}}</h3>
                                                                    @else
                                                                        <h3>{{ ($course->is_free == 'on')? __('Free') : \App\Utility::priceFormat($course->price)}}</h3>
                                                                    @endif
                                                                </div>
                                                                <div class="add-cart">
                                                                    @if($key !== false)
                                                                        <a id="cart-btn-{{$course->id}}">{{__('Already in Cart')}}</a>
                                                                    @else
                                                                        <a id="cart-btn-{{$course->id}}" class="add_to_cart" data-id="{{$course->id}}">{{__('Add in Cart')}}</a>
                                                                    @endif
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        @endforeach
                    @else
                        <tr>
                            <td colspan="7">
                                <div class="text-center">
                                    <i class="fas fa-folder-open text-gray" style="font-size: 48px;"></i>
                                    <h2>{{ __('Opps...') }}</h2>
                                    <h6> {!! __('No data Found.') !!} </h6>
                                </div>
                            </td>
                        </tr>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script-page')
    {{--CART--}}
    <script>
        $(document).on('click', '.add_to_cart', function (e) {
            e.preventDefault();
            var id = $(this).attr('data-id');
            /* console.log(id);
             return false*/
            $.ajax({
                type: "POST",
                url: '{{route('user.addToCart', ['__product_id',$store->slug])}}'.replace('__product_id', id),
                data: {
                    "_token": "{{ csrf_token() }}",
                },
                success: function (response) {
                    if (response.status == "Success") {
                        show_toastr('Success', response.success, 'success');
                        $('#cart-btn-' + id).html('Already in Cart');
                        $('.cart_item_count').html(response.item_count);
                    } else {
                        show_toastr('Error', response.error, 'error');
                    }

                },
                error: function (result) {
                }
            });
        });
    </script>
@endpush
