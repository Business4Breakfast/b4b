@extends('email.text.layout-text')

@section('content')

Vážený člen

{{ Lang::get('email.invoice_reminder_title') }}

@if($content['count'] == 1) {{ __('email.invoice_reminder_first') }}  @endif
@if($content['count'] == 2){{ __('email.invoice_reminder_second_1') }}

{{ __('email.invoice_reminder_second_2') }}@endif
@if($content['count'] == 3) {{ __('email.invoice_reminder_third_1') }} @endif


{{$content['signature']->title_before}} {{$content['signature']->name}} {{$content['signature']->surname}} {{$content['signature']->title_after}}
{{$content['signature']->job_position}}
{{$content['signature']->phone}}
{{$content['signature']->email}}
{{ __('app.web') }}

@endsection
