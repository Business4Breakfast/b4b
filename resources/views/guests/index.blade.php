@extends('layouts.app')

@section('title', 'Zoznam hostí')

@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content m-b-sm border-bottom">

                        <table class="table table-hover dataTable-guests">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Meno</th>
                                <th>Email</th>
                                <th>Telefon</th>
                                <th width="200px"></th>
                            </tr>
                            </thead>
                            <tfoot>
                            <tr>
                                <th>#</th>
                                <th>Meno</th>
                                <th>Email</th>
                                <th>Telefon</th>
                                <th width="200px"></th>
                            </tr>
                            </tfoot>
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


    {{--<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>--}}
    {{--<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>--}}
    {{--<script type="text/javascript" src="https://cdn.datatables.net/v/dt/jq-3.2.1/dt-1.10.16/b-1.5.1/b-html5-1.5.1/b-print-1.5.1/r-2.2.1/datatables.min.js"></script>--}}

    <script>

        $(document).ready(function(){

            $('.dataTable-guests').DataTable({

                "pageLength": 50,

                dom: '<"html5buttons"B>lTgftpi',

                "language": {
                    "lengthMenu": "Zobraziť _MENU_ záznamov",
                    "zeroRecords": "Nenašli sa žiadne záznamy",
                    //"info": "Showing page _PAGE_ of _PAGES_",
                    "infoEmpty": "No records available",
                   // "infoFiltered": "(filtered from _MAX_ total records)",
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



//                dom: '<"html5buttons"B>lTfgitp',
//                buttons: [
//                    { extend: 'copy'},
//                    {extend: 'csv'},
//                    {extend: 'excel', title: 'ExampleFile'},
//                    {extend: 'pdf', title: 'ExampleFile'},
//
//                    {extend: 'print',
//                        customize: function (win){
//                            $(win.document.body).addClass('white-bg');
//                            $(win.document.body).css('font-size', '10px');
//
//                            $(win.document.body).find('table')
//                                .addClass('compact')
//                                .css('font-size', 'inherit');
//                        }
//                    }
//                ],


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

            $('<label style="margin-left: 10px;">Filter by ' +
                '<select class="form-control input-sm">'+
                '<option value="volvo">Completed Trip</option>'+
                '<option value="saab">Upcoming Trip</option>'+
                '</select>' +
                '</label>').appendTo("#user_wrapper #user_length");

        });

    </script>

@endsection