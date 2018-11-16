@extends('layouts.app')

@section('title', 'Applications')

@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Backend menu </h5>
                    </div>
                    <div class="ibox-content">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Invited by</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($applications as $item)
                                <tr>
                                    <td>{{$item->id}}</td>
                                    <td>{{$item->data->name}} {{$item->data->surname}}</td>
                                    <td>{{$item->name}}</td>
                                    <td>
                                        <a href="{{url('applications/'.$item->id)}}" type="button" class="pull-right btn btn-success btn-xs">Detail</a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
