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
            {{--zrusenie defaultnych ctextov--}}
            @if(!$content['event']->email_text_custom)
            <tr>
                <td>
                    @if($content['event']->event_type == 5)
                        {{ __('email.invitation_type_5_1') }}
                        <br>
                        <br>
                        {{ __('email.invitation_type_5_2') }}
                        <br>
                        <br>
                        {{ __('email.invitation_type_5_3') }}
                        <br>
                        <br>
                        {{ __('email.invitation_type_5_4') }}
                        <br>
                        <br>
                    @elseif($content['event']->event_type == 6)
                        {{ __('email.invitation_type_6_1') }}
                        <br>
                        <br>
                        {{ __('email.invitation_type_6_2') }}
                        <br>
                        <br>
                        {{ __('email.invitation_type_6_3') }}
                        <br>
                        <br>
                        {{ __('email.invitation_type_6_4') }}
                        <br>
                        <br>
                        {{ __('email.invitation_type_6_5') }}
                        <br>
                        <br>
                    @else
                        {{ __('email.invitation_member_1') }}
                        <br>
                        <br>
                        @if($content['event']->event_type == 4)
                            {{ __('email.invitation_member_lunch_2') }}
                        @else
                            {{ __('email.invitation_member_2') }}
                        @endif
                        <br>
                        <br>
                        {{ __('email.invitation_member_3') }}
                        <br>
                        <br>
                        {{ __('email.invitation_member_4') }}
                        <br>
                        <br>
                    @endif
                </td>
            </tr>
            @endif
            @if( $content['event']->description)
                <tr>
                    <td>
                        <br>
                        <strong>Informácia o udalosti.</strong>
                        <br>
                        {!! nl2br($content['event']->description) !!}
                        <br>
                        <br>
                    </td>
                </tr>
            @endif
            @if( $content['attendance']->description)
                <tr>
                    <td>
                        <br>
                        <strong>Správa od pozývajúceho člena.</strong>
                        {{ $content['attendance']->description }}
                        <br>
                        <br>
                    </td>
                </tr>
            @endif
            @if( $content['event']->type->description )
                <tr>
                    <td>
                        <strong>O udalosti.</strong><br>
                        {{ $content['event']->type->description }}
                        <br>
                        <br>
                    </td>
                </tr>
            @endif
            @if( $content['activity'])
                <tr>
                    <td>
                        <br>
                        <strong>Aktivity udalosti.</strong>
                        <br>
                        @if(count($content['activity']) > 0 )
                            @foreach($content['activity'] as $al)
                                <strong>{{$al->activity }}:</strong> {{$al->name }} {{$al->surname}}<br>
                            @endforeach
                        @endif
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
                    <strong>O BFORB</strong><br>
                    {{ __('email.invitation_1') }}
                    <br>
                    <br>
                </td>
            </tr>
            <tr>
                <td>
                    @if(!in_array($content['event']->event_type, [1,4]))

                        @if($content['event']->type->btn_confirm_attend == 1)
                        <table border="0" cellpadding="0" cellspacing="0" class="btn btn-green">
                            <tbody>
                            <tr>
                                <td align="left">
                                    <table border="0" cellpadding="0" cellspacing="0">
                                        <tbody>
                                        <tr>
                                            <td><a class="btn btn-primary" href="{!! $content['link_accept'] !!}" target="_blank">Potvrdzujem svoju účasť</a></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        @endif

                    @endif
                    @if($content['event']->type->btn_refused_attend == 1)
                        <table border="0" cellpadding="0" cellspacing="0" class="btn btn-red">
                        <tbody>
                        <tr>
                            <td align="left">
                                <table border="0" cellpadding="0" cellspacing="0">
                                    <tbody>
                                    <tr>
                                        <td><a class="btn btn-primary" href="{!! $content['link_apology'] !!}" target="_blank">Ospravedlňujem svoju neúčasť</a></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    @endif
                    @if($content['event']->type->btn_invite_guest == 1)
                        <table border="0" cellpadding="0" cellspacing="0" class="btn btn-orange">
                            <tbody>
                            <tr>
                                <td align="left">
                                    <table border="0" cellpadding="0" cellspacing="0">
                                        <tbody>
                                        <tr>
                                            <td><a class="btn btn-orange" href="{!! $content['link_invite_guest'] !!}" target="_blank">Chcem pozvať hosťa</a></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    @endif
                </td>
            </tr>
            <tr>
                <td>
                    <br>
                    <strong>Informácie o čase a mieste:</strong><br>
                    <strong>Kedy:</strong> {{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $content['event']->event_from)->format('d.m.Y  H:i') }} -
                                            {{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $content['event']->event_to)->format('H:i') }}<br>
                    <strong>Kde:</strong> {{ $content['event']->host_name }}, {{ $content['event']->address_street }}, {{ $content['event']->address_city }}<br>
                    <strong>Internet:</strong> {{ $content['event']->url }}<br>
                    <strong>Čo Vás čaká:</strong> predstavenie konceptu BforB, informácia o klube<br>
                    <strong>Čo so sebou:</strong> {{ $content['event']->price }} .- EUR ako príspevok na občerstvenie<br>
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
            <tr>
                <td>
                    <img src="{!! $message->embed($content['image_club']) !!}">
                </td>
            </tr>
            <tr>
                <td>
                    &nbsp;
                </td>
            </tr>
        </table>
    </td>
</tr>
@endsection