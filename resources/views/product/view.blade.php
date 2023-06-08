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
    <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
@endsection
@section('action-btn')
    <div class="">
        <a href="{{ route('product.edit', $product->id) }}" class="btn btn-sm btn-primary btn-icon m-1"
            data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Edit Product') }}"><i
                class="ti ti-pencil text-white"></i></a>
    </div>
@endsection
@section('filter')
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <!-- Product title -->
                    <h5 class="h4">{{ $product->name }}</h5>
                    <!-- Rating -->
                    <div class="row align-items-center">
                        <div class="col-sm-6">
                            <span class="static-rating static-rating-sm d-block">
                                @for ($i = 1; $i <= 5; $i++)
                                    @php
                                        $icon = 'fa-star';
                                        $color = '';
                                        $newVal1 = $i - 0.5;
                                        if ($avg_rating < $i && $avg_rating >= $newVal1) {
                                            $icon = 'fa-star-half-alt';
                                        }
                                        if ($avg_rating >= $newVal1) {
                                            $color = 'text-warning';
                                        }
                                    @endphp
                                    <i class="fas {{ $icon . ' ' . $color }}"></i>
                                @endfor
                                {{ $avg_rating }}/5 ({{ $user_count }} {{ __('reviews') }})
                            </span>
                        </div>
                        <div class="col-sm-6 text-sm-right">
                            <ul class="list-inline mb-0">
                                <li class="list-inline-item">
                                    <span
                                        class="badge badge-pill badge-soft-info">{{ __('ID: #') }}{{ $product->SKU }}</span>
                                </li>
                                <li class="list-inline-item">
                                    @if ($product->enable_product_variant != 'on')
                                        @if ($product->quantity == 0)
                                            <span class="badge badge-pill badge-soft-danger">
                                                {{ __('Out of stock') }}
                                            </span>
                                        @else
                                            <span class="badge badge-pill badge-soft-success">
                                                {{ __('In stock') }}
                                            </span>
                                        @endif
                                    @endif
                                </li>
                            </ul>
                        </div>
                    </div>
                    <!-- Description -->
                    {!! $product->description !!}
                </div>
            </div>

            <div class="card">
                <div class="card-body p-3 d-flex justify-content-between">
                    <h5 class="float-left mb-0 pt-2">{{ __('Rating') }}</h5>
                    <a href="#" class="btn btn-sm btn-primary btn-icon m-1" data-bs-toggle="tooltip" data-size="md"
                        data-toggle="modal" data-url="{{ route('rating', [$store->slug, $product->id]) }}"
                        data-ajax-popup="true" data-title="{{ __('Create New Rating') }}" data-bs-placement="top"
                        title="{{ __('Create New Rating') }}"><i class="ti ti-plus"></i></i></a>
                </div>
                @foreach ($product_ratings as $product_key => $product_rating)
                    <div id="review_list" class="px-3 pt-2 border-top pb-0">
                        <div class="d-flex justify-content-between">
                            <div class="theme-review float-left" id="comment_126267">
                                <div class="theme_review_item">
                                    <div class="theme-review__heading">
                                        <div class="theme-review__heading__item text-sm small">
                                            <h6>{{ $product_rating->title }}</h6>
                                            <tr class="list-dotted ">
                                                <td class="list-dotted__item">by {{ $product_rating->name }} :</td>
                                                <td class="list-dotted__item">
                                                    {{ $product_rating->created_at->diffForHumans() }}</td>
                                            </tr>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="">
                                    <span class="m-0">
                                        <div class="action-btn btn-primary ms-2">
                                            <a href="#" data-size="md"
                                                class="mx-3 btn btn-sm d-inline-flex align-items-center"
                                                data-url="{{ route('rating.edit', $product_rating->id) }}"
                                                data-ajax-popup="true" data-title="{{ __('Edit Rating') }}"
                                                data-bs-toggle="tooltip" data-bs-placement="top"
                                                title="{{ __('Edit Product') }}"><i
                                                    class="ti ti-pencil text-white"></i></a>
                                        </div>
                                        <div class="action-btn bg-danger ms-2">
                                        <a class="bs-pass-para align-items-center btn btn-sm d-inline-flex" href="#" data-title="{{__('Delete Lead')}}" data-confirm="{{__('Are You Sure?')}}" data-text="{{__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="delete-form-{{$product_rating->id}}">
                                            <i class="ti ti-trash"></i>

                                        </a>
                                        {!! Form::open(['method' => 'DELETE', 'route' => ['rating.destroy', $product_rating->id],'id'=>'delete-form-'.$product_rating->id]) !!}
                                        {!! Form::close() !!}



                                        </div>
                                    </span>
                                <div class="rate">
                                    @for ($i = 0; $i < 5; $i++)
                                        <i
                                            class="fas fa-star {{ $product_rating->ratting > $i ? 'text-warning' : '' }}"></i>
                                    @endfor
                                </div>
                            </div>
                        </div>
                        <span class="clearfix"></span>
                        <div class="d-flex mt-2 justify-content-end">
                            <div class="custom-control form-switch">
                                <input type="checkbox" class="form-check-input rating_view" name="rating_view"
                                    id="enable_rating{{ $product_key }}" data-id="{{ $product_rating['id'] }}"
                                    {{ $product_rating->rating_view == 'on' ? 'checked' : '' }}>
                                <label class="custom-control-label form-check-label"
                                    for="enable_rating{{ $product_key }}"></label>
                            </div>
                        </div>
                        <br>
                        <div class="main_reply_body">
                            <p class="small pt-2">{{ $product_rating->description }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="col-lg-6">
            @if ($product->enable_product_variant == 'on')
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <input type="hidden" id="product_id" value="{{ $product->id }}">
                            <input type="hidden" id="variant_id" value="">
                            <input type="hidden" id="variant_qty" value="">
                            @foreach ($product_variant_names as $key => $variant)
                                <div class="col-sm-6 mb-4 mb-sm-0">
                                    <span class="d-block h6 mb-0">
                                        <th>
                                            <label for="" class="col-form-label"> {{ $variant->variant_name }}</label>

                                        </th>
                                        <select name="product[{{$key}}]" id='choices-multiple-{{$key}}'  class="form-control multi-select  pro_variants_name{{$key}}">
                                        <option value="">{{ __('Select')  }}</option>
                                            @foreach ($variant->variant_options as $key => $values)
                                            <option value="{{$values}}">{{$values}}</option>
                                        @endforeach
                                    </select>
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-sm-6 mb-4 mb-sm-0">
                            <span class="d-block h3 mb-0 variasion_price">
                                @if ($product->enable_product_variant == 'on')
                                    {{ \App\Models\Utility::priceFormat(0) }}
                                @else
                                    {{ \App\Models\Utility::priceFormat($product->price) }}
                                @endif

                            </span>
                            {{ !empty($product->product_taxs) ? $product->product_taxs->name : '' }}
                            {{ !empty($product->product_taxs->rate) ? $product->product_taxs->rate . '%' : '' }}
                        </div>
                        <div class="col-sm-6 d-flex justify-content-end">
                            <button type="button" class="btn btn-primary btn-icon">
                                <span class="btn-inner--icon variant_qty">
                                     @if($product->enable_product_variant =='on')
                                        0
                                    @else
                                        {{$product->quantity}}
                                    @endif
                                </span>
                                <span class="btn-inner--text">
                                    {{ __('Total Avl.Quantity') }}
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Product images -->
            <div class="card">
                <div class="card-body">
                    @if (!empty($product->is_cover))
                        <a href="{{ asset(Storage::url('uploads/is_cover_image/' . $product->is_cover)) }}"
                            data-fancybox="product">
                            <img alt="Image placeholder"
                                src="{{ asset(Storage::url('uploads/is_cover_image/' . $product->is_cover)) }}"
                                class="img-center pro_max_width1">
                        </a>
                    @else
                        <a href="{{ asset(Storage::url('uploads/is_cover_image/default.jpg')) }}"
                            data-fancybox="product">
                            <img alt="Image placeholder"
                                src="{{ asset(Storage::url('uploads/is_cover_image/default.jpg')) }}"
                                class="img-center pro_max_width1">
                        </a>
                    @endif
                    <div class="row mt-4">
                        @foreach ($product_image as $key => $products)
                            <div class="col-4">
                                <div class="p-3 border rounded">
                                    @if (!empty($product_image[$key]->product_images))
                                        <a href="{{ asset(Storage::url('uploads/product_image/' . $product_image[$key]->product_images)) }}"
                                            class="stretched-link" data-fancybox="product">
                                            <img alt="Image placeholder"
                                                src="{{ asset(Storage::url('uploads/product_image/' . $product_image[$key]->product_images)) }}"
                                                class="img-fluid">
                                        </a>
                                    @else
                                        <a href="{{ asset(Storage::url('uploads/product_image/default.jpg')) }}"
                                            class="stretched-link" data-fancybox="product">
                                            <img alt="Image placeholder"
                                                src="{{ asset(Storage::url('uploads/product_image/default.jpg')) }}"
                                                class="img-fluid">
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script-page')
    <script>
        $(document).on('change', '.rating_view', function() {
            var id = $(this).attr('data-id');
            var status = 'off';
            if ($(this).is(":checked")) {
                status = 'on';
            }
            var data = {
                id: id,
                status: status
            }

            $.ajax({
                url: '{{ route('rating.rating_view') }}',
                method: 'POST',
                data: data,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(data) {
                    show_toastr('success', data.success, 'success');
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                }
            });
        });

        $(document).on('change', '#pro_variants_name', function() {

            var variants = [];
            $(".variant-selection").each(function(index, element) {
                variants.push(element.value);
            });
            if (variants.length > 0) {
                $.ajax({
                    url: '{{ route('get.products.variant.quantity') }}',
                    data: {
                        "_token": $('meta[name="csrf-token"]').attr('content'),
                        variants: variants.join(' : '),
                        product_id: $('#product_id').val()
                    },

                    success: function(data) {
                        console.log(data);
                        $('.variasion_price').html(data.price);
                        $('#variant_id').val(data.variant_id);
                        $('.variant_qty').html(data.quantity);
                    }
                });
            }
        });
    </script>
@endpush
