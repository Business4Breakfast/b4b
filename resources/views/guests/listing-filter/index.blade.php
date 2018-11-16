@extends('layouts.app')

@section('title', 'Zoznam hostí')

@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <div class="row">
                            <form id="user_form" class="" method="GET" action="{{ route('guests.guest-listings-filter.index') }}" >
                                <div class="col-sm-1" style="padding-right: 0;">
                                    <div class="form-group">
                                        <label class="control-label" for="user_status">Stav</label>
                                        <select name="user_status" id="user_status" class="form-control">
                                            <option value=""  @if($req['user_status']  == "")  selected="selected" @endif >Všetky</option>
                                            <option value="90"  @if($req['user_status']  == 90)  selected="selected" @else selected="selected" @endif >Aktívne</option>
                                            <option value="80"  @if($req['user_status']  == 80)  selected="selected" @endif >Neaktívne</option>
                                            @foreach($user_status as $key =>$us)
                                                <option value="{{$us->id}}" @if($req['user_status']  == $us->id)  selected="selected" @endif >{{$us->status}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label class="control-label" for="club_id">Klub</label>
                                        <select name="club_id" id="club_id" class="form-control" data-placeholder="Klub..."  style="width:100%;"  tabindex="2" >
                                            <option value="">-- výber --</option>
                                            @foreach($clubs as $key =>$club)
                                                <option value="{{$club->id}}" @if($req['club_id']  == $club->id)  selected="selected" @endif >{{$club->title}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label class="control-label" for="user_created">Kontakt pozval</label>
                                        <select name="user_created" id="user_created" class="form-control chosen-select"  style="width:100%;"  tabindex="2">
                                            <option value="">-- výber --</option>
                                            @foreach($members as $member)
                                            <option value="{{$member->id}}" @if($req['user_created']  == $member->id)  selected="selected" @endif >{{$member->full_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label class="control-label" for="industry_id">Podnikanie</label>
                                        <select name="industry_id" id="industry_id" class="form-control chosen-select"  style="width:100%;"  tabindex="2">
                                            <option value="">-- výber --</option>
                                            @foreach($industry as $typ)
                                                <option value="{{$typ->id}}" @if($req['industry_id']  == $typ->id)  selected="selected" @endif >{{$typ->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label class="control-label" for="user_crm_status">Akcia CRM</label>
                                        <select name="user_crm_status" id="user_crm_status" class="form-control"  style="width:100%;"  tabindex="2">
                                            <option value="">-- výber --</option>
                                            @foreach($crm_status as $crm)
                                                <option value="{{$crm->id}}" @if($req['user_crm_status']  == $crm->id)  selected="selected" @endif >{{$crm->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <label class="control-label block" for="submit">&nbsp;</label>
                                    {{--<input type="submit" class="btn btn-primary" value="Filtrovať">--}}
                                    <a href="{{route('guests.guest-listings-filter.index', ['reset' => true])}}" class="btn btn-danger"><i class="fa fa-close"></i> </a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="ibox-content m-b-sm border-bottom">

                        <table class="table table-hover dataTable-guests">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Meno</th>
                                <th>Stav</th>
                                <th>Email</th>
                                <th>Telefon</th>
                                <th width="200px"></th>
                            </tr>
                            </thead>
                            <tfoot>
                            <tr>
                                <th>#</th>
                                <th>Meno</th>
                                <th>Stav</th>
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

    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/v/bs/dt-1.10.18/b-1.5.2/b-html5-1.5.2/b-print-1.5.2/datatables.min.js"></script>

    <script>

        $(document).ready(function(){

            var table = $('.dataTable-guests').DataTable({

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
                    {extend: 'csv',
                        title: 'Zoznam hosti',
                        exportOptions: {
                            columns: [1,2,3,4]
                        }
                    },
                    {extend: 'excel',
                        title: 'Zoznam hosti',
                        exportOptions: {
                            columns: [1,2,3,4]
                        }
                    },
                    {extend: 'pdf',
                        title: 'Zoznam_hosti',
                        exportOptions: {
                            columns: [1,2,3,4]
                        }
                    },

                    {extend: 'print',
                        exportOptions: {
                            columns: [1,2,3,4]
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
                    "data": function ( d ) {
                        return $.extend( {}, d, {
                            "user_status": $('#user_status').val(),
                            "user_crm_status": $('#user_crm_status').val(),
                            "user_created": $('#user_created').val(),
                            "club_id": $('#club_id').val(),
                            "industry_id": $('#industry_id').val(),
                            "_token":"{{csrf_token()}}"
                        } );
                    }
                },
                "columns": [
                    { "data": "id" },
                    { "data": "name" },
                    { "data": "status" },
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

            $('#user_status').change(function (){
                table.ajax.reload();
            });

            $('#user_crm_status').change(function (){
                table.ajax.reload();
            });

            $('#club_id').change(function (){
                table.ajax.reload();
            });

            $('#industry_id').change(function (){
                table.ajax.reload();
            });

            $('#user_created').change(function (){
                table.ajax.reload();
            })

        });

    </script>

@endsection