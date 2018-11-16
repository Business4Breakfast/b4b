@extends('layouts.app')

@section('title', 'Detail udalosti')

@section('content')


    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">

                    @include('invitation.header_event')

                    @include('components.validation')

                    <div class="ibox-content">

                        <div class="row">
                            <h3 class="col-lg-offset-4">Vloženie nového hosťa</h3>
                            {{Form::open(['route' => ['invitations.event.guest-new.store', 'event' => $event->id], 'id' => 'form_guest_new_step_1',
                                             'class' => "form-horizontal"])}}

                            {{Form::hidden('event_id', $event->id)}}
                            {{Form::hidden('form_type','guest_new_step_1')}}
                            {{ csrf_field() }}

                            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                <label for="email" class="col-md-4 control-label">E-Mail Address</label>
                                <div class="col-md-4">
                                    <input id="new_guest_email" type="email" class="form-control" name="email"
                                           value="@if($res['email'])  {{$res['email']}}  @else {{ old('email') }} @endif">
                                    @if ($errors->has('email'))
                                        <span class="help-block">
                                                <strong>{{ $errors->first('email') }}</strong>
                                            </span>
                                    @endif
                                    <span class="help-block text-danger hide font-bold" id="error_guest_email" ></span>
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('phone') ? ' has-error' : '' }}">
                                <label for="phone" class="col-md-4 control-label">Telefón</label>
                                <div class="col-md-4">
                                    <input id="new_guest_phone" type="text" class="form-control" name="phone" value="{{ old('phone') }}" placeholder="">

                                    @if ($errors->has('phone'))
                                        <span class="help-block" id="error_guest_phone">
                                    <strong>{{ $errors->first('phone') }}</strong>
                                </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-6 col-md-offset-4">
                                    <button type="submit" class="btn btn-primary">
                                        Pozvať hosťa
                                    </button>
                                </div>
                            </div>
                            {{Form::close()}}

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>


@endsection

@section('page_css')

@endsection


@section('scripts')

    <script>
        jQuery(window).ready(function (){

            $.validator.setDefaults({ ignore: ":hidden:not(.chosen-select)" });

            $('#new_guest_phone').mask('+000000000000');

            if($("#form_guest_new_step_1").length){
                $("#form_guest_new_step_1").validate({
                    rules: {
                        phone: {
                            required: true,
                            minlength: 13
                        },
                        email: {
                            required: true,
                            email: true
                        }
                    }
                });
            }



            $("#form_guest_new_step_2").validate({
                rules: {
                    phone: {
                        required: true,
                        minlength: 13
                    },
                    email: {
                        required: true,
                        email: true
                    },
                    guest: { required: true }

                }
            });





        });
    </script>

@endsection