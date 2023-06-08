@extends('layouts.admin')
@section('page-title')
    {{ __('Coupon Detail') }}
@endsection
@section('title')
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block text-white font-weight-bold mb-0 ">{{ __('Coupon Detail') }}</h5>
    </div>
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('product-coupon.index') }}">{{ __('Product Coupons') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('Coupon Detail') }}</li>
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <h4 class="my-2">{{ $productCoupon->code }}</h4>
                    <div class="table-responsive">
                        <table class="table mb-0 dataTable">
                            <thead>
                                <tr>
                                    <th class="sorting" tabindex="0" aria-controls="selection-datatable" rowspan="1"
                                        colspan="1" aria-label=" Coupon: activate to sort column ascending"
                                        style="width: 354px;"> Coupon</th>
                                    <th class="sorting" tabindex="0" aria-controls="selection-datatable" rowspan="1"
                                        colspan="1" aria-label=" User: activate to sort column ascending"
                                        style="width: 411px;"> User</th>
                                    <th class="sorting" tabindex="0" aria-controls="selection-datatable" rowspan="1"
                                        colspan="1" aria-label=" Date: activate to sort column ascending"
                                        style="width: 642px;"> Date</th>
                                </tr>
                            </thead>    
                            <tbody>
                                @foreach ($productCoupons as $userCoupon)
                                    <tr role="row" class="odd">
                                        <td>{{ !empty($productCoupon->name) ? $productCoupon->name : '' }}
                                        </td>
                                        <td>{{ !empty($userCoupon->name) ? $userCoupon->name : '' }}
                                        </td>
                                        <td>{{ $userCoupon->created_at }}</td>
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
