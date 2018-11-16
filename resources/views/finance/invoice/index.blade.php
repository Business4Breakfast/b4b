@extends('layouts.app')

@section('title', 'Zoznam faktúr')

@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <div class="row">
                            <form id="user_form" class="" method="GET" action="{{ route('finance.invoice.index') }}" >
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label class="control-label" for="search_category">Kategória</label>
                                        <select name="search_category" id="search_status" class="form-control">
                                            <option value="2"  selected="selected" >Všetky</option>
                                            <option value="1"  @if($req['search_category']  == 1)  selected="selected" @endif >Riadne faktúry</option>
                                            <option value="9"  @if($req['search_category']  == 9)  selected="selected" @endif >Proforma faktúry</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label class="control-label" for="search_type">Typ</label>
                                        <select id="search_type" class="form-control" name="search_type">
                                            <option value="0">Všetky</option>
                                            @foreach($type as $t)
                                                <option value="{{$t['id']}}"  @if($req['search_type']  == $t['id'])  selected="selected" @endif >{{str_limit($t['name'], 30)}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label class="control-label" for="search_status">Stav</label>
                                        <select name="search_status" id="search_status" class="form-control">
                                            <option value="0"  selected="selected" >Všetky</option>
                                            <option value="1"  @if($req['search_status']  == 1)  selected="selected" @endif >Nezaplatené</option>
                                            <option value="2"  @if($req['search_status']  == 2)  selected="selected" @endif >Zaplatené</option>
                                            <option value="3"  @if($req['search_status']  == 3)  selected="selected" @endif>Čiastočne zaplatene</option>
                                            <option value="4"  @if($req['search_status']  == 4)  selected="selected" @endif >Po splatnosti do 30 dní</option>
                                            <option value="5"  @if($req['search_status']  == 5)  selected="selected" @endif >Po splatnosti viac ako 30 dní</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label class="control-label" for="search_company">Odberateľ</label>
                                        <input type="text" id="search_company" name="search_company" value="{{$req['search_company']}}" placeholder="" class="form-control">
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label class="control-label" for="search_price">Číslo/Suma</label>
                                        <input type="text" id="search_price" name="search_price" value="{{$req['search_price']}}" placeholder="" class="form-control">
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <label class="control-label block" for="submit">&nbsp;</label>
                                    <input type="submit" class="btn btn-primary" value="Filtrovať">
                                    <a href="{{route('finance.invoice.index', ['reset' => true])}}" class="btn btn-danger"><i class="fa fa-close"></i> </a>

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
                                <th>Firma</th>
                                <th class="text-right">Cena</th>
                                <th class="text-right">Cena s DPH</th>
                                <th>Dodanie</th>
                                <th>Splatnosť</th>
                                <th>Stav</th>
                                <th width="200px"></th>
                            </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <td></td>
                                    <td>Suma celkom</td>
                                    <td class="text-right">{{number_format((float) $sum['price'], 2, ',', ' ')}}</td>
                                    <td class="text-right">{{number_format((float)$sum['price_w_dph'], 2, ',', ' ')}}</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                            <tbody>
                            @if($items)
                                @foreach($items as $item => $v)
                                <tr>
                                    <td><b>{{ $v->variable_symbol }}</b></td>
                                    <td>{{ $v->company_title }}</td>
                                    <td align="right"><strong>{{ number_format((float)$v->price, 2, ',', ' ')}}</strong></td>
                                    <td align="right"><strong>{{ number_format((float)$v->price_w_dph, 2, ',', ' ') }}  </strong></td>

                                    <td>{{ Carbon\Carbon::createFromFormat('Y-m-d H:i:s',$v->date_delivery)->format('d.m.Y') }}</td>

                                    <td>{{ Carbon\Carbon::createFromFormat('Y-m-d H:i:s',$v->date_pay_to)->format('d.m.Y') }}</td>
                                    @if($v->status  == config('invoice.status.paid'))
                                        <td class="text-success"> <i class="fa fa-check-circle"></i><strong> Uhradené</strong></td>
                                    @elseif($v->status  == config('invoice.status.partial-paid'))
                                        <td class="text-warning"> <i class="fa fa-question-circle"></i> <strong> Čiastočne</strong></td>
                                    @elseif($v->status  == config('invoice.status.paid-more'))
                                        <td class="text-info"> <i class="fa fa-question-circle"></i> <strong> Viac</strong></td>
                                    @else
                                        @if(Carbon\Carbon::now()->diffInDays( Carbon\Carbon::createFromFormat('Y-m-d H:i:s' , $v->date_pay_to) ,  false) > 0 )
                                            <td class="text-warning"> <i class="fa fa-times-circle"></i> <strong> Splatnosť ( {{ Carbon\Carbon::now()->diffInDays( Carbon\Carbon::createFromFormat('Y-m-d H:i:s' , $v->date_pay_to) ,  false) }} )</strong></td>
                                        @else
                                            <td class="text-danger"> <i class="fa fa-times-circle"></i> <strong> Neuhradené ( {{ Carbon\Carbon::now()->diffInDays( Carbon\Carbon::createFromFormat('Y-m-d H:i:s' , $v->date_pay_to) ,  false) }} )</strong></td>
                                        @endif
                                    @endif
                                    <td>
                                        <a type="button" data-item-id="{{ $v->id }}"
                                           class="m-l-sm pull-right btn btn-danger btn-xs delete-alert"><i class="fa fa-trash"></i></a>

                                        {{ Form::open(['method' => 'DELETE', 'route' => ['finance.invoice.destroy', $v->id ],
                                            'class' => 'class="m-l-sm pull-right btn btn-danger btn-xs delete-alert hide',
                                            'id' => 'item-del-'. $v->id  ])
                                        }}
                                        {{Form::hidden('user_id', $v->id  )}}
                                        {{ Form::close() }}

                                        <a href="{{ route('finance.invoice.print', ['user' => $v->id ]) }}"  target="_blank" type="button" class="pull-right btn btn-warning btn-xs m-l-sm"> <i class="fa fa-print"></i> </a>
                                        <a href="{{ route('finance.invoice-payment.edit', ['user' => $v->id ]) }}" type="button" class="pull-right btn btn-info btn-xs m-l-sm"> <i class="fa fa-money"></i> </a>
                                        <a href="{{ route('finance.invoice.edit', ['user' => $v->id ]) }}" type="button" class="pull-right btn btn-success btn-xs m-l-sm"><i class="fa fa-edit"></i> </a>
                                    </td>
                                </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                        {{--{{ $items->links() }}--}}
                        {{ $items->appends(request()->query())->links() }}
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