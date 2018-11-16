@extends('layouts.app')

@section('title', 'Editácia klubu')

@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title"></div>
                    <div class="ibox-content">

                        @include('components.validation')

                        <form id="user_form" class="form-horizontal" method="POST" action="{{ route('setting.club.update', ['id' =>  $club->id]) }}" enctype="multipart/form-data" >
                            {{ csrf_field() }}
                            {{ method_field('PUT') }}

                            <div class="form-group{{ $errors->has('active') ? ' has-error' : '' }}">
                                <label for="active" class="col-md-4 control-label">Aktivny</label>
                                <div class="col-md-4">
                                    <div class="checkbox">
                                        <input type="hidden"  value="0" name="active">
                                        <input id="active" type="checkbox" class="form-control" value="1" name="active" @if($club->active == 1) checked="checked"  @endif>
                                        <label for="checkbox1">
                                            Zaškrtnúť ak je club aktívny
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('franchisor_id') ? ' has-error' : '' }}">
                                <label for="franchisor_id" class="col-md-4 control-label">Franšízant</label>
                                <div class="col-md-2">
                                    <select id="franchisor_id" class="form-control" name="franchisor_id"  required>
                                        <option value="">-- výber --</option>
                                        @foreach( $franchisors as $key => $f)
                                            <option value="{{$f->id}}" @if($f->id == $club->franchisor_id) selected="selected" @endif>{{$f->company->company_name}}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('franchisor_id'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('franchisor_id') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
                                <label for="title" class="col-md-4 control-label">Názov klubu</label>
                                <div class="col-md-4">
                                    <input id="title" type="text" class="form-control" name="title" value="{{ $club->title }}"  required autofocus>
                                    @if ($errors->has('title'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('title') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('short_title') ? ' has-error' : '' }}">
                                <label for="ico" class="col-md-4 control-label">Skratka klubu</label>
                                <div class="col-md-2">
                                    <input id="short_title" type="text" class="form-control" name="short_title" value="{{ $club->short_title  }}" maxlength="20" required>
                                    @if ($errors->has('short_title'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('short_title') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <hr>
                            <div class="form-group{{ $errors->has('host_name') ? ' has-error' : '' }}">
                                <label for="host_name" class="col-md-4 control-label">Sídlo (hotel/reštaurácia)</label>
                                <div class="col-md-4">
                                    <input id="host_name" type="text" class="form-control" name="host_name" value="{{ $club->host_name  }}" maxlength="50"  required>
                                    @if ($errors->has('host_name'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('host_name') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>


                            <div class="form-group{{ $errors->has('time_from') ? ' has-error' : '' }} " >
                                <label for="time_from" class="col-md-4 control-label">Raňajky od</label>
                                <div class="col-md-2">
                                    <div class="input-group clockpicker" data-autoclose="true">
                                        <input type="time" class="form-control clock" value="{{$club->time_from }}" name="time_from" >
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
                                <label for="time_to" class="col-md-4 control-label">Raňajky do</label>
                                <div class="col-md-2">
                                    <div class="input-group clockpicker" data-autoclose="true">
                                        <input type="time" class="form-control clock" value="{{$club->time_to}}" name="time_to" >
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
                            <div class="form-group{{ $errors->has('repeat_day') ? ' has-error' : '' }}">
                                <label for="repeat_day" class="col-md-4 control-label">Deň v týždni</label>
                                <div class="col-md-2">
                                    <select id="repeat_day" class="form-control" name="repeat_day"  required>
                                        <option value="">-- výber --</option>
                                    @foreach(__('constant.week_name') as $key => $day)
                                            <option value="{{$key}}"  @if($club->repeat_day == $key) selected="selected" @endif >{{$day}}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('address_country'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('repeat_day') }}</strong>
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
                                            <option value="{{$key}}" @if($club->repeat_interval == $key) selected="selected" @endif >{{$day}}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('repeat_interval'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('repeat_interval') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('price') ? ' has-error' : '' }}">
                                <label for="address_street" class="col-md-4 control-label">Cena raňajok €</label>
                                <div class="col-md-2">
                                    <input id="price" type="number" class="form-control" name="price" value="{{ $club->price }}"  maxlength="10" required>
                                    @if ($errors->has('price'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('price') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('address_street') ? ' has-error' : '' }}">
                                <label for="address_street" class="col-md-4 control-label">Ulica</label>
                                <div class="col-md-4">
                                    <input id="address_street" type="text" class="form-control" name="address_street" value="{{ $club->address_street  }}"  maxlength="50" required>
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
                                    <input id="address_psc" type="text" class="form-control" name="address_psc" value="{{ $club->address_psc }}"  maxlength="8"  required>
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
                                    <input id="address_city" type="text" class="form-control" name="address_city" value="{{ $club->address_city }}"  maxlength="50"  required>
                                    @if ($errors->has('address_city'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('address_city') }}</strong>
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
                                            <option value="{{$v->id_district}}" @if($club->district_id == $v->id_district) selected="selected" @endif>{{$v->name}}</option>
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
                                        @foreach( $counties as $k => $v)
                                            @if($v->id_district == $club->district_id)
                                            <option value="{{$v->id_county}}" @if($club->county_id == $v->id_county) selected="selected" @endif>{{$v->name}}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                    @if ($errors->has('county'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('county') }}</strong>
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
                                                    <option value="{{$country->name}}" @if($club->address_country == $country->name) selected="selected" @endif >{{$country->name}}</option>
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
                            <div class="form-group{{ $errors->has('host_url') ? ' has-error' : '' }}">
                                <label for="host_url" class="col-md-4 control-label">Internet</label>
                                <div class="col-md-4">
                                    <input id="host_url" type="url" class="form-control" name="host_url" value="{{ $club->host_url  }}" placeholder="http://" required>
                                    @if ($errors->has('host_url'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('host_url') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                                <label for="description" class="col-md-4 control-label">Popis o kluba</label>
                                <div class="col-md-6">
                                    <textarea id="description" class="form-control" name="description" rows="6">{{ $club->description  }}</textarea>
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
                                    <input type="hidden" id="lat" name="lat" value="{{ $club->lat }}">
                                    <input type="hidden" id="lng" name="lng" value="{{ $club->lng }}">
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
                                    <textarea id="address_description" class="form-control" name="address_description" rows="4">{{ $club->address_description  }}</textarea>
                                    @if ($errors->has('address_description'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('address_description') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <hr>
                            <div class="form-group">
                                <label for="password_confirmation" class="col-md-4 control-label">Titulný obrázok klubu</label>
                                <div class="col-md-4">
                                    <div class="fileinput fileinput-new" data-provides="fileinput">
                                        <div class="fileinput-new thumbnail" style="max-width: 500px; max-height: 500px;">
                                            @if(file_exists('images/club/' . $club->id . '/' .$club->image))
                                                <img data-src="holder.js/100%x100%"  alt="..." src="{!! asset('images/club') !!}/{{$club->id}}/{!! $club->image !!}">
                                            @else
                                                <p class="text-center m-t-md">
                                                    <i class="fa fa-upload big-icon"></i>
                                                </p>
                                            @endif
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

@section('page_css')

    <link rel="stylesheet" href="{!! asset('css/plugins/clockpicker/clockpicker.css') !!}" />

    <link rel="stylesheet" href="{!! asset('css/plugins/dropzone/basic.css') !!}" />
    <link rel="stylesheet" href="{!! asset('css/plugins/dropzone/dropzone.css') !!}" />

@endsection


@section('scripts')

    <!-- Clock picker -->
    <script src="{!! asset('js/plugins/clockpicker/clockpicker.js') !!}" type="text/javascript"></script>
    <!-- DROPZONE -->
    <script src="{!! asset('js/plugins/dropzone/dropzone.js') !!}" type="text/javascript"></script>


    <!-- GOOGLE -->
    <script src="//maps.googleapis.com/maps/api/js?key={!! env('GOOGLE_MAP_API') !!}&libraries=places"></script>

    <script>
        jQuery(window).ready(function (){

            $('#phone').mask('+000000000000');
            $('.clock').mask('00:00');

            $('.clockpicker').clockpicker();


            $("#user_form").validate({
                rules: {
                    phone: {
                        required: true,
                        minlength: 13
                    },
                    password: "required",
                    password_confirmation: {
                        equalTo: "#password",
                        minlength: 8
                    }
                }
            });

            $('#host_url').keyup(function () {
                if (  ($(this).val().length >=5)
                    && ($(this).val().substr(0, 5) != 'http:')
                    && ($(this).val().substr(0, 5) != 'https') ) {
                    $(this).val('http://' + $(this).val());
                }
            });

            var geocoder = new google.maps.Geocoder();
            var marker = null;
            var map = null;
            var lat = @if($club->lat > 0){!! $club->lat !!}@else 0 @endif;
            var lng = @if($club->lng > 0){!! $club->lng !!}@else 0 @endif;
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


    {{--{{ dump(env()) }}--}}


@endsection