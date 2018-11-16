@extends('layouts.app')

@section('title', '')

@section('content')

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h3>Email notification - detail</h3>
                </div>
                <div class="ibox-content">

                @include('components.validation')

                <form id="manual_files_add" class="form-horizontal" method="POST" action="{{ route('developer.email-notifications.update', $item->id) }}" >
                    {{ csrf_field() }}
                    {{ method_field('PATCH') }}
                    <input type="hidden" id="div_code" name="data">

                <div class="form-group">
                    <div class="col-md-6 col-md-offset-3">
                        <button type="submit" class="btn btn-primary">
                            {{__('form.edit_record')}}
                        </button>
                    </div>
                </div>
                <div class="form-group{{ $errors->has('subject') ? ' has-error' : '' }}">
                    <label for="title" class="col-md-3 control-label">Subject</label>
                    <div class="col-md-6">
                        <input type="text" name="subject" value="{{$item->subject}}" class="form-control" >
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label">Status</label>
                    <div class="col-sm-4">
                        <select class="form-control" name="status" required>
                            <option value="0"  @if($item->status  == 0)  selected="selected" @endif >Waiting</option>
                            <option value="1"  @if($item->status  == 1)  selected="selected" @endif >Sent</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label">Send imediately</label>
                    <div class="col-sm-4">
                        <input type="checkbox" name="send_imediately" checked="checked" value="1">
                    </div>
                </div>
                <div class="form-group{{ $errors->has('subject') ? ' has-error' : '' }}">
                    <label for="title" class="col-md-3 control-label">Recipients</label>
                    <div class="col-md-6">
                        <textarea rows="3" name="recipients" class="form-control" >{{$item->recipients}}</textarea>
                    </div>
                </div>
                <div class="form-group{{ $errors->has('date_send') ? ' has-error' : '' }}">
                    <label for="title" class="col-md-3 control-label">Send date</label>
                    <div class="col-md-4">
                        <input type="text" name="date_send" value="{{$item->date_send}}" class="form-control" >
                    </div>
                </div>
                    <div class="form-group{{ $errors->has('updated_at') ? ' has-error' : '' }}">
                    <label for="title" class="col-md-3 control-label">Update date</label>
                    <div class="col-md-4">
                        <input type="text" name="updated_at" value="{{$item->updated_at}}" class="form-control" >
                    </div>
                </div>
                <div class="form-group{{ $errors->has('created_at') ? ' has-error' : '' }}">
                    <label for="title" class="col-md-3 control-label">Update date</label>
                    <div class="col-md-4">
                        <input type="text" name="created_at" value="{{$item->created_at}}" class="form-control" >
                    </div>
                </div>
                <div class="form-group{{ $errors->has('module') ? ' has-error' : '' }}">
                    <label for="title" class="col-md-3 control-label">Module</label>
                    <div class="col-md-2">
                        <input type="text" name="module" value="{{$item->module}}" class="form-control" >
                    </div>
                </div>
                <div class="form-group{{ $errors->has('module_id') ? ' has-error' : '' }}">
                    <label for="title" class="col-md-3 control-label">Module id</label>
                    <div class="col-md-2">
                        <input type="number" name="module_id" value="{{$item->module_id}}" class="form-control" >
                    </div>
                </div>
                <div class="form-group{{ $errors->has('html') ? ' has-error' : '' }}">
                    <label for="title" class="col-md-3 control-label">Text spravy</label>
                    <div class="col-md-6">
                        <textarea  name="text" rows="5" class="form-control" >{{$item->text}}</textarea>
                    </div>
                </div>
                <div class="form-group{{ $errors->has('html') ? ' has-error' : '' }}">
                    <label for="title" class="col-md-3 control-label">HTML</label>
                    <div class="col-md-6">
                        <textarea  name="html" rows="5" class="form-control" >{{$item->html}}</textarea>
                    </div>
                </div>
                <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
                    <label for="title" class="col-md-3 control-label">Data view</label>
                    <div class="col-md-8">
                        <div id="json-collapsed"></div>
                    </div>
                </div>
                <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
                    <label for="title" class="col-md-3 control-label">Data edit</label>
                    <div class="col-md-8">
                        <div id="json_code_editor" style="height: 300px;"></div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-6 col-md-offset-3">
                        <button type="submit" class="btn btn-primary">
                            {{__('form.edit_record')}}
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

    <link href="{!! asset('css/plugins/json-view/jquery.jsonview.min.css') !!}" rel="stylesheet">

    <link href="{!! asset('css/plugins/codemirror/codemirror.css') !!}" rel="stylesheet">
    <link href="{!! asset('css/plugins/codemirror/ambiance.css') !!}" rel="stylesheet">

    <link href="{!! asset('css/plugins/json-editor/jsoneditor.css') !!}" rel="stylesheet">

@endsection

@section('scripts')

    <script src="{!! asset('js/plugins/json-view/jquery.jsonview.min.js') !!}" type="text/javascript"></script>

    <!-- CodeMirror -->
    <script src="{!! asset('js/plugins/codemirror/codemirror.js') !!}" type="text/javascript"></script>
    <script src="{!! asset('js/plugins/codemirror/mode/javascript/javascript.js') !!}" type="text/javascript"></script>

    <script src="{!! asset('js/plugins/json-editor/jsoneditor.js') !!}" type="text/javascript"></script>

    <script>

        $(document).ready(function(){


            var json = {!! $item->data !!};

            var container = document.getElementById('json_code_editor');

            var options = {
                "mode": "view",
                "modes": [
                    "tree",
                    "form",
                    "code",
                    "text",
                    "view"
                ],
                "history": true,
                onChange: function () {
                    if (editor) {
                        // if you comment out the next line of code, the problem is solved
                        // editor.get() throws an exception when the editor does not
                        // contain valid JSON.

                        // get data from editor
                        var json_data = editor.get();
                        var jsonConvertedData = JSON.stringify(json_data);  // Convert to json

                        $('#div_code').val(jsonConvertedData);

                    }
                }
            };

            var editor = new JSONEditor(container, options);

            //inicializacia editora
            editor.set(json);

            // get data from editor
            var json_data = editor.get();
            var jsonConvertedData = JSON.stringify(json_data);  // Convert to json
            $('#div_code').val(jsonConvertedData);

            $(function() {
                $("#json").JSONView(json);

                $("#json-collapsed").JSONView(json, { collapsed: true, nl2br: true, recursive_collapser: true });

            });

//
//            $("#manual_files_add").validate({
//                rules: {
//                    title: "required"
//                }
//            });

        });

    </script>

@endsection