<!DOCTYPE html>
<html lang="en" dir="{{env('SITE_RTL') == 'on'?'rtl':''}}">
@php
    $userstore = \App\Models\UserStore::where('store_id', $store->id)->first();
    $settings   =\DB::table('settings')->where('name','company_favicon')->where('created_by', $userstore->user_id)->first();
@endphp
@include('layouts.shophead')
@php
    if(!empty(session()->get('lang')))
    {
        $currantLang = session()->get('lang');
    }else{
        $currantLang = $store->lang;
    }
    $languages= Utility::languages();
@endphp
<body class="loaded">
@include('layouts.shopheader')


@yield('content')

<div class="container-lg">
    <div class="row">
        <div class="modal fade edit-profile" id="commonModalBlur" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header align-items-center">
                        <h3 class="modal-title profile-heading" id="modelCommanModelLabel"></h3>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if($demoStoreThemeSetting['enable_footer_note'] == 'on' || $demoStoreThemeSetting['enable_footer'] == 'on')
    <footer id="footer-main">
        <div class="container-lg {{($demoStoreThemeSetting['enable_footer_note'] != 'on')?'pt-1':'' }}">
            <div class="row">
                <div class="col col-lg-12">
                    <div class="footer-section">
                        {{--FOOTER 1--}}
                        @if($demoStoreThemeSetting['enable_footer_note'] == 'on')
                            <div class="footer-logo">
                                <a href="{{route('store.slug',$store->slug)}}">
                                    @if(!empty($demoStoreThemeSetting['footer_logo']))
                                        <img src="{{asset('assets/img/lmsgo-logo.svg')}}" alt="lmsgo-logo" class="img-fluid">
                                    @else
                                        <img src="{{asset(Storage::url('uploads/store_logo/'.$demoStoreThemeSetting['footer_logo']))}}" alt="Footer logo" style="height: 70px;">
                                    @endif
                                </a>
                                <p>{{$demoStoreThemeSetting['footer_note']}}</p>
                            </div>
                            <div class="footer-link">
                                <ul>
                                    @if($demoStoreThemeSetting['enable_quick_link1'] == 'on')
                                        <li><a href="{{$demoStoreThemeSetting['quick_link_url11']}}">{{$demoStoreThemeSetting['quick_link_name11']}}</a></li>
                                    @endif
                                    @if($demoStoreThemeSetting['enable_quick_link2'] == 'on')
                                        <li><a href="{{$demoStoreThemeSetting['quick_link_url21']}}">{{$demoStoreThemeSetting['quick_link_name21']}}</a></li>
                                    @endif
                                    @if($demoStoreThemeSetting['enable_quick_link3'] == 'on')
                                        <li><a href="{{$demoStoreThemeSetting['quick_link_url31']}}">{{$demoStoreThemeSetting['quick_link_name31']}}</a></li>
                                    @endif
                                </ul>
                            </div>
                        @endif
                        @if($demoStoreThemeSetting['enable_footer'] == 'on')
                            <div class="footer-try  {{($demoStoreThemeSetting['enable_footer_note'] == 'on')?'delimiter-top mt-2':'' }}">
                                <ul class="nav justify-content-center justify-content-md-end mt-3 mt-md-0">
                                    @if(isset($demoStoreThemeSetting['email']))
                                        <li class="nav-item">
                                            <a class="nav-link" href="mailto:{{$demoStoreThemeSetting['email']}}" target="_blank">
                                                <i class="fa fa-envelope"></i>
                                            </a>
                                        </li>
                                    @endif
                                    @if(isset($demoStoreThemeSetting['whatsapp']))
                                        <li class="nav-item">
                                            <a class="nav-link" href="https://wa.me/{{$demoStoreThemeSetting['whatsapp']}}" target="_blank">
                                                <i class="fab fa-whatsapp"></i>
                                            </a>
                                        </li>
                                    @endif
                                    @if(isset($demoStoreThemeSetting['facebook']))
                                        <li class="nav-item">
                                            <a class="nav-link" href="{{$demoStoreThemeSetting['facebook']}}" target="_blank">
                                                <i class="fab fa-facebook"></i>
                                            </a>
                                        </li>
                                    @endif
                                    @if(isset($demoStoreThemeSetting['instagram']))
                                        <li class="nav-item">
                                            <a class="nav-link" href="{{$demoStoreThemeSetting['instagram']}}" target="_blank">
                                                <i class="fab fa-instagram"></i>
                                            </a>
                                        </li>
                                    @endif
                                    @if(isset($demoStoreThemeSetting['twitter']))
                                        <li class="nav-item">
                                            <a class="nav-link" href="{{$demoStoreThemeSetting['twitter']}}" target="_blank">
                                                <i class="fab fa-twitter"></i>
                                            </a>
                                        </li>
                                    @endif
                                    @if(isset($demoStoreThemeSetting['youtube']))
                                        <li class="nav-item">
                                            <a class="nav-link" href="{{$demoStoreThemeSetting['youtube']}}" target="_blank">
                                                <i class="fab fa-youtube"></i>
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </footer>
@endif

{!! $demoStoreThemeSetting['storejs'] !!}
<!-- Core JS - includes jquery, bootstrap, popper, in-view and sticky-kit -->
<script src="{{asset('assets/js/site.core.js')}}"></script>
<!-- notify -->
<script src="{{ asset('custom/libs/bootstrap-notify/bootstrap-notify.min.js')}}"></script>
<script src="{{asset('assets/js/bootstrap.min.3.3.5.js')}}"></script>
<script src="{{ asset('custom/libs/@fancyapps/fancybox/dist/jquery.fancybox.min.js')}}"></script>
<!-- Page JS -->
<script src="{{asset('assets/js/main.js')}}"></script>

<script src="{{asset('assets/libs/swiper/dist/js/swiper.min.js')}}"></script>
<!-- site JS -->
<script src="{{asset('assets/js/site.js')}}"></script>
<script type="text/javascript" src="{{ asset('custom/js/custom.js') }}"></script>

@stack('script-page')

{{--Search--}}
<script>
    $(document).ready(function () {
        $(document).on('click', '#search_icon', function () {

            //FETCH AND SEARCH
            function fetch_course_data(query = '') {
                $.ajax({
                    url: "{{ route('store.searchData',[$store->slug,'__query']) }}".replace('__query', query),
                    method: 'GET',
                    dataType: 'json',
                    success: function (data) {
                        $('#search_data_div').html(data.table_data);
                        $('#total_records').text(data.total_data);
                    }
                })
            }

            $(document).on('keyup', '#search_box', function () {
                var query = $(this).val();
                /*console.log(query);
                return false;*/
                if (query != '') {
                    fetch_course_data(query);
                } else {
                    $('#search_data_div').html('');
                }

            });
        });
    });
</script>

@if($message = Session::get('success'))
    <script>
        show_toastr('Success', '{!! $message !!}', 'success');
    </script>
@endif
@if($message = Session::get('error'))
    <script>
        show_toastr('Error', '{!! $message !!}', 'error');
    </script>
@endif
</body>
</html>

