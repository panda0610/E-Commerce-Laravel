@component('mail::layout')
{{-- Header --}}
@slot('header')
@component('mail::header', ['url' => config('app.url')])
{!! $mail_header !!}
@endcomponent
@endslot

{{-- Body --}}
{!! $content !!}

{{-- Subcopy --}}
@isset($subcopy)
@slot('subcopy')
@component('mail::subcopy')
{{ $subcopy }}
@endcomponent
@endslot
@endisset

{{-- Footer --}}
@slot('footer')
@component('mail::footer')
© {{ date('Y') }} {!! $mail_header !!}. @lang('All rights reserved.')
@endcomponent
@endslot
@endcomponent
