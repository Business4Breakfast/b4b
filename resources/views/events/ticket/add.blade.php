@extends('layouts.app')

@section('title', 'Sablona emailu')

@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">


        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">

                    @include('events.components.header_event')

                    @include('components.validation')

                    <div class="row">


                    </div>

                </div>
            </div>
        </div>
    </div>

@endsection

@section('css')


@endsection


@section('scripts_before')

@endsection



@section('scripts')


    <script>

        $(document).ready(function(){



        });

    </script>

@endsection