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

                        <form id="bug_report_add" class="form-horizontal" method="POST" action="{{ route('developer.bug-report.store') }}" enctype="multipart/form-data" >
                            {{ csrf_field() }}
                            {{Form::hidden('bug_report_route', Request::getUri() )}}

                            <div class="form-group{{ $errors->has('bug_type') ? ' has-error' : '' }}">
                                <label for="bug_type" class="col-md-4 control-label">Typ podnetu</label>
                                <div class="col-md-4">
                                    <select name="bug_type" class="form-control" required>
                                        <option value="">-- výber --</option>
                                        @foreach( __('constant.bug_report_type') as $k => $s)
                                            <option value="{{$k}}">{{$s}}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('bug_type'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('bug_type') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('ico') ? ' has-error' : '' }}">
                                <label for="description" class="col-md-4 control-label">Popis chyby</label>
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
                                <label for="password_confirmation" class="col-md-4 control-label">Screenshot podnetu </label>
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

@section('css')

    <link href="{!! asset('css/plugins/nouslider/jquery.nouislider.css') !!}" rel="stylesheet">


@endsection

@section('scripts')


    <script src="{!! asset('js/plugins/fullcalendar/fullcalendar.min.js') !!}" type="text/javascript"></script>
    <!-- TouchSpin -->
    <script src="{!! asset('js/plugins/touchspin/jquery.bootstrap-touchspin.min.js') !!}" type="text/javascript"></script>
    <!-- NouSlider -->
    <script src="{!! asset('js/plugins/nouslider/jquery.nouislider.min.js') !!}" type="text/javascript"></script>


    <script>

        $(document).ready(function(){

            $("#bug_report_add").validate({
                rules: {
                    bug_type: "required"
                }
            });


        });

    </script>

@endsection