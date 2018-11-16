@extends('layouts.app')

@section('title', 'Nadpis')

@section('content')

    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content p-md">
                        <h3>Artisan command</h3>

                        <p>
                            <a href="{{route('developer.artisan.action', 'view-clear')}}" class="btn btn-info" > View clear</a>
                        </p>
                        <p>
                            <a href="{{route('developer.artisan.action', 'cache-clear')}}" class="btn btn-info" > Cache clear</a>
                        </p>
                        <p>
                            <a href="{{route('developer.artisan.action', 'route-cache')}}" class="btn btn-info" > Route cache</a>
                        </p>
                        <p>
                            <a href="{{route('developer.artisan.action', 'route-clear')}}" class="btn btn-info" > Route clear</a>
                        </p>
                        <p>
                            <a href="{{route('developer.artisan.action', 'config-cache')}}" class="btn btn-info" > Config cache</a>
                        </p>
                        <p>
                            <a href="{{route('developer.artisan.action', 'config-clear')}}" class="btn btn-info" > Config clear</a>
                        </p>
                        <p>
                            <a href="{{route('developer.artisan.action', 'optimize')}}" class="btn btn-warning" > Optimize</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection

@section('page_css')

@endsection


@section('scripts')

    <script>
        jQuery(window).ready(function (){

        });
    </script>


@endsection