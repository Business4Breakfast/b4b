@extends('layouts.app')

@section('title', 'Pridanie nového užívateľa' )

@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                    </div>
                    <div class="ibox-content">

                        @include('components.validation')

                        <form id="membership_form" class="form-horizontal" method="POST" action="{{ route('setting.membership.store') }}">
                            {{ csrf_field() }}

                            <div class="form-group{{ $errors->has('company_id') ? ' has-error' : '' }}">
                                <label for="company_id" class="col-md-4 control-label">Spoločnosť</label>
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <select id="company_id" class="chosen-select  chosen-select-no-single chosen-select-deselect form-control" name="company_id"
                                                style="width: 400px;" data-placeholder="Výber platiteľa..." >
                                            <option value="">Výber platiteľa...</option>
                                            @foreach($companies as $company)
                                                <option value="{{$company->id}}" data-email="{{$company->email}}">{{$company->company_name}} - [ {{$company->ico}} ]</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @if ($errors->has('company_id'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('company_id') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('valid_from') ? ' has-error' : '' }} date_picker_year" >
                                <label for="valid_from" class="col-md-4 control-label">Členstvo od</label>
                                <div class="col-md-3">
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
                            <div class="form-group{{ $errors->has('valid_to') ? ' has-error' : '' }} date_picker_year" >
                                <label for="valid_to" class="col-md-4 control-label">Členstvo do</label>
                                <div class="col-md-3">
                                    <div class="input-group date">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input type="text" class="form-control date_mask" value="{{ Carbon\Carbon::now()->addYears(1)->subDay(1)->format('d.m.Y') }}" name="valid_to" >
                                    </div>
                                    @if ($errors->has('valid_to'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('valid_to') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('price') ? ' has-error' : '' }}">
                                <label for="price" class="col-md-4 control-label">Cena ročného členstva €</label>
                                <div class="col-md-2">
                                    <input id="price" type="number" class="form-control" name="price" value="{{ env('MEMBERSHIP')}}"  required>
                                    @if ($errors->has('price'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('price') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('user') ? ' has-error' : '' }}">
                                <label for="user" class="col-md-4 control-label">Užívatelia</label>
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <select name="user[]" id="user" data-placeholder="Výber užívateľa..." class="chosen-member" multiple style="width:400px;"  required>
                                            @foreach($users as $key =>$user)
                                                <option value="{{$user->id}}">{{$user->full_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @if ($errors->has('user'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('user') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('club') ? ' has-error' : '' }}">
                                <label for="club" class="col-md-4 control-label">Kluby</label>
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <select name="club[]" data-placeholder="Výber klubu..." class="chosen-member" multiple style="width:400px;"  required>
                                            @foreach($clubs as $key =>$club)
                                                <option value="{{$club->id}}">{{$club->short_title}} ({{$club->active}})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @if ($errors->has('club'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('club') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label"></label>
                                <div class="col-sm-5">
                                    <div class="checkbox">
                                        <input type="hidden" name="invoice" value="0">
                                        <input type="checkbox" id="invoice" name="invoice" value="1" class="form-control" checked="checked">
                                        <label for="checkbox1">
                                            Vystaviť proforma fakturu
                                        </label>
                                    </div>
                                    <div class="form-group{{ $errors->has('price') ? ' has-error' : '' }}" id="div_pay_to_day">
                                        <label for="price" class="col-md-4 control-label">Splatnosť (dní)</label>
                                        <div class="col-md-3">
                                            <select name="pay_to_day" class="form-control">
                                                @for ($i = 10; $i <= 60; $i=$i+10)
                                                    <option value="{{ $i }}" @if($i == 10) selected="selected" @endif>{{ $i }}</option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>
                                    <div class="checkbox">
                                        <input type="hidden" name="send_email" value="0">
                                        <input type="checkbox" name="send_email" value="1" class="form-control" checked="checked">
                                        <label for="checkbox1">
                                            Odoslať emailom
                                        </label>
                                    </div>
                                    <div class="checkbox">
                                        <input type="hidden" name="divide_50" value="0">
                                        <input type="checkbox" name="divide_50" id="divide_50" value="1" class="form-control" >
                                        <label for="checkbox1">
                                            Rozdeliť platbu 50%
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('price') ? ' has-error' : '' }} hide" id="div_divide_month">
                                <label for="price" class="col-md-4 control-label">Druhá faktúra (mesiece)</label>
                                <div class="col-md-1">
                                    <select name="divide_month" class="form-control">
                                        @for ($i = 1; $i <= 6; $i++)
                                            <option value="{{ $i }}" @if($i == 2) selected="selected" @endif>{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                <label for="email" class="col-md-4 control-label">Adresa na zaslanie</label>
                                <div class="col-md-4">
                                    <input id="email" type="email" class="form-control" name="email" value=""  required>
                                    @if ($errors->has('email'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('email') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }} hide" id="div_form_group_other_email">
                                <label for="email" class="col-md-4 control-label">Ďalšie adresy na zaslanie</label>
                                <div class="col-md-4" >
                                    <div id="other_emails"></div>
                                </div>
                            </div>
                            <hr>
                            <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                                <label for="description" class="col-md-4 control-label">Poznámka</label>
                                <div class="col-md-5">
                                    <textarea id="description" class="form-control" name="description" rows="4">{{ old('description') }}</textarea>
                                    @if ($errors->has('description'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('description') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-6 col-md-offset-4">
                                    <button type="submit" class="btn btn-primary">
                                        Registrácia
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

@section('scripts')

    {{--<script src="{!! asset('js/plugins/pwstrength/pwstrength-bootstrap.js') !!}" type="text/javascript"></script>--}}

    <script>
        $(document).ready(function (){

            $('.date_mask').mask('00.00.0000');

            $('.chosen-member').chosen({
                'width': '300px'
            });

            $('#user').on('change', function(e, params) {
                var id  = params.selected;

                if(params.deselected > 0){

                    removeEmailTextbox(params.deselected);

                }else{

                    $.ajax({
                        type:'POST',
                        url:'/ajax/get-user-data',
                        data: { user_id: id},
                        dataType: 'json',
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        success:function(data){
                            if(data.status == 'true'){
                                addEmailTextbox(data.data.email, id);
                            }
                        }
                    });

                }
            });

            function addEmailTextbox(email, id) {
                $('#div_form_group_other_email').removeClass('hide').addClass('show');
                $('<div/>').addClass( 'input-group' )
                    .append('<input id="email_more" type="email" class="form-control" name="email_more[]" value="' + email + '"  required>')
                    .append('<span class="input-group-btn"><button type="button" class="btn btn-default gray-bg remove" id="remove_' + id + '" >Zmazať</button></span>')
                    .insertBefore( $('#other_emails') );
            }

            //zmazanie inputpoxu pri odobrani usera
            function removeEmailTextbox(id){
                $('#remove_'+id).closest( 'div.input-group' ).remove();
                //ak uz neexistuje, skryjeme cely div s adresami
                if (!$('#email_more').length > 0) {
                    $('#div_form_group_other_email').removeClass('show').addClass('hide');
                }
            }

            //vymatanie textboxu s adresou
            $(document).on('click', 'button.remove', function( e ) {
                e.preventDefault();
                $(this).closest( 'div.input-group' ).remove();
                //ak uz neexistuje, skryjeme cely div s adresami
                if (!$('#email_more').length > 0) $('#div_form_group_other_email').removeClass('show').addClass('hide');
            });

            $("#company_id").chosen().change(function(){
                var id = $(this).val();
                $.ajax({
                    type:'POST',
                    url:'/ajax/get-company-data',
                    data: { company_id: id},
                    dataType: 'json',
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    success:function(data){
                        if(data.status == 'true'){
                            $('#email').val(data.data.email);
                        } else {
                            $('#email').val("");
                        }
                    }
                });
            });

            $('#divide_50').change(function() {
                if ($('#div_divide_month').hasClass('hide')) {
                    $('#div_divide_month').removeClass('hide').addClass('show');
                }else{
                    $('#div_divide_month').removeClass('show').addClass('hide');
                }
            });

            $('#invoice').change(function() {
                if ($('#div_pay_to_day').hasClass('hide')) {
                    $('#div_pay_to_day').removeClass('hide').addClass('show');
                }else{
                    $('#div_pay_to_day').removeClass('show').addClass('hide');
                }
            });

            $.validator.setDefaults({ ignore: ":hidden:not(.chosen-member)" });
            $("#membership_form").validate({
                rules: {
                    phone: {
                        required: true,
                        minlength: 13
                    },
                    company_id: "required"
                }
            });

        });
    </script>
@endsection