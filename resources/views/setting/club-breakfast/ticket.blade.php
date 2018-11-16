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
                        @if(count($items)>0)
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th width="5%">#</th>
                                <th>Od</th>
                                <th>Komu</th>
                                <th class="text-right">Hodnota</th>
                                <th>Dátum</th>
                                <th>Popis transakcie</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                                @foreach($items as $item => $v)
                                <tr>
                                    <td>{{ $v->id }}</td>
                                    <td><strong>@if($v->from_name ){{ $v->from_name }} {{ $v->from_surname }}@else Hosť @endif</strong></td>
                                    <td><strong>@if($v->to_name ){{ $v->to_name }} {{ $v->to_surname }}@else Hosť @endif</strong></td>
                                    <td align="right" @if($v->value_1 < 0)class="text-danger" @else class="text-success"  @endif><strong>{{ number_format((float)$v->value_1, 2, ',', ' ') }}</strong></td>
                                    <td>{{ Carbon\Carbon::createFromFormat('Y-m-d',$v->date)->format('d.m.Y') }}</td>
                                    <td>{{ $v->ref_name }}</td>
                                    <td>{{ $v->description }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @else
                            <p class="text-danger">Neexistujú žiadne záznamy</p>
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