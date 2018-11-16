@extends('layouts.app')

@section('title', 'Detail udalosti')

@section('content')

    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">

                    @include('invitation.header_event')

                    @include('components.validation')

                    <div class="ibox-title">

                        <div class="row">
                            {{Form::open(['route' => ['invitations.event.guest-new-step3.store', 'event' => $event->id], 'id' => 'form_guest_new_step_3',
                                                 'class' => "form-horizontal"])}}

                            <input type="hidden" name="form_type" value="guest_new_step_3">
                            {{Form::hidden('event_id', $event->id)}}
                            {{ csrf_field() }}
                            {{Form::hidden('user_id', Auth::user()->id)}}
                            {{Form::hidden('club_id', $event->club_id)}}



                            <h3 class="col-lg-offset-4">Existujúci hosť</h3>
                            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                <label for="email" class="col-md-4 control-label">E-Mail Address</label>
                                <div class="col-md-4">
                                    <input id="new_guest_email" type="email" class="form-control" name="email"
                                           value="{{$res['email'] }}" readonly>
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
                                    <input id="new_guest_phone" type="text" class="form-control" name="phone" value="{{$res['phone'] }}" readonly>

                                    @if ($errors->has('phone'))
                                        <span class="help-block" id="error_guest_phone">
                                <strong>{{ $errors->first('phone') }}</strong>
                            </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group {{ $errors->has('gender') ? ' has-error' : '' }}">
                                <label class="col-sm-4 control-label">Pozvať ako</label>
                                <div class="col-sm-8 inline">
                                    <div class="col-md-4">
                                        <input type="hidden" name="invite_person" value="">
                                        <input type="radio" name="invite_person" id="invite_person" value="manager" class="form-control"   checked="checked">
                                        <label for="invite_person" class="text-normal">
                                            <strong>Manažer klubu</strong>
                                        </label>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="radio" name="invite_person" id="invite_person" value="personely" class="form-control"  @if(old('invite_person') == "personely" ) checked="checked" @endif>
                                        <label for="invite_person" class="text-normal">
                                            <strong>V mojom mene</strong>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <hr>

                            <div class="form-group {{ $errors->has('gender') ? ' has-error' : '' }}">
                                <label class="col-sm-4 control-label">Pohlavie</label>
                                <div class="col-sm-5 inline">
                                    <div class="col-md-4">
                                        <input type="hidden" name="gender" value="">
                                        <input type="radio" name="gender" id="M" value="M" class="form-control"  @if(old('gender') == "M" ) checked="checked" @endif>
                                        <label for="gender" class="text-normal">
                                            Muž
                                        </label>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="radio" name="gender" id="F" value="F" class="form-control"  @if(old('gender') == "F" ) checked="checked" @endif>
                                        <label for="gender" class="text-normal">
                                            Žena
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('title_before') ? ' has-error' : '' }}">
                                <label for="title_before" class="col-md-4 control-label">Titul pred menom</label>
                                <div class="col-md-2">
                                    <input id="title_before" type="text" maxlength="10" class="form-control" name="title_before" value="{{ old('title_before') }}" autofocus>

                                    @if ($errors->has('title_before'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('title_before') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                <label for="name" class="col-md-4 control-label">Meno</label>
                                <div class="col-md-6">
                                    <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" required>

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
                                    <input id="surname" type="text" class="form-control" name="surname" value="{{ old('surname') }}" required>
                                    @if ($errors->has('surname'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('surname') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('title_after') ? ' has-error' : '' }}">
                                <label for="title_after" class="col-md-4 control-label">Titul za menom</label>
                                <div class="col-md-2">
                                    <input id="title_after" type="text" maxlength="10" class="form-control" name="title_after" value="{{ old('title_after') }}">

                                    @if ($errors->has('title_after'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('title_after') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('industry') ? ' has-error' : '' }}">
                                <label for="industry" class="col-md-4 control-label">Typ podnikania</label>
                                <div class="col-md-4">

                                    <select class="form-control chosen-select" name="industry" required>
                                        <option value="">-- výber --</option>
                                        @foreach($industry as $in)
                                            <option value="{{$in->id}}" @if(old('industry') == $in->id ) selected="selected" @endif>{{$in->name}}</option>
                                        @endforeach
                                    </select>

                                    @if ($errors->has('industry'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('industry') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('internet') ? ' has-error' : '' }}">
                                <label for="internet" class="col-md-4 control-label">Internet</label>
                                <div class="col-md-6">
                                    <input id="internet" type="url" class="form-control url_address" name="internet" value="{{ old('internet') }}" placeholder="http://" >
                                    @if ($errors->has('internet'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('internet') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('company') ? ' has-error' : '' }}">
                                <label for="company" class="col-md-4 control-label">Firma</label>
                                <div class="col-md-6">
                                    <input id="company" type="text" class="form-control" name="company" value="{{ old('company') }}" required>
                                    @if ($errors->has('company'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('company') }}</strong>
                                    </span>
                                    @endif
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
                                        Odoslať pozvánku
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

            $("#form_guest_new_step_3").validate({
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