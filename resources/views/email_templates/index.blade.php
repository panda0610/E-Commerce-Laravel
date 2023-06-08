@extends('layouts.admin')
@section('page-title')
    {{__('Email Templates')}}
@endsection
@section('title')
    <div class="d-inline-block">
        <h5 class="h5 d-inline-block text-white font-weight-bold mb-0 ">{{__('Email Templates')}}</h5>
    </div>
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('Email Templates') }}</li>
@endsection
@push('script-page')
    <script type="text/javascript">
        @can('On-Off Email Template')
        $(document).on("click", ".email-template-checkbox", function () {
            var chbox = $(this);
            $.ajax({
                url: chbox.attr('data-url'),
                data: {_token: $('meta[name="csrf-token"]').attr('content'), status: chbox.val()},
                type: 'PUT',
                success: function (response) {
                    if (response.is_success) {
                        show_toastr('Success', response.success, 'success');
                        if (chbox.val() == 1) {
                            $('#' + chbox.attr('id')).val(0);
                        } else {
                            $('#' + chbox.attr('id')).val(1);
                        }
                    } else {
                        show_toastr('Error', response.error, 'error');
                    }
                },
                error: function (response) {
                    response = response.responseJSON;
                    if (response.is_success) {
                        show_toastr('Error', response.error, 'error');
                    } else {
                        show_toastr('Error', response, 'error');
                    }
                }
            })
        });
        @endcan
    </script>
@endpush
@section('action-btn')
    {{--    <a href="#" class="btn btn-sm btn-white btn-icon-only rounded-circle" data-ajax-popup="true" data-title="{{__('Create New Email Template')}}" data-url="{{route('email_template.create')}}"><i class="fas fa-plus"></i> {{__('Add')}} </a>--}}
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
                                <th width="90%"> {{__('Name')}}</th>
                                @if(\Auth::user()->type == 'super admin')
                                    <th> {{__('Action')}}</th>
                                @elseif(\Auth::user()->type == 'Owner')
                                    <th> {{__('On/Off')}}</th>
                                @endcan
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($EmailTemplates as $EmailTemplate)
                                <tr>
                                    <td>{{ $EmailTemplate->name }}</td>
                                    <td class="Action">
                                        <span>
                                            <div class="action-btn bg-info ms-2">
                                                <a href="{{ route('manage.email.language',[$EmailTemplate->id,\Auth::user()->lang]) }}"
                                                    class="mx-3 btn btn-sm d-inline-flex align-items-center"
                                                    data-toggle="tooltip" data-original-title="{{ __('View') }}"
                                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                                    title="{{ __('View') }}"><i
                                                        class="ti ti-eye text-white"></i></a>
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
</div>
@endsection
