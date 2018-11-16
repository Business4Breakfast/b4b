@extends('layouts.app')

@section('title', 'Udalost')

@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">

            <div class="col-lg-12">
                <div class="ibox">
                    <div class="ibox-title">
                        <div class="row">
                            <div class="col-sm-12">
                                {{--<div id="chart"></div>--}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="ibox">
                    <div class="ibox-title">
                        <div class="row">
                            <form id="user_form" class="" method="GET" action="{{ route('events.stats.index') }}" >
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
                                    <a href="{{route('events.stats.index', ['reset' => true])}}" class="btn btn-danger"><i class="fa fa-close"></i> </a>
                                </div>
                            </form>
                        </div>
                    </div>

                    @include('components.validation')

                    <div class="ibox-content">
                        <div class="table-responsive">
                            @if(count($items) > 0)
                            <table class="table table-striped table-hover" id="table_stats">
                                <thead>
                                    <tr>
                                        <th width="20%">Klub</th>
                                        <th>Typ</th>
                                        <th>Dátum</th>
                                        <th>ALL</th>
                                        <th>MEM</th>
                                        <th>ATT</th>
                                        <th>APO</th>
                                        <th>NAP</th>
                                        <th>RAT</th>
                                        <th>GUE</th>
                                        <th>OTH</th>
                                        <th></th>
                                        <th width="20%"></th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>Počet udalostí: {{$items->count()}}</th>
                                        <th>Typ</th>
                                        <th>Dátum</th>
                                        <th>{{ $items->sum('count_attended') }}</th>
                                        <th>{{ round($items->sum('member_count') / $items->count(),1) }}</th>
                                        <th>{{ $items->sum('member_count_attend') }}</th>
                                        <th>{{ $items->sum('member_count_apologize') }}</th>
                                        <th>{{ $items->sum('member_count') - $items->sum('member_count_apologize')  - $items->sum('member_count_attend') }}</th>
                                        <th>
                                            @if($items->sum('member_count') > 0)
                                                {{  round(($items->sum('member_count_attend') * 100 ) / $items->sum('member_count'), 0) }}
                                            @else
                                                0
                                            @endif %
                                        </th>
                                        <th>
                                            {{ $items->sum('count_attended') - $items->sum('member_count_attend') - ($items->sum('member_all_attended') - $items->sum('member_count_attend')) }}
                                        </th>
                                        <th>
                                            {{ $items->sum('member_all_attended') - $items->sum('member_count_attend') }}
                                        </th>
                                        <th>
                                            {{ $items->sum('reference_coupons_price') + 0 }}
                                        </th>
                                        <th width="20%" align="left"></th>
                                    </tr>
                                </tfoot>
                                <tbody>
                                @foreach($items as $item => $v)
                                    <tr>
                                        <td>{{ $v->club_title }}, {{ $v->city }}</td>
                                        <td>
                                            <span data-original-title="{{ $v->event_title }}" data-toggle="tooltip" data-placement="top" >{{ str_limit($v->event_title,20) }}</span>
                                        </td>
                                        <td @if($v->event_from > Carbon\Carbon::today())
                                                class="text-success"
                                            @else
                                                class="text-danger"
                                            @endif >
                                            {{Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $v->event_from)->format('d.m.Y')}}
                                        </td>
                                        <td>
                                            <div class="badge badge-primary"
                                                 data-original-title="Celková nývštevnosť raňajok"
                                                 data-toggle="tooltip" data-placement="top" >{{$v->count_attended}}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="badge badge-success"
                                                 data-original-title="Počet členov klubu"
                                                 data-toggle="tooltip" data-placement="top" >{{$v->member_count}}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="badge badge-warning"
                                                   data-original-title="Účasť členov klubu"
                                                   data-toggle="tooltip" data-placement="top" >{{$v->member_count_attend}}
                                            </div>
                                        </td>
                                        <td>
                                            @if($v->member_count_apologize > 0)
                                                <div class="badge badge-warning"
                                                     data-original-title="Počet ospravedlnených členov"
                                                     data-toggle="tooltip" data-placement="top" >{{$v->member_count_apologize}}
                                                </div>
                                            @else
                                                <div class="badge badge-success"
                                                     data-original-title="Počet ospravedlnených členov"
                                                     data-toggle="tooltip" data-placement="top" >{{$v->member_count_apologize}}
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            @if($v->member_count - $v->member_count_apologize - $v->member_count_attend > 0)
                                                <div class="badge badge-danger"
                                                     data-original-title="Počet neospravedlnených členov"
                                                     data-toggle="tooltip" data-placement="top" >{{$v->member_count - $v->member_count_apologize - $v->member_count_attend}}
                                                </div>
                                            @else
                                                <div class="badge badge-success"
                                                     data-original-title="Počet neospravedlnených členov"
                                                     data-toggle="tooltip" data-placement="top" >{{$v->member_count - $v->member_count_apologize - $v->member_count_attend}}
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            @if($v->member_count > 0)
                                                <div class="badge badge-info"
                                                     data-original-title="Návštevnosť percentuálne"
                                                     data-toggle="tooltip" data-placement="top" >{{  round(($v->member_count_attend * 100 ) / $v->member_count, 0) }}%
                                                </div>
                                            @else
                                                <div class="badge badge-info"
                                                     data-original-title="Návštevnosť"
                                                     data-toggle="tooltip" data-placement="top" >0
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            {{--hostia vsetci zucastbeni okrem clenov klubu  a clenov vykonneho timu--}}
                                            @if($v->count_attended - $v->member_count_attend - ($v->member_all_attended - $v->member_count_attend) > 0)
                                                <div class="badge badge-success"
                                                     data-original-title="Počet hostí"
                                                     data-toggle="tooltip" data-placement="top" >{{ $v->count_attended - $v->member_count_attend - ($v->member_all_attended - $v->member_count_attend)}}
                                                </div>
                                            @else
                                                <div class="badge badge-danger"
                                                     data-original-title="Počet hostí"
                                                     data-toggle="tooltip" data-placement="top" >{{ $v->count_attended - $v->member_count_attend - ($v->member_all_attended - $v->member_count_attend)}}
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            {{--clenovia vykonneho timov inych klubov--}}
                                            @if($v->member_all_attended - $v->member_count_attend > 0)
                                                <div class="badge badge-primary"
                                                     data-original-title="Členovia iných klubov"
                                                     data-toggle="tooltip" data-placement="top" >{{ $v->member_all_attended - $v->member_count_attend }}
                                                </div>
                                            @else
                                                <div class="badge badge-success"
                                                     data-original-title="Členovia iných klubov"
                                                     data-toggle="tooltip" data-placement="top" >{{ $v->member_all_attended - $v->member_count_attend }}
                                                </div>
                                            @endif

                                        </td>
                                        <td>
                                            {{ $v->reference_coupons_price }}
                                        </td>
                                        <td>
                                            @role(['superadministrator', 'administrator'])


                                            @endrole

                                            <a href="{{ route('events.listing.show', ['club' => $v->id ]) }}" type="button" class="pull-right btn btn-warning btn-xs m-l-sm "
                                               data-original-title="Detail" data-toggle="tooltip" data-placement="top" ><i class="fa fa-search"></i></a>

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

    <link rel="stylesheet" href="{!! asset('css/plugins/c3/c3.min.css') !!}" />

