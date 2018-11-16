@extends('layouts.app')

@section('title', 'Pridanie novej udalosti')

@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title"></div>
                    <div class="ibox-content">

                        @include('components.validation')

                        <form id="event_form" class="form-horizontal" method="POST" action="{{ route('events.listing.store') }}" enctype="multipart/form-data" >
                            {{ csrf_field() }}

                            <div class="form-group{{ $errors->has('active') ? ' has-error' : '' }}">
                                <label for="active" class="col-md-4 control-label">Aktivna udalosť</label>
                                <div class="col-md-4">
                                    <div class="checkbox">
                                        <input type="hidden"  value="0" name="active">
                                        <input id="active" type="checkbox" class="form-control" value="1" name="active" >
                                        <label for="checkbox1">
                                            Zaškrtnúť ak je udalosť aktívna
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('event_type') ? ' has-error' : '' }}">
                                <label for="event_type" class="col-md-4 control-label">Typ udalosti</label>
                                <div class="col-md-4">
                                    <select id="event_type" class="form-control" name="event_type"  required>
                                        <option value="">-- výber --</option>
                                        @foreach( $event_types as $et)
                                            <option value="{{$et->id}}">{{$et->name}}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('event_type'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('event_type') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('club_id') ? ' has-error' : '' }}">
                                <label for="club_id" class="col-md-4 control-label">Organizátor (Klub)</label>
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <select id="club_id" class="chosen-select-width form-control" name="club_id"  required>
                                            <option value="">-- výber --</option>
                                            @foreach($clubs as $c)
                                                <option value="{{$c->id}}"
                                                        data-host_name="{{$c->host_name}}"
                                                > {{$c->title}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @if ($errors->has('club_id'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('club_id') }}</strong>
                                    </span>
                                    @endif
                                </div>

                            </div>
                            <hr>
                            <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
                                <label for="title" class="col-md-4 control-label">Názov udalosti</label>
                                <div class="col-md-4">
                                    <input id="title" type="text" class="form-control" name="title" value="{{ old('title') }}"  required autofocus>
                                    @if ($errors->has('title'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('title') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('active') ? ' has-error' : '' }}">
                                <label for="active" class="col-md-4 control-label">Opakovaná udalosť</label>
                                <div class="col-md-4">
                                    <div class="checkbox">
                                        <input type="hidden"  value="0" name="repeat_event_check">
                                        <input id="repeat_event_check" type="checkbox" class="form-control" value="1" name="repeat_event_check">
                                        <label for="checkbox1">
                                            Zaškrtnúť ak chcete vytvoriť viac udalostí
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="hide alert" id="repeat_block">
                                <div class="form-group{{ $errors->has('repeat_count') ? ' has-error' : '' }}">
                                    <label for="repeat_count" class="col-md-4 control-label">Počet opakovaní</label>
                                    <div class="col-md-2">
                                        <select id="repeat_count" class="form-control" name="repeat_count"  required>
                                            @for ($i = 2; $i <= 5; $i++)
                                                <option value="{{$i}}">{{$i}}</option>
                                            @endfor
                                        </select>
                                        @if ($errors->has('repeat_count'))
                                            <span class="help-block">
                                        <strong>{{ $errors->first('repeat_count') }}</strong>
                                    </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group{{ $errors->has('repeat_interval') ? ' has-error' : '' }}">
                                    <label for="repeat_interval" class="col-md-4 control-label">Interval opakovania</label>
                                    <div class="col-md-2">
                                        <select id="repeat_interval" class="form-control" name="repeat_interval"  required>
                                            <option value="">-- výber --</option>
                                            @foreach(__('constant.repeat_interval') as $key => $day)
                                                <option value="{{$key}}" >{{$day}}</option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('repeat_interval'))
                                            <span class="help-block">
                                        <strong>{{ $errors->first('repeat_interval') }}</strong>
                                    </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('host_name') ? ' has-error' : '' }}">
                                <label for="host_name" class="col-md-4 control-label">Miesto konania (hotel/reštaurácia)</label>
                                <div class="col-md-4">
                                    <input id="host_name" type="text" class="form-control" name="host_name" value="{{ old('host_name') }}" maxlength="50"  required>
                                    @if ($errors->has('host_name'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('host_name') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('date_create') ? ' has-error' : '' }} date_picker_year" >
                                <label for="date_create" class="col-md-4 control-label">Deň konania</label>
                                <div class="col-md-3">
                                    <h5 class="text-info " id="date_last_event"></h5>
                                    <div class="input-group date">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input id="date_create" type="text" class="form-control date_mask" value="{{\Carbon\Carbon::now()->format('d.m.Y')}}" name="date_create" >
                                    </div>
                                    @if ($errors->has('date_create'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('date_create') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('time_from') ? ' has-error' : '' }} " >
                                <label for="time_from" class="col-md-4 control-label">Začiatok</label>
                                <div class="col-md-2">
                                    <div class="input-group clockpicker" data-autoclose="true">
                                        <input id="time_from" type="time" class="form-control clock" value="09:00" name="time_from" >
                                        <span class="input-group-addon">
                                    <span class="fa fa-clock-o"></span>
                                </span>
                                    </div>
                                    @if ($errors->has('time_from'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('time_from') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('time_to') ? ' has-error' : '' }} " >
                                <label for="time_to" class="col-md-4 control-label">Koniec</label>
                                <div class="col-md-2">
                                    <div class="input-group clockpicker" data-autoclose="true">
                                        <input id="time_to" type="time" class="form-control clock" value="16:00" name="time_to" >
                                        <span class="input-group-addon">
                                    <span class="fa fa-clock-o"></span>
                                </span>
                                    </div>
                                    @if ($errors->has('time_to'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('time_to') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('capacity') ? ' has-error' : '' }}">
                                <label for="capacity" class="col-md-4 control-label">Kapacita</label>
                                <div class="col-md-2">
                                    <input id="capacity" type="number" class="form-control" name="capacity" value="0"  maxlength="5" required>
                                    @if ($errors->has('price'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('capacity') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('price') ? ' has-error' : '' }}">
                                <label for="price" class="col-md-4 control-label">Cena €</label>
                                <div class="col-md-2">
                                    <input id="price" type="number" class="form-control" name="price" value=""  maxlength="10" required>
                                    @if ($errors->has('price'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('price') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('district') ? ' has-error' : '' }}">
                                <label for="district" class="col-md-4 control-label">Kraj</label>
                                <div class="col-md-2">
                                    <select id="district" class="form-control select_district" name="district_id"  required>
                                        <option value="">-- výber --</option>
                                        @foreach( $districts as $k => $v)
                                            <option value="{{$v->id_district}}">{{$v->name}}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('district'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('district') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('county') ? ' has-error' : '' }}">
                                <label for="county" class="col-md-4 control-label">Okres</label>
                                <div class="col-md-2">
                                    <select id="county" class="form-control select_county" name="county_id"  required>
                                        <option value="">-- výber --</option>
                                    </select>
                                    @if ($errors->has('county'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('county') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('address_city') ? ' has-error' : '' }}">
                                <label for="address_city" class="col-md-4 control-label">Mesto</label>
                                <div class="col-md-4">
                                    <input id="address_city" type="text" class="form-control" name="address_city" value="{{ old('address_city') }}"  maxlength="50"  required>
                                    @if ($errors->has('address_city'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('address_city') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('address_street') ? ' has-error' : '' }}">
                                <label for="address_street" class="col-md-4 control-label">Ulica</label>
                                <div class="col-md-4">
                                    <input id="address_street" type="text" class="form-control" name="address_street" value="{{ old('addess_streetr')  }}"  maxlength="50" required>
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
                                    <input id="address_psc" type="text" class="form-control" name="address_psc" value="{{ old('address_psc') }}"  maxlength="8"  required>
                                    @if ($errors->has('address_psc'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('address_psc') }}</strong>
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
                            <div class="form-group{{ $errors->has('url') ? ' has-error' : '' }}">
                                <label for="url" class="col-md-4 control-label">Internet</label>
                                <div class="col-md-4">
                                    <input id="url" type="url" class="form-control url_address" name="url" value="{{ old('url')  }}" placeholder="http://" required>
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
                            <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                                <label for="" class="col-md-4 control-label">Adresa</label>
                                <div class="col-md-6">
                                    <input id="pac-input" class="controls form-control" type="text" placeholder="Hľadaj adresu">
                                    <input type="hidden" id="lat" name="lat" value="">
                                    <input type="hidden" id="lng" name="lng" value="">
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                                <label for="description" class="col-md-4 control-label">Mapa</label>
                                <div class="col-md-6">
                                    <div id="map-canvas" style="height: 400px;"></div>
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('address_description') ? ' has-error' : '' }}">
                                <label for="address_description" class="col-md-4 control-label">Info o adrese (ako sa k nám dostanete)</label>
                                <div class="col-md-6">
                                    <textarea id="address_description" class="form-control" name="address_description" rows="4"></textarea>
                                    @if ($errors->has('address_description'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('address_description') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <hr>
                            <div class="form-group">
                                <label for="password_confirmation" class="col-md-4 control-label">Titulný obrázok udalosti</label>
                                <div class="col-md-4">
                                    <div class="fileinput fileinput-new" data-provides="fileinput">
                                        <div class="fileinput-new thumbnail" style="width: 200px; height: 200px;">
                                            <p class="text-center m-t-md">
                                                <i class="fa fa-upload big-icon"></i>
                                            </p>
                                        </div>
                                        <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 500px; max-height: 500px;"></div>
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

@section('page_css')

    <link rel="stylesheet" href="{!! asset('css/plugins/clockpicker/clockpicker.css') !!}" />

    <link rel="stylesheet" href="{!! asset('css/plugins/dropzone/basic.css') !!}" />
    <link rel="stylesheet" href="{!! asset('css/plugins/dropzone/dropzone.css') !!}" />

@endsection


@section('scripts')

    <!-- Clock picker -->
    <script src="{!! asset('js/plugins/moment/moment.min.js') !!}" type="text/javascript"></script>

    <script src="{!! asset('js/plugins/clockpicker/clockpicker.js') !!}" type="text/javascript"></script>

    <!-- DROPZONE -->
    <script src="{!! asset('js/plugins/dropzone/dropzone.js') !!}" type="text/javascript"></script>

    <!-- GOOGLE -->
    <script src="//maps.googleapis.com/maps/api/js?key={!! env('GOOGLE_MAP_API') !!}&libraries=places"></script>

    <script>
        jQuery(window).ready(function (){

            $('#event_type').change(function(){

                if($(this).find('option:selected').val() != "") {
                    $("#title").val($(this).find('option:selected').text());
                }else{
                    $("#title").val("");
                }
            });


            $("#club_id").chosen().change(function(){
                var id = $(this).val();
                var data = $("option:selected", this).data();

                if(id == "") {

                    $("#address_street").val("");
                    $("#address_psc").val("");
                    $("#address_city").val("");
                    $("#host_name").val("");
                    $("#url").val("");
                    $("#lat").val("");
                    $("#lng").val("");
                    $("#repeat_interval").val("");
                    $("#time_from").val("");
                    $("#time_to").val("");
                    $("#price").val("0");
                    $(".select_district").val("").trigger('change');
                    $('#county').val("");
                    $('#address_description').val("");

                }else if(id == 0){

                    console.log('franchisor');

                }else{

                    $.ajax({
                        type: 'POST',
                        url: '/ajax/get-club-data',
                        data: {club_id: id},
                        dataType: 'json',
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        success: function (ajax) {

                            if (ajax.status == 'true') {

                                var time_from = ajax.data.time_from;
                                var time_to = ajax.data.time_to;

                                $("#address_street").val(ajax.data.address_street);
                                $("#address_psc").val(ajax.data.address_psc);
                                $("#address_city").val(ajax.data.address_city);
                                $("#host_name").val(ajax.data.host_name);
                                $("#url").val(ajax.data.host_url);
                                $("#lat").val(ajax.data.lat);
                                $("#lng").val(ajax.data.lng);
                                $("#repeat_interval").val(ajax.data.repeat_interval);
                                $("#time_from").val(time_from.substring(0,5));
                                $("#time_to").val(time_to.substring(0,5));
                                $("#price").val(ajax.data.price);
                                $("#address_description").val(ajax.data.address_description);


                                //ak je vybrana udalost ranajky nastavime datum
                                if($('#event_type').val() == 1 ){
                                    $('#date_last_event').html('Posledná udalosť klubu: ' + ajax.data.last_event_date);
                                    var new_date = moment(ajax.data.last_event_date, "DD-MM-YYYY").add(ajax.data.repeat_interval, 'days');

                                    $("#date_create").datepicker({
                                        format: 'dd.mm.yyyy',
                                        autoclose: true
                                    }).datepicker("update", new_date.format('DD.MM.YYYY'));
                                }

                                $(".select_district").val(ajax.data.district_id).trigger('change');
                                //$('.select_district').trigger('change');
                                delay(function(){
                                    $('#county').val(ajax.data.county_id);

                                    var txtAddress = ajax.data.address_street + ", " + ajax.data.address_psc + ", " + ajax.data.address_city;
                                    if(txtAddress.length > 15){
                                        getCoordinatesAddress(txtAddress);
                                        $("#pac-input").val(txtAddress);
                                    }
                                }, 1000 )

                            } else {
                                console.log('ajax error');
                            }

                        }
                    });

                }

            });

            $('#phone').mask('+000000000000');
            $('.clock').mask('00:00');

            $('.clockpicker').clockpicker();

            $('#host_url').keyup(function () {
                if (  ($(this).val().length >=5)
                    && ($(this).val().substr(0, 5) != 'http:')
                    && ($(this).val().substr(0, 5) != 'https') ) {
                    $(this).val('http://' + $(this).val());
                }
            });

            $("#repeat_event_check").click(function(){
                if ($("#repeat_event_check").is(":checked")){
                    $("#repeat_block").removeClass('hide').addClass('show');
                }else{
                    $("#repeat_block").removeClass('show').addClass('hide');
                }
            });

            $.validator.setDefaults({ ignore: ":hidden:not(.chosen-select)" });

            $("#event_form").validate({
                rules: {
                    phone: {
                        required: true,
                        minlength: 13
                    },
                    time_from: {
                        time: "required time"
                    },
                    time_to: {
                        time: "required time"
                    },
                    password: "required",
                    password_confirmation: {
                        equalTo: "#password",
                        minlength: 8
                    },
                    repeat_count: {
                        required: {
                            depends: function () {
                                return $('#repeat_event_check').is(':checked')
                            }
                        }
                    },
                    repeat_interval: {
                        required: {
                            depends: function () {
                                return $('#repeat_event_check').is(':checked')
                            }
                        }
                    }
                }
            });


            var geocoder = new google.maps.Geocoder();
            var marker = null;
            var map = null;
            var lat = 48.14339275;
            var lng = 17.10681249;
            var center = new google.maps.LatLng(lat,lng);
            var placeLatLng = center;

            function initMap() {

                var mapOptionsPickup = {
                    zoom: 13,
                    center: placeLatLng,
                    panControl: false,
                    zoomControl: true,
                    scaleControl: true,
                    mapTypeId: google.maps.MapTypeId.ROADMAP
                };

                // init map with marker
                map = new google.maps.Map(document.getElementById('map-canvas'), mapOptionsPickup);
                if (marker && marker.getMap) marker.setMap(map);
                marker = new google.maps.Marker({
                    position: placeLatLng,
                    map: map,
                    title: 'Potiahni ma!',
                    draggable: true
                });


                // autocomlite address
                var autocomplete = new google.maps.places.Autocomplete((document.getElementById('pac-input')),
                    {types: ['geocode']});
                autocomplete.addListener('place_changed', updateAddress);

                google.maps.event.addListener(marker, 'dragend', function(marker) {
                    var latLng = marker.latLng;
                    placeLatLng.value = latLng.lat();
                    placeLatLng.value = latLng.lng();

                    $('#lat').val(latLng.lat());
                    $('#lng').val(latLng.lng());

                });

            }

            function updateAddress() {
                var address = $("#pac-input").val();
                if (address.length == 0) {
                    toastr.options.positionClass = 'toast-top-full-width';
                    toastr.error('Nevyplnili ste adresu!');
                    return false;
                }
                getCoordinatesAddress(address);
            }

            function getCoordinatesAddress(address) {
                geocoder.geocode({ 'address': address }, function (results, status) {
                    if (status == google.maps.GeocoderStatus.OK) {
                        map.setCenter(results[0].geometry.location);
                        marker.setPosition(results[0].geometry.location);
                        //console.log(marker.getPosition().lat());
                        $('#lat').val(marker.getPosition().lat());
                        $('#lng').val(marker.getPosition().lng());

                    } else {
                        toastr.warning("Nie je možné nájsť adresu, z dôvodu: " + status);
                    }
                });
            };

            $("#address_street, #address_psc, #address_city").each(function(){
                $(this).on('blur keyup change click input', function () {
                    delay(function(){
                        var txtAddress = $('#address_street').val() + ", " + $('#address_psc').val() + ", " + $('#address_city').val();
                        if(txtAddress.length > 15){
                            getCoordinatesAddress(txtAddress);
                            $("#pac-input").val(txtAddress);
                        }

                    }, 1000 )
                });
            });

            var delay = (function(){
                var timer = 0;
                return function(callback, ms){
                    clearTimeout (timer);
                    timer = setTimeout(callback, ms);
                };
            })();

           initMap();

        });


    </script>

@endsection