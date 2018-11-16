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
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th width="5%">#</th>
                                <th>Firma</th>
                                <th class="text-right">Cena</th>
                                <th class="text-right">Cena s DPH</th>
                                <th>Splatnosť</th>
                                <th>Popis transakcie</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if($items)
                                @foreach($items as $item => $v)
                                <tr>
                                    <td><b>{{ $v->variable_symbol }}</b></td>
                                    <td>{{ $v->company_title }}</td>
                                    <td align="right"><strong>{{ number_format((float)$v->price, 2, ',', ' ')}}</strong></td>
                                    <td align="right" @if($v->price_payment < 0)class="text-danger" @else class="text-success"  @endif><strong>{{ number_format((float)$v->price_payment, 2, ',', ' ') }}</strong></td>

                                    <td>{{ Carbon\Carbon::createFromFormat('Y-m-d H:i:s',$v->date_payment)->format('d.m.Y') }}</td>
                                    <td>{{ $v->description_payment }}</td>
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