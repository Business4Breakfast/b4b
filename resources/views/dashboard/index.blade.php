@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">

        @role(['superadministrator', 'administrator'])
        <div class="row">
            <div class="col-lg-3 col-md-6">
                <div class="widget style1 navy-bg">
                    <div class="row">
                        <div class="col-xs-4">
                            <i class="fa fa-bank fa-5x"></i>
                        </div>
                        <div class="col-xs-8 text-right">
                            <span> Počet klubov </span>
                            <h2 class="font-bold">{{$club['count']}}</h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="widget style1 lazur-bg">
                    <div class="row">
                        <div class="col-xs-4">
                            <i class="fa fa-group fa-5x"></i>
                        </div>
                        <div class="col-xs-8 text-right">
                            <span> Počet členov </span>
                            <h2 class="font-bold">{{$user['count']}}</h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="widget style1 bg-danger">
                    <div class="row">
                        <div class="col-xs-4">
                            <i class="fa fa-trophy fa-5x"></i>
                        </div>
                        <div class="col-xs-8 text-right">
                            <span> Celkové píjmy </span>
                            <h2 class="font-bold">{{ number_format((float)$finance['invoice'], 0, ',', ' ')}}</h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="widget style1 yellow-bg">
                    <div class="row">
                        <div class="col-xs-4">
                            <i class="fa fa-coffee fa-5x"></i>
                        </div>
                        <div class="col-xs-8 text-right">
                            <span> Počet meetingov </span>
                            <h2 class="font-bold">{{$club['events']}}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endrole

        @if($data['function_statt'])
            @foreach($data['function_statt'] as $fs)
            <div class="row">
                <div class="col-lg-12">
                    <h3>Klub: {{$fs['club']}},  Funkcia: {{$fs['function']->display_name}} </h3>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="widget style1 navy-bg">
                        <div class="row">
                            <div class="col-xs-4">
                                <i class="fa fa-bank fa-5x"></i>
                            </div>
                            <div class="col-xs-8 text-right">
                                <span> Počet klubov </span>
                                <h2 class="font-bold">{{$club['count'] - 1}}</h2>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="widget style1 lazur-bg">
                        <div class="row">
                            <div class="col-xs-4">
                                <i class="fa fa-group fa-5x"></i>
                            </div>
                            <div class="col-xs-8 text-right">
                                <span> Počet členov </span>
                                <h2 class="font-bold">{{$user['count']}}</h2>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="widget style1 bg-warning">
                        <div class="row">
                            <div class="col-xs-4">
                                <i class="fa fa-user fa-5x"></i>
                            </div>
                            <div class="col-xs-8 text-right">
                                <span> Naši členovia </span>
                                <h2 class="font-bold">{{ number_format((float)$fs['club_members_count'], 0, ',', ' ')}}</h2>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="widget style1 bg-danger">
                        <div class="row">
                            <div class="col-xs-4">
                                <i class="fa fa-star fa-5x"></i>
                            </div>
                            <div class="col-xs-8 text-right">
                                <span> Počet nových členov </span>
                                <h2 class="font-bold">{{ number_format((float)$fs['new_members'], 0, ',', ' ')}}</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        @endrole

        {{--<div class="row animated fadeInDown">--}}
            {{--<div class="col-lg-12">--}}
                {{--<div class="ibox float-e-margins">--}}
                    {{--<div class="ibox-title">--}}
                        {{--<h5>Moje nadchádzajúce udalosti</h5>--}}
                    {{--</div>--}}
                    {{--<div class="ibox-content">--}}
                        {{--<p>--}}
                            {{--@foreach($data['earliest_events'] as $ee)--}}
                                {{--<a class="btn btn-success btn-facebook" href="{{route('events.listing.show', $ee->id)}}">--}}
                                    {{--<i class="fa fa-coffee"> </i> {{$ee->club->short_title}}--}}
                                    {{--{{ Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $ee->event_from)->format('d.m.y H:i') }}--}}
                                    {{--{{$ee->title}}--}}
                                {{--</a>--}}
                            {{--@endforeach--}}
                        {{--</p>--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</div>--}}
        {{--</div>--}}

        <div class="row animated fadeInDown">
            <div class="col-lg-7">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Kalendár udalostí</h5>
                    </div>
                    <div class="ibox-content">
                        <div id="calendar"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Najnovšie udalosti</h5>
                    </div>
                    <div class="ibox-content">
                        <p>
                        @if($data['earliest_events'])
                        @foreach($data['earliest_events'] as $ee)
                            <a class="btn btn-success btn-block" href="{{route('events.listing.show', $ee->id)}}">
                                <i class="fa fa-coffee"> </i> {{$ee->club->short_title}}
                                {{ Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $ee->event_from)->format('d.m.y H:i') }}
                                {{str_limit($ee->title, 30)}}
                            </a>
                        @endforeach
                        @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{--<div class="row m-t-lg">--}}
            {{--<div class="col-lg-6">--}}
                {{--<div class="ibox float-e-margins">--}}

                    {{--<div class="ibox-content">--}}
                        {{--<h2>TODO Small version</h2>--}}
                        {{--<small>This is example of small version of todo list</small>--}}
                        {{--<ul class="todo-list m-t small-list ui-sortable">--}}
                            {{--<li>--}}
                                {{--<span class="m-l-xs todo-completed">Buy a milk</span>--}}
                            {{--</li>--}}
                            {{--<li>--}}
                                {{--<a href="#" class="check-link"><i class="fa fa-check-square"></i> </a>--}}
                                {{--<span class="m-l-xs todo-completed">Go to shop and find some products.</span>--}}
                            {{--</li>--}}
                            {{--<li>--}}
                                {{--<a href="#" class="check-link"><i class="fa fa-square-o"></i> </a>--}}
                                {{--<span class="m-l-xs">Send documents to Mike</span>--}}
                                {{--<small class="label label-primary"><i class="fa fa-clock-o"></i> 1 mins</small>--}}
                            {{--</li>--}}
                            {{--<li>--}}
                                {{--<a href="#" class="check-link"><i class="fa fa-square-o"></i> </a>--}}
                                {{--<span class="m-l-xs">Go to the doctor dr Smith</span>--}}
                            {{--</li>--}}
                            {{--<li>--}}
                                {{--<a href="#" class="check-link"><i class="fa fa-square-o"></i> </a>--}}
                                {{--<span class="m-l-xs">Plan vacation</span>--}}
                            {{--</li>--}}
                        {{--</ul>--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</div>--}}


            {{--<div class="col-lg-6">--}}
                {{--<div>--}}
                    {{--<div class="row">--}}
                        {{--<div class="col-md-6">--}}
                            {{--<div class="ibox-content text-center">--}}


                            {{--</div>--}}
                        {{--</div>--}}
                        {{--<div class="col-md-6">--}}
                            {{--<div class="ibox-content">--}}
                                {{--<div>--}}
                                    {{--<div>--}}
                                        {{--<span>Memory</span>--}}
                                        {{--<small class="pull-right">10/200 GB</small>--}}
                                    {{--</div>--}}
                                    {{--<div class="progress progress-small">--}}
                                        {{--<div style="width: 60%;" class="progress-bar"></div>--}}
                                    {{--</div>--}}

                                    {{--<div>--}}
                                        {{--<span>Bandwidth</span>--}}
                                        {{--<small class="pull-right">20 GB</small>--}}
                                    {{--</div>--}}
                                    {{--<div class="progress progress-small">--}}
                                        {{--<div style="width: 50%;" class="progress-bar"></div>--}}
                                    {{--</div>--}}

                                    {{--<div>--}}
                                        {{--<span>Activity</span>--}}
                                        {{--<small class="pull-right">73%</small>--}}
                                    {{--</div>--}}
                                    {{--<div class="progress progress-small">--}}
                                        {{--<div style="width: 40%;" class="progress-bar"></div>--}}
                                    {{--</div>--}}

                                    {{--<div>--}}
                                        {{--<span>FTP</span>--}}
                                        {{--<small class="pull-right">400 GB</small>--}}
                                    {{--</div>--}}
                                    {{--<div class="progress progress-small">--}}
                                        {{--<div style="width: 20%;" class="progress-bar progress-bar-danger"></div>--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</div>--}}

                {{--</div>--}}
            {{--</div>--}}
        {{--</div>--}}

        <div class="row">
            <div class="col-lg-12">
                <div class="text-center m-t-lg">
                    <h1>
                        Welcome to Bussiness for Breakfast Slovakia
                    </h1>
                    <small>
                        The Best networking franchisor on The Universe.
                    </small>
                </div>
            </div>
        </div>
    </div>

    {{--<div class="row">--}}
        {{--<div class="col-lg-12">--}}
            {{--<div class="wrapper wrapper-content animated fadeInUp">--}}
                {{--<ul class="notes">--}}
                    {{--<li>--}}
                        {{--<div>--}}
                            {{--<small>14.2.2018</small>--}}
                            {{--<h4>Juraj toto treba urobiť</h4>--}}
                            {{--<p>The years, sometimes by accident, sometimes on purpose (injected humour and the like).</p>--}}
                            {{--<a href="#"><i class="fa fa-trash-o "></i></a>--}}
                        {{--</div>--}}
                    {{--</li>--}}
                    {{--<li>--}}
                        {{--<div>--}}
                            {{--<small>14.2.2018</small>--}}
                            {{--<h4>Marian aj toto treba urobiť </h4>--}}
                            {{--<p>The first line of Lorem Ipsum, "Lorem ipsum dolor sit amet..", comes from a line in section 1.10.32.</p>--}}
                            {{--<a href="#"><i class="fa fa-trash-o "></i></a>--}}
                        {{--</div>--}}
                    {{--</li>--}}
                    {{--<li>--}}
                        {{--<div>--}}
                            {{--<small>14.2.2018</small>--}}
                            {{--<h4>The standard chunk of Lorem</h4>--}}
                            {{--<p>Ipsum used since the 1500s is reproduced below for those interested.</p>--}}
                            {{--<a href="#"><i class="fa fa-trash-o "></i></a>--}}
                        {{--</div>--}}
                    {{--</li>--}}
                    {{--<li>--}}
                        {{--<div>--}}
                            {{--<small>14.2.2018</small>--}}
                            {{--<h4>The generated Lorem Ipsum </h4>--}}
                            {{--<p>The generated Lorem Ipsum is therefore always free from repetition, injected humour, or non-characteristic words etc.</p>--}}
                            {{--<a href="#"><i class="fa fa-trash-o "></i></a>--}}
                        {{--</div>--}}
                    {{--</li>--}}
                {{--</ul>--}}
            {{--</div>--}}
        {{--</div>--}}
    {{--</div>--}}


    <div id="calendarModal" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span> <span class="sr-only">close</span></button>
                    <h4 id="modalTitle" class="modal-title"></h4>
                </div>
                <div id="modalBody" class="modal-body">
