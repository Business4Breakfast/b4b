@extends('layouts.app')

@section('title', '')

@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="ibox-content">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="m-b-md">
                                            <h2>{{str_limit($issue->description, 30 )}}</h2>
                                        </div>
                                        <dl class="dl-horizontal">
                                            <dt>Status:</dt>
                                            <dd>
                                                @if($issue->status == 0)
                                                    <span class="label label-danger">{{__('constant.bug_report_status.0')}}</span>
                                                @elseif($issue->status == 1)
                                                    <span class="label label-warning">{{__('constant.bug_report_status.1')}}</span>
                                                @elseif($issue->status == 10)
                                                    <span class="label label-success">{{__('constant.bug_report_status.10')}}</span>
                                                @else
                                                    <span class="label label-warning">Chybmý status</span>
                                                @endif
                                            </dd>
                                        </dl>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-5">
                                        <dl class="dl-horizontal">
                                            <dt>Vytvoril:</dt> <dd>{{$issue->name}} {{$issue->surname}}</dd>
                                        </dl>
                                    </div>
                                    <div class="col-lg-7" id="cluster_info">
                                        <dl class="dl-horizontal">
                                            <dt>Aktualizácia:</dt>
                                            <dd>{{\Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $issue->updated_at)->format('d.m.Y - H:i')}}</dd>
                                            <dt>Vytvorené:</dt>
                                            <dd>{{\Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $issue->created_at)->format('d.m.Y - H:i')}} </dd>
                                        </dl>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <dl class="dl-horizontal">
                                            <dt>Úloha:</dt>
                                            <dd>
                                                <p>{!! nl2br($issue->description) !!}.</p>
                                            </dd>
                                            <dt>Dokončené:</dt>
                                            <dd>
                                                <small>Dokončené: {{$issue->progres}}%</small>
                                                <div class="progress" style="height: 10px;">
                                                    <div style="width: {{$issue->progres}}%" aria-valuemax="100" aria-valuemin="0" aria-valuenow="{{$issue->progres}}" role="progressbar" class="progress-bar progress-bar-success">
                                                        <span class="sr-only">Dokončené {{$issue->progres}}%</span>
                                                    </div>
                                                </div>
                                            </dd>
                                        </dl>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-offset-3 col-md-5">
                                        @if(file_exists('images/bug-report/' . $issue->id . '/' .$issue->image))
                                            <a href="{!! asset('images/bug-report') !!}/{{$issue->id}}/large/{{$issue->image}}" data-fancybox>
                                                <img src="{!! asset('images/bug-report') !!}/{{$issue->id}}/sq/{{$issue->image}}" class="img-fluid img-responsive">
                                            </a>
                                        @endif
                                    </div>
                                </div>
                                <div class="row m-t-sm">
                                    <div class="col-lg-12">
                                        <div class="panel-body">
                                            <div class="feed-activity-list">
                                                <div class="hr-line-solid"></div>
                                                @foreach($items as $i)
                                                <div class="feed-element">
                                                    @if(file_exists('images/bug-report/' . $i->id . '/' .$i->image))
                                                        <a href="{!! asset('images/bug-report') !!}/{{$i->id}}/large/{{$i->image}}" data-fancybox class="m-b-lg">
                                                            <img src="{!! asset('images/bug-report') !!}/{{$i->id}}/sq/{{$i->image}}" class="img-fluid img-responsive m-b-lg" style="height: 300px;">
                                                        </a>
                                                    @endif
                                                    <div class="media-body">
                                                        <small class="pull-right">{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $i->created_at)->diffForHumans() }}</small>
                                                        <strong>{{$i->name}} {{$i->surname}}</strong><br>
                                                        <strong>{{Lang::get('constant.bug_report_items_status')[$i->status] }}</strong><br>
                                                        <p>{{$i->description}}</p>
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="ibox-content">
                                <h3>Aktualizácia úlohy</h3>
                                <form id="form_bug_report_detail" role="form" method="POST" action="{{ route('developer.bug-report.update', $issue->id) }}" enctype="multipart/form-data" >
                                    {{Form::token()}}
                                    {{ method_field('PATCH') }}

                                    {{Form::hidden('bug_report_route', Request::getUri() )}}
                                    <div class="form-group">
                                        <label>Status</label>
                                        <select name="status" class="form-control" id="status" @if($issue->status == 10) disabled @endif>
                                            <option value="">-- výber --</option>
                                            @foreach( __('constant.bug_report_items_status') as $k => $r)
                                                <option value="{{$k}}">{{$r}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div id="div_progres_bar" class="hide">
                                        <div class="form-group">
                                            <label>Dokončené %</label>
                                            <div id="basic_slider"></div>
                                            <input type="hidden" id="hidden_slider" name="hidden_slider" value="{{$issue->progres}}">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Popis riešenia</label>
                                        <textarea name="description" rows="6" class="form-control" required @if($issue->status == 10) disabled @endif></textarea>
                                    </div>
                                    @if($issue->status != 10)
                                    <div class="form-group m-b-lg">
                                        <label>Notifikovať ďalších užívateľov</label>
                                        <div class="input-group" style="position:fixed; z-index:99999">
                                            <select name="user[]" id="user" data-placeholder="Výber užívateľa..." class="chosen-member" multiple style="width:400px;"  required>
                                                <option value="">-- výber --</option>
                                                @foreach( $users as $k => $u)
                                                    <option value="{{$u->id}}">{{$u->full_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="m-t-md">Výrez obrazovky</label>
                                        <div class="input-group">
                                            <div class="fileinput fileinput-new" data-provides="fileinput">
                                                <div class="fileinput-new thumbnail" style="max-width: 500px; max-height: 500px;">
                                                    <p class="text-center m-t-md">
                                                        <i class="fa fa-upload big-icon"></i>
                                                    </p>
                                                </div>
                                                <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 500px; max-height: 500px;"></div>
                                                <div>
                                                <span class="btn btn-default btn-file">
                                                    <span class="fileinput-new"><i class="fa fa-paperclip"></i> {{__('form.file_select')}}</span><span class="fileinput-exists">
                                                        <i class="fa fa-undo"></i> {{__('form.new_file')}}</span>
                                                    <input type="file" name="files">
                                                </span>
                                                    <a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">
                                                        <i class="fa fa-trash"></i> {{__('form.delete')}}</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                    <div class="form-group">
                                        @if($issue->status != 10)
                                            <button type="submit" class="btn btn-success">{{__('form.create')}}</button>
                                        @endif
                                    </div>
                                </form>
                            </div>
                        </div>
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


    <script src="{!! asset('js/plugins/fullcalendar/fullcalendar.min.js') !!}" type="text/javascript"></script>
    <!-- TouchSpin -->
    <script src="{!! asset('js/plugins/touchspin/jquery.bootstrap-touchspin.min.js') !!}" type="text/javascript"></script>
    <!-- NouSlider -->
    <script src="{!! asset('js/plugins/nouslider/jquery.nouislider.min.js') !!}" type="text/javascript"></script>


    <script>

        $(document).ready(function(){

            var start_value = 0;

            if($("#hidden_slider").length){
                if($("#hidden_slider").val() > 0){
                    start_value = parseInt($("#hidden_slider").val());
                }
            }


            $('.chosen-member').chosen({
                'width': '300px'
            });

//            $('#user').on('change', function(e, params) {
//                var id  = params.selected;
//                $.ajax({
//                    type:'POST',
//                    url:'/ajax/get-user-data',
//                    data: { user_id: id},
//                    dataType: 'json',
//                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
//                    success:function(data){
//                        if(data.status == 'true'){
//
//                            console.log(data);
//                            //addEmailTextbox(data.data.email, id);
//                        }
//                    }
//                });
//
//                if(params.deselected > 0){
//                    //removeEmailTextbox(params.deselected);
//                }
//            });

            //skryvanie progres baru
            $('#status').change(function () {

                if($(this).val() == 2 ){
                    $('#div_progres_bar').removeClass('hide').addClass('show');
                }else{
                    $('#div_progres_bar').removeClass('show').addClass('hide');
                }
            })

            $("#basic_slider").noUiSlider({

                start: start_value,
                behaviour: 'tap',
                connect: 'lower',
                step: 1,
                range: {
                    'min':  0,
                    'max':  100
                },

                create: function(event, ui){
                    $(this).slider('value', 6);
                }
            });

            $("#basic_slider").on('slide', function(event, values) {
                $("#hidden_slider").val(values);
            });

            $("#form_bug_report_detail").validate({
                rules: {
                    status: "required"
                }
            });


            $(document).on('click', '[data-toggle="lightbox"]', function(event) {
                event.preventDefault();
                $(this).ekkoLightbox();
            });

            $("#form_bug_report_detail").validate({
                rules: {
                    bug_type: "required"
                }
            });

        });

    </script>

@endsection