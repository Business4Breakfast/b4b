@extends('email.html.layout')

@section('content')
<tr>
    <td class="wrapper">
        <table border="0" cellpadding="0" cellspacing="0">
            <tr>
                <td>
                    <h2>{{ $content['subject'] }}</h2>
                </td>
            </tr>
            <tr>
                <td>
                    @if($content['member']->gender == 'M') {{__('email.invoice_user_gender_man')}}
                    @else {{__('email.invoice_user_gender_female')}}  @endif
                    {{$content['member']->name}} {{$content['member']->surname}},<br>
                    <br>
                        prichádzame už s tretím tipom, ako urýchliť Váš štart a úspech vo Vašom klube Business for Breakfast. Orná pôda pre dlhotrvajúci zdroj odporúčaní v náš prospech.<br>
                        <br>
                        <b>Odovzdajte Vaše prvé 3 odporúčania</b> <br>
                        Prejavte záujem a popremýšľajte o tom, pre koho by bolo prínosom poznať práve firmy Vašich kolegov v klube. Napíšte odporúčanie na referenčný ústrižok a pohovorte o odporúčaní s kolegom počas networkingovej prestávky. <br>
                        Alebo si s kolegom dajte krátky míting 1 na 1 a pohovorte o zamýšľanom odporúčaní. Urobte zo seba človeka, ktorého proste ostatní musia mať okolo seba a musia ho poznať – pretože je to jednoznačne prínos.<br>
                        Dosiahnete tak, že nabudúce, až budete žiadať o kontakt či odporúčanie Vy, títo ľudia Vás budú počúvať a budú premýšľať, ako by Vám mohli pomôcť.<br>
                        <br>
                        <b>Moji prví 3 hostia - Koho mám pozvať? </b> <br>
                        1) Pozorne počúvajte predstavenie v 60tich sekundách všetkých členov a hostí v klube a nepremýšľajte štýlom – zaujíma ma táto firma?
                        Nie - lebo služby/výrobky tejto firmy práve nepotrebujem. Skúste sa zamyslieť takto: <b>poznám niekoho,</b> pre koho by mohli služby či výrobky tejto firmy <b>byť prínosom?</b> Alebo pre koho z mojich dodávateľov či odberateľov bude prínosné <b>poznať</b> tohto človeka?
                        Ten, kto Vám napadne, je ideálnym hosťom, ktorého môžete pozvať a rovno zoznámiť s niekým konkrétnym z Vašich kolegov.   
                        2) Nie je v klube zastúpený odbor, v ktorom je výborný a odvádza dobrú prácu niektorý z Vašich dodávateľov alebo odberateľov? Pozvite ho a zoznámte ho so svojimi kolegami, s ktorými v klube spolupracujete. Prinesiete mu tak ďalší okruh potenciálnych klientov, alebo tento človek môže rozšíriť rady členov v klube a môže tak ťažiť zo vzájomnej spolupráce so všetkými členmi klubu.<br>
                        Výhoda pre Vás – pokiaľ je to Váš dodávateľ, nájdete v ňom niekoho, komu nielen platíte, ale i toho, kto Vám pomáha získavať ďalší biznis. Pokiaľ je to Váš odberateľ, tak vďaka spolupráci s Vami bude mať záujem udržať dobré vzťahy i naďalej a prinesie Vám omnoho viacej odporúčaní než v klasickom vzťahu dodávateľ-odberateľ. <br>
                        <br>
                        <br><b>Pokračovanie nabudúce</b> – 4. diel seriálu - Úspešný štart v Business for Breakfast!<br>
                        Pre akékoľvek dotazy prosím neváhajte kontaktovať manažéra Vášho klubu alebo priamo tím Business for Breakfast Slovakia. <br>
                    <br>
                        BUSINESS FOR BREAKFAST<br>
                    <br>
                        <b>MENÍME ATMOSFÉRU PODNIKANIA NA SLOVENSKU</b><br>
                    <br>
                    {{$content['signature']->title_before}} {{$content['signature']->name}} {{$content['signature']->surname}} {{$content['signature']->title_after}}<br>
                    {{$content['signature']->job_position}}<br>
                    {{$content['signature']->phone}}<br>
                    {{$content['signature']->email}}<br>
                    {{ __('app.web') }}<br>
                </td>
            </tr>
            <tr>
                <td>
                    <br>
                </td>
            </tr>
        </table>
    </td>
</tr>
@endsection