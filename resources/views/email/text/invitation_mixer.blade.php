@extends('email.text.layout-text')

@section('content')

Pozvánka na {{ strtolower($content['event']->title) }}

@if($content['recipient']->gender == 'M') {{__('email.invoice_user_gender_man')}}
@else {{__('email.invoice_user_gender_female')}}  @endif
{{$content['recipient']->name}} {{$content['recipient']->surname}},

@if( $content['event']->description)
{{ $content['event']->description }}
@endif

@if( $content['attendance']->description)
Správa od pozývateľa.
{{ $content['attendance']->description }}
@endif

Pozývajúci člen
{{$content['invite_person']->name}} {{$content['invite_person']->surname}}
{{$content['invite_person']->phone}}
{{$content['invite_person']->email}}

@if( $content['event']->type->description )
O udalosti.
{{ $content['event']->type->description }}
@endif

{{ __('email.invitation_1') }}

<a class="btn btn-primary" href="{!! $content['link_accept'] !!}" " target="_blank">Potvrdzujem svoju účasť</a>

<a class="btn btn-primary" href="{!! $content['link_apology'] !!}" " target="_blank">Ospravedlňujem svoju neúčasť</a>

{{ __('email.invitation_4') }}

Ak áno, máte príležitosť:
Kedy: {{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $content['event']->event_from)->format('d.m.Y  H:i') }} - {{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $content['event']->event_to)->format('H:i') }}
Kde: {{ $content['event']->host_name }}, {{ $content['event']->address_street }}, {{ $content['event']->address_city }}
Internet:{{ $content['event']->url }}
Čo Vás čaká: predstavenie konceptu BforB, informácia o klube
Cena: {{ $content['event']->price }} .- EUR (v cene je welcome drink a malé občerstvenie - ďalšia konzumácia podľa vlastného uváženia)


Manažér klubu: {{$content['signature']->title_before}} {{$content['signature']->name}} {{$content['signature']->surname}} {{$content['signature']->title_after}}
Telefón: {{$content['signature']->phone}}
Email: {{$content['signature']->email}}
                        {{ __('app.web') }}

                        {{ __('email.invitation_5') }}

                        {{ __('email.invitation_6') }}

<a class="btn btn-primary" href="{!! $content['link_refused'] !!}" target="_blank">Nechcem už dostávať pozvánky</a></td>

@endsection
