@extends('layouts.app_public')

@section('title', '')

@section('content')

    <div class="wrapper wrapper-content">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 col-lg-offset-2 col-sm-12">

                    <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <h3 class="font-bold">{{$event->title}}</h3>
                            <p class="font-bold">{{$event->club->title}}</p>

                        </div>
                        <div class="ibox-content">

                            @include('components.validation')

                            <form id="invitation_form" class="form-horizontal" method="POST" action="{{ route('ext.invite.guest.step1.store') }}">
                                {{ csrf_field() }}
{{--                                {{Form::hidden('user_id', $user->id)}}--}}
                                {{Form::hidden('form_type', 'guest_new_step_1')}}
                                {{Form::hidden('order_token', Request::segment(3))}}

                                <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                    <label for="name" class="col-md-4 control-label">Typ členstva</label>
                                    <div class="col-md-6">
                                        @if($recipient->admin == 1)
                                            <p class="form-control readonly">Clen</p>
                                        @else
                                            <p class="form-control readonly">Nečlen</p>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                    <label for="name" class="col-md-4 control-label">Meno</label>
                                    <div class="col-md-6">
                                        <input id="name" type="text" class="form-control" name="name" value="{{ $recipient->name }}" required readonly>

                                        @if ($errors->has('name'))
                                            <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group{{ $errors->has('surname') ? ' has-error' : '' }}">
                                    <label for="surname" class="col-md-4 control-label">Priezvisko</label>
                                    <div class="col-md-6">
                                        <input id="surname" type="text" class="form-control" name="surname" value="{{ $recipient->surname }}" required readonly>

                                        @if ($errors->has('surname'))
                                            <span class="help-block">
                                        <strong>{{ $errors->first('surname') }}</strong>
                                    </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                    <label for="email" class="col-md-4 control-label">E-Mail Address</label>
                                    <div class="col-md-6">
                                        <input id="email" type="email" class="form-control" name="email" value="{{ $recipient->email }}" required readonly>

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
                                        <input id="phone" type="text" class="form-control" name="phone" value="{{ $recipient->phone }}" placeholder="" required readonly>

                                        @if ($errors->has('phone'))
                                            <span class="help-block">
                                        <strong>{{ $errors->first('phone') }}</strong>
                                    </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group{{ $errors->has('pcs') ? ' has-error' : '' }}">
                                    <label for="pcs" class="col-md-4 control-label">Počet kusov</label>
                                    <div class="col-md-2">
                                        <input id="pcs" type="number" class="form-control" name="pcs" value="1" placeholder="" required>
                                    </div>
                                </div>
                                <div class="form-group{{ $errors->has('price') ? ' has-error' : '' }}">
                                    <label for="price" class="col-md-4 control-label">Cena</label>
                                    <div class="col-md-2">
                                        <input id="price" type="text" class="form-control" name="price" value="{{ $data['price'] }}" placeholder="" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-6 col-md-offset-4">
                                        <button type="submit" class="btn btn-primary">
                                            Objednať vstupenky
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