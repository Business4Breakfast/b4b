@extends('layouts.app')

@section('title', 'Editácia faktúry')

@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title"></div>
                    <div class="ibox-content">

                        @include('components.validation')

                        <form id="invoice_form" class="form-horizontal" method="POST" action="{{ route('finance.invoice.update',['id' => $item->id]) }}" >
                            {{ csrf_field() }}
                            {{ method_field('PUT') }}

                            <div class="form-group{{ $errors->has('variable_symbol') ? ' has-error' : '' }}">
                                <label for="variable_symbol" class="col-md-4 control-label">Číslo faktúry</label>
                                <div class="col-md-2">
                                    <input id="variable_symbol" type="text" class="form-control" name="variable_symbol" value="{{ $item->variable_symbol }}"  maxlength="50"  readonly>
                                    @if ($errors->has('variable_symbol'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('variable_symbol') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('company_id') ? ' has-error' : '' }}">
                                <label for="company_id" class="col-md-4 control-label">Spoločnosť</label>
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <select id="company_id" class="chosen-select  chosen-select-no-single chosen-select-deselect form-control" name="company_id"
                                                style="width: 400px;" data-placeholder="Výber platiteľa..." >
                                            <option value="0">Výber platiteľa...</option>
                                            @foreach($companies as $company)
                                                <option value="{{$company->id}}" @if($item->company_id == $company->id)  selected="selected" @endif >{{$company->company_name}} - [ {{$company->ico}} ]</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @if ($errors->has('company_id'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('company_id') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <hr>
                            <div class="form-group{{ $errors->has('company_title') ? ' has-error' : '' }}">
                                <label for="company_title" class="col-md-4 control-label">Názov firmy</label>
                                <div class="col-md-4">
                                    <input id="company_title" type="text" class="form-control" name="company_title" value="{{ $item->company_title }}"  maxlength="50"  required>
                                    @if ($errors->has('company_title'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('company_title') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('ico') ? ' has-error' : '' }}">
                                <label for="ico" class="col-md-4 control-label">IČO</label>
                                <div class="col-md-2">
                                    <input id="ico" type="text" class="form-control" name="ico" value="{{ $item->ico }}" maxlength="10" required>
                                    @if ($errors->has('ico'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('ico') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('dic') ? ' has-error' : '' }}">
                                <label for="dic" class="col-md-4 control-label">DIČ</label>
                                <div class="col-md-2">
                                    <input id="dic" type="text" class="form-control" name="dic" value="{{ $item->dic }}"  required>
                                    @if ($errors->has('dic'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('dic') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('ic_dph') ? ' has-error' : '' }}">
                                <label for="ic_dph" class="col-md-4 control-label">IČ DPH</label>
                                <div class="col-md-2">

                                    <div class="input-group m-b"><span class="input-group-btn">
                                        <input id="ic_dph" type="text" class="form-control" name="ic_dph" value="{{ $item->ic_dph }}">
                                        <button id="btn_ic_dph" type="button" class="btn btn-primary">Overiť</button> </span>
                                    </div>

                                    @if ($errors->has('ic_dph'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('ic_dph') }}</strong>
                                    </span>
                                    @endif
                                    <span class="error" id="company_vat"></span>
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('address_street') ? ' has-error' : '' }}">
                                <label for="address_street" class="col-md-4 control-label">Ulica</label>
                                <div class="col-md-4">
                                    <input id="address_street" type="text" class="form-control" name="address_street" value="{{ $item->address_street }}"  maxlength="50" required>
                                    @if ($errors->has('address_street'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('address_street') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('address_psc') ? ' has-error' : '' }}">
                                <label for="address_psc" class="col-md-4 control-label">PSČ</label>
                                <div class="col-md-2">
                                    <input id="address_psc" type="text" class="form-control" name="address_psc" value="{{ $item->address_psc }}" maxlength="8"  required>
                                    @if ($errors->has('address_psc'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('address_psc') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('address_city') ? ' has-error' : '' }}">
                                <label for="address_city" class="col-md-4 control-label">Mesto</label>
                                <div class="col-md-4">
                                    <input id="address_city" type="text" class="form-control" name="address_city" value="{{ $item->address_city }}"  maxlength="50"  required>
                                    @if ($errors->has('address_city'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('address_city') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('address_country') ? ' has-error' : '' }}">
                                <label for="address_country" class="col-md-4 control-label">Štát</label>
                                <div class="col-md-4">
                                    {{--<input id="address_country" type="text" class="form-control" name="address_country" value="{{ old('address_country') }}" required>--}}
                                    <div class="input-group">
                                        <select id="address_country" class="chosen-select form-control" name="address_country"  required>
                                                @foreach($countries as $country)
                                                    <option value="{{$country->name}}"  @if($item->address_country == $country->name)  selected="selected" @endif>{{$country->name}}</option>
                                                @endforeach
                                        </select>
                                    </div>
                                @if ($errors->has('address_country'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('address_country') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('date_create') ? ' has-error' : '' }} date_picker_year" >
                                <label for="date_create" class="col-md-4 control-label">Deň vystavenia</label>
                                <div class="col-md-3">
                                    <div class="input-group date">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input type="text" class="form-control date_mask" value="{{ Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $item->date_create)->format('d.m.Y') }}" name="date_create" >
                                    </div>
                                    @if ($errors->has('date_create'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('date_create') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('date_delivery') ? ' has-error' : '' }} date_picker_year" >
                                <label for="date_delivery" class="col-md-4 control-label">Deň dodania</label>
                                <div class="col-md-3">
                                    <div class="input-group date">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input type="text" class="form-control date_mask"  name="date_delivery" value="{{ Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $item->date_delivery)->format('d.m.Y') }}" >
                                    </div>
                                    @if ($errors->has('date_delivery'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('date_delivery') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('date_pay_to') ? ' has-error' : '' }} date_picker_year" >
                                <label for="date_pay_to" class="col-md-4 control-label">Deň splatnosti</label>
                                <div class="col-md-3">
                                    <div class="input-group date">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input type="text" class="form-control date_mask" value="{{ Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $item->date_pay_to)->format('d.m.Y') }}" name="date_pay_to" >
                                    </div>
                                    @if ($errors->has('date_pay_to'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('date_pay_to') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('price') ? ' has-error' : '' }}">
                                <label for="price" class="col-md-4 control-label">Cena bez DPH</label>
                                <div class="col-md-2">
                                    <input id="price" type="number" class="form-control" name="price" value="{{ $item->price }}"  required>
                                    @if ($errors->has('price'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('price') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-4 control-label"></label>
                                <div class="col-sm-5">
                                    <div class="checkbox">
                                        <input type="hidden" name="vat_invoice" value="0">
                                        <input type="checkbox" name="vat_invoice" id="vat_invoice" value="1" class="form-control" @if($item->price > 0 && $item->price == $item->price_w_dph && $item->price_dph == 0) checked="checked" @else disabled @endif >
                                        <label for="checkbox">
                                            Faktúrovať bez DPH (zahraničie)
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div id="dhp_element" class="">
                                <div class="form-group{{ $errors->has('dph') ? ' has-error' : '' }}">
                                    <label for="dph" class="col-md-4 control-label">DPH</label>
                                    <div class="col-md-2">
                                        <input id="dph" type="text" class="form-control" name="dph" value="{{ $item->price_dph }}"  required readonly>
                                        @if ($errors->has('dph'))
                                            <span class="help-block">
                                        <strong>{{ $errors->first('dph') }}</strong>
                                    </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group{{ $errors->has('price_dph') ? ' has-error' : '' }}">
                                    <label for="price_dph" class="col-md-4 control-label">Cena s DPH</label>
                                    <div class="col-md-2">
                                        <input id="price_dph" type="text" class="form-control" name="price_dph" value="{{ $item->price_w_dph }}"  required readonly="">
                                        @if ($errors->has('price_dph'))
                                            <span class="help-block">
                                        <strong>{{ $errors->first('price_dph') }}</strong>
                                    </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('invoice_text') ? ' has-error' : '' }}">
                                <label for="invoice_text" class="col-md-4 control-label">Šablony textu faktúr</label>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <select id="invoice_text" class="form-control" name="invoice_text" >
                                            <option value="">-- výber --</option>
                                            @foreach($texts as $text)
                                                <option value="{{$text->name}}">{{str_limit($text->description, 30)}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @if ($errors->has('invoice_text'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('invoice_text') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <hr>
                            <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                                <label for="description" class="col-md-4 control-label">Text faktúry</label>
                                <div class="col-md-6">
                                    <textarea id="description" class="form-control" name="description" rows="8" required>{!! str_replace("\r", '',  $item->description) !!}</textarea>
                                    @if ($errors->has('description'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('description') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            @if($memberships->count() > 0 )
                                <div class="form-group" >
                                    <label for="valid_to" class="col-md-4 control-label">Členstvo</label>
                                    <div class="col-md-6">
                                        <table class="table" style="font-size: small">
                                            <tbody >
                                            @foreach($memberships as $i)
                                                <tr>
                                                    <td class="pull-left">Číslo členstva: {{$i->id}}</td>
                                                    <td class="pull-left">{{$i->company->company_name}}</td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif

                            <div class="form-group">
                                <div class="col-md-6 col-md-offset-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{__('form.edit_record')}}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection

@section('scripts')


    <script>
        $(document).ready(function (){

            $('#phone').mask('+000000000000');

            $('.date_mask').mask('00.00.0000');

            $("#invoice_text").change(function(){
                var textval = $(":selected",this).val();
                $('#description').val(textval);
            });

            $('#vat_invoice').click(function() {
                if ($(this).is(':checked')) {
                    $('#ic_dph').attr("disabled", true);
                    $('#btn_ic_dph').attr("disabled", true);

                    $('#dhp_element').removeClass('show').addClass('hide');
                }else{
                    $('#ic_dph').attr("disabled", false);
                    $('#btn_ic_dph').attr("disabled", false);
                    $('#dhp_element').removeClass('hide').addClass('show');
                }
            });

            $("#company_id").chosen().change(function(){
                var id = $(this).val();
                console.log(id);

                $.ajax({
                    type:'POST',
                    url:'/ajax/get-company-data',
                    data: { company_id: id},
                    dataType: 'json',
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    success:function(ajax_data){

                        console.log(ajax_data);
                        if(ajax_data.status == 'true'){

                            $('#ico').val(ajax_data.data.ico);
                            $('#dic').val(ajax_data.data.dic);
                            $('#ic_dph').val(ajax_data.data.ic_dph);
                            $('#company_title').val(ajax_data.data.company_name);

                            $('#address_psc').val(ajax_data.data.address_psc);
                            $('#address_street').val(ajax_data.data.address_street);
                            $('#address_city').val(ajax_data.data.address_city)
                            $('#registration').val(ajax_data.data.registration);
                            $('#address_country').val(ajax_data.data.address_country);
                            $('#address_country').trigger("chosen:updated");

                            //overenie dph
                            doneTyping();
                        } else {

                            $('#ico').val("");
                            $('#dic').val("");
                            $('#ic_dph').val("");
                            $('#company_title').val("");

                            $('#address_psc').val("");
                            $('#address_street').val("");
                            $('#address_city').val("");
                            $('#registration').val("");
                            $('#address_country').val('Slovensko');
                            $('#address_country').trigger("chosen:updated");

                        }

                    }
                });


            });

            $.validator.setDefaults({ ignore: ":hidden:not(.chosen-select)" });
            $("#invoice_form").validate({
                rules: {
                    phone: {
                        required: true,
                        minlength: 13
                    }
                    //company_id: "required"
                }
            });

            var message = 'Nesprávne číslo pre DPH';
            $("#user_form").validate({

                rules: {
                    phone: {
                        required: true,
                        minlength: 13
                    },
                    ic_dph: {
                        required:"#dph_payer:checked",
                        remote: {
                            url: "ajax/check-vat-registration",
                            type: "post",
                            data:{number: $("#ic_dph").val()},
                            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                            dataFilter: function(data) {
                                response = $.parseJSON(data);

                                if (response.status.valid === true) {
                                    $("#title_vat").html(response.status.name);
                                    return true;
                                } else {
                                    message = response.status.error;
                                    return false;
                                }
                            }
                        }

                    }

                },
                messages: {
                    ic_dph: {
                        remote: function(){ return message; }
                    }

                }
            });

            //setup before functions
            var typingTimer;                //timer identifier
            var doneTypingInterval = 5000;  //time in ms (5 seconds)

            //on keyup, start the countdown
            $('#ic_dph').keyup(function(){
                clearTimeout(typingTimer);
                if ($('#ic_dph').val()) {
                    typingTimer = setTimeout(doneTyping, doneTypingInterval);
                }
            });

            $("#btn_ic_dph").click(function(){
                doneTyping();
            });

            //user is "finished typing," do something
            function doneTyping () {
                //do something
                //toastr.success($("#dic").val());

                $.ajax({
                    type:"POST",
                    url: "ajax/check-vat-registration",
                    data:{"ic_dph": $("#ic_dph").val()},
                    dataType: 'json',
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    success:function(response){

                        console.log(response);

                        if (response.status.valid === true) {
                            toastr.info(response.status.name);
                            $("#company_vat").html(response.status.name);
                            $("#vat_invoice").attr("disabled", false);

                            return true;
                        } else {
                            message = response.status.error;
                            toastr.error(message);
                            $("#company_vat").html(message);
                            $("#vat_invoice").attr("disabled", true);
                            return false;
                        }

                    }
                });
            }

            $("#price").on('blur keyup change click input', function () {

                var vat = {!! config('invoice.setting.vat') !!};
                var price = $('#price').val();
                var vat_value = ((price*vat)/100).toFixed(2);
                var total = parseFloat(vat_value) + parseFloat(price);

                $('#dph').val(vat_value);
                $('#price_dph').val(parseFloat(total).toFixed(2));

            });

        });
    </script>
@endsection

