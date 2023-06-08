@php
// get theme color
$setting = App\Models\Utility::colorset();
$color = 'theme-3';
if (!empty($setting['color'])) {
    $color = $setting['color'];
}
$users = \Auth::user();
$currantLang = $users->currentLanguages();
$languages = \App\Models\Utility::languages();
$footer_text = isset(\App\Models\Utility::settings()['footer_text']) ? \App\Models\Utility::settings()['footer_text'] : '';
@endphp
<!DOCTYPE html>
<html dir="{{ env('SITE_RTL') == 'on' ? 'rtl' : '' }}">
<meta name="csrf-token" content="{{ csrf_token() }}">
{{-- @dd($color) --}}
 @include('partials.admin.head')
<body class="{{ $color }}">
    <!-- [ Pre-loader ] start -->
    <div class="loader-bg">
        <div class="loader-track">
            <div class="loader-fill"></div>
        </div>
    </div>
    <!-- [ navigation menu ] start -->
    @include('partials.admin.menu')
    <!-- [ Header ] start -->
    @include('partials.admin.header')
    <!-- [ Main Content ] start -->
    <div class="dash-container">
        <div class="dash-content">
            <!-- [ breadcrumb ] start -->
            <div class="page-header">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <div class="d-flex d-sm-flex align-items-center justify-content-between">
                                <div>
                                    <div class="page-header-title">
                                        <h4 class="m-b-10">@yield('page-title')</h4>
                                    </div>
                                    <ul class="breadcrumb">
                                        @yield('breadcrumb')
                                    </ul>
                                </div>
                                @yield('action-btn')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- [ breadcrumb ] end -->
            <!-- [ Main Content ] start -->
            @yield('content')
        </div>
    </div>
    <!-- [ Main Content ] end -->
    <!-- Modal -->
    {{-- <div class="modal fade" id="commonModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header align-items-center">
                    <div class="modal-title">
                        <h6 class="mb-0" id="modelCommanModelLabel"></h6>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                </div>
            </div>
        </div>
    </div> --}}
    <div class="modal fade" id="commonModal" tabindex="-1" role="dialog" aria-labelledby="commonModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="commonModalLabel"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
            </div>
        </div>
    </div>
</div>
<input type="checkbox" class="d-none" id="cust-theme-bg"  {{ Utility::getValByName('cust_theme_bg') == 'on' ? 'checked' : '' }} />
<input type="checkbox" class="d-none" id="cust-darklayout" {{ Utility::getValByName('cust_darklayout') == 'on' ? 'checked' : '' }} />
    @include('partials.admin.footer')
</body>

</html>
