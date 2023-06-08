@extends('layouts.admin')
@section('page-title')
    {{ __('Dashboard') }}
@endsection
@section('content')
    @php
    $logo = asset(Storage::url('uploads/logo/'));
    $company_logo = \App\Models\Utility::getValByName('company_logo');
    @endphp
    <!-- [ Main Content ] start -->
    @if (\Auth::user()->type == 'Owner')
        <div class="row">
            <!-- [ sample-page ] start -->
            <div class="col-sm-12">
                <div class="row">
                    <div class="col-xxl-7">
                        <div class="row">
                            <div class="col-lg-3 col-6">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="theme-avtar bg-primary qrcode">
                                            {!! QrCode::generate($store_id['store_url']) !!}
                                        </div>
                                        <h6 class="mb-3 mt-4 ">{{ $store_id->name }}</h6>
                                        <a href="#" class="btn btn-primary btn-sm text-sm cp_link"
                                            data-link="{{ $store_id['store_url'] }}" data-bs-toggle="tooltip" data-bs-placement="top"
                                            title="{{ __('Click to copy link') }}">{{ __('Store Link') }}</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="theme-avtar bg-info">
                                            <i class="fas fa-cube"></i>
                                        </div>
                                        <h6 class="mb-3 mt-4 ">{{ __('Total Products') }}</h6>
                                        <h3 class="mb-0">{{ $newproduct }}</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="theme-avtar bg-warning">
                                            <i class="fas fa-cart-plus"></i>
                                        </div>
                                        <h6 class="mb-3 mt-4 ">{{ __('Total Sales') }}</h6>
                                        <h3 class="mb-0">{{ \App\Models\Utility::priceFormat($totle_sale) }}
                                        </h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="theme-avtar bg-danger">
                                            <i class="fas fa-shopping-bag"></i>
                                        </div>
                                        <h6 class="mb-3 mt-4 ">{{ __('Total Orders') }}</h6>
                                        <h3 class="mb-0">{{ $totle_order }}</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header d-flex justify-content-between">
                                <h5>{{ __('Recent Orders') }}</h5>
                                <span class="float-right mb-0">{{ __('Top') . ' 8 ' . __('Recent Orders') }}</span>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th scope="col">{{ __('Order') }}</th>
                                                <th scope="col" class="sort">{{ __('Date') }}</th>
                                                <th scope="col" class="sort">{{ __('Name') }}</th>
                                                <th scope="col" class="sort">{{ __('Value') }}</th>
                                                <th scope="col" class="sort">{{ __('Payment Type') }}</th>
                                                <th scope="col" class="text-end">{{ __('Action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (!empty($new_orders))
                                                @foreach ($new_orders as $order)
                                                    @if ($order->status != 'Cancel Order')
                                                        <tr>
                                                            <td>
                                                                <div class="d-flex align-items-center">
                                                                    <a href="{{ route('orders.show', \Illuminate\Support\Facades\Crypt::encrypt($order->id)) }}"
                                                                        class="btn bg-warning  btn-sm text-sm cp_link"
                                                                        data-link="{{ $store_id['store_url'] }}"
                                                                        data-toggle="tooltip"
                                                                        data-original-title="{{ __('Click to copy link') }}">
                                                                        <span class="btn-inner--icon"><i
                                                                                class="fas fa-download"></i></span>
                                                                        <span
                                                                            class="btn-inner--text">{{ $order->order_id }}</span>
                                                                    </a>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <h6 class="m-0">
                                                                    {{ \App\Models\Utility::dateFormat($order->created_at) }}
                                                                </h6>
                                                            </td>
                                                            <td>
                                                                <h6 class="m-0">{{ $order->name }}</h6>
                                                            </td>
                                                            <td>
                                                                <h6 class="m-0">
                                                                    {{ \App\Models\Utility::priceFormat($order->price) }}
                                                                    <h6>
                                                            </td>
                                                            <td>
                                                                <h6 class="m-0">{{ $order->payment_type }}<h6>
                                                            </td>
                                                            <td>
                                                                <div class="actions ml-3">
                                                                    <div
                                                                        class="d-flex align-items-center justify-content-end">
                                                                        <button type="button"
                                                                            class="btn btn-sm {{ $order->status == 'pending' ? 'btn-soft-info' : 'btn-soft-success' }} btn-icon rounded-pill">
                                                                            <span class="btn-inner--icon">

                                                                                @if ($order->status == 'pending')
                                                                                    <i class="fas fa-check soft-info"></i>
                                                                                @else
                                                                                    <i
                                                                                        class="fa fa-check-double soft-success"></i>
                                                                                @endif
                                                                            </span>
                                                                            @if ($order->payment_status == 'approved' && $order->status == 'pending')
                                                                                <span class="btn-inner--text">
                                                                                    {{ __('Paid') }}:
                                                                                    {{ \App\Models\Utility::dateFormat($order->created_at) }}
                                                                                @else
                                                                                </span><span class="btn-inner--text">
                                                                                    {{ __('Delivered') }}:
                                                                                    {{ \App\Models\Utility::dateFormat($order->updated_at) }}
                                                                                </span>
                                                                            @endif

                                                                        </button>
                                                                        <div class="action-btn bg-info ms-2">
                                                                            <a href="{{ route('orders.show', \Illuminate\Support\Facades\Crypt::encrypt($order->id)) }}"
                                                                                class="mx-3 btn btn-sm d-inline-flex align-items-center"
                                                                                data-bs-toggle="tooltip" data-bs-placement="top"
                                                                                title="{{ __('Details') }}"><i
                                                                                    class="ti ti-eye text-white"></i></a>
                                                                        </div>

                                                                    </div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endif
                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-5">
                        <div class="card">
                            <div class="card-header">
                                <h5>{{ __('Order') }}</h5>
                            </div>
                            <div class="card-body">
                                <div id="apex-dashborad" data-color="primary" data-height="230"></div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header d-flex justify-content-between">
                                <h5>{{ __('Top Products') }}</h5>
                                <span class="float-right">{{ __('Top') . ' 5 ' . __('Products') }}</span>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th scope="col" class="sort" data-sort="name">
                                                    {{ __('Product') }}
                                                </th>
                                                <th scope="col" class="sort" data-sort="budget">
                                                    {{ __('Quantity') }}
                                                </th>
                                                <th scope="col" class="sort text-right" data-sort="completion">
                                                    {{ __('Price') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($products as $product)
                                                @foreach ($item_id as $k => $item)
                                                    @if ($product->id == $item)
                                                        <tr>
                                                            <td>
                                                                <div class="d-flex align-items-center">
                                                                    @if (!empty($product->is_cover))
                                                                        <img src="{{ asset(Storage::url('uploads/is_cover_image/' . $product->is_cover)) }}"
                                                                            class="wid-25" alt="images">
                                                                    @else
                                                                        <img src="{{ asset(Storage::url('uploads/is_cover_image/default.jpg')) }}"
                                                                            class="wid-25" alt="images">
                                                                    @endif
                                                                    <div class="ms-3">
                                                                        <h6 class="m-0">{{ $product->name }}
                                                                        </h6>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <h6 class="m-0">{{ $product->quantity }}</h6>
                                                            </td>
                                                            <td>
                                                                <small
                                                                    class="text-muted">{{ \App\Models\Utility::priceFormat($product->price) }}</small>
                                                                <h6 class="m-0">{{ $totle_qty[$k] }}
                                                                    {{ __('Sold') }}</h6>
                                                            </td>
                                                        </tr>
                                                    @endif
                                                @endforeach
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- [ sample-page ] end -->
        </div>
    @else
        <div class="row">
            <!-- [ sample-page ] start -->
            <div class="col-sm-12">
                <div class="row">
                    <div class="col-xxl-6">
                        <div class="row">
                            <div class="col-lg-4 col-6">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="theme-avtar bg-primary">
                                            <i class="fas fa-cube"></i>
                                        </div>
                                        <h6 class="mb-3 mt-4 ">{{ __('Total Store') }}</h6>
                                        <h3 class="mb-0">{{ $user->total_user }}</h3>
                                        {{-- <h6 class="mb-3 mt-4 ">{{ __('Paid Store') }}</h6>
                                        <h3 class="mb-0">{{ $user['total_paid_user'] }}</h3> --}}
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-6">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="theme-avtar bg-warning">
                                            <i class="fas fa-cart-plus"></i>
                                        </div>
                                        <h6 class="mb-3 mt-4 ">{{ __('Total Orders') }}</h6>
                                        <h3 class="mb-0">{{ $user->total_orders }}</h3>
                                        {{-- <h6 class="mb-3 mt-4 ">{{ __('Total Order Amount') }}</h6>
                                        <h3 class="mb-0">
                                            {{ env('CURRENCY_SYMBOL') . $user['total_orders_price'] }}</h3> --}}
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-6">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="theme-avtar bg-danger">
                                            <i class="fas fa-shopping-bag"></i>
                                        </div>
                                        <h6 class="mb-3 mt-4 ">{{ __('Total Plans') }}</h6>
                                        <h3 class="mb-0">{{ $user['total_plan'] }}</h3>
                                        {{-- <h6 class="mb-3 mt-4 ">{{ __('Most Purchase Plan') }}</h6>
                                        <h3 class="mb-0">
                                            {{ !empty($user['most_purchese_plan']) ? $user['most_purchese_plan'] : '-' }}</h3> --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-6">
                        <div class="card">
                            <div class="card-header">
                                <h5>{{ __('Recent Order') }}</h5>
                            </div>
                            <div class="card-body">
                                <div id="plan_order" data-color="primary" data-height="230"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- [ sample-page ] end -->
        </div>
    @endif
    <!-- [ Main Content ] end -->
@endsection
@push('script-page')
    @if (\Auth::user()->type == 'Owner')
        <script>
            $(document).ready(function() {
                $('.cp_link').on('click', function() {
                    var value = $(this).attr('data-link');
                    var $temp = $("<input>");
                    $("body").append($temp);
                    $temp.val(value).select();
                    document.execCommand("copy");
                    $temp.remove();
                    show_toastr('Success', '{{ __('Link copied') }}', 'success')
                });
            });

            (function() {
                var options = {
                    chart: {
                        height: 250,
                        type: 'area',
                        toolbar: {
                            show: false,
                        },
                    },
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        width: 2,
                        curve: 'smooth'
                    },


                    series: [{
                        name: "Order",
                        data: {!! json_encode($chartData['data']) !!}
                    }],

                    xaxis: {
                        axisBorder: {
                            show: !1
                        },
                        type: "MMM",
                        categories: {!! json_encode($chartData['label']) !!}
                    },
                    colors: ['#e83e8c'],

                    grid: {
                        strokeDashArray: 4,
                    },
                    legend: {
                        show: false,
                    },
                    markers: {
                        size: 4,
                        colors: ['#FFA21D'],
                        opacity: 0.9,
                        strokeWidth: 2,
                        hover: {
                            size: 7,
                        }
                    },
                    yaxis: {
                        tickAmount: 3,
                    }
                };
                var chart = new ApexCharts(document.querySelector("#apex-dashborad"), options);
                chart.render();
            })();
            var scrollSpy = new bootstrap.ScrollSpy(document.body, {
                target: '#useradd-sidenav',
                offset: 300
            })
        </script>
    @else
        <script>
            (function() {
                var options = {
                    chart: {
                        height: 250,
                        type: 'area',
                        toolbar: {
                            show: false,
                        },
                    },
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        width: 2,
                        curve: 'smooth'
                    },


                    series: [{
                        name: "Order",
                        data: {!! json_encode($chartData['data']) !!}
                        // data: [10,20,30,40,50,60,70,40,20,50,60,20,50,70]
                    }],

                    xaxis: {
                        axisBorder: {
                            show: !1
                        },
                        type: "MMM",
                        categories: {!! json_encode($chartData['label']) !!}
                    },
                    colors: ['#e83e8c'],

                    grid: {
                        strokeDashArray: 4,
                    },
                    legend: {
                        show: false,
                    },
                    markers: {
                        size: 4,
                        colors: ['#FFA21D'],
                        opacity: 0.9,
                        strokeWidth: 2,
                        hover: {
                            size: 7,
                        }
                    },
                    yaxis: {
                        tickAmount: 3,
                    }
                };
                var chart = new ApexCharts(document.querySelector("#plan_order"), options);
                chart.render();
            })();
        </script>
    @endif
@endpush
