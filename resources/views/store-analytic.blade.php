@extends('layouts.admin')
@section('page-title')
    {{ __('Store Analytics') }}
@endsection
@push('css-page')
@endpush
@section('content')
    <!-- [ Main Content ] start -->
    <div class="row">
        <!-- [ sample-page ] start -->
        <div class="col-sm-12">
            <div class="row">
                <div class="col-xxl-5">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <h5>{{ __('Top URL') }}</h5>
                            <span class="float-right">{{ __('Top') . ' 5 ' . __('URL') }}</span>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <tbody>
                                        @foreach ($visitor_url as $url)
                                            <tr>
                                                <td>
                                                    <h6 class="m-0">{{ $url->url }}</h6>
                                                </td>
                                                <td>
                                                    <h6 class="m-0">{{ $url->total }}</h6>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <h5>{{ 'Platform' }}</h5>
                            <span class="float-right mb-0">{{ __('Last 15 Days') }}</span>
                        </div>
                        <div class="card-body">
                            <div id="user_platform-chart"></div>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-7">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <h5>{{ __('Visitor') }}</h5>
                            <span class="float-right mb-0">{{ __('Last 15 Days') }}</span>
                        </div>
                        <div class="card-body">
                            <div id="apex-storedashborad" data-color="primary" data-height="200"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between">
                                    <h5>{{ __('DeviceLast') }}</h5>
                                    <span class="float-right mb-0">{{ __('Last 15 Days') }}</span>
                                </div>
                                <div class="card-body">
                                    <div id="pie-storedashborad"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between">
                                    <h5>{{ __('Browser') }}</h5>
                                    <span class="float-right mb-0">{{ __('Last 15 Days') }}</span>
                                </div>
                                <div class="card-body">
                                    <div id="pie-storebrowser"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- [ sample-page ] end -->
    </div>
    <!-- [ Main Content ] end -->
@endsection
@push('script-page')
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
                    name: "{{ __('Totle Page View') }}",
                    data: {!! json_encode($chartData['data']) !!}
                }, {
                    name: "{{ __('Unique Page View') }}",
                    data: {!! json_encode($chartData['unique_data']) !!}
                }],

                xaxis: {
                    categories: {!! json_encode($chartData['label']) !!},
                    title: {
                        text: 'Last 15 days'
                    }
                },
                colors: ['#e83e8c','#ffa21d'],

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
            var chart = new ApexCharts(document.querySelector("#apex-storedashborad"), options);
            chart.render();
        })();

        var options = {
            series: {!! json_encode($devicearray['data']) !!},
            chart: {
                width: 350,
                type: 'pie',
            },
            colors: ["#685ee5", "#3ec9d6", "#ffa21d", "#e83e8c"],
            labels: {!! json_encode($devicearray['label']) !!},
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        width: 200
                    },
                    legend: {
                        position: 'bottom',
                    }
                }
            }]
        };
        var chart = new ApexCharts(document.querySelector("#pie-storedashborad"), options);
        chart.render();
        var options = {
            series: {!! json_encode($browserarray['data']) !!},
            chart: {
                width: 350,
                type: 'pie',
            },
            colors: ["#685ee5", "#3ec9d6", "#ffa21d", "#e83e8c"],
            labels: {!! json_encode($browserarray['label']) !!},
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        width: 200
                    },
                    legend: {
                        position: 'bottom',
                    }
                }
            }]
        };
        var chart = new ApexCharts(document.querySelector("#pie-storebrowser"), options);
        chart.render();
    </script>
    <script>
        var WorkedHoursChart = (function() {
            var $chart = $('#user_platform-chart');

            function init($this) {
                var options = {
                    chart: {
                        width: '100%',
                        type: 'bar',
                        zoom: {
                            enabled: false
                        },
                        toolbar: {
                            show: false
                        },
                        shadow: {
                            enabled: false,
                        },

                    },
                    plotOptions: {
                        bar: {
                            horizontal: false,
                            distributed: true,
                            columnWidth: '25%',
                            borderRadius: 12,
                            endingShape: 'rounded'
                        },
                    },
                    colors: ["#685ee5", "#3ec9d6", "#ffa21d", "#e83e8c"],
                    stroke: {
                        show: true,
                        width: 2,
                        colors: ['transparent']
                    },
                    series: [{
                        name: 'Platform',
                        data: {!! json_encode($platformarray['data']) !!},
                    }],
                    xaxis: {
                        labels: {
                            // format: 'MMM',
                            style: {
                                colors: PurposeStyle.colors.gray[600],
                                fontSize: '14px',
                                fontFamily: PurposeStyle.fonts.base,
                                cssClass: 'apexcharts-xaxis-label',
                            },
                        },
                        axisBorder: {
                            show: false
                        },
                        axisTicks: {
                            show: true,
                            borderType: 'solid',
                            color: PurposeStyle.colors.gray[300],
                            height: 6,
                            offsetX: 0,
                            offsetY: 0
                        },
                        title: {
                            text: '{{ __('Platform') }}'
                        },
                        categories: {!! json_encode($platformarray['label']) !!},
                    },
                    yaxis: {
                        labels: {
                            style: {
                                color: PurposeStyle.colors.gray[600],
                                fontSize: '12px',
                                fontFamily: PurposeStyle.fonts.base,
                            },
                        },
                        axisBorder: {
                            show: false
                        },
                        axisTicks: {
                            show: true,
                            borderType: 'solid',
                            color: PurposeStyle.colors.gray[300],
                            height: 6,
                            offsetX: 0,
                            offsetY: 0
                        }
                    },
                    fill: {
                        type: 'solid',
                        opacity: 1

                    },
                    markers: {
                        size: 4,
                        opacity: 0.7,
                        strokeColor: "#fff",
                        strokeWidth: 3,
                        hover: {
                            size: 7,
                        }
                    },
                    grid: {
                        borderColor: PurposeStyle.colors.gray[300],
                        strokeDashArray: 5,
                    },
                    dataLabels: {
                        enabled: false
                    }
                }
                // Get data from data attributes
                var dataset = $this.data().dataset,
                    labels = $this.data().labels,
                    color = $this.data().color,
                    height = $this.data().height,
                    type = $this.data().type;

                // Inject synamic properties
                // options.colors = [
                //     PurposeStyle.colors.theme[color]
                // ];
                // options.markers.colors = [
                //     PurposeStyle.colors.theme[color]
                // ];
                options.chart.height = height ? height : 350;
                // Init chart
                var chart = new ApexCharts($this[0], options);
                // Draw chart
                setTimeout(function() {
                    chart.render();
                }, 300);
            }

            // Events
            if ($chart.length) {
                $chart.each(function() {
                    init($(this));
                });
            }
        })();
    </script>
@endpush
