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
                            <div class="ibox float-e-margins">
                                <div class="ibox-content">

                                    @include('components.validation')

                                    <form id="user_form" class="form-horizontal" method="POST" action="{{ route('events.ticket-setting.update', ['id' => $ticket_setup->id] ) }}" enctype="multipart/form-data" >
                                        {{ csrf_field() }}
                                        <input name="_method" type="hidden" value="PUT">
                                        <input type="hidden" name="event_id" value="{{$event->id}}">

                                        <div class="form-group{{ $errors->has('member') ? ' has-error' : '' }}">
                                            <div class="col-md-offset-4 col-lg-offset-4 col-md-8">
                                                <a class="btn-sm btn btn-warning" href="{{route('events.ticket-setting.show', ['id' => $event->id ])}}">Spať na zoznam nastavení</a>
                                            </div>
                                        </div>
                                        <div class="form-group{{ $errors->has('member') ? ' has-error' : '' }}">
                                            <label for="member" class="col-md-4 control-label">Člen</label>
                                            <div class="col-md-4">
                                                <div class="checkbox">
                                                    <input id="member" type="checkbox" class="form-control" name="member" value="1"
                                                    @if($ticket_setup->member == 1) checked="checked" @endif>
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
                                                    <input id="not_member" type="checkbox" class="form-control" name="not_member" value="1"
                                                           @if($ticket_setup->not_member == 1) checked="checked" @endif>
                                                    <label for="not_member">
                                                        Lístok si môže zakupiť aj  nečlen alebo hosť
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group{{ $errors->has('price') ? ' has-error' : '' }}">
                                            <label for="price" class="col-md-4 control-label">Cena bez DPH</label>
                                            <div class="col-md-2">
                                                <input id="price" type="number" class="form-control" name="price" value="{{ $ticket_setup->price }}"  required>
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
                                                    <input id="price_dph" type="text" class="form-control" name="price_dph" value="{{ $ticket_setup->price_dph }}"  required >
                                                    @if ($errors->has('price_dph'))
                                                        <span class="help-block">
                                                            <strong>{{ $errors->first('price_dph') }}</strong></span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="form-group{{ $errors->has('price_w_dph') ? ' has-error' : '' }}">
                                                <label for="price_w_dph" class="col-md-4 control-label">Cena s DPH</label>
                                                <div class="col-md-2">
                                                    <input id="price_w_dph" type="text" class="form-control" name="price_w_dph" value="{{ $ticket_setup->price_w_dph  }}"  required >
                                                    @if ($errors->has('price_w_dph'))
                                                        <span class="help-block">
                                                            <strong>{{ $errors->first('price_w_dph') }}</strong></span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group{{ $errors->has('date_pay_to') ? ' has-error' : '' }} date_picker_year" >
                                            <label for="date_pay_to" class="col-md-4 control-label">Dátum do kedy platí cena</label>
                                            <div class="col-md-2">
                                                <div class="input-group date">
                                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                    <input type="text" class="form-control date_mask" value="{{ Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $ticket_setup->date_before_event)->format('d.m.Y') }}" name="date_pay_to" >
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
                                                <textarea id="description" class="form-control" name="description" rows="4">{{ $ticket_setup->description }}</textarea>
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


        });

    </script>

@endsection