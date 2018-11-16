@extends('layouts.app')

@section('title', 'Detail udalosti')

@section('content')

    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">

                    @include('invitation.header_event')

                    @include('components.validation')

                    <div class="ibox-title">
                        <div class="row">
                            {{Form::open(['route' => 'invitations.event.guest.store' ])}}
                            {{Form::hidden('event_id', $event->id)}}
                            {{Form::hidden('form_type','guest_status')}}
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label class="control-label" for="user_status">Stav</label>
                                        <select class="input form-control input-s-sm inline" name="user_status" onchange="this.form.submit()">
                                            <option value="0">-- Všetky stavy --</option>
                                            @if($user_statuses)
                                                @foreach($user_statuses as $us)
                                                    <option value="{{$us->id}}" id="{{$us->id}}" @if( Session::get('invitation_guest_filter_status') == $us->id) selected="selected" @endif>{{$us->status}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label class="control-label" for="user_invite">Pozval</label>
                                    <select class="input form-control input-s-sm inline" name="user_invite" onchange="this.form.submit()">
                                        <option value="0">-- Nepriradení --</option>
                                        @if($user_members)
                                            @foreach($user_members as $um)
                                                <option value="{{$um->id}}" id="{{$um->id}}" @if( Session::get('invitation_guest_filter_user_invite') == $um->id) selected="selected" @endif>{{$um->name}} {{$um->surname}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label class="control-label" for="search_category">Klub</label>
                                        <select class="input form-control input-s-sm inline" name="club" onchange="this.form.submit()"

                                            @if(Auth::user()->hasRole(['superadministrator','administrator' ])  == false)  disabled @endif
                                            >
                                            @if($clubs)
                                                @foreach($clubs as $c)

                                                    @php
                                                        if(Session::get('invitation_guest_filter_club') > 0) {
                                                            $club_var = Session::get('invitation_guest_filter_club');
                                                        } else {
                                                            $club_var = $event->club_id;
                                                        }
                                                    @endphp

                                                    <option value="{{$c->id}}" id="{{$c->id}}"  @if( $club_var == $c->id) selected="selected" @endif >{{$c->title}}</option>

                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label class="control-label" for="search_company">Meno <small>(min. 3 znaky)</small></label>
                                        <input type="text" id="search_user" name="search_user" value="{{Session::get('invitation_guest_filter_user', null)}}" placeholder="" class="form-control">
                                    </div>
                                </div>

                                <div class="col-sm-2">
                                    <label class="control-label block" for="submit">&nbsp;</label>
                                    <input type="submit" class="btn btn-primary" value="Filtrovať">
                                    <a href="{{route('invitations.event.guest', ['event' => $event->id, 'reset' => 'true' ]) }}" class="btn btn-danger"><i class="fa fa-close"></i> </a>

                                </div>
                            {{Form::close()}}
                        </div>
                    </div>


                    <div class="ibox-content">
                    @if(count($club_guests) >0)
                        <div class="table-responsive">
                            {{Form::open( ['route' => 'invitations.event.guest.store' ])}}
                            {{Form::hidden('form_type','club_guests')}}
                            {{Form::hidden('event_id', $event->id)}}

                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th width="5%"><input type="checkbox"  class="i-checks select_all" ></th>
                                    <th width="250px"> Meno</th>
                                    <th width="120px">Pozval</th>
                                    <th width="120px">Telefon</th>
                                    <th width="100px">Stav</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($club_guests as $item => $v)
                                    <tr>
                                        <td width="5%"><input type="checkbox"  class="i-checks" name="user[]" value="{{$v->id}}"
                                                   @if($attendance) @foreach($attendance as $a)
                                                   @if($a->user_id == $v->id) disabled checked="checked" @endif @endforeach @endif
                                            >
                                        </td>
                                        <td width="250px">@if($v->name ){{ $v->name }} {{ $v->surname }}

                                            {{--@role('superadministrator', 'administrator')--}}
                                            <a href="{{route('guests.guest-listings.edit',
                                                   ['id' => $v->id])}}" target="_blank"> <i class="fa fa-external-link"></i> </a>
                                            {{--@endrole--}}

                                            @else Hosť @endif</td>
                                        <td width="100px">@if($v->created_user > 0){{$v->invited_user}}@endif</td>
                                        <td width="120px">{{ $v->phone }}</td>
                                        <td width="100px">{{ $v->status_name }}</td>
                                        <td>
                                            @if($attendance) @foreach($attendance as $a)
                                                @if($a->user_id == $v->id)
                                                    <i class="fa fa-check-circle-o"></i> Užívateľ je už pozvaný
                                                @endif
                                            @endforeach @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <button type="submit" class="btn btn-primary btn-sm send-alert"><i class="fa fa-send"></i> Odoslať pozvánku</button>
                            {{Form::close()}}
                            {{ $club_guests->links() }}
                        </div>
                    @else
                        <p class="text-danger">Neexistujú žiadne záznamy</p>
                    @endif
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection

@section('page_css')

@endsection


@section('scripts')

    <script>
        jQuery(window).ready(function (){

            $.validator.setDefaults({ ignore: ":hidden:not(.chosen-select)" });

            $('#new_guest_phone').mask('+000000000000');

            $('#new_guest_email').delayKeyup(function(){
                var email = $('#new_guest_email').val();
                var phone = $('#new_guest_phone').val();

                $('#new_guest_email').filter(function(){

                    var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
                    if( !emailReg.test( email ) ) {

                        //zmazeme nespravny email
                        $('#error_guest_email').html('Nesprávny formát emailu').removeClass('hide').addClass('show');
                        $('#new_guest_email').val("").focus();

                        toastr.warning('Please enter valid email');

                    } else {

                        $('#error_guest_email').removeClass('show').addClass('hide');


                        toastr.success('Thank you for your valid email');
                    }
                })

                toastr.success($('#new_guest_email').val());

            }, 3000);


            var tab = param('tab');
            if ( tab == "") tab = 1;
            $('#tab_'+tab).tab('show');

            $('.i-checks').iCheck({
                checkboxClass: 'icheckbox_square-green',
                radioClass: 'iradio_square-green'
            });


            $('.select_all').on('ifChecked', function(event){
                $(this).closest('table').find('td input:checkbox').iCheck('check')
            });

            $('.select_all').on('ifUnchecked', function(event){
                $(this).closest('table').find('td input:checkbox').iCheck('uncheck')
            });


            $('.delete-alert').click(function (e) {

                var id = $(e.currentTarget).attr("data-item-id");
                swal({
                    title: "Ste si istý?",
                    text: "Táto operácia je nevratná!",
                    type: "warning",
                    showCancelButton: true,
                    cancelButtonText: "Zrušiť",
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Áno, zmazať",
                    closeOnConfirm: false
                }, function () {
                    document.getElementById('item-del-'+id).submit();
                    swal("Deleted", "Záznam bol zmazaný", "success");
                });
            });


            function param(name) {
                return (location.search.split(name + '=')[1] || '').split('&')[0];
            }


        });
    </script>

@endsection