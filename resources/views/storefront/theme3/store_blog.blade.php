@extends('storefront.layout.theme3')
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
    <hr>
    <div class="container mt-10">
        <div class="row">
            <div class="card-group">
                @foreach($blogs as $blog)
                    <div class="{{($blogs->count() == 1)?'col-6':'col-lg-4'}}">
                        <div class="card border-0 text-white hover-scale-110 hover-shadow-lg overflow-hidden">
                            <figure class="figure">
                                @if(!empty($blog->blog_cover_image) && \Storage::exists('uploads/store_logo/'.$blog->blog_cover_image))
                                    <img alt="Image placeholder" class="img-fluid" src="{{asset(Storage::url('uploads/store_logo/'.$blog->blog_cover_image))}}">
                                @else
                                    <img src="{{asset(Storage::url('uploads/store_logo/default.jpg'))}}" class="img-center img-fluid">
                                @endif
                            </figure>
                            <span class="mask hover-mask bg-dark opacity-7"></span>
                            <div class="card-img-overlay d-flex flex-column justify-content-end">
                                <div class="text-center">
                                    <div class="animate-item--visible opacity-10">
                                        <a href="{{route('store.store_blog_view',[$store->slug,$blog->id])}}" class="h3 text-white mb-1 stretched-link">{{$blog->title}}</a>
                                        <p class="card-text text-white">{{\App\Models\Utility::dateFormat($blog->created_at)}}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script>
        $(document).ready(function () {
            var blog = {{sizeof($blogs)}};
            if (blog < 1) {
                window.location.href = "{{route('store.slug',$store->slug)}}";
            }
        });
    </script>
@endpush
