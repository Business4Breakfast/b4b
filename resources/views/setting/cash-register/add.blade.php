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

                        <form id="user_form" class="form-horizontal" method="POST" action="{{ route('setting.'.$module.'.store') }}">
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
                            <div class="form-group {{ $errors->has('gender') ? ' has-error' : '' }}">
                                <label class="col-sm-4 control-label">Typ (príjem/výdaj)</label>
                                <div class="col-sm-5 inline">
                                    <div class="col-md-2">
                                        <input type="hidden" name="polarity" value="">
                                        <input type="radio" name="polarity" value="0"  checked="checked" class="form-control">
                                        <label for="gender" class="text-normal">
                                            Výdaj
                                        </label>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="radio" name="polarity"  value="1" class="form-control">
                                        <label for="gender" class="text-normal">
                                            Príjem
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('ico') ? ' has-error' : '' }}">
                                <label for="description" class="col-md-4 control-label">Popis</label>
                                <div class="col-md-6">
                                    <input id="description" type="text" class="form-control" name="description" value="{{ old('description') }}" required>
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
                    name: "required",
                    polarity: "required"
                }
            });

        });
    </script>
@endsection