text
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{__('form.cancel')}}</button>
                </div>
            </div>
        </div>
    </div>


@endsection


@section('css')

    <link rel="stylesheet" href="{!! asset('css/plugins/fullcalendar/fullcalendar.css') !!}" />
    <link rel="stylesheet" href="{!! asset('css/plugins/fullcalendar/fullcalendar.print.css') !!}"  media='print'/>

@endsection

@section('scripts')

    <!-- jQuery UI custom -->
    <script src="{!! asset('js/jquery-ui.custom.min.js') !!}"></script>

    <script src="{!! asset('js/plugins/fullcalendar/fullcalendar.min.js') !!}" type="text/javascript"></script>

    <script>

        $(document).ready(function(){

            $('.i-checks').iCheck({
                checkboxClass: 'icheckbox_square-green',
                radioClass: 'iradio_square-green'
            });

            /* initialize the external events
             -----------------------------------------------------------------*/

            $('#external-events div.external-event').each(function() {

                // store data so the calendar knows to render an event upon drop
                $(this).data('event', {
                    title: $.trim($(this).text()), // use the element's text as the event title
                    stick: true // maintain when user navigates (see docs on the renderEvent method)
                });

                // make the event draggable using jQuery UI
                $(this).draggable({
                    zIndex: 1111999,
                    revert: true,      // will cause the event to go back to its
                    revertDuration: 0  //  original position after the drag
                });

            });


            /* initialize the calendar
             -----------------------------------------------------------------*/
            var date = new Date();
            var d = date.getDate();
            var m = date.getMonth();
            var y = date.getFullYear();

            $('#calendar').fullCalendar({
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'month,agendaWeek,agendaDay'
                },
                editable: true,
                droppable: true, // this allows things to be dropped onto the calendar
                drop: function() {
                    // is the "remove after drop" checkbox checked?
                    if ($('#drop-remove').is(':checked')) {
                        // if so, remove the element from the "Draggable Events" list
                        $(this).remove();
                    }
                },

                events: {
                    url:'/ajax/function',
                    type: 'POST',
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    data: {
                        user_id: {!! Auth::user()->id !!},
                        action: 'get_calendar_data'
                    },

                    error: function() {
                        toastr.error('there was an error while fetching events!');
                    },

                    color: 'yellow',   // a non-ajax option
                    textColor: 'black' // a non-ajax option
                },

                eventClick:  function(event, jsEvent, view) {
                    $('#modalTitle').html(event.title);
                    $('#modalBody').html(event.description);
                    $('#eventUrl').attr('href',event.url);
                    $('#calendarModal').modal();
                }

            });

        });

    </script>

@endsection
