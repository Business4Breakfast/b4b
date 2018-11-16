<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BFORB Prihlásenie - @yield('title') </title>

    <link rel="stylesheet" href="{!! asset('css/vendor.css') !!}" />
    <link rel="stylesheet" href="{!! asset('css/app.css') !!}" />
</head>

<body class="gray-bg">

<div class="loginColumns animated fadeInDown">
    <div class="row">

        <div class="col-md-6">
            <h2 class="font-bold">{{Trans('login.text_title')}}</h2>
            <img src="{{asset('images/app'). '/B4B_logo_alpha_500.png'}}" class="img-responsive" style="width: 70%;">
        </div>
        <div class="col-md-6">
            <div class="ibox-content">
                <form class="m-t" role="form" method="POST"  action="{{ route('login') }}">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="{{Trans('login.login_name')}}" required autofocus>
                        @if ($errors->has('email'))
                            <span class="help-block">
                                <strong class="text-danger">{{ $errors->first('email') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="form-group">
                        <input id="password" type="password" class="form-control" name="password" required placeholder="Heslo">
                        @if ($errors->has('password'))
                            <span class="help-block error">
                                <strong>{{ $errors->first('password') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="form-group">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> {{Trans('login.remember_me')}}
                            </label>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary block full-width m-b">{{Trans('login.login_button')}}</button>

                    <a href="#">
                        <small>{{Trans('login.lost_password')}}</small>
                    </a>
                </form>
                <p class="m-t">
                    <small>{{Trans('login.text_log_copy')}}</small>
                </p>
            </div>
        </div>
    </div>
    <hr/>
    <div class="row">
        <div class="col-md-6">
            {{Trans('app.footer_application')}} {{env('APP_NAME')}}
        </div>
        <div class="col-md-6 text-right">
            <small>{{Trans('app.footer_copyright1')}} {{Trans('app.footer_copyright2')}} {{\Carbon\Carbon::now()->year}}</small>
        </div>
    </div>
</div>

</body>

</html>