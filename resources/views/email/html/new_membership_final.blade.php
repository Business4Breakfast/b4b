@extends('email.html.layout')

@section('content')
<tr>
    <td class="wrapper">
        <table border="0" cellpadding="0" cellspacing="0">
            <tr>
                <td>
                    <h2>{{ __('email.invoice_final_title') }}</h2>
                </td>
            </tr>
            <tr>
                <td>
                    @if($content['member']->gender == 'M') {{__('email.invoice_user_gender_man')}}
                    @else {{__('email.invoice_user_gender_female')}}  @endif
                    {{$content['member']->title_before}} {{$content['member']->name}} {{$content['member']->surname}} {{$content['member']->title_after}},<br>
                    <br>
                    {{ Lang::get('email.invoice_final_text1') }}<br>
                    <br>
                    {{ Lang::get('email.invoice_final_text2') }}<br>
                    <br>
                    {{ Lang::get('email.invoice_final_text3') }}<br>
                    <br>
                    {{ Lang::get('email.invoice_final_text4') }}<br>
                    <br>
                    {{ Lang::get('email.invoice_final_text5') }}<br>
                    <br>
                    {{ Lang::get('email.invoice_final_text6') }}<br>
                    <br>
                    {{ Lang::get('email.invoice_best_regards') }}<br>
                    <br>
                    {{$content['signature']->title_before}} {{$content['signature']->name}} {{$content['signature']->surname}} {{$content['signature']->title_after}}<br>
                    {{$content['signature']->job_position}}<br>
                    {{$content['signature']->phone}}<br>
                    {{$content['signature']->email}}<br>
                    {{ __('app.web') }}<br>
                </td>
            </tr>
            {{--<tr>--}}
            {{--<td>--}}
            {{--{!! $content['qr_code'] !!}<br>--}}
            {{--<img src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->size(600)->generate( $content['qr_code'] )) !!} ">--}}
            {{--</td>--}}
            {{--</tr>--}}
            <tr>
                <td>
                    <br>

                </td>
            </tr>
        </table>
    </td>
</tr>
@endsection