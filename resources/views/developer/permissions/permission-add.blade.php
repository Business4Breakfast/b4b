@extends('layouts.app')

@section('title', 'Editacia backend menu')

@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                    </div>
                    <div class="ibox-content">

                        @include('components.validation')

                        {!! Form::open(['route' => 'developer.permission.create', 'method' => 'POST' ,'class' => 'form-horizontal']) !!}

                        <div class="form-group">
                                <label class="col-sm-3 control-label">Display name</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" id="display-name" name="display_name" value="{{ Form::old('display_name') }}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Code name</label>
                                <div class="col-sm-4">
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" readonly>
                                        <span class="input-group-btn">
                                            <button type="button" class="btn btn-default clipboard" data-clipboard-target="#name">Copy</button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Description</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" name="description" value="{{ old('description') }}" >
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

@section('scripts')

    <script>

        $(document).ready(function (){
            $("#display-name").on('blur keyup change click input', function () {
                var txtClone = $('#display-name').val();
                // first capital letter
                txtClone = txtClone.charAt(0).toUpperCase() + txtClone.substr(1);
                $('#name').val(webalize(txtClone));
                $('#display-name').val(ltrim(txtClone));
            });
        });

    </script>
@endsection