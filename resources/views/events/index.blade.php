@extends('layouts.app')

@section('title', 'Udalost')

@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox">
                    <div class="ibox-title">
                        <div class="row">
                            <form id="user_form" class="" method="GET" action="{{ route('events.listing.index') }}" >
                                <div class="col-sm-1 col-md-1" style="padding-right: 3px;">
                                    <div class="form-group">
                                        <label class="control-label" for="search_status">Stav</label>
                                        <select name="search_status" id="search_status" class="form-control">
                                            <option value="9"  @if($req['search_status']  == "")  selected="selected" @endif >Všetky</option>
                                            <option value="1"  @if($req['search_status']  == 1)  selected="selected" @endif >Aktívne</option>
                                            <option value="0"  @if($req['search_status']  == 0)  selected="selected" @endif >Neaktívne</option>
                                            <option value="2"  @if($req['search_status']  == 2)  selected="selected" @endif >Uzavreté</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label class="control-label" for="search_company">Types</label>
                                        <select name="search_type" id="search_type" class="form-control"  style="width:100%;"  tabindex="2">
                                            <option value="">-- výber --</option>
                                            @foreach($event_types as $typ)
                                                <option value="{{$typ->id}}" @if($req['search_type']  == $typ->id)  selected="selected" @endif >{{$typ->name}}</option>
                                            @endforeach
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
                                        <select name="search_club" id="search_club" class="form-control" data-placeholder="Klub..."  style="width:100%;"  tabindex="2" >

                                            <option value="">-- výber --</option>
                                            @foreach($clubs as $key =>$club)
                                                <option value="{{$club->id}}" @if($req['search_club']  == $club->id)  selected="selected" @endif >{{$club->title}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <label class="control-label block" for="submit">&nbsp;</label>
                                    <input type="submit" class="btn btn-primary" value="Filtrovať">
                                    <a href="{{route('events.listing.index', ['reset' => true])}}" class="btn btn-danger"><i class="fa fa-close"></i> </a>
                                </div>
                            </form>
                        </div>
                    </div>

                    @include('components.validation')

                    <div class="ibox-content">
                        <div class="table-responsive">
                            @if(count($items) > 0)
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Typ</th>
                                        <th>Dátum</th>
                                        <th>Od-Do</th>
                                        <th>Klub</th>
                                        <th>Názov (hostia)</th>
                                        <th width="20%"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($items as $item => $v)
                                    <tr>
                                        {{--<td>{{$loop->iteration}}</td>--}}
                                        <td>
                                            @if($v->active == 1)
                                                <i class="fa fa-check text-success"></i>
                                            @elseif($v->active == 2)
                                                <i class="fa fa-dropbox text-warning"></i>
                                            @else
                                                <i class="fa fa-close text-danger"></i>
                                            @endif
                                            <span data-original-title="{{ $v->event_title }}" data-toggle="tooltip" data-placement="top" >{{ str_limit($v->event_title,20) }}</span>
                                        </td>
                                        <td @if($v->event_from > Carbon\Carbon::today())
                                                class="text-success"
                                            @else
                                                class="text-danger"
                                            @endif >
                                            {{Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $v->event_from)->format('d.m.Y')}}
                                        </td>
                                        <td>{{Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $v->event_from)->format('H:i')}}
                                            - {{Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $v->event_to)->format('H:i')}}
                                        </td>

                                        <td>{{ $v->club_title }}, {{ $v->city }}</td>
                                        <td>
                                            @if($v->attend_count > 0)
                                                @if($v->active == 2)
                                                    <div class="badge badge-danger"
                                                         data-original-title="Počet pozvánok" data-toggle="tooltip" data-placement="top" >{{$v->attend_count}}</div>

                                                    @if($v->attended_count)
                                                        <div class="badge badge-warning-light"
                                                             data-original-title="Účasť" data-toggle="tooltip" data-placement="top" >{{$v->attended_count}}</div>
                                                    @endif
                                                @else
                                                    <div class="badge badge-success">{{$v->attend_count}}</div>
                                                @endif
                                            @else
                                                @if($v->active == 2)
                                                    <div class="badge badge-danger"
                                                         data-original-title="Počet pozvánok" data-toggle="tooltip" data-placement="top" >{{$v->attend_count}}</div>
                                                @else
                                                    <div class="badge badge-warning">{{$v->attend_count}}</div>
                                                @endif
                                            @endif
                                            @if($v->attend_count_confirm)
                                                <div class="badge badge-info"
                                                     data-original-title="Počet potvrdených hostí" data-toggle="tooltip" data-placement="top" >{{$v->attend_count_confirm}}</div>
                                            @endif

                                            @if($v->reference_coupons_count)
                                                <div class="badge badge-primary"
                                                     data-original-title="Počet ústrižkov" data-toggle="tooltip" data-placement="top" >{{$v->reference_coupons_count}}</div>
                                            @endif
                                        </td>
                                        <td>
                                            @role(['superadministrator', 'administrator'])

                                                <a type="button" data-item-id="{{ $v->id }}"
                                                   class="m-l-sm pull-right btn btn-danger btn-xs delete-alert"><i class="fa fa-trash-o"></i> </a>

                                                {{ Form::open(['method' => 'DELETE', 'route' => ['events.listing.destroy', $v->id ],
                                                    'class' => 'class="m-l-sm pull-right btn btn-danger btn-xs delete-alert hide',
                                                    'id' => 'item-del-'. $v->id  ])
                                                }}

                                                {{Form::hidden('user_id', $v->id  )}}
                                                {{ Form::close() }}

                                            @endrole
                                            {{--ak je uzavreta neda sa editovat okrem admina a superadmina--}}
                                            @if($v->active == 2 && !Auth::user()->hasRole(['superadministrator', 'administrator']))
                                                <a type="button" class="pull-right btn btn-success btn-xs m-l-sm disabled"><i class="fa fa-edit"></i> </a>
                                            @else
                                                <a href="{{ route('events.listing.edit', ['user' => $v->id ]) }}" type="button" class="pull-right btn btn-success btn-xs m-l-sm"><i class="fa fa-edit"></i> </a>
                                            @endif
                                            <a href="{{ route('events.listing.show', ['club' => $v->id ]) }}" type="button" class="pull-right btn btn-warning btn-xs m-l-sm "
                                               data-original-title="Detail" data-toggle="tooltip" data-placement="top" ><i class="fa fa-search"></i> Detail</a>

                                            {{--<a href="{{ route('setting.club-breakfast-ticket', ['club' => $v->id ]) }}" type="button" class="pull-right btn btn-primary btn-xs m-l-sm "--}}
                                               {{--data-original-title="Kontrolné ústrižky" data-toggle="tooltip" data-placement="top" ><i class="fa fa-address-card"></i> </a>--}}

                                            {{--<a href="{{ route('setting.club-breakfast-attendance', ['club' => $v->id ]) }}" type="button" class="pull-right btn btn-primary btn-xs m-l-sm"--}}
                                               {{--data-original-title="Prezenčná listina" data-toggle="tooltip" data-placement="top" ><i class="fa fa-users"></i>--}}
                                            {{--</a>--}}
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            {{ $items->appends(request()->query())->links() }}
                            @else
                                <p class="text-danger">Neexistujú žiadne záznamy</p>
                            @endif
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection

@section('page_css')

@endsection


@section('scripts')


    <script>
        jQuery(window).ready(function (){




        });

    </script>


@endsection