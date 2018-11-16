{{--<script src="https://code.jquery.com/jquery-1.10.2.js"></script>--}}
{{--<script src="https://code.jquery.com/ui/1.11.4/jquery-ui.js"></script>--}}
{{--<script async src="https://autoform.ekosystem.slovensko.digital/assets/autoform.js"></script>--}}



@extends('layouts.app')

@section('title', 'Test formulara')

@section('content')


    <form data-slovensko-digital-autoform="9eaddf6ee97b9312cb11b111179737e7ad673b0e72fe679d5df34578e984e03096c5355155efecb0" action="...">
        <p>
            <label for="name">Názov</label><br>
            <input type="text" name="name" data-slovensko-digital-autoform="name"/>
        </p>
        <p>
            <label for="cin">IČO</label><br>
            <input type="text" name="cin" data-slovensko-digital-autoform="cin"/>
        </p>
        <p>
            <label for="formatted_address">Adresa</label><br>
            <input type="text" name="formatted_address" data-slovensko-digital-autoform="formatted-address"/>
        </p>
        <p>
            <label for="tin">DIČ</label><br>
            <input type="text" name="tin" data-slovensko-digital-autoform="tin"/>
        </p>
        <p>
            <label for="vatin">IČ-DPH</label><br>
            <input type="text" name="vatin" data-slovensko-digital-autoform="vatin"/>
        </p>
    </form>


@endsection



@section('scripts')

    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script async src="https://autoform.ekosystem.slovensko.digital/assets/autoform.js"></script>

    <script>
        $(document).ready(function (){


        });
    </script>
@endsection