@extends('layouts.app')

@section('title', 'Editacia backend menu')

@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Editovanie položiek menu</h5>
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

                            {!! Form::open(['url' => 'developer/menu-save', 'method' => 'POST' ,'class' => 'form-horizontal']) !!}
                            {!! Form::hidden('menu_id', $menu->id)!!}

                            <div class="form-group">
                                <label class="col-sm-3 control-label">Názov</label>
                                <div class="col-sm-5">
                                    <div class="input-group">
                                        <span class="input-group-btn">
                                            <button class="btn btn-default" data-icon="{{$menu->icon }}" data-iconset="fontawesome"
                                                    role="iconpicker" data-placement="left" name="icon" >
                                            </button>
                                        </span>
                                        <input type="text" name="title" class="form-control" value="{{$menu->title }}" placeholder="Názov">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Route prefix</label>
                                <div class="col-sm-5">
                                    <input type="text" class="form-control" name="route_prefix">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Route</label>
                                <div class="col-sm-5">
                                    {!! Form::select('route', $route , $menu->route , ['class' => 'form-control', 'placeholder' => '-- výber --']) !!}
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Rank</label>
                                <div class="col-sm-1">
                                    <input type="text" class="form-control" name="rank" value="{{$menu->rank }}" >
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Group name</label>
                                <div class="col-sm-3">
                                    <input type="text" class="form-control" name="block" value="{{$menu->block }}" >
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Parent</label>
                                <div class="col-sm-1">
                                    <input type="text" class="form-control" name="parent" value="{{$menu->parent }}" >
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <div class="col-sm-5 col-sm-offset-3">
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

