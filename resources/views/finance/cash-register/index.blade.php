@extends('layouts.app')

@section('title', 'Zoznam faktúr')

@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <div class="row">
                            <form id="user_form" class="" method="GET" action="{{ route('finance.cash-register.index') }}" >
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label class="control-label" for="search_category">Kategória</label>
                                        <select name="search_category" id="search_status" class="form-control">
                                            <option value=""  selected="selected" >Všetky</option>
                                            <option value="1"  @if($req['search_category']  == 1)  selected="selected" @endif >Príjmy</option>
                                            <option value="2"  @if($req['search_category']  == 2)  selected="selected" @endif >Výdaje</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label class="control-label" for="search_type">Transakcia typ</label>
                                        <select id="search_type" class="form-control" name="search_type">
                                            <option value="0">Všetky</option>
                                            @foreach($type as $t)
                                                <option value="{{$t->id}}" @if($req['search_type'] == $t->id) selected="selected" @endif>{{$t->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label class="control-label" for="search_description">Text popisu</label>
                                        <input type="text" id="search_description" name="search_description" value="{{$req['search_description']}}" placeholder="" class="form-control">
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label class="control-label" for="search_amount">Číslo/Suma</label>
                                        <input type="text" id="search_amount" name="search_amount" value="{{$req['search_amount']}}" placeholder="" class="form-control">
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <label class="control-label block" for="submit">&nbsp;</label>
                                    <input type="submit" class="btn btn-primary" value="Filtrovať">
                                    <a href="{{route('finance.cash-register.index', ['reset' => true])}}" class="btn btn-danger"><i class="fa fa-close"></i> </a>

                                    <a data-toggle="collapse" href="#div_form_hide"  aria-expanded="false" aria-controls="collapseExample" class="btn btn-success pull-right"> Vložiť položku</a>

                                </div>
                            </form>
                        </div>
                    </div>
                        <div class="collapse @if(count($errors) > 0 ) in @endif" id="div_form_hide">
                            <div class="ibox float-e-margins">
                                <div class="ibox-content">
                                    <h3>Pridanie položky</h3>
                                    @include('components.validation')
                                    <form id="cash_add" class="form-horizontal" method="POST" action="{{ route('finance.cash-register.store') }}" enctype="multipart/form-data">
                                        {{ csrf_field() }}

                                        <div class="form-group{{ $errors->has('date_payment') ? ' has-error' : '' }} date_picker_year" >
                                            <label for="date_payment" class="col-md-4 control-label">Dátum</label>
                                            <div class="col-md-3">
                                                <div class="input-group date">
                                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                    <input type="text" class="form-control date_mask" value="{{ Carbon\Carbon::now()->format('d.m.Y') }}" name="date_payment" >
                                                </div>
                                                @if ($errors->has('date_payment'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('date_payment') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-4 control-label">Typ transakcie</label>
                                            <div class="col-sm-4">
                                                <select class="form-control" name="type" id="type" required>
                                                    <option value="">-- výber --</option>
                                                    @foreach($type as $t)
                                                        <option value="{{$t->id}}" @if(old('section_id') == $t->id) selected="selected" @endif>{{$t->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-sm-1">
                                                <a href="{{route('setting.cash-register-type.create')}}" class="btn btn-sm btn-info">Pridať novú</a>
                                            </div>
                                        </div>
                                        <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                                            <label for="title" class="col-md-4 control-label">Popis</label>
                                            <div class="col-md-6">
                                                <input id="description" type="text" class="form-control" name="description"  value="{{old('description')}}">
                                                @if ($errors->has('title'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('description') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="form-group{{ $errors->has('amount') ? ' has-error' : '' }}">
                                            <label for="price_payment" class="col-md-4 control-label money">Čiastka €</label>
                                            <div class="col-md-2">
                                                <input id="amount" type="text" class="form-control" name="amount" value=""  required>
                                                @if ($errors->has('amount'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('amount') }}</strong>
                                                    </span>
                                                @endif
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

                    <div class="ibox-content">
                        <div class="table-responsive">
                            <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Dátum</th>
                                <th>Popis transakcie</th>
                                <th>Uźívateľ</th>
                                <th class="text-right">Suma</th>
                                <th class="text-right">Stav pokladne</th>
                                <th width="200px"></th>
                            </tr>
                            </thead>
                                <tfoot>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th>Medzisúčet</th>
                                    <th class="text-right text-info">{{ number_format((float)$items->sum('amount'), 2, ',', ' ')}}</th>
                                    <th class="text-right"></th>
                                    <th width="200px"></th>
                                </tr>
                                </tfoot>
                            <tbody>
                            @if($items)
                                @foreach($items as $item => $v)
                                <tr>
                                    <td><b>CSH-{{$v->id}}</b></td>
                                    <td>{{ Carbon\Carbon::createFromFormat('Y-m-d H:i:s',$v->date_payment)->format('d.m.y H:i') }}</td>
                                    <td>{{ $v->description }}</td>
                                    <td>{{ $v->user_name }} {{ $v->user_surname }}</td>
                                    <td class="text-right">
                                        @if($v->amount < 0)
                                            <span class="text-danger">{{ number_format((float)$v->amount, 2, ',', ' ') }}</span>
                                        @else
                                            <span class="text-success">{{ number_format((float)$v->amount, 2, ',', ' ') }}</span>
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        @if($v->sub_total < 0)
                                            <strong class="text-danger">{{ number_format((float)$v->sub_total, 2, ',', ' ') }}</strong>
                                        @else
                                            <strong class="text-success">{{ number_format((float)$v->sub_total, 2, ',', ' ') }}</strong>
                                        @endif
                                    </td>
                                    <td>
                                        @role('superadministrator')
                                        <a type="button" data-item-id="{{ $v->id }}"
                                           class="m-l-sm pull-right btn btn-danger btn-xs delete-alert"><i class="fa fa-trash"></i></a>

                                        {{ Form::open(['method' => 'DELETE', 'route' => ['finance.cash-register.destroy', $v->id ],
                                            'class' => 'class="m-l-sm pull-right btn btn-danger btn-xs delete-alert hide',
                                            'id' => 'item-del-'. $v->id  ])
                                        }}
                                        {{Form::hidden('user_id', $v->id  )}}
                                        {{ Form::close() }}
                                        @endrole
                                    </td>
                                </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                        </div>
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


            $('#type').change( function () {
                $('#description').val($(this).find('option:selected').text()+' ').focus();
            })

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


            $("#cash_add").validate({
                rules: {
                    description: {
                        required: true,
                        minlength: 10
                    },
                    amount:{
                        required: true,
                        money:true
                    },
                    description: {
                        required: true,
                        minlength: 10
                    }
                }
            });

            $.validator.addMethod("money", function(value, element) {
                return this.optional(element) || value.match(/^\$?(\d+(?:\s\d+)*(?:[.,]\d{1,2})?)\s?\$?$/);
            }, "Neplatné číslo");

        });

    </script>

@endsection