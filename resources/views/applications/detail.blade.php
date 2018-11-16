@extends('layouts.app')

@section('title', 'Application detail')

@section('content')

    <div class="wrapper wrapper-content">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 col-lg-offset-2">

                    <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <h3 class="font-bold no-margins">
                                Prihláška do podnikateľského klubu BFORB
                            </h3>
                        </div>
                        <div class="ibox-content">

                            @include('components.validation')

                            <form id="new_member_form" enctype="multipart/form-data" class="form-horizontal" method="POST" action="{{ route('applications.store') }}">
                                {{ csrf_field() }}
                                {{Form::hidden('user_id',  (isset($user->id)) ? $user->id : 0 ) }}

                                <div class="form-group{{ $errors->has('invite_person') ? ' has-error' : '' }}">
                                    <label for="invite_person" class="col-md-4 control-label">Pozývajúci člen</label>
                                    <div class="col-md-6">
                                        <input id="invite_person" type="text" class="form-control" name="invite_person" value="{{$data->name}}" readonly>
                                        @if ($errors->has('invite_person'))
                                            <span class="help-block">
                                        <strong>{{ $errors->first('invite_person') }}</strong>
                                    </span>
                                        @endif
                                    </div>
                                </div>
                                <hr>
                                <H3 class="m-b">1. Údaje o firme</H3>
                                <div class="form-group{{ $errors->has('company') ? ' has-error' : '' }}">
                                    <label for="company" class="col-md-4 control-label">Názov firmy</label>
                                    <div class="col-md-6">
                                        <input id="company" type="text" class="form-control" name="company" value="{{ (old('company') != "") ? old('company') : isset($data->data->company) ? $data->data->company : null }}" required>
                                        @if ($errors->has('company'))
                                            <span class="help-block">
                                        <strong>{{ $errors->first('company') }}</strong>
                                    </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group{{ $errors->has('industry') ? ' has-error' : '' }}">
                                    <label for="industry" class="col-md-4 control-label">Typ podnikania</label>
                                    <div class="col-md-4">

                                        <select class="form-control chosen-select" name="industry" class="form-control" required>
                                            <option value="">-- výber --</option>
                                            @foreach($industry as $in)
                                                <option value="{{$in->id}}" @if(old('industry') == $in->id ) selected="selected" @elseif($data->data->industry == $in->id) selected="selected" @endif>{{$in->name}}</option>
                                            @endforeach
                                        </select>

                                        @if ($errors->has('industry'))
                                            <span class="help-block">
                                        <strong>{{ $errors->first('industry') }}</strong>
                                    </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                    <label for="email" class="col-md-4 control-label">E-mail</label>
                                    <div class="col-md-6">
                                        <input id="email" type="email" class="form-control" name="email" value="{{ (old('email') != "") ? old('email') : isset($data->data->email) ? $data->data->email : null }}" required>

                                        @if ($errors->has('email'))
                                            <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group{{ $errors->has('phone') ? ' has-error' : '' }}">
                                    <label for="phone" class="col-md-4 control-label">Telefón</label>
                                    <div class="col-md-6">
                                        <input id="phone" type="text" class="form-control" name="phone" value="{{ (old('phone') != "") ? old('phone') : isset($data->data->phone) ? "+421".$data->data->phone : null }}" placeholder="" required>

                                        @if ($errors->has('phone'))
                                            <span class="help-block">
                                        <strong>{{ $errors->first('phone') }}</strong>
                                    </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group{{ $errors->has('internet') ? ' has-error' : '' }}">
                                    <label for="internet" class="col-md-4 control-label">Internet</label>
                                    <div class="col-md-6">
                                        <input id="internet" type="url" class="form-control url_address" name="internet" value="{{ (old('internet') != "") ? old('internet') : isset($data->data->internet) ? $data->data->internet : null }}" placeholder="http://" >
                                        @if ($errors->has('internet'))
                                            <span class="help-block">
                                        <strong>{{ $errors->first('internet') }}</strong>
                                    </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                                    <label for="description" class="col-md-4 control-label">Popis podnikania</label>
                                    <div class="col-md-8">
                                        <textarea id="description" class="form-control" name="description" rows="4">{{ (old('description') != "") ? old('description') : isset($data->data->description) ? $data->data->description : null }}</textarea>
                                        @if ($errors->has('description'))
                                            <span class="help-block">
                                        <strong>{{ $errors->first('description') }}</strong>
                                    </span>
                                        @endif
                                    </div>
                                </div>
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
                                <H3 class="m-b text-primary">2. Informácie o členstve</H3>
                                <div class="form-group{{ $errors->has('club') ? ' has-error' : '' }}">
                                    <label for="club" class="col-md-4 control-label">Klub</label>
                                    <div class="col-md-4">
                                        <select class="form-control" name="club" class="form-control" required>
                                            <option value="">-- výber --</option>
                                            @foreach($clubs as $c)
                                                <option value="{{$c->id}}" @if(old('club') == $c->id ) selected="selected" @elseif($data->data->club == $c->id) selected="selected" @endif>{{$c->short_title}} ({{$c->address_city}})</option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('club'))
                                            <span class="help-block">
                                        <strong>{{ $errors->first('club') }}</strong>
                                    </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group{{ $errors->has('date_create') ? ' has-error' : '' }} date_picker_year" >
                                    <label for="date_create" class="col-md-4 control-label">Dátum zahájenia</label>
                                    <div class="col-md-3">
                                        <div class="input-group date">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                            <input type="text" class="form-control date_mask" value="{{ isset($data->data->date_create) ? Carbon\Carbon::createFromDate(explode(".",$data->data->date_create)[2],explode(".",$data->data->date_create)[1],explode(".",$data->data->date_create)[0])->format('d.m.Y') : Carbon\Carbon::now()->format('d.m.Y') }}" name="date_create" >
                                        </div>
                                        @if ($errors->has('date_create'))
                                            <span class="help-block">
                                        <strong>{{ $errors->first('date_create') }}</strong>
                                    </span>
                                        @endif
                                    </div>
                                </div>
                                <H3 class="m-b">3. Fakturačné údaje</H3>
                                <hr>
                                <div class="form-group{{ $errors->has('company_name') ? ' has-error' : '' }}">
                                    <label for="company_name" class="col-md-4 control-label">Obchodný názov/spoločnosť</label>
                                    <div class="col-md-8">
                                        <input id="company_name" type="text" class="form-control" name="company_name" value="{{ (old('company_name') != "") ? old('company_name') : isset($data->data->company_name) ? $data->data->company_name : null }}" data-slovensko-digital-autoform="name" required autofocus>
                                        @if ($errors->has('company_name'))
                                            <span class="help-block">
                                        <strong>{{ $errors->first('company_name') }}</strong>
                                    </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group{{ $errors->has('ico') ? ' has-error' : '' }}">
                                    <label for="ico" class="col-md-4 control-label">IČO</label>
                                    <div class="col-md-3">
                                        <input id="ico" type="text" class="form-control" name="ico" value="{{ (old('ico') != "") ? old('ico') : isset($data->data->ico) ? $data->data->ico : null }}" maxlength="10" data-slovensko-digital-autoform="cin" required>
                                        @if ($errors->has('ico'))
                                            <span class="help-block">
                                        <strong>{{ $errors->first('ico') }}</strong>
                                    </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group{{ $errors->has('dic') ? ' has-error' : '' }}">
                                    <label for="dic" class="col-md-4 control-label">DIČ</label>
                                    <div class="col-md-3">
                                        <input id="dic" type="text" class="form-control" name="dic" value="{{ (old('dic') != "") ? old('dic') : isset($data->data->dic) ? $data->data->dic : null }}"  maxlength="12" data-slovensko-digital-autoform="tin">
                                        @if ($errors->has('dic'))
                                            <span class="help-block">
                                        <strong>{{ $errors->first('dic') }}</strong>
                                    </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group{{ $errors->has('ic_dph') ? ' has-error' : '' }}">
                                    <label for="ic_dph" class="col-md-4 control-label">Platiteľ DPH</label>
                                    <div class="col-md-6">
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
                                    <div class="col-md-3">
                                        <input id="ic_dph" type="text" class="form-control copy_text_vat" name="ic_dph" value="{{ (old('ic_dph') != "") ? old('ic_dph') : isset($data->data->ic_dph) ? $data->data->ic_dph : null }}"  maxlength="15" data-slovensko-digital-autoform="vatin"  required>
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
                                    <div class="col-md-8">
                                        <input id="registration" type="text" class="form-control copy_text_function" name="registration" value="{{ (old('registration') != "") ? old('registration') : isset($data->data->registration) ? $data->data->registration : null }}" maxlength="50">
                                        @if ($errors->has('registration'))
                                            <span class="help-block">
                                        <strong>{{ $errors->first('registration') }}</strong>
                                    </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group{{ $errors->has('address_street') ? ' has-error' : '' }}">
                                    <label for="address_street" class="col-md-4 control-label">Ulica</label>
                                    <div class="col-md-6">
                                        <input id="address_street" type="text" class="form-control copy_text_function" name="address_street" value="{{ (old('address_street') != "") ? old('address_street') : isset($data->data->address_street) ? $data->data->address_street : null }}"  maxlength="50"  data-slovensko-digital-autoform="formatted-street" required>
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
                                        <input id="address_psc" type="text" class="form-control" name="address_psc" value="{{ (old('address_psc') != "") ? old('address_psc') : isset($data->data->address_psc) ? $data->data->address_psc : null }}"  maxlength="8"  data-slovensko-digital-autoform="postal-code" required>
                                        @if ($errors->has('address_psc'))
                                            <span class="help-block">
                                        <strong>{{ $errors->first('address_psc') }}</strong>
                                    </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group{{ $errors->has('address_city') ? ' has-error' : '' }}">
                                    <label for="address_city" class="col-md-4 control-label">Mesto</label>
                                    <div class="col-md-6">
                                        <input id="address_city" type="text" class="form-control" name="address_city" value="{{ (old('address_city') != "") ? old('address_city') : isset($data->data->address_city) ? $data->data->address_city : null }}"  maxlength="50"  data-slovensko-digital-autoform="municipality"  required>
                                        @if ($errors->has('address_city'))
                                            <span class="help-block">
                                        <strong>{{ $errors->first('address_city') }}</strong>
                                    </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group{{ $errors->has('address_country') ? ' has-error' : '' }}">
                                    <label for="address_country" class="col-md-4 control-label">Štát</label>
                                    <div class="col-md-6">
                                        <select id="address_country" class="chosen-select"  class="form-control" name="address_country"  required>
                                            @foreach($countries as $country)
                                                <option value='{{$country->name}} @if(old('address_country') == $country->name ) selected="selected" @elseif($data->data->address_country == $country->name) selected="selected" @endif'>{{$country->name}}</option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('address_country'))
                                            <span class="help-block">
                                        <strong>{{ $errors->first('address_country') }}</strong>
                                    </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group{{ $errors->has('contact_person') ? ' has-error' : '' }}">
                                    <label for="contact_person" class="col-md-4 control-label">Kontaktná osoba</label>
                                    <div class="col-md-6">
                                        <input id="contact_person" type="search" class="form-control" name="contact_person" value="{{ (old('contact_person') != "") ? old('contact_person') : isset($data->data->contact_person) ? $data->data->contact_person : null }}"  maxlength="50" required>
                                        @if ($errors->has('contact_person'))
                                            <span class="help-block">
                                        <strong>{{ $errors->first('contact_person') }}</strong>
                                    </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                    <label for="email" class="col-md-4 control-label">Kontaktný E-Mail</label>
                                    <div class="col-md-6">
                                        <input id="email" type="email" class="form-control" name="email" value="{{ (old('email') != "") ? old('email') : isset($data->data->email) ? $data->data->email : null }}" required>

                                        @if ($errors->has('email'))
                                            <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group{{ $errors->has('phone') ? ' has-error' : '' }}">
                                    <label for="phone" class="col-md-4 control-label">Kontaktný telefón</label>
                                    <div class="col-md-6">
                                        <input id="phone" type="text" class="form-control" name="phone" value="{{ (old('phone') != "") ? old('phone') : isset($data->data->phone) ? "+421".$data->data->phone : null }}" placeholder="+421" required>

                                        @if ($errors->has('phone'))
                                            <span class="help-block">
                                        <strong>{{ $errors->first('phone') }}</strong>
                                    </span>
                                        @endif
                                    </div>
                                </div>

                                <hr>
                                <H3 class="m-b">4. Osobné údaje člena</H3>
                                <hr>
                                <div class="form-group {{ $errors->has('gender') ? ' has-error' : '' }}">
                                    <label class="col-sm-4 control-label">Pohlavie</label>
                                    <div class="col-sm-5 inline">
                                        <div class="col-md-4">
                                            <input type="hidden" name="gender" value="">
                                            <input type="radio" name="gender" id="M" value="M" class="form-control"  @if(isset($user->gender) && $user->gender == "M" ) checked="checked" @elseif(isset($data->data->gender) && $data->data->gender == "M") checked="checked"  @endif>
                                            <label for="gender" class="text-normal">
                                                Muž
                                            </label>
                                        </div>
                                        <div class="col-md-4">
                                            <input type="radio" name="gender" id="F" value="F" class="form-control"  @if(isset($user->gender) && $user->gender == "F" ) checked="checked" @elseif(isset($data->data->gender) && $data->data->gender == "F") checked="checked"  @endif>
                                            <label for="gender" class="text-normal">
                                                Žena
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group{{ $errors->has('title_before') ? ' has-error' : '' }}">
                                    <label for="title_before" class="col-md-4 control-label">Titul pred menom</label>
                                    <div class="col-md-2">
                                        <input id="title_before" type="text" maxlength="10" class="form-control" name="title_before" value="{{ (old('title_before') != "") ? old('title_before') : isset($data->data->title_before) ? $data->data->title_before : null }}" autofocus>

                                        @if ($errors->has('title_before'))
                                            <span class="help-block">
                                        <strong>{{ $errors->first('title_before') }}</strong>
                                    </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                    <label for="name" class="col-md-4 control-label">Meno</label>
                                    <div class="col-md-6">
                                        <input id="name" type="text" class="form-control" name="name" value="{{ (old('name') != "") ? old('name') : isset($data->data->name) ? $data->data->name : null }}" required>

                                        @if ($errors->has('name'))
                                            <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group{{ $errors->has('surname') ? ' has-error' : '' }}">
                                    <label for="surname" class="col-md-4 control-label">Priezvisko</label>
                                    <div class="col-md-6">
                                        <input id="surname" type="text" class="form-control" name="surname" value="{{ (old('surname') != "") ? old('surname') : isset($data->data->surname) ? $data->data->surname : null }}" required>
                                        @if ($errors->has('surname'))
                                            <span class="help-block">
                                        <strong>{{ $errors->first('surname') }}</strong>
                                    </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group{{ $errors->has('title_after') ? ' has-error' : '' }}">
                                    <label for="title_after" class="col-md-4 control-label">Titul za menom</label>
                                    <div class="col-md-2">
                                        <input id="title_after" type="text" maxlength="10" class="form-control" name="title_after" value="{{ (old('title_after') != "") ? old('title_after') : isset($data->data->title_after) ? $data->data->title_after : null }}">

                                        @if ($errors->has('title_after'))
                                            <span class="help-block">
                                        <strong>{{ $errors->first('title_after') }}</strong>
                                    </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group{{ $errors->has('date_birth') ? ' has-error' : '' }} date_picker_year" >
                                    <label for="date_birth" class="col-md-4 control-label">Dátum narodenia</label>
                                    <div class="col-md-3">
                                        <div class="input-group date">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                            <input type="text" class="form-control date_mask" value="{{ isset($data->data->date_birth) ? Carbon\Carbon::createFromDate(explode(".",$data->data->date_birth)[2],explode(".",$data->data->date_birth)[1],explode(".",$data->data->date_birth)[0])->format('d.m.Y') : Carbon\Carbon::now()->format('d.m.Y') }}" name="date_birth" >
                                        </div>
                                        @if ($errors->has('date_create'))
                                            <span class="help-block">
                                        <strong>{{ $errors->first('date_birth') }}</strong>
                                    </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group{{ $errors->has('interest') ? ' has-error' : '' }}">
                                    <label for="interest" class="col-md-4 control-label">Záujmy užívateľa</label>
                                    <div class="col-md-8">
                                        <select data-placeholder="Výber záujmov..." name="interest[]" class="chosen-select" style="width: 100%;" multiple  tabindex="4">
                                            @foreach($interest as $int)
                                                <option value="{{$int->id}}">
                                                    {{$int->name}}
                                                </option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('interest'))
                                            <span class="help-block">
                                        <strong>{{ $errors->first('interest') }}</strong>
                                    </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="title_after" class="col-md-4 control-label">Ročný poplatok</label>
                                    <div class="col-md-4">
                                        <div class="input-group m-b disabled">
                                            <span class="input-group-addon">EUR</span>
                                            <p class="form-control">{{env('MEMBERSHIP')}}</p>
                                            <span class="input-group-addon"> + DPH</span>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <h3>5. Prehlásenie žiadateľa o členstvo v klube</h3>
                                <div class="form-group">
                                    <div class="col-md-6 col-md-offset-4">
                                        <p>a) Rozumiem a súhlasím, že ako člen B4B som zaviazaný k účasti na mítingoch, ktoré sú organizované každé dva týždne, alebo pošlem za seba náhradníka, ktorý sa mítingu zúčastní v mojom zastúpení.</p>
                                        <p>b) Povediem všetky svoje podnikateľské aktivity poctivo, záväzne a čestne.</p>
                                        <p>c) Budem nasledovať každé odporúčanie a nové kontakty, ktoré obdržím a budem ich zaznamenávať na referenčné ústrižky a ich kópie odovzdám Výkonnému tímu klubu pre evidenciu.</p>
                                        <p>d) Dovoľujem B4B a členom B4B distribuovať moju vizitku alebo informácie o mojej spoločnosti za účelom propagácie môjho podniku.</p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-6 col-md-offset-4">
                                        <div class="checkbox">
                                            {{--<input type="hidden" name="accept_condition" value="0">--}}
                                            <input id="accept_condition" type="checkbox" class="form-control" name="accept_condition" value="1" @if(isset($data->data->accept_condition) && $data->data->accept_condition == "1") checked disabled @endif>
                                            <label for="checkbox">
                                                Zaškrtnutím súhlasím s podmienkami členstva.
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-md-6 col-md-offset-4">
                                        <button type="submit" class="btn btn-success">
                                            Prijať Prihlášku
                                        </button>
                                    </div>

                                    <div class="col-md-6 col-md-offset-4">
                                        <button type="submit" class="btn btn-danger">
                                            Zamietnuť prihlášku
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

@endsection
