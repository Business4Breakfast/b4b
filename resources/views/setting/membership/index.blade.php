@extends('layouts.app')

@section('title', 'Zoznam spoločností')

@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <div class="row">
                            <form id="user_form" class="" method="GET" action="{{ route('setting.membership.index') }}" >

                                @include('components.validation')

                                <div class="col-sm-1" style="padding-right: 0;">
                                    <div class="form-group">
                                        <label class="control-label" for="search_status">Stav</label>
                                        <select name="search_status" id="search_status" class="form-control">
                                            <option value="2"  selected="selected" >Všetky</option>
                                            <option value="1"  @if($req['search_status']  == 1)  selected="selected" @endif >Aktívne</option>
                                            <option value="0"  @if($req['search_status']  == 0)  selected="selected" @endif >Neaktívne</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group" id="data_5">
                                        <label class="control-label">Rozsah dátumov</label>
                                        <div class="input-daterange input-group" id="datepicker">
                                            <input type="text" class="form-control" name="date_from" value="{{$req['date_from']}}"/>
                                            <span class="input-group-addon">do</span>
                                            <input type="text" class="form-control" name="date_to" value="{{$req['date_to']}}" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label class="control-label" for="search_company">Klub</label>
                                        <select name="search_club" id="search_club" class="form-control" data-placeholder="Klub..."  style="width:100%;"  tabindex="2">
                                            <option value="">-- výber --</option>
                                            @foreach($clubs as $key =>$club)
                                                <option value="{{$club->id}}" @if($req['search_club']  == $club->id)  selected="selected" @endif >{{$club->title}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                    <label class="control-label" for="search_company">Odberateľ</label>
                                    <select name="search_company" id="search_company" data-placeholder="Odberateľ..." class="chosen-select"  style="width:100%;"  tabindex="3">
                                            <option value="">-- výber --</option>
                                            @foreach($company as $key =>$c)
                                                <option value="{{$c->id}}" @if($req['search_company']  == $c->id)  selected="selected" @endif>{{$c->company_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label class="control-label" for="search_user">Člen</label>
                                        <select name="search_user" id="search_user" data-placeholder="Uživateľ..." class="chosen-select"  style="width:100%;"  tabindex="4">
                                            <option value="">-- výber --</option>
                                            @foreach($users as $key =>$user)
                                                <option value="{{$user->id}}" @if($req['search_user']  == $user->id)  selected="selected" @endif>{{$user->full_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <label class="control-label block" for="submit">&nbsp;</label>
                                    <input type="submit" class="btn btn-primary" value="Filtrovať">
                                    <a href="{{route('setting.membership.index', ['reset' => true])}}" class="btn btn-danger"><i class="fa fa-close"></i> </a>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <table class="table table-hover" id="item_table">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Dni</th>
                                <th>Firma</th>
                                <th>Termín</th>
                                <th>Cena</th>
                                <th>Aktívne</th>
                                <th width="200px"></th>
                            </tr>
                            </thead>
                            <tbody>
                            @if($items)
                                @foreach($items as $item => $v)
                                <tr>
                                {{-- <td>{{ $loop->iteration }}</td>--}}
                                    <td>MB-{{ $v->id }}</td>
                                    <td>
                                        @if(Carbon\Carbon::now()->diffInDays( Carbon\Carbon::createFromFormat('Y-m-d H:i:s' , $v->valid_to) ,  true) < 60 &&
                                                                Carbon\Carbon::now()->diffInDays( Carbon\Carbon::createFromFormat('Y-m-d H:i:s' , $v->valid_to) ,  true) >= 30 )
                                            <strong class="text-warning">{{ Carbon\Carbon::now()->diffInDays( Carbon\Carbon::createFromFormat('Y-m-d H:i:s' , $v->valid_to) ,  false) }}</strong>
                                        @elseif(Carbon\Carbon::now()->diffInDays( Carbon\Carbon::createFromFormat('Y-m-d H:i:s' , $v->valid_to) ,  true) < 30)
                                            <strong class="text-danger">{{ Carbon\Carbon::now()->diffInDays( Carbon\Carbon::createFromFormat('Y-m-d H:i:s' , $v->valid_to) ,  false) }}</strong>
                                        @else
                                            <strong class="text-success">{{ Carbon\Carbon::now()->diffInDays( Carbon\Carbon::createFromFormat('Y-m-d H:i:s' , $v->valid_to) ,  false) }}</strong>
                                        @endif
                                    </td>
                                    <td>
                                        @if($v->company_name){{ $v->company_name}} @endif [@foreach($v->users as $u) {{$u->full_name}},  @endforeach]
                                            [ @foreach($v->clubs as $c) {{$c->short_title}},  @endforeach ]
                                    </td>
                                    <td>{{Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $v->valid_from)->format('d.m.Y')}}-{{Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $v->valid_to)->format('d.m.Y')}}
                                    </td>
                                    <td>{{ $v->price }}</td>
                                        @if($v->active  == 1)
                                            <td class="text-success"> <i class="fa fa-check-circle"></i> <strong> Aktívne</strong></td>
                                        @else
                                            <td class="text-danger"> <i class="fa fa-times-circle"></i> <strong> Neaktívne</strong></td>
                                        @endif
                                    <td>
                                        @permission('memberships-delete')
                                        <a type="button" data-item-id="{{ $v->id }}"
                                           class="m-l-sm pull-right btn btn-danger btn-xs delete-alert"><i class="fa fa-trash-o"></i> </a>
                                        @endpermission

                                        {{ Form::open(['method' => 'DELETE', 'route' => ['setting.membership.destroy', $v->id ],
                                            'class' => 'class="m-l-sm pull-right btn btn-danger btn-xs delete-alert hide',
                                            'id' => 'item-del-'. $v->id  ])
                                        }}
                                        {{Form::hidden('user_id', $v->id  )}}
                                        {{ Form::close() }}

                                        @permission('memberships-edit')
                                            <a href="{{ route('setting.membership.edit', ['user' => $v->id ]) }}" type="button" class="pull-right btn btn-success btn-xs m-l-sm"><i class="fa fa-edit"></i> </a>
                                            <a href="{{ route('setting.membership.payment', ['user' => $v->id ]) }}" type="button" class="pull-right btn btn-info btn-xs m-l-sm"><i class="fa fa-money"></i> </a>
                                            <a href="{{ route('setting.membership.renewal', ['membership' => $v->id ]) }}" type="button" class="pull-right btn btn-warning btn-xs m-l-sm"><i class="fa fa-calendar"></i> </a>
                                        @endpermission
                                    </td>

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

@endsection

@section('scripts')

    <script type="text/javascript" src="https://cdn.datatables.net/v/bs/dt-1.10.16/datatables.min.js"></script>

    <script>

        $(document).ready(function(){

            // sorting s diakritikou
            $('#item_table').DataTable({
                "pageLength": 25,
                "dom": 'lrfrtip',
                columnDefs : [
                    { targets: 0, type: 'locale-compare' }
                ],
                "order": [1, 'asc']
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