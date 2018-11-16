@extends('layouts.app')

@section('title', 'číselníky')

@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <div class="row">
                            <form id="user_form" class="" method="GET" action="{{ route('setting.event-type-text.index') }}" >
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label class="control-label" for="search_category">Kategória</label>
                                        <select name="search_type" id="search_type" class="form-control">
                                            <option value=""  selected="selected" >Všetky</option>
                                            @foreach($event_types as $et)
                                                <option value="{{$et->id}}"  @if(\Illuminate\Support\Facades\Input::get('search_type') == $et->id)  selected="selected" @endif >{{$et->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <label class="control-label block" for="submit">&nbsp;</label>
                                    <input type="submit" class="btn btn-primary" value="Filtrovať">
                                    <a href="{{route('setting.event-type-text.index', ['reset' => true])}}" class="btn btn-danger"><i class="fa fa-close"></i> </a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="ibox-content">
                        <table class="table table-hover" id="item_table">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Členstvo</th>
                                <th>Názov</th>
                                <th>Popis</th>
                                <th width="250"></th>
                            </tr>
                            </thead>
                            <tbody>
                            @if($items)
                                @foreach($items as $item => $v)
                                <tr>
                                    <td>TXT_{{$v->id}}</td>
                                    <td>{{  implode(",",$v->membership)}}</td>
                                    <td>{{  implode(",",$v->type)}}</td>
                                    <td>{{ $v->description }}</td>
                                    <td>
                                        @permission('acl-item-list-delete')
                                        <a type="button" data-item-id="{{ $v->id }}"
                                           class="m-l-sm pull-right btn btn-danger btn-xs delete-alert"><i class="fa fa-trash"></i><span class="hidden-xs hidden-sm"> </span></a>

                                        {{ Form::open(['method' => 'DELETE', 'route' => ['setting.'.$module.'.destroy', $v->id ],
                                            'class' => 'class="m-l-sm pull-right btn btn-danger btn-xs delete-alert hide',
                                            'id' => 'item-del-'. $v->id  ])}}
                                        {{Form::hidden('user_id', $v->id  )}}
                                        {{ Form::close() }}
                                        @endpermission
                                        <a href="{{ route('setting.'.$module.'.edit', ['user' => $v->id ]) }}" type="button" class="pull-right btn btn-info btn-xs m-l-sm"><i class="fa fa-edit"></i><span class="hidden-xs"> {{__('form.edit_record')}}</span></a>

                                        <a type="button" onclick="event.preventDefault(); document.getElementById('active-form-{{$v->id}}').submit();"
                                           data-toggle="tooltip" data-placement="top"
                                            @if($v->active == 1)
                                                class="m-l-sm pull-right btn btn-success  btn-xs" title="Deaktivovať" ><i class="fa fa-check"></i>
                                            @else
                                                class="m-l-sm pull-right btn btn-danger  btn-xs" title="Aktivovať"><i class="fa fa-close"></i>
                                            @endif
                                        </a>

                                        {{ Form::open(['method' => 'PUT', 'route' => ['setting.'. $module .'.active', $v->id ],
                                            'class' => 'class="m-l-sm pull-right btn btn-danger btn-xs hide',
                                            'id' => 'active-form-'. $v->id  ])
                                        }}
                                        {{Form::hidden('user_id', $v->id  )}}

                                        @if($v->active == 1)
                                            {{Form::hidden('active', 0 )}}
                                        @else
                                            {{Form::hidden('active', 1  )}}
                                        @endif
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

@endsection

@section('css')

@endsection

@section('scripts')


    <script type="text/javascript" src="https://cdn.datatables.net/v/bs/dt-1.10.16/datatables.min.js"></script>

    <script>

        $(document).ready(function(){

            // sorting s diakritikou
            $('#item_table').DataTable({
                "pageLength": 50,
                "dom": 'lrfrtip',
                columnDefs : [
                    { targets: 0, type: 'locale-compare' }
                ],
                "order": [0, 'asc']
            });


            jQuery.extend( jQuery.fn.dataTableExt.oSort, {
                "locale-compare-asc": function ( a, b ) {
                    return a.localeCompare(b, 'da', { sensitivity: 'accent' })
                },
                "locale-compare-desc": function ( a, b ) {
                    return b.localeCompare(a, 'da', { sensitivity: 'accent' })
                }
            });


            $('#item_table').on("click", ".delete-alert", function(){
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