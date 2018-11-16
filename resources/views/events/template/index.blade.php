@extends('layouts.app')

@section('title', 'Sablona emailu')

@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">


        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">

                    @include('events.components.header_event')

                    @include('components.validation')

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="ibox float-e-margins">
                                <div class="ibox-content p-md">
                                    <div class="list-group connectedSortable" id="sortable1">


                                        <div class="list-group-item" id="item_1">
                                            <div class="input-group m-b">
                                                <span class="input-group-addon">
                                                    <input type="checkbox">
                                                </span>
                                                <textarea class="form-control" name="content" id="markdown_1" rows="6">test</textarea>
                                            </div>
                                        </div>

                                        @if($event->type->btn_confirm_attend == 1)
                                        <div class="list-group-item" href="#" id="btn_confirm_attend">
                                                <button class="btn btn-success m-l-lg"> Potvrdzujem svoju účasť</button>
                                        </div>
                                        @endif
                                        @if($event->type->btn_refused_attend == 1)
                                            <div class="list-group-item" href="#" id="btn_refused_attend">
                                                <button class="btn btn-danger m-l-lg"> Ospravedlňujem svoju neúčasť</button>
                                            </div>
                                        @endif
                                        @if($event->type->btn_invite_guest == 1)
                                            <div class="list-group-item" href="#" id="btn_invite_guest">
                                                <button class="btn btn-warning m-l-lg"> Pozvať hosťa</button>
                                            </div>
                                        @endif

                                        <div class="list-group-item" id="item_1">
                                            <form class="form-group">
                                                <div class="input-group m-b">
                                                    <span class="input-group-addon">
                                                        <input type="checkbox">
                                                    </span>
                                                    <textarea class="form-control" name="content" id="markdown_2" rows="6"><h1>test 33</h1></textarea>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="list-group-item" href="#" id="item_2">
                                            <h3 class="list-group-item-heading">Why painful the sixteen how minuter</h3>

                                            <p class="list-group-item-text">I throw myself down among the tall grass by the trickling stream; and, as I lie close to the earth, a thousand unknown plants are noticed by me: when I hear the buzz of the little world among the stalks, and grow familiar with the countless indescribable forms of the insects and flies, then I feel the presence of the Almighty, who formed us in his own image, and the breath </p>
                                        </div>

                                        <div class="list-group-item" href="#" id="item_3">
                                            <h3 class="list-group-item-heading">Barton waited twenty always repair</h3>
                                            <p class="list-group-item-text">I never was a greater artist than now. When, while the lovely valley teems with vapour around me, and the meridian sun strikes the upper surface of the impenetrable foliage of my trees, and but a few stray gleams steal into the inner sanctuary</p>
                                        </div>

                                        @if($event->type->btn_deleted_guest == 1)
                                            <div class="list-group-item" href="#" id="btn_deleted_guest">
                                                {{--<div class="input-group">--}}
                                                <button class="btn btn-info m-l-lg"> Nechcem už dostávať pozvánky</button>
                                                {{--</div>--}}
                                            </div>
                                        @endif

                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="ibox float-e-margins">
                                <div class="ibox-content p-md">
                                    <div class="list-group">

                                        <div class="list-group-item" href="#">
                                            <h3 class="list-group-item-heading">Why painful the sixteen how minuter</h3>

                                            <p class="list-group-item-text">I throw myself down among the tall grass by the trickling stream; and, as I lie close to the earth, a thousand unknown plants are noticed by me: when I hear the buzz of the little world among the stalks, and grow familiar with the countless indescribable forms of the insects and flies, then I feel the presence of the Almighty, who formed us in his own image, and the breath </p>
                                        </div>

                                        <div class="list-group-item" href="#">
                                            <h3 class="list-group-item-heading">Barton waited twenty always repair</h3>
                                            <p class="list-group-item-text">I never was a greater artist than now. When, while the lovely valley teems with vapour around me, and the meridian sun strikes the upper surface of the impenetrable foliage of my trees, and but a few stray gleams steal into the inner sanctuary</p>
                                        </div>

                                        <div class="list-group-item" href="#">
                                            <h3 class="list-group-item-heading">Why painful the sixteen how minuter</h3>

                                            <p class="list-group-item-text">I throw myself down among the tall grass by the trickling stream; and, as I lie close to the earth, a thousand unknown plants are noticed by me: when I hear the buzz of the little world among the stalks, and grow familiar with the countless indescribable forms of the insects and flies, then I feel the presence of the Almighty, who formed us in his own image, and the breath </p>
                                        </div>

                                        <div class="list-group-item" href="#">
                                            <h3 class="list-group-item-heading">Barton waited twenty always repair</h3>
                                        </div>
                                    </div>


                                </div>
                            </div>
                        </div>


                    </div>




                </div>
            </div>
        </div>
    </div>

@endsection

@section('css')

    <!-- Bootstrap markdown -->
    <link rel="stylesheet" href="{!! asset('css/plugins/bootstrap-markdown/bootstrap-markdown.min.css') !!}" />

@endsection


@section('scripts_before')

    <script src="{!! asset('js/plugins/jquery-ui/jquery-ui.js') !!}"  type="text/javascript"></script>

@endsection



@section('scripts')

    <!-- Bootstrap markdown -->
    <script src="{!! asset('js/plugins/bootstrap-markdown/bootstrap-markdown.js') !!}"  type="text/javascript"></script>
    <script src="{!! asset('js/plugins/bootstrap-markdown/markdown.js') !!}"  type="text/javascript"></script>


    <script>

        $(document).ready(function(){

            $("#markdown_1, #markdown_2").markdown(
                    {   autofocus:false,
                        savable:false,
                        editable:true,
                        hideable: false,

                        // onShow: function(e) {
                        //     showPreview('all');
                        // },

                        onPreview: function(e) {

                            //e.hideButtons("all");
                        //
                        console.log(e.showPreview());
                        //
                        //     var previewContent
                        //
                        //     if (e.isDirty()) {
                        //         var originalContent = e.getContent()
                        //
                        //         previewContent = "Prepended text here..."
                        //             + "\n"
                        //             + originalContent
                        //             + "\n"
                        //             +"Apended text here..."
                        //     } else {
                        //         previewContent = "Default content"
                        //     }
                        //
                        //     return previewContent
                        }

                    }
            );

            $('#markdown_1').data('markdown').showPreview();

            //$('#markdown_1').data('markdown').showEditor();



            $( "#sortable1" ).sortable({
                connectWith: ".connectedSortable",
                update : function () {

                    var order1 = $('#sortable1').sortable('toArray').toString();

                    console.log("Order 1:"+order1);

                }
            }).disableSelection();

        });

    </script>

@endsection