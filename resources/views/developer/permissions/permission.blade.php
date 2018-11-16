@extends('layouts.app')

@section('title', 'Generovanie backend menu')

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
                                @if($items)
                                    @foreach($items as $k => $item)
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <h5 class="panel-title">
                                                    <a data-toggle="collapse" data-parent="#accordion" href="#{{$k}}"
                                                       aria-expanded="false" class="collapsed">Skupina ({{ucfirst($k)}})</a>
                                                </h5>
                                            </div>
                                            <div id="{{$k}}" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
                                                <table class="table table-hover">
                                                    <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Názov</th>
                                                        <th>code name</th>
                                                        <th>Popis</th>
                                                        <th></th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @foreach($item as $it => $v)
                                                        <tr>
                                                            <td>{{ $v->id }}</td>
                                                            <td>{{ $v->display_name }}</td>
                                                            <td id="permission_name-{{ $v->id }}">{{ $v->name }}</td>
                                                            <td>{{ $v->description }}</td>
                                                            <td>
                                                                <a type="button" data-permission-id="{{ $v['id'] }}"
                                                                   class="m-l-sm pull-right btn btn-danger btn-xs delete-alert">Zmazať</a>
                                                                <form id="permission-del-{!! $v['id']!!}" action="{{route('developer.permission.destroy')}}" method="POST" style="display: none;">
                                                                    {{Form::token()}}
                                                                    {{Form::hidden('id', $v['id'] )}}
                                                                </form>
                                                                <a href="{{ route('developer.permission.edit', ['id' => $v['id']]) }}" type="button" class="pull-right btn btn-success btn-xs">Upraviť</a>
                                                                <button type="button" class="pull-right btn btn-default btn-xs clipboard m-r-sm" data-clipboard-target="#permission_name-{{ $v->id }}"><i class="fa fa-clipboard"></i></button>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
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

        $('.delete-alert').click(function (e) {

            var id = $(e.currentTarget).attr("data-permission-id");
            swal({
                title: "Ste si istý?",
                text: "Táto operácia je nevratná!",
                type: "warning",
                showCancelButton: true,
                cancelButtonText: "Zrušiť",
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Áno, zmazať",
                closeOnConfirm: false
            }, function () {
                document.getElementById('permission-del-'+id).submit();
                swal("Deleted", "Záznam bol zmazaný", "success");
            });
        });

    });

</script>

@endsection