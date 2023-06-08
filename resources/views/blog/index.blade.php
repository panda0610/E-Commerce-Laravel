@php($store_logo = asset(Storage::url('uploads/store_logo/')))
@extends('layouts.admin')
@section('page-title')
    {{ __('Blog') }}
@endsection
@section('title')
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block text-white font-weight-bold mb-0 ">{{ __('Blog') }}</h5>
    </div>
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('Blog') }}</li>
@endsection
@push('css-page')
    <link rel="stylesheet" href="{{ asset('custom/libs/summernote/summernote-bs4.css') }}">
@endpush
@push('script-page')
    <script src="{{ asset('custom/libs/summernote/summernote-bs4.js') }}"></script>
@endpush
@section('action-btn')
    <div class="pr-2">
        <a href="#" data-size="lg" data-url="{{ route('blog.create') }}" data-ajax-popup="true"
            data-title="{{ __('Create New Product Category') }}" class="btn btn-sm btn-primary btn-icon m-1"
            data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Create New Blog') }}"><i
                class="ti ti-plus"></i></a>
    </div>
@endsection
@section('filter')
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
                                    <th>{{ __('Blog Cover Image') }}</th>
                                    <th>{{ __('Title') }}</th>
                                    <th>{{ __('Created At') }}</th>
                                    <th class="text-right">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($blogs as $blog)
                                    <tr data-name="{{ $blog->title }}">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if (!empty($blog->blog_cover_image))
                                                    <img alt="Image placeholder"
                                                        src="{{ $store_logo }}/{{ $blog->blog_cover_image }}"
                                                       class="rounded-circle" alt="images">
                                                @else
                                                    <img alt="Image placeholder" src="{{ $store_logo . '/avatar.png' }}"
                                                       class="rounded-circle" alt="images">
                                                @endif
                                            </div>
                                        </td>
                                        <td>{{ $blog->title }}</td>
                                        <td>
                                            {{ \App\Models\Utility::dateFormat($blog->created_at) }}</td>
                                        <td class="Action">
                                            <span>
                                                <div class="action-btn btn-primary ms-2">
                                                    <a href="#" data-size="lg"
                                                        data-url="{{ route('blog.edit', $blog->id) }}"
                                                        data-ajax-popup="true" data-title="{{ __('Edit Blog') }}"
                                                        class="mx-3 btn btn-sm d-inline-flex align-items-center"
                                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                                        title="{{ __('Edit') }}"><i
                                                            class="ti ti-pencil text-white"></i></a>
                                                </div>
                                                <div class="action-btn bg-danger ms-2">
                                                    <a class="bs-pass-para align-items-center btn btn-sm d-inline-flex"
                                                        href="#" data-title="{{ __('Delete Lead') }}"
                                                        data-confirm="{{ __('Are You Sure?') }}"
                                                        data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                        data-confirm-yes="delete-form-{{ $blog->id}}"
                                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                                        title="{{ __('Delete ') }}">

                                                        <i class="ti ti-trash"></i>
                                                    </a>
                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['blog.destroy', $blog->id], 'id' => 'delete-form-' . $blog->id]) !!}
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
