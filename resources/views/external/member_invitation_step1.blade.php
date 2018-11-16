@extends('layouts.app_public')

@section('title', '')

@section('content')

    <div class="wrapper wrapper-content">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 col-lg-offset-2 col-sm-12">

                    <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <h3 class="font-bold no-margins">
                                Prihláška hosťa na podnikateľské raňajky
                            </h3>
                        </div>
                        <div class="ibox-content">

                            @include('components.validation')

                            <form id="invitation_form" class="form-horizontal" method="POST" action="{{ route('ext.invite.guest.step1.store') }}">
                                {{ csrf_field() }}
                                {{Form::hidden('user_id', $user->id)}}
                                {{Form::hidden('form_type', 'guest_new_step_1')}}
                                {{Form::hidden('order_token', Request::segment(3))}}

                                <div class="form-group{{ $errors->has('invite_person') ? ' has-error' : '' }}">
                                    <label for="invite_person" class="col-md-4 control-label">Pozývajúci člen</label>
                                    <div class="col-md-6">
                                        <input id="invite_person" type="text" class="form-control" name="invite_person" value="{{$user->full_name}}" readonly>
                                        @if ($errors->has('invite_person'))
                                            <span class="help-block">
                                        <strong>{{ $errors->first('invite_person') }}</strong>
                                    </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group{{ $errors->has('event') ? ' has-error' : '' }}">
                                    <label for="event" class="col-md-4 control-label">Výber udalosti</label>
                                    <div class="col-md-4">
                                        <select class="form-control" name="event" class="form-control" required>
                                            <option value="">-- výber --</option>
                                            @foreach($events as $e)
                                                <option value="{{$e->id}}" @if(old('event') == $e->id ) selected="selected" @endif>{{ \Carbon\Carbon::createFromFormat( 'Y-m-d H:i:s', $e->event_from)->format('j.n.Y') }} - {{$e->club}} - {{$e->event}}</option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('event'))
                                            <span class="help-block">
                                        <strong>{{ $errors->first('event') }}</strong>
                                    </span>
                                        @endif
                                    </div>
                                </div>
                                <hr>
                                <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                    <label for="email" class="col-md-4 control-label">E-Mail Address</label>
                                    <div class="col-md-6">
                                        <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required>

                                        @if ($errors->has('email'))
                                            <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group{{ $errors->has('phone') ? ' has-error' : '' }}">
                                    <label for="phone" class="col-md-4 control-label">Telefón</label>
                                    <div class="col-md-6">
                                        <input id="phone" type="text" class="form-control" name="phone" value="{{ old('phone') }}" placeholder="" required>

                                        @if ($errors->has('phone'))
                                            <span class="help-block">
                                        <strong>{{ $errors->first('phone') }}</strong>
                                    </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-6 col-md-offset-4">
                                        <button type="submit" class="btn btn-primary">
                                            Odoslať pozvánku na raňajky
                                        </button>
                                    </div>
                                </div>
                            </form>

                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>

@endsection

@section('scripts')

    <script>
        $(document).ready(function (){

            $('#phone').mask('+000000000000');

            $.validator.setDefaults({ ignore: ":hidden:not(.chosen-select)" });

            $("#invitation_form").validate({
                rules: {
                    name: "required",
                    gender: "required"
                }
            });

        });
    </script>
@endsection