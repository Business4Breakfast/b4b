@extends('email.html.layout')

@section('content')
<tr>
    <td class="wrapper_content">
        <table border="0" cellpadding="0" cellspacing="0" class="content_background">
            <tr class="m-t-20">
                <td>
                    <br>
                    <h2>Pozvánka na {{ strtolower($content['event']->title) }}</h2>
                </td>
            </tr>
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
            <tr>
                <td>
                    <br>
                    <br>
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
                        {!!  nl2br($content['event']->description,false) !!}
                        <br>
                        <br>
                    </td>
                </tr>
            @endif
            @if( $content['event']->type->description )
                <tr>
                    <td>
                        <strong>O udalosti.</strong>
                        <br>
                        {{  $content['event']->type->description }}
                        <br>
                        <br>
                    </td>
                </tr>
            @endif

            @if($content['link_buy_ticket'])
            <tr>
                <td>
                    <table border="0" cellpadding="0" cellspacing="0" class="btn btn-orange">
                        <tbody>
                        <tr>
                            <td align="left">
                                <table border="0" cellpadding="0" cellspacing="0">
                                    <tbody>
                                    <tr>
                                        <td><a class="btn btn-orange" href="{{$content['link_buy_ticket']}}" target="_blank">Kúpiť lístok na udalosť</a></td>
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
                    <br>
                    <strong>Kedy:</strong> {{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $content['event']->event_from)->format('d.m.Y  H:i') }} -
                                            {{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $content['event']->event_to)->format('H:i') }}<br>
                    <strong>Kde:</strong> {{ $content['event']->host_name }}, {{ $content['event']->address_street }}, {{ $content['event']->address_city }}<br>
                    <strong>Internet:</strong> {{ $content['event']->url }}<br>
                    <strong>Cena: </strong> {{ $content['event']->price }} .- <br>
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
                    {{--<img src="data:image/png;base64,{!! base64_encode(file_get_contents( $content['map'] ))!!}" alt="">--}}
                {{--</td>--}}
            {{--</tr>--}}
        </table>
    </td>
</tr>
@endsection