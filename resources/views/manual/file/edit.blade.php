@extends('layouts.app')

@section('title', '')

@section('content')

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h3>Manual Súbory - sekcia</h3>
                </div>
                <div class="ibox-content">
                @include('components.validation')
                <form id="manual_files_add" class="form-horizontal" method="POST" action="{{ route('manual.files.update', $file->id) }}" >
                {{ csrf_field() }}
                    {{ method_field('PATCH') }}

                <div class="form-group">
                    <label class="col-sm-4 control-label">Sekcia</label>
                    <div class="col-sm-4">
                        <select class="form-control" name="section_id" required>
                            <option value="">-- výber --</option>
                            @foreach($types as $t)
                                <option value="{{$t->id}}" @if( $file->module_id == $t->id) selected="selected" @endif>{{$t->title}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label">Súbor</label>
                    <div class="col-sm-3">
                        @if(in_array($file->ext, ['jpg','png', 'jpeg']))
                            @if(file_exists($file->path . '/image/' . $file->file))
                                <a class="image" href="{!! asset($file->path . '/' . $file->file) !!}" data-fancybox class="m-b-lg">
                                    <img src="{!! asset($file->path . '/image/' . $file->file) !!}" class="img-responsive">
                                </a>
                            @endif
                        @endif
                    </div>
                </div>
                <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
                    <label for="title" class="col-md-4 control-label">Názov súboru</label>
                    <div class="col-md-6">
                        <p>{{$file->file}}</p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label">Oprávnenia</label>
                    <div class="col-sm-5">
                        <input type="hidden" name="role" value="">
                        @foreach($roles as $r)
                            <div class="checkbox">
                                <input type="checkbox" name="role[]" value="{{$r->name}}" class="form-control"
                                        @if($file_role) @foreach($file_role as $fr) @if($fr == $r->name)
                                                        checked="checked" @endif @endforeach @endif >
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
                            {{__('form.edit_record')}}
                        </button>
                    </div>
                </div>
                </form>
                </div>
            </div>
        </div>
    </div>
</div>




@endsection

@section('css')

    <link href="{!! asset('css/plugins/nouslider/jquery.nouislider.css') !!}" rel="stylesheet">


@endsection

@section('scripts')


    <script>

        $(document).ready(function(){

            $("#manual_files_add").validate({
                rules: {
                    title: "required"
                }
            });

        });

    </script>

@endsection