<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>@yield('title') &dash; {{ config('app.name') }}</title>
    <link rel="icon" href="{{ asset(Storage::url('uploads/logo/favicon.png')) }}" type="image/png">
    <link rel="stylesheet" href="{{ asset('custom/libs/@fortawesome/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/site.css') }}">
</head>
@php
    $user = \Auth::user();
@endphp
<body>
@yield('content')

<script src="{{ asset('assets/js/site.core.js') }}"></script>
<script src="{{ asset('assets/js/site.js') }}"></script>
</body>
</html>
