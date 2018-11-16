@extends('email.text.layout-text')

@section('content')

Pozvánka na {{ strtolower($content['event']->title) }}

@if($content['recipient']->gender == 'M') {{__('email.invoice_user_gender_man')}}
@else {{__('email.invoice_user_gender_female')}}  @endif
{{$content['recipient']->name}} {{$content['recipient']->surname}},

@if( $content['event']->description)
{{ $content['event']->description }}
@endif

@if( $content['event']->type->description )
O udalosti.
{{ $content['event']->type->description }}
@endif

Ak áno, máte príležitosť:
Kedy: {{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $content['event']->event_from)->format('d.m.Y  H:i') }} - {{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $content['event']->event_to)->format('H:i') }}
Kde: {{ $content['event']->host_name }}, {{ $content['event']->address_street }}, {{ $content['event']->address_city }}
Internet:{{ $content['event']->url }}
Cena: {{ $content['event']->price }} .- EUR

@endsection
