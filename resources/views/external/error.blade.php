@extends('layouts.app_public')

@section('title', '')

@section('content')

    <div class="wrapper wrapper-content">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 col-lg-offset-2">
                    <div class="ibox float-e-margins">
                        <div class="ibox-content">

                            @include('components.validation')

                            <div class="jumbotron">
                                <h1>Neexistujúca stránka</h1>
                                @if(isset($message))<h3 class="text-danger">{{$message}}</h3>@endif
                                <p>Ďakujeme za Vašu reakciu.</p>
                                <p><a role="button" href="{{__('app.web')}}" class="btn btn-danger btn-lg" >Pokračuj na BFORB</a>
                                </p>
                            </div>

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


        });
    </script>
@endsection