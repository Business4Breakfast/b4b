@extends('layouts.app')

@section('title', 'Zoznam hostí')

@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">


        <div class="ibox-content m-b-sm border-bottom">
            <div class="row">
                <div class="col-sm-3">
                    <div class="form-group">
                        <label class="control-label" for="order_id">Order ID</label>
                        <input type="text" id="order_id" name="order_id" value="" placeholder="Order ID" class="form-control">
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <label class="control-label" for="status">Order status</label>
                        <input type="text" id="status" name="status" value="" placeholder="Status" class="form-control">
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group">
                        <label class="control-label" for="customer">Customer</label>
                        <input type="text" id="customer" name="customer" value="" placeholder="Customer" class="form-control">
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group">
                        <label class="control-label" for="customer">Customer</label>
                        <input type="text" id="customer" name="customer" value="" placeholder="Customer" class="form-control">
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group">
                        <button type="button" class="btn btn-w-m btn-primary m-t-md pull-right">Filtruj</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content m-b-sm border-bottom">

                        {{--{{ $items->links() }}--}}

                        <table class="table table-hover dataTable-guests">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Meno</th>
                                <th>Email</th>
                                <th>Telefon</th>
                                {{--<th>Pozvaný</th>--}}
                                {{--<th>Status</th>--}}
                                <th width="200px"></th>
                            </tr>
                            </thead>
                            {{--<tbody>--}}
                            {{--@if($items)--}}
                                {{--@foreach($items as $item => $v)--}}
                                {{--<tr>--}}
                                    {{--<td>{{ $v->id }}</td>--}}
                                    {{--<td>{{ $v->title}} {{ $v->name}} {{ $v->surname}}</td>--}}
                                    {{--<td>{{ $v->email}}</td>--}}
                                    {{--<td>{{ $v->phone}}</td>--}}
                                    {{--<td>{{ $v->invite_user}}/{{ $v->invite_club}}</td>--}}
                                    {{--<td>{{ $v->status}}</td>--}}

                                    {{--<td>--}}
                                        {{--<a type="button" data-item-id="{{ $v->id }}"--}}
                                           {{--class="m-l-sm pull-right btn btn-danger btn-xs delete-alert">Zmazať</a>--}}

                                        {{--{{ Form::open(['method' => 'DELETE', 'route' => ['guests.guest-listings.destroy', $v->id ],--}}
                                            {{--'class' => 'class="m-l-sm pull-right btn btn-danger btn-xs delete-alert hide',--}}
                                            {{--'id' => 'item-del-'. $v->id  ])--}}
                                        {{--}}--}}
                                        {{--{{Form::hidden('user_id', $v->id  )}}--}}
                                        {{--{{ Form::close() }}--}}

                                        {{--<a href="{{ route('guests.guest-listings.edit', ['user' => $v->id ]) }}" type="button" class="pull-right btn btn-success btn-xs m-l-sm">Upraviť</a>--}}
                                    {{--</td>--}}
                                {{--</tr>--}}
                                {{--@endforeach--}}
                            {{--@endif--}}
                            {{--</tbody>--}}
                        </table>

                        {{--{{ $items->links() }}--}}

                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('css')

@endsection

@section('scripts')

    <script src="{!! asset('js/plugins/dataTables/datatables.min.js') !!}" type="text/javascript"></script>


    <script>

        $(document).ready(function(){

            $('.dataTable-guests').DataTable({
                "pageLength": 50,

                dom: '<"html5buttons"B>lTgftpi',

                "language": {
                    "lengthMenu": "Zobraziť _MENU_ záznamov",
                    "zeroRecords": "Nenašli sa žiadne záznamy",
                    "info": "Showing page _PAGE_ of _PAGES_",
                    "infoEmpty": "No records available",
                    "infoFiltered": "(filtered from _MAX_ total records)",
                    "search": "Textové hľadanie"
                },

                buttons: [
                    { extend: 'copy'},
                    {extend: 'csv'},
                    {extend: 'excel',
                        title: 'Zoznam hosti',
                        exportOptions: {
                            columns: [1,2]
                        }
                    },
                    {extend: 'pdf',
                        title: 'Zoznam_hosti',
                            exportOptions: {
                                columns: [1,2,3]
                            }
                        },

                    {extend: 'print',
                        exportOptions: {
                            columns: [1,2,3]
                        },
                        customize: function (win){
                            $(win.document.body).addClass('white-bg');
                            $(win.document.body).css('font-size', '10px');

                            $(win.document.body).find('table')
                                .addClass('compact')
                                .css('font-size', 'inherit');
                        }

                    }
                ],

                "processing": true,
                "serverSide": true,
                "ajax":{
                    "url": "{{ url('ajax/get-guest-data') }}",
                    "dataType": "json",
                    "type": "POST",
                    "data":{ _token: "{{csrf_token()}}"}
                },
                "columns": [
                    { "data": "id" },
                    { "data": "name" },
                    { "data": "email" },
                    { "data": "phone" },
                    { "data": "action"}
                ]
            });


            $('.dataTable-guests').on("click", ".delete-alert", function(){

                var id = $(this).attr('data-item-id');
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