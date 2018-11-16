@extends('email.text.layout-text')

@section('content')

{{ $content['subject'] }}

@if($content['member']->gender == 'M') {{__('email.invoice_user_gender_man')}} @else {{__('email.invoice_user_gender_female')}} @endif{{$content['member']->name}} {{$content['member']->surname}},

je nám veľkým potešením, že ste sa stali členom tímu Business for Breakfast.
Prostredníctvom spolupráce s kolegami v klube si medzi sebou vybudujete prostredie, v ktorom nájdete zázemie, istotu kvalitných a preverených ľudí, a vzájomne si pomôžete v získavaní odporúčaní, nových kontaktov, zákazníkov a odovzdáte si rady a skúsenosti zo svojich odborov.
V BforB nám ide o to, obklopiť sa ľuďmi, na ktorých sa môžeme spoľahnúť. Práve ste okolo seba získali obchodných „parťákov“, ktorým záleží na vzájomnej podpore, pomoci a raste. A v tom je sila podnikateľov.

My Vám teraz prinášame pár tipov a rád do začiatku Vašej účasti na BforB, ktorými môžete veľmi rýchlo naštartovať skvelú a prínosnú spoluprácu s Vašimi kolegami v klube. Behom niekoľkých týždňov od nás obdržíte štvordielny seriál „Úspešný štart v Business for Breakfast!“, ktorý Vám pomôže začať Vašu spoluprácu úspešne a IHNEĎ.
Nech sme úspešní hneď od začiatku!

!!Postavte základný kameň pre Vašu dôveru a spoľahlivosť!!
Zaznamenajte si všetky termíny mítingov Vášho klubu BforB do diára, nech s nimi dopredu počítate a nenaplánujete si do tohto termínu omylom nejaké iné stretnutie. Odteraz s Vami členovia počítajú a potrebujú vedieť, že Vám môžu dôverovať a že sa môžu na Vás vždy spoľahnúť.

!!Stanovte si konkrétny cieľ!!
Čo chcem prostredníctvom spolupráce s kolegami v klube získať?
Nové kontakty? – a koľko?
Nové zákazky? – koľko a v akej hodnote?
Rady pre moju firmu? – aké konkrétne rady aktuálne potrebujete?
Čo aktuálne riešite vo Vašej firme a hľadáte pre to riešenie?
Atď... – akýkoľvek iný cieľ, ktorý bude prínosom pre Vás a Vašu firmu.

Ciele nemusíte mať dlhodobé, môžete ich mať krátkodobé a môžete ich meniť hoci každý míting. Pokiaľ idete ale ráno na stretnutie klubu - obchodné raňajky - bez konkrétneho cieľa, najskôr sa Vám stane, že na daných raňajkách nič nezískate – pretože nebudete vedieť, čo ste chceli získať.
Príklad - Potrebujem získať kontakt na predstaviteľa istej firmy: Hovorím o svojom zámere v 60tich sekundách, môžem vysvetliť krátko aj prečo kontakt potrebujem. Počas networkingovej prestávky pohovorím s niekoľkými účastníkmi a spýtam sa, či by nevedeli o niekom, kto takýto kontakt môže mať. Popremýšľam, kto z účastníkov môže mať logicky kontakty v podobnom odbore a dám si s ním schôdzku 1na1 – a hľadám spôsoby ako sa ku kontaktu dostať.

Pokračovanie nabudúce – 2. diel seriálu - Úspešný štart v Business for Breakfast!
Pre akékoľvek dotazy prosím neváhajte kontaktovať manažéra Vášho klubu alebo priamo tím Business for Breakfast Slovakia, e-mail: bforb@bforb.sk, tel: +421 903 723 736.


{{$content['signature']->title_before}} {{$content['signature']->name}} {{$content['signature']->surname}} {{$content['signature']->title_after}}
{{$content['signature']->job_position}}
{{$content['signature']->phone}}
{{$content['signature']->email}}
{{ __('app.web') }}

@endsection
