@extends('email.text.layout-text')

@section('content')

Vážený člen

{{ Lang::get('email.invitation_title') }}

{{ __('email.invitation_1') }}
<br>
<br>
{{ __('email.invitation_2') }}


{{$content['signature']->title_before}} {{$content['signature']->name}} {{$content['signature']->surname}} {{$content['signature']->title_after}}
{{$content['signature']->job_position}}
{{$content['signature']->phone}}
{{$content['signature']->email}}
{{ __('app.web') }}

@endsection
