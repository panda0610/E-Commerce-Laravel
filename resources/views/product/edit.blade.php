@extends('layouts.admin')
@section('page-title')
    {{ __('Product') }}
@endsection
@section('title')
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block text-white font-weight-bold mb-0 ">{{ __('Product') }}</h5>
    </div>
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('product.index') }}">{{ __('Product') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('Edit') }}</li>
@endsection
@section('action-btn')
@endsection
@section('filter')
@endsection
@push('css-page')
    <link rel="stylesheet" href="{{ asset('custom/libs/summernote/summernote-bs4.css') }}">
@endpush
@push('script-page')
    <script src="{{ asset('custom/libs/summernote/summernote-bs4.js') }}"></script>
    <script>
        var Dropzones = function() {
            var e = $('[data-toggle="dropzone1"]'),
                t = $(".dz-preview");
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            e.length && (Dropzone.autoDiscover = !1, e.each(function() {
                var e, a, n, o, i;
                e = $(this), a = void 0 !== e.data("dropzone-multiple"), n = e.find(t), o = void 0, i = {
                    url: "{{ route('products.update', $product->id) }}",
                    method: 'PUT',
                    headers: {
                        'x-csrf-token': CSRF_TOKEN,
                    },
                    thumbnailWidth: null,
                    thumbnailHeight: null,
                    previewsContainer: n.get(0),
                    previewTemplate: n.html(),
                    maxFiles: 10,
                    parallelUploads: 10,
                    autoProcessQueue: false,
                    uploadMultiple: true,
                    acceptedFiles: a ? null : "image/*",
                    success: function(file, response) {
                        if (response.flag == "success") {
                            show_toastr('success', response.msg, 'success');
                            window.location.href = "{{ route('product.index') }}";
                        } else {
                            show_toastr('Error', response.msg, 'error');
                        }
                    },
                    error: function(file, response) {
                        // Dropzones.removeFile(file);
                        if (response.error) {
                            show_toastr('Error', response.error, 'error');
                        } else {
                            show_toastr('Error', response, 'error');
                        }
                    },
                    init: function() {
                        var myDropzone = this;

                        this.on("addedfile", function(e) {
                            !a && o && this.removeFile(o), o = e
                        })
                    }
                }, n.html(""), e.dropzone(i)
            }))
        }()

        $('#submit-all').on('click', function() {

            var fd = new FormData();
            var file = document.getElementById('is_cover_image').files[0];
            var attachmentfile = document.getElementById('attachment').files[0];
            var downloadable_prodcutfile = document.getElementById('downloadable_prodcut').files[0];

            if (file) {
                fd.append('is_cover_image', file);
            }
            if (attachmentfile) {
                fd.append('attachment', attachmentfile);
            }
            if (downloadable_prodcutfile) {
                fd.append('downloadable_prodcut', downloadable_prodcutfile);
            }

            var files = $('[data-toggle="dropzone1"]').get(0).dropzone.getAcceptedFiles();
            $.each(files, function(key, file) {
                fd.append('multiple_files[' + key + ']', $('[data-toggle="dropzone1"]')[0].dropzone
                    .getAcceptedFiles()[key]); // attach dropzone image element
            });
            var other_data = $('#frmTarget').serializeArray();

            $.each(other_data, function(key, input) {
                fd.append(input.name, input.value);
            });

            $.ajax({
                url: "{{ route('products.update', $product->id) }}",
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: fd,
                contentType: false,
                processData: false,
                success: function(data) {

                    if (data.flag == "success") {
                        show_toastr('success', data.msg, 'success');
                        window.location.href = "{{ route('product.index') }}";
                    } else {
                        show_toastr('Error', data.msg, 'error');
                    }
                },
                error: function(data) {
                    if (data.error) {
                        show_toastr('Error', data.error, 'error');
                    } else {
                        show_toastr('Error', data, 'error');
                    }
                },
            });
        });

        $(".deleteRecord").click(function() {

            var id = $(this).data("id");

            var token = $("meta[name='csrf-token']").attr("content");

            $.ajax({
                url: '{{ route('products.file.delete', '__product_id') }}'.replace('__product_id', id),
                type: 'DELETE',
                data: {
                    "id": id,
                    "_token": token,
                },
                success: function(data) {

                    if (data.success) {
                        show_toastr('success', data.success, 'success');
                        $('.product_Image[data-id="' + data.id + '"]').remove();
                    } else {
                        show_toastr('Error', data.error, 'error');
                        $('.product_Image[data-id="' + data.id + '"]').remove();
                    }
                }
            });
        });
    </script>
