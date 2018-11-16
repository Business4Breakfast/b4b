@extends('layouts.app')

@section('title', 'Sablona emailu')

@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">


        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">

                    @include('events.components.header_event')

                    @include('components.validation')

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="ibox float-e-margins collapse" id="form_add_setting">
                                <div class="ibox-content">

                                    @include('components.validation')

                                    <form id="user_form" class="form-horizontal" method="POST" action="{{ route('events.ticket-setting.store') }}" enctype="multipart/form-data" >
                                        {{ csrf_field() }}
                                        <input type="hidden" name="event_id" value="{{$event->id}}">

                                        <div class="form-group{{ $errors->has('member') ? ' has-error' : '' }}">
                                            <label for="member" class="col-md-4 control-label">Člen</label>
                                            <div class="col-md-4">
                                                <div class="checkbox">
                                                    <input id="member" type="checkbox" class="form-control" name="member" value="1">
                                                    <label for="member">
                                                        Lístok si môže zakupiť len člen
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group{{ $errors->has('not_member') ? ' has-error' : '' }}">
                                            <label for="not_member" class="col-md-4 control-label">Nečlen</label>
                                            <div class="col-md-4">
                                                <div class="checkbox">
                                                    <input id="not_member" type="checkbox" class="form-control" name="not_member" value="1">
                                                    <label for="not_member">
                                                        Lístok si môže zakupiť aj  nečlen alebo hosť
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group{{ $errors->has('price') ? ' has-error' : '' }}">
                                            <label for="price" class="col-md-4 control-label">Cena bez DPH</label>
                                            <div class="col-md-2">
                                                <input id="price" type="number" class="form-control" name="price" value="{{ $event->price }}"  required>
                                                @if ($errors->has('price'))
                                                    <span class="help-block">
                                        <strong>{{ $errors->first('price') }}</strong></span>
                                                @endif
                                            </div>
                                        </div>
                                        <div id="dhp_element" class="">
                                            <div class="form-group{{ $errors->has('price_dph') ? ' has-error' : '' }}">
                                                <label for="price_dph" class="col-md-4 control-label">DPH</label>
                                                <div class="col-md-2">
                                                    <input id="price_dph" type="text" class="form-control" name="price_dph" value="{{ $event->price * 0.2 }}"  required >
                                                    @if ($errors->has('price_dph'))
                                                        <span class="help-block">
                                                            <strong>{{ $errors->first('price_dph') }}</strong></span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="form-group{{ $errors->has('price_w_dph') ? ' has-error' : '' }}">
                                                <label for="price_w_dph" class="col-md-4 control-label">Cena s DPH</label>
                                                <div class="col-md-2">
                                                    <input id="price_w_dph" type="text" class="form-control" name="price_w_dph" value="{{ $event->price  + ($event->price * config('invoice.setting.vat')/100)  }}"  required >
                                                    @if ($errors->has('price_w_dph'))
                                                        <span class="help-block">
                                                            <strong>{{ $errors->first('price_w_dph') }}</strong></span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group{{ $errors->has('price_from_pcs') ? ' has-error' : '' }}">
                                            <label for="price_from_pcs" class="col-md-4 control-label">Cena od počtu kusov</label>
                                            <div class="col-md-1">
                                                <input id="price_from_pcs" type="number" class="form-control" name="price_from_pcs" value="0" required >
                                                @if ($errors->has('price_from_pcs'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('price_from_pcs') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="form-group{{ $errors->has('date_pay_to') ? ' has-error' : '' }} date_picker_year" >
                                            <label for="date_pay_to" class="col-md-4 control-label">Dátum do kedy platí cena</label>
                                            <div class="col-md-2">
                                                <div class="input-group date">
                                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                    <input type="text" class="form-control date_mask" value="{{ Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $event->event_from)->format('d.m.Y') }}" name="date_pay_to" >
                                                </div>
                                                @if ($errors->has('date_pay_to'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('date_pay_to') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                                            <label for="description" class="col-md-4 control-label">Popis</label>
                                            <div class="col-md-4">
                                                <textarea id="description" class="form-control" name="description" rows="4">{{ old('description') }}</textarea>
                                                @if ($errors->has('description'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('description') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="form-group">
                                            <div class="col-md-6 col-md-offset-4">
                                                <button type="submit" class="btn btn-primary">
                                                    {{__('form.add_record')}}
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <div class="ibox-title" style="min-height: 60px;">

                                <span class="pull-right">
                                    <a class="btn btn-success btn-sm " data-toggle="collapse" href="#form_add_setting"
                                       aria-expanded="false" aria-controls="collapseExample">Pridať nové nastavenie</a>
                                </span>
                            </div>
                            <div class="ibox-content">
                                <table class="table table-hover" id="item_event_type">
                                    <thead>
                                    <tr>
                                        <th>Člen</th>
                                        <th>Nečlen</th>
                                        <th>Cena</th>
                                        <th>S DPH</th>

                                        <th>Dátum</th>
                                        <th width="40%">Popis</th>
                                        <th width=""></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if($items)
                                        @foreach($items as $item => $v)
                                            <tr>
                                                <td>
                                                    @if($v->member  == 1 )
                                                        <i class="fa fa-user-secret"></i>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($v->not_member  == 1 )
                                                        <i class="fa fa-users"></i>
                                                    @endif
                                                </td>
                                                <td>{{number_format((float)  $v->price, 2, ',', ' ')}}</td>
                                                <td>{{number_format((float)  $v->price_w_dph, 2, ',', ' ')}}</td>

                                                <td>{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $v->date_before_event)->format('d.m.Y') }}</td>
                                                <td width="40%">{{ str_limit($v->description, 120) }}</td>
                                                <td>
                                                    <a href="{{ route('events.ticket-setting.edit', ['ticket' => $v->id ]) }}" type="button" class="pull-right btn btn-info btn-xs m-l-sm"><i class="fa fa-edit"></i><span class="hidden-xs"> {{__('form.edit_record')}}</span></a>

                                                    <a type="button" data-item-id="{{ $v->id }}"
                                                       class="m-l-sm pull-right btn btn-danger btn-xs delete-alert"><i class="fa fa-trash"></i><span class="hidden-xs hidden-sm"> {{__('form.delete')}}</span></a>

                                                    {{ Form::open(['method' => 'DELETE', 'route' => ['events.ticket-setting.destroy', $v->id ],
                                                        'class' => 'class="m-l-sm pull-right btn btn-danger btn-xs delete-alert hide',
                                                        'id' => 'item-del-'. $v->id  ])}}
                                                    {{Form::hidden('setting_id', $v->id  )}}
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
    </div>

@endsection

@section('css')


@endsection


@section('scripts_before')

@endsection



@section('scripts')


    <script>

        $(document).ready(function(){

            $("#price").on('blur keyup change click input', function () {

                var vat = {!! config('invoice.setting.vat') !!};
                var price = $('#price').val();
                var vat_value = ((price*vat)/100).toFixed(2);
                var total = parseFloat(vat_value) + parseFloat(price);

                $('#price_dph').val(vat_value);
                $('#price_w_dph').val(parseFloat(total).toFixed(2));

            });

            $('#item_event_type').on("click", ".delete-alert", function(){
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