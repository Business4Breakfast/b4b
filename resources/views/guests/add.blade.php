@extends('layouts.app')

@section('title', 'Pridanie nového užívateľa')

@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content">

                        @include('components.validation')

                        <form id="user_form" class="form-horizontal" method="POST" action="{{ route('guests.guest-listings.store') }}">
                            {{ csrf_field() }}
                            <div class="form-group{{ $errors->has('club_id') ? ' has-error' : '' }}">
                                <label for="club_id" class="col-md-4 control-label">Hosť pre klub</label>
                                <div class="col-md-4">

                                    <select class="form-control " name="club_id" class="form-control">
                                        <option value="">-- všetky kluby --</option>
                                        @foreach($clubs as $c)
                                            <option value="{{$c->id}}">{{$c->title}}</option>
                                        @endforeach
                                    </select>

                                    @if ($errors->has('club_id'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('club_id') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group {{ $errors->has('title_before') ? ' has-error' : '' }}">
                                <label class="col-sm-4 control-label">Pohlavie</label>
                                <div class="col-sm-5 inline">
                                    <div class="col-md-2">
                                        <input type="hidden" name="gender" value="">
                                        <input type="radio" name="gender" id="M" value="M" class="form-control">
                                        <label for="gender" class="text-normal">
                                            Muž
                                        </label>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="radio" name="gender" id="F" value="F" class="form-control">
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
                                <div class="col-md-4">
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
                                <div class="col-md-4">
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

                                    <select class="form-control chosen-select" name="industry_id" class="form-control" required>
                                        <option value="">-- výber --</option>
                                        @foreach($industry as $in)
                                            <option value="{{$in->id}}">{{$in->name}}</option>
                                        @endforeach
                                    </select>

                                    @if ($errors->has('industry'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('industry') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                <label for="email" class="col-md-4 control-label">E-Mail Address</label>
                                <div class="col-md-5">
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
                                <div class="col-md-4">
                                    <input id="phone" type="text" class="form-control" name="phone" value="{{ old('phone') }}" placeholder="" required>

                                    @if ($errors->has('phone'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('phone') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('internet') ? ' has-error' : '' }}">
                                <label for="internet" class="col-md-4 control-label">Internet</label>
                                <div class="col-md-4">
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
                                <div class="col-md-4">
                                    <input id="company" type="text" class="form-control" name="company" value="{{ old('company') }}" required>
                                    @if ($errors->has('company'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('company') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                                <label for="description" class="col-md-4 control-label">Poznámka</label>
                                <div class="col-md-4">
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

            $('#phone').mask('+000000000000');
            $.validator.setDefaults({ ignore: ":hidden:not(.chosen-select)" });


            $("#user_form").validate({
                rules: {
                    phone: {
                        required: true,
                        minlength: 13
                    },
                    gender: {
                        required: true
                    }
                }
            });
        });
    </script>

@endsection