@endpush
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    {{ Form::model($product, ['method' => 'POST', 'id' => 'frmTarget', 'enctype' => 'multipart/form-data']) }}
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                {{ Form::label('name', __('Name'), ['class' => 'col-form-label']) }}
                                {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => __('Enter Name')]) !!}
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                {{ Form::label('product_categorie', __('Product Categories'), ['class' => 'col-form-label']) }}
                                {!! Form::select('product_categorie[]', $product_categorie, explode(',', $product->product_categorie), ['class' => 'form-control multi-select','id'=>'choices-multiple', 'multiple']) !!}
                                @if (count($product_categorie) == 0)
                                    {{ __('Add product category') }}
                                    <a href="{{ route('product_categorie.index') }}">
                                        {{ __('Click here') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                {{ Form::label('SKU', __('SKU'), ['class' => 'col-form-label']) }}
                                {!! Form::text('SKU', null, ['class' => 'form-control', 'placeholder' => __('Enter SKU')]) !!}
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                {{ Form::label('product_tax', __('Product Tax'), ['class' => 'col-form-label']) }}
                                {{ Form::select('product_tax[]', $product_tax, explode(',', $product->product_tax), ['class' => 'form-control multi-select','id'=>'choices-multiple1','multiple']) }}
                                @if (count($product_tax) == 0)
                                    {{ __('Add product tax') }}
                                    <a href="{{ route('product_tax.index') }}">
                                        {{ __('Click here') }}
                                    </a>
                                @endif
                                @error('product_tax')
                                    <span class="invalid-product_tax" role="alert">
                                        <strong class="text-danger">{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-6 proprice">
                            <div class="form-group">
                                {{ Form::label('price', __('Price'), ['class' => 'col-form-label']) }}
                                {{ Form::number('price', null, ['step' => 'any', 'class' => 'form-control']) }}
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                {{ Form::label('last_price', __('Last Price'), ['class' => 'col-form-label']) }}
                                {{ Form::number('last_price', null, ['step' => 'any', 'class' => 'form-control']) }}
                            </div>
                        </div>
                        <div class="col-6 proprice">
                            <div class="form-group">
                                {{ Form::label('quantity', __('Stock Quantity'), ['class' => 'col-form-label']) }}
                                {!! Form::text('quantity', null, ['class' => 'form-control', 'placeholder' => __('Enter Stock Quantity')]) !!}
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="attachment" class="col-form-label">{{ __('Attachment') }}</label>
                                <input type="file" name="attachment" id="attachment" class="form-control">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="downloadable_prodcut"
                                    class="col-form-label font-bold-700">{{ __('Downloadable Product') }}</label>
                                <input type="file" name="downloadable_prodcut" id="downloadable_prodcut"
                                    class="form-control">
                                <small>{{ $product->downloadable_prodcut }}</small>
                            </div>

                        </div>
                        <div class="col-12">
                            <h6>{{ __('Custom Field') }} </h6>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                {{ Form::label('custom_field_1', __('Custom Field'), ['class' => 'col-form-label']) }}
                                {{ Form::text('custom_field_1', null, ['class' => 'form-control','placeholder' => __('Enter Custom Field'),'required' => 'required']) }}
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                {{ Form::label('custom_value_1', __('Custom Value'), ['class' => 'col-form-label']) }}
                                {{ Form::text('custom_value_1', null, ['class' => 'form-control','placeholder' => __('Enter Custom Value'),'required' => 'required']) }}
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                {{ Form::label('custom_field_2', __('Custom Field'), ['class' => 'col-form-label']) }}
                                {{ Form::text('custom_field_2', null, ['class' => 'form-control','placeholder' => __('Enter Custom Field'),'required' => 'required']) }}
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                {{ Form::label('custom_value_2', __('Custom Value'), ['class' => 'col-form-label']) }}
                                {{ Form::text('custom_value_2', null, ['class' => 'form-control','placeholder' => __('Enter Custom Value'),'required' => 'required']) }}
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                {{ Form::label('custom_field_3', __('Custom Field'), ['class' => 'col-form-label']) }}
                                {{ Form::text('custom_field_3', null, ['class' => 'form-control','placeholder' => __('Enter Custom Field'),'required' => 'required']) }}
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                {{ Form::label('custom_value_3', __('Custom Value'), ['class' => 'col-form-label']) }}
                                {{ Form::text('custom_value_3', null, ['class' => 'form-control','placeholder' => __('Enter Custom Value'),'required' => 'required']) }}
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                {{ Form::label('custom_field_4', __('Custom Field'), ['class' => 'col-form-label']) }}
                                {{ Form::text('custom_field_4', null, ['class' => 'form-control','placeholder' => __('Enter Custom Field'),'required' => 'required']) }}
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                {{ Form::label('custom_value_4', __('Custom Value'), ['class' => 'col-form-label']) }}
                                {{ Form::text('custom_value_4', null, ['class' => 'form-control','placeholder' => __('Enter Custom Value'),'required' => 'required']) }}
                            </div>
                        </div>

                        <div class="form-group col-md-6">
                            <div class="custom-control form-switch">
                                <input type="checkbox" name="product_display" class="form-check-input" id="product_display"
                                    {{ $product->product_display == 'on' ? 'checked' : '' }}>
                                    {{ Form::label('product_display', __('Product Display'), ['class' => 'form-check-label']) }}
                            </div>
                            @error('product_display')
                                <span class="invalid-product_display" role="alert">
                                    <strong class="text-danger">{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        @if (isset($product_variant_names) && !empty($product_variant_names))

                            <div class="form-group col-md-6 py-4">
                                <div class="custom-control form-switch">
                                    <input type="checkbox" class="form-check-input" name="enable_product_variant"
                                        id="enable_product_variant"
                                        {{ $product['enable_product_variant'] == 'on' ? 'checked' : '' }}>

                                    <label class="custom-control-label"
                                        for="enable_product_variant">{{ __('Display Variants') }}</label>
                                </div>
                            </div>
                            <div id="productVariant" class="col-md-12">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="card my-3">
                                            <div class="card-header">
                                                <h5 class="card-header-title">{{ __('Product Variants') }}</h5>
                                            </div>
                                            <div class="card-body">
                                                <div class="row form-group">
                                                    <div class="table-responsive">
                                                        <table class="table">
                                                            <thead>
                                                                <tr class="text-center">



                                                                    @if (isset($product_variant_names))
                                                                        @foreach ($product_variant_names as $variant)
                                                                            <th><span>{{ ucwords($variant) }}</span></th>
                                                                        @endforeach
                                                                    @endif
                                                                    <th><span>{{ __('Price') }}</span></th>
                                                                    <th><span>{{ __('Quantity') }}</span></th>
                                                                    <th></th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @if (isset($productVariantArrays))
                                                                    @foreach ($productVariantArrays as $counter => $productVariant)
                                                                        <tr
                                                                            data-id="{{ $productVariant['product_variants']['id'] }}">
                                                                            @foreach (explode(' : ', $productVariant['product_variants']['name']) as $key => $values)
                                                                                <td>
                                                                                    <input type="text"
                                                                                        name="variants[{{ $productVariant['product_variants']['id'] }}][variants][{{ $key }}][]"
                                                                                        autocomplete="off"
                                                                                        spellcheck="false"
                                                                                        class="form-control"
                                                                                        value="{{ $values }}">
                                                                                </td>
                                                                            @endforeach
                                                                            <td>
                                                                                <input type="number"
                                                                                    name="variants[{{ $productVariant['product_variants']['id'] }}][price]"
                                                                                    autocomplete="off" spellcheck="false"
                                                                                    placeholder="{{ __('Enter Price') }}"
                                                                                    class="form-control vprice_{{ $counter }}"
                                                                                    value="{{ $productVariant['product_variants']['price'] }}">
                                                                            </td>
                                                                            <td>
                                                                                <input type="number"
                                                                                    name="variants[{{ $productVariant['product_variants']['id'] }}][quantity]"
                                                                                    autocomplete="off" spellcheck="false"
                                                                                    placeholder="{{ __('Enter Quantity') }}"
                                                                                    class="form-control vquantity_{{ $counter }}"
                                                                                    value="{{ $productVariant['product_variants']['quantity'] }}">
                                                                            </td>
                                                                            <td
                                                                                class="d-flex align-items-center mt-3 border-0">

                                                                                <div class="action-btn bg-danger ms-2">
                                                                                    <a class="bs-pass-para align-items-center btn btn-sm d-inline-flex"
                                                                                        href="#"
                                                                                        data-title="{{ __('Delete Lead') }}"
                                                                                        data-confirm="{{ __('Are You Sure?') }}"
                                                                                        data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                                                        data-confirm-yes="delete-form-{{ $productVariant['product_variants']['id']}}">
                                                                                        <i class="ti ti-trash"></i>

                                                                                    </a>
                                                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['products.variant.delete', $productVariant['product_variants']['id']], 'id' => 'delete-form-' . $productVariant['product_variants']['id']]) !!}
                                                                                    {!! Form::close() !!}
                                                                                </div>
                                                                            </td>

                                                                            {{-- <td
                                                                                    class="d-flex align-items-center mt-3 border-0">
                                                                                    <a href="#" class="action-item"
                                                                                        data-toggle="tooltip"
                                                                                        data-original-title="{{ __('Delete') }}"
                                                                                        data-confirm="{{ __('Are You Sure?') . ' | ' . __('This action can not be undone. Do you want to continue?') }}"
                                                                                        data-confirm-yes="document.getElementById('delete-p-variant-{{ $productVariant['product_variants']['id'] }}').submit();">
                                                                                        <i class="fas fa-trash text-danger"></i>
                                                                                    </a>
                                                                                    @if ($loop->iteration == 1)
                                                                                        <form action="" method=""></form>
                                                                                    @endif
                                                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['products.variant.delete', $productVariant['product_variants']['id']], 'id' => 'delete-p-variant-' . $productVariant['product_variants']['id']]) !!}
                                                                                    {!! Form::close() !!} --}}
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                @endif
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="col-12 border-0">
                            <div class="row">
                                <div class="col-6">
                                    <h5 class="mb-0">{{ __('Product Image') }}</h5>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        {{ Form::label('sub_images', __('Upload Product Images'), ['class' => 'col-form-label']) }}
                                        <div class="dropzone dropzone-multiple" data-toggle="dropzone1"
                                            data-dropzone-url="http://" data-dropzone-multiple>
                                            <div class="fallback">
                                                <div class="custom-file">
                                                    <input type="file" class="custom-file-input" id="dropzone-1" name="file"
                                                        multiple>
                                                    <label class="custom-file-label"
                                                        for="customFileUpload">{{ __('Choose file') }}</label>
                                                </div>
                                            </div>
                                            <ul
                                                class="dz-preview dz-preview-multiple list-group list-group-lg list-group-flush">
                                                <li class="list-group-item px-0">
                                                    <div class="row align-items-center">
                                                        <div class="col-auto">
                                                            <div class="avatar">
                                                                <img class="rounded" src="" alt="Image placeholder"
                                                                    data-dz-thumbnail>
                                                            </div>
                                                        </div>
                                                        <div class="col">
                                                            <h6 class="text-sm mb-1" data-dz-name>...</h6>
                                                            <p class="small text-muted mb-0" data-dz-size></p>
                                                        </div>
                                                        <div class="col-auto">
                                                            <a href="#" class="dropdown-item" data-dz-remove>
                                                                <i class="fas fa-trash-alt"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="card-wrapper p-3 lead-common-box">
                                            @foreach ($product_image as $file)
                                                <div class="card mb-3 border shadow-none product_Image"
                                                    data-id="{{ $file->id }}">
                                                    <div class="px-3 py-3">
                                                        <div class="row align-items-center">
                                                            <div class="col ml-n2">
                                                                <p class="card-text small text-muted">
                                                                    <img class="rounded"
                                                                        src=" {{ asset(Storage::url('uploads/product_image/' . $file->product_images)) }}"
                                                                        width="70px" alt="Image placeholder"
                                                                        data-dz-thumbnail>
                                                                </p>
                                                            </div>
                                                            <div class="col-auto actions">
                                                                <a class="action-item"
                                                                    href=" {{ asset(Storage::url('uploads/product_image/' . $file->product_images)) }}"
                                                                    download="" data-toggle="tooltip"
                                                                    data-original-title="{{ __('Download') }}">
                                                                    <i class="fas fa-download"></i>
                                                                </a>
                                                            </div>

                                                            <div class="col-auto actions">
                                                                <a name="deleteRecord" class="action-item deleteRecord"
                                                                    data-id="{{ $file->id }}">
                                                                    <i class="fas fa-trash"></i>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="is_cover_image"
                                            class="col-form-label">{{ __('Upload Cover Image') }}</label>
                                        <input type="file" name="is_cover_image" id="is_cover_image"
                                            class="form-control">
                                    </div>

                                    <div class="card-wrapper p-3 lead-common-box">
                                        <div class="card mb-3 border shadow-none">
                                            <div class="px-3 py-3">
                                                <div class="row align-items-center">
                                                    <div class="col ml-n2">
                                                        <p class="card-text small text-muted">
                                                            <img class="rounded"
                                                                src=" {{ asset(Storage::url('uploads/is_cover_image/' . $product->is_cover)) }}"
                                                                width="70px" alt="Image placeholder" data-dz-thumbnail>
                                                        </p>
                                                    </div>
                                                    <div class="col-auto actions">
                                                        <a class="action-item"
                                                            href=" {{ asset(Storage::url('uploads/is_cover_image/' . $product->is_cover)) }}"
                                                            download="" data-toggle="tooltip"
                                                            data-original-title="{{ __('Download') }}">
                                                            <i class="fas fa-download"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 pt-4">
                            <div class="form-group">
                                {{ Form::label('description', 'Product Description', ['class' => 'col-form-label']) }}
                                {!! Form::textarea('description', !empty($product->description) ? $product->description : '', ['class' => 'form-control summernote-simple', 'rows' => 2, 'placeholder' => 'Product Description']) !!}
                            </div>
                        </div>
                        <div class="col-12 pt-4">
                            <div class="form-group">
                                {{ Form::label('specification', 'Product Specification', ['class' => 'col-form-label']) }}
                                {{ Form::textarea('specification', !empty($product->specification) ? $product->specification : '', ['class' => 'form-control summernote-simple','rows' => 3,'placeholder' => 'Product Specification']) }}
                            </div>
                        </div>
                        <div class="col-12 pt-4">
                            <div class="form-group">
                                {{ Form::label('detail', 'Product Details', ['class' => 'col-form-label']) }}
                                {{ Form::textarea('detail', !empty($product->detail) ? $product->detail : '', ['class' => 'form-control summernote-simple','rows' => 3,'placeholder' => 'Product Details']) }}
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-12 d-flex justify-content-end col-form-label">
                        <a href="{{ route('product.index') }}"
                            class="btn btn-secondary btn-light">{{ __('Cancel') }}</a>
                        <input type="submit" id="submit-all" value="{{ __('Update') }}" class="btn btn-primary ms-2">
                    </div>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
@endsection
