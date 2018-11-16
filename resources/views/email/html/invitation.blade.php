@extends('email.html.layout')

@section('content')
<tr>
    <td class="wrapper_content">
        <table border="0" cellpadding="0" cellspacing="0" class="content_background">
            {{--<tr>--}}
                {{--@if( $content['preview'] == false )--}}
                    {{--<td>--}}
                        {{--<img src="{!! $message->embed($content['image_event']) !!}">--}}
                    {{--</td>--}}
                {{--@else--}}
                    {{--<td>--}}
                        {{--<img src="">--}}
                    {{--</td>--}}
                {{--@endif--}}
            {{--</tr>--}}
            @if($content['event']->image)
                <tr>
                    @if( $content['preview'] == false )
                        <td>
                            <img src="{!! $message->embed($content['image_event']) !!}">
                        </td>
                    @else
                        <td>
                            <img src="{!! $content['image_event'] !!}">
                        </td>
                    @endif
                </tr>
            @endif
            <tr class="m-t-20">
                <td>
                    <h2>Pozvánka na {{ strtolower($content['event']->title) }} ({{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $content['event']->event_from)->format('d.m.y  H:i') }})</h2>
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
                                        <td><a class="btn btn-primary" href="{!! $content['link_accept'] !!}" target="_blank">Potvrdzujem svoju účasť</a></td>
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
                    <strong>{{ __('email.invitation_4') }}</strong><br>
                </td>
            </tr>
            <tr>
                <td>
                    <br>
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
                        {{__('email.invitation_type_5_1')}}
                        <br>
                        <br>
                        {{__('email.invitation_type_5_2')}}
                        <br>
                        <br>
                        {{__('email.invitation_type_5_3')}}
                        <br>
                        <br>
                        {{__('email.invitation_type_5_4')}}
                        <br>
                        <br>
                    @elseif($content['event']->event_type == 6)
                        {{__('email.invitation_type_6_1')}}
                        <br>
                        <br>
                        {{__('email.invitation_type_6_2')}}
                        <br>
                        <br>
                        {{__('email.invitation_type_6_3')}}
                        <br>
                        <br>
                        {{__('email.invitation_type_6_4')}}
                        <br>
                        <br>
                        {{__('email.invitation_type_6_5')}}
                        <br>
                        <br>
                    @else
                        {{__('email.invitation_guest_1')}}
                        <br>
                        <br>
                        @if($content['event']->event_type == 4)
                            {{__('email.invitation_guest_lunch_2')}}
                        @else
                            {{__('email.invitation_guest_2') }}
                        @endif
                        <br>
                        <br>
                        {{__('email.invitation_guest_3')}}
                        <br>
                        <br>
                        @if($content['event']->event_type == 4)
                            {{__('email.invitation_guest_lunch_4') }}
                        @else
                            {{__('email.invitation_guest_4')}}
                        @endif
                        <br>
                        <br>
                    @endif
                </td>
            </tr>
            @endif
            @if( $content['event']->description)
                <tr>
                    <td>
                        {!! nl2br($content['event']->description, false) !!}
                        <br>
                    </td>
                </tr>
            @endif
            @if( $content['preview'] == false )
                @if( $content['attendance']->description)
                    <tr>
                        <td>
                            <strong>Správa od pozývateľa.</strong><br>
                            {{ $content['attendance']->description }}
                            <br>
                        </td>
                    </tr>
                @endif
            @endif
            <tr>
                <td>
                    <br>
                    <strong>Pozývajúci člen.</strong><br>
                    {{$content['invite_person']->name}} {{$content['invite_person']->surname}}<br>
                    {{$content['invite_person']->phone}}<br>
                    {{$content['invite_person']->email}}<br>
                    <br>
                </td>
            </tr>
            @if( $content['event']->type->description )
                <tr>
                    <td>
                        <br>
                        <strong>O udalosti.</strong>
                        <br>
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
                    <strong>O BFORB</strong>
                    <br>
                    {{ __('email.invitation_1') }}
                    <br />
                    <br>
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
                                        <td><a class="btn btn-green" href="{!! $content['link_accept'] !!}" target="_blank">Potvrdzujem svoju účasť</a></td>
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
                    <strong>{{ __('email.invitation_4') }}</strong><br>
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
                    <br>
                    <strong>Ak áno, máte príležitosť:</strong><br>
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
            {{--zrusenie obrazku clubu--}}
            @if(!$content['event']->email_image_club)
            <tr>
                @if( $content['preview'] == false )
                    <td>
                        <img src="{!! $message->embed($content['image_club']) !!}">
                    </td>
                @else
                    <td>
                        <img src="{!! $content['image_club'] !!}">
                    </td>
                @endif
            </tr>
            @endif
            <tr>
                <td>
                    <br>
                    <br>
                    <strong>{{ __('email.invitation_3') }}</strong><br>
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
                    @if($content['event']->type->btn_deleted_guest == 1)
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
                    @endif
                </td>
            </tr>
        </table>
    </td>
</tr>
@endsection