@extends('layouts.app')

@section('title', 'Detail udalosti')

@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content p-md">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="pull-left">
                                    <div class="img">
                                        <img alt="image" class="feed-photo img-responsive" style="max-width: 80px;"  src="{!! $event->image_thumb !!}">
                                    </div>
                                </div>
                                <div class="profile-content media-body">
                                    <h3><strong>{{$event->title}} ({{Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $event->event_from)->format('d.m.Y H:i')}} - {{Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $event->event_to)->format('H:i')}})</strong></h3>
                                    <h5>{{$event->club->title}}</h5>
                                    <p><i class="fa fa-map-marker"></i> {{$event->host_name}},  {{$event->address_street}}<br>{{$event->address_psc}}, {{$event->address_city}}</p>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <p class="pull-right">
                                    <a href="{{ route('events.listing.show', ['event' => $event->id ]) }}" type="button" class="btn btn-success btn-sm m-l-sm"><i class="fa fa-edit"></i> Detail</a>
                                    <a href="{{ route('events.invitation.detail', ['event' => $event->id]) }}"  class="btn btn-default btn-facebook btn-outline"><i class="fa fa-print"></i> Pozvánky</a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-12">
                <div class="tabs-container m-b-lg">

                    @include('components.validation')

                    <ul class="nav nav-tabs">
                        <li><a data-toggle="tab" href="#tab-1" aria-expanded="true" id="tab_1"> Nový hosť</a></li>
                        <li><a data-toggle="tab" href="#tab-2" aria-expanded="false" id="tab_2"> Členovia ostatných klubov</a></li>
                        <li><a data-toggle="tab" href="#tab-3" aria-expanded="false" id="tab_3"> Hostia</a></li>
                        <li><a data-toggle="tab" href="#tab-4" aria-expanded="false" id="tab_4"> Členovia klubu</a></li>
                    </ul>

                    <div class="tab-content">
                        <div id="tab-1" class="tab-pane active">
                            <div class="panel-body">

                                <form id="new_guest_invitation_form" class="form-horizontal" method="POST" action="">
                                    {{ csrf_field() }}
                                    {{Form::hidden('user_id', Auth::user()->id)}}

                                    <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                        <label for="email" class="col-md-4 control-label">E-Mail Address</label>
                                        <div class="col-md-6">
                                            <input id="new_guest_email" type="email" class="form-control" name="email" value="{{ old('email') }}" required>

                                            @if ($errors->has('email'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('email') }}</strong>
                                                </span>
                                            @endif
                                            <span class="help-block text-danger hide font-bold" id="error_guest_email" ></span>
                                        </div>
                                    </div>
                                    <div class="form-group{{ $errors->has('phone') ? ' has-error' : '' }}">
                                        <label for="phone" class="col-md-4 control-label">Telefón</label>
                                        <div class="col-md-6">
                                            <input id="new_guest_phone" type="text" class="form-control" name="phone" value="{{ old('phone') }}" placeholder="" required>

                                            @if ($errors->has('phone'))
                                                <span class="help-block" id="error_guest_phone">
                                        <strong>{{ $errors->first('phone') }}</strong>
                                    </span>
                                            @endif
                                        </div>
                                    </div>

                                    <hr>
                                    <div class="hide">
                                        <div class="form-group {{ $errors->has('gender') ? ' has-error' : '' }}">
                                            <label class="col-sm-4 control-label">Pohlavie</label>
                                            <div class="col-sm-5 inline">
                                                <div class="col-md-4">
                                                    <input type="hidden" name="gender" value="">
                                                    <input type="radio" name="gender" id="M" value="M" class="form-control"  @if(old('gender') == "M" ) checked="checked" @endif>
                                                    <label for="gender" class="text-normal">
                                                        Muž
                                                    </label>
                                                </div>
                                                <div class="col-md-4">
                                                    <input type="radio" name="gender" id="F" value="F" class="form-control"  @if(old('gender') == "F" ) checked="checked" @endif>
                                                    <label for="gender" class="text-normal">
                                                        Žena
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group{{ $errors->has('title_before') ? ' has-error' : '' }}">
                                            <label for="title_before" class="col-md-4 control-label">Titul pred menom</label>
                                            <div class="col-md-2">
                                                <input id="title_before" type="text" maxlength="10" class="form-control" name="title_before" value="{{ old('title_before') }}" autofocus>

                                                @if ($errors->has('title_before'))
                                                    <span class="help-block">
                                            <strong>{{ $errors->first('title_before') }}</strong>
                                        </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                            <label for="name" class="col-md-4 control-label">Meno</label>
                                            <div class="col-md-6">
                                                <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" required>

                                                @if ($errors->has('name'))
                                                    <span class="help-block">
                                            <strong>{{ $errors->first('name') }}</strong>
                                        </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="form-group{{ $errors->has('surname') ? ' has-error' : '' }}">
                                            <label for="surname" class="col-md-4 control-label">Priezvisko</label>
                                            <div class="col-md-6">
                                                <input id="surname" type="text" class="form-control" name="surname" value="{{ old('surname') }}" required>
                                                @if ($errors->has('surname'))
                                                    <span class="help-block">
                                            <strong>{{ $errors->first('surname') }}</strong>
                                        </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="form-group{{ $errors->has('title_after') ? ' has-error' : '' }}">
                                            <label for="title_after" class="col-md-4 control-label">Titul za menom</label>
                                            <div class="col-md-2">
                                                <input id="title_after" type="text" maxlength="10" class="form-control" name="title_after" value="{{ old('title_after') }}">

                                                @if ($errors->has('title_after'))
                                                    <span class="help-block">
                                            <strong>{{ $errors->first('title_after') }}</strong>
                                        </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="form-group{{ $errors->has('industry') ? ' has-error' : '' }}">
                                            <label for="industry" class="col-md-4 control-label">Typ podnikania</label>
                                            <div class="col-md-4">

                                                <select class="form-control chosen-select" name="industry" required>
                                                    <option value="">-- výber --</option>
                                                    @foreach($industry as $in)
                                                        <option value="{{$in->id}}" @if(old('industry') == $in->id ) selected="selected" @endif>{{$in->name}}</option>
                                                    @endforeach
                                                </select>

                                                @if ($errors->has('industry'))
                                                    <span class="help-block">
                                            <strong>{{ $errors->first('industry') }}</strong>
                                        </span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-group{{ $errors->has('internet') ? ' has-error' : '' }}">
                                            <label for="internet" class="col-md-4 control-label">Internet</label>
                                            <div class="col-md-6">
                                                <input id="internet" type="url" class="form-control url_address" name="internet" value="{{ old('internet') }}" placeholder="http://" required>
                                                @if ($errors->has('internet'))
                                                    <span class="help-block">
                                            <strong>{{ $errors->first('internet') }}</strong>
                                        </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="form-group{{ $errors->has('company') ? ' has-error' : '' }}">
                                            <label for="company" class="col-md-4 control-label">Firma</label>
                                            <div class="col-md-6">
                                                <input id="company" type="text" class="form-control" name="company" value="{{ old('company') }}" required>
                                                @if ($errors->has('company'))
                                                    <span class="help-block">
                                            <strong>{{ $errors->first('company') }}</strong>
                                        </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                                            <label for="description" class="col-md-4 control-label">Doplňujúci text pozvánky</label>
                                            <div class="col-md-6">
                                                <textarea id="description" class="form-control" name="description" rows="4">{{ old('description') }}</textarea>
                                                @if ($errors->has('description'))
                                                    <span class="help-block">
                                            <strong>{{ $errors->first('description') }}</strong>
                                        </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div> <!-- hide  block -->

                                    <div class="form-group">
                                            <div class="col-md-6 col-md-offset-4">
                                                <button type="submit" class="btn btn-primary">
                                                    Odoslať pozvánku na raňajky
                                                </button>
                                            </div>
                                        </div>
                                </form>
                            </div>
                        </div>
                        <div id="tab-2" class="tab-pane">
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-sm-3 m-b-xs">

                                        {{Form::open(['route' => 'events.invitation.detail.store' ,
                                            'id' => 'item-del-'. $event->id  ])}}
                                        {{Form::hidden('event_id', $event->id)}}
                                        {{Form::hidden('form_type','club_select')}}

                                        <select class="input form-control input-s-sm inline" name="club" onchange="this.form.submit()">
                                            <option value="0">-- Všetky kluby --</option>
                                            @if($clubs)
                                                @foreach($clubs as $c)
                                                    <option value="{{$c->id}}" id="{{$c->id}}" @if( Session::get('events_invitation_filter_club') == $c->id) selected="selected" @endif>{{$c->title}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        {{Form::close()}}
                                    </div>
                                </div>

                                @if(count($clubs_user)>0)
                                    {{Form::open(['route' => 'events.invitation.detail.store' ,
                                            'id' => 'item-del-'. $event->id  ])}}
                                    {{Form::hidden('form_type','others_member')}}
                                    {{Form::hidden('event_id', $event->id)}}
                                    @foreach($clubs_user as $item => $cu)
                                        @if(count($cu['users']) > 0 )
                                        <div class="table-responsive m-b-lg">
                                            <table class="table table-striped">
                                                <thead>
                                                <tr>
                                                    <th width="5%"><input type="checkbox" class="i-checks select_all" name="input[]"></th>
                                                    <th width="200px" align="left" class="pull-left">{{$cu['club']}}</th>
                                                    <th>Telefon </th>
                                                    <th>Stav</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @if($cu['users'])
                                                @foreach($cu['users'] as $item => $v)
                                                    <tr>
                                                        <td><input type="checkbox"  class="i-checks" name="user[]" value="{{$v->id}}"
                                                                   @if($attendance) @foreach($attendance as $a)
                                                                   @if($a->user_id == $v->id) disabled checked="checked" @endif @endforeach @endif
                                                            >
                                                        </td>
                                                        <td>@if($v->name ){{ $v->name }} {{ $v->surname }}@else Hosť @endif</td>
                                                        <td>{{ $v->phone }}</td>
                                                        <td>
                                                            @if($attendance) @foreach($attendance as $a)
                                                                @if($a->user_id == $v->id)
                                                                    <i class="fa fa-check-circle-o"></i> Užívateľ je už pozvaný
                                                                @endif
                                                            @endforeach @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                @endif
                                                </tbody>
                                            </table>
                                        </div>
                                        @endif
                                    @endforeach
                                    <button type="submit" class="btn btn-primary btn-sm send-alert"><i class="fa fa-send"></i> Odoslať pozvánku</button>
                                    {{Form::close()}}
                                @else
                                    <p class="text-danger">Neexistujú žiadne záznamy</p>
                                @endif

                            </div>
                        </div>
                        <div id="tab-3" class="tab-pane">
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-sm-3 m-b-xs">

                                        {{Form::open(['route' => 'events.invitation.detail.store' ,
                                            'id' => 'item-del-'. $event->id  ])}}
                                        {{Form::hidden('event_id', $event->id)}}
                                        {{Form::hidden('form_type','guest_status')}}

                                        <select class="input form-control input-s-sm inline" name="user_status" onchange="this.form.submit()">
                                            <option value="0">-- Všetky stavy --</option>
                                            @if($user_statuses)
                                                @foreach($user_statuses as $us)
                                                    <option value="{{$us->id}}" id="{{$us->id}}" @if( Session::get('events_invitation_filter_status') == $us->id) selected="selected" @endif>{{$us->status}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        {{Form::close()}}
                                    </div>
                                </div>
                                @if(count($club_guests) >0)
                                    <div class="table-responsive">
                                        {{Form::open(['route' => 'events.invitation.detail.store' ,
                                                        'id' => 'item-del-'. $event->id  ])}}
                                        {{Form::hidden('form_type','club_guests')}}
                                        {{Form::hidden('event_id', $event->id)}}

                                        <table class="table table-striped">
                                            <thead>
                                            <tr>
                                                <th width="5%"><input type="checkbox"  class="i-checks select_all" ></th>
                                                <th> Meno</th>
                                                <th>Telefon</th>
                                                <th>Stav</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($club_guests as $item => $v)
                                                <tr>
                                                    <td><input type="checkbox"  class="i-checks" name="user[]" value="{{$v->id}}"
                                                               @if($attendance) @foreach($attendance as $a)
                                                               @if($a->user_id == $v->id) disabled checked="checked" @endif @endforeach @endif
                                                        >
                                                    </td>
                                                    <td>@if($v->name ){{ $v->name }} {{ $v->surname }}@else Hosť @endif</td>
                                                    <td>{{ $v->phone }} - {{ $v->status_name }}</td>
                                                    <td>
                                                        @if($attendance) @foreach($attendance as $a)
                                                            @if($a->user_id == $v->id)
                                                                <i class="fa fa-check-circle-o"></i> Užívateľ je už pozvaný
                                                            @endif
                                                        @endforeach @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                        <button type="submit" class="btn btn-primary btn-sm send-alert"><i class="fa fa-send"></i> Odoslať pozvánku</button>
                                        {{Form::close()}}

                                        {{ $club_guests->links()->rou }}

                                    </div>
                                @else
                                    <p class="text-danger">Neexistujú žiadne záznamy</p>
                                @endif
                            </div>
                        </div>
                        <div id="tab-4" class="tab-pane">
                            <div class="panel-body">
                                @if(count($club_members) >0)
                                    <div class="table-responsive">
                                        {{Form::open(['route' => 'events.invitation.detail.store' ,
                                                        'id' => 'item-del-'. $event->id  ])}}
                                        {{Form::hidden('form_type','own_member')}}
                                        {{Form::hidden('event_id', $event->id)}}

                                        <table class="table table-striped">
                                            <thead>
                                            <tr>
                                                <th width="5%"><input type="checkbox"  class="i-checks select_all" ></th>
                                                <th> Meno</th>
                                                <th>Telefon</th>
                                                <th>Stav</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($club_members as $item => $v)
                                                <tr>
                                                    <td><input type="checkbox"  class="i-checks" name="user[]" value="{{$v->id}}"
                                                               @if($attendance) @foreach($attendance as $a)
                                                               @if($a->user_id == $v->id) disabled checked="checked" @endif @endforeach @endif
                                                        >
                                                    </td>
                                                    <td>@if($v->name ){{ $v->name }} {{ $v->surname }}@else Hosť @endif</td>
                                                    <td>{{ $v->phone }}</td>
                                                    <td>
                                                        @if($attendance) @foreach($attendance as $a)
                                                            @if($a->user_id == $v->id)
                                                                <i class="fa fa-check-circle-o"></i> Užívateľ je už pozvaný
                                                            @endif
                                                        @endforeach @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                        <button type="submit" class="btn btn-primary btn-sm send-alert"><i class="fa fa-send"></i> Odoslať pozvánku</button>
                                        {{Form::close()}}

                                    </div>
                                @else
                                    <p class="text-danger">Neexistujú žiadne záznamy</p>
                                @endif
                            </div>
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

    <script>
        jQuery(window).ready(function (){

            $.validator.setDefaults({ ignore: ":hidden:not(.chosen-select)" });

            $('#new_guest_phone').mask('+000000000000');

//            $("#new_guest_invitation_form").validate({
//                rules: {
//                    new_guest_email: "required",
//                    new_guest_phone: "required",
//                    gender: "required"
//                }
//            });

            $('#new_guest_email').delayKeyup(function(){
                var email = $('#new_guest_email').val();
                var phone = $('#new_guest_phone').val();

                    $('#new_guest_email').filter(function(){

                        var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
                        if( !emailReg.test( email ) ) {

                            //zmazeme nespravny email
                            $('#error_guest_email').html('Nesprávny formát emailu').removeClass('hide').addClass('show');
                            $('#new_guest_email').val("").focus();


                            toastr.warning('Please enter valid email');


                        } else {

                            $('#error_guest_email').removeClass('show').addClass('hide');


                            toastr.success('Thank you for your valid email');
                        }
                    })

                //});
            //});


//                    $.ajax({
//                        type:'POST',
//                        url:'/ajax/get-company-data',
//                        data: { email: id},
//                        dataType: 'json',
//                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
//                        success:function(data){
//                            if(data.status == 'true'){
//                                $('#email').val(data.data.email);
//                            } else {
//                                $('#email').val("");
//                            }
//                        }
//                    });

                toastr.success($('#new_guest_email').val());

            }, 3000);





            var tab = param('tab');
            if ( tab == "") tab = 1;
            $('#tab_'+tab).tab('show');

            $('.i-checks').iCheck({
                checkboxClass: 'icheckbox_square-green',
                radioClass: 'iradio_square-green'
            });


            $('.select_all').on('ifChecked', function(event){
                $(this).closest('table').find('td input:checkbox').iCheck('check')
            });

            $('.select_all').on('ifUnchecked', function(event){
                $(this).closest('table').find('td input:checkbox').iCheck('uncheck')
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


            function param(name) {
                return (location.search.split(name + '=')[1] || '').split('&')[0];
            }


        });
    </script>

@endsection