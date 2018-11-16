@extends('layouts.app')

@section('title', 'Detail udalosti')

@section('content')


    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    @include('invitation.header_event')

                    @include('components.validation')


                    <div class="ibox-content">
                        @if(count($club_members) >0)
                            <div class="table-responsive">
                                {{Form::open(['route' => 'invitations.event.member.store' ,
                                                'id' => 'item-del-'. $event->id  ])}}
                                {{Form::hidden('form_type','own_member')}}
                                {{Form::hidden('event_id', $event->id)}}

                                <table class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th width="5%"><input type="checkbox"  class="i-checks select_all" ></th>
                                        <th width="250px"> Meno</th>
                                        <th>Telefon</th>
                                        <th>Stav</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($club_members as $item => $v)
                                        <tr>
                                            <td width="5%">
                                                <input type="checkbox"  class="i-checks" name="user[]" value="{{$v->id}}"
                                                       @if($attendance) @foreach($attendance as $a)
                                                       @if($a->user_id == $v->id) disabled checked="checked" @endif @endforeach @endif
                                                >
                                            </td>
                                            <td width="250px">@if($v->name ){{ $v->name }} {{ $v->surname }}@else Hosť @endif</td>
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
                                    </tbody>
                                </table>
                                <button type="submit" class="btn btn-primary btn-sm send-alert"><i class="fa fa-send"></i> Odoslať pozvánku</button>
                                {{Form::close()}}

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


            function param(name) {
                return (location.search.split(name + '=')[1] || '').split('&')[0];
            }


        });
    </script>

@endsection