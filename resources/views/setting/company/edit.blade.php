@extends('layouts.app')

@section('title', 'Úprava údajov spoločnosti')

@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title"></div>
                    <div class="ibox-content">

                        @include('components.validation')

                        <form id="user_form" class="form-horizontal" method="POST" action="{{ route('setting.company.update', ['id' =>  $company->id]) }}" enctype="multipart/form-data" >
                            {{ csrf_field() }}
                            {{ method_field('PUT') }}

                            <div class="form-group{{ $errors->has('company_name') ? ' has-error' : '' }}">
                                <label for="company_name" class="col-md-4 control-label">Registračný názov firmy</label>
                                <div class="col-md-4">
                                    <input id="company_name" type="text" class="form-control" name="company_name" value="{{ $company->company_name }}" required autofocus>
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
                                    <input id="ico" type="text" class="form-control" name="ico" value="{{ $company->ico }}" maxlength="10"  readonly>
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
                                    <input id="title" type="text" class="form-control" name="title" value="{{ $company->title }}" maxlength="50"  required>
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
                                    <input id="dic" type="text" class="form-control" name="dic" value="{{ $company->dic }}"  maxlength="12" required>
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
                                        <input id="dph_payer" type="checkbox" class="form-control" name="dph_payer"  @if(strlen($company->ic_dph) > 0 ) checked="checked" @endif >
                                        <label for="checkbox1">
                                            Zaškrtnúť ak je firma plátca dph
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('ic_dph') ? ' has-error' : '' }}">
                                <label for="ic_dph" class="col-md-4 control-label">IČ DPH</label>
                                <div class="col-md-2">
                                    <input id="ic_dph" type="text" class="form-control" name="ic_dph" value="{{ $company->ic_dph }}"  maxlength="15"
                                           @if(!strlen($company->ic_dph) > 0 ) readonly @endif >
                                    <span class="help-block" id="title_vat">
                                    @if ($errors->has('ic_dph'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('ic_dph') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('registration') ? ' has-error' : '' }}">
                                <label for="registration" class="col-md-4 control-label">Registrácia</label>
                                <div class="col-md-4">
                                    <input id="registration" type="text" class="form-control" name="registration" value="{{ $company->registration }}"  maxlength="50" >
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
                                    <input id="address_street" type="text" class="form-control" name="address_street" value="{{ $company->address_street }}" maxlength="50" required>
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
                                    <input id="address_psc" type="text" class="form-control" name="address_psc" value="{{ $company->address_psc }}" maxlength="8"  required>
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
                                    <input id="address_city" type="text" class="form-control" name="address_city" value="{{ $company->address_city }}" maxlength="50"  required>
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
                                                    <option value="{{$country->name}}" @if($country->name == $company->address_country) selected="selected" @endif>{{$country->name}}</option>
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
                                <label for="contact_person" class="col-md-4 control-label">Kontaktná osoba</label>
                                <div class="col-md-4">
                                    <input id="address_country" type="text" class="form-control" name="contact_person" value="{{ $company->contact_person }}"  maxlength="50" required>
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
                                    <input id="email" type="email" class="form-control" name="email" value="{{ $company->email }}" required>
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
                                    <input id="phone" type="text" class="form-control" name="phone" value="{{ $company->phone }}" placeholder="+421" required>
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
                                    <input id="url" type="url" class="form-control" name="url" value="{{ $company->url }}" placeholder="http://" required>
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
                                    <textarea id="description" class="form-control" name="description" rows="4">{{ $company->description }}</textarea>
                                    @if ($errors->has('description'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('description') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <hr>
                            <div class="form-group">
                                <label for="password_confirmation" class="col-md-4 control-label">Logo spoločnosti</label>
                                <div class="col-md-4">
                                    <div class="fileinput fileinput-new" data-provides="fileinput">
                                        <div class="fileinput-new thumbnail" style="width: 200px; height: 200px;">
                                            @if(file_exists('images/company/' . $company->id . '/' .$company->image))
                                                <img data-src="holder.js/100%X100%"  alt="..." src="{!! asset('images/company') !!}/{{$company->id}}/{!! $company->image !!}">
                                            @else
                                                <p class="text-center m-t-md">
                                                    <i class="fa fa-upload big-icon"></i>
                                                </p>
                                            @endif
                                        </div>
                                        <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 300px; max-height: 200px;"></div>
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

                                //console.log(response.status);

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