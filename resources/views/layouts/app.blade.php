<!DOCTYPE html>
<html>
<head>
    <base href="{{url('/')}}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>{{Config('app.name')}} | {{ $backend_title }}</title>

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

    <!-- Spectrum color pisker -->
    <link rel="stylesheet" href="{!! asset('css/plugins/spectrum/spectrum.css') !!}" />

    <link rel="stylesheet" href="{!! asset('css/plugins/datapicker/datepicker3.css') !!}" />
    <link rel="stylesheet" href="{!! asset('css/plugins/jasny/jasny-bootstrap.min.css') !!}" />

    <link rel="stylesheet" href="{!! asset('css/plugins/iCheck/custom.css') !!}" />
    <link rel="stylesheet" href="{!! asset('css/plugins/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css') !!}" />
    <link rel="stylesheet" href="{!! asset('css/plugins/multiselect/multi-select.css') !!}" />
    <link rel="stylesheet" href="{!! asset('css/plugins/fancy-box/jquery.fancybox.css') !!}" />


    @yield('page_css')

    @yield('css')

    <link rel="stylesheet" href="{!! asset('css/vendor.css') !!}" />
    <link rel="stylesheet" href="{!! asset('css/app.css') !!}" />

</head>
<body class="skin-4">

  <!-- Wrapper-->
    <div id="wrapper">
        <!-- Navigation -->
        @include('layouts.navigation')
        <!-- Page wraper -->
        <div id="page-wrapper" class="gray-bg">
            <!-- Page wrapper -->
            @include('layouts.topnavbar')

            <div class="row wrapper border-bottom white-bg page-heading">
                <div class="col-sm-6">
                    <h2>{{ $backend_title }}</h2>
                    <ol class="breadcrumb">
                        <li>
                            <a href="{{url("/")}}">Domov</a>
                        </li>
                        @php $url = "" @endphp
                        @for($i = 1; $i <= count(Request::segments()); $i++)
                            @php $url = $url . Request::segment($i)."/" @endphp
                            <li>
                                <a href="{{url($url)}}">{{ucfirst(Request::segment($i))}}</a>
                            </li>
                        @endfor
                    </ol>
                </div>
                <div class="col-sm-6">
                    <div class="title-action">
                        @if($action_menu)
                            @if($action_menu['dropdown'] == 'no')
                                @foreach($action_menu['items'] as $v)
                                    <a class="btn  @if($v['class']){{$v['class']}}@else btn-default @endif" href="{{$v['link']}}">@if($v['icon']) <i class="fa fa-{{$v['icon']}}"></i> @endif {{$v['name']}}</a>
                                @endforeach
                            @else
                                <div class="btn-group">
                                    <button data-toggle="dropdown" class="btn {!! $action_menu['class'] !!}  dropdown-toggle">{{$action_menu['name']}} <span class="caret"></span></button>
                                    <ul class="dropdown-menu pull-right">
                                        @foreach($action_menu['items'] as $v)
                                            <li><a href="{{$v['link']}}">{{$v['name']}}</a></li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>

            <!-- will be used to show any messages -->
            @if (Session::has('message'))
                <div class="alert alert-success m-t-md m-l-md m-r-md">{{ Session::get('message') }}</div>
            @endif

            <div class="flash-message m-md  m-t-md m-l-md m-r-md">
                @foreach (['danger', 'warning', 'success', 'info'] as $msg)
                    @if(Session::has('alert-' . $msg))
                        <p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }}</p>
                    @endif
                @endforeach
            </div>

            <!-- Main view  -->
            @yield('content')

            <!-- Footer -->
            @include('layouts.footer')

        </div>
        <!-- End page wrapper-->

    </div>
    <!-- End wrapper-->

  <!-- scripts before -->

  @section('scripts_before')

  @show

  <!-- Mainly scripts -->
  <script src="{!! asset('js/plugins/fullcalendar/moment.min.js') !!}" type="text/javascript"></script>

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

  <!-- Spectrum colorpicker -->
  <script src="{!! asset('js/plugins/spectrum/spectrum.js') !!}" type="text/javascript"></script>

  <!-- iCheck -->
  <script src="{!! asset('js/plugins/iCheck/icheck.min.js') !!}" type="text/javascript"></script>
  <script src="{!! asset('js/plugins/multiselect/jquery.multi-select.js') !!}" type="text/javascript"></script>

  <script src="{!! asset('js/plugins/fancy-box/jquery.fancybox.js') !!}" type="text/javascript"></script>

  <script src="{!! asset('js/custom.js') !!}" type="text/javascript"></script>

  <script>

      if (jQuery)
          console.dir("jQuery "+$().jquery +" loaded");
      else
          console.dir("jQuery NOT loaded");

      if($.ui)
          console.dir("jQuery UI "+$.ui.version+" loaded");
      else
          console.dir("jQuery UI NOT loaded");

      $("#form_bug_report").validate({
          rules: {
              description: {
                  required: true,
                  minlength: 10
              },
              bug_type: {
                  required: true
              }
          }
      });

  </script>

@section('scripts')

@show

</body>
</html>
