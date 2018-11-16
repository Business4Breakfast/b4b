@extends('layouts.app')

@section('title', 'Franchisory')

@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Firma</th>
                                <th>Termín</th>
                                <th>Cena</th>
                                <th width="200px"></th>
                            </tr>
                            </thead>
                            <tbody>
                            @if($franchisors)
                                @foreach($franchisors as $item => $v)
                                <tr>
                                    <td>{{ $v->id }}</td>
                                    <td>@if($v->company->company_name){{ $v->company->company_name}}@endif</td>
                                    <td>{{Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $v->valid_from)->format('d.m.Y')}}-{{Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $v->valid_to)->format('d.m.Y')}}</td>
                                    <td>{{ $v->price }}</td>
                                    <td>
                                        <a type="button" data-item-id="{{ $v->id }}"
                                           class="m-l-sm pull-right btn btn-danger btn-xs delete-alert"><i class="fa fa-trash-o"></i> </a>

                                        {{ Form::open(['method' => 'DELETE', 'route' => ['franchisor.destroy', $v->id ],
                                            'class' => 'class="m-l-sm pull-right btn btn-danger btn-xs delete-alert hide',
                                            'id' => 'item-del-'. $v->id  ])
                                        }}
                                        {{Form::hidden('franchisor_id', $v->id  )}}
                                        {{ Form::close() }}
                                        <a href="{{ route('franchisor.edit', ['franchisor' => $v->id ]) }}" type="button" class="pull-right btn btn-success btn-xs m-l-sm"><i class="fa fa-edit"></i> </a>
                                    </td>
                                </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                        {{--{{ $franchisors->links() }}--}}
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