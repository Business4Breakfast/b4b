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
                            {{Form::open(['route' => 'invitations.event.member-all.store' ])}}
                            {{Form::hidden('event_id', $event->id)}}
                            {{Form::hidden('form_type','club_select')}}

                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="control-label" for="club">Klub</label>
                                    <select class="input form-control input-s-sm inline" name="club" onchange="this.form.submit()">
                                        <option value="0">-- Všetky kluby --</option>
                                    @if($clubs)
                                            @foreach($clubs as $c)
                                                <option value="{{$c->id}}" id="{{$c->id}}" @if( Session::get('invitation_member_all_filter_club') == $c->id) selected="selected" @endif>{{$c->title}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            {{Form::close()}}
                        </div>
                    </div>

                    <div class="ibox-content">
                        @if($clubs_user)
                            {{Form::open(['route' => 'invitations.event.member-all.store' ,
                                    'id' => 'item-del-'. $event->id  ])}}
                            {{Form::hidden('form_type','others_member')}}
                            {{Form::hidden('event_id', $event->id)}}
                            @foreach($clubs_user as $item => $cu)
                                @if( $cu['users'] )
                                    <div class="table-responsive m-b-lg">
                                        <table class="table table-striped">
                                            <thead>
                                            <tr>
                                                <th width="5%"><input type="checkbox" class="i-checks select_all" name="input[]"></th>
                                                <th width="250px" align="left" class="pull-left">{{ $cu['club']}} </th>
                                                <th>Telefon </th>
                                                <th>Stav</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @if($cu['users'])
                                                @foreach($cu['users'] as $item => $v)
                                                    <tr>
                                                        <td width="5%"><input type="checkbox"  class="i-checks" name="user[]" value="{{$v->id}}"
                                                                   @if($attendance) @foreach($attendance as $a)
                                                                   @if($a->user_id == $v->id) disabled checked="checked" @endif @endforeach @endif
                                                            >
                                                        </td>
                                                        <td width="250px" align="left">@if($v->name ){{ $v->name }} {{ $v->surname }}
                                                            @if( isset($v->function->display_name)) ({{ $v->function->display_name }}) @endif @else Hosť @endif</td>
                                                        <td>{{ $v->phone }}</td>
                                                        <td>
                                                            @if($attendance) @foreach($attendance as $a)
                                                                @if($a->user_id == $v->id)
                                                                    <i class="fa fa-check-circle-o"></i> Užívateľ je už pozvaný
                                                                @endif
                                                            @endforeach @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            @endforeach
                            <button type="submit" class="btn btn-primary btn-sm send-alert"><i class="fa fa-send"></i> Odoslať pozvánku</button>
                            {{Form::close()}}
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