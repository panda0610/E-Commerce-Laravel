@php($store_logo = asset(Storage::url('uploads/product_image/')))
@extends('layouts.admin')
@section('page-title')
    {{ __('Product Categorie') }}
@endsection
@section('title')
    <div class="d-inline-block">
        <h5 class="h5 d-inline-block text-white font-weight-bold mb-0 ">{{ __('Product Categorie') }}</h5>
    </div>
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{__('Home')}}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{__('Product Category')}}</li>
@endsection
@section('action-btn')
        <div class="pr-2">
            <a href="#" data-size="md" data-url="{{ route('product_categorie.create') }}" data-ajax-popup="true"
                data-title="{{ __('Create New Product Category') }}" class="btn btn-sm btn-primary btn-icon m-1"
                data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Create product categorie') }}"><i class="ti ti-plus"></i></a>
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
                                    <th scope="col" class="sort"></th>
                                    <th scope="col" class="sort" data-sort="name">{{ __('Categorie Name') }}
                                    </th>
                                    <th class="text-right">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($product_categorys as $product_category)
                                    <tr data-name="{{ $product_category->name }}">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if ($product_category->categorie_img)
                                                    <img alt="Image placeholder"
                                                        src="{{ $store_logo }}/{{ $product_category->categorie_img }}"
                                                        class="rounded-circle" alt="images">
                                                @else
                                                    <img alt="Image placeholder" src="{{ $store_logo }}/default.jpg"
                                                        class="rounded-circle" alt="images">
                                                @endif
                                            </div>
                                        </td>
                                        <td>{{ $product_category->name }}</td>
                                        </td>
                                        <td class="Action">
                                            <span>
                                                <div class="action-btn btn-primary ms-2">
                                                    <a href="#" data-size="md"
                                                        data-url="{{ route('product_categorie.edit', $product_category->id) }}"
                                                        data-ajax-popup="true" data-title="{{ __('Edit Categories') }}"
                                                        class="mx-3 btn btn-sm d-inline-flex align-items-center"
                                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                                        title="{{ __('Edit Product') }}"><i
                                                            class="ti ti-pencil text-white"></i></a>
                                                </div>

                                                <div class="action-btn bg-danger ms-2">
                                                    <a class="bs-pass-para align-items-center btn btn-sm d-inline-flex"
                                                        href="#" data-title="{{ __('Delete Lead') }}"
                                                        data-confirm="{{ __('Are You Sure?') }}"
                                                        data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                        data-confirm-yes="delete-form-{{ $product_category->id }}"
                                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                                        title="{{ __('Delete Product Pategorie') }}">
                                                        <i class="ti ti-trash"></i>
                                                    </a>
                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['product_categorie.destroy', $product_category->id], 'id' => 'delete-form-' . $product_category->id]) !!}
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
