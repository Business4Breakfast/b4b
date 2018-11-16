@extends('layouts.app')

@section('title', 'Úprava užívateľa')

@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                    </div>
                    <div class="ibox-content">

                        @include('components.validation')

                        <form id="user_form" class="form-horizontal" method="POST" action="{{ route('guests.guest-listings.update', ['id'=> $guest->id]) }}">
                            {{ csrf_field() }}
                            {{ method_field('PUT') }}

                            <div class="form-group {{ $errors->has('title_before') ? ' has-error' : '' }}">
                                <label class="col-sm-4 control-label">Pohlavie</label>
                                <div class="col-sm-5 inline">
                                    <div class="col-md-2">
                                        <input type="hidden" name="gender" value="">
                                        <input type="radio" name="gender" id="M" value="M" class="form-control" @if($guest->gender == 'M')checked="checked"@endif>
                                        <label for="gender" class="text-normal">
                                            Muž
                                        </label>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="radio" name="gender" id="F" value="F" class="form-control" @if($guest->gender == 'F')checked="checked"@endif>
                                        <label for="gender" class="text-normal">
                                            Žena
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('title_before') ? ' has-error' : '' }}">
                                <label for="title" class="col-md-4 control-label">Titul pred menom</label>
                                <div class="col-md-2">
                                    <input id="title_before" type="text" maxlength="10" class="form-control" name="title_before" value="{{ $guest->title_before  }}" autofocus>

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
                                    <input id="name" type="text" class="form-control" name="name" value="{{ $guest->name  }}" required>

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
                                    <input id="surname" type="text" class="form-control" name="surname" value="{{ $guest->surname  }}" required>
                                    @if ($errors->has('surname'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('surname') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('title_after') ? ' has-error' : '' }}">
                                <label for="title" class="col-md-4 control-label">Titul za menom</label>
                                <div class="col-md-2">
                                    <input id="title_after" type="text" maxlength="10" class="form-control" name="title_after" value="{{ $guest->title_after  }}" autofocus>

                                    @if ($errors->has('title_after'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('title_after') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('industry_id') ? ' has-error' : '' }}">
                                <label for="industry" class="col-md-4 control-label">Typ podnikania</label>
                                <div class="col-md-4">
                                    <select class="form-control chosen-select" name="industry_id" class="form-control" id="industry_id" required>
                                        <option value="">-- výber --</option>
                                        @foreach($industry as $in)
                                            <option value="{{$in->id}}" @if($guest->industry_id == $in->id ) selected="selected" @endif>{{$in->name}}</option>
                                        @endforeach
                                    </select>

                                    @if ($errors->has('industry_id'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('industry_id') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                <label for="email" class="col-md-4 control-label">E-Mail Address</label>
                                <div class="col-md-5">
                                    <input id="email" type="email" class="form-control" name="email" value="{{ $guest->email  }}"

                                        required>

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
                                    <input id="phone" type="text" class="form-control" name="phone" value="{{ $guest->phone  }}" placeholder="" required>

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
                                    <input id="internet" type="url" class="form-control url_address" name="internet" value="{{ $guest->internet  }}" placeholder="http://">
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
                                    <input id="company" type="text" class="form-control" name="company" value="{{ $guest->company  }}" required>
                                    @if ($errors->has('company'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('company') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('club') ? ' has-error' : '' }}">
                                <label for="club" class="col-md-4 control-label">Kluby</label>
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <select name="club[]" data-placeholder="Výber klubu..." class="chosen-select-width" multiple style="width:400px;"  required>
                                            @foreach($clubs as $key =>$club)
                                                <option value="{{$club->id}}"
                                                        @if($user_clubs)
                                                        @foreach($user_clubs as $c)
                                                        @if($c->club_id == $club->id) selected="selected" @endif
                                                        @endforeach
                                                        @endif
                                                >{{$club->title}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @if ($errors->has('club'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('club') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('status') ? ' has-error' : '' }}">
                                <label for="status" class="col-md-4 control-label">Status</label>
                                <div class="col-md-3">
                                    <select name="status"  class="form-control" required>
                                        @foreach($user_statuses as $key =>$status)
                                            <option value="{{$status->id}}" @if($status->id == $guest->status ) selected="selected" @endif>{{$status->status}}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('status'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('status') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('created_user') ? ' has-error' : '' }}">
                                <label for="created_user" class="col-md-4 control-label">Kontakt patrí užívateľovi</label>
                                <div class="col-md-3">
                                    <select name="created_user"  class="form-control" >
                                        <option value="0">-- Nepriradený --</option>
                                        @if($users_from_clubs)
                                    @foreach($users_from_clubs as $key =>$ufc)
                                            <option value="{{$ufc->id}}" @if($ufc->id == $guest->created_user ) selected="selected" @endif>{{$ufc->full_name}}</option>
                                        @endforeach
                                            @endif
                                    </select>
                                    @if ($errors->has('created_user'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('created_user') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                                <label for="description" class="col-md-4 control-label">Poznámka</label>
                                <div class="col-md-4">
                                    <textarea id="description" class="form-control" name="description" rows="4">{{ $guest->description  }}</textarea>
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
                                        {{__('form.edit_record')}}
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