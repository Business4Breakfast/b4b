@extends('layouts.app')

@section('title', 'P' )

@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                    </div>
                    <div class="ibox-content">

                        @include('components.validation')

                        {!! Form::open(['route' => ['finance.invoice-payment.update', $invoice->id], 'method' => 'PATCH' ,'class' => 'form-horizontal', 'id' => 'invoice_payment_form']) !!}
                        {!! Form::hidden('invoice_id', $invoice->id ) !!}

                        <div class="form-group" >
                            <label for="valid_to" class="col-md-4 control-label">Číslo faktúry</label>
                            <div class="col-md-3">
                                <div class="input-group date">
                                    <h3 class="form-control-static">{{ $invoice->variable_symbol }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="form-group" >
                            <label for="valid_to" class="col-md-4 control-label">Odberateľ</label>
                            <div class="col-md-3">
                                <div class="input-group date">
                                    <p class="form-control-static">{{ $invoice->company_title }}, {{ $invoice->address_street }}, {{ $invoice->address_psc }} {{ $invoice->address_city }}</p>
                                </div>
                            </div>
                        </div>
                        @if($inv_payment->count() > 0 )
                        <div class="form-group" >
                            <label for="valid_to" class="col-md-4 control-label">Úhrady faktúry</label>
                            <div class="col-md-4">
                                <table class="table" style="font-size: small">
                                    <tbody>
                                    @foreach($inv_payment as $ip)
                                        <tr>
                                            <td> {{ Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $ip->date_payment)->format('d.m.Y') }}</td>
                                            <td><strong>{{$ip->price_payment}}</strong></td>
                                            <td>{{$ip->user_name}} {{$ip->user_surname}}</td>
                                            <td>{{$ip->description_payment}}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @endif
                            <div class="form-group" >
                                <label for="valid_to" class="col-md-4 control-label">Suma</label>
                                <div class="col-md-3">
                                    <div class="input-group date">
                                        <p class="form-control-static">{{ $invoice->price }}, [{{ $invoice->price_dph }}], {{ $invoice->price_w_dph }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('date_payment') ? ' has-error' : '' }} date_picker_year" >
                                <label for="date_payment" class="col-md-4 control-label">Dátum platby</label>
                                <div class="col-md-3">
                                    <div class="input-group date">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input type="text" class="form-control date_mask" value="{{ Carbon\Carbon::now()->format('d.m.Y') }}" name="date_payment" >
                                    </div>
                                    @if ($errors->has('date_payment'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('date_payment') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('price_payment') ? ' has-error' : '' }}">
                                <label for="price_payment" class="col-md-4 control-label">Platba €</label>
                                <div class="col-md-2">
                                    <input id="price_payment" type="text" class="form-control" name="price_payment" value="{{ number_format((float)$due_payment, 2, '.', ' ') }}"  required>
                                    @if ($errors->has('price_payment'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('price_payment') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <hr>
                            <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                                <label for="description" class="col-md-4 control-label">Poznámka</label>
                                <div class="col-md-5">
                                    <textarea id="description" class="form-control" name="description" rows="3">{{ old('description') }}</textarea>
                                    @if ($errors->has('description'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('description') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-6 col-md-offset-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{__('form.edit_record')}}
                                    </button>
                                </div>
                            </div>
                        {{Form::close()}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')

    <script>
        $(document).ready(function (){

            $('.date_mask').mask('AB.CB.YYYY', {'translation': {
                A: {pattern: /[0-3]/},
                B: {pattern: /[0-9]/},
                C: {pattern: /[0-1]/},
                Y: {pattern: /[0-9]/}
                }
            });

            $("#invoice_payment_form").validate({
                rules: {
                    phone: {
                        required: true,
                        minlength: 13
                    },
                    email: {
                        email: true,
                        required: true
                    }
                }
            });

            $('#send_email').change(function() {
                if ($('#div_send_email').hasClass('hide')) {
                    $('#div_send_email').removeClass('hide').addClass('show');
                }else{
                    $('#div_send_email').removeClass('show').addClass('hide');
                }
            });



        });
    </script>
@endsection