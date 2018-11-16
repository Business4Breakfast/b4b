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
                            <p><i class="fa fa-map-marker"></i> {{$event->host_name}},  {{$event->address_street}}<br>{{$event->address_psc}}, {{$event->address_city}}
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <p class="pull-right">
                                @if($event->active == 2)
                                    <button type="button" class="btn btn-sm btn-danger"> Uzatvorená</button>
                                @else
                                    <button type="button" class="btn btn-sm btn-defaul"> Stav udalosti aktívna</button>
                                @endif

                                <a href="{{ route('events.listing.show', ['event' => $event->id ]) }}" type="button" class="btn btn-success btn-sm m-l-sm"><i class="fa fa-edit"></i> Detail</a>
                            </p>
                        </div>


                        <div class="row">
                            <div class="pull-right">

                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>