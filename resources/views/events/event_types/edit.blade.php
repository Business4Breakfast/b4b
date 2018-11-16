@extends('layouts.app')

@section('title', '')

@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title"></div>
                    <div class="ibox-content">

                        @include('components.validation')

                        {{--<form id="user_form" class="form-horizontal" method="POST" action="{{ route('setting.'.$module.'.update') }}">--}}

                        {{ Form::open(['method' => 'PUT', 'route' => ['setting.'.$module.'.update', $item->id ],
                           'class' => 'form-horizontal', 'id' => 'edit-form', 'enctype' => 'multipart/form-data'  ])
                        }}

                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label for="name" class="col-md-4 control-label">Názov</label>
                            <div class="col-md-4">
                                <input id="name" type="text" class="form-control" name="name" value="{{ $item->name }}" required autofocus>
                                @if ($errors->has('name'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('name') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('color') ? ' has-error' : '' }}">
                            <label for="name" class="col-md-4 control-label">Farba </label>
                            <div class="col-md-4">
                                <input id="color" type="text" class="form-control spectrum_picker" name="color" value="{{ $item->color }}" required >
                                @if ($errors->has('color'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('name') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('ico') ? ' has-error' : '' }}">
                            <label for="description" class="col-md-4 control-label">Popis</label>
                            <div class="col-md-8">
                                <textarea id="description" type="text" class="form-control" name="description" rows="6" required>{{ $item->description }}</textarea>
                                @if ($errors->has('description'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('description') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Nastavenie hostí</label>
                            <div class="col-sm-5">
                                <div class="checkbox">
                                    <input type="hidden" name="invite_guests" value="0">
                                    <input type="checkbox" name="invite_guests" value="1" class="form-control" @if($item->invite_guests )checked="checked" @endif>
                                    <label for="checkbox1">
                                        Povoliť pozývať hostí (verejná udalosť)
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <input type="hidden" name="change_status_guest" value="0">
                                    <input type="checkbox" name="change_status_guest" value="1" class="form-control" @if($item->change_status_guest )checked="checked" @endif>
                                    <label for="checkbox1">
                                        Zmenu statusu hosťa po uzávierke
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Nastavenie členov</label>
                            <div class="col-sm-5">
                                <div class="checkbox">
                                    <input type="hidden" name="invite_own_club" value="0">
                                    <input type="checkbox" name="invite_own_club" value="1"  id="invite_own_club" class="form-control" checked="checked" @if($item->invite_own_club )checked="checked" @endif>
                                    <label for="checkbox1">
                                        Pozývať len členov klubu vlastného klubu
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <input type="hidden" name="invite_other_executives" value="0">
                                    <input type="checkbox" name="invite_other_executives" id="invite_other_executives" value="1" class="form-control" @if($item->invite_other_executives )checked="checked" @endif>
                                    <label for="checkbox1">
                                        Pozývať členov výkonných tímov iných klubov
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <input type="hidden" name="invite_other_clubs" value="0">
                                    <input type="checkbox" name="invite_other_clubs" id="invite_other_clubs" value="1" class="form-control" @if($item->invite_other_clubs )checked="checked" @endif>
                                    <label for="checkbox1">
                                        Pozývať členov iných klubov
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Nastavenie tlačítok</label>
                            <div class="col-sm-5">
                                <div class="checkbox">
                                    <input type="hidden" name="btn_invite_guest" value="0">
                                    <input type="checkbox" name="btn_invite_guest" value="1"  id="btn_invite_guest" class="form-control"  @if($item->btn_invite_guest )checked="checked" @endif>
                                    <label for="checkbox1">
                                        Tlačítko pozvať hosťa
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <input type="hidden" name="btn_confirm_attend" value="0">
                                    <input type="checkbox" name="btn_confirm_attend" id="btn_confirm_attend" value="1" class="form-control" @if($item->btn_confirm_attend )checked="checked" @endif>
                                    <label for="checkbox1">
                                        Tlačítko potvrdiť účasť
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <input type="hidden" name="btn_refused_attend" value="0">
                                    <input type="checkbox" name="btn_refused_attend" id="btn_refused_attend" value="1" class="form-control" @if($item->btn_refused_attend )checked="checked" @endif>
                                    <label for="checkbox1">
                                        Tlačítko ospravedlniť účasť
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <input type="hidden" name="btn_deleted_guest" value="0">
                                    <input type="checkbox" name="btn_deleted_guest" id="btn_deleted_guest" value="1" class="form-control" @if($item->btn_deleted_guest )checked="checked" @endif>
                                    <label for="checkbox1">
                                        Tlačítko SPAM odhlásiť
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Nastavenie Email šablóny</label>
                            <div class="col-sm-5">
                                <div class="checkbox">
                                    <input type="hidden" name="email_text_custom" value="0">
                                    <input type="checkbox" name="email_text_custom" value="1"  id="email_text_custom" class="form-control"  @if($item->email_text_custom )checked="checked" @endif>
                                    <label for="checkbox1">
                                        Nozobrazovať preddefinované texty
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="password_confirmation" class="col-md-4 control-label">Titulný obrázok udalosti</label>
                            <div class="col-md-4">
                                <div class="fileinput fileinput-new" data-provides="fileinput">
                                    <div class="fileinput-new thumbnail" style="max-width: 500px; max-height: 500px;">
                                        @if(file_exists('images/event-type/' . $item->id . '/' .$item->image))
                                            <img data-src="holder.js/100%x100%"  alt="..." src="{!! asset('images/event-type') !!}/{{$item->id}}/{!! $item->image !!}">
                                        @else
                                            <p class="text-center m-t-md">
                                                <i class="fa fa-upload big-icon"></i>
                                            </p>
                                        @endif
                                    </div>
                                    <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 500px; max-height: 500px;"></div>
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
                                    {{__('form.edit_record')}}
                                </button>
                            </div>
                        </div>
                        {{Form::close()}}
                        {{--</form>--}}
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection

@section('scripts')

    <script>
        $(document).ready(function (){

            $("#edit-form").validate({
                rules: {
                    name: "required"
                }
            });

        });
    </script>
@endsection