@extends('layouts.app')

@section('title', 'Pridanie nového klubu')

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
                            <div class="col-md-4">
                                <div class="ibox-content profile-content">
                                    <h3><strong>{{$club->title}} ({{$club->short_title}})</strong></h3>
                                    <p><i class="fa fa-map-marker"></i> {{$club->address_street}}, {{$club->address_psc}}, {{$club->address_city}}</p>
                                    <h5>{{$club->host_name}}</h5>
                                    <p><a href="{{$club->host_url}}" target="_blank">{{$club->host_url}}</a></p>
                                    <p>{{str_limit($club->description, 100)}}</p>

                                </div>
                            </div>
                            <div class="col-md-6">
                                <div>
                                    <div class="feed-activity-list">
                                        @foreach($user_function as $uf)
                                            <div class="feed-user-element">
                                                <div class="media-body ">
                                                    <a data-toggle="modal" data-target="#edit_function_{{$uf->id}}" ><span class="label label-warning pull-right">Upraviť</span></a>
                                                    Funkcia <strong>{{$uf->display_name}}</strong></br>
                                                    @if(count($management) > 0)
                                                        @foreach($management as $m)
                                                            @if($uf->id == $m['id'] )
                                                                @if( $m['user'] != null)
                                                                    <strong>{{$m['user']->full_name}}</strong>
                                                                <small class="text-muted"> - začiatok funkcie - {{ Carbon\Carbon::parse( $m['valid_from'] )->format('d.m.Y') }}</small>
                                                                @else
                                                                    <strong>Funkcia nie je priradená</strong>
                                                                    <small class="text-muted"> od dátumu - {{ Carbon\Carbon::parse( $m['valid_from'] )->format('d.m.Y') }}</small>
                                                                @endif
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="modal inmodal" id="edit_function_{{$uf->id}}" tabindex="-1" role="dialog" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content animated bounceInRight">
                                                        <div class="modal-header">
                                                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                            <h4 class="modal-title">Upraviť funkciu {{$uf->display_name}}</h4>
                                                        </div>
                                                        <div class="modal-body">
                                                            @include('components.validation')
                                                            <form id="user_form" class="form-horizontal" method="POST" action="{{ route('setting.club-member.update', ['id' =>  $club->id]) }}" enctype="multipart/form-data" >
                                                                {{ csrf_field() }}
                                                                {{ method_field('PUT') }}

                                                                <input type="hidden" name="position" value="{{$uf->id}}">

                                                                <div class="form-group{{ $errors->has('valid_from') ? ' has-error' : '' }} date_picker_year" >
                                                                    <label for="valid_from" class="col-md-4 control-label">Začiatok funkcie</label>
                                                                    <div class="col-md-6">
                                                                        <div class="input-group date">
                                                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                                            <input type="text" class="form-control date_mask" value="{{ Carbon\Carbon::now()->format('d.m.Y') }}" name="valid_from" >
                                                                        </div>
                                                                        @if ($errors->has('valid_from'))
                                                                            <span class="help-block">
                                                                                <strong>{{ $errors->first('valid_from') }}</strong>
                                                                             </span>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                                <div class="form-group{{ $errors->has($uf->name) ? ' has-error' : '' }}">
                                                                    <label for="{{$uf->name}}" class="col-md-4 control-label">{{$uf->display_name}}</label>
                                                                    <div class="col-md-8">
                                                                        {{--<input id="address_country" type="text" class="form-control" name="address_country" value="{{ old('address_country') }}" required>--}}
                                                                        <div class="input-group">

                                                                            <select id="{{$uf->name}}" class="form-control" name="user_id"  required>
                                                                                <option value=""> -- výber --</option>
                                                                                <option value="0">Momentálne neurčená</option>
                                                                                @if($users)
                                                                                    @foreach($users as $u)
                                                                                        <option value="{{$u->id}}">{{$u->full_name}}</option>
                                                                                    @endforeach
                                                                                @endif
                                                                            </select>
                                                                        </div>
                                                                        @if ($errors->has($uf->name))
                                                                            <span class="help-block">
                                                                                <strong>{{ $errors->first($uf->name) }}</strong>
                                                                            </span>
                                                                        @endif
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

                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-white" data-dismiss="modal">{{__('form.cancel')}}</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="wrapper wrapper-content animated fadeInRight">
            <div class="row">
                @if($users)
                @foreach($users as $u)
                    <div class="col-lg-6">
                        <div class="contact-box">
                            {{--<a href="{{ route('setting.user.profile', [ 'id' => $u->id]) }}">--}}
                                <div class="col-sm-4">
                                    <div class="text-center">
                                        <img alt="image" class="img-circle m-t-xs img-responsive" src="{{ $u->imageThumb }}">
                                    </div>
                                    @permission('users-update')
                                    <div class="clear">
                                        <a href="{{ route('setting.user.profile.edit', $u->id ) }}" type="button" class="pull-left btn btn-success btn-sm m-t-lg m-l-xs"><i class="fa fa-edit"> Upraviť</i> </a>
                                        <a href="{{ route('setting.member-stats.show', $u->id ) }}" type="button" class="pull-left btn btn-warning btn-sm m-t-lg m-l-xs"><i class="fa fa-search"> Štatistika</i> </a>
                                    </div>
                                    @endpermission
                                </div>
                                <div class="col-sm-8">
                                    <h3><strong>{{$u->full_name}}</strong></h3>
                                    <address>
                                        {{$u->job_position}}<br>
                                        <strong>{{$u->phone}}</strong><br>
                                        @if(isset($u->industry->name))
                                        {{$u->industry->name}}<br>
                                        @endif
                                        {{$u->company}}<br>
                                        {{$u->email}}<br>
                                        @if($u['membership'])
                                            @foreach($u['membership'] as $mem)
                                            <p><strong>Členstvo: (id.{{$mem->id}})  {{\Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $mem->valid_from)->format('d.m.Y')}} -
                                                {{\Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $mem->valid_to)->format('d.m.Y')}} </strong></p>

                                                @if(Carbon\Carbon::now()->diffInDays( Carbon\Carbon::createFromFormat('Y-m-d H:i:s' , $mem->valid_to) ,  false) < 30 )
                                                    <p class="text-danger"> <i class="fa fa-times-circle"></i> <strong> Členstvo platné ešte ( {{ Carbon\Carbon::now()->diffInDays( Carbon\Carbon::createFromFormat('Y-m-d H:i:s' , $mem->valid_to) ,  true) }} ) dní</strong></p>
                                                @else
                                                    <p class="text-success"> <i class="fa fa-times-circle"></i> <strong> Členstvo platné ešte ( {{ Carbon\Carbon::now()->diffInDays( Carbon\Carbon::createFromFormat('Y-m-d H:i:s' , $mem->valid_to) ,  true) }} ) dní</strong></p>
                                                @endif
                                            @endforeach
                                        @endif
                                        <br>
                                        <abbr title="Phone"></abbr>
                                    </address>
                                </div>
                                <div class="clearfix"></div>
                            {{--</a>--}}
                        </div>
                    </div>
                    @if($u) @endif
                @endforeach
                @endif
            </div>
        </div>


    </div>


@endsection

@section('page_css')

@endsection


@section('scripts')


    <script>
        jQuery(window).ready(function (){

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