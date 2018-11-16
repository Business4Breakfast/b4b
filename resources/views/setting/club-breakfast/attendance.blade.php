@extends('layouts.app')

@section('title', 'Raňajky klubu')

@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">

                    </div>
                    <div class="ibox-content">
                        @if(count($items) >0)
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th width="5%">#</th>
                                <th>Meno</th>
                                <th>Telefon</th>
                                <th>Dátum</th>
                                <th>Stav 1</th>
                                <th>Stav 2</th>
                                <th>Popis</th>
                            </tr>
                            </thead>
                            <tbody>
                                @foreach($items as $item => $v)
                                <tr>
                                    <td>{{$loop->iteration}}</td>
                                    <td><strong>@if($v->name ){{ $v->name }} {{ $v->surname }}@else Hosť @endif</strong> </td>
                                    <td>{{ $v->phone }}</td>
                                    <td>{{ Carbon\Carbon::createFromFormat('Y-m-d H:i:s',$v->event_from)->format('d.m.Y') }}</td>
                                    <td>@if($v->status_id == 1)
                                            <i class="fa fa-exclamation-circle text-danger"></i>
                                        @elseif($v->status_id == 2)
                                            <i class="fa fa-check-circle text-success"></i>
                                        @elseif($v->status_id == 3)
                                            <i class="fa fa-times-circle text-warning"></i>
                                        @else
                                            <i class="fa fa-times-circle text-info"></i>
                                        @endif
                                        {{ $v->status_name }}
                                    </td>
                                    <td>{{ $v->user_status_name }}</td>
                                    <td>{{ $v->pozn }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                        {{ $items->links() }}

                        @else
                            <p class="text-danger">Neexistujú žiadne záznamy</p>

                            <div class="alert alert-success">
                                Neexistujú žiadne záznamy.
                            </div>
                        @endif

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