@extends('layouts.app')

@section('title', 'Zoznam spoločností')

@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">

                    </div>
                    <div class="ibox-content">
                        <table class="table table-hover" id="company_table">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Firma</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Kont. osoba</th>
                                <th width="200px"></th>
                            </tr>
                            </thead>
                            <tbody>
                            @if($items)
                                @foreach($items as $item => $v)
                                <tr>
                                    <td><img src="{{ $v->image_thumb }}" style="max-height: 35px; max-width: 75px;"></td>
                                    <td>{{ $v->company_name }}</td>
                                    <td>{{ $v->email }}</td>
                                    <td>{{ $v->phone }}</td>
                                    <td>{{ $v->contact_person }}</td>
                                    <td>
                                        <a type="button" data-item-id="{{ $v->id }}"
                                           class="m-l-sm pull-right btn btn-danger btn-xs delete-alert">Zmazať</a>

                                        {{ Form::open(['method' => 'DELETE', 'route' => ['setting.company.destroy', $v->id ],
                                            'class' => 'class="m-l-sm pull-right btn btn-danger btn-xs delete-alert hide',
                                            'id' => 'item-del-'. $v->id  ])
                                        }}
                                        {{Form::hidden('user_id', $v->id  )}}
                                        {{ Form::close() }}

                                        <a href="{{ route('setting.company.edit', ['user' => $v->id ]) }}" type="button" class="pull-right btn btn-success btn-xs m-l-sm">Upraviť</a>
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

    {{--<script src="{!! asset('js/plugins/dataTables/datatables.min.js') !!}" type="text/javascript"></script>--}}
    <script type="text/javascript" src="https://cdn.datatables.net/v/bs/dt-1.10.16/datatables.min.js"></script>


    <script>
        $(document).ready(function(){

            $('#company_table').DataTable({
                "pageLength": 50,
                "dom": 'lrfrtip',
                "order": [[1, 'asc']]
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