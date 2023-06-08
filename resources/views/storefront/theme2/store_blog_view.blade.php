@extends('storefront.layout.theme2')
@section('page-title')
    {{ __('Blog') }}
@endsection
@push('css-page')
    <style>
        .shoping_count:after {
            content: attr(value);
            font-size: 14px;
            background: #273444;
            border-radius: 50%;
            padding: 1px 5px 1px 4px;
            position: relative;
            left: -5px;
            top: -10px;
        }

        article p {
            word-break: break-all;
        }
    </style>
@endpush
@php
    if(!empty(session()->get('lang')))
    {
        $currantLang = session()->get('lang');
    }else{
        $currantLang = $store->lang;
    }
    $languages=\App\Models\Utility::languages();
@endphp
@section('content')
    <section class="my-cart-section mt-5 pt-5">
        <div class="slice bg-white">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-9">
                        <!-- Article body -->
                        <article>
                            <div>
                                <h5 class="float-left">{{$blogs->title}}</h5>
                                <span class="float-right">{{\App\Models\Utility::dateFormat($blogs->created_at)}}</span>
                                <span class="clearfix"></span>
                            </div>
                            <figure class="figure mt-0 w-100 text-center">
                                <img alt="Image placeholder" src="{{asset(Storage::url('uploads/store_logo/'.$blogs->blog_cover_image))}}" class="img-fluid rounded">
                            </figure>
                            <p class="lead">{!! $blogs->detail !!}</p>
                        </article>
                    </div>
                </div>
                @if(!empty($socialblogs) && $socialblogs->enable_social_button == 'on')
                    <div id="share" class="text-center"></div>
                @endif
            </div>
        </div>
    </section>
@endsection
@push('script')
    <script>
        $(document).ready(function () {
            var blog = {{$blogs}};
            if (blog == '') {
                window.location.href = "{{route('store.slug',$store->slug)}}";
            }
        });
    </script>
@endpush

