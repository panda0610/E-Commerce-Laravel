<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>
        @yield('page-title') -

        @if(trim($__env->yieldContent('page-title')) && Auth::user()->type == 'admin')
            {{ config('app.name', 'Taskly') }}
        @else
            {{ isset($currantWorkspace->company) && $currantWorkspace->company != '' ? $currantWorkspace->company : config('app.name', 'Taskly') }}
        @endif
    </title>

    <link rel="shortcut icon" href="{{asset(Storage::url('logo/favicon.png'))}}">

    <!-- General CSS Files -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="{{ asset('assets/css/iziToast.min.css') }}">

    <!-- Template CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/components.css')}}">
    <link rel="stylesheet" href="{{ asset('assets/css/icons.min.css')}}">
    <link href="{{ asset('assets/css/easy-autocomplete.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
    <link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css"/>
    @stack('style')
</head>

<body>

<!-- Begin page -->
<div>

    <script>
        var dataTableLang = {
            paginate: {previous: "<i class='fas fa-angle-left'>", next: "<i class='fas fa-angle-right'>"},
            lengthMenu: "{{__('Show')}} _MENU_ {{__('entries')}}",
            zeroRecords: "{{__('No data available in table.')}}",
            info: "{{__('Showing')}} _START_ {{__('to')}} _END_ {{__('of')}} _TOTAL_ {{__('entries')}}",
            infoEmpty: "{{ __('Showing 0 to 0 of 0 entries') }}",
            infoFiltered:   "{{ __('(filtered from _MAX_ total entries)') }}",
            search: "{{__('Search:')}}",
            thousands:",",
            loadingRecords: "{{ __('Loading...') }}",
            processing: "{{ __('Processing...') }}"
        }
    </script>

    <div id="app">
        <div class="main-wrapper">
            <div class="navbar-bg"></div>
            <nav class="navbar navbar-expand-lg main-navbar">
                @include('partials.topnav')
            </nav>
            <div class="main-sidebar">
                @include('partials.sidebar')
            </div>

            <!-- Main Content -->
            <div class="main-content">
                @yield('content')
            </div>
            <footer class="main-footer">
                @include('partials.footer')
            </footer>
        </div>
    </div>
</div>

<div id="commanModel" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modelCommanModelLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content ">
            <div class="modal-header">
                <h4 class="modal-title" id="modelCommanModelLabel"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body"></div>
        </div>
    </div>
</div>

@if(Auth::user()->type != 'admin')
    <div id="modelCreateWorkspace" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modelCreateWorkspaceLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modelCreateWorkspaceLabel">{{ __('Create Your Workspace') }}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <form class="pl-3 pr-3" method="post" action="{{ route('add_workspace') }}">
                        @csrf
                        <div class="form-group">
                            <label for="workspacename">{{ __('Name') }}</label>
                            <input class="form-control" type="text" id="workspacename" name="name" required="" placeholder="{{ __('Workspace Name') }}">
                        </div>

                        <div class="form-group">
                            <button class="btn btn-primary" type="submit">{{ __('Create Workspace') }}</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
@endif

@php
    \App::setLocale(env('DEFAULT_LANG'));
    $currantLang = 'en';
@endphp

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.nicescroll/3.7.6/jquery.nicescroll.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment-with-locales.min.js"></script>
<script>
    moment.locale('{{$currantLang}}');
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>


@if($currantLang != '' && $currantLang != 'en')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.9.2/i18n/jquery.ui.datepicker-{{$currantLang}}.min.js"></script>
    <script>$.datepicker.setDefaults($.datepicker.regional['{{$currantLang}}']);</script>
