@extends('layouts.admin')
@section('page-title')
    {{__('Plan Request')}}
@endsection
@section('title')
<h5 class="h4 d-inline-block font-weight-bold mb-0 text-white">{{__('Plan Request')}}</h5>
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('Plan Request') }}</li>
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table mb-0 dataTable">
                            <thead>
                                <tr>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Plan Name') }}</th>
                                    <th>{{ __('Max Products') }}</th>
                                    <th>{{ __('Max Stores') }}</th>
                                    <th>{{ __('Duration') }}</th>
                                    <th>{{ __('Created at') }}</th>
                                    <th class="text-right">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($plan_requests as $prequest)
                                    <tr>
                                        <td>
                                            <div class="font-style font-weight-bold">{{ $prequest->user->name }}</div>
                                        </td>
                                        <td>
                                            <div class="font-style font-weight-bold">{{ $prequest->plan->name }}</div>
                                        </td>
                                        <td>
                                            <div class="font-weight-bold">{{ $prequest->plan->max_products }}</div>
                                            <div>{{__('Products')}}</div>
                                        </td>
                                        <td>
                                            <div class="font-weight-bold">{{ $prequest->plan->max_stores }}</div>
                                            <div>{{__('Stores')}}</div>
                                        </td>
                                        <td>
                                            <div class="font-style font-weight-bold">{{ ($prequest->duration == 'monthly') ? __('One Month') : __('One Year') }}</div>
                                        </td>
                                        <td>{{ \App\Models\Utility::getDateFormated($prequest->created_at,true) }}</td>
                                        <td>
                                            <div>
                                                <a href="{{route('response.request',[$prequest->id,1])}}" class="btn btn-success btn-sm">
                                                    <i class="fas fa-check"></i>
                                                </a>
                                                <a href="{{route('response.request',[$prequest->id,0])}}" class="btn btn-danger btn-sm">
                                                    <i class="fas fa-times"></i>
                                                </a>
                                            </div>
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
