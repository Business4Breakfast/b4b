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

                        <form id="text_form" class="form-horizontal" method="POST" action="{{ route('setting.'.$module.'.store') }}">
                            {{ csrf_field() }}

                            <div class="form-group">
                                <label class="col-sm-4 control-label">Typy udalostí</label>
                                <div class="col-sm-5">
                                    <input type="hidden" name="event_type" value="">
                                    @foreach($event_type as $et)
                                        <div class="checkbox">
                                            <input type="checkbox" name="event_type[]" value="{{$et->id}}" class="form-control">
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
                                    <input type="hidden" name="event_type" value="">
                                    <div class="checkbox">
                                        <input type="checkbox" name="membership[]" value="member" class="form-control" checked="checked">
                                        <label for="checkbox">
                                            Aktívny člen BFORB
                                        </label>
                                    </div>
                                    <div class="checkbox">
                                        <input type="checkbox" name="membership[]" value="not_member" class="form-control">
                                        <label for="checkbox">
                                            Hosť (nie je členom)
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                                <label for="description" class="col-md-4 control-label">Popis</label>
                                <div class="col-md-6">
                                    <textarea id="description" rows="20" class="form-control" name="description"  required>{{ old('description') }}</textarea>
                                    @if ($errors->has('description'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('description') }}</strong>
                                    </span>
                                    @endif
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


            $("#edit-form").validate({
                rules: {
                    description: "required",
                    minlength: 5
                }
            });

        });
    </script>
@endsection