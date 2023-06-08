@extends('layouts.admin')
@section('page-title')
    {{ __('Product Tax') }}
@endsection
@section('title')
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block text-white font-weight-bold mb-0 ">{{ __('Product Tax') }}</h5>
    </div>
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('Product Tax') }}</li>
@endsection
@section('action-btn')
    <div class="pr-2">
        <a href="#" data-size="md" data-url="{{ route('product_tax.create') }}" data-ajax-popup="true"
            data-title="{{ __('Create New Product Tax') }}" class="btn btn-sm btn-primary btn-icon m-1"
            data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Create New Product Tax') }}"><i class="ti ti-plus"></i></a>
    </div>
@endsection
@section('filter')
@endsection
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body table-border-style">
                <h5></h5>
                <div class="table-responsive">
                    <table class="table mb-0 dataTable ">
                        <thead>
                            <tr>
                                <th scope="col" class="sort" data-sort="name">{{ __('Tax Name') }}</th>
                                <th scope="col" class="sort" data-sort="name">{{ __('Rate %') }}</th>
                                <th class="text-right">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($product_taxs as $product_tax)
                                <tr data-name="{{ $product_tax->name }}">
                                    <td >{{ $product_tax->name }}</td>
                                    <td >{{ $product_tax->rate }}</td>
                                    <td class="Action">
                                        <span>
                                            <div class="action-btn btn-primary ms-2">
                                                <a href="#" data-size="md"
                                                    data-url="{{ route('product_tax.edit', $product_tax->id) }}"
                                                    data-ajax-popup="true" data-title="{{ __('Edit Tax') }}"
                                                    class="mx-3 btn btn-sm d-inline-flex align-items-center"
                                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                                    title="{{ __('Edit Tax') }}"><i
                                                        class="ti ti-pencil text-white"></i></a>
                                            </div>

                                            <div class="action-btn bg-danger ms-2">
                                                <a class="bs-pass-para align-items-center btn btn-sm d-inline-flex"
                                                    href="#" data-title="{{ __('Delete Lead') }}"
                                                    data-confirm="{{ __('Are You Sure?') }}"
                                                    data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                    data-confirm-yes="delete-form-{{ $product_tax->id }}"
                                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                                    title="{{ __('Delete Tax') }}">
                                                    <i class="ti ti-trash"></i>
                                                </a>
                                                {!! Form::open(['method' => 'DELETE', 'route' => ['product_categorie.destroy', $product_tax->id], 'id' => 'delete-form-' . $product_tax->id]) !!}
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
</div>
@endsection
@push('script-page')
    <script>
        $(document).ready(function() {
            $(document).on('keyup', '.search-user', function() {
                var value = $(this).val();
                $('.employee_tableese tbody>tr').each(function(index) {
                    var name = $(this).attr('data-name').toLowerCase();
                    if (name.includes(value.toLowerCase())) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            });
        });
    </script>
@endpush