@endif
@if(env('CHAT_MODULE') == 'yes' && isset($currantWorkspace) && $currantWorkspace)
    @auth('web')
    {{-- Pusher JS--}}
    <script src="https://js.pusher.com/5.0/pusher.min.js"></script>
    <script>
        $(document).ready(function () {
            pushNotification('{{ Auth::id() }}');
        });

        function pushNotification(id) {

            // ajax setup form csrf token
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Enable pusher logging - don't include this in production
            Pusher.logToConsole = false;

            var pusher = new Pusher('{{env('PUSHER_APP_KEY')}}', {
                cluster: '{{env('PUSHER_APP_CLUSTER')}}',
                forceTLS: true
            });

            var channel = pusher.subscribe('{{$currantWorkspace->slug}}');
            channel.bind('notification', function (data) {

                if (id == data.user_id) {
                    $(".notification-toggle").addClass('beep');
                    $(".notification-dropdown .dropdown-list-icons").prepend(data.html);
                }
            });
            channel.bind('chat', function (data) {
                if (id == data.to) {
                    getChat();
                }
            });
        }

        function getChat() {
            $.ajax({
                url: '{{route('message.data',$currantWorkspace->slug)}}',
                type: "get",
                cache: false,
                success: function (data) {
                    if (data.length != 0) {
                        $(".message-toggle").addClass('beep');
                        $(".dropdown-list-message").html(data);
                        LetterAvatar.transform();
                    }
                }
            })
        }

        getChat();

        $(document).on("click", ".mark_all_as_read", function () {
            $.ajax({
                url: '{{route('notification.seen',$currantWorkspace->slug)}}',
                type: "get",
                cache: false,
                success: function (data) {
                    $('.notification-dropdown .dropdown-list-icons').html('');
                    $(".notification-toggle").removeClass('beep');
                }
            })
        });
        $(document).on("click", ".mark_all_as_read_message", function () {
            $.ajax({
                url: '{{route('message.seen',$currantWorkspace->slug)}}',
                type: "get",
                cache: false,
                success: function (data) {
                    $('.dropdown-list-message').html('');
                    $(".message-toggle").removeClass('beep');
                }
            })
        });
    </script>
    {{-- End  Pusher JS--}}
    @endauth
@endif

<script src="{{ asset('assets/js/iziToast.min.js') }}"></script>
<script src="{{ asset('assets/js/stisla.js') }}"></script>
<script src="{{ asset('assets/js/scripts.js') }}"></script>
<script src="{{ asset('assets/js/scrollreveal.min.js') }}"></script>
<script>var userID = "{{ Auth::id() }}";</script>
<script src="{{ asset('custom/js/custom.js') }}"></script>
<script>
    var calender_header = {
        today: "{{__('today')}}",
        month: '{{__('month')}}',
        week: '{{__('week')}}',
        day: '{{__('day')}}',
        list: '{{__('list')}}'
    };
</script>

@if(isset($currantWorkspace) && $currantWorkspace)
    <script src="{{ asset('assets/js/jquery.easy-autocomplete.min.js') }}"></script>
    <script>
        var options = {
            url: function (phrase) {
                return "@auth('web'){{route('search.json',$currantWorkspace->slug)}}@elseauth{{route('client.search.json',$currantWorkspace->slug)}}@endauth/" + phrase;
            },
            categories: [
                {
                    listLocation: "Projects",
                    header: "{{ __('Projects') }}"
                },
                {
                    listLocation: "Tasks",
                    header: "{{ __('Tasks') }}"
                }
            ],
            getValue: "text",
            template: {
                type: "links",
                fields: {
                    link: "link"
                }
            }
        };
        $(".search-element input").easyAutocomplete(options);
    </script>
@endif
@stack('scripts')
@if ($message = Session::get('success'))
    <script>toastr('{{__('Success')}}', '{!! $message !!}', 'success')</script>
@endif

@if ($message = Session::get('error'))
    <script>toastr('{{__('Error')}}', '{!! $message !!}', 'error')</script>
@endif

@if ($message = Session::get('info'))
    <script>toastr('{{__('Info')}}', '{!! $message !!}', 'info')</script>
@endif
@if ($message = Session::get('warning'))
    <script>toastr('{{__('Warning')}}', '{!! $message !!}', 'warning')</script>
@endif
</body>

</html>
