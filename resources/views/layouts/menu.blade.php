@foreach($backend_menu as $key => $item)
<li class="{{ isActiveRequest($item['route']) }}
            @if(array_key_exists('children', $item))
                @if(in_array_r(Request::path(), $item['children'])) active @endif
            @endif
            @if( $item['group'] ==  Request::segment(1)) active @endif  {{--{{ ak je zhoda v group  }}--}}
        ">
    @if($item['d'] == 0)
        <a href="{{ URL::to($item['route'])}}"><i class="fa {{ $item['icon'] }}"></i> <span class="nav-label">{{ $item['title'] }} </span>@if(array_key_exists('children', $item))<span class="fa arrow"></span>@endif</a>
        @if(array_key_exists('children', $item))
            <ul class="nav nav-second-level  collapse @if(in_array_r(Request::path(), $item['children'])) in @endif" style="">
                @foreach($item['children'] as $item_second)
                    <li class="{{ isActiveRequest($item_second['route']) }}
                            @if(array_key_exists('children', $item_second))
                                @if(in_array_r(Request::path(), $item_second['children'])) active @endif
                            @endif
                            @if(Request::segment(2) == routeExplodeHelper($item_second['route'], 1))  active @endif
                            ">

                        <a href="{{ URL::to($item_second['route'])}}"><i class="fa {{ $item_second['icon']}}"></i> {{ $item_second['title'] }}@if(array_key_exists('children', $item_second))<span class="fa arrow"></span>@endif</a>
                        @if(array_key_exists('children', $item_second))
                        <ul class="nav nav-third-level collapse @if(in_array_r(Request::path(), $item_second['children'])) in @endif">
                            @foreach($item_second['children'] as $item_third)
                                <li class="{{ isActiveRequest($item_third['route']) }}">
                                    <a href="{{ URL::to($item_third['route'])}}">{{ $item_third['title'] }}</a>
                                </li>
                            @endforeach
                        </ul>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif
    @endif
</li>
@endforeach
{{--<li class="landing_link">--}}
    {{--<a>test</a>--}}
{{--</li>--}}