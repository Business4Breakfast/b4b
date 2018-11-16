@extends('email.html.layout')

@section('content')
<tr>
    <td class="wrapper_content">
        <table border="0" cellpadding="0" cellspacing="0" class="content_background">
            <tr>
                <td>
                    <img src="{!! $message->embed($content['image_event']) !!}">
                </td>
            </tr>
            <tr class="m-t-20">
                <td>
                    <br>
                    <h2>Pozvánka na {{ strtolower($content['event']->title) }}</h2>
                    <h2>Kedy: {{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $content['event']->event_from)->format('d.m.Y  H:i') }} -
                    {{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $content['event']->event_to)->format('H:i') }}</h2>
                </td>
            </tr>
            <tr>
                <td>
                    @if($content['recipient']->gender == 'M') {{__('email.invoice_user_gender_man')}}
                    @else {{__('email.invoice_user_gender_female')}}  @endif
                        <strong>{{$content['recipient']->name}} {{$content['recipient']->surname}}</strong>,
                    <br>
                    <br>
                </td>
            </tr>
            @if( $content['event']->description)
                <tr>
                    <td>
                        {{ $content['event']->description }}
                        <br>
                        <br>
                    </td>
                </tr>
            @endif
            @if( $content['attendance']->description)
                <tr>
                    <td>
                        <strong>Správa od pozývateľa.</strong><br>
                        {{ $content['attendance']->description }}
                        <br>
                        <br>
                    </td>
                </tr>
            @endif
            {{--<tr>--}}
                {{--<td>--}}
                    {{--<br>--}}
                    {{--<strong>Pozývajúci člen.</strong><br>--}}
                    {{--{{$content['invite_person']->name}} {{$content['invite_person']->surname}}<br>--}}
                    {{--{{$content['invite_person']->phone}}<br>--}}
                    {{--{{$content['invite_person']->email}}<br>--}}
                    {{--<br>--}}
                {{--</td>--}}
            {{--</tr>--}}
            @if( $content['event']->type->description )
                <tr>
                    <td>
                        <strong>O udalosti.</strong>
                        <br>
                        {{ $content['event']->type->description }}
                        <br>
                        <br>
                    </td>
                </tr>
            @endif
            @if( $content['link_info_detail'])
                <tr>
                    <td>
                        <table border="0" cellpadding="0" cellspacing="0" class="btn btn-primary">
                            <tbody>
                            <tr>
                                <td align="left">
                                    <table border="0" cellpadding="0" cellspacing="0">
                                        <tbody>
                                        <tr>
                                            <td><a class="btn btn-primary" href="{!! $content['link_info_detail'] !!}" target="_blank">Potvrdení účastníci udalosti</a></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            @endif
            <tr>
                <td>
                    <strong>O BFORB</strong>
                    <br>
                    {{ __('email.invitation_mixer_2') }}
                    <br />
                    <br>
                </td>
            </tr>
            <tr>
                <td>
                    <table border="0" cellpadding="0" cellspacing="0" class="btn btn-green">
                        <tbody>
                        <tr>
                            <td align="left">
                                <table border="0" cellpadding="0" cellspacing="0">
                                    <tbody>
                                    <tr>
                                        <td><a class="btn btn-primary" href="{!! $content['link_accept'] !!}" " target="_blank">Potvrdzujem svoju účasť</a></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <table border="0" cellpadding="0" cellspacing="0" class="btn btn-red">
                        <tbody>
                        <tr>
                            <td align="left">
                                <table border="0" cellpadding="0" cellspacing="0">
                                    <tbody>
                                    <tr>
                                        <td><a class="btn btn-primary" href="{!! $content['link_apology'] !!}" " target="_blank">Ospravedlňujem svoju neúčasť</a></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <strong>{{ __('email.invitation_mixer_1') }}</strong><br>
                </td>
            </tr>
            <tr>
                <td>
                    <br>
                    <strong>Kedy:</strong> {{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $content['event']->event_from)->format('d.m.Y  H:i') }} -
                                            {{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $content['event']->event_to)->format('H:i') }}<br>
                    <strong>Kde:</strong> {{ $content['event']->host_name }}, {{ $content['event']->address_street }}, {{ $content['event']->address_city }}<br>
                    <strong>Internet:</strong> {{ $content['event']->url }}<br>
                    <strong>Cena: </strong> {{ $content['event']->price }} .- EUR (v cene je welcome drink a malé občerstvenie - ďalšia konzumácia podľa vlastného uváženia)<br>
                </td>
            </tr>
            <tr>
                <td>
                    <br>
                    <strong>Manažér klubu:</strong> {{$content['signature']->title_before}} {{$content['signature']->name}} {{$content['signature']->surname}} {{$content['signature']->title_after}}<br>
                    <strong>Telefón:</strong> {{$content['signature']->phone}}<br>
                    <strong>Email:</strong> {{$content['signature']->email}}<br>
                    {{ __('app.web') }}<br>
                    <br>
                    <br>
                </td>
            </tr>
            @if($content['event']->address_description)
                <tr>
                    <td>
                        <br>
                        <strong>Ako sa k nám dostanete:</strong> {{$content['event']->address_description}}<br>
                        <br>
                    </td>
                </tr>
            @endif
            {{--<tr>--}}
                {{--<td>--}}
                    {{--<img src="data:image/png;base64,{{base64_encode(file_get_contents( $content['map'] ))}}" alt="">--}}
                {{--</td>--}}
            {{--</tr>--}}
            <tr>
                <td>
                    <img src="{!! $message->embed($content['image_club']) !!}">
                </td>
            </tr>
            <tr>
                <td>
                    <br>
                    <br>
                    {{ __('email.invitation_5') }}
                    <br />
                    {{ __('email.invitation_6') }}
                    <br>
                    <br>
                </td>
            </tr>
            <tr>
                <td>
                    <table border="0" cellpadding="0" cellspacing="0" class="btn btn-primary">
                        <tbody>
                        <tr>
                            <td align="left">
                                <table border="0" cellpadding="0" cellspacing="0">
                                    <tbody>
                                    <tr>
                                        <td><a class="btn btn-primary" href="{!! $content['link_refused'] !!}" target="_blank">Nechcem už dostávať pozvánky</a></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>
    </td>
</tr>
@endsection