@endsection


@section('scripts')

    <script src="{!! asset('js/plugins/c3/c3.min.js') !!}" type="text/javascript"></script>
    <script src="{!! asset('js/plugins/d3/d3.min.js') !!}" type="text/javascript"></script>

    <script>

        jQuery(window).ready(function (){

            $.ajax({
                type:'POST',
                url:'/ajax/function',
                data: {
                    action: 'get_event_stat_data',
                    date_from: "{!! $req['date_from'] !!}",
                    date_to: "{!! $req['date_to'] !!}",
                    event_type: "{!! $req['search_type'] !!}",
                    club_id: "{!! $req['search_club'] !!}"
                },
                dataType: 'json',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                cache: false,
                async: true,
                success:function(data){

                    console.log(data);

                    generateChart(data);
                },
                error: function (xhr, status, error) {
                    alert(error);
                }

            });


            function generateChart(response) {

                var data = [];

                data['price'] = ['price'];
                data['reference'] = ['reference'];
                data['face_to_face'] = ['face2face'];
                data['records'] = ['records'];
                data['evidence']  = ['evidence'];
                data['thank_you'] = ['thank_you'];
                data['x'] = ['x'];

                response.forEach(function (v,k) {

                    data['price'].push(v.price);
                    data['reference'].push(v.reference);
                    data['face_to_face'].push(v.face_to_face);
                    data['records'].push(v.records);
                    data['evidence'].push(v.evidence);
                    data['thank_you'].push(v.thank_you);
                    data['x'].push(v.year + "-" + v.month);
                });

                var chart = c3.generate({
                    bindto: '#chart',
                    data: {
                        x: 'x',
                        xFormat: '%Y-%m',
                        // pri nacitani hidneme price
                        hide: ['price'],

                        columns: [
                            data['x'],
                            data['price'],
                            data['reference'],
                            data['face_to_face'],
                            data['records'],
                            data['evidence'],
                            data['thank_you']

                            ]
                    },
                    axis: {
                        x: {
                            type: 'timeseries',
                            tick: {
                                format: '%Y-%m'
                            }
                        }
                    },
                    // keys: {
                    //     value: ['price', 'download']
                    // }
                });
            }

        });

    </script>


@endsection