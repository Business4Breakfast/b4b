@extends('layouts.app')

@section('title', 'Uhrada členstva' )

@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                    </div>
                    <div class="ibox-content">

                        @include('components.validation')

                        {!! Form::open(['route' => ['setting.membership.payment.store', $membership->id], 'method' => 'POST' ,'class' => 'form-horizontal', 'id' => 'membership_payment']) !!}
                        {!! Form::hidden('membership_id', $membership->id ) !!}

                        <div class="form-group" >
                            <label for="valid_to" class="col-md-4 control-label">Číslo členstva</label>
                            <div class="col-md-6">
                                <div class="input-group date">
                                    <h3 class="form-control-static">{{ str_pad($membership->id,3,'0', STR_PAD_LEFT) }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="form-group" >
                            <label for="valid_to" class="col-md-4 control-label">Platitel</label>
                            <div class="col-md-6">
                                <div class="input-group date">
                                    <p class="form-control-static">{{ $membership->company->company_name }}, {{ $membership->company->address_street }}, {{ $membership->company->address_psc }} {{ $membership->company->address_city }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="form-group" >
                            <label for="valid_to" class="col-md-4 control-label">Kluby</label>
                            <div class="col-md-8">
                                <div class="input-group date">
                                    @if($membership->club)
                                        @foreach($membership->club as $c)
                                            <p class="form-control-static">{{ $c->title }}, {{ $c->address_city}}</p>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="form-group" >
                            <label for="valid_to" class="col-md-4 control-label">Členovia</label>
                            <div class="col-md-8">
                                <div class="input-group date">
                                    @if($membership->user)
                                        @foreach($membership->user as $u)
                                            <p class="form-control-static">{{ $u->full_name }}</p>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>

                        @if($payments->count() > 0 )
                            <div class="form-group" >
                                <label for="valid_to" class="col-md-4 control-label">Úhrady členstva</label>
                                <div class="col-md-6">
                                    <table class="table" style="font-size: small">
                                        <tbody >
                                        @foreach($payments as $p)
                                            <tr  @if( in_array($p->payment_type, [5,6,8])) class="text-danger" @else class="text-success"  @endif>
                                                <td> {{ Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $p->date_payment)->format('d.m.Y H:i') }}</td>
                                                <td>{{$p->type}}</td>
                                                <td>{{$p->full_name}}</td>
                                                <td>{{$p->description}}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                        <hr>
                        <div class="form-group" >
                            <label for="valid_to" class="col-md-4 control-label">Typ úhrady</label>
                            <div class="col-md-8">
                                <div class="input-group date">
                                    <select id="payment_type" class="form-control" name="payment_type" required>
                                        <option value="">-- výber --</option>
                                        @foreach($types as $t)
                                            <option value="{{$t->id}}">{{str_limit($t->title, 50)}}</option>
                                        @endforeach
                                    </select>
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
                                    {{__('form.add_record')}}
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

            $("#membership_payment").validate({
                rules: {
                    payment_type: {
                        required: true
                    }
                }
            });

        });
    </script>
@endsection