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

                        <form id="user_form" class="form-horizontal" method="POST" action="{{ route('setting.'.$module.'.store') }}" enctype="multipart/form-data" >
                            {{ csrf_field() }}

                            <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                <label for="name" class="col-md-4 control-label">Názov</label>
                                <div class="col-md-4">
                                    <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" required autofocus>
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
                                    <input id="color" type="text" class="form-control spectrum_picker" name="color" value="{{ old('color') }}" required >
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
                                    <textarea id="description" type="text" class="form-control" name="description" rows="6" required>{{ old('description') }}</textarea>
                                    @if ($errors->has('description'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('description') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="password_confirmation" class="col-md-4 control-label">Titulný obrázok udalosti</label>
                                <div class="col-md-4">
                                    <div class="fileinput fileinput-new" data-provides="fileinput">
                                        <div class="fileinput-new thumbnail" style="max-width: 500px; max-height: 500px;">
                                            <p class="text-center m-t-md">
                                                <i class="fa fa-upload big-icon"></i>
                                            </p>
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
                                        {{__('form.add_record')}}
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
    <script>
        $(document).ready(function (){

            $("#name").on('blur keyup change click input', function () {
                var txtClone = $('#name').val();
                // first capital letter
                txtClone = txtClone.charAt(0).toUpperCase() + txtClone.substr(1);
                $('#description').val(ltrim(txtClone));
            });

            $("#user_form").validate({
                rules: {
                    name: "required"
                }
            });

        });
    </script>
@endsection