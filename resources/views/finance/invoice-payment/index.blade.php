@extends('layouts.app')

@section('title', 'Zoznam faktúr')

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
                                    <td>{{ str_limit($v->description_payment,50) }}</td>
                                </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                        {{ $items->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('css')

    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs/jszip-2.5.0/dt-1.10.16/b-1.5.1/b-html5-1.5.1/b-print-1.5.1/r-2.2.1/datatables.min.css"/>

@endsection

@section('scripts')

    {{--<script src="{!! asset('js/plugins/dataTables/datatables.min.js') !!}" type="text/javascript"></script>--}}
    {{--<script type="text/javascript" src="https://cdn.datatables.net/v/bs/dt-1.10.16/datatables.min.js"></script>--}}

    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/v/bs/jszip-2.5.0/dt-1.10.16/b-1.5.1/b-html5-1.5.1/b-print-1.5.1/r-2.2.1/datatables.min.js"></script>


    <script>

        $(document).ready(function(){

            // sorting s diakritikou
            $('#item_table').DataTable({
                "pageLength": 30,
                "dom": 'lrfrtip',
                responsive: true,
                columnDefs : [
                    { targets: 0, type: 'locale-compare' }
                ],
                "order": [0, 'desc']
            });


            jQuery.extend( jQuery.fn.dataTableExt.oSort, {
                "locale-compare-asc": function ( a, b ) {
                    return a.localeCompare(b, 'da', { sensitivity: 'accent' })
                },
                "locale-compare-desc": function ( a, b ) {
                    return b.localeCompare(a, 'da', { sensitivity: 'accent' })
                }
            });

        });

    </script>

@endsection