@extends('layouts.app')

@section('title', 'Štatistické informácie člena')

@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">

        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content p-md">

                        <div class="row m-b-lg m-t-lg">
                            <div class="col-md-6">
                                <div class="profile-image">
                                    <img src="{!! asset( $user->image_thumb ) !!}" class="img-circle circle-border m-b-md" alt="profile">
                                </div>
                                <div class="profile-info">
                                    <div class="">
                                        <div>
                                            <h2 class="no-margins">
                                                {{$user->title_before}} {{$user->full_name}} {{$user->title_after}}
                                            </h2>
                                            <h5>{{$user->job_position}} </h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <table class="table small m-b-xs">
                                    <tbody>
                                    <tr>
                                        <td>
                                            <strong>{{$stats_total['reference_evidence_from']}}</strong> Odovzdané ústrižky
                                        </td>
                                        <td>
                                            <strong>{{$stats_total['reference_evidence_to']}} </strong> Prijaté ustrižky
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <strong>{{$stats_total['invited_guest_total']}}</strong> Vytvorený hostia
                                        </td>
                                        <td>
                                            <strong>{{$stats_total['invited_guest_attend_total']}}</strong> Pozvaní hostia
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-3">
                                <small>Celkový obchod</small>
                                <h4 class="no-margins"> {{$stats_total['reference_evidence_price']}} EUR</h4>
                                <small>Celkovo prijal obchod</small>
                                <h4 class="no-margins"> {{$stats_total['reference_from_evidence_price']}} EUR</h4>
                                <small>Celkový odovzdal obchod</small>
                                <h4 class="no-margins"> {{$stats_total['reference_to_evidence_price']}} EUR</h4>
                                <div id="sparkline1"><canvas style="display: inline-block; width: 269px; height: 50px; vertical-align: top;" width="269" height="50"></canvas></div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-lg-12">

                @include('components.validation')

                <div class="tabs-container m-b-lg">
                    <ul class="nav nav-tabs">
                        <li class="active"><a data-toggle="tab" href="#tab-1" aria-expanded="false"> Karta člena</a></li>
                        <li class=""><a data-toggle="tab" href="#tab-2" aria-expanded="false"> Štatistika</a></li>
                        <li class=""><a data-toggle="tab" href="#tab-3" aria-expanded="false"> Pozvaní hostia</a></li>
                        <li class=""><a data-toggle="tab" href="#tab-4" aria-expanded="false"> Udalosti</a></li>
                        <li class=""><a data-toggle="tab" href="#tab-5" aria-expanded="false"> Všetky udalosti</a></li>
                        <li class=""><a data-toggle="tab" href="#tab-6" aria-expanded="false"> Raňajky navštevnosť</a></li>


                    </ul>
                    <div class="tab-content">
                        <div id="tab-1" class="tab-pane active">
                            <div class="panel-body">

                                <form id="user_form" class="form-horizontal" method="POST" action="{{ route('setting.user.update', ['id' =>  $user->id]) }}" enctype="multipart/form-data">
                                    {{ csrf_field() }}
                                    {{ method_field('PUT') }}

                                    <div class="form-group {{ $errors->has('gender') ? ' has-error' : '' }}">
                                        <label class="col-sm-4 control-label">Pohlavie</label>
                                        <div class="col-sm-5 inline">
                                            @if($user->gender == 'M')
                                                <p class="form-control-static">Muž</p>
                                            @else
                                                <p class="form-control-static">Žena</p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="form-group{{ $errors->has('title_before') ? ' has-error' : '' }}">
                                        <label for="title_before" class="col-md-4 control-label">Titul pred menom</label>
                                        <div class="col-md-2">
                                            <p class="form-control-static">{{ $user->title_before }}</p>
                                        </div>
                                    </div>
                                    <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                        <label for="name" class="col-md-4 control-label">Meno</label>
                                        <div class="col-md-4">
                                            <p class="form-control-static">{{ $user->name }}</p>
                                        </div>
                                    </div>
                                    <div class="form-group{{ $errors->has('surname') ? ' has-error' : '' }}">
                                        <label for="surname" class="col-md-4 control-label">Priezvisko</label>
                                        <div class="col-md-4">
                                            <p class="form-control-static">{{ $user->surname }}</p>
                                        </div>
                                    </div>
                                    <div class="form-group{{ $errors->has('title_after') ? ' has-error' : '' }}">
                                        <label for="title_after" class="col-md-4 control-label">Titul za menom</label>
                                        <div class="col-md-2">
                                            <p class="form-control-static">{{ $user->title_after }}</p>
                                        </div>
                                    </div>
                                    <div class="form-group{{ $errors->has('job_position') ? ' has-error' : '' }}">
                                        <label for="title_after" class="col-md-4 control-label">Pracovná pozícia</label>
                                        <div class="col-md-4">
                                            <p class="form-control-static">{{ $user->job_position }}</p>
                                        </div>
                                    </div>
                                    <div class="form-group{{ $errors->has('industry') ? ' has-error' : '' }}">
                                        <label for="industry" class="col-md-4 control-label">Typ podnikania</label>
                                        <div class="col-md-4">
                                            @foreach($industry as $in)
                                                @if($in->id == $user->industry_id)
                                                    <p class="form-control-static">{{$in->name}}</p>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="form-group{{ $errors->has('birthday') ? ' has-error' : '' }}">
                                        <label for="title_after" class="col-md-4 control-label">Dátum narodenia</label>
                                        <div class="col-md-2">
                                                <p class="form-control-static"><i class="fa fa-calendar"></i> {{Carbon\Carbon::createFromFormat('Y-m-d', $user->birthday)->format('d.m.Y')}}</p>
                                        </div>
                                    </div>

                                    <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                        <label for="email" class="col-md-4 control-label">E-Mail Address</label>
                                        <div class="col-md-5">
                                            <p class="form-control-static">{{$user->email}}</p>
                                        </div>
                                    </div>
                                    <div class="form-group{{ $errors->has('phone') ? ' has-error' : '' }}">
                                        <label for="phone" class="col-md-4 control-label">Telefón</label>
                                        <div class="col-md-3">
                                            <p class="form-control-static">{{$user->phone}}</p>
                                        </div>
                                    </div>

                                    <div class="form-group{{ $errors->has('interest') ? ' has-error' : '' }}">
                                        <label for="interest" class="col-md-4 control-label">Záujmy užívateľa</label>
                                        <div class="col-md-4">
                                            @foreach($interest as $int)
                                                @foreach($user->interest as $r )
                                                        @if($r->id == $int->id)
                                                            <p class="form-control-static">{{$int->name}}</p>
                                                        @endif
                                                @endforeach
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="form-group{{ $errors->has('company') ? ' has-error' : '' }}">
                                        <label for="company" class="col-md-4 control-label">Firma</label>
                                        <div class="col-md-6">
                                            <p class="form-control-static">{{$user->company}}</p>
                                        </div>
                                    </div>
                                    <div class="form-group{{ $errors->has('internet') ? ' has-error' : '' }}">
                                        <label for="internet" class="col-md-4 control-label">Internet</label>
                                        <div class="col-md-4">
                                            <p class="form-control-static">{{$user->internet}}</p>
                                        </div>
                                    </div>
                                    <hr>
                                </form>
                            </div>
                        </div>
                        <div id="tab-2" class="tab-pane">
                            <div class="panel-body">
                                 @foreach($event_stat as $es)
                                    <div class="ibox-title">
                                        <h4 class="@if($es['membership']->membership_active == 1) text-success @else text-danger @endif">  členstvo: ({{ $es['membership']->club_title }})   Od: {{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s',$es['membership']->valid_from )->format('d.m.Y')}}
                                            - {{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s',$es['membership']->valid_to )->format('d.m.Y')}}
                                        </h4>
                                    </div>
                                    <div class="ibox-content">
                                        <div class="row m-b-lg m-t-lg">
                                                <div class="col-lg-3">
                                                    <h3> Udalosti: {{$es['event_count']}}/{{$es['event_attend']}}</h3>
                                                    <h2>
                                                        @if($es['event_attend'] > 0)
                                                            {{  round($es['event_attend'] * 100 / $es['event_count'], 0 ) }}
                                                        @else 0 @endif %
                                                    </h2>
                                                    <div class="progress progress-small">
                                                        <div style="width: @if($es['event_attend'] > 0){{round($es['event_attend'] * 100 / $es['event_count'], 0 )}}@else 0 @endif%;" class="progress-bar"></div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <p>Počet vytvorenych hostí: {{$es['user_created']}}</p>
                                                    <p>Počet pozvaných hostí: {{$es['guest_attend_count']}}</p>
                                                    <p>Počet potvrdených hostí: {{$es['guest_attend_count_confirmed']}}</p>
                                                    <p>Počet hostí ktorí prišli: {{$es['guest_attend_count_attend']}}</p>
                                                </div>
                                                <div class="col-lg-3">
                                                    <p>Počet odovzdaných lístkov: {{$es['reference_from']}}</p>
                                                    <p>Počet referencií/suma: {{$es['reference_from_evidence']}}/{{$es['reference_from_evidence_price']}}</p>
                                                    <p>Počet stretnutí 1 na 1: {{$es['reference_from_1x1']}}</p>
                                                    <p>Počet odporúčaní: {{$es['reference_from']}}</p>
                                                </div>
                                                <div class="col-lg-3">
                                                    <p>Počet prijatých lístkov: {{$es['reference_to']}}</p>
                                                    <p>Počet referencií/suma: {{$es['reference_to_evidence']}}/{{$es['reference_to_evidence_price']}}</p>
                                                    <p>Počet stretnutí 1 na 1: {{$es['reference_to_1x1']}}</p>
                                                    <p>Počet odporúčaní: {{$es['reference_to']}}</p>
                                                </div>

                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div id="tab-3" class="tab-pane">
                            <div class="panel-body">

                                <div class="ibox-content">
                                    <table class="table table-hover" id="item_table">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th class="hide"></th>
                                            <th>Meno</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Vytvorené</th>
                                            <th>Odvetvie</th>
                                            <th>Spoločnosť
                                            <th width="200px"></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @if($guests)
                                            @foreach($guests as $k => $v)
                                                <tr>
                                                    <td></td>
                                                    <td>{{ $v->name }} {{ $v->surname }}</td>
                                                    <td>{{ $v->email }} </td>
                                                    <td>{{ $v->phone }} </td>
                                                    <td>{{  \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $v->created_at)->format('d.m.Y') }} </td>
                                                    <td>{{ $v->industry }}</td>
                                                    <td>{{ $v->company }}</td>
                                                    <td>
                                                        <a href="{{ route('guests.guest-listings.edit', ['user' => $v->id]) }}" type="button" class="pull-right btn btn-warning btn-xs m-l-sm">Detail</a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                        </tbody>
                                    </table>
                                </div>

                            </div>
                        </div>

                        <div id="tab-4" class="tab-pane">
                            <div class="panel-body">

                                @foreach($event_stat as $es)
                                    <div class="ibox-title">
                                        <h4 class="@if($es['membership']->membership_active == 1) text-success @else text-danger @endif">  členstvo: ({{ $es['membership']->club_title }})   Od: {{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s',$es['membership']->valid_from )->format('d.m.Y')}}
                                            - {{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s',$es['membership']->valid_to )->format('d.m.Y')}}
                                        </h4>
                                    </div>
                                    <div class="ibox-content">

                                            @if($es['events'] && $es['events']->count() > 0 )

                                            <table class="table table-hover" id="item_table">
                                            <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Udalosť</th>
                                                <th>Klub</th>
                                                <th>Termín</th>
                                                <th width="200px"></th>
                                            </tr>
                                            </thead>
                                            <tbody>

                                            @foreach($es['events'] as $event )
                                                <tr>
                                                    <td>{{$event->id}}</td>
                                                    <td>{{$event->title}}</td>
                                                    <td>{{$event->club_title}} </td>
                                                    <td>
                                                        {{\Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $event->event_from)->format('d.m.Y H:i')}} -
                                                        {{\Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $event->event_to)->format('H:i')}}
                                                    </td>
                                                    <td>
                                                     <a href="{{ route('events.listing.show', ['id' => $event->id]) }}" type="button" class="pull-right btn btn-warning btn-xs m-l-sm">Detail</a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                        @else
                                            <p class="text-info">Neexistujú žiadne udalosti.</p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div id="tab-5" class="tab-pane">
                            <div class="panel-body">


                                    <div class="ibox-content">

                                        @if($es['events_from_user'] )

                                            <table class="table table-hover" id="item_table">
                                                <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Udalosť</th>
                                                    <th>Klub</th>
                                                    <th>Termín</th>
                                                    <th>Pozvanka</th>
                                                    <th>Návšteva</th>
                                                    <th>Stav</th>
                                                    <th width="200px"></th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($es['events_from_user'] as $event )
                                                    <tr>
                                                        <td>{{$event->event_id}}</td>
                                                        <td>{{$event->title}}</td>
                                                        <td>{{$event->club_title}} </td>
                                                        <td>
                                                            {{\Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $event->event_from)->format('d.m.Y H:i')}}
                                                        </td>
                                                        <td>
                                                            @if($event->status == 1)
                                                                <i class="fa fa-exclamation-circle text-danger m-r-xs " style="font-size: 20px;"></i>
                                                            @elseif($event->status == 2)
                                                                <i class="fa fa-check-circle text-success m-r-xs " style="font-size: 20px;"></i>
                                                            @elseif($event->status  == 3)
                                                                <i class="fa fa-times-circle text-warning m-r-xs " style="font-size: 20px;"></i>
                                                            @else
                                                                <i class="fa fa-times-circle text-info m-r-xs" style="font-size: 20px;"></i>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($event->user_attend == 1)
                                                                <i class="fa fa-check-circle text-success m-r-xs " style="font-size: 20px;"></i>
                                                            @else
                                                                <i class="fa fa-times-circle text-danger m-r-xs" style="font-size: 20px;"></i>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            {{$event->user_status_attend}}
                                                        </td>
                                                        <td>
                                                            <a href="{{ route('events.listing.show', ['id' => $event->event_id]) }}" type="button" class="pull-right btn btn-warning btn-xs m-l-sm">Detail</a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        @else
                                            <p class="text-info">Neexistujú žiadne udalosti.</p>
                                        @endif
                                    </div>

                            </div>
                        </div>


                        <div id="tab-6" class="tab-pane">
                            <div class="panel-body">

                                @foreach($event_attend as $es)
                                    <div class="ibox-title">
                                        <h4 class="@if($es->membership_active == 1) text-success @else text-danger @endif">  členstvo: ({{ $es->club_title }})   Od: {{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s',$es->valid_from )->format('d.m.Y')}}
                                            - {{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s',$es->valid_to )->format('d.m.Y')}}
                                        </h4>
                                    </div>

                                    <div class="ibox-content">

                                        @if( isset($es->udalosti)  )

                                            <table class="table table-hover" id="item_table">
                                                <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Udalosť</th>
                                                    <th>Klub</th>
                                                    <th>Termín</th>
                                                    <th>Stav</th>
                                                    <th>Návšteva</th>
                                                    <th width="200px"></th>
                                                </tr>
                                                </thead>
                                                <tbody>

                                                @foreach($es->udalosti as $event )
                                                    <tr>
                                                        <td>{{$event->id}}</td>
                                                        <td>{{$event->title}}</td>
                                                        <td>{{$event->club_title}} </td>
                                                        <td>
                                                            {{\Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $event->event_from)->format('d.m.Y H:i')}} -
                                                            {{\Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $event->event_to)->format('H:i')}}
                                                        </td>
                                                        <td>
                                                            @if($event->status == 1)
                                                                <i class="fa fa-exclamation-circle text-danger m-r-xs " style="font-size: 20px;"></i>
                                                            @elseif($event->status == 2)
                                                                <i class="fa fa-check-circle text-success m-r-xs " style="font-size: 20px;"></i>
                                                            @elseif($event->status  == 3)
                                                                <i class="fa fa-times-circle text-warning m-r-xs " style="font-size: 20px;"></i>
                                                            @else
                                                                <i class="fa fa-times-circle text-info m-r-xs" style="font-size: 20px;"></i>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($event->attend == 1)
                                                                <i class="fa fa-check-circle text-success m-r-xs " style="font-size: 20px;"></i>
                                                            @else
                                                                <i class="fa fa-times-circle text-danger m-r-xs" style="font-size: 20px;"></i>
                                                            @endif
                                                        </td>


                                                        <td>
                                                            <a href="{{ route('events.listing.show', ['id' => $event->id]) }}" type="button" class="pull-right btn btn-warning btn-xs m-l-sm">Detail</a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        @else
                                            <p class="text-info">Neexistujú žiadne udalosti.</p>
                                        @endif
                                    </div>

                                @endforeach
                            </div>
                        </div>



                    </div>
                </div>
            </div>
        </div>

@endsection

@section('page_css')

@endsection


@section('scripts')

            <script src="{!! asset('js/plugins/sparkline/jquery.sparkline.min.js') !!}" type="text/javascript"></script>


            <script>
        jQuery(window).ready(function (){



            $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                var target = $(e.target).attr("href") // activated tab
                //alert(target);

                sparklineCharts();

            });



            var sparklineCharts = function(){

                $("#sparkline6").sparkline([5, 3], {
                    type: 'pie',
                    height: '140',
                    sliceColors: ['#1ab394', '#F5F5F5']
                });

            };

            var sparkResize;

            // $(window).resize(function(e) {
            //     clearTimeout(sparkResize);
            //     sparkResize = setTimeout(sparklineCharts, 500);
            // });

            sparklineCharts();


        });

    </script>


@endsection