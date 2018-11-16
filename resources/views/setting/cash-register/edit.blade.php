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

                        {{--<form id="user_form" class="form-horizontal" method="POST" action="{{ route('setting.'.$module.'.update') }}">--}}

                        {{ Form::open(['method' => 'PUT', 'route' => ['setting.'.$module.'.update', $item->id ],
                           'class' => 'form-horizontal', 'id' => 'edit-form' ])
                        }}

                            <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                <label for="name" class="col-md-4 control-label">Názov</label>
                                <div class="col-md-4">
                                    <input id="name" type="text" class="form-control" name="name" value="{{ $item->name }}" required autofocus>
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
                                        <input type="radio" name="polarity" value="0"  @if($item->polarity == 0) checked="checked" @endif class="form-control">
                                        <label for="gender" class="text-normal">
                                            Výdaj
                                        </label>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="radio" name="polarity"  value="1" @if($item->polarity == 1) checked="checked" @endif class="form-control">
                                        <label for="gender" class="text-normal">
                                            Príjem
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('ico') ? ' has-error' : '' }}">
                                <label for="description" class="col-md-4 control-label">Popis</label>
                                <div class="col-md-6">
                                    <input id="description" type="text" class="form-control" name="description" value="{{ $item->description }}" required>
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

@section('scripts')

    <script>
        $(document).ready(function (){

            $("#edit-form").validate({
                rules: {
                    name: "required"
                }
            });

        });
    </script>
@endsection