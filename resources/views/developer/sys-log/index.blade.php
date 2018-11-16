@extends('layouts.app')

@section('title', 'Zoznam faktúr')

@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <div class="row">
                            <form id="user_form" class="" method="GET" action="{{ route('developer.sys-log.index') }}" >
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label class="control-label" for="search_type">Typ</label>
                                        <select id="search_type" class="form-control" name="search_type">
                                            <option value="">Všetky</option>
                                            @if($modules)
                                                @foreach($modules as $m)
                                                    <option value="{{$m}}"  @if($req['search_type']  == $m)  selected="selected" @endif >{{$m}}</option>
                                                @endforeach
                                            @endif
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
                                        <label class="control-label" for="search_price">Text</label>
                                        <input type="text" id="search_text" name="search_text" value="{{$req['search_text']}}" placeholder="" class="form-control">
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <label class="control-label block" for="submit">&nbsp;</label>
                                    <input type="submit" class="btn btn-primary" value="Filtrovať">
                                    <a href="{{route('developer.sys-log.index', ['reset' => true])}}" class="btn btn-danger"><i class="fa fa-close"></i> </a>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="table-responsive">
                            <table class="table table-hover">
                            <thead>
                            <tr>
                                <th width="5%">#</th>
                                <th>Date</th>
                                <th>Transaction</th>
                                <th >Module</th>
                                <th>Id Module</th>
                                <th width="200px"></th>
                            </tr>
                            </thead>
                            <tbody>
                            @if($items)
                                @foreach($items as $item => $v)
                                <tr>
                                    <td>SYS-{{ $v->id }}</td>
                                    <td>{{ Carbon\Carbon::createFromFormat('Y-m-d H:i:s' , $v->log_date)->format('d.m.Y H:i') }}</td>
                                    <td>{{ $v->transaction }}</td>
                                    <td>{{ $v->module }}</td>
                                    <td>{{ $v->module_id }}</td>

                                    <td>
                                        <a type="button" data-item-id="{{ $v->id }}"
                                           class="m-l-sm pull-right btn btn-danger btn-xs delete-alert"><i class="fa fa-trash"></i></a>

                                        {{ Form::open(['method' => 'DELETE', 'route' => ['developer.sys-log.destroy', $v->id ],
                                            'class' => 'class="m-l-sm pull-right btn btn-danger btn-xs delete-alert hide',
                                            'id' => 'item-del-'. $v->id  ])
                                        }}
                                        {{Form::hidden('id', $v->id  )}}
                                        {{ Form::close() }}

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