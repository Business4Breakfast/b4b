@extends('email.text.layout-text')

@section('content')

{{ $content['subject'] }}

{{$content['text']}}

Link na detail:
{!! $content['url'] !!}


{{ __('app.web') }}

@endsection
