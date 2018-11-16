@extends('email.text.layout-text')

@section('content')

@if($content['member']->gender == 'M') {{__('email.invoice_user_gender_man')}} @else {{__('email.invoice_user_gender_female')}} @endif{{$content['member']->name}},

{{ Lang::get('email.invoice_final_text1') }}

{{ Lang::get('email.invoice_final_text2') }}

{{ Lang::get('email.invoice_final_text3') }}

{{ Lang::get('email.invoice_final_text4') }}

{{ Lang::get('email.invoice_final_text5') }}

{{ Lang::get('email.invoice_final_text6') }}

{{ Lang::get('email.invoice_best_regards') }}<br>

{{$content['signature']->title_before}} {{$content['signature']->name}} {{$content['signature']->surname}} {{$content['signature']->title_after}}
{{$content['signature']->job_position}}
{{$content['signature']->phone}}
{{$content['signature']->email}}
{{ __('app.web') }}

@endsection
