@extends('layouts.app_public')

@section('title', '')

@section('content')

    <div class="wrapper wrapper-content">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 col-lg-offset-2">
                    <div class="ibox float-e-margins">

                        @include('components.validation')

                        <div class="jumbotron  bg-@if($text['bg_jumbotron_']){{$text['bg_jumbotron_']}}@else success @endif-df">
                            <h1>{{$text['title']}}</h1>
                            <h2>{{$user->name}} {{$user->surname}}</h2>

                            <p>Dakujeme za Vašu reakciu.</p>
                            <p><a href="{{url('//bforb.sk')}}" role="button" class="btn btn-@if($text['bg_jumbotron_']){{$text['bg_jumbotron_']}}@else success @endif btn-lg">Pokračuj na BFORB</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')

    <script>
        $(document).ready(function (){

            setTimeout(function() {
                window.location.href = "{{url('//bforb.sk')}}"
            }, 4000);

        });
    </script>
@endsection