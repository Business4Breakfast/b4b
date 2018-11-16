@extends('email.text.layout-text')

@section('content')

{{ $content['subject'] }}

@if($content['member']->gender == 'M') {{__('email.invoice_user_gender_man')}} @else {{__('email.invoice_user_gender_female')}} @endif{{$content['member']->name}} {{$content['member']->surname}},

prichádzame už s tretím tipom, ako urýchliť Váš štart a úspech vo Vašom klube Business for Breakfast.
Orná pôda pre dlhotrvajúci zdroj odporúčaní v náš prospech.

!!Odovzdajte Vaše prvé 3 odporúčania!!  
Prejavte záujem a popremýšľajte o tom, pre koho by bolo prínosom poznať práve firmy Vašich kolegov v klube. Napíšte odporúčanie na referenčný ústrižok a pohovorte o odporúčaní s kolegom počas networkingovej prestávky. Alebo si s kolegom dajte krátky míting 1na1 a pohovorte o zamýšľanom odporúčaní. Urobte zo seba človeka, ktorého proste ostatní musia mať okolo seba a musia ho poznať – pretože je to jednoznačne prínos.
Dosiahnete tak, že nabudúce, až budete žiadať o kontakt či odporúčanie Vy, títo ľudia Vás budú počúvať a budú premýšľať, ako by Vám mohli pomôcť.

!!Moji prví 3 hostia!! Koho mám pozvať?  
1) Pozorne počúvajte predstavenie v 60tich sekundách všetkých členov a hostí v klube a nepremýšľajte štýlom – zaujíma ma táto firma? Nie - lebo služby/výrobky tejto firmy práve nepotrebujem. Skúste sa zamyslieť takto: poznám niekoho, pre koho by mohli služby či výrobky tejto firmy byť prínosom? Alebo pre koho z mojich dodávateľov či odberateľov bude prínosné poznať tohto človeka? Ten, kto Vám napadne, je ideálnym hosťom, ktorého môžete pozvať a rovno zoznámiť s niekým konkrétnym z Vašich kolegov.   2) Nie je v klube zastúpený odbor, v ktorom je výborný a odvádza dobrú prácu niektorý z Vašich dodávateľov alebo odberateľov? Pozvite ho a zoznámte ho so svojimi kolegami, s ktorými v klube spolupracujete. Prinesiete mu tak ďalší okruh potenciálnych klientov, alebo tento človek môže rozšíriť rady členov v klube a môže tak ťažiť zo vzájomnej spolupráce so všetkými členmi klubu.
Výhoda pre Vás – pokiaľ je to Váš dodávateľ, nájdete v ňom niekoho, komu nielen platíte, ale i toho, kto Vám pomáha získavať ďalší biznis. Pokiaľ je to Váš odberateľ, tak vďaka spolupráci s Vami bude mať záujem udržať dobré vzťahy i naďalej a prinesie Vám omnoho viacej odporúčaní než v klasickom vzťahu dodávateľ-odberateľ.  
3) Potrebuje niekto Vo Vašom okolí viac zákaziek a viac kvalitných ľudí okolo seba? Aby mu firma lepšie rástla? Pozvite ho na obchodné raňajky do klubu a zoznámte ho s ľuďmi, ktorí pomáhajú práve Vám, možno mu budú tiež nápomocní.
4) Viete o niektorom z Vašich dodávateľov a odberateľov, že sú známou firmou alebo že majú veľa zákazníkov? Alebo že majú veľmi zaujímavých zákazníkov? A Vy by ste sa k nim tiež radi dostali? Pozvite ich na obchodné raňajky, napríklad za odmenu, že si vážite spoluprácu s nimi a na oplátku by ste ich radi zoznámili s ľuďmi, s ktorými spolupracujete Vy a ktorí sú kvalitní a s ktorými máte dobrú skúsenosť.

Pokračovanie nabudúce – 4. diel seriálu - Úspešný štart v Business for Breakfast!
Pre akékoľvek dotazy prosím neváhajte kontaktovať manažéra Vášho klubu alebo priamo tím Business for Breakfast Slovakia, e-mail: bforb@bforb.sk, tel: +421 903 723 736.

{{$content['signature']->title_before}} {{$content['signature']->name}} {{$content['signature']->surname}} {{$content['signature']->title_after}}
{{$content['signature']->job_position}}
{{$content['signature']->phone}}
{{$content['signature']->email}}
{{ __('app.web') }}

@endsection
