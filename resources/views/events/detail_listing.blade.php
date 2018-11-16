<tr>
    <td>
        @if( $event->active == 2)
            <input type="checkbox"  class="i-checks" name="user_resend[]" value="{{$v->user_attend}}"
                   {{--ak event je uz uzavreti checkboxy sa nastavia podla skutocnej navstevy--}}
                   @if($event->active == 2 )
                   @if($v->user_attend == 1) checked="checked"  @endif disabled
                   @else checked="checked" @endif>
        @else
            <input type="checkbox"  class="i-checks" name="user_resend[]" value="{{$v->attend_id}}">
        @endif

    </td>
    <td><strong>@if($v->name ){{ $v->surname }}, {{ $v->name }}@else Hosť @endif</strong> </td>
    <td class="hidden-xs">{{ $v->phone }}</td>
    <td>{{ Carbon\Carbon::createFromFormat('Y-m-d H:i:s',$v->date_create)->format('d.m.Y') }}</td>
    <td>
        @if($v->attend_user_event)
            <span class="text-danger"> Náš {{$v->user_status_name}}</span>
        @else
            <span class="text-info">{{$v->user_status_name}}</span>
        @endif
    </td>
    <td>

        {{ Form::open(['method' => 'POST', 'route' => ['invite.attendance.resend.store', $v->attend_id ],
            'class' => 'class="m-l-sm pull-right btn btn-danger btn-xs delete-alert hide',
            'id' => 'item-send-'. $v->attend_id  ])
        }}
        {{Form::hidden('attendance_id', $v->attend_id  )}}
        {{ Form::close() }}

        <div class="input-group ">
        <span class="input-group-btn icon_status_element_{{$v->attend_id}}" style="font-size: inherit; margin-right: 10px;">
            @if($v->status_id == 1)
                <i class="fa fa-exclamation-circle text-danger m-r-xs "></i>
            @elseif($v->status_id == 2)
                <i class="fa fa-check-circle text-success m-r-xs "></i>
            @elseif($v->status_id == 3)
                <i class="fa fa-times-circle text-warning m-r-xs "></i>
            @else
                <i class="fa fa-times-circle text-info m-r-xs"></i>
            @endif
        </span>

            <select class="form-control input-sm guest_status" data-attendance-id="{{$v->attend_id}}"
                     @if($event->active > 1) disabled @endif >
                @foreach($events_guest_status as $egs)
                    <option  value="{{$egs->id}}" @if($egs->id == $v->status_id) selected="selected" @endif>{{$egs->status}}</option>
                @endforeach
            </select>

        </div>
    </td>
    <td>

    @if(Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $event->event_from) > Carbon\Carbon::createFromFormat('Y-m-d H:i:s', now()  ) && $event->active < 2)
        <button type="button" style="margin-left: 10px;"  data-item-id="{{ $v->attend_id }}"
                                            class="btn btn-primary btn-xs send_invitation_detail"><i class="fa fa-send-o"></i></button>
    @endif

        @role('superadministrator')
            <a type="button" data-item-id="{{ $v->id }}"
               class="btn btn-danger btn-xs delete-alert"><i class="fa fa-trash-o"></i> </a>

            {{ Form::open(['method' => 'DELETE', 'route' => ['events.attendance.destroy', $v->attend_id ],
                'class' => 'class="m-l-sm pull-right btn btn-danger btn-xs delete-alert hide',
                'id' => 'item-del-'. $v->id  ])
            }}
            {{Form::hidden('event_id', $v->id  )}}
            {{ Form::close() }}
        @endrole
    </td>
</tr>