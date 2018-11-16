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

                            <form id="invitation_form_step_2" class="form-horizontal" method="POST" action="{{ route('ext.invite.guest.step2.store') }}">
                                {{ csrf_field() }}
                                {{Form::hidden('user_id', $user->id)}}
                                {{Form::hidden('event_id', $event->id)}}
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
                                        <input id="event_name" type="text" class="form-control" name="event_date" value="{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $event->event_from)->format('d.m.Y,  H:i')}} - {{\Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $event->event_to)->format('H:i')}}" readonly>
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

                                <hr>

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
                                                                <input type="radio" class="form-control readonly" name="invitation_to" value="{{$v->id}}" required >
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



            $("#invitation_form_step_2").validate({
                ignore: [],
                rules: {
                    user_to: "required",
                    gender: "required",
                    invitation_to: { required: true }
                    //description: "required"
                },
                messages: {
                    "invitation_to": {
                        required: function () {
                            toastr.error('Vyberte koho chcete pozvať')
                        }
                    }

                }
//                ,submitHandler: function (form) { // for demo
//                    toastr.success('success')
//                    //return false; // for demo
//                }

            });

        });
    </script>
@endsection