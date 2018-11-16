@extends('email.html.layout')

@section('content')
<tr>
    <td class="wrapper">
        <table border="0" cellpadding="0" cellspacing="0">
            <tr>
                <td>
                    <h2>{{ __('email.invoice_renew_member_title') }}</h2>
                </td>
            </tr>
            <tr>
                <td>
                    @if($content['member']->gender == 'M') {{__('email.invoice_user_gender_man')}}
                    @else {{__('email.invoice_user_gender_female')}}  @endif
                    {{$content['member']->title_before}} {{$content['member']->name}} {{$content['member']->surname}} {{$content['member']->title_after}},<br>
                    <br>
                    {{ Lang::get('email.invoice_renew_member_text1') }}<br>
                    <br>
                    {{ Lang::get('email.invoice_renew_member_text2') }}<br>
                    <br>
                    {{$content['signature']->title_before}} {{$content['signature']->name}} {{$content['signature']->surname}} {{$content['signature']->title_after}}<br>
                    {{$content['signature']->job_position}}<br>
                    {{$content['signature']->phone}}<br>
                    {{$content['signature']->email}}<br>
                    {{ __('app.web') }}<br>
                </td>
            </tr>
            <tr>
                <td>
                    <br>
                    {{--<strong>Registračné údaje</strong><br>--}}
                    {{--<strong>Meno:</strong> {{ $content['reg_name'] }}<br>--}}
                    {{--<strong>Email:</strong> {{ $content['reg_email'] }}<br>--}}
                    {{--<br>--}}
                    {{--<strong>Info o udalosti</strong><br>--}}
                    {{--<strong>Miesto konania:</strong> {{ Lang::get('text_app.miesto') }}<br>--}}
                    {{--<strong>Adresa:</strong> {{ Lang::get('text_app.adresa') }}<br>--}}
                    {{--<strong>Dátum:</strong> {{ Lang::get('text_app.datum_konania') }}<br>--}}
                    {{--<strong>Email:</strong> <a href="mailto:{{ Lang::get('text_app.kontakt_email') }}">{{ Lang::get('text_app.kontakt_email') }}</a><br>--}}
                    {{--<strong>Telefón:</strong> <a href="tel:{{ Lang::get('text_app.kontakt_phone') }}">{{ Lang::get('text_app.kontakt_phone') }}</a> <br>--}}
                    {{--<br>--}}
                    {{--{!!  $content['text_footer'] !!}--}}
                    {{--<br>--}}
                </td>
            </tr>
        </table>
    </td>
</tr>
@endsection