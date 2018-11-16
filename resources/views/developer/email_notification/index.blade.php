@extends('layouts.app')

@section('title', 'Franchisory')

@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox-title">
                    <h3>Sending email</h3>
                    <div class="ibox-title">
                        <div class="row">
                            <form id="user_form" class="" method="GET" action="{{ route('developer.email-notifications.index') }}" >
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label class="control-label" for="price">Email</label>
                                        <input type="text" id="search_club" name="search_email" value="{{$req['search_email']}}" placeholder="" class="form-control">
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <label class="control-label block" for="quantity">&nbsp;</label>
                                    <input type="submit" class="btn btn-primary" value="Filtrovať">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="ibox float-e-margins">
                    <div class="ibox-content">
                        <p>
                            <a href="{{route('developer.email.notification.send')}}" class="btn btn-info pull-right" >Send emails</a>
                        </p>
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Recipients</th>
                                <th width="200px">Send</th>
                                <th>Status</th>
                                <th>Module</th>
                                <th>Subject</th>
                                <th>Update</th>
                                <th>Error</th>
                                <th width="200px"></th>
                            </tr>
                            </thead>
                            <tbody>
                            @if($items)
                                @foreach($items as $k => $v)
                                <tr>
                                    <td>{{ $v->id }}</td>
                                    <td>{{ $v->recipients_string }}</td>
                                    <td>
                                        {{Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $v->date_send)->format('d.m.Y H:i')}}

                                        @if(Carbon\Carbon::now()->diffInHours(Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $v->date_send)) > 0)
                                            @if(Carbon\Carbon::now()->diffInHours(Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $v->date_send)) > 24)
                                                <strong class="text-success">( {{ Carbon\Carbon::now()->diffInWeeks(Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $v->date_send), false) }} w )</strong>
                                            @else
                                                <strong class="text-success">( {{ Carbon\Carbon::now()->diffInHours(Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $v->date_send), false) }} h )</strong>
                                            @endif
                                        @else
                                            @if(Carbon\Carbon::now()->diffInHours(Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $v->date_send)) > 24)
                                                <strong class="text-danger">( {{ Carbon\Carbon::now()->diffInWeeks(Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $v->date_send), false) }} w )</strong>
                                            @else
                                                <strong class="text-danger">( {{ Carbon\Carbon::now()->diffInHours(Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $v->date_send), false) }} h )</strong>
                                            @endif
                                        @endif
                                    </td>
                                    <td>{{$v->status}}</td>
                                    <td>{{$v->module}} - {{$v->module_id}}</td>
                                    <td>{{ $v->subject }}</td>
                                    <td>{{Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $v->updated_at)->format('d.m.Y H:i')}}</td>
                                    <td>{{ $v->error_log }}</td>
                                    <td>
                                        <a type="button" data-item-id="{{ $v->id }}"
                                           class="m-l-sm pull-right btn btn-danger btn-xs delete-alert"><i class="fa fa-trash-o"></i> </a>

                                        {{ Form::open(['method' => 'DELETE', 'route' => ['developer.email-notifications.destroy', $v->id ],
                                            'class' => 'class="m-l-sm pull-right btn btn-danger btn-xs delete-alert hide',
                                            'id' => 'item-del-'. $v->id  ])
                                        }}
                                        {{Form::hidden('notification_id', $v->id  )}}
                                        {{ Form::close() }}
                                        <a href="{{ route('developer.email-notifications.edit', ['id' => $v->id ]) }}" type="button" class="pull-right btn btn-success btn-xs m-l-sm"><i class="fa fa-edit"></i> </a>
                                    </td>
                                </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                        {{ $links }}
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('css')

@endsection

@section('scripts')

    <script>

        $(document).ready(function(){

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

        });

    </script>

@endsection