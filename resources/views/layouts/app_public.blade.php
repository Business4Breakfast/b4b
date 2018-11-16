<!DOCTYPE html>
<html>
<head>

    <base href="{{url('/')}}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <title> {{Config('app.name')}} </title>
    <!-- app and jquery -->
    <script src="{!! asset('js/app.js') !!}" type="text/javascript"></script>

    <link rel="stylesheet" href="{!! asset('css/flag-icon.css') !!}" />
    <link rel="stylesheet" href="{!! asset('css/bootstrap-iconpicker.css') !!}" />
    <link rel="stylesheet" href="{!! asset('css/plugins/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css') !!}" />


    <link rel="stylesheet" href="{!! asset('css/plugins/chosen/chosen.css') !!}" />
    <!-- Toastr style -->
    <link rel="stylesheet" href="{!! asset('css/plugins/toastr/toastr.min.css') !!}" />
    <!-- Sweet Alert -->
    <link rel="stylesheet" href="{!! asset('css/plugins/sweetalert/sweetalert.css') !!}" />

    <link rel="stylesheet" href="{!! asset('css/plugins/datapicker/datepicker3.css') !!}" />
    <link rel="stylesheet" href="{!! asset('css/plugins/jasny/jasny-bootstrap.min.css') !!}" />

    @yield('page_css')

    @yield('css')

    <link rel="stylesheet" href="{!! asset('css/vendor.css') !!}" />
    <link rel="stylesheet" href="{!! asset('css/app.css') !!}" />

</head>
<body class="top-navigation">
  <!-- Wrapper-->
    <div id="wrapper">
        <!-- Page wraper -->
        <div id="page-wrapper" class="gray-bg">
                    <div class="row border-bottom white-bg">
                        <nav class="navbar navbar-static-top" role="navigation">
                            <div class="navbar-header">
                                <button aria-controls="navbar" aria-expanded="false" data-target="#navbar" data-toggle="collapse" class="navbar-toggle collapsed" type="button">
                                    <i class="fa fa-reorder"></i>
                                </button>
                                <a href="//bforb.sk" class="navbar-brand">BFORB Slovensko</a>

                                {{--<select class="form-control">--}}
                                    {{--<option >1</option>--}}
                                    {{--<option >1</option>--}}
                                    {{--<option >1</option>--}}
                                    {{--<option >1</option>--}}

                                {{--</select>--}}
                            </div>

                            {{--<div class="navbar-collapse collapse" id="navbar">--}}
                                {{--<ul class="nav navbar-nav">--}}
                                    {{--<li class="active">--}}
                                        {{--<a aria-expanded="false" role="button" href="layouts.html"> Hlavná stránka</a>--}}
                                    {{--</li>--}}
                                    {{--<li class="dropdown">--}}
                                        {{--<a aria-expanded="false" role="button" href="#" class="dropdown-toggle" data-toggle="dropdown"> Menu item <span class="caret"></span></a>--}}
                                        {{--<ul role="menu" class="dropdown-menu">--}}
                                            {{--<li><a href="">Menu item</a></li>--}}
                                            {{--<li><a href="">Menu item</a></li>--}}
                                            {{--<li><a href="">Menu item</a></li>--}}
                                            {{--<li><a href="">Menu item</a></li>--}}
                                        {{--</ul>--}}
                                    {{--</li>--}}
                                {{--</ul>--}}
                            {{--</div>--}}

                        </nav>
                    </div>

                    <!-- will be used to show any messages -->
                    @if (Session::has('message'))
                        <div class="alert alert-success m-t-md m-l-md m-r-md">{{ Session::get('message') }}</div>
                    @endif

                    <div class="flash-message m-md">
                        @foreach (['danger', 'warning', 'success', 'info'] as $msg)
                            @if(Session::has('alert-' . $msg))
                                <p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }}</p>
                            @endif
                        @endforeach
                    </div>

                    <!-- Main view  -->
                    @yield('content')


            <!-- Footer -->
            <div class="footer">
                <div>
                    <strong>{{__('app.footer_copyright')}}</strong> {{app_name()}} &copy; 2013-{{ Carbon\Carbon::now()->format('Y') }}
                </div>
            </div>

        </div>
        <!-- End page wrapper-->
    </div>
    <!-- End wrapper-->

  <script src="{!! asset('js/bootstrap-iconpicker-iconset-all.min.js') !!}" type="text/javascript"></script>
  <script src="{!! asset('js/bootstrap-iconpicker.min.js') !!}" type="text/javascript"></script>
  <!-- Toastr script -->
  <script src="{!! asset('js/plugins/toastr/toastr.min.js') !!}" type="text/javascript"></script>
  <!-- Sweet alert -->
  <script src="{!! asset('js/plugins/sweetalert/sweetalert.min.js') !!}" type="text/javascript"></script>
  <!-- Clipboard -->
  <script src="{!! asset('js/plugins/clipboard/clipboard.min.js') !!}" type="text/javascript"></script>
  <!-- Validate -->
  <script src="{!! asset('js/plugins/validate/jquery.validate.min.js') !!}" type="text/javascript"></script>
  <script src="{!! asset('js/plugins/validate/additional-methods.js') !!}" type="text/javascript"></script>
  <script src="{!! asset('js/plugins/validate/messages_sk.js') !!}" type="text/javascript"></script>
  <!-- Chosen -->
  <script src="{!! asset('js/plugins/chosen/chosen.jquery.js') !!}" type="text/javascript"></script>

  <!-- Jasny -->
  <script src="{!! asset('js/plugins/jasny/jasny-bootstrap.min.js') !!}"></script>

  <!-- Inputmask -->
  <script src="{!! asset('js/plugins/inputmask/jquery.mask.min.js') !!}" type="text/javascript"></script>

  <!-- Data picker -->
  <script src="{!! asset('js/plugins/datapicker/bootstrap-datepicker.js') !!}"></script>


  <script src="{!! asset('js/custom.js') !!}" type="text/javascript"></script>

@section('scripts')

@show

</body>
</html>
