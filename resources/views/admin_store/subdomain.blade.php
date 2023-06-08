@extends('layouts.admin')
@section('page-title')
    {{__('Sub Domain')}}
@endsection
@section('title')
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block text-white font-weight-bold mb-0 ">{{__('Domain')}}</h5>
    </div>
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('store-resource.index') }}">{{ __('Store') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('Sub Domain') }}</li>
@endsection
@section('action-btn')
<div class="pr-2">
    <a href="{{ route('store.customDomain') }}" class="btn btn-sm btn-primary btn-icon m-1" data-bs-toggle="tooltip" data-bs-placement="top"
        title="{{ __('Custom Domain') }}" >{{__('Custom Domain')}}</a>

    <a href="{{ route('store.grid') }}" class="btn btn-sm btn-primary btn-icon m-1" data-bs-toggle="tooltip"
        data-bs-placement="top" title="{{ __('Grid View') }}"><i class="ti ti-grid-dots"></i></a>

    <a href="{{ route('store-resource.index') }}" class="btn btn-sm btn-primary btn-icon m-1" data-bs-toggle="tooltip"
        data-bs-placement="top" title="{{ __('List View') }}"><i class="fas fa-list"></i></a>

    <a href="#"  data-size="md" data-url="{{ route('store-resource.create') }}" data-ajax-popup="true" data-title="{{__('Create New Store')}}"  class="btn btn-sm btn-primary btn-icon m-1" data-bs-toggle="tooltip"
        data-bs-placement="top" title="{{ __('Create New Store') }}"><i class="ti ti-plus"></i></a>
</div>
@endsection
@section('filter')
@endsection
@push('css-page')
@endpush
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table mb-0 dataTable">
                            <thead>
                                <tr>
                                    <th>{{ __('Sub Domain Name')}}</th>
                                    <th>{{ __('Store Name')}}</th>
                                    <th>{{ __('Email')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($stores as $store)
                                <tr>
                                    <td>
                                        {{$store->subdomain}}
                                    </td>
                                    <td>
                                        {{$store->name}}
                                    </td>
                                    <td>
                                        {{($store->email)}}
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
