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

                        <form id="membership_form" class="form-horizontal" method="POST" action="{{ route('franchisor.store') }}">
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
                                <label for="valid_from" class="col-md-4 control-label">Franchísa od</label>
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
                                <label for="valid_to" class="col-md-4 control-label">Franchísa do</label>
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
                                <label for="price" class="col-md-4 control-label">Cena franchísy €</label>
                                <div class="col-md-2">
                                    <input id="price" type="number" class="form-control" name="price" value=""  required>
                                    @if ($errors->has('price'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('price') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('user') ? ' has-error' : '' }}">
                                <label for="user" class="col-md-4 control-label">Zodpovedná osoba</label>
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <select name="user" id="user"  class="form-control chosen-select"   required>
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


            $.validator.setDefaults({ ignore: ":hidden:not(.chosen-select)" });
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