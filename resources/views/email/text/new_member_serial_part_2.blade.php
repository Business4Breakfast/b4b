@extends('email.text.layout-text')

@section('content')

{{ $content['subject'] }}

@if($content['member']->gender == 'M') {{__('email.invoice_user_gender_man')}} @else {{__('email.invoice_user_gender_female')}} @endif{{$content['member']->name}} {{$content['member']->surname}},

prichádzame s druhým tipom, ako urýchliť Váš štart a úspech vo Vašom klube Business for Breakfast.

Buďme zapamätateľní!

!!Zostavte si účinné predstavenie v 60tich sekundách!!
Nepozerajte sa na svojich kolegov v klube ako na koncových zákazníkov, ale hovorte s nimi ako s obchodnými partnermi, ktorí môžu správu o Vás a Vašej firme šíriť ďalej.
Nezabudnite, že v miestnosti zaznie x príspevkov, je treba teda, aby ten Váš bol stručný a ľahko zapamätateľný – všetko ostatné bude po poslednom príspevku zabudnuté!
Pár tipov, ktorými sa môžete inšpirovať:
Predstavte seba, svoju spoločnosť a odbor, v ktorom pôsobíte.
Popíšte bežnú problematickú situáciu, ktorú s Vami riešia Vaši zákazníci.
Použite fakty, ktorá sa týkajú Vášho odboru.
V čom dokážete Vašim klientom pomôcť? Jeden príbeh = jeden míting.
Hovorte o výhodách pre svojich zákazníkov.
Čo sa zmení u zákazníka, keď mu pomôžete?
Urobte špecifickú žiadosť o odporúčanie, radu, či kontakt v súlade s Vašim aktuálnym cieľom.
Urobte sa zapamätateľným! Stačí heslo, slogan, alebo nejaký rým, ktorý sa hodí k Vašej firme. Alebo napríklad nejaké porekadlo, ľudovú múdrosť – proste čokoľvek, vďaka čomu si na Vás vždy všetci spomenú.

!!Dohovorte si Vaše prvé 3 mítingy 1na1 s členmi v klube!!
a) Zaujímajte sa o to, čo robia, na čom pracujú a koho hľadajú
Popremýšľajte, či by ich služby nemohli byť užitočné pre niekoho z radov Vašich dodávateľov a odberateľov.
Zistite, kto sú ich klienti – s kým spolupracujú a akými kontaktami sa obklopujú.
b) Pohovorte o tom, ako môžu Vaše služby či výrobky byť užitočné ostatným.
Čo získajú, keď si zakúpia Vašu službu či výrobok.
Pohovorte o spôsobe, akým pracujete s Vašimi klientami.
c) Vytvorte dôvod, prečo by Vám mala druhá strana pomôcť, odovzdať kontakt, poradiť, atď... Nezabudnite, že pracujeme s najväčšou komoditou, ktorú všetci vlastníme – naše kontakty a každý musí mať dobrý dôvod, aby ich zdieľal.

Pokračovanie nabudúce – 3. diel seriálu - Úspešný štart v Business for Breakfast!
Pre akékoľvek dotazy prosím neváhajte kontaktovať manažéra Vášho klubu alebo priamo tím Business for Breakfast Slovakia, e-mail: bforb@bforb.sk, tel: +421 903 723 736.

BUSINESS FOR BREAKFAST
… MENÍME ATMOSFÉRU PODNIKANIA NA SLOVENSKU …
www.bforb.sk

{{$content['signature']->title_before}} {{$content['signature']->name}} {{$content['signature']->surname}} {{$content['signature']->title_after}}
{{$content['signature']->job_position}}
{{$content['signature']->phone}}
{{$content['signature']->email}}
{{ __('app.web') }}

@endsection
