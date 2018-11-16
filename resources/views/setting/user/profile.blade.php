@extends('layouts.app')

@section('title', 'Úprava profilu používateľa')

@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                    </div>
                    <div class="ibox-content">

                        @include('components.validation')

                        <form id="user_form" class="form-horizontal" method="POST" action="{{ route('setting.user.profile.save', ['id' =>  $user->id] ) }}" enctype="multipart/form-data" >
                            {{ csrf_field() }}
                            {{ method_field('PUT') }}
                            {{ Form::input('hidden','gender',$user->gender) }}

                            <div class="form-group{{ $errors->has('title_before') ? ' has-error' : '' }}">
                                <label for="title_before" class="col-md-4 control-label">Titul pred menom</label>
                                <div class="col-md-2">
                                    <input id="title_before" type="text" maxlength="10" class="form-control" name="title_before" value="{{ $user->title_before }}" autofocus>

                                    @if ($errors->has('title_before'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('title_before') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                <label for="name" class="col-md-4 control-label">Meno</label>
                                <div class="col-md-4">
                                    <input id="name" type="text" class="form-control" name="name" value="{{ $user->name  }}" >

                                    @if ($errors->has('name'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('surname') ? ' has-error' : '' }}">
                                <label for="surname" class="col-md-4 control-label">Priezvisko</label>
                                <div class="col-md-4">
                                    <input id="surname" type="text" class="form-control" name="surname" value="{{ $user->surname  }}" >
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
                                    <input id="title_after" type="text" maxlength="10" class="form-control" name="title_after" value="{{ $user->title_after  }}" >

                                    @if ($errors->has('title_after'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('title_after') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('birthday') ? ' has-error' : '' }}  date_picker_year">
                                <label for="title_after" class="col-md-4 control-label">Dátum narodenia</label>
                                <div class="col-md-2">
                                        <div class="input-group date">
                                            <span class="input-group-addon disabled" style="background-color: #eee;"><i class="fa fa-calendar disabled"></i></span>

                                            <input type="text" class="form-control"
                                                   value="{{Carbon\Carbon::createFromFormat('Y-m-d', $user->birthday)->format('d.m.Y')}}" name="birthday" >
                                        </div>
                                    @if ($errors->has('birthday'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('birthday') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                <label for="email" class="col-md-4 control-label">E-Mail Address</label>
                                <div class="col-md-4">
                                    <input id="email" type="email" class="form-control" name="email" value="{{ $user->email  }}" disabled>

                                    @if ($errors->has('email'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('phone') ? ' has-error' : '' }}">
                                <label for="phone" class="col-md-4 control-label">Telefón</label>
                                <div class="col-md-4">
                                    <input id="phone" type="text" class="form-control" name="phone" value="{{ $user->phone  }}" placeholder="+421" >

                                    @if ($errors->has('phone'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('phone') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('company') ? ' has-error' : '' }}">
                                <label for="company" class="col-md-4 control-label">Firma</label>
                                <div class="col-md-6">
                                    <input id="company" type="text" class="form-control" name="company" value="{{ $user->company  }}" required>
                                    @if ($errors->has('company'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('company') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('internet') ? ' has-error' : '' }}">
                                <label for="internet" class="col-md-4 control-label">Internet</label>
                                <div class="col-md-4">
                                    <input id="internet" type="url" class="form-control url_address" name="internet" value="{{ $user->internet }}">
                                    @if ($errors->has('internet'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('internet') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            @if(Auth::user()->id == $user->id )
                                <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                    <label for="password" class="col-md-4 control-label">Heslo</label>

                                    <div class="col-md-4">
                                        <input id="password" type="password" class="form-control" name="password" >

                                        @if ($errors->has('password'))
                                            <span class="help-block">
                                            <strong>{{ $errors->first('password') }}</strong>
                                        </span>
                                        @endif
                                        <div class="pwstrength_viewport_progress"></div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="password_confirmation" class="col-md-4 control-label">Potvrdenie hesla</label>

                                    <div class="col-md-4">
                                        <input id="password_confirmation" type="password" class="form-control" name="password_confirmation" >
                                    </div>
                                </div>
                            @endif
                            <hr>
                            <div class="form-group">
                                <label for="password_confirmation" class="col-md-4 control-label">Foto užívateľa</label>
                                <div class="col-md-4">
                                    <div class="fileinput fileinput-new" data-provides="fileinput">
                                        <div class="fileinput-new thumbnail" style="width: 200px; height: 200px;">
                                            <img data-src="holder.js/100%x100%"  alt="..." src="{!! asset('images/user') !!}/{{$user->id}}/small_sq/{!! $user->image !!}">
                                        </div>
                                        <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 300px; max-height: 200px;"></div>
                                        <div>
                                            <span class="btn btn-default btn-file">
                                                <span class="fileinput-new"><i class="fa fa-paperclip"></i> {{__('form.file_select')}}</span><span class="fileinput-exists">
                                                    <i class="fa fa-undo"></i> {{__('form.new_file')}}</span>
                                                <input type="file" name="files">
                                            </span>
                                            <a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">
                                                <i class="fa fa-trash"></i> {{__('form.delete')}}</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="form-group">
                                <div class="col-md-6 col-md-offset-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{__('form.save')}}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')

    <script src="{!! asset('js/plugins/pwstrength/pwstrength-bootstrap.js') !!}" type="text/javascript"></script>

    <script>

        $(document).ready(function (){

            $('#phone').mask('+000000000000');

            $("#user_form").validate({
                rules: {
                    phone: {
                        required: true,
                        minlength: 13
                    },
                    password_confirmation: {
                        equalTo: "#password",
                        minlength: 8
                    }
                }
            });

            "use strict";
            var options = {};
            options.ui = {
                container: "#pwd-container",
                showVerdictsInsideProgressBar: true,
                viewports: {
                    progress: ".pwstrength_viewport_progress"
                }
            };
            options.common = {
                minChar: 8,
                debug: true,
                onLoad: function () {
                    $('#messages').text('Začnite písať heslo');
                }
            };

            $(':password').pwstrength(options);


        });

    </script>
@endsection