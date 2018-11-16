<nav class="navbar-default navbar-static-side" role="navigation">
    <div class="sidebar-collapse">
        <ul class="nav metismenu" id="side-menu">
            <li class="nav-header">
                <div class="dropdown profile-element">
                    @if (Auth::check())
                    <div class="dropdown profile-element">
                        <span>
                            <img alt="{{$logged_user->name}} {{$logged_user->surname}}" class="img-circle" src="{!! asset( $logged_user->image_thumb ) !!}" width="50px">
                         </span>
                        <a data-toggle="dropdown" class="dropdown-toggle" href="#" aria-expanded="false">
                        <span class="clear"> <span class="block m-t-xs"> <strong class="font-bold">{{Auth::user()->name }} {{Auth::user()->surname }}</strong>
                            </span> <span class="text-muted text-xs block">@foreach($logged_user->roles as $v) {{ $v->description }}, @endforeach
                                <b class="caret"></b></span>
                            </span>
                        </a>
                        <ul class="dropdown-menu animated fadeInRight m-t-xs">
                            <li><a href="{{route('setting.user.profile')}}">Profile</a></li>
                            <li><a target="_blank" href="{{url('/ext/invite-guest')}}/{{ Auth::user()->token_id }}">Pozvánka</a></li>
                            {{--<li><a href="mailbox.html">Mailbox</a></li>--}}
                            <li class="divider"></li>
                            <li>
                                <a href="#"
                                   onclick="event.preventDefault();
                                document.getElementById('logout-form-sidebar').submit();">
                                    Odhlásenie
                                </a>
                                <form id="logout-form-sidebar" action="{{ URL::to('logout') }}" method="POST" style="display: none;">
                                    {{ csrf_field() }}
                                </form>
                            </li>
                        </ul>
                    </div>
                    @endif
                </div>
                <div class="logo-element">
                    IN+
                </div>
            </li>
            @component('layouts.menu')@endcomponent
        </ul>

    </div>
</nav>
