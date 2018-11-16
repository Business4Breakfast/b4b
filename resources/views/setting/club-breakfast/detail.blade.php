@extends('layouts.app')

@section('title', 'Raňajky klubu')

@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">

        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content p-md">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="img">
                                    <img alt="image" class="feed-photo img-responsive" src="{!! $club->image_thumb !!}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="ibox-content profile-content">
                                    <p>Club:</p>
                                    <h3><strong>{{$club->title}} ({{$club->short_title}})</strong></h3>
                                    <p><i class="fa fa-map-marker"></i> {{$club->address_street}}, {{$club->address_psc}}, {{$club->address_city}}</p>
                                    <h5>
                                        {{$club->host_name}}
                                    </h5>
                                    <p>{{str_limit($club->description, 100)}}
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-4">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content">
                        @if(count($items) > 0)
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>Dátum</th>
                                <th>Udalosť</th>
                                <th>Vzdelávací bod</th>
                                <th>Zamerané na biznis</th>
                                <th>Názov</th>
                                <th width="300px"></th>
                            </tr>
                            </thead>
                            <tbody>

                                @foreach($items as $item => $v)
                                    <tr>
                                        <td>
                                            @if($v->event_from > Carbon\Carbon::today())
                                                <i class="fa fa-check text-success"></i>
                                            @else
                                                <i class="fa fa-close text-danger"></i>
                                            @endif  {{Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $v->event_from)->format('d.m.Y')}}
                                        </td>
                                        <td>{{$v->title}}</td>
                                        <td>
                                            <select class="class_activity"  data-activity_type="1" data-event_id="{{$v->id}}" data-club_id="{{$club->id}}">
                                                <option value="">-- neurčený --</option>
                                                @foreach($club_users as $cu)
                                                    <option value="{{$cu->id}}"
                                                        @if($user_activity)
                                                            @foreach($user_activity as $ua)
                                                                @if($club->id == $ua->club_id &&  $v->id == $ua->event_id  && $ua->activity_id == 1 )
                                                                    @if($cu->id == $ua->user_id)
                                                                        selected="selected"
                                                                    @endif
                                                                @endif
                                                            @endforeach
                                                        @endif
                                                    > {{$cu->name}} {{$cu->surname}}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <select class="class_activity" data-activity_type="2" data-event_id="{{$v->id}}" data-club_id="{{$club->id}}">
                                             <option value="" >-- neurčený --</option>
                                                @if($club_users)
                                                @foreach($club_users as $cu)
                                                    <option value="{{$cu->id}}"
                                                    @if($user_activity)
                                                        @foreach($user_activity as $ua)
                                                            @if($club->id == $ua->club_id &&  $v->id == $ua->event_id  && $ua->activity_id == 2 )
                                                                @if($cu->id == $ua->user_id)
                                                                    selected="selected"
                                                                @endif
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                    > {{$cu->name}} {{$cu->surname}}</option>
                                                @endforeach
                                                @endif
                                            </select>
                                        </td>


                                        <td></td>
                                        <td>

                                            <a href="{{ route('events.balance.show', ['user' => $v->id ]) }}" type="button" class="pull-right btn btn-success btn-xs m-l-sm"><i class="fa fa-edit"></i> </a>

                                            @if($v->event_from > Carbon\Carbon::today())
                                                <a href="{{ route('setting.club-breakfast-ticket', ['club' => $v->id ]) }}" type="button" class="pull-right btn btn-primary btn-xs m-l-sm disabled">Ústrižky</a>
                                            @else
                                                <a href="{{ route('events.listing.show', ['club' => $v->id ]) }}" type="button" class="pull-right btn btn-primary btn-xs m-l-sm ">Ústrižky</a>
                                            @endif

                                            <a href="{{ route('events.listing.show', ['event' => $v->id ]) }}" type="button" class="pull-right btn btn-primary btn-xs m-l-sm">Prezenčka</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{ $items->links() }}
                        @else
                            <p class="text-danger">Neexistujú žiadne záznamy</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6">

            </div>

            <div class="col-lg-6">

            </div>

        </div>
    </div>


@endsection

@section('page_css')

@endsection


@section('scripts')


    <script>
        jQuery(window).ready(function (){

            $(".class_activity").change( function () {

                var user_id = $(this).val();
                var activity_type = $(this).data('activity_type');
                var event_id = $(this).data('event_id');
                var club_id = $(this).data('club_id');

                console.log(user_id);
                console.log(activity_type);
                console.log(event_id);

                $.ajax({
                    type:'POST',
                    url:'/ajax/function',
                    data: {
                        action: "set_event_activity",
                        user_id: user_id,
                        activity_type: activity_type,
                        club_id: club_id,
                        event_id: event_id
                    },
                    dataType: 'json',
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    success:function(data){

                        console.log(data);

                        if(data){



//                            toastr.success('Stav úspešne zapísaný');
//                            replaceIconStatus(data.status, data.attendance)

                        }
                    }
                });
            });

            $('#phone').mask('+000000000000');
            $('.clock').mask('00:00');


            $("#user_form").validate({
                rules: {
                    phone: {
                        required: true,
                        minlength: 13
                    }
                }
            });
        });

    </script>


@endsection