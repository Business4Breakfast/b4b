@extends('email.text.layout-text')

@section('content')

{{ $content['subject'] }}

@if($content['member']->gender == 'M') {{__('email.invoice_user_gender_man')}} @else {{__('email.invoice_user_gender_female')}} @endif{{$content['member']->name}} {{$content['member']->surname}},

prichádzame s posledným, štvrtým tipom, ako urýchliť Váš štart a úspech vo Vašom novom klube Business for Breakfast.

Som vidieť.

!!Rezervujte si termín pre Vaše Zamerané na biznis a Vzdelávací bod!!  
Zamerané na biznis – získate tak 10 min pre prezentáciu svojej firmy, prezentáciu toho, ako pracujete, akým prínosom pre svojich zákazníkov môžete byť. Je to priestor len pre Vás a Vašu firmu, kedy ostatní v miestnosti sedia len a len kvôli Vám a venujú Vám svoju pozornosť. Nepremárnite túto šancu a pripravte sa čo najlepšie. Nezabudnite, že sa prezentujete svojim obchodným partnerom (a nie zákazníkom), ktorí musia mať dôvod o Vás a Vašej firme hovoriť! Dajte im v 10tich minútach dôvod, prečo Vás odporučiť a objasnite, komu Vás majú odporučiť.  
Vzdelávací bod – dokážte všetkým, že práve Vy ste odborníkom Vo Vašom odbore. Zdieľajte svoje rady a tipy, ktoré sú platné pre Váš odbor či oblasť podnikania, ktorú v klube zastupujete. Budujete si tak dôveru medzi ostatnými, že sa na Vás naozaj môžu obrátiť a následne aj ich známi. Povedzte niečo naozaj zaujímavé, čo je aktuálne a zaistíte si tak to, že Váš tip (spolu s Vašim menom) budú Vaši kolegovia v klube radi šíriť ďalej, ako užitočnú radu „pre život“.

!!Využite každú príležitosť k zoznámeniu, k nadviazaniu nového kontaktu!!  
Priviedol niekto z Vašich kolegov na raňajky hosťa? Potom to musí byť kvalitný človek. Nehľadiac na to, čo robí (a Vás tento odbor práve nezaujíma), zoznámte sa, nadviažte kontakt a zaujímajte sa o neho. Kvalitný človek má okolo seba množstvo ďalších kvalitných ľudí – skúste sa dozvedieť, akí ľudia to sú a čo robia, iste medzi nimi môžete nájsť niekoho, pre koho Vaša firma môže byť zaujímavá.
Nejde o to, že hosťom je kvetinárka (a Vy nemáte kvetiny radi), ale ide o to, komu a kde dodáva a aranžuje kvety a že si do budúcna môžete byť vzájomným dlhodobým prínosom.
Vážte si vzájomne jeden druhého v klube a pokiaľ si privediete hostí, zaujímajte sa o nich – preukazujete tým úctu hosťom, Vášmu kolegovi, ktorý ho pre Vás priviedol a sami sebe – prestávate posudzovať a prichádzať tak o množstvo zdrojov odporúčaní…
Naučme se vidieť za roh! Priamo vidíme úplne všetci, ale za roh to vie už iba málokto.

Veľa úspechov v Business for Breakfast!

Príďte rozvíjať svoje umenie v odporúčaní na úvodný workshop pre členov – maximalizujte svoju investíciu do členstva a staňte sa pre seba a svoje okolie stredobodom kontaktov. Zaistite si svoje miesto u moderátora/manažéra klubu.

Pre akékoľvek dotazy prosím neváhajte kontaktovať manažéra Vášho klubu alebo priamo tím Business for Breakfast Slovakia, e-mail: bforb@bforb.sk, tel: +421 903 723 736.

{{$content['signature']->title_before}} {{$content['signature']->name}} {{$content['signature']->surname}} {{$content['signature']->title_after}}
{{$content['signature']->job_position}}
{{$content['signature']->phone}}
{{$content['signature']->email}}
{{ __('app.web') }}

@endsection
