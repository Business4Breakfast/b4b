@extends('layouts.app_public')

@section('title', '')

@section('content')

    <div class="wrapper wrapper-content">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 col-lg-offset-2 col-sm-12">

                    <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="pull-left">
                                        <div class="img">
                                            <img alt="image" class="feed-photo img-responsive" src="{!! $event->image_thumb !!}" style="max-width: 100px;">
                                        </div>
                                    </div>
                                    <h3 class="font-bold">{{$event->title}}, {{ Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $event->event_from)->format('d.m.y H:i')}}</h3>
                                    <p class="font-bold">{{$event->club->title}}</p>
                                    <p class="font-bold">{{$recipient->full_name}}</p>
                                </div>
                            </div>
                        </div>
                        <div class="ibox-content">
                            <ul class="list-group clear-list m-t">
                                @if($attendance)
                                    @foreach($attendance as $a)
                                        <li class="list-group-item  @if( $loop->first) fist-item @endif">
                                        <span class="pull-right">
                                            <span class="label label-success">{{$a->status_name}}</span>
                                        </span>
                                            <h3>{{$a->name}} {{$a->surname}}</h3>
                                            Spoločnosť: {{$a->company}}, Odvetvie: {{$a->industry}}
                                            <br>Pozval: {{$a->invited_user}},
                                            @if($a->attend_user_event)
                                                <span class="text-danger"> Náš {{$a->user_status_name}}</span>
                                            @else
                                                <span class="text-info">{{$a->user_status_name}}</span>
                                            @endif
                                        </li>
                                    @endforeach
                                @endif
                            </ul>
                            <ul class="list-group clear-list m-t">
                                @if($activities->count() > 0)
                                    <li class="list-group-item ">
                                        <h3>Aktivita klubu</h3>
                                        @foreach($activities as $a)
                                            <p><b>{{$a->activity_name}}</b>: {{$a->full_name}}</p>
                                        @endforeach
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')

    <script>
        $(document).ready(function (){



        });
    </script>
@endsection