@extends('layouts.app')

@section('title', 'Generovanie backend menu')

@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Role užívateľov </h5>
                    </div>
                    <div class="ibox-content">
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
                            @foreach($roles as $item => $v)
                            <tr>
                                <td>{{ $v->id }}</td>
                                <td>{{ $v->display_name }}</td>
                                <td id="role_name-{{ $v->id }}">{{ $v->name }}</td>
                                <td>{{ $v->description }}</td>
                                <td>
                                    <a type="button" data-role-id="{{ $v['id'] }}"
                                       class="m-l-sm pull-right btn btn-danger btn-xs delete-alert">Zmazať</a>
                                    <form id="role-del-{!! $v['id']!!}" action="{{route('developer.role.destroy')}}" method="POST" style="display: none;">
                                        {{Form::token()}}
                                        {{Form::hidden('id', $v['id'] )}}
                                    </form>
                                    <a href="{{ route('developer.role.edit', ['id' => $v['id']]) }}" type="button" class="pull-right btn btn-success btn-xs">Upraviť</a>
                                    <a href="{{ route('developer.role.permissions', ['id' => $v['id']]) }}" type="button" class="pull-right btn btn-warning btn-xs m-r-sm"><i class="fa fa-key"></i> </a>
                                    <button type="button" class="pull-right btn btn-default btn-xs clipboard m-r-sm" data-clipboard-target="#role_name-{{ $v->id }}"><i class="fa fa-clipboard"></i></button>
                                </td>
                            </tr>
                            @endforeach
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

                var id = $(e.currentTarget).attr("data-role-id");
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
                    document.getElementById('role-del-'+id).submit();
                    swal("Deleted", "Záznam bol zmazaný", "success");
                });
            });

        });

    </script>

@endsection