@extends('layouts.app')

@section('title', 'Zoznam užívateľov')

@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                    </div>

                    <div class="ibox-content">
                        <div class="panel-body">
                            <div class="panel-group" id="accordion">
                                {!! Form::open(['route' => 'setting.user.permissions.save', 'class' => 'register-form']) !!}
                                {{ Form::hidden('user_id',$user->id) }}

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
                                                @foreach($v as $p )
                                                <li>
                                                    {{--<input  type="hidden"  name="permission[{{$p->name}}]" value="0">--}}

                                                    <input @foreach($user_permission as $r )
                                                           @if($r->name == $p->name) checked="checked" @endif
                                                           @endforeach
                                                           type="checkbox"  id="checkbox{{ $p->id }}" name="permission[{{$p->name}}]" value="1"
                                                            @foreach($role_user_permission as $ur )
                                                                @if($ur == $p->id) disabled="disabled" @endif
                                                            @endforeach
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