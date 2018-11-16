@extends('layouts.app')

@section('title', '')

@section('content')

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h3>Manual Súbory - sekcia</h3>
                </div>
                <div class="ibox-content">

                    @include('components.validation')

                    <form id="manual_files_add" class="form-horizontal" method="POST" action="{{ route('manual.files-type.update', $section->id) }}">
                        {{ method_field('PUT') }}
                        {{ csrf_field() }}

                        <div class="form-group">
                            <label class="col-sm-4 control-label">Oprávnenia</label>
                            <div class="col-sm-5">
                                <div class="checkbox">
                                    <input type="hidden" name="active" value="0">
                                    <input type="checkbox" name="active" value="1" class="form-control" @if($section->active == 1) checked="checked" @endif>
                                    <label for="checkbox1">
                                        Aktívny
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Názov</label>
                            <div class="col-sm-6">
                                <div class="input-group">
                                    <span class="input-group-btn">
                                        <button class="btn btn-default"  data-icon="{{ $section->icon }}"  data-iconset="fontawesome"
                                                role="iconpicker" data-placement="left" name="icon" >
                                        </button>
                                    </span>
                                    <input type="text" name="title"  id="title" class="form-control" value="{{ $section->title }}" placeholder="Názov">
                                </div>
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('ico') ? ' has-error' : '' }}">
                            <label for="description" class="col-md-4 control-label">Popis sekcie</label>
                            <div class="col-md-6">
                                <textarea id="description" type="text" class="form-control" name="description" rows="5" required>{{ $section->description }}</textarea>
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

    <link href="{!! asset('css/plugins/nouslider/jquery.nouislider.css') !!}" rel="stylesheet">


@endsection

@section('scripts')


    <script>

        $(document).ready(function(){

            $("#manual_files_add").validate({
                rules: {
                    title: "required"
                }
            });


        });

    </script>

@endsection