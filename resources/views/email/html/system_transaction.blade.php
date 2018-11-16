@extends('email.html.layout')

@section('content')
<tr>
    <td class="wrapper">
        <table border="0" cellpadding="0" cellspacing="0">
            <tr>
                <td>
                    <h2>{{ $content['subject'] }}</h2>
                </td>
            </tr>
            <tr>
                <td>
                    {!! nl2br($content['text'], false) !!}
                <br>
                    @if($content['url'])
                        <table border="0" cellpadding="0" cellspacing="0" class="btn btn-green">
                            <tbody>
                            <tr>
                                <td align="left">
                                    <table border="0" cellpadding="0" cellspacing="0">
                                        <tbody>
                                        <tr>
                                            <td><a class="btn btn-primary" href="{!! $content['url'] !!}" target="_blank">Detail transakcie</a></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    @endif
                <br>
                <br>
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