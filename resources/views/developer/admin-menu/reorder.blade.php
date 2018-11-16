@extends('layouts.app')

@section('title', 'Generovanie backend menu')

@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Horizontal form</h5>
                    </div>
                    <div class="ibox-content">
                        <div class="row">
                            <div class="col-md-8 col-md-offset-2">
                                <div class="dd" id="nestable"></div>
                                <p><strong>Serialised Output (per list)</strong></p>
                                <textarea id="nestable-output"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('css')

    <link rel="stylesheet" href="{!! asset('js/plugins/nestable2/jquery.nestable.min.css') !!}" />

@endsection

@section('scripts')

    <script src="{!! asset('js/plugins/nestable2/jquery.nestable.min.js') !!}" type="text/javascript"></script>

<script>
    $(document).ready(function(){


        var updateOutput = function(e) {
            var list = e.length ? e : $(e.target),
                output = list.data('output');

            if(window.JSON) {
                output.val(window.JSON.stringify(list.nestable('serialize')));//, null, 2));
            }
            else {
                output.val('JSON browser support required.');
            }

            $.ajax({
                type:'POST',
                url:'/ajax/backend-menu-update',
                data: { rank: output.val()},
                dataType: 'json',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                success:function(ajax_data){
                    //$("#msg").html(data.msg);
                    toastr.success(ajax_data.msg)

                }
            });
        };

        var json = {!! json_encode($menu) !!}


        // activate Nestable for list 1
        $('#nestable').nestable({
            group: 1,
            json: json,
            maxDepth: 3,
            contentCallback: function(item) {
                var content = item.content || '' ? item.content : item.id;
                //content += ' <i>(id = ' + item.id + ')</i>';
                return content;
            }
        }).on('change', updateOutput);

        // output initial serialised data
        updateOutput($('#nestable').data('output', $('#nestable-output')));

        $('#nestable-menu').on('click', function(e) {
            var target = $(e.target),
                action = target.data('action');
            if(action === 'expand-all') {
                $('.dd').nestable('expandAll');
            }
            if(action === 'collapse-all') {
                $('.dd').nestable('collapseAll');
            }
        });


        $("#title").on('blur keyup change click input', function () {
            var txtClone = $('#title').val();
            $('#block').val(webalize(txtClone));
            $('#permission_name').val(webalize('menu-'+txtClone));
            txtClone = txtClone.charAt(0).toUpperCase() + txtClone.substr(1);
            $('#permission_display').val('Menu '+txtClone);
            $('#permission_description').val('Permission from menu '+txtClone);
        });

    });


</script>

@endsection