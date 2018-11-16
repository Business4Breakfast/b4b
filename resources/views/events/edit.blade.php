@extends('layouts.app')

@section('title', 'Editácia udalosti')

@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title"></div>
                    <div class="ibox-content">

                        @include('components.validation')

                        <form id="event_form" class="form-horizontal" method="POST" action="{{ route('events.listing.update', ['id' =>  $event->id]) }}" enctype="multipart/form-data" >
                            {{ csrf_field() }}
                            {{ method_field('PUT') }}

                            @if($event->active < 2 )
                            <div class="form-group{{ $errors->has('active') ? ' has-error' : '' }}">
                                <label for="active" class="col-md-4 control-label">Aktivna udalosť</label>
                                <div class="col-md-4">
                                    <div class="checkbox">
                                        <input type="hidden"  value="0" name="active">
                                        <input id="active" type="checkbox" class="form-control" value="1" name="active" @if($event->active == 1) checked="checked"  @endif>
                                        <label for="checkbox1">
                                            Zaškrtnúť ak je udalosť aktívna
                                        </label>
                                    </div>
                                </div>
                            </div>
                            @endif
                            <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
                                <label for="title" class="col-md-4 control-label">Názov udalosti</label>
                                <div class="col-md-4">
                                    <input id="title" type="text" class="form-control" name="title" value="{{ $event->title }}"  required autofocus>
                                    @if ($errors->has('title'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('title') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <hr>
                            <div class="form-group{{ $errors->has('event_type') ? ' has-error' : '' }}">
                                <label for="event_type" class="col-md-4 control-label">Typ udalosti</label>
                                <div class="col-md-4">
                                    <select id="event_type" class="form-control" name="event_type"  required>
                                        <option value="">-- výber --</option>
                                        @foreach( $event_types as $et)
                                            <option value="{{$et->id}}" @if($et->id == $event->event_type) selected="selected" @endif >{{$et->name}}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('event_type'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('event_type') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('address_country') ? ' has-error' : '' }}">
                                <label for="address_country" class="col-md-4 control-label">Organizátor (Klub)</label>
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <select id="address_country" class="chosen-select form-control" name="club_id"  required>
                                            @foreach($clubs as $c)
                                                <option value="{{$c->id}}" @if($event->club_id == $c->id) selected="selected" @endif >{{$c->title}} @if($c->active == 1) (act) @endif</option>
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
                            <div class="form-group{{ $errors->has('host_name') ? ' has-error' : '' }}">
                                <label for="host_name" class="col-md-4 control-label">Miesto konania (hotel/reštaurácia)</label>
                                <div class="col-md-4">
                                    <input id="host_name" type="text" class="form-control" name="host_name" value="{{ $event->host_name  }}" maxlength="50"  required>
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
                                    <div class="input-group date">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>

                                        <input type="text" class="form-control date_mask" value="{{ Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $event->event_from)->format('d.m.Y') }}" name="date_create" >
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
                                        <input type="time" class="form-control clock" value="{{ Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $event->event_from)->format('H:i:s') }}" name="time_from" >
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
                                        <input type="time" class="form-control clock" value="{{ Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $event->event_to)->format('H:i:s') }}" name="time_to" >
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
                                    <input id="capacity" type="number" class="form-control" name="capacity" value="{{ $event->capacity }}"  maxlength="5" required>
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
                                    <input id="price" type="number" class="form-control" name="price" value="{{ $event->price }}"  maxlength="10" required>
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
                                    <select id="district" class="form-control select_district" name="district_id" required>
                                        <option value="">-- výber --</option>
                                        @foreach( $districts as $k => $v)
                                            <option value="{{$v->id_district}}" @if($event->district_id == $v->id_district) selected="selected" @endif>{{$v->name}}</option>
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
                                    <select id="county" class="form-control select_county" name="county_id" required>
                                        <option value="">-- výber --</option>
                                        @foreach( $counties as $k => $v)
                                            @if($v->id_district == $event->district_id)
                                                {{--<option value="{{$v->id_county}}" @if($event->county_id == $v->id_county) selected="selected" @endif>{{$v->name}}</option>--}}
                                            @endif
                                                <option value="{{$v->id_county}}" @if($event->county_id == $v->id_county) selected="selected" @endif>{{$v->name}}</option>
                                        @endforeach
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
                                    <input id="address_city" type="text" class="form-control" name="address_city" value="{{ $event->address_city }}"  maxlength="50"  required>
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
                                    <input id="address_street" type="text" class="form-control" name="address_street" value="{{ $event->address_street  }}"  maxlength="50" required>
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
                                    <input id="address_psc" type="text" class="form-control" name="address_psc" value="{{ $event->address_psc }}"  maxlength="8"  required>
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
                                        <select id="address_country" class="chosen-select form-control" name="address_country"  required>
                                                @foreach($countries as $country)
                                                    <option value="{{$country->name}}" @if($event->address_country == $country->name) selected="selected" @endif >{{$country->name}}</option>
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
                                    <input id="url" type="url" class="form-control url_address" name="url" value="{{ $event->url  }}" placeholder="http://" required>
                                    @if ($errors->has('url'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('url') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                                <label for="description" class="col-md-4 control-label">Popis (text pozvánka)</label>
                                <div class="col-md-6">
                                    <textarea id="description" class="form-control" name="description" rows="10">{{ $event->description  }}</textarea>
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
                                    <input type="hidden" id="lat" name="lat" value="{{ $event->lat }}">
                                    <input type="hidden" id="lng" name="lng" value="{{ $event->lng }}">
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
                                    <textarea id="address_description" class="form-control" name="address_description" rows="4">{{ $event->address_description  }}</textarea>
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
                                        <div class="fileinput-new thumbnail" style="max-width: 500px; max-height: 500px;">
                                            @if(file_exists('images/event/' . $event->id . '/' .$event->image))
                                                <img data-src="holder.js/100%x100%"  alt="..." src="{!! asset('images/event') !!}/{{$event->id}}/{!! $event->image !!}">
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

                            <div class="form-group">
                                <label class="col-sm-4 control-label">Priložené súbory</label>
                                <div class="col-sm-4">
                                    @if($files)
                                        @foreach($files as $f)
                                            <p id="listing_file_{{$f->id}}">
                                                <a href="{{route('manual.download.file', $f->download)}}" ><i class="fa fa-download"></i>  {{$f->short_file_name}}</a>
                                                    <i class="fa fa-times-circle text-danger delete_button" id="{{$f->id}}" data-id-delete="{{$f->id}}" ></i>
                                            </p>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Nahrať súbor <br /><small>Môžete vložiť viac súborov</small></label>
                                <div class="col-sm-4">
                                    <input id="btn_upload" type="file" name="attach_files[]"  data-dragdrop="true"
                                           multiple="multiple" class="filestyle" data-btnClass="btn-success" data-size="sm">
                                </div>
                            </div>
                            <hr>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Nastavenie Email šablóny</label>
                                <div class="col-sm-5">
                                    <div class="checkbox">
                                        <input type="hidden" name="email_text_custom" value="0">
                                        <input type="checkbox" name="email_text_custom" value="1"  id="email_text_custom" class="form-control"  @if($event->email_text_custom )checked="checked" @endif>
                                        <label for="checkbox1">
                                            Nozobrazovať preddefinované texty
                                        </label>
                                    </div>
                                    <div class="checkbox">
                                        <input type="hidden" name="email_image_club" value="0">
                                        <input type="checkbox" name="email_image_club" value="1"  id="email_image_club" class="form-control"  @if($event->email_image_club )checked="checked" @endif>
                                        <label for="checkbox1">
                                            Nozobrazovať obrázok klubu
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            @if($event->active < 2 )
                            <div class="form-group">
                                <div class="col-md-6 col-md-offset-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{__('form.edit_record')}}
                                    </button>
                                </div>
                            </div>
                            @endif
                        </form>



                        {{--<div class="text-center">--}}
                            {{--<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal">--}}
                                {{--Launch demo modal--}}
                            {{--</button>--}}
                        {{--</div>--}}

                        {{--<div class="modal inmodal" id="myModal" tabindex="-1" role="dialog" aria-hidden="true">--}}
                            {{--<div class="modal-dialog">--}}
                                {{--<div class="modal-content animated bounceInRight">--}}
                                    {{--<div class="modal-header">--}}
                                        {{--<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>--}}
                                        {{--<i class="fa fa-laptop modal-icon"></i>--}}
                                        {{--<h4 class="modal-title">Modal title</h4>--}}
                                        {{--<small class="font-bold">Lorem Ipsum is simply dummy text of the printing and typesetting industry.</small>--}}
                                    {{--</div>--}}
                                    {{--<div class="modal-body">--}}
                                        {{--<p><strong>Lorem Ipsum is simply dummy</strong> text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown--}}
                                            {{--printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting,--}}
                                            {{--remaining essentially unchanged.</p>--}}
                                        {{--<div class="form-group"><label>Sample Input</label> <input type="email" placeholder="Enter your email" class="form-control"></div>--}}
                                    {{--</div>--}}
                                    {{--<div class="modal-footer">--}}
                                        {{--<button type="button" class="btn btn-white" data-dismiss="modal">Close</button>--}}
                                        {{--<button type="button" class="btn btn-primary">Save changes</button>--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}

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

    <link href="{!! asset('css/animate.css') !!}" rel="stylesheet">

@endsection


@section('scripts')

    <!-- file upload -->
    <script src="{!! asset('js/plugins/filestyle/bootstrap-filestyle.js') !!}"></script>
    <script src="{!! asset('js/plugins/pace/pace.min.js') !!}"></script>

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

            $("#event_form").validate({
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


            var geocoder = new google.maps.Geocoder();
            var marker = null;
            var map = null;
            var lat = @if($event->lat > 0){!! $event->lat !!}@else 0 @endif;
            var lng = @if($event->lng > 0){!! $event->lng !!}@else 0 @endif;
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

        $(".delete_button").click(function(e){

            var id = $(this).data("id-delete");

            swal({
                title: "Vymazať súbor?",
                text: "Táto operácia je nevratná!",
                type: "warning",
                showCancelButton: true,
                cancelButtonText: "Zrušiť",
                confirmButtonColor: "red",
                confirmButtonText: "Áno, zmazať",
                closeOnConfirm: true
            }, function () {

                $.ajax({
                    type:'POST',
                    url:'/ajax/function',
                    data: {
                        action: 'delete_uploaded_file',
                        file_id: id
                    },
                    dataType: 'json',
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    success:function(data){

                        console.log(data);

                        if(data.status == 'OK'){

                            $("#listing_file_"+id).remove();

                            toastr.error('Súbor bol zmazaný.');
                        }
                    }
                });

            });

        });



    </script>


@endsection