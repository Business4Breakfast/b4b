@extends('layouts.app')

@section('title', '')

@section('content')

    <div class="wrapper wrapper-content">
        <div class="row">
            <div class="col-lg-3">
                <div class="ibox float-e-margins">
                    <div class="ibox-content">
                        <div class="file-manager">
                            @role(['superadministrator', 'administrator'])
                            <a data-toggle="collapse" href="#div_form_hide"  aria-expanded="false" aria-controls="collapseExample" class="btn btn-primary btn-block"> Nahrať súbory</a>
                            <div class="hr-line-dashed"></div>
                            @endrole
                            <h5>Adresáre</h5>
                            <ul class="folder-list" id="toolbar" style="padding: 0">
                                @foreach($types as $t)
                                    <li>
                                        {{--<span class="small pull-right">{{$t->id}}</span>--}}
                                        <a data-toggle="tab" href="#tab-{{$t->id}}" aria-expanded="false"><i class="fa {{$t->icon}}"></i>  {{$t->title}}</a>
                                    </li>
                                @endforeach
                            </ul>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-9">
            <div class="collapse @if(count($errors) > 0 ) in @endif" id="div_form_hide">
                <div class="ibox float-e-margins">
                    <div class="ibox-content">
                        <h3>Upload súboru</h3>
                        @include('components.validation')
                        <form id="manual_files_add" class="form-horizontal" method="POST" action="{{ route('manual.files.store') }}" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Sekcia</label>
                                <div class="col-sm-4">
                                    <select class="form-control" name="section_id" required>
                                        <option value="">-- výber --</option>
                                        @foreach($types as $t)
                                        <option value="{{$t->id}}" @if(old('section_id') == $t->id) selected="selected" @endif>{{$t->title}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Súbor</label>
                                <div class="col-sm-6">
                                    <input id="btn_upload" type="file" name="files[]"  data-dragdrop="true"
                                           multiple="multiple" class="filestyle" data-btnClass="btn-success" data-size="sm">
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
                                <label for="title" class="col-md-4 control-label">Názov súboru</label>
                                <div class="col-md-6">
                                    <input id="description" type="text" class="form-control" name="title"  value="{{old('title')}}">
                                    <small> Ak ostane pole prázdne použije sa pôvodný nazov súboru, inak sa súbor premenuje</small>
                                    @if ($errors->has('title'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('title') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Oprávnenia</label>
                                <div class="col-sm-5">
                                    <input type="hidden" name="role" value="">
                                    @foreach($roles as $r)
                                    <div class="checkbox">
                                        <input type="checkbox" name="role[]" value="{{$r->name}}" class="form-control" @if(in_array($r->id, [1,2,7,8])) checked="checked" @endif >
                                        <label for="checkbox1">
                                            {{$r->description}}
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-6 col-md-offset-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{__('form.add_record')}}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="tab-content ">
                @foreach($types as $t)
                    <div id="tab-{{$t->id}}" class="tab-pane @if ($loop->first) active @endif">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="ibox float-e-margins">
                                    <div class="ibox-content">
                                        @role(['superadministrator', 'administrator'])
                                            <a href="{{route('manual.files-type.edit', $t->id )}}" class="btn btn-xs btn-success pull-right"><i class="fa fa-edit"></i> Upraviť</a>
                                        @endrole
                                        <h3><i class="fa {{$t->icon}}"></i> {{$t->title}}</h3>
                                        <p>{!! nl2br($t->description) !!}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                @if($files)
                                    @foreach($files as $f)
                                        @if($f->module_id == $t->id)

                                            @role( $f->role_string )
                                            <div class="file-box">
                                                <div class="file">
                                                    <a href="{{route('manual.download.file', $f->download)}}">
                                                        <span class="corner"></span>
                                                        @if(in_array($f->ext, ['jpg','png', 'jpeg']))
                                                            @if(file_exists($f->path . '/image/' . $f->file))
                                                                <a class="image" href="{!! asset($f->path . '/' . $f->file) !!}" data-fancybox class="m-b-lg">
                                                                    <img src="{!! asset($f->path . '/image/' . $f->file) !!}" class="img-responsive">
                                                                </a>
                                                            @endif
                                                        @else
                                                            <div class="icon" style="min-height: 132px;">
                                                                <i class="fa {{$f->icon}}"></i>
                                                            </div>
                                                        @endif
                                                        <div class="file-name" style="min-height: 100px;">
                                                            <a href="{{route('manual.download.file', $f->download)}}" ><i class="fa fa-download"></i>  {{$f->short_file_name}}</a>
                                                            <br>
                                                            <small>Pridane: {{\Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $f->created_at)->format('d.m.Y H:i')}}</small>
                                                        </div>
                                                        @role(['superadministrator','administrator'])
                                                            <div class="file-name" style="min-height: 45px;">

                                                                <a href="{{route('manual.files.edit', $f->id)}}" class="m-l-xs pull-left btn btn-success btn-xs"><i class="fa fa-edit"></i><span class="hidden-xs hidden-sm"> Upraviť</span></a>
                                                                <a type="button" data-item-id="{{ $f->id }}"
                                                                   class="m-l-sm pull-right btn btn-danger btn-xs delete-alert"><i class="fa fa-trash"></i><span class="hidden-xs hidden-sm"> {{__('form.delete')}}</span></a>

                                                                {{ Form::open(['method' => 'DELETE', 'route' => ['manual.files.destroy', $f->id ],
                                                                    'class' => 'class="m-l-sm pull-right btn btn-danger btn-xs delete-alert hide',
                                                                    'id' => 'item-del-'. $f->id  ])}}
                                                                {{Form::hidden('user_id', $f->id  )}}
                                                                {{ Form::close() }}

                                                            </div>
                                                        @endrole
                                                    </a>
                                                </div>
                                            </div>
                                            @endrole
                                        @endif
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            </div>
        </div>
    </div>

@endsection

@section('css')

    <link href="{!! asset('css/animate.css') !!}" rel="stylesheet">

@endsection

@section('scripts')

    <!-- file upload -->
    <script src="{!! asset('js/plugins/filestyle/bootstrap-filestyle.js') !!}"></script>
    <script src="{!! asset('js/plugins/pace/pace.min.js') !!}"></script>

    <script>

        $(document).ready(function(){

            $("#manual_files_add").validate({
                rules: {
                    section_id: {
                        required: true
                    },
                    title:{
                        minlength: 5
                    }
                }
            });


            $('.delete-alert').click(function(e){
                e.preventDefault();

                var id = $(this).attr('data-item-id');
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

            $('#btn_upload').filestyle({
                text : 'Multiple',
                btnClass : 'btn-danger'
            });

        });

    </script>

@endsection