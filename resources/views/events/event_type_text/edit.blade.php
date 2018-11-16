@extends('layouts.app')

@section('title', '')

@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content">

                        @include('components.validation')

                        {{ Form::open(['method' => 'PUT', 'files' => true,  'route' => ['setting.'.$module.'.update', $item->id ],
                           'class' => 'form-horizontal', 'id' => 'edit-form' ])
                        }}

                            <div class="form-group">
                                <label class="col-sm-4 control-label">Typy udalostí</label>
                                <div class="col-sm-5">
                                    <input type="hidden" name="event_type" value="">
                                    @foreach($event_type as $et)
                                        <div class="checkbox">
                                            <input type="checkbox" name="event_type[]" value="{{$et->id}}" class="form-control"
                                                   @if(strlen($item->event_type) > 2)
                                                        @if(in_array($et->id, json_decode($item->event_type))) checked="checked"
                                                        @endif
                                                    @endif >
                                            <label for="checkbox1">
                                                {{$et->name}}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Členstvo</label>
                                <div class="col-sm-5">
                                    <input type="hidden" name="membership" value="">
                                    <div class="checkbox">
                                        <input type="checkbox" name="membership[]" value="member" class="form-control"
                                               @if(strlen($item->membership) > 2)
                                               @if(in_array('member', json_decode($item->membership))) checked="checked"
                                                @endif
                                                @endif >
                                        <label for="checkbox">
                                            Aktívny člen BFORB
                                        </label>
                                    </div>
                                    <div class="checkbox">
                                        <input type="checkbox" name="membership[]" value="not_member" class="form-control"
                                               @if(strlen($item->membership) > 2)
                                               @if(in_array('not_member', json_decode($item->membership))) checked="checked"
                                                @endif
                                                @endif >
                                        <label for="checkbox">
                                            Hosť (nie je členom)
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                                <label for="description" class="col-md-4 control-label">Popis</label>
                                <div class="col-md-6">
                                    <textarea id="description" rows="20" class="form-control" name="description"  required>{{ $item->description }}</textarea>
                                    @if ($errors->has('description'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('description') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                        @if(file_exists('images/event-type-text/' . $item->id . '/' .$item->image))
                            <div class="form-group{{ $errors->has('ico') ? ' has-error' : '' }}">
                                <label for="description" class="col-md-4 control-label">Linky obrazku pre kopirovanie</label>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <input id="image_link" type="text" class="form-control" value="{{ url('images/event-type-text/' . $item->id . '/' .$item->image) }}" >
                                        <span class="input-group-btn">
                                            <button type="button" class="btn btn-primary clipboard" data-clipboard-target="#image_link">
                                                <i class="fa fa-clipboard"></i> Copy</button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endif
                            <div class="form-group">
                                <label for="password_confirmation" class="col-md-4 control-label">Obrázok k textu</label>
                                <div class="col-md-4">
                                    <div class="fileinput fileinput-new" data-provides="fileinput">
                                        <div class="fileinput-new thumbnail" style="max-width: 500px; max-height: 500px;">
                                            @if(file_exists('images/event-type-text/' . $item->id . '/' .$item->image))
                                                <img data-src="holder.js/100%x100%"  alt="..." src="{!! asset('images/event-type-text') !!}/{{$item->id}}/{!! $item->image !!}">
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

@section('css')

    <!-- Bootstrap markdown -->
    <link rel="stylesheet" href="{!! asset('css/plugins/bootstrap-markdown/bootstrap-markdown.min.css') !!}" />

@endsection


@section('scripts_before')

    <script src="{!! asset('js/plugins/jquery-ui/jquery-ui.js') !!}"  type="text/javascript"></script>

@endsection



@section('scripts')

    <!-- Bootstrap markdown -->
    <script src="{!! asset('js/plugins/bootstrap-markdown/bootstrap-markdown.js') !!}"  type="text/javascript"></script>
    <script src="{!! asset('js/plugins/bootstrap-markdown/markdown.js') !!}"  type="text/javascript"></script>


    <script>
        $(document).ready(function (){

            $("#description").markdown(
                {   autofocus:false,
                    savable:false,
                    editable:true,
                    hideable: false,

                    onPreview: function(e) {

                        console.log(e.showPreview());

                    }

                }
            );

            $('#description').data('markdown').showPreview();

            $("#edit-form").validate({
                rules: {
                    description: "required",
                    minlength: 5
                }
            });

        });
    </script>
@endsection