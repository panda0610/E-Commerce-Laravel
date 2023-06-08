@extends('layouts.admin')
@section('page-title')
    {{__('Store')}}
@endsection
@section('title')
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0 text-white">{{__('Store')}}</h5>
    </div>
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('Store') }}</li>
@endsection
@section('action-btn')
<div class="pr-2">
    <a href="{{ route('store.subDomain') }}" class="btn btn-sm btn-primary btn-icon m-1" data-bs-toggle="tooltip" data-bs-placement="top"
        title="{{ __('Sub Domain') }}" >{{__('Sub Domain')}}</a>

    <a href="{{ route('store.customDomain') }}" class="btn btn-sm btn-primary btn-icon m-1" data-bs-toggle="tooltip" data-bs-placement="top"
        title="{{ __('Custom Domain') }}" >{{__('Custom Domain')}}</a>

    <a href="{{ route('store-resource.index') }}" class="btn btn-sm btn-primary btn-icon m-1" data-bs-toggle="tooltip"
        data-bs-placement="top" title="{{ __('List View') }}"><i class="fas fa-list"></i></a>

    <a href="#"  data-size="md" data-url="{{ route('store-resource.create') }}" data-ajax-popup="true" data-title="{{__('Create New Store')}}"  class="btn btn-sm btn-primary btn-icon m-1" data-bs-toggle="tooltip"
        data-bs-placement="top" title="{{ __('Create New Store') }}"><i class="ti ti-plus"></i></a>
</div>
@endsection

@section('action-btn')
    <a href="{{ route('store.subDomain') }}" class="btn btn-sm btn-white bor-radius">
        {{__('Sub Domain')}}
    </a>
    <a href="{{ route('store.customDomain') }}" class="btn btn-sm btn-white bor-radius">
        {{__('Custom Domain')}}
    </a>
    <a href="{{ route('store-resource.index') }}" class="btn btn-sm btn-white bor-radius">
        {{__('List View')}}
    </a>
    <a href="#" data-size="lg" data-url="{{ route('store-resource.create') }}" data-ajax-popup="true" data-title="{{__('Create New User')}}" class="btn btn-sm btn-white btn-icon-only rounded-circle">
        <i class="ti ti-plus"></i></i>
    </a>
@endsection
@section('filter')
@endsection
@section('content')

    @if(\Auth::user()->type = 'super admin')
        <div class="row">
            @foreach($users as $user)
            <div class="col-md-4 col-xxl-3">
                <div class="card">
                    <div class="card-header border-0 pb-0">
                        <div class="card-header-right">
                            <div class="btn-group card-option">
                                <button type="button" class="btn dropdown-toggle" data-bs-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                    <i class="ti ti-dots-vertical"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end" style="">
                                    <a href="#" data-size="md" data-url="{{ route('user.edit',$user->id) }}" data-ajax-popup="true"  class="dropdown-item"><i
                                            class="ti ti-edit"></i>
                                        <span>{{ __('Edit') }}</span></a>

                                    <a href="#" data-size="md" data-url="{{ route('plan.upgrade',$user->id) }}" data-ajax-popup="true"  class="dropdown-item"><i class="ti ti-trophy"></i>
                                        <span>{{ __('Upgrade Plan') }}</span></a>

                                    <a class="bs-pass-para dropdown-item trigger--fire-modal-1" href="#"
                                        data-title="{{ __('Delete') }}" data-confirm="{{ __('Are You Sure?') }}"
                                        data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                        data-confirm-yes="delete-form-{{ $user->id }}">
                                        <i class="ti ti-trash"></i><span>{{ __('Delete') }} </span>
                                    </a>
                                    {!! Form::open(['method' => 'DELETE', 'route' => ['user.destroy', $user->id], 'id' => 'delete-form-' . $user->id]) !!}
                                    {!! Form::close() !!}

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body text-center">
                        <div class="avatar-parent-child">
                            <img alt="" src="{{ asset(Storage::url("uploads/profile/")).'/'}}{{ !empty($user->avatar)?$user->avatar:'avatar.png' }}" class="img-fluid rounded-circle card-avatar">
                        </div>

                        <h5 class="h6 mt-4 mb-0"> {{$user->name}}</h5>
                        <a href="#" class="d-block text-sm text-muted my-4"> {{$user->email}}</a>
                        <div class="card mb-0 mt-3">
                            <div class="card-body p-3">
                                <div class="row">
                                    <div class="col-6">
                                        <h6 class="mb-0">{{$user->countProducts($user->id)}}</h6>
                                        <p class="text-muted text-sm mb-0">{{ __('Products')}}</p>
                                    </div>
                                    <div class="col-6 text-end">
                                        <h6 class="mb-0">{{$user->countStores($user->id)}}</h6>
                                        <p class="text-muted text-sm mb-0">{{ __('Stores')}}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                            <div class="actions d-flex justify-content-between">
                                <span class="d-block text-sm text-muted"> {{__('Plan') }} : {{$user->currentPlan->name}}</span>
                            </div>
                            <div class="actions d-flex justify-content-between mt-1">
                                <span class="d-block text-sm text-muted">{{__('Plan Expired') }} : {{!empty($user->plan_expire_date) ? \Auth::user()->dateFormat($user->plan_expire_date):'Unlimited'}}</span>
                            </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif
@endsection
