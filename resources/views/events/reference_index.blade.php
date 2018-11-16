@extends('layouts.app')

@section('title', 'Raňajky klubu')

@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">

                    @include('events.components.header_event')

                    @include('components.validation')

                    <div class="ibox-content">
                        <form id="reference_add" class="" method="POST" action="{{ route('events.reference.store')}}">

                            {{Form::hidden('event_id',  $event->id )}}
                            {{Form::token()}}

                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label class="control-label" for="reference_type">Typ ústrižku</label>
                                    <select name="reference_type" id="reference_type" class="form-control" required>
                                        <option value="" >-- výber --</option>
                                        @foreach($reference_type as $r)
                                            <option value="{{$r->id}}">{{$r->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="control-label" for="user_to">Komu</label>
                                    <select name="user_to" id="user_to" class="form-control chosen-select">
                                        <option value="" >-- výber --</option>
                                        @foreach($attendance as $a)
                                            <option value="{{$a->user_id}}">{{$a->surname}} {{$a->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="control-label" for="user_from">Od koho</label>
                                    <select name="user_from" id="user_from" class="form-control">
                                        <option value="" >-- výber --</option>p
                                        @foreach($attendance as $a)
                                            <option value="{{$a->user_id}}">{{$a->surname}} {{$a->name}} </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="control-label" for="price">Cena </label>
                                    <input type="number" value="0" class="form-control" name="price">
                                </div>
                            </div>
                            <div class="col-sm-5">
                                <div class="form-group">
                                    <label class="control-label" for="description">Poznámka</label>
                                    <textarea rows="4" class="form-control" name="description"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label class="control-label block" for="submit">&nbsp;</label>
                                    <input type="submit" class="btn btn-primary" value="{{__('form.save')}}">
                                </div>
                            </div>
                        </div>
                        </form>

                        <hr>

                        @if(count($items)>0)
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>Od</th>
                                <th>Komu</th>
                                <th class="text-right">Hodnota</th>
                                <th>Dátum</th>
                                <th>Popis transakcie</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                                @foreach($items as $item => $v)
                                <tr>
                                    <td><strong>@if($v->from_name ){{ $v->from_name }} {{ $v->from_surname }}@else Hosť @endif</strong></td>
                                    <td><strong>@if($v->to_name ){{ $v->to_name }} {{ $v->to_surname }}@else Hosť @endif</strong></td>
                                    <td align="right" @if($v->price < 0)class="text-danger" @else class="text-success"  @endif><strong>{{ number_format((float)$v->price, 2, ',', ' ') }}</strong></td>
                                    <td>{{ Carbon\Carbon::createFromFormat('Y-m-d',$v->date)->format('d.m.Y') }}</td>
                                    <td>{{ $v->ref_name }}</td>
                                    <td>
                                        <a href="{{route('events.reference.edit', $v->id)}}" type="button" class="m-l-sm  btn btn-success btn-xs pull-right"><i class="fa fa-edit"></i> </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @else
                            <p class="text-danger">Neexistujú žiadne záznamy</p>
                        @endif

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

            $("#reference_type").change( function (){

                var reference_type = $(this).val();
                var club_id = {!! $event->club_id !!};
                var event_id = {!! $event->id !!};

                $.ajax({
                    type:'POST',
                    url:'/ajax/function',
                    data: {
                        action: "get_members_attend",
                        club_id: club_id,
                        reference_type: reference_type,
                        event_id: event_id
                    },
                    dataType: 'json',
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    success:function(data){
                        if(data){

                            $("#user_to").empty();

                            var option = $('<option></option>').attr("value", "").text("-- výber --");
                            $("#user_to").append(option);

                            $.each(data, function(key) {
                                var option = $('<option></option>').attr("value", data[key].user_id ).text(data[key].user_name);
                                $("#user_to").append(option).trigger("chosen:updated");

                            });

                        }

                    }

                });

            });

            $("#reference_add").validate({
                rules: {
                    user_from: {
                        required: true,
                        notEqualTo: user_to
                    },
                    user_to: {
                        required: true,
                        notEqualTo: user_from
                    }
                },

                messages:{
                    user_from: {
                        required: "Meno je povinné",
                        notEqualTo: 'Ústrižok nemôže odovzdať sám sebe :)'
                    },
                    user_to: {
                        required: "Meno je povinné",
                        notEqualTo: 'Ústrižok nemôže odovzdať sám sebe :)'
                    }
                }

            });

        });

    </script>

@endsection