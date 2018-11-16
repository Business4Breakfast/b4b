@extends('layouts.app')

@section('title', 'Zoznam klubov')

@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <div class="row">
                            <form id="user_form" class="" method="GET" action="{{ route('setting.club.index') }}" >
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label class="control-label" for="status">Stav</label>
                                        <select name="search_status" id="search_status" class="form-control">
                                            <option value="2"  selected="selected" >Všetky</option>
                                            <option value="1" @if($req['search_status']  == 1)  selected="selected" @endif  >Aktívne</option>
                                            <option value="0" @if($req['search_status']  == 0)  selected="selected" @endif >Neaktívne</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label class="control-label" for="price">Názov klubu</label>
                                        <input type="text" id="search_club" name="search_club" value="{{$req['search_club']}}" placeholder="" class="form-control">
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label class="control-label" for="quantity">Adresa, miesto</label>
                                        <input type="text" id="search_address" name="search_address" value="{{$req['search_address']}}" placeholder="" class="form-control">
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <label class="control-label block" for="quantity">&nbsp;</label>
                                    <input type="submit" class="btn btn-primary" value="Filtrovať">
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Klub</th>
                                <th>Act</th>
                                <th>Čas</th>
                                <th>Miesto</th>
                                <th>Kont. osoba</th>
                                <th width="300px"></th>
                            </tr>
                            </thead>
                            <tbody>
                            @if($items)
                                @foreach($items as $item => $v)
                                <tr>
                                    <td><img src="{{ $v->imageThumb }}" width="35px"></td>
                                    <td>{{ str_limit($v->short_title, 30) }}, {{ $v->address_city }}</td>
                                    <td>
                                        @if($v->active == 1)
                                            <div class="badge badge-success"><i class="fa fa-check"></i></div>
                                        @else
                                            <div class="badge badge-danger"><i class="fa fa-close"></i></div>
                                        @endif
                                    </td>
                                    <td>{{Carbon\Carbon::createFromFormat('H:i:s', $v->time_from)->format('H:i')}} - {{Carbon\Carbon::createFromFormat('H:i:s', $v->time_to)->format('H:i')}}</td>
                                    <td>{{ $v->host_name }}</td>
                                    <td>{{ $v->contact_person }}</td>
                                    <td>
                                        @role(['superadministrator', 'administrator'])
                                        <a type="button" data-item-id="{{ $v->id }}"
                                           class="m-l-sm pull-right btn btn-danger btn-xs delete-alert"><i class="fa fa-trash-o"></i> </a>

                                        {{ Form::open(['method' => 'DELETE', 'route' => ['setting.club.destroy', $v->id ],
                                            'class' => 'class="m-l-sm pull-right btn btn-danger btn-xs delete-alert hide',
                                            'id' => 'item-del-'. $v->id  ])
                                        }}
                                        {{Form::hidden('user_id', $v->id  )}}
                                        {{ Form::close() }}
                                        @endrole
                                        <a href="{{ route('setting.club.edit', ['user' => $v->id ]) }}" type="button" class="pull-right btn btn-success btn-xs m-l-sm"><i class="fa fa-edit"></i> </a>
                                        <a href="{{ route('setting.club-member.show', ['club' => $v->id ]) }}" type="button" class="pull-right btn btn-primary btn-xs m-l-sm">Členovia</a>
                                        <a href="{{ route('setting.club-breakfast.show', ['club' => $v->id ]) }}" type="button" class="pull-right btn btn-primary btn-xs m-l-sm">Udalosti</a>
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