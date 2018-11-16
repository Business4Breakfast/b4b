@extends('email.html.layout')

@section('content')
<tr>
    <td class="wrapper_content">
        <table border="0" cellpadding="0" cellspacing="0" class="content_background">
            <tr>
                <td>
                    <h2>{{ $content['subject'] }}</h2>
                </td>
            </tr>
            <tr>
                <td>
                    Vážený člen,
                    <br>
                    <br>
                    @if($content['count'] == 1) {{ __('email.invoice_reminder_first') }}  @endif
                    @if($content['count'] == 2){{ __('email.invoice_reminder_second_1') }}
                    <br>
                    <br>
                    {{ __('email.invoice_reminder_second_2') }}@endif
                    @if($content['count'] == 3) {{ __('email.invoice_reminder_third_1') }} @endif
                    <br>
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

                </td>
            </tr>
        </table>
    </td>
</tr>
@endsection