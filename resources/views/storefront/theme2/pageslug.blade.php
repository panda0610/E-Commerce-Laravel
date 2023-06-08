@extends('storefront.layout.theme2')
@section('page-title')
    {{ ucfirst($pageoption->name) }}
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

        .pagedetails {
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
        <div class="main-content position-relative bg-white">
        <div class="container">
            <div class="page-title">
                <div class="row justify-content-between align-items-center">
                    <div class="col-auto mb-md-0">
                        <div class="d-inline-block">
                            <h5 class="h4 d-inline-block font-weight-bold mb-0 pt-4"> {{ ucfirst($pageoption->name) }}</h5>
                        </div>
                    </div>
                    <div class="col">
                    </div>
                    <div class="col-auto">
                    </div>
                </div>
            </div>
            <div class="page-content">
                <div class="mt-4 pagedetails">
                    {!! $pageoption->contents !!}
                </div>
            </div>
        </div>
    </div>
    </section>
@endsection


