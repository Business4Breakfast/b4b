@extends('layouts.app')

@section('title', 'Generovanie backend menu')

@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Pridavanie položiek menu</h5>
                    </div>
                    <div class="ibox-content">

                        @if (count($errors) > 0)
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                            {!! Form::open(['url' => 'developer/menu-store', 'method' => 'POST' ,'class' => 'form-horizontal']) !!}
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Názov</label>
                                <div class="col-sm-6 col-md-6">
                                    <div class="input-group">
                                        <span class="input-group-btn">
                                            <button class="btn btn-default" data-icon="fa-adjust" data-iconset="fontawesome"
                                                    role="iconpicker" data-placement="left" name="icon" >
                                            </button>
                                        </span>
                                        <input type="text" name="title"  id="title" class="form-control" value="" placeholder="Názov">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Route prefix</label>
                                <div class="col-sm-6 col-md-6">
                                    <input type="text" class="form-control" name="route_prefix">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Route</label>
                                <div class="col-sm-6">
                                    {!! Form::select('route', $route , null , ['class' => 'form-control', 'placeholder' => '-- výber --']) !!}
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Rank</label>
                                <div class="col-sm-3">
                                    <input type="number" class="form-control" name="rank" value="0" >
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Group name</label>
                                <div class="col-sm-5">
                                    <input type="text" class="form-control" id="block" name="block" value="">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Roles</label>
                                <div class="col-sm-5">
                                    @foreach($roles as $role)
                                    <div class="checkbox">
                                        <input type="checkbox" name="role[]" value="{{$role->id}}" class="form-control">
                                        <label for="checkbox1">
                                            {{$role->display_name}}
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Permission</label>
                                <div class="col-sm-5">
                                    <input type="text" class="form-control" id="permission_name" name="permission_name" value="">
                                    <input type="hidden" class="form-control" id="permission_display" name="permission_display" value="">
                                    <input type="hidden" class="form-control" id="permission_description" name="permission_description" value="">

                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <div class="col-sm-6 col-md-8 col-sm-offset-4">
                                    <button class="btn btn-primary" type="submit">Uložiť</button>
                                </div>
                            </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('css')

    <link rel="stylesheet" href="{!! asset('js/plugins/nestable2/jquery.nestable.min.css') !!}" />

@endsection

@section('scripts')

    <script src="{!! asset('js/plugins/nestable2/jquery.nestable.min.js') !!}" type="text/javascript"></script>

<script>
    $(document).ready(function(){

        $("#title").on('blur keyup change click input', function () {
            var txtClone = $('#title').val();
            $('#block').val(webalize(txtClone));
            $('#permission_name').val(webalize('menu-'+txtClone));
            txtClone = txtClone.charAt(0).toUpperCase() + txtClone.substr(1);
            $('#permission_display').val('Menu '+txtClone);
            $('#permission_description').val('Permission from menu '+txtClone);
        });

    });


</script>

@endsection