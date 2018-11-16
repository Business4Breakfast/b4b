@extends('layouts.app')

@section('title', 'Pridanie nového užívateľa' )

@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                    </div>
                    <div class="ibox-content">

                        @include('components.validation')

                        <form id="user_form" class="form-horizontal" method="POST" action="{{ route('finance.invoice-income.update', $inv->id) }}" >
                            {{ csrf_field() }}
                            {{ method_field('PUT') }}

                            <input id="registration_1" type="hidden"  data-slovensko-digital-autoform="registration-office" >
                            <input id="registration_2" type="hidden"  data-slovensko-digital-autoform="registration-number" >

                            <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
                                <label for="title" class="col-md-4 control-label">Obchodný názov (značka/franchísa)</label>
                                <div class="col-md-4">
                                    <input id="title" type="text" class="form-control" name="title" value="{{ $inv->suplier_company }}"  maxlength="50"  required>
                                    @if ($errors->has('title'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('title') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('ico') ? ' has-error' : '' }}">
                                <label for="ico" class="col-md-4 control-label">IČO</label>
                                <div class="col-md-2">
                                    <input id="ico" type="text" class="form-control" name="ico" value="{{ $inv->suplier_ico }}" maxlength="10" required>
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
                                    <input id="dic" type="text" class="form-control" name="dic" value="{{ $inv->suplier_dic }}"  maxlength="12" >
                                    @if ($errors->has('dic'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('dic') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('dph_payer') ? ' has-error' : '' }}">
                                <label for="ic_dph" class="col-md-4 control-label">Platiteľ DPH</label>
                                <div class="col-md-4">
                                    <div class="checkbox">
                                        <input id="dph_payer" type="checkbox" class="form-control" name="dph_payer">
                                        <label for="checkbox1">
                                            Zaškrtnúť ak je firma plátca dph
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('ic_dph') ? ' has-error' : '' }}">
                                <label for="ic_dph" class="col-md-4 control-label">IČ DPH</label>
                                <div class="col-md-2">
                                    <input id="ic_dph" type="text" class="form-control copy_text_vat" name="ic_dph" value="{{ $inv->suplier_ic_dph }}"  maxlength="15"  required>
                                    @if ($errors->has('ic_dph'))
                                        <span class="help-block">
                                             <strong>{{ $errors->first('ic_dph') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="col-md-4">
                                    <span class="help-block text-success" id="ic_dph"></span>
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('address_street') ? ' has-error' : '' }}">
                                <label for="address_street" class="col-md-4 control-label">Ulica</label>
                                <div class="col-md-4">
                                    <input id="address_street" type="text" class="form-control copy_text_function" name="address_street" value="{{ $inv->suplier_address_street }}"  maxlength="50"  required>
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
                                    <input id="address_psc" type="text" class="form-control" name="address_psc" value="{{ $inv->suplier_address_psc }}"  maxlength="8"  required>
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
                                    <input id="address_city" type="text" class="form-control" name="address_city" value="{{ $inv->suplier_address_city }}"  maxlength="50"  required>
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
                                    <div class="input-group">
                                        <select id="address_country" class="chosen-select-width form-control" name="address_country"  required>
                                            @foreach($countries as $country)
                                                <option value="{{$country->name}}" @if($inv->country == $country->name) selected="selected"  @endif >{{$country->name}}</option>
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
                            <div class="form-group{{ $errors->has('contact_person') ? ' has-error' : '' }}">
                                <label for="contact_person" class="col-md-4 control-label">Kontaktná osoba</label>
                                <div class="col-md-4">
                                    <input id="contact_person" type="search" class="form-control" name="contact_person" value="{{ $inv->suplier_contact_person }}"  maxlength="50" required>
                                    @if ($errors->has('contact_person'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('contact_person') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                <label for="email" class="col-md-4 control-label">E-Mail</label>
                                <div class="col-md-5">
                                    <input id="email" type="email" class="form-control" name="email" value="{{ $inv->suplier_email }}" required>
                                    @if ($errors->has('email'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('phone') ? ' has-error' : '' }}">
                                <label for="phone" class="col-md-4 control-label">Telefón</label>
                                <div class="col-md-4">
                                    <input id="phone" type="text" class="form-control" name="phone" value="{{ $inv->suplier_phone }}" placeholder="+421" required>

                                    @if ($errors->has('phone'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('phone') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <hr>
                            <div class="form-group{{ $errors->has('var_symbol') ? ' has-error' : '' }}">
                                <label for="var_symbol" class="col-md-4 control-label">Číslo faktúry/variabilný symbol</label>
                                <div class="col-md-3">
                                    <input id="var_symbol" type="var_symbol" class="form-control" name="var_symbol" value="{{ $inv->variable_symbol }}" required>
                                    @if ($errors->has('var_symbol'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('var_symbol') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('internal_id') ? ' has-error' : '' }}">
                                <label for="internal_id" class="col-md-4 control-label">Interné číslo faktúry</label>
                                <div class="col-md-3">
                                    <input id="internal_id" type="internal_id" class="form-control" name="internal_id" value="{{ $inv->internal_id }}">
                                    @if ($errors->has('var_symbol'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('internal_id') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('date_delivery') ? ' has-error' : '' }} date_picker_year" >
                                <label for="date_delivery" class="col-md-4 control-label">Dátum dodania</label>
                                <div class="col-md-3">
                                    <div class="input-group date">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input type="text" class="form-control date_mask" value="{{ Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $inv->date_delivery)->format('d.m.Y') }}" name="date_delivery" >
                                    </div>
                                    @if ($errors->has('date_delivery'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('date_delivery') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('date_pay_to') ? ' has-error' : '' }} date_picker_year" >
                                <label for="date_pay_to" class="col-md-4 control-label">Dátum splatnosti</label>
                                <div class="col-md-3">
                                    <div class="input-group date">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input type="text" class="form-control date_mask" value="{{ Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $inv->date_pay_to)->format('d.m.Y') }}" name="date_pay_to" >
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
                                    <input id="price" type="number" class="form-control" name="price" value="{{ $inv->price }}"  required>
                                    @if ($errors->has('price'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('price') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div id="dhp_element" class="">
                                <div class="form-group{{ $errors->has('dph') ? ' has-error' : '' }}">
                                    <label for="dph" class="col-md-4 control-label">DPH</label>
                                    <div class="col-md-2">
                                        <input id="dph" type="text" class="form-control" name="dph" value="{{ $inv->price_dph }}"  required >
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
                                        <input id="price_dph" type="text" class="form-control" name="price_dph" value="{{ $inv->price_w_dph }}"  required >
                                        @if ($errors->has('price_dph'))
                                            <span class="help-block">
                                        <strong>{{ $errors->first('price_dph') }}</strong>
                                    </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="form-group{{ $errors->has('type') ? ' has-error' : '' }}">
                                <label for="type" class="col-md-4 control-label">Kategória nákladov</label>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <select id="type" class="form-control" name="type" >
                                            <option value="">-- výber --</option>
                                            @foreach($texts as $text)
                                                <option value="{{$text->id}}" @if($inv->type == $text->id) selected="selected"  @endif data-name="{{$text->name }}">{{str_limit($text->name, 30)}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @if ($errors->has('type'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('type') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                                <label for="description" class="col-md-4 control-label">Popis</label>
                                <div class="col-md-4">
                                    <textarea id="description" class="form-control" name="description" rows="4">{{ $inv->description }}</textarea>
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

    <script src="{!! asset('js/plugins/jquery-ui-1.12.1/jquery-ui.js') !!}"></script>
    <script async src="https://autoform.ekosystem.slovensko.digital/assets/autoform.js"></script>

    <script>
        $(document).ready(function (){

            $('.date_mask').mask('00.00.0000');

            $('#type').change( function (e) {
                e.preventDefault();

                var type = $('#type option:selected').data('name');

               // $('#description').val($(this).find('option:selected').text()+' ').focus();
                $('#description').val(type+' ').focus();
            })

            $("#company_id").chosen().change(function(){
                var id = $(this).val();
                $.ajax({
                    type:'POST',
                    url:'/ajax/get-company-data',
                    data: { company_id: id},
                    dataType: 'json',
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    success:function(data){
                        if(data.status == 'true'){

                            $('#company_name').val(data.data.company_name);
                            $('#ico').val(data.data.ico);
                            $('#dic').val(data.data.dic);
                            $('#ic_dph').val(data.data.ic_dph);
                            $('#registration').val(data.data.registration);
                            $('#address_street').val(data.data.address_street);
                            $('#address_city').val(data.data.address_city);
                            $('#address_psc').val(data.data.address_psc);
                            $('#email').val(data.data.email);
                            $('#phone').val(data.data.phone);
                            $('#contact_person').val(data.data.contact_person);

                        } else {
                            $('#title').val("");
                            $('#ico').val("");
                            $('#dic').val("");
                            $('#ic_dph').val("");
                            $('#registration').val("");
                            $('#address_street').val("");
                            $('#address_city').val("");
                            $('#address_psc').val("");
                            $('#email').val("");
                            $('#phone').val("");
                            $('#contact_person').val("");
                        }
                    }
                });
            });


            $("#search_user").chosen().change(function(){
                var id = $(this).val();
                $.ajax({
                    type:'POST',
                    url:'/ajax/get-user-detail',
                    data: { user_id: id},
                    dataType: 'json',
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    success:function(ajax_data){

                        if(ajax_data.status == 'true'){

                            $('#contact_person').val(ajax_data.data.title_before+" "+ajax_data.data.name+" "+ajax_data.data.surname+" "+ajax_data.data.title_after);
                            $('#email').val(ajax_data.data.email);
                            $('#phone').val(ajax_data.data.phone)
                            $('#url').val(ajax_data.data.internet);

                        } else {

                            $('#contact_person').val("");
                            $('#email').val("");
                            $('#phone').val("");
                            $('#url').val("");

                        }

                    }
                });
            });

            (function ($) {
                var originalVal = $.fn.val;
                $.fn.val = function (value) {
                    var res = originalVal.apply(this, arguments);

                    if (this.is('.copy_text_function') && arguments.length >= 1 ) {
                        // this is input type=text setter
                        this.trigger("input");
                    }

                    if (this.is('.copy_text_vat') && arguments.length >= 1 ) {
                        // this is input type=text setter
                        this.trigger("input_vat");
                    }

                    return res;
                };
            })(jQuery);

            var registration_1 =  $("#registration_1");
            var registration_2 =  $("#registration_2");
            var $input = $("#address_street");
            var compmany = $('#company_name');

            var $vat = $('#ic_dph');

            $input.on('input', function() {
                // Do this when value changes
                $('#title').val(compmany.val());
                $('#registration').val(registration_1.val() + ', ' + registration_2.val());

                $('#email').val("");
                $('#phone').val("");
                $('#contact_person').val("");
                $("#search_user").val("").trigger("chosen:updated")

            });

            $vat.on('input_vat', function() {
                if($vat.length && $vat.val().length){
                    $('#dph_payer').prop('checked', true);
                } else {
                    $('#dph_payer').prop('checked', false);
                }
            });

            var message = 'Nesprávne číslo pre DPH';
            $("#user_form").validate({

                rules: {
                    phone: {
                        required: true,
                        minlength: 13
                    },
                    url: {
                        url: true
                    },
                   ic_dph: {
                       required:"#dph_payer:checked"
                   }
                },
                messages: {
                    ic_dph: {
                        remote: function(){ return message; }
                    }
                }
            });


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