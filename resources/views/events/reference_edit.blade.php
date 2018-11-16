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
                        <form id="reference_add" class="" method="POST" action="{{ route('events.reference.update', $coupon->id )}}">

                            {{Form::hidden('event_id',  $event->id )}}
                            {{Form::token()}}
                            {{method_field("PATCH")}}

                            <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label class="control-label" for="user_from">Od koho</label>
                                    <select name="user_from" id="user_from" class="form-control">
                                    @foreach($attendance as $a)
                                            <option value="{{$a->user_id}}" @if($coupon->user_from == $a->user_id) selected="selected" @endif>{{$a->surname}} {{$a->name}} </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="control-label" for="user_to">Komu</label>
                                    <select name="user_to" id="user_to" class="form-control">
                                        @foreach($attendance as $a)
                                            <option value="{{$a->user_id}}" @if($coupon->user_to == $a->user_id) selected="selected" @endif>{{$a->surname}} {{$a->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="control-label" for="reference_type">Typ ústrižku</label>
                                    <select name="reference_type" id="reference_type" class="form-control" required>
                                        @foreach($reference_type as $r)
                                            <option value="{{$r->id}}" @if($coupon->reference_type == $r->id) selected="selected" @endif>{{$r->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="control-label" for="price">Cena </label>
                                    <input type="number" value="{{$coupon->price}}" class="form-control" name="price">
                                </div>
                            </div>
                            <div class="col-sm-5">
                                <div class="form-group">
                                    <label class="control-label" for="description">Poznámka</label>
                                    <textarea rows="4" class="form-control" name="description">{{$coupon->description}}</textarea>
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