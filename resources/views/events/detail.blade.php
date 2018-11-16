@extends('layouts.app')

@section('title', 'Detail udalosti')

@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">

                <div class="ibox float-e-margins">
                    <div class="ibox-content p-md">
                        <div class="row">
                            <div class="col-md-7">
                                <div class="pull-left">
                                    <div class="img">
                                        <img alt="image" class="feed-photo img-responsive" src="{!! $event->image_thumb !!}" style="max-width: 100px;">
                                    </div>
                                </div>
                                <div class=" profile-content">
                                    @if($event->active == 2)
                                        <button type="button" class="btn btn-xs btn-danger"> Uzatvorená</button>
                                    @else
                                        <button type="button" class="btn btn-xs btn-defaul"> Stav udalosti aktívna</button>
                                    @endif
                                    <h3><strong>{{$event->title}}</strong></h3>
                                    <h3>{{Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $event->event_from)->format('d.m.Y H:i')}} - {{Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $event->event_to)->format('H:i')}}</h3>
                                    <h5>{{$event->club->title}}</h5>
                                    <h5>{{$event->host_name}}</h5>
                                    <p><i class="fa fa-map-marker"></i> {{$event->address_street}}, {{$event->address_psc}}, {{$event->address_city}}</p>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="pull-right">
                                    <div class="btn-group">
                                        <button data-toggle="dropdown" class="btn btn-warning btn-sm dropdown-toggle"><i class="fa fa-print"></i>  Tlačové výstupy <span class="caret"></span></button>
                                        <ul class="dropdown-menu">
                                            <li><a target="_blank" href="{{ route('events.print.attendance', ['attendance' => $event->id, 'type' => 'attendance']) }}">Prezenčka</a></li>
                                            <li><a target="_blank" href="{{ route('events.print.attendance', ['attendance' => $event->id, 'type' => 'attendance_price']) }}">Prezenčka s cenou</a></li>
                                            <li><a target="_blank" href="{{ route('events.print.attendance', ['attendance' => $event->id, 'type' => 'guest_list']) }}">Zoznam hostí</a></li>
                                            <li class="divider"></li>
                                            <li><a href="#">QR info o raňajkách</a></li>
                                        </ul>
                                    </div>

                                    <div class="btn-group">
                                        <button data-toggle="dropdown" class="btn btn-info btn-sm dropdown-toggle"><i class="fa fa-cog"></i>  Akcie <span class="caret"></span></button>
                                        <ul class="dropdown-menu">
                                            <li><a  href="{{ route('events.balance.show', [  $event->id ]) }}">Uzávierka</a></li>
                                            <li><a  href="{{ route('events.reference.show',[ $event->id ] ) }}">Ustrižky</a></li>
                                            <li><a href="{{route('events.inviting.preview', ['id' => $event->id])}}" target="_blank"> Náhľad pozvánky</a></li>
                                            {{--<li><a href="{{route('events.template.show', ['id' => $event->id])}}" target="_blank"> Šablona pozvánky</a></li>--}}
                                            {{--<li><a href="{{route('events.ticket-setting.show', ['id' => $event->id])}}"> Nastavenie lístkov</a></li>--}}
                                            <li class="divider"></li>
                                            <li><a href="{{route('events.listing-duplicate', ['id' => $event->id])}}"> Vytvorenie kópie</a></li>
                                        </ul>
                                    </div>

                                    <a href="{{ route('events.listing.edit', ['user' => $event->id ]) }}" type="button" class="btn btn-success btn-sm m-l-sm"><i class="fa fa-edit"></i> Upraviť</a>

                                        @if(Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $event->event_from) > Carbon\Carbon::createFromFormat('Y-m-d H:i:s', now() ))
                                            <h3>Pozvánky</h3>
                                            <div class="btn-group">
                                                <a href="{{ route('invitations.event.guest-new', [ 'event' => $event->id, 'club' => $event->club_id] ) }}"
                                                   class="btn @if(str_limit(Request::capture()->segment(3), 9, "" ) == 'guest-new') btn-warning @else btn-white  @endif " type="button">Nový hosť</a>

                                                <a href="{{ route('invitations.event.guest', [ 'event' => $event->id] ) }}"
                                                   class="btn @if(Request::capture()->segment(2) == 'event-guest') btn-warning @else btn-white  @endif " type="button">Hostia</a>

                                                <a href="{{ route('invitations.event.member', [ 'event' => $event->id] ) }}"
                                                   class="btn @if(Request::capture()->segment(2) == 'event-member') btn-warning @else btn-white  @endif " type="button">Členovia</a>

                                                <a href="{{ route('invitations.event.member-all', [ 'event' => $event->id] ) }}"
                                                   class="btn @if(Request::capture()->segment(2) == 'event-member-all') btn-warning @else btn-white  @endif " type="button">Členovia ostatní</a>
                                            </div>
                                        @else
                                            @if( $event->active != 2 )
                                                <h3>Pridanie osoby do prezencky</h3>
                                                <div class="btn-group">
                                                    <a href="{{ route('invitations.event.guest-new', [ 'event' => $event->id, 'club' => $event->club_id] ) }}"
                                                       class="btn @if(str_limit(Request::capture()->segment(3), 9, "" ) == 'guest-new') btn-success @else btn-white  @endif " type="button">Nový hosť</a>

                                                    <a href="{{ route('invitations.event.guest', [ 'event' => $event->id] ) }}"
                                                       class="btn @if(Request::capture()->segment(2) == 'event-guest') btn-success @else btn-white  @endif " type="button">Hostia</a>

                                                    <a href="{{ route('invitations.event.member', [ 'event' => $event->id] ) }}"
                                                       class="btn @if(Request::capture()->segment(2) == 'event-member') btn-success @else btn-white  @endif " type="button">Členovia</a>

                                                    <a href="{{ route('invitations.event.member-all', [ 'event' => $event->id] ) }}"
                                                       class="btn @if(Request::capture()->segment(2) == 'event-member-all') btn-success @else btn-white  @endif " type="button">Členovia ostatní</a>
                                                </div>
                                            @endif
                                        @endif

                                    {{--<a href="{{ route('invite.event.member', ['event' => $event->id]) }}" class="btn btn-success btn-facebook btn-outline"><i class="fa fa-print"></i> Pozvánka členov</a>--}}
                                    {{--<a class="btn btn-info btn-facebook btn-outline"><i class="fa fa-print"></i> Info</a>--}}

                                    {{--<a href="{{ route('setting.club-breakfast-ticket', ['event' => $event->id ]) }}" class="btn btn-success btn-facebook btn-outline"><i class="fa fa-address-card"></i> Ústrižky</a>--}}
                                    {{--<a href="{{ route('setting.club-breakfast-attendance', ['event' => $event->id ]) }}" class="btn btn-success btn-facebook btn-outline"><i class="fa fa-users"></i> Prezenčka</a>--}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">

                @include('components.validation')

                <div class="tabs-container m-b-lg">
                    <ul class="nav nav-tabs">
                        <li class="active"><a data-toggle="tab" href="#tab-1" aria-expanded="true"> Prezenčná listina</a></li>
                        <li class=""><a @if(count($activities) == 0)class="bg-danger-light"@endif  data-toggle="tab" href="#tab-2" aria-expanded="false"> Aktivita klubu</a></li>
                        <li class=""><a data-toggle="tab" href="#tab-3" aria-expanded="false"> Ústrižky</a></li>
                        <li class=""><a data-toggle="tab" href="#tab-4" aria-expanded="false"> Fotogaléria</a></li>
                    </ul>
                    <div class="tab-content">
                        <div id="tab-1" class="tab-pane active">
                            <div class="panel-body">
                                @if(count($attendance) >0)
                                    <p>
                                        <button type="button" class="btn btn-primary btn-w-m" id="btn_invited"><i class="fa fa-user"></i>
                                            {{ count($attendance)}} Pozvaní</button>
                                        <button type="button" class="btn btn-success btn-w-m" id="btn_confirm"><i class="fa fa-thumbs-up"></i>
                                            {{ count($attendance->where('status_id', 2))}} Potvrdená účasť</button>
                                        <button type="button" class="btn btn-warning btn-w-m" id="btn_reject"><i class="fa fa-thumbs-down"></i>
                                            {{ count($attendance->where('status_id', 3))}} Ospravedlnení</button>
                                        <button type="button" class="btn btn-danger btn-w-m" id="btn_no_response"><i class="fa fa-exclamation"></i>
                                            {{ count($attendance->where('status_id', 1))}} Bez reakcie</button>
                                    </p>

                                    <div class="row">
                                        {{   Form::open( ['route' => ['events.listing.show', 'event' => $event->id ], 'method' => 'GET' ])}}
                                        {{Form::hidden('form_type','attendance_type')}}
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label class="control-label" for="status_attendance">Status prihlásenia</label>
                                                <select class="input form-control input-s-sm inline" name="status" id="status_attendance" onchange="this.form.submit()">
                                                    <option value="">-- Všetky stavy --</option>
                                                    @if($events_guest_status)
                                                        @foreach($events_guest_status as $egs)
                                                            <option value="{{$egs->id}}" id="{{$egs->id}}" @if( Request::input('status')== $egs->id) selected="selected" @endif>{{$egs->status}}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-2 col-md-2" style="padding-right: 3px;">
                                            <div class="form-group">
                                                <label class="control-label" for="search_contact">Typ kontaktu</label>
                                                <select name="search_contact" id="search_contact" class="form-control" onchange="this.form.submit()">
                                                    <option value="" >Všetky</option>
                                                    <option value="member" @if( Request::input('search_contact') == 'member') selected="selected" @endif>Člen</option>
                                                    <option value="host" @if( Request::input('search_contact') == 'host') selected="selected" @endif>Hosť</option>
                                                </select>
                                            </div>
                                        </div>

                                        {{Form::close()}}
                                        @if(Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $event->event_from) > Carbon\Carbon::createFromFormat('Y-m-d H:i:s', now()  )  && $event->active < 2)
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label class="control-label" for="status_attendance">Preposlať pozvánku vybraným</label>
                                                <button id="btn_resend_invitation" class="btn btn-primary btn-sm"><i class="fa fa-send"></i> Odoslať</button>
                                            </div>
                                        </div>
                                        @endif
                                    </div>

                                    <table class="table table-hover" id="table_attendance">
                                        <thead>
                                        <tr>
                                            <th width="5%">
                                                @if( $event->active < 2)
                                                <input type="checkbox"  class="i-checks select_all" value="0">
                                                @else
                                                 Účasť
                                                @endif
                                            </th>
                                            <th>Meno</th>
                                            <th class="hidden-xs">Telefon</th>
                                            <th>Dátum</th>
                                            <th>Stav</th>
                                            <th width="25%">Akcia</th>
                                            <th width="10%"></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        {{Form::open()}}
                                        @foreach($attendance as $item => $v)

                                            @if(( Request::input('status') > 0 && Request::input('status') == $v->status_id) || Request::input('status') == "")

                                                {{--filter ci je pozvany clen alebo host--}}
                                                @if( Request::input('search_contact') == 'member' && $v->attend_user_event ==  1 )

                                                    @include('events.detail_listing')

                                                @elseif( Request::input('search_contact') == 'host' && $v->attend_user_event ==  0 )

                                                    @include('events.detail_listing')

                                                @elseif( Request::input('search_contact') == '' )

                                                    @include('events.detail_listing')

                                                @endif

                                            @endif
                                        @endforeach
                                        {{Form::close()}}
                                        </tbody>
                                    </table>

                                @else
                                    <p class="text-danger">Neexistujú žiadne záznamy</p>
                                @endif
                            </div>
                        </div>
                        <div id="tab-2" class="tab-pane">
                            <div class="panel-body">
                                <div class="col-md-8">
                                    @if(count($activities) > 0)
                                        <table class="table table-responsive">
                                            <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Člen</th>
                                                <th>Aktivita</th>
                                                <th>Poznámka</th>
                                                <th></th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($activities as $a)
                                                <tr>
                                                    <td>{{$a->id}}</td>
                                                    <td>{{$a->full_name}}</td>
                                                    <td>{{$a->activity_name}}</td>
                                                    <td>{{$a->description}}</td>
                                                    <td>
                                                        <button class="btn btn-success btn-xs activity_edit" id="{{$a->id}}"
                                                                data-edit-id="{{$a->id}}"
                                                                data-edit-user="{{$a->user_id}}"
                                                                data-edit-description="{{$a->description}}"
                                                                data-edit-activity-type="{{$a->activity_id}}" ><i class="fa fa-edit"></i> Upraviť</button>
                                                        <a type="button" data-item-id="{{ $a->id }}"
                                                           class="m-l-sm  btn btn-danger btn-xs delete-alert"><i class="fa fa-trash-o"></i> </a>

                                                        {{ Form::open(['method' => 'DELETE', 'route' => ['events.activity.destroy', $a->id ],
                                                            'class' => 'class="m-l-sm pull-right btn btn-danger btn-xs delete-alert hide',
                                                            'id' => 'item-del-'. $a->id  ])
                                                        }}
                                                        {{Form::hidden('event_id', $a->id  )}}
                                                        {{ Form::close() }}

                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    @else
                                        <p class="text-danger">Neexistujú žiadne záznamy</p>
                                    @endif
                                </div>
                                <div class="col-md-4">
                                    <form id="event_activity" method="POST" action="{{ route('events.activity.store') }}" class="form-horizontal">

                                        {{ csrf_field() }}
                                        {{ Form::hidden('event_id', $event->id) }}
                                        {{ Form::hidden('club_id', $event->club_id) }}

                                        <input type="hidden" id="activity_id_hidden" name="activity_id_hidden" value="0">

                                        <div class="form-group"><label class="col-lg-6 control-label">Člen</label>
                                            <div class="col-lg-6">
                                                <select id="user_id" class="form-control" name="user_id"  required>
                                                    <option value="">-- výber --</option>
                                                    @foreach( $users as $u)
                                                        <option value="{{$u->id}}" >{{$u->full_name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group"><label class="col-lg-6 control-label">Aktivita</label>
                                            <div class="col-lg-6">
                                                <select id="activity_id" class="form-control" name="activity_id"  required>
                                                    <option value="">-- výber --</option>
                                                    @foreach( $event_activities as $ea)
                                                        <option value="{{$ea->id}}" >{{$ea->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group"><label class="col-lg-6 control-label">Poznámka</label>
                                            <div class="col-lg-6">
                                                <textarea  id="description" name="description" type="text" class="form-control" rows="2"></textarea>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-lg-offset-6 col-lg-6">
                                                <button class="btn btn-sm btn-white" type="submit" id="submit_btn">{{__('form.add_record')}}</button>
                                                <button class="btn btn-sm btn-white" type="button" id="reset_btn">Zrušiť</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div id="tab-3" class="tab-pane">
                            <div class="panel-body">
                                @if(count($tickets)>0)
                                    <table class="table table-hover">
                                        <thead>
                                        <tr>
                                            <th width="5%">#</th>
                                            <th>Od</th>
                                            <th>Komu</th>
                                            <th class="text-right">Hodnota</th>
                                            <th>Dátum</th>
                                            <th>Popis transakcie</th>
                                            <th></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($tickets as $item => $v)
                                            <tr>
                                                <td>{{ $v->id }}</td>
                                                <td><strong>@if($v->from_name ){{ $v->from_name }} {{ $v->from_surname }}@else Hosť @endif</strong></td>
                                                <td><strong>@if($v->to_name ){{ $v->to_name }} {{ $v->to_surname }}@else Hosť @endif</strong></td>
                                                <td align="right" @if($v->value_1 < 0)class="text-danger" @else class="text-success"  @endif><strong>{{ number_format((float)$v->value_1, 2, ',', ' ') }}</strong></td>
                                                <td>{{ Carbon\Carbon::createFromFormat('Y-m-d',$v->date)->format('d.m.Y') }}</td>
                                                <td>{{ $v->ref_name }}</td>
                                                <td>{{ $v->description }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                @else
                                    <p class="text-danger">Neexistujú žiadne záznamy</p>
                                @endif
                            </div>
                        </div>

                        <div id="tab-4" class="tab-pane">
                            <div class="panel-body">

                                <!-- The file upload form used as target for the file upload widget -->
                                <form id="fileupload" action="{{route('events.upload.images.blue-imp')}}" method="POST" enctype="multipart/form-data">
                                    {{ csrf_field() }}
                                    {{ Form::hidden('event_id', $event->id ) }}

                                    <!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
                                    <div class="row fileupload-buttonbar">
                                        <div class="col-lg-7">
                                            <!-- The fileinput-button span is used to style the file input field as button -->
                                            <span class="btn btn-success fileinput-button">
                                                <i class="glyphicon glyphicon-plus"></i>
                                                <span>Pridať súbory...</span>
                                                <input type="file" name="files[]" multiple>
                                            </span>
                                            <button type="submit" class="btn btn-primary start">
                                                <i class="glyphicon glyphicon-upload"></i>
                                                <span>Nahrať súbory</span>
                                            </button>
                                            <button type="reset" class="btn btn-warning cancel">
                                                <i class="glyphicon glyphicon-ban-circle"></i>
                                                <span>Zrušiť nahrávanie</span>
                                            </button>
                                            {{--<button type="button" class="btn btn-danger delete">--}}
                                                {{--<i class="glyphicon glyphicon-trash"></i>--}}
                                                {{--<span>Zmazať</span>--}}
                                            {{--</button>--}}
                                            {{--<input type="checkbox" class="toggle">--}}
                                            <!-- The global file processing state -->
                                            <span class="fileupload-process"></span>
                                        </div>
                                        <!-- The global progress state -->
                                        <div class="col-lg-5 fileupload-progress fade">
                                            <!-- The global progress bar -->
                                            <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
                                                <div class="progress-bar progress-bar-success" style="width:0%;"></div>
                                            </div>
                                            <!-- The extended global progress state -->
                                            <div class="progress-extended">&nbsp;</div>
                                        </div>
                                    </div>
                                    <!-- The table listing the files available for upload/download -->
                                    <table role="presentation" class="table table-striped"><tbody class="files"></tbody></table>
                                </form>
                                <div class="row image_galery">
                                    @if($images)
                                        @foreach($images as $img)
                                            <div class="col-md-4 " id="event_image_listing_{!! $img->id !!}">
                                                <img class="img-responsive m-b-sm" src="{!! asset('/images/event-images/') . '/'.$event->id .'/sq/' . $img->image !!}">
                                                <div class="img-overlay">
                                                    <button data-img-button-id="{{$img->id}}" class="btn btn-md btn-danger delete_image_btn"><i class="fa fa-trash-o"></i> Zmazať</button>
                                                </div>
                                            </div>

                                            {{ Form::open(['method' => 'DELETE', 'route' => ['events.activity.destroy', $img->id+1000000 ],
                                                        'class' => 'class="m-l-sm pull-right btn btn-danger btn-xs delete-alert hide',
                                                        'id' => 'image-delete-'. $img->id  ])
                                                    }}
                                            {{Form::hidden('image_id', $img->id  )}}
                                            {{ Form::close() }}

                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection

@section('page_css')

    <style>

        .img-overlay {
            position: absolute;
            top: 0;
            bottom: -25px;
            left: 25px;
            right: 0;
            text-align: left;
        }

        .img-overlay:before {
            content: ' ';
            display: block;
            /* adjust 'height' to position overlay content vertically */
            height: 80%;
        }

    </style>

    <!-- blueimp Gallery styles -->
    <link rel="stylesheet" href="https://blueimp.github.io/Gallery/css/blueimp-gallery.min.css">
    <!-- CSS to style the file input field as button and adjust the Bootstrap progress bars -->
    <link rel="stylesheet" href="{!! asset('js/plugins/jQuery-File-Upload/css/jquery.fileupload.css') !!}">
    <link rel="stylesheet" href="{!! asset('js/plugins/jQuery-File-Upload/css/jquery.fileupload-ui.css') !!}">

    <!-- CSS adjustments for browsers with JavaScript disabled -->
    <noscript><link rel="stylesheet" href="{!! asset('js/plugins/jQuery-File-Upload/css/jquery.fileupload-noscript.css') !!}"></noscript>
    <noscript><link rel="stylesheet" href="{!! asset('js/plugins/jQuery-File-Upload/css/jquery.fileupload-ui-noscript.css') !!}"></noscript>


@endsection


@section('scripts')

    <script type="text/javascript" src="https://cdn.datatables.net/v/bs/dt-1.10.16/datatables.min.js"></script>

    <!-- The jQuery UI widget factory, can be omitted if jQuery UI is already included -->
    <script src="{!! asset('js/plugins/jQuery-File-Upload/js/vendor/jquery.ui.widget.js') !!}"></script>

    <!-- The Templates plugin is included to render the upload/download listings -->
    <script src="https://blueimp.github.io/JavaScript-Templates/js/tmpl.min.js"></script>
    <!-- The Load Image plugin is included for the preview images and image resizing functionality -->
    <script src="https://blueimp.github.io/JavaScript-Load-Image/js/load-image.all.min.js"></script>
    <!-- The Canvas to Blob plugin is included for image resizing functionality -->
    <script src="https://blueimp.github.io/JavaScript-Canvas-to-Blob/js/canvas-to-blob.min.js"></script>

    <!-- blueimp Gallery script -->
    <script src="https://blueimp.github.io/Gallery/js/jquery.blueimp-gallery.min.js"></script>
    <!-- The Iframe Transport is required for browsers without support for XHR file uploads -->
    <script src="{!! asset('js/plugins/jQuery-File-Upload/js/jquery.iframe-transport.js' ) !!}"></script>

    <!-- The basic File Upload plugin -->
    <script src="{!! asset('js/plugins/jQuery-File-Upload/js/jquery.fileupload.js' ) !!}"></script>

    <!-- The File Upload processing plugin -->
    <script src="{!! asset('js/plugins/jQuery-File-Upload/js/jquery.fileupload-process.js' ) !!}"></script>

    <!-- The File Upload image preview & resize plugin -->
    <script src="{!! asset('js/plugins/jQuery-File-Upload/js/jquery.fileupload-image.js' ) !!}"></script>

    {{--<!-- The File Upload audio preview plugin -->--}}
    {{--<script src="{!! asset('js/plugins/jQuery-File-Upload/js/jquery.fileupload-audio.js' ) !!}"></script>--}}

    {{--<!-- The File Upload video preview plugin -->--}}
    {{--<script src="{!! asset('js/plugins/jQuery-File-Upload/js/jquery.fileupload-video.js' ) !!}"></script>--}}

    <!-- The File Upload validation plugin -->
    <script src="{!! asset('js/plugins/jQuery-File-Upload/js/jquery.fileupload-validate.js' ) !!}"></script>

    <!-- The File Upload user interface plugin -->
    <script src="{!! asset('js/plugins/jQuery-File-Upload/js/jquery.fileupload-ui.js' ) !!}"></script>

    <!-- The main application script -->
    <script src="{!! asset('js/plugins/jQuery-File-Upload/js/main.js' ) !!}"></script>

    <!-- The XDomainRequest Transport is included for cross-domain file deletion for IE 8 and IE 9 -->
    <!--[if (gte IE 8)&(lt IE 10)]>
    <script src="{!! asset('js/plugins/jQuery-File-Upload/js/cors/jquery.xdr-transport.js' ) !!}"></script>
    <![endif]-->

    <script>
        jQuery(window).ready(function (){


            $('.delete_image_btn').click(function (e) {

                var id = $(e.currentTarget).attr("data-img-button-id");

                swal({
                    title: "Zmazať obrázok?",
                    text: "Táto operácia je nevratná!",
                    type: "warning",
                    showCancelButton: true,
                    cancelButtonText: "Zrušiť",
                    confirmButtonColor: "red",
                    confirmButtonText: "Áno, zmazať",
                    closeOnConfirm: true
                }, function () {

                    $.ajax({
                        type:'POST',
                        url:'/ajax/function',
                        data: {
                            action: 'delete_image_event',
                            event_id: {!! $event->id !!},
                            image_id: id
                        },
                        dataType: 'json',
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        success:function(data){
                            if(data.status == 'OK'){

                                $('#event_image_listing_'+id).remove();
                                toastr.success('Obrázok bol zmazaný.');
                            }
                        }
                    });

                });

            });


            jQuery.extend( jQuery.fn.dataTableExt.oSort, {
                "locale-compare-asc": function ( a, b ) {
                    return a.localeCompare(b, 'da', { sensitivity: 'accent' })
                },
                "locale-compare-desc": function ( a, b ) {
                    return b.localeCompare(a, 'da', { sensitivity: 'accent' })
                }
            });


            //zaskrtnute check box posleme znovu pozvanku
            $('#btn_resend_invitation').click(function (event) {
                event.preventDefault();
                var checkedValues = $("input:checkbox:checked", "#table_attendance").map(function() {
                    return $(this).val();
                }).get();

                //zmazeme 0, checkob
                var removeItem = "0";
                checkedValues = jQuery.grep(checkedValues, function(value) {
                    return value != removeItem;
                });

                var count_invitation = checkedValues.length;

                swal({
                    title: "Znovu odoslať pozvánku "+count_invitation+" užívateľom?",
                    text: "Táto operácia je nevratná!",
                    type: "warning",
                    showCancelButton: true,
                    cancelButtonText: "Zrušiť",
                    confirmButtonColor: "#51DD95",
                    confirmButtonText: "Áno, odoslať",
                    closeOnConfirm: false
                }, function () {

                    $.ajax({
                        type:'POST',
                        url:'/ajax/function',
                        data: {
                            action: 'send_invitation_multiplay',
                            attendance: checkedValues,
                            event: {!! $event->id !!}
                        },
                        dataType: 'json',
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        success:function(data){
                            if(data.status == 'OK'){
                                toastr.success('Pozvánky boli úspešne odoslané.');
                            }
                        }
                    });
                    swal("Odoslaná", "Pozvánka(ky) boli odoslané", "success");
                });
            });

            $('.i-checks').iCheck({
                checkboxClass: 'icheckbox_square-green',
                radioClass: 'iradio_square-green'
            });

            // icheck after paginate datatable
            $('#table_attendance').on('draw.dt', function () {

                $('.i-checks').iCheck({
                    checkboxClass: 'icheckbox_square-green',
                    radioClass: 'iradio_square-green'
                });

            });

            $("#btn_no_response").click(function () {
                $("#status_attendance").val("1").change();
            });

            $("#btn_confirm").click(function () {
                $("#status_attendance").val("2").change();
            });

            $("#btn_reject").click(function () {
                $("#status_attendance").val("3").change();
            });

            $("#btn_invited").click(function () {
                $("#status_attendance").val("").change();
            });

            $(".guest_status").change( function () {

                var status = $(this).val();
                var attendance = $(this).data('attendance-id');
                var event = "{!! $event->id !!}";

                console.log('event'+event);
                console.log('attendance'+attendance);
                console.log('status'+status);


                $.ajax({
                    type:'POST',
                    url:'/ajax/save-attendance-status',
                    data: {
                        attendance: attendance,
                        status: status,
                        event: event
                    },
                    dataType: 'json',
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    success:function(data){
                        if(data.attendance > 0){
                        toastr.success('Stav úspešne zapísaný');
                            replaceIconStatus(data.status, data.attendance)
                        }
                    }
                });
            });

            function replaceIconStatus(status, attendance) {

                var content = "";

                if(status==1) content = '<i class="fa fa-exclamation-circle text-danger m-r-xs "></i>';
                if(status==2) content = '<i class="fa fa-check-circle text-success m-r-xs "></i>';
                if(status==3) content = '<i class="fa fa-times-circle text-warning m-r-xs "></i>';
                if(status==4) content = '<i class="fa fa-times-circle text-info m-r-xs "></i>';
                $('.icon_status_element_'+attendance).html(content);
            }



            $(".activity_edit").click(function() {

                var row = $(this).closest("tr");
                var id = $(this).data('edit-id');

                row.addClass('success');
                row.siblings().removeClass( "success" );

                $('#activity_id_hidden').val(id);
                $('#user_id').val($(this).data('edit-user'));
                $('#activity_id').val($(this).data('edit-activity-type'));
                $('#description').val($(this).data('edit-description'));
                $('#submit_btn').text('Upraviť').removeClass('btn-white').addClass('btn-success');
            });

            $('#reset_btn').click(function () {

                $('#activity_id_hidden').val(0);
                $('#user_id').val("");
                $('#activity_id').val("");
                $('#description').val("");
                $('.success').removeClass("success");
            });

            $("#event_activity").validate({
                rules: {
                    event_activity_id: {
                        required: true
                    },
                    user_id: {
                        required: true
                    }
                }
            });


            $('.delete-alert').click(function (e) {

                var id = $(e.currentTarget).attr("data-item-id");
                swal({
                    title: "Ste si istý?",
                    text: "Táto operácia je nevratná!",
                    type: "warning",
                    showCancelButton: true,
                    cancelButtonText: "Zrušiť",
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Áno, zmazať",
                    closeOnConfirm: false
                }, function () {
                    document.getElementById('item-del-'+id).submit();
                    swal("Deleted", "Záznam bol zmazaný", "success");
                });
            });

            $('.send-alert').click(function (e) {

                var id = $(e.currentTarget).attr("data-item-id");
                swal({
                    title: "Znovu odoslať pozvánku?",
                    text: "Táto operácia je nevratná!",
                    type: "warning",
                    showCancelButton: true,
                    cancelButtonText: "Zrušiť",
                    confirmButtonColor: "#51DD95",
                    confirmButtonText: "Áno, odoslať",
                    closeOnConfirm: false
                }, function () {
                    document.getElementById('item-send-'+id).submit();
                    swal("Odoslaná", "Pozvánka bola odoslana", "success");
                });

            });


            $('#table_attendance').on("click", ".send_invitation_detail", function(){

//                var id = $(e.currentTarget).attr("data-item-id");
                var id = $(this).attr('data-item-id');
                 id = [id];

                console.log(id);

                swal({
                    title: "Znovu odoslať pozvánku?",
                    text: "Táto operácia je nevratná!",
                    type: "warning",
                    showCancelButton: true,
                    cancelButtonText: "Zrušiť",
                    confirmButtonColor: "#51DD95",
                    confirmButtonText: "Áno, odoslať",
                    closeOnConfirm: false
                }, function () {

                    $.ajax({
                        type:'POST',
                        url:'/ajax/function',
                        data: {
                            action: 'send_invitation_multiplay',
                            attendance: id,
                            event: {!! $event->id !!}
                        },
                        dataType: 'json',
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        success:function(data){
                            if(data.status == 'OK'){
                                toastr.success('Pozvánka bola úspešne odoslané.');
                            }
                        }
                    });

                    swal("Odoslaná", "Pozvánka bola odoslana", "success");
                });
            });


            // sorting s diakritikou
            $('#table_attendance').DataTable({
                "pageLength": 50,
                "dom": 'lrfrtip',
                columnDefs : [
                    { targets: 0, type: 'locale-compare' }
                ],
                "order": [1, 'asc']
            });


            $('.select_all').on('ifChecked', function(event){
                event.preventDefault();
                $(this).closest('table').find('td input:checkbox').iCheck('check')

            });

            $('.select_all').on('ifUnchecked', function(event){
                event.preventDefault();
                $(this).closest('table').find('td input:checkbox').iCheck('uncheck')
            });



            // callback ak su vsetky obrazky uploadovane
            $('#fileupload').bind('fileuploadstop', function (e, data) {

                toastr.success('success upload');

                //vymazeme zabulku a zobrazime galeriu fotografii
                $('[role="presentation"]').remove();

                $.ajax({
                    type:'POST',
                    url:'/ajax/function',
                    data: {
                        action: 'get_event_images',
                        event: {!! $event->id !!}
                    },
                    dataType: 'json',
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    success:function(data){

                        //vyprazdnime galeriu
                        $('.image_galery').empty();

                        $.each(data.data, function(i){

                            var tpl_image = '<div class="col-md-4">' +
                                '<img class="img-responsive m-b-sm" src="'+data.data[i].image_src+'">' +
                                '</div>';

                            $('.image_galery').append(tpl_image);
                        });

                        if(data.status == 'OK'){
                            toastr.success('');
                        }
                    }
                });



            });



        });

    </script>

    <!-- The template to display files available for upload -->
    <script id="template-upload" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-upload fade">
        <td>
            <span class="preview"></span>
        </td>
        <td>
            <p class="name">{%=file.name%}</p>
            <strong class="error text-danger"></strong>
        </td>
        <td>
            <p class="size">Processing...</p>
            <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="progress-bar progress-bar-success" style="width:0%;"></div></div>
        </td>
        <td>
            {% if (!i && !o.options.autoUpload) { %}
                <button class="btn btn-primary start" disabled>
                    <i class="glyphicon glyphicon-upload"></i>
                    <span>Start</span>
                </button>
            {% } %}
            {% if (!i) { %}
                <button class="btn btn-warning cancel">
                    <i class="glyphicon glyphicon-ban-circle"></i>
                    <span>Cancel</span>
                </button>
            {% } %}
        </td>
    </tr>
{% } %}
</script>

<!-- The template to display files available for download -->
<script id="template-download" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-download fade">
        <td>
            <span class="preview">
                {% if (file.thumbnailUrl) { %}
                    <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" data-gallery><img src="{%=file.thumbnailUrl%}" width="80px"></a>
                {% } %}
            </span>
        </td>
        <td>
            <p class="name">
                {% if (file.url) { %}
                    <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" {%=file.thumbnailUrl?'data-gallery':''%}>{%=file.name%}</a>
                {% } else { %}
                    <span>{%=file.name%}</span>
                {% } %}
            </p>
            {% if (file.error) { %}
                <div><span class="label label-danger">Error</span> {%=file.error%}</div>
            {% } %}
        </td>
        <td>
            <span class="size">{%=o.formatFileSize(file.size)%}</span>
        </td>
        <td>
            {% if (file.deleteUrl) { %}
                <button class="btn btn-danger delete" data-type="{%=file.deleteType%}" data-url="{%=file.deleteUrl%}"{% if (file.deleteWithCredentials) { %} data-xhr-fields='{"withCredentials":true}'{% } %}>
                    <i class="glyphicon glyphicon-trash"></i>
                    <span>Delete</span>
                </button>
                <input type="checkbox" name="delete" value="1" class="toggle">
            {% } else { %}
                <button class="btn btn-warning cancel">
                    <i class="glyphicon glyphicon-ban-circle"></i>
                    <span>Cancel</span>
                </button>
            {% } %}
        </td>
    </tr>
{% } %}
</script>


@endsection