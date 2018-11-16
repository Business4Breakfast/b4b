@extends('layouts.app')

@section('title', 'Zoznam užívateľov')

@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Nastavenie práv role: {{$role->display_name}}</h5>
                    </div>
                    <div class="ibox-content">
                        <div class="panel-body">
                            <div class="panel-group" id="accordion">
                                {!! Form::open(['route' => 'developer.role.permissions.save', 'class' => 'register-form']) !!}
                                {{ Form::hidden('role_id',$role->id) }}
                            @if($items)
                                @foreach($items as $k => $v)
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h5 class="panel-title">
                                                <a data-toggle="collapse" data-parent="#accordion" href="#{{$k}}"
                                                   aria-expanded="false" class="collapsed">Skupina ({{ucfirst($k)}})</a>
                                            </h5>
                                        </div>
                                        <div id="{{$k}}" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">


                                            <ul class="todo-list m-t small-list">
                                                @foreach($v as $k => $p )
                                                    <li>
                                                        {{--<input  type="hidden"  name="permission[{{$k}}]" value="0">--}}
                                                        <input
                                                                @foreach($role_permission as $r )
                                                               @if($r == $p->id) checked="checked"  @endif
                                                               @endforeach

                                                               type="checkbox"  id="checkbox{{ $p->id }}" name="permission[]" value="{{$p->id}}"
                                                        >

                                                        <span class="m-l-xs">{{ $p->display_name }}</span>
                                                    </li>
                                                    @if($v->last() === $p)
                                                        <li>
                                                            <button class="btn btn-sm btn-success m-t-sm" type="submit">Uložiť</button>
                                                        </li>
                                                    @endif
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                                {{Form::close()}}



                        {{--{{Form::open(  [ 'action' => route('setting.user.permissions.save'), 'method'=>'post']) }}--}}
                        {{--{!! Form::open(['route' => 'developer.role.permissions.save', 'class' => 'register-form']) !!}--}}
                        {{--{{ Form::hidden('role_id',$role->id) }}--}}

                        {{--<table class="table table-hover">--}}
                            {{--<thead>--}}
                            {{--<tr>--}}
                                {{--<th>#</th>--}}
                                {{--<th>Oprávnenie</th>--}}
                                {{--<th>Popis</th>--}}
                                {{--<th width="200px"></th>--}}
                            {{--</tr>--}}
                            {{--</thead>--}}
                            {{--<tbody>--}}
                            {{--@if($items)--}}
                                {{--@foreach($items as $item => $i)--}}
                                {{--<tr>--}}
                                    {{--<td>{{ $i->id }}</td>--}}
                                    {{--<td>--}}
                                        {{--<div class="checkbox m-r-xs">--}}
                                            {{--<input @foreach($role_permission as $p )--}}
                                                   {{--@if($p == $i->id) checked="checked" @endif--}}
                                                   {{--@endforeach--}}
                                            {{--type="checkbox" class="checkbox" id="checkbox1" name="permission[]" value="{{$i->id}}">--}}

                                            {{--<label for="checkbox1">--}}
                                                {{--{{ $i->display_name }} {{ $i->surname }}--}}
                                            {{--</label>--}}
                                        {{--</div>--}}
                                    {{--</td>--}}
                                    {{--<td>{{ $i->description }}</td>--}}
                                    {{--<td>--}}
                                        {{--<button class="btn btn-sm btn-default" type="submit">Uložiť</button>--}}
                                    {{--</td>--}}
                                {{--</tr>--}}
                                {{--@endforeach--}}
                            {{--@endif--}}
                            {{--</tbody>--}}
                        {{--</table>--}}
                        {{--{{Form::close()}}--}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('css')

@endsection

@section('scripts')


<script>

    $(document).ready(function(){



    });

</script>

@endsection