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
                        <table class="table table-hover" id="item_table">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th class="hide"></th>
                                <th>Meno</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Účet</th>
                                <th>Kluby</th>
                                <th width="200px"></th>
                            </tr>
                            </thead>
                            <tbody>
                            @if($items)
                                @foreach($items as $item => $v)
                                <tr>
                                    <td><img src="{{ $v->image_thumb }}" height="35px"></td>
                                    <td class="hide">{{ $v->surname }}</td>
                                    <td>{{ $v->name }} {{ $v->surname }}</td>
                                    <td>{{ $v->email }}</td>
                                    <td>{{ $v->phone }}</td>
                                    <td>@foreach($v->roles as $role) {{$role->name}}, @endforeach</td>
                                    <td>
                                        @if($v->clubs->count() > 0)
                                        @foreach($v->clubs as $c) {{$c->title}}, @endforeach</td>
                                        @else
                                            <span class="text-danger">neaktívny</span>
                                        @endif
                                    <td>
                                        <a type="button" data-item-id="{{ $v->id }}"
                                           class="m-l-sm pull-right btn btn-danger btn-xs delete-alert"><i class="fa fa-trash-o"></i> </a>

                                        {{ Form::open(['method' => 'DELETE', 'route' => ['setting.user.destroy', $v->id ] ,
                                            'class' => 'class="m-l-sm pull-right btn btn-danger btn-xs delete-alert hide',
                                            'id' => 'item-del-'. $v->id ] )
                                        }}
                                        {{ Form::hidden('user_id', $v->id ) }}
                                        {{ Form::close() }}

                                        <a href="{{ route('setting.member-stats.show', [ 'user' => $v->id ]) }}" class="pull-right btn btn-primary btn-xs m-l-sm"><i class="fa fa-search"></i></a>
                                        <a href="{{ route('setting.user.edit', [ 'user' => $v->id ]) }}" type="button" class="pull-right btn btn-success btn-xs m-l-sm">Upraviť</a>
                                        <a href="{{ route('setting.user.permissions', ['user' => $v->id ] ) }}" type="button" class="pull-right btn btn-warning btn-xs"><i class="fa fa-key"></i></a>
                                    </td>
                                </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
{{--                        {{ $items->links() }}--}}

                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('css')

@endsection

@section('scripts')


    <script type="text/javascript" src="https://cdn.datatables.net/v/bs/dt-1.10.16/datatables.min.js"></script>

    <script>

        $(document).ready(function(){

            // sorting s diakritikou
            $('#item_table').DataTable({
                "pageLength": 25,
                "dom": 'lrfrtip',
                columnDefs : [
                    { targets: 0, type: 'locale-compare' }
                ],
                "order": [1, 'asc']
            });
            $('.delete-alert').click(function (e) {

                var id = $(e.currentTarget).attr("data-item-id");
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
                    document.getElementById('item-del-'+id).submit();
                    swal("Deleted", "Záznam bol zmazaný", "success");
                });
            });

        });

    </script>

@endsection