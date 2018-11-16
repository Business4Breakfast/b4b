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

                        <form id="manual_files_add" class="form-horizontal" method="POST" action="{{ route('manual.files.store') }}">
                            {{ csrf_field() }}

                            <div class="form-group">
                                <label class="col-sm-4 control-label">Názov</label>
                                <div class="col-sm-6">
                                    <div class="input-group">
                                        <span class="input-group-btn">
                                            <button class="btn btn-default" @if(old('icon')) data-icon="{{ old('icon') }}" @else data-icon="fa-folder" @endif data-iconset="fontawesome"
                                                    role="iconpicker" data-placement="left" name="icon" >
                                            </button>
                                        </span>
                                        <input type="text" name="title"  id="title" class="form-control" value="" placeholder="Názov">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('ico') ? ' has-error' : '' }}">
                                <label for="description" class="col-md-4 control-label">Popis sekcie</label>
                                <div class="col-md-6">
                                    <textarea id="description" type="text" class="form-control" name="description" rows="5" required>{{ old('description') }}</textarea>
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