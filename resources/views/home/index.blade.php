@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-3">
                <div class="widget style1 bg-danger">
                    <div class="row">
                        <div class="col-xs-4 text-center">
                            <i class="fa fa-trophy fa-5x"></i>
                        </div>
                        <div class="col-xs-8 text-right">
                            <span> Celkové píjmy </span>
                            <h2 class="font-bold">4,232 €</h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="widget style1 navy-bg">
                    <div class="row">
                        <div class="col-xs-4">
                            <i class="fa fa-group fa-5x"></i>
                        </div>
                        <div class="col-xs-8 text-right">
                            <span> Počet klubov </span>
                            <h2 class="font-bold">26'C</h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="widget style1 lazur-bg">
                    <div class="row">
                        <div class="col-xs-4">
                            <i class="fa fa-bank fa-5x"></i>
                        </div>
                        <div class="col-xs-8 text-right">
                            <span> Počet členov </span>
                            <h2 class="font-bold">260</h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="widget style1 yellow-bg">
                    <div class="row">
                        <div class="col-xs-4">
                            <i class="fa fa-coffee fa-5x"></i>
                        </div>
                        <div class="col-xs-8 text-right">
                            <span> Počet meetingov </span>
                            <h2 class="font-bold">12</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="text-center m-t-lg">
                    <h1>
                        Welcome to Bussiness for Breakfast Slovakia
                    </h1>
                    <small>
                        The Best networking franchisor on The Universe.
                    </small>
                </div>
            </div>
        </div>
    </div>
@endsection
