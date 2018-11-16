@extends('layouts.app')

@section('title', 'Generovanie backend menu')

@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Backend menu </h5>
                    </div>
                    <div class="ibox-content">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Title</th>
                                <th>Rank</th>
                                <th>Parent</th>
                                <th>Route</th>
                                <th>Group</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($menu_arr as $item => $v)
                            <tr @if($v['id'] == 3)class="bg-danger-df"@endif>
                                <td>{{ $v['id'] }}</td>
                                <td><i class="m-r-sm fa {{ $v['icon'] }}"></i>{{ $v['title'] }}</td>
                                <td>{{ $v['rank'] }}</td>
                                <td>{{ $v['parent'] }}</td>
                                <td>{{ $v['route'] }}</td>
                                <td>{{ $v['block'] }}</td>
                                <td>
                                    <a type="button" data-test-id="{{ $v['id'] }}"
                                       class="m-l-sm pull-right btn btn-danger btn-xs delete-alert">Zmazať</a>

                                        <form id="menu-del-{!! $v['id']!!}" action="{{url('developer/menu-delete')}}" method="POST" style="display: none;">
                                            {{Form::token()}}
                                            {{Form::hidden('id', $v['id'] )}}
                                        </form>

                                    <a href="{{url('developer/menu-edit/'.$v['id'])}}" type="button" class="pull-right btn btn-success btn-xs">Upraviť</a>

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

    <link rel="stylesheet" href="{!! asset('js/plugins/nestable2/jquery.nestable.min.css') !!}" />

@endsection

@section('scripts')

    <script src="{!! asset('js/plugins/nestable2/jquery.nestable.min.js') !!}" type="text/javascript"></script>

<script>
    $(document).ready(function(){

        $('.delete-alert').click(function (e) {

            var id = $(e.currentTarget).attr("data-test-id");

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
                document.getElementById('menu-del-'+id).submit();
                swal("Deleted", "Záznam bol zmazaný", "success");
            });
        });


    });


</script>

@endsection