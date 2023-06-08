@extends('layouts.admin')
@section('page-title')
    {{__('Coupons')}}
@endsection
@section('title')
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block text-white font-weight-bold mb-0 ">{{__('Coupons')}}</h5>
    </div>
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('Coupons') }}</li>
@endsection

@section('action-btn')
    <div class="pr-2">
        <a href="#" data-size="md" data-url="{{route('coupons.create')}}"  data-ajax-popup="true"
        data-title="{{__('Add Coupon')}}"  class="btn btn-sm btn-primary btn-icon m-1" data-bs-toggle="tooltip"
            data-bs-placement="top" title="{{ __('Add Coupon') }}"><i class="ti ti-plus"></i></i></a>
    </div>
@endsection

@push('script-page')
    <script>
        $(document).on('click', '#code-generate', function () {
            var length = 10;
            var result = '';
            var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            var charactersLength = characters.length;
            for (var i = 0; i < length; i++) {
                result += characters.charAt(Math.floor(Math.random() * charactersLength));
            }
            $('#auto-code').val(result);
        });
    </script>
@endpush
@section('content')
 <!-- [ Main Content ] start -->
 <div class="row">
    <!-- [ basic-table ] start -->
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body table-border-style">
                <div class="table-responsive">
                    <table class="table dataTable">
                        <thead>
                            <tr>
                                <th> {{__('Name')}}</th>
                                <th> {{__('Code')}}</th>
                                <th> {{__('Discount (%)')}}</th>
                                <th> {{__('Limit')}}</th>
                                <th> {{__('Used')}}</th>
                                <th> {{__('Action')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($coupons as $coupon)
                            <tr>
                                <td>{{ $coupon->name }}</td>
                                <td>{{ $coupon->code }}</td>
                                <td>{{ $coupon->discount }}</td>
                                <td>{{ $coupon->limit }}</td>
                                <td>{{ $coupon->used_coupon() }}</td>
                                <td class="Action">
                                    <span>
                                        <div class="action-btn bg-info ms-2">
                                            <a href="{{ route('coupons.show',$coupon->id) }}"
                                                class="mx-3 btn btn-sm d-inline-flex align-items-center"
                                                data-bs-toggle="tooltip" data-bs-placement="top"
                                                title="{{ __('View') }}"><i
                                                    class="ti ti-eye text-white"></i></a>
                                        </div>

                                        <div class="action-btn btn-primary ms-2">
                                            <a href="#" data-size="md"
                                                class="mx-3 btn btn-sm d-inline-flex align-items-center"
                                                data-bs-toggle="tooltip" data-ajax-popup="true"
                                                data-url="{{route('coupons.edit',[$coupon->id])}}"
                                                data-title="{{ __('Edit Coupon') }}" data-bs-placement="top"
                                                title="{{ __('Edit') }}"><i
                                                    class="ti ti-pencil text-white"></i></a>
                                        </div>

                                        <div class="action-btn bg-danger ms-2">
                                            <a class="bs-pass-para align-items-center btn btn-sm d-inline-flex"
                                                href="#" data-title="{{ __('Delete Lead') }}"
                                                data-confirm="{{ __('Are You Sure?') }}"
                                                data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                data-confirm-yes="delete-form-{{ $coupon->id }}"
                                                data-bs-toggle="tooltip" data-bs-placement="top"
                                                title="{{ __('Delete') }}">
                                                <i class="ti ti-trash"></i>
                                            </a>
                                            {!! Form::open(['method' => 'DELETE', 'route' => ['coupons.destroy', $coupon->id], 'id' => 'delete-form-' . $coupon->id]) !!}
                                            {!! Form::close() !!}
                                        </div>
                                    </span>
                                </td>


                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- [ basic-table ] end -->
</div>
<!-- [ Main Content ] end -->
@endsection
