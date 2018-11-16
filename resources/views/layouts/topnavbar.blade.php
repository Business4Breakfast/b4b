<div class="row border-bottom">
    <nav class="navbar navbar-static-top white-bg" role="navigation" style="margin-bottom: 0">
        <div class="navbar-header">
            <a class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="{{route('dashboard.search')}}"><i class="fa fa-bars"></i> </a>
            <form role="search" class="navbar-form-custom" method="post" action="/">
                <div class="form-group">
                    <input type="text" placeholder="Rýchle vyhľadávanie..." class="form-control" name="top-search" id="top-search" />
                    {{ csrf_field() }}
                </div>
            </form>
        </div>
        <ul class="nav navbar-top-links navbar-right" data-original-title="Nahlásiť podnet (chybu)" data-toggle="tooltip" data-placement="top">
            <li class="dropdown">
                <a class="dropdown-toggle count-info" data-toggle="dropdown" href="#" aria-expanded="false">
                    <i class="fa fa-bug text-danger"></i>
                    {{--<span class="label label-primary">8</span>--}}
                </a>
                <ul class="dropdown-menu dropdown-alerts" style="width: 200px;">
                    <li>
                        <a href="#modal-form" data-toggle="modal" >
                            <div>
                                <i class="fa fa-edit fa-fw"></i> Nahlásiť chybu
                            </div>
                        </a>
                    </li>
                    <li class="divider"></li>
                    <li>
                        <div class="text-center link-block">
                            <a href="{{route('developer.bug-report.index')}}">
                                <strong>Zobraziť všetky</strong>
                                <i class="fa fa-angle-right"></i>
                            </a>
                        </div>
                    </li>
                </ul>
            </li>

            @if(Auth::user()->hasRole('superadministrator') || Session::exists('change_user') )
            <li>
                <form id="change_login_user_form" method="POST" action="{{route('setting.change.user.login')}}">
                    {{ csrf_field() }}
                    @if(!Session::exists('change_user'))
                        {{ Form::hidden('auth_change_user', Auth::user()->id)}}
                    @endif
                    <select name="select_user_change" class="chosen-select-200" id="select_user_change">
                        @foreach(\App\User::where('admin', 1)->get() as $user)
                            <option value="{{$user->id}}" @if($user->id == Auth::user()->id) selected="selected" @endif>{{$user->full_name}}</option>
                        @endforeach
                    </select>
                </form>
            </li>
            @endif

            {{--@endrole--}}
            <li class="dropdown">
                <a class="dropdown-toggle count-info" data-toggle="dropdown" href="#" aria-expanded="false">
                    <i class="flag-icon flag-icon-{{App::getLocale()}}"></i>
                </a>
                <ul class="dropdown-menu">
                    <li>
                        <a href="{{ URL::to('lang/sk') }}">
                            <div>
                                <i class="flag-icon flag-icon-sk"></i> Slovensky
                            </div>
                        </a>
                    </li>
                    <li class="divider"></li>
                    <li>
                        <a href="{{ URL::to('lang/en') }}">
                            <div>
                                <i class="flag-icon flag-icon-en"></i> Anglicky
                            </div>
                        </a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="{{ URL::to('logout') }}"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fa fa-sign-out"></i> Odhlásenie
                </a>

                <form id="logout-form" action="{{ URL::to('logout') }}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                </form>
            </li>
        </ul>
    </nav>
</div>

<div id="modal-form" class="modal fade" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12">
                        <h3 class="m-t-none m-b">Nahlásiť podnet:</h3>
                        <p>Vyplň typ a popis podnetu</p>
                    </div>
                    <form id="form_bug_report" role="form" method="POST" action="{{ route('developer.bug-report.store') }}" enctype="multipart/form-data" >
                        {{Form::token()}}
                        {{Form::hidden('bug_report_route', Request::getUri() )}}
                    <div class="col-sm-8">
                        <div class="form-group">
                            <label>Typ podnetu</label>
                            <select name="bug_type" class="form-control">
                                <option value="">-- výber --</option>
                                @foreach( __('constant.bug_report_type') as $k => $s)
                                    <option value="{{$k}}">{{$s}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Popis chyby</label>
                            <textarea name="description" rows="6" class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label for="password_confirmation" class="control-label">Obrázok chyby</label>
                        <div class="fileinput fileinput-new" data-provides="fileinput">
                            <div class="fileinput-new thumbnail" style="max-width: 500px; max-height: 500px;">
                                <p class="text-center m-t-md">
                                    <i class="fa fa-upload big-icon"></i>
                                </p>
                            </div>
                            <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 500px; max-height: 500px;"></div>
                            <div>
                                <span class="btn btn-default btn-file">
                                    <span class="fileinput-new"><i class="fa fa-paperclip"></i> {{__('form.file_select')}}</span><span class="fileinput-exists">
                                        <i class="fa fa-undo"></i> {{__('form.new_file')}}</span>
                                    <input type="file" name="files">
                                </span>
                                <a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">
                                    <i class="fa fa-trash"></i> {{__('form.delete')}}</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <button type="button" class="btn btn-white" data-dismiss="modal">{{__('form.cancel')}}</button>
                        <button type="submit" class="btn btn-primary">{{__('form.create')}}</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
