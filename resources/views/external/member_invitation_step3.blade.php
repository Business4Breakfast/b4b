@extends('layouts.app_public')

@section('title', '')

@section('content')

    <div class="wrapper wrapper-content">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 col-lg-offset-2">

                    <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <h3 class="font-bold no-margins">
                                Prihláška hosťa na podnikateľské raňajky
                            </h3>
                        </div>
                        <div class="ibox-content">

                            @include('components.validation')

                            <form id="invitation_form" class="form-horizontal" method="POST" action="{{ route('ext.invite.guest.step3.store') }}">
                                {{ csrf_field() }}
                                {{Form::hidden('user_id', $user->id)}}
                                {{Form::hidden('event_id', $event->id)}}
                                {{Form::hidden('order_token', $res['order_token'])}}
                                {{Form::hidden('created_user', $user->id)}}


                                <div class="form-group">
                                    <div class="col-md-6 col-md-offset-4">
                                        <a  href="{{route('ext.invite.guest.step1', $res['order_token'])}}"  class="btn btn-success">
                                            Späť na pozvánku
                                        </a>
                                    </div>
                                </div>

                                <div class="form-group{{ $errors->has('invite_person') ? ' has-error' : '' }}">
                                    <label for="invite_person" class="col-md-4 control-label">Pozývajúci člen</label>
                                    <div class="col-md-6">
                                        <input id="invite_person" type="text" class="form-control" name="invite_person" value="{{$user->full_name}}" readonly>
                                        @if ($errors->has('invite_person'))
                                            <span class="help-block">
                                        <strong>{{ $errors->first('invite_person') }}</strong>
                                    </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group{{ $errors->has('event_name') ? ' has-error' : '' }}">
                                    <label for="event_name" class="col-md-4 control-label">Udalosť</label>
                                    <div class="col-md-6">
                                        <input id="event_name" type="text" class="form-control" name="event_name" value="{{$event->title}}" readonly>
                                        @if ($errors->has('event_name'))
                                            <span class="help-block">
                                        <strong>{{ $errors->first('event_name') }}</strong>
                                    </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group{{ $errors->has('event_name') ? ' has-error' : '' }}">
                                    <label for="event_name" class="col-md-4 control-label">Dátum</label>
                                    <div class="col-md-6">
                                        <input id="event_name" type="text" class="form-control" name="event_name" value="{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $event->event_from)->format('d.m.Y,  H:i')}} - {{\Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $event->event_to)->format('H:i')}}" readonly>
                                        @if ($errors->has('event_name'))
                                            <span class="help-block">
                                        <strong>{{ $errors->first('event_name') }}</strong>
                                    </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group{{ $errors->has('event_name') ? ' has-error' : '' }}">
                                    <label for="event_name" class="col-md-4 control-label">Klub</label>
                                    <div class="col-md-6">
                                        <input id="event_name" type="text" class="form-control" name="event_name" value="{{$event->club->title}}" readonly>
                                        @if ($errors->has('event_name'))
                                            <span class="help-block">
                                        <strong>{{ $errors->first('event_name') }}</strong>
                                    </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group{{ $errors->has('invitation_to') ? ' has-error' : '' }}">
                                    <label for="invitation_to" class="col-md-4 control-label">Hosť</label>
                                    <div class="col-md-8">

                                        @php  $input = 0; @endphp

                                        @if(count($users_exist)>0)
                                            @foreach($users_exist as $v)
                                                <div class="row">
                                                    <div class="col-md-9">
                                                        {{--<div class=" p-m @if(count($attendance) > 0 ) @foreach($attendance as $a) @if($v->id == $a->user_id)--}}
                                                        {{--bg-warning-light @endif @endforeach @endif">--}}

                                                        <div class=" p-m @if($v->attend > 0) bg-warning-light @endif ">

                                                            @if($v->attend > 0)
                                                                <h4 class="text-info"> Tento užívateľ (hosť) je už pozvaný.</h4>
                                                            @else
                                                                <input type="radio" class="form-control readonly" name="invitation_to" value="{{$v->id}}" >
                                                                @php  $input = 1; @endphp
                                                            @endif

                                                            <p class="pull-righ" >
                                                            <address>
                                                                <strong>{{$v->name}} {{$v->surname}}</strong><br>
                                                                Status: {{$v->status}} (Id: {{$v->id}})<br>
                                                                {{$v->industry}}, {{$v->user_company}}<br>
                                                                {{$v->internet}}<br>
                                                                {{ maskPhone($v->phone)}}<br>
                                                                {{ maskEmail($v->email) }}
                                                            </address>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                        @if ($errors->has('invitation_to'))
                                            <span class="help-block" id="error_guest_phone">
                                                <strong>{{ $errors->first('invitation_to') }}</strong>
                                            </span>
                                        @endif

                                        {{--Zobrazime hidden radioo len ak nie je ziadny user na vyber--}}
                                        @if($input == 0)
                                            <input type="hidden"  name="invitation_to" value="">
                                        @endif

                                    </div>
                                </div>

                                <hr>
                                <div class="form-group {{ $errors->has('gender') ? ' has-error' : '' }}">
                                    <label class="col-sm-4 control-label">Pohlavie</label>
                                    <div class="col-sm-5 inline">
                                        <div class="col-md-4">
                                            <input type="hidden" name="gender" value="">
                                            <input type="radio" name="gender" id="M" value="M" class="form-control"  @if($res['gender'] == "M" ) checked="checked" @endif>
                                            <label for="gender" class="text-normal">
                                                Muž
                                            </label>
                                        </div>
                                        <div class="col-md-4">
                                            <input type="radio" name="gender" id="F" value="F" class="form-control"  @if( $res['gender'] == "F" ) checked="checked" @endif>
                                            <label for="gender" class="text-normal">
                                                Žena
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group{{ $errors->has('title_before') ? ' has-error' : '' }}">
                                    <label for="title_before" class="col-md-4 control-label">Titul pred menom</label>
                                    <div class="col-md-2">
                                        <input id="title_before" type="text" maxlength="10" class="form-control" name="title_before" value="{{ $res['title_before'] }}" autofocus>

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
                                        <input id="name" type="text" class="form-control" name="name" value="{{ $res['name'] }}" required readonly="">

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
                                        <input id="surname" type="text" class="form-control" name="surname" value="{{ $res['surname'] }}" required readonly="">
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
                                        <input id="title_after" type="text" maxlength="10" class="form-control" name="title_after" value="{{ $res['title_after'] }}">

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

                                        <select class="form-control chosen-select-200" name="industry" class="form-control" required>
                                            <option value="">-- výber --</option>
                                            @foreach($industry as $in)
                                                <option value="{{$in->id}}" @if( $res['industry'] == $in->id ) selected="selected" @endif>{{$in->name}}</option>
                                            @endforeach
                                        </select>

                                        @if ($errors->has('industry'))
                                            <span class="help-block">
                                        <strong>{{ $errors->first('industry') }}</strong>
                                    </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                    <label for="email" class="col-md-4 control-label">E-Mail Address</label>
                                    <div class="col-md-6">
                                        <input id="email" type="email" class="form-control" name="email" value="{{ $res['email'] }}" required readonly>

                                        @if ($errors->has('email'))
                                            <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group{{ $errors->has('phone') ? ' has-error' : '' }}">
                                    <label for="phone" class="col-md-4 control-label">Telefón</label>
                                    <div class="col-md-6">
                                        <input id="phone" type="text" class="form-control" name="phone" value="{{ $res['phone'] }}" placeholder="" required readonly>

                                        @if ($errors->has('phone'))
                                            <span class="help-block">
                                        <strong>{{ $errors->first('phone') }}</strong>
                                    </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group{{ $errors->has('internet') ? ' has-error' : '' }}">
                                    <label for="internet" class="col-md-4 control-label">Internet</label>
                                    <div class="col-md-6">
                                        <input id="internet" type="url" class="form-control url_address" name="internet" value="{{ $res['internet']}}" placeholder="http://" >
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
                                        <input id="company" type="text" class="form-control" name="company" value="{{ $res['company'] }}" required>
                                        @if ($errors->has('company'))
                                            <span class="help-block">
                                        <strong>{{ $errors->first('company') }}</strong>
                                    </span>
                                        @endif
                                    </div>
                                </div>
                                <hr>
                                <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                                    <label for="description" class="col-md-4 control-label">Doplňujúci text pozvánky</label>
                                    <div class="col-md-6">
                                        <textarea id="description" class="form-control" name="description" rows="4">{{ $res['description'] }}</textarea>
                                        @if ($errors->has('description'))
                                            <span class="help-block">
                                        <strong>{{ $errors->first('description') }}</strong>
                                    </span>
                                        @endif
                                    </div>
                                </div>
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

                </div>

            </div>
        </div>
    </div>

@endsection

@section('scripts')

    <script>
        $(document).ready(function (){

            $('#phone').mask('+000000000000');

            $.validator.setDefaults({ ignore: ":hidden:not(.chosen-select)" });

            $("#invitation_form").validate({
                rules: {
                    name: "required",
                    gender: "required"
                }
            });

        });
    </script>
@endsection