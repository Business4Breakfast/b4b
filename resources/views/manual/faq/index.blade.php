@extends('layouts.app')

@section('title', '')

@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                @if($items)
                    @foreach($items as $item => $v)
                    <div class="faq-item" id="faq-accord">
                        <div class="row">
                            <div class="col-md-7">
                                <a data-toggle="collapse" href="#faq{{$v->id}}" class="faq-question collapsed" aria-expanded="false">{{ str_limit($v->name, 100) }}</a>
                                <small>Pridal <strong>{{$v->user_name}} {{$v->user_surname}}</strong> <i class="fa fa-clock-o"></i> {{Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $v->created_at)->format('d.m.Y')}}</small>
                            </div>
                            <div class="col-md-2">
                                <span class="small font-bold">Kategória</span>
                                <div class="tag-list">
                                    <a href="#" ><span class="tag-item">General</span></a>
                                    <span class="tag-item">License</span>
                                </div>
                            </div>
                            <div class="col-md-1 text-right">
                                <span class="small font-bold">Voting </span><br>
                                42
                            </div>
                            <div class="col-md-2">
                                <span class="m-t-n-sm"></span>

                                <div class="tag-list">
                                    @permission('acl-item-list-delete')
                                    <a type="button" data-item-id="{{ $v->id }}"
                                       class="btn btn-danger btn-xs delete-alert pull-right m-l-xs"><i class="fa fa-trash"></i><span class="hidden-xs hidden-sm"></span></a>

                                    {{ Form::open(['method' => 'DELETE', 'route' => [$prefix . '.'.$module.'.destroy', $v->id ],
                                        'class' => 'class="m-l-sm pull-right btn btn-danger btn-xs delete-alert hide',
                                        'id' => 'item-del-'. $v->id  ])}}
                                    {{Form::hidden('user_id', $v->id  )}}
                                    {{ Form::close() }}
                                    @endpermission
                                    <a href="{{ route($prefix . '.'.$module.'.edit', ['user' => $v->id ]) }}" type="button" class="btn btn-info btn-xs pull-right m-l-xs"><i class="fa fa-edit"></i><span class="hidden-xs"> </span></a>

                                    <a type="button" onclick="event.preventDefault(); document.getElementById('active-form-{{$v->id}}').submit();"
                                       data-toggle="tooltip" data-placement="top"
                                       @if($v->active == 1)
                                       class="btn btn-success  btn-xs pull-right" title="Deaktivovať" ><i class="fa fa-check"></i>
                                        @else
                                            class="btn btn-danger  btn-xs" title="Aktivovať"><i class="fa fa-close"></i>
                                        @endif
                                    </a>

                                    {{ Form::open(['method' => 'PUT', 'route' => [$prefix . '.'. $module .'.active', $v->id ],
                                        'class' => 'class="m-l-sm pull-right btn btn-danger btn-xs hide',
                                        'id' => 'active-form-'. $v->id  ])
                                    }}
                                    {{Form::hidden('user_id', $v->id  )}}

                                    @if($v->active == 1)
                                        {{Form::hidden('active', 0 )}}
                                    @else
                                        {{Form::hidden('active', 1  )}}
                                    @endif
                                    {{ Form::close() }}

                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div id="faq{{$v->id}}" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
                                    <div class="faq-answer">
                                        <p>
                                            {!! nl2br($v->description) !!}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>

@endsection

@section('css')

@endsection

@section('scripts')


    <script>

        $(document).ready(function(){

            $('#faq-accord').on('shown.bs.collapse', function() {
                console.log("shown");
            }).on('show.bs.collapse', function() {
                console.log("show");
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

        });

    </script>

@endsection