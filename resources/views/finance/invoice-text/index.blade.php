@extends('layouts.app')

@section('title', 'číselníky')

@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">

                    </div>
                    <div class="ibox-content">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th width="400px">Názov</th>
                                <th width="300px">Popis</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            @if($items)
                                @foreach($items as $item => $v)
                                <tr>
                                    <td>{{ $v->name }}</td>
                                    <td>({{ str_limit($v->description, 30) }})</td>
                                    <td>
                                        <a type="button" data-item-id="{{ $v->id }}"
                                           class="m-l-sm pull-right btn btn-danger btn-xs delete-alert"><i class="fa fa-trash"></i><span class="hidden-xs"> {{__('form.delete')}}</span></a>

                                        {{ Form::open(['method' => 'DELETE', 'route' => ['finance.'.$module.'.destroy', $v->id ],
                                            'class' => 'class="m-l-sm pull-right btn btn-danger btn-xs delete-alert hide',
                                            'id' => 'item-del-'. $v->id  ])}}
                                        {{Form::hidden('user_id', $v->id  )}}
                                        {{ Form::close() }}

                                        <a href="{{ route('finance.'.$module.'.edit', ['user' => $v->id ]) }}" type="button" class="pull-right btn btn-info btn-xs m-l-sm"><i class="fa fa-edit"></i><span class="hidden-xs"> {{__('form.edit_record')}}</span></a>

                                        {{--<a type="button" id="active" data-active="{{$v->id}}" data-status="{{$v->active}}"--}}
                                           {{--class="m-l-sm pull-right btn btn-success btn-xs"><i class="fa fa-check"></i></a>--}}

                                        <a type="button" onclick="event.preventDefault(); document.getElementById('active-form-{{$v->id}}').submit();"
                                           data-toggle="tooltip" data-placement="top"
                                            @if($v->active == 1)
                                                class="m-l-sm pull-right btn btn-success  btn-xs" title="Deaktivovať" ><i class="fa fa-check"></i>
                                            @else
                                                class="m-l-sm pull-right btn btn-danger  btn-xs" title="Aktivovať"><i class="fa fa-close"></i>
                                            @endif

                                        </a>

                                        {{ Form::open(['method' => 'PUT', 'route' => ['finance.'.$module.'.active', $v->id ],
                                            'class' => 'class="m-l-sm pull-right btn btn-danger btn-xs  hide',
                                            'id' => 'active-form-'. $v->id  ])
                                        }}
                                        {{Form::hidden('user_id', $v->id  )}}

                                        @if($v->active == 1)
                                            {{Form::hidden('active', 0 )}}
                                        @else
                                            {{Form::hidden('active', 1  )}}
                                        @endif
                                        {{ Form::close() }}

                                    </td>
                                </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
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

            $('.delete-alert').click(function (e) {

                var id = $(e.currentTarget).attr("data-item-id");
                swal({
                    title: "Ste si istý?",
                    text: "Táto operácia je nevratná!",
                    type: "warning",
                    showCancelButton: true,
                    cancelButtonText: "Zrušiť",
                    //confirmButtonColor: "#DD6B55",
                    //confirmButtonText: "Áno, zmazať",
                    closeOnConfirm: true,
                    showConfirmButton: true
                }, function () {
                    document.getElementById('item-del-'+id).submit();
                    swal("Deleted", "Záznam bol zmazaný", "success");
                });
            });

        });

    </script>

@endsection