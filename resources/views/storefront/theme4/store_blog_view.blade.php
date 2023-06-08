@extends('storefront.layout.theme4')
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
    <div class="slice bg-white">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-9">
                    <!-- Article body -->
                    <article>
                        <div class="mt-5">
                            <h5 class="float-left">{{$blogs->title}}</h5>
                            <span class="float-right">{{\App\Models\Utility::dateFormat($blogs->created_at)}}</span>
                            <span class="clearfix"></span>
                        </div>
                        <figure class="figure mt-0 w-100 text-center">
                            @if(!empty($blogs->blog_cover_image) && \Storage::exists('uploads/store_logo/'.$blogs->blog_cover_image))
                                <img alt="Image placeholder" src="{{asset(Storage::url('uploads/store_logo/'.$blogs->blog_cover_image))}}" class="img-fluid rounded">
                            @else
                                <img alt="Image placeholder" src="{{asset(Storage::url('uploads/store_logo/default.jpg'))}}" class="img-fluid rounded">
                            @endif
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

