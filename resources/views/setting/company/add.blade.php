@extends('layouts.app')

@section('title', 'Pridanie novej spoločnosti')

@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title"></div>
                    <div class="ibox-content">

                        @include('components.validation')

                        <form id="user_form" class="form-horizontal" method="POST" action="{{ route('setting.company.store') }}" enctype="multipart/form-data" data-slovensko-digital-autoform="9eaddf6ee97b9312cb11b111179737e7ad673b0e72fe679d5df34578e984e03096c5355155efecb0" >
                            {{ csrf_field() }}

                            <input id="registration_1" type="hidden"  data-slovensko-digital-autoform="registration-office" >
                            <input id="registration_2" type="hidden"  data-slovensko-digital-autoform="registration-number" >

                            <div class="form-group{{ $errors->has('company_name') ? ' has-error' : '' }}">
                                <label for="company_name" class="col-md-4 control-label">Registračný názov firmy</label>
                                <div class="col-md-4">
                                    <input id="company_name" type="text" class="form-control" name="company_name" value="{{ old('company_name') }}" data-slovensko-digital-autoform="name" required autofocus>
                                    @if ($errors->has('company_name'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('company_name') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('ico') ? ' has-error' : '' }}">
                                <label for="ico" class="col-md-4 control-label">IČO</label>
                                <div class="col-md-2">
                                    <input id="ico" type="text" class="form-control" name="ico" value="{{ old('ico') }}" maxlength="10" data-slovensko-digital-autoform="cin" required>
                                    @if ($errors->has('ico'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('ico') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <hr>
                            <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
                                <label for="title" class="col-md-4 control-label">Obchodný názov (značka/franchísa)</label>
                                <div class="col-md-4">
                                    <input id="title" type="text" class="form-control" name="title" value="{{ old('title') }}"  maxlength="50"  required>
                                    @if ($errors->has('title'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('title') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('dic') ? ' has-error' : '' }}">
                                <label for="dic" class="col-md-4 control-label">DIČ</label>
                                <div class="col-md-2">
                                        <input id="dic" type="text" class="form-control" name="dic" value="{{ old('dic') }}"  maxlength="12" data-slovensko-digital-autoform="tin">
                                    @if ($errors->has('dic'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('dic') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('ic_dph') ? ' has-error' : '' }}">
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
                                    <input id="ic_dph" type="text" class="form-control copy_text_vat" name="ic_dph" value="{{ old('ic_dph') }}"  maxlength="15" data-slovensko-digital-autoform="vatin"  required>
                                    @if ($errors->has('ic_dph'))
                                        <span class="help-block">
                                             <strong>{{ $errors->first('ic_dph') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="col-md-4">
                                    <span class="help-block text-success" id="title_vat"></span>
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('registration') ? ' has-error' : '' }}">
                                <label for="registration" class="col-md-4 control-label">Registrácia</label>
                                <div class="col-md-4">
                                    <input id="registration" type="text" class="form-control copy_text_function" name="registration" value="{{ old('registration') }}" maxlength="50">
                                    @if ($errors->has('registration'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('registration') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('address_street') ? ' has-error' : '' }}">
                                <label for="address_street" class="col-md-4 control-label">Ulica</label>
                                <div class="col-md-4">
                                    <input id="address_street" type="text" class="form-control copy_text_function" name="address_street" value="{{ old('address_street') }}"  maxlength="50"  data-slovensko-digital-autoform="formatted-street" required>
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
                                    <input id="address_psc" type="text" class="form-control" name="address_psc" value="{{ old('address_psc') }}"  maxlength="8"  data-slovensko-digital-autoform="postal-code" required>
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
                                    <input id="address_city" type="text" class="form-control" name="address_city" value="{{ old('address_city') }}"  maxlength="50"  data-slovensko-digital-autoform="municipality"  required>
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
                                        <select id="address_country" class="chosen-select form-control" name="address_country"  required>
                                                @foreach($countries as $country)
                                                    <option value="{{$country->name}}">{{$country->name}}</option>
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
                            <hr>

                            <div class="form-group{{ $errors->has('contact_person') ? ' has-error' : '' }}">
                                <label for="contact_person" class="col-md-4 control-label">Zoznam kontaktov</label>
                                <div class="col-md-4">
                                    <select id="search_user" data-placeholder="Uživateľ..." class="chosen-select"  style="width:100%;"  tabindex="4">
                                        <option value="">-- výber --</option>
                                        @foreach($users as $key => $user)
                                            <option value="{{$user->id}}">{{$user->full_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('contact_person') ? ' has-error' : '' }}">
                                <label for="contact_person" class="col-md-4 control-label">Kontaktná osoba</label>
                                <div class="col-md-4">
                                    <input id="contact_person" type="search" class="form-control" name="contact_person" value="{{ old('contact_person') }}"  maxlength="50" required>
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
                                    <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required>

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
                                    <input id="phone" type="text" class="form-control" name="phone" value="{{ old('phone') }}" placeholder="+421" required>

                                    @if ($errors->has('phone'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('phone') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('url') ? ' has-error' : '' }}">
                                <label for="url" class="col-md-4 control-label">Internet</label>
                                <div class="col-md-4">
                                    <input id="url" type="url" class="form-control" name="url" value="{{ old('url') }}" placeholder="http://" required>
                                    @if ($errors->has('url'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('url') }}</strong>
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
                                <label for="user_image" class="col-md-4 control-label">Logo spoločnosti</label>
                                <div class="col-md-4">
                                    <div class="fileinput fileinput-new" data-provides="fileinput">
                                        <div class="fileinput-new thumbnail" style="width: 200px; height: 200px;">
                                            <p class="text-center m-t-md">
                                                <i class="fa fa-upload big-icon"></i>
                                            </p>
                                        </div>
                                        <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 200px; max-height: 200px;"></div>
                                        <div>
                                            <span class="btn btn-default btn-file">
                                                <span class="fileinput-new"><i class="fa fa-paperclip"></i> {{__('form.file_select')}}</span><span class="fileinput-exists">
                                                    <i class="fa fa-undo"></i> {{__('form.new_file')}}</span>
                                                <input type="file" name="files">
                                            </span>
                                            <a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">
                                                <i class="fa fa-trash"></i> {{__('form.delete')}}</a>
                                        </div>
                                    </div>
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


@endsection

@section('scripts')

    <script src="{!! asset('js/plugins/jquery-ui-1.12.1/jquery-ui.js') !!}"></script>
    <script async src="https://autoform.ekosystem.slovensko.digital/assets/autoform.js"></script>

    <script>
        $(document).ready(function (){

            $('#phone').mask('+000000000000');

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

            $('#url').keyup(function () {
                if (  ($(this).val().length >=5)
                    && ($(this).val().substr(0, 5) != 'http:')
                    && ($(this).val().substr(0, 5) != 'https') ) {
                        $(this).val('http://' + $(this).val());
                }
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
            });

            $vat.on('input_vat', function() {
                if($vat.length && $vat.val().length){
                    $('#dph_payer').prop('checked', true);
                } else {
                    $('#dph_payer').prop('checked', false);
                }
            });

            $('#dph_payer').change(function(){
                $('#ic_dph').val('');
                $('#ic_dph').attr('readonly', !this.checked, false);
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

        });
    </script>
@endsection