@extends('layouts.app_public')

@section('title', '')

@section('content')

    <div class="wrapper wrapper-content">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 col-lg-offset-2">

                    <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <h3 class="font-bold no-margins">
                                Prihláška existujúceho hosťa na podnikateľské raňajky
                            </h3>
                        </div>
                        <div class="ibox-content">

                            @include('components.validation')

                            <form id="invitation_form" class="form-horizontal" method="POST" action="{{ route('invite.guest.exist.store') }}">
                                {{ csrf_field() }}
                                {{Form::hidden('user_id', $user->id)}}

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
                                                <option value="{{$e->id}}" @if( $req['event'] == $e->id ) selected="selected" @endif>{{ \Carbon\Carbon::createFromFormat( 'Y-m-d H:i:s', $e->event_from)->format('j.n.Y') }} - {{$e->club}}</option>
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

                                <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                                    <label for="description" class="col-md-4 control-label">Existujúci hosť</label>
                                    <div class="col-md-6">
                                        @foreach( $guests AS $g)
                                            <label>
                                                <input type="radio" value="{{ $g->id }}" id="" name="invitation_to" class="m-r-lg" >
                                                {{ $g->name }} {{ $g->surname }}, {{ $g->email }} (  {{ $g->status }} )
                                            </label>
                                            @endforeach
                                    </div>
                                </div>

                                <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                                    <label for="description" class="col-md-4 control-label">Doplňujúci text pozvánky</label>
                                    <div class="col-md-6">
                                        <textarea id="description" class="form-control" name="description" rows="4">{{ old('description') }}</textarea>
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
                    gender: "required",
                    invitation_to: "required"
                }
            });

        });
    </script>
@endsection