<div class="row">
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="ibox-content p-md">
                <div class="row">
                    <div class="col-md-6">
                        <div class="pull-left">
                            <div class="img">
                                <img alt="image" class="feed-photo img-responsive" style="max-width: 80px;"  src="{!! $event->image_thumb !!}">
                            </div>
                        </div>
                        <div class="profile-content media-body">
                            <h3><strong>{{$event->title}} ({{Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $event->event_from)->format('d.m.Y H:i')}} - {{Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $event->event_to)->format('H:i')}})</strong></h3>
                            <h5>{{$event->club->title}}</h5>
                            <p><i class="fa fa-map-marker"></i> {{$event->host_name}},  {{$event->address_street}}<br>{{$event->address_psc}}, {{$event->address_city}}</p>
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <p class="pull-right">
                                <a href="{{ route('events.listing.show', ['event' => $event->id ]) }}" type="button" class="btn btn-success btn-sm m-l-sm"><i class="fa fa-edit"></i> Detail</a>
                            </p>
                        </div>


                        <div class="row">
                            <div class="pull-right">
                            @if(Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $event->event_from) > Carbon\Carbon::createFromFormat('Y-m-d H:i:s', now()  ))
                                <h3>Pozvánky</h3>
                                <div class="btn-group">
                                    <a href="{{ route('invitations.event.guest-new', [ 'event' => $event->id, 'club' => $event->club_id] ) }}"
                                       class="btn @if(str_limit(Request::capture()->segment(3), 9, "" ) == 'guest-new') btn-warning @else btn-white  @endif " type="button">Nový hosť</a>

                                    <a href="{{ route('invitations.event.guest', [ 'event' => $event->id] ) }}"
                                       class="btn @if(Request::capture()->segment(2) == 'event-guest') btn-warning @else btn-white  @endif " type="button">Hostia</a>

                                    <a href="{{ route('invitations.event.member', [ 'event' => $event->id] ) }}"
                                       class="btn @if(Request::capture()->segment(2) == 'event-member') btn-warning @else btn-white  @endif " type="button">Členovia</a>

                                    <a href="{{ route('invitations.event.member-all', [ 'event' => $event->id] ) }}"
                                       class="btn @if(Request::capture()->segment(2) == 'event-member-all') btn-warning @else btn-white  @endif " type="button">Členovia ostatní</a>
                                </div>
                            @else
                                {{--ak je uzavierka neda sa doplnit--}}
                                {{--@if($event->status < 2)--}}
                                    <h3>Pridanie osoby do prezenčky</h3>
                                    <div class="btn-group">
                                        <a href="{{ route('invitations.event.guest-new', [ 'event' => $event->id, 'club' => $event->club_id] ) }}"
                                           class="btn @if(str_limit(Request::capture()->segment(3), 9, "" ) == 'guest-new') btn-success @else btn-white  @endif " type="button">Nový hosť</a>

                                        <a href="{{ route('invitations.event.guest', [ 'event' => $event->id] ) }}"
                                           class="btn @if(Request::capture()->segment(2) == 'event-guest') btn-success @else btn-white  @endif " type="button">Hostia</a>

                                        <a href="{{ route('invitations.event.member', [ 'event' => $event->id] ) }}"
                                           class="btn @if(Request::capture()->segment(2) == 'event-member') btn-success @else btn-white  @endif " type="button">Členovia</a>

                                        <a href="{{ route('invitations.event.member-all', [ 'event' => $event->id] ) }}"
                                           class="btn @if(Request::capture()->segment(2) == 'event-member-all') btn-success @else btn-white  @endif " type="button">Členovia ostatní</a>
                                    </div>
                                {{--@endif--}}
                            @endif
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>