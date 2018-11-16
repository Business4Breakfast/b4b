@extends('layouts.app')

@section('title', 'Detail udalosti')

@section('content')


    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">

                    @include('events.components.header_event')

                    @include('components.validation')

                    <div class="ibox-content">
                            <div class="table-responsive">

                                {{Form::open(['route' => 'events.balance.store' ,
                                                'id' => 'item-del-'. $event->id  ])}}
                                {{Form::hidden('event_id', $event->id)}}

                                @if($attendance['confirmed_member'])
                                <table class="table table-striped" id="table_confirmed_member">
                                    <h3>Potvrdení členovia</h3>
                                    @include('events.components.balance_table_index')
                                    <tbody>

                                    @foreach($attendance['confirmed_member'] as $item => $v)
                                        <tr>
                                            <td>
                                                {{ $loop->iteration}}
                                            </td>
                                            <td>@if($v->name ) {{ $v->name }} {{ $v->surname }}@else Hosť @endif
                                                @if($v->is_club_member) <span class="text-danger">(člen)</span>  @endif
                                            </td>
                                            <td>{{ $v->phone }}</td>
                                            <td>
                                                {{--<span class="input-group-btn icon_status_element_{{$v->attend_id}}" style="font-size: inherit; margin-right: 10px;">--}}
                                                    @if($v->status_id == 1)
                                                        <i class="fa fa-exclamation-circle text-danger m-r-xs "></i>
                                                    @elseif($v->status_id == 2)
                                                        <i class="fa fa-check-circle text-success m-r-xs "></i>
                                                    @elseif($v->status_id == 3)
                                                        <i class="fa fa-times-circle text-warning m-r-xs "></i>
                                                    @else
                                                        <i class="fa fa-times-circle text-info m-r-xs"></i>
                                                    @endif
                                                {{--</span>--}}
                                                {{$v->status_name}}
                                            </td>
                                            <td align="left">
                                                <input type="checkbox"  class="i-checks user_check" name="user[]" value="{{$v->id}}"
                                                       {{--ak event je uz uzavreti checkboxy sa nastavia podla skutocnej navstevy--}}
                                                       @if($event->active == 2 )
                                                            @if($v->user_attend == 1) checked="checked"   @endif disabled
                                                       @else checked="checked" @endif>

                                            </td>
                                            <td align="left">
                                                {{$v->user_status_name}}
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <td></td>
                                        <td colspan="3"></td>
                                        <td>Celkom: <span id="table_confirmed_member_count"></span></td>
                                        <td></td>
                                    </tr>
                                    </tfoot>
                                </table>
                                @endif

                                @if($attendance['apologized_member'])
                                    <h3>Ospravedlnení členovia</h3>
                                    <table class="table table-striped" id="table_apologized_member">
                                        @include('events.components.balance_table_index')
                                        <tbody>
                                        @foreach($attendance['apologized_member'] as $item => $v)
                                            @if($v->is_club_member && $v->status_id == 3)
                                                <tr>
                                                    <td>
                                                    </td>
                                                    <td>@if($v->name ) {{ $v->name }} {{ $v->surname }}@else Hosť @endif
                                                        @if($v->is_club_member) <span class="text-danger">(člen)</span>  @endif
                                                    </td>
                                                    <td>{{ $v->phone }}</td>
                                                    <td>
                                                        {{--<span class="input-group-btn icon_status_element_{{$v->attend_id}}" style="font-size: inherit; margin-right: 10px;">--}}
                                                        @if($v->status_id == 1)
                                                            <i class="fa fa-exclamation-circle text-danger m-r-xs "></i>
                                                        @elseif($v->status_id == 2)
                                                            <i class="fa fa-check-circle text-success m-r-xs "></i>
                                                        @elseif($v->status_id == 3)
                                                            <i class="fa fa-times-circle text-warning m-r-xs "></i>
                                                        @else
                                                            <i class="fa fa-times-circle text-info m-r-xs"></i>
                                                        @endif
                                                        {{--</span>--}}
                                                        {{$v->status_name}}
                                                    </td>
                                                    <td align="left">
                                                        {{--<input type="checkbox"  class="i-checks" name="user[]" value="{{$v->id}}">--}}


                                                        <input type="checkbox"  class="i-checks user_check" name="user[]" value="{{$v->id}}"
                                                               {{--ak event je uz uzavreti checkboxy sa nastavia podla skutocnej navstevy--}}
                                                               @if($event->active == 2 )
                                                                 @if($v->user_attend == 1) checked="checked" @endif  disabled
                                                                @endif>

                                                    </td>
                                                    <td align="left">
                                                        {{$v->user_status_name}}
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                        </tbody>
                                        <tfoot>
                                        <tr>
                                            <td></td>
                                            <td colspan="3"></td>
                                            <td>Celkom: <span id="table_apologized_member_count"></span></td>
                                            <td></td>
                                        </tr>
                                        </tfoot>
                                    </table>
                                @endif

                                @if($attendance['confirmed_guests'])
                                    <h3>Potvrdení hostia</h3>
                                    <table class="table table-striped" id="table_confirmed_guests">
                                        @include('events.components.balance_table_index')
                                        <tbody>
                                        @foreach($attendance['confirmed_guests'] as $item => $v)
                                                <tr>
                                                    <td>
                                                    </td>
                                                    <td>@if($v->name ) {{ $v->name }} {{ $v->surname }}@else Hosť @endif
                                                        @if($v->is_club_member) <span class="text-danger">(člen)</span>  @endif
                                                    </td>
                                                    <td>{{ $v->phone }}</td>
                                                    <td>
                                                        {{--<span class="input-group-btn icon_status_element_{{$v->attend_id}}" style="font-size: inherit; margin-right: 10px;">--}}
                                                        @if($v->status_id == 1)
                                                            <i class="fa fa-exclamation-circle text-danger m-r-xs "></i>
                                                        @elseif($v->status_id == 2)
                                                            <i class="fa fa-check-circle text-success m-r-xs "></i>
                                                        @elseif($v->status_id == 3)
                                                            <i class="fa fa-times-circle text-warning m-r-xs "></i>
                                                        @else
                                                            <i class="fa fa-times-circle text-info m-r-xs"></i>
                                                        @endif
                                                        {{--</span>--}}
                                                        {{$v->status_name}}
                                                    </td>
                                                    <td align="left">
                                                        <input type="checkbox"  class="i-checks user_check" name="user[]" value="{{$v->id}}"
                                                               {{--ak event je uz uzavreti checkboxy sa nastavia podla skutocnej navstevy--}}
                                                               @if($event->active == 2 )
                                                               @if($v->user_attend == 1) checked="checked"  @endif disabled
                                                               @else checked="checked" @endif>

                                                    </td>
                                                    <td align="left">
                                                        {{$v->user_status_name}}
                                                    </td>
                                                </tr>
                                        @endforeach
                                        </tbody>
                                        <tfoot>
                                        <tr>
                                            <td></td>
                                            <td colspan="3"></td>
                                            <td>Celkom: <span id="table_confirmed_guests_count"></span></td>
                                            <td></td>
                                        </tr>
                                        </tfoot>
                                    </table>
                                @endif

                                @if($attendance['apologized_guests'])
                                    <h3>Ospravedlnení hostia</h3>
                                    <table class="table table-striped" id="table_apologized_guests">
                                        @include('events.components.balance_table_index')
                                        <tbody>
                                        @foreach($attendance['apologized_guests'] as $item => $v)
                                                <tr>
                                                    <td>
                                                    </td>
                                                    <td>@if($v->name ) {{ $v->name }} {{ $v->surname }}@else Hosť @endif
                                                        @if($v->is_club_member) <span class="text-danger">(člen)</span>  @endif
                                                    </td>
                                                    <td>{{ $v->phone }}</td>
                                                    <td>
                                                        {{--<span class="input-group-btn icon_status_element_{{$v->attend_id}}" style="font-size: inherit; margin-right: 10px;">--}}
                                                        @if($v->status_id == 1)
                                                            <i class="fa fa-exclamation-circle text-danger m-r-xs "></i>
                                                        @elseif($v->status_id == 2)
                                                            <i class="fa fa-check-circle text-success m-r-xs "></i>
                                                        @elseif($v->status_id == 3)
                                                            <i class="fa fa-times-circle text-warning m-r-xs "></i>
                                                        @else
                                                            <i class="fa fa-times-circle text-info m-r-xs"></i>
                                                        @endif
                                                        {{--</span>--}}
                                                        {{$v->status_name}}
                                                    </td>
                                                    <td align="left">
                                                        <input type="checkbox"  class="i-checks user_check" name="user[]" value="{{$v->id}}"
                                                               {{--ak event je uz uzavreti checkboxy sa nastavia podla skutocnej navstevy--}}
                                                               @if($event->active == 2 )
                                                               @if($v->user_attend == 1) checked="checked"  @endif disabled
                                                                @endif>
                                                    </td>
                                                    <td align="left">
                                                        {{$v->user_status_name}}
                                                    </td>
                                                </tr>
                                        @endforeach
                                        </tbody>
                                        <tfoot>
                                        <tr>
                                            <td></td>
                                            <td colspan="3"></td>
                                            <td>Celkom: <span id="table_apologized_guests_count"></span></td>
                                            <td></td>
                                        </tr>
                                        </tfoot>
                                    </table>
                                @endif

                                @if($attendance['other_guests'])
                                    <h3>Ostatní hostia</h3>
                                    <table class="table table-striped" id="table_other_guests">
                                        @include('events.components.balance_table_index')
                                        <tbody>
                                        @foreach($attendance['other_guests'] as $item => $v)
                                                <tr>
                                                    <td>
                                                    </td>
                                                    <td>@if($v->name ) {{ $v->name }} {{ $v->surname }}@else Hosť @endif
                                                        @if($v->is_club_member) <span class="text-danger">(člen)</span>  @endif
                                                    </td>
                                                    <td>{{ $v->phone }}</td>
                                                    <td>
                                                        {{--<span class="input-group-btn icon_status_element_{{$v->attend_id}}" style="font-size: inherit; margin-right: 10px;">--}}
                                                        @if($v->status_id == 1)
                                                            <i class="fa fa-exclamation-circle text-danger m-r-xs "></i>
                                                        @elseif($v->status_id == 2)
                                                            <i class="fa fa-check-circle text-success m-r-xs "></i>
                                                        @elseif($v->status_id == 3)
                                                            <i class="fa fa-times-circle text-warning m-r-xs "></i>
                                                        @else
                                                            <i class="fa fa-times-circle text-info m-r-xs"></i>
                                                        @endif
                                                        {{--</span>--}}
                                                        {{$v->status_name}}
                                                    </td>
                                                    <td align="left">
                                                        <input type="checkbox"  class="i-checks user_check" name="user[]" value="{{$v->id}}"
                                                               {{--ak event je uz uzavreti checkboxy sa nastavia podla skutocnej navstevy--}}
                                                               @if($event->active == 2 )
                                                               @if($v->user_attend == 1) checked="checked"   @endif disabled
                                                                @endif>
                                                    </td>
                                                    <td align="left">
                                                        {{$v->user_status_name}}
                                                    </td>
                                                </tr>
                                        @endforeach
                                        </tbody>
                                        <tfoot>
                                        <tr>
                                            <td></td>
                                            <td colspan="3"></td>
                                            <td>Celkom: <span id="table_other_guests_count"></span></td>
                                            <td></td>
                                        </tr>
                                        </tfoot>
                                    </table>
                                @endif
                                @if( in_array($event->active, [0,1] ))
                                    <button type="submit" class="btn btn-danger btn-sm"><i class="fa fa-check-circle-o"></i> Odoslať uzávierku</button>
                                @endif
                                {{Form::close()}}
                            </div>
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
        $(document).ready(function() {


            $('input').on('ifChecked', function(event){
                checkboxChecker('table_members');
            });

            $('input').on('ifUnchecked', function(event){
                checkboxChecker();
            });

            // General/modular function for status logging
            var checkboxChecker = function() {
                var count = 0;
                var tables = ['confirmed_member','apologized_member','confirmed_guests', 'apologized_guests', 'other_guests'];

                $.each(tables, function (index, value){
                   // prejdeme vsetky tabulky
                    count=0;
                    $('#table_'+value+' tr').each(function(i) {
                        // Only check rows that contain a checkbox
                        var $chkbox = $(this).find('input[type="checkbox"]');
                        if ($chkbox.length) {
                            var status = $chkbox.prop('checked');
                            //spocitame len zakliknute
                            if(status == true){
                                count = count + 1;
                            }
                            $('#table_'+value+'_count').text(count);
                        }
                    });

                });

            };

            // Check status checkobox pri nacitani stranky
            checkboxChecker();



            $.validator.setDefaults({ ignore: ":hidden:not(.chosen-select)" });

            $('#new_guest_phone').mask('+000000000000');


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



        });
    </script>

@endsection