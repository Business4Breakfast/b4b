@extends('email.html.layout')

@section('content')
<tr>
    <td class="wrapper_content">
        <table border="0" cellpadding="0" cellspacing="0" class="content_background_yellow">
            <tr>
                <td>
                    <h2>{{ $content['subject'] }}</h2>
                </td>
            </tr>
            @if($content['user_to_send'])
            <tr>
                <td>
                    {!! $content['user_to_send'] !!}
                    <br>
                    <br>
                </td>
            </tr>
            @endif
            <tr>
                <td>
                     {!! $content['markdown_text'] !!}
                </td>
            </tr>
            @if($content['images_from_event'])
                @foreach($content['images_from_event'] as $img)
                    {{--@if(file_exists( asset('/images/event-images/') . '/'.$img->event_id .'/' . $img->image))--}}
                        <tr class="m-b-20">
                            <td>
                                <img src="{!! $message->embed( asset('/images/event-images/') . '/'.$img->event_id .'/' . $img->image ) !!}">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <br>
                            </td>
                        </tr>
                    {{--@endif--}}
                @endforeach
            @endif
            <tr>
                <td>
                    {!! nl2br($content['text'], false) !!}
                </td>
            </tr>
            <tr>
                <td>
                    @if($content['url'])
                        <table border="0" cellpadding="0" cellspacing="0" class="btn btn-purple">
                            <tbody>
                            <tr>
                                <td align="left">
                                    <table border="0" cellpadding="0" cellspacing="0">
                                        <tbody>
                                        <tr>
                                            <td><a class="btn btn-purple" href="{!! $content['url'] !!}" target="_blank">Chcem sa stať členom</a></td>
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
                    {{ __('app.web') }}<br>
                </td>
            </tr>
            <tr>
                <td>
                    <br>
                    <br>
                </td>
            </tr>
        </table>
    </td>
</tr>
@endsection