@extends('layouts.app')

@section('title', 'Detail udalosti')

@section('content')

    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">

                    @include('invitation.header_event')

                    @include('components.validation')

                    <div class="ibox-title">

                        <div class="row">

                            {{Form::open(['route' => ['invitations.event.guest-new-step2.store', 'event' => $event->id], 'id' => 'form_guest_new_step_2',
                                                 'class' => "form-horizontal"])}}

                            <input type="hidden" name="form_type" value="guest_new_step_2">
                            {{Form::hidden('event_id', $event->id)}}
                            {{ csrf_field() }}
                            {{Form::hidden('user_id', Auth::user()->id)}}
                            {{Form::hidden('club_id', $event->club_id)}}

                            <h3 class="col-lg-offset-4">Existujúci hosť</h3>
                            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                <label for="email" class="col-md-4 control-label">E-Mail Address</label>
                                <div class="col-md-4">
                                    <input id="new_guest_email" type="email" class="form-control" name="email"
                                           value="{{$res['email'] }}" readonly>
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
                                <div class="col-md-4">
                                    <input id="new_guest_phone" type="text" class="form-control" name="phone" value="{{$res['phone'] }}" readonly>

                                    @if ($errors->has('phone'))
                                        <span class="help-block" id="error_guest_phone">
                                <strong>{{ $errors->first('phone') }}</strong>
                            </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group {{ $errors->has('gender') ? ' has-error' : '' }}">
                                <label class="col-sm-4 control-label">Pozvať ako</label>
                                <div class="col-sm-8 inline">
                                    <div class="col-md-4">
                                        <input type="hidden" name="invite_person" value="">
                                        <input type="radio" name="invite_person" id="invite_person" value="manager" class="form-control"   checked="checked">
                                        <label for="invite_person" class="text-normal">
                                            <strong>Manažer klubu</strong>
                                        </label>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="radio" name="invite_person" id="invite_person" value="personely" class="form-control"  @if(old('invite_person') == "personely" ) checked="checked" @endif>
                                        <label for="invite_person" class="text-normal">
                                            <strong>V mojom mene</strong>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="form-group{{ $errors->has('guest') ? ' has-error' : '' }}">
                                <label for="guest" class="col-md-4 control-label">Hosť</label>
                                <div class="col-md-8">

                                    @if(count($users_exist)>0)
                                        @foreach($users_exist as $v)
                                            <div class="row">
                                                <div class="col-md-8
                                                    @if(count($attendance) > 0 ) @foreach($attendance as $a) @if($v->id == $a->user_id)
                                                        bg-danger-light
                                                    @endif @endforeach @endif ">

                                                    <input type="radio" class="form-control" name="guest" value="{{$v->id}}"
                                                    @if(count($attendance) > 0 ) @foreach($attendance as $a) @if($v->id == $a->user_id)
                                                                disabled
                                                    @endif @endforeach @endif >

                                                    @if(count($attendance) > 0 ) @foreach($attendance as $a) @if($v->id == $a->user_id)
                                                                <h4 class="text-danger"> Tento užívateľ (hosť) je už pozvaný.</h4>
                                                    @endif @endforeach @endif

                                                    <p class="pull-righ" >
                                                    <address>
                                                        <strong>{{$v->name}} {{$v->surname}}</strong><br>
                                                        Status: {{$v->status}} (Id: {{$v->id}})<br>
                                                        {{$v->industry}}, {{$v->user_company}}<br>
                                                        {{$v->internet}}<br>
                                                        {{$v->phone}}<br>
                                                        <a href="mailto:{{$v->email}}">{{$v->email}}</a>
                                                    </address>
                                                    </p>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                    @if ($errors->has('guest'))
                                        <span class="help-block" id="error_guest_phone">
                                <strong>{{ $errors->first('guest') }}</strong>
                            </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-6 col-md-offset-4">
                                    <button type="submit" class="btn btn-primary">
                                        Odoslať pozvánku
                                    </button>
                                </div>
                            </div>
                            {{Form::close()}}
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

            $("#form_guest_new_step_2").validate({
                rules: {
                    phone: {
                        required: true,
                        minlength: 13
                    },
                    email: {
                        required: true,
                        email: true
                    },
                    guest: { required: true }

                }
            });

        });
    </script>

@endsection